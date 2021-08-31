<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Order_details extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('follow_orders_model');
        $this->load->model('general_model');
        $this->load->model('orders/orders_model');
        $this->load->model('orders/pay_later_model');
        $this->load->model('products/products_model');
        $this->load->model('optional_fields/optional_fields_model');
        $this->load->model('payment_options/payment_methods_model');

        $this->load->library('api_lib');
        $this->load->library('encryption');

        $this->config->load('encryption_keys');


    }

    public function index()
    {

        $email         = strip_tags($this->input->post('email', TRUE));
        $password      = strip_tags($this->input->post('password', TRUE));

        $lang_id       = intval($this->input->post('langId', TRUE));
        $orderNumber   = intval($this->input->post('orderNumber', TRUE));
        $country_id    = intval($this->input->post('countryId', TRUE));

        $output        = array();
        $order_history = array();
        $serials_array = '';

        $fail_message  = $this->general_model->get_lang_var_translation('execution_fail',$lang_id);
        $settings      = $this->general_model->get_settings();
        $images_path   = $this->api_lib->get_images_path();

        if($this->ion_auth->login($email, $password))
        {
            $user_data = $this->ion_auth->user()->row();
            $this->api_lib->check_user_store_country_id($email, $password, $user_data->id, $country_id);

            $order_details = $this->orders_model->get_order_details( $orderNumber, $lang_id);

            if(count($order_details) != 0) //&& !empty($order_details))
            {
                $order_log          = $this->orders_model->get_orders_log( $orderNumber, $lang_id);
                $order_payment_log  = $this->follow_orders_model->get_order_payment_log( $orderNumber);
                $order_products     = $this->orders_model->get_order_products($orderNumber, $lang_id);


                if(!empty($order_log))
                {
                    foreach($order_log as $log)
                    {
                        $order_history [] = array(
                                                    'orderStatusDate'   => date('Y-m-d',$log->unix_time),
                                                    'orderStatus'       => $log->name,
                                                    'orderStatusNote'   => $log->notes,
                                                 );
                    }
                }

                if(isset($order_payment_log) && !empty($order_payment_log))
                {
                    $orderGateResponse = 1;

                }else
                {

                    $orderGateResponse = 0;
                }

                if(!empty($order_products))
                {
                    $log_conds  = array(
                      'order_id'  => $orderNumber,
                      'status_id' => 1
                    );
                    $approve_data = $this->orders_model->get_table_data('orders_log', $log_conds, 'row');
                    $allowed_time = time() - ($settings->return_days * 24 * 60 * 60);

                    foreach($order_products as $product)
                    {
                        $serials_array = array();
                        if($product->product_id == 0)
                        {
                          $pic = base_url().'assets/template/site/images/wallet.jpg';
                        }
                        else if(isset($product->image)&& $product->image != '')
                        {
                            $pic =  $images_path.$product->image;
                        }
                        else
                        {
                           $pic = '';
                        }

                        if($order_details->order_status_id == 1)
                        {
                            $orders_serials = $this->orders_model->get_product_serials($product->product_id, $product->order_id);

                            if(!empty($orders_serials))
                            {
                                foreach($orders_serials as $serial)
                                {

                                    $secret_key  = $this->config->item('new_encryption_key');
                                    $secret_iv   =  md5('serial_iv');//md5($row->unix_time);
                                    $enc_serials = $this->encryption->decrypt($serial->serial, $secret_key, $secret_iv);

                                    $serials_array [] = array(
                                                                'serialId'           => $serial->id,
                                                                'serialCode'         => $enc_serials , //$serial->serial,
                                                                'serialSMSed'        => $serial->smsed,
                                                                'serialPrinted'      => $serial->printed,
                                                            );
                                }

                            }
                        }

                        $user_product_optional_fields = $this->products_model->get_user_order_product_optional_fields_data($product->order_product_id, $lang_id);
                        $product_optional_fields      = array();

                        if(count($user_product_optional_fields) != 0)
                        {
                            foreach($user_product_optional_fields as $field)
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
                                              $pic = $images_path.$option->image;
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

                        if($product->product_id == 0)
                        {
                            $card_lang = $this->general_model->get_lang_var_translation('recharge_card',$lang_id);
                            $product_name = $card_lang.' '. $product->price;
                        }
                        else
                        {
                            $product_name = $product->title;
                        }

                        //check if available for return
                        $allow_return = true;
                        $return_msg   = '';
                        if($order_details->order_status_id != 1)
                        {
                          $allow_return = false;
                        }
                        else {
                          //check return time
                          if($approve_data->unix_time < $allowed_time)
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

                              $return_lang   = $this->general_model->get_lang_var_translation('returned_product', $lang_id);
                              $quantity_lang = $this->general_model->get_lang_var_translation('quantity', $lang_id);

                              if($product->return_status == 2)
                              {
                                $allow_return = false;
                                $success_lang = $this->general_model->get_lang_var_translation('success', $lang_id);

                                $return_msg   = $return_lang.' ( '.$quantity_lang.' '.$product->returned_qty.' )';
                                $return_msg  .= '<br>'.$success_lang.' - '.$return_status_data->notes;

                              }
                              else if($product->return_status == 3)
                              {
                                $allow_return = false;
                                $reject_lang  = $this->general_model->get_lang_var_translation('reject', $lang_id);

                                $return_msg   = $return_lang.' ( '.$quantity_lang.' '.$product->returned_qty.' )';
                                $return_msg  .= '<br>'.$reject_lang.' - '.$return_status_data->notes;

                              }
                              else {
                                $allow_return = false;
                                $return_msg   = $return_lang.' ( '.$quantity_lang.' '.$product->returned_qty.' )';

                              }

                            }
                          }
                        }

                        if($product->vat_type == 1)
                        {
                          //inclusive vat
                          $product_vat_message = $this->general_model->get_lang_var_translation('inclusive_vat', $lang_id);
                          $product_price = $product->price;
                        }
                        else {
                          //exclusive vat
                          $product_vat_message = $this->general_model->get_lang_var_translation('exclusive_vat', $lang_id);
                          $product_price = $product->price + $product->vat_value;
                        }


                        $order_products_array[] = array(
                                                        'productId'         => $product->product_id     ,
                                                        'orderProductId'    => $product->order_product_id,
                                                        'productName'       => $product_name            ,
                                                        'productImage'      => $pic                     ,
                                                        'productPrice'      => "$product_price",//$product->price"        ,
                                                        'itemTotalPrice'    => $product->qty * $product_price,
                                                        'serials'           => $serials_array           ,
                                                        'optionalFields'    => $product_optional_fields ,
                                                        'vatValue'          => $product->vat_value      ,
                                                        'vatPercent'        => $product->vat_percent    ,
                                                        'quantity'          => $product->qty            ,
                                                        'allowReturn'       => $allow_return            ,
                                                        'returnMsg'         => $return_msg              ,
                                                        'vatMessage'        => $product_vat_message     ,

                                                        );


                    }
                }
                else
                {
                    $order_products_array = array();

                }


                if($order_details->shipping_type == 1)
                {
                    $shipping_type = $this->general_model->get_lang_var_translation('deliver_home', $lang_id);
                }
                elseif($order_details->shipping_type == 2)
                {
                    $shipping_type = $this->general_model->get_lang_var_translation('recieve_from_shop', $lang_id);
                }
                else
                {
                    $shipping_type = '';
                }

                $payment_method = $this->payment_methods_model->get_row_data($order_details->payment_method_id, $lang_id);
                $currency       = $this->currency->get_country_currency_name($order_details->country_id, $lang_id);
                $payment_url    = '';

                if($order_details->order_status_id == 9 && $order_details->main_order_id != 0)
                {
                    $payment_method_id = $order_details->payment_method_id;
                    $products_names ='maintenance cost';

                    if($payment_method_id == 4 || $payment_method_id == 5 || $payment_method_id == 6 || $payment_method_id == 8  || $payment_method_id ==13 )
                    {
                        $payment_url = $this->_generate_payment_form($payment_method_id, $orderNumber, $order_details->final_total, $order_details->currency_symbol, $products_names, $lang_id, $user_id);
                    }
                }

                $pay_later_bills = 0;
                if($order_details->payment_method_id == 14 && $order_details->paid_amount < $order_details->final_total && $order_details->rest_amount != 0  )
                {
                    $pay_later_bills = 1;
                }

                $prev_bills = array();

                if($order_details->payment_method_id == 14)
                {
                    $old_bills = $this->pay_later_model->get_order_bills($order_details->id);

                    foreach ($old_bills as $key => $value) {
                      $prev_bills[] = array(
                                              'amount'          => $value->amount,
                                              'orderTotal'      => $value->order_total,
                                              'orderRestAmount' => $value->order_rest,
                                              'orderPaidAmount' => $value->order_paid,
                                              'addedDate'       => date('Y/m/d H:i', $value->unix_time)
                                            );
                    }
                }

                if($order_details->bank_statement != '')
                {
                  $bank_statement_link = $images_path.$order_details->bank_statement;
                }
                else {
                  $bank_statement_link = '';
                }


                $total = round($order_details->total, 2);
                $final_total = round($order_details->final_total, 2);
                $order_shipping_cost = round($order_details->shipping_cost, 2);
                $order_wrapping_cost = round($order_details->wrapping_cost, 2);
                $order_Tax = round($order_details->tax, 2);
                $order_coupon_discount = round($order_details->coupon_discount, 2);
                $order_discount = round($order_details->discount, 2);
                $vat_value = round($order_details->vat_value, 2);

                $output  = array(
                                'orderNumber'            => $order_details->id                          ,
                                'orderTotal'             => "$total"                                    ,
                                'orderFinalTotal'        => "$final_total"                              ,
                                'orderShippingCost'      => "$order_shipping_cost"                      ,
                                'orderWrappingCost'      => "$order_wrapping_cost"                      ,
                                'orderTax'               => "$order_Tax"                                ,
                                'orderCouponDiscount'    => "$order_coupon_discount"                    ,
                                'orderDiscount'          => "$order_discount"                           ,
                                'vatValue'               => "$vat_value"                                ,
                                'vatPercent'             => $order_details->vat_percent                 ,
                                'orderCreateDate'        => date('Y-m-d h:i',$order_details->unix_time) ,
                                'orderPaymentMethod'     => $payment_method->name                       ,
                                'orderPaymentMethodId'   => $order_details->payment_method_id           ,
                                'bankStatement'          => $bank_statement_link                        ,
                                'orderCurrentStatus'     => $order_details->status                      ,
                                'orderCurrentStatusId'   => $order_details->order_status_id             ,
                                'orderNote'              => $order_details->notes                       ,
                                'orderGateResponse'      => $orderGateResponse                          ,
                                'orderProducts'          => $order_products_array                       ,
                                'orderHistory'           => $order_history                              ,
                                'shippingTypeID'         => $order_details->shipping_type               ,
                                'shippingType'           => $shipping_type                              ,
                                'storeName'              => $order_details->store_name                  ,
                                'currency'               => $currency                                   ,
                                'paymentUrl'             => $payment_url                                ,
                                'pay_later_bills'        => "$pay_later_bills"                          ,
                                'prevBills'              => $prev_bills ,
                                'shipmentTrackingNo'     => $order_details->tracking_number
                                );

              if($order_details->shipping_type == 1)
              {
                $output['shippingCity']     = $order_details->city_name;
                $output['shippingPhone']    = $order_details->shipping_phone;
                $output['shippingName']     = $order_details->shipping_name;
                $output['shippingAddress']  = $order_details->shipping_address;

              }
              elseif($order_details->shipping_type == 2)
              {
                $output['branchName']     = $order_details->branch_name;
              }
              elseif($order_details->shipping_type == 3)
              {
                $output['shippingCountry']  = $order_details->shipping_country;
                $output['shippingCity']     = $order_details->shipping_city;
                $output['shippingDistrict'] = $order_details->shipping_district;
                $output['shippingCompany']  = $order_details->shipping_company;
                $output['shippingAddress']  = $order_details->shipping_address;
                $output['shippingName']     = $order_details->shipping_name;
                $output['shippingPhone']    = $order_details->shipping_phone;


              }

            }//if order
            else
            {
                $output  = array(
                                    'message' => $fail_message,
                                    'response' => 0
                                );
            }
        }
        else
        {
            $output  = array(
                                'message' => $fail_message,
                                'response' => 0
                                );
            //$output  = array( 'message' => '0');
        }//if login

        $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));

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
            $url = base_url().'orders/Payment_gateways/process_hyperpay/'.$order_id.'/hyperpay_visa';
        }
        else
        {
            $url = base_url().'orders/order/view_order_details/'.$order_id;
        }

        return $url;
    }

    public function upload_bank_statement()
    {
        $email         = strip_tags($this->input->post('email', TRUE));
        $password      = strip_tags($this->input->post('password', TRUE));

        $lang_id       = intval($this->input->post('langId', TRUE));
        $orderNumber   = intval($this->input->post('orderNumber', TRUE));
        $country_id    = intval($this->input->post('countryId', TRUE));

        $image = $this->input->post('image', true);

        $fail_message  = $this->general_model->get_lang_var_translation('execution_fail',$lang_id);

        if($this->ion_auth->login($email, $password))
        {
            $user_data = $this->ion_auth->user()->row();
            $user_id   = $user_data->id;

            $order_details = $this->orders_model->get_order_details( $orderNumber, $lang_id);

            if(count($order_details) != 0 && $order_details->user_id == $user_id && $order_details->payment_method_id == 3)
            {
              //order payment method is bank
              // receive image as POST Parameter
              $file_name = $this->create_image($image);
              //$file_name = $this->uploaded_images->upload_image($image);

              //update order
              $updated_data = array(
                'bank_statement' => $file_name
              );

              $conds = array('id' => $orderNumber);

              $this->orders_model->update_table_data('orders', $conds, $updated_data);
              $success_msg = $this->general_model->get_lang_var_translation('uploaded_successfully', $lang_id);

              $output  = array(
                                  'message' => $success_msg,
                                  'response' => 1
                                  );
            }
            else {
              $output  = array(
                                  'message' => $fail_message,
                                  'response' => 0
                                  );
            }
        }
        else
        {
            $output  = array(
                                'message' => $fail_message,
                                'response' => 0
                                );
            //$output  = array( 'message' => '0');
        }//if login

        $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));
    }

    public function create_image($image_code)
    {
        /*
        $recievedJson2 = "iVBORw0KGgoAAAANSUhEUgAAABwAAAASCAMAAAB/2U7WAAAABl'
       . 'BMVEUAAAD///+l2Z/dAAAASUlEQVR4XqWQUQoAIAxC2/0vXZDr'
       . 'EX4IJTRkb7lobNUStXsB0jIXIAMSsQnWlsV+wULF4Avk9fLq2r'
       . '8a5HSE35Q3eO2XP1A1wQkZSgETvDtKdQAAAABJRU5ErkJggg==";
       */



        // Get image string posted from Android App
        $base = $image_code;
    	// Get file name posted from Android App
    	$filename = 'bank_st_'.time().'.jpg';
    	// Decode Image
        $binary=base64_decode($base);
        header('Content-Type: bitmap; charset=utf-8');
    	// Images will be saved under 'www/imgupload/uplodedimages' folder
        $file = fopen('assets/uploads/'.$filename, 'wb');
    	// Create File
        fwrite($file, $binary);
        fclose($file);
        //echo 'Image upload complete, Please check your php file directory';

        return $filename;

    }


/************************************************************************/
}
