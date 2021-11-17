<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class User_balance extends CI_Controller
{
  
    public function __construct()
    {
        parent::__construct();
        
        $this->load->library('api_lib');
        $this->load->library('currency');
        
        $this->load->model('general_model');
        
    }

    public function index()
    {
        
        $lang_id    = intval(strip_tags($this->input->post('langId', TRUE)));
        $user_id    = intval(strip_tags($this->input->post('userId', TRUE)));
        $email      = strip_tags($this->input->post('email', TRUE));
        $password   = strip_tags($this->input->post('password', TRUE));
        
        $agent              = strip_tags($this->input->post('agent', TRUE));
        $user_id            = 0;

        $output     = array();
        
        if($this->ion_auth->login($email, $password))
        {
            $user_data      = $this->ion_auth->user()->row();
            $user_id    = $user_data->id;
            
            if($user_data->user_balance != '')
            {
                $user_balance = $this->api_lib->get_any_user_balance($user_data->id);
            }
            else
            {
                $user_balance = 0;
            }
            $currency_name  = $this->currency->get_country_currency_name($user_data->store_country_id, $lang_id);
            
            $output =   array(
                                'balance' => $user_balance,
                                'symbol'  => $currency_name  
                             );
            
        }
        else
        {
            $message = $this->general_model->get_lang_var_translation('login_error', $lang_id);
            $output  = array(
                              'message'  => $message ,
                              'response' => 0
                           );
        }
        
        //***************LOG DATA***************//
        //insert log
        $this->api_lib->insert_log($user_id, current_url(), 'User balance', $agent, $_POST, $output);
        //***************END LOG***************//
        
        $this->output->set_content_type('application/json')->set_output(json_encode($output));

        
    }
       
     
/************************************************************************/    
}