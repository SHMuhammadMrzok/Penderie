<link href="<?php echo base_url();?>assets/template/admin/global/plugins/dropzone/css/dropzone.css" rel="stylesheet"/>

<!-- START UPLOAD IMAGE LIKE IN GROCERY -->
<?php
    $field_name        = 'image';
    $unique_id         = mt_rand();
    $unique_name       = 's'.substr(md5($field_name),0,8);//'s5ae0c1c8';//'p'.substr(md5($unique_id), 0, 10);
    $upload_path       = base_url().'assets/uploads/';
    $display_style     = '';
    $display_image_div = '';
    $value             = '';
 ?>
<!--<script type="text/javascript">
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
             url: "add_art.php",  //url to get tags
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
</script>-->
<!-- END UPLOAD IMAGE LIKE IN GROCERY -->


<div class="form">

<span class="error"><?php if(isset($validation_msg)) echo $validation_msg;?></span>
<?php
 $att=array('class'=> 'form-horizontal form-bordered cmxform' , 'id'=>'validate-me-plz');
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
        <div class="tab-pane  active" id="tab_general">
	      <div class="form-body">
            <div class="form-group">
                <label class="control-label col-md-3">
                  <?php echo lang('location');?><span class="required">*</span>
                </label>
               <div class="col-md-4">
                <?php  echo form_error('location');?>
                    <select name="location" class="form-control">
                        <option value="top" <?php echo (isset($general_data->location) && $general_data->location=='top')? 'selected' : ''; ?>><?php echo "slider";?></option>
                        <option value="ads_2" <?php echo (isset ($general_data->location) && $general_data->location == "ads_2" )? 'selected' : '';?>><?php echo "top";?></option>
                        <option value="middle" <?php echo (isset ($general_data->location) && $general_data->location == "middle" )? 'selected' : '';?>><?php echo "middle";?></option>
                        <option value="bottom" <?php echo (isset ($general_data->location) && $general_data->location == "bottom" )? 'selected' : '';?>><?php echo "bottom";?></option>
                        <?php /*<option value="menu_cats" <?php echo (isset($general_data->location) && $general_data->location=='menu_cats')? 'selected' : ''; ?>><?php echo "menu";?></option>
                        <option value="side" <?php echo (isset ($general_data->location) && $general_data->location == "side" )? 'selected' : '';?>><?php echo "side";?></option>
                        <option value="ads_3" <?php echo (isset ($general_data->location) && $general_data->location == "ads_3" )? 'selected' : '';?>><?php echo "ads_3";?></option>
                        <option value="ads_5" <?php echo (isset ($general_data->location) && $general_data->location == "ads_5" )? 'selected' : '';?>><?php echo "ads_5";?></option>
                        */?>

                    </select>
              </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3"><?php echo lang('cat_name');?><span class="required"></span></label>
                <div class="col-md-4" id="available_cats">
                    <?php
                         $cat_id = isset($general_data->category_id) ? $general_data->category_id : set_value('cat_id') ;
                          echo form_dropdown('cat_id', $cats,$cat_id,'class="form-control select2" id="cat_id"');
                          echo form_error('cat_id');
                    ?>

               </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3">
                  <?php echo lang('image');?><span class="required">*</span>
                </label>
               <div class="col-md-4">
                <?php
                 echo form_error('image');
                  if(isset($general_data->image) && ($general_data->image!=''))
                  {
                    $display_style     = "display:none;";
                    $display_image_div = '
                                            <div id="success_'.$unique_id.'" class="upload-success-url" style=" padding-top:7px;">
                                                <a href="'.$images_path.$general_data->image.'" id="file_'.$unique_id.'" class="open-file image-thumbnail">
                                                    <img src="'.$images_path.$general_data->image.'" height="50px">
                                                </a>
                                                <a href="javascript:void(0)" id="delete_'.$unique_id.'" class="delete-anchor">delete</a>
                                            </div>
                                        ';
                    $value             = $general_data->image;
                  }
                 ?>
                <!-- image upload-->

                <div class="form-div">
                    <div class="form-field-box odd" id="<?php echo $field_name;?>_field_box">
                        <div class="form-input-box" id="<?php echo $field_name;?>_input_box">

                            <span class="fileinput-button qq-upload-button" id="upload-button-<?php echo $unique_id; ?>" style="<?php echo $display_style;?>">
                    			<span>Upload a file</span>
                    			<input type="file" name="<?php echo $unique_name; ?>" class="gc-file-upload" rel="<?php echo base_url();?>uploads/upload_image/image_uploads/upload_file/<?php echo $field_name;?>" id="<?php echo $unique_id; ?>">
                    			<input class="hidden-upload-input" type="hidden" name="<?php echo $field_name;?>" value="<?php echo $value;?>" rel="<?php echo $unique_name; ?>">
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
                                <a href="<?php echo base_url();?>index.php/users/admin_users/index/delete_file/<?php echo $field_name;?>" id="delete_url_<?php echo $unique_id; ?>" rel=""></a>
                            </div>

                      </div>
                      <div class="clear"></div>

                    </div>

                </div>

                <!-- image upload-->
               </div>
            </div>

           <div class="form-group">
                <label class="control-label col-md-3">
                  <?php echo lang('url');?>
                </label>
                <div class="col-md-4">
                    <?php
                            echo form_error("url");
                            $url_data = array('name'=>"url", 'class'=>"form-control" ,'value'=> isset($general_data->url)? $general_data->url : set_value("url") );
                            echo form_input($url_data);
                    ?>
               </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3">
                  <?php echo lang('target');?><span class="required">*</span>
                </label>
               <div class="col-md-4">
                <?php  echo form_error('target');?>
                    <select name="target" class="form-control">
                        <option value="_blank" <?php echo (isset ($general_data->target) && $general_data->target == "_blank" )? 'selected' : '';?>><?php echo lang("blank");?></option>
                        <option value="_self" <?php echo (isset($general_data->target) && $general_data->target=='_self')? 'selected' : ''; ?>><?php echo lang("self");?></option>
                    </select>
              </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3">
                  <?php echo lang('active');?><span class="required">*</span>
                </label>
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
        <?php foreach($data_languages as $key=> $lang){ ?>

    		<div class="tab-pane" id="tab_lang_<?php echo $lang->id; ?>">
    		      <div class="form-body">
                    <div class="form-group">
                        <label class="control-label col-md-3">
                            <?php echo lang('title');?>
                        </label>
                       <div class="col-md-4">
                        <?php
                                echo form_error("title[$lang->id]");
                                $title_data = array('name'=>"title[$lang->id]" , 'class'=>"form-control" ,'value'=> isset($data[$lang->id]->title)? $data[$lang->id]->title : set_value("title[$lang->id]") );
                                echo form_input($title_data);

                        ?>
                       </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3">
                            <?php echo lang('description');?>
                        </label>
                        <div class="col-md-4">
                        <?php
                                //echo form_error("description[$lang->id]");
                                $text_data = array('name'=> "description[$lang->id]" , 'class'=>"form-control" , 'value'=> isset($data[$lang->id]->description)? $data[$lang->id]->description : set_value("description[$lang->id]"));
                                echo form_textarea($text_data);
                        ?>
                        </div>
                    </div>

                    <?php  echo form_hidden('lang_id[]', $lang->id); ?>
                </div>

    		</div>
        <?php } ?>

        <div class="form-actions">
			<div class="row">
				<div class="col-md-offset-3 col-md-9">
                    <?php  echo isset($id) ? form_hidden('advertisement_id', $id) : ''; ?>
                 	<button type="submit" class="btn green"><i class="fa fa-check"></i> <?php echo lang('submit');?></button>
				 </div>
			</div>
        </div>

	</div>
</div>

<?php echo form_close();?>
</div>
<style>
.error{
    color: #a94442;
}
input.error {
  border: 1px dotted red;
}
</style>
<!--<script type="text/javascript" src="http://code.jquery.com/jquery-2.1.0.js"></script>-->

<script src="<?php echo base_url();?>assets/template/admin/global/plugins/dropzone/dropzone.js"></script>
<script src="<?php echo base_url();?>assets/template/admin/pages/scripts/form-dropzone.js"></script>
<script>
jQuery(document).ready(function() {

         FormDropzone.init();
});
</script>
<script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
<script type="text/javascript">
        $('#validate-me-plz').validate();
</script>


<script type="text/javascript">

			var upload_info_<?php echo $unique_id; ?> = {
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

			var base_url = "";
			var upload_a_file_string = "Upload a file";

</script>
