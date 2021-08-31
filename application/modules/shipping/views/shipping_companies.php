<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/template/site/css/maps/styles.css" />

<?php
    $field_name1  = 'image';
    $unique_id1   = mt_rand();
    $unique_name1 = 's'.substr(md5($field_name1),0,8);

    $upload_path       = base_url().'assets/uploads/';
    $display_style     = '';
    $display_logo_div  = '';
    $value             = '';
 ?>
<div class="form">
    <span class="error"><?php if(isset($validation_msg)) echo $validation_msg; ?></span>
    <?php $att=array('class'=> 'form-horizontal form-bordered cmxform');
          echo form_open_multipart($form_action, $att);

    ?>
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
                        <label class="control-label col-md-3"><?php echo lang('service_name');?><span class="required">*</span></label>
                       <div class="col-md-4">
                          <?php
                               echo form_error("service_name");
                               $service_name_data = array('name'=>"service_name" , 'class'=>"form-control" , 'value'=> isset($general_data->service_name)? $general_data->service_name : set_value('service_name'));
                               echo form_input($service_name_data);
                          ?>
                       </div>
                    </div><!--$service_name div-->

                    <div class="form-group">
                        <label class="control-label col-md-3">
                          <?php echo lang('thumbnail');?><span class="required">*</span>
                        </label>
                        <div class="col-md-4">
                            <?php
                              echo form_error("image");
                              if(isset($general_data->logo) && ($general_data->logo!=''))
                              {
                                $display_style     = "display:none;";
                                $display_logo_div = '
                                                      <div id="success_'.$unique_id1.'" class="upload-success-url" style=" padding-top: 7px; display: block;">
                                                          <a href="'.$images_path.$general_data->logo.'" id="file_'.$unique_id1.'" class="open-file image-thumbnail" target="_blank">
                                                              <img src="'.$images_path.$general_data->logo.'" height="50px">
                                                          </a>
                                                          <a href="javascript:void(0)" id="delete_'.$unique_id1.'" class="delete-anchor">delete</a>
                                                      </div>

                                                     ';
                                $value             = $general_data->logo;
                              }
                             ?>
                            <!-- logo upload-->

                            <div class="form-div">
                                <div class="form-field-box odd" id="<?php echo $field_name1;?>_field_box">
                                    <div class="form-input-box" id="<?php echo $field_name1;?>_input_box">

                                        <span class="fileinput-button qq-upload-button" id="upload-button-<?php echo $unique_id1; ?>" style="<?php echo $display_style;?>">
                                			<span><?php echo lang('upload')?></span>
                                			<input type="file" name="<?php echo $unique_name1; ?>" class="gc-file-upload" rel="<?php echo base_url();?>uploads/upload_image/image_uploads/upload_file/<?php echo $field_name1;?>" id="<?php echo $unique_id1; ?>">
                                			<input class="hidden-upload-input" type="hidden" name="<?php echo $field_name1;?>" value="<?php if(isset($general_data->logo)){echo $general_data->logo;}?>" rel="<?php echo $unique_name1; ?>">
                                		</span>

                                        <div id="uploader_<?php echo $unique_id1; ?>" rel="<?php echo $unique_id1; ?>" class="grocery-crud-uploader" style=""></div>

                                        <?php echo $display_logo_div; ?>

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
                                  <div class="clear"></div>
                              </div>
                            </div>
                        </div>
                   </div><!-- image div-->

                   <div class="form-group">
                        <label class="control-label col-md-3"><?php echo lang('estimated_delivery_time');?></label>
                       <div class="col-md-4">
                          <?php
                               echo form_error("estimated_delivery_time");
                               $cost_data = array('name'=>"estimated_delivery_time" , 'class'=>"form-control" , 'value'=> isset($general_data->estimated_delivery_time)? $general_data->estimated_delivery_time : set_value('estimated_delivery_time'));
                               echo form_input($cost_data);
                          ?>
                          <span class="error"><?php echo lang('day');?></span>

                       </div>
                    </div><!--estimated delivery time div-->

                    <div class="form-group">
                        <label class="control-label col-md-3"><?php echo lang('type');?></label>
                       <div class="col-md-4">
                          <?php echo form_error("cost_calc_type"); ?>
                          <select name="cost_calc_type" class="form-control select2 type">

                            <option value="1" <?php echo isset($general_data->type) && $general_data->type ==1 ? 'selected':'';?>><?php echo lang('per_kg');?></option>
                            <option value="2" <?php echo isset($general_data->type) && $general_data->type ==2 ? 'selected':'';?> ><?php echo lang('calculation');?></option>
                            <option value="3" <?php echo isset($general_data->type) && $general_data->type ==3 ? 'selected':'';?> ><?php echo lang('per_piece');?></option>
                          </select>

                       </div>
                    </div><!--cost div-->

                    <div class="cost" style="display: <?php echo (isset($general_data->type) && (($general_data->type == 1)||($general_data->type == 3)) )||$mode=='add' ? 'block' : 'none' ;?>;">
                         <div class="form-group">
                          <label class="control-label col-md-3"><?php echo lang('cost');?></label>
                           <div class="col-md-4">
                              <?php
                                   echo form_error("cost_per_kgm");
                                   $cost_data = array('name'=>"cost" , 'class'=>"form-control" , 'value'=> isset($general_data->cost)? $general_data->cost : set_value('cost'));
                                   echo form_input($cost_data);
                              ?>
                              <span class="error">*zero means free</span>
                           </div>
                        </div><!--cost div-->
                    </div>

                    <div class="equation" style="display: <?php echo isset($general_data->type) && ($general_data->type == 2) ? 'block' : 'none' ;?>;">
                        <div class="form-group">
                          <label class="control-label col-md-3"><?php echo lang('intial_kgs');?></label>
                           <div class="col-md-4">
                              <?php
                                   echo form_error("intial_kgs");
                                   $to_data = array(
                                                        'name'=>"intial_kgs" ,
                                                        'class'=>"form-control" ,
                                                        'value'=> isset($general_data->intial_kgs)? $general_data->intial_kgs : set_value('intial_kgs')
                                                    );

                                   echo form_input($to_data);
                              ?>

                           </div>
                           <span class="error">Kg</span>
                        </div><!--intial_kgs div-->

                        <div class="form-group">
                          <label class="control-label col-md-3"><?php echo lang('intial_cost');?></label>
                           <div class="col-md-4">
                              <?php
                                   echo form_error("intial_cost");
                                   $intial_cost_data = array(
                                                        'name' =>"intial_cost" ,
                                                        'class'=>"form-control" ,
                                                        'value'=> isset($general_data->intial_cost)? $general_data->intial_cost : set_value('intial_cost')
                                                      );
                                   echo form_input($intial_cost_data);
                              ?>
                           </div>
                        </div><!--intial_cost div-->

                        <div class="form-group">
                          <label class="control-label col-md-3"><?php echo lang('each_kg_cost');?></label>
                           <div class="col-md-4">
                              <?php
                                   echo form_error("each_kg_cost");
                                   $each_kg_cost_data = array(
                                                        'name' =>"each_kg_cost" ,
                                                        'class'=>"form-control" ,
                                                        'value'=> isset($general_data->each_kg_cost)? $general_data->each_kg_cost : set_value('each_kg_cost')
                                                     );
                                   echo form_input($each_kg_cost_data);
                              ?>
                           </div>
                        </div><!--cost div-->
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
                    </div><!-- active -->

                </div>
            </div>

            <?php foreach($data_languages as $key=> $lang){ ?>
                <div class="tab-pane" id="tab_lang_<?php echo $lang->id; ?>">
    		      <div class="form-body">

                    <div class="form-group">
                       <label class="control-label col-md-3">
                         <?php echo lang('company_name');?><span class="required">*</span>
                       </label>
                       <div class="col-md-4">
                          <?php
                                echo form_error("name[$lang->id]");
                                $title_data = array('name'=>"name[$lang->id]" , 'class'=>"form-control" , 'value'=> isset($data[$lang->id]->name)? $data[$lang->id]->name : set_value("name[$lang->id]"));
                                echo form_input($title_data);
                          ?>
                        </div>
                    </div>

                    <?php  echo form_hidden('lang_id[]', $lang->id); ?>
                </div>

    		</div>
      <?php }?>


            <div class="form-actions">
    			<div class="row">
    				<div class="col-md-offset-3 col-md-9">
                        <?php  echo isset($id) ? form_hidden('id', $id) : ''; ?>
                       <button type="submit"  class="btn green"><i class="fa fa-check"></i><?php echo lang('submit');?></button>
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

<script>
$( "body" ).on( "change", ".type", function(){
    var type = $( ".type option:selected" ).val();

    if(type == 1 || type == 3)
    {
        $('.cost').show();
        $('.equation').hide();
    }
    else if(type == 2)
    {
        $('.cost').hide();
        $('.equation').show();
    }
});
</script>
