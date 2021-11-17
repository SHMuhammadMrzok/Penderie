<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Orders_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    /*************************INSERT*************************/
    public function insert_order($order_data)
    {
        return $this->db->insert('orders', $order_data);
    }

    public function insert_order_products($order_products_data)
    {
        if($this->db->insert('orders_products', $order_products_data))
        {
            return $this->db->insert_id();
        }
        else
        {
            return false;
        }
    }

    public function insert_order_log($order_log_data)
    {
        return $this->db->insert('orders_log', $order_log_data);
    }

    public function insert_product_serials($serials_data)
    {
        return $this->db->insert('orders_serials', $serials_data);
    }

    public function insert_sms_log_data($data)
    {
        return $this->db->insert('sent_serials_via_sms', $data);
    }

    public function insert_payment_log($data)
    {
        return $this->db->insert('payment_log', $data);
    }

    public function insert_order_note($data)
    {
        return $this->db->insert('orders_comments', $data);
    }

    /**************************GET****************************/
    public function get_order_details($order_id, $display_lang_id)
    {

        $this->db->select('orders.*, orders_log.*, users.*, order_status_translation.name,countries.country_symbol, orders.id as id,
                            order_status_translation.name as status, orders.notes as notes, shipping_companies_translation.name as shipping_company,
                            shipping_costs_translation.country as shipping_country, branches_translation.name as branch_name,
                            cities_translation.name as city_name, shipping_methods_translation.name as shipping_method,
                            stores_translation.name as store_name, orders.id as id, orders.user_id as user_id,
                            shipping_cities_translation.name as shipping_city, payment_methods_translation.name as payment_method, user_addresses.lat, user_addresses.lng,
                            user_addresses.address as shipping_address, user_addresses.city as shipping_city, user_addresses.title as shipping_district'
                            .',orders.shipping_city as order_shipping_city, orders.shipping_town as order_shipping_town, orders.shipping_district as orders_shipping_district, orders.shipping_address as order_shipping_address ' // only This row, Mrzok Edits
                            .', user_nationality_translation.name as country_name'
                            ); // , user_addresses.*

        $this->db->join('orders_log', 'orders.id = orders_log.order_id', 'left');
        $this->db->join('order_status_translation', 'orders.order_status_id = order_status_translation.status_id', 'left');
        $this->db->join('users', 'orders.user_id = users.id', 'left');
        $this->db->join('shipping_companies_translation', 'orders.shipping_company_id = shipping_companies_translation.shipping_company_id
                        AND shipping_companies_translation.lang_id ='.$display_lang_id, 'left');
        $this->db->join('shipping_costs_translation', 'orders.shipping_country_id = shipping_costs_translation.shipping_cost_id
                        AND shipping_costs_translation.lang_id ='.$display_lang_id, 'left');
        $this->db->join('shipping_methods_translation', 'orders.shipping_type = shipping_methods_translation.method_id
                        AND shipping_methods_translation.lang_id ='.$display_lang_id, 'left');

        $this->db->join('branches_translation', 'orders.branch_id = branches_translation.branch_id
                        AND branches_translation.lang_id ='.$display_lang_id, 'left');
        $this->db->join('cities_translation', 'orders.shipping_city = cities_translation.city_id
                        AND cities_translation.lang_id ='.$display_lang_id, 'left');
        $this->db->join('stores_translation', 'orders.store_id = stores_translation.store_id
                        AND stores_translation.lang_id ='.$display_lang_id, 'left');
        $this->db->join('shipping_cities_translation', 'orders.shipping_city = shipping_cities_translation.city_id
                        AND shipping_cities_translation.lang_id ='.$display_lang_id, 'left');
        $this->db->join('user_addresses', 'orders.address_id = user_addresses.id', 'left');
        $this->db->join('payment_methods_translation', 'orders.payment_method_id = payment_methods_translation.payment_method_id
                        AND payment_methods_translation.lang_id ='.$display_lang_id, 'left');
        $this->db->join('countries', 'orders.country_id = countries.id');
        
        $this->db->join('user_nationality_translation', 'users.Country_ID = user_nationality_translation.user_nationality_id AND user_nationality_translation.lang_id = '.$display_lang_id);


        $this->db->where('orders.id', $order_id);
        $this->db->where('order_status_translation.lang_id', $display_lang_id);

        $query = $this->db->get('orders');

        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }

    public function get_order_main_details($order_id, $display_lang_id)
    {

        $this->db->select('orders.*, users.*, shipping_companies_translation.name as shipping_compani_name, countries.country_symbol, order_status_translation.name, orders.id as id, order_status_translation.name as status');

        $this->db->join('order_status_translation', 'orders.order_status_id = order_status_translation.status_id');
        $this->db->join('users', 'orders.user_id = users.id');
        $this->db->join('countries', 'orders.country_id = countries.id');
        $this->db->join('shipping_companies_translation', 'orders.shipping_company_id = shipping_companies_translation.shipping_company_id');

        $this->db->where('orders.id', $order_id);
        $this->db->where('order_status_translation.lang_id', $display_lang_id);
        $this->db->where('shipping_companies_translation.lang_id', $display_lang_id);

        $query = $this->db->get('orders');

        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }

    public function get_order($order_id)
    {
        $this->db->where('id', $order_id);
        $row = $this->db->get('orders');

        if($row)
        {
            return $row->row();
        }
        else
        {
            return false;
        }
    }

    public function get_order_with_country($order_id, $lang_id)
    {
        $this->db->select('orders.*, countries_translation.name as country, stores_translation.name as store_name');

        $this->db->join('countries_translation', 'orders.country_id = countries_translation.country_id AND countries_translation.lang_id ='.$lang_id);
        $this->db->join('stores_translation', 'orders.store_id = stores_translation.store_id AND stores_translation.lang_id ='.$lang_id);

        $this->db->where('orders.id', $order_id);
        $query = $this->db->get('orders');

        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }

    public function get_order_products($order_id, $display_lang_id, $product_title = '')
    {
        $this->db->select('orders_products.*, products.*, products_translation.title,
        orders_products.id as order_product_id, orders_products.product_id as product_id,
        orders_products.id as order_product_id, categories_translation.name as cat_name,
        categories.route as cat_route');

        $this->db->join('products', 'orders_products.product_id = products.id', 'left');
        $this->db->join('categories', 'products.cat_id = categories.id', 'left');
        $this->db->join('categories_translation', 'products.cat_id = categories_translation.category_id
                        AND categories_translation.lang_id='.$display_lang_id, 'left');
        $this->db->join('products_translation', 'orders_products.product_id = products_translation.product_id AND products_translation.lang_id = '.$display_lang_id, 'left');

        $this->db->where('orders_products.order_id', $order_id);

        if($product_title != '')
        {
            $this->db->like('products_translation.title', $product_title, 'both');
        }

        $result = $this->db->get('orders_products');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_grouped_orders_products($orders_number, $display_lang_id, $required_order_status = array())
    {
        $this->db->select('orders_products.*, products.*, products_translation.title, orders_products.id as order_product_id, orders_products.product_id as product_id');

        $this->db->join('products', 'orders_products.product_id = products.id', 'left');
        $this->db->join('orders', 'orders_products.order_id = orders.id');
        $this->db->join('products_translation', 'orders_products.product_id = products_translation.product_id
                                                 AND products_translation.lang_id = '.$display_lang_id);

        $this->db->where('orders.orders_number', $orders_number);
        //$this->db->where('orders.id', $orders_number);
        if(!empty($required_order_status)){
            $this->db->where_in('orders.order_status_id', $required_order_status);
        }

        $result = $this->db->get('orders_products');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_order_products_data($order_id)
    {
        $this->db->where('order_id', $order_id);
        $result = $this->db->get('orders_products');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_order_all_products($order_id)
    {
        $this->db->where('order_id', $order_id);

        $result = $this->db->get('orders_products');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }


    public function get_orders_log($order_id, $display_lang_id)
    {
        $this->db->select('orders_log.*, order_status_translation.*, orders_log.id as id');

        $this->db->join('order_status_translation', 'orders_log.status_id = order_status_translation.status_id');

        $this->db->where('orders_log.order_id', $order_id);
        $this->db->where('order_status_translation.lang_id',$display_lang_id);

        $result = $this->db->get('orders_log');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function generate_product_serials($product_id, $limit, $country_id, $store_id=0, $optional_fields=array(), $selected_ops=array())
    {
        $this->db->group_start();
            $this->db->where('serial_status', 0);
            $this->db->where('invalid', 0);
            $this->db->where('active', 1);
            $this->db->where('product_id', $product_id);
        $this->db->group_end();

        $this->db->group_start();
            $this->db->where('country_id', $country_id);
            $this->db->or_where('country_id', 0);
        $this->db->group_end();

        if($store_id != 0)
        {
            $this->db->where('store_id', $store_id);
        }


        // START Mrzok's Edit : To limit the selection to the xart user selected options serials
        if(count($optional_fields) > 0 )
        {
            $ops = implode(',',$optional_fields);
            $this->db->group_start();
                $this->db->where('optional_fields', $ops);
            $this->db->group_end();
        }

        if(count($selected_ops) > 0 )
        {
            $ops = implode(',',$selected_ops);
            $this->db->group_start();
                $this->db->where('selected_optional_fields', $ops);
            $this->db->group_end();
        }
        // End Mrzok's Edit

        $result = $this->db->get('products_serials', $limit);

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_product_serials($product_id, $order_id)
    {
        $display_lang_id = $this->session->userdata('lang_id');
        $this->db->select('orders_serials.*, products_serials.*, products_translation.title, products_translation.title as product_name');

        $this->db->join('products_serials', 'orders_serials.product_serial_id = products_serials.id');
        $this->db->join('products_translation', 'orders_serials.product_id = products_translation.product_id');

        $this->db->where('products_translation.lang_id', $display_lang_id);
        $this->db->where('orders_serials.product_id', $product_id);
        $this->db->where('orders_serials.order_id', $order_id);
        $this->db->where('products_serials.invalid', 0);

        $result = $this->db->get('orders_serials');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_admin_product_serials($product_id, $order_id, $order_product_id = 0)
    {
        $this->db->select('orders_serials.*, products_serials.*');
        $this->db->join('products_serials', 'orders_serials.product_serial_id = products_serials.id');

        $this->db->where('orders_serials.product_id', $product_id);
        $this->db->where('orders_serials.order_id', $order_id);

        // Mrzok Edit 9/2021 => to get serials of specific order_product_id
        if($order_product_id != 0){
            $this->db->where('orders_serials.order_product_id', $order_product_id);
        }
        ///

        $result = $this->db->get('orders_serials');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_wholesaler_customer_group_id()
    {
        $settings = $this->db->get('settings')->result();

        if($settings)
        {
            return $settings->wholesaler_customer_group_id;
        }
        else
        {
            return false;
        }
    }

    public function get_payment_status_id($payment_method_id)
    {
        $this->db->where('id', $payment_method_id);
        $row = $this->db->get('bank_accounts')->row();

        if($row)
        {
            return $row->order_status_id;
        }
        else
        {
            return false;
        }
    }

    public function get_other_payment_status_id($payment_method_id)
    {
        $this->db->where('id', $payment_method_id);
        $row = $this->db->get('payment_methods')->row();

        if($row)
        {
            return $row->order_status_id;
        }
        else
        {
            return false;
        }
    }

    public function get_count_all_orders($lang_id, $stores_ids=array(), $search_word='', $search_field_id=0, $username_filter_id=0, $countries_filter_id=0, $status_filter_id=0, $payment_filter_id=0, $driver_user_id=0)
    {
        $this->db->join('users' ,'orders.user_id = users.id');
        $this->db->join('countries_translation', 'orders.country_id = countries_translation.country_id');
        $this->db->join('order_status_translation', 'orders.order_status_id = order_status_translation.status_id');
        $this->db->join('payment_methods_translation', 'orders.payment_method_id = payment_methods_translation.payment_method_id');

        if(trim($search_word) !='')
        {
            //$this->db->where('(users.username LIKE "%'.$search_word.'%" OR orders.id LIKE "%'.$search_word.'%" OR orders.total LIKE "%'.$search_word.'%"OR orders.final_total LIKE "%'.$search_word.'%")');
            if($search_field_id == 0)
            {
                $this->db->like('orders.id', $search_word, 'both');
            }
            elseif($search_field_id == 1)
            {
                $this->db->like('orders.final_total', $search_word, 'both');
            }
            elseif($search_field_id == 2)
            {
                $this->db->like('users.username', $search_word, 'both');
            }
            elseif($search_field_id == 3)
            {
                $this->db->like('users.phone', $search_word, 'both');
            }
            elseif($search_field_id == 4)
            {
                $this->db->like('orders.cart_id', $search_word, 'both');
            }
        }


        if($countries_filter_id != 0)
        {
            $this->db->where('orders.country_id', $countries_filter_id);
        }

        if($username_filter_id != 0)
        {
            $this->db->where('orders.user_id', $username_filter_id);
        }

        if($status_filter_id != 0)
        {
            $this->db->where('orders.order_status_id', $status_filter_id);
        }

        if($payment_filter_id != 0)
        {
            $this->db->where('orders.payment_method_id', $payment_filter_id);
        }

        if(count($stores_ids) != 0)
        {
            $this->db->where_in('orders.store_id', $stores_ids);
        }

        if($driver_user_id != 0)
        {
            $this->db->where('orders.driver_id', $driver_user_id);
        }

        $this->db->where('countries_translation.lang_id', $lang_id);
        $this->db->where('order_status_translation.lang_id', $lang_id);
        $this->db->where('payment_methods_translation.lang_id', $lang_id);


        return $this->db->count_all_results('orders');
    }

    public function get_orders_data($lang_id, $limit, $offset, $search_word, $order_by,
    $order_state, $username_filter_id, $countries_filter_id, $status_filter_id,
    $payment_filter_id, $search_field_id=0, $shipping_way_filter=0, $stores_filter_id=0,
    $stores_ids=array(), $driver_user_id=0)
    {
        $this->db->select('orders.* , users.*, order_status_translation.name, orders.id as id, order_status_translation.name as status,
                            countries_translation.name as country, payment_methods_translation.name as payment_method,
                            stores_translation.name as store_name');

        $this->db->join('users' ,'orders.user_id = users.id', 'left');
        $this->db->join('countries_translation', 'orders.country_id = countries_translation.country_id', 'left');
        $this->db->join('order_status_translation', 'orders.order_status_id = order_status_translation.status_id', 'left');
        $this->db->join('payment_methods_translation', 'orders.payment_method_id = payment_methods_translation.payment_method_id', 'left');
        $this->db->join('stores_translation', 'orders.store_id = stores_translation.store_id AND stores_translation.lang_id ='.$lang_id, 'left');

        if(trim($search_word) !='')
        {
            if($search_field_id == 0)
            {
                $this->db->like('orders.id', $search_word, 'both');
            }
            elseif($search_field_id == 1)
            {
                $this->db->like('orders.final_total', $search_word, 'both');
            }
            elseif($search_field_id == 2)
            {
                $this->db->like('users.username', $search_word, 'both');
            }
            elseif($search_field_id == 3)
            {
                $this->db->like('users.phone', $search_word, 'both');
            }
            elseif($search_field_id == 4)
            {
                $this->db->like('orders.cart_id', $search_word, 'both');
            }
            //$this->db->where('(users.username LIKE "%'.$search_word.'%" OR orders.id LIKE "%'.$search_word.'%" OR orders.total LIKE "%'.$search_word.'%"OR orders.final_total LIKE "%'.$search_word.'%")');
        }

        if($order_by != '')
        {
            if($order_by == lang('username'))
            {
                $this->db->order_by('users.username',$order_state);
            }
            elseif($order_by == lang('final_total'))
            {
                $this->db->order_by('orders.final_total',$order_state);
            }
            else
            {
                $this->db->order_by('orders.id',$order_state);
            }
        }
        else
        {
            $this->db->order_by('orders.id',$order_state);
        }

        if($countries_filter_id != 0)
        {
            $this->db->where('orders.country_id', $countries_filter_id);
        }

        if($username_filter_id != 0)
        {
            $this->db->where('orders.user_id', $username_filter_id);
        }

        if($status_filter_id != 0)
        {
            $this->db->where('orders.order_status_id', $status_filter_id);
        }
        /*else
        {
            $this->db->where('order_status_id !=', '8');
        }*/

        if($payment_filter_id != 0)
        {
            $this->db->where('orders.payment_method_id', $payment_filter_id);
        }

        if($shipping_way_filter != 0)
        {
            $this->db->where('orders.shipping_type', $shipping_way_filter);
        }

        if($stores_filter_id != 0)
        {
            $this->db->where('orders.store_id', $stores_filter_id);
        }

        if(count($stores_ids) != 0)
        {
            $this->db->where_in('orders.store_id', $stores_ids);
        }

        $this->db->where('countries_translation.lang_id', $lang_id);
        $this->db->where('order_status_translation.lang_id', $lang_id);
        $this->db->where('payment_methods_translation.lang_id', $lang_id);

        if($driver_user_id != 0)
        {
          $this->db->where('orders.driver_id', $driver_user_id);
        }

        $result = $this->db->get('orders', $limit, $offset);

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_serial_data($serial_id, $lang_id)
    {
        $this->db->select('orders_serials.*, products_serials.*, products.image, products_translation.title');

        $this->db->join('products_serials', 'orders_serials.product_serial_id = products_serials.id');
        $this->db->join('products', 'orders_serials.product_id = products.id');
        $this->db->join('products_translation', 'orders_serials.product_id = products_translation.product_id');

        $this->db->where('orders_serials.product_serial_id', $serial_id);
        $this->db->where('products_translation.lang_id', $lang_id);

        $row = $this->db->get('orders_serials')->row();

        if($row)
        {
            return $row;
        }
        else
        {
            return false;
        }
    }

    public function get_user_order_data($user_id, $display_lang_id, $limit, $offset, $order_number='', $start_date='', $end_date='', $final_total='', $status='', $grouped_orders=0, $orders_number=0, $status_id=0)
    {
        $this->db->select('orders.*, order_status_translation.name, orders.id as id, order_status_translation.name as status, stores_translation.name as store_name');

        $this->db->join('order_status_translation', 'orders.order_status_id = order_status_translation.status_id');
        $this->db->join('stores_translation', 'orders.store_id = stores_translation.store_id AND stores_translation.lang_id='.$display_lang_id, 'left');

        $this->db->where('orders.admin_not_completed_order != 1');
        $this->db->where('orders.order_status_id != 4');
        $this->db->where('orders.user_id', $user_id);
        $this->db->where('order_status_translation.lang_id', $display_lang_id);

        $this->db->order_by('orders.id', 'desc');

        if($order_number != '')
        {
            $this->db->like('orders.id', $order_number, 'both');
        }
        if($start_date != '')
        {
            $this->db->where('orders.unix_time >= ', $start_date);
            $this->db->where('orders.unix_time <= ', $end_date);
        }
        if($final_total != '')
        {
            $this->db->like('orders.final_total', $final_total, 'both');
        }
        if($status != '')
        {
            $this->db->like('order_status_translation.name', $status, 'both');
        }

        if($status_id != 0)
        {
            $this->db->like('orders.order_status_id', $status_id);
        }

        if($grouped_orders == 1)
        {
            $this->db->group_by('orders.orders_number');
        }


        if($orders_number != 0)
        {
            $this->db->where('orders.orders_number', $orders_number);
        }

        if($limit == 0)
        {
          $result = $this->db->get('orders');
        }
        else {
          $result = $this->db->get('orders', $limit, $offset);
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

    public function get_all_orders_count($lang_id, $user_id, $order_number='', $start_date='', $end_date='', $final_total='', $status='', $grouped_orders=0, $orders_number=0)
    {
        $this->db->select('orders.*, order_status_translation.name, order_status_translation.name as status , orders.id as order_id');

        $this->db->join('order_status_translation', 'orders.order_status_id = order_status_translation.status_id');

        $this->db->where('orders.user_id', $user_id);
        $this->db->where('orders.order_status_id != 4');
        $this->db->where('order_status_translation.lang_id', $lang_id);

        if($order_number != '')
        {
            $this->db->like('orders.id', $order_number, 'both');
        }
        if($start_date != '')
        {
            $this->db->where('orders.unix_time >= ', $start_date);
            $this->db->where('orders.unix_time <= ', $end_date);
        }
        if($final_total != '')
        {
            $this->db->like('orders.final_total');
        }
        if($status != '')
        {
            $this->db->like('order_status_translation.name', $status, 'both');
        }

        if($grouped_orders == 1)
        {
            $this->db->group_by('orders.orders_number');
        }

        if($orders_number != 0)
        {
            $this->db->where('orders.orders_number', $orders_number);
        }

        return $this->db->count_all_results('orders');
    }

    public function count_previous_rows($last_row_id, $conditions=array(), $stores_ids=array())
    {
        $this->db->order_by('id', 'desc');
        $this->db->where('id >', $last_row_id);

        if(count($conditions) != 0)
        {
            foreach($conditions as $key=>$val)
            {
                $this->db->where($key, $val);
            }
        }

        if(count($stores_ids) != 0)
        {
            $this->db->where_in('store_id', $stores_ids);
        }

        return $this->db->count_all_results('orders');
    }


    public function get_new_row_data($last_order_id, $lang_id, $conditions=array(), $stores_ids)
    {
        $this->db->select('orders.*, order_status_translation.name, users.*, countries_translation.name, orders.id as id, order_status_translation.name as status, countries_translation.name as country, stores_translation.name as store_name');

        $this->db->join('order_status_translation', 'orders.order_status_id = order_status_translation.status_id');
        $this->db->join('countries_translation', 'orders.country_id = countries_translation.country_id');
        $this->db->join('users' ,'orders.user_id = users.id');
        $this->db->join('stores_translation' ,'orders.store_id = stores_translation.store_id AND stores_translation.lang_id ='.$lang_id, 'left');

        $this->db->where('order_status_translation.lang_id', $lang_id);
        $this->db->where('countries_translation.lang_id', $lang_id);

        $this->db->where('orders.id >', $last_order_id);

        if(count($conditions) != 0)
        {
            foreach($conditions as $key=>$val)
            {
                $this->db->where($key, $val);
            }
        }

        if(count($stores_ids) != 0)
        {
            $this->db->where_in('orders.store_id', $stores_ids);
        }

        $row = $this->db->get('orders')->row();

        if($row)
        {
            return $row;
        }
        else
        {
            return false;
        }
    }

    public function get_order_serials_data($product_id, $order_id, $display_lang_id)
    {
        $this->db->select('orders_serials.*, products_serials.*, products.*, products_translation.title, products_serials.id as serial_id');

        $this->db->join('products_serials', 'orders_serials.product_serial_id = products_serials.id');
        $this->db->join('products', 'orders_serials.product_id = products.id');
        $this->db->join('products_translation', 'orders_serials.product_id = products_translation.product_id');

        $this->db->where('orders_serials.order_id', $order_id);
        $this->db->where('orders_serials.product_id', $product_id);
        $this->db->where('products_translation.lang_id', $display_lang_id);

        $result = $this->db->get('orders_serials')->result();

        if($result)
        {
            return $result;
        }
        else
        {
            return false;
        }

    }

    public function get_order_product_serials_data($order_id, $lang_id)
    {
        $this->db->select('orders_serials.*, products_serials.*, products_translation.title as product_name, orders_serials.order_id as order_id');

        $this->db->join('orders_serials', 'orders_products.product_id = orders_serials.product_id');
        $this->db->join('products_serials', 'orders_serials.product_serial_id = products_serials.id');
        $this->db->join('products_translation', 'orders_products.product_id = products_translation.product_id AND products_translation.lang_id ='.$lang_id);

        $this->db->where('orders_serials.order_id', $order_id);

        $query = $this->db->get('orders_products');

        if($query)
        {
            return $query->row_array();
        }
        else
        {
            return false;
        }

    }

    public function get_order_data($order_id)
    {
        $this->db->where('id', $order_id);
        $row = $this->db->get('orders')->row();

        if($row)
        {
            return $row;
        }
        else
        {
            return false;
        }
    }



    public function get_recharge_cards_count($order_id, $type='')
    {
        if($type != '')
        {
          $this->db->where('type', $type);
        }
        $this->db->where('product_id', 0);
        $this->db->where('order_id', $order_id);

        return $this->db->count_all_results('orders_products');
    }

    public function get_recharge_card($order_id, $lang_id=0, $type='', $return_type='result')
    {
        $this->db->select('orders_products.*, customer_groups.*, customer_groups_translation.*,
                          orders_products.id as id');
        $this->db->join('customer_groups', 'orders_products.package_id=customer_groups.id', 'left');
        $this->db->join('customer_groups_translation', 'orders_products.package_id=customer_groups_translation.customer_group_id
                        AND customer_groups_translation.lang_id = '.$lang_id, 'left');

        $this->db->where('orders_products.product_id', 0);
        $this->db->where('orders_products.order_id', $order_id);

        if($type != '')
        {
          $this->db->where('orders_products.type', $type);
        }

        $result = $this->db->get('orders_products');

        if($result)
        {
            return $result->$return_type();
        }
        else
        {
            return false;
        }
    }

    public function get_order_recharge_card($order_id)
    {
        $this->db->where('product_id', 0);
        $this->db->where('order_id', $order_id);

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

    public function get_orders_return($order_id)
    {
        $this->db->where('order_id', $order_id);

        $query = $this->db->get('orders_return_log');

        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }

    public function get_user_previous_orders_count($user_id, $order_id, $stores_ids=array())
    {
        $this->db->where('user_id', $user_id);
        $this->db->where('id !=', $order_id);

        if(count($stores_ids) != 0)
        {
            $this->db->where_in('store_id', $stores_ids);
        }

        return $this->db->count_all_results('orders');
    }

    public function get_user_previous_orders_data($user_id, $order_id, $lang_id)
    {
        $this->db->select('orders.*, orders.id as id, bank_accounts_translation.bank as bank_name');

        $this->db->join('bank_accounts_translation', 'orders.bank_id = bank_accounts_translation.bank_account_id AND bank_accounts_translation.lang_id ='.$lang_id);

        $this->db->where('orders.user_id', $user_id);
        $this->db->where('orders.id !=', $order_id);
        $this->db->where('payment_method_id', '3');

        $this->db->order_by('orders.id', 'desc');

        $result = $this->db->get('orders', '5');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }


    }

    public function check_serial_in_order($serial_id, $order_id)
    {
        $this->db->where('product_serial_id', $serial_id);
        $this->db->where('order_id', $order_id);

        $count = $this->db->count_all_results('orders_serials');

        if($count != 0)
        {
            return true;
        }
        else
        {
            return false;
        }

    }

    public function update_order_serial_data($serial_id, $order_id, $updated_data)
    {
        $this->db->where('product_serial_id', $serial_id);
        $this->db->where('order_id', $order_id);

        return $this->db->update('orders_serials', $updated_data);
    }

    ///order reports
    public function get_all_orders_data($limit=0, $offset, $lang_id, $store_id=0)
    {
        $this->db->select('orders.*, countries_translation.name, countries_translation.name as country');

        $this->db->join('countries_translation', 'orders.country_id = countries_translation.country_id');

        $this->db->order_by('orders.id', 'desc');
        $this->db->where('orders.order_status_id', 1);
        $this->db->where('countries_translation.lang_id', $lang_id);

        if($store_id != 0)
        {
          $this->db->where('orders.store_id', $store_id);
        }

        if($limit == 0)
        {
          $result = $this->db->get('orders');
        }
        else
        {
          $result = $this->db->get('orders', $limit, $offset);
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

    public function get_orders_final_total($store_id=0)
    {
        $this->db->select('sum(orders.final_total) as orders_total');

        $this->db->where('orders.order_status_id', 1);

        if($store_id != 0)
        {
          $this->db->where('orders.store_id', $store_id);
        }

        $query = $this->db->get('orders');

        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }

    public function get_report_orders_count($store_id=0)
    {
        $this->db->where('orders.order_status_id', 1);

        if($store_id != 0)
        {
          $this->db->where('orders.store_id', $store_id);
        }

        return  $this->db->count_all_results('orders');
    }

    public function get_order_start_time($order_id)
    {
        $this->db->where('order_id', $order_id);
        $this->db->order_by('id', 'desc');

        $row = $this->db->get('orders_log')->row();

        if($row)
        {
            return $row->unix_time;
        }
        else
        {
            return false;
        }
    }

    public function get_order_end_time($order_id)
    {
        $this->db->where('order_id', $order_id);
        $this->db->where('status_id', 1);
        $this->db->order_by('id', 'asc');

        $row = $this->db->get('orders_log')->row();

        if($row)
        {
            return $row->unix_time;
        }
        else
        {
            return false;
        }
    }

    public function get_orders_products_count($order_id)
    {
        $this->db->where('order_id', $order_id);
        return $this->db->count_all_results('orders_products');
    }

    public function count_products_in_order($order_id)
    {
        $this->db->where('type', 'product');
        $this->db->where('order_id', $order_id);

        return $this->db->count_all_results('orders_products');
    }

    public function get_count_completed_orders()
    {
        $this->db->where('order_status_id', 1);
        return $this->db->count_all_results('orders');
    }

    public function count_user_orders_per_day($start_time, $end_time, $user_id)
    {
        $this->db->where('user_id', $user_id);
        $this->db->where("unix_time BETWEEN $end_time AND $start_time");

        return $this->db->count_all_results('orders');
    }


    //Admin Statictics
    public function get_ordered_products($stores_ids=array())
    {
        $this->db->select('orders_products.product_id');

        $this->db->join('orders', 'orders_products.order_id = orders.id');

        $this->db->group_by('orders_products.product_id');

        $this->db->where('orders.order_status_id', 1);        // get Completed orders only
        $this->db->where('orders_products.product_id !=', 0); // get products not charge cards

        if(count($stores_ids) != 0)
        {
          $this->db->where_in('orders.store_id', $stores_ids);
        }

        $result = $this->db->get('orders_products');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_product_count($product_id)
    {
        $this->db->select('orders_products.product_id');

        $this->db->join('orders', 'orders_products.order_id = orders.id');

        // $this->db->group_by('orders_products.product_id');

        $this->db->where('orders.order_status_id', 1);        // get Completed orders only
        $this->db->where('product_id', $product_id);

        return $this->db->count_all_results('orders_products');
    }

    public function get_user_orders_count($user_id)
    {
        $this->db->where('user_id', $user_id);
        return $this->db->count_all_results('orders');
    }

    public function get_user_orders($user_id)
    {
        $this->db->where('user_id', $user_id);
        $this->db->where('order_status_id', 1);

        $result = $this->db->get('orders');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_limited_orders($limit, $lang_id, $stores_ids=array())
    {
        $this->db->select('orders.*, users.*, order_status_translation.name , orders.id as id, order_status_translation.name as status');

        $this->db->join('users', 'orders.user_id = users.id');
        $this->db->join('order_status_translation', 'orders.order_status_id = order_status_translation.status_id');

        $this->db->order_by('orders.id', 'desc');
        $this->db->where('order_status_translation.lang_id', $lang_id);

        if(count($stores_ids) != 0)
        {
          $this->db->where_in('orders.store_id', $stores_ids);
        }

        $result = $this->db->get('orders', $limit);

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_conditioned_orders($limit, $conditions, $lang_id, $stores_ids=array())
    {
        $this->db->select('orders.*, users.*, order_status_translation.name , orders.id as id, order_status_translation.name as status');

        $this->db->join('users', 'orders.user_id = users.id');
        $this->db->join('order_status_translation', 'orders.order_status_id = order_status_translation.status_id');

        $this->db->order_by('orders.id', 'desc');
        $this->db->where('order_status_translation.lang_id', $lang_id);

        foreach($conditions as $field => $value)
        {
            $this->db->where($field, $value);
        }

        if(count($stores_ids) != 0)
        {
          $this->db->where_in('orders.store_id', $stores_ids);
        }

        $result = $this->db->get('orders', $limit);

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_order_serials($order_id)
    {
        $this->db->select('orders_serials.*');
        $this->db->join('products_serials', 'orders_serials.product_serial_id = products_serials.id AND products_serials.serial_status !=3');

        $this->db->where('orders_serials.order_id', $order_id);

        $result = $this->db->get('orders_serials');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_order_non_serials_products($order_id, $lang_id)
    {
        $this->db->select('products_translation.title, products.*, products.id as product_id');

        $this->db->join('products', 'orders_products.product_id = products.id AND products.quantity_per_serial=0');
        $this->db->join('products_translation', 'orders_products.product_id = products_translation.product_id AND products_translation.lang_id = ' . $lang_id);

        $this->db->where('orders_products.order_id', $order_id);

        $result = $this->db->get('orders_products');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }


    public function get_all_pending_orders()
    {
        $ids = array(2,8);
        $this->db->where('auto_cancel', 1);         // order auto cancel is activated
        $this->db->where_in('order_status_id', $ids);     // order status is pending
        $this->db->order_by('id', 'acs');

        $result = $this->db->get('orders')->result();

        if($result)
        {
            return $result;
        }
        else
        {
            return false;
        }
    }


    ///////////////Dynamic Reports/////////////////////



    public function get_orders_filtered_data($lang_id, $country_id, $payment_id, $user_id, $user_email_id, $user_phone_id, $user_ip_address_id, $customer_group_id, $cat_id, $coupon_id, $order_id_from, $order_id_to, $date_from, $date_to, $status_date_from, $status_date_to, $order_status_id, $limit, $offset, $store_id)
    {
        $this->db->select('orders.*, count(orders.id) as orders_count, sum(orders.final_total) as total, sum(orders.coupon_discount) as coupons_discount, count(distinct orders.user_id) as users_count, sum(orders.items_count) as products_count');

        $this->_filtered_reports_conditions($lang_id, $country_id, $payment_id, $user_id, $user_email_id, $user_phone_id, $user_ip_address_id, $customer_group_id, $cat_id, $coupon_id, $order_id_from, $order_id_to, $date_from, $date_to, $status_date_from, $status_date_to, $order_status_id, $store_id);

        $this->db->order_by('orders.id', 'desc');
        $this->db->group_by('orders.year, orders.month');

        $result = $this->db->get('orders', $limit, $offset);

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_orders_sums($lang_id, $country_id, $payment_id, $user_id, $user_email_id, $user_phone_id, $user_ip_address_id, $customer_group_id, $cat_id, $coupon_id, $order_id_from, $order_id_to, $date_from, $date_to, $status_date_from, $status_date_to, $order_status_id, $limit, $offset)
    {
        $this->db->select('count(orders.id) as orders_count, sum(orders.final_total) as total, sum(orders.coupon_discount) as coupons_discount, count(distinct orders.user_id) as users_count, sum(orders.items_count) as products_count ');

        $this->_filtered_reports_conditions($lang_id, $country_id, $payment_id, $user_id, $user_email_id, $user_phone_id, $user_ip_address_id, $customer_group_id, $cat_id, $coupon_id, $order_id_from, $order_id_to, $date_from, $date_to, $status_date_from, $status_date_to, $order_status_id);

        $this->db->order_by('orders.id', 'desc');
        $this->db->group_by('orders.year, orders.month');

        $this->db->where('orders.order_status_id', 1);

        $result = $this->db->get('orders', $limit, $offset);

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    private function _filtered_reports_conditions($lang_id, $country_id, $payment_id, $user_id, $user_email_id, $user_phone_id, $user_ip_address_id, $customer_group_id, $cat_id, $coupon_id, $order_id_from, $order_id_to, $date_from, $date_to, $status_date_from, $status_date_to, $order_status_id, $store_id)
    {
        if($country_id != 0)
        {
            $this->db->where('orders.country_id', $country_id);
        }

        if($payment_id != 0)
        {
            $this->db->where('orders.payment_method_id', $payment_id);
        }

        if($user_id != 0)
        {
            $this->db->where('orders.user_id', $user_id);
        }

        if($user_email_id != 0)
        {
            $this->db->where('orders.user_id', $user_email_id);
        }

        if($user_phone_id != 0)
        {
            $this->db->where('orders.user_id', $user_phone_id);
        }

        if($user_ip_address_id != 0)
        {
            $this->db->where('orders.user_id', $user_ip_address_id);
        }

        if($customer_group_id != 0)
        {
            $this->db->join('users', 'orders.user_id = users.id');
            $this->db->where('users.customer_group_id', $customer_group_id);
        }

        if(count($cat_id) != 0)
        {
            $this->db->join('orders_products', 'orders.id = orders_products.order_id');
            $this->db->where_in('orders_products.cat_id', $cat_id);
        }

        if($coupon_id != 0)
        {
            $this->db->join('coupon_codes_users', 'orders.id = coupon_codes_users.order_id');
            $this->db->where('coupon_codes_users.coupon_id', $coupon_id);
        }

        if($store_id != 0)
        {
            $this->db->join('stores', 'orders.store_id = stores.id');
            $this->db->where('orders.store_id', $store_id);
        }

        if($order_id_from !=0)
        {
            $this->db->where('orders.id >=', $order_id_from);
        }

        if($order_id_to !=0)
        {
            $this->db->where('orders.id <', $order_id_to);
        }

        if($date_from != 0)
        {
            $this->db->where('orders.unix_time >=', $date_from);
        }

        if($date_to != 0)
        {
            $this->db->where('orders.unix_time <', $date_to);
        }

        if($status_date_from != 0 || $status_date_to != 0 || $order_status_id !=0)
        {
            //$this->db->join('orders_log', 'orders.id = orders_log.order_id');

            if($status_date_from != 0)
            {
                $this->db->where('orders.unix_time >=', $status_date_from);
            }

            if($status_date_to != 0)
            {
                $this->db->where('orders.unix_time <=', $status_date_to);
            }

            if($order_status_id != 0)
            {
                $this->db->where('orders.order_status_id', $order_status_id);
            }
        }




    }


    public function get_count_filtered_reports($lang_id, $country_id, $payment_id, $user_id, $user_email_id, $user_phone_id, $user_ip_address_id, $customer_group_id, $cat_id, $coupon_id, $order_id_from, $order_id_to, $date_from, $date_to, $status_date_from, $status_date_to, $order_status_id, $store_id)
    {
        $this->_filtered_reports_conditions($lang_id, $country_id, $payment_id, $user_id, $user_email_id, $user_phone_id, $user_ip_address_id, $customer_group_id, $cat_id, $coupon_id, $order_id_from, $order_id_to, $date_from, $date_to, $status_date_from, $status_date_to, $order_status_id, $store_id);

        $this->db->group_by('orders.year, orders.month');

        return $this->db->get('orders')->num_rows();

    }

    public function get_year_month_orders($month, $year, $lang_id, $country_id, $payment_id, $user_id, $user_email_id, $user_phone_id, $user_ip_address_id, $customer_group_id, $cat_id, $coupon_id, $order_id_from, $order_id_to, $date_from, $date_to, $status_date_from, $status_date_to, $order_status_id, $limit=0, $offset=0, $store_id=0)
    {

        $this->db->select('orders.*, users.*, countries_translation.name as country, order_status_translation.name as status, payment_methods_translation.name as payment_method, orders.id as id');

        $this->db->join('users', 'orders.user_id = users.id');
        $this->db->join('countries_translation', 'orders.country_id = countries_translation.country_id AND countries_translation.lang_id ='.$lang_id);
        $this->db->join('order_status_translation', 'orders.order_status_id = order_status_translation.status_id AND order_status_translation.lang_id ='.$lang_id, 'left');
        $this->db->join('payment_methods_translation', 'orders.payment_method_id = payment_methods_translation.payment_method_id AND payment_methods_translation.lang_id ='.$lang_id, 'left');

        if($country_id != 0)
        {
            $this->db->where('orders.country_id', $country_id);
        }

        if($payment_id != 0)
        {
            $this->db->where('orders.payment_method_id', $payment_id);
        }

        if($user_id != 0)
        {
            $this->db->where('orders.user_id', $user_id);
        }

        if($user_email_id != 0)
        {
            $this->db->where('orders.user_id', $user_email_id);
        }

        if($user_phone_id != 0)
        {
            $this->db->where('orders.user_id', $user_phone_id);
        }

        if($user_ip_address_id != 0)
        {
            $this->db->where('orders.user_id', $user_ip_address_id);
        }

        if($customer_group_id != 0)
        {
            $this->db->where('users.customer_group_id', $customer_group_id);
        }

        if($cat_id != 0)
        {
            $this->db->join('orders_products', 'orders.id = orders_products.order_id');
            $this->db->where_in('orders_products.cat_id', $cat_id);
        }

        if($coupon_id != 0)
        {
            $this->db->join('coupon_codes_users', 'orders.id = coupon_codes_users.order_id');
            $this->db->where('coupon_codes_users.coupon_id', $coupon_id);
        }

        if($order_id_from !=0)
        {
            $this->db->where('orders.id >=', $order_id_from);
        }

        if($store_id !=0)
        {
            $this->db->where('orders.store_id ', $store_id);
        }

        if($order_id_to !=0)
        {
            $this->db->where('orders.id <', $order_id_to);
        }

        if($date_from != 0)
        {
            $this->db->where('orders.unix_time >=', $date_from);
        }

        if($date_to != 0)
        {
            $this->db->where('orders.unix_time <', $date_to);
        }

        if($status_date_from != 0 || $status_date_to != 0 || $order_status_id !=0)
        {
            //$this->db->join('orders_log', 'orders.id = orders_log.order_id');

            if($status_date_from != 0)
            {
                $this->db->where('orders.unix_time >=', $status_date_from);
            }

            if($status_date_to != 0)
            {
                $this->db->where('orders.unix_time <', $status_date_to);
            }

            if($order_status_id != 0)
            {
                $this->db->where('orders.order_status_id', $order_status_id);
            }
        }


        $this->db->where('orders.year', $year);
        $this->db->where('orders.month', $month);

        $this->db->order_by('orders.id', 'desc');

        if($limit != 0)
        {
            $result = $this->db->get('orders', $limit, $offset);
        }
        else
        {
            $result = $this->db->get('orders');
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

    public function get_year_month_orders_count($month, $year, $lang_id, $country_id, $payment_id, $user_id, $user_email_id, $user_phone_id, $user_ip_address_id, $customer_group_id, $cat_id, $coupon_id, $order_id_from, $order_id_to, $date_from, $date_to, $status_date_from, $status_date_to, $order_status_id, $store_id)
    {
        $this->db->join('users', 'orders.user_id = users.id');
        $this->db->join('countries_translation', 'orders.country_id = countries_translation.country_id AND countries_translation.lang_id ='.$lang_id);
        $this->db->join('order_status_translation', 'orders.order_status_id = order_status_translation.status_id AND order_status_translation.lang_id ='.$lang_id, 'left');
        $this->db->join('payment_methods_translation', 'orders.payment_method_id = payment_methods_translation.payment_method_id AND payment_methods_translation.lang_id ='.$lang_id, 'left');

        if($country_id != 0)
        {
            $this->db->where('orders.country_id', $country_id);
        }

        if($payment_id != 0)
        {
            $this->db->where('orders.payment_method_id', $payment_id);
        }

        if($user_id != 0)
        {
            $this->db->where('orders.user_id', $user_id);
        }

        if($user_email_id != 0)
        {
            $this->db->where('orders.user_id', $user_email_id);
        }

        if($user_phone_id != 0)
        {
            $this->db->where('orders.user_id', $user_phone_id);
        }

        if($user_ip_address_id != 0)
        {
            $this->db->where('orders.user_id', $user_ip_address_id);
        }

        if($customer_group_id != 0)
        {
            $this->db->where('users.customer_group_id', $customer_group_id);
        }

        if($cat_id != 0)
        {
            $this->db->join('orders_products', 'orders.id = orders_products.order_id');
            $this->db->where_in('orders_products.cat_id', $cat_id);
        }

        if($coupon_id != 0)
        {
            $this->db->join('coupon_codes_users', 'orders.id = coupon_codes_users.order_id');
            $this->db->where('coupon_codes_users.coupon_id', $coupon_id);
        }

        if($order_id_from !=0)
        {
            $this->db->where('orders.id >=', $order_id_from);
        }

        if($order_id_to !=0)
        {
            $this->db->where('orders.id <', $order_id_to);
        }

        if($date_from != 0)
        {
            $this->db->where('orders.unix_time >=', $date_from);
        }

        if($date_to != 0)
        {
            $this->db->where('orders.unix_time <', $date_to);
        }

        if($store_id != 0)
        {
            $this->db->where('orders.store_id', $store_id);
        }

        if($status_date_from != 0 || $status_date_to != 0 || $order_status_id !=0)
        {
            //$this->db->join('orders_log', 'orders.id = orders_log.order_id');

            if($status_date_from != 0)
            {
                $this->db->where('orders.unix_time >=', $status_date_from);
            }

            if($status_date_to != 0)
            {
                $this->db->where('orders.unix_time <', $status_date_to);
            }

            if($order_status_id != 0)
            {
                $this->db->where('orders.order_status_id', $order_status_id);
            }
        }


        $this->db->where('orders.year', $year);
        $this->db->where('orders.month', $month);

        $this->db->order_by('orders.id', 'desc');

        return $this->db->count_all_results('orders');
    }

    public function get_orders_count_in_month($month, $year, $country_id, $payment_id,  $user_id, $user_email_id, $user_phone_id, $user_ip_address_id, $customer_group_id, $cat_id, $coupon_id, $order_id_from, $order_id_to, $date_from, $date_to, $status_date_from, $status_date_to, $order_status_id)
    {
        if($country_id != 0)
        {
            $this->db->where('orders.country_id', $country_id);
        }

        if($payment_id != 0)
        {
            $this->db->where('orders.payment_method_id', $payment_id);
        }

        if($user_id != 0)
        {
            $this->db->where('orders.user_id', $user_id);
        }

        if($customer_group_id != 0)
        {
            $this->db->join('users', 'orders.user_id = users.id');
            $this->db->where('users.customer_group_id', $customer_group_id);
        }

        if($cat_id != 0)
        {
            $this->db->join('orders_products', 'orders.id = orders_products.order_id');
            $this->db->where('orders_products.cat_id', $cat_id);
        }

        if($coupon_id != 0)
        {
            $this->db->join('coupon_codes_users', 'orders.id = coupon_codes_users.order_id');
            $this->db->where('coupon_codes_users.coupon_id', $coupon_id);
        }

        if($order_id_from !=0)
        {
            $this->db->where('orders.id >=', $order_id_from);
        }

        if($order_id_to !=0)
        {
            $this->db->where('orders.id <', $order_id_to);
        }

        if($date_from != 0)
        {
            $this->db->where('orders.unix_time >=', $date_from);
        }

        if($date_to != 0)
        {
            $this->db->where('orders.unix_time <', $date_to);
        }
        if($status_date_from != 0 || $status_date_to != 0 || $order_status_id !=0)
        {
            $this->db->join('orders_log', 'orders.id = orders_log.order_id');
            $this->db->group_by('orders.id');

            if($status_date_from != 0)
            {
                $this->db->where('orders_log.unix_time >=', $status_date_from);
            }

            if($status_date_to != 0)
            {
                $this->db->where('orders_log.unix_time <', $status_date_to);
            }

            if($order_status_id != 0)
            {
                $this->db->where('orders_log.status_id', $order_status_id);
            }
        }


        $this->db->where('MONTH(FROM_UNIXTIME(orders.unix_time))', $month);
        $this->db->where('Year(FROM_UNIXTIME(orders.unix_time))', $year);

        $result = $this->db->get('orders')->result();

        $num_rows = 0;
        if($result)
        {
            foreach($result as $row)
            {
                $num_rows++;
            }
        }

        return $num_rows;

    }



    public function get_users_ordered_count_in_month($month, $year, $country_id, $payment_id, $user_id, $user_email_id, $user_phone_id, $user_ip_address_id, $customer_group_id, $cat_id, $coupon_id, $order_id_from, $order_id_to, $date_from, $date_to, $status_date_from, $status_date_to, $order_status_id)
    {
        if($country_id != 0)
        {
            $this->db->where('orders.country_id', $country_id);
        }

        if($payment_id != 0)
        {
            $this->db->where('orders.payment_method_id', $payment_id);
        }

        if($user_id != 0)
        {
            $this->db->where('orders.user_id', $user_id);
        }

        if($customer_group_id != 0)
        {
            $this->db->join('users', 'orders.user_id = users.id');
            $this->db->where('users.customer_group_id', $customer_group_id);
        }

        if($cat_id != 0)
        {
            $this->db->join('orders_products', 'orders.id = orders_products.order_id');
            $this->db->where('orders_products.cat_id', $cat_id);
        }

        if($coupon_id != 0)
        {
            $this->db->join('coupon_codes_users', 'orders.id = coupon_codes_users.order_id');
            $this->db->where('coupon_codes_users.coupon_id', $coupon_id);
        }

        if($order_id_from !=0)
        {
            $this->db->where('orders.id >=', $order_id_from);
        }

        if($order_id_to !=0)
        {
            $this->db->where('orders.id <', $order_id_to);
        }

        if($date_from != 0)
        {
            $this->db->where('orders.unix_time >=', $date_from);
        }

        if($date_to != 0)
        {
            $this->db->where('orders.unix_time <', $date_to);
        }

        if($status_date_from != 0 || $status_date_to != 0 || $order_status_id !=0)
        {
            $this->db->join('orders_log', 'orders.id = orders_log.order_id');

            if($status_date_from != 0)
            {
                $this->db->where('orders_log.unix_time >=', $status_date_from);
            }

            if($status_date_to != 0)
            {
                $this->db->where('orders_log.unix_time <', $status_date_to);
            }

            if($order_status_id != 0)
            {
                $this->db->where('orders_log.status_id', $order_status_id);
            }
        }


        $this->db->group_by('user_id');
        $this->db->where('MONTH(FROM_UNIXTIME(orders.unix_time))', $month);
        $this->db->where('Year(FROM_UNIXTIME(orders.unix_time))', $year);

        $result = $this->db->get('orders')->result();
        $users_count = 0;

        if($result)
        {
            foreach($result as $row)
            {
                $users_count ++;
            }
        }

        return $users_count;
    }


    public function get_month_orders($month, $year, $country_id=0, $payment_id=0, $user_id=0, $user_email_id=0, $user_phone_id=0, $user_ip_address_id=0, $customer_group_id=0, $cat_id=0, $coupon_id=0, $order_id_from=0, $order_id_to=0, $date_from=0, $date_to=0, $status_date_from=0, $status_date_to=0, $order_status_id=0)
    {
        $this->db->select('orders_products.*, orders.*, orders.coupon_discount as order_coupon_discount, orders.discount as orders_discount');

        $this->db->join('users', 'orders.user_id = users.id');
        $this->db->join('orders_products', 'orders.id = orders_products.order_id', 'left');

        if($country_id != 0)
        {
            $this->db->where('orders.country_id', $country_id);
        }

        if($payment_id != 0)
        {
            $this->db->where('orders.payment_method_id', $payment_id);
        }

        if($user_id != 0)
        {
            $this->db->where('orders.user_id', $user_id);
        }

        if($user_email_id != 0)
        {
            $this->db->where('orders.user_id', $user_email_id);
        }

        if($user_phone_id != 0)
        {
            $this->db->where('orders.user_id', $user_phone_id);
        }

        if($user_ip_address_id != 0)
        {
            $this->db->where('orders.user_id', $user_ip_address_id);
        }

        if($customer_group_id != 0)
        {
            $this->db->where('users.customer_group_id', $customer_group_id);
        }

        if(count($cat_id) != 0)
        {
            $this->db->where_in('orders_products.cat_id', $cat_id);
        }

        if($coupon_id != 0)
        {
            $this->db->join('coupon_codes_users', 'orders.id = coupon_codes_users.order_id');
            $this->db->where('coupon_codes_users.coupon_id', $coupon_id);
        }

        if($order_id_from !=0)
        {
            $this->db->where('orders.id >=', $order_id_from);
        }

        if($order_id_to !=0)
        {
            $this->db->where('orders.id <', $order_id_to);
        }

        if($date_from != 0)
        {
            $this->db->where('orders.unix_time >=', $date_from);
        }

        if($date_to != 0)
        {
            $this->db->where('orders.unix_time <', $date_to);
        }

        if($status_date_from != 0 || $status_date_to != 0 || $order_status_id !=0)
        {
            if($status_date_from != 0)
            {
                $this->db->where('orders.unix_time >=', $status_date_from);
            }

            if($status_date_to != 0)
            {
                $this->db->where('orders.unix_time <', $status_date_to);
            }

            if($order_status_id != 0)
            {
                $this->db->where('orders.order_status_id', $order_status_id);
            }
        }

        $this->db->where('orders.order_status_id', 1);
        $this->db->where('orders.month', $month);
        $this->db->where('orders.year', $year);

        $result = $this->db->get('orders');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }

    }

    public function get_order_payment_count($order_id)
    {
        $this->db->where('order_id', $order_id);
        return $this->db->count_all_results('payment_log');
    }


    public function get_orders_export_data()
    {
        $this->db->select('orders.*');

        $this->db->order_by('orders.unix_time', 'desc');
        $this->db->group_by(array('MONTH(FROM_UNIXTIME(orders.unix_time)), YEAR(FROM_UNIXTIME(orders.unix_time))'));

        $result = $this->db->get('orders')->result();

        if($result)
        {
            return $result;
        }
        else
        {
            return false;
        }
    }

    public function get_month_orders_export_data($month, $year)
    {
        $this->db->select('orders.*, users.*, orders.id as id');
        $this->db->join('users', 'orders.user_id = users.id');

        $this->db->where('MONTH(FROM_UNIXTIME(orders.unix_time))', $month);
        $this->db->where('Year(FROM_UNIXTIME(orders.unix_time))', $year);

        $result = $this->db->get('orders')->result();

        if($result)
        {
            return $result;
        }
    }

    public function get_orders_count_in_month_export_data($month, $year)
    {
        $this->db->where('MONTH(FROM_UNIXTIME(orders.unix_time))', $month);
        $this->db->where('Year(FROM_UNIXTIME(orders.unix_time))', $year);

        $result = $this->db->get('orders');

        $num_rows = 0;
        if($result)
        {
            foreach($result->result() as $row)
            {
                $num_rows++;
            }
        }

        return $num_rows;
    }

    public function get_users_ordered_count_in_month_export_data($month, $year)
    {
        $this->db->group_by('user_id');
        $this->db->where('MONTH(FROM_UNIXTIME(orders.unix_time))', $month);
        $this->db->where('Year(FROM_UNIXTIME(orders.unix_time))', $year);

        $result = $this->db->get('orders');
        $users_count = 0;

        if($result)
        {
            foreach($result->result() as $row)
            {
                $users_count ++;
            }
        }

        return $users_count;
    }

    //////////////////////////////////////////////////////
    /**************************Update*************************/

    public function update_serial_status($seial_status, $id)
    {
        $this->db->where('id', $id);
        $this->db->update('products_serials', $seial_status);
    }

    public function update_order_status($order_id, $status_data)
    {
        $this->db->where('id', $order_id);
        $this->db->update('orders', $status_data);
    }

    public function update_order_serial($serial_id, $updated_data)
    {
        $this->db->where('product_serial_id', $serial_id);

        return $this->db->update('orders_serials', $updated_data);
    }

    public function update_product_order($order_id, $price, $updated_data)
    {
        $this->db->where('type', 'recharge_card');
        $this->db->where('price', $price);
        $this->db->where('order_id', $order_id);

        return $this->db->update('orders_products', $updated_data);
    }

    public function update_product_order_data($order_id, $product_id, $order_product_data)
    {
        $this->db->where('order_id', $order_id);
        $this->db->where('product_id', $product_id);

        return $this->db->update('orders_products', $order_product_data);
    }

    public function update_order_data($order_id, $updated_data)
    {
        $this->db->where('id', $order_id);
        return $this->db->update('orders', $updated_data);
    }

    public function update_payment_log($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('payment_log', $data);
    }

    public function delete_order_data($ids_array)
    {
        $this->db->where_in('order_id', $ids_array);
        $this->db->delete('orders_products');

        $this->db->where_in('order_id', $ids_array);
        $this->db->delete('orders_serials');

        $this->db->where_in('id', $ids_array);
        $this->db->delete('orders');
    }


    public function delete_order_serial_data($serial_id, $order_id, $product_id)
    {
        $this->db->where('product_serial_id', $serial_id);
        $this->db->where('order_id', $order_id);
        $this->db->where('product_id', $product_id);

        $this->db->delete('orders_serials');
    }

    public function get_order_product_details($order_id)
    {
        $this->db->where('order_id', $order_id);

        $result = $this->db->get('orders_products')->result();

        if($result)
        {
            return $result;
        }
        else
        {
            return false;
        }
    }

    public function get_to_be_sent_serials_orders($lang_id, $send_time)
    {
        $this->db->where('order_status_id ', 1);  /// completed orders
        $this->db->where('send_serials_time <=', $send_time);
        $this->db->where('send_later', 1);

        $result = $this->db->get('orders')->result();

        if($result)
        {
            return $result;
        }
        else
        {
            return false;
        }

    }

    public function get_not_replied_orders($check_time)
    {
        $orders_logs_ids = $this->get_orders_log_ids();
        if($orders_logs_ids)
        {
            $this->db->where_in('id', $orders_logs_ids);
        }
        $this->db->where('unix_time <=', $check_time);
        $this->db->where('order_status_id ', 2);
        $this->db->where('sorry_email ', 0);

        $result = $this->db->get('orders')->result();

        if($result)
        {
            return $result;
        }
        else
        {
            return false;
        }

    }

    public function get_orders_log_ids()
    {
        $sql = "
                    SELECT order_id FROM orders_log
                    WHERE order_id IN
                     (SELECT order_id FROM orders_log
                      GROUP BY order_id HAVING COUNT(order_id) =1)";
        $result = $this->db->query($sql);

        if($result)
        {
            $orders_logs_array = array();
            $orders_logs = $result->result();
            foreach($orders_logs as $order)
            {
                $orders_logs_array[] = $order->order_id;
            }

             return $orders_logs_array;
        }
        else
        {
            return false;
        }
    }

   /**************************COUNT**************************/

   public function check_user_have_orders($user_id)
   {
       $this->db->where('user_id', $user_id);
       $count = $this->db->count_all_results('orders');

       if($count > 0)
       {
          return true;
       }
       else
       {
           return false;
       }
   }

   public function get_order_coupon_data($order_id)
   {
       $this->db->select('coupon_codes_users.*, coupon_codes.*,coupon_codes_users.id as id, coupon_codes.id as coupon_id');
       $this->db->join('coupon_codes', 'coupon_codes_users.coupon_id = coupon_codes.id');

       $this->db->where('coupon_codes_users.order_id', $order_id);

       $row = $this->db->get('coupon_codes_users');

       if($row)
       {
           return $row->row();
       }
       else
       {
           return false;
       }
   }

   public function delete_cart_coupon_data($order_id)
   {
        $sql = "DELETE coupon_codes_users_products, coupon_codes_users FROM coupon_codes_users_products
                RIGHT JOIN coupon_codes_users ON coupon_codes_users_products.coupon_codes_users_id = coupon_codes_users.id
                WHERE coupon_codes_users.order_id = $order_id";
        $this->db->query($sql);
   }

   public function update_order_product($order_id, $product_id, $product_data)
   {
       $this->db->where('order_id', $order_id);
       $this->db->where('product_id', $product_id);

       return $this->db->update('orders_products', $product_data);
   }

   public function get_order_product_data($product_id, $order_id)
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

   public function get_order_status_image($status_id)
   {
    $this->db->where('id', $status_id);
    $query = $this->db->get('orders_status')->row();

    if($query)
    {
        return $query->status_image;
    }
    else
    {
        return false;
    }
   }

   function get_order_product_id($order_id, $product_id)
   {
        $this->db->where('order_id', $order_id);
        $this->db->where('product_id', $product_id);

        $query = $this->db->get('orders_products')->row();

        if($query)
        {
            return $query->id;
        }
        else
        {
            return false;
        }
   }

   public function get_serial_order($serial_id)
   {
       $this->db->select('orders.*, orders_serials.*');

       $this->db->join('orders', 'orders_serials.order_id = orders.id');
       $this->db->where('orders_serials.product_serial_id', $serial_id);

       $query = $this->db->get('orders_serials');

       if($query)
       {
           return $query->row();
       }
       else
       {
           return false;
       }
   }

   public function check_if_exist_pay_id($pay_id, $order_id)
   {
       $this->db->where('transaction_id', $pay_id);
       $this->db->or_where('order_id', $order_id);

       $count = $this->db->count_all_results('payment_log');

       if($count > 0)
       {
            return TRUE;
       }
       else
       {
            return FALSE;
       }
   }

   public function insert_order_edit_log($data)
   {
        $this->db->insert('orders_edit_log', $data);
   }

   public function get_order_edit_data($order_id)
   {
        $this->db->select('users.*, orders_edit_log.*');
        $this->db->join('users', 'users.id = orders_edit_log.user_id');

        $this->db->where('orders_edit_log.order_id', $order_id);

        $result = $this->db->get('orders_edit_log');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
   }

   public function get_top10_selling_products($lang_id, $country_id)
   {
       $query = "SELECT orders_products.product_id, COUNT(orders_products.product_id) `count` , products_translation.title , products.*, products.route as route

                FROM orders_products

                join products ON orders_products.product_id = products.id
                join products_translation ON orders_products.product_id = products_translation.product_id AND products_translation.lang_id = ".$lang_id."
                join products_countries ON products.id = products_countries.product_id AND products_countries.country_id = ".$country_id." AND products_countries.active = 1

                GROUP BY orders_products.product_id ORDER BY `count` DESC LIMIT 10";

       $result = $this->db->query($query);

       if($result)
       {
        return $result->result();
       }
       else
       {
        return false;
       }
   }

   public function get_most_bought_products($lang_id, $country_id, $store_ids_string='', $limit=4)
   {
        $query = "SELECT orders_products.product_id, COUNT(orders_products.product_id) `count` ,
                  products_translation.title , products.*, products.route as route,
                  categories.route as cat_route, categories_translation.name as cat_name

                FROM orders_products

                join products ON orders_products.product_id = products.id
                join products_translation ON orders_products.product_id = products_translation.product_id AND products_translation.lang_id = ".$lang_id."
                join categories ON products.cat_id = categories.id
                join categories_translation ON products.cat_id = categories_translation.category_id AND categories_translation.lang_id = ".$lang_id."
                join products_countries ON products.id = products_countries.product_id AND products_countries.country_id = ".$country_id." AND products_countries.active = 1";
      if($store_ids_string != '')
      {
        $query .= " where products.store_id in(".$store_ids_string.") ";
      }
      $query .= " GROUP BY orders_products.product_id ORDER BY `count` DESC LIMIT ".$limit;

        $result = $this->db->query($query);

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
   }

   public function get_most_bought_store_products($lang_id, $country_id, $store_id=0, $limit=15, $user_id=0, $blocked_users_ids=array(), $offset=0, $cart_id=0)
   {
        $query = "SELECT orders_products.product_id, COUNT(orders_products.product_id) as `count` , stores_translation.name as store_name,
              products_translation.title, products_translation.description, products.*, products.route as route, currencies_translation.name as currency,
              products_countries.*, shopping_cart_products.cart_id, users_favourite_products.id as fav_id, users_compare_products.id as compare_id,
              brands_translation.name as brand_name, categories_translation.name as cat_name, categories.route as cat_route

              FROM orders_products

              join products ON orders_products.product_id = products.id
              join categories ON products.cat_id = categories.id
              join products_translation ON orders_products.product_id = products_translation.product_id AND products_translation.lang_id = ".$lang_id."
              join categories_translation ON products.cat_id = categories_translation.category_id AND categories_translation.lang_id = ".$lang_id."
              join stores ON products.store_id = stores.id AND stores.active = 1
              join stores_translation ON products.store_id = stores_translation.store_id AND stores_translation.lang_id = ".$lang_id."
              join products_countries ON products.id = products_countries.product_id AND products_countries.country_id = ".$country_id." AND products_countries.active = 1 AND products_countries.display_home=1
              join countries ON products_countries.country_id = countries.id
              join currencies_translation ON countries.currency_id = currencies_translation.currency_id AND currencies_translation.lang_id =".$lang_id."
              LEFT JOIN brands_translation ON products.brand_id = brands_translation.brand_id AND brands_translation.lang_id = $lang_id
              LEFT JOIN users_compare_products ON products.id = users_compare_products.product_id
                              AND users_compare_products.user_id = $user_id
              LEFT JOIN users_favourite_products ON products.id = users_favourite_products.product_id
                              AND users_favourite_products.user_id = $user_id
              LEFT JOIN shopping_cart_products ON products.id = shopping_cart_products.product_id
                              AND shopping_cart_products.cart_id = $cart_id
              ";
              if($store_id != 0)
              {
                  $where_var = ' WHERE ';
                  $query .= " $where_var orders_products.store_id = ".$store_id;
                  $where_var = ' AND ';
              }
              else
              {
                  $where_var = ' WHERE ';
              }

              if($user_id != 0)
              {
                  $query .= "LEFT JOIN users_spams ON products.id = users_spams.product_id AND users_spams.user_id=$user_id";
                  $query .= "$where_var users_spams.product_id IS NULL";

              }

              if(count($blocked_users_ids) != 0)
              {
                  $query .= "$where_var products.owner_id not in $spammed_users_ids";

              }

              $query .= " GROUP BY orders_products.product_id ORDER BY `count` DESC LIMIT ".$limit;

              if($offset != 0)
              {
                $query .= " OFFSET ".$offset;
              }

        $result = $this->db->query($query);

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
   }

   public function get_most_bought_store_products_count($lang_id, $country_id, $store_id=0, $user_id=0, $blocked_users_ids=array())
   {
        $query = "SELECT COUNT(*) FROM orders_products

              join products ON orders_products.product_id = products.id
              join products_translation ON orders_products.product_id = products_translation.product_id AND products_translation.lang_id = ".$lang_id."
              join stores ON products.store_id = stores.id AND stores.active = 1
              join stores_translation ON products.store_id = stores_translation.store_id AND stores_translation.lang_id = ".$lang_id."
              join products_countries ON products.id = products_countries.product_id AND products_countries.country_id = ".$country_id." AND products_countries.active = 1
              join countries ON products_countries.country_id = countries.id
              join currencies_translation ON countries.currency_id = currencies_translation.currency_id AND currencies_translation.lang_id =".$lang_id."
              ";
              if($store_id != 0)
              {
                  $where_var = ' WHERE ';
                  $query .= " $where_var orders_products.store_id = ".$store_id;
                  $where_var = ' AND ';
              }
              else
              {
                  $where_var = ' WHERE ';
              }

              if($user_id != 0)
              {
                  $query .= "LEFT JOIN users_spams ON products.id = users_spams.product_id AND users_spams.user_id=$user_id";
                  $query .= "$where_var users_spams.product_id IS NULL";

              }

              if(count($blocked_users_ids) != 0)
              {
                  $query .= "$where_var products.owner_id not in $spammed_users_ids";

              }

              $query .= " GROUP BY orders_products.product_id";

        $result = $this->db->query($query);

        return $result->num_rows;
   }


   public function get_shipping_type($method_id, $lang_id)
   {
        $this->db->where('lang_id', $lang_id);
        $this->db->where('method_id', $method_id);

        $result = $this->db->get('shipping_methods_translation');

        if($result->row())
        {
            return $result->row()->name;
        }
        else
        {
            return false;
        }
   }

   public function get_order_notes($order_id)
   {
        $this->db->select('users.*, orders_comments.*');
        $this->db->join('users', 'orders_comments.user_id = users.id');

        $this->db->where('orders_comments.order_id', $order_id);
        $this->db->order_by('orders_comments.id', 'desc');

        $result = $this->db->get('orders_comments');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
   }

   public function updated_orders_related_orders($related_orders_ids, $updated_data)
   {
        $this->db->where_in('id', $related_orders_ids);

        return $this->db->update('orders', $updated_data);
   }

   public function get_orders_max_number()
   {
        $this->db->select_max('orders_number');
        $query = $this->db->get('orders');

        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
   }

   /*public function get_user_grouped_order_data($user_id, $display_lang_id, $orders_number, $is_admin=0)
   {
        $this->db->select('orders.*, order_status_translation.name, orders.id as id, order_status_translation.name as status,
                            payment_methods_translation.name as payment_method, SUM(orders.total) as total, SUM(orders.discount) as discount,
                            SUM(orders.coupon_discount) as coupon_discount, SUM(orders.tax) as tax, SUM(orders.shipping_cost) as shipping_cost,
                            SUM(orders.final_total) as final_total, SUM(orders.wrapping_cost) as wrapping_cost ,
                            user_addresses.*');

        $this->db->join('order_status_translation', 'orders.order_status_id = order_status_translation.status_id');
        $this->db->join('user_addresses', 'orders.address_id = user_addresses.id', 'left');
        $this->db->join('payment_methods_translation', 'orders.payment_method_id = payment_methods_translation.payment_method_id
                                                        AND payment_methods_translation.lang_id = '.$display_lang_id);

        $this->db->where('orders.admin_not_completed_order != 1');
        if(! $is_admin)
        {
          $this->db->where('orders.user_id', $user_id);
        }
        $this->db->where('orders.user_id', $user_id);
        $this->db->where('order_status_translation.lang_id', $display_lang_id);

        $this->db->order_by('orders.id', 'desc');

        $this->db->group_by('orders.orders_number');
        //$this->db->where('orders.orders_number', $orders_number);
        $this->db->where('orders.id', $orders_number);


        $query = $this->db->get('orders');

        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }

    }*/

    public function get_user_grouped_order_data($user_id, $display_lang_id, $orders_number, $is_admin=0)
    {
        $this->db->select('orders.*, order_status_translation.name, orders.id as id, order_status_translation.name as status,
                            payment_methods_translation.name as payment_method, SUM(orders.total) as total, SUM(orders.discount) as discount,
                            SUM(orders.coupon_discount) as coupon_discount, SUM(orders.tax) as tax, SUM(orders.shipping_cost) as shipping_cost,
                            SUM(orders.final_total) as final_total, SUM(orders.wrapping_cost) as wrapping_cost ,
                            user_addresses.*, users.first_name, users.last_name, shipping_companies_translation.name as shipping_company, orders.id as id');

        $this->db->join('order_status_translation', 'orders.order_status_id = order_status_translation.status_id');
        $this->db->join('user_addresses', 'orders.address_id = user_addresses.id', 'left');
        $this->db->join('users', 'orders.user_id = users.id');
        $this->db->join('payment_methods_translation', 'orders.payment_method_id = payment_methods_translation.payment_method_id
                                                        AND payment_methods_translation.lang_id = '.$display_lang_id);
        $this->db->join('shipping_companies_translation', 'orders.shipping_company_id = shipping_companies_translation.shipping_company_id
                                                        AND shipping_companies_translation.lang_id = '.$display_lang_id, 'left');


        $this->db->where('orders.admin_not_completed_order != 1');
        if(! $is_admin)
        {
          $this->db->where('orders.user_id', $user_id);
        }
        $this->db->where('order_status_translation.lang_id', $display_lang_id);
        
        $this->db->where('orders.orders_number', $orders_number);

        $this->db->order_by('orders.id', 'desc');

        //$this->db->group_by('orders.orders_number');


        $query = $this->db->get('orders');

        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }

    }

    public function get_user_grouped_order_basic_data($user_id, $display_lang_id, $orders_number, $is_admin=0)
    {
        $this->db->select('orders.*, users.*, order_status_translation.name, orders.id as id, order_status_translation.name as status,
                            payment_methods_translation.name as payment_method, SUM(orders.total) as total, SUM(orders.discount) as discount,
                            SUM(orders.coupon_discount) as coupon_discount, SUM(orders.tax) as tax, SUM(orders.shipping_cost) as shipping_cost,
                            SUM(orders.final_total) as final_total, SUM(orders.wrapping_cost) as wrapping_cost ,
                            users.first_name, users.last_name, shipping_companies_translation.name as shipping_company, 
                            shipping_costs_translation.country as shipping_country, cities_translation.name as city_name,
                            shipping_cities_translation.name as shipping_city, branches_translation.name as branch_name'
                            .', user_addresses.lat, user_addresses.lng, user_addresses.address as shipping_address, user_addresses.city as shipping_city, user_addresses.title as shipping_district'
                            .',orders.shipping_city as order_shipping_city, orders.shipping_town as order_shipping_town, orders.shipping_district as orders_shipping_district, orders.shipping_address as order_shipping_address '
                            .', user_nationality_translation.name as country_name'
                        );

        $this->db->join('order_status_translation', 'orders.order_status_id = order_status_translation.status_id');
        $this->db->join('user_addresses', 'orders.address_id = user_addresses.id', 'left');
        $this->db->join('users', 'orders.user_id = users.id', 'left');
        $this->db->join('payment_methods_translation', 'orders.payment_method_id = payment_methods_translation.payment_method_id
                                                        AND payment_methods_translation.lang_id = '.$display_lang_id);
        $this->db->join('shipping_companies_translation', 'orders.shipping_company_id = shipping_companies_translation.shipping_company_id
                                                        AND shipping_companies_translation.lang_id = '.$display_lang_id, 'left');
        $this->db->join('shipping_costs_translation', 'orders.shipping_country_id = shipping_costs_translation.shipping_cost_id
                        AND shipping_costs_translation.lang_id ='.$display_lang_id, 'left');
        $this->db->join('cities_translation', 'orders.shipping_city = cities_translation.city_id
                        AND cities_translation.lang_id ='.$display_lang_id, 'left');
        $this->db->join('shipping_cities_translation', 'orders.shipping_city = shipping_cities_translation.city_id
                        AND shipping_cities_translation.lang_id ='.$display_lang_id, 'left');
        $this->db->join('branches_translation', 'orders.branch_id = branches_translation.branch_id
                        AND branches_translation.lang_id ='.$display_lang_id, 'left');
                        
        $this->db->join('user_nationality_translation', 'users.Country_ID = user_nationality_translation.user_nationality_id AND user_nationality_translation.lang_id = '.$display_lang_id);


        $this->db->where('orders.admin_not_completed_order != 1');
        if(! $is_admin)
        {
          $this->db->where('orders.user_id', $user_id);
        }
        $this->db->where('order_status_translation.lang_id', $display_lang_id);

        $this->db->order_by('orders.id', 'desc');

        //$this->db->group_by('orders.orders_number');
        $this->db->where('orders.orders_number', $orders_number);


        $query = $this->db->get('orders');

        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }

    }

    public function get_smsa_cities()
    {
        $result = $this->db->get('smsa_cities');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_cities($lang_id)
    {
        $this->db->where('cities_translation.lang_id', $lang_id);

        $result = $this->db->get('cities_translation');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

   public function get_shipping_status_id($status)
   {
        $this->db->where('status', $status);
        $query = $this->db->get('shipping_status')->row();

        if($query)
        {
            return $query->id;
        }
        else
        {
            return false;
        }
   }

   public function insert_shipping_log($log_data)
   {
        return $this->db->insert('shipping_log', $log_data);
   }

   public function get_order_tracking_log_data($order_id, $lang_id)
   {
        $this->db->select('shipping_log.*, shipping_status_translation.status_name');
        $this->db->join('shipping_status_translation', 'shipping_log.status_id = shipping_status_translation.status_id AND shipping_status_translation.lang_id='.$lang_id,'left');

        $this->db->where('order_id', $order_id);
        $this->db->order_by('id', 'desc');

        $result = $this->db->get('shipping_log');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
   }


    public function check_maintenance_products_exist($order_id, $maintenance_cat_id)
    {
        $this->db->join('categories', 'orders_products.cat_id = categories.id AND categories.parent_id = '.$maintenance_cat_id);
        $this->db->where('orders_products.order_id', $order_id);

        $count = $this->db->count_all_results('orders_products');

        if($count > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /****************************************************************/
/********************REPORTS********************************************/
 public function get_countries_sales($lang_id, $stores_ids= array())
    {
        $this->db->select('countries.id as country_id, countries_translation.name as country_name, sum(orders.final_total) as orders_total');

        $this->db->join('countries_translation', 'countries.id = countries_translation.country_id AND countries_translation.lang_id = '.$lang_id);
        $this->db->join('orders', 'countries.id = orders.country_id');

        if(count($stores_ids) != 0)
        {
            $this->db->where_in('orders.store_id', $stores_ids);
        }

        $this->db->group_by('orders.country_id');

        $result = $this->db->get('countries');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_categories_sales($lang_id, $country_id, $stores_ids)
    {
        $this->db->select('categories.*, categories_translation.name as cat_name, orders.country_id as country_id, sum(orders_products.final_price * orders_products.qty) as cat_sales');

        $this->db->join('categories_translation', 'categories.id = categories_translation.category_id AND categories_translation.lang_id = '.$lang_id);
        $this->db->join('orders_products', 'categories.id = orders_products.cat_id');
        $this->db->join('orders', 'orders_products.order_id = orders.id AND orders.country_id = '.$country_id);

        if(count($stores_ids) != 0)
        {
            $this->db->where_in('orders.store_id', $stores_ids);
        }

        $this->db->group_by('orders_products.cat_id');

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

    public function get_agents_sales($lang_id, $country_id, $stores_ids)
    {
        $this->db->select('orders.agent, orders.country_id, sum(orders.final_total) as total_sales');

        $this->db->where('orders.country_id', $country_id);
        $this->db->where('orders.order_status_id', 1);

        if(count($stores_ids) != 0)
        {
            $this->db->where_in('orders.store_id', $stores_ids);
        }

        $this->db->group_by('orders.agent');

        $result = $this->db->get('orders');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_payment_methods_completed_orders_count($lang_id, $stores_ids)
    {
        $this->db->select('payment_methods_translation.name as payment_method, count(orders.id) as orders_count');
        $this->db->join('payment_methods_translation', 'orders.payment_method_id = payment_methods_translation.payment_method_id
                         AND payment_methods_translation.lang_id = '.$lang_id);

        $this->db->where('orders.order_status_id', 1);
        $this->db->group_by('orders.payment_method_id');

        if(count($stores_ids) != 0)
        {
            $this->db->where_in('orders.store_id', $stores_ids);
        }

        $result = $this->db->get('orders');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_year_month_sales($year, $country_id, $stores_ids)
    {
        $this->db->select('orders.month, orders.year, orders.country_id as country_id,
         SUM(orders.final_total) as month_sales, COUNT(orders.id) as orders_count');

        $this->db->where('orders.order_status_id', 1);
        $this->db->where('orders.year', $year);
        $this->db->where('orders.country_id', $country_id);

        if(count($stores_ids) != 0)
        {
            $this->db->where_in('orders.store_id', $stores_ids);
        }

        $this->db->group_by('orders.month');
        $this->db->order_by('orders.month', 'asc');

        $result = $this->db->get('orders');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_order_status_sales($country_id, $lang_id, $stores_ids)
    {
        $this->db->select('order_status_translation.name as order_status, orders.order_status_id, orders.country_id as country_id,
                            SUM(orders.final_total) as month_sales, COUNT(orders.id) as orders_count');
        $this->db->join('order_status_translation', 'orders.order_status_id = order_status_translation.status_id AND order_status_translation.lang_id = '.$lang_id);

        $this->db->where('orders.country_id', $country_id);
        $this->db->group_by('orders.order_status_id');

        if(count($stores_ids) != 0)
        {
            $this->db->where_in('orders.store_id', $stores_ids);
        }

        $result = $this->db->get('orders');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function delete_order_coupon_data($order_id)
   {
        $sql = "DELETE coupon_codes_users_products, coupon_codes_users FROM coupon_codes_users_products
                RIGHT JOIN coupon_codes_users ON coupon_codes_users_products.coupon_codes_users_id = coupon_codes_users.id
                WHERE coupon_codes_users.order_id = $order_id";
        $this->db->query($sql);
   }

   public function check_product_in_user_orders($user_id, $product_order_id)
   {
     $this->db->join('orders', 'orders_products.order_id = orders.id');
     $this->db->where('orders.user_id', $user_id);
     $this->db->where('orders_products.id', $product_order_id);

     return $this->db->count_all_results('orders_products');
   }

   public function get_order_product_all_data($display_lang_id, $order_product_id=0, $type='row')
   {
       $this->db->select('orders_products.*, products.*, products_translation.title,
       orders_products.id as order_product_id, orders_products.product_id as product_id,
       orders_products.id as order_product_id');

       $this->db->join('products', 'orders_products.product_id = products.id', 'left');
       $this->db->join('products_translation', 'orders_products.product_id = products_translation.product_id AND products_translation.lang_id = '.$display_lang_id);

       $this->db->where('orders_products.id', $order_product_id);

       $query = $this->db->get('orders_products');

       if($query)
       {
           return $query->$type();
       }
       else
       {
           return false;
       }
   }

   public function update_table_data($table_name, $conds, $data)
   {
     foreach($conds as $key=>$val)
     {
       $this->db->where($key, $val);
     }

     return $this->db->update($table_name, $data);
   }

   public function insert_table_data($table_name, $data)
   {
     return $this->db->insert($table_name, $data);
   }

   public function get_table_data($table_name, $conds, $type)
   {
     $this->db->order_by('id', 'desc');
     foreach($conds as $key=>$val)
     {
       $this->db->where($key, $val);
     }

     $query = $this->db->get($table_name);

     if($query)
     {
       return $query->$type();
     }
     else {
       return false;
     }
   }

   public function count_user_orders($user_id)
   {
     $this->db->where('user_id', $user_id);
     $count = $this->db->count_all_results('orders');

     return $count;
   }

   public function get_shipping_cities($company)
   {
     $this->db->where('shipping_company', $company);
     $result = $this->db->get('shipping_cities');

     if($result)
     {
       return $result->result();
     }
     else {
       return false;
     }
   }
    /*****************************************************************/


/****************************************************************/
}
