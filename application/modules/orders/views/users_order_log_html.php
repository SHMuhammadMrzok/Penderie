<style>
.xdsoft_timepicker{display: none!important;}
</style>
<div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
	<div class="row no-margin">
    	<div class="iner_page">
        	<h1 class="title_h1"><?php echo lang('orders_log');?>  </h1>
			<div class="row no-margin">
            	<table class="table  table-striped table-hover table-bordered" id="">
                	<tr class="header_tr">
                    	<td align="right"><?php echo lang('order_number');?></td>
                        <td align="right"><?php echo lang('order_date');?></td>
                        <td align="right" width="250"><?php echo lang('products');?></td>
                        <td align="right"><?php echo lang('final_total');?></td>
                        <td align="right"><?php echo lang('order_status');?></td>
                        <td align="right"><?php echo lang('view_order_details');?></td>
                    </tr>
                    <tr class="header_tr">
                    	<form>
                        	<td align="right"><input type="text" class="form-control order_log_search" placeholder=" <?php echo lang('serach');?>" id="order_number"/></td>
                        	<td align="right"><input type="text" class="form-control order_log_search default_datetimepicker" placeholder=" <?php echo lang('serach');?>" id="order_date"/></td>
                        	<td align="right"><input type="text" class="form-control order_log_search" placeholder=" <?php echo lang('serach');?>" id="title"/></td>
                        	<td align="right"><input type="text" class="form-control order_log_search" placeholder=" <?php echo lang('serach');?>" id="final_total"/></td>
                        	<td align="right"><input type="text" class="form-control order_log_search" placeholder=" <?php echo lang('serach');?>" id="status"/></td>
                        	<td align="right"></td>
                            
                        </form>
                    </tr>
                    
                    <tbody id="ajax_result"></tbody>
                </table>
                
                <!--Start Table Options-->
                
                <!--END Table Options-->
            </div><!--row-->
            
            <div class="row no-margin text-center margin-top-20px" id="pagination">
            	<nav>
                  <ul class="pagination">
                    <?php //if($pagination){ echo $pagination;}?>
                  </ul>
                </nav>
            </div><!--row-->
            
	    </div><!--iner_page-->
    </div><!--row-->
</div>

<!-- user order log search-->
<script>
    
    $('.order_log_search, #order_date').on('keyup change', function(){
        
       var postData = {
                        order_number  : $('#order_number').val() ,
                        order_date    : $('#order_date').val() ,
                        product_title : $('#title').val() ,
                        final_total   : $('#final_total').val() ,
                        status        : $('#status').val() 
                      };
       $.post('<?php echo base_url()."orders/user_orders/ajax_list";?>', postData, function(result){
            $("#ajax_result").html(result[0]);
            //$('#pagination').hide();
       }, 'json');
    });
  
</script>
