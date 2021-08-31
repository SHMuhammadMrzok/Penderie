<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Charge_with_code extends CI_Controller
{
  
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model('charge_with_code_model');
        $this->load->model('general_model');
        $this->load->model('users/user_model');
        
        $this->load->library('api_lib');
        $this->load->library('encryption');
        
        $this->config->load('encryption_keys');
        
    }

    public function index()
    {   
        
        $user_id            = strip_tags($this->input->post('userId', TRUE));
        $email              = strip_tags($this->input->post('email', TRUE));
        $password           = strip_tags($this->input->post('password', TRUE));
        $lang_id            = strip_tags($this->input->post('langId', TRUE));
        $serial             = strip_tags($this->input->post('generatedCode', TRUE));
        $pin                = strip_tags($this->input->post('genratedPin', TRUE));
        $deviceId           = strip_tags($this->input->post('deviceId', TRUE));
        $store_country_id   = strip_tags($this->input->post('storeCountryId', TRUE));
        
        $output    = array();
        
        $fail_message = $this->general_model->get_lang_var_translation('execution_fail',$lang_id);
        $success_message = $this->general_model->get_lang_var_translation('execution_success',$lang_id);
        
        if($this->ion_auth->login($email, $password))
        {            
            $user_id = $this->ion_auth->user()->row()->id;
            
            $this->api_lib->check_user_store_country_id($email, $password, $user_id, $store_country_id);
            
            $secret_key = $this->config->item('new_encryption_key');
            $secret_iv  = md5('generated_code');
            
            $dec_serial = $this->encryption->encrypt($serial, $secret_key, $secret_iv);
            $dec_pin    = $this->encryption->encrypt($pin, $secret_key, $secret_iv);
            
            $card_data  = $this->charge_with_code_model->get_code_data($dec_serial, $dec_pin);
            
            //var_dump($card_data);die();
            
            if($card_data)
            {
                if($card_data->sold == 0 && $card_data->charged == 0)
                {
                    $card_amount    = $this->encryption->decrypt($card_data->amount, $secret_key, $secret_iv);
                    //$user_data      = $this->user_model->get_row_data($user_id);
                    $user_data      = $this->ion_auth->user()->row();
                    $balance        = $this->encryption->decrypt($user_data->user_balance, $secret_key, $user_id);
                    
                    $user_new_balance = $card_amount + $balance;
                    $enc_balance = $this->encryption->encrypt($user_new_balance, $secret_key, $user_id);
                    
                    $serial_data = array(
                                           'charged' => 1,
                                           'sold'    => 1
                                       );
                    
                    $this->charge_with_code_model->update_serial_data($card_data->id, $serial_data);
                    
                    $balance_log_data = array(
                                                'user_id'           => $user_id,
                                                'payment_method_id' => $card_data->id,
                                                'code'              => '1',
                                                'balance'           => $user_new_balance,
                                                'amount'            => $card_amount,
                                                'balance_status_id' => '2',
                                                'ip_address'        => $this->input->ip_address(),
                                                'unix_time'         => time()
                                             );
                    
                    $this->charge_with_code_model->insert_balance_log($balance_log_data);
                    
                    $user_updated_data['user_balance'] = $enc_balance;
                    $this->charge_with_code_model->update_user_balance($user_id, $user_updated_data);
                    
                    
                    //get message vars
                    $balance_charged_with = $this->general_model->get_lang_var_translation('balance_charged_with',$lang_id);
                    $your_current_balance = $this->general_model->get_lang_var_translation('your_current_balance',$lang_id);
                    
                    $return_msg = $balance_charged_with." $card_amount ".$your_current_balance." $user_new_balance";
                    $output  = array(
                                       'message'  => $return_msg,
                                       'response' => 1
                                    );
                    //$output = array( 'message' => '1');
                }
                else
                {
                    //get message vars
                    $return_msg = $this->general_model->get_lang_var_translation('card_used_before',$lang_id);
                    $output  = array( 
                                        'message' => $return_msg,
                                        'response' => 0
                                    );
                }
            }
            else
            {
                //get message vars
                $return_msg = $this->general_model->get_lang_var_translation('no_data_about_this_card',$lang_id);
                $output  = array( 
                                    'message' => $return_msg,
                                    'response' => 0
                                );
            }
        }else{
            $output  = array( 
                                'message' => $fail_message,
                                'response' => 0
                            );
            //$output = array( 'message' => '0');
        }
        
        $this->output->set_content_type('application/json')->set_output(json_encode($output));
        
    }     
/************************************************************************/    
}