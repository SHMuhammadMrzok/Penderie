<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Cashu
{
    public $CI, $CashuMode, $CashuEncryptionKey,
           $CashuMerchantID, $CashuLanguage;

    public function __construct()
    {   
        $this->CI = &get_instance();
        $this->fetch_cashu_config();
        
        $this->CI->load->model('users/countries_model');
    }
    
    private function fetch_cashu_config()
    {
        $this->CI->config->load('payment_gateways');   

        $this->CashuMode            = $this->CI->config->item('CashuMode');
        $this->CashuEncryptionKey   = $this->CI->config->item('CashuEncryptionKey');
        $this->CashuMerchantID      = $this->CI->config->item('CashuMerchantID');
        $this->CashuLanguage        = $this->CI->config->item('CashuLanguage');
    }

    public function generate_token($amount, $currency)
    {
        return md5($this->CashuEncryptionKey . ':' . $amount . ':' . strtolower($currency) . ':' . $this->CashuEncryptionKey);
    }

    public function is_valid_token($posted_token, $amount, $currency)
    {
        $generated_token = $this->generate_token($amount, $currency);

        if(trim($posted_token) == $generated_token)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function fetch_form($order_id, $amount, $currency, $text)
    {
        $currency = strtolower($currency);

        $html = '<form action="https://www.cashu.com/cgi-bin/pcashu.cgi" method="post" class="pay_form">
                    <input type="hidden" name="merchant_id" value="'. $this->CashuMerchantID .'" />
                    <input type="hidden" name="token" value="'. $this->generate_token($amount, $currency, $order_id) .'" />
                    <input type="hidden" name="display_text" value="'. $text .'" />
                    <input type="hidden" name="currency" value="'.$currency.'" />
                    <input type="hidden" name="amount" value="'.$amount.'" /> 
                    <input type="hidden" name="language" value="'.$this->CashuLanguage.'" /> 
                    <input type="hidden" name="session_id" value="'. $this->CI->session->userdata('session_id') .'" /> 
                    <input type="hidden" name="txt1" value="' . $order_id . '" class="order_id_input" />
                    <input type="hidden" name="test_mode" value="'. $this->CashuMode .'" /> 
                </form>';

        return $html;
    }
    
    public function handleResponse($mode, $token, $order_data)
    {
        if($mode == 'sorry')
        {
            $status = 'failure';
        }
        
        elseif($mode == 'notification')
        {
            $status = 'pending';
        }
        
        elseif($mode == 'pending')
        {
            $status = 'pending';
        }
        
        elseif($mode == 'success')
        {
            $status = 'success';
        }
        
        if(in_array($mode, array('success', 'pending')))
        {
            $currency_symbol = $this->CI->countries_model->get_currency_symbol(1, $order_data->country_id);
            $is_valid_token  = $this->is_valid_token($token, $order_data->final_total, $currency_symbol);
            
            
            if(!$is_valid_token)
            {
                $status = 'invalid';
                //$log_data['status_id'] = 4;
            }
        }
        
        return $status;
    }

    // result array : 
    /* 
    Array
    (
        [language] => en
        [amount] => 125
        [currency] => USD
        [session_id] => asdasd-234-asdasd
        [txt1] => item27
        [txt2] => 12546
        [txt3] => islam4545
        [txt4] => 
        [txt5] => 
        [token] => 1380fdc614179cf702333b4e3887963a
        [trn_id] => 4080224
        [verificationString] => 665fa82b634c0cab9cc3dea693be5bfd21fd2cfa
        [trnDate] => 2015-12-08 09:49
        [servicesName] => 
        [netAmount] => 116.25
    )
    */
}
?>