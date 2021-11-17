<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class User_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
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

    public function get_count_all_users($search_word='', $list_filter =0)
    {
        if(trim($search_word) !='')
        {
            $this->db->where('(username LIKE "%'.$search_word.'%" OR email LIKE "%'.$search_word.'% "OR phone LIKE "%'.$search_word.'%")');
        }

        if($list_filter == 2)
        {
           $this->db->where('mail_list',0);

        }
        elseif($list_filter == 1)
        {
           $this->db->where('mail_list',1);
        }

       return $this->db->count_all_results('users');
    }

    public function get_users_data($limit,$offset, $lang_id,$search_word='',$order_by='',$order_state,$list_filter =0)
    {
        $this->db->select('users.*, customer_groups_translation.title as customer_group_name');
        $this->db->join('customer_groups_translation', 'users.customer_group_id = customer_groups_translation.customer_group_id
                        AND customer_groups_translation.lang_id = '.$lang_id);
                        
        if(trim($search_word) !='')
        {
            $this->db->where('(email LIKE "%'.$search_word.'%" OR first_name LIKE "%'.$search_word.'%" OR phone LIKE "%'.$search_word.'%")');
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
         if($list_filter == 2)
        {
           $this->db->where('mail_list',0);

        }elseif($list_filter == 1)
        {
           $this->db->where('mail_list',1);

        }
        $result = $this->db->get('users',$limit,$offset);

        if($result)
        {
            return $result->result();
        }
    }

   public function get_user_groups($lang_id ,$id)
   {
        $this->db->select('groups_translation.name , groups_translation.group_id ');

        $this->db->join('users_groups' ,'groups_translation.group_id = users_groups.group_id');

        $this->db->where('groups_translation.lang_id',$lang_id);
        $this->db->where('users_groups.user_id',$id);

        $result = $this->db->get('groups_translation');

        if($result)
        {
            return $result->result();
        }
        else {
          return false;
        }
   }

   public function get_group_users($group_id)
   {
        $this->db->select('users.*');
        $this->db->join('users_groups' ,'users.id = users_groups.user_id');
        $this->db->where('users_groups.group_id',$group_id);

        $result = $this->db->get('users');

        if($result)
        {
            return $result->result();
        }
        else {
          return false;
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

    public function get_user($lang_id , $id)
    {
        $this->db->select('groups_translation.group_id ,groups_translation.name, users.* ,customer_groups_translation.customer_group_id ,customer_groups_translation.title');

        $this->db->join('customer_groups_translation' ,'users.customer_group_id = customer_groups_translation.customer_group_id AND customer_groups_translation.lang_id='.$lang_id, 'left');
        $this->db->join('users_groups' ,'users.id = users_groups.user_id', 'left');
        $this->db->join('groups_translation' ,'users_groups.group_id = groups_translation.group_id AND groups_translation.lang_id ='.$lang_id, 'left');

        $this->db->where('users.id',$id);
        //$this->db->where('groups_translation.lang_id',$lang_id);
        //$this->db->where('customer_groups_translation.lang_id',$lang_id);

        $result = $this->db->get('users');

        if($result)
        {
            return $result->row();
        }else{
            return false ;
        }
    }

    public function get_active_users_data()
    {
        $this->db->select('users.*, users.username as name');
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
    public function get_user_row_data($lang_id, $user_id)
    {
        $this->db->select('users.*, countries_translation.name, countries_translation.name as country, users.id as user_id');
        $this->db->join('countries_translation', 'users.country_id = countries_translation.country_id');

        $this->db->where('users.id', $user_id);
        $this->db->where('countries_translation.lang_id', $lang_id);

        $row = $this->db->get('users');

        if($row)
        {
            return $row->row();
        }
        else
        {
            return false;
        }

    }

    public function count_user_data_exist($field, $value)
    {
        $this->db->where($field, $value);
        return $this->db->count_all_results('users');
    }

    public function get_user_data_by_field($field, $value)
    {
        $this->db->where($field, $value);

        $query = $this->db->get('users');

        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }

    public function get_user_data_by_fields($conditions_array)
    {
        foreach($conditions_array as $field=>$value)
        {
            $this->db->where($field, $value);
        }

        return $this->db->count_all_results('users');
    }

    public function get_user_data_by_conditions($conditions_array)
    {
        foreach($conditions_array as $field=>$value)
        {
            $this->db->where($field, $value);
        }

        $query = $this->db->get('users');

        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }

    public function get_users_result()
    {
        $this->db->select('users.*, users.username as name');

        $this->db->where('username != ""');
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

  /*************************************************************/
    public function update_user_balance($user_id, $new_balance_data)
    {
        $this->db->where('id', $user_id);

        return $this->db->update('users', $new_balance_data);
    }

    public function update_user($user_id, $user_data)
    {
        $this->db->where('id', $user_id);

        return $this->db->update('users', $user_data);
    }

    public function update_by_user_device($device_id, $data)
    {
        $this->db->where('device_id', $device_id);
        if($this->db->update('users', $data))
            return true;
        else
            return false;

    }

    public function get_user_id_by_affiliate_code($code)
    {
        $this->db->where('code', $code);

        $result = $this->db->get('affiliate');

        if($result)
        {
            return $result->row()->user_id;
        }
        else
        {
            return false;
        }
    }

   public function get_user_affiliate_log($user_id)
   {
        $this->db->select('users.*, affiliate_log.*, orders.final_total');

        //$this->db->join('affiliate', 'affiliate_log.user_id = affiliate.user_id');
        $this->db->join('users', 'affiliate_log.user_id = users.id');
        $this->db->join('orders', 'affiliate_log.order_id = orders.id');

        $this->db->where('affiliate_log.buyer_id',$user_id);

        $result = $this->db->get('affiliate_log');

        if($result)
        {
            return $result->result();
        }
   }

    public function get_user_orders_data($user_id, $lang_id)
    {
        $this->db->select('orders.* , users.*, order_status_translation.name, payment_methods_translation.name, orders.id as id, order_status_translation.name as status ,countries_translation.name as country_name, payment_methods_translation.name as payment_method');

        $this->db->join('users' ,'orders.user_id = users.id');
        $this->db->join('order_status_translation', 'orders.order_status_id = order_status_translation.status_id');
        $this->db->join('countries_translation' ,'orders.country_id = countries_translation.country_id');
        $this->db->join('payment_methods_translation' ,'orders.payment_method_id = payment_methods_translation.payment_method_id');

        $this->db->where('orders.user_id', $user_id);
        $this->db->where('order_status_translation.lang_id', $lang_id);
        $this->db->where('countries_translation.lang_id', $lang_id);
        $this->db->where('payment_methods_translation.lang_id', $lang_id);

        $this->db->order_by('orders.unix_time', 'desc');

        $result = $this->db->get('orders');

        if($result)
        {
            return $result->result();
        }
    }

    public function check_if_user_regestered($email, $phone)
    {
        $this->db->where('first_order', 1);
        $this->db->where('email', $email);
        $this->db->where('phone', $phone);

        return $this->db->count_all_results('users');
    }

    public function check_user_phone_exist($phone)
    {
        $this->db->where('phone', $phone);

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

    public function get_first_registered_user($email, $phone)
    {
        $this->db->where('first_order', 1);
        $this->db->where('email', $email);
        $this->db->where('phone', $phone);

        $row = $this->db->get('users');

        if($row)
        {
            return $row->row();
        }
        else
        {
            return false;
        }
    }

    public function get_user_log_data($user_id, $lang_id)
    {

        $this->db->select('userlog.*, userlog_actions_translation.*, users.*, controllers_translation.name, modules_translation.name ,controllers_translation.name as controller, modules_translation.name as module, userlog_actions_translation.name as action_name, userlog.ip_address as ip_address ');

        $this->db->join('userlog_actions_translation','userlog.action_id = userlog_actions_translation.userlog_actions_id');
        $this->db->join('users','userlog.user_id = users.id');
        $this->db->join('controllers_translation','userlog.controller_id = controllers_translation.controller_id');
        $this->db->join('modules_translation','userlog.module_id = modules_translation.module_id');

        $this->db->where('users.id', $user_id);
        $this->db->where('modules_translation.lang_id', $lang_id);
        $this->db->where('controllers_translation.lang_id', $lang_id);
        $this->db->where('userlog_actions_translation.lang_id', $lang_id);

        $result = $this->db->get('userlog');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_visits_log_data($user_id, $lang_id)
    {
        $this->db->select('visits_log.*, users.*, modules_translation.name, controllers_translation.name, methods_translation.name, modules_translation.name as module, controllers_translation.name as controller, methods_translation.name as method');

        $this->db->join('users', 'visits_log.user_id = users.id');
        $this->db->join('modules_translation', 'visits_log.module_id = modules_translation.module_id');
        $this->db->join('controllers_translation', 'visits_log.controller_id = controllers_translation.controller_id');
        $this->db->join('methods_translation', 'visits_log.method_id = methods_translation.method_id');

        $this->db->where('users.id', $user_id);
        $this->db->where('modules_translation.lang_id', $lang_id);
        $this->db->where('controllers_translation.lang_id', $lang_id);
        $this->db->where('methods_translation.lang_id', $lang_id);

        $result = $this->db->get('visits_log');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_suggestions($search_word, $field, $rep_id)
    {
        $this->db->select($field.', id');
        $this->db->where('('.$field.' LIKE "%'.$search_word.'%" )');

        if($rep_id != 0)
        {
            $this->db->where('representative_id', $rep_id);
        }

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

    public function get_user_id_by_email($email)
    {
        $this->db->select('id');
        $this->db->where('email', $email);

        $query = $this->db->get('users');

        if($query->row())
        {
            return $query->row()->id;
        }
        else
        {
            return false;
        }
    }

    public function get_users_suggestions($search_word, $rep_id)
    {
        $this->db->where('(first_name LIKE "%'.$search_word.'%" OR last_name LIKE "%'.$search_word.'%")');

        if($rep_id != 0)
        {
            $this->db->where('representative_id', $rep_id);
        }

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

    public function get_phones_suggestions($search_word, $rep_id)
    {
        $this->db->select('phone, id');
        $this->db->where('(phone LIKE "%'.$search_word.'%" )');

        if($rep_id != 0)
        {
            $this->db->where('representative_id', $rep_id);
        }

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

    public function get_user_stores($lang_id, $user_id)
    {
        $this->db->select('stores_translation.*');

        $this->db->join('users_stores', 'stores_translation.store_id = users_stores.store_id AND users_stores.user_id = '.$user_id);

        $this->db->where('stores_translation.lang_id', $lang_id);

        $result = $this->db->get('stores_translation');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function remove_user_stores($user_id, $user_stores=array())
    {
        $this->db->where('user_id', $user_id);
        $this->db->delete('users_stores');

        if(count($user_stores) !=0)
        {
            $this->db->where('user_id', $user_id);
            $this->db->where_not_in('user_stores_permissions.store_id', $user_stores);

            $this->db->delete('user_stores_permissions');
        }
    }

    public function insert_user_store($data)
    {
        return $this->db->insert('users_stores', $data);
    }

    public function user_stores_count($user_id)
    {
        $this->db->where('user_id', $user_id);
        return $this->db->count_all_results('users_stores');
    }

    public function get_user_permitted_stores_ids_per_method($user_id, $method_id, $controller_id, $lang_id)
    {
        $this->db->select('user_stores_permissions.store_id, user_stores_permissions.store_id as id, stores_translation.*');

        $this->db->join('permissions', 'user_stores_permissions.permission_id = permissions.id');
        $this->db->join('stores_translation', 'user_stores_permissions.store_id = stores_translation.store_id AND stores_translation.lang_id ='.$lang_id);

        $this->db->where('user_stores_permissions.user_id', $user_id);
        $this->db->where('permissions.controller_id', $controller_id);
        $this->db->where('permissions.method_id', $method_id);

        $result = $this->db->get('user_stores_permissions');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }

    }

    public function get_user_stores_ids($user_id)
    {
        $this->db->select('store_id');
        $this->db->where('user_id', $user_id);

        $result = $this->db->get('users_stores');

        if($result)
        {
            $result->result();
        }
        else
        {
            return false;
        }
    }

    public function check_user_exist($social_id, $type, $email)
    {
        if($email != '')
        {
            $this->db->where('email', $email);
        }
        else
        {
            $this->db->where('user_type', $type);
            $this->db->where($type.'_id', $social_id);
        }

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

    public function get_user_by_social($social_id, $type, $email)
    {
        if($email != '')
        {
            $this->db->where('email', $email);
        }
        else
        {
            $this->db->where('user_type', $type);
            $this->db->where($type.'_id', $social_id);
        }

        $query = $this->db->get('users');

        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }

    public function get_user_stores_data($user_id)
    {
        $this->db->join('stores', 'users_stores.store_id=stores.id AND stores.active=1');
        $this->db->where('users_stores.user_id', $user_id);

        $result = $this->db->get('users_stores');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function check_user_in_group($user_id, $group_id)
    {
      $this->db->where('user_id', $user_id);
      $this->db->where('group_id', $group_id);

      $count = $this->db->count_all_results('users_groups');

      if($count > 0)
      {
        return true;
      }
      else {
        return false;
      }
    }
  ////////////////////////////////////////////////

  /**
   * User API Log
   */
  
    public function get_count_all_user_api_log($search_word='')
    {
        $this->db->join('users', 'users_api_log.user_id=users.id', 'left');
        if($search_word != '')
        {
            $this->db->where('(users.phone LIKE "%'.$search_word.'%" OR users.first_name LIKE "%'.$search_word.'% "OR users.last_name LIKE "%'.$search_word.'%"OR users_api_log.api_name LIKE "%'.$search_word.'%"OR users_api_log.agent LIKE "%'.$search_word.'%")');
        }
        return $this->db->count_all_results('users_api_log');
    }

    public function get_user_api_log($lang_id, $limit, $offset, $search_word, $order_by, $order_state, $user_filter_id, $modules_filter_id, $controllers_filter_id, $methods_filter_id)
    {
        $this->db->select('users.first_name, users.last_name, users.phone, users_api_log.agent, users_api_log.api_name,
                            users_api_log.url, users_api_log.id, users_api_log.unix_time');
        $this->db->join('users', 'users_api_log.user_id=users.id', 'left');

        if($search_word != '')
        {
            $this->db->where('(users.phone LIKE "%'.$search_word.'%" OR users.first_name LIKE "%'.$search_word.'% "OR users.last_name LIKE "%'.$search_word.'%"OR users_api_log.api_name LIKE "%'.$search_word.'%"OR users_api_log.agent LIKE "%'.$search_word.'%")');
        }

        if($order_by == lang('date'))
        {
            $this->db->order_by('users_api_log.unix_time', $order_state);
        }
        else if($order_by == lang('username'))
        {
            $this->db->order_by('users.phone', $order_state);
        }
        else
        {
            $this->db->order_by('users_api_log.id', $order_state);
        }

        $result = $this->db->get('users_api_log', $limit, $offset);

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }


    }

    public function get_user_api_log_data($id)
    {
        $this->db->select('users.first_name, users.last_name, users.phone, users_api_log.*');
        $this->db->join('users', 'users_api_log.user_id=users.id', 'left');

        $this->db->where('users_api_log.id', $id);
        $query = $this->db->get('users_api_log');

        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }

    }
    
    public function delete_user_api_log_row($ids_array)
    {
        $this->db->where_in('id', $ids_array);
        $this->db->delete('users_api_log');
        
        echo '1';
    
    }

    public function insert_table_data($table_name, $data)
    {
        return $this->db->insert($table_name, $data);
    }
}
