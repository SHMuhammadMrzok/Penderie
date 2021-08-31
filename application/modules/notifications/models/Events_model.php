<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Events_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    /********************Insert *****************************/
    public function insert_events_data($events_data)
    {
        return $this->db->insert('events', $events_data);
    }
    
    public function insert_events_translation($events_translation_data)
    {
        return $this->db->insert('events_translation', $events_translation_data);
    }
   /**********************GET*******************************/
    
    public function get_count_all_events($lang_id,$search_word='')
    {
        $this->db->join('events_translation' ,'events.id = events_translation.event_id');
        $this->db->join('templates_translation', 'events.template_id = templates_translation.template_id');
        
        if(trim($search_word) !='')
        {
            $this->db->where('(events_translation.name LIKE "%'.$search_word.'%" OR events.event LIKE "%'.$search_word.'%")');
        }
        
        $this->db->where('events_translation.lang_id',$lang_id);
        $this->db->where('templates_translation.lang_id',$lang_id);
        
       return $this->db->count_all_results('events');
    }
   
   public function get_events_data($lang_id,$limit,$offset,$search_word,$order_by,$order_state)
    {
            
            $this->db->select('events_translation.*, events.*, events.id as id');// ,templates_translation.name as template_name');
            
            $this->db->join('events_translation', 'events.id = events_translation.event_id');
            //$this->db->join('templates_translation', 'events.admin_template_id = templates_translation.template_id');
            
            $this->db->where('events_translation.lang_id',$lang_id);
            //$this->db->where('templates_translation.lang_id',$lang_id);
            
            if(trim($search_word) !='')
            {
                $this->db->where('(events_translation.name LIKE "%'.$search_word.'%" OR events.event LIKE "%'.$search_word.'%")');
            }
            
            
            if($order_by != '')
            {
                if($order_by == lang('name'))
                { 
                    $this->db->order_by('events_translation.name',$order_state);
                }
                elseif($order_by == lang('event'))
                {
                    $this->db->order_by('events.event',$order_state);
                }
                elseif($order_by == lang('active'))
                {
                    $this->db->order_by('events.active',$order_state);
                }
                else
                {
                    $this->db->order_by('events.id',$order_state);
                }
            }
            else
            {
                $this->db->order_by('events.id',$order_state);
            }
            
            $result = $this->db->get('events',$limit,$offset);
    
            if($result)
            {
                return $result->result();    
            }
    }
    
    public function get_row_data($id,$display_lang_id)
    {
        $this->db->select('events_translation.*, events.*, events.id as id ,templates_translation.name as template_name');
        
        $this->db->join('events_translation', 'events.id = events_translation.event_id');
        $this->db->join('templates_translation', 'events.template_id = templates_translation.template_id AND templates_translation.lang_id = '.$display_lang_id, 'left');
        
        $this->db->where('events.id',$id);
        $this->db->where('events_translation.lang_id',$display_lang_id);
        //$this->db->where('templates_translation.lang_id',$display_lang_id);
        
        $result = $this->db->get('events');

        if($result)
        {
            return $result->row();    
        }
    }
  
   public function get_templates()
   {
        $active_lang_id    = $this->data['active_language']->id;
        $this->db->select('templates_translation.*, templates.*, templates.id as id');
        
        $this->db->join('templates_translation', 'templates.id = templates_translation.template_id');
        
        $this->db->where('templates_translation.lang_id',$active_lang_id);
        $this->db->where('templates.active',1);
        
        $result = $this->db->get('templates');

        if($result)
        {
            return $result->result();    
        }
   }
   
   public function get_user_templates($lang_id)
   {        
        $this->db->select('templates_translation.*, templates.*, templates.id as id');
        $this->db->join('templates_translation', 'templates.id = templates_translation.template_id');
        
        $this->db->where('templates_translation.lang_id', $lang_id);
        $this->db->where('templates.admin', 0);
        $this->db->where('templates.active', 1);
        
        $result = $this->db->get('templates');

        if($result)
        {
            return $result->result();    
        }
        else
        {
            return false;
        }
   }
   
   public function get_admin_templates($lang_id)
   {
        $this->db->select('templates_translation.*, templates.*, templates.id as id');
        $this->db->join('templates_translation', 'templates.id = templates_translation.template_id');
        
        $this->db->where('templates_translation.lang_id', $lang_id);
        $this->db->where('templates.active', 1);
        $this->db->where('templates.admin', 1);
        
        $result = $this->db->get('templates');

        if($result)
        {
            return $result->result();    
        }
        else
        {
            return false;
        }
   }
   
   public function get_events_translation_result($id)
   {
           $this->db->where('event_id',$id);
           $result = $this->db->get('events_translation');
    
            if($result)
            {
                return $result->result();    
            }
   }
    
    
    
    public function get_row_data_by_event_var($event_var)
    {
        $this->db->select('events.*, events.id as event_id');
        
        $this->db->where('events.event',$event_var);
        
        $result = $this->db->get('events');
    
        if($result)
        {
            return $result->row();    
        }
    }
   
  
    /**********************Update*******************************/
    public function update_events_data($id,$data)
    {
        $this->db->where('id',$id);
        return $this->db->update('events',$data);
    }
    
    public function update_events_translation($id,$lang_id,$data)
    {
        $this->db->where('event_id',$id);
        $this->db->where('lang_id',$lang_id);
        return $this->db->update('events_translation',$data);
    }
    /**********************DELETE*******************************/ 
   
    public function delete_events_data($ids_array)
    {
        $this->db->where_in('id',$ids_array);
        $this->db->delete('events');
        
        $this->db->where_in('event_id',$ids_array);
        $this->db->delete('events_translation');
        
        echo '1';  
    }
    
   
    
/****************************************************************/
}