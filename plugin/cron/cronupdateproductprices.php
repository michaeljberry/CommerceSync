<?php
require __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';

$start_time = microtime(true);

$count = $ibmdata->get_count();
echo $count . '<br>';

$currentPrices = $ecommerce->get_inventory_prices();

for ($low = 0; $low < $count; $low += 500) {
    $high = $low + 500;
    $vaidata = $ibmdata->sync_vai_price($low, $high);
    foreach ($vaidata as $v) {
        $sku = trim($v['J6ITEM']);
        $msrp = $ecommerce->removeCommasInNumber($v['J6LPRC']);
        $pl1 = $ecommerce->removeCommasInNumber($v['J6PL01']);
        $map = $ecommerce->removeCommasInNumber($v['J6PL09']);
        $pl10 = $ecommerce->removeCommasInNumber($v['J6PL10']);
        $cost = $ecommerce->removeCommasInNumber($v['FIFOCOST']);

        if (array_key_exists($sku, $currentPrices)) {
            if (
                $ecommerce->removeCommasInNumber($currentPrices[$sku]['msrp']) !== $msrp ||
                $ecommerce->removeCommasInNumber($currentPrices[$sku]['pl1']) !== $pl1 ||
                $ecommerce->removeCommasInNumber($currentPrices[$sku]['map']) !== $map ||
                $ecommerce->removeCommasInNumber($currentPrices[$sku]['pl10']) !== $pl10 ||
                $ecommerce->removeCommasInNumber($currentPrices[$sku]['cost']) !== $cost
            ) {
                $sku_id = $ecommerce->skuSoi($sku);
                $result = $ecommerce->updatePrices($sku_id, $msrp, $pl1, $map, $pl10, $cost);
                if($result){
                    echo "<br>$sku is updated<br>";
                    echo "<br><br><br>";
                }
            }
        }
    }
}

$end_time = microtime(true);
$execution_time = ($end_time - $start_time)/60;
$execution = "Execution time: $execution_time mins";
echo $execution;
echo "DateTime: " . date('Y-m-d H:i:s') . "<br>";