<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class  Stores_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    /**********************INSERT*******************************/
    public function insert_store($data)
    {
        return $this->db->insert('stores', $data);
    }

    public function insert_stores_translation($data)
    {
        return $this->db->insert('stores_translation', $data);
    }

    public function save_store_cats($data)
    {
        return $this->db->insert('store_categories', $data);
    }

    /**********************Update*******************************/
    public function update_store($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('stores',$data);
    }
    public function update_stores_translation($id, $lang_id, $store_translation_data)
    {
        $this->db->where('store_id', $id);
        $this->db->where('lang_id', $lang_id);
        return $this->db->update('stores_translation', $store_translation_data);
    }

    /**********************DELETE*******************************/
    public function delete_store_data($ids_array)
    {
        $this->db->where_in('id', $ids_array);
        $this->db->delete('stores');

        $this->db->where_in('store_id', $ids_array);
        $this->db->delete('stores_translation');

        echo '1';
    }

    public function delete_store_cats($store_id)
    {
        $this->db->where('store_id', $store_id);
        return $this->db->delete('store_categories');
    }

    /**********************GET*******************************/
    public function get_store_row($id)
    {
        $this->db->where('id',$id);
        $query = $this->db->get('stores');

        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }
    public function get_store_translation_result($id)
    {
        $this->db->where('stores_translation.store_id', $id);
        $query = $this->db->get('stores_translation');

        if($query)
        {
            return $query->result();
        }
        else
        {
            return false;
        }
    }

    public function get_store_name($store_id, $lang_id)
    {
        $this->db->where('stores_translation.store_id', $store_id);
        $this->db->where('stores_translation.lang_id', $lang_id);

        $row = $this->db->get('stores_translation');

        if($row->row())
        {
            return $row->row()->name;
        }
        else
        {
            return false;
        }
    }

    public function get_store_main_cats($store_id, $lang_id)
    {
        $this->db->select('categories.*, categories_translation.*');

        $this->db->join('categories_translation', 'categories.id = categories_translation.category_id AND categories_translation.lang_id = '.$lang_id);
        $this->db->join('store_categories', 'categories.id = store_categories.category_id AND store_categories.store_id = '.$store_id);

        $this->db->where('categories.parent_id', 0);
        $this->db->where('categories.active', 1);

        $result = $this->db->get('categories');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_store_sub_cats($cat_id, $store_id, $lang_id)
    {
        $this->db->select('categories.*, categories_translation.*');

        $this->db->join('categories_translation', 'categories.id = categories_translation.category_id AND categories_translation.lang_id = '.$lang_id);
        $this->db->join('store_categories', 'categories.id = store_categories.category_id AND store_categories.store_id = '.$store_id);

        $this->db->where('categories.parent_id', $cat_id);
        $this->db->where('categories.active', 1);

        $result = $this->db->get('categories');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_count_all_stores($lang_id ,$search_word ='')
    {
        $this->db->join('stores_translation' ,'stores.id = stores_translation.store_id');

        if(trim($search_word) !='')
        {
            $this->db->where('(stores_translation.name LIKE "%'.$search_word.'%" OR stores_translation.address LIKE "%'.$search_word.'%" OR stores.phone LIKE "%'.$search_word.'%")');
        }

        $this->db->where('stores_translation.lang_id', $lang_id);

        return $this->db->count_all_results('stores');
    }

    public function get_stores_data($lang_id, $limit=0, $offset=0, $search_word='', $order_by='', $order_state='desc', $conds=array())
    {
        $this->db->select('stores_translation.* , stores.*');

        $this->db->join('stores_translation' ,'stores.id = stores_translation.store_id');

        if(trim($search_word) !='')
        {
            $this->db->where('(stores_translation.name LIKE "%'.$search_word.'%" OR stores_translation.address LIKE "%'.$search_word.'%" OR stores.phone LIKE "%'.$search_word.'%")');
        }

        $this->db->where('stores_translation.lang_id', $lang_id);

        if(count($conds) != 0)
        {
            foreach($conds as $key=>$val)
            {
                $this->db->where($key, $val);
            }
        }

        if($order_by != '')
        {
            if($order_by == lang('name_of_store'))
            {
                $this->db->order_by('stores_translation.name', $order_state);
            }
            elseif($order_by == lang('show_in_main_page'))
            {
                $this->db->order_by('stores.show_in_main_page', $order_state);
            }
            elseif($order_by == lang('show_in_menu'))
            {
                $this->db->order_by('stores.show_in_menu', $order_state);
            }
            else
            {
                $this->db->order_by('stores.id', $order_state);
            }
        }
        else
        {
            $this->db->order_by('stores.id', $order_state);
        }

        if($limit != 0)
        {
            $result = $this->db->get('stores', $limit,$offset);
        }
        else
        {
            $result = $this->db->get('stores');
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
        $this->db->select('stores_translation.* , stores.*');
        $this->db->join('stores_translation' ,'stores.id = stores_translation.store_id');

        $this->db->where('stores.id',$id);
        $this->db->where('stores_translation.lang_id',$display_lang_id);

        $result = $this->db->get('stores');

        if($result)
        {
            return $result->row();
        }
        else
        {
            return false;
        }
    }

    public function all_stores()
    {
        $this->db->where('active', 1);

        $result = $this->db->get('stores');

        if($result)
        {
            return $result->result_array();
        }
        else
        {
            return false;
        }
    }

    public function get_all_stores($display_lang_id, $not_in_ids=array(), $conds=array())
    {
        $this->db->select('stores_translation.* , stores.* , stores.id as id,
                          stores_translation.name as name, count(products.id) as products_count');

        $this->db->join('stores_translation' ,'stores.id = stores_translation.store_id');
        $this->db->join('products' ,'stores.id = products.store_id', 'left');
        $this->db->group_by('stores.id');

        $this->db->where('stores_translation.lang_id',$display_lang_id);
        $this->db->where('stores.active', 1);

        if(count($not_in_ids) != 0 && !is_null($not_in_ids))
        {
            $this->db->where_not_in('stores.id', $not_in_ids);
        }

        if(count($conds) != 0)
        {
          foreach($conds as $key=>$val)
          {
            $this->db->where($key, $val);
          }
        }

        $result = $this->db->get('stores');
//echo '<pre>'; print_r($result->result()); die();
        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_store_cats($store_id)
    {
        $this->db->where('store_id', $store_id);
        $result = $this->db->get('store_categories');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_store_available_cats_data($store_id, $lang_id)
    {
        $this->db->select('categories.*, categories_translation.*');

        $this->db->join('categories_translation', 'categories.id = categories_translation.category_id AND categories_translation.lang_id = '.$lang_id);
        $this->db->join('store_categories', 'categories.id = store_categories.category_id AND store_categories.store_id = '.$store_id);

        $result = $this->db->get('categories');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_menu_stores($lang_id, $not_in_ids=array(), $limit=0,$offset=0, $all=0)
    {
        $this->db->select('stores_translation.* , stores.* , stores.id as id, stores_translation.name as store_name,
                            COUNT(orders.id) AS orders_count, COUNT(products.id) as products_count');

        $this->db->join('stores_translation' ,'stores.id = stores_translation.store_id AND stores_translation.lang_id ='.$lang_id);
        $this->db->join('orders', 'stores.id = orders.store_id', 'left');
        $this->db->join('products', 'stores.id = products.store_id', 'left');

        if(count((array)$not_in_ids) != 0)
        {
            $this->db->where_not_in('stores.id', $not_in_ids);
        }
        
        if($all==0)
        {
            $this->db->where('stores.show_in_menu', 1);
        }
        
        $this->db->where('stores.active', 1);
        $this->db->group_by('stores.id');
        $this->db->order_by('orders_count', 'DESC');
        $this->db->order_by('products_count', 'DESC');
        $this->db->order_by('products.id', 'DESC');

        if($limit != 0)
        {
            $result = $this->db->get('stores', $limit, $offset);
        }
        else
        {
            $result = $this->db->get('stores');
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

    public function get_menu_first_stores($lang_id, $inner=0)
    {
        $this->db->select('stores_translation.* , stores.* , stores.id as id, stores_translation.name as store_name,
                            settings.first_store_id, settings.second_store_id, settings.third_store_id,
                            settings.fourth_store_id, settings.fifth_store_id, count(products.id) as products_count');//, count(products.id) as products_count');

        $this->db->join('settings', '(stores.id = settings.first_store_id AND settings.first_store_id != 0) 
                                    OR (stores.id = settings.second_store_id AND settings.second_store_id!= 0) 
                                    OR (stores.id = settings.third_store_id AND settings.third_store_id != 0) 
                                    OR (stores.id = settings.fourth_store_id AND settings.fourth_store_id != 0)
                                    OR (stores.id = settings.fifth_store_id AND settings.fifth_store_id != 0)');

        $this->db->join('products', 'stores.id = products.store_id', 'left');
        $this->db->join('stores_translation' ,'stores.id = stores_translation.store_id AND stores_translation.lang_id ='.$lang_id);
        
        
        if($inner == 0)
        {
            $this->db->where('stores.show_in_menu', 1);
        }

        $result = $this->db->get('stores');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_home_stores($lang_id)
    {
        $this->db->select('stores_translation.* , stores.* , stores.id as id, stores_translation.name as store_name');

        $this->db->join('stores_translation' ,'stores.id = stores_translation.store_id AND stores_translation.lang_id ='.$lang_id);
        $this->db->where('stores.show_in_main_page', 1);

        $result = $this->db->get('stores');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function check_stores_products($stores_ids)
    {
      $this->db->where_in('products.store_id', $stores_ids);
      $count = $this->db->count_all_results('products');

      if($count > 0)
      {
        return true;
      }
      else {
        return false;
      }
    }



/****************************************************************/
}
