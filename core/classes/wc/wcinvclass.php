<?php

namespace wcinv;

use wc\woocommerceclass;

class wcinvclass extends woocommerceclass
{
    public function getWCListing($woocommerce, $product_id)
    {
        $response = $woocommerce->get('products/' . $product_id);
        return $response;
    }

    public function getWCListings($woocommerce, $page = 1)
    {
        $filter = [
            'page' => $page,
            'per_page' => 25
        ];
        $response = $woocommerce->get('products', $filter);
        return $response;
    }

    public function updateListing($woocommerce, $product_id, $listing, $filter)
    {
        foreach ($filter as $key => $value) {
            if (array_key_exists($key, $listing['product'])) {
                echo "$key => $value\n";
                $listing['product'][$key] = $value;
            }
        }
        unset($listing['product']['type']);
//        if($listing['product']['type'] == "variation"){
//            $listing['product']['variations'][] = ["regular_price" => "14.95"];
//        }
        print_r($listing);
        $response = $woocommerce->put("products/$product_id", $listing);

        return $response;
    }

    public function getListing($product_id)
    {
        $url = 'https://chesbroretail.com/wp-json/wc/v1/products/' . $product_id;
        $response = $this->woocommerceCurl($url, 'GET');
        return ($response);
    }

    public function getListings($page = 1)
    {
        $url = 'https://chesbroretail.com/wp-json/wc/v1/products/?page=' . $page . '&per_page=25';
        $response = $this->woocommerceCurl($url, 'GET');
        return ($response);
    }

    public function get_wc_products($e)
    {
        for ($page = 11; $page < 20; $page++) {
            $request = $this->getListings($page);
            $listings = json_decode($request);
            $this->saveWCListing($listings, $e);
        }
    }

    public function saveWCListing($listings, $e, $variation = null)
    {
        foreach ($listings as $l) {
            if (!empty($l->variations)) {
                $this->saveWCListing($l->variations, $e, true);
            } else {
                $id = $l->id;
                $request = $this->getListing($id);
                $listing = json_decode($request);
                print_r($listing);
                $title = $listing->name;
                $sku = $listing->sku;
                $description = $listing->description;
                $price = $listing->price;
                $url = $listing->permalink;
                $shipping_class = $listing->shipping_class;
                $manage_stock = $listing->manage_stock;
                $stock_quantity = $listing->stock_quantity;
                $in_stock = $listing->in_stock;
                $weight = $listing->weight;
                $length = $listing->dimensions->length;
                $width = $listing->dimensions->width;
                $height = $listing->dimensions->height;
                $photo_url = $listing->images[0]->src;
                $product_condition = 'New';

                //find-product-id
                $product_id = $e->product_soi($sku, $title, '', $description, '', '');
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
                $stock_id = $e->stock_soi($sku_id, $condition_id);

                if ($variation) {
                    $variation = 1;
                }

                $channel_array = [
                    'store_id' => $this->store_id,
                    'stock_id' => $stock_id,
                    'store_listing_id' => $id,
                    'price' => $price,
                    'url' => $url,
                    'title' => $title,
                    'description' => $description,
                    'sku' => $sku,
                    'inventory_level' => $stock_quantity,
                    'variations' => $variation,
                    'manage_stock' => $manage_stock,
                    'in_stock' => $in_stock,
                    'photo_url' => $photo_url
                ];
                $listing_id = $e->listing_soi('listing_wc', $this->store_id, $stock_id, $channel_array, 'true');
                echo $listing_id . '<br><br>';
            }
        }
    }

    public function updateInventory($stock_id, $stock_qty, $price, $e, $woocommerce, $sku)
    {
        $store_listing_id = $e->get_listing_id($stock_id, 'listing_wc');
        $listing = $this->getWCListing($woocommerce, $store_listing_id);
//        $variation = $this->is_variation($sku);
//        echo 'Stock ID: ' . $stock_id . ', ID: ' . $store_listing_id . ', Price: ' . $price . ', Qty: ' . $stock_qty . ', Variation: ' . $variation . '<br>';
        $manage_stock = true;
//        if($variation){
//            $manage_stock = false;
//        }
//        $in_stock = 0;
//        if($stock_qty > 1){
//            $in_stock = 1;
//        }
        $filter = [
//            'manage_stock' => 1,
            'stock_quantity' => $stock_qty,
            'regular_price' => $price
        ];
//        if(!$variation){
//            $filter['managing_stock'] = $manage_stock;
//            $filter['stock_quantity'] = $stock_qty;
//        }
//        $results = $this->post_inventory_update($store_listing_id, $filter, $wc_consumer_key, $wc_secret_key);
        $results = $this->updateListing($woocommerce, $store_listing_id, $listing, $filter);
        return $results;
    }

    public function post_inventory_update($store_listing_id, $filter)
    {
        $post_string = json_encode($filter);
        $url = 'https://chesbroretail.com/wp-json/wc/v1/products/' . $store_listing_id;

        $response = $this->woocommerceCurl($url, 'PUT', $post_string);

        $product = json_decode($response);
        return $product;
    }

    public function create_listing()
    {
        $filter = [
            'name' => 'test product',
            'regular_price' => '2.99'
        ];
        $post_string = json_encode($filter);
        $url = 'https://chesbroretail.com/wp-json/wc/v1/products/';

        $response = $this->woocommerceCurl($url, 'POST', $post_string);

        $product = json_decode($response);
        return $product;
    }
}