<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Registration_countries extends CI_Controller
{
  
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model('registration_countries_model');
        $this->load->model('general_model');
        
    }

    public function index()
    {
        $lang_id    = intval($this->input->post('langId', TRUE));
        $deviceId   = strip_tags($this->input->post('deviceId', TRUE));
        
        $countries  = $this->registration_countries_model->get_countries($lang_id);
        
        $output     = array();
        
        $success_message = $this->general_model->get_lang_var_translation('execution_success',$lang_id);       
       
        if(isset($countries) && !empty($countries))
        {
           foreach($countries as $country)
            {
                $cities = $this->registration_countries_model->get_cities($lang_id ,$country->id);
                
                 if(!empty($cities))
                 {
                    foreach($cities as $city)
                    {
                        $cities_array [] = array(
                                                    'regCityId'           => $city->id,
                                                    'regCityName'         => $city->name,
                                                );
                    }
                    
                 }else{
            
                     $cities_array = array();
                 }
                 
                $output [] = array(
                                    'regCountryId'             => $country->id,
                                    'regCountryName'           => $country->name,
                                    'regCountryKey'            => $country->calling_code,
                                    'regCities'                => $cities_array,
                                    );
            }   
        }
        else
        {
            $fail_message   = $this->general_model->get_lang_var_translation('execution_fail',$lang_id);
            $output         = array( 
                                    'message' => $fail_message,
                                    'response' => 0
                                    );
        }
        
        $this->output->set_content_type('application/json')->set_output(json_encode($output));
        
    }
       
     
/************************************************************************/    
}