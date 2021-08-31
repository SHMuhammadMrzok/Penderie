<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Serials_lib
{
    public $CI;
    
    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->model('products/products_serials_model');
    }
    
    public function insert_log($log_data)
    {
        $this->CI->products_serials_model->insert_serial_log($log_data);
    }
    
    
}