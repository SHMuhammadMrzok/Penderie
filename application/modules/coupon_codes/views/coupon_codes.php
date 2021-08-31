<div class="form">
    <?php if(isset($validation_msg)){?><span class="error"><?php echo $validation_msg;?></span><?php } 
    $att=array('class'=> 'form-horizontal form-bordered');
                      echo form_open_multipart($form_action, $att);?>
    <div class="tabbable-custom form">
	   <ul class="nav nav-tabs ">
	       <li class="active" >
    		<a href="#tab_general" data-toggle="tab">
                <span class="langname"><?php echo lang('general'); ?> </span>
            </a>
    	   </li>
          
           <?php foreach($data_languages as $key=> $lang){?>
    	       <li <?php //echo $key==0?'class="active"':'';?> >
    			 <a href="#tab_lang_<?php echo $lang->id; ?>" data-toggle="tab">
                    <img alt="" src="<?php echo base_url();?>/assets/template/admin/global/img/flags/<?php echo $lang->flag; ?>" />
    			     <span class="langname"><?php echo $lang->name; ?> </span>
                 </a>
    		  </li>
	       <?php } ?>
    	
	   </ul>
    
	<div class="tab-content">
        <div class="tab-pane active" id="tab_general">
	      <div class="form-body">
                <div class="form-group">
                    <label class="control-label col-md-3">
                      <?php echo lang('code');?><span class="required">*</span>
                    </label>
                   <div class="col-md-4">
                    <?php   
                            $code_data = array('name'=>'code','class'=>"form-control" , 'value'=> isset($general_data->code)? $general_data->code : set_value('code'));
                            echo form_input($code_data);
                    ?>
                    <?php echo form_error("code");?>
                   </div>
                 </div><!--code div-->
                 <div class="form-group">
                     <?php 
                       $amount_discount  = false;
                       $percent_discount = false;
                       $checked          = '';
                        
                       if(isset($general_data->discount) && !(isset($validation_msg)))
                       {
                          if($general_data->discount_type == 'amount')
                          { 
                            $amount_discount = true;
                            $checked         = "checked='checked'";
                          }
                          elseif($general_data->discount_type == 'percentage')
                          {
                            $percent_discount = true;
                          }
                       }
                       
                       if(isset($validation_msg) && isset($_POST['discount']))
                       {//echo $_POST['discount'];die();
                           if((int)$_POST['discount'] == 1)
                           {
                               $percent_discount = true;
                               
                           }
                           else
                           {
                               $amount_discount  = true;
                               $checked          = "checked='checked'";
                           }
                           
                           
                       }
                     ?>
					<label class="control-label col-md-3"><?php echo lang('discount');?><span class="required">*</span></label>
                    <div class="radio-list col-md-4">
                        
                        <label class="radio-inline" style="font-size: 13px;"><?php //echo lang('discount_percentage');?>
                            <?php
                                 $discount_percentage_data = array(
                                                                    'name'    => "discount",
                                                                    'class'   => 'radio-inline discount_percentage discount',
                                                                    'id'      => 'discount_percentage_radio',
                                                                    'value'   => 1,
                                                                    'checked' => set_radio("discount", $percent_discount, $percent_discount)
                                                                  );
                                         
                                echo form_radio($discount_percentage_data); 
                                echo lang('discount_percentage');
                            ?> 
                        </label>
                       
                       <label class="radio-inline" style="font-size: 13px;"> 
                           <?php $discount_amount_data = array(
                                                                'name'    => "discount",
                                                                'class'   => 'radio-inline discount_amount discount',
                                                                'id'      => 'discount_amount_radio',
                                                                'value'   => 0,
                                                                'checked' => set_radio("discount", $amount_discount, $amount_discount) 
                                                               );
                            //echo form_radio($discount_amount_data);
                             ?>           
                            
                            <input name="discount" type="radio" id="discount_amount_radio" class="radio-inline discount_amount discount" value="0" <?php echo $checked;?> />
                            <?php echo lang('discount_amount'); ?>
                        </label>
                        
                        <div id="discount_percentage" style="display: <?php echo (isset($general_data->discount)&& $general_data->discount_type == 'percentage'  && !isset($validation_msg)) || (isset($_POST['discount']) && $_POST['discount'] == 1)? 'block' : 'none' ;?>" >                                                                                                     
                            <?php  
                                   $discount_percentage_data = array('name'=>"discount_percentage" , 'class'=>"form-control discount_percentage_spinner " , 'value'=> isset($general_data->discount)&& $general_data->discount_type == 'percentage' ? $general_data->discount : set_value('discount_percentage', 0));
                                   echo form_input($discount_percentage_data);
                             ?>
                        </div>
                        
                        <div id="discount_amount" style="display: <?php echo (isset($general_data->discount)&& $general_data->discount_type == 'amount' && !isset($validation_msg)) || (isset($_POST['discount']) && $_POST['discount'] == 0)? 'block' : 'none' ;?>" >
                            <?php   
                                   $discount_amount_data = array('name'=>"discount_amount" , 'class'=>"form-control discount_amount_spinner ", 'value'=> isset($general_data->discount )&&$general_data->discount_type == 'amount' ? $general_data->discount : set_value('discount_amount', 0));
                                   echo form_input($discount_amount_data);
                              ?>
                        </div>
                        <?php echo form_error("discount");?>
                   </div>
				</div><!--discount div-->
                
                <div class="form-group">
                    <label class="control-label col-md-3">
                      <?php echo lang('min_amount');?><span class="required">*</span>
                    </label>
                   <div class="col-md-4">
                      <?php   
                           
                           $min_amount_data = array('name'=>"min_amount" , 'class'=>"form-control min_amount_spinner" , 'value'=> isset($general_data->min_amount)? $general_data->min_amount : set_value('min_amount'));
                           echo form_input($min_amount_data);
                      ?>
                      <?php echo form_error("min_amount");?>
                   </div>
                </div><!--min_amount div-->
                
                <div class="form-group">
                    <label class="control-label col-md-3">
                      <?php echo lang('country');?><span class="required">*</span>
                    </label>
                   <div class="col-md-4">
                      <?php   
                           
                           $country_id =  isset($general_data->country_id)? $general_data->country_id : set_value('country');
                           echo form_dropdown('country', $countries_array, $country_id, 'class="form-control select2" id="country"');
                      ?>
                      <?php echo form_error("country");?>
                   </div>
                </div><!--min_amount div-->
                
                <div class="form-group"  <?php if( ! isset($general_data->product_or_category)){?>style="display: none;"<?php }?> id="product_cat">
                    <?php
                      $product_checked = false;
                      $cat_checked     = false;
                      $total_checked   = false;
                       
                      if(isset($general_data->product_or_category))
                      {
                        if($general_data->product_or_category == 'product')
                        {
                            $product_checked = true;
                        }
                        elseif($general_data->product_or_category == 'category')
                        {
                            $cat_checked = true;
                        }
                        elseif($general_data->product_or_category == 'total')
                        {
                            $total_checked = true;
                        }
                      }
                    ?>
					<label class="control-label col-md-3"><?php echo lang('product_or_cat');?><span class="required">*</span></label>
                    <div class="radio-list col-md-4">
                        
                        <label class="radio-inline" style="font-size: 13px;">
                            <?php 
                                 $product_data = array(
                                                'name'    => "product_or_cat",
                                                'class'   => 'radio-inline product radio_type',
                                                'value'   => 1,
                                                'checked' => set_checkbox("product_or_cat", $product_checked, $product_checked)
                                                );    
                                 echo form_radio($product_data); 
                                 echo lang('products');
                            ?> 
                        </label>
                           
                           <label class="radio-inline" style="font-size: 13px;"> 
                               <?php $category_data = array(
                                                'name'    => "product_or_cat",
                                                'class'   => 'radio-inline category radio_type',
                                                'value'   => 0,
                                                'checked' => set_checkbox("product_or_cat", $cat_checked, $cat_checked)
                                                );   
                                                 
                                    echo form_radio($category_data); 
                                    echo lang('category'); ?>
                            </label>
                            <label class="radio-inline" style="font-size: 13px;">
                                <?php 
                                     $total_data = array(
                                                            'name'    => "product_or_cat",
                                                            'class'   => 'radio-inline total radio_type',
                                                            'value'   => 2,
                                                            'checked' => set_checkbox("product_or_cat", $total_checked, $total_checked)
                                                         );    
                                                
                                     echo form_radio($total_data); 
                                     echo lang('total');
                                ?> 
                            </label>
                            
                            <!--<div id="product_select"></div>-->
                            <div class="product_select" style="display: <?php echo isset($general_data->product_id)&& $general_data->product_or_category == 'product'? 'block' : 'none' ;?>">
                                <?php  
                                    $product_ids = isset($coupon_products) ? $coupon_products : set_value('product_id[]') ;                   
                                    echo form_multiselect('product_id[]', $products_options, $product_ids, 'class="form-control select2 products" id="products"');
                                 ?>
                            </div>
                            
                            <div id="category" style="display:<?php echo isset($general_data->cat_id)&& $general_data->product_or_category == 'category'? 'block' : 'none' ;?>">
                                <?php 
                                   $cat_ids = isset($coupon_cats) ? $coupon_cats : set_value('cat_id[]') ;
                                   echo form_multiselect('cat_id[]', $cats_array, $cat_ids, 'class="form-control select2" id="cat_id"');
                                  ?>
                            </div>
                            <?php echo form_error("product_or_cat");?>
                       </div>
                       
                    </div>
                
                <div class="form-group">
                    <label class="control-label col-md-3">
                      <?php echo lang('uses_per_customer');?><span class="required">*</span>
                    </label>
                   <div class="col-md-4">
                      <?php   
                           $uses_per_customer_data = array('name'=>"uses_per_customer" , 'class'=>"form-control uses_per_customer_spinner" , 'value'=> isset($general_data->uses_per_customer)? $general_data->uses_per_customer : set_value('uses_per_customer'));
                           echo form_input($uses_per_customer_data);
                           echo form_error("uses_per_customer");
                      ?>
                   </div>
                </div><!--uses_per_customer div-->
                
                <div class="form-group">
                    <label class="control-label col-md-3">
                      <?php echo lang('uses_per_coupon');?><span class="required">*</span>
                    </label>
                   <div class="col-md-4">
                      <?php   
                           $uses_per_coupon_data = array('name'=>"uses_per_coupon" , 'class'=>"form-control uses_per_coupon_spinner" , 'value'=> isset($general_data->uses_per_coupon)? $general_data->uses_per_coupon : set_value('uses_per_coupon'));
                           echo form_input($uses_per_coupon_data);
                           echo form_error("uses_per_coupon");
                      ?>
                   </div>
                </div><!--uses_per_coupon div-->
                
                <div class="form-group">
                    <label class="control-label col-md-3">
                      <?php echo lang('coupon_period');?><span class="required">*</span>
                    </label>
                    <div class="col-md-4">
						<div class="input-group input-large date-picker input-daterange" data-date="11-10-2012" data-date-format="dd-mm-yyyy">
						  
                            <?php 
                                $start_unix_time_data = array('name' => "start_unix_time" ,'class'=>"form-control" , 'value'=>isset($general_data->start_unix_time)?date('d-m-Y',$general_data->start_unix_time) : set_value("start_unix_time") );
                                echo form_input($start_unix_time_data);
                            ?>
							
                            <span class="input-group-addon">to </span>
                            
                            <?php 
                                $end_unix_time_data = array('name' => "end_unix_time" ,'class'=>"form-control" , 'value'=>isset($general_data->end_unix_time)?date('d-m-Y',$general_data->end_unix_time) : set_value("end_unix_time") );
                                echo form_input($end_unix_time_data);
                            ?>
						</div>
                        <?php 
                            echo form_error("start_unix_time");
                            echo form_error("end_unix_time");
                        ?>
					</div>
                </div><!--start & End date-->
                <div class="form-group">
                            <label class="control-label col-md-3">
                              <?php echo lang('active');?><!--<span class="required">*</span>-->
                            </label>
                           <div class="col-md-4">
                             <?php 
                                  // echo form_error('active');
                               
                                $active_value     = true ;
                                
                                if(isset($general_data->active)) 
                                {
                                    if($general_data->active == 1)
                                    {
                                        $active_value     = true;
                                    }
                                    if($general_data->active == 0)
                                    {
                                        $active_value     = false;
                                    }
                                }  
                                
                                $active_data = array(
                                            'name'           => "active",
                                            'class'          => 'make-switch',
                                            'data-on-color'  => 'danger',
                                            'data-off-color'  => 'default',
                                            'value'          => 1,
                                            'checked'        => set_checkbox("active", $active_value, $active_value),
                                            'data-on-text'   => lang('yes'),
                                            'data-off-text'  => lang('no'),
                                            );    
                                echo form_checkbox($active_data);  
                             ?>
                            </div>
                        </div><!-- active -->
                        
                        
            </div>
         </div>
        <?php foreach($data_languages as $key=> $lang){ ?>
        
    		<div class="tab-pane  <?php //echo $key==0 ? "active" :'';?>" id="tab_lang_<?php echo $lang->id; ?>">
    		      <div class="form-body">
                    
                    <div class="form-group">
                       <label class="control-label col-md-3">
                         <?php echo lang('coupon_name');?><span class="required">*</span>
                       </label>
                       <div class="col-md-4">
                          <?php 
                                echo form_error("name[$lang->id]");
                                $name_data = array('name'=>"name[$lang->id]" , 'class'=>"form-control" , 'value'=> isset($data[$lang->id]->name)? $data[$lang->id]->name : set_value("name[$lang->id]"));
                                echo form_input($name_data);
                          ?>
                        </div>
                    </div>
                    <?php  echo form_hidden('lang_id[]', $lang->id); ?>
                </div>  
             
    		</div>
        <?php } ?>
        <?php  echo isset($id) ? form_hidden('coupon_code_id', $id) : ''; ?>
        <div class="form-actions">
			<div class="row">
				<div class="col-md-offset-3 col-md-9">
                    <?php $submit_att= array('class'=>"btn green");?>
					<button type="submit" class="btn green"><i class="fa fa-check"></i> Submit</button>
				 
				</div>
			</div>
        </div>
        
	</div>
</div>

<div class="loading_modal"><!-- Place at bottom of page --></div>
<?php echo form_close();?>
</div>
<style>
        .loading_modal {
        display:     none;
        position:    fixed;
        z-index:     1000;
        top:         0;
        left:        0;
        height:      100%;/*750px;*/
        width:       100%;/*900px;*/
        margin-left: 300px;
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
    </style>
<script>
 
 $( document ).ready(function(){
    
    if($('#country').val() != 0)
    {
        $("#product_cat").show();
    }
 });
 
$("#country").change(function(){
    $(".radio_type").each(function(i) {
       $(this).removeAttr('checked');
       $.uniform.update();
       
       //$('.products').hide();
       $('.product_select').hide();
       $('#category').hide();
    });
});

/*Metronic.blockUI({
    target: ".portlet",
    animate: true
});*/
$(".product").click(function(){
   
    var country_id = $("#country").val();
    var postData   = {country_id: country_id}; 
    $(".product_select").empty();
     
    $.post("<?php echo base_url()."coupon_codes/admin_coupon_codes/get_products"?>", postData, function(result){
     console.log(result);
        $(".product_select").html(result);
        $(".select2-product").select2();
        $("#category").hide();
    });
});


    $("#country").click(function()
    {
        if($("#country").val() >=1 )
        {
            $("#product_cat").show();
        }
        else
        {
            $("#product_cat").hide();
        }
    });
 

$(document).on({
    ajaxStart: function() { $('body').addClass("loading");    },
    ajaxStop: function() { $('body').removeClass("loading"); }    
});

//Metronic.unblockUI(".portlet");

////////////////////////////////
 
 $(".discount_percentage").change(function () {    
     $('#discount_percentage').show();
     $('#discount_amount').hide();
   });
 
 $(".discount_amount").change(function () { 
     $('#discount_percentage').hide();
     $('#discount_amount').show();
   });
 
 $('body').on('click', '.category', function(){
     $('.product_select').hide();
     $('#category').show();
    });
    
 $('body').on('click', '.product', function(){
     $('.product_select').show();
     $('#category').hide();
    });
  
 $('body').on('click', '.total', function(){
     $('.product_select').hide();
     $('#category').hide();
    });


 ///////////////////////////////////////////
  $(function(){
                $(".min_amount_spinner").TouchSpin({          
                    buttondown_class: 'btn green',
                    buttonup_class: 'btn green',
                   // min: 0,
                    max: 1000000000,
                    step: .1,
                    stepinterval: 1,
                    maxboostedstep: 1,
                   
                }); 
            })
 ///////////////////////////////////////////
  $(function(){
                $(".uses_per_customer_spinner").TouchSpin({          
                    buttondown_class: 'btn red',
                    buttonup_class: 'btn red',
                    //min: 1,
                    max: 1000000000,
                    stepinterval: 1,
                    maxboostedstep: 1,
                    
                }); 
            })
 ///////////////////////////////////////////
  $(function(){
                $(".uses_per_coupon_spinner").TouchSpin({          
                    buttondown_class: 'btn blue',
                    buttonup_class: 'btn blue',
                   // min: 1,
                    max: 1000000000,
                    stepinterval: 1,
                    maxboostedstep: 1,
                    
                }); 
            })
 ///////////////////////////////////////////
  $(function(){
                $(".discount_percentage_spinner").TouchSpin({          
                    buttondown_class: 'btn blue',
                    buttonup_class: 'btn blue',
                    min: 0,
                    max: 1000000000,
                    step: .1,
                    stepinterval: 1,
                    maxboostedstep: 1,
                    prefix: '%'
                }); 
            })
 ///////////////////////////////////////////
  $(function(){
                $(".discount_amount_spinner").TouchSpin({          
                    buttondown_class: 'btn red',
                    buttonup_class: 'btn red',
                    min: 0,
                    max: 1000000000,
                    step: .1,
                    stepinterval: 1,
                    maxboostedstep: 1,
                    
                }); 
            })
</script>    	