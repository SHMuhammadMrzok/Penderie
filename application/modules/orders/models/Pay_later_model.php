<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Pay_later_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }


    public function insert_bill($data)
    {
        return $this->db->insert('pay_later_bills', $data);
    }

    public function get_order_bills($order_id)
    {
      $this->db->select('orders.currency_symbol, pay_later_bills.*');
      $this->db->join('orders', 'pay_later_bills.order_id = orders.id');

      $this->db->where('order_id', $order_id);
      $this->db->order_by('unix_time', 'desc');

      $result = $this->db->get('pay_later_bills');

      if($result)
      {
        return $result->result();
      }
      else {
        return false;

      }
    }

    public function get_pay_later_orders_count()
    {
      $this->db->where('payment_method_id', 14);

      return $this->db->count_all_results('orders');
    }

    public function get_pay_later_orders_data($perPage, $offset)
    {
      $this->db->order_by('id', 'desc');
      $this->db->where('payment_method_id', 14);

      $result =  $this->db->get('orders', $perPage, $offset);

      if($result)
      {
        return $result->result();
      }
      else {
        return false;
      }
    }

/****************************************************************/
}
