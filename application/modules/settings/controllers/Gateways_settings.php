<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Gateways_settings extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
         require(APPPATH . 'includes/global_vars.php');
       
        $this->load->model('gateways_model');
        
        $this->load->library('encryption');
        $this->config->load('encryption_keys');
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
        
        $this->data['count_all_records']    = $this->gateways_model->get_count_all_settings($lang_id);
        $this->data['data_language']        = $this->lang_model->get_active_data_languages();
        
        $this->data['columns']              = array(
                                                     lang('field'),
                                                     lang('value')
                                                   );
                                                   
        $this->data['orders']                = array(
                                                     lang('field')
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
        
        
        $grid_data       = $this->gateways_model->get_settings_data($lang_id, $limit, $offset, $search_word, $order_by, $order_state);
        $db_columns      = array(
                                 'id' ,   
                                 'field',
                                 'value' 
                                );
                       
       $this->data['hidden_fields'] = array('id');
                                           
       $new_grid_data = array();
       
       $secret_key  = $this->config->item('new_encryption_key');
       $secret_iv   = md5('gateway_set');
        
       foreach($grid_data as $key =>$row)
       { 
            foreach($db_columns as $column)
            {
                if($column == 'value')
                {
                    $dec_value = $this->encryption->decrypt($row->value, $secret_key, $secret_iv);
                    $new_grid_data[$key][$column] = $dec_value;
                }
                else
                {
                    $new_grid_data[$key][$column] = $row->{$column};
                }
            
            }
        }
        
        $this->data['unset_delete']      = 'true';
        $this->data['unset_add']         = 'true';
        $this->data['grid_data']         = $new_grid_data;
        $this->data['count_all_records'] = $this->gateways_model->get_count_all_settings($lang_id, $search_word);
        $this->data['display_lang_id']   = $lang_id; 
        
        $output_data = $this->load->view('Admin/grid/grid_data',$this->data, true);
        $count_data  = $this->data['count_all_records'];
        
        echo json_encode(array($output_data, $count_data, $search_word));
    }
    
     
    public function read($id)
    {
        $id      = intval($id);
        $lang_id = $this->data['lang_id'];
        
        if($id)
        {
            $data       = $this->gateways_model->get_row_data($id, $lang_id);
            $secret_key = $this->config->item('new_encryption_key');
            $secret_iv  = md5('gateway_set');
            $dec_val    = $this->encryption->decrypt($data->value, $secret_key, $secret_iv);
            
            $row_data = array(
                                //lang('field') => $data->field ,
                                //lang('value') => $dec_val
                                $data->field => $dec_val
                             );
                                         
            $this->data['row_data'] = $row_data;
            
            $this->data['content']  = $this->load->view('Admin/grid/read_view', $this->data, true);
            $this->load->view('Admin/main_frame',$this->data);
            
        }
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
                
                $this->form_validation->set_rules('value', lang('value'), 'trim');
                
                //$this->form_validation->set_message('required', lang('required'));
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            }
            
            if($this->form_validation->run() == FALSE)
    		{
    		   $this->_edit_form($id, $validation_msg);
            }
            else
            {
                $value = $this->input->post('value');
                $secret_key = $this->config->item('new_encryption_key');
                $secret_iv  = md5('gateway_set');
                $enc_value  = $this->encryption->encrypt($value, $secret_key, $secret_iv);
                
                
                $general_data = array(
                                        'value' => $enc_value
                                      );
            
                $this->gateways_model->update_settings($id, $general_data);
                
                $_SESSION['success'] = lang('success');
                $this->session->mark_as_flash('success');
                   
                redirect($this->data['module'] . "/" . $this->data['controller'], 'refresh');
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
        $lang_id                    = $this->data['active_language']->id;
        
        $general_data   = $this->gateways_model->get_settings_row($id, $lang_id); 
        $secret_key     = $this->config->item('new_encryption_key');
        $secret_iv      = md5('gateway_set');
        $dec_value      = $this->encryption->decrypt($general_data->value, $secret_key, $secret_iv);
        
        $general_data->{'value'} = $dec_value;
        
        $this->data['general_data']    = $general_data;
        
        $this->data['content']         = $this->load->view('gateway_settings', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
    }
    
/************************************************************************/    
}