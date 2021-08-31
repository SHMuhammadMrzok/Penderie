<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Notification_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    /********************Insert *****************************/
    public function insert_notifications_data($notifications_data)
    {
        return $this->db->insert('notifications', $notifications_data);
    }
    
    public function insert_push_notification_data($notifications_data)
    {
        return $this->db->insert('push_notifications', $notifications_data);
    }
   /**********************GET*******************************/
    
    public function get_count_all_notifications($search_word = '',$type_filter = 0)
    {
        if(trim($search_word) !='')
        {
            $this->db->like('notification_text', $search_word, 'both'); 
        }
        if($type_filter !=0)
        {
            $this->db->where('type', $type_filter);
        }
        return $this->db->count_all_results('notifications');
    }
    
    public function get_count_all_push_notifications($search_word = '')
    {
        if(trim($search_word) !='')
        {
            $this->db->like('text', $search_word, 'both'); 
        }
        
        return $this->db->count_all_results('push_notifications');
    }
   
    public function get_notifications_data($limit, $offset, $lang_id, $search_word, $order_by, $order_state, $type_filter=0 )
    {
        $this->db->select('notifications.*, events_translation.name, events_translation.name as event');
        
        $this->db->join('events_translation', 'notifications.event_id = events_translation.event_id');
        
        $this->db->where('events_translation.lang_id', $lang_id);
       
        if(trim($search_word) !='')
        {
            $this->db->like('notifications.notification_text', $search_word, 'both'); 
        }
        
        if($order_by != '')
        {
            if($order_by == lang('notification_type'))
            {
                $this->db->order_by('notifications.type',$order_state);
            }
            elseif($order_by == lang('notification_text'))
            {
                $this->db->order_by('notifications.notification_text',$order_state);
            }
            elseif($order_by == lang('unix_time'))
            {
                $this->db->order_by('notifications.unix_time',$order_state);
            }
            else
            {
                $this->db->order_by('notifications.id',$order_state);
            }
            
        }
        else
        {
            $this->db->order_by('notifications.id',$order_state);
        }
        if($type_filter == 'admin')
        {
           $this->db->where('notifications.type','admin');
        
        }elseif($type_filter == 'email')
        {
           $this->db->where('notifications.type','email');
        
        }elseif($type_filter == 'sms')
        {
           $this->db->where('notifications.type','sms');
        }
        
        $result = $this->db->get('notifications',$limit,$offset);

        if($result)
        {
            return $result->result();    
        }
    }
    
    public function get_push_notifications_data($limit, $offset, $search_word='', $order_by='', $order_state)
    {
        if(trim($search_word) !='')
        {
            $this->db->like('push_notifications.text', $search_word, 'both'); 
        }
        
        if($order_by != '')
        {
            if($order_by == lang('text'))
            {
                $this->db->order_by('push_notifications.tetx', $order_state);
            }
            else
            {
                $this->db->order_by('push_notifications.id', $order_state);
            }
            
        }
        else
        {
            $this->db->order_by('push_notifications.id', $order_state);
        }
        
        
        $result = $this->db->get('push_notifications', $limit, $offset);

        if($result)
        {
            return $result->result();    
        }
        else
        {
            return false;
        }
    }
    
    public function get_row_data($id)
    {
       $this->db->where('id',$id);
         
        $result = $this->db->get('notifications');

        if($result)
        {
            return $result->row();    
        }
    }
    
    public function get_push_notifications_row_data($id)
    {
       $this->db->where('id', $id);
         
        $result = $this->db->get('push_notifications');

        if($result)
        {
            return $result->row();    
        }
        else
        {
            return false;
        }
    }
   
    /**********************Update*******************************/
    public function update_notifications_data($id,$data)
    {
        $this->db->where('id',$id);
        return $this->db->update('notifications', $data);
    }
    
    public function read_notifications()
    {
        $data=array('read'=>1);
        $this->db->where('read',0);
        return $this->db->update('notifications', $data);
           
    }
    
    public function update_push_notification($id, $data)
    {
        $this->db->where('id',$id);
        return $this->db->update('push_notifications', $data);
    }
    /**********************DELETE*******************************/ 
   
    public function delete_notifications_data($ids_array)
    {
        $this->db->where_in('id',$ids_array);
        $this->db->delete('notifications');
        
    }
    
    public function delete_push_notifications_data($ids_array)
    {
        $this->db->where_in('id', $ids_array);
        $this->db->delete('push_notifications');
        
    }
    
   
    
/****************************************************************/
}