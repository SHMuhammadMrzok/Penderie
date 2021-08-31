<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Options_groups extends CI_Controller
{
    public $lang_row;
    
    public function __construct()
    {
        parent::__construct();
        
        require(APPPATH . 'includes/global_vars.php');
        
        $this->load->model('optional_fields_model');
        
        $this->lang_row = $this->admin_bootstrap->get_active_language_row(); 
    }

    

    public function index()
    {
        $lang_id = $this->data['active_language']->id;
        
        $this->data['count_all_records'] = $this->optional_fields_model->get_count_all_options_groups($lang_id);
        $this->data['data_language']     = $this->lang_model->get_active_data_languages();
        
        $this->data['columns']           = array(
                                                     lang('name'),
                                                     lang('limit')
                                                   );
            
        $this->data['orders']            = $this->data['columns'];
        
        $this->data['actions']           = array( 'delete'=>lang('delete'));
        $this->data['search_fields']     = array( lang('name'), lang('type'));
        
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
        
        
        $grid_data  = $this->optional_fields_model->get_optional_fields_groups_data($lang_id, $limit, $offset, $search_word, $order_state);
        
        $db_columns = array(
                             'id',   
                             'name',
                             'group_limit'
                           );
                       
        $this->data['hidden_fields'] = array('id');
        
        $new_grid_data = array();
        
        foreach($grid_data as $key =>$row)
        { 
            foreach($db_columns as $column)
            {
                $new_grid_data[$key][$column] = $row->{$column};   
            }
        }
        
        $this->data['grid_data']          = $new_grid_data; 
        $this->data['count_all_records']  = $this->optional_fields_model->get_count_all_options_groups($lang_id, $search_word);
        $this->data['display_lang_id']    = $lang_id;
        
        $count_data  = $this->data['count_all_records'];
        $output_data = $this->load->view('Admin/grid/grid_data', $this->data, true);
        
        echo json_encode(array($output_data, $count_data, $search_word, $country_id));
     }
     
     public function add()
     {
        $validation_msg  = false;
        $validation_text = '';
        $has_options     = 0;
        
        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
        {
            $languages   = $this->input->post('lang_id');
            
            foreach($languages as $lang_id)
            {
                $this->form_validation->set_rules('name['.$lang_id.']', lang('name'), 'required');
            }
            
            $this->form_validation->set_message('required', lang('required')."  : %s ");
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            
            $validation_msg  = true;
            $validation_text = validation_errors();
            
        }
        
        if ($this->form_validation->run() == FALSE)
		{
		    $this->_add_form($validation_msg, $validation_text);
        }
        else
        {
            $limit = $this->input->post('limit', true);
            
            $group_data   = array(
                                    'group_limit' => $limit ,
                                  );
            
            if($this->optional_fields_model->insert_data('optional_fields_groups', $group_data))
            {
            
                $group_id = $this->db->insert_id();
                $name     = $this->input->post('name', true);
                
                foreach($languages as $lang_id)
                {
                    $group_translation_data = array(
                                                       'group_id' => $group_id ,
                                                       'lang_id'  => $lang_id   ,
                                                       'name'     => $name[$lang_id]
                                                    );
                    
                    $this->optional_fields_model->insert_data('optional_fields_groups_translation', $group_translation_data);
                }
                
                
                $this->session->set_flashdata('success', lang('success'));
                redirect($this->data['module'] . "/" . $this->data['controller'], 'refresh');
            }
        }
     }
     
     private function _add_form($validation_msg)
     {
        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/add";
        $this->data['mode']         = 'add';
        
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }
        
        $this->data['content'] = $this->load->view('groups_form', $this->data, true);
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
                $languages   = $this->input->post('lang_id');
                
                foreach($languages as $lang_id)
                {
                    $this->form_validation->set_rules('name['.$lang_id.']', lang('name'), 'required');
                }
                
                
                $this->form_validation->set_message('required', lang('required')."  : %s ");
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
                
                $validation_msg  = true;
                $validation_text = validation_errors();
            }
            
            if($this->form_validation->run() == FALSE)
    		{
    		   $this->_edit_form($id, $validation_msg);
            }
            else
            {
                $limit = $this->input->post('limit', true);
            
                $group_data   = array(
                                        'group_limit' => $limit ,
                                      );
                $condtions = array('id'=>$id);
                
                if($this->optional_fields_model->update_data('optional_fields_groups', $group_data, $condtions))
                {
                    $name = $this->input->post('name', true);
                                
                    foreach($languages as $lang_id)
                    {
                        $option_translation_data = array(
                                                           'name' => $name[$lang_id]
                                                        );
                        
                        $trans_cond = array(
                                             'group_id' => $id,
                                             'lang_id' => $lang_id
                                           );
                        
                        $this->optional_fields_model->update_data('optional_fields_groups_translation', $option_translation_data, $trans_cond);
                    }
                    
                }
                
                $this->session->set_flashdata('success', lang('updated_successfully'));
                redirect($this->data['module'] . "/" . $this->data['controller'], 'refresh');   
            }   
        }
     }
     
     private function _edit_form($id, $validation_msg)
     {
        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/edit/".$id;
        $this->data['mode']         = 'edit';
        $this->data['id']           = $id;
        
        $filtered_data              = array();
        $option_options_array       = array();
        
        $general_data               = $this->optional_fields_model->get_table_row('optional_fields_groups', $id);
        $data                       = $this->optional_fields_model->get_group_translation_result($id);
        
        foreach($data as $row)
        {
            $filtered_data[$row->lang_id] = $row;
        }
        
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }
        
        $this->data['data']           = $filtered_data;
        $this->data['general_data']   = $general_data;
        
        $this->data['content']        = $this->load->view('groups_form', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
     }
     
     public function read($id, $display_lang_id)
     {
        $id              = intval($id);
        $display_lang_id = intval($display_lang_id);
        
        if($id && $display_lang_id)
        {
            $data     = $this->optional_fields_model->get_group_row_data($id, $display_lang_id);
            
            $row_data = array(
                                lang('name')    => $data->name ,
                                lang('limit')     => $data->group_limit 
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
        $options_ids = $this->input->post('row_id');

        if(is_array($options_ids))
        { 
            
            $ids_array = array();
            
            foreach($options_ids as $id)
            {
                $ids_array[] = $id['value'];
            }
        }
        else
        { 
            $ids_array = array($options_ids);
        }
        
        //check used option 
        $option_per_product_count = $this->optional_fields_model->count_options_products_groups($ids_array);
        
        if($option_per_product_count > 0)
        {
            echo lang('cant_delete_option_has_produts');
        }
        else
        {   
            $this->optional_fields_model->delete_group_data($ids_array);
            echo '1';
        }
        
     }
     
    
/************************************************************************/    
}