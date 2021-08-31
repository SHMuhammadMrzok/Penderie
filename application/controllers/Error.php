<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Error extends CI_Controller 
{
    var $data = array();
    public function __construct()
    {
        parent::__construct();
        
        require(APPPATH . 'includes/front_end_global.php');
        //$this->session->set_userdata('site_redir', current_url());
    }

    public function index()
    {
        $this->data['content'] = $this->load->view('site/error_page', $this->data, true);
        $this->load->view('site/main_frame',$this->data);
    }
    
    
/******************************************************************************/
  
}
/* End of file error.php */
/* Location: ./application/controllers/error.php */