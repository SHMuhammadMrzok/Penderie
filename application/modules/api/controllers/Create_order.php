<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Create_order extends CI_Controller
{
    public $status=1;
    public $settings;
    public $images_path;
    
    public function __construct()
    {
        parent::__construct();

        $this->load->model('general_model');
        $this->load->model('users/user_model');
        $this->load->model('orders/orders_model');
        $this->load->model('orders/order_status_model');
        $this->load->model('products/products_model');
        $this->load->model('affiliate/affiliate_log_model');
        $this->load->model('affiliate/admin_affiliate_model');
        $this->load->model('products/products_serials_model');
        $this->load->model('coupon_codes/coupon_codes_model');
        $this->load->model('payment_options/user_balance_model');
        $this->load->model('payment_options/bank_accounts_model');
        $this->load->model('payment_options/payment_methods_model');
        $this->load->model('shopping_cart/user_bank_accounts_model');

        $this->load->library('api_lib');
        $this->load->library('serials_lib');
        $this->load->library('shopping_cart');
        $this->load->library('encryption');
        $this->load->library('notifications');

        $this->load->library('payment_gateways/payfort');
        $this->load->library('payment_gateways/paypal');
        $this->load->library('payment_gateways/cashu');

        $settings = $this->general_model->get_settings();
        $this->settings = $settings;
        $this->images_path = $this->api_lib->get_images_path();
    }

    public function index()
    {
       /*agent:IOS
appVersion:1.6
userId:135
email:mrzok@shourasoft.com
password:123456
countryId:2
name
mobile
langId:1
userNotes
paymentMethodId:10
bankId:(null)
userAccountName
userAccountNumber
voucherNumber
deviceId:{length=32,bytes=0xa5f433ca6fffd6db67ac6a229d6ca290...efca0f52f3684667}
*/
        $lang_id              = intval($this->input->post('langId', TRUE));
        //$user_id              = intval($this->input->post('userId', TRUE));
        $email                = strip_tags($this->input->post('email', TRUE));
        $name                 = strip_tags($this->input->post('name', TRUE));
        $phone                = strip_tags($this->input->post('mobile', TRUE));
        $password             = strip_tags($this->input->post('password', TRUE));
        $country_id           = intval($this->input->post('countryId', TRUE));
        $agent                = strip_tags($this->input->post('agent', TRUE));
        $gift_msg             = strip_tags($this->input->post('gift_msg', True));

        $notes                = strip_tags($this->input->post('userNotes', TRUE));
        $payment_method_id    = intval($this->input->post('paymentMethodId', TRUE));

        $deviceId             = strip_tags($this->input->post('deviceId', TRUE));
        $ip_address           = $this->input->ip_address();
        $user_id = 0;
        if($this->ion_auth->login($email, $password))
        {
            $user_data = $this->ion_auth->user()->row();
            $user_id   = $user_data->id;
            $this->api_lib->check_user_store_country_id($email, $password, $user_data->id, $country_id);

        }

        $this->shopping_cart->set_user_data($user_id, $deviceId, $ip_address , $country_id ,$lang_id);

        $bank_id            = 0;
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


        $shopping_cart_id = $this->shopping_cart->get_cart_id();

        $cart_data        = $this->shopping_cart_model->get_cart_row_data($shopping_cart_id);
        $cart_contents    = $this->shopping_cart->contents();
        $min_stock        = $this->config->item('min_product_stock');

        /*
        ** $qty_status_array is an array variable of :
        ** $qty_status_array[$key]      => 'product_id-cart_product_id'
        ** $qty_status_array[$value]    => product availability status
        **/
        
        $qty_status_array = $this->shopping_cart->check_cart_products_quantities();

        /**
         * 0 => no_stock
         * 2 => max_qty_per_user_discount_reached
         * 3 => product_not_exist_in_country
         * 4 => max_products_per_order_reached
        */

        if(in_array(0, $qty_status_array))
        {
            $error_msg      = $this->general_model->get_lang_var_translation('no_stock', $lang_id);
            $order_error    = true;
            $output   = array(
                               'response' => 0,
                               'message'  => $error_msg
                             );
        }
        elseif(in_array(2, $qty_status_array))
        {
            $error_msg      = $this->general_model->get_lang_var_translation('max_qty_per_user_discount_reached', $lang_id);
            $order_error    = true;
            $output   = array(
                               'response' => 0,
                               'message'  => $error_msg
                             );
        }
        elseif(in_array(3, $qty_status_array))
        {
            $error_msg      = $this->general_model->get_lang_var_translation('product_not_exist_in_country', $lang_id);
            $order_error    = true;
            $output   = array(
                               'response' => 0,
                               'message'  => $error_msg
                             );
        }
        elseif(in_array(4, $qty_status_array))
        {
            $error_msg      = $this->general_model->get_lang_var_translation('max_products_per_order_reached', $lang_id);
            $order_error    = true;
            $output   = array(
                               'response' => 0,
                               'message'  => $error_msg
                             );
        }
        else
        {
            $settings                     = $this->general_model->get_settings();//$this->user_bootstrap->get_settings();
            $wholesaler_customer_group_id = $settings->wholesaler_customer_group_id;

            //$user_data = $this->data['user'];

            // if guest
            if($user_id == 0)
            {
                $first_order_array = $this->_check_first_time_order($user_id, $email, $phone, $name);

                $first_order_error_exist = $first_order_array[0];
                $first_order_error_msg   = $first_order_array[1];
                $first_order_user_id     = $first_order_array[2];
                $first_order_status      = $first_order_array[3];


                if($first_order_user_id != 0)
                {
                    $user_id = $first_order_user_id;
                }

                if($first_order_status == 0)
                {
                    $output = array(
                                       'response' => 0,
                                       'message'  => $first_order_error_msg
                                   );

                    $this->output->set_content_type('application/json')->set_output(json_encode($output));
                }

                if($first_order_status == 1)
                {
                    $user_id = $first_order_user_id;
                }

                if($first_order_status == 2)
                {
                    $output = array(
                                       'response' => 3,
                                       'message'  => $this->general_model->general_model->get_lang_var_translation('finish_sign_up_msg', $lang_id)
                                   );

                    $this->output->set_content_type('application/json')->set_output(json_encode($output));
                }

                if($first_order_status == 3 )
                {
                    $output = array(
                                       'response' => 2,
                                       'message'  => $this->general_model->general_model->get_lang_var_translation('email_or_phone_already_exist', $lang_id)
                                   );

                    $this->output->set_content_type('application/json')->set_output(json_encode($output));
                }

                $is_first_order = 1;

            }
            else
            {
                if($this->ion_auth->login($email, $password))
                {
                    $user_customer_group = $this->ion_auth->user()->row()->customer_group_id;

                    //$user_customer_group = $user_data->customer_group_id;

                    if($user_customer_group == $wholesaler_customer_group_id)
                    {
                        $is_wholesaler  = true;
                    }
                }
                else
                {
                    $login_error_message = $this->general_model->get_lang_var_translation('login_error', $lang_id);

                    $output = array(
                                       'response' => 0,
                                       'message'  => $login_error_message
                                   );
                }
            }


            $insertion_fail_message = $this->general_model->get_lang_var_translation('not_inserted', $lang_id);

            //if($cart_data->final_total_price_with_tax == 0)
            {

                if($user_id != 0)
                {
                    $start_time = time();
                    $end_time   = $start_time - (60 * 60 * 24);

                    $user_orders_count        = $this->orders_model->count_user_orders_per_day($start_time, $end_time, $user_id);
                    $user_customer_group_data = $this->customer_groups_model->get_user_customer_group_data($user_id);

                    if($user_customer_group_data->max_orders_per_day > $user_orders_count || $user_customer_group_data->max_orders_per_day == 0)
                    {

                        $user_data  = $this->user_model->get_row_data($user_id);

                        $secret_key = $this->config->item('new_encryption_key');
                        $secret_iv  = $user_id;

                        $method_data      = $this->order_status_model->get_payment_data($payment_method_id);

                        $order_status_id  = $method_data->order_status_id;

                        $insert_order = true;

                        //pocket
                        if($payment_method_id == 1)
                        {
                            $user_enc_old_balance = $user_data->user_balance;
                            $user_old_balance     = $this->encryption->decrypt($user_enc_old_balance, $secret_key, $secret_iv);

                            if($user_old_balance >= $cart_data->final_total_price_with_tax)
                            {
                                $user_new_balance                  = $user_old_balance - $cart_data->final_total_price_with_tax;
                                $user_enc_new_balance              = $this->encryption->encrypt($user_new_balance, $secret_key, $secret_iv);
                                $user_balance_data['user_balance'] = $user_enc_new_balance;

                                $this->user_model->update_user_balance($user_id, $user_balance_data);

                                $log_data = array(
                                                    'user_id'           => $user_id,
                                                    'payment_method_id' => $payment_method_id,
                                                    'amount'            => $cart_data->final_total_price_with_tax,
                                                    'balance'           => $user_new_balance ,
                                                    'balance_status_id' => 1,  //withdraw from balance
                                                    'ip_address'        => $this->input->ip_address(),
                                                    'unix_time'         => time()
                                                 );

                                $this->user_balance_model->insert_balance_log($log_data);
                            }
                            else
                            {
                                $output = array(
                                                 'message'  => $this->general_model->get_lang_var_translation('not_enough_pocket_money', $lang_id),
                                                 'response' => 0
                                                );

                                $insert_order = false;


                            }

                            $template_payment_method = $this->general_model->get_lang_var_translation('pocket_money', $lang_id);
                        }
                        elseif($payment_method_id == 2) //reward points
                        {

                            if($this->ion_auth->login($email, $password))
                            {

                                $user_reward_points_value = $this->api_lib->get_user_reward_points_value($user_id, $country_id);

                                if($user_reward_points_value >= $cart_data->final_total_price_with_tax)
                                {
                                    $user_new_reward_points = $user_reward_points_value - $cart_data->final_total_price_with_tax;
                                    $this->user_bootstrap->encrypt_and_update_users_data($user_id, 'user_points', $user_new_reward_points);

                                }
                                else
                                {
                                    $output = array(
                                                     'message'  => $this->general_model->get_lang_var_translation('error_in_reward_points', $lang_id),
                                                     'response' => 0
                                                    );
                                }

                                $template_payment_method = $this->general_model->get_lang_var_translation('reward_points', $lang_id);
                            }
                            else
                            {
                                $output = array(
                                                 'message'  => $this->general_model->get_lang_var_translation('error_in_reward_points', $lang_id),
                                                 'response' => 0
                                                );

                                $insert_order = false;
                            }


                        }
                        elseif($payment_method_id == 7) //voucher
                        {
                            $voucher                 = strip_tags($this->input->post('voucherNumber', TRUE));
                            $voucher_lang_var        = $this->general_model->get_lang_var_translation('voucher', $lang_id);
                            $template_payment_method = $voucher_lang_var;
                        }
                        elseif($payment_method_id == 3) //Banks
                        {
                            $bank_id = intval($this->input->post('bankId', TRUE));

                            $method_data      = $this->order_status_model->get_payment_data($payment_method_id);
                            $order_status_id  = $method_data->order_status_id;
                            //$order_status_id = $this->order_status_model->get_status_id($payment_method_id, 'bank_accounts');

                            // insert user bank account data
                            $user_bank_account_name   = $this->input->post('userAccountName', TRUE);
                            $user_bank_account_number = $this->input->post('userAccountNumber', TRUE);

                            $this->user_bank_accounts_model->delete_bank_account($bank_id, $user_id);

                            $data    = array(
                                                'user_id'        => $user_id                            ,
                                                'bank_id'        => $bank_id                            ,
                                                'account_name'   => strip_tags($user_bank_account_name) ,
                                                'account_number' => strip_tags($user_bank_account_number)
                                            );

                            $this->user_bank_accounts_model->insert_user_account_data($data);


                            $bank_data          = $this->bank_accounts_model->get_bank_data($bank_id, $lang_id);
                            $user_bank_data     = $this->user_bank_accounts_model->get_user_bank_data($bank_id, $user_id);

                            $order_bank_name    = $user_bank_data->account_name;
                            $order_bank_number  = $user_bank_data->account_number;

                            $bank_lang_var              = $this->general_model->get_lang_var_translation('bank_name',$lang_id);
                            $bank_account_name_lang_var = $this->general_model->get_lang_var_translation('bank_account_name',$lang_id);
                            $bank_account_num_lang_var  = $this->general_model->get_lang_var_translation('bank_account_number',$lang_id);

                            $template_payment_method = $bank_lang_var." : ".$bank_data->bank."<br>".$bank_account_name_lang_var." : ".$bank_data->account_name."<br>".$bank_account_num_lang_var." : ".$bank_data->account_number;
                        }
                        // else if( in_array($payment_method_id, array(13, 16)))// Basic Code
                        else if( in_array($payment_method_id, array(13, 15, 16, 17, 18, 19, 20, 21))) // Mrzok Edit = to add MyFatoora payment methods , hyperpay stc pay , Moyasar
                        {
                            $template_payment_method = $this->payment_methods_model->get_payment_method_name($payment_method_id, $lang_id);
                            $products_names = '';
                            $insert_order = 0;
                                               
                            $url = $this->_generate_payment_form($payment_method_id, $cart_data->id.'_'.time(), $cart_data->final_total_price_with_tax, $cart_data->currency_symbol, $products_names, $lang_id, $user_id);

                            if($payment_method_id != 10)
                            {
                                $order_status_id         = 8;
                            }
                            
                            $output = array(
                                               'response'   => intval(1),
                                               'message'    => '',
                                               'paymentURL' => $url,
                                               'orderId'    => "",
                                               'cartId'     => $cart_data->id//.'_'.time()
                                           );
                        }
                        else
                        {
                            $template_payment_method = $this->payment_methods_model->get_payment_method_name($payment_method_id, $lang_id);
                            
                        }

                        if($insert_order)
                        {
                            $currency_symbol = $cart_data->currency_symbol;

                            $cart_stores        = $this->shopping_cart->get_cart_stores($cart_data->id, $lang_id);

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

                            $max_orders_number = $this->orders_model->get_orders_max_number()->orders_number;
                            $new_orders_number = $max_orders_number + 1;



                            foreach($cart_stores as $store)
                            {
                                $cart_stores_products = $this->shopping_cart->get_cart_stores_products($cart_data->id, $store->store_id);

                                $order_tax            = $cart_data->tax / $cart_stores_count;
                                $order_shipping_cost  = $cart_data->shipping_cost / $cart_stores_count;
                                $order_shipping_cost  = round($order_shipping_cost, 2);
                                $order_wrapping_cost  = $cart_data->wrapping_cost / $cart_stores_count;
                                $shipping_cost        = $cart_data->shipping_cost / $cart_stores_count;

                                $order_total_coupons  = $cart_data->coupon_discount / $cart_stores_count;
                                $order_total_coupons  = round($order_total_coupons, 2);

                                $cart_products_result = $this->calculate_order_products_prices($cart_stores_products, $order_tax, $order_wrapping_cost, $shipping_cost, $cart_data->vat_percent, $order_total_coupons);

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
                                                            'agent'               => $agent                                 ,
                                                            'payment_method_id'   => $payment_method_id                     ,
                                                            'bank_id'             => $bank_id                               ,
                                                            'bank_account_name'   => $order_bank_name                       ,
                                                            'bank_account_number' => $order_bank_number                     ,
                                                            'voucher'             => $voucher                               ,
                                                            'order_status_id'     => $order_status_id                       ,
                                                            'country_id'          => $cart_data->country_id                 ,
                                                            'currency_symbol'     => $currency_symbol                       ,
                                                            'vat_type'            => $this->settings->vat_type              ,
                                                            'orders_number'       => $new_orders_number                     ,

                                                             /**related to each order**/
                                                            'items_count'         => count($cart_stores_products)           ,
                                                            'total'               => $final_price                           ,
                                                            'discount'            => $cart_products_result[0]               ,
                                                            'coupon_discount'     => $cart_products_result[1]               ,
                                                            'tax'                 => $order_tax                             ,
                                                            'final_total'         => $cart_products_result[3]               ,
                                                            'wrapping_cost'       => $order_wrapping_cost                   ,
                                                            'shipping_cost'       => $order_shipping_cost                   ,
                                                            'vat_value'           => $cart_products_result['vat_value']     ,
                                                            //'vat_percent'         => $cart_data->vat_percent                ,

                                                            'auto_cancel'         => 1                                      ,
                                                            'notes'               => $notes                                 ,
                                                            'address_id'          => $cart_data->address_id                 ,
                                                            'shipping_type'       => $cart_data->shipping_type              ,
                                                            'shipping_lng'        => $cart_data->shipping_lng               ,
                                                            'shipping_lat'        => $cart_data->shipping_lat               ,
                                                            'needs_shipping'      => $cart_data->needs_shipping             ,
                                                            'shipping_company_id' => $cart_data->shipping_company_id        ,
                                                            'shipping_country_id' => $cart_data->shipping_country_id        ,
                                                            'shipping_city'       => $cart_data->shipping_city              ,
                                                            'shipping_district'   => $cart_data->shipping_district          ,
                                                            'shipping_address'    => $cart_data->shipping_address           ,
                                                            'shipping_name'       => $cart_data->shipping_name              ,
                                                            'shipping_phone'      => $cart_data->shipping_phone             ,
                                                            'branch_id'           => $cart_data->branch_id                  ,
                                                            'send_as_gift'        => $cart_data->send_as_gift               ,
                                                            'wrapping_id'         => $cart_data->wrapping_id                ,
                                                            'gift_msg'            => $cart_data->gift_msg                   , // $gift_msg                              ,
                                                            'unix_time'           => time()                                 ,
                                                            'day'                 => date('d')                              ,
                                                            'month'               => date('m')                              ,
                                                            'year'                => date('Y')
                                                          );

                                if(!$this->orders_model->insert_order($order_data))
                                {
                                    $this->status = 0;

                                    $output = array(
                                                       'response' => 1,
                                                       'message'  => $insertion_fail_message
                                                   );
                                }
                                else
                                {
                                    $order_id   = $this->db->insert_id();

                                    //insert order products

                                    $this->_insert_order_products($order_id, $country_id, $cart_stores_products, $lang_id);

                                    if($this->status == 1)
                                    {
                                        $user_balance = $this->encryption->decrypt($user_data->user_balance, $secret_key, $secret_iv);

                                        // insert recharge cards
                                        $this->_insert_charge_cards($shopping_cart_id, $order_id, $country_id, $payment_method_id, $secret_key, $secret_iv, $user_id, $user_balance);

                                        if($this->status == 1)
                                        {
                                            //generate products serials
                                            $this->_generate_order_serials($order_id, $lang_id, $cart_data->country_id, $agent);

                                            if($this->status == 1)
                                            {
                                                $user_points = $this->encryption->decrypt($user_data->user_points, $secret_key, $secret_iv);

                                                /// insert order , create notification , Add Affiliate
                                                $this->_insert_order_log($lang_id, $order_id, $order_status_id, $secret_key, $secret_iv, $payment_method_id, $template_payment_method, $user_points);

                                                if($this->status == 1)
                                                {
                                                    //send notification
                                                    $products_names = '';
                                                    $status         = $this->order_status_model->get_status_translation_name($order_status_id, $lang_id);
                                                    $order_products = $this->orders_model->get_order_products($order_id, $lang_id);

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
                                                                    $product_data   = $this->products_model->get_row_data($product->product_id, $lang_id);
                                                                    //$product_price  = $this->CI->orders_model->get_order_product_data($product->product_id, $order_id);
                                                                    $product_name   = $product_data->title;
                                                                    $img_path       = $this->images_path.$product_data->image;

                                                                    $email_msg .= '<tr>
                                                                                    	<td><img src="'.$this->images_path.$product_data->image.'" width="50" height="50" style=" display:block; margin:5px auto;" alt="'.$product_name.'"/></td>
                                                                                        <td>'.$product_name.'</td>
                                                                                        <td>'.$product->qty.'</td>
                                                                                        <td>'.$product->final_price.' '.$currency_symbol.'</td>
                                                                                        <td>'.$product->final_price * $product->qty.' '.$currency_symbol.'</td>
                                                                                   </tr>';

                                                                    $sms_msg   .= lang('product').': '.$product_name.'--';
                                                                }
                                                                else
                                                                {
                                                                    $userdata         = $this->user_model->get_row_data($user_id);
                                                                    $secret_key       = $this->config->item('new_encryption_key');
                                                                    $secret_iv        = $userdata->id;
                                                                    $enc_user_balance = $userdata->user_balance;
                                                                    $user_balance     = $this->encryption->decrypt($enc_user_balance, $secret_key, $secret_iv);

                                                                    $email_msg .= '<tr><td></td><td>'.lang('recharge_card').' </td><td> '.$product->final_price.'</td><td>'.lang('current_balance').' </td><td> '.$user_balance.' '.$currency_symbol.' </td></tr>';
                                                                    $sms_msg   .= lang('recharge_card').' : '.$product->final_price.' '.$currency_symbol.'  '.lang('current_balance').$user_balance.' '.$currency_symbol;
                                                                }
                                                            }
                                                            
                                                        if($order_tax != 0)
                                                        {
                                                            $email_msg .= '
                                                                           <tr>
                                                                            <td colspan="3"></td>
                                                                            <td><span>'.lang('tax').'</span></td>
                                                                            <td colspan="2"><span>'.$order_tax.' '.$currency_symbol.'</span></td>
                                                                           </tr>';
                                                        }

                                                        if($order_shipping_cost != 0)
                                                        {
                                                            $email_msg .= '
                                                                           <tr>
                                                                            <td colspan="3"></td>
                                                                            <td><span>'.lang('shipping_cost').'</span></td>
                                                                            <td colspan="2"><span>'.$order_shipping_cost.' '.$currency_symbol.'</span></td>
                                                                           </tr>';
                                                        }

                                                        if($order_wrapping_cost != 0)
                                                        {
                                                            $email_msg .= '
                                                                           <tr>
                                                                            <td colspan="3"></td>
                                                                            <td><span>'.lang('wrapping_cost').'</span></td>
                                                                            <td colspan="2"><span>'.$order_wrapping_cost.' '.$currency_symbol.'</span></td>
                                                                           </tr>';
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
                                                                                'logo_path'             => base_url().'assets/template/site/img/logo.png',//$this->images_path.$this->settings->logo,
                                                                                'user_order_link'       => base_url()."orders/order/view_order_details/".$order_id
                                                                              );


                                                        $this->notifications->create_notification('new_order_added', $template_data, $emails, $mobile_number);



                                                    }


                                                    // check coupon

                                                    $cart_coupons = $this->coupon_codes_model->get_cart_coupons_count($shopping_cart_id);
                                                    if($cart_coupons > 0)
                                                    {
                                                        $coupon_data = array(
                                                                                'cart_id'  => 0,
                                                                                'order_id' => $order_id
                                                                            );
                                                        $this->coupon_codes_model->update_coupon_codes_using_cart_id($shopping_cart_id, $coupon_data);
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

                            if($this->status == 1)
                            {
                                $this->shopping_cart->delete();

                                $final_total = $cart_data->final_total_price_with_tax;

                                $url = '';

                                if($payment_method_id == 4 || $payment_method_id == 5 || $payment_method_id == 6 || $payment_method_id == 8  || $payment_method_id ==13 || $payment_method_id ==16 )
                                {
                                    $url = $this->_generate_payment_form($payment_method_id, $order_id, $final_total, $cart_data->currency_symbol, $products_names, $lang_id, $user_id);
                                }

                                $success_message = $this->general_model->get_lang_var_translation('order_inserted_successfully', $lang_id);

                                $output = array(
                                                   'response'   => 1,
                                                   'message'    => $success_message,
                                                   'paymentURL' => $url,
                                                   'orderId'    => $order_id,
                                                   'cartId'     => 0
                                               );
                            }
                            ///////////////////////////////////////////////////////////////////

                        }
                        
                    }
                    else
                    {
                        $fail_message = $this->general_model->get_lang_var_translation('max_orders_per_day_reached', $lang_id);

                        $output = array(
                                           'response' => 0,
                                           'message'  => $fail_message
                                       );
                    }

                }

            }
            /*else
            {
                $fail_message = $this->general_model->get_lang_var_translation('no_products_in_your_shopping_cart', $lang_id);

                $output = array(
                                   'response' => 0,
                                   'message'  => $fail_message
                               );
            }
            */
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));

    }


    private function _check_first_time_order($user_id, $email, $phone, $name)
    {
        $error_msg    = '';
        $errors_exist = false;
        $status       = 0;

        /*
              Status
            0- Error
            1- insert order
            2- sign up (first_order)
            3- sign in
        */

        //set validation rules
        $this->form_validation->set_rules('email', lang('email'), 'required|valid_email');
        $this->form_validation->set_rules('mobile', lang('phone'), 'required');
        $this->form_validation->set_rules('name', lang('name'), 'required');

        $this->form_validation->set_error_delimiters('', '');

        if ($this->form_validation->run() == FALSE)
        {
           $error_msg    = validation_errors();
           $errors_exist = true;
           $status       = 0;
        }
        else
        {
            $email = strip_tags($this->input->post('email', TRUE));
            $phone = strip_tags($this->input->post('mobile', TRUE));
            $name  = strip_tags($this->input->post('name', TRUE));

            $user_email_count = $this->user_model->count_user_data_exist('email', $email);
            $user_phone_count = $this->user_model->count_user_data_exist('phone', $phone);
            if($user_email_count == 0 && $user_phone_count == 0)
            {
                $new_user_customer_group_id = $this->config->item('new_user_customer_group_id');

                $additional_data = array(
                                          'phone'             => $phone,
                                          'first_name'        => $name ,
                                          'customer_group_id' => $new_user_customer_group_id,
                                          'first_order'       => 1,
                                          'active'            => 0
                                        );

                $group    = array('id' => 2);
                $user_id  = $this->ion_auth_model->register($name, '', $email, $additional_data, $group);

                if($user_id != '')
                {
                    $this->data['user_id'] = $user_id;
                    $status = 1;
                    $_SESSION['first_order_inserted'] = lang('first_order_inserted_successfully');
                }
            }
            else
            {
                $conditions_array = array(
                                            'email' => $email ,
                                            'phone' => $phone
                                         );

                $user_count = $this->user_model->get_user_data_by_fields($conditions_array);

                if($user_count == 1)
                {
                    $user_data = $this->user_model->get_user_data_by_field('email', $email);

                    /*if($user_data->first_order == 1)
                    {
                        $status = 2;  // sign up
                    }
                    else
                    {
                        $status = 3;   // sign in
                    }
                    */
                    $user_id = $user_data->id;
                    $this->data['user_id'] = $user_data->id;

                    $status = 1;
                }
                else
                {
                    $user_data_by_email = $this->user_model->get_user_data_by_field('email', $email);
                    $user_data_by_phone = $this->user_model->get_user_data_by_field('phone', $phone);

                    if(count($user_data_by_email) != 0)
                    {

                        //$status     = 3;   // sign in
                        //$error_msg  = 'sign in';
                        $user_id = $user_data_by_email->id;
                        $this->data['user_id'] = $user_data_by_email->id;
                        $status = 1;
                    }
                    else if(count($user_data_by_phone) != 0)
                    {
                        $user_id = $user_data_by_phone->id;
                        $this->data['user_id'] = $user_data_by_phone->id;
                        $status = 1;

                    }


                }
            }
        }

        return array($errors_exist, $error_msg, $user_id, $status);
    }


    private function _insert_order_products($order_id, $country_id, $cart_stores_products, $lang_id)
    {
        foreach($cart_stores_products as $content)
        {
            if($content->product_id != 0)
            {
                $reward_points  = $this->products_model->get_reward_points($content->product_id, $country_id);
                $product_data   = $this->products_model->get_product_country_data($content->product_id, $country_id);

                if($product_data->quantity_per_serial == 1)
                {
                    $product_cost = $product_data->average_cost;
                }
                else
                {
                    $product_cost = $product_data->cost;
                }
                
                if($content->vat_type == 2)//exclusive
                {
                    $final_price = $content->final_price + $content->vat_value;
                }
                else
                {
                    $final_price = $content->final_price;
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
                                               'final_price'        => $final_price,//$content->final_price            ,
                                               'discount'           => $content->discount               ,
                                               'coupon_discount'    => $content->coupon_discount        ,
                                               'vat_value'          => $content->vat_value              ,
                                               'vat_percent'        => $content->vat_percent            ,
                                               'vat_type'           => $content->vat_type               ,
                                               'reward_points'      => $reward_points * $content->qty   ,
                                               'unix_time'          => time()
                                           );

                $order_product_id = $this->orders_model->insert_order_products($order_product_data);
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

                $product_quantity     = $this->products_model->count_product_available_quantity($content->product_id, $country_id);
                $new_product_quantity = $product_quantity - $content->qty;
                $updated_amount       = array('product_quantity' => $new_product_quantity);

                $this->products_model->update_product_country_amount($updated_amount, $content->product_id, $country_id);

                $min_stock            = $this->config->item('min_product_stock');
                $available_qty        = $product_quantity - $min_stock;
                $new_product_quantity = $available_qty - $content->qty;


                if($new_product_quantity == 0)
                {
                    $product_name  = $this->products_model->get_product_name($content->product_id, $lang_id);
                    $emails[]      = $this->config->item('email');
                    $mobile_number = $this->config->item('mobile');
                    $template_data = array('product_name' => $product_name);

                    $this->notifications->create_notification('product_quantity_less_than_threshold', $template_data, $emails, $mobile_number);
                }
            }
        }
    }

    private function update_user_optional_fields_products($cart_id, $cart_product_id, $updated_data)
    {
        $this->products_model->update_product_optional_fields_data($cart_id, $cart_product_id, $updated_data);
    }

    private function _insert_charge_cards($shopping_cart_id, $order_id, $country_id, $payment_method_id, $secret_key, $secret_iv, $user_id, $user_balance)
    {
        $recharge_cards = $this->shopping_cart_model->get_recharge_cards_data($shopping_cart_id);
        $user_data        = $this->user_model->get_row_data($user_id);

        $currency_data  = $this->currency_model->get_country_currency_result($user_data->store_country_id);

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

                $this->orders_model->insert_order_products($order_product_data);



                $log_data = array(
                                    'user_id'           => $user_id,
                                    'payment_method_id' => $payment_method_id,
                                    'amount'            => $card->price,
                                    'balance'           => $user_balance,
                                    'balance_status_id' => 3,  //request to add balace
                                    'ip_address'        => $this->input->ip_address(),
                                    'unix_time'         => time()
                                 );

                if(!$this->user_balance_model->insert_balance_log($log_data))
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
        $order_data     = $this->orders_model->get_order($order_id);
        $order_products = $this->orders_model->get_order_products($order_id, $display_lang_id);

        foreach($order_products as $row)
        {
            /* 
             * Get selected user optins if exist from get_user_order_product_optional_fields_data 
             * by (order_product_id) for this product
             */
            // get user selected options for this product => if exist options
            $user_product_order_options = $this->products_model->get_user_order_product_optional_fields_data($row->order_product_id, $display_lang_id);

            if(count($user_product_order_options) > 0 )
            {
                // Splite posted options to Optional array and selected options array
                $selected_optionals_array   = array_values(array_column($user_product_order_options,'product_optional_field_id')); // array of the cart user selected product optionals ids
                $selected_options_array     = array_values(array_column($user_product_order_options,'product_optional_field_value')); // array of the cart user selected options values
            }else
            {
                $selected_optionals_array   = array();
                $selected_options_array     = array();
            }

            if($row->product_id != 0 && ($row->quantity_per_serial == 1))
            {
                $serials = $this->orders_model->generate_product_serials($row->product_id, $row->qty, $country_id, $order_data->store_id , $selected_optionals_array , $selected_options_array);//add options array if exist to be taen on consideration when selecting serials

                foreach($serials as $serial)
                {
                    $serials_data = array(
                                            'order_id'          => $order_id,
                                            'product_id'        => $row->product_id,
                                            'product_serial_id' => $serial->id,
                                            'unix_time'         => time()
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
                                        'user_ip_address'   => $this->input->ip_address()       ,
                                        'serial_id'         => $serial->id                      ,
                                        'product_id'        => $row->product_id                 ,
                                        'order_id'          => $order_id                        ,
                                        'status_id'         => $serial_status['serial_status']  ,
                                        'store_country_id'  => $country_id                      ,
                                        'unix_time'         => time()
                                     );

                    $this->serials_lib->insert_log($log_data);

                    $this->orders_model->update_serial_status($serial_status, $serial->id);
                    if(!$this->orders_model->insert_product_serials($serials_data))
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
        $order_products = $this->orders_model->get_order_all_products($order_id);
        $order_data     = $this->orders_model->get_order_main_details($order_id, $lang_id);
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

                    $this->orders_model->update_product_order_data($order_id, $product->product_id, $order_product_data);
                }
            }


            $user_total_reward_points = $total_reward_points + $user_points;

            $this->user_bootstrap->encrypt_and_update_users_data($user_id, 'user_points', $user_total_reward_points);

            // Add Affiliate
            $this->add_affiliate($order_id, $user_id);

            $serials_data = $this->orders_model->get_order_serials($order_id);

            $email_msg = '<div style="width:100%; display:block; overflow:hidden; overflow:hidden;">
                            <table cellpadding="0" border="0" width="100%" style="text-align:center; font-size:14px;">
                                <td>'.lang('thumbnail').'</td>
                                <td>'.lang('product').'</td>
                                <td>'.lang('serial').'</td>
                            </tr>';
            $sms_msg  = '';

            foreach($serials_data as $serial)
            {
                $product_serial = $this->products_serials_model->get_products_serials_row($serial->product_serial_id);
                $product_data   = $this->products_model->get_row_data($serial->product_id, $lang_id);
                $product_name   = $product_data->title;
                $img_path       = $this->images_path.$product_data->image;

                $secret_key  = $this->config->item('new_encryption_key');
                $secret_iv   = md5('serial_iv');
                $dec_serials = $this->encryption->decrypt($product_serial->serial, $secret_key, $secret_iv);

                $email_msg .= '<tr>
                                	<td><img src="'.$this->images_path.$product_data->image.'" width="50" height="50" style=" display:block; margin:5px auto;" alt="'.$product_name.'"/></td>
                                    <td>'.$product_name.'</td>
                                    <td>'.$dec_serials.'</td>
                               </tr>';

                $sms_msg   .= lang('product').': '.$product_name.'--'.lang('serial').': '.$dec_serials.'***';

            }

            $email_msg .= '</table>';


            $user_data     = $this->user_model->get_row_data($user_id);
            $emails[]      = $user_data->email;
            $mobile_number = $user_data->phone;

            if($user_data->username != '')
            {
                $username       = $user_data->username;
                $user_email     = $user_data->email;
                $emails[]       = $user_data->email;
                $mobile_number  = $user_data->phone;
            }
            else
            {

                $username = $this->general_model->get_lang_var_translation('visitor', $lang_id);
                /*$emails[]      = $email;
                $user_email    = $email;
                $mobile_number = $phone;
                */
            }

            $template_data = array(
                                    'unix_time'             => time()                           ,
                                    'username'              => $username                        ,
                                    'user_email'            => $user_email                      ,
                                    'user_phone'            => $mobile_number                   ,
                                    'status'                => lang('completed')                ,
                                    'order_id'              => $order_id                        ,
                                    'logo_path'             => $this->images_path.$this->settings->logo           ,
                                    'order_time'            => date('Y/m/d H:i', $order_data->unix_time)                ,
                                    'user_order_link'       => base_url()."orders/order/view_order_details/".$order_id  ,
                                    'order_details_email'   => $email_msg                                               ,
                                    'order_details_sms'     => $sms_msg                                                 ,
                                    'payment_method'        => $template_payment_method
                                  );

            $this->notifications->create_notification('direct_pay', $template_data, $emails, $mobile_number);


        }

        /*else
        {
            //$order_status_data = $this->order_status_model->get_payment_data($payment_method_id);
            //$order_status_id = $order_status_data->order_status_id;
        }*/


        $log_data = array(
                            'order_id'  => $order_id,
                            'status_id' => $order_status_id,
                            'unix_time' => time()
                         );

        $this->orders_model->insert_order_log($log_data);

    }

    /***********************************/
    /* Check if user has affiliate
    /***********************************/
    private function add_affiliate($order_id, $user_id)
    {
        $affliate_user_id = $this->user_model->get_row_data($user_id)->affiliate_user_id;
        $order_data       = $this->orders_model->get_order($order_id);
        $affiliate_data   = $this->admin_affiliate_model->get_afiliate_for_user($affliate_user_id);

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

                    $this->affiliate_log_model->insert_affiliate_log_data($aff_log_data);

                    $affiliate_updated_data['num_uses_done'] = $affiliate_data->num_uses_done + 1;
                    $this->admin_affiliate_model->update_affiliate($affiliate_updated_data, $affiliate_data->id);
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

                $this->affiliate_log_model->insert_affiliate_log_data($aff_log_data);

                $affiliate_updated_data['num_uses_done'] = $affiliate_data->num_uses_done + 1;
                $this->admin_affiliate_model->update_affiliate($affiliate_updated_data, $affiliate_data->id);
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
        elseif($payment_method_id == 13)  //Hyperpay
        {
            $url = base_url().'orders/Payment_gateways/process_hyperpay_from_cart/'.$order_id.'/hyperpay_visa';
        }
        elseif($payment_method_id == 15)     // MOYASAR
        {
            $url = base_url().'orders/Payment_gateways/process_moyasar/'.$order_id;
        }
        elseif($payment_method_id == 16)  //mada
        {
            $url = base_url().'orders/Payment_gateways/process_hyperpay_from_cart/'.$order_id.'/mada';
        }
        elseif($payment_method_id == 17)     // MyFatoora => VISA & MASTERCARD
        {
            $url = base_url().'orders/Payment_gateways/process_myFatoora_from_cart/'.$order_id.'/myFatoora_visa';
        }
        elseif($payment_method_id == 18)     // MyFatoora => Apple Pay (mada)
        {
            $url = base_url().'orders/Payment_gateways/process_myFatoora_from_cart/'.$order_id.'/myFatoora_apple_pay_mada';
        }
        elseif($payment_method_id == 19)     // MyFatoora => Apple Pay
        {
            $url = base_url().'orders/Payment_gateways/process_myFatoora_from_cart/'.$order_id.'/myFatoora_apple_pay';
        }
        elseif($payment_method_id == 20)     // MyFatoora => Mada
        {
            $url = base_url().'orders/Payment_gateways/process_myFatoora_from_cart/'.$order_id.'/myFatoora_mada';
        }
        elseif($payment_method_id == 21)     // HyperPay => STC Pay
        {
            $url = base_url().'orders/Payment_gateways/process_hyperpay_from_cart/'.$order_id.'/hyperpay_stc_pay';
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

            $final_total     = round($order_data->final_total, 2);
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
                $payment_option = 'VISA';
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

     public function calculate_order_products_prices($cart_products_array, $tax, $wrapping_cost, $shipping_cost, $vat_percent, $order_total_coupon)
     {
        $products_discount  = 0;
        $products_price     = 0;
        $coupon_discount    = 0;
        $vat_value = 0;

        foreach($cart_products_array as $product)
        {
            if($product->vat_type == 2)//exclusive
            {
                $products_price += (($product->price + $product->discount + $product->vat_value)  * $product->qty);
            }
            else
            {
                $products_price += ($product->price + $product->discount * $product->qty);
            }
            
            $products_discount   += ($product->discount * $product->qty);
            //$products_price      += (($product->price + $product->discount) * $product->qty);
            $coupon_discount     += ($product->coupon_discount * $product->qty);

            //if($product->vat_type == 2)
            //{
              $vat_value += $product->vat_value;
            //}
        }

        $final_price = $products_price - $products_discount - $coupon_discount - $order_total_coupon;
        $final_price_with_tax = $final_price + $tax + $wrapping_cost + $shipping_cost - $products_discount;// + $vat_value;;

        //$vat_value = ($final_price_with_tax * $vat_percent) / 100;

        /*if($this->settings->vat_type == 2)
        {
          //exclusive vats
          $final_price_with_tax = $final_price_with_tax + $vat_value;
        }*/

        $return_array = array(
                                $products_discount,
                                $order_total_coupon,//$coupon_discount,
                                $final_price,
                                $final_price_with_tax,
                                'vat_value'     => $vat_value,

                            );

        return $return_array;
        /**
         * $result[0] = $products_discount
         * $result[1] = $coupon_discount
         * $result[2] = $final_price
         * $result[3] = $final_price_with_tax
         *
         */


     }


/************************************************************************/
}
