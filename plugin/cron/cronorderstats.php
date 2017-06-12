<?php
error_reporting(-1);
require __DIR__ . '/../../core/init.php';
$start_time = microtime(true);

$results = $ecommerce->getOrderStatsWeek();
print_r($results);
foreach($results as $r){
    $date = $r['date'];
    $sales = $r['sales'];
    $units_sold = $r['units_sold'];
    $channel = $r['channel'];

    $stat_id = $ecommerce->saveOrderStats($channel, $date, $sales, $units_sold);
    if(!empty($stat_id)){
        echo "Date: $date; Channel: $channel; Sales: $sales, Units Sold: $units_sold<br>";
    }
}

$end_time = microtime(true);
$execution_time = ($end_time - $start_time)/60;
echo "Execution time: $execution_time mins";