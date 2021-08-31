<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Admin_vats extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        require(APPPATH . 'includes/global_vars.php');

        $this->load->model('vats_model');
    }

    public function index()
    {
        $lang_id = $this->data['active_language']->id;

        $this->data['count_all_records'] = $this->vats_model->get_count_all_vats($lang_id);
        $this->data['data_language']     = $this->lang_model->get_active_data_languages();

        $this->data['columns'] = array(
                                         lang('name'),
                                         lang('percent'),
                                         lang('type'),
                                         lang('active')
                                       );

        $this->data['orders'] = $this->data['columns'];
        $this->data['actions'] = array( 'delete'=>lang('delete'));

        $this->data['content'] = $this->load->view('Admin/grid/grid_html', $this->data, true);
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


        $grid_data  = $this->vats_model->get_vats_data($lang_id, $limit, $offset, $search_word, $order_by, $order_state);

        $db_columns = array(
                             'id',
                             'name',
                             'amount',
                             'type',
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
                }elseif($column == 'type')
                {
                  if($row->type == 1)
                  {
                    $new_grid_data[$key][$column] = lang('inclusive_vat');
                  }
                  elseif($row->type == 2)
                  {
                    $new_grid_data[$key][$column] = lang('exclusive_vat');
                  }
                }
                else{

                    $new_grid_data[$key][$column] = $row->{$column};
                }
            }
        }

        $this->data['grid_data'] = $new_grid_data;
        $this->data['count_all_records'] = $this->vats_model->get_count_all_vats($lang_id,$search_word);
        $this->data['display_lang_id']   = $lang_id;

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
            $data     = $this->vats_model->get_row_data($id, $display_lang_id);

            $active_value = '';

            if($data->active == 0)
            {
                $active_value = '<span class="badge badge-danger">'.lang('not_active').'</span>';
            }
            elseif($data->active = 1)
            {
                $active_value = '<span class="badge badge-success">'.lang('active').'</span>';
            }

            if($data->type == 1)
            {
              $type = lang('inclusive_vat');
            }
            elseif($data->type == 2)
            {
              $type = lang('exclusive_vat');
            }

            $row_data = array(
                                lang('name')   => $data->name ,
                                lang('amount') => $data->amount.' %' ,
                                lang('type')   => $type,
                                lang('active') => $active_value
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
        $ids = $this->input->post('row_id');

        if(is_array($ids))
        {

            $ids_array = array();

            foreach($ids as $id)
            {
                $ids_array[] = $country_id['value'];
            }
        }
        else
        {
            $ids_array = array($ids);
        }

        //check if used vats
        $is_used = $this->vats_model->check_if_used_vats($ids_array);

        if(!$is_used)
        {
          $this->vats_model->delete_vat_data($ids_array);
        }
        else {
          echo lang('cant_delete_option_has_produts');
        }

     }


     /*******************************************************************************/

     public function add()
     {
        $validation_msg = false;

        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
        {
            $validation_msg = true;

            $languages      = $this->input->post('lang_id');
            foreach($languages as $lang_id)
            {
                $this->form_validation->set_rules('name['.$lang_id.']' ,lang('name') , 'required');
            }

            $this->form_validation->set_rules('amount' , lang('amount') , 'required');
            $this->form_validation->set_rules('type' , lang('type') , 'required');

            $this->form_validation->set_message('required', lang('required'));
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        }

        if ($this->form_validation->run() == FALSE)
    		{
    		  $this->_add_form($validation_msg);
        }
        else
        {
            $amount = $this->input->post('amount', true);
            $type   = intval($this->input->post('type', true));

            $data = array(
                            'amount' => $amount,
                            'type'   => $type,
                            'active' => isset( $_POST['active']) ? 1:0
                         );

            if($this->vats_model->insert_table_data('vats', $data))
            {
                $vat_id  = $this->db->insert_id();
                $name    = $this->input->post('name', true);

                foreach($languages as $lang_id)
                {
                    $faq_translation_data = array(
                                                    'vat_id'     => $vat_id ,
                                                    'name'   => $name[$lang_id],
                                                    'lang_id'    => $lang_id ,
                                                  );
                    $this->vats_model->insert_table_data('vats_translation', $faq_translation_data);
                }

                $_SESSION['success'] = lang('success');
                $this->session->mark_as_flash('success');

                redirect($this->data['module'] . "/" . $this->data['controller'], 'refresh');
           }
        }
    }

    private function _add_form($validation_msg)
    {
        $this->data['mode'] = 'add';
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }

        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/add";

        $this->data['content']      = $this->load->view('vats_form', $this->data, true);
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

                $languages = $this->input->post('lang_id');

                foreach($languages as $lang_id)
                {
                    $this->form_validation->set_rules('name['.$lang_id.']' ,lang('name') , 'required');
                }

                $this->form_validation->set_rules('amount' , lang('amount') , 'required');
                $this->form_validation->set_rules('type' , lang('type') , 'required');

                $this->form_validation->set_message('required', lang('required'));
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            }

            if($this->form_validation->run() == FALSE)
        		{
        		   $this->_edit_form($id, $validation_msg);
            }
            else
            {
                $amount = $this->input->post('amount', true);
                $type = $this->input->post('type', true);

                $conds = array(
                                'id' => $id
                              );
                $data = array(
                              'amount' => $amount,
                              'type'   => $type,
                              'active' => isset( $_POST['active'])? 1:0
                             );

                $this->vats_model->update_table_data('vats', $conds, $data);

                $name = $this->input->post('name');

                foreach($languages as $lang_id)
                {
                    $trans_conds = array(
                                          'vat_id'  => $id,
                                          'lang_id' => $lang_id
                                        );
                    $translation_data = array(
                                                 'name' => $name[$lang_id]
                                               );

                    $this->vats_model->update_table_data('vats_translation', $trans_conds,$translation_data);
                }

                $_SESSION['success'] = lang('updated_successfully');
                $this->session->mark_as_flash('success');

                redirect($this->data['module'] . "/" . $this->data['controller'],'refresh');
            }
        }
    }

    private function _edit_form($id, $validation_msg)
    {
        $this->data['mode'] = 'edit';
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }

        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/edit/" . $id;
        $this->data['id']           = $id;

        $conds = array('id' => $id);
        $this->data['general_data'] = $this->vats_model->get_table_data('vats', $conds, 'row');

        $trans_conds   = array('vat_id'=>$id);
        $data          = $this->vats_model->get_table_data('vats_translation', $trans_conds, 'result');
        $filtered_data = array();

        foreach($data as $row)
        {
            $filtered_data[$row->lang_id] = $row;
        }
        $this->data['data'] = $filtered_data;

        $this->data['content'] = $this->load->view('vats_form', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
    }



/************************************************************************/
}
