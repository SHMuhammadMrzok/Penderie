<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Products_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    
   /**********************GET*******************************/
    
    public function get_count_all_products( $country_id , $category_id = 0)
    {
        $this->db->select('products_countries.* , products.*');
        
        $this->db->join('products_countries', 'products.id = products_countries.product_id');
        
        $this->db->where('products_countries.active', 1);
        $this->db->where('products_countries.country_id',$country_id);
        
        if($category_id)
        {
            $this->db->where('products.cat_id', $category_id);
        }else{
             $this->db->where('products_countries.display_home',1);
        }
        
        return $this->db->count_all_results('products');
    }
    
    public function get_products($lang_id  , $country_id , $category_id = 0 ,$limit ,$offset)
    {
      
        $this->db->select('products_countries.* , products.*, products_translation.* , countries_translation.currency','categories_translation.name');
        
        $this->db->join('products_translation', 'products.id = products_translation.product_id');
        $this->db->join('products_countries', 'products.id = products_countries.product_id');
        $this->db->join('countries_translation', 'products_countries.country_id = countries_translation.country_id');
        $this->db->join('categories_translation', 'products.cat_id = categories_translation.category_id');
        
        $this->db->where('products_countries.active', 1);
        $this->db->where('products_countries.country_id',$country_id);
        $this->db->where('products_translation.lang_id', $lang_id);
        $this->db->where('countries_translation.lang_id', $lang_id);
        $this->db->where('categories_translation.lang_id', $lang_id);
        
        if($category_id)
        {
            $this->db->where('products.cat_id', $category_id);
            $this->db->where('categories_translation.category_id', $category_id);
        }else{
             $this->db->where('products_countries.display_home',1);
        }
        
        $query = $this->db->get('products',$limit ,$offset);
        
        if($query)
        {
            return $query->result();
        }else{
            
            return false ;
        }
    }
   
    public function get_product_group_price($product_id ,$country_id , $customer_group_id)
    {
        $this->db->where('product_id', $product_id);
        $this->db->where('country_id', $country_id);
        $this->db->where('customer_group_id', $customer_group_id);
        
        $row = $this->db->get('products_customer_groups_prices');
        
        if($row)
        {
            return $row->row();
        }
        else
        {
            return false;
        }   
    }
   
    public function get_product_discount($product_id ,$country_id)
    {
        $this->db->where('product_id', $product_id);
        $this->db->where('country_id', $country_id);
        
        $row = $this->db->get('products_discounts');
        
        if($row)
        {
            return $row->row();
        }
        else
        {
            return false;
        }   
    }
    
    public function get_product_with_translation_data($product_id, $display_lang_id)
    {
        $this->db->select('products.*, products_translation.*');
        $this->db->join('products_translation', 'products.id = products_translation.product_id');
        
        $this->db->where('products.id', $product_id);
        $this->db->where('products_translation.lang_id', $display_lang_id);
        
        $row = $this->db->get('products');
        
        if($row)
        {
            return $row->row();
        }
        else
        {
            return false;
        }   
    }
    
    
/****************************************************************/
}