<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Sms_lib
{
    function __construct()
    {
        $this->CI = &get_instance();

    }

    //دالة فحص  حالة الإرسال بإستخدام بوابة CURL
    function sendStatus($viewResult=1)
    {
    	global $arraySendStatus;
    	$url = "www.mobily.ws/api/sendStatus.php";
    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($ch, CURLOPT_HEADER, 0);
    	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    	$result = curl_exec($ch);

    	if($viewResult)
    		$result = $this->printStringResult(trim($result) , $arraySendStatus);
    	return $result;
    }

    //دالة تغيير كلمة المرور لحساب الإرسال في موقع موبايلي بإستخدام بوابة  CURL
    function changePassword($userAccount, $passAccount, $newPassAccount, $viewResult=1)
    {
    	global $arrayAddAlphaSender;
    	$url = "www.mobily.ws/api/changePassword.php";
    	$stringToPost = "mobile=".$userAccount."&password=".$passAccount."&newPassword=".$newPassAccount;

    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($ch, CURLOPT_HEADER, 0);
    	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    	curl_setopt($ch, CURLOPT_POST, 1);
    	curl_setopt($ch, CURLOPT_POSTFIELDS, $stringToPost);
    	$result = curl_exec($ch);

    	if($viewResult)
    		$result = $this->printStringResult(trim($result) , $arrayChangePassword);
    	return $result;
    }

    //دالة إسترجاع كلمة المرور لحساب الإرسال في موقع موبايلي بإستخدام بوابة  CURL
    function forgetPassword($userAccount, $sendType, $viewResult=1)
    {
    	global $arrayForgetPassword;
    	$url = "http://www.mobily.ws/api/forgetPassword.php";
    	$stringToPost = "mobile=".$userAccount."&type=".$sendType;
    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($ch, CURLOPT_HEADER, 0);
    	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    	curl_setopt($ch, CURLOPT_POST, 1);
    	curl_setopt($ch, CURLOPT_POSTFIELDS, $stringToPost);
    	$result = curl_exec($ch);

    	if($viewResult)
    		$result = $this->printStringResult(trim($result) , $arrayForgetPassword);
    	return $result;
    }

    //دالة عرض الرصيد بإستخدام بوابة CURL
    function balanceSMS($userAccount, $passAccount, $viewResult=1)
    {
    	global $arrayBalance;
    	$url = "http://www.mobily.ws/api/balance.php";
    	$stringToPost = "mobile=".$userAccount."&password=".$passAccount;

    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($ch, CURLOPT_HEADER, 0);
    	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    	curl_setopt($ch, CURLOPT_POST, 1);
    	curl_setopt($ch, CURLOPT_POSTFIELDS, $stringToPost);
    	$result = curl_exec($ch);

    	if($viewResult)
    		$result = $this->printStringResult(trim($result), $arrayBalance, 'Balance');
    	return $result;
    }

    public function SendSms($GatewayURL, $userAccount, $passAccount, $numbers, $sender, $msg, $MsgID=0, $timeSend=0, $dateSend=0, $deleteKey=0, $viewResult=1)
    {
        $MsgID = 0;//rand(1, 9999);
      	global $arraySendMsg;
      	$url = $GatewayURL; //"http://www.oursms.net/api/sendsms.php";
      	$applicationType = "68";
        $sender = urlencode($sender);
      	$domainName = $_SERVER['SERVER_NAME'];

        $stringToPost = "username=$userAccount&password=$passAccount&numbers=$numbers&message=$msg&sender=$sender&unicode=E&return=full";

      	$ch = curl_init();
      	curl_setopt($ch, CURLOPT_URL, $url);
      	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      	curl_setopt($ch, CURLOPT_HEADER, 0);
      	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
      	curl_setopt($ch, CURLOPT_POST, 1);
      	curl_setopt($ch, CURLOPT_POSTFIELDS, $stringToPost);
      	$result = curl_exec($ch);
        return true;
//print_r($result);die();
      	//if($viewResult)
      		//$result = $this->printStringResult(trim($result) , $arraySendMsg);

      	//return $result;
    }

    //دالة الإرسال بإستخدام بوابة CURL
    public function SendSms_old($GatewayURL, $userAccount, $passAccount, $numbers, $sender, $msg, $MsgID=0, $timeSend=0, $dateSend=0, $deleteKey=0, $viewResult=1)
    {
        $MsgID = 0;//rand(1, 9999);
    	global $arraySendMsg;
    	$url = $GatewayURL;
    	$applicationType = "68";
        $msg = $msg;
    	$sender = urlencode($sender);
    	$domainName = $_SERVER['SERVER_NAME'];
    	$stringToPost = "mobile=".$userAccount."&password=".$passAccount."&numbers=".$numbers."&sender=".$sender."&msg=".$msg."&timeSend=".$timeSend."&dateSend=".$dateSend."&applicationType=".$applicationType."&domainName=".$domainName."&msgId=".$MsgID."&deleteKey=".$deleteKey."&lang=3";
        //mobile=" + UserName + "&password=" + Password + "&numbers=" + MobileNo + "&sender=" + SenderName + "&msg=" + ConvertToUnicode(Msg) + "&applicationType=24&msgId=0";

    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($ch, CURLOPT_HEADER, 0);
    	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    	curl_setopt($ch, CURLOPT_POST, 1);
    	curl_setopt($ch, CURLOPT_POSTFIELDS, $stringToPost);
    	$result = curl_exec($ch);

    	if($viewResult)
    		//$result = $this->printStringResult(trim($result) , $arraySendMsg);

    	return $result;
    }

    //دالة قالب الإرسال بإستخدام بوابة CURL
    function sendSMSWK($userAccount, $passAccount, $numbers, $sender, $msg, $msgKey, $MsgID, $timeSend=0, $dateSend=0, $deleteKey=0, $viewResult=1)
    {
    	global $arraySendMsgWK;
    	$url = "www.mobily.ws/api/msgSendWK.php";
    	$applicationType = "68";
        $msg = $msg;
    	$msgKey = $msgKey;
    	$sender = urlencode($sender);
    	$domainName = $_SERVER['SERVER_NAME'];
    	$stringToPost = "mobile=".$userAccount."&password=".$passAccount."&numbers=".$numbers."&sender=".$sender."&msg=".$msg."&msgKey=".$msgKey."&timeSend=".$timeSend."&dateSend=".$dateSend."&applicationType=".$applicationType."&domainName=".$domainName."&msgId=".$MsgID."&deleteKey=".$deleteKey."&lang=3";

    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($ch, CURLOPT_HEADER, 0);
    	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    	curl_setopt($ch, CURLOPT_POST, 1);
    	curl_setopt($ch, CURLOPT_POSTFIELDS, $stringToPost);
    	$result = curl_exec($ch);

    	if($viewResult)
    		$result = $this->printStringResult(trim($result) , $arraySendMsgWK);
    	return $result;
    }

    //دالة حذف الرسائل بإستخدام بوابة CURL
    function deleteSMS($userAccount, $passAccount, $deleteKey=0, $viewResult=1)
    {
    	global $arrayDeleteSMS;
    	$url = "www.mobily.ws/api/deleteMsg.php";
    	$stringToPost = "mobile=".$userAccount."&password=".$passAccount."&deleteKey=".$deleteKey;

    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($ch, CURLOPT_HEADER, 0);
    	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    	curl_setopt($ch, CURLOPT_POST, 1);
    	curl_setopt($ch, CURLOPT_POSTFIELDS, $stringToPost);
    	$result = curl_exec($ch);

    	if($viewResult)
    		$result = $this->printStringResult(trim($result) , $arrayDeleteSMS);
    	return $result;
    }

    //دالة طلب إسم مرسل (جوال) بإستخدام بوابة CURL
    function addSender($userAccount, $passAccount, $sender, $viewResult=1)
    {
    	global $arrayAddSender;
    	$url = "www.mobily.ws/api/addSender.php";
    	$stringToPost = "mobile=".$userAccount."&password=".$passAccount."&sender=".$sender;

    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($ch, CURLOPT_HEADER, 0);
    	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    	curl_setopt($ch, CURLOPT_POST, 1);
    	curl_setopt($ch, CURLOPT_POSTFIELDS, $stringToPost);
    	$result = curl_exec($ch);

    	if($viewResult)
    		$result = $this->printStringResult(trim($result), $arrayAddSender, 'Normal');
    	return $result;
    }

    //دالة تفعيل إسم مرسل (جوال) بإستخدام بوابة CURL
    function activeSender($userAccount, $passAccount, $senderId, $activeKey, $viewResult=1)
    {
    	global $arrayActiveSender;
    	$url = "www.mobily.ws/api/activeSender.php";
    	$stringToPost = "mobile=".$userAccount."&password=".$passAccount."&senderId=".$senderId."&activeKey=".$activeKey;

    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($ch, CURLOPT_HEADER, 0);
    	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    	curl_setopt($ch, CURLOPT_POST, 1);
    	curl_setopt($ch, CURLOPT_POSTFIELDS, $stringToPost);
    	$result = curl_exec($ch);

    	if($viewResult)
    		$result = $this->printStringResult(trim($result) , $arrayActiveSender);
    	return $result;
    }

    //دالة التحقق من حالة طلب إسم مرسل (جوال) بإستخدام بوابة CURL
    function checkSender($userAccount, $passAccount, $senderId, $viewResult=1)
    {
    	global $arrayCheckSender;
    	$url = "www.mobily.ws/api/checkSender.php";
    	$stringToPost = "mobile=".$userAccount."&password=".$passAccount."&senderId=".$senderId;

    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($ch, CURLOPT_HEADER, 0);
    	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    	curl_setopt($ch, CURLOPT_POST, 1);
    	curl_setopt($ch, CURLOPT_POSTFIELDS, $stringToPost);
    	$result = curl_exec($ch);

    	if($viewResult)
    		$result = $this->printStringResult(trim($result) , $arrayCheckSender);
    	return $result;
    }

    //دالة طلب إسم مرسل (أحرف) بإستخدام بوابة CURL
    function addAlphaSender($userAccount, $passAccount, $sender, $viewResult=1)
    {
    	global $arrayAddAlphaSender;
    	$url = "www.mobily.ws/api/addAlphaSender.php";
    	$stringToPost = "mobile=".$userAccount."&password=".$passAccount."&sender=".$sender;

    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($ch, CURLOPT_HEADER, 0);
    	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    	curl_setopt($ch, CURLOPT_POST, 1);
    	curl_setopt($ch, CURLOPT_POSTFIELDS, $stringToPost);
    	$result = curl_exec($ch);

    	if($viewResult)
    		$result = $this->printStringResult(trim($result) , $arrayAddAlphaSender);
    	return $result;
    }

    //دالة التحقق من حالة طلب إسم مرسل (أحرف) بإستخدام بوابة CURL
    function checkAlphasSender($userAccount, $passAccount, $viewResult=1)
    {
    	global $arrayCheckAlphasSender;
    	$url = "www.mobily.ws/api/checkAlphasSender.php";
    	$stringToPost = "mobile=".$userAccount."&password=".$passAccount;

    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($ch, CURLOPT_HEADER, 0);
    	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    	curl_setopt($ch, CURLOPT_POST, 1);
    	curl_setopt($ch, CURLOPT_POSTFIELDS, $stringToPost);
    	$result = curl_exec($ch);

    	if($viewResult)
    		$result = $this->printStringResult(trim($result) , $arrayCheckAlphasSender, 'Senders');
    	return $result;
    }

    //لطباعة القيمة الناتجه من بوابة الإرسال على شكل نص
    public function printStringResult($apiResult, $arrayMsgs, $printType = 'Alpha')
    {
    	global $undefinedResult;
    	switch ($printType)
    	{
    		case 'Alpha':
    		{
    			if(array_key_exists($apiResult, $arrayMsgs))
    				return $arrayMsgs[$apiResult];
    			else
    				return $arrayMsgs[0];
    		}
    		break;

    		case 'Balance':
    		{
    			if(array_key_exists($apiResult, $arrayMsgs))
    				return $arrayMsgs[$apiResult];
    			else
    			{
    				list($originalAccount, $currentAccount) = explode("/", $apiResult);
    				if(!empty($originalAccount) && !empty($currentAccount))
    				{
    					return sprintf($arrayMsgs[3], $currentAccount, $originalAccount);
    				}
    				else
    					return $arrayMsgs[0];
    			}
    		}
    		break;

    		case 'Senders':
    		{
    			$apiResult = str_replace('[pending]', '[pending]<br>', $apiResult);
    			$apiResult = str_replace('[active]', '<br>[active]<br>', $apiResult);
    			$apiResult = str_replace('[notActive]', '<br>[notActive]<br>', $apiResult);
    			return $apiResult;
    		}
    		break;

    		case 'Normal':
    			if($apiResult{0} != '#')
    				return $arrayMsgs[$apiResult];
    			else
    				return $apiResult;
    		break;
    	}
    }
}
