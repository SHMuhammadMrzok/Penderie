<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
/**
 *
 */
class User_bootstrap
{
    public $CI ;

    public $lang;
    public $lang_id;
    public $lang_row;

    public $settings;
    public $site_settings;
    public $languages;
    public $countries;
    public $country_id;

    public $user_id;
    public $user;

    public $module;
    public $controller;
    public $method;

    public $module_id;
    public $controller_id;
    public $method_id;

    public $module_row;
    public $controller_row;
    public $method_row;

    public $remember_me;

    public $right_advertisments;

    public function __construct($params = array())
    {
        $this->CI = &get_instance();

        $this->CI->load->library('userlog');
        $this->CI->load->library('encryption');
        $this->CI->load->library('visits_log');
        $this->CI->load->library('currency');

        $this->CI->config->load('encryption_keys');

        $this->CI->load->model('root/lang_model');
        $this->CI->load->model('settings/admin_model');
        $this->CI->load->model('categories/cat_model');
        $this->CI->load->model('users/user_model');
        $this->CI->load->model('users/countries_model');
        $this->CI->load->model('currencies/currency_model');
        $this->CI->load->model('advertisements/advertisement_model');
        $this->CI->load->model('shopping_cart_model');
        $this->CI->load->model('products/products_model');
        $this->CI->load->model('global_model');
        $this->CI->load->model('home_model');
        $this->CI->load->model('root/modules_model');
        $this->CI->load->model('root/controllers_model');
        $this->CI->load->model('root/methods_model');
        $this->CI->load->model('stores/stores_model');

        if($this->CI->ion_auth->logged_in())
        {
            $this->user     = $this->CI->ion_auth->user()->row();
            $this->user_id  = $this->user->id;
        }

        else
        {
            $this->user     = 'guest';
            $this->user_id  = 0;
        }

        $setting_row        = $this->CI->admin_model->get_settings_general_data();

        /*************************Language*****************************/

        ///load lang
        $lang_id = isset($_SESSION['lang_id'])?$_SESSION['lang_id']:'';

        if($lang_id == '')
        {
            /// get default lang
            $lang_row = $this->CI->lang_model->get_language_result($setting_row->default_lang);
        }
        else
        {
            $lang_row = $this->CI->lang_model->get_language_row_by_id($lang_id);
        }

        $this->CI->lang->load($lang_row->language, $lang_row->language);

        $_SESSION['direction'] = $lang_row->direction;

        $this->lang     = $lang_row->language;
        $this->lang_row = $lang_row;
        $this->lang_id  = $lang_row->id;

        $_SESSION['lang_id']    = $this->lang_id;

        $this->settings      = $this->CI->admin_model->get_settings($this->lang_id);
        $this->site_settings = $this->CI->admin_model->get_site_settings();

        //print_
        /*****************************countries ***********************/

        $country_id = isset($_SESSION['country_id'])?$_SESSION['country_id']:'';

        /*if(isset($this->user->store_country_id) && $this->user->store_country_id != 0)
        {
            $this->country_id       = $this->user->store_country_id;
            $_SESSION['country_id'] = $this->country_id;
        }
        else
        */
        if($country_id == '' || $country_id == 0)
        {
            $this->country_id       = $setting_row->default_country;
            $_SESSION['country_id'] = $this->country_id;
        }
        else
        {
            $this->country_id  = $_SESSION['country_id'];
        }

        /*************************sms login activate*******************************/

        $this->module           = $this->CI->router->fetch_module();
        $this->controller       = $this->CI->router->class;
        $this->method           = $this->CI->router->method;

        $this->module_id        = $this->CI->modules_model->get_module_id($this->module);
        $this->controller_id    = $this->CI->controllers_model->get_controller_id($this->controller, $this->module_id);
        $this->method_id        = $this->CI->methods_model->get_method_id($this->method, $this->controller_id, $this->module_id);


        /*******************************************************************************/
        $this->_check_login_verification();
        $this->_userlog_operations();
        $this->_visits_log_operations();

//        $this->right_advertisments = $this->get_right_advertisments();

    } /****************************End Constracut***************************************/




   /***********************************Languages****************************************************/
    public function get_structure_languages()
    {
        return $this->CI->lang_model->get_active_structure_languages();
    }

    public function get_data_languages()
    {
        return $this->CI->lang_model->get_active_data_languages();
    }

    public function get_active_language_row()
    {
        return $this->lang_row;
    }

    public function get_active_lang()
    {
        return $this->lang;
    }

    public function get_languages()
    {
        return   $this->CI->global_model->get_languages();

    }

    public function get_active_country_id()
    {
        return $this->country_id;
    }

    public function get_active_lang_id()
    {
        return $this->lang_id;
    }

    public function get_countries()
    {
        return $this->CI->global_model->get_countries($this->lang_id);
    }

    public function get_active_country_row()
    {
        return $this->CI->global_model->get_active_country($this->lang_id, $this->country_id);
    }

    public function get_settings()
    {
        return $this->settings;
    }

    /********************Setters *******************************/
    public function set_lang_id($lang_id)
    {
        //Check if this id is exist

        $_SESSION['lang_id']    = $lang_id;
        $this->lang_id          = $lang_id;

        //make new function to reload lang row
    }

    public function set_back_redirection_url($url)
    {
        $_SESSION['site_redir'] = $url;
    }
    /************************************************************/
    public function get_module()
    {
        return $this->module;
    }

    public function get_controller()
    {
        return $this->controller;
    }

    public function get_method()
    {
        return $this->method;
    }
   /*********************top menu*******************************/

   public function get_categories()
   {
        $conds = array('categories.show_home'=> 1);
        return $this->CI->cat_model->get_categories($this->lang_id, 0, 1, $conds);
   }

   public function get_advertisments($location= 'top', $limit=0)
   {
        return $this->CI->advertisement_model->get_advertisments($this->lang_id, $location, $limit);
   }

   public function get_middle_advertisments()
   {
        return $this->CI->advertisement_model->get_advertisments($this->lang_id, 'middle');
   }

   public function get_bottom_advertisments()
   {
        return $this->CI->advertisement_model->get_advertisments($this->lang_id, 'bottom');
   }

   public function get_side_advertisments()
   {
        return $this->CI->advertisement_model->get_advertisments($this->lang_id, 'side');
   }

   public function get_ads_with_location($location, $limit)
   {
        return $this->CI->advertisement_model->get_advertisments($this->lang_id, $location, $limit);
   }

   public function get_home_products()
   {
        return  $this->CI->home_model->get_home_products($this->lang_id , $this->country_id);
   }

   /*****************************Log in*************************************/
   public function get_user_id()
   {
        return $this->user_id;
   }

   public function get_user_data()
   {
        return $this->user;
   }

   public function get_customer_group_id()
   {
       if($this->CI->ion_auth->logged_in())
       {
           return $this->user->customer_group_id;
       }
       else
       {
           return 0;
       }
   }

   public function get_customer_group_name()
   {
     if($this->CI->ion_auth->logged_in())
     {
         $user = $this->CI->ion_auth->user()->row();
         $group_data = $this->CI->customer_groups_model->get_row_data($user->customer_group_id, $this->lang_id);

         return $group_data->title;
     }
     else
     {
         return '';
     }

   }

   public function is_logged_in()
   {
        if($this->CI->ion_auth->logged_in())
        {
            return true;
        }
        else
        {
            return false;
        }
   }

   public function is_wholesaller()
   {
        if($this->is_logged_in())
        {
            $wholesaller_group_ids = json_decode($this->settings->wholesaler_customer_group_id);
            $user_group_id         = $this->user->customer_group_id;

            if(in_array($user_group_id, $wholesaller_group_ids))
            {
                return true;
            }
            else
            {
                return false;
            }

        }
        else
        {
            return false;
        }
   }

   /****************************** Reload user data **************************/
   public function reload_user_data()
   {
        if($this->CI->ion_auth->logged_in())
        {
            $this->user     = $this->CI->ion_auth->user()->row();
            $this->user_id  = $this->user->id;

            if($this->user->store_country_id != 0)
            {
                $this->country_id = $this->user->store_country_id;
                //require(APPPATH . 'includes/front_global_vars.php');
            }

            //$this->check_user_country_store_id($this->user_id, $this->country_id);
        }
        else
        {
            $this->user     = false;
            $this->user_id  = 0;
        }
   }

   public function check_user_country_store_id($user_id, $country_id)
   {
        $user_data      = $this->CI->user_model->get_row_data($user_id);
        $is_wholesaler  = $this->is_wholesaller();

        /*if($is_wholesaler)
        {
            if($this->country_id != $user_data->store_country_id)
            {
                $this->country_id       = $user_data->store_country_id;
                $_SESSION['country_id'] = $this->country_id;
            }
        }
        else
        */
         if($country_id != $user_data->store_country_id)
        {
            $user_current_balanace  = $this->get_any_user_balance($user_id);
            $user_store_country_id  = $user_data->store_country_id;
            $new_store_country_id   = $country_id;

            $user_new_data['store_country_id'] = $new_store_country_id;
            $this->CI->user_model->update_user($user_id, $user_new_data);

            $this->update_user_credit($user_current_balanace, $user_id, $user_store_country_id, $new_store_country_id);
        }
   }

   public function convert_balance($current_currency_val, $new_currency_val, $amount)
    {
        if($amount == 0) return 0;

        $factor      = $new_currency_val / $current_currency_val;
        $new_amount  = $amount * $factor;

        return $new_amount;
    }

   public function update_user_credit($user_current_balanace, $user_id, $user_store_country_id, $new_store_country_id)
   {
       $new_balance_to_current_currency = $this->CI->currency->update_user_credit($user_current_balanace, $user_id, $user_store_country_id, $new_store_country_id);
       return $new_balance_to_current_currency;
   }

   public function get_user_by_id($user_id)
   {
       $user = $this->CI->user_model->get_row_data($user_id);

       return $user;
   }

   /************************User Payment Options**************************/

   public function get_user_balance()
   {
        $this->CI->load->library('encryption');

        $secret_iv        = $this->get_user_id();
        $secret_key       = $this->CI->config->item('new_encryption_key');
        $enc_user_balance = $this->get_user_data()->user_balance;

        $user_balance     = $this->CI->encryption->decrypt($enc_user_balance, $secret_key, $secret_iv);
        $user_balance     = round($user_balance, 2);

        if($enc_user_balance == '')
        {
            $user_balance = 0;
        }

        return $user_balance;
   }

   public function get_any_user_balance($user_id)
    {
        $this->CI->load->library('encryption');

        $secret_iv        = $user_id;
        $secret_key       = $this->CI->config->item('new_encryption_key');
        $enc_user_balance = $this->get_user_by_id($user_id)->user_balance;

        $user_balance     = $this->CI->encryption->decrypt($enc_user_balance, $secret_key, $secret_iv);

        if($enc_user_balance == '')
        {
            $user_balance = 0;
        }

        return $user_balance;
    }

   public function get_user_reward_points()
   {
        $this->CI->load->library('encryption');

        $secret_iv         = $this->get_user_id();
        $secret_key        = $this->CI->config->item('new_encryption_key');
        $enc_reward_points = $this->get_user_data()->user_points;

        $reward_points     = $this->CI->encryption->decrypt($enc_reward_points, $secret_key, $secret_iv);

        if($enc_reward_points == '')
        {
            $reward_points = 0;
        }

        return $reward_points;
   }

   public function get_user_reward_points_value()
   {
        $reward_points              = $this->get_user_reward_points();
        $country_id                 = $_SESSION['country_id'];
        $country_reward_point_value = $this->CI->countries_model->get_reward_points($country_id);

        $user_reward_points_value   = $reward_points * $country_reward_point_value;

        return $user_reward_points_value;
   }

   public function convert_user_reward_points($rewrd_points_value)
   {
        $country_id                 = $_SESSION['country_id'];
        $country_reward_point_value = $this->CI->countries_model->get_reward_points($country_id);

        $user_reward_points         = round($rewrd_points_value / $country_reward_point_value, 2);

        return $user_reward_points;
   }

   public function encrypt_and_update_users_data($user_id, $field, $data)
   {
        $secret_key    = $this->CI->config->item('new_encryption_key');
        $secret_iv     = $user_id;

        $user_enc_data = $this->CI->encryption->encrypt($data, $secret_key, $secret_iv);
        $user_points_data[$field]  = $user_enc_data;

        return $this->CI->user_model->update_user_balance($user_id, $user_points_data);

   }

    public function pay_images()
    {
        $payment_images = $this->CI->global_model->payment_images();
        $payment_images =(array)$payment_images;

        return $payment_images;
    }
   ///////////////////////////////////////

    private function _check_login_verification()
    {
        // disable auto redirect in these regions
        if(
            $this->module != 'users' &&
            !in_array($this->controller, array('users', 'register')) &&
            !in_array($this->method, array('sms_activate', 'resend_sms_code', 'sms_activation', 'email_active', 'logout'))
        )
        {
            if ($this->CI->ion_auth->logged_in())
            {
                if(get_cookie($this->CI->config->item('identity_cookie_name', 'ion_auth')) || get_cookie($this->CI->config->item('remember_cookie_name', 'ion_auth')))
                {
                    $this->remember_me = true;
                }
                else
                {
                    $this->remember_me = false;
                }

                // if user has phone inactive
                if($this->user->account_sms_activated == 0)
                {
                    if(!$this->CI->session->userdata('is_mobile'))
                    {
                        redirect(base_url().'users/register/activate_user_phone','refresh');
                    }
                }

                // if user has sms login verification
                if($this->user->login_auth == 1 && $this->user->login_auth_activated == 0)
                {
            		if(!$this->remember_me)
                    {
                        // if logged in through mobile app dont redirect
                        if(!$this->CI->session->userdata('is_mobile'))
                        {
                            redirect(base_url().'users/users/check_login_sms_auth','refresh');
                        }
                    }
                }


                // if user has google 2 way login verification
                if($this->user->login_auth == 2 && $this->user->login_auth_activated == 0)
                {
                    if(!$this->remember_me)
                    {
                        if(!$this->CI->session->userdata('is_mobile'))
                        {
                            redirect(base_url().'users/users/google_auth_form','refresh');
                        }
                    }
                }

                if($this->user->active == 0)
                {
                    $_SESSION['message'] = lang('login_unsuccessful_not_active');

                    $this->CI->session->mark_as_flash('message');

                    redirect(base_url().'users/users/email_active','refresh');

                }
            }
        }
    }

    public function check_if_is_wholesaler()
    {
        $user_id = $this->user_id;

        if($user_id == 0)
        {
            return false;
        }
        else
        {
            $user_data            = $this->user;
            $wholesaler_group_ids = json_decode($this->settings->wholesaler_customer_group_id);

            if(in_array($user_data->customer_group_id, $wholesaler_group_ids))
            {
                if( $user_data->header == '' || $user_data->footer == '' || $user_data->sms_content == ''|| $user_data->sms_name == '' || $user_data->geocomplete == '' || $user_data->google_map_lat == '' || $user_data->google_map_lng == '' || $user_data->logo == '')
                {
                    return true;
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }
    }

    private function _userlog_operations()
    {
        if($this->CI->userlog->check_action_exists($this->method))
        {
            $module_id      = $this->module_id;
            $controller_id  = $this->controller_id;
            $data           = array();
            $data_id        = '';
            $admin          = 0;

            $this->CI->userlog->add_log($module_id, $controller_id, $this->method, $this->user_id, $data, $data_id, $admin);
        }
    }

    private function _visits_log_operations()
    {
        $this->CI->visits_log->add_log($this->module_id, $this->controller_id, $this->method_id, $this->user_id);
    }

    private function _delete_old_shopping_carts()
    {
        /**************************Delete Visitors Old Shopping Carts*************************/
        $time = time();

        $last_24_hours_time = $time - 86400;

        $carts = $this->CI->shopping_cart_model->to_be_deleted_shopping_carts($last_24_hours_time);

        if($carts)
        {
            foreach($carts as $row)
            {
                $this->CI->shopping_cart_model->delete_shopping_cart($row->id);
                $this->CI->shopping_cart_model->delete_cart_used_coupons($row->id);
            }
        }
    }

    public function get_most_sold_products()
    {
        $most_bought_array        = array();
        $most_bought_products_ids = $this->CI->orders_model->get_most_bought_products($this->lang_id, $this->country_id);

        if(count($most_bought_products_ids) != 0)
        {
            foreach($most_bought_products_ids as $product)
            {
                $product_details    = $this->CI->products_model->get_product_row_details($product->id, $this->lang_id, $this->country_id);
                $product_price_data = $this->CI->products_lib->get_product_price_data($product_details);
                $currency           = $this->CI->currency->get_country_currency_name($this->country_id, $this->lang_id);

                $product->{'price_before'} = $product_price_data[0];
                $product->{'price'}        = $product_price_data[1];
                $product->{'strike'}       = $product_price_data[4];
                $product->{'currency'}     = $currency;

                $most_bought_array[] = $product;

            }
        }

        return $most_bought_array;
    }

    public function get_menu_stores()
    {
        $menu_array   = array();
        $firsts_array = array();
        $first_stores_ids = array();

        $menu_first_stores  = $this->CI->stores_model->get_menu_first_stores($this->lang_id);

        foreach($menu_first_stores as $store)
        {
            if($store->store_id == $this->CI->config->item('first_store_id'))
            {
                unset($firsts_array[0]);
                $firsts_array[0] = $store;
            }

            if($store->store_id == $this->CI->config->item('second_store_id'))
            {
                unset($firsts_array[1]);
                $firsts_array[1] = $store;
            }

            if($store->store_id == $this->CI->config->item('third_store_id'))
            {
                unset($firsts_array[2]);
                $firsts_array[2] = $store;
            }

            if($store->store_id == $this->CI->config->item('fourth_store_id'))
            {
                unset($firsts_array[3]);
                $firsts_array[3] = $store;
            }

            if($store->store_id == $this->CI->config->item('fifth_store_id'))
            {
                unset($firsts_array[4]);
                $firsts_array[4] = $store;
            }

            $first_stores_ids[] = $store->store_id;
        }

        ksort($firsts_array);

        $menu_stores = $this->CI->stores_model->get_menu_stores($this->lang_id, $this->settings->menu_horizontal_limit, $first_stores_ids);

        $stores_array = array_merge($firsts_array, $menu_stores);

        foreach($stores_array as $key=>$store)
        {
            $cats_array         = array();
            $store_cats_data    = $this->CI->stores_model->get_store_available_cats_data($store->id, $this->lang_id);

            foreach($store_cats_data as $cat)
            {
                $cats_array[$cat->parent_id][] = $cat;
            }

            $store->{'store_cats'} = $cats_array;

            $menu_array[] = $store;
        }

        return $menu_array;
    }

    public function get_user_orders_count()
    {
      $orders_count = 0;
      $user_id = $this->user_id;

      if($user_id != 0)
      {
        $orders_count = $this->CI->orders_model->count_user_orders($user_id);
      }

      return $orders_count;
    }

    public function get_site_settings()
    {
        return $this->site_settings;
    }

}
