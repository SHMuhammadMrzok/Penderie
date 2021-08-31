<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Faq_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
   
    /**********************GET*******************************/
   
    
    public function get_faq($lang_id)
    {
        $this->db->select('faq_translation.*,faq.*');
        
        $this->db->join('faq_translation','faq.id = faq_translation.faq_id');
        
        $this->db->where('faq.active',1);
        $this->db->where('faq_translation.lang_id',$lang_id);
        
        $query = $this->db->get('faq');
        
        if($query)
        {
            return $query->result();
        }
    }
   
/****************************************************************/
}