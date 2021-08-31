<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Dopay_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    
   /**********************GET*******************************/
    
    
     public function get_cart_row_data($cart_id)
    {
        $this->db->where('id', $cart_id);
        $query = $this->db->get('shopping_cart');
        
        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }
/****************************************************************/
}