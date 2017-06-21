<?php
require __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';

use ecommerce\Ecommerce as ecom;

$start = startClock();

$count = $ibmdata->get_count();
echo $count . '<br>';

$currentPrices = ecom::get_inventory_prices();

for ($low = 0; $low < $count; $low += 500) {
    $high = $low + 500;
    $vaidata = $ibmdata->sync_vai_price($low, $high);
    foreach ($vaidata as $v) {
        $sku = trim($v['J6ITEM']);
        $msrp = ecom::removeCommasInNumber($v['J6LPRC']);
        $pl1 = ecom::removeCommasInNumber($v['J6PL01']);
        $map = ecom::removeCommasInNumber($v['J6PL09']);
        $pl10 = ecom::removeCommasInNumber($v['J6PL10']);
        $cost = ecom::removeCommasInNumber($v['FIFOCOST']);

        if (array_key_exists($sku, $currentPrices)) {
            if (
                ecom::removeCommasInNumber($currentPrices[$sku]['msrp']) !== $msrp ||
                ecom::removeCommasInNumber($currentPrices[$sku]['pl1']) !== $pl1 ||
                ecom::removeCommasInNumber($currentPrices[$sku]['map']) !== $map ||
                ecom::removeCommasInNumber($currentPrices[$sku]['pl10']) !== $pl10 ||
                ecom::removeCommasInNumber($currentPrices[$sku]['cost']) !== $cost
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

endClock($start);