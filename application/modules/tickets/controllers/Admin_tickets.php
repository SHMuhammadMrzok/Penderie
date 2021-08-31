<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Admin_tickets extends CI_Controller
{
    public $stores;
    public $stores_ids;
    
    public function __construct()
    {
        parent::__construct();
        
        require(APPPATH . 'includes/global_vars.php');
       
        $this->load->model('admin_tickets_model');
        $this->load->model('admin_categories_model');
        $this->load->model('admin_status_model');
        $this->load->model('tickets_model');
        $this->load->model('users/user_model');
        
        $this->stores   = $this->admin_bootstrap->get_user_available_stores();
        
        $store_id_array = array();
        
        foreach($this->stores as $store)
        {
            $store_id_array[] = $store->store_id;
        }
        
        $this->stores_ids = $store_id_array;
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
        //$lang_id = $this->data['active_language']->id;
        
        $this->data['count_all_records']    = $this->admin_tickets_model->get_count_all_tickets('', $this->stores_ids);
        $this->data['data_language']        = $this->lang_model->get_active_data_languages();
        
        $this->data['columns']              = array(
                                                     lang('ticket_id')              ,
                                                     lang('title')                  ,
                                                     lang('ticket_department')      ,
                                                     lang('ticket_status')          ,
                                                     lang('ticket_owner')           ,
                                                     lang('order_number')           ,
                                                     lang('assigned_to')            ,
                                                     lang('last_update_unix_time')  ,
                                                     lang('reply')
                                                   );
                                                   
        $this->data['orders']               = $this->data['columns'] ;                                                     
            
        $this->data['actions']              = array( 'delete'=>lang('delete'));
        $this->data['search_fields']        = array( lang('title'));
        $this->data['index_method_id']      = $this->data['method_id'];
        
        $this->data['content']  = $this->load->view('Admin/grid/grid_html', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
    }
    
    public function ajax_list()
    {
        /**************************************/
        $this->stores   = $this->admin_bootstrap->get_user_available_stores($_POST['index_method_id']);
        $store_id_array = array();
        
        foreach($this->stores as $store)
        {
            $store_id_array[] = $store->store_id;
        }
        
        $this->stores_ids = $store_id_array;
        /**************************************/
        
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
        
        
        $grid_data       = $this->admin_tickets_model->get_tickets_data($limit, $offset, $search_word, $order_by, $order_state, $this->stores_ids);
        
        $db_columns      = array(
                                 'id'           ,
                                 'ticket_id'    ,   
                                 'title'        ,
                                 'cat_id'       ,
                                 'status_id'    ,
                                 'user_id'      ,
                                 'order_id'     ,
                                 'assigned_to'  ,
                                // 'last_updated_by',
                                 //'priority',
                                // 'unix_time',
                                 'last_update_unix_time',
                                 'replay'
                                );
                       
       $this->data['hidden_fields'] = array('id');
                                           
       $new_grid_data = array();
        
        foreach($grid_data as $key =>$row)
        { 
            /***********get data********************/
            $ticket_category    = $this->admin_categories_model->get_row_data($row->cat_id, $lang_id);
            $ticket_status      = $this->admin_status_model->get_row_data($row->status_id, $lang_id);
            $user_id            = $this->user_model->get_row_data($row->user_id);
            $assigned_to        = $this->user_model->get_row_data($row->assigned_to);
            $last_updated_by    = $this->user_model->get_row_data($row->last_updated_by);
            /*************************************/
            foreach($db_columns as $column)
            {
                
                if($column == 'cat_id')
                {
                    $new_grid_data[$key][$column] = $ticket_category->title;
                      
                }
                elseif($column == 'status_id')
                {
                    $new_grid_data[$key][$column] = $ticket_status->title;
                }
                elseif($column == 'user_id')
                {
                    if(isset($user_id->username) && ($user_id->username) != ''){
                        $username = $user_id->username;
                    }
                    else
                    {
                        $username = '';
                    }
                    
                    if(isset($user_id->last_name) && ($user_id->last_name) != ''){
                        $last_name = $user_id->last_name;
                    }else{
                        $last_name = '';
                    }
                    $new_grid_data[$key][$column] = $username."  ".$last_name;
                
                }
                elseif($column == 'order_id')
                {
                    $new_grid_data[$key][$column] = '<a href="'.base_url().'orders/admin_order/view_order/'.$row->order_id.'" target="_blank"> #'.$row->order_id.'</a>';
                }
                elseif($column == 'assigned_to')
                {
                    if(!empty($assigned_to))
                    {
                        $new_grid_data[$key][$column] = $assigned_to->username."  ".$assigned_to->last_name;
                    }else{
                         $new_grid_data[$key][$column] = '';
                    }
                }
                elseif($column == 'last_update_unix_time')
                {        
                    $new_grid_data[$key][$column] = date('Y/m/d H:i',$row->last_update_unix_time);
                
                }
                elseif($column == 'replay')
                {        
                    $new_grid_data[$key][$column] = '<a href="'.base_url().'tickets/admin_tickets/reply/'.$row->id.'"><img src="'.base_url().'assets/template/admin/img/Forward.png" title="'.lang('replay').'" /></a>';
                                                           
                }
                elseif($column == 'ticket_id')
                {
                    $new_grid_data[$key][$column] = $row->id;
                }
                else
                {    
                    $new_grid_data[$key][$column] = $row->{$column};
                }
            }
        }
        
        
        $this->data['grid_data']         = $new_grid_data;
        $this->data['count_all_records'] = $this->admin_tickets_model->get_count_all_tickets($search_word, $this->stores_ids);
        $this->data['display_lang_id']   = $lang_id;
       
        $output_data = $this->load->view('Admin/grid/grid_data',$this->data, true);
        $count_data  = $this->data['count_all_records'];
        
        echo json_encode(array($output_data, $count_data, $search_word));
    }
    
    public function reply($id)
    {
        if(is_numeric($id))
        {
            $id = intval($id);
            
            $validation_msg = false;
            
            if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
            {
                $validation_msg = true;
                
                $this->form_validation->set_rules('message_text', lang('message_text'), 'required');
                $this->form_validation->set_rules('status_id', lang('status'), 'required');
                
                $this->form_validation->set_message('required', lang('required'));
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            }
            
            if($this->form_validation->run() == FALSE)
    		{
    		   $this->_form($id, $validation_msg);
            }
            else
            {
                $ticket_id        = $this->input->post('ticket_id');
                $message_text     = $this->input->post('message_text');
                $status           = $this->input->post('status_id');
                $last_updated     = $this->input->post('last_updated'); 
                
                /*********** Upload****************************/
              
                $this->load->library('upload');
                $gallery_path = realpath(APPPATH. '../assets/uploads/tickets_posts');
                
                $config = array();
                $config['upload_path'] = $gallery_path;
                $config['allowed_types'] = 'ppt|pptx|xls|xls|pdf|docs|doc|text|png|jpg|jpeg|tif';
                $config['max_size']      = '0';
                $config['overwrite']     = FALSE;
                
                $files_name = '';
                $files      = $_FILES;
                
                $cpt        = count($_FILES['userfile']['name']);
                
                
                for($i=0; $i<$cpt; $i++)
                {
            
                    $_FILES['userfile']['name']     = $files['userfile']['name'][$i];
                    $_FILES['userfile']['type']     = $files['userfile']['type'][$i];
                    $_FILES['userfile']['tmp_name'] = $files['userfile']['tmp_name'][$i];
                    $_FILES['userfile']['error']    = $files['userfile']['error'][$i];
                    $_FILES['userfile']['size']     = $files['userfile']['size'][$i];    
            
                    $this->upload->initialize($config);
                   if ( ! $this->upload->do_upload())
                    {
                        $error['error'] = $this->upload->display_errors();
                        
                   
                   }
                   else
                   {     
                       $file_data   = $this->upload->data();
                       $files_name .= $file_data['file_name']." , ";
                   }
            
                }
                
                $files_name = trim($files_name," , ");
                
                $ticket_posts_data = array(
                                            'ticket_id'   => $ticket_id ,
                                            'user_id'     => $last_updated ,
                                            'post_text'   => $message_text ,
                                            'attachments' => (isset($files_name))? $files_name : '' ,
                                            'unix_time'   => time()
                                          );
                
                $tickets_data = array(
                                       'status_id'               => $status ,
                                       'last_updated_by'         => $last_updated ,
                                       'last_update_unix_time'   => time()
                                     );
                                   
                $this->tickets_model->insert_ticket_post($ticket_posts_data);
                $this->tickets_model->update_ticket_status($ticket_id , $tickets_data);
                
                //-->>Send notification
                
                $this->load->library('notifications');
                
                $owner_data  = $this->tickets_model->get_ticket_owner_data($ticket_id);
                $ticket_data = $this->admin_tickets_model->get_ticket_details($ticket_id); 
                
                $data       = array(
                                      'username'          => $owner_data->username,
                                      'logo_path'         => base_url().'assets/template/admin/img/logo.png',
                                      'admin_ticket_link' => base_url().'tickets/admin_tickets/read/' . $ticket_id . '/' . $this->data['active_language']->id,
                                      'user_ticket_link'  => base_url().'tickets/tickets/ticket_details/' . $ticket_id,
                                      'reply_text'      => $message_text
                                   );
                                   
                $emails[] = $owner_data->email;
                $phone    = $owner_data->phone;
                
                $this->notifications->create_notification('ticket_reply', $data, $emails, $phone, $ticket_data->store_id);
                
                redirect('tickets/admin_tickets/','refresh');
            }
        }
        
        
    }
    
    private function _form($ticket_id, $validation_msg)
    {
        if($validation_msg)
        {
            $data['validation_msg'] = lang('fill_required_fields');
        }
        
        $lang_id = $this->data['active_language']->id;  
        $user    = $this->ion_auth->user()->row();
        
        $data['ticket_id'] = $ticket_id;
        $ticket_data       = $this->admin_tickets_model->get_ticket_details($ticket_id);
        
        if(in_array($ticket_data->store_id, $this->stores_ids))
        {
            $status_result = $this->admin_status_model->get_ticket_status_translation_result($lang_id); 
                
            $options[NULL] = lang('choose');
            
            foreach($status_result as $row)
            {
                $options[$row->ticket_status_id] = $row->title;
            }
            
            $data['ticket']         = $ticket_data;
            $data['options']        = $options;
            $data['last_updated']   = $user->id;
        }
        else
        {
            $data['error_msg'] = lang('no_store_permission');
        }
        $this->data['content']  = $this->load->view('reply', $data, true);
        $this->load->view('Admin/main_frame', $this->data);
    }
     
     
    public function read($id, $display_lang_id)
    {
        $id              = intval($id);
        $display_lang_id = intval($display_lang_id);
        
        if($id && $display_lang_id)
        {
            $data = $this->admin_tickets_model->get_row_data($id,$display_lang_id);
            
            if($data) 
            {
                
                /***********get data********************/
                if(in_array($data->store_id, $this->stores_ids))
                {
                    $ticket_posts = $this->tickets_model->get_ticket_posts($id);
                    $ticket_category    = $this->admin_categories_model->get_row_data($data->cat_id, $display_lang_id);
                    $ticket_status      = $this->admin_status_model->get_row_data($data->status_id, $display_lang_id);
                    $user_id            = $this->user_model->get_row_data($data->user_id);
                    $assigned_to        = $this->user_model->get_row_data($data->assigned_to);
                    $last_updated_by    = $this->user_model->get_row_data($data->last_updated_by);
                    $serials            = $this->admin_tickets_model->get_ticket_serials($id,$data->order_id);
                    
                    $order_serial = '';
                    $this->load->library('encryption');
                    $this->config->load('encryption_keys');
           
                    foreach($serials as $serial)
                    {
                        $secret_key  = $this->config->item('new_encryption_key');
                        $secret_iv   =  md5('serial_iv');//md5($row->unix_time);
                        $enc_serials = $this->encryption->decrypt($serial->serial, $secret_key, $secret_iv);
                        
                        $order_serial .= $enc_serials ." , ";
                    }
                    $order_serial = rtrim($order_serial," , ");
                    /*************************************/
                   if(!empty($assigned_to))
                    {
                      $assignedto = $assigned_to->username."  ".$assigned_to->last_name;  
                    
                    }else{
                        $assignedto = '';
                    } 
                    $attachments = '';
                    if($data->attachments)
                    {
                        $ticket_attachments = explode(" , ", $data->attachments);
                              
                        for($i=0 ; $i < count($ticket_attachments); $i++)
                        {
                           $attachments .= "<a href='".base_url()."tickets/admin_tickets/download/".$ticket_attachments[$i]."' >".$ticket_attachments[$i]."</a><br/>";
                        }
                     }
                    
                    $row_data = array(
                                         lang('ticket_number')          => $data->id,
                                         lang('title')                  => $data->title,
                                         lang('ticket_department')      => $ticket_category->title,
                                         lang('ticket_status')          => $ticket_status->title,
                                         lang('ticket_owner')           => $user_id->username."  ".$user_id->last_name,
                                         lang('order_number')           => $data->order_id,
                                         lang('details')                => $data->details,
                                         lang('assigned_to')            => $assignedto,
                                         lang('last_updated_by')        => $last_updated_by->username."  ".$last_updated_by->last_name,
                                        // lang('priority')               => $data->priority,
                                         lang('unix_time')              => date('Y/m/d H:i',$data->unix_time) ,
                                         lang('last_update_unix_time')  => date('Y/m/d H:i',$data->last_update_unix_time) ,
                                         lang('serial')                 => $order_serial,
                                         lang('attachments')            => $attachments,
                                    );
                                 
                
            
                $this->data['row_data']         = $row_data;
                $ticket_posts['ticket_posts']   = $ticket_posts ;
                $ticket_posts['ticket_id']      = $id ;
                
                /*$this->data['content']          = $this->load->view('Admin/grid/read_view', $this->data, true);
                $this->data['content']         .= $this->load->view('ticket',$ticket_posts, true);
                $this->load->view('Admin/main_frame',$this->data);
                */
                }
                else
                {
                    $this->data['error_msg'] = lang('no_store_permission');
                }
            }
            else
            {
                $this->data['error_msg'] = lang('no_data');
            }
            
            $this->data['content']          = $this->load->view('Admin/grid/read_view', $this->data, true);
            if( isset($ticket_posts) && count($ticket_posts) != 0)
            {
                $this->data['content']         .= $this->load->view('ticket',$ticket_posts, true);
            }
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
        $tickets_ids = $this->input->post('row_id');

        if(is_array($tickets_ids))
        { 
            
            $ids_array = array();
            
            foreach($tickets_ids as $cat_id)
            {
                $ids_array[] = $cat_id['value'];
            }
        }
        else
        { 
            $ids_array = array($tickets_ids);
        }
            
        $this->admin_tickets_model->delete_ticket_data($ids_array);
        
        
    }  
 
   /**************************************************************************/ 
    
    public function edit($id)
    {
        if(is_numeric($id))
        {
            $id = intval($id);
            
            $validation_msg = false;
            
            if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
            {
                $validation_msg = true;
                
                $this->form_validation->set_rules('status_id', lang('status_id'), 'trim|required');
                $this->form_validation->set_rules('assigned_to', lang('assigned_to'), 'trim|required');
                
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
                                    'status_id'             => $this->input->post('status_id'),
                                    'assigned_to'           => $this->input->post('assigned_to'),
                                    'last_update_unix_time' => time()
                                  );
            
            $this->admin_tickets_model->update_ticket_status($id, $general_data);
            
            $_SESSION['success'] = lang('success');
            $this->session->mark_as_flash('success');
            
            redirect('tickets/admin_tickets/','refresh');
        }
    }
    
    private function _edit_form($id, $validation_msg)
    {
        $this->_js_and_css_files();
        $lang_id = $this->data['active_language']->id;
        
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }
        
        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/edit/" . $id;
        $this->data['id']           = $id;
        
        $general_data               = $this->admin_tickets_model->get_row_data($id,$lang_id);
        
        if(in_array($general_data->store_id, $this->stores_ids))
        { 
            $status_result     = $this->admin_status_model->get_ticket_status_translation_result($lang_id); 
            $admin_group_users = $this->admin_tickets_model->get_admin_group_users();
            
            
            foreach($status_result as $row)
            {
                
                $options[$row->ticket_status_id] = $row->title;
            }
            
            $users_options[null]= lang('choose');
            
            foreach($admin_group_users as $user)
            {
                
                $users_options[$user->id] = $user->username."  ".$user->last_name;
            }
            
            $this->data['options']          = $options;
            $this->data['users_options']    = $users_options;
            $this->data['general_data']     = $general_data;
        }
        else
        {
            $this->data['error_msg'] = lang('no_store_permission');
        }
        $this->data['content']      = $this->load->view('admin_tickets', $this->data, true);
        
        $this->load->view('Admin/main_frame',$this->data);
    }
    
    
    
    function download($filename)
    {
        $this->load->helper('file');
        $this->load->helper('download');
        $data = file_get_contents('/assets/uploads/tickets_posts/'.urldecode($filename)); // Read the file's contents
        
        force_download($filename, $data);  
    }
    
    
/************************************************************************/    
}