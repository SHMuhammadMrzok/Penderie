<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Admin_tickets_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    /****************Get****************/
  
    public function get_count_all_tickets($search_word='', $stores_ids=array())
    {
        if(trim($search_word) !='')
        {
            $this->db->like('title', $search_word, 'both');
            $this->db->like('details', $search_word, 'both'); 
        }
        
        if(count($stores_ids) != 0)
        {
            $this->db->where_in('store_id', $stores_ids);
        }
        
      return $this->db->count_all_results('tickets');
    }
    
    public function get_tickets_data($limit, $offset, $search_word='', $order_by='', $order_state, $stores_ids)
    {
        
        if(trim($search_word) !='')
        {
            $this->db->like('title', $search_word, 'both');
            //$this->db->like('details', $search_word, 'both'); 
            
        }
        
        if($order_by != '')
        {
            if($order_by == lang('title'))
            { 
                $this->db->order_by('title',$order_state);
            }
            elseif($order_by == lang('cat_id'))
            {
                $this->db->order_by('cat_id',$order_state);
            }
            elseif($order_by == lang('status_id'))
            {
                $this->db->order_by('status_id',$order_state);
            }
            elseif($order_by == lang('user_id'))
            {
                $this->db->order_by('user_id',$order_state);
            }
            elseif($order_by == lang('order_id'))
            {
                $this->db->order_by('order_id',$order_state);
            }
            elseif($order_by == lang('assigned_to'))
            {
                $this->db->order_by('assigned_to',$order_state);
            }
            elseif($order_by == lang('last_updated_by'))
            {
                $this->db->order_by('last_updated_by',$order_state);
            }
            elseif($order_by == lang('active'))
            {
                $this->db->order_by('active',$order_state);
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
        
        if(count($stores_ids) != 0)
        {
            $this->db->where_in('store_id', $stores_ids);
        }
        
        $result = $this->db->get('tickets',$limit,$offset);
 
        if($result)
        {
            return $result->result();    
        }
    }
    
    public function get_row_data($id)
    {
        $this->db->where('id',$id);
        $result = $this->db->get('tickets');

        if($result)
        {
            return $result->row();    
        }
    }
    
    public function get_ticket_status($id,$lang_id)
    {
        $this->db->select('tickets.id as id ,tickets.status_id ,tickets_status_translation.title');
        $this->db->join('tickets_status_translation','tickets.status_id = tickets_status_translation.ticket_status_id');
        
        $this->db->where('tickets.id',$id);
        $this->db->where('tickets_status_translation.lang_id',$lang_id);
        $result = $this->db->get('tickets');

        if($result)
        {
            return $result->row();    
        }
    }
    
    public function get_ticket_details($id)
    {
        $this->db->select('tickets_posts.*, tickets.*, tickets.id as id');
        $this->db->join('tickets_posts','tickets.id = tickets_posts.ticket_id', 'left');
        
        $this->db->where('tickets.id',$id);
        $result = $this->db->get('tickets');

        if($result)
        {
            return $result->row();    
        } 
    }
   
   public function get_ticket_serials($id,$order_id)
   {
        $this->db->select('products_serials.serial ,tickets_serials.serial_id as serial_id');
        $this->db->join('products_serials','tickets_serials.serial_id = products_serials.id');
        
        $this->db->where('tickets_serials.ticket_id',$id);
        $this->db->where('tickets_serials.order_id',$order_id);
        $result = $this->db->get('tickets_serials');

        if($result)
        {
            return $result->result();    
        } 
   }
   
   public function get_admin_group_users()
   {
        $this->db->select('users.*');
        $this->db->join('users_groups','users.id = users_groups.user_id');
        $this->db->join('groups','users_groups.group_id = groups.id');
        
        $this->db->where('groups.id',1);
        $this->db->where('users_groups.group_id',1);
        
        $result = $this->db->get('users');

        if($result)
        {
            return $result->result();    
        } 
   }
   /*************************DELETE*******************************/
   
    public function delete_ticket_data($tickets_id_array)
    {
        $this->db->where_in('id',$tickets_id_array);
        $this->db->delete('tickets');
        
        
        echo '1';  
    }
    
  /*****************INSERT***************************************/
  
   public function insert_tickets($data)
    {
                
        return $this->db->insert('tickets',$data);
    } 
    
   
    /***********************UPDATE*************************/
    
    public function update_category($cat_id,$category_data)
    {
        $this->db->where('id',$cat_id);
        $this->db->update('tickets',$category_data);
    }  
    
    public function update_ticket_status($ticket_id,$general_data)
    {
        $this->db->where('id',$ticket_id);
        $this->db->update('tickets',$general_data);
    }
     
/////////////////////////////////////////////////   
}