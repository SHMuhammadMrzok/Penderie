<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Advantages_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    /**********************INSERT*******************************/
    public function insert_advantages($data)
    {
        return $this->db->insert('products_advantages', $data);
    }
    
    public function insert_advantages_translation($spam_translation_data)
    {
        return $this->db->insert('products_advantages_translation', $spam_translation_data);
    }
    
    
    
    /**********************GET*******************************/
    public function get_advantage_row($id)
    {
        $this->db->where('id',$id);
        $query = $this->db->get('products_advantages');
        
        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }
    public function get_advantages_translation_result($id)
    {
        
        $this->db->where('products_advantages_translation.adv_id', $id);
        $query = $this->db->get('products_advantages_translation');
        
        if($query)
        {
            return $query->result();
        }
        else
        {
            return false;
        }
    }
    
    
    /**********************Update*******************************/
    public function update_adv($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('products_advantages',$data);
    }
    public function update_advantages_translation($id, $lang_id, $spam_translation_data)
    {
        $this->db->where('adv_id', $id);
        $this->db->where('lang_id', $lang_id);
        return $this->db->update('products_advantages_translation', $spam_translation_data);
    }
   
    /**********************DELETE*******************************/ 
    public function delete_advantages_data($ids_array)
    {
        $this->db->where_in('id', $ids_array);
        $this->db->delete('products_advantages');
        
        $this->db->where_in('adv_id', $ids_array);
        $this->db->delete('products_advantages_translation');
        
        echo '1'; 
    }
    
    public function get_count_all_advantages($lang_id, $stores_ids=array(), $search_word ='')
    {
        $this->db->join('products_advantages_translation' ,'products_advantages.id = products_advantages_translation.adv_id
                        AND products_advantages_translation.lang_id='.$lang_id);
        
        if(count($stores_ids) != 0)
        {
            $this->db->where_in('products_advantages.store_id', $stores_ids);
        }
        
        if(trim($search_word) !='')
        {
            $this->db->like('products_advantages_translation.name', $search_word, 'both');  
        }
        
        return $this->db->count_all_results('products_advantages');
    }
    
    public function get_advantages_data($lang_id, $limit=0, $offset=0, $search_word='', $order_by='', $order_state='desc', $stores_ids=array())
    {
        $this->db->select('products_advantages_translation.* , products_advantages.*, products_advantages_translation.name as spam_name');
        
        $this->db->join('products_advantages_translation' ,'products_advantages.id = products_advantages_translation.adv_id
                        AND products_advantages_translation.lang_id='.$lang_id);
        
        if(trim($search_word) !='')
        {
            $this->db->like('products_advantages_translation.name', $search_word, 'both');  
        }
        
        if(count($stores_ids) != 0)
        {
            $this->db->where_in('products_advantages.store_id', $stores_ids);
        }
        
        $this->db->order_by('products_advantages.id', $order_state);
        
        if($limit != 0)
        {
            $result = $this->db->get('products_advantages', $limit, $offset);
        }
        else
        {
            $result = $this->db->get('products_advantages');
        }

        if($result)
        {
            return $result->result();    
        }
        else
        {
            return false;
        }
    }
    
    public function get_row_data($id, $display_lang_id)
    {
        $this->db->select('products_advantages_translation.* , products_advantages.*, stores_translation.name as store_name ');
        
        $this->db->join('products_advantages_translation' ,'products_advantages.id = products_advantages_translation.adv_id');
        $this->db->join('stores_translation' ,'products_advantages.store_id = stores_translation.store_id
                        AND stores_translation.lang_id='.$display_lang_id);
        
        $this->db->where('products_advantages.id',$id);
        $this->db->where('products_advantages_translation.lang_id',$display_lang_id);
        
        $result = $this->db->get('products_advantages');

        if($result)
        {
            return $result->row();    
        }
        else
        {
            return false;
        }
    }
    
    public function get_all_advantages($display_lang_id, $store_id=0, $advs_ids=array())
    {
        $this->db->select('products_advantages_translation.* , products_advantages.*');
        
        $this->db->join('products_advantages_translation' ,'products_advantages.id = products_advantages_translation.adv_id');
        $this->db->where('products_advantages_translation.lang_id', $display_lang_id);
        $this->db->where('products_advantages.active', 1);
        
        if($store_id != 0)
        {
            $this->db->where('products_advantages.store_id', $store_id);
        }
        
        if(count($advs_ids) != 0)
        {
            $this->db->where_in('products_advantages.id', $advs_ids);
        }
        
        $result = $this->db->get('products_advantages');

        if($result)
        {
            return $result->result();    
        }
        else
        {
            return false;
        }
    }
    
    
    
/****************************************************************/
}