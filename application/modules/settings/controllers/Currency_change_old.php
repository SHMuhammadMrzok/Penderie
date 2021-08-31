<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Currency_change extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
         require(APPPATH . 'includes/global_vars.php');
       
        $this->load->model('currency_change_model');
    }
    
     private function _js_and_css_files()
     {
        $this->data['css_files'] = array();
        
        $this->data['js_files']  = array(
            
            //TouchSpin
            'global/plugins/fuelux/js/spinner.min.js',
            'global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js',
            'global/plugins/bootstrap-touchspin/bootstrap.touchspin.js',
            );
        
        $this->data['js_code'] = "ComponentsPickers.init();";
    }

    
    public function index()
    {
        $lang_id = $this->data['active_language']->id;
        
        $this->data['count_all_records']    = $this->currency_change_model->get_count_all_rows();
        $this->data['data_language']        = $this->lang_model->get_active_data_languages();
        
        $this->data['columns']              = array(
                                                     lang('currency'),
                                                     lang('dollar_value'),
                                                     lang('last_update_time')
                                                   );
                                                   
        $this->data['orders']                = array(
                                                     lang('dollar_value')
                                                   );             
                                                                                           
            
        $this->data['actions']              = array();
        
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
        
        
        $grid_data       = $this->currency_change_model->get_currency_change_data($limit, $offset, $search_word, $order_by, $order_state);
        
        $db_columns      = array(
                                 'id' ,   
                                 'currency_symbol',
                                 'dollar_val',
                                 'unix_time' 
                                );
                       
        $this->data['hidden_fields'] = array('id');
                                           
        $new_grid_data = array();
        
        foreach($grid_data as $key =>$row)
        { 
            
            foreach($db_columns as $column)
            {
                if($column == 'unix_time')
                {
                    $new_grid_data[$key][$column] = date('Y/m/d H:i');
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
        $this->data['count_all_records'] = $this->currency_change_model->get_count_all_rows($search_word);
        $this->data['display_lang_id']   = $lang_id; 
        
        $output_data = $this->load->view('Admin/grid/grid_data',$this->data, true);
        $count_data  = $this->data['count_all_records'];
        
        echo json_encode(array($output_data, $count_data, $search_word));
    }
    
     
    public function read()
    {
        
        $lang_id    = $this->data['active_language']->id;
        $read_array = array();
        $data       = $this->currency_change_model->get_currency_change_read_data($lang_id);
        
        foreach($data as $key=>$row)
        {
            $read_array[lang('dollar_value').' '.lang('in').' '.$row->country ] = $row->dollar_val.' '.$row->currency_symbol;
        }
        
        $this->data['row_data'] = $read_array;
        
        $this->data['content']  = $this->load->view('Admin/grid/read_view', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
            
        
    }
     
    public function do_action()
    {
        $action = $this->input->post('action');
        
    }
    
    public function edit()
    {
        $validation_msg = false;
            
        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
        {
            $validation_msg = true;
            
            $country_ids = $this->input->post('country_ids');
            
            foreach($country_ids as $key=>$val)
            {
                $this->form_validation->set_rules('dollar_vals['.$key.']', ('dollar_val'), 'required');
            }
            
            $this->form_validation->set_message('required', lang('required'));
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        }
        
        if($this->form_validation->run() == FALSE)
		{
		   $this->_edit_form($validation_msg);
        }
        else
        {
            $dollar_vals = $this->input->post('dollar_vals');
            $country_ids = $this->input->post('country_ids');
            
            foreach($dollar_vals as $key=>$val)
            {
                $this->currency_change_model->delete_currency_data($country_ids[$key]);
                
                $change_data = array(
                                        'country_id' => $country_ids[$key],
                                        'dollar_val' => $val,
                                        'unix_time'  => time()
                                      );
                
                $this->currency_change_model->insert_dollar_currency_data($change_data);
            }
            
            $_SESSION['success'] = lang('success');
            $this->session->mark_as_flash('success');
               
            redirect('settings/currency_change/', 'refresh');
        }
        
    }
    
    private function _edit_form($validation_msg)
    {
        $this->_js_and_css_files();
        
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }
        
        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/edit/" ;
        $data                       = $this->currency_change_model->get_countries_with_currency_result();
        
        $this->data['data']         = $data;
        $this->data['mode']         = 'edit';
        
        $this->data['content']      = $this->load->view('currency_change_form', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
    }
    
/************************************************************************/    
}