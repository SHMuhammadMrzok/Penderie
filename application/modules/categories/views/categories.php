<!-- START UPLOAD IMAGE LIKE IN GROCERY -->
<?php
  $field_name = 'image';
  $unique_id = mt_rand();
  $unique_name = 's'.substr(md5($field_name),0,8);//'s5ae0c1c8';//'p'.substr(md5($unique_id), 0, 10);
  $upload_path       = base_url().'assets/uploads/';
  $display_style     = '';
  $display_image_div = '';
  $value             = '';

  $field_name2        = 'image2';
  $unique_id2         = mt_rand();
  $unique_name2       = 's'.substr(md5($field_name2),0,8);//'s5ae0c1c8';//'p'.substr(md5($unique_id), 0, 10);
  $upload_path2       = base_url().'assets/uploads/';
  $display_style2     = '';
  $display_image_div2 = '';
  $value2             = '';
 ?>
<script type="text/javascript">
    $(document).ready(function() {
        <?php foreach($data_languages as $key=> $lang){ ?>
        $("#mytags_<?php echo $lang->id;?>").tagit({
           fieldName: "tags[<?php echo $lang->id;?>][]",
           singleField: false,
           singleFieldNode: $('#mySingleField_<?php echo $lang->id;?>'),
           allowSpaces: true,
           minLength: 2,
           removeConfirmation: true,
           tagSource: function( request, response ) {
            //console.log("1");
            $.ajax({
             url: "<?php echo base_url();?>categories/admin/get_suggestions",  //url to get tags
             data: { term:request.term }, //data post
             dataType: "json",
             type:"POST",
             success: function( data ) {
              response( $.map( data, function( item ) {
               return {
                label: item.label,
                value: item.value
               }
              }));
             }
            });
           }
          });
          <?php }?>
    });
</script>
<!-- END UPLOAD IMAGE LIKE IN GROCERY -->

<div class="form">
<span class="error"><?php if(isset($validation_msg)) { echo $validation_msg;}?></span>
<?php
    $att=array('class'=> 'form-horizontal form-bordered');
    echo form_open_multipart($form_action, $att);
    //echo validation_errors();
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
                        <label class="control-label col-md-3">
                          <?php echo lang('parent');?>
                        </label>
                       <div class="col-md-4">

                            <?php


                                $cat_id = isset($general_data->parent_id) ? $general_data->parent_id : set_value('parent') ;

                                echo form_dropdown('parent', $options,$cat_id,'class="form-control select2"');

                            ?>
                        </div>
                    </div>

                    <div class="form-group">

                        <label class="control-label col-md-3">
                          <?php echo lang('route');?>
                          <span class="required">*</span>
                        </label>

                       <div class="col-md-4">
                            <?php
                                echo form_error("route");
                                $route = array('name'=>"route" , 'class'=>"form-control" , 'value'=> isset($general_data->route)? $general_data->route : set_value("route"), 'pattern'=>'[A-Za-z0-9].{0,}');
                                echo form_input($route);
                            ?>
                       </div>

                       <div class="col-md-4" style="color: red;"><?php echo lang('route_valid_note');?></div>

                    </div>

                    <div class="form-group">

                        <label class="control-label col-md-3"><span class="required">*</span><?php echo lang('thumbnail');?></label>

                       <div class="col-md-4">
                             <?php
                              echo form_error("image");
                              if(isset($general_data->image) && ($general_data->image!=''))
                              {
                                $display_style     = "display:none;";
                                $display_image_div = '
                                                      <div id="success_'.$unique_id.'" class="upload-success-url" style=" padding-top: 7px; display: block;">
                                                          <a href="'.$images_path.$general_data->image.'" id="file_'.$unique_id.'" class="open-file image-thumbnail" target="_blank">
                                                              <img src="'.$images_path.$general_data->image.'" height="50px">
                                                          </a>
                                                          <a href="javascript:void(0)" id="delete_'.$unique_id.'" class="delete-anchor">delete</a>
                                                      </div>

                                                     ';
                                if(isset($general_data->image))
                                {
                                    $value = $general_data->image;
                                }else
                                {
                                    $value = set_value('image');
                                }
                              }
                             ?>
                            <!-- image upload-->

                            <div class="form-div">
                                <div class="form-field-box odd" id="<?php echo $field_name;?>_field_box">
                                    <div class="form-input-box" id="<?php echo $field_name;?>_input_box">

                                        <span class="fileinput-button qq-upload-button" id="upload-button-<?php echo $unique_id; ?>" style="<?php echo $display_style;?>">
                                			<span>Upload a file</span>
                                			<input type="file" name="<?php echo $unique_name; ?>" class="gc-file-upload" rel="<?php echo base_url();?>uploads/upload_image/image_uploads/upload_file/<?php echo $field_name;?>" id="<?php echo $unique_id; ?>">
                                			<input class="hidden-upload-input" type="hidden" name="<?php echo $field_name;?>" value="<?php if(isset($general_data->image)){echo $general_data->image;}?>" rel="<?php echo $unique_name; ?>">
                                		</span>

                                        <div id="uploader_<?php echo $unique_id; ?>" rel="<?php echo $unique_id; ?>" class="grocery-crud-uploader" style=""></div>

                                        <?php echo $display_image_div; ?>

                                        <div id="success_<?php echo $unique_id; ?>" class="upload-success-url" style="display:none; padding-top:7px;">
                                            <a href="<?php echo base_url();?>assets/uploads/" id="file_<?php echo $unique_id; ?>" class="open-file" target="_blank"></a>
                                            <a href="javascript:void(0)" id="delete_<?php echo $unique_id; ?>" class="delete-anchor">delete</a>
                                        </div>

                                        <div style="clear:both"></div>

                                        <div id="loading-<?php echo $unique_id; ?>" style="display:none">
                                            <span id="upload-state-message-<?php echo $unique_id; ?>"></span>
                                            <span class="qq-upload-spinner"></span>
                                            <span id="progress-<?php echo $unique_id; ?>"></span>
                                        </div>

                                        <div style="display:none">
                                            <a href="<?php echo base_url();?>uploads/upload_image/image_uploads/upload_file/<?php echo $field_name;?>" id="url_<?php echo $unique_id; ?>"></a>
                                        </div>

                                        <div style="display:none">
                                            <a href="<?php echo base_url();?>uploads/upload_image/image_uploads/delete_file/<?php echo $field_name;?>" id="delete_url_<?php echo $unique_id; ?>" rel=""></a>
                                        </div>

                                  </div>
                                  <div class="clear"></div>

                                </div>

                            </div>

                            <!-- image upload-->
                        </div>
                    </div>

                    <div class="form-group">

                        <label class="control-label col-md-3"><?php echo lang('icon_class');?><span class="required">*</span></label>

                       <div class="col-md-4">
                             <?php
                              echo form_error("image2");
                              if(isset($general_data->icon) && ($general_data->icon!=''))
                              {
                                $display_style2     = "display:none;";
                                $display_image_div2 = '
                                                      <div id="success_'.$unique_id2.'" class="upload-success-url" style=" padding-top: 7px; display: block;">
                                                          <a href="'.$upload_path.$general_data->icon.'" id="file_'.$unique_id2.'" class="open-file image-thumbnail" target="_blank">
                                                              <img src="'.$upload_path.$general_data->icon.'" height="50px">
                                                          </a>
                                                          <a href="javascript:void(0)" id="delete_'.$unique_id2.'" class="delete-anchor">delete</a>
                                                      </div>

                                                     ';
                                if(isset($general_data->icon))
                                {
                                    $value2 = $general_data->icon;
                                }else
                                {
                                    $value2 = set_value('image2');
                                }
                              }
                             ?>
                            <!-- image upload-->

                            <div class="form-div">
                                <div class="form-field-box odd" id="<?php echo $field_name2;?>_field_box">
                                    <div class="form-input-box" id="<?php echo $field_name2;?>_input_box">

                                        <span class="fileinput-button qq-upload-button" id="upload-button-<?php echo $unique_id2; ?>" style="<?php echo $display_style2;?>">
                                      <span>Upload a file</span>
                                      <input type="file" name="<?php echo $unique_name2; ?>" class="gc-file-upload" rel="<?php echo base_url();?>uploads/upload_image/image_uploads/upload_file/<?php echo $field_name2;?>" id="<?php echo $unique_id2; ?>">
                                      <input class="hidden-upload-input" type="hidden" name="<?php echo $field_name2;?>" value="<?php if(isset($general_data->icon)){echo $general_data->icon;}?>" rel="<?php echo $unique_name2; ?>">
                                    </span>

                                        <div id="uploader_<?php echo $unique_id2; ?>" rel="<?php echo $unique_id2; ?>" class="grocery-crud-uploader" style=""></div>

                                        <?php echo $display_image_div2; ?>

                                        <div id="success_<?php echo $unique_id2; ?>" class="upload-success-url" style="display:none; padding-top:7px;">
                                            <a href="<?php echo base_url();?>assets/uploads/" id="file_<?php echo $unique_id2; ?>" class="open-file" target="_blank"></a>
                                            <a href="javascript:void(0)" id="delete_<?php echo $unique_id2; ?>" class="delete-anchor">delete</a>
                                        </div>

                                        <div style="clear:both"></div>

                                        <div id="loading-<?php echo $unique_id2; ?>" style="display:none">
                                            <span id="upload-state-message-<?php echo $unique_id2; ?>"></span>
                                            <span class="qq-upload-spinner"></span>
                                            <span id="progress-<?php echo $unique_id2; ?>"></span>
                                        </div>

                                        <div style="display:none">
                                            <a href="<?php echo base_url();?>uploads/upload_image/image_uploads/upload_file/<?php echo $field_name2;?>" id="url_<?php echo $unique_id2; ?>"></a>
                                        </div>

                                        <div style="display:none">
                                            <a href="<?php echo base_url();?>uploads/upload_image/image_uploads/delete_file/<?php echo $field_name2;?>" id="delete_url_<?php echo $unique_id2; ?>" rel=""></a>
                                        </div>

                                  </div>
                                  <div class="clear"></div>

                                </div>

                            </div>

                            <!-- image upload-->
                        </div>
                    </div>

                    <div class="form-group">
                       <label class="control-label col-md-3"><?php echo lang('has_brands');?></label>
                       <div class="col-md-4">
                            <?php
                                echo form_error('has_brands');

                                $has_brands_value = true ;

                                if(isset($general_data->has_brands))
                                {
                                    if($general_data->has_brands == 1)
                                    {
                                        $has_brands_value = true;
                                    }
                                    if($general_data->has_brands == 0)
                                    {
                                        $has_brands_value = false;
                                    }
                                }

                                $has_brands_data = array(
                                            'name'           => 'has_brands',
                                            'class'          => 'make-switch',
                                            'value'          => 1,
                                            'checked'        => set_checkbox('has_brands', $has_brands_value, $has_brands_value),
                                            'data-on-text'   => lang('yes'),
                                            'data-off-text'  => lang('no'),
                                            );
                                echo form_checkbox($has_brands_data);
                               ?>

                        </div>
                    </div>

                    <div class="form-group">
                       <label class="control-label col-md-3"><?php echo lang('needs_shipping');?></label>
                       <div class="col-md-4">
                            <?php
                                echo form_error('needs_shipping');

                                $needs_shipping_value = true ;

                                if(isset($general_data->needs_shipping))
                                {
                                    if($general_data->needs_shipping == 1)
                                    {
                                        $needs_shipping_value = true;
                                    }
                                    if($general_data->needs_shipping == 0)
                                    {
                                        $needs_shipping_value = false;
                                    }
                                }

                                $needs_shipping_data = array(
                                            'name'           => 'needs_shipping',
                                            'class'          => 'make-switch',
                                            'value'          => 1,
                                            'checked'        => set_checkbox('needs_shipping', $needs_shipping_value, $needs_shipping_value),
                                            'data-on-text'   => lang('yes'),
                                            'data-off-text'  => lang('no'),
                                            );
                                echo form_checkbox($needs_shipping_data);
                               ?>

                        </div>
                    </div>

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

                    <div class="form-group">
                        <label class="control-label col-md-3"><?php echo lang('show_in_menu');?></label>
                        <div class="col-md-4">
                             <?php
                                 echo form_error('show_home');
                                 $show_home_value     = true ;
                                 if(isset($general_data->show_home))
                                 {
                                     if($general_data->show_home == 1)
                                     {
                                         $show_home_value = true;
                                     }
                                     if($general_data->show_home == 0)
                                     {
                                         $show_home_value = false;
                                     }
                                 }

                                 $show_home_data = array(
                                             'name'           => 'show_home',
                                             'class'          => 'make-switch',
                                             'value'          => 1,
                                             'checked'        => set_checkbox('show_home', $show_home_value, $show_home_value),
                                             'data-on-text'   => lang('yes'),
                                             'data-off-text'  => lang('no'),
                                             );
                                 echo form_checkbox($show_home_data);
                                ?>
                         </div>
                     </div>
                </div>
    		</div>


            <?php foreach($data_languages as $key=> $lang){ ?>

    		  <div class="tab-pane" id="tab_lang_<?php echo $lang->id; ?>">
    		      <div class="form-body">

                    <div class="form-group">

                        <label class="control-label col-md-3">
                          <?php echo lang('cat_name');?>
                          <span class="required">*</span>
                        </label>

                       <div class="col-md-4">
                            <?php
                                    echo form_error("cat_name[$lang->id]");
                                    $cat_name_data = array('name'=>"cat_name[$lang->id]" , 'class'=>"form-control" , 'value'=> isset($data[$lang->id]->name)? $data[$lang->id]->name : set_value("cat_name[$lang->id]"));
                                    echo form_input($cat_name_data);
                            ?>
                       </div>

                    </div>

                    <div class="form-group">

                        <label class="control-label col-md-3">
                          <?php echo lang('description');?>
                          <span class="required">*</span>
                        </label>

                        <div class="col-md-4">
                            <?php
                                    echo form_error("description[$lang->id]");
                                    $description_data = array('name'=>"description[$lang->id]" , 'class'=>"form-control" , 'value'=> isset($data[$lang->id]->description)? $data[$lang->id]->description : set_value("description[$lang->id]"));
                                    echo form_textarea($description_data);
                            ?>
                        </div>

                    </div>

                    <div class="form-group">

                        <label class="control-label col-md-3"><?php echo lang('meta_tag_keywords');?></label>
                        <div class="col-md-4">
                            <ul id="mytags_<?php echo $lang->id;?>">
                                <!-- Existing list items will be pre-added to the tags -->
                                <?php if(isset($tags[$lang->id])){
                                 foreach($tags[$lang->id] as $tag){?>
                                    <li><?php echo $tag; ?></li>
                                <?php
                                    }
                                }
                                ?>

                            </ul>

                        </div>

                    </div>

                    <div class="form-group">

                        <label class="control-label col-md-3"><?php echo lang('meta_tag_description');?></label>
                        <div class="col-md-4">
                            <?php
                                    echo form_error("meta_tag_description[$lang->id]");
                                    $meta_tag_description_data = array('name'=>"meta_tag_description[$lang->id]" , 'class'=>"form-control" , 'value'=> isset($data[$lang->id]->meta_tag_description)? $data[$lang->id]->meta_tag_description : set_value("cat_name[$lang->id]"));
                                    //$meta_tag_description_data = array('name'=>"meta_tag_description[$lang->id]" , 'class'=>"form-control" , 'value'=> isset($data[$lang->id]->meta_tag_description)? $data[$lang->id]->meta_tag_description : set_value("meta_tag_description[$lang->id]"));
                                    echo form_textarea($meta_tag_description_data);
                            ?>
                        </div>

                    </div><!-- Meta Description-->

                    <div class="form-group">

                        <label class="control-label col-md-3"><?php echo lang('meta_title');?></label>
                        <div class="col-md-4">
                            <?php
                                    echo form_error("meta_title[$lang->id]");
                                    $meta_title_data = array('name'=>"meta_title[$lang->id]" , 'class'=>"form-control" , 'value'=> isset($data[$lang->id]->meta_title)? $data[$lang->id]->meta_title : set_value("meta_title[$lang->id]"));
                                    echo form_textarea($meta_title_data);
                            ?>
                        </div>

                    </div>

                    <?php  echo form_hidden('lang_id[]', $lang->id); ?>
                </div>

    		</div>
        <?php } ?>
        <?php  echo isset($id) ? form_hidden('cat_id', $id) : ''; ?>
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

<script type="text/javascript">

			var upload_info_<?php echo $unique_id; ?> = {
				accepted_file_types: /(\.|\/)(gif|jpeg|jpg|png|tiff|doc|docx|txt|odt|xls|xlsx|pdf|ppt|pptx|pps|ppsx|mp3|m4a|ogg|wav|mp4|m4v|mov|wmv|flv|avi|mpg|ogv|3gp|3g2)$/i,
				accepted_file_types_ui : ".gif,.jpeg,.jpg,.png,.tiff,.doc,.docx,.txt,.odt,.xls,.xlsx,.pdf,.ppt,.pptx,.pps,.ppsx,.mp3,.m4a,.ogg,.wav,.mp4,.m4v,.mov,.wmv,.flv,.avi,.mpg,.ogv,.3gp,.3g2",
				max_file_size: 20971520,
				max_file_size_ui: "20MB"
			};

      var upload_info_<?php echo $unique_id2; ?> = {
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
