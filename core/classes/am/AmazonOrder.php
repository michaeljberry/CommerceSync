<?php

namespace am;

use ecommerce\Ecommerce as ecom;
use \DateTime;
use \DateTimeZone;

class AmazonOrder extends Amazon
{

    public function updateTrackingInfo($orderNum, $trackingID, $carrier, $num)
    {
        $xml = [
            'Message' => [
                'MessageID' => $num,
                'OrderFulfillment' => [
                    'AmazonOrderID' => $orderNum,
                    'FulfillmentDate' => gmdate("Y-m-d\TH:i:s\Z", time()),
                    'FulfillmentData' => [
                        'CarrierName' => $carrier,
                        'ShipperTrackingNumber' => $trackingID
                    ]
                ]
            ]
        ];
        $amazonFeed = ecom::makeXML($xml);
        return $amazonFeed;
    }

    public function update_amazon_tracking($xml1){
        $action = 'SubmitFeed';
        $feedtype = '_POST_ORDER_FULFILLMENT_DATA_';
        $version = '2009-01-01';
        $feed = 'Feeds';
        $whatToDo = 'POST';

        $paramAdditionalConfig = [
            'SellerId'
        ];

        $param = $this->setParams($action, $feedtype, $version, $paramAdditionalConfig);

        $xml = [
            'MessageType' => 'OrderFulfillment',
        ];
        $xml = ecom::makeXML($xml);
        $xml .= $xml1;

        $response = $this->amazonCurl($xml, $feed, $version, $param, $whatToDo);

        return $response;
    }

    public function getOrders()
    {
        $action = 'ListOrders';
        $feedtype = '';
        $version = '2013-09-01';
        $feed = 'Orders';
        $whatToDo = 'POST';
        $paramAdditionalConfig = [
            'MarketplaceId.Id.1',
            'SellerId',
        ];

        $param = $this->setParams($action, $feedtype, $version, $paramAdditionalConfig);

        $param['OrderStatus.Status.1'] = 'Unshipped';
        $param['OrderStatus.Status.2'] = 'PartiallyShipped';
//        $param['OrderStatus.Status.1'] = 'Shipped';
//        $param['FulfillmentChannel.Channel.1'] = 'MFN';
        $from = $this->get_order_dates($this->am_store_id);
        $from = $from['api_pullfrom'];
//        $from = "-1";
        $from .= ' days';
        ecom::dd($from);
        $createdAfter = new DateTime($from, new DateTimeZone('America/Boise'));
        $createdAfter = $createdAfter->format("Y-m-d\TH:i:s\Z");
        $param['CreatedAfter'] = $createdAfter;

        $xml = '';

        $response = $this->amazonCurl($xml, $feed, $version, $param, $whatToDo);

        return $response;
    }

    public function getMoreOrders($nextToken)
    {
        $action = 'ListOrdersByNextToken';
        $feedtype = '';
        $version = '2013-09-01';
        $feed = 'Orders';
        $whatToDo = 'POST';
        $paramAdditionalConfig = [
            'MarketplaceId.Id.1',
            'SellerId',
        ];

        $param = $this->setParams($action, $feedtype, $version, $paramAdditionalConfig);

        $param['NextToken'] = $nextToken;

        $xml = '';

        $response = $this->amazonCurl($xml, $feed, $version, $param, $whatToDo);

        return $response;
    }

    public function getOrderItems($orderNum)
    {
        $action = 'ListOrderItems';
        $feedtype = '';
        $version = '2013-09-01';
        $feed = 'Orders';
        $whatToDo = 'POST';
        $paramAdditionalConfig = [
            'SellerId'
        ];

        $param = $this->setParams($action, $feedtype, $version, $paramAdditionalConfig);
        $param['AmazonOrderId'] = $orderNum;

        $xml = '';

        $response = $this->amazonCurl($xml, $feed, $version, $param, $whatToDo);

        return $response;
    }

    protected function ifItemsExist($orderNum, $orderId, $totalTax, $totalShipping, $ecommerce)
    {
        $orderItems = simplexml_load_string($this->getOrderItems($orderNum));

        if(isset($orderItems->ListOrderItemsResult->OrderItems->OrderItem)) {
            $items = $this->parseItems($orderItems->ListOrderItemsResult->OrderItems->OrderItem, $orderId, $totalTax, $totalShipping, $ecommerce);
            return $items;
        }else{
            sleep(2);
            $this->ifItemsExist($orderNum, $orderId, $totalTax, $totalShipping, $ecommerce);
        }
    }

    protected function parseItems($items, $orderId, $totalTax, $totalShipping, $ecommerce)
    {
        $totalWithoutTax = 0.00;
        $poNumber = 1;
        $itemXml = '';

        $itemObject = [];

        foreach ($items as $item) {
            $quantity = $item->QuantityOrdered;

            $title = $item->Title;
            $sku = $item->SellerSKU;
            $itemObject['sku'] = $sku;
            $upc = '';

            $itemPrice = (float)$item->ItemPrice->Amount;
            $promotionDiscount = (float)$item->PromotionDiscount->Amount;
            $itemPrice += (float)$promotionDiscount;

            $giftWrapPrice = (float)$item->GiftWrapPrice->Amount;
            $itemPrice += (float)$giftWrapPrice;

            $totalWithoutTax += (float)$itemPrice;
            $principle = ecom::formatMoney((float)$itemPrice / $quantity);

            $shippingPrice = (float)$item->ShippingPrice->Amount;
            $shippingDiscount = (float)$item->ShippingDiscount->Amount;
            $shippingPrice += (float)$shippingDiscount;
            $totalShipping += (float)$shippingPrice;

            $itemTax = ecom::formatMoney((float)$item->ItemTax->Amount);
            $totalTax += (float)$itemTax;

            $shippingTax = (float)$item->ShippingTax->Amount;
            $totalTax += (float)$shippingTax;

            $giftWrapTax = (float)$item->GiftWrapTax->Amount;
            $totalTax += (float)$giftWrapTax;
            $totalTax = ecom::formatMoney($totalTax);
            ecom::dd("Total Tax: $totalTax");

            $skuId = $ecommerce->skuSoi($sku);
            $ecommerce->save_order_items($orderId, $skuId, $itemPrice, $quantity);
            $itemXml .= $ecommerce->create_item_xml($sku, $title, $poNumber, $quantity, $principle, $upc);
            $poNumber++;
        }
        $itemObject['poNumber'] = $poNumber;
        $itemObject['itemXml'] = $itemXml;
        $itemObject['totalWithoutTax'] = $totalWithoutTax;
        $itemObject['totalTax'] = $totalTax;
        $itemObject['totalShipping'] = $totalShipping;
        return (object)$itemObject;
    }

    public function parseOrders($orders, $ecommerce, $ibmdata, $folder, $companyId, $nextPage = null)
    {
        $taxableStates = $ecommerce->getCompanyTaxInfo($companyId);

        $xmlOrders = simplexml_load_string($orders);

        $page = "ListOrdersResult";
        if($nextPage){
            $page = "ListOrdersByNextTokenResult";
        }
        foreach($xmlOrders->{$page}->Orders->Order as $order){

            $orderNum = $order->AmazonOrderId;

            $found = ecom::orderExists($orderNum);

            if(!$found) {

                $latestDeliveryDate = $order->LatestDeliveryDate;
                $orderType = (string)$order->OrderType;
                $purchaseDate = rtrim((string)$order->PurchaseDate, 'Z');

                $isReplacementOrder = $order->IsReplacementOrder;
                $numberOfItemsShipped = $order->NumberOfItemsShipped;
                $orderStatus = $order->OrderStatus;
                $salesChannel = $order->SalesChannel;
                $isBusinessOrder = $order->IsBusinessOrder;

                $orderTotal = (float)$order->OrderTotal->Amount;

                $shipToName = (string)$order->ShippingAddress->Name;
                $buyerName = explode(' ', $shipToName);
                $lastName = ucwords(strtolower(array_pop($buyerName)));
                $firstName = ucwords(strtolower(implode(' ',$buyerName)));
                $buyerEmail = (string)$order->BuyerEmail;
                $shippingPhone = (string)$order->ShippingAddress->Phone;
                $shipByDate = (string)$order->LatestShipDate;

                $shippingAddressLine1 = (string)$order->ShippingAddress->AddressLine1;
                $shippingAddressLine2 = (string)$order->ShippingAddress->AddressLine2 ?? '';
                $shippingCity = (string)$order->ShippingAddress->City;
                $shippingState = strtolower((string)$order->ShippingAddress->StateOrRegion);
                if(strlen($shippingState) > 2){
                    $shippingState = $ecommerce->stateToAbbr(ucfirst($shippingState));
                }
                $shippingState = strtoupper($shippingState);
                $shippingPostalCode = (string)$order->ShippingAddress->PostalCode;
                $shippingCountryCode = (string)$order->ShippingAddress->CountryCode;
                $shippingCountryCode = (string)$shippingCountryCode == 'US' ? 'USA' : $shippingCountryCode;

                $shipmentMethod = (string)$order->ShipmentServiceLevelCategory;

                $shipping = $ecommerce->shippingCode($orderTotal, [], $shipmentMethod);

                $totalTax = 0.00;
                $totalShipping = 0.00;

                $stateId = $ecommerce->stateId(strtoupper($shippingState));
                $zipId = $ecommerce->zipSoi($shippingPostalCode, $stateId);
                $cityId = $ecommerce->citySoi($shippingCity, $stateId);
                $custId = $ecommerce->customer_soi($firstName,$lastName,ucwords(strtolower($shippingAddressLine1)),ucwords(strtolower($shippingAddressLine2)),$cityId,$stateId,$zipId);
                $orderId = $ecommerce->save_order($this->am_store_id, $custId, $orderNum, $shipping, $totalShipping, $totalTax);

                $items = $this->ifItemsExist($orderNum, $orderId, $totalTax, $totalShipping, $ecommerce);

                $poNumber = (string)$items->poNumber;
                $totalTax = (float)$items->totalTax;
                $totalWithoutTax = (float)$items->totalWithoutTax;
                $totalShipping = (float)$items->totalShipping;
                $sku = (string)$items->sku;
                $itemXml = (string)$items->itemXml;

                if($ecommerce->taxableState($taxableStates, $shippingState)){
                    echo 'Should be taxed<br>';
                    if($totalTax == 0){
                        // No tax collected, but tax is required to remit.
                        // Need to calculate taxes and subtract from sales price of item(s)
                        $totalTax = $ecommerce->calculateTax($taxableStates[$shippingState], $totalWithoutTax, $totalShipping);
                    }
                    $itemXml .= $ecommerce->get_tax_item_xml(
                        $shippingState,
                        $poNumber,
                        $totalTax,
                        $taxableStates[$shippingState]['tax_line_name']
                    );
                }

                $orderId = $ecommerce->updateOrderShippingAndTaxes($orderId, $totalShipping, $totalTax);
                $channelName = 'Amazon';
                $channelNum = $ecommerce->get_channel_num($ibmdata, $channelName, $sku);

                $orderXml = $ecommerce->create_xml($channelNum, $channelName, $orderNum, $purchaseDate, $totalShipping, $shipping, $purchaseDate, $shippingPhone, $shipToName, $shippingAddressLine1, $shippingAddressLine2, $shippingCity, $shippingState, $shippingPostalCode, $shippingCountryCode, $itemXml);
                $ecommerce->saveXmlToFTP($orderNum, $orderXml, $folder, $channelName);
            }
        }

        if(isset($xmlOrders->{$page}->NextToken)) {
            $nextToken = (string)$xmlOrders->{$page}->NextToken;
            ecom::dd("Next Token:" . $nextToken);
        }
        if(isset($nextToken)){
            $orders = $this->getMoreOrders($nextToken);

            $this->parseOrders($orders, $ecommerce, $ibmdata, $folder, $companyId, true);
        }
    }
}