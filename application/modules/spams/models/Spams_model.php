<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class spams_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    /**********************INSERT*******************************/
    public function insert_spam($data)
    {
        return $this->db->insert('spams_reasons', $data);
    }
    
    public function insert_spam_reasons_translation($spam_translation_data)
    {
        return $this->db->insert('spam_reasons_translation', $spam_translation_data);
    }
    
    
    public function insert_user_spam_data($data)
    {
        return $this->db->insert('users_spams', $data);
    }
   
    /**********************GET*******************************/
    public function get_spam_row($id)
    {
        $this->db->where('id',$id);
        $query = $this->db->get('spams_reasons');
        
        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }
    public function get_spam_translation_result($id)
    {
        
        $this->db->where('spam_reasons_translation.reason_id', $id);
        $query = $this->db->get('spam_reasons_translation');
        
        if($query)
        {
            return $query->result();
        }
        else
        {
            return false;
        }
    }
    
    
    /**********************Update*******************************/
    public function update_spam($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('spams_reasons',$data);
    }
    public function update_spam_reasons_translation($id, $lang_id, $spam_translation_data)
    {
        $this->db->where('reason_id', $id);
        $this->db->where('lang_id', $lang_id);
        return $this->db->update('spam_reasons_translation', $spam_translation_data);
    }
   
    /**********************DELETE*******************************/ 
    public function delete_spam_data($ids_array)
    {
        $this->db->where_in('id', $ids_array);
        $this->db->delete('spams_reasons');
        
        $this->db->where_in('reason_id', $ids_array);
        $this->db->delete('spam_reasons_translation');
        
        echo '1'; 
    }
    
    public function get_count_all_spams($lang_id ,$search_word ='')
    {
        $this->db->join('spam_reasons_translation' ,'spams_reasons.id = spam_reasons_translation.reason_id');
        
        if(trim($search_word) !='')
        {
            $this->db->like('spam_reasons_translation.name', $search_word, 'both');  
        }
        
        $this->db->where('spam_reasons_translation.lang_id',$lang_id);
        
        return $this->db->count_all_results('spams_reasons');
    }
    
    public function get_spams_data($lang_id, $limit=0, $offset=0, $search_word='', $order_by='', $order_state='desc')
    {
        $this->db->select('spam_reasons_translation.* , spams_reasons.*, spam_reasons_translation.name as spam_name');
        
        $this->db->join('spam_reasons_translation' ,'spams_reasons.id = spam_reasons_translation.reason_id 
                         AND spam_reasons_translation.lang_id ='. $lang_id);
        
        if(trim($search_word) !='')
        {
            $this->db->like('spam_reasons_translation.name', $search_word, 'both');  
        }
        
        if($order_by != '')
        {
            if($order_by == lang('spam'))
            {
                $this->db->order_by('spam_reasons_translation.name', $order_state);
            }
            else
            {
                $this->db->order_by('spams_reasons.id', $order_state);
            }
        }
        else
        {
            $this->db->order_by('spams_reasons.id', $order_state);
        }
        
        if($limit != 0)
        {
            $result = $this->db->get('spams_reasons', $limit, $offset);
        }
        else
        {
            $result = $this->db->get('spams_reasons');
        }

        if($result)
        {
            return $result->result();    
        }
        else
        {
            return false;
        }
    }
    
    public function get_row_data($id, $display_lang_id)
    {
        $this->db->select('spam_reasons_translation.* , spams_reasons.* , spams_reasons.id as id, spam_reasons_translation.name as name');
        
        $this->db->join('spam_reasons_translation' ,'spams_reasons.id = spam_reasons_translation.reason_id');
        
        $this->db->where('spams_reasons.id',$id);
        $this->db->where('spam_reasons_translation.lang_id',$display_lang_id);
        
        $result = $this->db->get('spams_reasons');

        if($result)
        {
            return $result->row();    
        }
        else
        {
            return false;
        }
    }
    
    
    public function all_spams()
    {
        $this->db->where('active', 1);
        
        $result = $this->db->get('spams_reasons');
        
        if($result)
        {
            return $result->result_array();
        }
        else
        {
            return false;
        }
    }
    
    public function get_all_spams($display_lang_id)
    {
        $this->db->select('spam_reasons_translation.* , spams_reasons.* , spams_reasons.id as id, spam_reasons_translation.name as name');
        
        $this->db->join('spam_reasons_translation' ,'spams_reasons.id = spam_reasons_translation.reason_id');
        $this->db->where('spam_reasons_translation.lang_id',$display_lang_id);
        
        $result = $this->db->get('spams_reasons');

        if($result)
        {
            return $result->result();    
        }
        else
        {
            return false;
        }
    }
    
    public function get_user_blocked_users($user_id, $limit, $offset)
    {
        $this->db->select('users.*, users_spams.*, users.id as blocked_user_id');
        $this->db->join('users', 'users_spams.product_owner_id=users.id');
        
        $this->db->where('users_spams.user_id', $user_id);
        $this->db->where('users_spams.block_user', 1);
        $this->db->group_by('users.id');
        
        $result = $this->db->get('users_spams', $limit, $offset);
        
        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }
    
    public function update_user_spams($unblock_data, $conditions_array)
    {
        foreach($conditions_array as $key=>$val)
        {
            $this->db->where($key, $val);
        }
        
        return $this->db->update('users_spams', $unblock_data);
    }
    
    
/****************************************************************/
}