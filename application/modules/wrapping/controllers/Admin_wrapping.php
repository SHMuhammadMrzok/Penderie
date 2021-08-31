<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Admin_wrapping extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        require(APPPATH . 'includes/global_vars.php');

        $this->load->model('admin_wrapping_model');
        $this->load->model('colors_model');
        $this->load->model('users/users_model');
    }

    private function _js_and_css_files()
    {

    }

    public function index()
    {
        $lang_id = $this->data['active_language']->id;

        $this->data['count_all_records']    = $this->admin_wrapping_model->get_count_all_data($lang_id);
        $this->data['data_language']        = $this->lang_model->get_active_data_languages();

        $this->data['columns']              = array(
                                                     //lang('type')   ,
                                                     lang('colors') ,
                                                     lang('cost')   ,
                                                     lang('active')
                                                   );

        $this->data['orders']               = array(
                                                     //lang('type'),
                                                     lang('colors')
                                                   );

        $this->data['actions']              = array( 'delete'=>lang('delete'));

        $this->data['search_fields']        = array(
                                                     lang('colors')
                                                   );

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


        $grid_data       = $this->admin_wrapping_model->get_grid_data($lang_id, $limit, $offset, $search_word, $order_by, $order_state);

        $db_columns      = array(
                                 'id'       ,
                                 //'type'     ,
                                 'color'    ,
                                 'cost'     ,
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
                    if($row->{$column} == 0)
                    {
                        $new_grid_data[$key][$column] = '<span class="badge badge-danger">'.lang('not_active').'</span>';
                    }
                    elseif($row->{$column} == 1)
                    {
                        $new_grid_data[$key][$column] = '<span class="badge badge-success">'.lang('active').'</span>';
                    }

               }
               else if($column == 'type')
               {
                    if($row->type == 1)
                    {
                        $type = lang('wrapping');
                    }
                    else if($row->type == 2)
                    {
                        $type = lang('ribbon');
                    }
                    else if($row->type == 3)
                    {
                        $type = lang('box');
                    }

                    $new_grid_data[$key][$column] = $type;
               }
               else
               {
                    $new_grid_data[$key][$column] = $row->{$column};
               }
            }
        }


        $this->data['grid_data']         = $new_grid_data;
        $this->data['count_all_records'] = $this->admin_wrapping_model->get_count_all_data($lang_id, $search_word);
        $this->data['display_lang_id']   = $lang_id;

        $output_data = $this->load->view('Admin/grid/grid_data',$this->data, true);
        $count_data  = $this->data['count_all_records'];

        echo json_encode(array($output_data, $count_data, $search_word));
    }

    public function read($id,$display_lang_id)
    {
        $id = intval($id);

        if($id)
        {
            $data = $this->admin_wrapping_model->get_wrapping_data($id, $display_lang_id);

            if($data)
            {
                if($data->active == 1)
                {
                    $active_value = lang('active');
                    $class        = 'success';
                }
                else
                {
                    $active_value = lang('not_active');
                    $class        = 'danger';
                }

                if($data->type == 1)
                {
                    $type = lang('wrapping');
                }
                else
                {
                    $type = lang('ribbon');
                }

                $row_data = array(
                                    //lang('type')   => $type         ,
                                    lang('colors') => $data->color  ,
                                    lang('cost')   => $data->cost   ,
                                    lang('active') => '<span class="badge badge-' . $class . '">' . $active_value . '</span>'
                                 );

                if($data->image != '')
                {
                    $row_data[lang('thumbnail')] = '<img src="'.$images_path.$data->image.'" style="width: 150px; height: 100px;" />';
                }

                $this->data['row_data'] = $row_data;

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

    public function delete()
    {
        $ids = $this->input->post('row_id');

        if(is_array($ids))
        {

            $ids_array = array();

            foreach($ids as $id)
            {
                $ids_array[] = $id['value'];
            }
        }
        else
        {
            $ids_array = array($ids);
        }

        $this->admin_wrapping_model->delete_wrapping_data($ids_array);


    }

   /**************************************************************************/

    public function add()
    {
        $validation_msg = false;

        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
        {
            $validation_msg = true;
            $languages      = $this->input->post('lang_id');

            $this->form_validation->set_rules('cost', lang('cost'), 'required');
            $this->form_validation->set_rules('color_id', lang('colors'), 'required');
            //$this->form_validation->set_rules('type', lang('type'), 'required');
            $this->form_validation->set_rules('image', lang('thumbnail'), 'required');

            $this->form_validation->set_message('required', lang('required')."  : %s ");
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        }

        if ($this->form_validation->run() == FALSE)
    		{
    		  $this->_add_form($validation_msg);
        }
        else
        {

            $cost     = strip_tags($this->input->post('cost', TRUE));
            $color_id = strip_tags($this->input->post('color_id', TRUE));
            $type     = strip_tags($this->input->post('type', TRUE));
            $image    = strip_tags($this->input->post('image', TRUE));
            $active   = isset($_POST['active']) ? '1' : '0';

            // check if added before
            $is_exist = $this->admin_wrapping_model->check_existed_type($color_id, $type);

            if($is_exist)
            {
                $_SESSION['custom_error_msg'] = lang('already_exist');
                $this->session->mark_as_flash('custom_error_msg');
            }
            else
            {
                $general_data = array(
                                        'cost'      => $cost    ,
                                        'active'    => $active  ,
                                        'type'      => $type    ,
                                        'image'     => $image   ,
                                        'color_id'  => $color_id
                                      );

                if($this->admin_wrapping_model->insert_wrapping($general_data))
                {
                    $_SESSION['success'] = lang('success');
                    $this->session->mark_as_flash('success');
                }
                else
                {
                    $_SESSION['error'] = lang('error');
                    $this->session->mark_as_flash('error');
                }
            }

            redirect($this->data['module'] . "/" . $this->data['controller'], 'refresh');
        }
    }

    private function _add_form($validation_msg)
    {
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }

        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/add";

        $colors_array = array();
        $colors = $this->colors_model->get_all_colors_data($this->data['lang_id']);

        foreach($colors as $color)
        {
            $colors_array[$color->id] = $color->name;
        }

        $this->data['colors']   = $colors_array;
        $this->data['content']  = $this->load->view('wrapping_form', $this->data, true);
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
                $languages      = $this->input->post('lang_id');

                $this->form_validation->set_rules('cost', lang('cost'), 'required');
                $this->form_validation->set_rules('color_id', lang('colors'), 'required');
                //$this->form_validation->set_rules('type', lang('type'), 'required');
                $this->form_validation->set_rules('image', lang('thumbnail'), 'required');

                $this->form_validation->set_message('required', lang('required')."  : %s ");
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            }
        }

        if($this->form_validation->run() == FALSE)
    		{
    		   $this->_edit_form($id, $validation_msg);
        }
        else
        {


            $cost     = strip_tags($this->input->post('cost', TRUE));
            $color_id = strip_tags($this->input->post('color_id', TRUE));
            $type     = strip_tags($this->input->post('type', TRUE));
            $image    = strip_tags($this->input->post('image', TRUE));
            $active   = isset($_POST['active']) ? '1' : '0';

            $general_data = $this->admin_wrapping_model->get_wrapping_data($id, $this->data['lang_id']);

            //echo "$general_data->color_id == $color_id && $general_data->type == $type";die();
            if($general_data->color_id == $color_id && $general_data->type == $type)
            {
                $update_row = true;
            }
            else
            {
                // check if added before
                $is_exist = $this->admin_wrapping_model->check_existed_type($color_id, $type);
                if($is_exist)
                {
                    $update_row = false;
                }
                else
                {
                    $update_row = true;
                }
            }



            if(!$update_row)
            {
                $_SESSION['custom_error_msg'] = ('already_exist');
                $this->session->mark_as_flash('custom_error_msg');
            }
            else
            {
               $general_data = array(
                                        'cost'      => $cost    ,
                                        'active'    => $active  ,
                                        'type'      => $type    ,
                                        'image'     => $image   ,
                                        'color_id'  => $color_id
                                      );

              if($this->admin_wrapping_model->update_wrapping($id, $general_data))
              {
                 $_SESSION['success'] = lang('success');
                 $this->session->mark_as_flash('success');
              }
              else
              {
                 $_SESSION['failed'] = lang('failed');
                 $this->session->mark_as_flash('failed');
              }
            }
            redirect($this->data['module'] . "/" . $this->data['controller'], 'refresh');
        }
    }

    private function _edit_form($id, $validation_msg)
    {
        $this->_js_and_css_files();

        $filtered_data = array();
        $colors_array  = array();

        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }

        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/edit/" . $id;
        $this->data['id']           = $id;

        $general_data               = $this->admin_wrapping_model->get_wrapping_data($id, $this->data['lang_id']);
        $colors                     = $this->colors_model->get_all_colors_data($this->data['lang_id']);

        foreach($colors as $color)
        {
            $colors_array[$color->id] = $color->name;
        }

        $this->data['colors']       = $colors_array;
        $this->data['data']         = $filtered_data;
        $this->data['general_data'] = $general_data;

        $this->data['content'] = $this->load->view('wrapping_form', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
    }



/************************************************************************/
}
