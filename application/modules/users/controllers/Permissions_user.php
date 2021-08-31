<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Permissions_user extends CI_Controller
{
    public $data = array();
    public $crud;
   
    public function __construct()
    {
        parent::__construct();
        
        $this->crud = new grocery_CRUD();
        $params     = array($this->crud);
        
        require(APPPATH . 'includes/global_vars.php');
        
        $this->load->model('permissions_model');
        $this->load->model('users_model');
        $this->load->model('acl_model');
        $this->load->model('root/controllers_model');
        
        $this->data['module_row']       = $this->admin_bootstrap->get_module_row();
        $this->data['controller_row']   = $this->admin_bootstrap->get_controller_row();
        $this->lang_row                 = $this->admin_bootstrap->get_active_language_row();
        
    }
    
     private function _js_and_css_files()
     {
        $this->data['css_files'] = array();
        
        $this->data['js_files']  = array( );
        
        $this->data['js_code'] = "";
    }
    
    public function index()
    {
        
        $lang_id = $this->data['active_language']->id;
        
        $this->data['count_all_records']    = $this->users_model->get_count_all_users($lang_id);
        $this->data['data_language']        = $this->lang_model->get_active_data_languages();
        
        $this->data['columns']              = array(
                                                     lang('username'),
                                                     lang('first_name'),
                                                     lang('last_name'),
                                                     lang('email'),
                                                     lang('permissions'),
                                                     lang('remove_special_permission')
                                                 );
                                                   
        $this->data['orders']                = array(
                                                     lang('username'),
                                                     lang('first_name'),
                                                     lang('last_name'),
                                                     lang('email'),
                                                    
                                                    );                                                        
            
        //$this->data['actions']              = array( 'delete'=>lang('delete'));
        $this->data['search_fields']        = array( lang('username'), lang('first_name'), lang('last_name'), lang('email'));
        
        $this->data['unset_edit']           = true;
        $this->data['unset_delete']         = true;
        $this->data['unset_view']           = true;
        
        $this->data['content']  = $this->load->view('Admin/grid/grid_html', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
       
    }
   
   public function ajax_list()
    {
        if(isset($_POST['lang_id']))
        {
            $lang_id = intval($this->input->post('lang_id'));
        }else{
            $lang_id = $this->data['active_language']->id;    
        }
        if(isset($_POST['limit']))
        {
            $limit = intval($this->input->post('limit'));
        }else{
            $limit = 1;    
        }
        
        if(isset($_POST['page_number']))
        {
            $active_page = intval($this->input->post('page_number'));
        }else{
            $active_page = 1;    
        }
        
        $offset  = ($active_page-1) * $limit;
           
        
        if(isset($_POST['search_word']) || trim($_POST['search_word']) == '')
        { 
            $search_word = $this->input->post('search_word');
        }
        else
        {
            $search_word = '';
        }
        
        if(isset($_POST['order_by']))
        {
            $order_by = $this->input->post('order_by');
        }
        else
        {
            $order_by = '';
        }
        
        if(isset($_POST['order_state']))
        {
            $order_state = $this->input->post('order_state');
        }
        else
        {
            $order_state = 'desc';
        }
        
        
        $grid_data       = $this->users_model->get_users_data($limit,$offset,$search_word,$order_by,$order_state);
        
        $db_columns      = array(
                                 'id'        ,   
                                 'username'  ,
                                 'first_name' ,
                                 'last_name',
                                 'email',
                                 'add_permission',
                                 'remove_permission',
                                 
                                 );
                       
       $this->data['hidden_fields'] = array('id');
                                           
       $new_grid_data = array();
        
        foreach($grid_data as $key =>$row)
        { 
            foreach($db_columns as $column)
            {
                if($column == 'add_permission')
                {
                    
                    $new_grid_data[$key][$column] = "<a href='".base_url()."users/permissions_user/set_permissions/".$row->id."' ><img src='".base_url()."assets/template/admin/img/key-add.png'/></a>";
               
                }elseif($column == 'remove_permission')
                {
                    $new_grid_data[$key][$column] = "<a href='".base_url()."users/permissions_user/remove_user_permissions/".$row->id."' ><img src='".base_url()."assets/template/admin/img/key-delete.png'/></a>";
                }else{
                    
                    $new_grid_data[$key][$column] = $row->{$column};
                }
                
                
            }
        }
        
        
        $this->data['grid_data']         = $new_grid_data;
        $this->data['count_all_records'] = $this->users_model->get_count_all_users($search_word);
        
        
        $this->data['unset_edit']              = true;
        $this->data['unset_delete']            = true;
        $this->data['unset_view']              = true;
        
        $output_data = $this->load->view('Admin/grid/grid_data',$this->data, true);
        $count_data  = $this->data['count_all_records'];
        
        echo json_encode(array($output_data, $count_data, $search_word));
    }
    
  
   //////////////////////////////////////////////////////////////////////////////////////
    public function set_permissions($user_id)
    {
        $user_id = intval($user_id);
        //->>>GET user's old permissions
        $user_permissions_count = $this->acl->get_user_permissions_count($user_id);
       
        $user_permissions = '';
        
        if($user_permissions_count == 0)
        {
            $user_groups        = $this->ion_auth->get_users_groups($user_id)->result();
            $user_groups_ids    = array();
           
           if(isset($user_groups) && count($user_groups) != 0)
           {
                foreach($user_groups as $user_group)
                {
                    $user_groups_ids[] = $user_group->id;
                }
                
                $user_permissions   = $this->permissions_model->get_groups_permissions($user_groups_ids); 
           }
            
        }
        else
        {
            $user_permissions   = $this->permissions_model->get_user_permissions($user_id);    
        }
        
        $old_permissions = array();
        $old_controllers = array();
        
        if(count($user_permissions) > 0)
        {
            foreach($user_permissions as $permission)
            {
                $old_permissions["{$permission->controller_id}"][] = $permission->permission_id;
                $old_controllers[] = $permission->controller_id;
            }
        }
        
        $this->data['user_old_permissions'] = $old_permissions;
        $this->data['user_old_controllers'] = $old_controllers;
        
       //->>>GET Controllers
        $controllers            = $this->controllers_model->get_controllers($this->lang_row->id);
        $modules                = array();
        $controller_permissions = array();
        
        foreach($controllers as $controller)
        {
            $modules["{$controller->module_id}"]            = array(
                                                                    'module'            => $controller->module,
                                                                    'module_name'       => $controller->module_name,
                                                                    //'module_name_en'    => $controller->module_name_en,
                                                                    'module_icon_class' => $controller->module_icon_class
            );
                                                                
            $controller_permissions["{$controller->id}"]    = $this->permissions_model->get_permissions($controller->id,$this->lang_row->id);
        }
        
        $this->data['controllers']          = $controllers;
        $this->data['modules']              = $modules;
        
        //->>>GET permissions
        $this->data['permissions']          = $controller_permissions;
        $this->data['user_id']              = $user_id;
        
        $this->data['content']              = $this->load->view('permissions_user',$this->data,true);
        
        $this->load->view('Admin/main_frame', $this->data);
    }
    
    public function save_permission()
    {
        
        $user_id        = $this->input->post('user_id');
        $checked_nodes  = $this->input->post('checked_nodes');

     // print_r($checked_nodes);die();
        $this->permissions_model->delete_user_permissions($user_id);
        if(isset($_POST['checked_nodes']) && count($checked_nodes) > 0)
        {
            foreach($checked_nodes as $node)
            {
                $p = explode('_',$node);
    
                if($p[0]=='p')
                {
                    $controller_id  = $p[3];
                    $permission     = $p[1];
                    
                    $this->permissions_model->save_user_permissions($user_id, $controller_id, $permission); 
                }
            }
         }
            
        echo "	<div class='alert alert-success alert-dismissable'>
			<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
			<strong>".lang('success')."</strong>".lang('')."</div>";
        
    }
    
    public function remove_user_permissions($user_id)
    {
        $user_id = intval($user_id);
        
        $this->permissions_model->delete_user_permissions($user_id);
        redirect(base_url().'users/permissions_user','refresh');
    }
/******************************************************************/


/***************************** TESTING ****************************/
public function apply_module_unit_testing()
{
    $this->load->library('unit_test');
    $this->unit->active(TRUE);
    
    $this->test_loaded_controllers();
    $this->test_user_assigned_to_groups();
    $this->test_user_permissions_count();
    
    echo $this->unit->report();
}

public function test_user_permissions_count()
{
    // Define the test name
    $test_name              = 'Test that user has permissions assigned';
    
    // Logged in user id
    $user_id                = $this->admin_bootstrap->get_user_id();
    
    // Count of permissions of this user id (using function to be tested)
    $user_permissions_count = $this->acl->get_user_permissions_count($user_id);
    
    $test                   = $user_permissions_count;

    $expected_result        = 'is_int';
    
    $this->unit->run($test, $expected_result, $test_name);
}

public function test_user_assigned_to_groups()
{
    // Define the test name
    $test_name              = 'Test that user was assigned to group/s';
    
    // Logged in user id
    $user_id                = $this->admin_bootstrap->get_user_id();
    
    // Return an object with user groups (using function to be tested)
    $user_groups            = $this->ion_auth->get_users_groups($user_id)->result();
    
    $test                   = $user_groups;

    $expected_result        = 'is_array';
    
    $this->unit->run($test, $expected_result, $test_name);
}

public function test_loaded_controllers()
{
    // Define the test name
    $test_name              = 'Test that loaded controllers exist';
    
    $controllers            = $this->controllers_model->get_controllers();
    
    $test                   = $controllers;

    $expected_result        = 'is_array';
    
    $this->unit->run($test, $expected_result, $test_name);
}
/***************************** TESTING ****************************/

}
?>