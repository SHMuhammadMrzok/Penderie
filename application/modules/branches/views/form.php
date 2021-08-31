<?php 
    $field_name1  = 'image';
    $unique_id1   = mt_rand(); 
    $unique_name1 = 's'.substr(md5($field_name1),0,8);//'s5ae0c1c8';//'p'.substr(md5($unique_id), 0, 10);
    
    $upload_path       = base_url().'assets/uploads/';
    $display_style     = '';
    $display_image_div = '';
    $value             = ''; 
 ?>
 
<div class="form">
    <span class="error"><?php if(isset($validation_msg)){echo $validation_msg;}?></span>
    
    <?php   
        $att = array('class'=> 'form-horizontal form-bordered');
                      echo form_open_multipart($form_action, $att);?>
    <div class="tabbable-custom form">
	   <ul class="nav nav-tabs ">
	       <li class="active" >
    		<a href="#tab_general" data-toggle="tab">
                <span class="langname"><?php echo lang('general'); ?> </span>
            </a>
    	   </li>
          
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
        <div class="tab-pane active" id="tab_general">
	      <div class="form-body">
                <div class="form-group">
                    <label class="control-label col-md-3">
                      <?php echo lang('city_name');?><span class="required">*</span>
                    </label>
                   <div class="col-md-4">
                    <?php 
                        echo form_error("city_id");
                        $city_id = isset($general_data->city_id) ? $general_data->city_id : set_value('city_id') ; 
                        echo form_dropdown('city_id', $cities, $city_id, 'class="form-control select2"');
                    ?>
                   </div>
                 </div>
                 
                 
                    
                    <div class="form-group">
                       <label class="control-label col-md-3">
                         <?php echo lang('latitude');?><span class="required">*</span>
                       </label>
                       <div class="col-md-4">
                          <?php 
                                echo form_error("lat");
                                $lat_data = array('name'=>"lat" , 'class'=>"form-control" , 'value'=> isset($general_data->lat)? $general_data->lat : set_value("lat"));
                                echo form_input($lat_data);
                          ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                       <label class="control-label col-md-3">
                         <?php echo lang('longitude');?><span class="required">*</span>
                       </label>
                       <div class="col-md-4">
                          <?php 
                                echo form_error("lng");
                                $lng_data = array('name'=>"lng" , 'class'=>"form-control" , 'value'=> isset($general_data->lng)? $general_data->lng : set_value("lng"));
                                echo form_input($lng_data);
                          ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                       <label class="control-label col-md-3">
                         <?php echo lang('phone');?><span class="required">*</span>
                       </label>
                       <div class="col-md-4">
                          <?php 
                                echo form_error("phone");
                                $phone_data = array('name'=>"phone" , 'class'=>"form-control" , 'value'=> isset($general_data->phone)? $general_data->phone : set_value("phone"));
                                echo form_input($phone_data);
                          ?>
                        </div>
                    </div>
                 
                     <div class="form-group">
                        <label class="control-label col-md-3">
                          <?php echo lang('thumbnail');?>
                        </label>
                        <div class="col-md-4">
                             <?php
                              if(isset($general_data->image) && ($general_data->image!=''))
                              {
                                $display_style     = "display:none;";
                                $display_image_div = '
                                                      <div id="success_'.$unique_id1.'" class="upload-success-url" style=" padding-top: 7px; display: block;">
                                                          <a href="'.$upload_path.$general_data->image.'" id="file_'.$unique_id1.'" class="open-file image-thumbnail" target="_blank">
                                                              <img src="'.$upload_path.$general_data->image.'" height="50px">
                                                          </a> 
                                                          <a href="javascript:void(0)" id="delete_'.$unique_id1.'" class="delete-anchor">delete</a>
                                                      </div>
                                                      
                                                     ';
                                $value             = $general_data->image;
                              }
                             ?>
                            <!-- image upload-->
                            
                            <div class="form-div">
                                <div class="form-field-box odd" id="<?php echo $field_name1;?>_field_box">
                                    <div class="form-input-box" id="<?php echo $field_name1;?>_input_box">
                                       
                                        <span class="fileinput-button qq-upload-button" id="upload-button-<?php echo $unique_id1; ?>" style="<?php echo $display_style;?>">
                                			<span><?php echo lang('upload')?></span>
                                			<input type="file" name="<?php echo $unique_name1; ?>" class="gc-file-upload" rel="<?php echo base_url();?>uploads/upload_image/image_uploads/upload_file/<?php echo $field_name1;?>" id="<?php echo $unique_id1; ?>">
                                			<input class="hidden-upload-input" type="hidden" name="<?php echo $field_name1;?>" value="<?php if(isset($general_data->image)){echo $general_data->image;}?>" rel="<?php echo $unique_name1; ?>">
                                		</span>
                                        
                                        <div id="uploader_<?php echo $unique_id1; ?>" rel="<?php echo $unique_id1; ?>" class="grocery-crud-uploader" style=""></div>
                                        
                                        <?php echo $display_image_div; ?>
                                        
                                        <div id="success_<?php echo $unique_id1; ?>" class="upload-success-url" style="display:none; padding-top:7px;">
                                            <a href="<?php echo base_url();?>assets/uploads/" id="file_<?php echo $unique_id1; ?>" class="open-file" target="_blank"></a> 
                                            <a href="javascript:void(0)" id="delete_<?php echo $unique_id1; ?>" class="delete-anchor">delete</a> 
                                        </div>
                                        
                                        <div style="clear:both"></div>
                                        
                                        <div id="loading-<?php echo $unique_id1; ?>" style="display:none">
                                            <span id="upload-state-message-<?php echo $unique_id1; ?>"></span> 
                                            <span class="qq-upload-spinner"></span> 
                                            <span id="progress-<?php echo $unique_id1; ?>"></span>
                                        </div>
                                        
                                        <div style="display:none">
                                            <a href="<?php echo base_url();?>uploads/upload_image/image_uploads/upload_file/<?php echo $field_name1;?>" id="url_<?php echo $unique_id1; ?>"></a>
                                        </div>
                                        
                                        <div style="display:none">
                                            <a href="<?php echo base_url();?>uploads/upload_image/image_uploads/delete_file/<?php echo $field_name1;?>" id="delete_url_<?php echo $unique_id1; ?>" rel=""></a>
                                        </div>				
                                  </div>
                                  <?php echo form_error("image");?>
                                  <div class="clear"></div>
                              </div>
                            </div>
                        </div>
                   </div>
                   
                   <div class="form-group">
                       <label class="control-label col-md-3"><?php echo lang('active');?></label>
                       <div class="col-md-4">
                         <?php 
                            
                            $active_value = false ;
                            if($mode == 'edit' && !isset($validation_msg)) 
                            {
                                if($general_data->active == 1)
                                {
                                    $active_value = true;
                                }
                                if($general_data->active == 0)
                                {
                                    $active_value = false;
                                }
                            }  
                            
                            if($mode == 'add' && !isset($validation_msg))
                            {
                                $active_value = true;
                            }
                            
                            $active_data = array(
                                        'name'           => "active",
                                        'class'          => 'make-switch',
                                        'data-on-color'  => 'danger',
                                        'data-off-color'  => 'default',
                                        'value'          => 1,
                                        'checked'        => set_checkbox("active", 1, $active_value),
                                        'data-on-text'   => lang('yes'),
                                        'data-off-text'  => lang('no'),
                                        );    
                            echo form_checkbox($active_data);  
                         ?>
                        </div>
                    </div>
                 
                 
            </div>
         </div>
        <?php foreach($data_languages as $key=> $lang){ ?>
        
    		<div class="tab-pane  <?php //echo $key==0 ? "active" :'';?>" id="tab_lang_<?php echo $lang->id; ?>">
    		      <div class="form-body">
                    
                    <div class="form-group">
                       <label class="control-label col-md-3">
                         <?php echo lang('name');?><span class="required">*</span>
                       </label>
                       <div class="col-md-4">
                          <?php 
                                echo form_error("title[$lang->id]");
                                $title_data = array('name'=>"title[$lang->id]" , 'class'=>"form-control" , 'value'=> isset($data[$lang->id]->name)? $data[$lang->id]->name : set_value("title[$lang->id]"));
                                echo form_input($title_data);
                          ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                       <label class="control-label col-md-3">
                         <?php echo lang('address');?><span class="required">*</span>
                       </label>
                       <div class="col-md-4">
                          <?php 
                                echo form_error("address[$lang->id]");
                                $address_data = array('name'=>"address[$lang->id]" , 'class'=>"form-control" , 'value'=> isset($data[$lang->id]->address)? $data[$lang->id]->address : set_value("address[$lang->id]"));
                                echo form_input($address_data);
                          ?>
                        </div>
                    </div>
                    
                    <?php  echo form_hidden('lang_id[]', $lang->id); ?>
                </div>  
             
    		</div>
        <?php } ?>
        <?php  echo isset($id) ? form_hidden('vendor_id', $id) : ''; ?>
        <div class="form-actions">
			<div class="row">
				<div class="col-md-offset-3 col-md-9">
                    <?php
                        $submit_att= array('class'=>"btn green");
                       // echo form_submit('mysubmit', 'Submit',$submit_att);
                    ?>
					<button type="submit" class="btn green"><i class="fa fa-check"></i> <?php echo lang('submit');?></button>
				 
				</div>
			</div>
        </div>
        
	</div>
</div>
    		
<?php echo form_close();?>
</div>


<script type="text/javascript">
	var upload_info_<?php echo $unique_id1; ?> = {
		accepted_file_types: /(\.|\/)(gif|jpeg|jpg|png|tiff|doc|docx|txt|odt|xls|xlsx|pdf|ppt|pptx|pps|ppsx|mp3|m4a|ogg|wav|mp4|m4v|mov|wmv|flv|avi|mpg|ogv|3gp|3g2)$/i,
		accepted_file_types_ui : ".gif,.jpeg,.jpg,.png,.tiff,.doc,.docx,.txt,.odt,.xls,.xlsx,.pdf,.ppt,.pptx,.pps,.ppsx,.mp3,.m4a,.ogg,.wav,.mp4,.m4v,.mov,.wmv,.flv,.avi,.mpg,.ogv,.3gp,.3g2",
		max_file_size: 20971520,
		max_file_size_ui: "20MB"
	};
    
    

	var string_upload_file 	= "Upload a file";
	var string_delete_file 	= "Deleting file";
	var string_progress 			= "Progress: ";
	var error_on_uploading 			= "An error has occurred on uploading.";
	var message_prompt_delete_file 	= "Are you sure that you want to delete this file?";

	var error_max_number_of_files 	= "You can only upload one file each time.";
	var error_accept_file_types 	= "You are not allow to upload this kind of extension.";
	var error_max_file_size 		= "The uploaded file exceeds the 20MB directive that was specified.";
	var error_min_file_size 		= "You cannot upload an empty file.";

	var base_url = "<?php echo base_url();?>";
	var upload_a_file_string = "Upload a file";
		
</script>