<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Maintenance extends CI_Controller
{
  
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model('general_model');
        $this->load->model('maintenance/maintenance_model');
        
        $this->load->library('api_lib');
        
        
    }

    public function index()
    {
        $lang_id        = intval($this->input->post('langId', TRUE));
        $email          = strip_tags($this->input->post('email', TRUE));
        $password       = strip_tags($this->input->post('password', TRUE));
        $name           = strip_tags($this->input->post('name', TRUE));
        $phone          = strip_tags($this->input->post('phone', TRUE));
        $description    = strip_tags($this->input->post('description', TRUE));
        $deviceId       = strip_tags($this->input->post('deviceId', TRUE));
        $lat            = strip_tags($this->input->post('lat', TRUE));
        $lng            = strip_tags($this->input->post('lng', TRUE));
        $product_name   = strip_tags($this->input->post('productName', TRUE));
        $ip_address     = $this->input->ip_address();
        
        $agent              = strip_tags($this->input->post('agent', TRUE));
        $user_id            = 0;
        
        $output         = array();
        
        if($this->ion_auth->login($email, $password))
        {
            $user     = $this->ion_auth->user()->row();
            $user_id  = $user->id;
            
            $name_lang          = $this->general_model->get_lang_var_translation('name', $lang_id);
            $phone_lang         = $this->general_model->get_lang_var_translation('phone', $lang_id);
            $address_lang       = $this->general_model->get_lang_var_translation('address', $lang_id);
            $description_lang   = $this->general_model->get_lang_var_translation('description', $lang_id);
            $required_lang      = $this->general_model->get_lang_var_translation('required', $lang_id);
            $product_name_lang  = $this->general_model->get_lang_var_translation('product_name', $lang_id);
            
            $this->form_validation->set_rules('name', $name_lang, 'required');
            $this->form_validation->set_rules('phone', $phone_lang, 'required');
            $this->form_validation->set_rules('productName', $product_name_lang, 'required');
            $this->form_validation->set_rules('description', $description_lang, 'required');
            $this->form_validation->set_rules('lat', $address_lang, 'required');
            $this->form_validation->set_rules('lng', $address_lang, 'required');
            
            $this->form_validation->set_message('required', $required_lang."  : %s ");
            
            if($this->form_validation->run() == FALSE)
            {
                $output = array(
                                'response' => 0,
                                'message' => strip_tags(validation_errors())
                                );
            }
            else
            {
                $main_data = array(
                                    'user_id'       => $user_id     ,
                                    'name'          => $name        ,
                                    'phone'         => $phone       ,
                                    'product_name'  => $product_name,
                                    'lat'           => $lat         ,
                                    'lng'           => $lng         ,
                                    'description'   => $description ,
                                    'ip_address'    => $ip_address  ,
                                    'unix_time'     => time()
                                    );
                
                $this->maintenance_model->insert_message($main_data);
                
                $message = $this->general_model->get_lang_var_translation('execution_success', $lang_id);
                $output = array(
                                'response' => 1,
                                'message' => $message
                                );
            }
        }
        else
        {
        $fail_message   = $this->general_model->get_lang_var_translation('login_error',$lang_id);
        $output         = array( 
                                    'message'   => $fail_message,
                                    'response'  => 0
                                );
        }
        
        //***************LOG DATA***************//
        //insert log
        $this->api_lib->insert_log($user_id, current_url(), 'Maintenance', $agent, $_POST, $output);
        //***************END LOG***************//

        $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));

        
    }
    
     
/************************************************************************/    
}