
<style type="text/css">
.form .form-bordered .form-group-border-none > div {
  padding: 5px !important;
  border-left: none !important;
}

.form .form-bordered .form-group-border-none {
  border-bottom: none !important;
}

.price_group_label {
    width: 100px !important;
}

</style>
<!-- START UPLOAD IMAGE LIKE IN GROCERY -->
<?php
    $field_name        = 'image';
    $unique_id         = mt_rand();
    $unique_name       = 's'.substr(md5($field_name),0,8);//'s5ae0c1c8';//'p'.substr(md5($unique_id), 0, 10);
    $upload_path       = base_url().'assets/uploads/products/';
    $display_style     = '';
    $display_image_div = '';
    $value             = '';

    $field_name2        = 'video';
    $unique_id2         = mt_rand();
    $unique_name2       = 's'.substr(md5($field_name2),0,8);//'s5ae0c1c8';//'p'.substr(md5($unique_id), 0, 10);
    $upload_path2       = base_url().'assets/uploads/videos/';
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
             url: "<?php echo base_url();?>products/admin_products/get_suggestions",  //url to get tags
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
          /*******************************************************************/
          $('.activate_country').on('switchChange.bootstrapSwitch', function (event, state) {
            var de = this;
            var pc = 'content_'+ $(de).attr('id');

            $('#'+pc).toggle();
            /*alert((this).val());
            if((this).val() == '1')
            {
                $('#'+pc).show();
            }
            else
            {
                $('#'+pc).hide();
            }*/

          });


          /*************************************************************************/
          //show cost field if quantity per serial = no

          $('.qty_per_serial').on('switchChange.bootstrapSwitch', function (event, state) {
            $('.cost_field').toggle();
          });



    });
</script>
<!-- END UPLOAD IMAGE LIKE IN GROCERY -->

<div class="form">
    <?php if(isset($error_msg)){?>
        <span class="error"><?php echo $error_msg;?></span>
    <?php }else{?>

        <span class="error"><?php if(isset($validation_msg)){echo $validation_msg.' '.validation_errors();}?></span>
        <?php $att=array('class'=> 'form-horizontal form-bordered cmxform' );
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
               <li>
            		<a href="#tab_price" data-toggle="tab">
                        <i class="fa fa-dollar font-yellow-gold"></i>
                       <span class="langname"><?php echo lang('price'); ?> </span>
                    </a>
        	   </li>

               <li>
            		<a href="#tab_optional_fields" data-toggle="tab">
                        <i class="fa fa-plus font-yellow-gold"></i>
                       <span class="langname"><?php echo lang('optional_fields'); ?> </span>
                    </a>
        	   </li>

               <li>
            		<a href="#tab_multiupload" data-toggle="tab">
                        <i class="fa fa-cloud-upload font-blue"></i>
                       <span class="langname"><?php echo lang('multi_upload'); ?> </span>
                    </a>
        	   </li>

        	</ul>

        	<div class="tab-content">
                <div class="tab-pane  active" id="tab_general">
        	      <div class="form-body">
                  <?php if($this->config->item('business_type') == 'b2b'){?>
                      <div class="form-group">
                          <label class="control-label col-md-3"><?php echo lang('name_of_store');?> <span class="required">*</span></label>
                          <div class="col-md-4">
                              <?php
                                 $store_id = isset($general_data->store_id) ? $general_data->store_id : set_value('store_id') ;

                                  echo form_dropdown('store_id', $stores, $store_id, 'class="form-control select2" id="store_id"');

                                  echo form_error('store_id');
                              ?>

                         </div>
                      </div>
                    <?php }?>

                    <div class="form-group">
                        <label class="control-label col-md-3"><?php echo lang('cat_name');?><span class="required">*</span></label>
                        <div class="col-md-4" id="available_cats">
                            <?php if(isset($mode) && $mode == 'edit' || $this->config->item('business_type') == 'b2c'){
                                   $cat_id = isset($general_data->cat_id) ? $general_data->cat_id : set_value('cat_id') ;
                                    echo form_dropdown('cat_id', $cats_array,$cat_id,'class="form-control select2" id="cat_id"');
                                    echo form_error('cat_id');
                              }
                            ?>

                       </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3"><?php echo lang('brand_name');?></label>
                        <div class="col-md-4">
                            <?php
                               $brand_id = isset($general_data->brand_id) ? $general_data->brand_id : set_value('brand_id') ;

                                echo form_dropdown('brand_id', $brands, $brand_id, 'class="form-control select2" id="brand_id"');

                                echo form_error('brand_id');
                            ?>

                       </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3"><?php echo lang('product_code');?></label>
                       <div class="col-md-4">
                            <?php
                                $code_data = array('name'=>'code','class'=>"form-control" , 'value'=> isset($general_data->code)? $general_data->code : set_value('code'));
                                echo form_input($code_data);
                            ?>
                       </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3"><?php echo lang('route');?><span class="required">*</span></label>
                       <div class="col-md-4">
                            <?php
                                $route_data = array('name'=>'route','class'=>"form-control" , 'value'=> isset($general_data->route)? $general_data->route : set_value('route'));
                                echo form_input($route_data);
                                echo form_error("route");
                            ?>
                       </div>
                       <div class="col-md-4" style="color: red;">
                       <?php echo lang('route_valid_note');?>
                       </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3"><?php echo lang('weight');?></label>
                       <div class="col-md-4">
                            <?php
                                $weight_data = array('name'=>'weight','class'=>"form-control" , 'value'=> isset($general_data->weight)? $general_data->weight : set_value('weight'));
                                echo form_input($weight_data);
                            ?>
                       </div>
                       <div class="col-md-4">
                       <?php echo lang('kgm');?>
                       </div>
                    </div>

                     <div class="form-group">
                       <label class="control-label col-md-3">
                          <?php echo lang('produc_image');?><span class="required">*</span>
                       </label>

                       <div class="col-md-4">
                         <?php
                            echo form_error('image');
                          if(isset($general_data->image) && $general_data->image !='')
                          {
                            $display_style     = "display:none;";
                            $display_image_div = '
                                                 <div id="success_'.$unique_id.'" class="upload-success-url" style=" padding-top:7px;">
                                                     <a href="'.$images_path.$general_data->image.'" id="file_'.$unique_id.'" class="open-file image-thumbnail">
                                                         <img src="'.$images_path.$general_data->image.'" height="50px" >
                                                     </a>
                                                     <a href="javascript:void(0)" id="delete_'.$unique_id.'" class="delete-anchor">delete</a>
                                                 </div>';
                            $value              = $general_data->image;
                          }
                         ?>
                            <!-- image upload-->
                            <div class="form-div">
                                <div class="form-field-box odd" id="<?php echo $field_name;?>_field_box">

                                    <div class="form-input-box" id="<?php echo $field_name;?>_input_box">

                                        <span class="fileinput-button qq-upload-button" id="upload-button-<?php echo $unique_id; ?>" style="<?php echo $display_style;?>">
                                			<span>Upload a file</span>
                                			<input type="file" name="<?php echo $unique_name; ?>" class="gc-file-upload" rel="<?php echo base_url();?>uploads/upload_image/product_uploads/upload_file/<?php echo $field_name;?>" id="<?php echo $unique_id; ?>">
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
                                            <a href="<?php echo base_url();?>uploads/upload_image/product_uploads/upload_file/<?php echo $field_name;?>" id="url_<?php echo $unique_id; ?>"></a>
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
                    </div><!-- image form group -->

                    <div class="form-group">
                       <label class="control-label col-md-3"><?php echo lang('youtube_video');?></label>
                      <div class="col-md-4">
                           <?php
                               $youtube_video_data = array(
                                 'name'=>'youtube_video',
                                 'class'=>"form-control" ,
                                 'value'=> isset($general_data->youtube_video)? 'https://www.youtube.com/watch?v='.$general_data->youtube_video : set_value('youtube_video')
                               );
                               echo form_input($youtube_video_data);
                           ?>
                      </div>
                      <div class="col-md-4">
                      <?php echo lang('youtube_video_hint');?>
                      </div>
                   </div>

                   <div class="form-group">
                     <label class="control-label col-md-3">
                        <?php echo lang('video');?><span class="required"></span>
                     </label>
                     
                     <div class="col-md-4">
                       <?php
                          echo form_error('video');
                        if(isset($general_data->video) && $general_data->video !='')
                        {
                          $display_style2     = "display:none;";
                          $display_image_div2 = '
                                               <div id="success_'.$unique_id2.'" class="upload-success-url" style=" padding-top:7px;">
                                                   <a href="'.$upload_path2.$general_data->video.'" id="file_'.$unique_id2.'" class="open-file image-thumbnail">
                                                       '.$general_data->video.'
                                                   </a>
                                                   <a href="javascript:void(0)" id="delete_'.$unique_id2.'" class="delete-anchor">delete</a>
                                               </div>';
                          $value              = $general_data->video;
                        }
                       ?>
                          <!-- image upload-->
                          <div class="form-div">
                              <div class="form-field-box odd" id="<?php echo $field_name2;?>_field_box">

                                  <div class="form-input-box" id="<?php echo $field_name2;?>_input_box">

                                      <span class="fileinput-button qq-upload-button" id="upload-button-<?php echo $unique_id2; ?>" style="<?php echo $display_style2;?>">
                                    <span><?php echo lang('upload');?></span>
                                    <input type="file" name="<?php echo $unique_name2; ?>" class="gc-file-upload" rel="<?php echo base_url();?>uploads/upload_image/image_uploads/upload_file/<?php echo $field_name2;?>" id="<?php echo $unique_id2; ?>">
                                    <input class="hidden-upload-input" type="hidden" name="<?php echo $field_name2;?>" value="<?php echo $general_data->video;?>" rel="<?php echo $unique_name2; ?>">
                                  </span>

                                      <div id="uploader_<?php echo $unique_id2; ?>" rel="<?php echo $unique_id2; ?>" class="grocery-crud-uploader" style=""></div>

                                      <?php echo $display_image_div2; ?>

                                      <div id="success_<?php echo $unique_id2; ?>" class="upload-success-url" style="display:none; padding-top:7px;">
                                          <a href="<?php echo base_url();?>assets/uploads/videos/" id="file_<?php echo $unique_id2; ?>" class="open-file" target="_blank"></a>
                                          <a href="javascript:void(0)" id="delete_<?php echo $unique_id2; ?>" class="delete-anchor"><?php echo lang('delete');?></a>
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
                   </div><!-- image form group -->



                    <div class="form-group">
                        <label class="control-label col-md-3"><?php echo lang('is_returned');?></label>
                        <div class="col-md-4">
                         <?php
                            $is_returned_value = true ;

                            if(isset($general_data->is_returned))
                            {
                                if($general_data->is_returned == 1)
                                {
                                    $is_returned_value = true;
                                }
                                if($general_data->is_returned == 0)
                                {
                                    $is_returned_value = false;
                                }
                            }

                            $is_returned_data = array(
                                        'name'           => "is_returned",
                                        'class'          => 'make-switch',
                                        'data-on-color'  => 'danger',
                                        'data-off-color' => 'default',
                                        'value'          => 1,
                                        'checked'        => set_checkbox("is_returned", $is_returned_value, $is_returned_value),
                                        'data-on-text'   => lang('yes'),
                                        'data-off-text'  => lang('no'),
                                        );
                            echo form_checkbox($is_returned_data);
                         ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3"><?php echo lang('needs_shipping');?></label>
                        <div class="col-md-4">
                         <?php
                            $shipping_value = true ;

                            if(isset($general_data->shipping))
                            {
                                if($general_data->shipping == 1)
                                {
                                    $shipping_value = true;
                                }
                                if($general_data->shipping == 0)
                                {
                                    $shipping_value = false;
                                }
                            }

                            $shipping_data = array(
                                        'name'           => "shipping",
                                        'class'          => 'make-switch',
                                        'data-on-color'  => 'danger',
                                        'data-off-color' => 'default',
                                        'value'          => 1,
                                        'checked'        => set_checkbox("shipping", $shipping_value, $shipping_value),
                                        'data-on-text'   => lang('yes'),
                                        'data-off-text'  => lang('no'),
                                        );
                            echo form_checkbox($shipping_data);
                         ?>
                        </div>
                    </div><!--Shipping-->

                    <div class="form-group">
                        <label class="control-label col-md-3"><?php echo lang('non_serials_product');?></label>
                       <div class="col-md-4">
                         <?php
                            $non_value = false ;

                            if(isset($general_data->non_serials))
                            {
                                if($general_data->non_serials == 1)
                                {
                                    $non_value = true;
                                }
                                if($general_data->non_serials == 0)
                                {
                                    $non_value = false;
                                }
                            }

                            $non_serials_data = array(
                                        'name'           => "non_serials",
                                        'class'          => 'make-switch',
                                        'data-on-color'  => 'danger',
                                        'data-off-color' => 'default',
                                        'value'          => 1,
                                        'checked'        => set_checkbox("non_serials", $non_value, $non_value),
                                        'data-on-text'   => lang('yes'),
                                        'data-off-text'  => lang('no'),
                                        );

                            echo form_checkbox($non_serials_data);
                         ?>
                        </div>
                    </div><!-- Non serials-->

                    <div class="form-group">
                        <label class="control-label col-md-3"><?php echo lang('quantity_per_serial');?></label>
                       <div class="col-md-4">
                         <?php
                               $quantity_per_serial_value     = true ;

                            if(isset($general_data->quantity_per_serial))
                            {
                                if($general_data->quantity_per_serial == 1)
                                {
                                    $quantity_per_serial_value     = true;
                                }
                                if($general_data->quantity_per_serial == 0)
                                {
                                    $quantity_per_serial_value     = false;
                                }
                            }

                            $quantity_per_serial_data = array(
                                        'name'           => "quantity_per_serial",
                                        'class'          => 'make-switch qty_per_serial',
                                        'data-on-color'  => 'danger',
                                        'data-off-color' => 'default',
                                        'value'          => 1,
                                        'checked'        => set_checkbox("quantity_per_serial", $quantity_per_serial_value, $quantity_per_serial_value),
                                        'data-on-text'   => lang('yes'),
                                        'data-off-text'  => lang('no'),
                                        );
                            echo form_checkbox($quantity_per_serial_data);
                         ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3"><?php echo lang('serials_per_country');?></label>
                       <div class="col-md-4">
                         <?php
                               $serials_per_country_value = true ;

                            if(isset($general_data->serials_per_country))
                            {
                                if($general_data->serials_per_country == 1)
                                {
                                    $serials_per_country_value = true;
                                }
                                if($general_data->serials_per_country == 0)
                                {
                                    $serials_per_country_value = false;
                                }
                            }

                            $serials_per_country_data = array(
                                        'name'           => "serials_per_country",
                                        'class'          => 'make-switch serials_per_country',
                                        'data-on-color'  => 'danger',
                                        'data-off-color' => 'default',
                                        'value'          => 1,
                                        'checked'        => set_checkbox("serials_per_country", $serials_per_country_value, $serials_per_country_value),
                                        'data-on-text'   => lang('yes'),
                                        'data-off-text'  => lang('no'),
                                        );
                            echo form_checkbox($serials_per_country_data);
                         ?>
                        </div>
                    </div>

                    <div class="form-group cost_field" style="<?php if($mode =='edit' && isset($general_data->quantity_per_serial) && ($general_data->quantity_per_serial == 0)){?>display:block;<?php }else{?>display:none;<?php } ?>">
                        <label class="control-label col-md-3"><?php echo lang('product_cost');?></label>
                       <div class="col-md-4">
                            <?php
                                echo form_error("product_cost");
                                $cost_data = array('name'=>'product_cost','class'=>"form-control" , 'value'=> isset($general_data->cost)? $general_data->cost : set_value('cost'));
                                echo form_input($cost_data);
                            ?>
                       </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3"><?php echo lang('used');?></label>
                       <div class="col-md-4">
                         <?php
                            $is_used_value = false ;
                            if(isset($general_data->is_used))
                            {
                                if($general_data->is_used == 1)
                                {
                                    $is_used_value = true;
                                }
                                if($general_data->is_used == 0)
                                {
                                    $is_used_value = false;
                                }
                            }

                            $is_used_data = array(
                                        'name'           => "is_used",
                                        'class'          => 'make-switch',
                                        'data-on-color'  => 'danger',
                                        'data-off-color' => 'default',
                                        'value'          => 1,
                                        'checked'        => set_checkbox("is_used", $is_used_value, $is_used_value),
                                        'data-on-text'   => lang('yes'),
                                        'data-off-text'  => lang('no'),
                                        );
                            echo form_checkbox($is_used_data);
                         ?>
                        </div>
                    </div>

            </div>
        </div>
                <?php foreach($data_languages as $key=> $lang){ ?>

            		<div class="tab-pane lang_tab" id="tab_lang_<?php echo $lang->id; ?>">
            		      <div class="form-body">
                            <div class="form-group">
                                <label class="control-label col-md-3">
                                    <?php echo lang('title');?><span class="required">*</span>
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
                                <label class="control-label col-md-3"><?php echo lang('description');?></label>
                                <div class="col-md-4">
                                <?php

                                        $text_data = array('name'=> "description[$lang->id]" , 'class'=>"form-control summernote_1" , 'value'=> isset($data[$lang->id]->description)? $data[$lang->id]->description : set_value("description[$lang->id]"));
                                        echo form_textarea($text_data);
                                ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3"><?php echo lang('meta_title');?></label>
                                <div class="col-md-4">
                                <?php

                                        $meta_title_data = array('name'=> "meta_title[$lang->id]" , 'class'=>"form-control " , 'value'=> isset($data[$lang->id]->meta_title)? $data[$lang->id]->meta_title : set_value("meta_title[$lang->id]"));
                                        echo form_textarea($meta_title_data);
                                ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3"><?php echo lang('meta_tag');?></label>
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
                            <div id="cat_spec_<?php echo $lang->id;?>">
                                <?php
                                  if(isset($cat_specs) && count($cat_specs) != 0)
                                  {

                                    foreach($cat_specs[$lang->id] as $row){?>
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?php echo $row->spec_label;?></label>
                                            <div class="col-md-4">
                                                <input type="text" name="spec_value[<?php echo $row->category_specification_id;?>][<?php echo $lang->id;?>]" value="<?php echo $row->spec_value;?>" class="form-control" />
                                            </div>
                                        </div>
                                    <?php }
                                  }
                                ?>
                            </div>

                            <?php  echo form_hidden('lang_id[]', $lang->id); ?>
                        </div>

            		</div>
                <?php } ?>

                <div class="cat_species_ids">
                    <?php
                      /*if(isset($cat_specs) && count($cat_specs) != 0)
                      {
                        foreach($cat_specs[$lang->id] as $row){?>
                            <input type="hidden" name="cat_spec_id[]" value="<?php echo $row->category_specification_id;?>" />
                        <?php }
                      } */
                    ?>
                </div>

                <div class="tab-pane" id="tab_price">
        	      <div class="form-body">
                    <div class="tabbable-custom form">
                    	<ul class="nav nav-tabs ">

                    	   <?php foreach($countries as $key=> $country){?>
                    	    <li <?php echo $key==0?'class="active"':'';?>>
                    			<a href="#tab_country_<?php echo $country->id; ?>" data-toggle="tab">
                                    <img alt="" src="<?php echo base_url();?>assets/uploads/<?php echo $country->flag; ?>" />
                    			     <span class="langname"><?php echo $country->name; ?> </span>
                                </a>
                        	</li>
                    	  <?php } ?>

                    	</ul>
                        <div class="tab-content">
                            <?php foreach($countries as $key=> $country){?>

                            <script type="text/javascript">

                            $(function(){
                                $(".price_spinner_<?php echo $country->id; ?>").TouchSpin({
                                    buttondown_class: 'btn green',
                                    buttonup_class: 'btn green',
                                    min: 0,
                                    max: 1000000000,
                                    step: .1,
                                    stepinterval: 1,
                                    maxboostedstep: 1,
                                    prefix: '<?php echo $country->currency_symbol; ?>'
                                });
                            })

                            </script>
                            <div class="tab-pane <?php echo $key==0?'active':'';?>" id="tab_country_<?php echo $country->id; ?>">


                              <div class="form-body">
                                <div class="form-group">
                                    <label class="control-label col-md-3">
                                      <?php echo lang('activate_this_country');?><span class="required">*</span>
                                    </label>
                                   <div class="col-md-4" style="display:block;">
                                        <?php

                                         $country_active = false;

                                         if($mode =='edit' && isset($products_countries[$id]) && in_array($country->id,$products_countries[$id]) && !isset($validation_msg))
                                         {
                                            $country_active = true;
                                         }

                                         if((isset($_POST["activate_price"][$country->id]) )&& $_POST["activate_price"][$country->id] == 1)
                                         {
                                            $country_active = true;
                                         }

                                         $show_country = array(
                                                        'name'           => "activate_price[$country->id]"  ,
                                                        'class'          => 'make-switch activate_country'  ,
                                                        'id'             => "price_tab_$country->id"        ,
                                                        'data-on-color'  => 'warning'                       ,
                                                        'data-off-color' => 'default'                       ,
                                                        'value'          => 1                               ,
                                                        'data-on-text'   => lang('yes')                     ,
                                                        'data-off-text'  => lang('no')                      ,
                                                        'checked'        => set_checkbox("activate_price[$country->id]", $country_active, $country_active)
                                                        );

                                        echo form_checkbox($show_country);
                                        echo form_error("activate_price[$country->id]");
                                          ?>
                                   </div>
                                </div>

                               <div class="price_content" style="<?php if(($mode =='edit' && isset($products_countries[$id]) && in_array($country->id,$products_countries[$id]) && !(isset($validation_msg))) || (isset($_POST["activate_price"][$country->id]) && $_POST["activate_price"][$country->id] == 1 ) ){?>display:block;<?php }else{?>display:none;<?php } ?>" id="content_price_tab_<?php echo $country->id;?>">
                                <div class="form-group">
                                    <label class="control-label col-md-3"><?php echo lang('currency');?></label>
                                   <div class="col-md-4">
                                       <?php   echo form_error("currency[$country->id]");
                                               $currency_data = array('name'=>"currency[$country->id]" , 'class'=>"form-control" ,'readonly'=>'true' , 'value'=> isset($country->currency )? $country->currency  : set_value("currency[$country->id]"));
                                               echo form_input($currency_data);
                                      ?>
                                   </div>
                                </div>
                                <?php if($mode == 'edit' && $general_data->quantity_per_serial == 1){?>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo lang('current_quantity');?></label>
                                       <div class="col-md-4">
                                           <?php   echo form_error("current_quantity[$country->id]");
                                                   $id = isset($id) ? $id : 0;
                                                   $current_quantity_data = array('name'=>"current_quantity[$country->id]" , 'class'=>"form-control" ,'readonly'=>'true' ,  'value'=> isset($available_serials[$id][$country->id])? $available_serials[$id][$country->id] : 00);
                                                   echo form_input($current_quantity_data);
                                          ?>
                                       </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo lang('average_cost');?></label>
                                       <div class="col-md-4">
                                           <?php   echo form_error("average_cost[$country->id]");
                                                   $current_quantity_data = array('name'=>"average_cost[$country->id]" , 'class'=>"form-control" ,'readonly'=>'true' ,  'value'=> isset($average_cost[$country->id])? round($average_cost[$country->id], 2) : 00);
                                                   echo form_input($current_quantity_data);
                                          ?>
                                       </div>

                                       <div class="col-md-4">
                                           <?php echo $currency_symbol;?>
                                       </div>

                                    </div>
                                <?php }?>

                                <div class="form-group">
                                     <label class="control-label col-md-3"><?php echo lang('vat');?></label>
                                    <div class="col-md-4">
                                        <?php
                                          echo form_error("vat_id[$country->id]");
                                          $vat_id = isset($products_countries_data[$country->id]->vat_id )? $products_countries_data[$country->id]->vat_id : 0;

                                          echo form_dropdown('vat_id['.$country->id.']', $vats, $vat_id, 'class="form-control select2"');
                                        ?>
                                    </div>
                                 </div>

                                <div class="form-group">
                                    <label class="control-label col-md-3">
                                      <?php echo lang('price');?><span class="required">*</span>
                                    </label>
                                   <div class="col-md-4">
                                       <?php
                                               echo form_error("price[$country->id]");
                                               $price_data = array('name'=>"price[$country->id]" , 'class'=>"form-control price_spinner_". $country->id , 'value'=> isset($products_countries_data[$country->id]->price)? $products_countries_data[$country->id]->price : set_value("price[$country->id]", 0));
                                               echo form_input($price_data);
                                      ?>
                                   </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-md-3"><?php echo lang('group_price');?></label>
                                    <div class="col-md-6">
                                        <?php foreach($customer_groups as $group){?>
                                            <div class="form-group form-group-border-none">
                                               <div class="col-md-4 input-inline price_group_label">
                                               <div style="color: #2977f7;">
                                                   <?php
                                                           echo form_label($group->title, 'price');
                                                   ?>
                                               </div>
                                               </div>

                                               <div class="col-md-4">
                                                 <div class="input-medium input-inline">
                                                   <?php   echo form_error("group_price[$country->id][$group->id]");
                                                           $group_price_data = array('name'=>"group_price[$country->id][$group->id]" , 'class'=>"form-control price_spinner_" . $country->id, 'value'=> isset($products_customer_groups_prices[$country->id][$group->id])? $products_customer_groups_prices[$country->id][$group->id] : set_value("group_price[$country->id][$group->id]", 0));
                                                           echo form_input($group_price_data);
                                                  ?>
                                                  </div>
                                               </div>
                                            </div>
                                        <?php }?>
                                   </div>
                                </div><!--customer groups -->

                                <?php /*<div class="form-group">
                                    <label class="control-label col-md-3"><?php echo lang('reward_points');?></label>
                                   <div class="col-md-4">
                                       <?php   echo form_error("reward_points[$country->id]");
                                               $reward_points_data = array('name'=>"reward_points[$country->id]" , 'class'=>"form-control reward_points_spinner" , 'value'=> isset($products_countries_data[$country->id]->reward_points )? $products_countries_data[$country->id]->reward_points  : set_value("reward_points[$country->id]", 0));
                                               echo form_input($reward_points_data);
                                      ?>
                                   </div>
                                </div>
                                */?>

                                <?php /*<div class="form-group">
                                    <label class="control-label col-md-3"><?php echo lang('points_cost');?></label>
                                   <div class="col-md-4">
                                       <?php   echo form_error("points_cost[$country->id]");
                                               $points_cost_data = array(
                                                                        'name'=>"points_cost[$country->id]" ,
                                                                        'class'=>"form-control points_cost_spinner" ,
                                                                        'value'=> isset($products_countries_data[$country->id]->points_cost )? $products_countries_data[$country->id]->points_cost  : set_value("points_cost[$country->id]", 0));
                                               echo form_input($points_cost_data);
                                      ?>
                                   </div>
                                </div>
                                */?>
                                <div class="form-group">
                                    <label class="control-label col-md-3">
                                      <?php echo lang('active');?><span class="required">*</span>
                                    </label>
                                   <div class="col-md-4">
                                     <?php
                                        echo form_error('active');

                                        $active_value     = true ;

                                        if(isset($products_countries_data[$country->id]->active))
                                        {
                                            if($products_countries_data[$country->id]->active == 1)
                                            {
                                                $active_value     = true;
                                            }
                                            if($products_countries_data[$country->id]->active == 0)
                                            {
                                                $active_value     = false;
                                            }
                                        }

                                        $active_data = array(
                                                    'name'           => "active[$country->id]",
                                                    'class'          => 'make-switch',
                                                    'data-on-color'  => 'danger',
                                                    'data-off-color' => 'default',
                                                    'value'          => 1,
                                                    'checked'        => set_checkbox("active[$country->id]", $active_value, $active_value),
                                                    'data-on-text'   => lang('yes'),
                                                    'data-off-text'  => lang('no'),
                                                    );
                                        echo form_checkbox($active_data);
                                     ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-md-3">
                                      <?php echo lang('display_home');?><span class="required">*</span>
                                    </label>
                                   <div class="col-md-4">
                                     <?php
                                        $display_home = true ;

                                        if(isset($products_countries_data[$country->id]->display_home))
                                        {
                                            if($products_countries_data[$country->id]->display_home == 1)
                                            {
                                                $display_home     = true;
                                            }
                                            if($products_countries_data[$country->id]->display_home == 0)
                                            {
                                                $display_home     = false;
                                            }
                                        }

                                        $display_home_data = array(
                                                    'name'           => "display_home[$country->id]",
                                                    'class'          => 'make-switch',
                                                    'data-on-color'  => 'success',
                                                    'data-off-color'  => 'default',
                                                    'value'          => 1,
                                                    'checked'        => set_checkbox("display_home[$country->id]", $display_home, $display_home),
                                                    'data-on-text'   => lang('yes'),
                                                    'data-off-text'  => lang('no'),
                                                    );
                                        echo form_checkbox($display_home_data);
                                     ?>
                                    </div>
                                </div>
                                </div><!--price content-->
                              </div><!-- form-body-->
                           </div><!--tab-pane-->
                           <?php  echo form_hidden('country_id[]', $country->id) ;?>
                           <?php } ?>
                        </div>
                     </div> <!--tabbable inserted taps -->

                </div><!--form-body -->
               </div><!--tap_price -->

               <div class="tab-pane" id="tab_optional_fields">
	                <div class="form-body form-inline">

                  <?php
                  if(isset($product_optional_fields) && count($product_optional_fields) != 0){
                      foreach($product_optional_fields as $field){?>
                          <div class="option_row">
                              <button class="btn btn-sm red filter-cancel btn-warning remove_option" value="<?php echo $field->id;?>" data-toggle="confirmation"><i class="fa fa-times"></i><span><?php echo lang('delete');?></span></button>

                  						<div class="row bg-gray">
                  							<div class="col-md-6">
                  								<div class="form-group" >
                  									<div class="col-md-6">
                  									   <label class="control-label"><?php echo lang('optional_fields');?></label>
                  									</div>

                  									<div class="col-md-6 no-padding">
                  										<select class="form-control select2 option_id" name="exist_option_id[]">
                  											<?php
                  											 foreach($optional_fields as $option)
                  											 {
                  												$selected = '';
                  												if($field->optional_field_id == $option->id){$selected='selected';}
                  												?>
                  												<option value="<?php echo $option->id;?>" <?php echo $selected;?> data-has_value="<?php echo $option->has_value;?>" data-has_options="<?php echo $option->has_options;?>" data-free="<?php echo $option->free;?>"><?php echo $option->label;?></option>
                  											<?php }?>
                  										</select>

                  								   </div>
                  								</div>
                  							</div><!--col-->
                  							<div class="col-md-6">
                  							   <div class="form-group" >
                          						<div class="col-md-6">
                           								<label class="control-label"><?php echo lang('is_required');?></label>
                           					    </div>

                                        <div class="col-md-6 no-padding">
                                            <?php
                                                   $required_value = true ;

                                                    if($field->required == 1)
                                                    {
                                                        $required_value = true;
                                                    }
                                                    else
                                                    {
                                                        $required_value = false;
                                                    }

                                                    $required_data = array(
                                                                'name'           => "exist_required[$field->id]",
                                                                'class'          => 'make-switch required_field',
                                                                'data-on-color'  => 'danger',
                                                                'data-off-color' => 'default',
                                                                'value'          => 1,
                                                                'checked'        => set_checkbox("required", $required_value, $required_value),
                                                                'data-on-text'   => lang('yes'),
                                                                'data-off-text'  => lang('no'),
                                                                );
                                                    echo form_checkbox($required_data);
                                                ?>
                                       </div>
                                    </div>
                  							</div><!--col-->
                            </div><!--row-->

                						<div class="row">
                							   <div class="form-group value_div" style="display: <?php echo $field->has_value == 1 ? '' : 'none';?>;">
                                   <label class="control-label col-md-3"><?php echo lang('value');?></label>
                                   <div class="col-md-4">
                                        <?php
                                            $value_data = array('name'=>'exist_value['.$field->id.']', 'value'=>$field->default_value, 'class'=>"form-control");
                                            echo form_input($value_data);
                                        ?>
                                   </div>
                                </div>
                						</div>

                        <div class="row">

                        <div class="cost_div" style="display: <?php echo $field->free == 0 ? '' : 'none';?>;">

							<div class="row m-0">
		                      <?php if($field->has_options == 0){?>
                                   <div class="col-md-4 no-padding">
                                        <?php
                                            $cost_data = array('name'=>'exist_cost['.$field->id.'][0]', 'value'=>$field->cost[0]->cost, 'class'=>"form-control");
                                            echo form_input($cost_data);
                                        ?>
                                   </div>

                                <?php }else{?>

                          <?php foreach($field->cost as $item){?>
                              <div class="row-flex-1re">
                                  <div class="col-md-4">
								     <div class="form-group ">
                                        <div class="col-md-6 no-padding">
    										<label class="control-label"><?php echo $item->field_value;?></label>
    									</div>

                                        <div class="col-md-6 no-padding">
                                            <?php
                                                $cost_data = array('style'=>'margin-top:5px;' , 'name'=>'exist_cost['.$field->id.']['.$item->id.']', 'value'=>$item->cost, 'class'=>"form-control");
                                                echo form_input($cost_data);
                                            ?>
                                          </div>
                                      </div>
								  </div><!--col-->

                                  <?php if($field->has_weight == 1){?>
                                      <div class="col-md-4">
    								     <div class="form-group ">
                                            <div class="col-md-6 no-padding">
        										<label class="control-label"><?php echo lang('weight');?></label>
        									</div>

                                            <div class="col-md-6 no-padding">
                                                <?php
                                                    $weight_data = array('style'=>'margin-top:5px;',
                                                     'name'=>'exist_weight['.$field->id.']['.$item->id.']',
                                                     'value'=>$item->weight, 'class'=>"form-control"
                                                     );
                                                    echo form_input($weight_data);
                                                ?>
                                              </div>
                                          </div>
    								  </div><!--col-->
                                  <?php }?>
								  <div class="col-md-4">

                                      <div class="form-group">
                                        <label class="control-label col-md-6"><?php echo lang('thumbnail');?></label>

                                          <?php if($item->image != ''){?>
                                             <div class="col-md-6 no-padding option_image_<?php echo $item->id;?>">

                                                  <img  width="70" height="70" src="<?php echo $images_path.$item->image;?>" />
                                                  <button  value="<?php echo $field->id.'_'.$item->id;?>"  class="btn btn-sm red filter-cancel delete_alert delete-btn remove_op_image"><?php echo lang('delete');?></button>
                                             </div>
                                          <?php }else{?>
                                              <div class="col-md-6 no-padding ">
                                                  <input type="file" name="exist_op_image[<?php echo $field->id.']['.$item->id;?>]" />
                                              </div>
                                          <?php }?>

                                      </div>

								  </div><!--col-->

								  <div class="col-md-4">


                                      <div class="form-group">
                                        <label class="control-label col-md-6"><?php echo lang('active');?></label>

                                          <div class="col-md-4 no-padding">

                                              <?php
                                              if($item->active ==1 )
                                              {
                                                $active_val = true;
                                              }
                                              else
                                              {
                                                $active_val = false;
                                              }

                                              $active_option_data = array(
                                                'name'           => 'exist_op_active['.$field->id.']['.$item->id.']',
                                                'class'          => 'make-switch',
                                                'data-on-color'  => 'danger',
                                                'data-off-color' => 'default',
                                                'value'          => 1,
                                                'checked'        => set_checkbox("op_active", $active_val, $active_val),
                                                'data-on-text'   => lang('yes'),
                                                'data-off-text'  => lang('no'),
                                                );

                                                echo form_checkbox($active_option_data);
                                                ?>
                                           </div>
                                      </div>
								  </div><!--col-->


                              </div>

                              <div class="form-group">
                                <label class="control-label col-md-3"><?php echo lang('group_price');?></label>
                                <div class="col-md-6">
                                    <?php foreach($customer_groups as $group){?>
                                        <div class="form-group form-group-border-none" style="display: block;">
                                           <div class="col-md-4 input-inline price_group_label">
                                           <div style="color: #2977f7;">
                                               <?php
                                                       echo form_label($group->title, 'price');
                                               ?>
                                           </div>
                                           </div>

                                           <div class="col-md-4">
                                             <div class="input-medium input-inline">
                                               <?php //echo '<pre>'; print_r($item->groups_prices[$group->id] );die();
                                               echo form_error("exist_op_group_price[$field->id][$item->id][$group->id]");
                                                       $op_group_price_data = array('name'=>"exist_op_group_price[$field->id][$item->id][$group->id]" ,
                                                       'class'=>"form-control price_spinner",
                                                       'value'=> isset($item->groups_prices[$group->id])? $item->groups_prices[$group->id] : set_value("op_group_price[$field->id][$item->id][$group->id]", 0));
                                                       echo form_input($op_group_price_data);
                                              ?>
                                              </div>
                                           </div>
                                        </div>
                                    <?php }?>
                               </div>
                            </div><!--customer groups -->

                          <?php }?>
                 <?php }?>
							</div><!--col-->


                        </div>
			         </div><!--row-->


						<div class="row">

                <?php if(isset($field->secondary_fields) && count($field->secondary_fields) != 0){  //echo '<pre>';print_r($field->secondary_fields);die();?>
                  <div class="col-md-12">
                       <label class="control-label"><?php echo lang('secondary');?></label>
                   </div><!--col-->
                    <?php foreach($field->secondary_fields as $row){?>
                        <div class="form-group">

                              <div class="col-md-4 no-padding">
                                  <?php
                                     $sec_field_id = isset($row->id) ? $row->id : 0;

                                      echo form_dropdown('exist_sec_option_id['.$field->id.'][]', $secondary_optional_fields2, $sec_field_id, 'class="form-control select2 sec_option_id" id=""');

                                      echo form_error('sec_option_id');
                                  ?>

                             </div>
                          </div>


                          <div class="form-group value_div" style="display: <?php echo $row->has_value == 1 ? '' : 'none';?>;">

      											<div class="col-md-4">
      												<label class="control-label"><?php echo lang('value');?></label>
      											</div><!--col-->

                             <div class="col-md-4">
                                  <?php
                                      $sec_value_data = array('name'=>'exist_sec_value[]', 'value'=>$row->default_value, 'class'=>"form-control");
                                      echo form_input($sec_value_data);
                                  ?>
                             </div>
                          </div>

                          <div class="form-group cost_div" style="margin-top:10px;width: 100%; display: <?php echo $row->free == 0 ? 'block' : 'none';?>;">
                                                <?php /*
          											<div class="col-md-3">
          												<label class="control-label"><?php echo lang('cost');?></label>
          											</div><!--col-->
                                                    */?>

                              <div class="col-md-10 no-padding" style="border-style: ridge;overflow: hidden;margin-bottom: 10px; margin-right: 40px;">

                               <?php if($row->has_options == 0){?>
                                   <div class="col-md-4">
                                        <?php
                                            $cost_data = array('name'=>'exist_cost['.$field->optional_field_id.']['.$row->optional_field_id.'][0]', 'value'=>$row->cost[0]->cost, 'class'=>"form-control");
                                            echo form_input($cost_data);
                                        ?>
                                   </div>
                                 <?php }else{?>

                                  <?php foreach($row->cost as $item){?>
                                    <div class="row" style="margin-bottom: 10px;">
                                      <div style="display: block!important; overflow: hidden; margin-bottom: 10px;">

										<div class="col-md-3">
											<label class="control-label"><?php echo $item->field_value;?></label>
										</div><!--col-->

                                          <div class="col-md-3 no-padding">
                                              <?php
                                                  $cost_data = array('name'=>'exist_cost['.$field->optional_field_id.']['.$row->optional_field_id.']['.$item->optional_field_option_id.']', 'value'=>$item->cost, 'class'=>"form-control");
                                                  echo form_input($cost_data);
                                              ?>
                                         </div>


                                  <?php if($item->image != ''){?>
                                     <div class="col-md-4 no-padding option_image_<?php echo $item->id;?>" style="float: left;">

                                          <img width="150" height="100" src="<?php echo $images_path.$item->image;?>" />
                                          <button style="margin: 5px;" value="<?php echo $field->id.'_'.$item->id;?>"  class="btn btn-sm red filter-cancel delete_alert delete-btn remove_op_image"><?php echo lang('delete');?></button>
                                     </div>
                                  <?php }else{?>
                                            <input type="file" name="exist_op_image[<?php echo $field->optional_field_id.']['.$row->optional_field_id.']['.$item->optional_field_option_id;?>]" />

                                  <?php }?>
                                 </div>
                                </div>
                                  <?php /*<div class="margin-bottom-10">

                                			<label for="option1" style="width: 100px;"><?php echo lang('default');?></label>

                                                <?php /*
                                                    $default_selection = $field->default_selection;
                                                    $default_sec_data = array(
                                                                                'name'    => 'default_sec_selection['.$field->optional_field_id.']',
                                                                                'class'   => 'make-switch switch-radio1',
                                                                                'id'      => 'option1',
                                                                                'value'   => '['.$item->optional_field_option_id.']',
                                                                                'checked' => set_radio('default_sec_selection['.$field->optional_field_id.']', 1, $default_selection)
                                                                            );

                                                    echo form_radio($default_sec_data);
                                                    */
                                        /*
                                			</div>
                                        */?>
                                        <?php }?>
                                       <?php }?>
									</div><!--col-->
                                </div>

                        <?php }
                        }?>
                        </div><!--row-->


						<?php if(!isset($field->secondary_fields)){?>
                            <button class="add_sec btn btn-sm green filter-success btn-success sec_<?php echo $field->id;?>" style="margin-bottom:15px" data-option_id="<?php echo $field->id;?>"><?php echo lang('add').' '.lang('secondary');?></button>
                        <?php }?>

                    </div>

            <?php }
            }
            else{?>

                <div class="option_row">

                   	<div class="col-md-11">

						   <div class="form-group" >
    						<div class="col-md-2">
    							<div class="col-md-12">
    								<label class="control-label"><?php echo lang('optional_fields');?></label>
    							</div>
    					    </div>

                            <div class="col-md-4 no-padding">
                                <select class="form-control select2 option_id" name="option_id[]">
                                    <option value="">-----------------</option>
                                    <?php foreach($optional_fields as $option){?>
                                        <option value="<?php echo $option->id;?>" data-has_value="<?php echo $option->has_value;?>" data-free="<?php echo $option->free;?>"><?php echo $option->label;?></option>
                                    <?php }?>
                                </select>
                                <?php
                                   //echo form_dropdown('option_id[]', $optional_fields, 0,'class="form-control select2"');
                                ?>
                           </div>
						</div>



                        <div class="form-group" >
    						<div class="col-md-2">
    							<div class="col-md-12">
    								<label class="control-label"><?php echo lang('is_required');?></label>
    							</div>
    					    </div>

                            <div class="col-md-4 no-padding">
                                 <?php

                               $required_value = true ;

                               $required_data = array(
                                            //'name'           => "required[]",
                                            'class'          => 'make-switch required_field',
                                            'data-on-color'  => 'danger',
                                            'data-off-color' => 'default',
                                            'value'          => 1,
                                            'checked'        => set_checkbox("required", $required_value, $required_value),
                                            'data-on-text'   => lang('yes'),
                                            'data-off-text'  => lang('no'),
                                            );
                                echo form_checkbox($required_data);
                            ?>

                           </div>
						</div>

						<div class="col-md-12">
               <div class="form-group value_div" style="display: none;">
                      <label class="control-label col-md-3"><?php echo lang('value');?></label>
                     <div class="col-md-4">
                          <?php
                              $sec_value_data = array('name'=>'sec_value[]', 'class'=>"form-control");
                              echo form_input($sec_value_data);
                          ?>
                     </div>
                  </div>

                  <div class="form-group cost_div" style="display: none;"></div>
						</div>
					</div> <!--col-->




					<div class="col-md-1">
						<button class="btn btn-sm red filter-cancel btn-warning remove_option" data-toggle="confirmation"><i class="fa fa-times"></i><span><?php echo lang('delete');?></span></button>
					</div>


                </div>
            <?php }?>

            <div class="form-actions add_div" style="margin-top: 15px;">

                        <button class="btn blue add_option"><i class="fa fa-plus"></i> <?php echo lang('add_option');?></button>

            </div>

         </div>
       </div>

               <div class="tab-pane" id="tab_multiupload">
        	      <div class="form-body">

                    <?php if(isset($product_images)){?>
                        <div class="form-group">
                            <div class="row no-margin">
                            	<label class="control-label"><?php echo lang('product_images');?></label>
                            </div><!--row-->
                            <div class="row no-margin">
                            <?php

                                foreach ($product_images as $image){?>
                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12" id="product_image_<?php echo $image->id;?>">
                                    	<div class="upload-div">
                                        <button style="margin: 5px;" value="<?php echo $image->id;?>" class="btn btn-sm red filter-cancel delete_alert delete-btn remove_image"><?php echo lang('delete');?></button>
                                        <a href='<?php echo base_url();?>assets/uploads/products/<?php echo $image->image;?>' class='image-thumbnail'>
                                            <img src='<?php echo base_url();?>assets/uploads/products/<?php echo $image->image;?>' width='80' height='50' />
                                        </a>
                                    </div><!--upload-div-->
                                    </div><!--col-->
                            <?php  }?>
                            </div><!--row-->
                        </div>
                    <?php }?>

                    <div class="form-group">
                        <label class="control-label col-md-3"><?php echo lang('multi_upload');?></label>
                        <div class="col-md-4">
                            <?php
                                $multi_data = array('name'=>"files[]", "multiple"=>true, 'class'=>"form-control", 'accept'=>'.gif,.jpeg,.jpg,.png,.tiff' );
                                echo form_upload($multi_data);
                            ?>
                       </div>
                    </div>

                  </div>
               </div>


                <div class="form-actions">
        			<div class="row">
        				<div class="col-md-offset-3 col-md-9">
                            <?php  echo isset($id) ? form_hidden('product_id', $id) : ''; ?>
                         	<button type="submit" class="btn green"><i class="fa fa-check"></i> <?php echo lang('submit');?></button>
        				 </div>
        			</div>
                </div>

        	</div>
        </div>

        <?php echo form_close();?>
    <?php }?>
</div>
<style>
.error{
    color: #a94442;
}
input.error {
  border: 1px dotted red;
}
</style>


<script type="text/javascript">

var upload_info_<?php echo $unique_id; ?> = {
	accepted_file_types: /(\.|\/)(gif|jpeg|jpg|png|tiff|doc|docx|txt|odt|xls|xlsx|pdf|ppt|pptx|pps|ppsx|mp3|m4a|ogg|wav|mp4|m4v|mov|wmv|flv|avi|mpg|ogv|3gp|3g2)$/i,
	accepted_file_types_ui : ".gif,.jpeg,.jpg,.png,.tiff,.doc,.docx,.txt,.odt,.xls,.xlsx,.pdf,.ppt,.pptx,.pps,.ppsx,.mp3,.m4a,.ogg,.wav,.mp4,.m4v,.mov,.wmv,.flv,.avi,.mpg,.ogv,.3gp,.3g2",
	max_file_size: 20971520,
	max_file_size_ui: "20MB"
};

var upload_info_<?php echo $unique_id2; ?> = {
  accepted_file_types: /(\.|\/)(m4a|ogg|wav|mp4|m4v|mov|wmv|flv|avi|mpg|ogv|3gp|3g2)$/i,
  accepted_file_types_ui : ".m4a,.ogg,.wav,.mp4,.m4v,.mov,.wmv,.flv,.avi,.mpg,.ogv,.3gp,.3g2",
  max_file_size: 50971520,
	max_file_size_ui: "50MB"
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

var upload_a_file_string = "Upload a file";

$(function(){
                $(".reward_points_spinner").TouchSpin({
                    buttondown_class: 'btn red',
                    buttonup_class: 'btn green',
                    min: 0,
                    max: 1000000000,
                    step: .1,
                    stepinterval: 1,
                    maxboostedstep: 1,

                });
            })

$(function(){
                $(".points_cost_spinner").TouchSpin({
                    buttondown_class: 'btn red',
                    buttonup_class: 'btn green',
                    min: 0,
                    max: 1000000000,
                    step: .1,
                    stepinterval: 1,
                    maxboostedstep: 1,

                });
            })



</script>
<?php if(isset($general_data)){?>
<div id="fancybox-tmp" style="padding: 50px;"></div>
<div id="fancybox-loading" style="display: none;"><div></div></div>
<div id="fancybox-overlay" style="display: none;"></div>
<div id="fancybox-wrap" style="display: none; width: 232px; height: auto; top: 1020px; left: 538px;">
    <div id="fancybox-outer">
        <div class="fancybox-bg" id="fancybox-bg-n"></div>
        <div class="fancybox-bg" id="fancybox-bg-ne"></div>
        <div class="fancybox-bg" id="fancybox-bg-e"></div>
        <div class="fancybox-bg" id="fancybox-bg-se"></div>
        <div class="fancybox-bg" id="fancybox-bg-s"></div>
        <div class="fancybox-bg" id="fancybox-bg-sw"></div>
        <div class="fancybox-bg" id="fancybox-bg-w"></div>
        <div class="fancybox-bg" id="fancybox-bg-nw"></div>
        <div id="fancybox-content" style="border-width: 10px; width: 212px; height: 159px;">
            <img id="fancybox-img" src="<?php echo $upload_path.$general_data->image;?>" alt="">
        </div>
        <a id="fancybox-close" style="display: none;"></a>
        <div id="fancybox-title" style="display: none;"></div>
        <a href="javascript:;" id="fancybox-left"><span class="fancy-ico" id="fancybox-left-ico"></span></a>
        <a href="javascript:;" id="fancybox-right"><span class="fancy-ico" id="fancybox-right-ico"></span></a>
    </div>
</div>
<?php }?>
<script>
/************************************************************************/
//show category optional fields
$(document).ready(function() {
  $('#cat_id').on('change', function(){
    //alert('caaaats');
    var postData = {
                      cat_id : $('#cat_id').val()
                   };

    $.post('<?php echo base_url().'products/admin_products/get_cat_spec';?>', postData, function(result){

        $.each(result[0], function(index, value) {
            $("#cat_spec_"+index).html(value);
        });

        $('.cat_species_ids').html(result[1]);
    }, 'json');

  });
});
</script>


<script>
$('.remove_image').click(function(event){
   event.preventDefault();
   var image_id = $(this).val();

   postdata = {
                image_id   : image_id,
                product_id : '<?php echo isset($id) ? $id : 0;?>'
              }

   $.post('<?php echo base_url().'products/admin_products/remove_product_image'?>', postdata, function(result){
    if(result[0] == 1)
    {
        $('#product_image_'+image_id).remove();
        showToast(result[1], '<?php echo lang('success');?>', 'success');
    }
    else
    {
        showToast(result[1], '<?php echo lang('error');?>', 'error');
    }
   }, 'json');
});


<?php if($this->config->item('business_type') == 'b2b'){?>
  /**********Show store cats****************************************/


  $(document).ready(function() {
    //on page load

    var store_id = $('#store_id').val();
    <?php if($mode == 'add'){?>
    if(store_id != 0)
    {
      var postData = {
                        store_id : store_id
                     };

      $.post('<?php echo base_url().'products/admin_products/get_store_cats';?>', postData, function(result){
          $('#available_cats').html(result);
      });
    }
    <?php }?>

    // on change store
    $('#store_id').on('change', function(){

      var postData = {
                        store_id : $('#store_id').val()
                     };

      $.post('<?php echo base_url().'products/admin_products/get_store_cats';?>', postData, function(result){
          $('#available_cats').html(result);
      });

    });
  });
<?php }?>
/*************************************************/
</script>


<!--OPTIONAL FIELDS-->
<script>
 //show value input
 $('body').on('change', '.option_id', function(){

    var optionId    = $(this).val();
    var hasValues   = $(this).find(':selected').data('has_value');
    var hasOptionss = $(this).find(':selected').data('has_options');
    var free        = $(this).find(':selected').data('free');

    var costDiv     = $(this).closest(".option_row").find(".cost_div");

    //var optionId  = $(this).val();

    if(parseInt(hasValues) == 1)
    {
         $(this).closest(".option_row").find(".value_div").show();
    }
    else
    {
        $(this).closest(".option_row").find(".value_div").hide();
    }

    if(parseInt(free) == 0)
    {
        postData = {
            option_id : optionId
        }
        $.post('<?php echo base_url();?>products/admin_products/get_optional_field_options', postData, function(result){
           costDiv.show();
           costDiv.html(result);
        });

    }
    else
    {
        costDiv.hide();
    }


     //add secondary field name
     //$(this).find('input').attr('name', 'song' + i);

    var requiredFieldName = 'required['+optionId+']';

    $(this).closest(".option_row").find('.required_field').attr('name', requiredFieldName);
 });

  //show value input
 $('body').on('change', '.sec_option_id', function(){

    var optionId    = $(this).val();
    var hasValues   = $(this).find(':selected').data('has_value');
    var hasOptionss = $(this).find(':selected').data('has_options');
    var free        = $(this).find(':selected').data('free');

    var costDiv     = $(this).closest(".sec_option_row").find(".sec_cost_div");

    //var optionId  = $(this).val();

    if(parseInt(hasValues) == 1)
    {
         $(this).closest(".option_row").find(".value_div").show();
    }
    else
    {
        $(this).closest(".option_row").find(".value_div").hide();
    }

    if(parseInt(free) == 0)
    {
        var mainOptionId = $(this).find(':selected').data('main_option_id');

        postData = {
            option_id : optionId,
            main_option_id : mainOptionId

        }
        $.post('<?php echo base_url();?>products/admin_products/get_optional_field_options/1', postData, function(result){
           costDiv.show();
           costDiv.html(result);
        });

    }
    else
    {
        costDiv.hide();
    }


    var requiredFieldName = 'required['+optionId+']';

    $(this).closest(".option_row").find('.required_field').attr('name', requiredFieldName);
 });

 /********************************************/
 //Add option row

 $('body').on('click', '.add_option', function(event){
    event.preventDefault();

    var optionRow = '<div class="option_row"><button class="btn btn-sm red filter-cancel btn-warning remove_option" data-toggle="confirmation"><i class="fa fa-times"></i><span><?php echo lang('delete');?></span></button><div class="form-group"><div class="col-md-2"><div class="col-md-12"><label class="control-label"><?php echo lang('optional_fields');?></label></div></div><div class="col-md-4 no-padding"><select class="form-control select2 option_id" name="option_id[]"><option value="">-----------------</option><?php foreach($optional_fields as $option){?><option value="<?php echo $option->id;?>" data-has_value="<?php echo $option->has_value;?>" data-has_options="<?php echo $option->has_options;?>" data-free="<?php echo $option->free;?>"><?php echo $option->label;?></option><?php }?></select></div></div><div class="form-group"><div class="col-md-2"><div class="col-md-12"><label class="control-label"><?php echo lang('is_required');?></label></div></div><div class="col-md-4 no-padding"><input name="required[]" type="checkbox" class="make-switch required_field" data-on-color="danger" data-off-color="default" value=1 checked= <?php set_checkbox("required", "true", "true");?> data-on-text=<?php echo lang('yes');?> data-off-text= <?php echo lang('no');?>/></div></div><div class="form-group value_div" style="display: none;"><div class="col-md-2"><div class="col-md-12"><label class="control-label"><?php echo lang('value');?></label></div></div><div class="col-md-4 no-padding"><input name="value[]" class="form-control" value="<?php set_value('value');?>" /></div></div><div class="form-group cost_div" style="display: none;"><label class="control-label"><?php echo lang('cost');?></label></div></div>';

    $('.add_div').before(optionRow);
 });

 /********************************************/
 //remove row

<?php if($mode == 'edit'){?>
 $('body').on('click', '.remove_option', function(event){
    event.preventDefault();
    if($(this).val())
    {
        row_val = $(this).val();
        postData = {
                    option_id: row_val,
                    product_id: '<?php echo $id;?>'
                    };
        $.post('<?php echo base_url().'products/admin_products/remove_product_optional_field';?>', postData, function(result){

        });
    }

    $(this).parent('.option_row').remove();
 });
<?php }?>

</script>

<script>
/******ADD SECONDARY OPTION*****/
$('body').on('click', '.add_sec', function(event){
    event.preventDefault();

    var optionId = $(this).data('option_id');

    var secOptionRow = '<div class="sec_option_row"><div class="form-group" style="margin-bottom:10px;"><?php ;?><div class="col-md-2"><div class="col-md-12"><label class="control-label"><?php echo lang('secondary');?></label></div></div><?php ;?><div class="col-md-4 no-padding"><select class="form-control select2 sec_option_id" name="sec_option_id['+ optionId + '][]"><?php ;?><option value="">-----------------</option><?php foreach($secondary_optional_fields as $sec_option){?><option data-main_option_id='+optionId+' value="<?php echo $sec_option->id;?>" data-has_value="<?php echo $sec_option->has_value;?>" data-free="<?php echo $sec_option->free;?>"><?php echo $sec_option->label;?></option><?php }?></select></div></div><div class="form-group" ><?php ;?></div><div class="form-group value_div" style="display: none;"><?php ;?><label class="control-label col-md-3"><?php echo lang("value");?></label><div class="col-md-4"><input type="text" name="sec_value[]" value="" class="form-control"><?php ?></div></div><div class="form-group sec_cost_div" style="display: none;"></div></div>';
    /**/
    $(this).after(secOptionRow);
    $(this).hide();

 });

 /********REMOVE OPTIONAL FIELD IMAGE******/
 $('.remove_op_image').click(function(event){
   event.preventDefault();
   //var option_id = $(this).val();
   var optional_option_id   = $(this).val().split('_'); // we changed the value in html with optionalFieldId_optionId to instead of optionId only to be used with appended input below 
   var optional_id          = optional_option_id[0];
   var option_id            = optional_option_id[1];

   postdata = {
                option_id   : option_id,
                product_id : '<?php echo isset($id) ? $id : 0;?>'
              }

   $.post('<?php echo base_url().'products/admin_products/remove_option_field_image'?>', postdata, function(result){
    if(result[0] == 1)
    { 
        var image_input = '<input type="file" name="exist_op_image[' + optional_id + '][' + option_id + ']" />';
        $('.option_image_'+option_id).empty();
        $('.option_image_'+option_id).append(image_input);

        showToast(result[1], '<?php echo lang('success');?>', 'success');
    }
    else
    {
        showToast(result[1], '<?php echo lang('error');?>', 'error');
    }
   }, 'json');
});

</script>

<script type="text/javascript">

$(function(){
    $(".price_spinner").TouchSpin({
        buttondown_class: 'btn green',
        buttonup_class: 'btn green',
        min: 0,
        max: 1000000000,
        step: .1,
        stepinterval: 1,
        maxboostedstep: 1,
        prefix: '<?php //echo $country->currency_symbol; ?>'
    });
})

</script>
