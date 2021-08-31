<div class="portlet-body">
	<div class="row">
    <?php if(isset($error)){?>
        <div class="row static-info" style="margin: 10px;">
            <div class="col-md-5 name">
				  <?php echo $error;?>
			</div>
	    
		</div>
    <?php }else{?>
    		<div class="col-md-3 col-sm-3 col-xs-3">
    			<div class="tab_user_style">
                    <ul class="nav nav-tabs tabs-left" style="border-right: none;"><!--tabs-right-->
        				<li class="active"><a href="#general" data-toggle="tab"> <?php echo lang('data');?> </a></li>
        				<li><a href="#Discount" data-toggle="tab"> <?php echo lang('discount_details');?> </a></li>
        				<?php if(!$quantity_per_serial){?>
                            <li><a href="#Stock_History" data-toggle="tab"> <?php echo lang('stock_history');?> </a></li>
                        <?php }?>
                        <li><a href="#Sales_History" data-toggle="tab"> <?php echo lang('sales_history');?> </a></li>
        			</ul>
                </div>
    		</div>
            
    		<div class="col-md-9 col-sm-9 col-xs-9">
    			<div class="tab-content">
    				<div class="tab-pane active" id="general">
    					<p>
    					  <?php
                                foreach($row_data as $column_name => $column_data)
                                {?>
                                    <div class="row static-info">
                        				<div class="col-md-5 name">
                        					 <?php echo $column_name;?>  :
                        				</div>
                        				<div class="col-md-7 value">
                        					 <?php echo $column_data;?>
                        				</div>
                        			</div>
                                <?php }?>
                             
                              <br />
                            <div class="products" style="">
                            
                                <div class="products_detials">
                                     
                                  <?php 
                                   if(!empty($products_countries)){?>
                                   <div class="col-md-12" >
                					<!-- BEGIN SAMPLE TABLE PORTLET-->
                					<div class="portlet box blue">
                					    <div class="portlet-title"><div class="caption"><?php echo lang('product_countries_detials');?></div></div>
                						<div class="portlet-body">
                							<div class="table-scrollable">
                		                      <table class="table table-bordered table-hover">
                								<thead>
                								<tr>
                									<td> <?php echo lang('country_name');?></td>
                                                    <td> <?php echo lang('currency');?></td>
                                                    
                                                    <?php if(! $quantity_per_serial){?>
                                                        <td colspan="2"> <?php echo lang('current_quantity');?></td>
                                                    <?php }?>
                                                    
                                                    <td> <?php echo lang('price');?> </td>
                                                    
                                                    <?php //if($serial_per_country && ! $quantity_per_serial){?>
                                                        <td> <?php echo lang('average_cost');?> </td>
                                                    <?php //}?>
                                                    
                                                    <td> <?php echo lang('reward_points');?></td>
                                                    <td> <?php echo lang('active');?></td>
                                                    <td> <?php echo lang('display_home');?></td>
                                                    
                								</tr>
                								</thead>
                								<tbody>
                        					        <?php foreach($products_countries as $pcountry){?>
                                                        <tr>
                                                            <td> <?php echo $pcountry->name;?></td>
                                                            <td> <?php echo $pcountry->currency;?></td>
                                                            
                                                            <?php if(! $quantity_per_serial){?>
                                                                <td> <?php echo $pcountry->active_product_quantity;?></td>
                                                                <td> <?php echo $pcountry->not_active_product_quantity;?></td>
                                                            <?php }?>
                                                            
                                                            <td> <?php echo $pcountry->price;?></td>
                                                            <?php //if($serial_per_country && ! $quantity_per_serial){?>
                                                                <td> <?php echo $pcountry->avg_cost.' '.$default_currency;?></td>
                                                            <?php //}?>
                                                            <td> <?php echo $pcountry->reward_points;?></td>
                                                            <td> 
                                                                <?php 
                                                                        if($pcountry->active == 1)
                                                                        {
                                                                            $active_value =  '<span class="badge badge-success">'.lang('active').'</span>';
                                                                        }
                                                                        elseif($pcountry->active == 0)
                                                                        {
                                                                            $active_value = '<span class="badge badge-danger">'.lang('not_active').'</span>';
                                                                        }
                                                                        echo $active_value;
                                                                ?>
                                                            </td>
                                                            <td> 
                                                                <?php  if($pcountry->display_home == 1)
                                                                        {
                                                                            $display_home_value =  '<span class="badge badge-success">'.lang('yes').'</span>';
                                                                        }
                                                                        elseif($pcountry->display_home == 0)
                                                                        {
                                                                            $display_home_value = '<span class="badge badge-danger">'.lang('no').'</span>';
                                                                        }
                                                                        echo $display_home_value;
                                                                ?>
                                                            </td>
                                                            
                                                        </tr>
                                                    <?php }?>
                                                </tbody>
                                            </table>
                                    	  </div>
                					   </div>
                					</div><!-- END SAMPLE TABLE PORTLET-->
                				</div><!-- END col-md-6-->
                             <?php }?>
                             </div><!--products_detials-->
                             <br />
                            </div><!-- products_detials_div-->
                            <br />
                            
                            <div class="products" style="">
                            
                                <div class="products_detials">
                                     
                                  <?php 
                                   if(!empty($products_customer_groups)){?>
                                   <div class="col-md-12" >
                					<!-- BEGIN SAMPLE TABLE PORTLET-->
                					<div class="portlet box blue">
                					    <div class="portlet-title"><div class="caption"><?php echo lang('group_price');?></div></div>
                						<div class="portlet-body">
                							<div class="table-scrollable">
                		                      <table class="table table-bordered table-hover">
                								<thead>
                								<tr>
                									<td> <?php echo lang('country_name');?></td>
                                                    <td> <?php echo lang('currency');?></td>
                                                    <td> <?php echo lang('group');?></td>
                                                    <td> <?php echo lang('price');?> </td>
                                           		</tr>
                								</thead>
                								<tbody>
                        					        <?php foreach($products_customer_groups as $group){?>
                                                        <tr>
                                                            <td> <?php echo $group->name;?></td>
                                                            <td> <?php echo $group->currency;?></td>
                                                            <td> <?php echo $group->title;?></td>
                                                            <td> <?php echo $group->group_price;?></td>  
                                                        </tr>
                                                    <?php }?>
                                                </tbody>
                                            </table>
                                    	  </div>
                					   </div>
                					</div><!-- END SAMPLE TABLE PORTLET-->
                				</div><!-- END col-md-6-->
                             <?php }?>
                             </div><!--products_detials-->
                             <br />
                            </div><!-- products_detials_div-->
    					</p>
                        <p>
                            <div class="col-md-12">
                                <!-------------Product Optional Fields------------------->
                                <?php if(isset($product_optional_fields) && count($product_optional_fields) != 0){?>
                                    <div class="portlet box blue">
                					    <div class="portlet-title"><div class="caption"><?php echo lang('product_optional_fields');?></div></div>
                						<div class="portlet-body">
                							<div class="table-scrollable">
                		                      <table class="table table-bordered table-hover">
                								<thead>
                    								<tr>
                    									<td> <?php echo lang('optional_fields');?></td>
                                                        <td> <?php echo lang('is_required');?></td>
                                                        <td> <?php echo lang('value');?></td>
                    								</tr>
                								</thead>
                								<tbody>
                        					        <?php foreach($product_optional_fields as $field){?>
                                                        <tr>
                                                            <td> <?php echo $field->label;?></td>
                                                            <td> <?php echo $field->required_span;?></td>
                                                            <td> <?php echo $field->default_value;?></td>
                                                            
                                                        </tr>
                                                    <?php }?>
                                                </tbody>
                                            </table>
                                    	  </div>
                					   </div>
                					</div>
                                <?php }?>
                            </div>
                            <!--Product optional fields div-->
                        </p>
                        <p>
                            <div class="col-md-12">
                                <!-------------Product Optional Fields Quantities------------------->
                                <?php if(isset($product_optional_options_quantity) && !empty($product_optional_options_quantity)){?>
                                    <div class="portlet box blue">
                					    <div class="portlet-title"><div class="caption"><?php echo lang('product_optional_fields')." ( ".lang('current_quantity')." ) ";?></div></div>
                						<div class="portlet-body">
                							<div class="table-scrollable">
                		                      <table class="table table-bordered table-hover">
                								<thead>
                    								<tr>
                    									<td> <?php echo lang('optional_fields');?></td>
                                                        <td> <?php echo lang('current_quantity');?></td>
                    								</tr>
                								</thead>
                								<tbody>
                        					        <?php foreach($product_optional_options_quantity as $field){?>
                                                        <tr>
                                                            <td> 
                                                                <?php 
                                                                $optional_fields = explode(",",$field->optional_fields);
                                                                $optional_options = explode(",",$field->selected_optional_fields);
                                                                
                                                                for($index = 0 ; $index < count($optional_options) ; $index++)
                                                                {
                                                                    $optional_id    = $optional_fields[$index];
                                                                    $option_id      = $optional_options[$index];

                                                                    $filtered = array_values( array_filter($all_optional_field_options, function ($item) use ($optional_id , $option_id )  {
                                                                        if( $item->optional_field_id === $optional_id && $item->optional_field_option_id === $option_id){
                                                                            // return $item;
                                                                            echo $item->label." : ".$item->field_value."<br />";
                                                                            return;
                                                                        }
                                                                    }));
                                                                    // echo $filtered[0]->label." : ".$filtered[0]->field_value."<br />";
                                                                }
                                                                ?>
                                                            </td>
                                                            <td> <span class="badge badge-success"> <?php echo lang('active');?> </span> = <?php echo $field->serials_count;?></td>
                                                            
                                                        </tr>
                                                    <?php }?>
                                                </tbody>
                                            </table>
                                    	  </div>
                					   </div>
                					</div>
                                <?php }?>
                            </div>
                            <!--Product optional fields Quantities div-->
                        </p>
    				</div>
                    
    				<div class="tab-pane fade" id="Discount">
    					<p>
                            <div class="portlet-body">
    					       <div class="table-scrollable">
        				        <table class="table table-bordered table-hover">
        					       <thead>
            					       <tr>
                                            <td ><?php echo lang('country');?></td>
                                            <td ><?php echo lang('price');?></td>
                                            <td ><?php echo lang('discount_start_unix_time');?></td>
                                            <td ><?php echo lang('discount_end_unix_time');?></td>
                                            <td ><?php echo lang('max_units_customers');?></td>
                                            <td ><?php echo lang('active');?></td>
            		                   </tr>
        					       </thead>
        					       <tbody>
                                       <?php //print_r($product_discount);
                                       if(isset($product_discount) && !empty($product_discount)){
                                                foreach($product_discount as $pro){
                                       ?>
        					           <tr>
        					                <td><?php echo $pro->name;?></td>
                                            <td><?php echo $pro->price;?></td>
                                            <td><?php echo date('Y/m/d ',$pro->discount_start_unix_time);?></td>
                                            <td><?php echo date('Y/m/d ',$pro->discount_end_unix_time);?></td>
                                            <td><?php echo $pro->max_units_customers;?></td>
                                            <td>
                                                <?php 
                                                    if($pro->active == 0)
                                                    {
                                                        $active = '<span class="badge badge-danger">'.lang('not_active').'</span>';    
                                                    }
                                                    elseif($pro->active == 1)
                                                    {
                                                        $active = '<span class="badge badge-success">'.lang('active').'</span>';
                                                    }
                                                    echo $active;
                                                ?>
                                            </td>
                                       </tr>
                                       <?php }
                                        }else{?>
                                            <tr> <td colspan="6" style="text-align: center;"><?php echo lang("no_data");?></td></tr>
                                       <?php } ?>
        						   </tbody>
        						</table>
                               </div>
                            </div>
    					</p>
    				</div>
    				<?php if(!$quantity_per_serial){?>
                        <div class="tab-pane fade" id="Stock_History"></div>
                     <?php }?>
                        
    			     
                    <div class="tab-pane fade" id="Sales_History"></div>
    			</div>
    		</div>
        <?php }?>
	</div>
</div>

<script type="text/javascript">
$(function(){
   get_sales('<?php echo base_url().'products/admin_products/products_sales_ajax/'; ?>');
   get_stock('<?php echo base_url().'products/admin_products/products_stock_ajax/'; ?>');
   
   $('body').on('click', '.pages_links_sales', function(e){
        e.preventDefault();
        get_sales($(this).attr('href'));
   });
   
   $('body').on('click', '.pages_links_stock', function(e){
        e.preventDefault();
        get_stock($(this).attr('href'));
   });
    
});

function get_sales(url)
{
    $.post(url, {lang_id: <?php echo $lang_id; ?>, product_id: <?php echo $product_id; ?>}, function(result){
        $('#Sales_History').html(result);
    });
}

function get_stock(url)
{
    $.post(url, {lang_id: <?php echo $lang_id; ?>, product_id: <?php echo $product_id; ?>}, function(result){
        $('#Stock_History').html(result);
    });
}

</script>
