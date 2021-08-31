<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class methods_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
  
   
   public function get_method_id($method,$controller_id,$module_id)
   {
        $this->db->where('module_id',$module_id);
        $this->db->where('controller_id',$controller_id);
        $this->db->where('method',$method);
        
        $row=$this->db->get('methods')->row();
        if($row)
        {
            return $row->id;
        }else{
            return false;
        }
   }
   
   public function get_method($method_id,$lang_id)
   {
        $this->db->join('methods_translation','methods.id=methods_translation.method_id');
        $this->db->where('id',$method_id);
        $this->db->where('methods_translation.lang_id',$lang_id);
         
        $row=$this->db->get('methods')->row();
        
        if($row)
        {
            return $row;
        }else{
            return false;
        }
   }
  
  public function get_controller_methods($controller_id,$lang_id)
  {
        $this->db->select('methods_translation.name,methods.*');
        $this->db->join('methods_translation','methods.id=methods_translation.method_id');
        
        $this->db->where('methods.controller_id',$controller_id);
        $this->db->where('methods_translation.lang_id',$lang_id);
        
        $result=$this->db->get('methods')->result();
        
        if($result)
        {
            return $result;
        }else{
            return false;
        }
  }
  
  public function get_controller_nonexist_methods($controller_id)
  {
        $this->db->where('controller_id',$controller_id);
        $query=$this->db->get('methods');
         
        if($query)
        {
           $filtered_result=array();
          
          foreach($query->result() as $row )
          {
            $this->db->where('method_id',$row->id);
            if($this->db->count_all_results('permissions')== 0)
            {
                $filtered_result[]=$row;
            }
          }  
            return $filtered_result;
        }else{
            return false;
        }
  }
   /////////////////////////////////////////////////////////
    public function delete_methods_translation($method_id)
    {
        $this->db->where('method_id',$method_id);
        $this->db->delete('methods_translation'); 
    }
    
    public function insert_methods($data)
    {
        return $this->db->insert('methods', $data);
    }
    
    public function insert_methods_translation($methods_translation_data)
    {
        return $this->db->insert('methods_translation', $methods_translation_data);
    }
    
    public function get_methods_translation_result($id)
    {
        $this->db->select('methods_translation.*');
        $this->db->join('methods_translation','methods.id = methods_translation.method_id');
        $this->db->where('methods.id',$id);
        $query = $this->db->get('methods');
        
        if($query)
        {
            return $query->result();
        }
    }
    
    public function get_methods_result($id)
    {
        $this->db->where('id',$id);
        $query = $this->db->get('methods');
        
        if($query)
        {
            return $query->row();
        }
    }
    
    public function update_methods_translation($method_id,$lang_id,$methods_translation_data)
    {
        $this->db->where('method_id',$method_id);
        $this->db->where('lang_id',$lang_id);
        $this->db->update('methods_translation',$methods_translation_data);
    }
    
    public function update_methods($method_id,$methods_data)
    {
        $this->db->where('id',$method_id);
        $this->db->update('methods',$methods_data);
    }
    
    public function get_methods_data($lang_id,$limit,$offset,$search_word='',$order_by,$order_state,$modules_filter_id=0,$controllers_filter_id=0)
    {
        $this->db->select('methods.* , methods_translation.* ,controllers_translation.name, modules_translation.name, methods.id as id, methods_translation.name as method, controllers_translation.name as controller, modules_translation.name as module');
        
        $this->db->join('methods_translation' , 'methods.id = methods_translation.method_id');
        $this->db->join('controllers_translation' , 'methods.controller_id = controllers_translation.controller_id');
        $this->db->join('modules_translation' , 'methods.module_id = modules_translation.module_id');
        
        $this->db->where('methods_translation.lang_id',$lang_id);
        $this->db->where('controllers_translation.lang_id',$lang_id);
        $this->db->where('modules_translation.lang_id',$lang_id);
        
        if(trim($search_word) !='')
        {
            $this->db->like('methods_translation.name', $search_word, 'both');
            $this->db->or_like('controllers_translation.name', $search_word, 'both');
            $this->db->or_like('modules_translation.name', $search_word, 'both');
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
            elseif($order_by == lang('active'))
            {
                $this->db->order_by('methods.active',$order_state);
            }
            else
            {
                $this->db->order_by('methods.id',$order_state);
            }
            
        }
        else
        {
            $this->db->order_by('methods.id',$order_state);
        }
        
        if($modules_filter_id !=0)
        {
            $this->db->where('methods.module_id', $modules_filter_id);
        }
        
        if($controllers_filter_id !=0)
        {
            $this->db->where('methods.controller_id', $controllers_filter_id);
        }
        
        $result = $this->db->get('methods',$limit,$offset);

        if($result)
        {
            return $result->result();    
        }
     
    }
    
    public function get_count_all_methods($lang_id,$search_word='',$modules_filter_id=0,$controllers_filter_id=0)
    {
        $this->db->join('methods_translation' ,'methods.id = methods_translation.method_id');
        
        if(trim($search_word) !='')
        {
            $this->db->like('methods_translation.name', $search_word, 'both'); 
        }
        
        if($modules_filter_id !=0)
        {
            $this->db->where('methods.module_id', $modules_filter_id);
        }
        
        if($controllers_filter_id !=0)
        {
            $this->db->where('methods.controller_id', $controllers_filter_id);
        }
        
        $this->db->where('methods_translation.lang_id',$lang_id);
        
        return $this->db->count_all_results('methods');
    }
    
    public function delete_method_data($ids_array)
    {
        $this->db->where_in('method_id',$ids_array);
        $this->db->delete('methods_translation');
        
        $this->db->where_in('id',$ids_array);
        $this->db->delete('methods');
        
        echo '1';
    }
    
    public function get_methods_filter_data($lang_id)
    {
        $this->db->select('methods.*, methods_translation.name, methods.id as id ');
        $this->db->join('methods_translation' , 'methods.id = methods_translation.method_id');
        
        $this->db->where('methods_translation.lang_id',$lang_id);
        
        $query = $this->db->get('methods');
        
        if($query)
        {
            return $query->result();
        }
    }
    
    public function get_row_data($id,$display_lang_id)
    {
        $this->db->select('methods.* , methods_translation.* ,controllers_translation.name, modules_translation.name, methods.id as id, methods_translation.name as method, controllers_translation.name as controller, modules_translation.name as module');
        
        $this->db->join('methods_translation' , 'methods.id = methods_translation.method_id');
        $this->db->join('controllers_translation' , 'methods.controller_id = controllers_translation.controller_id');
        $this->db->join('modules_translation' , 'methods.module_id = modules_translation.module_id');
        
        $this->db->where('methods.id',$id);
        $this->db->where('methods_translation.lang_id',$display_lang_id);
        $this->db->where('controllers_translation.lang_id',$display_lang_id);
        $this->db->where('modules_translation.lang_id',$display_lang_id);
        
        
        
        $result = $this->db->get('methods');

        if($result)
        {
            return $result->row();    
        }
    }
    
    public function check_methods_count($module_id,$controller_id,$method)
    {
        $this->db->where('module_id'     , $module_id);
        $this->db->where('controller_id' , $controller_id);
        $this->db->where('method'        , $method);
        
        $count = $this->db->count_all_results('methods');
        return $count;
    }
    
    public function get_a_controller_method_id($controller_id, $method)
    {
        $this->db->where('controller_id', $controller_id);
        $this->db->where('method', $method);
        
        $query = $this->db->get('methods');
        
        if($query->row())
        {
            return $query->row()->id;
        }
        else
        {
            return false;
        }
    }
    /***************************************************************************/
}