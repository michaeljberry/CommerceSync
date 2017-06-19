<?php

namespace ecommerce;

use PDO;
use controllers\channels\ChannelHelperController as CHC;
use models\ModelDB as EDB;

class Ecommerce
{
    private $eq;

    public function getCustomersCompanies()
    {
        $sql = "SELECT c.id as company FROM account a LEFT OUTER JOIN sync_userroles sur ON sur.UserID = a.id LEFT OUTER JOIN sync_rolepermissions srp ON srp.RoleID = sur.RoleID LEFT OUTER JOIN sync_permissions sp ON srp.PermissionID = sp.ID LEFT OUTER JOIN company c ON c.id = a.company_id WHERE sp.Title IN ('root', 'management') GROUP BY company";
        return EDB::query($sql, [], 'fetchAll');
    }

    public function completeOrderTracking($id)
    {
        $sql = "UPDATE order_sync SET track_successful = 1 WHERE order_id = :order_id";
        $query_params = [
            ':order_id' => $id
        ];
        EDB::query($sql, $query_params);
    }

    public function cancelOrder($id)
    {
        $sql = "UPDATE sync.order SET cancelled = 1 WHERE order_num = :order_num";
        $query_params = [
            ':order_num' => $id
        ];
        EDB::query($sql, $query_params);
    }

    //Get orders by supplied search field
    public function getOrders($array, $channel){
        $result_array = CHC::parseConditions($array);
        $condition = $result_array[0];
        $query_params = $result_array[1];
        $query_params['channel'] = $channel;
        $sql = "SELECT o.id, o.order_num, o.date, c.first_name, c.last_name, t.tracking_num, t.carrier FROM sync.order o JOIN customer c ON o.cust_id = c.id LEFT JOIN tracking t ON o.id = t.order_id JOIN order_sync os ON o.order_num = os.order_id WHERE $condition AND os.type = :channel";
        return EDB::query($sql, $query_params, 'fetchAll');
    }

    //Get specific order information
    public function getOrder($order_id){
        $sql = "SELECT o.order_num, o.date, o.ship_method, o.shipping_amount, o.taxes, c.first_name, c.last_name, c.street_address, c.street_address2, city.name AS city, s.name, s.abbr as state_abbr, z.zip, t.tracking_num, t.carrier, os.processed as date_processed, os.success, os.type as channel, os.track_successful FROM sync.order o JOIN customer c ON o.cust_id = c.id LEFT JOIN tracking t ON o.id = t.order_id JOIN order_sync os ON o.order_num = os.order_id JOIN state s ON c.state_id = s.id JOIN city ON c.city_id = city.id JOIN zip z ON c.zip_id = z.id WHERE o.id = :order_id";
        $query_params = [
            ':order_id' => $order_id
        ];
        return EDB::query($sql, $query_params, 'fetch');
    }

    //Get order items
    public function getOrderItems($order_id){
        $sql = "SELECT s.sku, p.name, oi.price, oi.quantity FROM order_item oi JOIN sku s ON oi.sku_id = s.id JOIN product p ON s.product_id = p.id WHERE order_id = :order_id";
        $query_params = [
            ":order_id" => $order_id
        ];
        return EDB::query($sql, $query_params, 'fetchAll');
    }

    //***************Tracking Number*********************//
    //To mark an order as "tracking_successful"
    public function updateTrackingSuccessful($order_id){
        $sql = "UPDATE order_sync SET track_successful = '1' WHERE order_id = :order_id";
        $query_params = [
            ':order_id' => $order_id
        ];
        return EDB::query($sql, $query_params, 'id');
    }

    public function markAsShipped($order_num, $channel){
        $response = $this->updateTrackingSuccessful($order_num);
        if($response) {
            echo "Tracking for $channel order $order_num was updated!";
            return true;
        }
        return false;
    }

    //Update Tracking #'s for orders
    public function updateTrackingNum($order_id, $tracking_num, $carrier){
        $sql = "SELECT id FROM tracking WHERE order_id = :order_id AND tracking_num = :tracking_num";
        $query_params = [
            ':order_id' => $order_id,
            ':tracking_num' => $tracking_num
        ];
        $tracking_id = EDB::query($sql, $query_params, 'fetchColumn');
        if(empty($tracking_id)) {
            $sql = "INSERT INTO tracking (order_id, tracking_num, carrier) VALUES (:order_id, :tracking_num, :carrier)";
            $query_params = [
                ':order_id' => $order_id,
                ':tracking_num' => $tracking_num,
                ':carrier' => $carrier
            ];
            $tracking_id = EDB::query($sql, $query_params);
        }
        return $tracking_id;
    }

    public function getOrderId($order_num){
        $sql = "SELECT id FROM sync.order WHERE order_num = :order_num";
        $query_params = [
            ':order_num' => $order_num
        ];
        return EDB::query($sql, $query_params, 'fetchColumn');
    }

    //Save order stats for quicker compilation
    public function saveOrderStats($channel, $date, $sales, $units_sold){
        $sql = "INSERT INTO order_stats (channel, stats_date, sales, units_sold) VALUES (:channel, :date, :sales, :units_sold) ON DUPLICATE KEY UPDATE sales = :sales2, units_sold = :units_sold2";
        $query_params = [
            ':channel' => $channel,
            ':date' => $date,
            ':sales' => $sales,
            ':units_sold' => $units_sold,
            ':sales2' => $sales,
            ':units_sold2' => $units_sold
        ];
        return EDB::query($sql, $query_params, 'boolean');
    }

    //Get order information for the day
    public function getOrderStatsWeek(){
        $sql = "SELECT DATE(o.date) AS date, ROUND(SUM(oi.price), 2) AS sales, SUM(oi.quantity) AS units_sold, os.type AS channel FROM sync.order_item oi JOIN sync.order o ON o.id = oi.order_id JOIN order_sync os ON os.order_id = o.order_num WHERE o.date BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 WEEK) AND NOW() GROUP BY DATE(o.date), os.type";
        return EDB::query($sql, [], 'fetchAll');
    }

    public function getOrderStatsSum($channel = null, $period = 'THISMTD', $period2 = null, $period3 = null){
        $interval = CHC::sanitize_time_period($period);
        $dateColumn = 'stats_date';
        $condition = CHC::determine_time_condition($dateColumn, $period, $period2, $period3);

        if(empty($channel)) {
            $sql = "SELECT channel, ROUND(SUM(sales), 2) AS sales, SUM(units_sold) AS units_sold FROM order_stats WHERE $condition GROUP BY channel";
            $query_params = [];
        }else{
            $sql = "SELECT channel, ROUND(SUM(sales), 2) AS sales, SUM(units_sold) AS units_sold FROM order_stats WHERE $condition AND channel = :channel";
            $query_params = [
                ':channel' => $channel
            ];
        }
        return EDB::query($sql, $query_params, 'fetchAll');
    }

    public function stats_table($channel = null, $period = 'THISMTD', $period2 = null, $period3 = null){
        $html = "<div>***Stats are only available after 9/08/2015***</div>
                <table class='stattable'><thead>
                <tr>
                <th>Channel</th>
                <th colspan='2'>Today</th>
                <th colspan='2'>Yesterday</th>
                <th colspan='2'>Week To Date</th>
                <th colspan='2'>MTD</th>
                <th colspan='2'>YTD</th>
                </tr>
                <tr>
                <th></th>
                <th>Sales</th>
                <th>Units Sold</th>
                <th>Sales</th>
                <th>Units Sold</th>
                <th>Sales</th>
                <th>Units Sold</th>
                <th>Sales</th>
                <th>Units Sold</th>
                <th>Sales</th>
                <th>Units Sold</th>
                </tr>
                </thead>";
        //Today
        //Yesterday
        //WTD
        //MTD
        //YTD
        //LYMTD
        //LYYTD
        $channel_array = [];
        $channel_array2 = [];
        //getOrderStatsSum($channel, $time, $period)
        $today = $this->getOrderStatsSum($channel, 'TODAY', $period2, $period3);
        if(!empty($today[0]['channel'])) {
            $channel_array = $this->addStatsToArray($today, $channel_array, 'today');
        }

        $yesterday = $this->getOrderStatsSum($channel, 'YESTERDAY', $period2, $period3);
        if(!empty($yesterday[0]['channel'])) {
            $channel_array = $this->addStatsToArray($yesterday, $channel_array, 'yesterday');
        }

        $wtd = $this->getOrderStatsSum($channel, 'THISWTD', $period2, $period3);
        if(!empty($wtd[0]['channel'])) {
            $channel_array = $this->addStatsToArray($wtd, $channel_array, 'wtd');
        }

        $mtd = $this->getOrderStatsSum($channel, 'THISMTD', $period2, $period3);
        if(!empty($mtd[0]['channel'])) {
            $channel_array = $this->addStatsToArray($mtd, $channel_array, 'mtd');
        }

        $ytd = $this->getOrderStatsSum($channel, 'THISYTD', $period2, $period3);
        if(!empty($ytd[0]['channel'])) {
            $channel_array = $this->addStatsToArray($ytd, $channel_array, 'ytd');
        }

        $mtdly = $this->getOrderStatsSum($channel, 'MTDLASTYEAR', $period2, $period3);
        if(!empty($mtdly[0]['channel'])) {
            $channel_array2 = $this->addStatsToArray($mtdly, $channel_array2, 'mtdly');
        }

        $mtotally = $this->getOrderStatsSum($channel, 'LASTYEARMONTH', $period2, $period3);
        if(!empty($mtotally[0]['channel'])) {
            $channel_array2 = $this->addStatsToArray($mtotally, $channel_array2, 'mtotally');
        }

        $ytdly = $this->getOrderStatsSum($channel, 'LASTYTD', $period2, $period3);
        if(!empty($ytdly[0]['channel'])) {
            $channel_array2 = $this->addStatsToArray($ytdly, $channel_array2, 'ytdly');
        }

        $ytotally = $this->getOrderStatsSum($channel, 'LASTYEAR', $period2, $period3);
        if(!empty($ytotally[0]['channel'])) {
            $channel_array2 = $this->addStatsToArray($ytotally, $channel_array2, 'ytotally');
        }

        foreach($channel_array as $key => $value){
            $t_sales = self::formatMoney((!empty($value['today']['sales']) ? $value['today']['sales'] : "0.00"));
            $t_units = (!empty($value['today']['units_sold']) ? $value['today']['units_sold'] : "0");
            $yesterday_sales = self::formatMoney((!empty($value['yesterday']['sales']) ? $value['yesterday']['sales'] : "0.00"));
            $yesterday_units = (!empty($value['yesterday']['units_sold']) ? $value['yesterday']['units_sold'] : "0");
            $w_sales = self::formatMoney((!empty($value['wtd']['sales']) ? $value['wtd']['sales'] : "0.00"));
            $w_units = (!empty($value['wtd']['units_sold']) ? $value['wtd']['units_sold'] : "0");
            $m_sales = self::formatMoney((!empty($value['mtd']['sales']) ? $value['mtd']['sales'] : "0.00"));
            $m_units = (!empty($value['mtd']['units_sold']) ? $value['mtd']['units_sold'] : "0");
            $y_sales = self::formatMoney((!empty($value['ytd']['sales']) ? $value['ytd']['sales'] : "0.00"));
            $y_units = (!empty($value['ytd']['units_sold']) ? $value['ytd']['units_sold'] : "0");
            $html .= "<tr>";
            $html .= "<td>$key</td>";
            $html .= "<td>$t_sales</td>";
            $html .= "<td>$t_units</td>";
            $html .= "<td>$yesterday_sales</td>";
            $html .= "<td>$yesterday_units</td>";
            $html .= "<td>$w_sales</td>";
            $html .= "<td>$w_units</td>";
            $html .= "<td>$m_sales</td>";
            $html .= "<td>$m_units</td>";
            $html .= "<td>$y_sales</td>";
            $html .= "<td>$y_units</td>";
            $html .= "</tr>";
        }
        if(!empty($ytdly[0]['channel'])) {
            $html .= "<tr>";
            $html .= "<th colspan='11'>Last Year</th>";
            $html .= "</tr>";
            $html .= "<tr>";
            $html .= "<th></th>";
            $html .= "<th colspan='6'></th>";
            $html .= "<th>Sales</th>";
            $html .= "<th>Units</th>";
            $html .= "<th>Sales</th>";
            $html .= "<th>Units</th>";
            $html .= "</tr>";
            foreach ($channel_array2 as $key => $value) {
                $m_sales = self::formatMoney((!empty($value['mtdly']['sales']) ? $value['mtdly']['sales'] : "0.00"));
                $m_units = (!empty($value['mtdly']['units_sold']) ? $value['mtdly']['units_sold'] : "0");
                $mtotal_sales = self::formatMoney( (!empty($value['mtotally']['sales']) ? $value['mtotally']['sales'] : "0.00"));
                $mtotal_units = (!empty($value['mtotally']['units_sold']) ? $value['mtotally']['units_sold'] : "0");
                $y_sales = self::formatMoney((!empty($value['ytdly']['sales']) ? $value['ytdly']['sales'] : "0.00"));
                $y_units = (!empty($value['ytdly']['units_sold']) ? $value['ytdly']['units_sold'] : "0");
                $ytotal_sales = self::formatMoney( (!empty($value['ytotally']['sales']) ? $value['ytotally']['sales'] : "0.00"));
                $ytotal_units = (!empty($value['ytotally']['units_sold']) ? $value['ytotally']['units_sold'] : "0");
                $html .= "<tr>";
                $html .= "<td>$key</td>";
                $html .= "<td colspan='6'></td>";
                $html .= "<td>$m_sales<br>Total: $mtotal_sales</td>";
                $html .= "<td>$m_units<br>Total: $mtotal_units</td>";
                $html .= "<td>$y_sales<br>Total: $ytotal_sales</td>";
                $html .= "<td>$y_units<br>Total: $ytotal_units</td>";
                $html .= "</tr>";
            }
        }
        $html .= "</table>";
        echo $html;
    }

    public function addStatsToArray($array, $channel_array, $period){
        foreach($array as $t){
            $channel = $t['channel'];
            $t_sales = $t['sales'];
            $t_units_sold = $t['units_sold'];
            $channel_array[$channel][$period]['sales'] = $t_sales;
            $channel_array[$channel][$period]['units_sold'] = $t_units_sold;
        }
        return $channel_array;
    }

    public static function prepareStatJson($statsArray, $duration = 'daily')
    {
        $stats = self::eachStatRow($statsArray, $duration);
        $jsonarray = $stats['json'];
        $datearray = $stats['stats_date'];

        $maxkey = self::getMaxKey($jsonarray);

        $jsonarray2 = self::sortAndFillInArray($jsonarray, $maxkey);

        $jsonarray2['x'] = $datearray;
        return $jsonarray2;
    }

    public static function eachStatRow($statsArray, $duration)
    {
        $jsonarray = [];
        $datearray = [];
        $returnArray = [];
        $timeformat = '';
        if($duration === 'daily'){
            $timeformat = 'Y-m-d';
        }elseif($duration === 'monthly'){
            $timeformat = 'Y-m';
        }

        foreach($statsArray as $r => $value){
            $date = self::createFormattedDate($value['stats_date'], $timeformat);

            if(!preg_grep('/' . $date . '/', $datearray)){
                $datearray[] = $date;
                $key = array_search($date, $datearray);
            }

            foreach($value as $k => $v){
                if($k === 'channel'){
                    $channel = $v;
                }elseif($k === 'stats_date'){
                    continue;
                }else{
                    $var = $k;
                    $$var = $v;
                    $jsonarray[$channel . '-' . $var][$key] = $$var;
                }
            }
        }
        $returnArray['json'] = $jsonarray;
        $returnArray['stats_date'] = $datearray;
        return $returnArray;
    }

    public static function getMaxKey($array)
    {
        $maxkey = 0;
        foreach($array as $json){
            $keys = array_keys($json);
            $numkey = end($keys);
            if($numkey > $maxkey){
                $maxkey = $numkey;
            }
        }
        return $maxkey;
    }

    public static function sortAndFillInArray($jsonarray, $maxkey)
    {
        foreach($jsonarray as $key => $json){
            for($i = 0; $i <= $maxkey; $i++){
                if(!array_key_exists($i, $json)){
                    $json[$i] = '0';
                }
            }
            $json2 = $json;
            ksort($json2);
            $jsonarray2[$key] = $json2;
        }
        return $jsonarray2;
    }

    //-----------Address-------------------//
    //Return ZIP_id from Select or Insert If Not Exists
    public function zipSoi($zip, $state_id = ''){
        $zip = substr($zip,0,5); //Constrain ZIP to first 5 characters
        $sql = "SELECT id FROM zip WHERE zip.zip = :zip";
        $query_params = [
            ':zip' => $zip
        ];
        $zip_id = EDB::query($sql, $query_params, 'fetchColumn');
        if(empty($zip_id)){
            $sql = "INSERT INTO zip (state_id, zip) VALUES (:state_id, :zip) ";
            $query_params = [
                ':state_id' => $state_id,
                ':zip' => $zip
            ];
            $zip_id = EDB::query($sql, $query_params, 'id');
        }
        return $zip_id;
    }

    //Return State_id from Abbreviation
    public function stateId($state_abbr){
        $sql = "SELECT id FROM state WHERE state.abbr = :state";
        $query_params = [
            ':state' => $state_abbr
        ];
        return EDB::query($sql, $query_params, 'fetchColumn');
    }
    //Return State Abbreviation from State name
    public function stateToAbbr($state){
        $sql = "SELECT abbr FROM state WHERE state.name = :state";
        $query_params = [
            ':state' => $state
        ];
        return EDB::query($sql, $query_params, 'fetchColumn');
    }

    //Return City_id from Select or Insert if not Exists
    public function citySoi($city, $state_id){
        $sql = "SELECT id FROM city WHERE city.name = :city AND state_id = :state_id";
        $query_params = [
            ':city' => ucwords(strtolower($city)),
            ':state_id' => $state_id
        ];
        $city_id = EDB::query($sql, $query_params, 'fetchColumn');
        if(empty($city_id)){
            $sql = "INSERT INTO city (state_id, name) VALUES (:state_id, :city)";
            $query_params = [
                ':city' => ucwords(strtolower($city)),
                ':state_id' => $state_id
            ];
            $city_id = EDB::query($sql, $query_params, 'id');
        }
        return $city_id;
    }

    //--------------Product Spec---------------//
    //Return sku_id from Select or Insert if not Exists
    public function skuSoi($sku){
        $sql = "SELECT id FROM sku WHERE sku.sku = :sku";
        $query_params = [
            ':sku' => $sku
        ];
        $sku_id = EDB::query($sql, $query_params, 'fetchColumn');
        if(empty($sku_id) && !empty($sku)){
            $sql = "INSERT INTO sku (sku) VALUES (:sku)";
            $query_params = [
                ':sku' => $sku
            ];
            $sku_id = EDB::query($sql, $query_params, 'id');
        }
        return $sku_id;
    }

    public function getSkuIdFromProductId($product_id){
        $sql = "SELECT id FROM sku WHERE product_id = :product_id";
        $query_params = [
            ':product_id' => $product_id
        ];
        return EDB::query($sql, $query_params, 'fetchColumn');
    }

    //Normalize conditions
    public function normalCondition($condition){
        if($condition == "New"){
            $condition = "New";
        }elseif($condition == "Brand New"){
            $condition = "Brand New";
        }elseif($condition == "Like New" || $condition == "UsedLikeNew"){
            $condition = "Used Like New";
        }elseif($condition == "Very Good" || $condition == "UsedVeryGood"){
            $condition = "UsedVeryGood";
        }elseif($condition == "Good" ||$condition == "UsedGood"){
            $condition = "UsedGood";
        }elseif($condition == "Acceptable" ||$condition == "UsedAcceptable"){
            $condition = "UsedAcceptable";
        }elseif($condition == "Used"){
            $condition = "Used";
        }elseif($condition == "Refurbished"){
            $condition = "Refurbished";
        }
        return $condition;
    }

    //Return condition_id from Select or Insert if not Exists
    public function conditionSoi($condition){
        $sql = "SELECT id FROM sync.condition WHERE condition.condition = :condition";
        $query_params = [
            ':condition' => $condition
        ];
        $condition_id = EDB::query($sql, $query_params, 'fetchColumn');
        if(empty($condition_id)) {
            return $condition;
        }
        return $condition_id;
    }

    //Return stock_id from Select or Insert if not Exists
    public function stockSoi($sku_id, $condition_id = null, $uofm = 1){
        $sql = "SELECT id FROM stock WHERE sku_id = :sku_id";
        $query_params = [
            ':sku_id' => $sku_id
        ];
        $stock_id = EDB::query($sql, $query_params, 'fetchColumn');
        if(empty($stock_id)) {
            //Add sku_id to Stock Table
            $sql = "INSERT INTO stock (sku_id, condition_id, uofm_id) VALUES (:sku_id, :condition_id, :uofm_id)";
            $query_params = [
                ":sku_id" => $sku_id,
                ":condition_id" => $condition_id,
                ":uofm_id" => 1
            ];
            $stock_id = EDB::query($sql, $query_params, 'id');
        }
        return $stock_id;
    }

    //Return sku_id from Product Select or insert if not Exists
    public function productSoiSku($sku, $name, $sub_title, $description, $upc, $weight, $status = ''){
        $sql = "SELECT product.id, product.upc, product.status FROM product JOIN sku ON sku.product_id = product.id WHERE sku.sku = :sku";
        $query_params = [
            ':sku' => $sku
        ];
        $results = EDB::query($sql, $query_params, 'fetch');
        $product_id = $results['id'];
        $upc2 = $results['upc'];
        $active = $results['status'];
        if(empty($product_id)){
            $sql = "INSERT INTO product (product.name, subtitle, description, upc, weight) VALUES (:name, :subtitle, :description, :upc, :weight)";
            $query_params = [
                ':name' => $name,
                ':subtitle' => $sub_title,
                ':description' => $description,
                ':upc' => $upc,
                ':weight' => $weight
            ];
            $product_id = EDB::query($sql, $query_params, 'id');
            $sql = "INSERT INTO sku (product_id, sku) VALUES (:product_id, :sku) ON DUPLICATE KEY UPDATE product_id = :product_id2";
            $query_params = [
                ':product_id' => $product_id,
                ':sku' => $sku,
                ':product_id2' => $product_id
            ];
            $sku_id = EDB::query($sql, $query_params, 'id');
        }elseif(empty($upc2)){
            $sql = "UPDATE product SET upc = :upc WHERE id = :id";
            $query_params = [
                ':upc' => $upc,
                ':id' => $product_id
            ];
            $sku_id = EDB::query($sql, $query_params, 'id');
            echo "$sku's UPC was updated";
        }elseif(empty($active)){
            $sql = "UPDATE product SET status = :status WHERE id = :id";
            $query_params = [
                ':status' => $status,
                ':id' => $product_id
            ];
            EDB::query($sql, $query_params, 'boolean');
            $sku_id = $this->getSkuIdFromProductId($product_id);
        }else{
            $sku_id = $this->skuSoi($sku);
        }
        return $sku_id;
    }

    //Return product_price_id from Product_Price Select or insert if not exists
    public function priceSoi($sku_id, $store_id, $price = null){
        $sql = "SELECT id FROM product_price WHERE sku_id = :sku_id AND store_id = :store_id";
        $query_params = [
            ':sku_id' => $sku_id,
            ':store_id' => $store_id
        ];
        $product_price_id = EDB::query($sql, $query_params, 'fetchColumn');
        if(empty($product_price_id)){
            $sql = "INSERT INTO product_price (sku_id, price, store_id) VALUES (:sku_id, :price, :store_id)";
            $query_params = [
                ':sku_id' => $sku_id,
                ':price' => $price,
                ':store_id' => $store_id
            ];
            $product_price_id = EDB::query($sql, $query_params, 'id');
        }
        return $product_price_id;
    }

    //Update costs based on sku
    public function updatePrices($sku_id, $msrp, $pl1, $map, $pl10, $cost){
        $sql = "INSERT INTO product_cost (sku_id, msrp, pl10, map, pl1, cost) VALUES (:sku_id, :msrp, :pl10, :map, :pl1, :cost) ON DUPLICATE KEY UPDATE msrp = :msrp2, pl10 = :pl102, map = :map2, pl1 = :pl12, cost = :cost2";
        $query_params = [
            ':sku_id' => $sku_id,
            ':msrp' => self::toCents($msrp),
            ':pl1' => self::toCents($pl1),
            ':map' => self::toCents($map),
            ':pl10' => self::toCents($pl10),
            ':cost' => self::toCents($cost),
            ':msrp2' => self::toCents($msrp),
            ':pl12' => self::toCents($pl1),
            ':map2' => self::toCents($map),
            ':pl102' => self::toCents($pl10),
            ':cost2' => self::toCents($cost)
        ];
        return EDB::query($sql, $query_params, 'boolean');
    }

    public function getSKUCosts($sku, $table){
        $table = CHC::sanitize_table_name($table);
        $sql = "SELECT (pc.msrp/100)as msrp, (pc.pl10/100) as pl10, (pc.pl1/100) as pl1, (pc.cost/100) as cost, lt.override_price, lt.title, p.upc FROM product_cost pc JOIN sku sk ON sk.id = pc.sku_id JOIN product p ON p.id = sk.product_id JOIN $table lt ON lt.sku = sk.sku WHERE sk.sku = :sku";
        $query_params = [
            ':sku' => $sku
        ];
        return EDB::query($sql, $query_params, 'fetch', PDO::FETCH_ASSOC);
    }

    public function getSalesHistory($sku_id)
    {
        /*
         *
         *  COUNT(o.order_num) as orders,
         *  ROUND(SUM(o.shipping_amount), 2) as shipping,
         *  ROUND(SUM(o.taxes), 2) as taxes
         */
        $sql = "SELECT
          os.type AS channel,
          (ROUND(SUM(quantity * price), 2) + ROUND(SUM(o.shipping_amount), 2)) as sales,
          SUM(oi.quantity) as unitsSold,
          DATE_FORMAT(date, '%Y-%m') as date
        FROM order_item oi
          LEFT OUTER JOIN `order` o ON o.id = oi.order_id
          LEFT OUTER JOIN order_sync os ON os.order_id = o.order_num
        WHERE oi.sku_id = :sku_id
        GROUP BY channel, DATE_FORMAT(date, '%Y-%m')
        ORDER BY date DESC
        ";
        $query_params = [
            ':sku_id' => $sku_id
        ];
        return EDB::query($sql, $query_params, 'fetchAll', PDO::FETCH_ASSOC);
    }

    public function formatChannelRecentSales($ebayRecentSales)
    {
        $items = $ebayRecentSales->searchResult;
        foreach ($items->item as $item){
            static::dd($item);
            $soldDate = self::createFormattedDate($item->listingInfo->endTime, 'Y-m-d');
            $url = $item->viewItemURL;
        }
    }

    public function getUpsideDownCost(){
        $sql = "SELECT sk.sku, (pc.pl10/100) as pl10, (pc.pl1/100) as pl1, (pc.cost/100) as cost FROM sku sk LEFT JOIN product_cost pc ON sk.id = pc.sku_id WHERE pc.pl10 < pc.pl1";
        return EDB::query($sql, [], 'fetchAll');
    }

    //Update price based on sku
    public function updateSKUPrice($sku, $price, $table){
        $table = CHC::sanitize_table_name($table);
        $sql = "UPDATE $table SET price = :price WHERE sku = :sku";
        $query_params = [
            ':price' => $price,
            ':sku' => $sku
        ];
        return EDB::query($sql, $query_params, 'boolean');
    }

    //Update Override on price
    public function updateSKUOverride($sku, $override, $table){
        $table = CHC::sanitize_table_name($table);
        $sql = "UPDATE $table SET override_price = :override_price WHERE sku = :sku";
        $query_params = [
            ':override_price' => $override,
            ':sku' => $sku
        ];
        return EDB::query($sql, $query_params, 'boolean');
    }

    //Update photo_url
    public function updateSKUPhoto($sku, $photo_url, $table){
        $table = CHC::sanitize_table_name($table);
        $sql = "UPDATE $table SET photo_url = :photo_url WHERE sku = :sku";
        $query_params = [
            ':photo_url' => $photo_url,
            ':sku' => $sku
        ];
        return EDB::query($sql, $query_params, 'boolean');
    }

    //Append to Description
    public function appendSKUDescription($sku, $description, $table){
        $table = CHC::sanitize_table_name($table);
        $sql = "UPDATE $table SET description = CONCAT(description, ' ', :description) WHERE sku = :sku";
        $query_params = [
            ':description' => $description,
            ':sku' => $sku
        ];
        return EDB::query($sql, $query_params, 'boolean');
    }

    //Get Description
    public function getSKUDescription($sku, $table){
        $table = CHC::sanitize_table_name($table);
        $sql = "SELECT description FROM $table WHERE sku = :sku";
        $query_params = [
            ':sku' => $sku
        ];
        return EDB::query($sql, $query_params, 'fetchColumn');
    }

    public function getCategoryId($category_name, $table){
        $table = CHC::sanitize_table_name($table);
        $sql = "SELECT id FROM $table WHERE category_name = :category_name";
        $query_params = [
            ':category_name' => $category_name
        ];
        return EDB::query($sql, $query_params, 'fetchColumn');
    }

    public function getCategoryToMap($category_id = null){
        if(empty($category_id)) {
            $sql = "SELECT cm.id as id, cm.categories_ebay_id, cm.categories_amazon_id, cm.categories_bigcommerce_id, ca.category_name AS am_cat_name, ce.category_name AS eb_cat_name, cb.category_name AS bc_cat_name FROM categories_mapped cm LEFT JOIN categories_amazon ca ON cm.categories_amazon_id = ca.category_id LEFT JOIN categories_ebay ce ON cm.categories_ebay_id = ce.category_id LEFT JOIN categories_bigcommerce cb ON cm.categories_bigcommerce_id = cb.category_id ORDER BY categories_ebay_id";
            $query_params = [];
        }else{
            $sql = "SELECT categories_amazon_id AS id FROM categories_mapped WHERE categories_ebay_id = :cat";
            $query_params = [
                ':cat' => $category_id
            ];
        }
        return EDB::query($sql, $query_params, 'fetchAll');
    }

    public function getParentCategories($table){
        $table = CHC::sanitize_table_name($table);
        $sql = "SELECT category_id, parent_category_id, category_name FROM $table WHERE category_id = parent_category_id ORDER BY parent_category_id ASC";
        return EDB::query($sql, [], 'fetchAll');
    }

    public function getChildCategories($table){
        $table = CHC::sanitize_table_name($table);
        $sql = "SELECT category_id, parent_category_id, category_name FROM $table WHERE category_id != parent_category_id ORDER BY parent_category_id ASC";
        return EDB::query($sql, [], 'fetchAll');
    }

    public function getCategory($cat_id){
        $sql = "SELECT category_id, parent_category_id, category_name FROM categories_ebay WHERE category_id LIKE :cat_id ORDER BY parent_category_id ASC";
        $query_params = [
            ':cat_id' => "%" . $cat_id . "%"
        ];
        return EDB::query($sql, $query_params, 'fetchAll');
    }
    public function getCategoryFeeOfSKU($table, $table2, $sku){
        $table = CHC::sanitize_table_name($table);
        $table2 = CHC::sanitize_table_name($table2);
        $sql = "SELECT category_fee FROM $table cat LEFT JOIN $table2 list ON cat.category_id = list.primary_category WHERE list.sku = :sku";
        $query_params = [
            ':sku' => $sku
        ];
        return EDB::query($sql, $query_params, 'fetchColumn');
    }

    public function get_all_sub_categories($parent_category, $table){
        $table = CHC::sanitize_table_name($table);
        $sql = "SELECT category_id, parent_category_id, category_name FROM $table WHERE parent_category_id = :parent_category_id ORDER BY category_id ASC";
        $query_params = [
            ':parent_category' => $parent_category
        ];
        return EDB::query($sql, $query_params, 'fetchAll');
    }

    public function save_category($category_id, $category_name, $category_parent_id, $table){
        $table = CHC::sanitize_table_name($table);
        $sql = "INSERT INTO $table (category_id, parent_category_id, category_name) VALUES (:category_id, :parent_category_id, :category_name) ON DUPLICATE KEY UPDATE category_name = :category_name2";
        $query_params = [
            ":category_id" => $category_id,
            ":parent_category_id" => $category_parent_id,
            ":category_name" => $category_name,
            ':category_name2' => $category_name
        ];
        return EDB::query($sql, $query_params, 'id');
    }

    public function get_category_fee($category_id){
        $sql = "SELECT category_fee FROM categories_ebay WHERE category_id = :category_id";
        $query_params = [
            ':category_id' => $category_id
        ];
        return EDB::query($sql, $query_params, 'fetchColumn');
    }

    public function save_category_fee($category_id, $fee){
        $sql = "UPDATE categories_ebay SET category_fee = :fee WHERE category_id = :category_id";
        $query_params = [
            ':fee' => $fee,
            ':category_id' => $category_id
        ];
        return EDB::query($sql, $query_params, 'boolean');
    }

    //Update mapped category
    public function update_mapped_category($id, $category_id, $column){
        $column = CHC::sanitize_table_name($column);
        $sql = "UPDATE categories_mapped SET $column = :category_id WHERE id = :id";
        $query_params = [
            ':category_id' => $category_id,
            ':id' => $id
        ];
        return EDB::query($sql, $query_params, 'boolean');
    }

    //Update product category
    public function update_category($sku, $category_id, $table){
        $table = CHC::sanitize_table_name($table);
        $sql = "UPDATE $table SET category_id = :category_id WHERE sku = :sku";
        $query_params = [
            ':category_id' => $category_id,
            ':sku' => $sku
        ];
        return EDB::query($sql, $query_params, 'boolean');
    }

    //Return product_id from Product Select or insert if not Exists
    public function product_soi($sku, $name, $sub_title, $description, $upc, $weight)
    {
        $sql = "SELECT product.id FROM product JOIN sku ON sku.product_id = product.id WHERE sku.sku = :sku";
        $query_params = [
            ':sku' => $sku
        ];
        $product_id = EDB::query($sql, $query_params, 'fetchColumn');
        if(empty($product_id)){
            $sql = "INSERT INTO product (product.name, subtitle, description, upc, weight) VALUES (:name, :subtitle, :description, :upc, :weight)";
            $query_params = [
                ':name' => $name,
                ':subtitle' => $sub_title,
                ':description' => $description,
                ':upc' => $upc,
                ':weight' => $weight
            ];
            $product_id = EDB::query($sql, $query_params, 'id');
            $sql = "INSERT INTO sku (product_id, sku) VALUES (:product_id, :sku) ON DUPLICATE KEY UPDATE product_id = :product_id2";
            $query_params = [
                ':product_id' => $product_id,
                ':product_id2' => $product_id,
                ':sku' => $sku
            ];
            EDB::query($sql, $query_params, 'boolean');
        }
        return $product_id;
    }

    //Make product available on store's channel
    public function availability_soi($product_id, $store_id)
    {
        $sql = "SELECT id FROM product_availability WHERE product_id = :product_id AND store_id = :store_id AND is_available = '1'";
        $query_params = [
            ':product_id' => $product_id,
            ':store_id' => $store_id
        ];
        $availability_id = EDB::query($sql, $query_params, 'fetchColumn');
        if(empty($availability_id)) {
            $sql = "INSERT INTO product_availability (product_id, store_id, is_available) VALUES (:product_id, :store_id, 1)";
            $query_params = [
                ":product_id" => $product_id,
                ":store_id" => $store_id
            ];
            $availability_id = EDB::query($sql, $query_params, 'id');
        }
        return $availability_id;
    }

    //Get Inventory updated in last two hours
    public function get_updated_inventory($table)
    {
        $table = CHC::sanitize_table_name($table);
        $sql = "SELECT tb.sku, tb.inventory_level AS qty FROM $table tb WHERE tb.last_edited >= DATE_SUB(NOW(), INTERVAL 45 MINUTE)";
        return EDB::query($sql, [], 'fetchAll');
    }

    public function get_inventory_prices($hours = null)
    {
        $sql = "SELECT sk.sku, (pc.msrp/100) as msrp, (pc.pl10/100) as pl10, (pc.map/100) as map, (pc.pl1/100) as pl1, (pc.cost/100) as cost FROM product_cost pc LEFT OUTER JOIN sku sk ON sk.id = pc.sku_id";
        if($hours) {
            $sql .= " WHERE pc.last_edited >= DATE_SUB(NOW(), INTERVAL $hours HOUR)";
        }
        return EDB::query($sql, [], 'fetchAll', PDO::FETCH_GROUP|PDO::FETCH_UNIQUE|PDO::FETCH_ASSOC);
    }

    public function get_inventory_for_update($table, $sku = null)
    {
        $table = CHC::sanitize_table_name($table);
        if(empty($sku)) {
            $sql = "SELECT st.id, st.sku_id, tb.inventory_level AS stock_qty";
            if($table === 'listing_amazon'){
                $sql .= ",tb.asin1";
            }
            $sql .= ", sk.sku FROM stock st JOIN $table tb ON tb.stock_id = st.id LEFT OUTER JOIN sku sk on sk.id = st.sku_id"; //WHERE tb.last_edited >= DATE_SUB(NOW(), INTERVAL 2 HOUR)
            return EDB::query($sql, [], 'fetchAll');
        }else{
            $sql = "SELECT st.id, st.sku_id, tb.inventory_level AS stock_qty FROM stock st JOIN $table tb ON tb.stock_id = st.id WHERE tb.sku = :sku";
            $query_params = [
                ':sku' => $sku
            ];
            return EDB::query($sql, $query_params, 'fetch');
        }
    }

    //Get Inventory for bi-monthly dump
    public function get_inventory_weekly($table)
    {
        $table = CHC::sanitize_table_name($table);
        $sql = "SELECT st.id, st.sku_id, tb.inventory_level AS stock_qty FROM stock st JOIN $table tb ON tb.stock_id = st.id";
        return EDB::query($sql, [], 'fetchAll');
    }

    public function get_inventory_price($sku, $table)
    {
        $table = CHC::sanitize_table_name($table);
        $sql = "SELECT price FROM $table WHERE sku = :sku AND override_price = 0";
        $query_params = [
            'sku' => $sku
        ];
        return EDB::query($sql, $query_params, 'fetchColumn');
    }

    public function get_sku($sku_id)
    {
        $sql = "SELECT sku.sku FROM sku WHERE id = :sku_id";
        $query_params = [
            'sku_id' => $sku_id
        ];
        return EDB::query($sql, $query_params, 'fetchColumn');
    }

    public function get_sku_id($sku)
    {
        $sql = "SELECT id FROM sku WHERE sku.sku = :sku";
        $query_params = [
            ':sku' => $sku
        ];
        return EDB::query($sql, $query_params, 'fetchColumn');
    }

    public function find_product($sku)
    {
        $sql = "SELECT * FROM product p JOIN sku sk ON p.id = sk.product_id JOIN stock st ON st.sku_id = sk.id WHERE sk.sku = :sku";
        $query_params = [
            ':sku' => $sku
        ];
        return EDB::query($sql, $query_params, 'fetch');
    }

    //--------------End of Product Spec---------------//

    public function analyze_sales($sku){
        if(empty($sku)){
            $sql = "SELECT sk.sku, c.name, o.date, oi.price, o.shipping_amount, oi.quantity, p.price AS current_price, o.id FROM order_item oi JOIN sync.order o ON o.id = oi.order_id JOIN store s ON s.id = o.store_id JOIN channel c ON c.id = s.channel_id JOIN sku sk ON sk.id = oi.sku_id JOIN (SELECT p.sku_id, p.price FROM product_price p GROUP BY p.sku_id) p ON p.sku_id = sk.id WHERE sk.sku <> '' AND c.name = 'Ebay' ORDER BY sk.sku, o.date ASC";
            return EDB::query($sql, [], 'fetchAll');
        }
    }

    public function get_products_from_all_channels($sku = null){ //, $offset, $limit
        if(empty($sku)){
            $sql = "SELECT a.sku, a.asin1 AS am_list, b.store_listing_id AS bc_list, e.store_listing_id AS eb_list, r.store_listing_id AS rev_list FROM sync.listing_amazon a LEFT JOIN listing_bigcommerce b ON b.sku = a.sku LEFT JOIN listing_ebay e ON e.sku = a.sku LEFT JOIN listing_reverb r ON r.sku = a.sku ORDER BY sku ASC"; // LIMIT $offset, $limit
            return EDB::query($sql, [], 'fetchAll');
        }
    }

    public function get_product_info_from_channel($sku, $table){
        $table = CHC::sanitize_table_name($table);
        $sql = "SELECT * FROM $table WHERE sku = :sku";
        $query_params = [
            ':sku' => $sku
        ];
        return EDB::query($sql, $query_params, 'fetch');
    }

    public function get_amazon_products($offset, $limit){
        $sql = "SELECT a.sku, a.asin1 AS am_list FROM sync.listing_amazon a ORDER BY sku ASC LIMIT $offset, $limit";
        return EDB::query($sql, [], 'fetchAll');
    }

    //Get listing ID by stock_id
    public function get_listing_id($stock_id, $table){
        $table_col = CHC::sanitize_table_name($table);
        $sql = "SELECT store_listing_id FROM $table_col WHERE stock_id = :stock_id";
        $query_params = [
            ':stock_id' => $stock_id
        ];
        return EDB::query($sql, $query_params, 'fetchColumn');
    }

    public function get_listing_id_by_sku($sku, $table){
        $table_col = CHC::sanitize_table_name($table);
        $sql = "SELECT store_listing_id FROM $table_col WHERE stock_id = :stock_id";
        $query_params = [
            ':sku' => $sku
        ];
        return EDB::query($sql, $query_params, 'fetchColumn');
    }


    //Prepare channel listings into arrays for manipulation
    public function prepare_arrays($channel_array){
        $columns = '';
        $values = '';
        $update_string = '';
        $prepared_array = [];
        $return_array = [];
        foreach($channel_array as $key => $val){
            $columns .= $key;
            $values .= ":" . $key;
            $update_string .= $key . "=:" . $key . '2';
            end($channel_array);
            if(key($channel_array) !== $key){
                $columns .= ',';
                $values .= ',';
                $update_string .= ',';
            }
            $prepared_array[':' . $key] = $val;
            $prepared_array[':' . $key . '2'] = $val;
        }
        $return_array[0] = $columns;
        $return_array[1] = $values;
        $return_array[2] = $update_string;
        $return_array[3] = $prepared_array;
        return $return_array;
    }

    //Return listing_id from Select or Insert if not Exists, Update if it does
    public function listing_soi($table, $store_id, $stock_id, $channel_array, $update = false)
    {
        $table = CHC::sanitize_table_name($table);
        $sql = "SELECT id FROM $table WHERE stock_id = :stock_id AND store_id = :store_id";
        $query_params = [
            ':stock_id' => $stock_id,
            ':store_id' => $store_id
        ];
        $listing_id = EDB::query($sql, $query_params, 'fetchColumn');
        if($update) {
            $return_array = $this->prepare_arrays($channel_array);
            $columns = $return_array[0];
            $values = $return_array[1];
            $update_string = $return_array[2];
            $query_params = $return_array[3];

            $sql = "INSERT INTO $table ($columns) VALUES ($values) ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id),$update_string"; //
            $listing_id = EDB::query($sql, $query_params, 'id');
        }
        return $listing_id;
    }

    public function update_shipping_amount($order, $shipping_amount){
        $sql = "UPDATE sync.order SET shipping_amount = :shipping_amount WHERE order_num = :order";
        $query_params = [
            ':shipping_amount' => $shipping_amount,
            ':order' => $order
        ];
        return EDB::query($sql, $query_params, 'boolean');
    }

    public function update_item_qty($order, $sku, $quantity){
        $sql = "UPDATE order_item oi JOIN sync.order o ON o.id = oi.order_id JOIN sku sk ON sk.id = oi.sku_id SET oi.quantity = :quantity WHERE o.order_num = :order AND sk.sku = :sku";
        $query_params = [
            ':quantity' => $quantity,
            ':order' => $order,
            ':sku' => $sku
        ];
        return EDB::query($sql, $query_params, 'boolean');
    }

    //Save order from channels to DB
    public function save_order($store_id, $cust_id, $order_num, $ship_method, $shipping_amount, $tax_amount = 0, $fee = 0, $trans_id = null){
        $sql = "SELECT id FROM sync.order WHERE store_id = :store_id AND order_num = :order_num";
        $query_params = [
            ':store_id' => $store_id,
            ':order_num' => $order_num
        ];
        $order_id = EDB::query($sql, $query_params, 'fetchColumn');
        if(empty($order_id)) {
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
            $order_id = EDB::query($sql, $query_params, 'id');
        }
        return $order_id;
    }
    public function save_taxes($order_id, $taxes){
        $sql = "UPDATE sync.order SET taxes = :taxes WHERE id = :id";
        $query_params = [
            ":taxes" => $taxes,
            ":id" => $order_id
        ];
        return EDB::query($sql, $query_params, 'id');
    }

    public function updateOrderShippingAndTaxes($order_id, $shipping, $taxes)
    {
        $sql = "UPDATE sync.order SET shipping_amount = :shipping, taxes = :taxes WHERE id = :id";
        $query_params = [
            ':shipping' => $shipping,
            ':taxes' => $taxes,
            ':id' => $order_id
        ];
        return EDB::query($sql, $query_params, 'id');
    }
    //Save order items from channel orders to DB
    public function save_order_items($order_id, $sku_id, $price, $quantity, $item_id = ''){
        $sql = "INSERT INTO order_item (order_id, sku_id, price, item_id, quantity) VALUES (:order_id, :sku_id, :price, :item_id, :quantity)";
        $query_params = [
            ':order_id' => $order_id,
            ':sku_id' => $sku_id,
            ':price' => $price,
            ':item_id' => $item_id,
            ':quantity' => $quantity
        ];
        return EDB::query($sql, $query_params, 'boolean');
    }

    //Return cust_id from Select or Insert if not Exists
    public function customer_soi($first_name, $last_name, $street_address, $street_address2, $city_id, $state_id, $zip_id){
        $sql = "SELECT id FROM customer WHERE first_name = :first_name AND last_name = :last_name AND street_address = :street_address AND zip_id = :zip_id";
        $query_params = [
            ':first_name' => $first_name,
            ':last_name' => $last_name,
            ':street_address' => $street_address,
            ':zip_id' => $zip_id
        ];
        $cust_id = EDB::query($sql, $query_params, 'fetchColumn');
        if(empty($cust_id)) {
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
            $cust_id = EDB::query($sql, $query_params, 'id');
        }
        return $cust_id;
    }

    public function get_current_inventory($table){
        $table = CHC::sanitize_table_name($table);
        $sql = "SELECT sku, inventory_level FROM $table";
        return EDB::query($sql, [], 'fetchAll', PDO::FETCH_GROUP|PDO::FETCH_UNIQUE|PDO::FETCH_ASSOC);
    }

    public function update_inventory($sku, $qty, $price, $table){
        $table = CHC::sanitize_table_name($table);
        if(!empty($price)) {
            $sql = "UPDATE $table tb SET tb.inventory_level = :qty, tb.price = :price WHERE tb.sku = :item";
            $query_params = [
                ":qty" => $qty,
                ":price" => $price,
                ":item" => $sku
            ];
        }else{
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
        return EDB::query($sql, $query_params, 'boolean');
    }

    public function sync_inventory_from($fromtable, $totable){
        $fromtable = CHC::sanitize_table_name($fromtable);
        $totable = CHC::sanitize_table_name($totable);
        $sql = "SELECT la.title, la.description, p.upc, sk.sku, la.inventory_level AS quantity, la.price, la.category_id, p.weight FROM sync.product p JOIN sku sk ON sk.product_id = p.id JOIN $fromtable la ON la.sku = sk.sku LEFT OUTER JOIN $totable le ON le.sku = la.sku WHERE p.upc <> '' AND le.sku IS NULL";
        return EDB::query($sql, [], 'fetchAll');
    }

    public function get_mapped_category($fromcolumn, $tocolumn, $category_id){
        $fromcolumn = CHC::sanitize_table_name($fromcolumn);
        $tocolumn = CHC::sanitize_table_name($tocolumn);
        $sql = "SELECT $tocolumn FROM categories_mapped WHERE $fromcolumn = :category_id";
        $query_params = [
            ':category_id' => $category_id
        ];
        return EDB::query($sql, $query_params, 'fetchColumn');
    }

    //Find if order has been downloaded to VAI
    public function findDownloadedVaiOrder($order_id){
        $sql = "SELECT * FROM order_sync WHERE order_id = :order_id AND success = 1";
        $query_params = [
            ':order_id' => $order_id
        ];
        return EDB::query($sql, $query_params, 'rowCount');
    }

    public function orderExists($orderNum)
    {
        if (!empty($orderNum))
        {
            $number = $this->findDownloadedVaiOrder($orderNum);

            if ($number > 0)
            {
                static::dd("Found in database");
                return true;
            }
        }
        return false;
    }

    //Create order for download to VAI to allow for XML creation
    public function insertOrder($order_id, $success = 1, $type = 'Amazon'){
        $sql = "INSERT INTO order_sync (order_id, success, type) VALUES (:order_id, :success, :type)";
        $query_params = [
            ":order_id" => $order_id,
            ":success" => $success,
            ":type" => $type
        ];
        return EDB::query($sql, $query_params, 'boolean');
    }
    //Get Channel Account #'s
    public function get_acct_num($channel){
        $sql = "SELECT co_one_acct, co_two_acct FROM channel WHERE channel.name = :name";
        $query_params = [
            ':name' => $channel
        ];
        return EDB::query($sql, $query_params, 'fetch');
    }
    //Create order XML for download to VAI
    public function create_xml($channel_num, $channel_name, $order_id, $timestamp, $shipping_amount, $shipping, $order_date, $buyer_phone, $ship_to_name, $address, $address2, $city, $state, $zip, $country, $item_xml){
        $xml = <<<EOD
        <NAMM_PO version="2007.1">
            <Id>S2S{$channel_num}_PO$order_id</Id>
            <Timestamp>$timestamp</Timestamp>
            <BuyerId>$channel_num</BuyerId>
            <BuyerIdDesc>My Music Life $channel_name</BuyerIdDesc>
            <PO>$order_id</PO>
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
    public function create_item_xml($sku, $title, $ponumber, $quantity, $principle, $upc){
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
    public function create_tax_item_xml($poNumber, $totalTax, $state, $stateTaxItemName = ''){
        $itemName = '';
        if(!empty($stateTaxItemName)){
            $itemName = $stateTaxItemName;
        }else {
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
    //Save created XML file to FTP folder to allow VAI to download
    public function save_xml_to_hd($order_id, $xml, $type){
        $folder = '/home/chesbro_amazon/';
        $log_file_name = date('ymd') . '.txt';
        $filename = $order_id . '.xml';
        $fp = fopen($folder . 'log/' . $log_file_name, 'a+');
        fwrite($fp, "\r\nRunning Script ** ** " . date('m/d/y h:i:s'));
        fwrite($fp, "\r\nOrder to process: " . count($order_id));
        fwrite($fp, "\r\nOrder filename: " . print_r($filename, true));
        fwrite($fp, "\r\nXML:\r\n " . preg_replace('/\s+/', '', $xml));
        file_put_contents($folder . $filename, $xml);
        chmod($folder . $filename, 0777);
        file_put_contents($folder . 'backup/' . $filename, $xml);
        chmod($folder . 'backup/' . $filename, 0777);
        if (file_exists($folder . 'backup/' . $filename)) {
            fwrite($fp, "\r\nBackup Order successfully written: " . print_r($filename, true));
        } else {
            fwrite($fp, "\r\n-----------------Backup Order NOT written: " . print_r($filename, true) . '-----------------');
        }
        if (file_exists($folder . $filename)) {
            fwrite($fp, "\r\nOrder successfully written: " . print_r($filename, true));
        } else {
            fwrite($fp, "\r\n-----------------Order NOT written: " . print_r($filename, true) . '-----------------');
        }
        $results = $this->insertOrder($order_id, 1, $type);
        fwrite($fp, "\r\nOrder Processed:" . print_r(1, true));
        $filename = 'log/lastrun.log';
        file_put_contents($folder . $filename, date('m/d/y h:i:s') . ' -- ' . $order_id);
        chmod($folder . $filename, 0777);
        fclose($fp);
    }
    public function substring_between($haystack,$start,$end)
    {
        if (stripos($haystack,$start) === false || stripos($haystack,$end) === false) {
            return false;
        }
        else {
            $start_position = stripos($haystack,$start)+strlen($start);
            $end_position = stripos($haystack,$end,$start_position);
            return substr($haystack,$start_position,$end_position-$start_position);
        }
    }
    public function curl($url){
        $options = [
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_FOLLOWLOCATION => TRUE,
            CURLOPT_AUTOREFERER => TRUE,
            CURLOPT_CONNECTTIMEOUT => 120,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_USERAGENT => "Mozilla/5.0 (Windows; U; MSIE 9.0; Windows NT 9.0; en-US)",
            CURLOPT_URL => $url,
            CURLOPT_SSL_VERIFYPEER => FALSE
        ];
        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
    public function clean_sku($sku){
        if(strpos($sku, ';') > 0){
            $sku = substr($sku, 0, strpos($sku, ';'));
        }else if(strpos($sku, ',') > 0){
            $sku = substr($sku, 0, strpos($sku, ','));
        }
        return $sku;
    }

    //Look for this in cronorderseb.php and other channels. Currently only in cronordersam.php
    public function get_channel_num($ibmdata, $channel_name, $sku){
        $accounts = $this->get_acct_num($channel_name);
        $co_one_acct = $accounts['co_one_acct'];
        $co_two_acct = $accounts['co_two_acct'];
        $inventory = $ibmdata->find_inventory($sku, $channel_name);
        $co_one_qty = $inventory['CO_ONE'];
        $co_two_qty = $inventory['CO_TWO'];
        if(!empty($co_one_qty)){
            $channel_num = $co_one_acct;
        }elseif (!empty($co_two_qty)){
            $channel_num = $co_two_acct;
        }else{
            $channel_num = $co_one_acct;
        }
        return $channel_num;
    }
    public function get_tax_item_xml($state_code, $poNumber, $totalTax, $stateTaxItemName = ''){
        $itemXml = '';
        if(!empty($stateTaxItemName)){
            $itemXml .= $this->create_tax_item_xml($poNumber, $totalTax, '', $stateTaxItemName);
        }else {
            if (strtolower($state_code) == 'id' || strtolower($state_code) == 'idaho') {
                $itemXml .= $this->create_tax_item_xml($poNumber, number_format($totalTax, 2), 'ID');
            } elseif (strtolower($state_code) == 'ca' || strtolower($state_code) == 'california') {
                $itemXml .= $this->create_tax_item_xml($poNumber, number_format($totalTax, 2), 'CA');
            } elseif (strtolower($state_code) == 'wa' || strtolower($state_code) == 'washington') {
                $itemXml .= $this->create_tax_item_xml($poNumber, number_format($totalTax, 2), 'WA');
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
        return EDB::query($sql, $query_params, 'fetchAll', PDO::FETCH_UNIQUE|PDO::FETCH_ASSOC);
    }

    public function taxableState($stateArray, $state)
    {
        $taxable = false;
        foreach($stateArray as $s => $value){
            if($s == $state){
                $taxable = true;
            }
        }
        return $taxable;
    }

    public function calculateTax($stateTaxArray, $totalWithoutTax, $totalShipping)
    {
        $taxRate = $stateTaxArray['tax_rate']/100;
        $totalTax = number_format($totalWithoutTax * $taxRate, 2);
        if($stateTaxArray['shipping_taxed']){
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
        if(curl_errno($request)){
            curl_close ($request);
            return 'Error: ' . curl_error($request);
        }
        curl_close ($request);
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
        if(!empty($param)){
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
        if($parameters){
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
        foreach ($xml as $key => $value){
            if(is_array($value)){
                if(is_numeric($key)){
                    $generatedXML .= self::generateXML($value, $pkey, $key);
//                    $generatedXML .= self::openXMLParentTag($pkey);
//                    $generatedXML .= self::makeXML($value, $key);
//                    $generatedXML .= self::closeXMLParentTag($pkey);
                }else {
                    $pkey = $key;
                    if(array_key_exists(0, $value)){
                        $generatedXML .= self::makeXML($value, $pkey);
                    }else{
                        $generatedXML .= self::generateXML($value, $key, $pkey);
//                        $generatedXML .= self::openXMLParentTag($key);
//                        $generatedXML .= self::makeXML($value, $pkey);
//                        $generatedXML .= self::closeXMLParentTag($key);
                    }
                }
            }else{
                $parameters = null;
                $delimiter = '~';
                if(strpos($key, $delimiter) !== false) {
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
        if(isset($address['state'])){
            if(
                stripos($address['address2'], '1850 Airport') &&
                stripos($address['city'], 'Erlanger') &&
                stripos($address['state'], 'KY') &&
                stripos($address['zip'], '41025')
            ){
                $shipping = 'UPIP';
            }
        }
        return $shipping;
    }

    protected static function determineShippingCode($shipping, $shipmentMethod)
    {
        if($shipmentMethod){
            switch(strtolower($shipmentMethod)) {
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
        if($total >= 250){
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
        if(file_exists($folder . $filename))
        {
            echo "Successfully uploaded $filename<br />";
            $results = $this->insertOrder($orderNum,1,$channel);
            if($results){
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
        if(isset($value['format'])){
            if($value['format'] !== 'aboveZero') {
                $format .= "class='{$value['format']}'";
            }else{
                if($value['value'] < 0){
                    $class = "loss";
                }else{
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
        if($cellType == 'th'){
            $cell .= ucfirst($value);
        }else{
            if(!is_array($value)) {
                $cell .= $value;
            }else{
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
        foreach($array as $key => $value){
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
        foreach($array as $a) {
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
        foreach($sellers as $key => $row){
            $priceArray[$key] = $row[$sortBy];
        }
        array_multisort($priceArray, SORT_ASC, $sellers);

        return $sellers;
    }

    public static function toDollars($cents)
    {
        $dollars = $cents/100;
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

    public function removeCommasInNumber($number)
    {
        $number = number_format($number, '2', '.', '');
        return $number;
    }

    public static function getChannelListingsFromDB($channel)
    {
        $sql = "SELECT sku, store_listing_id as id FROM listing_$channel";
        return EDB::query($sql, [], 'fetchAll', PDO::FETCH_GROUP|PDO::FETCH_UNIQUE|PDO::FETCH_ASSOC);
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
        $price = \ecommerce\Ecommerce::formatMoney($item['price']);
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
        if($carrier == 'USPS'){
            $tracking_url = 'https://tools.usps.com/go/TrackConfirmAction?qtc_tLabels1=' . $tracking_num;
        }elseif($carrier == 'FedEx'){
            $tracking_url = 'https://www.fedex.com/apps/fedextrack/?tracknumbers=' . $tracking_num . '&language=en&cntry_code=us';
        }elseif($carrier == 'UPS'){
            $tracking_url = 'https://wwwapps.ups.com/WebTracking/track?track=yes&trackNums=' . $tracking_num . '&loc=en_us';
        }
        $date_processed = self::createFormattedDate($oi['date'], 'm/d/Y H:i:s');
        $status = 'Unshipped';
        if($track_successful == '1'){
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