<?php

namespace bcinv;

use bc\bigcommerceclass;
use ecommerceclass\ecommerceclass as ecom;

class bcinvclass extends bigcommerceclass
{
    public function count_inventory_for_bc(){
        $query = $this->db->prepare("SELECT COUNT(Distinct l.stock_id) AS idcount FROM listing_bigcommerce l JOIN stock st ON st.id = l.stock_id JOIN store ON store.id = l.store_id JOIN product_availability pa ON pa.store_id = store.id WHERE store.id = '3' AND pa.is_available = '1'");
        $query->execute();
        return $query->fetchColumn();
    }
    public function pull_inventory_for_bc_update($offset, $limit, $store_id){
        $query = $this->db->prepare("SELECT l.store_listing_id, pp.price, st.stock_qty FROM listing_bigcommerce l JOIN stock st ON st.id = l.stock_id JOIN store ON store.id = l.store_id JOIN product_availability pa ON pa.store_id = store.id JOIN product_price pp ON pp.sku_id = st.sku_id WHERE store.id = :store_id AND pa.is_available = '1' LIMIT $offset, $limit");
        $query_params = array(
            ':store_id' => $store_id
        );
        $query->execute($query_params);
        return $query->fetchAll();
    }
    public function update_bc_inventory($stock_id, $stock_qty, $price, $e){ //$BC
        $store_listing_id = $e->get_listing_id($stock_id, 'listing_bigcommerce');
        echo 'Stock ID: ' .$stock_id . ', ID: ' . $store_listing_id . ', Price: ' . $price . ', Qty: ' . $stock_qty . '<br>';
        $filter = [
//            "inventory_level" => $stock_qty
        ];
        if(!empty($price)){
            $filter['price'] = $price;
        }
        $results = $this->post_inventory_update($store_listing_id, $filter);
        return $results;
    }
    public function updateInventory($id, $price){
        echo 'ID: ' . $id . ', Price: ' . $price . '<br>';
        $filter = [
            "price" => $price
        ];
        $results = $this->post_inventory_update($id, $filter);
        return $results;
    }
    public function update_bc_upc($store_listing_id, $upc){
        echo 'ID: ' . $store_listing_id . ', UPC: ' . $upc . '<br>';
        $filter = [
            "upc" => $upc
        ];
        $results = $this->post_inventory_update($store_listing_id, $filter);
        return $results;
    }
    public function add_item($title, $category, $price, $weight, $description, $sku,  $upc, $BC){
        $filter = [
            'name' => "$title",
            'type' => 'physical',
            'categories' => $category,
            'price' => "$price",
//            'availability' => 'available',
            'weight' => $weight,
            'description' => "$description",
            'sku' => "$sku",
            'is_visible' => true,
            'inventory_tracking' => 'simple',
            'upc' => $upc
        ];
        return $filter;
        echo $BC::createProduct($filter);
//        $post_string = json_encode($filter);
//        $api_url = 'https://mymusiclife.com/api/v2/products/';
//        $response = $this->bigcommerceCurl($api_url, 'POST', $post_string);
//
//        $product = json_decode($response);
//        curl_close($response);
//        print_r($items
//        return $product;
    }
    public function get_product_id_by_sku($sku, $page){
        $api_url = 'https://mymusiclife.com/api/v2/products.json?limit=250&page=' . $page; //
        $response = $this->bigcommerceCurl($api_url, 'GET');

        $product = json_decode($response);

        $present = 0;
        $p_id = '';
        if(!is_array($product)){
            return false;
        }else {
            foreach ($product as $p) {
                if(!isset($p->sku)){
                    echo "SKU is not set on page: $page<br>";
                    print_r($p);
                }
                $p_sku = $p->sku;
                if ($p_sku == $sku) {
                    $present = 1;
                    $p_id = $p->id;
                    break;
                }
            }
            if(empty($present)){
                $page++;
                $info2 = $this->get_product_id_by_sku($sku, $page);
                $page = $info2['page'];
                $p_id = $info2['p_id'];
            }
            $info = [
                'page' => $page,
                'p_id' => $p_id
            ];
            return $info;
        }
    }

    public function add_product_image($store_listing_id, $image_url){
        $filter = array(
            'image_file' => $image_url,
            'is_thumbnail' => true
        );
        $post_string = json_encode($filter);
        $api_url = 'https://mymusiclife.com/api/v2/products/' . $store_listing_id . '/images';
        $response = $this->bigcommerceCurl($api_url, 'POST', $post_string);

        $product = json_decode($response);
        return $product;
    }

    public function post_inventory_update($store_listing_id, $filter){
        $post_string = json_encode($filter);
        $api_url = 'https://mymusiclife.com/api/v2/products/' . $store_listing_id;
        $response = $this->bigcommerceCurl($api_url, 'PUT', $post_string);

        $product = json_decode($response);
        return $product;
    }

    public function update_price()
    {

    }

    public function get_product_images($product_id){
        $api_url = 'https://mymusiclife.com/api/v2/products/' . $product_id . '/images'; //
        $response = $this->bigcommerceCurl($api_url, 'GET');

        $product_images = json_decode($response);
        return $product_images;
    }
}