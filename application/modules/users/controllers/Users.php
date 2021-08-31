<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Users extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        require(APPPATH . 'includes/front_end_global.php');
        require(APPPATH . 'libraries/PHPGangsta_GoogleAuthenticator.php');

        $this->load->library('shopping_cart');
        $this->load->library('notifications');

        $this->load->model('front_end_users_model');
        $this->load->model('users/countries_model');
        $this->load->model('cities_model');
        $this->load->model('users/users_model');
        $this->load->model('shopping_cart_model');

        $this->load->model('coupon_codes/coupon_codes_model');
        $this->load->model('shopping_cart/user_bank_accounts_model');

        if($this->config->item('images_source') == 'amazon')
        {
          $images_path = "https://".$this->config->item('amazon_s3_my_bucket').".s3.".$this->config->item('amazon_s3_region').".amazonaws.com/".$this->config->item('amazon_s3_subfolder');
          //https://sbmcart.s3.eu-west-2.amazonaws.com/qhwastore/54e62-2019-10-19.png
        }
        else
        {
          $images_path = base_url().'assets/uploads/';
        }
        $this->data['images_path'] = $images_path;
    }

    public function login()
    {
        //insert log
        $this->visits_log->add_log(1, 91, 316, $this->data['user_id']);

        $this->load->model('root/lang_model');
        $data['structure_languages']= $this->lang_model->get_active_structure_languages();

        $this->form_validation->set_rules('email', lang('email'),'required|valid_email');
        $this->form_validation->set_rules('password', lang('password'), 'required');

        if ($this->form_validation->run() == true)
        {
            $remember = (bool)strip_tags($this->input->post('remember', TRUE));
            $email    = strip_tags($this->input->post('email', true));
            $password = strip_tags($this->input->post('password', true));

            if ($this->ion_auth->login($email, $password, $remember))
            {
                // to override the old user_bootstrap for old 'Guest'
                $this->user_bootstrap->reload_user_data();
                $user_data = $this->user_bootstrap->get_user_data();

                $this->data['country_id'] = $user_data->store_country_id;


                $user_id = $this->user_bootstrap->get_user_id();

                /*************change shopping cart user_id*******************/
                $session_id = session_id();
                $ip_address = $this->input->ip_address();

                $country_id = $this->data['country_id'];

                $lang_id    = $this->data['lang_id'];

                $this->shopping_cart->set_user_data($user_id, $session_id, $ip_address, $country_id, $lang_id);

                $user_cart_id    = $this->shopping_cart_model->get_cart_id($user_id);
                $visitor_cart_id = $this->shopping_cart->get_guest_cart_id();

                if($visitor_cart_id != 0)
                {
                    $visitor_cart_products_exist = $this->shopping_cart_model->check_cart_products_count($visitor_cart_id);

                    if($visitor_cart_products_exist)
                    {
                        $this->shopping_cart->convert_shopping_cart_user_id($visitor_cart_id, $user_cart_id);
                    }
                }


                /************google OR sms login auth deactivate**************/

                if(in_array($user_data->login_auth, array(1, 2)))
                {
                    $data_login = array();

                    $data_login['login_auth_activated'] = 0;

                    $this->user_model->update_user($user_id, $data_login);

                    if($user_data->login_auth == 1)
                    {
                        $this->_generate_sms_code($user_data->id);
                    }
                }
                /*******************************************************/
                /*******************************************************/
                //-->>if the login is successful redirect  to dashboard
                if(isset($_SESSION['site_redir']))
                {
                   redirect($_SESSION['site_redir'], 'refresh');
                }
                else
                {
                   $_SESSION['message'] = $this->ion_auth->messages();
                   $this->session->mark_as_flash('message');

                   redirect(base_url(),'refresh');
                }
            }
            else
            {
                // incorrect login event
                $this->_incorect_login_event($email, $password);

                //-->>if the login was un-successful redirect  to the login page
                $data['login_error'] = $this->ion_auth->errors();

                $_SESSION['login_error'] = $this->ion_auth->errors();
                $this->session->mark_as_flash('login_error');
                $this->data['hide_menu'] = true;


                $this->data['content'] = $this->load->view('user_login', $this->data, true);
                $this->load->view('site/main_frame',$this->data);

                //redirect(base_url(). 'User_login', 'refresh');
                //use redirects instead of loading views for compatibility with MY_Controller libraries
            }
        }
        else
        {

           $_SESSION['login_error'] = lang('enter_reqired');
           $this->session->mark_as_flash('login_error');

           redirect(base_url().'User_login', 'refresh');
        }//if validation
    }

    private function _incorect_login_event($email, $password)
    {
        $ip_address = $this->input->ip_address();
        $agent      = $_SERVER['HTTP_USER_AGENT'];

        $login_data = array(
                            'email'         => $email       ,
                            'password'      => $password    ,
                            'ip_address'    => $ip_address  ,
                            'agent'         => $agent       ,
                            'unix_time'     => time()
                           );

        $this->users_model->insert_incorrect_login_attempt($login_data);

        $emails[]      = $this->config->item('incorrect_login_email');

        $template_data = array(
                                'logo_path'     => base_url().'assets/template/site/img/white_logo.png',
                                'unix_time'     => date('Y/m/d H:i', time()),
                                'email'         => $email                   ,
                                'password'      => $password                ,
                                'agent'         => $agent                   ,
                                'ip_address'    => $ip_address              ,
                                'year'          => date('Y')
                              );

        $this->notifications->create_notification('incorect_login', $template_data, $emails);
    }

    public function google_auth_form()
    {
        $this->data['content'] = $this->load->view('google_auth_form', '', true);
        $this->load->view('site/main_frame',$this->data);
    }

    public function google_auth_verify()
    {

        if(isset($_POST['remember'])&& $_POST['remember'] != 0)
        {

            $cookie = array(
                            'name'   => 'google_auth_remember',
                            'value'  => '1',
                            'expire' => '2592000'
                        );
            $this->input->set_cookie($cookie);

        }
        $ga                = new PHPGangsta_GoogleAuthenticator();
        $user              = $this->user_bootstrap->get_user_data();
        $google_auth_code  = strip_tags($this->input->post('google_auth_code', TRUE));

        //-->>create cookie for this person
        $checkResult = $ga->verifyCode($user->google_auth_secret_key, $google_auth_code ,2 ); //$google_auth_code

        if($checkResult)
        {
            $_SESSION['message'] = 'successfully code';

            $this->session->mark_as_flash('message');
        }
        else
        {
            $_SESSION['error_message'] = lang('code_not_match');

            $this->session->mark_as_flash('error_message');
        }

        if($_SESSION['site_redir'])
        {
            redirect($_SESSION['site_redir'], 'refresh');
        }
        else
        {
           redirect(base_url(), 'refresh');
        }
    }

    public function resend_sms_code()
    {
        $user = $this->user_bootstrap->get_user_data();


        $message = lang('sms_activation_code').' '.$user->sms_code;
        $this->notifications->send_sms ($message, $user->phone);

        $_SESSION['message'] = lang('message_send');
        $this->session->mark_as_flash('message');

        redirect('users/register/activate_user_phone', 'refresh');
    }

    public function user_login()
    {
        if ($this->ion_auth->logged_in())
        {
            $_SESSION['not_allow'] = lang('you_are_logged_in');
            $this->session->mark_as_flash('not_allow');

            redirect(base_url(), 'refresh');
        }
        else
        {
            $this->data['hide_menu'] = true;
            $this->data['content'] = $this->load->view('user_login', '', true);
            $this->load->view('site/main_frame',$this->data);
        }
    }
    public function email_active()
    {
        $this->data['content']  = $this->load->view('email_activate',$this->data, true);
        $this->load->view('site/main_frame',$this->data);
    }
    public function forget()
    {
        $this->data['hide_menu'] = true;
        $this->data['content'] = $this->load->view('forget_password', '', true);
        $this->load->view('site/main_frame',$this->data);
    }

    public function forgot_password()
    {
  	    if($this->user_bootstrap->is_logged_in())
        {
            $_SESSION['error_message'] = lang('you_are_logged_in');
            redirect(base_url(), 'refresh');
        }

        //get the identity type from config and send it when you load the view
        $identity       = 'email';
        $identity_human = lang('email'); //if someone uses underscores to connect words in the column names

        $this->form_validation->set_rules('email', lang('email'), 'required|valid_email');

        if ($this->form_validation->run() == false)
        {
            $this->data['content'] = $this->load->view('forget_password', '', true);
            $this->load->view('site/main_frame',$this->data);
        }
        else
        {
            //run the forgotten password method to email an activation code to the user
            $forgotten = $this->ion_auth->forgotten_password($this->input->post($identity, TRUE));

            if($forgotten)
            {
                //if there were no errors
                $_SESSION['message'] = $this->ion_auth->messages();
                $this->session->mark_as_flash('message');

                 //redirect(base_url().'users/users/user_login', 'refresh');
                 redirect(base_url(), 'refresh');
            }
            else
            {
                $_SESSION['error_message'] = $this->ion_auth->errors();
                $this->session->mark_as_flash('error_message');

                //redirect(base_url().'users/users/forget', 'refresh');
                $this->forget();
            }
	      }
    }

    public function reset_password($code)
    {
        if (!$code)
        {
            redirect('error', 'refresh');
        }

        $user = $this->ion_auth->forgotten_password_check($code);

        if($user)
        {
    			//if the code is valid then display the password reset form
    			$this->form_validation->set_rules('new', $this->lang->line('reset_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[new_confirm]');
    			$this->form_validation->set_rules('new_confirm', $this->lang->line('reset_password_validation_new_password_confirm_label'), 'required');

          if ($this->form_validation->run() == false)
    			{
    				//display the form
    				//set the flash data error message if there is one
    				$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

    				$this->data['min_password_length'] = $this->config->item('min_password_length', 'ion_auth');
    				$this->data['new_password'] = array(
                                        					'name' => 'new',
                                        					'id'   => 'new',
                                        				    'type' => 'password',
                                                            'class'   => 'form-control',
                                        					'pattern' => '^.{'.$this->data['min_password_length'].'}.*$',
                                        				);

    				$this->data['new_password_confirm'] = array(
                                                					'name' => 'new_confirm',
                                                					'id'   => 'new_confirm',
                                                					'type' => 'password',
                                                                    'class'   => 'form-control',
                                                					'pattern' => '^.{'.$this->data['min_password_length'].'}.*$',
                                                				);
    				$this->data['user_id'] = array(
                                    					'name'  => 'user_id',
                                    					'id'    => 'user_id',
                                    					'type'  => 'hidden',
                                    					'value' => $user->id,
                                    			  );

            $this->data['csrf'] = $this->_get_csrf_nonce();
    				$this->data['code'] = $code;
    				//render
            $this->data['content'] = $this->load->view('reset_password', $this->data, true);
            $this->load->view('site/main_frame',$this->data);

    			}
			else
			{

				// do we have a valid request?
				if (//$this->_valid_csrf_nonce() === FALSE ||
        $user->id != intval($this->input->post('user_id', TRUE)))
				{
           //something fishy might be up
					$this->ion_auth->clear_forgotten_password_code($code);

					show_error($this->lang->line('error_csrf'));

				}
				else
				{
					// finally change the password
					$identity = $user->{$this->config->item('identity', 'ion_auth')};
					$change   = $this->ion_auth->reset_password($identity, strip_tags($this->input->post('new', TRUE)));

					if ($change)
					{
						//if the password was successfully changed
						$_SESSION['message'] = $this->ion_auth->messages();
                        $this->session->mark_as_flash('message');

                        redirect(base_url(), 'refresh');
						//$this->logout();
					}
					else
					{
                        $_SESSION['message'] = $this->ion_auth->errors();
                        $this->session->mark_as_flash('message');

                        redirect('users/users/reset_password/' . $code, 'refresh');
					}
				}
			}
		}
        else
        {
            //if the code is invalid then send them back to the forgot password page
            $_SESSION['message'] = $this->ion_auth->errors();
            $this->session->mark_as_flash('message');

            redirect("users/users/forgot_password", 'refresh');
        }
	}

    function _get_csrf_nonce()
  	{
  		$this->load->helper('string');
  		$key   = random_string('alnum', 8);
  		$value = random_string('alnum', 20);

      $_SESSION['csrfkey'] = $key;
      $this->session->mark_as_flash('csrfkey');

      $_SESSION['csrfvalue'] = $value;
      $this->session->mark_as_flash('csrfvalue');

  		return array($key => $value);
  	}

    function _valid_csrf_nonce()
  	{

      if ($this->input->post($this->session->flashdata('csrfkey'), TRUE) !== FALSE &&
  			$this->input->post($this->session->flashdata('csrfkey'), TRUE) == $this->session->flashdata('csrfvalue'))
  		{
  			return TRUE;
  		}
  		else
  		{
  			return FALSE;
  		}
  	}

    public function get_country_cities($country_id)
    {
        $country_id         = intval($country_id);
        $display_lang_id    = $this->session->userdata('lang_id');
        $cities             = $this->cities_model->get_country_cities($country_id , $display_lang_id);
        $user               = $this->user_bootstrap->get_user_data();
        $cities_options     = '';

        if(!empty($cities))
        {
            foreach($cities as $city)
            {
                $selected = '';
                if($city->id == $user->city)
                {
                    $selected   = 'selected';
                }
                $cities_options .="<option value='$city->id' '$selected'> $city->name </option>";
            }
            echo $cities_options;
        }
    }

    public function edit_mydata()
    {
        $this->session->set_userdata('site_redir', current_url());

        if(!$this->ion_auth->logged_in())
        {
            $this->session->set_flashdata('not_allow',lang('please_login_first'));
            redirect(base_url(), 'refresh');
        }
        else
        {
            // Set validation rules if the method is $_POST
            if($_SERVER['REQUEST_METHOD'] == 'POST')
            {
                $this->form_validation->set_rules('phone', lang('phone'), 'required|integer');
                $this->form_validation->set_rules('country_id', lang('country'), 'required');

                if(isset($_POST['password']) && $_POST['password'] != '')
                {
                    $this->form_validation->set_rules('password', lang('password'), 'matches[conf_password]|min_length[6]|max_length[12]');
                    $this->form_validation->set_rules('conf_password', lang('confirm_password'), 'required|matches[password]');
                }

                $this->form_validation->set_message('required', '%s'. lang('required'));
                $this->form_validation->set_message('integer', lang('is_integer'));
                $this->form_validation->set_error_delimiters('<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 no-padding"><p class="validation">', '</p></div>');
            }

            $user = $this->user_bootstrap->get_user_data();

            if($this->form_validation->run() == FALSE)
            {
                $this->_update_user_data_form($user);
            }
            else
            {
                $first_name         = strip_tags($this->input->post('username', TRUE));
                $last_name          = strip_tags($this->input->post('last_name', TRUE));
                $phone              = strip_tags($this->input->post('phone', TRUE));
                $country_id         = strip_tags($this->input->post('country_id', TRUE));
                $city_id            = strip_tags($this->input->post('city_id', TRUE));
                $mail_list          = strip_tags($this->input->post('mail_list', TRUE));

                //->>login_auth = 1 for login sms auth
                //->>login_auth = 2 for login google auth

                $login_auth         = strip_tags($this->input->post('login_auth', TRUE));

                $country_row        = $this->cities_model->get_country_call_code($country_id);
                $calling_code       = $country_row->calling_code;
                $userphone          = $calling_code . $phone;
                $data               = array(
                                              'first_name'           => $first_name        ,
                                              'last_name'            => $last_name         ,
                                              'phone'                => $userphone         ,
                                              'mail_list'            => $mail_list         ,
                                              'Country_ID'           => $country_id        ,
                                              'city'                 => $city_id           ,
                                              'login_auth'           => $login_auth        ,
                                              'login_auth_activated' => 1
                                        );

                if(isset( $_POST['password']) && $_POST['password']!= '')
                {
                    $data['password'] = strip_tags($this->input->post('password', TRUE));
                }

                $old_phone = $user->phone ;
                $new_phone = $userphone;

                if($this->ion_auth->update($user->id, $data))
                {
                    /***************insert user bank accounts ***************/
                    $banks_ids       = $this->input->post('bank_id', TRUE);
                    $account_names   = $this->input->post('account_name', TRUE);
                    $account_numbers = $this->input->post('account_number', TRUE);

                    if(isset($_POST['bank_id']))
                    {
                        $this->user_bank_accounts_model->delete_user_bank_accounts($user->id);

                        foreach($banks_ids as $key => $bank_id)
                        {
                            $user_bank_data = array(
                                                    'account_name'   => strip_tags($account_names[$key]) ,
                                                    'account_number' => strip_tags($account_numbers[$key]),
                                                    'user_id'        => $user->id,
                                                    'bank_id'        => strip_tags($bank_id)
                                                );


                            $this->user_bank_accounts_model->insert_user_account_data($user_bank_data);
                         }
                    }

                    /**************************************************/
                    //$this->session->set_flashdata('message', $this->ion_auth->messages());

                    //-->>> if update phone send sms verification
                    if($old_phone != $new_phone)
                    {
                        // to override the old user_bootstrap for old 'phone'
                        $this->user_bootstrap->reload_user_data();
                        //$this->_phone_activate();
                    }
                }
                else
                {
                    $this->session->set_flashdata('error', $this->ion_auth->errors());
                }

                /*******************************************************/
                if($login_auth == 2) //For google login 2way auth
                {
                     redirect(base_url().'users/qrcode/','refresh');
                }
                else
                {
                     redirect(base_url().'users/users/edit_mydata','refresh');
                }
            }
        }
    }

    private function _update_user_data_form($user)
    {
        $this->load->model('affiliate/admin_affiliate_model');
        $lang_id                = $this->user_bootstrap->get_active_lang_id();
        $user_bank_accounts     = $this->users_model->get_bank_accounts_result($lang_id, $user->id);
        $countries              = $this->cities_model->get_user_nationality_filter_data($lang_id);
        $country_row            = $this->cities_model->get_country_call_code($user->Country_ID);
        $calling_code           = '';
        $affiliate_code         = $this->admin_affiliate_model->get_user_affiliate($user->id);

        if($affiliate_code)
        {
            $data['affiliate_code'] = $affiliate_code;
        }
        $data['user']           = $user ;
        $data['cities']         = $this->cities_model->get_country_cities($user->Country_ID , $lang_id);


         //////////////////////////////////////////////////
         //convert phone number to array

        $calling_code_len       = strlen ( $calling_code );
        $phone_str              = substr($user->phone , $calling_code_len);

        $data['user_phone']     = $phone_str ;
        $data['calling_code']   = $calling_code ;

        $countries_array        = array();
        $countries_array[null]  = lang('choose');

        foreach($countries as $country)
        {
            $countries_array[$country->id]  = $country->name;
        }

        $data['user_countries']     = $countries_array;
        $data['user_bank_accounts'] = $user_bank_accounts ;

        $data['edit_profile'] = true;

        $this->data['content']  = $this->load->view('edit_mydata', $data, true);

        $this->load->view('site/main_frame', $this->data);


    }


    public function logout()
    {
        $this->ion_auth->logout();

        redirect(base_url(),'refresh');
    }



    public function edit_wholesaler_data()
    {
        $this->session->set_userdata('site_redir', current_url());

        $user_id = $this->user_bootstrap->get_user_id();

        if($user_id != 0 )
        {
            $user_data = $this->user_bootstrap->get_user_data();

            if(!$this->data['edit_wholesaler_data'])
            {
                redirect('not_allowed', 'refresh');
            }
            else
            {

                $logo = $user_data->logo;

                if( $user_data->header == '' || $user_data->footer == '' || $user_data->sms_content == ''|| $user_data->sms_name == '' || $user_data->geocomplete == '' || $user_data->google_map_lat == '' || $user_data->google_map_lng == '' || $user_data->logo == '')
                {
                    $validation_msg = false;

                    if($_SERVER['REQUEST_METHOD'] == 'POST')
                    {
                        $validation_msg = true;


                        if (isset($_FILES['image3']) && empty($_FILES['image3']['name']))
                        {
                            $this->form_validation->set_rules('image3', lang('logo'), 'trim|required');
                        }
                        $this->form_validation->set_rules('sms_name', lang('sms_sender'), 'trim|required|max_length[11]');
                        $this->form_validation->set_rules('sms_content', lang('sms_content'), 'trim|required|max_length[50]');
                        $this->form_validation->set_rules('header', lang('header'), 'trim|required');
                        $this->form_validation->set_rules('footer', lang('footer'), 'trim|required');
                        $this->form_validation->set_rules('geocomplete', ('address'), 'trim|required');

                        $this->form_validation->set_message('required', lang('required')." : %s ");
                        $this->form_validation->set_message('max_length', '%s '.lang('max_length_is').': '.' %s');
                        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
                    }

                    if($this->form_validation->run() == FALSE)
    	            {
    	                $this->_show_form($validation_msg, $user_data);
    	            }
                    else
                    {
                      $this->load->library('upload');
                      $gallery_path = realpath(APPPATH. '../assets/uploads');

                      if(isset($_FILES['image3']['name']))
                        {
                            //////////Upload logo image
                            $config = array();
                            $config['upload_path']   = $gallery_path;
                            $config['allowed_types'] = 'gif|jpeg|jpg|png';

                            $this->upload->initialize($config);

                            if(!$this->upload->do_upload('image3'))
                            {
                              $error['error'] = $this->upload->display_errors();
                            }
                            else
                            {
                              $file_data = $this->upload->data();
                              $logo      = $file_data['file_name'];
                            }
                         }


                        $sms_name         = strip_tags($this->input->post('sms_name', TRUE));
                        $sms_content      = strip_tags($this->input->post('sms_content', TRUE));
                        $header           = strip_tags($this->input->post('header', TRUE));
                        $footer           = strip_tags($this->input->post('footer', TRUE));
                        $geocomplete      = strip_tags($this->input->post('geocomplete', TRUE));
                        $lat              = strip_tags($this->input->post('lat', TRUE));
                        $lng              = strip_tags($this->input->post('lng', TRUE));

                        $wholesaler_data = array(
                                                    'geocomplete'      => $geocomplete,
                                                    'google_map_lat'   => $lat,
                                                    'google_map_lng'   => $lng,
                                                    'logo'             => $logo,
                                                    'sms_name'         => $sms_name,
                                                    'sms_content'      => $sms_content,
                                                    'header'           => $header,
                                                    'footer'           => $footer
                                                );

                        $this->user_model->update_user($user_id, $wholesaler_data);

                        $_SESSION['message'] = lang('wholesaler_data_added');
                        $this->session->mark_as_flash('message');

                        redirect(base_url(), 'refresh');
                    }

                }
                else
                {
                    redirect('not_allowed', 'refresh');
                }
            }


        }else
        {
            redirect('users/users/user_login', 'refresh');
        }
    }

    private function _show_form($validation_msg, $user_data)
    {
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }

        $this->data['user_data'] = $user_data;

        $this->data['content']   = $this->load->view('wholesaler_data', $this->data, true);
        $this->load->view('site/main_frame',$this->data);
    }

    public function check_login_sms_auth()
    {
        if(!$this->data['is_logged_in'])
        {
            $_SESSION['not_allow'] = lang('please_login_first');
            $this->session->mark_as_flash('not_allow');

            redirect(base_url(), 'refresh');
        }
        else
        {
            if($this->data['user']->account_sms_activated == 1 && $this->data['user']->login_auth_activated == 1)
            {
                $_SESSION['error_message'] = lang('account_already_activated');
                $this->session->mark_as_flash('error_message');

                redirect(base_url(), 'refresh');
            }
            // Set validation rules if the method is $_POST
            if($_SERVER['REQUEST_METHOD'] == 'POST')
            {
                $this->form_validation->set_rules('sms_code', lang('sms_code'), 'required');
                $this->form_validation->set_message('required','%s'. lang('required'));
                $this->form_validation->set_error_delimiters('<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 no-padding"><p class="validation">', '</p></div>');
            }

            if($this->form_validation->run() == TRUE)
            {
                $user = $this->user_bootstrap->get_user_data();

                if($user)
                {
                    $sms_code = strip_tags($this->input->post('sms_code', TRUE));

                    if($sms_code == $user->sms_code)
                    {
                        $data = array(
                            'login_auth_activated'  => 1,
                            'account_sms_activated' => 1
                        );

                        $this->ion_auth->update($user->id, $data);

                        $this->session->set_flashdata('message','successfully code');

                        redirect(base_url(), 'refresh');
                    }
                    else
                    {
                         $this->session->set_flashdata('message','wrong code');
                         redirect(base_url().'users/users/check_login_sms_auth','refresh');
                    }
                }
            }
            else
            {
                $this->data['content']  = $this->load->view('sms_activate', $this->data, true);
                $this->load->view('site/main_frame',$this->data);
            }
        }
   }
/*********************************************************/

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
        $this->send_code_sms($sms_code, $user->phone);

        //-->>load form
        redirect(base_url().'users/register/activate_user_phone','refresh');
    }

    private function _generate_sms_code($user_id, $send_sms = true)
    {
        //-->>Generate code
        $sms_code = rand(1000, 9999);

        $user = $this->user_bootstrap->get_user_data();

        $data = array(
        		   'sms_code' => $sms_code
        		);

        $this->ion_auth->update($user_id, $data);

        if($send_sms)
        {
            //-->>  send new sms_code
            $this->send_code_sms($sms_code, $user->phone);
        }
    }

    private function send_code_sms($code, $phone)
    {
        $msg = lang('sms_activation_code').' : '. $code;
        $this->notifications->send_sms($msg, $phone);
    }
}
