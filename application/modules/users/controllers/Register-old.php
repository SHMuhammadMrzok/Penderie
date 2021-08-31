<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Register extends CI_Controller
{
    var $data = array();
    
    public function __construct()
    {
        parent::__construct();
        
        $this->load->library('shopping_cart');
        $this->load->model('countries_model');
        $this->load->model('user_model');
        $this->load->model('cities_model');
        $this->load->model('ion_auth_model');
        $this->load->library('notifications');
        
        require(APPPATH . 'includes/front_end_global.php');
        
    }
    
    public function get_country_cities()
    {
        $country_id      = intval(strip_tags($this->input->post('id', TRUE)));   
        $display_lang_id = $this->session->userdata('lang_id');
        $cities          = $this->cities_model->get_country_cities($country_id , $display_lang_id);
       
        $cities_options  = '<select class="form-control" id="city" name="city_id">';
        $cities_options .= '<option value="0">---------</option>'; 
         
        if(count($cities)>0)
        {
            foreach($cities as $city)
            {
                $cities_options .="<option value='$city->id'> $city->name </option>";
            }
            
        }
        $cities_options .= "</select>";
        
        echo $cities_options;
        
    }
   
    public function get_country_call_code()
    {
        $calling_code = 0;
        $country_id   = strip_tags($this->input->post('id', true));
        $country      = $this->cities_model->get_country_call_code($country_id);
        
        if(count($country)>0)
        {
            $calling_code = $country->calling_code;
        }
        
        echo $calling_code;
    }
    
    public function index()
    {
        $validation_msg = false;
        
        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
        {
            $validation_msg   = true;
            
            //check if first register user
            $email_unique = '';
            $phone_unique = '';
        
            $email = strip_tags($this->input->post('email', TRUE));
            $phone = strip_tags($this->input->post('phone', TRUE));
            
            //$country_id   = intval(strip_tags($this->input->post('country_id', TRUE)));
            //$country      = $this->cities_model->get_country_call_code($country_id);
            
            //$calling_code = $country->calling_code;
            $user_phone   = $phone;
            
            /*$check_user_count = $this->user_model->check_if_user_regestered($email, $user_phone);
            
            if($check_user_count == 0)
            {
                $email_unique = '|is_unique[users.email]';
               $phone_unique = '|is_unique[users.phone]';//|callback_check_phone_code';
            }
            */
            
            //$this->form_validation->set_rules('first_name', lang('first_name'), 'required');
            //$this->form_validation->set_rules('last_name', lang('last_name'), 'required');
            $this->form_validation->set_rules('email', lang('email'), "required|valid_email|is_unique[users.email]");
            $this->form_validation->set_rules('phone', lang('phone'), "required|integer|is_unique[users.phone]");
            //$this->form_validation->set_rules('country_id', lang('country'), 'required');
            $this->form_validation->set_rules('password', lang('password'), 'required|matches[conf_password]|min_length[6]|max_length[12]');
            $this->form_validation->set_rules('conf_password', lang('confirm_password'), 'required');
            //$this->form_validation->set_rules('checkbox', lang('checkbox'), 'required');
            
            $this->form_validation->set_message('required', lang('required')."  : %s ");
            $this->form_validation->set_message('is_unique', lang('is_unique')."  : %s ");
            $this->form_validation->set_message('integer', lang('is_integer')."  : %s ");
            $this->form_validation->set_message('valid_email', lang('valid_email')."  : %s ");
            $this->form_validation->set_error_delimiters('', '');
            
            //$accept_rules = $this->input->post('checkbox');
        }
        
        if($this->form_validation->run() == FALSE)
		{
		  $this->_add_form($validation_msg);
        }
        else
        {
            $txt_msg        = '';
            
            $username       = $this->generateRandomString();
            //$first_name     = strip_tags($this->input->post('first_name', TRUE));
            //$last_name      = strip_tags($this->input->post('last_name', TRUE));
            $email          = strip_tags($this->input->post('email', TRUE));
            $phone          = strip_tags($this->input->post('phone', TRUE));
            //$country_id     = strip_tags($this->input->post('country_id', TRUE));
            //$city_id        = strip_tags($this->input->post('city_id', TRUE));
            $password       = strip_tags($this->input->post('password'));
            //$mail_list      = strip_tags($this->input->post('mail_list', TRUE));
           
            
            $new_user_customer_group_id = $this->config->item('new_user_customer_group_id');
            $affiliate_user_id = 0;
           
            //-->> ask if session(affiliate_user_id) exist
            if( $this->session->userdata('affiliate_user_id') != null)
            {
                $affiliate_user_id = $this->session->userdata('affiliate_user_id');
            }
           
            $session_id = $this->session->userdata('affiliate_user_id');
            //-->>if found store this user_id in field affiliate_user_id in new user row
            //$country          = $this->cities_model->get_country_call_code($country_id);
            //$calling_code     = $country->calling_code;
            
            //$userphone        = $calling_code.$phone;
            $store_country_id = $this->data['country_id'];
            
            $additional_data  = array(
                                      //'first_name'              => $first_name      ,
                                      //'last_name'               => $last_name       ,
                                      //'country_calling_code'    => $calling_code    ,
                                      'phone'                   => $phone       ,
                                      //'mail_list'               => $mail_list       ,
                                      //'Country_ID'              => $country_id      ,
                                      //'city_id'                 => $city_id         ,
                                      'customer_group_id'       => $new_user_customer_group_id ,
                                      'affiliate_user_id'       => $affiliate_user_id   ,
                                      'store_country_id'        => $store_country_id    ,
                                      // sms activation
                                      'active'                  => 1,
                                      'account_sms_activated'   => 0,
                                      'login_auth_activated'    => 0,
                                      'login_auth'              => 0
                                      
                                   );
                                   
            $group = array('id'=>2);
            
            
            
            $id = $this->ion_auth->register($username, $password, $email, $additional_data,$group);                   
            
            if($id)
            {
                //-->>Send notification
                $this->_send_new_registered_notification($id);
                
                $txt_msg = lang('email_activation_sent');
            }
            else
            {
                $txt_msg = $this->ion_auth->errors();
            }
                
            
           
            $this->data['message'] = $txt_msg;
    
            $this->data['content'] = $this->load->view('msg_view', $this->data, true);
            $this->load->view('site/main_frame',$this->data);
        }
    }
    
    private function _send_new_registered_notification($id)
    {
        $user       = $this->user_model->get_row_data($id);
        $data       = array(
                               'username'   => $user->first_name.' '.$user->last_name   ,
                               'user_id'    => $user->id                                ,
                               'email'      => $user->email                             ,
                               'phone'      => $user->phone                             ,
                               'ip_address' => $user->ip_address                        ,
                               'join_date'  => date('Y/m/d', $user->created_on)         ,
                               'logo_path'  => base_url().'assets/template/home/img/logo.png'
                           );
        
        $emails[] = $user->email;
        $phone    = $user->phone;
        
        $this->notifications->create_notification('new_user_registered', $data, $emails, $phone);
        
        if($this->sms_activate_login($id))
        {
            $_SESSION['message'] = $this->ion_auth->messages();
            $this->session->mark_as_flash('message');
        }
        else
        {
            $_SESSION['notification_error'] = lang('sms_active_code_notsend');
            $this->session->mark_as_flash('notification_error');    
        }
    }
    
    private function _add_form($validation_msg)
    {
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }
        
        if ($this->data['is_logged_in'])
        {
             $_SESSION['not_allow'] = lang('you_are_registered');
             $this->session->mark_as_flash('not_allow');
             
             redirect(base_url(),'refresh');
        
        }
        else
        {
            $countries       = $this->cities_model->get_user_nationality_filter_data($this->data['lang_id']);
            $countries_array = array();
            $cities_array    = array();
            $countries_array[null] = lang('choose');
            $calling_code = '';
           
            foreach($countries as $country)
            {
                if($country->id == 191)
                {
                    $calling_code = $country->calling_code;
                }
                $countries_array[$country->id]  = $country->name;
            }
            
            /*$cities = $this->cities_model->get_country_cities(191 , $this->data['lang_id']);
            
            foreach($cities as $city)
            {
                $cities_array[$city->id] = $city->name;
            }
            
            $this->data['cities']               = $cities_array;
            */$this->data['user_countries']       = $countries_array;
            $this->data['calling_code']         = $calling_code;
            
            $this->data['content'] = $this->load->view('site_register', $this->data, true);
            $this->load->view('site/main_frame',$this->data);
        }
    }
   
   public function affiliate ($code)
   {
        //-->>>get user_id   by affiliate code
        $user_id = $this->user_model->get_user_id_by_affiliate_code($code);
        
        //-->> store user_id in session(affiliate_user_id)
        $this->session->set_userdata('affiliate_user_id', $user_id);
        
        //-->> redirect to register form
        redirect(base_url().'users/register/','refresh');
   }
  
    public function sms_activate_login($user_id)
    {
        
        $user_id = intval($user_id);
        //-->>Generate code
        $sms_code = rand(1000, 9999);
        
        $user = $this->user_model->get_row_data($user_id);
        
        $data = array(
            		   'sms_code'              => $sms_code,
                       'account_sms_activated' => 0
            		 );
                
        $this->ion_auth->update($user_id, $data);
        $message = lang('sms_activation_code').' '.$sms_code;
        
        //-->>  send new sms_code
        if( $this->notifications->send_sms($message ,$user->phone))
        {
            return true;
        }
        else
        {
           return false ; 
        }
    }
    
   public function  member_activate($id)
   {
        $id = intval($id);
       
        $this->data['id'] = $id ;
        $this->data['content']  = $this->load->view('member_activate',$this->data, true);
        $this->load->view('site/main_frame',$this->data);
   }
   public function sms_register_active($id)
   {
        $id = intval($id);
        
        $this->data['id'] = $id ;
        $this->data['content']  = $this->load->view('sms_activate', $this->data, true);
        $this->load->view('site/main_frame',$this->data);
   }
   
   public function activate_user_phone()
   {
        if(!$this->ion_auth->logged_in())
        {
            $_SESSION['not_allow'] = lang('please_login_first');
            $this->session->mark_as_flash('not_allow');
            
            redirect(base_url(), 'refresh');
        }
        else
        {
            if($this->data['user']->account_sms_activated == 1)
            {
                $_SESSION['not_allow'] = lang('account_already_activated');
                $this->session->mark_as_flash('not_allow');
                
                redirect(base_url(), 'refresh');
            }
            // Set validation rules if the method is $_POST
            if($_SERVER['REQUEST_METHOD'] == 'POST')
            {
                $this->form_validation->set_rules('sms_code', lang('sms_code'), 'required');
                $this->form_validation->set_message('required','%s '. lang('required'));
                $this->form_validation->set_error_delimiters('<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 no-padding validation"><p class="validation" style="width: 250px;">', '</p></div>');
            }
            
            if($this->form_validation->run() == TRUE)
            {
                $user = $this->user_bootstrap->get_user_data();
        
                if($user)
                {
                    $sms_code = $this->input->post('sms_code');
                    
                    if($sms_code == $user->sms_code)
                    {
                        $data = array(
                            'login_auth_activated'  => 1,
                            'account_sms_activated' => 1
                        );
                        
                        $this->ion_auth->update($user->id, $data);
                        
                        $_SESSION['message'] = lang('code_applied_successfully');
                        $this->session->mark_as_flash('message');
                        
                        redirect(base_url(),'refresh');
                    }
                    else
                    {
                         
                         $_SESSION['error_message'] = lang('wrong_code_try_again');
                         $this->session->mark_as_flash('message'); 
                         
                         redirect(base_url().'users/register/activate_user_phone', 'refresh');
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

   public function first_register()
   {
        /*$this->form_validation->set_rules('email', lang('email'), 'required|valid_email');
        $this->form_validation->set_rules('phone', lang('phone'), 'required|number|is_unique[users.phone]');
        $this->form_validation->set_message('required', lang('required')."  : %s ");
        $this->form_validation->set_error_delimiters('<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 no-padding"><p class="validation">', '</p></div>');
        if($this->form_validation->run() == FALSE)
        {
            echo 'false register';
        }
        else
        {*/
        $email = $this->input->post('email');
        $phone = $this->input->post('phone');
        $count_user_email = $this->user_model->count_user_email_exist($email);
        if($count_user_email == 0)
        {
            $new_user_customer_group_id = $this->config->item('new_user_customer_group_id');
            $affiliate_user_id = 0;
            //-->> ask if session(affiliate_user_id) exist
            if( $this->session->userdata('affiliate_user_id') != null)
            {
                $affiliate_user_id = $this->session->userdata('affiliate_user_id');
            }
            $session_id = $this->session->userdata('affiliate_user_id');
            //-->>if found store this user_id in field affiliate_user_id in new user row
            $additional_data = array(
                                      'phone'             => $phone,
                                      'affiliate_user_id' => $affiliate_user_id,
                                      'first_order'       => 1
                                   );
            $group   = array('id' => 2);
            $user_id = $this->ion_auth->register('', '', $email, $additional_data,$group);
            if($user_id)
            {
                $user_data  = $this->user_model->get_row_data($user_id);
                $ip_address = $user_data->ip_address;
                $session_id = $this->session->userdata('session_id');
                $country_id = $this->session->userdata('country_id');
                $lang_id    = $this->session->userdata('lang_id');
                $this->shopping_cart->set_user_data($user_id, $session_id, $ip_address, $country_id, $lang_id);
                $updated_data['user_id'] = $user_id;
                $cart_id = $this->session->userdata("cart_id_session");
                $this->shopping_cart->update_shopping_cart($cart_id, $updated_data);
                echo 'true';
                
            }
            else
            {
                $errors = $this->ion_auth->errors();
                echo $errors;
            }
        }
        else
        {
            echo 'finish_sign_up_data';
        }
   }
   
  public function update_sign_up_data()
  {
    $return_message = '';
    $errors_exist   = 'true';
    
    $this->form_validation->set_rules('last_name', lang('last_name'), 'required');
    //$this->form_validation->set_rules('country_id', lang('country'), 'required');
    //$this->form_validation->set_rules('city_id', lang('city'), 'required');
    $this->form_validation->set_rules('password', lang('password'), 'required|matches[conf_pass]');
    $this->form_validation->set_rules('conf_pass', lang('confirm_password'), 'required');
    
    $this->form_validation->set_message('required', lang('required')."  : %s ");
    $this->form_validation->set_message('matches', lang('password_not_match'));
    $this->form_validation->set_error_delimiters('<div style="color: red;">', '</div>');        
    
    if($this->form_validation->run() == FALSE)
    {
        $return_message = validation_errors();
    }
    else
    {
        $email            = $this->input->post('email'); 
        $last_name        = $this->input->post('last_name');
        //$country_id       = $this->input->post('country_id');
        $city_id          = $this->input->post('city_id');
        $password         = $this->input->post('password');
        $confirm_password = $this->input->post('confirm_password');
        $user             = $this->user_model->get_user_data_by_field('email', $email);
        
        if($user)
        {
            $user_id     = $user->id;
            $update_data = array(
                                    'last_name'             => $last_name               ,
                                    'password'              => $password                ,
                                    //'Country_ID'            => $country_id              ,
                                    'city_id'               => $city_id                 ,
                                    'first_order'           => 0                        ,
                                    'store_country_id'      => $this->data['country_id'],
                                    // sms activation
                                    'account_sms_activated' => 0                        ,
                                    'login_auth_activated'  => 0                        ,
                                    'login_auth'            => 0
                                );
            
            if($this->ion_auth->update($user_id, $update_data))
            {
                $deactivate      = $this->ion_auth_model->deactivate($user_id);
                $activation_code = $this->ion_auth_model->activation_code;
     			$identity        = $this->config->item('identity', 'ion_auth');
                
                $data = array(
                				'identity'   => $user->{$identity},
                				'id'         => $user_id,
                				'email'      => $email,
                				'activation' => $activation_code,
                			 );
                                 
                
                //-->>Send notification
                $this->_send_new_registered_notification($user_id);
                
                //-->>>send email 	
                $message = $this->load->view($this->config->item('email_templates', 'ion_auth').$this->config->item('email_activate', 'ion_auth'), $data, true);
                
    			$this->email->clear();
    			$this->email->from($this->config->item('admin_email', 'ion_auth'), $this->config->item('site_title', 'ion_auth'));
    			$this->email->to($email);
    			$this->email->subject($this->config->item('site_title', 'ion_auth') . ' - ' . $this->lang->line('email_activation_subject'));
    			$this->email->message($message);
    
    			if ($this->email->send())// == TRUE
    			{
    				$_SESSION['message'] = lang('email_activation_sent');
                    $this->session->mark_as_flash('message'); 
                    
                    $_SESSION['first_register_msg'] = lang('email_activation_sent');
    			}
                else
                { 
                    $_SESSION['error_message'] = lang('email_activation_not_sent');
                    $this->session->mark_as_flash('message'); 
                    
                    $_SESSION['first_register_msg'] = lang('email_activation_not_sent');
                }
                
                //$this->sms_activate_login($user_id);
                $errors_exist = 'false';
                //redirect(base_url(), 'refresh');
            }
            else
            {
                $return_message = lang('not_updated');
                $_SESSION['first_register_msg'] = $return_message;
            }
            
            
        }
    }
    
      
      echo json_encode(array($errors_exist, $return_message));
  }
  
  public function view_first_msg()
  {
      $message = '';
      
      if(isset($_SESSION['first_register_msg']) && $_SESSION['first_register_msg'] != '')
      {
        $message = $_SESSION['first_register_msg'];
      }
      elseif(isset($_SESSION['first_order_inserted']) && $_SESSION['first_order_inserted'] != '')
      {
        $message = $_SESSION['first_order_inserted'];
      }
      
      $this->data['message'] = $message;
      
      $this->data['content'] = $this->load->view('msg_view', $this->data, true);
      $this->load->view('site/main_frame',$this->data);
  }
  
  public function check_phone($phone)
  {
    $country_id   = $this->input->post('country_id');
    $country      = $this->cities_model->get_country_call_code($country_id);
    
    $calling_code = $country->calling_code;
    $user_phone   = $calling_code.$phone;
    
    $user_phone_exist              = $this->user_model->check_user_phone_exist($phone);
    $phone_with_calling_code_exist = $this->user_model->check_user_phone_exist($user_phone);
    
    if($user_phone_exist || $phone_with_calling_code_exist)
    {
        $this->form_validation->set_message('check_phone', lang('is_unique')."  : %s ");
        
        return FALSE;
    }
    /*elseif(substr($phone, 0, 1) == 0 || substr($phone, 0, 1) == '+')
    {
        $this->form_validation->set_message('check_phone', lang('phone_number_not_start_with_zero'));
        return FALSE;
    }
    elseif(substr($phone, 0, strlen($calling_code)) == $calling_code)
    {
        $this->form_validation->set_message('check_phone', ('phone_number_not_start_with_calling_code'));
        return FALSE;
    }*/
    else
    {
        return TRUE;
    }
  }
  
  public function check_phone_code($phone)
  {
    $country_id   = $this->input->post('country_id');
    $country      = $this->cities_model->get_country_call_code($country_id);
    
    $calling_code = $country->calling_code;
    $user_phone   = $calling_code.$phone;
    
    if(substr($phone, 0, 4) == '9665')
    {
        return TRUE;
    }
    else
    {
        $this->form_validation->set_message('check_phone_code', lang('phone_number_not_start_with_calling_code'));
        return FALSE;
    }
    /*if(substr($phone, 0, 1) == 0 || substr($phone, 0, 1) == '+')
    {
        $this->form_validation->set_message('check_phone_code', lang('phone_number_not_start_with_zero'));
        return FALSE;
    }
    elseif(substr($phone, 0, strlen($calling_code)) == $calling_code)
    {
        $this->form_validation->set_message('check_phone_code', lang('phone_number_not_start_with_calling_code'));
        return FALSE;
    }
    else
    {
        return TRUE;
    }*/
  }
  
  public function generateRandomString($length = 6) 
  {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
/*********************************************************/
}