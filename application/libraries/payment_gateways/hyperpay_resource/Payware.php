<?php
class PayWare{
    function init_checkout($service_url, $request){
        $data = json_encode($request);
        $curl = curl_init($service_url);
        $headers = array(
              'Content-Type: application/json',
        	  'Content-Length: '. strlen($data));
        
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, $service_url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		
		// Only for testing purpose
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        
        $curl_response = curl_exec($curl);
        $http_status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($curl_response === false) {
            $info = curl_getinfo($curl);
            curl_close($curl);
            die('error occured during curl exec. Additioanl info: ' . var_export($info));
        }
        curl_close($curl);
        $decoded = json_decode($curl_response);
        
        // If status is not 200 (OK), throw error and kill process
        if ($http_status_code != 200) {
            die($curl_response);
        }
        
        $response = new PayWareResponse();
        $response->response_code = $http_status_code;
        $response->response = $decoded;
        
        return $response;
    }
    
    function get_checkout_status($service_url, $transaction_no, $merchant_id){
		$req = new CheckoutStatusModel();
		$req->transaction_no = $transaction_no;
		$req->merchant_id = $merchant_id;
		
        $data = json_encode($req);
        $curl = curl_init($service_url);
        $headers = array(
              'Content-Type: application/json',
        	  'Content-Length: '. strlen($data));
        
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, $service_url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		
		// Only for testing purpose
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        
        $curl_response = curl_exec($curl);
        $http_status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($curl_response === false) {
            $info = curl_getinfo($curl);
            curl_close($curl);
            die('error occured during curl exec. Additioanl info: ' . var_export($info));
        }
        curl_close($curl);
        $decoded = json_decode($curl_response);
        
        // If status is not 200 (OK), throw error and kill process
        if ($http_status_code != 200) {
            die($curl_response);
        }
        
        $response = new PayWareResponse();
        $response->response_code = $http_status_code;
        $response->response = $decoded;
        
        return $response;
    }
}

class PayWareRequest{
    var $api_user_name;
    var $api_secret;
    var $merchant_id;
    var $transaction_number;
    var $success_url;
    var $failure_url;
    var $lang;
    var $is_testing;
    var $amount;
}

class PayWareResponse{
    var $response_code;
    var $response;
}

class CheckoutStatusModel{
	var $transaction_no;
	var $merchant_id;
}
?>