<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Paypal_methods extends CI_Controller {
    
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model('payment_log_model');
        
        require(APPPATH . 'includes/front_end_global.php');
        $this->session->set_userdata('site_redir', current_url());
        $this->load->library('payment_gateways/paypal');
        
    }
    
    public function submit()
    {
        $items  = $_POST;
        $INVNUM = $this->input->post('order_id');
        $order_total = $this->input->post('total');
        $paypal_data = $this->payment_log_model->get_paypal_data();
        
        $paypal_tax  = intval($paypal_data->extra_fees) + (intval($paypal_data->extra_fees_percent) * intval($order_total)); 
        echo $paypal_tax.'---'.$paypal_data->extra_fees .'-dfffff--'.$paypal_data->extra_fees_percent;die();
        $other_costs = array('TotalTaxAmount' => $paypal_tax);
        
        $this->paypal->execute_send_request($items , $other_costs, $INVNUM);
    }
    
    public function return_url()
    {
        $token    = $_GET['token'];
        $PayerID  = $_GET['PayerID'];
        $order_id = $_GET['INVNUM'];
        $user_id  = $this->ion_auth->user()->id;
        
        $result_array = $this->paypal->get_result($token, $PayerID);
        
        $ack = trim($_GET['ACK']);
        
        if($ack == 'Success' || $ack == 'SuccessWithWarning')
        {
            $status_id = 4;
        }else
        {
            $status_id = 2;
        }
        
        
        $insert_data  = array(
                                'user_id'     => $user_id,
                                'total_price' => $_GET['AMT'],
                                'currency'    => $_GET['CURRENCYCODE'],
                                'order_id'    => $_GET['INVNUM'],
                                'status_id'   => $status_id,
                                'unix_time'   => time()
                             );
        
        $this->payment_log_model->insert_payment_log($insert_data);
        
        redirect('orders/order/view_order_details/'. $order_id, 'refresh');
    }

    
    
    /*********************************************************/
  
}
/* End of file paypal_methods.php */
/* Location: ./application/modules/orders/controllers/paypal_methods.php */