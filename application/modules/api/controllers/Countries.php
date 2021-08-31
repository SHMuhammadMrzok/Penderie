<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Countries extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('users/countries_model');
        $this->load->model('general_model');

    }

    public function index()
    {
        $lang_id    = intval($this->input->post('langId', TRUE));
        $deviceId   = strip_tags($this->input->post('deviceId', TRUE));

        $countries  = $this->countries_model->get_countries($lang_id);
        $output     = array();

        if(isset($countries) && !empty($countries))
        {
            foreach($countries as $country)
            {
                $output [] = array(
                                    'countryId'             => $country->id,
                                    'countryName'           => $country->name,
                                    'countryImage'          => base_url().'assets/uploads/'.$country->flag,
                                    'countryCurrency'       => $country->currency,
                                    'countryCurrencySymbol' => $country->currency_symbol,
                                    'countryRewardPoints'   => $country->reward_points,
                                    );
            }
        }
        else
        {
            $fail_message   = $this->general_model->get_lang_var_translation('execution_fail',$lang_id);
            $output         = array(
                                    'message' => $fail_message,
                                    'response' => 0
                                );
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($output));
    }

    public function update_user_country()
    {
        $this->load->library('api_lib');
        $this->load->library('shopping_cart');

        $lang_id        = intval(strip_tags($this->input->post('langId', TRUE)));
        $userId         = intval(strip_tags($this->input->post('userId', TRUE)));
        $email          = strip_tags($this->input->post('email', TRUE));
        $password       = strip_tags($this->input->post('password', TRUE));
        $deviceId       = strip_tags($this->input->post('deviceId', TRUE));
        $ip_address     = $this->input->ip_address();
        $country_id     = intval(strip_tags($this->input->post('countryId', TRUE)));
        $prev_country   = intval(strip_tags($this->input->post('prevCountryId', TRUE)));

        $output         = array();

        if($this->ion_auth->login($email, $password))
        {
            $user_data = $this->ion_auth->user()->row();
            $this->api_lib->check_user_store_country_id($email, $password, $user_data->id, $country_id);
        }

        if($country_id != $prev_country)
        {
            $this->shopping_cart->set_user_data($userId, $deviceId, $ip_address , $country_id ,$lang_id);
            $this->shopping_cart->delete();

            $response = 1;
            $message  = $this->general_model->get_lang_var_translation('updated_successfully', $lang_id);
        }
        else
        {
            $response = 0;
            $message  = $this->general_model->get_lang_var_translation('country_not_changed', $lang_id);
        }

        $output = array(
                         'response' => $response,
                         'message'  => $message
                       );


        $this->output->set_content_type('application/json')->set_output(json_encode($output));

    }


/************************************************************************/
}
