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


    public function getOrderXml($ebayDays, $pagenumber)
    {
        $xml = [
            'NumberOfDays' => $ebayDays,
            'Pagination' => [
                'EntriesPerPage' => '100',
                'PageNumber' => $pagenumber
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

    protected function saveItems($item, $poNumber, $itemObject, $Order)
    {
        $sku = (string)$item->Item->SKU;
        $title = (string)$item->Item->Title;
        $quantity = (int)$item->QuantityPurchased;
        $upc = '';
        $price = Ecommerce::removeCommasInNumber((float)$item->TransactionPrice);
        $itemID = (string)$item->Item->ItemID . '-' . (string)$item->TransactionID;
        $skuID = SKU::searchOrInsert($sku);
        $orderItem = new OrderItem($sku, $title, $quantity, $price, $upc, $poNumber, $itemID);
        if (!LOCAL) {
//            OrderItem::save($orderID, $skuID, $principle, $quantity, $itemID);
            $orderItem->save($Order);
        }
        $itemXml = OrderItemXMLController::create($orderItem);
        $itemObject['sku'] = $sku;
        $itemObject['itemXml'] .= $itemXml;
        $poNumber++;
        $itemObject['poNumber'] = $poNumber;
        return $itemObject;
    }

    protected function getItems($items, $Order)
    {
        $poNumber = 1;

        $itemObject = [];
        $itemObject['itemXml'] = '';

        if (count($items->Transaction) > 1) {
            foreach ($items->Transaction as $item) {
                $itemObject = $this->saveItems($item, $poNumber, $itemObject, $Order);
                $poNumber = $itemObject['poNumber'];
            }
        } else {
            $itemObject = $this->saveItems($items->Transaction, $poNumber, $itemObject, $Order);
        }

        return (object)$itemObject;
    }

    protected function getMoreOrders($requestName, $pagenumber, $ebayDays)
    {
        $xml = $this->getOrderXml($ebayDays, $pagenumber);
        $response = EbayClient::ebayCurl($requestName, $xml);
        return $response;
    }

    protected function parseOrders($xml_orders)
    {
        foreach ($xml_orders->OrderArray->Order as $order) {
            $orderNum = (string)$order->ExternalTransaction->ExternalTransactionID;
            $fee = Ecommerce::formatMoney((float)$order->ExternalTransaction->FeeOrCreditAmount);

            $order_status = trim((string)$order->OrderStatus);

            if ($order_status !== 'Cancelled') {

                echo "Order: $orderNum -> Status: $order_status<br>";
                echo (string)$order->OrderID . '<br>';
                $found = Order::get($orderNum);

                if (LOCAL || !$found) {
                    Ecommerce::dd($order);
                    $timestamp = (string)$order->CreatedTime;
                    $ismultilegshipping = (string)$order->IsMultiLegShipping;

                    $shippingPrice = Ecommerce::formatMoney((float)$order->ShippingDetails->ShippingServiceOptions->ShippingServiceCost);
                    $total = $order->Total;

                    $erlanger = [
                        'address2' => '1850 Airport',
                        'city' => 'Erlanger',
                        'state' => 'KY',
                        'zip' => '41025'
                    ];

                    $shippingCode = ShippingController::code($total, $erlanger);

                    $tax = Ecommerce::formatMoney((float)$order->ShippingDetails->SalesTax->SalesTaxAmount);
                    Ecommerce::dd($tax);
                    $channelOrderID = (string)$order->ShippingDetails->SellingManagerSalesRecordNumber;

                    //Address
                    if (strcasecmp($ismultilegshipping, 'true') == 0) {
                        $shippinginfo = (object)$order
                            ->MultiLegShippingDetails
                            ->SellerShipmentToLogisticsProvider
                            ->ShipToAddress;
                        $streetAddress = (string)$shippinginfo->ReferenceID;
                        $streetAddress2 = (string)$shippinginfo->Street1;
                    } else {
                        $shippinginfo = (object)$order
                            ->ShippingAddress;
                        $streetAddress = (string)$shippinginfo->Street1;
                        if (is_object($shippinginfo->Street2)) {
                            $streetAddress2 = '';
                        } else {
                            $streetAddress2 = (string)$shippinginfo->Street2;
                        }
                    }
                    $city = (string)$shippinginfo->CityName;
                    $state = (string)$shippinginfo->StateOrProvince;
                    $zipCode = (string)$shippinginfo->PostalCode;
                    $country = (string)$shippinginfo->Country;


                    //Buyer
                    $shipToName = (string)$shippinginfo->Name;
                    $buyerPhone = (string)$shippinginfo->Phone;
                    list($lastName, $firstName) = BuyerController::splitName($shipToName);
                    $buyer = new Buyer($firstName, $lastName, $streetAddress, $streetAddress2, $city, $state, $zipCode, $country);

                    $Order = new Order(EbayClient::getStoreID(), $buyer, $orderNum, $shippingCode, $shippingPrice, $tax, $fee, $channelOrderID);
                    //Save Order
                    if (!LOCAL) {
//                        $order_id = Order::save(EbayClient::getStoreID(), $buyerID, $orderNum, $shippingCode, $shippingPrice, $tax, $fee, $channelOrderID);
                        $Order->save(EbayClient::getStoreID());
                    }

                    //Order Items
                    $items = $this->getItems($order->TransactionArray, $Order);

                    $poNumber = (string)$items->poNumber;
                    $sku = (string)$items->sku;
                    $itemXml = (string)$items->itemXml;

                    $itemXml .= TaxXMLController::getItemXml($state, $poNumber, $tax);
                    $channelName = 'Ebay';
                    $channel_num = Channel::getAccountNumbersBySku($channelName, $sku);
                    $orderXml = OrderXMLController::create($channel_num, $channelName, $orderNum, $timestamp, $shippingPrice, $shippingCode, $buyerPhone, $shipToName, $streetAddress, $streetAddress2, $city, $state, $zipCode, $country, $itemXml);
                    Ecommerce::dd($orderXml);
                    if (!LOCAL) {
                        FTPController::saveXml($orderNum, $orderXml, $channelName);
                    }
                }
            }
        }
    }

    public function getOrders($requestName, $pagenumber, $ebayDays)
    {
        $response = $this->getMoreOrders($requestName, $pagenumber, $ebayDays);
        if ($response) {
            $xml_orders = simplexml_load_string($response);
            $orderCount = count($xml_orders->OrderArray->Order);
            echo "Order Count: $orderCount<br>";
            $this->parseOrders($xml_orders);
            if ($orderCount >= 100) {
                $pagenumber++;
                $this->getOrders($requestName, $pagenumber, $ebayDays);
            }
        }
    }
}