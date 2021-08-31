<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Get_user_shopping_cart extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('optional_fields/optional_fields_model');
        $this->load->model('products/products_serials_model');
        $this->load->model('products/products_model');
        $this->load->model('general_model');
        $this->load->model('global_model');

        $this->load->library('api_lib');
        $this->load->library('currency');
        $this->load->library('shopping_cart');
    }

    public function index()
    {
        $lang_id        = intval($this->input->post('langId', TRUE));
        $userId         = intval($this->input->post('userId', TRUE));
        $email          = strip_tags($this->input->post('email', TRUE));
        $password       = strip_tags($this->input->post('password', TRUE));
        $deviceId       = strip_tags($this->input->post('deviceId', TRUE));
        $ip_address     = $this->input->ip_address();
        $country_id     = intval($this->input->post('countryId', TRUE));
        $output         = array();
        $images_path    = $this->api_lib->get_images_path();

        if($this->ion_auth->login($email, $password))
        {
            $user_data = $this->ion_auth->user()->row();
            $this->api_lib->check_user_store_country_id($email, $password, $user_data->id, $country_id);
        }

        $this->shopping_cart->set_user_data($userId, $deviceId, $ip_address , $country_id ,$lang_id);

        $check_shopping_cart = $this->shopping_cart->check_if_cart_products_exist();

        if($check_shopping_cart)
        {

            /*************************************************************/

            $total_before      = 0;
            $stores_array      = array();
            //$contents          = $this->shopping_cart->contents();
            $cart_data         = $this->shopping_cart->shopping_cart_data();
            $coupon_discount   = $cart_data->coupon_discount;
            $user_id           = $cart_data->user_id;

            $cart_stores       = $this->shopping_cart->get_cart_stores($cart_data->id, $lang_id);
            $currency          = $this->currency->get_country_currency_name($country_id, $lang_id);

            if(count($cart_stores) != 0 && $cart_stores != '')
            {
            foreach($cart_stores as $store)
            {
                $cart_stores_products    = $this->shopping_cart->get_cart_stores_products($cart_data->id, $store->store_id);
                $products_array          = array();
                $product_optional_fields = array();
                $cart_items              = array();

                foreach($cart_stores_products as $content)
                {
                    if($content->product_id != 0)
                    {
                        $total_before += $content->price * $content->qty;
                        $product_translation_data   = $this->products_model->get_product_translation_data($content->product_id, $lang_id);
                        $check_product_country      = $this->products_model->check_product_country_exist($content->product_id, $country_id);
                        $count_user_optional_fields = $this->products_model->count_user_product_optional_fields($content->cart_product_id);

                        if($check_product_country)
                        {
                            $country_check = 1;
                        }
                        else
                        {
                            $country_check = 0;
                        }

                        $item_old_price       = '';
                        $item_old_total_price = '';
                        
                        // final price of item isn't the same as item price and it is less than item price itself to represewnt a discount
                        if($content->price != $content->final_price && $content->final_price < $content->price)
                        {
                            $item_old_price         = $content->price;
                            $item_old_total_price   = $content->price * $content->qty;
                        }

                        $item_name        = $product_translation_data->title;
                        $item_description = $product_translation_data->description;
                        $item_image       = $images_path.$content->image;
                        $image_path       = $images_path.$content->image;

                        $product_optional_fields = array();
                        if($count_user_optional_fields != 0)
                        {
                            $user_optional_fields = $this->products_model->get_user_optional_fields($content->cart_product_id, $lang_id);

                            foreach($user_optional_fields as $field)
                            {
                                if($field->has_options == 1)
                                {
                                    $option_options = $this->optional_fields_model->get_optional_field_options($field->optional_field_id, $lang_id);
                                    foreach($option_options as $option)
                                    {
                                        if($option->id == $field->product_optional_field_value)
                                        {
                                            $field->product_optional_field_value = $option->field_value;
                                            if($option->image != '')
                                            {
                                              $item_image = $images_path.$option->image;
                                            }
                                        }
                                    }
                                }

                                $product_optional_fields[] = array(
                                                                    'optionId'    => $field->label,
                                                                    'optionLabel' => $field->product_optional_field_value
                                                                  );
                            }
                        }


                    }
                     else
                    {
                        $item_name              = $this->general_model->get_lang_var_translation('recharge_card', $lang_id);
                        $country_check          = 1;
                        $item_old_price         = '';//$content->price
                        $item_description       = '';
                        $item_old_total_price   = '';
                        $item_image             = base_url().'assets/template/site/images/wallet.jpg';
                        $image_path             = base_url().'assets/template/site/images/wallet.jpg';
                    }

                    if($content->vat_type == 1)
                    {
                      //inclusive vat
                      $product_vat_message = $this->general_model->get_lang_var_translation('inclusive_vat', $lang_id);
                    }
                    else {
                      //exclusive vat
                      $product_vat_message = $this->general_model->get_lang_var_translation('exclusive_vat', $lang_id);
                    }

                    $cart_items[] = array(
                                            'cartRowId'         => $content->cart_product_id    ,
                                            'shoppingType'      => $content->type               ,
                                            'itemId'            => $content->product_id         ,
                                            'storeId'           => $content->store_id           ,
                                            'storeName'         => $store->store_name           ,
                                            'countryCheck'      => $currency                    ,
                                            'itemName'          => $item_name                   ,
                                            'itemOldPrice'      => $item_old_price              ,
                                            'itemOldTotalPrice' => $item_old_total_price        ,
                                            'itemDiscount'      => $content->discount           ,
                                            'itemPrice'         => $content->final_price + $content->optional_fields_cost       ,
                                            'itemTotalPrice'    => ($content->final_price + $content->optional_fields_cost) * $content->qty,
                                            'itemImage'         => $item_image              ,
                                            'itemDescription'   => $item_description        ,
                                            'shippingCost'      => '',//$content->shipping_cost  ,
                                            'quantityOrdered'   => $content->qty            ,
                                            'rewardPoints'      => $content->reward_points  ,
                                            'optionalFields'    => $product_optional_fields ,
                                            'vatPercent'        => $content->vat_percent    ,
                                            'vatValue'          => $content->vat_value      ,
                                            'vatMessage'        => $product_vat_message     ,
                                            'thumbnail'         => $images_path.$content->image
                                            //'imageBitMap'       => $image_code
                                         );

                }

               $cart_items_array[$store->id] = $cart_items;

               $stores_array[] = array(
                                        'storeId'   => $store->store_id,
                                        'storeName' => $store->store_name,
                                        'cartItems' => $cart_items
                                      );

            }
            }
            $is_wholesaler  = false;
            $show_coupon    = false;

            if($user_id != 0)
            {
                $settings                     = $this->global_model->get_config();
                $wholesaler_customer_group_id = $settings->wholesaler_customer_group_id;


                if($user_id != 0)
                {
                    if($this->ion_auth->login($email, $password))
                    {
                        $user_data = $this->user_model->get_row_data($user_id);

                        $user_customer_group = $user_data->customer_group_id;

                        if($user_customer_group == $wholesaler_customer_group_id)
                        {
                            $is_wholesaler  = true;
                        }
                    }

                }
            }

            if(!$is_wholesaler && $cart_data->coupon_discount == 0)
            {
                $show_coupon = true;
            }


            /*************************************************************/

            $total_cost = $cart_data->total_price + $cart_data->optional_fields_cost;

            $total = round($total_cost, 2);
            $discount = round($cart_data->discount, 2);
            $coupon_discount = round($cart_data->coupon_discount, 2);
            $final_total_price = round($cart_data->final_total_price_with_tax, 2);
            $shipping = round($cart_data->needs_shipping, 2);
            $vat_value = round($cart_data->vat_value, 2);

            $output = array(
                                'cartId'         => $cart_data->id                           ,
                                'stores'         => $stores_array                            ,
                                'currency'       => $currency                                ,
                                'totalCost'      => "$total"                                 ,
                                'discount'       => "$discount"                              ,
                                'couponDiscount' => "$coupon_discount"                       ,
                                'finalTotalCost' => "$final_total_price"                     ,
                                'cobonStatus'    => $show_coupon                             ,
                                'shipment'       => "$shipping"                              ,
                                'vatPercent'     => $cart_data->vat_percent                  ,
                                'vatValue'       => "$vat_value"                             ,
                           );
        }
        else
        {
            $output = array(
                               'message'  => $this->general_model->get_lang_var_translation('no_products_in_your_shopping_cart', $lang_id),
                               'response' => 0
                           );
        }


        $this->output->set_content_type('application/json')->set_output(json_encode($output));

    }

    public function activated_shipping_methods()
    {
        $lang_id        = intval($this->input->post('langId', TRUE));
        $userId         = intval(strip_tags($this->input->post('userId', TRUE)));
        $email          = strip_tags($this->input->post('email', TRUE));
        $password       = strip_tags($this->input->post('password', TRUE));
        $deviceId       = strip_tags($this->input->post('deviceId', TRUE));
        $ip_address     = $this->input->ip_address();
        $country_id     = intval(strip_tags($this->input->post('countryId', TRUE)));
        $output         = array();

        $this->shopping_cart->set_user_data($userId, $deviceId, $ip_address , $country_id ,$lang_id);

        $settigs = $this->general_model->get_settings();

        $active_array = array();

        if($settigs->home_delivery == 1)
        {
            $cart_data = $this->shopping_cart->shopping_cart_data();

            if($cart_data->final_total_price_with_tax >= $settigs->min_order_for_delivery)
            {
                $response = 1;
                $message  = '';
            }
            else
            {
                $response = 2;
                $message  = $this->general_model->get_lang_var_translation('min_order_delivery_note', $lang_id);
            }

            $home_delivery  = $this->general_model->get_lang_var_translation('home_delivery', $lang_id);
            $home_delivery_data = array(
                                        'id' => 1,
                                        'name' => $home_delivery,
                                        'response'  => $response,
                                        'message'   => $message
                                   );

            $active_array[] = $home_delivery_data;
        }

        if($settigs->recieve_from_branch == 1)
        {


            $recieve_from_branch      = $this->general_model->get_lang_var_translation('recieve_from_branch', $lang_id);
            $recieve_from_branch_data = array(
                                        'id'        => 2,
                                        'name'      => $recieve_from_branch,

                                   );

            $active_array[] = $recieve_from_branch_data;
        }

        if($settigs->shipping == 1)
        {
            $shipping      = $this->general_model->get_lang_var_translation('shipping', $lang_id);
            $shipping_data = array(
                                        'id' => 3,
                                        'name' => $shipping
                                   );

            $active_array[] = $shipping_data;
        }

        if($settigs->user_address == 1)
        {
            $shipping      = $this->general_model->get_lang_var_translation('user_address', $lang_id);
            $shipping_data = array(
                                        'id' => 4,
                                        'name' => $shipping
                                   );

            $active_array[] = $shipping_data;
        }

        /**
         * shipping methods ids
         * home delivery        = 1
         * shipping             = 2
         * recive from branch   = 3
        */



        if(count($active_array) != 0)
        {
            $output = $active_array;


        }
        else
        {
            $fail_message = $this->general_model->get_lang_var_translation('no_data', $lang_id);
            $output       = array(
                                    'message' => $fail_message,
                                    'response' => 0
                                );
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));

    }


/************************************************************************/
}
