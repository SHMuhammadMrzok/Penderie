<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Gateways_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    
    /***************Insert***********************/  
    public function insert_settings($data)
    {
       return $this->db->insert('settings',$data);
    }
    
    public function insert_settings_translation($settings_translation_data)
    {
        return $this->db->insert('settings_translation',$settings_translation_data);
    }
   
/********************Get********************/
    
    public function get_count_all_settings($lang_id, $search_word='')
    {
        if(trim($search_word) != '')
        {
            $this->db->join('gateways_settings_translation', 'gateways_settings.field_id = gateways_settings_translation.field_id AND gateways_settings_translation.lang_id = '.$lang_id);
            
            $this->db->like('gateways_settings_translation.name', $search_word, 'both');  
            $this->db->or_like('gateways_settings.value', $search_word, 'both');
        }
        
        return $this->db->count_all_results('gateways_settings');
    }
   
    public function get_settings_data($lang_id, $limit, $offset, $search_word='', $order_by='', $order_state)
    {
        $this->db->select('gateways_settings.*, gateways_settings_translation.name as field');
        $this->db->join('gateways_settings_translation', 'gateways_settings.field_id = gateways_settings_translation.field_id AND gateways_settings_translation.lang_id = '.$lang_id);
        
        if(trim($search_word) !='')
        {
            $this->db->like('gateways_settings_translation.name', $search_word, 'both');  
            $this->db->or_like('gateways_settings.value', $search_word, 'both');
        }
        
        if($order_by != '')
        {
            if($order_by == lang('field'))
            { 
                $this->db->order_by('gateways_settings_translation.name', $order_state);
            }
            else
            {
                $this->db->order_by('gateways_settings.id', $order_state);
            }
        }
        else
        {
            $this->db->order_by('gateways_settings.id', $order_state);
        }
        
        $result = $this->db->get('gateways_settings', $limit, $offset);

        if($result)
        {
            return $result->result();    
        }
        else
        {
            return false;
        }
    } 
    
    public function get_settings_row($id, $lang_id)
    {
        $this->db->select('gateways_settings.*, gateways_settings_translation.name as field');
        $this->db->join('gateways_settings_translation', 'gateways_settings.field_id = gateways_settings_translation.field_id AND gateways_settings_translation.lang_id = '.$lang_id);
        
        $this->db->where('id', $id);
        
        $row = $this->db->get('gateways_settings');
         
        if($row)
        {
           return $row->row();
        }
        else
        {
           return false;
        }
    }
   
    public function get_row_data($id, $lang_id)
    {
        $this->db->select('gateways_settings.*, gateways_settings_translation.name as field');
        
        $this->db->join('gateways_settings_translation', 'gateways_settings.field_id = gateways_settings_translation.field_id 
                         AND gateways_settings_translation.lang_id = '.$lang_id);
        
        $this->db->where('gateways_settings.id', $id);
        
        $result = $this->db->get('gateways_settings');

        if($result)
        {
            return $result->row();    
        }
        else
        {
            return false;
        }
    }
    
    public function get_field_value($field='', $id=0)
    {
        $this->db->select('gateways_settings.*');
        $this->db->join('gateways_fields', 'gateways_settings.field_id = gateways_fields.id');
        
        if($id != 0)
        {
            $this->db->where('gateways_fields.id', $id);
        }
        else
        {
            $this->db->where('gateways_fields.field', $field);
        }
        
        $query = $this->db->get('gateways_settings');
        
        if($query->row())
        {
            return $query->row()->value;
        }
        else
        {
            return false;
        }
    }
    
    /*****************Update ************************/
  
    public function update_settings($setting_id,$settings_data)
    {
        $this->db->where('id', $setting_id);
        return $this->db->update('gateways_settings', $settings_data);
    }
    
    /***************************Delete *********************************/
    
    public function delete_settings_data($setting_id_array)
    {
        $this->db->where_in('id',$setting_id_array);
        $this->db->delete('settings');
        
        $this->db->where_in('setting_id',$setting_id_array);
        $this->db->delete('settings_translation');
        echo '1';    
        
    } 
    
   
   
    /****************************************************/
}