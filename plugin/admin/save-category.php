<?php
use models\channels\Category;

require '../../core/init.php';

if (!empty($_POST['id'])) {
    $id = htmlentities($_POST['id']);
    $cat = htmlentities($_POST['val']);
    if (empty($cat)) {
        $cat = NULL;
    }
    $result = Category::updateMap($id, $cat, 'categories_ebay_id');
    return true;
}