<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Bank_accounts_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    
   /**********************GET*******************************/
    
    public function get_bank_accounts($lang_id)
    {
        
        $this->db->select('bank_accounts.*, bank_accounts_translation.*');
        $this->db->join('bank_accounts_translation', 'bank_accounts.id = bank_accounts_translation.bank_account_id'); 
       
        $this->db->where('bank_accounts_translation.lang_id', $lang_id);
         
        $result = $this->db->get('bank_accounts');

        if($result)
        {
            return $result->result();    
        }
    }
   
  
    public function get_bank_accounts_result($display_lang_id, $user_id=0)
    {
        $this->db->select('bank_accounts.*,bank_accounts.account_name as bank_account_name , 
                           bank_accounts.account_number as bank_account_number , bank_accounts_translation.*, 
                           user_bank_accounts.*, bank_accounts.id as id, user_bank_accounts.id as user_bank_account_id, 
                           user_bank_accounts.account_name as user_bank_account_name, user_bank_accounts.account_number as user_bank_account_number');
        
        $this->db->join('user_bank_accounts ', 'bank_accounts.id = user_bank_accounts.bank_id and user_bank_accounts.user_id ='.$user_id, 'left');
        $this->db->join('bank_accounts_translation', 'bank_accounts.id = bank_accounts_translation.bank_account_id');
        
        
        $this->db->where('bank_accounts_translation.lang_id', $display_lang_id);
        $this->db->where('bank_accounts.active', 1);
        
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
    
/****************************************************************/
}