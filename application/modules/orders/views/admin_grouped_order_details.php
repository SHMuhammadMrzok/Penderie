 <?php //echo '<pre>';print_r($_SESSION);die();?>
 <?php if($this->session->flashdata('send_sms_successfully')){?>
    <script>
        $( document ).ready(function(){
            showToast('<?php echo $this->session->flashdata('send_sms_successfully');?>','','success');
        });
     </script>
<?php }
if($this->session->flashdata('send_sms_error')){?>
    <script>
        $( document ).ready(function(){
            showToast('<?php echo $this->session->flashdata('send_sms_error');?>','','error');
        });
     </script>
<?php }?>
<?php if($this->session->flashdata('qty_error')){?>
    <script>
        $( document ).ready(function(){
            showToast('<?php echo $this->session->flashdata('qty_error');?>','','erroe');
        });
     </script>
<?php }?>

<?php if($this->session->flashdata('product_added_successfully')){?>
    <script>
        $( document ).ready(function(){
            showToast('<?php echo $this->session->flashdata('product_added_successfully');?>','','success');
        });
     </script>
<?php }?>


<style>
 .portlet-body{ height: auto;
 overflow: hidden;}
.dropdown-menu li{ direction: ltr;}
.menu ul li a:hover, .nav .open>a, .nav .open>a:hover, .nav .open>a:focus, .navbar-inverse .navbar-nav>.open>a, .navbar-inverse .navbar-nav>.open>a:hover, .navbar-inverse .navbar-nav>.open>a:focus, .navbar-nav>li>a:hover
{    background-color: #393939 !important;
    border-radius: 0px;
    box-shadow:none;
    transform: scale(1);
    -ms-transform: scale(1);
    -webkit-transform: scale(1);
    -webkit-transition: .5s ease-in-out;
    -moz-transition: .5s ease-in-out;
    -ms-transition: .5s ease-in-out;
    -o-transition: .5s ease-in-out;
    transition: .5s ease-in-out;
}
.page-breadcrumb{ direction: ltr;}
.navbar-nav>li>.dropdown-menu{ background: #fff;}
.dropdown-menu>li>a:hover, .dropdown-menu>li>a:focus{ color: #333 !important;}
table tr td{     vertical-align: middle !important;}
.title_h1,.title_order_page {
    text-align: left;
    background: #eee;
    line-height: 2;
    text-align: center;
    font-weight: bold;
}
.all,.all ~ td{   background:rgb(202, 228, 255); color:#000;
color:#000;
    font-weight: bold;
}
 </style>
 <?php /*
    if($grouped_orders_data->payment_method_id == 14)
    {
 ?>
    <center>
        <div class="col-md-12">
            <a href="<?php echo base_url();?>orders/later_payment/add_bill/<?php echo $grouped_orders_data->orders_number; ?>" style="border-radius: 20px;" class="btn green-meadow"><?php echo lang('add_bill');?></a>
        </div>
    </center>
    <?php } */ ?>
 <?php if(isset($error_msg)){?>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    	<div class="row no-margin">
        	<div class="iner_page">
                <span class="error"><?php echo $error_msg;?></span>
            </div>
         </div>
     </div>
 <?php }else{?>

    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    	<div class="row no-margin">
        	<div class="iner_page">


                <?php if($grouped_orders_data->is_pay_later_bill == 1){?>
                    <div class="row no-margin margin-top-20px">
                    	<h1 class="title_order_page">( <?php echo lang('order_bill').' #'.$grouped_orders_data->main_order_id;?> )</h1>
                    </div>
                <?php }?>

                <div class="row no-margin margin-top-20px">
                	<h1 class="title_order_page"><?php echo lang('user_details');?></h1>
                    <div class="row no-margin table_order">
                    	<table class="table table-striped table-hover table-bordered">
                            <?php if($grouped_orders_data->username != ''){?>

                                <tr>
                                	<td><?php echo lang('username');?></td>
                                    <td><?php echo $grouped_orders_data->first_name.' '.$grouped_orders_data->last_name;?></td>
                                </tr>

                            <?php }if($grouped_orders_data->email != ''){?>

                                <tr>
                                	<td><?php echo lang('email');?></td>
                                    <td><?php echo $grouped_orders_data->email;?></td>
                                </tr>

                            <?php }if($grouped_orders_data->phone != ''){?>

                                <tr>
                                	<td><?php echo lang('phone');?></td>
                                    <td><?php echo $grouped_orders_data->phone;?></td>
                                </tr>

                            <?php }if($grouped_orders_data->address != ''){?>

                                <tr>
                                	<td><?php echo lang('address');?></td>
                                    <td><?php echo $grouped_orders_data->address;?></td>
                                </tr>

                            <?php }if($grouped_orders_data->country != ''){?>

                                <tr>
                                	<td><?php echo lang('country');?></td>
                                    <td><?php echo $grouped_orders_data->country;?></td>
                                </tr>

                            <?php }if($grouped_orders_data->ip_address != ''){?>

                                <tr>
                                	<td><?php echo lang('ip_address');?></td>
                                    <td><?php echo $grouped_orders_data->ip_address;?></td>
                                </tr>

                             <?php }if($grouped_orders_data->agent != ''){?>

                                <tr>
                                	<td><?php echo lang('agent');?></td>
                                    <td><?php echo $grouped_orders_data->agent;?></td>
                                </tr>

                             <?php }if($grouped_orders_data->created_on != ''){?>

                                <tr>
                                	<td><?php echo lang('user_created_on');?></td>
                                    <td><?php echo date('Y/m/d', $grouped_orders_data->created_on);?></td>
                                </tr>

                            <?php }if($grouped_orders_data->last_login != ''){?>

                                <tr>
                                	<td><?php echo lang('user_last_login');?></td>
                                    <td><?php echo date('Y/m/d', $grouped_orders_data->last_login);?></td>
                                </tr>

                            <?php }if($grouped_orders_data->company != ''){?>

                                <tr>
                                	<td><?php echo lang('company');?></td>
                                    <td><?php echo $grouped_orders_data->company;?></td>
                                </tr>

                            <?php }if($grouped_orders_data->user_customer_group != ''){?>

                                <tr>
                                	<td><?php echo lang('customer_group');?></td>
                                    <td><?php echo $grouped_orders_data->user_customer_group;?></td>
                                </tr>

                            <?php }?>

                            <tr>
                            	<td><?php echo lang('pocket_money');?></td>
                                <td><?php echo $grouped_orders_data->user_balance;?></td>
                            </tr>

                            <tr>
                            	<td><?php echo lang('reward_points');?></td>
                                <td><?php echo $grouped_orders_data->user_points;?></td>
                            </tr>

                            <?php if($grouped_orders_data->user_previous_orders != ''){?>

                                <tr>
                                	<td><?php echo lang('user_previous_orders');?></td>
                                    <td><?php echo $grouped_orders_data->user_previous_orders;?></td>
                                </tr>

                            <?php }?>
                        </table>
                    </div><!--row-->
                </div><!--row-->

                <?php if(count($pre_orders_array) != 0){?>
                    <h1 class="title_h1"><?php echo lang('orders_history');?></h1>
                    <div class="row no-margin margin-top-20px">
                    	<table class="table table-striped table-hover table-bordered" style="text-align: center;">
                        	<tr class="header_tr">
                            	<td><?php echo lang('order_id');?></td>
                                <td><?php echo lang('products');?></td>
                                <td><?php echo lang('bank_name');?></td>
                                <?php /*<td><?php echo lang('account_name');?></td>
                                <td><?php echo lang('account_number');?></td>*/?>
                            </tr>

                            <?php foreach($pre_orders_array as $order){?>
                                <tr>
                                	<td><a href="<?php echo base_url();?>orders/admin_order/view_order/<?php echo $order->id;?>"><?php echo $order->id;?></a></td>
                                    <td> <?php echo $order->product_names;?></td>
                                    <td> <?php echo $order->bank_name;?></td>
                                    <?php /*<td> <?php echo $order->bank_account_name;?></td>
                                    <td> <?php echo $order->bank_account_number;?></td>*/?>
                                </tr>
                            <?php }?>

                        </table>
                    </div><!--row-->
                <?php }?>
                <!--<h1 class="title_h1"><?php echo lang('orders_info');?></h1>-->
                <div class="row no-margin margin-top-20px">
                	<h1 class="title_order_page"><?php echo lang('order_details');?></h1>
                    <div class="row no-margin table_order">
                    	<table class="table table-striped table-hover table-bordered">
                            <?php /*
                            <tr>
                            	<td><?php echo lang('order_number');?></td>
                                <td>#<?php echo $order_details->id;?></td>
                            </tr>

                            <tr>
                            	<td><?php echo lang('order_status');?></td>
                                <td><?php echo $order_details->status;?></td>
                            </tr>

                            <?php if($order_details->main_order_id != 0){?>
                                <tr>
                                	<td><?php echo lang('main_order_id');?></td>
                                    <td>
                                        <a href="<?php echo base_url();?>orders/admin_order/view_order/<?php echo $order_details->main_order_id;?>" target="_blank">
                                            #<?php echo $order_details->main_order_id;?>
                                        </a>
                                    </td>
                                </tr>
                            <?php }?>

                            <tr>
                            	<td><?php echo lang('name_of_store');?></td>
                                <td><?php echo $order_details->store_name;?></td>
                            </tr>

                            <?php if(isset($order_details->status_note) &&  $order_details->status_note != ''){?>
                                <tr>
                                	<td><?php echo lang('status_note');?></td>
                                    <td><?php echo $order_details->status_note;?></td>
                                </tr>
                            <?php }?>
                            */ ?>
                            
                            <?php if($grouped_orders_data->cart_id != 0){?>
                                <tr>
                                	<td><?php echo lang('transaction_id');?></td>
                                    <td>
                                        <?php echo $grouped_orders_data->cart_id;?>
                                    </td>
                                </tr>
                            <?php }?>

                            <tr>
                            	<td><?php echo lang('order_date');?></td>
                                <td><?php echo date('Y/m/d H:i',$grouped_orders_data->unix_time);?></td>
                            </tr>
                            <tr>
                            	<td><?php echo lang('payment_method');?></td>
                                <td><?php echo $payment_method->name;?><?php if($payment_method->image){?> <img height="20" src="<?php echo $this->data['images_path'] . $payment_method->image;?>" /><?php }?></td>
                            </tr>
                            <?php if($grouped_orders_data->payment_method_id == 3){?>
                                <tr>
                                	<td><?php echo lang('bank_name');?></td>
                                    <td><?php echo $bank_data->bank; ?></td>
                                </tr>
                                <tr>
                                	<td><?php echo lang('account_name');?></td>
                                    <td><?php echo $grouped_orders_data->bank_account_name; ?></td>
                                </tr>
                                <?php if($grouped_orders_data->bank_statement != ''){?>
                                  <tr>
                                  	<td><?php echo lang('bank_statement');?></td>
                                    <td>

                                        <img width="250px" src="<?php echo $this->data['images_path'].$grouped_orders_data->bank_statement;?>" />

                                    </td>
                                  </tr>
                              <?php }?>
                            <?php }elseif($grouped_orders_data->voucher != ''){?>
                                <tr>
                                	<td><?php echo lang('voucher_number');?></td>
                                    <td><?php echo $grouped_orders_data->voucher;?></td>
                                </tr>
                            <?php }?>

                            <?php if($grouped_orders_data->shipping_type != 0){?>
                                <tr>
                                	<td><?php echo lang('shipping_way');?></td>
                                    <td><?php echo $grouped_orders_data->shipping_type_lang;?></td>
                                </tr>
                            <?php }?>

                            <?php if($grouped_orders_data->shipping_company_id != 0){?>
                                <tr>
                                	<td><?php echo lang('shipping_company');?></td>
                                    <td><?php echo $grouped_orders_data->shipping_company;?></td>
                                </tr>
                            <?php }?>

                            <?php if($grouped_orders_data->shipping_country_id != 0){?>
                                <tr>
                                	<td><?php echo lang('shipping_country');?></td>
                                    <td><?php echo $grouped_orders_data->shipping_country;?></td>
                                </tr>
                            <?php }?>

                            <?php if($grouped_orders_data->shipping_city != ''){?>
                                <tr>
                                	<td><?php echo lang('shipping_city');?></td>
                                    <td><?php echo $grouped_orders_data->shipping_city;?></td>
                                </tr>
                            <?php }?>

                            <?php if($grouped_orders_data->shipping_district != ''){?>
                                <tr>
                                	<td><?php echo lang('shipping_district');?></td>
                                    <td><?php echo $grouped_orders_data->shipping_district;?></td>
                                </tr>
                            <?php }?>

                            <?php if($grouped_orders_data->shipping_address != ''){?>
                                <tr>
                                	<td><?php echo lang('address');?></td>
                                    <td><?php echo $grouped_orders_data->shipping_address;?></td>
                                </tr>
                            <?php }?>

                            <?php if($grouped_orders_data->lat != '' || $grouped_orders_data->lng != ''){?>
                                <tr>
                                	<td><?php echo lang('delivery_location');?></td>
                                    <td><a href="<?php echo 'https://www.google.com/maps/place/'.$grouped_orders_data->lat.','.$grouped_orders_data->lng;?>" target="_blank"><?php echo 'https://www.google.com/maps/place/'.$grouped_orders_data->lat.','.$grouped_orders_data->lng;?></a></td>
                                </tr>
                            <?php }?>

                            <?php if($grouped_orders_data->tracking_number && $grouped_orders_data->tracking_number != ''){?>
                                <tr>
                                	<td><?php echo lang('tracking_number');?></td>
                                    <td><?php echo $grouped_orders_data->tracking_number;?></td>
                                </tr>

                            <?php }?>


                            <?php if($grouped_orders_data->notes != ''){?>
                                <tr>
                                	<td><?php echo lang('notes');?></td>
                                    <td><?php echo $grouped_orders_data->notes;?></td>
                                </tr>
                            <?php }?>

                            <?php //delivery
                            if($grouped_orders_data->shipping_type == 1){?>
                                <tr>
                                	<td><?php echo lang('shipping_city');?></td>
                                    <td><?php echo $grouped_orders_data->city_name;?></td>
                                </tr>

                                <tr>
                                	<td><?php echo lang('phone');?></td>
                                    <td><?php echo $grouped_orders_data->shipping_phone;?></td>
                                </tr>

                                <tr>
                                	<td><?php echo lang('name');?></td>
                                    <td><?php echo $grouped_orders_data->shipping_name;?></td>
                                </tr>

                                <tr>
                                	<td><?php echo lang('address');?></td>
                                    <td><?php echo $grouped_orders_data->shipping_address;?></td>
                                </tr>
                            <?php }
                            elseif($grouped_orders_data->shipping_type == 2){?>
                                <tr>
                                	<td><?php echo lang('branch');?></td>
                                    <td><?php echo $grouped_orders_data->branch_name;?></td>
                                </tr>
                            <?php }
                            // Mrzok Edits
                            elseif($grouped_orders_data->shipping_type == 3){?>

                                <?php if($grouped_orders_data->order_shipping_city != '' && !is_int($grouped_orders_data->order_shipping_city)){?>
                                    <tr>
                                        <td><?php echo lang('shipping_city');?></td>
                                        <td><?php echo $grouped_orders_data->order_shipping_city;?></td>
                                    </tr>
                                <?php }?>

                                <?php if($grouped_orders_data->order_shipping_district != ''){?>
                                    <tr>
                                        <td><?php echo lang('shipping_district');?></td>
                                        <td><?php echo $grouped_orders_data->order_shipping_district;?></td>
                                    </tr>
                                <?php }?>

                                <?php if($grouped_orders_data->order_shipping_town != ''){?>
                                    <tr>
                                        <td><?php echo lang('shipping_town');?></td>
                                        <td><?php echo $grouped_orders_data->order_shipping_town;?></td>
                                    </tr>
                                <?php }?>

                                <?php if($grouped_orders_data->order_shipping_address != ''){?>
                                    <tr>
                                        <td><?php echo lang('shipping_address');?></td>
                                        <td><?php echo $grouped_orders_data->order_shipping_address;?></td>
                                    </tr>
                                <?php }?>

                                <?php if($grouped_orders_data->shipping_name != ''){?>
                                    <tr>
                                        <td><?php echo lang('name');?></td>
                                        <td><?php echo $grouped_orders_data->shipping_name;?></td>
                                    </tr>
                                <?php }?>

                                <?php if($grouped_orders_data->shipping_phone != ''){?>
                                    <tr>
                                        <td><?php echo lang('phone');?></td>
                                        <td><?php echo $grouped_orders_data->shipping_phone;?></td>
                                    </tr>
                                <?php }?>
                            <?php }
                            // End Edits
                            elseif($grouped_orders_data->shipping_type == 4){?>

                                <?php if(isset($grouped_orders_data->title) && $grouped_orders_data->title != ''){?>
                                    <tr>
                                        <td><?php echo lang('title');?></td>
                                        <td><?php echo $grouped_orders_data->title;?></td>
                                    </tr>
                                <?php }?>

                                <?php if(isset($grouped_orders_data->address) && $grouped_orders_data->address != ''){?>
                                    <tr>
                                        <td><?php echo lang('address');?></td>
                                        <td><?php echo $grouped_orders_data->address;?></td>
                                    </tr>
                                <?php }?>
                                
                            <?php }?>
                            
                            <?php if($grouped_orders_data->send_as_gift == 1 && isset($wrapping_data) && count($wrapping_data) != 0){?>
                                <?php if(isset($wrapping_data->wrapping_type)){ ?>
                                <tr>
                                    <td><?php echo lang('wrapping_type');?></td>
                                    <td><?php echo $wrapping_data->wrapping_type;?></td>
                                </tr>
                                <?php } ?>
                                
                                <?php if(isset($wrapping_data->ribbon_type)){ ?>
                                <tr>
                                    <td><?php echo lang('ribbon_type');?></td>
                                    <td><?php echo $wrapping_data->ribbon_type;?></td>
                                </tr>
                                <?php } ?>
                                
                                <?php if(isset($wrapping_data->box_size)){ ?>
                                <tr>
                                    <td><?php echo lang('box_size');?></td>
                                    <td><?php echo $wrapping_data->box_size;?></td>
                                </tr>
                                <?php } ?>
                                
                                <?php if(isset($wrapping_data->color)){ ?>
                                <tr>
                                    <td><?php echo lang('wrapping');?></td>
                                    <td><?php echo $wrapping_data->color;?></td>
                                </tr>
                                <?php } ?>
                                
                                <?php /*<tr>
                                    <td><?php echo lang('wrapping_cost');?></td>
                                    <td><?php echo $grouped_orders_data->wrapping_cost. ' '. $grouped_orders_data->currency_symbol;?></td>
                                </tr>*/?>
                                
                                <?php if(isset($grouped_orders_data->gift_msg)){ ?>
                                <tr>
                                    <td><?php echo lang('gift_msg');?></td>
                                    <td><?php echo $grouped_orders_data->gift_msg;?></td>
                                </tr>
                                <?php } ?>
                            <?php }?>


                        </table>
                    </div><!--row-->
                </div><!--row-->

                <div class="row no-margin margin-top-20px">
                    <table class="table table-striped table-hover table-bordered">

                        <?php $return_modals_array= array();
                        if(count($products_with_serials) != 0)
                        {
                        	foreach($products_with_serials as $product)
                            {
                        	   if($product->product_id == 0){?>
                                <tr class="header_tr">
                                	 <td></td>
                                   <td> <?php if($product->type == 'recharge_card'){
                                      echo lang('balance_recharge_card')." ".$product->final_price;
                                    }else if($product->type == 'package'){
                                      echo $product->title." ".$product->final_price;
                                    }?></td>
                                   <td><?php echo lang('price')." ".$product->final_price;?></td>
                                </tr>
                               <?php }else{?>
                                    <tr class="header_tr" style="background: rgb(108, 174, 241); color:#fff;">
                                        <td>
                                            <strong><?php echo $product->title;?></strong>

                                            <strong style="color:black;">
                                                <?php echo '<br>'.lang('name_of_store'). ' : '. $product->store_name;?>
                                            </strong>

                                            <strong style="color:brown;">
                                                <?php echo '<br>'.lang('order_number'). ' : #'. $product->order_id;?>
                                            </strong>

                                            <span>
                                                <?php echo '<br>'.lang('price'). ' : '. $product->final_price.' '.$grouped_orders_data->currency_symbol;?>
                                            </span>
                                            <span>
                                                <?php echo '<br>'.lang('final_total'). ' : '. $product->final_price*$product->qty.' '.$grouped_orders_data->currency_symbol;?>
                                            </span>
                                            <span>
                                                <?php echo '<br>'.lang('vat_value'). ' ( '.$product->vat_percent.'% ) : '. $product->vat_value.' '.$grouped_orders_data->currency_symbol.' <br />';?>
                                                <?php echo ($product->vat_type == 1)?lang('inclusive_vat'):lang('exclusive_vat');?>
                                            </span>
                                            <span>
                                                <?php echo '<br>'.lang('reward_points') .' : '. $product->reward_points;?>
                                            </span>
                                            <span>
                                                <?php echo '<br>'.lang('code') .' : '. $product->code;?>
                                            </span>
                                        </td>
                                        <td><img src="<?php echo $images_path . $product->image;?>" class="img-responsive" alt="img" style="width: 140px; height: 80px;" /></td>
                                    	   <td>
                                            <span><?php echo lang('quantity');?></span> :
                                            <?php
                                             if($edit_order && ! isset($product->non_serials_product)){ /*
                                                echo form_open('orders/admin_edit_order/update_quantity');
                                                    $quantity_data = array('type'=>'number', 'name'=> "quantity", 'class'=>"quantity_input", 'value'=>$product->qty, 'min'=> 1);
                                                    echo form_input($quantity_data);?>
                                                    <input type="hidden" name="order_id" value="<?php echo $product->order_id;?>" />
                                                    <input type="hidden" name="country_id" value="<?php echo $grouped_orders_data->country_id;?>" />
                                                    <input type="hidden" name="product_id" value="<?php echo $product->product_id;?>" />
                                                    <button type="submit" class="btn btn-primary" style="margin: auto 10px;"><?php echo lang('update_quantity');?></button>

                                            <?php  echo form_close(); */
                                             }else{?>
                                                <span><?php echo $product->qty;?></span>
                                            <?php }?>
                                        </td>
                                        <?php if($edit_order){?>
                                            <td width='7%' style="background: #e9f2fc;"><button type="button" class="btn red-sunglo remove_product"  name="serial_id"  data-order_id="<?php echo $product->order_id;?>" data-product_id="<?php echo $product->product_id;?>" data-price="<?php echo $product->price;?>" ><?php echo lang('remove_product');?></button></td>
                                        <?php }?>
                                    </tr>

                                    <!---------------------- Product Return ----------------->
                                    <?php /* if($product->return_status != 0){?>
                                        <tr class="title_h1" >
                                                <td>
                                                <?php echo lang('returned_product');?><br />
                                                <?php echo '( '.lang('quantity').' '.$product->returned_qty.' )';?><br />
                                                <?php
                                                if($get_order_message->notes != '')
                                                {
                                                    echo lang('notes').' : '.$get_order_message->notes;
                                                }
                                                ?>
                                                </td>

                                                <td colspan="2">
                                                <?php if($product->return_status == 1){
                                                    $return_modals_array[] = $product->order_product_id; ?>
                                                    <a href="#return_modal_<?php echo $product->order_product_id;?>" data-toggle="modal" data-oredr_product_id="<?php echo $product->order_product_id;?>" class="btn red-sunglo" style="margin-top: 3px;"><?php echo lang('update_return_status');?></a>
                                                <?php }else if($product->return_status == 2){?>
                                                    <span class="label label-success"><?php echo lang('accept');?></span>
                                                <?php }else if($product->return_status == 3){?>
                                                    <span class="label label-danger"><?php echo lang('reject');?></span>
                                                <?php }?>
                                            </td>
                                        </tr>
                                    <?php } */ ?>

                                    <!------------------------------Product Serials ---------------------------------->

                                    <?php
                                    if($product->order_status_id != 3 && $product->order_status_id != 4){
                                        if(isset($product->serials) && count($product->serials) != 0){?>
                                            <tr class="header_tr2" style="background:rgb(202, 228, 255); color:#000;">

                                                <td style="text-align: center;"><strong><?php echo lang('price');?></strong></td>
                                                <td style="text-align: center;"><strong><?php echo lang('serial');?></strong></td>
                                                <td style="text-align: center;"><strong><?php echo lang('item_sku');?></strong></td>
                                            </tr>
                                            <tbody class="product_serials_<?php echo $product->product_id;?>">
                                                <?php
                                                //if(isset($product->serials)){
                                                foreach($product->serials as $serial){?>
                                                    <tr class="serial_row_<?php echo $serial->product_serial_id;?>">
                                                        <td>
                                                            <del>
                                                            <?php
                                                            echo $product->price != $product->final_price ? $product->price.' '.$grouped_orders_data->currency_symbol.'<br>' : '';
                                                            ?>
                                                            </del>
                                                            <?php echo $product->final_price.' '.$grouped_orders_data->currency_symbol;?>
                                                        </td>
                                                        <td style="text-align: center;">
                                                            <?php echo $serial->dec_serial;?>
                                                            <?php /*
                                                            <?php if($serial->invalid == 0){?>
                                                            <div class="serial_<?php echo $serial->product_serial_id;?>">
                                                                <button type="button" class="btn yellow-crusta invalid_serial serial_<?php echo $serial->product_serial_id;?>" value="<?php echo $serial->product_serial_id;?>" name="serial_id" data-serial_id="<?php echo $serial->product_serial_id;?>" data-order_id="<?php echo $product->order_id;?>" data-product_id="<?php echo $serial->product_id;?>" data-price="<?php echo $product->price;?>" ><?php echo lang('invalid_serial');?></button>
                                                                <span style="display: block; font-size: 12px; font-family: tahoma;" class="msg_span serial_<?php echo $serial->product_serial_id;?>"><?php echo lang('invalid_serial_will_be_replaced');?></span>
                                                                <div><select style="display: none;" name="pocket_invalid_options" id="pocket_invalid_options<?php echo $serial->product_serial_id;?>" class="pocket_invalid_options"  data-serial_id="<?php echo $serial->product_serial_id;?>" data-order_id="<?php echo $product->order_id;?>" data-product_id="<?php echo $serial->product_id;?>" data-price="<?php echo $product->price;?>"></select></div>
                                                                <div><select style="display: none;" class="invalid_options" id="invalid_options<?php echo $serial->product_serial_id;?>" data-serial_id="<?php echo $serial->product_serial_id;?>" data-order_id="<?php echo $product->order_id;?>" data-product_id="<?php echo $serial->product_id;?>" data-price="<?php echo $product->price;?>"></select></div>
                                                            </div>
                                                            <?php }else{?>
                                                                <span style="color: red;"> <?php echo lang('invalid_serial');?> </span>
                                                            <?php }?>
                                                            */?>
                                                        </td>
                                                        <td style="text-align: center;">
                                                            <?php echo $serial->full_sku;?>
                                                        </td>

                                                    </tr>
                                                <?php }?>
                                            </tbody>

                                        <?php }
                                    } ?>


                                    <!------------------------------Product Selected Optional Options---------------------------------->

                                    <?php
                                        if(isset($product->user_optional_fields) && count($product->user_optional_fields) != 0){
                                            foreach($product->user_optional_fields as $field){
                                                ?>

                                                <tr style="border: solid;">
                                                    <td >
                                                        <label><?php echo $field->label;?></label>
                                                    </td>
                                                    <td colspan="2">
                                                        <label><?php echo $field->product_optional_field_value;?></label>
                                                        <?php if($field->has_qty == 1){?>
                                                            <span>( <?php echo lang('quantity').' : '. $field->qty;?> )</span>
                                                        <?php }?>
                                                    </td>
                                                </tr>
                                            <?php 
                                            }
                                        }
                                    ?>

                                    <?php 
                                }
                            }
                        }
                        if($charge_card)
                        {?>
                        <tr><td colspan="4"></td></tr>
                            <?php foreach($cards_data as $row){?>
                                <tr class="header_tr">
                                	 <?php if($row->type == 'recharge_card'){?>
                                     <td style="text-align: center;">
                                       <img src="<?php echo base_url().'assets/template/site/images/wallet.jpg';?>" width="50" />
                                     </td>
                                     <td> <?php echo lang('balance_recharge_card')." ".$row->price;?></td>
                                   <?php }elseif($row->type == 'package'){?>
                                     <td style="text-align: center;">
                                       <img src="<?php echo $this->data['images_path'].$row->image;?>" width="50" />
                                     </td>
                                     <td> <?php echo lang('renew_membership').' '. $row->title;?></td>
                                   <?php }?>
                                   <td><?php echo lang('group_price')." ".$row->price." ".$grouped_orders_data->currency_symbol;?></td>
                                </tr>
                            <?php }
                        }?>

                        <?php /*
                         if($edit_order){
                         $att=array('class'=> 'form-horizontal form-bordered');
                         echo form_open('orders/admin_edit_order/add_products', $att);
                                ?>
                                <div class="form-body">

                                    <span><?php echo lang('add_product');?></span>
                                    <div class="row" style="margin: 5px;">
                                        <label class="control-label col-md-3">
                                          <?php echo lang('product');?>
                                        </label>
                                        <div class="col-md-3">
                                           <?php echo form_dropdown('product_id', $country_other_products, 0, 'class="form-control select2 add_prod"');?>
                                        </div>
                                    </div>

                                    <?php echo form_hidden('order_id', $product->order_id);?>
                                    <?php echo form_hidden('country_id', $grouped_orders_data->country_id);?>


                                    <div class="row" style="margin: 5px;">
                                        <label class="control-label col-md-3">
                                          <?php echo lang('quantity');?>
                                        </label>
                                       <div class="col-md-3">
                                         <input name="qty" type="number" min="1" value="1" class="form-control"  />
                                       </div>
                                     </div>
                                     <div class="op_div"></div>

                                     <button type="submit" style="margin: 5px;" class="btn green-meadow"><?php echo lang('add');?></button>
                                </div>
                            <?php echo form_close();?>
                        <?php }*/?>
                        <tr><td colspan="4"></td></tr>

                        <tr>
                        	<td class="all"><?php echo lang('total');?></td>
                            <td colspan="5" class="all_td2"><span class="total"><?php echo $grouped_orders_data->total." ".$grouped_orders_data->currency_symbol;?></span></td>
                        </tr>

                        <?php if($grouped_orders_data->discount != 0){?>
                            <tr>
                            	<td class="all"><?php echo lang('total_discount');?></td>
                                <td colspan="5" class="all_td2">- <?php echo $grouped_orders_data->discount." ".$grouped_orders_data->currency_symbol;?></td>
                            </tr>
                        <?php }?>


                        <?php if($grouped_orders_data->coupon_discount != 0){?>
                            <tr>
                            	<td class="all"><?php echo lang('coupon_discount');?></td>
                                <td colspan="5" class="all_td2"><?php echo $grouped_orders_data->coupon_discount." ".$grouped_orders_data->currency_symbol;?></td>
                            </tr>
                        <?php }?>

                        <?php if($grouped_orders_data->maintenance_cost != 0){?>
                            <tr>
                            	<td class="all"><?php echo lang('maintenance_cost');?></td>
                                <td colspan="5" class="all_td2"><?php echo $grouped_orders_data->maintenance_cost." ".$grouped_orders_data->currency_symbol;?></td>
                            </tr>
                        <?php }?>

                        <?php if($grouped_orders_data->shipping_cost != 0){?>
                            <tr>
                            	<td   class="all"><?php echo lang('shipping_cost');?></td>
                                <td   colspan="5" class="all_td2"><?php echo $grouped_orders_data->shipping_cost." ".$grouped_orders_data->currency_symbol;?></td>
                            </tr>
                        <?php }?>

                        <?php if($grouped_orders_data->tax != 0){?>
                            <tr>
                            	<td   class="all"><?php echo lang('tax');?></td>
                                <td   colspan="5" class="all_td2"><?php echo $grouped_orders_data->tax." ".$grouped_orders_data->currency_symbol;?></td>
                            </tr>
                        <?php }?>

                        <?php if($grouped_orders_data->wrapping_cost != 0){?>
                            <tr>
                            	<td   class="all"><?php echo lang('wrapping_cost');?></td>
                                <td   colspan="5" class="all_td2"><?php echo $grouped_orders_data->wrapping_cost." ".$grouped_orders_data->currency_symbol;?></td>
                            </tr>
                        <?php }?>

                        <?php if($grouped_orders_data->vat_value != 0){?>
                          <tr>
                            <td class="all"><?php echo lang('vat_value');?></td>
                            <td colspan="5" class="all_td2"><?php echo $grouped_orders_data->vat_value." ".$grouped_orders_data->currency_symbol;?></td>
                          </tr>
                        <?php }?>

                        <tr>
                        	<td   class="all"><?php echo lang('final_total');?></td>
                            <td   colspan="5"  class="all_td2"><span class="final_total"><?php echo $grouped_orders_data->final_total." ".$grouped_orders_data->currency_symbol;?></span></td>
                        </tr>

                        <tr>
                        	<td class="all" colspan="6"><a  class="btn yellow-crusta" target="_blank" href="<?php echo base_url();?>orders/order/get_grouped_orders_reciept/<?php echo $grouped_orders_data->orders_number;?>"><?php echo lang('order_receipt');?></a></td>
                        </tr>

                        <?php if($grouped_orders_data->notes != ''){?>
                            <tr>
                            	<td   class="all"><?php echo lang('notes');?></td>
                                <td   colspan="5" class="all_td2"><?php echo $grouped_orders_data->notes;?></td>
                            </tr>
                        <?php }?>

                    </table>



                </div><!--row-->

                <?php /*
                <h1 class="title_h1"><?php echo lang('order_log');?></h1>
                <div class="row no-margin margin-top-20px">
                	<table class="table table-striped table-hover table-bordered" id="table">
                    	<tr class="header_tr">
                        	<td><?php echo lang('order_date');?></td>
                            <td><?php echo lang('order_status');?></td>
                        </tr>

                        <?php foreach($order_log as $log){?>
                            <tr>
                            	<td> <?php echo date('Y/m/d H:i', $log->unix_time);?></td>
                                <td><span class="label label-<?php echo $log->class;?>"><?php echo $log->name;?></span></td>
                            </tr>
                        <?php }?>

                    </table>
                </div><!--row-->
                */ ?>

                <?php /* if(count($edit_order_data) != 0){?>
                    <h1 class="title_h1"><?php echo lang('order_edit_log');?></h1>
                    <div class="row no-margin margin-top-20px">
                    	<table class="table table-striped table-hover table-bordered" id="table">
                        	<tr class="header_tr">
                            	<td><?php echo lang('name');?></td>
                                <td><?php echo lang('date');?></td>
                            </tr>

                            <?php foreach($edit_order_data as $log){?>
                                <tr>
                                	<td><?php echo $log->first_name.' '.$log->last_name;?></td>
                                    <td> <?php echo date('Y/m/d H:i', $log->unix_time);?></td>

                                </tr>
                            <?php }?>

                        </table>
                    </div><!--row-->
                <?php } */ ?>

                <?php /* //if($order_details->order_status_id != 1 && $order_details->order_status_id != 3){?>
                    <div class="row no-margin margin-top-20px">
                      <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                           <a href="#sms_modal" data-toggle="modal" class="btn red-sunglo" style="margin-top: 3px;"><?php echo lang('send_sms');?></a>
                           <!--<button type="button" class="btn red-sunglo" id="sms_modal" name="" ><?php echo ('send_sms');?></button>-->
                      </div><!--row-->
                    </div><!--row-->

                    <h1 class="title_h1"><?php echo lang('notes');?></h1>
                    <div class="row no-margin margin-top-20px">
                        <?php if(count($order_notes) != 0){?>
                            <table class="table table-striped table-hover table-bordered" id="table">
                            	<tr class="header_tr">
                                	<td><?php echo lang('comment');?></td>
                                    <td><?php echo lang('name');?></td>
                                    <td><?php echo lang('date');?></td>
                                </tr>

                                <?php foreach($order_notes as $note){?>
                                    <tr>
                                        <td><?php echo $note->comment;?></td>
                                        <td><?php echo $note->first_name.' ',$note->last_name;?> </td>
                                    	<td> <?php echo date('Y/m/d H:i', $note->unix_time);?></td>
                                    </tr>
                                <?php }?>

                            </table>
                        <?php }?>

                    	<form action="<?php echo base_url()."orders/admin_order/insert_order_note"?>" method="post">
                            <div class="row no-margin line_2">
                                <div class="col-lg-1 col-md-2 col-sm-2 col-xs-2">
                                    <?php
                                       echo form_label(lang('notes'), 'admin_note');
                                     ?>
                                </div><!--col-->
                                <div class="col-lg-7 col-md-8 col-sm-8 col-xs-10">
                                    <?php
                                      echo form_error("admin_note");
                                      //$status_id = isset($order_details->order_status_id) ? $order_details->order_status_id : set_value('status_id') ;
                                       $admin_note_data = array(
                                                      'name'        => 'admin_note'     ,
                                                      'class'       => 'form-control'   ,
                                                      'id'          => 'admin_note'     ,
                                                      //'value'       => $order_details->admin_note,
                                                      'rows'        => '5',
                                                      'cols'        => '10',
                                                      //'style'       => 'width:50%',
                                                    );
                                      echo form_textarea($admin_note_data);
                                    ?>
                                </div><!--col-->
                            </div><!--row-->


                                <?php echo form_hidden('order_id', $order_details->id);?>
                           </div>


                           <div class="row no-margin margin-top-20px">
                              <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">

                                   <button class="btn green"><?php echo lang('add');?></button>
                              </div><!--row-->
                           </div><!--row-->
                        </form>
                    </div><!--row-->
                <?php } */ ?>

                <!--maintenance cost form -->
                <?php /* if($order_details->order_status_id == 9 && isset($maintenance_product)){?>
                    <h1 class="title_h1"><?php echo lang('maintenance_cost');?></h1>
                    <div class="row no-margin margin-top-20px">
                        <form action="<?php echo base_url()."orders/admin_edit_order/insert_maintenance_cost"?>" method="post">

                            <div class="row no-margin line_2">
                                <div class="col-lg-1 col-md-2 col-sm-2 col-xs-2">
                                    <?php
                                       echo form_label(lang('cost'), 'cost');
                                     ?>
                                </div><!--col-->
                                <div class="col-lg-7 col-md-8 col-sm-8 col-xs-10">
                                    <?php
                                      echo form_error("cost");
                                      $cost_data = array(
                                                            'type'=>'text',
                                                            'name'=> "main_cost",
                                                            'class'=>"form-control",
                                                            );
                                      echo form_input($cost_data);?>

                                </div><!--col-->
                            </div><!--row-->
                            <input type="hidden" name="order_id" value="<?php echo $order_details->order_id;?>" />

                            <div class="row no-margin margin-top-20px">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <button class="btn green"><?php echo lang('submit');?></button>
                                </div><!--row-->
                            </div><!--row-->
                        </form>
                    </div><!--row-->
                <?php } */ ?>

                <?php /*if(! $is_driver){?>
                <h1 class="title_h1"><?php echo lang('add_driver');?></h1>
                <div class="row no-margin margin-top-20px">
                    <form action="<?php echo base_url()."orders/admin_order/add_driver"?>" method="post">
                        <div class="row no-margin line_2">
                            <div class="col-lg-1 col-md-2 col-sm-2 col-xs-2">
                                <?php
                                   echo form_label(lang('name'), 'driver_id');
                                 ?>
                            </div><!--col-->
                            <div class="col-lg-7 col-md-8 col-sm-8 col-xs-10">
                                <?php
                                  echo form_error("driver_id");
                                  $driver_id = isset($order_details->driver_id) ? $order_details->driver_id : set_value('driver_id') ;
                                  echo form_dropdown('driver_id', $drivers, $driver_id, 'class="form-control"');
                                ?>
                            </div><!--col-->
                        </div><!--row-->

                        <div class="row no-margin line_2">
                            <div class="col-lg-1 col-md-2 col-sm-2 col-xs-2">
                                <?php
                                   echo form_label(lang('send_sms'), 'send_sms');
                                 ?>
                            </div><!--col-->
                            <div class="col-lg-7 col-md-8 col-sm-8 col-xs-10">
                                <?php
                                echo form_error('send_sms');

                                $send_sms_data  = array(
                                                       'name'           => 'send_sms',
                                                       'class'          => 'make-switch',
                                                       'value'          => 1,
                                                       'checked'        => set_checkbox('send_sms', true, true),
                                                       'data-on-text'   => lang('yes'),
                                                       'data-off-text'  => lang('no'),
                                                     );

                                echo form_checkbox($send_sms_data);
                                ?>
                            </div><!--col-->
                        </div><!--row-->

                        <div style="margin-right: 100px;"></div>

                        <div class="row no-margin margin-top-20px">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                <?php echo form_hidden('order_id', $order_details->id);?>
                                <button class="btn green"><?php echo lang('submit');?></button>
                            </div><!--row-->
                        </div><!--row-->
                    </form>
                </div><!--row-->
                <?php }*/?>


                <?php /* if($order_details->order_status_id == 2 || $order_details->order_status_id == 8 || $order_details->order_status_id == 9 || ($order_details->order_status_id == 1)||$order_details->order_status_id == 12){?>
                    <h1 class="title_h1"><?php echo lang('change_staus');?></h1>
                    <div class="row no-margin margin-top-20px">
                        <form action="<?php echo base_url()."orders/admin_order/update_status"?>" method="post">

                            <div class="row no-margin line_2">
                                <div class="col-lg-1 col-md-2 col-sm-2 col-xs-2">
                                    <?php
                                       echo form_label(lang('shipping_company'), 'shipping_Company');
                                     ?>
                                </div><!--col-->
                                <div class="col-lg-7 col-md-8 col-sm-8 col-xs-10">
                                  <?php
                                     $default_shipping_company = isset($order_details->shipping_company_id) ? $order_details->shipping_company_id : $this->config->item('shipping_company_id');
                                     echo form_dropdown('shipping_Company', $shipping_compinies, $default_shipping_company, 'class="form-control"');
                                   ?>
                                </div>
                            </div><br />

                            <div class="row no-margin line_2">
                                <div class="col-lg-1 col-md-2 col-sm-2 col-xs-2">
                                    <?php
                                       echo form_label(lang('status'), 'status_id');
                                     ?>
                                </div><!--col-->
                                <div class="col-lg-7 col-md-8 col-sm-8 col-xs-10">
                                    <?php
                                      echo form_error("status_id");
                                      $status_id = isset($order_details->order_status_id) ? $order_details->order_status_id : set_value('status_id') ;
                                      echo form_dropdown('status_id', $status_options, $status_id, 'class="form-control"');
                                    ?>
                                </div><!--col-->
                            </div><!--row-->

                            <div class="row no-margin line_2" style="margin-top: 15px;">
                                <div class="col-lg-1 col-md-2 col-sm-2 col-xs-2">
                                    <?php
                                       echo form_label(lang('status_note'), 'status_note');
                                     ?>
                                </div><!--col-->
                                <div class="col-lg-7 col-md-8 col-sm-8 col-xs-10">
                                    <?php
                                      echo form_error("status_note");
                                      //$status_id = isset($order_details->order_status_id) ? $order_details->order_status_id : set_value('status_id') ;
                                       $status_note_data = array(
                                                      'name'        => 'status_note'    ,
                                                      'id'          => 'status_note'    ,
                                                      'class'       => 'form-control'   ,
                                                      //'value'       => $order_details->status_note,
                                                      'rows'        => '3',
                                                      'cols'        => '10',
                                                      //'style'       => 'width:50%',
                                                    );
                                      echo form_textarea($status_note_data);
                                    ?>
                                </div><!--col-->
                            </div>

                            <div style="margin-right: 100px;"></div>

                            <?php if($order_auto_cancel){?>
                                <div class="row no-margin margin-top-20px">
                                    <div class="form-group" style="margin: 15px 0px;">
                                        <label class="control-label col-md-3">
                                          <?php echo lang('order_auto_cancel');?>
                                        </label>

                                        <div class="col-md-4">
                                            <?php
                                              echo form_error('active');

                                              if($order_details->auto_cancel == 1)
                                              {
                                                $active_value = true;
                                              }
                                              elseif($order_details->auto_cancel == 0)
                                              {
                                                $active_value = false;
                                              }

                                              $active_data  = array(
                                                                     'name'           => 'active',
                                                                     'class'          => 'make-switch',
                                                                     'value'          => 1,
                                                                     'checked'        => set_checkbox('active', $active_value, $active_value),
                                                                     'data-on-text'   => lang('yes'),
                                                                     'data-off-text'  => lang('no'),
                                                                   );

                                              echo form_checkbox($active_data);
                                            ?>
                                        </div>
                                    </div>
                                    <?php echo form_hidden('order_id', $order_details->id);?>
                               </div>
                              <?php }?>
                            <div class="row no-margin margin-top-20px">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <button class="btn green"><?php echo lang('submit');?></button>
                                </div><!--row-->
                            </div><!--row-->
                        </form>
                    </div><!--row-->
                <?php } */ ?>

                <?php // if($order_details->order_status_id == 1 && $order_details->needs_shipping == 1){ // Old code => deal with order status "complete" ?>
                <?php if($enable_request_shipment) { // ($order_details->order_status_id == 12 && $order_details->needs_shipping == 1){ // Show Request for tracking no. when order status is "Out for delivery" => Mrzok edit?>

                <h1 class="title_h1" id="shipping_data"><?php echo lang('shipment_status');?></h1>
                <div class="row no-margin margin-top-20px">
                    <?php if($grouped_orders_data->delivered == 0){?>
                       <?php
                       $ship_date = 'none';
                        if($grouped_orders_data->shipping_company_id == 1)//smsa
                        {
                            $shipping_city          = 'block';
                            $preferred_receipt_time = 'block';
                        }
                        else if($grouped_orders_data->shipping_company_id == 2)//zajil
                        {
                            $shipping_city          = 'none';
                            $preferred_receipt_time = 'none';
                        }
                        else if($grouped_orders_data->shipping_company_id == 3)//aramex
                        {
                            $shipping_city          = 'block';
                            $preferred_receipt_time = 'block';
                            $ship_date = 'block';
                        }
                        else if($grouped_orders_data->shipping_company_id == 4)//aymakan
                        {
                            $shipping_city          = 'none';
                            $preferred_receipt_time = 'block';
                        }
                        else if($grouped_orders_data->shipping_company_id == 6)//Salasa
                        {
                            $shipping_city          = 'none';
                            $preferred_receipt_time = 'none';
                        }
                       ?>
                        <div class="row no-margin margin-top-20px">
                            <form method="post" action="<?php echo base_url();?>orders/shipping_gateways/create_shipping_request/">

                                <div class="row no-margin line_2" style="display: <?php echo $shipping_city ?>;">
                                    <div class="col-lg-1 col-md-2 col-sm-2 col-xs-2">
                                        <?php echo form_label(lang('shipping_city'), 'city');?>
                                    </div><!--col-->
                                    <div class="col-lg-7 col-md-8 col-sm-8 col-xs-10">
                                        <?php
                                          echo form_error("city");
                                          $city_id = isset($grouped_orders_data->shipping_city) ? $grouped_orders_data->shipping_city : set_value('shipping_city') ;
                                          echo form_dropdown('shipping_city', $cities_list, $city_id, 'class="form-control select2"');
                                        ?>
                                    </div><!--col-->
                                </div><!--row-->

                                <div class="row no-margin" style="margin-top: 15px; display: <?php echo $ship_date; ?>;">
                                    <div class="col-lg-1 col-md-2 col-sm-2 col-xs-2">
                                        <?php echo lang('ship_date');?>
                                    </div><!--col-->
                                    <div class="col-lg-7 col-md-8 col-sm-8 col-xs-10">
                                        <input <?php if($ship_date == 'block'){ echo 'required="required"'; } ?> class="form-control" type="datetime-local" id="ship_date" name="ship_date" /><br />
                                    </div>
                                </div><br />

                                <div class="row no-margin" style="margin-top: 15px; display: <?php echo $preferred_receipt_time ?>;">
                                    <div class="col-lg-1 col-md-2 col-sm-2 col-xs-2">
                                        <?php echo lang('preferred_receipt_time');?>
                                    </div><!--col-->
                                    <div class="col-lg-7 col-md-8 col-sm-8 col-xs-10">
                                        <input <?php if($preferred_receipt_time == 'block'){ echo 'required="required"'; } ?> class="form-control" type="datetime-local" id="preferred_receipt_time" name="preferred_receipt_time" /><br />
                                    </div>
                                </div><br />

                                <input type="hidden" name="order_id" value="<?php echo $grouped_orders_data->orders_number;?>" />
                                <input type="hidden" name="grouped_order" value="1" />
                                <input type="hidden" name="shipping_company_id" value="<?php echo $grouped_orders_data->shipping_company_id;?>" />
                                <button class="btn btn-sm green filter-submit margin-bottom"><?php echo lang('create_shipping_request');?></button>
                            </form>
                        </div>

                    <?php }else{?>

                            <div class="row no-margin margin-top-20px">
                                <div class="form-group" style="margin: 15px 0px;">
                                   <label class="control-label col-md-3"><?php echo lang('tracking_number');?></label>
                                   <div class="col-md-4">
                                      <span><?php echo $grouped_orders_data->tracking_number;?></span>
                                   </div>
                                </div><!--cost div-->
                            </div>

                            <?php
                            // $tracking_data = array();


                             /* if(count($tracking_data) != 0){
                            ?>
                                <table class="table table-striped table-hover table-bordered" style="margin: 10px;" >
                                	<tr class="header_tr">
                                    	<?php //<td><?php echo lang('status');</td> ?>
                                        <td><?php echo lang('check_date');?></td>
                                        <td><?php echo lang('response');?></td>
                                    </tr>

                                    <?php
                                    //echo"<pre>";print_r($tracking_data);die();
                                    foreach($tracking_data as $row){?>
                                        <tr>
                                            <?php //<td><?php //echo $row->status_name;</td>?>
                                            <td> <?php echo date('Y/m/d H:i', $row->unix_time);?></td>
                                            <td dir="rtl"><?php echo $row->decoded_response;?></td>
                                        </tr>
                                    <?php }?>

                                </table>
                            <?php } */ ?>
                                <?php /* if(isset($get_shipping_log['0']) && $get_shipping_log['0']->status_id == 5){ $disblay = 'none';}else{$disblay = 'block';} ?>
                                <div class="row no-margin margin-top-20px" style="margin: 10px;display:<?php echo $disblay; ?> ;">
                                    <form method="post" action="<?php echo base_url();?>orders/shipping_gateways/get_shipping_info">
                                        <input type="hidden" name="order_id" value="<?php echo $grouped_orders_data->related_orders_ids;?>" />
                                        <input type="hidden" name="admin" value="1" />
                                        <button class="btn btn-sm blue table-group-action-submit"><?php echo lang('shipment_status');?></button>
                                    </form>
                                </div>
                                <?php */ ?>

                                <?/*<form style="display:<?php echo $disblay; ?> ;" method="post" action="<?php echo base_url();?>orders/shipping_gateways/aramex_print">
                                    <input type="hidden" name="order_id" value="<?php echo $order_details->id;?>" />
                                    <button style="margin: 10px;" class="btn btn-sm red table-group-action-submit"><?php echo lang('print_policy');?></button>
                                </form>*/?>
                            <?php if($grouped_orders_data->shipping_company_id == 3){?>
                                <a class="btn btn-sm red table-group-action-submit" target="_blank" href="<?php echo base_url();?>orders/shipping_gateways/aramex_print/<?php echo $grouped_orders_data->orders_number;?>"><?php echo lang('print_policy');?></a>
                            <?php } ?>

                            <h1 style="display:<?php echo $disblay; ?> ;" class="title_h1" id="shipping_data"><?php echo lang('cancel_shipment');?></h1>
                            <div style="display:<?php echo $disblay; ?> ;" class="row no-margin line_2" >
                                <?php if($grouped_orders_data->shipping_company_id == 1){ //smsa?>
                                    <form method="post" action="<?php echo base_url();?>orders/shipping_gateways/cancel_smsa_shipment">
                                        <div class="col-lg-2 col-md-4 col-sm-4 col-xs-4">
                                            <label><?php echo lang('cancel_reason');?></label>
                                        </div>
                                        <div class="col-lg-7 col-md-8 col-sm-8 col-xs-10">
                                            <textarea required="required" name="cancel_reason" class="form-control" cols="6" rows="3"></textarea>
                                        </div>
                                        <input type="hidden" name="order_id" value="<?php echo $grouped_orders_data->orders_number;?>" />
                                        <input type="hidden" name="admin" value="1" />
                                        <button class="btn btn-sm red table-group-action-submit"><?php echo lang('cancel_shipment');?></button>
                                    </form>
                                <?php }
                                elseif($grouped_orders_data->shipping_company_id == 3){ //aramex?>
                                    <form method="post" action="<?php echo base_url();?>orders/shipping_gateways/cancel_aramex_shipment">
                                        <div class="col-lg-2 col-md-4 col-sm-4 col-xs-4">
                                            <label><?php echo lang('cancel_reason');?></label>
                                        </div>
                                        <div class="col-lg-7 col-md-8 col-sm-8 col-xs-10">
                                            <textarea required="required" name="cancel_reason" class="form-control" cols="6" rows="3"></textarea>
                                        </div>
                                        <input type="hidden" name="order_id" value="<?php echo $grouped_orders_data->orders_number;?>" />
                                        <input type="hidden" name="admin" value="1" />
                                        <button class="btn btn-sm red table-group-action-submit"><?php echo lang('cancel_shipment');?></button>
                                    </form>
                                <?php }
                                elseif($grouped_orders_data->shipping_company_id == 5){ //quick?>
                                    <form method="post" action="<?php echo base_url();?>orders/shipping_gateways/cancel_quick_shipment">
                                        <input type="hidden" name="order_id" value="<?php echo $grouped_orders_data->related_orders_ids;?>" />
                                        <input type="hidden" name="admin" value="1" />
                                        <button class="btn btn-sm red table-group-action-submit"><?php echo lang('cancel_shipment');?></button>
                                    </form>
                                <?php }
                                elseif($grouped_orders_data->shipping_company_id == 6){ // Cancel Salasa Shipment ?>
                                    <form method="post" action="<?php echo base_url();?>orders/shipping_gateways/cancel_salasa_shipment">
                                        <div class="col-lg-2 col-md-4 col-sm-4 col-xs-4">
                                            <label><?php echo lang('cancel_reason');?></label>
                                        </div>
                                        <div class="col-lg-7 col-md-8 col-sm-8 col-xs-10">
                                            <textarea required="required" name="cancel_reason" class="form-control" cols="6" rows="3"></textarea>
                                        </div>
                                        <input type="hidden" name="order_id" value="<?php echo $grouped_orders_data->orders_number;?>" />
                                        <input type="hidden" name="grouped_order" value="1" />
                                        <input type="hidden" name="admin" value="1" />
                                        <button class="btn btn-sm red table-group-action-submit"><?php echo lang('cancel_shipment');?></button>
                                    </form>
                                <?php } ?>
                            </div>




                    <?php }?>
                </div>
           <?php }?>

    	    </div><!--iner_page-->
        </div><!--row-->
    <?php }?>

</div><!--col-->
<div class="loading_modal"><!-- Place at bottom of page --></div>

<?php foreach($return_modals_array as $order_product_id){?>
  <div id="return_modal_<?php echo $order_product_id;?>" class="modal fade" tabindex="-1" aria-hidden="true">
  	<div class="modal-dialog">
  		<div class="modal-content">
  			<div class="modal-header">
  				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
  				<h4 class="modal-title"><?php echo lang('update_return_status');?></h4>
  			</div>
  			<div class="modal-body">
  				<div class="scroller" style="height:300px" data-always-visible="1" data-rail-visible1="1">
  					<div class="row">
  						<div class="col-md-12">
                <form action="<?php echo base_url();?>orders/admin_order/update_return_status" method="post" id="return_form">

                    <p><?php echo lang('status');?></p>
                    <p>
                      <select class="col-md-12 form-control" name="status_id">
                          <option value="2"><?php echo lang('accept');?></option>
                          <option value="3"><?php echo lang('reject');?></option>
                      </select>
                    </p>

                    <p><?php echo lang('return_to_sale_stock');?><p>
                    <p>
                        <?php
                            $return_to_sell_stock = array(
                                        'name'           => "return_to_sell_stock",
                                        'class'          => 'make-switch',
                                        'data-on-color'  => 'danger',
                                        'data-off-color' => 'default',
                                        'value'          => 1,
                                        'checked'        => false,
                                        'data-on-text'   => lang('yes'),
                                        'data-off-text'  => lang('no'),
                                        );
                            echo form_checkbox($return_to_sell_stock);
                        ?>
                    </p>
                    <hr>

                    <p><?php echo lang('notes');?></p>
                    <p>
                      <textarea class="col-md-12 form-control" name="notes"></textarea>
                    </p>


                    <div class="modal-footer">
                      <input type="hidden" name="order_id" value="<?php echo $order_details->id;?>" />
                      <input type="hidden" name="order_product_id" value="<?php echo $order_product_id;?>" />
                      <input type="hidden" name="order_country_id" value="<?php echo $order_details->country_id;?>" />
  		                <button type="button" class="btn green" id="return_submit"><?php echo lang('update');?></button>
                    </div>
                </form>
  						</div>
  					</div>
  				</div>
  			</div>
  			<div class="modal-footer">
  				<button type="button" data-dismiss="modal" class="btn default"><?php echo lang('close');?></button>
  			</div>
  		</div>
  	</div>
  </div>
<?php }?>

<?php /*
<div id="sms_modal" class="modal fade" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
				<h4 class="modal-title"><?php echo lang('send_user_sms');?></h4>
			</div>
			<div class="modal-body">
				<div class="scroller" style="height:300px" data-always-visible="1" data-rail-visible1="1">
					<div class="row">
						<div class="col-md-12">
                            <form action="<?php echo base_url();?>orders/admin_order/send_sms" method="post" id="sms_form">
                                <p><?php echo lang('phone');?></p>
    							<p>
    								<input type="text" class="col-md-12 form-control" name="phone" value="<?php echo $order_details->phone;?>" readonly="readonly" />
    							</p>
                                <p><?php echo lang('type');?></p>
                                <div>
                                    <label for="template"><?php echo lang('template_id');?></label>
    								<input type="radio" class="" name="type" value="1" id="template" />

                                    <label for="text_msg"><?php echo ('msg');?></label>
    								<input type="radio" class="" name="type" value="2" id="text_msg" />
                                </div>

                                <div style="display: none;" class="template_input">
                                    <p><?php echo lang('template_id');?></p>
        							<p>
        								<select class="col-md-12 form-control" name="template_id">
                                            <?php foreach($sms_templates as $template){?>
                                                <option value="<?php echo $template->id;?>"><?php echo $template->name;?></option>
                                            <?php }?>
                                        </select>
        							</p>
                                </div>
                                <div style="display: none;" class="text_input">
                                    <p><?php echo lang('msg_text');?></p>
        							<p>
        								<textarea class="col-md-12 form-control" name="msg_text"></textarea>
        							</p>
                                </div>
                                <input type="hidden" name="order_id" value="<?php echo $order_details->id;?>" />
                                <div class="modal-footer">
    							    <button type="button" class="btn green" id="form_submit"><?php echo lang('send');?></button>
                                </div>
                            </form>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" data-dismiss="modal" class="btn default"><?php echo lang('close');?></button>
			</div>
		</div>
	</div>
</div>
*/ ?>

<script>
///////////////////////Edit Products//////////////////////////

$('.remove_product').click(function(event){
    event.preventDefault();

    var postData = {
                       product_id : $(this).data('product_id'),
                       order_id   : $(this).data('order_id'),
                       country_id : <?php echo $order_details->country_id;?>
                   }

    $.post('<?php echo base_url()."orders/admin_edit_order/remove_product";?>', postData, function(result){

        window.location.reload();
    }, 'json');

});


$('body').on("change", '.add_prod', function(){
    var product_id = $( ".add_prod option:selected" ).val();
    postData = {
                product_id: product_id,
                user_id: '<?php echo $order_details->user_id;?>'
                };

    $.post('<?php echo base_url()."orders/admin_edit_order/get_product_optional_fields";?>', postData, function(result){
       $('.op_div').html(result);
    });

});

//////////////sms modal//////////////////////
$("#form_submit").click(function(){
    $( "#sms_form" ).submit();
});

$("#template").click(function(){
    $('.template_input').show();
    $('.text_input').hide();
});

$("#text_msg").click(function(){
    $('.template_input').hide();
    $('.text_input').show();
});

 //////////////return modal//////////////////////
$("#return_submit").click(function(){
    $( "#return_form" ).submit();
    });


</script>
