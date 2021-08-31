<div class="breadcrumb">
  <div class="container">
    <div class="breadcrumb-inner">
      <ul class="list-inline list-unstyled">
        <li><a href="<?php echo base_url();?>"><?php echo lang('home');?></a></li>
        <li class='active'><?php echo lang('all_brands');?></li>
      </ul>
    </div>
  </div>
</div>

<main>
  <div class="container">
    <div class="row">
      <div class="title no-border-bottom">
        <h1><?php echo lang('all_brands');?></h1>
      </div>
    </div>
    <div class="brand-page">
      <div class="row">
				<?php foreach($brands as $brand){?>
	        <div class="col-md-2">
	          <div class="brand-container">
	            <a href="<?php echo base_url().'Brand_Products/'.$brand->id.'/1';?>">
	              <img width="130" height="91" src="<?php echo $images_path.$brand->image;?>" alt="<?php echo $brand->name;?>" title="<?php echo $brand->name;?>" />
	            </a>
	          </div>
	        </div>
				<?php }?>

      </div>
    </div>
  </div>
</main>
