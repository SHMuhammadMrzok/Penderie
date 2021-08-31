<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Payment_log_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    
    /**********************GET*******************************/
    
    /*public function get_count_all_logs($lang_id, $search_word='', $users_filter=0, $status_filter=0)
    {
        $this->db->join('users', 'payment_log.user_id = users.id', 'left');
        $this->db->join('payment_status_translation', 'payment_log.status_id = payment_status_translation.status_id');
        
        if(trim($search_word) != '')
        {
            $this->db->where('(payment_log.transaction_id LIKE "%'.$search_word.'%" OR payment_log.order_id LIKE "%'.$search_word.'%" OR users.first_name LIKE "%'.$search_word.'%" OR payment_status_translation.status LIKE "%'.$search_word.'%" )');
        }
        
        if($users_filter != 0)
        {
            $this->db->where('users.id', $users_filter);
        }
        
        if($status_filter != 0)
        {
            $this->db->where('payment_log.status_id', $status_filter);
        }
        
        return $this->db->count_all_results('payment_log');
    }*/
    
    public function get_count_all_logs($lang_id, $search_word='', $users_filter=0, $status_filter=0, $date_from=0, $date_to=0, $stores_ids=array())
    {
        $this->db->join('users', 'payment_log.user_id = users.id', 'left');
        $this->db->join('payment_status_translation', 'payment_log.status_id = payment_status_translation.status_id', 'left');
        
        $this->db->where('payment_status_translation.lang_id', $lang_id);
        
        if(trim($search_word) != '')
        {
            $this->db->where('(payment_log.transaction_id LIKE "%'.$search_word.'%" OR payment_log.order_id LIKE "%'.$search_word.'%" OR users.first_name LIKE "%'.$search_word.'%" OR payment_status_translation.status LIKE "%'.$search_word.'%" )');
        }
        
        if($users_filter != 0)
        {
            $this->db->where('users.id', $users_filter);
        }
        
        if($status_filter != 0)
        {
            $this->db->where('payment_log.status_id', $status_filter);
        }
        
        if(count($stores_ids) != 0)
        {
            //$this->db->join('orders', 'payment_log.order_id = orders.id');
            $this->db->where_in('payment_log.store_id', $stores_ids);
        }
        
        return $this->db->count_all_results('payment_log');
    }
    
    public function get_payment_log_data($lang_id, $limit, $offset, $search_word, $order_by, $order_state, $users_filter, $status_filter, $date_from, $date_to, $stores_ids=array())
    {
        $this->db->select('payment_log.*,  users.*, payment_status_translation.status, payment_log.id as id, payment_status_translation.status as status');
        
        $this->db->join('users', 'payment_log.user_id = users.id', 'left');
        $this->db->join('payment_status_translation', 'payment_log.status_id = payment_status_translation.status_id 
                        AND payment_status_translation.lang_id='.$lang_id, 'left');
        
        //$this->db->where('payment_status_translation.lang_id', $lang_id);
        
        if(trim($search_word) != '')
        {
            $this->db->where('(payment_log.transaction_id LIKE "%'.$search_word.'%" OR payment_log.order_id LIKE "%'.$search_word.'%" OR users.first_name LIKE "%'.$search_word.'%" OR payment_status_translation.status LIKE "%'.$search_word.'%" )');
        }
        
        if($order_by != '')
        {
            if($order_by == lang('date'))
            {
                $this->db->order_by('payment_log.unix_time', $order_state);
            }
            else if($order_by == lang('order_id'))
            {
                $this->db->order_by('payment_log.order_id', $order_state);
            }
            else
            {
                $this->db->order_by('payment_log.id', $order_state);
            }
        }
        else
        {
            $this->db->order_by('payment_log.id', $order_state);
        }
        
        if($users_filter != 0)
        {
            $this->db->where('users.id', $users_filter);
        }
        
        if($status_filter != 0)
        {
            $this->db->where('payment_log.status_id', $status_filter);
        }
        
        if($date_from != 0)
        {
            $this->db->where('payment_log.unix_time >=', $date_from);
        }
       
        if($date_to != 0)
        {
            $this->db->where('payment_log.unix_time <=', $date_to);
        }
        
        if(count($stores_ids) != 0)
        {
            //$this->db->join('orders', 'payment_log.order_id = orders.id');
            //$this->db->where_in('payment_log.store_id', $stores_ids);
        }
        
        $result = $this->db->get('payment_log', $limit, $offset);
        
        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }
    
    public function get_row_data($id, $display_lang_id)
    {
        $this->db->select('payment_log.*, users.*, payment_methods_translation.name, payment_log.id as id, payment_methods_translation.name as payment_method');
        
        $this->db->join('users', 'payment_log.user_id = users.id', 'left');
        $this->db->join('payment_methods_translation', 'payment_log.payment_method_id = payment_methods_translation.payment_method_id');
        
        $this->db->where('payment_methods_translation.lang_id', $display_lang_id);
        $this->db->where('payment_log.id', $id);
        
        $row = $this->db->get('payment_log');
        
        if($row)
        {
            return $row->row();
        }
        else
        {
            return false;
        }
    }
    
    public function get_paypal_data()
    {
        $this->db->where('id', 5);
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
    
    public function get_payment_log_status($lang_id)
    {
        $this->db->select('payment_status_translation.*, payment_status_translation.status_id as id, payment_status_translation.status as name');
        $this->db->where('lang_id', $lang_id);
        $result = $this->db->get('payment_status_translation');
        
        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }
    
    public function get_payment_status_id($status)
    {
        $this->db->where('status', $status);
        $row = $this->db->get('payment_status')->row();
        
        if($row)
        {
            return $row->id;
        }
        else
        {
            return false;
        }
    }
    
    public function insert_payment_log($data)
    {
        return $this->db->insert('payment_log', $data);
    }
    
/****************************************************************/
}