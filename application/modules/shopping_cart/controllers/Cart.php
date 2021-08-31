<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Cart extends CI_Controller
{
    public $data = array();
    public $cart_data;
    public $check_shopping_cart;

    public function __construct()
    {
        parent::__construct();

        $this->load->library('encryption');
        $this->load->library('shopping_cart');
        $this->load->library('products_lib');

        $this->load->model('shopping_cart_model');
        $this->load->model('users/cities_model');
        $this->load->model('users/countries_model');
        $this->load->model('stores/stores_model');
        $this->load->model('shipping/costs_model');
        $this->load->model('shipping/companies_model');
        $this->load->model('products/products_model');
        $this->load->model('products/products_tags_model');
        $this->load->model('products/site_products_model');
        $this->load->model('coupon_codes/coupon_codes_model');
        $this->load->model('users/user_balance_model');
        $this->load->model('users/customer_groups_model');
        $this->load->model('payment_options/bank_accounts_model');
        $this->load->model('payment_options/payment_methods_model');
        $this->load->model('shopping_cart/user_bank_accounts_model');
        $this->load->model('optional_fields/optional_fields_model');
        $this->load->model('wrapping/admin_wrapping_model');

        require(APPPATH . 'includes/front_end_global.php');

        $this->shopping_cart->set_user_data(

                                        $this->data['user_id'],
                                        $this->data['session_id'],
                                        $this->data['ip_address'],
                                        $this->data['country_id'],
                                        $this->data['lang_id']
        );

        $this->user_bootstrap->set_back_redirection_url(base_url().'Shopping_Cart');

        $cart_data  = $this->shopping_cart->shopping_cart_data();
        $this->cart_data = $cart_data;
        $tax_msg = '';
        if($cart_data->payment_option_id != 0)
        {
            $payment_method_name = $this->payment_methods_model->get_payment_method_name($cart_data->payment_option_id, $this->data['lang_id']);
            $tax_msg = lang('taxes_on').' '.$payment_method_name;
        }

        $this->data['tax_msg'] = $tax_msg;

        $this->check_shopping_cart = $this->shopping_cart->check_if_cart_products_exist();

    }

    public function add_to_cart()
    {
        $message = '';
        $route = '';
        $image = '';
        $cart_final_total = '';
        $related_products_html = '';

        $product_id  = intval($this->input->post('product_id', true));
        $product_qty = intval($this->input->post('product_qty', true));

        if($product_qty == 0 && $product_id !=0)
        {
          $message = 'qty_error';
        }
        else
        {

          $quantity_per_serial_product = false;

          $cart_data  = $this->shopping_cart->shopping_cart_data();
          $current_country_id = $this->data['country_id'];
          $settings = $this->user_bootstrap->get_settings();

          if($cart_data->country_id != $current_country_id)
          {
              // delete cart
              $this->shopping_cart->delete();
          }

          // if it is a product not charge card
          if($product_id != 0)
          {
              $route = '';
              $image = '';
              $related_products_html = '';
              $is_available_products_per_user_customer_group = $this->shopping_cart->check_user_cart_products_per_customer_group();

              if($is_available_products_per_user_customer_group)
              {
                  $product_details    = $this->products_model->get_product_row_details($product_id, $this->data['lang_id'], $this->data['country_id']);
                  $route              = $product_details->route;

                  if(!$product_details)
                  {
                      $message = 'no_product_details';
                      //return false;
                  }
                  else
                  {
                      /**
                       * Mrzok Comment  : $is_product_exist need to be checked , 
                      ** for the case if the product has options and is exist with selected options not the same as new options
                      */
                      $is_product_exist   = $this->shopping_cart->check_if_product_in_shopping_cart($product_id);
                      $product_price_data = $this->products_lib->get_product_price_data($product_details);

                      /**
                       * get_product_price_data() result

                       * [0] = product price before discount
                       * [1] = final product price
                       * [2] = "0" => no limit to use the discount ,
                       *       "1" => limited uses of discount
                       * [3] = allowed number of uses
                       * [4] = strike
                       * [5] = discount amount
                       * [6] = used discount id

                      **/

                      if($is_product_exist)
                      {
                          $non_stock_product = 0;

                          if($product_details->quantity_per_serial == 0)
                          {
                              $non_stock_product = 1;

                              if($this->data['user_id'] != 0)
                              {
                                  $user_customer_group_data = $this->customer_groups_model->get_user_customer_group_data($this->data['user_id']);
                                  $max_per_order            = $user_customer_group_data->product_limit_per_order;
                              }
                              else
                              {
                                  $max_per_order = 100000;
                              }

                              $qty = $max_per_order - $cart_data->items_count;

                          }
                          else
                          {
                              // Check if the products count more than the minimum
                              $stock_count = $this->products_model->count_product_available_quantity($product_id, $this->data['country_id']);
                              $min_stock   = $this->config->item('min_product_stock');
                              $qty         = $stock_count - $min_stock;
                          }

                          if($qty < 1 )
                          {
                              $message = 'no_stock';
                          }
                          else
                          {
                              /**
                               * Mrzok Comment  : $cart_product_data need to be checked , 
                               * for the case if the product has options and is exist with selected options not the same as new options
                               * */
                              $cart_product_data  = $this->shopping_cart->get_product_details($product_id);

                              if(($cart_product_data->qty + $product_qty) <= $product_price_data[3] || $product_price_data[2] == 0)
                              {
                                  $product_optiona_fields_count = $this->products_model->count_product_optional_fields($product_id);
                                  
                                  if($product_optiona_fields_count > 0)
                                  {
                                      $message = 'optional_fields_required';
                                  }
                                  else
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
                                                      'qty'             => $cart_product_data->qty + $product_qty,
                                                      'discount'        => $product_price_data[5]         ,
                                                      'weight'          => $product_details->weight       ,
                                                      'coupon_discount' => 0,
                                                      'price'           => $product_price_data[0]         ,
                                                      'final_price'     => $final_price,//$product_price_data[1]         ,
                                                      'checked'         => 1,
                                                      'vat_type'        => $product_price_data['vat_type'],
                                                      'vat_percent'     => $product_price_data['vat_percent'],
                                                      'vat_value'       => $product_price_data['vat_value']
                                                   );

                                      /**
                                         * Mrzok Comment  : shopping_cart->update Query need to be checked , 
                                         ** for the case if the product has options and is exist with selected options not the same as new options
                                        */

                                      if($this->shopping_cart->update($product_details->id, $data, $non_stock_product))
                                      {
                                          /*if($product_price_data[6] != 0)
                                          {
                                              $updated_discount_log_data = array(
                                                                                    'qty'         => $cart_product_data->qty + 1,
                                                                                    'unix_time'   => time()
                                                                                 );

                                              $this->products_model->update_product_discount_log($cart_data->id, $product_id, $updated_discount_log_data);
                                          }*/

                                          if($cart_data->coupon_discount != 0)
                                          {
                                              $this->shopping_cart->reset_cart_coupon();
                                          }

                                          $message = lang('product_qty_increaded');
                                          $image = $product_details->image;

                                          //related products
                                          $product_tags  =  $this->products_tags_model->get_product_tags($product_details->id, $this->data['lang_id']);

                                          $tags = array();

                                          foreach($product_tags as $tag)
                                          {
                                              $tags[] = $tag->id;
                                          }

                                          //related products
                                          $related_products_id = array();
                                          if(count($tags) != 0)
                                          {
                                            $related_products_id = $this->site_products_model->get_related_products($product_details->id , $tags);
                                          }
                                          $related_products_html = $this->_get_products_prices($related_products_id);

                                      }
                                      else
                                      {
                                          $message = 'no_stock';
                                      }
                                  }
                              }
                              else
                              {
                                  $message = 'max_per_discount';
                              }
                          }

                  }
                  else
                  {
                      $qty_error = false;
                      $available_qty = '0';
                      if($product_details->quantity_per_serial == 1)
                      {
                          // Check if the products count more than the minimum
                          $stock_count = $this->products_model->count_product_available_quantity($product_id, $this->data['country_id']);
                          $min_stock   = $this->config->item('min_product_stock');
                          $available_qty = $stock_count - $min_stock;

                          if($available_qty < 1 )
                          {
                              $message = 'no_stock';
                              $qty_error = true;
                          }
                      }
                      else{
                        $is_stock_product = true;
                      }


                      if(!$qty_error)
                      {
                          if(($product_qty <= $product_price_data['allowed_limit'] || ($product_price_data['limit'] == 0 && $product_price_data['strike'] == 1) ) || $product_qty <= $available_qty || $is_stock_product)
                          {

                              $product_optiona_fields_count = $this->products_model->count_product_optional_fields($product_id);

                              if($product_optiona_fields_count > 0)
                              {
                                  $message = 'optional_fields_required';
                              }
                              else
                              {

                                if($product_price_data['vat_type'] == 2) //exclusive
                                {
                                    $final_price = $product_price_data[1] + $product_price_data['vat_value'];
                                }
                                else
                                {
                                    $final_price = $product_price_data[1];
                                }
                                  $data    = array(
                                                     'product_id'    => $product_details->id      ,
                                                     'type'          => 'product'                 ,
                                                     'cat_id'        => $product_details->cat_id  ,
                                                     'store_id'      => $product_details->store_id,
                                                     'qty'           => $product_qty              ,
                                                     'name'          => $product_details->title   ,
                                                     'weight'        => $product_details->weight  ,
                                                     'price'         => $product_price_data[0]    ,
                                                     'final_price'   => $final_price              ,
                                                     'discount'      => $product_price_data[5]    ,
                                                     'image'         => $product_details->image   ,
                                                     'reward_points' => $product_details->reward_points,
                                                     'checked'       => 1,
                                                     'vat_type'        => $product_price_data['vat_type'],
                                                     'vat_percent'     => $product_price_data['vat_percent'],
                                                     'vat_value'       => $product_price_data['vat_value']
                                                  );

                                      $this->shopping_cart->insert($data);

                                      //check shopping cart coupon
                                      if($cart_data->coupon_discount != 0)
                                      {
                                          $this->shopping_cart->reset_cart_coupon();
                                      }


                                      $message = lang('product_added_to_cart_successfully').' '.$product_details->title;
                                      $image = $product_details->image;

                                      //related products
                                      $product_tags  =  $this->products_tags_model->get_product_tags($product_details->id, $this->data['lang_id']);
                                      $tags = array();
                                      $tags_text = '';
                                      foreach($product_tags as $tag)
                                      {
                                          $tags[]     = $tag->id;
                                          $tags_text .= $tag->tag.' , ';
                                      }

                                      //related products
                                      $related_products_id = $this->site_products_model->get_related_products($product_details->id , $tags);
                                      $related_products_html = $this->_get_products_prices($related_products_id);

                                  }

                              }
                              else
                              {
                                  $message = 'max_per_discount';
                              }
                          }
                      }
                  }
              }
              else
              {
                  $message = 'max_products_per_order';
              }


              $cart_data = $this->shopping_cart->shopping_cart_data();
              $cart_final_total = $cart_data->final_total_price_with_tax.' '.$cart_data->currency_symbol;

          }
          elseif(isset($_POST['type']) && $_POST['type']=='recharge_card')
          {
              $balance_amount = intval($this->input->post('balance', TRUE));

              if($balance_amount > 1)
              {
                  $recharge_data = array(
                                         'product_id'    => 0               ,
                                         'store_id'      => $settings->default_store_id,
                                         'type'          => 'recharge_card' ,
                                         'cat_id'        => 0               ,
                                         'qty'           => 1               ,
                                         'name'          => $balance_amount.' recharge card' ,
                                         'price'         => $balance_amount ,
                                         'final_price'   => $balance_amount ,
                                         'image'         => ''              ,
                                         'checked'       => 1               ,
                                         'reward_points' => 0
                                       );


                  $this->shopping_cart->insert($recharge_data);

                  //echo lang('recharge_card_added_to_cart_successfully');
                  redirect('Shopping_Cart', 'refresh');
              }
              else
              {
                  $_SESSION['error_message'] = lang('min_recharge_card_value');
                  $this->session->mark_as_flash('error_message');

                  redirect('Balance_Recharge', 'refresh');
              }
          }
          else if (isset($_POST['type']) && $_POST['type']=='package')  // to upgrade user account
          {
              $package_id = intval($this->input->post('package_id', TRUE));

              //check this package (customer group) is exist
              $package_data = $this->customer_groups_model->get_row_data($package_id, $this->data['lang_id']);

              if(count($package_data) != 0)
              {
                  //delete other packages in cart
                  $this->shopping_cart_model->remove_account_packages_in_cart($cart_data->id);

                  $package_data = array(
                                         'product_id'    => 0                     ,
                                         'package_id'    => intval($this->input->post('package_id', true)),
                                         'store_id'      => $settings->default_store_id,
                                         'type'          => 'package'             ,
                                         'cat_id'        => 0                     ,
                                         'qty'           => 1                     ,
                                         'name'          => $package_data->title  ,
                                         'price'         => $package_data->price  ,
                                         'final_price'   => $package_data->price  ,
                                         'image'         => $package_data->image  ,
                                         'checked'       => 1                     ,
                                         'reward_points' => 0
                                       );

                  $this->shopping_cart->insert($package_data);

                  //redirect user to shopping cart page
                  //redirect('Shopping_Cart', 'refresh');

              }
              else
              {
                  $_SESSION['error_message'] = lang('no_data');
                  $this->session->mark_as_flash('error_message');

                  redirect('UpgradeAccount', 'refresh');
              }
          }
          else
          {
              echo "Error";
          }
        }

        $result_array = array(
                              $message,
                              $route,
                              $image,
                              $cart_final_total,
                              $related_products_html
                            );

        echo json_encode( array(
                              $message,
                              $route,$image,
                              $cart_final_total ));

        //echo array($message, $route, $image, $cart_final_total, $related_products_html)
    }

    public function view_cart()
    {
        if(!$this->data['is_logged_in'])
        {
            $this->load->model('users/cities_model');
            $display_lang_id = $this->data['lang_id'];
            $countries = $this->cities_model->get_user_nationality_filter_data($display_lang_id);

            $countries_array = array();
            $countries_array[null] = lang('choose');

            foreach($countries as $country)
            {
                $countries_array[$country->id] = $country->name;
            }

            $this->data['user_countries'] = $countries_array;
        }

        $branches = $this->shopping_cart_model->get_branches_data(1);

        /*$xml_data = '<?xml version="1.0" encoding="utf-8"?><markers>';

        foreach ($branches as $branch)
        {
            $xml_data .= '
                        	<marker branchid="'.$branch->id.'" name="'.$branch->name.'" lat="'.$branch->lat.'" lng="'.$branch->lng.'" category="Restaurant" address="" address2="" city="" state="" postal="" country="" phone="" email="" web="" hours1="" hours2="" hours3="" featured="" features="" />
                        ';
        }

        $xml_data .= '</markers>';

        */
        $this->data['branches'] = $branches;

        $this->data['content'] = $this->load->view('shopping_cart', $this->data, true);
        $this->load->view('site/main_frame',$this->data);
    }

    public function cart_ajax()
    {
        $lang_id = $this->data['lang_id'];
        $this->data['order_error'] = false;

        $insert_order_error = false;
        $shipping_required  = false;

        $check_shopping_cart = $this->shopping_cart->check_if_cart_products_exist();

        if($check_shopping_cart)
        {
            if(!$this->data['is_logged_in'])
            {
                $display_lang_id = $this->data['lang_id'];
                $countries = $this->cities_model->get_user_nationality_filter_data($display_lang_id);

                $countries_array = array();
                $countries_array[null] = lang('choose');

                foreach($countries as $country)
                {
                    $countries_array[$country->id] = $country->name;
                }

                $this->data['user_countries'] = $countries_array;
            }


            // Check Cart Country

            $cart_data  = $this->shopping_cart->shopping_cart_data();
            $current_country_id = $this->data['country_id'];

            if($cart_data->country_id != $current_country_id)
            {
                // empty cart
                $this->shopping_cart->delete();

                $empty_msg = '0';
                echo $empty_msg;
            }
            else
            {
                if(isset($_POST['qty']))
                {
                    $new_qties = $this->input->post('qty', TRUE);

                    $qty_status_array = $this->update_quantity($new_qties);

                    if(in_array(0, $qty_status_array))
                    {
                        $this->data['quantity_status_error'] = lang('no_stock');
                        $insert_order_error = true;
                    }
                    elseif(in_array(2, $qty_status_array))
                    {
                        $this->data['quantity_status_error'] = lang('max_qty_per_user_discount_reached');
                        $insert_order_error = true;
                    }
                    elseif(in_array(3, $qty_status_array))
                    {
                        $this->data['not_in_country'] = lang('product_not_exist_in_country');
                        $insert_order_error = true;
                    }
                    elseif(in_array(4, $qty_status_array))
                    {
                        $this->data['quantity_status_error'] = lang('max_products_per_order_reached');
                        $insert_order_error = true;
                    }

                }

                /*************************************************************/

                $reset_coupon_msg = '';

                if($cart_data->coupon_discount != 0)
                {
                    $reset_coupon_result = $this->shopping_cart->reset_cart_coupon();

                    if($reset_coupon_result[0] != 1)
                    {
                        $reset_coupon_msg = $reset_coupon_result[1];
                        $this->data['reset_coupon_msg'] = $reset_coupon_msg;
                    }
                }

                //if($cart_data->shipping_cost > 0)
                {
                    $this->update_cart_shipping_cost($cart_data->shipping_company_id, 1);
                }

                $is_wholesaler   = false;
                $total_before    = 0;

                $cart_data         = $this->shopping_cart->shopping_cart_data();
                $contents          = $this->shopping_cart->contents();
                $coupon_discount   = $cart_data->coupon_discount;
                $user_id           = $cart_data->user_id;
                $payment_option_id = $cart_data->payment_option_id;
                $bank_id           = $cart_data->bank_id;
                $voucher_number    = '';
                $cart_stores       = $this->shopping_cart->get_cart_stores($cart_data->id, $this->data['lang_id']);
                $settings          = $this->user_bootstrap->get_settings();


                if($this->data['is_logged_in'])
                {
                    $wholesaler_customer_group_id = $settings->wholesaler_customer_group_id;

                    $user_data = $this->user_bootstrap->get_user_data();

                    if($user_data)
                    {
                        $user_customer_group = $user_data->customer_group_id;
                        $is_wholesaler  = false;

                        if($user_customer_group == $wholesaler_customer_group_id)
                        {
                            $is_wholesaler  = true;
                        }
                    }
                }

                $contents_array = array();
                $shipping       = 0;
                $cart_vats = 0;

                foreach($cart_stores as $store)
                {
                    $cart_stores_products = $this->shopping_cart->get_cart_stores_products($cart_data->id, $store->store_id);

                    $products_array           = array();
                    $cart_store_final_total   = 0;

                    foreach($cart_stores_products as $content)
                    {
                        if($content->product_id != 0)
                        {
                            $product_data = $this->products_model->get_product_eith_translation_data($content->product_id, $this->data['lang_id']);
                            $content->{'quantity_per_serial'} = $product_data->quantity_per_serial;
                            $content->{'route'}         = $product_data->route;
                            $content->{'store_name'}    = $this->stores_model->get_store_name($content->store_id, $lang_id);
                            $content->{'cat_name'}      = $product_data->cat_name;
                            $content->{'cat_route'}     = $product_data->cat_route;
                            $content->{'name'}          = $product_data->title;

                            if($content->vat_type == 2)
                            {
                                $content->{'price'} = $content->price + $content->vat_value;
                            }

                            $count_user_optional_fields = $this->products_model->count_user_product_optional_fields($content->cart_product_id);

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
                                                  $content->{'image'} = $option->image;
                                                }
                                            }
                                        }

                                    }
                                }

                                $content->user_optional_fields = $user_optional_fields;
                            }

                             //check shipping products
                            if($product_data->shipping == 1)
                            {
                                $shipping = 1;
                            }

                            if($content->vat_type == 2)
                            {
                              $cart_vats += $content->vat_value * $content->qty;
                            }

                        }

                        $products_array[]  = $content;
                        $total_before     += $content->price * $content->qty;
                        $cart_store_final_total += $content->final_price * $content->qty;
                    }

                    $store->{'products'}          = $products_array;
                    $store->{'store_final_total'} = $cart_store_final_total;

                    $contents_array[] = $store;

                }

                if($shipping == 1)
                {
                    $this->get_shipping_data();
                }

                // update shipping field

                $cart_shipping['needs_shipping'] = $shipping;
                $this->shopping_cart->update_cart($cart_data->id, $cart_shipping);

                /*************************************************************/
                $tax_msg  = '';

                if(isset($_POST['payment_option_id']))
                {
                    $payment_option_id    = intval($this->input->post('payment_option_id'));
                }

                if(isset($_POST['bank_id']))
                {
                    $bank_id    = intval($this->input->post('bank_id'));
                }

                $stores_count = $this->shopping_cart_model->count_cart_stores($cart_data->id);


                $tax = ($this->calculate_payment_tax($payment_option_id, $cart_data->final_total_price)) * $stores_count;
                $final_total_price_with_tax = $cart_data->final_total_price + $tax + $cart_data->shipping_cost + $cart_data->wrapping_cost ;//+ $cart_vats;

                //in exclusive vat , vat is added to final price
                /*if($this->config->item('vat_type') == 2)
                {
                    $final_total_price_with_tax = $final_total_price_with_tax + $cart_data->vat_value;
                }*/

                //if voucher store voucher number
                if($payment_option_id == 7)
                {
                    $voucher_number = $this->input->post('voucher');
                }

                $updated_data = array(
                    'final_total_price_with_tax' => $final_total_price_with_tax,
                    'vat_value'         => $cart_vats,
                    'payment_option_id' => $payment_option_id,
                    'bank_id'           => $bank_id,
                    'tax'               => $tax,
                    'voucher_number'    => $voucher_number
                );

                $this->shopping_cart->update_this_shopping_cart($updated_data);

                //update it in the object $cart_data
                $cart_data->final_total_price_with_tax  = $final_total_price_with_tax;
                $cart_data->payment_option_id           = $payment_option_id;
                $cart_data->bank_id                     = $bank_id;
                $cart_data->tax                         = $tax;
                $cart_data->voucher_number              = $voucher_number;

                if($cart_data->payment_option_id != 0)
                {
                    $payment_method_name = $this->payment_methods_model->get_payment_method_name($cart_data->payment_option_id, $lang_id);
                    $tax_msg = lang('taxes_on').' '.$payment_method_name;
                }

                //$this->get_payment_methods($user_id);

                $branches = $this->shopping_cart_model->get_branches_data(1);

                $xml_data = '<?xml version="1.0" encoding="utf-8"?><markers>';

                foreach ($branches as $branch)
                {
                    $xml_data .= '
                                	<marker branchid="'.$branch->id.'" name="'.$branch->name.'" lat="'.$branch->lat.'" lng="'.$branch->lng.'" category="Restaurant" address="" address2="" city="" state="" postal="" country="" phone="" email="" web="" hours1="" hours2="" hours3="" featured="" features="" />
                                 ';
                }
                $xml_data .= '</markers>';

                if(($cart_data->final_total_price_with_tax < $this->config->item('min_order_for_delivery')) && ($cart_data->shipping_type == 1))
                {
                    $not_exceed_min_for_delivery = false;
                }
                else
                {
                    $not_exceed_min_for_delivery = true;
                }

                //$this->get_send_gift_data();

                $checked_stores_count = $this->shopping_cart->count_cart_checked_stores($cart_data->id);

                $this->data['checked_stores_count']         = $checked_stores_count;
                $this->data['cart_stores']                  = $contents_array;
                $this->data['cart_data']                    = $cart_data;
                $this->data['is_wholesaler']                = $is_wholesaler;
                $this->data['coupon_discount']              = $coupon_discount;
                $this->data['total_price_before_discount']  = $total_before;
                $this->data['tax_msg']                      = $tax_msg;
                $this->data['insert_order_error']           = $insert_order_error;
                $this->data['branches_xml']                 = $xml_data;
                $this->data['not_exceed_min_for_delivery']  = $not_exceed_min_for_delivery;
                $this->data['show_coupon'] = true;

                $this->load->view('shopping_cart_content', $this->data);
            }
        }
        else
        {
            $empty_msg = '0';

            echo $empty_msg;
        }
    }

    public function cart_total()
    {
      $cart_data = $this->shopping_cart->shopping_cart_data();
      $this->data['cart_data'] = $cart_data;

      $this->load->view('cart_total', $this->data);
    }

    public function get_payment_methods($user_id)
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
               $pocket_tax       = $this->calculate_payment_tax(1, $final_total);

               if(($final_total + $pocket_tax + $cart_data->vat_value) <= $user_balance)
               {
                    $pay_by_pocket = true;
                    //$pay_by_bank   = false;
                    $use_pocket    = 1;
               }
           }
        }

        // check reward points
        if($this->user_bootstrap->is_logged_in())
        {
            $enc_user_points   = $this->user_bootstrap->get_user_data()->user_points;
            $user_points       = $this->encryption->decrypt($enc_user_points, $secret_key, $secret_iv);
            $country_id        = $this->data['country_id'];
            $point_value       = $this->countries_model->get_reward_points($country_id);
            $user_points_value = $user_points * $point_value;
            $points_tax        = $this->calculate_payment_tax(2, $final_total);

            if(($final_total + $points_tax) <= $user_points_value)
            {
                $pay_by_reward_points = true;
            }
        }


        $display_lang_id     = $this->data['active_language_row']->id;
        $bank_accounts       = $this->user_bank_accounts_model->get_bank_accounts_result($display_lang_id, $user_id);

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


        $payment_options = $this->payment_methods_model->get_available_payment_options($display_lang_id, $user_customer_group, $not_included_ids, $use_pocket);

        //check payment options after applying option tax
        foreach($payment_options as $option)
        {
            $option_tax                  = $this->calculate_payment_tax($option->id, $final_total);
            $final_total_with_option_tax = $final_total + $option_tax;

            $country_min_order           = $this->currency->convert_from_default_currency($option->min_order_value, $country_id);

            if($final_total_with_option_tax >= $country_min_order)//$option->min_order_value)
            {
                if($option->id == 2)
                {
                  $option->{'page_text'} = lang('reward_point_value').' : '.$user_points_value.' </br>'.$option->page_text;
                }
                $payment_options_array[] = $option;
            }
        }

        $this->data['currency']                = $currency;
        $this->data['final_total']             = $final_total;
        $this->data['bank_accounts']           = $bank_accounts;
        $this->data['payment_options']         = $payment_options_array;//$payment_options;
        $this->data['pay_by_bank']             = $pay_by_bank;
        $this->data['pay_by_pocket']           = $pay_by_pocket;
        $this->data['pay_by_reward_points']    = $pay_by_reward_points;
    }


    public function calculate_payment_tax($payment_option_id, $total)
    {
        $tax = 0;
        if($payment_option_id ==0) return 0;

        $option_data = $this->payment_methods_model->get_option_data($payment_option_id);
        if(count($option_data) != 0)
        {
            $tax_percent = round(($option_data->extra_fees_percent * $total), 2)/ 100;
            $tax_amount  = $this->currency->convert_from_default_currency($option_data->extra_fees, $this->data['country_id']);

            $tax         = $tax_percent + $tax_amount;
        }

        return round($tax, 2);
    }

    public function update_quantity($new_qties)
    {
        /**
         * 0 => no stock
         * 1 => success
         * 2 => Max per user is reached
         * 3 => Country Error
         * 4 => Max per customer group
         * 5 => Quantity Error
         */

        $out_of_amount              = array();
        $country_id                 = $this->data['country_id'];
        $new_quantities_total_count = array_sum($new_qties);
        $cart_data                  = $this->shopping_cart->shopping_cart_data();
        $cart_contents              = $this->shopping_cart->contents();

        foreach($cart_contents as $row)
        {
            $product_id = $row->product_id;
            
            /*
            // Basic Code
            if(isset($new_qties[$product_id]))
            {
              $qty = $new_qties[$product_id];
            }
            else {
              $qty = $row->qty;
            }
            */
            // End Basic Code

            /*
            ** Mrzok Edit
            ** $product_refrence has been set to be array key as it contains imploded (product_id & cart_product_id)
            ** to handle the case of existing two rows with differrent ids on the shopping cart 
            ** for the same product but with different options selected 
            **/
            $cart_product_id    = $row->cart_product_id; // ==>Each lint call this variable is envolved in this Edit
            $product_refrence   = $product_id.'-'.$cart_product_id;
            if(isset($new_qties[$product_refrence]))
            {
              $qty = $new_qties[$product_refrence];
            }
            else {
              $qty = $row->qty;
            }
            /*** End Edit */

            if($product_id == 0)
            {
                $status = '1';
                $out_of_amount[$product_id] = 'false';
            }
            else
            {
                if($qty <= 0)
                {
                    $status = 5;//lang('shopping_cart_quanitity_error');
                    $out_of_amount[$product_id] = 'error';
                }
                else
                {
                    // Get Products Data from Products Database Table
                    $product_details = $this->shopping_cart_model->get_cart_product_row_details($product_id, $this->data['lang_id'], $this->data['country_id']);

                    if($product_details)
                    {
                        $check_product_country = $this->products_model->check_product_country_exist($product_id, $country_id);

                        if($check_product_country)
                        {
                            $non_stock_product  = 0;
                            if($product_details->quantity_per_serial == 0)
                            {
                                $non_stock_product = 1;

                                if($this->data['user_id'] != 0)
                                {
                                    $user_customer_group_data = $this->customer_groups_model->get_user_customer_group_data($this->data['user_id']);
                                    $max_per_order            = $user_customer_group_data->product_limit_per_order;

                                }
                                else
                                {
                                    $max_per_order = 100000;
                                }
                                $product_cart_data  = $this->shopping_cart->get_product_details($product_id, $cart_product_id);
                                $items_count        = $cart_data->items_count;
                                $product_cart_qty   = $product_cart_data->qty;

                                /*$max_per_order      = 1000;  // max qty of products in cart
                                $product_cart_data  = $this->shopping_cart->get_product_details($product_id);
                                $items_count        = $cart_data->items_count;
                                $product_cart_qty   = $product_cart_data->qty;
                                */
                                $product_qty = $max_per_order - ( $items_count - $product_cart_qty);

                            }
                            else
                            {
                                $min_stock   = $this->config->item('min_product_stock'); // Basic Code
                                /*******
                                ** Mrzok Comment ======>>>>>
                                ** here we need to check for user selcted options Quantities if exist for this $cart_product_id
                                **/
                                $user_product_shopping_cart   = $this->products_model->check_user_product_optional_fields($cart_product_id, $this->data['user_id']);
                                if($user_product_shopping_cart){
                                    /** Check selected options available quantities */
                                    // get user selected options for this product => if exist options
                                    $user_product_shopping_cart_options   = $this->products_model->get_user_optional_fields($cart_product_id, $this->data['lang_id']);

                                    // Splite posted options to Optional array and selected options array
                                    $selected_optionals_array   = array_values(array_column($user_product_shopping_cart_options, 'product_optional_field_id')); // array of the cart user selected product optionals ids
                                    $selected_options_array     = array_values(array_column($user_product_shopping_cart_options,'product_optional_field_value')); // array of the cart user selected options values

                                    // Check if the selected (Options of Product Optionals) count more than the minimum
                                    $selected_options_stock_count   = $this->products_model->count_product_available_quantity($product_id, $country_id , $selected_optionals_array , $selected_options_array);
                                    $product_qty                    = $selected_options_stock_count - $min_stock;
                                }
                                //// Product don't have user selected options 
                                else {
                                    // Start Basic Code
                                    $stock_count = $this->products_model->count_product_available_quantity($product_id, $country_id);

                                    $product_qty = $stock_count - $min_stock;
                                    // End Basic Code 
                                }
                            }


                            if($product_qty >= $qty)  // check stock
                            {
                                $out_of_amount[$product_id] = 'false';

                                //$product_details    = $this->products_model->get_product_row_details($product_id, $this->data['lang_id'], $this->data['country_id']);
                                $product_price_data = $this->products_lib->get_product_price_data($product_details);

                                if($new_quantities_total_count <= $product_price_data['max_products_per_customer_group'] || $product_price_data['max_products_per_customer_group'] == 0)  // check available products count per customer group
                                {
                                    if($qty <= $product_price_data[3] || $product_price_data[2] == 0)
                                    {
                                        $data = array('qty' => strip_tags($qty));

                                        if($product_price_data['vat_type'] == 2)//exclusive
                                        {
                                            $final_price = $product_price_data[1] + $product_price_data['vat_value'];
                                        }
                                        else
                                        {
                                            $final_price = $product_price_data[1];
                                        }

                                        $data = array(
                                                        'qty'             => strip_tags($qty)       ,
                                                        'discount'        => $product_price_data[5] ,
                                                        'price'           => $product_price_data[0] ,
                                                        'final_price'     => $final_price  + $row->optional_fields_cost
                                                     );

                                        $this->shopping_cart->update($product_id, $data, $non_stock_product , $cart_product_id);



                                        if($cart_data->coupon_discount != 0)
                                        {
                                            $this->shopping_cart->reset_cart_coupon();
                                        }

                                        $status = '1';
                                        $out_of_amount[$product_id] = 'false';

                                        // check if product needs shipping
                                        if($product_details->shipping == 1)
                                        {
                                            $status = '5';
                                        }
                                    }
                                    else
                                    {
                                        $status = '2';   // Max per user is reached
                                        $out_of_amount[$product_id] = 'true';
                                    }
                                }
                                else
                                {
                                    $status = '4';   // Max per customer group
                                    $out_of_amount[$product_id] = 'true';
                                }

                            }
                            else
                            {
                                $status = '0'; //not allowed qty
                                $out_of_amount[$product_id] = 'true';
                            }
                        }
                        else
                        {
                            $status = '3';
                            $out_of_amount[$product_id] = 'true';
                        }
                    }
                }
            }

            // $status_array[$product_id] = $status; // Basic Code
            $status_array[$product_refrence] = $status; // Mrzok Edit

        }

        $contents = $this->shopping_cart->contents();

        $new_price_data = array();

        foreach($contents as $content)
        {
            $field_price      = $content->price * $content->qty;
            $total_price      = $this->shopping_cart->total();

            $new_price_data[] = array(
                                        'field_price' => $field_price,
                                        'total_price' => $total_price,
                                        'product_id'  => $content->product_id,
                                        'status'      => $status ,
                                        'out_of_amount' => $out_of_amount[$content->product_id],
                                     );

        }

        return $status_array;
        //echo json_encode($new_price_data);
    }

    public function coupon_discount()
    {
        $coupon_code   = strip_tags($this->input->post('coupon_code', TRUE));

        echo json_encode($this->shopping_cart->coupon_discount($coupon_code));
    }

    public function delete_product()
    {
        $return          = array();
        $cart_product_id = $this->input->post('cart_product_id');
        $product_id      = $this->input->post('product_id');

        $delete_result   = $this->shopping_cart->destroy($cart_product_id);

        if($delete_result[0] == 1)
        {

            $return[0] = 1;
            $return[1] = lang('product_removed_from_cart');
        }
        else
        {
            $return[0] = 0;
            $return[1] = lang('product_not_removed_from_cart');
        }

        if($delete_result[1] != 'false')
        {
            //coupon result msg
            $return[2] = $delete_result[1];
        }

        echo json_encode($return);
    }

    public function float_num($number)
    {
        if(!floatval($number))
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    /*public function submit_product_optional_fields_all()
    {

        $lang_id    = $this->data['lang_id'];
        $product_id = $this->input->post('product_id', TRUE);

        $product_optional_fields = $this->products_model->get_product_optional_fields($product_id, $lang_id);

        foreach ($product_optional_fields as $field)
        {
            $custom_validation = '';
            if($field->field_type_id == 7)
            {
                $custom_validation = '|valid_email';
            }
            elseif($field->field_type_id == 10)
            {
                $custom_validation = '|callback_float_num';
            }

            if($field->required == 1)
            {
                $custom_validation .= '|required';
            }

            $this->form_validation->set_rules('optional_field['.$field->id.']', $field->label, 'trim'.$custom_validation);
        }

        $this->form_validation->set_rules('product_id', 'product', 'required');
        $this->form_validation->set_message('required', lang('required').': %s');
        $this->form_validation->set_message('valid_email', lang('valid_email'));
        $this->form_validation->set_message('integer', lang('valid_number').': %s');

        if ($this->form_validation->run() == FALSE)
        {
            $msg     = validation_errors();
            $success = 0;

            $return_array = array($success, $msg);
            echo json_encode($return_array);

        }
        else
        {
            $posted_options = $this->input->post('optional_field', TRUE);
            $has_options    = $this->input->post('has_options', TRUE);
            $cart_data      = $this->shopping_cart->shopping_cart_data();
            $cart_id        = $cart_data->id;

            $optional_fields_cost = 0;
            $optional_fields_weight = 0;
            $mouqete_vars = array();
            $cart_inserted = 0;

            foreach ($posted_options as $option_id=>$answer)
            {
                $excluded_from_cost = 0;
                $option_data = $this->optional_fields_model->get_optional_field_row($option_id);

                if($option_data->free == 0)
                {
                    $field_cost = $this->products_model->get_product_optional_field_cost($product_id, $option_id, $lang_id);
                }

                if(is_array($answer))
                {die('111');
                    foreach($answer as $row)
                    {
                        $weight = 0;

                        if($option_data->free == 0)
                        {
                            $cost_array = array();
                            $weight_array = array();



                            foreach($field_cost as $cost)
                            {
                                $cost_array[$cost->optional_field_option_id] = $cost->cost;
                                $weight_array[$cost->optional_field_option_id] = $cost->weight;
                            }


                            $cost = $cost_array[$row];
                            $weight = $weight_array[$row];
                        }
                        else
                        {
                            $cost = 0;
                        }

                        $optional_fields_data = array(
                                                    'user_id'                       => $this->user_bootstrap->get_user_id(),
                                                    'product_id'                    => $product_id,
                                                    'product_optional_field_id'     => $option_id,
                                                    'product_optional_field_value'  => strip_tags($row),
                                                    'shopping_cart_id'              => $cart_id,
                                                    'unix_time'                     => time()   ,
                                                    'cost'                          => $cost
                                                );

                        $this->products_model->insert_user_optional_fields_data($optional_fields_data);

                        $optional_fields_cost += $cost;
                        $optional_fields_weight += $weight;



                        // insert_product in shopping cart
                        $product_details    = $this->products_model->get_product_row_details($product_id, $this->data['lang_id'], $this->data['country_id']);
                        $product_price_data = $this->products_lib->get_product_price_data($product_details);

                        if(count($mouqete_vars) != 0)
                        {
                            $mouqette_cost = array_product($mouqete_vars);
                            $total_moquette_cost = $mouqette_cost * $product_price_data[1];

                            $optional_fields_cost += $total_moquette_cost;
                            $prod_optional_fields = $optional_fields_cost - $product_price_data[1];

                            $product_final_price = $total_moquette_cost;
                            //$optional_fields_cost = 0;
                        }
                        else
                        {
                            $product_final_price = $product_price_data[1] + $optional_fields_cost;
                            $prod_optional_fields = $optional_fields_cost;
                        }

                        $data    = array(
                                           'product_id'    => $product_details->id     ,
                                           'type'          => 'product'                ,
                                           'cat_id'        => $product_details->cat_id ,
                                           'store_id'      => $product_details->store_id,
                                           'qty'           => 1                        ,
                                           'name'          => $product_details->title  ,
                                           'weight'        => $product_details->weight + $optional_fields_weight ,
                                           'price'         => $product_price_data[0]   ,
                                           'final_price'   => $product_final_price     ,
                                           'discount'      => $product_price_data[5]   ,
                                           'image'         => $product_details->image  ,
                                           'reward_points' => $product_details->reward_points,
                                           'optional_fields_cost'   =>  $prod_optional_fields ,
                                           'vat_value'       => $product_price_data['vat_value'],
                                           'vat_percent'     => $product_price_data['vat_percent'],
                                           'checked'        => 1,

                                        );

                        $cart_row_id = $this->shopping_cart->insert($data, 1);

                        // update otional fields cart row id
                        $updated_data['cart_product_id'] = $cart_row_id;
                        $this->products_model->update_user_optional_fields($cart_id, $product_id, $updated_data);

                        $cart_inserted = 1;


                    }
                }
                else
                {die('222');
                    if($option_data->free == 0)
                    {
                        if($option_data->has_options == 1)
                        {
                            $cost_array = array();

                            foreach($field_cost as $cost)
                            {
                                $cost_array[$cost->optional_field_option_id] = $cost->cost;
                            }

                            $cost = $cost_array[$answer];
                        }
                        else
                        {
                            if($option_data->is_moquette == 1)
                            {
                                $mouqete_vars[] = $answer;
                                $excluded_from_cost = 1;
                                //$mouqette_cost *= $answer;
                            }

                            $cost = $field_cost[0]->cost;
                        }
                    }
                    else
                    {
                        $cost = 0;
                    }

                    $optional_fields_data = array(
                                                    'user_id'                       => $this->user_bootstrap->get_user_id(),
                                                    'product_id'                    => $product_id,
                                                    'product_optional_field_id'     => $option_id,
                                                    'product_optional_field_value'  => strip_tags($answer),
                                                    'shopping_cart_id'              => $cart_id,
                                                    'unix_time'                     => time()   ,
                                                    'cost'                          => $cost
                                                );

                    $this->products_model->insert_user_optional_fields_data($optional_fields_data);

                    if(!$excluded_from_cost)
                    {
                        $optional_fields_cost += $cost;
                    }
                }

                if(!$cart_inserted)
                {
                    // insert_product in shopping cart
                    $product_details    = $this->products_model->get_product_row_details($product_id, $this->data['lang_id'], $this->data['country_id']);
                    $product_price_data = $this->products_lib->get_product_price_data($product_details);

                    if(count($mouqete_vars) != 0)
                    {
                        $mouqette_cost = array_product($mouqete_vars);
                        $total_moquette_cost = $mouqette_cost * $product_price_data[1];

                        $optional_fields_cost += $total_moquette_cost;
                        $prod_optional_fields = $optional_fields_cost - $product_price_data[1];

                        $product_final_price = $total_moquette_cost;
                        //$optional_fields_cost = 0;
                    }
                    else
                    {
                        $product_final_price = $product_price_data[1] + $optional_fields_cost;
                        $prod_optional_fields = $optional_fields_cost;
                    }

                    $data    = array(
                                       'product_id'    => $product_details->id     ,
                                       'type'          => 'product'                ,
                                       'cat_id'        => $product_details->cat_id ,
                                       'store_id'      => $product_details->store_id,
                                       'qty'           => 1                        ,
                                       'name'          => $product_details->title  ,
                                       'weight'        => $product_details->weight + $optional_fields_weight ,
                                       'price'         => $product_price_data[0]   ,
                                       'final_price'   => $product_final_price     ,
                                       'discount'      => $product_price_data[5]   ,
                                       'image'         => $product_details->image  ,
                                       'reward_points' => $product_details->reward_points,
                                       'optional_fields_cost'   =>  $prod_optional_fields ,
                                       'vat_value'       => $product_price_data['vat_value'],
                                       'vat_percent'     => $product_price_data['vat_percent'],
                                       'checked'        => 1,

                                    );

                    $cart_row_id = $this->shopping_cart->insert($data, 1);

                    // update otional fields cart row id
                    $updated_data['cart_product_id'] = $cart_row_id;
                    $this->products_model->update_user_optional_fields($cart_id, $product_id, $updated_data);
                }




            }


            //check shopping cart coupon
            if($cart_data->coupon_discount != 0)
            {
                $this->shopping_cart->reset_cart_coupon();
            }

            $msg     = lang('product_added_to_cart_successfully');
            $success = 1;

            $return_array = array($success, $msg);
            echo json_encode($return_array);
        }

    }
    */

    public function submit_product_optional_fields()
    {
        $lang_id      = $this->data['lang_id'];
        $product_id   = $this->input->post('product_id', TRUE);
        $product_qty  = $this->input->post('product_qty', TRUE);
        $qty_error    = false;

        $product_details    = $this->products_model->get_product_row_details($product_id, $this->data['lang_id'], $this->data['country_id']);

        if($product_details->quantity_per_serial == 1)
        {
            // Check if the products count more than the minimum
            $stock_count = $this->products_model->count_product_available_quantity($product_id, $this->data['country_id']);//,$selected_optionals_array,$selected_options_array);
            $min_stock   = $this->config->item('min_product_stock');
            $available_qty = $stock_count - $min_stock;

            if($available_qty < 1 )
            {
                $message = lang('no_stock');
                $qty_error = true;
            }
        }
        else{
            $is_stock_product = true;
        }

        if($qty_error)
        {
          echo json_encode(array(0, lang('no_stock')));
        }
        else {
            $product_optional_fields = $this->products_model->get_product_optional_fields($product_id, $lang_id);

            foreach ($product_optional_fields as $field)
            {
                $custom_validation = '';
                if($field->field_type_id == 7)
                {
                    $custom_validation = '|valid_email';
                }
                elseif($field->field_type_id == 10)
                {
                    $custom_validation = '|callback_float_num';
                }

                if($field->required == 1)
                {
                    $custom_validation .= '|required';
                }

                $this->form_validation->set_rules('optional_field['.$field->id.']', $field->label, 'trim'.$custom_validation);
            }

            $this->form_validation->set_rules('product_id', 'product', 'required');
            $this->form_validation->set_message('required', lang('required').': %s');
            $this->form_validation->set_message('valid_email', lang('valid_email'));
            $this->form_validation->set_message('integer', lang('valid_number').': %s');

            if ($this->form_validation->run() == FALSE)
            {
                $msg     = validation_errors();
                $success = 0;

                $return_array = array($success, $msg);
                echo json_encode($return_array);

            }
            else
            {
                $posted_options = $this->input->post('optional_field', TRUE);
                $qties          = $this->input->post('op_qty', TRUE);
                $has_options    = $this->input->post('has_options', TRUE);
                $cart_data      = $this->shopping_cart->shopping_cart_data();
                $cart_id        = $cart_data->id;

                $optional_fields_cost = 0;
                $optional_fields_weight = 0;
                $mouqete_vars = array();

                /* *
                 * START Mrzok Edit : Checking the exisiting quantity of selected options ,
                 * if the product itself have options and the user already selected options
                 * */
                
                // this variable indicates that there is not exist same product with the same selected option ,
                // below it will contain an object of cart product data if exist
                // we should use this object to update exist cart product row with the new quantity requested if it is available
                $same_cart_product_exist = false ; 

                if (count($product_optional_fields) > 0 && count($posted_options) > 0 )
                {
                    // Splite posted options to Optional array and selected options array
                    $selected_optionals_array   = array_keys($posted_options);
                    $selected_options_array     = array_values($posted_options);

                    // Check if the selected (Options of Product Optionals) count more than the minimum
                    $selected_options_stock_count   = $this->products_model->count_product_available_quantity($product_id, $this->data['country_id'],$selected_optionals_array,$selected_options_array);
                    $selected_options_available_qty = $selected_options_stock_count - $min_stock;
                    
                    // Mrzok Edit 2
                    // check if the same product with the selected options is previously added to cart and check it's quantity 
                    $same_cart_product_exist    = $this->shopping_cart->get_product_with_selected_options_from_shopping_cart($lang_id,$product_id, $selected_optionals_array, $selected_options_array);
                    // update requsted qantity variable to be existing quantity in addition of requsted quantity => IF EXIST
                    $product_qty                = $product_qty + ( isset($same_cart_product_exist->qty) ? $same_cart_product_exist->qty : 0 ) ;
                    // End Edit 2
                    
                    /* Validate that system existing quantity for selecterd options is :
                     * - great than minimum stock which is configured by SYSTEM SETTINGS 
                     * - less than The requested quantity
                     * */
                    if($selected_options_available_qty < 1 || $product_qty > $selected_options_stock_count)
                    {
                        echo json_encode(array(0, lang('no_stock')));
                        // exit of continuing the code .... stop adding the product to the cart & display error message
                        return;
                    }
                }
                /* END Checking the exisiting quantity of selected options */


                $product_details    = $this->products_model->get_product_row_details($product_id, $this->data['lang_id'], $this->data['country_id']);
                $product_price_data = $this->products_lib->get_product_price_data($product_details);

                $non_stock_product = 0;
                if($product_details->quantity_per_serial == 0)
                {
                    $non_stock_product = 1;
                }

                // check that no same cart object and it is false = > This Condithion is Mrzok Edit 2
                if( !is_array($same_cart_product_exist) && !$same_cart_product_exist) 
                {
                    // Insert New CART PRODUCT row

                    // Insert user selected options to db
                    foreach ($posted_options as $option_id=>$answer)
                    {
                        $excluded_from_cost = 0;
                        $option_data = $this->optional_fields_model->get_optional_field_row($option_id);

                        if($option_data->free == 0)
                        {
                            $field_cost = $this->products_model->get_product_optional_field_cost($product_id, $option_id, $lang_id);
                        }

                        if(is_array($answer))
                        {
                            foreach($answer as $key=>$row)
                            {
                                $weight = 0;

                                if($option_data->free == 0)
                                {
                                    $cost_array = array();
                                    $weight_array = array();

                                    foreach($field_cost as $cost)
                                    {
                                        $cost_array[$cost->optional_field_option_id] = $cost->cost;
                                        $weight_array[$cost->optional_field_option_id] = $cost->weight;
                                    }

                                    //check customer group
                                    $conds = array(
                                                    'customer_group_id' => $this->data['customer_group_id'],
                                                    'option_id'         => $row,
                                                    'optional_field_id' => $option_id,
                                                    'product_id'        => $product_id,

                                                );


                                    //get customer group cost
                                    $customer_group_price = $this->products_model->get_table_data('optional_fields_customer_groups_prices', $conds, 'row');
                                    $cost = $cost_array[$row];

                                    if(count($customer_group_price) != 0)
                                    {
                                        if($customer_group_price->group_price != 0)
                                        {
                                            $cost = $customer_group_price->group_price;
                                        }
                                    }

                                    $weight = $weight_array[$row];
                                }
                                else
                                {
                                    $cost = 0;
                                }

                                $qty = $qties[$option_id][$key];

                                $cost = $cost * $qty;

                                $optional_fields_data = array(
                                                            'user_id'                       => $this->user_bootstrap->get_user_id(),
                                                            'product_id'                    => $product_id,
                                                            'product_optional_field_id'     => $option_id,
                                                            'product_optional_field_value'  => strip_tags($row),
                                                            'shopping_cart_id'              => $cart_id,
                                                            'unix_time'                     => time()   ,
                                                            'cost'                          => $cost,
                                                            'qty'                           => $qty
                                                        );

                                $this->products_model->insert_user_optional_fields_data($optional_fields_data);

                                $optional_fields_cost += $cost;
                                $optional_fields_weight += $weight;
                            }
                        }
                        else
                        {
                            if($option_data->free == 0)
                            {
                                if($option_data->has_options == 1)
                                {
                                    $cost_array = array();

                                    foreach($field_cost as $cost)
                                    {
                                        $cost_array[$cost->optional_field_option_id] = $cost->cost;
                                    }

                                    $cost = $cost_array[$answer];
                                }
                                else
                                {
                                    if($option_data->is_moquette == 1)
                                    {
                                        $mouqete_vars[] = $answer;
                                        $excluded_from_cost = 1;
                                        //$mouqette_cost *= $answer;
                                    }

                                    $cost = $field_cost[0]->cost;
                                }
                            }
                            else
                            {
                                $cost = 0;
                            }

                            $optional_fields_data = array(
                                                            'user_id'                       => $this->user_bootstrap->get_user_id(),
                                                            'product_id'                    => $product_id,
                                                            'product_optional_field_id'     => $option_id,
                                                            'product_optional_field_value'  => strip_tags($answer),
                                                            'shopping_cart_id'              => $cart_id,
                                                            'unix_time'                     => time()   ,
                                                            'cost'                          => $cost
                                                        );

                            $this->products_model->insert_user_optional_fields_data($optional_fields_data);

                            if(!$excluded_from_cost)
                            {
                                $optional_fields_cost += $cost;
                            }
                        }
                    }

                    // insert_product in shopping cart

                    /*
                    // Basic Code
                    $product_details    = $this->products_model->get_product_row_details($product_id, $this->data['lang_id'], $this->data['country_id']);
                    $product_price_data = $this->products_lib->get_product_price_data($product_details);
                    // End Basic Code
                    */

                    if(count($mouqete_vars) != 0)
                    {
                        $mouqette_cost = array_product($mouqete_vars);
                        $total_moquette_cost = $mouqette_cost * $product_price_data[1];

                        $optional_fields_cost += $total_moquette_cost;
                        $prod_optional_fields = $optional_fields_cost - $product_price_data[1];

                        $product_final_price = $total_moquette_cost;
                        //$optional_fields_cost = 0;
                    }
                    else
                    {
                        $product_final_price = $product_price_data[1] + $optional_fields_cost;
                        $prod_optional_fields = $optional_fields_cost;
                    }


                    $data    = array(
                                    'product_id'    => $product_details->id     ,
                                    'type'          => 'product'                ,
                                    'cat_id'        => $product_details->cat_id ,
                                    'store_id'      => $product_details->store_id,
                                    'qty'           => $product_qty             ,
                                    'name'          => $product_details->title  ,
                                    'weight'        => $product_details->weight + $optional_fields_weight ,
                                    'price'         => $product_price_data[0]   ,
                                    'final_price'   => $product_final_price     ,
                                    'discount'      => $product_price_data[5]   ,
                                    'image'         => $product_details->image  ,
                                    'reward_points' => $product_details->reward_points,
                                    'optional_fields_cost'   =>  $prod_optional_fields ,
                                    'vat_value'       => $product_price_data['vat_value'],
                                    'vat_percent'     => $product_price_data['vat_percent'],
                                    'checked'        => 1,

                                    );

                    $cart_row_id = $this->shopping_cart->insert($data, 1);

                    // update otional fields cart row id
                    $updated_data['cart_product_id'] = $cart_row_id;
                    $this->products_model->update_user_optional_fields($cart_id, $product_id, $updated_data);

                    //check shopping cart coupon
                    if($cart_data->coupon_discount != 0)
                    {
                        $this->shopping_cart->reset_cart_coupon();
                    }

                    $msg     = lang('product_added_to_cart_successfully');
                    $success = 1;
                }else {
                    // Mrzok Edit
                    // Update Current CART PRODUCT row
                    $cartRowId  = $same_cart_product_exist->cart_product_id;
                    if($product_price_data['vat_type'] == 2) //exclusive
                    {
                        $final_price = $product_price_data[1] + $product_price_data['vat_value'];
                    }
                    else
                    {
                        $final_price = $product_price_data[1];
                    }

                    $data       = array(
                                        'qty'             => $product_qty ,
                                        'discount'        => $product_price_data[5],
                                        'coupon_discount' => 0,
                                        'price'           => $product_price_data[0] ,//+ $cart_product_data->optional_fields_cost),
                                        'final_price'     => $final_price,
                                        'vat_value'       => $product_price_data['vat_value'],
                                        'vat_percent'     => $product_price_data['vat_percent'],
                                        'vat_type'        => $product_price_data['vat_type']
                                        );

                    if($this->shopping_cart->update($product_details->id, $data, $non_stock_product, $cartRowId))
                    {
                        //check shopping cart coupon
                        if($cart_data->coupon_discount != 0)
                        {
                            $this->shopping_cart->reset_cart_coupon();
                        }

                        $msg     = lang('product_qty_updated');
                        $success = 1;
                    }
                    else {
                        // quantity not updated
                        $msg     = lang('no_stock');
                        $success = 0;
                    }

                }

                $return_array = array($success, $msg);
                echo json_encode($return_array);
            }
        }

     }

     public function update_cart_shipping_country()
     {
        $shipping_country_id = intval(strip_tags($this->input->post('country_id', TRUE)));
        $cart_data           = $this->shopping_cart->shopping_cart_data();

        $updated_data['shipping_country_id'] = $shipping_country_id;
        $this->shopping_cart->update_cart($cart_data->id, $updated_data);
        //$this->shopping_cart->check_cart_shipping_cost();

    }



     public function update_cart_shipping_cost($shipping_company_id=0, $mode=0)
    {
        if(isset($_POST['company_id']))
        {
            $shipping_company_id = intval(strip_tags($this->input->post('company_id', TRUE)));
        }

        $cart_data    = $this->shopping_cart->shopping_cart_data();

        $updated_data = array(
                                'shipping_company_id' => $shipping_company_id
                             );

        $this->shopping_cart->update_cart($cart_data->id, $updated_data);

        $this->shopping_cart->check_cart_shipping_cost();

        $cart_data = $this->shopping_cart->shopping_cart_data();

        $cities_options = array();

        if($shipping_company_id == 1)
        {
            $this->load->library('shipping_gateways/smsa');

            $cities_data = '';//$this->smsa->getRTLCities();

            $cities = array();//explode("    ", $cities_data->getRTLCitiesResult->any);

            if(count($cities) != 0)
            {
                $cities_options = '<select name="shipping_town" class="form-control select2 shipping_town_select" id="">';

                foreach($cities as $key=>$city)
                {
                    $city_val = trim($city);
                    $cities_options .= '<option>'.$city_val.'</option>';

                }

                $cities_options .= '</select>';
            }
        }

        if($mode == 0)
        {
            echo json_encode(array($cart_data->shipping_cost, $cities_options));
        }
        else
        {
            return true;
        }

    }

    public function update_cart_shipping_address()
    {
        $shipping_address = trim(strip_tags($this->input->post('shipping_address', TRUE)));
        $cart_data        = $this->shopping_cart->shopping_cart_data();

        $updated_data['shipping_address'] = $shipping_address;
        $this->shopping_cart->update_cart($cart_data->id, $updated_data);
    }

    public function update_cart_shipping_city()
    {
        $shipping_city = trim(strip_tags($this->input->post('shipping_city', TRUE)));
        $cart_data     = $this->shopping_cart->shopping_cart_data();

        $updated_data['shipping_city'] = $shipping_city;
        $this->shopping_cart->update_cart($cart_data->id, $updated_data);
    }

    public function update_cart_shipping_town()
    {
        $shipping_town  = trim(strip_tags($this->input->post('shipping_town', TRUE)));
        $cart_data      = $this->shopping_cart->shopping_cart_data();

        $updated_data['shipping_city'] = $shipping_town;
        $this->shopping_cart->update_cart($cart_data->id, $updated_data);
    }

    public function update_cart_shipping_district()
    {
        $shipping_district = trim(strip_tags($this->input->post('shipping_district', TRUE)));
        $cart_data         = $this->shopping_cart->shopping_cart_data();

        $updated_data['shipping_district'] = $shipping_district;
        $this->shopping_cart->update_cart($cart_data->id, $updated_data);
    }

    public function update_cart_shipping_name()
    {
        $shipping_name = trim(strip_tags($this->input->post('shipping_name', TRUE)));
        $cart_data     = $this->shopping_cart->shopping_cart_data();

        $updated_data['shipping_name'] = $shipping_name;
        $this->shopping_cart->update_cart($cart_data->id, $updated_data);
    }

    public function get_shipping_data()
    {

        // available shipping_methods
        $methods_array          = array();
        $methods_array[NULL]    = '--------------';

        $this->data['shipping_companies'] = array();
        $this->data['countries_costs'] = array();

        $shipping_methods = $this->shopping_cart->get_available_shipping_methods($this->data['lang_id']);

        foreach ($shipping_methods as $method)
        {
            $methods_array[$method->id] = $method->name;

            if($method->id == 1)
            {
                // get recieve from home data
                $this->get_recieve_from_home_data();

            }

            if($method->id == 2)
            {
                // get branches data
            }

            if($method->id == 3)
            {
                // get shipping data
                $this->get_shipping_method_data();
            }
        }
        //$methods_array = rsort($methods_array);

        $this->data['shipping']         = true;
        $this->data['shipping_methods'] = $methods_array;
    }

    public function get_recieve_from_home_data()
    {
        $cities_array            = array();
        $cities_array[NUll]      = '------------------';

        $cities                  = $this->cities_model->get_store_cities($this->data['lang_id'], $this->data['country_id']);

        foreach($cities as $row)
        {
            $cities_array[$row->id] = $row->name;
        }

        $this->data['shipping']           = true;
        $this->data['cities']             = $cities_array;
    }

    public function get_shipping_method_data()
    {
        $companies_array        = array();
        $costs_array            = array();
        $address_array          = array();
        $companies_array[NUll]  = '------------------';
        $costs_array[NUll]      = '------------------';
        //$address_array[NULL]    = '------------------';

        $shipping_companies     = $this->companies_model->get_shipping_companies_result($this->data['lang_id']);
        $countries_costs        = $this->costs_model->get_shipping_costs_result($this->data['lang_id']);
        $store_currency_data    = $this->currency->get_country_currency_data($this->data['country_id']);
        //$user_addresses         = $this->shopping_cart_model->get_user_addresses($this->data['user_id']);


        foreach($shipping_companies as $comp)
        {
            $companies_array[$comp->id] = $comp->name . ' ( ' . lang('estimated_delivery_time') . ' ' . $comp->estimated_delivery_time . ' ' . lang('day') .' )';
        }

        foreach($countries_costs as $row)
        {
            if($row->currency_id != $store_currency_data->id)
            {
                $cost = $this->currency->get_amount_from_currency_to_currency($row->cost, $row->currency_id, $store_currency_data->id);
            }
            else
            {
                $cost = $row->cost;
            }

            $costs_array[$row->id] = $row->country;
        }

        /*foreach($user_addresses as $row)
        {
            $address_array[$row->id] = $row->title.' -- '.$row->address;//.' | <a href="'.base_url().'users/user_address/address/'.$row->id.'">'.lang('edit').'</a>';
        }*/

        $this->data['shipping']           = true;
        $this->data['shipping_companies'] = $companies_array;
        $this->data['countries_costs']    = $costs_array;
        //$this->data['user_addresses']     = $address_array;
    }

    public function update_cart_shipping_type()
    {
        $shipping_type = intval($this->input->post('shipping_type', true));
        $cart_data     = $this->shopping_cart->shopping_cart_data();

        $updated_data['shipping_type'] = $shipping_type;
        $this->shopping_cart->update_cart($cart_data->id, $updated_data);

    }

    public function update_cart_shipping_phone()
    {
        $shipping_phone = trim(strip_tags($this->input->post('shipping_phone', TRUE)));
        $cart_data      = $this->shopping_cart->shopping_cart_data();

        $updated_data['shipping_phone'] = $shipping_phone;
        $this->shopping_cart->update_cart($cart_data->id, $updated_data);

        echo 'done';
    }

    public function update_cart_user_address()
    {
      $address_id = intval($this->input->post('user_add_id', TRUE));
      $cart_data  = $this->shopping_cart->shopping_cart_data();

      $updated_data = array(
        'address_id' => $address_id,
        'shipping_type' => 4 //my addresses
      );

      $this->shopping_cart->update_cart($cart_data->id, $updated_data);

      echo 'done';
    }

    public function get_branch_list()
    {
        $this->load->library('location_locator');

        $output     = array();
        $lang_id    = intval($this->input->post('langId', TRUE));
        $lng        = strip_tags($this->input->post('lng', TRUE));
        $lat        = strip_tags($this->input->post('lat', TRUE));

        $list       = $this->location_locator->get_branch_list($lat.','.$lng, $lang_id);

        if(count($list) != 0)
        {
            $output = $list;
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($output));
    }

    public function get_send_gift_data()
    {

        $wrapping_array = array();

        $wrapping = $this->admin_wrapping_model->get_wrapping_type_data($this->data['lang_id'], 1);
        $currency = $this->countries_model->get_country_symbol($this->data['country_id']);
        $wrapping_array[0] = lang('choose');
        foreach($wrapping as $row)
        {
            $wrapping_array[$row->id] = $row->color .' - ' .$row->cost.' '.$currency.' <img src="'.base_url().'assets/uploads/'.$row->image.'"/>';
        }
        $this->data['wrapping'] = $wrapping_array;
    }

    public function update_cart_gift_cost()
    {

        $wrapping_id    = intval($this->input->post('wrapping_id', true));
        $gift_msg       = strip_tags($this->input->post('gift_msg', true));
        $cart_data      = $this->shopping_cart->shopping_cart_data();

        $wrapping_data  = $this->admin_wrapping_model->get_wrapping_row($wrapping_id);
        if(count($wrapping_data) != 0)
        {
          $total_cost     = $wrapping_data->cost;
        }
        else {
          $total_cost = 0;
        }

        $updated_data   = array(
                                    'send_as_gift'          => 1                    ,
                                    'wrapping_id'           => $wrapping_id         ,
                                    //'ribbon_id'             => $ribbon_id           ,
                                    //'box_id'                => $box_id              ,
                                    'wrapping_only_cost'    => $total_cost ,
                                    //'ribbon_only_cost'      => $ribbon_data->cost   ,
                                    //'box_only_cost'         => $box_data->cost      ,
                                    'gift_msg'              => $gift_msg,
                                    'wrapping_cost'         => $total_cost
                                );

        $this->shopping_cart->update_cart($cart_data->id, $updated_data);
        $this->shopping_cart->update_cart_total_prices($cart_data->id);

        $cart_data = $this->shopping_cart->shopping_cart_data();
        echo json_encode($cart_data);
    }

    public function reset_cart_gift_cost()
    {
        $cart_data      = $this->shopping_cart->shopping_cart_data();
        $updated_data   = array(
                                  'send_as_gift'       => 0,
                                  'wrapping_id'        => 0,
                                  'ribbon_id'          => 0,
                                  'box_id'             => 0,
                                  'wrapping_cost'      => 0,
                                  'wrapping_only_cost' => 0,
                                  'ribbon_only_cost'   => 0,
                                  'box_only_cost'      => 0,
                                );

        $this->shopping_cart->update_cart($cart_data->id, $updated_data);
    }


    public function update_checked_products()
    {
        $stores_ids = $this->input->post('checkd_stores', true);

        $cart_data  = $this->shopping_cart->shopping_cart_data();
        $cart_id    = $cart_data->id;

        $checked_data['checked']     = 1;
        $not_checked_data['checked'] = 0;

        $this->shopping_cart_model->update_check_stores_products($stores_ids, $cart_id, $checked_data);
        $this->shopping_cart_model->update_not_checked_stores_products($stores_ids, $cart_id, $not_checked_data);

        $cart_checked_stores = $this->shopping_cart->count_cart_checked_stores($cart_id);

        echo $cart_checked_stores;
    }

    public function shipping_address()
    {
      if(!$this->data['is_logged_in'])
      {
        redirect('User_login', 'refresh');
      }
      else
      {
        if($this->check_shopping_cart)
        {
          $user_addresses = $this->shopping_cart_model->get_user_addresses($this->data['user_id']);

          $this->data['user_addresses'] = $user_addresses;
          $this->data['hide_menu']  = true;
          $this->data['is_cart']    = true;

          $this->data['content'] = $this->load->view('cart_address', $this->data, true);
          $this->load->view('site/main_frame',$this->data);
        }else {
          redirect('Shopping_Cart', true);
        }
      }
    }

    public function gift_data()
    {
      if(!$this->data['is_logged_in'])
      {
        redirect('User_login', 'refresh');
      }
      else
      {
        if($this->check_shopping_cart)
        {
          if($this->cart_data->send_as_gift == 1)
          {
            $wrapping_data = $this->get_send_gift_data();

            $this->data['wrapping_data'] = $wrapping_data;
            $this->data['hide_menu'] = true;
            $this->data['is_cart'] = true;

            $this->data['content'] = $this->load->view('cart_send_as_gift', $this->data, true);
            $this->load->view('site/main_frame',$this->data);
          }
          else {
            redirect(base_url().'Cart_Payment', 'refresh');
          }
        }else {
          redirect('Shopping_Cart', true);
        }
      }
    }

    public function cart_payment_methods()
    {
      if(!$this->data['is_logged_in'])
      {
        redirect('User_login', 'refresh');
      }
      else
      {
        if($this->check_shopping_cart)
        {
          $this->get_payment_methods($this->data['user_id']);

          $this->data['hide_menu'] = true;
          $this->data['is_cart'] = true;

          $this->data['content'] = $this->load->view('cart_payment_methods', $this->data, true);
          $this->load->view('site/main_frame',$this->data);
        }
        else {
          redirect('Shopping_Cart', true);
        }
      }
    }

    private function _get_products_prices($products_array, $sort = 0, $search=0, $offers_only=0, $resturn_ids=0)
    {
        $products_new_array = array();
        $products_ids       = array();
        $products_html      = '';
        $product_route      = $this->data['product_route'];
        $currency           = $this->countries_model->get_country_symbol($this->cart_data->country_id);

        foreach($products_array as $product)
        {
            $product_details = $this->products_model->get_product_row_details($product->product_id, $this->data['lang_id'], $this->data['country_id']);

            if($product_details->quantity_per_serial == 1)
            {
                $product_qty   = $this->products_model->count_product_available_quantity($product->product_id, $this->data['country_id']);
                $min_stock     = $this->config->item('min_product_stock');
                $rest_qty      = $this->config->item('rest_product_qty');
                $available_qty = $product_qty - $min_stock;

                if($available_qty <= $rest_qty && $product_qty!=0)
                {
                  $stock_qty = $available_qty;
                  $product_details->{'stock_qty'} = $stock_qty;
                }

                if($available_qty > 0)
                {
                    $availability = lang('available');
                }
                else
                {
                    $availability = lang('unavailable');
                }
            }
            else
            {
                $availability = lang('available');
            }

            $product_details->{'availability'} = $availability;

            $product_price_data = $this->products_lib->get_product_price_data($product_details);
            $product_images     = $this->products_model->get_product_images($product->product_id, 1);

            if(isset($product_images[0]->image))
            {
                $product_details->{'hover_image'}  = $product_images[0]->image;
            }
            else
            {
              $product_details->{'hover_image'}  = $product_details->image;
            }

            $product_details->{'product_images'} = $product_images;

            $currency = $this->currency->get_country_currency_name($this->data['country_id'], $this->data['lang_id']);
            $product_details->{'currency'} = $currency;


            if(($offers_only && $product_price_data[3]==1) || $offers_only==0)
            {
                $product_details->{'price_before'} = $product_price_data[0];
                $product_details->{'price'}        = $product_price_data[1];
                $product_details->{'strike'}       = $product_price_data[3];

                $products_new_array[] = $product_details;

                $avg_product_rate = intval($product_details->rating_avg);
                $remain = 5 - $avg_product_rate ;

                $product_name = $product_details->title;

                if(strlen($product_name) > 20)
                {
                  $product_name = substr($product_name, 0, 20).'...';
                }

                $products_html .= '<div class="container-prod">
                  <div class="row no-gutters">
                    <div class="col-md-3">
                      <div class="img">
                        <a href="'.base_url().$product_route.$product_details->route.'">
                          <img style="width:78px; height: 72px;" src="'.base_url().'assets/uploads/products/'.$product_details->image.'" alt="" />
                        </a>
                      </div>
                    </div>
                    <div class="col-md-9">
                      <div class="info">
                        <h4><a href="'.base_url().$product_route.$product_details->route.'">'.$product_name.'</a></h4>
                        <p class="price">
                          <span class="new-price">'.$product_price_data[1].' '.$currency.' </span>';
                          if($product_price_data[0] != $product_price_data[1]){
                            $products_html .= '<span class="old-price">'.$product_price_data[0].' '.$currency.'</span>';
                          }

                          $products_html .= '</p>
                          <div class="rated">
                            <ul>';

                          for($x = 0 ; $x < $avg_product_rate ; $x ++) {
                            $products_html .= '<li class="active">
                              <svg>
                                <use xlink:href="#star"></use>
                              </svg>
                            </li>';
                          }
                          for($y = 0 ; $y < $remain ; $y ++) {
                            $products_html .= '<li>
                              <svg>
                                <use xlink:href="#star"></use>
                              </svg>
                            </li>';
                          }


                    $products_html .=  '<li>
                              <span>('.$product_details->rating_times.')</span>
                            </li>
                          </ul>
                        </div>

                      </div>
                    </div>

                  </div>
                </div>';
            }


            $products_ids[] = $product->product_id;
        }

        if($search == 1 && count($products_ids) !=0)
        {
            $this->site_products_model->update_searched_products($products_ids);
        }

        if($sort == 3 || $sort == 4)
        {
            if($sort == 3)
            {
                function compareOrder($a, $b)
                {
                  if ($a->price < $b->price)
                  {
                      return -1;
                  }
                  else if ($a->price > $b->price)
                  {
                      return 1;
                  }
                  else
                  {
                      return 0;
                  }
                }

                usort($products_new_array, 'compareOrder');
            }
            else if($sort == 4)
            {
                function compareOrder($a, $b)
                {
                  if ($a->price > $b->price)
                  {
                      return -1;
                  }
                  else if ($a->price < $b->price)
                  {
                      return 1;
                  }
                  else
                  {
                      return 0;
                  }
                }

                uasort($products_new_array, 'compareOrder');
            }
        }

        return $products_html;

    }


/************************************************************************/
}
