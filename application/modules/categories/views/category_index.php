<div class="breadcrumb">
      <div class="container">
        <div class="breadcrumb-inner">
          <ul class="list-inline list-unstyled">
            <li><a href="<?php echo base_url();?>"><?php echo lang('home');?></a></li>
            <li class='active'><?php echo $cat_data->name;?></li>
          </ul>
        </div>
      </div>
  </div>

 <main>
    <div class="container">
        <div class="row mb-3">
            <div class="main-slide">
                <img width="1140" height="416" src="<?php echo $images_path.$cat_data->image;?>" alt="<?php echo $cat_data->name;?>"/>
            </div>
        </div>

        <div class="row mb-5">
          <?php foreach($sub_cats as $cat){?>
            <div class="col-md-3">
                <div class="category-container">
                    <div class="cat-img">
                        <img width="255" height="182" src="<?php echo $images_path.$cat->image;?>" alt="<?php echo $cat->name;?>"/>
                    </div>
                    <div class="cat-info">
                       <h3><?php echo $cat->name;?></h3>
                       <p><?php echo $cat->products_count.' '.lang('item');?></p>
                    </div>
                    <a href="<?php echo base_url().$sub_category_route.$cat->route.'/0';?>"></a>
                </div>
            </div>
          <?php }?>

        </div>

    </div>
 </main>
