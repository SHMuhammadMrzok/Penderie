<div class="form">
    <?php if(isset($error_msg)){?>
        <span class="error"><?php echo $error_msg;?></span>
    <?php }else{?>
        <div class="tabbable-custom form">
    	   <div class="tab-content">
            
            <div class="tab-pane active" id="tab_cats">
    	      <div class="form-body">
                <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/template/admin/global/plugins/jstree/dist/themes/default/style.min.css"/>
                <script src="<?php echo base_url();?>assets/template/admin/global/plugins/jstree/dist/jstree.min.js"></script>
                <style>
                    .jstree-wholerow {
                      width: 1065px !important;
                      margin-left: 6px !important;
                    }
                </style>
                
                <script>
                    $(function(){
                        
                       /*************************************************************************************/
                        $('#tree_2').jstree({
                                'plugins': ["wholerow", "checkbox", "types"]
                                
                            });
                       
                       /*************************************************************************************/ 
                     $('#submit_form_permissions').click(function(e){
                            e.preventDefault();
                            var selectedElmsIds = $('#tree_2').jstree("get_selected");
                            
                            
                            $.ajax({
                        		type: 'post',
                        		data:{ checked_nodes: selectedElmsIds, user_id: <?php echo $user_id?>},
                       			url:  $("#permissions_user_form").attr("action"),
                         		success:function(data){
                         		  $("#message").html(data);
                         		}
                       		});
                            
                     })
           
                     /*************************************************************************/
                     
                    })
                    
                    </script>
                
<form id="permissions_user_form" method="post" action="<?php echo base_url()?>users/admin_users/save_store_permission" class="form-horizontal form-bordered">
    
    <div class="form-group last">
    	<div id="tree_2" role="tree">
            <?php foreach($stores_permissions as $store){?>        
               <ul>
                    <li role="treeitem_<?php echo $store->store_id;?>" aria-expanded="true" id="check_all_<?php echo $store->store_id;?>" aria-selected="false" data-jstree='{ "icon" : "fa fa-folder-open icon-state-warning ","opened" : false }'>
                        <a class="jstree-anchor_<?php echo $store->store_id;?>" href="#"><?php echo $store->name.' ( '. lang('check_all').' )';?></a>
                        <ul>
                            <?php foreach($modules as $module_id=>$module){
                                    $module_name=$module['module_name'];?>
                       
                                <li role="treeitem_<?php echo $store->store_id;?>" aria-expanded="true" id="m_<?php echo $module_id.'_'.$store->store_id; ?>" aria-selected="false" data-jstree='{ "icon" : "<?php echo $module['module_icon_class']; ?> icon-state-success ","opened" : false }'>
                                   <a class="jstree-anchor_<?php echo $store->store_id;?>" href="#"> <?php echo $module_name; ?> </a>
                                    <ul role="group" class="jstree-children">
                                        <?php  $controller_name='';
                                               foreach($controllers as $controller){ 
                                                    if($controller->module_id== $module_id && ($controller->id != 53)&& ($controller->id != 54))
                                                    {
                                                        $controller_check="";
                                                        
                                                        if(in_array($controller->id,$store->user_store_old_controllers))
                                                        {
                                                            $controller_check="checked";
                                                        }
                                                        
                                                        $controller_name = $controller->controller_name;
                                                        
                                            ?>
                                        
                                           <li role="treeitem" id="c_<?php echo $controller->id.'_'.$store->store_id; ?>" aria-selected="false" aria-expanded="true" data-jstree='{ "icon" : "<?php echo ($controller->icon_class=='')? 'fa  fa-file-o' : $controller->icon_class; ?> icon-state-danger ","opened" : false }'>
                                                <a class="jstree-anchor" href="#"><?php echo $controller_name; ?> </a>
                                                
                                                <ul role="group" class="jstree-children" style="">
                                                
                                                    <?php 
                                                        foreach($store->permissions["{$controller->id}"] as $perm)
                                                        {
                                                            $permission_check="";
                                                            if(isset($store->user_store_old_permissions["{$controller->id}"]))
                                                            {
                                                                if(in_array($perm->id, $store->user_store_old_permissions["{$controller->id}"]))
                                                                {
                                                                    $permission_check = ',"selected" : true';
                                                                }
                                                            }
                                                                
                                                            $perm_name = $perm->name;     
                                                        ?>
                                                              
                                                        <li role="treeitem" id="p_<?php echo $perm->id.'_'.$store->store_id; ?>_c_<?php echo $controller->id.'_'.$store->store_id; ?>" data-jstree='{ "icon" : "fa fa-toggle-on font-blue " <?php echo $permission_check;?> }'>
                                                            <a class="jstree-anchor" href="#"><?php echo $perm_name; ?></a>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                            </li>
                                      <?php }
                                       }?>
                                    
                                    </ul>
                                </li>
                        
                        <?php }?>
                        </ul>
                    </li><!--check all-->
                    
                </ul>
            
            <input type="hidden" name="store_id[]" value="<?php echo $store->store_id?>" />
            <input type="hidden" name="checked_nodes" id="checked_nodes_<?php echo $store->store_id;?>" value="" />
            
        <?php }?>
        
        <input type="hidden" name="user_id" value="<?php echo $user_id?>" />
    </div><!-- tree-->
</div><!-- form group-->
</form> 
            </div>  
         
    	</div>
        
            
    	</div>
    </div>
    <?php }?> 		
    
    <div id="message"></div>
    <div class="form-actions">
		<div class="row">
			<div class="col-md-offset-3 col-md-9">
				<button id="submit_form_permissions" type="button" class="btn purple"><i class="fa fa-check"></i> <?php echo lang('submit')?></button>
			</div>
		</div>
	</div>
    
</div>

