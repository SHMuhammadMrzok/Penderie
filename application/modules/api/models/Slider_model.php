<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Slider_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    
   /**********************GET*******************************/
    
    public function get_slider_images()
    {
        
        $this->db->where('location', 'top');
        $this->db->where('active', '1');
        
        $result = $this->db->get('advertisements');

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