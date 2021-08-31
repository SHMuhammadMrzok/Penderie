<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Admin_templates_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    

    public function get_count_all_admin_templates($lang_id, $search_word='')
    {
        $this->db->join('templates_translation', 'templates.id = templates_translation.template_id');
        
        if(trim($search_word) !='')
        {
            $this->db->like('templates_translation.name', $search_word, 'both');  
        }
        
        $this->db->where('templates.admin', 1);
        $this->db->where('templates_translation.lang_id', $lang_id);
        
        return $this->db->count_all_results('templates');
    }
    
    public function get_admin_template_data($lang_id, $limit, $offset, $search_word, $order_by='', $order_state)
    {
        $this->db->select('templates.*, templates_translation.*, templates.id as id');
        $this->db->join('templates_translation', 'templates.id = templates_translation.template_id');
        
        $this->db->where('templates.admin', 1);
        $this->db->where('templates_translation.lang_id', $lang_id);
        
        if(trim($search_word) !='')
        {
            $this->db->like('templates_translation.name', $search_word, 'both'); 
        }
        
        if($order_by != '')
        {
            if($order_by == lang('name'))
            { 
                $this->db->order_by('templates_translation.name', $order_state);
            }
            elseif($order_by == lang('unix_time'))
            {
                $this->db->order_by('templates.unix_time', $order_state);
            }
            elseif($order_by == lang('active'))
            {
                $this->db->order_by('templates.active', $order_state);
            }
            elseif($order_by == lang('sort'))
            {
                $this->db->order_by('templates.sort', $order_state);
            }
            else
            {
                $this->db->order_by('templates.id', $order_state);
            }
        }
        else
        {
            $this->db->order_by('templates.id', $order_state);
        }
        
        $result = $this->db->get('templates', $limit, $offset);

        if($result)
        {
            return $result->result();    
        }
        else
        {
            return false;
        }
    }
    
    public function check_used_template($temp_ids_array)
    {
        $this->db->where_in('admin_template_id', $temp_ids_array);
        $count = $this->db->count_all_results('events');
        
        if($count > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    public function delete_emails_template_data($id_array)
    {
        $this->db->where_in('id', $id_array);
        $this->db->delete('templates');
            
        $this->db->where_in('template_id', $id_array);
        $this->db->delete('templates_translation');
        echo '1';    
        
    }
    
    public function insert_template_data($general_data)
    {
        return $this->db->insert('templates', $general_data);
    }
    
    public function insert_template_translation($template_translation_data)
    {
        return $this->db->insert('templates_translation', $template_translation_data);
    }
    
    public function get_template_row($id)
    {
        $this->db->where('id', $id);
        $result = $this->db->get('templates');
        
        if($result)
        {
            return $result->row();
        }
        else
        {
            return false;
        }
    }
   
   public function get_template_translation($id)
   {
        $this->db->where('template_id', $id);
        $result = $this->db->get('templates_translation');
        
        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
   }
   
   public function update_template($temp_id, $template_data)
   {
        $this->db->where('id', $temp_id);
        return $this->db->update('templates', $template_data);
   }
   
   public function update_template_translation($temp_id, $lang_id, $template_translation_data)
   {
        $this->db->where('template_id', $temp_id);
        $this->db->where('lang_id', $lang_id);
        
        return $this->db->update('templates_translation', $template_translation_data);
   }
   
   public function get_row_data($id,$display_lang_id)
   {
        $this->db->select('templates.*, templates_translation.*, templates.id as id');
        
        $this->db->join('templates_translation', 'templates.id = templates_translation.template_id');
        
        $this->db->where('templates.id', $id);
        $this->db->where('templates_translation.lang_id', $display_lang_id);
        
        $result = $this->db->get('templates');

        if($result)
        {
            return $result->row();    
        }
        else
        {
            return false;
        }
   }
   
   public function get_event_variables($event_id, $lang_id)
   {
      $this->db->select('events_variables.*, events_variables_translation.*');
      
      $this->db->join('events_variables_translation', 'events_variables.id = events_variables_translation.event_variable_id');
      
      $this->db->where('events_variables.event_id', $event_id);
      $this->db->where('events_variables_translation.lang_id', $lang_id);
      
      $result = $this->db->get('events_variables');
      
      if($result)
      {
        return $result->result();
      }
      else
      {
        return false;
      }
   }
    
/****************************************************************/
}