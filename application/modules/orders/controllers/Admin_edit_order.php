<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Admin_edit_order extends CI_Controller
{
    public $lang_row;
    
    public function __construct()
    {
        parent::__construct();
        
        require(APPPATH . 'includes/global_vars.php');
        
        $this->load->library('orders');
        $this->load->library('encryption');
        $this->load->library('notifications');
        $this->load->library('admin_products_lib');
        
        $this->config->load('encryption_keys');
        
        $this->load->model('admin_order_model');
        $this->load->model('products/products_model');
        $this->load->model('users/user_balance_model');
        $this->load->model('optional_fields/optional_fields_model');
        
        
        $this->lang_row = $this->admin_bootstrap->get_active_language_row();
        $this->settings = $this->global_model->get_config();
    }
    
    public function add_products()
    {
        $order_id       = $this->input->post('order_id');
        $qty            = $this->input->post('qty');
        $product_id     = $this->input->post('product_id');
        $country_id     = $this->input->post('country_id');
        $lang_id        = $this->data['active_language']->id;
        
        $this->form_validation->set_rules('order_id', lang('order_id'), 'required');
        $this->form_validation->set_rules('qty', lang('qty'), 'required');
        $this->form_validation->set_rules('product_id', lang('product'), 'required');
        $this->form_validation->set_rules('country_id', lang('country'), 'required');
        
        $this->form_validation->set_message('required', lang('required')."  : %s ");
        
        if ($this->form_validation->run() == FALSE)
        {
            $_SESSION['qty_error'] = validation_errors();
            $this->session->mark_as_flash('qty_error');   
        }
        else
        {
            
            $order_data         = $this->admin_order_model->get_order_data($order_id);
            $product_details    = $this->products_model->get_product_row_details($product_id, $lang_id, $order_data->country_id);
            
            $non_serial_product = false;
            $errors             = false;
            $available_count    = 0;
            
            if($product_details->quantity_per_serial == 0)
            {
                $non_serial_product = true;
                if($qty != 1)
                {
                    $_SESSION['qty_error'] = lang('qty_of_one_only');
                    $this->session->mark_as_flash('qty_error');
                    
                    $errors = true;
                }
            }
            
            
            if(! $non_serial_product && ! $errors)
            {
                $product_available_serials_count = $this->products_model->count_product_available_quantity($product_id, $country_id);
                $min_stock = $this->settings->min_product_stock;
                
                $available_count = $product_available_serials_count - $min_stock;
            }
            
            if(! $errors)
            {
                if($qty <= $available_count || $non_serial_product)
                { 
                    //$product_details     = $this->products_model->get_product_row_details($product_id, $lang_id, $order_data->country_id);
                    $product_price_data  = $this->admin_products_lib->get_product_price_data($product_details, $order_data->country_id, $order_data->user_id);
                    $product_info        = $this->products_model->get_product_country_data($product_id, $country_id);
                    
                    //insert product
                    $product_data = array(
                                            'order_id'           => $order_id,
                                            'type'               => 'product',
                                            'product_id'         => $product_id,
                                            'cat_id'             => $product_details->cat_id,
                                            'purchased_cost'     => $product_info->average_cost,
                                            'price'              => $product_price_data[0],
                                            'final_price'        => $product_price_data[1],
                                            'discount'           => $product_price_data[4],
                                            'qty'                => $qty,
                                            'reward_points'      => $product_details->reward_points,
                                            'reward_points_used' => 0,
                                            'recharge_card_used' => 0,
                                            'unix_time'          => time() 
                                         );
                                         
                    $this->orders_model->insert_order_products($product_data);
                    
                    if(! $non_serial_product)
                    {
                        $product_serials = $this->orders_model->generate_product_serials($product_id, $qty, $order_data->country_id);
                    
                    
                        foreach($product_serials as $serial)
                        {
                            $serials_data = array(
                                                    'order_id'          => $order_id,
                                                    'product_id'        => $product_id,
                                                    'product_serial_id' => $serial->id,
                                                    'unix_time'         => time()
                                                 );
                                                 
                            $serial_status['serial_status'] = 1;
                            $this->orders_model->update_serial_status($serial_status, $serial->id);                                     
                            $this->orders_model->insert_product_serials($serials_data);
                        }
                    }
                    
                    $order_data = $this->orders_model->get_order_data($order_id);
                    
                    if($order_data->coupon_discount != 0)
                    {
                        $this->orders->reset_order_coupon($order_id);
                    }
                    $this->orders->update_order_total_prices($order_id);
                    
                    $_SESSION['product_added_successfully'] = lang('product_added_successfully');
                    $this->session->mark_as_flash('product_added_successfully');
                }
                else
                {
                    $_SESSION['qty_error'] = lang('not_enough_product_quantity');
                    $this->session->mark_as_flash('qty_error');   
                }
            }
        }
        
        redirect('orders/admin_order/view_order/'.$order_id, 'refresh');
        
    }
    
    public function update_quantity()
    {
        $order_id   = $this->input->post('order_id');
        $product_id = $this->input->post('product_id');
        $qty        = $this->input->post('quantity');
        $country_id = $this->input->post('country_id');
        
        $order_product_data = $this->admin_order_model->get_order_product_data($order_id, $product_id);
        $new_qty = $qty - $order_product_data->qty;
        
        if($qty == $order_product_data->qty)
        {
            $_SESSION['qty_error'] = lang('qty_not_changed');
            $this->session->mark_as_flash('qty_error');
        }
        elseif($new_qty < 0)  // Reduce Product Quantity 
        {
            $free_qty = $order_product_data->qty - $qty;
            
            $free_serials = $this->admin_order_model->get_product_serials($free_qty, $product_id, $order_id);
            
            foreach($free_serials as $serial)
            {
                $serial_status['serial_status'] = 0;
                $this->orders_model->update_serial_status($serial_status, $serial->product_serial_id);
                $this->admin_order_model->delete_order_serials($order_id, $product_id, $serial->product_serial_id);
            }
            
            
            $product_data['qty'] = $qty;
            $this->admin_order_model->update_order_products($order_id, $product_id, $product_data);
        }
        else
        {
            $product_available_serials_count = $this->products_model->count_product_available_quantity($product_id, $country_id);
            $min_stock = $this->settings->min_product_stock;
            
            $available_count = $product_available_serials_count - $min_stock;
            
            
            if($new_qty <= $available_count)
            {
                
                $product_serials = $this->orders_model->generate_product_serials($product_id, $new_qty, $country_id);
                
                foreach($product_serials as $serial)
                {
                    $serials_data = array(
                                            'order_id'          => $order_id,
                                            'product_id'        => $product_id,
                                            'product_serial_id' => $serial->id,
                                            'unix_time'         => time()
                                         );
                                         
                    $serial_status['serial_status'] = 1;
                    $this->orders_model->update_serial_status($serial_status, $serial->id);                                     
                    $this->orders_model->insert_product_serials($serials_data);
                }
                
                
                $product_data['qty'] = $qty;
                $this->admin_order_model->update_order_products($order_id, $product_id, $product_data);
                
                $order_data = $this->orders_model->get_order_data($order_id);
                if($order_data->coupon_discount != 0)
                {
                    $this->orders->reset_order_coupon($order_id);
                }
                $this->orders->update_order_total_prices($order_id);
            }
            else
            {
                $this->session->set_flashdata('qty_error', lang('not_enough_product_quantity'));
            }
        }
        
        redirect('orders/admin_order/view_order/'.$order_id, 'refresh');
        
    }    

    public function remove_product()
    {
        $product_id = $this->input->post('product_id');
        $order_id   = $this->input->post('order_id');
        $country_id = $this->input->post('country_id');
        
        $check_product_in_order = $this->admin_order_model->check_product_in_order($product_id, $order_id);
        if($check_product_in_order)
        {
            $order_data       = $this->orders_model->get_order_data($order_id);
            $product_serials  = $this->admin_order_model->get_order_product_serials($product_id, $order_id);
            $serial_ids_array = array();
            
            if(count($product_serials) != 0)
            {
                foreach($product_serials as $serial)
                {
                    $serial_data['serial_status'] = 0;
                    $this->admin_order_model->update_serial_data($serial->product_serial_id, $serial_data);
                    
                    $serial_ids_array[] = $serial->product_serial_id;
                }
            }
            
            if(count($serial_ids_array) != 0)
            {
                $this->admin_order_model->delete_order_product_serials($serial_ids_array);
            }
            $this->admin_order_model->delete_order_product($product_id, $order_id);
            
            if($order_data->coupon_discount != 0)
            {
                $this->orders->reset_order_coupon($order_id);
            }
            
            $new_total = $this->orders->update_order_total_prices($order_id);
            
            echo $new_total;
        }
        
    }
     
     
     public function replace_invalid_serial()
     {
        $new_serial_row = '';
        $success        = 0;
        $output         = array();
        
        $serial_id             = $this->input->post('serial_id');
        $order_id              = $this->input->post('order_id');
        $product_id            = $this->input->post('product_id');
        $price                 = $this->input->post('price');
        $pocket_invalid_option = $this->input->post('pocket_invalid_option');
        
        $lang_id    = $this->data['active_language']->id;
        
        $order_data = $this->orders_model->get_order_details($order_id, $lang_id);
        
        if(isset($_POST['status_id']))
        {
            $status_id = $this->input->post('status_id');
        }
        else
        {
            $status_id = 0;
        }
        
        if($order_data)
        {
            
            if($pocket_invalid_option == 1)
            {
                 // return serial and balance
                $this->return_serial_and_balance($product_id, $order_id, $serial_id, $order_data);
                
                $msg     = lang('serial_recovered_successfully');
                $success = 1;
                
                $output  = array($success, $msg, $serial_id);
            }
            elseif($pocket_invalid_option == 2)
            {
                // invalid serial
                $output = $this->mark_serial_as_invalid($status_id, $order_id, $product_id, $serial_id, $price, $order_data);
            }
            else if($pocket_invalid_option == 3)
            {
                
                //return balance and invalid serial
                $this->return_serial_and_balance($product_id, $order_id, $serial_id, $order_data, $status_id);
                $msg     = lang('balance_returned_with_invalid_serial');
                $success = 1;
                
                $output  = array($success, $msg, $serial_id);
            }
            
        }
        else
        {
            $msg = lang('no_data_about_this_order');
            $success = 0;
            $output  = array($success, $msg, $serial_id);
            
        }
        
        
        //echo json_encode(array($success, $msg, $new_serial_row, $product_id, $serial_id));
        echo json_encode($output);
     }
     
     public function return_serial_and_balance($product_id, $order_id, $serial_id, $order_data, $status_id=0)
     {
        $order_total        = $order_data->final_total;
                
        $order_product_data = $this->orders_model->get_order_product_data($product_id, $order_id);
         
        // status_id = 1 => mark as invalid serial
        if($status_id == 0)
        {
            $serial_data['serial_status'] = 0;
            $this->admin_order_model->delete_order_product_serials($serial_id);
        }
        else
        {
            $serial_data = array(
                                    'invalid'           => 1,
                                    'serial_status'     => 3,
                                    'invalid_status_id' => $status_id
                                );
        }
                
        $this->admin_order_model->update_serial_data($serial_id, $serial_data);
        
        
        $order_product_new_qty = $order_product_data->qty - 1;
        
        if($order_product_new_qty == 0)
        {
            $this->admin_order_model->delete_order_product($product_id, $order_id);
        }
        else
        {
            $product_qty_data['qty'] = $order_product_new_qty;
            $this->orders_model->update_order_product($order_id, $product_id, $product_qty_data);
        }
        
        if($order_data->coupon_discount != 0)
        {
            $this->orders->reset_order_coupon($order_id);
        }
        
        $this->orders->update_order_total_prices($order_id);
        
        $order_new_data  = $this->orders_model->get_order_data($order_id);
        $order_new_total = $order_new_data->final_total;
        
        // return reward points  
        // update order products purchased cost 
        $product_reward_points       = $order_product_data->reward_points / $order_product_data->qty;
        $order_product_reward_points = $order_product_data->reward_points - ($order_product_data->reward_points / $order_product_data->qty);
        
        $product_purchased_cost      = $order_product_data->purchased_cost / $order_product_data->qty;
        $new_purchased_cost          = $order_product_data->purchased_cost - $product_purchased_cost;
        
        //$product_reward_points_data['reward_points'] = $order_product_reward_points;
        $order_product_updated_data = array(
                                                'reward_points'  => $order_product_reward_points,
                                                'purchased_cost' => $new_purchased_cost
                                           );
        
        $this->orders_model->update_order_product($order_id, $product_id, $order_product_updated_data);
        
        if($product_reward_points != 0)
        {
            $user_reward_points = $this->admin_bootstrap->get_user_reward_points($order_data->user_id);
            $user_new_points    = $user_reward_points - $product_reward_points;
            
            $this->admin_bootstrap->encrypt_and_update_users_data($order_data->user_id, 'user_points', $user_new_points);
        } 
        
        // return balance
        $user_data           = $this->admin_bootstrap->get_user_by_id($order_data->user_id);
        $user_return_balance = $order_total - $order_new_total;
        
        //convert balance to country curreny
        $user_return_balance = $this->currency->convert_amount_from_country_to_country($order_data->country_id, $user_data->store_country_id, $user_return_balance);
        
        $user_old_balance    = $this->admin_bootstrap->get_user_balance($order_data->user_id);
        $user_new_balance    = $user_old_balance + $user_return_balance;
        
        $this->admin_bootstrap->encrypt_and_update_users_data($order_data->user_id, 'user_balance', $user_new_balance);
        
        // insert balance log
        
        $balance_log_data = array(
                                    'user_id'           => $order_data->user_id             ,
                                    'order_id'          => $order_id                        ,
                                    'payment_method_id' => $order_data->payment_method_id   ,
                                    'balance'           => $user_new_balance                ,
                                    'amount'            => $order_data->final_total         ,
                                    'currency_symbol'   => $order_data->currency_symbol     ,
                                    'store_country_id'  => $order_data->country_id          ,
                                    'balance_status_id' => 6 , // order cancelled
                                    'ip_address'        => $this->input->ip_address()       ,
                                    'unix_time'         => time()
                                 );
    
        $this->user_balance_model->insert_balance_log($balance_log_data);
        
        return true;
     }
     
     public function mark_serial_as_invalid($status_id, $order_id, $product_id, $serial_id, $price, $order_data)
     {
                
        //count available serials 
        $available_serials_count = $this->products_model->count_product_available_quantity($product_id, $order_data->country_id);
    
        if($available_serials_count > 1)
        {
            // mark serial as invalid
            $serial_data = array(
                                    'invalid'           => 1,
                                    'serial_status'     => 3,
                                    'invalid_status_id' => $status_id
                                );
            
            $this->products_serials_model->update_serial($serial_id, $serial_data);
            
            //insert new serial
            $new_serial_data_result = $this->orders_model->generate_product_serials($product_id, 1, $order_data->country_id);
                
            if($new_serial_data_result)
            {
                $data = array(
                                'order_id'          => $order_id,
                                'product_id'        => $product_id,
                                'product_serial_id' => $new_serial_data_result[0]->id,  // return result array
                                'unix_time'         => time()
                             );
                
                $this->orders_model->insert_product_serials($data);
                
                //update serial status
                if($order_data->order_status_id == 1)
                {
                    $status = 2;
                }
                else 
                {
                    $status = 1;
                }
                
                $new_serial_data['serial_status'] = $status;
                $this->products_serials_model->update_serial($new_serial_data_result[0]->id, $new_serial_data);
                
                $msg     = lang('serial_replaced_successfully');
                $success = 1;
                
                $secret_key     = $this->config->item('new_encryption_key');
                $secret_iv      = md5('serial_iv');
                
                $dec_serials    = $this->encryption->decrypt($new_serial_data_result[0]->serial, $secret_key, $secret_iv);
                
                $new_serial_row = '<tr style="text-align: center;">
                                     <td>'.$price.$order_data->currency_symbol.'</td>
                                     <td>'.$dec_serials.'</td>
                                     <td>
                                        <button type="button" class="btn yellow-crusta invalid_serial serial_'.$new_serial_data_result[0]->id.'" value="'.$new_serial_data_result[0]->id.'" name="serial_id" data-serial_id="'.$new_serial_data_result[0]->id.'" data-order_id="'.$order_id.'" data-product_id="'.$product_id.'" data-price="'.$price.'">'.lang('invalid_serial').'</button>
                                        <span style="display: block; font-size: 12px; font-family: tahoma;" class="msg_span serial_'.$new_serial_data_result[0]->id.'">'.lang('invalid_serial_will_be_replaced').'</span>
                                        <div><select style="display: none;" name="pocket_invalid_options" id="pocket_invalid_options'.$new_serial_data_result[0]->id.'" class="pocket_invalid_options" data-serial_id="'.$new_serial_data_result[0]->id.'" data-order_id="'.$order_id.'" data-product_id="'.$product_id.'" data-price="'.$price.'"></select></div>
                                        <div><select style="display: none;" class="invalid_options" id="invalid_options'.$new_serial_data_result[0]->id.'" data-serial_id="'.$new_serial_data_result[0]->id.'" data-order_id="'.$order_id.'" data-product_id="'.$product_id.'" data-price="'.$price.'"><option valie="">-----</option></select></div>
                                     </td>                                        
                                   </tr>';
                                
                
                                
                // update product available quantity
                $product_available_quantity = $this->products_serials_model->get_product_country_available_serials($product_id, $order_data->country_id);
                $updated_data['product_quantity'] = $product_available_quantity;
                $this->products_model->update_product_countries($product_id, $order_data->country_id, $updated_data);
                
                
                //send notification
                $secret_key    = $this->config->item('new_encryption_key');
                $secret_iv     = md5('serial_iv');
                
                $dec_invalid_serial = $this->products_serials_model->get_products_serials_row($serial_id)->serial;
                $invalid_serial     = $this->encryption->decrypt($dec_invalid_serial, $secret_key, $secret_iv);
                $new_serial         = $this->encryption->decrypt($new_serial_data_result[0]->serial, $secret_key, $secret_iv);
                $product_name       = $this->products_model->get_product_translation($product_id)->title;
                
                $username = $order_data->first_name .' '. $order_data->last_name;
                $emails[] = $order_data->email;
                $phone    = $order_data->phone;
                
                $replace_time = time();
                
                $template_data = array(
                                        'logo_path'      => base_url().'assets/template/site/img/logo.png',
                                        'username'       => $username, 
                                        'invalid_serial' => $invalid_serial,
                                        'new_serial'     => $new_serial,
                                        'product_name'   => $product_name,
                                        'order_id'       => $order_id,
                                        'replace_time'   => date('Y/m/d H:i', $replace_time)
                                      );
                                      
                if($this->notifications->create_notification('replace_invalid_serial', $template_data, $emails, $phone))
                {
                    $this->session->set_flashdata('success',lang('success'));
                }
                
                
                
            }
            else
            {
                $msg = lang('no_available_serials_to_replace_with');
            }
            
        }
        else
        {
            //return msg that serial is not marked as invalid
            $msg = lang('no_available_serials_to_replace_with');
        }
        
        $output = array($success, $msg, $new_serial_row, $product_id, $serial_id);
        
        return $output;
     }
     
     public function insert_maintenance_cost()
     {
        $cost = $this->input->post('main_cost', true);
        $main_order_id = intval($this->input->post('order_id', true));
        
        $order_status_id = 9;
        $main_order_data = $this->orders_model->get_order_main_details($main_order_id, $this->data['lang_id']);
        
        //create maintenance order
        // new order must be created to get a new order id for a new payment , if user used a payment gateway
        
        $new_order_data = array(
                                 'user_id'            => $main_order_data->user_id              ,
                                'store_id'            => $main_order_data->store_id             ,
                                'agent'               => $main_order_data->agent                ,
                                'payment_method_id'   => $main_order_data->payment_method_id    ,
                                'bank_id'             => $main_order_data->bank_id              ,
                                'bank_account_name'   => $main_order_data->bank_account_name    ,
                                'bank_account_number' => $main_order_data->bank_account_number  ,
                                'voucher'             => $main_order_data->voucher              ,
                                'order_status_id'     => $order_status_id                       ,
                                'currency_symbol'     => $main_order_data->currency_symbol      ,
                                'country_id'          => $main_order_data->country_id           ,
                                'maintenance_cost'    => $cost                                  ,
                                
                                /**related to each order**/
                                'items_count'         => 1          ,
                                'total'               => 0          ,
                                'discount'            => 0          ,
                                'coupon_discount'     => 0          ,
                                'tax'                 => 0          ,
                                'shipping_cost'       => 0          ,
                                'final_total'         => $cost      ,
                                'wrapping_cost'       => 0          ,
                                'wrapping_only_cost'  => 0          ,
                                'ribbon_only_cost'  => 0            ,
                                'wrapping_only_cost'  => 0          ,
                                /*****/
                                
                                'auto_cancel'         => 0          ,
                                'needs_shipping'      => 0          ,
                                'shipping_company_id' => 0          ,
                                'shipping_country_id' => 0          ,
                                'shipping_city'       => 0          ,
                                'shipping_district'   => 0          ,
                                'shipping_address'    => 0          ,
                                'shipping_name'       => 0          ,
                                'shipping_phone'      => 0          ,
                                'shipping_type'       => 0          ,
                                'branch_id'           => 0          ,
                                'notes'               => ''         ,
                                'send_as_gift'        => ''         ,
                                'wrapping_id'         => 0          ,
                                'ribbon_id'           => 0          ,
                                'box_id'              => 0          ,
                                
                                'gift_msg'            => ''         ,
                                'unix_time'           => time()     ,
                                'day'                 => date('d')  ,
                                'month'               => date('m')  , 
                                'year'                => date('Y')  ,
                                'main_order_id'       => $main_order_id
                               );
        
        $this->orders_model->insert_order($new_order_data);
        
        /*//create notification
        $template_data = array(
                                'username'              => $main_order_data->first_name.' '.$main_order_data->last_name  ,
                                'user_email'            => $main_order_data->email              ,
                                'user_phone'            => $main_order_data->phone           ,
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
                              
        
        $this->notifications->create_notification('new_order_added', $template_data, $emails, $mobile_number);
        */
        $_SESSION['success'] = lang('success');
        $this->session->mark_as_flash('success');
       
        redirect('orders/admin_order/', 'refresh');
        
     }
     
     public function get_product_optional_fields()
     {
        $product_id = intval($this->input->post('product_id', true));
        $user_id    = intval($this->input->post('user_id', true));
        $op_array   = array();
        $html = '';
        
        $product_optional_fields = $this->products_model->get_product_optional_fields($product_id, $this->data['lang_id']);
        //echo '<pre>';print_r($product_optional_fields); die();
        if(count($product_optional_fields) != 0)
        {
            $options_array = array();
            $customer_group_id = $this->admin_bootstrap->get_user_by_id($user_id)->customer_group_id;
            
            foreach ($product_optional_fields as $field)
            {
                $html .= '<div class="row" style="margin: 5px;">
                            <label class="control-label col-md-3">'.$field->label.'</label>
                            <div class="col-md-3">';
                if($field->has_options == 1)
                {
                    $option_options = $this->optional_fields_model->get_optional_field_options($field->optional_field_id, $this->data['lang_id'], $product_id, 1);

                    $option_options_array = array();
                    foreach($option_options as $key=>$option)
                    {
                        $option_price = $option->cost;
                        $conds = array(
                                        'customer_group_id' => $customer_group_id,
                                        'option_id'         => $option->id,
                                        'optional_field_id' => $option->optional_field_id,
                                        'product_id'        => $product_id

                                      );
                        //get customer group cost
                        $customer_group_price = $this->products_model->get_table_data('optional_fields_customer_groups_prices', $conds, 'row');

                        if(count($customer_group_price) != 0)
                        {
                            if($customer_group_price->group_price != 0)
                            {
                                $option_price = $customer_group_price->group_price;
                            }
                        }

                        $option->{'cost'} = $option_price;
                        
                        if($field->field_type_id == 2) // radio
                        {
                            $html .= '<div class="radio-area-checkbox">
							 		  <label class="container-radio">
										    <input type="radio" name="optional_field['.$field->id.']" value="'. $option->id.'"  />
											  <span class="checkmark"></span>
                                               '.$option->field_value.'
                                      </label>
		                             </div>';
                        }
                        else if($field->field_type_id == 3) // check box
                        {
                            $html .= '<div class="area-checkbox2">
                                        <label class="checkbox">
                                            <input type="checkbox" class="op_cost op_c_'. $field->id.'"  name="optional_field['.$field->id.']['.$key.']" value="'.$option->id.'" />
                                        </label>
    									 <span class="name-radio">
    									     '.$option->field_value.'
    									 </span>
                                     </div>';
                        }
                        
                    }

                    $field->options = $option_options_array;
                }
                else
                {
                    $html .= '<input name="qty" type="number" min="1" value="1" class="form-control">';
                                       
                }
                
                $html .= '</div></div>';

                //$options_array[] = $field;
            }

            $op_array = $options_array;
        }
        
        echo $html;
        
        
     }
    
     
    
/************************************************************************/    
}