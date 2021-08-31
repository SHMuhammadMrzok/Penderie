<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Admin extends CI_Controller
{
    public $lang_row;
    
    public function __construct()
    {
        parent::__construct();
        
        require(APPPATH . 'includes/global_vars.php');
        
        $this->load->model('vendors_model');
        $this->load->model('users/countries_model');
        $this->load->model('stores/stores_model');
        
        $this->lang_row = $this->admin_bootstrap->get_active_language_row(); 
    }

    

    public function index()
    {   
        $lang_id = $this->data['active_language']->id;
        
        $this->data['count_all_records'] = $this->vendors_model->get_count_all_vendors($lang_id);
        $this->data['data_language']     = $this->lang_model->get_active_data_languages();
        
        $this->data['filters']           = array(
                                                  array(
                                                        'filter_title' => lang('countries_filter')  ,
                                                        'filter_name'  => 'country_id'              ,
                                                        'filter_data'  => $this->countries_model->get_countries($lang_id)
                                                        )
                                          
                                                );
        
        $this->data['columns']           = array(
                                                     lang('country')        ,
                                                     lang('name_of_store')  ,
                                                     lang('title')          ,
                                                     lang('description')
                                                   );
            
        $this->data['orders']            = $this->data['columns'];
        
        $this->data['actions']           = array( 'delete'=>lang('delete'));
        $this->data['search_fields']     = array( lang('vendor'), lang('country_name'), lang('name_of_store'));
        
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
        
        
        $grid_data                  = $this->vendors_model->get_vendors_data($lang_id,$limit,$offset,$search_word,$country_id,$order_by,$order_state);
        
        $db_columns                 = array(
                                             'id'           ,   
                                             'country'      ,
                                             'store_name'   ,
                                             'title'        ,
                                             'description'
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
        
        $this->data['grid_data']                  = $new_grid_data; 
        
        $this->data['count_all_records']  = $this->vendors_model->get_count_all_vendors($lang_id,$search_word,$country_id);
        
        $this->data['display_lang_id']    = $lang_id;
         
        
        $count_data  = $this->data['count_all_records'];
        $output_data = $this->load->view('Admin/grid/grid_data',$this->data, true);
        
        echo json_encode(array($output_data, $count_data, $search_word, $country_id));
     }
     
     public function add()
     {
        $validation_msg = false;
        
        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
        {
            $languages = $this->input->post('lang_id');
        
            foreach($languages as $lang_id)
            {  
                $this->form_validation->set_rules('title['.$lang_id.']', lang('name'), 'required|is_unique[vendors_translation.title]');
            }
            
            $this->form_validation->set_rules('country_id', lang('country'), 'required');
            $this->form_validation->set_rules('store_id', lang('name_of_store'), 'required');
            
            $this->form_validation->set_message('required', lang('required')."  : %s ");
            $this->form_validation->set_message('is_unique', lang('is_unique')."  : %s ");
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            
            $validation_msg = true;
        }
        
        if ($this->form_validation->run() == FALSE)
		{
		  $this->_add_form($validation_msg);
        }
        else
        {
            $country_id = $this->input->post('country_id');
            $store_id   = $this->input->post('store_id');
            
            $data       = array(
                                    'country_id'  => $country_id,
                                    'store_id'    => $store_id
                                );
            
            if($this->vendors_model->insert_vendors($data))
            {
            
                $last_insert_id = $this->db->insert_id();
                $title          = $this->input->post('title');
                $description    = $this->input->post('description');
                               
                foreach($languages as $lang_id)
                {
                    $vendors_translation_data = array(
                                                        'vendor_id'     => $last_insert_id ,
                                                        'title'         => $title[$lang_id],
                                                        'description'   => $description[$lang_id],
                                                        'lang_id'       => $lang_id ,
                                                     );
                    
                    $this->vendors_model->insert_vendors_translation($vendors_translation_data);
                }
                
                $this->session->set_flashdata('success',lang('success'));
               
                redirect('vendors/admin/','refresh');
            }
        }
     }
     
     private function _add_form($validation_msg)
     {
        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/add";
       
        $countries                  = $this->countries_model->get_countries( $this->lang_row->id);
        $stores                     = $this->stores_model->get_all_stores($this->lang_row->id);
        
        $countries_options          = array();
        $stores_options             = array();
        
        $countries_options[null]    = lang('choose');
        $stores_options[null]       = lang('choose');
        
        foreach($countries as $row)
        {
            $countries_options[$row->id] = $row->name;
        }
        
        foreach($stores as $row)
        {
            $stores_options[$row->id] = $row->name;
        }
        
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }
        
        $this->data['countries_options'] = $countries_options;
        $this->data['stores']            = $stores_options;
        
        $this->data['content'] = $this->load->view('tabs', $this->data, true);
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
                $languages = $this->input->post('lang_id');
        
                foreach($languages as $lang_id)
                {
                    $this->form_validation->set_rules('title['.$lang_id.']', lang('name'), 'required');
                }
                
                $this->form_validation->set_rules('country_id', lang('country'), 'required');
                $this->form_validation->set_rules('store_id', lang('name_of_store'), 'required');
                
                $this->form_validation->set_message('required', lang('required')."  : %s ");
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
                
                $validation_msg = true;
            }
            
            if($this->form_validation->run() == FALSE)
    		{
    		   $this->_edit_form($id, $validation_msg);
            }
            else
            {
                $vendor_id  = $id;
                $country_id = $this->input->post('country_id');
                $store_id   = $this->input->post('store_id');
                
                $data       = array(
                                    'country_id' => $country_id,
                                    'store_id'   => $store_id
                                    );
               
                $this->vendors_model->update_vendors($vendor_id, $data);
               
                $title          = $this->input->post('title');
                $description    = $this->input->post('description'); 
                 
                foreach($languages as $lang_id)
                {
                    $vendors_translation_data = array(
                                                        'title'         => $title[$lang_id],
                                                        'description'   => $description[$lang_id],
                                                      );
                                                      
                    $this->vendors_model->update_vendors_translation($vendor_id,$lang_id,$vendors_translation_data);
                }
                
                $this->session->set_flashdata('success',lang('updated_successfully'));
                redirect('vendors/admin/','refresh');   
            }   
        }
     }
     
     private function _edit_form($id, $validation_msg)
     {
        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/edit/".$id;
        $this->data['id']           = $id;
        $general_data               = $this->vendors_model->get_vendors_result($id);
        
        $data                       = $this->vendors_model->get_vendors_translation_result($id);
        $filtered_data              = array();
        
        foreach($data as $row)
        {
            $filtered_data[$row->lang_id] = $row;
        }
        
        $countries                  = $this->countries_model->get_countries( $this->lang_row->id);
        $stores                     = $this->stores_model->get_all_stores($this->lang_row->id);
    
        $countries_options          = array();
        $stores_options             = array();
        
        $countries_options[null]    = lang('choose');
        $stores_options[null]       = lang('choose');
        
        
        foreach($countries as $row)
        {
            $countries_options[$row->id] = $row->name;
        }
        
        foreach($stores as $row)
        {
            $stores_options[$row->id] = $row->name;
        }
        
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }
        
        $this->data['countries_options']    = $countries_options;
        $this->data['stores']               = $stores_options;
        $this->data['data']                 = $filtered_data;
        $this->data['general_data']         = $general_data;
        
        $this->data['content']              = $this->load->view('tabs', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
     }
     
     public function read($id,$display_lang_id)
     {
        $id              = intval($id);
        $display_lang_id = intval($display_lang_id);
        
        if($id && $display_lang_id)
        {
            $data     = $this->vendors_model->get_row_data($id,$display_lang_id);
            $row_data = array(
                                lang('vendor')      => $data->title ,
                                lang('description') => $data->description ,
                                lang('country')     => $data->country
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
        $vendors_ids = $this->input->post('row_id');

        if(is_array($vendors_ids))
        { 
            
            $ids_array = array();
            
            foreach($vendors_ids as $country_id)
            {
                $ids_array[] = $country_id['value'];
            }
        }
        else
        { 
            $ids_array = array($vendors_ids);
        }
            
        $this->vendors_model->delete_vendor_data($ids_array);
        
     }  
     
     
    
/************************************************************************/    
}