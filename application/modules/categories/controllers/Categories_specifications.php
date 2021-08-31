<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Categories_specifications extends CI_Controller
{
    public $crud;
    
    public function __construct()
    {
        parent::__construct();
        
        require(APPPATH . 'includes/global_vars.php');
       
        $this->load->model('categories_specifications_model');
        $this->load->model('cat_model');
    }
    
     private function _js_and_css_files()
     {
        $this->data['css_files'] = array(
            'global/plugins/jquery-tags-input/jquery.tagsinput.css',
            );
        
        $this->data['js_files']  = array(
            //Date Range Picker
            'global/plugins/bootstrap-daterangepicker/moment.min.js',
            //Tags
            'tags/tag-it.js',
            );
        
        $this->data['js_code'] = "";
    }
    
    public function index()
    {
        $lang_id = $this->data['active_language']->id;
        
        $this->data['count_all_records']    = $this->categories_specifications_model->get_count_all_categories_specification($lang_id);
        $this->data['data_language']        = $this->lang_model->get_active_data_languages();
        
        $this->data['columns']              = array(
                                                     lang('category'),
                                                     lang('label')
                                                   );
                                                     
        $this->data['actions']              = array( 'delete'=>lang('delete'));
        
        $this->data['search_fields']        = array(lang('label')); 
        
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
        
        if(isset($_POST['filter'])&& isset($_POST['filter_data']))
        {
            $filters      = $this->input->post('filter');
            $filters_data = $this->input->post('filter_data');
        }
        
        $grid_data       = $this->categories_specifications_model->get_cateories_specifications_data($lang_id, $limit, $offset, $search_word);
        
        $db_columns      = array(
                                 'id'           ,   
                                 'cat_name'     ,
                                 'spec_label'
                                );
        
        $this->data['hidden_fields'] = array('id','sort');
                                           
        $new_grid_data = array();
        
        foreach($grid_data as $key =>$row)
        { 
            foreach($db_columns as $column)
            {
                $new_grid_data[$key][$column] = $row->{$column};
            }
        }
        
        
        $this->data['grid_data']         = $new_grid_data;
        $this->data['count_all_records'] = $this->categories_specifications_model->get_count_all_categories_specification($lang_id, $search_word);
        $this->data['display_lang_id']   = $lang_id;
         
        $output_data = $this->load->view('Admin/grid/grid_data',$this->data, true);
        $count_data  = $this->data['count_all_records'];
        
        echo json_encode(array($output_data, $count_data, $search_word));
    }
    
     public function add()
     {
        $this->_js_and_css_files();
        $validation_msg = false;
        
        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
        {
            $languages = $this->input->post('lang_id');
                   
            foreach($languages as $lang_id)
            { 
                $this->form_validation->set_rules('label['.$lang_id.']', lang('label'), 'trim|required');  
            }
            
            $this->form_validation->set_rules('cat_id', lang('category') , 'trim|required');
             
            $this->form_validation->set_message('required', lang('required')." : %s ");
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            
            $validation_msg = true;
        }
        
        if ($this->form_validation->run() == FALSE)
		{
		  $this->_add_form($validation_msg);
        }
        else
        {
            $cat_spec_general_data = array(
                                            'cat_id'    => $this->input->post('cat_id') , 
                                            'unix_time' => time()
                                          );
            
            if($this->categories_specifications_model->insert_cat_spec_data($cat_spec_general_data))
            {
                $last_insert_id = $this->db->insert_id();
                $lang_id	    = $this->input->post('lang_id');
                $cat_spec_label = $this->input->post('label');
                
                
                foreach($languages as $lang_id)
                {
                   
                    $cat_spec_translation_data = array(
                                                        'category_specification_id' => $last_insert_id           ,
                                                        'spec_label'                => $cat_spec_label[$lang_id] ,
                                                        'lang_id'                   => $lang_id 
                                                     );
                    $this->categories_specifications_model->insert_cat_spec_translation($cat_spec_translation_data);
                    
                }
                
                $_SESSION['success'] = lang('success');
                $this->session->mark_as_flash('success'); 
                 
                redirect('categories/categories_specifications/', 'refresh');
            }
        }    
     }
     
     private function _add_form($validation_msg = false)
     {
	    $categories = $this->cat_model->get_categories($this->data['active_language']->id);
        $cats_array = array();
        
        foreach($categories as $cat)
        {
            if($cat->parent_id == 0)
            {
                foreach($categories as $category)
                {
                    if($category->parent_id == $cat->id)
                    {
                        $cats_array["{$cat->name}"][$category->id] = $category->name;
                    }
                }
            }
        }
        
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }
        
        $this->data['options']      = $cats_array;  
	    $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/add";
        $this->data['content']      = $this->load->view('categories_specifications', $this->data, true);
        
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
                $id         = intval($this->input->post('cat_specifications_id'));
                $languages  = $this->input->post('lang_id');
                
                foreach($languages as $lang_id)
                { 
                    $this->form_validation->set_rules('label['.$lang_id.']', lang('label'), 'trim|required');  
                }
                
                $this->form_validation->set_rules('cat_id', lang('category') , 'trim|required');
                
                $this->form_validation->set_message('required', lang('required')." : %s ");
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
                $validation_msg = true;
            }
            
            if($this->form_validation->run() == FALSE)
    		{
    		   $this->_edit_form($id, $validation_msg);
            }
            else
            {
                $cat_spec_general_data = array(
                                                'cat_id'    => $this->input->post('cat_id') , 
                                                'unix_time' => time()
                                              );
                
                $this->categories_specifications_model->update_category_spec($id, $cat_spec_general_data);
                
                $cat_spec_label = $this->input->post('label');
                
                foreach($languages as $lang_id)
                {
                    $cat_spec_translation_data = array(  
                                                        'spec_label' => $cat_spec_label[$lang_id]
                                                       );
                    $this->categories_specifications_model->update_cat_spec_translation($id, $lang_id, $cat_spec_translation_data);
                }
                
                $_SESSION['success'] = lang('updated_successfully');
                $this->session->mark_as_flash('success'); 
                
                redirect('categories/categories_specifications/', 'refresh');
            }
        
        }
    }
    
    private function _edit_form($id, $validation_msg)
    {
        $this->_js_and_css_files();
        
        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/edit/".$id;
        $this->data['id']           = $id;
        $lang_id                    = $this->data['active_language']->id;
        $general_data               = $this->categories_specifications_model->get_category_spec_row($id); 
        $data                       = $this->categories_specifications_model->get_cat_spec_result($id);
        $categories                 = $this->cat_model->get_categories($lang_id);
        
        $filtered_data              = array();
        $cats_array                 = array();
        
        foreach($data as $row)
        {
            $filtered_data[$row->lang_id] = $row;
        }
        
        foreach($categories as $cat)
        {
            if($cat->parent_id == 0)
            {
                foreach($categories as $category)
                {
                    if($category->parent_id == $cat->id)
                    {
                        $cats_array["{$cat->name}"][$category->id] = $category->name;
                    }
                }
            }
        }
        
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }
        
        $this->data['general_data'] = $general_data;
        $this->data['data']         = $filtered_data;
        $this->data['options']      = $cats_array;
        
        $this->data['content'] = $this->load->view('categories_specifications', $this->data, true);
        
        $this->load->view('Admin/main_frame',$this->data);
    }
    
    public function read($id, $display_lang_id=0)
    {
        $id              = intval($id);
        $display_lang_id = intval($display_lang_id);
        
        if($id && $display_lang_id)
        {
            $data = $this->categories_specifications_model->get_row_data($id, $display_lang_id);
            
            if($data)
            {
                $row_data = array(
                                    lang('category') => $data->cat_name ,
                                    lang('label')    => $data->spec_label 
                                 );
                
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
        $categories_spec_ids = $this->input->post('row_id');

        $ids_array      = array();
            
        if(is_array($categories_spec_ids))
        { 
            foreach($categories_spec_ids as $cat_spec_id)
            {
                $ids_array[] = $cat_spec_id['value'];
            }
            
            // check sub cats
            foreach($ids_array as $cat_spec_id)
            {
                $used_labels_count = $this->categories_specifications_model->check_cat_spec_used_label($cat_spec_id);
                
                if($used_labels_count > 0)
                {
                    echo lang('used_label');
                    
                }
                else
                {
                    $this->categories_specifications_model->delete_category_spec_data($ids_array);
                }
            }
        }
        else
        {
            $used_labels_count = $this->categories_specifications_model->check_cat_spec_used_label($categories_spec_ids);
            
            
            if($used_labels_count > 0)
            {
                echo lang('used_label');
            }
            else
            {
                $ids_array = array($categories_spec_ids);
                
                $this->categories_specifications_model->delete_category_spec_data($ids_array);
            }
        }        
    }  
 
/************************************************************************/    
}