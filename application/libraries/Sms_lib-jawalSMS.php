<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Sms_lib
{
    function __construct()
    {
        $this->CI = &get_instance();
        
    }
   
   function UrlEncoding($str)
    {          
        $strResult="";
        for($i=0;$i<=strlen($str);$i++)
            {
                $strResult .= (ord(substr($str,$i,1)))."+";
             }
        return $strResult;
    }
   
    ///////////////////////////////////////////////
    //   Filter Mobile Numbers
    ///////////////////////////////////////////////
   
    function filterNumbers($partNumber)
    {
        $last = "";
        $partNumber=str_replace("00000000000000","",$partNumber);
        $Finalnum= explode(',',$partNumber);
        foreach($Finalnum as $t)
        {
            $t = trimtext($t);
            $t = ClearN($t);
            if(isInteger($t)==1){
                    if(strlen($t) > 5 &&  strlen($t) < 15){
                                  if(substr($t, 0, 2)=="00"){
                                  $t =substr($t, 2, strlen($t));
                                   }
                      $last .= $t.",";
                     }
             }// isInteger
        }// for
        $last = str_replace(",,",",",$last);
        $last = trimtext($last);
        $last = ClearN($last);
        return $last;
    }
    ///////////////////////////////////////////////
    //   Clear Numbers and messages
    ///////////////////////////////////////////////
    function ClearN($t) 
    {
        $t = str_replace(' ','',$t);
        $t = str_replace('\n','',$t);
        $t = str_replace('\r','',$t);
        $t = str_replace('"','',$t);
        $t = str_replace("'",'',$t);
        
        return $t;
    }
    
    function ClearM($t) 
    {
        $t = str_replace('\n','',$t);
        $t = str_replace('\r','',$t);
        $t = str_replace('"','',$t);
        $t = str_replace("'",'',$t);
       
        return $t;
    }
    
    ///////////////////////////////////////////////
    //   Trem sms numbers for categorisation
    ///////////////////////////////////////////////
    function trimtext($text) 
    {
        $thetext  = substr($text, 0,-1);
        $thetext2 = substr($text, -1);
        
        if($thetext2==","){
            $result = $thetext;
        }else{
            $result = $text;
        }
        
        return $result;
    }
    
    ///////////////////////////////////////////////
    //   Define integer numbers
    ///////////////////////////////////////////////
    function isInteger($number)
      {
         for($R=0;$R<=strlen($number);$R++)
          {
             if(substr($number,$R,1)>="0" && substr($number,$R,1)<="9")
                {}
             else
              {
               return 0;
              }
          }
          return 1;
      }
    
     
    
    ///////////////////////////////////////////////////
    // $name = iconv('cp1256', 'utf-8', $name);
    ///////////////////////////////////////////////////
    
    function IsCorrect($Number)
    {
        $Number = str_replace(",","",$Number);
        $Number = ClearN($Number);
        $Number = trimtext($Number);
        $Number = filterNumbers($Number);
        $Number = filterSaudiNumbers($Number);
        $Number = str_replace(",","",$Number);
        $Number = ClearN($Number);
        $Number = trimtext($Number);
        
        if(strlen($Number)!="12")
        {
            return "0";
        }else{
            return "1";
        }
    
    }
    ///////////////////////////////////////////////
    //   CURL to a remote url
    //////////////////////////////////////////////
    function GetData($url)
    {
         if(!$url || $url=="")
         {
            return "No URL";
         }else{
             $ch = curl_init();
             curl_setopt($ch, CURLOPT_URL,$url);
             curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
             curl_setopt($ch, CURLOPT_VERBOSE, 0);
             curl_setopt($ch, CURLOPT_HEADER,0);
             curl_setopt($ch, CURLE_HTTP_NOT_FOUND,1);
             //curl_setopt($ch, CURLOPT_FAILONERROR,1);
             $LastData = curl_exec ($ch);
             curl_close ($ch);
             return $LastData;
        }
    }
    
    ///////////////////////////////////////////////
    //   Get SMS Credits
    ///////////////////////////////////////////////
   function GetCredits()
   {
        global $UserName,$UserPassword;
    
    	$GatewayURL= "http://www.jawalsms.net/api/getBalance.aspx";
        $url = $GatewayURL."?username=".$UserName."&password=".$UserPassword;
    
        if($UseCURL!="1"){
        $result =$this->GetData($url);
        }else{
           if (!(@$fp =fopen($url,"r")))
           {
               $result = "0";
           }else{
             
              @$result =@fread(@$fp,50);
              @fclose(@$fp);

            }
        }
        
        @$result=str_replace(" ","",@$result);
        $SumUsersCredits = $ucre[0][0];
        return $result;
    
   }                                                                                                                            

   ///////////////////////////////////////////////
   //   Sender names
   ///////////////////////////////////////////////
  
  function sendername()
  {
      	global $UserName,$UserPassword,$Originator;
    
    	$GatewayURL="http://www.jawalsms.net/apis/users.aspx";
        $url = $GatewayURL."?code=9&username=".$UserName."&password=".$UserPassword;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
    
        // execute and return string (this should be an empty string '')
        $output = curl_exec($curl);
        curl_close($curl);
        $output = str_replace('New SMS','1',$output);
        $array=explode(",",$output);
        echo "<select size=1 name=Originator>";
        echo'<option selected value="1">Select sender name</option>';
        for($i=0;$i<count($array);$i++){
            echo'<option value="'.$array[$i].'">'.$array[$i].'</option>';
        }
        echo "</select>";
    
        return $result;
  }

  ///////////////////////////////////////////////
  //  ADD Sender names
  ///////////////////////////////////////////////
  
  function addsendername($UserName,$UserPassword,$addsender)
  {

	$GatewayURL="http://www.jawalsms.net/apis/users.aspx";
    $url = $GatewayURL."?code=2&username=".$UserName."&password=".$UserPassword."&newsender=".$addsender;

    if($UseCURL!="1")
    {
        $result =$this->GetData($url);
    }else{
        if (!(@$fp =fopen($url,"r")))
        {
           $result = "0";
        }else{
          
          @$result =@fread(@$fp,50);
          @fclose(@$fp);

        }
    }
    
    @$result=str_replace(" ","",@$result);
    $SumUsersCredits = $ucre[0][0];

    $FainalResult = $result;
	if($FainalResult=="143"){
	echo "<p class=note><b>Thanks,</b> Your Sender Name has been received,<br> Please wait until approve it by support Team then you Will see in the drop-down menu</p>";
	}elseif($FainalResult=="3104"){
	echo "<p class=note1>Parameter are missing</p>";
	}elseif($FainalResult=="3102"){
	echo "<p class=note1>Wrong Password</p>";
	}elseif($FainalResult=="443"){
	echo "<p class=note1>Sender Name Violation Rule</p>";
	}elseif($FainalResult=="444"){
	echo "<p class=note1>Sender Name exist</p>";
	}elseif($FainalResult=="3103"){
	echo "<p class=note1>User Name Don’t Exist</p>";
	}elseif($FainalResult=="3105"){
	echo "<p class=note1>Missing Parameter</p>";
	}else{
	echo "<p class=note1>Error Unknown</p>";
	}
   return $result;

   }

    //////////////////////////////////////////////////////
    //   Send SMS Messages
    //////////////////////////////////////////////////////
    
    public function IsUnicode($string)
    {
        if (strlen($string) != strlen(utf8_decode($string)))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    public function SendSms($GatewayURL, $UserName, $UserPassword, $mobile, $Originator, $SmsText)
    {
        if($this->IsUnicode($SmsText))
        {
            $SmsText = $this->ToUnicode($SmsText);
            $Ucode   = 'U';
        }
        else
        {
            $Ucode   = 'E';
        }
        
        $SmsText     = urlencode($SmsText);
       
        $url = $GatewayURL."?username=".$UserName."&password=".$UserPassword."&mobile=".$mobile."&sender=".$Originator."&message=".$SmsText."&unicode=".$Ucode;

    //	if($UseCURL!="1")
        //{
    	   $result = $this->GetData($url);
    /*	}else{
    	   
           if (!(@$fp =fopen($url,"r")))
           {
		        $result = "4";
		   }else{
    			@$result =@fread(@$fp,100);
    			@fclose(@$fp);
           }
    	}*/
    
    	@$result=(integer)str_replace(" ","",@$result);

    	$FainalResult = $result;
    	
        return $FainalResult;
    }
    
    private function ToUnicode($message)
	{
	   $encoding_array = array(
            '،' => '060C', 
            '؛' => '061B', 
            '؟' => '061F', 
            'ء' => '0621', 
            'آ' => '0622', 
            'أ' => '0623', 
            'ؤ' => '0624', 
            'إ' => '0625', 
            'ئ' => '0626', 
            'ا' => '0627', 
            'ب' => '0628', 
            'ة' => '0629', 
            'ت' => '062A', 
            'ث' => '062B', 
            'ج' => '062C', 
            'ح' => '062D', 
            'خ' => '062E', 
            'د' => '062F', 
            'ذ' => '0630', 
            'ر' => '0631', 
            'ز' => '0632', 
            'س' => '0633', 
            'ش' => '0634', 
            'ص' => '0635', 
            'ض' => '0636', 
            'ط' => '0637', 
            'ظ' => '0638', 
            'ع' => '0639', 
            'غ' => '063A', 
            'ف' => '0641', 
            'ق' => '0642', 
            'ك' => '0643', 
            'ل' => '0644', 
            'م' => '0645', 
            'ن' => '0646', 
            'ه' => '0647', 
            'و' => '0648', 
            'ى' => '0649', 
            'ي' => '064A', 
            'ـ' => '0640', 
            'ً' => '064B', 
            'ٌ' => '064C', 
            'ٍ' => '064D', 
            'َ' => '064E', 
            'ُ' => '064F', 
            'ِ' => '0650', 
            'ّ' => '0651', 
            'ْ' => '0652', 
            '!' => '0021', 
            '"' => '0022', 
            '#' => '0023', 
            '$' => '0024', 
            '%' => '0025', 
            '&' => '0026', 
            "'" => '0027', 
            '(' => '0028', 
            ')' => '0029', 
            '*' => '002A', 
            '+' => '002B', 
            ',' => '002C', 
            '-' => '002D', 
            '.' => '002E', 
            '/' => '002F', 
            '0' => '0030', 
            '1' => '0031', 
            '2' => '0032', 
            '3' => '0033', 
            '4' => '0034', 
            '5' => '0035', 
            '6' => '0036', 
            '7' => '0037', 
            '8' => '0038', 
            '9' => '0039', 
            ':' => '003A', 
            ';' => '003B', 
            '<' => '003C', 
            '=' => '003D', 
            '>' => '003E', 
            '?' => '003F', 
            '@' => '0040', 
            'A' => '0041', 
            'B' => '0042', 
            'C' => '0043', 
            'D' => '0044', 
            'E' => '0045', 
            'F' => '0046', 
            'G' => '0047', 
            'H' => '0048', 
            'I' => '0049', 
            'J' => '004A', 
            'K' => '004B', 
            'L' => '004C', 
            'M' => '004D', 
            'N' => '004E', 
            'O' => '004F', 
            'P' => '0050', 
            'Q' => '0051', 
            'R' => '0052', 
            'S' => '0053', 
            'T' => '0054', 
            'U' => '0055', 
            'V' => '0056', 
            'W' => '0057', 
            'X' => '0058', 
            'Y' => '0059', 
            'Z' => '005A', 
            '[' => '005B', 
            '\\' => '005C', 
            ']' => '005D', 
            '^' => '005E', 
            '_' => '005F', 
            '`' => '0060', 
            'a' => '0061', 
            'b' => '0062', 
            'c' => '0063', 
            'd' => '0064', 
            'e' => '0065', 
            'f' => '0066', 
            'g' => '0067', 
            'h' => '0068', 
            'i' => '0069', 
            'j' => '006A', 
            'k' => '006B', 
            'l' => '006C', 
            'm' => '006D', 
            'n' => '006E', 
            'o' => '006F', 
            'p' => '0070', 
            'q' => '0071', 
            'r' => '0072', 
            's' => '0073', 
            't' => '0074', 
            'u' => '0075', 
            'v' => '0076', 
            'w' => '0077', 
            'x' => '0078', 
            'y' => '0079', 
            'z' => '007A', 
            '{' => '007B', 
            '|' => '007C', 
            '}' => '007D', 
            '~' => '007E', 
            '©' => '00A9', 
            '®' => '00AE', 
            '÷' => '00F7', 
            '÷' => '00F7', 
            '§' => '00A7', 
            ' ' => '0020',
            "\r" => '000A',
            "\n" => '000D', 
            "\t" => '0009', 
            'é' => '00E9', 
            'ç' => '00E7', 
            'à' => '00E0', 
            'ù' => '00F9', 
            'µ' => '00B5', 
            'è' => '00E8'
        );
        
        return strtr(trim($message), $encoding_array);
        //die( strtr(trim($message), $encoding_array));
        
        /*
        $chrArray       = array_keys($encoding_array);
        $unicodeArray   = array_values($encoding_array);
        
        echo str_replace($chrArray, $unicodeArray, trim($message));
        die();
        */
	}
 
  ///////////////////////////////////////////
}