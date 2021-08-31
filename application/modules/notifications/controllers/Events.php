<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Events extends CI_Controller
{
    public $lang_row;
    public function __construct()
    {
        parent::__construct();
        
        require(APPPATH . 'includes/global_vars.php');
        
        $this->load->model('events_model');
        $this->load->model('templates_model');
        $this->load->model('users/users_model');
        
        $this->lang_row = $this->admin_bootstrap->get_active_language_row(); 
    }

    /**************** List functions **********************/
    
    private function _js_and_css_files()
    {
        $this->data['css_files'] = array();
        
        $this->data['js_files']  = array();
        
        
        $this->data['js_code'] = 'ComponentsPickers.init()';
    }
    
    
    public function index()
    {   
        $lang_id = $this->data['active_language']->id;
        
        $this->data['count_all_records'] = $this->events_model->get_count_all_events($lang_id);
        $this->data['data_language']     = $this->lang_model->get_active_data_languages();
        
       
        $this->data['columns'] = array(
                                          lang('event'),
                                          lang('name'),
                                          lang('user_sms_notification'),
                                          lang('user_email'),
                                          lang('admin_notification'),
                                          lang('admin_sms'),
                                          lang('admin_email'),
                                          lang('active'),
                                      );
                                                
        $this->data['orders'] = array(
                                          lang('event'),
                                          lang('template_id'),
                                          lang('name'),
                                          lang('active'),
                                      );    
                                                   
        $this->data['search_fields'] = array(  lang('event_name'), lang('event'));
        
        $this->data['content'] = $this->load->view('Admin/grid/grid_html', $this->data, true);
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
        
        $grid_data    = $this->events_model->get_events_data($lang_id,$limit,$offset,$search_word,$order_by,$order_state);
        
        $db_columns   = array(
                                 'id',   
                                 'event',
                                 'name',
                                 'enable_sms',
                                 'enable_email',
                                 'enable_admin',
                                 'enable_admin_sms',
                                 'enable_admin_email',                                             
                                 'active'
                             );
                       
        $this->data['hidden_fields'] = array('id');
        
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
                elseif($column == 'enable_admin')
                {
                    if($row->enable_admin == 0)
                    {
                        $new_grid_data[$key][$column] = '<span class="badge badge-danger">'.lang('no').'</span>';    
                    }
                    elseif($row->enable_admin = 1)
                    {
                        $new_grid_data[$key][$column] = '<span class="badge badge-success">'.lang('yes').'</span>';
                    }
                    
                }
                elseif($column == 'enable_sms')
                {
                    if($row->enable_sms == 0)
                    {
                        $new_grid_data[$key][$column] = '<span class="badge badge-danger">'.lang('no').'</span>';    
                    }
                    elseif($row->enable_sms = 1)
                    {
                        $new_grid_data[$key][$column] = '<span class="badge badge-success">'.lang('yes').'</span>';
                    }
                }
                elseif($column == 'enable_email')
                {
                    if($row->enable_email == 0)
                    {
                        $new_grid_data[$key][$column] = '<span class="badge badge-danger">'.lang('no').'</span>';    
                    }
                    elseif($row->enable_email = 1)
                    {
                        $new_grid_data[$key][$column] = '<span class="badge badge-success">'.lang('yes').'</span>';
                    }
                }
                elseif($column == 'enable_admin_sms')
                {
                    if($row->enable_admin_sms == 0)
                    {
                        $new_grid_data[$key][$column] = '<span class="badge badge-danger">'.lang('no').'</span>';    
                    }
                    elseif($row->enable_admin_sms = 1)
                    {
                        $new_grid_data[$key][$column] = '<span class="badge badge-success">'.lang('yes').'</span>';
                    }
                }
                elseif($column == 'enable_admin_email')
                {
                    if($row->enable_admin_email == 0)
                    {
                        $new_grid_data[$key][$column] = '<span class="badge badge-danger">'.lang('no').'</span>';    
                    }
                    elseif($row->enable_admin_email = 1)
                    {
                        $new_grid_data[$key][$column] = '<span class="badge badge-success">'.lang('yes').'</span>';
                    }
                }
                else
                {
                    $new_grid_data[$key][$column] = $row->{$column};
                }
                
            }
        }
        
        $this->data['grid_data']          = $new_grid_data; 
        
        $this->data['count_all_records']  = $this->events_model->get_count_all_events($lang_id,$search_word);
        
        $this->data['display_lang_id']    = $lang_id; 
        $this->data['unset_add']           = true;
        $this->data['unset_delete']         = true; 
        
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
            $data     = $this->events_model->get_row_data($id,$display_lang_id);
            
            if($data->active == 0)
            {
                $active =  '<span class="badge badge-danger">'.lang('not_active').'</span>';
            
            }elseif($data->active == 1){
                
                 $active = '<span class="badge badge-success">'.lang('active').'</span>';
            }
           
            if($data->enable_admin == 0)
            {
                $enable_admin = '<span class="badge badge-danger">'.lang('no').'</span>';    
            }
            elseif($data->enable_admin = 1)
            {
                $enable_admin = '<span class="badge badge-success">'.lang('yes').'</span>';
            }
            
            if($data->enable_admin_email == 0)
            {
                $enable_admin_email = '<span class="badge badge-danger">'.lang('no').'</span>';    
            }
            elseif($data->enable_admin_email = 1)
            {
                $enable_admin_email = '<span class="badge badge-success">'.lang('yes').'</span>';
            }
            
            if($data->enable_admin_sms == 0)
            {
                $enable_admin_sms = '<span class="badge badge-danger">'.lang('no').'</span>';    
            }
            elseif($data->enable_admin_sms = 1)
            {
                $enable_admin_sms = '<span class="badge badge-success">'.lang('yes').'</span>';
            }
                
            if($data->enable_sms == 0)
            {
                $enable_sms = '<span class="badge badge-danger">'.lang('no').'</span>';    
            }
            elseif($data->enable_sms = 1)
            {
                $enable_sms = '<span class="badge badge-success">'.lang('yes').'</span>';
            }
            
            if($data->enable_email == 0)
            {
                $enable_email = '<span class="badge badge-danger">'.lang('no').'</span>';    
            }
            elseif($data->enable_email = 1)
            {
                $enable_email = '<span class="badge badge-success">'.lang('yes').'</span>';
            }
            
            if($data->admin_template_id != 0)
            {
                $admin_template = $this->templates_model->get_row_data($data->admin_template_id, $display_lang_id);
                $admin_template_name = $admin_template->name;
            }
            else
            {
                $admin_template_name = '';
            }
            
            
               
            $row_data = array(
                                lang('event')           => $data->event,
                                lang('name')            => $data->name,
                                lang('user_template')   => $data->template_name,
                                lang('admin_template')  => $admin_template_name,
                                lang('unix_time')       => date('Y/m/d H:i',$data->unix_time) ,
                                lang('active')          => $active,
                                lang('admin')           => $enable_admin,
                                lang('admin_email')     => $enable_admin_email,
                                lang('admin_sms')       => $enable_admin_sms,
                                lang('enable_sms')      => $enable_sms,
                                lang('enable_email')    => $enable_email,
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
        $events_ids = $this->input->post('row_id');

        if(is_array($events_ids))
        { 
            
            $ids_array = array();
            
            foreach($events_ids as $event_id)
            {
                $ids_array[] = $event_id['value'];
            }
        }else{ 
            
            $ids_array = array($events_ids);
        }
            
        $this->events_model->delete_events_data($ids_array);
        echo "1";
      }  
     
     /***********************ADD & Edit Functions ************************/
    
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
                    $this->form_validation->set_rules("name[$lang_id]", lang('name'), 'required');
                }
                
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
                $user_template_id  = $this->input->post('user_template_id');
                $admin_template_id = $this->input->post('admin_template_id');
                $event             = $this->input->post('event');
               
                $events_data = array( 
                                        'template_id'        => $user_template_id,
                                        'admin_template_id'  => $admin_template_id,
                                        'user_group_id'      => $this->input->post('user_group_id'),
                                        'unix_time'          => time(),
                                        'enable_sms'         => (isset( $_POST['enable_sms']))? $this->input->post('enable_sms'):0,
                                        'enable_email'       => (isset( $_POST['enable_email']))? $this->input->post('enable_email'):0,
                                        'enable_admin'       => (isset( $_POST['enable_admin']))? $this->input->post('enable_admin'):0,
                                        'enable_admin_email' => (isset( $_POST['enable_admin_email']))? $this->input->post('enable_admin_email'):0,
                                        'enable_admin_sms'   => (isset( $_POST['enable_admin_sms']))? $this->input->post('enable_admin_sms'):0,
                                        'active'             => (isset( $_POST['active']))? $this->input->post('active'):0,
                                    );
                
                    $this->events_model->update_events_data($id, $events_data);
                    
                    $languages = $this->input->post('lang_id');
                    $name      = $this->input->post('name');
                
                    foreach($languages as $lang_id)
                    {
                        $events_translation_data = array('name'  => $name[$lang_id]);
                                                         
                        $this->events_model->update_events_translation($id,$lang_id,$events_translation_data);
                    }
                
                    $_SESSION['success'] = lang('success');
                    $this->session->mark_as_flash('success');
                    
                    redirect('notifications/events/','refresh');
            }
        }
     } 
     
     private function _edit_form($id, $validation_msg)
     {
        $this->_js_and_css_files();
        $lang_id                    = $this->data['active_language']->id;
        
        $this->data['mode']         = 'edit';
        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/edit/".$id;
        $this->data['id']           = $id;
        $data                       = $this->events_model->get_events_translation_result($id);
        $this->data['general_data'] = $this->events_model->get_row_data($id,$this->lang_row->id);
        $user_templates             = $this->events_model->get_user_templates($lang_id);
        $admin_templates            = $this->events_model->get_admin_templates($lang_id);
        $user_groups                = $this->users_model->get_user_groups($lang_id);
        
        $user_templates_options     = array();
        $admin_templates_options    = array();
        $user_groups_array          = array();
        $filtered_data              = array();
        
        $user_templates_options[0]  = lang('choose');
        $admin_templates_options[0] = lang('choose');
        $user_groups_array[0]       = lang('choose');
        
        foreach($user_templates as $row)
        {
            $user_templates_options[$row->id] = $row->name;
        }
        
        foreach($admin_templates as $row)
        {
            $admin_templates_options[$row->id] = $row->name;
        }
        
        foreach($data as $row)
        {
            $filtered_data[$row->lang_id] = $row;
        }
        
        foreach($user_groups as $group)
        {
            $user_groups_array[$group->group_id] = $group->name;
        }
        
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }
       
        $this->data['user_templates_options']  = $user_templates_options;
        $this->data['admin_templates_options'] = $admin_templates_options;
        $this->data['data']                    = $filtered_data;
        $this->data['user_groups']             = $user_groups_array;
       
        $this->data['content'] = $this->load->view('events', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
     }
    
/************************************************************************/    
}