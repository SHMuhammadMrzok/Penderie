<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Incorrect_login extends CI_Controller
{
    public $lang_row;
    
    public function __construct()
    {
        parent::__construct();
        
        require(APPPATH . 'includes/global_vars.php');
        
        $this->load->model('incorrect_login_model');
        
        $this->lang_row = $this->admin_bootstrap->get_active_language_row(); 
    }

    

    public function index()
    {   
        $lang_id = $this->data['active_language']->id;
        
        $this->data['count_all_records'] = $this->incorrect_login_model->get_count_all_data($lang_id);
        $this->data['data_language']     = $this->lang_model->get_active_data_languages();
        
        /*$this->data['filters']           = array(
                                                  array(
                                                        'filter_title' => lang('countries_filter')  ,
                                                        'filter_name'  => 'country_id'              ,
                                                        'filter_data'  => $this->countries_model->get_countries($lang_id)
                                                        )
                                          
                                                );
        */
        $this->data['columns']           = array(
                                                     lang('email')      ,
                                                     lang('password')   ,
                                                     lang('agent')      ,
                                                     lang('ip_address') ,
                                                     lang('date')
                                                   );
            
        $this->data['orders']            = $this->data['columns'];
        
        $this->data['actions']           = array( 'delete'=>lang('delete'));
        $this->data['search_fields']     = array( lang('email'), lang('password'), lang('agent'), 'ip_address');
        
        $this->data['content']           = $this->load->view('Admin/grid/grid_html', $this->data, true);
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
        
        /*if(isset($_POST['filter'])&& isset($_POST['filter_data']))
        {
            $filters = $this->input->post('filter');
            $filters_data = $this->input->post('filter_data');
            
            $country_id = intval($filters_data[0]);
        }
        else
        {
            $country_id = 0;            
        }  
        */
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
        
        
        $grid_data  = $this->incorrect_login_model->get_data($lang_id, $limit, $offset, $search_word, $order_by, $order_state);
        
        $db_columns = array(
                             'id'           ,   
                             'email'        ,
                             'password'     ,
                             'agent'        ,
                             'ip_address'   ,
                             'unix_time'
                           );
                       
        $this->data['hidden_fields'] = array('id');
        
        $new_grid_data = array();
        
        foreach($grid_data as $key =>$row)
        { 
            foreach($db_columns as $column)
            {
                if($column == 'agent')
                {
                    $new_grid_data[$key][$column] = substr($row->agent, 0, 20);
                }
                elseif($column == 'unix_time')
                {
                    $new_grid_data[$key][$column] = date('Y/m/d H:i', $row->unix_time);
                }
                else
                {
                    $new_grid_data[$key][$column] = $row->{$column};
                }
            }
        }
        
        $this->data['grid_data']                  = $new_grid_data; 
        
        $this->data['count_all_records']  = $this->incorrect_login_model->get_count_all_data($lang_id,$search_word);
        
        $this->data['display_lang_id']    = $lang_id;
        $this->data['unset_edit']         = true;
         
        
        $count_data  = $this->data['count_all_records'];
        $output_data = $this->load->view('Admin/grid/grid_data',$this->data, true);
        
        echo json_encode(array($output_data, $count_data, $search_word));
     }
     
     public function read($id, $display_lang_id)
     {
        $id              = intval($id);
        $display_lang_id = intval($display_lang_id);
        
        if($id && $display_lang_id)
        {
            $data     = $this->incorrect_login_model->get_row_data($id, $display_lang_id);
            $row_data = array(
                                lang('email')       => $data->email     ,
                                lang('password')    => $data->password  ,
                                lang('ip_address')  => $data->ip_address,
                                lang('agent')       => $data->agent     ,
                                lang('date')        => date('Y/m/d H:i', $data->unix_time)
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
        $ids = $this->input->post('row_id');

        if(is_array($ids))
        { 
            
            $ids_array = array();
            
            foreach($ids as $id)
            {
                $ids_array[] = $id['value'];
            }
        }
        else
        { 
            $ids_array = array($ids);
        }
            
        $this->incorrect_login_model->delete_data($ids_array);
        
     }  
     
     
    
/************************************************************************/    
}