<?php
use am\Amazon;
use eb\Ebay;

require '../../core/init.php';

require WEBPLUGIN . 'am/amvar.php';
require WEBPLUGIN . 'eb/ebvar.php';

if (isset($_POST['store']) && $_POST['store'] == 'amazon') {
    if (isset($_POST['fromDate']) && isset($_POST['toDate'])) {
        $fromDate = htmlentities($_POST['fromDate']);
        $toDate = htmlentities($_POST['toDate']);

        $result = Amazon::set_order_dates($fromDate, $toDate);
        if ($result) {
            echo 'Days saved successfully.';
        }
    }
} elseif (isset($_POST['store']) && $_POST['store'] == 'ebay') {
    if (isset($_POST['days'])) {
        $days = htmlentities($_POST['days']);
        $result = Ebay::set_order_days($days);
        if ($result)
            echo 'Days saved successfully.';
    }
}