<?php

namespace Amazon;

use Ecommerce\Ecommerce;
use models\channels\Channel;
use models\channels\order\{Order, OrderItem};
use controllers\channels\{FTPController, BuyerController, XMLController};
use Amazon\API\Orders\{ListOrders, ListOrderItems, ListOrdersByNextToken, GetOrder};

class AmazonOrder extends AmazonClient
{

    public static function getOrderById($amazonOrderId)
    {

        return AmazonClient::amazonCurl(new GetOrder($amazonOrderId));

    }

    protected static function getMoreOrders($nextToken)
    {

        return AmazonClient::amazonCurl(new ListOrdersByNextToken($nextToken));

    }

    public static function getOrderItems($orderNumber)
    {

        return AmazonClient::amazonCurl(new ListOrderItems($orderNumber));

    }

    public static function getMoreOrderItems($nextItemToken)
    {

        return AmazonClient::amazonCurl(new ListOrderItemsByNextToken($nextItemToken));

    }

    public static function getUnshippedOrders()
    {

        $orderStatus = [
            'Unshipped',
            'PartiallyShipped'
        ];

        return AmazonClient::amazonCurl(new ListOrders($orderStatus));

    }

    public static function parseOrders($orders, $nextPage = null)
    {

        if(!$orders)
        {

            return;

        }

        $xmlOrders = simplexml_load_string($orders);

        $orderPage = "ListOrdersResult";

        if ($nextPage)
        {

            $orderPage = "ListOrdersByNextTokenResult";

        }

        foreach ($xmlOrders->{$orderPage}->Orders->Order as $order)
        {

            static::parseOrder($order);

        }

        if (isset($xmlOrders->{$orderPage}->NextToken))
        {

            $nextToken = (string)$xmlOrders->{$orderPage}->NextToken;
            Ecommerce::dd("Next Token: $nextToken");

        }

        if (isset($nextToken))
        {

            $orders = static::getMoreOrders($nextToken);

            static::parseOrders($orders, true);

        }

    }

    public static function parseOrder($order)
    {

        $orderNumber = $order->AmazonOrderId;
        $found = Order::get($orderNumber);

        if (LOCAL || !$found)
        {

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

        if (!LOCAL)
        {

            $Order->save(AmazonClient::getStoreId());

        }

        $Order->setOrderId();

        static::parseItems($Order, static::getOrderItems($Order->getOrderNumber()));

        $tax = $Order->getTax()->get();

        Order::updateShippingAndTaxes($Order->getOrderId(), $Order->getShippingPrice(), $tax);

        $Order->setOrderXml($Order);

        if (!LOCAL)
        {

            FTPController::saveXml($Order);

        }

    }

    protected static function parseItems(Order $Order, $items, $nextItemPage = null)
    {

        $orderItems = simplexml_load_string($items);

        $itemPage = "ListOrderItemsResult";

        if($nextItemPage)
        {

            $itemPage = "ListOrderItemsByNextTokenResult";

        }

        if (isset($orderItems->{$itemPage}->OrderItems->OrderItem))
        {

            foreach ($orderItems->{$itemPage}->OrderItems->OrderItem as $item)
            {

                static::parseItem($Order, $item);

            }

            if(isset($orderItems->{$itemPage}->NextToken))
            {

                $nextItemToken = (string)$orderItems->{$itemPage}->NextToken;
                Ecommerce::dd("Next Item Token: $nextToken");

            }

            if(isset($nextItemToken))
            {

                $orderItems = static::getMoreOrderItems($nextItemToken);
                static::getItems($Order, $orderItems, true);

            }

        } else {

            sleep(2);
            static::getItems($Order);

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

        if (!LOCAL)
        {

            $orderItem->save($Order);

        }

    }

}