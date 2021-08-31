<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
/**
 * 
 */
class Currency
{
    public $CI ;
    public $default_currency_data;
    
    
    public function __construct($params = array())
    {
        $this->CI = &get_instance();
        
        $this->CI->load->library('encryption');
        
        $this->CI->config->load('encryption_keys');
        
        $this->CI->load->model('users/user_model');
        $this->CI->load->model('users/countries_model');
        $this->CI->load->model('currencies/currency_model');
        
        $this->default_currency_data = $this->CI->currency_model->get_default_currency_data();
        
    } /****************************End Constracut***************************************/
    
    public function convert_balance($current_currency_val, $new_currency_val, $amount)
    {
        if($amount == 0) return 0;
        
        if($current_currency_val != 0)
        {
            $factor      = $new_currency_val / $current_currency_val;
            $new_amount  = $amount * $factor;
            
            return $new_amount;
        }
        else
        {
            return 0;
        }
    }
    
    public function update_user_credit($user_current_balanace, $user_id, $user_store_country_id, $new_store_country_id)
    {
       $current_currency_data = $this->CI->currency_model->get_country_currency_result($user_store_country_id);
       $default_currency_data = $this->CI->currency_model->get_default_currency_data();
       $new_currency_data     = $this->CI->currency_model->get_country_currency_result($new_store_country_id);
       
       //user currency value to default currency value
       $new_balance_to_default_currency = $this->convert_balance($current_currency_data->currency_value, $default_currency_data->currency_value, $user_current_balanace);
       
       // convert to new store 
       $new_balance_to_current_currency = $this->convert_balance($default_currency_data->currency_value, $new_currency_data->currency_value, $new_balance_to_default_currency);
       
       // encrypted balance
       $encrypted_balannce = $this->encrypt_and_update_users_data($user_id, 'user_balance', $new_balance_to_current_currency); 
       
       $user_updated_data['store_country_id']  = $new_store_country_id;
       
       $this->CI->user_model->update_user($user_id, $user_updated_data);
       
       return $new_balance_to_current_currency; 
    }
    
    public function convert_to_default_currency($amount, $currency_id)
    {
        $current_currency_data      = $this->CI->currency_model->get_currecies_result($currency_id);
        $amount_to_default_currency = $this->convert_balance($current_currency_data->currency_value, $this->default_currency_data->currency_value, $amount);
        
        return round($amount_to_default_currency, 2);
    }
    
    public function convert_from_default_currency($amount, $country_id)
    {
        
        $currency_data = $this->get_country_currency_data($country_id);
        
        if($this->default_currency_data->id != $currency_data->id)
        {
            $amount_from_default_currency = $this->convert_balance($this->default_currency_data->currency_value, $currency_data->currency_value, $amount);
        }
        else
        {
            $amount_from_default_currency = $amount;
        }
        
        return round($amount_from_default_currency, 2);
    }
    
    public function convert_to_currency($current_symbol, $new_symbol, $amount)
    {
        $currency_data      = $this->get_symbol_data($current_symbol);
        $new_currency_data  = $this->get_symbol_data($new_symbol);
        
        $amount_to_new_currency = $this->convert_balance($currency_data->currency_value, $new_currency_data->currency_value, $amount);
        
        return round($amount_to_new_currency, 2);
    }
    
    public function convert_amount_from_country_to_country($current_country_id, $new_country_id, $amount)
    {
        $current_currency_data  = $this->get_country_currency_data($current_country_id);
        $new_currency_data      = $this->get_country_currency_data($new_country_id);
        
        if($current_currency_data->id != $new_currency_data->id)
        {
            $amount_to_new_currency = $this->convert_balance($current_currency_data->currency_value, $new_currency_data->currency_value, $amount);
        }
        else
        {
            $amount_to_new_currency = $amount;
        }
        
        return round($amount_to_new_currency, 2);
    }
    
    public function encrypt_and_update_users_data($user_id, $field, $data)
    {
        $secret_key    = $this->CI->config->item('new_encryption_key');
        $secret_iv     = $user_id;
        
        $user_enc_data = $this->CI->encryption->encrypt($data, $secret_key, $secret_iv);
        $user_points_data[$field]  = $user_enc_data;
        
        return $this->CI->user_model->update_user_balance($user_id, $user_points_data);  
    }
    
    public function get_amount_with_default_currency($amount, $user_store_country_id)
    {
       $current_currency_data = $this->CI->currency_model->get_country_currency_result($user_store_country_id);
       
       if($current_currency_data->id != $this->default_currency_data->id)
       {
           //user currency value to default currency value
           $new_balance_to_default_currency = $this->convert_balance($current_currency_data->currency_value, $this->default_currency_data->currency_value, $amount);
       }
       else
       {
            $new_balance_to_default_currency = $amount;
       }
       
       return round($new_balance_to_default_currency, 2); 
    }
    
    public function get_default_currency_data()
    {
        return $this->default_currency_data;
    }
    
    public function get_default_country_symbol()
    {
        return $this->default_currency_data->currency_symbol;
    }
    
    public function get_country_currency_data($country_id)
    {
        $currency_data = $this->CI->currency_model->get_country_currency_result($country_id);
        
        return $currency_data;
    }
    
    public function get_country_symbol($country_id)
    {
        $currency_data = $this->CI->currency_model->get_country_currency_result($country_id);
        
        return $currency_data->currency_symbol;
    }
    
    public function get_country_currency_name($country_id, $lang_id)
    {
        $currency = $this->CI->currency_model->get_country_currency_name($country_id, $lang_id);
        
        return $currency;
    }
    
    public function get_symbol_data($currency_symbol)
    {
        $currency_data = $this->CI->currency_model->get_symbol_data($currency_symbol);
        
        return $currency_data;
    }
    
    public function get_currency_data_by_id($currency_id)
    {
        $currency_data = $this->CI->currency_model->get_currency_result($currency_id);
        
        return $currency_data;
    }
    
    public function get_amount_from_currency_to_currency($amount, $new_currency_id, $current_currency_id)
    {
        if($new_currency_id != $current_currency_id)
        {
            $new_currency_data      = $this->get_currency_data_by_id($new_currency_id);
            $current_currency_data  = $this->get_currency_data_by_id($current_currency_id);
            
            $amount_to_new_currency = $this->convert_balance($current_currency_data->currency_value, $new_currency_data->currency_value, $amount);
            
            return $amount_to_new_currency;
        }
        else
        {
            return $amount;
        }
    }
    
}