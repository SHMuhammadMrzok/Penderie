<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Gateways_response
{
    public $CI ;

    public function __construct($params = array())
    {
        $this->CI = &get_instance();

        $this->CI->load->library('orders');

        $this->CI->load->model('orders/orders_model');
        $this->CI->load->model('orders/payment_log_model');
        $this->CI->load->model('orders/order_status_model');
    }
    
    public function handleStatus($lang_id, $status, $payment_method_id, $order_id, $ip, $currency, $pay_id, $text, $is_cart=0)
    {

        $status_id  = $this->_getStatusId($status);
        
        $user_id  = $this->_order_user_id($order_id, 'shopping_cart', 'id');
        $store_id = 0;
        
        if($is_cart)
        {
            if($order_id != 0 )
            {
              $log_data  = array(
                                    'payment_method_id' => $payment_method_id   ,
                                    'user_id'           => $user_id             ,
                                    'ip_address'        => $ip                  ,
                                    'currency'          => $currency            ,
                                    'order_id'          => $order_id            ,
                                    'transaction_id'    => $pay_id              ,
                                    'status_id'         => $status_id           ,
                                    'store_id'          => $store_id,
                                    'http_referer'      => '',//$_SERVER['HTTP_REFERER'],
                                    'unix_time'         => time()               ,
                                    'feed_back_text'    => json_encode($text)
                                );

              $this->CI->payment_log_model->insert_payment_log($log_data);
            }
            
            $msg = $this->_getStatusMsg($status);
              
              
            
        }
        else
        {
            $msg = '';//('order_already_updated');
        }

        return $msg;
    }
    
    /*public function handleStatus($lang_id, $status, $payment_method_id, $order_id, $ip, $currency, $pay_id, $text)
    {

        $user_id  = $this->_order_user_id($order_id, 'shopping_cart', 'id');
        $status_id  = $this->_getStatusId($status);
        $order_data = $this->_getOrderData($order_id);

        //echo "status_id : ".$status_id."<br />";

        if(count($order_data) != 0)//($order_data->order_status_id == 8)
        {
            $is_exist_pay_id = false;

            if($pay_id != '')
            {
                // check if exist pay id
                $is_exist_pay_id = $this->CI->orders_model->check_if_exist_pay_id($pay_id, $order_id);
            }

            if($is_exist_pay_id)
            {
                $msg = lang('paid_before');
            }
            else
            {
                $this->_updateOrderStatusId($lang_id, $order_id, $status_id, $payment_method_id);

                $log_data  = array(
                                      'payment_method_id' => $payment_method_id   ,
                                      'user_id'           => $user_id             ,
                                      'ip_address'        => $ip                  ,
                                      'currency'          => $currency            ,
                                      'order_id'          => $order_id            ,
                                      'transaction_id'    => $pay_id              ,
                                      'status_id'         => $status_id           ,
                                      'store_id'          => $order_data->store_id,
                                      'http_referer'      => '',//$_SERVER['HTTP_REFERER'],
                                      'unix_time'         => time()               ,
                                      'feed_back_text'    => json_encode($text)
                                  );

                $this->CI->payment_log_model->insert_payment_log($log_data);

                $msg = $this->_getStatusMsg($status);
            }

        }
        else
        {
            $msg = lang('payment_failure_msg');//('order_already_updated');
        }

        return $msg;
    }*/


    private function _getStatusId($status)
    {
        $status_id = $this->CI->payment_log_model->get_payment_status_id($status);

        return $status_id;
    }

    private function _order_user_id($order_id, $table='orders', $field='cart_id')
    {
        //$order_data = $this->CI->orders_model->get_order_data($order_id);
        $order_data = $this->CI->orders_model->get_table_data($table, array($field=>$order_id), 'row');
        return $order_data->user_id;
    }
    
    private function _getStatusMsg($status)
    {
        if($status == 'success' || $status == 'initiated')
        {
            $msg = lang('payment_success_msg');
        }
        else if($status == 'failure')
        {
            $msg = lang('payment_failure_msg');
        }
        else if($status == 'pending')
        {
            $msg = lang('pending_redirection_msg');
        }
        else
        {
            $msg = lang('payment_invalid_msg');
        }

        return $msg;
    }

    private function _updateOrderStatusId($lang_id, $order_id, $status_id, $payment_method_id)
    {
        if($status_id == 1)
        {
            //order takes payment method status id
            $order_status_id =  $this->CI->order_status_model->get_status_id($payment_method_id, 'payment_methods');


            if($order_status_id == 1) // order accepted
            {
                $this->CI->orders->approve_order($order_id, $lang_id);
            }
            elseif($order_status_id == 3 || $order_status_id == 4) // order rejected or deleted
            {
                $this->CI->orders->canceled_orders_operations($order_id, 3);
            }
            else
            {
                // update order status
                $order_data['order_status_id'] = $order_status_id;
                $this->CI->orders_model->update_order_data($order_id, $order_data);

                // update order log
                $log_data = array(
                                    'order_id'  => $order_id        ,
                                    'status_id' => $order_status_id ,
                                    'unix_time' => time()
                                 );

                $this->CI->orders_model->insert_order_log($log_data);


            }

        }
        elseif($status_id == 3 || $status_id == 4)
        {
            $this->CI->orders->canceled_orders_operations($order_id, 3);  // rejected order
        }

    }

    private function _getOrderData($order_id)
    {
        $order_data = $this->CI->orders_model->get_order($order_id);

        return $order_data;
    }
}
