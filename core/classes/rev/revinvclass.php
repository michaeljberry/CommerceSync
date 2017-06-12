<?php

namespace rev;

use rev\reverbclass;
use ecommerceclass\ecommerceclass as ecom;

class revinvclass extends reverbclass
{
    public function get_reverb_listings($page){
        $url = 'https://reverb.com/api/my/listings.json?page=' . $page;
        $post_string = '';
        $response = $this->reverbCurl(
            $url,
            'GET',
            $post_string
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
        $response = $this->reverbCurl(
            $url,
            'PUT',
            json_encode($postString)
        );
        return $response;
    }

    public function get_reverb_products($e){
        for($page = 1; $page < 370; $page++) { //340
            $request = $this->get_reverb_listings($page);
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
                    $product_id = $e->product_soi($sku, $name, '', $description, '', '');
                    //add-product-availability
                    $availability_id = $e->availability_soi($product_id, $this->store_id);
                    //find sku
                    $sku_id = $e->sku_soi($sku);
                    //add price
                    $price_id = $e->price_soi($sku_id, $price, $this->store_id);
                    //normalize condition
                    $condition = $e->normal_condition($product_condition);
                    //find condition id
                    $condition_id = $e->condition_soi($condition);
                    //add stock to sku
                    $stock_id = $e->stock_soi($sku_id,$condition_id);
                    $channel_array = [
                        'store_id' => $this->store_id,
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
                    $listing_id = $e->listing_soi('listing_reverb', $this->store_id, $stock_id, $channel_array, 'true');
                    echo $listing_id . '<br><br>';
                }
            }
        }
    }
}