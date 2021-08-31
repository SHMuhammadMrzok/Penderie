<?php if(! defined('BASEPATH')) exit('No direct script access allowed');

class Aymakan_curl
{
	private $data_path;
	private $config_params = array();
	private $connection_params = array();
	public $authorization_key;

	public function __construct()
  {
      $this->CI = &get_instance();
			$this->CI->load->library('Gateways');
			$this->authorization_key = $this->CI->gateways->get_gateway_field_value('aymakan_authorization');
  }

  public function createShipment($Aymakan_arr)
  {

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

    $url = "https://aymakan.com.sa/api/v2/shipping/create";//"https://dev.aymakan.com.sa/api/v2/shipping/create";
    $authorization = 'Authorization:'.$this->authorization_key;

    //echo $Aymakan_arr['requested_by'].'<prE>';print_r($Aymakan_arr); die();

    $json = '{
                "requested_by":"'.$Aymakan_arr['requested_by'].'",
                "declared_value":'.$Aymakan_arr['declared_value'].',
                "declared_value_currency":"'.$Aymakan_arr['declared_value_currency'].'",
                "reference":"'.$Aymakan_arr['order_id'].'",
                "is_cod":'.$Aymakan_arr['is_cod'].',
                "cod_amount":'.$Aymakan_arr['cod_amount'].',
                "currency":"'.$Aymakan_arr['currency'].'",
                "delivery_name":"'.$Aymakan_arr['delivery_name'].'",
                "delivery_email":"'.$Aymakan_arr['delivery_email'].'",
                "delivery_city":"'.$Aymakan_arr['delivery_city'].'",
                "delivery_address":"'.$Aymakan_arr['delivery_address'].'",
                "delivery_region":"'.$Aymakan_arr['delivery_region'].'",
                "delivery_postcode":"",
                "delivery_country":"'.$Aymakan_arr['delivery_country'].'",
                "delivery_phone":"'.$Aymakan_arr['delivery_phone'].'",
                "delivery_description":"",
                "collection_name":"'.$Aymakan_arr['collection_name'].'",
                "collection_email":"'.$Aymakan_arr['collection_email'].'",
                "collection_city":"Riyadh",
                "collection_address":"'.$Aymakan_arr['collection_address'].'",
                "collection_region":"",
                "collection_postcode":"",
                "collection_country":"SA",
                "collection_phone":"'.$Aymakan_arr['collection_phone'].'",
                "collection_description":"'.$Aymakan_arr['collection_description'].'",
                "pickup_date":"'.$Aymakan_arr['pickup_date'].'",
                "weight":'.$Aymakan_arr['weight'].',
                "pieces":'.$Aymakan_arr['pieces'].',
                "items_count":'.$Aymakan_arr['items_count'].'
              }';

                
    $response = json_decode($this->request($url, $json, $authorization, 'POST'), true);

                //echo"<pre>";print_r($response);die();

    if(isset($response['success']))
    {
      $output = array(
          'response' => 1,
          'sucsess'  => $response['data']['shipping']
        );
    }
    else {
      $output = array(
        'response' => 0,
        'error' => $response
      );
    }


    return $output;
  }

  public function track_shipment($tracking_number)
  {
    //$tracking_number

    $url = "https://aymakan.com.sa/api/v2/shipping/track/".$tracking_number;
    $authorization = 'Authorization:'.$this->authorization_key;
		//39310042d650a36e5647b16faa6a5df9-1b1caabd-1299-4081-a499-424e0ca898f3-7513e48cfcac6ef85b09294bff4dd8ea/392017a52ce98b4993095c33477fe07b/48f774d5-4546-4be6-baef-c684485e0342

    $response = json_decode($this->request($url, '', $authorization, 'GET'), true);

    if(isset($response['success']))
    {
      $output = array(
          'response' => 1,
          'sucsess'  => $response['data']['shipments']['0']
        );
    }
    else {
      $output = array(
        'response' => 0,
        'error' => $response
      );
    }


    return $output;
  }


  function request($url, $post, $authorization, $type)
  {

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
