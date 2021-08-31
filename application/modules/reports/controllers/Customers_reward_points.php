<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Customers_reward_points extends CI_Controller
{
    public   $lang_row;
 
    public function __construct()
    {
        parent::__construct();
        
        require(APPPATH . 'includes/global_vars.php');
        
        $this->load->library('pagination');
        $this->load->library('encryption');
        
        $this->load->model('users/users_model');
        $this->load->model('orders/orders_model');
        
        $this->config->load('encryption_keys');
        
        $this->lang_row = $this->admin_bootstrap->get_active_language_row(); 
    }
    
    
    public function index($page_id = 1) //reward_points
    {
        $secret_key = $this->config->item('new_encryption_key');
        
        $lang_id = $this->data['active_language']->id;
        $perPage = 25; 
        $offset  = ($page_id -1 ) * $perPage;

        if($offset < 0)
        {
            $offset = $perPage;
        }
        
        $config['base_url']         = base_url()."reports/customers_reward_points/index";
        $config['per_page']         = $perPage;
        $config['uri_segment']      = 4;
        $config['use_page_numbers'] = TRUE;
        $config['total_rows']       = $this->users_model->get_users_count();
        
        $config['first_link']      = lang('first_page');
        $config['last_link']       = lang('last_page');
        $config['first_tag_open']  = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open']   = '<li>';
        $config['last_tag_close']  = '</li>';
        $config['next_tag_open']   = '<li>';
        $config['next_tag_close']  = '</li>';
        $config['prev_tag_open']   = '<li>';
        $config['prev_tag_close']  = '</li>';
        $config['num_tag_open']    = '<li>';
        $config['num_tag_close']   = '</li>';
        $config['cur_tag_open']    = '<li><strong>';
        $config['cur_tag_close']   = '</strong></li>';
        
        $config['display_pages']   = TRUE;
        
        $this->pagination->initialize($config);
        
        $all_users          = $this->users_model->get_all_users_data($lang_id, $perPage, $offset);
        $default_currency   = $this->admin_bootstrap->get_default_currency_symbol();
        
        $users_data         = array();
        $total_balances     = 0;
         
        foreach($all_users as $user)
        {
            $secret_iv                           = $user->id;
            
            $user_points                         = 0;
            $user_balance_with_default_currency  = 0;
            
            if($user->user_points != '')
            {
                $user_points  = $this->encryption->decrypt($user->user_points, $secret_key, $secret_iv);
            }
            
            if($user->user_balance != '')
            {
                $user_balance                       = $this->encryption->decrypt($user->user_balance, $secret_key, $secret_iv);
                $user_balance_with_default_currency = $this->admin_bootstrap->get_amount_with_default_currency($user_balance, $user->store_country_id);
            }
            
            $user->{'reward_points'} = $user_points;
            $user->{'balance'}       = round($user_balance_with_default_currency, 2).' '.$default_currency;
            
            $user_orders             = $this->orders_model->get_user_orders_count($user->id);
            $user->{'orders_count'}  = $user_orders;
            
            if($user->active == 1)
            {
                $user->{'status'} = '<span class="badge badge-success">' .lang('active'). '</span>';
            }
            else
            {
                $user->{'status'} = '<span class="badge badge-danger">' .lang('not_active'). '</span>';
            }
            
            $users_data[]    = $user;
            //$total_balances += $user_balance_with_default_currency;
        }
        
        $user_balances  = $this->users_model->get_users_total_balances();
         
        foreach ($user_balances as $user)
        {
             $user_balance                       = $this->encryption->decrypt($user->user_balance, $secret_key, $user->id);
             $user_balance_with_default_currency = $this->admin_bootstrap->get_amount_with_default_currency($user_balance, $user->store_country_id);
             
             $total_balances += $user_balance_with_default_currency;
        }
        
        $this->data['users_data']     = $users_data;
        $this->data['pagination']     = $this->pagination->create_links();
        $this->data['total_balances'] = round($total_balances, 2).' '.$default_currency;
        
        $this->data['content']    = $this->load->view('customers_reward_points', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
    }
    
    
    

    
     
    
/************************************************************************/    
}