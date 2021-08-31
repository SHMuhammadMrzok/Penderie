<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Templates extends CI_Controller
{
    public   $lang_row;
    public function __construct()
    {
        parent::__construct();
        
        require(APPPATH . 'includes/global_vars.php');
        
        $this->load->model('templates_model');
        
        $this->lang_row = $this->admin_bootstrap->get_active_language_row(); 
    }

    /**************** List functions **********************/

    public function index()
    {   
        $lang_id = $this->data['active_language']->id;
        
        $this->data['count_all_records'] = $this->templates_model->get_count_all_templates($lang_id);
        $this->data['data_language']     = $this->lang_model->get_active_data_languages();
        
       
        $this->data['columns']           = array(
                                                  lang('name'),
                                                  lang('unix_time'),
                                                  lang('active')
                                                );
                                                
        $this->data['orders']            = array(
                                                  lang('name'),
                                                  lang('unix_time'),
                                                  lang('active'),
                                                  lang('sort')
                                                );                                                        
        
            
        $this->data['actions']           = array( 'delete'=>lang('delete'));
        
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
        
        
        $grid_data                  = $this->templates_model->get_template_data($lang_id,$limit,$offset,$search_word,$order_by,$order_state);
        
        $db_columns                 = array(
                                             'id',   
                                             'name',
                                             'unix_time',
                                             'active',
                                             'sort'
                                           );
                       
        $this->data['hidden_fields'] = array('id','sort');
        
        $new_grid_data = array();
        
        foreach($grid_data as $key =>$row)
        { 
            
            foreach($db_columns as $column)
            {
                if($column == 'active')
                {
                    if($row->active == 0)
                    {
                        $new_grid_data[$key][$column] = '<span class="badge badge-danger">'.lang('not_active').'</span>';    
                    }
                    elseif($row->active = 1)
                    {
                        $new_grid_data[$key][$column] = '<span class="badge badge-success">'.lang('active').'</span>';
                    }
                    
                    
                }
                elseif($column == 'unix_time')
                {
                    $new_grid_data[$key][$column]     = date('Y-m-d H:i',$row->unix_time); 
                }
                else{
                    $new_grid_data[$key][$column] = $row->{$column};
                }
                
            }
        }
        
        $this->data['grid_data']          = $new_grid_data; 
        $this->data['count_all_records']  = $this->templates_model->get_count_all_templates($lang_id,$search_word);
        $this->data['display_lang_id']    = $lang_id;
        
         
        
        $count_data  = $this->data['count_all_records'];
        $output_data = $this->load->view('Admin/grid/grid_data',$this->data, true);
        
        echo json_encode(array($output_data, $count_data, $search_word));
     }
     
     
     public function read($id,$display_lang_id)
     {
        $id              = intval($id);
        $display_lang_id = intval($display_lang_id);

        if($id && $display_lang_id)
        {
            $data     = $this->templates_model->get_row_data($id,$display_lang_id);
            
                if($data->active == 0)
                {
                    $active_value = '<span class="badge badge-danger">'.lang('not_active').'</span>';    
                }
                elseif($data->active = 1)
                {
                    $active_value = '<span class="badge badge-success">'.lang('active').'</span>';
                }
                
           
            $row_data = array(
                                lang('name')           => $data->name ,
                                lang('unix_time')      => date('Y/m/d' ,$data->unix_time) ,
                                //lang('admin_template') => $data->template,
                                lang('email_title')    => $data->email_title,
                                lang('email_template') => $data->email_template,
                                lang('sms_template')   => $data->sms_template,
                                lang('active')         => $active_value
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
        $notification_ids = $this->input->post('row_id');

        if(is_array($notification_ids))
        { 
            
            $ids_array = array();
            
            foreach($notification_ids as $notification_id)
            {
                $ids_array[] = $notification_id['value'];
            }
        }
        else
        {    
            $ids_array = array($notification_ids);
        }
        
        $is_used_template = $this->templates_model->check_used_template($ids_array);
        
        if($is_used_template)
        {
            echo lang('template_used');
        }
        else
        {   
            $this->templates_model->delete_emails_template_data($ids_array);
        }
        
     }  
     
     /***********************ADD & Edit Functions ************************/
    
    public function add()
    {
        $validation_msg = false;
        
        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
        {
             $languages = $this->input->post('lang_id');
               
            foreach($languages as $lang_id)
            { 
                $this->form_validation->set_rules('name['.$lang_id.']', lang('name'), 'trim|required');
                $this->form_validation->set_rules('email_template_title['.$lang_id.']', lang('email_template_title'), 'trim|required');
                $this->form_validation->set_rules('email_template['.$lang_id.']', lang('email_template') , 'trim|required');
                $this->form_validation->set_rules('sms_template['.$lang_id.']', lang('sms_template'), 'trim|required');  
            }
            
            $this->form_validation->set_message('required', lang('required')."  : %s ");
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        
            $validation_msg = true;
        }
        
        if ($this->form_validation->run() == FALSE)
		{
		  $this->_add_form($validation_msg);
        }
        else
        {
            $template_general_data = array(
                                            'active'     => (isset( $_POST['active']))? $this->input->post('active'):0 ,
                                            'unix_time'  => time()
                                          );
            
            if($this->templates_model->insert_template_data($template_general_data))
            {
                $last_insert_id         = $this->db->insert_id();
                $lang_id	            = $this->input->post('lang_id');
                $template_name          = $this->input->post('name');
                $email_template_title   = $this->input->post('email_template_title');
                $email_template         = $this->input->post('email_template');
                $sms_template           = $this->input->post('sms_template');
                
                
                foreach($languages as $lang_id)
                {
                   
                    $template_translation_data = array(
                                                        'template_id'       => $last_insert_id                 ,
                                                        'name'              => $template_name[$lang_id]        ,
                                                        'email_title'       => $email_template_title[$lang_id] ,
                                                        'email_template'    => $email_template[$lang_id],
                                                        'sms_template'      => $sms_template[$lang_id],
                                                        'lang_id'           => $lang_id ,
                                                     );
                    $this->templates_model->insert_template_translation($template_translation_data);
                    
                     
                }
                
                $_SESSION['success'] = lang('success');
                $this->session->mark_as_flash('success');   
                
                redirect('notifications/templates/','refresh');
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
        $this->data['content']      = $this->load->view('templates', $this->data, true);
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
                $temp_id     = intval($this->input->post('temp_id'));
                $languages   = $this->input->post('lang_id');
                
                foreach($languages as $lang_id)
                { 
                    $this->form_validation->set_rules('name['.$lang_id.']', lang('name'), 'trim|required');
                    //$this->form_validation->set_rules('template['.$lang_id.']', lang('admin_template') , 'trim|required');
                    $this->form_validation->set_rules('email_template_title['.$lang_id.']', lang('email_template_title'), 'trim|required');
                    $this->form_validation->set_rules('email_template['.$lang_id.']', lang('email_template') , 'trim|required');
                    $this->form_validation->set_rules('sms_template['.$lang_id.']', lang('sms_template'), 'trim|required');  
                    
                }
                
                $this->form_validation->set_message('required', lang('required')."  : %s ");
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
                
                $validation_msg = true;
            }
        }
        
        if($this->form_validation->run() == FALSE)
		{
		   $this->_edit_form($id, $validation_msg);
        }
        else
        {
            $active                 = $this->input->post('active');
            $template_name          = $this->input->post('name');
            $email_template_title   = $this->input->post('email_template_title');
            $email_template         = $this->input->post('email_template');
            $sms_template           = $this->input->post('sms_template');
                
            
            $template_data = array(
                                    'unix_time' => time() ,
                                    'active'    => $active
                                  ); 
            
            $this->templates_model->update_template($temp_id,$template_data);
           
            foreach($languages as $lang_id)
            {
                
               
                $template_translation_data = array(
                                                     'name'              => $template_name[$lang_id] , 
                                                     'email_title'       => $email_template_title[$lang_id],
                                                     'email_template'    => $email_template[$lang_id],
                                                     'sms_template'      => $sms_template[$lang_id],
                                                     'lang_id'           => $lang_id ,
                                                   );
                $this->templates_model->update_template_translation($temp_id,$lang_id,$template_translation_data);
                
            }
            
            $_SESSION['success'] = lang('updated_successfully');
            $this->session->mark_as_flash('success');
            
            redirect('notifications/templates/','refresh');
        }
    }
    
    private function _edit_form($id, $validation_msg)
    {
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }
        
        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/edit/".$id;
        $this->data['id']           = $id;
        $lang_id                    = $this->data['active_language']->id;
        $general_data               = $this->templates_model->get_template_row($id); 
        $data                       = $this->templates_model->get_template_translation($id);
        $variables_data             = $this->templates_model->get_event_variables($id, $lang_id);
        //print_r($variables_data);die();
        $filtered_data              = array();
        
        foreach($data as $row)
        {
            $filtered_data[$row->lang_id] = $row;
        }
        
        $this->data['general_data']   = $general_data;
        $this->data['data']           = $filtered_data;
        $this->data['variables_data'] = $variables_data;
        
        $this->data['content']      = $this->load->view('templates', $this->data, true);
        
        $this->load->view('Admin/main_frame',$this->data);
    }
    
/************************************************************************/    
}