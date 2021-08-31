<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class User_balance_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    public function insert_balance_log($balance_log_data)
    {
        return $this->db->insert('balance_log', $balance_log_data);
    }
    
    public function get_user_balance_log($user_id, $display_lang_id, $perPage, $offset)
    {
        $this->db->select('balance_log.*, balance_log_status_translation.name, payment_methods_translation.name, balance_log_status_translation.name as status, payment_methods_translation.name as method');
        
        $this->db->join('balance_log_status_translation', 'balance_log.balance_status_id = balance_log_status_translation.status_id');
        $this->db->join('payment_methods_translation', 'balance_log.payment_method_id = payment_methods_translation.payment_method_id AND payment_methods_translation.lang_id='.$display_lang_id, 'left');
        
        $this->db->order_by('balance_log.id', 'desc');
        $this->db->where('balance_log.user_id', $user_id);
        $this->db->where('balance_log_status_translation.lang_id', $display_lang_id);
        $this->db->group_by('balance_log.id');
        
        $result = $this->db->get('balance_log', $perPage, $offset);
        
        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }
    
    public function get_all_balance_log_count($user_id)
    {
        $this->db->where('user_id', $user_id);
        
        return $this->db->count_all_results('balance_log');
    }
    
    public function get_code_data($serial, $pin)
    {
        $this->db->where('serial', $serial);
        $this->db->where('pin', $pin);
        $this->db->where('active', 1);
        
        $query = $this->db->get('serials');
        
        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }
    
    public function update_serial_data($id, $updated_data)
    {
        $this->db->where('id', $id);
        return $this->db->update('serials', $updated_data);
    }
    
    public function get_log_status_name($status_id, $lang_id)
    {
        $this->db->where('status_id', $status_id);
        $this->db->where('lang_id', $lang_id);
        
        $query = $this->db->get('balance_log_status_translation')->row();
        
        if($query)
        {
            return $query->name;
        }
        else
        {
            return false;
        }
    }
    
    
   
    
/****************************************************************/
}