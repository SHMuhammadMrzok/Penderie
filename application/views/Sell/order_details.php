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
 
 <?php if(isset($error_msg)){?>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    	<div class="row no-margin">
        	<div class="iner_page">
                <span class="error"><?php echo $error_msg;?></span>
            </div>
         </div>
     </div>
 <?php }else{?>
    
        <div class="col-12 dashboard-left">
            <div class="row">
                <div class="col-md-6">
                    <div class="title">
                        <h3><?php echo lang('user_details');?></h3>
                    </div>
                    <div class="table-area">
                        <table class="table table-striped table-bordered table-hover">
                            <tbody>
                            
                                <?php if($order_details->first_name != ''){?>
                                
                                    <tr>
                                    	<td scope="row"><?php echo lang('username');?></td>
                                        <td <?php echo ($this->session->userdata('direction')=='ltr')?'style="text-align:left;"':'';?>><?php echo $order_details->first_name.' '.$order_details->last_name;?></td>
                                    </tr>
                                    
                                <?php }if($order_details->email != ''){?>
                                    
                                    <tr>
                                    	<td scope="row"><?php echo lang('email');?></td>
                                        <td <?php echo ($this->session->userdata('direction')=='ltr')?'style="text-align:left;"':'';?>><?php echo $order_details->email;?></td>
                                    </tr>
                                
                                <?php }if($order_details->phone != ''){?>
                                    
                                    <tr>
                                    	<td scope="row"><?php echo lang('phone');?></td>
                                        <td <?php echo ($this->session->userdata('direction')=='ltr')?'style="text-align:left;"':'';?>><?php echo $order_details->phone;?></td>
                                    </tr>
                                
                                <?php }if($order_details->address != ''){?>
                                    
                                    <tr>
                                    	<td scope="row"><?php echo lang('address');?></td>
                                        <td <?php echo ($this->session->userdata('direction')=='ltr')?'style="text-align:left;"':'';?>><?php echo $order_details->address;?></td>
                                    </tr>
                                
                                <?php }if($order_details->country != ''){?>
                                    
                                    <tr>
                                    	<td scope="row"><?php echo lang('country');?></td>
                                        <td <?php echo ($this->session->userdata('direction')=='ltr')?'style="text-align:left;"':'';?>><?php echo $order_details->country;?></td>
                                    </tr>
                               
                                <?php }if($order_details->ip_address != ''){?>
                                    
                                    <tr>
                                    	<td scope="row"><?php echo lang('ip_address');?></td>
                                        <td <?php echo ($this->session->userdata('direction')=='ltr')?'style="text-align:left;"':'';?>><?php echo $order_details->ip_address;?></td>
                                    </tr>
                                 
                                 <?php }if($order_details->agent != ''){?>
                                    
                                    <tr>
                                    	<td scope="row"><?php echo lang('agent');?></td>
                                        <td <?php echo ($this->session->userdata('direction')=='ltr')?'style="text-align:left;"':'';?>><?php echo $order_details->agent;?></td>
                                    </tr>
                                 
                                 <?php }if($order_details->created_on != ''){?>
                                    
                                    <tr>
                                    	<td scope="row"><?php echo lang('user_created_on');?></td>
                                        <td <?php echo ($this->session->userdata('direction')=='ltr')?'style="text-align:left;"':'';?>><?php echo date('Y/m/d', $order_details->created_on);?></td>
                                    </tr>
                               
                                <?php }if($order_details->last_login != ''){?>
                                    
                                    <tr>
                                    	<td scope="row"><?php echo lang('user_last_login');?></td>
                                        <td <?php echo ($this->session->userdata('direction')=='ltr')?'style="text-align:left;"':'';?>><?php echo date('Y/m/d', $order_details->last_login);?></td>
                                    </tr>
                                    
                                <?php }if($order_details->company != ''){?>
                                    
                                    <tr>
                                    	<td scope="row"><?php echo lang('company');?></td>
                                        <td <?php echo ($this->session->userdata('direction')=='ltr')?'style="text-align:left;"':'';?>><?php echo $order_details->company;?></td>
                                    </tr>
                                    
                                <?php }if($order_details->user_customer_group != ''){?>
                                    
                                    <tr>
                                    	<td scope="row"><?php echo lang('customer_group');?></td>
                                        <td <?php echo ($this->session->userdata('direction')=='ltr')?'style="text-align:left;"':'';?>><?php echo $order_details->user_customer_group;?></td>
                                    </tr>
                                
                                <?php }?>
                                
                                <tr>
                                	<td scope="row"><?php echo lang('pocket_money');?></td>
                                    <td><?php echo $order_details->user_balance;?></td>
                                </tr>
                                   
                                <tr>
                                	<td scope="row"><?php echo lang('reward_points');?></td>
                                    <td><?php echo $order_details->user_points;?></td>
                                </tr>
                                
                                <?php if($order_details->user_previous_orders != ''){?>
                                    
                                    <tr>
                                    	<td scope="row"><?php echo lang('user_previous_orders');?></td>
                                        <td <?php echo ($this->session->userdata('direction')=='ltr')?'style="text-align:left;"':'';?>><?php echo $order_details->user_previous_orders;?></td>
                                    </tr>
                                    
                                <?php }?>

                            </tbody>
                        </table>
                    </div>
                </div><!--col-->
                
                <div class="col-md-6">
                    <div class="title">
                        <h3><?php echo lang('order_details');?></h3>
                    </div>
                    <div class="table-area">
                        <table class="table table-striped table-bordered table-hover">
                            <tbody>
                                <tr>
                                	<td scope="row"><?php echo lang('order_number');?></td>
                                    <td>#<?php echo $order_details->id;?></td>
                                </tr>
                                
                                <tr>
                                	<td scope="row"><?php echo lang('order_status');?></td>
                                    <td><?php echo $order_details->status;?></td>
                                </tr>
                                
                                <tr>
                                	<td scope="row"><?php echo lang('name_of_store');?></td>
                                    <td><?php echo $order_details->store_name;?></td>
                                </tr>
                                
                                <?php if(isset($order_details->status_note) &&  $order_details->status_note != ''){?>
                                    <tr>
                                    	<td scope="row"><?php echo lang('status_note');?></td>
                                        <td><?php echo $order_details->status_note;?></td>
                                    </tr>
                                <?php }?>
                                
                                <tr>
                                	<td scope="row"><?php echo lang('order_date');?></td>
                                    <td><?php echo date('Y/m/d H:i',$order_details->unix_time);?></td>
                                </tr>
                                <tr>
                                	<td scope="row"><?php echo lang('payment_method');?></td>
                                    <td><?php echo $payment_method->name;?><?php if($payment_method->image){?> <img height="20" src="<?php echo base_url();?>assets/uploads/<?php echo $payment_method->image;?>" /><?php }?></td>
                                </tr>
                                <?php if($order_details->payment_method_id == 3){?>
                                    <tr>
                                    	<td scope="row"><?php echo lang('bank_name');?></td>
                                        <td><?php echo $bank_data->bank; ?></td>
                                    </tr>
                                    <tr>
                                    	<td scope="row"><?php echo lang('account_name');?></td>
                                        <td><?php echo $order_details->bank_account_name; ?></td>
                                    </tr>
                                    <tr>
                                    	<td scope="row"><?php echo lang('account_number');?></td>
                                        <td><?php echo $order_details->bank_account_number;?></td>
                                    </tr>
                                <?php }elseif($order_details->voucher != ''){?>
                                    <tr>
                                    	<td scope="row"><?php echo lang('voucher_number');?></td>
                                        <td><?php echo $order_details->voucher;?></td>
                                    </tr>
                                <?php }?>
                                
                                <?php if($order_details->shipping_company_id != 0){?>
                                    <tr>
                                    	<td scope="row"><?php echo lang('shipping_company');?></td>
                                        <td><?php echo $order_details->shipping_company;?></td>
                                    </tr>
                                <?php }?>
                                
                                <?php if($order_details->shipping_country_id != 0){?>
                                    <tr>
                                    	<td scope="row"><?php echo lang('shipping_country');?></td>
                                        <td><?php echo $order_details->shipping_country;?></td>
                                    </tr>
                                <?php }?>
                                
                                <?php if($order_details->shipping_city != ''){?>
                                    <tr>
                                    	<td scope="row"><?php echo lang('shipping_city');?></td>
                                        <td><?php echo $order_details->shipping_city;?></td>
                                    </tr>
                                <?php }?>
                                
                                <?php if($order_details->shipping_district != ''){?>
                                    <tr>
                                    	<td scope="row"><?php echo lang('shipping_district');?></td>
                                        <td><?php echo $order_details->shipping_district;?></td>
                                    </tr>
                                <?php }?>
                                
                                <?php if($order_details->shipping_address != ''){?>
                                    <tr>
                                    	<td scope="row"><?php echo lang('shipping_address');?></td>
                                        <td><?php echo $order_details->shipping_address;?></td>
                                    </tr>
                                <?php }?>
                                
                                <?php if($order_details->shipping_lat != '' || $order_details->shipping_lng != ''){?>
                                    <tr>
                                    	<td scope="row"><?php echo lang('delivery_location');?></td>
                                        <td><a href="<?php echo 'https://www.google.com/maps/place/'.$order_details->shipping_lat.','.$order_details->shipping_lng;?>" target="_blank"><?php echo 'https://www.google.com/maps/place/'.$order_details->shipping_lat.','.$order_details->shipping_lng;?></a></td>
                                    </tr>
                                <?php }?>
                                
                                <?php if($order_details->tracking_number && $order_details->tracking_number != ''){?>
                                    <tr>
                                    	<td scope="row"><?php echo lang('tracking_number');?></td>
                                        <td><?php echo $order_details->tracking_number;?></td>
                                    </tr>
                                    
                                <?php }?>
                                
                                <?php if($order_details->notes != ''){?>
                                    <tr>
                                    	<td scope="row"><?php echo lang('notes');?></td>
                                        <td><?php echo $order_details->notes;?></td>
                                    </tr>
                                <?php }?>
                                
                                <?php if($order_details->shipping_type != 0){?>
                                    <tr>
                                    	<td scope="row"><?php echo lang('shipping_way');?></td>
                                        <td><?php echo $order_details->shipping_type_lang;?></td>
                                    </tr>
                                <?php }?>
                                
                                <?php //delivery
                                 if($order_details->shipping_type == 1){?>
                                    <tr>
                                    	<td scope="row"><?php echo lang('shipping_city');?></td>
                                        <td><?php echo $order_details->city_name;?></td>
                                    </tr>
                                    
                                    <tr>
                                    	<td scope="row"><?php echo lang('phone');?></td>
                                        <td><?php echo $order_details->shipping_phone;?></td>
                                    </tr>
                                    
                                    <tr>
                                    	<td scope="row"><?php echo lang('name');?></td>
                                        <td><?php echo $order_details->shipping_name;?></td>
                                    </tr>
                                    
                                    <tr>
                                    	<td scope="row"><?php echo lang('address');?></td>
                                        <td><?php echo $order_details->shipping_address;?></td>
                                    </tr>
                                <?php }
                                elseif($order_details->shipping_type == 2){?>
                                    <tr>
                                    	<td scope="row"><?php echo lang('branch');?></td>
                                        <td><?php echo $order_details->branch_name;?></td>
                                    </tr>
                                <?php }?> 
                                
                                <?php if($order_details->send_gift == 1){?>
                                    <tr>
                                        <td scope="row"><?php echo lang('wrapping_type');?></td>
                                        <td><?php echo $wrapping_data->color;?></td>
                                    </tr>
                                    <tr>
                                        <td scope="row"><?php echo lang('ribbon_type');?></td>
                                        <td><?php echo $wrapping_data->color;?></td>
                                    </tr>
                                    <tr>
                                        <td scope="row"><?php echo lang('box_size');?></td>
                                        <td><?php echo $wrapping_data->color;?></td>
                                    </tr>
                                    <tr>
                                        <td scope="row"><?php echo lang('wrapping_cost');?></td>
                                        <td><?php echo $order_details->wrapping_cost. ' '. $order_details->currency_symbol;?></td>
                                    </tr>
                                <?php }?>
                            </tbody>
                        </table>
                    </div>
                </div><!--col-->
            </div><!--row-->
            
            <?php /*if(count($pre_orders_array) != 0){?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="title">
                            <h3><?php echo lang('user_previous_orders');?></h3>
                        </div>
                        <div class="table-area">
                            <table class="table table-striped table-bordered table-hover">
                                <tbody>
                                    <tr class="header-ta">
                                    	<td><?php echo lang('order_id');?></td>
                                        <td><?php echo lang('products');?></td>
                                        <td><?php echo lang('bank_name');?></td>
                                        <td><?php echo lang('account_name');?></td>
                                        <td><?php echo lang('account_number');?></td>
                                    </tr>
                                    <?php foreach($pre_orders_array as $order){?>
                                        <tr>
                                        	<td><a href="<?php echo base_url();?>orders/admin_order/view_order/<?php echo $order->id;?>"><?php echo $order->id;?></a></td>
                                            <td> <?php echo $order->product_names;?></td>
                                            <td> <?php echo $order->bank_name;?></td>
                                            <td> <?php echo $order->bank_account_name;?></td>
                                            <td> <?php echo $order->bank_account_number;?></td>
                                        </tr>
                                    <?php }?>
                                </tbody>
                            </table>
                        </div>
                    </div><!--col-->
                </div><!--row-->
            <?php }*/
            /*if($edit_order){
                $att=array('class'=> 'form-horizontal form-bordered');
                echo form_open('orders/admin_edit_order/add_products', $att);
            ?>
                <div class="row no-gutters">
                    <div class="add-prod-pruc">
                        <h2 class="tit"><?php echo lang('add_product');?></h2>
                        <div class="pr-inputs">
                            <div class="form-group">
                                <div class="row no-gutters">
                                    <label  class="col-md-2" for="prod"><?php echo lang('product');?></label>
                                    <?php 
                                        echo form_dropdown('product_id', $country_other_products, 0, 'class="col-md-10 form-control"');// select2
                                    ?>
                                </div><!--row-->
                            </div><!--form-group-->
                            <div class="form-group">
                                <div class="row no-gutters">
                                    <label class="col-md-2" for="quant"><?php echo lang('quantity');?></label>
                                    <input type="number" name="qty" min="1" value="1" class="col-md-10 form-control"  />
                                </div><!--row-->
                            </div>
                            
                            <div class="form-group">
                                <div class="row no-gutters align-items-left">
                                    <?php echo form_hidden('order_id', $order_details->id);?>
                                    <?php echo form_hidden('country_id', $order_details->country_id);?>
                                    <button class="button"><?php echo lang('add');?></button>
                                </div><!--row-->
                            </div>
                        </div>
                      
                    </div>
                </div><!--row-->
                <?php echo form_close();?>
            <?php }*/?>
            
            <div class="row no-gutters">
                <div class="table-area margin-top-20px">
                    <table class="table table-striped table-bordered table-hover">
                        <?php 
                        if(count($products_with_serials) != 0){?>
                        	 
                            <tr class="header-ta">
                                <td>
                                    <?php echo lang('product_name');?>
                                </td>
                                <td>
                                    <?php echo lang('thumbnail');?>
                                </td>
                                <td>
                                    <?php echo lang('quantity');?>
                                </td>
                              <?php /*  <td>
                                    <?php //echo lang('actions');?>
                                </td>*/?>
                            </tr>
                            <?php foreach($products_with_serials as $product){?>
                                <tr>
                                    <td>
                                        <?php echo $product->title;?> <br/>
                                        <?php echo '<br>'.lang('price'). ' : '. $product->final_price.' '.$order_details->currency_symbol;?><br/>
                                        <?php echo '<br>'.lang('vat_value'). ' : '. $product->vat_value.' '.$order_details->currency_symbol;?><br/>
                                        <?php echo '<br>'.lang('reward_points') .' : '. $product->reward_points;?>
                                    </td>

                                    <td>
                                         <img src="<?php echo base_url();?>assets/uploads/products/<?php echo $product->image;?>" alt="<?php echo $product->title;?>"/>               
                                    </td>

                                    <td>
                                        <?php
                                            if($edit_order && ! isset($product->non_serials_product))
                                            {
                                                echo form_open('orders/admin_edit_order/update_quantity');
                                                    $quantity_data = array( 
                                                                            'type'  => 'number', 
                                                                            'style' => 'height: 50px',
                                                                            'name'  => "quantity", 
                                                                            'class' => "quantity_input", 
                                                                            'value' => $product->qty, 
                                                                            'min'   => 1,
                                                                            '');
                                                    echo form_input($quantity_data);?>
                                                    <input type="hidden" name="order_id" value="<?php echo $order_details->id;?>" />
                                                    <input type="hidden" name="country_id" value="<?php echo $order_details->country_id;?>" />
                                                    <input type="hidden" name="product_id" value="<?php echo $product->product_id;?>" />
                                                    <button type="submit" class="button btn btn-primary" style="margin: auto 10px;"><?php echo lang('update_quantity');?></button>
                                                
                                                <?php 
                                                echo form_close(); 
                                            }else{
                                                ?>
                                                <span><?php echo $product->qty;?></span>
                                            <?php } ?>
                                    </td>
                                    <?php /*<td>
                                        <?php if($edit_order){?>
                                            <button type="button" class="button remove_product" style="background: red;" name="serial_id"  data-order_id="<?php echo $order_details->id;?>" data-product_id="<?php echo $product->product_id;?>" data-price="<?php echo $product->price;?>" ><?php echo lang('remove_product');?></button>
                                        <?php }
                                        /*if($product->return_status != 0){?>
                                          //  <?php echo lang('returned_product');?>
                                       // <?php }
                                        
                                    </td>
                                    */?>
                                </tr>

                                <!------------------------------Product Serials ---------------------------------->
                                <?php
                                    if($order_details->order_status_id != 3 && $order_details->order_status_id != 4){
                                        if(isset($product->serials) && count($product->serials) != 0){?>
                                            <tr class="header_tr2" style="background:rgb(202, 228, 255); color:#000;">

                                                <td><strong><?php echo lang('price');?></strong></td>
                                                <td><strong><?php echo lang('serial');?></strong></td>
                                                <td></td>
                                            </tr>
                                            <tbody class="product_serials_<?php echo $product->product_id;?>">
                                                <?php
                                                foreach($product->serials as $serial){?>
                                                    <tr class="serial_row_<?php echo $serial->product_serial_id;?>">
                                                        <td>
                                                            <del>
                                                            <?php
                                                            echo $product->price != $product->final_price ? $product->price.' '.$order_details->currency_symbol.'<br>' : '';
                                                            ?>
                                                            </del>
                                                            <?php echo $product->final_price.' '.$order_details->currency_symbol;?>
                                                        </td>
                                                        <td colspan="2" style="text-align: center;">
                                                            <?php echo $serial->dec_serial;?>
                                                            <?php /*
                                                            <?php if($serial->invalid == 0){?>
                                                            <div class="serial_<?php echo $serial->product_serial_id;?>">
                                                                <button type="button" class="btn yellow-crusta invalid_serial serial_<?php echo $serial->product_serial_id;?>" value="<?php echo $serial->product_serial_id;?>" name="serial_id" data-serial_id="<?php echo $serial->product_serial_id;?>" data-order_id="<?php echo $order_details->id;?>" data-product_id="<?php echo $serial->product_id;?>" data-price="<?php echo $product->price;?>" ><?php echo lang('invalid_serial');?></button>
                                                                <span style="display: block; font-size: 12px; font-family: tahoma;" class="msg_span serial_<?php echo $serial->product_serial_id;?>"><?php echo lang('invalid_serial_will_be_replaced');?></span>
                                                                <div><select style="display: none;" name="pocket_invalid_options" id="pocket_invalid_options<?php echo $serial->product_serial_id;?>" class="pocket_invalid_options"  data-serial_id="<?php echo $serial->product_serial_id;?>" data-order_id="<?php echo $order_details->id;?>" data-product_id="<?php echo $serial->product_id;?>" data-price="<?php echo $product->price;?>"></select></div>
                                                                <div><select style="display: none;" class="invalid_options" id="invalid_options<?php echo $serial->product_serial_id;?>" data-serial_id="<?php echo $serial->product_serial_id;?>" data-order_id="<?php echo $order_details->id;?>" data-product_id="<?php echo $serial->product_id;?>" data-price="<?php echo $product->price;?>"></select></div>
                                                            </div>
                                                            <?php }else{?>
                                                                <span style="color: red;"> <?php echo lang('invalid_serial');?> </span>
                                                            <?php }?>
                                                            */?>
                                                        </td>
                                                    </tr>
                                                <?php }?>
                                            </tbody>

                                        <?php }
                                    }
                                ?>
                                
                                <!------------------------------Product Selected Optional Options---------------------------------->
                                <?php
                                if(isset($product->user_optional_fields) && count($product->user_optional_fields) != 0) { ?>

                                        <?php foreach($product->user_optional_fields as $field){?>

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

                                        <?php }?>

                                    <?php 
                                } ?>
                                <!---------------------------------------------------------------->
                                
                            <?php }
                        }?>
                        
                        <tr>
                            <td colspan="2" class="total-ta"><?php echo lang('total');?></td>
                            <td class="total-ta"><?php echo $order_details->total." ".$order_details->currency_symbol;?></td>
                        </tr>
                        <?php if($order_details->discount != 0){?>
                            <tr>
                                <td colspan="2" class="total-ta"><?php echo lang('total_discount');?></td>
                                <td class="total-ta">- <?php echo $order_details->discount." ".$order_details->currency_symbol;?></td>
                            </tr>
                        <?php }?>
                        
                        <?php if($order_details->coupon_discount != 0){?>
                            <tr>
                            	<td colspan="2" class="total-ta"><?php echo lang('coupon_discount');?></td>
                                <td class="total-ta"><?php echo $order_details->coupon_discount." ".$order_details->currency_symbol;?></td>
                            </tr>
                        <?php }?>
                       
                        <?php if($order_details->shipping_cost != 0){?>
                            <tr>
                                <td colspan="2" class="total-ta"><?php echo lang('shipping_cost');?></td>
                                <td class="total-ta"><?php echo $order_details->shipping_cost." ".$order_details->currency_symbol;?></td>
                            </tr>
                        <?php }?>
                        
                        <?php if($order_details->vat_value != 0){?>
                            <tr>
                            	<td colspan="2" class="total-ta"><?php echo lang('vat_value');?></td>
                                <td class="total-ta"><?php echo $order_details->vat_value." ".$order_details->currency_symbol;?></td>
                            </tr>
                        <?php }?>
                        
                        <?php if($order_details->tax != 0){?>
                            <tr>
                                <td colspan="2" class="total-ta"><?php echo lang('tax');?></td>
                                <td class="total-ta"><?php echo $order_details->tax." ".$order_details->currency_symbol;?></td>
                            </tr>
                        <?php }?>
                        
                        <?php if($order_details->wrapping_cost != 0){?>
                            <tr>
                            	<td colspan="2" class="total-ta"><?php echo lang('wrapping_cost');?></td>
                                <td class="total-ta"><?php echo $order_details->wrapping_cost." ".$order_details->currency_symbol;?></td>
                            </tr>
                        <?php }?>
                            
                        <tr>
                            <td colspan="2" class="total-ta"><?php echo lang('final_total');?></td>
                            <td class="total-ta"><span class="final_total"><?php echo $order_details->final_total." ".$order_details->currency_symbol;?></span></td>
                        </tr>
                        
                        <?php if($order_details->notes != ''){?>
                            <tr>
                            	<td class="total-ta"><?php echo lang('notes');?></td>
                                <td colspan="3" class="total-ta"><?php echo $order_details->notes;?></td>
                            </tr>
                        <?php }?>
                    </table>
                </div><!--table-area-->
            </div>
            
            <div class="row no-gutters">
                <div class="col-md-12">
                    <div class="title">
                        <h3><?php echo lang('orders_log');?></h3>
                    </div>
                    <div class="table-area">
                        <table class="table table-striped table-bordered table-hover">
                            <tbody>
                                <tr class="header-ta">
                                	<td><?php echo lang('order_date');?></td>
                                    <td><?php echo lang('order_status');?></td>
                                </tr>
                                <?php foreach($order_log as $log){?>
                                    <tr>
                                    	<td><?php echo date('Y/m/d H:i', $log->unix_time);?></td>
                                        <td><span class="label label-<?php echo $log->class;?>"><?php echo $log->name;?></span></td>
                                    </tr>
                                <?php }?>
                            </tbody>
                        </table>
                    </div>
                </div><!--col-->
            </div><!--row-->
            
            <?php if(count($order_notes) != 0){?>
                <div class="row no-gutters">
                    <div class="col-md-12">
                        <div class="title">
                            <h3><?php echo lang('notes');?></h3>
                        </div>
                        <div class="table-area">
                            <table class="table table-striped table-bordered table-hover">
                                <tbody>
                                    <tr class="header-ta">
                                    	<td><?php echo lang('notes');?></td>
                                        <td><?php echo lang('name');?></td>
                                        <td><?php echo lang('date');?></td>
                                    </tr>
                                    <?php foreach($order_notes as $note){?>
                                        <tr>
                                            <td><?php echo $note->comment;?></td>
                                            <td><?php echo $note->first_name.' ',$note->last_name;?></td>
                                            <td><?php echo date('Y/m/d H:i', $note->unix_time);?></td>
                                        </tr>
                                    <?php }?>
                                </tbody>
                            </table>
                        </div>
                    </div><!--col-->
                </div><!--row-->
            <?php }?>
            
            <?php if(count($edit_order_data) != 0){?>
                <div class="row no-gutters">
                    <div class="col-md-12">
                        <div class="title">
                            <h3><?php echo lang('order_edit_log');?></h3>
                        </div>
                        <div class="table-area">
                            <table class="table table-striped table-bordered table-hover">
                                <tbody>
                                    <tr class="header-ta">
                                        <td><?php echo lang('name');?></td>
                                        <td><?php echo lang('date');?></td>
                                    </tr>
                                    <?php foreach($edit_order_data as $log){?>
                                        <tr>
                                            <td><?php echo $log->first_name.' '.$log->last_name;?></td>
                                            <td><?php echo date('Y/m/d H:i', $log->unix_time);?></td>
                                        </tr>
                                    <?php }?>
                                </tbody>
                            </table>
                        </div>
                    </div><!--col-->
                </div><!--row-->
            <?php }?>
            
            
            
            <div class="row margin-top-20px">
            
                <div class="col-md-6">
                  <div class="notes">
                        <div class="title margin-bottom-15px">
                            <h3><?php echo lang('add')." ".lang('notes');?> </h3>
                        </div>
                        <form action="<?php echo base_url()."orders/admin_order/insert_order_note"?>" method="post">
                            <div class="form-group">
                                <div class="row no-gutters">
                                    <label class="col-md-2">
                                        <?php echo form_label(lang('notes'), 'admin_note'); ?>
                                    </label>
                                    <?php
                                        echo form_error("admin_note");
                                        $admin_note_data = array(
                                                            'name'        => 'admin_note'             ,
                                                            'class'       => 'form-control col-md-10' ,
                                                            'id'          => 'admin_note'             ,
                                                            //'rows'        => '5'                      ,
                                                            //'cols'        => '10'
                                                            );
                                        echo form_textarea($admin_note_data);
                                    ?>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <div class="row no-gutters align-items-left">
                                    <div class="col-md-12">
                                        <?php echo form_hidden('order_id', $order_details->id);?>
                                        <button class="button"><?php echo lang('add');?></button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
              </div><!--col-->
              
              <?php  if($order_details->order_status_id != 1){//($order_details->order_status_id == 2 || $order_details->order_status_id == 8 || ($order_details->send_later == 1 && $order_details->order_status_id == 1)){?>
                <div class="col-md-6">
                    <div class="notes">
                        <div class="title margin-bottom-15px">
                            <h3><?php echo lang('change_staus');?></h3>
                        </div>
                        
                        <form action="<?php echo base_url()."orders/admin_order/update_status"?>" method="post">
                            <div class="form-group">
                                <div class="row no-gutters">
                                    <label class="col-md-2">
                                        <?php echo form_label(lang('status'), 'status_id'); ?>
                                    </label>
                                    <?php
                                        echo form_error("status_id");
                                        $status_id = isset($order_details->order_status_id) ? $order_details->order_status_id : set_value('status_id') ;                   
                                        echo form_dropdown('status_id', $status_options, $status_id, 'class="form-control col-md-10"');
                                    ?>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <div class="row no-gutters">
                                    <label class="col-md-2">
                                        <?php echo form_label(lang('status_note'), 'status_note');?>
                                     </label>
                                     <?php
                                        echo form_error("status_note");
                                        $status_note_data = array(
                                                            'name'        => 'status_note'              ,
                                                            'id'          => 'status_note'              ,
                                                            'class'       => 'form-control col-md-10'   ,
                                                            //'value'       => $order_details->status_note,
                                                            'rows'        => '3'                        ,
                                                            'cols'        => '10'
                                                            );
                                      echo form_textarea($status_note_data);
                                    ?>
                                </div>
                            </div>
                            
                            
                           
                            <div class="form-group">
                                <div class="row no-gutters align-items-left">
                                    <div class="col-md-12">
                                        <?php echo form_hidden('order_id', $order_details->id);?>
                                        <button class="button"><?php echo lang('submit');?></button>
                                      </div>    
                                  </div>
                              </div>    
                          </form>
                      </div>
                  </div><!--col-->
              <?php }?>
              
              
              <?php if($order_details->order_status_id == 1 && $order_details->needs_shipping == 1 && $order_details->shipping_type == 3){?>
                <div class="col-md-6">
                    <div class="notes">
                            <div class="title margin-bottom-15px">
                                <h3><?php echo lang('shipment_status');?></h3>
                            </div>
                            
                            
                            
                            
                            <?php if($order_details->shipping_company_id == 2){// Aramex?>
                            
                                <?php if($order_details->tracking_number == ''){?>
                                
                                <?php echo form_open(base_url().'orders/admin_edit_order/create_aramex_shipping');?>
                                    
                                    <div class="form-group">
                                        <div class="row no-gutters">
                                            <label class="col-md-3">
                                                <?php echo lang('product_type');?>
                                            </label>
                                            
                                            <div class="col-md-6">
                                                <select name="product_type" class="form-control ">
                                                    <option value="PDX">Priority Document Express</option>
                                                    <option value="PPX">Priority Parcel Express </option>
                                                    <option value="PLX">Priority Letter Express</option>
                                                    <option value="DDX">Deferred Document Express</option>
                                                    <option value="DPX">Deferred Parcel Express </option>
                                                    <option value="GDX">Ground Document Express</option>
                                                    <option value="GPX">Ground Parcel Express</option>
                                                    <option value="EPX">Economy Parcel Express</option>
                                                </select>
                                            </div>
                                        </div>    
                                    </div>
                                    
                                    <div class="form-group">
                                        <div class="row no-gutters">
                                            <label class="col-md-3">
                                                <?php echo lang('product_group');?>
                                            </label>
                                            
                                            <div class="col-md-6">
                                                <select name="product_group" class="form-control ">
                                                    <option value="EXP">Express</option>
                                                    <option value="DOM">Domestic</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <input type="hidden" name="order_id" value="<?php echo $order_details->id;?>" />
                                    
                                    <div class="form-group">
                                        <div class="row no-gutters align-items-left">
                                            <div class="col-md-12">
                                                <button class="button"><?php echo lang('start_aramex_shipping');?></button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                <?php echo form_close();?>
                                <?php }else{?>
                                
                                    <div class="form-group">
                                        <div class="row no-gutters">
                                            <label class="col-md-3">
                                                <?php echo lang('tracking_number');?>
                                            </label>
                                            
                                            <div class="col-md-6">
                                                <div class="form-control ">
                                                    <?php echo $order_details->tracking_number;?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                <?php }?>
                                
                                
                                
                            <?php }
                            
                            else if($order_details->shipping_company_id == 1){ //SMSA?>
                            
                            
                                <?php if($order_details->delivered == 0){?>
                                <form method="post" action="<?php echo base_url();?>orders/shipping_gateways/create_shipping_request/">
                                    <div class="form-group">
                                        <div class="row no-gutters">
                                            <label class="col-md-2"><?php echo form_label(lang('shipping_city'), 'city');?></label>
                                            <?php
                                                echo form_error("city");
                                                $city_id = isset($order_details->shipping_city) ? $order_details->shipping_city : set_value('shipping_city') ;
                                                echo form_dropdown('shipping_city', $cities_list, $city_id, 'class="form-control col-md-10"'); // select2
                                            ?>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <div class="row no-gutters align-items-left">
                                            <div class="col-md-12">
                                                <?php echo form_hidden('order_id', $order_details->id);?>
                                                <?php echo form_hidden('shipping_company_id', $order_details->shipping_company_id);?>
                                                <button class="button"><?php echo lang('create_shipping_request');?></button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            <?php }else{ ?>
                                <div class="form-group">
                                    <div class="row no-gutters">
                                        <label class="col-md-2"><?php echo lang('tracking_number');?></label>
                                        <span class="col-md-10"><?php echo $order_details->tracking_number;?></span>
                                    </div>
                                </div>
                                <?php if(count($tracking_data) != 0){?>
                                    <div class="table-area">
                                        <table class="table table-striped table-bordered table-hover">
                                            <tbody>
                                                <tr class="header-ta">
                                                    <td><?php echo lang('status');?></td>
                                                    <td><?php echo lang('check_date');?></td>
                                                    <td><?php echo lang('response');?></td>
                                                </tr>
                                                <?php foreach($tracking_data as $row){?>
                                                    <tr>
                                                        <td><?php echo $row->status_name;?></td>
                                                        <td> <?php echo date('Y/m/d H:i', $row->unix_time);?></td>
                                                        <td dir="ltr"><?php echo $row->decoded_response;?></td>
                                                    </tr>
                                                <?php }?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php }?>
                                
                                <form method="post" action="<?php echo base_url();?>orders/shipping_gateways/get_shipping_info">            
                                    <div class="form-group">
                                        <div class="row no-gutters align-items-left">
                                            <div class="col-md-12">
                                                <?php echo form_hidden('order_id', $order_details->id);?>
                                                <?php echo form_hidden('admin', '1');?>
                                                <button class="button"><?php echo lang('shipment_status');?></button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                
                                <div class="title margin-bottom-15px">
                                    <h3><?php echo lang('cancel_shipment');?></h3>
                                </div>
                                <form method="post" action="<?php echo base_url();?>orders/shipping_gateways/cancel_smsa_shipment">
                                    <div class="form-group">
                                        <div class="row no-gutters">
                                            <label class="col-md-2"><?php echo lang('cancel_reason');?></label>
                                            <textarea required="required" name="cancel_reason" class="form-control col-md-10" cols="6" rows="3"></textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <div class="row no-gutters align-items-left">
                                            <div class="col-md-12">
                                                <?php echo form_hidden('order_id', $order_details->id);?>
                                                <?php echo form_hidden('admin', '1');?>
                                                <button class="button"><?php echo lang('cancel_shipment');?></button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                
                            <?php }
                            }?>
                    </div><!--col-->
                </div><!--row-->
            <?php }?>
          </div>

        </div>
                
   
    <?php }?>
    
</div><!--col-->
<div class="loading_modal"><!-- Place at bottom of page --></div>



<style>
    .loading_modal {
        display:     none;
        position:    fixed;
        z-index:     1000;
        top:         0;
        left:        0;
        height:      100%;/*750px;*/
        width:       100%;/*900px;*/
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
    
    .quantity_input{
        color: #333333;
        width: 50px;
    }
    
</style>

<script>
    $(document).on({
        ajaxStart: function() { $('body').addClass("loading");    },
        ajaxStop: function() { $('body').removeClass("loading"); }    
    });
    
   
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
    
    
</script>
