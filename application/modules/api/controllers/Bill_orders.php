<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Bill_orders extends CI_Controller
{
    public $status=1;
    public function __construct()
    {
        parent::__construct();

        //$this->load->model('follow_orders_model');
        $this->load->model('general_model');
        $this->load->model('orders/orders_model');
        $this->load->model('orders/order_status_model');
        $this->load->model('products/products_model');
        $this->load->model('payment_options/payment_methods_model');
        $this->load->model('payment_options/user_balance_model');
        $this->load->model('shopping_cart/user_bank_accounts_model');
        $this->load->model('payment_options/bank_accounts_model');

        $this->load->library('api_lib');
        $this->load->library('encryption');
        $this->load->library('orders/orders');


        $this->config->load('encryption_keys');

    }

    public function index()
    {
        $lang_id              = intval($this->input->post('langId', TRUE));
        //$userId               = intval($this->input->post('userId', TRUE));
        $email                = strip_tags($this->input->post('email', TRUE));
        $name                 = strip_tags($this->input->post('name', TRUE));
        $password             = strip_tags($this->input->post('password', TRUE));
        $agent                = strip_tags($this->input->post('agent', TRUE));
        $country_id           = intval($this->input->post('countryId', TRUE));

        $payment_method_id    = intval($this->input->post('paymentMethodId', TRUE));
        $amount               = intval($this->input->post('amount', TRUE));
        $main_order_id        = intval($this->input->post('orderId', TRUE));

        $ip_address           = $this->input->ip_address();

        $user_id              = 0;
        
        if($this->ion_auth->login($email, $password) || (isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0 ))
        {
            $user_data  = $this->ion_auth->user()->row();
            $user_id    = $user_data->id;
            $main_order = $this->orders_model->get_order($main_order_id);

            if($amount > $main_order->rest_amount || $main_order->rest_amount == 0)
            {
                $amount_error_lang = $this->general_model->get_lang_var_translation('not_enough_pocket_money', $lang_id);
                $output = array(
                                    'response' => 0,
                                    'message'  => $amount_error_lang
                                );
            }
            else
            {

                $bank_id            = 0;
                $first_order_status = 0;

                $msg                      = '';
                $form                     = '';
                $order_bank_name          = '';
                $order_bank_number        = '';
                $first_order_error_msg    = '';
                $voucher                  = '';
                $user_bank_account_name   = '';
                $user_bank_account_number = '';


                $is_wholesaler       = false;
                $order_data          = array();
                $output              = array();
                $country_check_array = array();
                $stock_check_array   = array();


                $settings                     = $this->general_model->get_settings();//$this->user_bootstrap->get_settings();
                $wholesaler_customer_group_id = $settings->wholesaler_customer_group_id;


                $user_customer_group = $this->ion_auth->user()->row()->customer_group_id;

                if($user_customer_group == $wholesaler_customer_group_id)
                {
                    $is_wholesaler  = true;
                }


                $start_time = time();
                $end_time   = $start_time - (60 * 60 * 24);

                $user_orders_count        = $this->orders_model->count_user_orders_per_day($start_time, $end_time, $user_id);
                $user_customer_group_data = $this->customer_groups_model->get_user_customer_group_data($user_id);


                $secret_key = $this->config->item('new_encryption_key');
                $secret_iv  = $user_id;

                $method_data      = $this->order_status_model->get_payment_data($payment_method_id);

                $order_status_id  = $method_data->order_status_id;

                $insert_order = true;

                //pocket
                if($payment_method_id == 1)
                {
                    $user_enc_old_balance = $user_data->user_balance;
                    $user_old_balance     = $this->encryption->decrypt($user_enc_old_balance, $secret_key, $secret_iv);

                    if($user_old_balance >= $amount)
                    {
                        $user_new_balance                  = $user_old_balance - $amount;
                        $user_enc_new_balance              = $this->encryption->encrypt($user_new_balance, $secret_key, $secret_iv);
                        $user_balance_data['user_balance'] = $user_enc_new_balance;

                        $this->user_model->update_user_balance($user_id, $user_balance_data);

                        $log_data = array(
                                            'user_id'           => $user_id,
                                            'payment_method_id' => $payment_method_id,
                                            'amount'            => $amount,
                                            'balance'           => $user_new_balance ,
                                            'balance_status_id' => 1,  //withdraw from balance
                                            'ip_address'        => $this->input->ip_address(),
                                            'unix_time'         => time()
                                         );

                        $this->user_balance_model->insert_balance_log($log_data);
                    }
                    else
                    {
                        $output = array(
                                         'message'  => $this->general_model->get_lang_var_translation('not_enough_pocket_money', $lang_id),
                                         'response' => 0
                                        );

                        $insert_order = false;


                    }

                    $template_payment_method = $this->general_model->get_lang_var_translation('pocket_money', $lang_id);
                }
                elseif($payment_method_id == 2) //reward points
                {
                    $user_reward_points_value = $this->api_lib->get_user_reward_points_value($user_id, $country_id);

                    if($user_reward_points_value >= $amount)
                    {
                        $user_new_reward_points = $user_reward_points_value - $amount;
                        $this->user_bootstrap->encrypt_and_update_users_data($user_id, 'user_points', $user_new_reward_points);

                    }
                    else
                    {
                        $output = array(
                                         'message'  => $this->general_model->get_lang_var_translation('error_in_reward_points', $lang_id),
                                         'response' => 0
                                        );
                    }

                    $template_payment_method = $this->general_model->get_lang_var_translation('reward_points', $lang_id);



                }
                elseif($payment_method_id == 7) //voucher
                {
                    $voucher                 = strip_tags($this->input->post('voucherNumber', TRUE));
                    $voucher_lang_var        = $this->general_model->get_lang_var_translation('voucher', $lang_id);
                    $template_payment_method = $voucher_lang_var;
                }
                elseif($payment_method_id == 3) //Banks
                {
                    $bank_id = intval($this->input->post('bankId', TRUE));

                    $method_data      = $this->order_status_model->get_payment_data($payment_method_id);
                    $order_status_id  = $method_data->order_status_id;

                    // insert user bank account data
                    $user_bank_account_name   = $this->input->post('userAccountName', TRUE);
                    $user_bank_account_number = $this->input->post('userAccountNumber', TRUE);

                    $this->user_bank_accounts_model->delete_bank_account($bank_id, $user_id);

                    $data    = array(
                                        'user_id'        => $user_id                            ,
                                        'bank_id'        => $bank_id                            ,
                                        'account_name'   => strip_tags($user_bank_account_name) ,
                                        'account_number' => strip_tags($user_bank_account_number)
                                    );

                    $this->user_bank_accounts_model->insert_user_account_data($data);


                    $bank_data          = $this->bank_accounts_model->get_bank_data($bank_id, $lang_id);
                    $user_bank_data     = $this->user_bank_accounts_model->get_user_bank_data($bank_id, $user_id);

                    $order_bank_name    = $user_bank_data->account_name;
                    $order_bank_number  = $user_bank_data->account_number;

                    $bank_lang_var              = $this->general_model->get_lang_var_translation('bank_name',$lang_id);
                    $bank_account_name_lang_var = $this->general_model->get_lang_var_translation('bank_account_name',$lang_id);
                    $bank_account_num_lang_var  = $this->general_model->get_lang_var_translation('bank_account_number',$lang_id);

                    $template_payment_method = $bank_lang_var." : ".$bank_data->bank."<br>".$bank_account_name_lang_var." : ".$bank_data->account_name."<br>".$bank_account_num_lang_var." : ".$bank_data->account_number;
                }
                else
                {
                    $template_payment_method = $this->payment_methods_model->get_payment_method_name($payment_method_id, $lang_id);
                    if($payment_method_id != 10)
                    {
                        $order_status_id = 8;
                    }
                }

                if($insert_order)
                {
                    $currency_symbol = $main_order->currency_symbol;

                    $username      = $user_data->first_name . ' ' . $user_data->last_name;
                    $user_email    = $user_data->email;
                    $emails[]      = $user_data->email;
                    $mobile_number = $user_data->phone;

                    //insert order data
                    $order_data      = array(
                                                'user_id'             => $user_id               ,
                                                'main_order_id'       => $main_order_id         ,
                                                'is_pay_later_bill'   => 1                      ,
                                                'store_id'            => $main_order->store_id  ,
                                                'agent'               => $agent                 ,
                                                'payment_method_id'   => $payment_method_id     ,
                                                'bank_id'             => $bank_id               ,
                                                'bank_account_name'   => $order_bank_name       ,
                                                'bank_account_number' => $order_bank_number     ,
                                                'voucher'             => $voucher               ,
                                                'order_status_id'     => $order_status_id       ,
                                                'country_id'          => $country_id            ,
                                                'currency_symbol'     => $currency_symbol       ,

                                                'items_count'         => 1                      ,
                                                'total'               => $amount                ,
                                                'discount'            => 0                      ,
                                                'coupon_discount'     => 0                      ,
                                                'tax'                 => 0                      ,
                                                'final_total'         => $amount                ,
                                                'wrapping_cost'       => 0                      ,
                                                'shipping_cost'       => 0                      ,
                                                'vat_value'           => 0                      ,
                                                'vat_percent'         => 0                      ,

                                                'auto_cancel'         => 1                      ,
                                                'notes'               => ''                     ,
                                                'orders_number'       => $main_order->orders_number,
                                                'unix_time'           => time()                 ,
                                                'day'                 => date('d')              ,
                                                'month'               => date('m')              ,
                                                'year'                => date('Y')
                                              );

                        if(!$this->orders_model->insert_order($order_data))
                        {
                            $this->status = 0;

                            $output = array(
                                               'response' => 1,
                                               'message'  => $insertion_fail_message
                                           );
                        }
                        else
                        {
                            $new_order_id   = $this->db->insert_id();

                            //insert order products

                            $this->_insert_bill_products($new_order_id, $amount, $main_order->store_id);

                            if($this->status == 1)
                            {
                                $user_points = $this->encryption->decrypt($user_data->user_points, $secret_key, $secret_iv);

                                /// insert order , create notification
                                $this->_insert_order_log($new_order_id, $order_status_id, $payment_method_id, $user_data, $main_order_id, $amount);

                                if($this->status == 1)
                                {
                                    //send notification
                                    $status = $this->order_status_model->get_status_translation_name($order_status_id, $lang_id);

                                    if($order_status_id != 1)
                                    {
                                        //send notification
                                        $email_msg      = '';
                                        $sms_msg        = '';


                                        $template_data = array(
                                                                'unix_time'             => time()                           ,
                                                                'username'              => $username                        ,
                                                                'user_email'            => $user_email                      ,
                                                                'user_phone'            => $mobile_number                   ,
                                                                'status'                => lang('completed')                ,
                                                                'order_id'              => $new_order_id                        ,
                                                                'main_order_id'         => $main_order_id                   ,
                                                                'amount'                => $amount                          ,
                                                                'logo_path'             => base_url().'assets/uploads/'.$settings->logo             ,
                                                                'order_time'            => date('Y/m/d H:i', time())                ,
                                                                'user_order_link'       => base_url()."orders/order/view_order_details/".$new_order_id  ,
                                                                'payment_method'        => $template_payment_method         ,
                                                                'status'                => $status
                                                              );

                                        $this->notifications->create_notification('add_bill', $template_data, $emails, $mobile_number);
                                    }


                                   /*************--------------------------------------**********/


                                }
                                else
                                {
                                    $output = array(
                                                       'response' => 0,
                                                       'message'  => $insertion_fail_message
                                                   );
                                }
                            }
                            else
                            {
                                $output = array(
                                                   'response' => 0,
                                                   'message'  => $insertion_fail_message
                                               );
                            }
                        }


                    if($this->status == 1)
                    {

                        $final_total = $amount;

                        $url = '';

                        if($payment_method_id == 4 || $payment_method_id == 5 || $payment_method_id == 6 || $payment_method_id == 8  || $payment_method_id ==13 )
                        {
                            $products_names = lang('pay_later_bill');
                            $url = $this->_generate_payment_form($payment_method_id, $order_id, $amount, $main_order->currency_symbol, $products_names, $lang_id, $user_id);
                        }

                        $success_message = $this->general_model->get_lang_var_translation('order_inserted_successfully', $lang_id);

                        $output = array(
                                           'response'   => 1,
                                           'message'    => $success_message,
                                           'paymentURL' => $url,
                                           'orderId'    => $new_order_id
                                       );
                    }
                    ///////////////////////////////////////////////////////////////////

                }
            }
        }
        else
        {
            $login_error_message = $this->general_model->get_lang_var_translation('login_error', $lang_id);

            $output = array(
                               'response' => 0,
                               'message'  => $login_error_message
                           );
        }


        //***************LOG DATA***************//
        //insert log
        $this->api_lib->insert_log($user_id, current_url(), 'Bill Orders', $agent, $_POST, $output);
        //***************END LOG***************//

        $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));

    }

    private function _generate_payment_form($payment_method_id, $order_id, $products_names, $lang_id, $user_id)
    {

        $url = '';
        if($payment_method_id == 4 || $payment_method_id == 8)   //payfort OR Sadad
        {
            if($user_id != 0)
            {
                $user_data      = $this->user_model->get_row_data($user_id);
                $customer_name  = $user_data->first_name.' '.$user_data->last_name;
            }
            else
            {
                $customer_name = 'guest';
            }

            if($payment_method_id == 8)
            {
                $payment_option = 'SADAD';
            }
            else
            {
                $payment_option = null;
            }

            $products_names = preg_replace("/[^A-Za-z0-9]/", '', $products_names);

            //Product name max length is 35

            $url = base_url().'api/create_order/payfort_form/'.$order_id.'/'.$lang_id.'/'.$user_id;

        }
        elseif($payment_method_id == 5)   // PayPal
        {
            $url = base_url().'orders/payment_gateways/process_paypal/'.$order_id;
        }
        elseif($payment_method_id == 6)    //CashU
        {
            $url = base_url().'api/create_order/cashu_form/'.$order_id.'/'.$lang_id.'/'.$user_id;
        }
        elseif($payment_method_id == 13)  //Hyperpay
        {
            $url = base_url().'orders/Payment_gateways/process_hyperpay/'.$order_id.'/hyperpay_visa';
        }
        else
        {
            $url = '';//base_url().'orders/order/view_order_details/'.$order_id;
        }

        return $url;
    }

    private function _insert_bill_products($order_id, $amount, $store_id)
    {
        $order_product_data = array(
                                       'order_id'           => $order_id        ,
                                       'store_id'           => $store_id             ,
                                       'type'               => 'pay_later_bill' ,
                                       'product_id'         => null             ,
                                       'cat_id'             => null             ,
                                       'purchased_cost'     => 0                ,
                                       'qty'                => 1                ,
                                       'price'              => $amount          ,
                                       'final_price'        => $amount          ,
                                       'discount'           => 0                ,
                                       'coupon_discount'    => 0                ,
                                       'vat_value'          => 0                ,
                                       'vat_percent'        => 0                ,
                                       'reward_points'      => 0                ,
                                       'unix_time'          => time()
                                   );

        if(!$this->orders_model->insert_order_products($order_product_data))
        {
            $this->status = 0;
        }


    }

    /// insert order , create notification
    private function _insert_order_log($order_id, $order_status_id, $payment_method_id, $user_data, $main_order_id, $amount)
    {
        $order_data     = $this->orders_model->get_order($order_id);
        $user_id        = $order_data->user_id;

        if($order_status_id == 1)
        {
            $this->orders->add_order_bill($order_id, $amount, '', $order_data);

            $emails[]      = $user_data->email;
            $mobile_number = $user_data->phone;

            if($user_data->username != '')
            {
                $username       = $user_data->username;
                $user_email     = $user_data->email;
                $emails[]       = $user_data->email;
                $mobile_number  = $user_data->phone;
            }
            else
            {

                $username = $this->general_model->get_lang_var_translation('visitor', $lang_id);
                /*$emails[]      = $email;
                $user_email    = $email;
                $mobile_number = $phone;
                */
            }

            $template_data = array(
                                    'unix_time'             => time()                           ,
                                    'username'              => $username                        ,
                                    'user_email'            => $user_email                      ,
                                    'user_phone'            => $mobile_number                   ,
                                    'status'                => lang('completed')                ,
                                    'order_id'              => $order_id                        ,
                                    'main_order_id'         => $main_order_id                   ,
                                    'amount'                => $amount                          ,
                                    'logo_path'             => base_url().'assets/uploads/'.$settings->logo             ,
                                    'order_time'            => date('Y/m/d H:i', $order_data->unix_time)                ,
                                    'user_order_link'       => base_url()."orders/order/view_order_details/".$order_id  ,
                                    'payment_method'        => $template_payment_method
                                  );

            $this->notifications->create_notification('add_bill', $template_data, $emails, $mobile_number);


        }

        else
        {
            $order_status_data = $this->order_status_model->get_payment_data($payment_method_id);
            $order_status_id = $order_status_data->order_status_id;
        }


        $log_data = array(
                            'order_id'  => $order_id,
                            'status_id' => $order_status_id,
                            'unix_time' => time()
                         );

        $this->orders_model->insert_order_log($log_data);

    }





/************************************************************************/
}
