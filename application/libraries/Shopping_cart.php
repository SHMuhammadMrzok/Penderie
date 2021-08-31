<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
/**
 *
 */
class Shopping_cart
{
    public $CI ;
    public $user_id;
    public $session_id;
    public $country_id;
    public $ip_address;
    public $cart_id;
    public $cart_data;
    public $total_price;
    public $final_price;
    public $secret_key;
    public $secret_iv;
    public $settings;

    public function __construct()
    {
        $this->CI = &get_instance();

        $this->CI->load->model('global_model');
        $this->CI->load->model('shopping_cart_model');
        $this->CI->load->model('users/users_model');
        $this->CI->load->model('users/countries_model');
        $this->CI->load->model('products/products_model');
        $this->CI->load->model('users/customer_groups_model');
        $this->CI->load->model('coupon_codes/coupon_codes_model');

        $this->CI->load->library('encryption');
        $this->CI->load->library('products_lib');

        $this->settings = $this->CI->global_model->get_config();
        
        
    }

    public function set_user_data($user_id, $session_id, $ip_address, $country_id, $lang_id)
    {
        $this->secret_key = 'dF32%^#FDB4545@#DSGE';
        $this->secret_iv  = '3$%$rger#%#dsdDSGWE4';

        /*if($user_id == 0)
        {
            $user_id = NULL;
        }*/

        $this->user_id    = $user_id;
        $this->session_id = $session_id;
        $this->ip_address = $ip_address;
        $this->country_id = $country_id;
        $this->lang_id    = $lang_id;
        $this->vat_type   = $this->settings->vat_type;



    }

    public function store_cookie_cart_id()
    {
        $encrypted_cart_id = $this->CI->encryption->encrypt($this->cart_id, $this->secret_key, $this->secret_iv);

        $cookie = array(
                'name'   => 'shopping_cart_id',
                'value'  => $encrypted_cart_id,
                'expire' => '31536000' //after 1 year
        );

        $this->CI->input->set_cookie($cookie);

        return $encrypted_cart_id;
    }

    public function get_cart_id_from_cookie()
    {
        if(isset($_COOKIE['shopping_cart_id']))
        {
            $encrypted_cart_id = $this->CI->input->cookie('shopping_cart_id');

            if($encrypted_cart_id)
            {
                $decrypted_cart_id = $this->CI->encryption->decrypt($encrypted_cart_id, $this->secret_key, $this->secret_iv);

                return intval($decrypted_cart_id);
            }
            else
            {
                return 0;
            }
        }
        else
        {
            return 0;
        }
    }

    public function get_guest_cart_id()
    {
        $cookie_cart_id = $this->get_cart_id_from_cookie();

        $cart_exist_in_db = $this->CI->shopping_cart_model->check_cart_exist($cookie_cart_id, array(0));

        if($cookie_cart_id > 0 && $cart_exist_in_db)
        {
            $cart_id = $cookie_cart_id;
        }
        else
        {
            // check cart exist for guest using session_id
            $cart_id = $this->CI->shopping_cart_model->get_guest_cart_id_by_session($this->session_id, $this->ip_address);

            if(!$cart_id)
            {
                $cart_id = 0;
            }

        }

        return $cart_id;
    }

    public function generate_new_cart()
    {
        $country_data = $this->CI->countries_model->get_row_data($this->country_id, $this->lang_id);

        $user_address_id = 0;
        $conditions_array = array(
            'user_id' => $this->user_id,
            'default_add' => 1
        );
        $default_address = $this->CI->users_model->get_result_data_where('user_addresses', 'row', $conditions_array);
        if(count($default_address) != 0)
        {
          $user_address_id = $default_address->id;
        }

        $cart_data  = array(
                            'ip_address'          => $this->ip_address,
                            'session_id'          => $this->session_id,
                            'unix_time'           => time(),
                            'last_activity'       => time(),
                            'user_id'             => $this->user_id,
                            'country_id'          => $this->country_id,
                            'currency_symbol'     => $country_data->currency_symbol,
                            'address_id'          => $user_address_id,
                            'vat_type'            => $this->vat_type,
                            'shipping_company_id' => $this->settings->default_shipping_company_id
                            //'point_value'     => $country_data->reward_points
                           );

        $this->CI->shopping_cart_model->insert_new_shopping_cart($cart_data);

        $this->cart_id = $this->CI->db->insert_id();

        //store cart id in cookies
        $this->store_cookie_cart_id();

        return $this->cart_id;
    }

    public function get_cart_id()
    {

        //if called before
        if($this->cart_id > 0)
        {
            return $this->cart_id;
        }
        else
        {
            // if logged in user
            if($this->user_id > 0)
            {
                $cart_id = $this->CI->shopping_cart_model->get_user_cart_id($this->user_id);

                if(!$cart_id)
                {
                    //try to get cart id from cookies if entered it when he is/was guest
                    $cookie_cart_id = $this->get_cart_id_from_cookie();

                    $cart_exist_in_db = $this->CI->shopping_cart_model->check_cart_exist($cookie_cart_id, array(0));

                    if($cart_exist_in_db)
                    {
                        $this->cart_id = $cookie_cart_id;
                        //update user id for cart (previously was guest but now is logged in)
                        $this->update_cart($this->cart_id, array('user_id' => $this->user_id));
                    }
                    else
                    {
                        //generate new cart and return it's id
                        $this->cart_id = $this->generate_new_cart();
                    }
                }
                else
                {
                    $this->cart_id = $cart_id;
                }
            }
            else //if guest
            {
                $cart_id = $this->get_guest_cart_id();

                if($cart_id != 0)
                {
                    $this->cart_id = $cart_id;
                }
                else
                {
                    //generate new cart and return it's id
                    $this->cart_id = $this->generate_new_cart();
                }
            }

            return $this->cart_id;
        }
    }

    public function update_cart($cart_id, $update_array = array(), $return=0)
    {
        $result = $this->CI->shopping_cart_model->update_shopping_cart($cart_id, $update_array);

        if($return == 1)
        {
            return $result;
        }

    }

    public function insert($data, $return_row_id=0)
    {
        //return_row_id = 0 => return true
        //return_row_id = 1 => return product_cart_row_id

        $cart_id = $this->get_cart_id();
        $product_data = array(
                                'cart_id'   => $cart_id ,
                                'unix_time' => time(),
                                'last_edit' => time()
                             );

        $insert_data = array_merge($product_data, $data);

        $this->CI->shopping_cart_model->insert_shopping_cart_products($insert_data);
        $cart_row_id = $this->CI->db->insert_id();

        $this->update_shopping_cart_items_count($cart_id);
        $this->update_cart_total_prices($cart_id);

        if($return_row_id == 1)
        {
            return $cart_row_id;
        }
        else
        {
            return true ;
        }
    }


    public function update($product_id, $updated_data, $stock_product=0 ,$cart_product_id=0)
    {
        $cart_id   = $this->get_cart_id();
        $cart_data = $this->shopping_cart_data();
        // $product_cart_data  = $this->get_product_details($product_id); // Basic Code 
        $product_cart_data  = $this->get_product_details($product_id,$cart_product_id); // Mrzok Edit

        if($product_cart_data->qty > $updated_data['qty'])
        {
            $qty = true;
        }
        else if($stock_product == 1)
        {
            if($this->user_id != 0)
            {
                $user_customer_group_data = $this->CI->customer_groups_model->get_user_customer_group_data($this->user_id);
                $max_per_order            = $user_customer_group_data->product_limit_per_order;
            }
            else
            {
                $max_per_order = 100000;
            }

            //$available_stock = $max_per_order - $cart_data->items_count;

            $available_stock    = $max_per_order - ($cart_data->items_count - $product_cart_data->qty);

           if($available_stock >= 1)
           {
             $qty = true;
           }
           else
           {
            $qty = false;
           }
        }
        else
        {
            $qty             = $this->CI->products_model->count_product_available_quantity($product_id, $this->country_id);
            $min_stock       = $this->CI->config->item('min_product_stock');
            $available_stock = $qty - $min_stock;

            if($available_stock >= $updated_data['qty'])
            {
                $qty = true;
            }
            else
            {
                $qty = false;
            }
        }

        if($qty && $updated_data['qty'] > 0)
        {
            $this->CI->shopping_cart_model->update_shopping_cart_product($cart_id, $product_id, $updated_data, $cart_product_id);

            $this->update_shopping_cart_items_count($cart_id);
            $this->update_cart_total_prices($cart_id);

            return true;
        }
        else
        {
            return false;
        }
    }

    public function update_shopping_cart_product($product_id, $updated_data)
    {
        $cart_id         = $this->get_cart_id();

        $qty             = $this->CI->products_model->count_product_available_quantity($product_id, $this->country_id);
        $min_stock       = $this->CI->config->item('min_product_stock');
        $available_stock = $qty - $min_stock;

        $this->CI->shopping_cart_model->update_shopping_cart_product($cart_id, $product_id, $updated_data);

        $this->update_shopping_cart_items_count($cart_id);
        $this->update_cart_total_prices($cart_id);

       return true;
    }

    public function update_shopping_cart($cart_id, $updated_data)
    {

        $this->CI->shopping_cart_model->update_shopping_cart($cart_id, $updated_data);
    }

    public function update_this_shopping_cart($updated_data)
    {
        $cart_id = $this->get_cart_id();
        $this->CI->shopping_cart_model->update_shopping_cart($cart_id, $updated_data);
    }

    public function update_cart_product_row($cart_row_id, $updated_data)
    {
        $this->CI->shopping_cart_model->update_cart_product($cart_row_id, $updated_data);
    }

    public function total()
    {
        $cart_id     = $this->get_cart_id();
        $total_price = $this->CI->shopping_cart_model->get_shopping_cart_field($cart_id, 'total_price');

        return $total_price;
    }

    public function final_total()
    {
        $cart_id     = $this->get_cart_id();
        $final_price = $this->CI->shopping_cart_model->get_shopping_cart_field($cart_id, 'final_total_price');

        return $final_price;
    }

    public function total_items()
    {
        $cart_id     = $this->get_cart_id();
        $total_items = $this->CI->shopping_cart_model->get_shopping_cart_field($cart_id, 'items_count');

        return $total_items;
    }

    public function contents()
    {
        $cart_id  = $this->get_cart_id();
        $contents = $this->CI->shopping_cart_model->get_cart_contents($cart_id);

        return $contents;
    }

    public function get_cart_contents($cart_id)
    {
        $contents = $this->CI->shopping_cart_model->get_cart_contents($cart_id);

        return $contents;
    }

    public function shopping_cart_data($cart_id=0)
    {
        if($cart_id == 0)
        {
            $cart_id = $this->get_cart_id();
        }
        $data    = $this->CI->shopping_cart_model->get_cart_data($cart_id);

        return $data;
    }


    public function destroy($cart_product_id)
    {
        $reset_msg       = 'false';
        $cart_product_id = intval($cart_product_id);
        $cart_id         = $this->get_cart_id();

        $this->CI->shopping_cart_model->delete_shopping_cart_product($cart_id, $cart_product_id);
        $products_in_cart_count = $this->CI->shopping_cart_model->get_shopping_cart_items_count($cart_id);

        //delete from coupon_codes_users_products table
        $cart_data = $this->shopping_cart_data();
        if($cart_data->coupon_discount > 0)
        {
            $reset_result_array = $this->reset_cart_coupon();

            if($reset_result_array[0] != 1)
            {
                $reset_msg = $reset_result_array[1];
            }
        }

        if($products_in_cart_count == 0)
        {
            $this->CI->shopping_cart_model->delete_shopping_cart($cart_id);
            $this->CI->shopping_cart_model->delete_cart_coupon_data($cart_id);
        }

        $cart_products = $this->CI->shopping_cart_model->get_shopping_cart_products($cart_id);

        if(count($cart_products) != 0 && $cart_products !='')
        {
            $this->update_shopping_cart_items_count($cart_id);
            $this->update_cart_total_prices($cart_id);
        }

       return array(true, $reset_msg);
    }

    public function delete($cart_id=0)
    {
        if($cart_id ==0)
        {
            $cart_id = $this->get_cart_id();
        }
        $this->CI->shopping_cart_model->delete_shopping_cart($cart_id);
        $this->CI->shopping_cart_model->delete_cart_coupon_data($cart_id);

        $this->cart_id = 0;
    }

    public function delete_cart_checked_products()
    {
        $cart_id = $this->get_cart_id();

        $this->CI->shopping_cart_model->delete_shopping_cart_checked_products($cart_id);

        $cart_products = $this->CI->shopping_cart_model->get_shopping_cart_products($cart_id);

        if(count($cart_products) == 0)
        {
            $this->CI->shopping_cart_model->delete_cart($cart_id);
            $this->CI->shopping_cart_model->delete_cart_coupon_data($cart_id);

            $this->cart_id = 0;
        }
    }

    public function update_shopping_cart_items_count($cart_id)
    {
        $cart_products = $this->CI->shopping_cart_model->get_shopping_cart_products($cart_id);

        $items_count   = 0;
        $total_weights = 0;

        foreach($cart_products as $product)
        {
            $items_count   += $product->qty;
            $total_weights += $product->weight * $product->qty;
        }

        $updated_data = array(
                                'items_count'  => $items_count,
                                'total_weight' => $total_weights
                             );

        $this->CI->shopping_cart_model->update_shopping_cart($cart_id, $updated_data);
    }

    public function get_shopping_cart_products()
    {
        $cart_id            = $this->get_cart_id();
        $cart_products_data = $this->CI->shopping_cart_model->get_shopping_cart_products($cart_id);

        return $cart_products_data;
    }

    public function update_cart_total_prices($cart_id)
    {
        $cart_data = $this->shopping_cart_data();

        if($cart_data)
        {
            $products_discount    = 0;
            $coupon_discount      = 0;
            $products_price       = 0;
            $products_shipping    = 0;
            $optional_fields_cost = 0;
            $total_points         = 0;
            $cart_total_vat       = 0;
            $shipping             = array();

            $cart_products     = $this->CI->shopping_cart_model->get_shopping_cart_products($cart_id);
            $cart_total_coupon = $this->check_if_cart_coupon_is_total($cart_id);
            $vat_percent       = $this->settings->vat_percent;

            if(count($cart_products) != 0)
            {
                foreach($cart_products as $product)
                {
                    $product_data = $this->CI->products_model->get_products_row($product->product_id);

                    if($product->vat_type == 2)//exclusive
                    {
                        $products_price += (($product->price + $product->vat_value)  * $product->qty);
                    }
                    else
                    {
                        $products_price += ($product->price * $product->qty);
                    }
                    $products_discount    += ($product->discount * $product->qty);
                    //$products_price       += ($product->price * $product->qty);
                    $coupon_discount      += ($product->coupon_discount * $product->qty);
                    //$total_points         += ($product->points_cost * $product->qty);
                    $optional_fields_cost += ($product->optional_fields_cost * $product->qty);

                    // update products vat
                    /*$product_vat          = ($product->final_price * $vat_percent) / 100;
                    $product_vat_data = array(
                                                'vat_percent' => $vat_percent,
                                                'vat_value'   => $product_vat
                                            );
                    $this->update_cart_product_row($product->id, $product_vat_data);
                    */

                    //CART TOTAL VATS
                    //if($product->vat_type == 2)
                    //{
                      $cart_total_vat += $product->vat_value * $product->qty;
                    //}

                    if($product->product_id != 0)
                    {
                        if($product_data->shipping == 1 )
                        {
                            $shipping[] = 1;
                        }
                        else
                        {
                            $shipping[] = 0;
                        }
                    }
                    else
                    {
                        $shipping[] = 0;
                    }
                }
            }

            if($cart_total_coupon)
            {
                $coupon_discount = $cart_data->coupon_discount;
            }

            $final_price = $products_price - $products_discount - $coupon_discount + $optional_fields_cost;
            $final_price_with_tax = $final_price + $cart_data->tax + $cart_data->wrapping_cost + $cart_data->shipping_cost ;

            if(in_array(1, $shipping))
            {
                $products_shipping = 1;
            }

            //$cart_total_vat = ($final_price_with_tax * $vat_percent) / 100;

            //$cart_point_value = $this->CI->countries_model->get_reward_points($this->country_id);

            $price_data['total_price']                  = $products_price;
            $price_data['final_total_price']            = $final_price;
            $price_data['final_total_price_with_tax']   = $final_price_with_tax;
            $price_data['discount']                     = $products_discount;
            $price_data['coupon_discount']              = $coupon_discount;
            $price_data['needs_shipping']               = $products_shipping;
            $price_data['vat_percent']                  = $vat_percent;
            $price_data['vat_value']                    = $cart_total_vat;
            //$price_data['total_points_cost']            = $total_points;
            //$price_data['point_value']                  = $cart_point_value;
            $price_data['optional_fields_cost']         = $optional_fields_cost;

            $this->CI->shopping_cart_model->update_shopping_cart($cart_id, $price_data);
        }
    }

    public function check_if_cart_coupon_is_total($cart_id)
    {
        $return_result    = false;
        $cart_coupon_data = $this->CI->shopping_cart_model->get_cart_coupon_data($cart_id);

        if($cart_coupon_data!='' && count($cart_coupon_data) != 0)
        {
            if($cart_coupon_data->product_or_category == 'total')
            {
                $return_result = true;
            }
        }

        return $return_result;
    }

    public function check_if_product_in_shopping_cart($product_id)
    {
        $cart_id       = $this->get_cart_id();
        $product_count = $this->CI->shopping_cart_model->get_product_count_in_shopping_cart($cart_id, $product_id);

        if($product_count > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function get_product_details($product_id, $cart_product_id=0)
    {
        $cart_id      = $this->get_cart_id();
        // // $product_data = $this->CI->shopping_cart_model->get_product_data($cart_id, $product_id); // Basic Code
        $product_data = $this->CI->shopping_cart_model->get_product_data($cart_id, $product_id,$cart_product_id); // Mrzok Edit 1 

        if($product_data)
        {
            return $product_data;
        }
        else
        {
            return false;
        }
    }

    public function get_product_with_selected_options_from_shopping_cart($lang_id, $product_id, $selected_optionals_array = array(), $selected_options_array = array() )
    {
        /**
         * Mrzok Edit : 4/2021
            This function is created to Check if the user selected options of a product is already exist in user's cart or not 
            and if it is exist return this cart product row to continue the flow
         */
        $cart_id        = $this->get_cart_id();
        $cart_products  = $this->CI->shopping_cart_model->get_same_product_array_from_cart($cart_id, $product_id); 

        $exist_cart_product = array();

        foreach ($cart_products as $product) {
            # code...
            $cart_product_exist_options  = $this->CI->products_model->get_user_optional_fields($product->cart_product_id, $lang_id); 

            // Splite cart product exist options to Exist Optional array and Exist Options array
            $exist_optionals_array   = array_values(array_column($cart_product_exist_options, 'product_optional_field_id'));    // array of the cart product optionals ids
            $exist_options_array     = array_values(array_column($cart_product_exist_options, 'product_optional_field_value')); // array of the cart product options values

            // Check if the cart product have the same options which requested by the user
            if ($exist_options_array === $selected_options_array ) 
            {
                // add cart row object to exist product aray
                $exist_cart_product = $product;
            }
        }

        if(!empty($exist_cart_product))
        {
            return $exist_cart_product;
        }
        else
        {
            return false;
        }
    }

    public function check_if_cart_products_exist()
    {
        $cart_id             = $this->get_cart_id();

        $cart_products_count = $this->CI->shopping_cart_model->check_cart_products_count($cart_id);

        if($cart_products_count > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function convert_shopping_cart_user_id($visitor_cart_id, $user_cart_id)
    {



        if($user_cart_id != 0)
        {
            // delete user shopping cart data
            $this->CI->shopping_cart_model->delete_shopping_cart($user_cart_id);
            $this->CI->shopping_cart_model->delete_cart_coupon_data($user_cart_id);
        }

        // update visitor cart id with user id
        $shopping_cart_data = array(
                                        'user_id'         => $this->user_id ,
                                        'coupon_discount' => 0
                                    );

        $this->update_cart($visitor_cart_id, $shopping_cart_data);

        $shopping_cart_products_data['coupon_discount'] = 0;
        $this->CI->shopping_cart_model->update_cart_products($visitor_cart_id, $shopping_cart_products_data);

        $contents = $this->CI->shopping_cart_model->get_cart_contents($visitor_cart_id);

        // update shopping cart products
        foreach($contents as $content)
        {
            if($content->product_id != 0)
            {
                $shopping_cart_products_array = array();

                $product_details    = $this->CI->products_model->get_product_row_details($content->product_id, 1, $this->country_id);
                $product_price_data = $this->CI->products_lib->get_product_price_data($product_details, $this->country_id);

                $shopping_cart_products_array['price']        = $product_price_data[0];
                $shopping_cart_products_array['final_price']  = $product_price_data[1];
                $shopping_cart_products_array['discount']     = $product_price_data[0] - $product_price_data[1];

                $this->update_shopping_cart_product($content->product_id, $shopping_cart_products_array);
            }
        }



        //delete coupon data if user is wholesaller
        /*if($this->CI->user_bootstrap->is_wholesaller())
        {
            $this->CI->shopping_cart_model->delete_cart_coupon_data($visitor_cart_id);
        }
        else
        */{
            $this->cart_id = $visitor_cart_id;
            $this->reset_cart_coupon();
        }

    }

    public function count_charge_cards_in_cart()
    {
        $cart_id = $this->get_cart_id();
        $count   = $this->CI->shopping_cart_model->charge_cards_in_cart_count($cart_id);

        return $count;
    }

    public function get_cart_product_data($product_id,$cart_product_id=0)
    {
        $cart_id      = $this->CI->shopping_cart_model->get_cart_id($this->user_id, $this->session_id, $this->ip_address);
        // $product_data = $this->CI->shopping_cart_model->get_product_data($product_id, $cart_id); // Basic Code
        $product_data = $this->CI->shopping_cart_model->get_product_data($cart_id, $product_id,$cart_product_id ); // Mrzok Edit

        return $product_data;
    }

    public function reset_cart_coupon()
    {
        $cart_id            = $this->get_cart_id();
        $cart_contents      = $this->contents();
        $cart_coupon_data   = $this->CI->shopping_cart_model->get_cart_coupon_data($cart_id);

        $product_coupon_discount_data = array();

        $this->CI->shopping_cart_model->delete_cart_coupon_data($cart_id);
        $product_coupon_discount_data['coupon_discount'] = 0;

        //coupon discount for all cart products to be 0
        foreach($cart_contents as $content)
        {
            $product_coupon_discount_data['final_price'] = $content->price - $content->discount;
            $this->CI->shopping_cart_model->update_shopping_cart_product($cart_id, $content->product_id, $product_coupon_discount_data);
        }

        //coupon discount for cart = 0
        $cart_discount_data['coupon_discount'] = 0;
        $this->CI->shopping_cart_model->update_shopping_cart($cart_id, $cart_discount_data);

        $this->update_cart_total_prices($cart_id);

        //delete coupon use data
        $this->CI->shopping_cart_model->delete_cart_coupon_data($cart_id);
        //$reset_result_array = $this->coupon_discount($cart_coupon_data->code);

        if(count($cart_coupon_data) !=0 && ! empty($cart_coupon_data) )
        {
            $reset_result_array = $this->coupon_discount($cart_coupon_data->code);
        }
        else
        {
            $reset_result_array = true;
        }

        return $reset_result_array;

    }

    public function coupon_discount($coupon_code, $user_id=0)
    {
        if($user_id !=0)
        {
            $this->user_id=$user_id;
        }

        $cart_id       = $this->get_cart_id();
        $cart_contents = $this->contents();
        $cart_data     = $this->shopping_cart_data();
        $cart_id       = $cart_data->id;
        $final_price   = $cart_data->final_total_price;
        $country_id    = $this->country_id;
        $session_id    = $this->session_id;
        $user_id       = $this->user_id;
        $current_date  = time();

        $coupon_data   = $this->CI->coupon_codes_model->get_coupon_data($coupon_code);

        if($coupon_data)
        {
            if($coupon_data->country_id != $country_id)
            {
                return array(0, lang('this_coupon_cant_be_used_in_this_country'));
                // 'this coupon cant be used in this country';
            }

            if($current_date > $coupon_data->end_unix_time )
            {
                return array(0, lang('this_coupon_dates_ends'));
                //'this_coupon_dates_end';
            }

            if($current_date < $coupon_data->start_unix_time)
            {
                return array(0, lang('this_coupon_dates_not_started_yet'));
                // 'this_coupon_dates_not_started_yet';
            }

            if($coupon_data->uses_per_coupon != 0)
            {
                $coupon_uses_count = $this->CI->coupon_codes_model->get_coupon_uses_conditioned_count($coupon_data->id);

                if($coupon_uses_count >= $coupon_data->uses_per_coupon)
                {
                    return array(0, lang('maximum_uses_per_coupon_reached'));
                    // 'maximum_uses_per_coupon_reached';
                }
            }

            if($coupon_data->uses_per_customer != 0)
            {
                if($user_id == 0)
                {
                    $user_conditions['session_id'] = $session_id;
                }

                $user_conditions = array('user_id' => $user_id);

                $coupon_count_per_user = $this->CI->coupon_codes_model->get_coupon_uses_conditioned_count($coupon_data->id, $user_conditions);

                if($coupon_count_per_user >= $coupon_data->uses_per_customer)
                {
                    return array(0, lang('you_have_reached_maximum_uses_of_coupon'));
                    // 'you_have_reached_maximum_uses_of_coupon';
                }
            }

            if($final_price < $coupon_data->min_amount)
            {
                return array(0, lang('total_price_of_cart_is_not_enough_to_use_this_coupon'));
                // 'total price for cart is not enough to use this coupon';
            }

            $total_discount = $this->_check_coupon($cart_contents, $coupon_data, $cart_id);

            if($total_discount == 0)
            {
                return array(0, lang('no_discount_on_these_products'));
                // 'this coupon cant be applied on these products';
            }

            return array(1, lang('coupon_success'));
        }
        else
        {
            return array(0, lang('coupon_code_not_existing'));
            // 'coupon_code_not_existing';
        }
    }

    private function _check_coupon($cart_content, $coupon_data, $cart_id)
    {
        $cart_total_discount    = 0;
        $coupon_products_exist  = false;
        $cart_coupon_products   = array();

        $user_id    = $this->user_id;
        $session_id = $this->session_id;

        $cart_data  = $this->CI->shopping_cart_model->get_cart_data($cart_id);

        //coupon type : total
        if($coupon_data->product_or_category == 'total')
        {
            # if total coupon will be applied on final total price of cart,
            # so the qty of the products will be consered equals 1

            $price = $cart_data->total_price - $cart_data->discount + $cart_data->optional_fields_cost;

            $total_price_after_discount = $this->_calculate_amount($coupon_data->discount_type, $price, 1, $coupon_data->discount);
            $cart_total_discount        = $price - $total_price_after_discount;

            if($this->settings->vat_type == 2)
            {
              $vat = $total_price_after_discount * $this->settings->vat_percent / 100;
              $total_price_after_discount = $total_price_after_discount + $vat;
            }

            // Mrzok Edit 6/6/2021 ... Calculate final_total_price_with_tax
            $final_total_price_with_tax = $total_price_after_discount + $cart_data->tax + $cart_data->wrapping_cost + $cart_data->shipping_cost ;
            // End Edit

            $price_data = array(
                                   'coupon_discount'   => $cart_total_discount,
                                   'vat_value'         => $vat,
                                   'final_total_price' => $total_price_after_discount,
                                   'final_total_price_with_tax' => $final_total_price_with_tax // Mrzok Edit 6/6/2021 , to gurantee to update this field when this function is called through api
                               );

            $this->CI->shopping_cart_model->update_shopping_cart($cart_id, $price_data);

            $coupon_products_exist            = true;

        }
        else
        {
            foreach($cart_content as $content)
            {
                $product_price_after_discount = 0;
                $price = ($content->final_price + $content->optional_fields_cost) * $content->qty;

                if($coupon_data->product_or_category == 'category')     //coupon type : category
                {
                    $coupon_cats_ids = $this->CI->coupon_codes_model->get_coupon_cats_ids($coupon_data->id);

                    if(in_array($content->cat_id, $coupon_cats_ids))
                    {
                        $product_price_after_discount = $this->_calculate_amount($coupon_data->discount_type, $price, $content->qty, $coupon_data->discount);

                        $discount_on_product              = $price - $product_price_after_discount;

                        $content->{'cat_coupon'}          = true;
                        $content->{'discount_on_product'} = $discount_on_product;
                        $cart_coupon_products[]           = $content;
                        $one_product_discount             = $discount_on_product / $content->qty;
                        $one_product_final_price          = $product_price_after_discount / $content->qty;

                        $shopping_cart_product_data       = array(
                                                                   'coupon_discount' => $one_product_discount  ,
                                                                   'final_price'     => $one_product_final_price
                                                                 );

                        $this->CI->shopping_cart_model->update_shopping_cart_product($content->cart_id, $content->product_id, $shopping_cart_product_data);

                        $coupon_products_exist = true;
                    }

                }
                elseif($coupon_data->product_or_category == 'product')   //coupon type : product
                {
                    $coupon_products = $this->CI->coupon_codes_model->get_coupon_products_ids($coupon_data->id);

                    if(in_array($content->product_id, $coupon_products))
                    {
                        $product_price_after_discount     = $this->_calculate_amount($coupon_data->discount_type, $price, $content->qty, $coupon_data->discount);

                        $discount_on_product              = $price - $product_price_after_discount;

                        $content->{'product_coupon'}      = true;
                        $content->{'discount_on_product'} = $discount_on_product;
                        $cart_coupon_products[]           = $content;
                        $one_product_discount             = $discount_on_product / $content->qty;
                        $one_product_final_price          = $product_price_after_discount / $content->qty;

                        $shopping_cart_product_data = array(
                                                               'coupon_discount' => $one_product_discount  ,
                                                               'final_price'     => $one_product_final_price
                                                           );

                        $this->CI->shopping_cart_model->update_shopping_cart_product($content->cart_id, $content->product_id, $shopping_cart_product_data);

                        $coupon_products_exist = true;
                    }

                }

                $cart_total_discount += ($price - $product_price_after_discount);
            }

            $this->update_cart_total_prices($cart_id);
        }

        if(!$coupon_products_exist)
        {
            return lang('no_discount_on_these_products');
        }



        // insert in coupon_codes_users
        $coupon_code_user_data = array(
                                        'user_id'          =>  $user_id                       ,
                                        'session_id'       =>  $session_id                    ,
                                        'coupon_id'        =>  $coupon_data->id               ,
                                        'discount_type'    =>  $coupon_data->discount_type    ,
                                        'coupon_discount'  =>  $coupon_data->discount         ,
                                        'total_discount'   =>  $cart_total_discount           ,
                                       	'cart_id'          =>  $cart_id                       ,
                                        'unix_time'        =>  time()
                                      );

        $this->CI->coupon_codes_model->insert_coupon_uses_data($coupon_code_user_data);

        $coupon_code_user_id  = $this->CI->db->insert_id();

        if($coupon_data->product_or_category != 'total')
        {
            foreach($cart_coupon_products as $discount_product)
            {
                $product_data = array(
                                        'coupon_codes_users_id' => $coupon_code_user_id          ,
                                        'product_id'            => $discount_product->product_id ,
                                        'category_id'           => $discount_product->cat_id     ,
                                        'coupon_id'             => $coupon_data->id
                                     );

                if(isset($discount_product->cat_coupon))
                {
                    $product_data['cat_applied'] = 1;
                }

                if(isset($discount_product->product_coupon))
                {
                    $product_data['product_applied'] = 1;
                }

                $this->CI->coupon_codes_model->insert_coupon_uses_products($product_data);
            }
        }



        return true;
    }

    private function _calculate_amount($type, $product_price, $product_qyt, $discount_amount)
    {
        if($type == 'percentage')
        {
            $product_price_after_discount = $product_price * ((100 - $discount_amount) / 100);
        }
        elseif($type == 'amount')
        {
            $product_price_after_discount = $product_price - ($discount_amount * $product_qyt);
        }

        return $product_price_after_discount;
    }

    public function check_user_cart_products_per_customer_group()
    {
        $cart_id = $this->get_cart_id();
        if($this->user_id != 0)
        {
            $cart_data        = $this->shopping_cart_data();
            //$products_count = $this->CI->shopping_cart_model->count_cart_products($cart_id);
            $cart_items_count = $cart_data->items_count;

            $user_customer_group_data = $this->CI->customer_groups_model->get_user_customer_group_data($this->user_id);

            if(count($user_customer_group_data)  != 0)
            {
              if($user_customer_group_data->product_limit_per_order > $cart_items_count || $user_customer_group_data->product_limit_per_order == 0)
              {
                  return true;
              }
              else
              {
                  return false;
              }
            }
            else {
              return true;
            }
        }
        else
        {
            return true;
        }
    }

    public function check_cart_products_quantities($new_qties= array())
    {
        $status_array = array();
        $contents   = $this->contents();
        $country_id = $this->country_id;
        $user_id    = $this->user_id;

        if(count($new_qties) == 0)
        {
            $new_qties = array();

            foreach ($contents as $content)
            {
                //$new_qties[$content->product_id] = $content->qty; // Basic Code
                
                /*
                ** Mrzok Edit
                ** $content->cart_product_id has been added to array key , to handle the case of existing two rows with differrent ids
                ** on the shopping cart for the same product but with different options selected
                **/
                $new_qties[$content->product_id.'-'.$content->cart_product_id] = $content->qty;
            }
        }

        $out_of_amount              = array();
        $new_quantities_total_count = array_sum($new_qties);
        
        //foreach($new_qties as $product_id=>$qty) // Basic Code
        foreach($new_qties as $product_refrence=>$qty) // Mrzok Edit
        {
            /*** Mrzok Edit */
            $productId_cartRowId    = explode('-', $product_refrence);
            $product_id             = $productId_cartRowId[0];
            $cartRowId              = $productId_cartRowId[1];
            /*** End Edit */

            if($product_id == 0)
            {
                $status = '1';
                // $out_of_amount[$product_id] = 'false';       // Basic Code
                $out_of_amount[$product_refrence] = 'false';    // Mrzok Edit
            }
            else
            {

                if($qty <=0 )
                {
                    $status = 0;//lang('shopping_cart_quanitity_error');
                    // $out_of_amount[$product_id] = 'error';       // Basic Code
                    $out_of_amount[$product_refrence] = 'error';    // Mrzok Edit
                }
                else
                {
                    $product_details = $this->CI->products_model->get_product_row_details($product_id, $this->lang_id, $this->country_id);

                    if($product_details->quantity_per_serial == 0)
                    {
                        $status = '1';
                        // $out_of_amount[$product_id] = 'false'; // Basic Code
                        $out_of_amount[$product_refrence] = 'false'; // Mrzok Edit
                    }
                    else
                    {
                        $check_product_country = $this->CI->products_model->check_product_country_exist($product_id, $country_id);

                        if($check_product_country)
                        {

                            $stock_count = $this->CI->products_model->count_product_available_quantity($product_id, $country_id);
                            $min_stock   = $this->CI->config->item('min_product_stock');

                            $product_qty = $stock_count - $min_stock;

                            if($product_qty >= $qty)  // check stock
                            {
                                // Product Options qty validating flag
                                $valid_product_options_qty = true;

                                // check if ther product has selected options in user_products_optional_fields database table
                                $user_product_shopping_cart_has_options   = $this->CI->products_model->check_user_product_optional_fields($cartRowId, $user_id);

                                if ($user_product_shopping_cart_has_options){
                                    // get user selected options for this product => if exist options
                                    $user_product_shopping_cart_options   = $this->CI->products_model->get_user_optional_fields($cartRowId, $this->lang_id);

                                    // Splite posted options to Optional array and selected options array
                                    $selected_optionals_array   = array_values(array_column($user_product_shopping_cart_options, 'product_optional_field_id')); // array of the cart user selected product optionals ids
                                    $selected_options_array     = array_values(array_column($user_product_shopping_cart_options,'product_optional_field_value')); // array of the cart user selected options values

                                    // Check if the selected (Options of Product Optionals) count more than the minimum
                                    $selected_options_stock_count   = $this->CI->products_model->count_product_available_quantity($product_id, $country_id , $selected_optionals_array , $selected_options_array);
                                    $selected_options_available_qty = $selected_options_stock_count - $min_stock;
                                    /* Validate that system existing quantity for selecterd options is :
                                    * - great than minimum stock which is configured by SYSTEM SETTINGS 
                                    * - less than The requested quantity
                                    * */

                                    if($selected_options_available_qty < 1 || $qty > $selected_options_stock_count)
                                    {
                                        $valid_product_options_qty = false;
                                        // $status = '0';
                                        // // $out_of_amount[$product_id] = 'true'; // Basic Code
                                        // $out_of_amount[$product_refrence] = 'true'; // Mrzok Edit

                                        // continue;
                                    }
                                }

                                // $out_of_amount[$product_id] = 'false'; // Basic Code
                                $out_of_amount[$product_refrence] = 'false'; // Mrzok Edit

                                $product_details    = $this->CI->products_model->get_product_row_details($product_id, $this->lang_id, $this->country_id);
                                $product_price_data = $this->CI->products_lib->get_product_price_data($product_details, $this->country_id, $this->user_id, $this->session_id);
    
                                if(!$valid_product_options_qty)
                                {
                                    $status = '0';
                                    // $out_of_amount[$product_id] = 'true'; // Basic Code
                                    $out_of_amount[$product_refrence] = 'true'; // Mrzok Edit
                                }
                                elseif($new_quantities_total_count <= $product_price_data[7] || $product_price_data[7] == 0)  // check available products count per customer group
                                {
                                    if($qty <= $product_price_data[3] || $product_price_data[2] == 0)
                                    {
                                        $data = array('qty' => $qty);

                                        $data = array(
                                                        'qty'             => $qty ,
                                                        'discount'        => $product_price_data[5],
                                                        'price'           => $product_price_data[0],
                                                        'final_price'     => $product_price_data[1]
                                                     );

                                        $this->update($product_id, $data);

                                        $cart_data = $this->shopping_cart_data();

                                        if($cart_data->coupon_discount != 0)
                                        {
                                            $this->reset_cart_coupon();
                                        }

                                        $status = '1';
                                        // $out_of_amount[$product_id] = 'false'; // Basic Code
                                        $out_of_amount[$product_refrence] = 'false'; // Mrzok Edit
                                    }
                                    else
                                    {
                                        $status = '2';   // Max per user is reached
                                        // $out_of_amount[$product_id] = 'true'; // Basic Code
                                        $out_of_amount[$product_refrence] = 'true'; // Mrzok Edit
                                    }
                                }
                                else
                                {
                                    $status = '4';   // Max per customer group
                                    // $out_of_amount[$product_id] = 'true'; // Basic Code
                                    $out_of_amount[$product_refrence] = 'true'; // Mrzok Edit
                                }

                            }
                            else
                            {
                                $status = '0';
                                // $out_of_amount[$product_id] = 'true'; // Basic Code
                                $out_of_amount[$product_refrence] = 'true'; // Mrzok Edit
                            }
                        }
                        else
                        {
                            $status = '3';
                            // $out_of_amount[$product_id] = 'true'; // Basic Code
                            $out_of_amount[$product_refrence] = 'true'; // Mrzok Edit
                        }
                    }
                }
            }

            // $status_array[$product_id] = $status; // Basic Code
            $status_array[$product_refrence] = $status; // Mrzok Edit
        
        }

        $contents = $this->contents();

        $new_price_data = array();

        foreach($contents as $content)
        {
            $field_price      = $content->price * $content->qty;
            $total_price      = $this->total();

            $new_price_data[] = array(
                                        'field_price' => $field_price,
                                        'total_price' => $total_price,
                                        'product_id'  => $content->product_id,
                                        'status'      => $status ,
                                        // 'out_of_amount' => $out_of_amount[$content->product_id],// Basic Code
                                        'out_of_amount' => $out_of_amount[$product_refrence],
                                     );

        }

        return $status_array;
        //echo json_encode($new_price_data);
    }

    public function check_cart_shipping_cost()
    {
        $this->CI->load->model('shipping/companies_model');
        $contents = $this->contents();
        $shipping = 0;

        foreach($contents as $content)
        {
            if($content->product_id != 0)
            {
                $product_data = $this->CI->products_model->get_products_row($content->product_id);
                if($product_data->shipping == 1)
                {
                    $shipping = 1;
                }
            }
        }

        if($shipping == 0)
        {
            $cart_updated_data = array(
                                'shipping_cost'       => 0 ,
                                'shipping_company_id' => 0 ,
                                'shipping_country_id' => 0 ,
                                'shipping_address'    => ''
                              );

            $this->update_cart($this->cart_id, $cart_updated_data);
        }
        else
        {
            
            $cart_data              = $this->shopping_cart_data();
            $shipping_company_id    = $cart_data->shipping_company_id;

            if($shipping_company_id != 0 )//&& $cart_data->shipping_type==3)
            {
                $shipping_company_data  = $this->CI->companies_model->get_company_row($shipping_company_id);

                if($shipping_company_data->type == 1 || $shipping_company_data->type == 3 )
                {
                    if($shipping_company_data->cost == 0)
                    {
                        $updated_data = array(
                                                'shipping_cost'  => 0
                                             );

                        $this->update_cart($this->cart_id, $updated_data);
                        $this->update_cart_total_prices($this->cart_id);
                    }
                    else
                    {
                        $shipping_company_id = $cart_data->shipping_company_id;

                        //$shipping_company_data  = $this->CI->companies_model->get_company_row($shipping_company_id);

                        $cost = $this->CI->currency->convert_from_default_currency($shipping_company_data->cost, $this->country_id);

                        if($shipping_company_data->type == 1)
                        {
                          $total_shipping_cost = $cost * $cart_data->total_weight ;
                        }
                        else {
                          $product_optional_fields  = 1;
                        //   $cart_items_count         = $cart_data->items_count * $product_optional_fields_count;// Basic Code
                          $cart_items_count         = $cart_data->items_count; // Mrzok Edit
                          $total_shipping_cost      = $cost * $cart_items_count;
                        }

                        $updated_data = array(
                                                //'shipping_company_id' => $shipping_company_id,
                                                'shipping_cost'       => $total_shipping_cost
                                             );

                        $this->update_cart($this->cart_id, $updated_data);
                        $this->update_cart_total_prices($this->cart_id);
                    }
                }
                elseif($shipping_company_data->type == 2)
                {
                    $cost = $this->calculate_shiiping_cost_equation($cart_data->total_weight, $shipping_company_data->intial_cost, $shipping_company_data->intial_kgs, $shipping_company_data->each_kg_cost);
                    $cost = $this->CI->currency->convert_from_default_currency($cost, $this->country_id);

                    $updated_data = array(
                                            'shipping_cost' => $cost
                                         );

                    $this->update_cart($this->cart_id, $updated_data);
                    $this->update_cart_total_prices($this->cart_id);
                }

            }
            else
            {
                $updated_data = array(
                                        'shipping_cost'  => 0
                                     );

                $this->update_cart($this->cart_id, $updated_data);
                $this->update_cart_total_prices($this->cart_id);
            }
        }
    }

    public function calculate_shiiping_cost_equation($total_weight, $intial_cost, $intial_kgs, $each_kg_cost)
    {
      $first_kgs_cost = $intial_cost;
      if($total_weight != 0)
      {
          $rest_kgs_cost  = 0;
          if($intial_kgs < $total_weight)
          {
            $rest_kgs       = $total_weight - $intial_kgs;
            $rest_kgs_cost  = $rest_kgs * $each_kg_cost;
          }
          $final_cost = $first_kgs_cost + $rest_kgs_cost;
      }
      else
      {
          //$first_kgs_cost = 0;
          $final_cost = $first_kgs_cost;
      }

      return $final_cost;
    }

    public function get_available_shipping_methods($lang_id)
    {
        $active_array = array();

        if( $this->CI->config->item('home_delivery') == 1)
        {
            $method_id          = 1;
            $home_delivery      = $this->CI->shopping_cart_model->get_shipping_method($method_id, $lang_id);
            $home_delivery_data = (object)array(
                                        'id'    => $method_id,
                                        'name'  => $home_delivery->name
                                   );
            $active_array[] = $home_delivery_data;
        }

        if( $this->CI->config->item('recieve_from_branch') == 1)
        {
            $method_id                  = 2;
            $recieve_from_branch        = $this->CI->shopping_cart_model->get_shipping_method($method_id, $lang_id);
            $recieve_from_branch_data   = (object)array(
                                                'id'    => $method_id,
                                                'name'  => $recieve_from_branch->name
                                                );

            $active_array[] = $recieve_from_branch_data;
        }

        if( $this->CI->config->item('shipping') == 1)
        {
            $method_id     = 3;
            $shipping      = $this->CI->shopping_cart_model->get_shipping_method($method_id, $lang_id);
            $shipping_data = (object)array(
                                    'id'    => $method_id,
                                    'name'  => $shipping->name
                                   );
            $active_array[] = $shipping_data;
        }

        if( $this->CI->config->item('user_address') == 1 && $this->user_id != 0)
        {
            $method_id     = 4;
            $shipping      = $this->CI->shopping_cart_model->get_shipping_method($method_id, $lang_id);
            $shipping_data = (object)array(
                                    'id'    => $method_id,
                                    'name'  => $shipping->name
                                   );
            $active_array[] = $shipping_data;
        }

        return $active_array;


    }

    public function get_cart_stores($cart_id, $lang_id)
    {
        $stores = $this->CI->shopping_cart_model->get_cart_stores($cart_id, $lang_id);

        return $stores;
    }

    public function get_cart_stores_products($cart_id, $store_id)
    {
        $cart_stores_products = $this->CI->shopping_cart_model->get_cart_stores_products($cart_id, $store_id);

        return $cart_stores_products;
    }

    public function get_cart_checked_products($cart_id, $store_id)
    {
        $cart_checked_products = $this->CI->shopping_cart_model->get_cart_checked_products($cart_id, $store_id);

        return $cart_checked_products;
    }

    public function get_cart_products_translation($shopping_cart_id, $lang_id)
    {
        return $this->CI->shopping_cart_model->get_cart_products_translation($shopping_cart_id, $lang_id);
    }

    public function count_cart_checked_stores($cart_id)
    {
        return $this->CI->shopping_cart_model->count_cart_checked_stores($cart_id);
    }

    public function update_cart_product_optional_vat_data($product_id, $optional_fields_cost,$cart_product_id=0)
    {
        $cart_id = $this->get_cart_id();
        // $product_data = $this->get_product_details($product_id); // Basic Code
        $product_data = $this->get_product_details($product_id,$cart_product_id); // Mrzok Edit

        $vat = ($optional_fields_cost * $product_data->vat_percent) / 100;
        $this->CI->shopping_cart_model->update_cart_product_optional_vat_data($cart_id, $product_id, $vat, $optional_fields_cost);

        //$this->update_shopping_cart_items_count($cart_id);
        $this->update_cart_total_prices($cart_id);
    }

    public function check_product_in_cart($product_id)
    {
        $cart_id = $this->get_cart_id();
        return $this->CI->shopping_cart_model->check_product_in_cart($product_id, $cart_id);
    }




   /***************EOF**********************/

}
