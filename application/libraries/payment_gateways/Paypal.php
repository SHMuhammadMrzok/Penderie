<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Paypal
{
    public $CI, $PayPalMode, $PayPalApiUsername, 
           $PayPalApiPassword, $PayPalApiSignature, 
           $PayPalCurrencyCode, $PayPalReturnURL,
           $PayPalCancelURL, $PayPalLogoImg;
    
    public function __construct()
    {   
        $this->CI = &get_instance();
        $this->fetch_paypal_config();
    }
    
    private function fetch_paypal_config()
    {
        $this->CI->config->load('payment_gateways');
        
        $this->PayPalMode           = $this->CI->config->item('PayPalMode');
        $this->PayPalApiUsername    = $this->CI->config->item('PayPalApiUsername');
        $this->PayPalApiPassword    = $this->CI->config->item('PayPalApiPassword');
        $this->PayPalApiSignature   = $this->CI->config->item('PayPalApiSignature');
        $this->PayPalCurrencyCode   = $this->CI->config->item('PayPalCurrencyCode');
        $this->PayPalReturnURL      = $this->CI->config->item('PayPalReturnURL');
        $this->PayPalCancelURL      = $this->CI->config->item('PayPalCancelURL');
        $this->PayPalLogoImg        = $this->CI->config->item('PayPalLogoImg');
    }
    
    public function execute_send_request($items = array(), $other_costs = array(), $INVNUM = '', $currency, $NOSHIPPING = 0, $NOTETOBUYER = '', $shipping_data = array())
    {
        //Mainly we need 4 variables from product page Item Name, Item Price, Item Number and Item Quantity.
    
    	$ItemsNameArray 	= $items["itemname"]; //Item Name
    	$ItemsPriceArray 	= $items["itemprice"]; //Item Price
    	$ItemsDescArray 	= $items["itemdesc"]; //Item Number
    	$ItemsQtyArray 		= $items["itemQty"]; // Item Quantity
        
        $ItemTotalPrice     = 0;
        $payment_data_array = array();
        
        foreach($ItemsPriceArray as $key=>$ItemPrice)
        {
            $ItemTotalPrice += $ItemPrice * $ItemsQtyArray[$key];
            
            $payment_data_array['L_PAYMENTREQUEST_0_NAME'   . $key] = urlencode($ItemsNameArray[$key]);
            $payment_data_array['L_PAYMENTREQUEST_0_NUMBER' . $key] = ($key +1);
            $payment_data_array['L_PAYMENTREQUEST_0_DESC'   . $key] = urlencode($ItemsDescArray[$key]);
            $payment_data_array['L_PAYMENTREQUEST_0_AMT'    . $key] = urlencode($ItemPrice);
            $payment_data_array['L_PAYMENTREQUEST_0_QTY'    . $key] = urlencode($ItemsQtyArray[$key]);
        }
        
        $ItemTotalPrice  = $this->_price_approximation($ItemTotalPrice);
        
        $payment_data_array['PAYMENTREQUEST_0_ITEMAMT']  = urlencode($ItemTotalPrice);
	
    	//Other important variables like tax, shipping cost
        
        $TotalTaxAmount     = 0; //Sum of tax for all items in this order.
        $HandalingCost 		= 0; //Handling cost for this order.
    	$InsuranceCost 		= 0; //shipping insurance cost for this order.
    	$ShippinDiscount 	= 0; //Shipping discount for this order. Specify this as negative number.
    	$ShippinCost 		= 0; //Although you may change the value later, try to pass in a shipping amount that is reasonably accurate.
        
        if(isset($other_costs['TotalTaxAmount']) && $other_costs['TotalTaxAmount'] > 0) 
        {
    	   $TotalTaxAmount  = $this->_price_approximation($other_costs['TotalTaxAmount']);  //Sum of tax for all items in this order.
        }
        
        if(isset($other_costs['HandalingCost']) && $other_costs['HandalingCost'] > 0) 
        {
    	   $HandalingCost   = $this->_price_approximation($other_costs['HandalingCost']);  //Handling cost for this order.
        }
        
        if(isset($other_costs['InsuranceCost']) && $other_costs['InsuranceCost'] > 0) 
        {
    	   $InsuranceCost   = $this->_price_approximation($other_costs['InsuranceCost']);  //shipping insurance cost for this order.
        }
        
        if(isset($other_costs['ShippinDiscount']) && $other_costs['ShippinDiscount'] > 0) 
        {
    	   $ShippinDiscount = $this->_price_approximation($other_costs['ShippinDiscount']); //Shipping discount for this order. Specify this as negative number.
        }
        
        if(isset($other_costs['ShippinCost']) && $other_costs['ShippinCost'] > 0) 
        {
    	   $ShippinCost     = $this->_price_approximation($other_costs['ShippinCost']); //Although you may change the value later, try to pass in a shipping amount that is reasonably accurate.
        }
    	
    	//Grand total including all tax, insurance, shipping cost and discount
    	$GrandTotal = $this->_price_approximation($ItemTotalPrice + $TotalTaxAmount + $HandalingCost + $InsuranceCost + $ShippinCost + $ShippinDiscount);
        
        $payment_data_array['PAYMENTREQUEST_0_TAXAMT']         = urlencode($TotalTaxAmount);
        $payment_data_array['PAYMENTREQUEST_0_SHIPPINGAMT']    = urlencode($ShippinCost);
        $payment_data_array['PAYMENTREQUEST_0_HANDLINGAMT']    = urlencode($HandalingCost);
        $payment_data_array['PAYMENTREQUEST_0_SHIPDISCAMT']    = urlencode($ShippinDiscount);
        $payment_data_array['PAYMENTREQUEST_0_INSURANCEAMT']   = urlencode($InsuranceCost);
        $payment_data_array['PAYMENTREQUEST_0_AMT']            = urlencode($GrandTotal);
        $payment_data_array['PAYMENTREQUEST_0_CURRENCYCODE']   = urlencode(strtoupper($currency));
        
        ############# set session variable we need later for "DoExpressCheckoutPayment" #######
        $_SESSION['ResponseData'] = json_encode($payment_data_array);
        
        //Parameters for SetExpressCheckout, which will be sent to PayPal
        $payment_data_array['METHOD']                          = 'SetExpressCheckout';
        $payment_data_array['RETURNURL']                       = $this->PayPalReturnURL ;
        $payment_data_array['CANCELURL']                       = $this->PayPalCancelURL;
        $payment_data_array['PAYMENTREQUEST_0_PAYMENTACTION']  = urlencode("SALE");
        $payment_data_array['PAYMENTREQUEST_0_INVNUM']         = $INVNUM;
        $payment_data_array['NOTETOBUYER']                     = $NOTETOBUYER;
        
        $payment_data_array['NOSHIPPING']                      = $NOSHIPPING;
        $payment_data_array['LOCALECODE']                      = 'GB';
        $payment_data_array['LOGOIMG']                         = $this->PayPalLogoImg;
        $payment_data_array['CARTBORDERCOLOR']                 = 'FFFFFF';
        $payment_data_array['ALLOWNOTE']                       = '1';
        
        if(count($shipping_data) > 0)
        {
            //Override the buyer's shipping address stored on PayPal, The buyer cannot edit the overridden address.
            $payment_data_array['ADDROVERRIDE']                        = '1';
            $payment_data_array['PAYMENTREQUEST_0_SHIPTONAME']         = $shipping_data['Name'];
            $payment_data_array['PAYMENTREQUEST_0_SHIPTOSTREET']       = $shipping_data['Street'];
            $payment_data_array['PAYMENTREQUEST_0_SHIPTOCITY']         = $shipping_data['City'];
            $payment_data_array['PAYMENTREQUEST_0_SHIPTOSTATE']        = $shipping_data['State'];
            $payment_data_array['PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE']  = $shipping_data['CountryCode'];
            $payment_data_array['PAYMENTREQUEST_0_SHIPTOZIP']          = $shipping_data['Zip'];
            $payment_data_array['PAYMENTREQUEST_0_SHIPTOPHONENUM']     = $shipping_data['PhoneNum'];
        }
    	
        $padata = http_build_query($payment_data_array,'','&');

		//We need to execute the "SetExpressCheckOut" method to obtain paypal token
		$httpParsedResponseAr = $this->PPHttpPost('SetExpressCheckout', $padata, $this->PayPalApiUsername, $this->PayPalApiPassword, $this->PayPalApiSignature, $this->PayPalMode);
		
		//Respond according to message we receive from Paypal
		if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"]))
		{
            $paypalmode = ($this->PayPalMode=='sandbox') ? '.sandbox' : '';
            
            //Redirect user to PayPal store with Token received.
            $paypalurl ='https://www'.$paypalmode.'.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token='.$httpParsedResponseAr["TOKEN"].'';
			header('Location: '.$paypalurl);
			 
		}else{
			//Show error message
			echo '<div style="color:red"><b>Error : </b>'.urldecode($httpParsedResponseAr["L_LONGMESSAGE0"]).'</div>';
			/*echo '<pre>';
			print_r($httpParsedResponseAr);
			echo '</pre>';*/
		}
    }
    
    public function get_result($token, $PayerID)
    {
        //we will be using these two variables to execute the "DoExpressCheckoutPayment"
    	//Note: we haven't received any payment yet.
    	
    	//get session variables
    	$sess_data_array = json_decode($_SESSION['ResponseData']);
        
        //print_r($sess_data_array);
        
        $result_data_array = array(
            'TOKEN'                             => urlencode($token),
            'PAYERID'                           => urlencode($PayerID),
            'PAYMENTREQUEST_0_PAYMENTACTION'    => urlencode("SALE")
        );
    
        $payment_data_array = array_merge($result_data_array, (array)$sess_data_array);
    				
    	$padata = http_build_query($payment_data_array,'','&');
        	
    	//We need to execute the "DoExpressCheckoutPayment" at this point to Receive payment from user.

    	$httpParsedResponseAr = $this->PPHttpPost('DoExpressCheckoutPayment', $padata, $this->PayPalApiUsername, $this->PayPalApiPassword, $this->PayPalApiSignature, $this->PayPalMode);
    	
    	//Check if everything went ok..
    	if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) 
    	{
            /*
    		echo '<h2>Success</h2>';
    		echo 'Your Transaction ID : '.urldecode($httpParsedResponseAr["PAYMENTINFO_0_TRANSACTIONID"]);
    		*/
            
    		/*
    		//Sometimes Payment are kept pending even when transaction is complete. 
    		//hence we need to notify user about it and ask him manually approve the transiction
    		*/
    		
            
            /*
    		if('Completed' == $httpParsedResponseAr["PAYMENTINFO_0_PAYMENTSTATUS"])
    		{
    			echo '<div style="color:green">Payment Received! Your product will be sent to you very soon!</div>';
    		}
    		elseif('Pending' == $httpParsedResponseAr["PAYMENTINFO_0_PAYMENTSTATUS"])
    		{
    			echo '<div style="color:red">Transaction Complete, but payment is still pending! '.
    			'You need to manually authorize this payment in your <a target="_new" href="http://www.paypal.com">Paypal Account</a></div>';
    		}
            
            */
    
    		// we can retrive transection details using either GetTransactionDetails or GetExpressCheckoutDetails
    		// GetTransactionDetails requires a Transaction ID, and GetExpressCheckoutDetails requires Token returned by SetExpressCheckOut
    		$padata = 	'&TOKEN='.urlencode($token);
    		
    		$httpParsedResponseArAfter = $this->PPHttpPost('GetExpressCheckoutDetails', $padata, $this->PayPalApiUsername, $this->PayPalApiPassword, $this->PayPalApiSignature, $this->PayPalMode);
            
            
            /*
            if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) 
    		{
    			return $httpParsedResponseAr;
    		}
            else  
            {
    			echo '<div style="color:red"><b>GetTransactionDetails failed:</b>'.urldecode($httpParsedResponseAr["L_LONGMESSAGE0"]).'</div>';
    			/*echo '<pre>';
    			print_r($httpParsedResponseAr);
    			echo '</pre>';
    
    		}*/
    	
    	}/*else{
    			echo '<div style="color:red"><b>Error : </b>'.urldecode($httpParsedResponseAr["L_LONGMESSAGE0"]).'</div>';
    			/*echo '<pre>';
    			print_r($httpParsedResponseAr);
    			echo '</pre>';
    	}*/
        
        $httpParsedResponseArAfter['PAYMENTSTATUS'] = $httpParsedResponseAr['PAYMENTINFO_0_PAYMENTSTATUS'];
        
        return $httpParsedResponseArAfter;
        /**
        Array ( 
         [TOKEN] => EC%2d1UP16149LD452381Y 
         [BILLINGAGREEMENTACCEPTEDSTATUS] => 0 
         [CHECKOUTSTATUS] => PaymentActionCompleted 
         [TIMESTAMP] => 2016%2d05%2d30T15%3a15%3a37Z 
         [CORRELATIONID] => 93944393cdf5b 
         [ACK] => Success 
         [VERSION] => 109%2e0 
         [BUILD] => 000000 
         [EMAIL] => mariam%40shourasoft%2ecom 
         [PAYERID] => FQFRU48LHH3QU 
         [PAYERSTATUS] => unverified 
         [FIRSTNAME] => Mariam 
         [LASTNAME] => Abo%20Elsoud 
         [COUNTRYCODE] => US 
         [SHIPTONAME] => Mariam%20Abo%20Elsoud 
         [SHIPTOSTREET] => Gamal%20Abd%20Elnasr 
         [SHIPTOCITY] => Shbeen%20El%20Koom 
         [SHIPTOSTATE] => AA 
         [SHIPTOZIP] => 1234 
         [SHIPTOCOUNTRYCODE] => US 
         [SHIPTOCOUNTRYNAME] => United%20States 
         [ADDRESSSTATUS] => Confirmed 
         [CURRENCYCODE] => USD [AMT] => 56%2e00 
         [ITEMAMT] => 50%2e00 
         [SHIPPINGAMT] => 0%2e00 
         [HANDLINGAMT] => 0%2e00 
         [TAXAMT] => 6%2e00 [INVNUM] => 691 
         [INSURANCEAMT] => 0%2e00 
         [SHIPDISCAMT] => 0%2e00 
         [TRANSACTIONID] => 8FV95924C1303483V 
         [INSURANCEOPTIONOFFERED] => false 
         [L_NAME0] => recharge%2bcard 
         [L_NUMBER0] => 1 [L_QTY0] => 1 
         [L_TAXAMT0] => 0%2e00 
         [L_AMT0] => 50%2e00 
         [L_DESC0] => recharge%2bcard 
         [PAYMENTREQUEST_0_CURRENCYCODE] => USD 
         [PAYMENTREQUEST_0_AMT] => 56%2e00 
         [PAYMENTREQUEST_0_ITEMAMT] => 50%2e00 
         [PAYMENTREQUEST_0_SHIPPINGAMT] => 0%2e00 
         [PAYMENTREQUEST_0_HANDLINGAMT] => 0%2e00 
         [PAYMENTREQUEST_0_TAXAMT] => 6%2e00 
         [PAYMENTREQUEST_0_INVNUM] => 691 
         [PAYMENTREQUEST_0_INSURANCEAMT] => 0%2e00 
         [PAYMENTREQUEST_0_SHIPDISCAMT] => 0%2e00 
         [PAYMENTREQUEST_0_TRANSACTIONID] => 8FV95924C1303483V 
         [PAYMENTREQUEST_0_SELLERPAYPALACCOUNTID] => islamsharaf%2dfacilitator%40shourasoft%2ecom 
         [PAYMENTREQUEST_0_INSURANCEOPTIONOFFERED] => false 
         [PAYMENTREQUEST_0_SOFTDESCRIPTOR] => PAYPAL%20%2aTESTFACILIT 
         [PAYMENTREQUEST_0_SHIPTONAME] => Mariam%20Abo%20Elsoud 
         [PAYMENTREQUEST_0_SHIPTOSTREET] => Gamal%20Abd%20Elnasr 
         [PAYMENTREQUEST_0_SHIPTOCITY] => Shbeen%20El%20Koom 
         [PAYMENTREQUEST_0_SHIPTOSTATE] => AA 
         [PAYMENTREQUEST_0_SHIPTOZIP] => 1234 
         [PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE] => US 
         [PAYMENTREQUEST_0_SHIPTOCOUNTRYNAME] => United%20States 
         [PAYMENTREQUEST_0_ADDRESSSTATUS] => Confirmed 
         [L_PAYMENTREQUEST_0_NAME0] => recharge%2bcard 
         [L_PAYMENTREQUEST_0_NUMBER0] => 1 
         [L_PAYMENTREQUEST_0_QTY0] => 1 
         [L_PAYMENTREQUEST_0_TAXAMT0] => 0%2e00 
         [L_PAYMENTREQUEST_0_AMT0] => 50%2e00 
         [L_PAYMENTREQUEST_0_DESC0] => recharge%2bcard 
         [PAYMENTREQUESTINFO_0_TRANSACTIONID] => 8FV95924C1303483V 
         [PAYMENTREQUESTINFO_0_ERRORCODE] => 0 
         [PAYMENTSTATUS] => Completed )
        
        **/
    }
    
    private function PPHttpPost($methodName_, $nvpStr_, $PayPalApiUsername, $PayPalApiPassword, $PayPalApiSignature, $PayPalMode)
    {
			// Set up your API credentials, PayPal end point, and API version.
			$API_UserName = urlencode($PayPalApiUsername);
			$API_Password = urlencode($PayPalApiPassword);
			$API_Signature = urlencode($PayPalApiSignature);
			
			$paypalmode = ($PayPalMode=='sandbox') ? '.sandbox' : '';
	
			$API_Endpoint = "https://api-3t".$paypalmode.".paypal.com/nvp";
			$version = urlencode('109.0');
		
			// Set the curl parameters.
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
			curl_setopt($ch, CURLOPT_VERBOSE, 1);
		
			// Turn off the server and peer verification (TrustManager Concept).
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
		
			// Set the API operation, version, and API signature in the request.
			$nvpreq = "METHOD=$methodName_&VERSION=$version&PWD=$API_Password&USER=$API_UserName&SIGNATURE=$API_Signature&$nvpStr_";
		
			// Set the request as a POST FIELD for curl.
			curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);
		
			// Get response from the server.
			$httpResponse = curl_exec($ch);
		
			if(!$httpResponse) {
				exit("$methodName_ failed: ".curl_error($ch).'('.curl_errno($ch).')');
			}
		
			// Extract the response details.
			$httpResponseAr = explode("&", $httpResponse);
		
			$httpParsedResponseAr = array();
			foreach ($httpResponseAr as $i => $value) {
				$tmpAr = explode("=", $value);
				if(sizeof($tmpAr) > 1) {
					$httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
				}
			}
		
			if((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
				exit("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
			}
		
		return $httpParsedResponseAr;
	}
    
    private function _price_approximation($price)
    {
        return round($price, 2);
    }
    
    public function handleResponse($result)
    {
        if(in_array(trim($result['ACK']), array('Success', 'SuccessWithWarning')))
        {
            if($result["PAYMENTSTATUS"] == 'Pending')
            {
                $status = 'pending';
            }
            elseif($result["PAYMENTSTATUS"] == 'Completed')
            {
                $status = 'success';
            }
        }
        elseif(in_array(trim($result['ACK']), array('Failure', 'FailureWithWarning')))
        {
            $status = 'failure';
        }
        else
        {
            $status = 'invalid';
        }
        
        return $status;
    }
}