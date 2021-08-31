<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cashu_methods extends CI_Controller {
    
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model('payment_log_model');
        $this->load->model('users/countries_model');
        $this->load->model('orders/orders_model');
        
        require(APPPATH . 'includes/front_end_global.php');
        $this->session->set_userdata('site_redir', current_url());
        $this->load->library('payment_gateways/cashu');
        
    }
    
    public function cashu_return()
    {
        $lang_id      = $this->user_bootstrap->get_active_language_row()->id;
        $posted_token = $_POST['token'];
        $order_data   = $this->orders_model->get_order_data($_POST['text1']);
        $currency     = strtolower($this->countries_model->get_currency_symbol($lang_id, $order_data->country_id));
        
        $token_check = $this->cashu->is_valid_token($posted_token, $order_data->final_total, $currency);
        
        if($token_check)
        {
            $log_data = array(
                                'user_id'         => $_POST['text2'],
                                'ip_address'      => $this->input->ip_address(),
                                'type'            => 'cashu',
                                'currency'        => $_POST['currency'],
                                'order_id'        => $_POST['text1'],
                                'transaction_id'  => $_POST['trn_id'],
                                'unix_time'       => time(),
                                'json_text'       => json_encode($_POST)
                             );
            $this->payment_log_model->insert_payment_log($log_data);
            
            redirect('orders/order/view_order_details/'.$_POST['text1'], 'refresh');
            
        }
        else
        {
            redirect('404_override', 'refresh');
        }
    }
    
    public function return1()
    {
        print_r($_POST);
    }
    
    public function return2()
    {
        print_r($_POST);
    }
    
    public function return3()
    {
        print_r($_POST);
    }
    
    /*
     Array
    (
        [language] => en
        [amount] => 125
        [currency] => USD
        [session_id] => asdasd-234-asdasd
        [txt1] => item27
        [txt2] => 12546
        [txt3] => islam4545
        [txt4] => 
        [txt5] => 
        [token] => 1380fdc614179cf702333b4e3887963a
        [trn_id] => 4080224
        [verificationString] => 665fa82b634c0cab9cc3dea693be5bfd21fd2cfa
        [trnDate] => 2015-12-08 09:49
        [servicesName] => 
        [netAmount] => 116.25
    )*/
    
    /*********************************************************/
  
}
/* End of file paypal_methods.php */
/* Location: ./application/modules/orders/controllers/paypal_methods.php */