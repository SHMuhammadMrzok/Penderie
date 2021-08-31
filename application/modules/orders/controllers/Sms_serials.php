<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Sms_serials extends CI_Controller
{
   
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model('orders_model');
        $this->load->model('users/users_model');
        $this->load->library('notifications');
        
        require(APPPATH . 'includes/front_end_global.php');
        
        $this->config->load('encryption_keys');
    }
    
    public function sms_form($serial_id)
    {
        
        $serial_id = intval($serial_id);
        
        if($this->ion_auth->logged_in())
        {
            $display_lang_id = $this->user_bootstrap->get_active_language_row()->id;
            $serial_data     = $this->orders_model->get_serial_data($serial_id, $display_lang_id);
            $user_data       = $this->user_bootstrap->get_user_data();
            $order_data      = $this->orders_model->get_order($serial_data->order_id);
            
            if($user_data->id == $order_data->user_id && $this->data['is_wholesaler'])
            {
            
                $secret_key = $this->config->item('new_encryption_key');
                $secret_iv  = md5('serial_iv');
                
                $dec_serial = $this->encryption->decrypt($serial_data->serial, $secret_key, $secret_iv);
                
                $serial_data->{'serial'}   = $dec_serial;
                
                
                $msg       = $user_data->sms_content.' '. $serial_data->title.' : '.$dec_serial;
                
                $serial_data->{'msg'}      = $msg;
                $this->data['serial_id']   = $serial_id;
                $this->data['serial_data'] = $serial_data;
                
                $this->load->view('sms_form',$this->data);
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
    
    public function sms()
    {
        $serial_id       = intval($this->input->post('serial_id', TRUE));
        $receiver_number = trim($this->input->post('receiver_number', TRUE));
        $msg             = trim(strip_tags($this->input->post('message', TRUE)));
        
        if($this->ion_auth->logged_in())
        {
            $display_lang_id    = $this->user_bootstrap->get_active_language_row()->id;
            $user_data          = $this->user_bootstrap->get_user_data();
            $serial_order_data  = $this->orders_model->get_serial_order($serial_id);
            
            if($user_data->id == $serial_order_data->user_id && $this->data['is_wholesaler'])
            {
                if($this->notifications->send_sms($msg, $receiver_number, $user_data->sms_name))
                {
                    $updated_data['smsed'] = 1;
                    $this->orders_model->update_order_serial($serial_id, $updated_data);
                    
                    $sent_serials_log = array(
                                                'user_id'         => $user_data->id ,
                                                'serial_id'       => $serial_id ,
                                                'receiver_number' => $receiver_number
                                              );
                    $this->orders_model->insert_sms_log_data($sent_serials_log);
                    echo lang('send_sms_successfully');
                }
                else
                {
                    echo lang('send_sms_error');
                }
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
    
    public function sms_all_form($order_id, $product_id)
    {
        
        $order_id   = intval($order_id);
        $product_id = intval($product_id);
        
        if($this->ion_auth->logged_in())
        {
            $user_data  = $this->user_bootstrap->get_user_data();
            $order_data = $this->orders_model->get_order($order_id);
            
            if($user_data->id == $order_data->user_id)
            {
            
                $secret_key = $this->config->item('new_encryption_key');
                $secret_iv  = md5('serial_iv');
                
                $serials    = $this->orders_model->get_product_serials($product_id, $order_id);
                $msg        = $user_data->sms_content;
                
                
                foreach($serials as $serial)
                {
                    $dec_serial = $this->encryption->decrypt($serial->serial, $secret_key, $secret_iv);
                    $msg .= ' '.$serial->product_name.' : '.$dec_serial;
                }
                
                
                $this->data['product_id'] = $product_id;
                $this->data['order_id']   = $order_id;
                $this->data['serials']    = $serials;
                $this->data['msg']        = $msg;
                
                $this->load->view('sms_all_form',$this->data);
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
    
    public function sms_all_serials()
    {
        $order_id        = intval($this->input->post('order_id', TRUE));
        $product_id      = intval($this->input->post('product_id', TRUE));
        $receiver_number = trim(strip_tags($this->input->post('receiver_number')));
        $msg             = trim(strip_tags($this->input->post('message')));
        
        if($this->ion_auth->logged_in())
        {
            $user_data  = $this->user_bootstrap->get_user_data();
            $order_data = $this->orders_model->get_order($order_id);
            
            if($user_data->id == $order_data->user_id)
            {
            
                if($this->notifications->send_sms ($msg ,$receiver_number, $user_data->sms_name))
                {
                    $all_serials = $this->orders_model->get_product_serials($product_id, $order_id);
                    
                    foreach($all_serials as $key=>$serial)
                    {
                        $updated_data['smsed'] = 1;
                        $this->orders_model->update_order_serial($serial->product_serial_id, $updated_data);
                        
                        $sent_serials_log = array(
                                                    'user_id'         => $user_data->id             ,
                                                    'serial_id'       => $serial->product_serial_id ,
                                                    'receiver_number' => $receiver_number
                                                 );
                        
                        $this->orders_model->insert_sms_log_data($sent_serials_log);
                    }
                }
                else
                {
                    echo lang('send_sms_error');
                }
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