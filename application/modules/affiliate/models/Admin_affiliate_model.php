<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Admin_affiliate_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    /****************Get****************/
  
    public function get_count_all_affiliate($search_word='')
    {
        $this->db->select('users.username ,users.email, affiliate.*, affiliate.id as id');
        
        $this->db->join('users', 'affiliate.user_id = users.id');
        
        if(trim($search_word) !='')
        {
            $this->db->like('users.username', $search_word, 'both');
        }
        
        return $this->db->count_all_results('affiliate');
    }
    
    public function get_affiliate_data($limit,$offset,$search_word='',$order_by='',$order_state)
    {
        $this->db->select('users.first_name, users.last_name ,users.email, affiliate.*, affiliate.id as id');
        
        $this->db->join('users', 'affiliate.user_id = users.id');
        
        if(trim($search_word) !='')
        {
            $this->db->like('users.username', $search_word, 'both');
            
        }
        
        if($order_by != '')
        {
            if($order_by == lang('username'))
            { 
                $this->db->order_by('users.username',$order_state);
            }
            
            elseif($order_by == lang('active'))
            {
                $this->db->order_by('affiliate.active',$order_state);
            }
            
            else
            {
                $this->db->order_by('affiliate.id',$order_state);
            }
        }
        else
        {
            $this->db->order_by('affiliate.id',$order_state);
        }
        
        $result = $this->db->get('affiliate',$limit,$offset);
 
        if($result)
        {
            return $result->result();    
        }
    }
    
    public function get_row_data($id)
    {
        $this->db->select('users.* ,users.email, affiliate.*, affiliate.id as id');
        
        $this->db->join('users', 'affiliate.user_id = users.id');
        
        $this->db->where('affiliate.id',$id);
        
        $result = $this->db->get('affiliate');

        if($result)
        {
            return $result->row();    
        }
    }
    
    public function get_afiliate_for_user($user_id)
    {
        $this->db->where('user_id', $user_id);
        $row = $this->db->get('affiliate');
        
        if($row)
        {
            return $row->row();
        }
        else
        {
            return false;
        }
    }
    
    public function get_user_affiliate($user_id)
    {
        $this->db->where('user_id', $user_id);
        $query = $this->db->get('affiliate');
        
        if($query->row())
        {
            return $query->row()->code;
        }
        else
        {
            return false;
        }
    }
    
    public function get_affiliate_users()
    {
        $result = $this->db->get('affiliate');
        
        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }
    
    public function get_affiliae_available_users($affiliate_ids)
    {
        if(count($affiliate_ids) != 0)
        {
            $this->db->where_not_in('id', $affiliate_ids);
        }
        $result = $this->db->get('users');
        
        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }
    
   
   /*************************DELETE*******************************/
   
    public function delete_affiliate_data($affiliate_id_array)
    {
        $this->db->where_in('id',$affiliate_id_array);
        $this->db->delete('affiliate');
        
        echo '1';  
    }
    
  /*****************INSERT***************************************/
  
   public function insert_affiliate($data)
    {
                
        return $this->db->insert('affiliate',$data);
    } 
    
   
    /***********************UPDATE*************************/
    
    public function update_category($cat_id,$category_data)
    {
        $this->db->where('id',$cat_id);
        $this->db->update('affiliate',$category_data);
    }
    
    public function update_affiliate($updated_data, $affiliate_id)
    {
        $this->db->where('id', $affiliate_id);
        return $this->db->update('affiliate', $updated_data);
    }  
    
   
/////////////////////////////////////////////////   
}