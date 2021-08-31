    <?php if(isset($error_msg)){?>
        <span class="error"><?php echo $error_msg;?></span>
    <?php }else{?>
        <span class="error"><?php if(isset($validation_msg)) echo $validation_msg;?></span>
    <?php
    }    
        $att=array('class'=> 'form-horizontal form-bordered');
        echo form_open_multipart($form_action, $att);
    ?>
    
    <div class="right-content">
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <div class="form-item">
                        <label><?php echo lang('ticket_status');?></label>
                        <?php 
                            echo form_error("status_id");
                            $status_id = isset($general_data->status_id) ? $general_data->status_id : set_value('status_id') ;                   
                            
                            echo form_dropdown('status_id', $options,$status_id,'class="form-control"'); //select2
                        ?>
                    </div>
                </div>
            </div>
            
            <div class="col-12">
                <div class="form-group">
                    <div class="form-item">
                        <label><?php echo lang('assigned_to');?></label>
                        <?php
                            echo form_error("assigned_to");
                            $assigned_to = isset($general_data->assigned_to) ? $general_data->assigned_to : set_value('assigned_to') ;                   
                            
                            echo form_dropdown('assigned_to', $users_options,$assigned_to,'class="form-control"'); //select2
                        ?>                                                
                    </div>
                </div>
            </div>
                        
            <div class="form-group">
                <div class="row no-gutters align-items-left">
                    <div class="col-md-12">
                        <button class="button"><?php echo lang('save');?></button>
                    </div>
                </div>
            </div>
                                    
        </div>
    </div>
    
    <?php echo form_close();?>