<?php
use models\channels\Category;

require '../../core/init.php';

if (!isset($_REQUEST['term'])) {
    exit;
}
$category_id = htmlentities($_REQUEST['term']);
$results = Category::getEbay($category_id);
$data = array();
if ($results && count($results)) {
    foreach ($results as $rows) {
        $data[] = [
            'cat_id' => $rows['category_id'],
            'p_cat_id' => $rows['parent_category_id'],
            'cat_name' => $rows['category_name']
        ];
    }
}
echo json_encode($date);
flush();