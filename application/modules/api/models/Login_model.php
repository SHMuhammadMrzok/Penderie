<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Login_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    
   /**********************GET*******************************/
    
    public function get_user_group($lang_id ,$user_id)
    {
        
        $this->db->select('groups_translation.name as group_name');
        $this->db->join('groups_translation','groups.id = groups_translation.group_id');
        $this->db->join('users_groups','groups.id = users_groups.group_id');
        
        $this->db->where('users_groups.user_id',$user_id);
        $this->db->where('groups_translation.lang_id',$lang_id);
        
        $query = $this->db->get('groups');
        
        if($query)
        {
            return $query->row();
        }
    }
   
  public function get_user_country($lang_id ,$country_id)
    {
        
        $this->db->select('name as country_name ');
        
        $this->db->where('user_nationality_id',$country_id);
        $this->db->where('lang_id',$lang_id);
        
        $query = $this->db->get('user_nationality_translation');
        
        if($query)
        {
            return $query->row();
        }
    }
    
    public function get_user_region($lang_id ,$city_id)
    {
        
        $this->db->select('name as city_name ');
        
        $this->db->where('city_id',$city_id);
        $this->db->where('lang_id',$lang_id);
        
        $query = $this->db->get('cities_translation');
        
        if($query)
        {
            return $query->row();
        }
    }
    
    public function get_user_bank_accounts($lang_id ,$user_id)
    {
        
        $this->db->select('bank_accounts_translation.bank as bankName , user_bank_accounts .*');
        
        $this->db->join('bank_accounts_translation','user_bank_accounts.bank_id = bank_accounts_translation.bank_account_id');
        //$this->db->join('user_bank_accounts','groups.id = users_groups.group_id');
        
        $this->db->where('user_bank_accounts.user_id',$user_id);
        $this->db->where('bank_accounts_translation.lang_id',$lang_id);
        
        $query = $this->db->get('user_bank_accounts');
        
        if($query)
        {
            return $query->result();
        }
    } 
    
   
    
    public function get_bank_accounts_result($display_lang_id, $user_id)
    {
        $this->db->select('bank_accounts.*, bank_accounts_translation.*, user_bank_accounts.*, bank_accounts.account_name as bank_account_name , bank_accounts.account_number as bank_account_number , bank_accounts.id as id, user_bank_accounts.id as user_bank_account_id, user_bank_accounts.account_name as user_bank_account_name, user_bank_accounts.account_number as user_bank_account_number');
        
        $this->db->join('user_bank_accounts ', 'bank_accounts.id = user_bank_accounts.bank_id and user_bank_accounts.user_id ='.$user_id, 'left');
        $this->db->join('bank_accounts_translation', 'bank_accounts.id = bank_accounts_translation.bank_account_id');
        
        //$this->db->where('user_bank_accounts.user_id', $user_id);//AS default_bank_accounts
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
    
    public function get_cart_id($user_id, $session_id='', $ip_address='')
    {
        if($user_id == 0)
        {
            $this->db->where('ip_address', $ip_address);
            $this->db->where('session_id', $session_id);
        }
        
        $this->db->where('user_id', $user_id);
        
        $query = $this->db->get('shopping_cart');
        
        if($query)
        {
            return $query->row()->id;
        }
        else
        {
            return false;
        }
    }
    public function get_cart_id_by_device($device_id)
    {
        
        
        $this->db->where('session_id', $device_id);
       
        $query = $this->db->get('shopping_cart');
        
        if($query)
        {
            return $query->row()->id;
        }
        else
        {
            return false;
        }
    }
    
    public function update_user_login_auth_status($email ,$data)
    {
        $this->db->where('email',$email);
        
        $this->db->update('users' ,$data);
        
        return true;
    }
/****************************************************************/
}