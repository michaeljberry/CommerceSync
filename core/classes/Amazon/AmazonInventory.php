<?php

namespace Amazon;

use ecommerce\Ecommerce;
use controllers\channels\XMLController;
use Amazon\API\AmazonAPI;
use Amazon\API\FulfillmentInventory\ListInventorySupply;
use Amazon\API\Feeds\SubmitFeed;

class AmazonInventory extends AmazonClient
{

    public function getFbaInventory($sku)
    {

        $fbaInventory = new ListInventorySupply($sku);

        $xmlArray = '';

        return AmazonClient::amazonCurl($xmlArray, $fbaInventory);

    }

    public function updateInventory($xml1)
    {

        $feedType = '_POST_PRODUCT_DATA_';

        $xmlArray = [
            'MessageType' => 'Product'
        ];

        $xmlArray = array_merge($xmlArray, $xml1);

        $inventoryUpdate = new SubmitFeed($feedType);

        return AmazonClient::amazonCurl($xmlArray, $inventoryUpdate);

    }

    public function updatePrice($xml1)
    {

        $feedType = '_POST_PRODUCT_PRICING_DATA_';

        $xmlArray = [
            'MessageType' => 'Price'
        ];

        $xmlArray = array_merge($xmlArray, $xml1);

        $inventoryUpdate = new SubmitFeed($feedType);

        return AmazonClient::amazonCurl($xmlArray, $inventoryUpdate);

    }

    public function listMatchingProducts($searchTerm)
    {

        $action = ucfirst(__FUNCTION__);
        $feedType = '';
        $feed = 'Products';
        $whatToDo = 'POST';

        $xmlArray = '';

        $paramAdditionalConfig = [
            'Merchant',
            'MarketplaceId.Id.1',
            'PurgeAndReplace',
            'MarketplaceId'
        ];

        AmazonClient::setParameters($action, $feedType, $feed, $paramAdditionalConfig);

        AmazonClient::setParameterByKey("Query", $searchTerm);

        return AmazonClient::amazonCurl($xmlArray, $feed, $whatToDo);

    }

    public function getMatchingProduct($asin)
    {

        $action = ucfirst(__FUNCTION__);
        $feedType = '';
        $feed = 'Products';
        $whatToDo = 'POST';

        $xmlArray = '';

        $paramAdditionalConfig = [
            'Merchant',
            'MarketplaceId.Id.1',
            'PurgeAndReplace',
            'MarketplaceId'
        ];

        AmazonClient::setParameters($action, $feedType, $feed, $paramAdditionalConfig);

        AmazonClient::setParameterByKey("ASINList.ASIN.1", $asin);

        return AmazonClient::amazonCurl($xmlArray, $feed, $whatToDo);

    }

    public function GetMyPriceForSKU($sku)
    {

        $action = ucfirst(__FUNCTION__);
        $feedType = '';
        $feed = 'Products';
        $whatToDo = 'POST';

        $xmlArray = '';

        $paramAdditionalConfig = [
            'Merchant',
            'PurgeAndReplace',
            'MarketplaceId',
            'SellerId',
        ];

        AmazonClient::setParameters($action, $feedType, $feed, $paramAdditionalConfig);

        AmazonClient::setParameterByKey("SellerSKUList.SellerSKU.1", $sku);

        return AmazonClient::amazonCurl($xmlArray, $feed, $whatToDo);

    }

    public function listNewProduct($sku)
    {

        $feedType = '_POST_PRODUCT_DATA_';

        $xmlArray = [
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

            $xmlArray['Message']['Product']['DescriptionData']['Bullet'][] = $b;

        }

        $newProduct = new SubmitFeed($feedType);

        return AmazonClient::amazonCurl($xmlArray, $newProduct);

    }

    public function getProductInfo($asin)
    {

        $action = 'GetMatchingProductForId';
        $feedType = '';
        $feed = 'Products';
        $whatToDo = 'POST';

        $xmlArray = '';

        AmazonClient::setParameters($action, $feedType, $feed);

        AmazonClient::setParameterByKey("IdType", "ASIN");
        AmazonClient::setParameterByKey("IdList.Id.1", $asin);

        return AmazonClient::amazonCurl($xmlArray, $feed, $whatToDo);

    }

    public function getFlatFile()
    {

        $action = 'RequestReport';
        $feedType = '_GET_FLAT_FILE_OPEN_LISTINGS_DATA_';
        $feed = 'doc';
        $whatToDo = 'POST';

        $xmlArray = '';

        $paramAdditionalConfig = [
            'Merchant',
            'MarketplaceId.Id.1',
            'PurgeAndReplace'
        ];

        AmazonClient::setParameters($action, $feedType, $feed, $paramAdditionalConfig);

        AmazonClient::setParameterByKey("ReportType", $feedType);

        return AmazonClient::amazonCurl($xmlArray, $feed, $whatToDo);

    }

    public function getLowestOfferListingsForSKU($sku)
    {

        $action = ucfirst(__FUNCTION__);
        $feedType = '';
        $feed = 'Products';
        $whatToDo = 'POST';

        $xmlArray = '';

        $paramAdditionalConfig = [
            'MarketplaceId',
            'SellerId',
        ];

        AmazonClient::setParameters($action, $feedType, $feed, $paramAdditionalConfig);

        AmazonClient::setParameterByKey("ItemCondition", "New");
        AmazonClient::setParameterByKey("SellerSKUList.SellerSKU.1", $sku);

        return AmazonClient::amazonCurl($xmlArray, $feed, $whatToDo);

    }

    public static function inventoryArray($sku, $quantity, $num)
    {

        $xmlArray = [
            'Message' => [
                'MessageID' => $num,
                'Inventory' => [
                    'SKU' => $sku,
                    'Quantity' => $quantity
                ]
            ]
        ];

        return $xmlArray;

    }

    public static function priceArray($sku, $price, $num)
    {

        $xmlArray = [
            'Message' => [
                'MessageID' => $num,
                'Price' => [
                    'SKU' => $sku,
                    'StandardPrice~currency=USD' => $price
                ]
            ]
        ];

        return $xmlArray;

    }

    public static function taxCodeArray($sku, $asin, $num)
    {

        $xmlArray = [
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

        return $xmlArray;

    }

    public static function shippingPriceArray($sku, $shipping, $num)
    {

        $xmlArray = [
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

        return $xmlArray;

    }

    public static function sortAmazonSearchResults($response)
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

        return Ecommerce::sortBy($listings, 'total');

    }

}