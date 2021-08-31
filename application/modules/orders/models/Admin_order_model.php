<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Admin_order_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    public function check_product_in_order($product_id, $order_id)
    {
        $this->db->where('product_id', $product_id);
        $this->db->where('order_id', $order_id);
        
        $count = $this->db->count_all_results('orders_products');
        
        if($count>0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    public function get_order_product_serials($product_id, $order_id)
    {
        $this->db->where('product_id', $product_id);
        $this->db->where('order_id', $order_id);
        
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
    
    public function update_serial_data($serial_id, $serial_data)
    { 
        $this->db->where('id', $serial_id);
        
        return $this->db->update('products_serials', $serial_data);
    }
    
    public function delete_order_product_serials($serials_ids)
    {
        $this->db->where_in('product_serial_id', $serials_ids);
        $this->db->delete('orders_serials');
    }
    
    public function delete_order_product($product_id, $order_id)
    {
        $this->db->where('product_id', $product_id);
        $this->db->where('order_id', $order_id);
        
        $this->db->delete('orders_products');
    }
    
    public function get_order_all_products($order_id)
    {
        $this->db->where('order_id', $order_id);
        
        $result = $this->db->get('orders_products');
        
        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }
    
    public function update_order($order_id, $order_data)
    {
        $this->db->where('id', $order_id);
        return $this->db->update('orders', $order_data);
    }
    
    public function get_order_data($order_id)
    {
        $this->db->where('id', $order_id);
        $row = $this->db->get('orders');
        
        if($row)
        {
            return $row->row();
        }
        else
        {
            return false;
        }
    }
    
    public function update_order_products($order_id, $product_id, $product_data)
    {
        $this->db->where('product_id', $product_id);
        $this->db->where('order_id', $order_id);
        
        return $this->db->update('orders_products', $product_data);
    }
    
    public function get_order_product_data($order_id, $product_id)
    {
        $this->db->where('product_id', $product_id);
        $this->db->where('order_id', $order_id);
        
        $query = $this->db->get('orders_products');
        
        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }
    
    public function get_product_serials($qty, $product_id, $order_id)
    {
        $this->db->where('product_id', $product_id);
        $this->db->where('order_id', $order_id);
        
        $result = $this->db->get('orders_serials', $qty);
        
        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
        
    }
    
    public function delete_order_serials($order_id, $product_id, $product_serial_id)
    {
        $this->db->where('order_id', $order_id);
        $this->db->where('product_id', $product_id);
        $this->db->where('product_serial_id', $product_serial_id);
        
        $this->db->delete('orders_serials');
        
    }
    
    public function get_payment_methods_translation($lang_id)
    {
        $this->db->select('payment_methods_translation.*, payment_method_id as id');
        $this->db->where('lang_id', $lang_id);
        
        $result = $this->db->get('payment_methods_translation');
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