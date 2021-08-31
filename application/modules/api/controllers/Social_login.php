<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Social_login extends CI_Controller
{
  
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model('general_model');
        $this->load->model('login_model');
        $this->load->model('users/cities_model');
        $this->load->model('users/user_model');
        $this->load->model('registration_countries_model');
        
        $this->load->library('api_lib');
          
    }

    public function index( )
    {
       
       $email          = strip_tags($this->input->post('email', TRUE));
       $lang_id        = intval(strip_tags($this->input->post('langId', TRUE)));
       $deviceId       = strip_tags($this->input->post('deviceId', TRUE));
       $country_id     = intval(strip_tags($this->input->post('countryId', TRUE)));   
       
       $user_name      = strip_tags($this->input->post('userName', TRUE));
       $social_id      = strip_tags($this->input->post('socialId', TRUE));
       $social_json    = strip_tags($this->input->post('socialJson', TRUE));
       $type           = strip_tags($this->input->post('loginType', true));
       
       $this->form_validation->set_rules('socialId', ('socialId'),'required');
       $this->form_validation->set_rules('socialJson', ('socialJson'),'required');
       $this->form_validation->set_rules('loginType', ('loginType'),'required');
       
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
            /**
             * LOGIN TYPES:
             * facebook
             * twitter
             * system
             */
             
           // check if user has membership
           $user_exist = $this->user_model->check_user_exist($social_id, $type, $email);
           
           if($user_exist)
           {    
                $all_banks_Accounts = array();
                $country_info       = array();
                
                //return user data 
                $user_data = $this->user_model->get_user_by_social($social_id, $type, $email);
                
                //update user type and password
                $user_new_data = array(
                                        'user_type' => $type,
                                        'password'  => $social_id,
                                        $type.'_id' => $social_id
                                      );
                
                $this->ion_auth->update($user_data->id, $user_new_data);
                
                $country_data   = $this->general_model->get_nationality_data($user_data->Country_ID, $lang_id);
                $user_city      = $this->cities_model->get_city_name($user_data->city_id, $lang_id);
                
                if(count($country_data) != 0)
                {
                    $country        = $country_data->name;
                    $country_code   = $country_data->calling_code;
                }
                else
                {
                    $country = '';
                    $country_code = 0;
                }
                
                
                $user_phone     = substr($user_data->phone, strlen($country_code)); // return user phone without country code 
                
                $secret_key     = $this->config->item('new_encryption_key');
                $secret_iv      = $user_data->id;
                
                if($user_data->user_balance != '')
                {
                    $balance    = $this->encryption->decrypt($user_data->user_balance, $secret_key, $secret_iv);
                }
                else
                {
                    $balance    = 0;
                }
                
                if($user_data->user_points != '')
                {
                    $reward_points  = $this->encryption->decrypt($user_data->user_points, $secret_key, $secret_iv);
                }
                else
                {
                    $reward_points  = 0;
                }
                
                $bank_accounts      = $this->login_model->get_bank_accounts_result($lang_id, $user_data->id);
            
            
                if(count($bank_accounts) != 0)
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
                        
                        $all_banks_Accounts[]  = array(
                                            'bankId'             => $account->id,
                                            'bankName'           => $account->bank,
                                            'bankImage'          => $pic,
                                            'bankAccountName'    => (isset($account->bank_account_name))? $account->bank_account_name:'',
                                            'bankAccountNumber'  => (isset($account->bank_account_number))? $account->bank_account_number:'',
                                            'userAccountName'    => (isset($account->user_bank_account_name))? $account->user_bank_account_name:'',
                                            'userAccountNumber'  => (isset($account->user_bank_account_number))? $account->user_bank_account_number:'',
                                            );
                    }
                
                }
                else
                {
                    $all_banks_Accounts = (object)array();
                }
                
                if(!$user_city)
                {
                    $user_city = $this->general_model->get_lang_var_translation('no_cities_found', $lang_id);
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
                
                $mising_fields = false;
                if($user_data->email=='' || $country_code==0 || $user_phone=='' || $user_data->Country_ID==0 )
                {
                    $mising_fields = true;
                }
                
                $output[] = array(
                                        'userId'              => $user_data->id                ,
                                        'userFirstName'       => $user_data->first_name        ,  
                                        'userlastName'        => $user_data->last_name         ,
                                        'userMail'            => $user_data->email             ,
                                        'password'            => ''                            ,
                                        'passwordConfirm'     => ''                            ,
                                        'countryCode'         => $country_code                 , 
                                        'userMobile'          => $user_phone                   ,
                                        'userCustomerGroupId' => $user_data->customer_group_id ,
                                        'userCredit'          => $balance                      ,
                                        'userRewardPoints'    => $reward_points                ,
                                        'userCountry'         => $country_id                   ,
                                        'userCountryId'       => $user_data->Country_ID        ,
                                        'userRegion'          => $user_city                    ,
                                        'userRegionId'        => $user_data->city_id           ,
                                        'userMailList'        => $user_data->mail_list         ,
                                        'userBankAccounts'    => $all_banks_Accounts           ,
                                        'countryInfo'         => $country_info                  ,
                                        'missingFields'       => $mising_fields
                                     );
                
           }
           else
           {
                // create new user
                $group  = array('id'=>2);
                $settings         = $this->global_model->get_config();
                $new_user_customer_group_id = $settings->new_user_customer_group_id;
                
                $additional_data = array(
                                        'first_name'    => $user_name   ,
                                        'last_name'     => $user_name   ,
                                        'username'      => $user_name   ,
                                        'email'         => $email       ,
                                        'pssword'       => $social_id   ,
                                        'user_type'     => $type        ,
                                        $type.'_id'     => $social_id   ,
                                        'social_json'   => $social_json ,
                                        'active'        => 1            ,
                                        'customer_group_id'     => $new_user_customer_group_id,
                                        'account_sms_activated' => 1
                                        
                                      );
                
                $id = $this->ion_auth->register($user_name, $social_id, $email, $additional_data, $group);
                
                $output[] = array(
                                            'userId'              => $id        ,
                                            'userFirstName'       => $user_name ,  
                                            'userlastName'        => $user_name ,
                                            'userMail'            => $email     ,
                                            'password'            => '' ,
                                            'passwordConfirm'     => '' ,
                                            'countryCode'         => '' , 
                                            'userMobile'          => '' ,
                                            'userCustomerGroupId' => $new_user_customer_group_id ,
                                            'userCredit'          => '' ,
                                            'userRewardPoints'    => '' ,
                                            'userCountry'         => $country_id ,
                                            'userCountryId'       => '' ,
                                            'userRegion'          => '' ,
                                            'userRegionId'        => '' ,
                                            'userMailList'        => '' ,
                                            'userBankAccounts'    => '' ,
                                            'countryInfo'         => '' ,
                                            'missingFields'       => true
                                         );
                        
                
           }   
           
        }
        
        
      $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE)); 
    }
       
     
/************************************************************************/    
}