<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Check_serials extends CI_Controller
{
    
    public $lang_row ;
    public $data = array();
    
    public function __construct()
    {
        parent::__construct();
        
        require(APPPATH . 'includes/global_vars.php');
        $this->load->model('products_serials_model');
        $this->load->model('products_model');
        $this->load->model('users/countries_model');
        $this->load->model('purchase_orders_model');
        
        $this->load->library('encryption');
        $this->config->load('encryption_keys');
        
        $this->lang_row = $this->admin_bootstrap->get_active_language_row();
    }

    private function _js_and_css_files()
    {
        $this->data['css_files'] = array();
        
        $this->data['js_files']  = array();
        $this->data['js_code'] = "";
		
    }
    
    public function index($validation_msg = '')
    {
        $lang_id = $this->data['active_language']->id;
        
        $this->data['form_action']   = $this->data['module'] . "/" . $this->data['controller'] . "/check_serials";
        $this->data['data_language'] = $this->lang_model->get_active_data_languages();
        
        if($validation_msg != '')
        {
            $this->data['validation_msg'] = $validation_msg;
        }
        
        $this->data['content'] = $this->load->view('serials_check_form', $this->data, true);
        $this->load->view('Admin/main_frame', $this->data);
    }
    
    public function check_serials()
    {
        
        $serials    = $this->input->post('serials');
        $this->form_validation->set_rules('serials', lang('serials'), 'required');
        
        if($this->form_validation->run() == FALSE)
        {
            $validation_msg = validation_errors();
            $this->index($validation_msg);
        }
        else
        {
            $serials    = array_filter(explode("\n", $serials));
            
            $result     = array();
            $secret_key = $this->config->item('new_encryption_key');
            $secret_iv  = md5('serial_iv');
            
            foreach($serials as $serial)
            {
                //--> serial encription
                $serial      = trim($serial);
                $enc_serial  = $this->encryption->encrypt($serial, $secret_key, $secret_iv);
                $serial_data = $this->products_serials_model->get_serial_data($enc_serial);
                
                if(count($serial_data) != 0)
                {
                    $serial_status = $this->_get_serial_status($serial_data->serial_status, $serial_data->id);
                    
                    $result[] = $serial .'<td> '. $serial_status.'</td>';
                    
                }
                else
                {
                    $result[] = $serial .'<td><span class="badge badge-danger"> '. lang('serial_not_exist').'</span></td>';
                }
                
            }
            
            $this->data['result']  = $result;
            $this->data['content'] = $this->load->view('check_serials_result', $this->data, true);
            $this->load->view('Admin/main_frame', $this->data);
        }
        
    }
    
    private function _get_serial_status($serial_status_id, $serial_id)
    {
        
        if($serial_status_id == 3)
        {
            $result = '<span class="badge badge-danger">'.lang('invalid').'</span>';
        }
        elseif($serial_status_id == 0)
        {
            $result = '<span class="badge badge-success">'.lang('available').'</span>';
        }
        elseif($serial_status_id == 1)
        {
            $order_link = $this->_get_order_link($serial_id);
            $result     = '<span class="badge badge-warning">'.lang('pending').'</span> '.$order_link;
        }
        elseif($serial_status_id == 2)
        {
            $order_link = $this->_get_order_link($serial_id);
            
            $result     = '<span class="badge badge-info">'.lang('sold').'</span> '.$order_link;
        }
        
        return $result;
    }
    
    private function _get_order_link($serial_id)
    {
        $order_id   = $this->products_serials_model->get_serial_order_id($serial_id);
        $order_link = "<a href='".base_url()."orders/admin_order/view_order/$order_id' target='_blank'>$order_id</a>";
        
        $result = lang('order_id').' : '.$order_link;
        
        return $result;
    }
  
/************************************************************************/    
}