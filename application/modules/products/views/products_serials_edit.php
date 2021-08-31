<div class="form">
    <?php if(isset($error_msg)){?>
        <span class="error"><?php echo $error_msg;?></span>
    <?php }else{?>
        <span class="error"><?php if(isset($validation_msg)){echo $validation_msg;}?></span>
        <?php $att=array('class'=> 'form-horizontal form-bordered cmxform');
              echo form_open_multipart($form_action, $att);?>
            
            <div class="form-body">
              <div class="form-group">
                    <label class="control-label col-md-3"><?php echo lang('serial');?> </label>
                    
                   <div class="col-md-4">
                       <?php 
                            
                            $serial_data = array('name'=>"serial", 'class'=>"form-control" , 'value'=> isset($serial)? $serial : set_value("serial"));
                            echo form_input($serial_data);
                            echo form_error("serial");
                       ?>
                   </div>
              </div><!--serial input -->
              <div class="form-group">
                    <label class="control-label col-md-3"><?php echo lang('active');?></label>
                    
                   <div class="col-md-4">
                     <?php 
                           echo form_error('active');
                       
                            $active_value     = true ;
                            
                            if(isset($products_serial_data->active)) 
                            {
                                if($products_serial_data->active == 1)
                                {
                                    $active_value = true;
                                }
                                if($products_serial_data->active == 0)
                                {
                                    $active_value = false;
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
    <?php }?>
</div>
