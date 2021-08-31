<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Payment_methods_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    
   /**********************GET*******************************/
    
    public function get_user_payment_methods($lang_id,$customer_group_id)
    {
        $this->db->select('customer_groups_payment_methods.*,payment_methods_translation.*,payment_methods.*');
        
        $this->db->join('customer_groups_payment_methods','payment_methods.id = customer_groups_payment_methods.payment_method_id');
        $this->db->join('payment_methods_translation','payment_methods.id = payment_methods_translation.payment_method_id');
        
        $this->db->where('customer_groups_payment_methods.customer_group_id',$customer_group_id);
        $this->db->where('payment_methods_translation.lang_id',$lang_id);
        
        $query = $this->db->get('payment_methods');
        
        if($query)
        {
            return $query->result();
        }   
    }
        
    public function get_payment_methods($lang_id)
    {
        $this->db->select('payment_methods_translation.*,payment_methods.*');
        
        $this->db->join('payment_methods_translation','payment_methods.id = payment_methods_translation.payment_method_id');
        
        $this->db->where('payment_methods_translation.lang_id',$lang_id);
        
        $query = $this->db->get('payment_methods');
        
        if($query)
        {
            return $query->result();
        }   
    }
   
  
    
    
/****************************************************************/
}