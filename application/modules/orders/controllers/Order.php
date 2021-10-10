<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Order extends CI_Controller
{
    public $status=1;
    public $data = array();

    public function __construct()
    {
        parent::__construct();

        $this->load->library('orders');
        $this->load->library('encryption');
        $this->load->library('shopping_cart');
        $this->load->library('pagination');
        $this->load->library('notifications');
        $this->load->library('products_lib');
        $this->load->library('amazon_s3_uploads');

        $this->config->load('encryption_keys');

        $this->load->model('orders_model');
        $this->load->model('order_status_model');
        $this->load->model('pay_later_model');
        $this->load->model('users/user_model');
        $this->load->model('users/cities_model');
        $this->load->model('products/products_model');
        $this->load->model('optional_fields/optional_fields_model');
        $this->load->model('products/products_serials_model');
        $this->load->model('affiliate/affiliate_log_model');
        $this->load->model('affiliate/admin_affiliate_model');
        $this->load->model('coupon_codes/coupon_codes_model');
        $this->load->model('payment_options/user_balance_model');
        $this->load->model('payment_options/bank_accounts_model');
        $this->load->model('payment_options/payment_methods_model');
        $this->load->model('shopping_cart/user_bank_accounts_model');
        $this->load->model('wrapping/admin_wrapping_model');

        require(APPPATH . 'includes/front_end_global.php');

        $user_id    = $this->user_bootstrap->get_user_id();
        $session_id = $this->data['session_id'];
        $ip_address = $this->input->ip_address();
        $country_id = $this->data['country_id'];
        $lang_id    = $this->data['lang_id'];

        $this->shopping_cart->set_user_data($user_id, $session_id, $ip_address, $country_id, $lang_id);
    }



    public function insert_order()
    {
        $error_msg        = '';
        $order_error      = false;

        $user_id          = $this->data['user_id'];
        $lang_id          = $this->data['lang_id'];
        $shopping_cart_id = $this->shopping_cart->get_cart_id();
        $cart_data  = $this->shopping_cart->shopping_cart_data();

        //insert log
        $this->visits_log->add_log(27, 67, 310, $user_id);

        $this->form_validation->set_rules('payment_option_id', ('payment_option'), 'required|callback_payment_validation');
        /*if($cart_data->needs_shipping == 1)
        {
          $this->form_validation->set_rules('shipping_type', lang('shipping_way'), 'required');
        }
        */

        if(isset($_POST['send_as_gift']))
        {

          $this->form_validation->set_rules('ribbon_id', lang('ribbon'), 'required|greater_than[0]');
          $this->form_validation->set_rules('wrapping_id', lang('wrapping'), 'required|greater_than[0]');
          $this->form_validation->set_rules('box_id', lang('box'), 'required|greater_than[0]');

        }

        //$shipping_type = intval($this->input->post('shipping_type', TRUE));
        $shipping_type = $cart_data->shipping_type;

        if($shipping_type == 2)
        {
            $this->form_validation->set_rules('branch_id', lang('branch'), 'required');
        }
        elseif($shipping_type == 1)
        {
            $this->form_validation->set_rules('shipping_name', lang('name'), 'required');
            $this->form_validation->set_rules('shipping_phone', lang('phone'), 'required');
            $this->form_validation->set_rules('shipping_city', lang('shipping_city'), 'required');
            $this->form_validation->set_rules('shipping_address', lang('address'), 'required');
            $this->form_validation->set_rules('shipping_lat', lang('delivery_location'), 'required');
        }
        elseif($shipping_type == 3)
        {
            $this->form_validation->set_rules('shipping_name', lang('name'), 'required');
            $this->form_validation->set_rules('shipping_phone', lang('phone'), 'required');
            $this->form_validation->set_rules('shipping_company_id', lang('shipping_company'), 'required');
        }
        elseif($shipping_type == 4)
        {
            //$this->form_validation->set_rules('address_id', lang('user_address'), 'required');
        }

        $this->form_validation->set_message('required', lang('required').' : %s ');
        $this->form_validation->set_message('greater_than', lang('required').' : %s ');

        if($this->form_validation->run() == FALSE)
  		{
            $error_msg      = validation_errors();
            $order_error    = true;
        }


        if(!$order_error)
        {
            if($this->data['is_logged_in'])
            {
                $return = $this->orders->insert_order($user_id, $lang_id, $shopping_cart_id);
            }
            else
            {
                $return = json_encode(array('login_redirect'));
            }
        }
        else
        {
            $return = json_encode(array(0, $error_msg));
        }

        echo $return;
    }

    public function payment_validation($payment_method_id)
     {
        $user_id    = $this->data['user_id'];

        //user available payment methods
        $available_options_ids = $this->get_payment_methods_ids($user_id);

        if(in_array($payment_method_id, $available_options_ids))
        {
            return true;
        }
        else
        {
            $this->form_validation->set_message('payment_validation', lang('payment_method_validation'));
            return false;
        }

     }


    public function get_payment_methods_ids($user_id)
    {
        $charge_card_in_cart   = $this->shopping_cart->count_charge_cards_in_cart();
        $final_total           = $this->shopping_cart->final_total();
        $cart_data             = $this->shopping_cart->shopping_cart_data();
        $currency              = $this->countries_model->get_country_symbol($cart_data->country_id);
        $country_id            = $this->data['country_id'];

        $secret_key            = $this->config->item('new_encryption_key');
        $secret_iv             = $user_id;

        $wholesaler_pocket     = 0;
        $user_customer_group   = 0;
        $use_pocket            = 0;

        $pay_by_bank           = true;
        $is_wholesaler         = false;
        $pay_by_pocket         = false;
        $pay_by_reward_points  = false;

        $not_included_ids      = array();
        $payment_options_array = array();

        $settings                     = $this->user_bootstrap->get_settings();
        $wholesaler_customer_group_id = $settings->wholesaler_customer_group_id;

        $user_data = $this->user_bootstrap->get_user_data();

        if($user_data != 'guest')
        {
            $user_customer_group = $user_data->customer_group_id;

            if($user_customer_group == $wholesaler_customer_group_id)
            {
                $is_wholesaler  = true;
            }
        }

        if($charge_card_in_cart == 0 )
        {
           // check if logged in to use pocket pay
           if($this->user_bootstrap->is_logged_in())
           {
               $enc_user_balance = $this->user_bootstrap->get_user_data()->user_balance;

               $user_balance     = $this->encryption->decrypt($enc_user_balance, $secret_key, $secret_iv);
               $pocket_tax       = $this->orders->calculate_payment_tax(1, $final_total);

               if(($final_total + $pocket_tax) <= $user_balance)
               {
                    $pay_by_pocket = true;
                    $pay_by_bank   = false;
                    $use_pocket    = 1;
               }
           }
        }

        // check reward points
        if($this->user_bootstrap->is_logged_in())
        {
            $enc_user_points   = $this->user_bootstrap->get_user_data()->user_points;
            $user_points       = $this->encryption->decrypt($enc_user_points, $secret_key, $secret_iv);
            $country_id        = $country_id;
            $point_value       = $this->countries_model->get_reward_points($country_id);
            $user_points_value = $user_points * $point_value;
            $points_tax        = $this->orders->calculate_payment_tax(2, $final_total);

            if(($final_total + $points_tax) <= $user_points_value)
            {
                $pay_by_reward_points = true;
            }
        }

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

        // if visitor remove voucher payment method
        if(! $this->user_bootstrap->is_logged_in())
        {
            $not_included_ids[] = 7;
        }


        $payment_options = $this->payment_methods_model->get_available_payment_options($this->data['lang_id'], $user_customer_group, $not_included_ids, $use_pocket);

        //check payment options after applying option tax
        foreach($payment_options as $option)
        {
            $option_tax                  = $this->orders->calculate_payment_tax($option->id, $final_total);
            $final_total_with_option_tax = $final_total + $option_tax;

            $country_min_order           = $this->currency->convert_from_default_currency($option->min_order_value, $country_id);

            if($final_total_with_option_tax >= $country_min_order)//$option->min_order_value)
            {
                $payment_options_ids_array[] = $option->id;
            }
        }

        return $payment_options_ids_array;
    }

    public function view_order_details($order_id)
    {
        $order_id = intval($order_id);

        if($this->data['is_logged_in'])
        {
            $display_lang_id = $this->user_bootstrap->get_active_language_row()->id;
            $order_details   = $this->orders_model->get_order_details($order_id, $display_lang_id);
            $user_data       = $this->user_bootstrap->get_user_data();
            $user_id         = $user_data->id;

            if($order_details && $order_details->user_id == $user_id)
            {
                $cards_array       = array();
                $log_array         = array();
                $charge_card       = false;
                $charge_card_count = $this->orders_model->get_recharge_cards_count($order_id);

                if($charge_card_count > 0)
                {
                    $charge_card = true;
                    $cards_array = $this->orders_model->get_recharge_card($order_id, $this->data['lang_id']);
                }

                $order_products = $this->orders_model->get_order_products($order_id, $display_lang_id);
                $order_log      = $this->orders_model->get_orders_log($order_id, $display_lang_id);

                foreach($order_log as $log)
                {
                    if($log->status_id == 1)
                    {
                        $class = 'success';
                    }
                    elseif($log->status_id == 2)
                    {
                        $class = 'warning';
                    }
                    elseif($log->status_id == 3)
                    {
                        $class = 'danger';
                    }
                    elseif($log->status_id == 8)
                    {
                        $class = 'info';
                    }

                    $log->{'class'} = $class;

                    $log_array[] = $log;
                }

                $user_customer_group_id = $user_data->customer_group_id;

                $go_to_payment = 'false';
                $pay_by_paypal = 'false';
                $pay_by_cashu  = 'false';
                $cashu_data    = array();

                $payment_method = $this->payment_methods_model->get_payment_method_name($order_details->payment_method_id, $display_lang_id);

                if($order_details->payment_method_id == 5)
                {
                    $pay_by_paypal = 'true';
                }
                elseif($order_details->payment_method_id == 6)
                {
                    $pay_by_cashu   = 'true';
                }
                else
                {
                    $payment_check_count = $this->orders_model->get_order_payment_count($order_id);

                    if($payment_check_count == 0 && $order_details->order_status_id == 2)
                    {
                        $go_to_payment = 'true';
                    }
                }

                if($order_details->payment_method_id == 14)  // pay later
                {
                    $old_bills = $this->pay_later_model->get_order_bills($order_id);
                    $this->data['order_bills'] = $old_bills;

                    if($order_details->rest_amount !=0  && $order_details->paid_amount != $order_details->final_total)
                    {
                        $this->data['pay_later_order'] = 1;

                        $payment_methods = $this->orders->get_order_payment_methods($order_details->final_total, $order_details->country_id, $user_id, $display_lang_id, $user_data);
                        $this->data['payment_options'] = $payment_methods;
                        $bank_accounts = $this->user_bank_accounts_model->get_bank_accounts_result($display_lang_id, $user_id);
                        $this->data['bank_accounts'] = $bank_accounts;

                    }
                }


                $wholesaler_options = false;

                /*if ($this->data['is_wholesaler'])
                {
                    $wholesaler_options = true;
                }*/

                $products_with_serials  = array();
                $approve_data = array();
                $cashu_data['products'] = '';
                
                //if order current status is completed or is shipped then allow reteurn products 
                if(in_array($order_details->order_status_id, array(1, 10)))
                {
                    $log_conds  = array(
                      'order_id'  => $order_id,
                      'status_id' => $order_details->order_status_id
                    );
                    $approve_data = $this->orders_model->get_table_data('orders_log', $log_conds, 'row');
                }
                
                $allowed_time = time() - ($this->config->item('return_days') * 24 * 60 * 60);

                foreach($order_products as $product)
                {
                    if($product->product_id != 0)
                    {
                        $serials_array  = array();
                        
                        if($product->vat_type == 2)//exclusive vat
                        {
                            $product->{'price'} = $product->price + $product->vat_value;
                        }
                                                
                        if($product->quantity_per_serial == 1)
                        {
                            $orders_serials = $this->orders_model->get_product_serials($product->product_id, $product->order_id);

                            foreach($orders_serials as $serial)
                            {
                                $secret_key  = $this->config->item('new_encryption_key');
                                $secret_iv   = md5('serial_iv');

                                $dec_serial  = $this->encryption->decrypt($serial->serial, $secret_key, $secret_iv);

                                $serial->{'dec_serial'}  = $dec_serial;
                                $serials_array[] = $serial;
                            }

                            $product->{'serials'} = $serials_array;
                        }

                        $user_product_optional_fields = $this->products_model->get_user_order_product_optional_fields_data($product->order_product_id, $display_lang_id);

                        if(count($user_product_optional_fields) != 0)
                        {
                            foreach($user_product_optional_fields as $field)
                            {
                                if($field->has_options == 1)
                                {
                                    $option_options = $this->optional_fields_model->get_optional_field_options($field->optional_field_id, $display_lang_id);
                                    foreach($option_options as $option)
                                    {
                                        if($option->id == $field->product_optional_field_value)
                                        {
                                            $field->product_optional_field_value = $option->field_value;
                                            if($option->image != '')
                                            {
                                              $product->{'image'} = $option->image;
                                            }
                                        }
                                    }
                                }

                                $product->user_optional_fields = $user_product_optional_fields;
                            }
                        }

                        //check if available for return
                        $allow_return = true;
                        $return_msg   = '';
                        if($order_details->order_status_id != 1 && $order_details->order_status_id != 10)
                        {
                          $allow_return = false;
                        }
                        else {
                          //check return time
                          if(count($approve_data) >0 && $approve_data->unix_time < $allowed_time)
                          {
                            $allow_return = false;
                            $return_msg  = lang('return_days_ended');
                          }
                          else {
                            if($product->return_status != 0 )
                            {
                              $log_data = array(
                                'order_id' => $order_id,
                                'status_id' => $product->return_status
                              );
                              $return_status_data = $this->orders_model->get_table_data('orders_return_log', $log_conds, 'row');

                              if($product->return_status == 2)
                              {
                                $allow_return = false;
                                $return_msg   = lang('returned_product').' ( '.lang('quantity').' '.$product->returned_qty.' )';
                                $return_msg  .= '<br>'.lang('success').' - '.$return_status_data->notes;

                              }
                              else if($product->return_status == 3)
                              {
                                $allow_return = false;
                                $return_msg   = lang('returned_product').' ( '.lang('quantity').' '.$product->returned_qty.' )';
                                $return_msg  .= '<br>'.lang('reject').' - '.$return_status_data->notes;

                              }
                              else {
                                $allow_return = false;
                                $return_msg   = lang('returned_product').' ( '.lang('quantity').' '.$product->returned_qty.' )';

                              }

                            }
                          }
                        }

                        $product->{'allow_return'} = $allow_return;
                        $product->{'return_msg'}   = $return_msg;

                        $products_with_serials[] = $product;

                        $cashu_data['products'] .= $product->title.' - ';
                    }
                }

                if($order_details->order_status_id == 1 && $order_details->send_later == 0)
                {
                    $show_serials = true;
                }
                else
                {
                    $show_serials = false;
                }

                /**
                 * shipping_types
                 *   1 => Delivery
                 *   2 => Recieve from shop

                */

                if($order_details->send_as_gift == 1)
                {
                    //$wrapping_data = $this->admin_wrapping_model->get_row_data($order_details->wrapping_id, $this->data['lang_id']);
                    $ribbon_data    = $this->admin_wrapping_model->get_wrapping_data($order_details->ribbon_id, $this->data['lang_id']);
                    $wrapping_data  = $this->admin_wrapping_model->get_wrapping_data($order_details->wrapping_id, $this->data['lang_id']);
                    $box_data       = $this->admin_wrapping_model->get_wrapping_data($order_details->box_id, $this->data['lang_id']);

                    $this->data['ribbon_data']   = $ribbon_data;
                    $this->data['wrapping_data'] = $wrapping_data;
                    $this->data['box_data']      = $box_data;
                }

                if($order_details->order_status_id == 1 && $order_details->shipping_type == 3 && $order_details->delivered != 0)
                {
                    $tracking_array = array();
                    $tracking_data  = $this->orders_model->get_order_tracking_log_data($order_details->id, $this->data['lang_id']);

                    foreach($tracking_data as $row)
                    {
                        $text         = '';
                        $decoded_text = json_decode($row->feed_back_text);

                        foreach($decoded_text as $key=>$val)
                        {
                            $text .= $key.' : '.$val.'<br />';
                        }

                        $row->{'decoded_response'} = $text;

                        $tracking_array[] = $row;
                    }

                    $this->data['tracking_data'] = $tracking_array;
                }

                $this->data['order_details']         = $order_details;
                $this->data['payment_method']        = $payment_method;
                $this->data['products_with_serials'] = $products_with_serials;
                $this->data['wholesaler_options']    = $wholesaler_options;
                $this->data['order_log']             = $log_array;
                $this->data['show_serial']           = $show_serials;
                $this->data['charge_card']           = $charge_card;
                $this->data['cards_data']            = $cards_array;
                $this->data['go_to_payment']         = $go_to_payment;
                $this->data['pay_by_paypal']         = $pay_by_paypal;
                $this->data['pay_by_cashu']          = $pay_by_cashu;
                $this->data['cashu_data']            = $cashu_data;
                $this->data['user_id']               = $user_id;
                $this->data['orders_log']            = true;

                $this->data['content'] = $this->load->view('order_details', $this->data, true);
                $this->load->view('site/main_frame',$this->data);
            }
            else
            {
                redirect('Orders_Log', 'refresh');
            }
        }
        else
        {
            $this->user_bootstrap->set_back_redirection_url(current_url());
            redirect('users/users/user_login', 'refresh');
        }
    }

    public function user_orders($page_id =1)
    {
        $page_id = intval($page_id);
        $lang_id = $this->data['lang_id'];

        if($this->ion_auth->logged_in())
        {
            $perPage = 10;
            $offset  = ($page_id -1 ) * $perPage;

            $all_orders_array= array();
            $user_id         = $this->data['user_id'];
            $display_lang_id = $this->data['lang_id'];
            $grouped_orders  = $this->orders_model->get_user_order_data($user_id, $display_lang_id, $perPage, $offset, '', '', '', '', '', 1);

            foreach($grouped_orders as $row)
            {
              $detailed_orders  = $this->orders_model->get_user_order_data($user_id, $display_lang_id, 0, 0, '', '', '', '', '', 0, $row->orders_number);

              $row->{'detailed_orders'} = $detailed_orders;

              $all_orders_array[] = $row;
            }

            $config['base_url']    = base_url()."orders/order/user_orders/";
            $config['per_page']    = $perPage;
            $config['first_link']  = FALSE;
            $config['last_link']   = FALSE;
            $config['uri_segment'] = 4;
            $config['use_page_numbers'] = TRUE;
            $config['first_tag_open'] = '<li>';
            $config['first_tag_close'] = '</li>';
            $config['last_tag_open'] = '<li>';
            $config['last_tag_close'] = '</li>';
            $config['next_tag_open'] = '<li>';
            $config['next_tag_close'] = '</li>';
            $config['prev_tag_open'] = '<li>';
            $config['prev_tag_close'] = '</li>';
            $config['num_tag_open'] = '<li>';
            $config['num_tag_close'] = '</li>';
            $config['cur_tag_open'] = '<li class="active"><a>';
            $config['cur_tag_close'] = '</a></li>';

            $config['total_rows']  = $this->orders_model->get_all_orders_count($lang_id, $user_id, '', '', '', '', '', 1);

            $this->pagination->initialize($config);

            $this->data['orders_data']  = $all_orders_array;
            $this->data['pagination']   = $this->pagination->create_links();
            $this->data['orders_log']   = true;

            $this->data['content'] = $this->load->view('user_grouped_orders', $this->data, true);
            $this->load->view('site/main_frame',$this->data);
        }
        else
        {
            $this->session->set_userdata('redir', current_url());

			redirect('users/users/user_login', 'refresh');
        }
    }

    public function get_grouped_orders($orders_number, $page_id=1)
    {
        $orders_number  = intval($orders_number);
        $page_id        = intval($page_id);
        $lang_id        = $this->data['lang_id'];

        if($this->ion_auth->logged_in())
        {
            $perPage = 10;
            $offset  = ($page_id -1 ) * $perPage;

            if($offset < 0)
            {
                $offset = $perPage;
            }

            $user_id                   = $this->user_bootstrap->get_user_id();
            $display_lang_id           = $this->user_bootstrap->get_active_language_row()->id;
            $users_order_data          = $this->orders_model->get_user_order_data($user_id, $display_lang_id, $perPage, $offset, '', '', '', '', '', 0, $orders_number);

            $orders_array              = array();
            $order_products_array      = array();

            foreach($users_order_data as $order)
            {

                $order_products = $this->orders_model->get_order_products($order->id, $display_lang_id);

                foreach($order_products as $product_row)
                {
                    if($product_row->type == 'product')
                    {
                        $product_row->{'title'} = $product_row->title.'---'.$product_row->qty;
                    }
                    else if($product_row->type == 'recharge_card')
                    {
                        $product_row->{'title'} = $product_row->price.' '.lang('recharge_card');
                    }
                    else if($product_row->type == 'pay_later_bill')
                    {
                        $product_row->{'title'} = $product_row->price.' '.lang('order_bill').' #'.$order->main_order_id;
                    }

                    $order_products_array[$order->id][] = $product_row;
                }

                $label = '';

                if($order->order_status_id == 1)
                {
                    $label = 'success';
                }
                else if($order->order_status_id == 2)
                {
                    $label = 'warning';
                }
                else if($order->order_status_id == 3)
                {
                    $label = 'danger';
                }
                else if($order->order_status_id == 8)
                {
                    $label = 'info';
                }

                $order->{'label'} = $label;

                $orders_array[]   = $order;
            }

            $config['base_url']    = base_url()."orders/order/get_grouped_orders/".$orders_number;
            $config['per_page']    = $perPage;
            $config['first_link']  = FALSE;
            $config['last_link']   = FALSE;
            $config['uri_segment'] = 5;
            $config['use_page_numbers'] = TRUE;

            $config['total_rows']  = $this->orders_model->get_all_orders_count($lang_id, $user_id, '', '', '', '', '', 0, $orders_number);

            $this->pagination->initialize($config);

            $this->data['orders_data']      = $orders_array;
            $this->data['pagination']       = $this->pagination->create_links();
            $this->data['order_products']   = $order_products_array;
            $this->data['orders_log']       = true;

            $this->data['content'] = $this->load->view('users_order_log', $this->data, true);
            $this->load->view('site/main_frame',$this->data);
        }
        else
        {
            $this->session->set_userdata('redir', current_url());
            redirect('users/users/user_login', 'refresh');
        }
    }

    public function get_grouped_orders_reciept($orders_number)
    {
        if($this->data['user_id'] != 0)
        {
            $orders_number      = intval($orders_number);
            $display_lang_id    = $this->data['lang_id'];
            $user_id            = $this->data['user_id'];
            
            if($this->ion_auth->in_group(1)) // if in group admin view this page
            {
              $is_admin = 1;
            }

            $user_orders_data = $this->orders_model->get_user_grouped_order_data($user_id, $display_lang_id, $orders_number, $is_admin);            

            if(!empty($user_orders_data)) // (count($user_orders_data) != 0)
            {
                $orders_products = $this->orders_model->get_grouped_orders_products($orders_number, $display_lang_id);


                if($user_orders_data->send_as_gift == 1)
                {
                    $wrapping_data = $this->admin_wrapping_model->get_wrapping_data($user_orders_data->wrapping_id, $this->data['lang_id']);
                    $this->data['wrapping_data'] = $wrapping_data;
                }



                $this->data['order_details']        = $user_orders_data;
                $this->data['orders_products']      = $orders_products;
            }
            else
            {
                $this->data['error_msg'] = lang('no_data_about_this_order');
            }

            $this->load->view('grouped_orders_receipt', $this->data);
            //$this->data['content'] = $this->load->view('grouped_orders_receipt', $this->data, true);
            //$this->load->view('site/main_frame', $this->data);
        }
        else
        {
            redirect('User_login', 'refresh');
        }
    }

    public function filter_order_log($page_id =1)
    {
        $page_id = intval($page_id);
        $lang_id = $this->data['lang_id'];
        $perPage = 100;
        $offset  = ($page_id -1 ) * $perPage;

        if($offset < 0)
        {
            $offset = $perPage;
        }

        $order_number  = intval($this->input->post('order_number', TRUE));
        $order_date    = strip_tags($this->input->post('order_date', TRUE));
        $product_title = strip_tags($this->input->post('product_title', TRUE));
        $final_total   = strip_tags($this->input->post('final_total', TRUE));
        $status        = strip_tags($this->input->post('status', TRUE));
        $start_date    = strtotime($order_date);
        //$end_date    = strToTime($order_date.' 23:59:59' );
        $end_date      = $start_date + 86399;

        $user_id          = $this->user_bootstrap->get_user_id();
        $display_lang_id  = $this->user_bootstrap->get_active_language_row()->id;

        $users_order_data = $this->orders_model->get_user_order_data($user_id, $display_lang_id, $perPage, $offset, $order_number, $start_date, $end_date, $final_total, $status);

        $orders_array       = array();
        $order_charge_cards = array();

        if(count($users_order_data) != 0)
        {
            foreach($users_order_data as $order)
            {
                $order_products  = $this->orders_model->get_order_products($order->id, $display_lang_id, $product_title);

                //if(stristr(lang('recharge_card'), $product_title) != false )
                if($product_title == '')
                {
                    $order_charge_cards     = $this->orders_model->get_recharge_card($order->id, $this->data['lang_id']);
                }

                if(count($order_products) != 0 || count($order_charge_cards) != 0)
                {
                    $order->{'products'}    = $order_products;
                    $order->{'charge_card'} = $order_charge_cards;

                    if($order->order_status_id == 1)
                    {
                        $label = 'success';
                    }
                    else if($order->order_status_id == 2)
                    {
                        $label = 'warning';
                    }
                    else if($order->order_status_id == 3)
                    {
                        $label = 'danger';
                    }

                    $order->{'label'} = $label;

                    $orders_array[]   = $order;
                }
            }
        }

        $config['base_url']    = base_url()."orders/order/filter_order_log/";
        $config['per_page']    = $perPage;
        $config['first_link']  = FALSE;
        $config['last_link']   = FALSE;
        $config['uri_segment'] = 4;
        $config['use_page_numbers'] = TRUE;

        $config['total_rows']  = $this->orders_model->get_all_orders_count($lang_id, $user_id, $order_number, $start_date, $end_date, $final_total, $status);

        $this->pagination->initialize($config);

        $this->data['orders_data'] = $orders_array;
        $this->data['pagination']  = $this->pagination->create_links();

        $this->load->view('orders/orders_log_ajax_view', $this->data);

        //echo json_encode(array($output, $pagination));
    }

    public function order_confirmation()
    {
      if(isset($_POST['order_id']))
      {
        $order_id = intval($this->input->post('order_id', true));
        $this->data['order_id']  = $order_id;
        $this->data['hide_menu'] = true;
        $this->data['is_cart']   = true;

        $order_data = $this->orders_model->get_table_data('orders', array('id'=>$order_id), 'row');
        //if order is payed by bank , add form upload
        if($order_data->payment_method_id == 3)
        {
          $this->data['orders_number'] = $order_data->orders_number;
          $this->data['upload_bank_statement'] = true;
        }

        $this->data['content'] = $this->load->view('order_congratulations', $this->data, true);
        $this->load->view('site/main_frame',$this->data);
      }
      else {
        redirect(base_url().'Orders_Log');
      }
    }

    public function upload_order_bank_statement()
    {
      if($this->data['is_logged_in'])
      {
        $grouped_order_id = intval($this->input->post('grouped_order_id', true));

        /*********** Upload****************************/
        $this->load->library('upload');
        $gallery_path = realpath(APPPATH. '../assets/uploads/');

        $config = array();
        $config['upload_path']   = $gallery_path;
        $config['allowed_types'] = 'png|jpg|jpeg|tif';
        $config['max_size']      = '100';
        $config['encrypt_name']  = true;

        $file_name = '';
        $error      = array();

        $this->upload->initialize($config);
       if(!$this->upload->do_upload())
       {
            $error = $this->upload->display_errors();

            $_SESSION['error_message'] = $error;
            $this->session->mark_as_flash('error_message');
            //$this->data['message'] = $error;
       }
       else
       {
           $file_data = $this->upload->data();
           $file_name = $file_data['file_name'];

           $this->amazon_s3_uploads->upload_to_o3($file_name, '');

           //update orders bank statement
           $conds = array('orders_number' => $grouped_order_id);
           $updated_data = array('bank_statement' => $file_name);

           $this->orders_model->update_table_data('orders', $conds, $updated_data);

           //$this->data['message'] = lang('uploaded_successfully');
           $_SESSION['message'] = lang('uploaded_successfully');
           $this->session->mark_as_flash('message');

       }

       redirect('orders/order/user_orders/', 'refresh');
      }
      else {
        redirect('User_login', 'refresh');
      }
    }

/************************************************************************/
}
