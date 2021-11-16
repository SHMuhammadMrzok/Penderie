<?php
 if (!defined('BASEPATH'))
    exit('No direct script access allowed');
    
 class All_brands extends CI_Controller
 {

    public $settings;
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model('brands/Brands_model');
        $this->load->model('general_model');
        
        $this->load->library('api_lib');
        
        $this->settings = $this->general_model->get_settings();
    }

    public function index()
    {
        $lang_id    = intval($this->input->post('langId', TRUE));
        $page       = intval($this->input->post('page', TRUE));
        
        $email      = strip_tags($this->input->post('email', TRUE));
        $password   = strip_tags($this->input->post('password', TRUE));
        $agent      = strip_tags($this->input->post('agent', TRUE));
        
        if($this->ion_auth->login($email, $password))
        {
            $user_data = $this->ion_auth->user()->row();
            $user_id = $user_data->id;
            // $this->api_lib->check_user_store_country_id($email, $password, $user_data->id, $country_id);
        }
        else {
          $user_id = 0;
        }
        
        $output    = array();
        
        if(!$page) $page = 1;
        $limit           = $this->settings->products_limit;
        $offset          = ($page -1)*$limit;
        
        $brands_data = $this->Brands_model->get_all_brands($lang_id, $limit, $offset);
        
        if(count($brands_data) != 0)
        {
        
        
            $images_path = $this->api_lib->get_images_path();
            
            foreach($brands_data as $all_brands)
            {
                
    
                    $output[] = array(
                                            'brandId'   => $all_brands->brand_id,
                                            'name'      => $all_brands->name,
                                            'image'     => $images_path.$all_brands->image
                                        );
            }
            
        }
        else
        {
            $fail_message = $this->general_model->get_lang_var_translation('no_data',$lang_id);
            $output       = array(
                                        'message'   => $fail_message,
                                        'response'  => 0
                                   );
        }
        
        //***************LOG DATA***************//
        //insert log
        $this->api_lib->insert_log($user_id, current_url(), 'Brands', $agent, $_POST, $output);
        //***************END LOG***************//
        
        $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));
    }
 }
?>