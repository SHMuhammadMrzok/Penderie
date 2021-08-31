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
    .country_field{
        display: none;
    }
    .order_fields{
        display: none;
    }
    .total_fields{
        display: none;
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
    <?php echo validation_errors();?>
    <?php $att=array('class'=> 'form-horizontal form-bordered', 'id'=>'products_form');
                      echo form_open_multipart($form_action, $att);?>
    <div class="tabbable-custom form">
    
	<div class="tab-content">
        <div class="tab-pane active" id="tab_general">
	      <div class="form-body">
                <div class="form-group">
                    <label class="control-label col-md-3">
                      <?php echo lang('name_of_store');?><span class="required">*</span>
                    </label>
                   <div class="col-md-4">
                    <?php   
                        echo form_error("store_id");
                        $store_id = isset($general_data->store_id) ? $general_data->store_id : set_value('store_id') ; 
                        echo form_dropdown('store_id', $stores, $store_id, 'class="form-control select2" id="store_id"');
                    ?>
                   </div>
                 </div>
                <div class="form-group">
                    <label class="control-label col-md-3">
                      <?php echo lang('username');?><span class="required">*</span>
                    </label>
                   <div class="col-md-4">
                    <?php   
                        echo form_error("users");
                        $user_id = isset($general_data->user_id) ? $general_data->user_id : set_value('users') ; 
                        echo form_dropdown('users', $users, $user_id, 'class="form-control select2" id="user_id"');
                    ?>
                   </div>
                 </div>
                 
                 <div class="form-group country_field">
                    <label class="control-label col-md-3">
                      <?php echo lang('country');?><span class="required">*</span>
                    </label>
                   <div class="col-md-4">
                    <?php   
                        echo form_error("country_id");
                        $country_id = isset($general_data->country_id) ? $general_data->country_id : set_value('country_id') ; 
                        echo form_dropdown('country_id', $countries_options, $country_id, 'class="form-control select2" id="country_id" data-toggle="modal" href="#optional_fields_modal"' );
                    ?>
                   </div>
                 </div>
                 
                 <div class="form-group order_fields">
                    <label class="control-label col-md-3">
                      <?php echo lang('product');?><span class="required">*</span>
                    </label>
                   <div class="col-md-4">
                    <?php   
                        echo form_error("product_id");
                        $product_id = isset($general_data->product_id) ? $general_data->product_id : set_value('product_id') ; 
                        echo form_dropdown('product_id[]', $products, $product_id, 'class="form-control select2 check_optional_fields" id="products"');
                    ?>
                   </div>
                   <a href="#" id="add_product" class="order_fields" style="display: none; font-size: 45px;" title="<?php echo lang('add_more_products');?>">+</a>
                   
                 </div>
                 <div class="form-group product_quantity order_fields" style='display: none;'>
                    <label class="control-label col-md-3">
                      <?php echo lang('quantity');?><span class="required">*</span>
                    </label>
                   <div class="col-md-4">
                    <?php   
                        echo form_error("quantity");
                        $quantity_data = array('type'=>'number', 'name'=> "quantity[$product_id]", 'class'=>"form-control update_total", 'value'=> 1, 'min'=> 1); 
                        echo form_input($quantity_data);
                    ?> 
                   </div>
                   
                 </div>
                 <div class="optional_fields_div"></div>
                 
                 <div id="product_optional_fields_"></div>
                 
                 <div id="container"></div>
                 
                  <div class="form-group order_fields">
                    <label class="control-label col-md-3">
                      <?php echo lang('notes');?>
                    </label>
                   <div class="col-md-4">
                    <?php   
                        echo form_error("notes");
                        $text_data = array('name'=> "notes", 'class'=>"form-control update_total"); 
                        echo form_textarea($text_data);
                    ?>
                   </div>
                   
                 </div>
                 
                 <!-- Total-->
                 <div class="total_row total_fields">
                	<div class="row no-margin margin-top-10px">
                        <div class="control-label col-md-3"><?php echo lang('total_price');?> :</div><!--col-->
                    	<div class="col-md-4"><input type="text" class="total form-control inp_border" name="total_price" readonly /> <span class="currency"> </span></div><!--col-->
                    </div><!--row-->
                    <div class="row no-margin margin-top-10px">
                        <div class="control-label col-md-3"><?php echo lang('final_total');?> :</div><!--col-->
                    	<div class="col-md-4"> <input type="text" class="final_total form-control inp_border" name="final_price" readonly /> <span class="currency"> </span></div><!--col-->
                        <span style="color: red;" id='final_total_notice'>*<?php echo lang('final_total_after_applying_product_discount');?></span>
                    </div><!--row-->
                    
                    
                </div><!--total_row-->
                 
             </div>
                 
           </div>
         </div>
        
        <div class="form-actions total_fields submit_div">
			<div class="row">
				<div class="col-md-offset-3 col-md-9">
                    <?php $submit_att= array('class'=>"btn green");?>
					<button type="submit" class="btn green"><i class="fa fa-check"></i> <?php echo lang('add_products');?></button>
				</div>
			</div>
        </div>
        
	</div>
</div>
    		
<?php echo form_close();?>
</div>
<div class="loading_modal"><!-- Place at bottom of page --></div>

<?php $att=array('class'=> 'form-horizontal form-bordered cmxform','id'=>'modal_form' );
      echo form_open_multipart('',$att);?>
    <div id="optional_fields_modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" ng-blur="show=false">
        <div class="modal-dialog">
        	<div class="modal-content">
                <div class="modal-header">
                	<!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>-->
                	<h4 class="modal-title"><?php echo lang('optional_fields');?></h4>
                </div>
                <div class="modal-body">
                  <div class="scroller" style="height:400px" data-always-visible="1" data-rail-visible1="1">
                	 <div class="row">
                        <div class="form-body">
                            <div id="msg_div" style="text-align: center; color: red; font-weight: bold; display: none;"></div>
                		    
                            <div class="optional_fields_inputs form"> </div>
                            
                        </div><!-- form-body -->
                      </div><!-- row -->
                  </div>
                </div>
            	<div class="modal-footer">
            		<!--<button type="button" data-dismiss="modal" class="btn default" id="close_modal"><?php echo lang('close');?></button>-->
            		<button type="button" class="btn green" id="save_optional_fields"><?php echo lang('save');?></button>
            	</div>
           </div>
       </div>
    </div>
<?php echo form_close();?>
<script>

    $(document).on({
        //ajaxStart: function() { $('body').addClass("loading");   },
        //ajaxStop: function() { $('body').removeClass("loading"); }    
    });
    
    $("#user_id").change(function(){
        var user_id  = $("#user_id").val();
        var store_id = $("#store_id").val();
        
        if((user_id != 0) && (store_id != 0))
        {
            $(".country_field").show();
        }
        else
        {
            $(".country_field").hide();
            $(".order_fields").hide();
        }
    });
    
    /*---------------------------------------*/
    
    $("#store_id").change(function(){
        var store_id = $("#store_id").val();
        var user_id  = $("#user_id").val();
        
        if((user_id != 0) && (store_id != 0))
        {
            $(".country_field").show();
        }
        else
        {
            $(".country_field").hide();
            $(".order_fields").hide();
        }
    });
    
    /*---------------------------------------*/
    
    $("#country_id").change(function(){
       $('.order_fields').show();
       
       var country_id = $(this).val();
       var user_id    = $("#user_id").val();
       var store_id   = $("#store_id").val();
       
       var post_data  = {
                          country_id : country_id,
                          user_id    : user_id,
                          store_id   : store_id
                        };
       
       // Get Country Products
       $.post('<?php echo base_url()?>orders/admin_order/get_country_products/"', post_data, function(info){
            $("#products").html(info);
            $(".total_fields").hide();
            $('.product_div').remove();
            $('#products').select2('data', null);
            
       });
       
       // Get Currency
       $.post('<?php echo base_url()."orders/admin_order/get_country_currency";?>', post_data, function(currency){
          $('.currency').html(currency);
       });
       
    });
    
    /////////////////////////////////////
      ///////////Calculate Total//////
    
    $(' #products, .update_total, .products, .quantity').change(function(){
       check_available_qty();
    });
    
    function check_available_qty()
    {
        var postData = $('#products_form').serializeArray();
        
        $.post('<?php echo base_url()."orders/admin_order/check_qty/"?>', postData, function(status_result){
            <?php
                /*
                   $status = 0 --> no stock
                   $status = 1 --> available
                   $status = 2 --> max_qty_per_user_discount_reached
                   $status = 3 --> max_products_per_order_reached
                   $status = 4 --> cant add more than one of this product
                   $status = 5 --> optional fields required
                */
            ?>
            
            if(status_result[0]== 0)
            {
                showToast(status_result[1],'<?php echo lang('error');?>','error');
                
                $('.submit_div').hide();
                
                if(status_result[2] == 3)
                {
                    $('.product_div').last().remove(); 
                }
                
            }
            else
            {
                if(status_result[3] != '')
                {
                    //$('#product_optional_fields').html(status_result[3]);
                }
                else
                {
                    $('.submit_div').show();
                }
                calculate_total();
            } 
            
        }, 'json');
    }
    
    function calculate_total()
    {
        
        $(".total_fields").show();
        var postData2   = $('#products_form').serializeArray(); 
        
        $.post('<?php echo base_url()."orders/admin_order/total/"?>', postData2, function(result2){
            
            $('.total').val(result2[0]);
            $('.final_total').val(result2[1]);
            
        }, 'json');
    }
    
    ////////////////////////////////////
    //Add Products Button
    
    $("#add_product").click(function(e){
        e.preventDefault();
        var country_id = $("#country_id").val();
        view_products(country_id);
        
        return false;
    });
    
    /*---------------------------------------*/
    
    function view_products(country_id)
    {
        data = {
                    country_id : country_id,
                    user_id    : $("#user_id").val(),
                    store_id   : $("#store_id").val(),
               };
        
        $.post('<?php echo base_url()?>orders/admin_order/get_country_products/', data, function(result){
            $("#container").append('<div class="product_div"><div class="form-group"> <label class="control-label col-md-3"><?php echo lang('product');?><span class="required">*</span></label><div class="col-md-4"><select name="product_id[]" class="form-control update_total products check_optional_fields"></select></div></div><div class="form-group"><label class="control-label col-md-3"><?php echo lang("quantity");?><span class="required">*</span></label><div class="col-md-4"><input type="number" name="quantity[]" value="1" class="form-control update_total product_quantity"><div></div><a href="#" class="remove_product" name="delete[]"><?php echo lang('delete');?></a></div><div class="optional_fields_div"></div>');
            $(".products").last().html(result);
            
            //get total of order
            $(' #products, .update_total, .products, .quantity').change(function(){
               check_available_qty();
            });
        });
    }
    /*---------------------------------------*/
    
    //////////Remove Product////////////
    $('body').on('click', '.remove_product', function(event){
        event.preventDefault();
        
        $(this).parent().parent().parent('div').remove(); 
        calculate_total();
    });
   
    //////////////////////////////////////////////////////////////
    //// check added optional fields 
    
    $('body').on('click', '#save_optional_fields', function(event){
        event.preventDefault();
        
        if(! validate_modal())
        {
            $('#msg_div').show();
            $('#msg_div').html('<?php echo 'fill options';?>');
        }
        else
        {
            var html = '';
            $("#modal_form :input").each(function (){
            
                if($(this).attr("type") != 'button')
                {
                    html += '<div class="form-group added_fields">';
                    html += '<label class="control-label col-md-3"><?php echo lang('optional_fields');?></label>';
                    html += '<div class="col-md-4">'+$(this).val()+'</div>';
                    html += '<input type="hidden" name="'+$(this).attr('name')+'" value="'+$(this).val()+'" />';
                    html += '</div>';
                }
            });
            
            $("#container").append(html);
            
            $("#modal_form")[0].reset();
            $("#close_modal").click();
        }
        
    });
    
    $('body').on('click', '#close_modal, .close', function(event){
        event.preventDefault();
        
    });
    
    function validate_modal() { 
        //Validation Function - Sample, just checks for empty fields
         var valid;
        $("#modal_form :input").each(function (){
            
            if($(this).attr("type") != 'hidden' && $(this).attr("type") != 'button')
            {
                if ($(this).val() === "") {
                    
                    var a = $(this).val();
                    valid = false;
                }
            }
        });
        if (valid !== false) {
            return true;
        }
        else {
            return false;
        }
    }
    

</script> 
  	