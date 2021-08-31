<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Currency_values extends CI_Controller
{
    public $lang_row;
    
    public function __construct()
    {
        parent::__construct();
        
        require(APPPATH . 'includes/global_vars.php');
        
        $this->load->model('currency_model');
        
        $this->lang_row = $this->admin_bootstrap->get_active_language_row(); 
    }
    
    private function _js_and_css_files()
    {
        $this->data['css_files'] = array();
        
        $this->data['js_files']  = array(
            
            //TouchSpin
            'global/plugins/fuelux/js/spinner.min.js',
            'global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js',
            'global/plugins/bootstrap-touchspin/bootstrap.touchspin.js',
            );
        
        $this->data['js_code'] = "ComponentsPickers.init();";
    }

    public function index()
    {
        $validation_msg = false;
        $lang_id        = $this->data['active_language']->id;
        
        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
        {
            $currencies = $this->input->post('currency_id');
        
            foreach($currencies as $currency_id)
            {  
                $this->form_validation->set_rules('currency_value['.$currency_id.']', lang('currency_value'), 'required');
            }
            
            $this->form_validation->set_message('required', lang('required')."  : %s ");
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            
            $validation_msg = true;
        }
        
        if ($this->form_validation->run() == FALSE)
		{
		  $this->_currencies_form($validation_msg, $lang_id);
        }
        else
        {
            $value = $this->input->post('currency_value');
            
            
            foreach($currencies as $currency_id)
            {
                $default = 0;
                if(isset($_POST['default']) && $_POST['default'] == $currency_id)
                {
                    $default = 1;
                }
                
                $currency_data = array(
                                          'currency_value'   => $value[$currency_id],
                                          'system_default'   => $default,
                                          'last_update_unix' => time() 
                                      );
                
                $this->currency_model->update_currency($currency_id, $currency_data);                
            }
            
            $this->session->set_flashdata('success',lang('success'));
               
               redirect('currencies/currency_values/','refresh');
           
            
        }
     }
     
     private function _currencies_form($validation_msg, $lang_id)
     {
        $this->_js_and_css_files();
        
        $currencies_data = $this->currency_model->get_currecies_result($lang_id);
        
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }
        
        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/index";
        $this->data['data']         = $currencies_data;
        
        $this->data['content']      = $this->load->view('currencies_values', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
     }
     
    
/************************************************************************/    
}