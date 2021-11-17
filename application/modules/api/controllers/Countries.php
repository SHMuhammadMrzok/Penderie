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

        $this->load->library('api_lib');
    }

    public function index()
    {
        $lang_id    = intval($this->input->post('langId', TRUE));
        $deviceId   = strip_tags($this->input->post('deviceId', TRUE));

        // Added for api log
        $email              = strip_tags($this->input->post('email', TRUE));
        $password           = strip_tags($this->input->post('password', TRUE));  
        $agent              = strip_tags($this->input->post('agent', TRUE));
        $user_id            = 0;

        if($this->ion_auth->login($email, $password))
        {
            $user_data  = $this->ion_auth->user()->row();

            $user_id    = $user_data->id;
        }
        ///

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

        //***************LOG DATA***************//
        //insert log
        $this->api_lib->insert_log($user_id, current_url(), 'Countries List', $agent, $_POST, $output);
        //***************END LOG***************//

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

        $agent          = strip_tags($this->input->post('agent', TRUE));
        
        $output         = array();
      
        if($this->ion_auth->login($email, $password))
        {
            $user_data = $this->ion_auth->user()->row();
            $user_id    = $user_data->id;
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

        //***************LOG DATA***************//
        //insert log
        $this->api_lib->insert_log($user_id, current_url(), 'Countries - Update user Country', $agent, $_POST, $output);
        //***************END LOG***************//

        $this->output->set_content_type('application/json')->set_output(json_encode($output));

    }


/************************************************************************/
}
