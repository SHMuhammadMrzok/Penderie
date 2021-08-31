<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Rebuild_encryption extends CI_Controller
{
    public   $lang_row;
    public function __construct()
    {
        parent::__construct();
        
        require(APPPATH . 'includes/global_vars.php');
        
        $this->load->model('rebuild_encryption_model');
        $this->load->library('encryption');
        $this->config->load('encryption_keys');
        
        $this->lang_row = $this->admin_bootstrap->get_active_language_row(); 
    }
    
    private function _js_and_css_files()
     {
        $this->data['css_files'] = array('thickbox.css');
        
        $this->data['js_files']  = array(
                                          'global/scripts/thickbox.js',   
                                        );
        
        $this->data['js_code'] = "";
    }

    /**************** List functions **********************/

   
    public function index()
    {
        $this->_js_and_css_files();
        
        $this->data['content'] = $this->load->view('rebuild_encryption_modal',$this->data,true);
        $this->load->view('Admin/main_frame',$this->data);
    }
    
    public function load_modal($process, $percent, $process_completed)
    { 
        $this->data['process']   = $process;
        $this->data['percent']   = $percent;
        $this->data['completed'] = $process_completed;
        
        $this->load->view('modal_content', $this->data);
        
    }
    
    public function rebuild_encryption_modal()
    {
        $encrypted_fields  = array(
                                    'products_serials' => array('serial'),
                                    'serials'          => array('serial', 'amount'),
                                    'users'            => array('user_balance', 'user_points', 'device_id', 'security_code'),
                                    'settings'         => array('payfort_secret_sig', 'wholesaler_secret_key')
                                  );
                                  
        $limit             = 100;
        $percent           = 0;
        $decrypt_done      = false;
        $encrypt_done      = false;
        $process_completed = false;
        
        
        $count_all_rows        = $this->rebuild_encryption_model->get_all_rows_count($encrypted_fields);
        $decrypted_rows_count  = $this->rebuild_encryption_model->get_decrepted_rows($encrypted_fields);
        
        if($count_all_rows != $decrypted_rows_count)
        {
            $test_decryption = $this->rebuild_encryption_model->test_decryption($encrypted_fields);
            
            if(!$test_decryption)
            {
                echo lang('change_enc_keys');
                die();
                
            }
            //decrypt all rows
            foreach($encrypted_fields as $table => $fields)
            {
                if($decrypt_done == false)
                {
                    $decrypted_rows_count = $this->rebuild_encryption_model->check_count('decrypt_value','0',$table);
                
                    if($decrypted_rows_count > 0)
                    {
                        
                        $this->decrypt_data($table, $fields, $limit);
                        
                        $decrypt_done          = true;
                        $count_decrypted_rows = $this->rebuild_encryption_model->get_decrepted_rows($encrypted_fields);
                        $percent               = round(($count_decrypted_rows / ($count_all_rows *2)) * 100);
                        $process               = lang('decrypting');
                        break;
                    }
                }
            }
        }
        else
        {
            $percent = 50;
            
            $encrypted_rows_count = $this->rebuild_encryption_model->count_all_encrypted_rows($encrypted_fields);
            
            //re-encrypt rows       
            if($encrypt_done == false)
            {
                $data_array           = $this->re_encrypt_data($encrypted_fields, $limit);
                $encrypted_rows_count = $this->rebuild_encryption_model->count_all_encrypted_rows($encrypted_fields);
                $percent             += round(($encrypted_rows_count / ($count_all_rows * 2)) * 100);
                $process              = lang('encrypting_with_new_key');  
                $encrypt_done         = true;  
            }
            
            if($encrypted_rows_count == $count_all_rows)
            {
                $process_completed = true;
                $process           = lang('process_completed');
                $this->rebuild_encryption_model->reset_encryption_values($encrypted_fields);
            }
        }
        
        //when finishing encrypt reset encryp_value && decrypt_value to be Zeros
        
        $this->load_modal($process, $percent,$process_completed);
    }
    
    public function decrypt_data($table, $encrypted_fields, $limit)
    {   
        return $this->rebuild_encryption_model->decrypt_rows($table, $encrypted_fields, $limit);
    }
    
    public function re_encrypt_data($encrypted_fields, $limit)
    {
        return $this->rebuild_encryption_model->encrypt_fields_with_new_key($encrypted_fields, $limit);
    }
    
    
    
/************************************************************************/    
}