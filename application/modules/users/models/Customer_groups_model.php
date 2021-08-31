<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Customer_groups_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    /*****************insert ****************/
    public function insert_customer_groups($data)
    {
        return $this->db->insert('customer_groups', $data);
    }

    public function insert_customer_groups_translation($customer_groups_translation_data)
    {
        return $this->db->insert('customer_groups_translation', $customer_groups_translation_data);
    }

    public function insert_group_payment_option($payment_data)
    {
        return $this->db->insert('customer_groups_payment_methods', $payment_data);
    }

    /*************** get *******************/
    public function get_customer_groups_result($id)
    {
        $this->db->where('id',$id);
        $query = $this->db->get('customer_groups');

        if($query)
        {
            return $query->row();
        }
    }
    public function get_customer_groups_translation_result($id)
    {
        $this->db->select('customer_groups_translation.*');
        $this->db->join('customer_groups_translation','customer_groups.id = customer_groups_translation.customer_group_id');
        $this->db->where('customer_groups.id',$id);
        $query = $this->db->get('customer_groups');

        if($query)
        {
            return $query->result();
        }
    }

    public function get_customer_groups($lang_id)
    {
        $this->db->select('customer_groups_translation.*,customer_groups.*');
        $this->db->join('customer_groups_translation','customer_groups.id = customer_groups_translation.customer_group_id');
        $this->db->where('customer_groups_translation.lang_id',$lang_id);
        $query = $this->db->get('customer_groups');

        if($query)
        {
            return $query->result();
        }
    }

    public function get_available_customer_groups($lang_id, $users_group_id)
    {
        $this->db->select('customer_groups_translation.*,customer_groups.*');
        $this->db->join('customer_groups_translation','customer_groups.id = customer_groups_translation.customer_group_id');

        $this->db->where('customer_groups.id !=',$users_group_id);
        $this->db->where('customer_groups_translation.lang_id',$lang_id);

        $query = $this->db->get('customer_groups');

        if($query)
        {
            return $query->result();
        }
    }

    public function get_customer_group_users_count($group_id)
    {
        $this->db->where('customer_group_id',$group_id);

        return $this->db->count_all_results('users');
    }


    public function get_customer_groups_data($lang_id,$limit,$offset,$search_word='',$country_id=0,$order_by='',$order_state)
    {
        $this->db->select('customer_groups_translation.* , customer_groups.* ,countries_translation.*','customer_groups.id as id ');

        $this->db->join('customer_groups_translation' ,'customer_groups.id = customer_groups_translation.customer_group_id');
        $this->db->join('countries_translation','customer_groups.country_id = countries_translation.country_id AND countries_translation.lang_id = '.$lang_id, 'left');

        //$this->db->where('countries_translation.lang_id',$lang_id);
        $this->db->where('customer_groups_translation.lang_id',$lang_id);

        if(trim($search_word) !='')
        {
            $this->db->like('customer_groups_translation.title', $search_word, 'both');
        }

        if($country_id != 0)
        {
             $this->db->where('customer_groups.country_id',$country_id);
        }

        if($order_by != '')
        {
            if($order_by == lang('customer_group_name'))
            {
                $this->db->order_by('customer_groups_translation.title',$order_state);
            }
            elseif($order_by == lang('country'))
            {
                $this->db->order_by('countries_translation.name',$order_state);
            }
            elseif($order_by == lang('discount_percentage'))
            {
                $this->db->order_by('customer_groups.discount_percentage',$order_state);
            }
            elseif($order_by == lang('product_limit_per_order'))
            {
                $this->db->order_by('customer_groups.product_limit_per_order',$order_state);
            }
            else
            {
                $this->db->order_by('customer_groups.id',$order_state);
            }
        }
        else
        {
            $this->db->order_by('customer_groups.id',$order_state);
        }

        $result = $this->db->get('customer_groups',$limit,$offset);

        if($result)
        {
            return $result->result();
        }

    }

    public function get_count_all_customer_groups($lang_id ,$search_word ='',$country_id=0)
    {
        $this->db->join('customer_groups_translation' ,'customer_groups.id = customer_groups_translation.customer_group_id');

        if(trim($search_word) !='')
        {
            $this->db->like('customer_groups_translation.title', $search_word, 'both');
        }
        if($country_id != 0)
        {
             $this->db->where('customer_groups.country_id',$country_id);
        }

        $this->db->where('customer_groups_translation.lang_id',$lang_id);

        return $this->db->count_all_results('customer_groups');
    }


    public function get_row_data($row_id, $display_lang_id)
    {
        $this->db->select('customer_groups_translation.* , customer_groups.* , countries_translation.name, countries_translation.name as country');

        $this->db->join('customer_groups_translation' ,'customer_groups.id = customer_groups_translation.customer_group_id');
        $this->db->join('countries_translation','customer_groups.country_id = countries_translation.country_id');

        $this->db->where('countries_translation.lang_id',$display_lang_id);
        $this->db->where('customer_groups_translation.lang_id',$display_lang_id);
        $this->db->where('customer_groups.id',$row_id);

        $result = $this->db->get('customer_groups');

        if($result)
        {
            return $result->row();
        }

    }

    public function get_customer_group_translation($customer_group_id, $lang_id)
    {
        $this->db->where('customer_group_id', $customer_group_id);
        $this->db->where('lang_id', $lang_id);

        $query = $this->db->get('customer_groups_translation');

        if($query)
        {
            $query = $query->row();
            if($query)
            {
                return $query->title;
            }
        }
        else
        {
            return false;
        }
    }

    public function get_group_payment_options($goup_id, $lang_id)
    {
        $this->db->select('payment_methods.*, customer_groups_payment_methods.*, payment_methods_translation.name , payment_methods.id as id');

        $this->db->join('customer_groups_payment_methods', 'payment_methods.id=customer_groups_payment_methods.payment_method_id AND customer_groups_payment_methods.customer_group_id ='.$goup_id, 'left');
        $this->db->join('payment_methods_translation', 'payment_methods.id = payment_methods_translation.payment_method_id');

        $this->db->where('payment_methods_translation.lang_id', $lang_id);
        //$this->db->where('')

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

    public function available_group_payment_options($group_id, $lang_id)
    {
        $this->db->select('payment_methods.*, customer_groups_payment_methods.*, payment_methods_translation.name , payment_methods.id as id');

        $this->db->join('customer_groups_payment_methods', 'payment_methods.id=customer_groups_payment_methods.payment_method_id AND customer_groups_payment_methods.customer_group_id ='.$group_id, 'left');
        $this->db->join('payment_methods_translation', 'payment_methods.id = payment_methods_translation.payment_method_id');

        $this->db->where('payment_methods_translation.lang_id', $lang_id);
        $this->db->where('customer_groups_payment_methods.customer_group_id', $group_id);

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

    public function get_user_payment_options($customer_group_id)
    {
        $this->db->where('customer_group_id', $customer_group_id);

        $result = $this->db->get('customer_groups_payment_methods');
        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_user_customer_group_data($user_id)
    {
        $this->db->select('customer_groups.*');

        $this->db->join('users', 'users.customer_group_id = customer_groups.id');
        $this->db->where('users.id', $user_id);

        $query = $this->db->get('customer_groups');

        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }

    public function get_customer_group_main_data($customer_group_id)
    {
        $this->db->where('id', $customer_group_id);

        $query = $this->db->get('customer_groups');

        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }

    /********************update ********************/

    public function update_customer_groups($customer_group_id,$data)
    {
        $this->db->where('id',$customer_group_id);
        return $this->db->update('customer_groups',$data);
    }
    public function update_customer_groups_translation($customer_group_id,$lang_id,$customer_groups_translation_data)
    {
        $this->db->where('customer_group_id',$customer_group_id);
        $this->db->where('lang_id',$lang_id);
        return $this->db->update('customer_groups_translation',$customer_groups_translation_data);
    }

    /******************delete*******************************/

    public function delete_customer_groups_translation($customer_group_id)
    {
        $this->db->where('customer_group_id',$customer_group_id);
        $this->db->delete('customer_groups_translation');
    }


    public function delete_customer_groups_data($customer_group_id_array)
    {
        $this->db->where_in('id',$customer_group_id_array);
        $this->db->delete('customer_groups');

        $this->db->where_in('customer_group_id',$customer_group_id_array);
        $this->db->delete('customer_groups_translation');

        $this->db->where_in('customer_group_id',$customer_group_id_array);
        $this->db->delete('customer_groups_payment_methods');

        //echo '1';
    }

    public function delete_group_payment_options($customer_group_id)
    {
        $this->db->where('customer_group_id', $customer_group_id);
        return $this->db->delete('customer_groups_payment_methods');
    }

    public function check_group_users($groups_ids)
    {
        $this->db->where_in('customer_group_id', $groups_ids);

        $count = $this->db->count_all_results('users');

        if($count > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function get_upgade_account_groups($lang_id, $excluded_groups, $min_price=0)
    {
      $this->db->select('customer_groups.*, customer_groups_translation.*');
      $this->db->join('customer_groups_translation', 'customer_groups.id = customer_groups_translation.customer_group_id
                      AND customer_groups_translation.lang_id = '. $lang_id);

      $this->db->where_not_in('customer_groups.id', $excluded_groups);

      if($min_price != 0)
      {
        $this->db->where('customer_groups.price >', $min_price);
      }

      $query = $this->db->get('customer_groups');

      if($query)
      {
        return $query->result();
      }
      else {
        return false;
      }

    }
/****************************************************************/
}
