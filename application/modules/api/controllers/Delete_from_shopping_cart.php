<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Delete_from_shopping_cart extends CI_Controller
{
  
    public function __construct()
    {
        parent::__construct();
        
        $this->load->library('api_lib');
        $this->load->library('shopping_cart');
        
        $this->load->model('general_model');
        
    }

    public function index()
    {
        
        $userId         = intval(strip_tags($this->input->post('userId', TRUE)));
        $email          = strip_tags($this->input->post('email', TRUE));
        $password       = strip_tags($this->input->post('password', TRUE));
        
        $deviceId       = strip_tags($this->input->post('deviceId', TRUE));
        $ip_address     = $this->input->ip_address();
        
        $country_id     = intval(strip_tags($this->input->post('countryId', TRUE)));
        $lang_id        = intval(strip_tags($this->input->post('langId', TRUE)));
        $productId      = intval(strip_tags($this->input->post('productId', TRUE)));
        $cartRowId      = intval(strip_tags($this->input->post('cartRowId', TRUE)));
        
        if($this->ion_auth->login($email, $password))
        {
            $user_data = $this->ion_auth->user()->row();
            $this->api_lib->check_user_store_country_id($email, $password, $user_data->id, $country_id);
        }
        
        $this->shopping_cart->set_user_data($userId, $deviceId, $ip_address , $country_id ,$lang_id);
        $delete_result   = $this->shopping_cart->destroy($cartRowId);
        
        if($delete_result[0] == 1)
        {
            $response = 1;
            $message  = $this->general_model->get_lang_var_translation('product_removed_from_cart', $lang_id);
        }
        else
        {
            $response = 0;
            $message  = $this->general_model->get_lang_var_translation('product_not_removed_from_cart', $lang_id);
        }
        
        $output = array(
                          'message'  => $message,
                          'response' => $response
                       );
        
        $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE)); 
        
    }
       
     
/************************************************************************/    
}