<?php

namespace Walmart;

use controllers\channels\order\ChannelOrderTracking;
use controllers\channels\order\ChannelTracking;
use ecommerce\Ecommerce;
use Exception;
use WalmartAPI\Order as WMOrder;

class WalmartOrderTracking extends ChannelOrderTracking
{

    public function updateTracking(ChannelTracking $walmartTracking, ChannelOrderTracking $walmartOrderTracking)
    {
        $orderNumber = $walmartOrderTracking->getOrderNumber();
        $carrier = $walmartOrderTracking->getCarrier();
        $trackingNumber = $walmartOrderTracking->getTrackingNumber();

        $order = WalmartOrder::getOrder($orderNumber);

        if (isset($order['orderLines']['orderLine']['orderLineStatuses']['orderLineStatus']['trackingInfo']) && array_key_exists('trackingInfo',
                $order['orderLines']['orderLine']['orderLineStatuses']['orderLineStatus'])
        ) {
            return $order;
        }
        echo '<br><br>';
        $date = date("Y-m-d") . "T" . date("H:i:s") . "Z";
        echo "Date: $date<br><br>";
//        $order_num = $order['purchaseOrderId'];
        $trackingURL = '';
        if ($carrier == 'USPS') {
            $trackingURL = "https://tools.usps.com/go/TrackConfirmAction.action";
        } elseif ($carrier == 'UPS') {
            $trackingURL = "http://wwwapps.ups.com/WebTracking/track";
        }
        Ecommerce::dd($order);
        if (array_key_exists('lineNumber', $order['orderLines']['orderLine'])) {
            $tracking = $this->processTracking($order['orderLines'], $orderNumber, $date, $carrier,
                $trackingNumber,
                $trackingURL);
        } else {
            foreach ($order['orderLines']['orderLine'] as $o) {
                $tracking = $this->processTracking($order['orderLines']['orderLine'], $orderNumber, $date,
                    $carrier,
                    $trackingNumber, $trackingURL);
            }
        }

        return $tracking;
    }

    public function processTracking($order, $orderNumber, $date, $carrier, $trackingNumber, $trackingURL)
    {
        foreach ($order as $o) {
            $lineNumber = $o['lineNumber'];
            $quantity = $o['orderLineQuantity']['amount'];
            try {
                $response = WalmartOrder::configure()->ship(
                    $orderNumber,
                    $this->createTrackingArray($lineNumber, $quantity, $date, $carrier, $trackingNumber,
                        $trackingURL)
                );
            } catch (Exception $e) {
                die("There was a problem requesting the data: " . $e->getMessage());
            }
            print_r($response);
        }
        return $response;
    }

    public function createTrackingArray($lineNumber, $quantity, $date, $carrier, $trackingNumber, $trackingURL)
    {
        $tracking = [
            'orderShipment' => [
                'orderLines' => [
                    [
                        'lineNumber' => $lineNumber,
                        'orderLineStatuses' => [
                            [
                                'status' => 'Shipped',
                                'statusQuantity' => [
                                    'unitOfMeasurement' => 'Each',
                                    'amount' => $quantity
                                ],
                                'trackingInfo' => [
                                    'shipDateTime' => $date,
                                    'carrierName' => [
                                        'carrier' => $carrier
                                    ],
                                    'methodCode' => 'Standard',
                                    'trackingNumber' => $trackingNumber,
                                    'trackingURL' => $trackingURL
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
        return $tracking;
    }

    public function updated($response)
    {
        if (isset($response['orderLines']['orderLine']['orderLineStatuses']['orderLineStatus']['trackingInfo']['trackingNumber'])) {
            return true;
        }
        return false;
    }
}
