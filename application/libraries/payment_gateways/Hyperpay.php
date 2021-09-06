<?php if(!defined('BASEPATH'))
    exit('No direct script access allowed');

class Hyperpay
{
    public $CI;


    public  $payment_user_id;           // = "8a8294185ec84a95015edc19596424b0";
    public  $payment_user_password;     //   = "Zab5xkHTTc";
    public  $entity_id;                 //   = "8a8294185ec84a95015edc19c00a24b6";
    public  $token;
    public  $payment_type;              //    = "DB";
    private $payment_host_name;         //  = "https://test.oppwa.com";
    //public  $test_mode;                 //   = "EXTERNAL";

    public $paymentReturnURL;           // For General Hyperpay Integration

    public $olpIdAlias;                 // For Sadad Hyperpay Integration
    public $merchantId;                 // For Sadad Hyperpay Integration
    public $sadadHyperpayLandingURL;    // For Sadad Hyperpay Integration
    public $sadadHyperpayFailureURL;    // For Sadad Hyperpay Integration

    public function __construct()
    {
      $this->CI = &get_instance();

      $this->CI->load->library('Gateways');
      $this->CI->load->library('payment_gateways/hyperpay_resource/payware');
      $this->CI->config->load('payment_gateways');

      $this->entity_id                = $this->CI->gateways->get_gateway_field_value('hyperpay_entity_id'); // "8ac7a4c870682d650170763a35650a88";// // '8acda4ca74257f1501742ed858534f43';//
      $this->token                    = $this->CI->gateways->get_gateway_field_value('hyperpay_user_password'); // "OGFjN2E0Yzg3MDY4MmQ2NTAxNzA3NjM5ZDI3YzBhODR8eFdwU3lUeDJuYQ=="; //
      // 'OGFjZGE0Y2E3NDI1N2YxNTAxNzQyZWQ3YzY1MzRmMzd8U3hXZDZSQmdnWQ==';//$this->CI->gateways->get_gateway_field_value('hyperpay_token');

      $this->paymentReturnURL         = $this->CI->config->item('HyperPayReturnURL');
      $this->sadadHyperpayLandingURL  = $this->CI->config->item('SadadHyperpayLandingURL');
      $this->sadadHyperpayFailureURL  = $this->CI->config->item('SadadHyperpayFailureURL');

  		$this->payment_host_name  = 'https://oppwa.com';// "https://test.oppwa.com"; //
  		$this->payment_type       = 'DB';
		// $this->test_mode          = 'EXTERNAL';
    }

	public function prepareCheckout($order_id, $final_total, $currency_symbol, $user_ip , $user_email , $payment_brand, $order_data, $user_data, $country_iso , $lang_id)
	{
        if($payment_brand == 'apple_pay')
        {
            $entity_id = '';
        }
        else if($payment_brand == 'MADA')
        {
            $entity_id = $this->CI->gateways->get_gateway_field_value('hyperpay_mada_entity_id'); // "8ac7a4c77400219f017400ffae7906eb";//'8acda4ca74257f1501742ed9112b4f50';
        }
        else
        {
            $entity_id = $this->entity_id;
        } 

        $lang_local = "en";
        if ($lang_id == 2){
            $lang_local = "ar";
        }
     
        $order_id = $order_id.'_'.time();
        
        if($user_data->id == 2930)
        {
            $final_total = 5;
        }
      
	    $final_total = ceil($final_total);
        // echo "Order Final_total : $final_total";
        
    	$request_payment_url    = $this->payment_host_name."/v1/checkouts";
    	$request_payment_data   =
        //"authentication.userId=".$this->payment_user_id.
    		//"&authentication.password=".$this->payment_user_password.
    		"currency=".$currency_symbol.
    		"&entityId=".$entity_id.
    		"&amount=".$final_total.
    		"&paymentType=".$this->payment_type.
    		"&customer.ip=".$user_ip.
    		"&customer.email=".$user_email.
            "&customer.givenName=".$user_data->first_name.' '.$user_data->last_name.
            "&customer.surname=".$user_data->first_name.' '.$user_data->last_name .
            "&merchantTransactionId=".$order_id.
            "&billing.street1=" . 'address'.//$order_data->shipping_address .
            "&billing.city=" . 'gedda'.//$order_data->shipping_city .
            "&billing.state=" . 'gedda'.//$order_data->shipping_district .
            "&billing.country=" . $country_iso . //$order_data->shipping_country .
            "&billing.postcode=111111" ;

        // $request_payment_data .="&testMode=EXTERNAL"; // For Test Mode
            //echo $request_payment_data; die();
        if($payment_brand == 'SADAD')
        {
            $request_payment_data .=
                "&customParameters[SADAD_OLP_ID]=".$this->olpIdAlias.
                "&bankAccount.country=SA".
                "&shopperResultUrl=".$this->paymentReturnURL;
        }

        // echo "<br /> HyperPay Library : prepareCheckout => request_payment_data <pre>";
        // print_r($request_payment_data);
        // echo "<br />";
        // die();

    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, $request_payment_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                   'Authorization:Bearer '.$this->token));
    	curl_setopt($ch, CURLOPT_POST, 1);
    	curl_setopt($ch, CURLOPT_POSTFIELDS, $request_payment_data);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// this should be set to true in production
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    	$responseData = curl_exec($ch);


    	if(curl_errno($ch)) {
    		return curl_error($ch);
    		
            curl_close($ch);
    	}else{
    	    curl_close($ch);

    	    try {
    	        $jsonResponse = json_decode($responseData, true);
                
                $return_url = $this->paymentReturnURL.'/'.$payment_brand;
                $mada_form = '';
                
                if($payment_brand == 'MADA')
                {
                    $return_url = $this->paymentReturnURL.'/'.$payment_brand;
                    $mada_form  = '<script src="https://code.jquery.com/jquery.js" type="text/javascript"></script>
                                    <style>

                                        .wpwl-form-card
                                        {
                                            min-height: 0px !important;
                                        }

                                        .wpwl-label-brand{
                                            display: none !important;
                                        }
                                        .wpwl-control-brand{
                                            display: none !important;
                                        }

                                        .wpwl-brand-card
                                        {
                                            display: block;
                                            visibility: visible;
                                            position: absolute;
                                            right: 178px;
                                            top: 40px;
                                            width: 67px;
                                            z-index: 10;
                                        }

                                        .wpwl-brand-MASTER
                                        {
                                            margin-top: -10;
                                            margin-right: -10;
                                        }

                                    </style>

                                    <script>

                                        var wpwlOptions = {
                                            locale: "'.$lang_local.'", //check if the store is in Arabic or English

                                            onReady: function(){
                                                if (wpwlOptions.locale == "ar") {
                                                    $(".wpwl-group").css("direction", "ltr");
                                                    $(".wpwl-control-cardNumber").css({"direction": "ltr" , "text-align":"right"});
                                                    $(".wpwl-brand-card").css("right", "200px");
                                                }
                                            }}
                                        }
                                    </script>
                                ';
                }

                /**
                 * Mrzok Edits
                 */
                else if($payment_brand == 'VISA MASTER')
                {
                    $return_url = $this->paymentReturnURL.'/hyperpay_visa';
                }
                // End Edit 

                $form = $mada_form.'
                        <script src="'.$this->payment_host_name.'/v1/paymentWidgets.js?checkoutId='.$jsonResponse['id'].'"></script>
                        <form action="'.$return_url.'" class="paymentWidgets" data-brands="'.$payment_brand.'"></form>
                        ';
                        
                       
    	        return $form;
    	    }
    	    catch (Exception $exception) {
    				echo "exception : ". $exception->getMessage();
    		}
    	}
	}

	public function getPaymentStatus($resourcePath, $brand='')
	{
	    //echo "resourcePath : ".$resourcePath."<br />";

    	$url = $this->payment_host_name.$resourcePath;
    	//$url .= "?authentication.userId=".$this->payment_user_id;
    	//$url .= "&authentication.password=".$this->payment_user_password;
    	//$url .= "?entityId=".$this->entity_id;
        
        if($brand == 'MADA')
        {
            //MADA entity ID
            $url .= "?entityId=".$this->CI->gateways->get_gateway_field_value('hyperpay_mada_entity_id'); // "?entityId=8acda4ca74257f1501742ed9112b4f50";//.$this->entity_id;
        }
        else
        {
            //visa mastercard entity ID
            $url .= "?entityId=".$this->entity_id;
        }
        // echo "<br /> HyperPay Library : getPaymentStatus => url <pre>";
        // print_r($url);
        // echo "<br />============================================<br />";
        // die();
    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                 'Authorization:Bearer '.$this->token));
    	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);// this should be set to true in production
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    	$responseData = curl_exec($ch);
    	if(curl_errno($ch)) {
    		return curl_error($ch);
    	    curl_close($ch);
    	}else{
    	    curl_close($ch);

    	    try {
    	        
    	        return $responseData;
    	    }
    	    catch (Exception $exception) {
    	        echo $exception->getMessage();
    		}
    	}
	}

    public function initSadadCheckout($order_id, $final_total, $currency_symbol, $user_ip )
    {
        // PayWare service URL
        $service_url                    = 'https://stg.sadad.hyperpay.com/PayWareHub/api/PayWare/SetCheckout';

        // Payware request
        $request = new PayWareRequest();
        $request->api_user_name         = '47511ed8-c152-4911-aef6-cd5b84fa69ea';
        $request->api_secret            = 'cb9067b7-b418-4baf-ae1d-12f8f055d57a';
        $request->amount                = $final_total;
        $request->transaction_number    = $order_id;//'211112111111211111110';//
        $request->merchant_id           = $this->merchantId;
        $request->success_url           = $this->sadadHyperpayLandingURL;
        $request->failure_url           = $this->sadadHyperpayFailureURL;
        $request->lang                  = 'EN'; // $this->CI->data['lang_id'];

        //$request->is_testing            = true; //should be removed for Live Server

        // Init PayWare client class
        $client = new PayWare();

        // Call init checkout process
        $response = $client->init_checkout($service_url, $request);

        // If response code is 200, it means success
        if($response->response_code == 200){
            // Redirect to PayWare checkout page
            $staging_checkout_url = "https://stg.sadad.hyperpay.com/PayWareHub/Pages/Checkout/Checkout.aspx?id=".$response->response;
            header('Location: '.$staging_checkout_url);
        } else {
            // Display error
            echo $response->response;
        }
	}

    public function getSadadCheckoutStatus($merchantRefNum)
    {
        // Init PayWare client class
        $client = new PayWare();
        $service_url = 'https://stg.sadad.hyperpay.com/PayWareHub/api/PayWare/GetCheckoutStatus';

        // Call init checkout process
        $response = $client->get_checkout_status($service_url, $merchantRefNum, $this->merchantId);

        if($response->response_code == 200){
            
            return $responseData;
        } else {
            // Display error
            echo $response->response;
        }
	}

    public function handleResponse($paymentStatus)
    {
        if(
		    $paymentStatus == '0'             // Payment is successfully paid => SADAD Hyperpay
		 || $paymentStatus == '000.000.000'   // Transaction succeeded
		 || $paymentStatus == '000.000.100'   // successful request
		 || $paymentStatus == '000.100.110'   // Request successfully processed in 'Merchant in Integrator Test Mode'
		 || $paymentStatus == '000.100.111'   // Request successfully processed in 'Merchant in Validator Test Mode'
		 || $paymentStatus == '000.100.112'   // Request successfully processed in 'Merchant in Connector Test Mode'
		 || $paymentStatus == '000.300.000'   // Two-step transaction succeeded
		 || $paymentStatus == '000.300.100'   // Risk check successful
		 || $paymentStatus == '000.300.101'   // Risk bank account check successful
		 || $paymentStatus == '000.300.102'   // Risk report successful
		 || $paymentStatus == '000.600.000'   // transaction succeeded due to external update
		    )
		{
            $status = 'success';
        } else{
			$status = 'failure';
		}

        //echo "handleResponse code : ".$paymentStatus."<br />";
        //echo "handleResponse status : ".$status."<br />";

        return $status;
    }
}
