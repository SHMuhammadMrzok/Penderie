<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Affiliate_log_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
   
   /**************Update****************/// 
   public function update_affilliate_pay_status($id, $data)
   {
        $this->db->where('id',$id);
        $this->db->update('affiliate_log' ,$data );
   }
   /*************************DELETE*******************************/
   
    public function delete_affiliate_log_data($affiliate_log_id_array)
    {
        $this->db->where_in('id',$affiliate_log_id_array);
        $this->db->delete('affiliate_log');
        
        echo '1';  
    }
    /****************Get****************/
  
    public function get_count_all_affiliate_log($search_word='', $users_filter_id =0 ,$buyers_filter_id = 0)
    {
        $search   = '';
        $ufilter  = '';
        $bfilter  = '';
        
        if(trim($search_word) !='')
        {
           $search = ' AND `users1`.`first_name`  like "%' . $search_word . '%"';
        }
        
        if($users_filter_id !=0)
        {
           $ufilter = ' AND `affiliate_log`.`user_id`  = ' . $users_filter_id . '';
        }
        
        if($buyers_filter_id !=0)
        {
            $bfilter = ' AND `affiliate_log`.`buyer_id`  = ' . $buyers_filter_id . '';
            
        }
        
        $query = $this->db->query('SELECT COUNT(*) AS `numrows` FROM (`affiliate_log`) JOIN `users` as `users1` ON `affiliate_log`.`user_id` = `users1`.`id` JOIN `users` as `users2` ON `affiliate_log`.`buyer_id` = `users2`.`id` WHERE 1 '.$search.$ufilter.$bfilter);
        
        $row = $query->row();
        return $row->numrows;
    }
    
    public function get_affiliate_log_data($limit, $offset, $search_word='', $order_by='', $order_state, $users_filter_id =0, $buyers_filter_id = 0)
    {
        $this->db->select('affiliate_log.*, orders.*, affiliate_log.id as id, orders.id as order_id');
        $this->db->join('orders', 'affiliate_log.order_id = orders.id');
        
        if(trim($search_word) !='')
        {
            $this->db->like('orders.total', $search_word, 'both');
        }
        
        if($order_by != '')
        {
            if($order_by == lang('username'))
            {
                //$this->db->order_by('users.username',$order_state);
            }
            else
            {
                $this->db->order_by('affiliate_log.id',$order_state);
            }
        }
        else
        {
            
           $this->db->order_by('affiliate_log.id',$order_state);
        }
        
        if($users_filter_id !=0)
        {
            $this->db->where('affiliate_log.user_id', $users_filter_id);
        }
        
        if($buyers_filter_id !=0)
        {
            $this->db->where('affiliate_log.buyer_id', $buyers_filter_id);
        }
        
        $result = $this->db->get('affiliate_log', $limit, $offset);
        if($result)
        {
            return $result->result();    
        }
        else
        {
            return false;
        }
    }
    
    public function get_row_data($id)
    {
        $n=$this->db->query('SELECT `affiliate_log`.* ,`users1`.`username` as `user_username`,`users2`.`username` as `buyer_username` from `affiliate_log` JOIN `users` as `users1` ON `affiliate_log`.`user_id` = `users1`.`id` JOIN `users` as `users2` ON `affiliate_log`.`buyer_id` = `users2`.`id` WHERE `affiliate_log`.`id` = '.$id);
        
        return  $n->row();
        /*$this->db->select('users.username ,users.email, affiliate_log.*, affiliate_log.id as id');
        
        $this->db->join('users', 'affiliate_log.user_id = users.id');
        
        $this->db->where('affiliate_log.id',$id);
        
        $result = $this->db->get('affiliate_log');

        if($result)
        {
            return $result->row();    
        }*/
    }
    
   public function get_users_filter_data()
   {        
        $sql = "SELECT CONCAT(first_name, ' ', last_name) AS name, id FROM users";
        
        $result = $this->db->query($sql);
        
        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
        
   }
   
   public function get_affilliate_row_pay($id)
   {
        $this->db->where('id',$id);
        
        $result = $this->db->get('affiliate_log');

        if($result)
        {
            return $result->row()->pay;    
        }
   }
   
   public function get_user_affiliate($user_id)
   {
        $this->db->where('user_id',$user_id);
       
        $result = $this->db->get('affiliate');

        if($result)
        {
            return $result->row();    
        }
   }
  
   public function get_all_affiliate_log_count($user_id)
   {
        $this->db->where('buyer_id',$user_id);
        return $this->db->count_all_results('affiliate_log');
   } 
   
   public function get_user_affiliate_log($user_id, $perPage, $offset)
   {
        $this->db->select('users.* , affiliate_log.*, orders.*, orders.id as order_id');
        
        $this->db->join('users', 'affiliate_log.user_id = users.id');
        $this->db->join('orders', 'affiliate_log.order_id = orders.id');
        
        $this->db->where('affiliate_log.buyer_id',$user_id);
        
        $result = $this->db->get('affiliate_log',$perPage,$offset);

        if($result)
        {
            return $result->result();    
        }
     //order->final_total;
   }
   
   public function insert_affiliate_log_data($log_data)
   {
        return $this->db->insert('affiliate_log', $log_data);
   }
 
   
/////////////////////////////////////////////////   
}