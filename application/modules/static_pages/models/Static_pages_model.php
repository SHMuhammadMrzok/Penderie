<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Static_pages_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    /**********************INSERT*******************************/
   
    public function insert_static_pages($data)
    {
        return $this->db->insert('static_pages',$data);
    }
    
    public function insert_static_pages_translation($translation_data)
    {
        return $this->db->insert('static_pages_translation', $translation_data);
    }
   
    /**********************GET*******************************/
    
    public function get_static_pages_data($lang_id, $limit, $offset, $search_word, $order_by, $order_state)
    {
        $this->db->select('static_pages.*, static_pages_translation .*, static_pages.id as id');
        
        $this->db->join('static_pages_translation', 'static_pages.id = static_pages_translation.page_id');
        
        if($search_word != '')
        {
            $this->db->like('title',$search_word,'both');
            $this->db->or_like('page_text',$search_word,'both');
        }
        
        if($order_by != '')
        {
            if($order_by == lang('page_id'))
            {
                $this->db->order_by('static_pages.page_id', $order_state);
            }
            elseif($order_by == lang('title'))
            {
                $this->db->order_by('static_pages_translation.title', $order_state);
            }
            elseif($order_by == lang('page_text'))
            {
                $this->db->order_by('static-pages_translation.page_text', $order_state);
            }
            else
            {
                $this->db->order_by('static_pages.id', $order_state);
            }
        }
        else
        {
            $this->db->order_by('static_pages.id', 'desc');
        }    
        
        $this->db->where('static_pages_translation.lang_id', $lang_id);
        
        $result = $this->db->get('static_pages', $limit, $offset);
        
        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }
    
    public function get_static_pages_result($page_id)
    {
        $this->db->where('id',$page_id);
        $query = $this->db->get('static_pages');
        
        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }
    
    public function get_static_pages_translation_result($page_id)
    {
        $this->db->where('page_id',$page_id);
        $query = $this->db->get('static_pages_translation');
        
        if($query)
        {
            return $query->result();
        }
        else
        {
            return false;
        }
    }
    
    public function get_row_data($static_page_id, $display_lang_id)
    {
        $this->db->select('static_pages.*, static_pages_translation.*, static_pages.id as id');
        
        $this->db->join('static_pages_translation', 'static_pages.id = static_pages_translation.page_id');
        
        $this->db->where('static_pages.id', $static_page_id);
        $this->db->where('static_pages_translation.lang_id', $display_lang_id);
        
        $query = $this->db->get('static_pages');
        
        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }
    
    /**********************Count*********************************/
    
    public function get_count_all_rows($lang_id ,$search_word ='')
    {
        $this->db->join('static_pages_translation', 'static_pages.id = static_pages_translation.page_id');
        
        if($search_word != '')
        {
            $this->db->like('title',$search_word,'both');
            $this->db->or_like('page_text',$search_word,'both');
        }
        $this->db->where('static_pages_translation.lang_id',$lang_id);
        
        $count = $this->db->count_all_results('static_pages');
        
        return $count;
    }
    
    /**********************Update*******************************/
    
    public function update_static_page($static_page_id,$data)
    {
        $this->db->where('id',$static_page_id);
        
        return $this->db->update('static_pages',$data);
    }
    
    public function update_static_page_translation($static_page_id, $lang_id, $static_page_translation_data)
    {
        $this->db->where('page_id',$static_page_id);
        $this->db->where('lang_id',$lang_id);
        
        return $this->db->update('static_pages_translation', $static_page_translation_data);
    }
   
    /**********************DELETE*******************************/ 
    
    public function delete_static_pages_data($ids_array)
    {
        $this->db->where_in('id',$ids_array);
        $this->db->delete('static_pages');
        
        $this->db->where_in('page_id',$ids_array);
        $this->db->delete('static_pages_translation');
        
        echo '1';
    }
/****************************************************************/
}