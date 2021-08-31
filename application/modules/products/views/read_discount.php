<div class="row">
    <div class="col-md-12 col-sm-12">
		<div class="portlet-body">
            <?php if(isset($error_msg)){?>
                <span class="error"><?php echo $error_msg;?></span>
            <?php }else{?>
            <?php 
                if(!empty($read_data))
                {
                    /*foreach($row_data as $column_name => $column_data)
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
                    }*/
                    ?>
                    
                    <div class="table-scrollable">
                      <table class="table table-bordered table-hover">
						<thead>
						<tr>
							<td> <?php echo lang('product_name');?></td>
                            <td> <?php echo lang('country');?></td>
                            <td> <?php echo lang('price');?></td>
                            <td> <?php echo lang('discount_start_unix_time');?> </td>
                            <td> <?php echo lang('discount_end_unix_time');?></td>
                            <td> <?php echo lang('max_units_customers');?></td>
                            <td> <?php echo lang('active');?></td>
						</tr>
						</thead>
						<tbody>
					        <?php foreach ($read_data as $key=>$row){?>
                                <tr>
                                    <td> <?php echo $row->product_name;?></td>
                                    <td> <?php echo $row->country;?></td>
                                    <td> <?php echo $row->price;?></td>
                                    <td> <?php echo date('Y-m-d ',$row->discount_start_unix_time);?></td>
                                    <td> <?php echo date('Y-m-d ',$row->discount_end_unix_time);?></td>
                                    <td> <?php echo $row->max_units_customers;?></td>
                                    <td> <?php echo $row->active_value;?></td>
                                </tr>
                            <?php }?>
                        </tbody>
                    </table>
            	  </div>
                
                <?php }else{?>
                    <div class="static-info border-cell">
                        <div class="col-md-5 name">
        					  <?php echo lang("no_data");?>
        				</div>
   				    
            		</div>
                <?php }// if !empty?>
        	<?php }?>
		</div>
    </div>
</div>
</div>
</div>