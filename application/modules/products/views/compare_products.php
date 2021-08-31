    <section class="predcramp">
        <div class="container no-padding">
            <ul>
                <li><a href="<?php echo base_url();?>"><?php echo lang('home');?></a></li>
                <li><span>/</span></li>
                <li><a href="<?php echo base_url();?>Compare_Products"><?php echo lang('compare_products');?></a></li>
            </ul>
        </div><!--container-->
    </section><!--predcramp-->

    <main class="no-padding-top">
        <div class="container no-padding">
            <div class="row no-margin">
                <div class="title-page">
 	   	 	        <h4><?php echo lang('compare_products');?></h4>
 	   	 	    </div><!--title-page-->
                <?php if(isset($error_msg)){?>
                    <span class="error"><?php echo $error_msg;?></span>
                <?php }else{?>
                    <div class="order-records">
                        <table class="table table-bordered table-hover">
                            <tr class="header-table">
                                <td><?php echo lang('thumbnail');?></td>
                    						<td><?php echo lang('product_name');?></td>
                    						<td><?php echo lang('name_of_store');?></td>
                    						<td><?php echo lang('availability_status');?></td>
                    						<td><?php echo lang('price');?></td>
                    						<td><?php echo lang('action');?></td>
                            </tr>
                            <?php foreach($compare_products as $product){?>
                                <tr class="compare_product_<?php echo $product->product_id;?>">
                    							<td>
                    								<a  href="<?php echo base_url().$product_route.$product->route;?>">
                                                        <img src="<?php echo base_url();?>assets/uploads/products/<?php echo $product->image;?>" class="img-responsive" alt="<?php echo $product->title;?>" width="70" style="text-align: center!important;" />
                    								</a>
                    							</td>
                    							<td>
                                    <a href="<?php echo base_url().$product_route.$product->route;?>"><?php echo $product->title;?></a>
                    							</td>
        							<td><?php echo $product->store_name;?></td>
        							<td><?php echo $product->availability;?></td>
        							<td>
        								<div>
                                        <?php /*if($points_cost){?>
                                            <?php echo lang('points').' : '.$product->points_cost;?>
                                        <?php }else{*/?>
                                            <?php echo $product->price.' '.$currency_name;?>
                                        <?php //}?>
                                            <?php /*<br />
                                            <span><?php  echo lang('vat_value').' '.$product->vat_value.' '.$currency_name;?></span>
                                            <br />
                                            ( <?php echo $product->vat_percent;?> % )
                                            */?>
                                        </div>
        							</td>
        							<td>
        								<a data-product_id="<?php echo $product->product_id;?>" class="btn btn-success cart" title="" data-toggle="tooltip" type="button" data-original-title="<?php echo lang('add_to_cart');?>"><i class="fa fa-cart-plus"></i></a>
        								<a data-product_id="<?php echo $product->product_id;?>" class="btn btn-danger remove_compare_product" title="" data-toggle="tooltip" data-original-title="<?php echo lang('remove_compare_product');?>"><i class="fa fa-times"></i></a>
        							</td>
        						</tr>
                            <?php }?>
                        </table>
                    </div><!--order-records-->
              <?php }?>
            </div><!--row-->
        </div><!--container-->
    </main>
