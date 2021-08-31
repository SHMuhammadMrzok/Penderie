<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Slider extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('slider_model');
        $this->load->model('general_model');
        $this->load->library('api_lib');
        $this->load->model('advertisements/advertisement_model');

    }

    public function index()
    {

        //$slider_images  = $this->slider_model->get_slider_images();
        $lang_id        = intval($this->input->post('langId', TRUE));

        $slider_images = $this->advertisement_model->get_advertisments($lang_id, 'top');
        
        $output         = array();
        //$amazon_path = "https://maskaninet.s3.eu-west-2.amazonaws.com/goldy/";
        $images_path    = $this->api_lib->get_images_path();

        if(count($slider_images) != 0 )// && !empty($slider_images))
        {
              foreach($slider_images as $slider)
              {
                if(isset($slider->image)&& $slider->image != '')
                {
                    //$pic = base_url().'assets/uploads/'.$slider->image;
                    $pic = $images_path.$slider->image;
                }else{
                   $pic = '';
                }

                $output [] = array(
                                    'advId'             => $slider->id,
                                    'advImage'          => $pic,
                                    'url'               => $slider->url,
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
