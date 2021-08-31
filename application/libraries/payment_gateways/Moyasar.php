<?php if(!defined('BASEPATH'))
    exit('No direct script access allowed');

class Moyasar
{
    public $CI;
    public $payment_url;
    public $redirect_url;
    public $api_key;
    public $secret_key;

    public function __construct()
    {
        $this->CI = &get_instance();

        $this->CI->load->library('Gateways');
        $this->CI->config->load('payment_gateways');

        $this->payment_url  = 'https://api.moyasar.com/v1/payments.html';
        $this->redirect_url = $this->CI->config->item('moyasarRedirectURL');
        $this->api_key      = 'pk_test_QGHvAeaWy6aKY26tPBCapFkKbUZcs3qY32uEzyvo';//$this->CI->gateways->get_gateway_field_value('moyasar_api_key');
        $this->secret_key   = 'sk_test_6bS8Bi5u3nCENr7YUA7jmeBf1wwwNF5RbmEbsk63';
        /*$this->olpIdAlias               = 'https://api.moyasar.com/v1/payments.html';$this->CI->gateways->get_gateway_field_value('sadad_olpIdAlias');

        */
    }

	public function INITIATE_PAYMENT($order_id, $order_total, $currency_symbol, $post_data)
    {
        $request_payment_url = 'https://api.moyasar.com/v1/payments^-u'.$this->api_key.':';//$this->payment_url;
        $total_in_halalas = $order_total * 100;
        
        $request_payment_data = '{
                                  "amount": '.$total_in_halalas.',
                                  "currency": "'.$currency_symbol.'",
                                  "invoice_id": "'.$order_id.'",
                                  "ip": '.$this->CI->input->ip_address().',
                                  "callback_url": '.$this->redirect_url.',
                                  "source": {
                                    "type": "creditcard",
                                    "company": "visa",
                                    "name": "'.$post_data['source']['name'].'",
                                    "number": "'.$post_data['source']['name'].'",
                                    
                                  }
                                }';

        $ch = curl_init();
      	curl_setopt($ch, CURLOPT_URL, $request_payment_url);
      	curl_setopt($ch, CURLOPT_POST, 1);
      	curl_setopt($ch, CURLOPT_POSTFIELDS, $request_payment_data);
      	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// this should be set to true in production
      	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

      	$responseData = curl_exec($ch);

      	if(curl_errno($ch)) {
      		return curl_error($ch);
            
      		print "<br> Error: " .curl_error($ch);
            
      	    die();

              curl_close($ch);
      	}
        else{
            die('222');
            print_r($responseData);die();
        }

        /*
        {
  "id": "760878ec-d1d3-5f72-9056-191683f55872",
  "status": "paid",
  "amount": 88571,
  "fee": 1580,
  "currency": "SAR",
  "refunded": 0,
  "refunded_at": null,
  "description": null,
  "amount_format": "885.71 SAR",
  "fee_format": "15.80 SAR",
  "refunded_format": "0.00 SAR",
  "invoice_id": "9785ba96-a1be-5b13-a281-b27a4a6dad39",
  "ip": null,
  "callback_url": null,
  "created_at": "2016-05-11T17:04:17.000Z",
  "updated_at": "2016-05-12T17:04:19.633Z",
  "source": {
    "type": "creditcard",
    "company": "visa",
    "name": "Abdulaziz Alshetwi",
    "number": "XXXX-XXXX-XXXX-1881",
    "message": null,
    "transaction_url": null
  }
}
        */
    }

    public function handleResponse($response_status)
    {

      if($response_status == '')
      {
        $status = 'success';
      }
      else {
        $status = 'failure';
      }

      return $status;
        /*$url = "https://api.moyasar.com/v1/payments/".$payment_id;
        $stringToPost = '';

        $result = $this->curlRequest($url, $stringToPost);
        print_r($result);die();
        */
    }

    public function curlRequest($url, $stringToPost)
    {
        $ch = curl_init();
      	curl_setopt($ch, CURLOPT_URL, $url);
      	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      	curl_setopt($ch, CURLOPT_HEADER, 0);
      	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
      	//curl_setopt($ch, CURLOPT_POST, 1);
        //curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
      	curl_setopt($ch, CURLOPT_POSTFIELDS, $stringToPost);


      	$result = curl_exec($ch);

        if (curl_error($ch))
        {
            print curl_error($ch);
            die();
        }
        else
        {
            print 'ret: ' .$result;

        }
        //return $result;
    }




}
