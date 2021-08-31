<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Admin_order extends CI_Controller
{
    public $lang_row;
    public $settings;
    public $status = 1;
    public $driver_id;

    public $stores;
    public $stores_ids;

    public function __construct()
    {
        parent::__construct();

        require(APPPATH . 'includes/global_vars.php');

        $this->load->library('encryption');
        $this->load->library('notifications');
        //$this->load->library('admin_products_lib');
        $this->load->library('products_lib');

        $this->load->library('orders');
        $this->load->library('shopping_cart');
        $this->load->library('payment_gateways/payfort');
        $this->load->library('payment_gateways/paypal');
        $this->load->library('payment_gateways/cashu');

        $this->config->load('encryption_keys');

        $this->load->model('orders_model');
        $this->load->model('admin_order_model');
        $this->load->model('order_status_model');
        $this->load->model('users/user_model');
        $this->load->model('users/users_model');
        $this->load->model('users/countries_model');
        $this->load->model('users/customer_groups_model');
        $this->load->model('notifications/templates_model');
        $this->load->model('affiliate/affiliate_log_model');
        $this->load->model('affiliate/admin_affiliate_model');
        $this->load->model('coupon_codes/coupon_codes_model');
        $this->load->model('products/invalid_serials_model');
        $this->load->model('products/products_serials_model');
        $this->load->model('payment_options/user_balance_model');
        $this->load->model('payment_options/bank_accounts_model');
        $this->load->model('payment_options/payment_methods_model');
        $this->load->model('optional_fields/optional_fields_model');
        $this->load->model('shopping_cart/user_bank_accounts_model');
        $this->load->model('wrapping/admin_wrapping_model');
        $this->load->model('shipping/Companies_model');

        $this->lang_row = $this->admin_bootstrap->get_active_language_row();
        $this->settings = $this->global_model->get_config();


        $store_id_array = array();

        if($this->config->item('business_type') == 'b2b')
        {
          $this->stores   = $this->admin_bootstrap->get_user_available_stores();
          foreach($this->stores as $store)
          {
              $store_id_array[] = $store->store_id;
          }
        }

        $this->stores_ids = $store_id_array;

        $this->view_folder = 'Admin';

        if($this->data['store_owner'] == 1)
        {
            $this->view_folder = 'Sell';
        }


        $is_driver = $this->admin_bootstrap->check_if_driver();
        $this->driver_id = 0;
        $this->is_driver = $is_driver;
        if($is_driver)
        {
          $this->driver_id = $this->data['user_id'];
        }

    }

    public function index()
    {
        $lang_id = $this->data['active_language']->id;

        $this->data['count_all_records'] = $this->orders_model->get_count_all_orders($lang_id, $this->stores_ids,'', 0, 0, 0, 0, 0, $this->driver_id);

        $this->data['data_language']     = $this->lang_model->get_active_data_languages();


        $this->data['columns']           = array(
                                                     lang('order_number')           ,
													 lang('order_details')          ,
                                                     lang('username')               ,
                                                     lang('transaction_id')         ,
                                                     lang('name_of_store')          ,
                                                     lang('shipping_way')           ,
                                                     //lang('country')                ,
                                                     lang('user_previous_orders')   ,
                                                     lang('agent')                  ,
                                                     lang('status')                 ,
                                                     //lang('purchased_products')     ,
                                                     //lang('payment_method')         ,
                                                     lang('total')                  ,
                                                     lang('date')                   ,

                                                     lang('delete')
                                                );

        $this->data['orders']            = array(
                                                     lang('date'),
                                                     lang('username'),
                                                     lang('final_total'),
                                                 );


        $this->data['filters']           = array(
                                                   /*array(
                                                          'filter_title' => lang('users_filter'),
                                                          'filter_name'  => 'username_filter',
                                                          'filter_data'  => $this->user_model->get_active_users_data()

                                                         ) ,
                                                   */
                                                   array(
                                                          'filter_title' => lang('countries_filter'),
                                                          'filter_name'  => 'countries_filters',
                                                          'filter_data'  => $this->countries_model->get_countries($lang_id)
                                                         ),
                                                    array(
                                                          'filter_title' => lang('status_filter'),
                                                          'filter_name'  => 'status_filter',
                                                          'filter_data'  => $this->order_status_model->get_all_statuses($lang_id)
                                                         ),
                                                    array(
                                                          'filter_title' => lang('payment_methods_filter'),
                                                          'filter_name'  => 'payment_methods_filter',
                                                          'filter_data'  => $this->admin_order_model->get_payment_methods_translation($lang_id)
                                                         ),
                                                    array(
                                                          'filter_title' => lang('shipping_way'),
                                                          'filter_name'  => 'shipping_way_filter',
                                                          'filter_data'  => $this->get_available_shipping_methods($lang_id)
                                                        ),
                                                    array(
                                                          'filter_title' => lang('name_of_store'),
                                                          'filter_name'  => 'store_id',
                                                          'filter_data'  => $this->stores
                                                        )
                                                 );


        //$this->data['actions']           = array( 'delete'=>lang('delete'));
        $this->data['search_fields_data']   = array( lang('order_number'), lang('total'), lang('username'), lang('phone'), lang('transaction_id'));
        $this->data['unset_actions']        = true;
        $this->data['index_method_id']      = $this->data['method_id'];

        $this->data['content'] = $this->load->view($this->view_folder.'/grid/grid_html', $this->data, true);
        $this->load->view($this->view_folder.'/main_frame',$this->data);
    }

    public function ajax_list()
    {

        /**************************************/
        $store_id_array = array();

        if($this->config->item('business_type') == 'b2b'){
          $this->stores   = $this->admin_bootstrap->get_user_available_stores($_POST['index_method_id']);
          foreach($this->stores as $store)
          {
              $store_id_array[] = $store->store_id;
          }
        }

        $this->stores_ids = $store_id_array;
        /**************************************/

        if(isset($_POST['lang_id']))
        {
            $lang_id = intval($this->input->post('lang_id'));
        }else{
            $lang_id = $this->data['active_language']->id;
        }
        if(isset($_POST['limit']))
        {
            $limit = intval($this->input->post('limit'));
        }else{
            $limit = 1;
        }

        if(isset($_POST['page_number']))
        {
            $active_page = intval($this->input->post('page_number'));
        }else{
            $active_page = 1;
        }

        $offset  = ($active_page-1) * $limit;



        if(isset($_POST['search_word']) || trim($_POST['search_word']) == '')
        {
            $search_word = $this->input->post('search_word');
        }
        else
        {
            $search_word = '';
        }

        if(isset($_POST['order_by']))
        {
            $order_by = $this->input->post('order_by');
        }
        else
        {
            $order_by = '';
        }

        if(isset($_POST['order_state']))
        {
            $order_state = $this->input->post('order_state');
        }
        else
        {
            $order_state = 'desc';
        }

        if(isset($_POST['filter'])&& isset($_POST['filter_data']))
        {
            $filters      = $this->input->post('filter');
            $filters_data = $this->input->post('filter_data');

            //$username_filter_id  = intval($filters_data[0]);
            $username_filter_id  = 0;
            $countries_filter_id = intval($filters_data[0]);
            $status_filter_id    = intval($filters_data[1]);
            $payment_filter_id   = intval($filters_data[2]);
            $shipping_way_filter = intval($filters_data[3]);
            $stores_filter_id    = intval($filters_data[4]);
        }
        else
        {
            $username_filter_id  = 0;
            $countries_filter_id = 0;
            $status_filter_id    = 0;
            $payment_filter_id   = 0;
            $shipping_way_filter = 0;
            $stores_filter_id    = 0;
        }

        if(isset($_POST['search_field_id']))
        {
            $search_field_id = $this->input->post('search_field_id');
        }
        else
        {
            $search_field_id = 0;
        }

        $grid_data  = $this->orders_model->get_orders_data($lang_id, $limit, $offset,$search_word,
         $order_by, $order_state, $username_filter_id, $countries_filter_id, $status_filter_id,
          $payment_filter_id, $search_field_id, $shipping_way_filter, $stores_filter_id, $this->stores_ids, $this->driver_id);

        $db_columns = array(
                             'id'               ,
							 'order_details'    ,
                             'username'         ,
                             'cart_id'          ,
                             'store_name'       ,
                             'shipping_type'    ,
                             //'country'          ,
                             'previous_orders'  ,
                             'agent'            ,
                             'status'           ,
                             //'products'         ,
                             //'payment_method'   ,
                             'final_total'      ,
                             'unix_time'        ,

                             'delete'
                           );

        $this->data['hidden_fields'] = array();

        $new_grid_data = array();

        foreach($grid_data as $key =>$row)
        {
            foreach($db_columns as $column)
            {
				if($column == 'order_details')
                {
                    $details = '<a class="btn btn-sm blue table-group-action-submit" href="'.base_url().'orders/admin_order/view_order/'.$row->id.'">'.lang('order_details').'</a>';
                    if($row->order_status_id == 4)
                    {
                        $details = '';
                    }
                    $new_grid_data[$key][$column] = $details;
                }
                else if($column == 'shipping_type')
                {
                    $shipping_type = $this->orders_model->get_shipping_type($row->shipping_type, $lang_id);

                     $new_grid_data[$key][$column] = $shipping_type;
                }
               else if($column == 'country')
                {
                    $country = $this->countries_model->get_country_name($row->country_id, $lang_id);
                    if($row->order_status_id == 4)
                    {
                        $country = '<span style="color: #B79999;">'.$country.'</span>';
                    }


                    $new_grid_data[$key][$column] = $country;
                }
                else if($column == 'previous_orders')
                {
                    $user_previous_count = $this->orders_model->get_user_previous_orders_count($row->user_id, $row->id);

                    if($row->order_status_id == 4)
                    {
                        $user_previous_count = '<span style="color: #B79999;">'.$user_previous_count.'</span>';
                    }

                    $new_grid_data[$key][$column] = $user_previous_count;
                }
                else if($column == 'agent')
                {
                    $new_grid_data[$key][$column] = substr($row->agent, 0, 20);
                }
                else if($column == 'products')
                {
                    $products_names     = '';
                    $products_data      = $this->orders_model->get_order_products($row->id, $lang_id);
                    $order_charge_cards = $this->orders_model->get_recharge_card($row->id);

                    foreach($products_data as $item)
                    {
                        $products_names .= $item->qty." X ".$item->title." <br> ";
                    }
                    foreach($order_charge_cards as $card)
                    {
                        $products_names .= lang('recharge_card')." X ".$card->price;
                    }

                    if($row->order_status_id == 4)
                    {
                        $products_names = '<span style="color: #B79999;">'.$products_names.'</span>';
                    }
                    $new_grid_data[$key][$column] = $products_names;

                }

                else if($column == 'payment_method')
                {
                    $payment_method_data = $this->payment_methods_model->get_row_data($row->payment_method_id, $lang_id);

                    $payment_method = $payment_method_data->name;

                    if($row->payment_method_id == 3)
                    {
                        $bank_data = $this->bank_accounts_model->get_row_data($row->bank_id, $lang_id);
                        $payment_method .= ': '.$bank_data->bank;
                    }

                    if($row->order_status_id == 4)
                    {
                        $payment_method = '<span style="color: #B79999;">'.$payment_method.'</span>';
                    }

                    $new_grid_data[$key][$column] = $payment_method ." <img height='20' src='".base_url()."assets/uploads/".$payment_method_data->image."'>";
                }
                else if($column == 'final_total')
                {
                    $final_total = $row->final_total." ".$row->currency_symbol;
                    if($row->order_status_id == 4)
                    {
                        $final_total = '<span style="color: #B79999;">'.$final_total.'</span>';
                    }
                    $new_grid_data[$key][$column] = $final_total ;
                }
                else if($column == 'unix_time')
                {
                    $unix_time = date('Y/m/d H:i', $row->unix_time);
                    if($row->order_status_id == 4)
                    {
                        $unix_time = '<span style="color: #B79999;">'.$unix_time.'</span>';
                    }
                    $new_grid_data[$key][$column] = $unix_time;
                }

                elseif($column == 'delete')
                {
                    if($row->order_status_id == 2 || $row->order_status_id == 8)
                    {
                        $delete = //'<button class="btn btn-sm red table-group-action-submit delete-btn" value="'.$row->id.' >'.lang('delete').'</button>';
                        '<button value="'.$row->id.'" class="btn btn-sm red table-group-action-submit delete-btn" href="'.base_url().'orders/admin_order/delete_order/'.$row->id.'">'.lang('delete').'</button>';
                    }
                    else
                    {
                        $delete = '';
                    }
                    if($row->order_status_id == 4)
                    {
                        $delete = '<span style="color: red;">'. lang('deleted_order').'</span>';
                    }

                    $new_grid_data[$key][$column] = $delete;
                }
                elseif($column == 'username')
                {
                    $new_grid_data[$key][$column] = $row->first_name. ' ' . $row->last_name;;
                }
                else
                {
                    $field = $row->{$column};
                    //echo $row->{$column};
                    if($row->order_status_id == 4 )
                    {
                        if($column == 'username')
                        {
                            $field = '<span style="color: #B79999;">'.$row->first_name . ' ' . $row->last_name . '</span>';
                        }
                        elseif($column == 'status')
                        {
                            $field = '<span style="color: #B79999;">'.$row->status.'</span>';
                        }
                    }
                    $new_grid_data[$key][$column] = $field;
                }
            }
        }

        $this->data['grid_data']          = $new_grid_data;

        $this->data['count_all_records']  = $this->orders_model->get_count_all_orders($lang_id, $this->stores_ids,$search_word, $search_field_id, $username_filter_id, $countries_filter_id, $status_filter_id, $payment_filter_id,  $this->driver_id);

        $this->data['display_lang_id']    = $lang_id;

        $this->data['unset_view']   = true;
        $this->data['unset_edit']   = true;
        $this->data['unset_delete'] = true;


        $count_data  = $this->data['count_all_records'];
        $output_data = $this->load->view($this->view_folder.'/grid/grid_data', $this->data, true);

        echo json_encode(array($output_data, $count_data, $search_word));
     }

     public function view_order($order_id)
     {

        $this->data['form_action'] = $this->data['module'] . "/" . $this->data['controller'] . "/update";
        $order_id = intval($order_id);
        $lang_id  = $this->data['active_language']->id;
        $this->data['is_driver'] = $this->is_driver;

        if($order_id)
        {
            $display_lang_id = $this->data['active_language']->id;
            $order_details   = $this->orders_model->get_order_details($order_id, $display_lang_id);


            // check driver primissions
            $allowed_order = true;
            if($this->driver_id != 0)
            {
              if($order_details->driver_id != $this->data['user_id'])
              {
                $allowed_order = false;
              }
            }

            if($order_details && $allowed_order)
            {
                if(in_array($order_details->store_id, $this->stores_ids) || count($this->stores_ids) == 0)
                {

                    $cards_array       = array();
                    $product_new_array = array();
                    $charge_card       = false;
                    $edit_order        = false;

                    if(($order_details->order_status_id == 2 || $order_details->order_status_id == 8) && $order_details->payment_method_id != 1 && $order_details->payment_method_id != 2)
                    {
                        $edit_order = true;
                    }

                    $charge_card_count = $this->orders_model->get_recharge_cards_count($order_id);

                    if($charge_card_count > 0)
                    {
                        $charge_card = true;
                        $cards_array = $this->orders_model->get_recharge_card($order_id, $this->data['lang_id']);
                    }

                    $order_products               = $this->orders_model->get_order_products($order_id, $display_lang_id);
                    $order_log                    = $this->orders_model->get_orders_log($order_id, $display_lang_id);
                    $wholesaler_customer_group_id = $this->config->item('wholesaler_customer_group_id');

                    $payment_method = $this->payment_methods_model->get_row_data($order_details->payment_method_id, $display_lang_id);//get_payment_method_name($order_details->payment_method_id, $display_lang_id);

                    if($order_details->payment_method_id == 3)
                    {
                        $bank_data               = $this->payment_methods_model->get_bank_data($order_details->bank_id, $display_lang_id);
                        $this->data['bank_data'] = $bank_data;
                    }

                    if ($this->ion_auth->in_group($wholesaler_customer_group_id))
                    {
                        $wholesaler_options = true;
                    }
                    else
                    {
                        $wholesaler_options = false;
                    }

                    $main_cat_id = $this->settings->maintenance_cat_id;
                    $products_with_serials = array();
                    $order_products_ids    = array();
                    $maintenance_products  = array();

                    foreach($order_products as $product)
                    {
                        if($product->product_id != 0)
                        {
                            $serials_array  = array();
                            if($product->quantity_per_serial == 1)
                            {
                                $orders_serials = $this->orders_model->get_admin_product_serials($product->product_id, $product->order_id, $product->order_product_id);

                                foreach($orders_serials as $serial)
                                {

                                    $secret_key    = $this->config->item('new_encryption_key');
                                    $secret_iv     = md5('serial_iv');

                                    $dec_serials   = $this->encryption->decrypt($serial->serial, $secret_key, $secret_iv);

                                    $serial->{'dec_serial'}  = $dec_serials;
                                    $serials_array[] = $serial;
                                }

                                $product->{'serials'} = $serials_array;
                            }
                            else
                            {
                                $product->{'non_serials_product'} = 1;
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

                                    if($field->field_type_id == 9)
                                    {
                                        $field->product_optional_field_value = '<a href="'.base_url().'orders/admin_order/download/'.$field->product_optional_field_value.'">'.$field->product_optional_field_value.'</a>';
                                    }

                                    $product->user_optional_fields = $user_product_optional_fields;
                                }
                            }


                            $products_with_serials[] = $product;
                            $order_products_ids[]    = $product->product_id;
                        }

                    }

                    $check_maintenance_products_exist = $this->orders_model->check_maintenance_products_exist($order_id, $main_cat_id);
                    if($check_maintenance_products_exist)
                    {
                        $this->data['maintenance_product'] = true;
                    }

                    $status         = $this->order_status_model->get_all_statuses($display_lang_id);
                    $status_options = array();

                    foreach($status as $row)
                    {
                        $status_options[$row->id] = $row->name;
                    }

                    $secret_key   = $this->config->item('new_encryption_key');
                    $secret_iv    = $order_details->user_id;

                    if($order_details->user_balance == '')
                    {
                        $user_balance = 0;
                    }
                    else
                    {
                        $user_balance = $this->encryption->decrypt($order_details->user_balance, $secret_key, $secret_iv);
                    }

                    if($order_details->user_balance == '')
                    {
                        $user_points = 0;
                    }
                    else
                    {
                        $user_points = $this->encryption->decrypt($order_details->user_points, $secret_key, $secret_iv);
                    }

                    $customer_group_name = $this->customer_groups_model->get_customer_group_translation($order_details->customer_group_id, $display_lang_id);
                    $country             = $this->countries_model->get_country_name($order_details->country_id, $display_lang_id);
                    $user_previous_count = $this->orders_model->get_user_previous_orders_count($order_details->user_id, $order_id);



                    $order_details->{'user_balance'}         = $user_balance;
                    $order_details->{'user_points'}          = $user_points;
                    $order_details->{'user_customer_group'}  = $customer_group_name;
                    $order_details->{'country'}              = $country;
                    $order_details->{'user_previous_orders'} = $user_previous_count;

                    //Cancel Order Auto_cancel
                    //$settings         = $this->global_model->get_config();
                    $auto_cancel_time = $this->settings->min_order_hours * 60 * 60;
                    $allowed_time     = time() - $auto_cancel_time;
                    $order_time       = $order_details->unix_time;

                    $allow_cancel_auto_cancel = 'true';

                    if($order_time < $allowed_time)
                    {
                        $allow_cancel_auto_cancel = 'false';
                    }

                    $sms_templates = $this->templates_model->get_user_templates($lang_id);

                    $country_other_products = $this->products_model->get_country_other_products($order_products_ids, $order_details->country_id, $lang_id, $order_details->store_id);

                    if(count($country_other_products) != 0)
                    {
                        foreach($country_other_products as $product)
                        {
                            $product_new_array[$product->product_id] = $product->title;
                        }
                    }

                    $orders_log_array = array();

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
                        elseif($log->status_id == 4)
                        {
                            $class = 'danger';
                        }
                        else
                        {
                            $class = 'info';
                        }

                        $log->{'class'} = $class;

                        $orders_log_array[] = $log;
                    }

                    $invalid_options = $this->invalid_serials_model->get_invalid_status($lang_id);
                    $invalid_options_select = '<option valie="">-----</option>';

                    foreach ($invalid_options as $option)
                    {
                        $invalid_options_select .= '<option value='.$option->status_id.'>'.$option->status.'</option>';
                    }

                    if($order_details->payment_method_id == 1)
                    {
                        $pocket_invalid_options = '<option>-----</option><option value="1">'.lang('recovery').'</option><option value="2">'.lang('compensation').'</option><option value="3">'.lang('return_with_invalid').'</option>';

                        $this->data['pocket_invalid_options'] = $pocket_invalid_options;
                    }

                    //admin edit order data

                    $user_edit_order_data = $this->orders_model->get_order_edit_data($order_id);

                    $user_previous_orders = $this->orders_model->get_user_previous_orders_data($order_details->user_id, $order_id, $lang_id);
                    $pre_orders_array = array();
                    $products_names = '';
                    foreach($user_previous_orders as $order)
                    {
                        $products_data      = $this->orders_model->get_order_products($order->id, $lang_id);
                        $order_charge_cards = $this->orders_model->get_recharge_card($order->id);
                        $products_names = '';
                        foreach($products_data as $item)
                        {
                            $products_names .= $item->qty." X ".$item->title." <br> ";
                        }
                        foreach($order_charge_cards as $card)
                        {
                            $products_names .= lang('recharge_card')." X ".$card->price;
                        }

                        $order->{'product_names'} = $products_names;

                        $pre_orders_array[]     = $order;
                    }

                    if($order_details->total_weight != 0)
                    {
                        $weight_in_kg = $order_details->total_weight / 1000;
                        $order_details->total_weight = $weight_in_kg;
                    }

                    /**
                     * shipping_types
                     *   1 => Delivery
                     *   2 => Recieve from shop

                    */

                    $shipping_type = '';

                    if($order_details->shipping_type == 1)
                    {
                        $shipping_type = lang('deliver_home');
                    }
                    elseif($order_details->shipping_type == 2)
                    {
                        $shipping_type = lang('recieve_from_shop');
                    }
                    else if($order_details->shipping_type == 3)
                    {
                        $shipping_type = lang('shipping');
                    }
                    else if($order_details->shipping_type == 4)
                    {
                        $shipping_type = lang('user_address'); // lang('address');
                    }

                    $order_details->{'shipping_type_lang'} = $shipping_type;

                    $order_notes = $this->orders_model->get_order_notes($order_id);

                    if($order_details->send_as_gift == 1)
                    {
                        $wrapping_data = $this->admin_wrapping_model->get_wrapping_data($order_details->wrapping_id, $this->data['lang_id']);
                        $this->data['wrapping_data'] = $wrapping_data;
                    }

                    //if($order_details->order_status_id == 1) // Old code => deal with order status "complete"
                    if($order_details->order_status_id == 12) // Show Request for tracking no. when order status is "Out for delivery" => Mrzok edit
                    {
                        if($order_details->delivered == 0)
                        {
                            if($order_details->shipping_company_id == 1)
                            {
                                //SMSA
                                $cities = $this->orders_model->get_shipping_cities('smsa');
                                foreach($cities as $city)
                                {
                                    $cities_array[$city->city] = $city->city;
                                }
                                $this->data['cities_list'] = $cities_array;
                            }
                            else if($order_details->shipping_company_id == 3)
                            {
                                //ARAMEX
                                $cities_aramex = $this->orders_model->get_shipping_cities('aramex');
                                foreach($cities_aramex as $city)
                                {
                                    $aramex_cities_array[$city->city] = $city->city;
                                }
                                $this->data['cities_list'] = $aramex_cities_array;
                            }
                        }
                        else
                        {
                            $tracking_array = array();
                            $tracking_data  = $this->orders_model->get_order_tracking_log_data($order_id, $this->data['lang_id']);

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

                            $this->data['tracking_data'] = $tracking_data;

                        }
                    }

                    $shipping_companies = $this->Companies_model->get_companies_data($lang_id, array('shipping_companies.active'=>1));

                    $drivers_group_id = $this->config->item('drivers_group_id');
                    $drivers = $this->user_model->get_group_users($drivers_group_id);

                    $drivers_array = array();
                    $shipping_companies_array = array();

                    foreach($drivers as $row)
                    {
                      $drivers_array[$row->id] = $row->first_name.' '.$row->last_name;
                    }

                    foreach($shipping_companies as $row)
                    {
                        $shipping_companies_array[$row->id]  = $row->name;
                    }

                    $get_orders_return  = $this->orders_model->get_orders_return($order_id);
                    $get_shipping_log  = $this->orders_model->get_order_tracking_log_data($order_id, $lang_id);

                    $this->data['order_details']          = $order_details;
                    $this->data['payment_method']         = $payment_method;
                    $this->data['order_products']         = $order_products;
                    $this->data['products_with_serials']  = $products_with_serials;
                    $this->data['wholesaler_options']     = $wholesaler_options;
                    $this->data['order_log']              = $orders_log_array;
                    $this->data['status_options']         = $status_options;
                    $this->data['charge_card']            = $charge_card;
                    $this->data['cards_data']             = $cards_array;
                    $this->data['order_auto_cancel']      = $allow_cancel_auto_cancel;
                    $this->data['sms_templates']          = $sms_templates;
                    $this->data['edit_order']             = false;//$edit_order;
                    $this->data['country_other_products'] = $product_new_array;
                    $this->data['invalid_options_select'] = $invalid_options_select;
                    $this->data['edit_order_data']        = $user_edit_order_data;
                    $this->data['pre_orders_array']       = $pre_orders_array;
                    $this->data['order_notes']            = $order_notes;
                    $this->data['drivers']                = $drivers_array;
                    $this->data['get_order_message']      = $get_orders_return;
                    $this->data['get_shipping_log']       = $get_shipping_log;
                    $this->data['shipping_compinies']     = $shipping_companies_array;

                }
                else
                {
                    $this->data['error_msg'] = lang('no_store_permission');
                }
            }
            else
            {
                $this->data['error_msg'] = lang('no_data_about_this_order');
            }

            if($this->view_folder == 'Sell')
            {
                $this->data['content'] = $this->load->view('Sell/order_details', $this->data, true);
            }
            else
            {
                $this->data['content'] = $this->load->view('admin_order_details', $this->data, true);
            }
            $this->load->view($this->view_folder.'/main_frame',$this->data);
        }
     }

     public function update_status()
     {
        $this->form_validation->set_rules('status_id', lang('status_id'), 'required');
        if($this->form_validation->run() == false)
        {

        }
        else
        {

            $lang_id            = $this->data['active_language']->id;
            $order_id           = $this->input->post('order_id');
            $status_id          = $this->input->post('status_id');
            $status_note        = $this->input->post('status_note');
            $auto_cancel        = (isset( $_POST['active']))? $this->input->post('active'):0;//$this->input->post('auto_cancel');
            $send_later         = (isset( $_POST['send_later']))? $this->input->post('send_later'):0;//$this->input->post('auto_cancel');
            $shipping_company   = strip_tags($this->input->post('shipping_Company', true));

            if($shipping_company == 0)
            {
                $shipping_company == '';
            }

            $order_data     = $this->orders_model->get_order_data($order_id);
            $order_products = $this->orders_model->get_order_all_products($order_id);

            $secret_key = $this->config->item('new_encryption_key');
            $secret_iv  = $order_data->user_id;

            $send_time = 0;

            if($send_later == 1)
            {
                $send_time = time()+60;
            }


            $updated_data = array(
                                    'order_status_id'       => $status_id   ,
                                    'auto_cancel'           => $auto_cancel ,
                                    'send_later'            => $send_later  ,
                                    'send_serials_time'     => $send_time   ,
                                    'status_note'           => $status_note ,
                                    'shipping_company_id'   => $shipping_company
                                 );

            $this->orders_model->update_order_status($order_id, $updated_data);

            //insert order edit log
            $edit_log_data = array(
                                    'user_id'   => $this->data['user_id'],
                                    'order_id'  => $order_id,
                                    'unix_time' => time()
                                   );

            $this->orders_model->insert_order_edit_log($edit_log_data);

            /*if($send_later == 1)
            {}
            else*/
            if($status_id == 1 && $send_later == 0 || $status_id == 10) // Aprroved
            {
                $this->_approve_order($order_id, $order_data, $order_products, $secret_key, $secret_iv, $status_id);
            }
            else if($status_id == 3 || $status_id == 4 || $status_id == 11)  // delete OR reject
            {
                $this->_canceled_orders_operations($order_id, $order_data->country_id, $status_id, $order_products);
            }
            else//if($status_id == 2 || $status_id == 9)  // Pending
            {
                $this->_insert_order_log($order_id, $status_id);
            }



            if($auto_cancel == 0 && $order_data->auto_cancel == 1)
            {
                // send notification
                $userdata = $this->users_model->get_user($order_data->user_id);
                $username = $userdata->first_name. ' ' . $userdata->last_name;
                $emails[] = $userdata->email;
                $phone    = $userdata->phone;
                $status   = $this->order_status_model->get_status_translation_name($status_id, $lang_id);

                $template_data = array(
                                        'unix_time' => time(),
                                        'username'  => $username,
                                        'status'    => $status,
                                        'order_id'  => $order_data->id,
                                        'order_time' => date('Y/m/d', $order_data->unix_time)
                                      );

                if($this->notifications->create_notification('cancel_auto_cancel_order', $template_data, $emails, $phone))
                {
                     $this->session->set_flashdata('success',lang('success'));
                }
                else
                {
                    $this->session->set_flashdata('notification_error',lang('sms_auto_cancel_order_not_sent'));
                }
            }

            $_SESSION['success'] = lang('success');
            $this->session->mark_as_flash('success');

            redirect('orders/admin_order/view_order/'.$order_data->id, 'refresh');
        }
     }

     public function do_action()
     {
        $action = $this->input->post('action');
        if($action == 'delete')
        {
            $this->delete();
        }
     }

     public function send_notification($order_id, $user_id, $lang_id)
     {
        // send notification
        $userdata = $this->users_model->get_user($user_id);
        $username = $userdata->first_name. ' ' . $user_data->last_name;
        $emails[] = $userdata->email;
        $phone    = $userdata->phone;
        $status   = $this->order_status_model->get_status_translation_name(3, $lang_id);

        $template_data = array(
                                'unix_time' => time(),
                                'username'  => $username,
                                'status'    => $status,
                                'order_id'  => $order_id
                              );

        if($this->notifications->create_notification('Insert_order', $template_data, $emails, $phone))
        {
            $this->session->set_flashdata('success',lang('success'));
        }
        else
        {
            $this->session->set_flashdata('notification_error',lang('sms_insert_order_not_sent'));
        }
     }

     public function stream()
     {
        /**************************************/
        $this->stores   = $this->admin_bootstrap->get_user_available_stores($_POST['index_method_id']);
        $store_id_array = array();

        foreach($this->stores as $store)
        {
            $store_id_array[] = $store->store_id;
        }

        $this->stores_ids = $store_id_array;
        /**************************************/

        $last_row_id  = $this->input->post('last_row_id');
        $lang_id      = $this->input->post('lang_id');
        $limit        = $this->input->post('limit');
        $page_number  = $this->input->post('page_number');

        if($page_number !=1)
        {
            $rows_count = $page_number * $limit;
            $previous_rows_count = $this->orders_model->count_previous_rows($last_row_id, array(), $this->stores_ids);

            $new_rows_count = $previous_rows_count % $rows_count;

            if($new_rows_count != 0)
            {
                $new_row_data = $this->orders_model->get_new_row_data($last_row_id, $lang_id, array(), $this->stores_ids);

            }
        }



        $rows_count = $page_number * $limit;
        $previous_rows_count = $this->orders_model->count_previous_rows($last_row_id, array(), $this->stores_ids);

        $new_rows_count = $previous_rows_count % $rows_count;

        if($new_rows_count != 0)
        {
            $new_row_data = $this->orders_model->get_new_row_data($last_row_id, $lang_id, array(), $this->stores_ids);
            if($new_row_data)
            {
                $user_previous_count = $this->orders_model->get_user_previous_orders_count($new_row_data->user_id, $new_row_data->id, $this->stores_ids);

                $products_names     = '';
                $products_data      = $this->orders_model->get_order_products($new_row_data->id, $lang_id);
                $order_charge_cards = $this->orders_model->get_recharge_card($new_row_data->id);

                foreach($products_data as $item)
                {
                    $products_names .= $item->qty." X ".$item->title." <br> ";
                }
                foreach($order_charge_cards as $card)
                {
                    $products_names .= lang('recharge_card')." X ".$card->price;
                }

                $payment_method = $this->payment_methods_model->get_payment_method_name($new_row_data->payment_method_id, $lang_id);

                $row = '<tr data-sort="" data-id="'.$new_row_data->id.' " class="sorting nodrag">
                             <td width="5%">
                        		<div class="checker"><span><input type="checkbox" class="group-checkable checkbox" name="row_id[]" value="'.$new_row_data->id.'"></span></div>
                        	</td>

                            <td style="text-align: center;"><a href="'.base_url().'orders/admin_order/view_order/'.$new_row_data->id.'">'.$new_row_data->id.'</a></td>
                            <td style="text-align: center;">
                      			<a class="btn btn-sm blue table-group-action-submit" href="'.base_url().'orders/admin_order/view_order/'.$new_row_data->id.'">'.lang('order_details').'</a>
                            </td>
        	                <td style="text-align: center;">'.$new_row_data->first_name.' '.$new_row_data->last_name.'</td>
                            <td style="text-align: center;">'.$new_row_data->cart_id.'</td>
                            <td style="text-align: center;">'.$new_row_data->store_name.'</td>
                            <td style="text-align: center;"></td>
                            <td style="text-align: center;">'.$user_previous_count.'</td>
                            <td style="text-align: center;">'.substr($new_row_data->agent, 0, 20).'</td>
                            <td style="text-align: center;">'.$new_row_data->status.'</td>


                            <td style="text-align: center;">'.$new_row_data->final_total.' '.$new_row_data->currency_symbol.'</td>
                            <td style="text-align: center;">'.date('Y/m/d H:i', $new_row_data->unix_time).'</td>

                            <td style="text-align: center;">
                      			<button value="'.$new_row_data->id.'" class="btn btn-sm red table-group-action-submit delete-btn" href="'.base_url().'orders/admin_order/delete_order/'.$new_row_data->id.'">'.lang('delete').'</button>
                            </td>

                        </tr>';

                echo $row;
            }
            else
            {
                echo '0';
            }
        }
        else
        {
            echo '0';
        }
     }

     public function send_sms()
     {
        $order_id = $this->input->post('order_id');
        if($order_id)
        {
            $phone_number = $this->input->post('phone');
            $type         = $this->input->post('type');
            $lang_id      = $this->data['active_language']->id;

            if($type == 1)
            {
                $template_id = $this->input->post('template_id');
                $template_data = $this->templates_model->get_row_data($template_id, $lang_id);
                $msg = $template_data->sms_template;
            }
            elseif($type == 2)
            {
                $msg = $this->input->post('msg_text');
            }

            if($msg != '')
            {
                $this->notifications->send_sms($msg, $phone_number);
                $this->session->set_flashdata('send_sms_successfully', lang('msg_sent_successfully'));
            }
            else
            {
                $this->session->set_flashdata('send_sms_error', lang('no_msg_to_send'));
            }

        }
        redirect('orders/admin_order/view_order/'.$order_id, 'refresh');
     }

     public function delete_order($order_id=0)
     {
        if($order_id == 0)
        {
            $order_id = $_POST['row_id'];
        }
        //update product_serials value
        $lang_id        = $this->data['active_language']->id;
        $order_data     = $this->orders_model->get_order_details($order_id, $lang_id);
        $order_products = $this->orders_model->get_order_products($order_id, $lang_id);
        $serials_data   = $this->orders_model->get_order_serials($order_id);
        $status_id      = 4;

        $this->_canceled_orders_operations($order_id, $order_data->country_id, $status_id, $order_products);

        $order_new_data['order_status_id'] = 4;
        $this->orders_model->update_order_data($order_id, $order_new_data);

        //redirect('orders/admin_order/', 'refresh');
        echo '1';
     }

     public function delete($order_id=0)
     {

        if($order_id == 0)
        {
            $order_id = $_POST['row_id'];
        }

        //update product_serials value
        $lang_id        = $this->data['active_language']->id;
        $order_data     = $this->orders_model->get_order_details($order_id, $lang_id);
        $order_products = $this->orders_model->get_order_products($order_id, $lang_id);
        $serials_data   = $this->orders_model->get_order_serials($order_id);
        $status_id      = 4;

        $this->_canceled_orders_operations($order_id, $order_data->country_id, $status_id, $order_products);

        $order_new_data['order_status_id'] = 4;
        $this->orders_model->update_order_data($order_id, $order_new_data);

        //redirect('orders/admin_order/', 'refresh');
        echo '1';
        /*
        $orders_ids = intval($this->input->post('row_id'), TRUE);
        if(is_array($orders_ids))
        {

            $ids_array = array();

            foreach($orders_ids as $row)
            {
                $ids_array[] = $row['value'];
            }
        }
        else
        {
            $ids_array = array($orders_ids);
        }

        $serials_ids_array = array();

        foreach($ids_array as $order_id)
        {
            // order log
            $log_data = array(
                                'order_id'  => $order_id,
                                'status_id' => 6,           //deleted
                                'unix_time' =>time()
                             );

            $this->orders_model->insert_order_log($log_data);

            //update product_serials value
            $serials_data = $this->orders_model->get_order_serials($order_id);

            foreach($serials_data as $serial)
            {
                $serial_data['serial_status'] = 0;
                $this->product_serials_model->update_serial($serial->product_serial_id, $serial_data);
            }
        }

        $this->orders_model->delete_order_data($ids_array);
        */
     }


     public function _approve_order($order_id, $order_data, $order_products, $secret_key, $secret_iv, $status_id)
     {
        $this->load->model('currencies/currency_model');
        $this->load->model('stores/stores_model');

        $update_order = 1;
        if($order_data->order_status_id == 10)
        {
          $update_order = 0;
        }

        $lang_id   = $this->data['active_language']->id;

        if($update_order)
        {
          if($order_data->is_pay_later_bill == 1)
          {
              $this->orders->add_order_bill($order_id, $order_data->final_total, '', $order_data);
          }

          $order_user_data      = $this->users_model->get_user($order_data->user_id);

          // add charge cards to user balance
          $recharge_cards_count = $this->orders_model->get_recharge_cards_count($order_id, 'recharge_card');

          if($recharge_cards_count > 0)
          {
              $recharge_cards_balance = $this->orders_model->get_recharge_card($order_id, $this->data['lang_id'], 'recharge_card');
              $new_balance = 0;

              foreach($recharge_cards_balance as $card)
              {
                  if($card->recharge_card_used == 0)
                  {
                      $new_balance += $card->final_price;
                      $card_updated_data['recharge_card_used'] = 1;
                      //$this->orders_model->update_product_order($order_id, $card->price, $card_updated_data);
                      $this->orders_model->update_table_data('orders_products', array('id'=>$card->id), $card_updated_data);
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
                  $user_balance     = $this->encryption->decrypt($enc_user_balance, $secret_key, $secret_iv);
                  $user_new_balance = $user_balance + $new_balance;
              }


              $balance = $this->encryption->encrypt($user_new_balance, $secret_key, $secret_iv);
              $user_new_balance_data['user_balance'] = $balance;

              $this->user_model->update_user_balance($order_data->user_id, $user_new_balance_data);

              $currency_data = $this->currency_model->get_country_currency_result($order_data->country_id);

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

              $this->user_balance_model->insert_balance_log($log_data);
          }

          //check upgrade account packages
          $package_check = $this->orders_model->get_recharge_cards_count($order_id, 'package');
          if($package_check > 0)
          {
              $order_package = $this->orders_model->get_recharge_card($order_id, $this->data['lang_id'], 'package', 'row');
//print_r($order_package); die();
              //update user data
              $user_new_account_data = array(
                'customer_group_id' => $order_package->package_id,
                'customer_group_price' => $order_package->price
              );
              $user_conds = array('id' => $order_data->user_id);

              $this->orders_model->update_table_data('users', $user_conds, $user_new_account_data);

              //insert in accounts log
              $account_log_data = array(
                'user_id'  => $order_data->user_id  ,
                'order_id' => $order_data->id       ,
                'previous_customer_group_id' => $order_user_data->customer_group_id,
                'current_customer_group_id'  => $order_package->package_id, //new customer group id
                'price'     => $order_package->price,
                'unix_time' => time()
              );

              $this->orders_model->insert_table_data('accounts_upgrading_log', $account_log_data);

          }

          //update product_serials value
          $serials_data         = $this->orders_model->get_order_serials($order_id);
          $non_serials_products = $this->orders_model->get_order_non_serials_products($order_id, $lang_id);

          if(count($serials_data) != 0)
          {
              foreach($serials_data as $serial)
              {
                  $serial_data['serial_status'] = 2;
                  $this->products_serials_model->update_serial($serial->product_serial_id, $serial_data);

                  $country_id = $order_data->country_id;

                  foreach($order_products as $product)
                  {
                      $product_qty = $this->products_model->count_product_available_quantity($product->product_id , $country_id );
                      $product_updated_data['product_quantity'] = $product_qty;

                      $this->products_model->update_product_countries($product->product_id, $country_id, $product_updated_data);
                  }
              }
          }

          //insert user total reward points
          $enc_user_points     = $order_user_data->user_points;
          $user_points         = $this->encryption->decrypt($enc_user_points, $secret_key, $secret_iv);

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
          $enc_reward_points        = $this->encryption->encrypt($user_total_reward_points, $secret_key, $secret_iv);

          $user_data['user_points'] = $enc_reward_points;

          $this->user_model->update_user($order_data->user_id, $user_data);

          // Add Affiliate
          $affliate_user_id = $order_user_data->affiliate_user_id;
          $order_data     = $this->orders_model->get_order($order_id);
          $affiliate_data = $this->admin_affiliate_model->get_afiliate_for_user($affliate_user_id);

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

          // send notification
          $userdata = $this->users_model->get_user($order_data->user_id);
          $username = $userdata->first_name.' '. $userdata->last_name;

          if($username){$username = $username;}
          else{$username = lang('visitor');}

          $emails[]   = $userdata->email;

          if($userdata->stop_wholesaler_sms == 0)
          {
              $phone  = $userdata->phone;
          }
          else
          {
              $phone  = '';
          }

          $user_email = $userdata->email;
          $status     = $this->order_status_model->get_status_translation_name($status_id, $lang_id);
          $new_serials_array = array();
          $email_msg  = '';
          $sms_msg    = '';

          if(count($serials_data) != 0 || count($non_serials_products) != 0)
          {
              $email_msg .= '<div style="width:100%; display:block; overflow:hidden; overflow:hidden;">
                              <table cellpadding="0" border="0" width="100%" style="text-align:center; font-size:14px;">
                          	   <tr style="background:#e1f0f8; font-size:14px;">
                                      <td>'.lang('thumbnail').'</td>
                                      <td>'.lang('product').'</td>
                                      <td>'.lang('quantity').'</td>
                                      <td>'.lang('price').'</td>
                                  </tr>';
              $sms_msg  = '';
//echo '<pre>'; print_r($serials_data); die();
              if(count($serials_data) != 0)
              {
                  foreach($serials_data as $serial)
                  {
                      $product_serial = $this->products_serials_model->get_products_serials_row($serial->product_serial_id);
                      $product_data   = $this->products_model->get_row_data($serial->product_id, $lang_id);
                      $product_price  = $this->orders_model->get_order_product_data($serial->product_id, $order_id);
                      $product_name   = $product_data->title;
                      //$img_path       = base_url().'assets/uploads/products/'.$product_data->image; // => Set Image From Server uploads
                      $img_path       = $this->data['images_path'].$product_data->image; // =>Set image from Amazon S3 Bucket 
                      //$store_name     = $product_data->store_name;

                      $secret_key  = $this->config->item('new_encryption_key');
                      $secret_iv   = md5('serial_iv');
                      $dec_serials = $this->encryption->decrypt($product_serial->serial, $secret_key, $secret_iv);

                      $email_msg .= '<tr>
                                      	<td><img src="'.$img_path.'" width="50" height="50" style=" display:block; margin:5px auto;" alt="'.$product_name.'"/></td>
                                          <td>'.$product_name.'</td>
                                          <td>'.$product_price->qty.'</td>
                                          <td>'.$product_price->final_price.' '.$order_data->currency_symbol.'</td>
                                     </tr>';

                      $sms_msg   .= lang('product').': '.$product_name.'--'.lang('serial').': '.$dec_serials.'***';
                  }
              }


              if(count($non_serials_products) != 0)
              {
                  foreach($non_serials_products as $product)
                  {
                      $product_price  = $this->orders_model->get_order_product_data($product->product_id, $order_id);
                      
                      //<td><img src="'.base_url().'assets/uploads/products/'.$product->image.'" width="50" height="50" style=" display:block; margin:5px auto;" alt="'.$product->title.'"/></td>
                      $email_msg .= '<tr>
                                      	<td><img src="'.$this->data['images_path'].$product->image.'" width="50" height="50" style=" display:block; margin:5px auto;" alt="'.$product->title.'"/></td>
                                          <td>'.$product->title.'</td>
                                          <td>'.$product_price->qty.'</td>
                                          <td>'.$product_price->final_price.' '.$order_data->currency_symbol.'</td>
                                     </tr>';

                      $sms_msg   .= lang('product').': '.$product->title.'***';
                  }
              }

          }

          $cards_count = $this->orders_model->get_recharge_cards_count($order_id);

          if($cards_count > 0)
          {
              $secret_key             = $this->config->item('new_encryption_key');
              $secret_iv              = $order_data->user_id;
              $enc_user_balance       = $userdata->user_balance;
              $user_balance           = $this->encryption->decrypt($enc_user_balance, $secret_key, $secret_iv);
              $user_currency_symbol   = $this->currency->get_country_symbol($userdata->store_country_id);

              $recharge_cards         = $this->orders_model->get_recharge_card($order_id);

              foreach($recharge_cards as $card)
              {
                  $email_msg .= '<tr>';
                  $email_msg .= '<td>'.lang('recharge_card').' </td><td> '.$card->price .' '. $user_currency_symbol . '</td>
                                 <td>'.lang('current_balance').' </td><td> '.$user_balance . ' ' . $user_currency_symbol . '</td>';
                  $email_msg .= '</tr>';


                  $sms_msg   .= lang('recharge_card').' : '.$card->price .' '. $user_currency_symbol.'  '.lang('current_balance').$user_balance.' '.$user_currency_symbol;

              }

              $email_msg .= '<tr>
                              <td colspan="2"></td>
                              <td><span>'.lang('final_total').'</span></td>
                              <td><span>'.$order_data->final_total.' '.$user_currency_symbol.'</span></td>
                             </tr>';
          }

          $email_msg .= '</div></table>';
          $payment_method = $this->payment_methods_model->get_row_data($order_data->payment_method_id, $lang_id);

          $store_name = $this->stores_model->get_store_name($order_data->store_id, $lang_id);

          $template_data = array(
                                  'unix_time'             => time()                                               ,
                                  'store_name'            => $store_name                                          ,
                                  'username'              => $username                                            ,
                                  'user_phone'            => $userdata->phone                                     ,
                                  'user_email'            => $user_email                                          ,
                                  'payment_method'        => $payment_method->name                                ,
                                  'status'                => $status                                              ,
                                  'order_id'              => $order_data->id                                      ,
                                  'logo_path'             => $this->images_path.$this->config->item('logo'), //base_url().'assets/uploads/'.$this->config->item('logo') ,
                                  'img_path'              => base_url().'assets/template/site/img/'               ,
                                  'order_time'            => date('Y/m/d H:i', $order_data->unix_time)            ,
                                  'order_details_email'   => $email_msg                                           ,
                                  'order_details_sms'     => $sms_msg
                                );
                                
          if($this->notifications->create_notification('pending_order_completed', $template_data, $emails, $phone))
          {
              $this->session->set_flashdata('success',lang('success'));
          }
          else
          {
              $this->session->set_flashdata('notification_error',lang('serials_msg_not_sent'));
          }
        }

        $this->_insert_order_log($order_id, $status_id);
     }

     private function _canceled_orders_operations($order_id, $country_id, $status_id, $order_products)
     {
        $order_data = $this->orders_model->get_order($order_id);

        //if order payment method = balance ... return user balance
        if($order_data->payment_method_id == 1)
        {
            $user_old_balance = $this->admin_bootstrap->get_user_balance($order_data->user_id);
            $user_new_balance = $user_old_balance + $order_data->final_total;

            $this->admin_bootstrap->encrypt_and_update_users_data($order_data->user_id, 'user_balance', $user_new_balance);

            $balance_log_data = array(
                                        'user_id'           => $order_data->user_id             ,
                                        'order_id'          => $order_id                        ,
                                        'payment_method_id' => $order_data->payment_method_id   ,
                                        'balance'           => $user_new_balance                ,
                                        'amount'            => $order_data->final_total         ,
                                        'currency_symbol'   => $order_data->currency_symbol     ,
                                        'store_country_id'  => $order_data->country_id          ,
                                        'balance_status_id' => 7 , // order cancelled
                                        'ip_address'        => $this->input->ip_address()       ,
                                        'unix_time'         => time()
                                     );

            $this->user_balance_model->insert_balance_log($balance_log_data);

        }
        else if($order_data->payment_method_id = 2)  //if order payment method = reward points ... return user reward points
        {
            $order_points = $this->admin_bootstrap->convert_into_reward_points($order_data->country_id, $order_data->final_total);

            $user_old_points = $this->admin_bootstrap->get_user_reward_points($order_data->user_id);
            $user_new_points = $order_points + $user_old_points;

            $this->admin_bootstrap->encrypt_and_update_users_data($order_data->user_id, 'user_points', $user_new_points);
        }

        //update product_serials value
        $serials_data = $this->orders_model->get_order_serials($order_id);

        if(count($serials_data) != 0)
        {
            foreach($serials_data as $serial)
            {
                $serial_data['serial_status'] = 0;
                $serial_data['sold_order_id'] = 0;
                $this->products_serials_model->update_serial($serial->product_serial_id, $serial_data);

                foreach($order_products as $product)
                {
                    $product_qty = $this->products_model->count_product_available_quantity($product->product_id, $country_id);
                    $product_updated_data['product_quantity'] = $product_qty;

                    $this->products_model->update_product_countries($product->product_id, $country_id, $product_updated_data);
                }
            }
        }

        $this->_insert_order_log($order_id, $status_id);

        //  send_notification

        $user_data     = $this->admin_bootstrap->get_user_by_id($order_data->user_id);
        $username      = $user_data->first_name.' '. $user_data->last_name;
        $emails[]      = $user_data->email;
        $mobile_number = $user_data->phone;
        $template_data = array(
                                'unix_time'    => time(),
                                'username'     => $username,
                                'order_id'     => $order_id,
                                'logo_path'    => $this->images_path.$this->config->item('logo'), //base_url().'assets/uploads/'.$this->config->item('logo'),
                                'order_time'   => date('Y/m/d H:i', $order_data->unix_time),
                                'year'         => date('Y')
                              );
                              
        $this->notifications->create_notification('cancel_order', $template_data, $emails, $mobile_number);
     }

     private function _insert_order_log($order_id, $status_id)
     {
        $log_data = array(
                            'order_id'  => $order_id  ,
                            'status_id' => $status_id ,
                            'unix_time' => time()
                         );

        $this->orders_model->insert_order_log($log_data);
     }

     public function add()
     {
        $validation_msg = false;

        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
        {
            $validation_msg = true;

             $this->form_validation->set_rules('users', lang('username'), 'required');
             $this->form_validation->set_rules('country_id', lang('country'), 'required');
             $this->form_validation->set_rules('store_id', lang('name_of_store'), 'required');
             $this->form_validation->set_rules('product_id[]', lang('products'), 'required');

             $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

        }

        if ($this->form_validation->run() == FALSE)
		{
		  $this->_add_form($validation_msg);
        }
        else
        {
            $lang_id         = $this->data['active_language']->id;
            $user_id         = $this->input->post('users');
            $store_id        = $this->input->post('store_id');
            $notes           = $this->input->post('notes');
            $country_id      = $this->input->post('country_id');
            $total_price     = $this->input->post('total_price');
            $final_price     = $this->input->post('final_price');
            $optional_fields = $this->input->post('optional_field');
            $secret_iv       = $user_id;
            $secret_key      = $this->config->item('new_encryption_key');

            $currency_symbol = $this->currency->get_country_symbol($country_id);

            //insert order data
            $order_data      = array(
                                        'user_id'                   => $user_id                                 ,
                                        'store_id'                  => $store_id                                ,
                                        'agent'                     => 'admin -- '.$_SERVER['HTTP_USER_AGENT']  ,
                                        'currency_symbol'           => $currency_symbol                         ,
                                        'country_id'                => $country_id                              ,
                                        'items_count'               => 0                                        ,
                                        'total'                     => $total_price                             ,
                                        'final_total'               => $final_price                             ,
                                        'notes'                     => $notes                                   ,
                                        'unix_time'                 => time()                                   ,
                                        'day'                       => date('d')                                ,
                                        'month'                     => date('m')                                ,
                                        'year'                      => date('Y')                                ,
                                        'admin_not_completed_order' => 1                                        ,
                                        'not_added_optional_fields' => 1
                                    );

            $this->orders_model->insert_order($order_data);
            $order_id = $this->db->insert_id();

            //insert order products
            $quantity = $this->input->post('quantity');
            $products = $this->input->post('product_id');
            $products_count      = 0;
            $total_reward_points = 0;
            $total_discount      = 0;

            foreach($products as $key => $product_id)
            {
                if($product_id != 0)
                {
                    $product_details    = $this->products_model->get_product_row_details($product_id, $lang_id, $country_id);
                    $product_price_data = $this->products_lib->get_product_price_data($product_details, $country_id, $user_id, session_id());

                    $reward_points      = $product_details->reward_points;

                    if($product_details->quantity_per_serial == 1)
                    {
                        $product_cost = $product_details->average_cost * $quantity[$key];
                    }
                    else
                    {
                        $product_cost = $product_details->cost * $quantity[$key];
                    }

                    $order_product_data = array(
                                                   'order_id'           => $order_id                                        ,
                                                   'type'               => 'product'                                        ,
                                                   'product_id'         => $product_id                                      ,
                                                   'cat_id'             => $product_details->cat_id                         ,
                                                   'qty'                => $quantity[$key]                                  ,
                                                   'price'              => $product_price_data[0]                           ,
                                                   'final_price'        => $product_price_data[1]                           ,
                                                   'discount'           => $product_price_data[4]                           ,
                                                   'reward_points'      => $product_details->reward_points * $quantity[$key]   ,
                                                   'purchased_cost'     => $product_cost                                    ,
                                                   'unix_time'          => time()
                                               );

                    $this->orders_model->insert_order_products($order_product_data);

                    $product_quantity     = $this->products_model->count_product_available_quantity($product_id, $country_id);
                    $min_stock            = $this->settings->min_product_stock;
                    $available_qty        = $product_quantity - $min_stock;
                    $new_product_quantity = $available_qty - $quantity[$key];
                    $updated_amount       = array('product_quantity' => $new_product_quantity);

                    $this->products_model->update_product_country_amount($updated_amount, $product_id, $country_id);

                    if($new_product_quantity == 0)
                    {
                        $product_name  = $product_details->title;
                        $emails[]      = $this->config->item('email');
                        $mobile_number = $this->config->item('mobile');
                        $template_data = array('product'=>$product_name);

                        $this->notifications->create_notification('product_quantity_less_than_threshold', $template_data, $emails, $mobile_number);
                    }


                    $products_count += $quantity[$key];
                    $total_discount += $product_price_data[4] * $quantity[$key];
                }
            }

            if(count($optional_fields) != 0)
            {
                foreach($optional_fields as $option_id=>$option_value)
                {
                    $option_data = $this->products_model->get_product_optional_field_data($option_id);

                    if(is_array($option_value))
                    {
                        foreach($answer as $row)
                        {
                            $optional_fields_data = array(
                                                            'user_id'                       => $user_id                 ,
                                                            'product_id'                    => $option_data->product_id ,
                                                            'product_optional_field_id'     => $option_id               ,
                                                            'product_optional_field_value'  => $row                     ,
                                                            'order_id'                      => $order_id                ,
                                                            'unix_time'                     => time()
                                                        );

                            $this->products_model->insert_user_optional_fields_data($optional_fields_data);
                        }
                    }
                    else
                    {


                        $optional_fields_data = array(
                                                    'user_id'                       => $user_id                 ,
                                                    'product_id'                    => $option_data->product_id ,
                                                    'product_optional_field_id'     => $option_id               ,
                                                    'product_optional_field_value'  => $user_option             ,
                                                    'order_id'                      => $order_id                ,
                                                    'unix_time'                     => time()
                                                );

                        $this->products_model->insert_user_optional_fields_data($optional_fields_data);
                    }
                }
            }

            // update products count in orders table
            $updated_data = array(
                                    'items_count' => $products_count,
                                    'discount'    => $total_discount
                                 );

            $this->orders_model->update_order_data($order_id, $updated_data);

            $this->_generate_order_serials($order_id, $lang_id, $country_id);

            redirect('orders/admin_order/order_optional_fields/'.$order_id, 'refresh');
        }
     }

     private function _add_form($validation_msg)
     {
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }

        $lang_id                    = $this->lang_row->id;
        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/add";

        $users                      = $this->users_model->get_active_users_names();
        $countries                  = $this->countries_model->get_countries($this->lang_row->id);
        $bank_accounts              = $this->bank_accounts_model->get_bank_accounts_result($lang_id);
        $status                     = $this->order_status_model->get_all_statuses($lang_id);
        $stores                     = $this->stores;

        $all_users                  = array();
        $countries_options          = array();
        $products_options           = array();
        $status_options             = array();
        $stores_options             = array();

        $all_users[null]            = lang('choose');
        $countries_options[null]    = lang('choose');
        $stores_options[null]       = lang('choose');

        foreach($users as $user)
        {
            $all_users[$user->id] = $user->first_name.' '.$user->last_name;
        }

        foreach($countries as $row)
        {
            $countries_options[$row->id] = $row->name;
        }

        foreach($status as $row)
        {
            $status_options[$row->id] = $row->name;
        }

        foreach($stores as $row)
        {
            $stores_options[$row->id] = $row->name;
        }

        $this->data['products']          = $products_options;
        $this->data['countries_options'] = $countries_options;
        $this->data['users']             = $all_users;
        $this->data['bank_accounts']     = $bank_accounts;
        $this->data['status_options']    = $status_options;
        $this->data['stores']            = $stores_options;

        $this->data['content']           = $this->load->view('orders_form', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
     }

     public function check_qty()
     {
        $status_array           = array();
        $optional_fields_html   = '';

        $lang_id    = $this->data['active_language']->id;

        $user_id    = $this->input->post('users');
        $quantity   = $this->input->post('quantity');
        $country_id = $this->input->post('country_id');
        $products   = $this->input->post('product_id');

        $new_quantities_total_count = array_sum($quantity);

        foreach($products as $key=>$product_id)
        {
            $product_details                = $this->products_model->get_product_row_details($product_id, $lang_id, $country_id);
            $product_optiona_fields_count   = $this->products_model->count_product_optional_fields($product_id);

            if($product_details->quantity_per_serial == 0)
            {
                if($quantity[$key] != 1)
                {
                    $message = lang('qty_of_one_only');
                    $status  = 4;
                }
                else
                {
                    //check optional fields
                    if($product_optiona_fields_count > 0)
                    {
                        // add optional fields
                        //$optional_fields_html = $this->get_products_optional_fields($product_id);
                        $status = '5';  // optional fields required

                    }
                    else
                    {
                        $message = lang('product_qty_increaded');
                        $status  = 1;
                    }
                }
            }
            else
            {
                $qty             = $quantity[$key];
                $product_details = $this->products_model->get_product_row_details($product_id, $lang_id, $country_id);

                // check if product qty is valid and is > 0
                if($quantity[$key] < 1)
                {
                    $message =  lang('qty_error');
                    $status  = 0;
                }
                else
                {
                    // check if there is available stock
                    $product_quantity     = $this->products_model->count_product_available_quantity($product_id, $country_id);
                    $min_stock            = $this->settings->min_product_stock;
                    $available_qty        = $product_quantity - $min_stock;

                    if($quantity[$key] > $available_qty)
                    {
                        $status = '0';
                        $out_of_amount[$product_id] = 'true';
                    }
                    else
                    {

                        //check if product exist in country
                        $check_product_country = $this->products_model->check_product_country_exist($product_id, $country_id);

                        if($check_product_country)
                        {
                            // check product available quantity
                            $stock_count = $this->products_model->count_product_available_quantity($product_id, $country_id);
                            $min_stock   = $this->config->item('min_product_stock');

                            $product_qty = $stock_count - $min_stock;

                            if($product_qty >= $qty)  // check stock
                            {
                                $out_of_amount[$product_id] = 'false';

                                //$product_details    = $this->products_model->get_product_row_details($product_id, $this->data['lang_id'], $this->data['country_id']);
                                $product_price_data = $this->products_lib->get_product_price_data($product_details, $country_id, $user_id, session_id());

                                if($new_quantities_total_count <= $product_price_data[7] || $product_price_data[7] == 0)  // check available products count per customer group
                                {
                                    if($qty <= $product_price_data[3] || $product_price_data[2] == 0)
                                    {
                                        //check optional fields
                                        if($product_optiona_fields_count > 0)
                                        {
                                            // add optional fields
                                            //$optional_fields_html = $this->get_products_optional_fields($product_id);

                                            $status = '5';  // optional fields required
                                        }
                                        else
                                        {
                                            $message = lang('product_qty_increaded');

                                            $status = '1';
                                            $out_of_amount[$product_id] = 'false';
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
                                $status = '0';
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


            $status_array[] = $status;
        }

        if(in_array(0, $status_array))
        {
            echo json_encode(array(0, lang('no_stock'), $status));
        }
        elseif(in_array(2, $status_array))
        {
            echo json_encode(array(0, lang('max_qty_per_user_discount_reached'), $status));
        }
        elseif(in_array(3, $status_array))
        {
            echo json_encode(array(0, lang('max_products_per_order_reached'), $status));
        }
        elseif(in_array(4, $status_array))
        {
            echo json_encode(array(0, lang('qty_of_one_only'), $status));
        }
        else
        {
            echo json_encode(array(1,  ('available'), $status, $optional_fields_html));
        }

        /**
           * $status = 0 --> no stock
           * $status = 1 --> available
           * $status = 2 --> max_qty_per_user_discount_reached
           * $status = 3 --> max_products_per_order_reached
           * $status = 4 --> cant add more than one of this product

        **/
     }

     public function order_optional_fields($order_id)
     {
        $lang_id         = $this->data['active_language']->id;
        $optional_fields = $this->check_optional_fields($order_id);

        if(count($optional_fields) == 0)
        {
            $order_data['not_added_optional_fields'] = 0;
            $this->orders_model->update_order_data($order_id, $order_data);
            redirect('orders/admin_order/finish_order/'.$order_id, 'refresh');
        }
        else
        {

            $validation_msg = false;

            if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
            {
                $validation_msg = true;
                $product_ids    = $this->input->post('product_id');
                $product_ids    = array_unique($product_ids);

                foreach ($product_ids as $product_id)
                {
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
                            $custom_validation = '|integer';
                        }

                        if($field->required == 1)
                        {
                            $custom_validation .= '|required';
                        }

                        $this->form_validation->set_rules('optional_field['.$field->id.']', $field->label, 'trim'.$custom_validation);
                    }
                }
                $this->form_validation->set_message('required', lang('required').' : %s ');
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            }

            if ($this->form_validation->run() == FALSE)
    		{
    		  $this->_order_optional_fields_form($order_id, $optional_fields, $validation_msg);
            }
            else
            {
                $posted_options = $this->input->post('optional_field', TRUE);
                $has_options    = $this->input->post('has_options', TRUE);
                $order_data     = $this->orders_model->get_order_data($order_id);

                foreach ($posted_options as $option_id=>$answer)
                {
                    $option_data        = $this->optional_fields_model->get_product_option_row($option_id);
                    $order_product_id   = $this->orders_model->get_order_product_id($order_id, $option_data->product_id);

                    if(is_array($answer))
                    {
                        foreach($answer as $row)
                        {
                            $optional_fields_data = array(
                                                            'user_id'                       => $order_data->user_id     ,
                                                            'product_id'                    => $option_data->product_id ,
                                                            'product_optional_field_id'     => $option_id               ,
                                                            'product_optional_field_value'  => strip_tags($row)         ,
                                                            'order_id'                      => $order_id                ,
                                                            'order_products_id'             => $order_product_id        ,
                                                            'unix_time'                     => time()
                                                        );

                            $this->products_model->insert_user_optional_fields_data($optional_fields_data);
                        }
                    }
                    else
                    {
                        $optional_fields_data = array(
                                                        'user_id'                       => $order_data->user_id     ,
                                                        'product_id'                    => $option_data->product_id ,
                                                        'product_optional_field_id'     => $option_id               ,
                                                        'product_optional_field_value'  => strip_tags($answer)      ,
                                                        'order_id'                      => $order_id                ,
                                                        'order_products_id'             => $order_product_id        ,
                                                        'unix_time'                     => time()
                                                    );

                        $this->products_model->insert_user_optional_fields_data($optional_fields_data);

                    }


                }

                $order_updated_data['not_added_optional_fields'] = 0;
                $this->orders_model->update_order_data($order_id, $order_updated_data);
                redirect('orders/admin_order/finish_order/'.$order_id, 'refresh');
            }
        }
     }

     private function _order_optional_fields_form($order_id, $optional_fields, $validation_msg)
     {
        $this->data['form_action']                  = $this->data['module'] . "/" . $this->data['controller'] . "/order_optional_fields/".$order_id;
        $this->data['order_id']                     = $order_id;
        $this->data['products_optional_fields']     = $optional_fields;

        $this->data['content'] = $this->load->view('order_optional_fields_form', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
     }

     public function finish_order($order_id)
     {
        /**************************************/
        if($this->data['method_id'] == 0)
        {
            $add_method_id = $this->admin_bootstrap->get_a_controller_method_id($this->data['controller_id'], 'add');
        }
        else
        {
            $add_method_id = $this->data['method_id'];
        }

        $this->stores   = $this->admin_bootstrap->get_user_available_stores($add_method_id);
        $store_id_array = array();

        foreach($this->stores as $store)
        {
            $store_id_array[] = $store->store_id;
        }

        $this->stores_ids = $store_id_array;
        /**************************************/

        $order_id       = intval($order_id);
        $lang_id        = $this->data['active_language']->id;

        $order_data     = $this->orders_model->get_order_with_country($order_id, $lang_id);

        if(in_array($order_data->store_id, $this->stores_ids))
        {
            if($order_data->not_added_optional_fields != 0)
            {
                redirect('orders/admin_order/order_optional_fields/'.$order_id, 'refresh');
            }
            else
            {

                if($order_data)
                {
                    if($order_data->admin_not_completed_order == 1)
                    {
                        $status         = $this->order_status_model->get_all_statuses($lang_id);
                        $order_products = $this->orders_model->get_order_products($order_id, $lang_id);

                        $this->get_payment_methods($order_data->final_total, $order_data->country_id, $order_data->user_id);

                        $this->data['form_action']    = $this->data['module'] . "/" . $this->data['controller'] . "/";
                        $this->data['order_data']     = $order_data;
                        $this->data['order_products'] = $order_products;
                    }
                    else
                    {
                        $this->data['error_msg'] = lang('not_allowed_order');
                    }
                }
                else
                {
                    $this->data['error_msg'] = lang('no_data_about_this_order');
                }

                $this->data['content'] = $this->load->view('finish_order', $this->data, true);
                $this->load->view('Admin/main_frame',$this->data);
            }
        }
        else
        {
            $this->data['error_msg'] = lang('no_store_permission');

            $this->data['content'] = $this->load->view('finish_order', $this->data, true);
            $this->load->view('Admin/main_frame',$this->data);
        }

     }

     public function get_order_prices()
     {
        $order_id = $this->input->post('order_id');
        $order_id = intval($order_id);

        $order_data   = $this->orders_model->get_order_data($order_id);

        $result_array = array($order_data->total, $order_data->discount, $order_data->coupon_discount, $order_data->tax, $order_data->final_total);

        echo json_encode($result_array);
     }

     public function get_payment_methods($final_total, $country_id, $user_id)
     {
        $secret_key           = $this->config->item('new_encryption_key');
        $secret_iv            = $user_id;

        $wholesaler_pocket    = 0;
        $user_customer_group  = 0;

        $pay_by_bank          = true;
        $is_wholesaler        = false;
        $pay_by_pocket        = false;
        $pay_by_reward_points = false;

        $not_included_ids     = array();

        $wholesaler_customer_group_id = $this->settings->wholesaler_customer_group_id;

        $user_data = $this->admin_bootstrap->get_user_by_id($user_id);


        $user_customer_group = $user_data->customer_group_id;

        if($user_customer_group == $wholesaler_customer_group_id)
        {
            $is_wholesaler  = true;
        }

        $enc_user_balance = $user_data->user_balance;

        $user_balance     = $this->encryption->decrypt($enc_user_balance, $secret_key, $secret_iv);

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
        $user_points       = $this->encryption->decrypt($enc_user_points, $secret_key, $secret_iv);
        $point_value       = $this->countries_model->get_reward_points($country_id);
        $user_points_value = $user_points * $point_value;

        if($final_total <= $user_points_value)
        {
            $pay_by_reward_points = true;
        }


        $display_lang_id     = $this->data['active_language']->id;
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

        $payment_options = $this->payment_methods_model->get_available_payment_options($display_lang_id, $user_customer_group, $not_included_ids, $wholesaler_pocket);


        $this->data['final_total']             = $final_total;
        $this->data['bank_accounts']           = $bank_accounts;
        $this->data['payment_options']         = $payment_options;
        $this->data['pay_by_bank']             = $pay_by_bank;
        $this->data['pay_by_pocket']           = $pay_by_pocket;
        $this->data['pay_by_reward_points']    = $pay_by_reward_points;

    }

     public function get_country_products()
     {
        $lang_id                   = $this->lang_row->id;
        $country_id                = $this->input->post('country_id');
        $user_id                   = $this->input->post('user_id');
        $store_id                  = $this->input->post('store_id');

        $countries_products_result = $this->products_model->get_products_per_country($country_id, $lang_id, $store_id);

        $countries_products = "<option value='0'>-----------</option>";

        foreach($countries_products_result as $product)
        {

            $product_details    = $this->products_model->get_product_row_details($product->product_id, $lang_id, $country_id);
            $product_price_data = $this->products_lib->get_product_price_data($product_details, $country_id, $user_id, session_id());

            $countries_products .= "<option value=$product->product_id data-price=$product->price> $product->title ---$product_price_data[1]  $product->currency";

        }

        echo $countries_products;

     }

     public function total($product_ids=array(), $country_id=0, $quantity=array(), $user_id=0, $mode='0')
    {
        $product_ids = $this->input->post('product_id');
        $country_id  = $this->input->post('country_id');
        $quantity    = $this->input->post('quantity');
        $user_id     = $this->input->post('users');

        $lang_id     = $this->data['active_language']->id;

        $total       = 0;
        $final_total = 0;

        if(isset($_POST['product_id']))
        {
            foreach($product_ids as $key=>$product_id)
            {
                $product_details    = $this->products_model->get_product_row_details($product_id, $lang_id, $country_id);
                $product_price_data = $this->products_lib->get_product_price_data($product_details, $country_id, $user_id, session_id());

                $price_before = $product_price_data[0];
                $price        = $product_price_data[1];

                $total       += $price_before * $quantity[$key];
                $final_total += $price * $quantity[$key];


            }
        }

        if($mode == 0)   // to echo in view page using jquery
        {
            echo json_encode(array($total, $final_total));
        }
        else   // to return an array
        {
            return array($total, $final_total);
        }

    }

    public function coupon_discount($mode=0)
    {
        /*
         //mode = 0 => echo json_encode
         //mode = 1 => return array
        */
        $output = array();
        $current_date  = time();
        $coupon_code   = trim(strip_tags($this->input->post('coupon', TRUE)));
        $order_id      = trim(strip_tags($this->input->post('oredr_id', TRUE)));

        $order_data    = $this->orders_model->get_order($order_id);

        if($order_data)
        {
            $product_ids   = $this->input->post('product_id');
            $quantity      = $this->input->post('quantity');

            $final_price   = $order_data->final_total;

            $coupon_data   = $this->coupon_codes_model->get_coupon_data($coupon_code);

            if($coupon_data)
            {
                if($coupon_data->country_id != $order_data->country_id)
                {
                    $output = array(0, lang('this_coupon_cant_be_used_in_this_country'));
                    // 'this coupon cant be used in this country';
                    echo json_encode($output);exit();
                }

                if($current_date > $coupon_data->end_unix_time )
                {
                    $output = array(0, lang('this_coupon_dates_ends'));
                    //'this_coupon_dates_end';
                    if($mode == 0)
                    {
                        echo json_encode($output);
                    }
                    else
                    {
                        return $output;
                    }
                    exit();
                }

                if($current_date < $coupon_data->start_unix_time)
                {
                    $output = array(0, lang('this_coupon_dates_not_started_yet'));
                    // 'this_coupon_dates_not_started_yet';
                    //echo json_encode($output);exit();

                    if($mode == 0)
                    {
                        echo json_encode($output);
                    }
                    else
                    {
                        return $output;
                    }
                    exit();
                }

                $coupon_uses_count = $this->coupon_codes_model->get_coupon_uses_conditioned_count($coupon_data->id);

                if($coupon_uses_count > $coupon_data->uses_per_coupon)
                {
                    $output = array(0, lang('maximum_uses_per_coupon_reached'));
                    // 'maximum_uses_per_coupon_reached';
                   // echo json_encode($output);exit();

                   if($mode == 0)
                    {
                        echo json_encode($output);
                    }
                    else
                    {
                        return $output;
                    }
                    exit();
                }

                $user_conditions = array('user_id' => $order_data->user_id);

                $coupon_count_per_user = $this->coupon_codes_model->get_coupon_uses_conditioned_count($coupon_data->id, $user_conditions);

                if($coupon_count_per_user >= $coupon_data->uses_per_customer)
                {
                    $output = array(0, lang('you_have_reached_maximum_uses_of_coupon'));
                    // 'you_have_reached_maximum_uses_of_coupon';
                    //echo json_encode($output);exit();

                    if($mode == 0)
                    {
                        echo json_encode($output);
                    }
                    else
                    {
                        return $output;
                    }
                    exit();
                }

                if($final_price < $coupon_data->min_amount)
                {
                    $output = array(0, lang('total_price_of_cart_is_not_enough_to_use_this_coupon'));
                    // 'total price for cart is not enough to use this coupon';
                    //echo json_encode($output);exit();

                    if($mode == 0)
                    {
                        echo json_encode($output);
                    }
                    else
                    {
                        return $output;
                    }
                    exit();
                }

                $coupon_applied = $this->_check_coupon($order_id, $order_data, $coupon_data, $order_data->user_id);

                if($coupon_applied == 0)
                {
                    $output = array(0, lang('no_discount_on_these_products'));
                    // 'this coupon cant be applied on these products';
                    //echo json_encode($output);exit();

                    if($mode == 0)
                    {
                        echo json_encode($output);
                    }
                    else
                    {
                        return $output;
                    }
                    exit();
                }
                else
                {
                    $output = array(1, lang('coupon_success'));
                    //echo json_encode($output);exit();

                    if($mode == 0)
                    {
                        echo json_encode($output);
                    }
                    else
                    {
                        return $output;
                    }
                    exit();
                }
            }
            else
            {
                $output = array(0, lang('coupon_code_not_existing'));
                // 'coupon_code_not_existing';
                //echo json_encode($output);exit();

                if($mode == 0)
                {
                    echo json_encode($output);
                }
                else
                {
                    return $output;
                }
                exit();
            }

            //echo json_encode($output);

        }
    }

     private function _check_coupon($order_id, $order_data, $coupon_data, $user_id)
     {
        $order_total_discount   = 0;
        $discount_on_product    = 0;
        $coupon_products_exist  = false;
        $order_coupon_products  = array();

        $order_products  = $this->orders_model->get_order_all_products($order_id);

        //coupon type : total
        if($coupon_data->product_or_category == 'total')
        {
            # if total coupon will be applied on final total price of cart,
            # so the qty of the products will be consered equals 1

            $price = $order_data->final_total;

            $total_price_after_discount   = $this->_calculate_amount($coupon_data->discount_type, $price, 1, $coupon_data->discount);
            $order_total_discount         = $price - $total_price_after_discount;

            $price_data = array(
                                   'coupon_discount' => $order_total_discount,
                                   'final_total'     => $total_price_after_discount
                               );

            $this->orders_model->update_order_data($order_id, $price_data);

            $coupon_products_exist = true;

        }
        else
        {
            foreach($order_products as $product)
            {
                $product_price_after_discount = 0;
                $price = $product->final_price * $product->qty;

                if($coupon_data->product_or_category == 'category')     //coupon type : category
                {
                    $coupon_cats_ids = $this->coupon_codes_model->get_coupon_cats_ids($coupon_data->id);

                    if(in_array($product->cat_id, $coupon_cats_ids))
                    {
                        $product_price_after_discount = $this->_calculate_amount($coupon_data->discount_type, $price, $product->qty, $coupon_data->discount);

                        $discount_on_product              = $price - $product_price_after_discount;

                        $product->{'cat_coupon'}          = true;
                        $product->{'discount_on_product'} = $discount_on_product;
                        $order_coupon_products[]          = $product;
                        $one_product_discount             = $discount_on_product / $product->qty;
                        $one_product_final_price          = $product_price_after_discount / $product->qty;

                        $order_product_data               = array(
                                                                   'coupon_discount' => $one_product_discount  ,
                                                                   'final_price'     => $one_product_final_price
                                                                 );

                        $this->orders_model->update_product_order_data($order_id, $product->product_id, $order_product_data);

                        $coupon_products_exist = true;
                    }

                }
                elseif($coupon_data->product_or_category == 'product')   //coupon type : product
                {
                    $coupon_products = $this->coupon_codes_model->get_coupon_products_ids($coupon_data->id);

                    if(in_array($product->product_id, $coupon_products))
                    {
                        $product_price_after_discount     = $this->_calculate_amount($coupon_data->discount_type, $price, $product->qty, $coupon_data->discount);

                        $discount_on_product              = $price - $product_price_after_discount;

                        $product->{'product_coupon'}          = true;
                        $product->{'discount_on_product'} = $discount_on_product;
                        $order_coupon_products[]          = $product;

                        $one_product_discount             = $discount_on_product / $product->qty;
                        $one_product_final_price          = $product_price_after_discount / $product->qty;

                        $order_product_data = array(
                                                       'coupon_discount' => $one_product_discount  ,
                                                       'final_price'     => $one_product_final_price
                                                   );

                        $this->orders_model->update_product_order_data($order_id, $product->product_id, $order_product_data);

                        $coupon_products_exist = true;
                    }

                }

                $order_total_discount += $discount_on_product;
            }

            $order_final_total  = $order_data->final_total - $order_total_discount;
            $order_updated_data = array(
                                           'coupon_discount' => $order_total_discount,
                                           'final_total'     => $order_final_total
                                       );
            $this->orders_model->update_order_data($order_id, $order_updated_data);
        }

        if(!$coupon_products_exist)
        {
            return lang('no_discount_on_these_products');
        }



        // insert in coupon_codes_users
        $coupon_code_user_data = array(
                                        'user_id'          =>  $user_id                       ,
                                        'session_id'       =>  session_id()                   ,
                                        'coupon_id'        =>  $coupon_data->id               ,
                                        'discount_type'    =>  $coupon_data->discount_type    ,
                                        'coupon_discount'  =>  $coupon_data->discount         ,
                                        'total_discount'   =>  $order_total_discount          ,
                                       	'order_id'         =>  $order_id                      ,
                                        'unix_time'        =>  time()
                                      );

        $this->coupon_codes_model->insert_coupon_uses_data($coupon_code_user_data);

        $coupon_code_user_id  = $this->db->insert_id();

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

                $this->coupon_codes_model->insert_coupon_uses_products($product_data);
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

    public function apply_order_payment_method()
    {
        $order_updated_data = array();
        $order_id          = intval($this->input->post('order_id'));
        $payment_method_id = intval($this->input->post('payment_option_id'));

        $order_status_id   = $this->order_status_model->get_status_id($payment_method_id, 'payment_methods');

        $order_data   = $this->orders_model->get_order_data($order_id);
        $final_total  = $order_data->final_total - $order_data->tax;
        $tax          = $this->calculate_payment_tax($payment_method_id, $final_total, $order_data->country_id);

        $final_total_price_with_tax = $final_total + $tax;

        $order_updated_data = array(
                                       'payment_method_id' => $payment_method_id,
                                       'tax'               => $tax,
                                       'final_total'       => $final_total_price_with_tax,
                                       'order_status_id'   => $order_status_id
                                    );

        $this->orders_model->update_order_data($order_id, $order_updated_data);
        return true;
    }

    public function calculate_payment_tax($payment_option_id, $total, $country_id)
    {
        if($payment_option_id == 0) return 0;

        $option_data = $this->payment_methods_model->get_option_data($payment_option_id);

        $tax_percent = round(($option_data->extra_fees_percent * $total), 2)/ 100;
        $tax_amount  = $this->currency->convert_from_default_currency($option_data->extra_fees, $country_id);
        $tax         = $tax_percent + $tax_amount;

        return $tax;
     }

     public function submit_order()
     {
        $order_updated_data = array();
        $lang_id            = $this->data['active_language']->id;

        $order_id           = $this->input->post('order_id');
        $payment_method_id  = $this->input->post('payment_option_id');

        $order_data = $this->orders_model->get_order_data($order_id);
        $user_data  = $this->admin_bootstrap->get_user_by_id($order_data->user_id);

        $secret_key = $this->config->item('new_encryption_key');
        $secret_iv  = $order_data->user_id;


        //pocket
        if($payment_method_id == 1)
        {
            $user_enc_old_balance = $user_data->user_balance;
            $user_old_balance     = $this->encryption->decrypt($user_enc_old_balance, $secret_key, $secret_iv);
            //convert to order currency
            $user_old_balance     = $this->currency->convert_amount_from_country_to_country($user_data->store_country_id, $order_data->country_id, $user_old_balance);

            if($user_old_balance >= $order_data->final_total)
            {
                $user_new_balance     = $user_old_balance - $order_data->final_total;
                $user_enc_new_balance = $this->encryption->encrypt($user_new_balance, $secret_key, $secret_iv);
                $user_balance_data['user_balance']      = $user_enc_new_balance;
                $user_balance_data['store_country_id']  = $order_data->country_id;


                $this->user_model->update_user_balance($order_data->user_id, $user_balance_data);

                $log_data = array(
                                    'user_id'           => $order_data->user_id         ,
                                    'payment_method_id' => $payment_method_id           ,
                                    'currency_symbol'   => $order_data->currency_symbol ,
                                    'store_country_id'  => $order_data->country_id      ,
                                    'amount'            => $order_data->final_total     ,
                                    'balance'           => $user_new_balance            ,
                                    'balance_status_id' => 1,  //withdraw from balance
                                    'ip_address'        => $this->input->ip_address()   ,
                                    'unix_time'         => time()
                                 );

                $this->user_balance_model->insert_balance_log($log_data);
            }
            else
            {
                echo 'error_in_pocket';
            }

            $template_payment_method = lang('pocket_money');
        }
        elseif($payment_method_id == 2) //reward points
        {
            $user_reward_points_value = $this->admin_bootstrap->get_user_reward_points_value($order_data->user_id, $order_data->country_id);

            if($user_reward_points_value >= $order_data->final_total)
            {
                $user_new_reward_points_value = $user_reward_points_value - $order_data->final_total;
                $user_new_reward_points       = $this->admin_bootstrap->convert_user_reward_points($user_new_reward_points_value, $order_data->country_id);

                $this->admin_bootstrap->encrypt_and_update_users_data($order_data->user_id, 'user_points', $user_new_reward_points);

            }
            else
            {
                echo 'error_in_reward_points';
            }

            $template_payment_method = lang('reward_points');

        }
        elseif($payment_method_id == 7) //voucher
        {
            $voucher = $this->input->post('voucher');
            $template_payment_method       = lang('voucher');
            $order_updated_data['voucher'] = $voucher;
        }
        elseif($payment_method_id == 3) //Banks
        {
            $bank_id         = intval($this->input->post('bank_id'));
            $order_status_id = $this->order_status_model->get_status_id($payment_method_id, 'bank_accounts');

            // insert user bank account data
            $bank_accounts_names   = $this->input->post('account_name');
            $bank_accounts_numbers = $this->input->post('account_number');

            foreach($bank_accounts_names as $bank_number=>$user_bank_account_name)
            {
                $this->user_bank_accounts_model->delete_bank_account($bank_number, $order_data->user_id);

                $data    = array(
                                    'user_id'        => $order_data->user_id    ,
                                    'bank_id'        => $bank_number            ,
                                    'account_name'   => $user_bank_account_name ,
                                    'account_number' => $bank_accounts_numbers[$bank_number]
                                );

                $this->user_bank_accounts_model->insert_user_account_data($data);

                $bank_account_name   = $user_bank_account_name;
                $bank_account_number = $bank_accounts_numbers[$bank_number];
            }

            $user_bank_data = $this->user_bank_accounts_model->get_user_bank_data($bank_id, $order_data->user_id);
            $bank_data      = $this->bank_accounts_model->get_bank_data($bank_id, $lang_id);

            $order_bank_name   = $user_bank_data->account_name;
            $order_bank_number = $user_bank_data->account_number;

            $order_updated_data['bank_id']             = $bank_id;
            $order_updated_data['bank_account_name']   = $order_bank_name;
            $order_updated_data['bank_account_number'] = $order_bank_number;
            $order_updated_data['order_status_id']     = $order_status_id;
            $order_updated_data['voucher']             = '';

            $template_payment_method = lang('bank_name')." : ".$bank_data->bank."<br>".lang('bank_account_name')." : ".$bank_data->account_name."<br>".lang('bank_account_number')." : ".$bank_data->account_number;
        }
        else
        {
            $template_payment_method = $this->payment_methods_model->get_payment_method_name($payment_method_id, $lang_id);
        }

        if(count($order_updated_data) != 0)
        {
            $this->orders_model->update_order_data($order_id, $order_updated_data);
        }

        $order_data = $this->orders_model->get_order_data($order_id);

        //$this->_generate_order_serials($order_id, $lang_id, $order_data->country_id);

        if($this->status == 1)
        {
            /// insert order , create notification , Add Affiliate
            $this->_insert_new_order_log($order_data->user_id, $lang_id, $order_id, $order_data->order_status_id, $secret_key, $secret_iv, $order_data->payment_method_id, $template_payment_method);

            if($this->status == 1)
            {
                //send notification
                $products_names = '';
                $status         = $this->order_status_model->get_status_translation_name($order_data->order_status_id, $lang_id);
                $order_products = $this->orders_model->get_order_products($order_id, $lang_id);

                foreach($order_products as $product)
                {
                    $products_names .= $product->title." , ";
                }


                $username      = $user_data->first_name . ' ' . $user_data->last_name;
                $emails[]      = $user_data->email;
                $mobile_number = $user_data->phone;

                $template_data = array(
                                        'username'         => $username ,
                                        'products'         => $products_names ,
                                        'payment_method'   => $template_payment_method ,
                                        'status'           => $status,
                                        'order_time'       => date('Y/m/d H:i', time()),
                                        'order_id'         => $order_id,
                                        'logo_path'        => $this->images_path.$this->config->item('logo'), //base_url().'assets/uploads/'.$this->config->item('logo'),
                                        'user_order_link'  => base_url()."orders/order/view_order_details/".$order_id
                                      );


                $this->notifications->create_notification('new_order_added', $template_data, $emails, $mobile_number);

                $form = $this->_generate_payment_form($payment_method_id, $order_id, $order_data->final_total, $order_data->currency_symbol, $products_names, $user_data->email, $username);
                ///////////////////////////////////////////////////////////////////
                $order_finish_data['admin_not_completed_order'] = 0;
                $this->orders_model->update_order_data($order_id, $order_finish_data);

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

        echo json_encode(array($this->status, $msg, $form));

    }

    private function _generate_order_serials($order_id, $display_lang_id, $country_id)
    {
        $order_data     = $this->orders_model->get_order($order_id);
        $order_products = $this->orders_model->get_order_products($order_id, $display_lang_id);

        foreach($order_products as $row)
        {
            if($row->product_id != 0 && ($row->quantity_per_serial == 1))
            {
                $serials = $this->orders_model->generate_product_serials($row->product_id, $row->qty, $country_id);

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

                    if($order_data->order_status_id == 1)
                    {
                        $serial_status['serial_status'] = 2;
                    }
                    else
                    {
                        $serial_status['serial_status'] = 1;
                    }

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
    private function _insert_new_order_log($user_id, $lang_id, $order_id, $order_status_id, $secret_key, $secret_iv, $payment_method_id, $template_payment_method)
    {
        $order_products = $this->orders_model->get_order_all_products($order_id);

        $order_data     = $this->orders_model->get_order_main_details($order_id, $lang_id);

        if($order_status_id == 1)
        {
            //reward points
            $enc_user_points = $this->admin_bootstrap->get_user_by_id($user_id)->user_points;
            $user_points     = $this->encryption->decrypt($enc_user_points, $secret_key, $secret_iv);

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

            $this->admin_bootstrap->encrypt_and_update_users_data($user_id, 'user_points', $user_total_reward_points);

            // Add Affiliate
            $this->add_affiliate($order_id, $user_id);

            $serials_data = $this->orders_model->get_order_serials($order_id);

            $email_msg = '<table width="100%" border="1" style="font-family: Tahoma, Geneva, sans-serif; font-size: 15px; line-height: 2; text-align: center" >
                        	<tr style="text-align: center; font-weight: bold; font-size: 16px; background: #009; color: #fff;" >
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
                //$img_path       = base_url().'assets/uploads/products/'.$product_data->image; // => Set Image From Server uploads
                $img_path       = $this->images_path.$product_data->image; // =>Set image from Amazon S3 Bucket 

                $secret_key  = $this->config->item('new_encryption_key');
                $secret_iv   = md5('serial_iv');
                $dec_serials = $this->encryption->decrypt($product_serial->serial, $secret_key, $secret_iv);

                $email_msg .= '<tr>
                                	<td><img src="'.base_url().'assets/uploads/products/'.$product_data->image.'" width="50" height="50" style=" display:block; margin:5px auto;" alt="'.$product_name.'"/></td>
                                    <td>'.$product_name.'</td>
                                    <td>'.$dec_serials.'</td>
                               </tr>';

                $sms_msg   .= lang('product').': '.$product_name.'--'.lang('serial').': '.$dec_serials.'***';

            }

            $email_msg .= '</table>';


            $user_data     = $this->admin_bootstrap->get_user_by_id($user_id);
            $emails[]      = $user_data->email;
            $mobile_number = $user_data->phone;
            $template_data = array(
                                    'unix_time'    => time(),
                                    'username'     => $user_data->username,
                                    'status'       => lang('completed'),
                                    'order_id'     => $order_id,
                                    'logo_path'    => $this->images_path.$this->config->item('logo'), //base_url().'assets/uploads/'.$this->config->item('logo'),
                                    'order_time'   => date('Y/m/d H:i', $order_data->unix_time),
                                    'user_order_link'     => base_url()."orders/order/view_order_details/".$order_id,
                                    'order_details_email' => $email_msg,
                                    'order_details_sms'   => $sms_msg,
                                    'payment_method'      => $template_payment_method
                                  );

            $this->notifications->create_notification('direct_pay', $template_data, $emails, $mobile_number);


        }

        else
        {
            $order_status_id = $this->orders_model->get_other_payment_status_id($payment_method_id);
        }


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
        $affliate_user_id = $this->admin_bootstrap->get_user_by_id($user_id)->affiliate_user_id;
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

    private function _generate_payment_form($payment_method_id, $order_id, $final_total, $currency_symbol, $products_names, $email, $customer_name)
    {
        $form = '';
        if($payment_method_id == 4 || $payment_method_id == 8)   //payfort OR Sadad
        {
            //$form = $this->payfort->generate_form($order_id, $final_total, $currency_symbol, $products_names);
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

            $form = $this->payfort->generate_form($order_id, $final_total, $currency_symbol, $payment_option, $email, '', $customer_name);
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
            $form = $this->cashu->fetch_form($order_id, $final_total, $currency_symbol, $products_names);
        }
        else
        {
            $form = '<form method="get" action="'.base_url().'orders/admin_order/view_order/'.$order_id.'" class="pay_form"></form>';

        }
        return $form;
    }

    public function check_product_optional_fields($product_id=0)
    {
        if(isset($_POST['product_id']))
        {
            $product_id = $this->input->post('product_id');
        }


        $optional_fields            = '';
        $product_optional_fields    = $this->products_model->get_product_optional_fields($product_id, $this->data['lang_id']);

        if(count($product_optional_fields) != 0)
        {

            $options_array = array();

            foreach ($product_optional_fields as $field)
            {
                if($field->has_options == 1)
                {
                    $option_options = $this->optional_fields_model->get_optional_field_options($field->optional_field_id, $lang_id);
                    $field->options = $option_options;
                }

                $required       = '';
                $required_span  = '';

                if($field->required == 1)
                {
                    $required       = 'required';
                    $required_span  = " <span class='required'>*</span>";
                }

                $optional_fields .= "<div class='form-group product_fields'>";
                $optional_fields .= " <label class='control-label col-md-3'>$field->label $required_span</label>";
                $optional_fields .= "<div class='col-md-4'>";

                if($field->field_type_id == 2) // radio
                {
                   foreach($field->options as $option)
                   {
                       $optional_fields .= "<label>$option->field_value $required_span</label>";
                       $optional_fields .= '<input type="radio" name="optional_field['. $field->id.']" value="'.$option->id.'"'.$required.'/>';
                   }
                }
                else if($field->field_type_id == 3) //check box
                {
                   foreach($field->options as $option)
                   {
                      $optional_fields .= "<label>$option->field_value $required_span</label>";
                      $optional_fields .= '<input type="checkbox" name="optional_field['.$field->id.']" value=" '. $option->id .'" '. $required .' />';
                   }
                }
                else if($field->field_type_id == 8) //select
                {

                    $optional_fields .= '<select class="select2" name="optional_field[' . $field->id .']" ' . $required .'>';
                    foreach($field->options as $option)
                    {
                       $optional_fields .= '<option value=" '. $option->id .'">'. $option->field_value .'</option>';
                    }

                   $optional_fields .= '</select>';

                }
                else
                {
                    $optional_fields .= '<input name="optional_field['. $field->id .']" '. $required .' type="'. $field->type_name .'" class="form-control" placeholder="'. $field->default_value .'" />';
                }

                $optional_fields .= '</div></div>';

            }
        }

        echo $optional_fields;
    }

    public function check_optional_fields($order_id)
    {
        $product_optional_fields_array = array();
        $order_products = $this->orders_model->get_order_products_data($order_id);

        foreach ($order_products as $product)
        {
            $product_optional_fields    = $this->products_model->get_product_optional_fields($product->product_id, $this->data['lang_id']);

            if(count($product_optional_fields) != 0)
            {
                $options_array = array();

                foreach ($product_optional_fields as $field)
                {
                    if($field->has_options == 1)
                    {
                        $option_options = $this->optional_fields_model->get_optional_field_options($field->optional_field_id, $lang_id);
                        $field->options = $option_options;
                    }

                    $options_array[] = $field;
                }

                $product_optional_fields_array[] = $options_array;
            }
        }

        return $product_optional_fields_array;

    }


    function download($filename)
    {
        $this->load->helper('file');
        $this->load->helper('download');

        $data = file_get_contents(APPPATH. '../assets/uploads/'.urldecode($filename)); // Read the file's contents

        force_download($filename, $data);
    }


    public function get_available_shipping_methods($lang_id)
    {
        $active_array = array();

        if( $this->settings->home_delivery == 1)
        {
            $method_id          = 1;
            $home_delivery      = $this->orders_model->get_shipping_type($method_id, $lang_id);
            $home_delivery_data = (object)array(
                                        'id'    => $method_id,
                                        'name'  => $home_delivery
                                   );
            $active_array[] = $home_delivery_data;
        }

        if( $this->settings->recieve_from_branch == 1)
        {
            $method_id                  = 2;
            $recieve_from_branch        = $this->orders_model->get_shipping_type($method_id, $lang_id);
            $recieve_from_branch_data   = (object)array(
                                                'id'    => $method_id,
                                                'name'  => $recieve_from_branch
                                                );

            $active_array[] = $recieve_from_branch_data;
        }

        if( $this->settings->shipping == 1)
        {
            $method_id     = 3;
            $shipping      = $this->orders_model->get_shipping_type($method_id, $lang_id);
            $shipping_data = (object)array(
                                    'id'    => $method_id,
                                    'name'  => $shipping
                                   );
            $active_array[] = $shipping_data;
        }

        return $active_array;




    }


    public function insert_order_note()
    {
        $order_id = $this->input->post('order_id');
        $note     = strip_tags($this->input->post('admin_note'));

        $order_note_data = array(
                                    'order_id' => $order_id,
                                    'user_id'   => $this->data['user_id'],
                                    'comment'   => $note
                                );
        //$updated_data['admin_note'] = strip_tags($this->input->post('admin_note'));
        $this->orders_model->insert_order_note($order_note_data);

        redirect('orders/admin_order/view_order/'.$order_id, 'refresh');
    }

    public function add_driver()
    {
      $order_id  = intval($this->input->post('order_id', true));
      $driver_id = intval($this->input->post('driver_id', true));

      $updated_data = array('driver_id' => $driver_id);
      $this->orders_model->update_order_data($order_id, $updated_data);

      if(isset($_POST['send_sms']))
      {
        // send sms to driver
        $driver_data = $this->orders_model->get_table_data('users', array('id'=>$driver_id), 'row');
        $msg = lang('store_name').' - '. lang('new_order_added').' #'.$order_id.' '.lang('not_logged_in_order_details_msg');

        $this->notifications->send_sms($msg, $driver_data->phone);
      }

      redirect(base_url().'orders/admin_order/view_order/'.$order_id, 'refresh');
    }

    public function update_return_status()
    {
      $order_id   = intval($this->input->post('order_id', true));
      $order_product_id = intval($this->input->post('order_product_id', true));
      $status_id  = intval($this->input->post('status_id', true));
      $note       = strip_tags($this->input->post('note', true));

      $conds = array('id' => $order_product_id);
      $return_product_data = $this->orders_model->get_table_data('orders_products', $conds, 'row');

      if($return_product_data->return_status == 1)
      {
        //update product return status
        $product_data = array(
          'return_status' => $status_id
        );
        $product_conds = array(
          'id' => $order_product_id,
          'order_id' => $order_id,
        );

        if($this->orders_model->update_table_data('orders_products', $product_conds, $product_data))
        {
          $return_qty = $return_product_data->returned_qty;

          //insert return log
          $log_data = array(
            'order_id'         => $order_id         ,
            'status_id'        => $status_id        ,
            'order_product_id' => $order_product_id ,
            'returned_qty'     => $return_qty       ,
            'notes'            => $note             ,
            'added_by'         => $this->data['user_id'],
            'unix_time'        => time()
          );

          $this->orders_model->insert_table_data('orders_return_log', $log_data);


          /**
           * Mrzok Updates - 6/2021
           * if return request is accepted , update product serial state to be enabled in store for selling again automatically
           */
          $return_to_stock = isset( $_POST['return_to_sell_stock'])? $_POST['return_to_sell_stock']:0;// will be st to catched from post
          $order_country_id  = intval($this->input->post('order_country_id', true));
          
          if($status_id == 2 && $return_to_stock) // Retuen Accepted - and allowed to be returned to stock
          {
              $return_product_serials = $this->orders_model->get_admin_product_serials($return_product_data->product_id, $order_id);
              
              if(count($return_product_serials) > 0 ){
                  $return_product_serials = array_slice($return_product_serials , 0 , $return_qty );
                  foreach($return_product_serials as $serial)
                  {
                      // Update serial status
                      $serial_data['serial_status'] = 0;
                      $serial_data['sold_order_id'] = 0;
                      $this->products_serials_model->update_serial($serial->product_serial_id, $serial_data);

                      // delete serial from orders_serials table
                      $this->orders_model->delete_order_serial_data($serial->product_serial_id, $order_id, $return_product_data->product_id);
                  }

                  // Update product serials count for a country status
                  $product_qty = $this->products_model->count_product_available_quantity($return_product_data->product_id, $order_country_id);
                  $product_updated_data['product_quantity'] = $product_qty;
                
                  $this->products_model->update_product_countries($return_product_data->product_id, $order_country_id, $product_updated_data);

              }
              
          }
        }
      }

      redirect(base_url().'orders/admin_order/view_order/'.$order_id, 'refresh');

    }


/************************************************************************/
}
