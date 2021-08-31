<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Userslog_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }
  
    public function get_count_all_users_log($lang_id ,$search_word ='',$users_filter_id=0,$actions_filter_id=0,$modules_filter_id=0,$controllers_filter_id=0)
    {
        $this->db->join('userlog_actions_translation','userlog.action_id = userlog_actions_translation.userlog_actions_id');
        $this->db->join('users','userlog.user_id = users.id');
        $this->db->join('controllers_translation','userlog.controller_id = controllers_translation.controller_id');
        $this->db->join('modules_translation','userlog.module_id = modules_translation.module_id');
        
        if(trim($search_word) !='')
        {
            $this->db->like('userlog_actions_translation.name', $search_word, 'both');  
        }
        if($users_filter_id !=0)
        {
            $this->db->where('userlog.user_id', $users_filter_id);
        }
        
        if($actions_filter_id !=0)
        {
            $this->db->where('userlog.action_id', $actions_filter_id);
        }
        
        if($modules_filter_id !=0)
        {
            $this->db->where('userlog.module_id', $modules_filter_id);
        }
        
        if($controllers_filter_id !=0)
        {
            $this->db->where('userlog.controller_id', $controllers_filter_id);
        }
        
        $this->db->where('userlog_actions_translation.lang_id',$lang_id);
        $this->db->where('controllers_translation.lang_id',$lang_id);
        $this->db->where('modules_translation.lang_id',$lang_id);
        
        return $this->db->count_all_results('userlog');
    }
    
    public function get_users_log_data($lang_id,$limit,$offset=0,$search_word='',$order_by='userlog.id',$order_state='desc',$users_filter_id=0,$actions_filter_id=0,$modules_filter_id=0,$controllers_filter_id=0)
    {
        $this->db->select('userlog.*, userlog_actions_translation.*, users.username, controllers_translation.name, modules_translation.name ,controllers_translation.name as controller, modules_translation.name as module, userlog_actions_translation.name as action_name ');
        
        $this->db->join('userlog_actions_translation','userlog.action_id = userlog_actions_translation.userlog_actions_id');
        $this->db->join('users','userlog.user_id = users.id');
        $this->db->join('controllers_translation','userlog.controller_id = controllers_translation.controller_id');
        $this->db->join('modules_translation','userlog.module_id = modules_translation.module_id');
        
        if(trim($search_word) !='')
        {
            $this->db->like('userlog_actions_translation.name', $search_word, 'both');  
        }
        
        if($order_by != '')
        {
            if($order_by == lang('username'))
            {
                $this->db->order_by('users.username',$order_state);
            }
            elseif($order_by == lang('action'))
            {
                $this->db->order_by('userlog_actions_translation.name',$order_state);
            }
            elseif($order_by == lang('module'))
            {
                $this->db->order_by('modules_translation.name',$order_state);
            }
            elseif($order_by == lang('controller'))
            {
                $this->db->order_by('controllers_translation.name',$order_state);
            }
            elseif($order_by == lang('ip_address'))
            {
                $this->db->order_by('userlog.ip_address',$order_state);
            }
            else
            {
                $this->db->order_by('userlog.id',$order_state);
            }
        }
        else
        {
            $this->db->order_by('userlog.id',$order_state);
        }
        
        if($users_filter_id !=0)
        {
            $this->db->where('userlog.user_id', $users_filter_id);
        }
        
        if($actions_filter_id !=0)
        {
            $this->db->where('userlog.action_id', $actions_filter_id);
        }
        
        if($modules_filter_id !=0)
        {
            $this->db->where('userlog.module_id', $modules_filter_id);
        }
        
        if($controllers_filter_id !=0)
        {
            $this->db->where('userlog.controller_id', $controllers_filter_id);
        }
        
        $this->db->where('userlog_actions_translation.lang_id',$lang_id);
        $this->db->where('controllers_translation.lang_id',$lang_id);
        $this->db->where('modules_translation.lang_id',$lang_id);
        
        $result = $this->db->get('userlog', $limit, $offset)->result();

        if($result)
        {
            return $result;    
        }
        else
        {
            return false;
        }
    }
}