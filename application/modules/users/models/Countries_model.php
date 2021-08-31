<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Countries_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        
    }
    public function get_countries($lang_id)
    {
        $this->db->select('countries_translation.*,countries.*, currencies.*, currencies_translation.name as currency, countries.id as id');
        
        $this->db->join('countries_translation','countries.id = countries_translation.country_id');
        $this->db->join('currencies','countries.currency_id = currencies.id', 'left');
        $this->db->join('currencies_translation','countries.currency_id = currencies_translation.currency_id AND currencies_translation.lang_id ='.$lang_id);
        
        $this->db->where('countries_translation.lang_id',$lang_id);
        $query = $this->db->get('countries');
        
        if($query)
        {
            return $query->result();
        }
        else
        {
            return false;
        }
    }
    
    public function get_product_countries($product_id,$lang_id)
    {
        $this->db->select('countries_translation.*,countries.*, products_countries.price as country_price, 
        products_countries.country_id as product_country,products_countries.product_id as product_id');
        
        $this->db->join('countries_translation','countries.id = countries_translation.country_id');
        $this->db->join('products_countries','countries.id = products_countries.country_id');
        
        $this->db->where('countries_translation.lang_id',$lang_id);
        $this->db->where('products_countries.product_id',$product_id);
        
        $query = $this->db->get('countries');
        
        if($query)
        {
            return $query->result();
        }   
    }
    public function insert_countries($data)
    {
        return $this->db->insert('countries', $data);
    }
    
    public function insert_countries_translation($countries_translation_data)
    {
        return $this->db->insert('countries_translation', $countries_translation_data);
    }
    
    public function get_countries_translation_result($id)
    {
        $this->db->select('countries_translation.*');
        $this->db->join('countries_translation','countries.id = countries_translation.country_id');
        $this->db->where('countries.id',$id);
        $query = $this->db->get('countries');
        
        if($query)
        {
            return $query->result();
        }
    }
    
    public function get_countries_result($id)
    {
        $this->db->where('id',$id);
        $query = $this->db->get('countries');
        
        if($query)
        {
            return $query->row();
        }
    }
    
    public function update_countries_translation($country_id,$lang_id,$countries_translation_data)
    {
        $this->db->where('country_id',$country_id);
        $this->db->where('lang_id',$lang_id);
        return $this->db->update('countries_translation',$countries_translation_data);
    }
    
    public function update_countries($country_id,$data)
    {
        $this->db->where('id',$country_id);
        return $this->db->update('countries',$data);
    }
    public function delete_countries_translation($country_id)
    {
        $this->db->where('country_id',$country_id);
        $this->db->delete('countries_translation'); 
    }
    public function get_country($id,$lang_id)
    {
        $this->db->select('countries_translation.*');
        $this->db->join('countries_translation','countries.id = countries_translation.country_id');
        $this->db->where('countries.id',$id);
        $this->db->where('countries_translation.lang_id',$lang_id);
        $query = $this->db->get('countries');
        
        if($query)
        {
            return $query->row();
        }
    }
    
    public function get_countries_data($lang_id, $limit, $offset, $search_word='', $order_by='', $order_state)
    {
        $this->db->select('countries.*, countries_translation.*, currencies_translation.name as currency_symbol');
        
        $this->db->join('countries_translation','countries.id = countries_translation.country_id');
        $this->db->join('currencies_translation','countries.currency_id = currencies_translation.currency_id AND currencies_translation.lang_id ='.$lang_id, 'left');
        
        if(trim($search_word) !='')
        {
            $this->db->like('countries_translation.name', $search_word, 'both');  
        }
        else
        {
            $this->db->where('countries_translation.lang_id',$lang_id);
        }
        
        if($order_by != '')
        {
            if($order_by == lang('country'))
            {
                $this->db->order_by('countries_translation.name', $order_state);
            }
            elseif($order_by == lang('currency'))
            {
                $this->db->order_by('countries.currency_symbol', $order_state);
            }
            else
            {
                $this->db->order_by('countries.id',$order_state);
            }
        }
        else
        {
            $this->db->order_by('countries.id',$order_state);
        }
        
        $result = $this->db->get('countries',$limit,$offset);

        if($result)
        {
            return $result->result();    
        }
        
    }
    
    
    public function get_count_all_countries($lang_id ,$search_word ='',$country_id=0)
    {
        $this->db->join('countries_translation' ,'countries.id = countries_translation.country_id');
        $this->db->join('currencies_translation','countries.currency_id = currencies_translation.currency_id AND currencies_translation.lang_id ='.$lang_id);
        
        if(trim($search_word) !='')
        {
            $this->db->like('countries_translation.name', $search_word, 'both');  
        }
        
        $this->db->where('countries_translation.lang_id',$lang_id);
        
        return $this->db->count_all_results('countries');
    }
    
    public function get_row_data($row_id,$display_lang_id)
    {
        $this->db->select('countries_translation.* , countries.*, currencies.currency_symbol');
        
        $this->db->join('countries_translation','countries.id = countries_translation.country_id');
        $this->db->join('currencies','countries.currency_id = currencies.id');
        
        $this->db->where('countries_translation.lang_id',$display_lang_id);
        $this->db->where('countries.id',$row_id);
        
        $result = $this->db->get('countries');
        
        if($result)
        {
            return $result->row();
        }
        
    }
    
    public function get_active_countries_data($active_lang_id)
    {
        $this->db->select('countries.*, countries_translation.name, countries.id as id');
        
        $this->db->join('countries_translation', 'countries.id = countries_translation.country_id');
        
        $this->db->where('countries_translation.lang_id', $active_lang_id);
        
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
   public function delete_country_data($countries_id_array)
   {
        $this->db->where_in('country_id',$countries_id_array);
        $product_count = $this->db->count_all_results('products_countries');
        
        if($product_count > 0)
        {
            echo lang('error_existing_products');
        }
        else
        {
            $this->db->where_in('id',$countries_id_array);
            $this->db->delete('countries');
            
            $this->db->where_in('country_id',$countries_id_array);
            $this->db->delete('countries_translation');
            
            echo '1';
        }
   } 
   public function get_all_countries($active_lang_id)
   {        
        $this->db->select('countries_translation.name, countries.* , countries.id as id');
        $this->db->join('countries_translation', 'countries.id = countries_translation.country_id');
        
        $this->db->where('countries_translation.lang_id', $active_lang_id);
        
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
   
   public function get_reward_points($country_id)
   {
        $this->db->where('id', $country_id);
        $row = $this->db->get('countries');
        
        if($row)
        {
            return $row->row()->reward_points;
        }
        else
        {
            return false;
        }
   }
   
   public function get_reward_points_data($product_id, $country_id)
   {
        $this->db->where('product_id', $product_id);
        $this->db->where('country_id', $country_id);
        
        $row = $this->db->get('products_countries');
        
        if($row)
        {
            return $row->row();
        }
        else
        {
            return false;
        }
   }
   
   public function get_country_name($country_id, $lang_id)
   {
        $this->db->where('country_id', $country_id);
        $this->db->where('lang_id', $lang_id);
        
        $row = $this->db->get('countries_translation');
                
        if($row->row())
        {
            return $row->row()->name;
        }
        else
        {
            return false;
        }
   }
   
   public function get_currency_symbol($lang_id, $country_id)
   {
        $this->get_country_symbol($country_id);
        /*$this->db->where('id', $country_id);
        
        $row = $this->db->get('countries');
        
        if($row)
        {
            return $row->row()->currency_symbol;
        }
        else
        {
            return false;
        }*/
        
   }
   
   public function get_country_symbol($country_id)
   {
        $this->db->select('currencies.*');
        $this->db->join('currencies', 'countries.currency_id = currencies.id');
        $this->db->where('countries.id', $country_id);
        
        $row = $this->db->get('countries');
        
        if($row)
        {
            return $row->row()->currency_symbol;
        }
        else
        {
            return false;
        }
   }
   
   public function get_countries_filter($lang_id)
    {
        $this->db->select('countries_translation.*, countries_translation.country_id as id');
        
        $this->db->where('countries_translation.lang_id',$lang_id);
        $query = $this->db->get('countries_translation');
        
        if($query)
        {
            return $query->result();
        }
        else
        {
            return false;
        }
    }
/****************************************************************/
}