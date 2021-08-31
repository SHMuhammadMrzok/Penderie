<!--START upload single image like in GROCERY CRUD-->

<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>assets/grocery_crud/css/jquery_plugins/chosen/chosen.css" />
<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>assets/grocery_crud/css/ui/simple/jquery-ui-1.10.1.custom.min.css" />
<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>assets/grocery_crud/css/jquery_plugins/file_upload/file-uploader.css" />
<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>assets/grocery_crud/css/jquery_plugins/file_upload/jquery.fileupload-ui.css" />
<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>assets/grocery_crud/css/jquery_plugins/fancybox/jquery.fancybox.css" />
<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>assets/grocery_crud/css/jquery_plugins/file_upload/fileuploader.css" />

<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>assets/template/admin/global/css/components-rtl.css" />

<?php
    $field_name        = 'image';
    $unique_id         = mt_rand();
    $unique_name       = 's'.substr(md5($field_name),0,8);//'s5ae0c1c8';//'p'.substr(md5($unique_id), 0, 10);
    $upload_path       = base_url().'assets/uploads/products/';
    $display_style     = '';
    $display_image_div = '';
    $value             = '';
 ?>

 <style>
 /*.form-group{display: block;}*/
 </style>

<div class="right-content">
    <div class="list">
        <div class="relate"><a href="#addA" class="active"><?php echo lang('general'); ?></a></div>
        <div class="relate"><a href="#addB"><?php echo lang('product_desc');?></a></div>
        <div class="relate"><a href="#addC"><?php echo lang('price'); ?></a></div>
        <div class="relate"><a href="#addD"><?php echo lang('product_optional_fields'); ?></a></div>
        <div class="relate"><a href="#addE"><?php echo lang('product_extra_images'); ?> </a></div>
    </div>
    <form method="post" action="" class="col-12" id="main_form" enctype="multipart/form-data" accept-charset="utf-8" novalidate>
    <div class="add">
        <div id="addA" class="relateDiv row">

         <div class="error"><?php if(isset($validation_msg)){echo $validation_msg;}?></div>
            <?php
            /*
            $att=array('class'=> 'col-12' , 'id' => 'main_form');
                  echo form_open_multipart($form_action, $att);
            */
            ?>

            <div class="col-12 col-sm-6">
                <div class="form-group">
                    <div class="form-item">
                        <label><?php echo lang('name_of_store');?><span>*</span></label>
                        <?php
                           $store_id = isset($general_data->store_id) ? $general_data->store_id : set_value('store_id') ;
                            echo form_dropdown('store_id', $stores, $store_id, 'class="form-control" id="store_id" required="required"');
                            echo form_error('store_id');
                        ?>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6" >
                <div class="form-group">
                    <div class="form-item c">
                        <label><?php echo lang('cat_name');?><span>*</span></label>
                        <div id="available_cats">
                             <?php if(isset($mode) && $mode == 'edit'){
                                   $cat_id = isset($general_data->cat_id) ? $general_data->cat_id : set_value('cat_id') ;
                                    echo form_dropdown('cat_id', $cats_array,$cat_id,'class="form-control " id="cat_id" required="required"');
                                    echo form_error('cat_id');
                              }
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6">
                <div class="form-group">
                    <div class="form-item">
                        <label><?php echo lang('product_code');?></label>
                        <?php
                            $code_data = array('name'=>'code','class'=>"form-control" , 'required'      => 'required', 'value'=> isset($general_data->code)? $general_data->code : set_value('code'));
                            echo form_input($code_data);
                        ?>
                    </div>
                </div>
            </div>



            <div class="col-12 col-sm-6">
                <div class="form-group">
                    <div class="form-item">
                        <label><?php echo lang('route');?> <span> * </span> </label>
                        <?php
                            $route_data = array('name'=>'route','class'=>"form-control" , 'required'      => 'required', 'value'=> isset($general_data->route)? $general_data->route : set_value('route'));
                            echo form_input($route_data);
                            echo form_error("route");
                        ?>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6">
                <div class="form-group">
                    <div class="form-item">
                        <label><?php echo lang('weight');?></label>
                        <?php
                            $weight_data = array('name'=>'weight',
                            'class'=>"form-control" ,
                            'type' => 'number',
                            'required'      => 'required',
                            'value'=> isset($general_data->weight)? $general_data->weight : set_value('weight'));
                            echo form_input($weight_data);
                        ?>
                    </div>
                </div>
            </div>


            <div class="col-12 col-sm-6">
                <div class="form-group">
                    <div class="form-item">
                       <label >
                          <?php echo lang('produc_image');?><span class="required">*</span>
                       </label>

                       <div >
                         <?php
                            echo form_error('image');
                          if(isset($general_data->image) && $general_data->image !='')
                          {
                            $display_style     = "display:none;";
                            $display_image_div = '
                                                 <div id="success_'.$unique_id.'" class="upload-success-url" style=" padding-top:7px;">
                                                     <a href="'.$upload_path.$general_data->image.'" id="file_'.$unique_id.'" class="open-file image-thumbnail">
                                                         <img src="'.$upload_path.$general_data->image.'" height="50px" >
                                                     </a>
                                                     <a href="javascript:void(0)" id="delete_'.$unique_id.'" class="delete-anchor">
                                                     <svg id="Layer_1" enable-background="new 0 0 512 512" height="16" viewBox="0 0 512 512" width="16" xmlns="http://www.w3.org/2000/svg"><g><path d="m424 64h-88v-16c0-26.467-21.533-48-48-48h-64c-26.467 0-48 21.533-48 48v16h-88c-22.056 0-40 17.944-40 40v56c0 8.836 7.164 16 16 16h8.744l13.823 290.283c1.221 25.636 22.281 45.717 47.945 45.717h242.976c25.665 0 46.725-20.081 47.945-45.717l13.823-290.283h8.744c8.836 0 16-7.164 16-16v-56c0-22.056-17.944-40-40-40zm-216-16c0-8.822 7.178-16 16-16h64c8.822 0 16 7.178 16 16v16h-96zm-128 56c0-4.411 3.589-8 8-8h336c4.411 0 8 3.589 8 8v40c-4.931 0-331.567 0-352 0zm313.469 360.761c-.407 8.545-7.427 15.239-15.981 15.239h-242.976c-8.555 0-15.575-6.694-15.981-15.239l-13.751-288.761h302.44z"/><path d="m256 448c8.836 0 16-7.164 16-16v-208c0-8.836-7.164-16-16-16s-16 7.164-16 16v208c0 8.836 7.163 16 16 16z"/><path d="m336 448c8.836 0 16-7.164 16-16v-208c0-8.836-7.164-16-16-16s-16 7.164-16 16v208c0 8.836 7.163 16 16 16z"/><path d="m176 448c8.836 0 16-7.164 16-16v-208c0-8.836-7.164-16-16-16s-16 7.164-16 16v208c0 8.836 7.163 16 16 16z"/></g></svg>
                                                     </a>
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
                                            <a href="javascript:void(0)" id="delete_<?php echo $unique_id; ?>" class="delete-anchor">
                                            <svg id="Layer_1" enable-background="new 0 0 512 512" height="16" viewBox="0 0 512 512" width="16" xmlns="http://www.w3.org/2000/svg"><g><path d="m424 64h-88v-16c0-26.467-21.533-48-48-48h-64c-26.467 0-48 21.533-48 48v16h-88c-22.056 0-40 17.944-40 40v56c0 8.836 7.164 16 16 16h8.744l13.823 290.283c1.221 25.636 22.281 45.717 47.945 45.717h242.976c25.665 0 46.725-20.081 47.945-45.717l13.823-290.283h8.744c8.836 0 16-7.164 16-16v-56c0-22.056-17.944-40-40-40zm-216-16c0-8.822 7.178-16 16-16h64c8.822 0 16 7.178 16 16v16h-96zm-128 56c0-4.411 3.589-8 8-8h336c4.411 0 8 3.589 8 8v40c-4.931 0-331.567 0-352 0zm313.469 360.761c-.407 8.545-7.427 15.239-15.981 15.239h-242.976c-8.555 0-15.575-6.694-15.981-15.239l-13.751-288.761h302.44z"/><path d="m256 448c8.836 0 16-7.164 16-16v-208c0-8.836-7.164-16-16-16s-16 7.164-16 16v208c0 8.836 7.163 16 16 16z"/><path d="m336 448c8.836 0 16-7.164 16-16v-208c0-8.836-7.164-16-16-16s-16 7.164-16 16v208c0 8.836 7.163 16 16 16z"/><path d="m176 448c8.836 0 16-7.164 16-16v-208c0-8.836-7.164-16-16-16s-16 7.164-16 16v208c0 8.836 7.163 16 16 16z"/></g></svg>
                                        </a>
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
                    </div>
                </div>
            </div>
            <div class="container-fluid button-style butcenter">
              <button class="next"><?php echo lang('continue');?></button>
            </div>
            <input type="hidden" name="shipping" value="1" />
            <input type="hidden" name="non_serials" value="1" />
            <input type="hidden" name="quantity_per_serial" value="0" />

        </div>
        <div id="addB" class="relateDiv row">
            <?php
            $isa = 0;
            foreach(array_reverse($data_languages) as $key=> $lang){
            $isa++;
            if($isa ==1){
                ?>
                <div class="col-12">
                    <div class="form-group">
                        <div class="form-item">
                            <label for="address"> <?php echo lang('seo_hint');?></label>
                            <a href="<?php echo $this->config->item('seo_url');?>" target="_blank" ><?php echo lang('press_here');?></a>
                        </div>
                    </div>
                </div>

               <?php
            }



            ?>


                <div class="col-12 col-sm-6 <?php echo $lang->direction == 'ltr' ? 'en-version':'';?>">
                    <h5 style="color: #f79d36;padding: 10px 0"><?php echo $lang->name; ?></h5>
                    <div class="form-group">
                        <div class="form-item">
                            <label for="address"> <?php echo lang('title');?><span>*</span></label>
                            <?php
                                echo form_error("title[$lang->id]");
                                $title_data = array('name'=>"title[$lang->id]" , 'required'      => 'required', 'class'=>"form-control" ,'value'=> isset($data[$lang->id]->title)? $data[$lang->id]->title : set_value("title[$lang->id]") );
                                echo form_input($title_data);
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-item">
                            <label for="desc"><?php echo lang('description');?></label>
                            <?php
                                $text_data = array('name'=> "description[$lang->id]" , 'required'      => 'required', 'class'=>"form-control summernote_1" , 'value'=> isset($data[$lang->id]->description)? $data[$lang->id]->description : set_value("description[$lang->id]"));
                                echo form_textarea($text_data);
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-item">
                            <label for="desc2"><?php echo lang('meta_title');?></label>
                            <?php
                                $meta_title_data = array('name'=> "meta_title[$lang->id]" , 'required'      => 'required', 'class'=>"form-control " , 'value'=> isset($data[$lang->id]->meta_title)? $data[$lang->id]->meta_title : set_value("meta_title[$lang->id]"));
                                echo form_textarea($meta_title_data);
                            ?>
                        </div>
                    </div>

                </div>
                <?php  echo form_hidden('lang_id[]', $lang->id); ?>
            <?php }?>

              <div class="container-fluid button-style butcenter">
                        <a href="#"  class="back"><?php echo lang('previous');?></a>
                        <a href="#" class="next"><?php echo lang('continue');?></a>
                    </div>
        </div>
        <div id="addC" class="relateDiv row">

            <p style="display: block;padding: 15px;width: 100%"><a href="<?php echo base_url();?>Page_Details/18" target="_blank"><?php echo lang('price_hint');?> </a></p>

            <?php foreach(array_reverse($countries) as $key=> $country){?>
                <input type="hidden" name="country_id[]" value="<?php echo $country->id; ?>" />
                <div class="col-12">
                    <div class="form-group">
                        <div class="form-item">
                            <label><?php echo $country->name; ?></label>
                            <div class="checkbox kuwait-div">
                                <label for="con_<?php echo $country->id; ?>">
                                    <?php
                                            $country_active = false;

                                             if($mode =='edit' && isset($products_countries[$id]) && in_array($country->id,$products_countries[$id]) && !isset($validation_msg))
                                             {
                                                $country_active = true;
                                             }

                                             if((isset($_POST["active"][$country->id]) )&& $_POST["active"][$country->id] == 1)
                                             {
                                                $country_active = true;
                                             }

                                             $show_country = array(
                                                            'name'           => "active[$country->id]"  ,
                                                            'class'          => 'form-control activate_country'  ,
                                                            'id'             => "price_tab_$country->id"        ,
                                                            'value'          => 1                               ,
                                                            'data-toggle'    => 'toggle'                        ,
                                                            'checked'        => set_checkbox("active[$country->id]", $country_active, $country_active)
                                                            );

                                            echo form_checkbox($show_country);
                                            echo form_error("active[$country->id]");
                                              ?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="price-coun">
                        <div class="form-group">
                            <div class="form-item">
                                <label><?php echo lang('currency');?></label>
                                <?php   echo form_error("currency[$country->id]");
                                       $currency_data = array('name'=>"currency[$country->id]" , 'class'=>"form-control" ,'readonly'=>'true' , 'value'=> isset($country->currency )? $country->currency  : set_value("currency[$country->id]"));
                                       echo form_input($currency_data);
                                  ?>
                            </div>
                        </div><!--form-group-->

                        <div class="form-group">
                            <div class="form-item">
                                <label><?php echo lang('price');?></label>
                                <?php
                                   echo form_error("price[$country->id]");
                                   $price_data = array('name'=>"price[$country->id]" , 'class'=>"form-control price_spinner_". $country->id , 'value'=> isset($products_countries_data[$country->id]->price)? $products_countries_data[$country->id]->price : set_value("price[$country->id]", 0));
                                   echo form_input($price_data);
                              ?>
                            </div>
                        </div><!--form-group-->


                    </div><!---->
                </div>
            <?php }?>

            <div class="container-fluid button-style butcenter">
              <a href="#" class="back"><?php echo lang('previous');?></a>
              <a href="#" class="next" ><?php echo lang('continue');?></a>
            </div>

        </div>
        <?php /*
        <div id="addD" class="relateDiv row">
            <h5 style="color :#f79d36;display: block;padding: 15px;width: 100%"><?php echo lang('product_sizes');?></h5>
                <div class="form-actions add_div">
            			<div class="row">
            				<div class="col-md-offset-3 col-md-9">
                      <button class="btn blue add_option"><i class="fa fa-plus"></i> <?php echo lang('add_option');?></button>
            				 </div>
            			</div>
                </div>

                <div class="container-fluid button-style butcenter">
                  <button class="back"><a href="#"><?php echo lang('previous');?></a></button>
                  <button class="next"><a href="#" ><?php echo lang('continue');?></a></button>
                </div>
             </div>
             */?>
        <div id="addD" class="relateDiv row m-0">
          <div class="form-body form-inline row w-100  m-0">
            <?php
            if(isset($product_optional_fields) && count($product_optional_fields) != 0){
                foreach($product_optional_fields as $field){?>
                    <div class="option_row">
                        <button class="btn btn-sm red filter-cancel btn-warning remove_option" value="<?php echo $field->id;?>" data-toggle="confirmation">
                            <!--<span><?php echo lang('delete');?></span>-->
                            <svg id="Layer_1" enable-background="new 0 0 512 512" height="512" viewBox="0 0 512 512" width="512" xmlns="http://www.w3.org/2000/svg"><g><path d="m424 64h-88v-16c0-26.467-21.533-48-48-48h-64c-26.467 0-48 21.533-48 48v16h-88c-22.056 0-40 17.944-40 40v56c0 8.836 7.164 16 16 16h8.744l13.823 290.283c1.221 25.636 22.281 45.717 47.945 45.717h242.976c25.665 0 46.725-20.081 47.945-45.717l13.823-290.283h8.744c8.836 0 16-7.164 16-16v-56c0-22.056-17.944-40-40-40zm-216-16c0-8.822 7.178-16 16-16h64c8.822 0 16 7.178 16 16v16h-96zm-128 56c0-4.411 3.589-8 8-8h336c4.411 0 8 3.589 8 8v40c-4.931 0-331.567 0-352 0zm313.469 360.761c-.407 8.545-7.427 15.239-15.981 15.239h-242.976c-8.555 0-15.575-6.694-15.981-15.239l-13.751-288.761h302.44z"/><path d="m256 448c8.836 0 16-7.164 16-16v-208c0-8.836-7.164-16-16-16s-16 7.164-16 16v208c0 8.836 7.163 16 16 16z"/><path d="m336 448c8.836 0 16-7.164 16-16v-208c0-8.836-7.164-16-16-16s-16 7.164-16 16v208c0 8.836 7.163 16 16 16z"/><path d="m176 448c8.836 0 16-7.164 16-16v-208c0-8.836-7.164-16-16-16s-16 7.164-16 16v208c0 8.836 7.163 16 16 16z"/></g></svg>
						</button>

                        <div class="row bg-gray">
                          <div class="col-md-6">
                            <div class="form-group" >
                              <div class="col-md-6">
                                 <label class="control-label"><?php echo lang('optional_fields');?></label>
                              </div>

                              <div class="col-md-6 no-padding">
                                <select class="form-control  option_id w-100" name="exist_option_id[]">
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
                          <?php /*<div class="col-md-6">
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
                          </div><!--col-->*/?>
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

                                            <img  width="70" height="70" src="<?php echo base_url().'assets/uploads/products/'.$item->image;?>" />
                                            <button  value="<?php echo $item->id;?>"  class="btn btn-sm red filter-cancel delete_alert delete-btn remove_op_image"><?php echo lang('delete');?></button>
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

                                echo form_dropdown('exist_sec_option_id['.$field->id.'][]', $secondary_optional_fields2, $sec_field_id, 'class="form-control w-100  sec_option_id" id=""');

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

                                    <img width="150" height="100" src="<?php echo base_url().'assets/uploads/products/'.$item->image;?>" />
                                    <button style="margin: 5px;" value="<?php echo $item->id;?>"  class="btn btn-sm red filter-cancel delete_alert delete-btn remove_op_image"><?php echo lang('delete');?></button>
                               </div>
                            <?php }else{?>
                                <input type="file" name="exist_op_image[<?php echo $field->optional_field_id.']['.$row->optional_field_id.']['.$item->optional_field_option_id;?>]" />
                            <?php }?>
                           </div>
                          </div>

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
              <div class="row">
                    <div class="col-md-10">
                        <div  class="row">
                            <div class="col-md-4" >
                                <div class="form-item">
                                    <label><?php echo lang('optional_fields');?><span></span></label>
                                    <select class="form-control option_id w-100" name="option_id[]">
                                        <option value="">-----------------</option>
                                        <?php foreach($optional_fields as $option){?>
                                            <option value="<?php echo $option->id;?>" data-has_value="<?php echo $option->has_value;?>" data-free="<?php echo $option->free;?>"><?php echo $option->label;?></option>
                                        <?php }?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-item">
                                    <label><?php echo lang('is_required'); ?></label>
                                    <div class="checkbox kuwait-div">
                                        <label for="req">
                                            <?php
                                                $required_value = true ;
                                                $required_data = array(
                                                            //'name'           => "required[]",
                                                            'class'          => 'form-control activate_country',
                                                            'data-on-color'  => 'danger',
                                                            'data-off-color' => 'default',
                                                            'value'          => 1,
                                                            'checked'        => set_checkbox("required", $required_value, $required_value),
                                                            'data-on-text'   => lang('yes'),
                                                            'data-off-text'  => lang('no'),
                                                            );
                                                echo form_checkbox($required_data);?>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
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
                        </div>
                    </div>
              
                    <div class="col-md-2">
                        <button class="btn btn-sm red filter-cancel btn-warning remove_option" data-toggle="confirmation">
                          <!-- <span><?php echo lang('delete');?></span>-->
                          <svg id="Layer_1" enable-background="new 0 0 512 512" height="512" viewBox="0 0 512 512" width="512" xmlns="http://www.w3.org/2000/svg"><g><path d="m424 64h-88v-16c0-26.467-21.533-48-48-48h-64c-26.467 0-48 21.533-48 48v16h-88c-22.056 0-40 17.944-40 40v56c0 8.836 7.164 16 16 16h8.744l13.823 290.283c1.221 25.636 22.281 45.717 47.945 45.717h242.976c25.665 0 46.725-20.081 47.945-45.717l13.823-290.283h8.744c8.836 0 16-7.164 16-16v-56c0-22.056-17.944-40-40-40zm-216-16c0-8.822 7.178-16 16-16h64c8.822 0 16 7.178 16 16v16h-96zm-128 56c0-4.411 3.589-8 8-8h336c4.411 0 8 3.589 8 8v40c-4.931 0-331.567 0-352 0zm313.469 360.761c-.407 8.545-7.427 15.239-15.981 15.239h-242.976c-8.555 0-15.575-6.694-15.981-15.239l-13.751-288.761h302.44z"/><path d="m256 448c8.836 0 16-7.164 16-16v-208c0-8.836-7.164-16-16-16s-16 7.164-16 16v208c0 8.836 7.163 16 16 16z"/><path d="m336 448c8.836 0 16-7.164 16-16v-208c0-8.836-7.164-16-16-16s-16 7.164-16 16v208c0 8.836 7.163 16 16 16z"/><path d="m176 448c8.836 0 16-7.164 16-16v-208c0-8.836-7.164-16-16-16s-16 7.164-16 16v208c0 8.836 7.163 16 16 16z"/></g></svg>    
                        </button>
                    </div>
              </div> 

          

           </div>
         <?php }?>

           <div class="form-actions add_div" style="margin-top: 15px;">
             <button class="main-button"><i class="fa fa-plus"></i> <?php echo lang('add_option');?></button>
           </div>
         </div>

         <div class="container-fluid button-style butcenter">
           <a href="#" class="back"><?php echo lang('previous');?></a>
           <a href="#" class="next"><?php echo lang('continue');?></a>
         </div>

        </div>

        <div id="addE" class="relateDiv row">
            <h5 style="color :#f79d36;display: block;padding: 15px;width: 100%"><?php echo lang('multi_upload');?></h5>


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
            <div class="col-12 col-sm-12">
                <div class="form-group col-sm-6">
                    <div class="form-item">
                        <label><?php echo lang('product_images');?></label>
                        <?php
                            $multi_data = array('name'=>"files[]", "multiple"=>true, 'class'=>"form-control", 'accept'=>'.gif,.jpeg,.jpg,.png,.tiff' );
                            echo form_upload($multi_data);
                        ?>
                    </div>
                </div>



            </div>
             <div class="container-fluid button-style butcenter">
                  <a href="#" class="back"><?php echo lang('previous');?></a>
                  <?php /*<button class="finish"><?php echo lang('finish');?></button>*/?>
                  <input type="submit" class="finish" value="<?php echo lang('finish');?>" />
              </div>
        </div>


    </div>
      <?php  echo isset($id) ? form_hidden('product_id', $id) : ''; ?>
       <input type="hidden" name="sell" value="1" />

        <?php echo form_close();?>
</div>

<script>
/**********Show store cats****************************************/
$(document).ready(function() {
     /*next button*/
    $(".next ").click(function(e){
        e.preventDefault();

        var attrbefore = $(".list .relate a[class='active']").attr('href').replace('#','');
        $(".list .relate a[class='active']").removeClass('active').parent().next().children().addClass("active");
         var attrafter = $(".list .relate a[class='active']").attr('href').replace('#','');
         $(".add div[id='"+attrbefore+"']").css("display","none");
         $(".add div[id='"+attrafter+"']").css("display","flex");

         if(attrbefore == null){
             alert("hello");
         }


    });
    /*back button*/
    $(".back ").click(function(e){
        e.preventDefault();
        var attrbefore = $(".list .relate a[class='active']").attr('href').replace('#','');
        $(".list .relate a[class='active']").removeClass('active').parent().prev().children().addClass("active");
         var attrafter = $(".list .relate a[class='active']").attr('href').replace('#','');
         $(".add div[id='"+attrbefore+"']").css("display","none");
         $(".add div[id='"+attrafter+"']").css("display","flex");


    });


  $('#store_id').on('change', function(){

    var postData = {
                      store_id : $('#store_id').val()
                   };

    $.post('<?php echo base_url().'products/admin_products/get_store_cats';?>', postData, function(result){
        $('#available_cats').html(result);
    });

  });
});
/*************************************************/
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

var upload_a_file_string = "Upload a file";
</script>

<script>
/*
$(".finish").click(function(){

    $( "#main_form" ).submit();
    });
    */
    /*$(document).ready(function() {
      $(".finish").click(function() {
        postData = $( "#main_form" ).serialize(),
        console.log(postData);

        $.post($(location).attr("href"), postData, function(result){
           alert(result);
        });
      });
  });*/
</script>

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

    var optionRow = '<div class="option_row"><button class="btn btn-sm red filter-cancel btn-warning remove_option" data-toggle="confirmation"><i class="fa fa-times"></i><span><?php echo lang('delete');?></span></button><div class="form-group"><div class="col-md-2"><div class="col-md-12"><label class="control-label"><?php echo lang('optional_fields');?></label></div></div><div class="col-md-4 no-padding"><select class="form-control  w-100" name="option_id[]"><option value="">-----------------</option><?php foreach($optional_fields as $option){?><option value="<?php echo $option->id;?>" data-has_value="<?php echo $option->has_value;?>" data-has_options="<?php echo $option->has_options;?>" data-free="<?php echo $option->free;?>"><?php echo $option->label;?></option><?php }?></select></div></div><div class="form-group"><div class="col-md-2"><div class="col-md-12"><label class="control-label"><?php echo lang('is_required');?></label></div></div><div class="col-md-4 no-padding"><input name="required[]" type="checkbox" class="make-switch required_field" data-on-color="danger" data-off-color="default" value=1 checked= <?php set_checkbox("required", "true", "true");?> data-on-text=<?php echo lang('yes');?> data-off-text= <?php echo lang('no');?>/></div></div><div class="form-group value_div" style="display: none;"><div class="col-md-2"><div class="col-md-12"><label class="control-label"><?php echo lang('value');?></label></div></div><div class="col-md-4 no-padding"><input name="value[]" class="form-control" value="<?php set_value('value');?>" /></div></div><div class="form-group cost_div" style="display: none;"><label class="control-label"><?php echo lang('cost');?></label></div></div>';

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
//Remove img
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

</script>
