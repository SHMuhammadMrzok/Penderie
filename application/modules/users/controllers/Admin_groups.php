<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Admin_groups extends CI_Controller
{
    public $data = array();
    public $crud;
    
    public function __construct()
    {
        parent::__construct();
        
        require(APPPATH . 'includes/global_vars.php');
        
        $this->load->model('groups_model');
        
        $this->lang_row   = $this->admin_bootstrap->get_active_language_row();
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
        
        $this->data['count_all_records']    = $this->groups_model->get_count_all_groups($lang_id);
        $this->data['data_language']        = $this->lang_model->get_active_data_languages();
        
        $this->data['columns']              = array(
                                                     lang('group_name'),
                                                     lang('description'),
                                                 );
                                                   
        $this->data['orders']                = array(
                                                     lang('group_name'),
                                                     lang('description'),
                                                    );                                                        
            
        $this->data['actions']              = array( 'delete'=>lang('delete'));
        $this->data['search_fields']        = array( lang('group_name'), lang('description') );
        
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
        
        
        $grid_data       = $this->groups_model->get_groups_data($lang_id,$limit,$offset,$search_word,$order_by,$order_state);
        
        $db_columns      = array(
                                 'id'          ,   
                                 'name'        ,
                                 'description' ,
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
        $this->data['count_all_records'] = $this->groups_model->get_count_all_groups($lang_id,$search_word);
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
            $data     = $this->groups_model->get_row_data($id,$display_lang_id);
            
            if($data)
            {
              
                $row_data = array(
                                    lang('group_name')             => $data->name ,
                                    lang('description')          => $data->description ,
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
        $groups_ids = $this->input->post('row_id');

        if(is_array($groups_ids))
        { 
            
            $ids_array = array();
            
            foreach($groups_ids as $group_id)
            {
                $ids_array[] = $group_id['value'];
            }
        }
        else
        { 
            $ids_array = array($groups_ids);
        }
            
        $this->groups_model->delete_group_data($ids_array);
        echo "1";
    }  
 
 //////////////////////////////////////////////////////////
 
    public function add()
    {
        $validation_msg = false;
        
        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
        {
            $validation_msg = true;
            
            $languages = $this->input->post('lang_id');
        
            foreach($languages as $lang_id)
            { 
                $this->form_validation->set_rules('name['.$lang_id.']', lang('name'), 'trim|required');
                $this->form_validation->set_rules('description['.$lang_id.']', lang('description'), 'trim|required');
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
            $group_general_data  = array('id' => NULL);
                                    
            if($this->groups_model->insert_group($group_general_data))
            {
                $last_insert_id = $this->db->insert_id();
                $name           = $this->input->post('name');
                $description    = $this->input->post('description');
                
                foreach($languages as $lang_id)
                {
                    $groups_translation_data = array(
                                                        'group_id'     => $last_insert_id        ,
                                                        'name'         => $name[$lang_id]        ,
                                                        'description'  => $description[$lang_id] ,
                                                        'lang_id'      => $lang_id
                                                     );
                    
                    $this->groups_model->insert_group_translation($groups_translation_data);
                }
                
                $_SESSION['success'] = lang('success');
                $this->session->mark_as_flash('success');
               
                redirect('users/admin_groups/','refresh');
            }
        }
    }
    
    private function _add_form($validation_msg)
    {
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }
        
        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/add";
        
        $this->data['content']           = $this->load->view('groups_tabs', $this->data, true);
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
                
                $group_id        = intval($this->input->post('group_id'));
                $languages       = $this->input->post('lang_id');
                
                foreach($languages as $lang_id)
                { 
                    $this->form_validation->set_rules('name['.$lang_id.']', lang('group_name'), 'trim|required');
                    $this->form_validation->set_rules('description['.$lang_id.']', lang('description'), 'trim|required');  
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
             $name       = $this->input->post('name');
            $description = $this->input->post('description');
            
            foreach($languages as $lang_id)
            {
                $groups_translation_data = array(
                                                    'name'         => $name[$lang_id],
                                                    'description'  => $description[$lang_id],
                                                 );
                                                                
                $this->groups_model->update_group_translation($group_id, $lang_id, $groups_translation_data);
            }
           
            $_SESSION['success'] = lang('updated_successfully');
            $this->session->mark_as_flash('success');
            
            redirect('users/admin_groups/','refresh');
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
        $data                       = $this->groups_model->get_group_translation_result($id);
       
        $filtered_data              = array();
        
        foreach($data as $row)
        {
            $filtered_data[$row->lang_id] = $row;
        }
        
        $this->data['data']              = $filtered_data;
        $this->data['content']           = $this->load->view('groups_tabs', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
    }
    
     

    

}
/* End of file groups.php */
/* Location: ./application/modules/Users/controllers/groups.php */