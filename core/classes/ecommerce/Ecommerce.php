<?php

namespace ecommerce;

use models\channels\SKU;
use models\channels\Tracking;
use PDO;
use controllers\channels\ChannelHelperController as CHC;
use models\ModelDB as MDB;
use IBM;

class Ecommerce
{

    //Prepare channel listings into arrays for manipulation
    public static function prepare_arrays($channelArray)
    {
        $columns = '';
        $values = '';
        $updateString = '';
        $preparedArray = [];
        $returnArray = [];
        foreach ($channelArray as $key => $val) {
            $columns .= $key;
            $values .= ":" . $key;
            $updateString .= $key . "=:" . $key . '2';
            end($channelArray);
            if (key($channelArray) !== $key) {
                $columns .= ',';
                $values .= ',';
                $updateString .= ',';
            }
            $preparedArray[':' . $key] = $val;
            $preparedArray[':' . $key . '2'] = $val;
        }
        $returnArray[0] = $columns;
        $returnArray[1] = $values;
        $returnArray[2] = $updateString;
        $returnArray[3] = $preparedArray;
        return $returnArray;
    }

    //Return listing_id from Select or Insert if not Exists, Update if it does

    public function update_shipping_amount($order, $shipping_amount)
    {
        $sql = "UPDATE sync.order SET shipping_amount = :shipping_amount WHERE order_num = :order";
        $query_params = [
            ':shipping_amount' => $shipping_amount,
            ':order' => $order
        ];
        return MDB::query($sql, $query_params, 'boolean');
    }

    public function update_item_qty($order, $sku, $quantity)
    {
        $sql = "UPDATE order_item oi JOIN sync.order o ON o.id = oi.order_id JOIN sku sk ON sk.id = oi.sku_id SET oi.quantity = :quantity WHERE o.order_num = :order AND sk.sku = :sku";
        $query_params = [
            ':quantity' => $quantity,
            ':order' => $order,
            ':sku' => $sku
        ];
        return MDB::query($sql, $query_params, 'boolean');
    }

    //Save order from channels to DB
    public function save_order(
        $store_id,
        $cust_id,
        $order_num,
        $ship_method,
        $shipping_amount,
        $tax_amount = 0,
        $fee = 0,
        $trans_id = null
    ) {
        $sql = "SELECT id FROM sync.order WHERE store_id = :store_id AND order_num = :order_num";
        $query_params = [
            ':store_id' => $store_id,
            ':order_num' => $order_num
        ];
        $order_id = MDB::query($sql, $query_params, 'fetchColumn');
        if (empty($order_id)) {
            $sql = "INSERT INTO sync.order (store_id, cust_id, order_num, ship_method, shipping_amount, taxes, fee, channel_order_id) VALUES (:store_id, :cust_id, :order_num, :ship_method, :shipping_amount, :taxes, :fee, :trans_id)";
            $query_params = [
                ":store_id" => $store_id,
                ":cust_id" => $cust_id,
                ":order_num" => $order_num,
                ":ship_method" => $ship_method,
                ":shipping_amount" => $shipping_amount,
                ":taxes" => $tax_amount,
                ':fee' => $fee,
                ':trans_id' => $trans_id
            ];
            $order_id = MDB::query($sql, $query_params, 'id');
        }
        return $order_id;
    }

    public function save_taxes($order_id, $taxes)
    {
        $sql = "UPDATE sync.order SET taxes = :taxes WHERE id = :id";
        $query_params = [
            ":taxes" => $taxes,
            ":id" => $order_id
        ];
        return MDB::query($sql, $query_params, 'id');
    }

    public function updateOrderShippingAndTaxes($order_id, $shipping, $taxes)
    {
        $sql = "UPDATE sync.order SET shipping_amount = :shipping, taxes = :taxes WHERE id = :id";
        $query_params = [
            ':shipping' => $shipping,
            ':taxes' => $taxes,
            ':id' => $order_id
        ];
        return MDB::query($sql, $query_params, 'id');
    }

    //Save order items from channel orders to DB
    public function save_order_items($order_id, $sku_id, $price, $quantity, $item_id = '')
    {
        $sql = "INSERT INTO order_item (order_id, sku_id, price, item_id, quantity) VALUES (:order_id, :sku_id, :price, :item_id, :quantity)";
        $query_params = [
            ':order_id' => $order_id,
            ':sku_id' => $sku_id,
            ':price' => $price,
            ':item_id' => $item_id,
            ':quantity' => $quantity
        ];
        return MDB::query($sql, $query_params, 'boolean');
    }

    //Return cust_id from Select or Insert if not Exists
    public function customer_soi(
        $first_name,
        $last_name,
        $street_address,
        $street_address2,
        $city_id,
        $state_id,
        $zip_id
    ) {
        $sql = "SELECT id FROM customer WHERE first_name = :first_name AND last_name = :last_name AND street_address = :street_address AND zip_id = :zip_id";
        $query_params = [
            ':first_name' => $first_name,
            ':last_name' => $last_name,
            ':street_address' => $street_address,
            ':zip_id' => $zip_id
        ];
        $cust_id = MDB::query($sql, $query_params, 'fetchColumn');
        if (empty($cust_id)) {
            $sql = "INSERT INTO customer (first_name, last_name, street_address, street_address2, city_id, state_id, zip_id) VALUES (:first_name, :last_name, :street_address, :street_address2, :city_id, :state_id, :zip_id)";
            $query_params = [
                ":first_name" => $first_name,
                ":last_name" => $last_name,
                ":street_address" => $street_address,
                ":street_address2" => $street_address2,
                ":city_id" => $city_id,
                ":state_id" => $state_id,
                ":zip_id" => $zip_id
            ];
            $cust_id = MDB::query($sql, $query_params, 'id');
        }
        return $cust_id;
    }

    public function get_current_inventory($table)
    {
        $table = CHC::sanitize_table_name($table);
        $sql = "SELECT sku, inventory_level FROM $table";
        return MDB::query($sql, [], 'fetchAll', PDO::FETCH_GROUP | PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);
    }

    public function update_inventory($sku, $qty, $price, $table)
    {
        $table = CHC::sanitize_table_name($table);
        if (!empty($price)) {
            $sql = "UPDATE $table tb SET tb.inventory_level = :qty, tb.price = :price WHERE tb.sku = :item";
            $query_params = [
                ":qty" => $qty,
                ":price" => $price,
                ":item" => $sku
            ];
        } else {
//            $sql = "UPDATE stock st JOIN sku sk ON sk.id = st.sku_id JOIN $table tb ON st.id = tb.stock_id SET tb.inventory_level = :qty WHERE sk.sku = :item";
//            UPDATE $table tb SET tb.inventory_level = :qty WHERE tb.sku = :item
//            INSERT INTO $table tb (tb.sku, tb.inventory_level) VALUES(:sku, :qty) ON DUPLICATE KEY UPDATE tb.inventory_level = :qty2
            $sql = "INSERT INTO $table (sku, inventory_level) VALUES(:sku, :qty) ON DUPLICATE KEY UPDATE inventory_level = :qty2";
            $query_params = [
                ":qty" => $qty,
                ":sku" => $sku,
                ":qty2" => $qty
            ];
        }
        return MDB::query($sql, $query_params, 'boolean');
    }

    public function sync_inventory_from($fromtable, $totable)
    {
        $fromtable = CHC::sanitize_table_name($fromtable);
        $totable = CHC::sanitize_table_name($totable);
        $sql = "SELECT la.title, la.description, p.upc, sk.sku, la.inventory_level AS quantity, la.price, la.category_id, p.weight FROM sync.product p JOIN sku sk ON sk.product_id = p.id JOIN $fromtable la ON la.sku = sk.sku LEFT OUTER JOIN $totable le ON le.sku = la.sku WHERE p.upc <> '' AND le.sku IS NULL";
        return MDB::query($sql, [], 'fetchAll');
    }

    public function get_mapped_category($fromcolumn, $tocolumn, $category_id)
    {
        $fromcolumn = CHC::sanitize_table_name($fromcolumn);
        $tocolumn = CHC::sanitize_table_name($tocolumn);
        $sql = "SELECT $tocolumn FROM categories_mapped WHERE $fromcolumn = :category_id";
        $query_params = [
            ':category_id' => $category_id
        ];
        return MDB::query($sql, $query_params, 'fetchColumn');
    }

    //Find if order has been downloaded to VAI
    public static function findDownloadedVaiOrder($order_num)
    {
        $sql = "SELECT * FROM order_sync WHERE order_num = :order_num AND success = 1";
        $query_params = [
            ':order_num' => $order_num
        ];
        return MDB::query($sql, $query_params, 'rowCount');
    }

    public static function orderExists($orderNum)
    {
        $number = Ecommerce::findDownloadedVaiOrder($orderNum);

        if ($number > 0) {
            Ecommerce::dd("Found in database");
            return true;
        }
        return false;
    }

    //Create order for download to VAI to allow for XML creation
    public function insertOrder($order_num, $success = 1, $type = 'Amazon')
    {
        $sql = "INSERT INTO order_sync (order_num, success, type) VALUES (:order_num, :success, :type)";
        $query_params = [
            ":order_num" => $order_num,
            ":success" => $success,
            ":type" => $type
        ];
        return MDB::query($sql, $query_params, 'boolean');
    }

    //Get Channel Account #'s
    public function get_acct_num($channel)
    {
        $sql = "SELECT co_one_acct, co_two_acct FROM channel WHERE channel.name = :name";
        $query_params = [
            ':name' => $channel
        ];
        return MDB::query($sql, $query_params, 'fetch');
    }

    //Create order XML for download to VAI
    public function create_xml(
        $channel_num,
        $channel_name,
        $order_num,
        $timestamp,
        $shipping_amount,
        $shipping,
        $order_date,
        $buyer_phone,
        $ship_to_name,
        $address,
        $address2,
        $city,
        $state,
        $zip,
        $country,
        $item_xml
    ) {
        $xml = <<<EOD
        <NAMM_PO version="2007.1">
            <Id>S2S{$channel_num}_PO$order_num</Id>
            <Timestamp>$timestamp</Timestamp>
            <BuyerId>$channel_num</BuyerId>
            <BuyerIdDesc>My Music Life $channel_name</BuyerIdDesc>
            <PO>$order_num</PO>
            <Backorder>Y</Backorder>
            <SupplierId>33076</SupplierId>
            <SupplierName>Chesbro Music Co.</SupplierName>
            <TermsCode>P999</TermsCode>
            <TermsDays>0</TermsDays>
            <TermsDate>12/31/1899</TermsDate>
            <TermsPercent>$shipping_amount</TermsPercent>
            <TermsPercentDays>0</TermsPercentDays>
            <ShipInstructions></ShipInstructions>
            <TranspCode>$shipping</TranspCode>
            <TranspDesc></TranspDesc>
            <TranspCarrier></TranspCarrier>
            <TranspTime>0</TranspTime>
            <TranspTerms></TranspTerms>
            <DateOrdered>$timestamp</DateOrdered>
            <DateBeginShip>12/31/1899</DateBeginShip>
            <DateEndShip>12/31/1899</DateEndShip>
            <DateCancel>12/31/1899</DateCancel>
            <BuyerName></BuyerName>
            <BuyerPhone>$buyer_phone</BuyerPhone>
            <POComments></POComments>
            <ShipToName>$ship_to_name</ShipToName>
            <ShipToId>$channel_num</ShipToId>
            <ShipToAddress1>$address</ShipToAddress1>
            <ShipToAddress2>$address2</ShipToAddress2>
            <ShipToAddress3></ShipToAddress3>
            <ShipToAddress4></ShipToAddress4>
            <ShipToCity>$city</ShipToCity>
            <ShipToState>$state</ShipToState>
            <ShipToPostalCode>$zip</ShipToPostalCode>
            <ShipToCountry></ShipToCountry>
            <ShipToCountryCode>$country</ShipToCountryCode>
            <SoldToName>My Music Life $channel_name</SoldToName>
            <SoldToId>$channel_num</SoldToId>
            <SoldToAddress1>PO Box 2009</SoldToAddress1>
            <SoldToAddress2></SoldToAddress2>
            <SoldToAddress3></SoldToAddress3>
            <SoldToAddress4></SoldToAddress4>
            <SoldToCity>Idaho Falls</SoldToCity>
            <SoldToState>ID</SoldToState>
            <SoldToPostalCode>83403-2009</SoldToPostalCode>
            <SoldToCountry></SoldToCountry>
            <SoldToCountryCode></SoldToCountryCode>
            <PORevisionNumber></PORevisionNumber>
            <POStatusIndicator></POStatusIndicator>
            <ASNRequirement></ASNRequirement>
            <POFileType></POFileType>
            $item_xml
            </NAMM_PO>
EOD;
        return $xml;
    }

    //Create Order Item XML for inclusion in Order XML
    public function create_item_xml($sku, $title, $ponumber, $quantity, $principle, $upc)
    {
        $item_xml = "<Item>
            <ItemId>$sku</ItemId>
            <ItemDesc><![CDATA[ $title ]]></ItemDesc>
            <POLineNumber>$ponumber</POLineNumber>
            <UOM>EACH</UOM>
            <Qty>$quantity</Qty>
            <UCValue>$principle</UCValue>
            <UCCurrencyCode></UCCurrencyCode>
            <RetailValue></RetailValue>
            <RetailCurrencyCode></RetailCurrencyCode>
            <StdPackQty></StdPackQty>
            <StdContainerQty></StdContainerQty>
            <SupplierItemId>$sku</SupplierItemId>
            <BarcodeId>$upc</BarcodeId>
            <BarcodeType>UPC</BarcodeType>
            <ItemNote></ItemNote>
        </Item>";
        return $item_xml;
    }

    //Create Tax Item for inclusion in Order XML
    public static function create_tax_item_xml($poNumber, $totalTax, $state, $stateTaxItemName = '')
    {
        $itemName = '';
        if (!empty($stateTaxItemName)) {
            $itemName = $stateTaxItemName;
        } else {
            if ($state == 'ID') {
                $itemName = "SALES TAX IDAHO @ 6%";
            } elseif ($state == 'CA') {
                $itemName = "SALES TAX CALIFORNIA";
            } elseif ($state == 'WA') {
                $itemName = "SALES TAX WASHINGTON";
            }
        }
        $itemXml = "<Item>
                    <ItemId>$itemName</ItemId>
                    <ItemDesc><![CDATA[ $itemName ]]></ItemDesc>
                    <POLineNumber>$poNumber</POLineNumber>
                    <UOM>EACH</UOM>
                    <Qty>1</Qty>
                    <UCValue>$totalTax</UCValue>
                    <UCCurrencyCode></UCCurrencyCode>
                    <RetailValue></RetailValue>
                    <RetailCurrencyCode></RetailCurrencyCode>
                    <StdPackQty></StdPackQty>
                    <StdContainerQty></StdContainerQty>
                    <SupplierItemId>$itemName</SupplierItemId>
                    <BarcodeId></BarcodeId>
                    <BarcodeType>UPC</BarcodeType>
                    <ItemNote></ItemNote>
                </Item>";
        return $itemXml;
    }

    public function substring_between($haystack, $start, $end)
    {
        if (stripos($haystack, $start) === false || stripos($haystack, $end) === false) {
            return false;
        } else {
            $start_position = stripos($haystack, $start) + strlen($start);
            $end_position = stripos($haystack, $end, $start_position);
            return substr($haystack, $start_position, $end_position - $start_position);
        }
    }

    public function curl($url)
    {
        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_AUTOREFERER => true,
            CURLOPT_CONNECTTIMEOUT => 120,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_USERAGENT => "Mozilla/5.0 (Windows; U; MSIE 9.0; Windows NT 9.0; en-US)",
            CURLOPT_URL => $url,
            CURLOPT_SSL_VERIFYPEER => false
        ];
        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    public function clean_sku($sku)
    {
        if (strpos($sku, ';') > 0) {
            $sku = substr($sku, 0, strpos($sku, ';'));
        } else {
            if (strpos($sku, ',') > 0) {
                $sku = substr($sku, 0, strpos($sku, ','));
            }
        }
        return $sku;
    }

    //Look for this in cronorderseb.php and other channels. Currently only in cronordersam.php
    public function get_channel_num($channel_name, $sku)
    {
        $accounts = $this->get_acct_num($channel_name);
        $co_one_acct = $accounts['co_one_acct'];
        $co_two_acct = $accounts['co_two_acct'];
        $inventory = IBM::findInventory($sku, $channel_name);
        $co_one_qty = $inventory['CO_ONE'];
        $co_two_qty = $inventory['CO_TWO'];
        if (!empty($co_one_qty)) {
            $channel_num = $co_one_acct;
        } elseif (!empty($co_two_qty)) {
            $channel_num = $co_two_acct;
        } else {
            $channel_num = $co_one_acct;
        }
        return $channel_num;
    }

    public static function get_tax_item_xml($state_code, $poNumber, $totalTax, $stateTaxItemName = '')
    {
        $itemXml = '';
        if (!empty($stateTaxItemName)) {
            $itemXml .= self::create_tax_item_xml($poNumber, $totalTax, '', $stateTaxItemName);
        } else {
            if (strtolower($state_code) == 'id' || strtolower($state_code) == 'idaho') {
                $itemXml .= self::create_tax_item_xml($poNumber, number_format($totalTax, 2), 'ID');
            } elseif (strtolower($state_code) == 'ca' || strtolower($state_code) == 'california') {
                $itemXml .= self::create_tax_item_xml($poNumber, number_format($totalTax, 2), 'CA');
            } elseif (strtolower($state_code) == 'wa' || strtolower($state_code) == 'washington') {
                $itemXml .= self::create_tax_item_xml($poNumber, number_format($totalTax, 2), 'WA');
            }
        }
        return $itemXml;
    }

    public function getCompanyTaxInfo($company_id)
    {
        $sql = "SELECT s.abbr, t.tax_rate, t.tax_line_name, t.shipping_taxed FROM taxes t INNER JOIN state s ON s.id = t.state_id WHERE company_id = :company_id";
        $query_params = [
            ':company_id' => $company_id
        ];
        return MDB::query($sql, $query_params, 'fetchAll', PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);
    }

    public function taxableState($stateArray, $state)
    {
        $taxable = false;
        foreach ($stateArray as $s => $value) {
            if ($s == $state) {
                $taxable = true;
            }
        }
        return $taxable;
    }

    public function calculateTax($stateTaxArray, $totalWithoutTax, $totalShipping)
    {
        $taxRate = $stateTaxArray['tax_rate'] / 100;
        $totalTax = number_format($totalWithoutTax * $taxRate, 2);
        if ($stateTaxArray['shipping_taxed']) {
            $totalTax += number_format($totalShipping * $taxRate, 2);
        }
        return $totalTax;
    }

    public static function dd($data)
    {
        echo '<br><pre>';
        print_r($data);
        echo '</pre><br>';
    }

    public static function curlRequest($request)
    {
        return self::sendCurl($request);
    }

    protected static function sendCurl($request)
    {
        $response = curl_exec($request);
        if (curl_errno($request)) {
            curl_close($request);
            return 'Error: ' . curl_error($request);
        }
        curl_close($request);
        return $response;
    }

    public static function xmlOpenTag()
    {
        $openTag = '<?xml version="1.0" encoding="UTF-8"?>';
        return $openTag;
    }

    public static function openXMLParentTag($tagName, $param = null)
    {
        $parentTag = "<$tagName ";
        if (!empty($param)) {
            $parentTag .= $param;
        }
        $parentTag .= ">";
        return $parentTag;
    }

    public static function closeXMLParentTag($tagname)
    {
        return "</$tagname>";
    }

    public static function xmlTag($tagName, $tagContents, $parameters = null)
    {
        $tag = "<$tagName";
        if ($parameters) {
            $tag .= " ";
            $tag .= $parameters[0] . '="' . $parameters[1] . '"';
        }
        $tag .= ">";
        $tag .= htmlspecialchars($tagContents);
        $tag .= "</$tagName>";
        return $tag;
    }

    protected static function generateXML($value, $pkey, $key)
    {
        $generatedXML = self::openXMLParentTag($pkey);
        $generatedXML .= self::makeXML($value, $key);
        $generatedXML .= self::closeXMLParentTag($pkey);
        return $generatedXML;
    }

    public static function makeXML($xml, $pkey = null)
    {
        //        $xml = [
//            'Item' =>
//            [
//                'Title' => 'The Whiz Bang Awesome Product',
//                'SKU' => '123456',
//                'NameValueList' => [
//                    'Name' => 'Brand',
//                    'Value' => 'Unbranded'
//                ],
//                'NameValueList' => [
//                    'Name' => 'MPN',
//                    'Value' => '123456'
//                ],
//                'ShippingDetails' => [
//                    'ShippingServiceOptions' => [
//                        'FreeShipping' => 'true',
//                        'ShippingService' => 'ShippingMethodStandard',
//                        'ShippingServiceCost' => '0.00',
//                        'ShippingServiceAdditionalCost' => '0.00',
//                        'ShippingServicePriority' => '1'
//                    ],
//                    'ShippingServiceOptions' => [
//                        'ShippingService' => 'UPSGround',
//                        'ShippingServiceCost' => '9.99',
//                        'ShippingServiceAdditionalCost' => '9.99',
//                        'ShippingServicePriority' => '2'
//                    ]
//                ]
//            ]
//        ];

        $generatedXML = '';
        foreach ($xml as $key => $value) {
            if (is_array($value)) {
                if (is_numeric($key)) {
                    $generatedXML .= self::generateXML($value, $pkey, $key);
//                    $generatedXML .= self::openXMLParentTag($pkey);
//                    $generatedXML .= self::makeXML($value, $key);
//                    $generatedXML .= self::closeXMLParentTag($pkey);
                } else {
                    $pkey = $key;
                    if (array_key_exists(0, $value)) {
                        $generatedXML .= self::makeXML($value, $pkey);
                    } else {
                        $generatedXML .= self::generateXML($value, $key, $pkey);
//                        $generatedXML .= self::openXMLParentTag($key);
//                        $generatedXML .= self::makeXML($value, $pkey);
//                        $generatedXML .= self::closeXMLParentTag($key);
                    }
                }
            } else {
                $parameters = null;
                $delimiter = '~';
                if (strpos($key, $delimiter) !== false) {
                    $param = substr($key, strpos($key, $delimiter) + 1);
                    $attribute = strstr($param, '=', true);
                    $attributeValue = substr($param, strpos($param, '=') + 1);
                    $parameters[] = $attribute;
                    $parameters[] = $attributeValue;
                    $key = strstr($key, $delimiter, true);
                }
                $generatedXML .= self::xmlTag($key, $value, $parameters);
            }
        }
        return $generatedXML;
    }

    protected static function determineErlanger($shipping, $address)
    {
        if (isset($address['state'])) {
            if (
                stripos($address['address2'], '1850 Airport') &&
                stripos($address['city'], 'Erlanger') &&
                stripos($address['state'], 'KY') &&
                stripos($address['zip'], '41025')
            ) {
                $shipping = 'UPIP';
            }
        }
        return $shipping;
    }

    protected static function determineShippingCode($shipping, $shipmentMethod)
    {
        if ($shipmentMethod) {
            switch (strtolower($shipmentMethod)) {
                case 'standard':
                    $shipping = 'ZSTD';
                    break;
                case 'expedited':
                    $shipping = 'ZEXP';
                    break;
                case 'secondday':
                    $shipping = 'Z2DY';
                    break;
                case '2nd day':
                    $shipping = 'Z2ND';
                    break;
                case 'nextday':
                case 'next day':
                    $shipping = 'ZNXT';
                    break;
            }
        }
        return $shipping;
    }

    public function shippingCode($total, $address = [], $shipmentMethod = null)
    {
        $shipping = 'ZSTD';
        if ($total >= 250) {
            $shipping = 'URIP';
        }
        $shipping = self::determineErlanger($shipping, $address);
        $shipping = self::determineShippingCode($shipping, $shipmentMethod);
        return $shipping;
    }

    protected static function saveFileToDisk($folder, $filename, $orderXml)
    {
        file_put_contents($folder . $filename, $orderXml);
        chmod($folder . $filename, 0777);
        file_put_contents($folder . 'backup/' . $filename, $orderXml);
        chmod($folder . 'backup/' . $filename, 0777);
    }

    public function saveXmlToFTP($orderNum, $orderXml, $folder, $channel)
    {
        $filename = $orderNum . '.xml';
        echo $filename . '<br />';
        self::saveFileToDisk($folder, $filename, $orderXml);
        if (file_exists($folder . $filename)) {
            echo "Successfully uploaded $filename<br />";
            $results = $this->insertOrder($orderNum, 1, $channel);
            if ($results) {
                echo "$orderNum successfully updated in DB.";
            }
        }
    }

    protected static function cellOpeningTag($value, $cellType)
    {
        $openTag = '';
        $openTag .= "<$cellType ";
        $openTag .= self::cellFormat($value);
        $openTag .= ">";
        return $openTag;
    }

    protected static function cellClosingTag($cellType)
    {
        $closingTag = "</$cellType>";
        return $closingTag;
    }

    protected static function cellFormat($value)
    {
        $format = '';
        if (isset($value['format'])) {
            if ($value['format'] !== 'aboveZero') {
                $format .= "class='{$value['format']}'";
            } else {
                if ($value['value'] < 0) {
                    $class = "loss";
                } else {
                    $class = "gain";
                }
                $format .= "class=$class";
            }
        }
        return $format;
    }

    protected static function cellValue($value, $cellType)
    {
        $cell = '';
        if ($cellType == 'th') {
            $cell .= ucfirst($value);
        } else {
            if (!is_array($value)) {
                $cell .= $value;
            } else {
                $cell .= isset($value['url']) ? "<a href='{$value['url']}' target='_blank'>" : "";
                $cell .= isset($value['display']) ? $value['display'] : $value['value'];
                $cell .= isset($value['url']) ? "</a>" : "";
            }
        }
        return $cell;
    }

    protected static function tableRow($array, $cellType = "td")
    {
        $row = "<tr>";
        foreach ($array as $key => $value) {
            $row .= self::cellOpeningTag($value, $cellType);
            $row .= self::cellValue($value, $cellType);
            $row .= self::cellClosingTag($cellType);
        }
        $row .= "<tr>";

        return $row;
    }

    protected static function arrayToTableHead($array)
    {
        $head = "<thead>";
        $headArray = array_keys($array[0]);
        $head .= self::tableRow($headArray, "th");
        $head .= "</thead>";

        return $head;
    }

    protected static function arrayToTableBody($array)
    {
        $body = "<tbody>";
        foreach ($array as $a) {
            $body .= self::tableRow($a);
        }
        $body .= "</tbody>";

        return $body;
    }

    public static function arrayToTable($array, $tableLabel = '')
    {
        $table = $tableLabel;
        $table .= "<table class='tableBorder'>";
        $table .= self::arrayToTableHead($array);
        $table .= self::arrayToTableBody($array);
        $table .= "</table>";

        return $table;
    }

    public static function sortBy($sellers, $sortBy)
    {
        $priceArray = [];
        foreach ($sellers as $key => $row) {
            $priceArray[$key] = $row[$sortBy];
        }
        array_multisort($priceArray, SORT_ASC, $sellers);

        return $sellers;
    }

    public static function toDollars($cents)
    {
        $dollars = $cents / 100;
        $dollars = self::formatMoney($dollars);
        return $dollars;
    }

    public static function toCents($dollars)
    {
        $cents = $dollars * 100;
        return $cents;
    }

    public static function roundMoney($number, $places = 2)
    {
        $number = round($number, $places);
        return $number;
    }

    public static function formatMoney($number, $places = 2)
    {
        $number = number_format($number, $places);
        return $number;
    }

    public static function removeCommasInNumber($number)
    {
        $number = number_format($number, '2', '.', '');
        return $number;
    }

    public static function getChannelListingsFromDB($channel)
    {
        $sql = "SELECT sku, store_listing_id as id FROM listing_$channel";
        return MDB::query($sql, [], 'fetchAll', PDO::FETCH_GROUP | PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);
    }

    public static function createFormattedDate($date, $format = 'Y/m/d')
    {
        $date = date_create($date);
        $date = $date->format($format);
        return $date;
    }

    public function orderItemHtml($item, $total)
    {
        $sku = $item['sku'];
        $name = $item['name'];
        $price = self::formatMoney($item['price']);
        $total += $price;
        $quantity = $item['quantity'];
        $itemHtml = "<dt><span class='hide'>Quantity x Name</span></dt>
        <dd class='product-name'>$quantity x $name<br>
        <b>$sku</b>
        </dd>
        <dt><span class='hide'>Price</span></dt>
        <dd class='product-total'><i class='fa fa-usd'></i><b>$price</b></dd><hr>";
        return [
            $itemHtml,
            $total
        ];
    }

    public function orderHtml($oi, $total, $item_html)
    {
        extract($oi);
        $date = self::createFormattedDate($oi['date'], 'm/d/Y');
        $tracking_url = '';
        if ($carrier == 'USPS') {
            $tracking_url = 'https://tools.usps.com/go/TrackConfirmAction?qtc_tLabels1=' . $tracking_num;
        } elseif ($carrier == 'FedEx') {
            $tracking_url = 'https://www.fedex.com/apps/fedextrack/?tracknumbers=' . $tracking_num . '&language=en&cntry_code=us';
        } elseif ($carrier == 'UPS') {
            $tracking_url = 'https://wwwapps.ups.com/WebTracking/track?track=yes&trackNums=' . $tracking_num . '&loc=en_us';
        }
        $date_processed = self::createFormattedDate($oi['date'], 'm/d/Y H:i:s');
        $status = 'Unshipped';
        if ($track_successful == '1') {
            $status = 'Shipped';
        }
        $html = "<table class='popuptable'>
        <thead>
            <th>Date</th>
            <th>Order Number</th>
            <th>Channel</th>
            <th>Status</th>
            <th>Total</th>
        </thead>
        <tbody>
            <tr>
                <td>$date</td>
                <td>$order_num</td>
                <td>$channel</td>
                <td>$status</td>
                <td><i class='fa fa-usd'></i><b>$total</b></td>
            </tr>
            <tr>
                <td colspan='5'>
                    <article class='one-third'>
                    <h3>Customer</h3>
                        <dl class='order-dl'>
                            <dt>
                                <span class='hide'>Customer Details</span>
                                <i class='fa fa-map-marker'></i>
                            </dt>
                            <dd>$first_name $last_name<br>
                            $street_address<br>";
        $html .= (!empty($street_address2) ? $street_address2 . '<br>' : '');
        $html .= "$city, $state_abbr $zip
                            </dd>
                        </dl>
                    </article>
                    <article class='one-third'>
                        <h3>Order Status</h3>
                        <dl class='order-dl'>
                            <dt>
                                <span class='hide'>Date Processed</span>
                                <i class='fa fa-calendar'></i>
                            </dt>
                            <dd>$date_processed</dd>
                            <dt>
                                <span class='hide'>Tracking</span>
                                <i class='fa fa-truck'></i>
                            </dt>
                            <dd>$carrier<br>";
        $html .= (!empty($tracking_url) ? "<a class='product_link' href=" . $tracking_url . " target='_blank'>" : "");
        $html .= "$tracking_num";
        $html .= (!empty($tracking_url) ? "</a>" : "");
        $html .= "</dd>
                        </dl>
                    </article>
                    <article class='one-third'>
                    <h3>Items</h3>
                        <dl class='item-list'>
                            $item_html
                        </dl>
                    </article>
                </td>
            </tr>";
        $html .= "</tbody>
        </table>";
        return $html;
    }

    public function getChannelNumbers($channel)
    {
        $companyNumbers = [
            'ebay' => ['5001072', '5001420'],
            'amazon' => ['5001017', '5004375'],
            'reverb' => ['5001942', '5005843'],
            'bigcommerce' => ['5002050', '5005370'],
            'walmart' => ['5002193', '5007106'],
            'fba' => ['5001432', '5005460'],
            'harmony' => ['5001860']
        ];
        return implode(",", $companyNumbers[strtolower($channel)]);

    }
}