<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Site_setting extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('general_model');
        $this->load->library('Gateways');

        $this->load->library('api_lib');
    }

    public function index( )
    {
       $lang_id  = intval($this->input->post('langId', TRUE));

       $output   = array();

       $settings = $this->general_model->get_site_settings($lang_id);


       if($settings)
  	   {
  	       $adds            = '';
           $mobiles         = '';
           $telephones      = '';
           $emails          = '';

	       foreach(json_decode($settings->address) as $add)
           {
                $adds .= $add." , ";
           }

           foreach(json_decode($settings->mobile) as $mobile)
           {
                $mobiles .= $mobile." , ";
           }

           foreach(json_decode($settings->telephone) as $telephone)
           {
                $telephones .= $telephone." , ";
           }


           foreach(json_decode($settings->email) as $email)
           {
                $emails .= $email." , ";
           }


           if($settings->vat_percent == 0)
           {
             $vat_message = '';
           }
           else
           {
             $vat_message = $this->general_model->get_lang_var_translation('vat_value',$lang_id); // vat_type
           }

           if($this->gateways->get_gateway_field_value('', 13) != false)
           {
                $tawk_key = $this->gateways->get_gateway_field_value('', 13);
           }
           else
           {
                $tawk_key = '';
           }

           $output = array(
                            'site_name'     => $settings->site_name ,
                            'address'       => $adds                ,
                            'mobile'        => $mobiles             ,
                            'telephone'     => $telephones          ,
                            'whatsApp'      => $settings->whats_app_number,
                            'emails'        => $emails              ,
                            'facebook'      => $settings->facebook  ,
                            'twitter'       => $settings->twitter   ,
                            'youtube'       => $settings->youtube   ,
                            'instagram'     => $settings->instagram ,
                            'linkedin'      => $settings->linkedin ,
                            'googleapi_key' => $settings->googleapi_key,
                            'tawlk_id'      => $tawk_key,
                            'appStore'      => $settings->ios_app_link,
                            'vatType'       => $settings->vat_type    ,
                            'vatMessage'    => $vat_message,
                            'vatPercent'    => $settings->vat_percent  ,
                            'taxNumber'     => $settings->tax_number
                           );
	   }
       else
       {
	       $fail_message = $this->general_model->get_lang_var_translation('execution_fail',$lang_id);
	       $output       = array(
                                    'message' => $fail_message,
                                    'response' => 0
                                );
	   }

       $this->output->set_content_type('application/json')->set_output(json_encode($output));

    }


/************************************************************************/
}
