<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Contact_us extends CI_Controller
{

    public function __construct()
    {
        /*parent::__construct();

        require(APPPATH . 'includes/front_end_global.php');
        $this->load->model('contact_us_model');

        $this->session->set_userdata('site_redir', current_url());*/
        
        parent::__construct();

        require(APPPATH . 'includes/front_end_global.php');

        $this->session->set_userdata('site_redir', current_url());
                
        $this->load->model('global_model');
        $this->load->model('contact_us_model');
        $this->load->model('notifications/events_model');
        $this->load->model('users/users_model');
                
        $this->load->library('notifications');
                
        $this->settings = $this->global_model->get_config();
        $this->lang_id = $this->settings->default_lang;
    }

    var $data = array();

    public function index()
    {

        $this->data['content'] = $this->load->view('contact_us', $this->data, true);
        $this->load->view('site/main_frame',$this->data);
    }

    public function send()
    {
        $this->form_validation->set_rules('name', lang('name') , 'required');
        $this->form_validation->set_rules('email',lang('email') , 'required|valid_email');
        $this->form_validation->set_rules('mobile', lang('mobile') , 'required|integer');
        $this->form_validation->set_rules('title', lang('message_title') , 'required');
        $this->form_validation->set_rules('message', lang('message') , 'required');

        $this->form_validation->set_message('required', lang('required')."  : %s ");
        $this->form_validation->set_message('integer', "%s : ".lang('integer_required'));
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');


        if ($this->form_validation->run() == FALSE)
        {
            //$this->session->set_flashdata('contact_us_validation_message',lang('please_fill_all_fields'));
            $_SESSION['contact_us_validation_message'] = validation_errors();//lang('message_not_send');
            $this->session->mark_as_flash('contact_us_validation_message');

            $this->data['content'] = $this->load->view('contact_us', $this->data, true);
            $this->load->view('site/main_frame',$this->data);

        }
        else
        {

            $user_name    = strip_tags($this->input->post('name', TRUE));
            $user_email   = strip_tags($this->input->post('email', TRUE));
            $user_mobile  = strip_tags($this->input->post('mobile', TRUE));
            $user_subject = strip_tags($this->input->post('title', TRUE));
            $user_message = strip_tags($this->input->post('message', TRUE));

            $data = array(
                        'name'      => $user_name,
                        'email'     => $user_email,
                        'mobile'    => $user_mobile,
                        'title'     => $user_subject,
                        'message'   => $user_message,
                        'unix_time' => time()
                    );

            /*if($this->contact_us_model->save_msg($data))
            {
                $_SESSION['success_msg'] = lang('message_send');
                $this->session->mark_as_flash('success_msg');
            }
            else
            {
                $_SESSION['error_message'] = lang('message_not_send');
                $this->session->mark_as_flash('error_message');
            }

            $msg_subject = $user_subject.' | '.lang('contact_us').' | '.$this->config->item('site_name');
            //-->>>>  send mail to administrator (to site mail)
            $this->load->library('email');

            $site_title = $this->config->item('site_name');
            $emails_array = json_decode($this->config->item('email')) ;

            foreach($emails_array as $email)
            {
                $this->email->from($user_email, $site_title);
                $this->email->to($email);
                $this->email->subject($msg_subject);
                $this->email->message($user_message);

                $this->email->send();
            }*/#Abdu Edit
            
            if($this->contact_us_model->save_msg($data))
            {
                $last_insert_id = $this->contact_us_model->get_lastInsertId();
                                
                $this->send_contact_us_email($last_insert_id, $user_name);
                $_SESSION['success_message'] = lang('message_send');
                $this->session->mark_as_flash('success_message');
                
                $this->data['success_message'] = lang('message_send');
            
            
                //send email to site admin
                $msg_subject = $user_subject.' | '.lang('contact_us').' | '.$this->config->item('site_name');
                //-->>>>  send mail to administrator (to site mail)
                $this->load->library('email');
    
                $site_title = $this->config->item('site_name');
                $emails_array = json_decode($this->config->item('email')) ;
    
                foreach($emails_array as $email)
                {
                    $this->email->from($user_email, $site_title);
                    $this->email->to($email);
                    $this->email->subject($msg_subject);
                    $this->email->message($user_message);
    
                    $this->email->send();
                }

                
                //redirect (base_url(), 'refresh');
            
            }
            else
            {
                $_SESSION['error_message'] = lang('message_not_send');
                $this->session->mark_as_flash('error_message');
                
                $this->data['error_message'] = lang('message_not_send');
                
            
            }
            

            //redirect (base_url().'Contact_US/','refresh');
            redirect (base_url(), 'refresh');
            //$this->data['content'] = $this->load->view('contact_us', $this->data, true);
            //$this->load->view('site/main_frame',$this->data);
        }
    }
    
    public function send_contact_us_email($message_id, $user_name)
    {

        $template_data = array(
                                'logo_path'             => $this->data['images_path'].$this->settings->logo,
                                'site_name'             => lang("website_name"),
                                'username'              => $user_name,
                                'admin_message_link'    => base_url().'contact_us/admin/reply/'.$message_id
                                                                                                        
                              );
        $event_data = $this->events_model->get_row_data_by_event_var('contact_us');
                
        $users = $this->users_model->get_group_users($event_data->user_group_id);
        
        foreach($users as $user)
        {
            $emails[] = $user->email;
                             
        $this->notifications->create_notification('contact_us', $template_data, $emails);                 
    
        $this->notifications->create_notification('contact_us', $template_data, $emails);
        }
    }
/************************************************************************/
}
