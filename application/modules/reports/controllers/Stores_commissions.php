<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Stores_commissions extends CI_Controller
{
    public   $lang_row;

    public function __construct()
    {
        parent::__construct();

        require(APPPATH . 'includes/global_vars.php');

        $this->load->library('pagination');

        $this->load->model('stores/stores_model');
        $this->load->model('orders/orders_model');

        $this->lang_row = $this->admin_bootstrap->get_active_language_row();
    }

    public function index($page_id = 1)
    {
        $lang_id = $this->data['active_language']->id;

        $perPage = 20;
        $offset  = ($page_id -1 ) * $perPage;

        $config['base_url']    = base_url()."reports/stores_commissions/index/";
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

        $config['total_rows']  = $this->stores_model->get_count_all_stores($lang_id);
        $this->pagination->initialize($config);

        $stores = $this->stores_model->get_stores_data($lang_id, $perPage, $offset);
        $default_currency = $this->admin_bootstrap->get_default_currency_symbol();

        $stores_array  = array();

        foreach($stores as $row)
        {
          $site_comm    = 0;
          //$orders_count = 0;
          //$store_orders_total = 0;

          $orders_count = $this->orders_model->get_report_orders_count($row->id);
          $store_orders_total = $this->orders_model->get_orders_final_total($row->id)->orders_total;
          $store_orders_total = round($store_orders_total, 2);

          if($row->commission_type == 'amount')
          {
            $site_comm = $orders_count * $row->commission;
          }
          else if($row->commission_type == 'percent')
          {
            $site_comm = $store_orders_total * $row->commission / 100;
          }

          $row->{'site_commission'} = $site_comm;
          $row->{'orders_count'}    = $orders_count;
          $row->{'orders_total'}    = $store_orders_total;

          $stores_array[] = $row;
        }

        $this->data['pagination']  = $this->pagination->create_links();
        $this->data['report_data'] = $stores_array;
        $this->data['currency']    = $default_currency;

        $this->data['content']     = $this->load->view('stores_report', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);

    }


/************************************************************************/
}
