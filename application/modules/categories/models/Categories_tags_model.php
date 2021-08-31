<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Categories_tags_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
  
    public function get_suggestions($term)
    {
        $this->db->like('tag', $term, 'after');
        $query=$this->db->get('tags', 10);
        if($query)
        {
            return $query->result();
        }
        else
        {
            return false;
        }
        
    }
    
    public function get_tag_id($tag,$lang_id)
    {
        $this->db->where('tag',$tag);
        $query=$this->db->get('tags');
        
        if($row = $query->row())
        {
            return $row->id;
        }else{
           return $this->insert_tag($tag,$lang_id);
        }
    }
    
    public function insert_tag($tag,$lang_id)
    {
        $data = array('lang_id'=>$lang_id,'tag'=>$tag);
        $this->db->insert('tags',$data);
        return $this->db->insert_id();
    }
    
    public function insert_tags_categories($categories_tags_data)
    {
         $this->db->insert('tags_categories',$categories_tags_data);
    }
    
    public function delete_tags_categories($cat_id)
    {
        $this->db->where('category_id',$cat_id);
        $this->db->delete('tags_categories');
    }
    
    public function get_cat_tags($cat_id, $lang_id)
    {
        $this->db->select('tags_categories.*, tags.*');
        $this->db->join('tags', 'tags_categories.tag_id = tags.id');
        
        $this->db->where('tags_categories.category_id', $cat_id);
        $this->db->where('tags.lang_id', $lang_id);
        
        $result = $this->db->get('tags_categories');
        
        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
        
    }
}