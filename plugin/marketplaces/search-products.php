<?php
use models\channels\Listing;
use models\channels\product\Product;

require '../../core/init.php';
if ($_POST['product-sku']) {
    $product = htmlentities($_POST['product-sku']);
    $amazon_info = Listing::getBySku($product, 'listing_amazon');
//    print_r($amazon_info);
    $amazon_html = "";
    $amazon_checked = '';
    if (!empty($amazon_info)) {
        $amazon_checked = 'checked';
        $am_listing_id = $amazon_info['store_listing_id'];
        $am_price = $amazon_info['price'];
        $am_override_price = $amazon_info['override_price'];
        $am_url = $amazon_info['url'];
        $am_title = $amazon_info['title'];
        $am_description = $amazon_info['description'];
        $am_active = $amazon_info['active'];
        $am_sku = $amazon_info['sku'];
        $am_quantity = $amazon_info['inventory_level'];
        $am_category_id = $amazon_info['category_id'];
        $am_listed_since = $amazon_info['open_date'];
        $am_photo_url = $amazon_info['photo_url'];
        $am_asin1 = $amazon_info['asin1'];
        $am_fulfillment_channel = $amazon_info['fulfillment_channel'];
        $am_last_edited = $amazon_info['last_edited'];
        $am_category = ['AutoAccessory', 'Beauty', 'CameraPhoto', 'CE',
            'Clothing', 'FoodAndBeverages', 'Health', 'Home',
            'HomeImprovement', 'Jewelry', 'Miscellaneous',
            'MusicalInstruments', 'Office', 'PetSupplies',
            'SoftwareVideoGames', 'Sports', 'TiresAndWheels',
            'Tools', 'ToysBaby', 'Wireless'];
        $amazon_html .= "";
    } else {
        $amazon_html .= "";
    }

//    echo '<br><br>';
    $ebay_info = Listing::getBySku($product, 'listing_ebay');
//    print_r($ebay_info);
    $ebay_html = "
                <a href='www.ebay.com/itm/{{eb-listing-id}}' target='_blank'></a>
                <img src='{{eb-photo-url}}' class='small-product-photo'/>
                <label for='{{eb-title}}' class='formlabel'>Title</label><br>
                <input type='text' id='{{eb-title}}' name='{{eb-title}}' class='forminput' value='{{eb-title}}' maxlength='80' onkeyup='keylimit(this)'/>
                <div class='statusinput'><span id='{{eb-title}}-status'></span></div>

                <label for='{{eb-condition}}' class='formlabel'>Condition</label><br>
                <input type='text' id='{{eb-condition}}' name='{{eb-condition}}' class='forminput' value='{{eb-condition}}'/>
                <div class='statusinput'><span id='{{eb-condition}}-status'></span></div>

                <label for='{{eb-description}}' class='formlabel'>Description</label><br>
                <input type='text' id='{{eb-description}}' name='{{eb-description}}' class='forminput' value='{{eb-description}}'/>
                <div class='statusinput'><span id='{{eb-description}}-status'></span></div>

                <label for='{{eb-brand}}' class='formlabel'>Brand</label><br>
                <input type='text' id='{{eb-brand}}' name='{{eb-brand}}' class='forminput' value='{{eb-brand}}' maxlength='80' onkeyup='keylimit(this)'/>
                <div class='statusinput'><span id='{{eb-brand}}-status'></span></div>

                <button id='update-eb' class='submit' type='submit'>Update eBay Listing</button>
                <button id='delete-eb' class='submit type='submit'>Delete Listing</button>

                <label for='{{field}}' class='formlabel'>PlaceHolder</label><br>
                <input type='text' id='{{field}}' name='{{field}}' class='forminput' value='{{field}}' maxlength='80' onkeyup='keylimit(this)'/>
                <div class='statusinput'><span id='{{field}}-status'></span></div>
                ";
    $ebay_checked = '';
    if (!empty($ebay_info)) {
        $ebay_checked = 'checked';
        $eb_listing_id = $ebay_info['store_listing_id'];
        $eb_price = $ebay_info['price'];
        $eb_override_price = $ebay_info['override_price'];
        $eb_url = $ebay_info['url'];
        $eb_title = $ebay_info['title'];
        $eb_description = $ebay_info['description'];
        $eb_active = $ebay_info['active'];
        $eb_sku = $ebay_info['sku'];
        $eb_quantity = $ebay_info['inventory_level'];
        $eb_condition = $ebay_info['product_condition'];
        $eb_duration = $ebay_info['listing_duration'];
        $eb_type = $ebay_info['listing_type'];
        $eb_category_id = $ebay_info['primary_category'];
        $eb_category_name = $ebay_info['category_name'];
        $eb_free_shipping = $ebay_info['free_shipping'];
        $eb_shipping_cost = $ebay_info['shipping_cost'];
        $eb_shipping_additional_cost = $ebay_info['shipping_cost_additional'];
        $eb_photo_url = $ebay_info['photo_url'];
        $eb_external_photo_url = $ebay_info['external_photo_url'];
        $eb_last_edited = $ebay_info['last_edited'];
    }
    echo '<br><br>';
    $mml_info = Listing::getBySku($product, 'listing_bigcommerce');
    print_r($mml_info);
    $mml_checked = '';
    if (!empty($mml_info)) {
        $mml_checked = 'checked';
    }
    echo '<br><br>';
    $reverb_info = Listing::getBySku($product, 'listing_reverb');
    print_r($reverb_info);
    $reverb_checked = '';
    if (!empty($reverb_info)) {
        $reverb_checked = 'checked';
    }
    echo '<br><br>';
    $r = Product::getAllInfo($product);
    $name = $r['name'];
    $subtitle = $r['subtitle'];
    $mpn = $r['MPN'];
    $brand_id = $r['brand_id'];
    $description = $r['description'];
    $upc = $r['upc'];
    $country_of_manufacture_id = $r['country_of_manufacture_id'];
    $meta_keywords = $r['meta_keywords'];
    $meta_description = $r['meta_description'];
    $page_title = $r['page_title'];
    $width = $r['width'];
    $weight = $r['weight'];
    $height = $r['height'];
    $depth = $r['depth'];
    $product_id = $r['product_id'];
    $sku = $r['sku'];
    $sku_id = $r['sku_id'];
    $msrp = $r['msrp'];
    $map = $r['map'];
    $condition_id = $r['condition_id'];
    $uofm_id = $r['uofm_id'];
    $stock_qty = $r['stock_qty'];
    $warehouse_id = $r['warehouse_id'];
    $last_edited = $r['last_edited'];
    $html = '';
    $html .= "
    <div id='product'>
        <div id='product-aux' class='floatright'>
            <div id='productphoto'>
                <img src='http://1561a6a7909d9f53d86a-7cac89ee19f3b4d177ef11effcca7827.r55.cf1.rackcdn.com/images/$sku.jpg' title='$name' alt='$name' class='product-photo'/>
            </div>
            <div id='channel-list'>
                <h3>
                    <input type='checkbox' name='amazon' id='amazon' class='formd' $amazon_checked/>
                    <label for='amazon' class='formlabel'><span></span>Amazon</label>
                </h3>
                <div>
                    This is Amazon stuff.
                </div>
                <h3>
                    <input type='checkbox' name='ebay' id='ebay' class='formd' $ebay_checked/>
                    <label for='ebay' class='formlabel'><span></span>eBay</label>
                </h3>
                <div>
                    This is eBay stuff.
                </div>
                <h3>
                    <input type='checkbox' name='mml' id='mml' class='formd' $mml_checked/>
                    <label for='mml' class='formlabel'><span></span>MML</label>
                </h3>
                <div>
                    This is my stuff.
                </div>
                <h3>
                    <input type='checkbox' name='reverb' id='reverb' class='formd' $reverb_checked/>
                    <label for='reverb' class='formlabel'><span></span>Reverb</label>
                </h3>
                <div>
                    This is Reverb stuff.
                </div>
            </div>
        </div>
        <div id='productinfo' class='floatleft'>
            <form>
                <label for='name' class='formlabel'>Name</label><br>
                <input type='text' id='name' name='name' class='forminput' value='$name' maxlength='80' onkeyup='keylimit(this)'/><div class='statusinput'><span id='name-status'></span></div><br>

                <label class='formlabel'>SKU - </label>$sku<br>

                <label class='formlabel'>MPN - </label>$mpn<br>

                <label class='formlabel'>UPC - </label>$upc<br>

                <label for='description' class='formlabel'>Description</label><br>
                <textarea id='description' name='description' class='formtextarea' value='$description'></textarea><br>

                <label for='page-title' class='formlabel'>Page Title</label><br>
                <input type='text' id='page-title' name='page-title' class='forminput' value='$page_title' maxlength='80' onkeyup='keylimit(this)'/><div class='statusinput'><span id='page-title-status'></span></div><br>

                <label for='subtitle'class='formlabel'>Subtitle</label><br>
                <input type='text' id='subtitle' name='subtitle' class='forminput' value='$subtitle' maxlength='80' onkeyup='keylimit(this)'/><div class='statusinput'><span id='subtitle-status'></span></div><br>

                <label class='formlabel'>Meta Description</label><br>
                <textarea id='meta-description' name='meta-description' class='formtextarea' value='$meta_description' maxlength='150' rows='1' onkeyup='keylimit(this);'></textarea><div class='statustextarea'><span id='meta-description-status'></span></div><br>
                <ul class='dimensions-list'>
                    <li><label class='formlabel'>Weight</label><br>
                        <input type='text' id='weight' name='weight' class='forminputdimension' value='$weight'/>
                    </li>
                    <li>
                        <label class='formlabel'>Width</label><br>
                        <input type='text' id='width' name='width' class='forminputdimension' value='$width'/>
                    </li>
                    <li>
                        <label class='formlabel'>Height</label><br>
                        <input type='text' id='height' name='height' class='forminputdimension' value='$height'/>
                    </li>
                    <li>
                        <label class='formlabel'>Depth</label><br>
                        <input type='text' id='depth' name='depth' class='forminputdimension' value='$depth'/>
                    </li>
                </ul>
            </form>
        </div>
    </div>
     <script>CKEDITOR.replace('description');</script>
     <script>
        $('#channel-list').accordion({
            collapsible: true,
            heightStyle: 'content'
        });
         $('#channel-list .formlabel span').on('click', function(e){
            e.stopPropagation();
            var id = $(e.target);
            var parent = $(e.target).parents('h3');
            var checkbox = $(parent).children('.formd');
            if(checkbox.is(':checked')){
                checkbox.prop('checked', false);
            }else{
                checkbox.prop('checked', true);
            }
         });
     </script>";
    echo $html;
}
