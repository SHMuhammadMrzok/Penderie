<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Get_profile_data_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    
   /**********************GET*******************************/
    
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
   
  public function get_user_country($lang_id ,$country_id)
    {
        
        $this->db->select('name as country_name');
        
        $this->db->where('country_id',$country_id);
        $this->db->where('lang_id',$lang_id);
        
        $query = $this->db->get('countries_translation');
        
        if($query)
        {
            return $query->row();
        }
    }
    
    
/****************************************************************/
}