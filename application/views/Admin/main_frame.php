<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html dir="<?php echo $_SESSION['direction'];?>" class="no-js" style="direction: <?php echo $_SESSION['direction'];?>;">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="utf-8"/>
<?php
if($this->session->userdata('direction')=='ltr'){   $direction = '';}
else{  $direction = '-rtl';}
?>
<title><?php echo lang('site_title');?></title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1" name="viewport"/>
<meta content="" name="description"/>
<meta content="" name="author"/>
<!-- BEGIN GLOBAL MANDATORY STYLES -->
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url();?>assets/template/admin/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url();?>assets/template/admin/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url();?>assets/template/admin/global/plugins/bootstrap/css/bootstrap<?php echo $direction;?>.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url();?>assets/template/admin/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url();?>assets/template/admin/global/plugins/bootstrap-switch/css/bootstrap-switch<?php echo $direction;?>.min.css" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL PLUGIN STYLES -->
<link href="<?php echo base_url();?>assets/template/admin/global/plugins/bootstrap-select/bootstrap-select.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url();?>assets/template/admin/global/plugins/select2/select2.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url();?>assets/template/admin/global/plugins/jquery-multi-select/css/multi-select.css" rel="stylesheet" type="text/css"/>

<link href="<?php echo base_url();?>assets/template/admin/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url();?>assets/template/admin/global/plugins/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" type="text/css"/>

<?php
if(isset($css_files))
{
  foreach($css_files as $css_file)
  {
    echo '<link href="'. base_url() . 'assets/template/admin/' . $css_file . '" rel="stylesheet" type="text/css"/>';
  }
}
?>

<link href="<?php echo base_url();?>assets/template/admin/global/plugins/jqvmap/jqvmap/jqvmap.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url();?>assets/template/admin/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css"/>

<link href="<?php echo base_url();?>assets/template/admin/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url();?>assets/template/admin/global/plugins/typeahead/typeahead.css" rel="stylesheet" type="text/css"/>

<!-- END PAGE LEVEL PLUGIN STYLES -->
<!-- BEGIN PAGE STYLES -->
<link href="<?php echo base_url();?>assets/template/admin/global/plugins/bootstrap-toastr/toastr.min.css" rel="stylesheet" type="text/css"/>

<!-- END PAGE STYLES -->
<!-- BEGIN THEME STYLES -->
<!-- DOC: To use 'rounded corners' style just load 'components-rounded.css' stylesheet instead of 'components.css' in the below style tag -->
<link href="<?php echo base_url();?>assets/template/admin/global/css/components<?php echo $direction;?>.css" id="style_components" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url();?>assets/template/admin/global/css/plugins<?php echo $direction;?>.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url();?>assets/template/admin/layout/css/layout<?php echo $direction;?>.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url();?>assets/template/admin/layout/css/themes/default<?php echo $direction;?>.css" rel="stylesheet" type="text/css" id="style_color"/>
<link href="<?php echo base_url();?>assets/template/admin/layout/css/custom<?php echo $direction;?>.css" rel="stylesheet" type="text/css"/>

<link href="<?php echo base_url();?>assets/template/admin/layout/css/custom<?php echo $direction;?>.css" rel="stylesheet" type="text/css"/>

<link rel="stylesheet" type="text/css" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/flick/jquery-ui.css">
<link href="<?php echo base_url();?>assets/template/admin/tags/jquery.tagit.css" rel="stylesheet" type="text/css">
<!-- END THEME STYLES -->

<link rel="shortcut icon" href="<?php echo $images_path . $this->config->item('fav_ico');?>" />

<script src="<?php echo base_url();?>assets/template/admin/global/plugins/jquery.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/template/admin/global/plugins/jquery-migrate.min.js" type="text/javascript"></script>

<!--START upload single image like in GROCERY CRUD-->

<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>assets/grocery_crud/css/jquery_plugins/chosen/chosen.css" />
<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>assets/grocery_crud/css/ui/simple/jquery-ui-1.10.1.custom.min.css" />
<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>assets/grocery_crud/css/jquery_plugins/file_upload/file-uploader.css" />
<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>assets/grocery_crud/css/jquery_plugins/file_upload/jquery.fileupload-ui.css" />
<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>assets/grocery_crud/css/jquery_plugins/fancybox/jquery.fancybox.css" />
<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>assets/grocery_crud/css/jquery_plugins/file_upload/fileuploader.css" />


<script src="<?php echo base_url();?>assets/grocery_crud/js/jquery_plugins/jquery.chosen.min.js"></script>
<script src="<?php echo base_url();?>assets/grocery_crud/js/jquery_plugins/config/jquery.chosen.config.js"></script>
<script src="<?php echo base_url();?>assets/grocery_crud/js/jquery_plugins/ui/jquery-ui-1.10.3.custom.min.js"></script>
<script src="<?php echo base_url();?>assets/grocery_crud/js/jquery_plugins/tmpl.min.js"></script>
<script src="<?php echo base_url();?>assets/grocery_crud/js/jquery_plugins/jquery.fancybox-1.3.4.js"></script>
<script src="<?php echo base_url();?>assets/grocery_crud/js/jquery_plugins/jquery.fileupload.js"></script>
<script src="<?php echo base_url();?>assets/grocery_crud/js/jquery_plugins/config/jquery.fileupload.config.js"></script>
<script src="<?php echo base_url();?>assets/grocery_crud/js/jquery_plugins/config/jquery.fancybox.config.js"></script>
<!--END upload single image like in GROCERY CRUD-->

<script>
var tb_pathToImage = "<?php echo base_url();?>assets/template/admin/img/loadingAnimation.gif";
</script>
<style>
    @media print
    {
    .noprint {display:none;}
    @page { margin: 0; }
    .portlet.box.green-meadow{border: none !important;}
    .print{display: block !important;}
    body { margin: 1.6cm; }
    }
</style>

</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<!-- DOC: Apply "page-header-fixed-mobile" and "page-footer-fixed-mobile" class to body element to force fixed header or footer in mobile devices -->
<!-- DOC: Apply "page-sidebar-closed" class to the body and "page-sidebar-menu-closed" class to the sidebar menu element to hide the sidebar by default -->
<!-- DOC: Apply "page-sidebar-hide" class to the body to make the sidebar completely hidden on toggle -->
<!-- DOC: Apply "page-sidebar-closed-hide-logo" class to the body element to make the logo hidden on sidebar toggle -->
<!-- DOC: Apply "page-sidebar-hide" class to body element to completely hide the sidebar on sidebar toggle -->
<!-- DOC: Apply "page-sidebar-fixed" class to have fixed sidebar -->
<!-- DOC: Apply "page-footer-fixed" class to the body element to have fixed footer -->
<!-- DOC: Apply "page-sidebar-reversed" class to put the sidebar on the right side -->
<!-- DOC: Apply "page-full-width" class to the body element to have full width page without the sidebar menu -->
<body class="page-header-fixed page-quick-sidebar-over-content page-style-square">
<!-- BEGIN HEADER -->
<div class="page-header navbar navbar-fixed-top">
    <!-- BEGIN HEADER INNER -->
    <div class="page-header-inner">
	<!-- BEGIN LOGO -->
	<div class="page-logo">
            <a href="<?php echo base_url();?>admin/dashboard">
            <img src="<?php echo $images_path . $this->config->item('logo');?>" height="30" alt="logo" class="logo-default"/>
            </a>
            <div class="menu-toggler sidebar-toggler hide">
                    <!-- DOC: Remove the above "hide" to enable the sidebar toggler button on header -->
            </div>
	</div>
	<!-- END LOGO -->
        <!-- BEGIN RESPONSIVE MENU TOGGLER -->
        <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
        </a>
        <!-- END RESPONSIVE MENU TOGGLER -->
        <!-- BEGIN TOP NAVIGATION MENU -->
	<div class="top-menu">
	<ul class="nav navbar-nav pull-right">

    <?php if(!$store_owner && !$is_driver){?>
    <!-- BEGIN NOTIFICATION DROPDOWN -->
    <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
    <?php $notifications = $this->admin_bootstrap->get_admin_notification();
          $notifications_count = $this->admin_bootstrap->get_admin_unread_notifications();
    ?>
        <li class="dropdown dropdown-extended dropdown-notification" id="header_notification_bar">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
               <i class="icon-bell"></i>
               <span class="badge badge-default" id="notification_count"><?php echo $notifications_count;?> </span>
            </a>
		    <ul class="dropdown-menu">
                <li class="external">
                        <h3 id="pendinig_notifications"><span class="bold" id="pend_notification_count"><?php echo $notifications_count;?></span><span class="bold"><?php echo "   ".lang('new')."   ";?></span><?php echo lang('notifications')?> </h3>
                        <?php //if(!$store_owner){?>
                            <a href="<?php echo base_url();?>notifications/notification/"><?php echo lang('view_all')?></a>
                        <?php //}?>
                </li>
                <li>
                   	<ul class="dropdown-menu-list scroller" style="height: 250px;" data-handle-color="#637283">
		              <?php foreach($notifications as $row ){?>
                        <li>
                            <a href="javascript:;">

                                <span class="details">
                                    <span class="label label-sm label-icon label-success">
                                       <i class="fa fa-plus"></i>
                                    </span>
                                        <?php echo $row->notification_text; ?>
                                </span>

                                <?php
                                   $periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
                                   $lengths = array("60","60","24","7","4.35","12","10");

                                   $now     = time();
                                   $difference    = $now - $row->unix_time;

                                   for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
                                       $difference /= $lengths[$j];
                                   }

                                   $difference = round($difference);

                                   if($difference  != 1) {
                                       $periods[$j].= "s";
                                   }

                                   echo "$difference $periods[$j] 'ago' ";
                                ?>
                            </a>
                        </li>
		              <?php } ?>
		           </ul>
                </li>
		      </ul>
            </li>

            <!-- END NOTIFICATION DROPDOWN -->
			<?php }?>
				<!-- BEGIN USER LOGIN DROPDOWN -->
				<!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
			    <?php
                        $active_module=$this->admin_bootstrap->get_module();
                        $active_controller=$this->admin_bootstrap->get_controller();
                        $active_method=$this->admin_bootstrap->get_method();
                ?>
                <?php //if($active_controller == 'admin' && $active_method == 'dashboard'){?>
                    <li class="dropdown dropdown-language">
            					<a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
            					<img alt="" src="<?php echo base_url();?>assets/template/admin/global/img/flags/<?php echo $active_language->flag;?>"/>
            					<span class="langname">	<?php echo $active_language->name;?> </span>
            					<i class="fa fa-angle-down"></i>
            					</a>
            					<ul class="dropdown-menu dropdown-menu-default">
            						<?php foreach($structure_languages as $lang){?>
                        	  <li>
                							 <a href="<?php echo base_url();?>admin/change_lang/<?php echo $lang->language ;?>">
                							 <img alt="" src="<?php echo base_url();?>assets/template/admin/global/img/flags/<?php echo $lang->flag;?>"/> <?php echo $lang->name;?> </a>
              						   </li>
                          <?php }?>
                     	</ul>
            				</li>
                <?php //}?>

            	<li class="dropdown dropdown-user">
                <?php $user=$this->admin_bootstrap->get_user();?>
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                    <?php if ($user->image){?>
                        <img alt="" class="img-circle" src="<?php echo $images_path . $user->image;?>"/>
                    <?php }else{?>
					   <img alt="" class="img-circle" src="<?php echo base_url();?>assets/template/admin/layout/img/avatar3_small.jpg"/>
					<?php }?>
                        <span class="username username-hide-on-mobile"><?php echo $user->username;?></span>
					   <i class="fa fa-angle-down"></i>
					</a>
					<ul class="dropdown-menu dropdown-menu-default">
						<li>
							<a href="<?php echo base_url();?>users/admin_users/edit/<?php echo $user->id;?>">
							<i class="icon-user"></i><?php echo lang('my_personal');?></a>
						</li>

						<li>
							<a href="<?php echo base_url();?>admin/logout">
							<i class="icon-key"></i><?php echo lang('logout');?></a>
						</li>


					</ul>
				</li>



				<!-- END QUICK SIDEBAR TOGGLER -->
			</ul>
		</div>
		<!-- END TOP NAVIGATION MENU -->
	</div>
	<!-- END HEADER INNER -->
</div>
<!-- END HEADER -->
<div class="clearfix">
</div>
<!-- BEGIN CONTAINER -->
<div class="page-container">
	<!-- BEGIN SIDEBAR -->

	<div class="page-sidebar-wrapper">
		<div class="page-sidebar navbar-collapse collapse">
			<!-- BEGIN SIDEBAR MENU -->
			<!-- DOC: Apply "page-sidebar-menu-light" class right after "page-sidebar-menu" to enable light sidebar menu style(without borders) -->
			<!-- DOC: Apply "page-sidebar-menu-hover-submenu" class right after "page-sidebar-menu" to enable hoverable(hover vs accordion) sub menu mode -->
			<!-- DOC: Apply "page-sidebar-menu-closed" class right after "page-sidebar-menu" to collapse("page-sidebar-closed" class must be applied to the body element) the sidebar sub menu mode -->
			<!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
			<!-- DOC: Set data-keep-expand="true" to keep the submenues expanded -->
			<!-- DOC: Set data-auto-speed="200" to adjust the sub menu slide up/down speed -->
			<ul class="page-sidebar-menu" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
				<!-- DOC: To remove the sidebar toggler from the sidebar you just need to completely remove the below "sidebar-toggler-wrapper" LI element -->
				<li class="sidebar-toggler-wrapper">
					<!-- BEGIN SIDEBAR TOGGLER BUTTON -->
					<div class="sidebar-toggler">
					</div>
					<!-- END SIDEBAR TOGGLER BUTTON -->
				</li>
				<!-- DOC: To remove the search box from the sidebar you just need to completely remove the below "sidebar-search-wrapper" LI element -->
				<li class="sidebar-search-wrapper">
					<!-- BEGIN RESPONSIVE QUICK SEARCH FORM -->
					<!-- DOC: Apply "sidebar-search-bordered" class the below search form to have bordered search box -->
					<!-- DOC: Apply "sidebar-search-bordered sidebar-search-solid" class the below search form to have bordered & solid search box -->
					<!--<form class="sidebar-search " action="extra_search.html" method="POST">
						<a href="javascript:;" class="remove">
						<i class="icon-close"></i>
						</a>
						<div class="input-group">
							<input type="text" class="form-control" placeholder="Search...">
							<span class="input-group-btn">
							<a href="javascript:;" class="btn submit"><i class="icon-magnifier"></i></a>
							</span>
						</div>
					</form>-->
					<!-- END RESPONSIVE QUICK SEARCH FORM -->
				</li>



				<li class="start<?php if ($active_controller == 'admin' && $active_method == 'dashboard' ){?> active open<?php } ?>">
					<a href="<?php echo base_url();?>admin/dashboard">
					<i class="icon-home"></i>

					<span class="title"><?php echo lang('dashboard');  ?></span>
					<span class="selected"></span>
					</a>
				</li>
                 <?php
                       $menu_permissions = $this->admin_bootstrap->get_menu_permissions();
                       $modules     = $menu_permissions[0];
                       $controllers = $menu_permissions[1];

                       foreach($modules as $module_id=>$module_array){
                        ?>

				<li<?php if ($active_module == $module_array['module']){?> class="active open" <?php } ?>>
					<a href="javascript:;">
                        <i class="<?php echo $module_array['module_icon_class']; ?>"></i>
					    <span class="title"><?php echo $module_array['module_name']; ?></span>
					    <span class="arrow <?php if ($active_module == $module_array['module']){?> open <?php } ?>" ></span>
					</a>
					<ul class="sub-menu">
                     <?php
                       foreach($controllers as $key=>$controller_row)
                       {
                            if($controller_row->module_id == $module_id)
                            {
                                $controller_name='';
                                $method='';

                                if(in_array('index', $controller_row->methods))
                                {
                                    $method="";
                                }
                                elseif(in_array('add', $controller_row->methods))
                                {
                                    $method="add";
                                }

                                $controller_name = $controller_row->controller_name;

                            ?>
                     <?php {?>
    					 	<li<?php if ($active_controller == $controller_row->controller){?> class="active" <?php } ?>>
    					 		 <?php /*<a href="<?php echo base_url();?><?php echo $module_array['module']; ?>/<?php echo $controller_row->controller; ?>/<?php echo $method; ?>">*/ ?>
                                 <a href="<?php echo base_url();?><?php echo $controller_row->module_path; ?>/<?php echo $controller_row->controller; ?>/<?php echo $method; ?>">
    					 		     <i class="<?php echo ($controller_row->icon_class=='')? 'fa  fa-file-o' : $controller_row->icon_class; ?>"></i>
    							     <?php echo $controller_name ; ?>
                                 </a>
    						</li>

                          <?php } }}?>

					</ul>
				</li>

                <?php  } ?>







				</ul>
			<!-- END SIDEBAR MENU -->
		</div>
	</div>
	<!-- END SIDEBAR -->
	<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
            <?php
if( $this->admin_bootstrap->get_method() != 'dashboard')
{
    $active_controller_row = $this->admin_bootstrap->get_controller_row();
    $active_controller_name = $active_controller_row->name;

    $this->load->view('Admin/breadcrumb.php');
?>

<div class="row">
<div class="col-md-12">
	<div class="portlet green-meadow box">
		<div class="portlet-title">
			<div class="caption">
				<?php if($active_controller_row){?>
                    <i class="<?php echo ($active_controller_row->icon_class=='')? 'fa  fa-file-o' : $active_controller_row->icon_class; ?>"></i>
                    <?php echo $active_controller_name; ?>
                <?php }?>
			</div>
            <div class="actions">
                <?php

                 $check_add_method     = $this->acl_model->check_add_method($active_module, $active_controller, $active_method);
                 $check_add_permission = $this->acl_model->check_add_permission($active_module, $active_controller);
                 if($check_add_method && $check_add_permission)
                 {?>

                    <a href="<?php echo base_url().$module."/".$controller."/add";?>" class="btn btn-default btn-sm">
    				    <i class="fa fa-plus"></i> <?php echo lang('add');?>
                    </a>


                <?php
                 }
                 /*
                 if($active_controller_row->controller == 'admin_purchase_orders' && $active_method == 'index' ){ ?>
                    <a href="<?php echo base_url().$module."/admin_drafts/";?>" class="btn btn-default btn-sm">
    				    <i class="fa glyphicon glyphicon-folder-close"></i> <?php echo lang('show_draft');?>
                    </a>
                <?php }
                */?>
            </div>

		</div>
		<div class="portlet-body">
            <?php if (isset($_SESSION['success'])){?>
                <script>
                    $( document ).ready(function(){
                        showToast('<?php echo lang('records_added_successfully');?>','<?php echo $_SESSION['success'];?>','success');
                    });
                </script>

            <?php }
            elseif(isset($_SESSION['faild'])){?>
             <script>
                $( document ).ready(function(){
                    showToast('<?php echo lang('records_not_added');?>','<?php echo $_SESSION['faild'];?>','error');
                });
             </script>

           <?php }elseif(isset($_SESSION['user_register_error'])){ ?>
                    <script>
                        $( document ).ready(function(){
                            showToast('<?php echo lang('user_not_deleted');?>','<?php echo $_SESSION['user_register_error'];?>','error');
                        });
                     </script>

           <?php }elseif(isset($_SESSION['custom_error_msg'])){?>
                    <script>
                    $( document ).ready(function(){
                        showToast('<?php echo $_SESSION['custom_error_msg'];?>','<?php echo lang('error');?>','error');
                    });
                 </script>
           <?php }

           if(isset($_SESSION['warrning'])){?>
            <script>
                $( document ).ready(function(){
                    showToast('<?php echo $_SESSION['warrning'];?>','<?php echo 'warning'// lang('warning');?>','warning');
                });
             </script>
           <?php }

           if(isset($_SESSION['warrning2'])){?>
                    <script>
                    $( document ).ready(function(){
                        showToast('<?php echo $_SESSION['warrning2'];?>','<?php echo 'warning'// lang('warning');?>','warning');
                    });
                 </script>
           <?php }

           if(isset($_SESSION['warrning3'])){?>
                    <script>
                    $( document ).ready(function(){
                        showToast('<?php echo $_SESSION['warrning3'];?>','','warning');
                    });
                 </script>
           <?php }

           if(isset($_SESSION['send_sms_successfully'])){?>
                <script>
                    $( document ).ready(function(){
                        showToast('<?php echo $_SESSION['send_sms_successfully'];?>','','success');
                    });
                 </script>
           <?php }

           if(isset($_SESSION['send_sms_error'])){?>
                <script>
                    $( document ).ready(function(){
                        showToast('<?php echo $_SESSION['send_sms_error'];?>','','error');
                    });
                 </script>
           <?php }
           if(isset($_SESSION['success'])){?>
             <script>showToast('<?php echo $_SESSION['success'];?>','','success');</script>
           <?php } ?>


           <?php echo $content;?>
        </div>
        </div>
	</div>
</div>
<?php }else{
    echo $content;
} ?>
</div>
	</div>
	<!-- END CONTENT -->


</div>
<!-- END CONTAINER -->
<!-- BEGIN FOOTER -->
<div class="page-footer">
	<?php /*<div class="page-footer-inner">
		 <?php echo date('Y');?> &copy; <?php echo lang('site_title');?>   by  <a href="" target="_blank">ShouraSoft</a>.
	</div>*/?>
	<div class="scroll-to-top">
		<i class="icon-arrow-up"></i>
	</div>
</div>
<!-- END FOOTER -->
<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
<script src="<?php echo base_url();?>assets/template/admin/global/plugins/respond.min.js"></script>
<script src="<?php echo base_url();?>assets/template/admin/global/plugins/excanvas.min.js"></script>
<![endif]-->

<!-- IMPORTANT! Load jquery-ui.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
<script src="<?php echo base_url();?>assets/template/admin/global/plugins/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/template/admin/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/template/admin/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/template/admin/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>

<script src="<?php echo base_url();?>assets/template/admin/global/plugins/jquery.cokie.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/template/admin/global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/template/admin/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->

<!-- BEGIN PAGE LEVEL PLUGINS -->


<script src="<?php echo base_url();?>assets/template/admin/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/template/admin/global/plugins/bootstrap-select/bootstrap-select.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/template/admin/global/plugins/select2/select2.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/template/admin/global/plugins/jquery-multi-select/js/jquery.multi-select.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/template/admin/global/plugins/bootbox/bootbox.min.js" type="text/javascript"></script>

<script src="<?php echo base_url();?>assets/template/admin/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/template/admin/global/plugins/bootstrap-daterangepicker/daterangepicker.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/template/admin/global/plugins/bootstrap-daterangepicker/moment.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/template/admin/pages/scripts/components-pickers.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/template/admin/pages/scripts/components-form-tools.js" type="text/javascript"></script>



<?php
if(isset($js_files))
{
  foreach($js_files as $js_file)
  {
    echo '<script src="'.base_url().'assets/template/admin/'.$js_file.'" type="text/javascript"></script>';

  }
}
?>

<script src="<?php echo base_url();?>assets/template/admin/global/plugins/bootstrap-toastr/toastr.min.js" type="text/javascript"></script>

<script src="<?php echo base_url();?>assets/template/admin/global/plugins/typeahead/handlebars.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/template/admin/global/plugins/typeahead/typeahead.bundle.min.js" type="text/javascript"></script>





<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script>
var assetsPath = '<?php echo base_url();?>';

    var globalImgPath = 'global/img/';

    var globalPluginsPath = 'global/plugins/';

    var globalCssPath = 'global/css/';
</script>
<script src="<?php echo base_url();?>assets/template/admin/global/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/template/admin/layout/scripts/layout.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/template/admin/layout/scripts/quick-sidebar.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/template/admin/layout/scripts/demo.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/template/admin/pages/scripts/index.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/template/admin/pages/scripts/tasks.js" type="text/javascript"></script>

<!--Drag and Drop Sortable Table Row-->
<script src="<?php echo base_url();?>assets/template/admin/global/plugins/jquery.rowsorter.js"></script>
<!--jquery date-->



<!-- END PAGE LEVEL SCRIPTS -->
<script>
jQuery(document).ready(function() {
   Metronic.init(); // init metronic core componets
   Layout.init(); // init layout
   QuickSidebar.init(); // init quick sidebar
   Demo.init(); // init demo features
   Index.init();
   Tasks.initDashboardWidget();
   $('.select2').select2();
   ComponentsPickers.init();

   <?php
       if(isset($js_code))
       {
          echo $js_code;
       }
   ?>

});

 $("#notification_count").mouseover(function(){
   $(this).text(0);
   $('#pend_notification_count').text(0);
   $.ajax({
    type:'post',
    url:"<?php echo base_url()?>notifications/notification/read_notifications/",

   });
   return false;
});

</script>

<script>
    function showToast($msg,$title,$type)
    {
        var msg = $msg;
        var title = $title;
        var shortCutFunction = $type;

        toastr.options = {
              "closeButton": true,
              "debug": false,
              "positionClass": "toast-top-center",
              "onclick": null,
              "showDuration": "100000",
              "hideDuration": "100000",
              "timeOut": "5000000",
              "extendedTimeOut": "100000",
              "showEasing": "swing",
              "hideEasing": "linear",
              "showMethod": "fadeIn",
              "hideMethod": "fadeOut"

           }

            var $toast = toastr[shortCutFunction](msg, title); // Wire up an event handler to a button in the toast, if it exists
            $toastlast = $toast;
            if ($toast.find("#okBtn").length) {
                $toast.delegate("#okBtn", "click", function () {
                    alert("you clicked me. i was toast #" + toastIndex + ". goodbye!");
                    $toast.remove();
                });
            }
            if ($toast.find("#surpriseBtn").length) {
                $toast.delegate("#surpriseBtn", "click", function () {
                    alert("Surprise! you clicked me. i was toast #" + toastIndex + ". You could perform an action here.");
                });
            }

            $("#clearlasttoast").click(function () {
                toastr.clear($toastlast);
            });
    }
</script>
<!--Dynamic Reports-->
<script>
$(document).ready(function(){

	$(".button_filter").click(function() {
    $($('.show_table')[$(this).index(".button_filter")]).toggle('slow');
});



$('#select_area').change(function(){
     /*******Filters******/
    var selected_payment = $('#payment_filter').find('option:selected');
    var payment_type     = selected_payment.data('type');

    var postData = {
                         country_id         : $('#country_id').val(),
                         payment_id         : $('#payment_filter').val(),
                         payment_type       : payment_type,
                         customer_group_id  : $('#customer_group_id').val(),
                         cat_id             : $('#cat_id').val(),
                         coupon_id          : $('#coupon_id').val(),
                         order_id_from      : $('#order_id_from').val(),
                         order_id_to        : $('#order_id_to').val(),
                         date_from          : $('#date_from').val(),
                         date_to            : $('#date_to').val(),
                         status_date_from   : $('#status_date_from').val(),
                         status_date_to     : $('#status_date_to').val(),
                         order_status_id    : $('#order_status_id').val(),
                         user_id            : $('#users_filter').val(),
                         user_email_id      : $('#emails').val(),
                         user_phone_id      : $('#phone').val(),
                         user_ip_address_id : $('#ip_address').val()
                    }
    $.post('<?php echo base_url()."reports/dynamic_reports/ajax_list";?>', postData, function(result){
        $('#tbody').html(result);
        var select_val = $('#select_area').val();
    });



});

});
</script>
<script>
    <?php if($this->session->flashdata('qty_error')){ ?>
        showToast('<?php echo lang('qty_error');?>','<?php echo $this->session->flashdata('qty_error');?>','error');
    <?php }?>
</script>

<script>
function myFunction() {
  window.print();
}
</script>

<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>
