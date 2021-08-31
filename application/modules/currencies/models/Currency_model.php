<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Currency_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    /**********************INSERT*******************************/
    public function insert_currency($data)
    {
        return $this->db->insert('currencies', $data);
    }
    
    public function insert_currency_translation($translation_data)
    {
        return $this->db->insert('currencies_translation', $translation_data);
    }
   
    
    
    /**********************Update*******************************/
    public function update_currency($currency_id, $data)
    {
        $this->db->where('id',$currency_id);
        return $this->db->update('currencies', $data);
    }
    public function update_currency_translation($currency_id, $lang_id, $currency_translation_data)
    {
        $this->db->where('lang_id', $lang_id);
        $this->db->where('currency_id', $currency_id);
        
        return $this->db->update('currencies_translation', $currency_translation_data);
    }
   
    /**********************GET*******************************/ 
    
    
    public function get_count_all_currencies($lang_id ,$search_word ='')
    {
        $this->db->join('currencies_translation' ,'currencies.id = currencies_translation.currency_id AND currencies_translation.lang_id='.$lang_id);
        
        if(trim($search_word) !='')
        {
            $this->db->where('(currencies_translation.name LIKE "%'.$search_word.'%" OR currencies.currency_symbol LIKE "%'.$search_word.'%")');
        }
        
        return $this->db->count_all_results('currencies');
    }
    
    public function get_currency_data($lang_id, $limit, $offset, $search_word='', $order_by='', $order_state)
    {
        $this->db->select('currencies.* , currencies_translation.*');
        
        $this->db->join('currencies_translation' ,'currencies.id = currencies_translation.currency_id AND currencies_translation.lang_id='.$lang_id);
        
        if(trim($search_word) !='')
        {
            $this->db->where('(currencies_translation.name LIKE "%'.$search_word.'%" OR currencies.currency_symbol LIKE "%'.$search_word.'%")');  
        }
        
        
        $this->db->order_by('currencies.id', $order_state);
        
        
        $result = $this->db->get('currencies', $limit, $offset);

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
        $this->db->select('currencies.* , currencies_translation.*');
        
        $this->db->join('currencies_translation' ,'currencies.id = currencies_translation.currency_id AND currencies_translation.lang_id='.$lang_id);
        
        $this->db->where('currencies.id', $id);
        
        $result = $this->db->get('currencies');

        if($result)
        {
            return $result->row();    
        }
        else
        {
            return false;
        }
    }
    
    
    public function get_currecies_result($lang_id)
    {
        $this->db->select('currencies.* , currencies_translation.*');
        
        $this->db->join('currencies_translation' ,'currencies.id = currencies_translation.currency_id AND currencies_translation.lang_id='.$lang_id);
        
        $query = $this->db->get('currencies');
        
        if($query)
        {
            return $query->result();
        }
        else
        {
            return false;
        }
    }
    
    public function get_default_currency_data()
    {
        $this->db->where('system_default', 1);
        
        $query = $this->db->get('currencies');
        
        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }
    
    public function get_currency_result($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('currencies');
        
        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }
    public function get_currency_translation_result($currency_id)
    {   
        $this->db->where('currencies_translation.currency_id', $currency_id);
        
        $query = $this->db->get('currencies_translation');
        
        if($query)
        {
            return $query->result();
        }
        else
        {
            return false;
        }
    }
    
    public function get_country_currency_result($country_id)
    {
        $this->db->select('currencies.*');
        
        $this->db->join('countries', 'currencies.id = countries.currency_id AND countries.id = '.$country_id);
        
        $query = $this->db->get('currencies');
        
        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }
    
    public function get_country_currency_name($country_id, $lang_id)
    {
        $this->db->select('currencies_translation.name as name');
        $this->db->join('countries', 'currencies.id = countries.currency_id AND countries.id = '.$country_id);
        $this->db->join('currencies_translation' ,'currencies.id = currencies_translation.currency_id AND currencies_translation.lang_id='.$lang_id);
        
        $query = $this->db->get('currencies')->row();
        
        if($query)
        {
            return $query->name;
        }
        else
        {
            return false;
        }
    }
    
    public function get_symbol_data($symbol)
    {
        $this->db->where('currency_symbol', $symbol);
        
        $query = $this->db->get('currencies');
        
        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }
    
    /**********************DELETE*******************************/ 
    
    public function delete_currency_data($currency_ids_array)
    {
        $this->db->where_in('vendor_id', $vendor_id_array);
        $vendor_purchase = $this->db->count_all_results('purchase_orders');
        
        if($vendor_purchase > 0)
        {
            echo lang('vendor_purchase_exist');
            
        }else{
            
            $this->db->where_in('id',$vendor_id_array);
            $this->db->delete('vendors');
            
            $this->db->where_in('vendor_id',$vendor_id_array);
            $this->db->delete('vendors_translation');
            
            echo '1';
        }
    }
    
    
/****************************************************************/
}