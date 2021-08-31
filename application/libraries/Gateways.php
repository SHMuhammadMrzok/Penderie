<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
/**
 * 
 */
class Gateways
{
    public $CI ;
    
    public function __construct($params = array())
    {
        $this->CI = &get_instance();
        
        $this->CI->load->library('encryption');
        $this->CI->config->load('encryption_keys');
        $this->CI->load->model('settings/gateways_model');
        
    }
    
   public function get_gateway_field_value($field='', $id=0)
   {
        $this->CI->load->model('settings/gateways_model');
        
        $value      = $this->CI->gateways_model->get_field_value($field, $id);
        
        $secret_key = $this->CI->config->item('new_encryption_key');
        $secret_iv  = md5('gateway_set');
        
        $dec_value  = $this->CI->encryption->decrypt($value, $secret_key, $secret_iv);
        return $dec_value;
   }
   
}
