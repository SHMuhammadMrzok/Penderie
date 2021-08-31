<div class="portlet-body flip-scroll">
    <table class="table table-bordered table-striped table-condensed flip-content">
        <thead class="flip-content">
            <tr>
            	<th>
            		 <?php echo lang('coupon_name');?>
            	</th>
            	<th>
            		 <?php echo lang('coupon_code');?>
            	</th>
            	<th class="numeric">
            		 <?php echo lang('orders_count');?>
            	</th>
            	<th class="numeric">
            		 <?php echo lang('total');?>
            	</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($coupons_data as $coupon){?>
                <tr>
                    <td>
                		 <a class="noprint" href="<?php echo base_url()."coupon_codes/admin_coupon_codes/read/".$coupon->id."/".$lang_id;?>" target="_blank"><?php echo $coupon->name;?></a>
           	             <p class="print" style="display: none;"><?php echo $coupon->name;?></p>
                    </td>
                	<td>
                		 <?php echo $coupon->code;?>
                	</td>
                	<td class="numeric">
                		 <?php echo $coupon->uses_count;?>
                	</td>
                	<td class="numeric">
                		 <?php echo $coupon->total_discount;?>
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
