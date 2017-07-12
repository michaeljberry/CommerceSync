<?php
require __DIR__ . '/../../core/init.php';

if (!empty($_POST['status'])) {
    if ($_POST['status'] == 'complete') {
        $orderNum = htmlentities($_POST['id']);
        \models\channels\Tracking::updateTrackingSuccessful($orderNum);
    } elseif ($_POST['status'] == 'cancel') {
        $orderNum = htmlentities($_POST['id']);
        \models\channels\Order::cancel($orderNum);
    }
}