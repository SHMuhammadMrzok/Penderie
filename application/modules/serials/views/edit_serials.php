<div class="form">
<?php echo validation_errors(); ?>
<?php $att=array('class'=> 'form-horizontal form-bordered cmxform');
      echo form_open_multipart($form_action, $att);?>
    
    <div class="form-body">
      <div class="form-group">
            <label class="control-label col-md-3"><?php echo lang('serial');?> </label>
            
           <div class="col-md-4">
               <?php 
                   $serial_data = array('name'=>"serial" , 'class'=>"form-control" , 'value'=> isset($serial)? $serial : set_value("serial"), 'readonly'=>'readonly');
                    echo form_input($serial_data);
               ?>
           </div>
      </div><!--serial input -->
      
      <div class="form-group">
            <label class="control-label col-md-3"><?php echo lang('pin');?> </label>
            
           <div class="col-md-4">
               <?php 
                    $pin_data = array('name'=>"pin" , 'class'=>"form-control" , 'value'=> isset($pin)? $pin : set_value("pin"), 'readonly'=>'readonly');
                    echo form_input($pin_data);
               ?>
           </div>
      </div><!--amount input -->
      
      <div class="form-group">
            <label class="control-label col-md-3"><?php echo lang('amount_code');?> </label>
            
           <div class="col-md-4">
               <?php 
                    $serial_data = array('name'=>"amount" , 'class'=>"form-control" , 'value'=> isset($amount)? $amount : set_value("amount"), 'readonly'=>'readonly');
                    echo form_input($serial_data);
                    echo form_error('amount');
               ?>
           </div>
      </div><!--amount input -->
      
      <div class="form-group">
            <label class="control-label col-md-3"><?php echo lang('active');?></label>
            
           <div class="col-md-4">
             <?php 
                  
                    $active_value     = true ;
                    
                    if(isset($serial_row_data->active)) 
                    {
                        if($serial_row_data->active == 1)
                        {
                            $active_value     = true;
                        }
                        if($serial_row_data->active == 0)
                        {
                            $active_value     = false;
                        }
                    }  
                    
                    $active_data = array(
                                'name'           => "active",
                                'class'          => 'make-switch',
                                'data-on-color'  => 'danger',
                                'data-off-color' => 'default',
                                'value'          => 1,
                                'checked'        => set_checkbox("active", $active_value, $active_value),
                                'data-on-text'   => lang('yes'),
                                'data-off-text'  => lang('no'),
                                );    
                    echo form_checkbox($active_data);  
             ?>
            </div>
           
        </div><!--active input-->
        
        <div class="form-group">
            <label class="control-label col-md-3"><?php echo lang('charged');?> </label>
            
           <div class="col-md-4">
               <?php 
               
                if($serial_row_data->charged == 0)
                {
                    $charged_status =  '<span class="badge badge-danger">'.lang('no').'</span>';    
                }
                elseif($serial_row_data->charged = 1)
                {
                    $charged_status = '<span class="badge badge-success">'.lang('yes').'</span>';
                }
                echo $charged_status;
               ?>
           </div>
        </div><!--Charged input -->
           
        <div class="form-actions">
			<div class="row">
				<div class="col-md-offset-3 col-md-9">
                    <?php  echo isset($id) ? form_hidden('id', $id) : ''; ?> 
                   <button type="submit"  class="btn green"><i class="fa fa-check"></i><?php echo lang('submit');?></button>
               </div>
			</div>
        </div>
        
    </div>
    <?php echo form_close();?>
  
</div>
