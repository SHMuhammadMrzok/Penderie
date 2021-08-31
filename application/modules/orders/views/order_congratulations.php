<section class="steps-check-out">
  <div class="container">
    <div class="row w-100">
      <div class="col">
        <div class="step-container active">
          <div>
            <a href="<?php echo base_url().'Cart_Address';?>">
              <span class="num">1</span><span><?php echo lang('shipping_addrress');?></span>
            </a>
          </div>
        </div>
      </div>

      <div class="col">
        <div class="step-container active">
          <div>
            <a href="<?php echo base_url().'Cart_Send_As_Gift';?>" class="disabled"></a>
              <span class="num">2</span><span><?php echo lang('send_as_gift');?></span>
            </a>
          </div>

        </div>
      </div>

      <div class="col">
        <div class="step-container active">
          <div>
            <a href="<?php echo base_url();?>Cart_Payment" class="disabled">
              <span class="num">3</span><span><?php echo lang('payment');?></span>
            </a>
          </div>

        </div>
      </div>


      <div class="col">
        <div class="step-container active">
          <div>
            <a href="<?php echo base_url();?>orders/order/order_confirmation" class="disabled">
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
      <div class="congratulation">
          <svg>
              <use xlink:href="#check"></use>
            </svg>
          <p><?php echo lang('order_inserted_successfully');?></p>
          <a href="<?php echo base_url().'orders/order/view_order_details/'.$order_id;?>"><?php echo lang('track_your_order');?></a>

      </div>

      <?php if(isset($upload_bank_statement) && $upload_bank_statement){?>
        <div class="d-flex mt-3 mb-5 w-100">
          <div class="col-md-6  mr-auto ml-auto ">
            <div class="form-bank-transfer w-100">
              <form class="bank-upload-form" method="post" action="<?php echo base_url().'orders/order/upload_order_bank_statement';?>" enctype="multipart/form-data">
                <label><?php echo lang('upload_bank_statement');?></label>
                <input type="file" name="userfile" class="form-control h-43"/>
                <input type="hidden" name="grouped_order_id" value="<?php echo $orders_number;?>" />

                <button class="sec-button"><?php echo lang('upload');?></button>
              </form>
            </div>
          </div>

        </div>
      <?php }?>

    </div>
  </div>
</main>
