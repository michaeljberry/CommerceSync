<?php

namespace Amazon;

use ecommerce\Ecommerce;
use controllers\channels\XMLController;
use Amazon\API\AmazonAPI;
use Amazon\API\FulfillmentInventory\ListInventorySupply;

class AmazonInventory extends AmazonClient
{

    public function getFbaInventory($sku)
    {

        $fbaInventory = new ListInventorySupply($sku);

        $xml = '';

        return AmazonClient::amazonCurl($xml, $fbaInventory);

    }

    public function updateAmazonInventory($xml1)
    {

        $action = 'SubmitFeed';
        $feedType = '_POST_PRODUCT_DATA_';
        $feed = 'Feeds';
        $whatToDo = 'POST';

        $xml = [
            'MessageType' => 'Product'
        ];
        $xml = XMLController::makeXML($xml);
        $xml .= $xml1;

        $paramAdditionalConfig = [
            'MarketplaceId',
            'SellerId',
        ];

        AmazonClient::setParameters($action, $feedType, $feed, $paramAdditionalConfig);

        return AmazonClient::amazonCurl($xml, $feed, $whatToDo);

    }

    public function updateAmazonInventoryPrice($xml1)
    {

        $action = 'SubmitFeed';
        $feedType = '_POST_PRODUCT_PRICING_DATA_';
        $feed = 'Feeds';
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

        AmazonClient::setParameters($action, $feedType, $feed, $paramAdditionalConfig);

        return AmazonClient::amazonCurl($xml, $feed, $whatToDo);

    }

    public function listMatchingProducts($searchTerm)
    {

        $action = ucfirst(__FUNCTION__);
        $feedType = '';
        $feed = 'Products';
        $whatToDo = 'POST';

        $xml = '';

        $paramAdditionalConfig = [
            'Merchant',
            'MarketplaceId.Id.1',
            'PurgeAndReplace',
            'MarketplaceId'
        ];

        AmazonClient::setParameters($action, $feedType, $feed, $paramAdditionalConfig);

        AmazonClient::setParameterByKey("Query", $searchTerm);

        return AmazonClient::amazonCurl($xml, $feed, $whatToDo);

    }

    public function getMatchingProduct($asin)
    {

        $action = ucfirst(__FUNCTION__);
        $feedType = '';
        $feed = 'Products';
        $whatToDo = 'POST';

        $xml = '';

        $paramAdditionalConfig = [
            'Merchant',
            'MarketplaceId.Id.1',
            'PurgeAndReplace',
            'MarketplaceId'
        ];

        AmazonClient::setParameters($action, $feedType, $feed, $paramAdditionalConfig);

        AmazonClient::setParameterByKey("ASINList.ASIN.1", $asin);

        return AmazonClient::amazonCurl($xml, $feed, $whatToDo);

    }

    public function GetMyPriceForSKU($sku)
    {

        $action = ucfirst(__FUNCTION__);
        $feedType = '';
        $feed = 'Products';
        $whatToDo = 'POST';

        $xml = '';

        $paramAdditionalConfig = [
            'Merchant',
            'PurgeAndReplace',
            'MarketplaceId',
            'SellerId',
        ];

        AmazonClient::setParameters($action, $feedType, $feed, $paramAdditionalConfig);

        AmazonClient::setParameterByKey("SellerSKUList.SellerSKU.1", $sku);

        return AmazonClient::amazonCurl($xml, $feed, $whatToDo);

    }

    public function listNewProduct($sku)
    {

        $action = 'SubmitFeed';
        $feedType = '_POST_PRODUCT_DATA_';
        $feed = 'Feeds';
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

        AmazonClient::setParameters($action, $feedType, $feed);

        return AmazonClient::amazonCurl($xml, $feed, $whatToDo);

    }

    public function getProductInfo($asin)
    {

        $action = 'GetMatchingProductForId';
        $feedType = '';
        $feed = 'Products';
        $whatToDo = 'POST';

        $xml = '';

        AmazonClient::setParameters($action, $feedType, $feed);

        AmazonClient::setParameterByKey("IdType", "ASIN");
        AmazonClient::setParameterByKey("IdList.Id.1", $asin);

        return AmazonClient::amazonCurl($xml, $feed, $whatToDo);

    }

    public function getFlatFile()
    {

        $action = 'RequestReport';
        $feedType = '_GET_FLAT_FILE_OPEN_LISTINGS_DATA_';
        $feed = 'doc';
        $whatToDo = 'POST';

        $xml = '';

        $paramAdditionalConfig = [
            'Merchant',
            'MarketplaceId.Id.1',
            'PurgeAndReplace'
        ];

        AmazonClient::setParameters($action, $feedType, $feed, $paramAdditionalConfig);

        AmazonClient::setParameterByKey("ReportType", $feedType);

        return AmazonClient::amazonCurl($xml, $feed, $whatToDo);

    }

    public function getLowestOfferListingsForSKU($sku)
    {

        $action = ucfirst(__FUNCTION__);
        $feedType = '';
        $feed = 'Products';
        $whatToDo = 'POST';

        $xml = '';

        $paramAdditionalConfig = [
            'MarketplaceId',
            'SellerId',
        ];

        AmazonClient::setParameters($action, $feedType, $feed, $paramAdditionalConfig);

        AmazonClient::setParameterByKey("ItemCondition", "New");
        AmazonClient::setParameterByKey("SellerSKUList.SellerSKU.1", $sku);

        return AmazonClient::amazonCurl($xml, $feed, $whatToDo);

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
        return XMLController::makeXML($xml);

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
        return XMLController::makeXML($xml);

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
        return XMLController::makeXML($xml);

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
        return XMLController::makeXML($xml);

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

        return Ecommerce::sortBy($listings, 'total');

    }

}