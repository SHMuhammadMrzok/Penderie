<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Site_products_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }


   /********************GET*****************************/
    public function get_cat_products($cat_id,$lang_id,$country_id, $store_id = 0, $sort=0, $price_filter=0, $rating_filter=0, $brands_filter=array(), $price_from=0, $price_to=0, $condtion_filter=0)
    {
        $this->db->select('products_countries.price ,products_translation.*,products.*, currencies_translation.name as currency, stores_translation.name as store_name');

        $this->db->join('products_translation','products.id = products_translation.product_id');
        $this->db->join('products_countries', 'products.id = products_countries.product_id');
        $this->db->join('countries', 'products_countries.country_id = countries.id');
        $this->db->join('currencies_translation', 'countries.currency_id = currencies_translation.currency_id AND currencies_translation.lang_id ='. $lang_id);
        $this->db->join('stores', 'products.store_id = stores.id AND stores.active=1');
        $this->db->join('stores_translation', 'products.store_id = stores_translation.store_id AND stores_translation.lang_id ='. $lang_id, 'left');

        $this->db->where('products.cat_id',$cat_id);
        $this->db->where('products_countries.active',1);
        $this->db->where('products_countries.country_id',$country_id);
        $this->db->where('products_translation.lang_id',$lang_id);

        if($store_id != 0)
        {
            $this->db->where('products.store_id', $store_id);
        }

        if($condtion_filter != 0)
        {
          if($condtion_filter == 1)//used products only
          {
            $this->db->where('products.is_used', 1);
          }
          else if($condtion_filter == 2)//new products only
          {
            $this->db->where('products.is_used', 0);
          }
        }

        if($sort == 1)
        {
            $this->db->order_by('products_translation.title', 'asc');
        }
        elseif($sort == 2)
        {
            $this->db->order_by('products_translation.title', 'desc');
        }
        elseif($sort == 5)
        {
            $this->db->order_by('products.rating_avg', 'desc');
        }
        elseif($sort == 6)
        {
            $this->db->order_by('products.rating_avg', 'asc');
        }
        else
        {
            $this->db->order_by('products.id', 'desc');
        }

        if($price_filter != 0)
        {
            if($price_filter == 1)
            {
                $this->db->where('products_countries.price < 100');
            }
            else if($price_filter == 2)
            {
                $this->db->where('products_countries.price BETWEEN 100 AND 200');
            }
            else if($price_filter == 3)
            {
                $this->db->where('products_countries.price BETWEEN 200 AND 300');
            }
            else if($price_filter == 4)
            {
                $this->db->where('products_countries.price > 300');
            }
        }

        if($price_from!=0)
        {
          $this->db->where('products_countries.price >=', $price_from);
        }

        if($price_to!=0)
        {
          $this->db->where('products_countries.price <=', $price_to);
        }

        if($rating_filter != 0)
        {
            if($rating_filter == 1)
            {
                $this->db->where('products.rating_avg > 0');
                $this->db->where('products.rating_avg <= 1');
            }
            else if($rating_filter == 2)
            {
                $this->db->where('products.rating_avg > 1');
                $this->db->where('products.rating_avg <= 2');
            }
            else if($rating_filter == 3)
            {
                $this->db->where('products.rating_avg > 2');
                $this->db->where('products.rating_avg <= 3');
            }
            else if($rating_filter == 4)
            {
                $this->db->where('products.rating_avg > 4');
                $this->db->where('products.rating_avg <= 4');
            }
            else if($rating_filter == 5)
            {
                $this->db->where('products.rating_avg > 4');
                $this->db->where('products.rating_avg <= 5');
            }
        }

        if(count($brands_filter) != 0)
        {
          $this->db->where_in('products.brand_id', $brands_filter);
        }
        $query = $this->db->get('products');

        if($query)
        {
            return $query->result();
        }else{
            return false ;
        }
    }

    public function get_all_offers_products($lang_id,$country_id, $store_id = 0, $sort=0, $rating_filter=0, $limit=15, $offset, $parent_cat_id=0, $brands_ids= array(), $price_from=0, $price_to=0, $conds=array())
    {
        $this->db->select('products_countries.price ,products_translation.*,products.*,
        currencies_translation.name as currency, stores_translation.name as store_name');

        $this->db->join('products_discounts', 'products.id = products_discounts.product_id');
        $this->db->join('products_translation','products.id = products_translation.product_id');
        $this->db->join('products_countries', 'products.id = products_countries.product_id');
        $this->db->join('countries', 'products_countries.country_id = countries.id');
        $this->db->join('currencies_translation', 'countries.currency_id = currencies_translation.currency_id AND currencies_translation.lang_id ='. $lang_id);
        $this->db->join('stores', 'products.store_id = stores.id AND stores.active=1');
        $this->db->join('stores_translation', 'products.store_id = stores_translation.store_id AND stores_translation.lang_id ='. $lang_id);

        if($parent_cat_id != 0)
        {
          $this->db->join('categories', 'products.cat_id = categories.id
                          AND categories.parent_id='.$parent_cat_id);
        }

        $this->db->where('products_countries.active',1);
        $this->db->where('products_countries.country_id',$country_id);
        $this->db->where('products_translation.lang_id',$lang_id);

        if($store_id != 0)
        {
            $this->db->where('products.store_id', $store_id);
        }

        if($price_from!=0)
        {
          $this->db->where('products_countries.price >=', $price_from);
        }

        if($price_to!=0)
        {
          $this->db->where('products_countries.price <=', $price_to);
        }

        if(count($brands_ids) != 0)
        {
            $this->db->where_in('products.brand_id', $brands_ids);
        }

        if($sort == 1)
        {
            $this->db->order_by('products_translation.title', 'asc');
        }
        elseif($sort == 2)
        {
            $this->db->order_by('products_translation.title', 'desc');
        }
        elseif($sort == 5)
        {
            $this->db->order_by('products.rating_avg', 'desc');
        }
        elseif($sort == 6)
        {
            $this->db->order_by('products.rating_avg', 'asc');
        }
        else
        {
            $this->db->order_by('products.id', 'desc');
        }

        if($rating_filter != 0)
        {
            if($rating_filter == 1)
            {
                $this->db->where('products.rating_avg > 0');
                $this->db->where('products.rating_avg <= 1');
            }
            else if($rating_filter == 2)
            {
                $this->db->where('products.rating_avg > 1');
                $this->db->where('products.rating_avg <= 2');
            }
            else if($rating_filter == 3)
            {
                $this->db->where('products.rating_avg > 2');
                $this->db->where('products.rating_avg <= 3');
            }
            else if($rating_filter == 4)
            {
                $this->db->where('products.rating_avg > 4');
                $this->db->where('products.rating_avg <= 4');
            }
            else if($rating_filter == 5)
            {
                $this->db->where('products.rating_avg > 4');
                $this->db->where('products.rating_avg <= 5');
            }
        }

        if(count($conds) != 0)
        {
          foreach($conds as $key=>$val)
          {
            $this->db->where($key, $val);
          }
        }

        $query = $this->db->get('products', $limit, $offset);

        if($query)
        {
            return $query->result();
        }else{
            return false ;
        }
    }

    public function count_all_offers_products($lang_id,$country_id, $store_id = 0, $rating_filter=0, $parent_cat_id=0, $brands_ids=array(), $price_from=0, $price_to=0, $conds=array())
    {
        $this->db->join('products_discounts', 'products.id = products_discounts.product_id');
        $this->db->join('products_translation','products.id = products_translation.product_id');
        $this->db->join('products_countries', 'products.id = products_countries.product_id');
        $this->db->join('countries', 'products_countries.country_id = countries.id');
        $this->db->join('currencies_translation', 'countries.currency_id = currencies_translation.currency_id AND currencies_translation.lang_id ='. $lang_id);
        $this->db->join('stores', 'products.store_id = stores.id AND stores.active=1');
        $this->db->join('stores_translation', 'products.store_id = stores_translation.store_id AND stores_translation.lang_id ='. $lang_id);

        if($parent_cat_id != 0)
        {
          $this->db->join('categories', 'products.cat_id = categories.id
                          AND categories.parent_id='.$parent_cat_id);
        }

        $this->db->where('products_countries.active',1);
        $this->db->where('products_countries.country_id',$country_id);
        $this->db->where('products_translation.lang_id',$lang_id);

        if($store_id != 0)
        {
            $this->db->where('products.store_id', $store_id);
        }

        if(count($brands_ids) != 0)
        {
            $this->db->where_in('products.brand_id', $brands_ids);
        }

        if($price_from!=0)
        {
          $this->db->where('products_countries.price >=', $price_from);
        }

        if($price_to!=0)
        {
          $this->db->where('products_countries.price <=', $price_to);
        }

        if($rating_filter != 0)
        {
            if($rating_filter == 1)
            {
                $this->db->where('products.rating_avg > 0');
                $this->db->where('products.rating_avg <= 1');
            }
            else if($rating_filter == 2)
            {
                $this->db->where('products.rating_avg > 1');
                $this->db->where('products.rating_avg <= 2');
            }
            else if($rating_filter == 3)
            {
                $this->db->where('products.rating_avg > 2');
                $this->db->where('products.rating_avg <= 3');
            }
            else if($rating_filter == 4)
            {
                $this->db->where('products.rating_avg > 4');
                $this->db->where('products.rating_avg <= 4');
            }
            else if($rating_filter == 5)
            {
                $this->db->where('products.rating_avg > 4');
                $this->db->where('products.rating_avg <= 5');
            }
        }

        if(count($conds) != 0)
        {
          foreach($conds as $key=>$val)
          {
            $this->db->where($key, $val);
          }
        }

        return $this->db->count_all_results('products');

    }

    public function get_brand_products($brand_id,$lang_id,$country_id)
    {
        $this->db->select('products_countries.price ,products_translation.*,products.*, currencies_translation.name as currency');

        $this->db->join('products_translation','products.id = products_translation.product_id');
        $this->db->join('products_countries', 'products.id = products_countries.product_id');
        $this->db->join('countries', 'products_countries.country_id = countries.id');
        $this->db->join('currencies_translation', 'countries.currency_id = currencies_translation.currency_id AND currencies_translation.lang_id ='. $lang_id);


        $this->db->where('products.brand_id',$brand_id);
        $this->db->where('products_countries.active',1);
        $this->db->where('products_countries.country_id',$country_id);
        $this->db->where('products_translation.lang_id',$lang_id);

        $query = $this->db->get('products');

        if($query)
        {
            return $query->result();
        }else{
            return false ;
        }
    }

    public function get_store_products($store_id, $lang_id, $country_id, $sort=0, $limit=15, $offset=0, $brands_filter=array(), $rating_filter=0, $price_from=0, $price_to=0, $conds= array())
    {
        $this->db->select('products_countries.price ,products_translation.*,products.*,
        currencies_translation.name as currency, stores_translation.name as store_name,
        categories.route as cat_route, categories_translation.name as cat_name');

        $this->db->join('products_translation','products.id = products_translation.product_id');
        $this->db->join('products_countries', 'products.id = products_countries.product_id');
        $this->db->join('countries', 'products_countries.country_id = countries.id');
        $this->db->join('categories', 'products.cat_id = categories.id');
        $this->db->join('stores', 'products.store_id = stores.id AND stores.active=1');
        $this->db->join('categories_translation', 'products.cat_id = categories_translation.category_id AND categories_translation.lang_id ='. $lang_id);
        $this->db->join('currencies_translation', 'countries.currency_id = currencies_translation.currency_id AND currencies_translation.lang_id ='. $lang_id);
        $this->db->join('stores_translation', 'products.store_id = stores_translation.store_id AND stores_translation.lang_id ='. $lang_id, 'left');

        if($store_id != 0)
        {
            $this->db->where('products.store_id', $store_id);
        }
        $this->db->where('products_countries.active', 1);
        $this->db->where('products_countries.country_id', $country_id);
        $this->db->where('products_translation.lang_id', $lang_id);
        $this->db->where('products.status_id !=', 1); // verified products only

        if($sort == 1)
        {
            $this->db->order_by('products_translation.title', 'asc');
        }
        elseif($sort == 2)
        {
            $this->db->order_by('products_translation.title', 'desc');
        }
        elseif($sort == 5)
        {
            $this->db->order_by('products.rating_avg', 'desc');
        }
        elseif($sort == 6)
        {
            $this->db->order_by('products.rating_avg', 'asc');
        }
        else
        {
            $this->db->order_by('products.sort', 'desc');
        }

        if($price_from!=0)
        {
          $this->db->where('products_countries.price >=', $price_from);
        }

        if($price_to!=0)
        {
          $this->db->where('products_countries.price <=', $price_to);
        }


        if(count($brands_filter) != 0)
        {
          $this->db->where_in('products.brand_id', $brands_filter);
        }

        if($rating_filter != 0)
        {
            if($rating_filter == 1)
            {
                $this->db->where('products.rating_avg > 0');
                $this->db->where('products.rating_avg <= 1');
            }
            else if($rating_filter == 2)
            {
                $this->db->where('products.rating_avg > 1');
                $this->db->where('products.rating_avg <= 2');
            }
            else if($rating_filter == 3)
            {
                $this->db->where('products.rating_avg > 2');
                $this->db->where('products.rating_avg <= 3');
            }
            else if($rating_filter == 4)
            {
                $this->db->where('products.rating_avg > 4');
                $this->db->where('products.rating_avg <= 4');
            }
            else if($rating_filter == 5)
            {
                $this->db->where('products.rating_avg > 4');
                $this->db->where('products.rating_avg <= 5');
            }
        }

        if(count($conds) != 0)
        {
          foreach($conds as $key=>$val)
          {
            $this->db->where($key, $val);
          }
        }

        if($limit != 0)
        {
            $query = $this->db->get('products', $limit, $offset);
        }
        else
        {
            $query = $this->db->get('products');
        }


        if($query)
        {
            return $query->result();
        }else{
            return false ;
        }
    }

    public function get_store_products_count($store_id, $lang_id, $country_id, $brands_filter=array(), $rating_filter=0, $price_from=0, $price_to=0)
    {
        $this->db->join('products_translation','products.id = products_translation.product_id');
        $this->db->join('products_countries', 'products.id = products_countries.product_id');
        $this->db->join('countries', 'products_countries.country_id = countries.id');
        $this->db->join('categories', 'products.cat_id = categories.id');
        $this->db->join('stores', 'products.store_id = stores.id AND stores.active=1');
        $this->db->join('categories_translation', 'products.cat_id = categories_translation.category_id AND categories_translation.lang_id ='. $lang_id);
        $this->db->join('currencies_translation', 'countries.currency_id = currencies_translation.currency_id AND currencies_translation.lang_id ='. $lang_id);
        $this->db->join('stores_translation', 'products.store_id = stores_translation.store_id AND stores_translation.lang_id ='. $lang_id);

        if($store_id != 0)
        {
            $this->db->where('products.store_id', $store_id);
        }
        $this->db->where('products_countries.active', 1);
        $this->db->where('products_countries.country_id', $country_id);
        $this->db->where('products_translation.lang_id', $lang_id);
        $this->db->where('products.status_id !=', 1); // verified products only

        if($price_from!=0)
        {
          $this->db->where('products_countries.price >=', $price_from);
        }

        if($price_to!=0)
        {
          $this->db->where('products_countries.price <=', $price_to);
        }


        if(count($brands_filter) != 0)
        {
          $this->db->where_in('products.brand_id', $brands_filter);
        }

        if($rating_filter != 0)
        {
            if($rating_filter == 1)
            {
                $this->db->where('products.rating_avg > 0');
                $this->db->where('products.rating_avg <= 1');
            }
            else if($rating_filter == 2)
            {
                $this->db->where('products.rating_avg > 1');
                $this->db->where('products.rating_avg <= 2');
            }
            else if($rating_filter == 3)
            {
                $this->db->where('products.rating_avg > 2');
                $this->db->where('products.rating_avg <= 3');
            }
            else if($rating_filter == 4)
            {
                $this->db->where('products.rating_avg > 4');
                $this->db->where('products.rating_avg <= 4');
            }
            else if($rating_filter == 5)
            {
                $this->db->where('products.rating_avg > 4');
                $this->db->where('products.rating_avg <= 5');
            }
        }

        return $this->db->count_all_results('products');
    }

    public function get_most_searched_store_products($lang_id, $country_id, $store_id=0, $limit, $user_id=0, $blocked_users_ids=array())
    {
        $this->db->select('products_countries.price ,products_translation.*,products.*, stores_translation.name as store_name,
                            products.id as id, currencies_translation.name as currency');

        $this->db->join('products_translation','products.id = products_translation.product_id');
        $this->db->join('products_countries', 'products.id = products_countries.product_id');
        $this->db->join('countries', 'products_countries.country_id = countries.id');
        $this->db->join('currencies_translation', 'countries.currency_id = currencies_translation.currency_id AND currencies_translation.lang_id ='. $lang_id);
        $this->db->join('stores_translation', 'products.store_id = stores_translation.store_id AND stores_translation.lang_id ='. $lang_id);

        if($user_id != 0)
        {
            $this->db->join('users_spams', 'products.id = users_spams.product_id AND users_spams.user_id='.$user_id, 'left');
            $this->db->where('users_spams.product_id IS NULL', null, 'false');
        }

        if(count($blocked_users_ids) != 0)
        {
            $this->db->where_not_in('products.owner_id', $blocked_users_ids);
        }

        if($store_id != 0)
        {
            $this->db->where('products.store_id', $store_id);
        }

        $this->db->where('products_countries.active', 1);
        $this->db->where('products_countries.country_id', $country_id);
        $this->db->where('products_translation.lang_id', $lang_id);
        $this->db->where('products.searched != 0');

        $this->db->order_by('products.searched', 'desc');

        $query = $this->db->get('products', $limit);

        if($query)
        {
            return $query->result();
        }else{
            return false ;
        }
    }

    public function get_cats_products($sub_cats_ids, $lang_id, $country_id, $limit=0, $offset=0, $store_id=0, $sort=0, $price_filter=0, $rating_filter=0, $conds=array(), $user_id=0, $spammed_users_ids=array(), $op_filter=array(), $products_api=0, $cart_id=0)
    {
       $select = 'products_countries.price, products_countries.vat_id, products_translation.*, products.*, currencies_translation.name as currency,
                            stores_translation.name as store_name, products.id as id, brands_translation.name as brand_name ';

       if($products_api == 1)
       {
         $select .= ' ,shopping_cart_products.cart_id, users_favourite_products.id as fav_id, users_compare_products.id as compare_id';
       }

       $this->db->select("$select");

        $this->db->join('products_translation', 'products.id = products_translation.product_id');
        $this->db->join('products_countries', 'products.id = products_countries.product_id');
        //$this->db->join('countries_translation', 'products_countries.country_id = countries_translation.country_id');
        $this->db->join('countries', 'products_countries.country_id = countries.id');
        $this->db->join('currencies_translation', 'countries.currency_id = currencies_translation.currency_id AND currencies_translation.lang_id ='. $lang_id);
        $this->db->join('stores_translation', 'products.store_id = stores_translation.store_id AND stores_translation.lang_id ='. $lang_id, 'left');
        $this->db->join('stores', 'products.store_id = stores.id AND stores.active=1', 'left');
        $this->db->join('brands_translation', 'products.brand_id=brands_translation.brand_id
                        AND brands_translation.lang_id='.$lang_id, 'left');

        if($products_api == 1)
        {
          $this->db->join('users_compare_products', 'products.id=users_compare_products.product_id
                          AND users_compare_products.user_id = '.$user_id, 'left');
          $this->db->join('users_favourite_products', 'products.id=users_favourite_products.product_id
                          AND users_favourite_products.user_id = '.$user_id, 'left');
          $this->db->join('shopping_cart_products', 'products.id = shopping_cart_products.product_id
                          AND shopping_cart_products.cart_id = '.$cart_id, 'left');
        }

        if($user_id != 0)
        {
            $this->db->join('users_spams', 'products.id = users_spams.product_id AND users_spams.user_id='.$user_id, 'left');
            $this->db->where('users_spams.product_id IS NULL', null, 'false');
        }

        if(count($op_filter) !=0)
        {
          $this->db->join('products_optional_fields_options_costs', 'products.id = products_optional_fields_options_costs.product_id
                          AND products_optional_fields_options_costs.active=1');
          $this->db->where_in('products_optional_fields_options_costs.option_id ', $op_filter);
          $this->db->group_by('products.id');
        }

        if(count($sub_cats_ids) != 0)
        {
            $this->db->where_in('products.cat_id', $sub_cats_ids);
        }

        if($store_id != 0)
        {
            $this->db->where('products.store_id', $store_id);
        }

        if($price_filter != 0)
        {
            if($price_filter == 1)
            {
                $this->db->where('products_countries.price < 500');
            }
            else if($price_filter == 2)
            {
                $this->db->where('products_countries.price BETWEEN 500 AND 1000');
            }
            else if($price_filter == 3)
            {
                $this->db->where('products_countries.price BETWEEN 1000 AND 3000');
            }
            else if($price_filter == 4)
            {
                $this->db->where('products_countries.price > 3000');
            }
        }

        if(count($spammed_users_ids) != 0)
        {
            $this->db->where_not_in('products.owner_id', $spammed_users_ids);
        }

        $this->db->where('products_countries.active', 1);
        $this->db->where('products_countries.country_id', $country_id);
        $this->db->where('products_translation.lang_id', $lang_id);

        if($sort == 1)
        {
            $this->db->order_by('products_translation.title', 'asc');
        }
        elseif($sort == 2)
        {
            $this->db->order_by('products_translation.title', 'desc');
        }
        elseif($sort == 5)
        {
            $this->db->order_by('products.rating_avg', 'desc');
        }
        elseif($sort == 6)
        {
            $this->db->order_by('products.rating_avg', 'asc');
        }
        elseif($sort == 7)
        {
            $this->db->order_by('products.view', 'desc');
        }
        else
        {
            $this->db->order_by('products.sort', 'desc');
        }

        if($rating_filter != 0)
        {
            if($rating_filter == 1)
            {
                $this->db->where('products.rating_avg > 0');
                $this->db->where('products.rating_avg <= 1');
            }
            else if($rating_filter == 2)
            {
                $this->db->where('products.rating_avg > 1');
                $this->db->where('products.rating_avg <= 2');
            }
            else if($rating_filter == 3)
            {
                $this->db->where('products.rating_avg > 2');
                $this->db->where('products.rating_avg <= 3');
            }
            else if($rating_filter == 4)
            {
                $this->db->where('products.rating_avg > 4');
                $this->db->where('products.rating_avg <= 4');
            }
            else if($rating_filter == 5)
            {
                $this->db->where('products.rating_avg > 4');
                $this->db->where('products.rating_avg <= 5');
            }
        }

        if(count($conds) != 0)
        {
            foreach($conds as $key=>$val)
            {
                $this->db->where($key, $val);
            }
        }

        if($limit != 0)
        {
            $query = $this->db->get('products', $limit, $offset);
        }
        else
        {
            $query = $this->db->get('products');
        }

        if($query)
        {
            return $query->result();
        }
        else
        {
            return false ;
        }
    }

    public function get_cats_products_count($sub_cats_ids, $lang_id, $country_id, $store_id=0)
    {
        $this->db->select('products_countries.price, products_translation.*, products.*, currencies_translation.name as currency');

        $this->db->join('products_translation', 'products.id = products_translation.product_id');
        $this->db->join('products_countries', 'products.id = products_countries.product_id');
        $this->db->join('countries', 'products_countries.country_id = countries.id');
        $this->db->join('currencies_translation', 'countries.currency_id = currencies_translation.currency_id AND currencies_translation.lang_id ='. $lang_id);
        $this->db->join('stores', 'products.store_id = stores.id AND stores.active=1');

        if(count($sub_cats_ids) != 0)
        {
            $this->db->where_in('products.cat_id', $sub_cats_ids);
        }

        if($store_id != 0)
        {
            $this->db->where('products.store_id', $store_id);
        }

        $this->db->where('products_countries.active', 1);
        $this->db->where('products_countries.country_id', $country_id);
        $this->db->where('products_translation.lang_id', $lang_id);

        return $this->db->count_all_results('products');

    }

    public function get_user_wishlist_products($count, $lang_id, $country_id, $user_id, $limit=0, $offset=0)
    {
        $this->db->select('products_countries.price, products_translation.*, products.*, currencies_translation.name as currency, stores_translation.name as store_name, users_favourite_products.*, users_favourite_products.unixtime as unixtime');

        $this->db->join('products_translation', 'products.id = products_translation.product_id');
        $this->db->join('products_countries', 'products.id = products_countries.product_id');
        $this->db->join('countries', 'products_countries.country_id = countries.id');
        $this->db->join('currencies_translation', 'countries.currency_id = currencies_translation.currency_id AND currencies_translation.lang_id ='. $lang_id);
        $this->db->join('stores_translation', 'products.store_id = stores_translation.store_id AND stores_translation.lang_id ='. $lang_id);
        $this->db->join('stores', 'products.store_id = stores.id AND stores.active=1');
        $this->db->join('users_favourite_products', 'products.id = users_favourite_products.product_id');

        $this->db->where('products_countries.active', 1);
        $this->db->where('products_countries.country_id', $country_id);
        $this->db->where('products_translation.lang_id', $lang_id);
        $this->db->where('users_favourite_products.user_id', $user_id);

        $this->db->order_by('users_favourite_products.unixtime', 'desc');

        if($count == 0)
        {
            $this->db->group_by('products.id');
            $query = $this->db->get('products', $limit, $offset);

            if($query)
            {
                return $query->result();
            }
            else
            {
                return false ;
            }
        }
        else
        {
            return $this->db->count_all_results('products');
        }


    }

     public function get_product($id, $lang_id, $country_id)
     {
            $this->db->select('products_countries.price , products_countries.reward_points, products_translation.*,products.*,
                               currencies_translation.name as currency , categories_translation.name as cat_name,
                               brands_translation.name as brand_name, stores_translation.name as store_name');

            $this->db->join('products_translation','products.id = products_translation.product_id');
            $this->db->join('products_countries', 'products.id = products_countries.product_id');
            $this->db->join('countries', 'products_countries.country_id = countries.id');
            $this->db->join('currencies_translation', 'countries.currency_id = currencies_translation.currency_id AND currencies_translation.lang_id ='. $lang_id);
            $this->db->join('brands_translation', 'products.brand_id = brands_translation.brand_id AND brands_translation.lang_id ='. $lang_id, 'left');
            $this->db->join('stores', 'products.store_id = stores.id AND stores.active=1');
            $this->db->join('stores_translation', 'products.store_id = stores_translation.store_id AND stores_translation.lang_id ='. $lang_id, 'left');

            $this->db->join('categories_translation','products.cat_id = categories_translation.category_id');

            $this->db->where('products.id',$id);
            $this->db->where('products_countries.active', 1);
            $this->db->where('products_countries.country_id',$country_id);
            $this->db->where('products_translation.lang_id',$lang_id);
            $this->db->where('categories_translation.lang_id', $lang_id);

            $query = $this->db->get('products');

            if($query)
            {
                return $query->row();
            }else{
                return false ;
            }
     }

     public function get_product_by_name($product_name, $lang_id, $country_id, $sort=0, $brands_filter=array(), $price_from=0, $price_to=0)
     {
        $query = false;
        if($product_name != '')
        {
            $this->db->select('products_countries.price, products_translation.*, products.*, currencies_translation.name as currency, stores_translation.name as store_name');

            $this->db->join('products_translation','products.id = products_translation.product_id');// AND products_translation.lang_id='.$lang_id);
            $this->db->join('products_countries', 'products.id = products_countries.product_id
                            AND products_countries.active = 1');
            $this->db->join('countries', 'products_countries.country_id = countries.id');
            $this->db->join('currencies_translation', 'countries.currency_id = currencies_translation.currency_id AND currencies_translation.lang_id ='. $lang_id);
            $this->db->join('stores', 'products.store_id = stores.id AND stores.active=1');
            $this->db->join('stores_translation', 'products.store_id = stores_translation.store_id AND stores_translation.lang_id ='. $lang_id);

            //$this->db->where('products_translation.lang_id',$lang_id);
            $this->db->group_by('products.id');
            $this->db->where('products_countries.country_id', $country_id);

            //$this->db->like('products_translation.title', $product_name , 'both');
            $this->db->where('(products_translation.title LIKE "%'.$product_name.'%" OR products.code LIKE "%'.$product_name.'%")');

            if(count($brands_filter) != 0)
            {
              $this->db->where_in('products.brand_id', $brands_filter);
            }

            if($price_from!=0)
            {
              $this->db->where('products_countries.price >=', $price_from);
            }

            if($price_to!=0)
            {
              $this->db->where('products_countries.price <=', $price_to);
            }

            if($sort == 1)
            {
                $this->db->order_by('products_translation.title', 'asc');
            }
            elseif($sort == 2)
            {
                $this->db->order_by('products_translation.title', 'desc');
            }
            elseif($sort == 5)
            {
                $this->db->order_by('products.rating_avg', 'desc');
            }
            elseif($sort == 6)
            {
                $this->db->order_by('products.rating_avg', 'asc');
            }
            else
            {
                $this->db->order_by('products.id', 'desc');
            }

            $query = $this->db->get('products', 25);
        }
        if($query)
        {
            return $query->result();
        }
        else
        {
            return false ;
        }
     }

     public function get_product_by_name_count($product_name, $lang_id, $country_id)
     {
        if($product_name != '')
        {
            $this->db->select('products_countries.price ,products_translation.*,products.*, currencies_translation.name as currency');

            $this->db->join('products_translation','products.id = products_translation.product_id');
            $this->db->join('products_countries', 'products.id = products_countries.product_id');
            $this->db->join('countries', 'products_countries.country_id = countries.id');
            $this->db->join('currencies_translation', 'countries.currency_id = currencies_translation.currency_id AND currencies_translation.lang_id ='. $lang_id);
            $this->db->join('stores', 'products.store_id = stores.id AND stores.active=1');

            $this->db->where('products_translation.lang_id',$lang_id);
            $this->db->where('products_countries.country_id', $country_id);

            
            //$this->db->like('products_translation.title', $product_name , 'both');
            $this->db->where('(products_translation.title LIKE "%'.$product_name.'%" OR products.code LIKE "%'.$product_name.'%")');
            

            return $this->db->count_all_results('products');
        }
     }

     public function get_product_tags($id)
     {
        $this->db->where ('product_id',$id);
        $query = $this->db->get('tags_products');

        if($query)
        {
            return $query->result();
        }else{
            return false ;
        }
     }

     public function get_related_products($id , $tags=array(), $country_id=0, $limit=0)
     {
        if($country_id == 0)
        {
            $country_id = $this->data['country_id'];
        }
        $this->db->select('tags_products.*, products.id as product_id');
        $this->db->join('products_countries', 'tags_products.product_id = products_countries.product_id');
        $this->db->join('products', 'tags_products.product_id = products.id');

        $this->db->where ('products_countries.country_id ', $country_id);
        $this->db->where ('products_countries.active ', 1);
        $this->db->where ('tags_products.product_id !=',$id);

        if(count($tags) != 0)
        {
           $this->db->where_in ('tags_products.tag_id',$tags);
        }

        $this->db->group_by('products.id');

        if($limit != 0)
        {
          $query = $this->db->get('tags_products', $limit);
        }
        else {
          $query = $this->db->get('tags_products');
        }

        if($query)
        {
            return $query->result();
        }else{
            return false ;
        }
     }

     public function get_product_view($id)
     {
         $this->db->where ('id',$id);
         $query = $this->db->get('products');

         if($query)
         {
             return $query->row();
         }else{
            return false ;
         }
     }

    //-->> product view function
     public function increment_product_view($id)
     {
         $view = $this->get_product_view($id);

         if(!empty($view)){

            $view = $view->view + 1;

             $data = array('view'=>$view);

             $this->db->where ('id',$id);


             $this->db->update('products',$data);
         }
     }

     public function get_category_sub_cats_ids($cat_id)
     {
        $this->db->select('id');
        $this->db->where('active', 1);
        $this->db->where('parent_id', $cat_id);

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

     public function get_category_sub_cats_translation($cat_id, $lang_id=2)
     {
        $this->db->select('categories_translation.name , categories.*');
        $this->db->join('categories_translation', 'categories.id = categories_translation.category_id AND categories_translation.lang_id = '.$lang_id);
        $this->db->where('categories.active', 1);
        if($cat_id != 0)
        {
          $this->db->where('categories.parent_id', $cat_id);
        }
        else {
          $this->db->where('categories.parent_id != 0');
        }
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

    /****************************************************************/
    ////Rating
    public function check_user_product_rate($product_id, $user_id)
    {
        $this->db->where('product_id', $product_id);
        $this->db->where('user_id', $user_id);

        return $this->db->count_all_results('products_rating');
    }

    public function insert_product_rate($rate_data)
    {
        return $this->db->insert('products_rating', $rate_data);
    }

    public function get_product_total_points($product_id)
    {
        $this->db->select('SUM(rate) as total_rate');
        $this->db->where('product_id', $product_id);

        $query = $this->db->get('products_rating')->row();

        if($query)
        {
            return $query->total_rate;
        }
        else
        {
            return false;
        }
    }

    public function update_product($product_id, $product_data)
    {
        $this->db->where('id', $product_id);

        return $this->db->update('products', $product_data);
    }

    public function update_searched_products($products_ids)
    {
        $this->db->set('searched', 'searched+1', FALSE);
        $this->db->where_in('id', $products_ids);

        return $this->db->update('products');

    }

    public function check_product_in_wishlist($product_id, $user_id)
    {
        $this->db->where('user_id', $user_id);
        $this->db->where('product_id', $product_id);

        $count = $this->db->count_all_results('users_favourite_products');

        if($count > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function insert_wishlist_product($data)
    {
        return $this->db->insert('users_favourite_products', $data);
    }

    public function remove_wishlist_product($product_id, $user_id)
    {
        $this->db->where('user_id', $user_id);
        $this->db->where('product_id', $product_id);

        return $this->db->delete('users_favourite_products');
    }

    /**************************************************************/
    #Reviews
    public function insert_product_comment($data)
    {
        return $this->db->insert('products_comments', $data);
    }

    public function get_product_comments($product_id)
    {
        $this->db->where('product_id', $product_id);
        $this->db->where('approved', 1);

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

    public function get_new_used_products($new, $lang_id, $country_id, $limit=0, $offset=0, $store_id=0, $sort=0, $price_filter=0, $rating_filter=0, $user_id=0, $blocked_users_ids=array())
    {
        $this->db->select('products_countries.price, products_translation.*, products.*, currencies_translation.name as currency, stores_translation.name as store_name');

        $this->db->join('products_translation', 'products.id = products_translation.product_id');
        $this->db->join('products_countries', 'products.id = products_countries.product_id');
        //$this->db->join('countries_translation', 'products_countries.country_id = countries_translation.country_id');
        $this->db->join('countries', 'products_countries.country_id = countries.id');
        $this->db->join('currencies_translation', 'countries.currency_id = currencies_translation.currency_id AND currencies_translation.lang_id ='. $lang_id);
        $this->db->join('stores_translation', 'products.store_id = stores_translation.store_id AND stores_translation.lang_id ='. $lang_id);

        if($user_id != 0)
        {
            $this->db->join('users_spams', 'products.id = users_spams.product_id AND users_spams.user_id='.$user_id, 'left');
            $this->db->where('users_spams.product_id IS NULL', null, 'false');
        }

        if(count($blocked_users_ids) != 0)
        {
            $this->db->where_not_in('products.owner_id', $blocked_users_ids);
        }

        $this->db->where('products.new', $new);
        $this->db->where('products.status_id !=', 1);


        if($store_id != 0)
        {
            $this->db->where('products.store_id', $store_id);
        }

        if($price_filter != 0)
        {
            if($price_filter == 1)
            {
                $this->db->where('products_countries.price < 100');
            }
            else if($price_filter == 2)
            {
                $this->db->where('products_countries.price BETWEEN 100 AND 200');
            }
            else if($price_filter == 3)
            {
                $this->db->where('products_countries.price BETWEEN 200 AND 300');
            }
            else if($price_filter == 4)
            {
                $this->db->where('products_countries.price > 300');
            }
        }

        $this->db->where('products_countries.active', 1);
        $this->db->where('products_countries.country_id', $country_id);
        $this->db->where('products_translation.lang_id', $lang_id);

        if($sort == 1)
        {
            $this->db->order_by('products_translation.title', 'asc');
        }
        elseif($sort == 2)
        {
            $this->db->order_by('products_translation.title', 'desc');
        }
        elseif($sort == 5)
        {
            $this->db->order_by('products.rating_avg', 'desc');
        }
        elseif($sort == 6)
        {
            $this->db->order_by('products.rating_avg', 'asc');
        }
        else
        {
            $this->db->order_by('products.id', 'desc');
        }

        if($rating_filter != 0)
        {
            if($rating_filter == 1)
            {
                $this->db->where('products.rating_avg > 0');
                $this->db->where('products.rating_avg <= 1');
            }
            else if($rating_filter == 2)
            {
                $this->db->where('products.rating_avg > 1');
                $this->db->where('products.rating_avg <= 2');
            }
            else if($rating_filter == 3)
            {
                $this->db->where('products.rating_avg > 2');
                $this->db->where('products.rating_avg <= 3');
            }
            else if($rating_filter == 4)
            {
                $this->db->where('products.rating_avg > 4');
                $this->db->where('products.rating_avg <= 4');
            }
            else if($rating_filter == 5)
            {
                $this->db->where('products.rating_avg > 4');
                $this->db->where('products.rating_avg <= 5');
            }
        }

        if($limit != 0)
        {
            $query = $this->db->get('products', $limit, $offset);
        }
        else
        {
            $query = $this->db->get('products');
        }

        if($query)
        {
            return $query->result();
        }
        else
        {
            return false ;
        }
    }

    public function check_product_in_compare_list($product_id, $user_id)
    {
        $this->db->where('user_id', $user_id);
        $this->db->where('product_id', $product_id);

        $count = $this->db->count_all_results('users_compare_products');

        if($count > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function insert_compare_product($data)
    {
        return $this->db->insert('users_compare_products', $data);
    }

    public function remove_compare_product($product_id, $user_id)
    {
        $this->db->where('user_id', $user_id);
        $this->db->where('product_id', $product_id);

        return $this->db->delete('users_compare_products');
    }

    public function get_user_compare_products($count, $lang_id, $country_id, $user_id, $limit=0, $offset=0)
    {
        $this->db->select('products_countries.price, products_translation.*, products.*, currencies_translation.name as currency,
            stores_translation.name as store_name, users_compare_products.*, users_compare_products.unixtime as unixtime');

        $this->db->join('products_translation', 'products.id = products_translation.product_id');
        $this->db->join('products_countries', 'products.id = products_countries.product_id');
        $this->db->join('countries', 'products_countries.country_id = countries.id');
        $this->db->join('currencies_translation', 'countries.currency_id = currencies_translation.currency_id AND currencies_translation.lang_id ='. $lang_id);
        $this->db->join('stores_translation', 'products.store_id = stores_translation.store_id AND stores_translation.lang_id ='. $lang_id);
        $this->db->join('users_compare_products', 'products.id = users_compare_products.product_id');

        $this->db->where('products_countries.active', 1);
        $this->db->where('products_countries.country_id', $country_id);
        $this->db->where('products_translation.lang_id', $lang_id);
        $this->db->where('users_compare_products.user_id', $user_id);

        $this->db->order_by('users_compare_products.unixtime', 'desc');

        if($count == 0)
        {
            $query = $this->db->get('products', $limit, $offset);

            if($query)
            {
                return $query->result();
            }
            else
            {
                return false ;
            }
        }
        else
        {
            return $this->db->count_all_results('products');
        }
    }

    public function get_category_brands($cat_id, $lang_id, $cats_ids_is_array=0, $products_ids='')
    {
      $query = "SELECT brands_translation.*, count(products.id) as products_count
      FROM products
      JOIN brands_translation ON products.brand_id = brands_translation.brand_id
      AND brands_translation.lang_id = $lang_id ";
      if($cats_ids_is_array == 1)
      {
          $query .= " WHERE products.cat_id IN ($cat_id)";
      }
      else if($cat_id!=0) {
        $query .= " WHERE products.cat_id = $cat_id";
      }

      if($products_ids != '')
      {
        $query .= "WHERE products.id IN ($products_ids)";
      }

      $query .= " GROUP BY brands_translation.brand_id";

      $query = $this->db->query($query);

      if($query)
      {
        return $query->result();
      }
      else
      {
        return false;
      }
    }

    public function get_store_brands($store_id, $lang_id)
    {
      $query = "SELECT brands_translation.*, count(products.id) as products_count
      FROM products
      JOIN brands_translation ON products.brand_id = brands_translation.brand_id
      AND brands_translation.lang_id = $lang_id
      WHERE products.store_id = $store_id
      GROUP BY brands_translation.brand_id";

      $query = $this->db->query($query);

      if($query)
      {
        return $query->result();
      }
      else
      {
        return false;
      }
    }

    public function get_offer_brands($lang_id, $country_id)
    {

      $this->db->select('brands_translation.*, count(products.id) as products_count');

      $this->db->join('products_discounts', 'products.id = products_discounts.product_id');
      $this->db->join('products_countries', 'products.id = products_countries.product_id');
      $this->db->join('countries', 'products_countries.country_id = countries.id');
      $this->db->join('brands_translation', 'products.brand_id = brands_translation.brand_id
                      AND brands_translation.lang_id ='. $lang_id);



      $this->db->where('products_countries.active',1);
      $this->db->where('products_countries.country_id',$country_id);

      $query = $this->db->get('products');

      if($query)
      {
          return $query->result();
      }else{
          return false ;
      }

    }

    public function check_user_bought_this_product($product_id, $user_id)
    {
      $this->db->join('orders_products', 'orders.id = orders_products.order_id
      AND orders_products.product_id = '.$product_id);
      $this->db->where('orders.user_id', $user_id);

      $count = $this->db->count_all_results('orders');
      if($count > 0)
      {
        return true;
      }
      else {
        return false;
      }
    }

    public function get_product_autocomplete($search_word, $lang_id, $country_id, $limit)
    {
      $this->db->select('products_translation.title, products_translation.product_id');
      $this->db->join('products_countries', 'products_translation.product_id = products_countries.product_id
                      AND products_countries.active=1
                      AND products_countries.country_id='.$country_id);
      $this->db->like('products_translation.title', $search_word , 'both');
      $this->db->where('lang_id', $lang_id);

      $query = $this->db->get('products_translation', $limit);

      if($query)
      {
        return $query->result();
      }
      else {
        return false;
      }
    }

    public function get_most_viewed_products($lang_id, $country_id, $store_id=0, $limit, $user_id=0, $blocked_users_ids=array())
    {
        $this->db->select('products_countries.price ,products_translation.*,products.*, stores_translation.name as store_name,
                            products.id as id, currencies_translation.name as currency, stores.active as stores_active');

        $this->db->join('products_translation','products.id = products_translation.product_id');
        $this->db->join('products_countries', 'products.id = products_countries.product_id');
        $this->db->join('countries', 'products_countries.country_id = countries.id');
        $this->db->join('currencies_translation', 'countries.currency_id = currencies_translation.currency_id AND currencies_translation.lang_id ='. $lang_id);
        $this->db->join('stores_translation', 'products.store_id = stores_translation.store_id AND stores_translation.lang_id ='. $lang_id);
        $this->db->join('stores', 'products.store_id = stores.id');

        if($user_id != 0)
        {
            $this->db->join('users_spams', 'products.id = users_spams.product_id AND users_spams.user_id='.$user_id, 'left');
            $this->db->where('users_spams.product_id IS NULL', null, 'false');
        }

        if(count($blocked_users_ids) != 0)
        {
            $this->db->where_not_in('products.owner_id', $blocked_users_ids);
        }

        if($store_id != 0)
        {
            $this->db->where('products.store_id', $store_id);
        }

        $this->db->where('products_countries.active', 1);
        $this->db->where('stores.active', 1);
        $this->db->where('products_countries.country_id', $country_id);
        $this->db->where('products_translation.lang_id', $lang_id);
        $this->db->where('products.view != 0');

        $this->db->order_by('products.view', 'desc');

        $query = $this->db->get('products', $limit);

        if($query)
        {
            return $query->result();
        }else{
            return false ;
        }
    }


/****************************************************************/
}
