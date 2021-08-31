    <div class="right-content">
        <div class="row">
            <div class="col-md-12">
                 <?php echo form_open_multipart($form_action);?>
                    <br />
                    <span class="error"><?php if(isset($validation_msg)){echo $validation_msg;}?></span>
                                        
                    <div class="form-group">
                        <div class="row no-gutters">
                            <label class="col-md-2">
                                <?php echo lang('name');?>
                            </label>
                            <span class="form-control"><?php echo $general_data->username;?></span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="row no-gutters">
                            <label class="col-md-2">
                                <?php echo lang('comment');?>
                             </label>
                             <span class="form-control"><?php echo $general_data->comment;?></span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-item">
                            <label><?php echo lang('approved'); ?></label>
                            <div class="checkbox kuwait-div">
                                <label for="con_app">
                                    <?php 
                                        $active_value = false;
                                         
                                        if(isset($general_data->approved)) 
                                        {
                                            if($general_data->approved == 1)
                                            {
                                                $active_value     = true;
                                            }
                                            if($general_data->approved == 0)
                                            {
                                                $active_value     = false;
                                            }
                                        } 
                                         
                                         $approved_data = array(
                                                        'name'           => "approved"      ,
                                                        'class'          => 'form-control'  ,
                                                        'value'          => 1               ,
                                                        'data-toggle'    => 'toggle'        ,
                                                        'checked'        => set_checkbox("approved", $active_value, $active_value)
                                                        );   
                                        
                                        echo form_checkbox($approved_data);  
                                        echo form_error("approved");
                                          ?>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    
                    <div class="form-group">
                        <div class="row no-gutters align-items-left">
                            <div class="col-md-12">
                                <?php  echo isset($id) ? form_hidden('product_id', $id) : ''; ?>
                                <button class="button"><i class="fa fa-check"></i><?php echo lang('submit');?></button>
                            </div>
                        </div>
                    </div>
                    
                    
                   <?php echo form_close();?>
                
              </div><!--col-->
          </div><!--row-->
    </div><!--col-->