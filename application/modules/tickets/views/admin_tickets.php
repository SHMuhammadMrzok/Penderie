<div class="form">
    <?php if(isset($error_msg)){?>
        <span class="error"><?php echo $error_msg;?></span>
    <?php }else{?>
        <span class="error"><?php if(isset($validation_msg)) echo $validation_msg;?></span>
         <?php 
            $att=array('class'=> 'form-horizontal form-bordered');
            echo form_open_multipart($form_action, $att);
         ?>
           <div class="form-body">
                    <div class="form-group">
                        <label class="control-label col-md-3"><?php echo lang('ticket_status');?></label>
                        <div class="col-md-4">
                            <?php 
                                echo form_error("status_id");
                                $status_id = isset($general_data->status_id) ? $general_data->status_id : set_value('status_id') ;                   
                                
                                echo form_dropdown('status_id', $options,$status_id,'class="form-control select2"');
        
                            ?>
                        </div><!--col-md-4 -->
                    </div><!--status_id -->
                    <div class="form-group">
                        <label class="control-label col-md-3"><?php echo lang('assigned_to');?></label>
                        <div class="col-md-4">
                            <?php 
                                echo form_error("assigned_to");
                                $assigned_to = isset($general_data->assigned_to) ? $general_data->assigned_to : set_value('assigned_to') ;                   
                                
                                echo form_dropdown('assigned_to', $users_options,$assigned_to,'class="form-control select2"');
        
                            ?>
                        </div><!--col-md-4 -->
                    </div><!--assain to -->
                </div> <!--form-body --> 
            <?php  echo isset($id) ? form_hidden('ticket_id', $id) : ''; ?>
            <div class="form-actions">
        		<div class="row">
        			<div class="col-md-offset-3 col-md-9">
                        <?php
                            $submit_att= array('class'=>"btn green");
                        ?>
        				<button type="submit" class="btn green"><i class="fa fa-check"></i> Submit</button>
        			</div>
        		</div>
            </div>
        <?php echo form_close();?>
    <?php }?>
</div>    	