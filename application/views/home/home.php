
    	<section class="slider">
    		<div class="container no-padding">
    			<div class="row no-margin">
    				<div id="owl-demo" class="owl-carousel">
                        <?php foreach($advertisments as $adv){?>
                            <div class="item">
                                <a href="<?php echo base_url().'advertisements/advertisement/track_link/'.$adv->id;?>" target="<?php echo $adv->target;?>" >
                                    <img src="<?php echo base_url()."assets/uploads/thumb/1170x400/1170x400_$adv->image";?>" alt="" />
                                </a>
                            </div><!--item-->
                        <?php }?>
					</div>
    			</div><!--row-->
    		</div>
    	</section><!--slider-->
        
    	<main>
            <div class="container">
    			<div class="row">
                    <?php if(count($middle_advertisments) != 0){ ?>
        				<div class="ads">
        					<a href="<?php echo base_url().'advertisements/advertisement/track_link/'.$middle_advertisments['0']->id;?>" target="<?php echo $middle_advertisments['0']->target;?>">
                                <img width="1170" height="235" src="<?php echo base_url()."assets/uploads/".$middle_advertisments['0']->image;?>" alt=""/>
                            </a>
        				</div><!--ads-->
                    <?php }?>
    				
    				<div class="row margin-top-30px">
    					<div class="col-md-3">
                        
    						<?php if(count($side_advertisments) != 0){ ?>
                				<div class="ads">
                					<a href="<?php echo base_url().'advertisements/advertisement/track_link/'.$side_advertisments['0']->id;?>" target="<?php echo $side_advertisments['0']->target;?>">
                                        <img width="270" height="370" src="<?php echo base_url()."assets/uploads/".$side_advertisments['0']->image;?>" alt=""/>
                                    </a>
                				</div><!--ads-->
                            <?php }?>
                        
    						<div class="latest-product">
    							<div class="title">
    								<h2><?php echo lang('products');?> <span><?php echo lang('most_searched');?> </h2>
    								<!-- <a href="#">More</a> -->
    							</div>
                                
                                <ul class="nav nav-tabs" role="tablist">
                                    <?php
                                    $index = 0; 
                                    foreach($searched_products as $key=>$s_store){
                                        if(count($s_store->searched_products) != 0){
                                        ?>
                                            <li role="presentation" class="<?php echo $index==0 ? 'active':'';?>">
                                                <a href="#tab-searched-<?php echo $key+1;?>" aria-controls="tab-searched-<?php echo $key+1;?>" role="tab" data-toggle="tab">
                                                    <?php echo $s_store->store_name;?>
                               					</a>
                                            </li>
                                    <?php $index++;
                                        }
                                    } ?>
                                </ul>
                                    
    							<div class="tab-content">
                                
                                <?php
                                $index = 0;
                                foreach ($searched_products as $key=>$s_store) {
                                    if(count($s_store->searched_products) != 0){
                                        ?>
                                        <div role="tabpanel" class="tab-pane <?php echo $index==0 ? 'active' : '';?>" id="tab-searched-<?php echo $key+1;?>">
                                            <div class="row no-margin">
                                                <?php foreach($s_store->searched_products as $product){?>
                                                
                                                    <div class="product-row">
                                                        <div class="image-product">
                                                            <a href="<?php echo base_url().$product_route.$product->route;?>">
                                                                <img src="<?php echo base_url();?>assets/uploads/products/<?php echo $product->image;?>" alt=""/>
                                                            </a>
                                                        </div><!--image-product-->
                                                        <div class="product-data">
                                                            <h4><a href="<?php echo base_url().$product_route.$product->route;?>"><?php echo $product->title;?></a></h4>
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
                                                        </div><!--product-data-->
                                                    </div><!--product-row-->
                                                
                                                <?php }?>
                                                
                                            </div><!--row-->
                                        </div><!--tab-pane-->
                                        
                                        <?php $index++;
                                        }
                                    } ?>
                                </div><!--tab-content-->
                            </div><!--latest-product-->
                            
    						<?php if(count($side_advertisments) > 1){ ?>
                				<div class="ads">
                					<a href="<?php echo base_url().'advertisements/advertisement/track_link/'.$side_advertisments['1']->id;?>" target="<?php echo $side_advertisments['1']->target;?>">
                                        <img src="<?php echo base_url()."assets/uploads/".$side_advertisments['1']->image;?>" alt=""/>
                                    </a>
                				</div><!--ads-->
                            <?php }?>
                            
    					</div><!--col-->
                        
    					<div class="col-md-9">
                            <div class="row-tabs">
                                <div class="title">
                                    <h2> <?php echo lang('products');?> <span><?php echo lang('selected');?></span> </h2>
                                    <!--<a href="#">More</a>-->
                                </div><!--title-->
                                
                                <ul class="nav nav-tabs" role="tablist">
                                    <?php
                                    $index = 0; 
                                    foreach($stores_products as $key=>$store){
                                        if(count($store->products) != 0){
                                            ?>
                                            <li role="presentation" class="<?php echo $index==0 ? 'active':'';?>">
                            					<a href="#tab<?php echo $key+1;?>" aria-controls="tab<?php echo $key+1;?>" role="tab" data-toggle="tab">
                            					<?php echo $store->store_name;?>
                            					</a>
                            				</li>
                                        <?php $index++;
                                        }
                                    }?>
                                </ul>
                                
                                <div class="tab-content">
                                    <?php
                                    $index = 0;
                                    foreach($stores_products as $key=>$store){
                                        if(count($store->products) != 0){
                                            ?>
                                            <div role="tabpanel" class="tab-pane <?php echo $index==0 ? 'active':'';?>" id="tab<?php echo $key+1;?>">
        										<div class="slider-products">
        											<div class="owl-featured">
                                                         
                                                        <?php foreach($store->products as $product){?>
                                                            <div class="item">
                                                                <div class="prod-container">
                                                                    <div class="icons">
                                                                        <ul>
                                                                            <?php if($product->price_before != $product->price){?>
                                                                                <li><span><?php echo lang('deduct');?></span></li>
                                                                            <?php }?>
                                                                        </ul>
                                                                    </div><!--icons-->
                                                                    
                                                                    <div class="img-product">
                                                                        <img src="<?php echo base_url();?>assets/uploads/products/<?php echo $product->image;?>" alt=""/>
                                                                        <div class="hover-product">
                                                                            
                                                                            <a href="#" title="<?php echo lang('add_to_cart');?>" class="add-cart cart" data-toggle="tooltip" data-product_id="<?php echo $product->id;?>">
                                                                                <i class="fa fa-shopping-bag"></i>
                                                                            </a>
                                                                            
                                                                            <a href="#" title="<?php echo lang('add_to_wishlist');?>" class="wishlist wishlist_product" data-toggle="tooltip" data-product_id="<?php echo $product->product_id;?>">
                                                                                <i class="fa fa-heart"></i>
                                                                            </a>
                                                                            <a href="#" title="<?php echo lang('add_to_compare_product');?>" class="compare compare_products" data-toggle="tooltip" data-product_id="<?php echo $product->product_id;?>">
                                                                                <i class="fa fa-exchange"></i>
                                                                            </a>
                                                                        </div><!--hover-product-->
                                                                    </div><!--img-product-->
                                                                    
                                                                    <div class="pro-details">
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
            											 				 </div><!--rate-->
            											 				 <h4><a href="<?php echo base_url().$product_route.$product->route;?>"><?php echo $product->title;?></a></h4>
            											 				 <div class="row no-margin text-center margin-top-10px">
                                                                            <?php if($product->price_before != $product->price){?>
                                                                            <div class="old-price">
            																	<span class="old"><?php echo $product->price_before.' '.$product->currency;?></span>
            																</div><!--old-price-->
                                                                            <?php }?>
            																<div class="price">
            																	<span class="new"><?php echo $product->price.' '.$product->currency;?></span>
            																</div><!--price-->
            															 </div>
            											 			</div><!--pro-details-->
            											 		</div><!--prod-container-->
            											     </div><!--item-->
                                                         <?php }?>
                                                         
        											</div><!--owl-featured-->
        										</div><!--slider-products-->
        									</div><!--tabpanel-->
                                    
                                    <?php $index++;}
                                    }?>
                                    
                                </div><!--tab-content-->
    						</div><!--row-tabs-->
                            
                            <?php if(count($middle_advertisments) > 1){ ?>
                				<div class="ads margin-bottom-30px margin-top-10px">
                					<a href="<?php echo base_url().'advertisements/advertisement/track_link/'.$middle_advertisments['1']->id;?>" target="<?php echo $middle_advertisments['1']->target;?>">
                                        <img width="870" height="174" src="<?php echo base_url()."assets/uploads/".$middle_advertisments['1']->image;?>" alt=""/>
                                    </a>
                				</div><!--ads-->
                            <?php }?>
                                						
    						<div class="fe-product-block">
                                <div class="title">
                                    <h2><?php echo lang('products');?> <span><?php echo lang('most_bought_products');?></h2>
    								<!--<a href="#">More</a>-->
                                </div><!--title-->
                                
                                <ul class="nav nav-tabs" role="tablist">
                                    <?php
                                    $index = 0; 
                                    foreach($most_products as $key=>$m_store){
                                        if(count($m_store->most_products) != 0){
                                            ?>
                                            <li role="presentation" class="<?php echo $index==0 ? 'active':'';?>">
                            					<a href="#tab-seller-<?php echo $key+1;?>" aria-controls="tab-seller-<?php echo $key+1;?>" role="tab" data-toggle="tab">
                            					<?php echo $m_store->store_name;?>
                            					</a>
                            				</li>
                                        <?php $index++;}
                                        }?>
                                </ul>
                
                			  <div class="tab-content">
                                <?php 
                                $index = 0;
                                foreach ($most_products as $key=>$m_store) { 
                				    if(count($m_store->most_products) != 0){
                				        ?>
                    				 <div role="tabpanel" class="tab-pane <?php echo $index==0 ? 'active' : '';?>" id="tab-seller-<?php echo $key+1;?>">
                                        
                                        <div class="row flex-container">
                                            <?php foreach($m_store->most_products as $product){?>
                                                <div class="col-md-6 item-col">
                                                    <div class="container-area">
                                                        <div class="icons">
                                                            <ul>
                                                                <?php if($product->price_before != $product->price){?>
                                                                    <li><span><?php echo lang('deduct');?></span></li>
                                                                <?php }?>
                                                            </ul>
                                                        </div><!--icons-->
                                                        
                                                        <div class="image-div">
                                                            <a href="<?php echo base_url().$product_route.$product->route;?>">
                                                                <img src="<?php echo base_url();?>assets/uploads/products/<?php echo $product->image;?>" alt=""/>
                                                            </a>
                                                        </div><!--image-div-->
                                                        
                                                        <div class="about-product">
                                                            <h4><a href="<?php echo base_url().$product_route.$product->route;?>"><?php echo $product->title;?></a></h4>
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
                                                            
                                                            <ul class="add-to-list">
                                                                <li><a href="#" title="<?php echo lang('add_to_cart');?>" class="cart add-card" data-toggle="tooltip" data-product_id="<?php echo $product->id;?>"><i class="fa fa-shopping-bag"></i></a></li>
                                                                
                                                                <li><a href="#" title="<?php echo lang('add_to_wishlist');?>" class="wishlist wishlist_product add-card" data-toggle="tooltip" data-product_id="<?php echo $product->product_id;?>"><i class="fa fa-heart"></i> </a></li>
                                                                
                                                                <li><a href="#" title="<?php echo lang('add_to_compare_product');?>" class="compare compare_products add-card" data-toggle="tooltip" data-product_id="<?php echo $product->product_id;?>"><i class="fa fa-exchange"></i> </a></li>                                                                                                                                
                                                            </ul>
            										</div><!--about-product-->
            									</div><!--container-area-->
            								</div><!--col-->
                                            
                                            <?php } ?>
                                        
                                        </div><!--row-->
                                        
                    				</div><!--tab-pane-->
                                 <?php $index++;}
                                 }?>
                                 
                			  </div><!--tab-content-->
                                  							
    						</div> <!--fe-product-block-->
    					</div><!--col-->
    				</div>
    			</div><!--row-->
    		</div><!--container-->
    	</main>