<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
/**
 * 
 */
class Products
{
    public $CI ;
    public $settings;
    public $lang_id;
    
    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->library('encryption');
        
        $this->CI->load->model('global_model');
        $this->CI->load->model('products/products_model');
        $this->CI->load->model('products/products_serials_model');
        
        // Get settings table data
        $this->settings = $this->CI->global_model->get_config();
        $this->lang_id = $this->settings->default_lang;
    }
    
    public function get_product_quantity($product_id)
    {
        $product_row = $this->products_model->get_products_row($product_id);
        
        if($product_row)
        {
            return $product_row->row()->quantity;
        }
        else
        {
            return false;
        }
    }
    
    public function get_product_available_quantity_per_country()
    {
        $product_quantity = $this->products_model->get_product_quantity($product_id, $country_id);
        
        if($product_quantity)
        {
            return $product_quantity;
        }
        else
        {
            return false;
        }
    }
    
    public function update_product_quantity_data($product_id, $country_id)
    {
        //update product quantity in all countries
        $product_quantity_in_system = $this->products_model->count_product_all_quantity($product_id);
        $product_data['quantity']   = $product_quantity_in_system;
        $this->products_model->update_products($product_id, $product_data);
        
        //update product quantity in each country
        $product_per_country_count = $this->products_model->get_product_available_serial_count($product_id, $country_id);
        $product_country_data['product_quantity'] = $product_per_country_count;
        $this->products_model->update_product_countries($product_id, $country_id, $product_country_data);
        
        return true;
    }
    
}