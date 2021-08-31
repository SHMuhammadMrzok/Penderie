<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
    
class Mail_list_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        
    }
    
    public function insert_user($data)
    {
        return $this->db->insert('mail_list_members', $data);
    }
    
    public function get_count_all_data($lang_id, $search_word ='')
    {
        if(trim($search_word) !='')
        {
            $this->db->where('(mail_list_members.email LIKE "%'.$search_word.'%")');
        }
        
        return $this->db->count_all_results('mail_list_members');
    }
    
    public function get_data($lang_id, $limit, $offset, $search_word='', $order_by='',$order_state)
    {
        $this->db->select('mail_list_members.*');
        
        if(trim($search_word) !='')
        {
            $this->db->where('(mail_list_members.email LIKE "%'.$search_word.'%")'); 
        }
        
        $this->db->order_by('mail_list_members.id', $order_state);
        
        $result = $this->db->get('mail_list_members', $limit, $offset);

        if($result)
        {
            return $result->result();    
        }
        else
        {
            return false;
        }
    }
    
    public function get_row_data($id,$display_lang_id)
    {
        $this->db->where('id', $id);
        
        $query = $this->db->get('mail_list_members');

        if($query)
        {
            return $query->row();    
        }
        else
        {
            return false;
        }
    }
    
    public function delete_data($ids)
    {
        $this->db->where_in('id', $ids);
        $this->db->delete('mail_list_members');
        
        echo '1'; 
    }
    
    public function get_mail_list_member($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('mail_list_members');
        
        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }
    
    public function update_mail_member($id, $updated_data)
    {
        $this->db->where('id', $id);
        return $this->db->update('mail_list_members', $updated_data);
    }
    
    public function count_user_email($email)
    {
        $this->db->where('email', $email);
        return $this->db->count_all_results('mail_list_members');
    }
}