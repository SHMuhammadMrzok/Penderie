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
         <?php if(isset($variables_data)){?>
             <li class="">
        		<a href="#tab_variables" data-toggle="tab">
        		     <span class="langname"><?php echo lang('template_variables'); ?> </span>
                </a>
        	 </li>
         <?php }?>
	   <?php foreach($data_languages as $key=> $lang){?>
	       <li>
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
                    </div>
                </div>  
             
    		</div>
            <?php if(isset($variables_data)){?>
                <div class="tab-pane " id="tab_variables">
        	      <div class="form-body">
                    <?php if(count($variables_data)>0){?>
                        <div class="portlet-body col-lg-6" style="float: none;margin: 0 auto;display: block;">
        					<div class="table-scrollable">
        						<table class="table table-bordered table-striped table-condensed flip-content">
            						<thead class="flip-content">
            						<tr>
            							<th><?php echo lang('variable');?></th>
            							<th><?php echo lang('definition');?></th>
            						</tr>
            						</thead>
            						<tbody>
                                        <?php foreach($variables_data as $row){?>
                    						<tr>
                    							<td>
                    								{<?php echo $row->variable;?>}
                    							</td>
                    							<td>
                    								 <?php echo $row->variable_text;?>
                    							</td>
                    							
                    						</tr>
                                        <?php }?>
            						</tbody>
        					   </table>
        					</div>
        	           </div>
                    <?php }else{?>
                        <div class="form-group" style="border: 0;">
                            
                            <label class="control-label col-md-3">
                              <span><?php echo lang('no_defitions_available');?></span>
                            </label>
                           
                           
                        </div>                
                        
                    <?php }?>
                  </div>     
        	    </div>
            <?php }?>
        
            <?php foreach($data_languages as $key=> $lang){ ?>
        
    		  <div class="tab-pane" id="tab_lang_<?php echo $lang->id; ?>">
    		      <div class="form-body">
                    
                    <div class="form-group">
                        
                        <label class="control-label col-md-3">
                          <?php echo lang('name');?>
                          <span class="required">*</span>
                        </label>
                       
                       <div class="col-md-4">
                            <?php 
                                    echo form_error("name[$lang->id]");
                                    $template_data = array('name'=>"name[$lang->id]" , 'class'=>"form-control" , 'value'=> isset($data[$lang->id]->name)? $data[$lang->id]->name : set_value("name[$lang->id]"));
                                    echo form_input($template_data);
                            ?>
                       </div>
                       
                    </div>
                    
                    <div class="form-group">
                        
                        <label class="control-label col-md-3">
                          <?php echo lang('email_template_title');?>
                          <span class="required">*</span>
                        </label>
                        
                        <div class="col-md-4">
                            <?php 
                                    echo form_error("email_template_title[$lang->id]");
                                    $email_template_title = array('name'=>"email_template_title[$lang->id]" , 'class'=>"form-control" , 'value'=> isset($data[$lang->id]->email_title)? $data[$lang->id]->email_title : set_value("email_template_title[$lang->id]"));
                                    echo form_input($email_template_title);
                            ?>
                        </div>
                    
                    </div>
                    
                    <div class="form-group">
                        
                        <label class="control-label col-md-3">
                          <?php echo lang('email_template');?>
                          <span class="required">*</span>
                        </label>
                        
                        <div class="col-md-4">
                            <?php 
                                    echo form_error("email_template[$lang->id]");
                                    $email_template = array('name'=>"email_template[$lang->id]" , 'class'=>"form-control" , 'value'=> isset($data[$lang->id]->email_template)? $data[$lang->id]->email_template : set_value("email_template[$lang->id]"));
                                    echo form_textarea($email_template);
                            ?>
                        </div>
                    
                    </div>
                    
                    <div class="form-group">
                        
                        <label class="control-label col-md-3">
                          <?php echo lang('sms_template');?>
                          <span class="required">*</span>
                        </label>
                        
                        <div class="col-md-4">
                            <?php 
                                    echo form_error("sms_template[$lang->id]");
                                    $sms_template = array('name'=>"sms_template[$lang->id]" , 'class'=>"form-control" , 'value'=> isset($data[$lang->id]->sms_template)? $data[$lang->id]->sms_template : set_value("sms_template[$lang->id]"));
                                    echo form_textarea($sms_template);
                            ?>
                        </div>
                    
                    </div>
                    
                    
                    

                    <?php  echo form_hidden('lang_id[]', $lang->id); ?>
                </div>  
             
    		</div>
        <?php } ?>
        
        
        <?php  echo isset($id) ? form_hidden('temp_id', $id) : ''; ?>
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
        
	</div>
</div>
    		
<?php echo form_close();?>
</div>    	
