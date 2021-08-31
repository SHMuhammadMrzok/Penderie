<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Optional_fields extends CI_Controller
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
        
        $this->data['count_all_records'] = $this->optional_fields_model->get_count_all_optional_fields($lang_id);
        $this->data['data_language']     = $this->lang_model->get_active_data_languages();
        
        $this->data['columns']           = array(
                                                     lang('label'),
                                                     lang('type')
                                                   );
            
        $this->data['orders']            = $this->data['columns'];
        
        $this->data['actions']           = array( 'delete'=>lang('delete'));
        $this->data['search_fields']     = array( lang('label'), lang('type'));
        
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
        
        
        $grid_data  = $this->optional_fields_model->get_optional_fields_data($lang_id, $limit, $offset, $search_word, $order_state);
        
        $db_columns = array(
                             'id',   
                             'label',
                             'type_name'
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
        $this->data['count_all_records']  = $this->optional_fields_model->get_count_all_optional_fields($lang_id,$search_word);
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
            $has_options = $this->input->post('has_options');
            
            $this->form_validation->set_rules('type_id', lang('type'), 'required|greater_than[0]');
        
            foreach($languages as $lang_id)
            {
                $this->form_validation->set_rules('label['.$lang_id.']', lang('label'), 'required');
                
                if($has_options == 1)
                {
                    $this->form_validation->set_rules('option_value', lang('option_value'), 'callback_check_added_options');
                }
            }
            
            
            $this->form_validation->set_message('required', lang('required')."  : %s ");
            $this->form_validation->set_message('greater_than', lang('select_type'));
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            
            $validation_msg  = true;
            $validation_text = validation_errors();
            
        }
        
        if ($this->form_validation->run() == FALSE)
		{
		    $this->_add_form($validation_msg, $validation_text, $has_options);
        }
        else
        {
            $field_type_id = $this->input->post('type_id');
            $priority      = $this->input->post('priority');
            $has_value     = $this->input->post('has_value');
            
            $option_data   = array(
                                    'field_type_id' => $field_type_id,
                                    'has_options'   => $has_options,
                                    'has_value'     => $has_value,
                                    'priority'      => $priority,
                                    'unix_time'     => time()
                                  );
            
            if($this->optional_fields_model->insert_option($option_data))
            {
            
                $option_id = $this->db->insert_id();
                $label     = $this->input->post('label');
                               
                foreach($languages as $lang_id)
                {
                    $option_translation_data = array(
                                                       'optional_field_id' => $option_id ,
                                                       'lang_id'           => $lang_id   ,
                                                       'label'             => $label[$lang_id]
                                                    );
                    
                    $this->optional_fields_model->insert_option_translation($option_translation_data);
                }
                
                if(isset($_POST['has_options']) && $_POST['has_options'] == 1)
                {
                    $option_values   = $this->input->post('option_value');
                    $option_priority = $this->input->post('sort');
                    
                    foreach($option_priority as $key=>$value)
                    {
                        $option_data = array(
                                                'optional_field_id' => $option_id,
                                                'priority'          => $value
                                             );
                        
                        $this->optional_fields_model->insert_optional_field_option($option_data);
                        $option_inserted_id = $this->db->insert_id();
                        
                        foreach($languages as $lang_id)
                        {
                            $option_translation_data = array(
                                                               'optional_field_option_id' => $option_inserted_id ,
                                                               'lang_id'                  => $lang_id ,
                                                               'field_value'              => $option_values[$lang_id][$key]
                                                            );
                            
                            $this->optional_fields_model->insert_optional_field_option_translation($option_translation_data);
                        }
                    }
                }
                
                $this->session->set_flashdata('success', lang('success'));
                redirect('optional_fields/optional_fields/', 'refresh');
            }
        }
     }
     
     private function _add_form($validation_msg, $validation_text, $has_options)
     {
        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/add";
        $types                      = $this->optional_fields_model->get_types($this->lang_row->id);
        
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }
        
        if($validation_text != '')
        {
            $this->data['validation_text'] = $validation_text;
        }
        
        if($has_options == 1)
        {
            $this->data['has_options'] = true;
        }
        
        $this->data['types']   = $types;
        
        $this->data['content'] = $this->load->view('optional_fields_form', $this->data, true);
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
                $has_options = $this->input->post('has_options');
            
                foreach($languages as $lang_id)
                {
                    $this->form_validation->set_rules('label['.$lang_id.']', lang('label'), 'required');
                    
                    if($has_options == 1)
                    {
                        $this->form_validation->set_rules('option_value', lang('option_value'), 'callback_check_added_options');
                    }
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
                $field_type_id = $this->input->post('type_id');
                $priority      = $this->input->post('priority');
                
                $option_data   = array(
                                        'field_type_id' => $field_type_id,
                                        'has_options'   => $has_options,
                                        'priority'      => $priority,
                                        'unix_time'     => time()
                                      );
                
                if($this->optional_fields_model->update_option_field($id, $option_data))
                {
                    $label  = $this->input->post('label');
                                   
                    foreach($languages as $lang_id)
                    {
                        $option_translation_data = array(
                                                           'optional_field_id' => $id     ,
                                                           'lang_id'           => $lang_id,
                                                           'label'             => $label[$lang_id]
                                                        );
                        
                        $this->optional_fields_model->update_option_translation($id, $lang_id, $option_translation_data);
                    }
                    
                    $this->optional_fields_model->delete_option_options($id);
                    
                    if(isset($_POST['has_options']) && $_POST['has_options'] == 1)
                    {
                        $option_values   = $this->input->post('option_value');
                        $option_priority = $this->input->post('sort');
                        
                        foreach($option_priority as $key=>$value)
                        {
                            $option_data = array(
                                                    'optional_field_id' => $id,
                                                    'priority'          => $value
                                                 );
                            
                            $this->optional_fields_model->insert_optional_field_option($option_data);
                            $option_inserted_id = $this->db->insert_id();
                            
                            foreach($languages as $lang_id)
                            {
                                $option_translation_data = array(
                                                                   'optional_field_option_id' => $option_inserted_id ,
                                                                   'lang_id'                  => $lang_id ,
                                                                   'field_value'              => $option_values[$lang_id][$key]
                                                                );
                                
                                $this->optional_fields_model->insert_optional_field_option_translation($option_translation_data);
                            }
                        }
                    }
                }
                
                $this->session->set_flashdata('success', lang('updated_successfully'));
                redirect('optional_fields/optional_fields/', 'refresh');   
            }   
        }
     }
     
     private function _edit_form($id, $validation_msg)
     {
        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/edit/".$id;
        $this->data['id']           = $id;
        
        $filtered_data              = array();
        $option_options_array       = array();
        
        $types                      = $this->optional_fields_model->get_types($this->lang_row->id);
        $general_data               = $this->optional_fields_model->get_optional_field_row($id);
        $data                       = $this->optional_fields_model->get_optional_field_translation_result($id);
        
        if($general_data->has_options == 1)
        {
            $option_options = $this->optional_fields_model->get_optional_field_options_result($id);
            
            foreach($option_options as $row)
            {
                foreach($this->data['data_languages'] as $lang)
                {
                    if($lang->id == $row->lang_id)
                    {
                        $option_options_array[$row->id][$row->lang_id] = $row;
                    }
                    
                }
            }
        }
        
        foreach($data as $row)
        {
            $filtered_data[$row->lang_id] = $row;
        }
        
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }
        
        $this->data['types']          = $types;
        $this->data['option_options'] = $option_options_array;
        $this->data['data']           = $filtered_data;
        $this->data['general_data']   = $general_data;
        
        $this->data['content']        = $this->load->view('optional_fields_form', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
     }
     
     public function read($id, $display_lang_id)
     {
        $id              = intval($id);
        $display_lang_id = intval($display_lang_id);
        
        if($id && $display_lang_id)
        {
            $data     = $this->optional_fields_model->get_row_data($id, $display_lang_id);
            
            $row_data = array(
                                lang('label')    => $data->label ,
                                lang('type')     => $data->type_name ,
                                lang('priority') => $data->priority
                             );
            
            if($data->has_options = 1)
            {
                $option_options = $this->optional_fields_model->get_option_options_data($id, $display_lang_id);
                
                if(count($option_options) != 0)
                {
                    $i = 1;
                    foreach ($option_options as $option)
                    {
                        $row_data[lang('option').$i] = $option->field_value .' / '. lang('priority').' : '. $option->priority;
                        $i++;
                    }
                }
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
        $option_per_product_count = $this->optional_fields_model->count_options_products($ids_array);

        // check sizes option
        if($option_per_product_count > 0 || in_array(1, $ids_array))
        {
            echo lang('cant_delete_option_has_produts');
        }
        else
        {   
            $this->optional_fields_model->delete_optional_field_data($ids_array);
            echo '1';
        }
        
     }
     
     public function check_added_options($options)
     {
        $languages       = $this->input->post('lang_id');
        $option_priority = $this->input->post('sort');
        $options_values  = $this->input->post('option_value');
        
        if(!isset($_POST['option_value']))
        {
            $this->form_validation->set_message('check_added_options', lang('options_required'));
            return FALSE;
        }
        else if(!isset($_POST['sort']) || count(array_filter($_POST['sort'])) == 0)
        {
            $this->form_validation->set_message('check_added_options', ('options_sort_required'));
            return FALSE;
        }
        else
        {
            foreach($option_priority as $key=>$value)
            {
                foreach($languages as $lang_id)
                {
                    if(empty($options_values[$lang_id][$key]))
                    {
                        $errors_array[] = 1;
                    }
                    else
                    {
                        $errors_array[] = 0;
                    }
                }
            }
            
            if(in_array(1, $errors_array))
            {
                $this->form_validation->set_message('check_added_options', lang('option_translation_required'));
                return FALSE;        
            }
            else
            {
                return TRUE;
            }
        }
     }
     
     
     
    
/************************************************************************/    
}