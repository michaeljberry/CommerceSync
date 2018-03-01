<?php
require '../../core/init.php';

use Ecommerce\Ecommerce;

if (isset($_POST['id']) && !empty($_POST['id'])) {
    $id = htmlentities($_POST['id']);
    $oi = \models\channels\order\Order::getByID($id);
    $items = \models\channels\order\OrderItem::getByOrderId($id);

    $total = Ecommerce::formatMoney($oi['taxes']);
    $item_html = "";
    foreach ($items as $i) {
        $itemInfo = Ecommerce::orderItemHtml($i, $total);
        $item_html .= $itemInfo[0];
        $total = $itemInfo[1];
    }

    $html = Ecommerce::orderHtml($oi, $total, $item_html);

    echo $html;
}