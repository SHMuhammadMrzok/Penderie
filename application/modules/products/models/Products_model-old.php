<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Products_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

   /***************Delete *************/

    public function delete_products_data($product_id)
    {
        $this->db->where('product_id',$product_id);
        $this->db->delete('products_translation');

        $this->delete_tags_products($product_id);
        $this->delete_products_discounts($product_id);
        $this->delete_products_countries($product_id);
        $this->delete_products_customer_groups_prices($product_id);
    }

    public function delete_tags_products($product_id)
    {
        $this->db->where('product_id',$product_id);
        $this->db->delete('tags_products');
    }
    public function delete_products_countries($product_id)
    {
        $this->db->where('product_id',$product_id);
        $this->db->delete('products_countries');
    }

    public function delete_products_customer_groups_prices($product_id)
    {
        $this->db->where('product_id',$product_id);
        $this->db->delete('products_customer_groups_prices');
    }
    public function delete_products_discounts($product_id)
    {
        $this->db->where('product_id',$product_id);
        $this->db->delete('products_discounts');
    }

     public function delete_discount_data($discount_ids)
    {
        $this->db->where_in('products_discounts.id', $discount_ids);
        $this->db->delete('products_discounts');

        $this->db->where_in('products_discounts_countries.product_discount_id', $discount_ids);
        $this->db->delete('products_discounts_countries');

        echo '1';
    }


    public function delete_product_data($product_ids_array, $return=0)
    {
        $this->db->where_in('product_id',$product_ids_array);
        $product_serials_count = $this->db->count_all_results('products_serials');

        $this->db->where_in('product_id', $product_ids_array);
        $product_purchase_order_count = $this->db->count_all_results('purchase_orders_products');

        if($product_serials_count > 0)
        {
            $msg = lang('error_existing_serials');
        }
        elseif($product_purchase_order_count > 0)
        {
            $msg = lang('error_product_in_purchase_order');
        }
        else
        {
            $this->db->where_in('product_id',$product_ids_array);
            $this->db->delete('products_images');

            $this->db->from("products_discounts");
            $this->db->join("products_discounts_countries", "products_discounts.id = products_discounts_countries.product_discount_id");
            $this->db->where_in("products_discounts.product_id", $product_ids_array);
            $this->db->delete("products_discounts");

            $this->db->where_in('product_id',$product_ids_array);
            $this->db->delete('products_translation');

            $this->db->where_in('product_id',$product_ids_array);
            $this->db->delete('tags_products');

            $this->db->where_in('product_id',$product_ids_array);
            $this->db->delete('products_countries');

            $this->db->where_in('product_id',$product_ids_array);
            $this->db->delete('products_customer_groups_prices');

            $this->db->where_in('id',$product_ids_array);
            $this->db->delete('products');

            $this->db->where_in('product_id', $product_ids_array);
            $this->db->delete('products_optional_fields');

            $this->db->where_in('product_id', $product_ids_array);
            $this->db->delete('products_optional_fields');

            $this->db->where_in('product_id', $product_ids_array);
            $this->db->delete('products_optional_fields_options_costs');

            foreach($product_ids_array as $product_id)
            {
                $this->delete_product_cat_specs($product_id);
            }


            $msg = '1';
        }

        if($return == 0)
        {
            echo $msg;
        }
        else
        {
            return $msg;
        }
    }

    public function delete_product_cat_specs($product_id)
    {
        $species_ids         = array();
        $product_cat_species = $this->get_product_cat_specs($product_id);

        if(count($product_cat_species) != 0)
        {
            foreach($product_cat_species as $row)
            {
                $species_ids[] = $row->id;
            }

            $this->db->where_in('id', $species_ids);
            $this->db->delete('products_specifications');

            $this->db->where_in('product_spec_id', $species_ids);
            $this->db->delete('products_specifications_translation');
        }

    }

    public function delete_product_optional_field($product_id)
    {
        $this->db->where('product_id', $product_id);

        return $this->db->delete('products_optional_fields');
    }


    /********************Insert *****************************/
    public function insert_products($data)
    {
        return $this->db->insert('products', $data);
    }

    public function insert_products_translation($products_translation_data)
    {
        return $this->db->insert('products_translation', $products_translation_data);
    }

     public function insert_products_countries_prices($products_countries_data)
    {
        return $this->db->insert('products_countries', $products_countries_data);
    }

    public function insert_products_customer_groups_prices($products_customer_groups_prices_data)
    {
        return $this->db->insert('products_customer_groups_prices', $products_customer_groups_prices_data);
    }

    public function insert_product_discount_data($data)
    {
        return $this->db->insert('products_discounts', $data);
    }

    public function insert_product_discount_log($discount_log_data)
    {
        return $this->db->insert('products_discount_log', $discount_log_data);
    }

    public function insert_product_discount_countries_data($discount_countries_data)
    {
        return $this->db->insert('products_discounts_countries', $discount_countries_data);
    }

    public function insert_product_cat_specs($product_cat_spec_data)
    {
        return $this->db->insert('products_specifications', $product_cat_spec_data);
    }

    public function insert_cat_spec_translation_data($product_cat_spec_translation_data)
    {
        return $this->db->insert('products_specifications_translation', $product_cat_spec_translation_data);
    }

    public function insert_product_optional_field($data)
    {
        return $this->db->insert('products_optional_fields', $data);
    }

    public function insert_user_optional_fields_data($optiona_fields_data)
    {
        return $this->db->insert('user_products_optional_fields', $optiona_fields_data);
    }

    public function insert_image($image_data)
    {
        return $this->db->insert('gallery_images', $image_data);
    }

    public function insert_image_product($product_image_data)
    {
        return $this->db->insert('products_images', $product_image_data);
    }

   /********************GET*****************************/

   public function get_products_countries_data($product_id, $display_lang_id)
   {
        $this->db->select('products_countries.* , countries_translation .*, currencies_translation.name as currency');
        $this->db->join('countries_translation','products_countries.country_id = countries_translation.country_id');
        $this->db->join('countries','products_countries.country_id = countries.id');
        $this->db->join('currencies_translation','countries.currency_id = currencies_translation.currency_id And currencies_translation.lang_id ='.$display_lang_id);

        $this->db->where('products_countries.product_id',$product_id);
        $this->db->where('countries_translation.lang_id',$display_lang_id);

        $result = $this->db->get('products_countries');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false ;
        }
   }

   public function get_product_avg_cost($product_id, $country_id)
   {
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
        /*$this->db->where('product_id', $product_id);
        if($country_id != 0)
        {
            $this->db->where('country_id', $country_id);
        }
        $this->db->select_avg('price_per_unit');

        $row = $this->db->get('purchase_orders_products')->row();

        if($row)
        {
            return $row;
        }
        else
        {
            return false;
        }*/
   }

   public function get_country_products($country_id, $lang_id)
   {
        $this->db->select('products_countries.*, products_translation.title, products.*');

        $this->db->join('products', 'products_countries.product_id = products.id');
        $this->db->join('products_translation', 'products_countries.product_id = products_translation.product_id');
        //$this->db->join('countries_translation', 'products_countries.country_id = countries_translation.country_id');


        $this->db->where('products_translation.lang_id', $lang_id);
        //$this->db->where('countries_translation.lang_id', $lang_id);
        $this->db->where('products_countries.country_id', $country_id);

        $result = $this->db->get('products_countries')->result();

        if($result)
        {
            return $result;
        }
        else
        {
            return false ;
        }
   }

   public function get_country_other_products($order_products_ids, $country_id, $lang_id, $store_id)
   {
        $this->db->select('products_countries.*, products_translation.title, products.*');

        $this->db->join('products', 'products_countries.product_id = products.id');
        $this->db->join('products_translation', 'products_countries.product_id = products_translation.product_id');

        $this->db->where('products_translation.lang_id', $lang_id);
        $this->db->where('products_countries.country_id', $country_id);
        $this->db->where('products_countries.active', 1);
        $this->db->where('products.store_id', $store_id);


        if(count($order_products_ids) != 0)
        {
            $this->db->where_not_in('products_countries.product_id', $order_products_ids);
        }

        $result = $this->db->get('products_countries');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false ;
        }
   }

    public function get_products_customer_groups_data($product_id,$display_lang_id)
    {
        $this->db->select('products_customer_groups_prices.* , customer_groups_translation .title , countries_translation .*, currencies_translation.name as currency');
        $this->db->join('customer_groups_translation','products_customer_groups_prices.customer_group_id = customer_groups_translation.customer_group_id');
        $this->db->join('countries_translation','products_customer_groups_prices.country_id = countries_translation.country_id');
        $this->db->join('countries','products_customer_groups_prices.country_id = countries.id');
        $this->db->join('currencies_translation','countries.currency_id = currencies_translation.currency_id AND currencies_translation.lang_id ='.$display_lang_id);

        $this->db->where('products_customer_groups_prices.product_id',$product_id);
        $this->db->where('customer_groups_translation.lang_id',$display_lang_id);
        $this->db->where('countries_translation.lang_id',$display_lang_id);

        $result = $this->db->get('products_customer_groups_prices')->result();

        if($result)
        {
            return $result;
        }
        else
        {
            return false ;
        }
    }

    public function get_products_row($id)
    {
        $this->db->where('id', $id);
        $row = $this->db->get('products');

        if($row)
        {
            return $row->row();
        }
        else
        {
            return false ;
        }
    }

    public function get_products_translation_result($id)
    {
        $this->db->select('products_translation.*');
        $this->db->join('products_translation','products.id = products_translation.product_id');
        $this->db->where('products.id',$id);
        $result = $this->db->get('products')->result();

        if($result)
        {
            return $result;
        }
        else
        {
            return false ;
        }
    }

    public function get_products_countries($id)
    {
        $this->db->where('product_id',$id);
        $result = $this->db->get('products_countries');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false ;
        }
    }

    public function get_products_per_country($country_id, $lang_id, $store_id)
    {
        $this->db->select('products_countries.*, products_translation.title, countries_translation.currency, categories_translation.name as category, products.id as id');

        $this->db->join('products', 'products_countries.product_id = products.id');
        $this->db->join('categories_translation', 'products.cat_id = categories_translation.category_id AND categories_translation.lang_id ='.$lang_id);
        $this->db->join('products_translation', 'products_countries.product_id = products_translation.product_id');
        $this->db->join('countries_translation', 'products_countries.country_id = countries_translation.country_id');

        $this->db->where('products_translation.lang_id', $lang_id);
        $this->db->where('countries_translation.lang_id', $lang_id);
        $this->db->where('products_countries.country_id', $country_id);
        $this->db->where('products_countries.active', 1);
        $this->db->where('products.store_id', $store_id);

        $result = $this->db->get('products_countries')->result();

        if($result)
        {
            return $result;
        }
        else
        {
            return false;
        }
    }

    public function get_cat_products_per_country($cat_id, $country_id, $lang_id)
    {
        $this->db->select('products_countries.*, products_translation.title, countries_translation.currency');

        $this->db->join('products', 'products_countries.product_id = products.id');
        $this->db->join('products_translation', 'products_countries.product_id = products_translation.product_id');
        $this->db->join('countries_translation', 'products_countries.country_id = countries_translation.country_id');

        $this->db->where('products.cat_id', $cat_id);
        $this->db->where('products_translation.lang_id', $lang_id);
        $this->db->where('countries_translation.lang_id', $lang_id);
        $this->db->where('products_countries.country_id', $country_id);

        $result = $this->db->get('products_countries')->result();

        if($result)
        {
            return $result;
        }
        else
        {
            return false;
        }

    }

    public function products_customer_groups_prices($id)
    {
        $this->db->where('product_id',$id);
        $result = $this->db->get('products_customer_groups_prices');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false ;
        }
    }

    public function get_product_name($id, $lang_id)
    {
        $this->db->where('product_id',$id);
        $this->db->where('lang_id',$lang_id);

        $row = $this->db->get('products_translation')->row();

        if($row)
        {
            return $row->title;
        }
        else
        {
            return false ;
        }
    }

    public function get_product_translation_data($product_id, $lang_id)
    {
        $this->db->where('product_id', $product_id);
        $this->db->where('lang_id', $lang_id);

        $row = $this->db->get('products_translation')->row();

        if($row)
        {
            return $row;
        }
        else
        {
            return false;
        }
    }

    public function get_cat_products($cat_id,$lang_id)
    {
        $this->db->select('products_translation.title,products.id as id');
        $this->db->join('products_translation','products.id = products_translation.product_id');

        $this->db->where('products_translation.lang_id',$lang_id);
        $this->db->where('products.cat_id',$cat_id);

        $result = $this->db->get('products')->result();

        if($result)
        {
            return $result;
        }
        else
        {
            return false ;
        }
    }

    public function get_product_row_details($product_id, $lang_id, $country_id=0, $user_id=0)
    {
        $this->db->select('products.*, products_translation.*, products_countries.*, products.id as id,
                            stores_translation.name as store_name, categories_translation.name as cat_name,
                            brands_translation.name as brand_name, shipping_costs_translation.country as location_country,
                            shipping_cities_translation.name as location_city');

        $this->db->join('products_translation', 'products.id = products_translation.product_id');
        $this->db->join('products_countries', 'products.id = products_countries.product_id
                        AND products_countries.active=1 AND products_countries.country_id ='.$country_id, 'left');
        $this->db->join('stores_translation', 'products.store_id = stores_translation.store_id
                        AND stores_translation.lang_id ='.$lang_id, 'left');
        $this->db->join('brands_translation', 'products.brand_id = brands_translation.brand_id
                        AND brands_translation.lang_id ='.$lang_id, 'left');
        $this->db->join('categories_translation', 'products.cat_id = categories_translation.category_id
                        AND categories_translation.lang_id ='.$lang_id);
        $this->db->join('shipping_cities_translation', 'products.location_city_id = shipping_cities_translation.city_id
                        AND shipping_cities_translation.lang_id ='.$lang_id, 'left');
        $this->db->join('shipping_costs_translation', 'products.location_country_id = shipping_costs_translation.shipping_cost_id
                        AND shipping_costs_translation.lang_id ='.$lang_id, 'left');
        if($user_id != 0)
        {
            $this->db->join('users_spams', 'products.id = users_spams.product_id AND users_spams.user_id='.$user_id, 'left');
            $this->db->where('users_spams.product_id IS NULL', null, 'false');
        }


        $this->db->where('products.id', $product_id);
        $this->db->where('products_translation.lang_id', $lang_id);
        //$this->db->where('products.status_id !=', 1); // verified products only

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

    public function get_product_serials($product_id)
    {
        $this->db->where('product_id',$product_id);
        return $this->db->count_all_results('products_serials');
    }

    public function get_available_product_serials_count($product_id)
    {
        $this->db->where('product_id', $product_id);
        $this->db->where('serial_status', 0);
        $this->db->where('active', 1);

        return $this->db->count_all_results('products_serials');
    }

    public function get_products($lang_id, $limit=0, $offset=0, $country_id = 0, $cat_id=0, $store_id=0, $products_ids=array(), $conditions=array(), $all=0, $user_id=0, $spammed_users_ids=array())
    {
        $this->db->select('products.*, products_translation.*,categories_translation.name, products.id as id,
                            categories_translation.name as cat_name, stores_translation.name as store_name');

        $this->db->join('products_translation', 'products.id = products_translation.product_id');
        $this->db->join('categories_translation', 'products.cat_id = categories_translation.category_id');
        $this->db->join('products_countries', 'products.id = products_countries.product_id');
        $this->db->join('categories', 'products.cat_id = categories.id AND categories.active = 1');
        $this->db->join('stores_translation', 'products.store_id = stores_translation.store_id AND stores_translation.lang_id = '.$lang_id);

        if($user_id != 0)
        {
            $this->db->join('users_spams', 'products.id = users_spams.product_id AND users_spams.user_id='.$user_id, 'left');
            $this->db->where('users_spams.product_id IS NULL', null, 'false');
        }

        $this->db->order_by('products.sort','desc');

        $this->db->where('products_translation.lang_id', $lang_id);
        $this->db->where('categories_translation.lang_id', $lang_id);

        if($all == 0)
        {
            $this->db->where('products_countries.active', 1);
        }

        if($country_id != 0)
        {
            $this->db->where('products_countries.country_id', $country_id);
        }

        if($store_id != 0)
        {
            $this->db->where('products.store_id', $store_id);
        }

        if($cat_id != 0)
        {
            $this->db->where('products.cat_id', $cat_id);
        }
        else
        {
            $this->db->where('products_countries.display_home', 1);
        }

        if(count($products_ids) != 0)
        {
            $this->db->where_in('products.id', $products_ids);
        }

        if(count($conditions) != 0)
        {
            foreach($conditions as $key=>$val)
            {
                $this->db->where($key, $val);
            }
        }

        if(count($spammed_users_ids) != 0)
        {
            $this->db->where_not_in('products.owner_id', $spammed_users_ids);
        }

        if($limit != 0)
        {
            $result = $this->db->get('products', $limit, $offset);
        }
        else
        {
            $result = $this->db->get('products');
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

    public function get_all_products($lang_id)
    {
        $this->db->select('products.*, products_translation.*,categories_translation.name, products.id as id, categories_translation.name as cat_name');

        $this->db->join('products_translation', 'products.id = products_translation.product_id');
        $this->db->join('categories_translation', 'products.cat_id = categories_translation.category_id');

        $this->db->order_by('products.cat_id','desc');

        $this->db->where('products_translation.lang_id', $lang_id);
        $this->db->where('categories_translation.lang_id', $lang_id);
        //$this->db->where('products.active', 1);

        $result = $this->db->get('products');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_all_products_with_limit($lang_id, $limit, $offset)
    {
        $this->db->select('products.*, products_translation.*,categories_translation.name, products.id as id, categories_translation.name as cat_name');

        $this->db->join('products_translation', 'products.id = products_translation.product_id');
        $this->db->join('categories_translation', 'products.cat_id = categories_translation.category_id');

        $this->db->order_by('products.cat_id','desc');

        $this->db->where('products_translation.lang_id', $lang_id);
        $this->db->where('categories_translation.lang_id', $lang_id);
        //$this->db->where('products.active', 1);

        $result = $this->db->get('products', $limit, $offset);

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_all_products_with_limit_count($lang_id)
    {
        $this->db->select('products.*, products_translation.*,categories_translation.name, products.id as id, categories_translation.name as cat_name');

        $this->db->join('products_translation', 'products.id = products_translation.product_id');
        $this->db->join('categories_translation', 'products.cat_id = categories_translation.category_id');

        $this->db->order_by('products.cat_id','desc');

        $this->db->where('products_translation.lang_id', $lang_id);
        $this->db->where('categories_translation.lang_id', $lang_id);

        return $this->db->count_all_results('products');

    }

    public function get_products_names($lang_id)
    {
        $this->db->where('lang_id', $lang_id);
        $result = $this->db->get('products_translation')->result();

        if($result)
        {
            return $result;
        }
        else
        {
            return false;
        }
    }

    public function get_product_price($product_id, $country_id)
    {
        $this->db->where('product_id', $product_id);
        $this->db->where('country_id', $country_id);

        $row = $this->db->get('products_countries')->row();

        if($row)
        {
            return $row->price;
        }
        else
        {
            return false;
        }
    }
    public function get_product_with_translation_data($product_id, $display_lang_id)
    {
        $this->db->select('products.*, products_translation.*');
        $this->db->join('products_translation', 'products.id = products_translation.product_id');

        $this->db->where('products.id', $product_id);
        $this->db->where('products_translation.lang_id', $display_lang_id);

        $row = $this->db->get('products')->row();

        if($row)
        {
            return $row;
        }
        else
        {
            return false;
        }
    }

    public function get_all_products_data_per_country($lang_id, $country_id, $limit, $offset)
    {
        $this->db->select('products_countries.* , products.*, products_translation.title, categories_translation.name, categories_translation.name as cat_name, products_translation.title as product_name');

        $this->db->join('products_translation', 'products.id = products_translation.product_id');
        $this->db->join('products_countries', 'products.id = products_countries.product_id');
        $this->db->join('categories_translation', 'products.cat_id = categories_translation.category_id');

        $this->db->where('products_countries.country_id',$country_id);
        $this->db->where('products_translation.lang_id', $lang_id);
        $this->db->where('categories_translation.lang_id', $lang_id);
        $this->db->where('products.quantity_per_serial', 1);

        $result = $this->db->get('products', $limit, $offset)->result();

        if($result)
        {
            return $result;
        }
        else
        {

            return false ;
        }
    }

    public function check_product_exist($product_id)
    {
        $this->db->where('id', $product_id);
        $count = $this->db->count_all_results('products');

        if($count > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function get_product_serials_count_per_country($country_id, $product_id)
    {
        $this->db->where('country_id', $country_id);
        $this->db->where('product_id', $product_id);

        return $this->db->count_all_results('products_serials');
    }

    public function get_product_quantity($product_id, $country_id)
    {
        $this->db->where('product_id', $product_id);
        $this->db->where('country_id', $country_id);

        $row = $this->db->get('products_countries')->row();

        if($row)
        {
            return $row->product_quantity;
        }
        else
        {
            return false;
        }
    }

    public function get_product_translation($product_id)
    {
        $active_language = $this->admin_bootstrap->get_active_language_row();
        $lang_id = $active_language->id;

        $this->db->where('lang_id',$lang_id);
        $this->db->where('product_id',$product_id);

        $row = $this->db->get('products_translation')->row();

        if($row)
        {
            return $row;
        }
        else
        {
            return false;
        }
    }

    public function get_product_eith_translation_data($product_id, $lang_id)
    {
        $this->db->select('products_translation.title, categories_translation.name as cat_name, products_translation.title as product_name');

        $this->db->join('products_translation', 'products.id = products_translation.product_id');
        $this->db->join('categories_translation', 'products.cat_id = categories_translation.category_id');

        $this->db->where('products.id', $product_id);
        $this->db->where('products_translation.lang_id', $lang_id);
        $this->db->where('categories_translation.lang_id', $lang_id);

        $query = $this->db->get('products');

        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }

    public function get_count_all_wholesaler_products($lang_id, $child_cats_ids=0, $country_id=0)
    {
        $this->db->join('products_translation', 'products.id = products_translation.product_id');
        $this->db->join('products_countries', 'products.id = products_countries.product_id AND products_countries.active = 1');
        $this->db->join('categories_translation', 'products.cat_id = categories_translation.category_id');
        //$this->db->join('categories', 'products.cat_id = categories.id AND categories.active = 1');

       if($child_cats_ids != 0)
        {
            $this->db->where_in('categories_translation.category_id', $child_cats_ids);
            $this->db->where_in('products.cat_id', $child_cats_ids);
        }
        else
        {
            $this->db->where('products_countries.display_home', 1);
        }

        if($country_id != 0)
        {
            $this->db->where('products_countries.country_id', $country_id);
        }


        $this->db->where('products_translation.lang_id', $lang_id);
        $this->db->where('categories_translation.lang_id', $lang_id);
        $this->db->where('products.wholesaler_device', 1);

        return $this->db->count_all_results('products');
    }

    public function get_wholesaler_products($lang_id, $limit, $offset, $country_id = 0, $child_cats_ids=0)
    {
        $this->db->select('products.*, products_translation.*,categories_translation.name, products.id as id, categories_translation.name as cat_name');

        $this->db->join('products_translation', 'products.id = products_translation.product_id');
        $this->db->join('categories_translation', 'products.cat_id = categories_translation.category_id');
        $this->db->join('products_countries', 'products.id = products_countries.product_id');
        //$this->db->join('categories', 'products.cat_id = categories.id AND categories.active = 1');

        $this->db->order_by('products.cat_id','desc');

        $this->db->where('products_translation.lang_id', $lang_id);
        $this->db->where('categories_translation.lang_id', $lang_id);
        $this->db->where('products_countries.active', 1);
        $this->db->where('products.wholesaler_device', 1);

        if($country_id != 0)
        {
            $this->db->where('products_countries.country_id', $country_id);
        }

        if($child_cats_ids != 0)
        {
            $this->db->where_in('products.cat_id', $child_cats_ids);
        }
        else
        {
            $this->db->where('products_countries.display_home', 1);
        }

        $result = $this->db->get('products', $limit, $offset);

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }

    }



     /****************************Product Discounts Functions*********************************/


    public function get_product_discount_data($product_discount_id, $lang_id)
    {
        $this->db->select('products_discounts.*,products_translation.title as product_name');
        $this->db->join('products_translation', 'products_discounts.product_id = products_translation.product_id AND products_translation.lang_id='.$lang_id);

        $this->db->where('products_discounts.id', $product_discount_id);

        $result = $this->db->get('products_discounts');

        if($result)
        {
            return $result->row();
        }
        else
        {
            return false;
        }

    }

    public function get_discount_countries_data($product_discount_id, $lang_id)
    {
        $this->db->select('products_discounts.*, products_discounts_countries.*, countries.*, countries_translation.*, products_countries.price as country_price, products_discounts.id as id');

        $this->db->join('countries_translation', 'products_discounts_countries.country_id = countries_translation.country_id AND countries_translation.lang_id='.$lang_id);
        $this->db->join('products_discounts', 'products_discounts_countries.country_id = countries_translation.country_id AND products_discounts.id='. $product_discount_id);
        $this->db->join('countries', 'products_discounts_countries.country_id = countries.id');
        $this->db->join('products_countries','products_discounts.product_id = products_countries.product_id AND products_countries.country_id = products_discounts_countries.country_id');

        $this->db->where('products_discounts_countries.product_discount_id', $product_discount_id);

        $result = $this->db->get('products_discounts_countries');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }

    }


    public function count_available_discounts_on_product($product_id, $country_id)
    {
        $this->db->join('products_discounts_countries', 'products_discounts.id = products_discounts_countries.product_discount_id');

        $this->db->where('products_discounts_countries.active', 1);
        $this->db->where('products_discounts_countries.country_id', $country_id);
        $this->db->where('products_discounts.product_id', $product_id);

        return $this->db->count_all_results('products_discounts');
    }

    public function get_product_active_discounts($product_id, $country_id)
    {
        $this->db->select('products_discounts.*, products_discounts_countries.*');
        $this->db->join('products_discounts_countries', 'products_discounts.id = products_discounts_countries.product_discount_id');

        $this->db->order_by('discount_end_unix_time desc');//, 'id desc');

        $this->db->where('products_discounts_countries.active', 1);
        $this->db->where('products_discounts_countries.country_id', $country_id);
        $this->db->where('products_discounts.product_id', $product_id);

        $row = $this->db->get('products_discounts');

        if($row)
        {
            return $row->row();
        }
        else
        {
            return false;
        }
    }

    public function get_product_id($id)
    {
        $this->db->where('id',$id);
        $row = $this->db->get('products_discounts')->row();

        if($row)
        {
            return $row->product_id;
        }
        else
        {
            return false;
        }
    }

    public function get_products_discounts_data($lang_id, $limit, $offset,$search_word='', $order_by, $order_state, $stores_ids)
    {
        $this->db->select('products_discounts.*, products_translation.*, products_discounts.id as id, products_translation.title as product_name ');

        $this->db->join('products' ,'products_discounts.product_id = products.id');
        $this->db->join('products_translation' ,'products_discounts.product_id = products_translation.product_id');

        $this->db->where('products_translation.lang_id',$lang_id);

        if(trim($search_word) !='')
        {
            $this->db->where('(products_translation.title LIKE "%'.$search_word.'%")');// OR countries_translation.name LIKE "%'.$search_word.'%")');
        }

        if($order_by != '')
        {
            if($order_by == lang('product_name'))
            {
                $this->db->order_by('products_translation.title', $order_state);
            }
            elseif($order_by == lang('country'))
            {
                $this->db->order_by('countries_translation.name', $order_state);
            }
            elseif($order_by == lang('price'))
            {
                $this->db->order_by('products_discounts.price', $order_state);
            }
            elseif($order_by == lang('discount_start_unix_time'))
            {
                $this->db->order_by('products_discounts.discount_start_unix_time', $order_state);
            }
            elseif($order_by == lang('discount_end_unix_time'))
            {
                $this->db->order_by('products_discounts.discount_end_unix_time', $order_state);
            }
            elseif($order_by == lang('active'))
            {
                $this->db->order_by('products_discounts.active', $order_state);
            }
            elseif($order_by == lang('max_units_customers'))
            {
                $this->db->order_by('products_discounts.max_units_customers', $order_state);
            }
            elseif($order_by == lang('sort'))
            {
                $this->db->order_by('products_discounts.sort', $order_state);
            }
            else
            {
                $this->db->order_by('products_discounts.id', $order_state);
            }
        }
        else
        {
            $this->db->order_by('products_discounts.id', $order_state);
        }

        if(count($stores_ids) != 0)
        {
            $this->db->where_in('products.store_id', $stores_ids);
        }

        $result = $this->db->get('products_discounts', $limit, $offset);

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_count_all_products_discounts($lang_id,$search_word='')
    {
        $this->db->join('products_translation', 'products_discounts.product_id = products_translation.product_id');

        if(trim($search_word) !='')
        {
            $this->db->like('products_translation.title', $search_word, 'both');
        }

        $this->db->where('products_translation.lang_id',$lang_id);

        return $this->db->count_all_results('products_discounts');
    }

    public function get_products_data($lang_id, $limit, $offset, $search_word='', $order_by='', $order_state, $category_id=0, $store_id=0, $stores_ids=array())
    {
        $this->db->select('products.*, products_translation.*, categories_translation.name, products.id as id, categories_translation.name as category, stores_translation.name as store_name');

        $this->db->join('products_translation' ,'products.id = products_translation.product_id');
        $this->db->join('categories_translation','products.cat_id = categories_translation.category_id');
        $this->db->join('stores_translation','products.store_id = stores_translation.store_id AND stores_translation.lang_id ='.$lang_id);

        $this->db->where('categories_translation.lang_id',$lang_id);
        $this->db->where('products_translation.lang_id',$lang_id);

        if(trim($search_word) !='')
        {
            $this->db->where('(products_translation.title LIKE "%'.$search_word.'%" OR products.code LIKE "%'.$search_word.'%")');
        }

        if($category_id !=0)
        {
            $this->db->where('categories_translation.category_id',$category_id);
        }

        if($store_id != 0)
        {
            $this->db->where('products.store_id', $store_id);
        }

        if($order_by != '')
        {
            if($order_by == lang('product_name'))
            {
                $this->db->order_by('products_translation.title', $order_state);
            }
            elseif($order_by == lang('category'))
            {
                $this->db->order_by('products.cat_id', $order_state);
            }
            elseif($order_by == lang('serials_count'))
            {
                $this->db->select('COUNT(products_serials.id) as serials_count', FALSE);
                $this->db->join('products_serials', 'products.id = products_serials.product_id AND products_serials.serial_status = 0', 'left');
                $this->db->group_by('products.id');
                $this->db->order_by('serials_count', $order_state);
            }
            elseif($order_by == lang('code'))
            {
                $this->db->order_by('products.code', $order_state);
            }
            elseif($order_by == lang('sort'))
            {
                $this->db->order_by('products.sort', $order_state);
            }
            else
            {
                $this->db->order_by('products.id', $order_state);
            }
        }
        else
        {
            $this->db->order_by('products.id','desc');
        }

        if(count($stores_ids) != 0)
        {
            $this->db->where_in('products.store_id', $stores_ids);
        }

        $result = $this->db->get('products', $limit, $offset);

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_products_data_count($lang_id,$search_word='',$order_by='',$order_state='desc',$category_id=0)
    {

        $this->db->join('products_translation' ,'products.id = products_translation.product_id');
        $this->db->join('categories_translation','products.cat_id = categories_translation.category_id');


        $this->db->where('categories_translation.lang_id',$lang_id);
        $this->db->where('products_translation.lang_id',$lang_id);

        if(trim($search_word) !='')
        {
            $this->db->where('(products_translation.title LIKE "%'.$search_word.'%" OR products.code LIKE "%'.$search_word.'%")');
        }

        if($category_id !=0)
        {
            $this->db->where('categories_translation.category_id',$category_id);
        }
        if($order_by != '')
        {
            if($order_by == lang('product_name'))
            {
                $this->db->order_by('products_translation.title',$order_state);
            }
            elseif($order_by == lang('category'))
            {
                $this->db->order_by('products.cat_id',$order_state);
            }
            elseif($order_by == lang('code'))
            {
                $this->db->order_by('products.code',$order_state);
            }
            else
            {
                $this->db->order_by('products.id',$order_state);
            }
        }
        else
        {
            $this->db->order_by('products.id','desc');
        }

        return $this->db->count_all_results('products');
    }


    public function get_count_all_products($lang_id, $stores_ids=array(), $search_word='', $category_id=0, $store_id=0)
    {
        $this->db->join('products_translation', 'products.id = products_translation.product_id');
        $this->db->join('products_countries', 'products.id = products_countries.product_id AND products_countries.active = 1');
        $this->db->join('categories_translation', 'products.cat_id = categories_translation.category_id');
        $this->db->join('categories', 'products.cat_id = categories.id AND categories.active = 1');

        if(count($stores_ids) != 0)
        {
            $this->db->where_in('products.store_id', $stores_ids);
        }

        if(trim($search_word) !='')
        {
            $this->db->like('products_translation.title', $search_word, 'both');
        }

        if($category_id != 0)
        {
            $this->db->where('categories_translation.category_id',$category_id);
            $this->db->where('products.cat_id', $category_id);
        }
        else
        {
            $this->db->where('products_countries.display_home', 1);
        }

        if($store_id != 0)
        {
            $this->db->where('products.store_id', $store_id);
        }

        $this->db->where('products_translation.lang_id',$lang_id);
        $this->db->where('categories_translation.lang_id',$lang_id);

        return $this->db->count_all_results('products');
    }


    public function get_product_discount_row_data($id, $display_lang_id)
    {
        $this->db->select('products_discounts.*, products_discounts_countries.*, products_translation.*, countries_translation.name,
                            products_discounts.id as id, countries_translation.name as country, products_translation.title as product_name,
                             currencies.currency_symbol');

        $this->db->join('products_discounts_countries', 'products_discounts.id = products_discounts_countries.product_discount_id');
        $this->db->join('products_translation', 'products_discounts.product_id = products_translation.product_id');
        $this->db->join('countries_translation', 'products_discounts_countries.country_id = countries_translation.country_id');
        $this->db->join('countries', 'products_discounts_countries.country_id = countries.id');
        $this->db->join('currencies', 'countries.currency_id = currencies.id');


        $this->db->where('products_discounts.id', $id);
        $this->db->where('countries_translation.lang_id', $display_lang_id);
        $this->db->where('products_translation.lang_id', $display_lang_id);

        $result = $this->db->get('products_discounts');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_product_discount_row($id)
    {
        $this->db->select('products.*, products_discounts.*, products_discounts.id as id');
        $this->db->join('products', 'products_discounts.product_id = products.id');

        $this->db->where('products_discounts.id', $id);

        $query = $this->db->get('products_discounts');

        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }


    public function get_row_data($id, $display_lang_id)
    {
        $this->db->select('products.*, products_translation.*, stores_translation.name as store_name, categories_translation.name, products.id as id, categories_translation.name as category');

        $this->db->join('products_translation' ,'products.id = products_translation.product_id');
        $this->db->join('categories_translation','products.cat_id = categories_translation.category_id');
        $this->db->join('stores_translation','products.store_id = stores_translation.store_id AND stores_translation.lang_id = '.$display_lang_id, 'left');

        $this->db->where('products.id',$id);
        $this->db->where('products_translation.lang_id',$display_lang_id);
        $this->db->where('categories_translation.lang_id',$display_lang_id);

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

  public function get_product_amount($product_id)
  {
    $this->db->where('product_id',$product_id);
    return $this->db->count_all_results('products_serials');
  }

  public function get_product_purchase_orders ($product_id)
  {
    $this->db->select('purchase_order_id');
    $this->db->where('product_id', $product_id);
    $this->db->group_by('purchase_order_id');

    $result = $this->db->get('purchase_orders_products')->result_array();

    if($result)
    {
        return $result;
    }
  }

  public function get_product_purchase_orders_count($product_id, $draft=0)
  {
    $this->db->join('purchase_orders_products', 'purchase_orders.id = purchase_orders_products.purchase_order_id');

    $this->db->where('purchase_orders_products.product_id', $product_id);
    $this->db->where('purchase_orders.draft', $draft);

    return $this->db->count_all_results('purchase_orders');
  }

  public function get_products_amount($product_id, $country_id)
  {
    $this->db->where('product_id', $product_id);
    $this->db->where('country_id', $country_id);

    $row = $this->db->get('products_countries')->row();

    if($row)
    {
        return $row->product_quantity;
    }
    else
    {
        return false;
    }
  }

  public function count_product_available_quantity($product_id, $country_id)
  {
    $this->db->group_start();
        $this->db->where('product_id', $product_id);
        $this->db->where('serial_status', 0);
        $this->db->where('invalid', 0);
        $this->db->where('active', 1);
    $this->db->group_end();

    $this->db->group_start();
        $this->db->where('country_id', $country_id);
        $this->db->or_where('country_id', 0);
    $this->db->group_end();

    return $this->db->count_all_results('products_serials');
  }

  public function count_product_all_quantity($product_id)
  {
    $this->db->where('product_id', $product_id);
    $this->db->where('serial_status', 0);

    return $this->db->count_all_results('products_serials');
  }

  public function get_reward_points($product_id, $country_id)
  {
    $this->db->where('product_id', $product_id);
    $this->db->where('country_id', $country_id);

    $row = $this->db->get('products_countries')->row();

    if($row)
    {
        return $row->reward_points;
    }
    else
    {
        return false;
    }
  }

  public function get_product_country_data($product_id, $country_id)
  {
    $this->db->select('products.*, products_countries.*');
    $this->db->join('products', 'products_countries.product_id = products.id');

    $this->db->where('products_countries.product_id', $product_id);
    $this->db->where('products_countries.country_id', $country_id);

    $row = $this->db->get('products_countries');

    if($row)
    {
        return $row->row();
    }
    else
    {
        return false;
    }
  }


    public function get_count_products()
    {
        return $this->db->count_all('products');
    }

    public function get_products_filter_data($lang_id)
    {
        $this->db->select('products.*, products_translation.title as name');
        $this->db->join('products_translation' , 'products.id = products_translation.product_id');

        $this->db->where('products_translation.lang_id',$lang_id);

        $result = $this->db->get('products');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_product_cat_id($product_id)
    {
        $this->db->where('id', $product_id);
        $row = $this->db->get('products')->row();

        if($row)
        {
            return $row->cat_id;
        }
        else
        {
            return false;
        }
    }

    public function get_customer_group_price($product_id, $country_id, $customer_group_id)
    {
        $this->db->where('product_id', $product_id);
        $this->db->where('country_id', $country_id);
        $this->db->where('customer_group_id', $customer_group_id);

        $row = $this->db->get('products_customer_groups_prices')->row();

        if($row)
        {
            return $row->group_price;
        }
        else
        {
            return false;
        }
    }

    public function get_customer_group_price_data($product_id, $country_id, $customer_group_id)
    {
        $this->db->where('product_id', $product_id);
        $this->db->where('country_id', $country_id);
        $this->db->where('customer_group_id', $customer_group_id);

        $row = $this->db->get('products_customer_groups_prices');

        if($row)
        {
            return $row->row();
        }
        else
        {
            return false;
        }
    }

    public function get_product_discount($product_id , $lang_id)
    {
        $this->db->select('products_discounts.*, products_discounts_countries.*, countries_translation.name');

        $this->db->join('products_discounts_countries' , 'products_discounts.id = products_discounts_countries.product_discount_id');
        $this->db->join('countries_translation' , 'products_discounts_countries.country_id = countries_translation.country_id');

        $this->db->where('products_discounts.product_id',$product_id);
        $this->db->where('countries_translation.lang_id',$lang_id);

        $result = $this->db->get('products_discounts');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false ;
        }
    }

    public function get_product_stock($id ,$lang_id)
    {

        $this->db->select('purchase_orders.*, purchase_orders_products.*, countries_translation.name ,vendors_translation.title, countries_translation.name as country, purchase_orders.id as id');

        $this->db->join('purchase_orders_products', 'purchase_orders.id = purchase_orders_products.purchase_order_id');
        $this->db->join('countries_translation', 'purchase_orders_products.country_id = countries_translation.country_id AND countries_translation.lang_id = '.$lang_id, 'left');
        $this->db->join('vendors_translation', 'purchase_orders.vendor_id = vendors_translation.vendor_id');

        $this->db->where('purchase_orders.draft', 0);
        $this->db->where('purchase_orders_products.product_id', $id);
        $this->db->where('vendors_translation.lang_id', $lang_id);

        $result = $this->db->get('purchase_orders');

        if($result)
        {
            return $result->result();

        }
        else
        {
            return false ;
        }
    }

    public function get_product_stock_data($limit, $offset, $id, $lang_id)
    {

        $this->db->select('purchase_orders.*, purchase_orders_products.*, countries_translation.name ,vendors_translation.title, countries_translation.name as country, purchase_orders.id as id');

        $this->db->join('purchase_orders_products', 'purchase_orders.id = purchase_orders_products.purchase_order_id');
        $this->db->join('countries_translation', 'purchase_orders_products.country_id = countries_translation.country_id AND countries_translation.lang_id = '.$lang_id, 'left');
        $this->db->join('vendors_translation', 'purchase_orders.vendor_id = vendors_translation.vendor_id');

        $this->db->where('purchase_orders.draft', 0);
        $this->db->where('purchase_orders_products.product_id', $id);
        $this->db->where('vendors_translation.lang_id', $lang_id);

        $result = $this->db->get('purchase_orders', $limit, $offset);

        if($result)
        {
            return $result->result();

        }
        else
        {
            return false ;
        }
    }

    public function count_product_stock_data($id, $lang_id)
    {

        $this->db->select('purchase_orders.*, purchase_orders_products.*, countries_translation.name ,vendors_translation.title, countries_translation.name as country, purchase_orders.id as id');

        $this->db->join('purchase_orders_products', 'purchase_orders.id = purchase_orders_products.purchase_order_id');
        $this->db->join('countries_translation', 'purchase_orders_products.country_id = countries_translation.country_id AND countries_translation.lang_id = '.$lang_id, 'left');
        $this->db->join('vendors_translation', 'purchase_orders.vendor_id = vendors_translation.vendor_id');

        $this->db->where('purchase_orders.draft', 0);
        $this->db->where('purchase_orders_products.product_id', $id);
        $this->db->where('vendors_translation.lang_id', $lang_id);

        return $this->db->count_all_results('purchase_orders');

    }

    public function get_product_stock_count($purchase_order_id ,$product_id,$country_id)
    {
        $this->db->where('purchase_order_id',$purchase_order_id);
        $this->db->where('product_id',$product_id);
        $this->db->where('country_id',$country_id);
        $this->db->where('serial_status',0);

        return $this->db->count_all_results('products_serials');
    }

    public function get_product_sales($product_id ,$lang_id)
    {
        $this->db->select('orders.*, countries_translation.name, orders_products.*, orders_products.unix_time as unix_time, orders.id as id');

        $this->db->join('countries_translation', 'orders.country_id = countries_translation.country_id');
        $this->db->join('orders_products', 'orders.id = orders_products.order_id');

        $this->db->where('orders.order_status_id', 1);
        $this->db->where('orders_products.product_id', $product_id);
        $this->db->where('countries_translation.lang_id', $lang_id);

        $this->db->order_by('orders_products.order_id', 'DESC');

        $result = $this->db->get('orders');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false ;
        }
    }

    public function get_product_sales_data($limit, $offset, $product_id ,$lang_id)
    {
        $this->db->select('orders.*, countries_translation.name, orders_products.*, orders_products.unix_time as unix_time, orders.id as id, countries_translation.name as country_name');

        $this->db->join('countries_translation', 'orders.country_id = countries_translation.country_id');
        $this->db->join('orders_products', 'orders.id = orders_products.order_id');

        $this->db->where('orders.order_status_id', 1);
        $this->db->where('orders_products.product_id', $product_id);
        $this->db->where('countries_translation.lang_id', $lang_id);

        $this->db->order_by('orders_products.order_id', 'DESC');

        $result = $this->db->get('orders', $limit, $offset);

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false ;
        }
    }

    public function count_sales_rows($product_id ,$lang_id)
    {
        $this->db->select('orders.*, countries_translation.name, orders_products.*, orders_products.unix_time as unix_time, orders.id as id');

        $this->db->join('countries_translation', 'orders.country_id = countries_translation.country_id');
        $this->db->join('orders_products', 'orders.id = orders_products.order_id');

        $this->db->where('orders.order_status_id', 1);
        $this->db->where('orders_products.product_id', $product_id);
        $this->db->where('countries_translation.lang_id', $lang_id);

        $this->db->order_by('orders_products.order_id', 'DESC');

        return  $this->db->count_all_results('orders');
    }

   public function get_product_average_cost($product_id ,$country_id)
   {
        $this->db->where('product_id',$product_id);
        $this->db->where('country_id',$country_id);

        $row = $this->db->get('products_countries')->row();

        if($row)
        {
            return $row;
        }
        else
        {
            return false ;
        }
   }

   public function check_product_in_country($product_id, $country_id)
   {
        $this->db->where('product_id', $product_id);
        $this->db->where('country_id', $country_id);

        return $this->db->count_all_results('products_countries');
   }

   /********************************** UPDATE ************************************************/
    public function update_products($product_id,$products_data)
    {
        $this->db->where('id',$product_id);
        $this->db->update('products',$products_data);
    }


    public function update_products_translation($product_id,$lang_id,$products_translation_data)
    {
        $this->db->where('product_id',$product_id);
        $this->db->where('lang_id',$lang_id);
        $this->db->update('products_translation',$products_translation_data);
    }


   public function update_products_countries($id,$country_id,$products_countries_data)
    {
        $this->db->where('product_id',$id);
        $this->db->where('country_id',$country_id);
        $this->db->update('products_countries',$products_countries_data);
    }

    public function update_products_customer_groups_prices($id,$country_id,$group_id,$products_customer_groups_prices_data)
    {
        $this->db->where('product_id',$id);
        $this->db->where('country_id',$country_id);
        $this->db->where('customer_group_id',$group_id);
        $this->db->update('products_customer_groups_prices',$products_customer_groups_prices_data);
    }


    public function update_product_country_amount($updated_amount, $product_id, $country_id)
    {
        $this->db->where('product_id', $product_id);
        $this->db->where('country_id', $country_id);

        $this->db->update('products_countries', $updated_amount);
    }

    public function update_product_countries($product_id, $country_id=0, $updated_data)
    {
        $this->db->where('product_id', $product_id);

        if($country_id != 0)
        {
            $this->db->where('country_id', $country_id);
        }

        return $this->db->update('products_countries', $updated_data);
    }
    public function update_product_discount_data($data,$product_discount_id)
    {
        $this->db->where('id',$product_discount_id);
        return $this->db->update('products_discounts',$data);
    }

    public function update_product_discount_country_data($data, $product_discount_id, $country_id)
    {
        $this->db->where('product_discount_id', $product_discount_id);
        $this->db->where('country_id', $country_id);

        return $this->db->update('products_discounts_countries', $data);
    }

    public function update_user_optional_fields($cart_id, $product_id, $updated_data)
    {
        $this->db->where('cart_product_id', 0);
        $this->db->where('shopping_cart_id', $cart_id);
        $this->db->where('product_id', $product_id);

        return $this->db->update('user_products_optional_fields', $updated_data);
    }

    public function update_product_optional_fields_data($cart_id, $cart_product_id, $updated_data)
    {
        $this->db->where('shopping_cart_id', $cart_id);
        $this->db->where('cart_product_id', $cart_product_id);

        return $this->db->update('user_products_optional_fields', $updated_data);
    }

    public function get_product_discount_max_per_user($product_id, $country_id)
    {
        $this->db->order_by('id', 'desc');

        $this->db->where('product_id', $product_id);
        $this->db->where('country_id', $country_id);

        $query = $this->db->get('products_discounts');

        if($query)
        {
            return $query->row()->max_units_customers;
        }
        else
        {
            return false;
        }
    }

    public function check_product_country_exist($product_id, $country_id)
    {
        $this->db->where('product_id', $product_id);
        $this->db->where('country_id', $country_id);
        $this->db->where('active', 1);

        $count = $this->db->count_all_results('products_countries');

        if($count > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function count_user_discount_uses($user_id, $session_id, $product_discount_id, $country_id)
    {
        $this->db->select_sum('products_discount_log.qty');

        $this->db->join('orders', 'products_discount_log.order_id = orders.id');
        $reject_ids = array(3, 4);
        $this->db->where_not_in('orders.order_status_id', $reject_ids);
        $this->db->where('orders.country_id', $country_id);

        $this->db->where('products_discount_log.user_id', $user_id);
        $this->db->where('products_discount_log.discount_id', $product_discount_id);

        if($user_id == 0)
        {
            $this->db->where('products_discount_log.session_id', $session_id);
        }

        $result = $this->db->get('products_discount_log');

        if($result->row()->qty != '')
        {
            return $result->row()->qty;
        }
        else
        {
            return 0;
        }
    }

    public function get_order_product_data($order_id, $product_id)
    {
        $this->db->where('order_id', $order_id);
        $this->db->where('product_id', $product_id);

        $query = $this->db->get('orders_products');

        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }

    public function get_product_cat_specs_result($cat_id, $lang_id, $product_id)
    {
        $this->db->select('products_specifications.*, products_specifications_translation.*, products_specifications.id as id,
                        categories_specifications_translation.*, products_specifications_translation.lang_id as lang_id');

        $this->db->join('products_specifications', 'categories_specifications.id = products_specifications.cat_spec_id
                        AND products_specifications.product_id='. $product_id, 'left');
        $this->db->join('products_specifications_translation', 'products_specifications.id = products_specifications_translation.product_spec_id', 'left');
        $this->db->join('categories_specifications_translation', 'categories_specifications.id = categories_specifications_translation.category_specification_id
                         AND categories_specifications_translation.lang_id ='.$lang_id);

        $this->db->where('categories_specifications.cat_id', $cat_id);


        $result = $this->db->get('categories_specifications');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_product_cat_specs_data($product_id, $lang_id)
    {
        $this->db->select('products_specifications.*, products_specifications_translation.*, products_specifications.id as id, categories_specifications_translation.*, products_specifications_translation.lang_id as lang_id');

        $this->db->join('products_specifications_translation', 'products_specifications.id = products_specifications_translation.product_spec_id AND products_specifications_translation.lang_id ='.$lang_id);
        $this->db->join('categories_specifications_translation', 'products_specifications.cat_spec_id = categories_specifications_translation.category_specification_id AND categories_specifications_translation.lang_id ='.$lang_id);

        $this->db->where('products_specifications.product_id', $product_id);

        $result = $this->db->get('products_specifications');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_product_cat_specs($product_id)
    {
        $this->db->where('product_id', $product_id);

        $result = $this->db->get('products_specifications');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_count_country_products($lang_id)
    {
        $this->db->join('products', 'products_countries.product_id = products.id');
        $this->db->join('products_translation', 'products_countries.product_id = products_translation.product_id AND products_translation.lang_id = '.$lang_id);
        //$this->db->join('countries', 'products_countries.country_id = countries.id', 'left');
        //$this->db->join('currencies', 'countries.currency_id = currencies.id', 'left');
        $this->db->join('countries_translation', 'products_countries.country_id = countries_translation.country_id AND countries_translation.lang_id = '.$lang_id, 'left');


        return $this->db->count_all_results('products_countries');

    }

    public function get_products_per_countries($lang_id, $limit, $offset)
    {
        $this->db->select('products_countries.*, products.*, products_translation.title as product_name, countries_translation.name as country_name, products.id as id');

        $this->db->join('products', 'products_countries.product_id = products.id');
        $this->db->join('products_translation', 'products_countries.product_id = products_translation.product_id AND products_translation.lang_id = '.$lang_id);
        $this->db->join('countries_translation', 'products_countries.country_id = countries_translation.country_id AND countries_translation.lang_id = '.$lang_id, 'left');

        $this->db->order_by('products_countries.product_id');

        $result = $this->db->get('products_countries', $limit, $offset);

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_product_optional_fields($product_id, $lang_id, $group_id=0, $primary_id=0)
    {
        $this->db->select('products_optional_fields.*, optional_fields_translation.label, optional_fields_translation.text,
                           optional_fields.*, form_fields_types.*, products_translation.title as product_name,
                           optional_fields.id as id, optional_fields_groups_translation.name as group_name, optional_fields_groups_translation.group_id as group_id');

        $this->db->join('optional_fields', 'products_optional_fields.optional_field_id = optional_fields.id');
        $this->db->join('form_fields_types', 'optional_fields.field_type_id = form_fields_types.id');
        $this->db->join('optional_fields_translation', 'products_optional_fields.optional_field_id = optional_fields_translation.optional_field_id AND optional_fields_translation.lang_id ='.$lang_id);
        $this->db->join('products_translation', 'products_optional_fields.product_id = products_translation.product_id AND products_translation.lang_id ='.$lang_id);
        $this->db->join('optional_fields_groups_translation', 'products_optional_fields.field_group_id = optional_fields_groups_translation.group_id
                         AND optional_fields_groups_translation.lang_id ='.$lang_id, 'left');

        $this->db->order_by('optional_fields.priority', 'asc');

        $this->db->where('products_optional_fields.product_id', $product_id);
        $this->db->where('products_optional_fields.primary_option_id', $primary_id);

        if($group_id != 0)
        {
            $this->db->where('optional_fields_groups_translation.group_id', $group_id);
        }

        $result = $this->db->get('products_optional_fields');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    /*public function get_product_optional_fields($product_id, $lang_id)
    {
        $this->db->select('products_optional_fields.*, optional_fields_translation.label, optional_fields.*, form_fields_types.*, products_translation.title as product_name, products_optional_fields.id as id');

        $this->db->join('optional_fields', 'products_optional_fields.optional_field_id = optional_fields.id');
        $this->db->join('form_fields_types', 'optional_fields.field_type_id = form_fields_types.id');
        $this->db->join('optional_fields_translation', 'products_optional_fields.optional_field_id = optional_fields_translation.optional_field_id AND optional_fields_translation.lang_id ='.$lang_id);
        $this->db->join('products_translation', 'products_optional_fields.product_id = products_translation.product_id AND products_translation.lang_id ='.$lang_id);

        $this->db->order_by('optional_fields.priority', 'asc');

        $this->db->where('products_optional_fields.product_id', $product_id);

        $result = $this->db->get('products_optional_fields');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }*/

    public function get_product_optional_field_data($option_id)
    {
        $this->db->select('products_optional_fields.*, optional_fields_translation.label, optional_fields.*, form_fields_types.*, products_optional_fields.id as id');

        $this->db->join('optional_fields', 'products_optional_fields.optional_field_id = optional_fields.id');
        $this->db->join('form_fields_types', 'optional_fields.field_type_id = form_fields_types.id');
        $this->db->join('optional_fields_translation', 'products_optional_fields.optional_field_id = optional_fields_translation.optional_field_id AND optional_fields_translation.lang_id ='.$lang_id);

        $this->db->order_by('optional_fields.priority', 'asc');

        $this->db->where('products_optional_fields.product_id', $product_id);

        $query = $this->db->get('products_optional_fields');

        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }

    public function count_product_optional_fields($product_id)
    {
        $this->db->where('product_id', $product_id);

        return $this->db->count_all_results('products_optional_fields');
    }

    public function count_user_product_optional_fields($cart_product_id)
    {
        $this->db->where('cart_product_id', $cart_product_id);

        return $this->db->count_all_results('user_products_optional_fields');
    }

    public function get_user_optional_fields($cart_product_id, $lang_id)
    {
        $this->db->select('user_products_optional_fields.*, optional_fields.*, optional_fields_translation.*');

        $this->db->join('products_optional_fields', 'user_products_optional_fields.product_optional_field_id = products_optional_fields.id');
        $this->db->join('optional_fields', 'products_optional_fields.optional_field_id = optional_fields.id');
        $this->db->join('optional_fields_translation', 'optional_fields.id = optional_fields_translation.optional_field_id AND optional_fields_translation.lang_id ='.$lang_id);

        $this->db->where('user_products_optional_fields.cart_product_id', $cart_product_id);

        $result = $this->db->get('user_products_optional_fields');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_user_order_product_optional_fields_data($order_product_id, $lang_id)
    {
        $this->db->select('user_products_optional_fields.*, optional_fields.*, optional_fields_translation.*');

        $this->db->join('products_optional_fields', 'user_products_optional_fields.product_optional_field_id = products_optional_fields.id');
        $this->db->join('optional_fields', 'products_optional_fields.optional_field_id = optional_fields.id');
        $this->db->join('optional_fields_translation', 'optional_fields.id = optional_fields_translation.optional_field_id AND optional_fields_translation.lang_id ='.$lang_id);

        $this->db->where('user_products_optional_fields.order_products_id', $order_product_id);

        $result = $this->db->get('user_products_optional_fields');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function check_user_product_optional_fields($cart_product_id, $user_id)
    {
        $this->db->where('cart_product_id', $cart_product_id);
        $this->db->where('user_id', $user_id);

        $count = $this->db->count_all_results('user_products_optional_fields');

        if($count > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

     public function update_row_sort($id, $old_index, $new_index, $sort_state, $table)
    {
        $this->db->where('id', $id);
        $row = $this->db->get($table);

        if($row)
        {
            $row      = $row->row();
            $row_sort = $row->sort;

            // if the row moved down && sort state = ascending
            if($old_index < $new_index && $sort_state == 'asc' )
            {
                $moved_rows = $new_index - $old_index;
                $new_sort = $row_sort + $moved_rows ;

                //update other rows sort value
                $this->db->where('sort >',$row_sort);
                $this->db->where('sort <=',$new_sort);
                $other_rows = $this->db->get($table);

                if($other_rows)
                {
                    $other_rows = $other_rows->result();

                    foreach($other_rows as $other_row)
                    {
                        $data_array = array('sort' => ($other_row->sort - 1));

                        $this->db->where('id',$other_row->id);
                        $this->db->update($table,$data_array);
                    }
                }

            }
            //if the row moved up && sort state = ascending
            if(($old_index > $new_index && $sort_state=='asc'))
            {
                $moved_rows = $old_index - $new_index;
                $new_sort   = $row_sort - $moved_rows ;

                //update other rows sort value
                $this->db->where('sort <'  , $row_sort);
                $this->db->where('sort >=' , $new_sort);
                $other_rows = $this->db->get($table);

                if($other_rows)
                {
                    $other_rows = $other_rows->result();

                    foreach($other_rows as $other_row)
                    {
                        $data_array = array('sort' => ($other_row->sort + 1));

                        $this->db->where('id',$other_row->id);
                        $this->db->update($table,$data_array);
                    }
                }
            }

            //if the row moved up && sort state = descending
            if(($old_index > $new_index && $sort_state == 'desc' ))
            {
                $moved_rows = $old_index - $new_index ;
                $new_sort = $row_sort + $moved_rows ;

                //update other rows sort value
                $this->db->where('sort >',$row_sort);
                $this->db->where('sort <=',$new_sort);
                $other_rows = $this->db->get($table);

                if($other_rows)
                {
                    $other_rows = $other_rows->result();

                    foreach($other_rows as $other_row)
                    {
                        $data_array = array('sort' => ($other_row->sort - 1));

                        $this->db->where('id',$other_row->id);
                        $this->db->update($table,$data_array);
                    }
                }
            }

            //if the row moved up && sort state = descending
            if($old_index < $new_index && $sort_state=='desc')
            {
                $moved_rows = $new_index - $old_index;
                $new_sort   = $row_sort - $moved_rows ;

                //update other rows sort value
                $this->db->where('sort <' , $row_sort);
                $this->db->where('sort >=', $new_sort);
                $other_rows = $this->db->get($table);

                if($other_rows)
                {
                    $other_rows = $other_rows->result();

                    foreach($other_rows as $other_row)
                    {
                        $data_array = array('sort' => ($other_row->sort + 1));

                        $this->db->where('id', $other_row->id);
                        $this->db->update($table, $data_array);
                    }
                }
            }

            // update row sort value
            $row_new_sort = array('sort' => $new_sort);

            $this->db->where('id', $id);
            $this->db->update($table, $row_new_sort);

        }
   }

   public function get_products_countries_totals()
    {
        $this->db->select_sum('average_cost');
        $this->db->select_sum('product_quantity');
        $this->db->select('SUM(average_cost * product_quantity) as Totals');

        $this->db->join('products', 'products_countries.product_id = products.id');
        //SUM(CASE WHEN products.serials_per_country = 1 THEN (average_cost * product_quantity) ELSE  0) AS Totals
        $query = $this->db->get('products_countries');

        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }

    public function get_prduct_data_by_route($route)
    {
        $this->db->where('route', $route);
        $query = $this->db->get('products');

        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }

    public function get_product_images($product_id)
    {
        $this->db->select('gallery_images.*');
        $this->db->join('gallery_images', 'products_images.image_id = gallery_images.id');

        $this->db->where('products_images.product_id', $product_id);
        $this->db->order_by('gallery_images.id', 'desc');

        $result = $this->db->get('products_images');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_product_image_data($product_id, $image_id)
    {
        $this->db->select('gallery_images.*');
        $this->db->join('gallery_images', 'products_images.image_id = gallery_images.id');

        $this->db->where('products_images.product_id', $product_id);
        $this->db->where('products_images.image_id', $image_id);

        $query = $this->db->get('products_images');

        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }

    public function delete_product_image($product_id, $image_id)
    {
        $this->db->where('id', $image_id);
        $this->db->delete('gallery_images');

        $this->db->where('product_id', $product_id);
        $this->db->where('image_id', $image_id);
        $this->db->delete('products_images');
    }
    /****************************************************************/
    #Products Comments

    public function get_count_all_product_comments($product_id, $stores_ids=array(), $search_word='')
    {
        if(count($stores_ids) != 0)
        {
            $this->db->join('products', 'products_comments.id = products.id');
            $this->db->where_in('products.store_id', $stores_ids);
        }

        if($search_word != '')
        {
             $this->db->where('(products_comments.username LIKE "%'.$search_word.'%")');
        }

        $this->db->where('product_id', $product_id);
        return $this->db->count_all_results('products_comments');
    }

    public function get_product_comments_data($lang_id, $limit, $offset, $search_word, $order_state, $product_id, $stores_ids= array())
    {
        $this->db->select('products_comments.*, products_translation.title as product_name');
        $this->db->join('products_translation', 'products_comments.product_id = products_translation.product_id AND products_translation.lang_id='.$lang_id);

        if(count($stores_ids) != 0)
        {
            $this->db->join('products', 'products_comments.product_id = products.id');
            $this->db->where_in('products.store_id', $stores_ids);
        }

        if($search_word != '')
        {
             $this->db->where('(products_comments.username LIKE "%'.$search_word.'%")');
        }

        $this->db->where('products_comments.product_id', $product_id);
        $this->db->order_by('products_comments.id', $order_state);

        $result = $this->db->get('products_comments');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_product_comment_row_data($id, $lang_id)
    {
        $this->db->select('products_comments.*, products_translation.title as product_name, products.store_id');

        $this->db->join('products_translation', 'products_comments.product_id = products_translation.product_id AND products_translation.lang_id='.$lang_id);
        $this->db->join('products', 'products_comments.product_id = products.id');

        $this->db->where('products_comments.id', $id);
        $result = $this->db->get('products_comments');

        if($result)
        {
            return $result->row();
        }
        else
        {
            return false;
        }
    }

    public function delete_product_comment_data($ids_array)
    {
        $this->db->where_in('id', $ids_array);
        return $this->db->delete('products_comments');
    }

    public function update_product_comments($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('products_comments', $data);
    }

    public function get_product_daily_discount($product_id, $country_id, $current_hour)
    {
        $this->db->select('products_discounts.*, products_discounts_countries.*');
        $this->db->join('products_discounts_countries', 'products_discounts.id = products_discounts_countries.product_discount_id');

        $this->db->order_by('discount_end_time desc');

        $this->db->where('products_discounts_countries.active', 1);
        $this->db->where('products_discounts_countries.country_id', $country_id);
        $this->db->where('products_discounts.product_id', $product_id);

        $this->db->where('products_discounts_countries.dailey', 1);
        $this->db->where("products_discounts_countries.discount_start_time <= $current_hour");
        $this->db->where("products_discounts_countries.discount_end_time >= $current_hour");

        $row = $this->db->get('products_discounts');

        if($row)
        {
            return $row->row();
        }
        else
        {
            return false;
        }
    }

    public function get_product_status($conditions)
    {
        foreach($conditions as $key=>$val)
        {
            $this->db->where($key, $val);
        }

        $query = $this->db->get('products_status_translation');

        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }

    public function check_product_in_fav($product_id, $user_id)
    {
        $this->db->where('product_id', $product_id);
        $this->db->where('user_id', $user_id);

        $count = $this->db->count_all_results('users_favourite_products');

        if($count > 0)
        {
            return 1;
        }
        else
        {
            return 0;
        }
    }

    public function check_product_is_spammed($product_id, $user_id)
    {
        $this->db->where('user_id', $user_id);
        $this->db->where('product_id', $product_id);

        $count = $this->db->count_all_results('users_spams');

        if($count > 0)
        {
            return true;
        }
        else
        {
            return false;
        }

    }

   public function get_user_spammed_users($user_id)
   {
    $this->db->select('products.owner_id as blocked_user_id');
    $this->db->join('products', 'users_spams.product_id = products.id');

    $this->db->where('users_spams.user_id', $user_id);
    $this->db->where('users_spams.block_user', 1);

    $result = $this->db->get('users_spams');

    if($result)
    {
        return $result->result();
    }
    else
    {
        return false;
    }
   }

   public function get_product_blocked_count($product_id)
   {
        $this->db->where('product_id', $product_id);
        return $this->db->count_all_results('users_spams');
   }

   public function insert_products_optional_fields_costs($cost_data)
    {
        return $this->db->insert('products_optional_fields_options_costs', $cost_data);
    }

    public function get_product_optional_field_cost($product_id, $field_id, $lang_id, $primary_id=0)
    {
        $this->db->select('products_optional_fields_options_costs.*, optional_fields_options_translation.*');
        $this->db->join('optional_fields_options_translation', 'products_optional_fields_options_costs.option_id = optional_fields_options_translation.optional_field_option_id AND optional_fields_options_translation.lang_id='.$lang_id, 'left');

        $this->db->where('products_optional_fields_options_costs.product_id', $product_id);
        $this->db->where('products_optional_fields_options_costs.optional_field_id', $field_id);

        if($primary_id != 0)
        {
            $this->db->where('products_optional_fields_options_costs.primary_option_id', $primary_id);
        }

        $this->db->order_by('products_optional_fields_options_costs.option_id', 'asc');

        $result = $this->db->get('products_optional_fields_options_costs');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_product_optional_field_cost_sec($product_id, $field_id, $lang_id, $primary_id=0)
    {
        $this->db->select('products_optional_fields_options_costs.*, optional_fields_options_translation.*');

        $this->db->join('optional_fields_options', 'products_optional_fields_options_costs.optional_field_id = optional_fields_options.optional_field_id');
        $this->db->join('optional_fields_options_translation', 'optional_fields_options.id = optional_fields_options_translation.optional_field_option_id
                         AND optional_fields_options_translation.lang_id='.$lang_id, 'left');

        $this->db->where('products_optional_fields_options_costs.product_id', $product_id);
        $this->db->where('products_optional_fields_options_costs.optional_field_id', $field_id);

        if($primary_id != 0)
        {
            $this->db->where('products_optional_fields_options_costs.primary_option_id', $primary_id);
        }

        $this->db->order_by('products_optional_fields_options_costs.option_id', 'asc');
        $this->db->group_by('products_optional_fields_options_costs.id');

        $result = $this->db->get('products_optional_fields_options_costs');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_product_option_cost_row($option_cost_id)
    {
        $this->db->where('id', $option_cost_id);
        $row = $this->db->get('products_optional_fields_options_costs');

        if($row)
        {
            return $row->row();
        }
        else
        {
            return false;
        }
    }

    public function update_product_option_cost_row($option_id, $updated_data)
    {
        $this->db->where('id', $option_id);

        return $this->db->update('products_optional_fields_options_costs', $updated_data);
    }
/****************************************************************/
}
