<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Products_serials_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    
    /******************DELETE**********************/
    public function delete_products_serials($ids_array)
    {
        $this->db->where_in('id',$ids_array);
        $this->db->delete('products_serials');
    }
    /********************Insert *****************************/
    public function insert_products_serials($products_serials_data)
    {
        return $this->db->insert('products_serials', $products_serials_data);
    }
    
    public function insert_serial_log($data)
    {
        $this->db->insert('serials_log', $data);
    }
    
   /******************************Update**********************************/

    public function update_serial($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('products_serials', $data);
    }
   /********************GET*****************************/ 
    public function get_purchase_order_data($purchase_order_id, $lang_id)
    {
        $this->db->select('purchase_orders.*,purchase_orders_products.*,products_translation.title,countries_translation.name');
        
        $this->db->join('purchase_orders_products','purchase_orders.id = purchase_orders_products.purchase_order_id');
        $this->db->join('products_translation','products_translation.product_id = purchase_orders_products.product_id');
        $this->db->join('countries_translation','countries_translation.country_id = purchase_orders_products.country_id AND purchase_orders_products.country_id != 0 AND countries_translation.lang_id = '.$lang_id, 'left');
        
        $this->db->where('purchase_orders.id',$purchase_order_id);
        $this->db->where('products_translation.lang_id',$lang_id);
        
        $query = $this->db->get('purchase_orders');
        
        if($query)
        {
            return $query->result();
        }else{
            return false ;
        }
    }   
  
    public function get_products_serials_row($id)
    {
        $this->db->where('id',$id);
        $query = $this->db->get('products_serials');
        
        if($query)
        {
            return $query->row();
        }else{
            return false ;
        }
    }
  
   public function get_product_available_serials($product_id)
    {
        $this->db->where('product_id',$product_id);
        //$this->db->where('country_id',$country_id);
        $this->db->where('serial_status',0);
        
        return $this->db->count_all_results('products_serials');
        
    }
    
    public function get_product_available_serial_count($product_id, $country_id)
    {
        $this->db->where('serial_status', 0);
        $this->db->where('product_id', $product_id);
        $this->db->where('country_id', $country_id);
        
        return $this->db->count_all_results('products_serials');
    }
    
    public function get_product_available_serial_data($product_id, $country_id)
    {
        $this->db->where('serial_status', 0);
        $this->db->where('product_id', $product_id);
        $this->db->where('country_id', $country_id);
        
        $query = $this->db->get('products_serials');
        
        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }
    
    public function get_product_country_available_serials($product_id , $country_id)
    {
        $this->db->where('product_id',$product_id);
        $this->db->where('country_id',$country_id);
        $this->db->where('serial_status',0);
        
        return $this->db->count_all_results('products_serials');
        
    }
    
    public function get_count_all_products_serials($lang_id, $search_word='', $order_by='', $order_state='', $purchase_orders_filters_id=0, $products_filters_id=0, $status_filter_id='', $countries_filter_id=0, $date_from=0, $date_to=0)
    {
        if(trim($search_word) !='')
       {
           $this->db->like('products_serials.serial', $search_word, 'both');
       }
        
       if($order_by != '')
       {
          if($order_by == lang('order_number'))
          {
            $this->db->order_by('products_serials.purchase_order_id',$order_state);
          }
          elseif($order_by == lang('product_name'))
          {
            $this->db->order_by('products_translation.title',$order_state);
          
          }elseif($order_by == lang('country_name'))
          {
            $this->db->order_by('countries_translation.name',$order_state);
          }
          elseif($order_by == lang('serial'))
          {
            $this->db->order_by('products_serials.serial',$order_state);
          }
          else
          {
            $this->db->order_by('products_serials.id',$order_state);
          }
       }
       else
       {
          $this->db->order_by('products_serials.id',$order_state);
       }
       
       if($purchase_orders_filters_id != 0)
       {
           $this->db->where('products_serials.purchase_order_id', $purchase_orders_filters_id);
       }
        
       if($products_filters_id != 0)
       {
           $this->db->where('products_serials.product_id', $products_filters_id);
       }
       
       if($status_filter_id != '')
       {
           if($status_filter_id == 3)
           {
               $this->db->where('products_serials.serial_status', 3);
           }
           elseif($status_filter_id == 100)
           {
               $this->db->where('products_serials.serial_status', 0);
           }
           else
           {
               $this->db->where('products_serials.serial_status', $status_filter_id);
           }
           
       }
       
       if($countries_filter_id != 0)
       {
            $this->db->where('products_serials.country_id', $countries_filter_id);
       }
       
       if($date_from != 0)
       {
            $this->db->where('products_serials.unix_time >=', $date_from);
       }
       
       if($date_to != 0)
       {
           $this->db->where('products_serials.unix_time <=', $date_to);
       }
       
        return $this->db->count_all_results('products_serials');
    }
    
    public function get_products_serials_data($limit, $offset, $search_word='', $lang_id, $order_by, $order_state, $purchase_orders_filters_id=0, $products_filters_id=0, $status_filter_id='', $countries_filter_id, $date_from, $date_to, $stores_filter_id=0, $stores_ids=array())
    {
       $this->db->select('products_serials.*,products_translation.title,countries_translation.name');
       
       $this->db->join('products_translation','products_translation.product_id = products_serials.product_id');
       $this->db->join('countries_translation','countries_translation.country_id = products_serials.country_id OR products_serials.country_id =0');
       $this->db->join('purchase_orders', 'products_serials.purchase_order_id = purchase_orders.id');
       
       $this->db->where('products_translation.lang_id',$lang_id);
       $this->db->where('countries_translation.lang_id',$lang_id);
       
       if(trim($search_word) !='')
       {
           $this->db->like('products_serials.serial', $search_word, 'both');
       }
        
       if($order_by != '')
       {
          if($order_by == lang('order_number'))
          {
            $this->db->order_by('products_serials.purchase_order_id',$order_state);
          }
          elseif($order_by == lang('product_name'))
          {
            $this->db->order_by('products_translation.title',$order_state);
          
          }elseif($order_by == lang('country_name'))
          {
            $this->db->order_by('countries_translation.name',$order_state);
          }
          elseif($order_by == lang('serial'))
          {
            $this->db->order_by('products_serials.serial',$order_state);
          }
          else
          {
            $this->db->order_by('products_serials.id',$order_state);
          }
       }
       else
       {
          $this->db->order_by('products_serials.id',$order_state);
       }
       
       if($purchase_orders_filters_id != 0)
       {
           $this->db->where('products_serials.purchase_order_id', $purchase_orders_filters_id);
       }
        
       if($products_filters_id != 0)
       {
           $this->db->where('products_serials.product_id', $products_filters_id);
       }
       
       if($status_filter_id != '')
       {
           if($status_filter_id == 3)
           {
               $this->db->where('products_serials.serial_status', 3);
           }
           elseif($status_filter_id == 100)
           {
               $this->db->where('products_serials.serial_status', 0);
           }
           else
           {
               $this->db->where('products_serials.serial_status', $status_filter_id);
           }
           
       }
       
       if($countries_filter_id != 0)
       {
            $this->db->where('products_serials.country_id', $countries_filter_id);
       }
       
       if($date_from != 0)
       {
            $this->db->where('products_serials.unix_time >=', $date_from);
       }
       
       if($date_to != 0)
       {
           $this->db->where('products_serials.unix_time <=', $date_to);
       }
       
       if($stores_filter_id != 0)
       {
           $this->db->where('purchase_orders.store_id ', $stores_filter_id);
       }
       
       if(count($stores_ids) != 0)
       {
           $this->db->where_in('purchase_orders.store_id ', $stores_ids);
       }
        
       $this->db->group_by('products_serials.id');
       
       $result = $this->db->get('products_serials',$limit,$offset);

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
        $this->db->select('products_serials.*,products_translation.title,countries_translation.name');
        $this->db->join('products_translation','products_translation.product_id = products_serials.product_id');
        $this->db->join('countries_translation','countries_translation.country_id = products_serials.country_id AND countries_translation.lang_id ='.$display_lang_id, 'left');
        
        $this->db->where('products_serials.id',$id);
        $this->db->where('products_translation.lang_id',$display_lang_id);
        //$this->db->where('countries_translation.lang_id',$display_lang_id);
        
        $result = $this->db->get('products_serials');

        if($result)
        {
            return $result->row();    
        }
        else
        {
            return false;
        }
    }
    
    public function get_purchase_orders_filter_data()
    {
            $this->db->select('purchase_orders.id as name ,purchase_orders.id as id');
            $query = $this->db->get('purchase_orders');
            
            if($query)
            {
                return $query->result();
            }
    }
    
    public function get_product_country_serial_count($purchase_number, $productkey, $countrytkey, $optional_fields = '', $selected_optional_fields = '')
    {
        $this->db->where('purchase_order_id', $purchase_number);
        $this->db->where('product_id', $productkey);

        // Mrzok Edit -- Add if condition, to count serials for Global Quantities  
        // IMPORTANT NOTE |||===>>> This condition should be removed if country_id setted correctly from serial insertion
        if($countrytkey == Null) 
            $countrytkey = 0;
        // End Edit - to be removed

        $this->db->where('country_id', $countrytkey);

        // Mrzok Edit -- These lines has been added , to select serials with related Optionals and it's options
        if($optional_fields != '')
            $this->db->where('optional_fields', $optional_fields);

        if($selected_optional_fields != '')
            $this->db->where('selected_optional_fields', $selected_optional_fields);
        // End Edit
                
        return $this->db->count_all_results('products_serials');
    }
    
    public function get_country_name($countryid,$lang_id)
    {
        $this->db->where('country_id',$countryid);
        $this->db->where('lang_id',$lang_id);
        
        $row = $this->db->get('countries_translation')->row();
        
        if($row)
        {
            return $row->name;
        }
        else
        {
            return false ;
        }
    }
    
    public function get_products_serials_row_count($serial)
    {
        $this->db->where('serial',$serial);
        
        return $this->db->count_all_results('products_serials');
    }
    
    public function get_serial_status()
    {
        $result = $this->db->get('products_serials_status')->result();
        
        if($result)
        {
            return $result;
        }
        else
        {
            return false;
        }
    }
    
    public function count_order_serials($purchase_order_id)
    {
        $this->db->where('purchase_order_id', $purchase_order_id);
        return $this->db->count_all_results('products_serials');
    }
    public function get_wholesaler_order_serial_data($order_id)
    {
        $this->db->select('products_serials.*');
        
        $this->db->join('orders_serials', 'products_serials.id = orders_serials.product_serial_id AND orders_serials.order_id ='.$order_id);
        
        $query = $this->db->get('products_serials');
        
        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }
    
    public function count_order_product_serials($product_id, $purchase_order_id, $country_id = 0, $selected_optional_fields='')
    {
        $this->db->where('product_id', $product_id);
        $this->db->where('purchase_order_id', $purchase_order_id);
        

        if($country_id != 0)
            $this->db->where('country_id', $country_id);

        if($selected_optional_fields != '')
            $this->db->where('selected_optional_fields', $selected_optional_fields);
            
        return $this->db->count_all_results('products_serials');
    }
    
    public function count_order_available_product_serials($product_id, $purchase_order_id)
    {
        $this->db->where('active', 1);
        $this->db->where('serial_status', 0);
        $this->db->where('product_id', $product_id);
        $this->db->where('purchase_order_id', $purchase_order_id);
        
        return $this->db->count_all_results('products_serials');
    }
    
    public function check_if_exist_serial($serial)
    {
        $this->db->where('serial', $serial);
        $count = $this->db->count_all_results('products_serials');
        
        if($count > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    public function check_available_serial($serial_id)
    {
        $this->db->where('id', $serial_id);
        $row = $this->db->get('products_serials')->row();
        
        if($row && $row->serial_status == 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    public function get_product_global_serials_count($product_id)
    {
        $this->db->where('product_id', $product_id);
        $this->db->where('country_id', 0);
        $this->db->where('serial_status', 0);
        $this->db->where('active', 1);
        
        return $this->db->count_all_results('products_serials');
    }
    
    public function get_country_product_serials_count($product_id, $country_id=NULL, $active=1)
    {
        $this->db->where('product_id', $product_id);
        $this->db->where('serial_status', 0);
        $this->db->where('active', $active);
        
        if($country_id != NULL)
        { 
            $this->db->where('country_id', $country_id);
        }
        
        return $this->db->count_all_results('products_serials');
    }
    
    public function get_per_country_product_serials_count($product_id, $country_id, $active=1)
    {
        $this->db->where('product_id', $product_id);
        $this->db->where('serial_status', 0);
        $this->db->where('active', $active);
        
        $this->db->where('country_id', $country_id);
        
        
        return $this->db->count_all_results('products_serials');
    }
    
    public function get_serial_order_id($serial_id)
    {
        $this->db->order_by('order_id', 'desc');
        $this->db->where('product_serial_id', $serial_id);
        
        $query = $this->db->get('orders_serials')->row();
        
        if($query)
        {
            return $query->order_id;
        }
        else
        {
            return false;
        }
    }
    
    public function get_serial_data($serial)
    {
        $this->db->where('serial', $serial);
        $query = $this->db->get('products_serials');
        
        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }
    
    public function get_product_global_avg_cost($product_id)
    {
        $this->db->where('product_id', $product_id);
        $this->db->where('country_id', 0);
        
        $this->db->order_by('purchase_order_id', 'desc');
        
        $query = $this->db->get('purchase_orders_products')->row();
        
        if($query)
        {
            return $query->price_per_unit;
        }
        else
        {
            return false;
        }
    }
    
    public function get_product_serials_count_grouped_by_options($product_id, $country_id=NULL, $active=1, $invalid=0)
    {
        $this->db->select('products_serials.id , products_serials.optional_fields , products_serials.selected_optional_fields,
         COUNT(products_serials.id) as serials_count', FALSE);
        $this->db->where('product_id', $product_id);
        $this->db->where('serial_status', 0);
        $this->db->where('active', $active);
        $this->db->where('invalid', $invalid);
        
        //$this->db->where('country_id', $country_id);
        
        $this->db->group_by('products_serials.selected_optional_fields');
        
        // return $this->db->count_all_results('products_serialss');

        $result = $this->db->get('products_serials')->result();
        
        if($result)
        {
            return $result;
        }
        else
        {
            return false;
        }
    }
    
    /**************************************************************/
}