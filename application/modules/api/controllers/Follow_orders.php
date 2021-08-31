<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Follow_orders extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('follow_orders_model');
        $this->load->model('general_model');
        $this->load->model('orders/orders_model');

        $this->load->library('api_lib');
        $this->load->library('pagination');
    }

    public function index($page =1)
    {

        $userId                   = intval(strip_tags($this->input->post('userId', TRUE)));
        $email                    = strip_tags($this->input->post('email', TRUE));
        $password                 = strip_tags($this->input->post('password', TRUE));

        $country_id               = intval(strip_tags($this->input->post('countryId', TRUE)));
        $lang_id                  = intval(strip_tags($this->input->post('langId', TRUE)));

        $page                     = intval(strip_tags($this->input->post('page', TRUE)));
        $searchOrderNumbery       = strip_tags($this->input->post('searchOrderNumber', TRUE));
        $searchOrderstatus        = strip_tags($this->input->post('searchOrderstatus', TRUE));
        $deviceId                 = strip_tags($this->input->post('deviceId', TRUE));

        $output = array();



        if($this->ion_auth->login($email, $password))
        {

            $user    = $this->ion_auth->user()->row();
            $user_id = $user->id;
            $userId  = $user_id;

            $this->api_lib->check_user_store_country_id($email, $password, $user_id, $country_id);

            if(!$page) $page = 1;
            $limit = 10;
            $offset  = ($page -1 ) * $limit;

           /* if($offset < 0)
            {
                $offset = $limit;
            }*/

            $users_order_data   = $this->orders_model->get_user_order_data($user_id, $lang_id, $limit, $offset, $searchOrderNumbery, '', '', '', '', 0, 0, $searchOrderstatus);
                                                                            
            if(count($users_order_data) != 0) //&&!empty($users_order_data)
            {
                $config['base_url']    = base_url()."orders/order/user_orders/";
                $config['per_page']    = $limit;
                $config['first_link']  = FALSE;
                $config['last_link']   = FALSE;
                $config['uri_segment'] = 4;
                $config['use_page_numbers'] = TRUE;

                $config['total_rows']  = $this->follow_orders_model->get_all_orders_count($userId,$searchOrderNumbery,$searchOrderstatus);

                $this->pagination->initialize($config);
                $this->pagination->create_links();
                //$this->data['orders_data'] = $orders_array;
                foreach($users_order_data as $order)
                {
                    $output [] = array(
                                        'orderNumber'            => $order->id                      ,
                                        'orderTotal'             => $order->total                   ,
                                        'orderFinalTotal'        => $order->final_total             ,
                                        'orderCreateDate'        => date('Y-m-d',$order->unix_time) ,
                                        'orderStatus'            => $order->status                  ,
                                        'storeName'              => $order->store_name              ,
                                        //'orderStatusImage'       => base_url().'assets/template/site/img/'.$order->status_image
                                        );

                }

            }
            else
            {
                $fail_message = $this->general_model->get_lang_var_translation('no_orders_found',$lang_id);
                $output  = array(
                                    'message' => $fail_message,
                                    'response' => 0
                                );
                //$output  = array( 'message' => '0');
            }//if data

        }
        else
        {
            $fail_message   = $this->general_model->get_lang_var_translation('login_error',$lang_id);
            $output         = array(
                                    'message' => $fail_message,
                                    'response' => 0
                                );
            //$output  = array( 'message' => '0');
        }//if login

        $this->output->set_content_type('application/json')->set_output(json_encode($output));

    }


/************************************************************************/
}
