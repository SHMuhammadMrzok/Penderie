<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Languages extends CI_Controller
{
    
    public function __construct()
    {
        parent::__construct();
        
        require(APPPATH . 'includes/global_vars.php');
        
        $this->load->model('languages_model');
    }
    
    public function index()
    {
        $lang_id = $this->data['active_language']->id;
        
        $this->data['count_all_records'] = $this->languages_model->get_count_all_lang_vars($lang_id);
        $this->data['data_language']     = $this->lang_model->get_active_data_languages();
        
        $this->data['columns']           = array(
                                                     lang('language'),
                                                     lang('lang_var'),
                                                     lang('lang_definition')
                                                   );
            
        $this->data['orders']            = array(
                                                     lang('lang_var'),
                                                     lang('lang_definition')
                                                 );
        
        $this->data['actions']           = array();
        $this->data['search_fields']     = array(lang('lang_var'), lang('lang_definition'));
        
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
        
        
        $grid_data       = $this->languages_model->get_lang_vars_data($lang_id,$limit,$offset,$search_word,$order_by,$order_state);
        
        $db_columns      = array(
                                 'id'          ,   
                                 'name'        ,
                                 'lang_var'    ,
                                 'lang_definition'
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
        
        
        $this->data['grid_data']         = $new_grid_data;
        $this->data['count_all_records'] = $this->languages_model->get_count_all_lang_vars($lang_id,$search_word);
        
        $this->data['unset_delete']      = true;
        
        $this->data['display_lang_id']   = $lang_id;
         
        $output_data = $this->load->view('Admin/grid/grid_data',$this->data, true);
        $count_data  = $this->data['count_all_records'];
        
        
        echo json_encode(array($output_data, $count_data, $search_word));
    }
    
    public function read($id,$display_lang_id)
    {
        $id              = intval($id);
        $display_lang_id = intval($display_lang_id);
        
        if($id && $display_lang_id)
        {
            $data     = $this->languages_model->get_row_data($id,$display_lang_id);
            
            $row_data = array(
                                lang('lang_var')        => $data->lang_var,
                                lang('lang_definition') => $data->lang_definition ,
                                lang('language')        => $data->language 
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
                $lang_var_id    = intval($this->input->post('lang_var_id'));
                $languages      = $this->input->post('lang_id');
                
                foreach($languages as $lang_id)
                { 
                    $this->form_validation->set_rules('lang_definition['.$lang_id.']', lang('lang_definition').$lang_id, 'trim|required');  
                }
                
                $this->form_validation->set_message('required', lang('required'));
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            }
        }
        
        if($this->form_validation->run() == FALSE)
		{
		   $this->_edit_form($id, $validation_msg);
        }
        else
        {
            $lang_definition = $this->input->post('lang_definition');
           
            foreach($languages as $lang_id)
            {
                $lang_translation_data = array(  'lang_definition' => $lang_definition[$lang_id] );
                $this->languages_model->update_lang_translation($lang_var_id,$lang_id,$lang_translation_data);
            }
            
            $_SESSION['success'] = lang('success');
            $this->session->mark_as_flash('success');
            
            redirect('Languages/languages/','refresh');
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
        $data                       = $this->languages_model->get_lang_result($id);
        $general_data               = $this->languages_model->get_general_lang_data($id);
        $filtered_data              = array();
        
        foreach($data as $row)
        {
            $filtered_data[$row->lang_id] = $row;
        }

        $this->data['data']         = $filtered_data;
        $this->data['general_data'] = $general_data;
        $this->data['content']      = $this->load->view('tabs', $this->data, true);
        
        $this->load->view('Admin/main_frame',$this->data);
    }
    
   
 
/************************************************************************/    
}