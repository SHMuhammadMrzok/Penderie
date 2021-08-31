<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sell extends CI_Controller {

    public $lang_row;

    public function __construct()
    {
        parent::__construct();

        $this->load->model('global_model');

        $this->load->model('users/cities_model');
        $this->load->model('stores/stores_model');
        $this->load->model('settings/admin_model');

        $settings       = $this->global_model->get_config();
        $site_settings  = $this->admin_model->get_site_settings();
        $site_type      = $this->global_model->get_site_business_type();

        foreach($settings as $key => $value)
        {
            $this->config->set_item($key, $value);
        }

        foreach ($site_settings as $key => $row)
        {
          $this->config->set_item($row->field, $row->value);
        }


        if(!isset($_SESSION['lang_id']))
        {
             $default_lang = $settings->default_lang;
             $this->lang_row     = $this->global_model->get_lang_row($default_lang);

             $this->lang->load($this->lang_row->language, $this->lang_row->language);
        }
        else
        {
            $lang      = $_SESSION['lang_id'];
            $this->lang_row  = $this->global_model->get_lang_row($lang);
            $this->lang->load($this->lang_row->language, $this->lang_row->language);
        }

        if($site_type == 'b2c'){
          redirect(base_url(), 'refresh');
        }

    }
    
    function change_lang($lang='english')
   {
       $lang_row  = $this->global_model->get_language_row_by_lang($lang);
       $this->session->set_userdata('lang_id', $lang_row->id);
       redirect('sell/login');//($this->session->userdata('last_location'),'refresh');
   }

    public function index()
    {
        if ($this->ion_auth->logged_in())
        {
           redirect(base_url().'sell/dashboard','refresh');
        }
        else
        {
           redirect(base_url().'sell/login','refresh');
        }
    }

     public function dashboard()
     {
        require(APPPATH . 'includes/global_vars.php');
        $this->load->library('reports');

        $this->stores = $this->admin_bootstrap->get_user_available_stores(0, 1);
        $store_id_array   = array();
        $store_ids_string = '';
        $last_key = count($this->stores) - 1;

        foreach($this->stores as $key=>$store)
        {
            $store_id_array[] = $store->store_id;
            $store_ids_string .= $store->id;

            // to remove "," after last element
            if($key < $last_key)
            {
                $store_ids_string .= ', ';
            }
        }

        if(count($store_id_array) != 0)
        {

        $last_month_unix  = time() - (60 * 60 * 24 * 30 * 1);
        $default_currency = $this->currency->get_default_country_symbol();

        $countries_sales        = $this->reports->countries_sales($this->data['lang_id'], $store_id_array);
        $cats_sales             = $this->reports->categories_sales($this->data['lang_id'], $store_id_array);
        $most_products          = $this->orders_model->get_most_bought_products($this->data['lang_id'], 0, $store_ids_string);
        $agents_sales           = $this->reports->agents_sales($this->data['lang_id'], $store_id_array);
        //$new_users              = $this->reports->last_regestered_users($last_month_unix, $store_id_array);
        $payment_methods_sales  = $this->reports->payment_methods_sales($this->data['lang_id'], $store_id_array);
        $month_sales            = $this->reports->monthly_sales_amount(date('Y'), $this->data['lang_id'], $store_id_array);
        $order_status_sales     = $this->reports->order_status_sales($this->data['lang_id'], $store_id_array);


        $this->data['default_currency']         = $default_currency;
        $this->data['countries_sales']          = $countries_sales;
        $this->data['cats_sales']               = $cats_sales;
        $this->data['most_products']            = $most_products;
        $this->data['agents_sales']             = $agents_sales;
        //$this->data['new_users']                = $new_users;
        $this->data['payment_methods_sales']    = $payment_methods_sales;
        $this->data['month_sales']              = $month_sales;
        $this->data['status_sales']             = $order_status_sales;
        $this->data['stores_ids']               = $store_id_array;

        $this->data['content'] = $this->load->view('Sell/dashboard', $this->data, true);
        $this->load->view('Sell/main_frame',$this->data);
        }
        else
        {
            $this->data['message'] = lang('no_active_stores');
             $this->load->view('Sell/msg_view', $this->data);
        }

    }

    function login()
    {
        if ($this->ion_auth->logged_in()){

             redirect(base_url().'sell/dashboard','refresh');

          }else{

            $this->load->model('root/lang_model');
            $data['structure_languages']= $this->lang_model->get_active_structure_languages();

            $this->form_validation->set_rules('email', lang('email'),'required|valid_email');
            $this->form_validation->set_rules('password', lang('password'), 'required');

            $this->form_validation->set_message('required', lang('required'));
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

            if ($this->form_validation->run() == true)
            {

                $remember = (bool)$this->input->post('remember');
                if ($this->ion_auth->login($this->input->post('email'), $this->input->post('password'),$remember))
                {
                     $this->session->set_userdata('lang_id', $this->lang_row->id);

                     if($redir = $this->session->userdata('redir'))
                     {
                         redirect($redir);
                     }

                     //-->>if the login is successful redirect  to dashboard
                     $this->session->set_flashdata('message', $this->ion_auth->messages());
                     redirect(base_url().'sell/dashboard','refresh');

                }
                else
                {
                    if($this->config->item('images_source') == 'amazon')
                   {
                     $images_path = "https://".$this->config->item('amazon_s3_my_bucket').".s3.".$this->config->item('amazon_s3_region').".amazonaws.com/".$this->config->item('amazon_s3_subfolder');
                   }
                   else
                   {
                     $images_path = base_url().'assets/uploads/';
                   }

                   $data['images_path'] = $images_path;

                    //-->>if the login was un-successful redirect  to the login page
                    $data['login_error'] = lang('login_error');//$this->ion_auth->errors();
                    $this->session->set_flashdata('login_error', $this->ion_auth->errors());
                    $this->load->view('Sell/login', $data);

                    //use redirects instead of loading views for compatibility with MY_Controller libraries
                }
            }
            else
            {
              
               $conditions_page = $this->global_model->get_table_data('static_pages_translation', array('lang_id'=>$_SESSION['lang_id'], 'page_id'=>9), 'row');
               $data['conditions'] = $conditions_page;
               $data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

               $this->load->view('Sell/login', $data);
            }
        }
   }

    public function logout()
	{
	   $this->ion_auth->logout();
       redirect(base_url().'Sell/login','refresh');
	}

    public function edit_my_data()
    {
        require(APPPATH . 'includes/global_vars.php');
        $this->session->set_userdata('site_redir', current_url());

        if(!$this->ion_auth->logged_in())
        {
            $this->session->set_flashdata('not_allow',lang('please_login_first'));
            redirect(base_url().'sell', 'refresh');
        }
        else
        {
            // Set validation rules if the method is $_POST
            if($_SERVER['REQUEST_METHOD'] == 'POST')
            {
                $this->form_validation->set_rules('phone', lang('phone'), 'required|integer');
                //$this->form_validation->set_rules('country_id', lang('country'), 'required');

                if(isset( $_POST['password']) && $_POST['password'] != '')
                {
                    $this->form_validation->set_rules('conf_password', lang('confirm_password'), 'required|matches[password]');
                }

                $this->form_validation->set_message('required', '%s'. lang('required'));
                $this->form_validation->set_message('integer', lang('is_integer'));
                $this->form_validation->set_error_delimiters('<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 no-padding"><p class="validation">', '</p></div>');
            }

            $user = $this->admin_bootstrap->get_user_data();

            if($this->form_validation->run() == FALSE)
            {
                $this->_update_user_data_form($user);
            }
            else
            {
                $phone              = strip_tags($this->input->post('phone', TRUE));
                $first_name         = strip_tags($this->input->post('first_name', TRUE));
                $last_name          = strip_tags($this->input->post('last_name', TRUE));
                $phone              = strip_tags($this->input->post('phone', TRUE));
                //$country_id         = strip_tags($this->input->post('country_id', TRUE));
                $city_id            = strip_tags($this->input->post('city_id', TRUE));

                //->>login_auth = 1 for login sms auth
                //->>login_auth = 2 for login google auth

                $login_auth         = strip_tags($this->input->post('login_auth', TRUE));

                //$country_row        = $this->cities_model->get_country_call_code($country_id);
                //$calling_code       = $country_row->calling_code;
                //$userphone          = $phone;//$calling_code . $phone;

                $data               = array(
                                              'phone'                => $phone         ,
                                              'first_name'           => $first_name        ,
                                              'last_name'            => $last_name         ,
                                              //'Country_ID'           => $country_id        ,
                                              //'city'                 => $city_id           ,
                                              'login_auth'           => $login_auth        ,
                                              'login_auth_activated' => 1
                                        );


                if(isset( $_POST['password']) && $_POST['password']!= '')
                {
                    $data['password'] = strip_tags($this->input->post('password', TRUE));
                }

                //$old_phone = $user->phone ;
                //$new_phone = $userphone;

                if($this->ion_auth->update($user->id, $data))
                {

                    /***************insert user bank accounts ***************/
                    /*$banks_ids       = $this->input->post('bank_id', TRUE);
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
                   */
                    /**************************************************/
                  //  $this->session->set_flashdata('message', $this->ion_auth->messages());

                    //-->>> if update phone send sms verification
                    /*if($old_phone != $new_phone)
                    {
                        // to override the old user_bootstrap for old 'phone'
                        //$this->user_bootstrap->reload_user_data();
                        $this->_phone_activate();
                    }*/
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
                     redirect(base_url().'sell/dashboard','refresh');
                }
                redirect(base_url().'sell/dashboard','refresh');
            }
        }
    }

    private function _update_user_data_form($user)
    {
        require(APPPATH . 'includes/global_vars.php');

        $lang_id                = $this->data['lang_id'];
        $countries              = $this->cities_model->get_user_nationality_filter_data($lang_id);
        $country_row            = $this->cities_model->get_country_call_code($user->Country_ID);
        if(count($country_row)!= 0)
        {
        $calling_code           = $country_row->calling_code;

        $data['cities']         = $this->cities_model->get_country_cities($user->Country_ID , $lang_id);


         //////////////////////////////////////////////////
         //convert phone number to array

        //$calling_code_len       = strlen ( $calling_code );
        //$phone_str              = substr($user->phone, $calling_code_len);

        $data['user_phone']     = $user->phone ;
        $data['calling_code']   = $calling_code ;
       }
        $countries_array        = array();
        $countries_array[null]  = lang('choose');

        foreach($countries as $country)
        {
            $countries_array[$country->id]  = $country->name;
        }

        $data['user_countries']     = $countries_array;
        $data['user']           = $user ;

        $this->data['content']  = $this->load->view('Sell/profile', $data, true);
        $this->load->view('Sell/main_frame', $this->data);


    }

    private function _phone_activate()
    {
        //-->>Generate code
        $sms_code = rand(1000, 9999);

        $user = $this->admin_bootstrap->get_user_data();

        $data = array(
        		   'sms_code'               => $sms_code,
        		   'account_sms_activated'  => 0
        		);

        $this->ion_auth->update($user->id, $data);

        //-->>  send new sms_code
        $this->send_code_sms($sms_code, $user->phone);

        //-->>load form
       // redirect(base_url().'users/register/activate_user_phone/1','refresh');
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

    public function edit_my_store()
    {
         require(APPPATH . 'includes/global_vars.php');
        $this->session->set_userdata('site_redir', current_url());

        $stores   = $this->admin_bootstrap->get_user_available_stores(0, 1);

        $store_id_array = array();

        //Every user has access to edit one store
        $store_id = NULL;
        foreach($stores as $store)
        {
            $store_id_array[] = $store->store_id;
            $store_id = $store->store_id;
        }

        $id = $store_id;
        if(!is_null($id))
        {
            $id = intval($id);
            $validation_msg = false;

            if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
            {
                $languages = $this->input->post('lang_id');

                foreach($languages as $lang_id)
                {
                    $this->form_validation->set_rules('name['.$lang_id.']', lang('name'), 'required');
                    $this->form_validation->set_rules('description['.$lang_id.']', lang('description'), 'required');
                }

                $this->form_validation->set_rules('image', lang('thumbnail'), 'required');

                $this->form_validation->set_message('required', lang('required')."  : %s ");
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

                $validation_msg = true;
            }

            if($this->form_validation->run() == FALSE)
    		{
    		   $this->_edit_form($id, $validation_msg);
            }
            else
            {
                $image      = $this->input->post('image', true);
                $route      = strip_tags($this->input->post('route', true));
                $facebook   = strip_tags($this->input->post('facebook', true));
                $twitter    = strip_tags($this->input->post('twitter', true));
                $instagram  = strip_tags($this->input->post('instagram', true));
                $youtube    = strip_tags($this->input->post('youtube', true));
                $phone      = strip_tags($this->input->post('phone', true));

                $data    = array(
                                   'image'  => $image  ,
                                   'route'  => $route  ,
                                   'facebook' => $facebook,
                                   'twitter' => $twitter,
                                   'youtube' => $youtube,
                                   'instagram' => $instagram,
                                   'phone' => $phone
                                );

                $this->stores_model->update_store($id, $data);

                $name           = $this->input->post('name');
                $description    = $this->input->post('description');

                foreach($languages as $lang_id)
                {
                    $stores_translation_data = array(
                                                        'store_id'      => $id                      ,
                                                        'name'          => $name[$lang_id]          ,
                                                        //'address'       => $address[$lang_id]       ,
                                                        'description'   => $description[$lang_id]   ,
                                                        'lang_id'       => $lang_id
                                                     );

                    $this->stores_model->update_stores_translation($id, $lang_id, $stores_translation_data);
                }

                $_SESSION['success'] = lang('updated_successfully');
                $this->session->mark_as_flash('success');

                redirect('sell/dashboard/','refresh');
            }
        }
     }

     private function _edit_form($id, $validation_msg)
     {
        require(APPPATH . 'includes/global_vars.php');

        $this->data['id']           = $id;
        $general_data               = $this->stores_model->get_store_row($id);
        $data                       = $this->stores_model->get_store_translation_result($id);

        $filtered_data              = array();

        foreach($data as $row)
        {
            $filtered_data[$row->lang_id] = $row;
        }

        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required');
        }

        $this->data['data']             = $filtered_data;
        $this->data['general_data']     = $general_data;

        $this->data['content']          = $this->load->view('Sell/store_edit', $this->data, true);
        $this->load->view('Sell/main_frame',$this->data);
     }

    public function forbidden()
    {
        $this->data['content'] = $this->load->view('sell/403_error', $this->data, true);
        $this->load->view('sell/main_frame',$this->data);
    }
}
