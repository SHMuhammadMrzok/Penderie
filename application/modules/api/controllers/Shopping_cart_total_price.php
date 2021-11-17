<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Shopping_cart_total_price extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('user_shopping_cart_model');
        $this->load->model('general_model');
        $this->load->model('users/countries_model');
        $this->load->model('payment_options/payment_methods_model');
        $this->load->model('shopping_cart/user_bank_accounts_model');

        $this->load->library('api_lib');
        $this->load->library('shopping_cart');
        $this->load->library('products_lib');

    }

    public function index()
    {
        $lang_id            = intval($this->input->post('langId', TRUE));
        //$user_id             = intval($this->input->post('userId', TRUE));
        $deviceId           = strip_tags($this->input->post('deviceId', TRUE));
        $country_id         = intval($this->input->post('countryId', TRUE));
        $ip_address         = $this->input->ip_address();
        $email              = strip_tags($this->input->post('email', TRUE));
        $password           = strip_tags($this->input->post('password', TRUE));
        $payment_option_id  = intval($this->input->post('paymentMethodId', TRUE));
        
        $agent              = strip_tags($this->input->post('agent', TRUE));
        
        $user_id = 0;
        if($this->ion_auth->login($email, $password))
        {
            $user_data = $this->ion_auth->user()->row();
            $user_id = $user_data->id;
            $this->api_lib->check_user_store_country_id($email, $password, $user_data->id, $country_id);
        }

        $output = array();

        $this->shopping_cart->set_user_data($user_id, $deviceId, $ip_address , $country_id ,$lang_id);

        $cart_data  = $this->shopping_cart->shopping_cart_data();
        $tax        = $this->calculate_payment_tax($payment_option_id, $cart_data->final_total_price, $country_id);
        $final_total_price_with_tax = $cart_data->final_total_price + $tax;

        /*if($payment_option_id == 7)
        {
           //$voucher_number = $this->input->post('voucher');
        }*/

        if($payment_option_id == 3)
        {
            $bank_id = intval(strip_tags($this->input->post('bankId', TRUE)));
        }
        else
        {
            $bank_id = 0;
        }

        $updated_data = array(
                                'final_total_price_with_tax'    => $final_total_price_with_tax  ,
                                'payment_option_id'             => $payment_option_id           ,
                                'bank_id'                       => $bank_id                     ,
                                'tax'                           => $tax
                            );

        $this->shopping_cart->update_this_shopping_cart($updated_data);

        if($bank_id != 0 && $user_id != 0)
        {
            $this->user_bank_accounts_model->delete_bank_account($bank_id, $user_id);

            $data    = array(
                                'user_id'        => $user_id ,
                                'bank_id'        => $bank_id ,
                            );

            $this->user_bank_accounts_model->insert_user_account_data($data);
        }

        $this->shopping_cart->check_cart_shipping_cost();
        $this->shopping_cart->update_cart_total_prices($cart_data->id);
        $cart_data = $this->shopping_cart->shopping_cart_data();

        $total_cost = $cart_data->total_price + $cart_data->optional_fields_cost;

        $total = round($total_cost, 2);
        $discount = round($cart_data->discount, 2);
        $coupon_discount = round($cart_data->coupon_discount, 2);
        $tax = round($cart_data->tax, 2);
        $final_total_price_with_tax = round($cart_data->final_total_price_with_tax, 2);
        $shipping_cost = round($cart_data->shipping_cost, 2);
        $wrapping_cost = round($cart_data->wrapping_cost, 2);
        $vat_value = round($cart_data->vat_value, 2);

        $output    = array(
                            'currency'       => $cart_data->currency_symbol     ,
                            'totalCost'      => "$total"                        ,
                            'itemsDiscount'  => "$discount"                     ,
                            'couponDiscount' => "$coupon_discount"              ,
                            'paymentTaxCost' => "$tax"                          ,
                            'finalTotalCost' => "$final_total_price_with_tax"   ,
                            'shippingCosts'  => "$shipping_cost"                ,
                            'wrappingCost'   => "$wrapping_cost"                ,
                            'vatValue'       => "$vat_value"                    ,
                            'vatPercent'     => $cart_data->vat_percent         ,
                          );

        if($cart_data->payment_option_id == 2)
        {
          $point_value = $this->countries_model->get_reward_points($country_id);
          $cart_points = $cart_data->final_total_price_with_tax / $point_value;

          $output['cartPoints'] = $cart_points;
        }

        //***************LOG DATA***************//
        //insert log
        $this->api_lib->insert_log($user_id, current_url(), 'Shopping cart total price', $agent, $_POST, $output);
        //***************END LOG***************//

        $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));

    }

    public function calculate_payment_tax($payment_option_id, $total, $country_id)
    {
        if($payment_option_id ==0) return 0;

        $option_data = $this->payment_methods_model->get_option_data($payment_option_id);

        $tax_percent = round(($option_data->extra_fees_percent * $total), 2)/ 100;
        $tax_amount  = $this->currency->convert_from_default_currency($option_data->extra_fees, $country_id);

        $tax         = $tax_percent + $tax_amount;

        return $tax;
    }


/************************************************************************/
}
