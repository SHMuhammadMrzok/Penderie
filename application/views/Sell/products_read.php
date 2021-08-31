<style>
.title{
    margin-bottom: 5px;
}
</style>

<div class="right-content">

    <?php if(isset($error)){?>
        <div class="row static-info" style="margin: 10px;">
            <div class="col-md-5 name">
				  <?php echo $error;?>
			</div>

		</div>
    <?php }else{?>

        <div class="list">
            <div class="relate"><a href="#addA" class="active"><?php echo lang('data');?></a></div>
            <div class="relate"><a href="#addB"><?php echo lang('discount_details');?></a></div>
            <?php if(!$quantity_per_serial){?>
                <div class="relate"><a href="#Stock_History"><?php echo lang('stock_history'); ?></a></div>
            <?php }?>
            <div class="relate"><a href="#Sales_History"><?php echo lang('sales_history'); ?></a></div>
        </div>
        <div class="add">
            <div id="addA" class="relateDiv row">
                <div class="table-area table-responsive">
                    <table class="table table-striped table-bordered table-hover table-responsive">
                        <tbody>
                           <?php foreach($row_data as $column_name => $column_data){?>
                                <tr>
                                    <td scope="row"> <?php echo $column_name;?> 	</td>
                                    <td><?php echo $column_data;?></td>
                                </tr>
                           <?php }?>
                        </tbody>
                    </table>

                     <?php if(!empty($products_countries)){?>
                        <div class="table-area table-responsive">
                            <div class="title">
                                <h3> <?php echo lang('product_countries_detials');?></h3>
                            </div>
                            <table class="table table-striped table-bordered table-hover table-responsive">
                                <tr class="header-ta">
                                    <td>
                                       <?php echo lang('country_name');?>
                                    </td>
                                    <td>
                                        <?php echo lang('currency');?>
                                    </td>
                                    <?php if(! $quantity_per_serial){?>
                                        <td colspan="2"> <?php echo lang('current_quantity');?></td>
                                    <?php }?>
                                    <td>
                                        <?php echo lang('price');?>
                                    </td>
                                    <td>
                                        <?php echo lang('average_cost');?>
                                    </td>
                                    <td>
                                        <?php echo lang('reward_points');?>
                                    </td>
                                    <td>
                                        <?php echo lang('active');?>
                                    </td>
                                    <td>
                                        <?php echo lang('display_home');?>
                                    </td>
                                </tr>

                                <?php foreach($products_countries as $pcountry){?>
                                    <tr>
                                        <td> <?php echo $pcountry->name;?></td>
                                        <td> <?php echo $pcountry->currency;?></td>

                                        <?php if(! $quantity_per_serial){?>
                                            <td> <?php echo $pcountry->active_product_quantity;?></td>
                                            <td> <?php echo $pcountry->not_active_product_quantity;?></td>
                                        <?php }?>

                                        <td> <?php echo $pcountry->price;?></td>
                                        <?php //if($serial_per_country && ! $quantity_per_serial){?>
                                            <td> <?php echo $pcountry->avg_cost.' '.$default_currency;?></td>
                                        <?php //}?>
                                        <td> <?php echo $pcountry->reward_points;?></td>
                                        <td>
                                            <?php
                                                if($pcountry->active == 1)
                                                {
                                                    $active_value =  '<span class="badge badge-success">'.lang('active').'</span>';
                                                }
                                                elseif($pcountry->active == 0)
                                                {
                                                    $active_value = '<span class="badge badge-danger">'.lang('not_active').'</span>';
                                                }
                                                echo $active_value;
                                            ?>
                                        </td>
                                        <td>
                                            <?php  if($pcountry->display_home == 1)
                                                {
                                                    $display_home_value =  '<span class="badge badge-success">'.lang('yes').'</span>';
                                                }
                                                elseif($pcountry->display_home == 0)
                                                {
                                                    $display_home_value = '<span class="badge badge-danger">'.lang('no').'</span>';
                                                }
                                                echo $display_home_value;
                                            ?>
                                        </td>

                                    </tr>
                                <?php }?>

                            </table>
                                </div>
                     <?php }?>
                     
                     <?php if(isset($product_optional_fields) && count($product_optional_fields) != 0){?>

                    <div class="table-area">
                        <div class="title">
                            <h3> <?php echo lang('product_optional_fields');?></h3>
                        </div>
                        <table class="table table-striped table-bordered table-hover">
                            <tr>
								<td> <?php echo lang('optional_fields');?></td>
                                <?php /*<td> <?php echo lang('is_required');?></td>*/?>
                                <td> <?php echo lang('value');?></td>
							</tr>

                            <?php foreach($product_optional_fields as $field){?>
                                <tr>
                                    <td> <?php echo $field->label;?></td>
                                    <?php /*<td> <?php echo $field->required_span;?></td>*/?>
                                    <td> <?php echo $field->default_value;?></td>

                                </tr>
                            <?php }?>

                        </table>
                    </div>

                <?php }?>
                </div>

            </div>


            <div id="addB" class="relateDiv row">

                <?php if(!empty($products_customer_groups)){?>
                    <div class="table-area">
                        <div class="title">
                            <h3> <?php echo lang('group_price');?></h3>
                        </div>
                        <table class="table table-striped table-bordered table-hover">
                            <tr class="header-ta">
                                <td> <?php echo lang('country_name');?></td>
                                <td> <?php echo lang('currency');?></td>
                                <td> <?php echo lang('group');?></td>
                                <td> <?php echo lang('price');?> </td>
                            </tr>

                            <?php foreach($products_customer_groups as $group){?>
                                <tr>
                                    <td> <?php echo $group->name;?></td>
                                    <td> <?php echo $group->currency;?></td>
                                    <td> <?php echo $group->title;?></td>
                                    <td> <?php echo $group->group_price;?></td>
                                </tr>
                            <?php }?>

                        </table>
                    </div>
                <?php }?>


                
            </div>

            <div id="addC" class="relateDiv row">
                <div class="table-area">
                    <div class="title">
                        <h3> <?php echo lang('product_optional_fields');?></h3>
                    </div>
                    <table class="table table-striped table-bordered table-hover">
                        <tr>
                            <td ><?php echo lang('country');?></td>
                            <td ><?php echo lang('price');?></td>
                            <td ><?php echo lang('discount_start_unix_time');?></td>
                            <td ><?php echo lang('discount_end_unix_time');?></td>
                            <td ><?php echo lang('max_units_customers');?></td>
                            <td ><?php echo lang('active');?></td>
	                   </tr>

                        <?php if(isset($product_discount) && !empty($product_discount)){
                            foreach($product_discount as $pro){?>
                            <tr>
				                <td><?php echo $pro->name;?></td>
                                <td><?php echo $pro->price;?></td>
                                <td><?php echo date('Y/m/d ',$pro->discount_start_unix_time);?></td>
                                <td><?php echo date('Y/m/d ',$pro->discount_end_unix_time);?></td>
                                <td><?php echo $pro->max_units_customers;?></td>
                                <td>
                                    <?php
                                        if($pro->active == 0)
                                        {
                                            $active = '<span class="badge badge-danger">'.lang('not_active').'</span>';
                                        }
                                        elseif($pro->active == 1)
                                        {
                                            $active = '<span class="badge badge-success">'.lang('active').'</span>';
                                        }
                                        echo $active;
                                    ?>
                                </td>
                           </tr>
                        <?php }
                        }else{?>
                            <tr> <td colspan="6" style="text-align: center;"><?php echo lang("no_data");?></td></tr>
                       <?php } ?>

                    </table>
                </div>

            </div>
            <?php if(!$quantity_per_serial){?>
                <div id="Stock_History" class="relateDiv row"></div>
            <?php }?>

            <div id="Sales_History" class="relateDiv row"></div>

        </div>
        <form method="post" action="<?php echo base_url();?>products/admin_products/seller_borrow_product">
          <div id="" class="relateDiv row">
            <div class="col-md-12">
                <input type="hidden" name="product_id" value="<?php echo $product_id;?>" />
                <button class="button"><?php echo lang('sell_product');?> </button>
            </div>
          </div>
      </form>
    <?php }?>
</div>


<script type="text/javascript">
$(function(){
   get_sales('<?php echo base_url().'products/admin_products/products_sales_ajax/'; ?>');
   get_stock('<?php echo base_url().'products/admin_products/products_stock_ajax/'; ?>');

   $('body').on('click', '.pages_links_sales', function(e){
        e.preventDefault();
        get_sales($(this).attr('href'));
   });

   $('body').on('click', '.pages_links_stock', function(e){
        e.preventDefault();
        get_stock($(this).attr('href'));
   });

});

function get_sales(url)
{
    $.post(url, {lang_id: <?php echo $lang_id; ?>, product_id: <?php echo $product_id; ?>}, function(result){
        $('#Sales_History').html(result);
    });
}

function get_stock(url)
{
    $.post(url, {lang_id: <?php echo $lang_id; ?>, product_id: <?php echo $product_id; ?>}, function(result){
        $('#Stock_History').html(result);
    });
}

</script>
