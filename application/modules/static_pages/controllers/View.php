<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class View extends CI_Controller
{
    public $lang_row;
    var $data = array();
    public function __construct()
    {
        parent::__construct();
        
        //require(APPPATH . 'includes/global_vars.php');
        
        $this->load->model('static_pages_model');
        
        require(APPPATH . 'includes/front_end_global.php');
        
        $this->session->set_userdata('site_redir', current_url());
        
    }

    

    public function index($id)
    {   
        $page_id = intval($id);
        if($page_id)
        {
            $display_lang_id = $this->data['active_language_row']->id;
            $page_details    = $this->static_pages_model->get_row_data($page_id, $display_lang_id);
            
            if($page_details)
            {
            
                $this->data['page_details'] = $page_details;
                
                $this->data['content']     = $this->load->view('site_view', $this->data, true);
                $this->load->view('site/main_frame', $this->data);
            }
            
        }
    }
    
    public function all_branches()
    {
        $this->data['content']     = $this->load->view('branches_map', $this->data, true);
        $this->load->view('site/main_frame', $this->data);
    }
    
    public function get_all_branches_list()
    {
        $this->load->library('location_locator');
        
        $output     = array();
        $lang_id    = 2;//intval($this->input->post('langId', TRUE));
         
        $list       = $this->location_locator->get_all_branch_list($lang_id);
        //print_r($list);die();
        if(count($list) != 0)
        {
            $output = $list;
        }
        
        $this->output->set_content_type('application/json')->set_output(json_encode($output));
    }
    
     
     
    
/************************************************************************/    
}