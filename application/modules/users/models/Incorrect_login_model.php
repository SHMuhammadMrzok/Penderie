<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Incorrect_login_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    
    /**********************GET*******************************/
    
    /**********************DELETE*******************************/ 
    public function delete_data($ids)
    {
        $this->db->where_in('id', $ids);
        $this->db->delete('incorrect_login_attempts');
        
        echo '1'; 
    }
    
    public function get_count_all_data($lang_id, $search_word ='')
    {
        if(trim($search_word) !='')
        {
            $this->db->where('(incorrect_login_attempts.email LIKE "%'.$search_word.'%" OR incorrect_login_attempts.password LIKE "%'.$search_word.'% OR incorrect_login_attempts.ip_address LIKE "%'.$search_word.'% OR incorrect_login_attempts.agent LIKE "%'.$search_word.'%")');
        }
        
        return $this->db->count_all_results('incorrect_login_attempts');
    }
    
    public function get_data($lang_id, $limit, $offset, $search_word='', $order_by='',$order_state)
    {
        $this->db->select('incorrect_login_attempts.*');
        
        if(trim($search_word) !='')
        {
            $this->db->where('(incorrect_login_attempts.email LIKE "%'.$search_word.'%" OR incorrect_login_attempts.password LIKE "%'.$search_word.'% OR incorrect_login_attempts.ip_address LIKE "%'.$search_word.'% OR incorrect_login_attempts.agent LIKE "%'.$search_word.'%")'); 
        }
        
        $this->db->order_by('incorrect_login_attempts.id', $order_state);
        
        $result = $this->db->get('incorrect_login_attempts', $limit, $offset);

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
        
        $query = $this->db->get('incorrect_login_attempts');

        if($query)
        {
            return $query->row();    
        }
        else
        {
            return false;
        }
    }
/****************************************************************/
}