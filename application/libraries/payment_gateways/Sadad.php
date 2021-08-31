<?php if(!defined('BASEPATH'))
    exit('No direct script access allowed');

class Sadad
{
    public $CI;
    public $olpIdAlias;
    public $merchantId;
    public $dynamicMerchantLandingURL;
    public $dynamicMerchantFailureURL;
    
    public function __construct()
    {
        $this->CI = &get_instance();
        
        $this->CI->load->library('Gateways');
        $this->CI->config->load('payment_gateways');   
        
        $this->olpIdAlias = $this->CI->gateways->get_gateway_field_value('sadad_olpIdAlias');
        $this->merchantId = $this->CI->gateways->get_gateway_field_value('sadad_merchantId');
        
        $this->dynamicMerchantLandingURL = $this->CI->config->item('dynamicMerchantLandingURL');
        $this->dynamicMerchantFailureURL = $this->CI->config->item('dynamicMerchantFailureURL');
    }
    
    public function INITIATE_PAYMENT($order_id, $order_total, $currency_code)
    {
        $params = array(
                        'olpIdAlias'        => $this->olpIdAlias,
                        'merchantId'        => $this->merchantId,
                        'merchantRefNum'    => $order_id,  //The reference number of the merchant for that particular transaction
                        'paymentAmount'     => $order_total, //Payment amount for the transaction
                        'paymentCurrency'   => $currency_code,  //Payment Currency Code
                        'dynamicMerchantLandingURL' => $this->dynamicMerchantLandingURL,
                        'dynamicMerchantFailureURL' => $this->dynamicMerchantFailureURL
                        );
        
        $method = 'initiatePaymentDetailsReq';
        
        $result = $this->CallAPI($params, $method);
        echo '<pre>'; print_r($result);die();
    }
    
    public function CONFIRM_PAYMENT($transaction_id, $order_id)
    {
        $params = array(
                        'transactionId'  => $transaction_id,
                        'merchantId'     => $this->merchantId,
                        'merchantRefNum' => $order_id,  //The reference number of the merchant for that particular transaction
                        
                        );
        
        $method = 'confirmPaymentDetailsReq';
        
        $result = $this->CallAPI($params, $method);
        echo '<pre>'; print_r($result);die();
    }
    
    public function CallAPI($params, $method)
    {
		$context = stream_context_create([
		  'ssl' => [
		   'local_cert' => '/home/tamrco/public_html/newfile.crt.pem',
		   'local_pk'   => '/home/tamrco/public_html/newfile.key.pem',
		  ]]);
		  
        $client = new SoapClient('https://b2brbtest.riyadbank.com/soap?service=RB_OLP_INITIATE_PAYMENT', 
                                    array(
                                            'trace' => true,
											//'context' => $context,
                                            'local_cert' => '/home/tamrco/public_html/crtpk.pem',
                                            'passphrase' => 'b2b123',
                                            'verifypeer' => false,
                                            'verifyhost' => false 
                                          )
                                )
                                ;
        $result = $client->$method($params);
        
        $result_object = $method.'Result';
        
        if(isset($result->$result_object->any))
        {
            $response = $this->parseResponse($result->$result_object->any);
        }
        else
        {
            $response = $result;
        }
        
        return $response;
        
    }
    
    public function parseResponse($result)
    {
       $your_xml_response = $result;
       $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $your_xml_response);
       $xml = simplexml_load_string($clean_xml);
        
       return $xml->NewDataSet;
    }
}