<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Update_profile_data_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    
   /**********************GET*******************************/
    
    public function get_user_bank_accounts($user_id)
    {
        
        //$this->db->select('bank_accounts_translation.bank as bankName , user_bank_accounts .*');
        
        //$this->db->join('bank_accounts_translation','user_bank_accounts.bank_id = bank_accounts_translation.bank_account_id');
        //$this->db->join('user_bank_accounts','groups.id = users_groups.group_id');
        
        $this->db->where('user_id',$user_id);
        //$this->db->where('bank_accounts_translation.lang_id',$lang_id);
        
        $query = $this->db->get('user_bank_accounts');
        
        if($query)
        {
            return $query->result();
        }
    }
   
  public function update_user_bank_accounts($userId,$bankId ,$user_bank_accounts_data)
  {
        $this->db->where('user_id',$userId);
        $this->db->where('bank_id',$bankId);
        
        $this->db->update('user_bank_accounts',$user_bank_accounts_data);
        
        return true;
  }
    
/****************************************************************/
}