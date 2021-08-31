<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Sales_coupons extends CI_Controller
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
        
        $perPage = 25; 
        $offset  = ($page_id -1 ) * $perPage;

        if($offset < 0)
        {
            $offset = $perPage;
        }
        
        $config['base_url']    = base_url()."reports/sales_coupons/index/";
        $config['per_page']    = $perPage;
        $config['first_link']  = FALSE;
        $config['last_link']   = FALSE;
        $config['uri_segment'] = 4;
        $config['use_page_numbers'] = TRUE;
        
        $config['first_link']      = lang('first_page');
        $config['last_link']       = lang('last_page');
        $config['first_tag_open']  = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open']   = '<li>';
        $config['last_tag_close']  = '</li>';
        $config['next_tag_open']   = '<li>';
        $config['next_tag_close']  = '</li>';
        $config['prev_tag_open']   = '<li>';
        $config['prev_tag_close']  = '</li>';
        $config['num_tag_open']    = '<li>';
        $config['num_tag_close']   = '</li>';
        $config['cur_tag_open']    = '<li><strong>';
        $config['cur_tag_close']   = '</strong></li>';
        
        $config['display_pages']   = TRUE;
        
        $config['total_rows']  = $this->coupon_codes_model->get_count_all_coupon_codes($lang_id);
        
        $this->pagination->initialize($config);
        
        $coupons_data_array = array();
        
        $coupons            = $this->coupon_codes_model->get_all_coupons_data($lang_id, $perPage, $offset);
        $default_currency   = $this->admin_bootstrap->get_default_currency_symbol();
        
        foreach($coupons as $coupon)
        {
            $discount_with_default_currency = 0;
            $uses_count             = $this->coupon_codes_model->get_coupon_uses_count($coupon->id);
            $coupon->{'uses_count'} = $uses_count;
            $coupon_total_discount  = 0;
            
            if($uses_count > 0)
            {
                $used_coupon_data               = $this->coupon_codes_model->get_used_coupon_data($coupon->id);
                $coupon_total_discount          = $this->coupon_codes_model->get_coupon_total_discount($coupon->id);
                
                $discount_with_default_currency = $this->admin_bootstrap->get_amount_with_default_currency($coupon_total_discount, $coupon->country_id);
            }
            
            $coupon->{'uses_count'}     = $uses_count;
            $coupon->{'total_discount'} = round($discount_with_default_currency, 2).' '.$default_currency;
            
            $coupons_data_array[]       = $coupon;
        }
        
        $this->data['pagination']   = $this->pagination->create_links();
        $this->data['coupons_data'] = $coupons_data_array;
        $this->data['lang_id']      = $this->data['active_language']->id;
        
        $this->data['content']      = $this->load->view('sales_coupons', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
        
    }
    
    
/************************************************************************/    
}