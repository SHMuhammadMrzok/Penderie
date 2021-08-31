<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
/**
 * 
 */
class ACL
{
    public $CI ;
    
    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->model('acl_model');
        $this->CI->load->model('users/user_model');
        $this->CI->load->model('users/permissions_model');
    }
    
    public function has_permission($user_id, $method_id)
    {
        if($user_id == '')
        {
            return false;
        }
        
        if(!$method_id)
        {
            return true;
        }
        
        $permission_id = $this->CI->acl_model->get_permission_id($method_id);
        
        //-->> check if this method included in permissions
        if($permission_id)
        {
            //check method in stores permissions 
            $method_store_check = $this->CI->permissions_model->check_method_has_store_permissions($method_id);
            
            if($method_store_check)
            {
                $check_user_permissions = false;
                
                //check if user has stores
                $user_stores_count = $this->CI->user_model->user_stores_count($user_id);
                
                if($user_stores_count > 0 )
                {
                    $user_permission_exist  = $this->CI->acl_model->user_store_permission_exist($user_id, $permission_id);
                    $check_user_permissions = false;
                }
                else
                {
                    $check_user_permissions = true;
                }
            }
            else
            {
                $check_user_permissions = true;
            } 
            
            
            
            if($check_user_permissions)
            {
                $user_permissions_count = $this->CI->acl_model->get_user_permissions_count($user_id);
                
    			if($user_permissions_count == 0)
                {
                    $user_groups        = $this->CI->ion_auth->get_users_groups($user_id)->result();
                    $user_groups_ids    = array();
                    
                    foreach($user_groups as $user_group)
                    {
                        $user_groups_ids[] = $user_group->id;
                    }
                    
                    $user_permission_exist = $this->CI->acl_model->groups_permission_exist($user_groups_ids, $permission_id);   
                }
                else
                {
                    $user_permission_exist = $this->CI->acl_model->user_permission_exist($user_id, $permission_id);    
                }
            }
        }
        else
        {
            return true;
        }
        
       return $user_permission_exist;
    }
    
    public function get_user_permissions($user_id)
    {
        //echo $user_id; die();
        
        $user_permissions_count = $this->CI->acl_model->get_user_permissions_count($user_id);

        if($user_permissions_count == 0)
        {  
            $user_groups        = $this->CI->user_model->get_user_groups(2, $user_id);
            //print_r($user_groups); die();
            $user_groups_ids    = array();
            
            foreach($user_groups as $user_group)
            {
                $user_groups_ids[] = $user_group->group_id;
            }
            
            $user_permissions = $this->CI->acl_model->get_groups_permitted_methods($user_groups_ids);     
        }
        else
        {
            $user_permissions = $this->CI->acl_model->get_user_permitted_methods($user_id);    
        }
        //echo '<pre>';print_r($user_permissions);die();
        return $user_permissions;
    }
    
    public function get_user_permissions_count($user_id)
    {
        return $this->CI->acl_model->get_user_permissions_count($user_id);
    }
    
    public function check_user_store_method($user_id, $permission_id)
    {
        //check if user has stores
        $user_stores_count = $this->CI->user_model->user_stores_count($user_id);
        
        if($user_stores_count > 0 )
        {
            $user_permission_exist  = $this->CI->acl_model->user_store_permission_exist($user_id, $permission_id);
        }
        else
        {
            $user_permission_exist = true;
        }
        
        return $user_permission_exist;
    }
}
?>