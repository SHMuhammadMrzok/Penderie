<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Admin_status_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    /****************Get****************/
  
    public function get_count_all_categories($lang_id,$search_word='')
    {
        $this->db->join('tickets_status_translation' ,'tickets_status.id = tickets_status_translation.ticket_status_id');
        
        if(trim($search_word) !='')
        {
            $this->db->like('tickets_status_translation.title', $search_word, 'both'); 
        }
        
        $this->db->where('tickets_status_translation.lang_id',$lang_id);
        
        return $this->db->count_all_results('tickets_status');
    }
    
    public function get_cateories_data($lang_id,$limit,$offset,$search_word='',$order_by='',$order_state)
    {
        $this->db->select('tickets_status_translation.*, tickets_status.*, tickets_status.id as id');
        
        $this->db->join('tickets_status_translation', 'tickets_status.id = tickets_status_translation.ticket_status_id');
        
        $this->db->where('tickets_status_translation.lang_id',$lang_id);
        
        if(trim($search_word) !='')
        {
            $this->db->like('tickets_status_translation.title', $search_word, 'both');
            
        }
        
        if($order_by != '')
        {
            if($order_by == lang('title'))
            { 
                $this->db->order_by('tickets_status_translation.title',$order_state);
            }
            
            elseif($order_by == lang('active'))
            {
                $this->db->order_by('tickets_status.active',$order_state);
            }
            
            else
            {
                $this->db->order_by('tickets_status.id',$order_state);
            }
        }
        else
        {
            $this->db->order_by('tickets_status.id',$order_state);
        }
        
        $result = $this->db->get('tickets_status',$limit,$offset);
 
        if($result)
        {
            return $result->result();    
        }
    }
    
    public function get_row_data($id,$display_lang_id)
    {
        $this->db->select('tickets_status_translation.*, tickets_status.*, tickets_status.id as id');
        
        $this->db->join('tickets_status_translation', 'tickets_status.id = tickets_status_translation.ticket_status_id');
        
        $this->db->where('tickets_status.id',$id);
        $this->db->where('tickets_status_translation.lang_id',$display_lang_id);
        
        $result = $this->db->get('tickets_status');

        if($result)
        {
            return $result->row();    
        }else{
            return false ;
        }
    }
    
    public function get_category_row($id)
    {
         $row = $this->db->where('id',$id)->get('tickets_status');
         if($row){
            return $row->row();
         }else{
            return false;
        }
    }
    
   public function get_category_translation_result($id)
    {
        $this->db->where('ticket_status_id',$id);
        $query = $this->db->get('tickets_status_translation');
        
        if($query)
        {
            return $query->result();
        }
    }
    
     public function get_ticket_status_translation_result($lang_id)
    {
        $this->db->select('tickets_status_translation.*, tickets_status.active');
        $this->db->join('tickets_status', 'tickets_status_translation.ticket_status_id = tickets_status.id');
        
        $this->db->where('tickets_status.active', 1);
        $this->db->where('tickets_status_translation.lang_id',$lang_id);
        
        $query = $this->db->get('tickets_status_translation');
        
        if($query)
        {
            return $query->result();
        }
    }
    
    public function get_ticket_status($lang_id)
    {
        $this->db->select('tickets_status_translation.*, tickets_status.*, tickets_status.id as id');
        
        $this->db->join('tickets_status_translation', 'tickets_status.id = tickets_status_translation.ticket_status_id');
        
        $this->db->where('tickets_status_translation.lang_id',$display_lang_id);
        
        $result = $this->db->get('tickets_status');

        if($result)
        {
            return $result->result();    
        }
    }
   /*************************DELETE*******************************/
   
    public function delete_category_data($categories_id_array)
    {
        $this->db->where_in('id',$categories_id_array);
        $this->db->delete('tickets_status');
        
        $this->db->where_in('ticket_status_id',$categories_id_array);
        $this->db->delete('tickets_status_translation');
        echo '1';  
    }
    
  /*****************INSERT***************************************/
  
   public function insert_tickets_status($data)
    {
                
        return $this->db->insert('tickets_status',$data);
    } 
    
    public function insert_tickets_status_translation($cat_translation_data)
    {
        return $this->db->insert('tickets_status_translation',$cat_translation_data);
    }
    
    /***********************UPDATE*************************/
    
    public function update_category($cat_id,$category_data)
    {
        $this->db->where('id',$cat_id);
        $this->db->update('tickets_status',$category_data);
    }  
    
   public function update_cat_translation($cat_id,$lang_id,$cat_translation_data)
    {
        $this->db->where('ticket_status_id',$cat_id);
        $this->db->where('lang_id',$lang_id);
        $this->db->update('tickets_status_translation',$cat_translation_data);
    }   
/////////////////////////////////////////////////   
}