<div class="row">
    <div class="col-md-8 col-sm-12">
		<div class="portlet-body">
           <?php 
            if(!empty($row_data))
            {
                foreach($row_data as $column_name => $column_data)
                {
                    if(!empty($column_data)){?>
                        <div class="row static-info">
            				<div class="col-md-5 name">
            					 <?php echo $column_name;?>  :
            				</div>
            				<div class="col-md-7 value">
            					 <?php echo $column_data;?>
            				</div>
            			</div>
            <?php }
                }
             if(count($feed_back_text) != 0){?>
                
                <div class="table-responsive">
                    <div style="height: 9px;"></div>
                        <table class="table table-hover table-bordered table-striped" id="grid_table">
                            <thead>
                    			<tr><th colspan="2" style="text-align: center;"><?php echo lang('feed_back_text');?></th></tr>
                                <tr>    
                                    
                                    <th style="text-align: center;"><?php echo lang('field');?></th>
                                    <th style="text-align: center;"><?php echo lang('value');?></th>
                    			</tr>
                                
                			</thead>
                			
                            <tbody>
                                <?php foreach($feed_back_text as $key=>$val){?>		
                                    <tr>
                                        <td style="text-align: center;"><?php echo $key;?></td>
                                        <td style="text-align: center;"><?php echo $val;?></td>
                     		        </tr>
                               <?php }?>
                            </tbody>
                    </table>
                </div>
            <?php }?>
            <?php }else{?>
                    <div class="row static-info">
                        <div class="col-md-5 name">
        					  <?php echo lang("no_data");?>
        				</div>
   				    
            		</div>
                <?php }// if !empty?>
            
			
		</div>
    </div>
</div>