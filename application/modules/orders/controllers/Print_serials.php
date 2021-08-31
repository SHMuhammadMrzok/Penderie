<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Print_serials extends CI_Controller
{
   
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model('orders_model');
        $this->load->model('products/products_model');
        $this->load->model('users/users_model');
        
        $this->load->library('notifications');
        $this->config->load('encryption_keys');
        
        require(APPPATH . 'includes/front_end_global.php');
    }
    
    public function preview($serial_id)
    {
        $serial_id       = intval($serial_id);
        if($this->data['is_logged_in'])
        {
            $display_lang_id = $this->user_bootstrap->get_active_language_row()->id;
            $serial_data     = $this->orders_model->get_serial_data($serial_id, $display_lang_id);
            $user_data       = $this->user_bootstrap->get_user_data();
            $order_data      = $this->orders_model->get_order($serial_data->order_id);
            
            if($user_data->id == $order_data->user_id && $this->data['is_wholesaler'])
            {
                $secret_key      = $this->config->item('new_encryption_key');
                $secret_iv       = md5('serial_iv');
                
                $dec_serial = $this->encryption->decrypt($serial_data->serial, $secret_key, $secret_iv);
                $serial_data->{'dec_serial'} = $dec_serial;
                
                $updated_data['printed'] = 1;
                $this->orders_model->update_order_serial($serial_id, $updated_data);
                
                $serial_data->{'header'} = $user_data->header;
                $serial_data->{'logo'}   = $user_data->logo;
                $serial_data->{'footer'} = $user_data->footer;
                
                $this->data['serial_data'] = $serial_data;
                
                $this->load->view('print_review',$this->data);
            }
            else
            {
                redirect('Not_allowed', 'refresh');
            }
        }
        else
        {
            redirect('users/users/user_login', 'refresh');
        }
    }
    
    public function preview_all_product_serials($order_id, $product_id)
    {
        $order_id   = intval($order_id);
        $product_id = intval($product_id);
        
        if($this->data['is_logged_in'])
        {
            $user_data            = $this->user_bootstrap->get_user_data();
            $order_data           = $this->orders_model->get_order($order_id); 
            
            if($user_data->id == $order_data->user_id && $this->data['is_wholesaler'])
            {
                $display_lang_id      = $this->user_bootstrap->get_active_language_row()->id;
                $product_serials_data = $this->orders_model->get_order_serials_data($product_id, $order_id, $display_lang_id);
                $serials_new_array    = array();
                
                foreach($product_serials_data as $serial)
                {
                    $secret_key  = $this->config->item('new_encryption_key');
                    $secret_iv   = md5('serial_iv');
                    
                    $serial->{'dec_serial'} = $this->encryption->decrypt($serial->serial, $secret_key, $secret_iv);
                    
                    $serials_new_array[] = $serial;
                }
                
                $order_data           = $this->orders_model->get_order_data($order_id);
                
                $product_data         = $this->products_model->get_product_with_translation_data($product_id, $display_lang_id);  
            
                $updated_data['printed']   = 1;
                foreach($product_serials_data as $serial)
                {
                    $this->orders_model->update_order_serial($serial->serial_id, $updated_data);
                }
                
                
                $this->data['product_serials_data'] = $serials_new_array;
                $this->data['product_data']         = $product_data;
                $this->data['order_data']           = $order_data;
                $this->data['wholesaler_data']      = $user_data;
                
                $this->load->view('print_all_preview',$this->data);
            }
            else
            {
                redirect('Not_allowed', 'refresh');
            }
        }
        else
        {
            redirect('users/users/user_login', 'refresh');
        }
    }
        
        
/************************************************************************/    
}