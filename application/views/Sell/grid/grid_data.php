<?php
    if(count($grid_data))
    {
        foreach($grid_data as $row)
     {?>
        <tr data-sort="<?php if(isset($row['sort'])){ echo $row['sort'];}?>" data-id="<?php echo $row['id'];?>" class="sorting">
            <td width="5%">
				<input type="checkbox" class="group-checkable checkbox" name="row_id[]" value="<?php echo $row['id'];?>" />
			</td>

            <?php foreach($row as $column => $column_data)
            {
                if(isset($hidden_fields) && !in_array($column, $hidden_fields))
                {?>
                <td>
    				<?php echo $column_data;?>
    			</td>
            <?php
                }
            }
            ?>
                <?php if(! (isset($unset_view) && isset($unset_edit) && isset($unset_delete))){?>
                <td>
                <div class="table-buttons">
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
      					    <button onclick="window.location.href='<?php echo base_url().$module."/".$controller."/"?>read/<?php echo $row['id']."/".$display_lang_id;?><?php echo $seller_all_products==1?'/1':'';?>'">
                                <a href="#" >
                                    <?php echo lang('view');?>
                                </a>
                            </button>
                    <?php
                        }
                    }?>

    				<?php
                     if(!isset($unset_edit))
                     {
                         if($this->admin_bootstrap->has_permission('edit'))
                         {?>
                            <button onclick="window.location.href='<?php echo base_url().$module."/".$controller."/";?>edit/<?php echo $row['id'];?>'">
                                <a class="green" href="#" >
                                    <?php echo lang('edit');?>
                                </a>
                            </button>
    				<?php
                         }
                     }?>
                    <?php
                     if(!isset($unset_delete))
                     {
                         if($this->admin_bootstrap->has_permission('delete'))
                         {?>
                            <button class="button red delete_alert delete-btn" value="<?php echo $row['id'];?>" >
                                <?php echo lang('delete');?>
                            </button>
                     <?php
                         }
                     }?>
                     </div>
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
