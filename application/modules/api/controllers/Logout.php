<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Logout extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('login_model');
        $this->load->model('general_model');
        $this->load->library('api_lib');
    }

    public function index( )
    {
        $lang_id        = intval(strip_tags($this->input->post('langId', TRUE)));
        $user_id        = intval(strip_tags($this->input->post('userId', TRUE)));
        $email          = strip_tags($this->input->post('email', TRUE));
        $password       = strip_tags($this->input->post('password', TRUE));
        $deviceId       = strip_tags($this->input->post('deviceId', TRUE));

        // Added for api log 
        $agent              = strip_tags($this->input->post('agent', TRUE));
        $user_id            = 0;

        if($this->ion_auth->login($email, $password))
        {
            $user_data  = $this->ion_auth->user()->row();
            $user_id    = $user_data->id;
        }
        ///

        $output    = array();

        $fail_message = $this->general_model->get_lang_var_translation('execution_fail',$lang_id);
        $success_message = $this->general_model->get_lang_var_translation('execution_success',$lang_id);

        //if($this->ion_auth->login($email, $password))
        //{
            //$this->ion_auth->logout();

            $output  = array(
                                'message' => $success_message,
                                'response' => intval(1)
                            );

        //}
        /*else{
            $output  = array(
                                'message' => $fail_message,
                                'response' => 0
                                );
        }*/
        
        //***************LOG DATA***************//
        //insert log
        $this->api_lib->insert_log($user_id, current_url(), 'Logout', $agent, $_POST, $output);
        //***************END LOG***************//

        $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));
    }


/************************************************************************/
}
