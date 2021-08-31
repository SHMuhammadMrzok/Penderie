<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Admin_products_discounts extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        require(APPPATH . 'includes/global_vars.php');
        $this->load->model('products_model');
        $this->load->model('categories/cat_model');
        $this->load->model('users/countries_model');
        $this->load->model('products_serials_model');

        $this->lang_row = $this->admin_bootstrap->get_active_language_row();

        $this->stores   = $this->admin_bootstrap->get_user_available_stores();

        $store_id_array = array();

        foreach($this->stores as $store)
        {
            $store_id_array[] = $store->store_id;
        }

        $this->stores_ids = $store_id_array;

        $this->view_folder = 'Admin';

        if($this->data['store_owner'] == 1)
        {
            $this->view_folder = 'Sell';
        }
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
            'global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css'
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
            'global/plugins/bootstrap-touchspin/bootstrap.touchspin.js'

        );


        $this->data['js_code'] = 'ComponentsPickers.init();
        ';
    }

    public function index()
    {
        $lang_id = $this->data['active_language']->id;
        $index_method_id = $this->data['method_id'];

        $this->data['count_all_records']  = $this->products_model->get_count_all_products_discounts($lang_id, '', $this->stores_ids);
        $this->data['data_language']      = $this->lang_model->get_active_data_languages();

        $this->data['columns']            = array(
                                                     lang('product_name'),
                                                 );

        $this->data['orders']              = array(
                                                     lang('product_name'),
                                                     lang('sort')
                                                 );

        $this->data['actions']  = array( 'delete'=>lang('delete'));
        $this->data['search_fields']  = array( lang('product_name'));
        $this->data['index_method_id'] = $index_method_id;

        $this->data['content']  = $this->load->view($this->view_folder.'/grid/grid_html', $this->data, true);
        $this->load->view($this->view_folder.'/main_frame',$this->data);

    }

    public function ajax_list()
    {

        /**************************************/
        $this->stores   = $this->admin_bootstrap->get_user_available_stores($_POST['index_method_id']);
        $store_id_array = array();
        //echo $_POST['index_method_id']; die();

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



        $grid_data       = $this->products_model->get_products_discounts_data($lang_id, $limit, $offset, $search_word, $order_by, $order_state, $this->stores_ids);

        $db_columns = array(
                         'id'   ,
                         'title',
                         'sort'

                       );

        $this->data['hidden_fields'] = array('id', 'sort');

        $new_grid_data = array();

        foreach($grid_data as $key =>$row)
        {
            foreach($db_columns as $column)
            {
                if($column == 'active')
                {
                    if($row->active == 0)
                    {
                        $new_grid_data[$key][$column] = '<span class="badge badge-danger">'.lang('not_active').'</span>';
                    }
                    elseif($row->active = 1)
                    {
                        $new_grid_data[$key][$column] = '<span class="badge badge-success">'.lang('active').'</span>';
                    }
                }
                elseif($column == 'discount_start_unix_time')
                {
                    $new_grid_data[$key][$column] = date('Y-m-d',$row->discount_start_unix_time);
                }
                elseif($column == 'discount_end_unix_time')
                {
                    $new_grid_data[$key][$column] = date('Y-m-d',$row->discount_end_unix_time);
                }
                else
                {
                    $new_grid_data[$key][$column] = $row->{$column};
                }

            }
        }

        $this->data['grid_data']         = $new_grid_data;
        $this->data['count_all_records'] = $this->products_model->get_count_all_products_discounts($lang_id,$search_word);
        $this->data['display_lang_id']   = $lang_id;

        $output_data = $this->load->view($this->view_folder.'/grid/grid_data',$this->data, true);
        $count_data  = $this->data['count_all_records'];

        echo json_encode(array($output_data, $count_data, $search_word));
    }

    public function read($id, $display_lang_id)
    {
        $id              = intval($id);
        $display_lang_id = intval($display_lang_id);

        if($id && $display_lang_id)
        {
            $read_data = array();
            $data      = $this->products_model->get_product_discount_row_data($id, $display_lang_id);

            foreach($data as $row)
            {
                if($row->active == 1)
                {
                    $row->{'active_value'} = '<span class="badge badge-success">'.lang('active').'</span>' ;
                }
                else
                {
                    $row->{'active_value'} = '<span class="badge badge-danger">'.lang('not_active').'</span>';
                }

                $read_data[$row->country_id] = $row;
            }


            $this->data['read_data'] = $read_data;


            if($this->view_folder == 'Sell')
            {
                $this->data['content']  = $this->load->view($this->view_folder.'/grid/read_view', $this->data, true);
            }
            else
            {
                $this->data['content']  = $this->load->view('read_discount', $this->data, true);
            }
            $this->load->view($this->view_folder.'/main_frame',$this->data);
        }
    }

    public function no_discount()
    {
        $this->session->set_flashdata('nodiscount',lang('nodiscount'));
        redirect('products/admin_products/index','refresh');
    }

    public function add_form($product_id)
    {
        if(is_numeric($product_id))
        {
            $product_id     = intval($product_id);
            $validation_msg = false;
            $countries      = $this->input->post('country_id');

            if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
            {
                $validation_msg = true;

                foreach($countries as $country_id)
                {
                    $this->form_validation->set_rules('price['.$country_id.']', lang('new_price'), 'required|greater_than_equal_to[1]');
                    $this->form_validation->set_rules('max_units_customers['.$country_id.']', lang('max_units_customers'), 'required|greater_than_equal_to[0]');

                    if(!isset($_POST['dailey'][$country_id]))
                    {
                        $this->form_validation->set_rules('from['.$country_id.']', lang('start_discount_date'), 'required');
                        $this->form_validation->set_rules('to['.$country_id.']', lang('end_discount_date'), 'required|callback_allowed_to_time');
                    }
                    else
                    {
                        $this->form_validation->set_rules('time_from['.$country_id.']', lang('start_discount_date'), 'required|less_than_equal_to[24]');
                        $this->form_validation->set_rules('time_to['.$country_id.']', lang('end_discount_date'), 'required|callback_allowed_to_date|less_than_equal_to[24]');
                    }

                }
                $this->form_validation->set_message('required', lang('required')." : %s ");
                $this->form_validation->set_message('less_than_equal_to', lang('24_validation')." : %s ");
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

            }

            if ($this->form_validation->run() == FALSE)
        		{
        		  $this->_add_form($product_id, $validation_msg);
            }
            else
            {
                $product_id              = $this->input->post('product_id');
                $new_price               = $this->input->post('price');
                $active                  = (isset( $_POST['active']))? $this->input->post('active'):0;
                $max_units_customers     = $this->input->post('max_units_customers');
                $start_discount_date     = $this->input->post('from');
                $end_discount_date       = $this->input->post('to');
                $daily_discount          = $this->input->post('dailey');
                $start_discount_time     = $this->input->post('time_from');
                $end_discount_time       = $this->input->post('time_to');
                $special_offer_label     = (isset( $_POST['special_offer']))? $this->input->post('special_offer', true):0;


                $product_discount_data  = array(
                                                  'product_id'               => $product_id ,
                                                  'unix_time'                => time()
                                                );

                $this->products_model->insert_product_discount_data($product_discount_data);
                $product_discount_id = $this->db->insert_id();

                foreach($countries as $country_id)
                {
                    if(! isset($active[$country_id]) )
                    {
                        $active[$country_id] = 0;
                    }

                    if(isset($_POST['dailey'][$country_id]))
                    {
                        $p_dailey              = $daily_discount[$country_id];
                        $p_start_discount_time = $start_discount_time[$country_id];
                        $p_end_discount_time   = $end_discount_time[$country_id];
                    }
                    else
                    {
                        $p_dailey              = 0;
                        $p_start_discount_time = 0;
                        $p_end_discount_time   = 0;
                    }

                    $product_discount_countries_data = array(
                                                                'product_discount_id'      => $product_discount_id                          ,
                                                                'country_id'               => $country_id                                   ,
                                                                'price'                    => $new_price[$country_id]                       ,
                                                                'discount_start_unix_time' => strtotime($start_discount_date[$country_id])  ,
                                                                'discount_end_unix_time'   => strtotime($end_discount_date[$country_id])    ,
                                                                'active'                   => $active[$country_id]                          ,
                                                                'max_units_customers'      => $max_units_customers[$country_id]             ,
                                                                'dailey'                   => $p_dailey                                     ,
                                                                'discount_start_time'      => $p_start_discount_time                        ,
                                                                'discount_end_time'        => $p_end_discount_time                          ,
                                                                'special_offer_label'      => (isset( $_POST['special_offer_label'][$country_id]))? 1:0
                                                             );

                    $this->products_model->insert_product_discount_countries_data($product_discount_countries_data);
                }

                $_SESSION['success'] = lang('success');
                $this->session->mark_as_flash('success');

                redirect('products/admin_products_discounts/','refresh');
            }
        }
    }

    private function _add_form($product_id, $validation_msg)
    {
        $this->_js_and_css_files();

        $lang_id = $this->data['active_language']->id;

        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }

        $product_countries   = $this->countries_model->get_product_countries($product_id, $this->lang_row->id);
        
        $product_data        = $this->products_model->get_product_row_details($product_id, $lang_id);

        foreach($product_countries as $row)
        {
            if($product_data->serials_per_country == 0)
            {
                $row->{'available_serials'} = $this->products_serials_model->get_product_global_serials_count($row->product_id);
            }
            else
            {
                $row->{'available_serials'} = $this->products_serials_model->get_product_country_available_serials($row->product_id,$row->country_id);
            }

            $product_array_data[] = $row;

        }

        $this->data['product_id']   = $product_id;
        $this->data['product_name'] = $product_data->title;
        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/add_form/" . $product_id;
        $this->data['countries']    = $product_array_data;

        if($this->view_folder == 'Sell')
        {
            $this->data['content']  = $this->load->view($this->view_folder.'/products_discounts_form', $this->data, true);
        }
        else
        {
            $this->data['content']  = $this->load->view('products_discounts', $this->data, true);
        }

        $this->load->view($this->view_folder.'/main_frame',$this->data);
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

                $countries                 = $this->input->post('country_id');
                $product_id                = $this->input->post('product_id');
                $product_discount_id_array = $this->input->post('product_discount_id');

                foreach($countries as $country_id)
                {
                    $this->form_validation->set_rules('price['.$country_id.']', lang('new_price'), 'required|greater_than_equal_to[0]');
                    $this->form_validation->set_rules('max_units_customers['.$country_id.']', lang('max_units_customers'), 'required|greater_than_equal_to[0]');

                    if(!isset($_POST['dailey'][$country_id]))
                    {
                        $this->form_validation->set_rules('from['.$country_id.']', 'start_discount_date', 'required');
                        $this->form_validation->set_rules('to['.$country_id.']', 'end_discount_date', 'required|callback_allowed_to_date');
                    }
                    else
                    {
                        $this->form_validation->set_rules('time_from['.$country_id.']', 'start_discount_date', 'required|less_than_equal_to[24]');
                        $this->form_validation->set_rules('time_to['.$country_id.']', 'end_discount_date', 'required|callback_allowed_to_time|less_than_equal_to[24]');
                    }

                }

                $this->form_validation->set_message('required', lang('required').' : %s');
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            }
        }

        if($this->form_validation->run() == FALSE)
    		{
    		   $this->_edit_form($id, $validation_msg);
        }
        else
        {
            $new_price               = $this->input->post('price');
            $active                  = (isset( $_POST['active']))? $this->input->post('active'):array();
            $max_units_customers     = $this->input->post('max_units_customers');
            $start_discount_date     = $this->input->post('from');
            $end_discount_date       = $this->input->post('to');
            $daily_discount          = $this->input->post('dailey');
            $start_discount_time     = $this->input->post('time_from');
            $end_discount_time       = $this->input->post('time_to');

            $product_discount_data  = array(
                                              'product_id'  => $product_id ,
                                              'unix_time'   => time()
                                           );

            $this->products_model->update_product_discount_data($product_discount_data, $id);

            foreach($countries as $country_id)
            {
                if(! isset($active[$country_id]) )
                {
                    $active[$country_id] = 0;
                }

                if(isset($_POST['dailey'][$country_id]))
                {
                    $p_dailey              = $daily_discount[$country_id];
                    $p_start_discount_time = $start_discount_time[$country_id];
                    $p_end_discount_time   = $end_discount_time[$country_id];
                }
                else
                {
                    $p_start_discount_time = 0;
                    $p_end_discount_time   = 0;
                    $p_dailey              = 0;
                }

                if(isset($start_discount_date[$country_id]) && isset($end_discount_date[$country_id]) )
                {
                    $discount_s_date = strtotime($start_discount_date[$country_id]);
                    $discount_e_data = strtotime($end_discount_date[$country_id]);
                }
                else
                {
                    $discount_s_date = '0';
                    $discount_e_data = '0';
                }

                $product_discount_countru_data  = array(
                                                  'country_id'               => $country_id                                 ,
                                                  'price'                    => $new_price[$country_id]                     ,
                                                  'discount_start_unix_time' => $discount_s_date,
                                                  'discount_end_unix_time'   => $discount_e_data                            ,
                                                  'active'                   => $active[$country_id]                        ,
                                                  'max_units_customers'      => $max_units_customers[$country_id]           ,
                                                  'dailey'                   => $p_dailey                                   ,
                                                  'discount_start_time'      => $p_start_discount_time                      ,
                                                  'discount_end_time'        => $p_end_discount_time                        ,
                                                  'special_offer_label'      => (isset( $_POST['special_offer_label'][$country_id]))? 1:0
                                               );
//echo '<pre>'; print_r($_POST);
//echo '<pre>'; print_r($product_discount_countru_data); die();
                $this->products_model->update_product_discount_country_data($product_discount_countru_data, $id, $country_id);
            }

            $_SESSION['success'] = lang('success');
            $this->session->mark_as_flash('success');

            redirect('products/admin_products_discounts/','refresh');
        }
    }

    private function _edit_form($id, $validation_msg)
    {
        $this->_js_and_css_files();
        $general_data  = array();
        $filtered_data = array();

        $lang_id = $this->data['active_language']->id;

        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }

        $this->data['form_action'] = $this->data['module'] . "/" . $this->data['controller'] . "/edit/" . $id;

        $general_data       = $this->products_model->get_product_discount_data($id, $lang_id);
        $countries_data     = $this->products_model->get_discount_countries_data($id, $lang_id);

        if(count($general_data) != 0)
        {
            $product_data               = $this->products_model->get_product_row_details($general_data->product_id, $lang_id);
            //$product_countries          = $this->countries_model->get_product_countries($general_data->product_id, $lang_id);

            $this->data['product_id']   = $general_data->product_id;
            $this->data['product_name'] = $product_data->title;
        }

        foreach($countries_data as $row)
        {
            if($product_data->serials_per_country == 0)
            {
                $row->{'available_serials'} = $this->products_serials_model->get_product_global_serials_count($row->product_id);
            }
            else
            {
                $row->{'available_serials'} = $this->products_serials_model->get_product_country_available_serials($row->product_id,$row->country_id);
            }

            $filtered_data[$row->country_id] = $row;
        }

        $this->data['id']             = $id;
        $this->data['countries']      = $filtered_data;
        $this->data['general_data']   = $general_data;

        if($this->view_folder == 'Sell')
        {
            $this->data['content']  = $this->load->view($this->view_folder.'/products_discounts_form', $this->data, true);
        }
        else
        {
            $this->data['content']  = $this->load->view('products_discounts', $this->data, true);
        }

        $this->load->view($this->view_folder.'/main_frame',$this->data);
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
        $discounts_ids = $this->input->post('row_id');

        if(is_array($discounts_ids))
        {

            $ids_array = array();

            foreach($discounts_ids as $discount_id)
            {
                $ids_array[] = $discount_id['value'];
            }
        }
        else
        {
            $ids_array = array($discounts_ids);
        }

        $this->products_model->delete_discount_data($ids_array);
    }

    public function allowed_to_date($to_date)
    {
        $validation = true;
        $countries  = $this->input->post('country_id');
        $from_date  = $this->input->post('from');
        $to_date    = $this->input->post('to');

        foreach($countries as $country_id)
        {
            $from_date = strtotime($from_date[$country_id]);
            $to_date   = strtotime($to_date[$country_id]);

            if($to_date < $from_date)
            {
                $validation = false;
                $this->form_validation->set_message('allowed_to_date', lang('to_date_validation'));
            }
        }

        return $validation;
    }

    public function allowed_to_time($to_date)
    {
        $validation = true;
        $countries  = $this->input->post('country_id');
        $from_date  = $this->input->post('time_from');
        $to_date    = $this->input->post('time_to');

        foreach($countries as $country_id)
        {
            $from_date = $from_date[$country_id];
            $to_date   = $to_date[$country_id];

            if($to_date < $from_date)
            {
                $validation = false;
                $this->form_validation->set_message('allowed_to_time', lang('to_date_validation'));
            }
        }

        return $validation;
    }

    public function sorting()
    {
        $id         = $this->input->post('id');
        $old_index  = $this->input->post('old_sort');
        $new_index  = $this->input->post('new_sort');
        $sort_state = $this->input->post('sort_state');
        $table      = 'products_discounts';

        $this->products_model->update_row_sort($id,$old_index,$new_index,$sort_state, $table);

    }






/************************************************************************/
}
