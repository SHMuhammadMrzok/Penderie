<?php $this->load->view('shopping_cart_js'); ?>
<script>
$( "body" ).on( "click", ".save_wrapping", function(event){
  event.preventDefault();

   postData ={
               wrapping_id: $( ".wrapping_id option:selected" ).val(),
               gift_msg : $( ".gift_msg" ).val()
             }

   $.post('<?php echo base_url()?>shopping_cart/cart/update_cart_gift_cost/', postData, function(){
       showToast( '<?php echo lang('execution_success');?>', '<?php echo lang('message ');?>', 'success' );
   });


});
</script>

<section class="steps-check-out">
  <div class="container">
    <div class="row w-100">
      <div class="col">
        <div class="step-container active">
          <div>
            <a href="<?php echo base_url().'Cart_Address';?>">
              <span class="num">1</span><span><?php echo lang('shipping_address');?></span>
            </a>
          </div>
        </div>
      </div>

      <div class="col">
        <div class="step-container active">
          <div>
            <a href="<?php echo base_url().'Cart_Send_As_Gift';?>">
              <span class="num">2</span><span><?php echo lang('send_as_gift');?></span>
            </a>
          </div>

        </div>
      </div>

      <div class="col">
        <div class="step-container">
          <div>
            <a href="<?php echo base_url().'Cart_Payment';?>"  class="disabled">
              <span class="num">3</span><span><?php echo lang('payment');?></span>
            </a>
          </div>

        </div>
      </div>



      <div class="col">
        <div class="step-container">
          <div>
            <a href="#" class="disabled">
              <span class="num">4</span><span><?php echo lang('finish_order');?></span>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<main>
  <div class="container">
    <div class="row">
      <div class="col-md-8">
        <div class="gift-options">
          <div class="header-shooping-cart">
            <div class="title-page">
              <h2><?php echo lang('choose_gift_options');?></h2>
            </div>
          </div>

          <div class="gift-form all-in-box">
            <form action="#" class="w-100">
              <div class="form-group">
                  <div class="col-md-6 p-0">
                      <div class="type-box">
                          <label for="box-typing"><?php echo lang('wrapping_type');?></label>
                          <?php echo form_dropdown('wrapping_id', $wrapping, $cart_data->wrapping_id  , 'class="form-control wrapping_id"');?>
                        </div>
                  </div>
              </div>
              <div class="form-group">
                <label for="name"><?php echo lang('gift_msg');?><br />
                  <span> <?php //echo lang('gift_note');?></span>
                </label>
                <?php
                    $msg_data = array('name'=>"gift_msg", 'class'=>"form-control gift_msg" , 'value'=> isset($cart_data->gift_msg)? $cart_data->gift_msg : set_value("gift_msg"));
                    echo form_textarea($msg_data);
                ?>
              </div>
              <?php /*
              <div class="form-group mb-0">
                <button class="button ml-auto w-auto save_wrapping"><?php echo lang('save');?></button>
              </div>
              */?>

            </form>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="cart_total"></div>

      <div class="button-checkout">
        <a href="<?php echo base_url();?>Cart_Payment" class="save_gift_msg submit_gift"><?php echo lang('continue');?></a>
      </div>

      <div class="continue-shopping">
        <a href="<?php echo base_url().'Shopping_Cart';?>"><?php echo lang('Back').' '.lang('to').' '.lang('cart');?></a>
      </div>
    </div>
  </div>
</main>
