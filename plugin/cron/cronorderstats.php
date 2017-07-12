<?php
error_reporting(-1);
require __DIR__ . '/../../core/init.php';

use models\channels\order\OrderStats;

$start = startClock();

$results = OrderStats::getWeek();
print_r($results);
foreach ($results as $r) {
    $date = $r['date'];
    $sales = $r['sales'];
    $units_sold = $r['units_sold'];
    $channel = $r['channel'];

    $stat_id = OrderStats::save($channel, $date, $sales, $units_sold);
    if (!empty($stat_id)) {
        echo "Date: $date; Channel: $channel; Sales: $sales, Units Sold: $units_sold<br>";
    }
}

endClock($start);