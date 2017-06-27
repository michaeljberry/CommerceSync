<?php

namespace ecdord;


use ecd\ecdclass;

class ecdordclass extends ecdclass
{
    public function get_orders($ecd_ocp_key, $ecd_sub_key)
    {
        $url = "https://ecomdash.azure-api.net/api/orders/getOrders";
        $parameters = [
            'Status' => 'InProcess',
            'DateType' => 'CreatedDate',
            'FromDate' => date('Y-m-d', strtotime('-1 days')),
            'ToDate' => date('Y-m-d', strtotime('+1 days')),
//            "OnlyNeedsFulfillment" => true,
            'Pagination' => [
                'PageNumber' => 1,
                'ResultsPerPage' => 200
            ]
        ];
        $response = $this->curl_post($ecd_ocp_key, $ecd_sub_key, $url, $parameters);
        return $response;
    }

    public function create_shipment($ecd_ocp_key, $ecd_sub_key, $orderId)
    {
        $url = "https://ecomdash.azure-api.net/api/Shipments/Create";
        $parameters = [
            'OrderId' => $orderId
        ];
        $response = $this->curl_post($ecd_ocp_key, $ecd_sub_key, $url, $parameters);
        return $response;
    }

    public function update_tracking_num($ecd_ocp_key, $ecd_sub_key, $lineItemId, $shipmentId, $carrier, $trackingNumber)
    {
        $url = "https://ecomdash.azure-api.net/api/Shipments/submitTrackingInfo";
        $carrierId = '';
        if ($carrier == 'UPS') {
            $carrierId = '42222';
        } elseif ($carrier == 'USPS') {
            $carrierId = '42226';
        }
        $parameters = [
            'ShipmentId' => $shipmentId,
            'CarrierId' => $carrierId,
            'TrackingNumber' => $trackingNumber,
//            'ShippedDate' => date('Y-m-d'),
        ];
        $response = $this->curl_post($ecd_ocp_key, $ecd_sub_key, $url, $parameters);
        return $response;
    }
}