<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Notification extends CI_Controller
{
    
    public   $lang_row;
    public function __construct()
    {
        parent::__construct();
        
        require(APPPATH . 'includes/global_vars.php');
        
        $this->load->model('notification_model');
        
        $this->lang_row = $this->admin_bootstrap->get_active_language_row(); 
    }

    public function test_notification()
    {
        $this->load->library('notifications');
        $data      = array ('username'=>'maha');
        $event      = 'add_new_user';
        $this->notifications->create_notification($event , $data) ;
    }
    /**************** List functions **********************/

    public function index()
    {   
        $lang_id = $this->data['active_language']->id;
        
        $this->data['count_all_records'] = $this->notification_model->get_count_all_notifications();
        $this->data['data_language']     = $this->lang_model->get_active_data_languages();
        
        $filter_array_admin =  array('id'=>'admin','name'=>lang('admin'));
        $filter_array_email =  array('id'=>'email','name'=>lang('email'));
        $filter_array_sms   =  array('id'=>'sms','name'=>lang('sms'));
        
        $this->data['filters']           = array(
                                                   array(
                                                          'filter_title' => lang('type_filters'),
                                                          'filter_name'  => 'type_filter',
                                                          'filter_data'  => array((object)$filter_array_admin ,(object)$filter_array_email,(object)$filter_array_sms)
                                                         ) ,
                                                );
        
        $this->data['columns']           = array(
                                                  //lang('notification_text'),
                                                  lang('notification_type'),
                                                  lang('notification_time'),
                                                  lang('event')
                                                );
        $this->data['orders']            = $this->data['columns'];
            
        $this->data['actions']           = array( 'delete'=>lang('delete'));
        
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
            
            $type  = $filters_data[0];
         
        }
        else
        {
            $type = 0;      
        }  
        
        $grid_data  = $this->notification_model->get_notifications_data($limit, $offset, $lang_id, $search_word, $order_by, $order_state, $type);
        
        $db_columns = array(
                              'id',
                              'type',
                              'unix_time',
                              'event'
                            );
                       
        $this->data['hidden_fields'] = array('id');
        
        $new_grid_data = array();
        
        foreach($grid_data as $key =>$row)
        { 
            
            foreach($db_columns as $column)
            {
                if($column == 'unix_time')
                {
                    $new_grid_data[$key][$column] = date("Y-m-d H:i:s",$row->unix_time);
                   
                }else{
                    $new_grid_data[$key][$column] = $row->{$column};
                }
                
            }
        }
        
        $this->data['grid_data']          = $new_grid_data; 
        
        $this->data['count_all_records']  = $this->notification_model->get_count_all_notifications($search_word,$type);
        
        $this->data['display_lang_id']    = $lang_id;
         
        $this->data['unset_edit']         = true;
       
        $count_data  = $this->data['count_all_records'];
        $output_data = $this->load->view('Admin/grid/grid_data',$this->data, true);
        
        echo json_encode(array($output_data, $count_data, $search_word));
     }
     
     
     public function read($id)
     {
        $id = intval($id);
        if($id)
        {
            $data     = $this->notification_model->get_row_data($id);
            
            $row_data = array(
                              lang('notification_text') => $data->notification_text ,
                              lang('notification_type') => $data->type ,
                              lang('notification_time') => date("Y/m/d H:i",$data->unix_time) ,
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
        $notification_ids = $this->input->post('row_id');

        if(is_array($notification_ids))
        { 
            
            $ids_array = array();
            
            foreach($notification_ids as $notification_id)
            {
                $ids_array[] = $notification_id['value'];
            }
        }else{ 
            
            $ids_array = array($notification_ids);
        }
            
        $this->notification_model->delete_notifications_data($ids_array);
       echo "1"; 
     }  
     
    public function read_notifications()
    {
        $this->notification_model->read_notifications();
    }
     
/************************************************************************/    
}