<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Static_pages extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('static_pages/static_pages_model');
        $this->load->model('general_model');

        $this->load->library('api_lib');
    }

    public function index()
    {
        $lang_id    = intval($this->input->post('langId', TRUE));
        $page_id    = intval($this->input->post('pageId', TRUE));
        $deviceId   = strip_tags($this->input->post('deviceId', TRUE));

        // Added for api log
        $email              = strip_tags($this->input->post('email', TRUE));
        $password           = strip_tags($this->input->post('password', TRUE));  
        $agent              = strip_tags($this->input->post('agent', TRUE));
        $user_id            = 0;

        if($this->ion_auth->login($email, $password))
        {
            $user_data  = $this->ion_auth->user()->row();
            $user_id    = $user_data->id;
        }
        ///
        
        $page_data  = $this->static_pages_model->get_row_data($page_id, $lang_id);
        $output     = array();

        if(count($page_data) != 0)
        {
          if(isset($page_data->image) && $page_data->image != '')
          {
            $image = base_url().'assets/uploads/'.$page_data->image;
          }
          else {
            $image = '';
          }

          $page_text = str_replace('<br />', "", $page_data->page_text);
          $output = array(
                          'pageId'   => $page_data->id    ,
                          'title'    => $page_data->title ,
                          'image'    => $image            ,
                          'pageText' => $page_text//$page_data->page_text
                          );
        }
        else
        {
            $fail_message   = $this->general_model->get_lang_var_translation('execution_fail',$lang_id);
            $output         = array(
                                    'message' => $fail_message,
                                    'response' => 0
                                );
        }

        //***************LOG DATA***************//
        //insert log
        $this->api_lib->insert_log($user_id, current_url(), 'Static pages', $agent, $_POST, $output);
        //***************END LOG***************//

        $this->output->set_content_type('application/json')->set_output(json_encode($output));
    }

/************************************************************************/
}
