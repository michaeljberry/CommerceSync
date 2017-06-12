<script>
    $(document).ready(function(){
        jQuery.validator.addMethod("amazonOrder", function(value, element){
            return this.optional(element) || /\d{3}-\d{7}-\d{7}/.test(value);
        }, "Please verify your Amazon Order number");
        var validator = $('#price-calculator-form').validate({
            rules: {
                price_sku: {
                    required: true,
                    minlength: 2
                },
                price_quantity: {
                    required: true,
                    number: true
                },
                price_margin: {
                    required: true,
                    number: true
                },
                price_net_profit: {
                    required: true,
                    number: true
                },
                price_increment: {
                    required: true,
                    number: true
                },
                price_shipping: {
                    required: "#price-include-shipping:checked",
                    number: true
                }
            },
            messages: {
                price_sku: {
                    required: "Enter a SKU",
                    minlength: "Enter at least 2 characters"
                },
                price_quantity: {
                    required: "Enter a quantity",
                    number: "Enter a valid number (Integer)"
                },
                price_margin: {
                    required: "Enter a margin",
                    number: "Enter a valid number (Integer)"
                },
                price_net_profit: {
                    required: "Enter a net profit",
                    number: "Enter a valid number (Integer)"
                },
                price_increment: {
                    required: "Please enter a increment",
                    number: "Enter a valid number (Decimal/Integer)"
                },
                price_shipping: {
                    required: "Enter the shipping to charge",
                    number: "Enter a valid number (Decimal/Integer)"
                }
            },
            submitHandler: function(form){
                var data = $(form).serialize();
                $.ajax({
                    type: 'POST',
                    url: '<?php echo RELPLUGIN; ?>marketplaces/calculate-price-eb.php',
                    data: data,
                    success: function (response, status) {
                        $('#price-calculator-form').toggle();
                        $('#subcontainer').html(response);
                        $('#price_sku').val('');
                        $('#price_quantity').val('1');
                        $('#price_margin').val('28');
                        $('#price_net_profit').val('1');
                        $('#price_increment').val('.50');
                        $('#price_increment').val('.50');
                        $('#price_shipping').val('3.99');
                    },
                    error: function () {

                    }
                });
            }
        });
        $('.sub-menu').on('click', function(e){
            e.preventDefault();
            $(this).closest('ul').find('li').removeClass('sub-active');
            var menu = $(this).attr('id');
            menu = menu.substring(0, menu.length - 4);
            menu = '#' + menu + 'form';
            $(menu).toggle();
            if($(menu).is(':visible')){
                $(this).parent().addClass('sub-active');
            }
            $(menu).find(':input').first().focus();
            $('.sub-menu').each(function(){
                var menuId = $(this).attr('id');
                menuId = menuId.substring(0, menuId.length - 4);
                menuId = '#' + menuId + 'form';
                if(menuId != menu){
                    $(menuId).toggle(false);
                    validator.resetForm();
                }
            });
        });
    });

</script>