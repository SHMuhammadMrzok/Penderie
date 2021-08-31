<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Permission_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    function get_permission_row($id)
    {
        $this->db->order_by('id','asc');
        $this->db->where('controller_id',$id);
        return $this->db->get('permissions')->row_array();
    }
    
    public function get_permission_controller($controller_id)
    {
         $this->db->where('controller_id',$controller_id);
         $this->db->where('lang_id',1);
         
         $row = $this->db->get('controllers_translation')->row();
         
         if($row)
         {
            return $row;
         }else{
            return false;
         }
    } 
    
    public function get_permission_method($method_id)
    {
         $this->db->where('method_id',$method_id);
         $this->db->where('lang_id',1);
         
         $row = $this->db->get('methods_translation')->row();
         
         if($row)
         {
            return $row;
         }else{
            return false;
         }
    } 
    
    public function delete_permissions_translation($permission_id)
    {
        $this->db->where('permission_id',$permission_id);
        $this->db->delete('permissions_translation'); 
    }
    
    public function insert_permissions($data)
    {
        return $this->db->insert('permissions', $data);
    }
    
    public function insert_permissions_translation($permissions_translation_data)
    {
        return $this->db->insert('permissions_translation', $permissions_translation_data);
    }
    
    public function get_permissions_translation_result($id)
    {
        $this->db->select('permissions_translation.*');
        $this->db->join('permissions_translation','permissions.id = permissions_translation.permission_id');
        $this->db->where('permissions.id',$id);
        $query = $this->db->get('permissions');
        
        if($query)
        {
            return $query->result();
        }
    }
    
    public function get_permissions_result($id)
    {
        $this->db->where('id',$id);
        $query = $this->db->get('permissions');
        
        if($query)
        {
            return $query->row();
        }
    }
    
    public function update_permissions_translation($permission_id,$lang_id,$permissions_translation_data)
    {
        $this->db->where('permission_id',$permission_id);
        $this->db->where('lang_id',$lang_id);
        $this->db->update('permissions_translation',$permissions_translation_data);
    }
    
    public function update_permissions($permission_id,$permissions_data)
    {
        $this->db->where('id',$permission_id);
        $this->db->update('permissions',$permissions_data);
    }
    
    public function get_permissions_data($lang_id,$limit,$offset,$search_word='',$order_by,$order_state,$modules_filter_id=0,$controllers_filter_id=0)
    {
        $this->db->select('permissions.*, permissions_translation.name, methods_translation.name, controllers_translation.name, modules_translation.name, permissions.id as id, permissions_translation.name as permission, methods_translation.name as method, controllers_translation.name as controller, modules_translation.name as module');
        
        $this->db->join('permissions_translation' , 'permissions.id = permissions_translation.permission_id');
        $this->db->join('methods_translation' , 'permissions.method_id = methods_translation.method_id');
        $this->db->join('controllers_translation' , 'permissions.controller_id = controllers_translation.controller_id');
        $this->db->join('modules_translation' , 'permissions.module_id = modules_translation.module_id');
        
        $this->db->where('permissions_translation.lang_id',$lang_id);
        $this->db->where('methods_translation.lang_id',$lang_id);
        $this->db->where('controllers_translation.lang_id',$lang_id);
        $this->db->where('modules_translation.lang_id',$lang_id);
        
        if(trim($search_word) !='')
        {
            $this->db->like('permissions_translation.name', $search_word, 'both');
        }
        
        if($order_by != '')
        {
            if($order_by == lang('module'))
            {
                $this->db->order_by('modules_translation.name',$order_state);
            }
            elseif($order_by == lang('controller'))
            {
                $this->db->order_by('controllers_translation.name',$order_state);
            }
            elseif($order_by == lang('method'))
            {
                $this->db->order_by('methods_translation.name',$order_state);
            }
            else
            {
                $this->db->order_by('permissions.id',$order_state);
            }
        }
        else
        {
            $this->db->order_by('permissions.id',$order_state);
        }
        
        if($modules_filter_id !=0)
        {
            $this->db->where('permissions.module_id', $modules_filter_id);
        }
        
        if($controllers_filter_id !=0)
        {
            $this->db->where('permissions.controller_id', $controllers_filter_id);
        }
        
        $result = $this->db->get('permissions',$limit,$offset);

        if($result)
        {
            return $result->result();    
        }
     
    }
    
    public function get_count_all_permissions($lang_id,$search_word='',$modules_filter_id=0,$controllers_filter_id=0)
    {
        $this->db->join('permissions_translation' , 'permissions.id = permissions_translation.permission_id');
        
        if(trim($search_word) !='')
        {
            $this->db->like('permissions_translation.name', $search_word, 'both'); 
        }
        
        if($modules_filter_id !=0)
        {
            $this->db->where('permissions.module_id', $modules_filter_id);
        }
        
        if($controllers_filter_id !=0)
        {
            $this->db->where('permissions.controller_id', $controllers_filter_id);
        }
        
        $this->db->where('permissions_translation.lang_id',$lang_id);
        
        return $this->db->count_all_results('permissions');
    }
    
    public function delete_permissions_data($ids_array)
    {
        $this->db->where_in('permission_id',$ids_array);
        $this->db->delete('permissions_translation');
        
        $this->db->where_in('id',$ids_array);
        $this->db->delete('permissions');
        
        echo '1';
    }
    
    public function get_row_data($id,$display_lang_id)
    {
        $this->db->select('permissions.*, permissions_translation.name, methods_translation.name, controllers_translation.name, modules_translation.name, permissions.id as id, permissions_translation.name as permission, methods_translation.name as method, controllers_translation.name as controller, modules_translation.name as module');
        
        $this->db->join('permissions_translation' , 'permissions.id = permissions_translation.permission_id');
        $this->db->join('methods_translation' , 'permissions.method_id = methods_translation.method_id');
        $this->db->join('controllers_translation' , 'permissions.controller_id = controllers_translation.controller_id');
        $this->db->join('modules_translation' , 'permissions.module_id = modules_translation.module_id');
        
        $this->db->where('permissions.id',$id);
        $this->db->where('permissions_translation.lang_id',$display_lang_id);
        $this->db->where('methods_translation.lang_id',$display_lang_id);
        $this->db->where('controllers_translation.lang_id',$display_lang_id);
        $this->db->where('modules_translation.lang_id',$display_lang_id);
        
        $result = $this->db->get('permissions');

        if($result)
        {
            return $result->row();    
        }
        
    }
    
    public function check_permission_count($module_id,$controller_id,$method_id)
    {
        $this->db->where('module_id',$module_id);
        $this->db->where('controller_id',$controller_id);
        $this->db->where('method_id',$method_id);
        
        $count = $this->db->count_all_results('permissions');
        return $count;
    }
    
   /****************************************************************************/
}