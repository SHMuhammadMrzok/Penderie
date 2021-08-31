<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Cities_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    /***************Deleet********************/
    public function delete_cities_data($ids_array)
    {
        $this->db->where_in('city_id',$ids_array);
        $this->db->delete('cities_translation');
        
        $this->db->where_in('id',$ids_array);
        $this->db->delete('cities');
        
        echo '1';
    }
    
   
    /*****************Insert*************************/
     public function insert_cities($data)
    {
        return $this->db->insert('cities', $data);
    }
    
   public function insert_cities_translation($cities_translation_data)
    {
        return $this->db->insert('cities_translation', $cities_translation_data);
    }
    /****************Update*************************/
    
    public function update_cities_translation($lang_id , $id,  $cities_translation_data)
    {
        $this->db->where('city_id' ,$id);
        $this->db->where('lang_id' ,$lang_id);
        $this->db->update('cities_translation' ,$cities_translation_data);
    }
    
    public function update_cities($id , $data)
    {
        $this->db->where('id',$id);
        $this->db->update('cities',$data);
        
        return true;
    }
   
    /****************Get***************************/
    
    public function get_count_all_cites($lang_id,$search_word='',$user_nationality_filter_id=0)
    {
        $this->db->join('cities_translation' ,'cities.id = cities_translation.city_id');
        
        if(trim($search_word) !='')
        {
            $this->db->like('cities_translation.name', $search_word, 'both'); 
        }
        
        if($user_nationality_filter_id != 0)
        {
            $this->db->where('cities.user_nationality_id', $user_nationality_filter_id);
        }
        
        $this->db->where('cities_translation.lang_id',$lang_id);
        
        return $this->db->count_all_results('cities');
    }
    
     public function get_user_nationality_filter_data($lang_id)
    {
        $this->db->select('user_nationality.*, user_nationality_translation.name');
        $this->db->join('user_nationality_translation' , 'user_nationality.id = user_nationality_translation.user_nationality_id');
        
        $this->db->order_by('user_nationality.sort', 'asc');
        $this->db->where('user_nationality_translation.lang_id',$lang_id);
        
        $query = $this->db->get('user_nationality');
        
        if($query)
        {
            return $query->result();
        }
    }
    
    public function get_cities_data($lang_id,$limit,$offset,$search_word='',$order_by,$order_state,$user_nationality_filter_id=0)
    {
        $this->db->select('cities.*, cities_translation.name, user_nationality_translation.name as user_nationality_name');
        
        $this->db->join('cities_translation' , 'cities.id = cities_translation.city_id');
        $this->db->join('user_nationality_translation' , 'cities.user_nationality_id = user_nationality_translation.user_nationality_id');
        
        $this->db->where('cities_translation.lang_id',$lang_id);
        $this->db->where('user_nationality_translation.lang_id',$lang_id);
        
        if(trim($search_word) !='')
        {
            $this->db->like('cities_translation.name', $search_word, 'both');
            $this->db->or_like('user_nationality_translation.name', $search_word, 'both');
        }
        
        if($order_by != '')
        {
            if($order_by == lang('user_nationality'))
            {
                $this->db->order_by('user_nationality_translation.name',$order_state);
            }
            elseif($order_by == lang('city_name'))
            {
                $this->db->order_by('cities_translation.name',$order_state);
            }
           
            else
            {
                $this->db->order_by('cities.id',$order_state);
            }
        }
        else
        {
            $this->db->order_by('cities.id',$order_state);
        }
        
        if($user_nationality_filter_id !=0)
        {
            $this->db->where('cities.user_nationality_id', $user_nationality_filter_id);
        }
        
        $result = $this->db->get('cities',$limit,$offset);

        if($result)
        {
            return $result->result();    
        }
     
    }
    
    public function get_row_data($id,$display_lang_id)
    {
        $this->db->select('cities.*, cities_translation.name, user_nationality_translation.name as user_nationality_name ');
        
        $this->db->join('cities_translation' , 'cities.id = cities_translation.city_id');
        $this->db->join('user_nationality_translation' , 'cities.user_nationality_id = user_nationality_translation.user_nationality_id');
        
        $this->db->where('cities.id',$id);
        $this->db->where('cities_translation.lang_id' , $display_lang_id);
        $this->db->where('user_nationality_translation.lang_id'  , $display_lang_id);
       
        
        $result = $this->db->get('cities');

        if($result)
        {
            return $result->row();    
        }
    }
    
    public function get_cities_translation_result($id)
    {
        $this->db->where('city_id',$id);
        $result = $this->db->get('cities_translation');

        if($result)
        {
            return $result->result();    
        }
    }
 
 
    public function get_country_cities($country_id , $lang_id)
    {
        $this->db->select('cities.*, cities_translation.name');
        
        $this->db->join('cities_translation' , 'cities.id = cities_translation.city_id');
        $this->db->join('user_nationality' , 'cities.user_nationality_id = user_nationality.id');
        
        $this->db->where('cities.user_nationality_id',$country_id);
        $this->db->where('cities_translation.lang_id',$lang_id);
        
        $query = $this->db->get('cities');
        
        if($query)
        {
            return $query->result();
        }
    }
    public function  get_country_call_code($country_id)
    {
        $this->db->where('id',$country_id);
        
        $query = $this->db->get('user_nationality');
        
        if($query)
        {
            return $query->row();
        }
    }
    
    public function get_city_name($city_id, $lang_id)
    {
        $this->db->where('city_id', $city_id);
        $this->db->where('lang_id', $lang_id);
        
        $query = $this->db->get('cities_translation');
        
        if($query->row())
        {
            return $query->row()->name;
        }
        else
        {
            return false;
        }
    }
    
    
    public function get_cities($lang_id)
    {
        $this->db->select('cities_translation.*,cities.*, cities.id as id');
        $this->db->join('cities_translation','cities.id = cities_translation.city_id');
        
        $this->db->where('cities_translation.lang_id',$lang_id);
        
        $query = $this->db->get('cities');
        
        if($query)
        {
            return $query->result();
        }
        else
        {
            return false;
        }
    }
    
    public function get_store_cities($lang_id, $store_id)
    {
        $this->db->select('cities_translation.*,cities.*, cities.id as id');
        $this->db->join('cities_translation','cities.id = cities_translation.city_id');
        $this->db->join('countries','cities.user_nationality_id = countries.user_nationality_id AND countries.id ='. $store_id);
        
        $this->db->where('cities_translation.lang_id', $lang_id);
        
        $query = $this->db->get('cities');
        
        if($query)
        {
            return $query->result();
        }
        else
        {
            return false;
        }
    }
   /////////////////////////////////////////////////////////
}