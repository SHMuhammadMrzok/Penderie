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
        
        $this->load->model('admin_contact_model');
               
        $this->lang_row = $this->admin_bootstrap->get_active_language_row(); 
    }

    

    public function index()
    {
        $lang_id = $this->data['active_language']->id;
        
        $this->data['count_all_records'] = $this->admin_contact_model->get_count_all_messages();
        $this->data['data_language']     = $this->lang_model->get_active_data_languages();
        
       
        $columns           = array(
                                                     lang('name'),
                                                     lang('email'),
                                                     lang('mobile'),
                                                     lang('message_title'),
                                                     lang('unix_time'),
                                                   );
        
        if($this->admin_bootstrap->has_permission('read')){
            $columns[] = lang('read');
        }
        
        if($this->admin_bootstrap->has_permission('reply')){
            $columns[] = lang('reply');
        }
        $this->data['columns']           = $columns;
            
        $this->data['orders']            = $this->data['columns'];
        
        $this->data['actions']           = array( 'delete'=>lang('delete'));
        $this->data['search_fields']     = array( lang('name'), lang('email'));
        
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
        
        
        $grid_data   = $this->admin_contact_model->get_contact_data($limit,$offset,$search_word,$order_by,$order_state);
        
        $db_columns  = array(
                                'id',   
                                'name',
                                'email',   
                                'mobile',
                                'title',
                                'unix_time',
                                'read'
                            );
        
        if($this->admin_bootstrap->has_permission('read')){
            $db_columns[] = 'read';
        }
        
        if($this->admin_bootstrap->has_permission('reply')){
            $db_columns[] = 'reply';
        }
        
        $this->data['hidden_fields'] = array('id');
        
        $new_grid_data = array();
        
        foreach($grid_data as $key =>$row)
        { 
            foreach($db_columns as $column)
            {
                if($column == 'read')
                {
                    if($row->read == 0)
                    {
                        $new_grid_data[$key][$column] = '<span class="badge badge-danger">'.lang('not_read').'</span>';    
                    }
                    elseif($row->read = 1)
                    {
                        $new_grid_data[$key][$column] = '<span class="badge badge-success">'.lang('read').'</span>';
                    }
                    
                    
                }elseif($column == 'unix_time'){
                    
                    $new_grid_data[$key][$column] = date('Y/m/d',$row->unix_time);
                    
                }elseif($column == 'reply'){
                    
                    $new_grid_data[$key][$column] = '<a href="'.base_url().'contact_us/admin/reply/'.$row->id.'" title="'.lang('reply').'"><img src="'.base_url().'assets/template/admin/img/Forward.png" title="'.lang('reply').'" /></a>';
                    
                }  else{
                    
                    $new_grid_data[$key][$column] = $row->{$column};
                }
                
                
            }
        }
        
        $this->data['grid_data']          = $new_grid_data; 
        $this->data['unset_edit']         = true;
        $this->data['count_all_records']  = $this->admin_contact_model->get_count_all_messages($lang_id,$search_word);
        
        $this->data['display_lang_id']    = $lang_id;
         
        
        $count_data  = $this->data['count_all_records'];
        $output_data = $this->load->view('Admin/grid/grid_data',$this->data, true);
        
        echo json_encode(array($output_data, $count_data, $search_word));
     }
     
     public function reply($msg_id)
     {
        $read_data = array ('read' => 1);
        $this->admin_contact_model->update_row_data($msg_id, $read_data);
            
        $data['msg'] = $this->admin_contact_model->get_row_data($msg_id);
        
        $this->data['content']  = $this->load->view('reply', $data, true);
        $this->load->view('Admin/main_frame',$this->data);
        
        
        
     }
     
     public function send_replay()
     {
        $this->load->library('notifications');
        
        $email   = array();
        $msg_id  = intval($this->input->post('msg_id'));
        $replay  = $this->input->post('replay');
        $data    = $this->admin_contact_model->get_row_data($msg_id);
        
        $unix_time  = time();
        
        $replay_data = array(
                                'contact_us_id	'   => $msg_id,
                                'contact_us_reply'  => $replay,
                                'admin_id'          => $this->data['user_id'],
                                'unixtime'          => $unix_time
                            );
        
        $this->admin_contact_model->insert_admin_replay($replay_data);
        
        $email[] = $data->email;
        $msg_text = lang('reply').' : '.$replay."<br />=================================<br />".lang('original_msg').' : '.$data->message;
        //echo 'RE:'.$data->title.'<br />'.$msg_text.'<br />';
        //print_r($email);die();
        $this->notifications->send_emails_notifications('RE:'.$data->title, $msg_text, $email);
        
        $this->session->set_flashdata('success',lang('success'));   
        redirect('contact_us/admin/','refresh');
     }
     
     public function read($id,$display_lang_id)
     {
        $id              = intval($id);
        $display_lang_id = intval($display_lang_id);
            
        $get_admin_replaies   = $this->admin_contact_model->get_admin_replay($id);
        
        foreach ($get_admin_replaies as $get_admin_replay_data)
        {
            $admin_name = $this->users_model->get_user($get_admin_replay_data->admin_id);
            
            $this->data['admin_contact_us_reply'][] = array(
                'contact_us_reply'  => $get_admin_replay_data->contact_us_reply,
                'admin_name'        => $admin_name->first_name.' '.$admin_name->last_name,
                'time'              => date('Y/m/d H:i',$get_admin_replay_data->unixtime),
            );
        }
        
        if($id && $display_lang_id)
        {
            $data     = $this->admin_contact_model->get_row_data($id);
            
            $row_data = array(
                                 lang('name')           => $data->name,
                                 lang('email')          => $data->email,
                                 lang('mobile')         => $data->mobile,
                                 lang('message_title')  => $data->title,
                                 lang('message')        => $data->message,
                                 lang('unix_time')      => date('Y/m/d H:i',$data->unix_time),
                                 lang('read')           => lang('read')//$data->read,
                                
                             );
        
            $read_data = array ('read' => 1);
            $this->admin_contact_model->update_row_data($id,$read_data);
            
            //$this->data['row_data'] = $row_data;
            $this->data['contact_us_data'] = $row_data;
            
            $this->data['content']  = $this->load->view('contact_us_read', $this->data, true);
            //$this->data['content']  = $this->load->view('Admin/grid/read_view', $this->data, true);
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
        $ids = $this->input->post('row_id');

        if(is_array($ids))
        { 
            
            $ids_array = array();
            
            foreach($ids as $id)
            {
                $ids_array[] = $id['value'];
            }
        }
        else
        { 
            $ids_array = array($ids);
        }
            
        $this->admin_contact_model->delete_message_data($ids_array);
        
     }  
     
   
    
    
/************************************************************************/    
}