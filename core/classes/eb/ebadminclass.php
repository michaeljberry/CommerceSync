<?php
/**
 * Created by PhpStorm.
 * User: marketing
 * Date: 11/25/15
 * Time: 11:35 AM
 */

namespace ebad;

use eb\ebayclass;

class ebadminclass extends ebayclass
{
    public function get_ebay_categories(){
        $requestName = 'GetCategories';

        $xml = [
            'CategorySiteID' => '0',
            'DetailLevel' => 'ReturnAll',
            'ViewAllNodes' => 'false',
        ];

        $response = $this->ebayCurl($requestName, $xml);
        return $response;
    }
}