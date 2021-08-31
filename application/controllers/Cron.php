<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cron extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        
        $this->load->library('notifications');
        $this->load->library('encryption');
        
        $this->load->model('global_model');
        $this->load->model('orders/orders_model');
        $this->load->model('orders/order_status_model');
        $this->load->model('products/products_model');
        $this->load->model('products/products_serials_model');
        $this->load->model('users/users_model');
        $this->load->model('users/user_model');
    }
    
    public function index()
    {
        
    }
    
    public function processCronJobs()
    {
        $this->load->library('orders/orders');
         
        $this->orders->process_auto_cancel_orders();
        //$this->orders->send_order_delay_sorry_email();
        //$this->orders->check_to_be_sent_serials();
    }
}