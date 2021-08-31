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
                foreach($result as $msg){
              ?>	
                <tr style="text-align: center;">       
                    <td style="width: 50%;"><?php echo $msg;?></td>
                </tr>
              <?php }?>
              
              <tr>
                <td colspan="2" style="text-align: center;">
                    <a class="btn btn-sm default filter-submit margin-bottom" href="<?php echo base_url();?>products/check_serials/">
                        <?php echo lang('Back');?>
                    </a>
                </td>
                
              </tr>
            </tbody>
            
    </table>
</div>