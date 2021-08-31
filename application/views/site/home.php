<section class="main-slider-area">
    <div class="row m-0">
      <div id="hero">
        <div class="owl-carousel owl-theme loading-div">
            <?php if(count($advertisments) !=0){?>
              <?php foreach($advertisments as $adv){?>
                  <div class="item">
                    <div class="img-container">
                      <a href="<?php echo base_url().'advertisements/advertisement/track_link/'.$adv->id;?>" title="<?php echo $adv->title;?>">
                        <picture>
                          <source media="(min-width: 650px)" srcset="<?php echo $images_path.$adv->image;?>" >
                          <source media="(min-width: 465px)" srcset="<?php echo $images_path.$adv->image;?>" >
                          <img src="<?php echo $images_path.$adv->image;?>" alt="<?php echo $adv->title;?>" style="width:auto;">
                        </picture>
                      </a>
                    </div>
                  </div>
            <?php }
            }?>

        </div>
      </div>
    </div>
  </section>
  
  <?php if(count($menu_stores) != 0 && $this->config->item('business_type') == 'b2b'){?>

    <section class="best-seller-section"><!--category-big-section-->
         <div class="container">
          <div class="row">
            <div class="title">
                <h1><?php echo lang('all_stores');?></h1>
            </div>
                <form action="<?php echo base_url().'All_stores'; ?>">
                    <button style="margin-right: 1000px;" class="item category-item add-to-cart"><?php echo lang('more_stores');?></button>
                </form>
    </section>
    <section class="category-big-section">
          <div class="loop owl-carousel owl-theme">
              <?php foreach($home_stores as $store){?>
                  <div class="item">
                    <div class="category-item">
                      <img width="371" height="464" src="<?php echo $images_path.$store->image;?>" alt="" />
                      <a href="<?php echo base_url().'Store_details/'.$store->id;?>" class="name-category"><?php echo $store->name;?></a>
                    </div>
                  </div>
              <?php }?>
          </div>
        </div>
      </div>
    </section>
  <?php }?>

<?php if(count($most_products) != 0){?>
    <section class="best-seller-section">
      <div class="container">
        <div class="row">
          <div class="title">
            <h1><?php echo lang('most_bought_products');?></h1>
          </div>
        </div>
        <div class="row">
          <?php
            foreach($most_products as $product){
              echo $product;
            }?>
        </div>
      </div>
    </section>


      <?php  if(count($top_advertisments) != 0){?>
        <section class="d-flex">
          <div class="container">
            <div class="row">
              <div class="col-12">
              <div class="img-full-width margin-bottom-20px">
                <a href="<?php echo base_url().'advertisements/advertisement/track_link/'.$top_advertisments[0]->id;?>;?>" target="<?php echo $top_advertisments[0]->target ;?>"  title="<?php echo $top_advertisments[0]->title;?>">
                  <img src="<?php echo $images_path.$top_advertisments[0]->image;?>"  class="mw-100" alt=""/>
                </a>
      </div>
              </div>
            </div>
          </div>
        </section>
    <?php }?>
    <?php if(count($top_advertisments) > 1){?>
      <section class="d-flex">
        <div class="container">
          <div class="row">
            <?php foreach($top_advertisments as $key=>$row){
              if($key>=1){?>
                <div class="col-md-<?php echo 12/(count($top_advertisments)-1);?>">
                <div class="img-full-width">
                  <a href="<?php echo base_url().'advertisements/advertisement/track_link/'.$row->id;?>;?>" target="<?php echo $row->target ;?>"  title="<?php echo $row->title;?>">
                    <img src="<?php echo $images_path.$row->image;?>"  class="mw-100" alt=""/>
                  </a>
              </div>
                </div>
            <?php }
          }?>

          </div>
        </div>
      </section>
    <?php }?>

  <?php }?>

  <?php if(count($categories_array[0]) != 0 ){?>
    <section class="category-big-section">
      <div class="container-fluid">
        <div class="row">
          <div class="loop owl-carousel owl-theme">
              <?php foreach($categories_array[0] as $cat){
                if(is_object($cat)){?>
                  <div class="item">
                    <div class="category-item">
                      <img width="371" height="464" src="<?php echo $images_path.$cat->image;?>" alt="" />
                      <a href="<?php echo base_url().$main_category_route.$cat->route.'/0';?>" class="name-category"><?php echo $cat->name;?></a>
                    </div>
                  </div>
                <?php }
              }?>
          </div>
        </div>
      </div>
    </section>
  <?php }?>

  <?php /*if(count($menu_stores) != 0 && $this->config->item('business_type') == 'b2b'){?>

    <section class="best-seller-section"><!--category-big-section-->
         <div class="container">
          <div class="row">
            <div class="title">
                <h1><?php echo lang('all_stores');?></h1>
            </div>
                <form action="<?php echo base_url().'All_stores'; ?>">
                    <button style="margin-right: 1000px;" class="item category-item add-to-cart"><?php echo lang('more_stores');?></button>
                </form>
    </section>
    <section class="category-big-section">
          <div class="loop owl-carousel owl-theme">
              <?php foreach($home_stores as $store){?>
                  <div class="item">
                    <div class="category-item">
                      <img width="371" height="464" src="<?php echo $images_path.$store->image;?>" alt="" />
                      <a href="<?php echo base_url().'Store_details/'.$store->id;?>" class="name-category"><?php echo $store->name;?></a>
                    </div>
                  </div>
              <?php }?>
          </div>
        </div>
      </div>
    </section>
  <?php }*/?>

  <?php /*
  <section class="category-big-section">
    <div class="container-fluid">
      <div class="row">
        <div class="loop owl-carousel owl-theme">
            <?php foreach($menu_stores as $store){?>
              <div class="item">
                <div class="category-item">
                  <img width="371" height="464" src="<?php echo base_url().'assets/uploads/'.$store->image;?>" alt="" />
                  <a href="<?php echo base_url().'Store_details/'.$store->store_id;?>" class="name-category"><?php echo $store->store_name;?></a>
                </div>
              </div>
          <?php }?>


        </div>

      </div>

    </div>
  </section>
   */?>
  <?php if(count($new_products) != 0){?>
    <section class="best-seller-section">
      <div class="container">
        <div class="row">
          <div class="title">
            <h1><?php echo lang('our_collections');?></h1>
          </div>
        </div>
        <div class="row">
          <?php foreach($new_products as $product){
            echo $product;?>
          <?php }?>

        </div>
      </div>
    </section>

    <?php if(count($bottom_advertisments) != 0){?>
      <section class="d-flex">
        <div class="container">
          <div class="row">
            <div class="col-12">
              <div class="img-full-width margin-bottom-20px">
              <a href="<?php echo base_url().'advertisements/advertisement/track_link/'.$bottom_advertisments[0]->id;?>;?>" target="<?php echo $bottom_advertisments[0]->target ;?>"  title="<?php echo $bottom_advertisments[0]->title;?>">
                <img src="<?php echo $images_path.$bottom_advertisments[0]->image;?>" class="mw-100" alt=""/>
              </a>
    </div>
            </div>
          </div>
        </div>
      </section>
  <?php }?>
  <?php /*if(count($bottom_advertisments) > 1){?>
    <section class="d-flex">
      <div class="container">
        <div class="row">
          <?php foreach($bottom_advertisments as $key=>$row){
            if($key>=1){?>
              <div class="col-md-<?php echo 12/(count($bottom_advertisments)-1);?>">
              <div class="img-full-width">
                <a href="<?php echo base_url().'advertisements/advertisement/track_link/'.$row->id;?>;?>" target="<?php echo $row->target ;?>"  title="<?php echo $row->title;?>">
                  <img src="<?php echo $images_path.$row->image;?>"  class="mw-100" alt=""/>
                </a>
            </div>
              </div>
          <?php }
        }?>
        </div>
      </div>
    </section>
  <?php }*/ ?>

  <?php }?>

  <section class="brands">
    <div class="container">
      <div class="row">
        <div class="brands-slider-container">
          <div class="owl-carousel owl-theme">
          <?php  foreach($brands as $brand){?>
            <div class="item loading-div">
              <a href="<?php echo base_url();?>products/brand_products/<?php echo $brand->id.'/1';?>" title="<?php echo $brand->name;?>">
                <img width="210" height="147" src="<?php echo $images_path. $brand->image;?>" alt="" />
              </a>
            </div>
        <?php }?>
        </div>


        </div>
      </div>
    </div>
  </section>    
  
  <script>
    $(window).load(function(){
      //$('.loading-div').fadeOut(2000);
    });
</script>

