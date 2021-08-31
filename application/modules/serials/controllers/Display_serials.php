<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Display_serials extends CI_Controller
{
    public $lang_row;
    
    public function __construct()
    {
        parent::__construct();
        
        require(APPPATH . 'includes/global_vars.php');
        
        $this->load->model('display_serials_model');
        $this->load->library('encryption');
        $this->config->load('encryption_keys');
        
        $this->lang_row = $this->admin_bootstrap->get_active_language_row(); 
    }

    

    public function index()
    {   
        $lang_id = $this->data['active_language']->id;
        
        $this->data['count_all_records'] = $this->display_serials_model->get_count_all_serials();
        $this->data['data_language']     = $this->lang_model->get_active_data_languages();
        
       
        $this->data['columns']           = array(
                                                  lang('serial')      ,
                                                  lang('pin')         ,
                                                  lang('amount_code') ,
                                                  lang('active')      ,
                                                  lang('charged')     ,
                                                  lang('unix_time')
                                                  
                                                );
            
        $this->data['actions']           = array( 'delete'=>lang('delete'));
        $this->data['search_fields']     = array( lang('serial'));
        
        $this->data['content']           = $this->load->view('Admin/grid/grid_html', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
    }
    
    public function ajax_list()
    {
        if(isset($_POST['lang_id']))
        {
            $lang_id = intval($this->input->post('lang_id'));
        }else{
            $lang_id = $this->data['active_language']->id;    
        }
        if(isset($_POST['limit']))
        {
            $limit = intval($this->input->post('limit'));
        }else{
            $limit = 1;    
        }
        
        if(isset($_POST['page_number']))
        {
            $active_page = intval($this->input->post('page_number'));
        }else{
            $active_page = 1;    
        }
        
       
        $offset  = ($active_page-1) * $limit;
        
        
        
        if(isset($_POST['search_word']) && trim($_POST['search_word']) != '')
        { 
            $post_word   = $this->input->post('search_word');
            $secret_key  = $this->config->item('new_encryption_key');
            $secret_iv   = md5('generated_code');
            
            $search_word = $this->encryption->encrypt($post_word, $secret_key, $secret_iv);
        }
        else
        {
            $search_word = '';
        }
        
        
        $grid_data                  = $this->display_serials_model->get_serials_data($limit, $offset, $search_word);
        
        $db_columns                 = array(
                                             'id',   
                                             'serial',
                                             'pin',
                                             'amount',
                                             'active',
                                             'charged',
                                             'unix_time'
                                           );
                       
        $this->data['hidden_fields'] = array('id');
        
        $new_grid_data = array();
        $secret_key    = $this->config->item('new_encryption_key');
        
        foreach($grid_data as $key =>$row)
        { 
            foreach($db_columns as $column)
            {
                $secret_iv  = md5('generated_code');
                
                if($column == 'serial')
                { 
                    
                    $new_grid_data[$key][$column] = $this->encryption->decrypt($row->serial, $secret_key, $secret_iv);
                }
                elseif($column == 'amount')
                {
                    $new_grid_data[$key][$column] = $this->encryption->decrypt($row->amount, $secret_key, $secret_iv);
                }
                elseif($column == 'pin')
                {
                    $new_grid_data[$key][$column] = $this->encryption->decrypt($row->pin, $secret_key, $secret_iv);
                }
                elseif($column == 'active')
                {
                    if($row->{$column} == 0)
                    {
                        $new_grid_data[$key][$column] = '<span class="badge badge-danger">'.lang('not_active').'</span>';    
                    }
                    elseif($row->{$column} == 1)
                    {
                        $new_grid_data[$key][$column] = '<span class="badge badge-success">'.lang('active').'</span>';
                    }
                }
                elseif($column == 'charged')
                {
                    if($row->charged == 0)
                    {
                        $new_grid_data[$key][$column] =  '<span class="badge badge-danger">'.lang('no').'</span>';    
                    }
                    elseif($row->charged = 1)
                    {
                        $new_grid_data[$key][$column] = '<span class="badge badge-success">'.lang('yes').'</span>';
                    }
                }
                elseif($column == 'unix_time')
                {
                    $new_grid_data[$key][$column] = date('Y-m-d H:i', $row->unix_time);
                }
                else
                {
                    $new_grid_data[$key][$column] = $row->{$column};    
                }
                
            }
        }
        
        $this->data['grid_data']         = $new_grid_data; 
        
        $this->data['count_all_records'] = $this->display_serials_model->get_count_all_serials($search_word);
        
        $this->data['display_lang_id']   = $lang_id; 
        
        $count_data  = $this->data['count_all_records'];
        $output_data = $this->load->view('Admin/grid/grid_data',$this->data, true);
        
        echo json_encode(array($output_data, $count_data, $search_word));
     }
     
     public function edit($id)
    {
        if(is_numeric($id))
        {
            $id = intval($id);
            
            $validation_msg = false;
            
            if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
            {
                $validation_msg = true;
                $this->form_validation->set_rules('serial' , lang('serial') , 'required|trim');
                $this->form_validation->set_rules('pin' , lang('pin') , 'required|trim');
                $this->form_validation->set_rules('amount' , lang('amount_code') , 'required|trim');
                
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            }
            
            if($this->form_validation->run() == FALSE)
    		{
    		   $this->_edit_form($id, $validation_msg);
            }
            else
            {
                $id     = $this->input->post('id');
                $active = $this->input->post('active');
                
                $data  = array(
                                'active' => $active 
                              );
            
                $this->display_serials_model->update_serial($id,$data);
              
                $_SESSION['success'] = lang('success');
                $this->session->mark_as_flash('success');
                
                redirect('serials/display_serials/','refresh');
                
            }
        }
        
        
    }
    
    private function _edit_form($id, $validation_msg)
    {
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }
        
        $this->data['mode']                  = 'edit';
        $this->data['form_action']           = $this->data['module'] . "/" . $this->data['controller'] . "/edit/" . $id;
        $this->data['id']                    = $id;
        
        $serial_row_data                     = $this->display_serials_model->get_serial_row($id);
        
        $secret_key                          = $this->config->item('new_encryption_key');
        $secret_iv                           = md5('generated_code');
        $serial                              = $this->encryption->decrypt($serial_row_data->serial, $secret_key, $secret_iv);
        $amount                              = $this->encryption->decrypt($serial_row_data->amount, $secret_key, $secret_iv);
        $pin                                 = $this->encryption->decrypt($serial_row_data->pin, $secret_key, $secret_iv);
        
        $this->data['serial_row_data']       = $serial_row_data;
        $this->data['serial']                = $serial;
        $this->data['amount']                = $amount;
        $this->data['pin']                   = $pin;
        
        $this->data['content']               = $this->load->view('edit_serials', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
        
    }
    
     public function edit_form($id)
     {
        $id = intval($id);
        
        if($id)
        {
            //$this->_js_and_css_files();
        
            $this->data['mode']                  = 'edit';
            $this->data['form_action']           = $this->data['module'] . "/" . $this->data['controller'] . "/update";
            $this->data['id']                    = $id;
            
            $serial_row_data                     = $this->display_serials_model->get_serial_row($id);
            
            $secret_key                          = $this->config->item('new_encryption_key');
            $secret_iv                           = md5('generated_code');
            $serial                              = $this->encryption->decrypt($serial_row_data->serial, $secret_key, $secret_iv);
            $amount                              = $this->encryption->decrypt($serial_row_data->amount, $secret_key, $secret_iv);
            $pin                                 = $this->encryption->decrypt($serial_row_data->pin, $secret_key, $secret_iv);
            
            $this->data['serial_row_data']       = $serial_row_data;
            $this->data['serial']                = $serial;
            $this->data['amount']                = $amount;
            $this->data['pin']                   = $pin;
            
            $this->data['content']               = $this->load->view('edit_serials', $this->data, true);
            $this->load->view('Admin/main_frame',$this->data);
        }
     }
     
     public function update()
     {
        $id               = $this->input->post('id');
        $serial           = $this->input->post('serial');
        $amount           = $this->input->post('amount');
        $pin              = $this->input->post('pin');
        $active           = $this->input->post('active');
        
        $allserials       = $this->display_serials_model->get_all_serials();
        $serials_array    = array();
        
        foreach($allserials as $row)
        {
            $serials_array []= $row->serial ;
        }
        if(in_array($serial,$serials_array))
        {   
            $this->session->set_flashdata('faild',lang('faild'));
            redirect('serials/display_serials/index','refresh');
        
        }
        else
        {
            $time = $this->display_serials_model->get_serial_unix_time($id);
            $secret_key = $this->config->item('new_encryption_key');
            $secret_iv  = md5('generated_code');
            
            $enc_serial = $this->encryption->encrypt($serial, $secret_key, $secret_iv);
            $enc_amount = $this->encryption->encrypt($amount, $secret_key, $secret_iv);
            $enc_pin    = $this->encryption->encrypt($pin, $secret_key, $secret_iv);
            
            $data  = array(
                            'serial' => $enc_serial ,
                            'amount' => $enc_amount ,
                            'pin'    => $enc_pin    ,
                            'active' => $active 
                          );
        
            $this->display_serials_model->update_serial($id,$data);
          
            $this->session->set_flashdata('success',lang('success'));
            redirect('serials/display_serials/index','refresh');
        }
  
     }
     
     public function read($id, $display_lang_id)
     {
        $id              = intval($id);
        $display_lang_id = intval($display_lang_id);
        
        if($id && $display_lang_id)
        {
            $data = $this->display_serials_model->get_serial_row($id,$display_lang_id);
            
            if($data)
            {
                $secret_key = $this->config->item('new_encryption_key');
                $secret_iv  = md5('generated_code');
                
                $serial     = $this->encryption->decrypt($data->serial, $secret_key, $secret_iv); 
                $amount     = $this->encryption->decrypt($data->amount, $secret_key, $secret_iv);
                $pin        = $this->encryption->decrypt($data->pin, $secret_key, $secret_iv);
                
                if($data->active == 1)
                {
                    $active_value = lang('active');
                    $active_class = 'success';
                }
                else
                {
                    $active_value = lang('not_active');
                    $active_class = 'danger';
                }
                
                if($data->charged == 0)
                {
                    $charged_status =  '<span class="badge badge-danger">'.lang('no').'</span>';    
                }
                elseif($data->charged = 1)
                {
                    $charged_status = '<span class="badge badge-success">'.lang('yes').'</span>';
                }
                
                $row_data = array(
                                    lang('serial')    => $serial ,
                                    lang('pin')       => $pin    ,
                                    lang('amount')    => $amount ,
                                    lang('unix_time') => date('Y-m-d H:i',$data->unix_time),
                                    lang('active')    => '<span class="badge badge-'.$active_class.'">'.$active_value.'</span>',
                                    lang('charged')   => $charged_status
                                 );
                
                if($data->charged == 1 || $data->sold == 1)
                {
                    $serial_log_data = $this->display_serials_model->get_serial_log_data($data->id);
                    //print_r($serial_log_data);die();
                    if(count($serial_log_data))
                    {
                        $row_data[lang('username')]   = $serial_log_data->username;
                        $row_data[lang('use_date')]   = date('Y-m-d H:i', $serial_log_data->unix_time);
                        $row_data[lang('ip_address')] = $serial_log_data->ip_address;
                    }
                }
                
            
                $this->data['row_data'] = $row_data;
                
                $this->data['content']  = $this->load->view('Admin/grid/read_view', $this->data, true);
                $this->load->view('Admin/main_frame',$this->data);
            
            }
        }
     }
     
    
     
     public function do_action()
     {
        $action = $this->input->post('action');
        if($action == 'delete')
        {
            $this->delete();
        }
     }
     
     public function delete()
     {
        $serials_ids = $this->input->post('row_id');

        if(is_array($serials_ids))
        { 
            
            $ids_array = array();
            
            foreach($serials_ids as $serial_id)
            {
                $ids_array[] = $serial_id['value'];
            }
        }else{ 
            
            $ids_array = array($serials_ids);
        }
            
        $this->display_serials_model->delete_serials_data($ids_array);
        echo "1";
     }  
     
     
    
/************************************************************************/    
}