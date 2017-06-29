<?php
use models\channels\Category;

include 'header-admin.php';

set_time_limit(3600);
$parents = Category::getParents('categories_amazon');
$children = Category::getChildren('categories_amazon');

$html = '<ul>';

foreach ($parents as $key => $p) {
    $p_cat_id = $p['category_id'];
    $p_cat_name = $p['category_name'];
    $html .= "<li>";
    $html .= "$p_cat_id: $p_cat_name";
    $html .= find_children($p_cat_id, $p_cat_name);
    $html .= "</li>";
//    $key = array_search($p_cat_id, array_column($parents, 'category_id'));
    unset($parents[$key]);
}

function find_children($p_cat_id, $p_cat_name)
{
    global $children;
    $child_html = '<ul>';
    foreach ($children as $key => $c) {
        $c_cat_id = $c['category_id'];
        $c_cat_name = $c['category_name'];
        $c_p_cat_id = $c['parent_category_id'];
        $cat_name = "$p_cat_name > $c_cat_id: $c_cat_name";
        if ($c_p_cat_id == $p_cat_id) {
            $child_html .= "<li>";
            $child_html .= "$cat_name";
            $child_html .= find_children($c_cat_id, $cat_name);
            $child_html .= "</li>";
            unset($children[$key]);
        }
    }
    $child_html .= '</ul>';
    return $child_html;
}

$html .= '</ul>';
echo $html;