<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Modules_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    function get_active_module()
    {
       return $this->db->get_where('modules',array('active'=>1))->result();
    }
    
    function get_active_controller()
    {
       return $this->db->get_where('modules',array('active'=>1))->result();
    }
   
   public function get_module_id($module)
   {
        $this->db->where('module',$module);
        $row=$this->db->get('modules')->row();
        if($row)
        {
            return $row->id;
        }else{
            return false;
        }
   }
   
   public function get_module($module_id,$lang_id)
   {
        $this->db->join('modules_translation','modules.id=modules_translation.module_id');
        $this->db->where('id',$module_id);
        $this->db->where('modules_translation.lang_id',$lang_id);
               
        $row=$this->db->get('modules')->row();
        
        if($row)
        {
            return $row;
        }else{
            return false;
        }
   }
  
  public function get_module_by_id($module_id)
  {
        $this->db->where('id',$module_id);
        $row=$this->db->get('modules')->row();
        
        if($row)
        {
            return $row;
        }
  } 
    public function delete_modules_translation($module_id)
    {
        $this->db->where('module_id',$module_id);
        $this->db->delete('modules_translation'); 
    }
    
    public function insert_modules($data)
    {
        return $this->db->insert('modules', $data);
    }
    
    public function insert_modules_translation($modules_translation_data)
    {
        return $this->db->insert('modules_translation', $modules_translation_data);
    }
    
    public function get_modules_translation_result($id)
    {
        $this->db->select('modules_translation.*','modules.*');
        $this->db->join('modules_translation','modules.id = modules_translation.module_id');
        $this->db->where('modules.id',$id);
        $query = $this->db->get('modules');
        
        if($query)
        {
            return $query->result();
        }
    }
    
    public function get_modules_result($id)
    {
        $this->db->where('id',$id);
        $query = $this->db->get('modules');
        
        if($query)
        {
            return $query->row();
        }
    }
    
    public function update_modules_translation($module_id,$lang_id,$modules_translation_data)
    {
        $this->db->where('module_id',$module_id);
        $this->db->where('lang_id',$lang_id);
        $this->db->update('modules_translation',$modules_translation_data);
    }
    
    public function update_modules($module_id,$modules_data)
    {
        $this->db->where('id',$module_id);
        $this->db->update('modules',$modules_data);
    }
    
    
    public function get_modules_data($lang_id,$limit,$offset,$search_word='',$order_by,$order_state)
    {
        $this->db->select('modules.*, modules_translation.name, modules.id as id, modules_translation.name as module_name');
        
        $this->db->join('modules_translation' , 'modules.id = modules_translation.module_id');
        
        $this->db->where('modules_translation.lang_id',$lang_id);
        
        if(trim($search_word) !='')
        {
            $this->db->or_like('modules_translation.name', $search_word, 'both');
        }
        
        if($order_by != '')
        {
            if($order_by == lang('module'))
            {
                $this->db->order_by('modules_translation.name',$order_state);
            }
            elseif($order_by == lang('icon_class'))
            {
                $this->db->order_by('modules.icon_class',$order_state);
            }
            elseif($order_by == lang('active'))
            {
                $this->db->order_by('modules.active',$order_state);
            }
            elseif($order_by == lang('sort'))
            {
                $this->db->order_by('modules.sort',$order_state);
            }
            else
            {
                $this->db->order_by('modules.id',$order_state);
            }
        }
        else
        {
            $this->db->order_by('modules.id',$order_state);
        }
        
        $result = $this->db->get('modules',$limit,$offset);

        if($result)
        {
            return $result->result();    
        }
     
    }
    
    public function get_count_all_modules($lang_id,$search_word='')
    {
        $this->db->join('modules_translation' ,'modules.id = modules_translation.module_id');
        
        if(trim($search_word) !='')
        {
            $this->db->like('modules_translation.name', $search_word, 'both'); 
        }
        
        $this->db->where('modules_translation.lang_id',$lang_id);
        
        return $this->db->count_all_results('modules');
    }
    
    public function delete_method_data($ids_array)
    {
        $this->db->where_in('module_id',$ids_array);
        $this->db->delete('modules_translation');
        
        $this->db->where_in('id',$ids_array);
        $this->db->delete('modules');
        
        echo '1';
    }
    
    public function get_modules_filter_data($lang_id)
    {
        $this->db->select('modules.*, modules_translation.name, modules.id as id ');
        $this->db->join('modules_translation' , 'modules.id = modules_translation.module_id');
        
        $this->db->where('modules_translation.lang_id',$lang_id);
        
        $query = $this->db->get('modules');
        
        if($query)
        {
            return $query->result();
        }
    }
    
    public function get_row_data($id,$display_lang_id)
    {
        $this->db->select('modules.*, modules_translation.name, modules.id as id, modules_translation.name as module_name');
        
        $this->db->join('modules_translation' , 'modules.id = modules_translation.module_id');
        
        $this->db->where('modules.id',$id);
        $this->db->where('modules_translation.lang_id',$display_lang_id);
        
        $result = $this->db->get('modules');

        if($result)
        {
            return $result->row();    
        }
    } 
    
    public function update_row_sort($id,$old_index,$new_index,$sort_state)
    {
        $this->db->where('id',$id);
        $row = $this->db->get('modules');
        if($row)
        {
            $row      = $row->row();
            $row_sort = $row->sort; 
            
            // if the row moved down && sort state = ascending
            if($old_index < $new_index && $sort_state == 'asc' )
            {   
                $moved_rows = $new_index - $old_index;
                $new_sort = $row_sort + $moved_rows ;
                
                //update other rows sort value
                $this->db->where('sort >',$row_sort);
                $this->db->where('sort <=',$new_sort);
                $other_rows = $this->db->get('modules');
                
                if($other_rows)
                {
                    $other_rows = $other_rows->result();
                
                    foreach($other_rows as $other_row)
                    {
                        $data_array = array('sort' => ($other_row->sort - 1));
                        
                        $this->db->where('id',$other_row->id);
                        $this->db->update('modules',$data_array);
                    }
                }
                
            }
            //if the row moved up && sort state = ascending
            if(($old_index > $new_index && $sort_state=='asc'))
            {
                $moved_rows = $old_index - $new_index;
                $new_sort   = $row_sort - $moved_rows ;
                
                //update other rows sort value
                $this->db->where('sort <'  , $row_sort);
                $this->db->where('sort >=' , $new_sort);
                $other_rows = $this->db->get('modules');
                
                if($other_rows)
                {
                    $other_rows = $other_rows->result();
                
                    foreach($other_rows as $other_row)
                    {
                        $data_array = array('sort' => ($other_row->sort + 1));
                        
                        $this->db->where('id',$other_row->id);
                        $this->db->update('modules',$data_array);
                    }
                } 
            }
            
            //if the row moved up && sort state = descending
            if(($old_index > $new_index && $sort_state == 'desc' )) 
            {   
                $moved_rows = $old_index - $new_index ;
                $new_sort = $row_sort + $moved_rows ;
                
                //update other rows sort value
                $this->db->where('sort >',$row_sort);
                $this->db->where('sort <=',$new_sort);
                $other_rows = $this->db->get('modules');
                
                if($other_rows)
                {
                    $other_rows = $other_rows->result();
                
                    foreach($other_rows as $other_row)
                    {
                        $data_array = array('sort' => ($other_row->sort - 1));
                        
                        $this->db->where('id',$other_row->id);
                        $this->db->update('modules',$data_array);
                    }
                }   
            }
            
            //if the row moved up && sort state = descending
            if($old_index < $new_index && $sort_state=='desc')
            {
                $moved_rows = $new_index - $old_index;
                $new_sort   = $row_sort - $moved_rows ;
                
                //update other rows sort value
                $this->db->where('sort <'  , $row_sort);
                $this->db->where('sort >=' , $new_sort);
                $other_rows = $this->db->get('modules');
                
                if($other_rows)
                {
                    $other_rows = $other_rows->result();
                
                    foreach($other_rows as $other_row)
                    {
                        $data_array = array('sort' => ($other_row->sort + 1));
                        
                        $this->db->where('id',$other_row->id);
                        $this->db->update('modules',$data_array);
                    }
                }
            }
            
            // update row sort value
            $row_new_sort = array('sort' => $new_sort);
            
            $this->db->where('id',$id);
            $this->db->update('modules',$row_new_sort);
            
        }
   }
    /***********************************************************/
}