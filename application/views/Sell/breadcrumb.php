<?php 
$module_row     = $this->admin_bootstrap->get_module_row();
$controller_row = $this->admin_bootstrap->get_controller_row();
$method_row     = $this->admin_bootstrap->get_method_row();

    
$active_module_name         = ($module_row) ? $module_row->name : '';
$active_controller_name     = ($controller_row) ? $controller_row->name : '';
$active_method_name         = ($method_row) ? $method_row->name : '';

$active_module=$this->admin_bootstrap->get_module();
$active_controller=$this->admin_bootstrap->get_controller();
$active_method=$this->admin_bootstrap->get_method();
?>

<div class="title">
    <h3><?php echo $active_controller_name; ?></h3>
    
    <?php
                 
     $check_add_method     = $this->acl_model->check_add_method($active_module, $active_controller, $active_method);
     $check_add_permission = $this->acl_model->check_add_permission($active_module, $active_controller);
     
     
     if($check_add_method && $check_add_permission && ($active_controller != 57))
     {?>
        
        <button onclick="window.location.href='<?php echo base_url().$module.'/'.$controller.'/add';?>'">
            <a href="<?php echo base_url().$controller_row->module_path."/".$controller."/add";?>" class="btn btn-default btn-sm">
		      <i class="fa fa-plus"></i> <?php echo lang('add');?> 
            </a>
        </button>  
	
    <?php }?>
    
</div>