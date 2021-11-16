<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class User_api_log extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        require(APPPATH . 'includes/global_vars.php');
        
        $this->load->model('user_model');
        $this->load->model('visit_log_model');
        
        $this->lang_row = $this->admin_bootstrap->get_active_language_row();      
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
        
        $this->data['count_all_records']    = $this->user_model->get_count_all_user_api_log();
        $this->data['data_language']        = $this->lang_model->get_active_data_languages();
        
        $this->data['filters']              = array(
                                                      /*array(
                                                            'filter_title' => lang('users_filters'),
                                                            'filter_name'  => 'username_filter',
                                                            'filter_data'  => $this->users_model->get_users_filter_data()
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
                                                            ),
                                                      /*array(
                                                            'filter_title' => lang('methods_filter'),
                                                            'filter_name'  => 'methods_filter',
                                                            'filter_data'  => $this->methods_model->get_methods_filter_data($lang_id)
                                                            ) 
                                                      */
                                                            
                                                    );
        
        
        $this->data['columns']              = array(
                                                     lang('username')   ,
                                                     lang('agent')      ,
                                                     lang('title')     ,
                                                     lang('unix_time'),
                                                     lang('url')        ,
                                                    
                                                   );
                                                   
        $this->data['orders']               = array(
                                                     lang('date'),
                                                     lang('username')   ,
                                                    
                                                   );                                                  
            
        $this->data['actions']              = array( /*'delete'=>lang('delete')*/);
        $this->data['search_fields']        = array( lang('username'), lang('agent'), lang('title'), lang('url'));
        
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
       $methods_filter_id     = 0;
       if(isset($_POST['filter'])&& isset($_POST['filter_data']))
       {
           $filters      = $this->input->post('filter');
           $filters_data = $this->input->post('filter_data');
            
           $user_filter_id        = intval($filters_data[0]);
           $modules_filter_id     = intval($filters_data[1]);
           $controllers_filter_id = intval($filters_data[2]);
           //$methods_filter_id     = intval($filters_data[3]);
       }
       else
       {
           $user_filter_id        = 0;
           $modules_filter_id     = 0;
           $controllers_filter_id = 0;
           $methods_filter_id     = 0;
       }
        
       $_SESSION[$this->data['module'] . "_" . $this->data['controller'] .'_active_page'] = $active_page;
       
       $grid_data  = $this->user_model->get_user_api_log($lang_id, $limit, $offset, $search_word, $order_by, $order_state, $user_filter_id, $modules_filter_id, $controllers_filter_id, $methods_filter_id);
       
       $db_columns = array(
                             'id'              ,   
                             'username'        ,
                             'agent',
                             'api_name'      ,
                             'unix_time'       ,
                             'url'
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
                }
                elseif($column == 'username')
                {
                    $new_grid_data[$key][$column] = $row->first_name.' '.$row->last_name.' - '.$row->phone;
                }
                else
                {
                    $new_grid_data[$key][$column] = $row->{$column};
                }
            }
        }
        
       $this->data['grid_data']          = $new_grid_data;
       $this->data['count_all_records']  = $this->user_model->get_count_all_user_api_log( $search_word);
       $this->data['display_lang_id']    = $lang_id;
       $this->data['unset_add']          = 'true';
       $this->data['unset_edit']         = 'true';
       //$this->data['unset_read']         = 'true';  
       $output_data = $this->load->view('Admin/grid/grid_data',$this->data, true);
       $count_data  = $this->data['count_all_records'];
        
       echo json_encode(array($output_data, $count_data, $search_word));
     }
     
     public function read($id, $display_lang_id)
     {
        $id              = intval($id);
        $display_lang_id = intval($display_lang_id);
        
        if($id && $display_lang_id)
        {
            $data = $this->user_model->get_user_api_log_data($id);
            
            $viewed_data_str = '';
            $posted_data_str = '';
            if($data->post_data != '')
            {
                $posted_data = json_decode($data->post_data);
                
                foreach($posted_data as $field=>$value)
                {
                    $value_field = '';
                    
                    if(is_array($value))
                    {
                        foreach($value as $key=>$item)
                        {
                            $value_field = '*'.$key.' : '.$item.'---';
                        }
                    }
                    else
                    {
                        $value_field = $value;
                    }
                    
                    $posted_data_str .= $field.' : '.$value_field.'</br>';
                }
            }
            //echo $data->recieved_data.'<br /><br /><br /><pre>'; print_r(json_decode($data->recieved_data)); die();
            if($data->recieved_data != '')
            {
                $viewed_data_str = '';//json_decode($data->recieved_data);
                
                foreach(json_decode($data->recieved_data) as $field=>$value)
                {
                    $value_field = '';
                    
                    if(is_array($value) || is_object($value))
                    {
                        
                        $item_val= '';
                        $value_field = '';
                        foreach($value as $key=>$item)
                        {
                            //print_r($item); die();
                            $item_val = '';
                            if(is_array($item) || is_object($item))
                            {
                                foreach($item_val as $key2=>$item2)
                                {
                                    $item_val .= $key2.' : '.$item2.'<br />';
                                }
                            }
                            else
                            {
                                $item_val .= $key.':'.$item.'<br />';
                            }
                            
                            $value_field .= '<br />'.$item_val.'<br />';
                        }
                        
                    }
                    else
                    {
                        $value_field .= $field.' : '.$value.'<br />';
                    }
                    
                    $viewed_data_str .= $value_field.'<br />';
                }
            }
            
            $row_data = array(
                                lang('username')    => $data->first_name.' '.$data->last_name,
                                lang('unix_time')   => date('Y-m-d H:i',$data->unix_time),
                                lang('url')         => $data->url,
                                lang('user_agent')  => $data->agent,
                                lang('posted_data') => $posted_data_str,
                                ('viewed_data') => $viewed_data_str//$data->recieved_data
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
            
        $this->user_model->delete_user_api_log_row($ids_array);
        
     }
    
}