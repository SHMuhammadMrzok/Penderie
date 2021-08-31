<div class="row">
    <div class="col-md-12 col-sm-12">
		<div class="portlet-body">
            <?php if(isset($error_msg)){?>
                <span class="error"><?php echo $error_msg;?></span>
            <?php }else{?>
            
                <?php if($controller == 'admin_purchase_orders'){
                        foreach($row_data as $column_name => $column_data) {
                            if($column_data){
                ?>
                    <div class="static-info border-cell">
        				<div class="col-md-5 name">
        					 <?php echo $column_name;?>  :
        				</div>
        				<div class="col-md-7 value">
        					 <?php echo $column_data;?>
        				</div>
        			</div>
                <?php }
                }//general data?>
                  <br />
                  
                  <br />
                <div class="products">
                 <?php foreach($products as $product) { ?>
                    <div class="products_detials col-md-12">
                        <!-- BEGIN SAMPLE TABLE PORTLET-->
    					<div class="portlet box blue">
    					    <div class="portlet-title"><div class="caption"><?php echo $product->product_name;?></div></div>
    						<div class="portlet-body">
    							<div class="table-scrollable">
    		                      <table class="table table-bordered table-hover">
    								<thead>
    								<tr>
    									<td> <?php echo lang('country_name');?></td>
                                        <td> <?php echo lang('quantity');?></td>
                                        <td> <?php echo lang('price_per_unit');?></td>
                                        <td> <?php echo lang('product_serials_count');?> </td>
                                        
    								</tr>
    								</thead>
    								<tbody>
                                        <tr>
                                        <?php $country_name = $product->country_id != 0 ? $product->name : lang('all_countries');?>
                                            <td> <?php echo $country_name;?></td>
                                            <td> <?php echo $product->quantity;?></td>
                                            <td> <?php echo $product->price_per_unit.' '.$currency_symbol;?></td>
                                            <td> <?php echo $product->serial_count;?></td>
                                        </tr>
                                    </tbody>
                                </table>
                        	  </div>
    					   </div>
    					</div><!-- END SAMPLE TABLE PORTLET-->
    				</div><!-- END col-md-6-->
                
                 </div><!--products_detials-->
                 <br />
                <?php }?>
               </div><!-- products_detials_div-->
               <?php }elseif($controller == 'admin_products'){
                        foreach($row_data as $column_name => $column_data) {
                ?>
                    <div class="static-info border-cell">
        				<div class="col-md-5 name">
        					 <?php echo $column_name;?>  :
        				</div>
        				<div class="col-md-7 value">
        					 <?php echo $column_data;?>
        				</div>
        			</div>
                <?php }//general data?>
                  <br />
                  -----------------------------------------------------------------------------
                  <br />
                <div class="products" style="width: 738px;">
                
                    <div class="products_detials col-md-12">
                         
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
                                        <td> <?php echo lang('current_quantity');?></td>
                                        <td> <?php echo lang('price');?> </td>
                                        <td> <?php echo lang('average_cost');?> </td>
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
                                                <td> <?php echo $pcountry->product_quantity;?></td>
                                                <td> <?php echo $pcountry->price;?></td>
                                                <td> <?php echo $pcountry->average_cost;?></td>
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
                  -----------------------------------------------------------------------------
                  <br />
                <div class="products" style="width: 738px;">
                
                    <div class="products_detials col-md-12">
                         
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
               <?php }else{
                    if(!empty($row_data))
                    {
                        foreach($row_data as $column_name => $column_data)
                        {
                            if(!empty($column_data))
                            {?>
                                <div class="static-info border-cell">
                    				<div class="col-md-5 name">
                    					 <?php echo $column_name;?>  :
                    				</div>
                    				<div class="col-md-7 value">
                    					 <?php echo $column_data;?>
                    				</div>
                    			</div>
                        <?php }
                        }
                        ?>
                    <?php }else{?>
                        <div class="static-info border-cell">
                            <div class="col-md-5 name">
            					  <?php echo lang("no_data");?>
            				</div>
       				    
                		</div>
                    <?php }// if !empty?>
            <?php }
            }?>
            
			
		</div>
    </div>
</div>
</div>
</div>