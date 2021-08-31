<div class="row">
    <div class="col-md-12 col-md-12">
		<div class="portlet-body">
            <div class="table-responsive">
				<table class="table table-hover table-bordered table-striped">
				<thead>
				<tr>
					<th><?php echo lang('username');?></th>
					<th><?php echo lang('use_date');?></th>
                    <th><?php echo lang('cart_id');?></th>
                    <th><?php echo lang('order_id');?></th>
					<?php if(!$is_total){?>
                        <th><?php echo lang('product');?></th>
    					<th><?php echo lang('category');?></th>
                    <?php }?>
                    <th><?php echo lang('type');?></th>
                    <!--<th><?php echo lang('discount');?></th>-->
				</tr>
				</thead>
				<tbody>
                    <?php foreach($uses_data as $row){?>
        				<tr>
        					<td><?php echo $row->username;?></td>
        					
        					<td><?php echo date('Y/m/d', $row->unix_time);?></td>
                            <td><?php $cart_text  = $row->cart_id==0 ? lang('is_order') : $row->cart_id; echo $cart_text;?></td>
                            <td><?php $order_text = $row->order_id==0 ? lang('not_ordered') : '<a target="_blank" href="'.base_url().'orders/admin_order/view_order/'.$row->order_id.'">'.$row->order_id.'</a>'; echo $order_text?></td>
        					<?php if(!$is_total){?>
                                <td><?php echo $row->product_name;?></td>
                                <td><?php echo $row->cat_name;?></td>
                            <?php }?>
                            <td><?php echo $row->coupon_type;?></td>
                            <!--<td><?php echo $row->coupon_discount;?></td>-->
                            
        				</tr>
				<?php }?>
				
				
				</tbody>
				</table>
		</div>
            
			
		</div>
    </div>
</div>