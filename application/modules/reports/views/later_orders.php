<div class="portlet-body flip-scroll">
    <table class="table table-bordered table-striped table-condensed flip-content">
        <thead class="flip-content">
            <tr>
            	<th>
            		 <?php echo lang('order_id');?>
            	</th>
            	<th>
            		 <?php echo lang('order_total');?>
            	</th>
            	<th >
            		 <?php echo lang('currency');?>
            	</th>
                <th >
            		 <?php echo lang('order_rest_amount');?>
            	</th>
                <th>
            		 <?php echo lang('order_paid_amount');?>
            	</th>

            </tr>
        </thead>
        <tbody>
            <?php foreach($later_orders as $row){?>
                <tr>
                    <td >
                		 <a class="noprint" href="<?php echo base_url();?>orders/admin_order/view_order/<?php echo $row->id;?>" target="_blank"><?php echo $row->id;?></a>
                         <p class="print" style="display: none;"><?php echo $row->id;?></p>
                	</td>
                	<td>
                		 <?php echo $row->final_total;?>
                	</td>
                	<td>
                		 <?php echo $row->currency_symbol;?>
                	</td>
                    <td>
                		 <?php echo $row->rest_amount;?>
                	</td>
                	<td >
                		 <?php echo $row->paid_amount;?>
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
