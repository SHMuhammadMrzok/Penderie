<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
/**
 *
 */
class Orders
{
    public $CI ;
    public $settings;
    public $site_settings;
    public $lang_id;

    public $status = 1;
    public $data   = array();
    public $config   = array();

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
        $this->CI->load->library('payment_gateways/hyperpay');
        $this->CI->load->library('payment_gateways/Myfatoora');

        $this->CI->load->model('global_model');
        $this->CI->load->model('orders_model');
        $this->CI->load->model('users/cities_model');
        $this->CI->load->model('users/user_model');
        $this->CI->load->model('users/users_model');
        $this->CI->load->model('orders/order_status_model');
        $this->CI->load->model('products/products_model');
        $this->CI->load->model('currencies/currency_model');
        $this->CI->load->model('products/products_serials_model');
        $this->CI->load->model('coupon_codes/coupon_codes_model');
        $this->CI->load->model('payment_options/user_balance_model');
        $this->CI->load->model('payment_options/payment_methods_model');


        // Get settings table data
        $this->settings = $this->CI->global_model->get_config();
        $this->lang_id = $this->settings->default_lang;
        
        // Set images_path => Mrzok Edit
        $site_settings_list = $this->CI->global_model->get_site_settings();
        
        foreach ($site_settings_list as $row)
        {
          $this->site_settings[$row->field] = $row->value;
        }
        
        if($this->site_settings['images_source'] == 'amazon')
        {
            $images_path = "https://".$this->site_settings['amazon_s3_my_bucket'].".s3.".$this->site_settings['amazon_s3_region'].".amazonaws.com/".$this->site_settings['amazon_s3_subfolder'];
            //https://sbmcart.s3.eu-west-2.amazonaws.com/qhwastore/54e62-2019-10-19.png
        }
        else
        {
            $images_path = base_url().'assets/uploads/';
        }
        $this->data['images_path'] = $images_path;
    }

    public function process_auto_cancel_orders()
    {
        /*******Auto Cancel Orders*********/
        $all_orders      = $this->CI->orders_model->get_all_pending_orders();
        $order_end_hours = $this->settings->min_order_hours;//$this->config->item('order_end_hours');
        $end_time        = time() - $order_end_hours * 60 * 60;
        $i = 0;
        foreach($all_orders as $order)
        {
            if($order->unix_time < $end_time)
            { echo "order id : $order->id <br>";
                // update order status
                $status_data['order_status_id'] = 3;
                $this->CI->orders_model->update_order_status($order->id, $status_data);

                $log_data = array(
                                    'order_id'  => $order->id,
                                    'status_id' => 3,          // rejected
                                    'unix_time' => time()
                                 );
                $this->CI->orders_model->insert_order_log($log_data);

                //update product_serials value
                $order_products = $this->CI->orders_model->get_order_all_products($order->id);
                $serials_data   = $this->CI->orders_model->get_order_serials($order->id);

                if($serials_data)
                {
                    foreach($serials_data as $serial)
                    {
                        $serial_data = array(
                                                'serial_status' => 0,
                                                'sold_order_id' => 0
                                            );

                        $this->CI->products_serials_model->update_serial($serial->product_serial_id, $serial_data);

                        $country_id = $order->country_id;

                        foreach($order_products as $product)
                        {
                            $product_qty = $this->CI->products_model->count_product_available_quantity($country_id, $product->product_id);
                            $product_updated_data['product_quantity'] = $product_qty;

                            $this->CI->products_model->update_product_countries($product->product_id, $country_id, $product_updated_data);
                        }
                    }
                }

                $this->send_auto_cancel_notification($order->id, $order->unix_time, $order->user_id);
                $i++;
            }
        }

        echo "total orders : $i";
    }

    public function send_auto_cancel_notification($order_id, $order_time, $user_id)
    {

        // send notification
        $userdata = $this->CI->users_model->get_user($user_id);
        $username = $userdata->first_name.' '.$userdata->last_name;
        $emails[] = $userdata->email;
        $phone    = $userdata->phone;
        $status   = $this->CI->order_status_model->get_status_translation_name(3, $this->lang_id);

        $template_data = array(
                                'logo_path'  => $this->data['images_path'].$this->settings->logo,
                                'unix_time'  => date('Y/m/d', time()),
                                'username'   => $username,
                                'status'     => $status,
                                'order_id'   => $order_id,
                                'order_time' => date('Y/m/d H:i', $order_time)
                              );

        $this->CI->notifications->create_notification('auto_cancel_order', $template_data, $emails, $phone);
    }

    public function check_to_be_sent_serials()
    {
        $this->CI->config->load('encryption_keys');

        $send_time   = time();
        $orders      = $this->CI->orders_model->get_to_be_sent_serials_orders($this->lang_id, $send_time);
        $secret_key  = $this->CI->config->item('new_encryption_key');

        foreach($orders as $order)
        {
            $userdata = $this->CI->users_model->get_user($order->user_id);

            $secret_iv            = $order->user_id;
            $order_products       = $this->CI->orders_model->get_order_all_products($order->id);
            $serials_data         = $this->CI->orders_model->get_order_serials($order->id);
            $recharge_cards_count = $this->CI->orders_model->get_recharge_cards_count($order->id);

            if($recharge_cards_count > 0)
            {
                $this->_update_user_balance($order, $secret_key, $userdata);
            }

            //update product_serials value
            $this->_update_order_serials($order->id, $order->country_id, $order_products);

            //insert user total reward points
            $this->_apply_user_reward_points($userdata, $order->id, $secret_key, $secret_iv, $order_products);

            // Add Affiliate
            $this->_add_affiliate($userdata, $order);

            //update order
            $order_data['send_later'] = 0;
            $this->CI->orders_model->update_order_data($order->id, $order_data);

            // insert order log
            $log_data = array(
                                'order_id'  => $order->id  ,
                                'status_id' => 1 ,
                                'unix_time' => time()
                             );

            $this->CI->orders_model->insert_order_log($log_data);

            // send notification

            $username = $userdata->first_name.' '. $userdata->last_name;

            if($username){$username = $username;}
            else{$username = lang('visitor');}

            $emails[] = $userdata->email;

            if($userdata->stop_wholesaler_sms == 1)
            {
                $phone = '';
            }
            else
            {
                $phone    = $userdata->phone;
            }
            $status   = $this->CI->order_status_model->get_status_translation_name($order->order_status_id, $this->lang_id);

            $email_msg = '';
            $sms_msg   = '';

            if($serials_data)
            {
                $email_msg = '<table width="100%" border="1" style="font-family: Tahoma, Geneva, sans-serif; font-size: 15px; line-height: 2; text-align: center" >
                            	<tr style="text-align: center; font-weight: bold; font-size: 16px; background: #009; color: #fff;" >
                                    <td>'.lang('thumbnail').'</td>
                                    <td>'.lang('product').'</td>
                                    <td>'.lang('serial').'</td>
                                </tr>';
                $sms_msg  = '';

                foreach($serials_data as $serial)
                {
                    $product_serial = $this->CI->products_serials_model->get_products_serials_row($serial->product_serial_id);
                    $product_data   = $this->CI->products_model->get_row_data($serial->product_id, $this->lang_id);
                    $product_name   = $product_data->title;

                    $secret_iv   = md5('serial_iv');
                    $dec_serials = $this->CI->encryption->decrypt($product_serial->serial, $secret_key, $secret_iv);

                    $email_msg .= '<tr>
                                    	<td><img src="'.$this->data['images_path'].$product_data->image.'" width="50" height="50" style=" display:block; margin:5px auto;" alt="'.$product_name.'"/></td>
                                        <td>'.$product_name.'</td>
                                        <td>'.$dec_serials.'</td>
                                   </tr>';

                    $sms_msg   .= lang('product').': '.$product_name.' '.lang('serial').': '.$dec_serials.' - ';

                }

                $email_msg .= '</table>';
            }

            $cards_count = $this->CI->orders_model->get_recharge_cards_count($order->id);

            if($cards_count > 0)
            {
                $secret_iv        = $order->user_id;
                $enc_user_balance = $userdata->user_balance;
                $user_balance     = $this->CI->encryption->decrypt($enc_user_balance, $secret_key, $secret_iv);

                $recharge_cards  = $this->CI->orders_model->get_recharge_card($order->id);
                $email_msg .= '<table width="100%" border="1" style="font-family:Tahoma, Geneva, sans-serif; font-size:15px; line-height:2;"><tr>';

                foreach($recharge_cards as $card)
                {
                    $email_msg .= '<td>'.lang('recharge_card').' : '.$card->price.'</td><td>'.lang('current_balance').' : '.$user_balance.' :</td>';
                    $sms_msg   .= lang('recharge_card').' : '.$card->price.'  '.lang('current_balance').$user_balance;
                }
            }

            $template_data = array(
                                    'unix_time'    => time(),
                                    'username'     => $username,
                                    'status'       => $status,
                                    'order_id'     => $order->id,
                                    'logo_path'    => $this->data['images_path'].$this->settings->logo,
                                    'order_time'   => date('Y/m/d H:i', $order->unix_time),
                                    'order_details_email' => $email_msg,
                                    'order_details_sms'   => $sms_msg
                                  );

            if($this->CI->notifications->create_notification('pending_order_completed', $template_data, $emails, $phone))
            {
                $_SESSION['success'] = lang('success');

                $this->CI->session->mark_as_flash('success');
            }
            else
            {
                $_SESSION['notification_error'] = lang('serials_msg_not_sent');

                $this->CI->session->mark_as_flash('notification_error');
            }
        }
    }

    private function _update_user_balance($order_data, $secret_key, $userdata)
    {
        $this->CI->load->model('payment_options/user_balance_model');

        $new_balance            = 0;
        $secret_iv              = $order_data->user_id;
        $recharge_cards_balance = $this->CI->orders_model->get_recharge_card($order_data->id);

        foreach($recharge_cards_balance as $card)
        {
            if($card->recharge_card_used == 0)
            {
                $new_balance += $card->price;

                $card_updated_data['recharge_card_used'] = 1;
                $this->CI->orders_model->update_product_order($order_data->id, $card->price, $card_updated_data);
            }
        }

        $payment_id = $order_data->payment_method_id;

        //user balance
        $enc_user_balance = $userdata->user_balance;

        if($enc_user_balance == '')
        {
            $user_new_balance = $new_balance;
        }
        else
        {
            $user_balance     = $this->CI->encryption->decrypt($enc_user_balance, $secret_key, $secret_iv);
            $user_new_balance = $user_balance + $new_balance;
        }



        $balance = $this->CI->encryption->encrypt($user_new_balance, $secret_key, $secret_iv);
        $user_new_balance_data['user_balance'] = $balance;

        $this->CI->user_model->update_user_balance($order_data->user_id, $user_new_balance_data);

        $log_data = array(
                            'user_id'           => $order_data->user_id,
                            'payment_method_id' => $payment_id,
                            'amount'            => $new_balance,
                            'balance'           => $user_new_balance ,
                            'balance_status_id' => 2,  //add to the balance
                            'unix_time'         => time()
                         );

        $this->CI->user_balance_model->insert_balance_log($log_data);
    }

    private function _update_order_serials($order_id, $country_id) // Sold
    {
        $serials_data = $this->CI->orders_model->get_order_serials($order_id);

        foreach($serials_data as $serial)
        {
            $serial_data['serial_status'] = 2;
            $this->CI->products_serials_model->update_serial($serial->product_serial_id, $serial_data);

            $product_qty = $this->CI->products_model->count_product_available_quantity($country_id, $serial->product_id);
            $product_updated_data['product_quantity'] = $product_qty;

            $this->CI->products_model->update_product_countries($serial->product_id, $country_id, $product_updated_data);
        }
    }

    private function _apply_user_reward_points($userdata, $order_id, $secret_key, $secret_iv, $order_products)
    {

        $enc_user_points     = $userdata->user_points;
        $user_points         = $this->CI->encryption->decrypt($enc_user_points, $secret_key, $secret_iv);

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
        $enc_reward_points        = $this->CI->encryption->encrypt($user_total_reward_points, $secret_key, $secret_iv);

        $user_data['user_points'] = $enc_reward_points;

        $this->CI->user_model->update_user($userdata->id, $user_data);
    }

    public function _add_affiliate($userdata, $order_data)
    {
        $this->CI->load->model('affiliate/admin_affiliate_model');
        $this->CI->load->model('affiliate/affiliate_log_model');

        $affliate_user_id = $userdata->affiliate_user_id;
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
                                            'order_id'     => $order_data->id,
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
                                        'order_id'     => $order_data->id,
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

    public function send_order_delay_sorry_email()
    {
        $check_time         = time() - 300;

        $not_replied_orders = $this->CI->orders_model->get_not_replied_orders($check_time);

        if(count($not_replied_orders) >0)
        {
            $template_data = array(
                                    'unix_time'    => time(),
                                    'logo_path'    => $this->data['images_path'].$this->settings->logo,
                                  );

            foreach($not_replied_orders as $order)
            {
                $userdata = $this->CI->users_model->get_user($order->user_id);
                $emails[] = $userdata->email;
                $phone    = $userdata->phone;

                $template_data['username']          = $userdata->first_name .' '. $userdata->last_name;
                $template_data['order_time']        = date('Y/m/d H:i', $order->unix_time);
                $template_data['order_id']          = $order->id;
                $template_data['user_order_link']   = base_url().'orders/order/view_order_details/'.$order->id;

                $this->CI->notifications->create_notification('order_reply', $template_data, $emails, $phone);

                $order_data['sorry_email'] = 1;

                $this->CI->orders_model->update_order_data($order->id, $order_data);
            }
        }
    }

    public function reset_order_coupon($order_id)
    {
        $lang_id            = $this->CI->data['active_language']->id;
        $order_details      = $this->CI->orders_model->get_order_details($order_id, $lang_id);
        $order_coupon_data  = $this->CI->orders_model->get_order_coupon_data($order_id);

        $product_coupon_discount_data = array();

        $this->CI->orders_model->delete_order_coupon_data($order_id);
        $product_coupon_discount_data['coupon_discount'] = 0;

        //coupon discount for all order products to be 0
        foreach($order_details as $content)
        {
            $product_coupon_discount_data['final_price'] = $content->price - $content->discount;
            $this->CI->orders_model->update_order_product($order_id, $content->product_id, $product_coupon_discount_data);
        }

        //coupon discount for order = 0
        $order_discount_data['coupon_discount'] = 0;
        $this->CI->orders_model->update_order_data($order_id, $order_discount_data);

        $this->update_order_total_prices($order_id);

        //delete coupon use data
        $this->CI->orders_model->delete_order_coupon_data($order_id);

        if(count($order_coupon_data) != 0 && ! empty($order_coupon_data) )
        {
            $reset_result_array = $this->coupon_discount($order_coupon_data->code);
        }
        else
        {
            $reset_result_array = true;
        }

        return $reset_result_array;


    }

    public function update_order_total_prices($order_id)
    {
        $order_data = $this->CI->orders_model->get_order_data($order_id);

        if($order_data)
        {
            $products_discount   = 0;
            $coupon_discount     = 0;
            $products_price      = 0;
            $items_count         = 0;

            $order_products      = $this->CI->orders_model->get_order_products_data($order_id);
            $order_total_coupon  = $this->check_if_order_coupon_is_total($order_id);

            foreach ($order_products as $product)
            {
                $products_discount   += ($product->discount * $product->qty);
                $products_price      += ($product->price * $product->qty);
                $coupon_discount     += ($product->coupon_discount * $product->qty);
                $items_count         += $product->qty;
            }

            if($order_total_coupon)
            {
                $coupon_discount = $order_data->coupon_discount;
            }

            $final_price = $products_price - $products_discount - $coupon_discount;

            $order_tax   = $this->calculate_payment_tax($order_data->payment_method_id, $final_price);

            $final_price_with_tax = $final_price + $order_tax;


            $price_data  = array(
                                    'total'           => $products_price,
                                    'final_total'     => $final_price_with_tax,
                                    'discount'        => $products_discount,
                                    'coupon_discount' => $coupon_discount,
                                    'tax'             => $order_tax,
                                    'items_count'     => $items_count
                                );

            $this->CI->orders_model->update_order_data($order_id, $price_data);

            return $final_price_with_tax;
        }
        else
        {
            return false;
        }
    }

    public function check_if_order_coupon_is_total($order_id)
    {
        $return_result     = false;
        $order_coupon_data = $this->CI->orders_model->get_order_coupon_data($order_id);

        if($order_coupon_data!='' && count($order_coupon_data) != 0)
        {
            if($order_coupon_data->product_or_category == 'total')
            {
                $return_result = true;
            }
        }

        return $return_result;
    }

    public function calculate_payment_tax($payment_option_id, $total)
    {
        if($payment_option_id == 0) return 0;
        if($total == 0) return 0;

        $option_data = $this->CI->payment_methods_model->get_option_data($payment_option_id);

        $tax_percent = round(($option_data->extra_fees_percent * $total), 2)/ 100;
        $tax         = $tax_percent + $option_data->extra_fees;

        return $tax;
     }

     public function coupon_discount($coupon_code, $order_id)
     {

        $lang_id       = $this->CI->data['active_language']->id;
        $order_data    = $this->CI->orders_model->get_order_data($order_id);
        $order_details = $this->CI->orders_model->get_order_details($order_id, $lang_id);
        $final_price   = $order_data->final_total;
        $country_id    = $order_data->country_id;
        //$session_id    = $order_data->session_id;
        $user_id       = $order_data->user_id;
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
                // 'total price for order is not enough to use this coupon';
            }

            $total_discount = $this->_check_coupon($order_details, $coupon_data, $order_id);

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

    private function _check_coupon($order_details, $coupon_data, $order_id)
    {
        $order_total_discount   = 0;
        $coupon_products_exist  = false;
        $order_coupon_products  = array();

        $order_data  = $this->CI->orders_model->get_order_data($order_id);
        $user_id     = $order_data->user_id;


        //coupon type : total
        if($coupon_data->product_or_category == 'total')
        {
            # if total coupon will be applied on final total price of cart,
            # so the qty of the products will be consered equals 1

            $price = $order_data->total - $order_data->discount;

            $total_price_after_discount   = $this->_calculate_amount($coupon_data->discount_type, $price, 1, $coupon_data->discount);
            $order_total_discount         = $price - $total_price_after_discount;

            $price_data = array(
                                   'coupon_discount'   => $order_total_discount,
                                   'final_total'       => $total_price_after_discount
                               );

            $this->CI->orders_model->update_order_data($order_id, $price_data);

            $coupon_products_exist  = true;

        }
        else
        {
            foreach($order_details as $content)
            {
                $product_price_after_discount = 0;
                $price = $content->final_price * $content->qty;

                if($coupon_data->product_or_category == 'category')     //coupon type : category
                {
                    $coupon_cats_ids = $this->CI->coupon_codes_model->get_coupon_cats_ids($coupon_data->id);

                    if(in_array($content->cat_id, $coupon_cats_ids))
                    {
                        $product_price_after_discount = $this->_calculate_amount($coupon_data->discount_type, $price, $content->qty, $coupon_data->discount);

                        $discount_on_product              = $price - $product_price_after_discount;

                        $content->{'cat_coupon'}          = true;
                        $content->{'discount_on_product'} = $discount_on_product;
                        $order_coupon_products[]          = $content;
                        $one_product_discount             = $discount_on_product / $content->qty;
                        $one_product_final_price          = $product_price_after_discount / $content->qty;

                        $order_product_data               = array(
                                                                   'coupon_discount' => $one_product_discount  ,
                                                                   'final_price'     => $one_product_final_price
                                                                 );

                        $this->CI->orders_model->update_order_product($order_id, $content->product_id, $order_product_data);

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
                        $order_coupon_products[]          = $content;
                        $one_product_discount             = $discount_on_product / $content->qty;
                        $one_product_final_price          = $product_price_after_discount / $content->qty;

                        $order_product_data               = array(
                                                               'coupon_discount' => $one_product_discount  ,
                                                               'final_price'     => $one_product_final_price
                                                           );

                        $this->CI->orders_model->update_order_product($order_id, $content->product_id, $order_product_data);

                        $coupon_products_exist = true;
                    }

                }

                $order_total_discount += ($price - $product_price_after_discount);
            }

            $this->update_order_total_prices($order_id);
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
                                        'total_discount'   =>  $order_total_discount          ,
                                       	'order_id'         =>  $order_id                      ,
                                        'unix_time'        =>  time()
                                      );

        $this->CI->coupon_codes_model->insert_coupon_uses_data($coupon_code_user_data);

        $coupon_code_user_id  = $this->CI->db->insert_id();

        if($coupon_data->product_or_category != 'total')
        {
            foreach($order_coupon_products as $discount_product)
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

    /********************************************************************************************/
    /*******************************Insert Order Functions***************************************/

    public function insert_order($user_id, $lang_id, $shopping_cart_id)
    {

        $msg                            = '';
        $form                           = '';
        //$order_bank_name                = '';
        //$order_bank_number              = '';
        $is_first_order                 = 0;
        $first_order_status             = 0;
        $first_order_error_msg          = '';
        $error_msg                      = '';
        $email_msg                      = '';
        $sms_msg                        = '';
        $all_stores_names               = '';

        $is_wholesaler                  = false;
        $max_orders                     = false;
        $first_order_problem            = false;
        $guest                          = false;
        $order_error                    = false;
        $order_data                     = array();
        $first_order_user_data          = array();

        $cart_data                      = $this->CI->shopping_cart_model->get_cart_row_data($shopping_cart_id);
        $cart_contents                  = $this->CI->shopping_cart->get_cart_contents($shopping_cart_id);

        $settings                       = $this->CI->user_bootstrap->get_settings();
        $wholesaler_customer_group_id   = $settings->wholesaler_customer_group_id;

        $user_data                      = $this->CI->user_model->get_row_data($user_id);

        /*
        ** $qty_status_array is an array variable of :
        ** $qty_status_array[$key]      => 'product_id-cart_product_id'
        ** $qty_status_array[$value]    => product availability status
        **/
        $qty_status_array = $this->CI->shopping_cart->check_cart_products_quantities();

        /**
         * 0 => no_stock
         * 2 => max_qty_per_user_discount_reached
         * 3 => product_not_exist_in_country
         * 4 => max_products_per_order_reached
        */

        if(in_array(0, $qty_status_array))
        {

            $error_msg      = lang('no_stock');
            $order_error    = true;
        }
        elseif(in_array(2, $qty_status_array))
        {
            $error_msg      = lang('max_qty_per_user_discount_reached');
            $order_error    = true;
        }
        elseif(in_array(3, $qty_status_array))
        {
            $error_msg      = lang('product_not_exist_in_country');
            $order_error    = true;
        }
        elseif(in_array(4, $qty_status_array))
        {
            $error_msg      = lang('max_products_per_order_reached');
            $order_error    = true;
        }


        //if($cart_data->total_price == 0 || $cart_data->final_total_price_with_tax == 0)
        if($cart_data->final_total_price_with_tax == 0)
        {
            $error_msg      = 'min order total';
            $order_error    = true;
        }

        if($order_error)
        {
            $this->status = 0;
        }
        else
        {
            // if guest
            if($user_id == 0)//(!$this->user_bootstrap->is_logged_in())
            {
                $guest = true;
                $first_order_array = $this->_check_first_time_order($user_id);

                $first_order_error_exist = $first_order_array[0];
                $first_order_error_msg   = $first_order_array[1];
                $first_order_user_id     = $first_order_array[2];
                $first_order_status      = $first_order_array[3];


                if($first_order_user_id != 0)
                {
                    $user_id = $first_order_user_id;
                }

                $is_first_order = 1;

                if($first_order_status != 1)//if($first_order_status == 2 || $first_order_status == 3 || $first_order_status == 4 || $first_order_status == 0)
                {
                    $first_order_problem = true;
                }

                if($first_order_status == 2)
                {
                    $first_order_user_data = $first_order_array[4];
                    $user_country          = $this->CI->users_model->get_nationality_name($first_order_array[4]->Country_ID, $lang_id);

                    $first_order_user_data->{'country'} = $user_country;
                }
            }
            else
            {
                $user_customer_group = $user_data->customer_group_id;

                if($user_customer_group == $wholesaler_customer_group_id)
                {
                    $is_wholesaler  = true;
                }
            }

            if($cart_contents && !$first_order_problem)
            {
                $notes      = '';
                $start_time = time();
                $end_time   = $start_time - (60 * 60 * 24);

                $user_orders_count        = $this->CI->orders_model->count_user_orders_per_day($start_time, $end_time, $user_id);
                $user_customer_group_data = $this->CI->customer_groups_model->get_user_customer_group_data($user_id);

                if($user_customer_group_data->max_orders_per_day > $user_orders_count || $user_customer_group_data->max_orders_per_day == 0)
                {
                    $country_id   = $cart_data->country_id;

                    $payment_method_id = intval(strip_tags($this->CI->input->post('payment_option_id', TRUE)));

                    if(isset($_POST['notes']) && $_POST['notes'] != '')
                    {
                        $notes = strip_tags($this->CI->input->post('notes', TRUE));
                    }

                    if($user_id != 0)
                    {
                        $bank_id = 0;
                        $voucher = '';
                        $bank_account_name   = '';
                        $bank_account_number = '';

                        $secret_key = $this->CI->config->item('new_encryption_key');
                        $secret_iv  = $user_id;

                        $order_status_id  = $this->CI->order_status_model->get_status_id($payment_method_id, 'payment_methods');


                        //pocket
                        if($payment_method_id == 1)
                        {
                            $template_payment_method = lang('pocket_money');
                        }
                        elseif($payment_method_id == 2) //reward points
                        {
                            $template_payment_method = lang('reward_points');
                        }
                        elseif($payment_method_id == 7) //voucher
                        {
                            $voucher = strip_tags($this->CI->input->post('voucher', TRUE));
                            $template_payment_method = lang('voucher');
                        }
                        elseif($payment_method_id == 3) //Banks
                        {
                            $bank_id = intval($this->CI->input->post('bank_id', TRUE));

                            // insert user bank account data
                            /*$bank_accounts_names   = $this->CI->input->post('account_name', TRUE);
                            $bank_accounts_numbers = $this->CI->input->post('account_number', TRUE);

                            foreach($bank_accounts_names as $bank_number=>$user_bank_account_name)
                            {
                                $this->CI->user_bank_accounts_model->delete_bank_account($bank_number, $user_id);

                                $data    = array(
                                                    'user_id'        => $user_id ,
                                                    'bank_id'        => $bank_number ,
                                                    'account_name'   => strip_tags($user_bank_account_name) ,
                                                    'account_number' => strip_tags($bank_accounts_numbers[$bank_number])
                                                );

                                $this->CI->user_bank_accounts_model->insert_user_account_data($data);

                                //$bank_account_name   = $bank_accounts_names[$payment_method_id];
                                //$bank_account_number = $bank_accounts_numbers[$payment_method_id];
                            }

                            $bank_data      = $this->CI->bank_accounts_model->get_bank_data($bank_id, $lang_id);
                            $user_bank_data = $this->CI->user_bank_accounts_model->get_user_bank_data($bank_id, $user_id);

                            $order_bank_name   = $user_bank_data->account_name;
                            $order_bank_number = $user_bank_data->account_number;
                            */
                            $bank_data = $this->CI->bank_accounts_model->get_bank_data($bank_id, $lang_id);
                            $template_payment_method = lang('bank_name')." : ".$bank_data->bank."<br>";//.lang('bank_account_name')." : ".$order_bank_name."<br>".lang('bank_account_number')." : ".$order_bank_number;
                        }
                        // else if( in_array($payment_method_id, array(13, 16)))// Basic Code
                        else if( in_array($payment_method_id, array(13, 15, 16, 17, 18, 19, 20, 21))) // Mrzok Edit = to add MyFatoora payment methods  , hyperpay stc pay , Moyasar
                        {
                            if($payment_method_id != 10)
                            {
                                $order_status_id         = 8;
                            }
                            $template_payment_method = $this->CI->payment_methods_model->get_payment_method_name($payment_method_id, $lang_id);
                            
                              //generate payment form for payment gateways only
                            $products_names = '';//$product_details->title;
                            $form = $this->_generate_payment_form($payment_method_id, $cart_data->id, $orders_total, $cart_data->currency_symbol, $products_names, $lang_id, $user_id, 1);

                            $insert_order = 0;

                            $output = array(
                                               1,
                                               'success',
                                                $form,
                                               
                                           );
                                           
                            return json_encode($output);
                            
                        }
                        else
                        {
                            $template_payment_method = $this->CI->payment_methods_model->get_payment_method_name($payment_method_id, $lang_id);
                            
                        }


                        // check coupon code
                        if($cart_data->coupon_discount != 0)
                        {
                            $this->CI->shopping_cart->reset_cart_coupon();
                        }

                        $currency_symbol = $cart_data->currency_symbol;

                        // type = 1 Delivery
                        // type = 2 recieve from shop
                        $branch_id      = 0;
                        $delivery_lng   = '';
                        $delivery_lat   = '';

                        if(isset($_POST['shipping_type']) && $_POST['shipping_type'] == 2)
                        {
                            $branch_id = intval($this->CI->input->post('branch_id', TRUE));
                            if(isset($_POST['shipping_lng']) && isset($_POST['shipping_lat']))
                            {
                                $delivery_lng = strip_tags($this->CI->input->post('shipping_lng',TRUE));
                                $delivery_lat = strip_tags($this->CI->input->post('shipping_lat',TRUE));
                            }
                            else
                            {
                                $delivery_lat = $cart_data->shipping_lat;
                                $delivery_lng = $cart_data->shipping_lng;
                            }
                        }

                        $cart_stores        = $this->CI->shopping_cart_model->get_cart_checked_stores($shopping_cart_id, $lang_id);
                        $cart_stores_count  = count($cart_stores);

                        $related_orders_ids = array();
                        $all_orders_total   = 0;

                        if(!$guest)
                        {
                            $username      = $user_data->first_name . ' ' . $user_data->last_name;
                            $emails[]      = $user_data->email;
                            $user_email    = $user_data->email;
                            $mobile_number = $user_data->phone;

                            if($user_data->stop_wholesaler_sms == 0)
                            {
                                $reciever_number = $mobile_number;
                            }
                            else
                            {
                                $reciever_number = '';
                            }
                        }
                        else
                        {
                            $username      = strip_tags($this->CI->input->post('name', TRUE));
                            $emails[]      = strip_tags($this->CI->input->post('email', TRUE));
                            $user_email    = strip_tags($this->CI->input->post('email', TRUE));
                            $phone         = strip_tags($this->CI->input->post('phone', TRUE));
                            $country_id    = intval($this->CI->input->post('country_id', TRUE));

                            $country       = $this->CI->cities_model->get_country_call_code($country_id);
                            $calling_code  = $country->calling_code;
                            $mobile_number = $calling_code.$phone;
                        }

                        $email_msg = '<div style="width:100%; display:block; overflow:hidden; overflow:hidden;">
                                        <table cellpadding="0" border="0" width="100%" style="text-align:center; font-size:14px;">
                                    	<tr style="background:#e1f0f8; font-size:14px;">
                                            <td>'.lang('thumbnail').'</td>
                                            <td>'.lang('product').'</td>
                                            <td>'.lang('name_of_store').'</td>
                                            <td>'.lang('quantity').'</td>
                                            <td>'.lang('price').'</td>
                                            <td>'.lang('total_price').'</td>
                                        </tr>';


                        foreach($cart_stores as $store)
                        {
                            $cart_stores_products = $this->CI->shopping_cart->get_cart_checked_products($cart_data->id, $store->store_id);

                            if(count($cart_stores_products) != 0)
                            {
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
                                 $optional_fields_cost  = $cart_products_result[4];

                                 $all_orders_total     += $cart_products_result[3];


                                 //insert order data
                                 $order_data      = array(
                                                            'user_id'             => $user_id                               ,
                                                            'store_id'            => $store->store_id                       ,
                                                            'agent'               => $_SERVER['HTTP_USER_AGENT']            ,
                                                            'payment_method_id'   => $payment_method_id                     ,
                                                            'bank_id'             => $bank_id                               ,
                                                            //'bank_account_name'   => $order_bank_name                       ,
                                                            //'bank_account_number' => $order_bank_number                     ,
                                                            'voucher'             => $voucher                               ,
                                                            'order_status_id'     => $order_status_id                       ,
                                                            'currency_symbol'     => $cart_data->currency_symbol            ,
                                                            'country_id'          => $cart_data->country_id                 ,
                                                            //'vat_percent'         => $this->settings->vat_percent           ,
                                                            //'vat_type'            => $this->settings->vat_type              ,

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

                                                            'gift_msg'            => $cart_data->gift_msg                   , // strip_tags($this->CI->input->post('gift_msg', True)),
                                                            'unix_time'           => time()                                 ,
                                                            'day'                 => date('d')                              ,
                                                            'month'               => date('m')                              ,
                                                            'year'                => date('Y')
                                                          );
                                                          
                                if(!$this->CI->orders_model->insert_order($order_data))
                                {
                                    $this->status = 0;
                                }
                                else
                                {
                                    $order_id = $this->CI->db->insert_id();
                                    $related_orders_ids[] = $order_id;

                                    // if payment method == pocket OR reward points reduce amount

                                    if($payment_method_id == 1)
                                    {
                                        $user_enc_old_balance = $user_data->user_balance;
                                        $user_old_balance     = $this->CI->encryption->decrypt($user_enc_old_balance, $secret_key, $secret_iv);

                                        if($user_old_balance >= $final_price_with_tax)
                                        {
                                            $user_new_balance                  = $user_old_balance - $final_price_with_tax;
                                            $user_enc_new_balance              = $this->CI->encryption->encrypt($user_new_balance, $secret_key, $secret_iv);
                                            $user_balance_data['user_balance'] = $user_enc_new_balance;
                                            $currency_symbol                   = $this->CI->currency->get_country_symbol($country_id);

                                            $this->CI->user_model->update_user_balance($user_id, $user_balance_data);

                                            $log_data = array(
                                                                'user_id'           => $user_id                                 ,
                                                                'order_id'          => $order_id                                ,
                                                                'payment_method_id' => $payment_method_id                       ,
                                                                'amount'            => $final_price_with_tax                    ,
                                                                'currency_symbol'   => $currency_symbol                         ,
                                                                'balance'           => $user_new_balance                        ,
                                                                'balance_status_id' => 1,  //withdraw from balance
                                                                'ip_address'        => $this->CI->input->ip_address()           ,
                                                                'unix_time'         => time()
                                                             );

                                            $this->CI->user_balance_model->insert_balance_log($log_data);
                                        }
                                        else
                                        {
                                            $this->status = 0;
                                            $insert_order_return = 'error_in_pocket';
                                        }

                                    }
                                    elseif($payment_method_id == 2) //reward points
                                    {
                                        $user_reward_points_value = $this->CI->user_bootstrap->get_user_reward_points_value();

                                        if($user_reward_points_value >= $final_price_with_tax)
                                        {
                                            $user_new_reward_points_value = $user_reward_points_value - $final_price_with_tax;
                                            $user_new_reward_points       = $this->CI->user_bootstrap->convert_user_reward_points($user_new_reward_points_value);

                                            $this->CI->user_bootstrap->encrypt_and_update_users_data($user_id, 'user_points', $user_new_reward_points);

                                        }
                                        else
                                        {
                                            $this->status = 0;
                                            $insert_order_return = 'error_in_reward_points';
                                        }

                                    }

                                    //insert order products

                                    $this->_insert_order_products($order_id, $country_id, $lang_id, $cart_stores_products, $user_id, $cart_data->session_id, $store->store_id);

                                    if($this->status == 1)
                                    {
                                        // insert recharge cards
                                        $this->_insert_charge_cards($shopping_cart_id, $order_id, $country_id, $payment_method_id, $secret_key, $secret_iv, $user_id);

                                        if($this->status == 1)
                                        {
                                            //generate products serials
                                            $this->_generate_order_serials($order_id, $lang_id, $cart_data->country_id);

                                            if($this->status == 1)
                                            {
                                                /// insert order , create notification , Add Affiliate
                                                $this->_insert_order_log($lang_id, $user_data, $order_id, $order_status_id, $secret_key, $secret_iv, $payment_method_id, $template_payment_method);

                                                if($this->status == 1)
                                                {
                                                    $products_names = '';
                                                    $status         = $this->CI->order_status_model->get_status_translation_name($order_status_id, $lang_id);
                                                    $order_products = $this->CI->orders_model->get_order_products($order_id, $lang_id);

                                                    foreach($order_products as $product)
                                                    {
                                                        if($product->product_id == 0)
                                                        {
                                                            $products_names .= lang('recharge_card').'  '.$product->final_price."   ";
                                                        }
                                                        else
                                                        {
                                                            $products_names .= $product->title."   ";
                                                        }
                                                    }



                                                    $template_data = array();

                                                    if($order_status_id != 1)
                                                    {
                                                        //send notification


                                                        $store_name     = $this->CI->stores_model->get_store_name($store->store_id, $lang_id);

                                                        $all_stores_names .= $store_name.', ';


                                                        foreach($order_products as $product)
                                                        {
                                                            if($product->product_id != 0)
                                                            {
                                                                $product_data   = $this->CI->products_model->get_row_data($product->product_id, $lang_id);
                                                                $product_name   = $product_data->title;
                                                                $img_path       = $this->data['images_path'].$product_data->image;// $this->data['images_path'].$product_data->image;

                                                                $email_msg .= '<tr>
                                                                                    <td><img src="'.$img_path.'" width="50" height="50" style=" display:block; margin:5px auto;" alt="'.$product_name.'"/></td>
                                                                                    <td>'.$product_name.'</td>
                                                                                    <td>'.$store_name.'</td>
                                                                                    <td>'.$product->qty.'</td>
                                                                                    <td>'.$product->final_price.' '.$currency_symbol.'</td>
                                                                                    <td>'.$product->final_price * $product->qty.' '.$currency_symbol.'</td>
                                                                               </tr>';

                                                                $sms_msg   .= lang('product').': '.$product_name.'--';
                                                            }
                                                            else
                                                            {
                                                                $userdata         = $this->CI->user_model->get_row_data($user_id);
                                                                $secret_key       = $this->CI->config->item('new_encryption_key');
                                                                $secret_iv        = $userdata->id;
                                                                $enc_user_balance = $userdata->user_balance;
                                                                $user_balance     = $this->CI->encryption->decrypt($enc_user_balance, $secret_key, $secret_iv);

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

                                                        $email_msg .= '
                                                                       <tr>
                                                                        <td colspan="3"></td>
                                                                        <td><span>'.lang('final_total').'</span></td>
                                                                        <td colspan="2"><span>'.$final_price_with_tax.' '.$currency_symbol.'</span></td>
                                                                       </tr>';



                                                    }

                                                    // check coupon
                                                    $cart_coupons = $this->CI->coupon_codes_model->get_cart_coupons_count($shopping_cart_id);

                                                    if($cart_coupons > 0)
                                                    {
                                                        $coupon_data = array(
                                                                                'cart_id'  => 0,
                                                                                'order_id' => $order_id
                                                                            );
                                                        $this->CI->coupon_codes_model->update_coupon_codes_using_cart_id($shopping_cart_id, $coupon_data);
                                                    }

                                                    $template_data  = array(
                                                            'username'              => $username                ,
                                                            'user_email'            => $user_email              ,
                                                            'user_phone'            => $mobile_number           ,
                                                            'store_name'            => $all_stores_names        ,
                                                            'products'              => $products_names          ,
                                                            'payment_method'        => $template_payment_method ,
                                                            'order_details_email'   => $email_msg               ,
                                                            'order_details_sms'     => $sms_msg                 ,
                                                            'status'                => $status                  ,
                                                            'order_time'            => date('Y/m/d H:i', time()),
                                                            'order_id'              => $order_id                ,
                                                            'logo_path'             => $this->data['images_path'].$this->settings->logo,
                                                            'user_order_link'       => base_url()."orders/order/view_order_details/".$order_id
                                                          );
                                                          

                                                $this->CI->notifications->create_notification('new_pending_order_added', $template_data, $emails, $mobile_number, $store->store_id);

                                                    ///////////////////////////////////////////////////////////////////
                                                }
                                            }
                                        }
                                    }
                                }
                            } // END if store has products condition
                        } // END stores loop

                        $email_msg .= '</table></div>';



                        $template_data  = array(
                                                'username'              => $username                ,
                                                'user_email'            => $user_email              ,
                                                'user_phone'            => $mobile_number           ,
                                                'store_name'            => $all_stores_names        ,
                                                'products'              => $products_names          ,
                                                'payment_method'        => $template_payment_method ,
                                                'order_details_email'   => $email_msg               ,
                                                'order_details_sms'     => $sms_msg                 ,
                                                'status'                => $status                  ,
                                                'order_time'            => date('Y/m/d H:i', time()),
                                                'order_id'              => $order_id                ,
                                                'logo_path'             => $this->data['images_path'].$this->settings->logo,
                                                'user_order_link'       => base_url()."orders/order/view_order_details/".$order_id
                                              );
                                              
                        $this->CI->notifications->create_notification('new_order_added', $template_data, $emails, $mobile_number);

                        $max_orders_number = $this->CI->orders_model->get_orders_max_number()->orders_number;
                        $new_orders_number = $max_orders_number + 1;

                        if($this->status == 1)
                        {
                            $updated_data = array(
                                                    'related_orders_ids' => json_encode($related_orders_ids),
                                                    'orders_number'      => $new_orders_number
                                                 );
                            $this->CI->orders_model->updated_orders_related_orders($related_orders_ids, $updated_data);

                            $form = $this->_generate_payment_form($payment_method_id, $order_id, $all_orders_total, $cart_data->currency_symbol, $shopping_cart_id, $user_email, $username, $guest);

                            $this->CI->shopping_cart->delete_cart_checked_products();
                        }

                    }
                }
                else
                {
                    $result     = 'max_orders_per_day';
                    $max_orders = true;
                }
            }
            else
            {
                $this->status == 0;
                $msg = lang('error_while_insert_order');
            }
        }

        if($this->status == 1)
        {
            $msg = 'success';
        }
        else
        {
            $msg = lang('error_while_insert_order');
        }

        if($order_error)
        {
            $insert_order_return = json_encode(array(0, $error_msg));
        }
        elseif($max_orders)
        {
            $insert_order_return = json_encode(array($result));
        }
        elseif($first_order_problem)
        {
            $insert_order_return = json_encode(array(0, $msg, '', $is_first_order, $first_order_status, $first_order_error_msg, $first_order_user_data));
        }
        else
        {
            $insert_order_return = json_encode(array($this->status, $msg, $form, $is_first_order, $first_order_status, $first_order_error_msg));
        }
        return $insert_order_return;

    }

    private function _check_first_time_order($user_id)
    {
        $error_msg      = '';
        $errors_exist   = false;
        $status         = 0;
        $reg_user_data  = array();

        /*
              Status
            0- Error
            1- insert order
            2- sign up (first_order)
            3- sign in
        */

        //set validation rules
        $this->CI->form_validation->set_rules('email', lang('email'), 'required|valid_email');
        $this->CI->form_validation->set_rules('phone', lang('phone'), 'required|integer');
        $this->CI->form_validation->set_rules('country_id', lang('country'), 'required');
        $this->CI->form_validation->set_rules('name', lang('name'), 'required');

        $this->CI->form_validation->set_message('integer', lang('is_integer').' : %s');
        $this->CI->form_validation->set_error_delimiters('<div style="color: red;">', '</div>');

        if ($this->CI->form_validation->run() == FALSE)
        {
           $error_msg    = validation_errors();
           $errors_exist = true;
           $status       = 0;
        }
        else
        {
            $email          = strip_tags($this->CI->input->post('email', TRUE));
            $user_phone     = strip_tags($this->CI->input->post('phone', TRUE));
            $name           = strip_tags($this->CI->input->post('name', TRUE));
            $country_id     = strip_tags($this->CI->input->post('country_id', TRUE));

            $country        = $this->CI->cities_model->get_country_call_code($country_id);
            $calling_code   = $country->calling_code;
            $phone          = $calling_code.$user_phone;

            $user_email_count = $this->CI->user_model->count_user_data_exist('email', $email);
            $user_phone_count = $this->CI->user_model->count_user_data_exist('phone', $phone);

            if($user_email_count == 0 && $user_phone_count == 0)
            {
                $new_user_customer_group_id = $this->CI->config->item('new_user_customer_group_id');

                $additional_data = array(
                                          'phone'             => $phone,
                                          'first_name'        => $name ,
                                          'Country_ID'        => $country_id,
                                          'customer_group_id' => $new_user_customer_group_id,
                                          'first_order'       => 1
                                        );

                $group    = array('id' => 2);
                $user_id  = $this->CI->ion_auth_model->register($name, '', $email, $additional_data, $group);

                if($user_id)
                {
                    $this->data['user_id'] = $user_id;
                    //$this->data['user']    = $reg_user_data;
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

                $user_count = $this->CI->user_model->get_user_data_by_fields($conditions_array);

                if($user_count == 1)
                {
                    $user_data = $this->CI->user_model->get_user_data_by_field('email', $email);

                    /*
                    if($user_data->first_order == 1)
                    {
                        $reg_user_data = $user_data;
                        $status        = 2;  // sign up
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
                    $user_data_by_email = $this->CI->user_model->get_user_data_by_field('email', $email);
                    $user_data_by_phone = $this->CI->user_model->get_user_data_by_field('phone', $phone);

                    if(count($user_data_by_email) != 0)
                    {
                        /*if($user_data_by_email->first_order == 1)
                        {
                            $error_msg    = lang('error_in_phone_to_complete_register');
                            $status       = 4;    // message
                            $errors_exist = true;
                        }
                        else
                        */
                        //{
                            //$status     = 3;   // sign in
                            //$error_msg  = 'sign in';
                            $user_id = $user_data_by_email->id;
                            $this->data['user_id'] = $user_data_by_email->id;
                            $status = 1;
                        //}
                    }
                    else if(count($user_data_by_phone) != 0)
                    {
                        /*if($user_data_by_phone->first_order == 1)
                        {
                            $error_msg    = lang('error_in_email_to_complete_register');
                            $status       = 4;    // message
                            $errors_exist = true;
                        }
                        else
                        */
                        //{
                            //$status     = 3;   // sign in
                            //$error_msg  = 'sign in';
                            $user_id = $user_data_by_phone->id;
                            $this->data['user_id'] = $user_data_by_phone->id;
                            $status = 1;
                        //}
                    }

                }
            }
        }

        /* first order status
        *  1 => success
        *  2 => sign up
        *  3 => sign in
        *  4 => message
        */

        return array($errors_exist, $error_msg, $user_id, $status, $reg_user_data);
    }

    private function _insert_order_products($order_id, $country_id, $lang_id, $cart_store_contents, $user_id, $session_id, $store_id )
    {
        
        foreach($cart_store_contents as $content)
        {
            if($content->product_id != 0)
            {
                $product_data  = $this->CI->products_model->get_product_country_data($content->product_id, $country_id);

                if($product_data->quantity_per_serial == 1)
                {
                    $product_cost = $product_data->average_cost * $content->qty;
                }
                else
                {
                    $product_cost = $product_data->cost * $content->qty;
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
                                               'order_id'           => $order_id                 ,
                                               'store_id'           => $content->store_id        ,
                                               'type'               => 'product'                 ,
                                               'product_id'         => $content->product_id      ,
                                               'cat_id'             => $content->cat_id          ,
                                               'qty'                => $content->qty             ,
                                               'price'              => $content->price           ,
                                               'final_price'        => $final_price              ,
                                               'vat_type'           => $content->vat_type        ,
                                               'vat_percent'        => $content->vat_percent     ,
                                               'vat_value'          => $content->vat_value       ,
                                               'discount'           => $content->discount        ,
                                               'coupon_discount'    => $content->coupon_discount ,
                                               'reward_points'      => $product_data->reward_points * $content->qty ,
                                               'purchased_cost'     => $product_cost ,
                                               'optional_fields_cost' => $content->optional_fields_cost,
                                               'unix_time'          => time()
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


                    $product_details    = $this->CI->products_model->get_product_row_details($content->product_id, $lang_id, $country_id);
                    $product_price_data = $this->CI->products_lib->get_product_price_data($product_details);

                    $product_discount_log_data = array(
                                                          'user_id'     => $user_id                 ,
                                                          'session_id'  => $session_id              ,
                                                          'discount_id' => $product_price_data[6]   ,
                                                          'order_id'    => $order_id                ,
                                                          'product_id'  => $content->product_id     ,
                                                          'store_id'    => $content->store_id       ,
                                                          'qty'         => $content->qty            ,
                                                          'unix_time'   => time()
                                                      );

                    $this->CI->products_model->insert_product_discount_log($product_discount_log_data);

                }

                if($product_details->quantity_per_serial == 1)
                {
                    $product_quantity     = $this->CI->products_model->count_product_available_quantity($content->product_id, $country_id);
                    $new_product_quantity = $product_quantity - $content->qty;
                    $updated_amount       = array('product_quantity' => $new_product_quantity);

                    $this->CI->products_model->update_product_country_amount($updated_amount, $content->product_id, $country_id);

                    $min_stock            = $this->CI->config->item('min_product_stock');
                    $available_qty        = $product_quantity - $min_stock;
                    $new_product_quantity = $available_qty - $content->qty;


                    if($new_product_quantity == 0)
                    {
                        $product_name  = $this->CI->products_model->get_product_name($content->product_id, $lang_id);
                        $emails[]      = $this->CI->config->item('email');
                        $mobile_number = $this->CI->config->item('mobile');
                        $template_data = array('product_name'=>$product_name);

                        $this->CI->notifications->create_notification('product_quantity_less_than_threshold', $template_data, $emails, $mobile_number, $store_id);
                    }
                }
            }
        }
    }

    private function update_user_optional_fields_products($cart_id, $cart_product_id, $updated_data)
    {
        $this->CI->products_model->update_product_optional_fields_data($cart_id, $cart_product_id, $updated_data);
    }

    private function _insert_charge_cards($shopping_cart_id, $order_id, $country_id, $payment_method_id, $secret_key, $secret_iv, $user_id)
    {
       // insert recharge cards and account packages
        $recharge_cards = $this->CI->shopping_cart_model->get_recharge_cards_data($shopping_cart_id);

        if(count($recharge_cards)!=0)
        {

            foreach($recharge_cards as $card)
            {
                $order_product_data = array(
                                               'order_id'    => $order_id       ,
                                               'type'        => $card->type ,
                                               'package_id'  => 0,//$card->package_id,
                                               'product_id'  => 0               ,
                                               'cat_id'      => 0               ,
                                               'store_id'    => $card->store_id ,
                                               'qty'         => 1               ,
                                               'price'       => $card->price    ,
                                               'final_price' => $card->final_price,
                                               'unix_time'   => time()
                                           );

                $this->CI->orders_model->insert_order_products($order_product_data);

                $user_data        = $this->CI->user_model->get_row_data($user_id);
                $enc_user_balance = $user_data->user_balance;
                //$currency_data    = $this->CI->currency_model->get_country_currency_result($user_data->store_country_id);
                $order_data       = $this->CI->orders_model->get_order($order_id);

                $user_balance     = $this->CI->encryption->decrypt($enc_user_balance, $secret_key, $secret_iv);

                $log_data = array(
                                    'user_id'           => $user_id                     ,
                                    'order_id'          => $order_id                    ,
                                    'store_id'          => $card->store_id              ,
                                    'payment_method_id' => $payment_method_id           ,
                                    'amount'            => $card->price                 ,
                                    'currency_symbol'   => $order_data->currency_symbol ,
                                    'store_country_id'  => $country_id                  ,
                                    'balance'           => $user_balance                ,
                                    'balance_status_id' => 3,  //request to add balace
                                    'ip_address'        => $this->CI->input->ip_address(),
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

    private function _generate_order_serials($order_id, $display_lang_id, $country_id)
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
                    // Need to generate SKU for each serial acording selected (Product Optionals Options) and gather there sku parts to gether with product code to result the full SKU

                    
                    $serials_data = array(
                                            'order_id'          => $order_id            ,
                                            'product_id'        => $row->product_id     ,
                                            'store_id'          => $order_data->store_id,
                                            'product_serial_id' => $serial->id          ,
                                            'unix_time'         => time()               ,
                                            "full_sku"          => $full_product_code_options_sku , // assigning SKU to selected Order Serial , Mrzok Edit => 9/2021
                                            'order_product_id'  => $row->order_product_id // set order_product_id for inserted serial
                                         );

                    ////////serial status///////
                    ////0--->available
                    ////1--->pending
                    ////2--->sold
                    ////3--->invalid

                    if($order_data->order_status_id == 1)
                    {
                        $serial_status['serial_status'] = 2;
                    }
                    else
                    {
                        $serial_status['serial_status'] = 1;
                    }

                    $serial_status['sold_order_id'] = $order_id;

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
    private function _insert_order_log($lang_id, $user_data, $order_id, $order_status_id, $secret_key, $secret_iv, $payment_method_id, $template_payment_method)
    {
        $order_products = $this->CI->orders_model->get_order_all_products($order_id);
        $order_data     = $this->CI->orders_model->get_order_main_details($order_id, $lang_id);


        if($order_status_id == 1)
        {
            //reward points
            if(count($user_data) != 0)
            {
                $total_reward_points = 0;
                $user_points         = $this->CI->encryption->decrypt($user_data->user_points, $secret_key, $secret_iv);

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

                $this->CI->user_bootstrap->encrypt_and_update_users_data($user_data->id, 'user_points', $user_total_reward_points);
            }

            // Add Affiliate
            $this->_add_affiliate($user_data, $order_data);

            $serials_data         = $this->CI->orders_model->get_order_serials($order_id);
            $non_serials_products = $this->CI->orders_model->get_order_non_serials_products($order_id, $lang_id);

            //Send Approve Notification
            $this->send_approve_order_notification($user_data, $order_status_id, $lang_id, $serials_data, $non_serials_products, $order_data);

        }

        $this->_insert_order_log_data($order_id, $order_status_id);

    }

    private function _insert_order_log_data($order_id, $order_status_id)
    {
        $log_data = array(
                            'order_id'  => $order_id        ,
                            'status_id' => $order_status_id ,
                            'unix_time' => time()
                         );

        $this->CI->orders_model->insert_order_log($log_data);
    }

    private function _generate_payment_form($payment_method_id, $order_id, $final_total, $currency_symbol, $shopping_cart_id, $email, $customer_name, $guest)
    {
        $products_names = '';
        $cart_products  = $this->CI->shopping_cart->get_cart_products_translation($shopping_cart_id, $this->lang_id);

        foreach($cart_products as $product)
        {
            if($product->product_id == 0)
            {
                $products_names .= lang('recharge_card').'  '.$product->final_price."   ";
            }
            else
            {
                $products_names .= $product->product_name."   ";
            }
        }

        $form = '';
        if($payment_method_id == 4 || $payment_method_id == 8)   //payfort OR Sadad
        {
            if($payment_method_id == 8)
            {
                $payment_option = 'SADAD';
            }
            else
            {
                $payment_option = 'VISA';
            }

            $products_names = preg_replace("/[^A-Za-z0-9]/", '', $products_names);
            //Product name max length is 35
            $form = $this->CI->payfort->generate_form($order_id, $final_total, $currency_symbol, $payment_option, $email, '', $customer_name);

        }
        elseif($payment_method_id == 5)   // PayPal
        {
            $form = '<form action="'.base_url().'orders/payment_gateways/submit" method="post" class="pay_form">
                        <input name="order_id" value="'.$order_id.'" type="hidden"/>
                        <input name="type" value="paypal" type="hidden" />
                     </form>';
        }
        elseif($payment_method_id == 6)    //CashU
        {
            $form = $this->CI->cashu->fetch_form($order_id, $final_total, $currency_symbol, $products_names);
        }
        elseif($payment_method_id == 9)
        {
            $form = '<form action="'.base_url().'orders/payment_gateways/submit" method="post" class="pay_form">
                        <input name="order_id" value="'.$order_id.'" type="hidden"/>
                        <input name="type" value="knet" type="hidden" />
                     </form>';
        }
        elseif($payment_method_id == 11)     //SADAD
        {
             $form = '<form action="'.base_url().'orders/payment_gateways/submit" method="post" class="pay_form">
                        <input name="order_id" value="'.$order_id.'" type="hidden"/>
                        <input name="type" value="sadad" type="hidden" />
                     </form>';
        }
        elseif($payment_method_id == 12)     // HyperPay => SADAD
        {
            $form = '<form action="'.base_url().'orders/payment_gateways/submit" method="post" class="pay_form">
                        <input name="order_id" value="'.$order_id.'" type="hidden"/>
                        <input name="type" value="hyperpay_sadad" type="hidden" />
                     </form>';
        }
        elseif($payment_method_id == 13)     // HyperPay => VISA & MASTERCARD
        {
            $form = '<form action="'.base_url().'orders/payment_gateways/submit" method="post" class="pay_form">
                        <input name="order_id" value="'.$order_id.'" type="hidden"/>
                        <input name="type" value="hyperpay_visa" type="hidden" />
                     </form>';
        }
        elseif($payment_method_id == 15)     // MOYASAR
        {
            // generate form to add payment data
           //$form = $this->CI->moyasar->generate_form($order_id, $final_total, $currency_symbol, $payment_option, $email, '', $customer_name);
           $form = '<form action="'.base_url().'orders/payment_gateways/submit" method="post" class="pay_form">
                        <input name="order_id" value="'.$order_id.'" type="hidden"/>
                        <input name="type" value="moyasar" type="hidden" />
                     </form>';
        }
        elseif($payment_method_id == 16)     // HyperPay => MADA
        {
            $form = '<form action="'.base_url().'orders/payment_gateways/submit" method="post" class="pay_form">
                        <input name="order_id" value="'.$order_id.'" type="hidden"/>
                        <input name="type" value="mada" type="hidden" />
                     </form>';
        }
        elseif($payment_method_id == 17)     // MyFatoora => VISA & MASTERCARD
        {
            // generate form to add payment data
            $form = '<form action="'.base_url().'orders/payment_gateways/submit" method="post" class="pay_form">
                        <input name="order_id" value="'.$order_id.'" type="hidden"/>
                        <input name="type" value="myFatoora_visa" type="hidden" />
                     </form>';
        }
        elseif($payment_method_id == 18)     // MyFatoora => Apple Pay (mada)
        {
            // generate form to add payment data
            $form = '<form action="'.base_url().'orders/payment_gateways/submit" method="post" class="pay_form">
                        <input name="order_id" value="'.$order_id.'" type="hidden"/>
                        <input name="type" value="myFatoora_apple_pay_mada" type="hidden" />
                     </form>';
        }
        elseif($payment_method_id == 19)     // MyFatoora => Apple Pay
        {
            // generate form to add payment data
            $form = '<form action="'.base_url().'orders/payment_gateways/submit" method="post" class="pay_form">
                        <input name="order_id" value="'.$order_id.'" type="hidden"/>
                        <input name="type" value="myFatoora_apple_pay" type="hidden" />
                     </form>';
        }
        elseif($payment_method_id == 20)     // MyFatoora => Mada
        {
            // generate form to add payment data
            $form = '<form action="'.base_url().'orders/payment_gateways/submit" method="post" class="pay_form">
                        <input name="order_id" value="'.$order_id.'" type="hidden"/>
                        <input name="type" value="myFatoora_mada" type="hidden" />
                     </form>';
        }
        elseif($payment_method_id == 21)     // HyperPay => STC Pay
        {
            $form = '<form action="'.base_url().'orders/payment_gateways/submit" method="post" class="pay_form">
                        <input name="order_id" value="'.$order_id.'" type="hidden"/>
                        <input name="type" value="hyperpay_stc_pay" type="hidden" />
                     </form>';
        }
        else
        {
            if(! $guest)
            {
                //$form = '<form method="get" action="'.base_url().'orders/order/view_order_details/'.$order_id.'" class="pay_form"></form>';
                $form = '<form method="post" action="'.base_url().'orders/order/order_confirmation" class="pay_form">
                  <input type="hidden" name="order_id" value="'.$order_id.'" />
                </form>';
            }
            else
            {
                $form = '<form method="get" action="'.base_url().'User_login/" class="pay_form"></form>';
            }
        }
        return $form;
    }


      public function approve_order($order_id, $lang_id)
     {

        $status_id            = 1;

        $order_data           = $this->CI->orders_model->get_order_data($order_id);
        $order_products       = $this->CI->orders_model->get_order_all_products($order_id);
        $order_user_data      = $this->CI->user_model->get_row_data($order_data->user_id);
        $recharge_cards_count = $this->CI->orders_model->get_recharge_cards_count($order_id);

        $secret_key           = $this->CI->config->item('new_encryption_key');
        $secret_iv            = $order_data->user_id;

        if($recharge_cards_count > 0)
        {
            $recharge_cards_balance = $this->CI->orders_model->get_recharge_card($order_id);
            $new_balance = 0;

            foreach($recharge_cards_balance as $card)
            {
                if($card->recharge_card_used == 0)
                {
                    $new_balance += $card->price;

                    $card_updated_data['recharge_card_used'] = 1;
                    $this->CI->orders_model->update_product_order($order_id, $card->price, $card_updated_data);
                }
            }

            $payment_id = $order_data->payment_method_id;//$this->payment_methods_model->get_payment_method_name($order_data->payment_method_id, $lang_id);


            //user balance
            $enc_user_balance = $order_user_data->user_balance;
            if($enc_user_balance == '')
            {
                $user_new_balance = $new_balance;
            }
            else
            {
                $user_balance     = $this->CI->encryption->decrypt($enc_user_balance, $secret_key, $secret_iv);
                $user_new_balance = $user_balance + $new_balance;
            }


            $balance = $this->CI->encryption->encrypt($user_new_balance, $secret_key, $secret_iv);
            $user_new_balance_data['user_balance'] = $balance;

            $this->CI->user_model->update_user_balance($order_data->user_id, $user_new_balance_data);

            $currency_data = $this->CI->currency_model->get_country_currency_result($order_data->country_id);

            $log_data      = array(
                                    'user_id'           => $order_data->user_id,
                                    'order_id'          => $order_data->id,
                                    'payment_method_id' => $payment_id,
                                    'amount'            => $new_balance,
                                    'currency_symbol'   => $currency_data->currency_symbol,
                                    'store_country_id'  => $order_data->country_id,
                                    'balance'           => $user_new_balance ,
                                    'balance_status_id' => 2,  //add to the balance
                                    'unix_time'         => time()
                                  );

            $this->CI->user_balance_model->insert_balance_log($log_data);
        }

        //update product_serials value
        $serials_data         = $this->CI->orders_model->get_order_serials($order_id);
        $non_serials_products = $this->CI->orders_model->get_order_non_serials_products($order_id, $lang_id);


        if(count($serials_data) != 0)
        {
            foreach($serials_data as $serial)
            {
                $serial_data['serial_status'] = 2;
                $this->CI->products_serials_model->update_serial($serial->product_serial_id, $serial_data);

                $country_id = $order_data->country_id;

                foreach($order_products as $product)
                {
                    $product_qty = $this->CI->products_model->count_product_available_quantity($country_id, $product->product_id);
                    $product_updated_data['product_quantity'] = $product_qty;

                    $this->CI->products_model->update_product_countries($product->product_id, $country_id, $product_updated_data);
                }
            }
        }


        //insert user total reward points
        $enc_user_points     = $order_user_data->user_points;
        $user_points         = $this->CI->encryption->decrypt($enc_user_points, $secret_key, $secret_iv);

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
        $enc_reward_points        = $this->CI->encryption->encrypt($user_total_reward_points, $secret_key, $secret_iv);

        $user_data['user_points'] = $enc_reward_points;

        $this->CI->user_model->update_user($order_data->user_id, $user_data);

        // Add Affiliate
        $this->_add_affiliate($order_user_data, $order_data);

        // send notification
        $this->send_approve_order_notification($order_user_data, $status_id, $lang_id, $serials_data, $non_serials_products, $order_data);

        // update order status
        $order_updated_data['order_status_id'] = $status_id;
        $this->CI->orders_model->update_order_data($order_id, $order_updated_data);

        // insert in order log table
        $this->_insert_order_log_data($order_id, $status_id);
     }

     public function send_approve_order_notification($userdata, $status_id, $lang_id, $serials_data, $non_serials_products, $order_data)
     {
        // send notification
        $username = $userdata->first_name.' '. $userdata->last_name;
        $order_id = $order_data->id;

        if($username){$username = $username;}
        else{$username = lang('visitor');}

        $emails[]       = $userdata->email;

        if($userdata->stop_wholesaler_sms == 0)
        {
            $phone      = $userdata->phone;
        }
        else
        {
            $phone      = '';
        }

        $user_email     = $userdata->email;
        $status         = $this->CI->order_status_model->get_status_translation_name($status_id, $lang_id);
        $payment_method = $this->CI->payment_methods_model->get_row_data($order_data->payment_method_id, $lang_id);

        $new_serials_array  = array();
        $email_msg          = '';
        $sms_msg            = '';

        if(count($serials_data) != 0 || count($non_serials_products) != 0)
        {
            $email_msg = '<div style="width:100%; display:block; overflow:hidden; overflow:hidden;">
                            <table cellpadding="0" border="0" width="100%" style="text-align:center; font-size:14px;">
                            	<tr style="background:#e1f0f8; font-size:14px;">
                                    <td>'.lang('thumbnail').'</td>
                                    <td>'.lang('product').'</td>
                                    <td>'.lang('serial').'</td>
                                    <td>'.lang('price').'</td>
                                </tr>';
            $sms_msg  = '';

            if(count($serials_data) != 0)
            {
                foreach($serials_data as $serial)
                {
                    $product_serial = $this->CI->products_serials_model->get_products_serials_row($serial->product_serial_id);
                    $product_data   = $this->CI->products_model->get_row_data($serial->product_id, $lang_id);
                    $product_price  = $this->CI->orders_model->get_order_product_data($serial->product_id, $order_id);
                    $product_name   = $product_data->title;
                    $img_path       = $this->data['images_path'].$product_data->image;

                    $secret_key  = $this->CI->config->item('new_encryption_key');
                    $secret_iv   = md5('serial_iv');
                    $dec_serials = $this->CI->encryption->decrypt($product_serial->serial, $secret_key, $secret_iv);

                    $email_msg .= '<tr>
                                    	<td><img src="'.$this->data['images_path'].$product_data->image.'" width="50" height="50" style=" display:block; margin:5px auto;" alt="'.$product_name.'"/></td>
                                        <td>'.$product_name.'</td>
                                        <td>'.$dec_serials.'</td>
                                        <td>'.$product_price->final_price.' '.$order_data->currency_symbol.'</td>
                                   </tr>';

                    $sms_msg   .= lang('product').': '.$product_name.'--'.lang('serial').': '.$dec_serials.' ';
                }
            }

            if(count($non_serials_products) != 0)
            {
                foreach($non_serials_products as $product)
                {
                    $product_price  = $this->CI->orders_model->get_order_product_data($product->product_id, $order_id);
                    $email_msg .= '<tr>
                                    	<td><img src="'.$this->data['images_path'].$product->image.'" width="50" height="50" style=" display:block; margin:5px auto;" alt="'.$product->title.'"/></td>
                                        <td>'.$product->title.'</td>
                                        <td></td>
                                        <td>'.$product->final_price.' '.$order_data->currency_symbol.'</td>
                                   </tr>';

                    $sms_msg   .= lang('product').': '.$product->title.' ';
                }
            }


        }

        $cards_count = $this->CI->orders_model->get_recharge_cards_count($order_id);

        if($cards_count > 0)
        {
            $secret_key       = $this->CI->config->item('new_encryption_key');
            $secret_iv        = $userdata->id;
            $enc_user_balance = $userdata->user_balance;
            $user_balance     = $this->CI->encryption->decrypt($enc_user_balance, $secret_key, $secret_iv);

            $recharge_cards  = $this->CI->orders_model->get_recharge_card($order_id);

            foreach($recharge_cards as $card)
            {
                $email_msg .= '<tr><td>'.lang('recharge_card').' : '.$card->price.' '.$order_data->currency_symbol.'</td><td>'.lang('current_balance').' : '.$user_balance.' '.$order_data->currency_symbol.'</td></tr>';
                $sms_msg   .= lang('recharge_card').' : '.$card->price.' '.$order_data->currency_symbol.'  '.lang('current_balance').':'.$user_balance.' '.$order_data->currency_symbol;
            }
        }

         $email_msg .= '<tr>
                            <td colspan="2"></td>
                            <td><span>'.lang('final_total').'</span></td>
                            <td><span>'.$order_data->final_total.' '.$order_data->currency_symbol.'</span></td>
                           </tr>';

         $email_msg .= '</table></div>';

         $store_name   = $this->CI->stores_model->get_store_name($order_data->store_id, $lang_id);

        $template_data = array(
                                'unix_time'             => time()                                               ,
                                'username'              => $username                                            ,
                                'user_phone'            => $userdata->phone                                     ,
                                'user_email'            => $user_email                                          ,
                                'payment_method'        => $payment_method->name                                ,
                                'store_name'            => $store_name                                          ,
                                'status'                => $status                                              ,
                                'order_id'              => $order_data->id                                      ,
                                'logo_path'             => $this->data['images_path'].$this->settings->logo                                ,
                                'img_path'              => base_url().'assets/template/site/img/'               ,
                                'order_time'            => date('Y/m/d H:i', $order_data->unix_time)            ,
                                'order_details_email'   => $email_msg                                           ,
                                'order_details_sms'     => $sms_msg
                              );

        $this->CI->notifications->create_notification('pending_order_completed', $template_data, $emails, $phone, $order_data->store_id);
     }

     public function canceled_orders_operations($order_id, $status_id)
     {
        $order_data     = $this->CI->orders_model->get_order($order_id);
        $order_products = $this->CI->orders_model->get_order_all_products($order_id);

        $secret_key     = $this->CI->config->item('new_encryption_key');
        $secret_iv      = $order_data->user_id;

        $user_id        = $order_data->user_id;
        $country_id     = $order_data->country_id;

        //if order payment method = balance ... return user balance
        if($order_data->payment_method_id == 1)
        {
            $user_old_balance = $this->get_user_balance($user_id);
            $user_new_balance = $user_new_balance + $order_data->final_total;

            $user_balance = $this->CI->encryption->encrypt($user_new_balance, $secret_key, $secret_iv);
            $user_new_balance_data['user_balance'] = $user_balance;

            $this->CI->user_model->update_user_balance($user_id, $user_new_balance_data);

        }
        else if($order_data->payment_method_id = 2)  //if order payment method = reward points ... return user reward points
        {
            $order_points = $this->convert_into_reward_points($order_data->country_id, $order_data->final_total);

            $user_old_points = $this->get_user_reward_points($order_data->user_id);
            $user_new_points = $order_points + $user_old_points;

            //$this->admin_bootstrap->encrypt_and_update_users_data($order_data->user_id, 'user_points', $user_new_points);

            $enc_points = $this->CI->encryption->encrypt($user_new_points, $secret_key, $secret_iv);
            $user_new_points_data['user_points'] = $enc_points;

            $this->CI->user_model->update_user_balance($user_id, $user_new_points_data);

        }

        //update product_serials value
        $serials_data = $this->CI->orders_model->get_order_serials($order_id);

        if(count($serials_data) != 0)
        {
            foreach($serials_data as $serial)
            {
                $serial_data['serial_status'] = 0;
                $this->CI->products_serials_model->update_serial($serial->product_serial_id, $serial_data);

                foreach($order_products as $product)
                {
                    $product_qty = $this->CI->products_model->count_product_available_quantity($product->product_id, $country_id);
                    $product_updated_data['product_quantity'] = $product_qty;

                    $this->CI->products_model->update_product_countries($product->product_id, $country_id, $product_updated_data);
                }
            }
        }

        // update order status
        $order_updated_data['order_status_id'] = $status_id;
        $this->CI->orders_model->update_order_data($order_id, $order_updated_data);

        // insert in order log table
        $this->_insert_order_log_data($order_id, 3);
     }

     public function get_user_balance($user_id)
     {
        $secret_iv        = $user_id;
        $secret_key       = $this->CI->config->item('new_encryption_key');
        $enc_user_balance = $this->user_model->get_row_data($user_id)->user_balance;

        $user_balance     = $this->CI->encryption->decrypt($enc_user_balance, $secret_key, $secret_iv);

        if($enc_user_balance == '')
        {
            $user_balance = 0;
        }

        return $user_balance;
     }

     public function convert_into_reward_points($country_id, $rewrd_points_value)
     {
        $country_reward_point_value = $this->CI->countries_model->get_reward_points($country_id);

        $user_reward_points         = round($rewrd_points_value / $country_reward_point_value, 2);

        return $user_reward_points;
     }

     public function get_user_reward_points($user_id)
     {
        $secret_iv         = $user_id;
        $secret_key        = $this->CI->config->item('new_encryption_key');
        $enc_reward_points = $this->CI->user_model->get_row_data($user_id)->user_points;

        $reward_points     = $this->CI->encryption->decrypt($enc_reward_points, $secret_key, $secret_iv);

        if($enc_reward_points == '')
        {
            $reward_points = 0;
        }

        return $reward_points;
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
            // Basic Code ... I do not know why we add $product->discount to $product->price we suppose to subtract it  : Mrzok Comment
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

     public function get_order_payment_methods($final_total, $country_id, $user_id, $lang_id, $user_data)
     {
        $secret_key           = $this->CI->config->item('new_encryption_key');
        $secret_iv            = $user_id;

        $wholesaler_pocket    = 0;
        $user_customer_group  = 0;

        $pay_by_bank          = true;
        $is_wholesaler        = false;
        $pay_by_pocket        = false;
        $pay_by_reward_points = false;

        $not_included_ids     = array();

        $settings                     = $this->CI->global_model->get_config();
        $wholesaler_customer_group_id = $settings->wholesaler_customer_group_id;

        $user_customer_group = $user_data->customer_group_id;

        if($user_customer_group == $wholesaler_customer_group_id)
        {
            $is_wholesaler  = true;
        }

        $enc_user_balance = $user_data->user_balance;

        $user_balance     = $this->CI->encryption->decrypt($enc_user_balance, $secret_key, $secret_iv);

        if($final_total <= $user_balance)
        {
            $pay_by_pocket = true;
            $pay_by_bank   = false;
        }

        if($is_wholesaler && $final_total <= $user_balance)
        {
            $wholesaler_pocket = 1;
        }



        // check reward points
        $enc_user_points   = $user_data->user_points;
        $user_points       = $this->CI->encryption->decrypt($enc_user_points, $secret_key, $secret_iv);
        $point_value       = $this->CI->countries_model->get_reward_points($country_id);
        $user_points_value = $user_points * $point_value;

        if($final_total <= $user_points_value)
        {
            $pay_by_reward_points = true;
        }

        $bank_accounts       = $this->CI->user_bank_accounts_model->get_bank_accounts_result($lang_id, $user_id);

        if(!$pay_by_pocket)
        {
            $not_included_ids[] = 1;
        }

        if(!$pay_by_reward_points)
        {
            $not_included_ids[] = 2;
        }

        if(!$pay_by_bank)
        {
            $not_included_ids[] = 3;
        }

        $payment_options = $this->CI->payment_methods_model->get_available_payment_options($lang_id, $user_customer_group, $not_included_ids, $wholesaler_pocket);

        return $payment_options;

    }

    public function add_order_bill($new_order_id, $amount, $notes, $new_order_data, $admin=0)
    {
        $this->CI->load->model('pay_later_model');

        $order_data = $this->CI->orders_model->get_order($new_order_data->main_order_id);
        $order_id   = $new_order_data->main_order_id;

        $new_order_paid_amount = $amount + $order_data->paid_amount;
        $new_order_rest_amount = $order_data->rest_amount - $amount;

        $data   = array(
                           'added_by'    => $this->CI->data['user_id'],
                           'order_id'    => $order_id,
                           'amount'      => $amount,
                           'notes'       => $notes,
                           'order_total' => $order_data->final_total,
                           'order_rest'  => $new_order_rest_amount ,
                           'order_paid'  => $new_order_paid_amount ,
                           'unix_time'   => time()
                       );

           if($this->CI->pay_later_model->insert_bill($data))
           {
               //update order data
               $order_new_data = array(
                 'paid_amount' => $new_order_paid_amount,
                 'rest_amount' => $new_order_rest_amount
               );

               $this->CI->orders_model->update_order_data($order_id, $order_new_data);

               return true;
            }
            else
            {
                return false;
            }
    }




}
