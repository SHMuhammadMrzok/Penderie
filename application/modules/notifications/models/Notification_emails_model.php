<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Notification_emails_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    /********************Insert *****************************/
    public function insert_notification_emails_data($notification_emails_data)
    {
        return $this->db->insert('notification_emails', $notification_emails_data);
    }
   /**********************GET*******************************/
    
    public function get_count_all_notifications()
    {
        return $this->db->count_all_results('notification_emails');
    }
   
   public function get_notification_emails_data($limit,$offset,$search_word)
    {
       
        if(trim($search_word) !='')
        {
            $this->db->like('name', $search_word, 'both'); 
            $this->db->or_like('email', $search_word, 'both'); 
        }
       
        $result = $this->db->get('notification_emails',$limit,$offset);

        if($result)
        {
            return $result->result();    
        }
    }
    
    public function get_row_data($id)
    {
       $this->db->where('id',$id);
         
        $result = $this->db->get('notification_emails');

        if($result)
        {
            return $result->row();    
        }
    }
  
   
    /**********************Update*******************************/
    public function update_notification_emails_data($id,$data)
    {
        $this->db->where('id',$id);
        return $this->db->update('notification_emails',$data);
    }
    
    /**********************DELETE*******************************/ 
   
    public function delete_notification_emails_data($ids_array)
    {
        $this->db->where_in('id',$ids_array);
        $this->db->delete('notification_emails');
        
    }
    
   
    
/****************************************************************/
}