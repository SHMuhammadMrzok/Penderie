<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class User_bank_accounts extends CI_Controller
{
   
    public function __construct()
    {
        parent::__construct();
        
        $this->load->library('cart');
        
        $this->load->model('shopping_cart/user_bank_accounts_model');
        
        require(APPPATH . 'includes/front_end_global.php');
    }
    
    public function view_user_bank_accounts()
    {
        $user_id                = $this->ion_auth->user()->row()->id;
        $bank_id                = $this->input->post('bank_id');
        
        //$user_account_data      = $this->user_bank_accounts_model->get_bank_accounts(); 
        $user_bank_account_data = $this->user_bank_accounts_model->get_user_bank_account($bank_id, $user_id);
        //print_r($user_bank_account_data);
        if($user_bank_account_data)
        {
           $this->data['general_data'] = $user_bank_account_data;
           
        }
        else
        {
           $this->data['general_data'] = array();  
        }
        
        $this->data['bank_id']      = $bank_id;
        
        $this->load->view('user_bank_accounts_form',$this->data);
    }
    
    public function save()
    { 
        if($this->ion_auth->logged_in())
        { 
            $bank_id = $this->input->post('bank_id');
            $this->form_validation->set_rules("account_name", lang('account_name'), 'required');
            $this->form_validation->set_rules("account_number", lang('account_number'), 'required');
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            
            if ($this->form_validation->run() == FALSE)
            {
                echo 'form validation error';
            }
            else
            {
                $user_id            = $this->ion_auth->user()->row()->id;
                $account_name       = $this->input->post("account_name");
                $account_number     = $this->input->post("account_number");
                
                $bank_account_count = $this->user_bank_accounts_model->count_bank_accounts_for_user($bank_id, $user_id);
                
                if($bank_account_count > 0)
                {
                    $this->user_bank_accounts_model->delete_bank_account($bank_id, $user_id);
                }
                
                $data    = array(
                                    'user_id'        => $user_id ,
                                    'bank_id'        => $bank_id ,
                                    'account_name'   => $account_name ,
                                    'account_number' => $account_number
                                ); 
                              
                $this->user_bank_accounts_model->insert_user_account_data($data);
            }
        }
        else
        {
            echo 'not logged in';
        }
    }
    
    /*public function update()
    {
        if($this->ion_auth->logged_in())
        {
            $bank_id        = $this->input->post('user_bank_account_id');
            
            $account_name   = $this->input->post("account_name_$bank_id");
            $account_number = $this->input->post("account_numbe_$bank_id");
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            
            if ($this->form_validation->run() == FALSE)
            {
                echo 'form validation error';
            }
            else
            {
                
                $account_name     = $this->input->post("account_name_$bank_id");
                $account_number   = $this->input->post("account_numbe_$bank_id");
                
                $data    = array(
                                    'bank_id'        => $bank_id ,
                                    'account_name'   => $account_name ,
                                    'account_number' => $account_number
                                ); 
                                
                $this->user_bank_accounts_model->update_user_account_data($data, $user_bank_acc_id);
            }
        }
        else
        {
            echo 'not logged in';
        }
    }*/
    
/************************************************************************/    
}