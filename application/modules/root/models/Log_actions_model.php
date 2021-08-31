<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Log_actions_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
   public function get_action($action_id)
   {
        $this->db->where('id',$action_id);
        $row = $this->db->get('userlog_actions')->row();
        
        if($row)
        {
            return $row;
        }
        else
        {
            return false;
        }
   }
  
  public function get_action_by_id($action_id,$lang_id)
  {
        $this->db->where('userlog_actions_id',$action_id);
        $this->db->where('lang_id',$lang_id);
        $query=$this->db->get('userlog_actions_translation');
        
        if($query)
        {
            return $query->row();
        }
  }
  
  public function delete_userlog_actions_translation($userlog_actions_id)
    {
        $this->db->where('userlog_actions_id',$userlog_actions_id);
        $this->db->delete('userlog_actions_translation'); 
    }
    
    public function insert_userlog_actions($data)
    {
        return $this->db->insert('userlog_actions', $data);
    }
    
    public function insert_userlog_actions_translation($userlog_actions_translation_data)
    {
        return $this->db->insert('userlog_actions_translation', $userlog_actions_translation_data);
    }
    
    public function get_userlog_actions_translation_result($id)
    {
        $this->db->select('userlog_actions_translation.*');
        $this->db->join('userlog_actions_translation','userlog_actions.id = userlog_actions_translation.userlog_actions_id');
        $this->db->where('userlog_actions.id',$id);
        $query = $this->db->get('userlog_actions');
        
        if($query)
        {
            return $query->result();
        }
    }
    
    public function get_userlog_actions_result($id)
    {
        $this->db->where('id',$id);
        $query = $this->db->get('userlog_actions');
        
        if($query)
        {
            return $query->row();
        }
    }
    
    public function update_userlog_actions_translation($userlog_actions_id,$lang_id,$userlog_actions_translation_data)
    {
        $this->db->where('userlog_actions_id',$userlog_actions_id);
        $this->db->where('lang_id',$lang_id);
        $this->db->update('userlog_actions_translation',$userlog_actions_translation_data);
    }
    
    public function update_userlog_actions($userlog_actions_id,$userlog_actions_data)
    {
        $this->db->where('id',$userlog_actions_id);
        $this->db->update('userlog_actions',$userlog_actions_data);
    }
    
    
    
    
    public function get_users_log_data($lang_id,$limit,$offset,$search_word='',$order_by='',$order_state,$users_filter_id=0,$actions_filter_id=0,$modules_filter_id=0,$controllers_filter_id=0)
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
                $this->db->order_by('countries.id',$order_state);
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
        
        $result = $this->db->get('userlog',$limit,$offset);

        if($result)
        {
            return $result->result();    
        }
        else
        {
            return false;
        }
    }
    
    public function get_count_all_users_log($lang_id ,$search_word ='',$users_filter_id=0,$actions_filter_id=0,$modules_filter_id=0,$controllers_filter_id=0)
    {
        if(trim($search_word) !='')
        {
            $this->db->join('userlog_actions_translation','userlog.action_id = userlog_actions_translation.userlog_actions_id');
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
        
        return $this->db->count_all_results('userlog');
    }
    
    public function delete_user_log_data($user_log_id_array)
    {
        $this->db->where_in('id', $user_log_id_array);
        $this->db->delete('userlog');
        
        echo '1';
    }
    
    
    public function get_actions_data($lang_id,$limit,$offset,$search_word='',$order_by,$order_state)
    {
        $this->db->select('userlog_actions.*, userlog_actions_translation.name, userlog_actions.id as id, userlog_actions_translation.name as action_name');
        
        $this->db->join('userlog_actions_translation' , 'userlog_actions.id = userlog_actions_translation.userlog_actions_id');
        
        $this->db->where('userlog_actions_translation.lang_id',$lang_id);
        
        if(trim($search_word) !='')
        {
            $this->db->like('userlog_actions_translation.name', $search_word, 'both');
        }
        
        if($order_by != '')
        {
            if($order_by == lang('method'))
            {
                $this->db->order_by('userlog_actions_translation.name',$order_state);
            }
            elseif($order_by == lang('active'))
            {
                $this->db->order_by('userlog_actions.active',$order_state);
            }
            else
            {
                $this->db->order_by('userlog_actions.id',$order_state);
            }
        }
        else
        {
            $this->db->order_by('userlog_actions.id',$order_state);
        }
        
        $result = $this->db->get('userlog_actions',$limit,$offset);

        if($result)
        {
            return $result->result();    
        }
     
    }
    
    public function get_count_all_actions($lang_id,$search_word='')
    {
        $this->db->join('userlog_actions_translation' ,'userlog_actions.id = userlog_actions_translation.userlog_actions_id');
        
        if(trim($search_word) !='')
        {
            $this->db->like('userlog_actions_translation.name', $search_word, 'both'); 
        }
        
        $this->db->where('userlog_actions_translation.lang_id',$lang_id);
        
        return $this->db->count_all_results('userlog_actions');
    }
    
    public function delete_actions_data($ids_array)
    {
        $this->db->where_in('userlog_actions_id',$ids_array);
        $this->db->delete('userlog_actions_translation');
        
        $this->db->where_in('id',$ids_array);
        $this->db->delete('userlog_actions');
        
        echo '1';
    }
    
    public function get_log_actions_filter_data($lang_id)
    {
        $this->db->select('userlog_actions.*, userlog_actions_translation.name, userlog_actions.id as id ');
        $this->db->join('userlog_actions_translation' ,'userlog_actions.id = userlog_actions_translation.userlog_actions_id');
        
        $this->db->where('active',1);
        $this->db->where('userlog_actions_translation.lang_id',$lang_id);
        
        $query = $this->db->get('userlog_actions');
        
        if($query)
        {
            return $query->result();
        }
    }
    public function get_row_data($id,$display_lang_id)
    {   
        $this->db->select('userlog.*, userlog_actions_translation.*, users.username, controllers_translation.name, modules_translation.name ,controllers_translation.name as controller, modules_translation.name as module, userlog_actions_translation.name as action_name ');
        
        $this->db->join('userlog_actions_translation','userlog.action_id = userlog_actions_translation.userlog_actions_id');
        $this->db->join('users','userlog.user_id = users.id');
        $this->db->join('controllers_translation','userlog.controller_id = controllers_translation.controller_id');
        $this->db->join('modules_translation','userlog.module_id = modules_translation.module_id');
        
        $this->db->where('userlog.id',$id);
        $this->db->where('userlog_actions_translation.lang_id',$display_lang_id);
        $this->db->where('controllers_translation.lang_id',$display_lang_id);
        $this->db->where('modules_translation.lang_id',$display_lang_id);
        
        $result = $this->db->get('userlog');

        if($result)
        {
            return $result->row();    
        }
    }
    
    public function get_user_log_action_row($id,$display_lang_id)
    {
        $this->db->select('userlog_actions.*, userlog_actions_translation.name, userlog_actions.id as id, userlog_actions_translation.name as action_name');
        
        $this->db->join('userlog_actions_translation' , 'userlog_actions.id = userlog_actions_translation.userlog_actions_id');
        
        $this->db->where('userlog_actions.id',$id);
        $this->db->where('userlog_actions_translation.lang_id',$display_lang_id);
        
        $result = $this->db->get('userlog_actions');

        if($result)
        {
            return $result->row();    
        }
    }
    
   /****************************************************************************/  
}