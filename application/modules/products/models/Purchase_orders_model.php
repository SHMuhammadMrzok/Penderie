<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Purchase_orders_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    /******************DELETE**********************/
    public function delete_purchase_orders_data ($order_id)
    {
        $this->db->where_in('id',$order_id);
        $this->db->delete('purchase_orders');

        $this->delete_purchase_orders_products_data($order_id);
    }
    public function delete_purchase_orders_products_data($product_id)
    {
        $this->db->where_in('product_id',$product_id);
        $this->db->delete('purchase_orders_products');
    }

    public function delete_purchase_orders_old_products_data($product_id)
    {
        $this->db->where('purchase_order_id',$product_id);
        $this->db->delete('purchase_orders_products');
    }

    /********************Insert *****************************/
    public function insert_purchase_orders($purchase_orders_data)
    {
        return $this->db->insert('purchase_orders', $purchase_orders_data);
    }

    public function insert_purchase_orders_products($purchase_orders_products_data)
    {
        return $this->db->insert('purchase_orders_products', $purchase_orders_products_data);
    }

    public function insert_table_data($table_name, $table_data)
    {
        return $this->db->insert($table_name, $table_data);
    }

    /******************************Update**********************************/

    public function update_purchase_orders($id,$purchase_orders_data)
    {
        $this->db->where('id',$id);
        $this->db->update('purchase_orders' , $purchase_orders_data);
    }


    public function update_products_country_average_cost($product_id ,$country_id=0 ,$data)
    {
        $this->db->where('product_id',$product_id);

        if($country_id != 0)
        {
            $this->db->where('country_id',$country_id);
        }

        $this->db->update('products_countries',$data);

        return true;
    }

    public function get_purchase_order_per_vendor_number($order_id)
    {
        $this->db->where('id', $order_id);
        $query = $this->db->get('purchase_orders');
        if($query)
        {
            return $query->row()->order_number;
        }
        else
        {
            return false;
        }

    }
   /********************GET*****************************/
    public function get_category_products($cat_id, $lang_id, $store_id)
    {
        $this->db->select('products_translation.*,products.*');
        $this->db->join('products_translation','products.id = products_translation.product_id');

        $this->db->where('products.cat_id', $cat_id);
        $this->db->where('products.store_id', $store_id);
        $this->db->where('products.quantity_per_serial', 1);
        $this->db->where('products_translation.lang_id', $lang_id);

        $query = $this->db->get('products');

        if($query)
        {
            return $query->result();
        }else{
            return false ;
        }
    }


    public function get_product_countries($product_id,$lang_id)
    {
        $this->db->select('products_countries.country_id,countries_translation.name');

        $this->db->join('countries_translation','products_countries.country_id = countries_translation.country_id');
        $this->db->join('products','products.id = products_countries.product_id');

        $this->db->where('products_countries.product_id',$product_id);
        $this->db->where('countries_translation.lang_id',$lang_id);
        //$this->db->where('products.serials_per_country',1);

        $query = $this->db->get('products_countries');

        if($query)
        {
            return $query->result();
        }else{
            return false ;
        }

    }

    public function get_vendor_currency($vendor_id,$lang_id)
    {
        $this->db->select('countries_translation.currency');
        $this->db->join('countries_translation','vendors.country_id = countries_translation.country_id');

        $this->db->where('vendors.id',$vendor_id);
        $this->db->where('countries_translation.lang_id',$lang_id);

        $query = $this->db->get('vendors');

        if($query)
        {
            return $query->row();
        }else{
            return false ;
        }
    }



    public function get_vendor_purchase_orders($vendor_id)
    {
        $this->db->where('vendor_id',$vendor_id);
        return  $this->db->count_all_results('purchase_orders');
    }

    public function get_purchase_orders_row($id)
    {
        //$this->db->select('purchase_orders.* , vendors.* , currencies.currency_symbol'); // Basic Code
        $this->db->select('purchase_orders.* , vendors.store_id , vendors.country_id , currencies.currency_symbol'); // Select specific fields from vendors table to remove conflict

        $this->db->join('vendors','vendors.id = purchase_orders.vendor_id');
        $this->db->join('currencies','purchase_orders.currency_id = currencies.id');

        $this->db->where('purchase_orders.id',$id);
        $query = $this->db->get('purchase_orders');

        if($query)
        {
            return $query->row();
        }else{
            return false ;
        }
    }

    public function get_purchase_order_vendor_data($lang_id, $order_id)
    {
        $this->db->select('vendors_translation.title, purchase_orders.*, purchase_orders.id as id, vendors_translation.title as vendor');

        $this->db->join('vendors_translation', 'purchase_orders.vendor_id = vendors_translation.vendor_id');
        $this->db->where('purchase_orders.id', $order_id);
        $this->db->where('vendors_translation.lang_id', $lang_id);

        $query = $this->db->get('purchase_orders');

        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }

    public function get_purchase_orders_products_result($id, $lang_id)
    {
        $this->db->select('products_translation.title, countries_translation.name, purchase_orders_products.*');

        $this->db->join('products_translation', 'purchase_orders_products.product_id = products_translation.product_id');
        $this->db->join('countries_translation', 'purchase_orders_products.country_id = countries_translation.country_id AND countries_translation.lang_id ='.$lang_id,'left');

        $this->db->where('purchase_orders_products.purchase_order_id', $id);
        $this->db->where('products_translation.lang_id', $lang_id);

        $query = $this->db->get('purchase_orders_products');

        if($query)
        {
            return $query->result();
        }
        else
        {
            return false ;
        }
    }

   public function get_count_all_purchase_orders($search_word='')
    {
        $this->db->where('draft',0);
        return $this->db->count_all_results('purchase_orders');
    }

  public function get_purchase_orders_data($lang_id, $limit, $offset, $search_word='', $order_by='', $order_state='desc', $status_id=0, $store_filter_id, $stores_ids )
    {
        $this->db->select('vendors_translation.title, purchase_orders.*, purchase_orders.id as id');

        $this->db->join('vendors_translation', 'purchase_orders.vendor_id = vendors_translation.vendor_id');

        if($order_by != '')
        {
            if($order_by == lang('order_number'))
            {
                $this->db->order_by('purchase_orders.order_number',$order_state);
            }
            elseif($order_by == lang('vendor_id'))
            {
                $this->db->order_by('purchase_orders.vendor_id',$order_state);
            }
            else
            {
                $this->db->order_by('purchase_orders.id',$order_state);
            }
        }
        else
        {
            $this->db->order_by('purchase_orders.id','desc');
        }

        $this->db->where('vendors_translation.lang_id', $lang_id);
        $this->db->where('purchase_orders.draft', 0);


        if(trim($search_word) !='')
        {
            $this->db->where('(vendors_translation.title LIKE "%'.$search_word.'%" OR purchase_orders.order_number LIKE "%'.$search_word.'%")');
        }

        if($status_id != 0)
        {
            if($status_id == 1)
            {
                $this->db->where('purchase_orders.serials_count = serials_cash_count');
                //$this->db->where('purchase_orders.serials_cash_count != 0');

            }
            elseif($status_id == 2)
            {
                $this->db->where('purchase_orders.serials_count != serials_cash_count');
                //$this->db->where('purchase_orders.serials_cash_count != 0');
            }
        }

        if($store_filter_id != 0)
        {
            $this->db->where('purchase_orders.store_id', $store_filter_id);
        }

        if(count($stores_ids) != 0)
        {
            $this->db->where_in('purchase_orders.store_id', $stores_ids);
        }

        $result = $this->db->get('purchase_orders', $limit, $offset);

        if($result)
        {
            return $result->result();
        }
    }

    public function get_all_purshased_orders_products($lang_id, $limit, $offset)
    {
        $this->db->select('purchase_orders_products.*, purchase_orders.*, products_translation.title, purchase_orders.id as order_id, products_translation.title as product_name, currencies.*');

        $this->db->join('purchase_orders', 'purchase_orders_products.purchase_order_id = purchase_orders.id');
        $this->db->join('products_translation', 'purchase_orders_products.product_id = products_translation.product_id');
        $this->db->join('currencies', 'purchase_orders.currency_id = currencies.id ');

        $this->db->order_by('purchase_orders.id', 'desc');
        $this->db->where('purchase_orders.draft', 0);
        $this->db->where('products_translation.lang_id', $lang_id);

        $result = $this->db->get('purchase_orders_products', $limit, $offset);

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }

    }

    public function get_purshased_orders_products_count($lang_id)
    {
        $this->db->join('purchase_orders', 'purchase_orders_products.purchase_order_id = purchase_orders.id');
        $this->db->join('products_translation', 'purchase_orders_products.product_id = products_translation.product_id');

        $this->db->where('purchase_orders.draft', 0);
        $this->db->where('products_translation.lang_id', $lang_id);

        return $this->db->count_all_results('purchase_orders_products');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }

    }

    public function get_all_purshased_orders_products_count()
    {
        $this->db->join('purchase_orders', 'purchase_orders_products.purchase_order_id = purchase_orders.id');
        $this->db->where('purchase_orders.draft', 0);

        return $this->db->count_all_results('purchase_orders_products');
    }

    public function get_product_avg_cost_per_country($product_id, $country_id)
    {
        /*$this->db->select('purchase_orders_products.*');

        $this->db->join('purchase_orders', 'purchase_orders_products.purchase_order_id = purchase_orders.id');

        $this->db->where('purchase_orders.draft', 0);
        $this->db->where('purchase_orders_products.product_id', $product_id);
        $this->db->where('purchase_orders_products.country_id', $country_id);

        $this->db->select_avg('price_per_unit');
        $query = $this->db->get('purchase_orders_products');

        if($query)
        {
            return $query->row()->price_per_unit;
        }
        else
        {
            return false;
        }*/


        $this->db->where('product_id', $product_id);
        $this->db->where('country_id', $country_id);

        $row = $this->db->get('products_countries');

        if($row->row())
        {
            return $row->row()->average_cost;
        }
        else
        {
            return false;
        }

    }


    /***************************Drafts***************************/

    public function get_count_all_drafts($search_word='', $stores_ids= array())
    {
        $this->db->where('draft',1);

        if(count($stores_ids) != 0)
        {
            $this->db->where_in('store_id', $stores_ids);
        }

        return $this->db->count_all_results('purchase_orders');
    }

    public function get_drafts_data($lang_id,$limit,$offset,$search_word='', $stores_ids= array())
    {
        $this->db->select('vendors_translation.title, purchase_orders.*, purchase_orders.id as id');

        $this->db->join('vendors_translation', 'purchase_orders.vendor_id = vendors_translation.vendor_id');

        $this->db->where('purchase_orders.draft', 1);
        if(trim($search_word) != '')
        {
            $this->db->like('vendors_translation.title', $search_word, 'both');
            $this->db->or_like('purchase_orders.order_number', $search_word, 'both');
        }

        $this->db->where('vendors_translation.lang_id', $lang_id);
        //$this->db->where('purchase_orders.draft', 1);

        if(count($stores_ids) != 0)
        {
            $this->db->where_in('purchase_orders.store_id', $stores_ids);
        }

        $result = $this->db->get('purchase_orders', $limit, $offset);

        if($result)
        {
            return $result->result();
        }
    }

    public function get_row_data($id, $display_lang_id)
    {
        $this->db->select('vendors_translation.title, purchase_orders.*, purchase_orders.id as id, vendors_translation.title as vendor, stores_translation.name as store_name');

        $this->db->join('vendors_translation', 'purchase_orders.vendor_id = vendors_translation.vendor_id');
        $this->db->join('stores_translation', 'purchase_orders.store_id = stores_translation.store_id AND stores_translation.lang_id ='.$display_lang_id);

        $this->db->where('vendors_translation.lang_id',$display_lang_id);
        $this->db->where('purchase_orders.id',$id);

        $result = $this->db->get('purchase_orders');

        if($result)
        {
            return $result->row();
        }

    }
  function get_purchase_order_products($purchase_id)
  {
        $this->db->select('product_id');
        $this->db->where('purchase_order_id',$purchase_id);
        $this->db->group_by('product_id');

        $result = $this->db->get('purchase_orders_products')->result();

        if($result)
        {
            return $result;
        }
  }

  function get_purchase_order_products_details($purchase_id ,$lang_id)
  {
        $this->db->select('products_translation.title,  countries_translation.name, purchase_orders_products.*, products_translation.title as product_name');

        $this->db->join('products_translation', 'purchase_orders_products.product_id  = products_translation.product_id');
        $this->db->join('countries_translation', 'purchase_orders_products.country_id = countries_translation.country_id  AND countries_translation.lang_id ='.$lang_id,'left');

        $this->db->where('purchase_orders_products.purchase_order_id',$purchase_id);
        $this->db->where('products_translation.lang_id',$lang_id);

        $result = $this->db->get('purchase_orders_products');

        if($result)
        {
            return $result->result();
        }
  }

  function get_purchase_order_serials($id)
  {
        $this->db->where('purchase_order_id',$id);
        //$this->db->where('product_id',$product_id);

        /*return $this->db->count_all_results('products_serials');*/
        $result = $this->db->get('products_serials');

        if($result)
        {
            return $result->result();
        }

  }

  public function get_products_country_data($product_id ,$country_id=0 )
  {
        $this->db->where('product_id',$product_id);

        if($country_id != 0)
        {
            $this->db->where('country_id',$country_id);
        }

        $result = $this->db->get('purchase_orders_products');

        if($result)
        {
            return $result->result();
        }
  }

  public function get_product_purchase_orders($product_id, $country_id)
  {
    $this->db->where('product_id', $product_id);
    $this->db->where('country_id', $country_id);

    $result = $this->db->get('purchase_orders_products');

    if($result)
    {
        return $result->result();
    }
    else
    {
        return false;
    }
  }

  public function check_order_number_with_vendor($order_number, $vendor_id)
  {
    $this->db->where('order_number', $order_number);
    $this->db->where('vendor_id', $vendor_id);

    $count = $this->db->count_all_results('purchase_orders');

    if($count >0)
    {
        return true;
    }
    else{
        return false;
    }
  }

  public function get_purchase_order_product_data($purchase_order_id, $product_id, $country_id = 0, $selected_optional_fields='')
  {
     $this->db->where('product_id', $product_id);
     $this->db->where('purchase_order_id', $purchase_order_id);

     if($country_id != 0)
        $this->db->where('country_id', $country_id);

     if($selected_optional_fields != '')
        $this->db->where('selected_optional_fields', $selected_optional_fields);

     $query = $this->db->get('purchase_orders_products');

     if($query)
     {
        return $query->row();
     }
     else
     {
        return false;
     }
  }

  public function get_product_purchased_orders($product_id)
  {
     $this->db->select('purchase_orders.*, purchase_orders_products.*');
     $this->db->join('purchase_orders_products', 'purchase_orders.id = purchase_orders_products.purchase_order_id');

     $this->db->where('purchase_orders_products.product_id', $product_id);

     $result = $this->db->get('purchase_orders');

     if($result)
     {
        return $result->result();
     }
     else
     {
        return false;
     }

  }

  public function update_serials_cash_count($purchase_order_id, $serials_count)
  {
    $this->db->where('id', $purchase_order_id);
    $this->db->set('serials_cash_count', 'serials_cash_count+'.$serials_count, FALSE);

    return $this->db->update('purchase_orders');
  }

   public function get_purchase_order_store_id($purchase_order_id)
    {
        $this->db->where('id', $purchase_order_id);
        $query = $this->db->get('purchase_orders');

        if($query->row())
        {
            return $query->row()->store_id;
        }
        else
        {
            return false;
        }
    }




  ///////////////////////////////////////////
}
