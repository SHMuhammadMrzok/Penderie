<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Lang_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    function check_lang_vars($lang_id,$lang_var)
    {
        $this->db->where('lang_id',$lang_id);
        $this->db->where('lang_var',$lang_var);
        $count = $this->db->count_all_results('lang_words');
        if($count > 0){
            return false;
        }else{
            return true;
        }
    }
    function save_data($data)
    {
        $this->db->insert('lang_words',$data);
    }
    
    public function get_active_structure_languages()
    {
        $this->db->where('active',1);
        $this->db->where('strucure',1);
        $this->db->order_by('id', 'desc');
        
        $query = $this->db->get('languages');
        
        if($query)
        {
            return $query->result();
        }
        else
        {
            return false;
        }
    }
    
    
    public function get_active_data_languages()
    {
        $this->db->where('active',1);
        $this->db->where('data',1);
        
        $query = $this->db->get('languages');
        
        if($query)
        {
            return $query->result();
        }
        else
        {
            return false;
        }
    }
    
    public function get_language_row_by_lang($lang)
    {
        $query = $this->db->get_where('languages', array('language' => $lang));
        
        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }
    
    public function get_language_row_by_id($lang_id)
    {
        $query = $this->db->get_where('languages', array('id' => $lang_id));
        
        if($query)
        {
            return $query->row();
        }
        
    }
    
    
    
    /****************************************************************************/
    
    function insert_lang_var($data)
    {
        return $this->db->insert('lang_vars',$data);
    }
    public function insert_lang_translation($lang_translation_data)
    {
        return $this->db->insert('lang_translation',$lang_translation_data);
    }
    public function get_lang_result($id)
    {
        $this->db->select('lang_translation.* , lang_vars.*');
        $this->db->join('lang_translation','lang_vars.id = lang_translation.var_id');
        $this->db->where('lang_vars.id',$id);
        $query = $this->db->get('lang_vars');
        
        if($query)
        {
            return $query->result();
        }
    }
    public function get_general_lang_data($id)
    {
        $this->db->where('id',$id);
        $query = $this->db->get('lang_vars');
        if($query)
        {
            return $query->row();
        }else{
            return false;
        }
    }
    public function update_general_data($lang_var_id,$data)
    {
        $this->db->where('id',$lang_var_id);
        $this->db->update('lang_vars',$data);
    }
    public function update_lang_translation($lang_var_id,$lang_id,$lang_translation_data)
    {
        $this->db->where('var_id',$lang_var_id);
        $this->db->where('lang_id',$lang_id);
        $this->db->update('lang_translation',$lang_translation_data);
    }
    
    public function get_count_all_lang_vars($lang_id,$search_word='')
    {
        $this->db->join('lang_translation' ,'lang_vars.id = lang_translation.var_id');
        
        if(trim($search_word) !='')
        {
            $this->db->like('lang_translation.lang_definition', $search_word, 'both');  
        }
        else
        {
            $this->db->where('lang_translation.lang_id',$lang_id);
        }
        
        return $this->db->count_all_results('lang_vars');
    }
    
    public function get_lang_vars_data($lang_id,$limit,$offset,$search_word='',$order_by='',$order_state)
    {
        
        $this->db->select('lang_vars.* , lang_translation.* ,languages.*, lang_vars.id as id, ');
        
        $this->db->join('lang_translation' ,'lang_vars.id = lang_translation.var_id');
        $this->db->join('languages','lang_translation.lang_id = languages.id');
        
        
        
        if(trim($search_word) !='')
        {
            $this->db->like('lang_translation.lang_definition', $search_word, 'both');
            $this->db->or_like('lang_vars.lang_var', $search_word, 'both');
        }
        else
        {
            $this->db->where('lang_translation.lang_id',$lang_id);
        }
        
        if($order_by != '')
        {
            if($order_by == lang('lang_var'))
            {
                $this->db->order_by('lang_vars.lang_var',$order_state);
            }
            elseif($order_by == lang('lang_definition'))
            {
                $this->db->order_by('lang_translation.lang_definition',$order_state);
            }
            elseif($order_by == 'id')
            {
                $this->db->order_by('lang_vars.id',$order_state);
            }
            else
            {
                $this->db->order_by('lang_vars.id',$order_state);
            }
            
        }
        else
        {
            $this->db->order_by('lang_vars.id',$order_state);
        }
        
        $result = $this->db->get('lang_vars',$limit,$offset);

        if($result)
        {
            return $result->result();    
        }
        
    }
    
    public function get_row_data($id,$display_lang_id)
    {
        $this->db->select('lang_vars.* , lang_translation.* ,languages.*, lang_vars.id as id, languages.name as language ');
        
        $this->db->join('lang_translation' ,'lang_vars.id = lang_translation.var_id');
        $this->db->join('languages','lang_translation.lang_id = languages.id');
        
        $this->db->where('lang_vars.id',$id);
        $this->db->where('lang_translation.lang_id',$display_lang_id);
        
        $result = $this->db->get('lang_vars');

        if($result)
        {
            return $result->row();    
        }
    }
    
    public function get_count_all_languages($search_word='')
    {
        if(trim($search_word != ''))
        {
            $this->db->like('name',$search_word,'both');
        }
        
        return $this->db->count_all_results('languages');
    }
    
    public function get_languages_data($limit,$offset,$search_word='',$order_by,$order_state)
    {
        if(trim($search_word) !='')
        {
            $this->db->like('name', $search_word, 'both');
        }
        
        if($order_by != '')
        {
            if($order_by == lang('language'))
            { 
                $this->db->order_by('name',$order_state);
            }
            elseif($order_by == lang('direction'))
            {
                $this->db->order_by('direction',$order_state);
            }
            elseif($order_by == lang('active'))
            {
                $this->db->order_by('active',$order_state);
            }
            else
            {
                $this->db->order_by('id',$order_state);
            }
        }
        else
        {
            $this->db->order_by('id',$order_state);
        }
        
        $result = $this->db->get('languages',$limit,$offset);

        if($result)
        { 
            return $result->result();    
        }
    }
    
    public function get_language_row_data($id)
    {
        $this->db->where('id',$id);
        $row = $this->db->get('languages');
        
        if($row)
        {
            return $row->row();
        }
    }
    
    public function delete_languages_data($ids_array)
    {
        $this->db->where_in('id',$ids_array);
        $this->db->delete('languages');
        
        echo '1';  
    }
    
    public function insert_lang_vars($general_data)
    {
        return $this->db->insert('languages',$general_data);
    }
    
    public function get_language_result($id)
    {
        $this->db->where('id',$id);
        $result = $this->db->get('languages');
        
        if($result)
        {
            return $result->row();
        }
    }
    
    public function update_language($row_id,$row_data)
    {
        $this->db->where('id',$row_id);
        $this->db->update('languages',$row_data);
    }
    
    
    
}