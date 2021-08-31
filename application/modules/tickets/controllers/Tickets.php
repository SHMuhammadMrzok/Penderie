<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Tickets extends CI_Controller
{
    var $data = array();
    public $user_id = '';
    
    public function __construct()
    {
        parent::__construct();
        
        require(APPPATH . 'includes/front_end_global.php');
        $this->load->model('tickets_model');
        $this->load->model('admin_categories_model');
        $this->load->model('admin_tickets_model');
        
        $this->user_id = $this->user_bootstrap->get_user_id();
        
        $this->session->set_userdata('site_redir', current_url());
    }
    
    public function index($page =1)
    {
        $page = intval($page);
        
        if (!$this->ion_auth->logged_in())
        {
            $this->session->set_flashdata('not_allow',lang('please_login_first'));//
            redirect('users/users/user_login','refresh');
            
        }else{
          
            $lang_id        = $this->session->userdata('lang_id');
            
            $per_page       = 20 ;
            $offset         = ($page -1)*$per_page;
            
            $tickets_count  = $this->tickets_model->get_count_all_tickets($this->user_id);
            /****************pagination**************/
            
            $this->load->library('pagination');
           
            $config['base_url']         = base_url() . $this->router->fetch_module() . '/' . $this->router->class . '/' . $this->router->method;
            $config['use_page_numbers'] = TRUE;
            $config['total_rows']       = $tickets_count;
            $config['per_page']         = $per_page; 
            $config['uri_segment']      = 4;
            
            
            $this->pagination->initialize($config); 
            
            
            $this->data['pagination']   = $this->pagination->create_links();
            $tickets                    = $this->tickets_model->get_tickets($lang_id,$per_page,$offset,$this->user_id);
            $new_ticket_array = array();
            
            foreach($tickets as $ticket)
            {
                if($ticket->status_id == 1)
                {
                    $class = 'success';
                }
                elseif($ticket->status_id == 2)
                {
                    $class = 'warning';
                }
                elseif($ticket->status_id == 3)
                {
                    $class = 'info';
                }
                elseif($ticket->status_id == 4)
                {
                    $class = 'danger';
                }
                elseif($ticket->status_id == 5)
                {
                    $class = 'default';
                }
                
                $ticket->{'class'} = $class;
                
                $new_ticket_array[] = $ticket; 
            }
            /***************************************/
            
            $this->data['tickets']      = $new_ticket_array;
            $this->data['content']      = $this->load->view('list_tickets', $this->data, true);
            $this->load->view('site/main_frame',$this->data);
        }
    }
    
    
   /**************************************************************************/ 
   
   public function new_ticket()
   {
       $validation_msg = false;
        
        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
        {
            $validation_msg = true;
            
            $lang_id = $this->session->userdata('lang_id');
        
            $this->form_validation->set_rules('title', lang('title'), 'required');
            $this->form_validation->set_rules('order_id', lang('order_number'), 'required');
            $this->form_validation->set_rules('ticket_cat', lang('ticket_cat'), 'required');
            $this->form_validation->set_rules('details', lang('details'), 'required');
            
            $this->form_validation->set_message('required', lang('required').'  : %s');
            
            $this->form_validation->set_error_delimiters('<div class="error" style="color: red">', '</div>');
        }
        
        if ($this->form_validation->run() == FALSE)
		{
		    $this->_new_ticket_form($validation_msg);
        }
        else
        {
             //--->>>Get form data 
            $title          = strip_tags($this->input->post('title', TRUE));
            $ticket_cat     = strip_tags($this->input->post('ticket_cat', TRUE));
            $details        = strip_tags($this->input->post('details', TRUE));
            $order_id       = strip_tags($this->input->post('order_id', TRUE));               
            $order_serials  = $this->input->post('order_serial', TRUE); 
            
            //-->>> other_data
           
            $user = $this->ion_auth->user()->row();
            
            $user_id                = $user->id;
            $last_updated_by        = $user->id;
            $unix_time              = time();
            $last_update_unix_time  = time();
            
            /*********** Upload****************************/
           
            $this->load->library('upload');
            $gallery_path = realpath(APPPATH. '../assets/uploads/tickets_posts');
            
            $config = array();
            $config['upload_path']   = $gallery_path;
            $config['allowed_types'] = 'ppt|pptx|xls|xls|pdf|docs|doc|text|png|jpg|jpeg|tif';
            $config['max_size']      = '100';
            
            $files_name = '';
            $error      = array();
            
            if(count(array_filter($_FILES['userfile']['name'])))
            {
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
                   if(!$this->upload->do_upload())
                   {
                        $error = $this->upload->display_errors();
                        $error['error'] = $error; 
                   }
                   else
                   {
                       $file_data   = $this->upload->data();
                       $files_name .= $file_data['file_name']." , ";
                   }
            
                }
                
                $files_name = trim($files_name," , ");
            }
            
            if(count($error) > 0)
            {
                $_SESSION['error_message'] = $error['error'];
                $this->session->mark_as_flash('error_message');
                 
                $validation_msg = $error['error'];
                $this->_new_ticket_form($validation_msg);
            }
            else
            {
                $order_data = $this->orders_model->get_order($order_id);
                
                $data = array (
                                'cat_id'                => $ticket_cat              ,
                                'status_id'             => 1                        ,
                                'user_id'               => $user_id                 ,
                                'order_id'              => $order_id                ,
                                'store_id'              => $order_data->store_id    ,
                                'title'                 => strip_tags($title)       ,
                                'details'               => strip_tags($details)     ,
                                'last_updated_by'       => $last_updated_by         ,
                                'unix_time'             => $unix_time               ,
                                'last_update_unix_time' => $last_update_unix_time   ,
                                'attachments'           => $files_name              ,
                             );
            
                
                $this->tickets_model->insert_ticket($data); 
                
                $ticket_id = $this->db->insert_id();
            
                if($ticket_id)
                {
                    if(!empty($order_serials))
                    {
                        foreach($order_serials as $serial)
                        {
                            $tickets_serials_data = array (
                                                            'ticket_id' => $ticket_id ,
                                                            'order_id'  => $order_id  ,
                                                            'serial_id' => strip_tags($serial)
                                                          );
                            $this->tickets_model->insert_ticket_serials($tickets_serials_data);
                        }
                    }
                    
                    //-->>Send notification
                    
                    $this->load->library('notifications');
                    $data      = array (
                                         'username'          => $user->username,
                                         'logo_path'         => base_url().'assets/template/admin/img/logo.png',
                                         'admin_ticket_link' => base_url().'tickets/admin_tickets/read/' . $ticket_id . '/' . $lang_id,
                                         'user_ticket_link'  => base_url().'Ticket_Details/' . $ticket_id,
                                       );
                    $emails[] = $user->email;
                    $phone    = $user->phone;
                    
                    $this->notifications->create_notification('ticket_sent',$data ,$emails , $phone, $order_data->store_id);
                  
                    $_SESSION['success'] = lang('success');
                    $this->session->mark_as_flash('success');
                    
                    redirect('Ticket_Details/'.$ticket_id,'refresh');
                }
                else
                {
                     $_SESSION['failed'] = lang('failed');
                     $this->session->mark_as_flash('failed');
                    
                     redirect('Support_Tickets/', 'refresh'); 
                }
            }
          
        }
   }
   
   private function _new_ticket_form($validation_msg)
   {
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }
        
        $lang_id     = $this->session->userdata('lang_id');
        $ticket_cats = $this->admin_categories_model->get_tickets_cat($lang_id);
        
        //$ticket_cats_array[NULL] = lang('choose');    
        
        foreach($ticket_cats as $cat)
        {
            $ticket_cats_array[$cat->id] = $cat->title;
        }
        
        $this->data['tickets_cat']      = $ticket_cats_array;
        $this->data['tickets_orders']   = $this->tickets_model->get_tickets_orders($this->user_id);
        
        $this->data['content']          = $this->load->view('new_ticket', $this->data, true);
        $this->load->view('site/main_frame',$this->data);
   }
   
    public function ticket_details($id)
    {
        $id      = intval($id);
        $lang_id = $this->session->userdata('lang_id');
         
        if ($this->ion_auth->logged_in())
        {
            $ticket = $this->tickets_model->get_ticket_detials($id,$lang_id);
            
            if($ticket)
            {
                if($ticket->user_id == $this->user_id)
                {
                    $this->data['ticket_last_updated']  = $this->tickets_model->get_ticket_last_updated_by_name($ticket->last_updated_by);
                    $this->data['ticket_posts']         = $this->tickets_model->get_ticket_posts($id);
                    
                    $this->data['ticket_id']            = $id;
                    $this->data['user_id']              = $this->user_id;
                    $this->data['ticket']               = $ticket;
                    $serials                            = $this->admin_tickets_model->get_ticket_serials($id,$ticket->order_id);
                    
                    $order_serial = '';
                    $this->load->library('encryption');
                    $this->config->load('encryption_keys');
           
                    foreach($serials as $serial)
                    {
                        $secret_key  = $this->config->item('new_encryption_key');
                        $secret_iv   =  md5('serial_iv');
                        $enc_serials = $this->encryption->decrypt($serial->serial, $secret_key, $secret_iv);
                        
                        $order_serial .= $enc_serials ." , ";
                    }
                    
                    $order_serial = rtrim($order_serial," , ");
                    $this->data['order_serial']               = $order_serial;
                    /*************************************/
                    $this->data['content'] = $this->load->view('ticket_details', $this->data, true);
                    $this->load->view('site/main_frame',$this->data);
                }
                else
                {
                    
                    $this->session->set_flashdata('not_allow',lang('not_allow_page'));//
                    redirect('Support_Tickets/','refresh');
                }
            }
            else
            {
                $this->session->set_flashdata('not_allow',lang('not_allow_page'));//
                redirect('Support_Tickets/','refresh');
            }
         }
         else
         {
            
           $this->session->set_flashdata('not_allow',lang('please_login_first'));//
           redirect('users/users/user_login','refresh');
        }
    }
  
    public function save_post()
    {
        $validation_msg = '';
        if (!$this->ion_auth->logged_in())
        {
            $this->session->set_flashdata('not_allow',lang('please_login_first'));//
            redirect('users/users/user_login','refresh');
        }
        
        $this->form_validation->set_rules('message_text', lang('message_text'), 'required');
        $this->form_validation->set_message('required', lang('required').'  : %s');
        
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        
        $ticket_id = $this->input->post('ticket_id', TRUE);
        
        if($this->form_validation->run() == FALSE)
        {
            $this->_save_post_form($ticket_id, $validation_msg);
        }
        else
        {
            $message_text   = $this->input->post('message_text', true);
            $solved_status  = (isset( $_POST['status']))? 3:2;
            $user_id        = $this->input->post('user_id', true); 
            $files_name     = '';
            $error          = '';//array();            
            
            if(count(array_filter($_FILES['userfile']['name'])))
            {
                /*********** Upload****************************/
                $this->load->library('upload');
                $gallery_path = realpath(APPPATH. '../assets/uploads/tickets_posts');
                
                $config = array();
                $config['upload_path']   = $gallery_path;
                $config['allowed_types'] = 'ppt|pptx|xls|xls|pdf|docs|doc|text|png|jpg|jpeg|tif';
                $config['max_size']      = '0';
                
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
                    
                    if(!$this->upload->do_upload())
                    {
                        $error = $this->upload->display_errors();
                        //$error['error'] = $error;
                    } 
                    else
                    {      
                       $file_data   = $this->upload->data();
                       $files_name .= $file_data['file_name']." , ";    
                    }
                }
            }
            
            if($error != '')
            {
                $_SESSION['error_message'] = $error;//['error'];
                $this->session->mark_as_flash('error_message');
                 
                $validation_msg = $error;//['error'];
                $this->_save_post_form($ticket_id, $validation_msg);
            }
            else
            {
            
                $files_name = trim($files_name," , ");
                
                $data = array(
                               'ticket_id'   => $ticket_id ,
                               'user_id'     => $user_id ,
                               'post_text'   => strip_tags($message_text) ,
                               'attachments' => $files_name,
                               'unix_time'   => time()
                             );
            
                $ticket_status = array( 
                                        'status_id'               => $solved_status,
                                        'last_updated_by'         => $user_id ,
                                        'last_update_unix_time'   => time()
                                      );  
                                   
                $this->tickets_model->insert_ticket_post($data);
                $this->tickets_model->update_ticket_status($ticket_id , $ticket_status);
                /**********************************************/
                redirect('tickets/tickets/ticket_details/'.$ticket_id, 'refresh');
            }
        }
    }
  
    private function _save_post_form($ticket_id, $validation_msg)
    {
        $lang_id  = $this->session->userdata('lang_id');
        $id       = $ticket_id;
        
        if (!$this->ion_auth->logged_in())
        {
            $this->session->set_flashdata('not_allow',lang('please_login_first'));//
            redirect('users/users/user_login','refresh');
        }
        else
        {
            $ticket                             = $this->tickets_model->get_ticket_detials($id, $lang_id);
            $this->data['ticket_last_updated']  = $this->tickets_model->get_ticket_last_updated_by_name($ticket->last_updated_by);
            $this->data['ticket']               = $ticket; 
            $this->data['ticket_posts']         = $this->tickets_model->get_ticket_posts($id);
            $this->data['ticket_id']            = $id;
            $this->data['user_id']              = $this->user_id;
            $serials                            = $this->admin_tickets_model->get_ticket_serials($id,$ticket->order_id);
            
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
            $this->data['order_serial'] = $order_serial;
            /*************************************/
            $this->data['content']              = $this->load->view('ticket_details', $this->data, true);
            $this->load->view('site/main_frame',$this->data);
        }
    }
  
    public function reopen($ticket_id)
    {
        $ticket_id = intval($ticket_id);
        
        $ticket_status = array( 
                                'status_id'               => 5,
                                'last_updated_by'         => $this->user_id ,
                                'last_update_unix_time'   => time()
                              );  
                           
       $this->tickets_model->update_ticket_status($ticket_id , $ticket_status);
       redirect('tickets/tickets/ticket_details/'.$ticket_id,'refresh');
  }
  
   private function do_upload( $file )
   {   
        $file_ext     = strstr($_FILES[$file]['name'],".") ;
        $new_name     = time().$file_ext;
        $gallery_path = realpath(APPPATH. '../assets/uploads/tickets_posts');
        
        $config       = array(
                            'upload_path'   => $gallery_path,
                            'allowed_types' => 'ppt|pptx|xls|xls|pdf|docs|doc|text|png|jpg|jpeg|tif',
                            'file_name'     => $new_name,
                            'max_size'      => '0'
                        );
    
       // echo "config";  var_dump($config);
        $this->load->library('upload');
        $this->upload->initialize($config);
        if( ! $this->upload->do_upload($file)){
            //echo the errors
             $error = array('error' => $this->upload->display_errors());
	         print_r($error);die();
        }else{
            //If the upload success
            $file_data   = $this->upload->data();
            $upload_path = $file_data['file_name'];
            return $upload_path;
        }
    }
	  
  public function get_serials ($order_id)
  {
     $order_id   = intval($order_id);
     $serials    =  $this->tickets_model->get_completed_order_serials($order_id);
     $serials_ar = '';
     
     $this->load->library('encryption');
     $this->config->load('encryption_keys');
     
     if ($serials != false)
     {
         foreach($serials as $serial)
         {
            $secret_key  = $this->config->item('new_encryption_key');
            $secret_iv   =  md5('serial_iv');
            $enc_serials = $this->encryption->decrypt($serial->serial, $secret_key, $secret_iv);
            
            $serials_ar .="<input type='checkbox'  name='order_serial[]' value='$serial->id' />  $enc_serials <br/>" ;
            //"<input >$serial->id   :  $serial->serial  <br/>";
         }
         
         echo $serials_ar;
     
     }
     else
     {
        echo lang('no_available_serials');
     }
     
  }
  
  function download($filename)
  {
     $this->load->helper('file');
     $this->load->helper('download');
     $data = file_get_contents(base_url().'/assets/uploads/tickets_posts/'.urldecode($filename)); // Read the file's contents
    
     force_download($filename, $data);  
  }
/************************************************************************/    
}