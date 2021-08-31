<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Countries_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    
   /**********************GET*******************************/
    
    public function get_countries($lang_id)
    {
        $this->db->select('countries_translation.*,countries.*');
        $this->db->order_by('countries_translation.name', 'asc');
        
        $this->db->join('countries_translation','countries.id = countries_translation.country_id');
        $this->db->where('countries_translation.lang_id',$lang_id);
        $query = $this->db->get('countries');
        
        if($query)
        {
            return $query->result();
        }   
    }  
    
    
/****************************************************************/
}