<div class="form">
<span class="error"><?php if(isset($validation_msg)){echo $validation_msg;}?></span>
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
	   <?php foreach($data_languages as $key=> $lang){?>
	       <li <?php //echo $key==0?'class="active"':'';?> >
			<a href="#tab_lang_<?php echo $lang->id; ?>" data-toggle="tab">
                <img alt="" src="<?php echo base_url();?>/assets/template/admin/global/img/flags/<?php echo $lang->flag; ?>" />
			     <span class="langname"><?php echo $lang->name; ?> </span>
            </a>
		</li>
	  <?php } ?>
      
	</ul>
    
	<div class="tab-content">
        <div class="tab-pane active " id="tab_general">
    		      <div class="form-body">
                    <div class="form-group">
                        <label class="control-label col-md-3"> <?php echo lang('user_template');?></label>
                       <div class="col-md-4">
                           <?php 
                                echo form_error("user_template_id");
                                $user_template_id = isset($general_data->template_id) ? $general_data->template_id : set_value('user_template_id') ;                   
                                echo form_dropdown('user_template_id', $user_templates_options, $user_template_id, 'class="form-control select2"');
                            ?>
                        </div>
                    </div><!--template_id div-->
                    
                    <div class="form-group">
                        <label class="control-label col-md-3"> <?php echo lang('admin_template');?></label>
                       <div class="col-md-4">
                           <?php 
                                echo form_error("admin_template_id");
                                $admin_template_id = isset($general_data->admin_template_id) ? $general_data->admin_template_id : set_value('admin_template_id') ;                   
                               echo form_dropdown('admin_template_id', $admin_templates_options, $admin_template_id, 'class="form-control select2"');
                            ?>
                        </div>
                    </div><!--admin template_id div-->
                    
                    <div class="form-group">
                        <label class="control-label col-md-3"> <?php echo lang('send_to_user_group');?></label>
                       <div class="col-md-4">
                           <?php 
                                echo form_error("users_group_id");
                                $user_group_id = isset($general_data->user_group_id) ? $general_data->user_group_id : set_value('user_group_id') ;                   
                               echo form_dropdown('user_group_id', $user_groups, $user_group_id, 'class="form-control select2"');
                            ?>
                        </div>
                    </div><!--users group_id div-->
                    
                    <div class="form-group">
                        <label class="control-label col-md-3"><?php echo lang('event');?><span class="required">*</span></label><!--,'readonly'=>"true"-->
                       <div class="col-md-4">
                          <?php 
                               echo form_error("event");  
                               $event_data = array('name'=>"event" , 'class'=>"form-control " ,'readonly'=>"true"  , 'value'=> isset($general_data->event)? $general_data->event : set_value('event',''));
                               echo form_input($event_data);
                          ?>
                       </div>
                    </div><!--event div-->
                    
                    <div class="form-group">
                       <label class="control-label col-md-3"><?php echo lang('enable_sms');?></label>
                       <div class="col-md-4">
                            <?php 
                                echo form_error('enable_sms');
                               
                                $enable_sms_value     = true ;
                                
                                if(isset($general_data->enable_sms)) 
                                {
                                    if($general_data->enable_sms == 1)
                                    {
                                        $enable_sms_value     = true;
                                    }
                                    if($general_data->enable_sms == 0)
                                    {
                                        $enable_sms_value     = false;
                                    }
                                }  
                                
                                $enable_sms_data = array(
                                            'name'           => 'enable_sms',
                                            'class'          => 'make-switch',
                                            'value'          => 1,
                                            'checked'        => set_checkbox('enable_sms', $enable_sms_value, $enable_sms_value),
                                            'data-on-text'   => lang('yes'),
                                            'data-off-text'  => lang('no'),
                                            );    
                                echo form_checkbox($enable_sms_data); 
                               ?>
                              
                        </div>
                    </div><!--enable_sms div -->
                    
                    <div class="form-group">
                       <label class="control-label col-md-3"><?php echo lang('enable_email');?></label>
                       <div class="col-md-4">
                            <?php 
                                echo form_error('enable_email');
                               
                                $enable_email_value     = true ;
                                
                                if(isset($general_data->enable_email)) 
                                {
                                    if($general_data->enable_email == 1)
                                    {
                                        $enable_email_value     = true;
                                    }
                                    if($general_data->enable_email == 0)
                                    {
                                        $enable_email_value     = false;
                                    }
                                }  
                                
                                $enable_email_data = array(
                                            'name'           => 'enable_email',
                                            'class'          => 'make-switch',
                                            'value'          => 1,
                                            'checked'        => set_checkbox('enable_email', $enable_email_value, $enable_email_value),
                                            'data-on-text'   => lang('yes'),
                                            'data-off-text'  => lang('no'),
                                            );    
                                echo form_checkbox($enable_email_data); 
                               ?>
                              
                        </div>
                    </div><!--enable_email div -->
                    
                    <div class="form-group">
                       <label class="control-label col-md-3"><?php echo lang('enable_admin');?></label>
                       <div class="col-md-4">
                            <?php 
                                echo form_error('enable_admin');
                               
                                $enable_admin_value     = true ;
                                
                                if(isset($general_data->enable_admin)) 
                                {
                                    if($general_data->enable_admin == 1)
                                    {
                                        $enable_admin_value     = true;
                                    }
                                    if($general_data->enable_admin == 0)
                                    {
                                        $enable_admin_value     = false;
                                    }
                                }  
                                
                                $enable_admin_data = array(
                                            'name'           => 'enable_admin',
                                            'class'          => 'make-switch',
                                            'value'          => 1,
                                            'checked'        => set_checkbox('enable_admin', $enable_admin_value, $enable_admin_value),
                                            'data-on-text'   => lang('yes'),
                                            'data-off-text'  => lang('no'),
                                            );    
                                echo form_checkbox($enable_admin_data); 
                               
                               /*if($general_data->enable_admin == 0)
                                {
                                    echo  '<span class="badge badge-danger">'.lang('no').'</span>';    
                                }
                                elseif($general_data->enable_admin = 1)
                                {
                                    echo '<span class="badge badge-success">'.lang('yes').'</span>';
                                }*/
                               ?>
                              
                        </div>
                    </div><!--enable_admin div -->
                    
                    <div class="form-group">
                       <label class="control-label col-md-3"><?php echo lang('enable_admin_email');?></label>
                       <div class="col-md-4">
                            <?php 
                                echo form_error('enable_admin_email');
                               
                                $enable_admin_email_value = true ;
                                
                                if(isset($general_data->enable_admin_email)) 
                                {
                                    if($general_data->enable_admin_email == 1)
                                    {
                                        $enable_admin_email_value = true;
                                    }
                                    if($general_data->enable_admin_email == 0)
                                    {
                                        $enable_admin_email_value = false;
                                    }
                                }  
                                
                                $enable_admin_email_data = array(
                                                                    'name'           => 'enable_admin_email',
                                                                    'class'          => 'make-switch',
                                                                    'value'          => 1,
                                                                    'checked'        => set_checkbox('enable_admin_email', $enable_admin_email_value, $enable_admin_email_value),
                                                                    'data-on-text'   => lang('yes'),
                                                                    'data-off-text'  => lang('no'),
                                                                );    
                                echo form_checkbox($enable_admin_email_data); 
                               ?>
                        </div>
                    </div><!--enable adimn email div-->
                    
                    <div class="form-group">
                       <label class="control-label col-md-3"><?php echo lang('enable_admin_sms');?></label>
                       <div class="col-md-4">
                            <?php 
                                echo form_error('enable_admin_sms');
                               
                                $enable_admin_sms_value = true ;
                                
                                if(isset($general_data->enable_admin_sms)) 
                                {
                                    if($general_data->enable_admin_sms == 1)
                                    {
                                        $enable_admin_sms_value = true;
                                    }
                                    if($general_data->enable_admin_sms == 0)
                                    {
                                        $enable_admin_sms_value = false;
                                    }
                                }  
                                
                                $enable_admin_sms_data = array(
                                                                    'name'           => 'enable_admin_sms',
                                                                    'class'          => 'make-switch',
                                                                    'value'          => 1,
                                                                    'checked'        => set_checkbox('enable_admin_sms', $enable_admin_sms_value, $enable_admin_sms_value),
                                                                    'data-on-text'   => lang('yes'),
                                                                    'data-off-text'  => lang('no'),
                                                                );    
                                echo form_checkbox($enable_admin_sms_data); 
                               ?>
                        </div>
                    </div><!--Enable Admin SMS Div-->
                    
                    <div class="form-group">
                       <label class="control-label col-md-3"><?php echo lang('active');?></label>
                       <div class="col-md-4">
                            <?php 
                                echo form_error('active');
                               
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
                                            'name'           => 'active',
                                            'class'          => 'make-switch',
                                            'value'          => 1,
                                            'checked'        => set_checkbox('active', $active_value, $active_value),
                                            'data-on-text'   => lang('yes'),
                                            'data-off-text'  => lang('no'),
                                            );    
                                echo form_checkbox($active_data); 
                               ?>
                              
                        </div>
                    </div><!--active div -->
               
                </div>  
             
    		</div>
        
        
            <?php foreach($data_languages as $key=> $lang){ ?>
        
    		  <div class="tab-pane" id="tab_lang_<?php echo $lang->id; ?>">
    		      <div class="form-body">
                    <div class="form-group">
                       <label class="control-label col-md-3"><?php echo lang('name');?><span class="required">*</span></label>
                       <div class="col-md-4">
                            <?php 
                                    echo form_error("name[$lang->id]");
                                    $name_data = array('name'=>"name[$lang->id]" , 'class'=>"form-control" , 'value'=> isset($data[$lang->id]->name)? $data[$lang->id]->name : set_value("name[$lang->id]"));
                                    echo form_input($name_data);
                            ?>
                       </div>
                       
                    </div>
                    <?php  echo form_hidden('lang_id[]', $lang->id); ?>
                </div>  
             
    		</div>
        <?php } ?>
        
        
        
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
  	</div>
</div>
<?php echo form_close();?>
</div>   