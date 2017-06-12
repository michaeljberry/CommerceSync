</div>
<script type="application/javascript">
    $('.date').datepicker();
    $('#product-search-button').on('click', function(e){
        e.preventDefault();
        //Need to validate that at least one field is filled in
        var validated = false;
        $(':text').each(function(){
            if(this.value.length > 0){
                validated = true;
            }
        });
        if(validated) {
            $("#error").toggle(false);
            var data = $('#product-search-form').serialize();

            $.ajax({
                type: 'POST',
                url: '<?php echo RELPLUGIN; ?>marketplaces/search-products.php',
                data: data,
                success: function(response, status){
                    $('#product-search-form').toggle();
                    $('#subcontainer').html(response);
                    $(".forminput, .formtextarea").each(function(){
                        keylimit($(this));
                    });
                    $('#product-sku').val('');
                },
                error: function(){

                }
            });
        }else{
            $("#error").toggle(true);
            $("#error").html('Please fill in one of the search fields.');
        }
    });
    $('#update-inventory-button').on('click', function(e){
        e.preventDefault();
        //Need to validate that at least one field is filled in
        var validated = false;
        $(':text, [type=date]').each(function(){
            if(this.value.length > 0){
                validated = true;
            }
        });
        if(validated) {
            $("#error").toggle(false);
            var data = $('#update-inventory-form').serialize();

            $.ajax({
                type: 'POST',
                url: '<?php echo RELPLUGIN . 'marketplaces/';?>update-product-inventory.php',
                data: data,
                success: function (response, status) {
                    $('#update-inventory-form').toggle();
                    $('#subcontainer').html(response);
                    $('#inventory-sku').val('');
                },
                error: function () {

                }
            });
        }else{
            $("#error").toggle(true);
            $("#error").html('Please fill in one of the search fields.');
        }
    });
    $('#price-include-shipping').on('click', function(e){
        $('#shipping-price-div').toggle();
    });
    $('#price-calculator-button').on('click', function(e){
//        e.preventDefault();
//        var data = $('#price-calculator-form').serialize();
//        $.ajax({
//            type: 'POST',
//            url: '<?php //echo RELPLUGIN; ?>//marketplaces/calculate-price-eb.php',
//            data: data,
//            success: function (response, status) {
//                $('#price-calculator-form').toggle();
//                $('#subcontainer').html(response);
//                $('#price_sku').val('');
//                $('#price_quantity').val('1');
//                $('#price_margin').val('28');
//                $('#price_net_profit').val('1');
//                $('#price_increment').val('.50');
//            },
//            error: function () {
//
//            }
//        });
    });
    $("#order-lookup-button").on('click', function(e){
        e.preventDefault();
        //Need to validate that at least one field is filled in
        var validated = false;
        $(':text, [type=date]').each(function(){
            if(this.value.length > 0){
                validated = true;
            }
        });
        if(validated) {
            $("#error").toggle(false);
            var data = $('#order-lookup-form').serialize();
            $.ajax({
                type: 'POST',
                url: '<?php echo RELPLUGIN; ?>marketplaces/search-orders.php',
                data: data,
                success: function (response, status) {
                    $('#order-lookup-form').toggle();
                    $('#subcontainer').html(response);
                    $('#order_num').val('');
                    $('#tracking_num').val('');
                    $('#first_name').val('');
                    $('#last_name').val('');
                    $('#date').val('');
                    $('#error').html();
                },
                error: function () {

                }
            });
        }else{
            $("#error").toggle(true);
            $("#error").html('Please fill in one of the search fields.');
        }
    });
    $("#unshipped-orders").on('click', function(e){
        e.preventDefault();
        $('#subcontainer').load('<?php echo RELPLUGIN; ?>marketplaces/unshipped-orders.php?channel=<?php echo (!empty($channel_page) ? $channel_page : "") ?>');
    });
    $('#inventory-count-button').on('click', function(e){
        e.preventDefault();
        var validated = false;
        var text = $('#sku_list').val();
        var lines = text.split(/\r|\r\n|\n/);
        var count = lines.length;
        console.log(count);
        if(count <= 31 && count > 0){
            var validated = true
        }
        if(validated) {
            var data = $('#inventory-count-form').serialize();
            $.ajax({
                type: 'POST',
                url: '<?php echo RELPLUGIN; ?>marketplaces/inventory-count.php',
                data: data,
                success: function (response, status) {
                    $('#inventory-count-form').toggle();
                    $('#subcontainer').html(response);
                    $('#sku_list').val('');
                },
                error: function () {

                }
            });
        }else{
            $("#error").toggle(true);
            $("#error").html('Please enter between 1 and 30 skus inclusive.');
        }
    });
    function keylimit(e){
        var maxLength = $(e).attr('maxlength');
        var curLength = $(e).val();
        var inputId = $(e).attr('id');
        curLength = curLength.length;
        var diff = maxLength - curLength;//;
        if(diff < 10){
            $('#' + inputId + '-status').addClass('fontred');
        }else{
            $('#' + inputId + '-status').removeClass('fontred');
        }
        $('#' + inputId + '-status').text(diff);
    }

</script>
<script type='text/javascript'>
    $('#stats-table').load('<?php echo RELPLUGIN; ?>marketplaces/stats-table.php?channel=<?php echo (!empty($channel_page) ? $channel_page : "") ?>');
    var groupLabels = [
        ['Amazon-sales', 'Ebay-sales', 'Walmart-sales', 'Reverb-sales', 'BigCommerce-sales']
    ];
    graph('#chart',
        groupLabels,
        '<?php echo RELPLUGIN; ?>marketplaces/order-stats.php?channel=<?php echo (!empty($channel_page) ? $channel_page : "") ?>',
        '%Y-%m-%d',
        'Sales $',
        '')
</script>
<?php include 'sub-menu-script.php'; ?>