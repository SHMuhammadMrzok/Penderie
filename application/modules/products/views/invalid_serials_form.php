<div class="form">
<span class="error"><?php if(isset($validation_msg)) echo $validation_msg;?></span>
<?php
  $att=array('class'=> 'form-horizontal form-bordered');
  echo form_open_multipart($form_action, $att);
?>
<div class="tabbable-custom form">
	<ul class="nav nav-tabs ">
         <li class="active">
			<a href="#tab_general" data-toggle="tab">
                
			     <span class="langname"><?php echo lang('general'); ?> </span>
            </a>
		 </li>
    	
	</ul>
    
	<div class="tab-content">
        <div class="tab-pane active " id="tab_general">
	      <div class="form-body">
            <div class="form-group">
                <label class="control-label col-md-3"><?php echo lang('purchase_order_id');?></label>
               <div class="col-md-4">
                  <?php 
                       $purchase_order_id_data = array('name'=>"purchase_order_id" , 'class'=>"form-control" , 'readonly'=>'readonly', 'value'=> isset($general_data->purchase_order_id)? $general_data->purchase_order_id: set_value('purchase_order_id'));
                       echo form_input($purchase_order_id_data);
                       echo form_error('purchase_order_id');
                  ?>
               </div>
            </div><!--purchase_order_id div-->
            
            <div class="form-group">
                <label class="control-label col-md-3"><?php echo lang('vendor');?></label>
               <div class="col-md-4">
                  <?php 
                       $vendor_data = array('name'=>"vendor" , 'class'=>"form-control" , 'readonly'=>'readonly', 'value'=> isset($general_data->vendor)? $general_data->vendor: set_value('vendor'));
                       echo form_input($vendor_data);
                       echo form_error('vendor');
                  ?>
               </div>
            </div><!--vendor div-->
            
            <div class="form-group">
                <label class="control-label col-md-3"><?php echo lang('product');?></label>
               <div class="col-md-4">
                  <?php 
                       $product_name_data = array('name'=>"product_name" , 'class'=>"form-control" , 'readonly'=>'readonly', 'value'=> isset($general_data->product_name)? $general_data->product_name: set_value('product_name'));
                       echo form_input($product_name_data);
                       echo form_error('product_name');
                  ?>
               </div>
            </div><!--product div-->
            
            <div class="form-group">
                <label class="control-label col-md-3"><?php echo lang('country');?></label>
               <div class="col-md-4">
                  <?php 
                       $country_data = array('name'=>"country" , 'class'=>"form-control" , 'readonly'=>'readonly', 'value'=> isset($general_data->country)? $general_data->country: set_value('country'));
                       echo form_input($country_data);
                       echo form_error('country');
                  ?>
               </div>
            </div><!--country div-->
            
            <div class="form-group">
                <label class="control-label col-md-3"><?php echo lang('invalid_serial');?></label>
               <div class="col-md-4">
                  <?php 
                       $serial_data = array('name'=>"serial" , 'class'=>"form-control" , 'readonly'=>'readonly', 'value'=> isset($general_data->serial)? $general_data->serial: set_value('serial'));
                       echo form_input($serial_data);
                       echo form_error('serial');
                  ?>
               </div>
            </div><!--country div-->
            
            <div class="form-group">
                <label class="control-label col-md-3"><?php echo lang('serial_add_date');?></label>
               <div class="col-md-4">
                  <?php 
                       $serial_add_date_data = array('name'=>"unix_time" , 'class'=>"form-control" , 'readonly'=>'readonly', 'value'=> isset($general_data->unix_time)? date('Y / m / d H:i', $general_data->unix_time): set_value('unix_time'));
                       echo form_input($serial_add_date_data);
                       echo form_error('unix_time');
                  ?>
               </div>
            </div><!--serial_add_date div-->
            
            <div class="form-group">
                <label class="control-label col-md-3"><?php echo lang('order_id');?></label>
               <div class="col-md-4">
                  <?php 
                       $order_id_data = array('name'=>"order_id" , 'class'=>"form-control" , 'readonly'=>'readonly', 'value'=> $general_data->order_id? $general_data->order_id: set_value('order_id'));
                       echo form_input($order_id_data);
                       echo form_error('order_id');
                  ?>
               </div>
            </div><!--order_id div-->
            
            <div class="form-group">
                <label class="control-label col-md-3"><?php echo lang('order_date');?></label>
               <div class="col-md-4">
                  <?php 
                       $order_date_data = array('name'=>"order_date" , 'class'=>"form-control" , 'readonly'=>'readonly', 'value'=> isset($general_data->order_date)? date('Y / m / d H:i', $general_data->order_date): set_value('order_date'));
                       echo form_input($order_date_data);
                       echo form_error('order_date');
                  ?>
               </div>
            </div><!--order_date div-->
            
            <div class="form-group">
                <label class="control-label col-md-3"><?php echo lang('status');?></label>
                <div class="col-md-4">
                  <?php
                       $default_status_id = isset($general_data->invalid_status_id) ? $general_data->invalid_status_id : set_value('invalid_status_id') ;                   
                       echo form_dropdown('invalid_status_id', $status_options, $default_status_id, 'class="form-control select2"');
                   ?>
                </div>
            </div><!--invalid_status_id div-->
            
            <div class="form-group">
                <label class="control-label col-md-3">
                  <?php echo lang('sent_to_vendor');?>
                </label>
               <div class="col-md-4">
                 <?php 
                       echo form_error('sent_to_vendor');
                   
                    $sent_to_vendor_value = true ;
                    
                    if(isset($general_data->sent_to_vendor)) 
                    {
                        if($general_data->sent_to_vendor == 1)
                        {
                            $sent_to_vendor_value  = true;
                        }
                        if($general_data->sent_to_vendor == 0)
                        {
                            $sent_to_vendor_value  = false;
                        }
                    }  
                    
                    $sent_to_vendor_data = array(
                                    'name'           => "sent_to_vendor",
                                    'class'          => 'make-switch',
                                    'data-on-color'  => 'danger',
                                    'data-off-color' => 'default',
                                    'value'          => 1,
                                    'checked'        => set_checkbox("sent_to_vendor", $sent_to_vendor_value, $sent_to_vendor_value),
                                    'data-on-text'   => lang('yes'),
                                    'data-off-text'  => lang('no'),
                                    );    
                    echo form_checkbox($sent_to_vendor_data);  
                 ?>
                </div>
            </div>
            
            <div class="form-group">
                <label class="control-label col-md-3"><?php echo lang('serial');?></label>
               <div class="col-md-4">
                  <?php 
                       if(isset($replaced_serial) && $replaced_serial != ''){?>
                          <span><?php echo $replaced_serial;?></span>
                  <?php
                       }
                       else
                       {
                           $new_serial_data = array('name'=>"new_serial", 'class'=>"form-control", 'value'=> isset($general_data->new_serial)? $general_data->new_serial: set_value('new_serial'));
                           echo form_input($new_serial_data);
                           echo form_error('new_serial');
                       }
                  ?>
               </div>
            </div><!--order_date div-->
            
       
    </div><!--form_body-->
            </div><!--general tab-->   
               
        <?php  echo isset($id) ? form_hidden('id', $id) : ''; ?>
        <div class="form-actions">
			<div class="row">
				<div class="col-md-offset-3 col-md-9">
                    <?php
                        $submit_att= array('class'=>"btn green");
                    ?>
					<button type="submit" class="btn green"><i class="fa fa-check"></i> <?php echo lang('submit');?></button>
				</div>
			</div>
        </div>
        <?php echo form_close();?>
        
	</div>
</div>
    		

</div>    	
