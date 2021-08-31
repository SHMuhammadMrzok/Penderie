<style>
    .total_row{ 
        padding: 10px 0;
        background: #eee;
    }
    .inp_border{     
        border: 1px solid #ccc;
        background: #fff;
    }
    .margin-top-10px{ 
        margin-top: 10px;
    }
    .no-padding{ 
        padding: 0; 
    }
    .block_padd{
        overflow: hidden;
        height: auto;
        width: 100%;
        background-color: #eee;
        margin-top: 10px;
    }
    .margin-bottom-10px{ 
        margin-bottom: 10px;
    }
    .checkbox input[type=checkbox], .checkbox-inline input[type=checkbox], .radio input[type=radio], .radio-inline input[type=radio] {
        position: absolute;
        margin-top: 4px;
        margin-left: -7px;
    }
    .form-horizontal .checkbox, .form-horizontal .checkbox-inline, .form-horizontal .radio, .form-horizontal .radio-inline{
        padding-top: 3px;
    }
    #banks_list{    
        background-color: #D4D4D4;
        display: block;
        overflow: hidden;
    }
    .bank_acc_data{
        text-align: left;
    }
    
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
    
    .user_account_style{
    display: block;
    margin-left: 40px;
    margin-top: 10px;
    }

</style>

<div class="form">
    <?php if(isset($error_msg)){?>
        <h1 class="title_order_page"><?php echo $error_msg;?></h1>
    <?php }else{?>
        <?php echo validation_errors();?>
        <?php $att=array('class'=> 'form-horizontal form-bordered', 'id'=>'order_form');
                          echo form_open_multipart($form_action, $att);?>
        <div class="tabbable-custom form">
        
       	    <div class="tab-content">
            
                <h1 class="title_order_page"><?php echo lang('order_details');?></h1>
                <div class="row no-margin table_order">
                	<table class="table table-striped table-hover table-bordered">
                        <tr>
                        	<td><?php echo lang('order_number');?></td>
                            <td>#<?php echo $order_data->id;?></td>
                        </tr>
                        <tr>
                        	<td><?php echo lang('name_of_store');?></td>
                            <td><?php echo $order_data->store_name;?></td>
                        </tr>
                        
                        <tr>
                        	<td><?php echo lang('country');?></td>
                            <td><?php echo $order_data->country;?></td>
                        </tr>
                        
                        <tr>
                        	<td><?php echo lang('order_date');?></td>
                            <td><?php echo date('Y/m/d H:i',$order_data->unix_time);?></td>
                        </tr>
                        
                        <?php if($order_data->notes){?>
                            <tr>
                            	<td><?php echo lang('notes');?></td>
                                <td><?php echo $order_data->notes;?></td>
                            </tr>
                        <?php }?>
                    </table>
                </div>
                <table class="table table-striped table-hover table-bordered">
                
                    <?php 
                	 foreach($order_products as $product)
              	     {?>
                        <tr class="header_tr" style="background: rgb(108, 174, 241); color:#fff;">
                            <td><strong><?php echo $product->title;?></strong></td>
                            <td><img src="<?php echo base_url();?>assets/uploads/products/<?php echo $product->image;?>" class="img-responsive" alt="img" style="width: 140px; height: 80px;" /></td>                            
                        	<td>
                                <?php if($product->discount != 0){?>
                                    <span><?php echo lang('old_price');?></span> : 
                                    <span><?php echo $product->price.' '.$order_data->currency_symbol;?></span>
                                <?php }?>
                                <span><?php echo lang('price');?></span> : 
                                <span><?php echo $product->final_price.' '.$order_data->currency_symbol;?></span>
                            </td>
                            <td>
                                <span><?php echo lang('quantity');?></span> : 
                                <span><?php echo $product->qty;?></span>
                            </td>
                            
                        </tr>
                    <?php   
                     }
                    ?>
                   
                </table> 
                <div class="tab-pane active" id="tab_general">
        	      <div class="form-body">
                        <input type="hidden" name="order_id" class="order_id" value="<?php echo $order_data->id;?>" />
                         <!-- Total-->
                         <div class="total_row total_fields">
                        	<div class="row no-margin margin-top-10px">
                                <div class="control-label col-md-3"><?php echo lang('total_price');?> :</div><!--col-->
                            	<div class="col-md-4"><input type="text" class="total_price form-control inp_border" name="total_price" value="<?php echo $order_data->total;?>" readonly  /> <span class="currency"><?php echo $order_data->currency_symbol;?> </span></div><!--col-->
                            </div><!--row-->
                            <div class="row no-margin margin-top-10px">
                                <div class="control-label col-md-3"><?php echo lang('discount_details');?> :</div><!--col-->
                            	<div class="col-md-4"><input type="text" class="discount form-control inp_border" name="discount" value="<?php echo $order_data->discount;?>" readonly  /> <span class="currency"> <?php echo $order_data->currency_symbol;?></span></div><!--col-->
                            </div><!--row-->
                            <div class="row no-margin margin-top-10px">
                                <div class="control-label col-md-3"><?php echo lang('coupon_discount');?> :</div><!--col-->
                            	<div class="col-md-4"><input type="text" class="coupon_discount form-control inp_border" name="coupon_discount" value="<?php echo $order_data->coupon_discount;?>" readonly  /> <span class="currency"> <?php echo $order_data->currency_symbol;?></span></div><!--col-->
                            </div><!--row-->
                            <div class="row no-margin margin-top-10px">
                                <div class="control-label col-md-3"><?php echo lang('tax');?> :</div><!--col-->
                            	<div class="col-md-4"><input type="text" class="tax form-control inp_border" name="tax" value="<?php echo $order_data->tax;?>" readonly  /> <span class="currency"> <?php echo $order_data->currency_symbol;?></span></div><!--col-->
                            </div><!--row-->
                            <div class="row no-margin margin-top-10px">
                                <div class="control-label col-md-3"><?php echo lang('final_total');?> :</div><!--col-->
                            	<div class="col-md-4"> <input type="text" class="final_total form-control inp_border" name="final_price" value="<?php echo $order_data->final_total;?>" readonly /> <span class="currency"> <?php echo $order_data->currency_symbol;?></span></div><!--col-->
                                <span style="color: red;" id='final_total_notice'>*<?php echo lang('final_total_after_applying_product_discount');?></span>
                            </div><!--row-->
                            
                            <?php if($order_data->coupon_discount == 0){?>
                                <div class="row no-margin margin-top-10px coupon_div">
                                    <div class="control-label col-md-3"><?php echo lang('apply_coupon_here');?></div><!--col-->
                                    <div class="col-md-4"><input type="text" class="form-control inp_border" name="coupon" id="coupon" placeholder="" /> </div><!--col-->
                                    <div id="coupon_msg" style="color: red;"></div>
                                    
                                </div><!--row-->
                                
                                
                                <div class="row no-margin margin-top-10px coupon_div">
                                    <div class="col-md-offset-3 col-md-9">
                                	    <a href="#" class="btn bg-primary" id="submit_coupon"><?php echo lang('submit_coupon');?></a>
                                    </div>
                                </div><!--row-->
                         <?php }?>   
                            
                        </div><!--total_row-->
                         
                         
                         <!--Payment Options-->
                         <div class="row no-margin margin-top-10px total_fields">
                        	<div class="col-md-12 no-padding">
                            	<div class="payment_bar">
                                    
                                    <div class="row no-margin">
                                        <span class="validation_error" style="color: red; text-align: center;"></span>
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
                                                                                'class' => 'bank_btn payment_options',
                                                                                'value' => $option->id,
                                                                                'id'    => 'bank_payment_selection'
                                                                             );
            
                                                 echo form_radio($payment_method_data);
                                                ?>
            
                                                <label for="options_input_<?php echo $option->id;?>"><?php echo $option->name;?></label>
                                            </div> 
                                            
                                             <?php echo form_error('bank_id'); ?>
                                             <div id="bank_options_div" style="display: none; margin-left: 20px; overflow: hidden;">
                                                 <?php 
                                                 foreach($bank_accounts as $account)
                                                 {
                                                 ?>
                                                    <div class="row no-margin margin-bottom-10px">
                                                        <input type="radio" name="bank_id" class="bank_btn" data-bank_id="<?php echo $account->id;?>" value="<?php echo $account->id;?>" id="bank_input_<?php echo $account->id;?>" />
                                                        <label for="bank_input_<?php echo $account->id;?>"><?php echo $account->bank;?></label>
                                                    </div><!--row-->
                                                    <div id="bank_details_<?php echo $account->id;?>" class="bank_details" style="display: none;">
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
                                         <?php }
                                         else{?>
                                            <div class="row no-margin margin-bottom-10px">
                                                <?php
            
                                                 echo form_error('payment_option_id');
                                                 if($option->id == 7)
                                                 {
                                                    $id    = 'voucher';
                                                    $class = 'payment_options';
                                                 }
                                                 else
                                                 {
                                                    $id    = 'options_input_'.$option->id;
                                                    $class = 'payment_options other_payment_options';
                                                 }
                                                 $payment_method_data = array(
                                                                                'name'  => 'payment_option_id',
                                                                                'class' => $class,
                                                                                'value' => $option->id,
                                                                                'id'    => $id
                                                                             );
                                                 
                                                 echo form_radio($payment_method_data);
                                                ?>
            
                                                <label for="options_input_<?php echo $option->id;?>"><?php echo $option->name;?></label>
                                                <?php if($option->id == 7){?>
                                                <input name="voucher" type="text" value="" class="voucher_input" />
                                                <?php }?>
                                            </div><!--row-->
                                    <?php }
                                         
                                         }?>
                                                                          
                                    </div>
                                </div>
                            </div>
                         </div>   
                    </div>
                 </div>
                
                <div class="form-actions total_fields">
        			<div class="row">
        				<div class="col-md-offset-3 col-md-9">
                            <?php $submit_att= array('class'=>"btn green");?>
        					<button type="submit" class="btn green" id="submit_order"><i class="fa fa-check"></i> <?php echo lang('submit');?></button>
        				</div>
        			</div>
                </div>
           
       	    </div>
        </div>
        		
        <?php echo form_close();?>
    <?php }?>
</div>
<div id="payment_div"></div>
<div class="loading_modal"><!-- Place at bottom of page --></div>

<style>

</style>
  	
<script>

    $(document).on({
        ajaxStart: function() { $('body').addClass("loading");   },
        ajaxStop: function() { $('body').removeClass("loading"); }    
    });
    
    ///////////////////////////////////Apply Coupon/////////////////////////////////////////////////////////////////
    
    $("#submit_coupon").click(function(event)
    {
        event.preventDefault();
        
        var coupon      = $("#coupon").val();
        var order_id    = $(".order_id").val();
        var post_data   = {           
                            coupon   : coupon,
                            oredr_id : order_id 
                          };
        
        $.post('<?php echo base_url()."orders/admin_order/coupon_discount";?>', post_data, function(coupon_result){
            
            if(coupon_result[0] == 1)
            {
                //success
                //$(".final_price").html(data2[0]);
                showToast(coupon_result[1], '<?php echo lang('success'); ?>', 'success');
                $(".coupon_div").remove();
                get_order_details();
            }
            else if(coupon_result[0] == 0)
            {
                $("#coupon_msg").html(coupon_result[1]);
            }
            
            
        }, 'json');
    });
    
    
    //////////////////////////////////////////////////////////////
    
    function get_order_details()
    {
        var order_id = $(".order_id").val();
        
        var postData = {
                            order_id : order_id
                       };
                       
        $.post('<?php echo base_url().'orders/admin_order/get_order_prices'?>', postData, function(result){
            $('.total').val(result[0]);
            $('.discount').val(result[1]);
            $('.coupon_discount').val(result[2]);
            $('.tax').val(result[3]);
            $('.final_total').val(result[4]);
        }, 'json');
    }
    ////////////////////////////////////////////////////////////////////
    $( "body" ).on( "click", '.payment_options', function() {
        var order_data = $('#order_form').serializeArray();
        
        $.post('<?php echo base_url().'orders/admin_order/apply_order_payment_method';?>', order_data, function(result){
            get_order_details();
        });
        
    });
    ////////////////////////////////////////////////////////////////////
    //submit finish order
    $( "body" ).on( "click", "#submit_order", function(event){
        event.preventDefault();
    
        //payment option validation
        if($('input[name=payment_option_id]:checked').length <= 0)
        {
            $('.validation_error').html('<?php echo lang('select_payment_option');?>');
        }
        else
        {
            //bank validation
            if($('#bank_payment_selection').is(':checked'))
            {
                if($('input[name=bank_id]:checked').length <= 0)
                {
                    $('.validation_error').html('<?php echo lang('select_bank');?>');
                }
                else
                {                    
                    var bank_id        = $('input:radio[name=bank_id]:checked').val();
                    var account_name   = $("input[name='account_name["+bank_id+"]']").val();
                    var account_number = $("input[name='account_number["+bank_id+"]']").val();
                    
                    if((account_name == '') || (account_number == ''))
                    {
                        $('.validation_error').html('<?php echo lang('add_bank_data');?>');
                    }
                    else
                    {
                        create_order();
                    }
                }
            }
            else if($('#voucher').is(':checked'))
            {
                if ($('.voucher_input').val() != '')
                {
                    create_order();
                }
                else
                {
                    $('.validation_error').html('<?php echo lang('add_voucher');?>');
                }
            }
            else
            {
                create_order();
            }
        }
    });
    
    function create_order()
    {
        var postData    = $('#order_form').serializeArray();
        var submit_form = false;
        
        $.post('<?php echo base_url()."orders/admin_order/submit_order";?>',postData, function(result){
           
            if(result[0] == '0')
            {
                showToast(result[1], '<?php echo lang('error')?>', 'error');
            }
            else if(result[0] == '1')
            {
                submit_form = true;
            }
            
            if(submit_form)
            {
                $('#payment_div').html(result[2]);
                $('.pay_form').submit();
                $( "#order_form" ).empty();
            }
        }, 'json');
    }
     ////////////////////////////////////////////////////////////////////
     
    $( "body" ).on( "click", '#bank_payment_selection', function() {
        $('#bank_options_div').show();
    });
    
    //////////////////////////////////////////////////////////////////// 
    
    $( "body" ).on( "click", '.bank_btn', function() {
        var bank_id = $(this).data("bank_id");
        $('.bank_details').hide();
        $('#bank_details_'+bank_id).show();
    });
    
    ////////////////////////////////////////////////////////////////////
    ////Other payment options Extra taxes
    $( "body" ).on( "click", '.other_payment_options, #voucher', function() {
        $("#bank_options").hide();
        $('#bank_options_div').hide();
    });
    



</script>   	