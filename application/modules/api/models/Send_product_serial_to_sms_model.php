<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Send_product_serial_to_sms_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    
   /**********************GET*******************************/
    public function get_serial_data($serial_id, $lang_id)
    {
        $this->db->select('orders_serials.*, products_serials.*, products.image, products_translation.title');
        
        $this->db->join('products_serials', 'orders_serials.product_serial_id = products_serials.id');
        $this->db->join('products', 'orders_serials.product_id = products.id');
        $this->db->join('products_translation', 'orders_serials.product_id = products_translation.product_id');
        
        $this->db->where('orders_serials.product_serial_id', $serial_id);
        $this->db->where('products_translation.lang_id', $lang_id);
        
        $query = $this->db->get('orders_serials');
        
        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }
    
    
  public function get_product_details($lang_id , $productId)//,$country_id
  {
        $this->db->select('products.*, products_translation.* ');//products_countries.* , 
        
        $this->db->join('products_translation', 'products.id = products_translation.product_id');
        //$this->db->join('products_countries', 'products.id = products_countries.product_id');
        
       
        //$this->db->where('products_countries.country_id',$country_id);
        $this->db->where('products_translation.lang_id', $lang_id);
        $this->db->where('products.id', $productId);
       
        $query = $this->db->get('products');
        
        if($query)
        {
            return $query->row();
        }else{
            
            return false ;
        }
  }
  
  
   public function update_order_serial($serial_id, $updated_data)
    {
        $this->db->where('product_serial_id', $serial_id);
        
        return $this->db->update('orders_serials', $updated_data);
    }
    
    
   public function insert_sms_log_data($data)
    {
        return $this->db->insert('sent_serials_via_sms', $data);
    }  
    
    
   public function get_product_serials($product_id, $order_id)
    {
        $this->db->select('orders_serials.*, products_serials.*');
        $this->db->join('products_serials', 'orders_serials.product_serial_id = products_serials.id');
        
        $this->db->where('orders_serials.product_id', $product_id);
        $this->db->where('orders_serials.order_id', $order_id);
        
        $result = $this->db->get('orders_serials');
        
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