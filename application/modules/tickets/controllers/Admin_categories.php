<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Admin_categories extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        require(APPPATH . 'includes/global_vars.php');
       
        $this->load->model('admin_categories_model');
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
        
        $this->data['count_all_records']    = $this->admin_categories_model->get_count_all_categories($lang_id);
        $this->data['data_language']        = $this->lang_model->get_active_data_languages();
        
        $this->data['columns']              = array(
                                                     lang('title'),
                                                     lang('unix_time'),
                                                     lang('active')
                                                   );
                                                   
        $this->data['orders']        = $this->data['columns'] ;                                                     
            
        $this->data['actions']       = array( 'delete'=>lang('delete'));
        $this->data['search_fields'] = array( lang('category'));
        
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
        
        
        $grid_data       = $this->admin_categories_model->get_cateories_data($lang_id,$limit,$offset,$search_word,$order_by,$order_state);
        
        $db_columns      = array(
                                 'id'         ,   
                                 'title'      ,
                                 'unix_time'  ,
                                 'active'     ,
                                );
                       
       $this->data['hidden_fields'] = array('id','sort');
                                           
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
                     
                }elseif($column == 'unix_time'){
                        
                        $new_grid_data[$key][$column] = date('Y/m/d H:i',$row->unix_time);
                
                }else{
                    
                    $new_grid_data[$key][$column] = $row->{$column};
                }
            }
        }
        
        
        $this->data['grid_data']         = $new_grid_data;
        $this->data['count_all_records'] = $this->admin_categories_model->get_count_all_categories($lang_id,$search_word);
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
            $data     = $this->admin_categories_model->get_row_data($id,$display_lang_id);
            
            if($data)
            {
                if($data->active == 1)
            {
                $active_value = lang('active');
            }
            else
            {
                $active_value = lang('not_active');
            }
            
            $row_data = array(
                                lang('title')          => $data->title ,
                                lang('unix_time')      => date('Y/m/d H:i',$data->unix_time) ,
                                lang('active')         => '<span class="badge badge-info">'.$active_value.'</span>'
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
        $categories_ids = $this->input->post('row_id');

        if(is_array($categories_ids))
        { 
            
            $ids_array = array();
            
            foreach($categories_ids as $cat_id)
            {
                $ids_array[] = $cat_id['value'];
            }
        }
        else
        { 
            $ids_array = array($categories_ids);
        }
        
        $tickets_count = $this->admin_categories_model->count_tickets($ids_array);
        
        if($tickets_count > 0)
        {
            echo lang('cant_delete_has_tickets');
        }
        else
        {
            $this->admin_categories_model->delete_category_data($ids_array);
        }
            
        
        
        
    }  
 
   /**************************************************************************/ 
   
   public function add()
    {
        $validation_msg = false;
        
        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
        {
            $validation_msg = true;
            
            $languages = $this->input->post('lang_id');
               
            foreach($languages as $lang_id)
            { 
                $this->form_validation->set_rules('title['.$lang_id.']', lang('title'), 'trim|required');
            }
            
            $this->form_validation->set_message('required', lang('required'));
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        }
        
        if ($this->form_validation->run() == FALSE)
		{
		  $this->_add_form($validation_msg);
        }
        else
        {
            $general_data = array(
                                    'unix_time' => time(),
                                    'active'    => (isset( $_POST['active']))? $this->input->post('active'):0,
                                  );
            
            if($this->admin_categories_model->insert_tickets_categories($general_data))
            {
                $last_insert_id         = $this->db->insert_id();
                $lang_id	            = $this->input->post('lang_id');
                $cat_name               = $this->input->post('title');
                
                
                foreach($languages as $lang_id)
                {
                   
                    $cat_translation_data = array(
                                                    'ticket_cat_id'   => $last_insert_id     ,
                                                    'title'           => $cat_name[$lang_id] ,
                                                    'lang_id'         => $lang_id ,
                                                 );
                    $this->admin_categories_model->insert_tickets_categories_translation($cat_translation_data);
               }
                
                $_SESSION['success'] = lang('success');
                $this->session->mark_as_flash('success');   
                
                redirect('tickets/admin_categories/','refresh');
            }
        }
    }
    
    private function _add_form($validation_msg)
    {
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }
        
        $this->_js_and_css_files();
                
        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/add";
        
        $this->data['content']      = $this->load->view('admin_categories', $this->data, true);
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
                
                $cat_id     = intval($this->input->post('id'));
                $id         = $cat_id;
                $languages  = $this->input->post('lang_id');
                
                foreach($languages as $lang_id)
                { 
                   $this->form_validation->set_rules('title['.$lang_id.']', lang('title'), 'trim|required');  
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
            $general_data = array(
                                    'active'    => (isset( $_POST['active']))? $this->input->post('active'):0,
                                  );
            
            $this->admin_categories_model->update_category($cat_id,$general_data);
            
            $cat_name               = $this->input->post('title');
                        
            foreach($languages as $lang_id)
            {
               
                $cat_translation_data = array(  
                                                'title' => $cat_name[$lang_id] ,
                                             );
                $this->admin_categories_model->update_cat_translation($cat_id,$lang_id,$cat_translation_data);
                
                
            }
            
            $_SESSION['success'] = lang('updated_successfully');
            $this->session->mark_as_flash('success');
            
           redirect('tickets/admin_categories/','refresh');
        }
    }
    
    private function _edit_form($id, $validation_msg)
    {
        $this->_js_and_css_files();
        
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }
        
        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/edit/" . $id;
        $this->data['id']           = $id;
        
        $general_data               = $this->admin_categories_model->get_category_row($id); 
        $data                       = $this->admin_categories_model->get_category_translation_result($id);
        
        $filtered_data              = array();
        
        foreach($data as $row)
        {
            $filtered_data[$row->lang_id] = $row;
        }
        
       
        $this->data['general_data'] = $general_data;
        $this->data['data']         = $filtered_data;
       
        
        $this->data['content']      = $this->load->view('admin_categories', $this->data, true);
        
        $this->load->view('Admin/main_frame',$this->data);
    }
    
    
    
/************************************************************************/    
}