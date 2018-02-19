<?php

namespace Amazon;

use ecommerce\Ecommerce;
use models\channels\Channel;
use models\channels\order\Order;
use models\channels\order\OrderItem;
use controllers\channels\FTPController;
use controllers\channels\BuyerController;
use controllers\channels\XMLController;
use \DateTime;
use \DateTimeZone;

class AmazonOrder extends Amazon
{

    protected static function getMoreOrders($nextToken)
    {

        $action = 'ListOrdersByNextToken';
        $feedtype = '';
        $feed = 'Orders';
        $whatToDo = 'POST';
        $paramAdditionalConfig = [
            'MarketplaceId.Id.1',
            'SellerId',
        ];

        AmazonClient::setParams($action, $feedtype, $feed, $paramAdditionalConfig);

        // $param['NextToken'] = $nextToken;
        AmazonClient::setParameterByKey("NextToken", $nextToken);

        $xml = '';

        return AmazonClient::amazonCurl($xml, $feed, $whatToDo);

    }

    public static function getOrderItems($orderNumber)
    {

        $action = 'ListOrderItems';
        $feedtype = '';
        $feed = 'Orders';
        $whatToDo = 'POST';
        $paramAdditionalConfig = [
            'SellerId'
        ];

        AmazonClient::setParams($action, $feedtype, $feed, $paramAdditionalConfig);

        // $param['AmazonOrderId'] = $orderNumber;
        AmazonClient::setParameterByKey("AmazonOrderId", $orderNumber);

        $xml = '';

        return AmazonClient::amazonCurl($xml, $feed, $whatToDo);

    }

    public static function getOrders()
    {

        $action = 'ListOrders';
        $feedtype = '';
        $feed = 'Orders';
        $whatToDo = 'POST';
        $paramAdditionalConfig = [
            'MarketplaceId.Id.1',
            'SellerId',
        ];

        AmazonClient::setParams($action, $feedtype, $feed, $paramAdditionalConfig);

        // $param['OrderStatus.Status.1'] = 'Unshipped';
        AmazonClient::setParameterByKey("OrderStatus.Status.1", "Unshipped");
        // $param['OrderStatus.Status.2'] = 'PartiallyShipped';
        AmazonClient::setParameterByKey("OrderStatus.Status.2", "PartiallyShipped");
//        $param['OrderStatus.Status.1'] = 'Shipped';
//        $param['FulfillmentChannel.Channel.1'] = 'MFN';
        $from = Amazon::getApiOrderDays();
        $from = $from['api_from'];
//        $from = "-1";
        $from .= ' days';
        $createdAfter = new DateTime($from, new DateTimeZone('America/Boise'));
        $createdAfter = $createdAfter->format("Y-m-d\TH:i:s\Z");
        // $param['CreatedAfter'] = $createdAfter;
        AmazonClient::setParameterByKey("CreatedAfter", $createdAfter);

        $xml = '';

        return AmazonClient::amazonCurl($xml, $feed, $whatToDo);

    }

    public static function parseOrders($orders, $nextPage = null)
    {

        $xmlOrders = simplexml_load_string($orders);

        $page = "ListOrdersResult";

        if ($nextPage) {

            $page = "ListOrdersByNextTokenResult";

        }

        foreach ($xmlOrders->{$page}->Orders->Order as $order) {

            static::parseOrder($order);

        }

        if (isset($xmlOrders->{$page}->NextToken)) {

            $nextToken = (string)$xmlOrders->{$page}->NextToken;
            Ecommerce::dd("Next Token:" . $nextToken);

        }

        if (isset($nextToken)) {

            $orders = static::getMoreOrders($nextToken);

            static::parseOrders($orders, true);

        }

    }

    public static function parseOrder($order)
    {

        $orderNumber = $order->AmazonOrderId;
        $found = Order::get($orderNumber);

        if (LOCAL || !$found) {

            static::orderFound($order, $orderNumber);

        }

    }

    public static function orderFound($order, $orderNumber)
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

        $Order = new Order(1, $channelName, AmazonClient::getStoreId(), $buyer, $orderNumber, $purchaseDate,
            $shippingCode, $shippingPrice, $tax);

        if (!LOCAL) {

            $Order->save(AmazonClient::getStoreId());

        }

        $Order->setOrderId();

        static::getItems($Order);

        $tax = $Order->getTax()->get();

        Order::updateShippingAndTaxes($Order->getOrderId(), $Order->getShippingPrice(), $tax);

        $Order->setOrderXml($Order);

        if (!LOCAL) {

            FTPController::saveXml($Order);

        }

    }

    protected static function getItems(Order $Order)
    {

        $orderItems = simplexml_load_string(static::getOrderItems($Order->getOrderNumber()));

        if (isset($orderItems->ListOrderItemsResult->OrderItems->OrderItem)) {

            static::parseItems($Order, $orderItems->ListOrderItemsResult->OrderItems->OrderItem);

        } else {

            sleep(2);
            static::getItems($Order);

        }

    }

    protected static function parseItems(Order $Order, $items)
    {

        foreach ($items as $item) {

            static::parseItem($Order, $item);

        }

    }

    protected static function parseItem(Order $Order, $item)
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
        Ecommerce::dd($Order);

        if (!LOCAL) {

            $orderItem->save($Order);

        }

    }

}