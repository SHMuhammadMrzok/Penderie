<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Follow_orders_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    
   /**********************GET*******************************/
    
    public function get_order_status($lang_id)
    {
        $this->db->select('order_status_translation.*,orders_status.*');
        $this->db->join('order_status_translation','orders_status.id = order_status_translation.status_id');
        $this->db->where('order_status_translation.lang_id',$lang_id);
        $query = $this->db->get('orders_status');
        
        if($query)
        {
            return $query->result();
        }   
    } 
    
    public function get_user_order_data($user_id, $display_lang_id, $limit, $offset, $order_number='', $status='')
    {
        $this->db->select('orders.*, orders_status.*, order_status_translation.name, orders.id as id, orders_status.status_image as status_image, 
                            orders_status.id as status_id, order_status_translation.name as status, stores_translation.name as store_name');
        
        $this->db->join('orders_status', 'orders.order_status_id = orders_status.id');
        $this->db->join('order_status_translation', 'orders.order_status_id = order_status_translation.status_id');
        $this->db->join('stores_translation', 'orders.store_id = stores_translation.store_id AND stores_translation.lang_id='.$display_lang_id);
        
        $this->db->where('orders.user_id', $user_id);
        $this->db->where('order_status_translation.lang_id', $display_lang_id);
        
        $this->db->order_by('orders.id', 'desc');
        
        if($status != '')
        {
            $this->db->where('orders.order_status_id', $status);
        }
        
        if($order_number != '')
        {
            $this->db->like('orders.id', $order_number, 'both');
        }        
        
        $result = $this->db->get('orders', $limit, $offset);
        
        if($result)
        {
            return $result->result();
        } 
        else
        {
            return false;
        }
        
    }
    
    public function get_all_orders_count($user_id, $order_number='', $status='')
    {
        $this->db->select('orders.*, order_status_translation.name, orders.id as id, order_status_translation.name as status');
        
        $this->db->join('order_status_translation', 'orders.order_status_id = order_status_translation.status_id');
        
        $this->db->where('orders.user_id', $user_id);
        
        if($order_number != '')
        {
            $this->db->like('orders.id', $order_number, 'both');
        }
        
        if($status != '')
        {
            $this->db->like('order_status_translation.name', $status, 'both');
        }
        
        return $this->db->count_all_results('orders');
    }
    
    public function get_order_details($userId ,$order_id, $display_lang_id)
    {
        $this->db->select('orders.*,  users.username,  orders_status.status, orders.id as id, payment_methods_translation.name as payment_method');
        
       // $this->db->join('orders_log', 'orders.id = orders_log.order_id');orders_log.*,
        $this->db->join('orders_status', 'orders.order_status_id = orders_status.id');
        $this->db->join('users', 'orders.user_id = users.id');        
        $this->db->join('payment_methods_translation', 'orders.payment_method_id = payment_methods_translation.payment_method_id');
        
        $this->db->where('orders.id', $order_id);
        $this->db->where('users.id', $userId);
        $this->db->where('payment_methods_translation.lang_id', $display_lang_id);
        
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
   
   public function get_order_log($order_id)
   {
        $this->db->select('orders_log.*,  orders_status.status');
        $this->db->join('orders_status', 'orders_log.status_id = orders_status.id');
        
        $this->db->where('orders_log.order_id', $order_id);
        $query = $this->db->get('orders_log');

        if($query)
        {
            return $query->result();
        }
        else
        {
            return false;
        }
   }
   
   public function get_order_products($order_id, $display_lang_id)
    {
        $this->db->select('orders_products.*, products.image, products_translation.title ');
        
        $this->db->join('products', 'orders_products.product_id = products.id');
        $this->db->join('products_translation', 'orders_products.product_id = products_translation.product_id');
        
        $this->db->where('orders_products.order_id', $order_id);
        $this->db->where('products_translation.lang_id', $display_lang_id);
       
       
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
    
    public function get_order_cards($order_id)
    {
        $this->db->where('order_id', $order_id);
        $this->db->where('product_id', 0);
        
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
    
   public function get_order_payment_log( $orderNumber)
   {
        //$this->db->where('ORDERID', $orderNumber);
        $this->db->where('order_id', $orderNumber);
        
        $result = $this->db->get('payment_log');
        
        if($result)
        {
            return $result->row();
        }
        else
        {
            return false;
        }
   }   
/****************************************************************/
}