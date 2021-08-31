<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Colors_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    /****************Get****************/
  
    public function get_count_all_data($lang_id, $search_word='')
    {
        $this->db->select('colors.* ,colors_translation.*');
        $this->db->join('colors_translation', 'colors.id = colors_translation.color_id AND colors_translation.lang_id ='.$lang_id);
        
        if(trim($search_word) !='')
        {
            $this->db->where('(colors_translation.name LIKE "%'.$search_word.'%")');
        }
        
        return $this->db->count_all_results('colors');
    }
    
    public function get_grid_data($lang_id, $limit, $offset, $search_word='', $order_by='', $order_state='desc')
    {
        $this->db->select('colors.* ,colors_translation.*');
        $this->db->join('colors_translation', 'colors.id = colors_translation.color_id AND colors_translation.lang_id ='.$lang_id);
        
        if(trim($search_word) !='')
        {
            $this->db->where('(colors_translation.name LIKE "%'.$search_word.'%")');
        }
        
        $this->db->order_by('colors.id',$order_state);
        
        
        $result = $this->db->get('colors', $limit, $offset);
 
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
        $this->db->select('colors.* ,colors_translation.*');
        $this->db->join('colors_translation', 'colors.id = colors_translation.color_id AND colors_translation.lang_id ='.$lang_id);
        
        $this->db->where('colors.id', $id);
        
        $result = $this->db->get('colors');

        if($result)
        {
            return $result->row();    
        }
        else
        {
            return false;
        }
    }
    
    public function check_color_used($color_ids)
    {
        $this->db->where_in('color_id', $color_ids);
        $count = $this->db->count_all_results('wrapping_data');
        
        if($count > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    public function get_colors_data($id)
    {
        $this->db->where('id', $id);
        $row = $this->db->get('colors');
        
        if($row)
        {
            return $row->row();
        }
        else
        {
            return false;
        }
    }
    
    public function get_colors_translation_result($color_id)
    {
        $this->db->where('color_id', $color_id);
        $result = $this->db->get('colors_translation');
        
        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }
    
    public function get_all_colors_data($lang_id)
    {
        $this->db->select('colors.* ,colors_translation.*');
        $this->db->join('colors_translation', 'colors.id = colors_translation.color_id AND colors_translation.lang_id ='.$lang_id);
        
        $result = $this->db->get('colors');
        
        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }
   
   /*************************DELETE*******************************/
   
    public function delete_colors_data($ids_array)
    {
        $this->db->where_in('id', $ids_array);
        $this->db->delete('colors');
        
        $this->db->where_in('color_id', $ids_array);
        $this->db->delete('colors_translation');
        
        echo '1';  
    }
    
  /*****************INSERT***************************************/
  
    public function insert_colors($data)
    {
                
        return $this->db->insert('colors', $data);
    }
    
    public function insert_colors_translation($data)
    {
                
        return $this->db->insert('colors_translation', $data);
    } 
    
   
    /***********************UPDATE*************************/
    
    public function update_colors($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('colors', $data);
    }
    
    public function update_colors_translation($id, $lang_id, $trans_data)
    {
        $this->db->where('color_id', $id);
        $this->db->where('lang_id', $lang_id);
        
        return $this->db->update('colors_translation', $trans_data);
    }  
    
   
/////////////////////////////////////////////////   
}