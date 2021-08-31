<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Faq extends CI_Controller
{
   
    public function __construct()
    {
        parent::__construct();
      
        require(APPPATH . 'includes/front_end_global.php');
        $this->load->model('faq_model');
        $this->session->set_userdata('site_redir', current_url());
    }

    var $data = array();
    
    public function index()
    {   
        $lang_id               = $this->data['lang_id'];
        $faqs                  = $this->faq_model->get_faq($lang_id);
        
        $this->data['faqs']    = $faqs;
        
        $this->data['content'] = $this->load->view('faq', $this->data, true);
        $this->load->view('site/main_frame',$this->data);
    }
    
    
/************************************************************************/    
}