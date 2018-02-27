<?php
use ecommerce\Ecommerce;
use models\channels\Inventory;
use models\channels\Listing;
use models\channels\SKU;
use Ebay\EbayInventory;

require '../../core/init.php';
$start_time = microtime(true);
if ($_POST['inventory-sku']) {
    $sku = htmlentities($_POST['inventory-sku']);
    $ebay_inv = '';
    $amazon_inv = '';
    $mml_inv = '';
    if (!empty($_POST['ebay-check'])) {
        $ebay_inv = htmlentities($_POST['ebay-check']);
    }
    if (!empty($_POST['amazon-check'])) {
        $amazon_inv = htmlentities($_POST['amazon-check']);
    }
    if (!empty($_POST['mml-check'])) {
        $mml_inv = htmlentities($_POST['mml-check']);
    }
//    echo $ebay_inv . ' ' . $amazon_inv . ' ' . $mml_inv;

    if ($amazon_inv) {
        echo '-- Amazon --<br>';
        $table = 'listing_amazon';
        $folder = '/var/www/html/portal/';
        $log_file_name = date('ymd') . ' - Amazon Inventory.txt';
        $inventory_log = $folder . 'log/inventory/' . $log_file_name;
        $fp = fopen($inventory_log, 'a+');
        fwrite($fp, "------------------" . date("Y/m/d H:i:s") . substr((string)$start_time, 1, 6) . "------------------" . PHP_EOL);
        fwrite($fp, "Updated SKU's: Stock_QTY" . PHP_EOL);

        $updated = Listing::getUpdatedBySku($table, $sku);
        if (!empty($updated)) {
//        print_r($updated);cd /var/www/html/portal/amazon
            /* Update Amazon Quantity per SKU */
            $x = 1;
            $y = 1;
            $amazon_price_xml = [];
            $stock_id = $updated['id'];
            $sku_id = $updated['sku_id'];
            $stock_qty = $updated['stock_qty'];
            $sku = SKU::getById($sku_id);
            fwrite($fp, $sku . ': ' . $stock_qty . PHP_EOL);
            $price = Listing::getPriceBySku($sku, $table);
            if (!empty($price)) {
                $amazon_price_xml = array_merge($amazon_price_xml, $aminv->create_inventory_price_update_item_xml($sku, $price, $y));
            } else {
                echo 'There was either no price, or the price was overrode<br>';
            }
            echo 'Amazon Quantity: ' . $stock_qty . '; Amazon Price: ' . $price . '<br>';

//        Create XML for Amazon
            $amazon_xml = $aminv->create_inventory_update_item_xml($sku, $stock_qty, $x);

            fwrite($fp, 'Inventory Upload File: ' . PHP_EOL . $amazon_xml . PHP_EOL . PHP_EOL);
            fwrite($fp, 'Price Upload File: ' . PHP_EOL . $amazon_price_xml . PHP_EOL . PHP_EOL);

//            echo $amazon_xml . '<br><br>';
//            echo $amazon_price_xml . '<br><br>';

            $response = $aminv->updateAmazonInventory($amazon_xml);
            print_r($response);
            if (strpos($response, 'SUBMITTED')) {
                echo 'Amazon Inventory Update was uploaded successfully.<br>';
            }
            fwrite($fp, "Inventory Upload Response: " . PHP_EOL . $response . PHP_EOL . PHP_EOL);
            $response = $aminv->updateAmazonInventoryPrice($amazon_price_xml);
//            print_r($response);
            if (strpos($response, 'SUBMITTED')) {
                echo 'Amazon Price Update was uploaded successfully.';
            }
            fwrite($fp, "Price Upload Response: " . PHP_EOL . $response . PHP_EOL . PHP_EOL);
            echo '<br>';
        } else {
            echo 'This SKU does not update to Amazon: ' . $sku . '<br>';
            fwrite($fp, 'This SKU does not update to Amazon:' . $sku);
        }
        fclose($fp);
    }
    echo '<br><br>';
    if ($ebay_inv) {
        echo '-- eBay --<br>';
        $table = 'listing_ebay';
        $folder = '/var/www/html/portal/';
        $log_file_name = date('ymd') . ' - eBay Inventory.txt';
        $inventory_log = $folder . 'log/inventory/' . $log_file_name;
        $fp = fopen($inventory_log, 'a+');
        fwrite($fp, "------------------" . date("Y/m/d H:i:s") . substr((string)$start_time, 1, 6) . "------------------" . PHP_EOL);
        fwrite($fp, "Updated SKU's: Stock_QTY" . PHP_EOL);

        $updated = Listing::getUpdatedBySku($table, $sku);
        if (!empty($updated)) {
            $stock_id = $updated['id'];
            $sku_id = $updated['sku_id'];
            $stock_qty = $updated['stock_qty'];
            $sku = SKU::getById($sku_id);
            fwrite($fp, $sku . ': ' . $stock_qty . PHP_EOL);
            $price = Listing::getPriceBySku($sku, $table);
            if (empty($price)) {
                $price = '';
                echo 'There was either no price, or the price was overrode<br>';
            }
            echo 'eBay Quantity: ' . $stock_qty . '; eBay Price: ' . $price . '<br>';

            $response = EbayInventory::updateEbayInventory($stock_id, $stock_qty, $price, $ecommerce);

            if (strpos($response, 'Success')) {
                echo 'eBay Inventory/Price Update was uploaded successfully';
            } else if (strpos($response, 'redundant')) {
                echo 'There was no change on eBay for this SKU.';
            } else {
                echo 'There was an issue with updating this SKU. Please let Michael read the following report:<br>';
                print_r($response);
            }

            fwrite($fp, "Inventory Upload Response: " . PHP_EOL . $response . PHP_EOL . PHP_EOL);
            echo '<br>';
        } else {
            echo 'This SKU does not update to eBay: ' . $sku . '<br>';
            fwrite($fp, 'This SKU does not update to eBay:' . $sku);
        }
        fclose($fp);
    }
    if ($mml_inv) {
        echo '-- MML --<br>';
        $table = 'listing_bigcommerce';
        $folder = '/var/www/html/portal/';
        $log_file_name = date('ymd') . ' - MML Inventory.txt';
        $inventory_log = $folder . 'log/inventory/' . $log_file_name;
        $fp = fopen($inventory_log, 'a+');
        fwrite($fp, "------------------" . date("Y/m/d H:i:s") . substr((string)$start_time, 1, 6) . "------------------" . PHP_EOL);
        fwrite($fp, "Updated SKU's: Stock_QTY" . PHP_EOL);

        $updated = Listing::getUpdatedBySku($table, $sku);
        if (!empty($updated)) {
            print_r($updated);
            $stock_id = $updated['id'];
            $sku_id = $updated['sku_id'];
            $stock_qty = $updated['stock_qty'];
            $sku = SKU::getById($sku_id);
            fwrite($fp, $sku . ': ' . $stock_qty . PHP_EOL);
            $price = Listing::getPriceBySku($sku, $table);
            if (empty($price)) {
                $price = '';
                echo 'There was either no price, or the price was overrode<br>';
            }
            echo 'MML Quantity: ' . $stock_qty . '; MML Price: ' . $price . '<br>';

            $response = $bcinv->update_bc_inventory($stock_id, $stock_qty, $price, $ecommerce);
            if (!empty($response)) {
                echo 'MML Inventory/Price was uploaded successfully';
                print_r($response);
            }
            fwrite($fp, 'Inventory Upload Response: ' . PHP_EOL . $response . PHP_EOL . PHP_EOL);
            echo '<br>';
        } else {
            echo 'This SKU does not update to MML: ' . $sku . '<br>';
            fwrite($fp, 'This SKU does not update to MML:' . $sku);
        }
        fclose($fp);
    }
}
$end_time = microtime(true);
$execution_time = ($end_time - $start_time) / 60;
echo "Execution time: $execution_time mins";