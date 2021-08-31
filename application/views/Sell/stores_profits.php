<div class="right-content">
    <table class="table table-striped ">
        <thead>
        <tr>
        	<th width="20%">
        		 <span><?php echo lang('order_id');?></span>
        	</th>
        	<th>
        		 <span><?php echo lang('name_of_store');?></span>
        	</th>
            <th>
        		 <span><?php echo lang('owner_profit');?></span>
        	</th>
        	<th class="numeric">
        		 <span><?php echo lang('store_profit');?></span>
        	</th>
        </tr>
            
        </thead>
        <tbody>
            <?php foreach($profits_rows as $row){?>
                <tr>
                    <td class="numeric">
                		 <a href="<?php echo base_url().'orders/admin_order/view_order/'.$row->order_id;?>" target="_blank"><?php echo $row->order_id;?></a>
                	</td>
                	<td>
                		 <?php echo $row->store_name;?>
                	</td>
                    <td>
                		 <?php echo $row->owner_profit.' '.$row->currency_symbol;?>
                	</td>
                	<td class="numeric">
                		 <?php echo $row->store_profit.' '.$row->currency_symbol;?>
                	</td>
                </tr>
            <?php }?>
        
        </tbody>
    </table>
    <ul class="pagination"><?php echo $pagination; ?></ul>
</div>