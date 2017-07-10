<?php

namespace models\channels;

use ecommerce\Ecommerce;
use models\ModelDB as MDB;
use PDO;

class ProductPrice
{

    public static function getBySKUId($skuID, $storeID)
    {
        $sql = "SELECT id 
                FROM product_price 
                WHERE sku_id = :sku_id 
                AND store_id = :store_id";
        $query_params = [
            ':sku_id' => $skuID,
            ':store_id' => $storeID
        ];
        return MDB::query($sql, $query_params, 'fetchColumn');
    }

    public static function saveBySKUId($skuID, $price, $storeID)
    {
        $sql = "INSERT INTO product_price (sku_id, price, store_id) 
                VALUES (:sku_id, :price, :store_id)";
        $query_params = [
            ':sku_id' => $skuID,
            ':price' => $price,
            ':store_id' => $storeID
        ];
        return MDB::query($sql, $query_params, 'id');
    }

    public static function searchOrInsert($skuID, $storeID, $price = null)
    {
        $product_price_id = ProductPrice::getBySKUId($skuID, $storeID);
        if (empty($product_price_id)) {
            $product_price_id = ProductPrice::saveBySKUId($skuID, $price, $storeID);
        }
        return $product_price_id;
    }

    public static function updatePrices($skuID, $msrp, $pl1, $map, $pl10, $cost)
    {
        $sql = "INSERT INTO product_cost (sku_id, msrp, pl10, map, pl1, cost) 
                VALUES (:sku_id, :msrp, :pl10, :map, :pl1, :cost) 
                ON DUPLICATE KEY UPDATE msrp = :msrp2, pl10 = :pl102, map = :map2, pl1 = :pl12, cost = :cost2";
        $query_params = [
            ':sku_id' => $skuID,
            ':msrp' => Ecommerce::toCents($msrp),
            ':pl1' => Ecommerce::toCents($pl1),
            ':map' => Ecommerce::toCents($map),
            ':pl10' => Ecommerce::toCents($pl10),
            ':cost' => Ecommerce::toCents($cost),
            ':msrp2' => Ecommerce::toCents($msrp),
            ':pl12' => Ecommerce::toCents($pl1),
            ':map2' => Ecommerce::toCents($map),
            ':pl102' => Ecommerce::toCents($pl10),
            ':cost2' => Ecommerce::toCents($cost)
        ];
        return MDB::query($sql, $query_params, 'boolean');
    }

    public static function getUpsideDownCost()
    {
        $sql = "SELECT sk.sku, (pc.pl10/100) as pl10, (pc.pl1/100) as pl1, (pc.cost/100) as cost 
                FROM sku sk 
                LEFT JOIN product_cost pc ON sk.id = pc.sku_id 
                WHERE pc.pl10 < pc.pl1";
        return MDB::query($sql, [], 'fetchAll');
    }

    public static function get()
    {
        $sql = "SELECT sk.sku, (pc.msrp/100) as msrp, (pc.pl10/100) as pl10, (pc.map/100) as map, (pc.pl1/100) as pl1, (pc.cost/100) as cost 
                FROM product_cost pc 
                LEFT OUTER JOIN sku sk ON sk.id = pc.sku_id";
        return MDB::query($sql, [], 'fetchAll', PDO::FETCH_GROUP | PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);
    }

    public static function getUpdated($hours)
    {
        $sql = "SELECT sk.sku, (pc.msrp/100) as msrp, (pc.pl10/100) as pl10, (pc.map/100) as map, (pc.pl1/100) as pl1, (pc.cost/100) as cost 
                FROM product_cost pc 
                LEFT OUTER JOIN sku sk ON sk.id = pc.sku_id 
                WHERE pc.last_edited >= DATE_SUB(NOW(), INTERVAL $hours HOUR)";
        return MDB::query($sql, [], 'fetchAll', PDO::FETCH_GROUP | PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);
    }
}