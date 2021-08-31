<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Register extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('global_model');
        $this->load->model('general_model');
        $this->load->model('users/user_model');
        $this->load->model('users/cities_model');
        $this->load->model('registration_countries_model');

        $this->load->library('notifications');
        $this->load->library('api_lib');

    }

    public function index( )
    {

        $message = '';

        $settings         = $this->global_model->get_config();
        $email            = strip_tags($this->input->post('email', TRUE));
        $mobile           = strip_tags($this->input->post('mobile', TRUE));
        $lang_id          = intval($this->input->post('langId', TRUE));

        $firstName        = strip_tags($this->input->post('firstName', TRUE));
        $lastName         = strip_tags($this->input->post('lastName', TRUE));
        $countryId        = intval($this->input->post('countryId', TRUE));
        $regionId         = intval($this->input->post('regionId', TRUE));
        $password         = strip_tags($this->input->post('password', TRUE));
        $mailList         = intval($this->input->post('mailList', TRUE));
        $deviceId         = strip_tags($this->input->post('deviceId', TRUE));
        $store_country_id = intval($this->input->post('storeCountryId', TRUE));

        $check_user_count = $this->user_model->check_if_user_regestered($email, $mobile);

       if($check_user_count == 0)
       {
           $email_unique = '|'.'is_unique[users.email]';
           $phone_unique = '|'.'callback_check_phone';
       }

       $first_name_lang         = $this->general_model->get_lang_var_translation('first_name', $lang_id);
       $last_name_lang          = $this->general_model->get_lang_var_translation('last_name', $lang_id);
       $email_lang              = $this->general_model->get_lang_var_translation('email', $lang_id);
       $phone_lang              = $this->general_model->get_lang_var_translation('phone', $lang_id);
       $country_lang            = $this->general_model->get_lang_var_translation('country', $lang_id);
       $password_lang           = $this->general_model->get_lang_var_translation('password', $lang_id);
       $confirm_password_lang   = $this->general_model->get_lang_var_translation('confirm_password', $lang_id);
       //$checkbox_lang           = $this->general_model->get_lang_var_translation('checkbox', $lang_id);
       $required_lang           = $this->general_model->get_lang_var_translation('required', $lang_id);
       $is_unique_lang          = $this->general_model->get_lang_var_translation('is_unique', $lang_id);
       $is_integer_lang         = $this->general_model->get_lang_var_translation('is_integer', $lang_id);
       $valid_email_lang        = $this->general_model->get_lang_var_translation('valid_email', $lang_id);
       $password_not_match      = $this->general_model->get_lang_var_translation('password_not_match', $lang_id);
       
       $this->form_validation->set_rules('firstName', $first_name_lang, 'required');
       //$this->form_validation->set_rules('lastName', $last_name_lang, 'required');
       $this->form_validation->set_rules('email', $email_lang, "required|valid_email" . $email_unique);
       $this->form_validation->set_rules('mobile', $phone_lang, "required|integer" . $phone_unique);
       $this->form_validation->set_rules('countryId', $country_lang, 'required');
       $this->form_validation->set_rules('password', $password_lang, 'required|matches[confirmPassword]|min_length[6]|max_length[12]');
       $this->form_validation->set_rules('confirmPassword', $confirm_password_lang, 'required');
       //$this->form_validation->set_rules('checkbox', $checkbox_lang, 'required');


       $this->form_validation->set_message('required', $required_lang."  : %s ");
       $this->form_validation->set_message('is_unique', $is_unique_lang."  : %s ");
       $this->form_validation->set_message('integer', $is_integer_lang."  : %s ");
       $this->form_validation->set_message('valid_email', $valid_email_lang);
       $this->form_validation->set_message('matches', $password_not_match);
       
       $this->form_validation->set_error_delimiters('', '');

       if($this->form_validation->run() == FALSE)
       {
           $message = validation_errors();
           $output = array(
                                'message'  => $message,
                                'response' => '0'
                              );
       }
       else
       {
        /*
           $country      = $this->cities_model->get_country_call_code($countryId);
           $country_name = $country->name;
           $calling_code = $country->calling_code;

          */
           $country   = $this->general_model->get_nationality_data($countryId, $lang_id);
           $country_name   = $country->name;
           $calling_code   = $country->calling_code;


           $user_phone   = substr($mobile, strlen($calling_code));//$calling_code.$phone;

           //echo $user_phone[0];die();
           if($user_phone[0] == 0)
           {
                $user_phone  = substr($user_phone, 1);
                $user_mobile = $calling_code.$user_phone;
           }
           else
           {
                $user_mobile = $mobile;
           }

           $userName    = $firstName;

           $new_user_customer_group_id = $settings->new_user_customer_group_id;


           $additional_data = array(
                                      'first_name'          => $firstName ,
                                      'last_name'           => $lastName  ,
                                      'phone'               => $user_mobile ,
                                      'mail_list'           => $mailList  ,
                                      'Country_ID'          => $countryId ,
                                      'city_id'             => $regionId  ,
                                      'store_country_id'    => $store_country_id,
                                      'customer_group_id'   => $new_user_customer_group_id ,
                                      'active'              => 1   ,
                                      'login_auth_activated' => 1,
                                      'account_sms_activated' => 1
                                   );

           $group  = array('id'=>2);

           // if user not exist
           if($check_user_count == 0)
           {
               $id = $this->ion_auth->register($userName, $password, $email, $additional_data,$group);

              //-->>Send notification
              $this->_send_new_registered_notification($id, $lang_id);

              $message = $this->general_model->get_lang_var_translation('successfully_registered', $lang_id);

              $user_city = $this->cities_model->get_city_name($regionId, $lang_id);
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
                                                'regCountryId'    => $id ,
                                                'regCountryName'  => $country_name       ,
                                                'regCountryKey'   => $calling_code  ,
                                                'regCities'       => $cities_array
                                          );
              $cities = $this->registration_countries_model->get_cities($lang_id, $countryId);



              $user_new_data[] = array(
                                        'userId'              => $id                ,
                                        'userFirstName'       => $firstName        ,
                                        'userlastName'        => $lastName         ,
                                        'userMail'            => $email             ,
                                        'password'            => ''                            ,
                                        'passwordConfirm'     => ''                            ,
                                        'countryCode'         => $calling_code                 ,
                                        'userMobile'          => $user_mobile                   ,
                                        'userCustomerGroupId' => 2 ,
                                        'userCredit'          => 0                      ,
                                        'userRewardPoints'    => 0                ,
                                        'userCountry'         => $country_name                      ,
                                        'userCountryId'       => $countryId        ,
                                        'userRegion'          => $user_city        ,
                                        'userRegionId'        => $regionId           ,
                                        'userMailList'        => $mailList         ,
                                        'userBankAccounts'    => array()           ,
                                        'countryInfo'         => $country_info
                                     );

              $output = array(
                                'message'  => $message,
                                'response' => 1,
                                'userData' => $user_new_data
                              );
           }
           else
           {
               $user    = $this->user_model->get_first_registered_user($email, $mobile);
               $user_id = $user->id;

               $updated_data = array(
                                        'username'          => $userName        ,
                                        'password'          => $password        ,
                                        'first_name'        => $firstName       ,
                                        'last_name'         => $lastName        ,
                                        'phone'             => $mobile          ,
                                        'mail_list'         => $mailList        ,
                                        'Country_ID'        => $countryId       ,
                                        'city_id'           => $regionId        ,
                                        'first_order'       => 0                ,
                                        'store_country_id'  => $store_country_id,
                                        'customer_group_id' => $new_user_customer_group_id
                                     );

               $this->ion_auth->update($user_id, $updated_data);

               $this->_send_second_register_notifications($user_id, $user->email, $lang_id);

               $message = $this->general_model->get_lang_var_translation('email_activation_sent', $lang_id);

               $output = array(
                                'message'  => $message,
                                'response' => 1
                              );

           }

       }



      $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));
    }

    private function _send_new_registered_notification($id, $lang_id)
    {
        $user       = $this->user_model->get_row_data($id);
        $data       = array(
                               'username'   => $user->first_name.' '.$user->last_name   ,
                               'user_id'    => $user->id                                ,
                               'email'      => $user->email                             ,
                               'phone'      => $user->phone                             ,
                               'ip_address' => $user->ip_address                        ,
                               'join_date'  => date('Y/m/d', $user->created_on)         ,
                               'logo_path'  => base_url().'assets/template/admin/img/logo.png'
                           );

        $emails[] = $user->email;
        $phone    = $user->phone;

        $this->notifications->create_notification('new_user_registered', $data, $emails, $phone);

        //$this->sms_activate_login($id, $lang_id);
    }

    private function _send_second_register_notifications($user_id, $email, $lang_id)
    {
        //-->> make activation code
        $deactivate = $this->ion_auth_model->deactivate($user_id);


    	$activation_code = $this->ion_auth_model->activation_code;
		$identity        = $this->config->item('identity', 'ion_auth');
		$user            = $this->ion_auth_model->user($user_id)->row();

		$data = array(
        				'identity'   => $user->{$identity},
        				'id'         => $user_id,
        				'email'      => $email,
        				'activation' => $activation_code,
        			 );

        //-->>>send email
        $from_mail = $this->config->item('sender_email');

        $message   = $this->load->view($this->config->item('email_templates', 'ion_auth').$this->config->item('email_activate', 'ion_auth'), $data, true);

		$this->email->clear();
		$this->email->from($from_mail, $this->config->item('site_title', 'ion_auth'));
		$this->email->to($email);
		$this->email->subject($this->config->item('site_title', 'ion_auth') . ' - ' . $this->lang->line('email_activation_subject'));
		$this->email->message($message);

        $this->email->send();

        //$this->sms_activate_login($user_id, $lang_id);
    }

    public function sms_activate_login($user_id, $lang_id)
    {
        //-->>Generate code
        $sms_code = rand(1000, 9999);

        $user = $this->user_model->get_row_data($user_id);

        $data = array(
            		   'sms_code'              => $sms_code,
                       'account_sms_activated' => 0
            		 );

        $this->ion_auth->update($user_id, $data);

        $sms_activation_code_lang = $this->general_model->get_lang_var_translation('sms_activation_code', $lang_id);
        $msg = $sms_activation_code_lang.' : '.$sms_code;
        //-->>  send new sms_code
        if( $this->notifications->send_sms($msg ,$user->phone))
        {
            return true;
        }
        else
        {
           return false ;
        }
    }

    public function check_phone($phone)
    {
        $country_id   = intval($this->input->post('countryId', TRUE));
        $country      = $this->cities_model->get_country_call_code($country_id);

        $calling_code = $country->calling_code;
        $user_phone   = $calling_code.$phone;

        $user_phone_exist              = $this->user_model->check_user_phone_exist($phone);
        $phone_with_calling_code_exist = $this->user_model->check_user_phone_exist($user_phone);

        if($user_phone_exist || $phone_with_calling_code_exist)
        {
            $lang_id = $this->input->post('langId');
            $unique_lang = $this->general_model->get_lang_var_translation('is_unique', $lang_id);
            $this->form_validation->set_message('check_phone', $unique_lang."  : %s ");

            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }


/************************************************************************/
}
