<?php

namespace models\channels\order;

use controllers\channels\order\OrderItemXMLController;
use ecommerce\Ecommerce;
use models\channels\SKU;
use models\ModelDB as MDB;

class OrderItem
{
    private $sku;
    private $title;
    private $quantity;
    private $price;
    private $poNumber;
    private $upc;
    private $channelOrderItemID;
    private $itemXML;

    public function __construct($sku, $title, $quantity, $price, $upc, $poNumber, $channelOrderItemID = null)
    {
        $this->setSku($sku);
        $this->setTitle($title);
        $this->setQuantity($quantity);
        $this->setPrice($price);
        $this->setPoNumber($poNumber);
        $this->setUpc($upc);
        $this->setChannelOrderItemId($channelOrderItemID);
        $this->setItemXml();
    }

    private function setSku($sku)
    {
        $this->sku = new SKU($sku);
    }

    private function setTitle($title)
    {
        $this->title = $title;
    }

    private function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    private function setPrice($price)
    {
        $this->price = $price;
    }

    private function setPoNumber($poNumber)
    {
        $this->poNumber = $poNumber;
    }

    private function setUpc($upc)
    {
        $this->upc = $upc;
    }

    private function setChannelOrderItemId($channelOrderItemID)
    {
        $this->channelOrderItemID = $channelOrderItemID;
    }

    private function setItemXml()
    {
        $this->itemXML = OrderItemXMLController::create($this);
    }

    public function getSku(): SKU
    {
        return $this->sku;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getPoNumber(): int
    {
        return $this->poNumber;
    }

    public function getUpc()
    {
        return $this->upc;
    }

    public function getChannelOrderItemId()
    {
        return $this->channelOrderItemID;
    }

    public function getItemXml()
    {
        return $this->itemXML;
    }

    public static function getByOrderId($orderID)
    {
        $sql = "SELECT s.sku, p.name, oi.price, oi.quantity 
                FROM order_item oi 
                JOIN sku s ON oi.sku_id = s.id 
                JOIN product p ON s.product_id = p.id 
                WHERE order_id = :order_id";
        $queryParams = [
            ":order_id" => $orderID
        ];
        return MDB::query($sql, $queryParams, 'fetchAll');
    }

    public static function updateQty($orderNum, $sku, $qty)
    {
        $sql = "UPDATE order_item oi 
                JOIN sync.order o ON o.id = oi.order_id 
                JOIN sku sk ON sk.id = oi.sku_id 
                SET oi.quantity = :quantity 
                WHERE o.order_num = :order_num 
                AND sk.sku = :sku";
        $queryParams = [
            ':quantity' => $qty,
            ':order_num' => $orderNum,
            ':sku' => $sku
        ];
        return MDB::query($sql, $queryParams, 'boolean');
    }

    public function save(Order $order)
    {
        $sql = "INSERT INTO order_item (order_id, sku_id, price, item_id, quantity) 
                VALUES (:order_id, :sku_id, :price, :item_id, :quantity)";
        $queryParams = [
            ':order_id' => $order->getOrderId(),
            ':sku_id' => $this->getSku()->getSkuId(),
            ':price' => Ecommerce::toCents($this->getPrice()),
            ':item_id' => $this->getChannelOrderItemId(),
            ':quantity' => $this->getQuantity()
        ];
        return MDB::query($sql, $queryParams, 'boolean');
    }

}
