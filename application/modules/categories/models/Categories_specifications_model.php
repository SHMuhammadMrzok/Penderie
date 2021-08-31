<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Categories_specifications_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    public function insert_cat_spec_data($cat_spec_data)
    {
        return $this->db->insert('categories_specifications', $cat_spec_data);
    }
    
    public function insert_cat_spec_translation($cat_spec_translation_data)
    {
        return $this->db->insert('categories_specifications_translation', $cat_spec_translation_data);
    }
    
    public function get_count_all_categories_specification($lang_id, $search_word='')
    {
        $this->db->join('categories_specifications_translation' ,'categories_specifications.id = categories_specifications_translation.category_specification_id');
        
        if(trim($search_word) !='')
        {
            $this->db->like('categories_specifications_translation.spec_label', $search_word, 'both');
        }
        
        $this->db->where('categories_specifications_translation.lang_id', $lang_id);
        
        return $this->db->count_all_results('categories_specifications');
    }
    
    public function get_cateories_specifications_data($lang_id, $limit, $offset, $search_word)
    {
        $this->db->select('categories_specifications.*, categories_specifications_translation.*, categories_translation.name as cat_name, categories_specifications.id as id');
        
        $this->db->join('categories_specifications_translation' ,'categories_specifications.id = categories_specifications_translation.category_specification_id');
        $this->db->join('categories_translation' ,'categories_specifications.cat_id = categories_translation.category_id AND categories_translation.lang_id = '.$lang_id);
        
        if(trim($search_word) !='')
        {
            $this->db->like('categories_specifications_translation.spec_label', $search_word, 'both');
        }
        
        $this->db->where('categories_specifications_translation.lang_id', $lang_id);
        
        $result = $this->db->get('categories_specifications');
        
        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }
    
    public function get_cats($lang_id)
    {
        $this->db->select('categories_translation.*, categories_translation.category_id as id');
        $this->db->where('categories_translation.lang_id', $lang_id);
        
        $result = $this->db->get('categories_translation');
        
        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }
    
    public function get_category_spec_row($id)
    {
         $this->db->where('id', $id);
         
         $row = $this->db->get('categories_specifications');
         if($row)
         {
            return $row->row();
         }
         else
         {
            return false;
        }
    }
    
    public function get_cat_spec_result($id)
    {
        
         $this->db->select('categories_specifications.*, categories_specifications_translation.*, categories_specifications.id as id');
         
         $this->db->join('categories_specifications_translation' ,'categories_specifications.id = categories_specifications_translation.category_specification_id');
         
         $this->db->where('categories_specifications.id', $id);
         
         $result = $this->db->get('categories_specifications');
         
         if($result)
         {
            return $result->result();
         }
         else
         {
            return false;
        }
    }
    
    public function get_row_data($id, $lang_id)
    {
        $this->db->select('categories_specifications.*, categories_specifications_translation.*, categories_translation.name as cat_name, categories_specifications.id as id');
        
        $this->db->join('categories_specifications_translation' ,'categories_specifications.id = categories_specifications_translation.category_specification_id AND categories_specifications_translation.lang_id = '.$lang_id);
        $this->db->join('categories_translation' ,'categories_specifications.cat_id = categories_translation.category_id AND categories_translation.lang_id = '.$lang_id);
        
        $this->db->where('categories_specifications.id', $id);
        
        $query = $this->db->get('categories_specifications');
        
        if($query)
        {
            return $query->row();
        }
    }
    
    public function check_cat_spec_used_label($cat_spec_id)
    {
        $this->db->where('cat_spec_id', $cat_spec_id);
        
        return $this->db->count_all_results('products_specifications');
    }
    
    public function get_cat_all_specifications($cat_id, $lang_id)
    {
        
        $this->db->select('categories_specifications.*, categories_specifications_translation.*, categories_specifications.id as id');
        
        $this->db->join('categories_specifications_translation' ,'categories_specifications.id = categories_specifications_translation.category_specification_id AND categories_specifications_translation.lang_id ='.$lang_id);
        
        $this->db->where('categories_specifications.cat_id', $cat_id);
        
        $result = $this->db->get('categories_specifications');
        
        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
        
    }
    
    public function delete_category_spec_data($ids_array)
    {
        $this->db->where_in('id', $ids_array);
        $this->db->delete('categories_specifications');
        
        $this->db->where_in('category_specification_id', $ids_array);
        $this->db->delete('categories_specifications_translation');
        
        echo 1;
    }
    
    public function update_category_spec($id, $cat_spec_general_data)
    {
        $this->db->where('id', $id);
        return $this->db->update('categories_specifications', $cat_spec_general_data);
    }
    
    public function update_cat_spec_translation($id, $lang_id, $cat_spec_translation_data)
    {
        $this->db->where('category_specification_id', $id);
        $this->db->where('lang_id', $lang_id);
        
        return $this->db->update('categories_specifications_translation', $cat_spec_translation_data);
    }
   
   
    /****************************************************/
}