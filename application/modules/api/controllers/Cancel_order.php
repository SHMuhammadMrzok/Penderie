<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Cancel_order extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('general_model');
        $this->load->model('users/user_model');
        $this->load->model('users/countries_model');
        $this->load->model('orders/orders_model');
        $this->load->model('products/products_serials_model');
        $this->load->model('payment_options/user_balance_model');

        $this->load->library('notifications');

    }

    public function index()
    {

        //$settings         = $this->global_model->get_config();
        $email            = strip_tags($this->input->post('email', TRUE));
        $password         = strip_tags($this->input->post('password', TRUE));
        $lang_id          = intval($this->input->post('langId', TRUE));
        $order_id         = intval($this->input->post('orderId', TRUE));
        $country_id       = intval($this->input->post('countryId', TRUE));
        $device_id        = strip_tags($this->input->post('deviceId', TRUE));


        if($this->ion_auth->login($email, $password))
        {

            $user_data  = $this->ion_auth->user()->row();
            $order_data = $this->orders_model->get_order($order_id);

            if($user_data->id == $order_data->user_id)
            {
                if($order_data->order_status_id == 2)
                {
                    $status_id    = 11;
                    $status_note  = 'canceled by user';
                    $updated_data = array(
                                            'order_status_id'   => $status_id   ,
                                            'status_note'       => $status_note
                                         );

                    $this->orders_model->update_order_status($order_data->id, $updated_data);


                    $order_products = $this->orders_model->get_order_all_products($order_id);
                    $this->_canceled_orders_operations($order_id, $country_id, $status_id, $order_products);

                    $message = $this->general_model->get_lang_var_translation('success', $lang_id);

                    $output = array(
                                      'message'  => strip_tags($message) ,
                                      'response' => 1
                                   );
                }
                else
                {
                    $message = $this->general_model->get_lang_var_translation('not_allowed_to_cancel_order', $lang_id);

                    $output = array(
                                      'message'  => strip_tags($message) ,
                                      'response' => 0
                                    );
                }
            }
            else
            {
                $message = $this->general_model->get_lang_var_translation('no_data', $lang_id);

                $output = array(
                                  'message'  => strip_tags($message) ,
                                  'response' => 0
                               );
            }

       }
       else
       {
            $message = $this->general_model->get_lang_var_translation('login_error', $lang_id);

            $output = array(
                              'message'  => strip_tags($message) ,
                              'response' => 0
                           );


       }

       $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));
    }


    private function _canceled_orders_operations($order_id, $country_id, $status_id, $order_products)
     {
        $order_data = $this->orders_model->get_order($order_id);

        //if order payment method = balance ... return user balance
        if($order_data->payment_method_id == 1)
        {
            $user_old_balance = $this->get_user_balance($order_data->user_id);
            $user_new_balance = $user_old_balance + $order_data->final_total;

            $this->encrypt_and_update_users_data($order_data->user_id, 'user_balance', $user_new_balance);

            $balance_log_data = array(
                                        'user_id'           => $order_data->user_id             ,
                                        'order_id'          => $order_id                        ,
                                        'payment_method_id' => $order_data->payment_method_id   ,
                                        'balance'           => $user_new_balance                ,
                                        'amount'            => $order_data->final_total         ,
                                        'currency_symbol'   => $order_data->currency_symbol     ,
                                        'store_country_id'  => $order_data->country_id          ,
                                        'balance_status_id' => 7 , // order cancelled
                                        'ip_address'        => $this->input->ip_address()       ,
                                        'unix_time'         => time()
                                     );

            $this->user_balance_model->insert_balance_log($balance_log_data);

        }
        else if($order_data->payment_method_id = 2)  //if order payment method = reward points ... return user reward points
        {
            $order_points = $this->convert_into_reward_points($order_data->country_id, $order_data->final_total);

            $user_old_points = $this->get_user_reward_points($order_data->user_id);
            $user_new_points = $order_points + $user_old_points;

            $this->encrypt_and_update_users_data($order_data->user_id, 'user_points', $user_new_points);
        }

        //update product_serials value
        $serials_data = $this->orders_model->get_order_serials($order_id);

        if(count($serials_data) != 0)
        {
            foreach($serials_data as $serial)
            {
                $serial_data['serial_status'] = 0;
                $serial_data['sold_order_id'] = 0;
                $this->products_serials_model->update_serial($serial->product_serial_id, $serial_data);

                foreach($order_products as $product)
                {
                    $product_qty = $this->products_model->count_product_available_quantity($product->product_id, $country_id);
                    $product_updated_data['product_quantity'] = $product_qty;

                    $this->products_model->update_product_countries($product->product_id, $country_id, $product_updated_data);
                }
            }
        }

        $this->_insert_order_log($order_id, $status_id);

        //  send_notification

        $user_data     = $this->general_model->get_user_row($order_data->user_id);
        $emails[]      = $user_data->email;
        $mobile_number = $user_data->phone;
        $template_data = array(
                                'unix_time'    => time(),
                                'username'     => $user_data->username,
                                'order_id'     => $order_id,
                                'logo_path'    => base_url().'assets/template/site/img/logo.png',
                                'order_time'   => date('Y/m/d H:i', $order_data->unix_time),
                                'year'         => date('Y')
                              );

        $this->notifications->create_notification('cancel_order', $template_data, $emails, $mobile_number);
     }

     private function _insert_order_log($order_id, $status_id)
     {
        $log_data = array(
                            'order_id'  => $order_id  ,
                            'status_id' => $status_id ,
                            'unix_time' => time()
                         );

        $this->orders_model->insert_order_log($log_data);
     }
      public function get_user_balance($user_id)
    {
        $this->load->library('encryption');

        $secret_iv        = $user_id;
        $secret_key       = $this->config->item('new_encryption_key');
        $enc_user_balance = $this->general_model->get_user_row($user_id)->user_balance;

        $user_balance     = $this->encryption->decrypt($enc_user_balance, $secret_key, $secret_iv);

        if($enc_user_balance == '')
        {
            $user_balance = 0;
        }

        return $user_balance;
    }

     public function encrypt_and_update_users_data($user_id, $field, $data)
   {
        $secret_key    = $this->config->item('new_encryption_key');
        $secret_iv     = $user_id;

        $user_enc_data     = $this->encryption->encrypt($data, $secret_key, $secret_iv);
        $user_data[$field] = $user_enc_data;

        return $this->user_model->update_user_balance($user_id, $user_data);

   }

    public function convert_into_reward_points($country_id, $rewrd_points_value)
   {
        $country_reward_point_value = $this->countries_model->get_reward_points($country_id);

        $user_reward_points         = round($rewrd_points_value / $country_reward_point_value, 2);

        return $user_reward_points;
   }

   public function get_user_reward_points($user_id)
    {
        $this->load->library('encryption');

        $secret_iv         = $user_id;
        $secret_key        = $this->config->item('new_encryption_key');
        $enc_reward_points = $this->general_model->get_user_row($user_id)->user_points;

        $reward_points     = $this->encryption->decrypt($enc_reward_points, $secret_key, $secret_iv);

        if($enc_reward_points == '')
        {
            $reward_points = 0;
        }

        return $reward_points;
    }



/************************************************************************/
}
