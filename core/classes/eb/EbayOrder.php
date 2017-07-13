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

    protected function saveItems($item, $poNumber, $order_id, Ecommerce $ecommerce, $itemObject)
    {
        $sku = $item->Item->SKU;
        $title = $item->Item->Title;
        $quantity = $item->QuantityPurchased;
        $upc = '';
        $principle = Ecommerce::removeCommasInNumber((float)$item->TransactionPrice);
        $item_id = $item->Item->ItemID . '-' . $item->TransactionID;
        $sku_id = SKU::searchOrInsert($sku);
        if (!LOCAL) {
            OrderItem::save($order_id, $sku_id, $principle, $quantity, $item_id);
        }
        $itemXml = OrderItemXMLController::create($sku, $title, $poNumber, $quantity, $principle, $upc);
        $itemObject['sku'] = $sku;
        $itemObject['itemXml'] .= $itemXml;
        $poNumber++;
        $itemObject['poNumber'] = $poNumber;
        return $itemObject;
    }

    protected function getItems($items, $order_id, Ecommerce $ecommerce)
    {
        $poNumber = 1;

        $itemObject = [];
        $itemObject['itemXml'] = '';

        if (count($items->Transaction) > 1) {
            foreach ($items->Transaction as $item) {
                $itemObject = $this->saveItems($item, $poNumber, $order_id, $ecommerce, $itemObject);
                $poNumber = $itemObject['poNumber'];
            }
        } else {
            $itemObject = $this->saveItems($items->Transaction, $poNumber, $order_id, $ecommerce, $itemObject);
        }

        return (object)$itemObject;
    }

    protected function getMoreOrders($requestName, $pagenumber, $ebayDays)
    {
        $xml = $this->getOrderXml($ebayDays, $pagenumber);
        $response = EbayClient::ebayCurl($requestName, $xml);
        return $response;
    }

    protected function parseOrders($xml_orders, $folder, Ecommerce $ecommerce)
    {
        foreach ($xml_orders->OrderArray->Order as $order) {
            $order_num = (string)$order->ExternalTransaction->ExternalTransactionID;
            $fee = Ecommerce::formatMoney((float)$order->ExternalTransaction->FeeOrCreditAmount);

            $order_status = trim((string)$order->OrderStatus);

            if ($order_status !== 'Cancelled') {

                echo "Order: $order_num -> Status: $order_status<br>";
                echo (string)$order->OrderID . '<br>';
                $found = Order::get($order_num);

                if (LOCAL || !$found) {
                    Ecommerce::dd($order);
                    $timestamp = (string)$order->CreatedTime;
                    $ismultilegshipping = (string)$order->IsMultiLegShipping;

                    $shipping_amount = Ecommerce::formatMoney((float)$order->ShippingDetails->ShippingServiceOptions->ShippingServiceCost);
                    $total = $order->Total;

                    $erlanger = [
                        'address2' => '1850 Airport',
                        'city' => 'Erlanger',
                        'state' => 'KY',
                        'zip' => '41025'
                    ];

                    $shipping = ShippingController::code($total, $erlanger);

                    $item_taxes = Ecommerce::formatMoney((float)$order->ShippingDetails->SalesTax->SalesTaxAmount);
                    Ecommerce::dd($item_taxes);
                    $trans_id = $order->ShippingDetails->SellingManagerSalesRecordNumber;

                    //Address
                    if (strcasecmp($ismultilegshipping, 'true') == 0) {
                        $shippinginfo = (object)$order
                            ->MultiLegShippingDetails
                            ->SellerShipmentToLogisticsProvider
                            ->ShipToAddress;
                        $streetAddress = (string)$shippinginfo->ReferenceID;
                        $streetAddress2 = (string)$shippinginfo->Street1;
                    } else {
                        $shippinginfo = (string)$order
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
                    $buyerID = (new Buyer($firstName, $lastName, $streetAddress, $streetAddress2, $city, $state,
                        $zipCode, $country))->getBuyerId();

                    //Save Order
                    if (!LOCAL) {
                        $order_id = Order::save(EbayClient::getStoreID(), $buyerID, $order_num, $shipping,
                            $shipping_amount, $item_taxes, $fee, $trans_id);
                    }

                    //Order Items
                    $items = $this->getItems($order->TransactionArray, $order_id, $ecommerce);

                    $poNumber = (string)$items->poNumber;
                    $sku = (string)$items->sku;
                    $itemXml = (string)$items->itemXml;

                    $itemXml .= TaxXMLController::getItemXml($state, $poNumber, $item_taxes);
                    $channelName = 'Ebay';
                    $channel_num = Channel::getAccountNumbersBySku($channelName, $sku);
                    $orderXml = OrderXMLController::create($channel_num, $channelName, $order_num, $timestamp, $shipping_amount, $shipping, $buyerPhone, $shipToName, $streetAddress, $streetAddress2, $city, $state, $zipCode, $country, $itemXml);
                    Ecommerce::dd($orderXml);
                    if (!LOCAL) {
                        FTPController::saveXml($order_num, $orderXml, $folder, $channelName);
                    }
                }
            }
        }
    }

    public function getOrders($requestName, $pagenumber, $ebayDays, $folder, Ecommerce $ecommerce)
    {
        $response = $this->getMoreOrders($requestName, $pagenumber, $ebayDays);
        if ($response) {
            $xml_orders = simplexml_load_string($response);
            $orderCount = count($xml_orders->OrderArray->Order);
            echo "Order Count: $orderCount<br>";
            $this->parseOrders($xml_orders, $folder, $ecommerce);
            if ($orderCount >= 100) {
                $pagenumber++;
                $this->getOrders($requestName, $pagenumber, $ebayDays, $folder, $ecommerce);
            }
        }
    }
}