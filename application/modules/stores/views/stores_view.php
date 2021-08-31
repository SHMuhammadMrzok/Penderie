<style>
.store_image{
    align-items: center;
    padding: 15px;
    justify-content: center;
    border: 1px solid #ccc;
    border-radius: 4px;
    margin-bottom: 15px;
    margin-top: 15px;
    width: 250px;
}
</style>
<div class="breadcrumb">
    <div class="container">
        <div class="breadcrumb-inner">
            <ul class="list-inline list-unstyled">
                <li><a href="<?php echo base_url();?>"><?php echo lang('home');?></a></li>
                <li class='active'><?php echo lang('all_stores');?></li>
            </ul>
        </div>
    </div>
</div>


<main>
    <div class="container">
        <div class="row">
          <?php foreach($stores as $store){?>
            <div class="col-md-4">
                <div class="store-container">
                    <a href="<?php echo base_url();?>Store_details/<?php echo $store->id;?>">
                    <img src="<?php echo $images_path.$store->image;?>" class="store_image" />
                        <span class="name"><?php echo $store->name;?></span>
                        <span><?php //echo $store->products_count.' '.lang('products');?></span>
                    </a>
                </div>
            </div>
          <?php }?>

        </div>
    </div>
</main>
