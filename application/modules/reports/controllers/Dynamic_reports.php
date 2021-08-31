<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Dynamic_reports extends CI_Controller
{
    public $lang_row;
    public $country_id;
    public $payment_id;
    public $user_id;
    public $customer_group_id;
    public $cat_id;
    public $coupon_id;
    public $order_id_from;
    public $order_id_to;
    public $date_from;
    public $date_to;
    public $status_date_from;
    public $status_date_to;
    public $order_status_id;
    public $user_email_id;
    public $user_phone_id;
    public $user_ip_address_id;

    public $user_groups_ids;
    public $settings;

    public function __construct()
    {
        parent::__construct();

        require(APPPATH . 'includes/global_vars.php');

        $this->load->library('pagination');
        $this->load->library('PHPExcel');
        $this->load->library('currency');
        $this->load->library('pagination');

        $this->load->model('settings/admin_model');
        $this->load->model('orders/orders_model');
        $this->load->model('orders/order_status_model');
        $this->load->model('categories/cat_model');
        $this->load->model('products/products_model');
        $this->load->model('stores/stores_model');
        $this->load->model('users/user_model');
        $this->load->model('users/customer_groups_model');
        $this->load->model('coupon_codes/coupon_codes_model');
        $this->load->model('payment_options/bank_accounts_model');
        $this->load->model('payment_options/payment_methods_model');

        $this->lang_row = $this->admin_bootstrap->get_active_language_row();

       // $this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
       // $this->output->set_header('Pragma: no-cache');
       // $this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

       $user_groups     = $this->ion_auth->get_users_groups($this->data['user']->id)->result();

       $user_groups_ids = array();

        foreach ($user_groups as $group)
        {
            $this->user_groups_ids[] = $group->id;
        }

        $this->settings = $this->admin_model->get_settings_general_data();

    }

    private function _js_and_css_files()
    {

        $this->data['css_files'] = array(
            'global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css',
            'global/plugins/fullcalendar/fullcalendar.min.css',
            'global/plugins/clockface/css/clockface.css',
            'global/plugins/bootstrap-datepicker/css/datepicker3.css',
            'global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css',
            'global/plugins/bootstrap-colorpicker/css/colorpicker.css',
            'global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css',
            'global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css',
            'global/plugins/jquery-tags-input/jquery.tagsinput.css'
        );

        $this->data['js_files'] = array(

            //Date Range Picker
            'global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js',
            'global/plugins/bootstrap-daterangepicker/daterangepicker.js',
            'global/plugins/bootstrap-daterangepicker/moment.min.js',
            'pages/scripts/components-pickers.js',
            'pages/scripts/components-form-tools.js',

            //touch spin
            'global/plugins/fuelux/js/spinner.min.js',
            'global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js',
            'global/plugins/bootstrap-touchspin/bootstrap.touchspin.js',

            //Tags
            'tags/tag-it.js'
        );


        $this->data['js_code'] = 'ComponentsPickers.init();
        ';
    }


    public function index($page_id = 1)
    {
        $this->_js_and_css_files();
        $lang_id = $this->data['active_language']->id;

        $this->data['columns'] = array(
                                         '',
                                         lang('year')               ,
                                         lang('month')              ,
                                         lang('orders')             ,
                                         lang('customers')          ,
                                         lang('products')           ,
                                         lang('reward_points')      ,
                                         lang('final_total')        ,
                                         ('orders_sub_total')       ,
                                         lang('total_discount')     ,
                                         lang('coupon_discount')    ,
                                         lang('products_cost')      ,
                                         lang('total_profit')       ,
                                         lang('profit')." [%]"
                                      );

        $countries       = $this->countries_model->get_countries_filter($lang_id);
        $payment_options = $this->payment_methods_model->get_payment_options($lang_id);
        $customer_groups = $this->customer_groups_model->get_customer_groups($lang_id);
        $categories      = $this->cat_model->get_categories($lang_id);
        $order_status    = $this->order_status_model->get_all_statuses($lang_id);
        $products        = array();//$this->products_model->get_products_names($lang_id);
        $stores          = $this->stores_model->get_stores_data($lang_id);




        /*foreach ($user_groups as $g){
            $groupids[] = $g->id;
        }
        //print_r($groupids); exit;
        if(!in_array('1',$groupids)){
            redirect(base_url().'AmmarFiras4ever8384/dashboard');
            exit;
        }
*/
        $represetitive = false;

        if(in_array($this->settings->rep_group_id, $this->user_groups_ids))
        {
            $represetitive = true;
        }

        $this->data['represetitive'] = $represetitive;

        $countries_array = array();
        $groups_array    = array();
        $cats_array      = array();
        $coupon_array    = array();
        $status_array    = array();
        $users_array     = array();
        $emails_array    = array();
        $phones_array    = array();
        $ip_addresses    = array();
        $products_array  = array();
        $payment_array   = array();
        $stores_array    = array();

        $countries_array[0] = '-----------------';
        $groups_array[0]    = '-----------------';
        //$cats_array[0]      = '-----------------';
        $coupon_array[0]    = '-----------------';
        $status_array[0]    = '--------';
        $users_array[0]     = '-----------------';
        $emails_array[0]    = '-----------------';
        $phones_array[0]    = '-----------------';
        $ip_addresses[0]    = '-----------------';
        $products_array[0]  = '-----------------';
        $payment_array[0]   = '-----------------';
        $stores_array[0]    = '-----------------';

        foreach($countries as $country)
        {
            $countries_array[$country->id] = $country->name;
        }

        foreach($customer_groups as $group)
        {
            $groups_array[$group->id] = $group->title;
        }

        foreach($categories as $cat)
        {
            if($cat->parent_id == 0)
            {
                foreach($categories as $category)
                {
                    if($category->parent_id == $cat->id)
                    {
                        $cats_array["{$cat->name}"][$category->id] = $category->name;
                    }
                }
            }
        }


        /*foreach($coupon_codes as $coupon)
        {
            $coupon_array[$coupon->id] = $coupon->name;
        }
        */
        foreach($order_status as $status)
        {
            $status_array[$status->id] = $status->name;
        }

        /*foreach($users_data as $user)
        {
            //$users_array[$user->id]  = $user->first_name.' '.$user->last_name;
            $emails_array[$user->id] = '';//$user->email;
            $phones_array[$user->id] = '';//$user->phone;
            $ip_addresses[$user->id] = '';//$user->ip_address;
        }
        */
        /*foreach($products as $product)
        {
            $products_array[$product->product_id] = $product->title;
        }*/

        foreach($payment_options as $option)
        {
            $payment_array[$option->id] = $option->name;
        }

        foreach($stores as $row)
        {
            $stores_array[$row->id] = $row->name;
        }

        $this->data['countries']       = $countries_array;
        $this->data['payment_options'] = $payment_array;
        $this->data['customer_groups'] = $groups_array;
        $this->data['categories']      = $cats_array;
        //$this->data['coupon_codes']    = $coupon_array;
        $this->data['order_status']    = $status_array;
        $this->data['stores']          = $stores_array;
        //$this->data['products']        = $products_array;


        //////////////////////////////////////////////////////////

        if(isset($_POST['user_id']))
        {
            $user_name = $this->input->post('user_id');

            $user_name_array    = explode("-",$user_name);
            $first_name         = $user_name_array[0];
            $last_name          = $user_name_array[1];

            $user_id = $user_name_array[2];
        }
        else
        {
            $user_id = 0;
        }

        if(isset($_POST['user_phone_id']))
        {
            $user_phone         = $this->input->post('user_phone_id');
            $user_phone_array   = explode("/",$user_phone);

            $user_phone_id      = $user_phone_array[1];
        }
        else
        {
            $user_phone_id = 0;
        }

        if(isset($_POST['user_ip_address_id']))
        {
            $user_ip_address    = $this->input->post('user_ip_address_id');
            $user_ip_address_array = explode("/",$user_ip_address);

            $user_ip_address_id = $user_ip_address_array[1];
        }
        else
        {
            $user_ip_address_id = 0;
        }

        if(isset($_POST['user_email_id']) && $_POST['user_email_id'] != '')
        {
            $user_email     = $this->input->post('user_email_id');
            $user_email_id  = $this->user_model->get_user_id_by_email($user_email);
        }
        else
        {
            $user_email_id = 0;
        }

        if(isset($_POST['country_id']))
        {
            $country_id = intval($this->input->post('country_id'));
        }
        else
        {
            $country_id = 0;
        }


        if(isset($_POST['payment']))
        {
            $payment_id = intval($this->input->post('payment'));
        }
        else
        {
            $payment_id = 0;
        }



        if(isset($_POST['customer_group_id']))
        {
            $customer_group_id = intval($this->input->post('customer_group_id'));
        }
        else
        {
            $customer_group_id = 0;
        }

        if(isset($_POST['cat_id']))
        {
            $cat_id = $this->input->post('cat_id');
        }
        else
        {
            $cat_id = array();
        }

        if(isset($_POST['coupon_id']))
        {
            $coupon = $this->input->post('coupon_id');

            $coupon_post_array  = explode("/", $coupon);
            $coupon_id          = $coupon_post_array[1];
        }
        else
        {
            $coupon_id = 0;
        }

        if(isset($_POST['order_id_from']))
        {
            $order_id_from = intval($this->input->post('order_id_from'));
        }
        else
        {
            $order_id_from = 0;
        }

        if(isset($_POST['order_id_to']))
        {
            $order_id_to = intval($this->input->post('order_id_to'));
        }
        else
        {
            $order_id_to = 0;
        }

        if(isset($_POST['date_from']))
        {
            $date_from = strtotime($this->input->post('date_from'));
        }
        else
        {
            $date_from = 0;
        }

        if(isset($_POST['date_to']))
        {
            $date_to = strtotime($this->input->post('date_to'));
        }
        else
        {
            $date_to = 0;
        }

        if(isset($_POST['status_date_from']))
        {
            $status_date_from = strtotime($this->input->post('status_date_from'));
        }
        else
        {
            $status_date_from = 0;
        }

        if(isset($_POST['status_date_to']))
        {
            $status_date_to = strtotime($this->input->post('status_date_to'));
        }
        else
        {
            $status_date_to = 0;
        }

        if(isset($_POST['status_id']))
        {
            $order_status_id = intval($this->input->post('status_id'));
        }
        else
        {
            $order_status_id = 0;
        }

        if(isset($_POST['store_id']))
        {
            $store_id = intval($this->input->post('store_id', true));
        }
        else
        {
            $store_id = 0;
        }



        if(isset($_POST['report_details']))
        {
            $report_details = intval($this->input->post('report_details'));
        }
        else
        {
            $report_details = 0;
        }

        $perPage = 4;
        $offset  = ($page_id -1 ) * $perPage;

        if($offset < 0)
        {
            $offset = 0;
        }

        $config['base_url']         = base_url()."reports/dynamic_reports/index/";
        $config['per_page']         = $perPage;
        $config['first_link']       = FALSE;
        $config['last_link']        = FALSE;
        $config['uri_segment']      = 4;
        $config['use_page_numbers'] = TRUE;

        $config['total_rows']       = $this->orders_model->get_count_filtered_reports($lang_id, $country_id, $payment_id, $user_id, $user_email_id, $user_phone_id, $user_ip_address_id, $customer_group_id, $cat_id, $coupon_id, $order_id_from, $order_id_to, $date_from, $date_to, $status_date_from, $status_date_to, $order_status_id, $store_id);

        $this->pagination->initialize($config);


        $orders_data             = $this->orders_model->get_orders_filtered_data($lang_id, $country_id, $payment_id, $user_id, $user_email_id, $user_phone_id, $user_ip_address_id, $customer_group_id, $cat_id, $coupon_id, $order_id_from, $order_id_to, $date_from, $date_to, $status_date_from, $status_date_to, $order_status_id, $perPage, $offset, $store_id);
        $default_currency_symbol = $this->currency->get_default_country_symbol();

        $db_columns  = array(
                              'id'                  ,
                              'details_icon'        ,
                              'year'                ,
                              'month'               ,
                              'orders_count'        ,
                              'users_count'         ,
                              'products_count'      ,
                              'total'               ,
                              'orders_sub_total'    ,
                              'total_discount'      ,
                              'reward_points'       ,
                              'coupons_discount'    ,
                              'products_cost'       ,
                              'total_profit'        ,
                              'profit_percent'
                            );


        $this->data['hidden_fields'] = array('id');
        $new_grid_data               = array();

        $pdf_body                 = '';
        $pdf_no_details_body      = '';
        $pdf_product_details_body = '';

        if(count($orders_data) != 0)
        {
            foreach($orders_data as $key =>$row)
            {
                $month = $row->month;
                $year  = $row->year;

                $month_orders_data      = $this->orders_model->get_month_orders($month, $year, $country_id, $payment_id, $user_id, $user_email_id, $user_phone_id, $user_ip_address_id, $customer_group_id, $cat_id, $coupon_id, $order_id_from, $order_id_to, $date_from, $date_to, $status_date_from, $status_date_to, $order_status_id, $store_id);

                $purchased_cost         = 0;
                $total_coupons          = 0;
                $reward_points          = 0;
                $orders_total_amount    = 0;
                $orders_total_discount  = 0;
                $charge_cards_total     = 0;
                $orders_total_coupons   = 0;
                $total_discounts        = 0;
                $total_coupons_array    = array();
                $total_discount_array   = array();
                $order_total_coupon     = 0;

                $order_sub_total_amount = 0;

                foreach($month_orders_data as $order)
                {
                    if($order->product_id != 0)
                    {
                        $products_total         = $order->price * $order->qty;
                        $order_price            = $this->_amount_with_default_currency($products_total, $order->country_id);

                        $orders_total_amount   += $order_price;
                    }
                    else
                    {
                        $charge_cards_total += $order->price;
                    }

                    $order_sub_total_amount += $orders_total_amount + $charge_cards_total;

                    $cost = $order->purchased_cost;

                    $total_coupons_array[$order->country_id][$order->id] = $order->order_coupon_discount;
                    $total_discount_array[$order->country_id][$order->id] = $order->orders_discount;

                    $purchased_cost         += $cost;
                    $reward_points          += $order->reward_points;

                }

                foreach ($total_coupons_array as $country_id=>$amount_array)
                {
                    foreach ($amount_array as $amount)
                    {
                        $order_coupon          = $this->_amount_with_default_currency($amount, $country_id);
                        $orders_total_coupons += $order_coupon;
                    }
                }

                foreach ($total_discount_array as $country_id=>$amount_array)
                {
                    foreach ($amount_array as $amount)
                    {
                        $order_discount         = $this->_amount_with_default_currency($amount, $country_id);
                        $orders_total_discount += $order_discount;
                    }
                }



                $profit = $orders_total_amount - ($purchased_cost + $orders_total_coupons + $orders_total_discount);

                foreach($db_columns as $column)
                {

                    $total_profit = round($profit, 2);

                    $order_details_array  = array();
                    $products_array       = array();
                    $customers_array      = array();

                    if($column == 'coupons_discount')
                    {
                        $new_grid_data[$key][$column]   = $orders_total_coupons.' '.$default_currency_symbol;
                    }

                    else if($column == 'total')
                    {
                        $new_grid_data[$key][$column] = ($orders_total_amount) .' '.$default_currency_symbol;
                    }

                    elseif($column == 'orders_sub_total')
                    {
                        $new_grid_data[$key][$column] = $order_sub_total_amount;
                    }

                    else if($column == 'total_discount')
                    {
                        $new_grid_data[$key][$column] = $orders_total_discount.' '.$default_currency_symbol;
                    }

                    else if($column == 'total_profit')
                    {
                        $new_grid_data[$key][$column] = $total_profit.' '.$default_currency_symbol;
                    }

                    else if($column == 'profit_percent')
                    {
                        $profit_percent = 0;
                        if($orders_total_amount != 0)
                        {
                            $profit_percent = ($total_profit / ($orders_total_amount - $orders_total_coupons - $orders_total_discount )) * 100;
                        }

                        $profit_percent = round($profit_percent, 2);

                        $new_grid_data[$key][$column] = $profit_percent;
                    }

                    else if($column == 'details_icon')
                    {
                        $new_grid_data[$key][$column] = '<a href="#" data-year="'.$row->year.'" data-month="'.$row->month.'" class="details_icon"><img src="'.base_url().'assets/template/admin/icons/plus.png" /></a>';
                    }

                    else if($column == 'reward_points')
                    {
                        $new_grid_data[$key][$column] = $reward_points;
                    }

                    else if($column == 'purchased_cost')
                    {
                        //$cost = $this->_amount_with_default_currency($purchased_cost, $row->country_id);
                        $new_grid_data[$key][$column] = $purchased_cost.' '.$default_currency_symbol;
                    }

                    else if($column == 'products_cost')
                    {
                        //$cost = $this->_amount_with_default_currency($purchased_cost, $row->country_id);
                        $new_grid_data[$key][$column] = $purchased_cost.' '.$default_currency_symbol;
                    }

                    else
                    {
                        $field = $row->{$column};
                        $new_grid_data[$key][$column] = $field;
                    }

                    $orders_details   = array();
                    $products_details = array();
                    $customer_details = array();

                    if($report_details == 1 || $report_details == 4)
                    {
                        $orders_details = $month_orders_data;
                    }
                    if($report_details == 2 || $report_details == 4)
                    {
                        $products_details = $products_array;
                    }
                    if($report_details == 3 || $report_details == 4)
                    {
                        $customer_details = $customers_array;
                    }

                }



            }//die();// end order data loop

        }


        $this->data['data']       = $new_grid_data;

        $this->data['pagination'] = $this->pagination->create_links();

        if(isset($_POST['details_type']) && $_POST['details_type'] == 'pdf')
        {
            $html = $this->load->view('dynamic_report_view_pdf', $this->data, true);

            $this->load->library('mpdf/mpdf');
            ob_clean();

    	    $mpdf=new mPDF('utf-8','A4','','',20,15,48,25,10,10);

            $mpdf->allow_charset_conversion=true;  // Set by default to TRUE
            $mpdf->charset_in='UTF-8';

            $stylesheet  = file_get_contents(base_url().'assets/template/admin/global/plugins/bootstrap/css/bootstrap.min.css'); // external css
            $stylesheet2 = file_get_contents(base_url().'assets/template/admin/global/css/components.css'); // external css
            //$stylesheet3 = file_get_contents(base_url().'assets/template/admin/global/css/plugins.css'); // external css
            $stylesheet4 = file_get_contents(base_url().'assets/template/admin/layout/css/layout.css'); // external css

            $stylesheet6 = file_get_contents(base_url().'assets/template/admin/layout/css/custom.css'); // external css

            $mpdf->WriteHTML($stylesheet, 1);
            $mpdf->WriteHTML($stylesheet2, 1);
            //$mpdf->WriteHTML($stylesheet3, 1);
            $mpdf->WriteHTML($stylesheet4, 1);

            $mpdf->WriteHTML($stylesheet6, 1);
            $mpdf->WriteHTML($html, 2);


            $mpdf->Output(date('Y/m/d').'.pdf', "D");
        }
        elseif(isset($_POST['details_type']) && $_POST['details_type'] == 'excel')
        {
            $this->export_excel($new_grid_data, $page_id);
        }



        //////////////////////////////////////////////////////////////////////

        $this->data['content'] = $this->load->view('dynamic_report_view', $this->data, true);

        $this->load->view('Admin/main_frame',$this->data);
    }


    public function get_month_orders($page = 1)
    {
        $year    = $this->input->post('year');
        $month   = $this->input->post('month');
        $lang_id = $this->data['active_language']->id;

        if(isset($_POST['user_id']) && $_POST['user_id'] != '')
        {
            $user_name = $this->input->post('user_id');

            $user_name_array    = explode("-",$user_name);
            $first_name         = $user_name_array[0];
            $last_name          = $user_name_array[1];

            $user_id = $user_name_array[2];
        }
        else
        {
            $user_id = 0;
        }

        if(isset($_POST['user_phone_id']) && $_POST['user_phone_id'] != '')
        {
            $user_phone         = $this->input->post('user_phone_id');
            $user_phone_array   = explode("/",$user_phone);

            $user_phone_id      = $user_phone_array[1];
        }
        else
        {
            $user_phone_id = 0;
        }

        if(isset($_POST['user_ip_address_id']) && $_POST['user_ip_address_id'] != '')
        {
            $user_ip_address    = $this->input->post('user_ip_address_id');
            $user_ip_address_array = explode("/",$user_ip_address);

            $user_ip_address_id = $user_ip_address_array[1];
        }
        else
        {
            $user_ip_address_id = 0;
        }

        if(isset($_POST['user_email_id']) && $_POST['user_email_id'] != '')
        {
            $user_email     = $this->input->post('user_email_id');
            $user_email_id  = $this->user_model->get_user_id_by_email($user_email);
        }
        else
        {
            $user_email_id = 0;
        }

        if(isset($_POST['country_id']))
        {
            $country_id = intval($this->input->post('country_id'));
        }
        else
        {
            $country_id = 0;
        }


        if(isset($_POST['payment']))
        {
            $payment_id = intval($this->input->post('payment'));
        }
        else
        {
            $payment_id = 0;
        }



        if(isset($_POST['customer_group_id']))
        {
            $customer_group_id = intval($this->input->post('customer_group_id'));
        }
        else
        {
            $customer_group_id = 0;
        }

        if(isset($_POST['cat_id']))
        {
            $cat_id = $this->input->post('cat_id');
        }
        else
        {
            $cat_id = array();
        }

        if(isset($_POST['coupon_id']) && $_POST['coupon_id'] != '')
        {
            $coupon = $this->input->post('coupon_id');

            $coupon_post_array  = explode("/", $coupon);
            $coupon_id          = $coupon_post_array[1];
        }
        else
        {
            $coupon_id = 0;
        }

        if(isset($_POST['order_id_from']))
        {
            $order_id_from = intval($this->input->post('order_id_from'));
        }
        else
        {
            $order_id_from = 0;
        }

        if(isset($_POST['order_id_to']))
        {
            $order_id_to = intval($this->input->post('order_id_to'));
        }
        else
        {
            $order_id_to = 0;
        }

        if(isset($_POST['date_from']))
        {
            $date_from = strtotime($this->input->post('date_from'));
        }
        else
        {
            $date_from = 0;
        }

        if(isset($_POST['date_to']))
        {
            $date_to = strtotime($this->input->post('date_to'));
        }
        else
        {
            $date_to = 0;
        }

        if(isset($_POST['status_date_from']))
        {
            $status_date_from = strtotime($this->input->post('status_date_from'));
        }
        else
        {
            $status_date_from = 0;
        }

        if(isset($_POST['status_date_to']))
        {
            $status_date_to = strtotime($this->input->post('status_date_to'));
        }
        else
        {
            $status_date_to = 0;
        }

        if(isset($_POST['order_status_id']))
        {
            $order_status_id = intval($this->input->post('order_status_id'));
        }
        else
        {
            $order_status_id = 0;
        }

        if(isset($_POST['store_id']))
        {
            $store_id = intval($this->input->post('store_id'));
        }
        else
        {
            $store_id = 0;
        }
//echo '<pre>'; print_r($_POST);
//echo $store_id;die();
        //pagination
        $limit   = 3;
        $offset  = ($page -1) * $limit;


        $config['base_url']         = base_url().'reports/dynamic_reports/get_month_orders/';
        $config['total_rows']       = $this->orders_model->get_year_month_orders_count($month, $year, $lang_id, $country_id, $payment_id, $user_id, $user_email_id, $user_phone_id, $user_ip_address_id, $customer_group_id, $cat_id, $coupon_id, $order_id_from, $order_id_to, $date_from, $date_to, $status_date_from, $status_date_to, $order_status_id, $store_id);
        //print_r($config);die();
        $config['per_page']         = $limit;
        $config['uri_segment']      = 4;
        $config['use_page_numbers'] = TRUE;
        $config['attributes']       = array('class' => 'orders_details');

        $config['first_link']       = lang('first_page');
        $config['last_link']        = lang('last_page');
        $config['first_tag_open']   = '<li>';
        $config['first_tag_close']  = '</li>';
        $config['last_tag_open']    = '<li>';
        $config['last_tag_close']   = '</li>';
        $config['next_tag_open']    = '<li>';
        $config['next_tag_close']   = '</li>';
        $config['prev_tag_open']    = '<li>';
        $config['prev_tag_close']   = '</li>';
        $config['num_tag_open']     = '<li>';
        $config['num_tag_close']    = '</li>';
        $config['cur_tag_open']     = '<li><strong>';
        $config['cur_tag_close']    = '</strong></li>';

        $config['display_pages']    = TRUE;

        $this->pagination->initialize($config);

        $this->data['page_links']   = $this->pagination->create_links();


        $orders_data                = $this->orders_model->get_year_month_orders($month, $year, $lang_id, $country_id, $payment_id, $user_id, $user_email_id, $user_phone_id, $user_ip_address_id, $customer_group_id, $cat_id, $coupon_id, $order_id_from, $order_id_to, $date_from, $date_to, $status_date_from, $status_date_to, $order_status_id, $limit, $offset, $store_id);
        $default_currency_symbol    = $this->currency->get_default_country_symbol();

        $represetitive = false;

        if(in_array($this->settings->rep_group_id, $this->user_groups_ids))
        {
            $represetitive = true;
        }

        $table = '<div class="dr_orders_details_'.$year.'_'.$month.'">
                    <table class="table table-bordered table-hover ">
                    <thead>
                        <tr>
                            <th scope="col">'.lang('order_id').'</th>
                            <th scope="col">'.lang('username').'</th>
                            <th scope="col">'.lang('country').'</th>
                            <th scope="col">'.lang('status').'</th>
                            <th scope="col">'.lang('purchased_products').'</th>
                            <th scope="col">'.lang('payment_method').'</th>
                            <th scope="col">'.lang('reward_points').'</th>
                            <th scope="col">'.lang('total').'</th>';
        if(!$represetitive)
        {
            $table .= '<th scope="col">'.lang('products_cost').'</th>
                            <th scope="col">'.lang('recharge_card').'</th>
                            <th scope="col">'.lang('total_discount').'</th>
                            <th scope="col">'.lang('coupon_discount').'</th>
                            <th scope="col">'.lang('total_profit').'</th>
                            <th scope="col">'.lang('profit').'[%]'.'</th>
                            <th scope="col">'.lang('tax').'</th>
                            <th scope="col">'.lang('final_total').'</th>
                        </tr>
                    </thead>
                    <tbody>';
        }




        foreach($orders_data as $order)
        {
            $order_products = $this->orders_model->get_order_products($order->id, $lang_id);

            $table .='<tr>
                        <td>'.'<a href="'.base_url().'orders/admin_order/view_order/'.$order->id.'" target="_blank">'.$order->id.'</a>'.'</td>
                        <td>'.$order->first_name.' '.$order->last_name.'</td>
                        <td>'.$order->country.'</td>
                        <td>'.$order->status.'</td>';
            $products = '';

            $purchased_cost         = 0;
            $charge_cards_total     = 0;
            $products_total_price   = 0;
            $reward_points          = 0;

            foreach($order_products as $product)
            {
                if($product->product_id == 0)
                {
                    $products .= $product->price.' '.lang('recharge_card')."\n";

                    $charge_cards_total += $product->price;
                }
                else
                {

                    $products .= $product->qty.' * '.$product->title."\n";

                    $cost                = $product->purchased_cost ;//* $product->qty;
                    $product_final_price = $product->final_price * $product->qty;
                    $product_details     = $this->products_model->get_products_row($product->product_id);

                    //if product quantity per serial = 1 , cost will be with default currency
                    /*if($product_details->quantity_per_serial != 0)
                    {
                        //$cost = $this->currency->get_amount_with_default_currency($cost, $order->country_id);
                    }*/


                    $purchased_cost       += $cost;
                    $products_total_price += $product_final_price;
                    $reward_points        += $product->reward_points;

                }
            }

            $order_total_coupon = 0;
            $profit_percent     = 0;

            if($order->coupon_discount > 0)
            {
                $is_total_coupon = $this->_check_order_coupon_discount_type($order->id);

                if($is_total_coupon)
                {
                    $order_total_coupon = $order->coupon_discount;
                    $order_total_coupon = $this->_amount_with_default_currency($order_total_coupon, $order->country_id) ;
                }
            }

            $total_with_default_currency        = $this->_amount_with_default_currency($order->total, $order->country_id) ;
            $discount_with_default_currency     = $this->_amount_with_default_currency($order->discount, $order->country_id) ;
            $coupon_with_default_currency       = $this->_amount_with_default_currency($order->coupon_discount, $order->country_id) ." ". $default_currency_symbol;
            $tax_with_default_currency          = $this->_amount_with_default_currency($order->tax, $order->country_id);
            $final_with_default_currency        = $this->_amount_with_default_currency($products_total_price, $order->country_id);
            $cost_with_default_currency         = $purchased_cost;
            $cards_with_default_currency        = $this->_amount_with_default_currency($charge_cards_total, $order->country_id);
            $total_with_tax_default_currency    = $this->_amount_with_default_currency($order->final_total, $order->country_id);

            /**
             * $final_with_default_currency = total products final price (substract(-) coupons of type product or category)
             * $purchased_cost              = total products cost
             * $order_total_coupon          = if order has coupon of type total, its added to calculate profit
             */


            $profit       = $final_with_default_currency - ($purchased_cost + $order_total_coupon);
            $total_profit = $profit ;

            $order_total  = $total_with_default_currency - $cards_with_default_currency;
            if($final_with_default_currency != 0)
            {
                $profit_percent = round(($total_profit / ($final_with_default_currency - $order_total_coupon )) * 100 , 2) . ' % ';
            }

            $table .= '<td>'.$products.'</td>
                       <td>'.$order->payment_method.'</td>
                       <td>'.$reward_points.'</td>
                       <td>'.$total_with_default_currency . " " . $default_currency_symbol.'</td>';

            if(!$represetitive)
            {
                $table .= '<td>'.$cost_with_default_currency . " " . $default_currency_symbol.'</td>
                       <td>'.$cards_with_default_currency . " " . $default_currency_symbol.'</td>
                       <td>'.$discount_with_default_currency . " " . $default_currency_symbol.'</td>
                       <td>'.$coupon_with_default_currency . '</td>
                       <td>'.$total_profit . " " . $default_currency_symbol.'</td>
                       <td>'.$profit_percent . '</td>
                       <td>'.$tax_with_default_currency . " " . $default_currency_symbol.'</td>
                       <td>'.$total_with_tax_default_currency . " " . $default_currency_symbol.'</td>

                     </tr>';
            }

        }

        $table .= '</tbody>
                   </table>';

        if(count($config['total_rows']) != 0)
        {
            $table .= '<ul class="pagination">' . $this->data['page_links'] . '</ul>';
            $table .= '</div>';
        }

        echo $table;
    }

    private function _check_order_coupon_discount_type($order_id)
    {
        $coupon_data = $this->orders_model->get_order_coupon_data($order_id);

        if(count($coupon_data) != 0)
        {
            if($coupon_data->product_or_category == 'total')
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    public function export_excel($grid_data, $page_id=1)
    {
        $lang_id = $this->data['active_language']->id;

        if(isset($_POST['country_id']))
        {
            $country_id = intval($this->input->post('country_id'));
        }
        else
        {
            $country_id = 0;
        }


        if(isset($_POST['payment']))
        {
            $payment_id = intval($this->input->post('payment'));
        }
        else
        {
            $payment_id = 0;
        }

        if(isset($_POST['user_id']))
        {
            $user_id = intval($this->input->post('user_id'));
        }
        else
        {
            $user_id = 0;
        }

        if(isset($_POST['customer_group_id']))
        {
            $customer_group_id = intval($this->input->post('customer_group_id'));
        }
        else
        {
            $customer_group_id = 0;
        }

        if(isset($_POST['cat_id']))
        {
            $cat_id = $this->input->post('cat_id');
        }
        else
        {
            $cat_id = 0;
        }

        if(isset($_POST['coupon_id']))
        {
            $coupon_id = intval($this->input->post('coupon_id'));
        }
        else
        {
            $coupon_id = 0;
        }

        if(isset($_POST['order_id_from']))
        {
            $order_id_from = intval($this->input->post('order_id_from'));
        }
        else
        {
            $order_id_from = 0;
        }

        if(isset($_POST['order_id_to']))
        {
            $order_id_to = intval($this->input->post('order_id_to'));
        }
        else
        {
            $order_id_to = 0;
        }

        if(isset($_POST['date_from']))
        {
            $date_from = strtotime($this->input->post('date_from'));
        }
        else
        {
            $date_from = 0;
        }

        if(isset($_POST['date_to']))
        {
            $date_to = strtotime($this->input->post('date_to'));
        }
        else
        {
            $date_to = 0;
        }

        if(isset($_POST['status_date_from']))
        {
            $status_date_from = strtotime($this->input->post('status_date_from'));
        }
        else
        {
            $status_date_from = 0;
        }

        if(isset($_POST['status_date_to']))
        {
            $status_date_to = strtotime($this->input->post('status_date_to'));
        }
        else
        {
            $status_date_to = 0;
        }

        if(isset($_POST['status_id']))
        {
            $order_status_id = intval($this->input->post('status_id'));
        }
        else
        {
            $order_status_id = 0;
        }

        if(isset($_POST['user_email_id']))
        {
            $user_email_id = intval($this->input->post('user_email_id'));
        }
        else
        {
            $user_email_id = 0;
        }

        if(isset($_POST['user_phone_id']))
        {
            $user_phone_id = intval($this->input->post('user_phone_id'));
        }
        else
        {
            $user_phone_id = 0;
        }

        if(isset($_POST['user_ip_address_id']))
        {
            $user_ip_address_id = intval($this->input->post('user_ip_address_id'));
        }
        else
        {
            $user_ip_address_id = 0;
        }

        if(isset($_POST['report_details']))
        {
            $report_details = intval($this->input->post('report_details'));
        }
        else
        {
            $report_details = 0;
        }

        if(isset($_POST['store_id']))
        {
            $store_id = intval($this->input->post('store_id'));
        }
        else
        {
            $store_id = 0;
        }


        $perPage = 10;
        $offset  = ($page_id -1 ) * $perPage;

        if($offset < 0)
        {
            $offset = 0;
        }

        $this->excel = new PHPExcel();
        //activate worksheet number 1
        $this->excel->setActiveSheetIndex(0);
        //name the worksheet
        $this->excel->getActiveSheet()->setTitle('dynamic-report');

        $chars_array = range('A', 'Z');

        $represetitive = false;

        if(in_array($this->settings->rep_group_id, $this->user_groups_ids))
        {
            $represetitive = true;
        }

        $default_currency_symbol = $this->currency->get_default_country_symbol();

        $i=-1;
        $x=1;

        foreach($grid_data as $key=>$row)
        {
            //set cell A1 content with some text
            $this->excel->getActiveSheet()->setCellValue('A' . ($key + $x), lang('year'));
            $this->excel->getActiveSheet()->setCellValue('B' . ($key + $x), lang('month'));
            $this->excel->getActiveSheet()->setCellValue('C' . ($key + $x), lang('orders'));
            $this->excel->getActiveSheet()->setCellValue('D' . ($key + $x), lang('customers'));
            $this->excel->getActiveSheet()->setCellValue('E' . ($key + $x), lang('products'));
            $this->excel->getActiveSheet()->setCellValue('F' . ($key + $x), lang('reward_points'));
            $this->excel->getActiveSheet()->setCellValue('G' . ($key + $x), lang('final_total'));

            if(!$represetitive)
            {
                $this->excel->getActiveSheet()->setCellValue('H' . ($key + $x), lang('total_discount'));
                $this->excel->getActiveSheet()->setCellValue('I' . ($key + $x), lang('coupon_discount'));
                $this->excel->getActiveSheet()->setCellValue('J' . ($key + $x), lang('products_cost'));
                $this->excel->getActiveSheet()->setCellValue('K' . ($key + $x), lang('total_profit'));
                $this->excel->getActiveSheet()->setCellValue('L' . ($key + $x), lang('profit')." [%]");
                $this->excel->getActiveSheet()->setCellValue('M' . ($key + ($x)), '');
                $this->excel->getActiveSheet()->setCellValue('N' . ($key + ($x)), '');
                $this->excel->getActiveSheet()->setCellValue('O' . ($key + ($x)), '');
            }

            $month = $row['month'];
            $year  = $row['year'];

            $month_orders_data = $this->orders_model->get_month_orders($month, $year, $country_id, $payment_id, $user_id, $user_email_id, $user_phone_id, $user_ip_address_id, $customer_group_id, $cat_id, $coupon_id, $order_id_from, $order_id_to, $date_from, $date_to, $status_date_from, $status_date_to, $order_status_id, $store_id);

            $purchased_cost         = 0;
            $total_coupons          = 0;
            $reward_points          = 0;
            $orders_total_amount    = 0;
            $orders_total_discount  = 0;
            $charge_cards_total     = 0;
            $orders_total_coupons   = 0;
            $total_discounts        = 0;
            $total_coupons_array    = array();
            $total_discount_array   = array();
            $order_total_coupon     = 0;

            foreach($month_orders_data as $order)
            {
                if($order->product_id != 0)
                {
                    $products_total         = $order->price * $order->qty;
                    $order_price            = $this->_amount_with_default_currency($products_total, $order->country_id);

                    $orders_total_amount   += $order_price;
                }
                else
                {
                    $charge_cards_total += $order->price;
                }

                $cost = $order->purchased_cost;

                $total_coupons_array[$order->country_id][$order->id] = $order->order_coupon_discount;
                $total_discount_array[$order->country_id][$order->id] = $order->orders_discount;

                $purchased_cost         += $cost;
                $reward_points          += $order->reward_points;

            }

            foreach ($total_coupons_array as $country_id=>$amount_array)
            {
                foreach ($amount_array as $amount)
                {
                    $order_coupon          = $this->_amount_with_default_currency($amount, $country_id);
                    $orders_total_coupons += $order_coupon;
                }
            }

            foreach ($total_discount_array as $country_id=>$amount_array)
            {
                foreach ($amount_array as $amount)
                {
                    $order_discount         = $this->_amount_with_default_currency($amount, $country_id);
                    $orders_total_discount += $order_discount;
                }
            }

            $profit         = $orders_total_amount - ($purchased_cost + $orders_total_coupons + $orders_total_discount);
            $total_profit   = round($profit, 2);

            $profit_percent = 0;

            if($orders_total_amount != 0)
            {
                $profit_percent = ($total_profit / ($orders_total_amount - $orders_total_coupons - $orders_total_discount )) * 100;
            }

            $profit_percent = round($profit_percent, 2);

            $x = $x + 1;

            $this->excel->getActiveSheet()->setCellValue($chars_array[$i+1].($key + ($x)), $row['year']);
            $this->excel->getActiveSheet()->setCellValue($chars_array[$i+2].($key + ($x)), $row['month']);
            $this->excel->getActiveSheet()->setCellValue($chars_array[$i+3].($key + ($x)), $row['orders_count']);
            $this->excel->getActiveSheet()->setCellValue($chars_array[$i+4].($key + ($x)), $row['users_count']);
            $this->excel->getActiveSheet()->setCellValue($chars_array[$i+5].($key + ($x)), $row['products_count']);
            $this->excel->getActiveSheet()->setCellValue($chars_array[$i+6].($key + ($x)), $row['reward_points']);
            $this->excel->getActiveSheet()->setCellValue($chars_array[$i+7].($key + ($x)), $orders_total_amount.' '.$default_currency_symbol);

            if(!$represetitive)
            {
                $this->excel->getActiveSheet()->setCellValue($chars_array[$i+8].($key + ($x)), $orders_total_discount.' '.$default_currency_symbol);
                $this->excel->getActiveSheet()->setCellValue($chars_array[$i+9].($key + ($x)), $row['coupons_discount'].' '.$default_currency_symbol);
                $this->excel->getActiveSheet()->setCellValue($chars_array[$i+10].($key + ($x)), $purchased_cost.' '.$default_currency_symbol);
                $this->excel->getActiveSheet()->setCellValue($chars_array[$i+11].($key + ($x)), $total_profit.' '.$default_currency_symbol);
                $this->excel->getActiveSheet()->setCellValue($chars_array[$i+12].($key + ($x)), $profit_percent.' [%]');
                $this->excel->getActiveSheet()->setCellValue($chars_array[$i+13].($key + ($x)), '');
                $this->excel->getActiveSheet()->setCellValue($chars_array[$i+14].($key + ($x)), '');
                $this->excel->getActiveSheet()->setCellValue($chars_array[$i+15].($key + ($x)), '');
            }


            /*foreach($chars_array as $index=>$char)
            {
                $this->excel->getActiveSheet()->getStyle($char.($x + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            }*/


            //orders details
            $orders_data = $this->orders_model->get_year_month_orders($month, $year, $lang_id, $country_id, $payment_id, $user_id, $user_email_id, $user_phone_id, $user_ip_address_id, $customer_group_id, $cat_id, $coupon_id, $order_id_from, $order_id_to, $date_from, $date_to, $status_date_from, $status_date_to, $order_status_id);

            $x = $x + 1;

            $this->excel->getActiveSheet()->setCellValue($chars_array[$i+1] . ($x), lang('order_id'));
            $this->excel->getActiveSheet()->setCellValue($chars_array[$i+2] . ($x), lang('username'));
            $this->excel->getActiveSheet()->setCellValue($chars_array[$i+3] . ($x), lang('country'));
            $this->excel->getActiveSheet()->setCellValue($chars_array[$i+4] . ($x), lang('status'));
            $this->excel->getActiveSheet()->setCellValue($chars_array[$i+5] . ($x), lang('purchased_products'));
            $this->excel->getActiveSheet()->setCellValue($chars_array[$i+6] . ($x), lang('payment_method'));
            $this->excel->getActiveSheet()->setCellValue($chars_array[$i+7] . ($x), lang('reward_points'));
            $this->excel->getActiveSheet()->setCellValue($chars_array[$i+8] . ($x), lang('total'));

            if(!$represetitive)
            {
                $this->excel->getActiveSheet()->setCellValue($chars_array[$i+9] . ($x), lang('products_cost'));
                $this->excel->getActiveSheet()->setCellValue($chars_array[$i+10] . ($x), lang('recharge_card'));
                $this->excel->getActiveSheet()->setCellValue($chars_array[$i+11] . ($x), lang('discount_details'));
                $this->excel->getActiveSheet()->setCellValue($chars_array[$i+12] . ($x), lang('coupon_discount'));
                $this->excel->getActiveSheet()->setCellValue($chars_array[$i+13] . ($x), lang('total_profit'));
                $this->excel->getActiveSheet()->setCellValue($chars_array[$i+14] . ($x), lang('profit').'[%]');
                $this->excel->getActiveSheet()->setCellValue($chars_array[$i+15] . ($x), lang('tax'));
                $this->excel->getActiveSheet()->setCellValue($chars_array[$i+16] . ($x), lang('final_total'));
            }

            foreach($chars_array as $key=>$char)
            {
                //Main Table Style
                //change the font size
                $this->excel->getActiveSheet()->getStyle($char.$x)->getFont()->setSize(14);
                //make the font become bold
                $this->excel->getActiveSheet()->getStyle($char.$x)->getFont()->setBold(true);

                //set aligment to center for that merged cell (A1 to D1)
                $this->excel->getActiveSheet()->getStyle($char.$x)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                //$this->excel->getActiveSheet()->getStyle($char.$key)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            }


            foreach($orders_data as $key=>$order)
            {
                $purchased_cost         = 0;
                $charge_cards_total     = 0;
                $products_total_price   = 0;
                $reward_points          = 0;
                $products = '';

                $order_products = $this->orders_model->get_order_products($order->id, $lang_id);

                foreach($order_products as $product)
                {
                    if($product->product_id == 0)
                    {
                        $products .= $product->price.' '.lang('recharge_card')."\n";

                        $charge_cards_total += $product->price;
                    }
                    else
                    {

                        $products .= $product->qty.' * '.$product->title."\n";

                        $cost                = $product->purchased_cost ;//* $product->qty;
                        $product_final_price = $product->final_price * $product->qty;
                        $product_details     = $this->products_model->get_products_row($product->product_id);

                        $purchased_cost       += $cost;
                        $products_total_price += $product_final_price;
                        $reward_points        += $product->reward_points;

                    }
                }

                $order_total_coupon = 0;
                $profit_percent     = 0;

                if($order->coupon_discount > 0)
                {
                    $is_total_coupon = $this->_check_order_coupon_discount_type($order->id);

                    if($is_total_coupon)
                    {
                        $order_total_coupon = $order->coupon_discount;
                        $order_total_coupon = $this->_amount_with_default_currency($order_total_coupon, $order->country_id) ;
                    }
                }

                $total_with_default_currency        = $this->_amount_with_default_currency($order->total, $order->country_id) ;
                $discount_with_default_currency     = $this->_amount_with_default_currency($order->discount, $order->country_id) ;
                $coupon_with_default_currency       = $this->_amount_with_default_currency($order->coupon_discount, $order->country_id) ." ". $default_currency_symbol;
                $tax_with_default_currency          = $this->_amount_with_default_currency($order->tax, $order->country_id);
                $final_with_default_currency        = $this->_amount_with_default_currency($products_total_price, $order->country_id);
                $cost_with_default_currency         = $purchased_cost;
                $cards_with_default_currency        = $this->_amount_with_default_currency($charge_cards_total, $order->country_id);
                $total_with_tax_default_currency    = $this->_amount_with_default_currency($order->final_total, $order->country_id);

                /**
                 * $final_with_default_currency = total products final price (substract(-) coupons of type product or category)
                 * $purchased_cost              = total products cost
                 * $order_total_coupon          = if order has coupon of type total, its added to calculate profit
                 */


                $profit       = $final_with_default_currency - ($purchased_cost + $order_total_coupon);
                $total_profit = $profit ;

                $order_total  = $total_with_default_currency - $cards_with_default_currency;
                if($final_with_default_currency != 0)
                {
                    $profit_percent = round(($total_profit / ($final_with_default_currency - $order_total_coupon )) * 100 , 2) . ' % ';
                }


                $x = $x + 1;

                $this->excel->getActiveSheet()->setCellValue($chars_array[$i+1] . $x, $order->id);
                $this->excel->getActiveSheet()->setCellValue($chars_array[$i+2] . $x, $order->first_name.' '.$order->last_name);
                $this->excel->getActiveSheet()->setCellValue($chars_array[$i+3] . $x, $order->country);
                $this->excel->getActiveSheet()->setCellValue($chars_array[$i+4] . $x, $order->status);
                $this->excel->getActiveSheet()->setCellValue($chars_array[$i+5] . $x, $products);
                $this->excel->getActiveSheet()->setCellValue($chars_array[$i+6] . $x, $order->payment_method);
                $this->excel->getActiveSheet()->setCellValue($chars_array[$i+7] . $x, $reward_points);
                $this->excel->getActiveSheet()->setCellValue($chars_array[$i+8] . $x, $total_with_default_currency.' '.$default_currency_symbol);

                if(!$represetitive)
                {
                    $this->excel->getActiveSheet()->setCellValue($chars_array[$i+9] . $x, $cost_with_default_currency.' '.$default_currency_symbol);
                    $this->excel->getActiveSheet()->setCellValue($chars_array[$i+10] . $x, $cards_with_default_currency.' '.$default_currency_symbol);
                    $this->excel->getActiveSheet()->setCellValue($chars_array[$i+11] . $x, $discount_with_default_currency.' '.$default_currency_symbol);
                    $this->excel->getActiveSheet()->setCellValue($chars_array[$i+12] . $x, $coupon_with_default_currency.' '.$default_currency_symbol);
                    $this->excel->getActiveSheet()->setCellValue($chars_array[$i+13] . $x, $total_profit.' '.$default_currency_symbol);
                    $this->excel->getActiveSheet()->setCellValue($chars_array[$i+14] . $x, $profit_percent);
                    $this->excel->getActiveSheet()->setCellValue($chars_array[$i+15] . $x, $tax_with_default_currency.' '.$default_currency_symbol);
                    $this->excel->getActiveSheet()->setCellValue($chars_array[$i+16] . $x, $final_with_default_currency.' '.$default_currency_symbol);
                }
                // to add new line
                $this->excel->getActiveSheet()->getStyle($chars_array[$i+5] . $x)->getAlignment()->setWrapText(true);

            }

            $x++;

        }




        foreach($chars_array as $key=>$char)
        {
            //Main Table Style
            //change the font size
            $this->excel->getActiveSheet()->getStyle($char.'1')->getFont()->setSize(14);
            //make the font become bold
            $this->excel->getActiveSheet()->getStyle($char.'1')->getFont()->setBold(true);

            //set aligment to center for that merged cell (A1 to D1)
            $this->excel->getActiveSheet()->getStyle($char.'1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            //$this->excel->getActiveSheet()->getStyle($char.$key)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        }


        //merge cell A1 until D1
        //$this->excel->getActiveSheet()->mergeCells('A1:D1');
        //set aligment to center for that merged cell (A1 to D1)
        //$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $filename = date('d-m-Y').'-dynamic-report.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache

        //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
        //if you want to save it as .XLSX Excel 2007 format
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        //force user to download the Excel file without writing it to server's HD
        $objWriter->save('php://output');
    }

    private function _amount_with_default_currency($amount, $country_id)
    {
        $amount_with_default_currency = $this->currency->get_amount_with_default_currency($amount, $country_id);

        return $amount_with_default_currency;
    }

    public function get_users_emails()
    {
        $users_array = array();
        $users = $this->users_model->get_active_users();

        foreach($users as $user)
        {
            $users_array[$user->id] = $user->email;
        }

        echo $users_array;
    }

    public function get_users_suggestions()
    {
        $term        = $this->input->post('term');

        if(in_array(3, $this->user_groups_ids))  // if in group representative
        {
            //$users_data   = array();//$this->users_model->get_representative_related_users($this->data['user']->id);
            $rep_id = $this->data['user']->id;
        }
        else
        {
            //$users_data   = array();//$this->users_model->get_active_users();
            $rep_id = 0;
        }

        $suggestions = $this->user_model->get_users_suggestions($term, $rep_id);
        $result      = array();

        foreach($suggestions as $row)
        {
            $result[]=array('label'=>$row->first_name.' '.$row->last_name , 'value'=>$row->first_name.'-'.$row->last_name.'-'.$row->id);
        }

        echo json_encode($result);
    }



    public function get_suggestions($field)
    {
        $term        = $this->input->post('term');

        if(in_array(3, $this->user_groups_ids))  // if in group representative
        {
            $rep_id = $this->data['user']->id;
        }
        else
        {
            $rep_id = 0;
        }

        $suggestions = $this->user_model->get_suggestions($term, $field, $rep_id);
        $result      = array();

        if($field == 'email')
        {
            foreach($suggestions as $row)
            {
                $result[]=array('label'=>$row->$field, 'value'=>$row->$field);
            }
        }
        else
        {
            foreach($suggestions as $row)
            {
                $result[]=array('label'=>$row->$field, 'value'=>$row->$field.'/'.$row->id);
            }
        }
        echo json_encode($result);
    }

    public function get_coupon_suggestions()
    {
        $term        = $this->input->post('term');

        $suggestions = $this->coupon_codes_model->get_coupon_suggestions($term, $this->data['active_language']->id);
        $result      = array();

        foreach($suggestions as $row)
        {
            $result[]=array('label'=>$row->name, 'value'=>$row->name.'/'.$row->id);
        }

        echo json_encode($result);
    }

    public function get_cat_suggestions()
    {
        $term        = $this->input->post('term');

        $suggestions = $this->cat_model->get_cats_suggestions($term, $this->data['active_language']->id);
        $result      = array();

        foreach($suggestions as $row)
        {
            $result[]=array('label'=>$row->name, 'value'=>$row->name);
        }

        echo json_encode($result);
    }



}
