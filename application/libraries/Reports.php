<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
/**
 *
 */
class Reports
{
    public $CI ;

    public function __construct()
    {
        $this->CI = &get_instance();

        $this->CI->load->model('orders/orders_model');
        $this->CI->load->model('users/user_model');

        $this->CI->load->library('currency');
    }

    public function countries_sales($lang_id, $store_id_array)
    {
        $sales = $this->CI->orders_model->get_countries_sales($lang_id, $store_id_array);
        $sales_array = array();

        foreach($sales as $row)
        {
            $amount_with_default_currency = $this->CI->currency->get_amount_with_default_currency($row->orders_total, $row->country_id);
            $row->{'orders_total'} = $amount_with_default_currency;

            $sales_array[] = $row;
        }

        return $sales_array;
    }

    public function categories_sales($lang_id, $store_id_array)
    {

        $sales     = array();
        $countries = $this->CI->global_model->get_countries($lang_id);

        foreach($countries as $country)
        {
            $sales_orders = $this->CI->orders_model->get_categories_sales($lang_id, $country->id, $store_id_array);

            foreach($sales_orders as $order)
            {

                if(! array_key_exists($order->id, $sales))
                {
                    $sales[$order->id] = array(
                                                'cat_name' => $order->cat_name,
                                                'cat_sales' => 0
                                              );
                }

                $amount_with_default_currency = $this->CI->currency->get_amount_with_default_currency($order->cat_sales, $order->country_id);

                $sales[$order->id]['cat_sales'] += $amount_with_default_currency;
            }
        }

        return $sales;
    }

    public function agents_sales($lang_id, $stores_ids)
    {
        $sales     = array();
        $countries = $this->CI->global_model->get_countries($lang_id);

        foreach($countries as $key=>$country)
        {
            $orders_array = array();
            $sales_orders = $this->CI->orders_model->get_agents_sales($lang_id, $country->id, $stores_ids);

            foreach($sales_orders as $order)
            {
                if(! array_key_exists($order->agent, $sales))
                {
                    $sales[$order->agent] = 0;
                }

                $amount_with_default_currency = $this->CI->currency->get_amount_with_default_currency($order->total_sales, $order->country_id);
                $sales[$order->agent]        += $amount_with_default_currency;
            }
        }


        return $sales;
    }

   public function last_regestered_users($max_time)
   {
        $result = $this->CI->user_model->last_regestered_users($max_time);
        return $result;
   }

   public function payment_methods_sales($lang_id, $stores_ids)
   {
        $result = $this->CI->orders_model->get_payment_methods_completed_orders_count($lang_id, $stores_ids);
        return $result;
   }

   public function monthly_sales_amount($year, $lang_id, $stores_ids)
   {
        $sales = array();
        $countries = $this->CI->global_model->get_countries($lang_id);
        foreach($countries as $key=>$country)
        {
            $sales_orders = $this->CI->orders_model->get_year_month_sales($year, $country->id, $stores_ids);

            foreach($sales_orders as $order)
            {
                if(! array_key_exists($order->month, $sales))
                {
                    $sales[$order->month] = array(
                                                    'year'          => $order->year,
                                                    'sales'         => 0,
                                                    'orders_count'  => 0
                                                 );
                }

                $amount_with_default_currency = $this->CI->currency->get_amount_with_default_currency($order->month_sales, $order->country_id);
                $sales[$order->month]['sales']  += $amount_with_default_currency;
                $sales[$order->month]['orders_count'] += $order->orders_count;
            }
        }

        ksort($sales);
        return $sales;
   }

   public function order_status_sales($lang_id, $stores_ids)
   {
        $result    = array();
        $countries = $this->CI->global_model->get_countries($lang_id);

        foreach($countries as $country)
        {
            $sales = $this->CI->orders_model->get_order_status_sales($country->id, $lang_id, $stores_ids);

            foreach($sales as $order)
            {
                if(! array_key_exists($order->order_status_id, $result))
                {
                    $result[$order->order_status_id] = array(
                                                    'status'        => $order->order_status,
                                                    'status_id'     => $order->order_status_id,
                                                    'sales'         => 0,
                                                    'orders_count'  => 0
                                                 );
                }

                $amount_with_default_currency = $this->CI->currency->get_amount_with_default_currency($order->month_sales, $order->country_id);

                $result[$order->order_status_id]['sales']        += $amount_with_default_currency;
                $result[$order->order_status_id]['orders_count'] += $order->orders_count;
            }

        }

        return $result;
   }

}
