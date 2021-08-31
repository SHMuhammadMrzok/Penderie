<div class="portlet-body flip-scroll">
    <table class="table table-bordered table-striped table-condensed flip-content">
        <thead class="flip-content">
            <tr>
            	<th style="text-align: center;">
            		 <?php echo lang('customer_name');?>
            	</th>
            	<th style="text-align: center;">
            		 <?php echo lang('email');?>
            	</th>
            	<th style="text-align: center;">
            		 <?php echo lang('customer_group');?>
            	</th>
                <th style="text-align: center;">
            		 <?php echo lang('status');?>
            	</th>
            	
            	<th class="numeric" style="text-align: center;">
            		 <?php echo lang('reward_points');?>
            	</th>
            	<th class="numeric" style="text-align: center;">
            		 <?php echo lang('orders_count');?>
            	</th>
                <th class="numeric" style="text-align: center;">
            		 <?php echo lang('user_balance');?>
            	</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($users_data as $user){?>
                <tr>
                    <td style="text-align: center;">
                		 <?php echo $user->first_name." ".$user->last_name;?>
                	</td>
                	<td style="text-align: center;">
                		 <?php echo $user->email;?>
                	</td>
                	<td style="text-align: center;">
                		 <?php echo $user->customer_group;?>
                	</td>
                    <td style="text-align: center;">
                		 <?php echo $user->status;?>
                	</td>
                	<td class="numeric" style="text-align: center;">
                		 <?php echo $user->reward_points;?>
                	</td>
                	<td class="numeric" style="text-align: center;">
                		 <?php echo $user->orders_count;?>
                	</td>
                    <td class="numeric" style="text-align: center;">
                		 <?php echo $user->balance;?>
                	</td>
                </tr>
            <?php }?>
        
        </tbody>
    </table>
    <div class="row no-margin alert alert-success"><?php echo lang('total_balances').' : '.$total_balances;?></div>
    
    <ul class="pagination noprint"><?php if($pagination) echo $pagination;?></ul>
    <ul class="pagination noprint">
        <li><a onclick="myFunction()" class="noprint"><i class="fa fa-print"></i><?php echo lang('print_page');?></a></li>
    </ul>
</div>
