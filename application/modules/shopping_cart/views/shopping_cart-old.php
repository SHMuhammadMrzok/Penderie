<?php $this->load->view('shopping_cart_js'); ?>
<style type="text/css">
.loading_modal {
    display:     none;
    position:    fixed;
    z-index:     1000;
    top:         0;
    left:        0;
    height:      100%;/*750px;*/
    width:       100%;/*900px;*/
    margin-left: 0px;
    background:  rgba( 255, 255, 255, .8 )
                 url('<?php echo base_url().'assets/ajax-loader.gif';?>')
                 50% 50%
                 no-repeat;
}

body.loading {
    overflow: hidden;
}

body.loading .loading_modal {
    display: block;
}

.modal-footer .btn{
    width: 100px!important;
    border: 1px solid #ccc;
    height: 30px;
}
</style>



  <div class="breadcrumb">
    <div class="container">
      <div class="breadcrumb-inner">
        <ul class="list-inline list-unstyled">
          <li><a href="#">Home</a></li>
          <li class='active'>Shopping Cart</li>
        </ul>
      </div>
    </div>
  </div>

    <main class="no-padding-top">
        <div class="container no-padding">
            <!--<div class="row">-->

            <!--Middle Part Start-->
            <div class="fail_message container-cart title-page" style="display: none;"><i class="fa fa-exclamation-triangle"></i>  </div>
            <div id="cart_contents"></div>
            <div id="payment_div"></div>
            <!--Middle Part End -->

            <!--</div><!--row-->
        </div>
    </main>

<div class="loading_modal"><!-- Place at bottom of page --></div>

<?php
    if(!$is_logged_in)
    {
        $this->load->view('message_modal');
        $this->load->view('login_modal');
        $this->load->view('register_modal');
    }
?>
