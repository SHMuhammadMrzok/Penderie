<div class="table-responsive">
    <div style="height: 9px;"></div>
        <table class="table table-hover table-bordered table-striped" id="grid_table">
			
            <thead>
    			<tr>    
                    <th colspan="2" style="text-align: center;"><?php echo lang('result');?></th>
    			</tr>
			</thead>
			
            <tbody>	
              <?php
                foreach($serials_msgs as $msg_array){
                foreach($msg_array as $msg){
              ?>	
                <tr>       
                    <td colspan="2" style="text-align: center;"><?php echo $msg;?></td>
                </tr>
              <?php }
                  }?>
              <tr style="text-align: center;">
                <td>
                    <a class="btn btn-sm default filter-submit margin-bottom" href="<?php echo base_url();?>products/admin_products_serials">
                        <?php echo lang('go_to_serials');?>
                    </a>
                </td>
                <td>
                    <a class="btn btn-sm default filter-submit margin-bottom" href="<?php echo base_url();?>products/admin_purchase_orders/">
                        <?php echo lang('go_to_purchase_orders');?>
                    </a>
                </td>
              </tr>
            </tbody>
            
    </table>
</div>