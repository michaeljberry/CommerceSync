<?php

namespace eb;

use controllers\channels\BuyerController;
use controllers\channels\FTPController;
use ecommerce\Ecommerce;
use models\channels\address\Address;
use models\channels\address\City;
use models\channels\address\State;
use models\channels\address\ZipCode;
use models\channels\Buyer;
use models\channels\Channel;
use models\channels\order\Order;
use models\channels\order\OrderItem;
use controllers\channels\order\OrderItemXMLController;
use controllers\channels\order\OrderXMLController;
use controllers\channels\ShippingController;
use models\channels\SKU;
use models\channels\Tax;
use controllers\channels\tax\TaxXMLController;

class EbayOrder extends Ebay
{

    protected $orderNumber;
    protected $channel;


    public static function getOrderXml($pageNumber)
    {
        $xml = [
            'NumberOfDays' => Ebay::get_order_days(),
            'Pagination' => [
                'EntriesPerPage' => '100',
                'PageNumber' => $pageNumber
            ],
            'DetailLevel' => 'ReturnAll'
        ];
        return $xml;
    }

    public function update_ebay_tracking($tracking_id, $carrier, $item_id, $trans_id)
    {
        $requestName = 'CompleteSale';

        $xml = [
            'ItemID' => $item_id,
            'TransactionID' => $trans_id,
            'Shipped' => 'true',
            'Shipment' =>
                [
                    'ShipmentTrackingDetails' =>
                        [
                            'ShipmentTrackingNumber' => $tracking_id,
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
        $xml_orders = simplexml_load_string($orders);

        $orderCount = count($xml_orders->OrderArray->Order);
        echo "Order Count: $orderCount<br>";

        foreach ($xml_orders->OrderArray->Order as $order) {
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
        $orderNum = (string)$order->ExternalTransaction->ExternalTransactionID;

        $orderStatus = trim((string)$order->OrderStatus);

        if ($orderStatus !== 'Cancelled') {

            $found = Order::get($orderNum);

            if (LOCAL || !$found) {

                $this->orderFound($order, $orderNum);
            }
        }
    }

    /**
     * @param $order
     * @param $orderNum
     */
    protected function orderFound($order, $orderNum)
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
        $shippingCode = ShippingController::code($total, $address);

        $shippingPrice = (float)$order->ShippingDetails->ShippingServiceOptions->ShippingServiceCost;

        $fee = (float)$order->ExternalTransaction->FeeOrCreditAmount;
        $tax = (float)$order->TransactionArray->Transaction->Taxes->TotalTaxAmount;

        $channelOrderID = (string)$order->ShippingDetails->SellingManagerSalesRecordNumber;

        //Buyer
        $shipToName = (string)$shippingAddress->Name;
        $buyerUserID = (string)$order->BuyerUserID;
        $phone = (string)$shippingAddress->Phone;
        list($lastName, $firstName) = BuyerController::splitName($shipToName);
        $buyer = new Buyer($firstName, $lastName, $streetAddress, $streetAddress2, $city, $state, $zipCode,
            $country, $phone);

        $Order = new Order(1, $channelName, EbayClient::getStoreID(), $buyer, $orderNum, $purchaseDate,
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