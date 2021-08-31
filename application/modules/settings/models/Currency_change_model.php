<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Currency_change_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    
    
    /********************insert********************/
    public function insert_dollar_currency_data($data)
    {
        return $this->db->insert('dollar_values', $data);
    }
    /********************Get********************/
    
    public function get_count_all_rows($search_word='')
    {
        $this->db->join('countries', 'dollar_values.country_id = countries.id');
        
        if(trim($search_word) !='')
        {
            $this->db->where('(countries.currency_symbol LIKE "%'.$search_word.'%" OR dollar_values.dollar_val LIKE "%'.$search_word.'%")');
        }
        
        return $this->db->count_all_results('dollar_values');
    }
   
    public function get_currency_change_data( $limit, $offset, $search_word='', $order_by='', $order_state)
    {
        $this->db->select('countries.*, dollar_values.*, dollar_values.id as id');
        
        $this->db->join('countries', 'dollar_values.country_id = countries.id');
        
        
        if(trim($search_word) !='')
        {
            $this->db->where('(countries.currency_symbol LIKE "%'.$search_word.'%" OR dollar_values.dollar_val LIKE "%'.$search_word.'%")');
        }
        
        if($order_by != '')
        {
            if($order_by == lang('value_in_dollars'))
            { 
                $this->db->order_by('dollar_values.dollar_val', $order_state);
            }
            else
            {
                $this->db->order_by('dollar_values.id', $order_state);
            }
        }
        else
        {
            $this->db->order_by('dollar_values.id', $order_state);
        }
        
        $result = $this->db->get('dollar_values', $limit, $offset);

        if($result)
        {
            return $result->result();    
        }
        else
        {
            return false;
        }
    }
    
    public function get_countries_with_currency_result()
    {
        $this->db->select('countries.*, dollar_values.*, dollar_values.id as id, countries.id as country_id');
        $this->db->join('dollar_values', 'countries.id = dollar_values.country_id', 'left');
        
        $result = $this->db->get('countries');

        if($result)
        {
            return $result->result();    
        }
        else
        {
            return false;
        }
    }
    
    public function get_currency_change_read_data($lang_id)
    {
        $this->db->select('countries.*, countries_translation.name as country, dollar_values.*, dollar_values.id as id, countries.id as country_id');
        
        $this->db->join('dollar_values', 'countries.id = dollar_values.country_id');
        $this->db->join('countries_translation', 'countries.id = countries_translation.country_id');
        
        $this->db->where('countries_translation.lang_id', $lang_id);
        
        $result = $this->db->get('countries');
        
        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }
    
    public function get_dollar_value_in_country($country_id)
    {
        $this->db->where('country_id', $country_id);
        
        $query = $this->db->get('dollar_values');
        
        if($query->row())
        {
            return $query->row()->dollar_val;
        }
        else
        {
            return false;
        }
    }
    
    /***************************Delete *********************************/
    
    public function delete_currency_data($country_id)
    {
        $this->db->where('country_id', $country_id);
        return $this->db->delete('dollar_values');
    } 
    
   
   
    /****************************************************/
}