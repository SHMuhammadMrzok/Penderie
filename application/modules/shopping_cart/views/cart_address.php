<?php $this->load->view('shopping_cart_js'); ?>
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
        <div class="step-container">
          <div>
            <a href="<?php echo base_url().'Cart_Send_As_Gift';?>" class="disabled"></a>
              <span class="num">2</span><span><?php echo lang('send_as_gift');?></span>
            </a>
          </div>

        </div>
      </div>

      <div class="col">
        <div class="step-container">
          <div>
            <a href="<?php echo base_url();?>Cart_Payment" class="disabled">
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
        <div class="shipping-area">
          <div class="header-shooping-cart">
            <div class="title-page">
              <h2><?php echo lang('shipping_address');?></h2>
              <p>( <?php echo lang('please_select_address');?> )</p>
            </div>
          </div>

          <form action="#">
            <div class="row">
              <?php foreach($user_addresses as $row){
                $checked = '';
                if($cart_data->address_id != 0){
                  if($cart_data->address_id == $row->id)
                  {
                    $checked = 'checked';
                  }
                }
                else {
                  if($row->default_add == $row->id)
                  {
                    $checked = 'checked';
                  }
                }?>
                <div class="col-md-4">
                  <div class="address-ar-container">
                    <div class="info">
                      <label><?php echo $row->title;?></label>
                      <h3><?php echo $row->address;?></h3>
                    </div>
                    <input class="cart_address" type="radio" name="user_add_id" value="<?php echo $row->id;?>" <?php echo $checked;?> required />
                  </div>
                </div>
              <?php }?>

              <div class="col-md-4">
                <div class="add-new-address">
                  <a href="<?php echo base_url();?>users/user_address/address/0/1">
                    <svg>
                      <use xlink:href="#plus"></use>
                    </svg>
                    <span>
                      <?php echo lang('new_address');?>
                    </span>
                  </a>
                </div>
              </div>
            </div>

          </form>
        </div>

      </div>
      <div class="col-md-4">

        <div class="cart_total"></div>

        <div class="button-checkout">
          <a class="submit_address" href="<?php echo base_url();?>Cart_Send_As_Gift"><?php echo lang('continue');?></a>
        </div>

        <div class="continue-shopping">
          <a href="<?php echo base_url().'Shopping_Cart';?>"><?php echo lang('Back').' '.lang('to').' '.lang('cart');?></a>
        </div>
      </div>
    </div>
  </div>
</main>

<script>
$(document).ready( function(){

  postData = {user_add_id : $( ".cart_address:checked" ).val()}
    $.post('<?php echo base_url().'shopping_cart/cart/update_cart_user_address';?>', postData, function(){
   });
});

$( "body" ).on( "click", ".submit_address", function(event){
  if($('input[name=user_add_id]:checked').length <= 0){
    event.preventDefault();
    showToast('<?php echo lang('please_select_address') ?>', '<?php echo lang('error');?>', 'error');
  }

});
</script>
