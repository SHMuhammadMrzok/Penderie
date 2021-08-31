<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rate extends CI_Controller {
    
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model('home_model');
        $this->load->model('products/products_model');
        
        require(APPPATH . 'includes/front_end_global.php');
        $this->session->set_userdata('site_redir', current_url());
    }
    
    public function index()
    {
        $this->data['content'] = $this->load->view('rate', $this->data, true);
        $this->load->view('site/main_frame', $this->data);
    }
    
    

    
    /*********************************************************/
  
}
/* End of file test.php */
/* Location: ./application/controllers/test.php */