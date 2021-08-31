<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
/**
 * 
 */
class Wholesaler_api_lib
{
    public $CI ;
    public $settings;
    public $lang_id;
    
    public function __construct()
    {
        $this->CI = &get_instance();
        
        $this->CI->load->library('serials_lib');
        $this->CI->load->library('encryption');
        $this->CI->load->library('products_lib');
        $this->CI->load->library('user_bootstrap');
        $this->CI->load->library('notifications');
        
        $this->CI->config->load('encryption_keys');
        
        $this->CI->load->model('global_model');
        $this->CI->load->model('general_model');
        $this->CI->load->model('users/user_model');
        
        $this->CI->load->model('users/countries_model');
        $this->CI->load->model('orders/orders_model');
        $this->CI->load->model('orders/order_status_model');
        $this->CI->load->model('products/products_model');
        $this->CI->load->model('products/products_serials_model');
        $this->CI->load->model('affiliate/admin_affiliate_model');
        $this->CI->load->model('payment_options/user_balance_model');
        $this->CI->load->model('payment_options/payment_methods_model');
        
        // Get settings table data
        $this->settings = $this->CI->global_model->get_config();
        $this->lang_id  = $this->settings->default_lang;
    }
    
    public function check_user_validation($email, $password, $posted_device_id, $posted_security_code, $lang_id)
    {
        // check login
        $user_data = $this->_get_user_data($email, $password);
        
        if(! $user_data)
        {
            $message = $this->CI->general_model->get_lang_var_translation('login_unsuccessful', $lang_id);    
            $output  = array(
                              'message'  => $message,
                              'response' => 0
                           );
            
            //return $output ;
        }
        else
        {
            // check user auth
            $auth_check = $this->_check_user_auth($user_data, $lang_id);
            
            if($auth_check == 3)  // account SMS not activated
            {
                $message = $this->CI->general_model->get_lang_var_translation('sms_activation_required', $lang_id);
            
                $output  = array(
                                  'message'  => $message ,
                                  'response' => 4
                               );
            } 
            else if($auth_check == 2)   //SMS Login auth required
            {
                $message = $this->CI->general_model->get_lang_var_translation('sms_auth_required', $lang_id);
            
                $output  = array(
                                  'message'  => $message ,
                                  'response' => 3
                               );
            }
            else
            {
                //check device id
                
                $posted_device_id = $this->decrypt_posted_code($posted_device_id); 
                $device_id_check  = $this->_check_device_id($user_data->id, $posted_device_id, $user_data->device_id);
                
                //Device ID not correct
                if(! $device_id_check)
                {
                    $message = $this->CI->general_model->get_lang_var_translation('device_id_error', $lang_id);
                    
                    $output  = array(
                                      'message'  => $message,
                                      'response' => 0
                                    );
                }
                else
                {
                    $security_code_check = $this->_check_user_security_code($user_data->security_code, $posted_security_code, $lang_id, $user_data->id);
                    /**
                        #security code check result
                        
                        1 => security code correct
                        2 => security code needed 
                        3 => security code not correct
                    **/
                    
                    if($security_code_check == 2)
                    {
                        $message = $this->CI->general_model->get_lang_var_translation('security_code_needed', $lang_id);
                
                        $output  = array(
                                           'message'  => $message,
                                           'response' => 2
                                        );
                        
                        //return $output;
                    }
                    elseif($security_code_check == 3)
                    {
                        $message = $this->CI->general_model->get_lang_var_translation('security_code_error', $lang_id);
                
                        $output  = array(
                                           'message'  => $message,
                                           'response' => 0
                                        );
                        
                        //return $output;
                    }
                    else
                    {
                        $output = true;
                    }
                    
                }
            }
        }
        
        return $output;
    }
    
    private function _get_user_data($email, $password)
    {
        $password = $this->decrypt_posted_code($password);
        
        if($this->CI->ion_auth->login($email, $password))
        {
            $user_data = $this->CI->ion_auth->user()->row();
            return $user_data;
        }
        else
        {
            return false;
        }
    }
    
    private function _check_user_auth($user_data, $lang_id)
    {
        if($user_data->account_sms_activated == 0)
        {            
            $result = 3;
        }
        elseif($user_data->login_auth == 1)
        {
            #Resend SMS code
            $this->resend_user_sms_code($user_data->id, $lang_id);
            
            $auth_data['login_auth_activated'] = 0;
            $this->CI->user_model->update_user($user_data->id, $auth_data);
            
            $result = 2;
        }
        else
        {
            $result = 1;
        }
        
        return $result;
    }
    
    private function _check_device_id($user_id, $posted_device_id, $user_device_id)
    {
        $secret_key     = $this->CI->config->item('new_encryption_key');
        $secret_iv      = $user_id;
            
        $user_device_id = $this->CI->encryption->decrypt($user_device_id, $secret_key, $secret_iv);
        
        if($user_device_id == $posted_device_id)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    public function _check_user_security_code($user_security_code, $posted_security_code, $lang_id, $user_id)
    {
        if($user_security_code == '')
        {
            return 2; // security_code_needed
        }
        else
        {
            $secret_key         = $this->CI->config->item('new_encryption_key');
            $secret_iv          = $user_id;
            
            $decrypted_code     = $this->decrypt_posted_code($posted_security_code);
            $user_security_code = $this->CI->encryption->decrypt($user_security_code, $secret_key, $secret_iv);
            
            if($decrypted_code == $user_security_code)
            {
                return 1;  // valid security code 
            }
            else
            {
                return 3;  // not valid security code
            }
            
        }
    }
    
    public function decrypt_posted_code($encrypted_code)
    {
        
        if(base64_decode($encrypted_code) == false)
        {
            $decrypted_code = '';
        }
        else
        {
            $secret_key = $this->CI->config->item('new_encryption_key');
            $secret_iv  = "wholesaler_api";
            
            $encrypted_wholesaler_secret_key  = $this->settings->wholesaler_api_secret_key;
            $decrypted_wholesaler_secret_key  = $this->CI->encryption->decrypt($encrypted_wholesaler_secret_key, $secret_key, $secret_iv);
            
            $encrypted_wholesaler_secret_iv   = $this->settings->wholesaler_api_secret_iv;
            $decrypted_wholesaler_secret_iv   = $this->CI->encryption->decrypt($encrypted_wholesaler_secret_iv, $secret_key, $secret_iv);
            
            $decrypted_code                   = $this->decrypt_code($encrypted_code, $decrypted_wholesaler_secret_key, $decrypted_wholesaler_secret_iv);//$this->CI->encryption->decrypt($encrypted_code, $decrypted_wholesaler_secret_key, $decrypted_wholesaler_secret_iv);
            
            
        }
        
        return $decrypted_code;
        
    }
    
    public function encrypt_data($text_to_encrypt)
    {
        $secret_key = $this->CI->config->item('new_encryption_key');
        $secret_iv  = "wholesaler_api";
        
        $encrypted_wholesaler_secret_key  = $this->settings->wholesaler_api_secret_key;
        $decrypted_wholesaler_secret_key  = $this->CI->encryption->decrypt($encrypted_wholesaler_secret_key, $secret_key, $secret_iv);
         
        $encrypted_wholesaler_secret_iv   = $this->settings->wholesaler_api_secret_iv;
        $decrypted_wholesaler_secret_iv   = $this->CI->encryption->decrypt($encrypted_wholesaler_secret_iv, $secret_key, $secret_iv);
         
        $encrypted_text = $this->encrypt_code($text_to_encrypt, $decrypted_wholesaler_secret_key, $decrypted_wholesaler_secret_iv);//CI->encryption->encrypt($text_to_encrypt, $decrypted_wholesaler_secret_key, $decrypted_wholesaler_secret_iv);
        
        return $encrypted_text;
    }
    
    public function resend_user_sms_code($user_id, $lang_id)
    {
        $user_id = intval($user_id);
        $sms_code = rand(1000, 9999);
        
        $data = array(
            		     'sms_code' => $sms_code,
            		 );
                
        $this->CI->ion_auth->update($user_id, $data);
        
        $user = $this->CI->user_model->get_row_data($user_id);
        
        $this->CI->load->library('notifications');
        $sms_activation_code_lang = $this->CI->general_model->get_lang_var_translation('sms_activation_code', $lang_id);
        $msg = $sms_activation_code_lang.' : '.$sms_code;
        
        $this->CI->notifications->send_sms($msg, $user->phone);
        
    }
    
    /**********************END LOGIN FUNCTIONS***********************/
    
    /***********************Orders Functions*************************/
    public function get_payment_method_tax($total, $payment_option_id)
    {
        $option_data = $this->CI->payment_methods_model->get_option_data($payment_option_id);
        
        $tax_percent = round(($option_data->extra_fees_percent * $total), 2)/ 100;
        $tax         = $tax_percent + $option_data->extra_fees;
        
        return $tax;
    }
    
    public function insert_order($product_id, $country_id, $user_id, $payment_method_id, $lang_id, $device_id, $charging_price=0, $bank_id=0, $user_account_name='', $user_account_number='', $voucher_number='')
    {
        /**
            #return result status
               1 => success
               2 => no stock
               3 => not enough balance
               4 => error in inerting order
               5 => product not exist in country
               6 => product not exist
            
        **/
        
        $error_array     = array();
        $qty             = 1;
        $order_discount  = 0;
        $order_serial    = 0;
        
        $user_data       = $this->CI->user_model->get_row_data($user_id);
        $secret_key      = $this->CI->config->item('new_encryption_key');
        $secret_iv       = $user_data->id;
        
        if($product_id != 0)
        {
            $product_check  = $this->_check_product_exist($product_id);
            
            if(! $product_check)
            {
                $status        = 6;
                $result        = array($status, 0); // product not exist
                $error_array[] = 1;
            }
            else
            {
                $product_check  = $this->_check_product_exist_per_country($product_id, $country_id);
                
                if(!$product_check)
                {
                    $status        = 5;
                    $result        = array($status, 0); // product not exist in country
                    $error_array[] = 1;
                }
                else
                {
                    $check_product_stock = $this->_check_product_stock($product_id, $country_id);
                
                    if(!$check_product_stock)
                    {
                        $status        = 2;
                        $result        = array($status, 0); // no stock
                        $error_array[] = 1;
                    }
                    else
                    {
                        if($check_product_stock == 1)
                        {
                            $order_serial = 1;
                        }
                        else
                        {
                            $order_serial = 0;
                        }
                        
                        $product_details    = $this->CI->products_model->get_product_row_details($product_id, $lang_id, $country_id);
                        $product_price_data = $this->CI->products_lib->get_product_price_data($product_details, $country_id, $user_data->id, $device_id);
                       
                        $user_balance       = $this->CI->encryption->decrypt($user_data->user_balance, $secret_key, $secret_iv);
                        
                        if($user_balance >= $product_price_data[1])
                        {
                            $payment_method_id = 1;
                            $order_before_total  = $product_price_data[0];
                            $order_total         = $product_price_data[1];
                            $order_discount      = $product_price_data[5];
                        }
                        else
                        {
                            $status = 3;
                            $result = array($status, 0);
                            $error_array[] = 1;
                        }
                    }
                    
                }
            }
            
        }
        else
        {
            $order_before_total = $charging_price;
            $order_total        = $charging_price;
        }
        
        if(count($error_array) == 0)
        {
            $payment_tax       = $this->get_payment_method_tax($order_total, $payment_method_id);
            $order_status_id   = $this->CI->payment_methods_model->get_option_data($payment_method_id)->order_status_id;
            $order_total_price = $order_total + $payment_tax;
            $currency_symbol   = $this->CI->countries_model->get_country_symbol($country_id);
            
            if($payment_method_id == 1)
            {
                $template_payment_method = $this->CI->general_model->get_lang_var_translation('pocket', $lang_id);
            }
            elseif($payment_method_id == 3)
            {
                $template_payment_method = $this->CI->general_model->get_lang_var_translation('bank', $lang_id);
            }
            elseif($payment_method_id == 7)
            {
                $template_payment_method = $this->CI->general_model->get_lang_var_translation('voucher', $lang_id);
            }
            
            
            
            $order_data        = array(
                                    'user_id'             => $user_data->id         ,
                                    'agent'               => 'wholesaler_device'    ,
                                    'payment_method_id'   => $payment_method_id     ,
                                    'bank_id'             => $bank_id               ,
                                    'bank_account_name'   => $user_account_name     ,
                                    'bank_account_number' => $user_account_number   ,
                                    'voucher'             => $voucher_number        ,
                                    'order_status_id'     => $order_status_id       ,
                                    'currency_symbol'     => $currency_symbol       ,
                                    'country_id'          => $country_id            ,
                                    'items_count'         => $qty                   ,
                                    'total'               => $order_before_total    ,
                                    'discount'            => $order_discount        ,
                                    'coupon_discount'     => 0                      ,
                                    'tax'                 => $payment_tax           ,
                                    'final_total'         => $order_total_price     ,
                                    'auto_cancel'         => 1                      ,
                                    'notes'               => ' '                    ,
                                    'unix_time'           => time()                 ,
                                    'day'                 => date('d')              ,
                                    'month'               => date('m')              , 
                                    'year'                => date('Y') 
                               );
           
           $this->CI->orders_model->insert_order($order_data);
           $order_id = $this->CI->db->insert_id();
           
           if($payment_method_id == 1)
           {
               $update_balance = $this->_update_user_balance($user_data->user_balance, $order_total_price, $user_data->id, $order_id);
               if(!$update_balance)
               {
                   $status = 3;
                   $result = array($status, 0, date('Y/m/d H:i'));
                   $error_array[] = 1;
               }               
           }
           
           if(count($error_array) == 0)
           {
                if($product_id != 0)
                {
                    //insert order products
                    $order_products_inserted = $this->_insert_order_products($order_id, $country_id, $product_id, $product_price_data[0], $product_price_data[5], $product_price_data[1], $lang_id);
                    
                    if(!$order_products_inserted)
                    {
                        $status        = 4;
                        $result        = array($status, 0, date('Y/m/d H:i'));
                        $error_array[] = 1;
                    }
                    else
                    {
                        if($order_serial == 1)
                        {
                            //generate products serials
                            $generated_serials = $this->_generate_order_serials($order_id, $lang_id, $country_id, $product_id);
                            
                            if(!$generated_serials)
                            {
                                $status        = 4;
                                $result        = array($status, 0, date('Y/m/d H:i'));
                                $error_array[] = 1;
                            }
                        }
                    }
                }
                else
                {
                    // insert recharge cards
                    $recharge_result = $this->_insert_charge_cards($charging_price, $order_id, $payment_method_id, $secret_key, $secret_iv, $user_data->id, $user_data->user_balance);
                    
                    if(!$recharge_result)
                    {
                        $status        = 4;
                        $result        = array($status, 0, date('Y/m/d H:i'));
                        $error_array[] = 1;
                    }
                    
                }
                
                if(count($error_array) == 0)
                {
                    $this->_insert_order_log($lang_id, $order_id, $secret_key, $secret_iv, $payment_method_id, $template_payment_method, $user_id);
                            
                    //send notification
                    $products_names = '';
                    $status         = $this->CI->order_status_model->get_status_translation_name($order_status_id, $lang_id); 
                    $order_products = $this->CI->orders_model->get_order_products($order_id, $lang_id);
                    
                    foreach($order_products as $product)
                    {
                        $products_names .= $product->title." , ";
                    }
                    
                    
                    $username      = $user_data->first_name . ' ' . $user_data->last_name;
                    $emails[]      = $user_data->email;
                    
                    if($userdata->stop_wholesaler_sms == 0)
                    {
                        $mobile_number = $user_data->phone;
                    }
                    else
                    {
                        $mobile_number = '';
                    }
                    
                    
                    if($order_status_id != 1)
                    {
                        //send notification
                        $email_msg      = '';
                        $sms_msg        = '';
                        
                        foreach($order_products as $product)
                        {
                            
                            $email_msg = '<div style="width:100%; display:block; overflow:hidden; overflow:hidden;">
                                            <table cellpadding="0" border="0" width="100%" style="text-align:center; font-size:14px;">
                                        	<tr style="background:#e1f0f8; font-size:14px;">
                                                <td>'.lang('thumbnail').'</td>
                                                <td>'.lang('product').'</td>
                                                <td>'.lang('quantity').'</td>
                                                <td>'.lang('price').'</td>
                                                <td>'.lang('total_price').'</td>
                                            </tr>';
                            $sms_msg  = '';
                                        
                            foreach($order_products as $product)
                            {
                                if($product->product_id != 0)
                                {
                                    $product_data   = $this->CI->products_model->get_row_data($product->product_id, $lang_id);
                                    //$product_price  = $this->CI->orders_model->get_order_product_data($product->product_id, $order_id);
                                    $product_name   = $product_data->title;
                                    $img_path       = base_url().'assets/uploads/products/'.$product_data->image;
                                    
                                    $email_msg .= '<tr>
                                                    	<td><img src="'.base_url().'assets/uploads/products/'.$product_data->image.'" width="50" height="50" style=" display:block; margin:5px auto;" alt="'.$product_name.'"/></td>
                                                        <td>'.$product_name.'</td>
                                                        <td>'.$product->qty.'</td>
                                                        <td>'.$product->final_price.' '.$currency_symbol.'</td>
                                                        <td>'.$product->final_price * $product->qty.' '.$currency_symbol.'</td>
                                                   </tr>';
                                                   
                                    $sms_msg   .= lang('product').': '.$product_name.'--';
                                }
                                else
                                {
                                    $userdata         = $this->CI->user_model->get_row_data($user_id);
                                    $secret_key       = $this->CI->config->item('new_encryption_key');
                                    $secret_iv        = $userdata->id;
                                    $enc_user_balance = $userdata->user_balance;
                                    $user_balance     = $this->CI->encryption->decrypt($enc_user_balance, $secret_key, $secret_iv);
                                    
                                    $email_msg .= '<tr><td></td><td>'.lang('recharge_card').' </td><td> '.$product->final_price.'</td><td>'.lang('current_balance').' </td><td> '.$user_balance.' '.$currency_symbol.' </td></tr>';
                                    $sms_msg   .= lang('recharge_card').' : '.$product->final_price.' '.$currency_symbol.'  '.lang('current_balance').$user_balance.' '.$currency_symbol;
                                }
                            }
                            
                            $email_msg .= '<tr>
                                            <td colspan="3"></td>
                                            <td><span>'.lang('final_total').'</span></td>
                                            <td><span>'.$cart_data->final_total_price_with_tax.' '.$currency_symbol.'</span></td>
                                           </tr>';
                            
                            $email_msg .= '</table></div>';

                        }
                        
                        
                    
                        $template_data = array(
                                                'username'              => $username                ,
                                                'products'              => $products_names          ,
                                                'payment_method'        => $template_payment_method ,
                                                'order_details_email'   => $email_msg               ,
                                                'order_details_sms'     => $sms_msg                 ,
                                                'status'                => $status                  ,
                                                'order_time'            => date('Y/m/d H:i', time()),
                                                'order_id'              => $order_id                ,
                                                'user_email'            => $user_data->email        ,
                                                'user_phone'            => $user_data->phone        ,
                                                'logo_path'             => base_url().'assets/template/site/img/white_logo.png',
                                                'user_order_link'       => base_url()."orders/order/view_order_details/".$order_id
                                              );
                                              
                        
                        $this->CI->notifications->create_notification('new_order_added', $template_data, $emails, $mobile_number);
                    
                    }
                    
                    
                    $status = 1;
                    $result = array($status, $order_id, date('Y/m/d H:i'), $order_serial);
                }
           }
             
        }
        
        return $result;
        
    }
    
    private function _check_product_exist($product_id)
    {
        $product_exist = $this->CI->products_model->check_product_exist($product_id);
        
        if($product_exist)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    private function _check_product_exist_per_country($product_id, $country_id)
    {
        $product_per_country_count = $this->CI->products_model->check_product_in_country($product_id, $country_id);
        
        if($product_per_country_count != 0)
        {
            return true;
        }
        else
        {
            return false;
        }
        
        
    }
    
    private function _check_product_stock($product_id, $country_id)
    {
        if($product_id == 0)
        {
            return true;
        }
        else
        {
            $product_details = $this->CI->products_model->get_products_row($product_id);
            
            if($product_details->quantity_per_serial == 0)
            {
                return 2;
            }
            else
            {
            
                $stock_count = $this->CI->products_model->count_product_available_quantity($product_id, $country_id);
                
                $min_stock   = $this->CI->config->item('min_product_stock');
                
                $product_qty = $stock_count - $min_stock;
                $qty         = 1;
                
                if($product_qty >= $qty)
                {
                    return true;
                }
                else
                {
                    return false;// no stock
                }
            }
        }
    }
    
    private function _update_user_balance($user_balance, $order_total, $user_id, $order_id)
    {
        $payment_method_id    = 1;
        $secret_key           = $this->CI->config->item('new_encryption_key');
        $secret_iv            = $user_id;
         
        $user_enc_old_balance = $user_balance;
        $user_old_balance     = $this->CI->encryption->decrypt($user_enc_old_balance, $secret_key, $secret_iv);
        
        if($user_old_balance >= $order_total)
        {
            $user_new_balance                  = $user_old_balance - $order_total;
            $user_enc_new_balance              = $this->CI->encryption->encrypt($user_new_balance, $secret_key, $secret_iv);
            $user_balance_data['user_balance'] = $user_enc_new_balance;
            
            $this->CI->user_model->update_user_balance($user_id, $user_balance_data);
            
            $log_data = array(
                                'user_id'           => $user_id             ,
                                'order_id'          => $order_id            ,
                                'payment_method_id' => $payment_method_id   ,
                                'amount'            => $order_total         ,
                                'balance'           => $user_new_balance    ,
                                'balance_status_id' => 1,  //withdraw from balance
                                'ip_address'        => $this->CI->input->ip_address(),
                                'unix_time'         => time()
                             );
            
            $this->CI->user_balance_model->insert_balance_log($log_data);
            return true;
        }
        else
        {
            return false;
            
        } 
   }
   
   private function _insert_order_products($order_id, $country_id, $product_id, $product_price_before, $product_discount, $product_final_price, $lang_id)
   {
        $qty             = 1;
        $product_data    = $this->CI->products_model->get_product_country_data($product_id, $country_id);
        $product_details = $this->CI->products_model->get_products_row($product_id);
        
        $order_product_data = array(
                                       'order_id'           => $order_id                 ,
                                       'type'               => 'product'                 ,
                                       'product_id'         => $product_id               ,
                                       'cat_id'             => $product_details->cat_id     ,
                                       'qty'                => $qty                      ,
                                       'price'              => $product_price_before     ,
                                       'final_price'        => $product_final_price      ,
                                       'discount'           => $product_discount         ,
                                       'reward_points'      => $product_data->reward_points * $qty ,
                                       'purchased_cost'     => $product_data->average_cost         ,
                                       'unix_time'          => time()
                                   );
        
        if($this->CI->orders_model->insert_order_products($order_product_data))
        {
            $product_quantity     = $this->CI->products_model->count_product_available_quantity($product_id, $country_id);
            $new_product_quantity = $product_quantity - $qty;
            $updated_amount       = array('product_quantity' => $new_product_quantity);
            
            $this->CI->products_model->update_product_country_amount($updated_amount, $product_id, $country_id);
            
            $min_stock            = $this->CI->config->item('min_product_stock');
            $available_qty        = $product_quantity - $min_stock;
            $new_product_quantity = $available_qty - $qty;
                
                
            if($new_product_quantity == 0)
            {
                $product_name  = $this->CI->products_model->get_product_name($product_id, $lang_id);
                $emails[]      = $this->CI->config->item('email');
                $mobile_number = $this->CI->config->item('mobile');
                $template_data = array('product_name'=>$product_name);
                
                $this->CI->notifications->create_notification('product_quantity_less_than_threshold', $template_data, $emails, $mobile_number);
            }
            
            return true;
        }
        else
        {
            return false;
        }
    }
    
    /*******************************************************/
    /* Generate order serials and assign them to the order
    /*******************************************************/
    
    private function _generate_order_serials($order_id, $lang_id, $country_id, $product_id)
    {
        $order_data = $this->CI->orders_model->get_order($order_id);
        $qty        = 1;
        
        $serials    = $this->CI->orders_model->generate_product_serials($product_id, $qty, $country_id);

        foreach($serials as $serial)
        {
            $serials_data = array(
                                    'order_id'          => $order_id,
                                    'product_id'        => $product_id,
                                    'product_serial_id' => $serial->id,
                                    'unix_time'         => time()
                                 );
            
            ////////serial status///////
            ////0--->available
            ////1--->pending
            ////2--->sold
            ////3--->invalid
            
            if($order_data->order_status_id == 1)
            {
                $serial_status['serial_status'] = 2;
            }
            else
            {
                $serial_status['serial_status'] = 1;
            }
            
            //insert log data
            $log_data = array(
                                'user_agent'        => 'wholesaler device'              ,
                                'user_ip_address'   => $this->CI->input->ip_address()   ,
                                'serial_id'         => $serial->id                      ,
                                'product_id'        => $product_id                      ,
                                'order_id'          => $order_id                        ,
                                'status_id'         => $serial_status['serial_status']  ,
                                'store_country_id'  => $country_id                      ,
                                'unix_time'         => time()
                             );
            
            $this->CI->serials_lib->insert_log($log_data);
            
            $this->CI->orders_model->update_serial_status($serial_status, $serial->id);                                     
            if(!$this->CI->orders_model->insert_product_serials($serials_data))
            {
                return false;
            }
            else
            {
                return true;
            }
        }
    }
    
    private function _insert_charge_cards($charging_price, $order_id, $payment_method_id, $secret_key, $secret_iv, $user_id, $enc_user_balance)
    {
        $order_product_data = array(
                                       'order_id'    => $order_id,
                                       'type'        => 'recharge_card',
                                       'product_id'  => 0,
                                       'cat_id'      => 0,
                                       'qty'         => 1,
                                       'price'       => $charging_price,
                                       'final_price' => $charging_price,
                                       'unix_time'   => time()
                                   );
        
        $this->CI->orders_model->insert_order_products($order_product_data);
        
        $user_balance   = $this->CI->encryption->decrypt($enc_user_balance, $secret_key, $secret_iv);
        
        $log_data       = array(
                                'user_id'           => $user_id,
                                'order_id'          => $order_id,
                                'payment_method_id' => $payment_method_id,
                                'amount'            => $charging_price,
                                'balance'           => $user_balance,
                                'balance_status_id' => 3,  //request to add balace
                                'ip_address'        => $this->CI->input->ip_address(),
                                'unix_time'         => time()
                               );
        
        if($this->CI->user_balance_model->insert_balance_log($log_data))
        {
           return true;
        }
        else
        {
            return false;
        }
            
        
    }
    
    private function _insert_order_log($lang_id, $order_id, $secret_key, $secret_iv, $payment_method_id, $template_payment_method, $user_id)
    {
        
        $order_products = $this->CI->orders_model->get_order_all_products($order_id);
        $order_data     = $this->CI->orders_model->get_order_main_details($order_id, $lang_id);
        $user_data      = $this->CI->user_model->get_row_data($user_id);
        
        if($order_data->order_status_id == 1)
        {
            //reward points
            $enc_user_points = $user_data->user_points;
            $user_points     = $this->CI->encryption->decrypt($enc_user_points, $secret_key, $secret_iv);
            
            $total_reward_points = 0;
            
            foreach($order_products as $product)
            {
                if($product->reward_points_used == 0)
                {
                    $total_reward_points += $product->reward_points;
                    $order_product_data['reward_points_used'] = 1;
                    
                    $this->CI->orders_model->update_product_order_data($order_id, $product->product_id, $order_product_data);
                }
            }
            
            
            $user_total_reward_points = $total_reward_points + $user_points;
            
            $this->CI->user_bootstrap->encrypt_and_update_users_data($user_id, 'user_points', $user_total_reward_points);
            
            // Add Affiliate
            $this->add_affiliate($order_id);
            
            //Send Approve Notification
            $serials_data         = $this->CI->orders_model->get_order_serials($order_id);
            $non_serials_products = $this->CI->orders_model->get_order_non_serials_products($order_id, $lang_id);
            
            $this->send_approve_order_notification($user_data, $order_data->order_status_id, $lang_id, $serials_data, $non_serials_products, $order_data);
        
            
        }
        else 
        {
            $order_status_id = $this->CI->orders_model->get_other_payment_status_id($payment_method_id);
        }
        
        $log_data = array(
                            'order_id'  => $order_id, 
                            'status_id' => $order_data->order_status_id,
                            'unix_time' => time()
                         );
                         
        $this->CI->orders_model->insert_order_log($log_data);
    }
    
    
    /***********************************/
    /* Check if user has affiliate
    /***********************************/
    private function add_affiliate($order_id)
    {
        $order_data       = $this->CI->orders_model->get_order($order_id);
        $affliate_user_id = $this->CI->user_model->get_row_data($order_data->user_id)->affiliate_user_id;
        $affiliate_data   = $this->CI->admin_affiliate_model->get_afiliate_for_user($affliate_user_id);
        
        if($affiliate_data)
        {
            if($affiliate_data->num_uses !=0)
            {
                if($affiliate_data->num_uses_done < $affiliate_data->num_uses)
                {
                    $affiliate_amount = $order_data->final_total * ($affiliate_data->commission / 100);
                    
                    $aff_log_data = array(
                                            'user_id'      => $order_data->user_id,
                                            'buyer_id'     => $affliate_user_id,
                                            'affiliate_id' => $affiliate_data->id,
                                            'order_id'     => $order_id,
                                            'commission'   => $affiliate_data->commission,
                                            'amount'       => $affiliate_amount,
                                            'unix_time'    => time()
                                         );
                                         
                    $this->CI->affiliate_log_model->insert_affiliate_log_data($aff_log_data);
                    
                    $affiliate_updated_data['num_uses_done'] = $affiliate_data->num_uses_done + 1;
                    $this->CI->admin_affiliate_model->update_affiliate($affiliate_updated_data, $affiliate_data->id);
                }
            }
            else
            {
                $affiliate_amount = $order_data->final_total * ($affiliate_data->commission / 100);
                    
                $aff_log_data = array(
                                        'user_id'      => $order_data->user_id,
                                        'buyer_id'     => $affliate_user_id,
                                        'affiliate_id' => $affiliate_data->id,
                                        'order_id'     => $order_id,
                                        'commission'   => $affiliate_data->commission,
                                        'amount'       => $affiliate_amount,
                                        'unix_time'    => time()
                                     );
                                     
                $this->CI->affiliate_log_model->insert_affiliate_log_data($aff_log_data);
                
                $affiliate_updated_data['num_uses_done'] = $affiliate_data->num_uses_done + 1;
                $this->CI->admin_affiliate_model->update_affiliate($affiliate_updated_data, $affiliate_data->id);
            }
        }
    }
    
    public function decrypt_field($value, $posted_field, $lang_id, $user_id)
    {
        if($user_security_code == '')
        {
            return 2; // empty posted field
        }
        else
        {
            $secret_key         = $this->CI->config->item('new_encryption_key');
            $secret_iv          = $user_id;
            
            $decrypted_code     = $this->decrypt_posted_code($posted_field);
            $field_value        = $this->CI->encryption->decrypt($value, $secret_key, $secret_iv);
            
            if($value == $field_value)
            {
                return 1;  // valid field
            }
            else
            {
                return 3;  // not valid field
            }
            
        }
    }
    
    /*****************Encryption***********************************/
    
    public function encrypt_code($string, $key, $iv) 
    { 
      
      $str = $this->pkcs5_pad($string);   

      $td = mcrypt_module_open('rijndael-128', '', 'cbc', $iv); 
      
      mcrypt_generic_init($td, $key, $iv);
      $encrypted = mcrypt_generic($td, $str); 
      
      mcrypt_generic_deinit($td);
      mcrypt_module_close($td); 

      return bin2hex($encrypted);
      
    }
    
    public function decrypt_code($encrypted_code, $key, $iv) 
    { 
      $code = $this->hex2bin($encrypted_code);
      $td   = mcrypt_module_open('rijndael-128', '', 'cbc', $iv); 

      mcrypt_generic_init($td, $key, $iv);
      $decrypted = mdecrypt_generic($td, $code); 

      mcrypt_generic_deinit($td);
      mcrypt_module_close($td); 

      $ut =  utf8_encode(trim($decrypted));
      
      $unpadded = $this->pkcs5_unpad($ut);
        if($unpadded){
        return $unpadded;
        }else{
        return $ut;
        }

    }

    protected function hex2bin($hexdata) 
    {
      $bindata = ''; 
      
      for ($i = 0; $i < strlen($hexdata); $i += 2)
      {
          $bindata .= chr(hexdec(substr($hexdata, $i, 2)));
      } 
      
      return $bindata;
    }

    protected function pkcs5_pad ($text) 
    {
      $blocksize = 16;
      $pad       = $blocksize - (strlen($text) % $blocksize);
      
      return $text . str_repeat(chr($pad), $pad);
    }
 

    protected function pkcs5_unpad($text) 
    {
      $pad = ord($text{strlen($text)-1});
      
      if ($pad > strlen($text)) 
      {
          return false; 
      }
      
      if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) 
      {
          return false;
      }
       
      return substr($text, 0, -1 * $pad);
      
    }
    
    public function send_approve_order_notification($userdata, $status_id, $lang_id, $serials_data, $non_serials_products, $order_data)
    {
        // send notification
        $username = $userdata->first_name.' '. $userdata->last_name;
        $order_id = $order_data->id;
        
        
        $emails[]       = $userdata->email;
        $user_email     = $userdata->email;
        
        if($userdata->stop_wholesaler_sms == 0)
        {
            $phone = $userdata->phone;
        }
        else
        {
            $phone = '';
        }
        $status         = $this->CI->order_status_model->get_status_translation_name($status_id, $lang_id);
        $payment_method = $this->CI->payment_methods_model->get_row_data($order_data->payment_method_id, $lang_id);
        
        $new_serials_array  = array();
        $email_msg          = '';
        $sms_msg            = '';
        
        $final_total_lang   = $this->CI->general_model->get_lang_var_translation('final_total', $lang_id);
        
        if(count($serials_data) != 0 || count($non_serials_products) != 0)
        {
            $thumbnail_lang = $this->CI->general_model->get_lang_var_translation('thumbnail', $lang_id);
            $product_lang   = $this->CI->general_model->get_lang_var_translation('product', $lang_id);
            $serial_lang    = $this->CI->general_model->get_lang_var_translation('serial', $lang_id);
            $price_lang     = $this->CI->general_model->get_lang_var_translation('price', $lang_id);
            
            
            $email_msg = '<div style="width:100%; display:block; overflow:hidden; overflow:hidden;">
                            <table cellpadding="0" border="0" width="100%" style="text-align:center; font-size:14px;">
                            	<tr style="background:#e1f0f8; font-size:14px;">
                                    <td>'.$thumbnail_lang.'</td>
                                    <td>'.$product_lang.'</td>
                                    <td>'.$serial_lang.'</td>
                                    <td>'.$price_lang.'</td>
                                </tr>';
            $sms_msg  = '';
            
            if(count($serials_data) != 0)
            {
                foreach($serials_data as $serial)
                {
                    $product_serial = $this->CI->products_serials_model->get_products_serials_row($serial->product_serial_id);
                    $product_data   = $this->CI->products_model->get_row_data($serial->product_id, $lang_id);
                    $product_price  = $this->CI->orders_model->get_order_product_data($serial->product_id, $order_id);
                    $product_name   = $product_data->title;
                    $img_path       = base_url().'assets/uploads/products/'.$product_data->image;
                    
                    $secret_key  = $this->CI->config->item('new_encryption_key');
                    $secret_iv   = md5('serial_iv');
                    $dec_serials = $this->CI->encryption->decrypt($product_serial->serial, $secret_key, $secret_iv);
                    
                    $email_msg .= '<tr>
                                    	<td><img src="'.base_url().'assets/uploads/products/'.$product_data->image.'" width="50" height="50" style=" display:block; margin:5px auto;" alt="'.$product_name.'"/></td>
                                        <td>'.$product_name.'</td>
                                        <td>'.$dec_serials.'</td>
                                        <td>'.$product_price->final_price.' '.$order_data->currency_symbol.'</td>
                                   </tr>';
                                   
                    $sms_msg   .= $product_lang.': '.$product_name.'--'.$serial_lang.': '.$dec_serials.'***';
                }
            }
            
            if(count($non_serials_products) != 0)
            {
                foreach($non_serials_products as $product)
                {
                    $product_price  = $this->CI->orders_model->get_order_product_data($product->product_id, $order_id);
                    $email_msg .= '<tr>
                                    	<td><img src="'.base_url().'assets/uploads/products/'.$product->image.'" width="50" height="50" style=" display:block; margin:5px auto;" alt="'.$product->title.'"/></td>
                                        <td>'.$product->title.'</td>
                                        <td></td>
                                        <td>'.$product->final_price.' '.$order_data->currency_symbol.'</td>
                                   </tr>';
                               
                    $sms_msg   .= $product_lang.': '.$product->title.'***';
                }
            }
            
           
        }
        
        $cards_count = $this->CI->orders_model->get_recharge_cards_count($order_id);
        
        if($cards_count > 0)
        {
            $recharge_card_lang     = $this->CI->general_model->get_lang_var_translation('recharge_card', $lang_id);
            $current_balance_lang   = $this->CI->general_model->get_lang_var_translation('current_balance', $lang_id);
            
            $secret_key       = $this->CI->config->item('new_encryption_key');
            $secret_iv        = $userdata->id;
            $enc_user_balance = $userdata->user_balance;
            $user_balance     = $this->CI->encryption->decrypt($enc_user_balance, $secret_key, $secret_iv);
            
            $recharge_cards  = $this->CI->orders_model->get_recharge_card($order_id);
            
            foreach($recharge_cards as $card)
            {
                $email_msg .= '<tr><td>'.$recharge_card_lang.' : '.$card->price.' '.$order_data->currency_symbol.'</td><td>'.$current_balance_lang.' : '.$user_balance.' '.$order_data->currency_symbol.'</td></tr>';
                $sms_msg   .= $recharge_card_lang.' : '.$card->price.' '.$order_data->currency_symbol.'  '.$current_balance_lang.':'.$user_balance.' '.$order_data->currency_symbol;
            }
        }
        
         $email_msg .= '<tr>
                            <td colspan="2"></td>
                            <td><span>'.$final_total_lang.'</span></td>
                            <td><span>'.$order_data->final_total.' '.$order_data->currency_symbol.'</span></td>
                           </tr>';
            
         $email_msg .= '</table></div>';
        
         $template_data = array(
                                'unix_time'             => time()                                               ,
                                'username'              => $username                                            , 
                                'user_phone'            => $userdata->phone                                     ,
                                'user_email'            => $user_email                                          ,
                                'payment_method'        => $payment_method->name                                ,
                                'status'                => $status                                              ,
                                'order_id'              => $order_data->id                                      ,
                                'logo_path'             => base_url().'assets/template/site/img/white_logo.png' ,
                                'img_path'              => base_url().'assets/template/site/img/'               ,
                                'order_time'            => date('Y/m/d H:i', $order_data->unix_time)            ,
                                'order_details_email'   => $email_msg                                           ,
                                'order_details_sms'     => $sms_msg
                              );
                        
        $this->CI->notifications->create_notification('pending_order_completed', $template_data, $emails, $phone);
    }
    
}
