<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class User_affiliate extends CI_Controller
{
    public $crud;
    
    public function __construct()
    {
        parent::__construct();
        
        require(APPPATH . 'includes/front_end_global.php');
       
        $this->load->model('affiliate_log_model');
        $this->load->model('users/users_model');
        
        $this->session->set_userdata('site_redir', current_url());
    }
    
    
    public function index($page_id =1)
    {
           if($this->ion_auth->logged_in())
           {
               $this->load->library('pagination');
                
               $perPage = 15; 
               $offset  = ($page_id -1 ) * $perPage;
    
               if($offset < 0)
               {
                   $offset = $perPage;
               } 
               //-->> Get user row And user affliate data
               $user                                = $this->user_bootstrap->get_user_data();  
               $user_affiliate                      = $this->affiliate_log_model->get_user_affiliate($user->id);  
                 
               if(!empty($user_affiliate))
               {
                 $this->data['user_affiliate_code'] = $user_affiliate->code ;
               }
               
               $user_affiliate_log_data             = $this->affiliate_log_model->get_user_affiliate_log($user->id, $perPage, $offset);
                
               $config['base_url']                  = base_url()."affiliate/user_affiliate/index/";
               $config['per_page']                  = $perPage;
               $config['first_link']                = FALSE;
               $config['last_link']                 = FALSE;
               $config['uri_segment']               = 4;
               $config['use_page_numbers']          = TRUE;
               $config['total_rows']                = $this->affiliate_log_model->get_all_affiliate_log_count($user->id);
                
               $this->pagination->initialize($config);
               
               $this->data['pagination']            = $this->pagination->create_links();
               
               $this->data['user_affiliate_log']    = $user_affiliate_log_data;
               
               $this->data['content']               = $this->load->view('user_affiliate', $this->data, true);
               $this->load->view('site/inner_main_frame',$this->data);
           }
           else
           {
                $this->session->set_userdata('redir', current_url());
                  
    			redirect('users/users/user_login', 'refresh');
           }
    }
    
      
/************************************************************************/    
}