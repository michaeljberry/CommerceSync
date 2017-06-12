<?php
require '../../core/init.php';
if(isset($_POST['id']) && !empty($_POST['id'])){
    $id = htmlentities($_POST['id']);
    $oi = $ecommerce->getOrder($id);
    $items = $ecommerce->getOrderItems($id);

    $total = \ecommerce\Ecommerce::formatMoney($oi['taxes']);
    $item_html = "";
    foreach($items as $i){
        $itemInfo = $ecommerce->orderItemHtml($i, $total);
        $item_html .= $itemInfo[0];
        $total = $itemInfo[1];
    }

    $html = $ecommerce->orderHtml($oi, $total, $item_html);

    echo $html;
}