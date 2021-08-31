<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Notifications_model extends CI_Model
{
    
    
    public function get_event_row($event)
    {
        $this->db->where('event',$event);
        $query = $this->db->get('events');
        
        if($query)
        {
            return $query->row();
        
        }else{
            
            return false ;
        }
    }
    
    public function get_template_row($temp_id , $lang_id)
    {
        $this->db->select('templates_translation.*,templates.id as id');
        $this->db->join('templates_translation','templates.id = templates_translation.template_id');
        
        $this->db->where('templates.id',$temp_id);
        $this->db->where('templates_translation.lang_id',$lang_id);
        
        $query = $this->db->get('templates');
        
        if($query)
        {
            return $query->row();
        
        }else{
            
            return false ;
        }
    }
    
    public function get_notification_sms()
    {
        $this->db->where('active',1);
        
        $query = $this->db->get('sms_notifications');
        
        if($query)
        {
            return $query->result();
        
        }else{
            
            return false ;
        }
    }
    
    public function get_notification_emails($store_id=0)
    {
        $this->db->group_start();
            $this->db->where('store_id', $store_id);
            $this->db->or_where('store_id', 0);
        $this->db->group_end();
        
        $this->db->group_start();
            $this->db->where('active',1);
        $this->db->group_end();
        
        $query = $this->db->get('notification_emails');
        
        if($query)
        {
            return $query->result();
        
        }
        else
        {
            
            return false ;
        }
    }
    
    public function get_admin_notifications($limit= 0, $stores_ids=array())
    {
        $this->db->where('type','admin');
        $this->db->order_by('id','desc');
        
        if(count($stores_ids) != 0)
        {
            $this->db->where_in('store_id', $stores_ids);
        }
        
        if($limit == 0)
        {
            $query = $this->db->get('notifications');
        }
        else
        {
            $query = $this->db->get('notifications', $limit);
        }
        
        if($query)
        {
            return $query->result();
        
        }else{
            
            return false ;
        }
    }
    
    public function get_admin_unread_notifications($stores_ids=array())
    {
        $this->db->where('type','admin');
        $this->db->where('read',0);
        $this->db->order_by('id','desc');
        
        
        if(count($stores_ids) != 0)
        {
            $this->db->where_in('store_id', $stores_ids);
        }
        
       return $this->db->count_all_results('notifications');
        
        
    }
    /*********************** Insert ***********************/
    
    public function insert_notification($data)
    {
        $this->db->insert('notifications',$data);
    }
////////////////////////////
}
