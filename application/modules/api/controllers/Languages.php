<?php 
if(!defined('BASEPATH'))
    exit('No Direct script access allowed');
    
class Languages extends CI_Controller
{
    public function __construct()
    {
        parent :: __construct();
        
        $this->load->model('general_model');
    }
    
    public function all_languages()
    {
        $output    = array();
        $languages = $this->general_model->get_languages();
        
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
        
        $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));
    }
    
    public function lang_vars()
    {
        $output  = array(); 
        $lang_id = intval($this->input->post('langId', TRUE));
        
        $lang_vars = $this->general_model->get_language_vars($lang_id);
        
        foreach ($lang_vars as $var)
        {
            $output[$var->lang_var] = $var->lang_definition;
        }
        
        $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));
    }
}