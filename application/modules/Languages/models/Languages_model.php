<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Languages_model extends CI_Model
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
    function get_language($lang_id)
    {
        $this->db->where('id',$lang_id);
        $row = $this->db->get('languages')->row_array();
        return $row['name'];   
    }
    function get_languages_result()
    {
        $this->db->where('active',1);
        return $this->db->get('languages')->result();
    }
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
    
    public function update_lang_translation($lang_var_id,$lang_id,$lang_translation_data)
    {
        $this->db->where('var_id',$lang_var_id);
        $this->db->where('lang_id',$lang_id);
        $this->db->update('lang_translation',$lang_translation_data);
    }
    
    public function get_var_value($id)
    {
        $this->db->where('id',$id);
        $row = $this->db->get('lang_vars')->row_array();
        if($row)
        {
            return $row['lang_var'];
        }else{
            return false;
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
    
    public function get_lang_vars_data($lang_id,$limit,$offset,$search_word='',$order_by,$order_state,$lang_filter_id=0)
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
        if($lang_filter_id != 0)
        { 
            $this->db->where('lang_translation.lang_id', $lang_filter_id);
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
    
    public function get_row_data($id,$display_lang_id)
    {
        //$active_lang_id    = $this->data['active_language']->id;
        
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
}