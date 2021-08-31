<?php
    $field_name1  = 'image';
    $unique_id1   = mt_rand();
    $unique_name1 = 's'.substr(md5($field_name1),0,8);//'s5ae0c1c8';//'p'.substr(md5($unique_id), 0, 10);

    $upload_path       = base_url().'assets/uploads/';
    $display_style     = '';
    $display_image_div = '';
    $value             = '';
 ?>


<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
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
                  <?php echo lang('phone');?>
                </label>
               <div class="col-md-4">
                <?php
                    echo form_error("phone");
                    $phone_data = array(
                                        'name'=>"phone" ,
                                        'class'=>"form-control" ,
                                        'value'=> isset($general_data->phone)? $general_data->phone : set_value("phone")
                                        );
                    echo form_input($phone_data);
                ?>
               </div>
             </div>

                <div class="form-group">
                   <label class="control-label col-md-3">
                     <?php echo lang('facebook');?>
                   </label>
                   <div class="col-md-4">
                      <?php
                            echo form_error("lat");
                            $fb_data = array(
                                        'name'=>"facebook" ,
                                        'class'=>"form-control" ,
                                        'value'=> isset($general_data->facebook)? $general_data->facebook : set_value("facebook")
                                        );
                            echo form_input($fb_data);
                      ?>
                    </div>
                </div>

                <div class="form-group">
                   <label class="control-label col-md-3">
                     <?php echo lang('twitter');?>
                   </label>
                   <div class="col-md-4">
                      <?php
                            echo form_error("twitter");
                            $twitter_data = array(
                                        'name'=>"twitter" ,
                                        'class'=>"form-control" ,
                                        'value'=> isset($general_data->twitter)? $general_data->twitter : set_value("twitter")
                                        );
                            echo form_input($twitter_data);
                      ?>
                    </div>
                </div>

                <div class="form-group">
                   <label class="control-label col-md-3">
                     <?php echo lang('instagram');?>
                   </label>
                   <div class="col-md-4">
                      <?php
                            echo form_error("instagram");
                            $instagram_data = array(
                                        'name'=>"instagram" ,
                                        'class'=>"form-control" ,
                                        'value'=> isset($general_data->instagram)? $general_data->instagram : set_value("instagram")
                                        );
                            echo form_input($instagram_data);
                      ?>
                    </div>
                </div>

                <div class="form-group">
                   <label class="control-label col-md-3">
                     <?php echo lang('youtube');?>
                   </label>
                   <div class="col-md-4">
                      <?php
                            echo form_error("youtube");
                            $youtube_data = array(
                                        'name'=>"youtube" ,
                                        'class'=>"form-control" ,
                                        'value'=> isset($general_data->youtube)? $general_data->youtube : set_value("youtube")
                                        );
                            echo form_input($youtube_data);
                      ?>
                    </div>
                </div>

                <div class="form-group">
                   <label class="control-label col-md-3">
                     <?php echo lang('commission_type');?>
                   </label>
                   <div class="col-md-4">
                      <select name="commission_type" class="form-control select2">
                        <option value="amount" <?php echo isset($general_data->commission_type)&&$general_data->commission_type=='amount' ? 'selected':''; ?>><?php echo lang('Amount_of_money');?></option>
                        <option value="percent" <?php echo isset($general_data->commission_type)&&$general_data->commission_type=='percent' ? 'selected':''; ?>><?php echo lang('percent');?></option>
                      </select>
                    </div>
                </div>

                <div class="form-group">
                   <label class="control-label col-md-3">
                     <?php echo lang('commission');?>
                   </label>
                   <div class="col-md-4">
                      <?php
                            echo form_error("commission");
                            $commission_data = array(
                                        'name'=>"commission" ,
                                        'class'=>"form-control" ,
                                        'value'=> isset($general_data->commission)? $general_data->commission : set_value("commission")
                                        );
                            echo form_input($commission_data);
                      ?>
                    </div>
                </div>

                 <div class="form-group">
                    <label class="control-label col-md-3">
                      <?php echo lang('thumbnail');?><span class="required">*</span>
                    </label>

                    <?php /*<div class="col-md-4">
                      <a href="#upload_modal" data-toggle="modal" class="btn red-sunglo" style="margin-top: 3px;"><?php echo lang('upload');?></a>
                    </div>
                    */?>

                    <div class="col-md-4">
                         <?php
                          if(isset($general_data->image) && ($general_data->image!=''))
                          {
                            $display_style     = "display:none;";
                            $display_image_div = '
                                                  <div id="success_'.$unique_id1.'" class="upload-success-url" style=" padding-top: 7px; display: block;">
                                                      <a href="'.$this->data['images_path'].$general_data->image.'" id="file_'.$unique_id1.'" class="open-file image-thumbnail" target="_blank">
                                                          <img src="'.$this->data['images_path'].$general_data->image.'" height="50px">
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
                   <label class="control-label col-md-3"><?php echo lang('show_in_main_page');?></label>
                   <div class="col-md-4">
                     <?php
                        $show_in_main_page_value = false ;
                        if($mode == 'edit' && !isset($validation_msg))
                        {
                            if(isset($general_data->show_in_main_page) && $general_data->show_in_main_page == 1)
                            {
                                $show_in_main_page_value = true;
                            }
                            else
                            {
                                $show_in_main_page_value = false;
                            }
                        }

                        if($mode == 'add' && !isset($validation_msg))
                        {
                            $show_in_main_page_value = true;
                        }

                        $show_in_main_page_data = array(
                                    'name'           => "show_in_main_page",
                                    'class'          => 'make-switch',
                                    'data-on-color'  => 'danger',
                                    'data-off-color'  => 'default',
                                    'value'          => 1,
                                    'checked'        => set_checkbox("show_in_main_page", 1, $show_in_main_page_value),
                                    'data-on-text'   => lang('yes'),
                                    'data-off-text'  => lang('no'),
                                    );
                        echo form_checkbox($show_in_main_page_data);
                     ?>
                    </div>
                </div>

                <div class="form-group">
                   <label class="control-label col-md-3"><?php echo lang('show_in_menu');?></label>
                   <div class="col-md-4">
                     <?php

                        $show_in_menu_value = false ;
                        if($mode == 'edit' && !isset($validation_msg))
                        {
                            if(isset($general_data->show_in_menu) && $general_data->show_in_menu == 1)
                            {
                                $show_in_menu_value = true;
                            }
                            else
                            {
                                $show_in_menu_value = false;
                            }
                        }

                        if($mode == 'add' && !isset($validation_msg))
                        {
                            $show_in_menu_value = true;
                        }

                        $show_in_menu_data = array(
                                    'name'           => "show_in_menu",
                                    'class'          => 'make-switch',
                                    'data-on-color'  => 'danger',
                                    'data-off-color'  => 'default',
                                    'value'          => 1,
                                    'checked'        => set_checkbox("show_in_menu", 1, $show_in_menu_value),
                                    'data-on-text'   => lang('yes'),
                                    'data-off-text'  => lang('no'),
                                    );
                        echo form_checkbox($show_in_menu_data);
                     ?>
                    </div>
                </div>

               <div class="form-group">
                   <label class="control-label col-md-3"><?php echo lang('active');?></label>
                   <div class="col-md-4">
                     <?php

                        $active_value = false ;
                        if($mode == 'edit' && !isset($validation_msg))
                        {
                            if(isset($general_data->active) && $general_data->active == 1)
                            {
                                $active_value = true;
                            }
                            else
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

    		<div class="tab-pane" id="tab_lang_<?php echo $lang->id; ?>">
    		      <div class="form-body">

                    <div class="form-group">
                       <label class="control-label col-md-3">
                         <?php echo lang('name');?><span class="required">*</span>
                       </label>
                       <div class="col-md-4">
                          <?php
                                echo form_error("name[$lang->id]");
                                $name_data = array('name'=>"name[$lang->id]" , 'class'=>"form-control" , 'value'=> isset($data[$lang->id]->name)? $data[$lang->id]->name : set_value("name[$lang->id]"));
                                echo form_input($name_data);
                          ?>
                        </div>
                    </div>

                    <div class="form-group">
                       <label class="control-label col-md-3">
                         <?php echo lang('address');?>
                       </label>
                       <div class="col-md-4">
                          <?php
                                echo form_error("address[$lang->id]");
                                $address_data = array('name'=>"address[$lang->id]" , 'class'=>"form-control" , 'value'=> isset($data[$lang->id]->address)? $data[$lang->id]->address : set_value("address[$lang->id]"));
                                echo form_textarea($address_data);
                          ?>
                        </div>
                    </div>

                    <div class="form-group">
                       <label class="control-label col-md-3">
                         <?php echo lang('description');?><span class="required">*</span>
                       </label>
                       <div class="col-md-4">
                          <?php
                                echo form_error("description[$lang->id]");
                                $description_data = array('name'=>"description[$lang->id]" , 'class'=>"form-control" , 'value'=> isset($data[$lang->id]->description)? $data[$lang->id]->description : set_value("description[$lang->id]"));
                                echo form_textarea($description_data);
                          ?>
                        </div>
                    </div>

                    <?php  echo form_hidden('lang_id[]', $lang->id); ?>
                </div>

    		</div>
        <?php } ?>


        </div>

	</div>

        <?php  echo isset($id) ? form_hidden('store_id', $id) : ''; ?>
        <div class="form-actions">
			<div class="row">
				<div class="col-md-offset-3 col-md-9">
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

<?php /*
<div id="upload_modal" class="modal fade" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
				<h4 class="modal-title"><?php echo lang('upload');?></h4>
			</div>
			<div class="modal-body">
				<div class="scroller" style="height:300px" data-always-visible="1" data-rail-visible1="1">
					<div class="row">
						<div class="col-md-12">
                <form action="hhg" method="post" enctype="multipart/form-data" id="upload_form">
                  <p><?php echo lang('uplod_image');?></p>

                  <input type="hidden" name="key" value="qhwastore/bakr.png" />
                  <input type="hidden" name="acl" value="public-read" />
                  <input type="hidden" name="X-Amz-Credential" value="<?php echo $access_key; ?>/<?php echo $short_date; ?>/<?php echo $region; ?>/s3/aws4_request" />
                  <input type="hidden" name="X-Amz-Algorithm" value="AWS4-HMAC-SHA256" />
                  <input type="hidden" name="X-Amz-Date" value="<?php echo $iso_date ; ?>" />
                  <input type="hidden" name="Policy" value="<?php echo base64_encode($policy); ?>" />
                  <input type="hidden" name="X-Amz-Signature" value="<?php echo $signature ?>" />
                  <input type="hidden" name="success_action_redirect" value="<?php echo $success_redirect ?>" />
                  <input type="file" name="file" class="col-md-12 form-control" />

                  <div class="modal-footer">
		                  <input id="upload_btn" type="submit" value="Upload File" />
                  </div>
              </form>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" data-dismiss="modal" class="btn default"><?php echo lang('close');?></button>
			</div>
		</div>
	</div>
</div>

<style>
    .loading_modal {
        display:     none;
        position:    fixed;
        z-index:     1000;
        top:         0;
        left:        0;
        height:      100%;
        width:       100%;
        background:  rgba( 255, 255, 255, .8 )
                     url('<?php echo base_url().'assets/ajax-loader.gif';?>')
                     50% 50%
                     no-repeat;
    }

    body.loading {
        overflow: hidden;
    }

    body.loading .loading_modal {
        display: block;
    }

    .quantity_input{
        color: #333333;
        width: 50px;
    }

</style>

<script>
$( "body" ).on( "click", "#upload_btn", function(event){
//$( document ).ready(function(event) {
  event.preventDefault();
  postData = $('#upload_form').serializeArray();
  var url = 'http://<?php echo $my_bucket ?>.s3.amazonaws.com';//$('#upload_form').attr('action');

  $.post(url, postData, function(result){
    alert(result);
  });
});
</script>
*/?>
