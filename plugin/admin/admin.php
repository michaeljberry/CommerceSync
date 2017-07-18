<?php
use am\Amazon;
use eb\Ebay;

include 'header-admin.php';
require WEBPLUGIN . 'am/amvar.php';
require WEBPLUGIN . 'bc/bcvar.php';
require WEBPLUGIN . 'eb/ebvar.php';
require WEBPLUGIN . 'rev/revvar.php';
require WEBPLUGIN . 'wm/wmvar.php';

$days = Amazon::get_order_dates();
$from = $days['api_pullfrom'];
$to = $days['api_pullto'];

$ebayDays = Ebay::get_order_days();


?>
    <h2>Amazon</h2>
    Pull Amazon Orders From:<br>
    <form id="dates">
        <input type="hidden" value="amazon" name="store"/>
        <select id="fromDate" name="fromDate">
            <option value="-3" <?= $from == -3 ? 'selected' : '' ?>>-3 days</option>
            <option value="-4" <?= $from == -4 ? 'selected' : '' ?>>-4 days</option>
            <option value="-5" <?= $from == -5 ? 'selected' : '' ?>>-5 days</option>
            <option value="-6" <?= $from == -6 ? 'selected' : '' ?>>-6 days</option>
        </select><br>
        Pull Amazon Orders to:<br>
        <select id="toDate" name="toDate">
            <option value="0" <?= $to == 0 ? 'selected' : '' ?>>0 days</option>
            <option value="-1" <?= $to == -1 ? 'selected' : '' ?>>-1 days</option>
            <option value="-2" <?= $to == -2 ? 'selected' : '' ?>>-2 days</option>
            <option value="-3" <?= $to == -3 ? 'selected' : '' ?>>-3 days</option>
        </select>
    </form>
    <br>
    <button id="manuallyPullAmazonOrders" class="submit">Manually Pull Amazon Orders</button>
    <br>
    <br>
    <h2>eBay</h2>
    Pull eBay Orders for the last:<br>
    <form id="ebayDays">
        <input type="hidden" value="ebay" name="store"/>
        <select id="days" name="days">
            <option value="1" <?= $ebayDays == 1 ? 'selected' : '' ?>>1 Days</option>
            <option value="2" <?= $ebayDays == 2 ? 'selected' : '' ?>>2 Days</option>
            <option value="3" <?= $ebayDays == 3 ? 'selected' : '' ?>>3 Days</option>
            <option value="4" <?= $ebayDays == 4 ? 'selected' : '' ?>>4 Days</option>
        </select>
    </form>
    <br>
    <button id="manuallyPullEbayOrders" class="submit">Manually Pull eBay Orders</button>
    <div id="orderDetails"></div>
<?php
include 'footer-admin.php';