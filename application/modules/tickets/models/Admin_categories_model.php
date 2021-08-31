<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Admin_categories_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    /****************Get****************/
  
    public function get_count_all_categories($lang_id,$search_word='')
    {
        $this->db->join('tickets_categories_translation' ,'tickets_categories.id = tickets_categories_translation.ticket_cat_id');
        
        if(trim($search_word) !='')
        {
            $this->db->like('tickets_categories_translation.title', $search_word, 'both'); 
        }
        
        $this->db->where('tickets_categories_translation.lang_id',$lang_id);
        
        return $this->db->count_all_results('tickets_categories');
    }
    
    public function get_cateories_data($lang_id,$limit,$offset,$search_word='',$order_by='',$order_state)
    {
        $this->db->select('tickets_categories_translation.*, tickets_categories.*, tickets_categories.id as id');
        
        $this->db->join('tickets_categories_translation', 'tickets_categories.id = tickets_categories_translation.ticket_cat_id');
        
        $this->db->where('tickets_categories_translation.lang_id',$lang_id);
        
        if(trim($search_word) !='')
        {
            $this->db->like('tickets_categories_translation.title', $search_word, 'both');
            
        }
        
        if($order_by != '')
        {
            if($order_by == lang('title'))
            { 
                $this->db->order_by('tickets_categories_translation.title',$order_state);
            }
            
            elseif($order_by == lang('active'))
            {
                $this->db->order_by('tickets_categories.active',$order_state);
            }
            
            else
            {
                $this->db->order_by('tickets_categories.id',$order_state);
            }
        }
        else
        {
            $this->db->order_by('tickets_categories.id',$order_state);
        }
        
        $result = $this->db->get('tickets_categories',$limit,$offset);
 
        if($result)
        {
            return $result->result();    
        }
    }
    
    public function get_row_data($id,$display_lang_id)
    {
        $this->db->select('tickets_categories_translation.*, tickets_categories.*, tickets_categories.id as id');
        
        $this->db->join('tickets_categories_translation', 'tickets_categories.id = tickets_categories_translation.ticket_cat_id');
        
        $this->db->where('tickets_categories.id',$id);
        $this->db->where('tickets_categories_translation.lang_id',$display_lang_id);
        
        $result = $this->db->get('tickets_categories');

        if($result)
        {
            return $result->row();    
        }
    }
    
    public function get_category_row($id)
    {
         $row = $this->db->where('id',$id)->get('tickets_categories');
         if($row){
            return $row->row();
         }else{
            return false;
        }
    }
    
   public function get_category_translation_result($id)
    {
        $this->db->where('ticket_cat_id',$id);
        $query = $this->db->get('tickets_categories_translation');
        
        if($query)
        {
            return $query->result();
        }
    }
    
    public function  get_tickets_cat($lang_id)
    {
        $this->db->select('tickets_categories_translation.*, tickets_categories.*, tickets_categories.id as id');
        
        $this->db->join('tickets_categories_translation', 'tickets_categories.id = tickets_categories_translation.ticket_cat_id');
        
        $this->db->where_in('tickets_categories_translation.lang_id',$lang_id);
        $this->db->where('tickets_categories.active', 1);
        $result = $this->db->get('tickets_categories');
 
        if($result)
        {
            return $result->result();    
        }
    }
    
    public function count_tickets($cats_ids)
    {
        $this->db->where_in('cat_id', $cats_ids);
        
        return $this->db->count_all_results('tickets');
    }
   /*************************DELETE*******************************/
   
    public function delete_category_data($categories_id_array)
    {
        $this->db->where_in('id',$categories_id_array);
        $this->db->delete('tickets_categories');
        
        $this->db->where_in('ticket_cat_id',$categories_id_array);
        $this->db->delete('tickets_categories_translation');
        echo '1';  
    }
    
  /*****************INSERT***************************************/
  
   public function insert_tickets_categories($data)
    {
                
        return $this->db->insert('tickets_categories',$data);
    } 
    
    public function insert_tickets_categories_translation($cat_translation_data)
    {
        return $this->db->insert('tickets_categories_translation',$cat_translation_data);
    }
    
    /***********************UPDATE*************************/
    
    public function update_category($cat_id,$category_data)
    {
        $this->db->where('id',$cat_id);
        $this->db->update('tickets_categories',$category_data);
    }  
    
   public function update_cat_translation($cat_id,$lang_id,$cat_translation_data)
    {
        $this->db->where('ticket_cat_id',$cat_id);
        $this->db->where('lang_id',$lang_id);
        $this->db->update('tickets_categories_translation',$cat_translation_data);
    }   
/////////////////////////////////////////////////   
}