<?php

namespace eb;

class EbayOrder extends Ebay
{

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

    public function update_ebay_tracking($tracking_id, $carrier, $item_id, $trans_id){
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

        $response = $this->ebayCurl($requestName, $xml);
        return $response;
    }

    public function getOrdersRequestXML()
    {

    }
}