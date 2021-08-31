<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Categories_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    
   /**********************GET*******************************/
    
    public function get_categories($lang_id, $parent_id, $store_id)
    {
        $this->db->select('categories_translation.* , categories.*');
        $this->db->join('categories_translation','categories.id = categories_translation.category_id');
        
        if($store_id != 0)
        {
            $this->db->join('store_categories', 'categories.id = store_categories.category_id');
            $this->db->where('store_id', $store_id);    
        }
        
        $this->db->where('categories_translation.lang_id',$lang_id);
        $this->db->where('categories.parent_id', $parent_id);
        $this->db->where('categories.active', 1);
        
        $query = $this->db->get('categories');
        
        if($query)
        {
            return $query->result();
        } 
    }
    
    public function get_sub_categories($lang_id, $store_id)
    {
        $this->db->select('categories_translation.* , categories.*');
        $this->db->join('categories_translation','categories.id = categories_translation.category_id');
        
        if($store_id != 0)
        {
            $this->db->join('store_categories', 'categories.id = store_categories.category_id');
            $this->db->where('store_id', $store_id);    
        }
        
        $this->db->where('categories_translation.lang_id',$lang_id);
        $this->db->where('categories.parent_id != 0');
        $this->db->where('categories.active', 1);
        
        
        $query = $this->db->get('categories');
        
        if($query)
        {
            return $query->result();
        }
        else
        {
            return false;
        }
    }
    
    public function get_wholesaler_categories($lang_id)
    {
        $this->db->select('categories_translation.* , categories.*');
        $this->db->join('categories_translation','categories.id = categories_translation.category_id');
        
        $this->db->where('categories_translation.lang_id',$lang_id);
        $this->db->where('categories.parent_id', 0);
        $this->db->where('categories.active', 1);
        $this->db->where('categories.wholesaler_device', 1);
        
        $result = $this->db->get('categories');
        
        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }
   
  
    
    
/****************************************************************/
}