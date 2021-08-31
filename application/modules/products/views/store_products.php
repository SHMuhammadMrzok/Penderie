
      	<section class="predcramp">
          <div class="container no-padding">
			<ul>
				<li><a href="<?php echo base_url();?>"><?php echo lang('home');?></a></li>
				<li><span>/</span></li>
				<li><a href="<?php echo base_url().'All_stores/';?>"><?php echo lang('all_stores');?></a></li>
				<li><span>/</span></li>
				<li><a href="<?php echo base_url().'stores/stores/'.$s_data->id;?>"><?php echo $s_data->name;?></a></li>
			</ul>
          </div><!--container-->
	   </section><!--predcramp-->
	   
 	   <main class="no-padding-top">
 	   	 <div class="container no-padding">

 	   	 	<div class="row no-margin margin-bottom-30px">
 	   	 		<div class="title-store">
 	   	 			<div class="col-md-4">
 	   	 				<div class="store-image">
 	   	 					<img src="<?php echo base_url();?>assets/uploads/<?php echo $s_data->image;?>" alt="<?php echo $s_data->name;?>" title="<?php echo $s_data->name;?>"/>
 	   	 				</div><!--store-image-->
 	   	 				<div class="store-name">
 	   	 					<h2><?php echo $s_data->name;?></h2>
 	   	 				</div><!--store-name-->
 	   	 			</div><!--col-->

 	   	 			<div class="col-md-12">
 	   	 				<div class="info-store">
                            <span><?php echo $s_data->description;?></span>
 	   	 				</div><!--store-description-->
 	   	 			</div><!--col-->

 	   	 		</div><!--title-store-->
			</div><!--row-->
				 
 	   	 	<div class="row">
 	   	 		<div class="col-md-3">
 	   	 			<div class="filer">
 	   	 				    <h4><?php echo lang('store_cats'); ?></h4>

                            <!--Store Categories-->
                            <?php if(count($s_cats) != 0 && isset($s_cats[0])){
                            foreach($s_cats[0] as $cat){?>
                                <div class="row-form">
                                    <label for="ch<?php echo $cat->id;?>"><?php echo $cat->name;?></label>
     	   	 				    </div><!--row-form-->

                                <!--Store Sub Categories-->
                                <?php if(isset($s_cats[$cat->id]) && $s_cats[$cat->id]  != 0){?>
                                    <?php foreach($s_cats[$cat->id] as $sub_cats){?>
                                        <div class="row-form">
                                            <label class="checkbox">
            									<input type="checkbox" name="" id="ch<?php echo $sub_cats->id;?>" disabled="disabled" checked="checked"/>
            									<span></span>
            								</label>
                                            <label for="ch<?php echo $sub_cats->id;?>">
                                                <a href="<?php echo base_url().$sub_category_route.$sub_cats->route.'/'.$s_data->store_id;?>" class="fa-indicator hidden-xs" ><?php echo $sub_cats->name;?></a>
                                            </label>
                                        </div><!--row-form-->
                                    <?php }?>
                                <?php }?>
                            <?php }
                            }?>
 	   	 			</div><!--filer-->
 	   	 		</div>
 	   	 		<div class="col-md-9">
 	   	 			<div class="area-products">
 	   	 				<div class="title-page">

                            <?php if(count($s_products) != 0){?>
                                <h4><?php echo lang('products_count'); ?><span> <?php echo ' ( '.count($s_products).' ) ';?></span></h4>


                                <?php if(isset($sorting) ){?>
                                    <div class="sort">
      	   	 							<select id="input-sort" class="form-control" >
     	   	 								<option value="0" ><?php echo lang('order_by');?> </option>
                                            <option value="1" <?php echo isset($sort) && $sort == 1 ? 'selected' : '';?>><?php echo lang('name_asc');?></option>
                        					<option value="2" <?php echo isset($sort) && $sort == 2 ? 'selected' : '';?>><?php echo lang('name_desc');?></option>
                        					<option value="3" <?php echo isset($sort) && $sort == 3 ? 'selected' : '';?>><?php echo lang('price_asc');?></option>
                        					<option value="4" <?php echo isset($sort) && $sort == 4 ? 'selected' : '';?>><?php echo lang('price_desc');?></option>
                        					<option value="5" <?php echo isset($sort) && $sort == 5 ? 'selected' : '';?>><?php echo lang('rating_desc');?></option>
                        					<option value="6" <?php echo isset($sort) && $sort == 6 ? 'selected' : '';?>><?php echo lang('rating_asc');?></option>
     	   	 							</select>
                                    </div><!--sort-->
                                <?php } ?>

                            <?php }?>
 	   	 				</div><!--title-page-->

 	   	 				<div class="row flex-container">
                        <?php
                        if(count($s_products) != 0){
                            foreach($s_products as $product){?>
 	   	 					<div class="col-md-6 item-col">
 	   	 						<div class="container-area">
    									   <div class="icons">
												<ul>
                                                    <?php if($product->price_before != $product->price && (!$points_cost)){?>
                                                        <li><span><?php echo lang('deduct');?></span></li>
                                                    <?php }?>
												</ul>
											</div>
    										<div class="image-div">
    											<a href="<?php echo base_url() . $product_route . $product->route;?>">
                                                    <img src="<?php echo base_url();?>assets/uploads/products/<?php echo $product->image;?>" alt="<?php echo $product->title;?>" title="<?php echo $product->title;?>" style="width: 180px; height: 195px;"/>
                                                </a>
    										</div><!--image-div-->
    										<div class="about-product">
                                                <h4>
                                                    <a href="<?php echo base_url() . $product_route . $product->route;?>"> <?php echo $product->title;?> </a>
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


                                                <ul>
                                                    <a href="<?php echo $product->id;?>" class="add-cart cart add-card" data-toggle="tooltip" data-product_id="<?php echo $product->id;?>"><i class="fa fa-shopping-bag"></i> <?php echo lang('add_to_cart');?></a>
                                                    <a href="<?php echo $product->product_id;?>" class="wishlist wishlist_product add-card" data-toggle="tooltip" data-product_id="<?php echo $product->product_id;?>"><i class="fa fa-heart"></i> <?php echo lang('add_to_wishlist');?></a>
                                                    <a href="<?php echo $product->product_id;?>" class="compare compare_products add-card" data-toggle="tooltip" data-product_id="<?php echo $product->product_id;?>"><i class="fa fa-exchange"></i> <?php echo lang('add_to_compare_product');?></a>
                                                </ul>
    										</div><!--about-product-->
    									</div>
 	   	 					</div><!--col-->
                            <?php }
                            }else{?>
                                <div class="col-md-12 item-col" style="margin-right: 20px;">
                                    <?php echo lang('no_available_products');?>
                                </div>	
                            <?php }?>
 	   	 				</div><!--row-->

 	   	 				<?php if(isset($pagination)){?>
                             <div class="row no-margin pagination">
                               <div class="pagination-area">
                                    <!--
     	   	 						<ul>
     	   	 							<li><a href="#">PREV</a></li>
     	   	 							<li><a href="#">1</a></li>
     	   	 							<li><a href="#" class="active">2</a></li>
     	   	 							<li><a href="#">3</a></li>
     	   	 							<li><a href="#">4</a></li>
     	   	 							<li><a href="#">NEXT</a></li>
     	   	 						</ul>
                                    -->
        							<ul>
                                        <?php echo $pagination;?>
        							</ul>
        						</div><!--pagination-area-->

 	   	 				     </div>
                        <?php }?>

 	   	 			</div><!--area-products-->
 	   	 		</div><!--col-->
 	   	 	</div><!--row-->
 	   	 </div><!--container-->
 	   </main>
