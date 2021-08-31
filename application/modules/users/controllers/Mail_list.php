<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Mail_list extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mail_list_model');
        require(APPPATH . 'includes/front_end_global.php');
    }

    public function insert_member()
    {
        $error_msg = '';
        $this->form_validation->set_rules('email', lang('email'), 'required|is_unique[mail_list_members.email]');

        $this->form_validation->set_message('required', lang('required')."  : %s ");
        $this->form_validation->set_message('is_unique', lang('is_unique')."  : %s ");

        if($this->form_validation->run() == FALSE)
        {
            $msg = validation_errors();
            $error = 1;

            //$_SESSION['not_allow'] = $error_msg;
          //  $this->session->mark_as_flash('not_allow');

        }
        else
        {
            $email = strip_tags($this->input->post('email', TRUE));

          /*  // check if email already exist
            $email_count = $this->mail_list_model->count_user_email($email);

            if($email_count > 0)
            {
                $error_msg = lang('email_already_exist');
                $_SESSION['not_allow'] = lang('email_already_exist');
                $this->session->mark_as_flash('not_allow');

            }
            else
            {
              */
                $data = array(
                                'email'             => $email,
                                'activation_code'   => sha1(md5(microtime()))
                             );

                $this->mail_list_model->insert_user($data);

                $id = $this->db->insert_id();
                $data['id'] = $id;


                //send activation code
                $result = $this->send_mail_list_activation($data);
                $msg = $result['message'];
                $error = $result['response'];
           //}
        }

        $this->data['mail_list_error'] = $error_msg;

        $output = array('message'=> strip_tags($msg), 'error'=>$error);

        echo json_encode($output);
        //redirect(base_url(), 'refresh');
    }

    public function send_mail_list_activation($data)
    {
        //-->>>send email
        $message = $this->load->view('activate_mail_list', $data, true);

    		$this->email->clear();
    		$this->email->from($this->config->item('sender_email'), $this->config->item('site_name'));
    		$this->email->to($data['email']);
    		$this->email->subject($this->config->item('site_name') . ' - ' . lang('join_mail_list'));
    		$this->email->message($message);

    		if($this->email->send())
    		{
            $msg = lang('email_activation_sent');
            $error = 0;
    		    //$_SESSION['message'] = lang('email_activation_sent');
            //$this->session->mark_as_flash('message');
        }
        else
        {
            $msg = lang('email_activation_not_sent');
            $error = 1;
            //$_SESSION['error_message'] = ('email_activation_not_sent');
            //$this->session->mark_as_flash('error_message');
        }

        return array(
          'message' => $msg,
          'response' => $error
        );
    }

    public function activate($id, $code)
    {
        $id     = intval($id);
        $code   = strip_tags($code);

        if ($code !== false)
		{
            $data = $this->mail_list_model->get_mail_list_member($id);

            if($data->activation_code == $code)
            {
                $updated_data = array(
                                        'active' => 1,
                                        'activation_code' => ''
                                     );

                $this->mail_list_model->update_mail_member($id, $updated_data);

                $_SESSION['message'] = lang('welcome_to_our_mail_list');
                $this->session->mark_as_flash('message');
            }
            else
            {
                $_SESSION['error_message'] = lang('error');
                $this->session->mark_as_flash('error_message');
            }
		}
        else
        {
            $_SESSION['error_message'] = lang('error');
            $this->session->mark_as_flash('error_message');
        }

        redirect(base_url(), 'refresh');
    }
}
