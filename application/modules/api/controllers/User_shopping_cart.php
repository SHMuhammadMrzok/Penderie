<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class User_shopping_cart extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('user_shopping_cart_model');
        $this->load->model('general_model');

        $this->load->library('shopping_cart');
        $this->load->library('products_lib');
        $this->load->library('api_lib');

    }

    public function index()
    {
        $lang_id            = strip_tags($this->input->post('langId', TRUE));
        $userId             = strip_tags($this->input->post('userId', TRUE));
        $deviceId           = strip_tags($this->input->post('deviceId', TRUE));
        $country_id         = strip_tags($this->input->post('countryId', TRUE));
        $ip_address         = $this->input->ip_address();

        $email              = strip_tags($this->input->post('email', TRUE));
        $password           = strip_tags($this->input->post('password', TRUE));

        $productId          = strip_tags($this->input->post('productId', TRUE));
        $cartRowId          = strip_tags($this->input->post('cartRowId', TRUE));    // this field is used to check if product has optional fields when update quantity from shopping cart
        $quantity_sent      = intval($this->input->post('quantityOrdered', TRUE));
        $quantity_type      = strip_tags($this->input->post('quantityType', TRUE));
        $shoppingType       = strip_tags($this->input->post('shoppingType', TRUE));
        $chargingPrice      = strip_tags($this->input->post('chargingPrice', TRUE));

        $settings           = $this->general_model->get_settings();

        $output = array();

        if($this->ion_auth->login($email, $password))
        {
            $user_data = $this->ion_auth->user()->row();
            $this->api_lib->check_user_store_country_id($email, $password, $user_data->id, $country_id);
        }

        $this->shopping_cart->set_user_data($userId, $deviceId, $ip_address , $country_id ,$lang_id);

        $productId = intval($productId);


        if($shoppingType == 'product')
        {
            /**
             * QTY types
             * 0 -> reduce
             * 1 -> increase by 1 (+1)
             * 2 -> increase by qty
             */

             if($quantity_type == 1 || $quantity_type==2)
            {
                $is_available_products_per_user_customer_group = $this->shopping_cart->check_user_cart_products_per_customer_group();
            }
            elseif($quantity_type == 0)
            {
                $is_available_products_per_user_customer_group = true;
            }


            if(!($is_available_products_per_user_customer_group) )
            {
                $message = $this->general_model->get_lang_var_translation('max_products_per_order_reached', $lang_id);
                $output  = array(
                                   'message'   => $message,       // max products per order for user cusromer group
                                   'response'  => 0
                                );
            }
            else
            {
                $is_product_exist      = $this->shopping_cart->check_if_product_in_shopping_cart($productId);
                $product_details       = $this->products_model->get_product_row_details($productId, $lang_id, $country_id);
                $check_product_country = $this->products_model->check_product_country_exist($productId, $country_id);
                $currency              = $this->currency->get_country_currency_name($country_id, $lang_id);

                if(!$product_details)
                {
                    $message = $this->general_model->get_lang_var_translation('no_product_details', $lang_id);
                    $output  = array(
                                       'message'   => $message,       // no product details
                                       'response'  => 0
                                    );
                }
                elseif(!$check_product_country)
                {
                    $message = $this->general_model->get_lang_var_translation('product_not_exist_in_country', $lang_id);
                    $output  = array(
                                       'message'   => $message,       // no stock
                                       'response'  => 0
                                    );
                }
                else
                {
                    $product_price_data = $this->products_lib->get_product_price_data($product_details, $country_id, $userId, $deviceId);
                    $cart_data          = $this->shopping_cart->shopping_cart_data();

                    $quantity           = 0;
                    $product_qty_error  = false;

                    $product_optiona_fields_count = $this->products_model->count_product_optional_fields($productId);
                    $user_product_shopping_cart   = $this->products_model->check_user_product_optional_fields($cartRowId, $userId);


                    if($is_product_exist)
                    {
                        $cart_product_data = $this->shopping_cart->get_product_details($productId,$cartRowId);
                        $non_stock_product = 0;

                        if($product_details->quantity_per_serial == 0 && ($quantity_type == 1 || $quantity_type == 2) )
                        {
                            $non_stock_product = 1;

                            if($userId != 0)
                            {
                                $user_customer_group_data = $this->customer_groups_model->get_user_customer_group_data($userId);
                                $max_per_order            = $user_customer_group_data->product_limit_per_order;
                            }
                            else
                            {
                                $max_per_order = 100000;
                            }

                            $qty = $max_per_order - $cart_data->items_count;

                        }
                        else if($quantity_type == 0)
                        {
                            $qty = 1;
                        }
                        else
                        {
                            $min_stock   = $this->config->item('min_product_stock'); // Basic Code
                            /*******
                            ** Mrzok Edit & Comment ======>>>>>
                            ** here we need to check for user selcted options Quantities if exist for this $cartRowId
                            **/
                            if($user_product_shopping_cart){
                                /** Check selected options available quantities */
                                // get user selected options for this product => if exist options
                                $user_product_shopping_cart_options   = $this->products_model->get_user_optional_fields($cartRowId, $lang_id);

                                // Splite posted options to Optional array and selected options array
                                $selected_optionals_array   = array_values(array_column($user_product_shopping_cart_options, 'product_optional_field_id')); // array of the cart user selected product optionals ids
                                $selected_options_array     = array_values(array_column($user_product_shopping_cart_options,'product_optional_field_value')); // array of the cart user selected options values

                
                                // Check if the selected (Options of Product Optionals) count more than the minimum
                                $selected_options_stock_count   = $this->products_model->count_product_available_quantity($productId, $country_id , $selected_optionals_array , $selected_options_array);
                                $selected_options_available_qty = $selected_options_stock_count - $min_stock;

                                // calculat remaining quantity to Validate that selecterd options quantities is greater than existing quantity in cart before increment with 1
                                // $qty                            = $selected_options_available_qty - $cart_product_data->qty;
                                
                                /* Validate that system existing quantity for selecterd options is :
                                    * - great than minimum stock which is configured by SYSTEM SETTINGS 
                                    * - less than The requested quantity
                                    * */
                                if($selected_options_available_qty < 1 || $cart_product_data->qty >= $selected_options_stock_count)
                                {
                                    // no stock
                                    // exit of continuing the code .... stop adding the product to the cart & display error message
                                    $qty    = 0;
                                }
                                else { 
                                    // Available Quantity
                                    $qty    = $selected_options_available_qty;
                                }
                            }
                            //// Product don't have user selected options 
                            else {
                                // Start Basic Code
                                $stock_count = $this->products_model->count_product_available_quantity($productId, $country_id);
                                
                                $qty         = $stock_count - $min_stock;
                                // End Basic Code 
                            }
                        }
                        

                        if($qty <1)
                        {
                            $message = $this->general_model->get_lang_var_translation('no_stock', $lang_id);
                            $output  = array(
                                               'message'   => $message ,       // no stock
                                               'response'  => 0
                                            );
                        }
                        else
                        {
                            if($quantity_type == 1)
                            {
                                $new_qty       = 1;
                                $quantity_sent = $cart_product_data->qty + 1;
                            }
                            else if($quantity_type == 0)
                            {
                                $new_qty        = $cart_product_data->qty - 1;
                                $quantity_sent  = $new_qty;

                                if($quantity < 1)
                                {
                                    $product_qty_error = true;
                                }
                            }
                            else if($quantity_type == 2)
                            {
                                $new_qty = $quantity_sent;
                            }

                            if( ($quantity_sent <= $product_price_data[3] || $product_price_data[2] == 0 && !$product_qty_error) || $quantity_type == 0)
                            {
                                if($product_price_data['vat_type'] == 2) //exclusive
                                {
                                    $final_price = $product_price_data[1] + $product_price_data['vat_value'];
                                }
                                else
                                {
                                    $final_price = $product_price_data[1];
                                }

                                $data = array(
                                                'qty'             => $quantity_sent ,
                                                'discount'        => $product_price_data[5],
                                                'coupon_discount' => 0,
                                                'price'           => $product_price_data[0] ,//+ $cart_product_data->optional_fields_cost),
                                                'final_price'     => $final_price,
                                                'weight'          => $product_details->weight,
                                                'image'           => $product_details->image  ,
                                                'vat_value'       => $product_price_data['vat_value'],
                                                'vat_percent'     => $product_price_data['vat_percent'],
                                                'vat_type'        => $product_price_data['vat_type']
                                             );

                                if($this->shopping_cart->update($product_details->id, $data, $non_stock_product, $cartRowId))
                                {

                                    if($cart_data->coupon_discount != 0)
                                    {
                                        $this->shopping_cart->reset_cart_coupon();
                                    }

                                    $cart_data  = $this->shopping_cart->shopping_cart_data();
                                    $message    = $this->general_model->get_lang_var_translation('product_qty_updated', $lang_id);
                                    $total_cost = $cart_data->total_price + $cart_data->optional_fields_cost;

                                    $output  = array(
                                                       'message'        => $message,       // quantity updated
                                                       'response'       => 1,
                                                       'currency'       => $currency                       ,
                                                       'totalCost'      => "$total_cost",//$cart_data->total_price + $cart_data->optional_fields_cost        ,
                                                       'discount'       => $cart_data->discount            ,
                                                       'couponDiscount' => $cart_data->coupon_discount     ,
                                                       'finalTotalCost' => $cart_data->final_total_price   ,
                                                       'shipment'       => $cart_data->needs_shipping      ,
                                                       'vatPercent'     => $cart_data->vat_percent         ,
                                                       'vatValue'       => $cart_data->vat_value           ,
                                                    );
                                }
                                else
                                {

                                    $message = $this->general_model->get_lang_var_translation('no_stock', $lang_id);
                                    $output  = array(
                                                       'message'   => $message,       // quantity not updated
                                                       'response'  => 0
                                                    );
                                }
                            }
                            elseif($product_qty_error)
                            {
                                $qty_error_msg  = $this->general_model->get_lang_var_translation('shopping_cart_quantity_error', $lang_id);

                                $output = array(
                                                   'message'  => $qty_error_msg,   // quantity error
                                                   'response' => 0
                                               );
                            }
                            else
                            {
                                $message = $this->general_model->get_lang_var_translation('max_qty_per_user_discount_reached', $lang_id);
                                $output  = array(
                                                       'message'   => $message,       // max_per_discount reached
                                                       'response'  => 0
                                                    );
                            }
                        }

                    }
                    else
                    {
                        $qty_error = false;
                        if($product_details->quantity_per_serial == 1)
                        {
                            $stock_count = $this->products_model->count_product_available_quantity($productId, $country_id);
                            $min_stock   = $this->config->item('min_product_stock');
                            $qty         = $stock_count - $min_stock;

                            if($qty <1)
                            {
                                $qty_error = true;
                                $message   = $this->general_model->get_lang_var_translation('no_stock', $lang_id);
                                $output    = array(
                                                   'message'   => $message,       // no stock
                                                   'response'  => 0
                                                );
                            }

                        }

                        if(! $qty_error)
                        {
                            if($product_optiona_fields_count > 0 && !$user_product_shopping_cart)
                            {
                                $message = $this->general_model->get_lang_var_translation('optional_fields_required', $lang_id);
                                $output  = array(
                                                   'message'   => $message,
                                                   'response'  => 2          // optional field needed
                                                );
                            }
                            else
                            {

                                if($quantity_type == 1)  // add 1 to the current qty
                                {
                                    $last_qty    =  1;
                                }
                                else if($quantity_type == 2)   // update current qty to the sent qty
                                {
                                    $last_qty = $quantity_sent;
                                }

                                if($product_price_data['vat_type'] == 2) //exclusive
                                {
                                    $final_price = $product_price_data[1] + $product_price_data['vat_value'];
                                }
                                else
                                {
                                    $final_price = $product_price_data[1];
                                }

                                $data    = array(
                                                   'product_id'    => $product_details->id     ,
                                                   'store_id'      => $product_details->store_id,
                                                   'type'          => 'product'                ,
                                                   'cat_id'        => $product_details->cat_id ,
                                                   'qty'           => $last_qty                ,
                                                   'name'          => $product_details->title  ,
                                                   'price'         => $product_price_data[0]   ,
                                                   'final_price'   => $final_price             ,
                                                   'discount'      => $product_price_data[5]   ,
                                                   'image'         => $product_details->image  ,
                                                   'reward_points' => $product_details->reward_points   ,
                                                   'weight'        => $product_details->weight          ,
                                                   'vat_value'     => $product_price_data['vat_value']  ,
                                                   'vat_percent'   => $product_price_data['vat_percent'],
                                                   'vat_type'      => $product_price_data['vat_type']
                                                );

                                $this->shopping_cart->insert($data);
                                //check shopping cart coupon
                                if($cart_data->coupon_discount != 0)
                                {
                                    $this->shopping_cart->reset_cart_coupon();
                                }

                                $cart_data = $this->shopping_cart->shopping_cart_data();
                                $message = $this->general_model->get_lang_var_translation('product_added_to_cart_successfully', $lang_id);
                                $output  = array(
                                                   'message'        => $message,       // quantity updated
                                                   'response'       => 1,
                                                   'currency'       => $currency                       ,
                                                   'totalCost'      => $cart_data->total_price + $cart_data->optional_fields_cost        ,
                                                   'discount'       => $cart_data->discount            ,
                                                   'couponDiscount' => $cart_data->coupon_discount     ,
                                                   'finalTotalCost' => $cart_data->final_total_price   ,
                                                   'shipment'       => $cart_data->needs_shipping      ,
                                                   'vatPercent'     => $cart_data->vat_percent         ,
                                                   'vatValue'       => $cart_data->vat_value           ,
                                                );
                            }
                        }
                    }
                }
            }
        }
        elseif($shoppingType == 'recharg')
        {
            if($chargingPrice == '')
            {
                $message = $this->general_model->get_lang_var_translation('insert_charging_value', $lang_id);
                $output  = array(
                                   'message'   => $message,
                                   'response'  => 0
                                );
            }
            else
            {

                $recharge_data = array(
                                       'product_id'    => 0               ,
                                       'type'          => 'recharge_card' ,
                                       'cat_id'        => 0               ,
                                       'qty'           => 1               ,
                                       'name'          => $chargingPrice.' recharge card' ,
                                       'price'         => $chargingPrice  ,
                                       'final_price'   => $chargingPrice  ,
                                       'image'         => ''              ,
                                       'store_id'      => $settings->default_store_id,
                                       'checked'       => 1,
                                       'reward_points' => 0
                                     );


                if($this->shopping_cart->insert($recharge_data))
                {
                    $message = $this->general_model->get_lang_var_translation('card_inserted', $lang_id);
                    $output  = array(
                                       'message'   => $message,
                                       'response'  => 1
                                    );
                }
                else
                {
                    $message = $this->general_model->get_lang_var_translation('not_inserted', $lang_id);
                    $output  = array(
                                       'message'   => $message,
                                       'response'  => 0
                                    );
                }
            }
        }

        /**
         *  response
         * 0 => failed
         * 1 => success
         * 2 => optional fields needed
         *
        */
        $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));

    }


/************************************************************************/
}
