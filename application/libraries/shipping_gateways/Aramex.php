<?php if(! defined('BASEPATH')) exit('No direct script access allowed');

class Aramex
{
	private $data_path;
	private $config_params = array();
	private $connection_params = array();

	public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->config->load('shipping_gateways');
        $this->CI->load->library('Gateways');

        $this->data_path = __DIR__ . '/data_aramex/';

        $this->config_params['AccountCountryCode'] = 'JO';
        $this->config_params['AccountEntity']      = 'AMM';
        $this->config_params['AccountNumber']      = 20016;
        $this->config_params['AccountPin']         = 331421;
        $this->config_params['UserName']           = 'reem@reem.com';
        $this->config_params['Password']           = '123456789';
        $this->config_params['Version']            = 'v1';
        $this->config_params['Source']             = 24;

        $this->connection_params = array(
            	  'ClientInfo'  			=> array(
									'UserName'			     => $this->config_params['UserName']					,
	                'Password'		 	     => $this->config_params['Password']					,
									'Version'		 	       => $this->config_params['Version']						,
									'AccountNumber'		 	 => $this->config_params['AccountNumber']			,
	                'AccountPin'		 		 => $this->config_params['AccountPin']				,
									'AccountEntity'		 	 => $this->config_params['AccountEntity']			,
	                'AccountCountryCode' => $this->config_params['AccountCountryCode'],
                	'Source' 			    	 => $this->config_params['Source']
            )
        );
    }

    public function calculator(array $originAddress, array $destinationAddress, array $shipmentDetails)
    {
        /*
         * 'OriginAddress' 	 	=> array(
									'City'					=> 'Cairo',
									'CountryCode'				=> 'EG'
								),

            'DestinationAddress' 	=> array(
                                        'City'					=> 'Dubai',
                                        'CountryCode'			=> 'AE'
                                    ),
            'ShipmentDetails'		=> array(
                                        'PaymentType'			 => 'P',
                                        'ProductGroup'			 => 'EXP',
                                        'ProductType'			 => 'PPX',
                                        'ActualWeight' 			 => array('Value' => 5, 'Unit' => 'KG'),
                                        'ChargeableWeight' 	     => array('Value' => 5, 'Unit' => 'KG'),
                                        'NumberOfPieces'		 => 5
                                    )
         */

        /**
         * Validate origin & destination address
         */


        $originAddressValidation      = $this->validateAddress('SA','SAUDI ARABIA');//($originAddress['CountryCode'], $originAddress['City']);
        $destinationAddressValidation = $this->validateAddress('AE','Dubai');//($destinationAddress['CountryCode'], $destinationAddress['City']);
				//print_r($originAddressValidation); die();

        if($originAddressValidation['status'] && $destinationAddressValidation['status']) {
            $wsdl_file  = $this->data_path . 'aramex-rates-calculator-wsdl.wsdl';
            $soapClient = new SoapClient($wsdl_file, array('trace' => 1));

            // calling the method and printing results
            try {
                $auth_call = $soapClient->CalculateRate(array_merge(
                    $this->connection_params,
                    array(
                        'OriginAddress'      => $originAddress,
                        'DestinationAddress' => $destinationAddress,
                        'ShipmentDetails'    => $shipmentDetails
                    )
                ));

                if(!$auth_call->HasErrors) {

                    $totalAmount = $auth_call->TotalAmount;

                    return array(
                        'status'       => true,
                        'CurrencyCode' => $totalAmount->CurrencyCode,
                        'Value'        => $totalAmount->Value
                    );
                } else {

                    $errors = $auth_call->Notifications->Notification;

                    return array(
                        'status' => false,
                        'errors' => $errors
                    );
                }
            } catch (SoapFault $fault) {
                die('Error : ' . $fault->faultstring);
            }
        } elseif(!$originAddressValidation['status']) {
            return $originAddressValidation;
        } elseif(!$destinationAddressValidation['status']) {
            return $destinationAddressValidation;
        }

    }

    public function trackingShipment($trackingNumber)
    {
        $wsdl_file  = $this->data_path . 'shipments-tracking-api-wsdl.wsdl';
        $soapClient = new SoapClient($wsdl_file);

        // calling the method and printing results
        try {
            $auth_call = $soapClient->TrackShipments(array_merge(
                $this->connection_params,
                array('Shipments' => array($trackingNumber))
            ));

            if(!$auth_call->HasErrors) {
                $trackingResults = $auth_call->TrackingResults;

                return array(
                    'status'          => true,
                    'trackingResults' => $trackingResults
                );
            } else {
                $errors = $auth_call->Notifications->Notification;

                return array(
                    'status' => false,
                    'errors' => $errors
                );
            }
        } catch (SoapFault $fault) {
            die('Error : ' . $fault->faultstring);
        }
    }

    public function createShipment()
    {
        $wsdl_file  = $this->data_path . 'shipping-services-api-wsdl.wsdl';
        $soapClient = new SoapClient($wsdl_file);

        // calling the method and printing results
        try {

						$shipmentParams = array(
								'LabelInfo' => null,
                'Shipments' => array(
									'Reference1' => '',
									'Reference2' => '',
									'Reference3' => '',
									'Shipper'	=> array(
										"Reference1" => "",
										"Reference2" => "",
										"AccountNumber" => "20016",
										"PartyAddress" => array(
											"Line1" => "Test",
											"Line2" => "",
											"Line3" => "",
											"City" => "Amman",
											"StateOrProvinceCode" => "",
											"PostCode" => "",
											"CountryCode" => "JO",
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
											"Type" => "")
                ),

								'Consignee' => array(
									"Reference1"=>"",
									"Reference2" => "",
									"AccountNumber" => "",
										"PartyAddress"=> array(
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
										) ,
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
										)

								),
								"ThirdParty" => array (
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
								"ShippingDateTime" => "\/Date(1484085970000-0500)\/",
								"DueDate" => "\/Date(1484085970000-0500)\/",
								"Comments" => "",
								"PickupLocation" => "",
								"OperationsInstructions" => "",
								"AccountingInstrcutions" => "",
								"Details" => array(
									"Dimensions" => null,
									"ActualWeight" => array("Unit" => "KG","Value" => 0.5),
									"ChargeableWeight" => null,
									"DescriptionOfGoods" => "Books",
									"GoodsOriginCountry" => "JO",
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
							),
							"Transaction" => array(
								"Reference1" => "",
								"Reference2" => "",
								"Reference3" => "",
								"Reference4" => "",
								"Reference5" => ""
							)
            );

            /*$shipmentParams['Shipments']['Shipment']['Details']['Items'][] = array(
                'PackageType' 	=> 'Box',
                'Quantity'		=> 1,
                'Weight'		=> array(
                    'Value'		=> 0.5,
                    'Unit'		=> 'Kg',
                ),
                'Comments'		=> 'Docs',
                'Reference'		=> ''
            );
						*/

						//echo '<pre>';print_r(array_merge($this->connection_params, $shipmentParams )); die();

            $auth_call = $soapClient->CreateShipments(array_merge($this->connection_params, $shipmentParams ));

            if(!$auth_call->HasErrors) {
                $shipments = $auth_call->Shipments;

                return array(
                    'status'    => true,
                    'shipments' => $shipments
                );
            } else {
                $errors = $auth_call->Notifications->Notification;

                return array(
                    'status' => false,
                    'errors' => $errors
                );
            }
        } catch (SoapFault $fault) {
            die('Error : ' . $fault->faultstring);
        }
    }

	public function getCountries()
	{
		$wsdl_file  = $this->data_path . 'Location-API-WSDL.wsdl';
        $soapClient = new SoapClient($wsdl_file);

        // calling the method and printing results
        try {
            $auth_call = $soapClient->FetchCountries($this->connection_params);

            if(!$auth_call->HasErrors) {
                $countries = $auth_call->Countries->Country;

                return array(
                    'status'    => true,
                    'countries' => $countries
                );
            } else {
                $errors = $auth_call->Notifications->Notification;

                return array(
                    'status' => false,
                    'errors' => $errors
                );
            }
        } catch (SoapFault $fault) {
            die('Error : ' . $fault->faultstring);
        }
	}

	public function getCountry($country_code)
    {
        $wsdl_file  = $this->data_path . 'Location-API-WSDL.wsdl';
        $soapClient = new SoapClient($wsdl_file);

        // calling the method and printing results
        try {
            $auth_call = $soapClient->FetchCountry(array_merge(
                $this->connection_params,
                array('Code' => $country_code)
            ));

            if(!$auth_call->HasErrors) {
                $country = $auth_call->Country;

                return array(
                    'status'  => true,
                    'country' => $country
                );
            } else {
                $errors = $auth_call->Notifications->Notification;

                return array(
                    'status' => false,
                    'errors' => $errors
                );
            }
        } catch (SoapFault $fault) {
            die('Error : ' . $fault->faultstring);
        }
    }

    public function getOffices($country_code)
    {
        $wsdl_file  = $this->data_path . 'Location-API-WSDL.wsdl';
        $soapClient = new SoapClient($wsdl_file);

        // calling the method and printing results
        try {
            $auth_call = $soapClient->FetchCountry(array_merge(
                $this->connection_params,
                array('CountryCode' => $country_code)
            ));

            if(!$auth_call->HasErrors) {
                $offices = $auth_call->Offices->Office;

                return array(
                    'status'  => true,
                    'offices' => $offices
                );
            } else {
                $errors = $auth_call->Notifications->Notification;

                return array(
                    'status' => false,
                    'errors' => $errors
                );
            }
        } catch (SoapFault $fault) {
            die('Error : ' . $fault->faultstring);
        }
    }

    public function getCities($country_code, $name_starts_with = '')
    {
        $wsdl_file  = $this->data_path . 'Location-API-WSDL.wsdl';
        $soapClient = new SoapClient($wsdl_file);

        $params = array_merge(
            $this->connection_params,
            array('CountryCode' => $country_code)
        );

        if(!empty($name_starts_with)) {
            $params = array_merge(
                $params,
                array('NameStartsWith' => $name_starts_with)
            );
        }

        // calling the method and printing results
        try {
            $auth_call = $soapClient->FetchCities($params);

            if(!$auth_call->HasErrors) {
                $cities = $auth_call->Cities->string;

                return array(
                    'status'  => true,
                    'cities'  => $cities
                );
            } else {
                $errors = $auth_call->Notifications->Notification;

                return array(
                    'status' => false,
                    'errors' => $errors
                );
            }
        } catch (SoapFault $fault) {
            die('Error : ' . $fault->faultstring);
        }
    }

    public function validateAddress($country_code, $city)
    {
			return array(
					'status'  => true
			);
			/*
        $wsdl_file  = $this->data_path . 'Location-API-WSDL.wsdl';
        $soapClient = new SoapClient($wsdl_file);

				//echo '<pre>'; print_r($this->connection_params); die();
        // calling the method and printing results
        try {
					$mariam_array = Array ('ClientInfo'  			=> array('AccountCountryCode' => 'JO', 'AccountEntity' => 'AMM', 'AccountNumber' => '20016', 'AccountPin' => '331421', 'UserName' => 'reem@reem.com', 'Password' => '123456789', 'Version' => 'v1', 'Source' => 24));
            $auth_call = $soapClient->FetchCountry(array_merge(
                $this->connection_params,
                array(
                    'Address' => array(
                        'Line1'			        => '001',
                        'Line2'			        => '',
                        'Line3'			        => '',
                        'City'			        => $city,
                        'StateOrProvinceCode'	=> '',
                        'PostCode'			    => '400093',
                        'CountryCode'				=>  $country_code
                    )
                )
            ));


						//print_r($auth_call);
						//echo "<br>------b----------------------<br>";

            if(!$auth_call->HasErrors) {

                return array(
                    'status'  => true
                );

            } else {
                $errors = $auth_call->Notifications->Notification;
print_r($errors); die();
                $result = array(
                    'status' => false,
                    'errors' => $errors
                );

                if($auth_call->SuggestedAddresses) {
                    $suggestedAddresses = $auth_call->SuggestedAddresses->Address;
                    $result['suggestedAddresses'] = $suggestedAddresses;
                }

                return $result;
            }
        } catch (SoapFault $fault) {
            die('Error : ' . $fault->faultstring);
        }
				*/
    }
}
