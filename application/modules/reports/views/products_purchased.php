<div class="portlet-body flip-scroll">
    <table class="table table-bordered table-striped table-condensed flip-content">
        <thead class="flip-content">
            <tr>
            	<th>
            		 <?php echo lang('order_number');?>
            	</th>
            	<th>
            		 <?php echo lang('vendor');?>
            	</th>
            	<th >
            		 <?php echo lang('unix_time');?>
            	</th>
                <th>
            		 <?php echo lang('product_name');?>
            	</th>
                <th class="numeric">
            		 <?php echo lang('quantity');?>
            	</th>
                <th>
            		 <?php echo lang('price');?>
            	</th>
                <th>
            		 <?php echo lang('country');?>
            	</th>

            </tr>
        </thead>
        <tbody>
            <?php foreach($stock_data as $product){?>
                <tr>
                    <td>
                		 <?php echo $product->order_id;?>
                	</td>
                	<td>
                		 <?php echo $product->vendor_name;?>
                	</td>
                	<td>
                		 <?php echo date('Y-m-d H:i', $product->unix_time);?>
                	</td>
                    <td>
                		 <?php echo $product->product_name;?>
                	</td>
                    <td>
                		 <?php echo $product->current_qty;?>
                	</td>
                    <td>
                		 <?php echo $product->price_per_unit.' '.$product->currency_symbol;?>
                	</td>
                    <td>
                		 <?php echo $product->country;?>
                	</td>

                </tr>
            <?php }?>

        </tbody>
    </table>
    <ul class="pagination noprint"><?php if($pagination) echo $pagination;?></ul>
    <ul class="pagination noprint">
        <li><a onclick="myFunction()" class="noprint"><i class="fa fa-print"></i><?php echo lang('print_page');?></a></li>
    </ul>
</div>
