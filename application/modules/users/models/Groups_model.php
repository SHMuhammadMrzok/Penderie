<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Groups_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
  /***********************delete********************/  
    public function delete_group_data($groups_id_array)
    {
        $this->db->where_in('id',$groups_id_array);
        $this->db->delete('groups');
        
        $this->db->where_in('group_id',$groups_id_array);
        $this->db->delete('groups_translation'); 
            
       //$this->delete_groups_translation($groups_id_array);
    } 
   
    public function delete_groups_translation($groups_id_array)
    {
        $this->db->where_in('group_id',$groups_id_array);
        $this->db->delete('groups_translation'); 
    }
    
   /***********************insert********************/   
   public function insert_group($data)
   {
        return $this->db->insert('groups', $data);
   }
   
   public function insert_group_translation($groups_translation_data)
   {
        return $this->db->insert('groups_translation', $groups_translation_data);
   }
  
   /***********************get********************/   
   
   public function get_countries($lang_id)
   {
        $this->db->select('countries_translation.*,countries.*');
        $this->db->join('countries_translation','countries.id = countries_translation.country_id');
        $this->db->where('countries_translation.lang_id',$lang_id);
        
        $query = $this->db->get('countries');
        
        if($query)
        {
            return $query->result();
        }else{
            return false ;
        }
        
   } 
   public function get_group_result($id)
   {
       $this->db->where('id',$id);
       $query = $this->db->get('groups');
       
       if($query)
       {
           return $query->row();
       }
   }
    public function get_group_translation_result($id)
    {
        $this->db->select('groups_translation.*');
        $this->db->join('groups_translation','groups.id = groups_translation.group_id');
        $this->db->where('groups.id',$id);
        $query = $this->db->get('groups');
        
        if($query)
        {
            return $query->result();
        }
    }
    
    public function get_count_all_groups($lang_id,$search_word='')
    {
        $this->db->join('groups_translation' ,'groups.id = groups_translation.group_id');
        
        if(trim($search_word) !='')
        {
            $this->db->like('groups_translation.name', $search_word, 'both');  
            $this->db->or_like('groups_translation.description', $search_word, 'both');
       }
        
        $this->db->where('groups_translation.lang_id',$lang_id);
        
        return $this->db->count_all_results('groups');
    }
    
   public function get_groups_data($lang_id, $limit, $offset, $search_word='', $order_by='', $order_state)
    {
        $this->db->select('groups_translation.*, groups.*, groups.id as id');
        
        $this->db->join('groups_translation', 'groups.id = groups_translation.group_id');
        
        $this->db->where('groups_translation.lang_id',$lang_id);
        
        if(trim($search_word) !='')
        {
            $this->db->like('groups_translation.name', $search_word, 'both');
            $this->db->or_like('groups_translation.description', $search_word, 'both'); 
            
        }
        
        if($order_by != '')
        {
            if($order_by == lang('group_name'))
            { 
                $this->db->order_by('groups_translation.name',$order_state);
            }
            elseif($order_by == lang('description'))
            {
                $this->db->order_by('groups_translation.description',$order_state);
            }
            
            else
            {
                $this->db->order_by('groups.id',$order_state);
            }
        }
        else
        {
            $this->db->order_by('groups.id',$order_state);
        }
        
        $result = $this->db->get('groups',$limit,$offset);

        if($result)
        {
            return $result->result();    
        }
    }
   
     public function get_row_data($id,$display_lang_id)
    {
        $this->db->select('groups_translation.*, groups.*, groups.id as id');
        
        $this->db->join('groups_translation', 'groups.id = groups_translation.group_id');
        
        $this->db->where('groups.id',$id);
        $this->db->where('groups_translation.lang_id',$display_lang_id);
        
        $result = $this->db->get('groups');

        if($result)
        {
            return $result->row();    
        }
    } 
    
    public function get_groups($lang_id)
    {
        $this->db->select('groups_translation.*,groups.id');
        $this->db->join('groups_translation','groups.id = groups_translation.group_id');
        $this->db->where('groups_translation.lang_id',$lang_id);
        
        $query = $this->db->get('groups');
        
        if($query)
        {
            return $query->result();
        }
    }
    /***********************update********************/
    public function update_groups_data($group_id ,$group_general_data)
    {
        $this->db->where('id',$group_id);
        $this->db->update('groups',$group_general_data);
    }
    public function update_group_translation($group_id,$lang_id , $groups_translation_data)
    {
        $this->db->where('group_id',$group_id);
        $this->db->where('lang_id',$lang_id);
        
        $this->db->update('groups_translation',$groups_translation_data);
    }
    
    
   
///////////////////////////////////////////    
}