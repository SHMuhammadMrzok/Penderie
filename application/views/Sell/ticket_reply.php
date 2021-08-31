    <div class="right-content">
        <div class="row">
            <div class="col-md-12">
                <div class="title">
                    <h3><?php echo lang('reply');?></h3>
                </div>
                
                <?php if(isset($error_msg)){?>
                    <span class="error"><?php echo $error_msg;?></span>
                <?php }else{?>
                
                <?php   
                    $form_action = base_url().'tickets/admin_tickets/reply/'.$ticket_id;
                    echo form_open_multipart($form_action);
                ?>
                    <br />
                    <span class="error"><?php if(isset($validation_msg)){echo $validation_msg;}?></span>
                                        
                    <div class="form-group">
                        <div class="row no-gutters">
                            <label class="col-md-2">
                                <?php echo lang('ticket_status');?><span class="required">*</span>
                            </label>
                            <?php 
                                echo form_error("status_id");
                                $status_id = isset($ticket->status_id) ? $ticket->status_id : set_value('status_id') ;                   
                                echo form_dropdown('status_id', $options, $status_id, 'class="form-control col-md-10" required="required"');//select2
                            ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="row no-gutters">
                            <label class="col-md-2">
                                <?php echo lang('message_text');?><span class="required">*</span>
                             </label>
                             <?php
                                echo form_error('message_text');
                                $message_text_data = array(
                                                        'name'=> "message_text"                         ,
                                                        'class'=>"form-control col-md-10 text_editor"   ,
                                                        'value'=> set_value("message_text")
                                                        );
                                echo form_textarea($message_text_data);
                             ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="row no-gutters">
                            <label class="col-md-2"><?php echo lang('attachments');?></label>
                            <input type="file" class="col-md-10" name="userfile[]" multiple="multiple" />
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="row no-gutters align-items-left">
                            <div class="col-md-12">
                                <?php echo form_hidden('ticket_id', $ticket_id) ; ?>
                                <?php echo isset($last_updated) ? form_hidden('last_updated', $last_updated) : ''; ?>
                                <button class="button"><i class="fa fa-check"></i><?php echo lang('submit');?></button>
                            </div>
                        </div>
                    </div>
                   <?php echo form_close();?>
                <?php }?>
              </div><!--col-->
          </div><!--row-->
    </div><!--col-->