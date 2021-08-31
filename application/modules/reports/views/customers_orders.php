<div class="portlet-body flip-scroll">
    <table class="table table-bordered table-striped table-condensed flip-content">
        <thead class="flip-content">
            <tr>
            	<th>
            		 <?php echo lang('customer_name');?>
            	</th>
            	<th>
            		 <?php echo lang('email');?>
            	</th>
            	<th >
            		 <?php echo lang('customer_group');?>
            	</th>
                <th >
            		 <?php echo lang('status');?>
            	</th>
            	<th class="numeric">
            		 <?php echo lang('orders_count').' ('.lang('completed').')';?>
            	</th>
                <th class="numeric">
            		 <?php echo lang('products_number');?>
            	</th>
                <th class="numeric">
            		 <?php echo lang('total');?>
            	</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($users_data as $user){?>
                <tr>
                    <td >
                		 <?php echo $user->first_name." ".$user->last_name;?>
                	</td>
                	<td>
                		 <?php echo $user->email;?>
                	</td>
                	<td>
                		 <?php echo $user->customer_group;?>
                	</td>
                    <td>
                		 <?php echo $user->status;?>
                	</td>
                	<td class="numeric">
                		 <?php echo $user->orders_count;?>
                	</td>
                	<td class="numeric">
                		 <?php echo $user->all_products_count;?>
                	</td>
                    <td class="numeric">
                		 <?php echo $user->orders_total;?>
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
