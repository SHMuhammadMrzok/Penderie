<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Customers_orders extends CI_Controller
{
    public   $lang_row;
 
    public function __construct()
    {
        parent::__construct();
        
        require(APPPATH . 'includes/global_vars.php');
        
        $this->load->library('pagination');
        $this->load->library('encryption');
        
        $this->load->model('users/users_model');
        $this->load->model('orders/orders_model');
        
        $this->config->load('encryption_keys');
        
        $this->lang_row = $this->admin_bootstrap->get_active_language_row(); 
    }
    
    public function index($page_id = 1)
    {
        $users_data = array();
        $lang_id    = $this->data['active_language']->id;
        $perPage    = 25; 
        $offset     = ($page_id -1 ) * $perPage;

        if($offset < 0)
        {
            $offset = $perPage;
        }
        
        $config['base_url']    = base_url()."reports/customers_orders/index/";
        $config['per_page']    = $perPage;
        //$config['first_link']  = FALSE;
        //$config['last_link']   = FALSE;
        $config['uri_segment'] = 4;
        $config['use_page_numbers'] = TRUE;
        
         $config['first_link'] = lang('first_page');
        $config['last_link'] = lang('last_page');
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li><strong>';
        $config['cur_tag_close'] = '</strong></li>';
        
        $config['total_rows']  = $this->users_model->get_users_count();
        $this->pagination->initialize($config);
        
        $default_currency   = $this->admin_bootstrap->get_default_currency_symbol();
        $all_users          = $this->users_model->get_all_users_data($lang_id, $perPage, $offset);
        
        foreach($all_users as $user)
        {
            $total                   = 0;
            $all_products_count      = 0;
            //$user_orders             = $this->orders_model->get_user_orders_count($user->id);
            $user_completed_orders   = $this->orders_model->get_user_orders($user->id);
            $user->{'orders_count'}  = count($user_completed_orders);            
            
            foreach($user_completed_orders as $order)
            {
                $all_products_count += $order->items_count;
                
                $total_with_default_currency = $this->admin_bootstrap->get_amount_with_default_currency($order->final_total, $order->country_id);
                $total                      += $total_with_default_currency;
            }
            
            $user->{'orders_total'}       = $total.' '.$default_currency;
            $user->{'all_products_count'} = $all_products_count;
            
            if($user->active == 1)
            {
                $user->{'status'} = lang('active');
            }
            else
            {
                $user->{'status'} = lang('not_active');
            }
            
            $users_data[] = $user;
        }
        
        $this->data['users_data'] = $users_data;
        $this->data['pagination'] = $this->pagination->create_links();
        
        $this->data['content']    = $this->load->view('customers_orders', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
        
    }
    
     
    
/************************************************************************/    
}