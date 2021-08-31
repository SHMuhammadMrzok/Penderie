<?php 
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Admin_payment_log extends CI_Controller
{
    public $stores;
    public $stores_ids;
    
    public function __construct()
    {
        parent::__construct();
        
        require(APPPATH . 'includes/global_vars.php');
        
        $this->load->model('payment_log_model');
        $this->load->model('users/user_model');
        
        $this->lang_row = $this->admin_bootstrap->get_active_language_row();
        
        $this->stores   = $this->admin_bootstrap->get_user_available_stores();
        
        $store_id_array = array();
        
        foreach($this->stores as $store)
        {
            $store_id_array[] = $store->store_id;
        }
        
        $this->stores_ids = $store_id_array; 
    }

    

    public function index()
    {
        $lang_id       = $this->data['active_language']->id;
        
        $filter_users  = $this->user_model->get_users_result();
        $filter_status = $this->payment_log_model->get_payment_log_status($lang_id);
        
        $this->data['count_all_records'] = $this->payment_log_model->get_count_all_logs($lang_id, '', 0, 0, 0, 0, $this->stores_ids);
        $this->data['data_language']     = $this->lang_model->get_active_data_languages();        
        
        $this->data['columns']           = array(
                                                     lang('transaction_id'),
                                                     lang('username'),
                                                     lang('status'),
                                                     lang('unix_time'),
                                                     lang('pay_id')
                                                   );
            
        $this->data['filters']           = array(
                                                      array(
                                                             'filter_title'         => lang('users_filter'),
                                                             'filter_name'          => 'user_filter_id',
                                                             'filter_data'          => $filter_users
                                                           ),
                                                      array(
                                                             'filter_title'         => lang('status_filter'),
                                                             'filter_name'          => 'status_filter_id',
                                                             'filter_data'          => $filter_status
                                                           ),
                                                 );
        $this->data['date_filter']       = true;
        $this->data['search_fields']     = array(lang('transaction_id'), lang('order_id'), lang('username'), lang('status'));
        $this->data['index_method_id']      = $this->data['method_id'];
        
        $this->data['content']           = $this->load->view('Admin/grid/grid_html', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
    }
    
    public function ajax_list()
    {
        /**************************************/
        $this->stores   = $this->admin_bootstrap->get_user_available_stores($_POST['index_method_id']);
        $store_id_array = array();
        
        foreach($this->stores as $store)
        {
            $store_id_array[] = $store->store_id;
        }
        
        $this->stores_ids = $store_id_array;
        /**************************************/
        if(isset($_POST['lang_id']))
        {
            $lang_id = intval($this->input->post('lang_id'));
        }
        else
        {
            $lang_id = $this->data['active_language']->id;    
        }
        if(isset($_POST['limit']))
        {
            $limit = intval($this->input->post('limit'));
        }
        else
        {
            $limit = 1;    
        }
        
        if(isset($_POST['page_number']))
        {
            $active_page = intval($this->input->post('page_number'));
        }
        else
        {
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
            $filters       = $this->input->post('filter');
            $filters_data  = $this->input->post('filter_data');
            
            $users_filter  = intval($filters_data[0]);
            $status_filter = intval($filters_data[1]);
        }
        else
        {
            $users_filter  = 0;
            $status_filter = 0;
        }
        
        if(isset($_POST['date_from']))
        {
            $date_from = strtotime($this->input->post('date_from'));
        }
        else
        {
            $date_from = 0;
        }
        
        if(isset($_POST['date_to']))
        {
            $date_to = strtotime($this->input->post('date_to'));
        }
        else
        {
            $date_to = 0;
        }
        
        
        
        $grid_data   = $this->payment_log_model->get_payment_log_data($lang_id, $limit, $offset, $search_word, $order_by, $order_state, $users_filter, $status_filter, $date_from, $date_to, $this->stores_ids);
        
        $db_columns  = array(
                              'id'              ,   
                              'order_id'        ,
                              'username'        ,
                              'status'          ,
                              'unix_time'       ,
                              'transaction_id'  ,
                              
                            );
                       
        $this->data['hidden_fields'] = array('id');
        
        $new_grid_data = array();
        
        foreach($grid_data as $key =>$row)
        {
            
            foreach($db_columns as $column)
            {
                if($column == 'unix_time')
                {
                    $new_grid_data[$key][$column] = date('Y/m/d', $row->unix_time);
                }
                elseif($column == 'username')
                {
                    $new_grid_data[$key][$column] = $row->first_name .' '. $row->last_name;
                }
                else
                {
                    $new_grid_data[$key][$column] = $row->{$column};
                }
            }
        }
        
        $this->data['unset_delete']       = true;
        $this->data['unset_edit']         = true;
        $this->data['grid_data']          = $new_grid_data; 
        
        $this->data['count_all_records']  = $this->payment_log_model->get_count_all_logs($lang_id, $search_word, $users_filter, $status_filter, $date_from, $date_to, $this->stores_ids);
        
        $this->data['display_lang_id']    = $lang_id;
         
        
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
            $data = $this->payment_log_model->get_row_data($id, $display_lang_id);
            
            if($data)
            {
                //if(in_array($data->store_id, $this->stores_ids))
                {
                    if(isset($data->first_name))
                    {
                        $username = $data->first_name.' '.$data->last_name;
                    }
                    else
                    {
                        $username = lang('visitor');
                    }
                
                    $status_id = $data->status_id;
                    
                    if($status_id == 1)
                    {
                        $status = lang('success_pay');
                    }
                    elseif($status_id == 2)
                    {
                        $status = lang('pending');
                    }
                    elseif($status_id == 3)
                    {
                        $status = lang('failuer');
                    }
                    elseif($status_id == 4)
                    {
                        $status = lang('hacked');
                    }
                
                    $row_data = array(
                                        lang('username')       => $username             ,
                                        lang('ip_address')     => $data->ip_address     ,
                                        lang('payment_method') => $data->payment_method ,
                                        lang('currency')       => $data->currency       ,
                                        lang('transaction_id')       => $data->order_id       ,
                                        //lang('total')          => $data->total      ,
                                        lang('pay_id')         => $data->transaction_id	,
                                        lang('status')         => $status               ,
                                        lang('unix_time')      => date('Y/m/d', $data->unix_time)
                                     );
                    
                    $this->data['feed_back_text'] = json_decode($data->feed_back_text);
                
                    $this->data['row_data'] = $row_data;
                }
                /*else
                {
                    $this->data['error_msg'] = lang('no_store_permission');
                }*/
            }
            
            $this->data['content']  = $this->load->view('payment_log_read', $this->data, true);
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
        $status_ids = $this->input->post('row_id');

        if(is_array($status_ids))
        { 
            
            $ids_array = array();
            
            foreach($status_ids as $row)
            {
                $ids_array[] = $row['value'];
            }
        }
        else
        { 
            $ids_array = array($status_ids);
        }
            
        $this->order_status_model->delete_status_data($ids_array);
     }
/************************************************************************/    
}