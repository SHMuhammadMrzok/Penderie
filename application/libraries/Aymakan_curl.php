<?php if(! defined('BASEPATH')) exit('No direct script access allowed');

class Aymakan_curl
{
	private $data_path;
	private $config_params = array();
	private $connection_params = array();

	public function __construct()
  {
      $this->CI = &get_instance();
  }
  
  public function createShipment()
  {
    
    //$Aymakan_arr
    
    
   // echo $delivery_time_after . $preferred_receipt_time;die();
    
    /*
        # Is Required NO / YES
    */
    
    /*
        requested_by = YES
        declared_value = YES
        declared_value_currency = NO
        reference	 = NO
        is_cod	 = NO
        cod_amount = Similar to declared_value
        currency = NO
        delivery_name = YES
        delivery_email = NO
        delivery_city = YES
        delivery_address = YES
        delivery_region = NO
        delivery_postcode = NO
        delivery_country = YES
        delivery_phone = YES
        delivery_description = NO
        collection_name = YES
        collection_email = YES
        collection_city = YES
        collection_address = YES
        collection_region = NO
        collection_postcode = NO
        collection_country = YES
        collection_phone = YES
        collection_description = NO
        pickup_date = YES
        weight = NO
        pieces = YES
        items_count = NO
    */
    
    $url = "https://dev.aymakan.com.sa/api/v2/shipping/create";
    $authorization = 'Authorization: 39310042d650a36e5647b16faa6a5df9-1b1caabd-1299-4081-a499-424e0ca898f3-7513e48cfcac6ef85b09294bff4dd8ea/392017a52ce98b4993095c33477fe07b/48f774d5-4546-4be6-baef-c684485e0342';
    
    //$pickup_date = date('Y/m/d H:i',strtotime($Aymakan_arr['pickup_date']));
    
    //'.$Aymakan_arr['CustomerName'].'
    
    $json = '{
                "requested_by":"Abdulrahman",
                "declared_value":300,
                "declared_value_currency":"SAR",
                "reference":"",
                "is_cod":1,
                "cod_amount":300,
                "currency":"SAR",
                "delivery_name":"Ahmed",
                "delivery_email":"abdusisi1979@gmail.com",
                "delivery_city":"Riyadh",
                "delivery_address":"Near King Abulazeaz road",
                "delivery_region":"The firist fegion",
                "delivery_postcode":"",
                "delivery_country":"riyadh",
                "delivery_phone":"01006018705",
                "delivery_description":"",
                "collection_name":"4you",
                "collection_email":"4you@info.com",
                "collection_city":"Jeddah",
                "collection_address":"Near King fouad road",
                "collection_region":"",
                "collection_postcode":"",
                "collection_country":"SA",
                "collection_phone":"01006012345",
                "collection_description":"لاتنس تصوير المنتج عند الاستلام",
                "pickup_date":"2020-02-23 13:15:00",
                "weight":"",
                "pieces":5,
                "items_count":3
              }';
                
                $response = json_decode($this->request($url, $json, $authorization, 'POST'), true);
                
                echo"<pre>";print_r($response);die();

    if(isset($response))
    {
      $output = array(
          'response' => 1,
          'response' => $response
        );
    }
    else {
      $output = array(
        'response' => 0,
        'error' => $response
      );
    }
    
    //echo"<pre>";print_r($output);die();
    
    return $output;
  }
  
  function request($url, $post, $authorization, $type)
  {
      //$post = json_encode($post);
      //echo $post;die();
      
      //$postString = http_build_query($post, '', '&');
      $ch = curl_init($url);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);//'POST/GET'
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      if($type == 'POST')
      {
        curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
      }
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          'Content-Type: application/json',
          'Accept: application/json',
          $authorization)
      );

      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      $response = curl_exec($ch);
      curl_close($ch);

      if ($ch === false) {
      return 'cURL Error #:' . $ch;
      } else {
      return $response;
      }
  }
}