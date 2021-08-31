<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Bank_accounts extends CI_Controller
{
  
    public function __construct()
    {
        parent::__construct();
        
        $this->load->library('api_lib');
        
        $this->load->model('bank_accounts_model');
        $this->load->model('general_model');
        
    }

    public function index()
    {   
        $lang_id            = strip_tags($this->input->post('langId', TRUE));
        $email              = strip_tags($this->input->post('email', TRUE));
        $password           = strip_tags($this->input->post('password', TRUE));
        $deviceId           = strip_tags($this->input->post('deviceId', TRUE));
        $store_country_id   = strip_tags($this->input->post('storeCountryId', TRUE));
        
        
        $output    = array();
        
        $fail_message = $this->general_model->get_lang_var_translation('execution_fail',$lang_id);
        $success_message = $this->general_model->get_lang_var_translation('execution_success',$lang_id);
         
       if($this->ion_auth->login($email, $password))
       {
            $user    = $this->ion_auth->user()->row();
            $user_id = $user->id;
            
            $this->api_lib->check_user_store_country_id($email, $password, $user_id, $store_country_id);
       }
       else
       {
            $user_id = 0;
       }
            $bank_accounts      = $this->bank_accounts_model->get_bank_accounts_result($lang_id, $user_id);
            
            if(isset($bank_accounts) && !empty($bank_accounts))
            {
                    foreach($bank_accounts as $account)
                    {
                        if(isset($account->image) && $account->image != '')
                        {
                            $pic =  base_url().'assets/uploads/'.$account->image;
                        }
                        else
                        {
                           $pic = ''; 
                        }
                        
                        $image_path = realpath(APPPATH. '../assets/uploads/'.$account->image);
                        //$image_code = $this->api_lib->get_image_code($image_path);
                        
                        $output [] = array(
                                            'bankId'                      => $account->id,
                                            'bankName'                    => $account->bank,
                                            'bankImage'                   => $pic,
                                            'bankAccountName'             => (isset($account->bank_account_name))? $account->bank_account_name:'',
                                            'bankAccountNumber'           => (isset($account->bank_account_number))? $account->bank_account_number:'',
                                            'userAccountName'             => (isset($account->user_bank_account_name))? $account->user_bank_account_name:'',
                                            'userAccountNumber'           => (isset($account->user_bank_account_number))? $account->user_bank_account_number:'',
                                            //'imageBitMap'                 => $image_code
                                            );
                    }
                
            }
            else
            {
                $output  = array( 'message' => $fail_message);  
            }
             
       $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));        
    }       
     
/************************************************************************/    
}