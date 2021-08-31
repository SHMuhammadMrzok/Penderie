<?php
    if($users_log_data){
     foreach($users_log_data as $row)
     {?>
		<tr>
            
             <td width="5%">
				<input type="checkbox" class="group-checkable checkbox" name="customer_group_id[]" value="<?php echo $row->id;?>" />
			</td>
			<!--<td>
                <?php echo lang('user')." ".$row->username." ".lang('did')." ".$row->action_name." ".lang('in_module')." ".$row->module." ".lang('in_page')." ".$row->controller;?>
			</td>-->
			
            <td>
				<?php echo $row->username;?>
			</td>
			
            <td>
				 <?php echo $row->action_name;?>
			</td>
            <td>
				 <?php echo $row->module;?>
			</td>
            <td>
				 <?php echo $row->controller;?>
			</td>
			
            <td>
				 <?php echo date("Y-m-d H:i:s",$row->unix_time); ?>
			</td>
            <td>
				 <?php echo $row->ip_address;?>
			</td>
            
            <td>
				
                <?php if($this->admin_bootstrap->has_permission('delete')){?>
                    <button class="btn btn-sm red filter-cancel delete_alert delete-btn" value="<?php echo $row->id;?>" >
                        <i class="fa fa-times"></i> <?php echo lang('delete');?>
                    </button>
                <?php }?>
			</td>
            
			
		</tr>
        
    <?php 
      }
    }
    else
    {?>
        <tr><td colspan="9"><span style="text-align: center; display: block;"><?php echo lang('empty_table');?></span></td></tr>    
    <?php 
    }?>