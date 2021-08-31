	<p>
        <div class="portlet-body">
	       <div class="table-scrollable">
	        <table class="table table-bordered table-hover">
		       <thead>
			       <tr>
                        <td ><?php echo lang('order_number');?></td>
                        <td ><?php echo lang('unix_time');?></td>
                        <td ><?php echo lang('country');?></td>
                        <td ><?php echo lang('price');?></td>
                        <td ><?php echo lang('quantity');?></td>
                        <td ><?php echo lang('total_price');?></td>
                   </tr>
		       </thead>
		       <tbody>
                   <?php if(isset($product_sales) && !empty($product_sales)){
                            foreach($product_sales as $sale){
                   ?>
		           <tr>
                        <td><a href="<?php echo base_url().'orders/admin_order/view_order/'.$sale->order_id;?>" target="_blank"><?php  echo $sale->order_id;?></a></td>
                        <td><?php  echo date('Y/m/d', $sale->unix_time);?></td>
                        <td><?php  echo $sale->country_name;?></td>
                        <td><?php  echo $sale->final_price.' '.$currency_symbol; ?></td>
                        <td><?php  echo $sale->qty; ?></td>
                        <td><?php  $total_price = ($sale->final_price*$sale->qty);echo $total_price.' '.$currency_symbol;?></td> 
                   </tr>
                   <?php }?>
                   <tr>
                        <td><?php echo lang('total');?></td>
                        <td colspan="2"></td>
                        <td><?php  echo $total['total_price'].' '.$currency_symbol; ?></td>
                        <td><?php  echo $total['total_qty']; ?></td>
                        <td><?php  $total_price = ($total['total_price'] * $total['total_qty']);echo $total['final_total_price'].' '.$currency_symbol;?></td> 
                        
                   </tr>
                   <?php }else{?>
                        <tr> <td colspan="8" style="text-align: center;"><?php echo lang("no_data");?></td></tr>
                   <?php } ?>
			   </tbody>
			</table>
           </div>
           <?php if(count($product_sales) != 0){?>
                <ul class="pagination"><?php echo $page_links; ?></ul>
            <?php }?>
        </div>	
	</p>
