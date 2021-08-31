<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Sales_orders extends CI_Controller
{
    public   $lang_row;
 
    public function __construct()
    {
        parent::__construct();
        
        require(APPPATH . 'includes/global_vars.php');
        
        $this->load->library('pagination');
        
        $this->load->model('orders/orders_model');
        $this->load->model('coupon_codes/coupon_codes_model');
        
        $this->lang_row = $this->admin_bootstrap->get_active_language_row(); 
    }
    
    public function index($page_id = 1)
    {
        $lang_id = $this->data['active_language']->id;
        
        $perPage = 20; 
        $offset  = ($page_id -1 ) * $perPage;

        if($offset < 0)
        {
            $offset = $perPage;
        }
        
        $config['base_url']    = base_url()."reports/sales_orders/index/";
        $config['per_page']    = $perPage;
        $config['first_link']  = FALSE;
        $config['last_link']   = FALSE;
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
        
        $config['total_rows']  = $this->orders_model->get_count_completed_orders();
        $this->pagination->initialize($config);
        
        $orders      = $this->orders_model->get_all_orders_data($perPage, $offset, $lang_id);
        $orders_data = array();
        
        foreach($orders as $order)
        {
            $start_date      = $this->orders_model->get_order_start_time($order->id);
            $end_date        = $this->orders_model->get_order_end_time($order->id);
            $products_count  = $this->orders_model->get_orders_products_count($order->id);
            
            $order->{'start_date'}     = $start_date;
            $order->{'end_date'}       = $end_date ;
            $order->{'products_count'} = $products_count ;
            
            $orders_data[] = $order;
        }
        
        $this->data['pagination']  = $this->pagination->create_links();
        $this->data['orders_data'] = $orders_data;
        
        $this->data['content']     = $this->load->view('sales_orders', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
        
    }
     
    
/************************************************************************/    
}