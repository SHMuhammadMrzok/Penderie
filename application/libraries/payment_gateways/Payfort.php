<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Payfort
{
    public  $CI, $PayfortRequestEncryptionKey, $PayfortResponseEncryptionKey,
            $PayfortMerchantID, $PayfortLanguage, $PayfortAccessCode,
            $PayfortReturnUrl, $PayfortCommand;

    private $PayfortEncryptionKey;

    public $PayfortMode = ['test' => 'sbcheckout', 'prod' => 'checkout'];

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->fetch_payfort_config();
    }

    private function fetch_payfort_config()
    {
        $this->CI->load->library('currency');

        $this->CI->config->load('payment_gateways');

        $this->PayfortMode                  = $this->PayfortMode["{$this->CI->config->item('PayfortMode')}"];
        $this->PayfortRequestEncryptionKey  = $this->CI->config->item('PayfortRequestEncryptionKey');
        $this->PayfortResponseEncryptionKey = $this->CI->config->item('PayfortResponseEncryptionKey');
        $this->PayfortMerchantID            = $this->CI->config->item('PayfortMerchantID');
        $this->PayfortLanguage              = $this->CI->config->item('PayfortLanguage');
        $this->PayfortAccessCode            = $this->CI->config->item('PayfortAccessCode');
        $this->PayfortReturnUrl             = $this->CI->config->item('PayfortReturnUrl');
        $this->PayfortCommand               = $this->CI->config->item('PayfortCommand');
    }

    public function generate_signature(array $requestParams)
    {
        return hash('sha256',
        urldecode($this->PayfortEncryptionKey .
                http_build_query($requestParams,'','') .
                $this->PayfortEncryptionKey));
    }

    public function is_valid_signature($posted_signature, array $post_params)
    {
        $generated_signature = $this->generate_signature($post_params);

        if(strtolower(trim($posted_signature)) === strtolower($generated_signature))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function generate_form($order_id, $amount, $currency, $payment_option = null, $email, $order_description = null, $customer_name = null)
    {
        if(!in_array($payment_option, ['SADAD', 'MASTERCARD', 'VISA', 'NAPS', null])) {
            echo "You must choose the right payment option";

            return false;
        }

        $order_data = $this->CI->orders_model->get_order($order_id);
        // convert currency to riyal
        if($order_data->currency_symbol != 'SAR')
        {
            $currency = 'SAR';
            $amount   = $this->CI->currency->convert_to_currency($order_data->currency_symbol, $currency, $amount);
        }



        $this->setEncryptionKey($this->PayfortRequestEncryptionKey);

        $filtered_params = $this->generate_in_array($order_id, $amount, $currency, $payment_option, $email, $order_description, $customer_name);

        $html = '<form method="post" action="https://' . $this->PayfortMode . '.payfort.com/FortAPI/paymentPage" class="pay_form" name="form1">';
//echo '<pre>'; print_r($filtered_params); die();
        foreach ($filtered_params as $name => $value) {
            $html .= '<input type="hidden" name="' . strtolower(htmlentities($name)) . '" value="' . htmlentities($value) . '" />';
        }

        $html .= '<input type="hidden" name="signature" value="' . $this->generate_signature($filtered_params) . '" />
                </form>';
//echo $html;die();
        return $html;
    }

    public function generate_in_array($order_id, $amount, $currency, $payment_option, $email, $order_description = null, $customer_name = null)
    {
        if(!in_array($payment_option, ['SADAD', 'MASTERCARD', 'VISA', 'NAPS', null])) {
            echo "You must choose the right payment option";

            return false;
        }

        $requestParams = array(
            'access_code'         => $this->PayfortAccessCode ,
            'amount'              => floatval($amount)*100,
            'currency'            => strtoupper($currency),
            'customer_email'      => $email,
            'merchant_reference'  => $order_id,
            'order_description'   => $order_description,
            'language'            => $this->PayfortLanguage,
            'merchant_identifier' => $this->PayfortMerchantID,
            'payment_option'      => $payment_option,
            'command'             => $this->PayfortCommand,
            'return_url'          => $this->PayfortReturnUrl,
            'customer_name'       => $customer_name,
            'customer_ip'         => $this->CI->input->ip_address()
        );

        $filtered_params = array_filter($requestParams);
        ksort($filtered_params);

        return $filtered_params;
    }

    public function handleResponse(array $requestParams)
    {
        if(isset($requestParams['signature'])) {
            $returnSignature = $requestParams['signature'];
            unset($requestParams['signature']);

            $this->setEncryptionKey($this->PayfortResponseEncryptionKey);
        } else {
            return "invalid";
        }

        ksort($requestParams);

        if($this->is_valid_signature($returnSignature, $requestParams)) {

            if(isset($requestParams['status'])) {

                $statusCode = intval($requestParams['status']);

                if(in_array($statusCode, [14])) {
                    return "success";
                } elseif (in_array($statusCode, [13])) {
                    return "failure";
                } elseif (in_array($statusCode, [19, 20])) {
                    return "pending";
                } else {
                    return "invalid";
                }

            } else {
                return "invalid";
            }
        } else {
            return "invalid";
        }
    }

    private function setEncryptionKey($encryptionKey)
    {
        $this->PayfortEncryptionKey = $encryptionKey;
    }
}
