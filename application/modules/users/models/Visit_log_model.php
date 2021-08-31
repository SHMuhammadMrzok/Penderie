<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Visit_log_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    public function get_count_all_visits_log($lang_id, $search_word='', $user_filter_id=0, $modules_filter_id=0, $controllers_filter_id=0, $methods_filter_id=0)
    {
        $this->db->select('visits_log.*, users.username, modules_translation.name, controllers_translation.name, methods_translation.name, modules_translation.name as module, controllers_translation.name as controller, methods_translation.name as method');
        
        $this->db->join('users', 'visits_log.user_id = users.id');
        $this->db->join('modules_translation', 'visits_log.module_id = modules_translation.module_id');
        $this->db->join('controllers_translation', 'visits_log.controller_id = controllers_translation.controller_id');
        $this->db->join('methods_translation', 'visits_log.method_id = methods_translation.method_id');
        
        $this->db->where('modules_translation.lang_id', $lang_id);
        $this->db->where('controllers_translation.lang_id', $lang_id);
        $this->db->where('methods_translation.lang_id', $lang_id);
        
        
        if(trim($search_word) !='')
        {
            $this->db->like('modules_translation.name', $search_word, 'both');
            $this->db->or_like('controllers_translation.name', $search_word, 'both');
            $this->db->or_like('methods_translation.name', $search_word, 'both');
            $this->db->or_like('visits_log.url', $search_word, 'both');
            $this->db->or_like('visits_log.user_agent', $search_word, 'both');
        }
        
        if($user_filter_id != 0)
        {
            $this->db->where('visits_log.user_id', $user_filter_id);
        }
        if($modules_filter_id != 0)
        {
            $this->db->where('visits_log.module_id', $modules_filter_id);
        }
        if($controllers_filter_id != 0)
        {
            $this->db->where('visits_log.controller_id', $controllers_filter_id);
        }
        if($methods_filter_id != 0)
        {
            $this->db->where('visits_log.method_id', $methods_filter_id);
        }
        
        return $this->db->count_all_results('visits_log');
    }
    
    public function get_visits_log_data($lang_id, $limit, $offset, $search_word='', $order_by='visits_log.id', $order_state='desc', $user_filter_id=0, $modules_filter_id=0, $controllers_filter_id=0, $methods_filter_id=0)
    {
        $this->db->select('visits_log.*, users.username, modules_translation.name, controllers_translation.name, methods_translation.name, modules_translation.name as module, controllers_translation.name as controller, methods_translation.name as method');
        
        $this->db->join('users', 'visits_log.user_id = users.id');
        $this->db->join('modules_translation', 'visits_log.module_id = modules_translation.module_id');
        $this->db->join('controllers_translation', 'visits_log.controller_id = controllers_translation.controller_id');
        $this->db->join('methods_translation', 'visits_log.method_id = methods_translation.method_id');
        
        $this->db->where('modules_translation.lang_id', $lang_id);
        $this->db->where('controllers_translation.lang_id', $lang_id);
        $this->db->where('methods_translation.lang_id', $lang_id);
        
        if(trim($search_word) !='')
        {            
            $this->db->where('(modules_translation.name LIKE "%'.$search_word.'%" OR controllers_translation.name LIKE "%'.$search_word.'%"OR methods_translation.name LIKE "%'.$search_word.'%"OR visits_log.url LIKE "%'.$search_word.'%"OR visits_log.user_agent LIKE "%'.$search_word.'%")');
        }
        
        if($user_filter_id != 0)
        {
            $this->db->where('visits_log.user_id', $user_filter_id);
        }
        if($modules_filter_id != 0)
        {
            $this->db->where('visits_log.module_id', $modules_filter_id);
        }
        if($controllers_filter_id != 0)
        {
            $this->db->where('visits_log.controller_id', $controllers_filter_id);
        }
        if($methods_filter_id != 0)
        {
            $this->db->where('visits_log.method_id', $methods_filter_id);
        }
        
        if($order_by != '')
        {
            if($order_by == lang('username'))
            {
                $this->db->order_by('users.username', $order_state);
            }
            elseif($order_by == lang('admin'))
            {
                $this->db->order_by('visits_log.admin', $order_state);
            }
            elseif($order_by == lang('module'))
            {
                $this->db->order_by('modules_translation.name', $order_state);
            }
            elseif($order_by == lang('controller'))
            {
                $this->db->order_by('controllers_translation.name', $order_state);
            }
            elseif($order_by == lang('method'))
            {
                $this->db->order_by('methods_translation.name', $order_state);
            }
            elseif($order_by == lang('ip_address'))
            {
                $this->db->order_by('visits_log.ipaddress_long', $order_state);
            }
            else
            {
                $this->db->order_by('visits_log.id', $order_state);
            }
        }
        else
        {
            $this->db->order_by('visits_log.id', $order_state);
        }
        
        $result = $this->db->get('visits_log', $limit, $offset);
        
        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
        
    }
    
    public function get_row_data($id, $lang_id)
    {
        $this->db->select('visits_log.*, users.username, modules_translation.name, controllers_translation.name, methods_translation.name, modules_translation.name as module, controllers_translation.name as controller, methods_translation.name as method');
        
        $this->db->join('users', 'visits_log.user_id = users.id');
        $this->db->join('modules_translation', 'visits_log.module_id = modules_translation.module_id');
        $this->db->join('controllers_translation', 'visits_log.controller_id = controllers_translation.controller_id');
        $this->db->join('methods_translation', 'visits_log.method_id = methods_translation.method_id');
        
        $this->db->where('modules_translation.lang_id', $lang_id);
        $this->db->where('controllers_translation.lang_id', $lang_id);
        $this->db->where('methods_translation.lang_id', $lang_id);
        
        $this->db->where('visits_log.id', $id);
        
        $query = $this->db->get('visits_log');
        
        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }
    
    public function delete($ids_array)
    {
        $this->db->where_in('id', $ids_array);
        $this->db->delete('visits_log');
        
        echo '1';
    
    }
    
    
    
  ////////////////////////////////////////////////  
}