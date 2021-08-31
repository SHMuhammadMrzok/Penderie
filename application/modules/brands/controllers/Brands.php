<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Brands extends CI_Controller
{
   
    public function __construct()
    {
        parent::__construct();
      
        require(APPPATH . 'includes/front_end_global.php');
        $this->load->model('brands_model');
        
        $this->session->set_userdata('site_redir', current_url());
    }

    var $data = array();
    
    public function index()
    {   
        $brands = $this->brands_model->get_all_brands($this->data['lang_id']);
        $this->data['brands'] = $brands;
        
        $this->data['content'] = $this->load->view('brands_view', $this->data, true);
        $this->load->view('site/main_frame',$this->data);
    }
    
    
/************************************************************************/    
}