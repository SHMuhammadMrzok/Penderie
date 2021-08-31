<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Vendors_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    /**********************INSERT*******************************/
    public function insert_vendors($data)
    {
        return $this->db->insert('vendors', $data);
    }
    
    public function insert_vendors_translation($vendors_translation_data)
    {
        return $this->db->insert('vendors_translation', $vendors_translation_data);
    }
   
    /**********************GET*******************************/
    public function get_vendors_result($id)
    {
        $this->db->where('id',$id);
        $query = $this->db->get('vendors');
        
        if($query)
        {
            return $query->row();
        }
    }
    public function get_vendors_translation_result($id)
    {
        $this->db->select('vendors_translation.*');
        $this->db->join('vendors_translation','vendors.id = vendors_translation.vendor_id');
        $this->db->where('vendors.id',$id);
        $query = $this->db->get('vendors');
        
        if($query)
        {
            return $query->result();
        }
    }
    
    public function get_vendors($lang_id, $stores_ids=array())
    {
        $this->db->select('vendors_translation.*, vendors.*, stores_translation.name as store_name');
        
        $this->db->join('vendors_translation', 'vendors.id = vendors_translation.vendor_id');
        $this->db->join('stores_translation', 'vendors.store_id = stores_translation.store_id AND stores_translation.lang_id ='.$lang_id);
        
        $this->db->where('vendors_translation.lang_id',$lang_id);
        
        if(count($stores_ids) != 0)
        {
            $this->db->where_in('vendors.store_id', $stores_ids);
        }
        
        $this->db->order_by('vendors.store_id', 'desc');
        
        $query = $this->db->get('vendors');
        
        if($query)
        {
            return $query->result();
        }
    }
    
    public function get_vendor($vendor_id , $lang_id)
    {
        $this->db->select('title');
        
        $this->db->where('vendor_id',$vendor_id);
        $this->db->where('lang_id',$lang_id);
        
        $query = $this->db->get('vendors_translation');
        
        if($query)
        {
            return $query->row();
        }
    }
    /**********************Update*******************************/
    public function update_vendors($vendor_id,$data)
    {
        $this->db->where('id',$vendor_id);
        return $this->db->update('vendors',$data);
    }
    public function update_vendors_translation($vendor_id,$lang_id,$vendors_translation_data)
    {
        $this->db->where('vendor_id',$vendor_id);
        $this->db->where('lang_id',$lang_id);
        return $this->db->update('vendors_translation',$vendors_translation_data);
    }
   
    /**********************DELETE*******************************/ 
    public function delete_vendors_translation($vendor_id)
    {
        $this->db->where('vendor_id',$vendor_id);
        $this->db->delete('vendors_translation'); 
    }
    
    public function get_count_all_vendors($lang_id ,$search_word ='',$country_id=0)
    {
        $this->db->join('vendors_translation' ,'vendors.id = vendors_translation.vendor_id');
        
        if(trim($search_word) !='')
        {
            $this->db->like('vendors_translation.title', $search_word, 'both');  
            $this->db->or_like('vendors_translation.description', $search_word, 'both');
        }
        if($country_id != 0)
        {
             $this->db->where('vendors.country_id',$country_id);
        }
        
        $this->db->where('vendors_translation.lang_id',$lang_id);
        
        return $this->db->count_all_results('vendors');
    }
    
    public function get_vendors_data($lang_id,$limit,$offset,$search_word='',$country_id=0,$order_by='',$order_state)
    {
        $this->db->select('vendors_translation.* , vendors.* ,countries_translation.*, vendors.id as id, countries_translation.name as country, stores_translation.name as store_name');
        
        $this->db->join('vendors_translation' ,'vendors.id = vendors_translation.vendor_id');
        $this->db->join('countries_translation','vendors.country_id = countries_translation.country_id');
        $this->db->join('stores_translation','vendors.store_id = stores_translation.store_id AND stores_translation.lang_id ='.$lang_id, 'left');
        
        $this->db->where('countries_translation.lang_id',$lang_id);
        $this->db->where('vendors_translation.lang_id',$lang_id);
        
        if(trim($search_word) !='')
        {
            $this->db->where('(vendors_translation.title LIKE "%'.$search_word.'%" OR countries_translation.name LIKE "%'.$search_word.'%"  OR stores_translation.name LIKE "%'.$search_word.'%")');
        }
        
        if($country_id != 0)
        {
             $this->db->where('vendors.country_id',$country_id);
        }
        
        if($order_by != '')
        {
            if($order_by == lang('country'))
            {
                $this->db->order_by('countries_translation.name', $order_state);
            }
            elseif($order_by == lang('title'))
            {
                $this->db->order_by('vendors_translation.title', $order_state);
            }
            elseif($order_by == lang('description'))
            {
                $this->db->order_by('vendors_translation.description', $order_state);
            }
            elseif($order_by == lang('name_of_store'))
            {
                $this->db->order_by('stores_translation.name', $order_state);
            }
            else
            {
                $this->db->order_by('vendors.id', $order_state);
            }
        }
        else
        {
            $this->db->order_by('vendors.id', $order_state);
        }
        
        $result = $this->db->get('vendors', $limit, $offset);

        if($result)
        {
            return $result->result();    
        }
    }
    
    public function get_vendor_currency($vendor_id)
    {
        $this->db->select('currencies.currency_symbol');
        
        $this->db->join('countries', 'vendors.country_id = countries.id');
        $this->db->join('currencies', 'countries.currency_id = currencies.id');
        
        $this->db->where('vendors.id', $vendor_id);
        
        $query = $this->db->get('vendors');
        
        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }
    
    public function delete_vendor_data($vendor_id_array)
    {
        $this->db->where_in('vendor_id',$vendor_id_array);
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
    
    public function get_row_data($id,$display_lang_id)
    {
        $this->db->select('vendors_translation.* , vendors.* ,countries_translation.*, vendors.id as id, countries_translation.name as country');
        
        $this->db->join('vendors_translation' ,'vendors.id = vendors_translation.vendor_id');
        $this->db->join('countries_translation','vendors.country_id = countries_translation.country_id');
        
        $this->db->where('vendors.id',$id);
        $this->db->where('countries_translation.lang_id',$display_lang_id);
        $this->db->where('vendors_translation.lang_id',$display_lang_id);
        
        $result = $this->db->get('vendors');

        if($result)
        {
            return $result->row();    
        }
    }
    
    public function get_vendor_store_data($vendor_id, $lang_id)
    {
        $this->db->select('stores_translation.*');//, vendores_translation.*');
        
        //$this->db->join('vendors_translation', 'vendors.id = vendors_translation.vendor_id AND vendors_translation.lang_id ='.$lang_id);
        $this->db->join('stores_translation', 'vendors.store_id = stores_translation.store_id AND stores_translation.lang_id ='.$lang_id);
        
        $this->db->where('vendors.id', $vendor_id);
        
        $query = $this->db->get('vendors');
        
        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }
/****************************************************************/
}