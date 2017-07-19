<?php
use models\channels\product\ProductPrice;

include 'header-operations.php';
ini_set('max_execution_time', 3600);
if ($userID == 838) {
    $upsideDown = ProductPrice::getUpsideDownCost();
    $table = "<table><thead><tr><th>SKU</th><th>PL10</th><th>PL1</th></tr></thead><tbody>";
    foreach ($upsideDown as $u) {
        $sku = $u['sku'];
        $pl10 = $u['pl10'];
        $pl1 = $u['pl1'];
        $cost = $u['cost'];
        $table .= "<tr><td>$sku</td><td>$pl10</td><td>$pl1</td></tr>";
    }
    $table .= "</tbody></table>";
    echo $table;
    $ebay_sandbox = true;
    if ($ebay_sandbox) {
        $x = 0;
    }
}
?>
<?php
include 'footer-operations.php';
?>