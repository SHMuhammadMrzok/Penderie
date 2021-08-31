<section class="predcramp">
    <div class="container no-padding">
        <ul>
            <li><a href="<?php echo base_url();?>"><?php echo lang('home');?></a></li>
            <li><span>/</span></li>

            <?php if(isset($wishlist)){?>
                <li><a href="<?php echo base_url().'products/products/user_wishlist';?> "><?php echo lang('wishlist');?></a></li>
                <li><span>/</span></li>
            <?php }?>

            <?php if(isset($parent_cat_data) && count($parent_cat_data) !=0 ){?>
                <li><a href="<?php echo base_url().$main_category_route.$parent_cat_data->route.'/0';?> "><?php echo $parent_cat_data->name;?></a></li>
                <li><span>/</span></li>
            <?php }?>

            <?php if(isset($cat_data) && count($cat_data) != 0){?>
                <li><a href="<?php echo base_url(); echo $cat_data->parent_id == 0 ? $main_category_route:$sub_category_route;echo $cat_data->route.'/0';?> "><?php echo $cat_data->name;?></a></li>
                <li><span>/</span></li>
            <?php }?>
        </ul>
    </div><!--container-->
</section><!--predcramp-->

<main class="no-padding-top">
    <div class="container no-padding">
        <?php if(!isset($wishlist)){?>
            <div class="row no-margin margin-bottom-30px">
                <div class="title-store">
                    <?php if(isset($cat_data)){?>
                        <div class="col-md-4">
                            <div class="store-image">
     	   	 					<img src="<?php echo base_url();?>assets/uploads/<?php echo $cat_data->image;?>" alt="<?php echo $cat_data->name;?>"/>
     	   	 				</div><!--store-image-->
     	   	 				<div class="store-name">
     	   	 					<h2><?php echo $cat_data->name;?></h2>
     	   	 				</div><!--store-name-->
     	   	 			</div><!--col-->

     	   	 			<div class="col-md-8">
     	   	 				<div class="info-store">
                                <span><?php echo $cat_data->description;?></span>
     	   	 				</div><!--store-description-->
     	   	 			</div><!--col-->
                    <?php } elseif(isset($search_msg)){ ?>
                        <div class="col-md-12">
                            <div class="store-name">
     	   	 					<h2><?php echo $search_msg;?></h2>
     	   	 				</div><!--store-name-->
     	   	 			</div><!--col-->
                    <?php }?>
                </div><!--title-store-->
            </div><!--row-->
        <?php }?>
        <div class="row">
            <div class="col-md-3">
                <div class="filer">

                    <!--Start categories filter-->
                    <?php if(isset($sub_cats_data) && count($sub_cats_data) != 0){?>
                        <h4><?php echo lang('category');?></h4>
                        <?php foreach($sub_cats_data as $id=>$sub_cat){?>
                            <div class="row-form">
                                <label class="checkbox">
                                    <input type="checkbox" <?php echo isset($cat_filter) && in_array($id, $cat_filter) ? 'checked': '';?> class="cat-filter" name="cat_id_filter[]" value="<?php echo $id;?>" />
                                    <span></span>
                                </label>
    	   	 				              <label for="ch1"><?php echo $sub_cat;?></label>
                            </div><!--row-form-->
                        <?php }?>
                  <?php }?>
                  <!--End categories filter-->

                  <!--Start op filter-->
                  <?php if(isset($op_filters) && count($op_filters) != 0){?>
                      <?php foreach($op_filters as $key=>$row){?>
                          <h4><?php echo $row[0]->label;?></h4>
                          <?php foreach($row as $item){?>
                            <div class="row-form">
                                <label class="checkbox">
                                    <input type="checkbox" <?php echo isset($op_filter) && in_array($item->optional_field_option_id, $op_filter) ? 'checked': '';?> class="op-filter" name="op_filter[]" value="<?php echo $item->optional_field_option_id;?>" />
                                    <span></span>
                                </label>
                                <label for="ch1"><?php echo $item->field_value;?></label>
                            </div><!--row-form-->
                        <?php }?>
                      <?php }?>
                <?php }?>
                <!--End categories filter-->

                  <!--Start Prices Filter-->
                  <h4><?php echo lang('price');?></h4>
                    <div class="row-form">
                        <label class="checkbox">
                            <input type="radio" <?php echo isset($price_filter) && $price_filter==0 ? 'checked': '';?> class="price-filter" name="price_filter" value="0" />
                            <span></span>
                        </label>
     				    <label for="ch1"><?php echo lang('all').' ( '.$active_country_row->currency.' )';?></label>
                    </div><!--row-form-->

                    <div class="row-form">
                        <label class="checkbox">
                            <input type="radio" <?php echo isset($price_filter) && $price_filter==1 ? 'checked': '';?> class="price-filter" name="price_filter" value="1" />
                            <span></span>
                        </label>
     				    <label for="ch1"><?php echo lang('less_than_100').' ( '.$active_country_row->currency.' )';?></label>
                    </div><!--row-form-->
                    <div class="row-form">
                        <label class="checkbox">
                            <input type="radio" <?php echo isset($price_filter) && $price_filter==2 ? 'checked': '';?> class="price-filter" name="price_filter" value="2" />
                            <span></span>
                        </label>
     				    <label for="ch1"><?php echo lang('between_100_and_200').' ( '.$active_country_row->currency.' )';?></label>
                    </div><!--row-form-->
                    <div class="row-form">
                        <label class="checkbox">
                            <input type="radio" <?php echo isset($price_filter) && $price_filter==3 ? 'checked': '';?> class="price-filter" name="price_filter" value="3" />
                            <span></span>
                        </label>
     				    <label for="ch1"><?php echo lang('between_200_and_300').' ( '.$active_country_row->currency.' )';?></label>
                    </div><!--row-form-->
                    <div class="row-form">
                        <label class="checkbox">
                            <input type="radio" <?php echo isset($price_filter) && $price_filter==4 ? 'checked': '';?> class="price-filter" name="price_filter" value="4" />
                            <span></span>
                        </label>
     				    <label for="ch1"><?php echo lang('more_than_300').' ( '.$active_country_row->currency.' )';?></label>
                    </div><!--row-form-->
                    <!--End Prices Filter-->

                    <!--Start Rating Filter-->
                    <h4><?php echo lang('rating');?></h4>
                    <div class="row-form">
                        <label class="checkbox">
                            <input type="radio" <?php echo isset($rating_filter) && $rating_filter==0 ? 'checked': '';?> class="rating-filter" name="rating_filter" value="0" />
                            <span></span>
                        </label>
     				    <label for="ch1"><?php echo lang('all').' ( '.$active_country_row->currency.' )';?></label>
                    </div><!--row-form-->

                    <div class="row-form">
                        <label class="checkbox">
                            <input type="radio" <?php echo isset($rating_filter) && $rating_filter==1 ? 'checked': '';?> class="rating-filter" name="rating_filter" value="1" />
                            <span></span>
                        </label>
     				    <label for="ch1"><?php echo 1;?></label>
                    </div><!--row-form-->
                    <div class="row-form">
                        <label class="checkbox">
                            <input type="radio" <?php echo isset($rating_filter) && $rating_filter==2 ? 'checked': '';?> class="rating-filter" name="rating_filter" value="2" />
                            <span></span>
                        </label>
     				    <label for="ch1"><?php echo 2;?></label>
                    </div><!--row-form-->
                    <div class="row-form">
                        <label class="checkbox">
                            <input type="radio" <?php echo isset($rating_filter) && $rating_filter==3 ? 'checked': '';?> class="rating-filter" name="rating_filter" value="3" />
                            <span></span>
                        </label>
     				    <label for="ch1"><?php echo 3;?></label>
                    </div><!--row-form-->
                    <div class="row-form">
                        <label class="checkbox">
                            <input type="radio" <?php echo isset($rating_filter) && $rating_filter==4 ? 'checked': '';?> class="rating-filter" name="rating_filter" value="4" />
                            <span></span>
                        </label>
     				    <label for="ch1"><?php echo 4;?></label>
                    </div><!--row-form-->
                    <div class="row-form">
                        <label class="checkbox">
                            <input type="radio" <?php echo isset($rating_filter) && $rating_filter==5 ? 'checked': '';?> class="rating-filter" name="rating_filter" value="5" />
                            <span></span>
                        </label>
     				    <label for="ch1"><?php echo 5;?></label>
                    </div><!--row-form-->

                    <!--End Rating Filter-->

   	 			</div><!--filer-->


   	 		</div>
   	 		<div class="col-md-9">
                 <div class="area-products">
                    <div class="title-page product-filter filters-panel">
                         <h4><?php echo lang('products_count'); ?><span> <?php echo isset($cat_products)?count($cat_products):'( 0 ) ';?></span></h4>


                         <?php if(isset($sorting)){?>
                            <div class="sort form-group short-by">
                                <label><?php echo lang('order_by');?> : </label>
                                    <select id="input-sort" class="form-control" >
                                        <option value="0" >-------------</option>
                                        <option value="1" <?php echo isset($sort) && $sort == 1 ? 'selected' : '';?>><?php echo lang('name_asc');?></option>
                                        <option value="2" <?php echo isset($sort) && $sort == 2 ? 'selected' : '';?>><?php echo lang('name_desc');?></option>
                                        <option value="3" <?php echo isset($sort) && $sort == 3 ? 'selected' : '';?>><?php echo lang('price_asc');?></option>
                                        <option value="4" <?php echo isset($sort) && $sort == 4 ? 'selected' : '';?>><?php echo lang('price_desc');?></option>
                                        <option value="5" <?php echo isset($sort) && $sort == 5 ? 'selected' : '';?>><?php echo lang('rating_desc');?></option>
                                        <option value="6" <?php echo isset($sort) && $sort == 6 ? 'selected' : '';?>><?php echo lang('rating_asc');?></option>
                                    </select>
                                </div><!--sort-->
                        <?php }?>
                    </div><!--title-page-->

                    <div class="row flex-container">

                        <?php if(isset($error_msg)){?>
                            <div class="title-store">
                                <?php echo $error_msg;?>
                            </div>
                        <?php }?>

                        <?php
                        if(isset($cat_products) && count($cat_products)>0){
                            foreach($cat_products as $product){?>
                                <div class="col-md-6 item-col product_<?php echo $product->product_id;?>">
     	   	 						<div class="container-area">
        									   <div class="icons">
    												<ul>
                                                        <?php if($product->price_before != $product->price){?>
                                                            <li><span><?php echo lang('deduct');?></span></li>
                                                        <?php }?>
                                                    </ul>
                                                </div>

                                                <div class="image-div">
        											<a href="<?php echo base_url() . $product_route . $product->route;?>">
                                                        <img src="<?php echo base_url();?>assets/uploads/products/<?php echo $product->image;?>" alt="<?php echo $product->title;?>" style="width: 180px; height: 195px;" />
                                                    </a>
                                                </div><!--image-div-->

        										<div class="about-product">
        											<h4>
                                                        <a href="<?php echo base_url() . $product_route . $product->route;?>"> <?php echo $product->title;?></a>
                                                    </h4>
        											<div class="row no-margin">
    													<?php if($product->price_before != $product->price){?>
                                                            <div class="old-price">
                                                                <span><?php echo $product->price_before.' '.$product->currency;?></span>
                                                            </div><!--old-price-->
                                                        <?php }?>
                                                        <div class="price">
    														<span><?php echo $product->price.' '.$product->currency;?></span>
    													</div><!--price-->
    												 </div><!--row-->
    												 <div class="rate">
                                                        <?php
                                                        $avg_product_rate = intval($product->rating_avg);
                                                        $remain = 5 - $avg_product_rate ;
                                                        for($x = 0 ; $x < $avg_product_rate ; $x ++) {
                                                            ?>
                                                            <i class="fa fa-star" style="color: #ffae00;"></i>
                                                            <?php }
                                                            for($y = 0 ; $y < $remain ; $y ++) {
                                                            ?>
                                                            <i class="fa fa-star-o"></i>
                                                            <?php } ?>
                                                    </div>
                                                    <!--<ul>-->
                                                        <a href="#" class="add-cart cart add-card" data-product_id="<?php echo $product->product_id;?>" ><i class="fa fa-shopping-bag"></i> <?php echo lang('add_to_cart');?></a></li>
                                                        <?php if(isset($wishlist)){?>
                                                        <a href="#" class="wishlist remove_wishlist add-card" data-product_id="<?php echo $product->product_id;?>"><i class="fa fa-times"></i> <?php echo lang('remove_from_wishlist');?></a>
                                                        <?php }else{?>
                                                            <a href="#" class="wishlist wishlist_product add-card" data-product_id="<?php echo $product->product_id;?>"><i class="fa fa-heart"></i> <?php echo lang('add_to_wishlist');?></a>
                                                        <?php }?>

                                                        <a href="#" class="compare compare_products add-card" data-product_id="<?php echo $product->product_id;?>"><i class="fa fa-exchange"></i> <?php echo lang('add_to_compare_product');?></a>

        										</div><!--about-product-->
        									</div>
     	   	 					</div><!--col-->
                            <?php }
                            }?>

   	 				</div><!--row-->

   	 				<div class="row no-margin">
                        <?php if(isset($page_links)){?>
                            <div class="pagination-area">
                                <ul><?php echo $page_links;?></ul>
                            </div><!--pagination-area-->
                        <?php }?>
   	 				</div>

   	 			</div><!--area-products-->
   	 		</div><!--col-->
   	 	</div><!--row-->
   	 </div><!--container-->
</main>
