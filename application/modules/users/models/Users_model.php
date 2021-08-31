<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Users_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    /************Insert************/

   ///////////////groups functions ///////////
   public function insert_group($data)
   {
        return $this->db->insert('groups', $data);
   }

   public function insert_group_translation($groups_translation_data)
   {
        return $this->db->insert('groups_translation', $groups_translation_data);
   }

   public function insert_incorrect_login_attempt($data)
   {
        return $this->db->insert('incorrect_login_attempts', $data);
   }


    /********Update**************/

    public function update_groups_data($group_id ,$group_general_data)
    {
        $this->db->where('id',$group_id);
        $this->db->update('groups',$group_general_data);
    }
    public function update_group_translation($group_id , $groups_translation_data)
    {
        $this->db->where('group_id',$group_id);
        $this->db->update('groups_translation',$groups_translation_data);
    }

    public function update_user_bank_accounts($user_id,$bank,$user_bank_data)
    {
        $this->db->where('user_id',$user_id);
        $this->db->where('bank_id',$bank);

        $this->db->update('user_bank_accounts',$user_bank_data);
    }
    /**********Get***************/
    function get_user_group_id($user_id)
    {
       $row=$this->db->get_where('users_groups',array('user_id'=>$user_id))->row();
       if($row)
       {
            return $row->group_id;
       }
    }

   public function get_user($user_id)
   {
        $this->db->where('id',$user_id);
        $row=$this->db->get('users');
        if($row)
        {
            return $row->row();
        }else{
            return false;
        }
   }

   public function get_users()
   {
        $query = $this->db->get('users');

        if($query)
        {
            return $query->result();
        }
        else
        {
            return false;
        }
   }

   public function get_active_users()
   {
        $this->db->where('active', 1);

        $query = $this->db->get('users');

        if($query)
        {
            return $query->result();
        }
        else
        {
            return false;
        }
   }

   public function get_active_users_names()
   {
        $this->db->select('id, first_name, last_name');
        $this->db->where('active', 1);
        $this->db->where('first_order', 0);

        $query = $this->db->get('users');

        if($query)
        {
            return $query->result();
        }
        else
        {
            return false;
        }
   }

   public function get_representivives_users($rep_group_id)
   {
        $this->db->where('customer_group_id', $rep_group_id);
        $this->db->where('active', 1);

        $result = $this->db->get('users');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
   }

   public function get_countries()
   {
        $result = $this->db->get('countries')->result();
        return $result;
   }
   public function get_group_result($id)
   {
       $this->db->where('id',$id);
       $query = $this->db->get('groups');

       if($query)
       {
           return $query->row();
       }
   }
    public function get_group_translation_result($id)
    {
        $this->db->select('groups_translation.*');
        $this->db->join('groups_translation','groups.id = groups_translation.group_id');
        $this->db->where('groups.id',$id);
        $query = $this->db->get('groups');

        if($query)
        {
            return $query->result();
        }
    }


    public function get_users_filter_data()
    {
        $this->db->select('users.username, users.id, users.username as name');
        $query = $this->db->get('users');

        if($query)
        {
            return $query->result();
        }

    }
    public function get_count_all_users($search_word='')
    {
        if(trim($search_word) !='')
        {
            $this->db->like('username', $search_word, 'both');
            $this->db->or_like('first_name', $search_word, 'both');
            $this->db->or_like('last_name', $search_word, 'both');
            $this->db->or_like('email', $search_word, 'both');
       }

       return $this->db->count_all_results('users');
    }

   public function get_users_data($limit,$offset,$search_word='',$order_by='',$order_state)
    {
        if(trim($search_word) !='')
        {
            $this->db->like('username', $search_word, 'both');
            $this->db->or_like('first_name', $search_word, 'both');
            $this->db->or_like('last_name', $search_word, 'both');
            $this->db->or_like('email', $search_word, 'both');

        }

        if($order_by != '')
        {
            if($order_by == lang('username'))
            {
                $this->db->order_by('username',$order_state);
            }
            elseif($order_by == lang('email'))
            {
                $this->db->order_by('email',$order_state);
            }

        }
        else
        {
            $this->db->order_by('id',$order_state);
        }

        $result = $this->db->get('users',$limit,$offset);

        if($result)
        {
            return $result->result();
        }
    }

     public function get_row_data($id)
    {

        $this->db->where('id',$id);

        $result = $this->db->get('users');

        if($result)
        {
            return $result->row();
        }
        else
        {
            return false;
        }
    }

    public function get_users_names($limit)
    {
        $this->db->order_by('id', 'desc');

        $this->db->where('active', 1);

        $result = $this->db->get('users', $limit);

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_all_users_data($lang_id, $limit, $offset)
    {
        $this->db->select('users.*, customer_groups_translation.title, users.id as id, customer_groups_translation.title as customer_group');
        $this->db->join('customer_groups_translation', 'users.customer_group_id = customer_groups_translation.customer_group_id AND customer_groups_translation.lang_id ='.$lang_id, 'left');

        $result = $this->db->get('users', $limit, $offset);

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_users_count()
    {
        return $this->db->count_all_results('users');
    }

    public function get_user_bank_accounts_data($user_id ,$lang_id)
    {
        $this->db->select('user_bank_accounts.*, bank_accounts_translation.bank');
        $this->db->join('bank_accounts_translation', 'user_bank_accounts.bank_id = bank_accounts_translation.bank_account_id');

        $this->db->where('user_bank_accounts.user_id', $user_id);
        $this->db->where('bank_accounts_translation.lang_id', $lang_id);

        $result = $this->db->get('user_bank_accounts');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_bank_accounts_result($display_lang_id, $user_id)
    {
        $this->db->select('bank_accounts.*,bank_accounts.account_name as bank_account_name , bank_accounts.account_number as bank_account_number , bank_accounts_translation.*, user_bank_accounts.*, bank_accounts.id as id, user_bank_accounts.id as user_bank_account_id, user_bank_accounts.account_name as user_bank_account_name, user_bank_accounts.account_number as user_bank_account_number');

        $this->db->join('user_bank_accounts ', 'bank_accounts.id = user_bank_accounts.bank_id and user_bank_accounts.user_id ='.$user_id, 'left');
        $this->db->join('bank_accounts_translation', 'bank_accounts.id = bank_accounts_translation.bank_account_id');

        //$this->db->where('user_bank_accounts.user_id', $user_id);AS default_bank_accounts
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
    public function get_all_countries($lang_id)
    {
        $this->db->select('user_nationality.id as id, user_nationality_translation.name');
        $this->db->join('user_nationality_translation', 'user_nationality.id = user_nationality_translation.user_nationality_id');


        $this->db->where('user_nationality_translation.lang_id', $lang_id);

        $result = $this->db->get('user_nationality');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_nationality_name($country_id, $lang_id)
    {
        $this->db->where('user_nationality_id', $country_id);
        $this->db->where('lang_id', $lang_id);

        $row = $this->db->get('user_nationality_translation');

        if($row->row())
        {
            return $row->row()->name;
        }
        else
        {
            return false;
        }
    }

    public function get_user_groups($lang_id)
    {
        $this->db->where('lang_id', $lang_id);
        $result = $this->db->get('groups_translation');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_group_users($group_id)
    {
        $this->db->select('users_groups.*, users.*, users.id as id');
        $this->db->join('users', 'users_groups.user_id = users.id');

        $this->db->where('users_groups.group_id', $group_id);

        $result = $this->db->get('users_groups');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_users_total_balances()
    {
        //$this->db->select('id, user_balance');

        $this->db->where('user_balance !=', '');
        $this->db->where('user_balance !=', '0');

        $result = $this->db->get('users');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_representative_related_users($rep_user_id)
    {
        $this->db->where('representative_id', $rep_user_id);

        $result = $this->db->get('users');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_result_data_where($table_name, $type, $conditions_array, $limit=0, $offset=0)
    {
        foreach($conditions_array as $key=>$val)
        {
            $this->db->where($key, $val);
        }

        if($limit != 0)
        {
          $result = $this->db->get($table_name, $limit, $offset);
        }
        else {
          $result = $this->db->get($table_name);
        }

        if($result)
        {
            return $result->$type();
        }
        else
        {
            return false;
        }
    }

    public function insert_table_data($table_name, $data)
    {
      return $this->db->insert($table_name, $data);
    }

    public function update_table_data($table_name, $conds, $data)
    {
      foreach($conds as $key=>$val)
      {
        $this->db->where($key, $val);
      }

      return $this->db->update($table_name, $data);
    }

    public function check_user_address_used($address_id)
    {
      $this->db->where('address_id', $address_id);
      $count = $this->db->count_all_results('orders');

      if($count > 0)
      {
        return true;
      }
      else {
        return false;
      }

    }

    public function delete_user_address($address_id)
    {
      $this->db->where('id', $address_id);
      return $this->db->delete('user_addresses');
    }


  ////////////////////////////////////////////////
}
