<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Login extends CI_Controller
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
        $this->load->library('shopping_cart');
        $this->load->library('encryption');

        $this->config->load('encryption_keys');

        require(APPPATH . 'libraries/PHPGangsta_GoogleAuthenticator.php');
    }

    public function index( )
    {

       $email          = strip_tags($this->input->post('email', TRUE));
       $password       = strip_tags($this->input->post('password', TRUE));
       $lang_id        = intval($this->input->post('langId', TRUE));
       $deviceId       = strip_tags($this->input->post('deviceId', TRUE));
       $country_id     = intval($this->input->post('countryId', TRUE));

       $this->form_validation->set_rules('email', ('email'),'required|valid_email');
       $this->form_validation->set_rules('password', ('password'), 'required');

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
           if($this->ion_auth->login($email, $password))
           {

                $user_data = $this->ion_auth->user()->row();

                $user_id = $user_data->id;

                $this->api_lib->check_user_store_country_id($email, $password, $user_id, $country_id);

                $this->session->set_userdata('is_mobile', true);

                //require(APPPATH . 'includes/front_end_global.php');

                // to override the old user_bootstrap for old 'Guest'
                //$this->user_bootstrap->reload_user_data();


                // reload data
                $user_data = $this->ion_auth->user()->row();

                /*************change shopping cart user_id*******************/
                $session_id = $deviceId;
                $ip_address = $this->input->ip_address();
                $lang_id    = $lang_id;

                $this->shopping_cart->set_user_data($user_id, $session_id, $ip_address, $country_id, $lang_id);

                $user_cart_id    = $this->shopping_cart_model->get_cart_id($user_id);
                $visitor_cart_id = $this->shopping_cart->get_guest_cart_id();

                if($visitor_cart_id != 0)
                {
                    $this->shopping_cart->convert_shopping_cart_user_id($visitor_cart_id, $user_cart_id);
                }

                /**********End convert shopping cart user id *************/

                //$user_data  = $this->user_bootstrap->get_user_data();

                if($user_data->account_sms_activated == 0)
                {
                    $message = $this->general_model->get_lang_var_translation('sms_activation_required', $lang_id);

                    $output  = array(
                                      'message'  => $message ,
                                      'response' => 3
                                   );
                }
                elseif($user_data->login_auth == 1)
                {
                    #Resend SMS code
                    $this->resend_user_sms_code($user_id, $lang_id);

                    $auth_data['login_auth_activated'] = 0;
                    $this->user_model->update_user($user_id, $auth_data);

                    $message = $this->general_model->get_lang_var_translation('sms_auth_required', $lang_id);

                    $output  = array(
                                      'message'  => $message ,
                                      'response' => 1
                                   );
                }
                elseif($user_data->login_auth == 2)
                {
                    $auth_data['login_auth_activated'] = 0;
                    $this->user_model->update_user($user_id, $auth_data);

                    $message = $this->general_model->get_lang_var_translation('google_auth_required', $lang_id);

                    $output  = array(
                                      'message'  => $message ,
                                      'response' => 2
                                   );
                }
                else
                {

                    $country_data = $this->general_model->get_nationality_data($user_data->Country_ID, $lang_id);
                    $user_city    = '';
                    $country      = '';
                    $country_code = '';
                    $user_phone   = $user_data->phone;

                    if(count($country_data) !=0)
                    {
                      $user_city      = $this->cities_model->get_city_name($user_data->city_id, $lang_id);

                      $country        = $country_data->name;
                      $country_code   = $country_data->calling_code;

                      $user_phone     = substr($user_data->phone, strlen($country_code)); // return user phone without country code
                    }
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


                    $all_banks_Accounts = array();
                    $country_info       = (object)array();

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
                         $cities_array = (object)array();
                     }


                    $country_info = array(
                                                'regCountryId'    => $user_data->id ,
                                                'regCountryName'  => $country       ,
                                                'regCountryKey'   => $country_code  ,
                                                'regCities'       => $cities_array
                                          );

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
                                        'userCredit'          => "$balance"                      ,
                                        'userRewardPoints'    => "$reward_points"                ,
                                        'userCountry'         => $country                      ,
                                        'userCountryId'       => $user_data->Country_ID        ,
                                        'userRegion'          => "$user_city"                    ,
                                        'userRegionId'        => $user_data->city_id           ,
                                        'userMailList'        => $user_data->mail_list         ,
                                        'userBankAccounts'    => $all_banks_Accounts           ,
                                        'countryInfo'         => $country_info
                                     );

                }

           }
           else
           {
                $message = $this->general_model->get_lang_var_translation('login_error', $lang_id);

                $output = array(
                                  'message'  => strip_tags($message) ,
                                  'response' => 0
                               );


           }
       }

        //***************LOG DATA***************//
       //insert log
       $this->api_lib->insert_log($user_id, current_url(), 'Login', 'IOS', $_POST, $output);
       //***************END LOG***************//
       
       $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));

    }

    public function resend_user_sms_code($user_id, $lang_id)
    {
        $user_id  = intval($user_id);
        $sms_code = rand(1000, 9999);

        $data = array(
            		     'sms_code' => $sms_code,
            		 );

        $this->ion_auth->update($user_id, $data);

        $user = $this->user_bootstrap->get_user_data();

        $this->load->library('notifications');
        $sms_activation_code_lang = $this->general_model->get_lang_var_translation('sms_activation_code', $lang_id);
        $msg = $sms_activation_code_lang.' : '.$sms_code;

        $this->notifications->send_sms($msg, $user->phone);


    }


/************************************************************************/
}
