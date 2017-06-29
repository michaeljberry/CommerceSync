<?php
require '../../core/init.php';

use ecommerce\Ecommerce;

if (isset($_POST['id']) && !empty($_POST['id'])) {
    $id = htmlentities($_POST['id']);
    $oi = \models\channels\Order::getByID($id);
    $items = \models\channels\OrderItem::getByOrderId($id);

    $total = Ecommerce::formatMoney($oi['taxes']);
    $item_html = "";
    foreach ($items as $i) {
        $itemInfo = $ecommerce->orderItemHtml($i, $total);
        $item_html .= $itemInfo[0];
        $total = $itemInfo[1];
    }

    $html = $ecommerce->orderHtml($oi, $total, $item_html);

    echo $html;
}