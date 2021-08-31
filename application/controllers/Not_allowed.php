<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Not_allowed extends CI_Controller 
{
    public function __construct()
    {
        parent::__construct();
        
        require(APPPATH . 'includes/front_end_global.php');
        
    }

    public function index()
    {
        $this->data['content'] = $this->load->view('site/not_allowed', $this->data, true);
        $this->load->view('site/main_frame',$this->data);
    }
    
    
/******************************************************************************/
  
}
/* End of file error.php */
/* Location: ./application/controllers/error.php */