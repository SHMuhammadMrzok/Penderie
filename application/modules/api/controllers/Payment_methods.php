<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Payment_methods extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->library('api_lib');
        $this->load->library('encryption');
        $this->load->library('shopping_cart');

        $this->load->model('general_model');
        $this->load->model('users/countries_model');
        $this->load->model('payment_options/payment_methods_model');
        $this->load->model('shopping_cart/user_bank_accounts_model');

        //require(APPPATH . 'includes/front_end_global.php');

    }

    public function index()
    {

        $userId         = intval($this->input->post('userId', TRUE));
        $deviceId       = strip_tags($this->input->post('deviceId', TRUE));
        $ip_address     = $this->input->ip_address();
        $country_id     = intval($this->input->post('countryId', TRUE));
        $lang_id        = intval($this->input->post('langId', TRUE));

        $email          = strip_tags($this->input->post('email', TRUE));
        $password       = strip_tags($this->input->post('password', TRUE));
        $pay_later      = intval($this->input->post('pay_later_bills', TRUE));

        $agent              = strip_tags($this->input->post('agent', TRUE));
        $user_id            = 0;

        $this->shopping_cart->set_user_data($userId, $deviceId, $ip_address , $country_id ,$lang_id);

        if($this->ion_auth->login($email, $password))
        {

            $user = $this->ion_auth->user()->row();
            $this->api_lib->check_user_store_country_id($email, $password, $user->id, $country_id);

            $customer_group_id = $user->customer_group_id;
            $payment_methods   = $this->get_payment_methods($user->id, $lang_id, $country_id, $pay_later);

            $output = array();

            if(isset($payment_methods) && count($payment_methods) != 0)
            {
                 $output = $payment_methods;
            }
            else
            {
               $output = array(
                                 'response' => 0,
                                 'message'  => $this->general_model->get_lang_var_translation('no_available_options', $lang_id)

                              );
            }

        }
        else
        {

            $payment_methods  = $this->get_payment_methods($userId, $lang_id, $country_id);
            $output           = array();

            if(isset($payment_methods) && count($payment_methods) != 0)
            {
                $output = $payment_methods;
            }
            else
            {
               $output = array(
                                 'response' => 0,
                                 'message'  => $this->general_model->get_lang_var_translation('no_available_options', $lang_id)

                              );
            }
        }

        
        //***************LOG DATA***************//
        //insert log
        $this->api_lib->insert_log($user_id, current_url(), 'Payment methods', $agent, $_POST, $output);
        //***************END LOG***************//

        $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));
    }

    public function get_payment_methods($user_id, $lang_id, $country_id, $pay_later=0)
    {
        $charge_card_in_cart = $this->shopping_cart->count_charge_cards_in_cart();
        $final_total         = $this->shopping_cart->final_total();
        $cart_data           = $this->shopping_cart->shopping_cart_data();
        $contents            = $this->shopping_cart->contents();
        $images_path = $this->api_lib->get_images_path();

        $secret_key           = $this->config->item('new_encryption_key');
        $secret_iv            = $user_id;

        $wholesaler_pocket    = 0;
        $user_customer_group  = 0;
        $use_pocket           = 0;

        $pay_by_bank          = true;
        $is_wholesaler        = false;
        $pay_by_pocket        = false;
        $pay_by_reward_points = false;

        $not_included_ids     = array();
        $bank_accounts_array  = array();

        $settings                     = $this->global_model->get_config();
        $wholesaler_customer_group_id = $settings->wholesaler_customer_group_id;

        $user_data = $this->ion_auth->user()->row();//$this->user_bootstrap->get_user_data();

        if($user_data)
        {
            $user_customer_group = $user_data->customer_group_id;

            if($user_customer_group == $wholesaler_customer_group_id)
            {
                $is_wholesaler  = true;
            }
        }

        if($charge_card_in_cart == 0 )
        {
           // check if logged in to use pocket pay
           if($this->ion_auth->logged_in())
           {
               $enc_user_balance = $this->ion_auth->user()->row()->user_balance;
               $user_balance     = $this->encryption->decrypt($enc_user_balance, $secret_key, $secret_iv);


               if($cart_data->final_total_price_with_tax <= $user_balance)
               {
                    $pay_by_pocket = true;
                    //$pay_by_bank   = false;
                    $use_pocket    = 1;
               }

               /*if($is_wholesaler && $final_total <= $user_balance)
               {
                    //$wholesaler_pocket = 1;
               }
               */
           }
        }

        // check reward points
        if($this->ion_auth->logged_in())
        {
            $enc_user_points   = $this->ion_auth->user()->row()->user_points;
            $user_points       = $this->encryption->decrypt($enc_user_points, $secret_key, $secret_iv);
            $point_value       = $this->countries_model->get_reward_points($country_id);
            $user_points_value = $user_points * $point_value;

            if($final_total <= $user_points_value)
            {
                $pay_by_reward_points = true;
            }
        }

        if(!$pay_by_pocket)
        {
            $not_included_ids[] = 1;
        }

        if(!$pay_by_reward_points)
        {
            $not_included_ids[] = 2;
        }

        if(!$pay_by_bank)
        {
            $not_included_ids[] = 3;
        }

        // if visitor remove voucher payment method
        if(! $this->ion_auth->logged_in())
        {
            $not_included_ids[] = 7;
        }

        if($pay_later == 1)
        {
          $not_included_ids[] = 14;
        }

        $payment_options = $this->payment_methods_model->get_available_payment_options($lang_id, $user_customer_group, $not_included_ids, $use_pocket);
        $output = array();

        foreach($payment_options as $option)
        {
            if($option->id == 2)
            {
              $reward_point_value_lang = $this->general_model->get_lang_var_translation('reward_point_value', $lang_id);
              $method_text = $reward_point_value_lang.' : '.$user_points_value.' '.$option->description;
            }
            else {
              $method_text = $option->description;
            }

            $output[] = array(
                                'methodId'           => $option->id                                 ,
                                'methodName'         => $option->name                               ,
                                'methodImage'        => $images_path .$option->image ,
                                'methodExtraFees'    => $option->extra_fees                         ,
                                'methodExtraPercent' => $option->extra_fees_percent                 ,
                                'methodText'         => $method_text
                                //'imageBitMap'        => $image_code
                             );
        }
        return $output;
    }


/************************************************************************/
}
