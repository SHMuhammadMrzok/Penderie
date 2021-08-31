<?php $this->load->view('shopping_cart_js');?>
<script>
$( "body" ).on( "click", '#bank_payment_selection', function() {
    $('#bank_options_div').show();
    $('#voucher_div').hide();
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
            <a href="<?php echo base_url();?>Cart_Send_As_Gift">
              <span class="num">2</span><span><?php echo lang('send_as_gift');?></span>
            </a>
          </div>

        </div>
      </div>

      <div class="col">
        <div class="step-container active">
          <div>
            <a href="<?php echo base_url();?>Cart_Payment">
              <span class="num">3</span><span><?php echo lang('payment_methods');?></span>
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
        <div class="payment-area">
          <div class="header-shooping-cart">
            <div class="title-page">
              <h2><?php echo lang('payment_methods');?></h2>
            </div>
          </div>

          <div style="display: none;" class="alert alert-danger validation_error" role="alert"> </div>
          <form action="#" id="products_form">
            <?php foreach($payment_options as $option){
              // if banks
              if($option->id == 3){?>
                <div class="payment-container">
                  <div class="header-payment">
                    <div class="row">
                      <div class="col-md-6">
                        <?php
                            echo form_error('payment_option_id');
                            $payment_method_data = array(
                                                            'name'  => "payment_option_id",
                                                            'class' => 'bank_btn payment_options',
                                                            'value' => $option->id,
                                                            'id'    => 'bank_payment_selection',
                                                            'required' =>'required'
                                                         );

                            if($cart_data->payment_option_id == $option->id)
                            {
                                $payment_method_data['checked'] = set_radio('payment_option_id', $option->id, TRUE);
                            }

                            echo form_radio($payment_method_data);

                            echo form_error('payment_option_id');
                        ?>
                        <label for="options_input_<?php echo $option->id;?>"><?php echo $option->name;?>
                            <?php if($option->image != ''){?> <img height="20" src="<?php echo base_url();?>assets/uploads/<?php echo $option->image;?>" /><?php }?>
                        </label>
                      </div>
                      <?php echo form_error('bank_id'); ?>

                    </div>
                  </div>
                  <div class="main-payment">
                    <div id="bank_options_div" style="<?php echo ($cart_data->payment_option_id != $option->id)?'display: none;':''; ?> margin-left: 20px; overflow: hidden;">

                        <?php foreach($bank_accounts as $account) { ?>
                            <div class="form-group w-100 owner">
                                <input type="radio" name="bank_id" class="bank_btn"<?php echo ($cart_data->bank_id == $account->id && $cart_data->payment_option_id == $option->id)?' checked="checked"':''; ?> data-bank_id="<?php echo $account->id;?>" value="<?php echo $account->id;?>" id="bank_input_<?php echo $account->id;?>" />
                                <label for="bank_input_<?php echo $account->id;?>">
                                    <?php echo $account->bank;?>
                                    <?php if($account->image != ''){?>
                                        <img height="20" src="<?php echo base_url();?>assets/uploads/<?php echo $account->image;?>" />
                                    <?php }?>
                                </label>
                            </div><!--row-->

                            <div <?php echo ($cart_data->bank_id == $account->id && $cart_data->payment_option_id == $option->id)?' ':'style="display:none ;"'; ?> id="bank_details_<?php echo $account->id;?>" class="bank_details">
                                <div class="loader">
                                    <div id="bank_data_<?php echo $account->id;?>" class="bank_acc_data" style="display: ;">
                                        <div class="form-group w-100 owner">
                                             <?php echo lang('account_name');?> :<?php echo $account->bank_account_name;?>
                                        </div><!--row-->

                                        <div class="form-group w-100 owner">
                                            <?php echo lang('account_number');?> :<?php echo $account->bank_account_number; ?>
                                        </div><!--row-->
                                    </div>

                                    <div class="user_bank_accounts" id="bank_acc_<?php echo $account->id;?>"></div>
                                </div><!--loader-->

                                <?php /*<div class="user_account" id="user_acc_<?php echo $account->id;?>" >
                                    <div class="form-group w-100 owner">
                                        <?php
                                        if(isset($account->user_bank_id))
                                        {
                                            echo form_hidden('user_bank_account_id', $account->user_bank_account_id);
                                        }
                                        ?>
                                            <div class="form-group w-100 owner">
                                                <label class="owner">
                                                    <?php
                                                        echo lang('your_account_name').' : ';
                                                    ?>
                                                </label><!--name_acc-->
                                                <?php
                                                      echo form_error('account_name');
                                                      $acc_att = array(
                                                                          'name'=> "account_name[$account->id]",
                                                                          'id'=> "account_name_" . isset($account->user_bank_account_id)?intval($account->user_bank_account_id):0,
                                                                          'style'=>'display:block',
                                                                          'value'=> isset($account->user_bank_account_name)? $account->user_bank_account_name : '',
                                                                          'data-bank_id'=> $account->id
                                                                          );
                                                      echo form_input($acc_att);
                                                  ?>

                                            </div><!--row-->

                                            <div class="form-group w-100 owner">
                                                <label class="owner">
                                                    <?php echo lang('your_account_number').' : '; ?>
                                                </label><!--name_acc-->

                                                <?php
                                                    echo form_error('account_numer');
                                                    $acc_number_att = array('name'=>"account_number[$account->id]", 'id'=>"account_number_$account->user_bank_account_id", 'style'=>'display:block', 'value'=> isset($account->user_bank_account_number)? $account->user_bank_account_number : set_value("account_code"), 'data-bank_id'=> $account->id);
                                                    echo form_input($acc_number_att);
                                                ?>
                                                <input type="hidden" name="bank_id2" value="<?php echo $account->id;?>" data-bank_id="<?php echo $account->id;?>" />

                                            </div><!--row-->
                                        </div><!--row-->
                                    </div><!--user_account-->
                                    */?>
                                </div><!--bank_details_-->
                            <?php }?>
                    </div><!--bank_options-->
                  </div>
                </div>
            <?php }else{?>
              <div class="payment-container">
                <div class="header-payment">
                  <div class="row">
                    <div class="col-md-6">
                      <?php
                      if($option->id == 7)
                      {
                          $id    = 'voucher';
                          $class = 'payment_options';
                      } else {
                          $id    = 'options_input_'.$option->id;
                          $class = 'payment_options other_payment_options';
                      }
                      $payment_method_data = array(
                                                      'name'  => 'payment_option_id',
                                                      'class' => $class,
                                                      'value' => $option->id,
                                                      'id'    => $id,
                                                      'required' =>'required'
                                                  );
                      if($cart_data->payment_option_id == $option->id)
                      {
                          $payment_method_data['checked'] = set_radio('payment_option_id', $option->id, TRUE);
                      }

                      echo form_radio($payment_method_data);
                      ?>
                      <label for="m-2"><?php echo $option->name;?></label>

                    </div>
                    <div class="col-md-6">

                    </div>
                  </div>
                </div>
                <div class="main-payment">
                  <?php if($option->id == 7){//voucher number?>
                    <div class="form-group w-100" id="voucher_div" style="<?php echo ($cart_data->payment_option_id != $option->id)?'display: none;':''; ?>">
                      <label for="voucher_number">
                          <?php echo lang('voucher_number');?>
                      </label>
                      <input type="text" name="voucher" class="voucher_input" id="voucher_number" />

                    </div><!--row-->
                  <?php }else {?>

                    <div class="note">
                      <article>
                        <?php echo $option->description;?>
                      </article>
                    </div>
                  <?php }?>
                </div>
              </div>
            <?php }
          }?>

            <button href="#" class="place-order-button" id="submit_order"><?php echo lang('finish_order');?></button>
          </form>
        </div>

      </div>
      <div class="col-md-4">

        <?php //$this->load->view('cart_total', $this->data);?>
        <div class="cart_total"></div>
        <?php /*<div class="button-checkout">
           <a href="#" id="submit_order"><?php echo lang('finish_order');?></a>
        </div>*/?>



        <div class="continue-shopping">
          <a href="<?php echo base_url().'Shopping_Cart';?>"><?php echo lang('Back').' '.lang('to').' '.lang('cart');?></a>
        </div>
      </div>

    </div>

  </div>

</main>
<div id="payment_div"></div>
