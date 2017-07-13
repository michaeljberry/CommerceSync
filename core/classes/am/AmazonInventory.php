<?php

namespace am;

use ecommerce\Ecommerce;
use controllers\channels\XMLController;

class AmazonInventory extends Amazon
{
    public function getFbaInventory($sku)
    {
        $action = 'ListInventorySupply';
        $feedtype = '';
        $feed = 'FulfillmentInventory';
        $version = AmazonClient::getAPIFeedInfo($feed)['versionDate'];
        $whatToDo = 'POST';

        $xml = '';

        $paramAdditionalConfig = [
            'Merchant',
            'MarketplaceId.Id.1',
            'PurgeAndReplace',
            'MarketplaceId'
        ];

        $param = AmazonClient::setParams($action, $feedtype, $version, $paramAdditionalConfig);

        $param['ResponseGroup'] = 'Basic';

        if (is_array($sku)) {
            for ($i = 0; $i < count($sku); $i++) {
                $n_sku = $sku[$i];
                $item = $i + 1;
                $param["SellerSkus.member.$item"] = trim($n_sku);
            }
        } else {
            $param['SellerSkus.member.1'] = $sku;
        }

        $response = AmazonClient::amazonCurl($xml, $feed, $version, $param, $whatToDo);

        return $response;
    }

    public function updateAmazonInventory($xml1)
    {
        $action = 'SubmitFeed';
        $feedtype = '_POST_PRODUCT_DATA_';
        $feed = 'Feeds';
        $version = AmazonClient::getAPIFeedInfo($feed)['versionDate'];
        $whatToDo = 'POST';

        $xml = [
            'MessageType' => 'Product'
        ];
        $xml = XMLController::makeXML($xml);
        $xml .= $xml1;
//        Ecommerce::dd($xml);

        $paramAdditionalConfig = [
            'MarketplaceId',
            'SellerId',
        ];

        $param = AmazonClient::setParams($action, $feedtype, $version, $paramAdditionalConfig);

        $response = AmazonClient::amazonCurl($xml, $feed, $version, $param, $whatToDo);

        return $response;
    }

    public function updateAmazonInventoryPrice($xml1)
    {
        $action = 'SubmitFeed';
        $feedtype = '_POST_PRODUCT_PRICING_DATA_';
        $feed = 'Feeds';
        $version = AmazonClient::getAPIFeedInfo($feed)['versionDate'];
        $whatToDo = 'POST';

        $xml = [
            'MessageType' => 'Price'
        ];
        $xml = XMLController::makeXML($xml);
        $xml .= $xml1;

        $paramAdditionalConfig = [
            'MarketplaceId',
            'SellerId',
        ];

        $param = AmazonClient::setParams($action, $feedtype, $version, $paramAdditionalConfig);

        $response = AmazonClient::amazonCurl($xml, $feed, $version, $param, $whatToDo);

        return $response;
    }

    public function listMatchingProducts($searchTerm)
    {
        $action = ucfirst(__FUNCTION__);
        $feedtype = '';
        $feed = 'Products';
        $version = AmazonClient::getAPIFeedInfo($feed)['versionDate'];
        $whatToDo = 'POST';

        $xml = '';

        $paramAdditionalConfig = [
            'Merchant',
            'MarketplaceId.Id.1',
            'PurgeAndReplace',
            'MarketplaceId'
        ];

        $param = AmazonClient::setParams($action, $feedtype, $version, $paramAdditionalConfig);

        $param['Query'] = $searchTerm;

        $response = AmazonClient::amazonCurl($xml, $feed, $version, $param, $whatToDo);

        return $response;
    }

    public function getMatchingProduct($asin)
    {
        $action = ucfirst(__FUNCTION__);
        $feedtype = '';
        $feed = 'Products';
        $version = AmazonClient::getAPIFeedInfo($feed)['versionDate'];
        $whatToDo = 'POST';

        $xml = '';

        $paramAdditionalConfig = [
            'Merchant',
            'MarketplaceId.Id.1',
            'PurgeAndReplace',
            'MarketplaceId'
        ];

        $param = AmazonClient::setParams($action, $feedtype, $version, $paramAdditionalConfig);

        $param['ASINList.ASIN.1'] = $asin;

        $response = AmazonClient::amazonCurl($xml, $feed, $version, $param, $whatToDo);

        return $response;
    }

    public function GetMyPriceForSKU($sku)
    {
        $action = ucfirst(__FUNCTION__);
        $feedtype = '';
        $feed = 'Products';
        $version = AmazonClient::getAPIFeedInfo($feed)['versionDate'];
        $whatToDo = 'POST';

        $xml = '';

        $paramAdditionalConfig = [
            'Merchant',
            'PurgeAndReplace',
            'MarketplaceId',
            'SellerId',
        ];

        $param = AmazonClient::setParams($action, $feedtype, $version, $paramAdditionalConfig);

        $param['SellerSKUList.SellerSKU.1'] = $sku;

        $response = AmazonClient::amazonCurl($xml, $feed, $version, $param, $whatToDo);

        return $response;
    }

    public function listNewProduct($sku)
    {
        $action = 'SubmitFeed';
        $feedtype = '_POST_PRODUCT_DATA_';
        $feed = 'Feeds';
        $version = AmazonClient::getAPIFeedInfo($feed)['versionDate'];
        $whatToDo = 'POST';

        $xml = [
            'MessageType' => 'Product',
            'PurgeAndReplace' => 'false',
            'Message' => [
                'MessageID' => '1',
                'OperationType' => 'Insert',
                'Product' => [
                    'SKU' => $sku,
                    'ProductTaxCode' => 'A_GEN_TAX',
                    'DescriptionData' => [
                        'Title' => $title,
                        'Brand' => $brand,
                        'Description' => $description,
                        'ProductData' => [
                            $category => $category
                        ]
                    ]
                ]
            ]
        ];

        foreach ($bullets as $b) {
            $xml['Message']['Product']['DescriptionData']['Bullet'][] = $b;
        }

        $param = AmazonClient::setParams($action, $feedtype, $version);

        $response = AmazonClient::amazonCurl($xml, $feed, $version, $param, $whatToDo);

        return $response;
    }

    public function getProductInfo($asin)
    {
        $action = 'GetMatchingProductForId';
        $feedtype = '';
        $feed = 'Products';
        $version = AmazonClient::getAPIFeedInfo($feed)['versionDate'];
        $whatToDo = 'POST';

        $xml = '';

        $param = AmazonClient::setParams($action, $feedtype, $version);

        $param['IdType'] = 'ASIN';
        $param['IdList.Id.1'] = $asin;

        $response = AmazonClient::amazonCurl($xml, $feed, $version, $param, $whatToDo);

        return $response;
    }

    public function getFlatFile()
    {
        $action = 'RequestReport';
        $feedtype = '_GET_FLAT_FILE_OPEN_LISTINGS_DATA_';
        $feed = 'doc';
        $version = AmazonClient::getAPIFeedInfo($feed)['versionDate'];
        $whatToDo = 'POST';

        $xml = '';

        $paramAdditionalConfig = [
            'Merchant',
            'MarketplaceId.Id.1',
            'PurgeAndReplace'
        ];

        $param = AmazonClient::setParams($action, $feedtype, $version, $paramAdditionalConfig);

        $param['ReportType'] = $feedtype;

        $response = AmazonClient::amazonCurl($xml, $feed, $version, $param, $whatToDo);

        return $response;
    }

    public function getLowestOfferListingsForSKU($sku)
    {
        $action = ucfirst(__FUNCTION__);
        $feedtype = '';
        $feed = 'Products';
        $version = AmazonClient::getAPIFeedInfo($feed)['versionDate'];
        $whatToDo = 'POST';

        $xml = '';

        $paramAdditionalConfig = [
            'MarketplaceId',
            'SellerId',
        ];

        $param = AmazonClient::setParams($action, $feedtype, $version, $paramAdditionalConfig);

        $param['ItemCondition'] = 'New';
        $param['SellerSKUList.SellerSKU.1'] = $sku;

        $response = AmazonClient::amazonCurl($xml, $feed, $version, $param, $whatToDo);

        return $response;

    }

    public function create_inventory_update_item_xml($sku, $quantity, $num)
    {
        $xml = [
            'Message' => [
                'MessageID' => $num,
                'Inventory' => [
                    'SKU' => $sku,
                    'Quantity' => $quantity
                ]
            ]
        ];
        $amazon_feed = XMLController::makeXML($xml);

        return $amazon_feed;
    }

    public function create_inventory_price_update_item_xml($sku, $price, $num)
    {
        $xml = [
            'Message' => [
                'MessageID' => $num,
                'Price' => [
                    'SKU' => $sku,
                    'StandardPrice~currency=USD' => $price
                ]
            ]
        ];
        $amazon_feed = XMLController::makeXML($xml);
        return $amazon_feed;
    }

    public function updateTaxCode($sku, $asin, $num)
    {
        $xml = [
            'Message' => [
                'MessageID' => $num,
                'OperationType' => 'Update',
                'Product' => [
                    'SKU' => $sku,
                    'StandardProductID' => [
                        'Type' => 'ASIN',
                        'Value' => $asin
                    ],
                    'ProductTaxCode' => 'A_GEN_TAX'
                ]
            ]
        ];
        $amazonFeed = XMLController::makeXML($xml);
        return $amazonFeed;
    }

    public function updateShippingPrice($sku, $shipping, $num)
    {
        $xml = [
            'Message' => [
                'MessageID' => $num,
                'OperationType' => 'Update',
                'Product' => [
                    'SKU' => $sku,
                    'ShippingOverride' => [
                        'ShipAmount' => $shipping
                    ],
                ]
            ]
        ];
        $amazonFeed = XMLController::makeXML($xml);
        return $amazonFeed;
    }

    public function sortAmazonSearchResults($response)
    {
        foreach ($response->GetLowestOfferListingsForSKUResult->Product->LowestOfferListings->LowestOfferListing as $product) {
            $price = ((float)$product->Price->ListingPrice->Amount * 100) / 100;
            $shipping = ((float)$product->Price->Shipping->Amount * 100) / 100;
            $total = ((float)$product->Price->LandedPrice->Amount * 100) / 100;
            $numOfListingsAtThisPrice = (string)$product->NumberOfOfferListingsConsidered;
            $sellerRating = (string)$product->Qualifiers->SellerPositiveFeedbackRating;
            $shippingTime = (string)$product->Qualifiers->ShippingTime->Max;

            $listings[] = compact('numOfListingsAtThisPrice', 'sellerRating', 'shippingTime', 'price', 'shipping', 'total');
        }
        $sellers = Ecommerce::sortBy($listings, 'total');

        return $sellers;
    }
}