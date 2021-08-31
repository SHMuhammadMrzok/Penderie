<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class General_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    
   /**********************GET*******************************/
    
    public function get_settings()
    {
        $query = $this->db->get('settings');
        
        if($query)
        {
            return $query->row();
        }   
    }
   
    public function get_site_settings($lang_id)
    {
        $this->db->select('settings_translation.*, settings.*, settings.id as id');
        
        $this->db->join('settings_translation', 'settings.id = settings_translation.setting_id');
        
        $this->db->where('settings_translation.lang_id',$lang_id);
        
        $result = $this->db->get('settings');

        if($result)
        {
            return $result->row();    
        }
        else
        {
            return false;
        }
    }
    
    public function get_lang_var_translation($var, $display_lang_id)
    {
        $this->db->select('lang_vars.*, lang_translation.*');
        
        $this->db->join('lang_translation', 'lang_vars.id = lang_translation.var_id');
        
        $this->db->where('lang_vars.lang_var', $var);        
        $this->db->where('lang_translation.lang_id', $display_lang_id);
        
        
        $result = $this->db->get('lang_vars');
        
        if($result->row())
        {
            return $result->row()->lang_definition;
        }
        else
        {
            return false;
        }
    }
    
    public function get_nationality_transaltion($user_nationality_id, $lang_id)
    {
        $this->db->where('user_nationality_id', $user_nationality_id);
        $this->db->where('lang_id', $lang_id);
        
        $query = $this->db->get('user_nationality_translation')->row();
        
        if($query)
        {
            return $query->name;
        }
        else
        {
            return false;
        }
    }
    
    public function get_user_with_translation_data($user_id, $lang_id)
    {
        $this->db->select('users.*, customer_groups_translation.title, user_nationality_translation.name, cities_translation.name, customer_groups_translation.title as group_name,  user_nationality_translation.name as country_name, cities_translation.name as city_name');
        
        $this->db->join('customer_groups_translation', 'users.customer_group_id = customer_groups_translation.customer_group_id', 'left');
        $this->db->join('user_nationality_translation', 'users.Country_ID = user_nationality_translation.user_nationality_id', 'left');
        $this->db->join('cities_translation', 'users.city_id = cities_translation.city_id', 'left');
        
        $this->db->where('customer_groups_translation.lang_id', $lang_id);
        $this->db->where('user_nationality_translation.lang_id', $lang_id);
        $this->db->where('cities_translation.lang_id', $lang_id);
        $this->db->where('users.id', $user_id);
        
        $query = $this->db->get('users');
        
        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }
    
    public function get_user_row($user_id)
    {
        $this->db->where('id', $user_id);
        $query = $this->db->get('users');
        
        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }
    
    public function get_nationality_data($country_id, $lang_id)
    {
        $this->db->select('user_nationality.*, user_nationality_translation.*');
        
        $this->db->join('user_nationality_translation', 'user_nationality.id = user_nationality_translation.user_nationality_id');
        
        $this->db->where('user_nationality.id', $country_id);
        $this->db->where('user_nationality_translation.lang_id', $lang_id);
        
        $query = $this->db->get('user_nationality');
        
        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }
    
    public function get_languages()
    {
        $this->db->where('active',1);
        
        $result = $this->db->get('languages');
        
        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }
    
    public function get_language_vars($lang_id)
    {
        $this->db->select('lang_vars.*, lang_translation.*');
        $this->db->join('lang_translation', 'lang_vars.id = lang_translation.var_id');
        
        $this->db->where('lang_vars.mobile_app', 1);
        $this->db->where('lang_translation.lang_id', $lang_id);
        
        $result = $this->db->get('lang_vars');
        
        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }
    
    public function insert_table_data($table_name, $data)
    {
        return $this->db->insert($table_name, $data);
    }
  
    
    
/****************************************************************/
}