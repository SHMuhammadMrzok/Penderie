<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Admin_products_serials extends CI_Controller
{

    public $lang_row ;
    public $data = array();

    public function __construct()
    {
        parent::__construct();

        require(APPPATH . 'includes/global_vars.php');
        $this->load->model('products_serials_model');
        $this->load->model('products_model');
        $this->load->model('users/countries_model');
        $this->load->model('purchase_orders_model');

        $this->load->library('encryption');
        $this->config->load('encryption_keys');

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

        $this->data['js_files']  = array();
        $this->data['js_code'] = "";

    }

    public function index()
    {
        $lang_id = $this->data['active_language']->id;

        $this->data['count_all_records'] = $this->products_serials_model->get_count_all_products_serials($lang_id);
        $this->data['data_language']     = $this->lang_model->get_active_data_languages();

        $status_filter_data = array();
        $status_data        = $this->products_serials_model->get_serial_status();

        foreach($status_data as $filter)
        {
            if($filter->status_id == 0)
            {
                $filter->{'name'}     = lang($filter->status);
                $filter->{'id'}       = 100;
            }
            else
            {
                $filter->{'name'}     = lang($filter->status);
                $filter->{'id'}       = $filter->status_id;
            }

            $status_filter_data[] = $filter;
        }

        $this->data['filters']           = array(
                                                   array(
                                                          'filter_title' => lang('purchase_orders_filters'),
                                                          'filter_name'  => 'purchase_orders_filters',
                                                          'filter_data'  => $this->products_serials_model->get_purchase_orders_filter_data()
                                                         ) ,
                                                   array(
                                                          'filter_title' => lang('products_filters'),
                                                          'filter_name'  => 'products_filters',
                                                          'filter_data'  => $this->products_model->get_products_filter_data($lang_id)
                                                         ) ,
                                                   array(
                                                          'filter_title' => lang('validity_filter'),
                                                          'filter_name'  => 'status_filter',
                                                          'filter_data'  => $status_filter_data
                                                        ),
                                                   array(
                                                          'filter_title' => lang('countries_filter'),
                                                          'filter_name'  => 'countries_filter',
                                                          'filter_data'  => $this->countries_model->get_countries($lang_id)
                                                        ),
                                                   array(
                                                          'filter_title' => lang('name_of_store'),
                                                          'filter_name'  => 'store_filter_id',
                                                          'filter_data'  => $this->stores
                                                        )

                                                 );

        $this->data['columns']           = array(
                                                     lang('purchase_order_number'),
                                                     //lang('order_number'),
                                                     lang('product_name'),
                                                     lang('country_name'),
                                                     lang('serial'),
                                                     lang('unix_time'),
                                                     lang('last_update_time'),
                                                     lang('validity'),
                                                     lang('order_id'),
                                                     lang('active')
                                                 );

        $this->data['orders']            = $this->data['columns'];
        $this->data['date_filter']       = true;

        $this->data['actions']           = array( 'delete'=>lang('delete'));
        $this->data['search_fields']     = array(lang('serial'));
        $this->data['index_method_id']   = $this->data['method_id'];

        $this->data['content']  = $this->load->view('Admin/grid/grid_html', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);

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
        }
        else
        {
            $lang_id = $this->data['active_language']->id;
        }
        if(isset($_POST['limit']))
        {
            $limit = intval($this->input->post('limit'));
        }
        else
        {
            $limit = 1;
        }

        if(isset($_POST['page_number']))
        {
            $active_page = intval($this->input->post('page_number'));
        }
        else
        {
            $active_page = 1;
        }

        $offset  = ($active_page-1) * $limit;


        if(isset($_POST['search_word']) && trim($_POST['search_word']) != '')
        {
            $post_word   = $this->input->post('search_word');
            $secret_key  = $this->config->item('new_encryption_key');
            $secret_iv   = md5('serial_iv');

            $search_word = $this->encryption->encrypt($post_word, $secret_key, $secret_iv);
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

            $purchase_orders_filters_id     = intval($filters_data[0]);
            $products_filters_id            = intval($filters_data[1]);
            $status_filter_id               = intval($filters_data[2]);
            $countries_filter_id            = intval($filters_data[3]);
            $stores_filter_id               = intval($filters_data[4]);
        }
        else
        {
            $purchase_orders_filters_id     = 0;
            $products_filters_id            = 0;
            $status_filter_id               = '';
            $status_filter_id               = 0;
            $stores_filter_id               = 0;
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

        $grid_data  = $this->products_serials_model->get_products_serials_data($limit, $offset, $search_word, $lang_id, $order_by, $order_state, $purchase_orders_filters_id, $products_filters_id, $status_filter_id, $countries_filter_id, $date_from, $date_to, $stores_filter_id, $this->stores_ids);

        $db_columns = array(
                             'id',
                             //'order_number',
                             'purchase_order_id',
                             'title',
                             'name',
                             'serial',
                             'unix_time',
                             'last_update_time',
                             'status',
                             'order_id',
                             'active'
                           );

       $this->data['hidden_fields'] = array('id');

       $new_grid_data = array();

        foreach($grid_data as $key =>$row)
        {
            foreach($db_columns as $column)
            {
                if($column == 'serial')
                {
                    $secret_key  = $this->config->item('new_encryption_key');
                    $secret_iv   = md5('serial_iv');//md5($row->unix_time);
                    $dec_serials = $this->encryption->decrypt($row->serial, $secret_key, $secret_iv);

                    $new_grid_data[$key][$column] = $dec_serials;
                }
                elseif($column == 'status')
                {
                    if($row->serial_status == 3)
                    {
                        $status = '<span class="badge badge-danger">'.lang('invalid').'</span>';
                    }
                    elseif($row->serial_status == 0)
                    {
                        $status = '<span class="badge badge-success">'.lang('available').'</span>';
                    }
                    elseif($row->serial_status == 1)
                    {
                        $status = '<span class="badge badge-warning">'.lang('pending').'</span>';
                    }
                    elseif($row->serial_status == 2)
                    {
                        $status = '<span class="badge badge-info">'.lang('sold').'</span>';
                    }

                    $new_grid_data[$key][$column] = $status;
                }
                elseif($column == 'order_id')
                {
                    $order = '';

                    if($row->serial_status != 3 && $row->serial_status != 4)
                    {
                        $order_id = $this->products_serials_model->get_serial_order_id($row->id);
                        $order = '<a target="_blank" href="' . base_url() . 'orders/admin_order/view_order/' . $order_id . '">' . $order_id . '</a>';
                    }

                    $new_grid_data[$key][$column] = $order;
                }
                /*elseif($column == 'order_number')
                {
                    $order_number_per_vendor      = $this->purchase_orders_model->get_purchase_order_per_vendor_number($row->purchase_order_id);
                    $new_grid_data[$key][$column] = $order_number_per_vendor;
                }*/
                elseif($column == 'unix_time')
                {
                    $new_grid_data[$key][$column] = date('Y-m-d', $row->unix_time);
                }
                elseif($column == 'name')
                {
                    $new_grid_data[$key][$column] = $row->country_id=='0'? lang('all_countries'): $row->name;
                }
                elseif($column == 'last_update_time')
                {
                    if($row->last_update_time == 0)
                    {
                        $last_update_time = '';
                    }
                    else
                    {
                        $last_update_time = date('Y-m-d', $row->last_update_time);
                    }

                    $new_grid_data[$key][$column] = $last_update_time;
                }
                elseif($column == 'active')
                {
                    if($row->active == 1)
                    {
                        $active = '<span class="badge badge-success">'.lang('active').'</span>';
                    }
                    else
                    {
                        $active = '<span class="badge badge-danger">'.lang('not_active').'</span>';
                    }

                    $new_grid_data[$key][$column] = $active;
                }
                else
                {
                    $new_grid_data[$key][$column] = $row->{$column};
                }
            }
        }


        $this->data['grid_data']            = $new_grid_data;
        $this->data['count_all_records']    = $this->products_serials_model->get_count_all_products_serials($lang_id, $search_word, $order_by, $order_state, $purchase_orders_filters_id, $products_filters_id, $status_filter_id, $countries_filter_id, $date_from, $date_to);
        $this->data['display_lang_id']      = $lang_id;

        $output_data = $this->load->view('Admin/grid/grid_data',$this->data, true);
        $count_data  = $this->data['count_all_records'];

        echo json_encode(array($output_data, $count_data, $search_word));
    }

    public function read($id, $display_lang_id)
    {
        $id              = intval($id);
        $display_lang_id = intval($display_lang_id);

        if($id && $display_lang_id)
        {
            $status = '';
            $data   = $this->products_serials_model->get_row_data($id,$display_lang_id);

            $order_store_id = $this->purchase_orders_model->get_purchase_order_store_id($data->purchase_order_id);
            if(in_array($order_store_id, $this->stores_ids))
            {
                $secret_key  = $this->config->item('new_encryption_key');
                $secret_iv   = md5('serial_iv');//md5($row->unix_time);
                $dec_serials = $this->encryption->decrypt($data->serial, $secret_key, $secret_iv);

                if(isset($data->name) && $data->name != '')
                {
                    $country = $data->name;
                }
                else
                {
                    $country = lang('global_quantitiy');
                }

                if($data->serial_status == 3)
                {
                    $status = '<span class="badge badge-danger">'.lang('invalid').'</span>';
                }
                elseif($data->serial_status == 0)
                {
                    $status = '<span class="badge badge-success">'.lang('available').'</span>';
                }
                elseif($data->serial_status == 1)
                {
                    $status = '<span class="badge badge-warning">'.lang('pending').'</span>';
                }
                elseif($data->serial_status == 2)
                {
                    $status = '<span class="badge badge-info">'.lang('sold').'</span>';
                }

                $row_data = array(
                                    lang('order_number') => $data->purchase_order_id ,
                                    lang('product_name') => $data->title ,
                                    lang('country_name') => $country  ,
                                    lang('serial')       => $dec_serials ,
                                    lang('validity')     => $status      ,
                                    lang('unix_time')    => date('Y/m/d H:i' , $data->unix_time),
                                    lang('last_update_time') => date('Y/m/d H:i' , $data->last_update_time)

                                 );



                $this->data['row_data'] = $row_data;
            }
            else
            {
                $this->data['error_msg'] = lang('no_store_permission');
            }
            $this->data['content']  = $this->load->view('Admin/grid/read_view', $this->data, true);
            $this->load->view('Admin/main_frame',$this->data);
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

    public function delete()
    {
        $products_ids = $this->input->post('row_id');

        if(is_array($products_ids))
        {

            $ids_array = array();

            foreach($products_ids as $product_id)
            {
                $ids_array[] = $product_id['value'];
            }
        }
        else
        {
            $ids_array = array($products_ids);
        }

        $cant_delete = false;

        foreach($ids_array as $id)
        {
            $is_available = $this->products_serials_model->check_available_serial($id);
            //$order_store_id = $this->purchase_orders_model->get_purchase_order_store_id($data->purchase_order_id);

            if(!$is_available)
            {
                echo lang('cant_delete_sold_serials');
                $cant_delete = true;
                break;
            }
        }
        if(!$cant_delete)
        {
            // update product_quantity in product_countries table
            foreach($ids_array as $id)
            {

                $serial_data = $this->products_serials_model->get_products_serials_row($id);
                $product_qty = $this->products_model->count_product_available_quantity($serial_data->product_id, $serial_data->country_id);
                $updated_data['product_quantity'] = $product_qty - 1;
                $this->products_model->update_product_countries($serial_data->product_id, $serial_data->country_id, $updated_data);
                $purchase_order_ids[] = $serial_data->purchase_order_id;

            }

            $this->products_serials_model->delete_products_serials($ids_array);

            foreach($purchase_order_ids as $purchase_order_id)
            {
                //update product serials cash count
                $purchase_order_serials_cash_count = $this->products_serials_model->count_order_serials($purchase_order_id);
                $purchase_order_updated_data['serials_cash_count'] = $purchase_order_serials_cash_count;
                $this->purchase_orders_model->update_purchase_orders($purchase_order_id, $purchase_order_updated_data);
            }

            echo "1";
        }
    }


     public function add_form($purchase_order_id)
     {
        $validation_msg = false;

        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
        {

            // echo "<br /> Admin Products Serials Controller : add_form => _POST <pre>";
            // print_r($_POST);
            // echo "<br />";
            // die;

            $purchase_order_id = intval($this->input->post('purchase_order_id'));
            $products          = $this->input->post('product_id', true);
            
            if(isset($products)&& $products !='')
            {
                 foreach($products as $product_refrence => $product)
                 {
                    /*** Mrzok Edit */
                    $productId_countryId_selectedOptionalFields = explode('-', $product_refrence);
                    $product_id     = $productId_countryId_selectedOptionalFields[0];
                    $country_id     = $productId_countryId_selectedOptionalFields[1];
                    $selected_optional_fields    = str_replace( '_' , ',' ,$productId_countryId_selectedOptionalFields[2]); // Replace ( _ ) seperator with ( , ) to be the same as data existed in database

                    /*** End Edit */
                    // validation on only products with available serials count
                    $product_data          = $this->purchase_orders_model->get_purchase_order_product_data($purchase_order_id, $product_id , $country_id , $selected_optional_fields);
                    $product_total_serials = $product_data->quantity;
                    $added_serials_count   = $this->products_serials_model->count_order_product_serials($country_id, $purchase_order_id, $country_id , $selected_optional_fields);
                    $remaining_serials     = $product_total_serials - $added_serials_count;

                    if($remaining_serials != 0)
                    {
                        $selected_optional_fields_with_underscore = str_replace( ',' , '_' ,$selected_optional_fields); // we reconvert it to be handeled correctly as array key
                        $this->form_validation->set_rules("serial[$product_id-$country_id-$selected_optional_fields_with_underscore]" , lang('serial') , 'trim');
                    }

                    // echo "<br /> Admin Products Serials Controller : add_form => product <pre>";
                    // print_r($product);
                    // echo "<br />";
                    // die;
                    // foreach($product  as $countrytkey=> $country_id)
                    // {
                    //      // validation on only products with available serials count
                    //      $product_data          = $this->purchase_orders_model->get_purchase_order_product_data($purchase_order_id, $productkey);
                    //      $product_total_serials = $product_data->quantity;
                    //      $added_serials_count   = $this->products_serials_model->count_order_product_serials($productkey, $purchase_order_id);
                    //      $remaining_serials     = $product_total_serials - $added_serials_count;

                    //      if($remaining_serials != 0)
                    //      {
                    //         $this->form_validation->set_rules("serial[$productkey][$countrytkey]" , lang('serial') , 'trim');
                    //      }
                    // }
                 }
            }

            $this->form_validation->set_message('required', lang('required').' : %s');
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

            $validation_msg = true;
        }
        if ($this->form_validation->run() == FALSE)
    		{
    		  $this->_add_form($purchase_order_id, $validation_msg);
        }
        else
        {

            /**************************product_serial_data******************************/
            $serial_input  = $this->input->post('serial');
            $country_ids   = $this->input->post('country_id');
            $store_id      = $this->input->post('store_id');
            $pro_quantity  = $this->input->post('product_quantity');
            // echo '<pre>';print_r($country_ids); diE();
            $this->load->library('Csvreader');

            $doubl_message    = array();
            $qmessage         = array();
            $moremessage      = array();
            $repeated_serial  = array();
            $messages_array   = array();


            // echo "<br /> Admin Products Serials Controller : add_form => products <pre>";
            // print_r($products);
            // echo "<br />";
            // die();

            // foreach($products as $productkey=> $product) 
            foreach($products as $product_refrence => $product)
            {
                /*** Mrzok Edit */
                $productId_countryId_selectedOptionalFields = explode('-', $product_refrence);
                $product_id     = $productId_countryId_selectedOptionalFields[0];
                $country_id     = $productId_countryId_selectedOptionalFields[1];
                $selected_optional_fields    = $productId_countryId_selectedOptionalFields[2]; // value of selected options seperated with underscore 
                /// End Edit

                $ramain = 0;
                $product_country_remain_quantity = 0 ;

                // foreach($product  as $countrytkey=> $country_id)
                // {
                    $file_serials           =  array();
                    $serials                =  array();
                    $upload_serials         =  array();
                    $all_serials            =  array();
                    $serials_unique_array   =  array();
                    $duplicte_array         =  array();

                    // $serials = explode("\n", $serial_input[$productkey][$countrytkey]); // BASIC CODE
                    $serials = explode("\n", $serial_input[$product_id.'-'.$country_id.'-'.$selected_optional_fields]);

                    foreach($serials as $key=>$serial)
                    {
                        if(trim($serial) == '')
                        {
                            unset($serials[$key]);
                        }
                    }

                    // $product_quantity  = $pro_quantity[$productkey][$countrytkey]; // BASIC CODE
                    $product_quantity  = $pro_quantity[$product_id.'-'.$country_id.'-'.$selected_optional_fields];
                    /*******************UPloading Files****************************/

                    // $file_id    = $productkey."_".$countrytkey; // BASIC CODE
                    $file_id    = $product_id."_".$country_id."_".$selected_optional_fields;

                    if($_FILES["userfile_$file_id"]['error'] ==0)
                    {
                        $file_path      = $this->upload("userfile_$file_id");
                        if($file_path != '')
                        {
                            $upload_serials = $this->csvreader->parse_file($file_path);

                            foreach($upload_serials as $serial_arr)
                            {
                                foreach($serial_arr as $col_name=>$serial)
                                {
                                    $file_serials [] = $serial ;
                                }
                            }
                        }
                    }

                    /****************************************************/

                    $all_serials          = array_merge($file_serials, $serials);
                    $all_serials          = array_map('trim', $all_serials);
                    $serials_unique_array = array_unique($all_serials);

                    $duplicte_array       = $this->return_duplicate($all_serials);

                    // $product_name         = $this->products_model->get_product_name($productkey,$this->lang_row->id); // BASIC CODE
                    $product_name         = $this->products_model->get_product_name($product_id,$this->lang_row->id);

                    // if($countrytkey != 0) // BASIC CODE
                    if($country_id != 0)
                    {
                        // $country_name = $this->products_serials_model->get_country_name($countrytkey, $this->lang_row->id); //  BASIC CODE
                        $country_name = $this->products_serials_model->get_country_name($country_id, $this->lang_row->id);
                    }
                    else
                    {
                        $country_name = lang('all_countries');
                    }

                    if(isset($duplicte_array) && count($duplicte_array)!= 0 )
                    {
                        foreach($duplicte_array as $number)
                        {
                            $doubl_message[] = lang('dublicate_number')."<br/>".$number."</br>".lang('for_product').$product_name."</br>";
                        }
                    }

                    /**********************************************/
                    $selected_optional_fields_with_coma    = str_replace( '_' , ',' ,$selected_optional_fields); // Replace ( _ ) seperator with ( , ) to be the same as data existed in database
                    
                    /* calculate product serials numbers to this purchase from product_serials table and purchase_order_products table
                    ** if this product in this country for this purchase have serials before in products_serials table
                    ** then the remain available quantity is the differance between the product_country quantity in purchase_order_products table
                    ** and product_country serials count to this purchase
                    */
                    // $product_country_serial_count = $this->products_serials_model->get_product_country_serial_count($purchase_order_id ,$productkey,$countrytkey); // BASIC CODE
                    $product_country_serial_count = $this->products_serials_model->get_product_country_serial_count($purchase_order_id ,$product_id,$country_id, '', $selected_optional_fields_with_coma);

                    if($product_country_serial_count == 0)
                    {
                        $product_country_remain_quantity = $product_quantity;

                    }else{

                        $product_country_remain_quantity = $product_quantity - $product_country_serial_count;
                    }

                    if($product_country_remain_quantity < count($serials_unique_array))
                    {
                        //-->> take only the allowed number of entered serials

                       $serials_unique_array = array_slice($serials_unique_array,0, $product_country_remain_quantity);

                       $moremessage[] = lang('product_serials_count')."   ".lang('for_product').' ( '.$product_name.' )'.lang('in_country').' ( '.$country_name.' )  <br/>'.lang('more_than_product_quantity');

                    }

                    $inserted_serials = array();

                    if(!empty($serials_unique_array) && count($serials_unique_array) != 0)
                    {
                        foreach($serials_unique_array as $serial)
                        {
                            if($serial != '')
                            {
                                //--> serial encription
                                $secret_key  = $this->config->item('new_encryption_key');
                                $secret_iv   = md5('serial_iv');
                                $enc_serials = $this->encryption->encrypt($serial, $secret_key, $secret_iv);

                                //-->>ask for dublicate serial
                                $serial_count =  $this->products_serials_model->get_products_serials_row_count($enc_serials);

                                if($serial_count == 0)
                                {
                                    //get optional fields
                                    // $purchase_order_data = $this->purchase_orders_model->get_purchase_order_product_data($purchase_order_id, $productkey); // BASIC CODE
                                    $purchase_order_data = $this->purchase_orders_model->get_purchase_order_product_data($purchase_order_id, $product_id , $country_id , $selected_optional_fields_with_coma);

                                    $products_serials_data  = array(
                                                                     'purchase_order_id' => $purchase_order_id ,
                                                                    //  'product_id'        => $productkey        , // BASIC CODE
                                                                    //  'country_id'        => $countrytkey       , // BASIC CODE
                                                                     'product_id'        => $product_id        ,
                                                                     'country_id'        => $country_id       ,
                                                                     'store_id'          => $purchase_order_data->store_id,//$country_id['store_id'],
                                                                     'serial'            => $enc_serials       ,
                                                                     'unix_time'         => time()             ,
                                                                     'last_update_time'  => time()             ,
                                                                     'active'            => (isset( $_POST['active'][$product]))? $_POST['active'][$product]:1,
                                                                     'optional_fields'   => $purchase_order_data->optional_fields,
                                                                     'selected_optional_fields' => $purchase_order_data->selected_optional_fields
                                                                  );
                                    //echo '<pre>'; print_r($purchase_order_data); die();
                                    $this->products_serials_model->insert_products_serials($products_serials_data);
                                    $inserted_serials[]= $serial;

                                    $ramain     = $product_country_remain_quantity - count($inserted_serials) ;
                                    $qmessage[] = $serial.' '.lang('added_successfully').'<br/> '.lang('remain_product_serials_count')."  ".lang('for_product').' ( '.$product_name.' ) '.lang('in_country').' ( '.$country_name.' ) '.$ramain;

                                }
                                else
                                {
                                     $repeated_serial[] = lang('this_serial')." : ".$serial."  ".lang('is_repeated');
                                }
                            }
                        }
                    }

                    // update product_quantity in product_countries table
                    // $product_qty = $this->products_model->count_product_available_quantity($productkey, $countrytkey); // BASIC CODE
                    $product_qty = $this->products_model->count_product_available_quantity($product_id, $country_id == 0 ? NULL : $country_id);
                    $updated_data['product_quantity'] = $product_qty;
                    // $this->products_model->update_product_countries($productkey, $countrytkey, $updated_data); // BASIC CODE
                    $this->products_model->update_product_countries($product_id, $country_id == 0 ? NULL : $country_id, $updated_data);

                // }//for country
            }//for products

            //update product serials cash count
            $purchase_order_serials_cash_count = $this->products_serials_model->count_order_serials($purchase_order_id);
            $purchase_order_updated_data['serials_cash_count'] = $purchase_order_serials_cash_count;
            $this->purchase_orders_model->update_purchase_orders($purchase_order_id, $purchase_order_updated_data);


            if(count($qmessage)>0 || count($moremessage)>0 || count($doubl_message)>0 || count($repeated_serial)>0)
            {
                if(count($qmessage) > 0)
                {
                    $messages_array[] = $qmessage;
                }

                if(count($moremessage)>0)
                {
                    $messages_array[] = $moremessage;
                }

                if(count($doubl_message)>0)
                {
                    $messages_array[] = $doubl_message;
                }

                if(count($repeated_serial)>0)
                {
                    $messages_array[] = $repeated_serial;
                }

                $this->data['serials_msgs'] = $messages_array;

                $this->data['content']  = $this->load->view('serials_msg', $this->data, true);
                $this->load->view('Admin/main_frame',$this->data);
            }
            else
            {
                redirect('products/admin_products_serials/','refresh');
            }
        }


    }

    /* 
    // Basic Code  - Mariam Code
    public function add_form($purchase_order_id)
    {
        $validation_msg = false;

        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
        {

            echo "<br /> Admin Products Serials Controller : add_form => _POST <pre>";
            print_r($_POST);
            echo "<br />";
            die;

            $purchase_order_id = intval($this->input->post('purchase_order_id'));
            $products          = $this->input->post('product_id', true);
            
            if(isset($products)&& $products !='')
            {
                    foreach($products as $productkey=> $product)
                    {

                    echo "<br /> Admin Products Serials Controller : add_form => product <pre>";
                    print_r($product);
                    echo "<br />";
                    die;
                    foreach($product  as $countrytkey=> $country_id)
                    {
                            // validation on only products with available serials count
                            $product_data          = $this->purchase_orders_model->get_purchase_order_product_data($purchase_order_id, $productkey);
                            $product_total_serials = $product_data->quantity;
                            $added_serials_count   = $this->products_serials_model->count_order_product_serials($productkey, $purchase_order_id);
                            $remaining_serials     = $product_total_serials - $added_serials_count;

                            if($remaining_serials != 0)
                            {
                            $this->form_validation->set_rules("serial[$productkey][$countrytkey]" , lang('serial') , 'trim');
                            }
                    }
                    }
            }

            $this->form_validation->set_message('required', lang('required').' : %s');
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

            $validation_msg = true;
        }
        if ($this->form_validation->run() == FALSE)
            {
                $this->_add_form($purchase_order_id, $validation_msg);
        }
        else
        {

            // /**************************product_serial_data****************************** /
            $serial_input  = $this->input->post('serial');
            $country_ids   = $this->input->post('country_id');
            $store_id      = $this->input->post('store_id');
            $pro_quantity  = $this->input->post('product_quantity');
            //echo '<pre>';print_r($country_ids); diE();
            $this->load->library('Csvreader');

            $doubl_message    = array();
            $qmessage         = array();
            $moremessage      = array();
            $repeated_serial  = array();
            $messages_array   = array();


            echo "<br /> Admin Products Serials Controller : add_form => products <pre>";
            print_r($products);
            echo "<br />";
            die();

            foreach($products as $productkey=> $product)
            {
                $ramain = 0;
                $product_country_remain_quantity = 0 ;

                foreach($product  as $countrytkey=> $country_id)
                {

                    $file_serials           =  array();
                    $serials                =  array();
                    $upload_serials         =  array();
                    $all_serials            =  array();
                    $serials_unique_array   =  array();
                    $duplicte_array         =  array();

                        $serials = explode("\n", $serial_input[$productkey][$countrytkey]);

                    foreach($serials as $key=>$serial)
                    {
                        if(trim($serial) == '')
                        {
                            unset($serials[$key]);
                        }
                    }

                    $product_quantity  = $pro_quantity[$productkey][$countrytkey];
                    ///*******************UPloading Files**************************** /

                    $file_id    = $productkey."_".$countrytkey;

                    if($_FILES["userfile_$file_id"]['error'] ==0)
                    {
                        $file_path      = $this->upload("userfile_$file_id");
                        if($file_path != '')
                        {
                            $upload_serials = $this->csvreader->parse_file($file_path);

                            foreach($upload_serials as $serial_arr)
                            {
                                foreach($serial_arr as $col_name=>$serial)
                                {
                                    $file_serials [] = $serial ;
                                }
                            }
                        }
                    }

                    ///**************************************************** /

                        $all_serials          = array_merge($file_serials, $serials);
                    $all_serials          = array_map('trim', $all_serials);
                    $serials_unique_array = array_unique($all_serials);

                    $duplicte_array       = $this->return_duplicate($all_serials);

                    $product_name         = $this->products_model->get_product_name($productkey,$this->lang_row->id);

                    if($countrytkey != 0)
                    {
                        $country_name = $this->products_serials_model->get_country_name($countrytkey, $this->lang_row->id);
                    }
                    else
                    {
                        $country_name = lang('all_countries');
                    }

                    if(isset($duplicte_array) && count($duplicte_array)!= 0 )
                    {
                        foreach($duplicte_array as $number)
                        {
                            $doubl_message[] = lang('dublicate_number')."<br/>".$number."</br>".lang('for_product').$product_name."</br>";
                        }
                    }

                    ///********************************************** /

                    ///* calculate product serials numbers to this purchase from product_serials table and purchase_order_products table
                    //** if this product in this country for this purchase have serials before in products_serials table
                    //** then the remain available quantity is the differance between the product_country quantity in purchase_order_products table
                    //** and product_country serials count to this purchase
                    //* /

                    $product_country_serial_count = $this->products_serials_model->get_product_country_serial_count($purchase_order_id ,$productkey,$countrytkey, $purchase_order_product->optional_fields, $purchase_order_product->selected_optional_fields);

                    if($product_country_serial_count == 0)
                    {
                        $product_country_remain_quantity = $product_quantity;

                    }else{

                        $product_country_remain_quantity = $product_quantity - $product_country_serial_count;
                    }

                    if($product_country_remain_quantity < count($serials_unique_array))
                    {
                        //-->> take only the allowed number of entered serials

                        $serials_unique_array = array_slice($serials_unique_array,0, $product_country_remain_quantity);

                        $moremessage[] = lang('product_serials_count')."   ".lang('for_product').' ( '.$product_name.' )'.lang('in_country').' ( '.$country_name.' )  <br/>'.lang('more_than_product_quantity');

                    }

                    $inserted_serials = array();

                    if(!empty($serials_unique_array) && count($serials_unique_array) != 0)
                    {
                        foreach($serials_unique_array as $serial)
                        {
                            if($serial != '')
                            {
                                //--> serial encription
                                $secret_key  = $this->config->item('new_encryption_key');
                                $secret_iv   = md5('serial_iv');
                                $enc_serials = $this->encryption->encrypt($serial, $secret_key, $secret_iv);

                                //-->>ask for dublicate serial
                                $serial_count =  $this->products_serials_model->get_products_serials_row_count($enc_serials);

                                if($serial_count == 0)
                                {
                                    //get optional fields
                                    $purchase_order_data = $this->purchase_orders_model->get_purchase_order_product_data($purchase_order_id, $productkey);

                                    $products_serials_data  = array(
                                                                        'purchase_order_id' => $purchase_order_id ,
                                                                        'product_id'        => $productkey        ,
                                                                        'country_id'        => $countrytkey       ,
                                                                        'store_id'          => $purchase_order_data->store_id,//$country_id['store_id'],
                                                                        'serial'            => $enc_serials       ,
                                                                        'unix_time'         => time()             ,
                                                                        'last_update_time'  => time()             ,
                                                                        'active'            => (isset( $_POST['active'][$product]))? $_POST['active'][$product]:1,
                                                                        'optional_fields'   => $purchase_order_data->optional_fields,
                                                                        'selected_optional_fields' => $purchase_order_data->selected_optional_fields
                                                                    );
                                    //echo '<pre>'; print_r($purchase_order_data); die();
                                    $this->products_serials_model->insert_products_serials($products_serials_data);
                                    $inserted_serials[]= $serial;

                                    $ramain     = $product_country_remain_quantity - count($inserted_serials) ;
                                    $qmessage[] = $serial.' '.lang('added_successfully').'<br/> '.lang('remain_product_serials_count')."  ".lang('for_product').' ( '.$product_name.' ) '.lang('in_country').' ( '.$country_name.' ) '.$ramain;

                                }
                                else
                                {
                                        $repeated_serial[] = lang('this_serial')." : ".$serial."  ".lang('is_repeated');
                                }
                            }
                        }
                    }

                    // update product_quantity in product_countries table
                    $product_qty = $this->products_model->count_product_available_quantity($productkey, $countrytkey);
                    $updated_data['product_quantity'] = $product_qty;
                    $this->products_model->update_product_countries($productkey, $countrytkey, $updated_data);

                }//for country
                }//for products

                //update product serials cash count
                $purchase_order_serials_cash_count = $this->products_serials_model->count_order_serials($purchase_order_id);
                $purchase_order_updated_data['serials_cash_count'] = $purchase_order_serials_cash_count;
                $this->purchase_orders_model->update_purchase_orders($purchase_order_id, $purchase_order_updated_data);


                if(count($qmessage)>0 || count($moremessage)>0 || count($doubl_message)>0 || count($repeated_serial)>0)
                {
                    if(count($qmessage) > 0)
                    {
                    $messages_array[] = $qmessage;
                    }

                    if(count($moremessage)>0)
                    {
                    $messages_array[] = $moremessage;
                    }

                    if(count($doubl_message)>0)
                    {
                    $messages_array[] = $doubl_message;
                    }

                    if(count($repeated_serial)>0)
                    {
                    $messages_array[] = $repeated_serial;
                    }

                    $this->data['serials_msgs'] = $messages_array;

                    $this->data['content']  = $this->load->view('serials_msg', $this->data, true);
                    $this->load->view('Admin/main_frame',$this->data);
                }
                else
                {
                redirect('products/admin_products_serials/','refresh');
                }
        }
    }
    //// End Basic Code - Mariam Code
    */



     private function _add_form($purchase_order_id, $validation_msg)
     {
        $this->_js_and_css_files();

        $purchase_order_data = $this->products_serials_model->get_purchase_order_data($purchase_order_id,$this->lang_row->id);
        
        // echo '<pre>'; print_r($purchase_order_data); die();

        foreach($purchase_order_data as $key=>$purchase_order_product)
        {
            //  Mrzok Edit : if country id is NULL , ie. Global quantity .. set country id to 0 to be used insted of null
            if(!isset($purchase_order_product->country_id))
            {
                $purchase_order_product->{'country_id'} = 0;
            }
            // End Edit

            $product_total_serials = $purchase_order_product->quantity;
            $added_serials_count   = $this->products_serials_model->get_product_country_serial_count($purchase_order_id, $purchase_order_product->product_id, $purchase_order_product->country_id, $purchase_order_product->optional_fields, $purchase_order_product->selected_optional_fields);
            $purchase_order_product->{'remaining_serials'} = $product_total_serials - $added_serials_count;
            
            // Replace ( , ) seperator with ( _ ) to handle catching the value when adding
            $purchase_order_product->{'optional_fields'}            = str_replace( ',' , '_' ,$purchase_order_product->optional_fields);
            $purchase_order_product->{'selected_optional_fields'}   = str_replace( ',' , '_' ,$purchase_order_product->selected_optional_fields);

            // echo "<br /> Admin Products Serials Controller : _add_form => purchase_order_product <pre>";
            // print_r($purchase_order_product);
            // echo "<br />";
            // echo "<br /> Admin Products Serials Controller : _add_form => added_serials_count <pre>";
            // print_r($added_serials_count);
            // echo "<br />";
        }

        // echo "<br /> Admin Products Serials Controller : _add_form => purchase_order_data <pre>";
        // print_r($purchase_order_data);
        // echo "<br />";
        // die();
        
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }

        $this->data['mode']        = 'add';
        $this->data['form_action'] = $this->data['module'] . "/" . $this->data['controller'] . "/add_form/" . $purchase_order_id;
        $this->data['purchase_order_data'] = $purchase_order_data;

        $this->data['content']  = $this->load->view('products_serials', $this->data, true);
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
                $posted_serial        = trim($this->input->post('serial'));
                $products_serial_data = $this->products_serials_model->get_products_serials_row($id);

                $secret_key  = $this->config->item('new_encryption_key');
                $secret_iv   = md5('serial_iv');

                $dec_serial  = $this->encryption->decrypt($products_serial_data->serial, $secret_key, $secret_iv);

                if($dec_serial != $posted_serial)
                {
                    $this->form_validation->set_rules('serial', lang('serial'), 'trim|callback_check_serial_before_update');
                    $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

                    $validation_msg = true;
                }
                else
                {
                    $this->form_validation->set_rules('serial', lang('serial'), 'trim');
                }

            }

            if($this->form_validation->run() == FALSE)
    		{
    		   $this->_edit_form($id, $validation_msg);
            }
            else
            {
                $posted_serial = $this->input->post('serial');
                $enc_serial    = $this->encryption->encrypt($posted_serial, $secret_key, $secret_iv);

                $data          = array(
                                        'serial'            => $enc_serial,
                                        'active'            => $this->input->post('active'),
                                        'last_update_time'  => time()
                                      );

                $this->products_serials_model->update_serial($id, $data);

                $_SESSION['success'] = lang('updated_successfully');
                $this->session->mark_as_flash('success');

                redirect('products/admin_products_serials/','refresh');
            }
        }
     }

     private function _edit_form($id, $validation_msg)
     {
        $this->_js_and_css_files();
        $this->data['mode']                  = 'edit';
        $this->data['form_action']           = $this->data['module'] . "/" . $this->data['controller'] . "/edit/".$id;
        $this->data['id']                    = $id;

        $products_serial_data                = $this->products_serials_model->get_products_serials_row($id);
        $serial_store_id                     = $this->purchase_orders_model->get_purchase_order_store_id($products_serial_data->purchase_order_id);

        if(in_array($serial_store_id, $this->stores_ids))
        {

            $secret_key  = $this->config->item('new_encryption_key');
            $secret_iv   = md5('serial_iv');

            $dec_serials = $this->encryption->decrypt($products_serial_data->serial, $secret_key, $secret_iv);

            if($validation_msg)
            {
                $this->data['validation_msg'] = '';//lang('serial_error');
            }

            $this->data['serial'] = $dec_serials;
            $this->data['products_serial_data']  = $products_serial_data;
        }
        else
        {
            $this->data['error_msg'] = lang('no_store_permission');
        }

        $this->data['content'] = $this->load->view('products_serials_edit', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
     }

     public function check_serial_before_update($serial)
     {
        $secret_key  = $this->config->item('new_encryption_key');
        $secret_iv   = md5('serial_iv');

        $enc_serial  = $this->encryption->encrypt($serial, $secret_key, $secret_iv);

        $is_exist_serial = $this->products_serials_model->check_if_exist_serial($enc_serial);

        if ($is_exist_serial)
        {
            $this->form_validation->set_message('check_serial_before_update', lang('serial_already_exist'));
            return FALSE;
        }
        else
        {
            return TRUE;
        }
     }

      function return_duplicate($array)
      {
         $array_temp      = array();
         $duplicate_array = array();

         foreach($array as $val)
         {
            if (!in_array($val, $array_temp))
            {
               $array_temp[] = $val;
            }
            else
            {
               $duplicate_array [] = $val;
            }
         }

         if(isset($duplicate_array) && count($duplicate_array) != 0)
         {
            return $duplicate_array;
         }

      }

  public function upload($file)
  {
     /******************************Upload file data*****************************/
  	    $gallery_path = realpath(APPPATH. '../assets/uploads');
        $config['upload_path']   = $gallery_path;
		    $config['allowed_types'] = 'doc|docx|txt|csv|odt|xls|xlsx|pdf|ppt|pptx|pps|ppsx';

        $this->load->library('upload',$config);

        $this->upload->initialize($config);

        $upload_path = '';
        if( ! $this->upload->do_upload($file))
        {
            $error = array('error' => $this->upload->display_errors());
        }
        else
        {
            //If the upload success
            $data = $this->upload->data();
            $upload_path = $data['full_path'];
            return $upload_path;
        }
        /***************************************************************************/
  }


   public function get_products($cat_id)
    {
        $cat_id   = intval($cat_id);
        $products = $this->products_serials_model->get_category_products($cat_id,$this->lang_row->id);
        $result   = array();

        $options  = "<option>".lang('choose')."</option>";

        foreach($products as $row)
        {
             $options .= "<option value=$row->id>$row->title</option>";
        }

        echo $options;
    }

   public function get_product_details($product_id)
    {
        $product_id = intval($product_id);
        $countries  = $this->products_serials_model->get_product_countries($product_id,$this->lang_row->id);

        $options    = '';

        foreach($countries as $row)
        {
             $options .= "<option value=$row->country_id>$row->name</option>";
        }

        echo $options;
    }

   public function get_vendor_currency($vendor_id)
   {
        $vendor_id       = intval($vendor_id);
        $vendor_currency = $this->products_serials_model->get_vendor_currency($vendor_id,$this->lang_row->id);
        //echo  $vendor_currency->currency;

        echo "<input type='text' name='vendor_currency' value=".$vendor_currency->currency." class='form-control' id='vendor_currency' readonly='true' />";
   }

   public function show_draft()
   {
        $this->crud->set_table('purchase_orders');

        $this->crud->where('draft',1);
        $this->crud->columns('order_number','vendor_id');
        $this->crud->set_subject(lang('purchase_order'));

        $this->crud->unset_add();
        $this->crud->unset_edit();
        $this->crud->unset_delete();

        $this->crud->add_action(lang('edit'), base_url().'assets/template/admin/img/edit.png','products/purchase_orders/edit_form','ui-icon-image','');


        $this->crud->callback_column('vendor_id',array($this,'_callback_vendor_id'));


        $output = $this->crud->render();
        $this->_output_data($output);

   }

   public function download()
   {
        $filename = 'csv_sample.csv';

        $this->load->helper('file');
        $this->load->helper('download');
        $data = file_get_contents('assets/uploads/'.urldecode($filename)); // Read the file's contents

        force_download($filename, $data);
   }
/************************************************************************/
}
