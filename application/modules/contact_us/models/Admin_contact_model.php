<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Admin_contact_model extends CI_Model
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
            $this->db->or_like('email', $search_word, 'both');
        }
        return $this->db->count_all_results('contact_us');
    }
    
    public function get_contact_data($limit,$offset,$search_word='',$order_by='',$order_state)
    {
        
        if(trim($search_word) !='')
        {
            $this->db->like('name', $search_word, 'both');  
            $this->db->or_like('email', $search_word, 'both'); 
        }
        
        
        if($order_by != '')
        {
            
            if($order_by == lang('name'))
            {
                $this->db->order_by('name',$order_state);
            }
            elseif($order_by == lang('email'))
            {
                $this->db->order_by('email',$order_state);
            
            }elseif($order_by == lang('mobile'))
            {
                $this->db->order_by('mobile',$order_state);
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
        
        $result = $this->db->get('contact_us',$limit,$offset);

        if($result)
        {
            return $result->result();    
        }
    }
    
    public function get_row_data($id)
    {
        $this->db->where('id',$id);
        $result = $this->db->get('contact_us');

        if($result)
        {
            return $result->row();    
        }
    }
    
    public function get_admin_replay($contact_us_id)
    {
        $this->db->where('contact_us_id',$contact_us_id);
        $result = $this->db->get('contact_us_reply');

        if($result)
        {
            return $result->result();    
        }
    }
  
  /***************Update**************************/
  public function update_row_data($id,$read_data)
  {
        $this->db->where('id',$id);
        $this->db->update('contact_us',$read_data);
  }
  /**********************DELETE*******************************/ 
      
  public function delete_message_data($id_array)
  {
      $this->db->where_in('id',$id_array);
      $this->db->delete('contact_us');
      
      echo '1';
  }
  /**********************Insert*******************************/
    
  public function insert_admin_replay($replay_data)
  {
      $this->db->insert('contact_us_reply',$replay_data);
  }
    
  
/****************************************************************/
}