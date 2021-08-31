<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Shipping extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->library('api_lib');
        $this->load->library('products_lib');
        $this->load->library('shopping_cart');
        $this->load->library('currency');
        $this->load->library('location_locator');

        $this->load->model('users/cities_model');
        $this->load->model('general_model');
        $this->load->model('global_model');
        $this->load->model('products/products_model');
        $this->load->model('shipping/costs_model');
        $this->load->model('shipping/companies_model');


    }

    // for shipping
    public function shipping_companies()
    {
        $user_id     = $this->input->post('userId');
        $email       = $this->input->post('email');
        $password    = $this->input->post('password');

        $lang_id     = $this->input->post('langId');
        $country_id  = $this->input->post('countryId');

        $device_id   = $this->input->post('deviceId');
        $ip_address  = $this->input->ip_address();


        if($this->ion_auth->login($email, $password))
        {
            $user_data = $this->ion_auth->user()->row();
            $user_id   = $user_data->id;
            $this->api_lib->check_user_store_country_id($email, $password, $user_data->id, $country_id);
        }

        $this->shopping_cart->set_user_data($user_id, $device_id, $ip_address , $country_id ,$lang_id);

        $cart_data = $this->shopping_cart->shopping_cart_data();

        if($cart_data->needs_shipping == 1)
        {
            $companies_array      = array();

            $shipping_companies   = $this->companies_model->get_shipping_companies_result($lang_id);

            foreach($shipping_companies as $comp)
            {
                $companies_array[] = array(
                                            'companyId'   => $comp->id,
                                            'companyName' => $comp->name
                                          );
            }


            $output = $companies_array;
        }
        else
        {
            $msg    = $this->general_model->get_lang_var_translation('no_shipping_for_this_cart', $lang_id);

            $output = array(
                               'response' => 0,
                               'message'  => $msg
                           );
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));
    }

    public function shipping_countries()
    {
        $output      = array();
        $user_id     = intval($this->input->post('userId', true));
        $email       = strip_tags($this->input->post('email', true));
        $password    = strip_tags($this->input->post('password', true));

        $lang_id     = intval($this->input->post('langId', true));
        $country_id  = intval($this->input->post('countryId', true));

        $device_id   = strip_tags($this->input->post('deviceId', true));
        $not_cart     = intval($this->input->post('notCart', true));
        $ip_address  = $this->input->ip_address();


        if($this->ion_auth->login($email, $password))
        {
            $user_data = $this->ion_auth->user()->row();
            $user_id   = $user_data->id;
            $this->api_lib->check_user_store_country_id($email, $password, $user_data->id, $country_id);
        }

        $this->shopping_cart->set_user_data($user_id, $device_id, $ip_address , $country_id ,$lang_id);

        $cart_data = $this->shopping_cart->shopping_cart_data();

        if($cart_data->needs_shipping == 1 || $not_cart)
        {
            $costs_array          = array();

            $countries_costs      = $this->costs_model->get_shipping_costs_result($lang_id);
            $store_currency_data  = $this->currency->get_country_currency_data($country_id);

            foreach($countries_costs as $row)
            {
                if($row->currency_id != $store_currency_data->id)
                {
                    $cost = $this->currency->get_amount_from_currency_to_currency($row->cost, $row->currency_id, $store_currency_data->id);
                }
                else
                {
                    $cost = $row->cost;
                }

                $cities  = $this->costs_model->get_shipping_cities($row->id, $lang_id);
                $cities_array = array();
                foreach($cities as $item)
                {
                    $cities_array[] = array(
                                            'cityId'   => $item->id,
                                            'cityName' => $item->name
                                           );
                }


                $costs_array [] = array(
                                           'cost'    => $cost,
                                           'costId'  => $row->id,
                                           'country' => $row->country,
                                           'cities'  => $cities_array
                                       );
            }

            //$output = $costs_array;
        }
        else
        {
            $msg    = $this->general_model->get_lang_var_translation('no_shipping_for_this_cart', $lang_id);

            $costs_array = array(
                               'response' => 0,
                               'message'  => $msg
                           );
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($costs_array, JSON_UNESCAPED_UNICODE));
    }

    // for delivery
    public function shipping_cities()
    {
        $user_id     = intval($this->input->post('userId', TRUE));
        $email       = strip_tags($this->input->post('email', TRUE));
        $password    = strip_tags($this->input->post('password', TRUE));

        $lang_id     = intval($this->input->post('langId', TRUE));
        $country_id  = intval($this->input->post('countryId', TRUE));

        $device_id   = strip_tags($this->input->post('deviceId', TRUE));
        $ip_address  = $this->input->ip_address();


        /*if($this->ion_auth->login($email, $password))
        {
            $user_data = $this->ion_auth->user()->row();
            $user_id   = $user_data->id;
            $this->api_lib->check_user_store_country_id($email, $password, $user_data->id, $country_id);
        }
        */

        $this->shopping_cart->set_user_data($user_id, $device_id, $ip_address , $country_id ,$lang_id);

        $cart_data = $this->shopping_cart->shopping_cart_data();

        if($cart_data->needs_shipping == 1)
        {
            $cities_array   = array();
            $cities         = $this->cities_model->get_cities($lang_id);

            foreach($cities as $row)
            {
                $cities_array[] = array(
                                        'cityId'   => $row->id,
                                        'cityName' => $row->name
                                       );
            }

            $output = $cities_array;
        }
        else
        {
            $msg    = $this->general_model->get_lang_var_translation('no_shipping_for_this_cart', $lang_id);

            $output = array(
                               'response' => 0,
                               'message'  => $msg
                           );
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));
    }



    public function save_shipping_data()
    {
        $lang_id  = $this->input->post('langId', TRUE);
        $required = $this->general_model->get_lang_var_translation('required', $lang_id);

        $branch_lang_var        = $this->general_model->get_lang_var_translation('branch', $lang_id);
        $shipping_type_lang_var = $this->general_model->get_lang_var_translation('shipping_type', $lang_id);

        $email       = strip_tags($this->input->post('email', TRUE));
        $password    = strip_tags($this->input->post('password', TRUE));
        $country_id  = intval($this->input->post('countryId', TRUE));
        $device_id   = strip_tags($this->input->post('deviceId', TRUE));

        $ip_address  = $this->input->ip_address();

        if($this->ion_auth->login($email, $password))
        {
            $user_data = $this->ion_auth->user()->row();
            $user_id   = $user_data->id;
            $this->api_lib->check_user_store_country_id($email, $password, $user_data->id, $country_id);
        }
        else
        {
            $user_id = 0;
        }

        $this->shopping_cart->set_user_data($user_id, $device_id, $ip_address , $country_id ,$lang_id);

        $cart_data = $this->shopping_cart->shopping_cart_data();
        if($cart_data->needs_shipping != 1)
        {
            $msg    = $this->general_model->get_lang_var_translation('no_shipping_for_this_cart', $lang_id);
            $output = array(
                               'response' => 0,
                               'message'  => $msg
                           );
        }
        else
        {
            $shipping_type = intval($this->input->post('shippingType', TRUE));
            /**
             * shipping types
             * 1 => Delivery
             * 2 => Recieve from shop
             * 3 => companies
            */

            if($shipping_type == 1)
            {

                $city_lang_var      = $this->general_model->get_lang_var_translation('shipping_city', $lang_id);
                $address_lang_var   = $this->general_model->get_lang_var_translation('shipping_address', $lang_id);
                $name_lang_var      = $this->general_model->get_lang_var_translation('name', $lang_id);
                $phone_lang_var     = $this->general_model->get_lang_var_translation('phone', $lang_id);

                //$this->form_validation->set_rules('shippingCityId', $city_lang_var, 'required');
                $this->form_validation->set_rules('shippingName', $name_lang_var, 'required');
                $this->form_validation->set_rules('shippingPhone', $phone_lang_var, 'required');
                //$this->form_validation->set_rules('shippingAddress', $address_lang_var, 'required');

                $this->form_validation->set_message('required', $required.' : %s ');

                if ($this->form_validation->run() == false)
                {
                    $message = strip_tags(validation_errors());

                    $output  = array(
                                       'message'  => $message ,
                                       'response' => 0
                                    );
                }
                else
                {
                    $shipping_city_id   = trim($this->input->post('shippingCityId', TRUE));
                    $shipping_address   = trim($this->input->post('shippingAddress', TRUE));
                    $shipping_name      = trim($this->input->post('shippingName', TRUE));
                    $shipping_phone     = trim($this->input->post('shippingPhone', TRUE));
                    $lat                = strip_tags($this->input->post('lat', TRUE));
                    $lng                = strip_tags($this->input->post('lng', TRUE));

                    $list       = $this->location_locator->get_branch_list($lat.','.$lng, $lang_id);
                    $list       = json_decode($list);


                    if(count($list) != 0)
                    {
                        $updated_data = array(
                                                'shipping_type'     => $shipping_type       ,
                                                'shipping_city'     => $shipping_city_id    ,
                                                'shipping_name'     => $shipping_name       ,
                                                'shipping_address'  => $shipping_address    ,
                                                'shipping_phone'    => $shipping_phone      ,
                                                'shipping_lat'      => $lat,
                                                'shipping_lng'      => $lng,
                                             );

                        $this->shopping_cart->update_cart($cart_data->id, $updated_data);
                        $this->shopping_cart->check_cart_shipping_cost();

                        $msg    = $this->general_model->get_lang_var_translation('execution_success', $lang_id);

                        $output = array(
                                           'response' => 1,
                                           'message'  => $msg
                                       );
                    }
                    else
                    {
                        $msg    = $this->general_model->get_lang_var_translation('no_near_branches', $lang_id);

                        $output = array(
                                           'response' => 0,
                                           'message'  => $msg
                                       );
                    }

                }
            }
            else if($shipping_type == 2)
            {
                //$required          = $this->general_model->get_lang_var_translation('required', $lang_id);
                $branch_lang_var   = $this->general_model->get_lang_var_translation('branch', $lang_id);

                $this->form_validation->set_rules('branchId', $branch_lang_var, 'required|greater_than[0]');
                $this->form_validation->set_message('required', $required.' : %s ');
                $this->form_validation->set_message('greater_than', $required.' : %s ');

                if ($this->form_validation->run() == false)
                {
                    $message = strip_tags(validation_errors());

                    $output  = array(
                                       'message'  => $message ,
                                       'response' => 0
                                    );
                }
                else
                {
                    $branch_id    = intval($this->input->post('branchId', TRUE));

                    $updated_data = array(
                                            'shipping_type' => $shipping_type   ,
                                            'branch_id'     => $branch_id       ,
                                            'shipping_lat'  => strip_tags($this->input->post('lat', TRUE)),
                                            'shipping_lng'  => strip_tags($this->input->post('lng', TRUE)),
                                         );

                    $this->shopping_cart->update_cart($cart_data->id, $updated_data);

                    //update cart shipping cost
                    $this->shopping_cart->check_cart_shipping_cost();

                    $msg    = $this->general_model->get_lang_var_translation('execution_success', $lang_id);

                    $output = array(
                                       'response' => 1,
                                       'message'  => $msg
                                   );
                }
            }
            else if($shipping_type == 3)
            {
                //$required          = $this->general_model->get_lang_var_translation('required', $lang_id);
                $company_lang_var  = $this->general_model->get_lang_var_translation('shipping_company', $lang_id);
                $country_lang_var  = $this->general_model->get_lang_var_translation('shipping_country', $lang_id);
                $address_lang_var  = $this->general_model->get_lang_var_translation('shipping_address', $lang_id);
                $city_lang_var     = $this->general_model->get_lang_var_translation('shipping_city', $lang_id);
                $district_lang_var = $this->general_model->get_lang_var_translation('shipping_district', $lang_id);
                $name_lang_var      = $this->general_model->get_lang_var_translation('name', $lang_id);
                $phone_lang_var     = $this->general_model->get_lang_var_translation('phone', $lang_id);

                $this->form_validation->set_rules('companyId', $company_lang_var, 'required');
                $this->form_validation->set_rules('costId', $country_lang_var, 'required');
                $this->form_validation->set_rules('shippingAddress', $address_lang_var, 'required');
                //$this->form_validation->set_rules('shippingTown', $city_lang_var, 'required');
                $this->form_validation->set_rules('shippingDistrict', $district_lang_var, 'required');
                $this->form_validation->set_rules('shippingName', $name_lang_var, 'required');
                $this->form_validation->set_rules('shippingPhone', $phone_lang_var, 'required');

                $this->form_validation->set_message('required', $required.' : %s ');

                if ($this->form_validation->run() == false)
                {
                    $message = strip_tags(validation_errors());

                    $output  = array(
                                       'message'  => $message ,
                                       'response' => 0
                                    );
                }
                else
                {
                    $shipping_company_id = intval($this->input->post('companyId'));
                    $shipping_cost_id    = intval($this->input->post('costId'));
                    $shipping_address    = strip_tags($this->input->post('shippingAddress', TRUE));
                    $shipping_city       = strip_tags($this->input->post('shippingTown', TRUE));
                    $shipping_district   = strip_tags($this->input->post('shippingDistrict', TRUE));
                    $shipping_name       = strip_tags($this->input->post('shippingName', TRUE));
                    $shipping_phone      = strip_tags($this->input->post('shippingPhone', TRUE));

                    if($this->ion_auth->login($email, $password))
                    {
                        $user_data = $this->ion_auth->user()->row();
                        $user_id   = $user_data->id;
                        $this->api_lib->check_user_store_country_id($email, $password, $user_data->id, $country_id);
                    }

                    $this->shopping_cart->set_user_data($user_id, $device_id, $ip_address , $country_id ,$lang_id);

                    //update cart shipping cost
                    $this->shopping_cart->check_cart_shipping_cost();

                    $cart_data = $this->shopping_cart->shopping_cart_data();
                    if($cart_data->needs_shipping != 1)
                    {
                        $msg    = $this->general_model->get_lang_var_translation('no_shipping_for_this_cart', $lang_id);

                        $output = array(
                                           'response' => 0,
                                           'message'  => $msg
                                       );
                    }
                    else
                    {
                        $updated_data = array(
                                                'shipping_company_id'   => $shipping_company_id ,
                                                'shipping_country_id'   => $shipping_cost_id    ,
                                                'shipping_address'      => $shipping_address    ,
                                                'shipping_city'         => $shipping_city       ,
                                                'shipping_district'     => $shipping_district   ,
                                                'shipping_phone'        => $shipping_phone      ,
                                                'shipping_name'         => $shipping_name
                                             );

                        $this->shopping_cart->update_cart($cart_data->id, $updated_data);
                        $this->shopping_cart->check_cart_shipping_cost();


                        $msg    = $this->general_model->get_lang_var_translation('execution_success', $lang_id);

                        $output = array(
                                           'response' => 1,
                                           'message'  => $msg
                                       );
                    }
                }

            }
            elseif($shipping_type == 4)
            {
                $address_lang_var   = $this->general_model->get_lang_var_translation('address', $lang_id);

                $this->form_validation->set_rules('addressId', $address_lang_var, 'required');
                $this->form_validation->set_message('required', $required.' : %s ');

                if ($this->form_validation->run() == false)
                {
                    $message = strip_tags(validation_errors());

                    $output  = array(
                                       'message'  => $message ,
                                       'response' => 0
                                    );
                }
                else
                {
                    $address_id    = intval($this->input->post('addressId', TRUE));

                    $updated_data = array(
                                            'shipping_type' => $shipping_type   ,
                                            'address_id'    => $address_id
                                         );

                    $this->shopping_cart->update_cart($cart_data->id, $updated_data);

                    //update cart shipping cost
                    $this->shopping_cart->check_cart_shipping_cost();

                    $msg    = $this->general_model->get_lang_var_translation('execution_success', $lang_id);

                    $output = array(
                                       'response' => 1,
                                       'message'  => $msg
                                   );
                }
            }
        }


        $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));

    }

    public function check_shipping()
    {
        $user_id     = $this->input->post('userId', TRUE);
        $email       = $this->input->post('email', TRUE);
        $password    = $this->input->post('password', TRUE);

        $lang_id     = $this->input->post('langId', TRUE);
        $country_id  = $this->input->post('countryId', TRUE);

        $device_id   = $this->input->post('deviceId', TRUE);
        $ip_address  = $this->input->ip_address();

        if($this->ion_auth->login($email, $password))
        {
            $user_data = $this->ion_auth->user()->row();
            $user_id   = $user_data->id;
            $this->api_lib->check_user_store_country_id($email, $password, $user_data->id, $country_id);
        }

        $this->shopping_cart->set_user_data($user_id, $device_id, $ip_address , $country_id ,$lang_id);

        $cart_data = $this->shopping_cart->shopping_cart_data();

        if($cart_data->needs_shipping == 1)
        {
            $shipping = 1;
        }
        else
        {
            $shipping = 0;
        }

        $output['shipping'] = $shipping;

        $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));
    }

/************************************************************************/
}
