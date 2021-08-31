<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Contact_us_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    /**********Insert *************/
   
   public function save_msg($data)
   {
       return $this->db->insert('contact_us',$data);
   }
    /**********************GET*******************************/
   
    
    public function get_messages()
    {
        
        $query = $this->db->get('contact_us');
        
        if($query)
        {
            return $query->result();
        }
    }
    
    public function get_lastInsertId()
    {
        
        $query = $this->db->insert_id('contact_us');
        
        return  $query;

    }
   
/****************************************************************/
}