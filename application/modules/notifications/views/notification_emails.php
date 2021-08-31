<div class="form">
<span class="error"><?php if(isset($validation_msg)){echo $validation_msg;}?></span>
<?php $att=array('class'=> 'form-horizontal form-bordered cmxform');
      echo form_open_multipart($form_action, $att);?>
    
    <div class="form-body">
        <div class="form-group">
            <label class="control-label col-md-3"><?php echo lang('name');?><span class="required">*</span></label>
           <div class="col-md-4">
              <?php   
                   echo form_error("name");  
                   $name_data = array('name'=>"name" , 'class'=>"form-control name_spinner" , 'value'=> isset($general_data->name)? $general_data->name : set_value('name',''));
                   echo form_input($name_data);
              ?>
           </div>
        </div><!--amount div-->
        
        <div class="form-group">
            <label class="control-label col-md-3"><?php echo lang('name_of_store');?><span class="required">*</span></label>
           <div class="col-md-4">
              <?php   
                   echo form_error("store_id");  
                   $store_id = isset($general_data->store_id) ? $general_data->store_id : set_value('store_id') ;
                   echo form_dropdown('store_id', $stores, $store_id, 'class="form-control select2" style=""');
              ?>
           </div>
        </div><!--amount div-->
        
        <div class="form-group">
            <label class="control-label col-md-3"><?php echo lang('email');?><span class="required">*</span></label>
           <div class="col-md-4">
              <?php 
                   echo form_error("email");  
                   $email_data = array('name'=>"email" , 'class'=>"form-control email_spinner" , 'value'=> isset($general_data->email)? $general_data->email : set_value('email',''));
                   echo form_input($email_data);
              ?>
           </div>
        </div><!--email div-->
        
        <div class="form-group">
           <label class="control-label col-md-3"><?php echo lang('active');?></label>
           <div class="col-md-4">
             <?php 
                
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
                            'data-on-color'  => 'success',
                            'data-off-color' => 'default',
                            'value'          => 1,
                            'checked'        => set_checkbox("active", $active_value, $active_value),
                            'data-on-text'   => lang('yes'),
                            'data-off-text'  => lang('no'),
                            );    
                echo form_checkbox($active_data);  
             ?>
            </div>
        </div><!-- active -->
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
