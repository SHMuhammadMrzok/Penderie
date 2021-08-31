<?php

class Send_sms extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }
    function index()
    {
        $this->load->library('sms_lib');
       
        //////////////////////////////////////////////////////
        //   Variables Decleration.
        //////////////////////////////////////////////////////
        
        $GatewayURL   = "http://www.jawalsms.net/httpSmsProvider.aspx";
        $UserName     = 'alsoos';
        //$UserName     = 'computerhouse';
        //$UserPassword = '1346798520';
        $UserPassword = '1346798520';
        $sender_name  = 'Daleel'; // (computerhouse ,Daleel )
        $Ucode        = 'U'; // U for unicode , E for english
        $msg = "In The Name Of Allah";
        
        /////////////////////////
        
        $mobile = '201014193774';
       
        ////////////////////////////////
        
        $FainalResult = $this->sms_lib->SendSms($GatewayURL,$UserName,$UserPassword,$mobile,$sender_name,$msg,$Ucode);
        
        if($FainalResult=="0")
        {
    	  echo "<p class=note>Thanks, Your Message has Sent successfully</p>";
           return true;
    	
        }else{
            echo "<p class=note>$FainalResult</p>";
           return false;
        }
        /*elseif($FainalResult=="101")
        {
    	   echo "<p class=note1>Parameter are missing</p>";
    	
        }elseif($FainalResult=="104")
        {
    	   echo "<p class=note1>Either user name or password are missing or your Account is on hold</p>";
    	
        }elseif($FainalResult=="105")
        {
    	   echo "<p class=note1>Credit are not available</p>";
        
        }elseif($FainalResult=="106")
        {
    	   echo "<p class=note1>Wrong Unicodeá</p>";
    	
        }elseif($FainalResult=="107")
        {
    	   echo "<p class=note1>Blocked Aender Name</p>";
        
        }elseif($FainalResult=="108")
        {
    	   echo "<p class=note1>Missing sender name</p>";
    	
        }else{
    	   echo "<p class=note1>Error Unknown</p>";
    	}*/
    }
}
