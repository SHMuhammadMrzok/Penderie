<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Update_profile_data extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('update_profile_data_model');
        $this->load->model('login_model');
        $this->load->model('general_model');
        $this->load->model('users/cities_model');
        $this->load->model('registration_countries_model');
        $this->load->model('shopping_cart/user_bank_accounts_model');

        $this->load->library('api_lib');

        //require(APPPATH . 'includes/front_end_global.php');

    }

    public function index( )
    {
       $this->session->set_userdata('is_mobile', true);

       $lang_id                 = intval(strip_tags($this->input->post('langId', TRUE)));
       $userId                  = intval(strip_tags($this->input->post('userId', TRUE)));

       $email                   = strip_tags($this->input->post('userCurrentEmail', TRUE));
       $password                = strip_tags($this->input->post('userCurrentPassword', TRUE));
       $new_email               = strip_tags($this->input->post('email', TRUE));

       $newpassword             = strip_tags($this->input->post('password', TRUE));
       $userMobile              = strip_tags($this->input->post('userMobile', TRUE));
       $userCountry             = intval(strip_tags($this->input->post('regUserCountryId', TRUE)));
       $userRegion              = intval(strip_tags($this->input->post('regUserRegionId', TRUE)));
       $userMailList            = intval(strip_tags($this->input->post('userMailList', TRUE)));
       $userBankAccounts        = $this->input->post('userBankAccounts', TRUE);
       $store_country_id        = intval(strip_tags($this->input->post('storeCountryId', TRUE)));
       $social_id               = $this->input->post('socialId', TRUE);
       $social_type             = $this->input->post('socialType', TRUE);


       $fail_message    = $this->general_model->get_lang_var_translation('execution_fail',$lang_id);
       $success_message = $this->general_model->get_lang_var_translation('execution_success',$lang_id);
       $user_data = array();

       if(($social_type == 'twitter' || ($social_type == 'facebook')) && ($social_id != 0) )
       {
            $con_array = array(
                                'user_type' => $social_type,
                                $social_type.'_id' => $social_id,
                              );

            $user_data = $this->user_model->get_user_data_by_conditions($con_array);
       }
       else
       {
            if($this->ion_auth->login($email, $password))
            {
                $user_data  = $this->ion_auth->user()->row();
            }

       }

       if(count($user_data) != 0)
       {

            $this->api_lib->check_user_store_country_id($email, $password, $user_data->id, $store_country_id);

            //$user_data  = $this->ion_auth->user()->row();
            $old_phone  = $user_data->phone;

            $phone_lang     = $this->general_model->get_lang_var_translation('phone', $lang_id);
            $required_lang  = $this->general_model->get_lang_var_translation('required', $lang_id);
            $is_unique_lang = $this->general_model->get_lang_var_translation('is_unique', $lang_id);
            $country_lang   = $this->general_model->get_lang_var_translation('country', $lang_id);
            $email_lang     = $this->general_model->get_lang_var_translation('email', $lang_id);

            if($old_phone != $userMobile)
            {
                $this->form_validation->set_rules('userMobile', $phone_lang, 'trim|required|is_unique[users.phone]');
            }
            $this->form_validation->set_rules('regUserCountryId', $country_lang, 'trim|required');
            $this->form_validation->set_rules('userCurrentEmail', $email_lang, 'valid_email|required');

            $this->form_validation->set_message('required', $required_lang."  : %s ");
            $this->form_validation->set_message('is_unique', $is_unique_lang."  : %s ");
            $this->form_validation->set_error_delimiters('', '');

            if($this->form_validation->run() == FALSE)
            {
                $message = validation_errors();
                $output  = array(
                                    'message'  => $message,
                                    'response' => "0"
                                );
            }
            else
            {
                if(isset($userCountry) && $userCountry != '')
                {
                    $country = $userCountry;
                }
                else
                {
                    $country = $user_data->Country_ID;
                }

                //$city = $userRegion;

                $data = array(
                                      //'first_name'        => $userFirstName     ,
                                      //'last_name'         => $userLastName      ,
                                      'email'             => $email             ,
                                      'phone'             => $userMobile        ,
                                      'mail_list'         => $userMailList      ,
                                      'Country_ID'        => $userCountry       ,
                                      'city_id'           => $userRegion
                                    );

                if(isset( $_POST['password']) && $_POST['password']!= '')
                {
                    $data['password'] = $newpassword;
                }


                if($this->ion_auth->update($user_data->id, $data))
                {
                    $all_banks_Accounts = (object)array();
                    $country_info       = (object)array();

                    //-->>update user bank accounts

                   if(isset($userBankAccounts) && $userBankAccounts != '')
                   {
                        $userBankAccounts = json_decode($userBankAccounts);

                        $this->user_bank_accounts_model->delete_user_bank_accounts($user_data->id);

                        foreach($userBankAccounts as $account)
                        {
                            $user_bank_accounts_data  = array(
                                                                'user_id'         => $user_data->id                         ,
                                                                'bank_id'         => strip_tags($account->bankId)           ,
                                                                'account_name'    => strip_tags($account->userAccountName)  ,
                                                                'account_number'  => strip_tags($account->userAccountNumber)
                                                               );

                            $this->user_bank_accounts_model->insert_user_account_data($user_bank_accounts_data);
                        }
                    }
                    $send_user_data = true;

                    //-->>> Return user data
                    $user = $this->general_model->get_user_row($user_data->id);

                    //-->>> if update phone send verification sms
                    if($old_phone != $user->phone)
                    {
                        // to override the old user_bootstrap for old 'phone'
                        //$this->user_bootstrap->reload_user_data();
                        //$this->_phone_activate();
                        if($social_type != 'facebook' && $social_type != 'twitter')
                        {
                            $send_user_data = false;
                        }
                    }

                    if(!$send_user_data)
                    {
                        $activation_msg = $this->general_model->get_lang_var_translation('relogin_to_activate_account',$lang_id);
                        $output         = array(
                                                 'message'  => $activation_msg,
                                                 'response' => "2"
                                               );

                        //$this->resend_user_sms_code($user_data->id, $lang_id);

                    }
                    else
                    {
                        $user_data      = $this->user_model->get_row_data($user_data->id);

                        $country_data   = $this->general_model->get_nationality_data($user_data->Country_ID, $lang_id);
                        $user_city      = $this->cities_model->get_city_name($user_data->city_id, $lang_id);

                        $country        = $country_data->name;
                        $country_code   = $country_data->calling_code;

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

                        $user_phone  = substr($user_data->phone, strlen($country_code));
                        $secret_key  = $this->config->item('new_encryption_key');
                        $secret_iv   = $user_data->id;

                        if($user_data->user_balance != '')
                        {
                            $balance = $this->encryption->decrypt($user_data->user_balance, $secret_key, $secret_iv);
                        }
                        else
                        {
                            $balance = 0;
                        }

                        if($user_data->user_points != '')
                        {
                            $reward_points  = $this->encryption->decrypt($user_data->user_points, $secret_key, $secret_iv);
                        }
                        else
                        {
                            $reward_points  = 0;
                        }

                        if($user_city == false)
                        {
                            $user_city = "";
                        }
                        else
                        {
                            $user_city = $user_city;
                        }

                        $user_new_data[] = array(
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
                                            'userCountry'         => $country                      ,
                                            'userCountryId'       => $user_data->Country_ID        ,
                                            'userRegion'          => $user_city                    ,
                                            'userRegionId'        => $user_data->city_id           ,
                                            'userMailList'        => $user_data->mail_list         ,
                                            'userBankAccounts'    => $all_banks_Accounts           ,
                                            'countryInfo'         => $country_info
                                         );


                        $success_msg = $this->general_model->get_lang_var_translation('updated',$lang_id);
                        $output         = array(
                                                 'message'  => $success_msg,
                                                 'response' => "1",
                                                 'userData' => $user_new_data
                                               );
                    }

                }
                else
                {
                    $fail_message = $this->general_model->get_lang_var_translation('not_updated',$lang_id);
                    $output       = array(
                                             'message'  => $fail_message,
                                             'response' => "0"
                                          );
                }
            }

       }
       else
       {
            $fail_message = $this->general_model->get_lang_var_translation('login_error',$lang_id);
            $output       = array(
                                     'message'  => $fail_message,
                                     'response' => "0"
                                  );
       }

       $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));

    }

    private function _phone_activate()
    {
        //-->>Generate code
        $sms_code = rand(1000, 9999);

        $user = $this->user_bootstrap->get_user_data();

        $data = array(
            		   'sms_code'               => $sms_code,
            		   'account_sms_activated'  => 0
            	     );

        $this->ion_auth->update($user->id, $data);

        //-->>  send new sms_code
        $this->load->library('notifications');
        $this->notifications->send_sms ($sms_code, $user->phone);
        return true;
    }

    public function resend_user_sms_code($user_id, $lang_id)
    {
        $user_id  = intval($user_id);
        $sms_code = rand(1000, 9999);

        $data = array(
            		     'sms_code' => $sms_code,
                         'account_sms_activated'  => 0
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
