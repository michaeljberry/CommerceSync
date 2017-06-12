<?php
require '../../core/init.php';
$channel = '';
if(isset($_GET['channel']) && !empty($_GET['channel'])){
    $channel = htmlentities($_GET['channel']);
}
$results = $ecommerce->stats_table($channel);