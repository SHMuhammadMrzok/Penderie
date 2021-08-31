<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Admin_wrapping_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    /****************Get****************/
  
    public function get_count_all_data($lang_id, $search_word='')
    {
        $this->db->select('wrapping.* ,wrapping_translation.*');
        
        $this->db->join('wrapping_translation', 'wrapping.id = wrapping_translation.wrapping_id AND wrapping_translation.lang_id ='.$lang_id);
        
        if(trim($search_word) !='')
        {
            $this->db->where('(wrapping.box_size LIKE "%'.$search_word.'%" OR wrapping_translation.wrapping_type LIKE "%'.$search_word.'%" OR wrapping_translation.ribbon_type LIKE "%'.$search_word.'%")');
        }
        
        return $this->db->count_all_results('wrapping');
    }
    
    public function get_grid_data($lang_id, $limit, $offset, $search_word='', $order_by='', $order_state='desc')
    {
        $this->db->select('wrapping.* ,wrapping_translation.*');
        
        $this->db->join('wrapping_translation', 'wrapping.id = wrapping_translation.wrapping_id AND wrapping_translation.lang_id ='.$lang_id);
        
        if(trim($search_word) !='')
        {
            $this->db->where('(wrapping.box_size LIKE "%'.$search_word.'%" OR wrapping_translation.wrapping_type LIKE "%'.$search_word.'%" OR wrapping_translation.ribbon_type LIKE "%'.$search_word.'%")');
        }
        
        if($order_by != '')
        {
            if($order_by == lang('box_size'))
            { 
                $this->db->order_by('wrapping.box_size', $order_state);
            }
            elseif($order_by == lang('wrapping_type'))
            {
                $this->db->order_by('wrapping.wrapping_type', $order_state);
            }
            elseif($order_by == lang('ribbon_type'))
            {
                $this->db->order_by('wrapping.ribbon_type', $order_state);
            }
            else
            {
                $this->db->order_by('wrapping.id',$order_state);
            }
        }
        else
        {
            $this->db->order_by('wrapping.id',$order_state);
        }
        
        $result = $this->db->get('wrapping',$limit,$offset);
 
        if($result)
        {
            return $result->result();    
        }
    }
    
    public function get_row_data($id, $lang_id)
    {
        $this->db->select('wrapping.* ,wrapping_translation.*');
        
        $this->db->join('wrapping_translation', 'wrapping.id = wrapping_translation.wrapping_id AND wrapping_translation.lang_id ='.$lang_id);
        
        $this->db->where('wrapping.id',$id);
        
        $result = $this->db->get('wrapping');

        if($result)
        {
            return $result->row();    
        }
        else
        {
            return false;
        }
    }
    
    public function get_wrapping_data($id)
    {
        $this->db->where('id', $id);
        $row = $this->db->get('wrapping');
        
        if($row)
        {
            return $row->row();
        }
        else
        {
            return false;
        }
    }
    
    public function get_wrapping_translation_result($wrapping_id)
    {
        $this->db->where('wrapping_id', $wrapping_id);
        $result = $this->db->get('wrapping_translation');
        
        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }
    
    public function get_all_wrapping_data($lang_id)
    {
        $this->db->select('wrapping.* ,wrapping_translation.*');
        $this->db->join('wrapping_translation', 'wrapping.id = wrapping_translation.wrapping_id AND wrapping_translation.lang_id ='.$lang_id);
        
        $result = $this->db->get('wrapping');
        
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
   
    public function delete_wrapping_data($ids_array)
    {
        $this->db->where_in('id', $ids_array);
        $this->db->delete('wrapping');
        
        $this->db->where_in('wrapping_id', $ids_array);
        $this->db->delete('wrapping_translation');
        
        echo '1';  
    }
    
  /*****************INSERT***************************************/
  
    public function insert_wrapping($data)
    {
                
        return $this->db->insert('wrapping', $data);
    }
    
    public function insert_wrapping_translation($data)
    {
                
        return $this->db->insert('wrapping_translation', $data);
    } 
    
   
    /***********************UPDATE*************************/
    
    public function update_wrapping($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('wrapping', $data);
    }
    
    public function update_wrapping_translation($id, $lang_id, $trans_data)
    {
        $this->db->where('wrapping_id', $id);
        $this->db->where('lang_id', $lang_id);
        
        return $this->db->update('wrapping_translation', $trans_data);
    }  
    
   
/////////////////////////////////////////////////   
}