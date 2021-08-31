<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
*
*
*/

class Acl_model extends CI_Model
{
    // permissions
    
    public function get_permission_id($method_id)
    {
        $this->db->where('method_id', $method_id);
        
        $row = $this->db->get('permissions')->row();
        
        if($row)
        {
            return $row->id;
        }
        else
        {
            return false;
        }
    }
    
    //**********************User Permissions**********************************//
    
    public function get_user_permitted_methods($user_id)
    {
        $this->db->select('permissions_users.controller_id, methods.method, methods.id as method_id, modules.store_related, permissions.id as permission_id');
        
        $this->db->join('permissions', 'permissions_users.permission_id = permissions.id');
        $this->db->join('controllers', 'controllers.id = permissions.controller_id');
        $this->db->join('methods', 'methods.id = permissions.method_id');
        $this->db->join('modules', 'methods.module_id = methods.module_id');
        
        $this->db->where('permissions_users.user_id', $user_id);
        
        return $this->db->get('permissions_users')->result();
    }
    
    public function get_user_permissions_count($user_id)
    {
         $this->db->where('user_id', $user_id);
         return $this->db->count_all_results('permissions_users');
    }
    
    public function user_permission_exist($user_id, $permission_id)
    {
        $this->db->where('permission_id',$permission_id);
        $this->db->where('user_id',$user_id);
        
        $user_permission_count=$this->db->count_all_results('permissions_users');
        
        if($user_permission_count > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    public function get_user_permissions($user_id)
    {
        $this->db->where('user_id', $user_id);
        return $this->db->get('permissions_users')->result();
    }
    
    public function groups_permission_exist($groups_ids, $permission_id)
    {
        $this->db->where('permission_id', $permission_id);
        $this->db->where_in('group_id', $groups_ids);
        
        $group_permission_count = $this->db->count_all_results('permissions_groups');
        
        if($group_permission_count > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    public function get_groups_permitted_methods($group_ids)
    {
        $this->db->select('permissions_groups.controller_id, methods.method, methods.id as method_id, permissions.id as permission_id');
        
        $this->db->join('permissions', 'permissions_groups.permission_id=permissions.id');
        $this->db->join('controllers', 'controllers.id=permissions.controller_id');
        $this->db->join('methods', 'methods.id=permissions.method_id');
        
        $this->db->where_in('permissions_groups.group_id', $group_ids);
        
        $result = $this->db->get('permissions_groups')->result();
        
        $permissions = array_map("unserialize", array_unique(array_map("serialize", $result)));
        
        return $permissions;
    }
    
    public function check_add_method($module, $controller, $method)
    {
        if($method == 'index')
        {
            $module_where     = array('module'=>$module);
            $module_id        = $this->get_id('modules', $module_where);
            
            $controller_where = array('module_id'=>$module_id, 'controller'=>$controller);
            $controller_id    = $this->get_id('controllers', $controller_where);
            
            $method_where     = array('module_id'=>$module_id, 'controller_id'=>$controller_id, 'method'=>'add'); 
            $method_id        = $this->get_id('methods', $method_where);
            
            if($method_id)
            {
                
                $this->db->where('module_id',$module_id);
                $this->db->where('controller_id',$controller_id);
                $this->db->where('method_id',$method_id);
                
                $count = $this->db->count_all_results('permissions');
                
                if($count > 0)
                {
                    return true;
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }
    
    public function check_add_permission($module, $controller)
    {
        $module_where     = array('module'=>$module);
        $module_id        = $this->get_id('modules', $module_where);
        
        $controller_where = array('module_id'=>$module_id, 'controller'=>$controller);
        $controller_id    = $this->get_id('controllers', $controller_where);
        
        $method_where     = array('module_id'=>$module_id, 'controller_id'=>$controller_id, 'method'=>'add'); 
        $method_id        = $this->get_id('methods', $method_where);
        
        $this->db->where('module_id', $module_id);
        $this->db->where('controller_id', $controller_id);
        $this->db->where('method_id', $method_id);
        
        $count = $this->db->count_all_results('permissions');
        
        if($count > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
        
    }
    
    public function get_id($table, $where)
    {
        foreach($where as $column => $value)
        {
            $this->db->where($column,$value);
        }
        
        $query = $this->db->get($table)->row();
        
        if($query)
        {
            return $query->id;
        }
        else
        {
            return false;
        }
        
    }
    
    public function user_store_permission_exist($user_id, $permission_id)
    {
        $this->db->where('permission_id', $permission_id);
        $this->db->where_in('user_id', $user_id);
        
        $user_store_permission_count = $this->db->count_all_results('user_stores_permissions');
        
        if($user_store_permission_count > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    
    
    
////////////////////////////
}
