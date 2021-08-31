<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Invalid_serials_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    /********Delet*************/
    
    /********insert*************/
    
     /********get*************/
     public function get_count_all_invalid_serials($lang_id, $search_word='')
     {
        $this->db->select('products_serials.*, products_translation.title, products_serials.unix_time as unix_time, products_translation.title as product_name,invalid_status_translation.status as invalid_status, products_serials.id as product_serial_id');
        
        $this->db->join('products_translation', 'products_serials.product_id = products_translation.product_id');
        $this->db->join('invalid_status_translation', 'products_serials.invalid_status_id = invalid_status_translation.status_id AND invalid_status_translation.lang_id = '.$lang_id, 'left');
        $this->db->join('orders_serials', 'products_serials.id = orders_serials.product_serial_id');
        $this->db->join('orders', 'orders_serials.order_id = orders.id');
        
        if($search_word != '')
        {
            $this->db->where('products_serials.serial', $search_word);
        }
        
        $this->db->where('products_serials.invalid', 1);
        $this->db->where('products_translation.lang_id', $lang_id);
        
        return $this->db->count_all_results('products_serials');
     }
     
     public function get_invalid_serials_data($lang_id, $limit, $offset, $search_word, $stores_ids)
     {
        $this->db->select('products_serials.*, products_translation.title, products_serials.unix_time as unix_time,
                            products_translation.title as product_name,invalid_status_translation.status as invalid_status, 
                            products_serials.id as product_serial_id, orders.id as order_id, orders.unix_time as order_date');
        
        $this->db->join('products_translation', 'products_serials.product_id = products_translation.product_id');
        $this->db->join('invalid_status_translation', 'products_serials.invalid_status_id = invalid_status_translation.status_id AND invalid_status_translation.lang_id = '.$lang_id, 'left');
        $this->db->join('orders', 'products_serials.sold_order_id = orders.id', 'left');
        
        if($search_word != '')
        {
            $this->db->where('products_serials.serial', $search_word);
        }
        
        $this->db->where('products_serials.invalid', 1);
        $this->db->where('products_translation.lang_id', $lang_id);
        
        if(count($stores_ids) != 0)
        {
            $this->db->where_in('orders.store_id', $stores_ids);
        }
        
        $result = $this->db->get('products_serials', $limit, $offset);
        
        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
     }
     
     public function get_invalid_serial_order_data($product_serial_id)
     {
        $this->db->select('orders.id as order_id, orders.unix_time as order_date');
        
        $this->db->join('orders_serials', 'orders_serials.order_id = orders.id');
        $this->db->join('products_serials', 'orders_serials.product_serial_id = products_serials.id');
        
        $this->db->where('products_serials.id', $product_serial_id);
        
        $this->db->order_by('orders.id', 'desc');
        
        $query = $this->db->get('orders');
        
        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
     }
     
     public function get_row_data($id, $display_lang_id)
     {
        $this->db->select('orders_serials.*, products_serials.*, orders.*, products_translation.title, 
                            orders.id as order_id, products_serials.unix_time as unix_time, orders.unix_time as order_date, 
                            products_translation.title as product_name, invalid_status_translation.status as status');
        
        $this->db->join('products_serials', 'orders_serials.product_serial_id = products_serials.id');
        $this->db->join('orders', 'orders_serials.order_id = orders.id');
        $this->db->join('products_translation', 'orders_serials.product_id = products_translation.product_id');
        $this->db->join('invalid_status_translation', 'products_serials.invalid_status_id = invalid_status_translation.status_id AND invalid_status_translation.lang_id ='.$display_lang_id, 'left');
        
        $this->db->where('products_serials.id', $id);
        $this->db->where('products_translation.lang_id', $display_lang_id);
        
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
     
     public function get_invalid_status($lang_id)
     {
        $this->db->where('lang_id', $lang_id);
        
        $result = $this->db->get('invalid_status_translation');
        
        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
     }
     
     public function get_replaced_serial_data($ref_serial_id)
     {
        $this->db->where('invalid_serial_ref_id', $ref_serial_id);
        
        $query = $this->db->get('products_serials');
        
        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
     }
    
    /********update*************/
    
    
    
/****************************************************************/
}