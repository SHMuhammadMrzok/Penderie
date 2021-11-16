<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
/**
 *
 */
class Admin_bootstrap
{
    public $CI ;
    public $crud;
    public $user;
    public $user_id;
    public $module;
    public $controller;
    public $method;
    public $settings;
    public $crud_method;

    public $lang;
    public $lang_id;
    public $lang_row;

    public $module_id;
    public $controller_id;
    public $method_id;

    public $module_row;
    public $controller_row;
    public $method_row;

    public $notifications;
    public $unread_notifications;

    public $crud_methods = array('list','add','edit','delete','read','print','export');
    public $crud_action_methods = array('insert','update','delete');
    

    public function __construct($params = array())
    {
        $this->CI = &get_instance();
        
        $this->CI->load->library('userlog');
        $this->CI->load->library('currency');
        $this->CI->load->library('visits_log');
        $this->CI->load->library('encryption');
        $this->CI->load->library('notifications');

        $this->CI->config->load('encryption_keys');

        $this->CI->load->model('global_model');
        $this->CI->load->model('notifications_model');
        $this->CI->load->model('users/user_model');
        $this->CI->load->model('users/users_model');
        $this->CI->load->model('users/countries_model');
        $this->CI->load->model('root/modules_model');
        $this->CI->load->model('root/controllers_model');
        $this->CI->load->model('root/methods_model');
        $this->CI->load->model('root/lang_model');
        $this->CI->load->model('orders/orders_model');
        $this->CI->load->model('orders/order_status_model');
        $this->CI->load->model('products/products_model');
        $this->CI->load->model('products/products_serials_model');
        $this->CI->load->model('currencies/currency_model');

        $this->CI->load->library('acl');

        /*************************Language*****************************/
        //Load Language

        $lang_id = isset($_SESSION['lang_id'])?$_SESSION['lang_id']:'';

        if($lang_id == '')
        {
            /// get default lang
            $settings = $this->CI->global_model->get_config();
            $lang_row = $this->CI->lang_model->get_language_result($settings->default_lang);
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

        $_SESSION['lang_id'] = $this->lang_id;

        /*******************************************************/

        $this->crud             = ( count($params) > 0 ) ? $params[0] : false;
        $this->crud_method      = ( count($params) > 0 ) ? $this->crud->getState() : false;
        //$this->module           = $this->CI->router->fetch_module();
        $opened_module          = $this->CI->router->fetch_module();
        $this->controller       = $this->CI->router->class;
        $this->method           = $this->CI->router->method;

        $method                 = $this->method;

        if($this->crud != false && in_array($this->crud_method, $this->crud_methods) && $this->method =='index')
        {
            $method             = ($this->crud_method == 'list')? $this->method : $this->crud_method;
        }
        
        $controller_data = $this->CI->controllers_model->get_controller_by_module_path($opened_module, $this->controller);
        
        $this->module = isset($controller_data->module) ? $controller_data->module : ""; // $controller_data->module;
        $this->opened_module = $opened_module;
        
                
        $this->module_id =  isset($controller_data->module_id) ? $controller_data->module_id : ""; // $controller_data->module_id;
        $this->controller_id = isset($controller_data->id) ? $controller_data->id : ""; // $controller_data->id;
        
        //echo"<pre>";print_r($controller_data);die();
        
        //$this->module_id        = $this->CI->modules_model->get_module_id($this->module);
        //$this->controller_id    = $this->CI->controllers_model->get_controller_id($this->controller, $this->module_id);
        $this->method_id        = $this->CI->methods_model->get_method_id($method, $this->controller_id, $this->module_id);
        
        //echo $this->method_id;die();
        
        $this->module_row       = $this->CI->modules_model->get_module($this->module_id,$this->lang_id);
        $this->controller_row   = $this->CI->controllers_model->get_controller($this->controller_id,$this->lang_id);
        $this->method_row       = $this->CI->methods_model->get_method($this->method_id,$this->lang_id);
        

        $this->check_user_login();
        $this->applay_permissions();
        $this->userlog_operations();
        $this->visits_log_operations();

        if (!$this->CI->input->is_ajax_request())
        {
            $_SESSION['last_location'] = base_url(uri_string());
        }

        $this->settings = $this->CI->admin_model->get_settings($this->lang_id);

        if($this->CI->ion_auth->in_group(7))
        {
            $user_stores = $this->get_user_stores();

            if($this->controller == 'admin')
            {
                redirect('sell', 'refresh');
            }

            $user_active_stores = $this->get_user_available_stores(0, 1);
            if(count($user_active_stores) ==0 && $this->controller != 'sell')
            {
                redirect('sell/dashboard');
            }
        }
        else
        {
            $user_stores = array();
        }

        $this->notifications        = $this->CI->notifications_model->get_admin_notifications(10, $user_stores);
        $this->unread_notifications = $this->CI->notifications_model->get_admin_unread_notifications($user_stores);



        //echo '<pre>';print_r($user_stores);
        //echo $this->get_user_data()->id;
        //die();
    }
   /****************************End Constracut***************************************/

   public function get_user_stores()
   {
        $stores_ids  = array();
        $user_stores = $this->CI->user_model->get_user_stores_ids($this->user_id);

        if(count((array)$user_stores) != 0)
        {
            foreach($user_stores as $store)
            {
                $stores_ids[] = $store->store_id;
            }
        }

        return $stores_ids;

   }

   public function check_user_store_owner()
   {
        if($this->CI->ion_auth->in_group(7))
        {
            return true;
        }
        else
        {
            return false;
        }
   }

    public function check_user_login()
    {
        if (!$this->CI->ion_auth->logged_in())
		{
		    $_SESSION['redir'] = current_url();

			redirect('admin/login');
		}
        else
        {
            if($this->CI->ion_auth->in_group(2))   // users default group
            //if(!$this->CI->ion_auth->is_admin())
            {
                redirect(base_url());
            }
            else
            {
    		    $this->user       = $this->CI->ion_auth->user()->row();
                $this->user_id    = $this->user->id;
            }
		}

    }

    public function check_if_driver()
    {
        $drivers_group = $this->settings->drivers_group_id;
         if($this->CI->user_model->check_user_in_group($this->user_id, $drivers_group))
         {
             return true;
         }
         else
         {
             return false;
         }
    }

    public function get_user_data()
    {
        return $this->user;
    }

    public function is_wholesaller()
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

    public function check_if_wholesaler_group($group_id)
    {
        $wholesaller_group_ids = json_decode($this->settings->wholesaler_customer_group_id);
        if(in_array($group_id, $wholesaller_group_ids))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /***********************************User Log****************************************************/
    public function userlog_operations()
    {
        $method = $this->method;

        if($this->crud != false)
        {
            if($this->method == 'index' && in_array($this->crud_method, $this->crud_action_methods))
            {
                $method = $this->crud_method;
            }
        }

        if($this->CI->userlog->check_action_exists($method))
        {
            $module_id      = $this->module_id;
            $controller_id  = $this->controller_id;
            $data           = array();
            $data_id        = '';
            $admin          = 1;

            $this->CI->userlog->add_log($module_id,$controller_id,$method,$this->user_id,$data,$data_id ,$admin);
        }
    }

    public function visits_log_operations()
    {
        $this->CI->visits_log->add_log($this->module_id, $this->controller_id, $this->method_id, $this->user_id, 1);
    }



   /***************************Permissions************************************/
    public function get_menu_permissions()
    {

        $user_permissions               = $this->CI->acl->get_user_permissions($this->user_id);
        $user_controllers_permissions   = array();
        $permissions_controllers_ids    = array();

        foreach($user_permissions as $key=>$permission)
        {
            if(in_array($permission->method, array('index','add')))
            {
                $method_data = $this->CI->permissions_model->get_method_data($permission->method_id);

                if($method_data->store_related == 1)
                {
                    // check if has store permissions
                    $store_permission_check = $this->CI->acl->check_user_store_method($this->user_id, $permission->permission_id);

                    if(!$store_permission_check)
                    {
                        unset($user_permissions[$key]);
                    }
                    else
                    {
                        $user_controllers_permissions["{$permission->controller_id}"][] = $permission->method;
                        $permissions_controllers_ids[] = $permission->controller_id;
                    }
                }
                else
                {
                    $user_controllers_permissions["{$permission->controller_id}"][] = $permission->method;
                    $permissions_controllers_ids[] = $permission->controller_id;
                }

            }
        }

        $permissions_controllers_ids = array_unique($permissions_controllers_ids);

        $controllers = $this->CI->controllers_model->get_controllers($this->lang_id);

        $modules                = array();
        $filtered_controllers   = array();

        foreach($controllers as $controller)
        {
            if(in_array($controller->id, $permissions_controllers_ids))
            {
                $modules["{$controller->module_id}"]    = array('module'=>$controller->module, 'module_name'=>$controller->module_name,'module_icon_class'=>$controller->module_icon_class);
                $controller->{"methods"}                = $user_controllers_permissions["{$controller->id}"];
                $filtered_controllers[]                 = $controller;
            }

        }

       return array($modules, $filtered_controllers);
    }

    public function applay_crud_permissions()
    {
        $method = $this->method;

        if(in_array($this->crud_method, $this->crud_methods))
        {
            $method = $this->crud_method;
        }

        if(!$this->CI->acl->has_permission($this->user_id, $this->method_id))
        {
            foreach($this->crud_methods as $crud_method)
            {
                if($method == $crud_method)
                {
                    $func_name = 'unset_'.$crud_method;
                    $this->crud->$func_name();
                }
            }

        }
        else
        {
            if($method=='list')
            {
                foreach($this->crud_methods as $crud_method)
                {
                    $method_id = $this->CI->methods_model->get_method_id($crud_method, $this->controller_id, $this->module_id);

                    if(!$this->CI->acl->has_permission($this->user_id, $method_id))
                    {
                        $func_name = 'unset_'.$crud_method;
                        $this->crud->$func_name();
                    }
                }
            }
            elseif(in_array($method, array('add','edit')))
            {
                $method_id = $this->CI->methods_model->get_method_id('list', $this->controller_id, $this->module_id);

                if(!$this->CI->acl->has_permission($this->user_id, $method_id))
                {
                    $this->crud->unset_back_to_list();
                }
            }


        }

    }

    public function applay_permissions()
    {
        if($this->crud != false)
        {
            if($this->method == 'index' && in_array($this->crud_method, $this->crud_methods))
            {
                $this->applay_crud_permissions();
            }
        }

        if(!$this->CI->acl->has_permission($this->user_id, $this->method_id))
        {
            die('You Do not have permissions to access this page');
        }
    }

    public function has_permission($permission_method)
    {
        $method_id = $this->CI->methods_model->get_method_id($permission_method, $this->controller_id, $this->module_id);

        return $this->CI->acl->has_permission($this->user_id, $method_id);
    }

    /**********************Get User*******************************/
    public function get_user_id()
    {
        return $this->user_id;
    }

    public function get_user()
    {
        return $this->user;
    }

    public function get_user_by_id($user_id)
    {
        $this->CI->load->model('users/users_model');

        $user = $this->CI->users_model->get_user($user_id);

        return $user;
    }


    /**********************Get modules*******************************/
    public function get_module()
    {
        return $this->module;
    }
    
    public function get_opened_module()
    {
        return $this->opened_module;
    }

    public function get_controller()
    {
        return $this->controller;
    }

    public function get_controller_id()
    {
        return $this->controller_id;
    }

    public function get_method()
    {
        return $this->method;
    }

    public function get_method_id()
    {
        return $this->method_id;
    }

    public function get_module_row()
    {
        return $this->module_row;
    }

    public function get_controller_row()
    {
        return $this->controller_row;
    }

    public function get_method_row()
    {
        return $this->method_row;
    }

    public function get_a_controller_method_id($controller_id, $method)
    {
        return $this->CI->methods_model->get_a_controller_method_id($controller_id, $method);
    }
   /***********************************Languages****************************************************/
    public function get_structure_languages()
    {
        return $this->CI->lang_model->get_active_structure_languages();
    }

    public function get_data_languages()
    {
        return $this->CI->lang_model->get_active_data_languages();
    }

    public function get_current_lang_id()
    {
        return $this->lang_id;
    }

    public function get_active_language_row()
    {
        return $this->lang_row;
    }

   public function get_admin_notification()
    {
        return $this->notifications;
    }

    public function get_admin_unread_notifications()
    {
        return $this->unread_notifications;
    }

    /************************Countries*********************************/

    public function get_countries_data()
    {
        $lang_id = $this->lang_id;
        return $this->CI->countries_model->get_active_countries_data($lang_id);
    }

    /****************Dashboard Statistics**********************/

    public function get_top_selling_products($stores_ids=array())
    {
        $products_ids      = $this->CI->orders_model->get_ordered_products($stores_ids);
        $product_ids_array = array();

        foreach($products_ids as $product)
        {
            $product_count = $this->CI->orders_model->get_product_count($product->product_id);
            $product_ids_array[$product->product_id] = $product_count;
        }

        arsort($product_ids_array); // desc sort the array

        $products_data = array();
        $i     = 1;
        $limit = 10;     // get top ten records only

        foreach( $product_ids_array as $product_id=>$product_count)
        {
            if( $i >= $limit) break;

            $product_name  = $this->CI->products_model->get_product_name($product_id, $this->lang_id);
            $products_data[] = array(
                                        'product_id' => $product_id   ,
                                        'name'       => $product_name ,
                                        'count'      => $product_count
                                    );
            $i++;
        }

        return $products_data;

    }

    public function get_new_customers()
    {
        $limit      = 10;
        $users_data = $this->CI->users_model->get_users_names($limit);

        $new_customers_array = array();

        foreach($users_data as $user)
        {
            $orders_count      = $this->CI->orders_model->get_user_orders_count($user->id);
            $orders_total_cost = $this->CI->orders_model->get_user_orders($user->id);
            $total_cost        = 0;

            if($orders_total_cost)
            {
                foreach($orders_total_cost as $order)
                {
                    $total_cost += $order->final_total;
                }
            }


            $new_customers_array[] = array(
                                            'id'           => $user->id,
                                            'username'     => $user->first_name.' '.$user->last_name,
                                            'orders_count' => $orders_count,
                                            'total_cost'   => $total_cost
                                          );

        }

        return $new_customers_array;

    }

    public function get_latest_orders($stores_ids)
    {
        $orders_array   = array();
        $limit          = 10;
        $orders_data    = $this->CI->orders_model->get_limited_orders($limit, $this->lang_id,$stores_ids);

        foreach ($orders_data as $order)
        {
            if($order->order_status_id == 1)
            {
                $label = 'success';
            }
            elseif($order->order_status_id == 2)
            {
                $label = 'warning';
            }
            elseif($order->order_status_id == 3)
            {
                $label = 'danger';
            }
            elseif($order->order_status_id == 4)
            {
                $label = 'danger';
            }
            elseif($order->order_status_id == 8)
            {
                $label = 'info';
            }

            $order->{'label'} = $label;

            $orders_array[] = $order;
        }

        return $orders_array;

    }

    public function get_conditioned_orders($conditions, $stores_ids=array())
    {
        $limit       = 10;
        $orders_data = $this->CI->orders_model->get_conditioned_orders($limit, $conditions, $this->lang_id, $stores_ids);

        return $orders_data;
    }

    public function get_settings()
    {
        return $this->settings;
    }

    public function get_user_balance($user_id)
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

     public function get_user_reward_points($user_id)
    {
        $this->CI->load->library('encryption');

        $secret_iv         = $user_id;
        $secret_key        = $this->CI->config->item('new_encryption_key');
        $enc_reward_points = $this->get_user_by_id($user_id)->user_points;

        $reward_points     = $this->CI->encryption->decrypt($enc_reward_points, $secret_key, $secret_iv);

        if($enc_reward_points == '')
        {
            $reward_points = 0;
        }

        return $reward_points;
    }

    public function get_user_reward_points_value($user_id, $country_id)
    {
        $user_data                  = $this->get_user_by_id($user_id);
        $reward_points              = $this->get_user_reward_points($user_id);
        $country_reward_point_value = $this->CI->countries_model->get_reward_points($country_id);

        $user_reward_points_value   = $reward_points * $country_reward_point_value;

        return $user_reward_points_value;
   }

   public function convert_into_reward_points($country_id, $rewrd_points_value)
   {
        $country_reward_point_value = $this->CI->countries_model->get_reward_points($country_id);

        // $user_reward_points         = round($rewrd_points_value / $country_reward_point_value, 2); // Basic Code

        /**
         * Edit Code
         * To Fix Case of : country_reward_point_value = 0
         * */ 
        $user_reward_points         = round($rewrd_points_value, 2);
        if($country_reward_point_value > 0)
        {
            $user_reward_points         = round($rewrd_points_value / $country_reward_point_value, 2);
        }

        return $user_reward_points;
   }



   public function encrypt_and_update_users_data($user_id, $field, $data)
   {
        $secret_key    = $this->CI->config->item('new_encryption_key');
        $secret_iv     = $user_id;

        $user_enc_data     = $this->CI->encryption->encrypt($data, $secret_key, $secret_iv);
        $user_data[$field] = $user_enc_data;

        return $this->CI->user_model->update_user_balance($user_id, $user_data);

   }

   public function convert_user_reward_points($rewrd_points_value, $country_id)
   {
        $country_reward_point_value = $this->CI->countries_model->get_reward_points($country_id);

        $user_reward_points         = round($rewrd_points_value / $country_reward_point_value, 2);

        return $user_reward_points;
   }

   /******************************Currency Functions**********************************/

   public function get_amount_with_default_currency($amount, $user_store_country_id)
   {
       $new_balance_to_default_currency = $this->CI->currency->get_amount_with_default_currency($amount, $user_store_country_id);

       return round($new_balance_to_default_currency, 2);
   }

   public function get_default_currency_symbol()
   {
        $default_currency_data = $this->CI->currency_model->get_default_currency_data();

        return $default_currency_data->currency_symbol;
   }

   /***************************************************************************************/

   public function get_user_available_stores($method_id=0, $all=0)
   {
        $user_id = $this->user_id;


        if($all == 1)
        {
            // get user stores without permissions
            $user_stores = $this->CI->user_model->get_user_stores_data($user_id);
        }
        else
        {
            if($method_id == 0)
            {
                $method_id = $this->method_id;
            }

            //check if user has stores
            $user_stores_count = $this->CI->user_model->user_stores_count($user_id);

            if($user_stores_count > 0)
            {
                
                // get available stores
                $user_stores = $this->CI->user_model->get_user_permitted_stores_ids_per_method($user_id, $method_id, $this->controller_id, $this->lang_id);

            }
            else
            {
                if($this->CI->ion_auth->in_group(7))
                {

                    //return empty array
                    $user_stores = array();
                }
                else
                {
                    $this->CI->load->model('stores/stores_model');
                    $user_stores = $this->CI->stores_model->get_all_stores($this->lang_id);

                }
            }
        }

        return $user_stores;
   }

   /*public function get_user_available_stores($method_id=0)
   {
        $user_id = $this->user_id;

        if($method_id == 0)
        {
            $method_id = $this->method_id;
        }

        //check if user has stores
        $user_stores_count = $this->CI->user_model->user_stores_count($user_id);

        if($user_stores_count > 0)
        {
            // get available stores
            $user_stores = $this->CI->user_model->get_user_permitted_stores_ids_per_method($user_id, $method_id, $this->controller_id, $this->lang_id);

        }
        else
        {
            //get all stores
            $this->CI->load->model('stores/stores_model');
            $user_stores = $this->CI->stores_model->get_all_stores($this->lang_id);
        }

        return $user_stores;
   }*/

}
