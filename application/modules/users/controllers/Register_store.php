<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Register_store extends CI_Controller
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
        $this->load->model('permissions_model');
        $this->load->model('stores/stores_model');
        $this->load->model('stores/packages_model');
        $this->load->model('static_pages/static_pages_model');

        $this->load->library('stores/stores_lib');

        $this->load->library('notifications');

        require(APPPATH . 'includes/front_end_global.php');

    }



    public function index($ajax=0, $user_id=1)
    {
        $validation_msg = false;

        if((strtoupper($_SERVER['REQUEST_METHOD']) == 'POST'))// && (isset($_POST['user_store'])) && ($_POST['user_store'] == $user_id) )
        {

            $validation_msg   = true;
            $languages = $this->input->post('lang_id', true);

    	    if(count($languages) != 0)
            {
                foreach($languages as $lang_id)
                {
                    $this->form_validation->set_rules('name['.$lang_id.']', lang('name_of_store'), 'required');
                   // $this->form_validation->set_rules('address['.$lang_id.']', lang('address'), 'required');
                    $this->form_validation->set_rules('description['.$lang_id.']', lang('description'), 'required');
                }
            }

            //$this->form_validation->set_rules('route', lang('store_route'), "required|is_unique[stores.route]");
            $this->form_validation->set_rules('phone', lang('phone'), 'required|integer');
            //$this->form_validation->set_rules('package_id', lang('package_data'), 'required');
            $this->form_validation->set_rules('first_name', lang('first_name'), 'required');
            $this->form_validation->set_rules('last_name', lang('last_name'), 'required');
            $this->form_validation->set_rules('email', lang('email'), "required|valid_email|is_unique[users.email]");
            $this->form_validation->set_rules('phone', lang('phone'), "required|integer|is_unique[users.phone]");
            $this->form_validation->set_rules('country_id', lang('country'), 'required');
            $this->form_validation->set_rules('password', lang('password'), 'required|matches[conf_password]|min_length[6]|max_length[12]');
            $this->form_validation->set_rules('conf_password', lang('confirm_password'), 'required');

            $this->form_validation->set_rules('i_agree', lang('terms_conditions'), 'required');


            $this->form_validation->set_message('required', lang('required')."  : %s ");
            $this->form_validation->set_message('integer', lang('is_integer')."  : %s ");
            $this->form_validation->set_message('is_unique', lang('is_unique')." : %s ");
            $this->form_validation->set_error_delimiters('<div class="error" style="color:red">', '</div>');

        }

        if($this->form_validation->run() == FALSE)
    	  {
    	     if($ajax == 1)
           {
                $result     = 0;
                $message    = validation_errors();

                $_SESSION['error_message'] = $message;
                $this->session->mark_as_flash('error_message');
           }
           else
           {
                $this->_view_form($validation_msg, $user_id);
           }

        }
        else
        {
            $name           = $this->input->post('name', true);
            //$address        = $this->input->post('address', true);
            $description    = $this->input->post('description', true);
            //$route          = strip_tags($this->input->post('route', true));
            $store_lat      = strip_tags($this->input->post('store_lat', true));
            $store_lng      = strip_tags($this->input->post('store_lng', true));
            $first_name     = strip_tags($this->input->post('first_name', true));
            $last_name      = strip_tags($this->input->post('last_name', true));
            $country_id     = strip_tags($this->input->post('country_id', true));
            $store_image    = strip_tags($this->input->post('image', true));
            $id_image       = strip_tags($this->input->post('image2', true));

            /*$facebook       = strip_tags($this->input->post('facebook', true));
            $twitter        = strip_tags($this->input->post('twitter', true));
            $instagram      = strip_tags($this->input->post('instagram', true));
            $youtube        = strip_tags($this->input->post('youtube', true));
            */
            $package_id     = intval($this->input->post('package_id', true));
            $username       = $this->generateRandomString();
            $email          = strip_tags($this->input->post('email', TRUE));
            $phone          = strip_tags($this->input->post('phone', TRUE));
            $password       = strip_tags($this->input->post('password'));

            $new_user_customer_group_id = $this->config->item('new_user_customer_group_id');

            //  create new user

            $store_country_id = $this->data['country_id'];


            if($phone[0] == 0 && $phone[1] == 0)
            {
                $phone = substr($phone, 2);
            }

            if($phone[0]==9 && $phone[1] == 6 && $phone[2] == 6)
            {
                $phone = substr($phone, 3);
            }

            $additional_data  = array(
                                      'phone'                   => $phone     ,
                                      'Country_ID'              => $country_id,
                                      'first_name'              => $first_name,
                                      'last_name'               => $last_name,
                                      'customer_group_id'       => $new_user_customer_group_id ,
                                      'store_country_id'        => $store_country_id    ,
                                      'active'                  => 1,
                                      'account_sms_activated'   => 1,
                                      'login_auth_activated'    => 0,
                                      'login_auth'              => 0,
                                      'id_image'                => $id_image

                                   );

            $store_users_group = 7;
            $group = array('id'=>$store_users_group);

            $user_id = $this->ion_auth->register($username, $password, $email, $additional_data,$group);
            if($user_id)
            {
                //-->>Send notification
                $this->_send_new_registered_notification($user_id);

                $txt_msg = lang('store_activation_msg');//lang('email_activation_sent');
            }
            else
            {
                $txt_msg = $this->ion_auth->errors();
            }


            // create store
            $route = 'store_'.$user_id;
            $general_data = array(
                                    'package_id'    => $package_id  ,
                                    'phone'         => $phone       ,
                                    'accepted'      => 0            ,
                                    'added_by_user' => 1            ,
                                    'route'         => $route       ,
                                    'store_lat'     => $store_lat   ,
                                    'store_lng'     => $store_lng   ,
                                    'image'         => $store_image ,
                                    'user_id'       => $user_id//$this->data['user_id']

                                );

            $store_id = $this->stores_lib->insert_store($general_data);

            foreach($languages as $lang_id)
            {
                $stores_translation_data = array(
                                                    'store_id'      => $store_id                ,
                                                    'name'          => $name[$lang_id]          ,
                                                    //'address'       => $address[$lang_id]       ,
                                                    'description'   => $description[$lang_id]   ,
                                                    'lang_id'       => $lang_id
                                                 );

                $this->stores_lib->insert_store_translation($stores_translation_data);
            }

            // insert user store id
            $user_store_data = array(
                                        'user_id' => $user_id,
                                        'store_id' => $store_id
                                    );
            $this->user_model->insert_user_store($user_store_data);

            // insert store cats
            $cats = $this->cat_model->get_categories($this->data['lang_id']);

            foreach($cats as $cat)
            {

                $cat_store_data = array(
                                        'store_id'      => $store_id,
                                        'category_id'   => $cat->category_id
                                    );

                $this->stores_model->save_store_cats($cat_store_data);

            }

            // insert store permissions
            $controllers            = $this->controllers_model->get_store_controllers($this->data['lang_id']);
            $modules                = array();
            $controller_permissions = array();

            foreach($controllers as $controller)
            {
                //ticket status and tickets categories are not included
                if(($controller->id != 53) && ($controller->id != 54))
                {
                     $modules["{$controller->module_id}"]         = array(
                                                                            'module'            => $controller->module,
                                                                            'module_name'       => $controller->module_name,
                                                                            'module_icon_class' => $controller->module_icon_class
                                                                        );

                     $permissions = $this->permissions_model->get_permissions($controller->id, $lang_id);



                     foreach($permissions as $row)
                     {
                        if($row->permission_id != 200 && $row->permission_id != 223)
                        {
                            $user_store_data = array(
                                                        'store_id'      => $store_id,
                                                        'user_id'       => $user_id ,
                                                        'permission_id' => $row->permission_id,
                                                        'controller_id' => $controller->id
                                                    );
                            $this->permissions_model->save_user_store_permissions($user_store_data);
                        }

                     }


                }
            }

            $this->data['msg']  = lang('user_inserted_store_succefully_msg');
            $this->data['type'] = 'success';

            if($ajax == 1)
            {
                $result     = 1;
                $message    = lang('user_inserted_store_succefully_msg');

                //$_SESSION['suc_message'] = $message;
                //$this->session->mark_as_flash('suc_message');
            }
            else
            {
                $this->data['content'] = $this->load->view('msg_view', $this->data, true);
                $this->load->view('site/main_frame', $this->data);
            }
        }

        if($ajax == 1)
        {
            echo json_encode(array($result, $message));
        }

    }

    private function _view_form($validation_msg, $user_id)
    {
        $packages_array = array();

        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }

        $packages_data  = $this->packages_model->get_all_packages_data($this->data['lang_id']);
        $info_text      = $this->static_pages_model->get_row_data(7, $this->data['lang_id']);

        /*foreach($packages_data as $package)
        {
            if($package->products_limit == -1)
            {
                $products_limit = lang('unlimited');
            }
            else
            {
                $products_limit = $package->products_limit;
            }

            if($package->users_limit == -1)
            {
                $users_limit = lang('unlimited');
            }
            else
            {
                $users_limit = $package->users_limit;
            }

            if($package->payment_type == 'monthly')
            {
                $type = lang('monthly_fees');
                $cost = $package->monthly_fees;
            }
            elseif($package->payment_type == 'amount')
            {
                $type = lang('amount_per_order');
                $cost = $package->amount_per_order;
            }
            elseif($package->payment_type == 'percent')
            {
                $type = lang('percent_per_order');
                $cost = $package->percent_per_order.' %';
            }
            elseif($package->payment_type == 'free')
            {
                $type = lang('free');
                $cost = 0;
            }
            elseif($package->payment_type == 'categories_commission')
            {
                $type = lang('commission');
                $cost = '';
            }

            $package->{'products_limit'} = $products_limit;
            $package->{'users_limit'}    = $users_limit;
            $package->{'type'}           = $type;
            $package->{'cost'}           = $cost;

            $packages_array[] = $package;
        }
        */

        $countries       = $this->cities_model->get_user_nationality_filter_data($this->data['lang_id']);
        $countries_array = array();
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

        $this->data['user_countries'] = $countries_array;
        $this->data['packages_data'] = $packages_data;
        $this->data['new_user_id']   = $user_id;
        $this->data['info_text']     = $info_text;
        $this->data['terms_text']    = $info_text->page_text;

        $this->load->view('Sell/register', $this->data);

        //$this->data['content'] = $this->load->view('stores/stores_register_form', $this->data, true);
        //$this->load->view('sell/main_frame',$this->data);
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

        /*if($this->sms_activate_login($id))
        {
            //$_SESSION['message'] = lang('store_activation_msg');//$this->ion_auth->messages();
            //$this->session->mark_as_flash('message');
        }
        else
        {
            //$_SESSION['notification_error'] = lang('sms_active_code_notsend');
            //$this->session->mark_as_flash('notification_error');
        }*/
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

    public function check_email_unique()
    {
      $email = strip_tags($this->input->post('email', true));

      $conditions_array = array('email'=>$email);
      $is_exist = $this->users_model->get_result_data_where('users', 'row', $conditions_array);

      if(count($is_exist) != 0)
      {
        $result = lang('email_exist');
      }
      else {
        $result = '';
      }

      echo $result;
    }
/*********************************************************/
}
