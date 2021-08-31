<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Forget_password extends CI_Controller
{
  
    public function __construct()
    {
        parent::__construct();
        $this->load->model('general_model');
        $this->load->model('users/user_model');
        
        $this->load->library('api_lib');
    }

    public function index( )
    {   
      
       $email               = strip_tags($this->input->post('email', TRUE));
       $deviceId            = strip_tags($this->input->post('deviceId', TRUE));
       $lang_id             = intval(strip_tags($this->input->post('langId', TRUE)));
       $store_country_id    = intval(strip_tags($this->input->post('storeCountryId', TRUE)));
      
       $output    = array();
       
       $this->form_validation->set_rules('email', ('email'),'required|valid_email');
       
       if ($this->form_validation->run() == false)
       {
            $message = $this->general_model->get_lang_var_translation('validation_error', $lang_id);
            
            $output  = array(
                               'message'  => $message ,
                               'response' => 0
                            );
       }
       else
       {
           //-->>> check if email found in db
           if ($this->ion_auth->email_check($email))
    	   {
    	       $user_data   = $this->user_model->get_user_data_by_field('email', $email);
               $user_active = $user_data->active;
               
               if($user_active == 1)
               {
                    //$this->api_lib->check_user_store_country_id($email, $password, $user_data->id, $store_country_id);
        			//run the forgotten password method to email an activation code to the user
        			$forgotten = $this->ion_auth->forgotten_password($email);
                    
        			if ($forgotten)
        			{
        			     //if there were no errors
                         $success_message = $this->general_model->get_lang_var_translation('execution_success',$lang_id);
                         $output  = array( 
                                            'message' => $success_message,
                                            'response' => 1
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
                }
                else
                {
                    $return_message = $this->general_model->get_lang_var_translation('not_active_account',$lang_id);
                
                    $output         = array( 
                                               'message' => $return_message,
                                               'response' => 0
                                            );
                    }
          
           }
           else 
           {
                $return_message = $this->general_model->get_lang_var_translation('email_error',$lang_id);
                
                $output         = array( 
                                           'message' => $return_message,
                                           'response' => 0
                                        );
                 
    	  }
      }
   
      $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));
        
    }
       
     
/************************************************************************/    
}