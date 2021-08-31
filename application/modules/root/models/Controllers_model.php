<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class controllers_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    function get_controllers($lang_id)
    {
         $this->db->select('controllers.*,controllers_translation.name as controller_name,modules_translation.name as module_name,modules.module,modules.id as module_id,modules.icon_class as module_icon_class');
         $this->db->join('modules','controllers.module_id=modules.id');
         $this->db->join('controllers_translation','controllers.id=controllers_translation.controller_id');
         $this->db->join('modules_translation','modules.id=modules_translation.module_id');
         

         $this->db->where('controllers_translation.lang_id',$lang_id);
         $this->db->where('modules_translation.lang_id',$lang_id);                  
         $this->db->where('modules.active',1);
         $this->db->where('controllers.active',1);
         
         $this->db->order_by('modules.sort','asc');
         $this->db->order_by('controllers.sort','asc');
         
         return $this->db->get('controllers')->result();   
    }
    
    public function get_store_controllers($lang_id)
    {
         $this->db->select('controllers.*,controllers_translation.name as controller_name,modules_translation.name as module_name,modules.module,modules.id as module_id,modules.icon_class as module_icon_class');
         $this->db->join('modules','controllers.module_id=modules.id');
         $this->db->join('controllers_translation','controllers.id=controllers_translation.controller_id');
         $this->db->join('modules_translation','modules.id=modules_translation.module_id');
         

         $this->db->where('controllers_translation.lang_id', $lang_id);
         $this->db->where('modules_translation.lang_id', $lang_id);                  
         $this->db->where('controllers.active', 1);
         $this->db->where('modules.active', 1);
         $this->db->where('modules.store_related', 1);
         
         $this->db->order_by('modules.sort', 'asc');
         $this->db->order_by('controllers.sort', 'asc');
         
         $result = $this->db->get('controllers');
         
         if($result)
         {
            return $result->result();
         }   
         else
         {
            return false;
         }
    }
   
   function get_module_controllers($module_id,$lang_id)
   {
        $this->db->select('controllers_translation.name,controllers.*');
        $this->db->join('controllers_translation','controllers.id = controllers_translation.controller_id');
        
        $this->db->where('controllers.module_id',$module_id);
        $this->db->where('controllers_translation.lang_id',$lang_id);
        
        $query = $this->db->get('controllers');
        
        if($query)
        {
            return $query->result();
        }else{
            return false;
        }
       
   }
   
   public function get_controller_id($controller,$module_id)
   {
        $this->db->where('controller',$controller);
        $this->db->where('module_id',$module_id);
        $row=$this->db->get('controllers')->row();
        if($row)
        {
            return $row->id;
        }else{
            return false;
        }
   }
   
   public function get_controller($controller_id,$lang_id)
   {
        $this->db->join('controllers_translation','controllers.id=controllers_translation.controller_id');
        $this->db->where('id',$controller_id);
        $this->db->where('controllers_translation.lang_id',$lang_id);
        
        $row=$this->db->get('controllers')->row();
        if($row)
        {
            return $row;
        }else{
            return false;
        }
   }
   
   public function get_controller_by_id($controller_id)
   {
        $this->db->where('id',$controller_id);
        $row=$this->db->get('controllers')->row();
        if($row)
        {
            return $row;
        }else{
            return false;
        }
   }
   public function get_controller_module($module_id)
   {
         $this->db->where('module_id',$module_id);
         $this->db->where('lang_id',1);
         
         $row = $this->db->get('modules_translation')->row();
         
         if($row)
        {
            return $row;
        }else{
            return false;
        }
   } 
  
   public function delete_controllers_translation($controller_id)
    {
        $this->db->where('controller_id',$controller_id);
        $this->db->delete('controllers_translation'); 
    }
    
   public function insert_controllers($data)
    {
        return $this->db->insert('controllers', $data);
    }
    
   public function insert_controllers_translation($controllers_translation_data)
    {
        return $this->db->insert('controllers_translation', $controllers_translation_data);
    }
    
     public function get_controllers_translation_result($id)
    {
        $this->db->select('controllers_translation.*');
        $this->db->join('controllers_translation','controllers.id = controllers_translation.controller_id');
        $this->db->where('controllers.id',$id);
        $query = $this->db->get('controllers');
        
        if($query)
        {
            return $query->result();
        }
    }
    
    public function get_controllers_result($id)
    {
        $this->db->where('id',$id);
        $query = $this->db->get('controllers');
        
        if($query)
        {
            return $query->row();
        }
    }
    
    public function update_controllers_translation($controller_id,$lang_id,$controllers_translation_data)
    {
        $this->db->where('controller_id',$controller_id);
        $this->db->where('lang_id',$lang_id);
        $this->db->update('controllers_translation',$controllers_translation_data);
    }
    
    public function update_controllers($controller_id,$controllers_data)
    {
        $this->db->where('id',$controller_id);
        $this->db->update('controllers',$controllers_data);
    }
    
    public function get_controllers_data($lang_id,$limit,$offset,$search_word='',$order_by,$order_state,$modules_filter_id=0)
    {
        $this->db->select('controllers.*, controllers_translation.name, modules_translation.name, controllers.id as id, controllers_translation.name as controller_name, modules_translation.name as module');
        
        $this->db->join('controllers_translation' , 'controllers.id = controllers_translation.controller_id');
        $this->db->join('modules_translation' , 'controllers.module_id = modules_translation.module_id');
        
        $this->db->where('controllers_translation.lang_id',$lang_id);
        $this->db->where('modules_translation.lang_id',$lang_id);
        
        if(trim($search_word) !='')
        {
            $this->db->like('controllers_translation.name', $search_word, 'both');
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
            elseif($order_by == lang('active'))
            {
                $this->db->order_by('controllers.active',$order_state);
            }
            elseif($order_by == lang('sort'))
            {
                $this->db->order_by('controllers.module_id',$order_state);
                $this->db->order_by('controllers.sort',$order_state);
            }
            else
            {
                $this->db->order_by('controllers.id',$order_state);
            }
        }
        else
        {
            $this->db->order_by('controllers.id',$order_state);
        }
        
        if($modules_filter_id !=0)
        {
            $this->db->where('controllers.module_id', $modules_filter_id);
        }
        
        $result = $this->db->get('controllers',$limit,$offset);

        if($result)
        {
            return $result->result();    
        }
     
    }
    
    public function get_count_all_controllers($lang_id,$search_word='',$modules_filter_id=0)
    {
        $this->db->join('controllers_translation' ,'controllers.id = controllers_translation.controller_id');
        
        if(trim($search_word) !='')
        {
            $this->db->like('controllers_translation.name', $search_word, 'both'); 
        }
        
        if($modules_filter_id !=0)
        {
            $this->db->where('controllers.module_id', $modules_filter_id);
        }
        
        $this->db->where('controllers_translation.lang_id',$lang_id);
        
        return $this->db->count_all_results('controllers');
    }
    
    public function delete_controller_data($ids_array)
    {
        $this->db->where_in('controller_id',$ids_array);
        $this->db->delete('controllers_translation');
        
        $this->db->where_in('id',$ids_array);
        $this->db->delete('controllers');
        
        echo '1';
    }
    
    public function get_controllers_filter_data($lang_id)
    {
        $this->db->select('controllers.*, controllers_translation.name, controllers.id as id ');
        $this->db->join('controllers_translation' , 'controllers.id = controllers_translation.controller_id');
        
        $this->db->where('controllers_translation.lang_id',$lang_id);
        
        $query = $this->db->get('controllers');
        
        if($query)
        {
            return $query->result();
        }
    }
    
    public function get_row_data($id,$display_lang_id)
    {
        $this->db->select('controllers.*, controllers_translation.name, modules_translation.name, controllers.id as id, controllers_translation.name as controller_name, modules_translation.name as module');
        
        $this->db->join('controllers_translation' , 'controllers.id = controllers_translation.controller_id');
        $this->db->join('modules_translation' , 'controllers.module_id = modules_translation.module_id');
        
        $this->db->where('controllers.id',$id);
        $this->db->where('controllers_translation.lang_id' , $display_lang_id);
        $this->db->where('modules_translation.lang_id'     , $display_lang_id);
       
        
        $result = $this->db->get('controllers');

        if($result)
        {
            return $result->row();    
        }
    }
    
    
    public function update_row_sort($id,$old_index,$new_index,$sort_state)
    {
        $this->db->where('id',$id);
        $row = $this->db->get('controllers');
        if($row)
        {
            $row      = $row->row();
            $row_sort = $row->sort; 
            
            // if the row moved down && sort state = ascending
            if($old_index < $new_index && $sort_state == 'asc' )
            {   
                $lower_rows_same_module_count = $this->count_rows($row->module_id,'>',$row->sort,'asc');
                $moved_rows                   = $new_index - $old_index;
                
                if($lower_rows_same_module_count < $moved_rows)
                {
                    $moved_rows = $lower_rows_same_module_count;
                }
                
                $new_sort = $row_sort + $moved_rows ;
                
                //update other rows sort value
                $this->db->where('sort >',$row_sort);
                $this->db->where('sort <=',$new_sort);
                $this->db->where('module_id',$row->module_id);
                $other_rows = $this->db->get('controllers');
                
                if($other_rows)
                {
                    $other_rows = $other_rows->result();
                
                    foreach($other_rows as $other_row)
                    {
                        $data_array = array('sort' => ($other_row->sort - 1));
                        
                        $this->db->where('id',$other_row->id);
                        $this->db->update('controllers',$data_array);
                    }
                }
                
            }
            //if the row moved up && sort state = ascending
            if(($old_index > $new_index && $sort_state=='asc'))
            {
                
                $upper_rows_same_module_count = $this->count_rows($row->module_id,'<',$row->sort,'asc');
                $moved_rows                   = $old_index - $new_index;
                
                if($upper_rows_same_module_count < $moved_rows)
                {
                    $moved_rows = $upper_rows_same_module_count;
                }
                
                
                $new_sort   = $row_sort - $moved_rows ;
                
                //update other rows sort value
                $this->db->where('sort <'  , $row_sort);
                $this->db->where('sort >=' , $new_sort);
                $this->db->where('module_id',$row->module_id);
                $other_rows = $this->db->get('controllers');
                
                if($other_rows)
                {
                    $other_rows = $other_rows->result();
                
                    foreach($other_rows as $other_row)
                    {
                        $data_array = array('sort' => ($other_row->sort + 1));
                        
                        $this->db->where('id',$other_row->id);
                        $this->db->update('controllers',$data_array);
                    }
                } 
            }
            
            //if the row moved up && sort state = descending
            if(($old_index > $new_index && $sort_state == 'desc' )) 
            {   
                $upper_rows_same_module_count = $this->count_rows($row->module_id,'>',$row->sort,'desc');
                $moved_rows                   = $old_index - $new_index ;
                
                if($upper_rows_same_module_count < $moved_rows)
                {
                    $moved_rows = $upper_rows_same_module_count;
                }
                
                $new_sort = $row_sort + $moved_rows ;
                
                //update other rows sort value
                $this->db->where('sort >',$row_sort);
                $this->db->where('sort <=',$new_sort);
                $this->db->where('module_id',$row->module_id);
                $other_rows = $this->db->get('controllers');
                
                if($other_rows)
                {
                    $other_rows = $other_rows->result();
                
                    foreach($other_rows as $other_row)
                    {
                        $data_array = array('sort' => ($other_row->sort - 1));
                        
                        $this->db->where('id',$other_row->id);
                        $this->db->update('controllers',$data_array);
                    }
                }  
            }
            
            //if the row moved down && sort state = descending
            if($old_index < $new_index && $sort_state=='desc')
            {
                
                
                $lower_rows_same_module_count = $this->count_rows($row->module_id,'<',$row->sort,'desc');
                $moved_rows                   = $new_index - $old_index;
                
                if($lower_rows_same_module_count < $moved_rows)
                {
                    $moved_rows = $lower_rows_same_module_count;
                }
                
                $new_sort   = $row_sort - $moved_rows ;
                
                //update other rows sort value
                $this->db->where('sort <'  , $row_sort);
                $this->db->where('sort >=' , $new_sort);
                $this->db->where('module_id',$row->module_id);
                $other_rows = $this->db->get('controllers');
                
                if($other_rows)
                {
                    $other_rows = $other_rows->result();
                
                    foreach($other_rows as $other_row)
                    {
                        $data_array = array('sort' => ($other_row->sort + 1));
                        
                        $this->db->where('id',$other_row->id);
                        $this->db->update('controllers',$data_array);
                    }
                }
            }
            
            // update row sort value
            $row_new_sort = array('sort' => $new_sort);
            
            $this->db->where('id',$id);
            $this->db->update('controllers',$row_new_sort);
            
        }
   }
   
   public function count_rows($module_id,$operator,$sort_value,$sort_state)
   {
        $this->db->where('module_id',$module_id);
        $this->db->where("sort $operator",$sort_value);
        $this->db->order_by('sort',$sort_state);
        
        return $this->db->count_all_results('controllers');
   }
   
   public function check_controllers_count($module_id,$controller)
   {
        $this->db->where('module_id',$module_id);
        $this->db->where('controller',$controller);
        
        $count = $this->db->count_all_results('controllers');
        
        return $count;
   }
   
   public function get_controller_by_module_path($module_path,$controller)
   {
        $this->db->select('modules.module, controllers.*');
        
        $this->db->join('modules', 'controllers.module_id = modules.id');
        
        $this->db->where('module_path',$module_path);
        $this->db->where('controller',$controller);
        
        $result = $this->db->get('controllers');
        if($result)
        {
            return $result->row();
        }
        else
        {
            return FALSE;
        }
   }
   /////////////////////////////////////////////////////////
}