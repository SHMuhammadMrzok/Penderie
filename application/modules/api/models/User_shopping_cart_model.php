<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class User_shopping_cart_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    
   /**********************GET*******************************/
    
    
  public function get_product_details($lang_id ,$country_id , $productId)
  {
        $this->db->select('products_countries.* , products.*, products_translation.* ');
        
        $this->db->join('products_translation', 'products.id = products_translation.product_id');
        $this->db->join('products_countries', 'products.id = products_countries.product_id');
        
       
        $this->db->where('products_countries.country_id',$country_id);
        $this->db->where('products_translation.lang_id', $lang_id);
        $this->db->where('products.id', $productId);
       
        $query = $this->db->get('products');
        
        if($query)
        {
            return $query->row();
        }else{
            
            return false ;
        }
  }
    
  public function get_products_amount($product_id, $country_id)
  {
    $this->db->where('product_id', $product_id);
    $this->db->where('country_id', $country_id);
    
    $query = $this->db->get('products_countries');
    
    if($query)
    {
        return $query->row()->product_quantity;
    }
    else
    {
        return false;
    }
  }
     
/****************************************************************/
}