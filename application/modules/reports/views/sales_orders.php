<div class="portlet-body flip-scroll">
    <table class="table table-bordered table-striped table-condensed flip-content">
        <thead class="flip-content">
            <tr>
            	<th width="20%">
            		 <?php echo lang('order_id');?>
            	</th>
            	<th>
            		 <?php echo lang('order_start_date');?>
            	</th>
            	<th class="numeric">
            		 <?php echo lang('order_end_date');?>
            	</th>
                <th>
            		 <?php echo lang('country');?>
            	</th>
            	<th class="numeric">
            		 <?php echo lang('number_of_products');?>
            	</th>
            	<th class="numeric">
            		 <?php echo lang('total');?>
            	</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($orders_data as $order){?>
                <tr>
                    <td class="numeric">
                		 <a class="noprint" href="<?php echo base_url().'orders/admin_order/view_order/'.$order->id;?>" target="_blank"><?php echo $order->id;?></a>
                         <p class="print" style="display: none;"><?php echo $order->id;?></p>
                	</td>
                	<td>
                		 <?php echo date('Y/m/d H:i', $order->start_date);?>
                	</td>
                	<td>
                		 <?php echo date('Y/m/d H:i', $order->end_date);?>
                	</td>
                    <td>
                		 <?php echo $order->country;?>
                	</td>
                	<td class="numeric">
                		 <?php echo $order->products_count;?>
                	</td>
                	<td class="numeric">
                		 <?php echo $order->final_total.' '.$order->currency_symbol;?>
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
