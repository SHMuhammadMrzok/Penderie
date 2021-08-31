<div class="breadcrumb">
  <div class="container">
    <div class="breadcrumb-inner">
      <ul class="list-inline list-unstyled">
        <li><a href="<?php echo base_url();?>"><?php echo lang('home');?></a></li>
        <li class='active'><?php echo lang('wishlist');?></li>
      </ul>
    </div>
  </div>
</div>

<main>
  <div class="container">
    <div class="row">
      <?php $this->load->view('site/user_menu', $this->data);?>
      <div class="col-md-8">
        <div class="wishlist-main">
          <?php if(isset($cat_products) && count($cat_products) != 0){
            foreach($cat_products as $product){?>

              <div class="wishlist-container product_<?php echo $product->product_id;?>">
                  <div class="row no-gutters">
                      <div class="col-md-3">
                          <div class="image" style="width:181px; height:166px;">
                              <a href="<?php echo base_url() . $product_route . $product->route;?>">
                                <img src="<?php echo $images_path. $product->image;?>" alt="<?php echo $product->title;?>" width="181" height="166" />
                              </a>
                          </div>
                      </div>
                      <div class="col-md-9">
                          <div class="data-prod">
                              <div class="info">
                                  <h3 style="min-width: 360px">
                                    <a href="<?php echo base_url() . $product_route . $product->route;?>">
                                      <?php echo $product->title;?>
                                    </a>
                                  </h3>
                                  <p class="price">
                                    <span class="new-price"><?php echo $product->price.' '.$product->currency;?> </span>
                                    <?php if($product->price_before != $product->price){?>
                                      <span class="old-price"><?php echo $product->price_before.' '.$product->currency;?></span>
                                    <?php }?>
                                  </p>
                                  <div class="action-item">
                                      <div class="delet-item">
                                          <a href="#" class="remove_wishlist" data-product_id="<?php echo $product->product_id;?>">
                                              <svg>
                                                <use xlink:href="#rebbish"></use>
                                              </svg>
                                              <span><?php echo lang('remove_from_wishlist');?></span>
                                          </a>
                                      </div>
                                  </div>
                              </div>
                              <div class="add-to-card-button">
                                  <a href="#" class="cart" data-product_id="<?php echo $product->product_id;?>">
                                      <svg>
                                        <use xlink:href="#shopping-cart"></use>
                                      </svg>
                                      <span><?php echo lang('add_to_cart');?></span>
                                  </a>
                              </div>
                          </div>
                      </div>
                  </div>

              </div>

          <?php }
        }else{?>
          <div class="col-md-9">
            <div class="alert-area"><?php echo lang('no_data');?></div>
          </div>

        <?php }?>


        </div>
      </div>

    </div>
  </div>

</main>
