<?php if(! defined('BASEPATH')) exit('No direct script access allowed');

class Smsa
{
    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->config->load('shipping_gateways');
        $this->CI->load->library('Gateways');

        $this->passKey = $this->CI->gateways->get_gateway_field_value('smsa_key');//$this->CI->config->item('passKey');
    }


    Public Function addShipment($refNo, $sent_date, $cName, $cntry, $cCity, $cMobile, $cAddr1, $shipType, $PCs, $cEmail,
                                $carrValue, $carrCurr, $codAmt, $weight, $itemDesc)
    {
        #Add Shipment without Shipper and delivery details
        
        //$passkey = 'Faizmubarak$2296';
        
        //$cName = 'Smsa';
        
        //echo $refNo;die();
        
        $params = array(
                          'passKey'     => $this->passKey,
                          'refNo'       => $refNo,
                          'sentDate'    => $sent_date,//date('d / m / Y')
                          'idNo'        => $refNo,
                          'cName'       => $cName,
                          'cntry'       => $cntry,
                          'cCity'       => $cCity,
                          'cZip'        => '',
                          'cPOBox'      => '',
                          'cMobile'     => $cMobile,
                          'cTel1'       => $cMobile,
                          'cTel2'       => '',
                          'cAddr1'      => $cAddr1,
                          'cAddr2'      => '',
                          'shipType'    => $shipType,
                          'PCs'         => $PCs,
                          'cEmail'      => $cEmail,
                          'carrValue'   => '',
                          'carrCurr'    => '',
                          'codAmt'      => $codAmt,
                          'weight'      => $weight,
                          'custVal'     => '',
                          'custCurr'    => '',
                          'insrAmt'     => '',
                          'insrCurr'    => '',
                          'itemDesc'    => $itemDesc
		      );
        
        //echo"<pre>";print_r($params);die();
        
        $method = 'addShipment';
        $result = $this->CallAPI($params, $method);

        $result_object = $method.'Result';

        if(is_numeric($result->$result_object))
        {
            $result = $result->$result_object;
        }
        else
        {
            $result = array(
                            'response' => 0,
                            'message'  => $result->$result_object
                           );
        }

        return $result;

    }

    Public Function addShip( $refNo, $cName, $cntry, $cCity, $cMobile, $cAddr1, $shipType, $PCs, $cEmail, $codAmt,
                             $weight, $custVal, $custCurr, $itemDesc, $sName, $sContact, $sAddr1, $sCity, $sPhone, $sCntry)
    {

        /**
         * SENT Variables
         *
         * $passKey     => Unique Code for each Customer provided by SMSA
         * $refNo       => Unique Number  for each day
         * $sentDate    => Date
         * $idNo        => Optional
         * $cName       => Cannot be Null
         * $cntry       => KSA
         * $cCity       => Destination City Name
         * $cZip        => Optional Zip Code
         * $cPOBox      => Optional POBox
         * $cMobile     => Must be at least 9 digits
         * $cTel1       => Optional
         * $cTel2       => Optional
         * $cAddr1      => Either of Address fields to be filled duly
         * $cAddr2      => Address Line 2
         * $shipType    => Mandatory Value from DLV,VAL,HAL or BLT
         * $PCs         => No. of Pieces
         * $cEmail      => Optional
         * $carrValue   => Optional	Carraige Value
         * $carrCurr    => Optional	Carraige Currency
         * $codAmt      => (Required if CASH ON DELIVERY) Value Either 0 or greater than 0 in case of COD
         * $weight      => Weight of the Shipment
         * $custVal     => Optional	Customs Value
         * $custCurr    => Optional	Customs Currency
         * $insrAmt     => Optional	Insurance Value
         * $insrCurr    => Optional	Insurance Currency
         * $itemDesc    => Optional	Description of the items present in shipment
         * $sName       => Mandatory Shipper Name
         * $sContact    => Mandatory Shipper Contact name
         * $sAddr1      => Mandatory Shipper Address
         * $sAddr2      => Optional	Shipper Address
         * $sCity       => Mandatory Shipper City
         * $sPhone      => Mandatory Shipper Phone number
         * $sCntry      => Mandatory Shipper country
         * $prefDelvDate=> Optional	Preffered Delivery date in case of future or delayed delivery
         * $gpsPoints   => Optional	Google GPS points separated by comma for delivery to customer by google maps
         *
         */

         $params = array(
                          'passKey'     => $this->passKey,
                          'refNo'       => $refNo,
                          'sentDate'    => date('d / m / Y'),
                          'idNo'        => $refNo,
                          'cName'       => $cName,
                          'cntry'       => $cntry,
                          'cCity'       => $cCity,
                          'cZip'        => '',
                          'cPOBox'      => '',
                          'cMobile'     => $cMobile,
                          'cTel1'       => '',
                          'cTel2'       => '',
                          'cAddr1'      => $cAddr1,
                          'cAddr2'      => '',
                          'shipType'    => $shipType,
                          'PCs'         => $PCs,
                          'cEmail'      => $cEmail,
                          'carrValue'   => '',
                          'carrCurr'    => '',
                          'codAmt'      => $codAmt,
                          'weight'      => $weight,
                          'custVal'     => $custVal,
                          'custCurr'    => $custCurr,
                          'insrAmt'     => '',
                          'insrCurr'    => '',
                          'itemDesc'    => $itemDesc,
                          'sName'       => $sName,
                          'sContact'    => $sContact,
                          'sAddr1'      => $sAddr1,
                          'sAddr2'      => '',
                          'sCity'       => $sCity,
                          'sPhone'      => $sPhone,
                          'sCntry'      => $sCntry,
                          'prefDelvDate'=> '',
                          'gpsPoints'   => ''
		      );

        $method = 'addShip';

        $result = $this->CallAPI($params, $method);

        /**
         * #success result:
         * stdClass Object ( [addShipResult] => 290006690560 )
         *
         * #failed result :
         * stdClass Object ( [addShipResult] => Failed :: Duplicate Shipment Information 13 )
        */
    }

    public function getTracking($awbNo)
    {
        $passKey = $this->passKey;

        $params = array(
                            'awbNo'     => $awbNo,
                            'passkey'   => $this->passKey
                        );

        $method = 'getTracking';
        $result = $this->CallAPI($params, $method);

        if(count((array)$result) != 0)
        {
            $result = $result->Tracking;
        }
        else
        {
            $result = array(
                                'response' => 0,
                                'message'  => 'Failed'
                            );
        }

        return $result;

        /**
         * # result
         * stdClass Object
            (
                [getTrackingResult] => stdClass Object
                    (
                        [schema] =>
                        [any] => 29000669108515 Jun 2017 14:59DATA RECEIVED
            Details
            Riyadh
                    )

            )
            */
    }


    Public Function cancelShipment($awbNo, $reason)
    {
        $params = array(
                            'awbNo'     => $awbNo,
                            'passkey'   => $this->passKey,
                            'reas'      => $reason
                       );

        $method = 'cancelShipment';

        $result = $this->CallAPI($params, $method);
        $result_object = $method.'Result';

        return $result->$result_object;

        /**
         * #result
         * stdClass Object ( [cancelShipmentResult] => Success Cancellation )
         *
         */
    }

    Public Function getRTLCities()
    {
        $params = array(
                        'passkey' => $this->passKey
                       );

        $method = 'getRTLCities';
        $result = $this->CallAPI($params, $method);

        return $result;

       /**
        * Result:
        * stdClass Object
            (
                [getRTLCitiesResult] => stdClass Object
                    (
                        [schema] =>
                        [any] => ABT       Al-BahaABT       BaljurashiABT       MakhwahABT       QunfudahAFIF
                        AfifAJF       Dawmat Al JandalAJF       SkakahANS       NamassANS       Sapt Al OlayaANS
                        TanumahDMM       Al Qarya Al Ulya DMM       An NairiyahDMM       BuqaiqDMM       DammamDMM
                        DhahranDMM       JubailDMM       KhafjiDMM       KhobarDMM       QatifDMM
                        Ras TannurahDMM       SafwaDMM       SayhatEAM       NajranEAM       SharourahEAM
                        Wadi Al-Dawasir ( Khamasin )ELQ       Al BukayriyahELQ       Ar RassELQ       BuraydahELQ
                        MidhnabELQ       Riyadh Al KhabraELQ       SajirELQ       UnayzahGIZ       Abu ArishGIZ
                        Ad DarbGIZ       Al DayerGIZ       At Tuwal (Aratawiyah)GIZ       GizanGIZ       SabyaGIZ
                        SamthaHAS       HailHBT       Hafar Al BatenHBT       Hafer al BatenHBT       RafhaHOF
                        Al HufufHOF       IhsaJED       JeddahJED       RabighKMT       AbhaKMT       BishahKMT
                        Dhahran Al-JanoubKMT       Khamis MushaitKMT       MajardahKMT       Muhayyil AssirKMT
                        Raj'l AlmaaKMT       Sarat AbidaKMT       TathlithMAK       MakkahMED       Al UlaMED
                        MadinahMJM       Ad DuwadimiMJM       Al Majma'hMJM       Az ZulfiMJM       ShaqraRAE
                        ArarRUH       Al Aflaj (Layla)RUH       Al QuwayiyadhRUH       KharjRUH       MuzamiyahRUH
                        RiaydhRUH       RiyadhTIF       Al KhurmahTIF       RanyahTIF       TaifTIF       TurbahTUU
                        DhubaTUU       TabukTUU       TaimaURY       Al-QurayyatURY       TabarjalURY       TuraifWAE
                        SulayyilYNB       UmmlujjYNB       Yanbu
                    )

            )
            */
    }

    Public Function getRTLRetails($cityCode)
    {
        $params = array(
                        'passkey'   => $this->passKey,
                        'cityCode'  => $cityCode
                       );

        $method = 'getRTLRetails';

        $result = $this->CallAPI($params, $method);
        return $result;
    }

    Public function getStatus($awbNo)
    {
        //This method can be used to get the realtime Status information of the shipment.

        $params = array(
                        'passkey'   => $this->passKey,
                        'awbNo'     => $awbNo
                       );

        $method = 'getStatus';

        $result = $this->CallAPI($params, $method);

        return $result;
    }

    Public function getStatusByRef($refNo)
    {
        //This method can be used to get the realtime Status information of the shipment.

        $params = array(
                        'passkey'   => $this->passKey,
                        'refNo'     => $refNo
                       );

        $method = 'getStatusByRef';

        $result = $this->CallAPI($params, $method);

        return $result;
    }

    Public function getPDF($awbNo)
    {
        //This method can be used to get the AWB Copy in PDF format for printing and labeling on shipment.

        $params = array(
                        'passkey'   => $this->passKey,
                        'awbNo'     => $awbNo
                       );

        $method = 'getPDF';

        $result = $this->CallAPI($params, $method);

        return $result;
    }

    public function CallAPI($params, $method)
    {
        $client = new SoapClient('http://track.smsaexpress.com/seCOM/SMSAwebService.asmx?WSDL', array('trace'=>true));
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

    public function handel_status($status)
    {
        if($status == 'DATA RECEIVED' )
        {
            $status = 'delivered';
        }
        elseif($status == 'Success Cancellation ')
        {
            $status = 'canceled';
        }
        else
        {
            $status = 'invalid';
        }

        return $status;
    }


}
