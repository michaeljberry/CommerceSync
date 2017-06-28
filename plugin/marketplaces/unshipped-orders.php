<?php
require __DIR__ . '/../../core/init.php';

use models\channels\Tracking;

$channel = '';
if (isset($_GET['channel']) && !empty($_GET['channel'])) {
    $channel = htmlentities($_GET['channel']);
}

$unshippedOrders = Tracking::getUnshippedOrders($channel);

//print_r($orders_last_day);
$x = 0;
$table = "<table id='unshipped-table'>
        <thead><th>Date</th><th>Order Number</th><th>Channel</th><th>Complete?</th><th>Cancel?</th></thead>";
foreach ($unshippedOrders as $o) {
    $date = $o['processed'];
    $order_num = $o['order_num'];
    $channel = $o['type'];
    $table .= "<tr id='$order_num'>";
    $table .= "<td>$date</td>";
    $table .= "<td>$order_num</td>";
    $table .= "<td>$channel</td>";
    $table .= "<td><input type='radio' name='orderstatus$x' id='complete$x' value='complete$x' class='order-complete''/><label for='complete$x'><span></span></label></td>";
    $table .= "<td><input type='radio' name='orderstatus$x' id='cancel$x' value='cancel$x' class='order-cancel''/><label for='cancel$x'><span></span></label></td>";
    $table .= "</tr>";
    $x++;
}
$table .= "</table>";
$script = "<script type='text/javascript'>
        $('#unshipped-table').dataTable({
            'bJQueryUI' : true,
            'sPaginationType' : 'full_numbers',
            'iDisplayLength' : 15,
            'fnDrawCallback' : function(oSettings){
                $('.order-complete').on('click', function(e){
                    var id = $(e.target).closest('tr').attr('id');
                    editorder(id, 'complete');
                });
                $('.order-cancel').on('click', function(e){
                    var id = $(e.target).closest('tr').attr('id');
                    editorder(id, 'cancel');
                });
            }
        });
        function editorder(id, status){
            var data = 'id=' + id + '&status=' + status;
            $.ajax({
                type: 'POST',
                url: '" . RELPLUGIN . "marketplaces/change-order.php',
                data: data,
                success: function(response, status){
                    $('#subcontainer').load('" . RELPLUGIN . "marketplaces/unshipped-orders.php?channel=";
$script .= (!empty($channel) ? $channel : "");
$script .= "');
                },
                errors: function(){

                }
            });
        }
        </script>";
echo $table;
echo $script;