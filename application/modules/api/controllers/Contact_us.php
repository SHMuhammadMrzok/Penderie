<?php
  if(!defined('BASEPATH'))
  exit('No Direct script access allowed');
  
class Contact_us extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model('general_model');
        $this->load->model('contact_us/contact_us_model');
        
        $this->load->library('api_lib');
    }
    
    public function index()
    {
        
        $lang_id    = intval($this->input->post('langId', true));
        $agent      = strip_tags($this->input->post('agent', true));
        $email      = strip_tags($this->input->post('email', true));
        
        if($agent == 'IOS')
        {
            $name       = strip_tags($this->input->post('name', true));
            $phone      = strip_tags($this->input->post('phone', true));
        }
        else
        {
            $name       = strip_tags($this->input->post('Name', true));
            $phone      = strip_tags($this->input->post('Phone', true));
        }
        
        $title      = strip_tags($this->input->post('title', true));
        $comment    = strip_tags($this->input->post('comment', true));
        $device_id    = strip_tags($this->input->post('deviceId', true));
        
        $user_id = 0;

        $user_data = $this->user_model->get_user_data_by_field('phone', $phone);

        if(count((array)$user_data) != 0)
        {
            $user_id = $user_data->id;
        }
        
        $required_lang    = $this->general_model->get_lang_var_translation('required', $lang_id);
        $valid_email_lang = $this->general_model->get_lang_var_translation('valid_email', $lang_id);
        $email_lang       = $this->general_model->get_lang_var_translation('email', $lang_id);
        $phone_lang       = $this->general_model->get_lang_var_translation('phone', $lang_id);
        $comment_lang     = $this->general_model->get_lang_var_translation('message', $lang_id);
        
        $this->form_validation->set_rules('email', $email_lang, 'required|valid_email');
        
        if($agent == 'IOS')
        {
            $this->form_validation->set_rules('phone', $phone_lang, 'required');
        }
        else
        {
            $this->form_validation->set_rules('Phone', $phone_lang, 'required');
        }
        $this->form_validation->set_rules('comment', $comment_lang, 'required');
        
        $this->form_validation->set_message('required', $required_lang."  : %s ");
        $this->form_validation->set_message('valid_email', $valid_email_lang);
        $this->form_validation->set_error_delimiters('', '');
        
        if ($this->form_validation->run() == FALSE)
        {
            $output = array(
                            'message' => validation_errors(),
                            'response' => 0
                            );
        }
        else
        {
            $data = array(
                            'agent'     => $agent,
                            'name'      => $name,
                            'email'     => $email,
                            'mobile'    => $phone,
                            'title'     => $title,
                            'message'   => $comment,
                            'unix_time' => time()
                        );
            
            $this->contact_us_model->save_msg($data);
            
            $settings = $this->general_model->get_site_settings($lang_id);
            //send email
            $contact_us_lang    = $this->general_model->get_lang_var_translation('contact_us', $lang_id);
            $msg_subject        = $title.' | '.$contact_us_lang.' | '.$settings->site_name;
            //-->>>>  send mail to administrator (to site mail)
            $this->load->library('email');
            
            $site_title = $settings->site_name;
            $emails_array = json_decode($settings->email);
            
            foreach($emails_array as $receiver_email)
            {
                $this->email->from($email, $site_title);
                $this->email->to($receiver_email);
                $this->email->subject($msg_subject);
                $this->email->message($comment);
              
                $this->email->send();
            }
            
            $success_msg = $this->general_model->get_lang_var_translation('message_send', $lang_id);
            $output = array(
                            'message'  => $success_msg,
                            'response' => 1
                            );
        }
        
        //***************LOG DATA***************//
        //insert log
        $this->api_lib->insert_log($user_id, current_url(), 'Contact us', $agent, $_POST, $output);
        //***************END LOG***************//

        $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));
    }
}