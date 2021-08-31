<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Sms_lib
{
    function __construct()
    {
        $this->CI = &get_instance();

    }

    /**
     * ////////////////////////
     * // Unifonic SMS LIBRARY  //
     * ////////////////////////
     */


    //////////////////////////////////////////////////////
    //   Send SMS Messages
    //////////////////////////////////////////////////////

    public function SendSms($UserName, $UserPassword, $mobile, $Originator, $SmsText)
    {
        $post_fields = "AppSid=&Recipient=$mobile&Body=$SmsText&SenderID=$Originator&encoding=UTF8";

        $result = $this->sendRequest($post_fields);
        $result = json_decode($result);
        //echo '<pre>'; print_r($result); die();
        if(isset($result->success))
        {
            return true;
        }
        else
        {
            return false;
        }

    }

    ///////////////////////////////////////////////
    //   CURL to a remote url
    //////////////////////////////////////////////

    function sendRequest($post_fields)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "http://api.unifonic.com/rest/Messages/Send");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);

        curl_setopt($ch, CURLOPT_POST, TRUE);

        curl_setopt($ch, CURLOPT_POSTFIELDS,$post_fields);// "AppSid=Add Yor Own AppSid&Recipient=966555000000,966555000000&Body=Welcome&SenderID=unifonic");

        $headers[] = 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8;';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        curl_close($ch);
        //echo '<pre>';print_r($response); die();
        return $response;

    }




  ///////////////////////////////////////////
}
