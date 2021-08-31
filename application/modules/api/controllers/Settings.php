<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Settings extends CI_Controller
{
  
    public function __construct()
    {
        parent::__construct();
        $this->load->model('general_model');       
    }

    public function index( )
    {
       $deviceId = strip_tags($this->input->post('deviceId', TRUE));
       $lang_id  = intval(strip_tags($this->input->post('langId', TRUE)));
       
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
       
       $this->output->set_content_type('application/json')->set_output(json_encode($output));
        
    }
       
     
/************************************************************************/    
}