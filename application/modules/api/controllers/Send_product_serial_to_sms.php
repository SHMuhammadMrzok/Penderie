<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Send_product_serial_to_sms extends CI_Controller
{
  
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model('send_product_serial_to_sms_model');
        $this->load->model('general_model');
        
        $this->load->library('api_lib');
        $this->load->library('notifications');
        
    }

    public function index()
    {   
        
        $userId             = intval(strip_tags($this->input->post('userId', TRUE)));
        $email              = strip_tags($this->input->post('email', TRUE));
        $password           = strip_tags($this->input->post('password', TRUE));
        $receiverMobile     = strip_tags($this->input->post('receiverMobile', TRUE));
        
        $lang_id            = intval(strip_tags($this->input->post('langId', TRUE)));
        $deviceId           = strip_tags($this->input->post('deviceId', TRUE));
        
        $orderId            = intval(strip_tags($this->input->post('orderId', TRUE)));
        $productId          = intval(strip_tags($this->input->post('productId', TRUE)));
        $serialId           = intval(strip_tags($this->input->post('serialId', TRUE)));
        
        $store_country_id   = intval(strip_tags($this->input->post('storeCountryId', TRUE)));
        
        $agent              = strip_tags($this->input->post('agent', TRUE));
        $user_id            = 0;
        
        $output = array();
        
        $fail_message    = $this->general_model->get_lang_var_translation('execution_fail',$lang_id);
        $success_message = $this->general_model->get_lang_var_translation('execution_success',$lang_id);
        
        if($this->ion_auth->login($email, $password))
        {
            $user_data     = $this->ion_auth->user()->row();
            $user_id       = $user_data->id;
            
            $this->api_lib->check_user_store_country_id($email, $password, $user_id, $store_country_id);
            
            $sms_template  = $user_data->sms_content;
            
            if($user_data->sms_name)
            {
                $sms_sender_name = $user_data->sms_name;
            }
            else
            {
                $sms_sender_name = 'Like4card';                
            }            
            
            $product_details     = $this->send_product_serial_to_sms_model->get_product_details($lang_id , $productId);
            
            if(isset($serialId) && $serialId != '')
            {
                //-->>> send one serial
                
                $serial_data       = $this->send_product_serial_to_sms_model->get_serial_data($serialId, $lang_id);
        
                //$serial            = 'Serial Number For '.$product_details->title.' : \n'.$serial_data->serial;
                $serial            = $sms_template.'\n'.$serial_data->serial;
                
                if( $this->notifications->send_sms($serial ,$receiverMobile))//,$sms_sender_name
                {
                     $updated_data['smsed'] = 1;
                
                    $this->send_product_serial_to_sms_model->update_order_serial($serialId, $updated_data);
                    
                    $sent_serials_log = array(
                                                'user_id'         => $user_id ,
                                                'serial_id'       => $serialId ,
                                                'receiver_number' => $receiverMobile
                                              );
                    $this->send_product_serial_to_sms_model->insert_sms_log_data($sent_serials_log);
                    
                    $output  = array( 
                                        'message' => $success_message,
                                        'response' => 0
                                        );
                
                }
                else
                {
                    $output  = array( 
                                        'message' => $fail_message,
                                        'response' => 0
                                        );
                }              
                
            }
            else
            {
                
               //-->>> send  all product serials
               
                $all_serials     = $this->send_product_serial_to_sms_model->get_product_serials($productId, $orderId);
                $sms_serial      = $sms_template."\n";
                
                foreach($all_serials as $key=>$serial)
                {
                    $sms_serial .= $serial->serial."\n";
                }
                
               if ($this->notifications->send_sms ($sms_serial ,$receiverMobile))//,$sms_sender_name
               {
                    foreach($all_serials as $key=>$serial)
                    {
                        $updated_data['smsed'] = 1;
                        
                        $this->send_product_serial_to_sms_model->update_order_serial($serial->product_serial_id, $updated_data);
                        
                        $sent_serials_log = array(
                                                'user_id'         => $user_id ,
                                                'serial_id'       => $serial->product_serial_id ,
                                                'receiver_number' => $receiverMobile
                                              );
                        $this->send_product_serial_to_sms_model->insert_sms_log_data($sent_serials_log);
                    }
                    
                    $output  = array( 
                                        'message' => $success_message,
                                        'response' => 1
                                        );
                
                }
                else
                {
                    $output  = array( 
                                        'message' => $fail_message,
                                        'response' => 0
                                        );
               }//if send all
                
            }   
           
        }
        else
        {
            $output  = array( 
                                'message' => $fail_message,
                                'response' => 0
                                );
        }
        
        //***************LOG DATA***************//
        //insert log
        $this->api_lib->insert_log($user_id, current_url(), 'Send product serial to sms', $agent, $_POST, $output);
        //***************END LOG***************//
        
        $this->output->set_content_type('application/json')->set_output(json_encode($output)); 
        
    }
       
     
/************************************************************************/    
}