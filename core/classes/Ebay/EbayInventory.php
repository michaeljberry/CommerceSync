<?php

namespace Ebay;

use models\channels\{Listing, SKU, Stock};
use models\channels\product\{Product, ProductAvailability, ProductPrice};

class EbayInventory extends EbayClient
{

    public static function downloadEbayListings()
    {

        $requestName = 'GetMyeBaySelling';
        for ($x = 1; $x < 51; $x++) {   //123 pages  //200 Entries per page

            $xml = [
                'ActiveList' => [
                    'Pagination' => [
                        'EntriesPerPage' => '200',
                        'PageNumber' => $x
                    ],
                    'Sort' => 'ItemID'
                ]
            ];
            $response = EbayClient::ebayCurl($requestName, $xml);

            $xml_items = simplexml_load_string($response);

            foreach ($xml_items->ActiveList->ItemArray->Item as $xml) {

                $itemID = $xml->ItemID;
                $listing_id = static::saveEbayListing($itemID);
                echo $itemID . ' updated.<br>';

            }

        }

    }

    public static function saveEbayListing($itemID)
    {

        $response = static::getSingleItem($itemID);
        $xml = simplexml_load_string($response);

        $country = $xml->Item->Country;
        $description = $xml->Item->Description;
        $price = $xml->Item->StartPrice;
        $url = $xml->Item->Storefront->StoreURL;
        $listingDuration = $xml->Item->ListingDuration;
        $listingType = $xml->Item->ListingType;
        $primaryCategory = $xml->Item->PrimaryCategory->CategoryID;
        $categoryName = $xml->Item->PrimaryCategory->CategoryName;
        $quantity = $xml->Item->Quantity;
        $isbn = $xml->Item->ProductListingDetails->ISBN;
        $stockPhoto = $xml->Item->ProductListingDetails->StockPhotoURL;
        $globalShipping = $xml->Item->ShippingDetails->GlobalShipping;
        $freeShipping = $xml->Item->ShippingDetails->ShippingServiceOptions[0]->FreeShipping;
        $shippingCost = $xml->Item->ShippingDetails->ShippingServiceOptions->ShippingServiceCost;
        $shippingCostAdditional = $xml->Item->ShippingDetails->ShippingServiceOptions->ShippingServiceAdditionalCost;
        $shippingType = $xml->Item->ShippingDetails->ShippingType;
        $title = $xml->Item->Title;
        $sku = $xml->Item->SKU;
        $photoUrl = $xml->Item->PictureDetails->PictureURL[0];
        $externalPhotoUrl = $xml->Item->ExternalPictureURL;
        $refundOption = $xml->Item->ReturnPolicy->RefundOption;
        $returnswithinoption = $xml->Item->ReturnPolicy->ReturnsWithinOption;
        $returnsacceptedoption = $xml->Item->ReturnPolicy->ReturnsAcceptedOption;
        $productCondition = $xml->Item->ConditionDisplayName;
        $listingStatus = $xml->Item->SellingStatus->ListingStatus;
        $active = 0;

        if ($listingStatus == 'Active') {

            $active = 1;

        }
//
        //find-product-id
        $productID = Product::searchOrInsert($sku, $title, '', $description, '', '');
        //add-product-availability
        $availability_id = ProductAvailability::searchOrInsert($productID, EbayClient::getStoreId());
        //find sku
        $skuID = SKU::searchOrInsert($sku);
        //add price
        $price_id = ProductPrice::searchOrInsert($skuID, $price, EbayClient::getStoreId());
        //normalize condition
        $condition = ConditionController::normalCondition($productCondition);
        //find condition id
        $conditionID = Condition::searchOrInsert($condition);
        //add stock to sku
        $stockID = Stock::searchOrInsert($skuID, $conditionID);
        $listingItemArray = array(
            'store_id' => EbayClient::getStoreId(),
            'stock_id' => $stockID,
            'store_listing_id' => $itemID,
            'price' => $price,
            'url' => $url,
            'title' => $title,
            'description' => $description,
            'active' => $active,
            'country' => $country,
            'listing_duration' => $listingDuration,
            'listing_type' => $listingType,
            'primary_category' => $primaryCategory,
            'category_name' => $categoryName,
            'inventory_level' => $quantity,
            'isbn' => $isbn,
            'stock_photo_url' => $stockPhoto,
            'global_shipping' => $globalShipping,
            'free_shipping' => $freeShipping,
            'shipping_cost' => $shippingCost,
            'shipping_cost_additional' => $shippingCostAdditional,
            'shipping_type' => $shippingType,
            'sku' => $sku,
            'photo_url' => $photoUrl,
            'external_photo_url' => $externalPhotoUrl,
            'refund_option' => $refundOption,
            'returns_within_option' => $returnswithinoption,
            'returns_accepted_option' => $returnsacceptedoption,
            'product_condition' => $productCondition
        );
        $listing_id = Listing::searchOrInsert('listing_ebay', EbayClient::getStoreId(), $stockID, $listingItemArray, 'true');
        return $listing_id;

    }

    public static function updateEbayInventory($stockID, $quantity, $price)
    {

        $itemID = Listing::getChannelListingIdByStockId($stockID, 'listing_ebay');

        $requestName = 'ReviseInventoryStatus';
        $xml = [
            'InventoryStatus' => [
                'ItemID' => $itemID,
                'Quantity' => $quantity
            ]
        ];

        if (!empty($price)) {

            $xml['InventoryStatus']['StartPrice'] = $price;

        }

        $response = EbayClient::ebayCurl($requestName, $xml);
        return $response;

    }

    public static function updateEbayListingPrice($itemID, $price)
    {

        $requestName = 'ReviseItem';

        //Use Tax Table
        $xml = [
            'Item' => [
                'ItemID' => $itemID,
                'StartPrice' => $price
            ]
        ];

        //Shipping Settings
//        $xml = [
//            'Item' => [
//                'ListingDuration' => 'GTC',
//                'ShippingDetails' => [
//                    'ShippingServiceOptions' => [
//                        [
//                            'ShippingService' => 'ShippingMethodStandard',
//                            'FreeShipping' => 'true',
//                            'ShippingServiceCost' => '0.00',
//                            'ShippingServiceAdditionalCost' => '0.00',
//                            'ShippingServicePriority' => '1'
//                        ],
//                        [
//                            'ShippingService' => 'ShippingMethodStandard',
//                            'FreeShipping' => 'false',
//                            'ShippingServiceCost' => '3.99',
//                            'ShippingServiceAdditionalCost' => '1.00',
//                            'ShippingServicePriority' => '1'
//                        ],
//                    ],
//                    'ShippingType' => 'Flat'
//                ],
//                'StartPrice' => $price
//            ]
//        ];

        $response = EbayClient::ebayCurl($requestName, $xml);
        return $response;

    }

    //<IncludeItemSpecifics>true</IncludeItemSpecifics>
    public static function getSingleItem($itemID)
    {

        $requestName = 'GetItem';

        $xml = [
            'DetailLevel' => 'ReturnAll',
            'ItemID' => $itemID
        ];

        $response = EbayClient::ebayCurl($requestName, $xml);
        return $response;

    }

    public static function createEbayListing($categoryID, $title, $description, $upc, $sku, $photoUrl, $quantity, $price)
    {

        $requestName = 'AddItem';

        $xml = [
            'Item' => [
                'AutoPay' => 'true',
                'BuyerRequirementDetails' => [
                    'ShipToRegistrationCountry' => 'true'
                ],
                'CategoryMappingAllowed' => 'true',
                'ConditionID' => '1000',
                'Country' => 'US',
                'Currency' => 'USD',
                'Description' => '<![CDATA[' . $description . ']]>',
                'DispatchTimeMax' => '1',
                'ItemSpecifics' => [
                    'NameValueList' => [
                        [
                            'Name' => 'Brand',
                            'Value' => 'Unbranded',
                        ],
                        [
                            'Name' => 'MPN',
                            'Value' => $sku,
                        ]
                    ],
                ],
                'ListingDuration' => 'GTC',
                'ListingType' => 'FixedPriceItem',
                'PaymentMethods' => 'PayPal',
                'PayPalEmailAddress' => 'mymusiclifecs@gmail.com',
                'PictureDetails' => [
                    'GalleryType' => 'Gallery',
                    'PhotoDisplay' => 'None',
                    'PictureURL' => $photoUrl,
                ],
                'PostalCode' => '83403',
                'PrimaryCategory' => [
                    'CategoryID' => $categoryID,
                ],
                'ProductListingDetails' => [
                ],
                'Quantity' => $quantity,
                'ReturnPolicy' => [
                    'RefundOption' => 'MoneyBack',
                    'ReturnsWithinOption' => 'Days_14',
                    'ReturnsAcceptedOption' => 'ReturnsAccepted',
                    'Description' => 'Restocking fees: 10%',
                    'ShippingCostPaidByOption' => 'Buyer',
                    'RestockingFeeValueOption' => 'Percent_10',
                ],
                'ShippingDetails' => [
                    'ShippingServiceOptions' => [
                        [
                            'ShippingService' => 'ShippingMethodStandard',
                            'FreeShipping' => 'true',
                            'ShippingServiceCost' => '0.00',
                            'ShippingServiceAdditionalCost' => '0.00',
                            'ShippingServicePriority' => '1'
                        ],
                        [
                            'ShippingService' => 'UPSGround',
                            'FreeShipping' => 'false',
                            'ShippingServiceCost' => '9.99',
                            'ShippingServiceAdditionalCost' => '9.99',
                            'ShippingServicePriority' => '2'
                        ],
                    ],
                    'ShippingType' => 'Flat',
                ],
                'Site' => 'US',
                'SKU' => $sku,
                'StartPrice' => $price,
                'Title' => $title,
                'UseTaxTable' => 'true',
            ]
        ];

        if (strlen($upc) > 12) {

            $xml['Item']['ProductListingDetails'] = [
                'EAN' => $upc,
                'UPC' => 'Does not apply',
            ];

        } else {

            $xml['Item']['ProductListingDetails'] = [
                'UPC' => $upc,
                'EAN' => 'Does not apply',
            ];

        }

        $response = EbayClient::ebayCurl($requestName, $xml);
        return $response;

    }

    public static function updateUpc($itemID, $upc, $sku)
    {

        $requestName = 'ReviseItem';

        $xml = [
            'Item' => [
                'ItemID' => $itemID,
                'ItemSpecifics' => [
                    'NameValueList' => [
                        [
                            'Name' => 'Brand',
                            'Value' => 'Unbranded',
                        ],
                        [
                            'Name' => 'MPN',
                            'Value' => $sku,
                        ],
                    ],
                ],
                'ProductListingDetails' => [
                    'ISBN' => 'Does not apply',
                ],
            ]
        ];

        if (strlen($upc) > 12) {

            $xml['Item']['ProductListingDetails'][] = ['EAN' => $upc];
            $xml['Item']['ProductListingDetails'][] = ['UPC' => 'Does not apply'];

        } else {

            $xml['Item']['ProductListingDetails'][] = ['UPC' => $upc];
            $xml['Item']['ProductListingDetails'][] = ['EAN' => 'Does not apply'];

        }

        $response = EbayClient::ebayCurl($requestName, $xml);
        return $response;

    }

    public static function deleteItem($itemID)
    {

        $requestName = 'EndItem';

        $xml = [
            'ItemID' => $itemID,
            'EndingReason' => 'NotAvailable'
        ];

        $response = EbayClient::ebayCurl($requestName, $xml);
        return $response;

    }

    public static function getItem($sku)
    {

        $requestName = 'GetItem';

        $xml = [
            'SKU' => $sku,
//            'ItemID' => $sku,
            'IncludeItemSpecifics' => 'true'
        ];

        $response = EbayClient::ebayCurl($requestName, $xml);
        return $response;

    }

    public static function findItemsAdvanced($keywords)
    {

        $requestName = __FUNCTION__;

        $xml = [
            'keywords' => $keywords,
            'outputSelector' => 'SellerInfo'
        ];

        $response = EbayClient::ebayCurl($requestName, $xml, 'finding');
        return $response;

    }

    public static function findCompletedItems($keywords)
    {

        $requestName = __FUNCTION__;
        $xml = [
            'keywords' => $keywords,
            'outputSelector' => 'SellerInfo',
            'outputSelector' => 'ListingInfo'
        ];
        $response = EbayClient::ebayCurl($requestName, $xml, 'finding');
        return $response;

    }

    public static function geteBayComparableItems($sku)
    {

        $requestName = 'GetSellerList';

        $xml = [
            'SKUArray' => [
                'SKU' => $sku
            ],
            'Pagination' =>
                [
                    'EntriesPerPage' => 50,
                    'PageNumber' => 1
                ],
            'GranularityLevel' => 'Coarse',
//            'OutputSelector' => 'Item.BuyItNowPrice',
//            'OutputSelector' => 'Item.ShippingDetails.ShippingServiceOptions.ShippingServiceCost',
//            'OutputSelector' => 'Seller.UserID',
//            'OutputSelector' => 'Seller.SellerInfo.TopRatedSeller',
            'EndTimeFrom' => date('Y-m-d', strtotime('-120 days')),
            'EndTimeTo' => date('Y-m-d')
        ];

        $response = EbayClient::ebayCurl($requestName, $xml);
        return $response;

    }

    public static function sorteBaySearchResults($response)
    {

        $sellers = [];

        foreach ($response->searchResult->item as $item) {

            $title = (string)$item->title;
            $url = (string)$item->viewItemURL;
            $seller = (string)$item->sellerInfo->sellerUserName;
            $sellerFeedback = (string)$item->sellerInfo->positiveFeedbackPercent;
            $sellerScore = (string)$item->sellerInfo->feedbackScore;
            $price = ((float)$item->sellingStatus->currentPrice * 100) / 100;
            $shippingCollected = ((float)$item->shippingInfo->shippingServiceCost * 100) / 100;
            $total = $price + $shippingCollected;
            $condition = (string)$item->condition->conditionDisplayName;

            $sellers[] = compact(
                "title", "url", "seller", "sellerFeedback", "sellerScore",
                "price", "shippingCollected", "total", "condition"
            );

        }

        $sellers = Ecommerce::sortBy($sellers, 'total');

        return $sellers;

    }

    public static function removeExtraSellerInfo($ebaySellers)
    {

        $x = 0;

        foreach ($ebaySellers as $key => $seller) {

            if ($x > 6) {

                unset($ebaySellers[$key]);

            }

            $sellerName = $seller['seller'];

            if ($sellerName === 'mymusiclife-id' || $x > 0) {

                $x++;

            }

        }

        return $ebaySellers;

    }

}