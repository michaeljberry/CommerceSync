<?php
require __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';

use ecommerce\Ecommerce;
use models\channels\product\ProductPrice;
use models\channels\SKU;

$start = startClock();

$count = IBM::getCount();
echo $count . '<br>';

$currentPrices = ProductPrice::get();

for ($low = 0; $low < $count; $low += 500) {
    $high = $low + 500;
    $vaidata = IBM::syncVAIPrice($low, $high);
    foreach ($vaidata as $v) {
        $sku = trim($v['J6ITEM']);
        $msrp = Ecommerce::removeCommasInNumber($v['J6LPRC']);
        $pl1 = Ecommerce::removeCommasInNumber($v['J6PL01']);
        $map = Ecommerce::removeCommasInNumber($v['J6PL09']);
        $pl10 = Ecommerce::removeCommasInNumber($v['J6PL10']);
        $cost = Ecommerce::removeCommasInNumber($v['FIFOCOST']);

        if (array_key_exists($sku, $currentPrices)) {
            if (
                Ecommerce::removeCommasInNumber($currentPrices[$sku]['msrp']) !== $msrp ||
                Ecommerce::removeCommasInNumber($currentPrices[$sku]['pl1']) !== $pl1 ||
                Ecommerce::removeCommasInNumber($currentPrices[$sku]['map']) !== $map ||
                Ecommerce::removeCommasInNumber($currentPrices[$sku]['pl10']) !== $pl10 ||
                Ecommerce::removeCommasInNumber($currentPrices[$sku]['cost']) !== $cost
            ) {
                $sku_id = SKU::getIdBySku($sku);
                $result = ProductPrice::update($sku_id, $msrp, $pl1, $map, $pl10, $cost);
                if ($result) {
                    echo "<br>$sku is updated<br>";
                    echo "<br><br><br>";
                }
            }
        }
    }
}

endClock($start);