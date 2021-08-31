<?php if(! defined('BASEPATH')) exit('No direct script access allowed');

class Aramex_curl
{
	private $data_path;
	private $config_params = array();
	private $connection_params = array();

	public function __construct()
  {
      $this->CI = &get_instance();

      $this->config_params['AccountCountryCode'] = 'SA';
      $this->config_params['AccountEntity']      = 'RUH';
      $this->config_params['AccountNumber']      = 162359;//20016;
      $this->config_params['AccountPin']         = 543543;//331421;
      $this->config_params['UserName']           = 'cutomer-service@almohiagroup.com';
      $this->config_params['Password']           = 'CSzxcv0412@';
      $this->config_params['Version']            = 'v1';
      $this->config_params['Source']             = 24;
  }

  public function createShipment($request_data)
  {
    //$url = "https://ws.dev.aramex.net/ShippingAPI.V2/Shipping/Service_1_0.svc/json/CreateShipments";   //test env
		$url = "https://ws.aramex.net/ShippingAPI.V2/Shipping/Service_1_0.svc/json/CreateShipments";

		$products_names = $request_data['products_names'];
		$city 				= $request_data['from_city']; // from city
        $phone1 			= $request_data['from_phone'];  //site phone
        $user_cell 			= $request_data['from_cell']; //site cell
        $email 				= $request_data['email'];  // site email
        $to_city 			= $request_data['to_city']; // shipped to this city
        $to_country_code    = $request_data['to_country_code']; // shipped to this country
        $to_phone 			= $request_data['to_phone'];
        $to_cell 			= $request_data['to_cell'];
        $to_email 			= $request_data['to_email'];
        $weight 			= $request_data['weight'];
		$items_count 		= $request_data['items_count'];
		$shipping_date_time = $request_data['shipping_date_time'];
		$due_date 			= $request_data['due_date'];
        $ship_date          = $request_data['ship_date'];
        $to_address     	= trim(preg_replace('/\s\s+/', ' ', $request_data['to_address']));
        $to_user_name       = $request_data['to_user_name'];
        $services           = $request_data['services'];
        
        $product_group  = 'DOM';
        $product_type   = 'CDS';
        $payment_type   = 'P';//$request_data['payment_type'];//'P';
        $cash_on_delivery_amount = $request_data['cod'];//'{"CurrencyCode":"SAR","Value":100.000}';//'null';
		$goods_origin_country = $this->config_params['AccountCountryCode'];
//"ShippingDateTime":"\/Date(1582075970000-0500)\/","DueDate":"\/Date(1582075970000)\/



    //$json = '{"ClientInfo":{"UserName":"'.$this->config_params['UserName'].'","Password":"'.$this->config_params['Password'].'","Version":"'.$this->config_params['Version'].'","AccountNumber":"'.$this->config_params['AccountNumber'].'","AccountPin":"'.$this->config_params['AccountPin'].'","AccountEntity":"'.$this->config_params['AccountEntity'].'","AccountCountryCode":"'.$this->config_params['AccountCountryCode'].'","Source":'.$this->config_params['Source'].'},"LabelInfo":null,"Shipments":[{"Reference1":"","Reference2":"","Reference3":"","Shipper":{"Reference1":"","Reference2":"","AccountNumber":"'.$this->config_params['AccountNumber'].'","PartyAddress":{"Line1":"","Line2":"","Line3":"","City":"'.$city.'","StateOrProvinceCode":"","PostCode":"","CountryCode":"'.$this->config_params['AccountCountryCode'].'","Longitude":0,"Latitude":0,"BuildingNumber":null,"BuildingName":null,"Floor":null,"Apartment":null,"POBox":null,"Description":null},"Contact":{"Department":"","PersonName":"aramex","Title":"","CompanyName":"aramex","PhoneNumber1":"'.$phone1.'","PhoneNumber1Ext":"","PhoneNumber2":"","PhoneNumber2Ext":"","FaxNumber":"","CellPhone":"'.$user_cell.'","EmailAddress":"'.$email.'","Type":""}},"Consignee":{"Reference1":"","Reference2":"","AccountNumber":"","PartyAddress":{"Line1":"'.$to_address.'","Line2":"","Line3":"","City":"'.$to_city.'","StateOrProvinceCode":"","PostCode":"","CountryCode":"'.$to_country_code.'","Longitude":0,"Latitude":0,"BuildingNumber":"","BuildingName":"","Floor":"","Apartment":"","POBox":null,"Description":""},"Contact":{"Department":"","PersonName":"'.$to_user_name.'","Title":"","CompanyName":"'.$to_user_name.'","PhoneNumber1":"'.$to_phone.'","PhoneNumber1Ext":"","PhoneNumber2":"","PhoneNumber2Ext":"","FaxNumber":"","CellPhone":"'.$to_cell.'","EmailAddress":"'.$to_email.'","Type":""}},"ThirdParty":{"Reference1":"","Reference2":"","AccountNumber":"","PartyAddress":{"Line1":"","Line2":"","Line3":"","City":"","StateOrProvinceCode":"","PostCode":"","CountryCode":"","Longitude":0,"Latitude":0,"BuildingNumber":null,"BuildingName":null,"Floor":null,"Apartment":null,"POBox":null,"Description":null},"Contact":{"Department":"","PersonName":"","Title":"","CompanyName":"","PhoneNumber1":"","PhoneNumber1Ext":"","PhoneNumber2":"","PhoneNumber2Ext":"","FaxNumber":"","CellPhone":"","EmailAddress":"","Type":""}},"ShippingDateTime":"\/Date(1582075970000-0500)\/","DueDate":"\/Date(1582075970000)\/","Comments":"","PickupLocation":"","OperationsInstructions":"","AccountingInstrcutions":"","Details":{"Dimensions":null,"ActualWeight":{"Unit":"KG","Value":'.$weight.'},"ChargeableWeight":null,"DescriptionOfGoods":"'.$products_names.'","GoodsOriginCountry":"'.$goods_origin_country.'","NumberOfPieces":'.$items_count.',"ProductGroup":"'.$product_group.'","ProductType":"'.$product_type.'","PaymentType":"'.$payment_type.'","PaymentOptions":"","CustomsValueAmount":null,"CashOnDeliveryAmount":'.$cash_on_delivery_amount.',"InsuranceAmount":null,"CashAdditionalAmount":null,"CashAdditionalAmountDescription":"","CollectAmount":null,"Services":"","Items":[]},"Attachments":[],"ForeignHAWB":"","TransportType ":0,"PickupGUID":"","Number":null,"ScheduledDelivery":null}],"Transaction":{"Reference1":"","Reference2":"","Reference3":"","Reference4":"","Reference5":""}}';
    $json = '{"ClientInfo":{"UserName":"'.$this->config_params['UserName'].'","Password":"'.$this->config_params['Password'].'","Version":"'.$this->config_params['Version'].'","AccountNumber":"'.$this->config_params['AccountNumber'].'","AccountPin":"'.$this->config_params['AccountPin'].'","AccountEntity":"'.$this->config_params['AccountEntity'].'","AccountCountryCode":"'.$this->config_params['AccountCountryCode'].'","Source":'.$this->config_params['Source'].'},"LabelInfo":null,"Shipments":[{"Reference1":"","Reference2":"","Reference3":"","Shipper":{"Reference1":"","Reference2":"","AccountNumber":"'.$this->config_params['AccountNumber'].'","PartyAddress":{"Line1":"","Line2":"","Line3":"","City":"'.$city.'","StateOrProvinceCode":"","PostCode":"","CountryCode":"'.$this->config_params['AccountCountryCode'].'","Longitude":0,"Latitude":0,"BuildingNumber":null,"BuildingName":null,"Floor":null,"Apartment":null,"POBox":null,"Description":null},"Contact":{"Department":"","PersonName":"aramex","Title":"","CompanyName":"aramex","PhoneNumber1":"'.$phone1.'","PhoneNumber1Ext":"","PhoneNumber2":"","PhoneNumber2Ext":"","FaxNumber":"","CellPhone":"'.$user_cell.'","EmailAddress":"'.$email.'","Type":""}},"Consignee":{"Reference1":"","Reference2":"","AccountNumber":"","PartyAddress":{"Line1":"'.$to_address.'","Line2":"","Line3":"","City":"'.$to_city.'","StateOrProvinceCode":"","PostCode":"","CountryCode":"'.$to_country_code.'","Longitude":0,"Latitude":0,"BuildingNumber":"","BuildingName":"","Floor":"","Apartment":"","POBox":null,"Description":""},"Contact":{"Department":"","PersonName":"'.$to_user_name.'","Title":"","CompanyName":"'.$to_user_name.'","PhoneNumber1":"'.$to_phone.'","PhoneNumber1Ext":"","PhoneNumber2":"","PhoneNumber2Ext":"","FaxNumber":"","CellPhone":"'.$to_cell.'","EmailAddress":"'.$to_email.'","Type":""}},"ThirdParty":{"Reference1":"","Reference2":"","AccountNumber":"","PartyAddress":{"Line1":"","Line2":"","Line3":"","City":"","StateOrProvinceCode":"","PostCode":"","CountryCode":"","Longitude":0,"Latitude":0,"BuildingNumber":null,"BuildingName":null,"Floor":null,"Apartment":null,"POBox":null,"Description":null},"Contact":{"Department":"","PersonName":"","Title":"","CompanyName":"","PhoneNumber1":"","PhoneNumber1Ext":"","PhoneNumber2":"","PhoneNumber2Ext":"","FaxNumber":"","CellPhone":"","EmailAddress":"","Type":""}},"ShippingDateTime":"\/Date('.$ship_date.')\/","DueDate":"\/Date('.$due_date.')\/","Comments":"","PickupLocation":"","OperationsInstructions":"","AccountingInstrcutions":"","Details":{"Dimensions":null,"ActualWeight":{"Unit":"KG","Value":'.$weight.'},"ChargeableWeight":null,"DescriptionOfGoods":"'.$products_names.'","GoodsOriginCountry":"'.$goods_origin_country.'","NumberOfPieces":'.$items_count.',"ProductGroup":"'.$product_group.'","ProductType":"'.$product_type.'","PaymentType":"'.$payment_type.'","PaymentOptions":"","CustomsValueAmount":null,"CashOnDeliveryAmount":'.$cash_on_delivery_amount.',"InsuranceAmount":null,"CashAdditionalAmount":null,"CashAdditionalAmountDescription":"","CollectAmount":null,"Services":"'.$services.'","Items":[]},"Attachments":[],"ForeignHAWB":"","TransportType ":0,"PickupGUID":"","Number":null,"ScheduledDelivery":null}],"Transaction":{"Reference1":"","Reference2":"","Reference3":"","Reference4":"","Reference5":""}}';
//echo $json.'<br />.<br />'; 

    $response = json_decode($this->request($url, $json), true);

  //echo '<pre>'; print_r($response); die();

    if(isset($response['Shipments']['0']['ID']))
    {
      $output = array(
          'response' => 1,
          'id' => $response['Shipments']['0']['ID']
        );
    }
    else {
      $output = array(
        'response' => 0,
        'error' => $response['Notifications']['0']['Message']
      );
    }
    return $output;
  }

	public function getTracking($tracking_id)
	{
		$url = 'https://ws.aramex.net/ShippingAPI.V2/Tracking/Service_1_0.svc/json/TrackShipments';
		$json = '{"ClientInfo":{"UserName":"'.$this->config_params['UserName'].'","Password":"'.$this->config_params['Password'].'","Version":"'.$this->config_params['Version'].'","AccountNumber":"'.$this->config_params['AccountNumber'].'","AccountPin":"'.$this->config_params['AccountPin'].'","AccountEntity":"'.$this->config_params['AccountEntity'].'","AccountCountryCode":"'.$this->config_params['AccountCountryCode'].'","Source":'.$this->config_params['Source'].'},"GetLastTrackingUpdateOnly":false,"Shipments":["'.$tracking_id.'"],"Transaction":{"Reference1":"","Reference2":"","Reference3":"","Reference4":"","Reference5":""}}';

		$response = json_decode($this->request($url, $json), true);

		if(isset($response['TrackingResults'][0]['Value']))
		{
			$output = array(
				'response' => 1,
				'info' 	=> $response['TrackingResults'][0]['Value']
			);
		}
		else {
			$output = array(
				'response' => 0,
				'info' 	=> ''
			);
		}

		return $output;
	}

    public function print_Shipment($track_number)
    {
	   $url = "https://ws.aramex.net/ShippingAPI.V2/Shipping/Service_1_0.svc/json/PrintLabel";

        $product_group  = 'DOM';
        $product_type   = 'CDS';
        $payment_type   = 'P';
        $cash_on_delivery_amount = 'null';
		$goods_origin_country = $this->config_params['AccountCountryCode'];


        $json = '{"ClientInfo":{"UserName":"'.$this->config_params['UserName'].'","Password":"'.$this->config_params['Password'].'","Version":"'.$this->config_params['Version'].'","AccountNumber":"'.$this->config_params['AccountNumber'].'","AccountPin":"'.$this->config_params['AccountPin'].'","AccountEntity":"'.$this->config_params['AccountEntity'].'","AccountCountryCode":"'.$this->config_params['AccountCountryCode'].'","Source":'.$this->config_params['Source'].'},"LabelInfo":{"ReportID":9729,"ReportType":"URL"},"OriginEntity":"","ProductGroup":"'.$product_group.'","ShipmentNumber":"'.$track_number.'","Transaction":{"Reference1":"","Reference2":"","Reference3":"","Reference4":"","Reference5":""}}';

        $response = json_decode($this->request($url, $json), true);
    
        if($response['HasErrors'] != 1)
        {
          $output = array(
              'response' => 1,
              'URL' => $response['ShipmentLabel']['LabelURL']
            );
        }
        else {
          $output = array(
            'response' => 0,
            'error' => $response['Notifications']['0']['Message']
          );
        }
        return $output;
  }

  public function cancel_Shipment($track_number, $comments)
    {
	   $url = "https://ws.aramex.net/ShippingAPI.V2/Shipping/Service_1_0.svc/json/CancelPickup";

        $product_group  = 'DOM';
        $product_type   = 'CDS';
        $payment_type   = 'P';
        $cash_on_delivery_amount = 'null';
		$goods_origin_country = $this->config_params['AccountCountryCode'];


    $json = '{"ClientInfo":{"UserName":"'.$this->config_params['UserName'].'","Password":"'.$this->config_params['Password'].'","Version":"'.$this->config_params['Version'].'","AccountNumber":"'.$this->config_params['AccountNumber'].'","AccountPin":"'.$this->config_params['AccountPin'].'","AccountEntity":"'.$this->config_params['AccountEntity'].'","AccountCountryCode":"'.$this->config_params['AccountCountryCode'].'","Source":'.$this->config_params['Source'].'},"Comments":"'.$comments.'","PickupGUID":"'.$track_number.'","Transaction":{"Reference1":"","Reference2":"","Reference3":"","Reference4":"","Reference5":""}}';

    //echo $json; die();

    $response = json_decode($this->request($url, $json), true);

    //echo '<pre>'; print_r($response); die();

    if($response['HasErrors'] != 1)
    {
      $output = array(
          'response' => 1,
          'success_response' => $response
        );
    }
    else {
      $output = array(
        'response' => 0,
        'error' => $response['Notifications']['0']['Message']
      );
    }
    return $output;
  }

  function request($url, $post)
  {
      //$post = json_encode($post);
      //echo $post.'<br />';
      $ch = curl_init($url);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          'Content-Type: application/json',
          'Accept: application/json')
      );

      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      $response = curl_exec($ch);
      curl_close($ch);
//print_r($response); die();
      if ($ch === false) {
      return 'cURL Error #:' . $ch;
      } else {
      return $response;
      }
  }


/*
$postData = array(
  "ClientInfo" => array(
    "UserName" => $this->config_params['UserName'],
    "Password" => $this->config_params['Password'],
    "Version" => $this->config_params['Version'],
    "AccountNumber" => $this->config_params['AccountNumber'],
    "AccountPin" => $this->config_params['AccountPin'],
    "AccountEntity" => $this->config_params['AccountEntity'],
    "AccountCountryCode" => $this->config_params['AccountCountryCode'],
    "Source" => $this->config_params['Source']
  ),
  "LabelInfo" => null,
  "Shipments" => array(
    array(
      "Reference1" => "",
      "Reference2" => "",
      "Reference3" => "",
      "Shipper" => array(
        "Reference1" => "",
        "Reference2" => "",
        "AccountNumber" => $this->config_params['AccountNumber'],
        "PartyAddress" => array(
          "Line1" => "Test",
          "Line2" => "",
          "Line3" => "",
          "City" => "Amman",
          "StateOrProvinceCode" => "",
          "PostCode" => "",
          "CountryCode" => $this->config_params['AccountCountryCode'],
          "Longitude" => 0,
          "Latitude" => 0,
          "BuildingNumber" => null,
          "BuildingName" => null,
          "Floor" => null,
          "Apartment" => null,
          "POBox" => null,
          "Description" => null
        ),
        "Contact" => array(
          "Department" => "",
          "PersonName" => "aramex",
          "Title" => "",
          "CompanyName" => "aramex",
          "PhoneNumber1" => "009625515111",
          "PhoneNumber1Ext" => "",
          "PhoneNumber2" => "",
          "PhoneNumber2Ext" => "",
          "FaxNumber" => "",
          "CellPhone" => "9677956000200",
          "EmailAddress" => "test@test.com",
          "Type" => ""
        )
      ),
      "Consignee" => array(
            "Reference1" => "",
            "Reference2" => "",
            "AccountNumber" => "",
            "PartyAddress" => array(
              "Line1" => "Test",
              "Line2" => "",
              "Line3" => "",
              "City" => "Duabi",
              "StateOrProvinceCode" => "",
              "PostCode" => "",
              "CountryCode" => "AE",
              "Longitude" => 0,
              "Latitude" => 0,
              "BuildingNumber" => "",
              "BuildingName" => "",
              "Floor" => "",
              "Apartment" => "",
              "POBox" => null,
              "Description" => ""
            ),
            "Contact" => array(
              "Department" => "",
              "PersonName" => "aramex",
              "Title" => "",
              "CompanyName" => "aramex",
              "PhoneNumber1" => "009625515111",
              "PhoneNumber1Ext" => "",
              "PhoneNumber2" => "",
              "PhoneNumber2Ext" => "",
              "FaxNumber" => "",
              "CellPhone" => "9627956000200",
              "EmailAddress" => "test@test.com",
              "Type" => ""
            ),
          ),
          "ThirdParty" => array(
            "Reference1" => "",
            "Reference2" => "",
            "AccountNumber" => "",
            "PartyAddress" => array(
              "Line1" => "",
              "Line2" => "",
              "Line3" => "",
              "City" => "",
              "StateOrProvinceCode" => "",
              "PostCode" => "",
              "CountryCode" => "",
              "Longitude" => 0,
              "Latitude" => 0,
              "BuildingNumber" => null,
              "BuildingName" => null,
              "Floor" => null,
              "Apartment" => null,
              "POBox" => null,
              "Description" => null
            ),
            "Contact" => array(
              "Department" => "",
              "PersonName" => "",
              "Title" => "",
              "CompanyName" => "",
              "PhoneNumber1" => "",
              "PhoneNumber1Ext" => "",
              "PhoneNumber2" => "",
              "PhoneNumber2Ext" => "",
              "FaxNumber" => "",
              "CellPhone" => "",
              "EmailAddress" => "",
              "Type" => ""
            )
          ),
          "ShippingDateTime" => "\/Date(1579693100-0500)\/",
          "DueDate" => "\/Date(1579737600-0500)\/",
          "Comments" => "",
          "PickupLocation" => "",
          "OperationsInstructions" => "",
          "AccountingInstrcutions" => "",
          "Details" => array(
            "Dimensions" => null,
            "ActualWeight" => array("Unit" => "KG","Value" => 0.5),
            "ChargeableWeight" => null,
            "DescriptionOfGoods" => $products_names,
            "GoodsOriginCountry" => $this->config_params['AccountCountryCode'],
            "NumberOfPieces" => 1,
            "ProductGroup" => "EXP",
            "ProductType" => "PDX",
            "PaymentType" => "P",
            "PaymentOptions" => "",
            "CustomsValueAmount" => null,
            "CashOnDeliveryAmount" => null,
            "InsuranceAmount" => null,
            "CashAdditionalAmount" => null,
            "CashAdditionalAmountDescription" => "",
            "CollectAmount" => null,
            "Services" => "",
            "Items" => array()
          ),
          "Attachments" => array(),
          "ForeignHAWB" => "",
          "TransportType" => 0,
          "PickupGUID" => "",
          "Number" => null,
          "ScheduledDelivery" => null
        )
      ),

      "Transaction" => array(
        "Reference1" => "",
        "Reference2" => "",
        "Reference3" => "",
        "Reference4" => "",
        "Reference5" => ""
      )
    );
    */


}