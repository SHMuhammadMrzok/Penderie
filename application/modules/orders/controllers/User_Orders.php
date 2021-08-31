<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class User_orders extends CI_Controller
{
    public $status=1;
    public $data = array();

    public function __construct()
    {
        parent::__construct();

        $this->load->library('encryption');
        $this->load->library('pagination');

        $this->config->load('encryption_keys');

        $this->load->model('orders/orders_model');
        $this->load->model('payment_options/payment_methods_model');

        require(APPPATH . 'includes/front_end_global.php');


    }

    public function index()
    {
        if($this->ion_auth->logged_in())
        {
            $this->data['content'] = $this->load->view('users_order_log_html', $this->data, true);
            $this->load->view('site/inner_main_frame',$this->data);
        }
        else
        {
            $this->session->set_userdata('redir', current_url());
			redirect('users/users/user_login', 'refresh');
        }
    }

    public function ajax_list()
    {
        $lang_id = $this->data['lang_id'];

        if(isset($_POST['limit']))
        {
            $limit = intval($this->input->post('limit'));
        }else{
            $limit = 100;
        }

        if(isset($_POST['page_number']))
        {
            $active_page = intval($this->input->post('page_number'));
        }else{
            $active_page = 1;
        }

        $offset  = ($active_page-1) * $limit;

        $order_number  = intval($this->input->post('order_number'));
        $order_date    = $this->input->post('order_date');
        $product_title = trim($this->input->post('product_title'));
        $final_total   = $this->input->post('final_total');
        $status        = $this->input->post('status');
        $start_date    = strtotime($order_date);
        $end_date      = $start_date + 86399;

        $user_id          = $this->user_bootstrap->get_user_id();
        $display_lang_id  = $this->user_bootstrap->get_active_language_row()->id;

        $users_order_data = $this->orders_model->get_user_order_data($user_id, $display_lang_id, $limit, $offset, $order_number, $start_date, $end_date, $final_total, $status);

        $orders_array     = array();

        if(count($users_order_data) != 0)
        {
            foreach($users_order_data as $order)
            {
                $order_products         = $this->orders_model->get_order_products($order->id, $display_lang_id, $product_title);
                $order_charge_cards     = $this->orders_model->get_recharge_card($order->id);
                $order->{'products'}    = $order_products;
                $order->{'charge_card'} = $order_charge_cards;

                if($order->order_status_id == 1)
                {
                    $label = 'success';
                }
                else if($order->order_status_id == 2)
                {
                    $label = 'warning';
                }
                else if($order->order_status_id == 3)
                {
                    $label = 'danger';
                }

                $order->{'label'} = $label;

                $orders_array[]   = $order;
            }
        }

        $config['base_url']    = base_url()."orders/order/filter_order_log/";
        $config['per_page']    = $limit;
        $config['first_link']  = FALSE;
        $config['last_link']   = FALSE;
        $config['uri_segment'] = 4;
        $config['use_page_numbers'] = TRUE;

        $config['total_rows']  = $this->orders_model->get_all_orders_count($lang_id, $user_id, $order_number, $start_date, $end_date, $final_total, $status);

        $this->pagination->initialize($config);

        $this->data['orders_data'] = $orders_array;
        $this->data['pagination']  = $this->pagination->create_links();

        //$this->load->view('orders/orders_log_ajax_view', $this->data);

        $count_data  = $config['total_rows'];
        $output_data = $this->load->view('orders_log_ajax_view',$this->data, true);

        echo json_encode(array($output_data, $count_data));
    }


    public function view_order_details($order_id)
    {
        $order_id = intval($order_id);

        if($this->data['is_logged_in'])
        {
            $display_lang_id = $this->user_bootstrap->get_active_language_row()->id;
            $order_details   = $this->orders_model->get_order_details($order_id, $display_lang_id);
            $user_id         = $this->user_bootstrap->get_user_id();

            if($order_details)
            {
                $cards_array       = array();
                $log_array         = array();
                $charge_card       = false;
                $charge_card_count = $this->orders_model->get_recharge_cards_count($order_id);

                if($charge_card_count > 0)
                {
                    $charge_card = true;
                    $cards_array = $this->orders_model->get_recharge_card($order_id);
                }

                $order_products               = $this->orders_model->get_order_products($order_id, $display_lang_id);
                $order_log                    = $this->orders_model->get_orders_log($order_id, $display_lang_id);

                foreach($order_log as $log)
                {
                    if($log->status_id == 1)
                    {
                        $class = 'success';
                    }
                    elseif($log->status_id == 2)
                    {
                        $class = 'warning';
                    }
                    elseif($log->status_id == 3)
                    {
                        $class = 'danger';
                    }

                    $log->{'class'} = $class;

                    $log_array[] = $log;
                }

                if($this->ion_auth->logged_in())
                {
                    $user_customer_group_id       = $this->user_bootstrap->get_user_data()->customer_group_id;
                    $wholesaler_customer_group_id = $this->config->item('wholesaler_customer_group_id');
                }

                $go_to_payment = 'false';
                $pay_by_paypal = 'false';
                $pay_by_cashu  = 'false';
                $cashu_data    = array();


                $payment_method      = $this->payment_methods_model->get_payment_method_name($order_details->payment_method_id, $display_lang_id);

                if($order_details->payment_method_id == 5)
                {
                    $pay_by_paypal = 'true';
                }
                elseif($order_details->payment_method_id == 6)
                {
                    $pay_by_cashu   = 'true';
                }
                else
                {
                    $payment_check_count = $this->orders_model->get_order_payment_count($order_id);

                    if($payment_check_count == 0 && $order_details->order_status_id == 2)
                    {
                        $go_to_payment = 'true';
                    }
                }


                $wholesaler_options = false;

                if ( $this->ion_auth->logged_in() && $user_customer_group_id == $wholesaler_customer_group_id)
                {
                    $wholesaler_options = true;
                }

                $products_with_serials  = array();
                $cashu_data['products'] = '';

                foreach($order_products as $product)
                {
                    if($product->product_id != 0)
                    {
                        $serials_array  = array();
                        $orders_serials = $this->orders_model->get_product_serials($product->product_id, $product->order_id);

                        foreach($orders_serials as $serial)
                        {
                            $secret_key  = $this->config->item('new_encryption_key');
                            $secret_iv   = md5('serial_iv');
                            //$secret_iv   = md5($serial->unix_time);
                            $dec_serial  = $this->encryption->decrypt($serial->serial, $secret_key, $secret_iv);

                            $serial->{'dec_serial'}  = $dec_serial;
                            $serials_array[] = $serial;
                        }

                        $product->{'serials'}    = $serials_array;
                        $products_with_serials[] = $product;

                        $cashu_data['products'] .= $product->title.' - ';
                    }
                }


                if($order_details->order_status_id == 1 && $order_details->send_later == 0)
                {
                    $show_serials = true;
                }
                else
                {
                    $show_serials = false;
                }


                $this->data['order_details']         = $order_details;
                $this->data['payment_method']        = $payment_method;
                $this->data['products_with_serials'] = $products_with_serials;
                $this->data['wholesaler_options']    = $wholesaler_options;
                $this->data['order_log']             = $log_array;
                $this->data['show_serial']           = $show_serials;
                $this->data['charge_card']           = $charge_card;
                $this->data['cards_data']            = $cards_array;
                $this->data['go_to_payment']         = $go_to_payment;
                $this->data['pay_by_paypal']         = $pay_by_paypal;
                $this->data['pay_by_cashu']          = $pay_by_cashu;
                $this->data['cashu_data']            = $cashu_data;
                $this->data['user_id']               = $user_id;

                $this->data['content']               = $this->load->view('order_details', $this->data, true);
                $this->load->view('site/inner_main_frame',$this->data);
            }
            else
            {
                redirect('users/users/user_login', 'refresh');
            }
        }
        else
        {
            $this->user_bootstrap->set_back_redirection_url(current_url());
            redirect('users/users/user_login', 'refresh');
        }
    }

    public function user_orders($page_id =1)
    {
        $lang_id = $this->data['lang_id'];

        if($this->ion_auth->logged_in())
        {
            $perPage = 10;
            $offset  = ($page_id -1 ) * $perPage;

            $user_id          = $this->user_bootstrap->get_user_id();
            $display_lang_id  = $this->user_bootstrap->get_active_language_row()->id;
            $users_order_data = $this->orders_model->get_user_order_data($user_id, $display_lang_id, $perPage, $offset);

            $orders_array = array();

            foreach($users_order_data as $order)
            {
                $order_products = $this->orders_model->get_order_products($order->id, $display_lang_id);

                foreach($order_products as $product_row)
                {
                    if($product_row->product_id !=0)
                    {
                        $product_row->{'title'} = $product_row->title.'---'.$product_row->qty;
                    }
                    else
                    {
                        $product_row->{'title'} = $product_row->price.' '.lang('recharge_card');
                    }

                    $order_products_array[$order->id][] = $product_row;
                }

                $label = '';

                if($order->order_status_id == 1)
                {
                    $label = 'success';
                }
                else if($order->order_status_id == 2)
                {
                    $label = 'warning';
                }
                else if($order->order_status_id == 3)
                {
                    $label = 'danger';
                }

                $order->{'label'} = $label;

                $orders_array[]   = $order;
            }

            $config['base_url']    = base_url()."orders/order/user_orders/";
            $config['per_page']    = $perPage;
            $config['first_link']  = TRUE;
            $config['last_link']   = TRUE;
            $config['uri_segment'] = 3;
            $config['use_page_numbers'] = TRUE;

            $config['total_rows']  = $this->orders_model->get_all_orders_count($lang_id, $user_id);

            $this->pagination->initialize($config);

            $this->data['orders_data'] = $orders_array;
            $this->data['pagination']  = $this->pagination->create_links();
            $this->data['order_products'] = $order_products_array;

            $this->data['content']     = $this->load->view('users_order_log', $this->data, true);
            $this->load->view('site/inner_main_frame',$this->data);
        }
        else
        {
            $this->session->set_userdata('redir', current_url());

			redirect('users/users/user_login', 'refresh');
        }
    }

    public function filter_order_log($page_id =1)
    {
        $lang_id = $this->data['lang_id'];
        $perPage = 100;
        $offset  = ($page_id -1 ) * $perPage;

        if($offset < 0)
        {
            $offset = $perPage;
        }

        $order_number  = intval($this->input->post('order_number'));
        $order_date    = $this->input->post('order_date');
        $product_title = trim($this->input->post('product_title'));
        $final_total   = $this->input->post('final_total');
        $status        = $this->input->post('status');
        $start_date    = strtotime($order_date);
        //$end_date    = strToTime($order_date.' 23:59:59' );
        $end_date      = $start_date + 86399;

        $user_id          = $this->user_bootstrap->get_user_id();
        $display_lang_id  = $this->user_bootstrap->get_active_language_row()->id;

        $users_order_data = $this->orders_model->get_user_order_data($user_id, $display_lang_id, $perPage, $offset, $order_number, $start_date, $end_date, $final_total, $status);

        $orders_array     = array();

        if(count($users_order_data) != 0)
        {
            foreach($users_order_data as $order)
            {
                $order_products         = $this->orders_model->get_order_products($order->id, $display_lang_id, $product_title);
                $order_charge_cards     = $this->orders_model->get_recharge_card($order->id);
                $order->{'products'}    = $order_products;
                $order->{'charge_card'} = $order_charge_cards;

                if($order->order_status_id == 1)
                {
                    $label = 'success';
                }
                else if($order->order_status_id == 2)
                {
                    $label = 'warning';
                }
                else if($order->order_status_id == 3)
                {
                    $label = 'danger';
                }

                $order->{'label'} = $label;

                $orders_array[]   = $order;
            }
        }

        $config['base_url']    = base_url()."orders/order/filter_order_log/";
        $config['per_page']    = $perPage;
        $config['first_link']  = FALSE;
        $config['last_link']   = FALSE;
        $config['uri_segment'] = 4;
        $config['use_page_numbers'] = TRUE;

        $config['total_rows']  = $this->orders_model->get_all_orders_count($lang_id, $user_id, $order_number, $start_date, $end_date, $final_total, $status);

        $this->pagination->initialize($config);

        $this->data['orders_data'] = $orders_array;
        $this->data['pagination']  = $this->pagination->create_links();

        $this->load->view('orders/orders_log_ajax_view', $this->data);

        //echo json_encode(array($output, $pagination));
    }

/************************************************************************/
}
