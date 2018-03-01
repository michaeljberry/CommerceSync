<?php

namespace Reverb;

use models\channels\{Listing, SKU, Stock};
use models\channels\product\{Product, ProductAvailability, ProductPrice};

class ReverbInventory extends Reverb
{
    public static function getReverbListings($page)
    {
        $url = 'https://reverb.com/api/my/listings.json?page=' . $page;
        $response = ReverbClient::reverbCurl(
            $url,
            'GET'
        );
        return $response;
    }

    public function updateListing($id, $price)
    {
        $url = "https://api.reverb.com/api/listings/$id";
        $postString = [
            'price' => [
                'amount' => $price
            ]
        ];
        $response = ReverbClient::reverbCurl(
            $url,
            'PUT',
            json_encode($postString)
        );
        return $response;
    }

    public function get_reverb_products()
    {
        for ($page = 1; $page < 370; $page++) { //340
            $request = ReverbInventory::getReverbListings($page);
            $listings = substr($request, strpos($request, '"listings":'), -1);
            $listings = '{' . $listings . '}';
            $listings = json_decode($listings);
            $x = 0;
            foreach ($listings as $listing) {
                foreach ($listing as $r) {
                    $make = $r->make;
                    $store_listing_id = $r->sku;
                    $sku = $r->sku;
                    $model = $r->model;
                    $finish = $r->finish;
                    $name = $r->title;
                    $created_at = $r->created_at;
                    $description = $r->description;
                    $product_condition = $r->condition;
                    $price = $r->price->amount;
                    $offers_enabled = $r->offers_enabled;
                    $inventory = $r->inventory;
                    $photo_url = $r->_links->photo->href;
                    $url = $r->_links->web->href;
                    $shipping_cost = $r->shipping->us_rate->amount;

                    //find-product-id
                    $product_id = Product::searchOrInsert($sku, $name, '', $description, '', '');
                    //add-product-availability
                    $availability_id = ProductAvailability::searchOrInsert($product_id,
                        ReverbClient::getStoreId());
                    //find sku
                    $sku_id = SKU::searchOrInsert($sku);
                    //add price
                    $price_id = ProductPrice::searchOrInsert($sku_id, $price, ReverbClient::getStoreId());
                    //normalize condition
                    $condition = ConditionController::normalCondition($product_condition);
                    //find condition id
                    $condition_id = Condition::searchOrInsert($condition);
                    //add stock to sku
                    $stock_id = Stock::searchOrInsert($sku_id, $condition_id);
                    $channel_array = [
                        'store_id' => ReverbClient::getStoreId(),
                        'stock_id' => $stock_id,
                        'store_listing_id' => $store_listing_id,
                        'price' => $price,
                        'url' => $url,
                        'title' => $name,
                        'description' => $description,
                        'sku' => $sku,
                        'make' => $make,
                        'model' => $model,
                        'finish' => $finish,
                        'created_at' => $created_at,
                        'product_condition' => $product_condition,
                        'offers_enabled' => $offers_enabled,
                        'inventory_level' => $inventory,
                        'photo_url' => $photo_url,
                        'shipping_cost' => $shipping_cost
                    ];
//                    print_r($channel_array);
                    $listing_id = Listing::searchOrInsert('listing_reverb', ReverbClient::getStoreId(), $stock_id,
                        $channel_array, 'true');
                    echo $listing_id . '<br><br>';
                }
            }
        }
    }
}
