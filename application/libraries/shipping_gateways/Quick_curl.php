<?php if(! defined('BASEPATH')) exit('No direct script access allowed');

class Quick_curl
{
	private $data_path;
	private $config_params = array();
	private $connection_params = array();

	public function __construct()
  {
      $this->CI = &get_instance();
  }
  
  public function createShipment($quick_arr)
  {
    $access_token = '';
    $url = "https://c.quick.sa.com/API/V3/Store/Shipment";
    $authorization = 'Authorization: Bearer k67UmDCYqO_lES_I67Sh_8mdwcd7ECYwkZLacdpp0y2r8GD8xxfuSoV4GU_ap4Jye7DS-DDe-fwKmPEJbb3T3LNIn2uA-WQ-sCrn4nLIdUhy2Z0Lnn9pYI0YfmHo3iMZM-cBrmdrd1LZSU9_heDrch2WzZFv11wI2aQm8xOJT2nF0GfXhJFBG-FRfHhcLFtubyRGU-HUqUPeJvuASFHEmvaQBGuRqgi1dGFtQV4p8D_RATsgyf6M6Sq_7FeOZxJctgj-hhCkj6dJFJ6BtJOK14hJM6BCqS2jzoDmxz4fIxjF2IZUHWKE_8codtFiV3lqADwQ2yL69WZBKK6zfUkGCMhEn9zml-cLitXEFmW-er4MDLlkCw8MxMpZm5bXPCLjD1OpUPN5LAuwoeFblWiI_yIl4_3xEjwN3yMsmdVgiW-zU8yKT80nlE3z7EccoAlDz_o9nbzolla5yxo2mxwvCZNeHAa5CkEQjwo-J_YBCrMLVi7PNbeFBRJRbnx8TCE0nfMKAg';
    
    //$preferred_time = strtotime($quick_arr['preferred_receipt_time']);
    //$delivery_time = strtotime($quick_arr['delivery_time']);
    
    $preferred_receipt_time = date('Y/m/d H:i',strtotime($quick_arr['preferred_receipt_time']));
    
    $delivery_time_after = date('Y/m/d H:i', strtotime($quick_arr['delivery_time']));
    
   // echo $delivery_time_after . $preferred_receipt_time;die();
    
    $json = '{
                "SandboxMode":false,
                "CustomerName":"'.$quick_arr['CustomerName'].'",
                "CustomerPhoneNumber":"'.$quick_arr['CustomerPhoneNumber'].'",
                "PreferredReceiptTime":"'.$preferred_receipt_time.'",
                "PreferredDeliveryTime":"'.$delivery_time_after.'",
                "NotesFromStore":"'.$quick_arr['delivery_notes'].'",
                "PaymentMethodId":'.$quick_arr['PaymentMethodId'].',
                "ShipmentContentValueSAR":'.$quick_arr['ShipmentContentValueSAR'].',
                "ShipmentContentTypeId":4,
                "AddedServicesIds":[6,7],
                "CustomerLocation":
                {
                "Desciption":"'.$quick_arr['Desciption'].'",
                "Longitude":"'.$quick_arr['Longitude'].'",
                "Latitude":"'.$quick_arr['Latitude'].'",
                "GoogleMapsFullLink":"https://www.google.com/maps/place/",
                "CountryId":1,
                "CityAsString":"'.$quick_arr['CityAsString'].'"
                },
                "ExternalStoreShipmentIdRef":"'.$quick_arr['ExternalStoreShipmentIdRef'].'",
                "API_Call_Source":"SBM_CART",
                "Currency":"'.$quick_arr['Currency'].'"
                }';
                
                $response = json_decode($this->request($url, $json, $authorization, 'POST'), true);

    if(isset($response['resultData']['id']))
    {
      $output = array(
          'response' => 1,
          'id' => $response['resultData']['id']
        );
    }
    else {
      $output = array(
        'response' => 0,
        'error' => $response['MessageEn']
      );
    }
    //echo"<pre>";print_r($output);die();
    return $output;
    
  }
  
  public function GetConsistentData()
  {
    /*
   When you create a new shipment using our API
    or when you retrieve shipment details you notice
    few constant numbers used which presents Ids of
    the consistent data in our system like cities Ids,
    Shipping and payment methods types, …etc
    Use this API whenever needed. Another use is if
    you want to display City list for your buyers at
    your open cart checkout page, You can grab City
    list from our system and show it for the user so they can pick the correct and exact CityId that you need to pass to the “create shipment” request later.
    */
    
    $access_token = '';
    $url = "https://c.quick.sa.com/API/V3/GetConsistentData";
    $authorization = 'Authorization: Bearer k67UmDCYqO_lES_I67Sh_8mdwcd7ECYwkZLacdpp0y2r8GD8xxfuSoV4GU_ap4Jye7DS-DDe-fwKmPEJbb3T3LNIn2uA-WQ-sCrn4nLIdUhy2Z0Lnn9pYI0YfmHo3iMZM-cBrmdrd1LZSU9_heDrch2WzZFv11wI2aQm8xOJT2nF0GfXhJFBG-FRfHhcLFtubyRGU-HUqUPeJvuASFHEmvaQBGuRqgi1dGFtQV4p8D_RATsgyf6M6Sq_7FeOZxJctgj-hhCkj6dJFJ6BtJOK14hJM6BCqS2jzoDmxz4fIxjF2IZUHWKE_8codtFiV3lqADwQ2yL69WZBKK6zfUkGCMhEn9zml-cLitXEFmW-er4MDLlkCw8MxMpZm5bXPCLjD1OpUPN5LAuwoeFblWiI_yIl4_3xEjwN3yMsmdVgiW-zU8yKT80nlE3z7EccoAlDz_o9nbzolla5yxo2mxwvCZNeHAa5CkEQjwo-J_YBCrMLVi7PNbeFBRJRbnx8TCE0nfMKAg';
    
    $json = '{}';
                
    $response = json_decode($this->request($url, $json, $authorization, 'GET'), true);

    echo"<pre>";print_r($response);die();
    
    if(isset($response['resultData']['id']))
    {
      $output = array(
          'response' => 1,
          'id' => $response['resultData']['id']
        );
    }
    else {
      $output = array(
        'response' => 0,
        'error' => $response['MessageEn']
      );
    }
    //print_r($output);
    return $output;
    
  }
  
  public function track_shipment($tracking_number)
  {
    
    $access_token = '';
    $url = "https://c.quick.sa.com/API/V3/Store/Shipment/Track";
    $authorization = 'Authorization: Bearer k67UmDCYqO_lES_I67Sh_8mdwcd7ECYwkZLacdpp0y2r8GD8xxfuSoV4GU_ap4Jye7DS-DDe-fwKmPEJbb3T3LNIn2uA-WQ-sCrn4nLIdUhy2Z0Lnn9pYI0YfmHo3iMZM-cBrmdrd1LZSU9_heDrch2WzZFv11wI2aQm8xOJT2nF0GfXhJFBG-FRfHhcLFtubyRGU-HUqUPeJvuASFHEmvaQBGuRqgi1dGFtQV4p8D_RATsgyf6M6Sq_7FeOZxJctgj-hhCkj6dJFJ6BtJOK14hJM6BCqS2jzoDmxz4fIxjF2IZUHWKE_8codtFiV3lqADwQ2yL69WZBKK6zfUkGCMhEn9zml-cLitXEFmW-er4MDLlkCw8MxMpZm5bXPCLjD1OpUPN5LAuwoeFblWiI_yIl4_3xEjwN3yMsmdVgiW-zU8yKT80nlE3z7EccoAlDz_o9nbzolla5yxo2mxwvCZNeHAa5CkEQjwo-J_YBCrMLVi7PNbeFBRJRbnx8TCE0nfMKAg';
            
    $json = '
            {
                "ShipmentsIds": ['.$tracking_number.']
            }
            ';
    
    $response = json_decode($this->request($url, $json, $authorization, 'POST'), true);
    //echo '<pre>';print_r($response); die();
    if(isset($response['resultData']['0']['shipmentStatusList']['0']))
    {
      $output = array(
          $response['resultData']['0']['shipmentStatusList']['0']
        );
    }
    else {
      $output = array(
        'response' => 0,
        'error' => $response['messageEn']
      );
    }
    //print_r($output);
    return $output;
    
  }
  
  public function print_quick($tracking_number)
  {
    //$tracking_number
    $access_token = '';
    $url = "https://c.quick.sa.com/API/V3/Store/Shipment/ShippingLabelPDF/".$tracking_number;
    $authorization = 'Authorization: Bearer k67UmDCYqO_lES_I67Sh_8mdwcd7ECYwkZLacdpp0y2r8GD8xxfuSoV4GU_ap4Jye7DS-DDe-fwKmPEJbb3T3LNIn2uA-WQ-sCrn4nLIdUhy2Z0Lnn9pYI0YfmHo3iMZM-cBrmdrd1LZSU9_heDrch2WzZFv11wI2aQm8xOJT2nF0GfXhJFBG-FRfHhcLFtubyRGU-HUqUPeJvuASFHEmvaQBGuRqgi1dGFtQV4p8D_RATsgyf6M6Sq_7FeOZxJctgj-hhCkj6dJFJ6BtJOK14hJM6BCqS2jzoDmxz4fIxjF2IZUHWKE_8codtFiV3lqADwQ2yL69WZBKK6zfUkGCMhEn9zml-cLitXEFmW-er4MDLlkCw8MxMpZm5bXPCLjD1OpUPN5LAuwoeFblWiI_yIl4_3xEjwN3yMsmdVgiW-zU8yKT80nlE3z7EccoAlDz_o9nbzolla5yxo2mxwvCZNeHAa5CkEQjwo-J_YBCrMLVi7PNbeFBRJRbnx8TCE0nfMKAg';
    
    $post = '';
    
    $response = json_decode($this->request($url, $post, $authorization, 'GET'), true);
    
    if(isset($response['resultData']))
    {
      $output = array(
          $response['resultData']
        );
    }
    else {
      $output = array(
        'response' => 0,
        'error' => $response['messageEn']
      );
    }
    //print_r($output);
    return $output;
  }
  
  public function Delete_quick_shippment($tracking_number)
  {
    //$tracking_number
    $access_token = '';
    $url = "https://c.quick.sa.com/API/V3/Shipment/".$tracking_number;
    $authorization = 'Authorization: Bearer k67UmDCYqO_lES_I67Sh_8mdwcd7ECYwkZLacdpp0y2r8GD8xxfuSoV4GU_ap4Jye7DS-DDe-fwKmPEJbb3T3LNIn2uA-WQ-sCrn4nLIdUhy2Z0Lnn9pYI0YfmHo3iMZM-cBrmdrd1LZSU9_heDrch2WzZFv11wI2aQm8xOJT2nF0GfXhJFBG-FRfHhcLFtubyRGU-HUqUPeJvuASFHEmvaQBGuRqgi1dGFtQV4p8D_RATsgyf6M6Sq_7FeOZxJctgj-hhCkj6dJFJ6BtJOK14hJM6BCqS2jzoDmxz4fIxjF2IZUHWKE_8codtFiV3lqADwQ2yL69WZBKK6zfUkGCMhEn9zml-cLitXEFmW-er4MDLlkCw8MxMpZm5bXPCLjD1OpUPN5LAuwoeFblWiI_yIl4_3xEjwN3yMsmdVgiW-zU8yKT80nlE3z7EccoAlDz_o9nbzolla5yxo2mxwvCZNeHAa5CkEQjwo-J_YBCrMLVi7PNbeFBRJRbnx8TCE0nfMKAg';
    
    $post = '';
    
    $response = json_decode($this->request($url, $post, $authorization, 'DELETE'), true);
    
    if(isset($response))
    {
      $output = array(
          $response
        );
    }
    else {
      $output = array(
        'response' => 0,
        'error' => $response['messageEn']
      );
    }
    return $output;
  }

  function request($url, $post, $authorization, $type)
  {
      //$post = json_encode($post);
      //echo $post;
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
