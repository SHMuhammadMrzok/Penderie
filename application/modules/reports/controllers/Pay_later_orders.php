<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Pay_later_orders extends CI_Controller
{
    public   $lang_row;

    public function __construct()
    {
        parent::__construct();

        require(APPPATH . 'includes/global_vars.php');

        $this->load->library('pagination');
        $this->load->model('orders/pay_later_model');

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
            $offset = 0;
        }

        $config['base_url']    = base_url()."reports/pay_later_bills/index/";
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

        $config['total_rows']  = $this->pay_later_model->get_pay_later_orders_count();
        $this->pagination->initialize($config);

        $orders = $this->pay_later_model->get_pay_later_orders_data($perPage, $offset);
        
        $this->data['later_orders'] = $orders;
        $this->data['pagination']   = $this->pagination->create_links();

        $this->data['content']    = $this->load->view('later_orders', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);

    }



/************************************************************************/
}
