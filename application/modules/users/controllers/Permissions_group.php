<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Permissions_group extends CI_Controller
{
    public $data = array();
    public $crud;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model('permissions_model');
        $this->load->model('groups_model');
        
        $this->crud = new grocery_CRUD();
        $params     = array($this->crud);
        
       require(APPPATH . 'includes/global_vars.php');
       
       $this->lang_row                 = $this->admin_bootstrap->get_active_language_row();
    }
    
   private function _js_and_css_files()
     {
        $this->data['css_files'] = array(
            'global/plugins/jquery-tags-input/jquery.tagsinput.css',
            );
        
        $this->data['js_files']  = array(
            //Date Range Picker
            'global/plugins/bootstrap-daterangepicker/moment.min.js',
            //Tags
            'tags/tag-it.js',
            );
        
        $this->data['js_code'] = "";
    }
    
    public function index()
    {
        
        $lang_id = $this->data['active_language']->id;
        
        $this->data['count_all_records']    = $this->groups_model->get_count_all_groups($lang_id);
        $this->data['data_language']        = $this->lang_model->get_active_data_languages();
        
        $this->data['columns']              = array(
                                                     lang('group_name'),
                                                     lang('description'),
                                                     lang('permissions')
                                                 );
                                                   
        $this->data['orders']                = array(
                                                     lang('group_name'),
                                                     lang('description'),
                                                    );                                                                                                         
            
        //$this->data['actions']              = array( 'delete'=>lang('delete'));
        $this->data['search_fields']        = array( lang('group_name'), lang('description') );
        
        $this->data['unset_edit']           = true;
        $this->data['unset_delete']         = true;
        $this->data['unset_view']           = true;
        
        //$this->data['unset_actions']        = true;
        
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
        
        
        $grid_data       = $this->groups_model->get_groups_data($lang_id,$limit,$offset,$search_word,$order_by,$order_state);
        
        $db_columns      = array(
                                 'id'          ,   
                                 'name'        ,
                                 'description' ,
                                 'permission'
                                 );
                       
       $this->data['hidden_fields'] = array('id');
                                           
       $new_grid_data = array();
        
        foreach($grid_data as $key =>$row)
        { 
            foreach($db_columns as $column)
            {
                if($column == 'permission')
                {
                    
                    $new_grid_data[$key][$column] = "<a href='".base_url()."users/permissions_group/set_permissions/".$row->group_id."' class='' ><img src='".base_url()."assets/template/admin/img/key-add.png'/></a>";
               
                }else{
                    
                    $new_grid_data[$key][$column] = $row->{$column};
                }
                
                
            }
        }
        
        
        $this->data['grid_data']         = $new_grid_data;
        $this->data['count_all_records'] = $this->groups_model->get_count_all_groups($lang_id,$search_word);
        
        
        $this->data['unset_edit']              = true;
        $this->data['unset_delete']            = true;
        $this->data['unset_view']              = true;
        
        
        $output_data = $this->load->view('Admin/grid/grid_data',$this->data, true);
        $count_data  = $this->data['count_all_records'];
        
        echo json_encode(array($output_data, $count_data, $search_word));
    }
    
  
 
 //////////////////////////////////////////////////////////
    public function set_permissions($primary_key)
    {
        $primary_key = intval($primary_key);
        $this->load->model('permissions_model');
        $this->load->model('root/controllers_model');
        
        //->>>GET Group's old permissions
        $group_permissions = $this->permissions_model->get_groups_permissions($primary_key);
        $old_permissions   = array();
        $old_controllers   = array();
        
        foreach($group_permissions as $permission)
        {
            $old_permissions["{$permission->controller_id}"][]  = $permission->permission_id;
            $old_controllers[]                                  = $permission->controller_id;
        }
       
        //->>>GET Controllers
        $controllers            = $this->controllers_model->get_controllers($this->lang_row->id);
        $modules                = array();
        $controller_permissions = array();
        
        foreach($controllers as $controller)
        {
             $modules["{$controller->module_id}"]         = array(
                                                                    'module'            => $controller->module,
                                                                    'module_name'       => $controller->module_name,
                                                                   // 'module_name_en'    => $controller->module_name_en,
                                                                    'module_icon_class' => $controller->module_icon_class
                                                                );
             $controller_permissions["{$controller->id}"] = $this->permissions_model->get_permissions($controller->id,$this->lang_row->id);
        }
        
        $this->data['controllers']              = $controllers;
        $this->data['modules']                  = $modules;
        
        //->>>GET permissions
        $this->data['permissions']              = $controller_permissions;
        $this->data['group_old_permissions']    = $old_permissions;
        $this->data['group_old_controllers']    = $old_controllers;
        $this->data['group_id']                 = $primary_key;
       
        $this->data['content'] = $this->load->view('permissions_group',$this->data,true);
        $this->load->view('Admin/main_frame',$this->data);
    }
    
    public function save_permission()
    {
        $group_id        = $this->input->post('group_id');
        $checked_nodes   = $this->input->post('checked_nodes');
               
        $this->permissions_model->delete_group_permissions($group_id);
        
        if(isset($_POST['checked_nodes']) && count($_POST['checked_nodes']) > 0)
        {
            foreach($checked_nodes as $node)
            {
                $p = explode('_',$node);
    
                if($p[0]=='p')
                {
                    $controller_id  = $p[3];
                    $permission     = $p[1];
                    
                    $this->permissions_model->save_group_permissions($group_id, $controller_id, $permission); 
                }
              
              
            }
        } 
         
        echo "	<div class='alert alert-success alert-dismissable'>
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								<strong>".lang('success')."</strong>".lang('')."</div>";
    }
    
    public function test_list()
    {
        $lang_id = $this->data['active_language']->id;
        
        $this->data['count_all_records']  = $this->groups_model->get_count_all_groups($lang_id);
        $this->data['data_language']      = $this->lang_model->get_active_data_languages();
        
        $this->data['columns']            = array(
                                                    lang('group_name'),
                                                    lang('description')
                                                  );
            
        $this->data['actions']            = array( 'delete'=>lang('delete'));
        
        $this->data['content']            = $this->load->view('Admin/grid/grid_html', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
    }
}
?>