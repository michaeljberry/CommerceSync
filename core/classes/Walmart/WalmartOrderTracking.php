<?php

namespace Walmart;

use controllers\channels\order\ChannelOrderTracking;
use controllers\channels\order\ChannelTracking;
use Ecommerce\Ecommerce;
use Exception;
use WalmartAPI\Order as WMOrder;

class WalmartOrderTracking extends ChannelOrderTracking
{

    /**
     * If shipped return updated as true
     *
     * @param $response
     * @return bool
     */
    public function updated($response)
    {
        if ($this->shipped($response)) {
            return true;
        }
        return false;
    }

    /**
     * Check if order has already shipped
     *
     * @param $order
     * @return bool
     */
    protected function shipped($order)
    {
        if (isset(
                $order['orderLines']['orderLine']['orderLineStatuses']['orderLineStatus']['trackingInfo']) &&
            array_key_exists('trackingInfo', $order['orderLines']['orderLine']['orderLineStatuses']['orderLineStatus'])
        ) {
            return true;
        }
        return false;
    }

    /**
     * Update shipping information for $walmartOrderTracking (order)
     *
     * @param ChannelTracking $walmartTracking
     * @param ChannelOrderTracking $walmartOrderTracking
     * @return array
     */
    public function updateTracking(ChannelTracking $walmartTracking, ChannelOrderTracking $walmartOrderTracking)
    {
        $carrier = $walmartOrderTracking->getCarrier();

        $order = WalmartOrder::getOrder($walmartOrderTracking->getOrderNumber());

        if ($this->shipped($order)) {
            return $order;
        }

        $trackingURL = '';
        if ($carrier == 'USPS') {
            $trackingURL = "https://tools.usps.com/go/TrackConfirmAction.action";
        } elseif ($carrier == 'UPS') {
            $trackingURL = "http://wwwapps.ups.com/WebTracking/track";
        }
        $tracking = $this->processTracking($order, $walmartOrderTracking, $trackingURL);

        return $tracking;
    }

    /**
     * Check for multiple order items
     *
     * @param $order
     * @param ChannelOrderTracking $walmartOrderTracking
     * @param $trackingURL
     * @return array
     */
    protected function processTracking($order, ChannelOrderTracking $walmartOrderTracking, $trackingURL)
    {
        if (array_key_exists('lineNumber', $order['orderLines']['orderLine'])) {
            $tracking = $this->processOrderLineTracking($order['orderLines'], $walmartOrderTracking, $trackingURL);
        } else {
            foreach ($order['orderLines']['orderLine'] as $o) {
                $tracking = $this->processOrderLineTracking($order['orderLines']['orderLine'], $walmartOrderTracking,
                    $trackingURL);
            }
        }
        return $tracking;
    }

    /**
     * Process each order item to send shipping information
     *
     * @param $order
     * @param ChannelOrderTracking $walmartOrderTracking
     * @param $trackingURL
     * @return array
     */
    public function processOrderLineTracking($order, ChannelOrderTracking $walmartOrderTracking, $trackingURL)
    {
        foreach ($order as $o) {
            $lineNumber = $o['lineNumber'];
            $quantity = $o['orderLineQuantity']['amount'];
            $response = $this->shipOrderItem($walmartOrderTracking, $trackingURL, $lineNumber, $quantity);
        }
        return $response;
    }

    /**
     * Send shipping information to WalmartAPI
     *
     * @param ChannelOrderTracking $walmartOrderTracking
     * @param $trackingURL
     * @param $lineNumber
     * @param $quantity
     * @return array
     */
    protected function shipOrderItem(
        ChannelOrderTracking $walmartOrderTracking,
        $trackingURL,
        $lineNumber,
        $quantity
    ) {
        try {
            $response = WalmartOrder::configure()->ship(
                $walmartOrderTracking->getOrderNumber(),
                $this->createTrackingArray($walmartOrderTracking, $trackingURL, $lineNumber, $quantity)
            );
            return $response;
        } catch (Exception $e) {
            Ecommerce::dd("There was a problem sending the data: " . $e->getMessage());
        }
    }

    /**
     * Create array of information to send to WalmartAPI
     *
     * @param ChannelOrderTracking $walmartOrderTracking
     * @param $trackingURL
     * @param $lineNumber
     * @param $quantity
     * @return array
     */
    public function createTrackingArray(
        ChannelOrderTracking $walmartOrderTracking,
        $trackingURL,
        $lineNumber,
        $quantity
    ): array {
        return [
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
                                    'shipDateTime' => date("Y-m-d") . "T" . date("H:i:s") . "Z",
                                    'carrierName' => [
                                        'carrier' => $walmartOrderTracking->getCarrier()
                                    ],
                                    'methodCode' => 'Standard',
                                    'trackingNumber' => $walmartOrderTracking->getTrackingNumber(),
                                    'trackingURL' => $trackingURL
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}
