<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
/**
 * 
 */
class Admin_products_lib
{
    public $CI ;
    
    public function __construct()
    {
        $this->CI = &get_instance();
        
        //$this->CI->load->model('products/products_model');
    }
    
    public function get_product_price_data($product_details, $country_id, $user_id)
    {
        $max_per_user = 0;
        $strike       = false;
        
        $product_discount_data = $this->get_discount($product_details, $country_id);
        $product_discount      = $product_discount_data[0];
        $product_price         = $product_details->price;
        $product_price_before  = $product_details->price;    // product price before discount
        
        if($product_discount > 0)                    /// if not in group && there is discount on this product
        {
            $product_price = $product_discount;
            $max_per_user  = $product_discount_data[1];
            $strike        = true;
        }
        
        $user_data = $this->CI->admin_bootstrap->get_user_by_id($user_id);
        
        $new_user_customer_group_id  = $this->CI->config->item('new_user_customer_group_id');
        
        if($new_user_customer_group_id != $user_data->customer_group_id)
        {
            $customer_group_price_data = $this->CI->products_model->get_customer_group_price_data($product_details->id, $country_id, $user_data->customer_group_id);
            
            if(isset($customer_group_price_data->group_price) && $customer_group_price_data->group_price != 0)
            {
                $product_price = $customer_group_price_data->group_price; 
                $strike        = false;
            }
            else
            {
                $product_price = $product_details->price; 
                $strike        = false;
            }
        }
        
        
        $discount = abs($product_price_before - $product_price);
        
        return array($product_price_before, $product_price, $max_per_user, $strike, $discount);
    }
    
    public function get_discount($product_details, $country_id)
    {
        $discount_price  = 0;
        $max_per_user    = 0;
        $count_discounts = $this->CI->products_model->count_available_discounts_on_product($product_details->product_id, $country_id);
        
        if($count_discounts > 0)
        {
            $product_discount_data = $this->CI->products_model->get_product_active_discounts($product_details->product_id, $country_id);
            
            // check discount data
            $current_time = time();
            
            if($product_discount_data->discount_start_unix_time <= $current_time && $product_discount_data->discount_end_unix_time > $current_time)
            {
                $discount_price = $product_discount_data->price;
                $max_per_user   = $product_discount_data->max_units_customers;
                
            }
        }
        
        return array($discount_price, $max_per_user);
    }
}