<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Affiliate_log extends CI_Controller
{
    public $crud;
    
    public function __construct()
    {
        parent::__construct();
        
        require(APPPATH . 'includes/global_vars.php');
       
        $this->load->model('affiliate_log_model');
        $this->load->model('users/users_model');
    }
    
    
    public function index()
    {
        $lang_id = $this->data['active_language']->id;
        
        $this->data['count_all_records']    = $this->affiliate_log_model->get_count_all_affiliate_log($lang_id);
        $this->data['data_language']        = $this->lang_model->get_active_data_languages();
        $this->data['filters']           = array(
                                                   array(
                                                          'filter_title' => lang('affiliate_user'),
                                                          'filter_name'  => 'users_filters',
                                                          'filter_data'  => $this->affiliate_log_model->get_users_filter_data()
                                                         ) ,
                                                   array(
                                                          'filter_title' => lang('buyers_filters'),
                                                          'filter_name'  => 'buyers_filters',
                                                          'filter_data'  => $this->affiliate_log_model->get_users_filter_data()
                                                         )
                                                 );
        
        $this->data['columns']              = array(
                                                     lang('affiliate_user')     ,
                                                     lang('buyer')              ,
                                                     lang('order_id')           ,
                                                     lang('commission_amount')  ,
                                                     lang('order_total')        ,
                                                     lang('order_date')         ,
                                                     lang('commission')         ,
                                                     lang('pay_stat')           ,
                                                     lang('pay_status_update')
                                                   );
                                                   
        $this->data['orders']              = array(
                                                     //lang('affiliate_user') ,
                                                     //lang('buyer')          ,
                                                     lang('amount')         ,
                                                     lang('commission')     ,
                                                     lang('pay_stat')       ,
                                                   );
                                                   
        //$this->data['orders']                = $this->data['columns'] ;                                                     
            
        $this->data['actions']              = array( 'delete'=>lang('delete'));
        $this->data['search_fields']        = array( lang('username'), );
        
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
        
        if(isset($_POST['filter'])&& isset($_POST['filter_data']))
        {
            $filters      = $this->input->post('filter');
            $filters_data = $this->input->post('filter_data');
            
            $users_filter_id     = intval($filters_data[0]);
            $buyers_filter_id    = intval($filters_data[1]);
        }
        else
        {
            $users_filter_id     = 0;
            $buyers_filter_id    = 0;       
        }  
        
        $grid_data  = $this->affiliate_log_model->get_affiliate_log_data($limit, $offset, $search_word, $order_by, $order_state, $users_filter_id, $buyers_filter_id);
        
        $db_columns = array(  
                             'id'               ,
                             'user_username'    ,   
                             'buyer_username'   ,
                             'order_id'         ,
                             'amount'           ,
                             'final_total'      ,
                             'order_time'       ,
                             'commission'       ,
                             'pay'              ,
                             'pay_status'       ,
                           );
                       
       $this->data['hidden_fields'] = array('id','sort');
                                           
       $new_grid_data = array();
        
        foreach($grid_data as $key =>$row)
        { 
            //$user
            foreach($db_columns as $column)
            {
                
                if($column == 'pay_status')
                {
                    $new_grid_data[$key][$column] = '<a href="'.base_url().'affiliate/affiliate_log/update_stat/'.$row->id.'"><img src="'.base_url().'assets/template/admin/img/edit.png" title="'.lang('pay_status_update').'" /></a>';
                }
                elseif($column == 'user_username')
                {
                    $userdata = $this->admin_bootstrap->get_user_by_id($row->user_id);
                    $new_grid_data[$key][$column] = $userdata->first_name . ' ' . $userdata->last_name;
                }
                elseif($column == 'order_id')
                {
                    $new_grid_data[$key][$column] = '<a target="_blank" href="'.base_url().'orders/admin_order/view_order/'.$row->order_id.'">'.$row->order_id.'</a>';
                }
                elseif($column == 'buyer_username')
                {
                    $buyer_data = $this->admin_bootstrap->get_user_by_id($row->buyer_id);
                    
                    $new_grid_data[$key][$column] = $buyer_data->first_name . ' ' . $buyer_data->last_name;
                }
                elseif($column == 'amount')
                {
                    $new_grid_data[$key][$column] = $row->amount . ' ' . $row->currency_symbol;
                }
                elseif($column == 'final_total')
                {
                    $new_grid_data[$key][$column] = $row->final_total . ' ' . $row->currency_symbol;
                }
                elseif($column == 'pay')
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
                elseif($column == 'order_time')
                {
                    $new_grid_data[$key][$column] = date('Y/m/d H:i', $row->unix_time);
                }
                else{
                    
                    $new_grid_data[$key][$column] = $row->{$column};
                }
            }
        }
        
        
        $this->data['grid_data']         = $new_grid_data;
        $this->data['count_all_records'] = $this->affiliate_log_model->get_count_all_affiliate_log($search_word ,$users_filter_id ,$buyers_filter_id);
        $this->data['display_lang_id']   = $lang_id;
        $this->data['unset_edit']           = true;
        
        $output_data = $this->load->view('Admin/grid/grid_data',$this->data, true);
        $count_data  = $this->data['count_all_records'];
        
        echo json_encode(array($output_data, $count_data, $search_word));
    }
    
    
    public function update_stat($id)
    {
        $id = intval($id);
        
        $affilliate_pay = $this->affiliate_log_model->get_affilliate_row_pay($id);
        
        $data = array();
        
        if($affilliate_pay == 0)
        {
            $data = array ('pay'=> 1);
        
        }elseif($affilliate_pay == 1)
        {
            $data = array ('pay'=> 0);
        }
        
        $this->affiliate_log_model->update_affilliate_pay_status($id, $data);
        
        redirect('affiliate/affiliate_log/index','refresh');
        
    }
    
    public function read($id, $display_lang_id)
    {
        $this->load->model('orders/orders_model');
        $id = intval($id);
        $display_lang_id = intval($display_lang_id);
    
        if($id)
        {
            $data = $this->affiliate_log_model->get_row_data($id);
            
            if($data)
            {
                if($data->pay == 1)
                {
                    $active_value = lang('yes');
                }
                else
                {    
                    $active_value = lang('no');
                }
                
                $order_data = $this->orders_model->get_order_data($data->order_id);
                $aff_user   = $this->admin_bootstrap->get_user_by_id($data->user_id);
                $aff_buyer  = $this->admin_bootstrap->get_user_by_id($data->buyer_id);
            
                $row_data = array(
                                    lang('affiliate_user')      => $aff_user->first_name.' '.$aff_user->last_name       ,
                                    lang('buyer')               => $aff_buyer->first_name.' '.$aff_buyer->last_name     ,
                                    lang('order_total')         => $order_data->total.' '.$order_data->currency_symbol  ,
                                    lang('commission')          => $data->commission."%"                                ,
                                    lang('order_id')            => '<a href="'.base_url().'orders/admin_order/view_order/'.$data->order_id.'" target="_blank">'.$data->order_id.'</a>',
                                    lang('order_date')          => date('Y/m/d H : i', $order_data->unix_time)          ,
                                    lang('commission_amount')   => $data->amount.' '.$order_data->currency_symbol       ,
                                    lang('pay_stat')            => '<span class="badge badge-info">'.$active_value.'</span>'
                                );
                             
            
        
            $this->data['row_data'] = $row_data;
            
            $this->data['content']  = $this->load->view('Admin/grid/read_view', $this->data, true);
            $this->load->view('Admin/main_frame',$this->data);
            
            }
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
        $affiliate_ids = $this->input->post('row_id');

        if(is_array($affiliate_ids))
        { 
            
            $ids_array = array();
            
            foreach($affiliate_ids as $cat_id)
            {
                $ids_array[] = $cat_id['value'];
            }
        }
        else
        { 
            $ids_array = array($affiliate_ids);
        }
            
        $this->affiliate_log_model->delete_affiliate_log_data($ids_array);
        
        
    }
    
/************************************************************************/    
}