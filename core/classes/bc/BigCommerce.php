<?php

namespace bc;

use ecommerce\Ecommerce;
use models\channels\Listing;
use models\channels\Product;
use models\ModelDB as MDB;

class BigCommerce
{
    public function __construct($BC)
    {
        $this->configure($BC);
    }

    public function configure($BC)
    {
        $BC->configure([
            'store_url' => BigCommerceClient::getStoreUrl(),
            'username' => BigCommerceClient::getUsername(),
            'api_key' => BigCommerceClient::getAPIKey()
        ]);
    }

    public function save_app_info($crypt, $store_id, $store_url, $store_username, $api_key)
    {
        $sql = "INSERT INTO api_bigcommerce (store_id, store_url, username, api_key) 
                VALUES (:store_id, :store_url, :username, :api_key)";
        $query_params = array(
            ":store_id" => $store_id,
            ":store_url" => $store_url,
            ":username" => $crypt->encrypt($store_username),
            ":api_key" => $crypt->encrypt($api_key)
        );
        return MDB::query($sql, $query_params);

    }

    public function add_product($name, $description, $meta_keywords, $meta_description, $page_title, $width, $weight, $height, $depth)
    {
        $sql = "INSERT INTO product (name, description, meta_keywords, meta_description, page_title, width, weight, height, depth) 
                VALUES (:name, :description, :meta_keywords, :meta_description, :page_title, :width, :weight, :height, :depth)";
        $query_params = array(
            ":name" => $name,
            ":description" => $description,
            ":meta_keywords" => $meta_keywords,
            ":meta_description" => $meta_description,
            ":page_title" => $page_title,
            ":width" => $width,
            ":weight" => $weight,
            ":height" => $height,
            ":depth" => $depth
        );
        return MDB::query($sql, $query_params, 'id');

    }

    public function add_sku($product_id, $sku)
    {
        $sql = "INSERT INTO sku (product_id, sku) 
                VALUES (:product_id, :sku)";
        $query_params = array(
            ":product_id" => $product_id,
            ":sku" => $sku
        );
        return MDB::query($sql, $query_params, 'id');

    }

    public function add_sku_to_stock($sku_id, $condition, $uofm = 1)
    {
        //Add sku_id to Stock Table
        if ($condition == "New") {
            $condition_id = 1;
        } elseif ($condition == "Used") {
            $condition_id = 2;
        } elseif ($condition == "Refurbished") {
            $condition_id = 5;
        }
        $sql = "INSERT INTO stock (sku_id, condition_id, uofm_id) 
                VALUES (:sku_id, :condition_id, :uofm_id)";
        $query_params = array(
            ":sku_id" => $sku_id,
            ":condition_id" => $condition_id,
            ":uofm_id" => 1
        );
        return MDB::query($sql, $query_params, 'id');
    }

    public function add_stock_to_listing($store_id, $stock_id, $store_listing_id)
    {
        $sql = "INSERT INTO listing_bigcommerce (store_id, stock_id, store_listing_id) 
                VALUES (:store_id, :stock_id, :store_listing_id)";
        $query_params = array(
            ":store_id" => $store_id,
            ":stock_id" => $stock_id,
            ":store_listing_id" => $store_listing_id
        );
        return MDB::query($sql, $query_params, 'id');
    }

    public function add_product_availability($product_id, $store_id)
    {
        $sql = "INSERT INTO product_availability (product_id, store_id, is_available) 
                VALUES (:product_id, :store_id, 1)";
        $query_params = array(
            ":product_id" => $product_id,
            ":store_id" => $store_id
        );
        MDB::query($sql, $query_params);
    }

    public function add_product_price($sku_id, $price, $store_id)
    {
        $sql = "INSERT INTO product_price (sku_id, price, store_id) 
                VALUES (:sku_id, :price, :store_id";
        $query_params = array(
            ":sku_id" => $sku_id,
            ":price" => $price,
            ":store_id" => $store_id
        );
        MDB::query($sql, $query_params);
    }

    public function get_product_with_upc()
    {
        $sql = "SELECT p.id, p.upc, sk.sku, lb.store_listing_id 
                FROM product p 
                JOIN sku sk ON sk.product_id = p.id 
                JOIN listing_bigcommerce lb ON lb.sku = sk.sku";
        return MDB::query($sql, [], 'fetchAll');
    }

    public function update_upc($sku, $upc)
    {
        $sql = "UPDATE listing_bigcommerce 
                SET upc = :upc 
                WHERE sku = :sku";
        $query_params = [
            ':upc' => $upc,
            ':sku' => $sku
        ];
        MDB::query($sql, $query_params);

    }

    public function get_bc_product_info($product_id)
    {
        $api_url = 'https://mymusiclife.com/api/v2/products/' . $product_id . '.json';
        $response = BigCommerceClient::bigcommerceCurl($api_url, 'GET');

        $items = json_decode($response);
        return $items;
    }

    public function get_bc_product_upc($product_id)
    {
        $items = $this->get_bc_product_info($product_id);
        $upc = $items->upc;
        return $upc;
    }

    public function get_category_count()
    {
        $api_url = 'https://mymusiclife.com/api/v2/categories/count';
        $response = BigCommerceClient::bigcommerceCurl($api_url, 'GET');

        $items = json_decode($response);
        return $items;
    }

    public function get_categories()
    {
        $fields = array(
            'limit' => 250
        );
        $post_string = json_encode($fields);
        $api_url = 'https://mymusiclife.com/api/v2/categories?limit=250&page=2';
        $response = BigCommerceClient::bigcommerceCurl($api_url, 'GET', $post_string);

        $items = json_decode($response);
        return $items;
    }

    public function get_bc_products_info($BC, $filter)
    {
        $products = $BC::getProducts($filter);
        foreach ($products as $p) {
//            print_r($p);
            echo 'Name: ' . $p->name . "  -  ";
            echo 'Price: ' . $p->price . "<br>";
            echo 'SKU: ' . $p->sku . "<br>";
            echo 'ID: ' . $p->id . "<br>";
            echo 'Description: ' . $p->description . "<br>";
            echo 'Stock: ' . $p->inventory_level . "<br>";
            echo 'Type: ' . $p->type . "<br>";
            echo 'Search Keywords: ' . $p->search_keywords . "<br>";
            echo 'Keyword Filter: ' . $p->keyword_filter . "<br>";
            echo 'Availability: ' . $p->availability_description . "<br>";
            echo 'Cost: ' . $p->cost_price . "<br>";
            echo 'Retail Price: ' . $p->retail_price . "<br>";
            echo 'Sale Price: ' . $p->sale_price . "<br>";
            echo 'Calculated Price: ' . $p->calculated_price . "<br>";
            echo 'Sort Order: ' . $p->sort_order . "<br>";
            echo 'Visible: ' . $p->is_visible . "<br>";
            echo 'Is Featured: ' . $p->is_featured . "<br>";
            echo 'Related Products: ' . $p->related_products . "<br>";
            echo 'Inventory Warning Level: ' . $p->inventory_warning_level . "<br>";
            echo 'Warranty: ' . $p->warranty . "<br>";
            echo 'Weight: ' . $p->weight . "<br>";
            echo 'Width: ' . $p->width . "<br>";
            echo 'Height: ' . $p->height . "<br>";
            echo 'Depth: ' . $p->depth . "<br>";
            echo 'Fixed Shipping Price: ' . $p->fixed_cost_shipping_price . "<br>";
            echo 'Free Shipping: ' . $p->is_free_shipping . "<br>";
            echo 'Inventory Tracking: ' . $p->inventory_tracking . "<br>";
            echo 'Rating Total: ' . $p->rating_total . "<br>";
            echo 'Rating Count: ' . $p->rating_count . "<br>";
            echo 'Total Sold: ' . $p->total_sold . "<br>";
            echo 'Date Created: ' . $p->date_created . "<br>";
            echo 'Brand ID: ' . $p->brand_id . "<br>";
            echo 'View Count: ' . $p->view_count . "<br>";
            echo 'Page Title: ' . $p->page_title . "<br>";
            echo 'Meta Keywords: ' . $p->meta_keywords . "<br>";
            echo 'Meta Description: ' . $p->meta_description . "<br>";
            echo 'Layout File: ' . $p->layout_file . "<br>"; //BC's template file
            echo 'Price hidden: ' . $p->is_price_hidden . "<br>";
            echo 'Hidden Label: ' . $p->is_hidden_label . "<br>";
            echo 'Date Modified: ' . $p->date_modified . "<br>";
            echo 'Event Date Field: ' . $p->event_date_field_name . "<br>";
            echo 'Event Date Type: ' . $p->event_date_type . "<br>";
            echo 'Event Date Start: ' . $p->event_date_start . "<br>";
            echo 'Event Date End: ' . $p->event_date_end . "<br>";
            echo 'MYOB Asset: ' . $p->myob_asset_account . "<br>";
            echo 'MYOB Income: ' . $p->myob_income_account . "<br>";
            echo 'MYOB Expense: ' . $p->myob_expense_account . "<br>";
            echo 'Peachtree GL: ' . $p->peachtree_gl_account . "<br>";
            echo 'Condition: ' . $p->condition . "<br>";
            echo 'Condition Shown: ' . $p->is_condition_shown . "<br>";
            echo 'Preorder Release Date: ' . $p->preorder_release_date . "<br>";
            echo 'Preorder Only: ' . $p->is_preorder_only . "<br>";
            echo 'Preorder Message: ' . $p->preorder_message . "<br>";
            echo 'Order quantity Min.: ' . $p->order_quantity_minimum . "<br>";
            echo 'Order quantity Max: ' . $p->order_quantitiy_maximum . "<br>";
            echo 'Open Graph Type: ' . $p->open_graph_type . "<br>";
            echo 'Open Graph Title: ' . $p->open_graph_title . "<br>";
            echo 'Open Graph Description: ' . $p->open_graph_description . "<br>";
            echo 'Open Graph Thumbnail: ' . $p->is_open_graph_thumbnail . "<br>";
            echo 'UPC: ' . $p->upc . "<br>";
            echo 'Avalara Tax Code: ' . $p->avalara_product_tax_code . "<br>";
            echo 'Date Last Imported: ' . $p->date_last_imported . "<br>";
            echo 'Option Set ID: ' . $p->option_set_id . "<br>";
            echo 'Tax Class: ' . $p->tax_class_id . "<br>"; // 0 is Idaho
            echo 'Option Set Display: ' . $p->option_set_display . "<br>";
            echo 'BIN picking Num: ' . $p->bin_picking_number . "<br>";
            echo 'Custom URL: ' . $p->custom_url . "<br>";
            echo 'Availability: ' . $p->availability . "<br>";
            echo 'Categories: <br />';
            print_r($p->categories); //BC's category array
            echo "<br>";
            echo 'Primary Image: <br />';
            print_r($p->primary_image); //Object
            echo "<br>";
            echo 'Image: ' . $p->primary_image->standard_url;
            //id
            //zoome_url
            //thumbnail_url
            //standard_url
            //tiny_url

            echo "<br /><br />";
        }
    }

    public function get_bc_products($BC, Ecommerce $ecommerce)
    {
        $count = $BC::getProductsCount();
        $pages = $count / 250;
        for ($pn = 4; $pn < 101; $pn++) { //$pn = 36
            $filter = array(
                "page" => $pn,
                "limit" => 250 //250
            );
            $products = $BC::getProducts($filter);
            foreach ($products as $b) {
                $name = $b->name;
                $store_listing_id = $b->id;
                $sku = $b->sku;
                $price = $b->price;
                $product_condition = $b->condition;
                $description = $b->description;
                $inventory_level = $b->inventory_level;
                $type = $b->type;
                $search_keywords = $b->search_keywords;
                $keyword_filter = $b->keyword_filter;
                $cost_price = $b->cost_price;
                $retail_price = $b->retail_price;
                $sale_price = $b->sale_price;
                $calculated = $b->calculated_price;
                $sort_order = $b->sort_order;
                $visible = $b->is_visible;
                $featured = $b->is_featured;
                $related_products = $b->related_products;
                $inventory_warning_level = $b->inventory_warning_level;
                $warranty = $b->warranty;
                $width = $b->width;
                $weight = $b->weight;
                $height = $b->height;
                $depth = $b->depth;
                $meta_keywords = $b->meta_keywords;
                $meta_description = $b->meta_description;
                $page_title = $b->page_title;
                $url = 'https://mymusiclife.com' . $b->custom_url;
                $fixed_cost_shpping_price = $b->fixed_cost_shipping_price;
                $free_shipping = $b->is_free_shipping;
                $inventory_tracking = $b->inventory_tracking;
                $rating_total = $b->rating_total;
                $rating_count = $b->rating_count;
                $total_sold = $b->total_sold;
                $date_created = $b->date_created;
                $brand_id = $b->brand_id;
                $view_count = $b->view_count;
                $layout_file = $b->layout_file;
                $price_hidden = $b->is_price_hidden;
                $hidden_label = $b->is_hidden_label;
                $date_modified = $b->date_modified;
                $condition_shown = $b->is_condition_shown;
                $order_quantity_minimum = $b->order_quantity_minimum;
                $order_quantity_maximum = $b->order_quantity_maximum;
                $upc = $b->upc;
                $date_last_imported = $b->date_last_imported;
                $option_set_id = $b->option_set_id;
                $tax_class_id = $b->tax_class_id;
                $option_set_display = $b->option_set_display;
                $bin_picking_number = $b->bin_picking_number;
                $custom_url = $b->custom_url;
                $availability = $b->availability;
                $photo_url = $b->primary_image->standard_url;

                //find-product-id
                $product_id = Product::searchOrInsert($sku, $name, '', $description, $upc, $weight);
                //add-product-availability
                $availability_id = MDB::availability_soi($product_id, BigCommerceClient::getStoreID());
                //find sku
                $sku_id = $ecommerce->sku_soi($sku);
                //add price
                $price_id = $ecommerce->price_soi($sku_id, $price, BigCommerceClient::getStoreID());
                //normalize condition
                $condition = $ecommerce->normal_condition($product_condition);
                //find condition id
                $condition_id = $ecommerce->condition_soi($condition);
                //add stock to sku
                $stock_id = $ecommerce->stock_soi($sku_id, $condition_id);
                $channel_array = array(
                    'store_id' => BigCommerceClient::getStoreID(),
                    'stock_id' => $stock_id,
                    'store_listing_id' => $store_listing_id,
                    'url' => $url,
                    'title' => $name,
                    'description' => $description,
                    'sku' => $sku,
                    'price' => $price,
                    'product_condition' => $product_condition,
                    'inventory_level' => $inventory_level,
                    'type' => $type,
                    'search_keywords' => $search_keywords,
                    'keyword_filter' => $keyword_filter,
                    'cost_price' => $cost_price,
                    'retail_price' => $retail_price,
                    'sale_price' => $sale_price,
                    'calculated_price' => $calculated,
                    'sort_order' => $sort_order,
                    'visible' => $visible,
                    'featured' => $featured,
                    'related_products' => $related_products,
                    'inventory_warning_level' => $inventory_warning_level,
                    'warranty' => $warranty,
                    'width' => $width,
                    'weight' => $weight,
                    'height' => $height,
                    'depth' => $depth,
                    'meta_keywords' => $meta_keywords,
                    'meta_description' => $meta_description,
                    'page_title' => $page_title,
                    'fixed_cost_shipping_price' => $fixed_cost_shpping_price,
                    'free_shipping' => $free_shipping,
                    'inventory_tracking' => $inventory_tracking,
                    'rating_total' => $rating_total,
                    'rating_count' => $rating_count,
                    'total_sold' => $total_sold,
                    'date_created' => $date_created,
                    'brand_id' => $brand_id,
                    'view_count' => $view_count,
                    'layout_file' => $layout_file,
                    'price_hidden' => $price_hidden,
                    'hidden_label' => $hidden_label,
                    'date_modified' => $date_modified,
                    'condition_shown' => $condition_shown,
                    'order_quantity_minimum' => $order_quantity_minimum,
                    'order_quantity_maximum' => $order_quantity_maximum,
                    'upc' => $upc,
                    'date_last_imported' => $date_last_imported,
                    'option_set_id' => $option_set_id,
                    'tax_class_id' => $tax_class_id,
                    'option_set_display' => $option_set_display,
                    'bin_picking_number' => $bin_picking_number,
                    'custom_url' => $custom_url,
                    'availability' => $availability,
                    'photo_url' => $photo_url
                );
                //add stock to listing
                $listing_id = Listing::searchOrInsert('listing_bigcommerce', BigCommerceClient::getStoreID(), $stock_id,
                    $channel_array, 'true');
                echo $listing_id . '<br>';
//                return $listing_id;
            }
        }
    }


}