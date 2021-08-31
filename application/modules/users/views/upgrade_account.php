  <section class="best-seller-section">
    <div class="container">
      <div class="row">
        <div class="title">
          <h1><?php echo lang('our_packages');?></h1>
        </div>
      </div>
      <div class="row">
        <?php if(count($customer_groups) != 0){
            foreach($customer_groups as $row){?>
          <div class="col-md-3">
            <div class="product-container">
              <div class="images-product">
                <img src="<?php echo $images_path.$row->image;?>" alt="<?php echo $row->title;?>" />
                <div class="action"><?php echo $row->description;?></div>
                <div class="labels"></div>
              </div>
              <div class="info-product">

                <p class="price">
                  <span class="new-price"><?php echo $row->price.' '.$this->data['currency_name'];?> </span>
                </p>

                <h3><a href="#" class="add_package" title="<?php echo $row->title;?>"> <?php echo $row->title;?></a></h3>

                <a href="#" class="add-to-cart buy_product" data-package_id="<?php echo $row->id;?>" data-type="package">
                  <svg>
                        <use xlink:href="#shopping-cart"></use>
                  </svg>
                  <?php echo lang('select_package');?>
                </a>

              </div>
            </div>
          </div>

        <?php }
      }else {?>
        <div class="alert-area" style="width: 100%"><?php echo lang('no_data');?></div>
      <?php }?>

      </div>
    </div>
  </section>
