<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
/**
 *
 */
class Products_lib
{
    public $CI ;
    public $settings;

    public function __construct()
    {
        $this->CI = &get_instance();

        $this->CI->load->model('users/customer_groups_model');
        $this->CI->load->model('products/products_serials_model');
        $this->CI->load->model('orders/orders_model');
        $this->CI->load->model('products/vats_model');
        $this->CI->load->model('global_model');

        $this->settings = $this->CI->global_model->get_config();
    }

    public function get_product_price_data($product_details, $country_id=0, $user_id=0, $session_id='')
    {
        if($country_id == 0)
        {
            $country_id = $this->CI->data['country_id'];
        }

        if($user_id == 0)
        {
            if(isset($this->CI->data))
            {
                $user_id           = $this->CI->data['user_id'];
                $customer_group_id = $this->CI->data['customer_group_id'];
            }
            else
            {
                $user_id = 0;
            }
        }
        else
        {
            $user_data         = $this->CI->user_model->get_row_data($user_id);
            $customer_group_id = $user_data->customer_group_id;
        }

        if($session_id == '')
        {
            $session_id = $this->CI->data['session_id'];
        }

        $allowed_limit = 0;
        $strike        = false;

        $product_discount_data = $this->get_discount($product_details, $country_id, $user_id, $session_id);

        /**
           get_dicount result:
           [0] = discount price
           [1] = allowed number of uses
           [2] = [0] => no limit to use the discount ,
                 [1] => exceed uses of discount
           [3] = used disount id
           [4] = [0] => not dailey discount
                 [1] => is dailey discount
           [5] = end hour
        **/

        $product_discount      = $product_discount_data[0];
        $special_offer         = $product_discount_data['special_offer'];
        $product_price         = $product_details->price;
        $product_price_before  = $product_details->price;    // product price before discount
        $discount_id           = $product_discount_data[3];  // discount_id
        $limit                 = $product_discount_data[2];  // limited uses of discount
        $dailey                = $product_discount_data[4];
        $end_hour              = $product_discount_data[5];
        $discount_percent      = 0;

        if($product_discount > 0)                    /// if not in group && there is discount on this product
        {
            $product_price = $product_discount;
            $allowed_limit = $product_discount_data[1];
            $strike        = true;
            $price_percent = ($product_discount / $product_price_before) * 100;
            $discount_percent = 100 - $price_percent;
        }

        $max_products_per_customer_group = 0;
        //if($this->CI->data['user'] != 'guest')
        if($user_id != 0)
        {
            $new_user_customer_group_id  = $this->settings->new_user_customer_group_id;

            if($new_user_customer_group_id != $customer_group_id)
            {
                $customer_group_price_data = $this->CI->products_model->get_customer_group_price_data($product_details->id, $country_id, $customer_group_id);

                if(isset($customer_group_price_data->group_price) && $customer_group_price_data->group_price != 0)
                {
                    $product_price = $customer_group_price_data->group_price;
                    $strike        = false;
                }
                else
                {
                    $product_price = $product_details->price;
                    $strike        = false;
                }

                $user_customer_group_data        = $this->CI->customer_groups_model->get_customer_group_main_data($customer_group_id);
                $max_products_per_customer_group = $user_customer_group_data->product_limit_per_order;
            }
            else
            {
                $user_customer_group_data        = $this->CI->customer_groups_model->get_customer_group_main_data($customer_group_id);
                $max_products_per_customer_group = $user_customer_group_data->product_limit_per_order;
            }
        }


        $discount = abs($product_price_before - $product_price);

        //$vat_percent = $this->settings->vat_percent;
        $vat_id = $product_details->vat_id; //$vat_percent = $this->settings->vat_percent;

        if($vat_id == 0)
        {
          //$vat_percent = 0;
          //$vat_type    = 0;
          $vat_percent = $this->settings->vat_percent;
          $vat_type    = $this->settings->vat_type;
        }
        else
        {
          $vat_conds   = array('id' => $vat_id);
          $vat_data    = $this->CI->vats_model->get_table_data('vats', $vat_conds, 'row');
          $vat_percent = $vat_data->amount;
          $vat_type    = $vat_data->type;

        }

        $vat_value   = ($product_price * $vat_percent ) / 100;

        /*if($vat_type == 2) // exclusive vat
        {
          $product_price_before = $product_price_before - $vat_value;
          $product_price = $product_price - $vat_value;
        }*/

        $result = array(
                        $product_price_before,
                        $product_price,
                        $limit,
                        $allowed_limit,
                        $strike,
                        $discount,
                        $discount_id,
                        $max_products_per_customer_group,
                        $dailey,
                        $end_hour   ,

                        'product_price_before' => $product_price_before,
                        'product_price' => $product_price,
                        'limit' => $limit,
                        'allowed_limit' => $allowed_limit,
                        'strike' => $strike,
                        'discount' => $discount,
                        'discount_id' => $discount_id,
                        'max_products_per_customer_group' => $max_products_per_customer_group,
                        'dailey'        => $dailey      ,
                        'end_hour'      => $end_hour    ,
                        'vat_percent'   => $vat_percent ,
                        'vat_value'     => $vat_value   ,
                        'vat_type'      => $vat_type    ,
                        'special_offer' => $special_offer,
                        'discount_percent' => round($discount_percent)
                    );

        return $result;

    }

    public function get_discount($product_details, $country_id, $user_id, $session_id)
    {
        $discount_price     = 0;
        $max_per_user       = 0;
        $allowed_limit      = 0;
        $discount_id        = 0;
        $limit              = 0;  // limit number of uses for  discount
        $daily              = 0;  // Daily discount
        $end_hour           = 0;  // remaining seconds in daily discount
        $special_offer      = 0; //special offer label

        $product_id = $product_details->id;

        $count_discounts    = $this->CI->products_model->count_available_discounts_on_product($product_id, $country_id);

        if($count_discounts > 0)
        {
            //check dailey discount
            $current_hour = date('H');

            $product_discount_data = $this->CI->products_model->get_product_daily_discount($product_details->id, $country_id, $current_hour);

            if($product_discount_data)
            {
                $discount_price = $product_discount_data->price;
                $max_per_user   = $product_discount_data->max_units_customers;
                $special_offer  = $product_discount_data->special_offer_label;

                if($max_per_user != 0)
                {
                    $user_discount_uses_count = $this->CI->products_model->count_user_discount_uses($user_id, $session_id, $product_discount_data->id, $country_id);

                    $allowed_limit = $max_per_user - $user_discount_uses_count;
                    $limit = 1;


                }

                $discount_id = $product_discount_data->id;
                $daily       = 1;

                $end_hour    = $product_discount_data->discount_end_time;

            }
            else
            {
                $product_discount_data = $this->CI->products_model->get_product_active_discounts($product_details->id, $country_id);

                // check discount data
                $current_time = time();

                if($product_discount_data->discount_start_unix_time <= $current_time && $product_discount_data->discount_end_unix_time > $current_time)
                {
                    $discount_price = $product_discount_data->price;
                    $max_per_user   = $product_discount_data->max_units_customers;
                    $special_offer  = $product_discount_data->special_offer_label;

                    if($max_per_user != 0)
                    {
                        $user_discount_uses_count = $this->CI->products_model->count_user_discount_uses($user_id, $session_id, $product_discount_data->id, $country_id);

                        $allowed_limit = $max_per_user - $user_discount_uses_count;
                        $limit = 1;


                    }

                    $discount_id = $product_discount_data->id;
                }

            }
        }

        return array(
                      $discount_price ,
                      $allowed_limit  ,
                      $limit          ,
                      $discount_id    ,
                      $daily          ,
                      $end_hour       ,
                      'special_offer'=>$special_offer
                    );
    }

    public function get_product_stock_per_country($product_id, $country_id)
    {
        return $this->CI->products_serials_model->get_per_country_product_serials_count($product_id, $country_id);
    }

    public function get_top_selling_products($lang_id, $country_id)
    {
        $products_data = $this->CI->orders_model->get_top10_selling_products($lang_id, $country_id);

        return $products_data;
    }



}
