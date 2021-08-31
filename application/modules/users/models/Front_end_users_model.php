<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Front_end_users_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    function get_user_group_id($user_id)
    {
       $row=$this->db->get_where('users_groups',array('user_id'=>$user_id))->row();
       if($row)
       {
            return $row->group_id;
       }
    }
   
   public function get_user($user_id)
   {
        $this->db->where('id',$user_id);
        $row=$this->db->get('users')->row();
        if($row)
        {
            return $row;
        }else{
            return false;
        }
   }
   
   public function get_users()
   {
        $query = $this->db->get('users');
        if($query)
        {
            return $query->result();
        }else{
            return false;
        }
   }
   
   ///////////////groups functions ///////////
   public function insert_group($data)
   {
        return $this->db->insert('groups', $data);
   }
   
   public function insert_group_translation($groups_translation_data)
   {
        return $this->db->insert('groups_translation', $groups_translation_data);
   }
   
   public function get_countries()
   {
        $result = $this->db->get('countries')->result();
        return $result;
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
    public function update_groups_data($group_id ,$group_general_data)
    {
        $this->db->where('id',$group_id);
        $this->db->update('groups',$group_general_data);
    }
    public function update_group_translation($group_id , $groups_translation_data)
    {
        $this->db->where('group_id',$group_id);
        $this->db->update('groups_translation',$groups_translation_data);
    }
    
    public function get_users_filter_data()
    {
        $this->db->select('users.username, users.id, users.username as name');
        $query = $this->db->get('users');
        
        if($query)
        {
            return $query->result();
        }
        
    }
    public function get_count_all_users($search_word='')
    {
        if(trim($search_word) !='')
        {
            $this->db->like('username', $search_word, 'both'); 
            $this->db->or_like('first_name', $search_word, 'both');
            $this->db->or_like('last_name', $search_word, 'both'); 
            $this->db->or_like('email', $search_word, 'both');
       }
        
       return $this->db->count_all_results('users');
    }
    
   public function get_users_data($limit,$offset,$search_word='',$order_by='',$order_state)
    {
        if(trim($search_word) !='')
        {
            $this->db->like('username', $search_word, 'both');
            $this->db->or_like('first_name', $search_word, 'both');
            $this->db->or_like('last_name', $search_word, 'both');  
            $this->db->or_like('email', $search_word, 'both'); 
            
        }
        
        if($order_by != '')
        {
            if($order_by == lang('username'))
            { 
                $this->db->order_by('username',$order_state);
            }
            elseif($order_by == lang('email'))
            {
                $this->db->order_by('email',$order_state);
            }
            
        }
        else
        {
            $this->db->order_by('id',$order_state);
        }
        
        $result = $this->db->get('users',$limit,$offset);

        if($result)
        {
            return $result->result();    
        }
    }
   
     public function get_row_data($id)
    {
        
        $this->db->where('id',$id);
       
        $result = $this->db->get('users');

        if($result)
        {
            return $result->row();    
        }
    } 
    
  ////////////////////////////////////////////////  
}