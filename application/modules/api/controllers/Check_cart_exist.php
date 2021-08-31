<?php
if(!defined('BASEPATH'))
    exit('No Direct script access allowed');

class Check_cart_exist extends CI_Controller
{
    public function __construct()
    {
        parent :: __construct();

        $this->load->library('shopping_cart');
        $this->load->model('shopping_cart_model');
        $this->load->model('general_model');
        

    }

    public function index()
    {
      $cart_id    = intval($this->input->post('cartId', true));
      $lang_id    = intval($this->input->post('langId', true));
      
      $order_id   = 0;
      $message    = '';
      
      $cart_exist = $this->shopping_cart_model->check_cart_exist($cart_id);
      if(!$cart_exist && $cart_id != 0)
      {
        $order_id = $this->shopping_cart_model->get_cart_order_id($cart_id);
        $message  = $this->general_model->get_lang_var_translation('order_inserted_successfully', $lang_id);
        
      } 

      $output = array(
        'status' => $cart_exist,
        'orderId' => $order_id,
        'message' => $message
      );

      $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));

    }
}
