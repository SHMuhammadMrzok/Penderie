<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Maintenance_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    
    /**********************GET*******************************/
    public function get_count_all_messages($search_word ='')
    {
        
        if(trim($search_word) !='')
        {
            $this->db->like('name', $search_word, 'both');  
        }
        return $this->db->count_all_results('maintenance');
    }
    
    public function get_contact_data($limit,$offset,$search_word='',$order_by='',$order_state)
    {
        
        if(trim($search_word) !='')
        {
            $this->db->like('name', $search_word, 'both');
        }
        
        
        if($order_by != '')
        {
            
            if($order_by == lang('name'))
            {
                $this->db->order_by('name',$order_state);
            }
            elseif($order_by == lang('mobile'))
            {
                $this->db->order_by('phone',$order_state);
            }elseif($order_by == lang('title'))
            {
                $this->db->order_by('title',$order_state);
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
        
        $result = $this->db->get('maintenance',$limit,$offset);

        if($result)
        {
            return $result->result();    
        }
    }
    
    public function get_row_data($id)
    {
        $this->db->where('id',$id);
        $result = $this->db->get('maintenance');

        if($result)
        {
            return $result->row();    
        }
    }
  
  /***************Update**************************/
  public function update_row_data($id,$read_data)
  {
        $this->db->where('id',$id);
        $this->db->update('maintenance',$read_data);
  }
    /**********************DELETE*******************************/ 
      
    public function delete_message_data($id_array)
    {
        $this->db->where_in('id',$id_array);
        $this->db->delete('maintenance');
        
        echo '1';
    }
    
    public function insert_message($data)
    {
        return $this->db->insert('maintenance', $data);
    }
    
  
/****************************************************************/
}