<?php
require __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';

use Ecommerce\Ecommerce;
use models\channels\product\Product;
use models\channels\product\ProductPrice;

$start = startClock();

$debug = false;
$testSku = '1127047';
if (!$debug) {
    $count = IBM::getCount();
    echo $count . '<br>';
    for ($low = 0; $low < $count; $low += 500) {
        $high = $low + 500;
        $vaidata = IBM::syncVAI($low, $high);
        foreach ($vaidata as $v) {
            $sku = trim($v['ICITEM']);
            if($sku !== $testSku){
                continue;
            }
            $name = trim($v['ICTITL']);
            $subTitle = trim($v['ICSUBT']);
            $description = trim($v['ICDSC1']);
            $upc = trim($v['ICUPC']);
            $weight = trim($v['ICWGHT']);
            $status = trim($v['ICDEL']);
            if ($status) {
                $status = 1;
            }
            $sku_id = Product::searchOrInsertBySku($sku, $name, $subTitle, $description, $upc, $weight, $status);
            $sku = str_replace("'", "''", $sku);
            $money = IBM::syncVAIPrices($sku, '1');
            if (isset($money[0])) {
                $msrp = Ecommerce::formatMoneyNoComma($money[0]['J6LPRC']);
                $pl1 = Ecommerce::formatMoneyNoComma($money[0]['J6PL01']);
                $map = Ecommerce::formatMoneyNoComma($money[0]['J6PL09']);
                $pl10 = Ecommerce::formatMoneyNoComma($money[0]['J6PL10']);
                $cost = Ecommerce::formatMoneyNoComma($money[0]['FIFOCOST']);
            } else {
                $money = IBM::syncVAIPrices($sku, '2');
                $msrp = Ecommerce::formatMoneyNoComma($money[0]['J6LPRC']);
                $pl1 = Ecommerce::formatMoneyNoComma($money[0]['J6PL01']);
                $map = Ecommerce::formatMoneyNoComma($money[0]['J6PL09']);
                $pl10 = Ecommerce::formatMoneyNoComma($money[0]['J6PL10']);
                $cost = Ecommerce::formatMoneyNoComma($money[0]['FIFOCOST']);
            }
            $result = ProductPrice::update($sku_id, $msrp, $pl1, $map, $pl10, $cost);

            if ($sku_id) {
//            echo $sku . ': Title: ' . $name . '; Subtitle: ' . $subTitle . '; Description: ' . $description . '; UPC: ' . $upc . '; Weight: ' . $weight . '<br>';
                echo $sku . ': Title: ' . $name . '; UPC: ' . $upc . ';<br>';
            } else {
                echo $sku_id . ' not successfully added/updated.';
            }
        }
    }
} else {
    $vaidata = IBM::syncVAI('', '', strtoupper($testSku));
    print_r($vaidata);
    $sku = trim($vaidata[0]['ICITEM']);
    $name = trim($vaidata[0]['ICTITL']);
    $subTitle = trim($vaidata[0]['ICSUBT']);
    $description = trim($vaidata[0]['ICDSC1']);
    $upc = trim($vaidata[0]['ICUPC']);
    $weight = trim($vaidata[0]['ICWGHT']);
    $status = trim($vaidata[0]['ICDEL']);
    if ($status) {
        $status = 1;
    }
    $sku_id = Product::searchOrInsert($sku, $name, $subTitle, $description, $upc, $weight, $status);
    Ecommerce::dd($sku_id);
    $sku = str_replace("'", "''", $sku);
    $money = IBM::syncVAIPrices($sku, '1');
    if (!isset($money[0])) {
        $money = IBM::syncVAIPrices($sku, '2');
    }
    $msrp = Ecommerce::formatMoneyNoComma($money[0]['J6LPRC']);
    $pl1 = Ecommerce::formatMoneyNoComma($money[0]['J6PL01']);
    $map = Ecommerce::formatMoneyNoComma($money[0]['J6PL09']);
    $pl10 = Ecommerce::formatMoneyNoComma($money[0]['J6PL10']);
    $cost = Ecommerce::formatMoneyNoComma($money[0]['FIFOCOST']);

    $result = ProductPrice::update($sku_id, $msrp, $pl1, $map, $pl10, $cost);

    if ($sku_id) {
//        echo $sku . ': Title: ' . $name . '; Subtitle: ' . $subTitle . '; Description: ' . $description . '; UPC: ' . $upc . '; Weight: ' . $weight . '<br>';
        echo $sku . ': Title: ' . $name . '; PL1: ' . $pl1 . '; PL10: ' . $pl10 . '; Cost: ' . $cost . '; MSRP: ' . $msrp . '<br>';
    } else {
        echo $sku_id . ' not successfully added/updated.';
    }
}
endClock($start);