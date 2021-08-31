<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Products_viewed extends CI_Controller
{
    public   $lang_row;
 
    public function __construct()
    {
        parent::__construct();
        
        require(APPPATH . 'includes/global_vars.php');
        
        $this->load->library('pagination');
        
        $this->load->model('products/products_model');
        $this->load->model('users/countries_model');
        $this->load->model('vendors/vendors_model');
        $this->load->model('products/purchase_orders_model');
        
        $this->lang_row = $this->admin_bootstrap->get_active_language_row(); 
    }    
    
    public function index($page_id = 1)
    {
        $perPage = 25; 
        $offset  = ($page_id -1 ) * $perPage;

        if($offset < 0)
        {
            $offset = $perPage;
        }
        
        $lang_id  = $this->data['active_language']->id;
        
        $config['base_url']    = base_url()."reports/products_viewed/index/";
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
        
        $config['total_rows']      = $this->products_model->get_all_products_with_limit_count($lang_id);
        $this->pagination->initialize($config);
        
        $products = $this->products_model->get_all_products_with_limit($lang_id, $perPage, $offset);
        
        $total_views = 0;
        
        foreach($products as $product)
        {
            $total_views += $product->view;
        }
        
        foreach($products as $product)
        {
            if($total_views == 0)
            {
                $product_percent = 0;
                $product->{'percent'} = 0;
            }
            else
            {
                $product_percent      = ($product->view / $total_views) * 100;
                $product->{'percent'} = round($product_percent, 2);
            }
            
        }
        
        
        $this->data['products']   = $products;
        $this->data['pagination'] = $this->pagination->create_links();
        
        $this->data['content']  = $this->load->view('products_viewed', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data); 
    }
    
    
     
    
/************************************************************************/    
}