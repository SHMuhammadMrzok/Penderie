<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Upgrade_account extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        require(APPPATH . 'includes/front_end_global.php');

        $this->load->model('customer_groups_model');
        if(!$this->data['is_logged_in'])
        {
            redirect('User_login', 'refresh');
        }
    }

    public function index()
    {
      $default_customer_group = $this->config->item('new_user_customer_group_id');

      $excluded_groups = array($default_customer_group);
      $min_price = 0;

      if($this->data['customer_group_id'] != $default_customer_group)
      {
        $excluded_groups[] = $this->data['customer_group_id'];
        $min_price = $this->data['user']->customer_group_price;
      }

      $customer_groups = $this->customer_groups_model->get_upgade_account_groups($this->data['lang_id'], $excluded_groups, $min_price);
      $this->data['customer_groups'] = $customer_groups;

      $this->data['content'] = $this->load->view('upgrade_account', $this->data, true);
      $this->load->view('site/main_frame',$this->data);
    }
    
}
