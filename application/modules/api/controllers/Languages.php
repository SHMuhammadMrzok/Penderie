<?php 
if(!defined('BASEPATH'))
    exit('No Direct script access allowed');
    
class Languages extends CI_Controller
{
    public function __construct()
    {
        parent :: __construct();
        
        $this->load->model('general_model');

        $this->load->library('api_lib');
    }
    
    public function all_languages()
    {
        $output    = array();
        $languages = $this->general_model->get_languages();

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
        
        foreach ($languages as $lang)
        {
            $output[] = array(
                                'id'        => $lang->id        ,
                                'name'      => $lang->name      ,
                                'symbol'    => $lang->symbol    ,
                                'direction' => $lang->direction ,
                                'flag'      => base_url().'assets/uploads/'.$lang->flag
                            );
        }
        
        //***************LOG DATA***************//
        //insert log
        $this->api_lib->insert_log($user_id, current_url(), 'All Languages', $agent, $_POST, $output);
        //***************END LOG***************//
        
        $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));
    }
    
    public function lang_vars()
    {
        $output  = array(); 
        $lang_id = intval($this->input->post('langId', TRUE));
        
        $lang_vars = $this->general_model->get_language_vars($lang_id);

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
        
        foreach ($lang_vars as $var)
        {
            $output[$var->lang_var] = $var->lang_definition;
        }
        
        //***************LOG DATA***************//
        //insert log
        $this->api_lib->insert_log($user_id, current_url(), 'Language variables', $agent, $_POST, $output);
        //***************END LOG***************//
        
        $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));
    }
}