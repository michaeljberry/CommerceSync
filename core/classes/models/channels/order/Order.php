<?php

namespace models\channels\order;

use controllers\channels\ChannelHelperController as CHC;
use ecommerce\Ecommerce;
use models\channels\Buyer;
use models\channels\Tracking;
use models\ModelDB as MDB;

class Order
{

    private $storeID;
    private $buyer;
    private $orderNum;
    private $orderID;
    private $shippingCode;
    private $shippingPrice;
    private $tax;
    private $fee;
    private $channelOrderID;

    public function __construct($storeID, Buyer $buyer, $orderNum, $shippingCode, $shippingPrice, $tax, $fee = null, $channelOrderID = null)
    {
        $this->setStoreId($storeID);
        $this->setBuyer($buyer);
        $this->setOrderNum($orderNum);
        $this->setOrderId();
        $this->setShippingCode($shippingCode);
        $this->setShippingPrice($shippingPrice);
        $this->setTax($tax);
        $this->setFee($fee);
        $this->setChannelOrderId($channelOrderID);
    }

    private function setStoreId($storeID)
    {
        $this->storeID = $storeID;
    }

    private function setBuyer(Buyer $buyer)
    {
        $this->buyer = $buyer;
    }

    private function setOrderNum($orderNum)
    {
        $this->orderNum = $orderNum;
    }

    private function setOrderId()
    {
        $this->orderID = Order::getIdByOrder($this->orderNum);
    }

    private function setShippingCode($shippingCode)
    {
        $this->shippingCode = $shippingCode;
    }

    private function setShippingPrice($shippingPrice)
    {
        $this->shippingPrice = $shippingPrice;
    }

    private function setTax($tax)
    {
        $this->tax = $tax;
    }

    private function setFee($fee)
    {
        $this->fee = $fee;
    }

    private function setChannelOrderId($channelOrderId)
    {
        $this->channelOrderID = $channelOrderId;
    }

    public function getStoreId(): int
    {
        return $this->storeID;
    }

    public function getBuyer(): Buyer
    {
        return $this->buyer;
    }

    public function getOrderNum(): string
    {
        return $this->orderNum;
    }

    public function getOrderId(): int
    {
        return $this->orderID;
    }

    public function getShippingCode(): string
    {
        return $this->shippingCode;
    }

    public function getShippingPrice()
    {
        return $this->shippingPrice;
    }

    public function getTax()
    {
        return $this->tax;
    }

    public function getFee()
    {
        return $this->fee;
    }

    public function getChannelOrderId()
    {
        return $this->channelOrderID;
    }

    public static function cancel($orderNum)
    {
        $sql = "UPDATE sync.order 
                SET cancelled = 1 
                WHERE order_num = :order_num";
        $queryParams = [
            ':order_num' => $orderNum
        ];
        MDB::query($sql, $queryParams);
    }

    public static function getIdByOrder($orderNum)
    {
        $sql = "SELECT id 
                FROM sync.order 
                WHERE order_num = :order_num";
        $queryParams = [
            ':order_num' => $orderNum
        ];
        return MDB::query($sql, $queryParams, 'fetchColumn');
    }

    public static function getBySearch($array, $channel)
    {
        $resultArray = CHC::parseConditions($array);
        $condition = $resultArray[0];
        $queryParams = $resultArray[1];
        $queryParams['channel'] = $channel;
        $sql = "SELECT o.id, o.order_num, o.date, c.first_name, c.last_name, t.tracking_num, t.carrier 
                FROM sync.order o 
                JOIN customer c ON o.cust_id = c.id 
                LEFT JOIN tracking t ON o.id = t.order_id 
                JOIN order_sync os ON o.order_num = os.order_num 
                WHERE $condition 
                AND os.type = :channel";
        return MDB::query($sql, $queryParams, 'fetchAll');
    }

    public static function getByID($id)
    {
        $sql = "SELECT o.order_num, o.date, o.ship_method, o.shipping_amount, o.taxes, c.first_name, c.last_name, c.street_address, c.street_address2, city.name AS city, s.name, s.abbr as state_abbr, z.zip, t.tracking_num, t.carrier, os.processed as date_processed, os.success, os.type as channel, os.track_successful 
                FROM sync.order o 
                JOIN customer c ON o.cust_id = c.id 
                LEFT JOIN tracking t ON o.id = t.order_id 
                JOIN order_sync os ON o.order_num = os.order_num 
                JOIN state s ON c.state_id = s.id 
                JOIN city ON c.city_id = city.id 
                JOIN zip z ON c.zip_id = z.id 
                WHERE o.id = :order_id";
        $queryParams = [
            ':order_id' => $id
        ];
        return MDB::query($sql, $queryParams, 'fetch');
    }

    public static function markAsShipped($orderNum, $channel)
    {
        $response = Tracking::updateTrackingSuccessful($orderNum);
        if ($response) {
            echo "Tracking for $channel order $orderNum was updated!";
            return true;
        }
        return false;
    }

    public static function updateShippingAmount($orderNum, $shippingAmount)
    {
        $sql = "UPDATE sync.order 
                SET shipping_amount = :shipping_amount 
                WHERE order_num = :order_num";
        $queryParams = [
            ':shipping_amount' => $shippingAmount,
            ':order_num' => $orderNum
        ];
        return MDB::query($sql, $queryParams, 'boolean');
    }

    public static function getIdByStoreId($storeID, $orderNum)
    {
        $sql = "SELECT id 
                FROM sync.order 
                WHERE store_id = :store_id 
                AND order_num = :order_num";
        $queryParams = [
            ':store_id' => $storeID,
            ':order_num' => $orderNum
        ];
        return MDB::query($sql, $queryParams, 'fetchColumn');
    }

    public function save($storeID)
    {
        $orderID = Order::getIdByStoreId($storeID, $this->getOrderNum());
        if (empty($orderID)) {
            $sql = "INSERT INTO sync.order (store_id, cust_id, order_num, ship_method, shipping_amount, taxes, fee, channel_order_id) 
                    VALUES (:store_id, :cust_id, :order_num, :ship_method, :shipping_amount, :taxes, :fee, :channel_order_id)";
            $queryParams = [
                ":store_id" => $this->getStoreId(),
                ":cust_id" => $this->getBuyer()->getId(),
                ":order_num" => $this->getOrderNum(),
                ":ship_method" => $this->getShippingCode(),
                ":shipping_amount" => $this->getShippingPrice(),
                ":taxes" => $this->getTax(),
                ':fee' => $this->getFee(),
                ':channel_order_id' => $this->getChannelOrderId()
            ];
            $orderID = MDB::query($sql, $queryParams, 'id');
        }
        return $orderID;
    }

    public static function saveTax($orderID, $tax)
    {
        $sql = "UPDATE sync.order 
                SET taxes = :taxes 
                WHERE id = :id";
        $queryParams = [
            ":taxes" => $tax,
            ":id" => $orderID
        ];
        return MDB::query($sql, $queryParams, 'id');
    }

    public static function updateShippingAndTaxes($orderID, $shipping, $tax)
    {
        $sql = "UPDATE sync.order 
                SET shipping_amount = :shipping, taxes = :taxes 
                WHERE id = :id";
        $queryParams = [
            ':shipping' => $shipping,
            ':taxes' => $tax,
            ':id' => $orderID
        ];
        return MDB::query($sql, $queryParams, 'id');
    }

    public static function getUploadedVaiOrder($orderNum)
    {
        $sql = "SELECT * 
                FROM order_sync 
                WHERE order_num = :order_num 
                AND success = 1";
        $queryParams = [
            ':order_num' => $orderNum
        ];
        return MDB::query($sql, $queryParams, 'rowCount');
    }

    public static function get($orderNum)
    {
        $number = Order::getUploadedVaiOrder($orderNum);

        if ($number > 0) {
            Ecommerce::dd("Found in database");
            return true;
        }
        return false;
    }

    public static function saveToSync($orderNum, $success = 1, $channel = 'Amazon')
    {
        $sql = "INSERT INTO order_sync (order_num, success, type) 
                VALUES (:order_num, :success, :channel)";
        $queryParams = [
            ":order_num" => $orderNum,
            ":success" => $success,
            ":channel" => $channel
        ];
        return MDB::query($sql, $queryParams, 'boolean');
    }
}