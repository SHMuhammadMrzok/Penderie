<script type="text/javascript">
function reload_data(submit_order)
{
    // Add intial value to submit_order var
    submit_order = submit_order || "0";

    var postData = $('#products_form').serializeArray();

    $.post('<?php echo base_url()."shopping_cart/cart/cart_ajax";?>', postData, function(result){
        if($.trim(result) == '0')
        {
            $('.fail_message').show();
            $('.fail_message').html('<?php echo '<div class="container"><div class="row"><div class="w-100 alert-area">'.lang('no_products_in_your_shopping_cart').'</div></div></div>';?>');
            $('.cart_container_div').hide();
        }
        else
        {
            $('#cart_contents').html(result);
        }
    });
}

function cart_total()
{
    var postData = [];
    $.post('<?php echo base_url()."shopping_cart/cart/cart_total";?>', postData, function(result){
        $('.cart_total').html(result);
    });
}

$(document).on({
    ajaxStart: function() { $('body').addClass("loading");    },
    ajaxStop: function() { $('body').removeClass("loading"); }
});

$(function(){
    reload_data();
    cart_total();

    $( "body" ).on( "change", ".qty", function(event){
        event.preventDefault();
        reload_data();
    });

    // When Deleting an item from shopping cart
    $( "body" ).on( "click", ".close_button", function(event){
        event.preventDefault();

        var deleted_product_id      = $(this).data("product_id");
        var deleted_cart_product_id = $(this).data("cart_product_id");

        bootbox.confirm(
            '<?php echo lang('confirm_delete_msg');?>',
            function(result) {
              if($.trim(result) == 'true')
              {
                  var postDelete = {
                                      product_id : deleted_product_id,
                                      cart_product_id : deleted_cart_product_id
                                   };

                  $.post('<?php echo base_url()."shopping_cart/cart/delete_product/";?>', postDelete, function(delete_result){

                      if($.trim(delete_result[0]) == '1')
                      {
                          reload_data();
                          //update cart qty icon
                          var cur_count = $( '.cart_items_count' ).html();
                          var product_qty = $('.qty_'+deleted_product_id).val();
                          var new_count = Number( cur_count ) - Number( product_qty ); //'<?php echo $cart_items_count + 1;?>';
                          $( '.cart_items_count' ).html( new_count );

                          showToast(delete_result[1], '<?php echo lang('success');?>', 'success');
                      }
                      else if($.trim(delete_result[0]) == '0')
                      {
                          showToast(delete_result[1], '<?php echo lang('error');?>', 'error');
                      }

                      if(typeof(delete_result[2]) != "undefined" && $.trim(delete_result[2]) != 'false')
                      {
                          showToast(delete_result[2], '<?php echo lang('error');?>', 'error');
                      }
                  }, 'json');
              }

            });
          });

    //When adding a coupon
    $( "body" ).on( "click", "#submit_coupon", function(event){
        event.preventDefault();

        var coupon      = $("#coupon").val();

        if($.trim(coupon).length != 0)
        {
            var postCoupon = {coupon_code: coupon};

            $.post('<?php echo base_url()."shopping_cart/cart/coupon_discount";?>', postCoupon, function(coupon_response){
                //

                if(coupon_response[0] == 1)
                {
                    reload_data();
                    showToast(coupon_response[1], '<?php echo lang('success'); ?>', 'success');
                }
                else
                {
                    $("#coupon_msg").html(coupon_response[1]);
                    $('#coupon').show();
                }
            }, 'json');
        }
        else
        {
            $("#coupon_msg").html('<?php echo lang('please_add_code_first');?>');
        }
    });
});

    $( "body" ).on( "click", "#submit_order", function(event){
        event.preventDefault();
        <?php if(!$this->ion_auth->logged_in()){?>
            if($('.email').val() == '' || $('.phone').val() == '' || $('.username').val() == '' || $('.country').val() == '')
            {
                $('.register_error').html('<?php echo lang('enter_required_fields');?> ')
            }
        <?php }?>

        <?php if(isset($shipping)){?>

            if($('#shipping_company').val() == '' || $('#shipping_cost_id').val() == '' || $('shipping_address').val() == '')
            {
                $('#shipping_validation').html('<?php echo lang('enter_required_fields');?> ')
            }
        <?php }?>

        //payment option validation
        if($('input[name=payment_option_id]:checked').length <= 0)
        {
            $('.validation_error').html('<?php echo lang('select_payment_option');?>');
            $('.validation_error').show();
        }
        else
        {
            $('.validation_error').hide();
            //bank validation
            if($('#bank_payment_selection').is(':checked'))
            {
                if($('input[name=bank_id]:checked').length <= 0)
                {
                    $('.validation_error').html('<?php echo lang('select_bank');?>');
                    $('.validation_error').show();
                }
                else
                {
                    var bank_id        = $('input:radio[name=bank_id]:checked').val();
                    var account_name   = $("input[name='account_name["+bank_id+"]']").val();
                    var account_number = $("input[name='account_number["+bank_id+"]']").val();

                    if((account_name == '') || (account_number == ''))
                    {
                        $('.validation_error').html('<?php echo lang('add_bank_data');?>');
                        $('.validation_error').show();
                    }
                    else
                    {
                        $('.validation_error').hide();
                        create_order();
                    }
                }
            }
            else if($('#voucher').is(':checked'))
            {
                $('.validation_error').hide();
                if ($('.voucher_input').val() != '')
                {
                    create_order();
                }
                else
                {
                    $('.validation_error').html('<?php echo lang('add_voucher');?>');
                    $('.validation_error').show();
                }
            }
            else
            {
                $('.validation_error').hide();
                create_order();
            }
        }
    });

    function create_order()
    {
        reload_data();

        var postData    = $('#products_form').serializeArray();
        var submit_form = false;

        $.post('<?php echo base_url()."orders/order/insert_order";?>',postData, function(result){
            <?php
            /*
              result[3] = is_first_order
              result[4] = first_order_status
              result[5] = error_msg

              result[4] = 0  -> validation error
              result[4] = 1  -> insert order
              result[4] = 2  -> sign up modal
              result[4] = 3  -> sign in modal
              result[4] = 4  -> show message
            */
            ?>


            /*
            if(result[0] == 'error')
            {
                //showToast(result[1], '<?php echo lang('error');?>', 'error');
            }
            else
            */
            if(result[0] == 'login_redirect')
            {
                window.location = "<?php echo base_url()."User_login";?>";
            }
            else if(result[0] == 'max_orders_per_day')
            {
                showToast('<?php echo lang('max_orders_per_day_reached') ?>', '<?php echo lang('error');?>', 'error');
            }
            else if(result[7] == true)
            {
                $('#shipping_validation').html(result[8]);
            }
            else if(result[3] == '1')
            {
                if(result[4] == '0')
                {
                    $('#validation_div').html(result[5]);
                }
                else if(result[4] == '1')
                {
                    submit_form = true;
                }
                else if(result[4] == '2')
                {
                    $("#email_reg").val(result[6].email);
                    $("#phone_reg").val(result[6].phone);
                    $("#username_reg").val(result[6].first_name);
                    $("#country_id_reg").val(result[6].Country_ID);

                    $("#user_nationality").val(result[6].country);
                    //$("#cart_country option:selected").text()
                    $('#registerModal').modal('show');

                    postData = {id: result[6].Country_ID};

                    $.post('<?php echo base_url()?>users/register/get_country_cities/', postData, function(result){
                        $("#city").html(result);
                    });

                }
                else if(result[4] == '3')
                {
                    $('#loginModal').modal('show');

                    if(result[5] != '')
                    {
                        showToast(result[5], '<?php echo lang('error');?>', 'error');
                    }
                }
                else if(result[4] == '4')
                {
                    $('#messageModal').modal('show');
                    $('.msg_span').html(result[5]);
                }
            }
            else if(result[0] == '1')
            {
                submit_form = true;
            }
            else if(result[0] == '0')
            {
                showToast(result[1], '<?php echo lang('error');?>', 'error');
            }


            if(submit_form)
            {
                //console.log(result);
                $('#payment_div').html(result[2]);
                $('.pay_form').submit();
                $( "#products_form" ).empty();
            }
        }, 'json');
    }


    $( "body" ).on( "click", '#select_bank', function() {
        reload_data();
    });



    $( "body" ).on( "click", '.bank_btn', function() {
        var bank_id = $(this).data("bank_id");
        $('.bank_details').hide();
        $('#bank_details_'+bank_id).show();

        postData = {bank_id: bank_id};
        $.post('<?php echo base_url()."shopping_cart/cart/cart_ajax";?>', postData, function(result){

        });
    });

    $( "body" ).on( "click", '.other_payment_options', function() {
        $('.bank_details').hide();
        $('#bank_options_div').hide();
        $('#voucher_div').hide();
    });

    $( "body" ).on( "click", '#voucher', function() {
        $('#voucher_div').show();
        $('#bank_options_div').hide();
    });


    $('.pocket_money, #reward_points_input, .payment_options').click(function(){
        reload_data();
    });

    $( "body" ).on( "click", '#voucher', function() {
       $('.voucher_input').show();
    });

    /*$( "body" ).on( "click", '#bank_payment_selection', function() {
        $('#bank_options_div').show();
    });*/


    ////Other payment options Extra taxes
    $( "body" ).on( "change", '.payment_options', function() {
        var payment_option_id = $( ".payment_options:checked" ).val();

        var postData = {payment_option_id : payment_option_id};

        $.post('<?php echo base_url()."shopping_cart/cart/cart_ajax";?>', postData, function(result){
            if($.trim(result) == '0')
            {
                $('.fail_message').show();
                $('.fail_message').html('<?php echo '<div class="container"><div class="row"><div class="col-md-8"><div class="alert-area">'.lang('no_products_in_your_shopping_cart').'</div></div></div></div>';?>');
                $('.cart_container_div').hide();
            }
            else
            {
                cart_total();
            }

        });

    });


       //Shippment

       $('body').on("change", '#shipping_company', function(){
        postData = {company_id : $( "#shipping_company option:selected" ).val()}

        $.post('<?php echo base_url().'shopping_cart/cart/update_cart_shipping_cost';?>', postData, function(result){
            reload_data();

       }, 'json');
    });

    $('body').on("change", "#shipping_country", function(){
        postData = {country_id : $('#shipping_country').val()}

        $.post('<?php echo base_url().'shopping_cart/cart/update_cart_shipping_country';?>', postData, function(){
            reload_data();
       });
    });

    $('body').on("blur", ".shipping_address", function(){
        postData = {shipping_address : $(this).val()}

        $.post('<?php echo base_url().'shopping_cart/cart/update_cart_shipping_address';?>', postData, function(){
       });
    });

    $('body').on("change", ".shipping_city", function(){
        postData = {shipping_city : $( ".shipping_city option:selected" ).val()}

        $.post('<?php echo base_url().'shopping_cart/cart/update_cart_shipping_city';?>', postData, function(){
       });
    });

    $('body').on("blur", ".shipping_town", function(){
        postData = {shipping_town : $(this).val()}

        $.post('<?php echo base_url().'shopping_cart/cart/update_cart_shipping_town';?>', postData, function(){
       });
    });

    $('body').on("blur", ".shipping_district", function(){
        postData = {shipping_district : $(this).val()}

        $.post('<?php echo base_url().'shopping_cart/cart/update_cart_shipping_district';?>', postData, function(){
       });
    });

    $('body').on("blur", ".shipping_name", function(){
        postData = {shipping_name : $(this).val()}

        $.post('<?php echo base_url().'shopping_cart/cart/update_cart_shipping_name';?>', postData, function(){
       });
    });

    $('body').on("blur", ".shipping_phone", function(){
        postData = {shipping_phone : $(this).val()}

        $.post('<?php echo base_url().'shopping_cart/cart/update_cart_shipping_phone';?>', postData, function(){

       });
    });

    $('body').on("change", ".cart_address", function(){

        postData = {user_add_id : $( ".cart_address:checked" ).val()}
        $.post('<?php echo base_url().'shopping_cart/cart/update_cart_user_address';?>', postData, function(){
       });
    });

    function showTempToast($msg,$title,$type)
    {
        var msg = $msg;
        var title = $title;
        var shortCutFunction = $type;

        toastr.options = {
              "closeButton": true,
              "debug": false,
              "positionClass": "toast-bottom-full-width",
              "onclick": null,
              "showDuration": "1000",
              "hideDuration": "1000",
              "timeOut": "3000",
              "extendedTimeOut": "1000",
              "showEasing": "swing",
              "hideEasing": "linear",
              "showMethod": "fadeIn",
              "hideMethod": "fadeOut"

           }

            var $toast = toastr[shortCutFunction](msg, title); // Wire up an event handler to a button in the toast, if it exists
            $toastlast = $toast;
            if ($toast.find("#okBtn").length) {
                $toast.delegate("#okBtn", "click", function () {
                    alert("you clicked me. i was toast #" + toastIndex + ". goodbye!");
                    $toast.remove();
                });
            }
            if ($toast.find("#surpriseBtn").length) {
                $toast.delegate("#surpriseBtn", "click", function () {
                    alert("Surprise! you clicked me. i was toast #" + toastIndex + ". You could perform an action here.");
                });
            }

            $("#clearlasttoast").click(function () {
                toastr.clear($toastlast);
            });
    }



     $( "body" ).on( "change", "#cart_country", function(){

       var nationality_id = $(this).val();

       var postData = {
            id : nationality_id
       };

       $.post('<?php echo base_url()?>users/register/get_country_call_code/', postData, function(result){
            $("#call_code").html(result);
       });

     });



     $( "body" ).on( "change", ".shipping_options", function(){

         var shipping_option = $( ".shipping_options option:selected" ).val();

         postData = {shipping_type: $( ".shipping_options option:selected" ).val()};

         $.post('<?php echo base_url()?>shopping_cart/cart/update_cart_shipping_type/', postData, function(){
            reload_data();
            if(shipping_option == 1)
            {
                //Home Delivery
                $('.delivery_form').show();
                $('.locator').hide();
                $('.shipping_form').hide();
                $('.user_address').hide();
            }
            else if(shipping_option == 2)
            {
                // Recieve From Shop
                $('.delivery_form').hide();
                $('.locator').show();
                $('.shipping_form').hide();
                $('.user_address').hide();
            }
            else  if(shipping_option == 3)
            {
                //Shipping
                $('.delivery_form').hide();
                $('.locator').hide();
                $('.shipping_form').show();
                $('.user_address').hide();
            }
            else  if(shipping_option == 4)
            {
                //Shipping
                $('.delivery_form').hide();
                $('.locator').hide();
                $('.shipping_form').hide();
                $('.user_address').show();
            }
            else
            {
                //Hide all
                $('.delivery_form').hide();
                $('.locator').hide();
                $('.shipping_form').hide();
                $('.user_address').hide();
            }
        });
     });

     /*$( "body" ).on( "change", ".wrapping_id, .ribbon_id, .box_id, .save_wrapping", function(event){
       event.preventDefault();
         //if ($('#send_gift_check').is(":checked"))
        //{
            postData ={
                        wrapping_id: $( ".wrapping_id option:selected" ).val(),
                        gift_msg : $( ".gift_msg" ).val()
                      }

            /*$.post('<?php echo base_url()?>shopping_cart/cart/update_cart_gift_cost/', postData, function(){
                reload_data();
            });
            */
        /*}
        else
        {
            $.post('<?php echo base_url()?>shopping_cart/cart/reset_cart_gift_cost/', postData, function(){
                reload_data();
            });
        }*/
  /*   });*/



     /*$( "body" ).on( "change", ".wrapping_id", function(){

        if ($('#send_gift_check').is(":checked"))
        {
            postData ={wrapping_id: $( ".wrapping_id option:selected" ).val()}
            $.post('<?php echo base_url()?>shopping_cart/cart/update_cart_gift_cost/', postData, function(){
                reload_data();
            });
        }
        else
        {
            $.post('<?php echo base_url()?>shopping_cart/cart/reset_cart_gift_cost/', postData, function(){
                reload_data();
            });
        }
     });
     */
     $( "body" ).on( "change", "#send_gift_check", function(){

        if ($('#send_gift_check').is(":checked"))
        {
            postData ={
                        wrapping_id: $( ".wrapping_id option:selected" ).val(),
                        ribbon_id: $( ".ribbon_id option:selected" ).val(),
                        box_id: $( ".box_id option:selected" ).val()
                      };

            $.post('<?php echo base_url()?>shopping_cart/cart/update_cart_gift_cost/', postData, function(){
                reload_data();
            });
        }
        else
        {
            postData = {'' : ''};
            $.post('<?php echo base_url()?>shopping_cart/cart/reset_cart_gift_cost/', postData, function(){
                reload_data();
            });
        }
     });



     $( "body" ).on( "change", ".checked_products", function(){
        var postData    = $('#products_form').serializeArray();

        $.post('<?php echo base_url();?>shopping_cart/cart/update_checked_products', postData, function(checked_stores_count){

            if(checked_stores_count < 1)
            {
                $('#submit_order').hide();
            }
            else
            {
                $('#submit_order').show();
            }

        });
     });

     $( "body" ).on( "change", ".wrapping_id", function(){
          postData ={wrapping_id: $( ".wrapping_id option:selected" ).val()}
          $.post('<?php echo base_url()?>shopping_cart/cart/update_cart_gift_cost/', postData, function(cart_data){
              cart_total();
          }, 'json');
     });

     $( "body" ).on( "click", ".save_gift_msg", function(e){
        e.preventDefault();
        if($('.wrapping_id').val() == 0){

          event.preventDefault();
          showToast('<?php echo lang('please_select_wrapping') ?>', '<?php echo lang('error');?>', 'error');
        }
        else {

          var postData ={
            gift_msg : $( ".gift_msg" ).val(),
            wrapping_id : $('.wrapping_id').val()
          };
          $.post('<?php echo base_url()?>shopping_cart/cart/update_cart_gift_cost/', postData, function(cart_data){
              window.location = "<?php echo base_url()."Cart_Payment";?>";
          }, 'json');

        }

     });


</script>
