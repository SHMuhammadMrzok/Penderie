<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Userslog extends CI_Controller
{
    public $crud;
    public $data = array();
    
    public function __construct()
    {
        parent::__construct();
        
        require(APPPATH . 'includes/global_vars.php');
        
        $this->load->model('root/controllers_model');
        $this->load->model('root/modules_model');
        $this->load->model('root/log_actions_model');
        $this->load->model('users_model');
        $this->lang_row                 = $this->admin_bootstrap->get_active_language_row();
              
    }
    
    private function _js_and_css_files()
    {    
        $this->data['css_files'] = array('');
        
        $this->data['js_files']  = array('');
        
        $this->data['js_code'] = '';
    }

    public function index()
    {
        $lang_id = $this->data['active_language']->id;
        
        $this->data['count_all_records']    = $this->log_actions_model->get_count_all_users_log($lang_id);
        $this->data['data_language']        = $this->lang_model->get_active_data_languages();
        
        $this->data['filters']              = array(
                                                      array(
                                                            'filter_title' => lang('users_filters'),
                                                            'filter_name'  => 'username_filter',
                                                            'filter_data'  => $this->users_model->get_users_filter_data()
                                                            ) ,
                                                      array(
                                                            'filter_title' => lang('actions_filter'),
                                                            'filter_name'  => 'actions_filter',
                                                            'filter_data'  => $this->log_actions_model->get_log_actions_filter_data($lang_id)
                                                            ) ,
                                                      array(
                                                            'filter_title' => lang('modules_filters'),
                                                            'filter_name'  => 'modules_filters',
                                                            'filter_data'  => $this->modules_model->get_modules_filter_data($lang_id)
                                                            ) ,
                                                      array(
                                                            'filter_title' => lang('controllers_filters'),
                                                            'filter_name'  => 'controllers_filters',
                                                            'filter_data'  => $this->controllers_model->get_controllers_filter_data($lang_id)
                                                            ) 
                                                            
                                                    );
        
        
        $this->data['columns']              = array(
                                                     lang('username')   ,
                                                     lang('action')     ,
                                                     lang('module')     ,
                                                     lang('controller') ,
                                                     lang('unix_time')  ,
                                                     lang('ip_address') ,
                                                     lang('admin') ,
                                                   );
                                                   
        $this->data['orders']               = array(
                                                     lang('username')   ,
                                                     lang('action')     ,
                                                     lang('module')     ,
                                                     lang('controller') ,
                                                     lang('ip_address')
                                                   );                                                  
            
        $this->data['actions']              = array( 'delete'=>lang('delete'));
        $this->data['search_fields']        = array( lang('action'));
        
        $this->data['content']              = $this->load->view('Admin/grid/grid_html', $this->data, true);
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
       
       if(isset($_POST['filter'])&& isset($_POST['filter_data']))
        {
            $filters      = $this->input->post('filter');
            $filters_data = $this->input->post('filter_data');
            
            $user_filter_id        = intval($filters_data[0]);
            $actions_filter_id     = intval($filters_data[1]);
            $modules_filter_id     = intval($filters_data[2]);
            $controllers_filter_id = intval($filters_data[3]);
        }
        else
        {
            $user_filter_id        = 0;     
            $actions_filter_id     = 0;
            $modules_filter_id     = 0;
            $controllers_filter_id = 0;       
        }  
        
        
        
        
       
       $grid_data  = $this->log_actions_model->get_users_log_data($lang_id,$limit,$offset,$search_word,$order_by,$order_state,$user_filter_id,$actions_filter_id,$modules_filter_id,$controllers_filter_id);
       
       $db_columns = array(
                         'id',   
                         'username',
                         'action_name',
                         'module',
                         'controller',
                         'unix_time',
                         'ip_address',
                         'admin'
                       );
                       
       $this->data['hidden_fields'] = array('id');
                                           
        $new_grid_data = array();
        
        foreach($grid_data as $key =>$row)
        { 
            foreach($db_columns as $column)
            {
                if($column == 'unix_time')
                {
                    $new_grid_data[$key][$column] = date('Y-m-d H:i A', $row->unix_time);
                }elseif($column == 'admin')
                {
                    if($row->{$column} == 0)
                    {
                        $new_grid_data[$key][$column] = '<span class="badge badge-danger">'.lang('no').'</span>';    
                    }
                    elseif($row->{$column} == 1)
                    {
                        $new_grid_data[$key][$column] = '<span class="badge badge-success">'.lang('yes').'</span>';
                    }
                }
                else
                {
                    $new_grid_data[$key][$column] = $row->{$column};
                }
            }
        }
        
       $this->data['grid_data']          = $new_grid_data;
       $this->data['count_all_records']  = $this->log_actions_model->get_count_all_users_log($lang_id,$search_word,$user_filter_id,$actions_filter_id,$modules_filter_id,$controllers_filter_id);
       $this->data['display_lang_id']    = $lang_id;
         
       $output_data = $this->load->view('Admin/grid/grid_data',$this->data, true);
       $count_data  = $this->data['count_all_records'];
        
       echo json_encode(array($output_data, $count_data, $search_word));
     }
     
     public function read($id,$display_lang_id)
     {
        $id              = intval($id);
        $display_lang_id = intval($display_lang_id);
        
        if($id && $display_lang_id)
        {
            $data               = $this->log_actions_model->get_row_data($id,$display_lang_id);
            
            if($data->admin == 0)
            {
                $admin = '<span class="badge badge-danger">'.lang('no').'</span>';    
            }
            elseif($data->admin == 1)
            {
                $admin = '<span class="badge badge-success">'.lang('yes').'</span>';
            }
            
            $row_data = array(
                                lang('username')    => $data->username,
                                lang('action')      =>$data->action_name,
                                lang('module')      => $data->module,
                                lang('controller')  => $data->controller,
                                lang('unix_time')   => date('Y-m-d',$data->unix_time),
                                lang('ip_address')  =>$data->ip_address ,
                                lang('admin')       =>$admin
                             );
        
            $this->data['row_data'] = $row_data;
            
            $this->data['content']  = $this->load->view('Admin/grid/read_view', $this->data, true);
            $this->load->view('Admin/main_frame',$this->data);
        }
     }
     
     public function do_action()
     {
        $action = $this->input->post('action');
        if($action == 'delete')
        {
            $this->delete();
        }
     }
     
     public function delete()
     {
        $user_log_id = $this->input->post('row_id');

        if(is_array($user_log_id))
        { 
            
            $ids_array = array();
            
            foreach($user_log_id as $user_log_id)
            {
                $ids_array[] = $user_log_id['value'];
            }
        }
        else
        { 
            $ids_array = array($user_log_id);
        }
            
        $this->log_actions_model->delete_user_log_data($ids_array);
        
     }
    
}