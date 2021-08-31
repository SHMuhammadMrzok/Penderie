<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Bank_accounts_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    /********************Insert *****************************/
    public function insert_bank_accounts_data($bank_accounts_data)
    {
        return $this->db->insert('bank_accounts', $bank_accounts_data);
    }

    public function insert_bank_account_translation($translation_data)
    {
        return $this->db->insert('bank_accounts_translation', $translation_data);
    }
   /**********************GET*******************************/

    public function get_count_all_accounts()
    {
        return $this->db->count_all_results('bank_accounts');
    }

   public function get_bank_accounts_data($lang_id, $limit, $offset, $search_word)
    {
        $this->db->select('bank_accounts.*, bank_accounts_translation.*, bank_accounts.id as id');
        $this->db->join('bank_accounts_translation', 'bank_accounts.id = bank_accounts_translation.bank_account_id');

        if(trim($search_word) !='')
        {
            $this->db->like('bank_accounts_translation.bank', $search_word, 'both');
            $this->db->or_like('bank_accounts.account_name', $search_word, 'both');
            $this->db->or_like('bank_accounts.account_number', $search_word, 'both');
        }

        $this->db->where('bank_accounts_translation.lang_id', $lang_id);

        $result = $this->db->get('bank_accounts',$limit,$offset);

        if($result)
        {
            return $result->result();
        }
    }

    public function get_row_data($id,$lang_id)
    {
        $this->db->select('bank_accounts.*, bank_accounts_translation.*, bank_accounts.id as id');
        $this->db->join('bank_accounts_translation', 'bank_accounts.id = bank_accounts_translation.bank_account_id');

        $this->db->where('id',$id);
        $this->db->where('bank_accounts_translation.lang_id', $lang_id);

        $result = $this->db->get('bank_accounts');

        if($result)
        {
            return $result->row();
        }
    }

    public function get_bank_account_name($id, $lang_id)
    {
        $this->db->select('bank_accounts.*, bank_accounts_translation.*, bank_accounts.id as id');
        $this->db->join('bank_accounts_translation', 'bank_accounts.id = bank_accounts_translation.bank_account_id');

        $this->db->where('id',$id);
        $this->db->where('bank_accounts_translation.lang_id', $lang_id);

        $result = $this->db->get('bank_accounts');

        if($result->row())
        {
            return $result->row()->bank;
        }
    }

    public function get_bank_account_data($bank_id, $lang_id, $user_id)
    {
        $this->db->select('user_bank_accounts.*, bank_accounts_translation.*, user_bank_accounts.id as id');

        $this->db->join('bank_accounts_translation', 'user_bank_accounts.bank_id = bank_accounts_translation.bank_account_id');

        $this->db->where('bank_accounts_translation.lang_id', $lang_id);
        $this->db->where('user_bank_accounts.user_id', $user_id);
        $this->db->where('user_bank_accounts.bank_id', $bank_id);

        $result = $this->db->get('user_bank_accounts');

        if($result)
        {
            return $result->row();
        }
    }

    public function get_bank_data($bank_id, $lang_id)
    {
        $this->db->select('bank_accounts.*, bank_accounts_translation.bank, user_bank_accounts.*, bank_accounts.id as id');

        $this->db->join('bank_accounts_translation', 'bank_accounts.id = bank_accounts_translation.bank_account_id');
        $this->db->join('user_bank_accounts', 'bank_accounts.id = user_bank_accounts.bank_id', 'left');

        $this->db->where('bank_accounts.id', $bank_id);
        $this->db->where('bank_accounts_translation.lang_id', $lang_id);

        $query = $this->db->get('bank_accounts');

        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }

    public function get_bank_accounts_result($display_lang_id)
    {
        $this->db->select('bank_accounts.*, bank_accounts_translation.*');
        $this->db->join('bank_accounts_translation', 'bank_accounts.id = bank_accounts_translation.bank_account_id');

        $this->db->where('bank_accounts_translation.lang_id', $display_lang_id);

        $result = $this->db->get('bank_accounts');

        if($result->result())
        {
            return $result;
        }
        else
        {
            return false;
        }
    }

    public function get_bank_name($bank_id, $lang_id)
    {
        $this->db->where('lang_id', $lang_id);
        $this->db->where('bank_account_id', $bank_id);

        $query = $this->db->get('bank_accounts_translation');

        if($query)
        {
            return $query->row()->bank;
        }
        else
        {
            return false;
        }
    }

    public function get_order_status($status_id, $lang_id)
    {
        $this->db->where('status_id', $status_id);
        $this->db->where('lang_id', $lang_id);

        $query = $this->db->get('order_status_translation');

        if($query)
        {
            return $query->row()->name;
        }
        else
        {
            return false;
        }
    }

    public function count_banks_orders($bank_ids)
    {
        $this->db->where_in('bank_id', $bank_ids);

        return $this->db->count_all_results('orders');
    }


    /**********************Update*******************************/
    public function update_bank_accounts_data($id,$data)
    {
        $this->db->where('id', $id);
        return $this->db->update('bank_accounts', $data);
    }

    public function update_bank_accounts_translation_data($id, $lang_id, $bank_accounts_translation_data)
    {
        $this->db->where('bank_account_id', $id);
        $this->db->where('lang_id', $lang_id);

        return $this->db->update('bank_accounts_translation', $bank_accounts_translation_data);
    }

    /**********************DELETE*******************************/

    public function delete_bank_accounts_data($bank_accounts_id_array)
    {
        $this->db->where_in('id',$bank_accounts_id_array);
        $this->db->delete('bank_accounts');

        $this->db->where_in('bank_accounts_translation.bank_account_id',$bank_accounts_id_array);
        $this->db->delete('bank_accounts_translation');

    }





/****************************************************************/
}
