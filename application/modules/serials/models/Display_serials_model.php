<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Display_serials_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    
    /**********************GET*******************************/
    public function get_serial_row($id)
    {
        $this->db->where('id', $id);
        $result = $this->db->get('serials');

        if($result)
        {
            return $result->row();    
        }
    }
    
    public function get_serials_data($limit,$offset,$search_word='')
    {
       
        if(trim($search_word) !='')
        {
            $this->db->like('serial', $search_word, 'both');
            $this->db->or_like('pin', $search_word, 'both');
        }
       
        $result = $this->db->get('serials',$limit,$offset);

        if($result)
        {
            return $result->result();    
        }
    }
    
     public function get_count_all_serials()
    {
        return $this->db->count_all_results('serials');
    }
   
    public function get_all_serials()
    {
        $result = $this->db->get('serials');

        if($result)
        {
            return $result->result();    
        }
    }
    
    public function get_serial_unix_time($id)
    {
        $this->db->where('id',$id);
        $result = $this->db->get('serials');

        if($result)
        {
            return $result->row()->unix_time;    
        }
    }
    
    public function get_serial_log_data($serial_id)
    {
        $this->db->select('users.username, balance_log.*');
        $this->db->join('users', 'balance_log.user_id = users.id');
        
        $this->db->where('code', 1);
        $this->db->where('payment_method_id', $serial_id);
        
        $query = $this->db->get('balance_log');
        
        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }
    /**********************Update*******************************/
    public function update_serials($vendor_id,$data)
    {
        $this->db->where('id',$vendor_id);
        return $this->db->update('serials',$data);
    }
    public function update_serials_translation($vendor_id,$lang_id,$serials_translation_data)
    {
        $this->db->where('vendor_id',$vendor_id);
        $this->db->where('lang_id',$lang_id);
        return $this->db->update('serials_translation',$serials_translation_data);
    }
   
    /**********************DELETE*******************************/ 
   
    public function delete_serials_data($serials_id_array)
    {
        $this->db->where_in('id',$serials_id_array);
        $this->db->delete('serials');
        
    }
    
 
   /**********************Update*******************************/
    public function update_serial($id,$data)
    {
        $this->db->where('id',$id);
        return $this->db->update('serials',$data);
    }
   
   
/****************************************************************/



}