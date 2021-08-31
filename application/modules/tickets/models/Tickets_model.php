<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Tickets_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    /****************Get****************/
  
    public function get_count_all_tickets($user_id)
    {
      $this->db->where('user_id',$user_id);
      return $this->db->count_all_results('tickets');
    }
    
    public function get_tickets($lang_id ,$limit,$offset,$user_id)
    {
        $this->db->select('tickets_categories_translation.title as cat_title, tickets_status_translation.title as status_title,users.username as last_updated_by_name, tickets.* ');
        
        $this->db->join('tickets_categories_translation', 'tickets.cat_id = tickets_categories_translation.ticket_cat_id');
        $this->db->join('tickets_status_translation', 'tickets.status_id  = tickets_status_translation.ticket_status_id');
        $this->db->join('users', 'tickets.last_updated_by  = users.id');
        
        $this->db->where('tickets_categories_translation.lang_id',$lang_id);
        $this->db->where('tickets_status_translation.lang_id',$lang_id);
        $this->db->where('tickets.user_id',$user_id);
        
        $this->db->order_by('id','desc');
        
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
        else
        {
            return false;
        }
    }
    
    public function get_ticket_detials($id,$lang_id)
    {
        $this->db->select('tickets_categories_translation.title as cat_title, tickets.* ,tickets.id as id , tickets_status_translation.title as status_title');
        $this->db->join('tickets_categories_translation', 'tickets.cat_id = tickets_categories_translation.ticket_cat_id');
        $this->db->join('tickets_status_translation', 'tickets.status_id  = tickets_status_translation.ticket_status_id');
        $this->db->join('users', 'tickets.last_updated_by  = users.id');
        
        $this->db->where('tickets_status_translation.lang_id',$lang_id);
        
        $this->db->where('tickets.id',$id);
       
        $this->db->where('tickets_categories_translation.lang_id',$lang_id);
        
        $result = $this->db->get('tickets');

        if($result)
        {
            return $result->row();      
        }
    }
    
    public function get_ticket_last_updated_by_name($user_id)
    {
        $this->db->where('id',$user_id);
        
        $result = $this->db->get('users');

        if($result)
        {
             return $result->row();      
        }
    }
    
    public function get_ticket_posts($id)
    {
        $this->db->select('users.username as username,users.image as user_image, tickets_posts.* ');
        $this->db->join('users', 'tickets_posts.user_id = users.id');
        
        $this->db->where('tickets_posts.ticket_id',$id);
        $result = $this->db->get('tickets_posts');

        if($result)
        {
            return $result->result();    
        }
    }
   
   public function get_tickets_orders($user_id)
   {
        $this->db->where('user_id',$user_id);
        $this->db->where('order_status_id', 1);
        
        $result = $this->db->get('orders');

        if($result)
        {
            return $result->result();    
        }
   }
   
   public function get_completed_order_serials($order_id)
   {
        $this->db->select('products_serials.id as id,products_serials.serial as serial,orders.id as order_id');
        
        $this->db->join('products_serials', 'orders_serials.product_serial_id = products_serials.id');
        $this->db->join('orders', 'orders_serials.order_id = orders.id');
        //$this->db->join('order_status_translation', 'orders.order_status_id = order_status_translation.status_id');
        //,order_status_translation.name as order_status
        $this->db->where('orders_serials.order_id',$order_id);
        $this->db->where('orders.id',$order_id);
        $this->db->where('orders.order_status_id',1);
        
        //$this->db->where('order_status_translation.id',$order_id);
        
        $result = $this->db->get('orders_serials');

        if($result)
        {
            return $result->result();    
        
        }else{
            return false ;
        }
   }
   
   public function get_ticket_owner_data($ticket_id)
   {
        $this->db->select('users.*');
        $this->db->join('users', 'tickets.user_id = users.id');
        
        $this->db->where('tickets.id', $ticket_id);
        $query = $this->db->get('tickets');
        
        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
   }
   /*************************DELETE*******************************/
   
    public function delete_ticket_data($tickets_id_array)
    {
        $this->db->where_in('id',$tickets_id_array);
        $this->db->delete('tickets');
        
        $this->db->where_in('ticket_id', $tickets_id_array);
        $this->db->delete('tickets_posts');
        
        echo '1';  
    }
    
  /*****************INSERT***************************************/
  
   public function insert_ticket($data)
    {
                
        return $this->db->insert('tickets',$data);
    } 
  
   public function insert_ticket_serials($tickets_serials_data)
   {
        return $this->db->insert('tickets_serials',$tickets_serials_data);
   }  
   
   public function insert_ticket_post($data)
   {
         return $this->db->insert('tickets_posts',$data);
   }
    /***********************UPDATE*************************/
    
    public function update_category($cat_id,$category_data)
    {
        $this->db->where('id',$cat_id);
        $this->db->update('tickets',$category_data);
    }  
   
   public function update_ticket_status($ticket_id , $ticket_status)
   {
        $this->db->where('id',$ticket_id);
        $this->db->update('tickets',$ticket_status);
   } 
     
/////////////////////////////////////////////////   
}