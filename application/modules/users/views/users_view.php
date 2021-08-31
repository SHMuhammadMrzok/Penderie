<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/template/site/css/maps/styles.css" />

<?php
    $field_name1  = 'image';
    $unique_id1   = mt_rand();
    $unique_name1 = 's'.substr(md5($field_name1),0,8);//'s5ae0c1c8';//'p'.substr(md5($unique_id), 0, 10);

    $field_name2  = 'image2';
    $unique_id2   = mt_rand();
    $unique_name2 = 's'.substr(md5($field_name2),0,8);//'s5ae0c1c8';//'p'.substr(md5($unique_id), 0, 10);

    $field_name3  = 'image3';
    $unique_id3   = mt_rand();
    $unique_name3 = 's'.substr(md5($field_name3),0,8);//'s5ae0c1c8';//'p'.substr(md5($unique_id), 0, 10);

    $upload_path       = base_url().'assets/uploads/';
    $display_style     = '';
    $display_image_div = '';
    $value             = '';
 ?>
<div class="form">
<span class="error"><?php if(isset($validation_msg)) echo $validation_msg; ?></span>
<?php $att=array('class'=> 'form-horizontal form-bordered cmxform');
      echo form_open_multipart($form_action, $att);

?>

    <div class="form-body">


        <div class="form-group">
            <label class="control-label col-md-3"><?php echo lang('email');?><span class="required">*</span></label>
           <div class="col-md-4">
              <?php
                   echo form_error("email");
                   $email_data = array('name'=>"email" , 'class'=>"form-control" , 'value'=> isset($general_data->email)? $general_data->email : set_value('email'));
                   echo form_input($email_data);
              ?>
           </div>
        </div><!--email div-->
         <div class="form-group">
            <label class="control-label col-md-3"><?php echo lang('password');?><span class="required"></span></label>
           <div class="col-md-4">
              <?php
                   echo form_error("password");
                   $password_data = array('name'=>"password" , 'class'=>"form-control " , 'value'=> set_value('password'));
                   echo form_password($password_data);
              ?>
           </div>
        </div><!--password div-->

        <div class="form-group">
            <label class="control-label col-md-3"><?php echo lang('first_name');?><span class="required">*</span></label>
           <div class="col-md-4">
              <?php
                   echo form_error("first_name");
                   $first_name_data = array('name'=>"first_name" , 'class'=>"form-control" , 'value'=> isset($general_data->first_name)? $general_data->first_name : set_value('first_name'));
                   echo form_input($first_name_data);
              ?>
           </div>
        </div><!--first_name div-->

         <div class="form-group">
            <label class="control-label col-md-3"><?php echo lang('last_name');?><span class="required">*</span></label>
           <div class="col-md-4">
              <?php
                   echo form_error("last_name");
                   $last_name_data = array('name'=>"last_name" , 'class'=>"form-control " , 'value'=> isset($general_data->last_name)? $general_data->last_name : set_value('last_name'));
                   echo form_input($last_name_data);
              ?>
           </div>
        </div><!--last_name div-->

        <div class="form-group">
            <label class="control-label col-md-3"><?php echo lang('address');?></label>
           <div class="col-md-4">
              <?php
                   $address_data = array('name'=>"address" , 'class'=>"form-control" , 'value'=> isset($general_data->address)? $general_data->address : set_value('address'));
                   echo form_input($address_data);
              ?>
           </div>
        </div><!--address div-->

        <div class="form-group">
            <label class="control-label col-md-3"><?php echo lang('phone');?><span class="required">*</span></label>
           <div class="col-md-4">
              <?php
                   echo form_error("phone");
                   $phone_data = array('name'=>"phone" , 'class'=>"form-control" , 'value'=> isset($general_data->phone)? $general_data->phone : set_value('phone'));
                   echo form_input($phone_data);
              ?>
              <span class="error"><?php echo lang('phone_ex');?></span>
           </div>

        </div><!--phone div-->

        <div class="form-group">
           <label class="control-label col-md-3"><?php echo lang('country');?>
           <span class="required">*</span><!---->
           </label>
           <div class="col-md-4">
                <?php
                    echo form_error('country_id');
                    $country_id = isset($general_data->Country_ID) ? $general_data->Country_ID : set_value('country_id') ;

                    echo form_dropdown('country_id', $user_countries, $country_id, 'class="form-control select2"');

                ?>
            </div>
        </div><!--customer_groups div-->

        <div class="form-group">
           <label class="control-label col-md-3"><?php echo lang('group');?><span class="required">*</span></label>
           <div class="col-md-4">
               <?php
                    echo form_error("group_id[]");

                    $group_id = isset($user_groups) ? $user_groups : set_value('group_id[]') ;

                    echo form_multiselect('group_id[]', $groups_options,$group_id,'class="form-control select2"');

                ?>
            </div>
        </div><!--groups div-->

        <div class="form-group">
           <label class="control-label col-md-3"><?php echo lang('customer_group_name');?>
           <span class="required">*</span><!---->
           </label>
           <div class="col-md-4">
                <?php
                    echo form_error('customer_group_id');
                    $customer_group_id = isset($general_data->customer_group_id) ? $general_data->customer_group_id : set_value('customer_group_id') ;

                    echo form_dropdown('customer_group_id', $customer_groups_options,$customer_group_id,'class="form-control select2" id="customer_group"');

                ?>
            </div>
            <?php if(isset($general_data->rep_user_id) && $general_data->rep_user_id != 0){?>
                <script>
                var post_data = {rep_user_id: <?php echo $general_data->rep_user_id;?>};
                $.post('<?php echo base_url().'users/admin_users/get_username/'?>', post_data, function(rep_username){
                    $('#rep_username').html(rep_username);
                });
                </script>
                <div style="line-height: 2.3;color: #95959E;"><?php echo lang('represintitve');?>: <span id="rep_username"></span></div>
            <?php }?>
        </div><!--customer_groups div-->

        <div class="form-group" style="display: none;" id="rep_div">
           <label class="control-label col-md-3"><?php echo lang('rep_users');?></label>
           <div class="col-md-4" id="rep">
                <?php
                    echo form_error('representative_id');
                    $rep_id = isset($general_data->representative_id) ? $general_data->representative_id : set_value('representative_id') ;

                    echo form_dropdown('representative_id', $representatives, $rep_id, 'class="form-control select2" style=""');

                ?>
           </div>
        </div><!--user_rep div-->

        <div class="form-group">
           <label class="control-label col-md-3"><?php echo lang('name_of_store');?><span class="required"></span></label>
           <div class="col-md-4">
               <?php
                  echo form_error("user_stores_id[]");
                  $user_stores_id = isset($user_stores) ? $user_stores : set_value('user_stores_id[]') ;
                  echo form_multiselect('user_stores_id[]', $stores, $user_stores_id, 'class="form-control select2"');
                ?>
            </div>
        </div><!--groups div-->


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
                      <div class="clear"></div>
                  </div>
                </div>
            </div>
       </div><!-- image div-->
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

        <div class="form-group">
           <label class="control-label col-md-3"><?php echo lang('mobile_active');?></label>
           <div class="col-md-4">
             <?php

                $mobile_active_value = false ;
                if($mode == 'edit' && !isset($validation_msg))
                {
                    if($general_data->account_sms_activated == 1)
                    {
                        $mobile_active_value = true;
                    }
                    if($general_data->account_sms_activated == 0)
                    {
                        $mobile_active_value = false;
                    }
                }

                if($mode == 'add' && !isset($validation_msg))
                {
                    $mobile_active_value = true;
                }

                $active_data = array(
                            'name'           => "mobile_active",
                            'class'          => 'make-switch',
                            'data-on-color'  => 'danger',
                            'data-off-color'  => 'default',
                            'value'          => 1,
                            'checked'        => set_checkbox("active", 1, $mobile_active_value),
                            'data-on-text'   => lang('yes'),
                            'data-off-text'  => lang('no'),
                            );
                echo form_checkbox($active_data);
             ?>
            </div>
        </div><!--mobile active-->

        <?php /*
        <div class="form-group">
           <label class="control-label col-md-3"><?php echo lang('mail_list');?></label>
           <div class="col-md-4">
             <?php
                $mail_list = false ;
                if($mode == 'edit' && !isset($validation_msg))
                {
                    if($general_data->mail_list == 1)
                    {
                        $mail_list = true;
                    }
                    if($general_data->mail_list == 0)
                    {
                        $mail_list = false;
                    }
                }

                if($mode == 'add' && !isset($validation_msg))
                {
                    $mail_list = true;
                }

                $list_data = array(
                            'name'           => "mail_list",
                            'class'          => 'make-switch',
                            'data-on-color'  => 'danger',
                            'data-off-color'  => 'default',
                            'value'          => 1,
                            'checked'        => set_checkbox("mail_list", 1, $mail_list),
                            'data-on-text'   => lang('yes'),
                            'data-off-text'  => lang('no'),
                            );
                echo form_checkbox($list_data);
             ?>
            </div>
        </div><!--Mail List-->
        */ ?>

        <div class="form-group">
            <label class="control-label col-md-3"><?php echo lang('two_way_auth');?></label>

            <?php
                $google_auth = false;
                $sms_auth    = false;
                $disable     = false;

                if($mode == 'edit' && !isset($validation_msg))
                {
                    if($general_data->login_auth == 1)
                    {
                        $sms_auth = true;
                    }
                    elseif($general_data->login_auth == 2)
                    {
                        $google_auth = true;
                    }
                    else
                    {
                        $disable = true;
                    }
                }

                if($mode == 'add' && !isset($validation_msg))
                {
                    $disable = true;
                }
            ?>


            <div class="col-md-9">
    			<div class="margin-bottom-10">
    				<label for="option1" style="width: 100px;"><?php echo lang('google_auth');?></label>

                    <?php

                        $google_auth_data = array(
                                                    'name'    => "auth",
                                                    'class'   => 'make-switch switch-radio1',
                                                    'id'      => 'option1',
                                                    'value'   => 'google_auth',
                                                    'checked' => set_radio("auth", 1, $google_auth)
                                                );

                        echo form_radio($google_auth_data);
                    ?>
    			</div>
    			<div class="margin-bottom-10">
    				<label for="option2" style="width: 100px;"><?php echo lang('sms_auth');?></label>
    				<?php
                        $sms_auth_data = array(
                                                    'name'    => "auth",
                                                    'class'   => 'make-switch switch-radio1',
                                                    'id'      => 'option2',
                                                    'value'   => 'sms_auth',
                                                    'checked' => set_radio("auth", 1, $sms_auth)
                                                );

                        echo form_radio($sms_auth_data);
                    ?>
    			</div>
    			<div class="margin-bottom-10">
    				<label for="option3" style="width: 100px;"><?php echo lang('disable');?></label>
    				<?php


                        $disable_data = array(
                                                    'name'    => "auth",
                                                    'class'   => 'make-switch switch-radio1',
                                                    'id'      => 'option3',
                                                    'value'   => 'none',
                                                    'checked' => set_radio("auth", 1, $disable)
                                                );

                        echo form_radio($disable_data);
                    ?>
    			</div>
    		</div>
        </div><!--Two Way Auth-->

        <div class="form-group wholesaler_option">
             <label class="control-label col-md-3">
               <?php echo lang('id_image');?>
             </label>
             <div class="col-md-4">
                  <?php
                   echo form_error("image3");

                   if(isset($general_data->id_image) && ($general_data->id_image!=''))
                   {
                     $display_style     = "display:none;";
                     $display_image_div = '
                                           <div id="success_'.$unique_id3.'" class="upload-success-url" style=" padding-top: 7px; display: block;">
                                               <a href="'.$upload_path.$general_data->id_image.'" id="file_'.$unique_id3.'" class="open-file image-thumbnail" target="_blank">
                                                   <img src="'.$upload_path.$general_data->id_image.'" height="50px">
                                               </a>
                                               <a href="javascript:void(0)" id="delete_'.$unique_id3.'" class="delete-anchor">'.lang('delete').'</a>
                                           </div>';
                     $value             = $general_data->id_image;
                   }
                  ?>
                 <!-- logo upload-->

                 <div class="form-div">
                     <div class="form-field-box odd" id="<?php echo $field_name3;?>_field_box">
                         <div class="form-input-box" id="<?php echo $field_name3;?>_input_box">

                             <span class="fileinput-button qq-upload-button" id="upload-button-<?php echo $unique_id3; ?>" style="<?php echo $display_style;?>">
                           <span><?php echo lang('upload')?></span>
                           <input type="file" name="<?php echo $unique_name3; ?>" class="gc-file-upload" rel="<?php echo base_url();?>uploads/upload_image/image_uploads/upload_file/<?php echo $field_name3;?>" id="<?php echo $unique_id3; ?>">
                           <input class="hidden-upload-input" type="hidden" name="<?php echo $field_name3;?>" value="<?php if(isset($general_data->logo)){echo $general_data->logo;}?>" rel="<?php echo $unique_name3; ?>">
                         </span>

                             <div id="uploader_<?php echo $unique_id3; ?>" rel="<?php echo $unique_id3; ?>" class="grocery-crud-uploader" style=""></div>

                             <?php echo $display_image_div; ?>

                             <div id="success_<?php echo $unique_id3; ?>" class="upload-success-url" style="display:none; padding-top:7px;">
                                 <a href="<?php echo base_url();?>assets/uploads/" id="file_<?php echo $unique_id3; ?>" class="open-file" target="_blank"></a>
                                 <a href="javascript:void(0)" id="delete_<?php echo $unique_id3; ?>" class="delete-anchor"><?php echo lang('delete');?></a>
                             </div>

                             <div style="clear:both"></div>

                             <div id="loading-<?php echo $unique_id3; ?>" style="display:none">
                                 <span id="upload-state-message-<?php echo $unique_id3; ?>"></span>
                                 <span class="qq-upload-spinner"></span>
                                 <span id="progress-<?php echo $unique_id3; ?>"></span>
                             </div>

                             <div style="display:none">
                                 <a href="<?php echo base_url();?>uploads/upload_image/image_uploads/upload_file/<?php echo $field_name3;?>" id="url_<?php echo $unique_id3; ?>"></a>
                             </div>

                             <div style="display:none">
                                 <a href="<?php echo base_url();?>uploads/upload_image/image_uploads/delete_file/<?php echo $field_name3;?>" id="delete_url_<?php echo $unique_id3; ?>" rel=""></a>
                             </div>
                       </div>
                       <div class="clear"></div>
                   </div>
                 </div>
             </div>
        </div><!--user id-->


        <?php /*
        <!-----------------------wholesaler data------------------------------------->
        <?php //if(isset($is_wholesaler) && $is_wholesaler){?>
            <div class="form-group wholesaler_option">
                <label class="control-label col-md-3">
                  <?php echo lang('google_maps_image');?>
                </label>
                <div class="col-md-4">
                    <div class="map_canvas"></div>
                    <div>
                       <?php
                         $geo_att  = array('name'=>'geocomplete', 'id'=>'geocomplete', 'placeholder'=>lang('type_in_address'), 'value'=>isset($general_data->geocomplete) && $general_data->geocomplete !='' ? $general_data->geocomplete: 'المملكة العربية السعودية');

                         echo form_input($geo_att);
                         echo form_error('geocomplete');
                       ?>
                       <input id="find" type="button" value="<?php echo lang('find');?>" />

                       <div class="row" style="margin: 5px;">
                           <label><?php echo lang('latitude');?></label>
                           <?php

                             $lat_att = array('name'=>'lat', 'value'=>isset($general_data->google_map_lat) && $general_data->google_map_lat !=''? $general_data->google_map_lat : '');
                             echo form_input($lat_att);
                           ?>
                       </div>

                       <div class="row" style="margin: 5px;">
                           <label><?php echo lang('longitude');?></label>

                           <?php
                             $lng_att = array('name'=>'lng', 'value'=>isset($general_data->google_map_lng) && $general_data->google_map_lng != '' ? $general_data->google_map_lng : '');
                             echo form_input($lng_att);
                           ?>
                       </div>

                       <a id="reset" href="#" style="display:none;">Reset Marker</a>
                    </div>


                </div>
           </div><!--Google map -->

           <div class="form-group wholesaler_option">
                <label class="control-label col-md-3">
                  <?php echo lang('logo');?>
                </label>
                <div class="col-md-4">
                     <?php
                      echo form_error("image3");

                      if(isset($general_data->logo) && ($general_data->logo!=''))
                      {
                        $display_style     = "display:none;";
                        $display_image_div = '
                                              <div id="success_'.$unique_id3.'" class="upload-success-url" style=" padding-top: 7px; display: block;">
                                                  <a href="'.$upload_path.$general_data->logo.'" id="file_'.$unique_id3.'" class="open-file image-thumbnail" target="_blank">
                                                      <img src="'.$upload_path.$general_data->logo.'" height="50px">
                                                  </a>
                                                  <a href="javascript:void(0)" id="delete_'.$unique_id3.'" class="delete-anchor">'.lang('delete').'</a>
                                              </div>

                                             ';
                        $value             = $general_data->logo;
                      }
                     ?>
                    <!-- logo upload-->

                    <div class="form-div">
                        <div class="form-field-box odd" id="<?php echo $field_name3;?>_field_box">
                            <div class="form-input-box" id="<?php echo $field_name3;?>_input_box">

                                <span class="fileinput-button qq-upload-button" id="upload-button-<?php echo $unique_id3; ?>" style="<?php echo $display_style;?>">
                        			<span><?php echo lang('upload')?></span>
                        			<input type="file" name="<?php echo $unique_name3; ?>" class="gc-file-upload" rel="<?php echo base_url();?>uploads/upload_image/image_uploads/upload_file/<?php echo $field_name3;?>" id="<?php echo $unique_id3; ?>">
                        			<input class="hidden-upload-input" type="hidden" name="<?php echo $field_name3;?>" value="<?php if(isset($general_data->logo)){echo $general_data->logo;}?>" rel="<?php echo $unique_name3; ?>">
                        		</span>

                                <div id="uploader_<?php echo $unique_id3; ?>" rel="<?php echo $unique_id3; ?>" class="grocery-crud-uploader" style=""></div>

                                <?php echo $display_image_div; ?>

                                <div id="success_<?php echo $unique_id3; ?>" class="upload-success-url" style="display:none; padding-top:7px;">
                                    <a href="<?php echo base_url();?>assets/uploads/" id="file_<?php echo $unique_id3; ?>" class="open-file" target="_blank"></a>
                                    <a href="javascript:void(0)" id="delete_<?php echo $unique_id3; ?>" class="delete-anchor"><?php echo lang('delete');?></a>
                                </div>

                                <div style="clear:both"></div>

                                <div id="loading-<?php echo $unique_id3; ?>" style="display:none">
                                    <span id="upload-state-message-<?php echo $unique_id3; ?>"></span>
                                    <span class="qq-upload-spinner"></span>
                                    <span id="progress-<?php echo $unique_id3; ?>"></span>
                                </div>

                                <div style="display:none">
                                    <a href="<?php echo base_url();?>uploads/upload_image/image_uploads/upload_file/<?php echo $field_name3;?>" id="url_<?php echo $unique_id3; ?>"></a>
                                </div>

                                <div style="display:none">
                                    <a href="<?php echo base_url();?>uploads/upload_image/image_uploads/delete_file/<?php echo $field_name3;?>" id="delete_url_<?php echo $unique_id3; ?>" rel=""></a>
                                </div>
                          </div>
                          <div class="clear"></div>
                      </div>
                    </div>
                </div>
           </div><!--wholesaler logo-->

            <div class="form-group wholesaler_option">
                <label class="control-label col-md-3"><?php echo lang('sms_sender');?></label>
               <div class="col-md-4">
                  <?php
                       echo form_error("sms_name");
                       $sms_name_data = array('name'=>"sms_name" , 'class'=>"form-control" , 'value'=> isset($general_data->sms_name)? $general_data->sms_name : set_value('sms_name'), 'maxlength'=>'11');
                       echo form_input($sms_name_data);
                  ?>
               </div>
            </div><!--SMS name-->

            <div class="form-group wholesaler_option">
                <label class="control-label col-md-3"><?php echo lang('sms_content');?></label>
               <div class="col-md-4">
                  <?php
                       echo form_error("sms_content");
                       $sms_content_data = array('name'=>"sms_content" , 'class'=>"form-control" , 'value'=> isset($general_data->sms_content)? $general_data->sms_content : set_value('sms_content'), 'maxlength'=>'50');
                       echo form_textarea($sms_content_data);
                  ?>
               </div>
            </div><!--SMS content-->

            <div class="form-group wholesaler_option">
                <label class="control-label col-md-3"><?php echo lang('header');?></label>
               <div class="col-md-4">
                  <?php
                       echo form_error("header");
                       $header_data = array('name'=>"header" , 'class'=>"form-control" , 'value'=> isset($general_data->header) ? $general_data->header : set_value('header'));
                       echo form_textarea($header_data);
                  ?>
               </div>
            </div><!--header content-->

            <div class="form-group wholesaler_option">
                <label class="control-label col-md-3"><?php echo lang('footer');?></label>
               <div class="col-md-4">
                  <?php
                       echo form_error("footer");
                       $footer_data = array('name'=>"footer" , 'class'=>"form-control" , 'value'=> isset($general_data->footer)? $general_data->footer : set_value('footer'));
                       echo form_textarea($footer_data);
                  ?>
               </div>
            </div><!--footer content-->
            */?>


            <div class="form-group wholesaler_option">
               <label class="control-label col-md-3"><?php echo lang('stop_wholesaler_sms');?></label>
               <div class="col-md-4">
                 <?php

                    $w_sms_value = false ;
                    if($mode == 'edit' && !isset($validation_msg))
                    {
                        if($general_data->stop_wholesaler_sms == 1)
                        {
                            $w_sms_value = true;
                        }
                        if($general_data->stop_wholesaler_sms == 0)
                        {
                            $w_sms_value = false;
                        }
                    }

                    if($mode == 'add' && !isset($validation_msg))
                    {
                        $w_sms_value = true;
                    }

                    $w_sms_data = array(
                                'name'           => "wholesaler_sms",
                                'class'          => 'make-switch',
                                'data-on-color'  => 'danger',
                                'data-off-color' => 'default',
                                'value'          => 1,
                                'checked'        => set_checkbox("wholesaler_sms", 1, $w_sms_value),
                                'data-on-text'   => lang('yes'),
                                'data-off-text'  => lang('no'),
                                );

                    echo form_checkbox($w_sms_data);
                 ?>
                </div>
            </div><!-- active -->
        <?php //}?>


         <div class="form-actions">
			<div class="row">
				<div class="col-md-offset-3 col-md-9">
                    <?php  echo isset($id) ? form_hidden('id', $id) : ''; ?>
                   <button type="submit"  class="btn green"><i class="fa fa-check"></i><?php echo lang('submit');?></button>
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

    var upload_info_<?php echo $unique_id2; ?> = {
		accepted_file_types: /(\.|\/)(gif|jpeg|jpg|png|tiff|doc|docx|txt|odt|xls|xlsx|pdf|ppt|pptx|pps|ppsx|mp3|m4a|ogg|wav|mp4|m4v|mov|wmv|flv|avi|mpg|ogv|3gp|3g2)$/i,
		accepted_file_types_ui : ".gif,.jpeg,.jpg,.png,.tiff,.doc,.docx,.txt,.odt,.xls,.xlsx,.pdf,.ppt,.pptx,.pps,.ppsx,.mp3,.m4a,.ogg,.wav,.mp4,.m4v,.mov,.wmv,.flv,.avi,.mpg,.ogv,.3gp,.3g2",
		max_file_size: 20971520,
		max_file_size_ui: "20MB"
	};

    var upload_info_<?php echo $unique_id3; ?> = {
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

<style>
.wholesaler_option{display: none;}
#rep_div{display: none;}
</style>

<script>
    $( document ).ready(function(){
        var customer_group = $('#customer_group').val();

        if(jQuery.inArray(customer_group, <?php echo $wholesaler_group_ids; ?> ) > -1 && customer_group != '')
        {
            $('.wholesaler_option').show();
            $('#rep_div').show();
        }
    });

    $( "body" ).on( "change", "#customer_group", function(){

        var customer_group = $('#customer_group').val();

        if(jQuery.inArray(customer_group, <?php echo $wholesaler_group_ids;?>) > -1)
        {
            $('.wholesaler_option').show();
            var postData = {customer_group: customer_group};
            $.post('<?php echo base_url()."users/admin_users/get_representatives/" ?>', postData, function(result){
                if(result != '')
                {
                    $('#rep_div').show();
                    $('#rep').html(result);
                }
            });
        }
        else
        {
            $('.wholesaler_option').hide();
            $('#rep_div').hide();
        }
    });
</script>

<?php /*if(isset($general_data)){?>
    <script src="http://maps.googleapis.com/maps/api/js?sensor=false&amp;libraries=places"></script>
    <script src="<?php echo base_url();?>assets/template/site/js/jquery.geocomplete.js"></script>

    <script>

      $(function(){
            $("#geocomplete").geocomplete({
              map: ".map_canvas",
              details: "form ",
              location : [<?php echo $general_data->google_map_lat != '' ? $general_data->google_map_lat : '23.885942';?>, <?php echo $general_data->google_map_lng != '' ? $general_data->google_map_lng : '45.079162';?>],

              markerOptions: {
                draggable: true
              }

            });

            <?php if($general_data->geocomplete == ''){?>
                $("#geocomplete").bind("geocode:dragged", function(event, latLng){
                  $("input[name=lat]").val(latLng.lat());
                  $("input[name=lng]").val(latLng.lng());
                  $("#reset").show();
                });
            <?php }?>

            $("#reset").click(function(){
              $("#geocomplete").geocomplete("resetMarker");
              $("#reset").hide();
              return false;
            });

            $("#find").click(function(){
              $("#geocomplete").trigger("geocode");
            }).click();
          });

    </script>
<?php }*/?>
