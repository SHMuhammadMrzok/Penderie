<style>
.gift_div{
    display: none;
}
</style>
<div id="" class="cart_container_div row">
    <div class="title-page title">
        <h4><?php echo lang('finish_order');?></h4>
    </div><!--title-page-->

    <?php if(isset($quantity_status_error) ){?>
        <script>
            showTempToast('<?php echo $quantity_status_error;?>', '<?php echo lang('error');?>', 'error');
        </script>
    <?php }?>

    <?php if(isset($max_per_user) ){?>
        <script>
            showToast('<?php echo $max_per_user;?>', '<?php ?>', 'warning');
        </script>
    <?php }?>

    <?php if(isset($not_in_country) ){?>
        <script>
            showToast('<?php echo $not_in_country;?>', '<?php lang('error'); ?>', 'error');
        </script>
    <?php }?>

    <?php if(isset($max_per_customer_group) ){?>
        <script>
            showToast('<?php echo $max_per_customer_group;?>', '<?php lang('error'); ?>', 'error');
        </script>
    <?php }?>

    <?php if(isset($error) ){?>
        <script>
            showToast('<?php echo $error;?>', '<?php lang('error'); ?>', 'error');
        </script>
    <?php }?>

    <?php if(isset($no_products_msg)) {
        echo $no_products_msg;
        } else {?>
        <form id="products_form" action="<?php echo base_url();?>orders/order/insert_order" method="post">
            <div class=" row margin-bottom-20px">
                <div class="col-md-9">
                    <div class="container-cart">
                        <?php
                        foreach($cart_stores as $store)
                        {
                            unset($before_discount_price); ?>

                                <div class="form-group row margin-bottom-20px">
                                    <div class="col-sm-1">
                                        <label class="checkbox">
                                            <?php if($store->checked == 1){
                                                $checked = "checked";
                                                } else {
                                                    $checked = "";
                                                } ?>
                                            <input type="checkbox" name="checkd_stores[]" value="<?php echo $store->store_id;?>" <?php echo $checked;?> class="checked_products" title="<?php echo lang('add_store_products_to_order');?>" />
                                            <span></span>
                                        </label>
                                    </div>
                                    <label class="control-label">
                                        <?php echo $store->store_name;?>
                                    </label>
                                </div><!--form-group-->


                                <div class="row no-margin">
                                    <table class="table table-bordered table-hover">
                                        <tr class="header-table">
                                            <td width="100px"><?php echo lang("thumbnail"); ?></td>
                                            <td width="50%"><?php echo lang("product"); ?></td>
                                            <td width="20%"><?php echo lang("quantity"); ?></td>
                                            <td width="10%"><?php echo lang("total"); ?></td>
                                            <td width="10%"></td>
                                        </tr>

                                        <?php foreach($store->products as $details){?>
                                        <tr id="row_<?php echo $details->product_id; ?>">
                                            <td width="100px">
                                                <div class="img-prod">
                                                    <?php if($details->type =='recharge_card'){ ?>
                                                        <img src="<?php echo base_url(); ?>assets/template/site/img/wallet.jpg" alt="wallet" title="wallet" />
                                                    <?php }elseif($details->image !=''){ ?>
                                                        <a href="<?php echo $details->product_id != 0 ? base_url().$product_route.$details->route : '#';?>">
                                                            <img src="<?php echo base_url(); ?>assets/uploads/products/<?php echo $details->image; ?>" alt="<?php echo $details->name; ?>" title="<?php echo $details->name; ?>" />
                                                        </a>
                                                    <?php } ?>
                                                </div><!--img-prod --->
                                            </td>

                                            <td width="60%">
                                                <div class="data-prod">
                                                    <h5>
                                                        <a href="<?php echo $details->product_id != 0 ? base_url().$product_route.$details->route : '#';?>">
                                                            <?php if($details->type =='recharge_card')
                                                            {
                                                                echo $details->name;
                                                            }elseif($details->type =='product'){
                                                                echo $details->name;
                                                            }?>
                                                        </a>
                                                    </h5>
                                                    <div class="row no-margin">
                                                        <?php if(($details->price+$details->optional_fields_cost) != $details->final_price) { ?>
                                                            <div class="old-price">
                                                                <span><?php echo $details->price; ?> <?php echo $cart_data->currency_symbol; ?></span>
                                                            </div><!--old-price-->
                                                        <?php } ?>

                                                        <div class="price">
                                                            <span><?php echo $details->final_price; ?> <?php echo $cart_data->currency_symbol; ?></span>
                                                        </div><!--price-->
                                                    </div><!--row-->

                                                    <div class="row no-margin">
                                                        <div class="price">
                                                            <span>
                                                                <?php echo lang('reward_points'); ?> : <small><?php echo $details->reward_points; ?></small>
                                                            </span>
                                                        </div><!--price-->
                                                    </div><!--row-->
                                                    <?php if($details->weight != 0){?>
                                                        <div class="row no-margin">
                                                            <div class="price">
                                                                <span>
                                                                    <?php echo lang('weight'); ?> : <small><?php echo $details->weight; ?>KG</small>
                                                                </span>
                                                            </div><!--price-->
                                                        </div><!--row-->
                                                    <?php }?>
                                                </div><!--data-prod-->
                                            </td>

                                            <td width="20%">
                                                <div class="produc-num">
                                                    <?php if($details->type == 'product'){ ?>
                                                        <input type="number" name="qty[<?php echo $details->product_id; ?>]" class="qty form-control" id="qty_<?php echo $details->product_id; ?>" data-rowid="<?php echo $details->id; ?>" data-product_id="<?php echo $details->product_id; ?>" value="<?php echo $details->qty; ?>"  />
                                                        <div class="row no-margin">
                                                            <div id="qty_msg_<?php echo $details->product_id; ?>"></div>
                                                        </div>
                                                    <?php }elseif($details->type == 'recharge_card'){ ?>
                                                        <input type="number" name="qty[<?php echo $details->product_id; ?>]" class="qty form-control" id="qty_<?php echo $details->product_id; ?>" data-rowid="<?php echo $details->id; ?>" data-product_id="<?php echo $details->product_id; ?>" value="<?php echo $details->qty; ?>" readonly />
                                                    <?php }

                                                    if( ($details->price + $details->optional_fields_cost) != $details->final_price)
                                                    {
                                                        $before_discount_price = $details->price;
                                                    }
                                                    ?>
                                                </div><!--produc-num-->
                                            </td>

                                            <td width="10%">
                                                <div class="data-prod">
                                                    <div class="row no-margin">
                                                    <?php if( ($details->price+$details->optional_fields_cost) != $details->final_price) { ?>
                                                            <div class="old-price">
                                                                <span><?php echo $details->price * $details->qty; ?> <?php echo $cart_data->currency_symbol; ?></span>
                                                            </div><!--old-price-->
                                                        <?php } ?>

                                                        <div class="price">
                                                            <span id="field_total_<?php echo $details->product_id; ?>">
                                                                <?php echo $details->final_price * $details->qty; ?>
                                                            </span>
                                                            <span><?php echo $cart_data->currency_symbol; ?></span>
                                                        </div><!--price-->
                                                    </div><!--row-->
                                                </div><!--data-prod-->
                                            </td>

                                            <td width="10%">
                                                <div class="delete">
                                                    <a href="#" class="close_button btn btn-danger" id="delete_btn_<?php echo $details->product_id; ?>" data-product_id="<?php echo $details->product_id; ?>" data-cart_product_id="<?php echo $details->cart_product_id; ?>" title="<?php echo lang("delete"); ?>">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                </div><!--delete-->
                                            </td>
                                        </tr>

                                        <?php //echo'<pre>'; print_r($details->user_optional_fields); die();
                                        if(isset($details->user_optional_fields) && count($details->user_optional_fields) != 0){?>
                                        <?php foreach($details->user_optional_fields as $field){?>
                                            <tr>
                                                <td colspan="2">
                                                    <label><?php echo $field->label;?></label>
                                                </td>
                                                <td colspan="3">
                                                    <label>
                                                        <?php echo $field->product_optional_field_value;?>
                                                    </label>

                                                    <?php if($field->has_qty == 1){?>
                                                        <label>( <?php echo lang('quantity').' : '.$field->qty;?>) </label>
                                                    <?php }?>
                                                </td>
                                            </tr>
                                            <?php }
                                            }
                                        }?>

                                        <tr>
                                            <td colspan="2"></td>
                                            <td>
                                                <strong><?php echo lang('products_total_price');?></strong>
                                            </td>
                                            <td colspan="2">
                                                <strong><?php echo $store->store_final_total.' '.$cart_data->currency_symbol;?></strong>
                                            </td>
                                        </tr>
                                    <?php //}?>
    							        </table>
     	   	 				</div><!--row-->
                        <?php }?>
                    </div><!--container-cart-->
                </div><!--col-->

                <div class="col-md-3">
                    <div class="sum-cart">
                        <ul>
                                <li>
                                    <span><strong><?php echo lang('total');?></strong></span>
                                    <span>
                                        <span id="total_price"><?php echo $cart_data->total_price+$cart_data->optional_fields_cost;?> </span> <?php echo ' '.$cart_data->currency_symbol; ?>
                                    </span>
                                </li>

                                <?php if($cart_data->discount > 0) { ?>
                                <li id="coupon_discount">
                                    <span><strong><?php echo lang('total_discount');?></strong></span>
                                    <span>
                                        <span>- <?php echo $cart_data->discount.' '.$cart_data->currency_symbol; ?></span>
                                    </span>
                                </li>
                                <?php }?>

                                <?php if($cart_data->coupon_discount > 0) { ?>
                                <li id="coupon_discount">
                                    <span><strong><?php echo lang('coupon_discount');?></strong></span>
                                    <span>
                                        <span>- <?php echo $cart_data->coupon_discount.' '.$cart_data->currency_symbol; ?></span>
                                    </span>
                                </li>
                                <?php }?>

                                <?php if($cart_data->shipping_cost > 0) { ?>
                                <li>
                                    <span><strong><?php echo lang('shipping_cost');?></strong></span>
                                    <span><?php echo $cart_data->shipping_cost.' '.$cart_data->currency_symbol; ?></span>
                                </li>
                                <?php }?>

                                <?php if($cart_data->wrapping_cost > 0) { ?>
                                <li>
                                    <span><strong><?php echo lang('wrapping_cost');?></strong></span>
                                    <span><?php echo $cart_data->wrapping_cost.' '.$cart_data->currency_symbol; ?></span>
                                </li>
                                <?php }?>


                                <?php if($cart_data->tax > 0) { ?>
                                <li>
                                    <span><?php echo $tax_msg;?></span>
                                    <span>
                                        <span class="tax_value"><?php echo $cart_data->tax;?> </span> <?php echo ' '.$cart_data->currency_symbol; ?>
                                    </span>
                                </li>
                                <?php }?>

                                <li class="line"></li>

                                <li class="total">
                                    <span><?php echo lang('final_total');?></span>
                                    <span>
                                        <span id="final_price"><?php echo $cart_data->final_total_price_with_tax;?> </span> <?php echo ' '.$cart_data->currency_symbol; ?>
                                    </span>
                                </li>

                                <?php if(!$is_wholesaler && $cart_data->coupon_discount == 0){?>
                                    <li>
                                        <div class="coupon">
                                            <div class="row no-margin">
                                                <span><?php echo lang('apply_coupon_here');?></span>
                                                <br />
                                                <span id="coupon_msg" style="color: red;"></span>
                                            </div>

                                            <div class="input-group">
                                               <div class="col-md-5 no-padding">
                                                	<input type="text" name="coupon" id="coupon" placeholder="" class="form-control"  />
                                                </div> <!--col-->
                                                <div class="col-md-7 no-padding">
                                                  <input type="button" value="<?php echo lang('submit_coupon');?>" id="submit_coupon" data-loading-text="Loading..."  class="btn btn-default" />
                                                </div> <!--col-->
                                            </div>

                                        </div><!--coupon -->
                                    </li>
                                <?php }?>

                            <li>
                                <?php if(!isset($quantity_status_error) && !isset($max_per_user) && !isset($not_in_country) && $not_exceed_min_for_delivery  &&  !($order_error) ){?>
                                    <!--<a href="#" class="checkout">checkout</a>-->
                                    <input type="button" class="checkout btn btn-default" id="submit_order" value="<?php echo lang('finish_order');?>" />
                                <?php }else{?>
                                    <span class="button" id="" ><?php echo lang('finish_order');?></span>
                                <?php }?>
                            </li>
                        </ul>
                    </div><!--sum-cart -->
                </div><!--col-->
            </div><!--row-->
<?php /*
            <div class="row margin-bottom-20px">
                <div class="col-md-9">
                    <div class="row no-margin">
                        <div class="title-page">
                            <h4><?php echo lang('send_as_gift');?></h4>
                        </div><!--title-page-->

                        <div class="container-cart ">

                            <div class="form-group row margin-bottom-20px ">
                                <label class="col-sm-2 control-label" for="input-gift">
                                    <?php echo lang('send_as_gift');?>
                                </label>
                                <div class="col-sm-10">
                                    <label class="checkbox">
                                        <?php
                                        echo form_error('send_as_gift');
                                        if($cart_data->send_as_gift == 1)
                                        {
                                            $active_value = true ;
                                        } else {
                                            $active_value = false ;
                                        }

                                        $active_data  = array(
                                                                'name'           => 'send_as_gift'  ,
                                                                'class'          => 'make-switch send_gift'   ,
                                                                'id'             => 'send_gift_check',
                                                                'value'          => 1,
                                                                'checked'        => set_checkbox('send_as_gift', $active_value, $active_value),
                                                                'data-on-text'   => lang('yes'),
                                                                'data-off-text'  => lang('no'),
                                                                );
                                        echo form_checkbox($active_data);
                                        ?>
                                        <span></span>
                                    </label>
                                </div>
                            </div><!--form-group-->

                            <div class="col-sm-12 form-group gift_div">
                                <div class="col-sm-2"><label class="control-label" for="input-gift"><?php echo lang('box');?></label></div>
                                <div class="col-sm-10">
                                    <select name="box_id" class="form-control box_id select2">
                                        <option>----</option>
                                       <?php
                                       foreach($boxes as $bix_id=>$box){?>
                                        <option <?php echo isset($cart_data->box_id) && ($cart_data->box_id == $box->id) ? 'selected' : '';?>
                                               value="<?php echo $box->id;?>" style="background-image:url(assets/uploads/<?php echo $box->image;?>);">
                                               <?php echo $box->name;?></option>
                                       <?php }
                                    ?>
                                    </select>
                                </div>
                              </div>

                            <div class="col-sm-12 form-group gift_div">
                                <div class="col-sm-2"><label class="control-label" for="input-gift"><?php echo lang('wrapping');?></label></div>
                                <div class="col-sm-10">
                                    <select name="wrapping_id" class="form-control wrapping_id select2">
                                        <option>----</option>
                                        <?php

                                        foreach($wrapping as $row){?>
                                        <option <?php echo isset($cart_data->wrapping_id) && ($cart_data->wrapping_id == $row->id) ? 'selected' : '';?>
                                               value="<?php echo $row->id;?>" style="background-image:url(assets/uploads/<?php echo $row->image;?>);">
                                               <?php echo $row->name;?></option>
                                       <?php }
                                    ?>
                                    </select>
                                </div>
                              </div>

                              <div class="col-sm-12 form-group gift_div">
                                <div class="col-sm-2"><label class="control-label" for="input-gift"><?php echo lang('ribbon');?></label></div>
                                <div class="col-sm-10">
                                    <select name="ribbon_id" class="form-control ribbon_id select2">
                                        <option>----</option>
                                        <?php

                                        foreach($ribbons as $row){?>
                                        <option <?php echo isset($cart_data->ribbon_id) && ($cart_data->ribbon_id == $row->id) ? 'selected' : '';?>
                                               value="<?php echo $row->id;?>" style="background-image:url(assets/uploads/<?php echo $row->image;?>);">
                                               <?php echo $row->name;?></option>
                                        <?php }
                                    ?>
                                    </select>
                                </div>
                              </div>



                            <div class="form-group row margin-bottom-20px gift_div">
                                <label class="col-sm-2 control-label" for="input-gift">
                                    <?php echo lang('gift_msg');?>
                                </label>
                                <div class="col-sm-10">
                                    <?php
                                        $msg_data = array('name'=>"gift_msg" , 'class'=>"form-control" , 'value'=> isset($cart_data->gift_msg)? $cart_data->gift_msg : set_value("gift_msg"));
                                        echo form_textarea($msg_data);
                                    ?>
                                </div>
 							</div><!--form-group-->

                        </div><!--container-cart-->
                    </div><!--row-->
                </div><!--col-->
            </div><!--row-->
*/?>

            <div class="row margin-bottom-20px">
                <div class="col-md-9">
                    <div class="row no-margin">
                        <div class="title-page">
                            <h4><?php echo lang('payment_options');?></h4>
                        </div><!--title-page-->

                        <div class="container-cart">
                            <div class="validation_error" style="color: red;"></div>
                                <?php
                                foreach($payment_options as $option)
                                {
                                    // if banks
                                    if($option->id == 3)
                                    {?>
                                        <div class="row no-margin margin-bottom-10px">
                                            <?php
                                                echo form_error('payment_option_id');
                                                $payment_method_data = array(
                                                                                'name'  => "payment_option_id",
                                                                                'class' => 'bank_btn other_payment_options payment_options',
                                                                                'value' => $option->id,
                                                                                'id'    => 'bank_payment_selection'
                                                                             );

                                                if($cart_data->payment_option_id == $option->id)
                                                {
                                                    $payment_method_data['checked'] = set_radio('payment_option_id', $option->id, TRUE);
                                                }

                                                echo form_radio($payment_method_data);
                                            ?>

                                            <label for="options_input_<?php echo $option->id;?>"><?php echo $option->name;?>
                                                <?php if($option->image != ''){?> <img height="20" src="<?php echo base_url();?>assets/uploads/<?php echo $option->image;?>" /><?php }?>
                                            </label>
                                        </div><!--row-->

                                        <?php echo form_error('bank_id'); ?>

                                        <div id="bank_options_div" style="<?php echo ($cart_data->payment_option_id != $option->id)?'display: none;':''; ?> margin-left: 20px; overflow: hidden;">

                                            <?php foreach($bank_accounts as $account) { ?>
                                                <div class="row no-margin margin-bottom-10px">
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
                                                            <div class="row no-margin">
                                                                <div class="name_acc"> <?php echo lang('account_name');?> :</div>
                                                                <div class="name_a"><?php echo $account->bank_account_name;?></div>
                                                            </div><!--row-->

                                                            <div class="row no-margin">
                                                                <div class="name_acc"><?php echo lang('account_number');?> :</div>
                                                                <div class="name_a"><?php echo $account->bank_account_number; ?></div>
                                                            </div><!--row-->
                                                        </div>

                                                        <div class="user_bank_accounts" id="bank_acc_<?php echo $account->id;?>"></div>
                                                    </div><!--loader-->

                                                    <div class="user_account" id="user_acc_<?php echo $account->id;?>" >
                                                        <div class="row no-margin">
                                                            <?php
                                                            if(isset($account->user_bank_id))
                                                            {
                                                                echo form_hidden('user_bank_account_id', $account->user_bank_account_id);
                                                            }
                                                            ?>
                                                                <div class="row no-margin margin-top-10px">
                                                                    <div class="name_acc">
                                                                        <?php
                                                                            echo lang('your_account_name').' : ';
                                                                        ?>
                                                                    </div><!--name_acc-->
                                                                    <div class="name_a">
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
                                                                    </div><!--name_a-->
                                                                </div><!--row-->

                                                                <div class="row no-margin margin-top-10px">
                                                                    <div class="name_acc">
                                                                        <?php
                                                                            echo lang('your_account_number').' : ';
                                                                        ?>
                                                                    </div><!--name_acc-->

                                                                    <div class="name_a">
                                                                        <?php
                                                                            echo form_error('account_numer');
                                                                            $acc_number_att = array('name'=>"account_number[$account->id]", 'id'=>"account_number_$account->user_bank_account_id", 'style'=>'display:block', 'value'=> isset($account->user_bank_account_number)? $account->user_bank_account_number : set_value("account_code"), 'data-bank_id'=> $account->id);
                                                                            echo form_input($acc_number_att);
                                                                        ?>
                                                                            <input type="hidden" name="bank_id2" value="<?php echo $account->id;?>" data-bank_id="<?php echo $account->id;?>" />
                                                                    </div><!--name_a-->
                                                                </div><!--row-->
                                                            </div><!--row-->
                                                        </div><!--user_account-->
                                                    </div><!--bank_details_-->
                                                <?php }?>
                                        </div><!--bank_options-->
                                        <?php } else {?>
                                        <div class="row no-margin margin-bottom-10px">
                                                <?php
                                                    echo form_error('payment_option_id');
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
                                                                                    'id'    => $id
                                                                                );
                                                    if($cart_data->payment_option_id == $option->id)
                                                    {
                                                        $payment_method_data['checked'] = set_radio('payment_option_id', $option->id, TRUE);
                                                    }

                                                    echo form_radio($payment_method_data);
                                                ?>

                                                <label for="options_input_<?php echo $option->id;?>">
                                                    <?php echo $option->name;?>
                                                    <?php if($option->image != ''){?>
                                                        <img height="20" src="<?php echo base_url();?>assets/uploads/<?php echo $option->image;?>" />
                                                    <?php }?>
                                                </label>

                                                <?php if($option->id == 7){?>
                                                    <input name="voucher" type="text" value="<?php echo $cart_data->voucher_number; ?>" class="voucher_input"<?php if($cart_data->payment_option_id != 7){?> style="display: none;" <?php } ?> />
                                                <?php }?>
                                        </div><!--row-->
                                    <?php }
                                }?>
                        </div><!--container-cart-->
                    </div><!--row-->
                </div><!--col-->
            </div><!--row-->

            <?php if(isset($shipping)){?>
            <div class="row margin-bottom-20px">
                <div class="col-md-9">
                    <div class="row no-margin">
                        <div class="title-page">
                            <h4><?php echo lang('shipping');?></h4>
                        </div><!--title-page-->

                        <div class="container-cart">

                            <div class="form-group row margin-bottom-20px required">
                                <label class="col-sm-2 control-label" for="input-gift">
                                    <?php echo lang('shipping_way');?>
                                </label>
                                <div class="col-sm-10">
                                    <?php
                                    $shipping_method_id = $cart_data->shipping_type;
                                    echo form_dropdown('shipping_type', $shipping_methods, $shipping_method_id, 'class="shipping_options form-control" ');
                                    ?>
                                </div>
                            </div><!--form-group-->


                            <!-- Delivery From Home Div -->
                            <div class="delivery_form" style="display: none;">
                                <?php if($not_exceed_min_for_delivery){?>

                                    <div class="form-group row margin-bottom-20px required">
                                        <label class="col-sm-2 control-label" for="input-name">
                                            <?php echo lang('name');?>
                                        </label>
                                        <div class="col-sm-10">
                                            <?php
                                                echo form_error('district');

                                                $shipping_name_data = array(
                                                                             'name'  => 'shipping_name' ,
                                                                             'class' => 'form-control shipping_name',
                                                                             'id'    => '' ,
                                                                             'value' => isset($cart_data->shipping_name) && $cart_data->shipping_name != '' ? $cart_data->shipping_name : set_value('shipping_name')
                                                                           );

                                                echo form_input($shipping_name_data);
                                             ?>
                                        </div>
                                    </div><!--form-group-->

                                    <div class="form-group row margin-bottom-20px required">
                                        <label class="col-sm-2 control-label" for="input-phone">
                                            <?php echo lang('phone');?>
                                        </label>
                                        <div class="col-sm-10">
                                            <?php
                                                echo form_error('phone');

                                                $shipping_phone_data = array(
                                                                                 'name'  => 'shipping_phone'                ,
                                                                                 'class' => 'form-control shipping_phone'   ,
                                                                                 'id'    => '' ,
                                                                                 'value' => isset($cart_data->shipping_phone) && $cart_data->shipping_phone != '' ? $cart_data->shipping_phone : set_value('shipping_phone')
                                                                               );

                                                echo form_input($shipping_phone_data);
                                             ?>
                                        </div>
                                    </div><!--form-group-->

                                    <div class="form-group row margin-bottom-20px required">
                                        <label class="col-sm-2 control-label" for="input-shipping_city">
                                            <?php echo lang('shipping_city');?>
                                        </label>
                                        <div class="col-sm-10">
                                            <?php
                                                echo form_error('shipping_city');

                                                $city_id = isset($cart_data->shipping_city) ? $cart_data->shipping_city : set_value('shipping_city');
                                                echo form_dropdown('shipping_city', $cities, $city_id, 'class="form-control select2 shipping_city" id=""');
                                            ?>
                                        </div>
                                    </div><!--form-group-->

                                    <div class="form-group row margin-bottom-20px required">
                                        <label class="col-sm-2 control-label" for="input-shipping_address">
                                            <?php echo lang('shipping_address');?>
                                        </label>
                                        <div class="col-sm-10">
                                            <?php
                                                echo form_error('shipping_address');

                                                $shipping_address_data = array(
                                                                                'name'  => 'shipping_address'   ,
                                                                                'class' => 'form-control notes shipping_address' ,
                                                                                'id'    => ''   ,
                                                                                'value' => isset($cart_data->shipping_address) && $cart_data->shipping_address != '' ? $cart_data->shipping_address : set_value('shipping_address')
                                                                              );

                                                echo form_textarea($shipping_address_data);

                                            ?>
                                        </div>
                                    </div><!--form-group-->

                                    <div class="form-group row margin-bottom-20px required">
                                        <label class="col-sm-2 control-label" for="input-delivery_location">
                                            <?php echo lang('delivery_location');?>
                                        </label>
                                        <div class="col-sm-10">
                                            <?php
                                                echo form_error('delivery_location');
                                            ?>
                                            <input type="hidden" name="shipping_lng" />
                                            <input type="hidden" name="shipping_lat" />
                                            <div class="delivery_map"></div>
                                        </div>
                                    </div><!--form-group-->

                                    <div class="delivery_error_msg" style="color: red;"></div>
                                <?php }else{?>
                                    <span style="color: red; text-align: center;">
                                        <?php echo lang('min_order_delivery_note');?>
                                    </span>
                                <?php }?>
                            </div>

                            <!-- Delivery Company Div -->
                            <div class="shipping_form" style="display: none;">

                                <div class="form-group row margin-bottom-20px required">
                                    <label class="col-sm-2 control-label" for="input-name">
                                        <?php echo lang('name');?>
                                    </label>
                                    <div class="col-sm-10">
                                        <?php
                                            echo form_error('shipping_name');

                                            $shipping_name_data = array(
                                                                         'name'  => 'shipping_name' ,
                                                                         'class' => 'form-control shipping_name',
                                                                         'id'    => '' ,
                                                                         'value' => isset($cart_data->shipping_name) && $cart_data->shipping_name != '' ? $cart_data->shipping_name : set_value('shipping_name')
                                                                       );

                                            echo form_input($shipping_name_data);

                                         ?>
                                    </div>
                                </div><!--form-group-->

                                <div class="form-group row margin-bottom-20px required">
                                    <label class="col-sm-2 control-label" for="input-phone">
                                        <?php echo lang('phone');?>
                                    </label>
                                    <div class="col-sm-10">
                                        <?php
                                            echo form_error('phone');
                                            $shipping_phone_data = array(
                                                                             'name'  => 'shipping_phone'                ,
                                                                             'class' => 'form-control shipping_phone'   ,
                                                                             'id'    => '' ,
                                                                             'value' => isset($cart_data->shipping_phone) && $cart_data->shipping_phone != '' ? $cart_data->shipping_phone : set_value('shipping_phone')
                                                                           );
                                            echo form_input($shipping_phone_data);
                                        ?>
                                    </div>
                                </div><!--form-group-->


                                <div class="form-group row margin-bottom-20px required">
                                    <label class="col-sm-2 control-label" for="input-company_name">
                                        <?php echo lang('company_name');?>
                                    </label>
                                    <div class="col-sm-10">
                                        <?php
                                            echo form_error('shipping_company_id');

                                            $company_id = isset($cart_data->shipping_company_id) ? $cart_data->shipping_company_id : set_value('shipping_company_id') ;
                                            echo form_dropdown('shipping_company_id', $shipping_companies, $company_id, 'class="form-control select2" id="shipping_company"');
                                        ?>
                                    </div>
                                </div><!--form-group-->

                                <div class="form-group row margin-bottom-20px required">
                                    <label class="col-sm-2 control-label" for="input-shipping_country">
                                        <?php echo lang('shipping_country');?>
                                    </label>
                                    <div class="col-sm-10">
                                        <?php
                                            echo form_error('shipping_country');

                                            $cost_id = isset($cart_data->shipping_country_id) ? $cart_data->shipping_country_id : set_value('shipping_country');
                                            echo form_dropdown('shipping_country', $countries_costs, $cost_id, 'class="form-control select2" id="shipping_country"');
                                        ?>
                                    </div>
                                </div><!--form-group-->

                                <div class="form-group row margin-bottom-20px required">
                                    <label class="col-sm-2 control-label" for="input-shipping_city">
                                        <?php echo lang('shipping_city');?>
                                    </label>
                                    <div class="col-sm-10">
                                        <?php
                                            echo form_error('shipping_town');
                                            $shipping_town_data = array(
                                                                         'name'  => 'shipping_town' ,
                                                                         'class' => 'form-control shipping_town'  ,
                                                                         'id'    => '' ,
                                                                         'value' => isset($cart_data->shipping_town) && $cart_data->shipping_town != '' ? $cart_data->shipping_town : set_value('shipping_town')
                                                                       );
                                            echo form_input($shipping_town_data);
                                        ?>
                                    </div>
                                </div><!--form-group-->

                                <div class="form-group row margin-bottom-20px required">
                                    <label class="col-sm-2 control-label" for="input-shipping_district">
                                        <?php echo lang('shipping_district');?>
                                    </label>
                                    <div class="col-sm-10">
                                        <?php
                                            echo form_error('district');
                                            $shipping_district_data = array(
                                                                             'name'  => 'shipping_district' ,
                                                                             'class' => 'form-control shipping_district' ,
                                                                             'id'    => '' ,
                                                                             'value' => isset($cart_data->shipping_district) && $cart_data->shipping_district != '' ? $cart_data->shipping_district : set_value('shipping_district')
                                                                           );
                                            echo form_input($shipping_district_data);
                                        ?>
                                    </div>
                                </div><!--form-group-->

                                <div class="form-group row margin-bottom-20px required">
                                    <label class="col-sm-2 control-label" for="input-shipping_address">
                                        <?php echo lang('shipping_address');?>
                                    </label>
                                    <div class="col-sm-10">
                                        <?php
                                            echo form_error('shipping_address');
                                            $shipping_address_data = array(
                                                                            'name'  => 'shipping_address'   ,
                                                                            'class' => 'form-control notes shipping_address',
                                                                            'id'    => ''   ,
                                                                            'value' => isset($cart_data->shipping_address) && $cart_data->shipping_address != '' ? $cart_data->shipping_address : set_value('shipping_address')
                                                                          );
                                            echo form_textarea($shipping_address_data);
                                        ?>
                                    </div>
                                </div><!--form-group-->
                            </div>

                            <!-- Recieve from Branch Div -->
                            <div class="margin-bottom-20px">
                                <div class="row margin-bottom-20px locator" style="display: none;">
                                    <div class="title margin-bottom-20px">
                                        <label class="col-sm-12 control-label" for="input-">
                                            <?php echo lang('search_address');?>
                                        </label>
                                    </div><!--title-->
                                    <div class="col-sm-12 control-label">
                                        <?php $this->load->view('locator', $this->data);?>
                                    </div>
                                </div><!--locator-->

                                <div class="col-sm-12" style="display: none;">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title"><?php echo lang('map');?></h4>
                                        </div>
                                        <div class="">
                                            <div class="panel-body">
                                                <div class="delivery_map"></div>
                                            </div>
                                        </div>
                                    </div><!--panel panel-default-->
                                </div>
                            </div>

                            <!-- Delivery Company Div -->
                            <div class="user_address" style="display: none;">

                                <div class="form-group row margin-bottom-20px required">
                                    <label class="col-sm-2 control-label" for="input-shipping_country">
                                        <?php echo lang('user_address');?>
                                    </label>
                                    <div class="col-sm-10">
                                        <?php
                                            echo form_error('address_id');

                                            $address_id = isset($cart_data->address_id) ? $cart_data->address_id : set_value('address_id');
                                            echo form_dropdown('address_id', $user_addresses, $address_id, 'class="form-control select2" id="user_add"');
                                        ?>
                                    </div>
                                </div><!--form-group-->

                                <div class="col-sm-10">
                                  <a href="<?php echo base_url();?>users/user_address/list"><?php echo lang('address_list');?></a>
                                </div>

                            </div>

                        </div><!--container-cart-->
                    </div><!--row-->
                </div><!--col-->
            </div><!--row-->
            <?php } ?>

            <div class="row margin-bottom-20px">
                <div class="col-md-9">
                    <div class="row no-margin">
                        <div class="title-page">
                            <h4><?php echo lang('cost');?></h4>
                        </div><!--title-page-->

                        <div class="container-cart">
                            <table class="table table-bordered table-hover">

                                <tr>
                                    <td width="40%"><strong><?php echo lang("total"); ?></strong></td>
                                    <td class="text-center" width="60%">
                                        <span id="total_price"><?php echo $cart_data->total_price + $cart_data->optional_fields_cost; ?></span> <?php echo $cart_data->currency_symbol; ?>
                                    </td>
                                </tr>

                                <?php if($cart_data->discount > 0) { ?>
                                <tr id="coupon_discount">
                                    <td width="40%"><strong><?php echo lang('total_discount');?></strong></td>
                                    <td class="text-center" width="60%">
                                        <span>-<?php echo $cart_data->discount; ?></span> <?php echo $cart_data->currency_symbol; ?>
                                    </td>
                                </tr>
                                <?php }?>

                                <?php if($cart_data->coupon_discount > 0) { ?>
                                    <tr id="coupon_discount">
                                        <td width="40%"><strong><?php echo lang('coupon_discount');?></strong></td>
                                        <td class="text-center" width="60%">
                                            <span>-<?php echo $cart_data->coupon_discount; ?></span> <?php echo $cart_data->currency_symbol; ?>
                                        </td>
                                    </tr>
                                <?php }?>

                                <?php if($cart_data->shipping_cost > 0) { ?>
                                <tr>
                                    <td width="40%"><strong><?php echo lang('shipping_cost');?></strong></td>
                                    <td class="text-center" width="60%">
                                        <span><?php echo $cart_data->shipping_cost ; ?></span> <?php echo $cart_data->currency_symbol; ?>
                                    </td>
                                </tr>
                                <?php }?>

                                <?php if($cart_data->wrapping_cost > 0) { ?>
                                <tr>
                                    <td width="40%"><strong><?php echo lang('wrapping_cost');?></strong></td>
                                    <td class="text-center" width="60%">
                                        <span><?php echo $cart_data->wrapping_cost ; ?></span> <?php echo $cart_data->currency_symbol; ?>
                                    </td>
                                </tr>
                                <?php }?>

                                <?php if($cart_data->tax > 0) { ?>
                                <tr>
                                    <td width="40%"><strong><?php echo $tax_msg;?></strong></td>
                                    <td class="text-center" width="60%">
                                        <span class="tax_value"><?php echo $cart_data->tax ; ?></span> <?php echo $cart_data->currency_symbol; ?>
                                    </td>
                                </tr>
                                <?php }?>

                                <tr class="header-table">
                                    <td width="40%"><strong><?php echo lang('final_total');?></strong></td>
                                    <td class="text-center" width="60%">
                                        <span><?php echo $cart_data->final_total_price_with_tax ; ?></span> <?php echo $cart_data->currency_symbol; ?>
                                    </td>
                                </tr>

                            </table>

                        </div><!--container-cart-->
                    </div><!--row-->
                </div><!--col-->
            </div><!--row-->

            <div class="row margin-bottom-20px">
                <div class="col-md-9">
                    <div class="row no-margin">
                        <div class="title-page">
                            <h4><?php echo lang('comments');?></h4>
                        </div><!--title-page-->

                        <div class="container-cart">
                            <div class="form-group row margin-bottom-20px">
                                <label class="col-sm-2 control-label" for="input-shipping_country">
                                    <?php echo lang('comments');?>
                                </label>
                                <div class="col-sm-10">
                                    <textarea rows="4" class="form-control notes" name="notes"></textarea>
                                </div>
                            </div><!--form-group-->

                            <div class="form-group row">
                                <div class="col-sm-1">
                                    <label class="checkbox">
                                        <?php
                                        echo form_error('privacy_conditions');
                                        $priv_value   = true;
                                        $active_data  = array(
                                                               'name'           => 'privacy_conditions',
                                                               'id'             => 'priv',
                                                               'value'          => 1,
                                                               'checked'        => set_checkbox('privacy_conditions', true, true),
                                                               'data-on-text'   => lang('yes'),
                                                               'data-off-text'  => lang('no'),
                                                               'class'          => 'validate required'
                                                            );
                                        echo form_checkbox($active_data);
                                        ?>
                                        <span></span>
                                    </label>
                                </div>
                                <label class="control-label">
                                    <?php echo lang('read_and_accepted');?> <a class="agree" href="<?php echo base_url();?>static_pages/view/index/3" target="_blank"><b><?php echo lang('privacy_policy');?></b></a>
                                </label>
                            </div><!--form-group-->

                        </div><!--container-cart-->
                    </div><!--row-->
                </div><!--col-->
            </div><!--row-->

            <div class="col-md-12">
                <div class="sum-cart">
                    <ul>
                        <li>
                            <span><strong><?php echo lang('total');?></strong></span>
                            <span>
                                <span id="total_price"><?php echo $cart_data->total_price+$cart_data->optional_fields_cost;?> </span> <?php echo ' '.$cart_data->currency_symbol; ?>
                            </span>
                        </li>

                        <?php if($cart_data->discount > 0) { ?>
                        <li id="coupon_discount">
                            <span><strong><?php echo lang('total_discount');?></strong></span>
                            <span>
                                <span>- <?php echo $cart_data->discount.' '.$cart_data->currency_symbol; ?></span>
                            </span>
                        </li>
                        <?php }?>

                        <?php if($cart_data->coupon_discount > 0) { ?>
                        <li id="coupon_discount">
                            <span><strong><?php echo lang('coupon_discount');?></strong></span>
                            <span>
                                <span>- <?php echo $cart_data->coupon_discount.' '.$cart_data->currency_symbol; ?></span>
                            </span>
                        </li>
                        <?php }?>

                        <?php if($cart_data->shipping_cost > 0) { ?>
                        <li>
                            <span><strong><?php echo lang('shipping_cost');?></strong></span>
                            <span><?php echo $cart_data->shipping_cost.' '.$cart_data->currency_symbol; ?></span>
                        </li>
                        <?php }?>

                        <?php if($cart_data->wrapping_cost > 0) { ?>
                        <li>
                            <span><strong><?php echo lang('wrapping_cost');?></strong></span>
                            <span><?php echo $cart_data->wrapping_cost.' '.$cart_data->currency_symbol; ?></span>
                        </li>
                        <?php }?>


                        <?php if($cart_data->tax > 0) { ?>
                        <li>
                            <span><?php echo $tax_msg;?></span>
                            <span>
                                <span class="tax_value"><?php echo $cart_data->tax;?> </span> <?php echo ' '.$cart_data->currency_symbol; ?>
                            </span>
                        </li>
                        <?php }?>

                        <li class="line"></li>

                        <li class="total">
                            <span><?php echo lang('final_total');?></span>
                            <span>
                                <span id="final_price"><?php echo $cart_data->final_total_price_with_tax;?> </span> <?php echo ' '.$cart_data->currency_symbol; ?>
                            </span>
                        </li>

                        <?php if(!$is_wholesaler && $cart_data->coupon_discount == 0){?>
                            <li>
                                <div class="coupon">
                                    <div class="row no-margin">
                                        <span><?php echo lang('apply_coupon_here');?></span>
                                        <br />
                                        <span id="coupon_msg" style="color: red;"></span>
                                    </div>

                                    <div class="input-group">
                                       <div class="col-md-6 no-padding">
                                        	<input type="text" name="coupon" id="coupon" placeholder="" class="form-control"  />
                                        </div> <!--col-->
                                        <div class="col-md-5 no-padding">
                                            <input type="button" value="<?php echo lang('submit_coupon');?>" id="submit_coupon" data-loading-text="Loading..."  class="btn btn-default" />
                                        </div> <!--col-->
                                    </div>

                                </div><!--coupon -->
                            </li>
                        <?php }?>

                        <li>
                            <?php if(!isset($quantity_status_error) && !isset($max_per_user) && !isset($not_in_country) && $not_exceed_min_for_delivery  &&  !($order_error) ){?>
                                <!--<a href="#" class="checkout">checkout</a>-->
                                <input type="button" class="checkout btn btn-default" id="submit_order" value="<?php echo lang('finish_order');?>" />
                            <?php }else{?>
                                <span class="button" id="" ><?php echo lang('finish_order');?></span>
                            <?php }?>
                        </li>
                    </ul>
                </div><!--sum-cart -->
                </div>

       </div><!--row-->
       </form>
    <?php }?>

</div><!--cart_container_div-->


<?php if(isset($reset_coupon_msg)){?>
    <script>
        showToast('<?php echo $reset_coupon_msg ?>', '<?php echo lang('error');?>', 'error');
    </script>
<?php }?>

<?php if(! $this->data['is_logged_in']){?>
    <script>
    $(document).ready(function(){
            var country_id = $('#cart_country').val();
            var postData = {
                    id : country_id
               };

               $.post('<?php echo base_url()?>users/register/get_country_call_code/', postData, function(result){
                    $("#call_code").html(result);
               });

         });
    </script>
<?php }?>

<script>
    $(document).ready(function(){
       if($('.send_gift').is(":checked"))
       {
        $('.gift_div').show();
       }
       else
       {
        $('.gift_div').hide();
       }

     });
</script>


<script>

$( document ).ready(function() {


            //Home Delivery
            $('.delivery_form').show();
            //$('.locator').hide();
            //$('.shipping_form').hide();

});

$( document ).ready(function() {

        var shipping_type = '<?php echo $cart_data->shipping_type;?>';

        if(shipping_type == 1)
        {
            //Home Delivery
            $('.delivery_form').show();
            $('.locator').hide();
            $('.shipping_form').hide();
            $('.user_address').hide();

            // add location

            var map_inputs = '<input class="map_delivery" type="text" id="autocomplete" name="autocomplete" placeholder="Enter your address" style="width: 50%"><input type="button" class="map_delivery"  value="<?php echo lang('locate_location');?>" onclick="branchlocate();" />';

            $( ".delivery_map" ).append( map_inputs );
        }
        else if(shipping_type == 2)
        {
            // Recieve From Shop
            $('.delivery_form').hide();
            $('.locator').show();
            $('.shipping_form').hide();
            $('.user_address').hide();

            $( ".delivery_map" ).remove( );
        }
        else  if(shipping_type == 3)
        {
            //Shipping
            $('.delivery_form').hide();
            $('.locator').hide();
            $('.shipping_form').show();
            $('.user_address').hide();

            $( ".delivery_map" ).remove( );
        }
        else  if(shipping_type == 4)
        {
            //Shipping
            $('.delivery_form').hide();
            $('.locator').hide();
            $('.shipping_form').hide();
            $('.user_address').show();

            $( ".delivery_map" ).remove( );
        }
        else
        {
            //Hide all
            $('.delivery_form').hide();
            $('.locator').hide();
            $('.shipping_form').hide();

            $( ".delivery_map" ).remove( );

        }

        var numberOfStores = '<?php echo $checked_stores_count;?>';

        if(numberOfStores < 1)
        {
            $('#submit_order').hide();
        }
        else
        {
            $('#submit_order').show();
        }
    });


</script>
