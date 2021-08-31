<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class General_report extends CI_Controller
{
    public   $lang_row;
 
    public function __construct()
    {
        parent::__construct();
        
        require(APPPATH . 'includes/global_vars.php');
        
       $this->lang_row = $this->admin_bootstrap->get_active_language_row(); 
    }
    
    public function index($page_id = 1)
    {
        
        
        $this->data['content']       = $this->load->view('general', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data); 
    }
    
    
    
/************************************************************************/    
}