<?php

namespace eb;

use ecommerce\Ecommerce;
use models\channels\Channel;
use models\channels\order\Order;
use models\channels\order\OrderItem;
use controllers\channels\FTPController;
use controllers\channels\BuyerController;

class EbayOrder extends Ebay
{
    public static function getOrderXml($pageNumber)
    {
        $xml = [
            'NumberOfDays' => Ebay::getApiOrderDays(),
            'Pagination' => [
                'EntriesPerPage' => '100',
                'PageNumber' => $pageNumber
            ],
            'DetailLevel' => 'ReturnAll'
        ];
        return $xml;
    }

    public function updateTracking($trackingNumber, $carrier, $itemID, $transID)
    {
        $requestName = 'CompleteSale';

        $xml = [
            'ItemID' => $itemID,
            'TransactionID' => $transID,
            'Shipped' => 'true',
            'Shipment' =>
                [
                    'ShipmentTrackingDetails' =>
                        [
                            'ShipmentTrackingNumber' => $trackingNumber,
                            'ShippingCarrierUsed' => $carrier
                        ]
                ]
        ];

        $response = EbayClient::ebayCurl($requestName, $xml);
        return $response;
    }

    public static function getOrders($requestName, $pageNumber)
    {
        $xml = EbayOrder::getOrderXml($pageNumber);
        return EbayClient::ebayCurl($requestName, $xml);
    }

    public function parseOrders($orders, $pageNumber, $requestName)
    {
        $xmlOrders = simplexml_load_string($orders);

        $orderCount = count($xmlOrders->OrderArray->Order);
        echo "Order Count: $orderCount<br>";

        foreach ($xmlOrders->OrderArray->Order as $order) {
            $this->parseOrder($order);
        }

        if ($orderCount >= 100) {
            $pageNumber++;
            $orders = EbayOrder::getOrders($requestName, $pageNumber);

            $this->parseOrders($orders, $pageNumber, $requestName);
        }
    }

    protected function parseOrder($order)
    {
        $orderNumber = (string)$order->ExternalTransaction->ExternalTransactionID;
        $orderStatus = trim((string)$order->OrderStatus);

        if ($orderStatus !== 'Cancelled') {
            $found = Order::get($orderNumber);

            if (LOCAL || !$found) {
                $this->orderFound($order, $orderNumber);
            }
        }
    }

    protected function orderFound($order, $orderNumber)
    {
        Ecommerce::dd($order);
        $channelName = 'Ebay';

        $purchaseDate = (string)$order->CreatedTime;

        $total = (float)$order->Total;

        //Address
        $ismultilegshipping = (string)$order->IsMultiLegShipping;
        if (strcasecmp($ismultilegshipping, 'true') == 0) {
            $shippingAddress = (object)$order
                ->MultiLegShippingDetails
                ->SellerShipmentToLogisticsProvider
                ->ShipToAddress;
            $streetAddress = (string)$shippingAddress->ReferenceID;
            $streetAddress2 = (string)$shippingAddress->Street1;
        } else {
            $shippingAddress = (object)$order
                ->ShippingAddress;
            $streetAddress = (string)$shippingAddress->Street1;
            if (is_object($shippingAddress->Street2)) {
                $streetAddress2 = '';
            } else {
                $streetAddress2 = (string)$shippingAddress->Street2;
            }
        }
        $city = (string)$shippingAddress->CityName;
        $state = (string)$shippingAddress->StateOrProvince;
        $zipCode = (string)$shippingAddress->PostalCode;
        $country = (string)$shippingAddress->Country;

        $address = [
            'address2' => $streetAddress2,
            'city' => $city,
            'state' => $state,
            'zip' => $zipCode
        ];
        $shippingCode = Order::shippingCode($total, $address);
        $shippingPrice = (float)$order->ShippingDetails->ShippingServiceOptions->ShippingServiceCost;

        $fee = (float)$order->ExternalTransaction->FeeOrCreditAmount;
        $tax = (float)$order->TransactionArray->Transaction->Taxes->TotalTaxAmount;

        $channelOrderID = (string)$order->ShippingDetails->SellingManagerSalesRecordNumber;

        //Buyer
        $shipToName = (string)$shippingAddress->Name;
        $buyerUserID = (string)$order->BuyerUserID;
        $phone = (string)$shippingAddress->Phone;
        list($lastName, $firstName) = BuyerController::splitName($shipToName);
        $buyer = Order::buyer($firstName, $lastName, $streetAddress, $streetAddress2, $city, $state, $zipCode,
            $country, $phone);


        $Order = new Order(1, $channelName, EbayClient::getStoreID(), $buyer, $orderNumber, $purchaseDate,
            $shippingCode, $shippingPrice, $tax, $fee, $channelOrderID);

        //Save Order
        if (!LOCAL) {
            $Order->save(EbayClient::getStoreID());
        }

        $this->getItems($Order, $order->TransactionArray);

        $tax = $Order->getTax()->get();

        Order::updateShippingAndTaxes($Order->getOrderId(), $Order->getShippingPrice(), $tax);

        $Order->setOrderXml($Order);

        if (!LOCAL) {
            FTPController::saveXml($Order);
        }
    }

    protected function getItems(Order $Order, $items)
    {
        $this->parseItems($Order, $items);
    }

    protected function parseItems(Order $Order, $items)
    {
        if (count($items->Transaction) > 1) {
            foreach ($items->Transaction as $item) {
                $this->parseItem($Order, $item);
            }
        } else {
            $this->parseItem($Order, $items->Transaction);
        }
    }

    protected function parseItem(Order $Order, $item)
    {
        $sku = (string)$item->Item->SKU;
        $Order->setChannelAccount(Channel::getAccountNumbersBySku($Order->getChannelName(), $sku));

        $title = (string)$item->Item->Title;
        $quantity = (int)$item->QuantityPurchased;
        $upc = '';

        $price = (float)$item->TransactionPrice;

        $itemID = (string)$item->Item->ItemID . '-' . (string)$item->TransactionID;

        $orderItem = new OrderItem($sku, $title, $quantity, $price, $upc, $Order->getPoNumber(), $itemID);
        $Order->setOrderItems($orderItem);
        if (!LOCAL) {
            $orderItem->save($Order);
        }
    }
}