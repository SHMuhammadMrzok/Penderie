<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Enc extends CI_Controller {
    
   public function __construct()
    {
        parent::__construct();
        $lang=$this->session->userdata('lang');
                
        if($lang=='')
        {
             $this->lang->load('english','english');
        }
        else{
            $this->lang->load($lang,$lang);
        }
        
    }


/******************************************************************************/

    public function test()
    {
        $key = hash('sha256', 'this is a secret key', true);
        $input_text = 'vpJ4OxNcPFVuGY0TVdlTHBNsyQIj';
        
        $encrypted_text = $this->encrypt($key, $input_text);
        
        echo 'Encrypted text : ' . $encrypted_text;
        
        $decrypted_text = $this->decrypt($key, $encrypted_text);
        
        echo '</br>';
        echo 'Decrypted text : ' . $decrypted_text;
    }
    
     public function test2()
    {
        $key = hash('sha256', 'this is a secret key', true);
        $input = "4655555565965jhfjhfjh";
        
        $td = mcrypt_module_open('rijndael-128', '', 'cbc', '');
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_DEV_URANDOM);
        mcrypt_generic_init($td, $key, $iv);
        $encrypted_data = mcrypt_generic($td, $input);
        
        echo 'encrypted_data : '.$encrypted_data;
        
        
        mcrypt_generic_init($td, $key, $iv);
        $p_t = mdecrypt_generic($td, $encrypted_data);
        
        echo '</br>decrypted data : '.$p_t ;
        
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        
        echo '</br>';
        
        
    }
    
    public function encrypt($key, $input_text)
    {
        
        $td = mcrypt_module_open('rijndael-128', '', 'cbc', '');
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_DEV_URANDOM);
        mcrypt_generic_init($td, $key, $iv);
        $encrypted_data = mcrypt_generic($td, $input_text);
        
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        
        return $encrypted_data;
    }
    
    public function decrypt($key, $encrypted_data)
    {
        $td = mcrypt_module_open('rijndael-128', '', 'cbc', '');
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_DEV_URANDOM);
        
        mcrypt_generic_init($td, $key, $iv);
        $p_t = mdecrypt_generic($td, $encrypted_data);
        
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        
        return $p_t;
        
    }
    
    
    public function crypt()
    {
        $salt = mcrypt_create_iv(22, MCRYPT_DEV_URANDOM);
        $salt = base64_encode($salt);
        $salt = str_replace('+', '.', $salt);
        $hash = crypt('mariam', '$2y$10$'.$salt.'$');
        
        echo $hash;
        
        echo '</br>';
        
        if (password_verify('mariam', $hash)) {
            echo 'Password is valid!';
        } else {
            echo 'Invalid password.';
        }

    }
    
    function encrypt_decrypt($action, $string) 
    {
        $output = false;
    
        $encrypt_method = "AES-256-CBC";
        $secret_key = 'This is my secret key';
        $secret_iv = 'This is my secret iv';
    
        // hash
        $key = hash('sha256', $secret_key);
        
        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
    
        if( $action == 'encrypt' ) {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        }
        else if( $action == 'decrypt' ){
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }
    
        return $output;
            
    }
    
    /**************************************************************************************************/
    
    
    public function try_2s()
    {
        $secret_key = 'This is my secret key';
        $secret_iv = '1234567899';
        
        $plain_txt = "C9VEsgNHbn9cnelA82dR4w6tfSQo";
        echo "Plain Text = $plain_txt\n";
        echo '</br></br>';
        
        $encrypted_txt = $this->encrypt2($plain_txt, $secret_key, $secret_iv);
        echo "Encrypted Text = $encrypted_txt\n";
        echo '</br></br>';
        
        $decrypted_txt = $this->decrypt2($encrypted_txt, $secret_key, $secret_iv);
        echo "Decrypted Text = $decrypted_txt\n";
        echo '</br></br>';
        
        if( $plain_txt === $decrypted_txt ) echo "SUCCESS";
        else echo "FAILED";
        
        echo "\n";
    }
    
    
    function encrypt2($string, $secret_key, $secret_iv) 
    {
        $encrypt_method = "AES-256-CBC";
    
        // hash
        $key = hash('sha256', $secret_key);
        
        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
    
        
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
        
        return $output;
            
    }
   
   
    function decrypt2($encrypted_txt, $secret_key, $secret_iv) 
    {
        $encrypt_method = "AES-256-CBC";
    
        // hash
        $key = hash('sha256', $secret_key);
        
        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
    
        $output = openssl_decrypt(base64_decode($encrypted_txt), $encrypt_method, $key, 0, $iv);
        
        return $output;
            
    }
    
    public function enc_lib()
    {
        $this->load->library('encryption');
        
        $secret_key = 'This is my secret key';
        $secret_iv = '1234567899';
        
        $plain_txt = "Hello Mariam";
        echo "Plain Text = $plain_txt\n";
        echo '</br></br>';
        
        $encrypted_txt = $this->encryption->encrypt($plain_txt, $secret_key, $secret_iv);
        echo "Encrypted Text = $encrypted_txt\n";
        echo '</br></br>';
        
        $decrypted_txt = $this->encryption->decrypt($encrypted_txt, $secret_key, $secret_iv);
        echo "Decrypted Text = $decrypted_txt\n";
        echo '</br></br>';
        
        if( $plain_txt === $decrypted_txt ) echo "SUCCESS";
        else echo "FAILED";
        
        echo "\n";
    }    
  
}
