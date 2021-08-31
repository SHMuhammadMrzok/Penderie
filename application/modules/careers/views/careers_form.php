<!--START upload single image like in GROCERY CRUD-->

<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>assets/grocery_crud/css/jquery_plugins/chosen/chosen.css" />
<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>assets/grocery_crud/css/ui/simple/jquery-ui-1.10.1.custom.min.css" />
<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>assets/grocery_crud/css/jquery_plugins/file_upload/file-uploader.css" />
<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>assets/grocery_crud/css/jquery_plugins/file_upload/jquery.fileupload-ui.css" />
<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>assets/grocery_crud/css/jquery_plugins/fancybox/jquery.fancybox.css" />
<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>assets/grocery_crud/css/jquery_plugins/file_upload/fileuploader.css" />


<script src="<?php echo base_url();?>assets/grocery_crud/js/jquery_plugins/jquery.chosen.min.js"></script>
<script src="<?php echo base_url();?>assets/grocery_crud/js/jquery_plugins/config/jquery.chosen.config.js"></script>
<script src="<?php echo base_url();?>assets/grocery_crud/js/jquery_plugins/ui/jquery-ui-1.10.3.custom.min.js"></script>
<script src="<?php echo base_url();?>assets/grocery_crud/js/jquery_plugins/tmpl.min.js"></script>
<script src="<?php echo base_url();?>assets/grocery_crud/js/jquery_plugins/jquery.fancybox-1.3.4.js"></script>
<script src="<?php echo base_url();?>assets/grocery_crud/js/jquery_plugins/jquery.fileupload.js"></script>
<script src="<?php echo base_url();?>assets/grocery_crud/js/jquery_plugins/config/jquery.fileupload.config.js"></script>
<script src="<?php echo base_url();?>assets/grocery_crud/js/jquery_plugins/config/jquery.fancybox.config.js"></script>
<!--END upload single image like in GROCERY CRUD-->

<?php
    $field_name1  = 'image';
    $unique_id1   = mt_rand();
    $unique_name1 = 's'.substr(md5($field_name1),0,8);//'s5ae0c1c8';//'p'.substr(md5($unique_id), 0, 10);

    $upload_path       = base_url().'assets/uploads/';
    $display_style     = '';
    $display_image_div = '';
    $value             = '';
 ?>

 <div class="breadcrumb">
  <div class="container">
    <div class="breadcrumb-inner">
      <ul class="list-inline list-unstyled">
        <li><a href="<?php echo base_url();?>"><?php echo lang('home');?></a></li>
        <li class='active'><?php echo lang('careers_form');?></li>
      </ul>
    </div>
  </div>
</div>

<main>
  <div class="container">
    <div class="contact-page">
      <div class="row">

        <div class="col-md-8 contact-form">
          <div class="contact-title">
            <h4><?php echo lang('careers_form');?></h4>
          </div>

          <form method="post" action="<?php echo base_url();?>careers/careers/save" enctype="multipart/form-data">

            <?php if(isset($_SESSION['error_msg'])){?>
               <div class="alert alert-danger">
                    <?php echo $this->session->flashdata('error_msg');?>
               </div><!--fail_message-->
           <?php }?>

            <?php if($this->session->flashdata('success_msg')){?>
                <div class="alert alert-success">
                    <?php echo $this->session->flashdata('success_msg');?>
                </div><!--success-->
            <?php }?>

            <div class="form-group">
              <label class="info-title" for="name"><?php echo lang('name');?><span>*</span></label>
              <?php $name_att = array(
                                        'id'       => 'name',
                                        'name'     => 'name',
                                        'class'    => 'form-control',
                                        'required' => 'required',
                                        'value'    => set_value('name')
                                     );

                    echo form_input($name_att);
              ?>
              <p class="error-alert"><?php echo form_error('name'); ?></p>
            </div>

            <div class="form-group">
              <label class="info-title" for="exampleInputEmail1"><?php echo lang('email');?> <span>*</span></label>
              <?php
                  $email_att = array(
                                      'name'          => 'email',
                                      'type'          => 'email',
                                      'id'            => 'exampleInputEmail1',
                                      'class'         => 'form-control unicase-form-control text-input',
                                      'required'      => 'required',
                                      'value'         => set_value('email')
                                   );

                  echo form_input($email_att);
              ?>
              <p class="error-alert"><?php echo form_error('email'); ?></p>
            </div>

            <div class="form-group">
              <label class="info-title" for="mobile"><?php echo lang('mobile');?> <span>*</span></label>

              	<?php
                    $mobile_att = array(
                                        'name'     => 'mobile',
                                        'id'       => 'mobile',
                                        'class'    => 'form-control',
                                        'required' => 'required',
                                        'value'    => set_value('mobile')
                                     );

                    echo form_input($mobile_att);
               ?>

              <p class="error-alert"><?php echo form_error('mobile'); ?></p>
            </div>

            <div class="form-group">
              <label class="info-title" for="applied_job_att"><?php echo lang('applied_job');?> <span>*</span></label>
             <?php
                $applied_job_att = array(
                                    'name'     => 'applied_job_att',
                                    'id'       => 'applied_job_att',
                                    'class'    => 'form-control',
                                    'required' => 'required',
                                    'value'    => set_value('applied_job_att')
                                 );

                echo form_input($applied_job_att);
             ?>
              <p class="error-alert"><?php echo form_error('applied_job_att'); ?></p>
            </div>


            <div class="form-group">
              <label class="info-title" for="cv"><?php echo lang('cv');?> <span>*</span></label>
              <?php
                /*$cv_att = array(
                                'name'     => 'userfile',
                                'id'       => 'cv',
                                'class'    => 'form-control',
                                'required' => 'required',
                                'accept'   => 'xls,xls,pdf,docs,docx,doc,text,odt,png,jpg,jpeg',
                                'value'    => set_value('cv'),
                                'placeholder' => lang('cv')

                             );

                echo form_upload($cv_att);
                */
            ?>

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
            <p class="error-alert"><?php echo form_error('cv'); ?></p>
            </div>

            <div class="form-group">
              <button type="submit" class="btn-upper btn btn-primary checkout-page-button"><?php echo lang('send');?></button>
            </div>

          </form>

        </div>

      </div>
    </div>
  </div>
</main>

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
