<?php
include 'header-woocommerce.php';

print_r($wcinv->get_wc_products($ecommerce)); //25816 parent; 25837 variation
?>
    <div id="subcontainer">
        <div id='stats-table'></div>
        <div id='chart'></div>
    </div>
<?php
include 'footer-woocommerce.php';