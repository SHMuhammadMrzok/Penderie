<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Orders_status extends CI_Controller
{
  
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model('follow_orders_model');
        $this->load->model('general_model');
        $this->load->library('pagination');

        $this->load->library('api_lib');
    }

    public function index($page =1)
    {   
                
        $lang_id   = intval(strip_tags($this->input->post('langId', TRUE)));
        $deviceId  = strip_tags($this->input->post('deviceId', TRUE));  
        
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

        $output = array();
        
        $fail_message = $this->general_model->get_lang_var_translation('execution_fail',$lang_id);
        
        $order_status     = $this->follow_orders_model->get_order_status($lang_id);
        
        if(isset($order_status))
        {
            foreach($order_status as $status)
            {
                $output [] = array(
                                    'statusId'      => $status->id,
                                    'statusTitle'   => $status->name,
                                    'statusImage'   => base_url().'assets/template/site/img/'.$status->status_image
                                    );
            }   
        }
        else
        {
            $output = array( 
                                'message' => $fail_message,
                                'response' => 0
                                );
        }
        
        //***************LOG DATA***************//
        //insert log
        $this->api_lib->insert_log($user_id, current_url(), 'Orders statuses', $agent, $_POST, $output);
        //***************END LOG***************//

        $this->output->set_content_type('application/json')->set_output(json_encode($output)); 
        
    }
       
     
/************************************************************************/    
}