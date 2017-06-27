<?php
require '../../core/init.php';
$start_time = microtime(true);

if (!empty($_POST['channel'])) {
    $order_num = '';
    $tracking_number = '';
    $first_name = '';
    $last_name = '';
    $date = '';
    $search_array = [];
    $channel = '';
    if (!empty($_POST['channel'])) {
        $channel = htmlentities($_POST['channel']);
    }
    if (!empty($_POST['order_num'])) {
        $search_array['order_num'] = htmlentities($_POST['order_num']);
    }
    if (!empty($_POST['tracking_num'])) {
        $search_array['tracking_num'] = htmlentities($_POST['tracking_num']);
    }
    if (!empty($_POST['first_name'])) {
        $search_array['first_name'] = htmlentities($_POST['first_name']);
    }
    if (!empty($_POST['last_name'])) {
        $search_array['last_name'] = htmlentities($_POST['last_name']);
    }
    if (!empty($_POST['date'])) {
        $search_array['date'] = $ecommerce->createFormattedDate(htmlentities($_POST['date']));
    }
    $results = $ecommerce->getOrders($search_array, $channel);
    $html = '';
    $scripts = '';
    $table = "<table><tr><td>Date</td><td>Order #</td><td>Name</td><td>Tracking #</td><td>Carrier</td></tr>";
    foreach ($results as $r) {
        $table .= "<tr>";
        $id = $r['id'];
        $order_num = $r['order_num'];
        $date = $ecommerce->createFormattedDate($r['date'], 'm/d/Y');
        $fname = $r['first_name'];
        $lname = $r['last_name'];
        $tracking = $r['tracking_num'];
        $carrier = $r['carrier'];
        $table .= "<td>$date</td>";
        $table .= "<td><a href='#' id='$id' class='order-specs'>$order_num</a></td>";
        $table .= "<td>$fname $lname</td>";
        $table .= "<td>$tracking</td>";
        $table .= "<td>$carrier</td>";
        $table .= "</tr>";
    }
    $table .= "</table>";
    $scripts .= "<script>
        $('.order-specs').on('click', function(e){
            e.preventDefault();
            var id = $(this).attr('id');
            $.colorbox({
                width: '75%',
                height: '60%',
                href: '";
    $scripts .= RELPLUGIN;
    $scripts .= "marketplaces/single-order.php',
                data: {'id': id}
            })
        });
        </script>";
    $html .= $table . $scripts;
    echo $html;
}