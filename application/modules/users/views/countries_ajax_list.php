<?php
    if($countries_data){
     foreach($countries_data as $row)
     {?>
		<tr>
            
             <td width="5%">
				<input type="checkbox" class="group-checkable checkbox" name="customer_group_id[]" value="<?php echo $row->id;?>" />
			</td>
			<td>
				<?php echo $row->name;?>
			</td>
			
            <td>
				<?php echo $row->currency;?>
			</td>
			
            <td>
                <a href="<?php echo base_url();?>assets/uploads/<?php echo $row->flag;?>" class="image-thumbnail" data-rel="fancybox-button">
				    <img class="img-responsive" src="<?php echo base_url();?>assets/uploads/<?php echo $row->flag;?>" alt="">
				</a>
				 
			</td>
			
            
            <td>
				
                <?php if($this->admin_bootstrap->has_permission('view')){?>
					<a class="btn btn-sm yellow filter-submit margin-bottom" href="<?php echo base_url();?>users/countries/read/<?php echo $row->id;?>">
                        <i class="fa fa-search"></i> <?php echo lang('view');?>
                    </a>
                <?php }?>
				<?php if($this->admin_bootstrap->has_permission('edit_form')){?>
                    <a class="btn btn-sm blue filter-submit margin-bottom" href="<?php echo base_url();?>users/countries/edit_form/<?php echo $row->id;?>" >
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