<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
*
*
*/

class Shopping_cart_model extends CI_Model
{
    public function get_user_cart_id($user_id)
    {
        $this->db->where('user_id', $user_id);

        $row = $this->db->get('shopping_cart')->row();

        if($row)
        {
            return $row->id;
        }
        else
        {
            return false;
        }
    }

    public function check_cart_exist($cart_id, $user_ids = array())
    {
        $this->db->where('id', $cart_id);

        if(count($user_ids))
        {
            $this->db->where_in('user_id', $user_ids);
        }

        $count = $this->db->count_all_results('shopping_cart');

        return ($count > 0)? true : false;
    }

    public function insert_new_shopping_cart($cart_data)
    {
        return $this->db->insert('shopping_cart', $cart_data);
    }


    //////////////////////////////////////////////////////////////
    public function check_user_cart_count($user_id, $session_id='', $ip_address='')
    {
        if($user_id == 0)
        {
            $this->db->where('session_id', $session_id);
            $this->db->where('ip_address', $ip_address);
        }

        $this->db->where('user_id', $user_id);

        $count = $this->db->count_all_results('shopping_cart');

        return $count;
    }

    public function get_guest_cart_id_by_session($session_id='', $ip_address='')
    {

        $this->db->where('session_id', $session_id);
        $this->db->where('ip_address', $ip_address);
        $this->db->where('user_id', 0);

        $row = $this->db->get('shopping_cart')->row();

        if($row)
        {
            return $row->id;
        }
        else
        {
            return false;
        }
    }

    public function get_shopping_cart_data($user_id, $session_id, $ip_address)
    {
        if($user_id == 0)
        {
            $this->db->where('ip_address', $ip_address);
            $this->db->where('session_id', $session_id);
        }

        $this->db->where('user_id',$user_id);

        $row = $this->db->get('shopping_cart')->row();

        if($row)
        {
            return $row;
        }
        else
        {
            return false;
        }
    }



    public function insert_shopping_cart_products($data)
    {
        return $this->db->insert('shopping_cart_products', $data);
    }

    public function update_shopping_cart_product($cart_id, $product_id, $updated_data ,$cart_product_id=0)
    {
        $this->db->where('cart_id', $cart_id);
        $this->db->where('product_id', $product_id);

        // Mrzok Edit - to get specific cart product row if there is multible rows for the same product
        if($cart_product_id != 0){
            $this->db->where('id', $cart_product_id);  
        }
        // End Edits

        return $this->db->update('shopping_cart_products', $updated_data);
    }

    public function update_shopping_cart($cart_id, $data)
    {
        $this->db->where('id', $cart_id);
        return $this->db->update('shopping_cart', $data);
    }

    public function get_shopping_cart_items_count($cart_id)
    {
        $this->db->where('cart_id',$cart_id);
        $count = $this->db->count_all_results('shopping_cart_products');

        return $count;
    }

    public function count_cart_products($cart_id)
    {
        $this->db->where('cart_id', $cart_id);
        $this->db->where('product_id !=0 ');

        $count = $this->db->count_all_results('shopping_cart_products');

        return $count;
    }

    public function get_product_data($cart_id, $product_id, $cart_product_id=0 )
    {
        // Mrzok Edit 
        // to get specific cart product row if there is multible rows for the same product
        if($cart_product_id != 0){
            $this->db->where('id', $cart_product_id);  
        }

        // End Edits

        $this->db->where('cart_id', $cart_id);
        $this->db->where('product_id', $product_id);

        $row = $this->db->get('shopping_cart_products')->row();

        if($row)
        {
            return $row;
        }
        else
        {
            return false;
        }
    }
    
    public function get_same_product_array_from_cart($cart_id, $product_id)
    {
        // Mrzok Edit 
        $this->db->select('shopping_cart_products.id as cart_product_id, shopping_cart_products.cart_id, shopping_cart_products.product_id, shopping_cart_products.qty');
        $this->db->where('shopping_cart_products.cart_id', $cart_id);
        $this->db->where('shopping_cart_products.product_id', $product_id);

        $result = $this->db->get('shopping_cart_products');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_shopping_cart_products($cart_id)
    {
        $this->db->where('cart_id', $cart_id);
        $result = $this->db->get('shopping_cart_products');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_shopping_cart_field($cart_id, $field)
    {
        $this->db->where('id', $cart_id);
        $row = $this->db->get('shopping_cart')->row();

        if($row)
        {
            return $row->{"$field"};
        }
        else
        {
            return false;
        }
    }

    public function get_cart_contents($cart_id)
    {
        $this->db->select('shopping_cart.*, shopping_cart_products.*, shopping_cart.id as id, shopping_cart_products.id as cart_product_id');

        $this->db->join('shopping_cart_products', 'shopping_cart.id = shopping_cart_products.cart_id');

        $this->db->where('shopping_cart.id',$cart_id);

        $this->db->order_by('shopping_cart_products.store_id', 'desc');

        $result = $this->db->get('shopping_cart');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_cart_data($cart_id)
    {
        $this->db->where('id', $cart_id);

        $row = $this->db->get('shopping_cart')->row();

        if($row)
        {
            return $row;
        }
        else
        {
            return false;
        }
    }

    public function delete_shopping_cart_product($cart_id, $cart_product_id)
    {
        $this->db->where('cart_id', $cart_id);
        $this->db->where('id', $cart_product_id);
        //$this->db->where('product_id', $product_id);
        $this->db->delete('shopping_cart_products');

        $this->db->where('cart_product_id', $cart_product_id);
        $this->db->delete('user_products_optional_fields');

        return true;
    }

    public function delete_shopping_cart($cart_id)
    {
        $this->db->where('shopping_cart_products.cart_id', $cart_id);
        $this->db->delete('shopping_cart_products');

        $this->db->where('shopping_cart.id', $cart_id);
        $this->db->delete('shopping_cart');

        return true;
    }

    public function delete_shopping_cart_checked_products($cart_id)
    {
        $this->db->where('shopping_cart_products.cart_id', $cart_id);
        $this->db->where('shopping_cart_products.checked', 1);
        $this->db->delete('shopping_cart_products');

        return true;
    }

    public function delete_cart($cart_id)
    {
        $this->db->where('shopping_cart.id', $cart_id);
        $this->db->delete('shopping_cart');

        return true;
    }

    public function delete_cart_used_coupons($cart_id)
    {
        $this->db->where('cart_id', $cart_id);
        return $this->db->delete('coupon_codes_users');
    }

    public function delete_applied_coupon_cart_product($cart_id, $product_id)
    {
        $sql = "DELETE coupon_codes_users_products FROM coupon_codes_users_products
                JOIN coupon_codes_users ON coupon_codes_users_products.coupon_codes_users_id = coupon_codes_users.id
                WHERE coupon_codes_users.cart_id = $cart_id AND coupon_codes_users_products.product_id = $product_id";
        $this->db->query($sql);

    }

    public function count_applied_coupon_products($cart_id, $product_id)
    {
        $this->db->join('coupon_codes_users', 'coupon_codes_users_products.coupon_codes_users_id = coupon_codes_users.id');

        $this->db->where('coupon_codes_users_products.product_id', $product_id);
        $this->db->where('coupon_codes_users.cart_id', $cart_id);

        return $this->db->count_all_results('coupon_codes_users_products');
    }

    public function get_coupon_user_data($cart_id)
    {
        $this->db->where('cart_id', $cart_id);
        $query = $this->db->get('coupon_codes_users')->row();

        if($row)
        {
            return $row;
        }
        else
        {
            return false;
        }
    }

    public function count_coupon_products($coupon_user_id)
    {
        $this->db->where('coupon_codes_users_id', $coupon_user_id);
        return $this->db->count_all_results('coupon_codes_users_products');
    }

    public function get_cart_coupon_data($cart_id)
   {
       $this->db->select('coupon_codes_users.*, coupon_codes.*,coupon_codes_users.id as id, coupon_codes.id as coupon_id');
       $this->db->join('coupon_codes', 'coupon_codes_users.coupon_id = coupon_codes.id');

       $this->db->where('coupon_codes_users.order_id', 0);
       $this->db->where('coupon_codes_users.cart_id', $cart_id);

       $row = $this->db->get('coupon_codes_users')->row();

       if($row)
       {
           return $row;
       }
       else
       {
           return false;
       }
   }

   public function delete_cart_coupon_data($cart_id)
   {
        $sql = "DELETE coupon_codes_users_products, coupon_codes_users FROM coupon_codes_users_products
                RIGHT JOIN coupon_codes_users ON coupon_codes_users_products.coupon_codes_users_id = coupon_codes_users.id
                WHERE coupon_codes_users.cart_id = $cart_id";
        $this->db->query($sql);
   }

   public function reset_products_discount($cart_id, $discount_data)
   {
       $this->db->where('cart_id', $cart_id);
       return $this->db->update('shopping_cart_products', $discount_data);
   }

    public function get_product_count_in_shopping_cart($cart_id, $product_id)
    {
        $this->db->where('product_id', $product_id);
        $this->db->where('cart_id', $cart_id);

        $count = $this->db->count_all_results('shopping_cart_products');

        return $count;
    }

    public function check_cart_products_count($cart_id)
    {

        $this->db->where('cart_id', $cart_id);
        $count = $this->db->count_all_results('shopping_cart_products');

        return $count;
    }

    public function get_cart_id($user_id, $session_id='', $ip_address='')
    {
        if($user_id == 0)
        {
            $this->db->where('ip_address', $ip_address);
            $this->db->where('session_id', $session_id);
        }

        $this->db->where('user_id', $user_id);

        $row = $this->db->get('shopping_cart')->row();

        if($row)
        {
            return $row->id;
        }
        else
        {
            return false;
        }
    }

    public function get_cart_row_data($cart_id)
    {
        $this->db->where('id', $cart_id);
        $row = $this->db->get('shopping_cart')->row();

        if($row)
        {
            return $row;
        }
        else
        {
            return false;
        }
    }

    public function get_user_shopping_cart_count($session_id, $ip_address)
    {
        $this->db->where('session_id', $session_id);
        $this->db->where('ip_address', $ip_address);

        return $this->db->count_all_results('shopping_cart');
    }

    public function update_shopping_cart_user_id($user_data, $session_id, $ip_address)
    {
        $this->db->where('session_id', $session_id);
        $this->db->where('ip_address', $ip_address);

        return $this->db->update('shopping_cart', $user_data);
    }

    public function get_visitor_cart_id($user_id, $session_id, $ip_address)
    {
        if($user_id == 0)
        {
            $this->db->where('session_id', $session_id);
            $this->db->where('ip_address', $ip_address);
        }
        $this->db->where('user_id', $user_id);


        $row = $this->db->get('shopping_cart')->row();

        if($row)
        {
            return $row->id;
        }
        else
        {
            return false;
        }
    }

    public function get_cart_product_data($cart_id, $product_id,$cart_product_id=0)
    {
        $this->db->where('cart_id', $cart_id);
        $this->db->where('product_id', $product_id);

        // Mrzok Edit - to get specific cart product row if there is multible rows for the same product
        if($cart_product_id != 0){
            $this->db->where('id', $cart_product_id);  
        }
        // End Edits

        $row = $this->db->get('shopping_cart_products')->row();

        if($row)
        {
            return $row;
        }
    }

    public function get_recharge_cards_data($shopping_cart_id)
    {
        $this->db->where('cart_id', $shopping_cart_id);
        $this->db->where('product_id', 0);

        $result = $this->db->get('shopping_cart_products');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function charge_cards_in_cart_count($cart_id)
    {
        $this->db->where('cart_id', $cart_id);
        $this->db->where('product_id', 0);

        return $this->db->count_all_results('shopping_cart_products');
    }

    public function get_cart_products($cart_id)
    {
        $this->db->where('cart_id', $cart_id);

        $result = $this->db->get('shopping_cart_products')->result();

        if($result)
        {
            return $result;
        }
        else
        {
            return false;
        }
    }

    public function to_be_deleted_shopping_carts($delete_time)
    {
        $this->db->where('user_id', 0);
        $this->db->where('unix_time <', $delete_time);

        $result = $this->db->get('shopping_cart')->result();

        if($result)
        {
            return $result;
        }
        else
        {
            return false;
        }
    }

    public function get_cart_id_by_session_id($session_id)
    {
        $this->db->where('session_id', $session_id);

        $row = $this->db->get('shopping_cart')->row();

        if($row)
        {
            return $row;
        }
        else
        {
            return false;
        }
    }

    public function reset_cart_discount($cart_id)
    {
        $sql ="UPDATE `shopping_cart` SET `final_price`=`total_price`,`coupon_discount`=0 WHERE `id` = $cart_id";

        return $this->db->query($sql);
    }

    public function update_cart_products($cart_id, $data)
    {
        $this->db->where('cart_id', $cart_id);
        return $this->db->update('shopping_cart_products', $data);
    }

    public function get_branches_data($lang_id)
    {
        $this->db->select('branches.*, branches_translation.*');
        $this->db->join('branches_translation', 'branches.id = branches_translation.branch_id AND branches_translation.lang_id ='.$lang_id);

        $this->db->where('branches.active', 1);

        $result = $this->db->get('branches');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_shipping_method($method_id, $lang_id)
    {
        $this->db->where('method_id', $method_id);
        $this->db->where('lang_id', $lang_id);

        $query = $this->db->get('shipping_methods_translation');

        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }

    public function count_cart_stores($cart_id)
    {
        $this->db->where('cart_id', $cart_id);
        $this->db->group_by('store_id');

        return $this->db->count_all_results('shopping_cart_products');
    }

    public function get_cart_stores($cart_id, $lang_id)
    {
        $this->db->select('shopping_cart_products.*, stores_translation.name as store_name');

        $this->db->join('stores_translation', 'shopping_cart_products.store_id = stores_translation.store_id AND stores_translation.lang_id ='.$lang_id, 'left');

        $this->db->where('cart_id', $cart_id);
        $this->db->group_by('store_id');

        $result = $this->db->get('shopping_cart_products');

        if($result->result())
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_cart_stores_products($cart_id, $store_id)
    {
        $this->db->select('shopping_cart_products.*, shopping_cart_products.id as cart_product_id');
        $this->db->where('cart_id', $cart_id);
        $this->db->where('store_id', $store_id);
        //$this->db->where('checked', 1);

        $result = $this->db->get('shopping_cart_products');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_cart_checked_products($cart_id, $store_id)
    {
        $this->db->select('shopping_cart_products.*, shopping_cart_products.id as cart_product_id');
        $this->db->where('cart_id', $cart_id);
        $this->db->where('store_id', $store_id);
        //$this->db->where('checked', 1);

        $result = $this->db->get('shopping_cart_products');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_cart_products_translation($cart_id, $lang_id)
    {
        $this->db->select('shopping_cart_products.*, products_translation.title as product_name');
        $this->db->join('products_translation', 'shopping_cart_products.product_id = products_translation.product_id AND products_translation.lang_id='.$lang_id, 'left');

        $this->db->where('shopping_cart_products.cart_id', $cart_id);

        $result = $this->db->get('shopping_cart_products');

        if($result->result())
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function update_check_stores_products($stores_ids, $cart_id, $updated_data)
    {
        $this->db->where('cart_id', $cart_id);
        $this->db->where_in('store_id', $stores_ids);

        return $this->db->update('shopping_cart_products', $updated_data);
    }

    public function update_not_checked_stores_products($stores_ids, $cart_id, $updated_data)
    {
        $this->db->where('cart_id', $cart_id);
        $this->db->where_not_in('store_id', $stores_ids);

        return $this->db->update('shopping_cart_products', $updated_data);
    }

    public function count_cart_checked_stores($cart_id)
    {
        $this->db->select('shopping_cart_products.*');

        $this->db->where('cart_id', $cart_id);
        $this->db->where('checked', 1);
        $this->db->group_by('store_id');

        return $this->db->count_all_results('shopping_cart_products');

    }

    public function get_cart_checked_stores($cart_id, $lang_id)
    {
        $this->db->select('shopping_cart_products.*, stores_translation.name as store_name');

        $this->db->join('stores_translation', 'shopping_cart_products.store_id = stores_translation.store_id AND stores_translation.lang_id ='.$lang_id);

        $this->db->where('shopping_cart_products.cart_id', $cart_id);
        $this->db->where('shopping_cart_products.checked', 1);
        $this->db->group_by('store_id');

        $result = $this->db->get('shopping_cart_products');

        if($result->result())
        {
            return $result->result();
        }
        else
        {
            return false;
        }

    }

    public function get_cart_product_row_details($product_id, $lang_id, $country_id=0)
    {
        $this->db->select('products.*, products_translation.*, products_countries.*, products.id as id, stores_translation.name as store_name');

        $this->db->join('shopping_cart_products', 'products.id = shopping_cart_products.product_id');
        $this->db->join('products_translation', 'products.id = products_translation.product_id');
        $this->db->join('products_countries', 'products.id = products_countries.product_id AND products_countries.active=1 AND products_countries.country_id ='.$country_id, 'left');
        $this->db->join('stores_translation', 'products.store_id = stores_translation.store_id AND stores_translation.lang_id ='.$lang_id, 'left');

        $this->db->where('products.id', $product_id);
        $this->db->where('products_translation.lang_id', $lang_id);


        $row = $this->db->get('products');

        if($row)
        {
            return $row->row();
        }
        else
        {
            return false;
        }
    }

    public function update_cart_product($cart_row_id, $data)
    {
        $this->db->where('id', $cart_row_id);
        return $this->db->update('shopping_cart_products', $data);
    }

    public function check_maintenance_products_exist($cart_id, $maintenance_cat_id)
    {
        $this->db->join('categories', 'shopping_cart_products.cat_id = categories.id AND categories.parent_id = '.$maintenance_cat_id);
        $this->db->where('shopping_cart_products.cart_id', $cart_id);

        $count = $this->db->count_all_results('shopping_cart_products');

        if($count > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function check_product_in_cart($product_id, $cart_id)
    {
        $this->db->where('product_id', $product_id);
        $this->db->where('cart_id', $cart_id);

        $count = $this->db->count_all_results('shopping_cart_products');

        if($count > 0)
        {
            return 1;
        }
        else
        {
            return 0;
        }
    }

    public function get_user_addresses($user_id)
    {
      $this->db->where('user_id', $user_id);
      $result = $this->db->get('user_addresses');

      if($result)
      {
        return $result->result();
      }
      else {
        return false;
      }
    } 

    public function remove_account_packages_in_cart($cart_id)
    {
        $this->db->where('cart_id', $cart_id);
        $this->db->where('type', 'package');
        return $this->db->delete('shopping_cart_products');
    }
    
    public function get_cart_order_id($cart_id)
    {
        $this->db->where('cart_id', $cart_id);
        $query = $this->db->get('orders')->row();
        
        if(count($query) != 0)
        {
            return $query->id;
        }
        else
        {
            return 0;
        }
    }
}
