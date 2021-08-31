<div class="total-price">
  <?php if(isset($show_coupon)){
     if(!$is_wholesaler && $cart_data->coupon_discount == 0){?>
    <div class="copon">
      <form>
        <input type="text" name="coupon" id="coupon" placeholder="<?php echo lang('apply_coupon_here');?>" class="form-control"  />
        <button id="submit_coupon"><?php echo lang('Apply');?></button>
        <span id="coupon_msg" style="color: red;"></span>
      </form>
    </div>
  <?php }
}else{?>
  <h3><?php echo lang('order_summary');?></h3>
<?php }?>
  <div class="num-subtotal">
    <p><?php echo lang("total"); ?> <span><span><?php echo $currency;?></span> <?php echo $cart_data->total_price + $cart_data->optional_fields_cost; ?></span></p>

    <?php if($cart_data->coupon_discount > 0) { ?>
        <p><?php echo lang('coupon_discount');?><span>- <span> <?php echo $currency; ?></span> <?php echo $cart_data->coupon_discount; ?></span> </p>
    <?php }?>

    <?php if($cart_data->discount > 0) { ?>
        <p><?php echo lang('total_discount');?><span>- <span> <?php echo $currency; ?></span> <?php echo $cart_data->discount; ?></span> </p>
    <?php }?>

    <?php if($cart_data->shipping_cost > 0) { ?>
      <p><?php echo lang('shipping_cost');?><span> <span> <?php echo $currency; ?></span> <?php echo $cart_data->shipping_cost; ?></span> </p>
    <?php }?>

    <?php if($cart_data->wrapping_cost > 0) { ?>
      <p><?php echo lang('wrapping_cost');?><span><span> <?php echo $currency; ?></span> <?php echo $cart_data->wrapping_cost; ?></span> </p>
    <?php }?>
    <?php if($cart_data->vat_value > 0) { ?>
      <p><?php echo lang('vat_value');?><span> <span> <?php echo $currency; ?></span> <?php echo $cart_data->vat_value; ?></span> </p>
    <?php }?>



    <?php if($cart_data->tax > 0) { ?>
      <p><?php echo $tax_msg;?><span> <span> <?php echo $currency; ?></span><?php echo $cart_data->tax; ?></span></p>
    <?php }?>

  </div>
  <hr />
  <p><?php echo lang('final_total');?>:<span>  </span></p>
  <h2><span><?php echo $currency; ?></span><?php echo $cart_data->final_total_price_with_tax; ?></h2>
  <?php if($cart_data->vat_value > 0) { ?>
    <p><?php echo lang('vat');?><span><span> <?php echo $currency; ?></span> <?php echo $cart_data->vat_value; ?></span> </p>
  <?php }?>

  <?php /*<p class="hint">Add 78.00 EGP of "Fulfilled by sneak" items to your order to qualify for FREE Shipping.</p>*/?>

</div>
