<?php
    if($customer_groups_data){
     foreach($customer_groups_data as $row)
     {?>
		<tr>
            
             <td width="5%">
				<input type="checkbox" class="group-checkable checkbox" name="customer_group_id[]" value="<?php echo $row->id;?>" />
			</td>
			<td>
				<?php echo $row->title;?>
			</td>
			
            <td>
				<?php echo $row->name;?>
			</td>
			
            <td>
				 <?php echo $row->discount_percentage;?>
			</td>
			
            <td>
				 <?php echo $row->product_limit_per_order; ?>
			</td>
            
            <td>
				
                <?php if($this->admin_bootstrap->has_permission('view')){?>
					<a class="btn btn-sm yellow filter-submit margin-bottom" href="<?php echo base_url();?>users/customer_groups/read/<?php echo $row->id;?>">
                        <i class="fa fa-search"></i> <?php echo lang('view');?>
                    </a>
                <?php }?>
				<?php if($this->admin_bootstrap->has_permission('edit_form')){?>
                    <a class="btn btn-sm blue filter-submit margin-bottom" href="<?php echo base_url();?>users/customer_groups/edit_form/<?php echo $row->id;?>" >
                    <i class="fa fa-edit"></i> <?php echo lang('edit');?>
                </a>
				<?php }?>
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
        <tr><td colspan="6"><span style="text-align: center; display: block;"><?php echo lang('empty_table');?></span></td></tr>    
    <?php 
    }?>