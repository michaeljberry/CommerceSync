<?php
use models\channels\Category;

include 'header-admin.php';

$results = Category::getMappable();
//print_r($results);

$html = "<table><thead><tr>
    <th>ID</th>
    <th>eBay Category ID</th>
    <th>Amazon Category ID</th>
    <th>BigCommerce</th>
    </tr></thead><tbody>";
foreach ($results as $r) {
    $id = $r['id'];
    $amazon_id = $r['categories_amazon_id'];
    $amazon_name = $r['am_cat_name'];
    $bc_id = $r['categories_bigcommerce_id'];
    $bc_name = $r['bc_cat_name'];
    $ebay_id = $r['categories_ebay_id'];
    $ebay_name = $r['eb_cat_name'];
    $html .= "<tr>";
    $html .= "<td>$id</td>";
    $html .= "<td><input type='text' name='$id' id='$id' class='category_id forminput' value='$ebay_id: $ebay_name' onclick='this.select();'/></td>";
    $html .= "<td>$amazon_id: $amazon_name</td>";
    $html .= "<td>$bc_id: $bc_name</td>";
    $html .= "</tr>";
}
$html .= '</tbody></table>';
echo $html;
?>
    <div id="subcontainer">

    </div>
<?php
include 'footer-admin.php';
?>