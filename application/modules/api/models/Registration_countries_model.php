<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
    
class Registration_countries_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    
   /**********************GET*******************************/
    
    public function get_countries($lang_id)
    {
        $this->db->select('user_nationality_translation.name,user_nationality.*');
        
        $this->db->join('user_nationality_translation','user_nationality.id = user_nationality_translation.user_nationality_id');
        
        $this->db->where('user_nationality_translation.lang_id',$lang_id);
        $this->db->order_by('sort', 'asc');
        
        $query = $this->db->get('user_nationality');
        
        if($query)
        {
            return $query->result();
        }   
    }
   
   public function get_cities($lang_id ,$country_id)
   {
        $this->db->select('cities_translation.name,cities.id');
        
        $this->db->join('cities_translation','cities.id = cities_translation.city_id');
        
        $this->db->where('cities_translation.lang_id',$lang_id);
        $this->db->where('cities.user_nationality_id',$country_id);
        
        $query = $this->db->get('cities');
        
        if($query)
        {
            return $query->result();
        }
        else
        {
            return false;
        }
   }
    
    
/****************************************************************/
}