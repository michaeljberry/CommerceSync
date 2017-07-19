<?php

namespace am;

use controllers\channels\BuyerController;
use controllers\channels\FTPController;
use ecommerce\Ecommerce;
use \DateTime;
use \DateTimeZone;
use models\channels\Channel;
use models\channels\order\Order;
use models\channels\order\OrderItem;
use controllers\channels\XMLController;

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
        $amazonFeed = XMLController::makeXML($xml);
        return $amazonFeed;
    }

    public function update_amazon_tracking($xml1)
    {
        $action = 'SubmitFeed';
        $feedtype = '_POST_ORDER_FULFILLMENT_DATA_';
        $feed = 'Feeds';
        $version = AmazonClient::getAPIFeedInfo($feed)['versionDate'];
        $whatToDo = 'POST';

        $paramAdditionalConfig = [
            'SellerId'
        ];

        $param = AmazonClient::setParams($action, $feedtype, $version, $paramAdditionalConfig);

        $xml = [
            'MessageType' => 'OrderFulfillment',
        ];
        $xml = XMLController::makeXML($xml);
        $xml .= $xml1;

        $response = AmazonClient::amazonCurl($xml, $feed, $version, $param, $whatToDo);

        return $response;
    }

    public function getMoreOrders($nextToken)
    {
        $action = 'ListOrdersByNextToken';
        $feedtype = '';
        $feed = 'Orders';
        $version = AmazonClient::getAPIFeedInfo($feed)['versionDate'];
        $whatToDo = 'POST';
        $paramAdditionalConfig = [
            'MarketplaceId.Id.1',
            'SellerId',
        ];

        $param = AmazonClient::setParams($action, $feedtype, $version, $paramAdditionalConfig);

        $param['NextToken'] = $nextToken;

        $xml = '';

        $response = AmazonClient::amazonCurl($xml, $feed, $version, $param, $whatToDo);

        return $response;
    }

    public function getOrderItems($orderNum)
    {
        $action = 'ListOrderItems';
        $feedtype = '';
        $feed = 'Orders';
        $version = AmazonClient::getAPIFeedInfo($feed)['versionDate'];
        $whatToDo = 'POST';
        $paramAdditionalConfig = [
            'SellerId'
        ];

        $param = AmazonClient::setParams($action, $feedtype, $version, $paramAdditionalConfig);
        $param['AmazonOrderId'] = $orderNum;

        $xml = '';

        $response = AmazonClient::amazonCurl($xml, $feed, $version, $param, $whatToDo);

        return $response;
    }

    public static function getOrders()
    {
        $action = 'ListOrders';
        $feedtype = '';
        $feed = 'Orders';
        $version = AmazonClient::getAPIFeedInfo($feed)['versionDate'];
        $whatToDo = 'POST';
        $paramAdditionalConfig = [
            'MarketplaceId.Id.1',
            'SellerId',
        ];

        $param = AmazonClient::setParams($action, $feedtype, $version, $paramAdditionalConfig);

        $param['OrderStatus.Status.1'] = 'Unshipped';
        $param['OrderStatus.Status.2'] = 'PartiallyShipped';
//        $param['OrderStatus.Status.1'] = 'Shipped';
//        $param['FulfillmentChannel.Channel.1'] = 'MFN';
        $from = Amazon::get_order_dates();
        $from = $from['api_pullfrom'];
//        $from = "-1";
        $from .= ' days';
        $createdAfter = new DateTime($from, new DateTimeZone('America/Boise'));
        $createdAfter = $createdAfter->format("Y-m-d\TH:i:s\Z");
        $param['CreatedAfter'] = $createdAfter;

        $xml = '';

        $response = AmazonClient::amazonCurl($xml, $feed, $version, $param, $whatToDo);

        return $response;
    }

    public function parseOrders($orders, $nextPage = null)
    {

        $xmlOrders = simplexml_load_string($orders);

        $page = "ListOrdersResult";
        if ($nextPage) {
            $page = "ListOrdersByNextTokenResult";
        }
        foreach ($xmlOrders->{$page}->Orders->Order as $order) {
            $this->parseOrder($order);
        }

        if (isset($xmlOrders->{$page}->NextToken)) {
            $nextToken = (string)$xmlOrders->{$page}->NextToken;
            Ecommerce::dd("Next Token:" . $nextToken);
        }
        if (isset($nextToken)) {
            $orders = $this->getMoreOrders($nextToken);

            $this->parseOrders($orders, true);
        }
    }

    public function parseOrder($order)
    {
        $orderNum = $order->AmazonOrderId;

        $found = Order::get($orderNum);

        if (LOCAL || !$found) {

            $this->orderFound($order, $orderNum);
        }
    }

    public function orderFound($order, $orderNum)
    {
        Ecommerce::dd($order);
        $channelName = 'Amazon';

        $purchaseDate = rtrim((string)$order->PurchaseDate, 'Z');

        $orderTotal = (object)$order->OrderTotal;
        $total = (float)$orderTotal->Amount;

        $latestDeliveryDate = (string)$order->LatestDeliveryDate;
        $orderType = (string)$order->OrderType;
        $isReplacementOrder = (string)$order->IsReplacementOrder;
        $numberOfItemsShipped = (int)$order->NumberOfItemsShipped;
        $numberOfItemsUnshipped = (int)$order->NumberOfItemsUnshipped;
        $orderStatus = (string)$order->OrderStatus;
        $salesChannel = (string)$order->SalesChannel;
        $isBusinessOrder = (string)$order->IsBusinessOrder;
        $lastUpdateDate = (string)$order->LastUpdateDate;
        $shipServiceLevel = (string)$order->ShipServiceLevel;
        $shippedByAmazonTFM = (string)$order->ShippedByAmazonTFM;
        $paymentMethodDetails = (object)$order->PaymentMethodDetails;
        $paymentMethodDetail = (string)$order->PaymentMethodDetail;
        $paymentMethod = (string)$order->PaymentMethod;
        $earliestDeliveryDate = (string)$order->EarliestDeliveryDate;
        $earliestShipDate = (string)$order->EarliestShipDate;
        $isPremiumOrder = (string)$order->IsPremiumOrder;
        $marketplaceId = (string)$order->MarketplaceId;
        $fulfillmentChannel = (string)$order->FulfillmentChannel;
        $isPrime = (string)$order->IsPrime;
        $buyer = (string)$order->BuyerName;



        $shipByDate = (string)$order->LatestShipDate;

        $shipmentMethod = (string)$order->ShipmentServiceLevelCategory;
        $shippingCode = Order::shippingCode($total, [], $shipmentMethod);
        $shippingPrice = 0.00;

        $tax = 0.00;


        //Address
        $shippingAddress = (object)$order->ShippingAddress;
        $streetAddress = (string)$shippingAddress->AddressLine1;
        $streetAddress2 = (string)$shippingAddress->AddressLine2 ?? '';
        $city = (string)$shippingAddress->City;
        $state = (string)$shippingAddress->StateOrRegion;
        $zipCode = (string)$shippingAddress->PostalCode;
        $country = (string)$shippingAddress->CountryCode;
        $country = (string)$country == 'US' ? 'USA' : $country;


        //Buyer
        $shipToName = (string)$order->ShippingAddress->Name;
        $phone = (string)$order->ShippingAddress->Phone;
        $email = (string)$order->BuyerEmail;
        list($lastName, $firstName) = BuyerController::splitName($shipToName);
        $buyer = Order::buyer($firstName, $lastName, $streetAddress, $streetAddress2, $city, $state, $zipCode,
            $country, $phone);


        $Order = new Order(1, $channelName, AmazonClient::getStoreID(), $buyer, $orderNum, $purchaseDate,
            $shippingCode, $shippingPrice, $tax);

        //Save Order
        if (!LOCAL) {
            $Order->save(AmazonClient::getStoreID());
        }

        $this->getItems($Order);

        $tax = $Order->getTax()->get();

        Order::updateShippingAndTaxes($Order->getOrderId(), $Order->getShippingPrice(), $tax);

        $Order->setOrderXml($Order);

        if (!LOCAL) {
            FTPController::saveXml($Order);
        }
    }

    protected function getItems(Order $Order)
    {
        $orderItems = simplexml_load_string($this->getOrderItems($Order->getOrderNum()));
//        Ecommerce::dd($orderItems);

        if (isset($orderItems->ListOrderItemsResult->OrderItems->OrderItem)) {
            $this->parseItems($orderItems->ListOrderItemsResult->OrderItems->OrderItem, $Order);
        } else {
            sleep(2);
            $this->getItems($Order);
        }
    }

    protected function parseItems($items, Order $Order)
    {
        foreach ($items as $item) {
            $this->parseItem($Order, $item);
        }
    }

    protected function parseItem(Order $Order, $item)
    {
        $sku = (string)$item->SellerSKU;
        $Order->setChannelAccount(Channel::getAccountNumbersBySku($Order->getChannelName(), $sku));

        $title = (string)$item->Title;
        $quantity = (int)$item->QuantityOrdered;
        $upc = '';

        $itemPrice = (float)$item->ItemPrice->Amount;
        $promotionDiscount = (float)$item->PromotionDiscount->Amount;
        $itemPrice += (float)$promotionDiscount;

        $giftWrapPrice = (float)$item->GiftWrapPrice->Amount;
        $itemPrice += (float)$giftWrapPrice;

        $totalNoTax = (float)$itemPrice;
        $Order->updateTotalNoTax($totalNoTax);
        $price = (float)$itemPrice / $quantity;

        $shippingPrice = (float)$item->ShippingPrice->Amount;
        $shippingDiscount = (float)$item->ShippingDiscount->Amount;
        $shippingPrice += (float)$shippingDiscount;
        $totalShipping = (float)$shippingPrice;
        $Order->updateShippingPrice($totalShipping);

        $itemTax = Ecommerce::formatMoney((float)$item->ItemTax->Amount);
        $totalTax = (float)$itemTax;

        $shippingTax = (float)$item->ShippingTax->Amount;
        $totalTax += (float)$shippingTax;

        $giftWrapTax = (float)$item->GiftWrapTax->Amount;
        $totalTax += (float)$giftWrapTax;
        $totalTax = Ecommerce::formatMoney($totalTax);
        $Order->getTax()->updateTax($totalTax);

        $orderItem = new OrderItem($sku, $title, $quantity, $price, $upc, $Order->getPoNumber());
        $Order->setOrderItems($orderItem);
        if (!LOCAL) {
            $orderItem->save($Order);
        }
    }

}