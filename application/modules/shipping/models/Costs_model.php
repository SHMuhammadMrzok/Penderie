<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Costs_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    
    /***************Insert***********************/  
    public function insert_cost($data)
    {
       return $this->db->insert('shipping_costs', $data);
    }
    
    public function insert_cost_translation($cost_translation_data)
    {
        return $this->db->insert('shipping_costs_translation', $cost_translation_data);
    }
   
/********************Get********************/
    
    public function get_count_all_costs($lang_id, $search_word='')
    {
        $this->db->join('shipping_costs_translation' ,'shipping_costs.id = shipping_costs_translation.shipping_cost_id');
        $this->db->join('currencies' ,'shipping_costs.currency_id = currencies.id');
        
        if(trim($search_word) !='')
        {
            $this->db->where('(shipping_costs_translation.country LIKE "%'.$search_word.'%" OR shipping_costs.cost LIKE "%'.$search_word.'%")');
        }
        
        $this->db->where('shipping_costs_translation.lang_id',$lang_id);
        
        return $this->db->count_all_results('shipping_costs');
    }
   
    public function get_costs_data($lang_id, $limit, $offset, $search_word='', $order_by='', $order_state)
    {
        $this->db->select('shipping_costs.*, shipping_costs_translation.*, currencies.*, shipping_costs.id as id');
        $this->db->join('shipping_costs_translation' ,'shipping_costs.id = shipping_costs_translation.shipping_cost_id');
        $this->db->join('currencies' ,'shipping_costs.currency_id = currencies.id');
        
        if(trim($search_word) !='')
        {
            $this->db->where('(shipping_costs_translation.country LIKE "%'.$search_word.'%" OR shipping_costs.cost LIKE "%'.$search_word.'%")');
        }
        
        $this->db->where('shipping_costs_translation.lang_id',$lang_id);
        
        $this->db->order_by('shipping_costs.id', $order_state);
        
        
        $result = $this->db->get('shipping_costs', $limit, $offset);

        if($result)
        {
            return $result->result();    
        }
        else
        {
            return false;
        }
    } 
    
    public function get_cost_row($id)
    {
        $this->db->select('shipping_costs.*, currencies.*');
        $this->db->join('currencies', 'shipping_costs.currency_id = currencies.id');
        
        $this->db->where('shipping_costs.id', $id);
        $row = $this->db->get('shipping_costs');
         
        if($row)
        {
           return $row->row();
        }
        else
        {
           return false;
        }
    }
   
    public function get_cost_translation_result($cost_id)
    {
        $this->db->where('shipping_cost_id', $cost_id);
        $result = $this->db->get('shipping_costs_translation');
        
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
        $this->db->join('shipping_costs_translation' ,'shipping_costs.id = shipping_costs_translation.shipping_cost_id AND shipping_costs_translation.lang_id ='.$lang_id);
        $this->db->join('currencies' ,'shipping_costs.currency_id = currencies.id');
        
        $this->db->where('shipping_costs.id', $id);
        
        $result = $this->db->get('shipping_costs');
        
        if($result)
        {
            return $result->row();    
        }
        else
        {
            return false;
        }
    }
    
    
    public function get_shipping_costs_result($lang_id)
    {
        $this->db->select('shipping_costs.*, shipping_costs_translation.*, currencies.*, shipping_costs.id as id');
        $this->db->join('shipping_costs_translation' ,'shipping_costs.id = shipping_costs_translation.shipping_cost_id AND shipping_costs_translation.lang_id ='.$lang_id);
        $this->db->join('currencies' ,'shipping_costs.currency_id = currencies.id');
        
        $result = $this->db->get('shipping_costs');
        
        if($result)
        {
            return $result->result();    
        }
        else
        {
            return false;
        }
    }
     
    /*****************Update ************************/
  
    public function update_cost($id, $cost_data)
    {
        $this->db->where('id', $id);
        return $this->db->update('shipping_costs', $cost_data);
    }
    
    public function update_cost_translation($cost_id, $lang_id, $cost_translation_data)
    {
        $this->db->where('shipping_cost_id', $cost_id);
        $this->db->where('lang_id', $lang_id);
        
        return $this->db->update('shipping_costs_translation', $cost_translation_data);
    }
    
  
    /***************************Delete *********************************/
    
    public function delete_cost_data($ids_array)
    {
        $this->db->where_in('id', $ids_array);
        $this->db->delete('shipping_costs');
        
        $this->db->where_in('shipping_cost_id', $ids_array);
        $this->db->delete('shipping_costs_translation');
        
        echo '1';
    } 
    
    public function delete_cities_data($ids_array)
    {
        $this->db->where_in('id', $ids_array);
        $this->db->delete('shipping_cities');
        
        $this->db->where_in('city_id', $ids_array);
        $this->db->delete('shipping_cities_translation');
        
        echo '1';
    }
    
    public function get_shipping_cities($country_id, $lang_id)
    {
        $this->db->select('shipping_cities.id as id, shipping_cities_translation.*');
        $this->db->join('shipping_cities_translation', 'shipping_cities.id = shipping_cities_translation.city_id
                         AND shipping_cities_translation.lang_id ='.$lang_id);
                         
        $this->db->where('shipping_cities.country_id', $country_id);
        
        $result = $this->db->get('shipping_cities');
        
        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }
    
     /**********************SHIPPING CITIES******************************/
    
    public function get_city_row_data($id, $lang_id)
    {
        $this->db->select('shipping_cities.*, shipping_costs_translation.country as country, shipping_cities_translation.name as city_name');
        
        $this->db->join('shipping_costs_translation' ,'shipping_cities.country_id = shipping_costs_translation.shipping_cost_id 
                         AND shipping_costs_translation.lang_id ='.$lang_id);
        $this->db->join('shipping_cities_translation' ,'shipping_cities.id = shipping_cities_translation.city_id 
                         AND shipping_costs_translation.lang_id ='.$lang_id);
        
        $this->db->where('shipping_cities.id', $id);
        
        $result = $this->db->get('shipping_cities');
        
        if($result)
        {
            return $result->row();    
        }
        else
        {
            return false;
        }
    }
    
    public function get_count_all_cities($lang_id, $search_word='')
    {
        $this->db->join('shipping_cities_translation' ,'shipping_cities.id = shipping_cities_translation.city_id');
        $this->db->join('shipping_costs_translation' ,'shipping_cities.country_id = shipping_costs_translation.shipping_cost_id
                        AND shipping_costs_translation.lang_id='.$lang_id);
        
        if(trim($search_word) !='')
        {
            $this->db->where('(shipping_cities_translation.country LIKE "%'.$search_word.'%" OR shipping_cities.cost LIKE "%'.$search_word.'%")');
        }
        
        $this->db->where('shipping_cities_translation.lang_id',$lang_id);
        
        return $this->db->count_all_results('shipping_cities');
    }
   
    public function get_cities_data($lang_id, $limit, $offset, $search_word='', $order_by='', $order_state)
    {
        $this->db->select('shipping_cities.*, shipping_cities_translation.name as city, shipping_cities.id as id, shipping_costs_translation.*');
        
        $this->db->join('shipping_cities_translation' ,'shipping_cities.id = shipping_cities_translation.city_id');
        $this->db->join('shipping_costs_translation' ,'shipping_cities.country_id = shipping_costs_translation.shipping_cost_id
                        AND shipping_costs_translation.lang_id='.$lang_id);
        
        
        if(trim($search_word) !='')
        {
            $this->db->where('(shipping_cities_translation.country LIKE "%'.$search_word.'%" OR shipping_cities.cost LIKE "%'.$search_word.'%")');
        }
        
        $this->db->where('shipping_cities_translation.lang_id',$lang_id);
        
        $this->db->order_by('shipping_cities.id', $order_state);
        
        
        $result = $this->db->get('shipping_cities', $limit, $offset);

        if($result)
        {
            return $result->result();    
        }
        else
        {
            return false;
        }
    } 
    
    public function insert_city($data)
    {
        return $this->db->insert('shipping_cities', $data);
    }
    
    public function insert_city_translation($data)
    {
        return $this->db->insert('shipping_cities_translation', $data);
    }
    
    public function update_city($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('shipping_cities', $data);
    }
    
    public function update_city_translation($id, $lang_id, $translation_data)
    {
        $this->db->where('city_id', $id);
        $this->db->where('lang_id', $lang_id);
        return $this->db->update('shipping_cities_translation', $translation_data);
    }
    
    public function get_city_row($city_id)
    {
        $this->db->where('id', $city_id);
        
        $query = $this->db->get('shipping_cities');
        
        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }
    
    public function get_city_translation_result($city_id)
    {
        $this->db->where('city_id', $city_id);
        
        $query = $this->db->get('shipping_cities_translation');
        
        if($query)
        {
            return $query->result();
        }
        else
        {
            return false;
        }
    }
    
    
    
    
   
   
    /****************************************************/
}