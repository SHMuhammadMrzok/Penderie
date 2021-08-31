<?php
    $field_name1  = 'image';
    $unique_id1   = mt_rand();
    $unique_name1 = 's'.substr(md5($field_name1),0,8);

    $field_name2  = 'image2';
    $unique_id2   = mt_rand();
    $unique_name2 = 's'.substr(md5($field_name2),0,8);

    $field_name3  = 'image3';
    $unique_id3   = mt_rand();
    $unique_name3 = 's'.substr(md5($field_name3),0,8);
    $display_style3     = '';
    $display_image_div3 = '';

    $field_name4  = 'image4';
    $unique_id4   = mt_rand();
    $unique_name4 = 's'.substr(md5($field_name4),0,8);
    $display_style4     = '';
    $display_image_div4 = '';

    $field_name5  = 'image5';
    $unique_id5   = mt_rand();
    $unique_name5 = 's'.substr(md5($field_name5),0,8);
    $display_style5     = '';
    $display_image_div5 = '';

    $upload_path       = base_url().'assets/uploads/';
    $display_style     = '';
    $display_image_div = '';
    $value             = '';
 ?>

<div class="form">
<span class="error"><?php if(isset($validation_msg)) echo $validation_msg;?></span>
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

      <li class="">
		<a href="#shipping_tab" data-toggle="tab">

		     <span class=""><?php echo lang('shippment'); ?> </span>
        </a>
	 </li>

     <li class="">
	     <a href="#stores_tab" data-toggle="tab">
	         <span class=""><?php echo lang('stores'); ?> </span>
      </a>
	 </li>

   <li class="">
     <a href="#images_tab" data-toggle="tab">
         <span class=""><?php echo lang('images'); ?> </span>
    </a>
 </li>

	</ul>

	<div class="tab-content">
      <div class="tab-pane active " id="tab_general">
	      <div class="form-body">
            <div class="form-group">
                <label class="control-label col-md-3"><?php echo lang('default_lang');?></label>
               <div class="col-md-4">
                  <?php
                       $default_lang = isset($general_data->default_lang) ? $general_data->default_lang : set_value('default_lang') ;
                       echo form_dropdown('default_lang', $lang_options,$default_lang,'class="form-control select2"');

                    ?>
                </div>
            </div><!-- default_lang div-->

            <div class="form-group">
                <label class="control-label col-md-3"><?php echo lang('default_country');?></label>
                <div class="col-md-4">
                  <?php //print_r($countries);
                       $default_country = isset($general_data->default_country) ? $general_data->default_country : set_value('default_country') ;

                        echo form_dropdown('default_country', $country_options , $default_country,'class="form-control select2"');
                   ?>
                </div>

            </div><!-- default_country div-->

            <div class="form-group">
                <label class="control-label col-md-3"><?php echo lang('wholesaler_customer_group');?></label>
                <div class="col-md-4">
                  <?php
                       $default_wholesaler_customer_group_id = isset($general_data->wholesaler_customer_group_id) ? $general_data->wholesaler_customer_group_id : set_value('wholesaler_customer_group_id') ;
                       echo form_multiselect('wholesaler_customer_group_id[]', $groups_options , $default_wholesaler_customer_group_id, 'class="form-control select2"');
                   ?>
                </div>
            </div><!-- default wholesaler customer group id div-->

            <div class="form-group">
                <label class="control-label col-md-3"><?php echo lang('drivers_group');?></label>
                <div class="col-md-4">
                  <?php
                       $drivers_group_id = isset($general_data->drivers_group_id) ? $general_data->drivers_group_id : set_value('drivers_group_id') ;
                       echo form_dropdown('drivers_group_id', $users_groups , $drivers_group_id, 'class="form-control select2"');
                   ?>
                </div>
            </div><!-- default drivers group id div-->

            <div class="form-group">
                <label class="control-label col-md-3"><?php echo lang('admin_notification_lang');?></label>
                <div class="col-md-4">
                  <?php
                       $admin_notification_lang_id = isset($general_data->admin_notification_lang_id) ? $general_data->admin_notification_lang_id : set_value('admin_notification_lang_id') ;
                       echo form_dropdown('admin_notification_lang_id', $lang_ids, $admin_notification_lang_id, 'class="form-control select2"');
                   ?>
                </div>
            </div><!-- default wholesaler customer group id div-->

            <div class="form-group">
                <label class="control-label col-md-3"><?php echo lang('new_user_customer_group');?></label>
                <div class="col-md-4">
                  <?php
                       $default_new_user_customer_group_id = isset($general_data->new_user_customer_group_id) ? $general_data->new_user_customer_group_id : set_value('new_user_customer_group_id') ;
                       echo form_dropdown('new_user_customer_group_id', $groups_options, $default_new_user_customer_group_id, 'class="form-control select2"');
                   ?>
                </div>
            </div><!-- default new user customer group id div-->

            <div class="form-group">
                <label class="control-label col-md-3"><?php echo lang('rep_customer_group');?></label>
                <div class="col-md-4">
                  <?php
                       $default_new_user_customer_group_id = isset($general_data->rep_group_id) ? $general_data->rep_group_id : set_value('rep_group_id') ;
                       echo form_dropdown('rep_group_id', $groups_options, $default_new_user_customer_group_id, 'class="form-control select2"');
                   ?>
                </div>
            </div><!-- default new user customer group id div-->

            <div class="form-group">
                <label class="control-label col-md-3"><?php echo lang('maintenanace_cat');?></label>
                <div class="col-md-4">
                  <?php
                       $maintenance_cat_id = isset($general_data->maintenance_cat_id) ? $general_data->maintenance_cat_id : set_value('maintenance_cat_id') ;
                       echo form_dropdown('maintenance_cat_id', $categories, $maintenance_cat_id, 'class="form-control select2"');
                   ?>
                </div>
            </div><!-- maintenanace cat id div-->

            <div class="form-group">
                <label class="control-label col-md-3"><?php echo lang('return_days_count');?></label>
               <div class="col-md-4">
                  <?php
                       $return_days_data = array('name'=>"return_days" , 'class'=>"form-control" , 'value'=> isset($general_data->return_days)? $general_data->return_days: set_value('return_days'));
                       echo form_input($return_days_data);
                       echo form_error('return_days');
                  ?>
               </div>
            </div><!--return days div-->

            <div class="form-group">
                <label class="control-label col-md-3"><?php echo lang('sender_email');?></label>
               <div class="col-md-4">
                  <?php
                       $sender_email_data = array('name'=>"sender_email" , 'class'=>"form-control" , 'value'=> isset($general_data->sender_email)? $general_data->sender_email: set_value('sender_email'));
                       echo form_input($sender_email_data);
                       echo form_error('sender_email');
                  ?>
               </div>
            </div><!--sender email div-->

            <div class="form-group">
                <label class="control-label col-md-3">
                  <?php echo lang('incorrect_login_email');?>
                </label>

               <div class="col-md-4">
                 <?php
                       $login_email_data = array('name'=>"incorrect_login_email" , 'class'=>"form-control" , 'value'=> isset($general_data->incorrect_login_email)? $general_data->incorrect_login_email: set_value('incorrect_login_email'));
                       echo form_input($login_email_data);
                       echo form_error('incorrect_login_email');
                 ?>
                </div>
            </div><!-- incorrect_login_email div-->

            <div class="form-group">
                <label class="control-label col-md-3">
                  <?php echo lang('site_email');?>
                  <br />
                  ( <?php echo lang('email_note');?> )
                </label>

               <div class="col-md-4">
                 <?php
                     $email_data = array('name'=>"email" , 'class'=>"form-control" , 'value'=> isset($general_data->email)? implode("\n",json_decode($general_data->email)) : set_value("email"));
                     echo form_textarea($email_data);
                 ?>
                </div>
            </div><!-- email div-->


           <div class="form-group">
                <label class="control-label col-md-3">
                  <?php echo lang('site_telephones');?>
                  <br />
                  ( <?php echo lang('telephone_note');?> )
                </label>

               <div class="col-md-4">
                 <?php
                     $telephone_data = array('name'=>"telephone" , 'class'=>"form-control" , 'value'=> isset($general_data->telephone)? implode("\n",json_decode($general_data->telephone)) : set_value("telephone"));
                     echo form_textarea($telephone_data);
                 ?>
                </div>
            </div><!--telephone div-->

           <div class="form-group">
                <label class="control-label col-md-3">
                  <?php echo lang('site_mobile');?><br>
                  (<?php echo lang('mobile_note');?>)
                </label>

               <div class="col-md-4">
                 <?php
                         $mobile_data = array('name'=>"mobile" , 'class'=>"form-control" , 'value'=> isset($general_data->mobile)? implode("\n",json_decode($general_data->mobile)) : set_value("mobile"));
                         echo form_textarea($mobile_data);
                 ?>
                </div>
           </div><!--mobile div-->

           <div class="form-group">
             <label class="control-label col-md-3"><?php echo lang('whats_app_number');?></label>
            <div class="col-md-4">
               <?php
                    $whats_app_number_data = array('name'=>"whats_app_number" ,
                    'class'=>"form-control" ,
                    'value'=> isset($general_data->whats_app_number)? $general_data->whats_app_number : set_value('whats_app_number')
                  );
                    echo form_input($whats_app_number_data);
               ?>
            </div>
         </div><!--fax div-->

          <div class="form-group">
            <label class="control-label col-md-3"><?php echo lang('site_fax');?></label>
           <div class="col-md-4">
              <?php
                   $fax_data = array('name'=>"fax" , 'class'=>"form-control" , 'value'=> isset($general_data->fax)? $general_data->fax : set_value('fax'));
                   echo form_input($fax_data);
              ?>
           </div>
        </div><!--fax div-->

        <div class="form-group">
            <label class="control-label col-md-3"><?php echo lang('facebook');?></label>
           <div class="col-md-4">
              <?php
                   $facebook_data = array('name'=>"facebook" , 'class'=>"form-control" , 'value'=> isset($general_data->facebook)? $general_data->facebook: set_value('facebook'));
                   echo form_input($facebook_data);
              ?>
           </div>
        </div><!--facebook div-->

        <div class="form-group">
            <label class="control-label col-md-3"><?php echo lang('twitter');?></label>
           <div class="col-md-4">
              <?php
                   $twitter_data = array('name'=>"twitter" , 'class'=>"form-control" , 'value'=> isset($general_data->twitter)? $general_data->twitter: set_value('twitter'));
                   echo form_input($twitter_data);
              ?>
           </div>
       </div><!--twitter div-->
       <div class="form-group">
            <label class="control-label col-md-3"><?php echo lang('youtube');?></label>
           <div class="col-md-4">
              <?php
                   $youtube_data = array('name'=>"youtube" , 'class'=>"form-control" , 'value'=> isset($general_data->youtube)? $general_data->youtube: set_value('youtube'));
                   echo form_input($youtube_data);
              ?>
           </div>
       </div><!--youtube div-->
       <div class="form-group">
            <label class="control-label col-md-3"><?php echo lang('instagram');?></label>
           <div class="col-md-4">
              <?php
                   $instagram_data = array('name'=>"instagram" , 'class'=>"form-control" , 'value'=> isset($general_data->instagram)? $general_data->instagram: set_value('instagram'));
                   echo form_input($instagram_data);
              ?>
           </div>
       </div><!--instagram div-->

       <div class="form-group">
            <label class="control-label col-md-3"><?php echo lang('linkedin');?></label>
           <div class="col-md-4">
              <?php
                   $linkedin_data = array('name'=>"linkedin" , 'class'=>"form-control" ,
                    'value'=> isset($general_data->linkedin)? $general_data->linkedin: set_value('linkedin'));
                   echo form_input($linkedin_data);
              ?>
           </div>
       </div><!--linkedin div-->

       <div class="form-group">
            <label class="control-label col-md-3"><?php echo lang('site_map');?></label>
           <div class="col-md-4">
              <?php
                   $google_map_key_data = array(
                     'name'=>"google_map_key" ,
                     'class'=>"form-control" ,
                     'value'=> isset($general_data->google_map_key)? $general_data->google_map_key: set_value('google_map_key')
                   );
                   echo form_textarea($google_map_key_data);
              ?>
           </div>
       </div><!--google map code div-->

       <div class="form-group">
            <label class="control-label col-md-3"><?php echo lang('android_app_link');?></label>
           <div class="col-md-4">
              <?php
                   $android_app_link_data = array(
                                        'name'=>"android_app_link" ,
                                        'class'=>"form-control" ,
                                        'value'=> isset($general_data->android_app_link)? $general_data->android_app_link: set_value('android_app_link'));
                   echo form_input($android_app_link_data);
              ?>
           </div>
       </div><!--android_app_link div-->

       <div class="form-group">
            <label class="control-label col-md-3"><?php echo lang('ios_app_link');?></label>
           <div class="col-md-4">
              <?php
                   $ios_app_link_data = array(
                                                'name'=>"ios_app_link" ,
                                                'class'=>"form-control" ,
                                                'value'=> isset($general_data->ios_app_link)? $general_data->ios_app_link: set_value('ios_app_link'));
                   echo form_input($ios_app_link_data);
              ?>
           </div>
       </div><!--google_map_key div-->

       <div class="form-group">
            <label class="control-label col-md-3"><?php echo lang('seller_video');?></label>
           <div class="col-md-4">
              <?php
                   $seller_video_data = array(
                                        'name'=>"seller_video" ,
                                        'class'=>"form-control" ,
                                        'value'=> isset($general_data->seller_video)? $general_data->seller_video: set_value('seller_video'));
                   echo form_input($seller_video_data);
              ?>
           </div>
       </div><!--seller_video div-->


       <div class="form-group">
            <label class="control-label col-md-3"><?php echo lang('min_product_stock');?></label>
           <div class="col-md-4">
              <?php
                   $min_product_stock_data = array('name'=>"min_product_stock" , 'class'=>"form-control" , 'value'=> isset($general_data->min_product_stock)? $general_data->min_product_stock: set_value('min_product_stock'));
                   echo form_input($min_product_stock_data);
              ?>
           </div>
       </div><!--min_product_stock div-->

       <div class="form-group">
            <label class="control-label col-md-3"><?php echo lang('rest_product_qty');?></label>
           <div class="col-md-4">
              <?php
                   $rest_product_qty_data = array(
                     'name'   => "rest_product_qty" ,
                     'class'  => "form-control" ,
                     'value'  => isset($general_data->rest_product_qty)? $general_data->rest_product_qty: set_value('rest_product_qty')
                   );

                   echo form_input($rest_product_qty_data);
              ?>
           </div>
       </div><!--rest_product_qty div-->

       <div class="form-group">
            <label class="control-label col-md-3"><?php echo lang('min_order_hours');?></label>
           <div class="col-md-4">
              <?php
                   $min_order_hours_data = array('name'=>"min_order_hours" , 'class'=>"form-control" , 'value'=> isset($general_data->min_order_hours)? ($general_data->min_order_hours) : set_value('min_order_hours'));
                   echo form_input($min_order_hours_data);
              ?>
           </div>
       </div><!--min_order_hours div-->

       <div class="form-group">
            <label class="control-label col-md-3"><?php echo lang('categories_vertical_limit');?></label>
           <div class="col-md-4">
              <?php
                   $categories_vertical_limit_data = array(
                                                           'type'   => 'number',
                                                           'name'   => "categories_vertical_limit",
                                                           'class'  => "form-control" ,
                                                           'value'  => isset($general_data->categories_vertical_limit)? ($general_data->categories_vertical_limit) : set_value('categories_vertical_limit')
                                                           );
                   echo form_input($categories_vertical_limit_data);
              ?>
           </div>
       </div><!--categories_vertical_limit div-->

       <div class="form-group">
            <label class="control-label col-md-3"><?php echo lang('menu_horizontal_limit');?></label>
           <div class="col-md-4">
              <?php
                   $menu_horizontal_limit_data = array(
                                                'type'  => 'number',
                                                'name'  => "menu_horizontal_limit",
                                                'class' => "form-control",
                                                'value' => isset($general_data->menu_horizontal_limit)? ($general_data->menu_horizontal_limit) : set_value('menu_horizontal_limit')
                                                );
                   echo form_input($menu_horizontal_limit_data);
              ?>
           </div>
       </div><!--menu_horizontal_limit div-->

       <div class="form-group">
            <label class="control-label col-md-3"><?php echo lang('products_limit');?></label>
           <div class="col-md-4">
              <?php
                   $products_limit_data = array(
                                                'type'  => 'number',
                                                'name'  => "products_limit" ,
                                                'class' => "form-control" ,
                                                'value' => isset($general_data->products_limit)? ($general_data->products_limit) : set_value('products_limit')
                                                );
                   echo form_input($products_limit_data);
              ?>
           </div>
       </div><!--products_limit div-->

       <div class="form-group">
            <label class="control-label col-md-3"><?php echo lang('vat_type');?></label>
           <div class="col-md-4">
              <select class="form-control" name="vat_type">
                <option value="1" <?php echo $general_data->vat_type==1?'selected':'';?>><?php echo lang('inclusive_vat');?></option>
                <option value="2" <?php echo $general_data->vat_type==2?'selected':'';?>><?php echo lang('exclusive_vat');?></option>
              </select>
           </div>
       </div><!--vat_percent div-->

       <div class="form-group">
            <label class="control-label col-md-3"><?php echo lang('vat_percent');?></label>
           <div class="col-md-4">
              <?php
                   $vat_percent_data = array(
                                                'type'  => 'text',
                                                'name'  => "vat_percent" ,
                                                'class' => "form-control" ,
                                                'value' => isset($general_data->vat_percent)? ($general_data->vat_percent) : set_value('vat_percent')
                                                );
                   echo form_input($vat_percent_data);
              ?>
           </div>
       </div><!--vat_percent div-->

       <?php /*<div class="form-group">
            <label class="control-label col-md-3"><?php echo lang('max_blocks');?></label>
           <div class="col-md-4">
              <?php
                   $max_blocks_data = array(
                                                'type'  => 'text',
                                                'name'  => "max_blocks" ,
                                                'class' => "form-control" ,
                                                'value' => isset($general_data->max_blocks)? ($general_data->max_blocks) : set_value('max_blocks')
                                                );
                   echo form_input($max_blocks_data);
              ?>
           </div>
       </div><!--max_blocks div-->
       */?>


       <?php /*
       <div class="form-group">
           <label class="control-label col-md-3"><?php echo lang('auto_active_product');?></label>
           <div class="col-md-4">
             <?php

                $auto_active_product_value = false ;
                if($general_data->auto_active_product == 1)
                {
                    $auto_active_product_value = true;
                }
                if($general_data->auto_active_product == 0)
                {
                    $auto_active_product_value = false;
                }


                $auto_active_product_data = array(
                            'name'           => "auto_active_product",
                            'class'          => 'make-switch',
                            'data-on-color'  => 'danger',
                            'data-off-color'  => 'default',
                            'value'          => 1,
                            'checked'        => set_checkbox("auto_active_product", 1, $auto_active_product_value),
                            'data-on-text'   => lang('yes'),
                            'data-off-text'  => lang('no'),
                            );
                echo form_checkbox($auto_active_product_data);
             ?>
            </div>
        </div><!--auto activate products-->
        */?>

        <div class="form-group">
             <label class="control-label col-md-3"><?php echo lang('tax_number');?></label>
            <div class="col-md-4">
               <?php
                    $tax_number_data = array(
                                                 'type'  => 'text',
                                                 'name'  => "tax_number" ,
                                                 'class' => "form-control" ,
                                                 'value' => isset($general_data->tax_number)? ($general_data->tax_number) : set_value('tax_number')
                                                 );
                    echo form_input($tax_number_data);
               ?>
            </div>
        </div><!--max_blocks div-->

        <div class="form-group">
             <label class="control-label col-md-3"><?php echo lang('toaster_seconds');?></label>
            <div class="col-md-4">
               <?php
                    $toaster_seconds_data = array(
                                                 'type'  => 'text',
                                                 'name'  => "toaster_seconds" ,
                                                 'class' => "form-control" ,
                                                 'value' => isset($general_data->toaster_seconds)? ($general_data->toaster_seconds) : set_value('toaster_seconds')
                                                 );
                    echo form_input($toaster_seconds_data);
               ?>
            </div>
        </div><!--toaster_seconds div-->

        <div class="form-group">
            <label class="control-label col-md-3"><?php echo lang('allow_user_auth');?></label>
            <div class="col-md-4">
              <?php

                 $allow_user_auth_value = false ;
                 if($general_data->allow_user_auth == 1)
                 {
                     $allow_user_auth_value = true;
                 }
                 if($general_data->allow_user_auth == 0)
                 {
                     $allow_user_auth_value = false;
                 }

                 $allow_user_auth_data = array(
                             'name'           => "allow_user_auth",
                             'class'          => 'make-switch',
                             'data-on-color'  => 'danger',
                             'data-off-color'  => 'default',
                             'value'          => 1,
                             'checked'        => set_checkbox("allow_user_auth", 1, $allow_user_auth_value),
                             'data-on-text'   => lang('yes'),
                             'data-off-text'  => lang('no'),
                             );
                 echo form_checkbox($allow_user_auth_data);
              ?>
             </div>
         </div><!--allow user auth-->
         
        <div class="form-group">
             <label class="control-label col-md-3"><?php echo lang('map_country_lat');?></label>
            <div class="col-md-4">
               <?php
                    $map_country_lat = array(
                                                 'type'  => 'text',
                                                 'name'  => "map_country_lat" ,
                                                 'class' => "form-control" ,
                                                 'value' => isset($general_data->map_country_lat)? ($general_data->map_country_lat) : set_value('map_country_lat')
                                                 );
                    echo form_input($map_country_lat);
               ?>
            </div>
        </div><!--country lat div-->
        
        <div class="form-group">
             <label class="control-label col-md-3"><?php echo lang('map_country_long');?></label>
            <div class="col-md-4">
               <?php
                    $map_country_lng = array(
                                                 'type'  => 'text',
                                                 'name'  => "map_country_lng" ,
                                                 'class' => "form-control" ,
                                                 'value' => isset($general_data->map_country_lng)? ($general_data->map_country_lng) : set_value('map_country_lng')
                                                 );
                    echo form_input($map_country_lng);
               ?>
            </div>
        </div><!--country lng div-->

        </div><!--form_body-->
      </div><!--general tab-->

       <?php foreach($data_languages as $key=> $lang){ ?>
         <div class="tab-pane" id="tab_lang_<?php echo $lang->id; ?>">
           <div class="form-body">
                    <div class="form-group">
                        <label class="control-label col-md-3">
                          <?php echo lang('site_name');?>
                          <span class="required">*</span>
                        </label>
                        <div class="col-md-4">
                            <?php
                                    echo form_error("site_name[$lang->id]");
                                    $site_name_data = array('name'=>"site_name[$lang->id]" , 'class'=>"form-control" , 'value'=> isset($data[$lang->id]->site_name)? $data[$lang->id]->site_name : set_value("site_name[$lang->id]"));
                                    echo form_input($site_name_data);
                            ?>
                       </div>
                    </div><!-- site_name-->

                    <div class="form-group">
                        <label class="control-label col-md-3"><?php echo lang('site_address');?></label><label class="control-label"><?php echo lang('address_note');?></label>
                        <div class="col-md-4">
                            <?php
                                    $address_data = array('name'=>"address[$lang->id]" , 'class'=>"form-control" , 'value'=> isset($data[$lang->id]->address)? implode("\n",json_decode( $data[$lang->id]->address))  : set_value("address[$lang->id]"));
                                    echo form_textarea($address_data);
                            ?>
                        </div>
                    </div><!-- address-->

                    <div class="form-group">
                       <label class="control-label col-md-3"><?php echo lang('site_keywords');?></label>
                        <div class="col-md-4">
                             <?php
                                    $keywords_data = array('name'=>"keywords[$lang->id]" , 'class'=>"form-control" , 'value'=> isset($data[$lang->id]->keywords)? $data[$lang->id]->keywords : set_value("keywords[$lang->id]"));
                                    echo form_textarea($keywords_data);
                            ?>
                        </div>
                   </div><!-- keywords-->

                   <div class="form-group">
                       <label class="control-label col-md-3"><?php echo lang('site_description');?></label>
                        <div class="col-md-4">
                            <?php
                                   $description_data = array('name'=>"description[$lang->id]" , 'class'=>"form-control" , 'value'=> isset($data[$lang->id]->description)? $data[$lang->id]->description : set_value("description[$lang->id]"));
                                   echo form_textarea($description_data);
                            ?>
                        </div>
                   </div><!-- description -->

                    <?php  echo form_hidden('lang_id[]', $lang->id); ?>
                </div>

    		</div>
        <?php } ?>

        <div class="tab-pane" id="shipping_tab">
            <div class="form-body">
<?php /*
               <div class="form-group">
                   <label class="control-label col-md-3"><?php echo lang('home_delivery');?></label>
                   <div class="col-md-4">
                     <?php

                        $home_delivery_value = false ;
                        if($general_data->home_delivery == 1)
                        {
                            $home_delivery_value = true;
                        }
                        if($general_data->home_delivery == 0)
                        {
                            $home_delivery_value = false;
                        }


                        $home_delivery_data = array(
                                    'name'           => "home_delivery",
                                    'class'          => 'make-switch',
                                    'data-on-color'  => 'danger',
                                    'data-off-color'  => 'default',
                                    'value'          => 1,
                                    'checked'        => set_checkbox("home_delivery", 1, $home_delivery_value),
                                    'data-on-text'   => lang('yes'),
                                    'data-off-text'  => lang('no'),
                                    );
                        echo form_checkbox($home_delivery_data);
                     ?>
                    </div>
                </div><!--home_delivery-->
*/?>
                <div class="form-group">
                   <label class="control-label col-md-3"><?php echo lang('shipping');?></label>
                   <div class="col-md-4">
                     <?php

                        $shipping_value = false ;
                        if($general_data->shipping == 1)
                        {
                            $shipping_value = true;
                        }
                        if($general_data->shipping == 0)
                        {
                            $shipping_value = false;
                        }


                        $shipping_data = array(
                                    'name'           => "shipping",
                                    'class'          => 'make-switch',
                                    'data-on-color'  => 'danger',
                                    'data-off-color'  => 'default',
                                    'value'          => 1,
                                    'checked'        => set_checkbox("shipping", 1, $shipping_value),
                                    'data-on-text'   => lang('yes'),
                                    'data-off-text'  => lang('no'),
                                    );
                        echo form_checkbox($shipping_data);
                     ?>
                    </div>
                </div><!--shipping-->

<?php /*
                <div class="form-group">
                   <label class="control-label col-md-3"><?php echo lang('recieve_from_branch');?></label>
                   <div class="col-md-4">
                     <?php

                        $recieve_from_branch_value = false ;
                        if($general_data->recieve_from_branch == 1)
                        {
                            $recieve_from_branch_value = true;
                        }
                        if($general_data->recieve_from_branch == 0)
                        {
                            $recieve_from_branch_value = false;
                        }


                        $recieve_from_branch_data = array(
                                    'name'           => "recieve_from_branch",
                                    'class'          => 'make-switch',
                                    'data-on-color'  => 'danger',
                                    'data-off-color'  => 'default',
                                    'value'          => 1,
                                    'checked'        => set_checkbox("recieve_from_branch", 1, $recieve_from_branch_value),
                                    'data-on-text'   => lang('yes'),
                                    'data-off-text'  => lang('no'),
                                    );
                        echo form_checkbox($recieve_from_branch_data);
                     ?>
                    </div>
                </div><!--recieve_from_branch-->
*/?>
                <div class="form-group">
                   <label class="control-label col-md-3"><?php echo lang('address_list');?></label>
                   <div class="col-md-4">
                     <?php

                        $address_value = false ;
                        if($general_data->user_address == 1)
                        {
                            $address_value = true;
                        }
                        if($general_data->user_address == 0)
                        {
                            $address_value = false;
                        }


                        $address_data = array(
                                    'name'           => "user_address",
                                    'class'          => 'make-switch',
                                    'data-on-color'  => 'danger',
                                    'data-off-color'  => 'default',
                                    'value'          => 1,
                                    'checked'        => set_checkbox("address", 1, $address_value),
                                    'data-on-text'   => lang('yes'),
                                    'data-off-text'  => lang('no'),
                                    );
                        echo form_checkbox($address_data);
                     ?>
                    </div>
                </div><!--address_list-->
            <div class="form-group">
                <label class="control-label col-md-3"><?php echo lang('default_shipping_company');?></label>
                <div class="col-md-4">
                  <?php
                       $default_shipping_company = isset($general_data->default_shipping_company_id) ? $general_data->default_shipping_company_id : set_value('shipping_Company') ;
                       echo form_dropdown('shipping_Company', $compinies, $default_shipping_company, 'class="form-control select2"');
                   ?>
                </div>
            </div>
                <div class="form-group">
                    <label class="control-label col-md-3"><?php echo lang('locator_max_distance');?></label>
                   <div class="col-md-4">
                      <?php
                           $locator_max_distance_data = array('name'=>"locator_max_distance" , 'class'=>"form-control" , 'value'=> isset($general_data->locator_max_distance)? ($general_data->locator_max_distance) : set_value('locator_max_distance'));
                           echo form_input($locator_max_distance_data);
                      ?>
                   </div>
               </div><!--locator max distance-->

                <div class="form-group">
                    <label class="control-label col-md-3"><?php echo lang('min_order_for_delivery');?></label>
                   <div class="col-md-4">
                      <?php
                           $min_order_for_delivery_data = array('name'=>"min_order_for_delivery" , 'class'=>"form-control" , 'value'=> isset($general_data->min_order_for_delivery)? $general_data->min_order_for_delivery: set_value('min_order_for_delivery'));
                           echo form_input($min_order_for_delivery_data);
                           echo form_error('min_order_for_delivery');
                      ?>
                   </div>
                </div><!--min_order_for_delivery div-->

                <div class="form-group">
                    <label class="control-label col-md-3"><?php echo ('locator_type');?></label>
                   <div class="col-md-4">
                      <select name="locator_type_id" class="form-control select2" >
                        <option value="1" <?php echo ($general_data->locator_type=='approximate')?'selected':''; ?>><?php echo ('approximate');?></option>
                        <option value="2" <?php echo ($general_data->locator_type == 'google_api')? 'selected' : ''; ?>><?php echo ('google_api');?></option>
                      </select>
                   </div>
               </div><!--googleapi_key div-->

                <div class="form-group">
                    <label class="control-label col-md-3"><?php echo lang('googleapi_key');?></label>
                   <div class="col-md-4">
                      <?php
                           $googleapi_key_data = array('name'=>"googleapi_key" , 'class'=>"form-control" , 'value'=> isset($general_data->googleapi_key)? $general_data->googleapi_key: set_value('googleapi_key'));
                           echo form_input($googleapi_key_data);
                      ?>
                   </div>
               </div><!--googleapi_key div-->


            </div><!--form_body-->
        </div>

        <div class="tab-pane" id="stores_tab">
            <div class="form-body">
                <div class="form-group">
                    <label class="control-label col-md-3"><?php echo lang('default_store');?></label>
                   <div class="col-md-4">
                      <?php
                           $default_store_id = isset($general_data->default_store_id) ? $general_data->default_store_id : set_value('default_store_id') ;
                           echo form_dropdown('default_store_id', $stores, $default_store_id, 'class="form-control select2"');
                       ?>
                   </div>
               </div><!--default_store_id div-->

                <div class="form-group">
                    <label class="control-label col-md-3"><?php echo lang('first_store');?></label>
                   <div class="col-md-4">
                      <?php
                           $first_store_id = isset($general_data->first_store_id) ? $general_data->first_store_id : set_value('first_store_id') ;
                           echo form_dropdown('first_store_id', $stores, $first_store_id, 'class="form-control select2"');
                       ?>
                   </div>
               </div><!--first_store_id div-->

               <div class="form-group">
                    <label class="control-label col-md-3"><?php echo lang('second_store');?></label>
                   <div class="col-md-4">
                      <?php
                           $second_store_id = isset($general_data->second_store_id) ? $general_data->second_store_id : set_value('second_store_id') ;
                           echo form_dropdown('second_store_id', $stores, $second_store_id, 'class="form-control select2"');
                       ?>
                   </div>
               </div><!--second_store_id div-->

               <div class="form-group">
                    <label class="control-label col-md-3"><?php echo lang('third_store');?></label>
                   <div class="col-md-4">
                      <?php
                           $third_store_id = isset($general_data->third_store_id) ? $general_data->third_store_id : set_value('third_store_id') ;
                           echo form_dropdown('third_store_id', $stores, $third_store_id, 'class="form-control select2"');
                       ?>
                   </div>
               </div><!--third_store_id div-->

                <div class="form-group">
                    <label class="control-label col-md-3"><?php echo lang('fourth_store');?></label>
                   <div class="col-md-4">
                      <?php
                           $fourth_store_id = isset($general_data->fourth_store_id) ? $general_data->fourth_store_id : set_value('fourth_store_id') ;
                           echo form_dropdown('fourth_store_id', $stores, $fourth_store_id, 'class="form-control select2"');
                       ?>
                   </div>
               </div><!--fourth_store_id div-->

               <div class="form-group">
                    <label class="control-label col-md-3"><?php echo lang('fifth_store');?></label>
                   <div class="col-md-4">
                      <?php
                           $fifth_store_id = isset($general_data->fifth_store_id) ? $general_data->fifth_store_id : set_value('fifth_store_id') ;
                           echo form_dropdown('fifth_store_id', $stores, $fifth_store_id, 'class="form-control select2"');
                       ?>
                   </div>
               </div><!--fifth_store_id div-->



            </div><!--form_body-->
        </div>

        <div class="tab-pane  " id="images_tab">
  	      <div class="form-body">

         <div class="form-group">
              <label class="control-label col-md-3">
                <?php echo lang('logo');?>
              </label>
              <div class="col-md-4">
                   <?php
                    if(isset($general_data->logo) && ($general_data->logo!=''))
                    {
                      $display_style     = "display:none;";
                      $display_image_div = '
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
                  <!-- image upload-->

                  <div class="form-div">
                      <div class="form-field-box odd" id="<?php echo $field_name1;?>_field_box">
                          <div class="form-input-box" id="<?php echo $field_name1;?>_input_box">

                              <span class="fileinput-button qq-upload-button" id="upload-button-<?php echo $unique_id1; ?>" style="<?php echo $display_style;?>">
                      			<span><?php echo lang('upload')?></span>
                      			<input type="file" name="<?php echo $unique_name1; ?>" class="gc-file-upload" rel="<?php echo base_url();?>uploads/upload_image/image_uploads/upload_file/<?php echo $field_name1;?>" id="<?php echo $unique_id1; ?>">
                      			<input class="hidden-upload-input" type="hidden" name="<?php echo $field_name1;?>" value="<?php if(isset($general_data->logo)){echo $general_data->logo;}?>" rel="<?php echo $unique_name1; ?>">
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
         </div><!-- LOGO DIV-->


         <div class="form-group">
              <label class="control-label col-md-3">
                <?php echo lang('fav_ico');?>
              </label>
              <div class="col-md-4">
                   <?php
                    if(isset($general_data->fav_ico) && ($general_data->fav_ico!=''))
                    {
                      $display_style     = "display:none;";
                      $display_image_div = '
                                            <div id="success_'.$unique_id2.'" class="upload-success-url" style=" padding-top: 7px; display: block;">
                                                <a href="'.$images_path.$general_data->fav_ico.'" id="file_'.$unique_id2.'" class="open-file image-thumbnail" target="_blank">
                                                    <img src="'.$images_path.$general_data->fav_ico.'" height="50px">
                                                </a>
                                                <a href="javascript:void(0)" id="delete_'.$unique_id2.'" class="delete-anchor">delete</a>
                                            </div>

                                           ';
                      $value             = $general_data->fav_ico;
                    }
                   ?>
                  <!-- image upload-->

                  <div class="form-div">
                      <div class="form-field-box odd" id="<?php echo $field_name2;?>_field_box">
                          <div class="form-input-box" id="<?php echo $field_name2;?>_input_box">

                              <span class="fileinput-button qq-upload-button" id="upload-button-<?php echo $unique_id2; ?>" style="<?php echo $display_style;?>">
                      			<span><?php echo lang('upload')?></span>
                      			<input type="file" name="<?php echo $unique_name2; ?>" class="gc-file-upload" rel="<?php echo base_url();?>uploads/upload_image/image_uploads/upload_file/<?php echo $field_name2;?>" id="<?php echo $unique_id2; ?>">
                      			<input class="hidden-upload-input" type="hidden" name="<?php echo $field_name2;?>" value="<?php if(isset($general_data->fav_ico)){echo $general_data->fav_ico;}?>" rel="<?php echo $unique_name2; ?>">
                      		</span>

                              <div id="uploader_<?php echo $unique_id2; ?>" rel="<?php echo $unique_id2; ?>" class="grocery-crud-uploader" style=""></div>

                              <?php echo $display_image_div; ?>

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
                        <?php echo form_error("image2");?>
                        <div class="clear"></div>
                    </div>
                  </div>
              </div>
         </div><!--FAV icon DIV-->

         <div class="form-group">
              <label class="control-label col-md-3">
                <?php echo lang('login_background');?>
              </label>
              <div class="col-md-4">
                   <?php
                    if(isset($general_data->login_background) && ($general_data->login_background!=''))
                    {
                      $display_style3     = "display:none;";
                      $display_image_div3 = '
                                            <div id="success_'.$unique_id3.'" class="upload-success-url" style=" padding-top: 7px; display: block;">
                                                <a href="'.$images_path.$general_data->login_background.'" id="file_'.$unique_id3.'" class="open-file image-thumbnail" target="_blank">
                                                    <img src="'.$images_path.$general_data->login_background.'" height="50px">
                                                </a>
                                                <a href="javascript:void(0)" id="delete_'.$unique_id3.'" class="delete-anchor">'.lang('delete').'</a>
                                            </div>

                                           ';
                      $value             = $general_data->login_background;
                    }
                   ?>
                  <!-- image upload-->

                  <div class="form-div">
                      <div class="form-field-box odd" id="<?php echo $field_name3;?>_field_box">
                          <div class="form-input-box" id="<?php echo $field_name3;?>_input_box">

                              <span class="fileinput-button qq-upload-button" id="upload-button-<?php echo $unique_id3; ?>" style="<?php echo $display_style3;?>">
                      			<span><?php echo lang('upload')?></span>
                      			<input type="file" name="<?php echo $unique_name3; ?>" class="gc-file-upload" rel="<?php echo base_url();?>uploads/upload_image/image_uploads/upload_file/<?php echo $field_name3;?>" id="<?php echo $unique_id3; ?>">
                      			<input class="hidden-upload-input" type="hidden" name="<?php echo $field_name3;?>" value="<?php if(isset($general_data->login_background)){echo $general_data->login_background;}?>" rel="<?php echo $unique_name3; ?>">
                      		</span>

                              <div id="uploader_<?php echo $unique_id3; ?>" rel="<?php echo $unique_id3; ?>" class="grocery-crud-uploader" style=""></div>

                              <?php echo $display_image_div3; ?>

                              <div id="success_<?php echo $unique_id3; ?>" class="upload-success-url" style="display:none; padding-top:7px;">
                                  <a href="<?php echo base_url();?>assets/uploads/" id="file_<?php echo $unique_id3; ?>" class="open-file" target="_blank"></a>
                                  <a href="javascript:void(0)" id="delete_<?php echo $unique_id3; ?>" class="delete-anchor">delete</a>
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
                        <?php echo form_error("image3");?>
                        <div class="clear"></div>
                    </div>
                  </div>
              </div>
         </div><!--FAV icon DIV-->

         <div class="form-group">
              <label class="control-label col-md-3">
                <?php echo lang('register_background');?>
              </label>
              <div class="col-md-4">
                   <?php
                    if(isset($general_data->register_background) && ($general_data->register_background!=''))
                    {
                      $display_style4     = "display:none;";
                      $display_image_div4 = '
                                            <div id="success_'.$unique_id4.'" class="upload-success-url" style=" padding-top: 7px; display: block;">
                                                <a href="'.$images_path.$general_data->register_background.'" id="file_'.$unique_id4.'" class="open-file image-thumbnail" target="_blank">
                                                    <img src="'.$images_path.$general_data->register_background.'" height="50px">
                                                </a>
                                                <a href="javascript:void(0)" id="delete_'.$unique_id4.'" class="delete-anchor">'.lang('delete').'</a>
                                            </div>

                                           ';
                      $value             = $general_data->register_background;
                    }
                   ?>
                  <!-- image upload-->

                  <div class="form-div">
                      <div class="form-field-box odd" id="<?php echo $field_name4;?>_field_box">
                          <div class="form-input-box" id="<?php echo $field_name4;?>_input_box">

                              <span class="fileinput-button qq-upload-button" id="upload-button-<?php echo $unique_id4; ?>" style="<?php echo $display_style4;?>">
                            <span><?php echo lang('upload')?></span>
                            <input type="file" name="<?php echo $unique_name4; ?>" class="gc-file-upload" rel="<?php echo base_url();?>uploads/upload_image/image_uploads/upload_file/<?php echo $field_name4;?>" id="<?php echo $unique_id4; ?>">
                            <input class="hidden-upload-input" type="hidden" name="<?php echo $field_name4;?>" value="<?php if(isset($general_data->register_background)){echo $general_data->register_background;}?>" rel="<?php echo $unique_name4; ?>">
                          </span>

                              <div id="uploader_<?php echo $unique_id4; ?>" rel="<?php echo $unique_id4; ?>" class="grocery-crud-uploader" style=""></div>

                              <?php echo $display_image_div4; ?>

                              <div id="success_<?php echo $unique_id4; ?>" class="upload-success-url" style="display:none; padding-top:7px;">
                                  <a href="<?php echo base_url();?>assets/uploads/" id="file_<?php echo $unique_id4; ?>" class="open-file" target="_blank"></a>
                                  <a href="javascript:void(0)" id="delete_<?php echo $unique_id4; ?>" class="delete-anchor">delete</a>
                              </div>

                              <div style="clear:both"></div>

                              <div id="loading-<?php echo $unique_id4; ?>" style="display:none">
                                  <span id="upload-state-message-<?php echo $unique_id4; ?>"></span>
                                  <span class="qq-upload-spinner"></span>
                                  <span id="progress-<?php echo $unique_id4; ?>"></span>
                              </div>

                              <div style="display:none">
                                  <a href="<?php echo base_url();?>uploads/upload_image/image_uploads/upload_file/<?php echo $field_name4;?>" id="url_<?php echo $unique_id4; ?>"></a>
                              </div>

                              <div style="display:none">
                                  <a href="<?php echo base_url();?>uploads/upload_image/image_uploads/delete_file/<?php echo $field_name4;?>" id="delete_url_<?php echo $unique_id4; ?>" rel=""></a>
                              </div>
                        </div>
                        <?php echo form_error("image4");?>
                        <div class="clear"></div>
                    </div>
                  </div>
              </div>
         </div><!--Rregister background DIV-->

         <div class="form-group">
              <label class="control-label col-md-3">
                <?php echo lang('forget_password_background');?>
              </label>
              <div class="col-md-4">
                   <?php
                    if(isset($general_data->forget_password_background) && ($general_data->forget_password_background!=''))
                    {
                      $display_style5     = "display:none;";
                      $display_image_div5 = '
                                            <div id="success_'.$unique_id5.'" class="upload-success-url" style=" padding-top: 7px; display: block;">
                                                <a href="'.$images_path.$general_data->forget_password_background.'" id="file_'.$unique_id5.'" class="open-file image-thumbnail" target="_blank">
                                                    <img src="'.$images_path.$general_data->forget_password_background.'" height="50px">
                                                </a>
                                                <a href="javascript:void(0)" id="delete_'.$unique_id5.'" class="delete-anchor">'.lang('delete').'</a>
                                            </div>

                                           ';
                      $value             = $general_data->forget_password_background;
                    }
                   ?>
                  <!-- image upload-->

                  <div class="form-div">
                      <div class="form-field-box odd" id="<?php echo $field_name5;?>_field_box">
                          <div class="form-input-box" id="<?php echo $field_name5;?>_input_box">

                              <span class="fileinput-button qq-upload-button" id="upload-button-<?php echo $unique_id5; ?>" style="<?php echo $display_style5;?>">
                                <span><?php echo lang('upload')?></span>
                                <input type="file" name="<?php echo $unique_name5; ?>" class="gc-file-upload" rel="<?php echo base_url();?>uploads/upload_image/image_uploads/upload_file/<?php echo $field_name5;?>" id="<?php echo $unique_id5; ?>">
                                <input class="hidden-upload-input" type="hidden" name="<?php echo $field_name5;?>" value="<?php if(isset($general_data->forget_password_background)){echo $general_data->forget_password_background;}?>" rel="<?php echo $unique_name5; ?>">
                              </span>

                              <div id="uploader_<?php echo $unique_id5; ?>" rel="<?php echo $unique_id5; ?>" class="grocery-crud-uploader" style=""></div>

                              <?php echo $display_image_div5; ?>

                              <div id="success_<?php echo $unique_id5; ?>" class="upload-success-url" style="display:none; padding-top:7px;">
                                  <a href="<?php echo base_url();?>assets/uploads/" id="file_<?php echo $unique_id5; ?>" class="open-file" target="_blank"></a>
                                  <a href="javascript:void(0)" id="delete_<?php echo $unique_id5; ?>" class="delete-anchor">delete</a>
                              </div>

                              <div style="clear:both"></div>

                              <div id="loading-<?php echo $unique_id5; ?>" style="display:none">
                                  <span id="upload-state-message-<?php echo $unique_id5; ?>"></span>
                                  <span class="qq-upload-spinner"></span>
                                  <span id="progress-<?php echo $unique_id5; ?>"></span>
                              </div>

                              <div style="display:none">
                                  <a href="<?php echo base_url();?>uploads/upload_image/image_uploads/upload_file/<?php echo $field_name5;?>" id="url_<?php echo $unique_id5; ?>"></a>
                              </div>

                              <div style="display:none">
                                  <a href="<?php echo base_url();?>uploads/upload_image/image_uploads/delete_file/<?php echo $field_name5;?>" id="delete_url_<?php echo $unique_id5; ?>" rel=""></a>
                              </div>
                        </div>
                        <?php echo form_error("image5");?>
                        <div class="clear"></div>
                    </div>
                  </div>
              </div>
           </div><!--Forget Password background DIV-->
         </div>

        </div><!--general tab-->

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
        <?php echo form_close();?>




	</div>
</div>


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

var upload_info_<?php echo $unique_id4; ?> = {
accepted_file_types: /(\.|\/)(gif|jpeg|jpg|png|tiff|doc|docx|txt|odt|xls|xlsx|pdf|ppt|pptx|pps|ppsx|mp3|m4a|ogg|wav|mp4|m4v|mov|wmv|flv|avi|mpg|ogv|3gp|3g2)$/i,
accepted_file_types_ui : ".gif,.jpeg,.jpg,.png,.tiff,.doc,.docx,.txt,.odt,.xls,.xlsx,.pdf,.ppt,.pptx,.pps,.ppsx,.mp3,.m4a,.ogg,.wav,.mp4,.m4v,.mov,.wmv,.flv,.avi,.mpg,.ogv,.3gp,.3g2",
max_file_size: 20971520,
max_file_size_ui: "20MB"
};

var upload_info_<?php echo $unique_id5; ?> = {
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
