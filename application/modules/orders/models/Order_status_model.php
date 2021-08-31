<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Order_status_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    /**********************INSERT*******************************/
    public function insert_order_status($data)
    {
        return $this->db->insert('orders_status', $data);
    }
    
    public function insert_status_translation($status_translation_data)
    {
        return $this->db->insert('order_status_translation', $status_translation_data);
    }
   
    /**********************GET*******************************/
    public function get_count_all_status($lang_id)
    {
        $this->db->join('order_status_translation', 'orders_status.id = order_status_translation.status_id');
        $this->db->where('order_status_translation.lang_id', $lang_id);
        return $this->db->count_all_results('orders_status');
    }
    
    public function get_order_status_data($lang_id,$limit,$offset,$search_word='',$order_by='',$order_state)
    {
        $this->db->select('orders_status.*, order_status_translation.*');
        $this->db->join('order_status_translation', 'orders_status.id = order_status_translation.status_id');
        
        if(trim($search_word) !='')
        {
            $this->db->like('order_status_translation.title', $search_word, 'both'); 
        }
        if($order_by != '')
        {
            if($order_by == lang('date'))
            {
                $this->db->order_by('orders_status.id',$order_state);
            }
            elseif($order_by == lang('status'))
            {
                $this->db->order_by('orders_status.status',$order_state);
            }
            elseif($order_by == lang('name'))
            {
                $this->db->order_by('order_status_translation.name',$order_state);
            }
            else
            {
                $this->db->order_by('orders_status.id',$order_state);
            }
        }
        else
        {
            $this->db->order_by('orders_status.id',$order_state);
        }
        
        $this->db->where('order_status_translation.lang_id', $lang_id);
        
        $result = $this->db->get('orders_status', $limit, $offset);
        
        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }
    
    
    public function get_status_id($method_id, $table)
    {
        $this->db->where('id', $method_id);
        $query = $this->db->get($table)->row();
        
        if($query)
        {
            return $query->order_status_id;
        }
        else
        {
            return false;
        }
    }
    
    public function get_payment_data($method_id)
    {
        $this->db->where('id', $method_id);
        $query = $this->db->get('payment_methods');
        
        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }
    
    
    
    
    public function get_all_statuses($display_lang_id)
    {
        $this->db->select('orders_status.*, order_status_translation.*');
        $this->db->join('order_status_translation', 'orders_status.id = order_status_translation.status_id');
        
        $this->db->where('order_status_translation.lang_id', $display_lang_id);
        
        $result = $this->db->get('orders_status');
        
        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }
    
    public function get_status_row($id)
    {
        $this->db->where('id', $id);
        $row = $this->db->get('orders_status');
        
        if($row)
        {
            return $row->row();
        }
        else
        {
            return false;
        }
    }
    
    public function get_status_translation($status_id)
    {
        $this->db->where('status_id', $status_id);
        
        $result = $this->db->get('order_status_translation');
        
        if($result)
        {
            return $result->result();
        }
    }
    
    public function get_row_data($id,$display_lang_id)
    {
        $this->db->select('orders_status.*, order_status_translation.*');
        $this->db->join('order_status_translation', 'orders_status.id = order_status_translation.status_id');
        
        $this->db->where('orders_status.id', $id);
        $this->db->where('order_status_translation.lang_id', $display_lang_id);
        
        $row = $this->db->get('orders_status');
        
        if($row)
        {
            return $row->row();
        }
        else
        {
            return false;
        }
    }
    
    public function get_status_translation_name($status_id, $lang_id)
    {
        $this->db->where('lang_id', $lang_id);
        $this->db->where('status_id', $status_id);
        $row = $this->db->get('order_status_translation');
        
        if($row->row())
        {
            return $row->row()->name;
        }
        else
        {
            return false;
        }
    }
   
    /**********************Update*******************************/
    
    public function update_status($status_id, $updated_data)
    {
        $this->db->where('id', $status_id);
        return $this->db->update('orders_status', $updated_data);
    }
    
    public function update_status_translation($status_id, $lang_id, $status_translation_data)
    {
        $this->db->where('status_id', $status_id);
        $this->db->where('lang_id', $lang_id);
        
        return $this->db->update('order_status_translation', $status_translation_data);
    }
   
    /**********************DELETE*******************************/ 
    public function delete_status_data($ids_array)
    {
        $this->db->where_in('id',$ids_array);
        $this->db->delete('orders_status');
        
        $this->db->where_in('status_id',$ids_array);
        $this->db->delete('order_status_translation');
        
        echo '1';
    }
/****************************************************************/
}