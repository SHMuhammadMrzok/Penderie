<?php
    $field_name1  = 'image';
    $unique_id1   = mt_rand();
    $unique_name1 = 's'.substr(md5($field_name1),0,8);
    
    $upload_path       = base_url().'assets/uploads/';
    $display_style     = '';
    $display_image_div = '';
    $value             = '';
?>
<div class="form">
<span class="error"><?php if(isset($validation_msg)){echo $validation_msg;}?></span>
<?php $att=array('class'=> 'form-horizontal form-bordered');
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
                  <?php echo lang('country');?><span class="required">*</span>
                </label>
               <div class="col-md-4">
                <?php   echo form_error("country_id");
                        $country_id = isset($general_data->country_id) ? $general_data->country_id : set_value('country_id') ;
                        echo form_dropdown('country_id', $countries_options,$country_id,'class="form-control select2"');
                ?>
               </div>
            </div>

            <?php /*<div class="form-group">
                <label class="control-label col-md-3">
                  <?php echo lang('discount_percentage');?><span class="required">*</span>
                </label>
               <div class="col-md-4">
                <?php
                        echo form_error("discount_percentage");
                        $discount_percentage_data = array('name'=>"discount_percentage" , 'class'=>"form-control discount_spinner" , 'value'=> isset($general_data->discount_percentage)? $general_data->discount_percentage : set_value('discount_percentage'));
                        echo form_input($discount_percentage_data);
                ?>

               </div>
               <div class="col-md-4" style="border: 0; margin-top: 25px;">
                <span>%</span>
               </div>
            </div>
            */?>

            <div class="form-group">
                <label class="control-label col-md-3">
                  <?php echo lang('product_limit_per_order');?><span class="required">*</span>
                </label>
               <div class="col-md-4">
                <?php
                        echo form_error("product_limit_per_order");
                        $product_limit_per_order_data = array('name'=>"product_limit_per_order" , 'class'=>"form-control" , 'value'=> isset($general_data->product_limit_per_order)? $general_data->product_limit_per_order : set_value('product_limit_per_order'));
                        echo form_input($product_limit_per_order_data);
                ?>
               </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3">
                  <?php echo lang('max_orders_per_day');?><span class="required"></span>
                </label>
               <div class="col-md-4">
                <?php
                    echo form_error("max_orders_per_day");
                    $max_orders_per_day_data = array('name'=>"max_orders_per_day" , 'class'=>"form-control" , 'value'=> isset($general_data->max_orders_per_day)? $general_data->max_orders_per_day : set_value('max_orders_per_day'));
                    echo form_input($max_orders_per_day_data);
                ?>
                <span class="error">*<?php echo lang('max_orders_hint');?></span>
               </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3">
                  <?php echo lang('group_price');?><span class="required"></span>
                </label>
               <div class="col-md-4">
                  <?php
                      echo form_error("price");
                      $price_data = array(
                        'name'  => "price" ,
                        'class' => "form-control" ,
                        'value' => isset($general_data->price)? $general_data->price : set_value('price')
                      );
                      echo form_input($price_data);
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
                                              <a href="'.$images_path.$general_data->image.'" id="file_'.$unique_id1.'" class="open-file image-thumbnail" target="_blank">
                                                  <img src="'.$images_path.$general_data->image.'" height="50px">
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
       </div>

            <div class="form-group">
                <label class="control-label col-md-3">
                  <?php echo lang('payment_methods');?>
                </label>
               <div class="col-md-4">
                    <?php foreach($payment_methods as $method){?>
                        <div style="margin: 5px">
                            <?php
                                echo form_error('payment_method[]');
                                $active = false;
                                if(isset($method->customer_group_id) && $method->customer_group_id != '')
                                {
                                    $active = true;
                                }


                                $payment_data = array(
                                                        'name'           => 'payment_method[]',
                                                        'class'          => 'make-switch',
                                                        'value'          => $method->id,
                                                        'checked'        => set_checkbox('payment_method[]', $active, $active),
                                                        'data-on-text'   => lang('yes'),
                                                        'data-off-text'  => lang('no'),
                                                     );
                                echo form_checkbox($payment_data);
                               ?>
                            <label><?php echo $method->name;?></label>
                        </div>
                    <?php }?>

               </div>
            </div>

          </div>
    	</div>
        <?php foreach($data_languages as $key=> $lang){ ?>
      		<div class="tab-pane" id="tab_lang_<?php echo $lang->id; ?>">
    		      <div class="form-body">
                    <div class="form-group">
                        <label class="control-label col-md-3">
                          <?php echo lang('customer_groups_title');?><span class="required">*</span>
                        </label>
                       <div class="col-md-4">
                        <?php
                                echo form_error("title[$lang->id]");
                                $title_data = array('name'=>"title[$lang->id]" , 'class'=>"form-control" , 'value'=> isset($data[$lang->id]->title)? $data[$lang->id]->title : set_value("title[$lang->id]"));
                                echo form_input($title_data);
                        ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label col-md-3">
                          <?php echo lang('description');?><span class="required"></span>
                        </label>
                       <div class="col-md-4">
                        <?php
                                echo form_error("description[$lang->id]");
                                $description_data = array(
                                    'name'=>"description[$lang->id]" , 
                                    'class'=>"form-control" , 
                                    'value'=> isset($data[$lang->id]->description)? $data[$lang->id]->description : set_value("description[$lang->id]")
                                );
                                echo form_textarea($description_data);
                        ?>
                        </div>
                    </div>
                    <?php  echo form_hidden('lang_id[]', $lang->id); ?>
                </div>
   		</div>
        <?php } ?>
        <?php  echo isset($id) ? form_hidden('customer_groups_id', $id) : ''; ?>
        <div class="form-actions">
			<div class="row">
				<div class="col-md-offset-3 col-md-9">
                 	<button type="submit" class="btn green"><i class="fa fa-check"></i><?php echo lang('submit')?></button>
				</div>
			</div>
        </div>

	</div>
</div>

<?php echo form_close();?>
</div>

<script type="text/javascript">
$(function(){
    $(".discount_spinner").TouchSpin({
        buttondown_class: 'btn green',
        buttonup_class: 'btn green',
        min: 0,
        step: .1,
        max: 1000000000,
        stepinterval: 1,
        maxboostedstep: 1
    });
});

</script>

<script type="text/javascript">
	var upload_info_<?php echo $unique_id1; ?> = {
		accepted_file_types: /(\.|\/)(gif|jpeg|jpg|png|tiff)$/i,
		accepted_file_types_ui : ".gif,.jpeg,.jpg,.png,.tiff",
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
