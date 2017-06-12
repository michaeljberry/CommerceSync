<?php

?>
<script type='application/javascript'>
    $('.category_id').on('change', function(e){
        var id = $(this).attr('id');
        var value = $(this).val();
        var data = "id=" + id + "&val=" + value;
        $.ajax({
            type: 'POST',
            url: '<?php echo RELPLUGIN; ?>admin/save-category.php',
            data: data,
            success: function(response, status){

            },
            error: function(){
                $("#error").toggle(true);
                $("#error").html("Something happened and this wasn't saved. Try again in a little bit.");
            }
        });
    });
    $('form#amazon-csv').submit(function(e){
        e.preventDefault();
        var data = new FormData(this);

        $.ajax({
            url: '<?php echo RELPLUGIN; ?>admin/amazon-csv.php',
            type: 'POST',
            data: data,
            async: false,
            contentType: false,
            processData: false,
            cache: false,
            success: function(data){
                $('#csv-results').html(data);
            }
        });
    });
    $('#fromDate, #toDate').on('change', function(e){
        var data = $('#dates').serialize();
        $.ajax({
            url: '<?php echo RELPLUGIN; ?>admin/save_dates.php',
            type: 'POST',
            data: data,
            success: function(data){
                $('#error').show().text(data).fadeOut(5000);
            },
            error: function(e){
                $('#error').show().text('There was an error trying to save the days. Please try again in a little bit');
            }
        });
    });
    $('#days').on('change', function(e){
        var data = $('#ebayDays').serialize();
        $.ajax({
            url: '<?php echo RELPLUGIN; ?>admin/save_dates.php',
            type: 'POST',
            data: data,
            success: function(data){
                $('#error').show().text(data).fadeOut(5000);
            },
            error: function(e){
                $('#error').show().text('There was an error trying to save the days. Please try again in a little bit');
            }
        });
    });
    $('#manuallyPullAmazonOrders').on('click', function(e){
        e.preventDefault();
        $.ajax({
            url: '<?php echo RELPLUGIN; ?>cron/cronordersam.php',
            type: 'POST',
            success: function(data){
                $('#orderDetails').html(data);
            },
            error: function(e){
                $('#error').show().text('There was an error trying to get orders from Amazon.');
            }
        });
    });
    $('#manuallyPullEbayOrders').on('click', function(e){
        e.preventDefault();
        $.ajax({
            url: '<?php echo RELPLUGIN; ?>cron/cronorderseb.php',
            type: 'POST',
            success: function(data){
                var i = data.indexOf("Successfully uploaded");
                if( i > 0){
                    var uploadedOrders = data.substr(i);
                    $('#orderDetails').html(uploadedOrders);
                }else{
                    $('#orderDetails').html('No new orders were found on eBay.');
                }

            },
            error: function(e){
                $('#error').show().text('There was an error trying to get orderes from Amazon.');
            }
        });
    });
</script>
