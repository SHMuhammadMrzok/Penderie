<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Pushnotification{

  public function __construct($params = array())
  {
    $this->CI=&get_instance();
    $this->CI->load->library('gateways');
  }


    //parse
	function sendPush($title)
    {
        $APPLICATION_ID = "";
        $REST_API_KEY   = "";

        $url  = 'https://api.parse.com/1/push';
        $data = array(
            //'channel' => 'global',
            'where' => '{}',
            'data' => array(
                'alert' => $title
                )
            );

        $_data   = json_encode($data);
        $headers = array(
            'X-Parse-Application-Id: ' . $APPLICATION_ID,
            'X-Parse-REST-API-Key: ' . $REST_API_KEY,
            'Content-Type: application/json',
            'Content-Length: ' . strlen($_data)

        );

        $curl    = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $_data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_exec($curl);

    }

    // one signal
    function sendMessageOld($message_en='', $message_ar=''){


        $app_id = $this->CI->gateways->get_gateway_field_value('one_signal_app_id');
        $api_key = $this->CI->gateways->get_gateway_field_value('one_signal_api_key');

		$content = array(
			"en" => $message_en
            //,"ar" => $message_ar
			);

		$fields = array(
			'app_id' => "$app_id",
			'included_segments' => array('All'),
            'data' => array("message" => "bar"),
			'contents' => $content
		);

		$fields = json_encode($fields);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
												   "Authorization: Basic $api_key"));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response = curl_exec($ch);
		curl_close($ch);

		return $response;
	}

    //firebase
  function sendMessage($message, $text='')
  {
    $api_key = $this->CI->gateways->get_gateway_field_value('firebase_key');
    $url = 'https://fcm.googleapis.com/fcm/send';
    //$api_key = 'AAAAdK9pUVs:APA91bF5jaqQYMmxjIel_xcfDT0pqB76jy8L49dKe4xP8n0Izj0953ewf6b85ceG8DiuStE1q8TyP2lO_doOxXYLOMnbXjFyxxHNcxVInrnTkAgDAWbCUPPfZwo61sA6f3DyjDJKPmFH';
    $fields = array (
        //'registration_ids' => array('fkx3cs51x1A:APA91bGG-TZAhOTAp0chGZQ9qpppxzN7jZ5wDtWa1S853o4APocxe9eBkruTTYsVjJUvul_YkNlp3rUx8oG3PHDpM-xQfP1m-0GuFI1wqBEyviC_x37hdO2Vmn6W_OsCrbIwZi9hKilW'),
        'to' => '/topics/all',
        'notification' => array (
          "body" => $text,
          "title" => $message,
          'sound' => 'default'
        )
    );

  //header includes Content type and api key
  $headers = array(
      'Content-Type:application/json',
      'Authorization:key='.$api_key
  );

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
  $result = curl_exec($ch);
  if ($result === FALSE) {
      die('FCM Send Error: ' . curl_error($ch));
  }
  curl_close($ch);
  //var_dump($result);
  return $result;
  }

}
