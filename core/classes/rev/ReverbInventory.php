<?php

namespace rev;

use ecommerce\Ecommerce;

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

    public function get_reverb_products(Ecommerce $ecommerce)
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
                    $product_id = $ecommerce->product_soi($sku, $name, '', $description, '', '');
                    //add-product-availability
                    $availability_id = $ecommerce->availability_soi($product_id, ReverbClient::getStoreID());
                    //find sku
                    $sku_id = $ecommerce->sku_soi($sku);
                    //add price
                    $price_id = $ecommerce->price_soi($sku_id, $price, ReverbClient::getStoreID());
                    //normalize condition
                    $condition = $ecommerce->normal_condition($product_condition);
                    //find condition id
                    $condition_id = $ecommerce->condition_soi($condition);
                    //add stock to sku
                    $stock_id = $ecommerce->stock_soi($sku_id, $condition_id);
                    $channel_array = [
                        'store_id' => ReverbClient::getStoreID(),
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
                    $listing_id = $ecommerce->listing_soi('listing_reverb', ReverbClient::getStoreID(), $stock_id, $channel_array, 'true');
                    echo $listing_id . '<br><br>';
                }
            }
        }
    }
}