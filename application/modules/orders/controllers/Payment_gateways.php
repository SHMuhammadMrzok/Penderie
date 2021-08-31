<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Payment_gateways extends CI_Controller {

    public $data;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('payment_log_model');
        $this->load->model('orders/orders_model');
        $this->load->model('products/products_model');
        $this->load->model('users/countries_model');
        $this->load->model('users/users_model');
        $this->load->model('payment_options/payment_methods_model');

        require(APPPATH . 'includes/front_end_global.php');

        $this->config->load('encryption_keys');

        $this->load->library('encryption');
        $this->load->library('payment_gateways/paypal');
        $this->load->library('payment_gateways/payfort');
        $this->load->library('payment_gateways/cashu');
        $this->load->library('payment_gateways/knet');
        $this->load->library('payment_gateways/sadad');
        $this->load->library('payment_gateways/hyperpay');
        $this->load->library('payment_gateways/moyasar');
        //$this->load->library('payment_gateways/myFatoora');

        $this->load->library('payment_gateways/gateways_response');

    }

    public function submit()
    {
        $type     = $this->input->post('type');
        $order_id = intval($this->input->post('order_id'));
        if($order_id > 0 && $type == 'paypal')
        {
            $this->process_paypal($order_id);
        }
        elseif($order_id > 0 && $type == 'knet')
        {
            $this->process_knet($order_id);
        }
        elseif($order_id > 0 && $type == 'sadad')
        {
            $this->process_sadad($order_id);
        }
        elseif($order_id > 0 && ($type == 'hyperpay_sadad' || $type == 'hyperpay_visa' || $type == 'mada' || $type == 'hyperpay_stc_pay') )
        {
            $this->process_hyperpay_from_cart($order_id,$type);
        }
        elseif($order_id > 0 && $type == 'moyasar')
        {
            $this->process_moyasar($order_id);
        }
        elseif($order_id > 0 && $type == 'myFatoora')
        {
            $this->process_myFatoora($order_id);
        }
    }

    public function process_paypal($order_id)
    {
        $this->load->library('products_lib');

        $items = array();
        $lang_id         = $this->data['active_language_row']->id;
        $order_products  = $this->orders_model->get_order_product_details($order_id);
        $order_data      = $this->orders_model->get_order_data($order_id);
        $currency_symbol = 'usd';//$order_data->currency_symbol;
        $tax             = $order_data->tax;
        $tax             = $this->_change_price_into_dollars($tax, $order_data->country_id);

        foreach($order_products as $item)
        {
            $price = $item->final_price;
            $price = $this->_change_price_into_dollars($price, $order_data->country_id);

            if($item->product_id != 0)
            {
                $translation_data = $this->products_model->get_product_translation_data($item->product_id, $lang_id);
                $title            = $translation_data->title;
                $description      = $translation_data->description;

            }
            else
            {
                $title = 'recharge card';
                $description  = 'recharge card';
            }

            $items["itemname"][]  = $title; //Item Name
            $items["itemprice"][] = $price; //Item Price
            $items["itemdesc"][]  = $description;
            $items["itemQty"][]   = $item->qty; // Item Quantity
        }

        $other_costs['TotalTaxAmount'] = $tax;

        if(isset($items["itemprice"]) && count($items["itemprice"]) > 0 && array_sum($items["itemprice"]) > 0)
        {
            $this->paypal->execute_send_request($items, $other_costs, $order_id, $currency_symbol);
        }
        else
        {
            echo 'Check the price';
        }
    }

    public function process_knet($order_id)
    {
        $notes       = array();
        $order_data  = $this->orders_model->get_order_data($order_id);

        // total with kuwit Denar
        $order_total = $this->currency->convert_to_currency($order_data->currency_symbol, 'KWD', $order_data->final_total);

        $notes[]     = $order_data->currency_symbol;
        $this->knet->execute_send_request($order_id, $order_total, $notes);
    }

    public function process_sadad($order_id)
    {
        $order_data = $this->orders_model->get_order_data($order_id);

        $this->sadad->INITIATE_PAYMENT($order_id, $order_data->final_total, $order_data->currency_symbol);
    }

    /*public function process_hyperpay($order_id,$type)
    {
        if(isset($this->data['lang_id']))
        {
          $lang_id = $this->data['lang_id'];
        }
        else {
          $lang_id = 1;
        }

        $order_data         = $this->orders_model->get_order_details($order_id, $lang_id);
        $user_data          = $this->users_model->get_user($order_data->user_id);
        $user_ip            = $this->input->ip_address();
        $country_data       = $this->countries_model->get_countries_result($order_data->country_id);
        $country_iso        = $country_data->country_symbol;

        // total with SAR
        $gateway_currency   = 'SAR';
        $order_total        = $this->currency->convert_to_currency($order_data->currency_symbol, $gateway_currency, $order_data->final_total);

        if($type == 'hyperpay_sadad')
        {
            $payment_brand  = 'SADAD';
            $this->hyperpay->initSadadCheckout($order_id, $order_total , $gateway_currency , $user_ip );
        }
        elseif($type == 'hyperpay_visa')
        {
            $payment_brand  = 'VISA MASTER';
            $form           = $this->hyperpay->prepareCheckout($order_id, $order_total , $gateway_currency , $user_ip ,$user_data->email  ,$payment_brand, $order_data, $user_data, $country_iso);
            echo $form;
        }
        elseif($type == 'mada')
        {
            $payment_brand  = 'MADA';
            $form           = $this->hyperpay->prepareCheckout($order_id, $order_total , $gateway_currency , $user_ip ,$user_data->email  ,$payment_brand, $order_data, $user_data, $country_iso);
            echo $form;
        }

    }*/
    
    public function process_myFatoora($order_id)
    {
        if(isset($this->data['lang_id']))
        {
          $lang_id = $this->data['lang_id'];
        }
        else {
          $lang_id = 1;
        }

        $order_data         = $this->orders_model->get_order_details($order_id, $lang_id);
        /*$user_data          = $this->users_model->get_user($order_data->user_id);
        $user_ip            = $this->input->ip_address();
        $country_data       = $this->countries_model->get_countries_result($order_data->country_id);
        $country_iso        = $country_data->country_symbol;

        // total with SAR
        $gateway_currency   = 'SAR';
        $order_total        = $this->currency->convert_to_currency($order_data->currency_symbol, $gateway_currency, $order_data->final_total);

        */
        $this->load->library('payment_gateways/myFatoora', $order_id);
        //$this->myFatoora->Hello();

         //$this->myFatoora->prepareCheckoutFatoora($order_id);//, $order_total , $gateway_currency , $user_ip ,$user_data->email  ,$payment_brand, $order_data, $user_data, $country_iso);
         //echo $form;
        

    }

    public function process_hyperpay_from_cart($cart_id, $type)
    {
        $this->load->model('shopping_cart_model');
        
        $cart_id =  explode('_', $cart_id)[0];
        $order_data   = $this->shopping_cart_model->get_cart_data($cart_id);
        $user_data    = $this->user_model->get_row_data($order_data->user_id);
        $order_id     = $order_data->id;
        $order_total  = $order_data->final_total_price_with_tax;
        $user_email   = $user_data->email;
        $user_phone   = $user_data->phone;
        $country_data = $this->countries_model->get_countries_result($order_data->country_id);
        $country_iso  = $country_data->country_symbol;
        $user_ip      = $this->input->ip_address();

        // total with SAR
        $gateway_currency   = 'SAR';

        // echo "<br /> Payment Gateways Controller : process_hyperpay_from_cart => cart_id <pre>";
        // print_r($cart_id);
        // echo "<br />============================================<br />";
        // echo "<br /> Payment Gateways Controller : process_hyperpay_from_cart => type <pre>";
        // print_r($type);
        // echo "<br />============================================<br />";

        if($type == 'hyperpay_sadad')
        {
            $payment_brand  = 'SADAD';
            $this->hyperpay->initSadadCheckout($order_id, $order_total , $gateway_currency , $user_ip );
        }
        elseif($type == 'hyperpay_visa')
        {
            //$payment_brand  = 'VISA MASTER MADA STC_PAY';
            $payment_brand  = 'VISA MASTER';
            $form           = $this->hyperpay->prepareCheckout($order_id, $order_total , $gateway_currency , $user_ip ,$user_email  ,$payment_brand, $order_data, $user_data, $country_iso);

            echo $form;
        }
        elseif($type == 'hyperpay_stc_pay')
        {
            $payment_brand  = 'STC_PAY';
            $form           = $this->hyperpay->prepareCheckout($order_id, $order_total , $gateway_currency , $user_ip ,$user_data->email  ,$payment_brand, $order_data, $user_data, $country_iso);

            echo $form;
        }
        elseif($type == 'mada')
        {
            $payment_brand  = 'MADA';
            $form           = $this->hyperpay->prepareCheckout($order_id, $order_total , $gateway_currency , $user_ip ,$user_data->email  ,$payment_brand, $order_data, $user_data, $country_iso);

            echo $form;
        }
        elseif($type == 'apple_pay')
        {
            $payment_brand  = 'apple_pay';
            $form           = $this->hyperpay->prepareCheckout($order_id, $order_total , $gateway_currency , $user_ip ,$user_data->email  ,$payment_brand, $order_data, $user_data, $country_iso);

            echo $form;
        }


    }

    public function process_moyasar($order_id)
    {
        $this->config->load('payment_gateways');
        $order_data = $this->orders_model->get_order_data($order_id);
        $api_key = 'pk_test_QGHvAeaWy6aKY26tPBCapFkKbUZcs3qY32uEzyvo';

        //create payment form
        $this->data['order_id'] = $order_id;
        $this->data['action'] = 'https://api.moyasar.com/v1/payments^-u '.$api_key.':';//base_url().'orders/payment_gateways/submit_moyasar_form';//base_url().'orders/payment_gateways/submit_moyasar_form';//'https://api.moyasar.com/v1/payments.html';//base_url().'orders/payment_gateways/submit_moyasar_form';
        $this->data['redirect_url'] = $this->config->item('moyasarRedirectURL');
        $this->data['currency_symbol'] = $order_data->currency_symbol;
        $this->data['api_key'] = $api_key;

        $this->data['total_in_halalas'] = $order_data->final_total * 100;

        
        $this->load->view('moyasar', $this->data);

        //$this->moyasar->INITIATE_PAYMENT($order_id, $order_data->final_total, $order_data->currency_symbol);
    }

    public function submit_moyasar_form()
    {
      $order_id    = $this->input->post('order_id', true);
      $source_data = $this->input->post('source', true); //visa and mastercard data
      
      $order_data = $this->orders_model->get_order_data($order_id);
      $this->moyasar->INITIATE_PAYMENT($order_id, $order_data->final_total, $order_data->currency_symbol, $_POST);
    }
    
    public function feed_back_hyperpay($brand='')
    {
        $resourcePath   = $this->input->get('resourcePath');

        $apple_pay = 0;

        if($brand == 'apple_pay')
        {
            $result     = $this->hyperpay->getApplePayPaymentStatus($resourcePath);
            $apple_pay  = 1;
        }
        else
        {
            $result     = $this->hyperpay->getPaymentStatus($resourcePath, $brand);
        }

        $lang_id        = $this->data['lang_id'];

        /*to do handle if result is empty or have no data*/
        $paymentGatewayResponse = json_decode($result, true);
        
        $order_id       = $paymentGatewayResponse['merchantTransactionId'];
        $pay_id         = $paymentGatewayResponse['id'];
        //$currency       = isset($paymentGatewayResponse['currency'])?$paymentGatewayResponse['currency']:$order_data->currency_symbol;
        $resultCode     = $paymentGatewayResponse['result']['code'];
        $paymentBrand   = $paymentGatewayResponse['paymentBrand'];
        //$user_id        = $this->_order_user_id($order_id);

        $order_id = explode('_', $order_id)[0]; // remove unix time of order id


        //create order from cart id
        $this->load->library('cart_orders');
        $currency = 'SAR';
        $status  = $this->hyperpay->handleResponse($resultCode);
        /*if ($order_id == 13579)
        {
          $status = 'success' ;
        }
        */

        if(!$pay_id)
        {
            $pay_id = '';
        }

        if($paymentBrand == 'VISA' || $paymentBrand == 'MASTER'|| $brand == '' || $brand == 'hyperpay_visa')
        {
            $payment_method_id = '13';
        }
        elseif($paymentBrand == 'SADAD')
        {
            $payment_method_id = '12';
        }
        elseif($paymentBrand == 'STC_PAY')
        {
            $payment_method_id = '21'; //'14';
        }
        elseif($paymentBrand == 'MADA')
        {
            $payment_method_id = '15';
        }

        $ip_address = $this->input->ip_address();
        
        if($status ==  'success' )
        {
            $message    = $this->gateways_response->handleStatus($lang_id, $status, $payment_method_id , $order_id, $ip_address, $currency , $pay_id, $paymentGatewayResponse, 1);
          
            $order_data = $this->cart_orders->create_order_from_cart($order_id);
            $message    = $order_data['message'];//$this->gateways_response->handleStatus($lang_id, $status, $payment_method_id , $order_id, $ip_address, $currency , $pay_id, $paymentGatewayResponse, 1);
            
            $this->data['msg'] = $message;
            $redirect_url = base_url().'orders/order/view_order_details/'.$order_data['orderId'];


            // echo "<br /> Payment Gateways Controller : feed_back_hyperpay => order_data <pre>";
            // print_r($order_data);
            // echo "<br />============================================<br />";

            // echo "<br /> Payment Gateways Controller : feed_back_hyperpay => redirect_url <pre>";
            // print_r($redirect_url);
            // echo "<br />============================================<br />";
        }
        else {

          $message    = $this->gateways_response->handleStatus($lang_id, $status, $payment_method_id , $order_id, $ip_address, $currency , $pay_id, $paymentGatewayResponse, 1);

          $this->data['msg'] = $message;//$status;//lang('payment_failure_msg');
          $redirect_url = base_url().'Orders_Log';
        }

        // echo " lolololololol y";
        if($apple_pay)
        {
            // echo "<br /> Payment Gateways Controller : feed_back_hyperpay => apple_pay <pre>";
            // print_r($apple_pay);
            // echo "<br />============================================<br />";

            $this->output->set_content_type('application/json')->set_output(json_encode($result, JSON_UNESCAPED_UNICODE));
        }
        else
        {

            // echo "<br /> Payment Gateways Controller : feed_back_hyperpay => redirect_url <pre>";
            // print_r($redirect_url);
            // echo "<br />============================================<br />";
            // echo "<br /> Payment Gateways Controller : feed_back_hyperpay => this->data['msg'] <pre>";
            // print_r($this->data['msg']);
            // echo "<br />============================================<br />";
            // die();
            $this->data['redirect_url'] = $redirect_url;

            $this->load->view('gateway_msg', $this->data);
        }
    }

    /*
    public function feed_back_hyperpay($brand='')
    {
        $resourcePath   = $this->input->get('resourcePath');
        $result         = $this->hyperpay->getPaymentStatus($resourcePath, $brand);

        $paymentGatewayResponse = json_decode($result, true);

        $order_id       = $paymentGatewayResponse['merchantTransactionId'];
        $pay_id         = $paymentGatewayResponse['id'];
        $currency       = 'SAR';//$paymentGatewayResponse['currency'];
        $resultCode     = $paymentGatewayResponse['result']['code'];
        $paymentBrand   = $paymentGatewayResponse['paymentBrand'];
        //$user_id        = $this->_order_user_id($order_id);
        //$order_data     = $this->orders_model->get_order_data($order_id);
        $lang_id        = $this->data['lang_id'];


        if(!$pay_id)
        {
            $pay_id = '';
        }

        if($paymentBrand == 'VISA' || $paymentBrand == 'MASTER')
        {
            $payment_method_id = '13';
        }
        elseif($paymentBrand == 'SADAD')
        {
            $payment_method_id = '12';
        }

        $ip_address = $this->input->ip_address();

        /*  
        $status  = $this->hyperpay->handleResponse($resultCode);
        $message = '';

        if($status == 'success')
        {
          $this->load->library('cart_orders');
          $order_data = $this->cart_orders->create_order_from_cart($order_id);
          //print_r($order_data); die();
          $message = $this->gateways_response->handleStatus($lang_id, $status, $payment_method_id , $order_data['orderId'], $ip_address, $currency , $pay_id, $paymentGatewayResponse);
        }
        */
        /*        
        $status  = $this->hyperpay->handleResponse($resultCode);
        $message = $this->gateways_response->handleStatus($lang_id, $status, $payment_method_id , $order_id, $ip_address, $currency , $pay_id, $paymentGatewayResponse);


        $this->data['msg'] = $message;

        $this->data['redirect_url'] = base_url().'orders/order/view_order_details/'.$order_id;

        $this->load->view('gateway_msg', $this->data);
    }
    */

    public function feed_back_moyasar()
    {
        $pay_id          = $this->input->get('id', true);
        $response_status = $this->input->get('status', true);
        $paymentGatewayResponse = $_GET;
        $payment_method_id = 15;
        $order_id   = '';
        $ip_address = $this->input->ip_address();
        $currency   = '';

        $status  = $this->moyasar->handleResponse($response_status);

        $message = $this->gateways_response->handleStatus($lang_id, $status, $payment_method_id , $order_id, $ip_address, $currency , $pay_id, $paymentGatewayResponse);

        $this->data['msg'] = $message;

        $this->data['redirect_url'] = base_url().'Orders_Log';
        //base_url().'orders/order/view_order_details/'.$order_id;

        $this->load->view('gateway_msg', $this->data);
    }

    public function feed_back_hyperpay_sadad()
    {
        //  Get Merchant Ref Number
        $order_id           = $_GET["MerchantRefNum"];

        // Get Payment Status
        $paymentStatus      = $_GET["PaymentStatus"];

        // Get Error discription (Reason of payment failure)
        //$errorDescription = $_GET["ErrorDescription"];

        // Call init checkout process
        $paymentGatewayResponse = $this->hyperpay->getSadadCheckoutStatus($order_id);

        $lang_id            = $this->data['lang_id'];
        $currency           = '';;
        $pay_id             = '';//$paymentGatewayResponse['id'];

        if(!$pay_id)
        {
            $pay_id = '';
        }

        $payment_method_id  = '12'; // Sadad Hyperpay Method ID

        $ip_address         = $this->input->ip_address();
        $status             = $this->hyperpay->handleResponse($paymentGatewayResponse->response);

        $message = $this->gateways_response->handleStatus($lang_id, $status, $payment_method_id , $order_id, $ip_address, $currency , $pay_id, $paymentGatewayResponse);

        /*
        echo "order_id : ".$order_id."<br />";
        echo "pay_id : ".$pay_id."<br />";
        echo "currency : ".$currency."<br />";
        echo "resultCode : ".$resultCode."<br />";
        echo "paymentBrand : ".$paymentBrand."<br />";
        echo "user_id : ".$user_id."<br />";
        echo "status : ".$status."<br />";
        echo "message : ".$message."<br />";
        echo "<pre>";  print_r($paymentGatewayResponse);die();
        */

        $this->data['msg'] = $message;

        $this->data['redirect_url'] = base_url().'orders/order/view_order_details/'.$order_id;

        $this->load->view('gateway_msg', $this->data);
    }

    public function feed_back_paypal()
    {
        $token      = $this->input->get('token');
        $PayerID    = $this->input->get('PayerID');
        $ip_address = $this->input->ip_address();
        $result     = $this->paypal->get_result($token, $PayerID);

        if(isset($result['ACK']) && isset($result['INVNUM']))
        {
            $order_id   = $result['INVNUM'];
            $user_id    = $this->_order_user_id($order_id);
            $pay_id     = $result['TRANSACTIONID'];
            $lang_id    = $this->data['lang_id'];

            if(!$pay_id)
            {
                $pay_id = '';
            }

            /***********STATUS************/
            /**success->1
            /**pending->2
            /**failure->3
            /**invalid->4
            /*****************************/

            $status = $this->paypal->handleResponse($result);

            $message = $this->gateways_response->handleStatus($lang_id, $status, 5, $order_id, $ip_address, '', $pay_id, $result);


            $this->data['msg'] = $message;

            $this->data['redirect_url'] = base_url().'orders/order/view_order_details/'.$order_id;

            $this->load->view('gateway_msg', $this->data);
        }
        else
        {
            $status_id  = 4;
            $log_data   = array(
                                  'payment_method_id' => 5,
                                  'ip_address'        => $ip_address,
                                  'status_id'         => $status_id,
                                  'unix_time'         => time(),
                                  'feed_back_text'    => json_encode($result)
                               );

            $this->payment_log_model->insert_payment_log($log_data);
        }
    }

    public function feed_back_payfort($mode = NULL, $method = 'client')
    {

        $status   = $this->payfort->handleResponse($_POST);
        $order_id = $this->input->post('merchant_reference');
        $ip       = $this->input->post('customer_ip');
        $currency = $this->input->post('currency');
        $pay_id   = $this->input->post('fort_id');
        $lang_id  = $this->data['lang_id'];

        if(!$pay_id)
        {
            $pay_id = '';
        }

        if(! $currency)
        {
            $currency = '';
        }

        $message = $this->gateways_response->handleStatus($lang_id, $status, 4, $order_id, $ip, $currency, $pay_id, $_POST);

        $this->data['msg'] = $message;

        $this->data['redirect_url'] = base_url().'orders/order/view_order_details/'.$order_id;

        $this->load->view('gateway_msg', $this->data);
    }

    public function feed_back_cashu($mode = NULL)
    {
        $order_id   = $this->input->post('txt1');
        $token      = $this->input->post('token');
        $pay_id     = $this->input->post('trn_id');
        $user_id    = $this->_order_user_id($order_id);
        $order_data = $this->orders_model->get_order_data($order_id);
        $lang_id    = $this->data['lang_id'];

        if(!$pay_id)
        {
            $pay_id = '';
        }

        $ip_address = $this->input->ip_address();

        $status  = $this->cashu->handleResponse($mode, $token, $order_data);
        $message = $this->gateways_response->handleStatus($lang_id, $status, 6, $order_id, $ip_address, '', $pay_id, $_POST);

        $this->data['msg'] = $message;

        $this->data['redirect_url'] = base_url().'orders/order/view_order_details/'.$order_id;

        $this->load->view('gateway_msg', $this->data);
    }

    public function feed_back_knet_native()
    {
        // Start of bakr code //
        $PaymentID = $_POST['paymentid'];
    	$presult   = $_POST['result'];
    	$postdate  = $_POST['postdate'];
    	$tranid    = $_POST['tranid'];
    	$auth      = $_POST['auth'];
    	$ref       = $_POST['ref'];
    	$trackid   = $_POST['trackid'];
    	$udf1      = $_POST['udf1'];
    	$udf2      = $_POST['udf2'];
    	$udf3      = $_POST['udf3'];
    	$udf4      = $_POST['udf4'];
    	$udf5      = $_POST['udf5'];

        $secret_key = $this->config->item('new_encryption_key');
        $secret_iv  = $_POST['trackid'];

        $PaymentID  = $this->encryption->encrypt($PaymentID, $secret_key, $secret_iv);
        $presult    = $this->encryption->encrypt($presult, $secret_key, $secret_iv);
        $tranid     = $this->encryption->encrypt($tranid, $secret_key, $secret_iv);


        if ( $presult == "CAPTURED" )
        {
            $result_url     = base_url() . 'orders/payment_gateways/feed_back_knet';
            $result_params  = "?PaymentID=" . $PaymentID . "&Result=" . $presult . "&PostDate=" . $postdate . "&TranID=" . $tranid . "&Auth=" . $auth . "&Ref=" . $ref . "&TrackID=" . $trackid . "&UDF1=" . $udf1 . "&UDF2=" .$udf2  . "&UDF3=" . $udf3  . "&UDF4=" . $udf4 . "&UDF5=" . $udf5  ;

            /*******************************************************************
        	/*******************************************************************

        	Merchant must send the email to customer containing all the transaction details if the transactino was successfull

        	*/
        }
        else
        {
            $result_url     = base_url() . 'orders/payment_gateways/feed_back_knet';
            $result_params  = "?PaymentID=" . $PaymentID . "&Result=" . $presult . "&PostDate=" . $postdate . "&TranID=" . $tranid . "&Auth=" . $auth . "&Ref=" . $ref . "&TrackID=" . $trackid . "&UDF1=" . $udf1 . "&UDF2=" .$udf2  . "&UDF3=" . $udf3  . "&UDF4=" . $udf4 . "&UDF5=" . $udf5  ;

        }
        echo "REDIRECT=".$result_url.$result_params;


        // End of bakr code //

    }


    public function feed_back_knet()
    {
		$secret_key = $this->config->item('new_encryption_key');
        $secret_iv  = $_GET['TrackID'];

        $payment_id = $this->input->get('PaymentID');
        $presult    = $this->input->get('Result');
        $postdate   = $this->input->get('PostDate');
        $tranid     = $this->input->get('TranID');
        $auth       = $this->input->get('Auth');
        $ref        = $this->input->get('Ref');
        $trackid    = $this->input->get('TrackID');
        $udf1       = $this->input->get('UDF1');
        $udf2       = $this->input->get('UDF2');
        $udf3       = $this->input->get('UDF3');
        $udf4       = $this->input->get('UDF4');
        $udf5       = $this->input->get('UDF5');

        $payment_id = $this->encryption->decrypt($payment_id, $secret_key, $secret_iv);
        $presult    = $this->encryption->decrypt($presult, $secret_key, $secret_iv);
        $tranid     = $this->encryption->decrypt($tranid, $secret_key, $secret_iv);

        $lang_id    = $this->data['lang_id'];
        $ip_address = $this->input->ip_address();

        $get_array  = array(
                                'PaymentID' => $payment_id  ,
                                'Result'    => $presult     ,
                                'PostDate'  => $postdate    ,
                                'TranID'    => $tranid      ,
                                'Auth'      => $auth        ,
                                'Ref'       => $ref         ,
                                'TrackID'   => $trackid     ,
                                'UDF1'      => $udf1        ,
                                'UDF2'      => $udf2        ,
                                'UDF3'      => $udf3        ,
                                'UDF4'      => $udf4        ,
                                'UDF5'      => $udf5
                            );

        $status  = $this->knet->handleResponse($presult);
        $message = $this->gateways_response->handleStatus($lang_id, $status, 9, $trackid, $ip_address, $udf1, $payment_id, $get_array);

        $this->data['msg'] = $message;
        $this->data['redirect_url'] = base_url().'orders/order/view_order_details/'.$order_id;

        $this->load->view('gateway_msg', $this->data);

    }

    public function feed_back_sadad($order_id = null)
    {
		if($order_id && isset($_GET['estn'])) {
			$transactionId = $this->input->get('estn');

			$response = $this->sadad->confirmPayment($transactionId, $order_id);
			echo '<br /><br /><br /><pre>';print_r($response);
			if(is_array($response)) {

                if(isset($response['curl_error_code']) && $response['curl_error_code'] == '35') {
        		  //if server curl error number == 35, then refresh
                  $this->data['order_id'] = $order_id;
                  $this->data['type'] = 'feed_back';
                  $this->data['estn'] = $transactionId;

                  $this->load->view('sadad_msg', $this->data);
                }
                else
                {
                    $lang_id = $this->data['lang_id'];
                    $ip      = $this->input->ip_address();
    				$status  = $this->sadad->handleResponse($response['paymentStatus']);
                    //$order_data      = $this->orders_model->get_order_data($order_id);
                    $currency       = 'SAR';


    				$message = $this->gateways_response->handleStatus($lang_id, $status, 11, $order_id, $ip, $currency, $_GET['estn'], $_GET);
    				$this->data['msg'] = $message;
    				$this->data['redirect_url'] = base_url().'orders/order/view_order_details/'.$order_id;
    				$this->load->view('gateway_msg', $this->data);

                }
			} else {
				echo "Failed!, Please try again ..";
			}
		} else {
			echo "Failed!, Please try again ..";
		}
    }
    
    public function feed_back_myFatoora($brand='')
    {
        $lang_id        = $this->data['lang_id'];
        $error_message  = '';
        $redirect_url   = base_url().'Orders_Log'; // General Redirect for failure casses , will be override in case of success

        if(isset($_GET['paymentId'])) {
            $payment_id = $_GET['paymentId']; // Sent Parameter through callback
            $result     = $this->myfatoora->getPaymentStatus($payment_id); // check statement of the payment transaction

            /*to do handle if result is empty or have no data*/
            $paymentGatewayResponse = json_decode($result, true);
            
            if(empty($paymentGatewayResponse))
            {
                // Didn't Receive Response - connection have error 
                $error_message      = "The Gateway response is empty , token key is not correct.";
                
                $this->data['msg']  = $error_message;
            }
            elseif(is_array($paymentGatewayResponse) && isset($paymentGatewayResponse['Data']) )
            {
                if($brand == 'myFatoora_visa'){
                    $payment_method_id  = '17'; 
                }
                elseif($brand == 'myFatoora_mada'){
                    $payment_method_id  = '20'; 
                }
                elseif($brand == 'myFatoora_apple_pay'){
                    $payment_method_id  = '19'; 
                }
                elseif($brand == 'myFatoora_apple_pay_mada'){
                    $payment_method_id  = '18'; 
                }
                
                $ip_address         = $this->input->ip_address();
                // $pay_id             = '';
                $status             = 'failure'; // Default status => failure
                //$user_id        = $this->_order_user_id($order_id);

                $transaction_id         = $paymentGatewayResponse['Data']['CustomerReference'];
                $pay_id                 = $paymentGatewayResponse['Data']['InvoiceId']; 
                $general_invoice_status = $paymentGatewayResponse['Data']['InvoiceStatus']; // "Paid" || "Pending" || "Canceled"
                $invoice_transactions   = $paymentGatewayResponse['Data']['InvoiceTransactions']; 

                // Get specific payment transaction object From InvoiceTransactions array whitch executed to InvoiceId
                $payment_transaction_object = array_search($payment_id, array_column($invoice_transactions, 'PaymentId'));

                // echo "<br /> Paymentgateways Controller : feed_back_myFatoora => payment_transaction_object <pre>";
                // print_r($payment_transaction_object);
                // echo "<br />";
                
                $order_id               = explode('_', $transaction_id)[0]; // remove unix time of transaction id
                $currency               = 'SAR';

                if($paymentGatewayResponse['IsSuccess'])
                {
                    if($general_invoice_status == 'Paid')
                    {
                        // The invoice will be "Paid" if there is a successful transaction (with "Succss" status).
                        // Payment Status Successf
                        $status                 = 'success';

                        // add log for the payment gate way response
                        $message            = $this->gateways_response->handleStatus($lang_id, $status, $payment_method_id , $order_id, $ip_address, $currency , $pay_id, $paymentGatewayResponse, 1);
            
                        // Create Order for the successfull payment from shopping cart with cart id
                        $this->load->library('cart_orders');
                        $order_data         = $this->cart_orders->create_order_from_cart($order_id);
                        $message            = $order_data['message'];//$this->gateways_response->handleStatus($lang_id, $status, $payment_method_id , $order_id, $ip_address, $currency , $pay_id, $paymentGatewayResponse, 1);
                        
                        $this->data['msg']  = $message;
                        $redirect_url       = base_url().'orders/order/view_order_details/'.$order_data['orderId'];
                    }
                    // elseif($general_invoice_status == 'Pending' || $general_invoice_status == 'Canceled'){
                    else {
                        // The invoice will be "Pending" if all transactions have "InProgress" or "Failed" status.
                        
                        // add log for the payment gate way response
                        $message            = $this->gateways_response->handleStatus($lang_id, $status, $payment_method_id , $order_id, $ip_address, $currency , $pay_id, $paymentGatewayResponse, 1);
                        
                        $error_message      = isset($payment_transaction_object['Error']) ? $payment_transaction_object['Error'] : $message;
                        $this->data['msg']  = $error_message;//$status;//lang('payment_failure_msg');
                    }   
                }
                else {
                    // Payment Status Failure

                    // add log for the payment gate way response
                    $message            = $this->gateways_response->handleStatus($lang_id, $status, $payment_method_id , $order_id, $ip_address, $currency , $pay_id, $paymentGatewayResponse, 1);

                    $this->data['msg'] = $message;//$status;//lang('payment_failure_msg');
                }
            }else {
                // Connection Fail , $result => is the error message in this case
                // Payment Status Failure
                if (isset($paymentGatewayResponse['Message'])) 
                {
                    // Invoice Failed to be Created - with error 
                    $error_message      = $paymentGatewayResponse['Message'];
                    $validation_errors  = $paymentGatewayResponse['ValidationErrors'];
                    foreach($validation_errors as $validation){
                        $error_message .= "\n".$validation['Name']." : ".$validation['Error'];
                    }
                }
                else{
                    // Unknown Error after connection
                    $error_message      = "Failed!, Please try again ..";
                }

                $this->data['msg']      = $error_message;
            }

        }else {
            // ERROR
			$error_message      = "Failed!, Please try again ..";
            $this->data['msg']      = $error_message;
		}

        // Pass Redirect to execute and show message
        $this->data['redirect_url'] = $redirect_url;
        $this->load->view('gateway_msg', $this->data);
    }

    private function _order_user_id($order_id)
    {
        $order_data = $this->orders_model->get_order_data($order_id);

        return $order_data->user_id;
    }

    private function _change_price_into_dollars($amount, $country_id)
    {
        $current_currency_data = $this->currency_model->get_country_currency_result($country_id);
        $dollar_currency_data  = $this->currency_model->get_currency_result(1);

        $price_in_dollars      = $this->user_bootstrap->convert_balance($current_currency_data->currency_value, $dollar_currency_data->currency_value, $amount);

        return $price_in_dollars;

        /*$this->user_bootstrap->convert_balance()
        $this->load->model('settings/currency_change_model');

        $dollar_value = $this->currency_change_model->get_dollar_value_in_country($country_id);

        $price_in_dollars = round(($amount / $dollar_value), 2);

        return $price_in_dollars;
        */

    }

    private function _change_price_into_riyal($amount, $country_id)
    {
        $current_currency_data  = $this->currency_model->get_country_currency_result($country_id);
        $new_currency_data      = $this->currency_model->get_currency_result(2); // Riyal

        $price_in_new_currency  = $this->user_bootstrap->convert_balance($current_currency_data->currency_value, $new_currency_data->currency_value, $amount);

        return $price_in_new_currency;
    }
    
    public function feedback_myFatoora($error = 0)
    {
        if($error)
        {
            echo 'payment failed';
            
        }
        else
        {
            echo 'success payment';
        }
        echo '<pre>'; print_r($_GET);
    }
    /*********************************************************/
}
/* End of file paypal_methods.php */
/* Location: ./application/modules/orders/controllers/paypal_methods.php */
?>
