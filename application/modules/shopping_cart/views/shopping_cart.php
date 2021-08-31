<?php $this->load->view('shopping_cart_js'); ?>



<div class="breadcrumb">
  <div class="container">
    <div class="breadcrumb-inner">
      <ul class="list-inline list-unstyled">
        <li><a href="<?php echo base_url();?>"><?php echo lang('home');?></a></li>
        <li class='active'><?php echo lang('shopping_cart');?></li>
      </ul>
    </div>
  </div>
</div>

<main>
  <div class="fail_message container-cart title-page" style="display: none;"></div>

  <div id="cart_contents"></div>
  <div id="payment_div"></div>


</main>
