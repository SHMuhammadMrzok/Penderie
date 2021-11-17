<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Add_coupon_code extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('general_model');

        $this->load->library('api_lib');
        $this->load->library('shopping_cart');
    }

    public function index()
    {
        $lang_id        = strip_tags($this->input->post('langId', TRUE));
        $country_id     = strip_tags($this->input->post('countryId', TRUE));

        //$user_id         = strip_tags($this->input->post('userId', TRUE));
        $email          = strip_tags($this->input->post('email', TRUE));
        $password       = strip_tags($this->input->post('password', TRUE));

        $couponCode     = strip_tags($this->input->post('couponCode', TRUE));

        $deviceId       = strip_tags($this->input->post('deviceId', TRUE));
        $agent          = strip_tags($this->input->post('agent', TRUE));
        $ip_address     = $this->input->ip_address();

        if($this->ion_auth->login($email, $password))
        {
            $user_data = $this->ion_auth->user()->row();
            $user_id = $user_data->id;
            $this->api_lib->check_user_store_country_id($email, $password, $user_data->id, $country_id);
        }
        else {
          $user_id = 0;
        }

        $this->shopping_cart->set_user_data($user_id, $deviceId, $ip_address , $country_id ,$lang_id);

        $coupon_result_array = $this->shopping_cart->coupon_discount($couponCode);
        $cart_data           = $this->shopping_cart->shopping_cart_data();

        $total_price = round($cart_data->total_price, 2);
        $discount = round($cart_data->discount, 2);
        $coupon_discount = round($cart_data->coupon_discount, 2);
        $final_total_price = round($cart_data->final_total_price_with_tax, 2);
        $vat_value = round($cart_data->vat_value, 2);

        if($coupon_result_array[0] == 1)
        {
            $output = array(
                               'totalCost'      => "$total_price"         ,
                               'discount'       => "$discount"            ,
                               'couponDiscount' => "$coupon_discount"     ,
                               'finalTotalCost' => "$final_total_price"   ,
                               'vatValue'       => "$vat_value"
                           );
        }
        else
        {
            $output = array(
                               'message'  => $coupon_result_array[1],
                               'response' => 0
                           );
        }


        //***************LOG DATA***************//
        //insert log
        $this->api_lib->insert_log($user_id, current_url(), 'Add coupon code', $agent, $_POST, $output);
        //***************END LOG***************//

        $this->output->set_content_type('application/json')->set_output(json_encode($output));

    }


/************************************************************************/
}
