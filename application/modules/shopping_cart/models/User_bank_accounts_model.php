<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class User_bank_accounts_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    /**********************INSERT*******************************/
   public function insert_user_account_data($data)
   {
       return $this->db->insert('user_bank_accounts', $data);
   }

   /**************************GET********************************/
   public function get_bank_accounts_result($display_lang_id, $user_id)
    {
        //$this->db->select('bank_accounts.*, bank_accounts_translation.*, user_bank_accounts.*, bank_accounts.id as id, user_bank_accounts.id as user_bank_account_id, user_bank_accounts.account_name as user_bank_account_name, user_bank_accounts.account_number as user_bank_account_number, bank_accounts.account_name as bank_account_name, bank_accounts.account_number as bank_account_number');
        $this->db->select('bank_accounts.*, bank_accounts_translation.*,
        bank_accounts.id as id, bank_accounts.account_name as bank_account_name,
        bank_accounts.account_number as bank_account_number');

        //$this->db->join('user_bank_accounts', 'bank_accounts.id = user_bank_accounts.bank_id and user_bank_accounts.user_id ='.$user_id, 'left');
        $this->db->join('bank_accounts_translation', 'bank_accounts.id = bank_accounts_translation.bank_account_id');

        $this->db->where('bank_accounts.active', 1);
        $this->db->where('bank_accounts_translation.lang_id', $display_lang_id);

        $result = $this->db->get('bank_accounts');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_user_bank_data($bank_id, $user_id)
    {
        $this->db->where('bank_id', $bank_id);
        $this->db->where('user_id', $user_id);

        $query = $this->db->get('user_bank_accounts');

        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }


   public function get_bank_accounts()
   {
       $this->db->select('bank_accounts.*, bank_accounts_translation.*');
       $this->db->join('bank_accounts_translation', 'bank_accounts.id = bank_accounts_translation.bank_account_id');

       $result = $this->db->get('bank_accounts');

       if($result)
       {
           return $result->result();
       }
       else
       {
           return false;
       }
   }

   public function get_user_bank_account($bank_id, $user_id)
   {
       $this->db->where('bank_id', $bank_id);
       $this->db->where('user_id', $user_id);

       $query = $this->db->get('user_bank_accounts');

       if($query)
       {
           return $query->row();
       }
       else
       {
           return false;
       }
   }

   /*********************UPDATE*************************************/

   public function update_user_account_data($data, $user_bank_acc_id)
   {
       $this->db->where('id',$user_bank_acc_id);

       $this->db->update('user_bank_accounts',$data);
   }

   /*************************COUNT*************************************/

   public function count_bank_accounts_for_user($bank_id, $user_id)
   {
       $this->db->where('bank_id', $bank_id);
       $this->db->where('user_id', $user_id);

       $count = $this->db->count_all_results('user_bank_accounts');

       return $count;
   }

   public function delete_bank_account($bank_id, $user_id)
   {
       $this->db->where('bank_id', $bank_id);
       $this->db->where('user_id', $user_id);

       return $this->db->delete('user_bank_accounts');
   }

   public function delete_user_bank_accounts($user_id)
   {
       $this->db->where('user_id', $user_id);
       return $this->db->delete('user_bank_accounts');
   }
/****************************************************************/
}
