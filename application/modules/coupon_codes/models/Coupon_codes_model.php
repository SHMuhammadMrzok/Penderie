<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Coupon_codes_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    /**********************Delete********************************/
    public function delete_coupon_data($coupon_id)
    {
        $this->db->where_in('id',$coupon_id);
        $this->db->delete('coupon_codes'); 
       
        $this->db->where_in('coupon_id',$coupon_id);
        $this->db->delete('coupon_codes_translation'); 
        
        $this->db->where_in('coupon_id',$coupon_id);
        $this->db->delete('coupon_data'); 
    }
    
    public function delete_coupon_codes_translation($coupon_id)
    {
        $this->db->where_in('coupon_id',$coupon_id);
        $this->db->delete('coupon_codes_translation'); 
       
    }
    
    public function delete_coupon_cats_and_products($coupon_id)
    {
        $this->db->where('coupon_id', $coupon_id);
        
        return $this->db->delete('coupon_data');
        
    }
    /**********************INSERT*******************************/
    public function insert_coupon_codes($data)
    {
        return $this->db->insert('coupon_codes', $data);
    }
    
    public function insert_coupon_codes_translation($coupon_codes_translation_data)
    {
        return $this->db->insert('coupon_codes_translation', $coupon_codes_translation_data);
    }
    
    public function insert_coupon_uses_data($data)
    {
         return $this->db->insert('coupon_codes_users', $data);
    } 
    
    public function insert_coupon_data($coupon_data)
    {
        return $this->db->insert('coupon_data', $coupon_data);
    }
    
    public function insert_coupon_uses_products($inserted_data)
    {
        return $this->db->insert('coupon_codes_users_products', $inserted_data);
    }
      /**********************Update*******************************/
    public function update_coupon_codes($coupon_id,$coupon_code_data)
    {
        $this->db->where('id',$coupon_id);
        return $this->db->update('coupon_codes',$coupon_code_data);
    }
    public function update_coupon_codes_translation($coupon_id,$lang_id,$coupon_codes_translation_data)
    {
        $this->db->where('coupon_id',$coupon_id);
        $this->db->where('lang_id',$lang_id);
        return $this->db->update('coupon_codes_translation',$coupon_codes_translation_data);
    }
    
    public function update_coupon_codes_using_cart_id($cart_id, $updated_data)
    {
        $this->db->where('cart_id', $cart_id);
        $this->db->update('coupon_codes_users', $updated_data);
    }
    
    public function update_shopping_cart_total_price($cart_id, $final_price)
    {
       $this->db->where('id', $cart_id);
       return $this->db->update('shopping_cart', $final_price);
    }
   
    /**********************GET*******************************/
    public function get_coupon_codes_result($id)
    {
        $this->db->where('id',$id);
        $query = $this->db->get('coupon_codes');
        
        if($query)
        {
            return $query->row();
        }
    }
    public function get_coupon_codes_translation_result($id)
    {
        $this->db->select('coupon_codes_translation.*');
        $this->db->join('coupon_codes_translation','coupon_codes.id = coupon_codes_translation.coupon_id');
        $this->db->where('coupon_codes.id',$id);
        $query = $this->db->get('coupon_codes');
        
        if($query)
        {
            return $query->result();
        }
    }
    
     public function get_coupons_result($id,$lang_id)
    {
        $this->db->select('coupon_codes_translation.* , coupon_codes.*, countries_translation.name, countries_translation.name as country');
        
        $this->db->join('coupon_codes_translation','coupon_codes.id = coupon_codes_translation.coupon_id');
        $this->db->join('countries_translation','coupon_codes.country_id = countries_translation.country_id');
        
        $this->db->where('coupon_codes.id',$id);
        $this->db->where('coupon_codes_translation.lang_id', $lang_id);
        $this->db->where('countries_translation.lang_id', $lang_id);
         
        $query = $this->db->get('coupon_codes');
        
        if($query)
        {
            return $query->row();
        }
    }
    
    public function get_coupon_codes($lang_id)
    {
        $this->db->select('coupon_codes_translation.*,coupon_codes.*');
        $this->db->join('coupon_codes_translation','coupon_codes.id = coupon_codes_translation.coupon_id');
        $this->db->where('coupon_codes_translation.lang_id',$lang_id);
        $query = $this->db->get('coupon_codes');
        
        if($query)
        {
            return $query->result();
        }
    }
    

    public function get_count_all_coupon_codes($lang_id ,$search_word ='')
    {
        $this->db->join('coupon_codes_translation' ,'coupon_codes.id = coupon_codes_translation.coupon_id');
        
        if(trim($search_word) !='')
        {
            $this->db->where('(coupon_codes_translation.name LIKE "%'.$search_word.'%" OR coupon_codes.code LIKE "%'.$search_word.'%")');  
        }
        
        $this->db->where('coupon_codes_translation.lang_id',$lang_id);
        
        return $this->db->count_all_results('coupon_codes');
    }
    
    public function get_coupon_codes_data($lang_id,$limit,$offset,$search_word='')
    {
        $this->db->select('coupon_codes_translation.* , coupon_codes.* , coupon_codes.id as id');
        
        $this->db->join('coupon_codes_translation' ,'coupon_codes.id = coupon_codes_translation.coupon_id');
        
        $this->db->where('coupon_codes_translation.lang_id', $lang_id);
        
        if(trim($search_word) !='')
        {
            $this->db->where('(coupon_codes_translation.name LIKE "%'.$search_word.'%" OR coupon_codes.code LIKE "%'.$search_word.'%")');    
        }
        
        $this->db->order_by('coupon_codes.id','desc');
        $result = $this->db->get('coupon_codes',$limit,$offset);

        if($result)
        {
            return $result->result();    
        }
    }
    
    public function get_category_products($cat_id,$lang_id)
    {
        $this->db->select('products_translation.*,products.*');
        $this->db->join('products_translation','products.id = products_translation.product_id');
        
        $this->db->where('products.cat_id',$cat_id);
        $this->db->where('products_translation.lang_id',$lang_id);
        
        $query = $this->db->get('products');
        
        if($query)
        {
            return $query->result();
        }else{
            return false ;
        }
    }   
    
    public function get_coupon_data($coupon)
    {
       $this->db->where('code', $coupon);
       $this->db->where('active', 1);
       
       $query = $this->db->get('coupon_codes');
       
       if($query)
       {
           return $query->row(); 
       }
       else
       {
           return false; 
       }
    }
   
    public function get_coupon_uses_conditioned_count($coupon_id, $conditions = array())
    {
        $this->db->where('coupon_id', $coupon_id);
        
        if(count($conditions))
        {
            foreach($conditions as $key =>$value)
            {
                $this->db->where($key, $value);
            }
        }
        
        $count = $this->db->count_all_results('coupon_codes_users');
        
        return $count;
    }
       
    public function get_all_coupons_data($lang_id, $limit, $offset)
    {
        $this->db->select('coupon_codes.*, coupon_codes_translation.name, coupon_codes.id as id');
        $this->db->join('coupon_codes_translation', 'coupon_codes.id = coupon_codes_translation.coupon_id');
        
        $this->db->where('coupon_codes_translation.lang_id', $lang_id);
        
        $result = $this->db->get('coupon_codes', $limit, $offset);
        
        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }
    
    public function get_coupon_uses_count($coupon_id)
    {
        $this->db->where('coupon_id', $coupon_id);
        
        return $this->db->count_all_results('coupon_codes_users');
    }
    
    public function get_cart_coupons_count($cart_id)
    {
        $this->db->where('cart_id', $cart_id);
        
        return $this->db->count_all_results('coupon_codes_users');
    }
    
    public function get_coupons_per_order($order_id)
    {
        $this->db->where('order_id', $order_id);
        $result = $this->db->get('coupon_codes_users');
        
        if($result)
        {
            return $result->result();
        }
    }
      
  
    public function get_row_data($id,$display_lang_id)
    {   
        $this->db->select('coupon_codes_translation.* , coupon_codes.*, coupon_codes.id as id , categories_translation.name as category, products_translation.title as product ');
        
        $this->db->join('coupon_codes_translation' , 'coupon_codes.id = coupon_codes_translation.coupon_id');
        $this->db->join('categories_translation' , 'coupon_codes.cat_id = categories_translation.category_id');
        $this->db->join('products_translation' , 'coupon_codes.product_id = products_translation.product_id');
        
        $this->db->where('coupon_codes.id',$id);
        $this->db->where('coupon_codes_translation.lang_id',$display_lang_id);
        $this->db->where('categories_translation.lang_id',$display_lang_id);
        $this->db->where('products_translation.lang_id',$display_lang_id);
        
        
        $result = $this->db->get('coupon_codes');

        if($result)
        {
            return $result->row();    
        }
    }
    
   public function get_coupon_cat_name($cat_id,$display_lang_id)
   {
          $this->db->where('category_id',$cat_id);
          $this->db->where('lang_id',$display_lang_id);
          
          
          $result = $this->db->get('categories_translation');

          if($result)
          {
              return $result->row()->name;    
          }
   } 
   
   public function get_coupon_product_name($product_id,$display_lang_id)
   {
          $this->db->where('product_id',$product_id);
          $this->db->where('lang_id',$display_lang_id);
          
          
          $result = $this->db->get('products_translation');

          if($result)
          {
              return $result->row()->title;    
          }
   }
   
   public function count_user_in_use_coupons($user_id, $cart_id, $session_id)
   {
       $this->db->where('order_id', 0);
       $this->db->where('cart_id', $cart_id);
       if($user_id == 0)
       {
           $this->db->where('session_id', $session_id);
       }
       else
       {
           $this->db->where('user_id', $user_id);
       }
       
       return $this->db->count_all_results('coupon_codes_users');
   }
   
   
   public function get_user_coupon_data($user_id, $cart_id)
   {
       $lang_id = $this->data['lang_id'];
        
       $this->db->select('coupon_codes_users.*, coupon_codes.*, coupon_codes_translation.name, coupon_codes.id as id, coupon_codes_users.id as coupon_id');
       
       $this->db->join('coupon_codes_translation', 'coupon_codes_users.coupon_id = coupon_codes_translation.coupon_id');
       $this->db->join('coupon_codes', 'coupon_codes_users.coupon_id = coupon_codes.id');
       
       $this->db->order_by('coupon_codes_users.id', 'desc');
       
       $this->db->where('coupon_codes_translation.lang_id', $lang_id);
       $this->db->where('coupon_codes_users.order_id', 0);
       $this->db->where('coupon_codes_users.user_id', $user_id);
       $this->db->where('coupon_codes_users.cart_id', $cart_id);
       
       $query = $this->db->get('coupon_codes_users');
       
       if($query)
       {
           return $query->row();
       }
       else
       {
           return false;
       }
   }
   
   public function get_coupon_cats_and_products($coupon_id)
   {
       $this->db->where('coupon_id', $coupon_id);
       $result = $this->db->get('coupon_data');
       
       if($result)
       {
         return $result->result();
       }
       else
       {
        return false;
       }
       
   }
   
   public function get_coupon_cats_ids($coupon_id)
   {
       $this->db->where('coupon_id', $coupon_id);
       $this->db->where('product_id', 0);
       $result = $this->db->get('coupon_data')->result();
       
       $cats_ids = array();
       foreach($result as $item)
       {
        $cats_ids[] = $item->cat_id;
       }
       
       return $cats_ids;
   }
   
    public function get_coupon_products_ids($coupon_id)
   {
       $this->db->where('coupon_id', $coupon_id);
       $this->db->where('cat_id', 0);
       
       $result = $this->db->get('coupon_data')->result();
       
       $products_ids = array();
       foreach($result as $item)
       {
          $products_ids[] = $item->product_id;
       }
       
       return $products_ids;
   }
   
   public function get_coupon_products($coupon_id)
   {
       $this->db->where('coupon_id', $coupon_id);
       $this->db->where('cat_id', 0);
       $result = $this->db->get('coupon_data');
       
       if($result)
       {
           return $result->result(); 
       }
       else
       {
           return false;
       }
   }
   
   public function check_user_coupons_count($user_id)
   {
       $this->db->where('user_id', $user_id);
       $this->db->where('order_id', 0);
       
       return $this->db->count_all_results('coupon_codes_users');
   }
   
   public function update_coupon_uses_data($id, $updated_data)
   {
       $this->db->where('id', $id);
       return $this->db->update('coupon_codes_users', $updated_data);
   }
   
   public function update_user_coupon_by_session($session_id, $user_coupon_data)
   {
       $this->db->where('session_id', $session_id);
       $this->db->where('order_id', 0);
       return $this->db->update('coupon_codes_users', $user_coupon_data);
   }
   
   public function delete_user_coupon($user_id, $session_id)
   {
        if($user_id == 0)
        { 
            $this->db->where('session_id', $session_id);
        }
        else
        {
            $this->db->where('user_id', $user_id);
        }
        
        $this->db->where('order_id', 0);
        
        return $this->db->delete('coupon_codes_users');
   }
   
   public function delete_cart_coupon($cart_id)
   {
        $this->db->where('cart_id', $cart_id);
        return $this->db->delete('coupon_codes_users');
   }
   
   public function delete_user_previous_coupons($user_id)
   {
        $this->db->order_by('id', 'asc');
        $this->db->where('user_id', $user_id);
        $this->db->where('order_id', 0);
        
        return $this->db->delete('coupon_codes_users');
   }
   
   public function check_coupon_users_count($session_id)
   {
       $this->db->where('session_id', $session_id);
       $this->db->where('order_id', 0);
       return $this->db->count_all_results('coupon_codes_users');
   }
   
   public function count_coupon_uses($coupon_id)
   {
       $this->db->where('coupon_id', $coupon_id);
       return $this->db->count_all_results('coupon_codes_users');
   }
   
   public function get_coupon_uses_data($coupon_id, $lang_id)
   {
       //$this->db->select('coupon_codes_users.*, coupon_codes_translation.name, coupon_codes_users_products.*, products_translation.title, categories_translation.name, products_translation.title as product_name, categories_translation.name as cat_name');
       $this->db->select('coupon_codes_users.*, coupon_codes_translation.name, coupon_codes_users_products.*');
       
       $this->db->join('coupon_codes_translation', 'coupon_codes_users.coupon_id = coupon_codes_translation.coupon_id');
       $this->db->join('coupon_codes_users_products', 'coupon_codes_users.id = coupon_codes_users_products.coupon_codes_users_id', 'left');
       //$this->db->join('products_translation', 'coupon_codes_users_products.product_id = products_translation.product_id');
       //$this->db->join('categories_translation', 'coupon_codes_users_products.category_id = categories_translation.category_id');
       
       $this->db->where('coupon_codes_users.coupon_id', $coupon_id);
       $this->db->where('coupon_codes_translation.lang_id', $lang_id);
       //$this->db->where('products_translation.lang_id', $lang_id);
       //$this->db->where('categories_translation.lang_id', $lang_id);
       
       $result = $this->db->get('coupon_codes_users');
       
       if($result)
       {
         return $result->result();
       }
       else
       {
         return false;
       }
        
   }
   
   public function get_coupon_detailed_product($coupon_id, $display_lang_id)
   {
        $this->db->select('products_translation.title, products_translation.title as product');
        $this->db->join('products_translation', 'coupon_data.product_id = products_translation.product_id');
        
        $this->db->where('products_translation.lang_id', $display_lang_id);
        $this->db->where('coupon_data.coupon_id', $coupon_id);
        
        $result = $this->db->get('coupon_data');
        
        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
   }
   
   public function get_coupon_detailed_cats($coupon_id, $display_lang_id)
   {
        $this->db->select('categories_translation.name, categories_translation.name as category');
        $this->db->join('categories_translation', 'coupon_data.cat_id = categories_translation.category_id');
        
        $this->db->where('categories_translation.lang_id', $display_lang_id);
        $this->db->where('coupon_data.coupon_id', $coupon_id);
        
        $result = $this->db->get('coupon_data');
        
        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        } 
   }
   
   public function get_used_coupon_data($coupon_id)
   {
       $this->db->select('coupon_codes_users.*, orders.*');
       $this->db->join('orders', 'coupon_codes_users.order_id = orders.id');
       
       $this->db->where('coupon_codes_users.coupon_id', $coupon_id);
       $this->db->select_sum('coupon_codes_users.total_discount');
       
       $query = $this->db->get('coupon_codes_users');
       
       if($query)
       {
            return $query->row();
       }
       else
       {
            return false;
       }
   }
   
   public function get_coupon_total_discount($coupon_id)
   {
       $this->db->where('coupon_id', $coupon_id);
       $this->db->select_sum('total_discount');
       
       $query = $this->db->get('coupon_codes_users');
       
       if($query->row())
       {
            return $query->row()->total_discount;
       }
       else
       {
            return false;
       }
   }
   
   public function get_coupon_suggestions($search_word, $lang_id)
   {
        $this->db->select('coupon_codes_translation.*, coupon_codes_translation.coupon_id as id');
        
        $this->db->where('(name LIKE "%'.$search_word.'%" )');
        $this->db->where('lang_id', $lang_id);
        
        $result = $this->db->get('coupon_codes_translation');
        
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