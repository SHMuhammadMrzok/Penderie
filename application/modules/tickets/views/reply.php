<div class="form">
    <?php if(isset($error_msg)){?>
        <span class="error"><?php echo $error_msg;?></span>
    <?php }else{?>
        <span class="error"><?php if(isset($validation_msg)){echo $validation_msg;}?></span>
    
        <?php   
            $form_action = base_url().'tickets/admin_tickets/reply/'.$ticket_id;
            $att = array('class'=> 'form-horizontal form-bordered');
                          echo form_open_multipart($form_action, $att);?>
        <div class="tabbable-custom form">
        
    	<div class="tab-content">
            <div class="tab-pane active" id="tab_general">
    	      <div class="form-body">
                    <div class="form-group">
                        <label class="control-label col-md-3">
                          <?php echo lang('attachments');?>
                        </label>
                       <div class="col-md-4">
                        <input type="file" name="userfile[]" multiple />
                       </div>
                     </div>
                    
                    <div class="form-group">
                        <label class="control-label col-md-3">
                          <?php echo lang('ticket_status');?><span class="required">*</span>
                        </label>
                       <div class="col-md-4">
                        <?php 
                            echo form_error("status_id");
                            $status_id = isset($ticket->status_id) ? $ticket->status_id : set_value('status_id') ;                   
                            
                            echo form_dropdown('status_id', $options, $status_id, 'class="form-control select2" required="required"');
                            
                        ?>
                       </div>
                     </div>
                     
                     <div class="form-group">
                        <label class="control-label col-md-3"><?php echo lang('message_text');?><span class="required">*</span></label>
                        <div class="col-md-4">
                        <?php 
                            echo form_error('message_text');
                            $message_text_data = array('name'=> "message_text", 'class'=>"form-control text_editor", 'value'=> set_value("message_text"));
                            echo form_textarea($message_text_data);
                        ?>
                        </div>
                    </div>
                    
                </div>
             </div>
            
            <?php  echo  form_hidden('ticket_id', $ticket_id) ; ?>
            <?php echo isset($last_updated) ? form_hidden('last_updated', $last_updated) : ''; ?>
            <div class="form-actions">
    			<div class="row">
    				<div class="col-md-offset-3 col-md-9">
                        <?php
                            $submit_att= array('class'=>"btn green");
                           // echo form_submit('mysubmit', 'Submit',$submit_att);
                        ?>
    					<button type="submit" class="btn green"><i class="fa fa-check"></i> Submit</button>
    				 
    				</div>
    			</div>
            </div>
            
    	</div>
    </div>
        		
    <?php echo form_close();?>
    <?php }?>
</div>    	