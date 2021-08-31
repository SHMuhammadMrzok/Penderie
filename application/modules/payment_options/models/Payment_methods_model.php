<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Payment_methods_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    /********************Insert *****************************/
    public function insert_payment_methods_data($payment_methods_data)
    {
        return $this->db->insert('payment_methods', $payment_methods_data);
    }

    public function insert_payment_methods_translation($translation_data)
    {
        return $this->db->insert('payment_methods_translation', $translation_data);
    }
   /**********************GET*******************************/

    public function get_count_all_payment_methods($search_word='')
    {
         $this->db->join('payment_methods_translation', 'payment_methods.id = payment_methods_translation.payment_method_id');

         if(trim($search_word) !='')
        {
            $this->db->like('payment_methods_translation.name', $search_word, 'both');
        }

        return $this->db->count_all_results('payment_methods');
    }

   public function get_payment_methods_data($lang_id, $limit, $offset, $search_word)
    {
        $this->db->select('payment_methods.*, payment_methods_translation.*, payment_methods.id as id');
        $this->db->join('payment_methods_translation', 'payment_methods.id = payment_methods_translation.payment_method_id');

        if(trim($search_word) !='')
        {
            $this->db->like('payment_methods_translation.name', $search_word, 'both');
        }

        $this->db->where('payment_methods_translation.lang_id', $lang_id);

        $result = $this->db->get('payment_methods',$limit,$offset);

        if($result)
        {
            return $result->result();
        }
    }

    public function get_row_data($id, $display_lang_id)
    {
        $this->db->select('payment_methods.*, payment_methods_translation.*, payment_methods.id as id');
        $this->db->join('payment_methods_translation', 'payment_methods.id = payment_methods_translation.payment_method_id');

        $this->db->where('id',$id);
        $this->db->where('payment_methods_translation.lang_id', $display_lang_id);

        $result = $this->db->get('payment_methods');

        if($result)
        {// echo ($result->row()->name);die();
            return $result->row();
        }
        else
        {
            return false;
        }
    }

    public function get_option_data($id)
    {
        $this->db->where('id', $id);

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

    public function get_payment_methods_translations_data($id)
    {
        $this->db->where('payment_method_id',$id);

        $result = $this->db->get('payment_methods_translation');

        if($result)
        {
            return $result->result();
        }
    }
    public function get_order_status($lang_id)
    {
        $this->db->where('lang_id', $lang_id);

        $result = $this->db->get('order_status_translation');

        if($result)
        {
            return $result->result();
        }
    }

    public function get_order_status_name( $order_status_id , $display_lang_id)
    {
        $this->db->where('lang_id', $display_lang_id);
        $this->db->where('status_id', $order_status_id);

        $result = $this->db->get('order_status_translation');

        if($result)
        {
            return $result->row()->name;
        }
    }
    public function get_payment_options($lang_id)
    {
        $this->db->select('payment_methods.*, payment_methods_translation.*, payment_methods.id as id');
        $this->db->join('payment_methods_translation', 'payment_methods.id = payment_methods_translation.payment_method_id');

        $this->db->where('payment_methods.active', 1);
        $this->db->where('payment_methods_translation.lang_id', $lang_id);

        $result = $this->db->get('payment_methods');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_payment_method_name($id, $lang_id)
    {
        $this->db->select('payment_methods.*, payment_methods_translation.*, payment_methods.id as id');
        $this->db->join('payment_methods_translation', 'payment_methods.id = payment_methods_translation.payment_method_id');

        $this->db->where('id',$id);
        $this->db->where('payment_methods_translation.lang_id', $lang_id);

        $query = $this->db->get('payment_methods');

        if($query->row())
        {
            return $query->row()->name;
        }else
        {
            return false;
        }
    }

    public function get_bank_data($bank_id, $lang_id)
    {
        $this->db->select('bank_accounts.*, bank_accounts_translation.*');
        $this->db->join('bank_accounts_translation', 'bank_accounts.id = bank_accounts_translation.bank_account_id');

        $this->db->where('bank_accounts.id', $bank_id);
        $this->db->where('bank_accounts_translation.lang_id', $lang_id);

        $result = $this->db->get('bank_accounts');

        if($result)
        {
            return $result->row();
        }
        else
        {
            return false;
        }
    }

    /**********************Update*******************************/
    public function update_payment_methods_data($id,$data)
    {
        $this->db->where('id', $id);
        return $this->db->update('payment_methods', $data);
    }

    public function update_payment_methods_translation_data($id,$lang_id, $payment_methods_translation_data)
    {
        $this->db->where('payment_method_id', $id);
        $this->db->where('lang_id', $lang_id);
        return $this->db->update('payment_methods_translation', $payment_methods_translation_data);
    }

    /**********************DELETE*******************************/

    public function delete_payment_methods_data($payment_methods_id_array)
    {
        $this->db->where_in('id',$payment_methods_id_array);
        $this->db->delete('payment_methods');

        $this->db->where_in('payment_methods_translation.payment_method_id',$payment_methods_id_array);
        $this->db->delete('payment_methods_translation');

    }

    public function get_payment_max_sort_value()
    {
        $this->db->select_max('sort');
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

    public function get_available_payment_options($display_lang_id, $customer_group_id, $not_included_ids, $wholesaler_pocket)
    {
        $this->db->select('payment_methods.*, payment_methods_translation.*, payment_methods.id as id');
        $this->db->join('payment_methods_translation', 'payment_methods.id = payment_methods_translation.payment_method_id');

        /*if($wholesaler_pocket)
        {
            $this->db->where('payment_methods.id', 1);
        }
        else*/
        if($customer_group_id != 0)
        {
            $this->db->join('customer_groups_payment_methods', 'payment_methods.id = customer_groups_payment_methods.payment_method_id');
            $this->db->where('customer_groups_payment_methods.customer_group_id', $customer_group_id);
        }

        $this->db->where('payment_methods.active', 1);
        $this->db->where('payment_methods_translation.lang_id', $display_lang_id);

        if(count($not_included_ids)>0)
        {
            $this->db->where_not_in('payment_methods.id', $not_included_ids);
        }

        $this->db->order_by('payment_methods.sort','asc');
        
        $result = $this->db->get('payment_methods');

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
