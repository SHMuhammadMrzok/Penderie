<?php $selected_op_cost = 0;?>
<link href="<?php echo base_url();?>assets/template/rating.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?php echo base_url();?>assets/template/rating.js"></script>

<script language="javascript" type="text/javascript">
$(function() {
    $("#rating_star").codexworld_rating_widget({
        starLength: '5',
        initialValue: '',
        callbackFunctionName: 'processRating',
        imageDirectory: 'images/',
        inputAttr: 'postID'
    });
});

function processRating(val, attrVal){
    $.ajax({
        type: 'POST',
        url: '<?php echo base_url();?>products/products/add_rate/',
        data: 'product_id='+'<?php echo $product->id;?>'+'&ratingPoints='+val,
        dataType: 'json',
        success : function(data) {
            //alert(data);
            if(data[0] == 'login')
            {
                window.location = '<?php echo base_url().'User_login';?>';
            }
            else if(data[0] == 'rated_before')
            {
                $('.codexworld_rating_widget').html('<?php echo lang('you_rated_product_before');?>');

            }
            else if(data[0] == 'buy_first')
            {
                $('.codexworld_rating_widget').html('<?php echo lang('buy_product_first');?>');
            }
            else
            {
                $('#avgrat').text(data.average_rating);
                $('#totalrat').text(data.rating_number);
            }
        }
    });
}
<?php if(!$this->data['is_logged_in']){?>
  $( "body" ).on( "click", "#add-review-tab", function(event){
    event.preventDefault();
    window.location="<?php echo base_url().'User_login';?>"
  });
<?php }?>
</script>

<div class="breadcrumb">
  <div class="container">
    <div class="breadcrumb-inner">
      <ul class="list-inline list-unstyled">
        <li><a href="<?php echo base_url();?>" title="<?php echo lang('home');?>"><?php echo lang('home');?></a></li>
        <?php if(count($parent_data) != 0){?>
          <li><a href="<?php echo base_url().$main_category_route.$parent_data->route;?>/0"><?php echo $parent_data->name;?></a></li>
        <?php }?>
        <li class="active"><?php echo $cat_data->name;?></li>

      </ul>
    </div>
  </div>
</div>
<main>
  <section class="about-product">

    <div class='container'>
      <div class="row">
        <div class="col-md-8 rht-col">
          <div class="detail-block">
            <h1 class="name"><?php echo $product->title;?></h1>
            <div class="rating-reviews">
              <div class="row">

                <div class="pull-left" style="margin-left:17px;">
                  <div class="rated">
                    <ul>
                      <?php $avg_product_rate = intval($product->rating_avg);
                      $remain = 5 - $avg_product_rate ;
                      for($x = 0 ; $x < $avg_product_rate ; $x ++) { ?>
                        <li class="active">
                          <svg>
                            <use xlink:href="#star"></use>
                          </svg>
                        </li>
                      <?php }?>
                      <?php for($y = 0 ; $y < $remain ; $y ++) {?>
                        <li>
                          <svg>
                            <use xlink:href="#star"></use>
                          </svg>
                        </li>
                      <?php }?>
                      <li>
                        <span>(<?php echo $product->rating_times;?>)</span>
                      </li>
                    </ul>
                  </div>

                  <?php /*<div class="box-review form-group">
                    <div class="ratings">
			                  <div class="rating-box">
                            <input name="rating" value="0" id="rating_star" type="hidden" postID="1" />
                            <div class="overall-rating">
                                ( <?php echo lang('avg_rating');?>
                                <span id="avgrat"> <?php echo $product->rating_avg;?> </span>
                                <?php echo lang('base_on');?>
                                <span id="totalrat"> <?php echo $product->rating_times; ?> </span>
                                <?php echo lang('rating');?> ) </span>
                            </div>
                        </div>
                    </div>
                </div>
                */?>



                </div>
                <?php /*<div class="pull-left">
                  <div class="reviews">
                    <a href="#" title="#" class="lnk">(<?php echo $product->rating_times;?>)</a>
                  </div>
                </div>
                */?>

              </div>
            </div>
            <div class="row">

              <div class="col-md-7 flex-row position-relative">
                  <div class="product-slider-container">
                      <div class="slider slider-single">
                        <img class="product-v-img" src="<?php echo $images_path . $product->image;?>">
                        <?php if($product->youtube_video != ''){?>
                          <iframe class="product-v-img" width="100%" height="315" src="https://www.youtube.com/embed/<?php echo $product->youtube_video;?>" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        <?php }?>
                        <?php if($product->video != ''){?>
                          <video width="100%" controls>
                              <source src="<?php echo $images_path . $product->video;?>" type="video/mp4">
                              Your browser does not support HTML5 video.
                          </video>
                        <?php }?>

                          <?php if(count($product_images) != 0){
                              foreach($product_images as $i=>$image){ ?>
                                <img class="product-v-img" src="<?php echo $this->data['images_path'].$image->image;?>">
                            <?php }
                          }

                          if(isset($optional_fields_images) && count($optional_fields_images) != 0){
                            foreach($optional_fields_images as $key=>$op_image){?>
                              <img class="product-v-img" src="<?php echo $this->data['images_path']. $op_image;?>" data-slide-target="<?php echo $key;?>">
                          <?php }
                        }?>

                      </div>

                      <div class="slider slider-nav my-3">
                          <img class="product-v-img" src="<?php echo $images_path . $product->image;?>">
                          <?php if($product->youtube_video != ''){?>
                            <img class="product-v-img" src="https://i.ytimg.com/vi/<?php echo $product->youtube_video;?>/hqdefault.jpg">
                          <?php }?>
                          <?php if($product->video != ''){?>
                            <img class="product-v-img" src="<?php echo $images_path . $product->video;?>">
                          <?php }?>

                          <?php if(count($product_images) != 0){
                              foreach($product_images as $i=>$image){ ?>
                                <img class="product-v-img" src="<?php echo $this->data['images_path']. $image->image;?>">
                            <?php }
                          }
                          if(isset($optional_fields_images) && count($optional_fields_images) != 0){
                            foreach($optional_fields_images as $op_image){?>
                              <img class="product-v-img" src="<?php echo $this->data['images_path']. $op_image;?>" >
                          <?php }
                          }
                          ?>

                      </div>
                  </div>
                  <div id="zoom-container"></div>
              </div>

              <?php /*
              <div class="col-md-7 flex-row position-relative">
                  <div class="product-slider-container">
                      <div class="slider slider-single">
                          <img class="product-v-img" src="<?php echo base_url();?>assets/uploads/products/<?php echo $product->image;?>">
                          <?php if(count($product_images) != 0){
                              foreach($product_images as $i=>$image){ ?>
                                <img class="product-v-img" src="<?php echo base_url();?>assets/uploads/products/<?php echo $image->image;?>">
                            <?php }
                          }?>

                      </div>
                      <div class="slider slider-nav my-3">
                        <img class="product-v-img" src="<?php echo base_url();?>assets/uploads/products/<?php echo $product->image;?>">
                        <?php if(count($product_images) != 0){
                            foreach($product_images as $i=>$image){ ?>
                              <img class="product-v-img" src="<?php echo base_url();?>assets/uploads/products/<?php echo $image->image;?>">
                          <?php }
                        }?>
                      </div>
                  </div>
                  <div id="zoom-container"></div>
              </div>
              */?>

              <div class="col-md-5">
                <div class="product-info-block">
                  <div class="product-info">
                    <p class="price">
                      <span class="new-price product_price"><?php echo $product->price + $selected_op_cost;?> </span>
                      <span><?php echo $product->currency;?></span>
                      <?php if($product->price_before != $product->price){?>
                        <span class="old-price"><?php echo $product->price_before."  ".$product->currency;?></span>
                      <?php }?>
                    </p>

                    <?php if(isset($product->rest_qty) && $product->rest_qty != 0){?>
                      <div class="alert-note">
                        <p><?php echo lang('order_now_note');?> <?php echo $product->rest_qty;?> <?php echo lang('left_in_stock');?>!</p>
                      </div>
                    <?php }?>

                    <div class="row m-0">
                      <div class="stock-container">
                        <div class="row m-0">
                          <div class="pull-left">
                            <div class="stock-box">
                              <?php echo lang('cat_name');?> :
                            </div>
                          </div>
                          <div class="pull-left">
                            <div class="stock-box">
                              <a href="<?php echo base_url().$sub_category_route.$cat_data->route;?>/0"><?php echo $cat_data->name;?></a>
                            </div>
                          </div>
                        </div>

                        <?php if($product->brand_name != ''){?>
                          <div class="row m-0">
                            <div class="pull-left">
                              <div class="stock-box">
                                <?php echo lang('brand_name');?> :
                              </div>
                            </div>
                            <div class="pull-left">
                              <div class="stock-box">
                                <a href="<?php echo base_url().'products/brand_products/'.$product->brand_id.'/1';?>"><?php echo $product->brand_name;?></a>
                              </div>
                            </div>
                          </div>
                        <?php }?>

                        <?php if($product->code != ''){?>
                          <div class="row m-0">
                            <div class="pull-left">
                              <div class="stock-box">
                                <?php echo lang('code');?> :
                              </div>
                            </div>
                            <div class="pull-left">
                              <div class="stock-box">
                                <?php echo $product->code;?>
                              </div>
                            </div>
                          </div>
                        <?php }?>

                        <?php if($product->reward_points != 0){?>
                           <div class="row m-0">
                            <div class="pull-left">
                              <div class="stock-box">
                                <?php echo lang('reward_points');?> :
                              </div>
                            </div>
                            <div class="pull-left">
                              <div class="stock-box">
                                <?php echo $product->reward_points;?>
                              </div>
                            </div>
                          </div>
                        <?php }?>


                        <div class="row m-0">

                          <div class="pull-left">
                            <div class="stock-box">
                              <?php echo lang('availability_status');?> :
                            </div>
                          </div>
                          <div class="pull-left">
                            <div class="stock-box">
                              <?php echo $product->availability;?>
                            </div>
                          </div>
                        </div>

                      </div>

                    </div>
                  </div>
                  <?php if(isset($product_optional_fields) && count($product_optional_fields) != 0){ ?>

                  <form id="optional_fields_form" enctype="multipart/form-data">
                      <?php

                      foreach ($product_optional_fields as $field){
                        $required       = '';
                        $required_span  = '';

                        if($field->required == 1)
                        {
                            $required       = 'required';
                            $required_span  = " <span class='required' style='color: red'> * </span>";
                        }
                        ?>
                        <div class="color-area">
                          <p><?php echo $field->label;?></p>
                        <?php
                        if($field->field_type_id == 2){ // radio?>
                          <ul class="slider slider-nav2 colors-indicators">
                            <?php foreach($field->options as $key=>$option){?>
                                <li id="item-<?php echo $key+1;?>">
                                    <div class="color " title="<?php echo $option->field_value;?>" id="<?php echo $option->id;?>">
                                      <?php if($option->image != ''){?>
                                        <img src="<?php echo $this->data['images_path']. $option->image;?>" alt="<?php echo $option->field_value;?>" />
                                      <?php }else{?>
                                        <img src="<?php echo $images_path.$this->config->item('logo');?>" alt="<?php echo $product->title;?>"/>
                                      <?php }?>
                                      <input type="radio" name="optional_field[<?php echo $field->id;?>]" value="<?php echo $option->id;?>" <?php echo $required;?> <?php echo $key==0?'selected':'';?>>
                                    </div>
                                </li>

                            <?php }?>
                          </ul>

                    <?php }
                    else if($field->field_type_id == 3) //check box
                    {
                       foreach($field->options as $key=>$option)
                       {
                           if($key == 0){ $selected_op_cost = $option->cost;}?>
                           <div class="area-checkbox2">
                             <label class="checkbox">
                                 <input type="checkbox" class="op_cost op_c_<?php echo $field->id.'_'.$key;?>" data-op_cost="<?php echo $option->cost;?>" data-op_index="<?php echo $key;?>" data-op_id="<?php echo $field->id;?>" name="optional_field[<?php echo $field->id;?>][<?php echo $key;?>]" value="<?php echo $option->id;?>" <?php echo $required;?> <?php echo $key==0 ? 'checked':'';?> />
                                 <span></span>
                             </label>
                              <span class="name-radio">
                                  <?php //echo $required_span;?><?php echo $option->field_value;?>
                              </span>

                            <?php if($option->cost != 0){?>
                              <span class="name-radio">
                                ( <?php echo lang('cost').' : '.$option->cost.' '.$product->currency;?> )
                              </span>
                          <?php }?>
                        </div>

                        <?php if($field->has_qty == 1){?>
                           <div class="row">
                               <label class="control-label"><?php echo lang('quantity');?></label>
                               <input type="number" name="op_qty[<?php echo $field->id;?>][<?php echo $key;?>]" value="1" min="1" class="op_qty_<?php echo $field->id.'_'.$key;?> op_q" data-op_cost="<?php echo $option->cost;?>" data-op_index="<?php echo $key;?>" data-op_id="<?php echo $field->id;?>" <?php echo $key!=0 ? 'disabled="true"' : '';?>/>
                           </div>
                      <?php }?>
                    <?php }
                    }
                    else if($field->field_type_id == 8) //select
                    {?>

                      <select class="form-control" name="optional_field[<?php echo $field->id;?>]" <?php echo $required;?>>
                          <?php foreach($field->options as $option){?>
                              <option value="<?php echo $option->id;?>"><?php echo $option->field_value;?></option>
                          <?php }
                       ?>
                      </select>

                    <?php
                    }
                    else if($field->field_type_id == 9) //file
                    {?>


                       <form id="fileupload" action="<?php echo base_url();?>uploadHandler2/do_upload" method="POST" enctype="multipart/form-data">
                           <!-- Redirect browsers with JavaScript disabled to the origin page -->
                           <noscript><input type="hidden" name="redirect" value="https://blueimp.github.io/jQuery-File-Upload/"></noscript>
                           <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
                           <div class="row fileupload-buttonbar">
                               <div class="col-lg-7">
                                   <!-- The fileinput-button span is used to style the file input field as button -->
                                   <span class="btn btn-success fileinput-button">
                                       <i class="glyphicon glyphicon-plus"></i>
                                       <span><?php echo lang('add_files');?>...</span>
                                       <input type="file" name="files[]" accept=".gif,.jpeg,.jpg,.png,.tiff,.doc,.docx,.txt,.odt,.xls,.xlsx,.pdf,.ppt,.pptx,.pps,.ppsx,.mp3,.m4a,.ogg,.wav,.mp4,.m4v,.mov,.wmv,.flv,.avi,.mpg,.ogv,.3gp,.3g2" multiple >
                                   </span>
                                   <button type="submit" class="btn btn-primary start">
                                       <i class="glyphicon glyphicon-upload"></i>
                                       <span><?php echo lang('start_upload');?></span>
                                   </button>
                                   <button type="reset" class="btn btn-warning cancel">
                                       <i class="glyphicon glyphicon-ban-circle"></i>
                                       <span><?php echo lang('cancel_upload');?></span>
                                   </button>
                                   <button type="button" class="btn btn-danger delete">
                                       <i class="glyphicon glyphicon-trash"></i>
                                       <span><?php echo lang('delete');?></span>
                                   </button>
                                   <input type="checkbox" class="toggle" />
                                   <!-- The global file processing state -->
                                   <span class="fileupload-process"></span>
                               </div>
                               <!-- The global progress state -->
                               <div class="col-lg-5 fileupload-progress fade">
                                   <!-- The global progress bar -->
                                   <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                                       <div class="progress-bar progress-bar-success" style="width:0%;"></div>
                                   </div>
                                   <!-- The extended global progress state -->
                                   <div class="progress-extended">&nbsp;</div>
                               </div>
                           </div>
                           <!-- The table listing the files available for upload/download -->
                           <table role="presentation" class="table table-striped"><tbody class="files"></tbody></table>
                       </form>


                       <!-- The template to display files available for download -->
                       <script id="template-download" type="text/x-tmpl">
                       {% for (var i=0, file; file=o.files[i]; i++) { %}
                           <tr class="template-download fade">
                               <td>
                                   <span class="preview">
                                       {% if (file.thumbnailUrl) { %}
                                           <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery>      <img src="{%=file.thumbnailUrl%}" alt=""/>
         </a>
                                       {% } %}
                                   </span>
                               </td>
                               <td>
                                   <p class="name">
                                       {% if (file.url) { %}
                                           <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>
                                       {% } else { %}
                                           <span>{%=file.name%}</span>
                                       {% } %}
                                   </p>
                                   {% if (file.error) { %}
                                       <div><span class="label label-danger">Error</span> {%=file.error%}</div>
                                   {% } %}
                               </td>
                               <td>
                                   <span class="size">{%=o.formatFileSize(file.size)%}</span>
                               </td>
                               <td>
                                   {% if (file.deleteUrl) { %}
                                       <button class="btn btn-danger delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
                                           <i class="glyphicon glyphicon-trash"></i>
                                           <span><?php echo lang('delete');?></span>
                                       </button>
                                       <input type="hidden" name="optional_field[<?php echo $field->id;?>][]" value={%=file.name%} />
                                       <input type="checkbox" name="delete" value="1" class="toggle">
                                   {% } else { %}
                                       <button class="btn btn-warning cancel">
                                           <i class="glyphicon glyphicon-ban-circle"></i>
                                           <span>Cancel</span>
                                       </button>
                                   {% } %}
                               </td>
                           </tr>
                       {% } %}
                       </script>


                    <?php }
                    else
                    {?>

                       <input name="optional_field[<?php echo $field->id;?>]" <?php echo $required;?> type="<?php echo $field->type_name;?>" class="input-form form-control" placeholder="<?php echo $field->default_value;?>" <?php echo ($field->field_type_id ==10) ? 'step="any"' : '';?> />

                    <?php
                    }
                    echo form_error('optional_field['.$field->id.']');

                  }?>
                  </div>
                  <input type="hidden" name="product_id" value="<?php echo $product->product_id;?>" />

                  <p><?php echo lang('choose_quantity');?></p>
                  <div class="quant">


                          <div class="numbers-row">
                              <input type="text" id="partridge" placeholder="1" value="1" name="product_qty" class="form-control product_qty"/>
                          </div>


                  </div>
                </form>
                    <?php } ?>

                </div>
                <?php /*
                <div class="size-area">
                  <p>Size</p>
                  <ul>
                   <li>
                     <div class="size" title="Red">
                        36 EUR
                       <input type="checkbox" name="sizr">
                     </div>
                   </li>

                   <li>
                     <div class="size" title="Red">
                        38 EUR
                       <input type="checkbox" name="sizr">
                     </div>
                   </li>
                   </ul>
               </div>
               */?>
                <div class="quantity-card">
                  <?php if(isset($optional_field) && count($optional_field) != 0){?>
                    <p><?php echo lang('choose_quantity');?></p>
                    <div class="quant">
                        <form method="post" action="#">

                            <div class="numbers-row">
                                <input type="text" id="partridge" placeholder="1" value="1" name="product_qty" class="form-control product_qty"/>
                            </div>

                        </form>
                    </div>
                  <?php }?>

                    <div class="add-to-card-button">
                        <a href="#" data-product_id="<?php echo $product->product_id;?>" class="<?php echo (isset($product_optional_fields) && count($product_optional_fields) != 0) ? 'add_optional_fields add-cart-bt' : 'cart';?>">
                          <svg>
                              <use xlink:href="#shopping-cart"></use>
                          </svg>
                          <span><?php echo lang('add_to_cart');?></span>
                        </a>
                      </div>
                </div>

                <?php /*<div class="other-offers">
                    <p>
                        <svg>
                            <use xlink:href="#offer"></use>
                        </svg>
                        <span>
                           2 other offers from <b>SAR 4081.00 </b> <a href="#">View All Offers</a>
                        </span>
                    </p>
                </div>
                */?>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-4">

          <?php
          /*<div class="location">
            <p>
              <svg>
                  <use xlink:href="#pin"></use>
              </svg>
              <span>
                  Ship to Cairo <a href="#">Change City</a><br />
                  Delivered by <b>Saturday, Jul 6</b></p>
              </span>
          </div>
          */?>
          <?php if($product->is_returned == 1){?>
            <div class="returns">
              <p>
                  <svg>
                      <use xlink:href="#returns"></use>
                    </svg>
                <span><?php echo lang('return_note');?></span></p>
            </div>
          <?php }?>

          <?php if($product->store_name != '' && $this->config->item('business_type') == 'b2b'){?>
            <div class="reseller">
                <p>
                  <svg>
                      <use xlink:href="#store"></use>
                  </svg>
                  <span> <?php echo lang('name_of_store');?> <a href="<?php echo base_url().'Store_details/'.$product->store_id;?>"><?php echo $product->store_name;?></a> </span>
                </p>
            </div>
          <?php }?>
          <div class="secure-shopping">
              <p>
                <svg>
                    <use xlink:href="#secure"></use>
                </svg>
                <span><?php echo lang('protected_data_note');?></span>
              </p>
          </div>
            <div class="buy-now">
               <a href="#" class="<?php echo (isset($product_optional_fields) && count($product_optional_fields) != 0) ? 'buy_optional_fields' : 'buy_now';?>" data-product_id="<?php echo $product->id;?>"><?php echo lang('buy_now');?></a>
            </div>
            <div class="wishlist-compare">
                <a class="wishlist_product" title="<?php echo lang('add_to_wishlist');?>" href="#" data-product_id="<?php echo $product->id;?>">
                  <svg>
                    <use xlink:href="#wishlist"></use>
                  </svg>
                  <span><?php echo lang('add_to_wishlist');?></span>
                </a>

                <?php /*<a class="compare_products" title="<?php echo lang('add_to_compare_product');?>" href="#" data-product_id="<?php echo $product->id;?>">
                  <svg>
                    <use xlink:href="#compare"></use>
                  </svg>
                  <span><?php echo lang('add_to_compare_product');?></span>
                </a>
                */?>
              </div>


              <div id="fb-root"></div>
              <script>(function(d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) return;
                js = d.createElement(s); js.id = id;
                js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.10";
                fjs.parentNode.insertBefore(js, fjs);
              }(document, 'script', 'facebook-jssdk'));</script><!-- Facebook share-->

              <div class="share social">
                <ul class="link">
                  <li class="fb pull-left">
                    <div class="fb-share-button" data-href="<?php echo base_url().$product_route.$product->route;?>" data-layout="button" data-size="small" data-mobile-iframe="true"><a class="fb-xfbml-parse-ignore" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=http%3A%2F%2Fsbmcart.com%2FLareene%2Fproduct%2Fskirt5&amp;src=sdkpreparse">Share</a></div>
                    <?php /*<a target="_blank" href="#" title="Facebook">
                      <svg>
                        <use xlink:href="#facebook"></use>
                      </svg>
                    </a>
                    */?>
                  </li>
                  <li class="tw pull-left">
                    <a href="https://twitter.com/share?ref_src=twsrc%5Etfw" class="twitter-share-button" data-show-count="false">Tweet</a><script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>
                    <?php /*<a target="_blank" href="#" title="Twitter">
                      <svg>
                        <use xlink:href="#twitter"></use>
                      </svg>
                    </a>*/?>
                  </li>

                </ul>
            </div>

        </div>
      </div>
    </div>
  </section>

  <section class="tabs-product-area">
    <div class="container">
      <div class="row">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
          <li class="nav-item">
            <a class="nav-link active" id="description-tab" data-toggle="tab" href="#description" role="tab" aria-controls="description"
              aria-selected="true"><?php echo lang('detials');?></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="reviews-tab" data-toggle="tab" href="#reviews" role="tab" aria-controls="reviews"
              aria-selected="false"><?php echo lang('customers_review');?></a>
          </li>

          <li class="nav-item">
              <a class="nav-link" id="add-review-tab" data-toggle="tab" href="#add-review" role="tab" aria-controls="add-review-"
                aria-selected="false"><?php echo lang('add_review');?></a>
            </li>

        </ul>
        <div class="tab-content w-100" id="myTabContent">
          <div class="tab-pane fade w-100 show active" id="description" role="tabpanel" aria-labelledby="description-tab">

            <div class="row m-0">
              <div class="descrip w-100">

                <article class="text">
                  <ul>
                    <li>
                      <?php echo str_replace("\r\n", '</li><li>', $product->description) ;?>
                    </li>
                  </ul>
                  <?php /*echo substr($product->description, 0, 1000);?>
                  <?php if(strlen($product->description) > 1000){?>
                    <span id="dots">...</span><span id="more"><?php echo substr($product->description, 1000);?></span>
                    <button onclick="myFunction()" id="myBtn" class="see-more">Read more</button>
                  <?php }*/?>
                </article>

              </div>
            </div>

          </div>
          <div class="tab-pane fade w-100" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
            <div class="comments-block">

              <div class="row">
                <div class="col-md-4">
                  <div class="rate-block-bar">
                    <h1>
                      <p><?php echo $product->rating_avg;?></p>
                      <div class="rated">
                        <ul>
                          <?php $avg_product_rate = intval($product->rating_avg);
                          $remain = 5 - $avg_product_rate ;
                          for($x = 0 ; $x < $avg_product_rate ; $x ++) { ?>
                            <li class="active">
                              <svg>
                                <use xlink:href="#star"></use>
                              </svg>
                            </li>
                          <?php }?>
                          <?php for($y = 0 ; $y < $remain ; $y ++) {?>
                            <li>
                              <svg>
                                <use xlink:href="#star"></use>
                              </svg>
                            </li>
                          <?php }?>
                          <li>
                            <span>(<?php echo $product->rating_times;?>)</span>
                          </li>
                        </ul>
                      </div>
                    </h1>
                    <div class="rate-bars">
                      <ul>
                        <li>
                          <p>
                            1 Star
                          </p>
                          <div class="bar">
                            <div class="fill" style="width: <?php echo isset($rating_data[1])?$rating_data[1]->rate_percent:0;?>%;" title="(<?php echo isset($rating_data[1])?$rating_data[1]->rating_times:0;?>)"></div>
                          </div>
                        </li>

                        <li>
                          <p>
                            2 Star
                          </p>
                          <div class="bar">
                            <div class="fill" style="width: <?php echo isset($rating_data[2])?$rating_data[2]->rate_percent:0;?>%;" title="(<?php echo isset($rating_data[2])?$rating_data[2]->rating_times:0;?>)"></div>
                          </div>
                        </li>


                        <li>
                          <p>
                            3 Star
                          </p>
                          <div class="bar">
                            <div class="fill" style="width: <?php echo isset($rating_data[3])?$rating_data[3]->rate_percent:0;?>%;" title="(<?php echo isset($rating_data[3])?$rating_data[3]->rating_times:0;?>)"></div>
                          </div>
                        </li>


                        <li>
                          <p>
                            4 Star
                          </p>
                          <div class="bar">
                            <div class="fill" style="width: <?php echo isset($rating_data[4])?$rating_data[4]->rate_percent:0;?>%;" title="(<?php echo isset($rating_data[4])?$rating_data[4]->rating_times:0;?>)"></div>
                          </div>
                        </li>

                        <li>
                          <p>
                            5 Star
                          </p>
                          <div class="bar">
                            <div class="fill" style="width: <?php echo isset($rating_data[5])?$rating_data[5]->rate_percent:0;?>%;" title="(<?php echo isset($rating_data[5])?$rating_data[5]->rating_times:0;?>)"></div>
                          </div>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>


              </div>


              <div class="other-comments">
                <?php if(count($product_comments) != 0){
                    foreach($product_comments as $row){?>
                      <div class="comment-row">
                      <h4><?php echo $row->username;?> </h4>
                      <?php /*<div class="rated">
                        <ul>
                          <li class="active">
                            <svg>
                              <use xlink:href="#star"></use>
                            </svg>
                          </li>
                          <li class="active">
                            <svg>
                              <use xlink:href="#star"></use>
                            </svg>
                          </li>
                          <li class="active">
                            <svg>
                              <use xlink:href="#star"></use>
                            </svg>
                          </li>
                          <li class="active">
                            <svg>
                              <use xlink:href="#star"></use>
                            </svg>
                          </li>
                          <li>
                            <svg>
                              <use xlink:href="#star"></use>
                            </svg>
                          </li>

                        </ul>
                      </div>*/?>
                      <p><?php echo date('Y-m-d H:i', $row->unix_time);?></p>
                      <article>
                        <?php echo $row->comment;?>
                      </article>
                    </div>
                    <?php }
                  }?>

              </div>

            </div>
          </div>

          <div class="tab-pane fade w-100" id="add-review" role="tabpanel" aria-labelledby="add-review-tab">
              <div class="comments-block w-100">

                <div class="write-comment w-100">
                  <form action="<?php echo base_url();?>products/products/add_product_comment" method="post">
                      <div class="box-review form-group">
                        <div class="ratings">
    			                  <div class="rating-box">
                                <input name="rating" value="0" id="rating_star" type="hidden" postID="1" />
                                <div class="overall-rating">
                                    ( <?php echo lang('avg_rating');?>
                                    <span id="avgrat"> <?php echo $product->rating_avg;?> </span>
                                    <?php echo lang('base_on');?>
                                    <span id="totalrat"> <?php echo $product->rating_times; ?> </span>
                                    <?php echo lang('rating');?> ) </span>
                                </div>
                            </div>
                        </div>
                    </div>

                  <div class="form-group">
                      <label><?php echo lang('name');?></label>
                      <input type="text" name="username" class="form-control" placeholder="<?php echo lang('name');?>" required="required" />
                    </div>

                    <div class="form-group">
                      <label><?php echo lang('add_review');?></label>
                      <textarea placeholder="<?php echo lang('add_review');?>" name="comment" class="form-control" required="required"></textarea>
                    </div>

                    <input type="hidden" name="product_id" value="<?php echo $product->id;?>" />
                    <input type="hidden" name="route" value="<?php echo $product->route;?>" />

                    <div class="form-group">
                      <button class="button w-auto ml-auto " style="min-width: 100px"><?php echo lang('send');?></button>
                    </div>
                  </form>
                </div>

              </div>
            </div>
        </div>
      </div>
    </div>
  </section>

  <?php if(isset($product_cat_specs) && count($product_cat_specs) != 0){?>
    <section class="featchers-area-table">
        <div class="container">
            <div class="row no-gutters">
                <div class="title">
                    <h1> <?php echo lang('specifications');?> </h1>
                </div>
            </div>
            <div class="row no-gutters">
              <table class="table table-bordered">
                <thead>
                  <tr>
                      <th width="30%"><?php echo lang('the_feature');?></th>
                      <th width="70%"><?php echo lang('value');?></th>

                  </tr>
                </thead>
                <tbody>
                  <?php foreach($product_cat_specs as $key=>$row){?>
                  <tr>
                    <td width="30%"><?php echo $row->spec_label;?> </td>
                    <td width="70%"><?php echo $row->spec_value;?></td>
                  </tr>
                <?php }?>

                </tbody>
              </table>
            </div>
        </div>
    </section>
  <?php }?>


  <?php if(count($related_products) != 0){?>
    <section class="related-area">
    <div class="container">
      <div class="row no-gutters">
        <div class="title">
          <h1> <?php echo lang('related_products');?> </h1>
        </div>
      </div>

      <div class="row">
        <div class="related-slider">
            <div class="loop owl-carousel owl-theme">
              <?php foreach($related_products as $key=>$r_product){
                 echo $r_product;
              }?>
            </div>
        </div>
      </div>
    </div>
  </section>
<?php }?>

<?php /*
  <section class="customers-viewed">
     <div class="container">
        <div class="row">
           <div class="title">
              <h1>Customers Also Viewed</h1>
           </div>
        </div>
        <div class="row">
          <div class="customers-slider">
              <div class="loop owl-carousel owl-theme">

                 <div class=item>
                      <div class=product-container>
                          <div class=images-product>
                              <img src=assets/images/item-8.jpg alt="">
                              <a href=product-details.html title=# class=hover-img><img src=assets/images/item-10.jpg
                                      alt=""></a>
                              <div class=action>
                                  <ul>
                                      <li>
                                          <a href=my-wishlist.html title=#>
                                              <svg>
                                                  <use xlink:href=#wishlist></use>
                                              </svg>
                                          </a>
                                      </li>
                                      <li>
                                          <a href=compare.html title=#>
                                              <svg>
                                                  <use xlink:href=#compare></use>
                                              </svg>
                                          </a>
                                      </li>
                                  </ul>
                              </div>

                              <div class=labels>

                                  <span class="sale">Sale</span>
                              </div>
                          </div>
                          <div class=info-product>
                              <div class="colors-product-area img">
                                  <ul>
                                      <li>
                                          <div class=img--options><img src=assets/images/color/1.jpg alt=""></div>
                                      </li>
                                      <li>
                                          <div class=img--options><img src=assets/images/color/2.jpg alt=""></div>
                                      </li>
                                      <li>
                                          <div class=img--options><img src=assets/images/color/3.jpg alt=""></div>
                                      </li>
                                      <li>
                                          <div class=img--options><img src=assets/images/color/4.jpg alt=""></div>
                                      </li>
                                      <li>
                                          <div class=img--options>
                                              <img src=assets/images/color/5.jpg alt="">
                                          </div>
                                      </li>
                                  </ul>
                              </div>
                              <p class=price>342$ </p>
                              <p class=brand><a href=store-details.html title=#>Nike</a></p>
                              <h3><a href=product-details.html title=#> Woman's Ziane Leather Slip-ons</a></h3>
                              <div class=rated>
                                  <ul>
                                      <li class=active>
                                          <svg>
                                              <use xlink:href=#star></use>
                                          </svg>
                                      </li>
                                      <li class=active>
                                          <svg>
                                              <use xlink:href=#star></use>
                                          </svg>
                                      </li>
                                      <li class=active>
                                          <svg>
                                              <use xlink:href=#star></use>
                                          </svg>
                                      </li>
                                      <li class=active>
                                          <svg>
                                              <use xlink:href=#star></use>
                                          </svg>
                                      </li>
                                      <li>
                                          <svg>
                                              <use xlink:href=#star></use>
                                          </svg>
                                      </li>
                                      <li>
                                          <span>(127)</span>
                                      </li>
                                  </ul>
                              </div>
                              <a href=shopping-cart.html title=# class=add-to-cart>ADD TO CART</a>
                          </div>
                      </div>
                  </div>

              </div>
          </div>
        </div>
     </div>
  </section>
  */?>
</main>

<script>
  $(".color-area .color").click(function(e){
    console.log("yes exists")
      // e.preventDefault();
      e.stopImmediatePropagation()
         if(!$(this).attr("id")){
             return;
         }

      var $targetSlide =   $("[data-slide-target="+$(this).attr("id")+"]").parent().parent().attr("data-slick-index");
      $('.slider-single').slick('slickGoTo',  $targetSlide);
      // $(this).parent().addClass("is-active").siblings().removeClass("is-active")
  })
</script>
