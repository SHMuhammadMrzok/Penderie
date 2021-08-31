<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Get_profile_data extends CI_Controller
{
  
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model('get_profile_data_model');
        
    }

    public function index( )
    {   
       
       $lang_id        = intval(strip_tags($this->input->post('langId', TRUE)));
       $email          = strip_tags($this->input->post('email', TRUE));
       $password       = strip_tags($this->input->post('password', TRUE));
       
       $output         = array();
         
       if($this->ion_auth->login($email, $password))
       {
            $user = $this->ion_auth->user()->row();
            
            //-->> get user bank accounts and country
            $bank_accounts      = $this->get_profile_data_model->get_user_bank_accounts($lang_id ,$user->id);
            $country            = $this->get_profile_data_model->get_user_country($lang_id ,$user->Country_ID);
            
            $user               =(array)$user;
            $country            =(array)$country;
            
            $user  = array_merge($user, $country);
            
            $user_bank_accounts_data = array();
            
            if(!empty($user))
            {
                if(!empty($bank_accounts))
                {
                    foreach($bank_accounts as $account)
                    {
                        $user_bank_accounts_data [] = array(
                                                            'bankId'                 => $account->id,
                                                            'bankName'               => $account->bankName,
                                                            'userAccountName'        => $account->account_name,
                                                            'userAccountNumber'      => $account->account_number,
                                                           );
                    }
                    
                }
                else
                {
                    
                    $user_bank_accounts_data = '';
                }
                
                $output [] = array(
                                    'userId'            => $user['id']          ,
                                    'userFirstName'     => $user['first_name']  ,
                                    'userlastName'      => $user['last_name']   ,
                                    'userName'          => $user['username']    ,
                                    'userMail'          => $user['email']       ,
                                    'userMobile'        => $user['phone']       ,
                                    'userCountry'       => $user['country_name'],
                                    'userMailList'      => $user['mail_list']   ,
                                    'userBankAccounts'  => $user_bank_accounts_data
                                );
            }else{
                
                $output =array( 'message' => 'user not found'); 
           }
       }else{
        
        $output =array( 'message' => 'login error'); 
       
       }
       
       $this->output->set_content_type('application/json')->set_output(json_encode($output));
        
    }
       
     
/************************************************************************/    
}