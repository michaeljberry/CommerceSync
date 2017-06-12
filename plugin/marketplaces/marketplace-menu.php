<?php
//This file for all marketplace pages (Amazon, eBay, etc.); Allows similar menu/functions
?>
<div id='sub-nav'>
    <ul id='sub-nav-ul'>
        <li><i class='fa fa-search fa-fw'></i><a href='#' id='order-lookup-menu' class='sub-menu'>Order Search</a>
        </li>
        <li><i class='fa fa-search fa-fw'></i><a href='#' id='product-search-menu' class='sub-menu'>Product Search</a></li>
        <li><i class='fa fa-refresh fa-fw'></i><a href='#' id='update-inventory-menu' class='sub-menu'>Update Inventory</a></li>
        <li><i class='fa fa-truck fa-fw'></i><a href='#' id='unshipped-orders' class='sub-menu'>Unshipped Orders</a></li>
        <?php
        if(isset($channel_page) && $channel_page == 'amazon'){
            echo "<li><i class='fa fa-tag fa-fw'></i><a href='#' id='inventory-count-menu' class='sub-menu'>Inventory Count</a></li>";
        }
        if(isset($channel_page) && $channel_page == 'ebay'){
            echo "<li><i class='fa fa-calculator fa-fw'></i><a href='#' id='price-calculator-menu' class='sub-menu'>Price Calculator</a></li>";
        }
        ?>
    </ul>
</div>
<form id='order-lookup-form'>
    <label for='order_num' class='formlabel'>Order Number</label><br>
    <input type='text' name='order_num' id='order_num' class='forminputround' placeholder='Order Number' onclick='this.select();'/><br>
    <label for='tracking_num' class='formlabel'>Tracking Number</label><br>
    <input type='text' name='tracking_num' id='tracking_num' class='forminputround' placeholder='Tracking Number' onclick='this.select();'/><br>
    <label for='first_name' class='formlabel'>First Name</label><br>
    <input type='text' name='first_name' id='first_name' class='forminputround' placeholder='First Name' onclick='this.select();'/><br>
    <label for='last_name' class='formlabel'>Last Name</label><br>
    <input type='text' name='last_name' id='last_name' class='forminputround' placeholder='Last Name' onclick='this.select();'/><br>
    <label for='date' class='formlabel'>Date</label><br>
    <input type='date' name='date' id='date' class='forminputround date' placeholder='Date' onclick='this.select();'/>
    <div id='radio'>
        <input type='radio' name='channel' value='amazon' id='amazon' class='formd' <?php echo (!empty($amazon_page)? " checked='checked' ": ''); ?>/>
        <label for='amazon' class='formlabel'><span></span>Amazon</label>
        <input type='radio' name='channel' value='ebay' id='ebay' class='formd'<?php echo (!empty($ebay_page) ? " checked='checked'" : ''); ?>/>
        <label for='ebay' class='formlabel'><span></span>eBay</label>
        <input type='radio' name='channel' value='bigcommerce' id='bigcommerce' class='formd'<?php echo (!empty($mml_page) ? " checked='checked'" : ''); ?>/>
        <label for='bigcommerce' class='formlabel'><span></span>MML</label>
        <input type='radio' name='channel' value='reverb' id='reverb' class='formd'<?php echo (!empty($reverb_page) ? " checked='checked'" : ''); ?>/>
        <label for='reverb' class='formlabel'><span></span>Reverb</label>
    </div>
    <button id='order-lookup-button' class='submit' type='submit'>Lookup Order</button>
</form>
<form id='product-search-form'>
    <label for='product-sku' class='formlabel'>SKU</label><br>
    <input type='text' name='product-sku' id='product-sku' class='forminputround' placeholder='SKU' /><br>
    <label for='product-name' class='formlabel'>Product Name</label><br>
    <input type='text' name='product-name' id='product-name' class='forminputround' placeholder='Product Name' /><br><br>
    <button id='product-search-button' class='submit' type='submit'>Lookup Product</button>
</form>
<form id='update-inventory-form' class='floatright'>
    <label for='inventory-sku' class='formlabel'>SKU</label><br>
    <input type='text' name='inventory-sku' id='inventory-sku' class='forminputround' placeholder='SKU' onclick='this.select();'/>
    <div id='checkboxes'>
        <input type='checkbox' name='amazon-check' id='amazon-check' class='formd' checked/>
        <label for='amazon-check' class='formlabel'><span></span>Amazon</label>
        <input type='checkbox' name='ebay-check' id='ebay-check' class='formd' checked/>
        <label for='ebay-check' class='formlabel'><span></span>eBay</label>
        <input type='checkbox' name='mml-check' id='mml-check' class='formd' checked/>
        <label for='mml-check' class='formlabel'><span></span>MML</label>
    </div><br>
    <button id='update-inventory-button' class='submit' type='submit'>Update Inventory</button>
</form>
<?php
if(isset($channel_page) && $channel_page == 'amazon') {
    echo "<form id='inventory-count-form' class='floatright'>
    <label for='sku_list' class='formlabel'>List of SKU's (30 max)</label><br>
    <textarea id='sku_list' name='sku_list' style='max-width: 292px'></textarea><br>
    <button id='inventory-count-button' class='submit' type='submit'>Cycle Count</button>
    </form>";
}
if(isset($channel_page) && $channel_page == 'ebay'){
    echo "<form id='price-calculator-form' class='floatright'>
    <div class='marginbottom'>
        <label for='price_sku' class='formlabel'>SKU</label><br>
        <input type='text' name='price_sku' id='price_sku' class='forminputround' placeholder='SKU' onclick='this.select();'/>
    </div>
    <ul class='dimensions-list'>
        <li>
            <label for='price_quantity' class='formlabel'>Quantity</label><br>
            <input type='text' name='price_quantity' id='price_quantity' class='forminputdimension smalltype' placeholder='Quantity' onclick='this.select();' value='1'/>
        </li>
        <li>
            <label for='price_margin' class='formlabel'>Margin</label><br>
            <input type='text' name='price_margin' id='price_margin' class='forminputdimension smalltype' placeholder='Margin' onclick='this.select();' value='28'/>%
        </li>
        <li>
            <label for='price_net_profit' class='formlabel'>Net Profit</label><br>
            <input type='text' name='price_net_profit' id='price_net_profit' class='forminputdimension smalltype' placeholder='Net Profit' onclick='this.select();' value='1'/>%
        </li>
        <li>
            <label for='price_increment' class='formlabel'>Increment</label><br>
            <input type='text' name='price_increment' id='price_increment' class='forminputdimension smalltype' placeholder='Increment' onclick='this.select();' value='.50'/>
        </li>
    </ul>
    <input type='checkbox' name='price-include-shipping' id='price-include-shipping' class='formd'/>
    <label for='price-include-shipping' class='formlabel'><span></span>Charge Shipping Separately?</label>
    <div id='shipping-price-div'>
        <label for='price_shipping' class='formlabel'>Shipping Cost</label><br>
        <input type='text' name='price_shipping' id='price_shipping' class='forminputround' placeholder='Shipping Cost' onclick='this.select();' value='3.99'/>
    </div><br>
    <div id='price-results'></div>
    <br>
    <button id='price-calculator-button' class='submit' type='submit'>Calculate Price</button>
    </form>";
}
?>