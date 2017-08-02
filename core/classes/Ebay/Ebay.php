<?php

namespace Ebay;

use models\channels\ChannelAPI;
use ecommerce\Ecommerce;
use models\channels\Fee;
use models\ModelDB as MDB;

class Ebay //extends ChannelAPI
{
    protected $apiTable = 'api_ebay';
    protected $apiColumns = [
        'api_from'
    ];

    public function sanitize_column_name($col)
    {
        switch ($col) {
            case $col == "token":
                $column = 'token';
                break;
            case $col == "devid":
                $column = 'devid';
                break;
            case $col == "sandbox_devid":
                $column = 'sandbox_devid';
                break;
            case $col == "sandbox_token":
                $column = 'sandbox_token';
                break;
            case $col == "sandbox_appid":
                $column = 'sandbox_appid';
                break;
            case $col == "sandbox_certid":
                $column = 'sandbox_certid';
                break;
        }
        return $column;
    }

    public function update_app_info($crypt, $store_id, $column, $id)
    {
        $column = $this->sanitize_column_name($column);
        $sql = "UPDATE api_ebay SET $column = :id WHERE store_id = :store_id";
        $query_params = [
            ':id' => $crypt->encrypt($id),
            ':store_id' => $store_id
        ];
        MDB::query($sql, $query_params);
    }

    public function get_ebay_app_id($user_id, $sand = null)
    {
        if (empty($sand)) {
            $sql = "SELECT store_id, devid, appid, certid, token FROM api_ebay INNER JOIN store ON api_ebay.store_id = store.id INNER JOIN account ON account.company_id = store.company_id INNER JOIN channel ON channel.id = store.channel_id WHERE account.id = :user_id AND channel.name = 'Ebay'";
        } else {
            $sql = "SELECT store_id, sandbox_devid AS devid, sandbox_appid AS appid, sandbox_certid AS certid, sandbox_token AS token FROM api_ebay INNER JOIN store ON api_ebay.store_id = store.id INNER JOIN account ON account.company_id = store.company_id INNER JOIN channel ON channel.id = store.channel_id WHERE account.id = :user_id AND channel.name = 'Ebay'";
        }
        $query_params = [
            ':user_id' => $user_id
        ];
        return MDB::query($sql, $query_params, 'fetch');
    }

    public function get_listings($item_id = null)
    {
        if (!$item_id) {
            $sql = "SELECT id, store_listing_id, price FROM listing_ebay";
            return MDB::query($sql, [], 'fetchAll');
        } else {
            $sql = "SELECT id FROM listing_ebay WHERE store_listing_id = :item_id";
            $query_params = [
                'item_id' => $item_id
            ];
            return MDB::query($sql, $query_params, 'fetchColumn');
        }
    }

    public function get_recently_updated_listings()
    {
        $sql = "SELECT store_listing_id, description FROM listing_ebay WHERE DATE(last_edited) = CURRENT_DATE ";
        return MDB::query($sql, [], 'fetchAll');
    }

    public function get_listing_upc()
    {
        $sql = "SELECT le.id, le.store_listing_id, le.sku, p.upc FROM listing_ebay le LEFT JOIN sku sk ON le.sku = sk.sku LEFT JOIN product p ON p.id = sk.product_id";
        return MDB::query($sql, [], 'fetchAll');
    }

    public function get_listing_id($sku)
    {
        $sql = "SELECT store_listing_id FROM listing_ebay WHERE sku = :sku";
        $query_params = [
            ':sku' => $sku
        ];
        return MDB::query($sql, $query_params, 'fetchColumn');
    }

    public function get_transaction_id($item_id)
    {
        $requestName = 'GetItemTransactions';

        $xml = [
            'ItemID' => $item_id
        ];

        $response = EbayClient::ebayCurl($requestName, $xml);
        return $response;
    }

    public function ebay_pricing(Ecommerce $ecommerce, $minimumProfitPercent, $minimumNetProfitPercent, $increment, $sku, $quantity, $msrp, $pl10, $pl1, $cost, $shippingIncludedInPrice, $shippingCharged, $propose = null, $increaseBy = 0)
    {

        $ebayFeeMax = 250;

        $paypalFeePercent = .029; //Round up
        $paypalFeeFlat = 0.30;

        $cost = Ecommerce::formatMoney($cost);

        $costOfQty = $cost * $quantity;

        if (empty($propose)) {
            $totalPrice = $pl10 * $quantity;
        } elseif (empty($increaseBy)) {
            $totalPrice = Ecommerce::roundMoney($costOfQty / (1 - ($minimumProfitPercent / 100)));
            $pl10 = Ecommerce::formatMoney($totalPrice / $quantity);
        } else {
            $totalPrice = Ecommerce::formatMoney($increaseBy);
            $pl10 = Ecommerce::formatMoney($totalPrice / $quantity);
        }

        $shippingCost = 3.99; //Amount we paid to ship the product

        $ebayFeePercent = Fee::getCategoryBySku('categories_ebay', 'listing_ebay', $sku);

        $shippingCollected = Ecommerce::formatMoney($shippingIncludedInPrice ? $shippingCharged : 0);

        $ebayTotalFee = Ecommerce::roundMoney((($totalPrice + $shippingCollected) * $ebayFeePercent) < $ebayFeeMax ? (($totalPrice + $shippingCollected) * $ebayFeePercent) : $ebayFeeMax);

        $paypalTotalFee = Ecommerce::roundMoney(($totalPrice + $shippingCollected) * $paypalFeePercent) + $paypalFeeFlat;

        $totalFees = Ecommerce::formatMoney($ebayTotalFee + $paypalTotalFee + $shippingCost);

        $totalCost = $costOfQty + $totalFees;

        $grossProfit = $totalPrice + $shippingCollected - $costOfQty;
        $grossProfitPercent = Ecommerce::formatMoney($grossProfit / $totalPrice, 4) * 100;

        $netProfit = Ecommerce::formatMoney($grossProfit - $ebayTotalFee - $paypalTotalFee - $shippingCost);
        $netProfitPercent = Ecommerce::formatMoney($netProfit / $totalPrice, 4) * 100;

        if (!empty($propose) && ($grossProfitPercent < $minimumProfitPercent || $netProfitPercent < $minimumNetProfitPercent)) {
            $totalPrice = $totalPrice + $increment;
            $totalPrice = Ecommerce::formatMoney($totalPrice);
            $priceArray = $this->ebay_pricing($ecommerce, $minimumProfitPercent, $minimumNetProfitPercent, $increment, $sku, $quantity, $msrp, $pl10, $pl1, $cost, $shippingIncludedInPrice, $shippingCharged, 1, $totalPrice);
        } else {
            $priceArray = compact(
                'sku', 'quantity', 'msrp', 'pl10', 'pl1', 'cost', 'totalPrice',
                'totalCost', 'shippingCollected', 'shippingCost', 'ebayFeePercent',
                'ebayFeeMax', 'ebayTotalFee', 'paypalFeePercent', 'paypalFeeFlat', 'paypalTotalFee',
                'minimumProfitPercent', 'totalFees', 'grossProfit', 'grossProfitPercent', 'netProfit',
                'netProfitPercent'
            );
        }
        return $priceArray;
    }

    public function pricingTables($priceArray)
    {
        $tableArray = [
            [
                'Qty' => $priceArray['quantity'],
                'pl10' => $priceArray['pl10'],
                'totalPrice' => [
                    'value' => $priceArray['totalPrice'],
                    'format' => 'revenue'
                ],
                'shippingCollected' => [
                    'value' => $priceArray['shippingCollected'],
                    'format' => 'revenue'
                ],
                'shippingCost' => [
                    'value' => $priceArray['shippingCost'],
                    'format' => 'expense'
                ],
                'ebayTotalFee' => [
                    'value' => $priceArray['ebayTotalFee'],
                    'format' => 'expense'
                ],
                'paypalTotalFee' => [
                    'value' => $priceArray['paypalTotalFee'],
                    'format' => 'expense'
                ],
                'cost' => [
                    'value' => $priceArray['cost'],
                    'format' => 'expense'
                ],
                'totalCost ((Qty x Cost) + Fees)' => [
                    'value' => $priceArray['totalCost'],
                    'format' => 'expense'
                ],
                'grossProfit' => [
                    'value' => $priceArray['grossProfit'],
                    'display' => "{$priceArray['grossProfit']} ({$priceArray['grossProfitPercent']}%)",
                    'format' => 'aboveZero'
                ],
                'netProfit' => [
                    'value' => $priceArray['netProfit'],
                    'display' => "{$priceArray['netProfit']} ({$priceArray['netProfitPercent']}%)",
                    'format' => 'aboveZero'
                ],
            ]
        ];
        return $tableArray;
    }

    public static function getApiOrderDays()
    {
        $sql = "SELECT api_from FROM api_ebay WHERE store_id = :store_id";
        $query_params = [
            ':store_id' => EbayClient::getStoreId()
        ];
        return MDB::query($sql, $query_params, 'fetchColumn');
    }

    public static function updateApiOrderDays($from, $to = null)
    {
        $sql = "UPDATE api_ebay SET api_days = :api_days WHERE store_id = :store_id";
        $query_params = [
            ':store_id' => EbayClient::getStoreId(),
            ':api_days' => $from
        ];
        MDB::query($sql, $query_params);
    }
}
