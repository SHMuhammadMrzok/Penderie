<?php $selected_op_cost = 0;?>
<link href="<?php echo base_url();?>assets/template/site/css/rating.css" rel="stylesheet" type="text/css">

<script type="text/javascript" src="<?php echo base_url();?>assets/template/site/js/rating.js"></script>

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
            else
            {
                $('#avgrat').text(data.average_rating);
                $('#totalrat').text(data.rating_number);
            }

        }
    });
}
</script>


<!-- Facebook share-->
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.10";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script><!-- Facebook share-->

        <section class="predcramp">
            <div class="container no-padding">
                <ul>
                    <li><a href="<?php echo base_url();?>"><?php echo lang('home');?></a></li>
                    <li><span>/</span></li>
                    <?php if(count($parent_data) != 0){?>
                        <li><a href="<?php echo base_url().$main_category_route.$parent_data->route;?>/0"><?php echo $parent_data->name;?></a></li>
                    <?php }?>
            		<li><a href="<?php echo base_url().$sub_category_route.$cat_data->route;?>/0"><?php echo $cat_data->name;?></a></li>
                </ul>
            </div><!--container-->
        </section><!--predcramp-->

        <section class="details-block">
            <div class="container"  >
                <div class="row">
                  <?php /*<div class="col-md-5">



           <div class="crs-wrap"  id="my-slider">

      <div class="crs-screen">

        <div class="crs-screen-roll">

          <div class="crs-screen-item">

            <img src="https://sejjadcom.com/assets/uploads/products/f2e94-c6f57849-b004-4fb0-824b-c7e0ad6c9eb2.jpeg"  class="block__pic"  alt=""/>

           </div>

          <div class="crs-screen-item"   >

            <img src="https://sejjadcom.com/assets/uploads/products/f2e94-c6f57849-b004-4fb0-824b-c7e0ad6c9eb2.jpeg"  class="block__pic"  alt=""/>

           </div>

          <div class="crs-screen-item"  >

            <img src="https://sejjadcom.com/assets/uploads/products/f2e94-c6f57849-b004-4fb0-824b-c7e0ad6c9eb2.jpeg"  class="block__pic"  alt=""/>

           </div>

          <div class="crs-screen-item"   >

            <img src="https://sejjadcom.com/assets/uploads/products/f2e94-c6f57849-b004-4fb0-824b-c7e0ad6c9eb2.jpeg"  class="block__pic"  alt=""/>

           </div>

          <div class="crs-screen-item"   >

            <img src="https://sejjadcom.com/assets/uploads/products/f2e94-c6f57849-b004-4fb0-824b-c7e0ad6c9eb2.jpeg"  class="block__pic"  alt=""/>

           </div>

          <div class="crs-screen-item"   >

            <img src="https://sejjadcom.com/assets/uploads/products/f2e94-c6f57849-b004-4fb0-824b-c7e0ad6c9eb2.jpeg"  class="block__pic"  alt=""/>

           </div>

        </div>

      </div>



      <div class="crs-bar">

        <div class="crs-bar-roll-current"></div>

        <div class="crs-bar-roll-wrap">

          <div class="crs-bar-roll">

            <div class="crs-bar-roll-item" style="background-image: url('https://sejjadcom.com/assets/uploads/products/f2e94-c6f57849-b004-4fb0-824b-c7e0ad6c9eb2.jpeg')"></div>

            <div class="crs-bar-roll-item" style="background-image: url('https://sejjadcom.com/assets/uploads/products/f2e94-c6f57849-b004-4fb0-824b-c7e0ad6c9eb2.jpeg')"></div>

            <div class="crs-bar-roll-item" style="background-image: url('https://sejjadcom.com/assets/uploads/products/f2e94-c6f57849-b004-4fb0-824b-c7e0ad6c9eb2.jpeg')"></div>

            <div class="crs-bar-roll-item" style="background-image: url('https://sejjadcom.com/assets/uploads/products/f2e94-c6f57849-b004-4fb0-824b-c7e0ad6c9eb2.jpeg')"></div>

            <div class="crs-bar-roll-item" style="background-image: url('https://sejjadcom.com/assets/uploads/products/f2e94-c6f57849-b004-4fb0-824b-c7e0ad6c9eb2.jpeg')"></div>

            <div class="crs-bar-roll-item" style="background-image: url('https://sejjadcom.com/assets/uploads/products/f2e94-c6f57849-b004-4fb0-824b-c7e0ad6c9eb2.jpeg')"></div>

          </div>

        </div>

      </div>
    </div>


					</div>*/?>

                    <div class="col-md-5">

                        <div class="slider-product">
                            <div id="slider1" class="flexslider">
                                <ul class="slides">
                                    <li>
                                        <span class="ex0">
                                            <img src="<?php echo base_url();?>assets/uploads/products/<?php echo $product->image;?>" title="<?php echo $product->title;?>" alt="<?php echo $product->title;?>" />
                                        </span>
									</li>
                                    <?php
                                    if(count($product_images) != 0){
                                        foreach($product_images as $i=>$image){ ?>
                                            <li>
                                                <span class="ex<?php echo $i+1;?>">
                                                    <img src="<?php echo base_url();?>assets/uploads/products/<?php echo $image->image;?>" title="<?php echo $product->title;?>" alt="<?php echo $product->title;?>" />
                                                </span>
                                            </li>
                                    <?php }
                                    }?>
                                </ul>
                            </div>
                            <div id="carousel" class="flexslider">
                                <ul class="slides">
                                    <li>
                                        <img src="<?php echo base_url();?>assets/uploads/products/<?php echo $product->image;?>" title="<?php echo $product->title;?>" alt="<?php echo $product->title;?>" />
									</li>
                                    <?php
                                    if(count($product_images) != 0){
                                        foreach($product_images as $i=>$image){ ?>
                                            <li>
                                                <img src="<?php echo base_url();?>assets/uploads/products/<?php echo $image->image;?>" title="<?php echo $product->title;?>" alt="<?php echo $product->title;?>" />
                                            </li>
                                    <?php }
                                    } ?>
                                </ul>
                            </div>
                        </div>

                        <div class="links-product-on-page">
                        	<ul>
                        		<li><a href="#description"><?php echo lang('detials');?></a></li>
                                 <?php if(count($product_cat_specs) != 0){?>
                                    <li><a href="#specifications"><?php echo lang('specifications');?></a></li>
                                <?php }?>
                                <?php if(count($related_products) != 0){?>
                        		  <li><a href="#related-products"><?php echo lang('related_products');?> </a></li>
                                <?php }?>
                        	</ul>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="MiddelContent">
                            <div class="icons">
                                <ul>
                                    <?php if($product->price_before != $product->price){?>
                                        <li><span><?php echo lang('deduct');?></span></li>
                                    <?php }?>
                                </ul>
                            </div>
                            <div class="row no-margin">
                                <div class="title-prod">
                                    <h4><?php echo $product->title;?></h4>
                                    <p><?php echo lang('cat_name');?> : <a href="<?php echo base_url().$sub_category_route.$cat_data->route;?>/0"><?php echo $cat_data->name;?></a></p>
                                    <?php if($product->brand_name != ''){?>
                                        <p><?php echo lang('brand_name');?> : <a href="<?php echo base_url().'All_Brands';?>"><?php echo $product->brand_name;?></a></p>
                                    <?php }?>
                                    <?php /*<p><?php echo lang('weight');?> : <?php echo $product->weight;?> KG </p>*/?>
                                    <p><?php echo lang('code');?> : <?php echo $product->code;?> </p>
                                    <p><?php echo lang('reward_points');?> : <?php echo $product->reward_points;?> </p>
                                </div><!--about-prod-->

                                <div class="ShopRate">
                                    <p><?php echo lang('name_of_store');?> : <a href="<?php echo base_url().'Store_details/'.$product->store_id;?>"><?php echo $product->store_name;?></a></p>
                                </div>

                            <?php if(isset($product_optional_fields) && count($product_optional_fields) != 0){?>
                                <div class="form-group box-info-product">
                                    <div class="row no-margin">
                                    	<div class="details_item">
                                        	<!--<h3 class="title-h3"><?php echo lang('product_optional_fields')?></h3>-->

                                            <form id="optional_fields_form" enctype="multipart/form-data">
                                                <?php //echo '<pre>'; print_r($product_optional_fields); die();
                                                 foreach ($product_optional_fields as $field)
                                                 {
                                                    $required       = '';
                                                    $required_span  = '';

                                                    if($field->required == 1)
                                                    {
                                                        $required       = 'required';
                                                        $required_span  = " <span class='required' style='color: red'> * </span>";
                                                    }
                                                 ?>

                                                    <div id="validation_div" style="margin: 5px;"></div>

                                                    <div class="row no-margin margin-bottom-10px">
                                                        <h5 class="title-h5"><?php echo $field->label.$required_span;?></h5>
                                                        <div class="row no-margin d-flex">
                                                            <?php
                                                             if($field->field_type_id == 2) // radio
                                                             {
                                                                foreach($field->options as $key=>$option)
                                                                {
                                                                    //if($option->active == 1){?>
																	<div class="radio-ara">
    																  <div class="radio-area-checkbox">
    															 		  <label class="container-radio">
 																			    <input type="radio" name="optional_field[<?php echo $field->id;?>]" value="<?php echo $option->id;?>" <?php echo $required;?> <?php echo $key==0?'selected':'';?>/>
																				  <span class="checkmark"></span>
																			</label>


    																	  <?php if($option->image != ''){?>

        																	   <img src="<?php echo base_url();?>assets/uploads/products/<?php echo $option->image;?>" alt=""  />

                                                                          <?php }?>

     																</div>

															        <div class="name-radio">
    																	    <a class="img-fancy"  data-fancybox="gallery" href="<?php echo base_url();?>assets/uploads/products/<?php echo $option->image;?>">
																				 <?php //echo $required_span;?><?php echo $option->field_value;?>
																		    </a>
    														     	 </div>
																	</div>

                                                             <?php //}
                                                                }
                                                             }
                                                             else if($field->field_type_id == 3) //check box
                                                             {
                                                                foreach($field->options as $key=>$option)
                                                                {
                                                                    if($key == 0){
                                                                        $selected_op_cost = $option->cost;
                                                                    }
                                                                    ?>
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

                                                               <select class="input-form" name="optional_field[<?php echo $field->id;?>]" <?php echo $required;?>>
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
                                                             ?>

                                                        </div>
                                                    </div><!--row-->
                                                    <input type="hidden" name="has_options[<?php echo $field->id;?>]" value="<?php echo $field->has_options;?>" />
                                                <?php }?>
                                                <input type="hidden" name="product_id" value="<?php echo $product->id;?>" />

                                                 <?php /*
                                                <a href="#" style="margin-top: 15px;" class="btn bg-primary buy_optional_fields" data-product_id="<?php echo $product->id;?>"><?php echo lang('buy_now')?></a>
                                                <a href="#" style="margin-top: 15px;" class="btn bg-primary add_optional_fields" data-product_id="<?php echo $product->id;?>"><?php echo lang('add_to_cart')?></a>
                                                */?>

                                            </form>
                                        </div><!--details_item-->

                                    </div>
                                </div>
                            <?php }?>

                           </div>
                       </div>
                    </div>

                    <div class="col-md-3">
                        <div class="LeftContent">

                            <?php if($product->auction == 0){?>
                                <div class="cart-div">
                                    <a href="#" class="btn btn-default <?php echo (isset($product_optional_fields) && count($product_optional_fields) != 0) ? 'add_optional_fields add-cart-bt' : 'cart';?>" data-toggle="tooltip" data-product_id="<?php echo $product->id;?>"><i class="fa fa-shopping-cart"></i> <?php echo lang('add_to_cart');?></a>
                                    <a href="#" class="btn btn-default buy-now-bt margin-top-10px <?php echo (isset($product_optional_fields) && count($product_optional_fields) != 0) ? 'buy_optional_fields' : 'buy_now';?>" data-toggle="tooltip" data-product_id="<?php echo $product->id;?>"><i class="fa fa-money"></i> <?php echo lang('buy_now');?></a>
                                </div>

                                <div class="links-product border-bottom">
                             	    <ul>
                                    <li>
                                        <a href="#" class="wishlist wishlist_product" data-toggle="tooltip" title="" data-product_id="<?php echo $product->id;?>" data-original-title="<?php echo lang('add_to_wishlist');?>"><i class="fa fa-heart"></i> <?php echo lang('add_to_wishlist');?> </a>
									                  </li>
                                    <li>
                                        <a href="#" class="compare compare_products" data-toggle="tooltip" title="" data-product_id="<?php echo $product->id;?>" data-original-title="<?php echo lang('add_to_compare_product');?>"><i class="fa fa-exchange"></i> <?php echo lang('add_to_compare_product');?></a>
									                  </li>
                             	</ul>
                             </div><!--links-product-->

                            <div class="row no-margin text-left margin-top-15px border-bottom">
                            	<div class="price-title">
                            		<span><?php echo lang('price'); ?>:</span>
                            	</div>
                                <?php if($product->price_before != $product->price){?>
                                    <div class="old-price">
        						   		             <span><?php echo $product->price_before."  ".$product->currency;?></span>
        						   	            </div><!--old-price-->
                                <?php }?>
              						    	<div class="price">
              						    		<span class="product_price"><?php echo $product->price + $selected_op_cost;?></span> <span><?php echo $product->currency;?></span>
              						    	</div><!--price-->

								                <div class="stock">
                                    <span><?php echo $product->availability;?></span>
                                </div>
                            </div>

                            <?php if(isset($product->daily_end_hour)){?>
                                <?php echo lang('remaining_time');?> : <p class="counter"></p>
                            <?php }?>

                          <?php }else{//Auction data?>
                                <div class="row" style="margin-bottom: 10px;">
                                    <form method="post" action="<?php echo base_url();?>products/auctions/add_bid" class="bid_form">
                                        <label><?php echo lang('your_bid');?></label>
                                        <?php $bid_data = array(
                                                                    'name' => 'bid',
                                                                    'min' => $product->price,
                                                                    'id'   => 'bid_amount',
                                                                    'class' => 'form-control'
                                                                );
                                        echo form_input($bid_data);?>
                                        <p><?php echo lang('min_bit_value').' '. $product->price."  ".$product->currency;?></p>
                                        <input type="hidden" name="product_id" id="product_id" value="<?php echo $product->id;?>" />
                                        <input type="submit" value="<?php echo lang('place_bid');?>" class="bid_submit btn btn-primary" />
                                    </form>
                                </div>
                            <?php }?>

                            <!--<h6 class="border-bottom">FREE SHIPPING</h6>-->
                            <div class="row no-margin border-bottom">

                                <ul class="social">
                                    <li><p><?php echo lang('share'); ?></p></li>
                                    <!--<li><a href="#"><i class="fa fa-facebook"><div id="fb-root"></div></i></a></li>-->
                                    <li>
                                        <div class="fb-share-button" data-href="<?php echo base_url().$product_route.$product->route;?>" data-layout="button" data-size="small" data-mobile-iframe="true"><a class="fb-xfbml-parse-ignore" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=http%3A%2F%2Fsbmcart.com%2FLareene%2Fproduct%2Fskirt5&amp;src=sdkpreparse">Share</a></div>
                                    </li>
                                    <li>
                                        <a href="https://twitter.com/share?ref_src=twsrc%5Etfw" class="twitter-share-button" data-show-count="false">Tweet</a><script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>
                                    </li>

                                    <!--<li><a href="#"><i class="fa fa-twitter"></i></a></li>
                                    <li><a href="#"><i class="fa fa-instagram"></i></a></li>-->
                                </ul>
                            </div>
                             <div class="rate">
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
						                </div><!--rate-->

                        </div>
                    </div>

                </div>

            </div>

        </section>

        <section class="Products" id="description">
            <div class="container">
                <div class="row">
                    <div class="Part1">
                        <div class="row">
                            <h4><?php echo lang('detials');?></h4>
                            <p><?php echo $product->description?></p>
                        </div>
                        <div class="row-img">
                            <img src="" alt=""/>
                            <span></span>
                        </div><!--row-img-->
                        <div class="row">
                            <?php if((isset($colors) && count($colors) != 0)){?>
                                <div class="image_option_type form-group required">
                                    <label class="control-label"><?php echo lang('available_colors');?></label>
                                    <ul class="product-options clearfix"id="input-option231">
                                        <?php foreach($colors as $color){?>
                                            <li class="radio">
                                                <label>
                                                    <?php //<img src="image/demo/colors/blue.jpg" class="img-thumbnail icon icon-color" />?>
                                                    <div class="color_box" style="background: <?php echo $color;?>; "></div>
                                                    <i class="fa fa-check"></i>
                                                    <label> </label>
                                                </label>
                                            </li>
                                        <?php }?>
                                        <li class="selected-option"></li>
                                    </ul>
                                </div>
                            <?php }?>
                        </div>

                        <?php if(!empty($meta_keywords)){?>
                        <div class="row">
                            <h4><?php echo lang('meta_tag');?></h4>
                            <p><?php echo $meta_keywords;?></p>
                        </div>
                        <?php } ?>

                    </div>
                </div>
            </div>
        </section>

        <?php if(count($product_cat_specs) != 0){?>
        <section class="Products" id="specifications">
            <div class="container">
                <div class="row">
                    <div class="Part1">
                        <div class="row">
                            <h4><?php echo lang('specifications');?></h4>
                            <?php foreach($product_cat_specs as $item){?>
                            <p>
                                <h5 class="title-h5"><?php echo $item->spec_label;?> : </h5> <?php echo $item->spec_value;?>
                            </p>
                            <?php }?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php }?>

        <section class="review" id="review">

            <div class="container">
                <div class="row">
                    <div class="col-md-9 center-div">
                        <div class="title">
                            <h3><?php echo lang('customers_review');?></h3>
                        </div><!--title-->

                        <!--ADD New Comment-->
                        <div class="comment-now">
                            <form action="<?php echo base_url();?>products/products/add_product_comment" method="post">
                                <div class="row-form required">
                                    <input type="text" name="username" class="form-control" placeholder="<?php echo lang('name');?>" required="required" />
								</div>
                                <div class="row-form required">
                                    <textarea placeholder="<?php echo lang('add_review');?>" name="comment" class="form-control" required="required"></textarea>
								</div>

                                <div class="buttons clearfix">
                				    <div>
                                        <input type="hidden" name="product_id" value="<?php echo $product->id;?>" />
                                        <input type="hidden" name="route" value="<?php echo $product->route;?>" />
                                        <button class="btn btn-default"><?php echo lang('add');?></button>
                                    </div>
                                </div>
                            </form>
                        </div><!--comment-now-->

                        <?php if(count($product_comments) != 0){
                            foreach($product_comments as $row){?>
                                <!--Exist Customer Comments-->
                                <div class="comment-area">
                                    <div class="row no-margin">
                                        <div class="user-img">
                                            <a href="#">
                                                <?php if(isset($row->user_image) && file_exists(base_url().'assets/uploads/'.$row->user_image)){?>
                                                    <img src="<?php echo base_url();?>assets/uploads/<?php echo $row->user_image;?>" alt="<?php echo $row->username;?>" width="70" height="70"/>
                                                <?php }else{?>
                                                    <img width="100" src="<?php echo base_url();?>assets/template/home/img/logo.png" alt="<?php echo $row->username;?>"/>
                                                <?php }?>
                                            </a>
                                        </div><!--user-img-->
                                        <div class="user-name">
                                            <h5><a href="#"><?php echo $row->username;?></a></h5>
                                            <p><?php echo date('Y-m-d H:i', $row->unix_time);?></p>
                                            <div class="row no-margin margin-top-5px">
                                                <article><?php echo $row->comment;?></article>
                                            </div><!--row-->
                                        </div><!--comment-->
                                    </div>	<!--row-->
                                </div><!--comment-area-->
                            <?php }
                            }?>
                    </div><!--col-->
                </div><!--row-->
            </div><!--container-->
        </section>

        <section class="block">
            <div class="container">
                <div class="row">
                    <div class="col-md-3 col-sm-3 col-xs-12 no-padding">
                        <?php if(count($side_advertisments) != 0){ ?>
                            <div class="ads">
                                <a href="<?php echo base_url().'advertisements/advertisement/track_link/'.$side_advertisments['0']->id;?>" target="<?php echo $side_advertisments['0']->target;?>">
                                    <img src="<?php echo base_url()."assets/uploads/".$side_advertisments['0']->image;?>" alt=""/>
                                </a>
                            </div><!--ads-->
                        <?php }?>
                    </div><!--col-->

                    <?php if(count($related_products) != 0){?>
                    <div class="col-md-9 col-sm-9 col-xs-12">
                        <div id="related-products">
                            <div class="title">
                                <h4><?php echo lang('related_products');?></h4>
                            </div><!--title-->
                        <div class="row no-margin">
                            <div class="slider-products">
                                <div id="owl-seller">
                                    <?php foreach($related_products as $r_product){?>
                                        <div class="item">
                                            <div class="prod-container">
                                                <div class="icons">
                                                    <ul>
                                                        <?php if($r_product->price_before != $r_product->price){?>
                                                            <li><span><?php echo lang('deduct');?></span></li>
                                                        <?php }?>
                                                    </ul>
                                                </div><!--icons-->

                                                <div class="img-product">
                                                    <img src="<?php echo base_url();?>assets/uploads/products/<?php echo $r_product->image;?>" alt=""/>
                                                    <div class="hover-product">
                                                        <a href="#" class="add-cart cart" data-toggle="tooltip" data-product_id="<?php echo $r_product->id;?>">
                                                            <i class="fa fa-shopping-bag"></i>
                                                        </a>
                                                        <a href="#" class="wishlist wishlist_product" data-toggle="tooltip" data-product_id="<?php echo $r_product->product_id;?>">
                                                            <i class="fa fa-heart"></i>
                                                        </a>
                                                        <a href="#" class="compare compare_products" data-toggle="tooltip" data-product_id="<?php echo $r_product->product_id;?>">
                                                            <i class="fa fa-exchange"></i>
                                                        </a>
                                                    </div><!--hover-product-->
                                                </div><!--img-product-->

                                                <div class="pro-details">
                                                    <div class="rate">
                                                        <?php
                                                        $avg_product_rate = intval($r_product->rating_avg);
                                                        $remain = 5 - $avg_product_rate ;
                                                        for($x = 0 ; $x < $avg_product_rate ; $x ++) {
                                                        ?>
                                                            <i class="fa fa-star" style="color: #ffae00;"></i>
                                                        <?php }
                                                        for($y = 0 ; $y < $remain ; $y ++) {
                                                        ?>
                                                            <i class="fa fa-star-o"></i>
                                                        <?php } ?>
                                                    </div><!--rate-->

                                                    <h4><a href="<?php echo base_url().$product_route.$r_product->route;?>"><?php echo $r_product->title;?></a></h4>
                                                    <div class="row no-margin text-center margin-top-10px">
                                                        <?php if($r_product->price_before != $r_product->price){?>
                                                            <div class="old-price">
                                                                <span class="old"><?php echo $r_product->price_before.' '.$r_product->currency;?></span>
                                                            </div><!--old-price-->
                                                        <?php }?>
                                                        <div class="price">
                                                            <span class="new"><?php echo $r_product->price.' '.$r_product->currency;?></span>
                                                        </div><!--price-->
                                                    </div>
                                                </div><!--pro-details-->

                                            </div><!--prod-container-->
                                        </div><!--item-->
                                    <?php }?>
                                </div><!--owl-featured-->
                            </div><!--slider-products-->
                        </div> <!--row-->
                    </div><!--related-products-->
                </div><!--col-->
                <?php }?>

            </div><!--row-->
        </div><!--container-->
    </section><!--block-->

    <script type="text/javascript" src="<?php echo base_url();?>assets/template/site/js/jquery.flexslider.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>assets/template/site/js/jquery.zoom.js"></script>

    <script>
			$(window).load(function(){
			  $('#carousel').flexslider({
				animation: "slide",
				controlNav: false,
				animationLoop: true,
				slideshow: true,
				itemWidth: 118,
				itemMargin: 5,
				asNavFor: '#slider1'
			  });

			  $('#slider1').flexslider({
				animation: "slide",
				controlNav: false,
				animationLoop: true,
				slideshow: true,
				sync: "#carousel",
				start: function(slider){
				  $('body').removeClass('loading');
				}
			  });

		      $('.ex1').zoom({ on:'click' });
			  $('#zoom1').zoom({ on:'click' });
					//$('#ex2').zoom({ on:'grab' });
					//$('.slides').zoom({ on:'click' });
					//$('#ex4').zoom({ on:'toggle' });

			   });
    </script>

 <?php if(isset($product->daily_end_hour)){?>
     <!--Count Down Script-->
     <script>
    // Set the date we're counting down to

    var countDownDate = new Date("<?php echo date('M d, Y ').$product->daily_end_hour.':0:0';?>").getTime();

    // Update the count down every 1 second
    var x = setInterval(function() {

        // Get todays date and time
        var now = new Date().getTime();

        // Find the distance between now an the count down date

        var distance = countDownDate - now;
        // Time calculations for days, hours, minutes and seconds
        //var days = Math.floor(distance / (1000 * 60 * 60 * 24));
        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);

        // Output the result in an element with id="demo"
        //document.getElementsByClassName("counter").innerHTML =  hours + "h " + minutes + "m " + seconds + "s ";
        $('.counter').html(hours + "h " + minutes + "m " + seconds + "s ");
        // If the count down is over, write some text
        if (distance < 0) {
            clearInterval(x);
            document.getElementsByClassName("counter").innerHTML = "EXPIRED";
        }
    }, 1000);
    </script>
 <!--END Count Down Script-->
 <?php }?>

 <script>
 //calculate product price with optional fields
 $('.op_cost ,.op_q').change(function() {
    //var sList = "";
    var product_price = 0;
     $('input[type=checkbox]').each(function () {


        if ($(this).is(':checked')) {
            var op_cost = $(this).attr("data-op_cost");
            var id      = $(this).attr("data-op_id");
            var index   = $(this).attr("data-op_index");

            $('.op_qty_'+id+'_'+index).prop('disabled', false);

            //var product_price = $('.product_price').text();
            var qty = $('.op_qty_'+id+'_'+index).val() ;

            cost = Number(op_cost) * Number(qty);

            product_price += cost;
        }
        else
        {
            $('.op_qty_'+id+'_'+index).prop('disabled', true);

        }

    });

    $('.product_price').html(product_price);

    //console.log (sList);
 });

 </script>

 <script>
 $('.bid_submit').click(function(e){
    e.preventDefault();
    postData = {
                    product_id : $('#product_id').val(),
                    bid_amount : $('#bid_amount').val(),
               }

    $.post('<?php echo base_url();?>products/auctions/add_bid', postData, function(result){
        var error = result['error'];
        var message = result['message_lang'];

        if(error == 0)
        {
            showToast('<?php echo lang('bid_added_successfully');?>', '<?php echo lang('success');?>', 'success');
        }
        else if(error == 1)
        {
            window.location.href = "<?php echo base_url();?>User_login";
        }
        else
        {
            showToast(message, '<?php echo lang('warning');?>', 'warning');
        }
    }, 'json');

 });
 </script>
