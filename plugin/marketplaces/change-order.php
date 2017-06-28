<?php
require __DIR__ . '/../../core/init.php';

if (!empty($_POST['status'])) {
    if ($_POST['status'] == 'complete') {
        $id = htmlentities($_POST['id']);
        \models\channels\TrackingModel::updateTrackingSuccessful($id);
    } elseif ($_POST['status'] == 'cancel') {
        $id = htmlentities($_POST['id']);
        \models\channels\OrderModel::cancel($id);
    }
}