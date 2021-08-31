<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Products_purchased extends CI_Controller
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
        $this->load->model('products/products_serials_model');

        $this->lang_row = $this->admin_bootstrap->get_active_language_row();
    }


    public function index($page_id = 1)
    {
        $products_data = array();
        $lang_id       = $this->data['active_language']->id;

        $perPage = 25;
        $offset  = ($page_id -1 ) * $perPage;

        if($offset < 0)
        {
            $offset = $perPage;
        }

        $config['base_url']    = base_url()."reports/Products_purchased/index/";
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

        $config['total_rows']  = $this->purchase_orders_model->get_purshased_orders_products_count($lang_id);
        $this->pagination->initialize($config);

        $purshased_orders_products = $this->purchase_orders_model->get_all_purshased_orders_products($lang_id, $perPage, $offset);

        foreach($purshased_orders_products as $product)
        {
            $vendor_name = $this->vendors_model->get_vendor($product->vendor_id, $lang_id)->title;
            $current_qty = $this->products_serials_model->count_order_product_serials($product->product_id, $product->purchase_order_id);
            if($product->country_id != 0)
            {
                $country = $this->countries_model->get_country_name($product->country_id, $lang_id);
            }
            else
            {
                $country = lang('global_quantitiy');
            }

            $product->{'vendor_name'} = $vendor_name;
            $product->{'country'}     = $country;
            $product->{'current_qty'} = $current_qty;

            $products_data[] = $product;
        }

        $this->data['stock_data'] = $products_data;
        $this->data['pagination'] = $this->pagination->create_links();

        $this->data['content']  = $this->load->view('products_purchased', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
    }





/************************************************************************/
}
