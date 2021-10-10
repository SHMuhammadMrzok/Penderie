<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
/**
 *
 */
class Cart_orders
{
    public $CI ;
    public $settings;
    public $lang_id;

    public $status = 1;
    public $data   = array();

    public function __construct()
    {
        $this->CI = &get_instance();

        //$this->CI->load->helper('security');

        $this->CI->load->library('notifications');
        $this->CI->load->library('encryption');
        $this->CI->load->library('products_lib');
        $this->CI->load->library('shopping_cart');
        $this->CI->load->library('payment_gateways/payfort');
        $this->CI->load->library('payment_gateways/paypal');
        $this->CI->load->library('payment_gateways/cashu');
        $this->CI->load->library('serials_lib'); // Mrzok Edit

        $this->CI->load->model('global_model');
        $this->CI->load->model('api/general_model');
        $this->CI->load->model('orders_model');
        $this->CI->load->model('users/user_model');
        $this->CI->load->model('users/cities_model');
        $this->CI->load->model('orders/order_status_model');
        $this->CI->load->model('branches/branches_model');
        $this->CI->load->model('products/products_model');
        $this->CI->load->model('currencies/currency_model');
        $this->CI->load->model('products/products_serials_model');
        $this->CI->load->model('coupon_codes/coupon_codes_model');
        $this->CI->load->model('payment_options/user_balance_model');
        $this->CI->load->model('payment_options/payment_methods_model');


        // Get settings table data
        $this->settings = $this->CI->global_model->get_config();
        $this->lang_id = $this->settings->default_lang;
    }

    public function create_order_from_cart($cart_id)
    {

        $cart_data            = $this->CI->shopping_cart->shopping_cart_data($cart_id);
        //$cart_data = (array)$cart_data;
        
        $user_data            = $this->CI->user_model->get_row_data($cart_data->user_id);
        $lang_id              = $this->lang_id;//$cart_data->lang_id != 0 ? $cart_data->lang_id : 2 ;//intval($this->input->post('langId', TRUE));
        $user_id              = $cart_data->user_id;
        $email                = $user_data->email;
        $name                 = $user_data->first_name;
        $phone                = $user_data->phone;
        //$password             = strip_tags($this->input->post('password', TRUE));
        $country_id           = $cart_data->country_id;
        $agent                = $cart_data->agent;//strip_tags($this->input->post('agent', TRUE));
        $gift_msg             = $cart_data->gift_msg;//strip_tags($this->input->post('gift_msg', True));

        $notes                = $cart_data->user_notes;//strip_tags($this->input->post('userNotes', TRUE));
        $payment_method_id    = $cart_data->payment_option_id;//intval($this->input->post('paymentMethodId', TRUE));
        $bank_id              = 0;//$cart_data->bank_id;
        $voucher              = '';
        $branch_id = 0;
        $delivery_lng   = '';
        $delivery_lat   = '';

        $deviceId             = $cart_data->session_id;
        $ip_address           = $this->CI->input->ip_address();

        $this->CI->shopping_cart->set_user_data($user_id, $deviceId, $ip_address , $country_id ,$lang_id);

        //$bank_id            = 0;
        $is_first_order     = 0;
        $first_order_status = 0;

        $msg                      = '';
        $form                     = '';
        $order_bank_name          = '';
        $order_bank_number        = '';
        $first_order_error_msg    = '';
        $voucher                  = '';
        $user_bank_account_name   = '';
        $user_bank_account_number = '';


        $is_wholesaler       = false;
        $order_data          = array();
        $output              = array();
        $country_check_array = array();
        $stock_check_array   = array();
        //$related_orders_ids  = array();

        $cart_contents  = $this->CI->shopping_cart->contents();
        $min_stock      = $this->settings->min_product_stock;

        $qty_status_array = $this->CI->shopping_cart->check_cart_products_quantities();

        /**
         * 0 => no_stock
         * 2 => max_qty_per_user_discount_reached
         * 3 => product_not_exist_in_country
         * 4 => max_products_per_order_reached
        */

        /*if(in_array(0, $qty_status_array))
        {
            $error_msg      = $this->CI->general_model->get_lang_var_translation('no_stock', $lang_id);
            $order_error    = true;
            $output   = array(
                               'response' => 0,
                               'message'  => $error_msg
                             );
        }
        elseif(in_array(2, $qty_status_array))
        {
            $error_msg      = $this->CI->general_model->get_lang_var_translation('max_qty_per_user_discount_reached', $lang_id);
            $order_error    = true;
            $output   = array(
                               'response' => 0,
                               'message'  => $error_msg
                             );
        }
        elseif(in_array(3, $qty_status_array))
        {
            $error_msg      = $this->CI->general_model->get_lang_var_translation('product_not_exist_in_country', $lang_id);
            $order_error    = true;
            $output   = array(
                               'response' => 0,
                               'message'  => $error_msg
                             );
        }
        elseif(in_array(4, $qty_status_array))
        {
            $error_msg      = $this->CI->general_model->get_lang_var_translation('max_products_per_order_reached', $lang_id);
            $order_error    = true;
            $output   = array(
                               'response' => 0,
                               'message'  => $error_msg
                             );
        }
        else*/
        //{
            $wholesaler_customer_group_id = $this->settings->wholesaler_customer_group_id;

            $user_customer_group = $user_data->customer_group_id;

            if($user_customer_group == $wholesaler_customer_group_id)
            {
                $is_wholesaler  = true;
            }

            $insertion_fail_message = $this->CI->general_model->get_lang_var_translation('not_inserted', $lang_id);

            //if($cart_data->final_total_price_with_tax == 0)


                if($user_id != 0)
                {
                    $start_time = time();
                    $end_time   = $start_time - (60 * 60 * 24);

                    $user_orders_count        = $this->CI->orders_model->count_user_orders_per_day($start_time, $end_time, $user_id);
                    $user_customer_group_data = $this->CI->customer_groups_model->get_user_customer_group_data($user_id);

                    if($user_customer_group_data->max_orders_per_day > $user_orders_count || $user_customer_group_data->max_orders_per_day == 0)
                    {

                        $secret_key = $this->CI->config->item('new_encryption_key');
                        $secret_iv  = $user_id;

                        $method_data      = $this->CI->order_status_model->get_payment_data($payment_method_id);
                        $order_status_id  = $method_data->order_status_id;

                        $insert_order = true;

                        $template_payment_method = $this->CI->payment_methods_model->get_payment_method_name($payment_method_id, $lang_id);
                        /*if($payment_method_id != 10)
                        {
                            $order_status_id         = 8;
                        }*/


                        }

                        if($insert_order)
                        {
                            $currency_symbol = $cart_data->currency_symbol;

                            $cart_stores        = $this->CI->shopping_cart->get_cart_stores($cart_data->id, $lang_id);
                            $cart_stores_count  = count($cart_stores);
                            $related_orders_ids = array();


                            if($user_id != 0)
                            {
                                $username      = $user_data->first_name . ' ' . $user_data->last_name;
                                $user_email    = $user_data->email;
                                $emails[]      = $user_data->email;
                                $mobile_number = $user_data->phone;
                            }
                            else
                            {
                                $username      = $name;
                                $emails[]      = $email;
                                $user_email    = $email;
                                $mobile_number = $phone;
                            }


                            foreach($cart_stores as $store)
                            {

                                $cart_stores_products = $this->CI->shopping_cart->get_cart_checked_products($cart_data->id, $store->store_id);
                                
                                $order_tax            = $cart_data->tax / $cart_stores_count;
                                $order_shipping_cost  = $cart_data->shipping_cost / $cart_stores_count;
                                $order_shipping_cost  = round($order_shipping_cost, 2);
                                $order_wrapping_cost  = $cart_data->wrapping_cost / $cart_stores_count;
                                $order_wrapping_cost  = round($order_wrapping_cost, 2);
                                $order_only_wrapping_cost  = $cart_data->wrapping_only_cost / $cart_stores_count;
                                $order_only_wrapping_cost  = round($order_only_wrapping_cost, 2);
                                $order_only_ribbon_cost    = $cart_data->ribbon_only_cost / $cart_stores_count;
                                $order_only_ribbon_cost    = round($order_only_ribbon_cost, 2);
                                $order_only_box_cost       = $cart_data->box_only_cost / $cart_stores_count;
                                $order_only_box_cost       = round($order_only_box_cost, 2);


                                $order_total_coupons  = $cart_data->coupon_discount / $cart_stores_count;
                                $order_total_coupons  = round($order_total_coupons, 2);

                                $cart_products_result = $this->calculate_order_products_prices($cart_stores_products, $order_tax, $order_wrapping_cost, $order_shipping_cost, $order_total_coupons);


                                /**
                                 * $result[0] = $products_discount
                                 * $result[1] = $coupon_discount
                                 * $result[2] = $final_price
                                 * $result[3] = $final_price_with_tax
                                 *
                                 */

                                 $products_discount     = $cart_products_result[0];
                                 $coupon_discount       = $cart_products_result[1];
                                 $final_price           = $cart_products_result[2];
                                 $final_price_with_tax  = $cart_products_result[3];


                                //insert order data
                                 $order_data      = array(
                                                            'user_id'             => $user_id                               ,
                                                            'store_id'            => $store->store_id                       ,
                                                            'cart_id'             => $cart_data->id,
                                                            'agent'               => $_SERVER['HTTP_USER_AGENT']            ,
                                                            'payment_method_id'   => $payment_method_id                     ,
                                                            'bank_id'             => $bank_id                               ,
                                                            //'bank_account_name'   => $order_bank_name                       ,
                                                            //'bank_account_number' => $order_bank_number                     ,
                                                            'voucher'             => $voucher                               ,
                                                            'order_status_id'     => $order_status_id                       ,
                                                            'currency_symbol'     => $cart_data->currency_symbol            ,
                                                            'country_id'          => $cart_data->country_id                 ,
                                                            
                                                            /**related to each order**/
                                                            'items_count'         => count($cart_stores_products)           ,
                                                            'total'               => $final_price                           ,
                                                            'discount'            => $cart_products_result[0]               ,
                                                            'coupon_discount'     => $cart_products_result[1]               ,
                                                            'vat_value'           => $cart_products_result['vat_value']     ,
                                                            'tax'                 => $order_tax                             ,
                                                            'shipping_cost'       => $order_shipping_cost                   ,
                                                            'final_total'         => $cart_products_result[3]               ,
                                                            'rest_amount'         => $cart_products_result[3]               ,
                                                            'optional_fields_cost' => $cart_products_result[4]               ,
                                                            'wrapping_cost'       => $order_wrapping_cost                   ,
                                                            'wrapping_only_cost'  => $order_only_wrapping_cost              ,
                                                            'ribbon_only_cost'    => $order_only_wrapping_cost                ,
                                                            'wrapping_only_cost'  => $order_only_wrapping_cost              ,
                                                            /*****/

                                                            'auto_cancel'         => 1                                      ,
                                                            'needs_shipping'      => $cart_data->needs_shipping             ,
                                                            'shipping_company_id' => $cart_data->shipping_company_id        ,
                                                            'shipping_country_id' => $cart_data->shipping_country_id        ,
                                                            'shipping_city'       => $cart_data->shipping_city              ,
                                                            'shipping_district'   => $cart_data->shipping_district          ,
                                                            'shipping_address'    => $cart_data->shipping_address           ,
                                                            'shipping_name'       => $cart_data->shipping_name              ,
                                                            'shipping_phone'      => $cart_data->shipping_phone             ,
                                                            'shipping_type'       => $cart_data->shipping_type              ,
                                                            'shipping_lat'        => $delivery_lat                          ,
                                                            'shipping_lng'        => $delivery_lng                          ,
                                                            'branch_id'           => $branch_id                             ,
                                                            'address_id'          => $cart_data->address_id                 ,
                                                            'notes'               => $notes                                 ,
                                                            'send_as_gift'        => $cart_data->send_as_gift               ,
                                                            'wrapping_id'         => $cart_data->wrapping_id                ,
                                                            'ribbon_id'           => $cart_data->ribbon_id                  ,
                                                            'box_id'              => $cart_data->box_id                     ,

                                                            'gift_msg'            => $gift_msg , //strip_tags($this->CI->input->post('gift_msg', True)),
                                                            'unix_time'           => time()                                 ,
                                                            'day'                 => date('d')                              ,
                                                            'month'               => date('m')                              ,
                                                            'year'                => date('Y')
                                                          );

                                if(!$this->CI->orders_model->insert_order($order_data))
                                {
                                    $this->status = 0;

                                    $output = array(
                                                       'response' => 1,
                                                       'message'  => $insertion_fail_message
                                                   );
                                }
                                else
                                {
                                    $order_id   = $this->CI->db->insert_id();
                                    $related_orders_ids[] = $order_id;

                                    //insert order products
                                    $this->_insert_order_products($order_id, $country_id, $cart_stores_products, $lang_id);

                                    if($this->status == 1)
                                    {
                                        $user_balance = $this->CI->encryption->decrypt($user_data->user_balance, $secret_key, $secret_iv);

                                        // insert recharge cards
                                        $this->_insert_charge_cards($cart_id, $order_id, $country_id, $payment_method_id, $secret_key, $secret_iv, $user_id, $user_balance);

                                        if($this->status == 1)
                                        {
                                            //generate products serials
                                            $this->_generate_order_serials($order_id, $lang_id, $cart_data->country_id, $agent);

                                            if($this->status == 1)
                                            {
                                                $user_points = $this->CI->encryption->decrypt($user_data->user_points, $secret_key, $secret_iv);

                                                /// insert order , create notification , Add Affiliate
                                                $this->_insert_order_log($lang_id, $order_id, $order_status_id, $secret_key, $secret_iv, $payment_method_id, $template_payment_method, $user_points);

                                                if($this->status == 1)
                                                {
                                                    //send notification
                                                    $products_names = '';
                                                    $status         = $this->CI->order_status_model->get_status_translation_name($order_status_id, $lang_id);
                                                    $order_products = $this->CI->orders_model->get_order_products($order_id, $lang_id);

                                                    foreach($order_products as $product)
                                                    {
                                                        $products_names .= $product->title." , ";
                                                    }


                                                    if($order_status_id != 1)
                                                    {
                                                        //send notification
                                                        $email_msg      = '';
                                                        $sms_msg        = '';

                                                        foreach($order_products as $product)
                                                        {

                                                            $email_msg = '<div style="width:100%; display:block; overflow:hidden; overflow:hidden;">
                                                                            <table cellpadding="0" border="0" width="100%" style="text-align:center; font-size:14px;">
                                                                        	<tr style="background:#e1f0f8; font-size:14px;">
                                                                                <td>'.lang('thumbnail').'</td>
                                                                                <td>'.lang('product').'</td>
                                                                                <td>'.lang('quantity').'</td>
                                                                                <td>'.lang('price').'</td>
                                                                                <td>'.lang('total_price').'</td>
                                                                            </tr>';
                                                            $sms_msg  = '';

                                                            foreach($order_products as $product)
                                                            {
                                                                if($product->product_id != 0)
                                                                {
                                                                    $product_data   = $this->CI->products_model->get_row_data($product->product_id, $lang_id);
                                                                    //$product_price  = $this->CI->orders_model->get_order_product_data($product->product_id, $order_id);
                                                                    $product_name   = $product_data->title;
                                                                    $img_path       = base_url().'assets/uploads/products/'.$product_data->image;

                                                                    $email_msg .= '<tr>
                                                                                    	<td><img src="'.base_url().'assets/uploads/products/'.$product_data->image.'" width="50" height="50" style=" display:block; margin:5px auto;" alt="'.$product_name.'"/></td>
                                                                                        <td>'.$product_name.'</td>
                                                                                        <td>'.$product->qty.'</td>
                                                                                        <td>'.$product->final_price.' '.$currency_symbol.'</td>
                                                                                        <td>'.$product->final_price * $product->qty.' '.$currency_symbol.'</td>
                                                                                   </tr>';

                                                                    $sms_msg   .= lang('product').': '.$product_name.'--';
                                                                }
                                                                else
                                                                {
                                                                    $secret_key       = $this->CI->config->item('new_encryption_key');
                                                                    $secret_iv        = $user_data->id;
                                                                    $enc_user_balance = $user_data->user_balance;
                                                                    $user_balance     = $this->CI->encryption->decrypt($enc_user_balance, $secret_key, $secret_iv);

                                                                    $email_msg .= '<tr><td></td><td>'.lang('recharge_card').' </td><td> '.$product->final_price.'</td><td>'.lang('current_balance').' </td><td> '.$user_balance.' '.$currency_symbol.' </td></tr>';
                                                                    $sms_msg   .= lang('recharge_card').' : '.$product->final_price.' '.$currency_symbol.'  '.lang('current_balance').$user_balance.' '.$currency_symbol;
                                                                }
                                                            }

                                                            $email_msg .= '<tr>
                                                                            <td colspan="3"></td>
                                                                            <td><span>'.lang('final_total').'</span></td>
                                                                            <td><span>'.$cart_data->final_total_price_with_tax.' '.$currency_symbol.'</span></td>
                                                                           </tr>';

                                                            $email_msg .= '</table></div>';

                                                        }

                                                        $template_data = array(
                                                                                'username'              => $username                ,
                                                                                'user_email'            => $user_email              ,
                                                                                'user_phone'            => $mobile_number           ,
                                                                                'products'              => $products_names          ,
                                                                                'payment_method'        => $template_payment_method ,
                                                                                'order_details_email'   => $email_msg               ,
                                                                                'order_details_sms'     => $sms_msg                 ,
                                                                                'status'                => $status,
                                                                                'order_time'            => date('Y/m/d H:i', time()),
                                                                                'order_id'              => $order_id,
                                                                                'logo_path'             => base_url().'assets/template/site/img/logo.png',
                                                                                'user_order_link'       => base_url()."orders/order/view_order_details/".$order_id
                                                                              );


                                                        $this->CI->notifications->create_notification('new_order_added', $template_data, $emails, $mobile_number);
                                                    }
                                                    
                                                    


                                                    // check coupon
                                                    $cart_coupons = $this->CI->coupon_codes_model->get_cart_coupons_count($cart_id);
                                                    if($cart_coupons > 0)
                                                    {
                                                        $coupon_data = array(
                                                                                'cart_id'  => 0,
                                                                                'order_id' => $order_id
                                                                            );
                                                        $this->CI->coupon_codes_model->update_coupon_codes_using_cart_id($cart_id, $coupon_data);
                                                    }

                                                    /*************--------------------------------------**********/


                                                }
                                                else
                                                {
                                                    $output = array(
                                                                       'response' => 0,
                                                                       'message'  => $insertion_fail_message
                                                                   );
                                                }
                                            }
                                            else
                                            {
                                                $output = array(
                                                                   'response' => 0,
                                                                   'message'  => $insertion_fail_message
                                                               );
                                            }
                                        }
                                        else
                                        {
                                            $output = array(
                                                               'response' => 0,
                                                               'message'  => $insertion_fail_message
                                                           );
                                        }
                                    }
                                    else
                                    {
                                        $output = array(
                                                           'response' => 0,
                                                           'message'  => $insertion_fail_message
                                                       );
                                    }
                                }
                            }

                                $max_orders_number = $this->CI->orders_model->get_orders_max_number()->orders_number;
                                $new_orders_number = $max_orders_number + 1;

                                $updated_data = array(
                                                    'related_orders_ids' => json_encode($related_orders_ids),
                                                    'orders_number'      => $new_orders_number
                                                 );
                                $this->CI->orders_model->updated_orders_related_orders($related_orders_ids, $updated_data);


                                $this->CI->shopping_cart->delete($cart_id);

                                $final_total = $cart_data->final_total_price_with_tax;

                                $url = '';

                              /*  if($payment_method_id == 4 || $payment_method_id == 5 || $payment_method_id == 6 || $payment_method_id == 8  || $payment_method_id ==13 )
                                {
                                    $url = $this->_generate_payment_form($payment_method_id, $order_id, $final_total, $cart_data->currency_symbol, $products_names, $lang_id, $user_id);
                                }
*/
                                $success_message = $this->CI->general_model->get_lang_var_translation('order_inserted_successfully', $lang_id);

                                $output = array(
                                                   'response'   => 1,
                                                   'message'    => $success_message,
                                                   'paymentURL' => $url,
                                                   'orderId'    => $order_id,
                                                   'cartId'     => ""
                                               );
                            
                            ///////////////////////////////////////////////////////////////////

                        }
                    }
                    else
                    {
                        //$fail_message = $this->CI->general_model->get_lang_var_translation('max_orders_per_day_reached', $lang_id);
                        $fail_message = $this->CI->general_model->get_lang_var_translation('error_while_insert_order', $lang_id);

                        $output = array(
                                           'response' => 0,
                                           'message'  => $fail_message
                                       );
                    }

                //}


            /*else
            {
                $fail_message = $this->general_model->get_lang_var_translation('no_products_in_your_shopping_cart', $lang_id);

                $output = array(
                                   'response' => 0,
                                   'message'  => $fail_message
                               );
            }
            */

        //$this->CI->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));
        return $output;

    }

    private function _insert_order_products($order_id, $country_id, $cart_stores_products, $lang_id)
    {
        foreach($cart_stores_products as $content)
        {
            if($content->product_id != 0)
            {
                $reward_points  = $this->CI->products_model->get_reward_points($content->product_id, $country_id);
                $product_data   = $this->CI->products_model->get_product_country_data($content->product_id, $country_id);

                if($product_data->quantity_per_serial == 1)
                {
                    $product_cost = $product_data->average_cost;
                }
                else
                {
                    $product_cost = $product_data->cost;
                }

                $order_product_data = array(
                                               'order_id'           => $order_id                        ,
                                               'store_id'           => $content->store_id               ,
                                               'type'               => 'product'                        ,
                                               'product_id'         => $content->product_id             ,
                                               'cat_id'             => $content->cat_id                 ,
                                               'purchased_cost'     => $product_cost * $content->qty    ,
                                               'qty'                => $content->qty                    ,
                                               'price'              => $content->price                  ,
                                               'final_price'        => $content->final_price            ,
                                               'discount'           => $content->discount               ,
                                               'coupon_discount'    => $content->coupon_discount        ,
                                               'vat_value'          => $content->vat_value              ,
                                               'vat_percent'        => $content->vat_percent            ,
                                               'reward_points'      => $reward_points * $content->qty   ,
                                               'unix_time'          => time()                           ,
                                               'vat_type'          => $content->vat_type                , // Mrzok Edit : add the same vate type was exist in cart product table to order product table
                                           );

                $order_product_id = $this->CI->orders_model->insert_order_products($order_product_data);
                if(!$order_product_id)
                {
                    $this->status = 0;
                }
                else
                {
                    $user_optional_field_updated_data = array(
                                                                'order_products_id' => $order_product_id,
                                                                'order_id'          => $order_id
                                                             );

                    $this->update_user_optional_fields_products($content->cart_id, $content->id, $user_optional_field_updated_data);



                }

                $product_quantity     = $this->CI->products_model->count_product_available_quantity($content->product_id, $country_id);
                $new_product_quantity = $product_quantity - $content->qty;
                $updated_amount       = array('product_quantity' => $new_product_quantity);

                $this->CI->products_model->update_product_country_amount($updated_amount, $content->product_id, $country_id);

                $min_stock            = $this->settings->min_product_stock;
                $available_qty        = $product_quantity - $min_stock;
                $new_product_quantity = $available_qty - $content->qty;


                if($new_product_quantity == 0)
                {
                    $product_name  = $this->CI->products_model->get_product_name($content->product_id, $lang_id);
                    $emails[]      = $this->settings->email;
                    $mobile_number = $this->settings->mobile;
                    $template_data = array('product_name' => $product_name);

                    $this->CI->notifications->create_notification('product_quantity_less_than_threshold', $template_data, $emails, $mobile_number);
                }
            }
        }
    }

    private function update_user_optional_fields_products($cart_id, $cart_product_id, $updated_data)
    {
        $this->CI->products_model->update_product_optional_fields_data($cart_id, $cart_product_id, $updated_data);
    }


    private function _insert_charge_cards($cart_id, $order_id, $country_id, $payment_method_id, $secret_key, $secret_iv, $user_id, $user_balance)
    {
        $recharge_cards = $this->CI->shopping_cart_model->get_recharge_cards_data($cart_id);
        $user_data      = $this->CI->user_model->get_row_data($user_id);

        $currency_data  = $this->CI->currency_model->get_country_currency_result($user_data->store_country_id);

        if($recharge_cards)
        {
            foreach($recharge_cards as $card)
            {
                $order_product_data = array(
                                               'order_id'           => $order_id,
                                               'type'               => 'recharge_card',
                                               'product_id'         => 0,
                                               'cat_id'             => 0,
                                               'qty'                => 1,
                                               'price'              => $card->price,
                                               'final_price'        => $card->final_price,
                                               'unix_time'          => time()
                                           );

                $this->CI->orders_model->insert_order_products($order_product_data);



                $log_data = array(
                                    'user_id'           => $user_id,
                                    'payment_method_id' => $payment_method_id,
                                    'amount'            => $card->price,
                                    'balance'           => $user_balance,
                                    'balance_status_id' => 3,  //request to add balace
                                    'ip_address'        => $this->input->ip_address(),
                                    'unix_time'         => time()
                                 );

                if(!$this->CI->user_balance_model->insert_balance_log($log_data))
                {
                    $this->status = 0;
                }
            }
        }
    }


    /*******************************************************/
    /* Generate order serials and assign them to the order
    /*******************************************************/

    private function _generate_order_serials($order_id, $display_lang_id, $country_id, $agent)
    {
        $order_data     = $this->CI->orders_model->get_order($order_id); // Mrzok Edit
        $order_products = $this->CI->orders_model->get_order_products($order_id, $display_lang_id);

        foreach($order_products as $row)
        {
            /* 
             ** Mrzok Edit
             * Get selected user optins if exist from get_user_order_product_optional_fields_data 
             * by (order_product_id) for this product
             */
            // get user selected options for this product => if exist options
            $user_product_order_options = $this->CI->products_model->get_user_order_product_optional_fields_data($row->order_product_id, $display_lang_id);

            $full_product_code_options_sku = $row->code; // initialize with product code

            if(count($user_product_order_options) > 0 )
            {
                // Splite posted options to Optional array and selected options array
                $selected_optionals_array   = array_values(array_column($user_product_order_options,'product_optional_field_id')); // array of the cart user selected product optionals ids
                $selected_options_array     = array_values(array_column($user_product_order_options,'product_optional_field_value')); // array of the cart user selected options values

                /**
                 * Setting SKU for each Optional Option of the Product ||  => 9/2021
                 * Get SKU Parts related to user selections from product data
                 */
                $user_product_options               = $this->CI->products_model->get_user_order_product_options_sku_parts_data($row->product_id, $selected_options_array);
                $user_product_sku_parts             = array_values(array_column($user_product_options,'sku')); // array of the SKU parts of user selected product options values
                $generic_product_options_sku_part   = implode("",$user_product_sku_parts);

                /**
                 * full_product_code_options_sku is consists from (product code + selected options sku parts) 
                 * which inserted in product data with the it's adding order in admin product form
                 */
                $full_product_code_options_sku      = $full_product_code_options_sku.$generic_product_options_sku_part; // Compination of product code and selected options sku parts
            }else
            {
                $selected_optionals_array   = array();
                $selected_options_array     = array();
            }
            // End Edit

            if($row->product_id != 0 && ($row->quantity_per_serial == 1))
            {
                // $serials = $this->CI->orders_model->generate_product_serials($row->product_id, $row->qty, $country_id); // Basic Code
                $serials = $this->CI->orders_model->generate_product_serials($row->product_id, $row->qty, $country_id, $order_data->store_id, $selected_optionals_array , $selected_options_array); //add options array if exist to be taen on consideration when selecting serials // Mrzok Edit

                foreach($serials as $serial)
                {
                    $serials_data = array(
                                            'order_id'          => $order_id,
                                            'product_id'        => $row->product_id,
                                            'product_serial_id' => $serial->id,
                                            'unix_time'         => time(),
                                            "full_sku"          => $full_product_code_options_sku, // assigning SKU to selected Order Serial , Mrzok Edit => 9/2021
                                            'order_product_id'  => $row->order_product_id // set order_product_id for inserted serial
                                         );

                    ////////serial status///////
                    ////0--->available
                    ////1--->pending
                    ////2--->sold
                    ////3--->invalid

                    $serial_status['serial_status'] = 1;
                    $serial_status['sold_order_id'] = $order_id;

                    //insert log data
                    $log_data = array(
                                        'user_agent'        => $agent                           ,
                                        'user_ip_address'   => $this->CI->input->ip_address()       ,
                                        'serial_id'         => $serial->id                      ,
                                        'product_id'        => $row->product_id                 ,
                                        'order_id'          => $order_id                        ,
                                        'status_id'         => $serial_status['serial_status']  ,
                                        'store_country_id'  => $country_id                      ,
                                        'unix_time'         => time()
                                     );

                    $this->CI->serials_lib->insert_log($log_data);

                    $this->CI->orders_model->update_serial_status($serial_status, $serial->id);
                    if(!$this->CI->orders_model->insert_product_serials($serials_data))
                    {
                        $this->status = 0;
                    }
                }
            }
        }
    }


    /// insert order , create notification , Add Affiliate
    private function _insert_order_log($lang_id, $order_id, $order_status_id, $secret_key, $secret_iv, $payment_method_id, $template_payment_method, $user_points)
    {
        $order_products = $this->CI->orders_model->get_order_all_products($order_id);
        $order_data     = $this->CI->orders_model->get_order_main_details($order_id, $lang_id);
        $user_id        = $order_data->user_id;

        if($order_status_id == 1)
        {
            //reward points

            $total_reward_points = 0;

            foreach($order_products as $product)
            {
                if($product->reward_points_used == 0)
                {
                    $total_reward_points += $product->reward_points;
                    $order_product_data['reward_points_used'] = 1;

                    $this->CI->orders_model->update_product_order_data($order_id, $product->product_id, $order_product_data);
                }
            }


            $user_total_reward_points = $total_reward_points + $user_points;

            $this->encrypt_and_update_users_data($user_id, 'user_points', $user_total_reward_points);

            // Add Affiliate
            $this->add_affiliate($order_id, $user_id);

            $serials_data = $this->CI->orders_model->get_order_serials($order_id);

            $email_msg = '<div style="width:100%; display:block; overflow:hidden; overflow:hidden;">
                            <table cellpadding="0" border="0" width="100%" style="text-align:center; font-size:14px;">
                                <td>'.lang('thumbnail').'</td>
                                <td>'.lang('product').'</td>
                                <td>'.lang('serial').'</td>
                            </tr>';
            $sms_msg  = '';

            foreach($serials_data as $serial)
            {
                $product_serial = $this->CI->products_serials_model->get_products_serials_row($serial->product_serial_id);
                $product_data   = $this->CI->products_model->get_row_data($serial->product_id, $lang_id);
                $product_name   = $product_data->title;
                $img_path       = base_url().'assets/uploads/products/'.$product_data->image;

                $secret_key  = $this->CI->config->item('new_encryption_key');
                $secret_iv   = md5('serial_iv');
                $dec_serials = $this->CI->encryption->decrypt($product_serial->serial, $secret_key, $secret_iv);

                $email_msg .= '<tr>
                                	<td><img src="'.base_url().'assets/uploads/products/'.$product_data->image.'" width="50" height="50" style=" display:block; margin:5px auto;" alt="'.$product_name.'"/></td>
                                    <td>'.$product_name.'</td>
                                    <td>'.$dec_serials.'</td>
                               </tr>';

                $sms_msg   .= lang('product').': '.$product_name.'--'.lang('serial').': '.$dec_serials.'***';

            }

            $email_msg .= '</table>';


            $user_data     = $this->CI->user_model->get_row_data($user_id);
            $emails[]      = $user_data->email;
            $mobile_number = $user_data->phone;

            if($user_data->username != '')
            {
                $username       = $user_data->username;
                $user_email     = $user_data->email;
                $emails[]       = $user_data->email;
                $mobile_number  = $user_data->phone;
            }

            $template_data = array(
                                    'unix_time'             => time()                           ,
                                    'username'              => $username                        ,
                                    'user_email'            => $user_email                      ,
                                    'user_phone'            => $mobile_number                   ,
                                    'status'                => lang('completed')                ,
                                    'order_id'              => $order_id                        ,
                                    'logo_path'             => base_url().'assets/template/site/img/logo.png'           ,
                                    'order_time'            => date('Y/m/d H:i', $order_data->unix_time)                ,
                                    'user_order_link'       => base_url()."orders/order/view_order_details/".$order_id  ,
                                    'order_details_email'   => $email_msg                                               ,
                                    'order_details_sms'     => $sms_msg                                                 ,
                                    'payment_method'        => $template_payment_method
                                  );

            $this->CI->notifications->create_notification('direct_pay', $template_data, $emails, $mobile_number);


        }

        else
        {
            $order_status_data = $this->CI->order_status_model->get_payment_data($payment_method_id);
            $order_status_id = $order_status_data->order_status_id;
        }


        $log_data = array(
                            'order_id'  => $order_id,
                            'status_id' => $order_status_id,
                            'unix_time' => time()
                         );

        $this->CI->orders_model->insert_order_log($log_data);

    }

    /***********************************/
    /* Check if user has affiliate
    /***********************************/
    private function add_affiliate($order_id, $user_id)
    {
        $affliate_user_id = $this->CI->user_model->get_row_data($user_id)->affiliate_user_id;
        $order_data       = $this->CI->orders_model->get_order($order_id);
        $affiliate_data   = $this->CI->admin_affiliate_model->get_afiliate_for_user($affliate_user_id);

        if($affiliate_data)
        {
            if($affiliate_data->num_uses !=0)
            {
                if($affiliate_data->num_uses_done < $affiliate_data->num_uses)
                {
                    $affiliate_amount = $order_data->final_total * ($affiliate_data->commission / 100);

                    $aff_log_data = array(
                                            'user_id'      => $order_data->user_id,
                                            'buyer_id'     => $affliate_user_id,
                                            'affiliate_id' => $affiliate_data->id,
                                            'order_id'     => $order_id,
                                            'commission'   => $affiliate_data->commission,
                                            'amount'       => $affiliate_amount,
                                            'unix_time'    => time()
                                         );

                    $this->CI->affiliate_log_model->insert_affiliate_log_data($aff_log_data);

                    $affiliate_updated_data['num_uses_done'] = $affiliate_data->num_uses_done + 1;
                    $this->CI->admin_affiliate_model->update_affiliate($affiliate_updated_data, $affiliate_data->id);
                }
            }
            else
            {
                $affiliate_amount = $order_data->final_total * ($affiliate_data->commission / 100);

                $aff_log_data = array(
                                        'user_id'      => $order_data->user_id,
                                        'buyer_id'     => $affliate_user_id,
                                        'affiliate_id' => $affiliate_data->id,
                                        'order_id'     => $order_id,
                                        'commission'   => $affiliate_data->commission,
                                        'amount'       => $affiliate_amount,
                                        'unix_time'    => time()
                                     );

                $this->CI->affiliate_log_model->insert_affiliate_log_data($aff_log_data);

                $affiliate_updated_data['num_uses_done'] = $affiliate_data->num_uses_done + 1;
                $this->CI->admin_affiliate_model->update_affiliate($affiliate_updated_data, $affiliate_data->id);
            }
        }
    }

    private function _generate_payment_form($payment_method_id, $order_id, $final_total, $currency_symbol, $products_names, $lang_id, $user_id)
    {

        $url = '';
        if($payment_method_id == 4 || $payment_method_id == 8)   //payfort OR Sadad
        {
            if($user_id != 0)
            {
                $user_data      = $this->user_model->get_row_data($user_id);
                $customer_name  = $user_data->first_name.' '.$user_data->last_name;
            }
            else
            {
                $customer_name = 'guest';
            }

            if($payment_method_id == 8)
            {
                $payment_option = 'SADAD';
            }
            else
            {
                $payment_option = null;
            }

            $products_names = preg_replace("/[^A-Za-z0-9]/", '', $products_names);

            //Product name max length is 35

            //$url = $this->payfort->generate_form($order_id, $final_total, $currency_symbol, $payment_option, $user_data->email, '', $customer_name);
            $url = base_url().'api/create_order/payfort_form/'.$order_id.'/'.$lang_id.'/'.$user_id;

        }
        elseif($payment_method_id == 5)   // PayPal
        {
            $url = base_url().'orders/payment_gateways/process_paypal/'.$order_id;
        }
        elseif($payment_method_id == 6)    //CashU
        {
            $url = base_url().'api/create_order/cashu_form/'.$order_id.'/'.$lang_id.'/'.$user_id;
        }
        elseif($payment_method_id == 13 || $payment_method_id == 14 || $payment_method_id == 15 || $payment_method_id == 16)  //Hyperpay visa & mastercard || Hyperpay stc and mada || apple pay
        {
            if($payment_method_id == 13) //visa and mastercard
            {
              $type = 'hyperpay_visa';
            }
            else if($payment_method_id == 14) //stc pay
            {
              $type = 'hyperpay_stc_pay';
            }
            else if($payment_method_id == 15) // mada
            {
              $type = 'hyperpay_mada';
            }
            else if($payment_method_id == 16) // apple pay
            {
              $type = 'apple_pay';
            }
            $url = base_url().'orders/Payment_gateways/process_hyperpay/'.$order_id.'/'.$type;
        }
        else
        {
            $url = base_url().'orders/order/view_order_details/'.$order_id;
        }

        return $url;
    }

    public function cashu_form($order_id, $lang_id, $user_id)
    {
        $order_data = $this->orders_model->get_order($order_id);

        if($user_id == $order_data->user_id)
        {
            $products_names = '';

            $order_products = $this->orders_model->get_order_products($order_id, $lang_id);

            $final_total     = $order_data->final_total;
            $currency_symbol = $order_data->currency_symbol;


            foreach($order_products as $product)
            {
                $products_names .= $product->title." , ";
            }

            $this->data['form'] = $this->cashu->fetch_form($order_id, $final_total, $currency_symbol, $products_names);
        }
        else
        {
            $this->data['error'] = $this->general_model->get_lang_var_translation('not_allowed_to_access_this_page', $lang_id);
        }

        $this->data['content'] = $this->load->view('payment_form', $this->data, true);
        $this->load->view('site/main_frame',$this->data);
    }

    public function payfort_form($order_id, $lang_id, $user_id)
    {
        $order_data = $this->orders_model->get_order($order_id);

        if($user_id == $order_data->user_id)
        {
            $user_data = $this->user_model->get_row_data($user_id);
            $products_names = '';

            $order_products = $this->orders_model->get_order_products($order_id, $lang_id);

            $final_total     = $order_data->final_total;
            $currency_symbol = $order_data->currency_symbol;
            $customer_name   = $user_data->first_name.' '.$user_data->last_name;

            foreach($order_products as $product)
            {
                $products_names .= $product->title." , ";
            }

            if($order_data->payment_method_id == 8)
            {
                $payment_option = 'SADAD';
            }
            else
            {
                $payment_option = null;
            }

            $this->data['form'] = $this->payfort->generate_form($order_id, $final_total, $currency_symbol, $payment_option, $user_data->email, '', $customer_name);
        }
        else
        {
            $this->data['error'] = $this->general_model->get_lang_var_translation('not_allowed_to_access_this_page', $lang_id);
        }

        $this->data['content'] = $this->load->view('payment_form', $this->data, true);
        $this->load->view('site/main_frame',$this->data);

    }

    public function encrypt_and_update_users_data($user_id, $field, $data)
    {
        $secret_key    = $this->CI->config->item('new_encryption_key');
        $secret_iv     = $user_id;

        $user_enc_data = $this->CI->encryption->encrypt($data, $secret_key, $secret_iv);
        $user_points_data[$field]  = $user_enc_data;

        return $this->CI->user_model->update_user_balance($user_id, $user_points_data);

    }

    public function calculate_order_products_prices($cart_products_array, $tax, $wrapping_cost, $order_shipping_cost, $order_total_coupons)
     {
        $products_discount    = 0;
        $products_price       = 0;
        $coupon_discount      = 0;
        $optional_fields_cost = 0;
        $vat_value = 0;

        foreach($cart_products_array as $product)
        {
            /*
            // Basic Code
            if($product->vat_type == 2)//exclusive
            {
                $products_price += (($product->price + $product->discount + $product->vat_value)  * $product->qty);
            }
            else
            {
                $products_price += (($product->price + $product->discount) * $product->qty);
            }
            */

            // Mrzok Edits : remove $product->discount from calculation as it subtracted later when calculating final_total
            //  $product->price as price is the product price before discount
            if($product->vat_type == 2)//exclusive
            {
                $products_price += (($product->price + $product->vat_value)  * $product->qty);
            }
            else
            {
                $products_price += (($product->price) * $product->qty);
            }
            // End Edits

            $products_discount    += ($product->discount * $product->qty);
            //$products_price       += (($product->price + $product->discount) * $product->qty);
            $coupon_discount      += ($product->coupon_discount * $product->qty);
            $optional_fields_cost += ($product->optional_fields_cost * $product->qty);
            //if($product->vat_type == 2) // exclusive vat
            //{
            $vat_value += $product->vat_value*$product->qty;
            //}
        }

        $final_price = $products_price - $products_discount - $coupon_discount + $optional_fields_cost;


        // $final_price_with_tax = $final_price + $tax + $wrapping_cost + $order_shipping_cost - $products_discount - $order_total_coupons;// + $vat_value ; // Basic Code
        $final_price_with_tax = $final_price + $tax + $wrapping_cost + $order_shipping_cost; // - $products_discount - $order_total_coupons;// + $vat_value ; // Mrzok Edits 5-2021
        //$vat_value = ($final_price_with_tax * $this->settings->vat_percent) / 100;
        /*if($this->settings->vat_type == 2)
        {
            $final_price_with_tax = $final_price_with_tax + $vat_value;
        }
        */
        $final_price_with_tax = round($final_price_with_tax, 2);

        $return_array = array(
                              $products_discount,
                              $order_total_coupons,
                              $final_price,
                              $final_price_with_tax,
                              $optional_fields_cost,
                              'vat_value' => $vat_value
                            );

        return $return_array;
        /**
         * $result[0] = $products_discount
         * $result[1] = $coupon_discount
         * $result[2] = $final_price
         * $result[3] = $final_price_with_tax
         * $result[4] = $optional_fields_cost
         *
         */


     }



  }
