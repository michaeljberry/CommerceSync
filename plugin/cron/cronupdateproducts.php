<?php
require __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';
$start = startClock();

$debug = false;
$sku_test = 'z11102-95-p-blk-7str';
if (!$debug) {
    $count = IBM::getCount();
    echo $count . '<br>';
    for ($low = 0; $low < $count; $low += 500) {
        $high = $low + 500;
        $vaidata = IBM::syncVAI($low, $high);
        foreach ($vaidata as $v) {
            $sku = trim($v['ICITEM']);
            $name = trim($v['ICTITL']);
            $sub_title = trim($v['ICSUBT']);
            $description = trim($v['ICDSC1']);
            $upc = trim($v['ICUPC']);
            $weight = trim($v['ICWGHT']);
            $status = trim($v['ICDEL']);
            if ($status) {
                $status = 1;
            }
            $sku_id = $ecommerce->productSoiSku($sku, $name, $sub_title, $description, $upc, $weight, $status);

            $sku = str_replace("'", "''", $sku);
            $money = IBM::syncVAIPrices($sku, '1');
            if (isset($money[0])) {
                $msrp = number_format($money[0]['J6LPRC'], 2);
                $pl1 = number_format($money[0]['J6PL01'], 2);
                $pl10 = number_format($money[0]['J6PL10'], 2);
                $cost = number_format($money[0]['FIFOCOST'], 2);
            } else {
                $money = IBM::syncVAIPrices($sku, '2');
                $msrp = number_format($money[0]['J6LPRC'], 2);
                $pl1 = number_format($money[0]['J6PL01'], 2);
                $pl10 = number_format($money[0]['J6PL10'], 2);
                $cost = number_format($money[0]['FIFOCOST'], 2);
            }
            $result = $ecommerce->updatePrices($sku_id, $msrp, $pl1, $pl10, $cost);

            if ($sku_id) {
//            echo $sku . ': Title: ' . $name . '; Subtitle: ' . $sub_title . '; Description: ' . $description . '; UPC: ' . $upc . '; Weight: ' . $weight . '<br>';
                echo $sku . ': Title: ' . $name . '; UPC: ' . $upc . ';<br>';
            } else {
                echo $sku_id . ' not successfully added/updated.';
            }
        }
    }
} else {
    $vaidata = IBM::syncVAI('', '', strtoupper($sku_test));
    print_r($vaidata);
    $sku = trim($vaidata[0]['ICITEM']);
    $name = trim($vaidata[0]['ICTITL']);
    $sub_title = trim($vaidata[0]['ICSUBT']);
    $description = trim($vaidata[0]['ICDSC1']);
    $upc = trim($vaidata[0]['ICUPC']);
    $weight = trim($vaidata[0]['ICWGHT']);
    $status = trim($vaidata[0]['ICDEL']);
    if ($status) {
        $status = 1;
    }
    $sku_id = $ecommerce->product_soi_sku($sku, $name, $sub_title, $description, $upc, $weight, $status);

    $sku = str_replace("'", "''", $sku);
    $money = IBM::syncVAIPrices($sku, '1');
    if (isset($money[0])) {
        $msrp = number_format($money[0]['J6LPRC'], 2);
        $pl1 = number_format($money[0]['J6PL01'], 2);
        $pl10 = number_format($money[0]['J6PL10'], 2);
        $cost = number_format($money[0]['FIFOCOST'], 2);
    } else {
        $money = IBM::syncVAIPrices($sku, '2');
        $msrp = number_format($money[0]['J6LPRC'], 2);
        $pl1 = number_format($money[0]['J6PL01'], 2);
        $pl10 = number_format($money[0]['J6PL10'], 2);
        $cost = number_format($money[0]['FIFOCOST'], 2);
    }
    $result = $ecommerce->updatePrices($sku_id, $msrp, $pl1, $pl10, $cost);

    if ($sku_id) {
//        echo $sku . ': Title: ' . $name . '; Subtitle: ' . $sub_title . '; Description: ' . $description . '; UPC: ' . $upc . '; Weight: ' . $weight . '<br>';
        echo $sku . ': Title: ' . $name . '; PL1: ' . $pl1 . '; PL10: ' . $pl10 . '; Cost: ' . $cost . '; MSRP: ' . $msrp . '<br>';
    } else {
        echo $sku_id . ' not successfully added/updated.';
    }
}
endClock($start);