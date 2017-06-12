<?php

namespace bc;

use Crypt;
use connect\DB;
use ecommerceclass\ecommerceclass as ecom;

class bigcommerceclass
{
    public $db;
    protected $bc_store_url;
    protected $bc_username;
    protected $bc_api_key;
    public $bc_store_id;


    public function __construct($bcclient){
        $this->db = $bcclient->getDBInstance();
        $this->bc_store_url = $bcclient->getBCStoreUrl();
        $this->bc_username = $bcclient->getBCUsername();
        $this->bc_api_key = $bcclient->getBCAPIKey();
        $this->bc_store_id = $bcclient->getBCStoreId();
    }
    public function configure($BC){
        $BC->configure(array(
            'store_url' => $this->bc_store_url,
            'username' => $this->bc_username,
            'api_key' => $this->bc_api_key
        ));
    }
    public function get_bc_app_info($user_id){
        $query = $this->db->prepare("SELECT store_id, store_url, bc.username, api_key FROM api_bigcommerce AS bc INNER JOIN store ON bc.store_id = store.id INNER JOIN account ON account.company_id = store.company_id INNER JOIN channel ON channel.id = store.channel_id WHERE account.id = :user_id AND channel.name = 'BigCommerce'");
        $query_params = array(
            ':user_id' => $user_id
        );
        $query->execute($query_params);
        return $query->fetch();
    }
    public function save_app_info($crypt, $store_id, $store_url, $store_username, $api_key){
        $query = $this->db->prepare("INSERT INTO api_bigcommerce (store_id, store_url, username, api_key) VALUES (:store_id, :store_url, :username, :api_key)");
        $query_params = array(
            ":store_id" => $store_id,
            ":store_url" => $store_url,
            ":username" => $crypt->encrypt($store_username),
            ":api_key" => $crypt->encrypt($api_key)
        );
        $query->execute($query_params);
        return true;
    }
    public function save_bc_id($store_id, $name, $store_listing_id, $sku, $condition, $description, $width, $weight, $height, $depth, $meta_keywords, $meta_description, $page_title, $price){
        try {
            $this->db->beginTransaction();

            $product_id = $this->add_product($name, $description, $meta_keywords, $meta_description, $page_title, $width, $weight, $height, $depth);
            //Add product details to Product table
            $this->add_product_availability($product_id, 3);
            $sku_id = $this->add_sku($product_id, $sku);
            $this->add_product_price($sku_id, $price, $store_id);
            $stock_id = $this->add_sku_to_stock($sku_id, $condition, 1);
            $listing_id = $this->add_stock_to_listing($store_id, $stock_id, $store_listing_id);

            $this->db->commit();
            return true;
        } catch (Exception $e){
            $this->db->rollback();
            die($e->getMessage());
        }
    }
    public function add_product($name, $description, $meta_keywords, $meta_description, $page_title, $width, $weight, $height, $depth){
        $query = $this->db->prepare("INSERT INTO product (name, description, meta_keywords, meta_description, page_title, width, weight, height, depth) VALUES (:name, :description, :meta_keywords, :meta_description, :page_title, :width, :weight, :height, :depth)");
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
        $query->execute($query_params);
        $product_id = $this->db->lastInsertId();

        return $product_id;
    }
    public function add_sku($product_id, $sku){
        //Add product_id and sku to Sku table
        $query = $this->db->prepare("INSERT INTO sku (product_id, sku) VALUES (:product_id, :sku)");
        $query_params = array(
            ":product_id" => $product_id,
            ":sku" => $sku
        );
        $query->execute($query_params);
        $sku_id = $this->db->lastInsertId();
        return $sku_id;
    }
    public function add_sku_to_stock($sku_id, $condition, $uofm = 1){
        //Add sku_id to Stock Table
        if($condition == "New"){
            $condition_id = 1;
        }elseif($condition == "Used"){
            $condition_id = 2;
        }elseif($condition == "Refurbished"){
            $condition_id = 5;
        }
        $query = $this->db->prepare("INSERT INTO stock (sku_id, condition_id, uofm_id) VALUES (:sku_id, :condition_id, :uofm_id)");
        $query_params = array(
            ":sku_id" => $sku_id,
            ":condition_id" => $condition_id,
            ":uofm_id" => 1
        );
        $query->execute($query_params);
        $stock_id = $this->db->lastInsertId();
        return $stock_id;
    }
    public function add_stock_to_listing($store_id, $stock_id, $store_listing_id){
        //Add stock_id to listing with additional info
        $query = $this->db->prepare("INSERT INTO listing_bigcommerce (store_id, stock_id, store_listing_id) VALUES (:store_id, :stock_id, :store_listing_id)");
        $query_params = array(
            ":store_id" => $store_id,
            ":stock_id" => $stock_id,
            ":store_listing_id" => $store_listing_id
        );
        $query->execute($query_params);
        $listing_id = $this->db->lastInsertId();
        return $listing_id;
    }
    public function add_product_availability($product_id, $store_id){
        $query = $this->db->prepare("INSERT INTO product_availability (product_id, store_id, is_available) VALUES (:product_id, :store_id, 1)");
        $query_params = array(
            ":product_id" => $product_id,
            ":store_id" => $store_id
        );
        $query->execute($query_params);
        return true;
    }
    public function add_product_price($sku_id, $price, $store_id){
        $query = $this->db->prepare("INSERT INTO product_price (sku_id, price, store_id) VALUES (:sku_id, :price, :store_id");
        $query_params = array(
            ":sku_id" => $sku_id,
            ":price" => $price,
            ":store_id" => $store_id
        );
        $query->execute($query_params);
        return true;
    }
    public function get_product_with_upc(){
        $query = $this->db->prepare("SELECT p.id, p.upc, sk.sku, lb.store_listing_id FROM product p JOIN sku sk ON sk.product_id = p.id JOIN listing_bigcommerce lb ON lb.sku = sk.sku");
        $query->execute();
        return $query->fetchAll();

    }
    public function update_upc($sku, $upc){
        $query = $this->db->prepare("UPDATE listing_bigcommerce SET upc = :upc WHERE sku = :sku");
        $query_params = [
            ':upc' => $upc,
            ':sku' => $sku
        ];
        $query->execute($query_params);
        return true;
    }
    public function get_bc_product_info($product_id){
        $api_url = 'https://mymusiclife.com/api/v2/products/' . $product_id . '.json';
        $response = $this->bigcommerceCurl($api_url, 'GET');

        $items = json_decode($response);
        return $items;
    }
    public function get_bc_product_upc($product_id){
        $items = $this->get_bc_product_info($product_id);
        $upc = $items->upc;
        return $upc;
    }
    public function get_category_count(){
        $api_url = 'https://mymusiclife.com/api/v2/categories/count';
        $response = $this->bigcommerceCurl($api_url, 'GET');

        $items = json_decode($response);
        return $items;
    }
    public function get_categories(){
        $fields = array(
            'limit' => 250
        );
        $post_string = json_encode($fields);
        $api_url = 'https://mymusiclife.com/api/v2/categories?limit=250&page=2';
        $response = $this->bigcommerceCurl($api_url, 'GET', $post_string);

        $items = json_decode($response);
        return $items;
    }
    public function get_bc_products_info($BC, $filter){
        $products = $BC::getProducts($filter);
        foreach($products as $p){
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
    public function get_bc_products($BC, $store_id, $e){
        $count = $BC::getProductsCount();
        $pages = $count/250;
        for($pn = 4; $pn < 101; $pn++){ //$pn = 36
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
                $product_id = $e->product_soi($sku, $name, '', $description, $upc, $weight);
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
                $listing_id = $e->listing_soi('listing_bigcommerce', $store_id, $stock_id, $channel_array, 'true');
                echo $listing_id . '<br>';
//                return $listing_id;
            }
        }
    }

    protected function setCurlOptions($url, $method, $post_string)
    {
        $request = curl_init($url);
        if($method === 'POST' || $method === 'PUT'){
            curl_setopt($request, CURLOPT_HTTPHEADER, array('Content-type: application/json', 'Content-Length: ' . strlen($post_string)));
        }elseif ($method === 'GET'){
            curl_setopt($request, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Length: 0'));
        }

        if($post_string){
            curl_setopt($request, CURLOPT_POSTFIELDS, $post_string);
        }
        curl_setopt($request, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($request, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($request, CURLOPT_USERPWD, $this->bc_username . ":" . $this->bc_api_key);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        return $request;
    }

    public function bigcommerceCurl($url, $method, $post_string = null)
    {
        $request = $this->setCurlOptions($url, $method, $post_string);
        return ecom::curlRequest($request);
    }
}