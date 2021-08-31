<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Careers_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    /**********Insert *************/
   
   public function save_careers_form_data($data)
   {
       return $this->db->insert('careers', $data);
   }
   /**********************GET*******************************/
   public function get_count_all_messages($search_word ='')
   {
        if(trim($search_word) !='')
        {
            $this->db->where('(careers.name LIKE "%'.$search_word.'%") OR careers.email LIKE "%'.$search_word.'%")');
        }
        
        return $this->db->count_all_results('careers');
   }
   
    public function get_grid_data($limit,$offset,$search_word='', $order_state='desc')
    {
        
        if(trim($search_word) !='')
        {
            $this->db->where('(careers.name LIKE "%'.$search_word.'%") OR careers.email LIKE "%'.$search_word.'%")'); 
        }
        
        $this->db->order_by('id', $order_state);
        
        $result = $this->db->get('careers', $limit, $offset);

        if($result)
        {
            return $result->result();    
        }
        else
        {
            return false;
        }
    }
    
    public function get_row_data($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('careers');
        
        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }
    
    public function delete_data($ids_array)
    {
        $this->db->where_in('id', $ids_array);
        $this->db->delete('careers');
        
        echo '1';
    }
   
    
   
/****************************************************************/
}