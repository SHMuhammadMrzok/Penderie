<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Sms_notifications_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    /********************Insert *****************************/
    public function insert_sms_notifications_data($sms_notifications_data)
    {
        return $this->db->insert('sms_notifications', $sms_notifications_data);
    }
   /**********************GET*******************************/
    
    public function get_count_all_notifications()
    {
        return $this->db->count_all_results('sms_notifications');
    }
   
   public function get_sms_notifications_data($limit,$offset,$search_word)
    {
       
        if(trim($search_word) !='')
        {
            $this->db->like('name', $search_word, 'both'); 
            $this->db->or_like('mobile', $search_word, 'both'); 
        }
       
        $result = $this->db->get('sms_notifications',$limit,$offset);

        if($result)
        {
            return $result->result();    
        }
    }
    
    public function get_row_data($id)
    {
       $this->db->where('id',$id);
         
        $result = $this->db->get('sms_notifications');

        if($result)
        {
            return $result->row();    
        }
    }
  
   
    /**********************Update*******************************/
    public function update_sms_notifications_data($id,$data)
    {
        $this->db->where('id',$id);
        return $this->db->update('sms_notifications',$data);
    }
    
    /**********************DELETE*******************************/ 
   
    public function delete_sms_notifications_data($ids_array)
    {
        $this->db->where_in('id',$ids_array);
        $this->db->delete('sms_notifications');
        echo 1;
    }
    
   
    
/****************************************************************/
}