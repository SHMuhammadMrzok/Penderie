<?php
if (!defined('BASEPATH'))
    exit('No direct script acess allowed');
    
class Locator extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        $this->load->library('location_locator');
        
        $this->load->model('general_model');
    }
    
    public function get_address()
    {
        $lang_id    = intval($this->input->post('langId', TRUE));
        $lng        = strip_tags($this->input->post('lng', TRUE));
        $lat        = strip_tags($this->input->post('lat', TRUE));
        
        $address = $this->location_locator->get_location_address($lng.','.$lat);
        
        if($address)
        {
            $output = array(
                                'address' => $address,
                                'response' => 1
                           );
        }
        else
        {
            $message   = $this->general_model->get_lang_var_translation('no_data', $lang_id);
            $output    = array(
                                'message' => $message,
                                'response' => 0
                              );
        }
        
        $this->output->set_content_type('application/json')->set_output(json_encode($output));
        
    }
    
    public function get_location_lat_lng()
    {
        $lang_id    = intval($this->input->post('langId', TRUE));
        $address    = strip_tags($this->input->post('address', TRUE));
        
        $lat_lng    = $this->location_locator->get_location_latlong($address);
        
        if($lat_lng)
        {
            $output = array(
                                'lat_lng'   => $lat_lng     ,
                                'lat'       => $lat_lng[0]  ,
                                'lng'       => $lat_lng[1]  ,
                                'response'  => 1
                           );
        }
        else
        {
            $message   = $this->general_model->get_lang_var_translation('no_data', $lang_id);
            $output    = array(
                                'message' => $message,
                                'response' => 0
                              );
        }
        
        $this->output->set_content_type('application/json')->set_output(json_encode($output));
    }
    
    public function get_branch_list()
    {
        $output     = array();
        $lang_id    = intval($this->input->post('langId', TRUE));
        $lng        = strip_tags($this->input->post('lng', TRUE));
        $lat        = strip_tags($this->input->post('lat', TRUE));
        
        $list       = $this->location_locator->get_branch_list($lat.','.$lng, $lang_id);
        $list       = json_decode($list);
        
        
        if(count($list) != 0)
        {
            foreach($list as $branch)
            {
                $output [] = array(
                                'id'        => $branch->id      ,
                                'lat'       => $branch->lat     ,
                                'lng'       => $branch->lng     ,
                                'distance'  => $branch->distance,
                                'address'   => $branch->address ,
                                'name'      => $branch->name    ,
                                'phone'     => $branch->phone   ,
                                'image'     => $branch->image
                            );
            }
        }
        else
        {
            $message   = $this->general_model->get_lang_var_translation('no_data', $lang_id);
            $output    = array(
                                'message'  => $message,
                                'response' => 0
                              );
        }
        
        $this->output->set_content_type('application/json')->set_output(json_encode($output));
    }
    
    
}