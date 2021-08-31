<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Admin_purchase_orders extends CI_Controller
{
    public $lang_row ;

    public function __construct()
    {
        parent::__construct();

        require(APPPATH . 'includes/global_vars.php');

        $this->load->model('purchase_orders_model');
        $this->load->model('vendors/vendors_model');
        $this->load->model('categories/cat_model');
        $this->load->model('products_serials_model');
        $this->load->model('currencies/currency_model');
        $this->load->model('stores/stores_model');

        $this->lang_row = $this->admin_bootstrap->get_active_language_row();

        /*************************************************************************/
        $this->stores   = $this->admin_bootstrap->get_user_available_stores();

        $store_id_array = array();

        foreach($this->stores as $store)
        {
            $store_id_array[] = $store->store_id;
        }

        $this->stores_ids = $store_id_array;
        /*************************************************************************/

    }

    private function _js_and_css_files()
    {
        $this->data['css_files'] = array();

        $this->data['js_files']  = array(
                                            //confirmation
                                            'global/plugins/bootstrap-confirmation/bootstrap-confirmation.min.js',
                                            'pages/scripts/ui-confirmations.js',
                                            //TouchSpin
                                            'global/plugins/fuelux/js/spinner.min.js',
                                            'global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js',
                                            'global/plugins/bootstrap-touchspin/bootstrap.touchspin.js'
                                        );
    }

    public function index()
    {
        $lang_id = $this->data['active_language']->id;

        $this->data['count_all_records'] = $this->purchase_orders_model->get_count_all_purchase_orders();
        $this->data['data_language']     = $this->lang_model->get_active_data_languages();

        $filters_array    = array();

        $filters_array[0] = (object)array(
                                            'id'   => 1,
                                            'name' => lang('completed')
                                         );

        $filters_array[1] = (object)array(
                                            'id'   => 2,
                                            'name' => lang('not_completed')
                                          );

        $this->data['columns']  = array(
                                             lang('order_number'),
                                             lang('purchase_order_number'),
                                             lang('products_number'),
                                             lang('vendor_id'),
                                             lang('unix_time'),
                                             lang('add_serials')
                                       );

        $this->data['orders']   = array(
                                            lang('order_number'),
                                            lang('vendor_id')
                                       );

        $this->data['filters']  = array(
                                            array(
                                                     'filter_title'         => lang('status_filter') ,
                                                     'filter_name'          => 'status_filter'       ,
                                                     'filter_data'          => $filters_array
                                                 ),
                                            array(
                                                     'filter_title'         => lang('name_of_store') ,
                                                     'filter_name'          => 'store_filter_id'     ,
                                                     'filter_data'          => $this->stores
                                                 )
                                       );

        $this->data['search_fields'] = array( lang('vendor'), lang('order_number'));
        $this->data['index_method_id']  = $this->data['method_id'];

        $this->data['content'] = $this->load->view('Admin/grid/grid_html', $this->data, true);
        $this->load->view('Admin/main_frame', $this->data);
    }

    public function ajax_list()
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


        if(isset($_POST['search_word']) && trim($_POST['search_word']) != '')
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

            $status_id    = $filters_data[0];
            $store_filter_id = $filters_data[1];
        }
        else
        {
            $status_id = 0;
            $store_filter_id = 0;
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


        $grid_data  = $this->purchase_orders_model->get_purchase_orders_data($lang_id, $limit, $offset, $search_word, $order_by, $order_state, $status_id, $store_filter_id, $this->stores_ids);

        $db_columns = array(
                             'id',
                             'order_number',
                             'products_number',
                             'title' ,
                             'unix_time',
                             'add_serials',
                          );

       $this->data['hidden_fields'] = array();

       $new_grid_data = array();

        foreach($grid_data as $key =>$row)
        {
            $products_number = 0 ;
            $product_num     = 0;
            $products_number = $this->purchase_orders_model->get_purchase_order_products($row->id);
            $product_num     = count($products_number);

            foreach($db_columns as $column)
            {
                if($column == 'add_serials')
                {
                    if($row->serials_cash_count != $row->serials_count )
                    {
                        $new_grid_data[$key][$column] = "<a href='".base_url()."products/admin_products_serials/add_form/" . $row->id . "' >" . lang('add_serials') . "</a>";
                    }
                    else
                    {
                        $new_grid_data[$key][$column] = lang('completed');
                    }

                }elseif($column == 'products_number'){

                    $new_grid_data[$key][$column] = $product_num;

                }
                elseif($column == 'unix_time')
                {
                    $new_grid_data[$key][$column] = date('Y/m/d', $row->unix_time);
                }
                else
                {
                    $new_grid_data[$key][$column] = $row->{$column};
                }
            }
        }


        $this->data['grid_data']            = $new_grid_data;
        $this->data['count_all_records']    = $this->purchase_orders_model->get_count_all_purchase_orders($search_word);
        $this->data['display_lang_id']      = $lang_id;
        $this->data['unset_edit']           = true;
        $this->data['unset_delete']         = true;

        $output_data = $this->load->view('Admin/grid/grid_data',$this->data, true);
        $count_data  = $this->data['count_all_records'];

        echo json_encode(array($output_data, $count_data, $search_word));
    }

     public function read($id,$display_lang_id)
     {

        $id              = intval($id);
        $display_lang_id = intval($display_lang_id);

        if($id && $display_lang_id)
        {
            $data                     = $this->purchase_orders_model->get_row_data($id, $display_lang_id);

            if(in_array($data->store_id, $this->stores_ids))
            {
                $purchase_order_products  = $this->purchase_orders_model->get_purchase_order_products($id);
                $products_details         = $this->purchase_orders_model->get_purchase_order_products_details($id ,$display_lang_id);
                $purchase_order_serials   = $this->purchase_orders_model->get_purchase_order_serials($id);
                $currency_data            = $this->currency_model->get_currency_result($data->currency_id);

                $product_num              = count($purchase_order_products);

                $products            = array();
                $product_serial      = array();
                $products_new_array  = array();

                $serial_count   = 0;

                foreach($products_details as $product)
                {

                    $serial_count = $this->products_serials_model->get_product_country_serial_count($id, $product->product_id, $product->country_id);
                    $product->{'serial_count'} = $serial_count;

                    $products_new_array[] = $product;
                }


                if($data)
                {
                    $row_data = array(
                                        lang('order_number')    => $data->order_number  ,
                                        lang('name_of_store')   => $data->store_name    ,
                                        lang('vendor')          => $data->vendor        ,
                                        lang('unix_time')       => date('Y/m/d',$data->unix_time),
                                        lang('products_number') => $product_num

                                     );

                    $this->data['row_data']        = $row_data;
                    $this->data['products']        = $products_new_array;
                    $this->data['currency_symbol'] = $currency_data->currency_symbol;

                    $this->data['content']  = $this->load->view('Admin/grid/read_view', $this->data, true);
                    $this->load->view('Admin/main_frame',$this->data);
                }
            }
            else
            {
                $this->data['error_msg'] = lang('no_store_permission');

                $this->data['content']  = $this->load->view('Admin/grid/read_view', $this->data, true);
                $this->load->view('Admin/main_frame',$this->data);
            }

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


    public function add()
    {
        $validation_msg = false;

        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
        {
            $validation_msg = true;
            $this->form_validation->set_rules('vendor_id' , lang('vendor_id') , 'required');
            $this->form_validation->set_rules('order_number' , lang('order_number') , 'required|callback_unique_per_vendor');

            $this->form_validation->set_message('required', lang('required')." : %s ");
        }

        $default_currency_data = $this->currency_model->get_default_currency_data();

        if ($this->form_validation->run() == FALSE)
		{
		  $this->_add_form($validation_msg, $default_currency_data->currency_symbol);
        }
        else
        {
            /**************************purchase_orders_data******************************/
            $vendor_id      = $this->input->post('vendor_id');
            $order_number   = $this->input->post('order_number');
            $draft          = $this->input->post('draft');
            $order_store_id = $this->input->post('order_store_id');

            $purchase_orders_data  = array(
                                            'order_number'  => $order_number ,
                                            'vendor_id'     => $vendor_id ,
                                            'currency_id'   => $default_currency_data->id,
                                            'unix_time'     => time() ,
                                            'draft'         => $draft ,
                                            'store_id'      => $order_store_id
                                          );

            $this->purchase_orders_model->insert_purchase_orders($purchase_orders_data);
            $purchase_order_id = $this->db->insert_id();

            /**********************purchase_orders_products data****************************/

             $product_ids    = $this->input->post('product_id');
             $quantity       = $this->input->post('quantity');
             $price_per_unit = $this->input->post('price_per_unit');
             $country_id     = $this->input->post('country_id');
             $store_id       = $this->input->post('store_id');
             $serials_count  = 0;

             foreach($product_ids as $key => $product_id)
             {
                $product_country = $country_id[$key] != 0 ? $country_id[$key] : NULL;

                $purchase_orders_products_data = array(
                                                        'purchase_order_id'   => $purchase_order_id     ,
                                                        'product_id'          => $product_id            ,
                                                        'quantity'            => $quantity[$key]        ,
                                                        'price_per_unit'      => $price_per_unit[$key]  ,
                                                        'country_id'          => $product_country       ,
                                                        'store_id'            => $store_id[$key]        ,
                                                     );

                $this->purchase_orders_model->insert_purchase_orders_products($purchase_orders_products_data);

                //-->>> GET Product_country count from purchase_orders_products  table

                $product_data  = $this->products_model->get_products_row($product_id);


                if($product_data->serials_per_country == 0)
                {
                    $country = NULL;
                }
                else
                {
                    $country = $country_id[$key];
                }

                $products_per_country = $this->purchase_orders_model->get_products_country_data($product_id, $country);

                /*$total_product_price_per_country = 0;

                foreach($products_per_country as $product)
                {
                    $total_product_price_per_country += $product->price_per_unit;
                }

                $product_average_cost_in_each_country = round ($total_product_price_per_country / count($products_per_country),2);
                */
                //-->>> save product_average_cost_in_each_country value in products_countries table

                $data = array('average_cost' => $price_per_unit[$key]);

                if($product_data->non_serials == 1)
                {
                    //generate serials dynamically
                    $secret_key  = $this->config->item('new_encryption_key');
                    $secret_iv   = md5('serial_iv');

                    for ($i=0; $i<$quantity[$key]; $i++)
                    {
                        $serial = $this->generateRandomString();

                        $enc_serials = $this->encryption->encrypt($serial, $secret_key, $secret_iv);


                        $products_serials_data  = array(
                                                         'purchase_order_id' => $purchase_order_id ,
                                                         'product_id'        => $product_id        ,
                                                         'country_id'        => $country           ,
                                                         'store_id'          => $store_id[$key]    ,
                                                         'serial'            => $enc_serials       ,
                                                         'unix_time'         => time()             ,
                                                         'last_update_time'  => time()             ,
                                                         'active'            => 1                  ,
                                                         'generated_dynamically' => 1
                                                      );

                        $this->products_serials_model->insert_products_serials($products_serials_data);

                    }

                    $this->purchase_orders_model->update_serials_cash_count($purchase_order_id, $quantity[$key]);
                }

                if($product_data->serials_per_country == 1)
                {
                    $this->purchase_orders_model->update_products_country_average_cost($product_id, $country_id[$key], $data);
                }
                else
                {
                    $this->purchase_orders_model->update_products_country_average_cost($product_id, 0, $data);
                }





                $serials_count += $quantity[$key];
             }

             $purchase_order_updated_data['serials_count'] = $serials_count;
             $this->purchase_orders_model->update_purchase_orders($purchase_order_id, $purchase_order_updated_data);

             $_SESSION['success'] = lang('success');
             $this->session->mark_as_flash('success');

             redirect('products/admin_purchase_orders/','refresh');
        }
    }

    private function _add_form($validation_msg, $currency_symbol)
    {
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }

        $this->_js_and_css_files();

        $this->data['mode']        = 'add';
        $this->data['form_action'] = $this->data['module'] . "/" . $this->data['controller'] . "/add";
        $vendors                   = $this->vendors_model->get_vendors($this->lang_row->id, $this->stores_ids);
        $categories                = array();//$this->cat_model->get_categories($this->lang_row->id);
        $stores                    = $this->stores;//$this->stores_model->get_all_stores($this->lang_row->id);

        $vendors_array     = array();
        $products_options  = array();
        $countries_options = array();
        $cats_array        = array();
        $stores_array      = array();

        $vendors_array[0]  = lang('choose');
        $cats_array[0]     = lang('choose');
        $stores_array[0]   = lang('choose');

        foreach($vendors as $row)
        {
            $vendors_array[$row->id] = $row->title.' ('.$row->store_name.') ';
        }

        foreach($categories as $cat)
        {
            if($cat->parent_id != 0)
            {
                $cats_array[$cat->id] = $cat->name;
            }
            /*if($cat->parent_id == 0)
            {
                foreach($categories as $category)
                {
                    if($category->parent_id == $cat->id)
                    {
                        $cats_array["{$cat->name}"][$category->id] = $category->name;
                    }
                }
            }*/
        }

        foreach($stores as $store)
        {
            $stores_array[$store->id] = $store->name;
        }

        $this->data['cats_array']         = $cats_array;
        $this->data['currency_symbol']    = $currency_symbol;
        $this->data['vendors_array']      = $vendors_array;
        $this->data['products_options']   = $products_options;
        $this->data['countries_options']  = $countries_options;
        $this->data['stores']             = $stores_array;

        $this->data['content'] = $this->load->view('purchase_orders', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
    }

    public function edit($id)
    {
        if(is_numeric($id))
        {
            $id = intval($id);

            $validation_msg = false;

            if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
            {
                $validation_msg = true;

                $id = $this->input->post('purchase_order_id');
                $this->form_validation->set_rules('vendor_id' , lang('vendor_id') , 'required');
                $this->form_validation->set_message('required', lang('required'));
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            }
        }

        if($this->form_validation->run() == FALSE)
		{
		   $this->_edit_form($id, $validation_msg);
        }
        else
        {
            /**************************purchase_orders_data form******************************/
            $form_vendor_id          = $this->input->post('vendor_id');
            $database_vendor_id      = $purchase_orders_data->vendor_id;
            $vendor_id ='';

            if($form_vendor_id == $database_vendor_id)
            {
               $order_number             =  $this->input->post('purchase_order_number');
               $vendor_id                =  $database_vendor_id;

            }else{

                $vendor_id               = $form_vendor_id;
                $vendor_purchase_orders  = $this->purchase_orders_model->get_vendor_purchase_orders($vendor_id);
                $order_number            = $vendor_purchase_orders +1;
            }

            $purchase_orders_data  = array(
                                            'order_number'  => $order_number ,
                                            'vendor_id'     => $vendor_id ,
                                            'unix_time'     => time() ,
                                            'draft'         => 0 ,
                                          );

            $this->purchase_orders_model->update_purchase_orders($id,$purchase_orders_data);

            /**********************purchase_orders_products data****************************/
            $this->purchase_orders_model->delete_purchase_orders_products_data($id);
            //-->>> Delete Old Data

             $product_id     = $this->input->post('product_id');
             $quantity       = $this->input->post('quantity');
             $price_per_unit = $this->input->post('price_per_unit');
             $country_id     = $this->input->post('country_id');

             foreach($product_id as $key => $value)
            {
                $product_country = $country_id[$key] != 0? $country_id[$key] : NULL;

                $purchase_orders_products_data = array(
                                                        'purchase_order_id'   => $id ,
                                                        'product_id'          => $product_id[$key] ,
                                                        'quantity'            => $quantity[$key],
                                                        'price_per_unit'      => $price_per_unit[$key],
                                                        'country_id'          => $product_country,
                                                     );

                $this->purchase_orders_model->insert_purchase_orders_products($purchase_orders_products_data);
            }

            $_SESSION['success'] = lang('updated_successfully');
            $this->session->mark_as_flash('success');

            redirect('products/admin_purchase_orders/','refresh');
        }
    }

    private function _edit_form($id, $validation_msg)
    {
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }

        $this->_js_and_css_files();


        $this->data['mode']               = 'edit';
        $this->data['form_action']        = $this->data['module'] . "/" . $this->data['controller'] . "/edit/". $id;
        $this->data['id']                 = $id;

        $purchase_orders_data             = $this->purchase_orders_model->get_purchase_orders_row($id);
        $purchase_orders_products_data    = $this->purchase_orders_model->get_purchase_orders_products_result($id,$this->lang_row->id);


        $vendors                          = $this->vendors_model->get_vendors($this->lang_row->id);
        $categories                       = $this->cat_model->get_categories($this->lang_row->id);


        $vendors_array      = array();
        $vendors_array[0]   = lang('choose');
        foreach($vendors as $row)
        {
            $vendors_array[$row->id] = $row->title;
        }


        $cats_array       = array();
        $cats_array[0]    = lang('choose');
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

        $products_options = array();
        $countries_options = array();

        $this->data['purchase_orders_data']             = $purchase_orders_data;
        $this->data['purchase_orders_products_data']    = $purchase_orders_products_data;
        $this->data['cats_array']                       = $cats_array;
        $this->data['vendors_array']                    = $vendors_array;
        $this->data['products_options']                 = $products_options;
        $this->data['countries_options']                = $countries_options;

        $this->data['content'] = $this->load->view('purchase_orders', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);

    }

    public function get_products()
    {

        $cat_id   = intval($this->input->post('cat_id', true));
        $store_id = intval($this->input->post('store_id', true));

        $result   = array();
        $products = $this->purchase_orders_model->get_category_products($cat_id, $this->lang_row->id, $store_id);

        $options  = "<option>".lang('choose')."</option>";

        if(isset($products) && count($products)!= 0)
        {
            foreach($products as $row)
            {
                 $options .= "<option value='$row->id'>$row->title</option>";
            }

            echo $options;
        }

    }

     public function unique_per_vendor($order_number)
     {
        $order_number = intval($order_number);
        $vendor_id    = $this->input->post('vendor_id');
        $is_exist_order_number = $this->purchase_orders_model->check_order_number_with_vendor($order_number, $vendor_id);

        if($is_exist_order_number)
        {
            $this->form_validation->set_message('unique_per_vendor', lang('order_number_is_unique_per_vendor'));
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    public function get_product_details($product_id)
    {
        $product_id   = intval($product_id);
        $product_data = $this->products_model->get_products_row($product_id);
        if($product_data->serials_per_country == 1)
        {
            $countries = $this->purchase_orders_model->get_product_countries($product_id,$this->lang_row->id);

            //$options    = "<option value = 0 >".lang('choose')."</option>";
            foreach($countries as $row)
            {
                 $options .= "<option value='$row->country_id'>$row->name</option>";
            }
        }
        else
        {
            $options    = 'none';
        }
        echo $options;
    }

    public function get_product_serials()
    {
        $product_id = intval($this->input->post('product_id'));
        $country_id = intval($this->input->post('country_id'));

        $serials = $this->products_serials_model->get_product_country_available_serials($product_id, $country_id);

        echo $serials;
    }

    /*public function get_vendor_currency()
    {
        $vendor_id = $this->input->post('vendor_id');

        $currency = $this->vendors_model->get_vendor_currency($vendor_id)->currency_symbol;

        echo $currency;
    }*/

    function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /*public function get_store_vendors()
    {
        $store_id = intval($this->input->post('store_id'));

        $vendors_data = $this->vendors_model->get_vendors($this->data['lang_id'], array($store_id));

        $options    = '<select name="vendor_id" class="form-control select2" id="vendor_id">
                        <option value="0">----------------</option>';

        foreach($vendors_data as $vendor)
        {
            $options .= '<option value="'.$vendor->vendor_id.'">'.$vendor->title.'</option>';
        }

        $options .= '</select>';
        echo $options;
    }*/

    public function get_vendor_store()
    {
        $vendor_id  = intval($this->input->post('vendor_id', TRUE));

        $store_data = $this->vendors_model->get_vendor_store_data($vendor_id, $this->data['lang_id']);

        $store = '<input type="hidden" name="store_id" id="store_id" value="'.$store_data->store_id.'" readonly />
                  <input type="text" name="store_name" id="store_name" class="form-control" value="'.$store_data->name.'" readonly />';

        echo $store;
    }


/************************************************************************/
}
