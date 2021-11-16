<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Confirmation_code extends CI_Controller
{
  
    public function __construct()
    {
        parent::__construct();
        
        $this->config->load('encryption_keys');
        
        $this->load->model('login_model');
        $this->load->model('general_model');
        $this->load->model('users/user_model');
        $this->load->model('users/countries_model');
        $this->load->model('users/cities_model');
        $this->load->model('registration_countries_model');
        
        $this->load->library('api_lib');
    }

    public function index( )
    { 
        $email                  = strip_tags($this->input->post('email', TRUE));
        $password               = strip_tags($this->input->post('password', TRUE));
        $lang_id                = strip_tags($this->input->post('langId', TRUE));
        $store_country_id       = strip_tags($this->input->post('storeCountryId', TRUE));
        
        //-->>$confirmationWay = 1 for sms confirmation
        //-->>$confirmationWay = 2 for  Google Auth confirmation
        
        $confirmationCode       = strip_tags($this->input->post('confirmationCode', TRUE));
        $confirmationWay        = strip_tags($this->input->post('confirmationWay', TRUE));
            
        $agent                  = strip_tags($this->input->post('agent', TRUE));
        $user_id                = 0;
        
        $output    = array();
            
        if($this->ion_auth->login($email, $password))
        {
                $user_data = $this->ion_auth->user()->row();
                $user_id   = $user_data->id;
                
                $this->api_lib->check_user_store_country_id($email, $password, $user_data->id, $store_country_id);
                //$user_data = $this->ion_auth->user()->row();
                
                //-->> Get user data
                //$user           = $this->user_model->get_user($lang_id, $user_id);
                $city_name      = $this->cities_model->get_city_name($user_data->city_id, $lang_id);
                $country_data   = $this->general_model->get_nationality_data($user_data->Country_ID, $lang_id);
                
                $country        = $country_data->name;
                $country_code   = $country_data->calling_code;
                
                $user_phone     = substr($user_data->phone, strlen($country_code)); // return user phone without country code
                
                $bank_accounts = $this->login_model->get_bank_accounts_result($lang_id, $user_id);
                
                $all_banks_Accounts  = array();
                
                if(count($bank_accounts) != 0)
                {
                    foreach($bank_accounts as $account)
                    {
                        if(isset($account->image) && $account->image != '')
                        {
                            $pic =  base_url().'assets/uploads/'.$account->image;
                        }else{
                        $pic = ''; 
                        }
                        
                        $all_banks_Accounts[] = array(
                                            'banckId'                     => $account->id,
                                            'bankName'                    => $account->bank,
                                            'bankImage'                   => $pic,
                                            'bankAccountName'             => (isset($account->bank_account_name))? $account->bank_account_name:'',
                                            'bankAccountNumber'           => (isset($account->bank_account_number))? $account->bank_account_number:'',
                                            'userAccountName'             => (isset($account->user_bank_account_name))? $account->user_bank_account_name:'',
                                            'userAccountNumber'           => (isset($account->user_bank_account_number))? $account->user_bank_account_number:'',
                                            );
                    }
                
                }
                
                $cities = $this->registration_countries_model->get_cities($lang_id, $user_data->Country_ID);
                    
                if(!empty($cities))
                {
                    foreach($cities as $city)
                    {
                        $cities_array [] = array(
                                                    'regCityId'           => $city->id,
                                                    'regCityName'         => $city->name,
                                                );
                    }
                    
                }
                else
                {
                    $cities_array = '';
                }
                
                $country_info = array(
                                                    'regCountryId'    => $user_data->id ,
                                                    'regCountryName'  => $country       ,
                                                    'regCountryKey'   => $country_code  ,
                                                    'regCities'       => $cities_array
                                            );
                
                $this->load->library('encryption');
                $secret_key   = $this->config->item('new_encryption_key');
                $secret_iv    = $user_id;
                
                $user_balance = $this->encryption->decrypt($user_data->user_balance, $secret_key , $secret_iv);
                
                if($user_data->user_points == '')
                {
                    $user_points = 0;
                }
                else
                {
                    $user_points  = $this->encryption->decrypt($user_data->user_points,$secret_key , $secret_iv);
                }
                
                if($confirmationWay == 1)
                {
                    //-->> compare sms code
                    if( $user_data->sms_code == $confirmationCode)
                    {
                        $data    = array('login_auth_activated' => 1);
                        $this->ion_auth->update($user_data->id, $data);
                        
                        
                        $country_data   = $this->general_model->get_nationality_data($user_data->Country_ID, $lang_id);
                        
                        $country        = $country_data->name;
                        $country_code   = $country_data->calling_code;
                        
                        $user_phone     = substr($user_data->phone, strlen($country_code)); // return user phone without country code
                        
                        $output[] = array(
                                            'userId'              => $user_data->id,
                                            'userFirstName'       => $user_data->first_name,
                                            'userlastName'        => $user_data->last_name,
                                            'userMail'            => $user_data->email,
                                            'countryCode'         => $country_code                 , 
                                            'userMobile'          => $user_phone                   ,
                                            'userCustomerGroupId' => $user_data->customer_group_id ,
                                            'userCountry'       => $country,
                                            'userCountryId'     => $user_data->Country_ID,
                                            'userRegion'        => $city_name,
                                            'userRegionId'      => $user_data->city_id,
                                            'userCredit'        => $user_balance,
                                            'userRewardPoints'  => $user_points,
                                            'userMailList'      => $user_data->mail_list,
                                            'userBankAccounts'  => $all_banks_Accounts,
                                        );
                        
                    }
                    else
                    {    
                        $error_msg = $this->general_model->get_lang_var_translation('sms_code_error', $lang_id); 
                        $output = array(
                                        'message'  => $error_msg,
                                        'response' => 0
                                        ); 
                    }
                }
                elseif($confirmationWay == 2)
                {
                    //-->> compare Google Auth code  
                    $ga                         = new PHPGangsta_GoogleAuthenticator();
                                
                    $google_auth_code           = $confirmationCode; 
                    $checkResult = $ga->verifyCode($user_data->google_auth_secret_key, $google_auth_code ,2 ); 
                    
                    if ($checkResult) 
                    {
                        $data    = array('login_auth_activated' => 1);
                        $this->ion_auth->update($user_data->id, $data);
                        
                        $output[] = array(
                                            'userId'            => $user_data->id,
                                            'userFirstName'     => $user_data->first_name,
                                            'userlastName'      => $user_data->last_name,
                                            'userMail'          => $user_data->email,
                                            'countryCode'         => $country_code                 , 
                                            'userMobile'          => $user_phone                   ,
                                            'userCustomerGroupId' => $user_data->customer_group_id ,
                                            'userCountry'       => $country_name,
                                            'userCountryId'     => $user_data->Country_ID,
                                            'userRegion'        => $city_name,
                                            'userRegionId'      => $user_data->city_id,
                                            'userCredit'        => $user_balance,
                                            'userRewardPoints'  => $user_points,
                                            'userMailList'      => $user_data->mail_list,
                                            'userBankAccounts'  => $all_banks_Accounts,
                                        );
                                        
                    } 
                    else 
                    {
                        $error_msg = $this->general_model->get_lang_var_translation('google_code_error', $lang_id);
                        $output    = array(
                                            'message' => $error_msg,
                                            'response' => 0
                                        ); 
                    }
                }
                elseif($confirmationWay == 3)
                { 
                    if($confirmationCode == $user_data->sms_code)
                    {
                        $data = array(
                                        'login_auth_activated'  => 1,
                                        'account_sms_activated' => 1
                                    );
                            
                        $this->ion_auth->update($user_data->id, $data);
                        
                        
                        $output[] = array(
                                            'userId'                => $user_data->id,
                                            'userFirstName'         => $user_data->first_name,
                                            'userlastName'          => $user_data->last_name,
                                            'userMail'              => $user_data->email,
                                            'countryCode'           => $country_code                 , 
                                            'userMobile'            => $user_phone                   ,
                                            'userCustomerGroupId'   => $user_data->customer_group_id ,
                                            'userCountry'           => $country,
                                            'userCountryId'         => $user_data->Country_ID,
                                            'userRegion'            => $city_name,
                                            'userRegionId'          => $user_data->city_id,
                                            'userCredit'            => $user_balance,
                                            'userRewardPoints'      => $user_points,
                                            'userMailList'          => $user_data->mail_list,
                                            'userBankAccounts'      => $all_banks_Accounts,
                                            'countryInfo'           => $country_info
                                        );
                    }
                    else
                    {
                        $error_msg = $this->general_model->get_lang_var_translation('sms_code_error', $lang_id);
                        $output    = array( 
                                            'message'  => $error_msg,
                                            'response' => 0
                                        ); 
                    }
                }
        
        }
        else
        { 
            $fail_message = $this->ion_auth->errors();//$this->general_model->get_lang_var_translation('login_error', $lang_id);
            $output = array( 'message' => $fail_message); //$this->ion_auth->errors()
        }
       
        //***************LOG DATA***************//
        //insert log
        $this->api_lib->insert_log($user_id, current_url(), 'Confirmation Code', $agent, $_POST, $output);
        //***************END LOG***************//

        $this->output->set_content_type('application/json')->set_output(json_encode($output));
        
    }
    
    public function send_code()
    {
        $email      = strip_tags($this->input->post('email', true));
        $password   = strip_tags($this->input->post('password', true));
        $lang_id    = intval($this->input->post('langId', true));
        
        if($this->ion_auth->login($email, $password))
        {
            $user_data = $this->ion_auth->user()->row();
            
            if($user_data->account_sms_activated == 0)
            {
                if($user_data->account_code_times < 5)
                {
                    //send code
                    $this->load->library('notifications');
                    $sms_activation_code_lang = $this->general_model->get_lang_var_translation('sms_activation_code', $lang_id);
                    $msg = $sms_activation_code_lang.' : '.$user_data->sms_code;
                    
                    $this->notifications->send_sms($msg, $user->phone);
                    
                    $user_id = $user_data->id;
                    $data = array(
                                    'account_code_times' => $user_data->account_code_times +1
                                  );
                    
                    $this->ion_auth->update($user_id, $data);
                    
                    $success_msg = $this->general_model->get_lang_var_translation('send_sms_successfully', $lang_id);
                    
                    $output = array(
                                    'response'  => 1,
                                    'message'   => $success_msg
                                    
                                    );
                }
                else
                {
                    // exceeded trials
                    $fail_message = $this->general_model->get_lang_var_translation('exceeded_send_codes_trials', $lang_id);
                    $output = array(
                                    'response' => 0,
                                    'message' => $fail_message
                                   );
                }
            }
            else
            {
                //account already activated
                $fail_message = $this->general_model->get_lang_var_translation('account_already_activated', $lang_id);
                $output = array(
                                'response' => 0,
                                'message' => $fail_message
                               );
            }
        }
        else
        { 
          $fail_message = $this->ion_auth->errors();//$this->general_model->get_lang_var_translation('login_error', $lang_id);
          $output = array( 
                            'response' => 0,
                            'message'  => strip_tags($fail_message)
                          ); //$this->ion_auth->errors()
        }
        
        $this->output->set_content_type('application/json')->set_output(json_encode($output));
    }
    
    
       
     
/************************************************************************/    
}