<?php
/**
 * Description of Knetm
 *
 * @author Admin
 */
class Knet
{
    public $CI, $KnetAlias, $KnetLanguage,
           $KnetResponseURL, $KnetErrorURL,
           $KnetCurrencyCode;
    public $SUCCESS = 0;
    public $FAILURE = -1;
    public $BUFFER = 2320;
    public $strIDOpen = "<id>";
    public $strPasswordOpen = "<password>";
    public $strWebAddressOpen = "<webaddress>";
    public $strPortOpen = "<port>";
    public $strContextOpen = "<context>";
    public $strIDClose = "</id>";
    public $strPasswordClose = "</password>";
    public $strWebAddressClose = "</webaddress>";
    public $strPortClose = "</port>";
    public $strContextClose = "</context>";
    public $webAddress;
    public $port;
    public $id;
    public $password;
    public $passwordHash;
    public $action;
    public $transId;
    public $amt;
    public $responseURL;
    public $trackId;
    public $udf1;
    public $udf2;
    public $udf3;
    public $udf4;
    public $udf5;
    public $paymentPage;
    public $paymentId;
    public $result;
    public $auth;
    public $ref;
    public $avr;
    public $date;
    public $currency;
    public $errorURL;
    public $language;
    public $context;
    public $resourcePath;
    public $alias;
    public $error;
    public $rawResponse;
    public $debugMsg;
    public $arr = array();

    public function __construct()
    {   
        $this->CI = &get_instance();
        $this->fetch_knet_config();
        
        $this->CI->load->model('users/countries_model');
        
        
    }
    
    private function fetch_knet_config()
    {
        $this->CI->config->load('payment_gateways');   
     
        $this->KnetAlias       = $this->CI->config->item('KnetAlias');
        $this->KnetLanguage    = $this->CI->config->item('KnetLanguage');
        $this->KnetResponseURL = $this->CI->config->item('KnetResponseURL');
        $this->KnetErrorURL    = $this->CI->config->item('KnetErrorURL');
        
        $this->webAddress = "";
        $this->port = "";
        $this->id = "";
        $this->password = "";
        $this->action = ""; // 1 = purchase
        $this->transId = "";
        $this->amt = "";
        $this->responseURL = "";
        $this->trackId = "";
        $this->udf1 = "";
        $this->udf2 = "";
        $this->udf3 = "";
        $this->udf4 = "";
        $this->udf5 = "";
        $this->paymentPage = "";
        $this->paymentId = "";
        $this->result = 0;
        $this->auth = "";
        $this->ref = "";
        $this->avr = "";
        $this->date = "";
        $this->currency = "";
        $this->errorURL = "";
        $this->language = "";
        $this->context = "";
        $this->resourcePath = "";
        $this->alias = "";
        $this->error = "";
        $this->rawResponse = "";
        $this->debugMsg = "";
    }
    
    public function execute_send_request($order_id, $amount, $notes = array())
    {
        $this->setAction(1);
        $this->setCurrency(414); //414 for KD "Kuwiti Dinar"
        $this->setLanguage($this->KnetLanguage); //change it to "ENG" for arabic language
        $this->setResponseURL($this->KnetResponseURL); // set your respone page URL
        $this->setErrorURL($this->KnetResponseURL); //set your error page URL
        //$this->setErrorURL($this->KnetErrorURL); //set your error page URL
        $this->setAmt($amount); //set the amount for the transaction
        //$this->setResourcePath("/Applications/MAMP/htdocs/php-toolkit/resource/");
        $this->setResourcePath(realpath(dirname(__FILE__)) . "/knet_resource/"); //change the path where your resource file is
        $this->setAlias($this->KnetAlias); //set your alias name here
        $this->setTrackId($order_id);//generate the random number here

        if(count($notes) > 0) $this->setUdf1($notes[0]); //set User defined value
        if(count($notes) > 1) $this->setUdf2($notes[1]); //set User defined value
        if(count($notes) > 2) $this->setUdf3($notes[2]); //set User defined value
        if(count($notes) > 3) $this->setUdf4($notes[3]); //set User defined value
        if(count($notes) > 4) $this->setUdf5($notes[4]); //set User defined value

        /*$this->setUdf1("ammar"); //set User defined value
        $this->setUdf2("alsoos"); //set User defined value
        $this->setUdf3("like4card"); //set User defined value
        $this->setUdf4("UDF 4"); //set User defined value
        $this->setUdf5("UDF 5"); //set User defined value*/
        
        if($this->performPaymentInitialization() != $this->SUCCESS){
            echo "Result=".$this->SUCCESS;
            echo "<br>".$this->getErrorMsg();
            echo "<br>".$this->getDebugMsg();
            
        } else {
            $payID = $this->getPaymentId();
            $payURL = $this->getPaymentPage();
            $udf1   = $this->getUdf1();
            
            header('Location: '.$payURL.'?PaymentID='.$payID);
            
        }
    }
    
    public function handleResponse($mode)
    {
            
        if(empty($mode) || $mode == "NOT CAPTURED")
        {
            $status = 'failure';
        }
        
        elseif($mode == 'CAPTURED')
        {
            $status = 'success';
        }
        
        return $status;
        
        //result array
        /*
         * Payment ID : 9261917162262690
            Post Date :	0926
            Result Code :	CAPTURED
            Transaction ID :	6775611172262690
            Auth :	661437
            Track ID :	3434
            Ref No :	626922838637
            UDF1 :	UDF 1
            UDF2 :	UDF 2
            UDF3 :	UDF 3
            UDF4 :	UDF 4
            UDF5 :	UDF 5
         * 
         * $PaymentID = $_POST['paymentid'];
            $presult = $_POST['result'];
            $postdate = $_POST['postdate'];
            $tranid = $_POST['tranid'];
            $auth = $_POST['auth'];
            $ref = $_POST['ref'];
            $trackid = $_POST['trackid'];
            $udf1 = $_POST['udf1'];
            $udf2 = $_POST['udf2'];
            $udf3 = $_POST['udf3'];
            $udf4 = $_POST['udf4'];
            $udf5 = $_POST['udf5'];
         */
    }

    public function getWebAddress() {
        return webAddress;
    }

    public function setWebAddress($s) {
        $this->webAddress = $s;
    }

    public function getPort() {
        return $this->port;
    }

    public function setPort($s) {
        $this->port = $s;
    }

    public function set($k, $v) {
        $this->arr[$k] = $v;
    }

    public function get($k) {
        return $this->arr[$k];
    }

    public function setId($s) {
        $this->id = $s;
    }

    public function getId() {
        return $this->id;
    }

    public function setPassword($s) {
        $this->password = $s;
    }

    public function setPasswordHash($s) {
        $this->passwordHash = $s;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getPasswordHash() {
        return $this->passwordHash;
    }

    public function setAction($s) {
        $this->action = $s;
    }

    public function getAction() {
        return $this->action;
    }

    public function setTransId($s) {
        $this->transId = $s;
    }

    public function getTransId() {
        return $this->transId;
    }

    public function setAmt($s) {
        $this->amt = $s;
    }

    public function getAmt() {
        return $this->amt;
    }

    public function setResponseURL($s) {
        $this->responseURL = $s;
    }

    public function getResponseURL() {
        return $this->responseURL;
    }

    public function setTrackId($s) {
        $this->trackId = $s;
    }

    public function getTrackId() {
        return $this->trackId;
    }

    public function setUdf1($s) {
        $this->udf1 = $s;
    }

    public function getUdf1() {
        return $this->udf1;
    }

    public function setUdf2($s) {
        $this->udf2 = $s;
    }

    public function getUdf2() {
        return $this->udf2;
    }

    public function setUdf3($s) {
        $this->udf3 = $s;
    }

    public function getUdf3() {
        return $this->udf3;
    }

    public function setUdf4($s) {
        $this->udf4 = $s;
    }

    public function getUdf4() {
        return $this->udf4;
    }

    public function setUdf5($s) {
        $this->udf5 = $s;
    }

    public function getUdf5() {
        return $this->udf5;
    }

    public function getPaymentPage() {
        return $this->paymentPage;
    }

    public function getPaymentId() {
        return $this->paymentId;
    }

    public function setPaymentId($s) {
        $this->paymentId = $s;
    }

    public function setPaymentPage($s) {
        $this->paymentPage = $s;
    }

    public function getRedirectContent() {
        return ($this->paymentPage . "&PaymentID=" . $this->paymentId);
    }

    public function getResult() {
        return $this->result;
    }

    public function getAuth() {
        return $this->auth;
    }

    public function getAvr() {
        return $this->avr;
    }

    public function getDate() {
        return $this->date;
    }

    public function getRef() {
        return $this->ref;
    }

    public function getCurrency() {
        return $this->currency;
    }

    public function setCurrency($s) {
        $this->currency = $s;
    }

    public function getLanguage() {
        return $this->language;
    }

    public function setLanguage($s) {
        $this->language = $s;
    }

    public function getErrorURL() {
        return $this->errorURL;
    }

    public function setErrorURL($s) {
        $this->errorURL = $s;
    }

    public function setContext($s) {
        $this->context = $s;
    }

    public function getResourcePath() {
        return $this->resourcePath;
    }

    public function setResourcePath($s) {
        $this->resourcePath = $s;
    }

    public function getAlias() {
        return $this->alias;
    }

    public function setAlias($s) {
        $this->alias = $s;
    }

    public function getErrorMsg() {
        return $this->error;
    }

    public function getRawResponse() {
        return $this->rawResponse;
    }

    public function getDebugMsg() {
        return $this->debugMsg;
    }

    private function performPaymentInitialization() {
        $stringbuffer = "";
        if (!$this->getSecureSettings())
            return -1;
        if (strlen($this->id) > 0)
            $stringbuffer .= ("id=" . $this->id . "&");
        if (strlen($this->password) > 0)
            $stringbuffer .= ("password=" . $this->password . "&");
        if (strlen($this->passwordHash) > 0)
            $stringbuffer.=("passwordhash=" . $this->passwordHash . "&");
        if (strlen($this->amt) > 0)
            $stringbuffer.=("amt=" . $this->amt . "&");
        if (strlen($this->currency) > 0)
            $stringbuffer.=("currencycode=" . $this->currency . "&");
        if (strlen($this->action) > 0)
            $stringbuffer.=("action=" . $this->action . "&");
        if (strlen($this->language) > 0)
            $stringbuffer.=("langid=" . $this->language . "&");
        if (strlen($this->responseURL) > 0)
            $stringbuffer.=("responseURL=" . $this->responseURL . "&");
        if (strlen($this->errorURL) > 0)
            $stringbuffer.=("errorURL=" . $this->errorURL . "&");
        if (strlen($this->trackId) > 0)
            $stringbuffer.=("trackid=" . $this->trackId . "&");
        if (strlen($this->udf1) > 0)
            $stringbuffer.=("udf1=" . $this->udf1 . "&");
        if (strlen($this->udf2) > 0)
            $stringbuffer.=("udf2=" . $this->udf2 . "&");
        if (strlen($this->udf3) > 0)
            $stringbuffer.=("udf3=" . $this->udf3 . "&");
        if (strlen($this->udf4) > 0)
            $stringbuffer.=("udf4=" . $this->udf4 . "&");
        if (strlen($this->udf5) > 0)
            $stringbuffer.=("udf5=" . $this->udf5 . "&");
        $s = $this->sendMessage($stringbuffer, "PaymentInitHTTPServlet");
        if ($s == null)
            return -1;
        $i = strpos($s, ":");
        if ($i == -1) {
            $this->error = "Payment Initialization returned an invalid response: " . $s;
            return -1;
        } else {
            $this->paymentId = substr($s, 0, $i);
            $this->paymentPage = substr($s, $i + 1);
            return 0;
        }
    }

    private function performTransaction() {
        $stringbuffer = "";
        if (!$this->getSecureSettings())
            return -1;
        if (strlen($this->id) > 0)
            $stringbuffer.=("id=" . $this->id . "&");
        if (strlen($this->password) > 0)
            $stringbuffer.=("password=" . $this->password . "&");
        if (strlen($this->passwordHash) > 0)
            $stringbuffer.=("passwordhash=" . $this->passwordHash . "&");
        if (strlen($this->currency) > 0)
            $stringbuffer.=("currencycode=" . $this->currency . "&");
        if (strlen($this->amt) > 0)
            $stringbuffer.=("amt=" . $this->amt . "&");
        if (strlen($this->action) > 0)
            $stringbuffer.=("action=" . $this->action . "&");
        if (strlen($this->paymentId) > 0)
            $stringbuffer.=("paymentid=" . $this->paymentId . "&");
        if (strlen($this->transId) > 0)
            $stringbuffer.=("transid=" . $this->transId . "&");
        if (strlen($this->trackId) > 0)
            $stringbuffer.=("trackid=" . $this->trackId . "&");
        if (strlen($this->udf1) > 0)
            $stringbuffer.=("udf1=" . $this->udf1 . "&");
        if (strlen($this->udf2) > 0)
            $stringbuffer.=("udf2=" . $this->udf2 . "&");
        if (strlen($this->udf3) > 0)
            $stringbuffer.=("udf3=" . $this->udf3 . "&");
        if (strlen($this->udf4) > 0)
            $stringbuffer.=("udf4=" . $this->udf4 . "&");
        if (strlen($this->udf5) > 0)
            $stringbuffer.=("udf5=" . $this->udf5 . "&");

        if (is_array($this->arr) && count($this->arr)) {
            foreach ($this->arr as $key => $var) {
                $stringbuffer .= ($key . "=" . $var . "&");
            }
        }
        $stringbuffer = substr($stringbuffer, 0, strlen($stringbuffer) - 1);
        
        $s = $this->sendMessage($stringbuffer, "PaymentTranHTTPServlet");
        if ($s == null)
            return -1;
        
        $arraylist = $this->parseResults($s);
        if ($arraylist == null) {
            return -1;
        } else {
            $this->result = $arraylist[0];
            $this->auth = $arraylist[1];
            $this->ref = $arraylist[2];
            $this->avr = $arraylist[3];
            $this->date = $arraylist[4];
            $this->transId = $arraylist[5];
            $this->trackId = $arraylist[6];
            $this->udf1 = $arraylist[7];
            $this->udf2 = $arraylist[8];
            $this->udf3 = $arraylist[9];
            $this->udf4 = $arraylist[10];
            $this->udf5 = $arraylist[11];
            return 0;
        }
    }

    private function sendMessage($s, $s1) {
        $stringbuffer = "";
        $error = "";
        $this->debugMsg .= ("<br>---------- " . $s1 . ": " . time() . " ---------- <br>");
        if ($this->port == "443") {
            if (strlen($this->webAddress) <= 0) {
                $error = "No URL specified.";
                return null;
            }
            if ($this->port == "443")
                $stringbuffer.=("https://");
            else
                $stringbuffer.=("http://");
            $stringbuffer.=($this->webAddress);
            if (strlen($this->port) > 0) {
                $stringbuffer.=(":");
                $stringbuffer.=($this->port);
            }
            if (strlen($this->context) > 0) {
                if (!$this->StartsWith($this->context, "/"))
                    $stringbuffer.=("/");
                $stringbuffer.=($this->context);
                if (!$this->EndsWith($this->context, "/"))
                    $stringbuffer.=("/");
            } else {
                $stringbuffer.=("/");
            }
            $stringbuffer.=("servlet/");
            $stringbuffer.=($s1);
            $this->debugMsg.=("<br>About to create the URL to: " . $stringbuffer);
            $url = $stringbuffer;
            //echo '<br>'. $stringbuffer . '<br>';
            $this->debugMsg.=("<br>About to create http connection....");

            $this->debugMsg.=("<br>Created connection.!!");
            if (strlen($s) > 0) {
                $c = curl_init();
				curl_setopt($c, CURLOPT_HEADER, 0);
				curl_setopt($c, CURLOPT_URL, $stringbuffer);
                curl_setopt($c, CURLOPT_POST, true);
                curl_setopt($c, CURLOPT_POSTFIELDS, $s);
                curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 2);
                curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
                $this->debugMsg.=("<br>about to write DataOutputSteam....");
                curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
				$this->debugMsg.=("<br>after DataOutputStream.!!");
                $tmp = curl_exec($c);
                if (curl_error($c)) {
                    echo 'CURL ERROR: ' . curl_errno($c) . '::' . curl_error($c);
                } elseif ($tmp) {

                    curl_close($c);
                    $this->rawResponse = $tmp;
                    $this->debugMsg.=("<br>Received RESPONSE: " . $this->rawResponse);
                    return $this->rawResponse;
                } else {
                    $error = "No Data To Post!";
                }
            } else {
                $this->clearFields();
                $this->error = "Failed to make connection:\n" . $error;  //. $exception;
                return null;
            }
        }
    }

    private function parseResults($s) {

        $arraylist = array(); {
            if ($this->StartsWith($s, "!ERROR!")) {
                $this->error = $s;
                return null;
            }

            $tokens = strtok($s, ":\r\n");
            print_r($tokens);
            $s1;
            $flag = false;
            foreach ($tokens as $token) {
                $s2 = $token;
                if (!$s2 . $this->startsWith(":")) {
                    $arraylist[] = ($s2);
                    $flag = false;
                } else {
                    if ($flag)
                        $arraylist[] = ("");
                    $flag = true;
                }
            }
            return $arraylist;
        }

        $this->error = "Internal Error!";
        return null;
    }

    private function clearFields() {
        $this->error = "";
        $this->paymentPage = "";
        $this->paymentId = "";
    }

    public function getSecureSettings() {
        $s = "";
        if (!$this->createReadableZip())
            return false;
        $s = $this->readZip();
        if ($s == "")
            return false;

        unlink($this->getResourcePath() . "resource.cgz");
        return $this->parseSettings($s);
    }

    private function createReadableZip() { {

            $filenameInput = $this->getResourcePath() . "resource.cgn";
            $handleInput = fopen($filenameInput, "r");
            $contentsInput = fread($handleInput, filesize($filenameInput));

            $filenameOutput = $this->getResourcePath() . "resource.cgz";
            @unlink($filenameOutput);
            $handleOutput = fopen($filenameOutput, "w");

            $inByteArray = $this->getBytes($contentsInput);
            $outByteArray = $this->simpleXOR($inByteArray);
		
            fwrite($handleOutput, $this->getString($outByteArray));
            fclose($handleInput);
            fclose($handleOutput);
        }

        return true;
    }

    private function readZip() {

        $s = ""; {

            $filenameInput = $this->getResourcePath() . "resource.cgz";

            $zipentry;
            $i = 0;

            $zip = new ZipArchive;
            if ($zip->open($filenameInput) === TRUE) {
                
                $zip->extractTo($this->resourcePath);
                $zip->close();
            } else {
                echo 'failed';
                $this->error = "Failed to unzip file";
            }

            if (strlen($this->error) === 0) {
                $xmlNameInput = $this->resourcePath . $this->getAlias() . ".xml";
                $xmlHandleInput = fopen($xmlNameInput, "r");
                $xmlContentsInput = fread($xmlHandleInput, filesize($xmlNameInput));
                fclose($xmlHandleInput);
                unlink($xmlNameInput);
                $s = $xmlContentsInput;

                $s = $this->getString($this->simpleXOR($this->getBytes($s)));
            } else {
                $this->error = "Unable to open resource";
            }
            return $s;
        }
    }

    private function parseSettings($s) {
        $i = 0;
        $j = 0;
        $i = strpos($s, "<id>") + strlen("<id>");
        $j = strpos($s, "</id>");

        $this->setId(substr($s, $i, $j - $i));
        $i = strpos($s, "<password>") + strlen("<password>");
        $j = strpos($s, "</password>");
        $this->setPassword(substr($s, $i, $j - $i));

        $i = strpos($s, "<passwordhash>") + strlen("<passwordhash>");
        $j = strpos($s, "</passwordhash>");
        $this->setPasswordHash(substr($s, $i, $j - $i));

        $i = strpos($s, "<webaddress>") + strlen("<webaddress>");
        $j = strpos($s, "</webaddress>");
        $this->setWebAddress(substr($s, $i, $j - $i));
        $i = strpos($s, "<port>") + strlen("<port>");
        $j = strpos($s, "</port>");
        $this->setPort(substr($s, $i, $j - $i));
        $i = strpos($s, "<context>") + strlen("<context>");
        $j = strpos($s, "</context>");
        $this->setContext(substr($s, $i, $j - $i));
        return true;
    }

    private function simpleXOR($abyte0) {
        $key = "Those who profess to favour freedom and yet depreciate agitation are men who want rain without thunder and lightning";
        $abyte1 = $this->getBytes($key);

        for ($i = 0; $i < sizeof($abyte0);) {
            for ($j = 0; $j < sizeof($abyte1); $j++) {
                $abyte2[$i] = ($abyte0[$i] ^ $abyte1[$j]);
                if (++$i == sizeof($abyte0))
                    break;
            }
        }

        return $abyte2;
    }

    public function getBytes($s) {
        $hex_ary = array();
        $size = strlen($s);
        for ($i = 0; $i < $size; $i++)
            $hex_ary[] = chr(ord($s[$i]));
        return $hex_ary;
    }

    public function getString($byteArray) {
        $s = "";
        foreach ($byteArray as $byte) {
            $s .=$byte;
        }
        return $s;
    }

    private function StartsWith($Haystack, $Needle) {
        // Recommended version, using strpos
        return strpos($Haystack, $Needle) === 0;
    }

    private function EndsWith($Haystack, $Needle) {
        // Recommended version, using strpos
        return strrpos($Haystack, $Needle) === strlen($Haystack) - strlen($Needle);
    }
    
    public function xor_string($string) {
        $buf = '';
        $size = strlen($string);
        for ($i = 0; $i < $size; $i++)
            $buf .= chr(ord($string[$i]) ^ 255);
        return $buf;
    }
}
