<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Admin_shipping_companies extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        require(APPPATH . 'includes/global_vars.php');
        $this->load->model('companies_model');
    }

     private function _js_and_css_files()
     {
        $this->data['css_files'] = array();

        $this->data['js_files']  = array(
            //Date Range Picker
            'global/plugins/bootstrap-daterangepicker/moment.min.js',

            );

        $this->data['js_code'] = "";
    }


    public function index()
    {
        $lang_id = $this->data['active_language']->id;

        $this->data['count_all_records']    = $this->companies_model->get_count_all_compinies($lang_id);
        $this->data['data_language']        = $this->lang_model->get_active_data_languages();

        $this->data['columns']              = array(
                                                     lang('company_name'),
                                                     lang('service_name'),
                                                     //lang('cost')        ,
                                                     lang('logo')        ,
                                                     lang('active')
                                                   );

        //$this->data['actions']              = array( 'delete'=>lang('delete'));

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


        $grid_data       = $this->companies_model->get_compinies_data($lang_id, $limit, $offset, $search_word, $order_by, $order_state);

        $db_columns      = array(
                                 'id'           ,
                                 'name'         ,
                                 'service_name' ,
                                 //'cost'         ,
                                 'logo'         ,
                                 'active'
                                );

       $this->data['hidden_fields'] = array('id','sort');

       $new_grid_data = array();

        foreach($grid_data as $key =>$row)
        {

            foreach($db_columns as $column)
            {
                if($column == 'name')
                {
                    $new_grid_data[$key][$column] ="<a href='".base_url()."shipping/admin_shipping_companies/edit/".$row->id."' class=''>$row->name</a>";
                }
                else if($column == 'logo')
                {
                    $logo = '';

                    if($row->logo != '')
                    {
                        $logo = "<a href='".$this->data['images_path'].$row->logo."' class='image-thumbnail'><img src='".$this->data['images_path'].$row->logo."' width='80' height='50' /></a>";
                    }

                    $new_grid_data[$key][$column] = $logo;
                }
                elseif($column == 'active')
                {
                    if($row->active == 1)
                    {
                        $value = '<span class="badge badge-success">'.lang('active').'</span>';
                    }
                    else
                    {
                        $value = '<span class="badge badge-danger">'.lang('not_active').'</span>';
                    }

                    $new_grid_data[$key][$column] = $value;
                }
                else
                {
                    $new_grid_data[$key][$column] = $row->{$column};
                }

            }
        }

        $this->data['grid_data']         = $new_grid_data;
        $this->data['count_all_records'] = $this->companies_model->get_count_all_compinies($lang_id, $search_word);
        $this->data['display_lang_id']   = $lang_id;
        $this->data['unset_delete']      = true;

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
            $data  = $this->companies_model->get_row_data($id, $display_lang_id);
            $logo  = '';

            if($data->logo != '')
            {
                $logo = "<a href='".$images_path.$data->logo."' class='image-thumbnail'><img src='".$images_path.$data->logo."' width='80' height='50' /></a>";
            }

            if($data->active == 1)
            {
                $active = '<span class="badge badge-success">'.lang('active').'</span>';
            }
            else
            {
                $active = '<span class="badge badge-danger">'.lang('not_active').'</span>';
            }



            $row_data = array(
                                lang('company_name') => $data->name         ,
                                lang('service_name') => $data->service_name ,
                                lang('logo')         => $logo               ,
                                //lang('cost')         => $data->cost         ,
                                lang('estimated_delivery_time') => $data->estimated_delivery_time.' '.lang('day'),
                                lang('active')       => $active
                             );

            if($data->type == 1)
            {
                $type = 'cost_per_kg';

                $row_data[lang('type')] = $type;
                $row_data[lang('cost')] = $data->cost;
            }
            elseif($data->type == 2)
            {
                $type = 'equation';

                $row_data[lang('type')]             = $type;
                $row_data[('intial_kgs')]    = $data->intial_kgs;
                $row_data[('intial_cost')]          = $data->intial_cost;
                $row_data[('each_kg_cost')]         = $data->each_kg_cost;
            }

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
        $companies_ids = $this->input->post('row_id');

        if(is_array($companies_ids))
        {
            $ids_array = array();

            foreach($companies_ids as $id)
            {
                $ids_array[] = $id['value'];
            }
        }
        else
        {
            $ids_array = array($companies_ids);
        }

        $this->companies_model->delete_company_data($ids_array);
    }

    public function add()
    {
        $validation_msg = false;

        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
        {
            $validation_msg = true;

            $languages = $this->input->post('lang_id');

            foreach($languages as $lang_id)
            {
                $this->form_validation->set_rules('name['.$lang_id.']', lang('company_name'), 'required');
            }

            //$this->form_validation->set_rules('image', lang('thumbnail'), 'required');
            $this->form_validation->set_rules('service_name', lang('service_name'), 'required');
            $this->form_validation->set_rules('cost_calc_type', ('cost_calc_type'), 'required');

            $this->form_validation->set_message('required', lang('required'));
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        }

        if ($this->form_validation->run() == FALSE)
    		{
    		  $this->_add_form($validation_msg);
        }
        else
        {
            $logo          = $this->input->post('image');
            $service_name  = $this->input->post('service_name');
            $company_name  = $this->input->post('name');
            $delivery_time = $this->input->post('estimated_delivery_time');
            $active        = (isset( $_POST['active']))? $this->input->post('active'):0;
            $type          = intval($this->input->post('cost_calc_type', true));

            /**
             * types
             * 1 => cost per kg
             * 2 =>equation
             */

            $cost               = 0;
            $intial_kgs  = 0;
            $intial_cost        = 0;
            $each_kg_cost       = 0;

            if($type == 1 || $type == 3)
            {
                $cost = $this->input->post('cost');
            }
            else if($type == 2)
            {
                $intial_kgs  = $this->input->post('intial_kgs', true);
                $intial_cost        = $this->input->post('intial_cost', true);
                $each_kg_cost       = $this->input->post('each_kg_cost', true);
            }


            $data          = array(
                                    'logo'              => $logo                ,
                                    'service_name'      => $service_name        ,
                                    'active'            => $active              ,
                                    'type'              => $type                ,
                                    'cost'              => $cost                ,
                                    'intial_kgs'        => $intial_kgs          ,
                                    'intial_cost'       => $intial_cost         ,
                                    'each_kg_cost'      => $each_kg_cost        ,
                                    'unix_time'         => time()               ,
                                    'estimated_delivery_time' => $delivery_time
                                  );

            if($this->companies_model->insert_company($data))
            {

                $last_insert_id = $this->db->insert_id();

                foreach($languages as $lang_id)
                {
                    $companies_translation_data = array(
                                                           'name'                => $company_name[$lang_id],
                                                           'lang_id'             => $lang_id,
                                                           'shipping_company_id' => $last_insert_id
                                                       );

                    $this->companies_model->insert_companies_translation($companies_translation_data);
                }

                $_SESSION['success'] = lang('success');
                $this->session->mark_as_flash('success');

                redirect('shipping/admin_shipping_companies/','refresh');
           }
        }
    }

    private function _add_form($validation_msg)
    {
        $this->_js_and_css_files();

        $lang_id = $this->data['active_language']->id;

        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }
        $this->data['mode']         = 'add';
        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/add";
        $this->data['content']      = $this->load->view('shipping_companies', $this->data, true);

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

                $languages  = $this->input->post('lang_id');
                $id         = $this->input->post('id');

                foreach($languages as $lang_id)
                {
                    $this->form_validation->set_rules('name['.$lang_id.']', lang('company_name'), 'required');
                }

                $this->form_validation->set_rules('image', lang('thumbnail'), 'required');
                $this->form_validation->set_rules('service_name', lang('service_name'), 'required');
                $this->form_validation->set_rules('cost_calc_type', ('cost_calc_type'), 'required');

                $this->form_validation->set_message('required', lang('required')." : %s ");
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            }

            if($this->form_validation->run() == FALSE)
    		{
    		   $this->_edit_form($id, $validation_msg);
            }
            else
            {
                $logo          = $this->input->post('image');
                $service_name  = $this->input->post('service_name');
                $company_name  = $this->input->post('name');
                $cost          = $this->input->post('cost');
                $delivery_time = $this->input->post('estimated_delivery_time');
                $active        = (isset( $_POST['active']))? $this->input->post('active'):0;
                $type          = intval($this->input->post('cost_calc_type', true));

            /**
             * types
             * 1 => cost per kg
             * 2 =>equation
             */

            $cost         = 0;
            $intial_kgs   = 0;
            $intial_cost  = 0;
            $each_kg_cost = 0;

            if($type == 1 || $type == 3)
            {
                $cost = $this->input->post('cost');
            }
            else if($type == 2)
            {
                $intial_kgs   = $this->input->post('intial_kgs', true);
                $intial_cost  = $this->input->post('intial_cost', true);
                $each_kg_cost = $this->input->post('each_kg_cost', true);
            }


            $general_data = array(
                                    'logo'              => $logo                ,
                                    'service_name'      => $service_name        ,
                                    'active'            => $active              ,
                                    'type'              => $type                ,
                                    'cost'              => $cost                ,
                                    'intial_kgs'        => $intial_kgs          ,
                                    'intial_cost'       => $intial_cost         ,
                                    'each_kg_cost'      => $each_kg_cost        ,
                                    'estimated_delivery_time' => $delivery_time
                                  );

                $this->companies_model->update_company($id, $general_data);

                $name = $this->input->post('name');

                foreach($languages as $lang_id)
                {
                    $companiy_translation_data = array(
                                                        'name' => $name[$lang_id]
                                                      );

                    $this->companies_model->update_company_translation($id ,$lang_id ,$companiy_translation_data);
               }

                $_SESSION['success'] = lang('success');
                $this->session->mark_as_flash('success');

                redirect('shipping/admin_shipping_companies/', 'refresh');
            }
        }


    }

    private function _edit_form($id, $validation_msg)
    {
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }

        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/edit/" . $id;
        $this->data['id']           = $id;

        $general_data               = $this->companies_model->get_company_row($id);
        $data                       = $this->companies_model->get_company_translation_result($id);
        $lang_id                    = $this->data['active_language']->id;
        $languages                  = $this->data['data_languages'];

        $filtered_data   = array();

        foreach($data as $row)
        {
            $filtered_data[$row->lang_id] = $row;
        }

        $this->data['general_data']    = $general_data;
        $this->data['data']            = $filtered_data;
        $this->data['mode']            = 'edit';

        $this->data['content']         = $this->load->view('shipping_companies', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
    }

/************************************************************************/
}
