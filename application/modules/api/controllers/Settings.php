<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Settings extends CI_Controller
{
  
    public function __construct()
    {
        parent::__construct();
        $this->load->model('general_model');   
        $this->load->library('api_lib');    
    }

    public function index( )
    {
        $deviceId = strip_tags($this->input->post('deviceId', TRUE));
        $lang_id  = intval(strip_tags($this->input->post('langId', TRUE)));
        
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

        $output   = array();
        
        $get_data = $this->general_model->get_settings();
        
        
        if($get_data)
        {
            $output = array( 
                                'wholeSalerGroupId'     =>   $get_data->wholesaler_customer_group_id
                                );
        }
        else
        {
            $fail_message = $this->general_model->get_lang_var_translation('execution_fail',$lang_id);
            $output       = array( 
                                        'message' => $fail_message,
                                        'response' => 0
                                    );
        }
        
        //***************LOG DATA***************//
        //insert log
        $this->api_lib->insert_log($user_id, current_url(), 'Settings', $agent, $_POST, $output);
        //***************END LOG***************//
        
        $this->output->set_content_type('application/json')->set_output(json_encode($output));
        
    }
       
     
/************************************************************************/    
}