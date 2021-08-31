<div class="<?php echo $product->card_class;?>">
  <div class="product-container <?php echo isset($product->no_stock) ? 'out-of-stock':'';?>">
    <div class="images-product">
      <img src="<?php echo $product->images_path.$product->image;?>" alt="<?php echo $product->title;?>" />
      <a href="<?php echo base_url().$product->product_route.$product->route;?>" title="<?php echo $product->title;?>" class="hover-img">
        <img src="<?php echo $product->images_path.$product->hover_image;?>" alt="<?php echo $product->title;?>" />
      </a>
      <?php if(isset($product->rest_qty) && $product->rest_qty!=0){?>
        <div class=latest-units>
            <p><?php echo $product->rest_qty;?> <?php echo lang('pieces_left');?></p>
        </div>
      <?php }?>

      <?php if(isset($product->no_stock)){?>
        <div class=latest-units>
            <p><?php echo lang('no_stock_for_this_product');?></p>
        </div>
      <?php }?>

      <div class="action">
        <ul>
          <li>
            <a href="#" data-product_id="<?php echo $product->product_id;?>" title="<?php echo lang('add_to_wishlist');?>" class="wishlist_product">
              <svg>
                <use xlink:href="#wishlist"></use>
              </svg>
            </a>
          </li>

          <?php /*
          <li>
            <a href="#" data-product_id="<?php echo $product->product_id;?>" title="<?php echo lang('add_to_compare_product');?>" class="compare_products">
              <svg>
                <use xlink:href="#compare"></use>
              </svg>
            </a>
          </li>
          */?>

        </ul>
      </div>

      <div class=labels>
        <?php if($product->price != $product->price_before){
          /*if(isset($product->special_offer) && $product->special_offer== 1){?>
            <span class=special-offer><?php echo lang('special_offer');?></span>
          <?php }else{*/?>
            <span class="sale"><?php echo lang('sale').' '.$product->discount_percent .'%';?></span>
        <?php //}
        }?>
      </div>
    </div>

    <div class="info-product">
      <?php if(isset($product->multi_images) && count($product->multi_images) != 0){?>
          <div class="colors-product-area img">
            <ul>
                <?php foreach($product->multi_images as $image){?>
                  <li>
                    <div class="img--options"><img src="<?php echo $product->images_path.$image->image;?>" alt="" /></div>
                  </li>
                <?php }?>
            </ul>
          </div>
        <?php }?>

      <p class="price">
        <span class="new-price"><?php echo $product->price.' '.$product->currency;?> </span>
        <?php if($product->price != $product->price_before){?>
          <span class="old-price"><?php echo $product->price_before.' '.$product->currency;?></span>
        <?php }?>
      </p>

      <p class="brand">
        <a href="<?php echo base_url().$product->sub_category_route.$product->cat_route.'/0';?>" title="<?php echo $product->cat_name;?>"><?php echo $product->cat_name;?></a>
      </p>

      <h3><a href="<?php echo base_url().$product->product_route.$product->route;?>" title="<?php echo $product->title;?>"> <?php echo $product->title;?></a></h3>

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

      <a href="#" class="add-to-cart cart" data-product_id="<?php echo $product->product_id;?>">
        <svg>
              <use xlink:href="#shopping-cart"></use>
        </svg>
        <?php echo lang('add_to_cart');?>
      </a>

    </div>
  </div>
</div>
