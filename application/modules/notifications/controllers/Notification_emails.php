<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Notification_emails extends CI_Controller
{
    public   $lang_row;
    
    public function __construct()
    {
        parent::__construct();
        
        require(APPPATH . 'includes/global_vars.php');
        
        $this->load->model('notification_emails_model');
        $this->load->model('stores/stores_model');
        
        $this->lang_row = $this->admin_bootstrap->get_active_language_row(); 
    }

    /**************** List functions **********************/

    public function index()
    {   
        $lang_id = $this->data['active_language']->id;
        
        $this->data['count_all_records'] = $this->notification_emails_model->get_count_all_notifications();
        $this->data['data_language']     = $this->lang_model->get_active_data_languages();
        
       
        $this->data['columns']           = array(
                                                  lang('name'),
                                                  lang('email'),
                                                  lang('active'),
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
        
        
        
        $grid_data                  = $this->notification_emails_model->get_notification_emails_data($limit,$offset,$search_word);
        
        $db_columns                 = array(
                                             'id',   
                                             'name',
                                             'email',
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
                    
                    
                }else{
                    $new_grid_data[$key][$column] = $row->{$column};
                }
                
            }
        }
        
        $this->data['grid_data']          = $new_grid_data; 
        
        $this->data['count_all_records']  = $this->notification_emails_model->get_count_all_notifications($search_word);
         
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
            $data = $this->notification_emails_model->get_row_data($id,$display_lang_id);
            
            if($data->active == 1)
            {
                $active = '<span class="badge badge-success">'.lang('active').'</span>';
            }
            else
            {
                $active = '<span class="badge badge-danger">'.lang('not_active').'</span>';
            }
            
            $row_data = array(
                                lang('name')    => $data->name ,
                                lang('email')   => $data->email ,
                                lang('active')  => $active
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
        }else{ 
            
            $ids_array = array($notification_ids);
        }
            
        $this->notification_emails_model->delete_notification_emails_data($ids_array);
        echo "1";
     }  
     
     /***********************ADD & Edit Functions ************************/
    
     private function _js_and_css_files()
    {
        $this->data['css_files'] = array();
        
        $this->data['js_files']  = array();
        
        
        $this->data['js_code'] = 'ComponentsPickers.init()';
    }
    
    public function add()
    {
        $this->_js_and_css_files();
        $validation_msg = false;
        
        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
        {
            $this->form_validation->set_rules('name', lang('name'), 'required');
            $this->form_validation->set_rules('email' , lang('email') , 'required|valid_email');
            
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
            $name       = $this->input->post('name');
            $email      = $this->input->post('email');
            $active     = $this->input->post('active');
            $store_id   = $this->input->post('store_id');
           
            $notification_emails_data = array(
                                        'name'      => $name    ,
                                        'email'     => $email   ,
                                        'unix_time' => time()   ,
                                        'store_id'  => $store_id,
                                        'active'    => (isset( $_POST['active']))? $this->input->post('active'):0,
                                       );
            
            $this->notification_emails_model->insert_notification_emails_data($notification_emails_data);
       
            $this->session->set_flashdata('success',lang('success'));
            redirect('notifications/notification_emails/','refresh');
        }
    }
    
    private function _add_form($validation_msg)
    {
        $this->_js_and_css_files();
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }
        
        $stores = $this->stores_model->get_all_stores($this->data['lang_id']);
        
        $stores_options     = array();
        $stores_options[0]  = lang('all');
        
        foreach($stores as $store)
        {
            $stores_options[$store->store_id] = $store->name;
        }
        
        $this->data['stores']       = $stores_options;
        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/add";
        $this->data['content']      = $this->load->view('notification_emails', $this->data, true);
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
                $this->form_validation->set_rules('name', lang('name'), 'required');
                $this->form_validation->set_rules('email' , lang('email') , 'required|valid_email');
                
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
                $name       = $this->input->post('name');
                $email      = $this->input->post('email');
                $store_id   = $this->input->post('store_id');
                
                $notification_emails_data = array(
                                            'name'      => $name    ,
                                            'email'     => $email   ,
                                            'unix_time' => time()   ,
                                            'store_id'  => $store_id,
                                            'active'    => (isset( $_POST['active']))? $this->input->post('active'):0,
                                           );
                
                $this->notification_emails_model->update_notification_emails_data($id,$notification_emails_data);
           
                $this->session->set_flashdata('success',lang('updated_successfully'));
                redirect('notifications/notification_emails/','refresh');
            }
        }
     }
     
     private function _edit_form($id, $validation_msg)
     {
        $this->_js_and_css_files();
        
        $this->data['mode']                  = 'edit';
        $this->data['form_action']           = $this->data['module'] . "/" . $this->data['controller'] . "/edit/".$id;
        $this->data['id']                    = $id;
        
        $this->data['general_data']          = $this->notification_emails_model->get_row_data($id);
        
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }
        
        $stores = $this->stores_model->get_all_stores($this->data['lang_id']);
        
        $stores_options     = array();
        $stores_options[0]  = lang('all');
        
        foreach($stores as $store)
        {
            $stores_options[$store->store_id] = $store->name;
        }
        
        $this->data['stores']       = $stores_options;
       
        $this->data['content']               = $this->load->view('notification_emails', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
     }
     
     
/************************************************************************/    
}