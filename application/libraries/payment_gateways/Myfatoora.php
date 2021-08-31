<?php if(!defined('BASEPATH'))
    exit('No direct script access allowed');

class Myfatoora
{
  public $CI;
  public $api_url;
  public $api_token_key;
  public $integration_mode;
  
  
  public function __construct() // $order_id
  {
    $this->CI = &get_instance();

    $this->CI->load->library('Gateways');
    // $this->CI->load->library('payment_gateways/myfatoora_resource/myfatoorahApiV2');
    // $this->CI->load->library('payment_gateways/myfatoora_resource/paymentMyfatoorahApiV2');
    // $this->CI->load->library('payment_gateways/myfatoora_resource/shippingMyfatoorahApiV2');
    $this->CI->config->load('payment_gateways');
    
    
    $this->integration_mode = $this->CI->gateways->get_gateway_field_value('myfatoora_test_mode'); // '1'; // 1 => Test Mode , 0 => Live Mode
    $this->api_token_key    = trim($this->CI->gateways->get_gateway_field_value('myfatoora_token_key')); // trim($this->generate_token());//
    $this->api_url          = ($this->integration_mode) ? 'https://apitest.myfatoorah.com' : 'https://api.myfatoorah.com';
    
    // $this->prepareCheckoutFatoora($order_id);
  }
  
  public function prepareCheckout($cart_id, $order_total, $user_fullname, $user_email, $user_phone, $country_code, $gateway_payment_method_id, $gateway_currency, $language, $brand_type)
  {
    /**
     * Integration With MyFatoora v2
     * Test Account =>
     * Test API URL: https://apitest.myfatoorah.com/v2/
     * Test API Key: It should be added to the authorization header of the API endpoint request preceded by the bearer word.
     * Test API Token Key : rLtt6JWvbUHDDhsZnfpAhpYk4dxYDQkbcPTyGaKp2TYqQgG7FGZ5Th_WD53Oq8Ebz6A53njUoo1w3pjU1D4vs_ZMqFiz_j0urb_BH9Oq9VZoKFoJEDAbRZepGcQanImyYrry7Kt6MnMdgfG5jn4HngWoRdKduNNyP4kzcp3mRv7x00ahkm9LAK7ZRieg7k1PDAnBIOG3EyVSJ5kK4WLMvYr7sCwHbHcu4A5WwelxYK0GMJy37bNAarSJDFQsJ2ZvJjvMDmfWwDVFEVe_5tOomfVNt6bOg9mexbGjMrnHBnKnZR1vQbBtQieDlQepzTZMuQrSuKn-t5XZM7V6fCW7oP-uXGX-sMOajeX65JOf6XVpk29DP6ro8WTAflCDANC193yof8-f5_EYY-3hXhJj7RBXmizDpneEQDSaSz5sFk0sV5qPcARJ9zGG73vuGFyenjPPmtDtXtpx35A-BVcOSBYVIWe9kndG3nclfefjKEuZ3m4jL9Gg1h2JBvmXSMYiZtp9MR5I6pvbvylU_PP5xJFSjVTIz7IQSjcVGO41npnwIxRXNRxFOdIUHn0tjQ-7LwvEcTXyPsHXcMD8WtgBh-wxR8aKX7WPSsT1O8d8reb2aR7K3rkV3K82K_0OgawImEpwSvp9MNKynEAJQS6ZHe_J_l77652xwPNxMRTMASk1ZsJL
     * Live Account =>
     * Live API URL: https://api.myfatoorah.com/v2/
     * Live API Key: It should be added to the authorization header of the API endpoint request preceded by the bearer word.
     */

    $transaction_id = $cart_id.'_'.time();

    // Data which will be sent through CURL , it will be converted to json before being sent
    $post_array  = array(
                            'PaymentMethodId'     => $gateway_payment_method_id   , // VISA/MASTER =>2 | Mada => 6 | Apple Pay => 11 | Apple Pay (Mada) => 13
                            'CustomerName'        => $user_fullname               ,
                            'DisplayCurrencyIso'  => $gateway_currency            , //"SAR"  ,
                            'MobileCountryCode'   => $country_code                , //"+966"  ,
                            'CustomerMobile'      => $user_phone                  ,
                            'CustomerEmail'       => $user_email                  ,
                            'InvoiceValue'        => $order_total                 ,
                            'CallBackUrl'         => base_url()."orders/payment_gateways/feed_back_myFatoora/$brand_type" ,
                            'ErrorUrl'            => base_url()."orders/payment_gateways/feed_back_myFatoora/$brand_type" ,// /1
                            'Language'            => $language                    , // "EN" to display the checkout page in English & "AR" to display the checkout page in Arabic
                            'CustomerReference'   => $transaction_id              , // Refers to the order or transaction ID in your own system and you can use for payment inquiry as well
                            /* **
                            *'CustomerCivilId'     => ""  ,
                            *'UserDefinedField'    => ""  ,
                            *'ExpireDate'          => ""  ,
                            *'CustomerAddress'     => array(
                            *                                'Block'               => 'demo block' ,
                            *                                'Street'              => 'demo street ' ,
                            *                                'HouseBuildingNo'     => 'demo HouseBuildingNo' ,
                            *                                'Address'             => 'demo Address' ,
                            *                                'AddressInstructions' => 'demo AddressInstructions'
                            *                              )  ,
                            *'InvoiceItems'        => array(
                            *                                array(
                            *                                  'ItemName'            => 'giftsweet' ,
                            *                                  'Quantity'            => '1' ,
                            *                                  'UnitPrice'           => '1' 
                            *                                )
                            *                              )   
                            */                
                          );

    // Set Api End Point To ExecutePayment , Parameters to be sent
    $api_end_point  = $this->api_url.'/v2/ExecutePayment';
    $stringToPost   = json_encode($post_array);

    // Init Curl Connection 
    $response       = $this->curlPostRequest($api_end_point, $stringToPost);
    $response_data  = json_decode($response,true);
    if(isset($response_data['IsSuccess']) && $response_data['IsSuccess']) 
    {
      // Invoice Created Successfully 
      $invoiceURL     = $response_data['Data']['PaymentURL']; // $response->Data->PaymentURL; 
      $invoiceId      = $response_data['Data']['InvoiceId']; // $response->Data->InvoiceId;  

      redirect($invoiceURL, 'refresh');
    }
    else{
      if (isset($response_data['Message'])) {
        // Invoice Failed to be Created - with error 
        $error_message      = $response_data['Message'];
        $validation_errors  = $response_data['ValidationErrors'];
        foreach($validation_errors as $validation){
          $error_message .= "\n".$validation['Name']." : ".$validation['Error'];
        }
      }
      else{
        // Didn't Receive Response - connection have error 
        $error_message      = "the response is empty when the token key is not correct.";
      }

      echo $error_message;
      return false;
    }
  }

  public function getPaymentStatus($paymentId)
  {
    
    // Data which will be sent through CURL , it will be converted to json in MyfatoorahApiV2 Liberary before being sent
    $post_array  = array(
                            'KeyType'   => 'PaymentId'   , // PaymentId || InvoiceId
                            'Key'       => "$paymentId"  
                          );

    // Set Api End Point To ExecutePayment , Parameters to be sent
    $api_end_point  = $this->api_url.'/v2/getPaymentStatus';
    $stringToPost   = json_encode($post_array);

    // Init Curl Connection 
    $response       = $this->curlPostRequest($api_end_point, $stringToPost);
    
    return $response;
  }

  public function getAvailablePaymentMethods($order_total, $gateway_currency)
  {
    
    // Data which will be sent through CURL , it will be converted to json in MyfatoorahApiV2 Liberary before being sent
    $post_array  = array(
                            'KeyType'   => $order_total   , // PaymentId || InvoiceId
                            'Key'       => $gateway_currency  
                          );

    // Set Api End Point To InitiatePayment , Parameters to be sent
    $api_end_point  = $this->api_url.'/v2/InitiatePayment';
    $stringToPost   = json_encode($post_array);

    // Init Curl Connection 
    $response       = $this->curlPostRequest($api_end_point, $stringToPost);
    
    return $response;
  }

  public function curlPostRequest($url, $stringToPost)
  {
    $headers = array("Authorization: Bearer $this->api_token_key","Content-Type: application/json");
    $channal = curl_init();
    curl_setopt($channal, CURLOPT_URL, $url);
    curl_setopt($channal, CURLOPT_POST, true);
    curl_setopt($channal, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($channal, CURLOPT_POSTFIELDS, $stringToPost);
    curl_setopt($channal, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($channal, CURLOPT_HTTPHEADER, $headers);
    // curl_setopt($channal, CURLOPT_TIMEOUT, 5);


    $result = curl_exec($channal);
    
    // echo "<br /> MyFatoor library : curlPostRequest => result <pre>";
    // print_r(json_decode($result));
    // echo "<br />";
    // die();

    if (curl_error($channal))
    {
      print curl_error($channal);
      die();
    }
    else
    {
      if(empty($result)){
        // echo "the response is empty when the token key is not correct.";
        return;
      }
      // print 'ret: ' .$result;
      return $result;
    }

  }
  
  
  /**
   * Mariam Code For integration with MyFatoora V1
   */
  public function Hello()
  {
      echo '*****************'; die();
  }

  public function prepareCheckoutFatoora($order_id)//, $final_total, $currency_symbol, $user_ip , $user_email , $payment_brand, $order_data, $user_data, $country_iso)
  {
    /**
     * Integration With MyFatoora v1
     */
      $access_token = $this->generate_token();
      
      $t= time();
      $name = "Demo Name";
      $post_string = '{
          "InvoiceValue": 10,
          "CustomerName": "'.$name.'",
          "CustomerBlock": "Block",
          "CustomerStreet": "Street",
          "CustomerHouseBuildingNo": "Building no",
          "CustomerCivilId": "123456789124",
          "CustomerAddress": "Payment Address",
          "CustomerReference": "'.$t.'",
          "DisplayCurrencyIsoAlpha": "KSA",
          "CountryCodeId": "+965",
          "CustomerMobile": "1234567890",
          "CustomerEmail": "dd@dd.net.sa",
          "DisplayCurrencyId": 3,
          "SendInvoiceOption": 1,
          "InvoiceItemsCreate": [
            {
              "ProductId": null,
              "ProductName": "Product01",
              "Quantity": 1,
              "UnitPrice": 2
            }
          ],
          "CallBackUrl":  "'.base_url().'orders/payment_gateways/feed_back_myFatoora'.'",
          "Language": 2,
          "ExpireDate": "2022-12-31T13:30:17.812Z",
          "ApiCustomFileds": "weight=10,size=L,lenght=170",
          "ErrorUrl": "'.base_url().'orders/payment_gateways/feed_back_myFatoora/1'.'"
        }';
      $soap_do     = curl_init();
      curl_setopt($soap_do, CURLOPT_URL, "https://apidemo.myfatoorah.com/ApiInvoices/CreateInvoiceIso");
      curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
      curl_setopt($soap_do, CURLOPT_TIMEOUT, 10);
      curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($soap_do, CURLOPT_POST, true);
      curl_setopt($soap_do, CURLOPT_POSTFIELDS, $post_string);
      curl_setopt($soap_do, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8','Content-Length: ' . strlen($post_string),  'Accept: application/json','Authorization: Bearer '.$access_token));
      $result1 = curl_exec($soap_do);
      //echo "<pre>";print_r($result1);die;
      $err    = curl_error($soap_do);
      $json1= json_decode($result1,true);
      $RedirectUrl= $json1['RedirectUrl'];
      $ref_Ex=explode('/',$RedirectUrl);
      $referenceId =  $ref_Ex[4];
      curl_close($soap_do);
      //echo $RedirectUrl; die();
      redirect($RedirectUrl, 'refresh');
      
    
  }
    
  public function generate_token()
  {
    // Test Mode 
    $access_token = "rLtt6JWvbUHDDhsZnfpAhpYk4dxYDQkbcPTyGaKp2TYqQgG7FGZ5Th_WD53Oq8Ebz6A53njUoo1w3pjU1D4vs_ZMqFiz_j0urb_BH9Oq9VZoKFoJEDAbRZepGcQanImyYrry7Kt6MnMdgfG5jn4HngWoRdKduNNyP4kzcp3mRv7x00ahkm9LAK7ZRieg7k1PDAnBIOG3EyVSJ5kK4WLMvYr7sCwHbHcu4A5WwelxYK0GMJy37bNAarSJDFQsJ2ZvJjvMDmfWwDVFEVe_5tOomfVNt6bOg9mexbGjMrnHBnKnZR1vQbBtQieDlQepzTZMuQrSuKn-t5XZM7V6fCW7oP-uXGX-sMOajeX65JOf6XVpk29DP6ro8WTAflCDANC193yof8-f5_EYY-3hXhJj7RBXmizDpneEQDSaSz5sFk0sV5qPcARJ9zGG73vuGFyenjPPmtDtXtpx35A-BVcOSBYVIWe9kndG3nclfefjKEuZ3m4jL9Gg1h2JBvmXSMYiZtp9MR5I6pvbvylU_PP5xJFSjVTIz7IQSjcVGO41npnwIxRXNRxFOdIUHn0tjQ-7LwvEcTXyPsHXcMD8WtgBh-wxR8aKX7WPSsT1O8d8reb2aR7K3rkV3K82K_0OgawImEpwSvp9MNKynEAJQS6ZHe_J_l77652xwPNxMRTMASk1ZsJL";
    
    // $access_token = "rLtt6JWvbUHDDhsZnfpAhpYk4dxYDQkbcPTyGaKp2TYqQgG7FGZ5Th_WD53Oq8Ebz6A53njUoo1w3pjU1D4vs_ZMqFiz_j0urb_BH9Oq9VZoKFoJEDAbRZepGcQanImyYrry7Kt6MnMdgfG5jn4HngWoRdKduNNyP4kzcp3mRv7x00ahkm9LAK7ZRieg7k1PDAnBIOG3EyVSJ5kK4WLMvYr7sCwHbHcu4A5WwelxYK0GMJy37bNAarSJDFQsJ2ZvJjvMDmfWwDVFEVe_5tOomfVNt6bOg9mexbGjMrnHBnKnZR1vQbBtQieDlQepzTZMuQrSuKn";
    
    // Live Mode

    /*
    $username = 'apiaccount@myfatoorah.com';
    $password = 'api12345*';
      
      $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL,'https://apidemo.myfatoorah.com/Token');
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query(array('grant_type' => 'password','username' => $username,'password' =>$password)));
    $result = curl_exec($curl);
    $info = curl_getinfo($curl);
    curl_close($curl);
    $json = json_decode($result, true);
    if(isset($json['access_token']) && !empty($json['access_token'])){
        $access_token= $json['access_token'];
    }else{
      $access_token='';
    }
    */
    
    return $access_token;
  }
}