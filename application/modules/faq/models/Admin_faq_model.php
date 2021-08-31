<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Admin_faq_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    /**********************INSERT*******************************/
    public function insert_faq($data)
    {
        return $this->db->insert('faq', $data);
    }
    
    public function insert_faq_translation($faq_translation_data)
    {
        return $this->db->insert('faq_translation', $faq_translation_data);
    }
   
    /**********************GET*******************************/
    public function get_faq_result($id)
    {
        $this->db->where('id',$id);
        $query = $this->db->get('faq');
        
        if($query)
        {
            return $query->row();
        }
    }
    public function get_faq_translation_result($id)
    {
        $this->db->select('faq_translation.*');
        $this->db->join('faq_translation','faq.id = faq_translation.faq_id');
        $this->db->where('faq.id',$id);
        $query = $this->db->get('faq');
        
        if($query)
        {
            return $query->result();
        }
    }
    
    public function get_faq($lang_id)
    {
        $this->db->select('faq_translation.*,faq.*');
        $this->db->join('faq_translation','faq.id = faq_translation.faq_id');
        $this->db->where('faq_translation.lang_id',$lang_id);
        $query = $this->db->get('faq');
        
        if($query)
        {
            return $query->result();
        }
    }
    
    public function get_vendor($faq_id , $lang_id)
    {
        $this->db->select('title');
        
        $this->db->where('faq_id',$faq_id);
        $this->db->where('lang_id',$lang_id);
        
        $query = $this->db->get('faq_translation');
        
        if($query)
        {
            return $query->row();
        }
    }
    
     public function get_count_all_faq($lang_id ,$search_word ='')
    {
        $this->db->join('faq_translation' ,'faq.id = faq_translation.faq_id');
        
        if(trim($search_word) !='')
        {
            $this->db->like('faq_translation.question', $search_word, 'both');  
            $this->db->or_like('faq_translation.answer', $search_word, 'both');
        }
       
        $this->db->where('faq_translation.lang_id',$lang_id);
        
        return $this->db->count_all_results('faq');
    }
    
    public function get_faq_data($lang_id,$limit,$offset,$search_word='',$order_by='',$order_state)
    {
        $this->db->select('faq_translation.* , faq.* , faq.id as id');
        
        $this->db->join('faq_translation' ,'faq.id = faq_translation.faq_id');
        
        $this->db->where('faq_translation.lang_id',$lang_id);
        
        if(trim($search_word) !='')
        {
            $this->db->like('faq_translation.question', $search_word, 'both');  
            $this->db->or_like('faq_translation.answer', $search_word, 'both'); 
        }
        
        
        if($order_by != '')
        {
            
            if($order_by == lang('question'))
            {
                $this->db->order_by('faq_translation.question',$order_state);
            }
            elseif($order_by == lang('answer'))
            {
                $this->db->order_by('faq_translation.answer',$order_state);
            }
            else
            {
                $this->db->order_by('faq.id',$order_state);
            }
        }
        else
        {
            $this->db->order_by('faq.id',$order_state);
        }
        
        $result = $this->db->get('faq',$limit,$offset);

        if($result)
        {
            return $result->result();    
        }
    }
    
    public function get_row_data($id,$display_lang_id)
    {
        $this->db->select('faq_translation.* , faq.* ,faq.id as id');
        
        $this->db->join('faq_translation' ,'faq.id = faq_translation.faq_id');
        
        $this->db->where('faq.id',$id);
        $this->db->where('faq_translation.lang_id',$display_lang_id);
        
        $result = $this->db->get('faq');

        if($result)
        {
            return $result->row();    
        }
    }
    /**********************Update*******************************/
    public function update_faq($faq_id,$data)
    {
        $this->db->where('id',$faq_id);
        return $this->db->update('faq',$data);
    }
    public function update_faq_translation($faq_id,$lang_id,$faq_translation_data)
    {
        $this->db->where('faq_id',$faq_id);
        $this->db->where('lang_id',$lang_id);
        return $this->db->update('faq_translation',$faq_translation_data);
    }
   
    /**********************DELETE*******************************/ 
    public function delete_faq_translation($faq_id)
    {
        $this->db->where('faq_id',$faq_id);
        $this->db->delete('faq_translation'); 
    }
    
   
    public function delete_faq_data($faq_id_array)
    {
        $this->db->where_in('id',$faq_id_array);
        $this->db->delete('faq');
        
        $this->db->where_in('faq_id',$faq_id_array);
        $this->db->delete('faq_translation');
        
        echo '1';
    }
    
  
/****************************************************************/
}