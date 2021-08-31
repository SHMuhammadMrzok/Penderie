<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Admin_shipping_cities extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        require(APPPATH . 'includes/global_vars.php');
        $this->load->model('costs_model');
        
        $this->load->library('currency');
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
        
        $this->data['count_all_records']    = $this->costs_model->get_count_all_cities($lang_id);
        $this->data['data_language']        = $this->lang_model->get_active_data_languages();
        
        $this->data['columns']              = array(
                                                     lang('country_name'),
                                                     lang('city_name'),
                                                     //lang('currency')
                                                   );
                                                           
        $this->data['actions']              = array( 'delete'=>lang('delete'));
        
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
        
        
        $grid_data       = $this->costs_model->get_cities_data($lang_id, $limit, $offset, $search_word, $order_by, $order_state);
        
        $db_columns      = array(
                                 'id' ,   
                                 'country',
                                 'city',
                                 //'currency_symbol'
                                );
                       
       $this->data['hidden_fields'] = array('id','sort');
                                           
       $new_grid_data = array();
        
        foreach($grid_data as $key =>$row)
        {
            foreach($db_columns as $column)
            {
                if($column == 'country')
                {
                    $new_grid_data[$key][$column] = "<a href='".base_url()."shipping/admin_shipping_cities/edit/$row->id'>$row->country</a>";
                }
                else
                {
                    $new_grid_data[$key][$column] = $row->{$column};
                }   
            }
        }
        
        $this->data['grid_data']         = $new_grid_data;
        $this->data['count_all_records'] = $this->costs_model->get_count_all_cities($lang_id, $search_word);
        $this->data['display_lang_id']   = $lang_id; 
        
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
            $data  = $this->costs_model->get_city_row_data($id, $display_lang_id);
            $logo  = '';
            
            
             
            
            $row_data = array(
                                lang('country_name')  => $data->country ,
                                lang('shipping_city') => $data->city_name
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
        $costs_ids = $this->input->post('row_id');

        if(is_array($costs_ids))
        {  
            $ids_array = array();
            
            foreach($costs_ids as $id)
            {
                $ids_array[] = $id['value'];
            }
        }
        else
        { 
            $ids_array = array($costs_ids);
        }
        
        $this->costs_model->delete_cities_data($ids_array);
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
                $this->form_validation->set_rules('name['.$lang_id.']', lang('shipping_city'), 'required');
            }
            
            $this->form_validation->set_rules('country_id', lang('country_name'), 'required');
            
            $this->form_validation->set_message('required', lang('required')." : %s ");
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        }
        
        $default_currency_data = $this->currency->get_default_currency_data();
        
        if ($this->form_validation->run() == FALSE)
		{
		  $this->_add_form($validation_msg, $default_currency_data->currency_symbol);
        }
        else
        {
            $name     = $this->input->post('name');
            $country  = $this->input->post('country_id');
             
            $data     = array(
                                'country_id' => $country,
                              );
            
            if($this->costs_model->insert_city($data))
            {
                $last_insert_id = $this->db->insert_id();
                               
                foreach($languages as $lang_id)
                {
                    $translation_data = array(
                                               'name'    => $name[$lang_id],
                                               'lang_id' => $lang_id,
                                               'city_id' => $last_insert_id 
                                            );
                    
                    $this->costs_model->insert_city_translation($translation_data);
                }
                
                $_SESSION['success'] = lang('success');
                $this->session->mark_as_flash('success');
               
                redirect($this->data['module'] . "/" . $this->data['controller'], 'refresh');
           }
        }
    }
    
    private function _add_form($validation_msg, $currency_symbol)
    {
        $this->_js_and_css_files();
        
        $countries_array = array();
        
        $lang_id = $this->data['active_language']->id;
        
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }
        
        $countries = $this->costs_model->get_shipping_costs_result($lang_id);
        
        foreach($countries as $row)
        {
            $countries_array[$row->id] = $row->country;
        }
        
        $this->data['mode']             = 'add';
        $this->data['currency_symbol']  = $currency_symbol;
        $this->data['countries']        = $countries_array;
        $this->data['form_action']      = $this->data['module'] . "/" . $this->data['controller'] . "/add";
        $this->data['content']          = $this->load->view('cities_form', $this->data, true);
        
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
                    $this->form_validation->set_rules('name['.$lang_id.']', lang('shipping_city'), 'required');
                }
                
                $this->form_validation->set_rules('country_id', lang('country_name'), 'required');
                
                $this->form_validation->set_message('required', lang('required').' : %s');
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            }
            
            $default_currency_data = $this->currency->get_default_currency_data();
            
            if($this->form_validation->run() == FALSE)
    		{
    		   $this->_edit_form($id, $validation_msg, $default_currency_data->currency_symbol);
            }
            else
            {
                $name    = $this->input->post('name');
                $country = $this->input->post('country_id');
                
                $general_data = array(
                                        'country_id' => $country
                                      );
            
                $this->costs_model->update_city($id, $general_data);
                
                foreach($languages as $lang_id)
                {
                    $translation_data = array(
                                                    'name' => $name[$lang_id] 
                                                  );
                                                    
                    $this->costs_model->update_city_translation($id, $lang_id, $translation_data);
               }
                
                $_SESSION['success'] = lang('success');
                $this->session->mark_as_flash('success');
                   
                redirect($this->data['module'] . "/" . $this->data['controller'], 'refresh');
            }
        }
        
        
    }
    
    private function _edit_form($id, $validation_msg, $currency_symbol)
    {
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }
        
        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/edit/" . $id;
        $this->data['id']           = $id;
        
        $lang_id                    = $this->data['active_language']->id;
        $languages                  = $this->data['data_languages'];
        $general_data               = $this->costs_model->get_city_row($id); 
        $data                       = $this->costs_model->get_city_translation_result($id);
        $countries                  = $this->costs_model->get_shipping_costs_result($lang_id);
        
        $filtered_data   = array();
        
        foreach($data as $row)
        {
            $filtered_data[$row->lang_id] = $row;
        }
        
        
        foreach($countries as $row)
        {
            $countries_array[$row->id] = $row->country;
        }
        
        $this->data['general_data']    = $general_data;
        $this->data['data']            = $filtered_data;
        $this->data['countries']       = $countries_array;
        $this->data['mode']            = 'edit';
        
        $this->data['content']         = $this->load->view('cities_form', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
    }
    
/************************************************************************/    
}