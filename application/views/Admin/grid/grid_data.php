<?php
    if(count($grid_data))
    {
     foreach($grid_data as $row)
     {?>
		<tr data-sort="<?php if(isset($row['sort'])){ echo $row['sort'];}?>" data-id="<?php echo $row['id'];?>" class="sorting">
             <td width="5%">
				<input type="checkbox" class="group-checkable checkbox" name="row_id[]" value="<?php echo $row['id'];?>" />
			</td>

			<?php
            foreach($row as $column => $column_data)
            {
                if(isset($hidden_fields) && !in_array($column, $hidden_fields))
                { ?>
                <td style="text-align: center;">
          				<?php echo $column_data;?>
          			</td>
            <?php
                }
            }
            ?>

            <?php if(! (isset($unset_view) && isset($unset_edit) && isset($unset_delete))){?>
                <td>
                    <?php
                    if(!isset($unset_view))
                    {?>
                        <script>
                           var display_lang_id = $('#lang_id').val();
                           $("#read_btn").attr("href", "<?php echo base_url().$module."/".$controller."/"?>read/<?php echo $row['id']."/";?>.display_lang_id");
                        </script>
                       <?php
                        if($this->admin_bootstrap->has_permission('read'))
                        {?>
      					    <a class="btn btn-sm yellow filter-submit margin-bottom" href="<?php echo base_url().$module."/".$controller."/"?>read/<?php echo $row['id']."/".$display_lang_id;?>" >
                                <i class="fa fa-search"></i> <?php echo lang('view');?>
                            </a>
                    <?php
                        }
                    }?>

    				<?php
                     if(!isset($unset_edit))
                     {
                         if($this->admin_bootstrap->has_permission('edit'))
                         {?>
                            <a class="btn btn-sm blue filter-submit margin-bottom" href="<?php echo base_url().$module."/".$controller."/";?>edit/<?php echo $row['id'];?>" >
                                <i class="fa fa-edit"></i> <?php echo lang('edit');?>
                            </a>
    				<?php
                         }
                     }?>
                    <?php
                     if(!isset($unset_delete))
                     {
                         if($this->admin_bootstrap->has_permission('delete'))
                         {?>
                            <button class="btn btn-sm red filter-cancel delete_alert delete-btn" value="<?php echo $row['id'];?>" >
                                <i class="fa fa-times"></i> <?php echo lang('delete');?>
                            </button>
                     <?php
                         }
                     }?>
    			</td>
             <?php }?>


		</tr>

    <?php
      }
    }
    else
    {?>
        <tr><td colspan="13"><span style="text-align: center; display: block;"><?php echo lang('empty_table');?></span></td></tr>
    <?php
    }?>
