<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Permissions_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }
  
    public function get_permissions($controller_id, $lang_id)
    {
        /*$this->db->where('controller_id', $controller_id);
        return $this->db->get('permissions')->result();*/
        
        $this->db->select('permissions_translation.*,permissions.*');
        $this->db->join('permissions_translation','permissions.id = permissions_translation.permission_id');
        
        $this->db->where('permissions.controller_id', $controller_id);
        $this->db->where('permissions_translation.lang_id', $lang_id);
        
        $query = $this->db->get('permissions');
        if($query)
        {
            return $query->result();
        }
    }
    
 /**************************** user permissions ************************************/   
    public function get_user_permissions($user_id)
    {
        $this->db->where('user_id', $user_id);
        return $this->db->get('permissions_users')->result();
    }
    
    public function delete_user_permissions($user_id)
    {
        $this->db->where('user_id', $user_id);
        $this->db->delete('permissions_users');
    }
    
    public function save_user_permissions($user_id, $controller_id, $permission)
    {
        $data = array(
                    'permission_id' => $permission,
                    'user_id'       => $user_id,
                    'controller_id' => $controller_id
        );
        
        $this->db->insert('permissions_users', $data); 
    }
    
    

 /******************************group permissions ******************************/
    public function get_groups_permissions($groups_ids)
    {
        $this->db->where_in('group_id', $groups_ids);
        return $this->db->get('permissions_groups')->result();
    }
    
    public function delete_group_permissions($group_id)
    {
        $this->db->where('group_id',$group_id);
        $this->db->delete('permissions_groups'); 
    }
    
    public function save_group_permissions($group_id,$controller_id,$permission)
    {
        $data = array(
                    'permission_id' => $permission,
                    'group_id'      => $group_id,
                    'controller_id' => $controller_id
        );
        
        $this->db->insert('permissions_groups', $data); 
    }
/*******************************************************/   

    public function get_permission_row($id)
    {
        $this->db->order_by('id','asc');
        $this->db->where('controller_id',$id);
        
        return $this->db->get('permissions')->row_array();
    }
    
    /*************************************************************************/
    
    public function get_user_stores_permissions($user_id, $store_id)
    {
        $this->db->where_in('user_id', $user_id);
        $this->db->where_in('store_id', $store_id);
        
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
    
    public function delete_user_store_permissions($user_id)
    {
        $this->db->where('user_id', $user_id);
        return $this->db->delete('user_stores_permissions');
    }
    
    public function save_user_store_permissions($user_store_data)
    {
        return $this->db->insert('user_stores_permissions', $user_store_data);
    }
    
    public function check_method_has_store_permissions($method_id)
    {
        $this->db->join('modules', 'methods.module_id = modules.id AND modules.store_related = 1');
        $this->db->where('methods.id', $method_id);
        
        $count = $this->db->count_all_results('methods');
        
        if($count > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    public function get_method_data($method_id)
    {
        $this->db->select('methods.*, modules.*');
        $this->db->join('modules', 'methods.module_id = modules.id');
        
        $this->db->where('methods.id', $method_id);
        
        $query = $this->db->get('methods');
        
        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }
    
}