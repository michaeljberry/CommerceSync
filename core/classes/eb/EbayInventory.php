<?php

namespace eb;

use ecommerce\Ecommerce as ecom;

class EbayInventory extends Ebay
{
    public function sync_ebay_products($store_id, $e){
        $requestName = 'GetMyeBaySelling';
        for($x = 1; $x < 51; $x++){   //123 pages  //200 Entries per page

            $xml = [
                'ActiveList' => [
                    'Pagination' => [
                        'EntriesPerPage' => '200',
                        'PageNumber' => $x
                    ],
                    'Sort' => 'ItemID'
                ]
            ];
            $response = $this->ebayCurl($requestName, $xml);

            $xml_items = simplexml_load_string($response);
            foreach ($xml_items->ActiveList->ItemArray->Item as $xml){
                $item_id = $xml->ItemID;
                $listing_id = $this->sync_inventory_items($item_id, $store_id, $e);
                echo $item_id . ' updated.<br>';
            }
        }
    }

    public function sync_inventory_items($item_id, $store_id, $e){
        $response = $this->getSingleItem($item_id);
        $xml = simplexml_load_string($response);

        $country = $xml->Item->Country;
        $description = $xml->Item->Description;
        $price = $xml->Item->StartPrice;
        $url = $xml->Item->Storefront->StoreURL;
        $listing_duration = $xml->Item->ListingDuration;
        $listing_type = $xml->Item->ListingType;
        $primary_category = $xml->Item->PrimaryCategory->CategoryID;
        $category_name = $xml->Item->PrimaryCategory->CategoryName;
        $quantity = $xml->Item->Quantity;
        $isbn = $xml->Item->ProductListingDetails->ISBN;
        $stock_photo = $xml->Item->ProductListingDetails->StockPhotoURL;
        $global_shipping = $xml->Item->ShippingDetails->GlobalShipping;
        $free_shipping = $xml->Item->ShippingDetails->ShippingServiceOptions[0]->FreeShipping;
        $shipping_cost = $xml->Item->ShippingDetails->ShippingServiceOptions->ShippingServiceCost;
        $shipping_cost_additional = $xml->Item->ShippingDetails->ShippingServiceOptions->ShippingServiceAdditionalCost;
        $shipping_type = $xml->Item->ShippingDetails->ShippingType;
        $title = $xml->Item->Title;
        $sku = $xml->Item->SKU;
        $photo_url = $xml->Item->PictureDetails->PictureURL[0];
        $external_photo_url = $xml->Item->ExternalPictureURL;
        $refund_option = $xml->Item->ReturnPolicy->RefundOption;
        $returnswithinoption = $xml->Item->ReturnPolicy->ReturnsWithinOption;
        $returnsacceptedoption = $xml->Item->ReturnPolicy->ReturnsAcceptedOption;
        $product_condition = $xml->Item->ConditionDisplayName;
        $listing_status = $xml->Item->SellingStatus->ListingStatus;
        $active = 0;
        if($listing_status == 'Ended'){
            $active = 0;
        }elseif($listing_status == 'Active'){
            $active = 1;
        }
//
        //find-product-id
        $product_id = $e->product_soi($sku, $title, '', $description, '', '');
        //add-product-availability
        $availability_id = $e->availability_soi($product_id, $store_id);
        //find sku
        $sku_id = $e->sku_soi($sku);
        //add price
        $price_id = $e->price_soi($sku_id, $price, $store_id);
        //normalize condition
        $condition = $e->normal_condition($product_condition);
        //find condition id
        $condition_id = $e->condition_soi($condition);
        //add stock to sku
        $stock_id = $e->stock_soi($sku_id,$condition_id);
        $channel_array = array(
            'store_id' => $store_id,
            'stock_id' => $stock_id,
            'store_listing_id' => $item_id,
            'price' => $price,
            'url' => $url,
            'title' => $title,
            'description' => $description,
            'active' => $active,
            'country' => $country,
            'listing_duration' => $listing_duration,
            'listing_type' => $listing_type,
            'primary_category' => $primary_category,
            'category_name' => $category_name,
            'inventory_level' => $quantity,
            'isbn' => $isbn,
            'stock_photo_url' => $stock_photo,
            'global_shipping' => $global_shipping,
            'free_shipping' => $free_shipping,
            'shipping_cost' => $shipping_cost,
            'shipping_cost_additional' => $shipping_cost_additional,
            'shipping_type' => $shipping_type,
            'sku' => $sku,
            'photo_url' => $photo_url,
            'external_photo_url' => $external_photo_url,
            'refund_option' => $refund_option,
            'returns_within_option' => $returnswithinoption,
            'returns_accepted_option' => $returnsacceptedoption,
            'product_condition' => $product_condition
        );
        $listing_id = $e->listing_soi('listing_ebay', $store_id, $stock_id, $channel_array, 'true');
        return $listing_id;
    }

    public function update_ebay_inventory($stock_id, $quantity, $price, $e){
        $item_id = $e->get_listing_id($stock_id, 'listing_ebay');

        $requestName = 'ReviseInventoryStatus';
        $xml = [
            'InventoryStatus' => [
                'ItemID' => $item_id,
                'Quantity' => $quantity
            ]
        ];
        if(!empty($price)){
            $xml['InventoryStatus']['StartPrice'] = $price;
        }

        $response = $this->ebayCurl($requestName, $xml);
        return $response;
    }
    public function update_all_ebay_inventory($item_id, $price){ //$price
        $requestName = 'ReviseItem';

        //Use Tax Table
        $xml = [
            'Item' => [
                'ItemID' => $item_id,
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

        $response = $this->ebayCurl($requestName, $xml);
        return $response;
    }
    //<IncludeItemSpecifics>true</IncludeItemSpecifics>
    public function getSingleItem($item_id){
        $requestName = 'GetItem';

        $xml = [
            'DetailLevel' => 'ReturnAll',
            'ItemID' => $item_id
        ];

        $response = $this->ebayCurl($requestName, $xml);
        return $response;
    }
    public function add_ebay_inventory($ebay_category_id, $title, $description, $upc, $sku, $photo_url, $quantity, $price){
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
                    'PictureURL' => $photo_url,
                ],
                'PostalCode' => '83403',
                'PrimaryCategory' => [
                    'CategoryID' => $ebay_category_id,
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

        if(strlen($upc) > 12) {
            $xml['Item']['ProductListingDetails'] = [
                'EAN' => $upc,
                'UPC' => 'Does not apply',
            ];
        }else{
            $xml['Item']['ProductListingDetails'] = [
                'UPC' => $upc,
                'EAN' => 'Does not apply',
            ];
        }

        $response = $this->ebayCurl($requestName, $xml);
        return $response;
    }

    public function edit_gtin($item_id, $upc, $sku){
        $requestName = 'ReviseItem';

        $xml = [
            'Item' => [
                'ItemID' => $item_id,
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

        if(strlen($upc) > 12) {
            $xml['Item']['ProductListingDetails'][] = ['EAN' => $upc];
            $xml['Item']['ProductListingDetails'][] = ['UPC' => 'Does not apply'];
        }else{
            $xml['Item']['ProductListingDetails'][] = ['UPC' => $upc];
            $xml['Item']['ProductListingDetails'][] = ['EAN' => 'Does not apply'];
        }

        $response = $this->ebayCurl($requestName, $xml);
        return $response;
    }
    public function deleteItem($item_id){
        $requestName = 'EndItem';

        $xml = [
            'ItemID' => $item_id,
            'EndingReason' => 'NotAvailable'
        ];

        $response = $this->ebayCurl($requestName, $xml);
        return $response;
    }

    public function getItem($sku)
    {
        $requestName = 'GetItem';

        $xml = [
            'SKU' => $sku,
//            'ItemID' => $sku,
            'IncludeItemSpecifics' => 'true'
        ];

        $response = $this->ebayCurl($requestName, $xml);
        return $response;
    }

    public function findItemsAdvanced($keywords)
    {
        $requestName = __FUNCTION__;

        $xml = [
            'keywords' => $keywords,
            'outputSelector' => 'SellerInfo'
        ];

        $response = $this->ebayCurl($requestName, $xml, 'finding');
        return $response;
    }

    public function findCompletedItems($keywords)
    {
        $requestName = __FUNCTION__;
        $xml = [
            'keywords' => $keywords,
            'outputSelector' => 'SellerInfo',
            'outputSelector' => 'ListingInfo'
        ];
        $response = $this->ebayCurl($requestName, $xml, 'finding');
        return $response;
    }

    public function geteBayComparableItems($sku)
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
            'EndTimeFrom' => date('Y-m-d',strtotime('-120 days')),
            'EndTimeTo' => date('Y-m-d')
        ];

        $response = $this->ebayCurl($requestName, $xml);
        return $response;
    }

    public function sorteBaySearchResults($response)
    {
        foreach($response->searchResult->item as $item){
            $title = (string)$item->title;
            $url = (string)$item->viewItemURL;
            $seller = (string)$item->sellerInfo->sellerUserName;
            $sellerFeedback = (string)$item->sellerInfo->positiveFeedbackPercent;
            $sellerScore = (string)$item->sellerInfo->feedbackScore;
            $price = ((float)$item->sellingStatus->currentPrice*100)/100;
            $shippingCollected = ((float)$item->shippingInfo->shippingServiceCost*100)/100;
            $total = $price+$shippingCollected;
            $condition = (string)$item->condition->conditionDisplayName;

            $sellers[] = compact(
                "title", "url", "seller", "sellerFeedback", "sellerScore",
                "price", "shippingCollected", "total", "condition"
            );
        }

        $sellers = ecom::sortBy($sellers, 'total');

        return $sellers;
    }

    public function removeExtraSellerInfo($ebaySellers)
    {
        $x = 0;
        foreach($ebaySellers as $key => $seller){
            if($x > 6){
                unset($ebaySellers[$key]);
            }
            $sellerName = $seller['seller'];
            if($sellerName === 'mymusiclife-id' || $x > 0){
                $x++;
            }
        }
        return $ebaySellers;
    }

}