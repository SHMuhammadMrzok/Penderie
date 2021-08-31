<!-- BEGIN STYLE CUSTOMIZER -->
<div class="theme-panel hidden-xs hidden-sm">
	<div class="toggler">
	</div>
	<div class="toggler-close">
	</div>
	<div class="theme-options">
		<div class="theme-option theme-colors clearfix">
			<span>
			THEME COLOR </span>
			<ul>
				<li class="color-default current tooltips" data-style="default" data-container="body" data-original-title="Default">
				</li>
				<li class="color-darkblue tooltips" data-style="darkblue" data-container="body" data-original-title="Dark Blue">
				</li>
				<li class="color-blue tooltips" data-style="blue" data-container="body" data-original-title="Blue">
				</li>
				<li class="color-grey tooltips" data-style="grey" data-container="body" data-original-title="Grey">
				</li>
				<li class="color-light tooltips" data-style="light" data-container="body" data-original-title="Light">
				</li>
				<li class="color-light2 tooltips" data-style="light2" data-container="body" data-html="true" data-original-title="Light 2">
				</li>
			</ul>
		</div>
		<div class="theme-option">
			<span>
			Layout </span>
			<select class="layout-option form-control input-sm">
				<option value="fluid" selected="selected">Fluid</option>
				<option value="boxed">Boxed</option>
			</select>
		</div>
		<div class="theme-option">
			<span>
			Header </span>
			<select class="page-header-option form-control input-sm">
				<option value="fixed" selected="selected">Fixed</option>
				<option value="default">Default</option>
			</select>
		</div>
		<div class="theme-option">
			<span>
			Top Menu Dropdown</span>
			<select class="page-header-top-dropdown-style-option form-control input-sm">
				<option value="light" selected="selected">Light</option>
				<option value="dark">Dark</option>
			</select>
		</div>
		<div class="theme-option">
			<span>
			Sidebar Mode</span>
			<select class="sidebar-option form-control input-sm">
				<option value="fixed">Fixed</option>
				<option value="default" selected="selected">Default</option>
			</select>
		</div>
		<div class="theme-option">
			<span>
			Sidebar Menu </span>
			<select class="sidebar-menu-option form-control input-sm">
				<option value="accordion" selected="selected">Accordion</option>
				<option value="hover">Hover</option>
			</select>
		</div>
		<div class="theme-option">
			<span>
			Sidebar Style </span>
			<select class="sidebar-style-option form-control input-sm">
				<option value="default" selected="selected">Default</option>
				<option value="light">Light</option>
			</select>
		</div>
		<div class="theme-option">
			<span>
			Sidebar Position </span>
			<select class="sidebar-pos-option form-control input-sm">
				<option value="left" selected="selected">Left</option>
				<option value="right">Right</option>
			</select>
		</div>
		<div class="theme-option">
			<span>
			Footer </span>
			<select class="page-footer-option form-control input-sm">
				<option value="fixed">Fixed</option>
				<option value="default" selected="selected">Default</option>
			</select>
		</div>
	</div>
</div>
<!-- END STYLE CUSTOMIZER -->

<?php
$module_row     = $this->admin_bootstrap->get_module_row();
$controller_row = $this->admin_bootstrap->get_controller_row();
$method_row     = $this->admin_bootstrap->get_method_row();

    
$active_module_name         = ($module_row) ? $module_row->name : 'NULL';
$active_controller_name     = ($controller_row) ? $controller_row->name : 'NULL';
$active_method_name         = ($method_row) ? $method_row->name : 'NULL';



?>

<!-- BEGIN PAGE HEADER-->
<h3 class="page-title noprint">
<?php echo $active_module_name; ?> <small><?php echo $active_controller_name; ?></small>
</h3>
<div class="page-bar">
	<ul class="page-breadcrumb">
		<li>
			<i class="fa fa-home"></i>
			<a href="<?php echo base_url();?>admin/dashboard"><?php echo lang('dashboard');  ?></a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<?php echo $active_module_name; ?>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url().@$controller_row->module_path.'/'.@$controller_row->controller;?>"><?php echo $active_controller_name; ?></a>
		</li>
        <?php ?>
       	<li>
		  <i class="fa fa-angle-right"></i>
          <?php echo $active_method_name; ?></a>
		</li>
	</ul>
	<!--<div class="page-toolbar">
		<div class="btn-group pull-right">
			<button type="button" class="btn btn-fit-height grey-salt dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true">
			Actions <i class="fa fa-angle-down"></i>
			</button>
			<ul class="dropdown-menu pull-right" role="menu">
				<li>
					<a href="#">Action</a>
				</li>
				<li>
					<a href="#">Another action</a>
				</li>
				<li>
					<a href="#">Something else here</a>
				</li>
				<li class="divider">
				</li>
				<li>
					<a href="#">Separated link</a>
				</li>
			</ul>
		</div>
	</div>-->
</div>
<!-- END PAGE HEADER-->