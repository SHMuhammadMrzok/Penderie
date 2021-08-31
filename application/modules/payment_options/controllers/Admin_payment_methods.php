<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Admin_payment_methods extends CI_Controller
{
    public $lang_row;

    public function __construct()
    {
        parent::__construct();

        require(APPPATH . 'includes/global_vars.php');
        $this->load->model('payment_methods_model');

        $this->lang_row = $this->admin_bootstrap->get_active_language_row();
    }

    /******************* list functions ****************************/

    public function index()
    {
        $lang_id = $this->data['active_language']->id;

        $this->data['count_all_records'] = $this->payment_methods_model->get_count_all_payment_methods();
        $this->data['data_language']     = $this->lang_model->get_active_data_languages();

        $this->data['columns']           = array(
                                                  lang('payment_method_name'),
                                                  lang('min_order_value'),
                                                  lang('order_status'),
                                                  lang('extra_fees_percent'),
                                                  lang('extra_fees_amount'),
                                                  lang('thumbnail'),
                                                  lang('active'),
                                                );

        //$this->data['actions']           = array( 'delete'=>lang('delete'));
        $this->data['search_fields']     = array( lang('name') );


        $this->data['content']           = $this->load->view('Admin/grid/grid_html', $this->data, true);
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

        $offset  = ($active_page-1) * $limit;

        if(isset($_POST['search_word']) || trim($_POST['search_word']) == '')
        {
            $search_word = $this->input->post('search_word');
        }
        else
        {
            $search_word = '';
        }



        $grid_data                  = $this->payment_methods_model->get_payment_methods_data($lang_id, $limit, $offset, $search_word);

        $db_columns                 = array(
                                             'id',
                                             'name',
                                             'min_order_value',
                                             'order_status_id',
                                             'extra_fees_percent',
                                             'extra_fees',
                                             'image',
                                             'active'
                                           );

        $this->data['hidden_fields'] = array('id');

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


                }elseif($column == 'image'){
                    if($row->image)
                    {
                      $new_grid_data[$key][$column] = "<a href='".$this->data['images_path'].$row->image."' class='image-thumbnail' ><img src='".$this->data['images_path'].$row->image."' width='150' height='50'  /></a>";
                    }else{
                        $new_grid_data[$key][$column] = $row->{$column};
                    }

                }
                elseif($column == 'order_status_id')
                {
                    $status = $this->payment_methods_model->get_order_status_name($row->order_status_id, $lang_id);
                    $new_grid_data[$key][$column] = $status;
                }
                else
                {
                    $new_grid_data[$key][$column] = $row->{$column};
                }
            }
        }

        $this->data['grid_data']          = $new_grid_data;

        $this->data['count_all_records']  = $this->payment_methods_model->get_count_all_payment_methods($search_word);
        $this->data['display_lang_id']    = $lang_id;

        $this->data['unset_delete'] = true;

        $count_data  = $this->data['count_all_records'];
        $output_data = $this->load->view('Admin/grid/grid_data',$this->data, true);

        echo json_encode(array($output_data, $count_data, $search_word));
     }


     public function read($id,$display_lang_id)
     {
        $id              = intval($id);
        $display_lang_id = intval($display_lang_id);

        if($id && $display_lang_id)
        {
           $data                = $this->payment_methods_model->get_row_data($id, $display_lang_id);
           $order_status_name   = $this->payment_methods_model->get_order_status_name( $data->order_status_id , $display_lang_id);

           if($data->active == 0)
            {
                $active_value = '<span class="badge badge-danger">'.lang('not_active').'</span>';
            }
            elseif($data->active = 1)
            {
                $active_value = '<span class="badge badge-success">'.lang('active').'</span>';
            }

            $row_data = array(

                                lang('payment_method_name')     => $data->name                          ,
                                lang('min_order_value')         => $data->min_order_value               ,
                                lang('order_status')            => $order_status_name                   ,
                                lang('extra_fees_percent')      => $data->extra_fees_percent            ,
                                lang('extra_fees_amount')       => $data->extra_fees                    ,
                                lang('image')                   => "<a href='".base_url()."assets/uploads/".$data->image."' class='image-thumbnail' ><img src='".base_url()."assets/uploads/".$data->image."' width='150' height='50'  /></a>",
                                lang('unix_time')               => date('Y-m-d H:i:s',$data->unix_time) ,
                                lang('active')                  => $active_value
                             );

            $this->data['row_data']     = $row_data;

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
        $acounts_ids = $this->input->post('row_id');

        if(is_array($acounts_ids))
        {

            $ids_array = array();

            foreach($acounts_ids as $account_id)
            {
                $ids_array[] = $account_id['value'];
            }
        }else{

            $ids_array = array($acounts_ids);
        }

        $this->payment_methods_model->delete_payment_methods_data($ids_array);
        echo 1 ;
     }

     /***********************ADD & Edit Functions ************************/

     private function _js_and_css_files()
    {
        $this->data['css_files'] = array();

        $this->data['js_files']  = array(
            //TouchSpin
            'global/plugins/fuelux/js/spinner.min.js',
            'global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js',
            'global/plugins/bootstrap-touchspin/bootstrap.touchspin.js',

            );


        $this->data['js_code'] = 'ComponentsPickers.init()';
    }

    public function add()
    {
        $this->_js_and_css_files();
        $validation_msg = false;

        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
        {
            $this->_js_and_css_files();
            $languages = $this->input->post('lang_id');

            foreach($languages as $lang_id)
            {
                $this->form_validation->set_rules('name['.$lang_id.']', lang('payment_method_name'), 'trim|required');
            }

            $this->form_validation->set_rules('min_order_value', lang('min_order_value'), 'required');
            $this->form_validation->set_rules('order_status_id', lang('order_status'), 'required');
            $this->form_validation->set_rules('extra_fees_percent', lang('extra_fees_percent'), 'required');
            $this->form_validation->set_rules('extra_fees', lang('extra_fees_amount'), 'required');
            $this->form_validation->set_rules('image', lang('thumbnail'), 'required');

            $this->form_validation->set_message('required',lang('required').'  : %s  ');
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

            $validation_msg = true;
        }

        if ($this->form_validation->run() == FALSE)
		{
		  $this->_add_form($validation_msg);
        }
        else
        {
            $max_sort = $this->payment_methods_model->get_payment_max_sort_value();

	        $payment_methods_data = array(
                                            'min_order_value'    => $this->input->post('min_order_value') ,
                                            'order_status_id'    => $this->input->post('order_status_id'),
                                            'extra_fees_percent' => $this->input->post('extra_fees_percent'),
                                            'extra_fees'         => $this->input->post('extra_fees'),
                                            'image'              => $this->input->post('image'),
                                            'active'             => (isset( $_POST['active']))? $this->input->post('active'):0,
                                            'unix_time'          => time(),
                                            'sort'               => $max_sort+1
                                          );

            if($this->payment_methods_model->insert_payment_methods_data($payment_methods_data))
            {
                $last_insert_id = $this->db->insert_id();
                $name           = $this->input->post('name', true);
                $description    = $this->input->post('description', true);

                foreach($languages as $lang_id)
                {
                    $payment_methods_translation_data = array(
                                                            'payment_method_id' => $last_insert_id ,
                                                            'name'              => $name[$lang_id],
                                                            'description'       => $description[$lang_id],
                                                            'lang_id'           => $lang_id
                                                          );
                    $this->payment_methods_model->insert_payment_methods_translation($payment_methods_translation_data);
                }

                $this->session->set_flashdata('success',lang('success'));
                redirect('payment_options/admin_payment_methods/','refresh');

            }
        }
    }

    private function _add_form($validation_msg)
    {
        $this->_js_and_css_files();
        $lang_id                    = $this->lang_row->id;
        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/add";
        $order_status               = $this->payment_methods_model->get_order_status($lang_id);

        $order_status_options = array();

        foreach($order_status as $row)
        {
            $order_status_options[$row->status_id] = $row->name;
        }

        $this->data['order_status_options'] = $order_status_options;

        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }

        $this->data['content']              = $this->load->view('payment_methods', $this->data, true);
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
                $languages      = $this->input->post('lang_id');

                foreach($languages as $lang_id)
                {
                    $this->form_validation->set_rules('name['.$lang_id.']', lang('payment_method_name'), 'trim|required');
                }

                $this->form_validation->set_rules('min_order_value', lang('min_order_value'), 'required');
                $this->form_validation->set_rules('order_status_id', lang('order_status'), 'required');
                $this->form_validation->set_rules('extra_fees_percent', lang('extra_fees_percent'), 'required');
                $this->form_validation->set_rules('extra_fees', lang('extra_fees_amount'), 'required');
                $this->form_validation->set_rules('image', lang('thumbnail'), 'required');

                $this->form_validation->set_message('required', lang('required').'  : %s  ');
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
                $validation_msg = true;
            }

            if($this->form_validation->run() == FALSE)
        		{
        		   $this->_edit_form($id, $validation_msg);
            }
            else
            {
                $payment_methods_data = array(
                                                'min_order_value'    => $this->input->post('min_order_value') ,
                                                'order_status_id'    => $this->input->post('order_status_id'),
                                                'extra_fees_percent' => $this->input->post('extra_fees_percent'),
                                                'extra_fees'         => $this->input->post('extra_fees'),
                                                'image'              => $this->input->post('image'),
                                                'active'             => (isset( $_POST['active']))? $this->input->post('active'):0,

                                               );

                $this->payment_methods_model->update_payment_methods_data($id, $payment_methods_data);

                $name         = $this->input->post('name', true);
                $description  = $this->input->post('description', true);

                foreach($languages as $lang_id)
                {
                    $payment_methods_translation_data = array(
                      'name' => $name[$lang_id],
                      'description' => $description[$lang_id]
                    );

                    $this->payment_methods_model->update_payment_methods_translation_data($id,$lang_id, $payment_methods_translation_data);
                }


                $this->session->set_flashdata('success',lang('updated_successfully'));
                redirect('payment_options/admin_payment_methods/','refresh');
            }
        }
    }

    private function _edit_form($id, $validation_msg)
    {
        $display_lang_id = $this->lang_row->id;

        $this->_js_and_css_files();
        $this->data['mode']                  = 'edit';
        $this->data['form_action']           = $this->data['module'] . "/" . $this->data['controller'] . "/edit/".$id;
        $this->data['id']                    = $id;

        $this->data['general_data']          = $this->payment_methods_model->get_row_data($id, $display_lang_id);

        $data                                = $this->payment_methods_model->get_payment_methods_translations_data($id);
        $filtered_data                       = array();

        foreach($data as $row)
        {
            $filtered_data[$row->lang_id] = $row;
        }

        $order_status                        = $this->payment_methods_model->get_order_status($display_lang_id);

        $order_status_options = array();

        foreach($order_status as $row)
        {
            $order_status_options[$row->status_id] = $row->name;
        }

        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }

        $this->data['order_status_options'] = $order_status_options;
        $this->data['data']                 = $filtered_data;
        $this->data['content']              = $this->load->view('payment_methods', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
    }



     public function update()
     {
         $this->_js_and_css_files();

         $id             = intval($this->input->post('id'));

        $languages      = $this->input->post('lang_id');

        foreach($languages as $lang_id)
        {
            $this->form_validation->set_rules('name['.$lang_id.']', lang('payment_method_name'), 'trim|required');

        }

        $this->form_validation->set_rules('min_order_value', lang('min_order_value'), 'required');
        $this->form_validation->set_rules('order_status_id', lang('order_status'), 'required');
        $this->form_validation->set_rules('extra_fees_percent', lang('extra_fees_percent'), 'required');
        $this->form_validation->set_rules('extra_fees', lang('extra_fees_amount'), 'required');
        $this->form_validation->set_rules('image', lang('thumbnail'), 'required');

        $this->form_validation->set_message('required', lang('required').'  : %s  ');
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

        if ($this->form_validation->run() == FALSE)
        {
            $this->data['mode']                  = 'edit';
            $this->data['form_action']           = $this->data['module'] . "/" . $this->data['controller'] . "/update";
            $this->data['id']                    = $id;

            $this->data['general_data']          = $this->payment_methods_model->get_row_data($id);
            $order_status                        = $this->payment_methods_model->get_order_status($lang_id);

            $order_status_options = array();

            foreach($order_status as $row)
            {
                $order_status_options[$row->status_id] = $row->name;
            }

            $this->data['order_status_options'] = $order_status_options;

            $this->data['content']               = $this->load->view('payment_methods', $this->data, true);
            $this->load->view('Admin/main_frame',$this->data);

        }else{

            $payment_methods_data = array(

                                        'min_order_value'    => $this->input->post('min_order_value') ,
                                        'order_status_id'    => $this->input->post('order_status_id'),
                                        'extra_fees_percent' => $this->input->post('extra_fees_percent'),
                                        'extra_fees'         => $this->input->post('extra_fees'),
                                        'image'              => $this->input->post('image'),
                                        'active'             => (isset( $_POST['active']))? $this->input->post('active'):0,

                                       );
            $this->payment_methods_model->update_payment_methods_data($id, $payment_methods_data);

            $name           = $this->input->post('name');

            foreach($languages as $lang_id)
            {
                $payment_methods_translation_data = array('name'              => $name[$lang_id]);

                $this->payment_methods_model->update_payment_methods_translation_data($id,$lang_id, $payment_methods_translation_data);
            }


            $this->session->set_flashdata('success',lang('success'));
            redirect('payment_options/admin_payment_methods/index','refresh');
	   }

     }

/************************************************************************/
}