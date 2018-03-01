<?php
use models\channels\Category;
use Ecommerce\Ecommerce;

include 'header-admin.php';

set_time_limit(3600);
$parents = Category::getParents('categories_ebay');
$children = Category::getChildren('categories_ebay');

$html = '<ul>';

$fees = [
    'Coins & Paper Money' => '.06',
    'Stamps' => '.06',
    'Musical Instruments & Gear' => '.07',
    'Desktops & All-In-Ones' => '.04',
    'iPads, Tablets & eBook Readers' => '.04',
    'Laptops & Netbooks' => '.04',
    'CPUs, Processors' => '.04',
    'Memory (RAM)' => '.04',
    'Motherboard & CPU Combos' => '.04',
    'Motherboards' => '.04',
    'Hard Drives (HDD, SSD & NAS)' => '.04',
    'Monitors,' => '.04',
    'Printers' => '.04',
    'Video Game Consoles' => '.04',
    'Car Electronics' => '.06',
    'Pro Audio Equipment' => '.06',
    'Memory Cards' => '.06',
    'Memory Card & USB Adapters' => '.06'
];

foreach ($parents as $key => $p) {
    $p_cat_id = $p['category_id'];
    $p_cat_name = $p['category_name'];
    if (array_key_exists($p_cat_name, $fees)) {
        $fee = $fees[$p_cat_name];

    } else {
        $fee = '.09';
    }
    $p_fee = $fee;
    $g_p_fee = $p_fee;
//    Fee::save_category_fee($p_cat_id, $fee);
//    $x = 0;
    $html .= "<li data-fees='$fee'>";
    $html .= "$p_cat_id: $p_cat_name";
    $html .= find_children($p_cat_id, $p_cat_name, $p_cat_name, $p_fee, $g_p_fee);
    $html .= "</li>";
//    $key = array_search($p_cat_id, array_column($parents, 'category_id'));
    unset($parents[$key]);
}

function find_children($p_cat_id, $p_cat_name, $list_name, $p_fee, $g_p_fee)
{
    global $children;
    global $fees;
//    global $x;
//    $parentArray = [619, 16212, 3858, 84659, 180009, 181222, 181223, 180012,
//        181227, 181228, 181246, 181249, 181254, 180010, 181225, 181489,
//        181224, 47043, 175696, 180015, 1451, 42459, 43381, 38103, 12922,
//        180016, 181281, 181282, 10181, 181267, 181268, 180008, 181162,
//        118974, 181163, 181170, 181172, 181180, 181187, 181193, 181196,
//        181491, 119023, 181255, 181260, 119027, 181202, 181203, 181210,
//        ];
    $child_html = '<ul>';
    foreach ($children as $key => $c) {
        $c_cat_id = $c['category_id'];
        $c_cat_name = $c['category_name'];
        $c_p_cat_id = $c['parent_category_id'];
        if (array_key_exists($c_cat_name, $fees)) {
            $fee = $fees[$c_cat_name];
            $p_fee = $fee;
        } elseif (array_key_exists($p_cat_name, $fees)) {
            $fee = $fees[$p_cat_name];
            $p_fee = $fee;
        } elseif ($p_fee != '.09') {
            $fee = $g_p_fee;
            $p_fee = $g_p_fee;
        } else {
            $fee = '.09';
            $p_fee = $fee;
        }
        $cat_name = "$list_name > $c_cat_id: $c_cat_name";
        if ($c_p_cat_id == $p_cat_id) {
            $child_html .= "<li data-fees='$fee'>";
            $child_html .= "$cat_name"; //- $fee
//            if(in_array($c_p_cat_id, $parentArray) && $x < 600) { //$current_fee != $fee && $x < 250
//                Ecommerce::save_category_fee($c_cat_id, $fee);
//                $x++;
//                echo "$x<br>";
//            }
            $child_html .= find_children($c_cat_id, $c_cat_name, $cat_name, $p_fee, $g_p_fee);
            $child_html .= "</li>";
            unset($children[$key]);
        }
    }
    $child_html .= '</ul>';
    return $child_html;
}

$html .= '</ul>';
echo $html;