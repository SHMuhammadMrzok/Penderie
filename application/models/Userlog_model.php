<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Userlog_model extends CI_Model
{
    
    public function get_user_action_id($method)
    {
        $this->db->where('method',$method);
        
        $row = $this->db->get('userlog_actions')->row();
        
        if ($row)
        {
            return $row->id;
        }else{
            return false;
        }
    }
    
    public function check_user_action_exists($method)
    {
        $this->db->where('method',$method);
        $this->db->where('active',1);
        
        $count = $this->db->count_all_results('userlog_actions');
        
        if ($count >0)
        {
            return true;
        }else{
            return false;
        }
    }
    
    public function insert_user_log($data)
    {
        $this->db->insert('userlog',$data);
    }
////////////////////////////
}
