<div class="form">
    <span class="error"><?php if(isset($validation_msg)){echo $validation_msg;}?></span>
    
    <?php   
        $att=array('class'=> 'form-horizontal form-bordered');
                      echo form_open_multipart(base_url().'contact_us/admin/send_replay', $att);?>
    <div class="tabbable-custom form">
	  
    
	<div class="tab-content">
        <div class="tab-pane active" id="tab_general">
	      <div class="form-body">
                    
                    <div class="form-group">
                       <label class="control-label col-md-3">
                         <?php echo lang('name');?><span class="required"></span>
                       </label>
                       <div class="col-md-4">
                          <?php 
                                echo form_error("name");
                                $title_data = array('name'=>"name" , 'class'=>"form-control" , 'value'=> isset($msg->name)? $msg->name : set_value("name"), 'readonly'=>'readonly');
                                echo form_input($title_data);
                          ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                       <label class="control-label col-md-3">
                         <?php echo lang('email');?><span class="required"></span>
                       </label>
                       <div class="col-md-4">
                          <?php 
                                echo form_error("name");
                                $title_data = array('name'=>"email" , 'class'=>"form-control" , 'value'=> isset($msg->email)? $msg->email : set_value("email"), 'readonly'=>'readonly');
                                echo form_input($title_data);
                          ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                       <label class="control-label col-md-3">
                         <?php echo lang('mobile');?><span class="required"></span>
                       </label>
                       <div class="col-md-4">
                          <?php 
                                echo form_error("mobile");
                                $title_data = array('name'=>"mobile" , 'class'=>"form-control" , 'value'=> isset($msg->mobile)? $msg->mobile : set_value("mobile"), 'readonly'=>'readonly');
                                echo form_input($title_data);
                          ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                       <label class="control-label col-md-3">
                         <?php echo lang('message_title');?><span class="required"></span>
                       </label>
                       <div class="col-md-4">
                          <?php 
                                echo form_error("title");
                                $title_data = array('name'=>"title" , 'class'=>"form-control" , 'value'=> isset($msg->title)? $msg->title : set_value("title"), 'readonly'=>'readonly');
                                echo form_input($title_data);
                          ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                       <label class="control-label col-md-3">
                         <?php echo lang('unix_time');?><span class="required"></span>
                       </label>
                       <div class="col-md-4">
                          <?php 
                                echo form_error("unix_time");
                                $title_data = array('name'=>"unix_time" , 'class'=>"form-control" , 'value'=> date('Y/m/d H:i',$msg->unix_time), 'readonly'=>'readonly');
                                echo form_input($title_data);
                          ?>
                        </div>
                    </div>
                    
                    
                    <div class="form-group">
                        <label class="control-label col-md-3"><?php echo lang('message');?></label>
                        <div class="col-md-4">
                            <?php 
                                $message_data = array('name'=> "message" , 'class'=>"form-control text_editor" , 'value'=> $msg->message, 'readonly'=>'readonly');
                                echo form_textarea($message_data);
                            ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label col-md-3"><?php echo lang('reply');?></label>
                        <div class="col-md-4">
                            <?php 
                                $reply_data = array('name'=> "replay" , 'class'=>"form-control text_editor" , 'value'=>set_value('replay'));
                                echo form_textarea($reply_data);
                            ?>
                        </div>
                    </div>
                    
                </div>
         </div>
        <input type="hidden" name="msg_id" value="<?php echo $msg->id;?>" />
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
        
	</div>
</div>
    		
<?php echo form_close();?>
</div>    	