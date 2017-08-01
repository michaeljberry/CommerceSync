<?php
use Amazon\Amazon;
use Ebay\Ebay;

require '../../core/init.php';

require WEBPLUGIN . 'am/amvar.php';
require WEBPLUGIN . 'eb/ebvar.php';

if (isset($_POST['store']) && $_POST['store'] == 'amazon') {
    if (isset($_POST['fromDate']) && isset($_POST['toDate'])) {
        $fromDate = htmlentities($_POST['fromDate']);
        $toDate = htmlentities($_POST['toDate']);

        $result = Amazon::updateApiOrderDays($fromDate, $toDate);
        if ($result) {
            echo 'Days saved successfully.';
        }
    }
} elseif (isset($_POST['store']) && $_POST['store'] == 'ebay') {
    if (isset($_POST['days'])) {
        $days = htmlentities($_POST['days']);
        $result = Ebay::updateApiOrderDays($days);
        if ($result)
            echo 'Days saved successfully.';
    }
}
