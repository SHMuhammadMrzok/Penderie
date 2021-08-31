<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Admin_customer_groups extends CI_Controller
{

    public   $lang_row;
    public function __construct()
    {
        parent::__construct();


        require(APPPATH . 'includes/global_vars.php');

        $this->load->model('customer_groups_model');
        $this->load->model('users_model');
        $this->load->model('countries_model');

        $this->load->model('payment_options/payment_methods_model');

        $this->lang_row = $this->admin_bootstrap->get_active_language_row();

    }

    private function _js_and_css_files()
    {
        $this->data['css_files'] = array('');

        $this->data['js_files']  = array(
            //TouchSpin
            'global/plugins/fuelux/js/spinner.min.js',
            'global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js',
            'global/plugins/bootstrap-touchspin/bootstrap.touchspin.js',
        );

        $this->data['js_code'] = '';
    }

    public function index()
    {
        $lang_id = $this->data['active_language']->id;

        $this->data['count_all_records']  = $this->customer_groups_model->get_count_all_customer_groups($lang_id);
        $this->data['data_language']      = $this->lang_model->get_active_data_languages();

        $this->data['filters']            = array(
                                                  array(
                                                        'filter_title' => lang('countries_filter')  ,
                                                        'filter_name'  => 'country_id'              ,
                                                        'filter_data'  => $this->countries_model->get_countries($lang_id)
                                                        )

                                       );

        $this->data['columns']            = array(
                                                     lang('customer_group_name'),
                                                     lang('country'),
                                                     //lang('users_count'),
                                                     //lang('discount_percentage'),
                                                     lang('product_limit_per_order')
                                                   );

        $this->data['orders']             = $this->data['columns'];

        $this->data['actions']            = array( 'delete'=>lang('delete'));
        $this->data['search_fields']      = array( lang('customer_group'), );

        $this->data['content']  = $this->load->view('Admin/grid/grid_html', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
    }

    public function ajax_list()
    {
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



        $offset  = ($active_page-1) * $limit;


        if(isset($_POST['filter'])&& isset($_POST['filter_data']))
        {
            $filters = $this->input->post('filter');
            $filters_data = $this->input->post('filter_data');

            $country_id = intval($filters_data[0]);
        }
        else
        {
            $country_id = 0;
        }

        if(isset($_POST['search_word']) || trim($_POST['search_word']) == '')
        {
            $search_word = $this->input->post('search_word');
        }
        else
        {
            $search_word = '';
        }



        $grid_data  = $this->customer_groups_model->get_customer_groups_data($lang_id,$limit,$offset,$search_word,$country_id,$order_by,$order_state);

        $db_columns = array(
                         'id',
                         'title',
                         'name',
                         //'users_count',
                         //'discount_percentage',
                         'product_limit_per_order'
                       );

        $this->data['hidden_fields']              = array('id');

        $new_grid_data = array();

        foreach($grid_data as $key =>$row)
        {
            $users_count = 0 ;
            $users_count = $this->customer_groups_model->get_customer_group_users_count($row->id);

            foreach($db_columns as $column)
            {
                if($column == 'users_count'){

                    $new_grid_data[$key][$column] = $users_count;

                }else{

                    $new_grid_data[$key][$column] = $row->{$column};
                }


            }
        }

        $this->data['grid_data']          = $new_grid_data;

        $this->data['count_all_records']  = $this->customer_groups_model->get_count_all_customer_groups($lang_id,$search_word,$country_id);

        $this->data['display_lang_id']    = $lang_id;

        $output_data = $this->load->view('Admin/grid/grid_data',$this->data, true);
        $count_data  = $this->data['count_all_records'];

        echo json_encode(array($output_data, $count_data, $search_word, $country_id));
     }

     public function add()
     {
        $validation_msg = false;

        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
        {
            $languages = $this->input->post('lang_id');

            foreach($languages as $lang_id)
            {
                $this->form_validation->set_rules('title['.$lang_id.']',lang('title'), 'required');
            }

            $this->form_validation->set_rules('country_id', lang('country'), 'required');
            //$this->form_validation->set_rules('discount_percentage', lang('discount_percentage'), 'required|decimal');
            $this->form_validation->set_rules('product_limit_per_order', lang('product_limit_per_order'), 'required|integer');

            $this->form_validation->set_message('required', lang('required'));

            $this->form_validation->set_message('decimal', lang('decimal_required') ."  : %s ");
            $this->form_validation->set_message('integer', lang('integer_required') ."  : %s ");
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

            $validation_msg = true;
        }

        if ($this->form_validation->run() == FALSE)
		{
		  $this->_add_form($validation_msg);
        }
        else
        {
           $country_id               = $this->input->post('country_id');
           $discount_percentage      = $this->input->post('discount_percentage');
           $product_limit_per_order  = $this->input->post('product_limit_per_order');
           $payment_methods          = $this->input->post('payment_method');
           $max_orders_per_day       = $this->input->post('max_orders_per_day');
           $price                   = $this->input->post('price', true);
           $image                    = $this->input->post('image', true);

           $data = array(
                            'country_id'                => $country_id,
                            'unix_time'                 => time(),
                            'discount_percentage'       => $discount_percentage,
                            'product_limit_per_order'   => $product_limit_per_order,
                            'max_orders_per_day'        => $max_orders_per_day,
                            'price'                     => $price,
                            'image'                     => $image
                        );

            if($this->customer_groups_model->insert_customer_groups($data))
            {

                $last_insert_id = $this->db->insert_id();
                $title          = $this->input->post('title');
                $description    = $this->input->post('description');

                foreach($languages as $lang_id)
                {
                    $customer_groups_translation_data = array(
                                                        'customer_group_id' => $last_insert_id ,
                                                        'title'             => $title[$lang_id],
                                                        'description'       => $description[$lang_id],
                                                        'lang_id'           => $lang_id ,
                                                     );
                    $this->customer_groups_model->insert_customer_groups_translation($customer_groups_translation_data);
                }

                if(count($payment_methods) > 0)
                {
                    foreach($payment_methods as $method_id)
                    {

                        $payment_data = array(
                                                'customer_group_id' => $last_insert_id,
                                                'payment_method_id' => $method_id,
                                                'unix_time'         => time()
                                             );

                        $this->customer_groups_model->insert_group_payment_option($payment_data);
                    }
                }


                $this->session->set_flashdata('success',lang('success'));

                redirect('users/admin_customer_groups/', 'refresh');
            }
        }
     }

     private function _add_form($validation_msg)
     {
        $this->_js_and_css_files();

        $lang_id = $this->data['active_language']->id;
        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/add/";

        $countries                  = $this->countries_model->get_countries( $this->lang_row->id);
        $payment_methods            = $this->payment_methods_model->get_payment_options($lang_id);

        $countries_options          = array();
        $countries_options[null]    = lang('choose');

        foreach($countries as $row)
        {
            $countries_options[$row->id] = $row->name;
        }

        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');;
        }

        $this->data['payment_methods']   = $payment_methods;

        $this->data['countries_options'] = $countries_options;
        $this->data['content'] = $this->load->view('customer_groups', $this->data, true);
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
                $customer_group_id = $this->input->post('customer_groups_id');
                $languages         = $this->input->post('lang_id');

                foreach($languages as $lang_id)
                {
                    $this->form_validation->set_rules('title['.$lang_id.']', lang('title'), 'required');
                }

                $this->form_validation->set_rules('country_id', lang('country'), 'required');
                //$this->form_validation->set_rules('discount_percentage', lang('discount_percentage'), 'required|decimal');
                $this->form_validation->set_rules('product_limit_per_order', lang('product_limit_per_order'), 'required|integer');

                $this->form_validation->set_message('required', lang('required') ."  : %s ");
                $this->form_validation->set_message('decimal', lang('decimal_required') ."  : %s ");
                $this->form_validation->set_message('integer', lang('integer_required') ."  : %s ");
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
                $validation_msg = true;
            }

            if($this->form_validation->run() == FALSE)
    		{
    		   $this->_edit_form($id, $validation_msg);
            }
            else
            {
               $country_id               = $this->input->post('country_id');
               $discount_percentage      = $this->input->post('discount_percentage');
               $product_limit_per_order  = $this->input->post('product_limit_per_order');
               $title                    = $this->input->post('title');
               $description              = $this->input->post('description');
               $payment_methods          = $this->input->post('payment_method');
               $max_orders_per_day       = $this->input->post('max_orders_per_day');
               $price                    = $this->input->post('price', true);
               $image                    = $this->input->post('image', true);

               $data      = array(
                                    'country_id'                => $country_id,
                                    'unix_time'                 => time(),
                                    'discount_percentage'       => $discount_percentage,
                                    'product_limit_per_order'   => $product_limit_per_order,
                                    'max_orders_per_day'        => $max_orders_per_day,
                                    'price'                     => $price,
                                    'image'                     => $image
                                  );

                $this->customer_groups_model->update_customer_groups($customer_group_id,$data);

                foreach($languages as $lang_id)
                {
                    $customer_groups_translation_data = array(
                        'title'       => $title[$lang_id],
                        'description' => $description[$lang_id]
                        );
                    $this->customer_groups_model->update_customer_groups_translation($customer_group_id,$lang_id,$customer_groups_translation_data);
                }

                //payment options
                $this->customer_groups_model->delete_group_payment_options($customer_group_id);
                if(!empty($payment_methods))
                {
                    foreach($payment_methods as $method_id)
                    {

                        $payment_data = array(
                                                'customer_group_id' => $customer_group_id,
                                                'payment_method_id' => $method_id,
                                                'unix_time'         => time()
                                             );

                        $this->customer_groups_model->insert_group_payment_option($payment_data);
                    }
                }

                $this->session->set_flashdata('success',lang('updated_successfully'));
                redirect('users/admin_customer_groups/','refresh');
            }
        }
     }

     private function _edit_form($id, $validation_msg)
     {
        $this->_js_and_css_files();

        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/edit/".$id;
        $this->data['id']           = $id;
        $lang_id                    = $this->data['active_language']->id;

        $general_data               = $this->customer_groups_model->get_customer_groups_result($id);
        $data                       = $this->customer_groups_model->get_customer_groups_translation_result($id);
        $user_payment_options       = $this->customer_groups_model->get_group_payment_options($id, $lang_id);

        $filtered_data              = array();
        $countries_options          = array();

        foreach($data as $row)
        {
            $filtered_data[$row->lang_id] = $row;
        }

        $countries                  = $this->countries_model->get_countries( $this->lang_row->id);

        $countries_options[NULL]       = lang('choose');

        foreach($countries as $row)
        {

            $countries_options[$row->id] = $row->name;
        }

        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }

        $this->data['payment_methods']      = $user_payment_options;
        $this->data['countries_options']    = $countries_options;
        $this->data['general_data']         = $general_data;
        $this->data['data']                 = $filtered_data;

        $this->data['content']              = $this->load->view('customer_groups', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
     }

     public function read($id, $display_lang_id)
     {

        $id              = intval($id);
        $display_lang_id = intval($display_lang_id);

        if($id && $display_lang_id)
        {
            $data = $this->customer_groups_model->get_row_data($id,$display_lang_id);
            $allowed_payment_methods = $this->customer_groups_model->available_group_payment_options($id, $display_lang_id);
            $methods = '';

            foreach($allowed_payment_methods as $method)
            {
                $methods .= $method->name.', ';
            }
            $row_data = array(
                                 lang('title')                   => $data->title,
                                 lang('country')                 => $data->country,
                                 lang('discount_percentage')     => $data->discount_percentage,
                                 lang('product_limit_per_order') => $data->product_limit_per_order,
                                 lang('unix_time')               => date('Y-m-d',$data->unix_time),
                                 lang('max_orders_per_day')      => $data->max_orders_per_day,
                                 lang('payment_methods')         => $methods
                              );

            $this->data['row_data'] = $row_data;

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
        $customer_group_id = $this->input->post('row_id', true);
        $must_not_delete_ids = array(1, 2, 3);
        $error_msg = '';

        if(is_array($customer_group_id))
        {

            $ids_array = array();

            foreach($customer_group_id as $row)
            {
                if(!in_array($row['value'], $must_not_delete_ids))
                {
                  $ids_array[] = $row['value'];
                }
                else {
                  $error_msg = ('cant_delete');
                  break;
                }
            }
        }
        else
        {
            if(!in_array($customer_group_id, $must_not_delete_ids))
            {
              $ids_array = array($customer_group_id);
            }
            else {
              $error_msg = ('cant_delete');
            }

        }

        if($error_msg != '')
        {
          echo $error_msg;
        }
        else
        {
          $has_users = $this->customer_groups_model->check_group_users($ids_array);

          if($has_users)
          {
              echo lang('cant_delete_group_has_users');
          }
          else
          {
              $this->customer_groups_model->delete_customer_groups_data($ids_array);
              echo '1';
          }
        }
    }

}
