<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Products_stock extends CI_Controller
{
    public   $lang_row;

    public function __construct()
    {
        parent::__construct();

        require(APPPATH . 'includes/global_vars.php');

        $this->load->library('currency');
        $this->load->library('pagination');
        $this->load->library('products_lib');

        $this->load->model('products/products_model');
        $this->load->model('users/countries_model');
        $this->load->model('vendors/vendors_model');
        $this->load->model('products/purchase_orders_model');

        $this->lang_row = $this->admin_bootstrap->get_active_language_row();
    }

    public function index($page_id = 1)
    {
        $country_data   = array();
        $products_array = array();
        $lang_id        = $this->data['active_language']->id;

        $perPage = 10;
        $offset  = ($page_id -1 ) * $perPage;

        if($offset < 0)
        {
            $offset = $perPage;
        }

        $config['base_url']         = base_url()."reports/products_stock/index/";
        $config['per_page']         = $perPage;
        $config['first_link']       = FALSE;
        $config['last_link']        = FALSE;
        $config['uri_segment']      = 4;
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

        $config['total_rows']       = $this->products_model->get_count_all_products($lang_id);//$this->products_model->get_count_country_products($lang_id);

        $this->pagination->initialize($config);

        $system_products       = $this->products_model->get_products_data($lang_id, $perPage, $offset, '','','desc');
        //$this->products_model->get_products_per_countries($lang_id, $perPage, $offset);
        $default_currency_data = $this->currency->get_default_currency_data();

        foreach($system_products as $product)
        {
            $products_countries = $this->products_model->get_products_countries_data($product->id, $lang_id);

            $country_data = array();
            foreach($products_countries as $country)
            {
                $current_qty    = $country->product_quantity;
                $avg_cost       = $country->average_cost;
                if(! $avg_cost)
                {
                    $avg_cost = 0;
                }
                $total_avg_cost = $avg_cost * $current_qty;


                $country->{'average_cost'}   = $avg_cost.' '.$default_currency_data->currency_symbol;
                $country->{'current_qty'}    = $current_qty;
                $country->{'total_avg_cost'} = $total_avg_cost.' '.$default_currency_data->currency_symbol;

                $country_data[] = $country;
            }

            if($product->serials_per_country == 0)
            {
                $global_qty         = $this->products_lib->get_product_stock_per_country($product->id, 0);
                $global_avg_cost    = $this->products_serials_model->get_product_global_avg_cost($product->id);
                if(! $global_avg_cost)
                {
                    $global_avg_cost = 0;
                }
                $global_array       = (object) array(
                                            'name'              => lang('global_quantitiy') ,
                                            'current_qty'       => $global_qty              ,
                                            'average_cost'      => $global_avg_cost.' '.$default_currency_data->currency_symbol ,
                                            'total_avg_cost'    => ($global_qty * $global_avg_cost) .' '. $default_currency_data->currency_symbol
                                        );
                $country_data[] = $global_array;
            }

            $total_array = $this->_product_sales($product->product_id);

            $product->{'country_data'} = $country_data;

            $product->{'total_array'}  = $total_array;

            if(count($total_array) != 0)
            {
              $total_qty         = $total_array['total_qty'];
              $total_price       = $total_array['total_price'];
              $final_total_price = $total_array['final_total_price'];
            }
            else {

              $total_qty         = 0;
              $total_price       = 0;
              $final_total_price = 0;
            }

            $product->{'total_qty'}         = $total_qty;
            $product->{'total_price'}       = $total_price;
            $product->{'final_total_price'} = $final_total_price;

            $products_array[] = $product;
        }

        $totals_array   = $this->products_model->get_products_countries_totals();//array($total_qty, $grand_total_cost, $grand_cost_with_qty);
        $average_cost   = round($totals_array->average_cost, 4);
        $total_avg_cost = $average_cost * $totals_array->product_quantity;

        $average_cost   = $average_cost .' '. $default_currency_data->currency_symbol;
        $total_avg_cost = $total_avg_cost .' '. $default_currency_data->currency_symbol;

        $totals_array->{'total'}        = $total_avg_cost;
        $totals_array->{'average_cost'} = $average_cost;


        $this->data['products']      = $products_array;
        $this->data['total_vals']    = $totals_array;
        $this->data['currency']      = $default_currency_data->currency_symbol;
        $this->data['pagination']    = $this->pagination->create_links();

        $this->data['content']       = $this->load->view('products_stock', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
    }

    private function _product_sales( $product_id)
    {
        $display_lang_id = $this->data['lang_id'];

        $total       = array();
        $total_price = 0;
        $total_qty   = 0;
        $final_total_price = 0;

        $products_total_sales = $this->products_model->get_product_sales($product_id ,$display_lang_id);

        foreach($products_total_sales as $row)
        {
            $price            = $this->currency->get_amount_with_default_currency($row->final_price, $row->country_id);

            $total_price       += $price;
            $final_total_price += ($price * $row->qty);
            $total_qty         += $row->qty;

            $total['total_qty']         = $total_qty;
            $total['total_price']       = $total_price;
            $total['final_total_price'] = $final_total_price;
        }

        return $total;
    }



/************************************************************************/
}
