<p>
   <div class="portlet-body">
       <div class="table-scrollable">
        <table class="table table-bordered table-hover">
	       <thead>
		       <tr>
                    <td ><?php echo lang('purchase_order_number');?></td>
                    <td ><?php echo lang('vendor');?></td>
                    <td ><?php echo lang('country');?></td>
                    <td ><?php echo lang('price');?></td>
                    <td ><?php echo lang('quantity');?></td>
                    <td ><?php echo lang('current_quantity');?></td>                                        
                    <td ><?php echo lang('unix_time');?></td>
                </tr>
	       </thead>
	       <tbody>
               <?php if(isset($product_stock) && !empty($product_stock)){
                        foreach($product_stock as $stock){
               ?>
	           <tr>
                    <td><?php echo $stock->purchase_order_id;?></td>
                    <td><?php echo $stock->title;?></td>
                    <td><?php echo $stock->country;?></td>
                    <td><?php echo $stock->price_per_unit;?>
                    <td><?php echo $stock->quantity;?></td>                                       
                    <td><?php echo $stock->current_qty;?></td>
                    <td><?php echo  date('Y/m/d', $stock->unix_time ) ;?></td> 
                    
               </tr>
               <?php }?>
                  <tr>
                    <td colspan="2"><?php echo lang("current_quantity");?></td>
                    <td colspan="2"><?php echo $current_amount;?></td>
                    <td colspan="2"><?php echo $current_not_active_amount;?></td>
                  </tr>
               <?php }else{?>
                    <tr> <td colspan="7" style="text-align: center;"><?php echo lang("no_data");?></td></tr>
               <?php  }?>
		   </tbody>
		</table>
       </div>
       <?php if(count($product_stock) != 0){?>
                <ul class="pagination"><?php echo $page_links; ?></ul>
            <?php }?>
    </div>	
</p>