<div class="top_grid2">	
 <div class="container">
    <div class="title">
        <h1><?php echo lang('search_result');?> </h1>
    </div>
    <?php if (isset($message)){?>
        <p class="text"><?php echo $message;?></p>  
    <?php }else {?>
        <p class="text"><?php echo lang('product_search_result')." ".$product_name." / ".lang('result_num')."  ".$result_num;?></p>
    <?php }?>
   <?php if(!empty($res_products)){
        foreach($res_products as $product){?>
           <div class="col-md-3 col-sm-3  col-xs-12">
              <div class="grid_1 contact-item">
                <div class="b-link-stroke b-animate-go  thickbox">
                	<a href="<?php echo base_url(). $product_route . $product->route;?>">
                        <img class="img-responsive" src="<?php echo base_url();?>assets/uploads/products/<?php echo $product->image;?>" alt="img"/>
                    </a>
                </div><!--b-link-stroke-->
               
                  <ul class="grid_2-bottom">
                 
                    <li class="grid_2-right">
                      <div class="btn btn-primary btn-normal btn-inline " target="_self" title="<?php echo $product->title?>>"><?php echo $product->title?><i class="fa fa-angle-right" aria-hidden="true"></i></div>
                    </li>
                    <div class="clearfix"> </div>
                    
                  </ul>
              </div><!-- contact-item-->
           </div><!--col--> 
   <?php }
   }?>
   
  </div><!--container-->
</div>
