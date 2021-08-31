<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home_model extends CI_Model
{
      
    //**********************Home Products**********************************//
    
  public function get_home_products()
    {
        $lang_id    = $this->data['lang_id'];
        $country_id = $this->data['country_id'];
        
        $this->db->select('products_countries.price ,products_countries.display_home , products.*, products_translation.* , currencies_translation.name as currency');
        
        $this->db->join('products_translation', 'products.id = products_translation.product_id');
        $this->db->join('products_countries', 'products.id = products_countries.product_id');
        //$this->db->join('countries_translation', 'products_countries.country_id = countries_translation.country_id');
        $this->db->join('countries', 'products_countries.country_id = countries.id');
        $this->db->join('currencies_translation', 'countries.currency_id = currencies_translation.currency_id AND currencies_translation.lang_id ='. $lang_id);
        $this->db->join('categories', 'products.cat_id = categories.id AND categories.active=1');
        
        $this->db->where('products_countries.active',1);
        $this->db->where('products_countries.display_home',1);
        $this->db->where('products_countries.country_id',$country_id);
        $this->db->where('products_translation.lang_id', $lang_id);
        //$this->db->where('countries_translation.lang_id', $lang_id);
        
        $this->db->order_by('products.sort', 'desc');
        
        $query = $this->db->get('products');
        
        if($query)
        {
            return $query->result();
        }else{
            
            return false ;
        }
    }
 
  public function get_last_offers($lang_id, $country_id, $current_time)
  {
     $this->db->select('products.*, products_discounts.*, products_discounts_countries.*, products_translation.title, currencies_translation.name as currency');
     
     $this->db->join('products', 'products.id = products_discounts.product_id');
     $this->db->join('products_discounts_countries', 'products_discounts.id = products_discounts_countries.product_discount_id');
     $this->db->join('products_translation', 'products_translation.product_id = products_discounts.product_id');
     $this->db->join('countries', 'products_discounts_countries.country_id = countries.id');
     $this->db->join('currencies_translation', 'countries.currency_id = currencies_translation.currency_id AND currencies_translation.lang_id ='. $lang_id);
     $this->db->join('products_countries', 'products.id = products_countries.product_id');
     $this->db->join('categories', 'products.cat_id = categories.id AND categories.active=1');
     
     $this->db->where('products_translation.lang_id', $lang_id);
     $this->db->where('products_discounts_countries.active', 1);
     $this->db->where('products_discounts_countries.country_id', $country_id);
     $this->db->where('products_countries.country_id', $country_id);
    
     $this->db->where('products_discounts_countries.discount_start_unix_time <=', $current_time);
     $this->db->where('products_discounts_countries.discount_end_unix_time >= ', $current_time);
     
     $this->db->order_by('products_discounts.sort', 'desc');
    
     $query = $this->db->get('products_discounts');
        
     if($query)
     {
         return $query->result();
     }
     else
     {
         return false;
     }
        
  }
  
  public function get_today_offers()
  {
        $lang_id     = $this->session->userdata('lang_id');
        $country_id  = $this->session->userdata('country_id');
        $today       = strtotime(date("Y-m-d"));
        $end         = $today + (60*60*24);
        
        $this->db->select('products_discounts.*, products_discounts_countries.*, products.*');
        
        $this->db->join('products', 'products.id = products_discounts.product_id');
        $this->db->join('products_discounts_countries', 'products_discounts.id = products_discounts_countries.product_discount_id');
        $this->db->join('products_countries', 'products.id = products_countries.product_id AND products_countries.country_id = '.$country_id.' AND products_countries.active = 1');
        $this->db->join('categories', 'products.cat_id = categories.id AND categories.active=1');                
        
        $this->db->where('products_discounts_countries.active', 1);
        $this->db->where('products_discounts_countries.country_id', $country_id);
        $this->db->where('products_discounts_countries.discount_start_unix_time >=', $today);
        $this->db->where('products_discounts_countries.discount_start_unix_time <=', $end);
        
        $this->db->order_by('products_discounts.sort', 'desc');
        
        $query = $this->db->get('products_discounts');
        
        if($query)
        {
            return $query->result();
        }
        else
        {
            return false;
        }
        
 }
////////////////////////////
}
