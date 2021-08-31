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
        //e.preventDefault();
        var selectedElmsIds = $('#tree_2').jstree("get_selected");
        
        $.ajax({
    		type: 'post',
    		data:{ checked_nodes: selectedElmsIds, group_id: <?php echo $group_id?>},
   			url:  $("#permissions_user_form").attr("action"),
     		success:function(data){
     		  $("#message").html(data);
     		}
   		});
 })
   
 /*************************************************************************/
 
})

</script>
<form id="permissions_user_form" method="post" action="<?php echo base_url()?>users/permissions_group/save_permission" class="form-horizontal form-bordered">
            <div class="form-group last">
        	<div id="tree_2" role="tree">
               <ul>
                    <li role="treeitem" aria-expanded="true" id="check_all" aria-selected="false" data-jstree='{ "icon" : "fa fa-folder-open icon-state-warning ","opened" : true }'>
                        <a class="jstree-anchor" href="#"><?php echo lang('check_all')?></a>
                    <ul>
                    <?php foreach($modules as $module_id=>$module){
                            $module_name=$module['module_name'];
                    ?>
                   
                    <li role="treeitem" aria-expanded="true" id="m_<?php echo $module_id; ?>" aria-selected="false" data-jstree='{ "icon" : "<?php echo $module['module_icon_class']; ?> icon-state-success ","opened" : true }'>
                       <a class="jstree-anchor" href="#"> <?php echo $module_name; ?> </a>
                        <ul role="group" class="jstree-children">
                            <?php  $controller_name='';
                                    foreach($controllers as $controller){ 
                                        if($controller->module_id== $module_id){
                                             $controller_check="";
                                            if(in_array($controller->id,$group_old_controllers))
                                            {
                                                $controller_check="checked";
                                            }
                                            
                                            $controller_name=$controller->controller_name;
                                            
                                ?>
                           <li role="treeitem" id="c_<?php echo $controller->id; ?>" aria-selected="false" aria-expanded="true" data-jstree='{ "icon" : "<?php echo ($controller->icon_class=='')? 'fa  fa-file-o' : $controller->icon_class; ?> icon-state-danger ","opened" : true }'>
                                <a class="jstree-anchor" href="#"><?php echo $controller_name; ?> </a>
                                <ul role="group" class="jstree-children" style="">
                                <?php 
                                      //  print_r($permissions);die();
                                    foreach($permissions["{$controller->id}"] as $perm)
                                        {
                                            $permission_check="";
                                            if(isset($group_old_permissions["{$controller->id}"]))
                                            {
                                                if(in_array($perm->id,$group_old_permissions["{$controller->id}"]))
                                                {
                                                    $permission_check=',"selected" : true';
                                                }
                                            }
                                            
                                            $perm_name=$perm->name;
                                            
                                    ?>
                                              
                                    <li role="treeitem" id="p_<?php echo $perm->id; ?>_c_<?php echo $controller->id; ?>" data-jstree='{ "icon" : "fa fa-toggle-on font-blue " <?php echo $permission_check;?> }'>
                                        <a class="jstree-anchor" href="#"><?php echo $perm_name; ?></a>
                                    </li>
                                <?php } ?>
                                </ul>
                            </li>
                          <?php } }?>
                        </ul>
                    </li>
                    
                    <?php }?>
                    </ul>
                </li><!--check all-->
                </ul>
                <input type="hidden" name="group_id" value="<?php echo $group_id?>" />
                <input type="hidden" name="checked_nodes" id="checked_nodes" value="" />
             </div><!-- tree-->
             </div><!-- form group-->
              <div id="message"></div>
             <div class="form-actions">
				<div class="row">
					<div class="col-md-offset-3 col-md-9">
						<button id="submit_form_permissions" type="button" class="btn purple"><i class="fa fa-check"></i> <?php echo lang('submit')?></button>
					</div>
				</div>
			</div>
           </form>  