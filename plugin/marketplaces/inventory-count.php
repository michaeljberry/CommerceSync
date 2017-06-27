<?php
require '../../core/init.php';
if ($_POST['sku_list']) {
    $sku_list = trim(htmlentities($_POST['sku_list']));
//    echo $sku_list;
//    echo "<br><br>";
    $sku_array = preg_split("/((\r(?!\n))|((?<!\r)\n)|(\r\n))/", $sku_list);
//    print_r($sku_array);
//    echo '<br><br>';
    $response = $aminv->getFbaInventory($sku_array);
//    print_r($response);
    $sku_r = simplexml_load_string($response);
//    echo '<br><br>';
//    print_r($sku_r);
    $skus = $sku_r->ListInventorySupplyResult->InventorySupplyList;
//    echo '<br><br>';
//    print_r($skus);
//    $member = $skus->member;
//    echo '<br><br>';
//    echo count($skus->member);
//    print_r($member);
    $table = "<table class='invtable'><thead><th>SKU</th><th>In Stock QTY</th><th>Total QTY</th></thead><tbody>";
    foreach ($skus->member as $key => $s) {
//        echo '<br><br>';
//        print_r($key);
//        echo '<br><br>';
//        print_r($s);
        $sku = $s->SellerSKU;
        $inStockQty = $s->InStockSupplyQuantity;
        $totalQty = $s->TotalSupplyQuantity;
        $table .= "<tr><td>$sku</td><td>$inStockQty</td><td>$totalQty</td></tr>";
    }
    $table .= "</tbody></table>";
    echo $table;
}