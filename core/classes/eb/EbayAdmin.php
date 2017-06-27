<?php

namespace eb;

class EbayAdmin extends Ebay
{
    public function get_ebay_categories()
    {
        $requestName = 'GetCategories';

        $xml = [
            'CategorySiteID' => '0',
            'DetailLevel' => 'ReturnAll',
            'ViewAllNodes' => 'false',
        ];

        $response = EbayClient::ebayCurl($requestName, $xml);
        return $response;
    }
}