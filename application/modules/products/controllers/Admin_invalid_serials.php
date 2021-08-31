<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Admin_invalid_serials extends CI_Controller
{
    public $lang_row ;
    public $stores;
    public $stores_ids;
 
    public function __construct()
    {
        parent::__construct();
        
        require(APPPATH . 'includes/global_vars.php');
        
        $this->load->model('invalid_serials_model');
        $this->load->model('purchase_orders_model');
        $this->load->model('products_serials_model');
        
        $this->load->library('encryption');
        $this->config->load('encryption_keys');
        
        $this->lang_row = $this->admin_bootstrap->get_active_language_row();
        
        /*************************************************************************/
        $this->stores   = $this->admin_bootstrap->get_user_available_stores();
        
        $store_id_array = array();
        
        foreach($this->stores as $store)
        {
            $store_id_array[] = $store->store_id;
        }
        
        $this->stores_ids = $store_id_array;
        /*************************************************************************/
    }

     private function _js_and_css_files()
     {
        $this->data['css_files'] = array();
        
        $this->data['js_files']  = array();
                                                                                                        
    }
    public function index()
    {
        $lang_id = $this->data['active_language']->id;
        
        $this->data['count_all_records']    = $this->invalid_serials_model->get_count_all_invalid_serials($lang_id);
        $this->data['data_language']        = $this->lang_model->get_active_data_languages();
        
        $this->data['columns']              = array(
                                                     lang('purchase_order_id'),
                                                     lang('vendor'),     
                                                     lang('product'),
                                                     lang('country'),
                                                     lang('serial'),
                                                     lang('serial_add_date'),
                                                     lang('order_id'),
                                                     lang('order_date'),
                                                     lang('sent_to_vendor'),
                                                     lang('status')
                                                   );
            
        $this->data['actions']              = array();
        $this->data['search_fields']        = array(lang('serial'));
        $this->data['index_method_id']      = $this->data['method_id'];
        
        $this->data['content']  = $this->load->view('Admin/grid/grid_html', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
    }
  
    public function ajax_list()
    {
        /**************************************/
        $this->stores   = $this->admin_bootstrap->get_user_available_stores($_POST['index_method_id']);
        $store_id_array = array();
        
        foreach($this->stores as $store)
        {
            $store_id_array[] = $store->store_id;
        }
        
        $this->stores_ids = $store_id_array;
        /**************************************/
        
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
            
            $dec_search_word = $this->input->post('search_word');
            $secret_key  = $this->config->item('new_encryption_key');
            $secret_iv   = md5('serial_iv');
            $search_word = $this->encryption->encrypt($dec_search_word, $secret_key, $secret_iv);
            
        }
        else
        {
            $search_word = '';
        }
        
        
        $grid_data  = $this->invalid_serials_model->get_invalid_serials_data($lang_id, $limit, $offset, $search_word, $this->stores_ids);
        
        $db_columns = array(
                               'id',   
                               'purchase_order_id',
                               'vendor',
                               'product_name',
                               'country_id' ,
                               'serial',
                               'unix_time',
                               'order_id',
                               'order_date',
                               'sent_to_vendor',
                               'invalid_status'
                           );
                       
       $this->data['hidden_fields'] = array('id');
                                           
       $new_grid_data = array();
        
        foreach($grid_data as $key =>$row)
        { 
            
            foreach($db_columns as $column)
            {
                //$order_data          = $this->invalid_serials_model->get_invalid_serial_order_data($row->product_serial_id);
                $purchase_order_data = $this->purchase_orders_model->get_purchase_order_vendor_data($lang_id, $row->purchase_order_id);
                
                if($column == 'serial')
                {
                    $secret_key  = $this->config->item('new_encryption_key');
                    $secret_iv   = md5('serial_iv');
                    $dec_serials = $this->encryption->decrypt($row->serial, $secret_key, $secret_iv);
                    
                    $new_grid_data[$key][$column] = $dec_serials;
                }
                elseif($column == 'vendor')
                {
                    $new_grid_data[$key][$column] = $purchase_order_data->vendor;
                }
                elseif($column == 'country_id')
                {
                    $country_name = $this->countries_model->get_country_name($row->country_id, $lang_id);
                    $new_grid_data[$key][$column] = $country_name;
                }
                elseif($column == 'unix_time')
                {
                    $new_grid_data[$key][$column] = date('Y/m/d H:i', $row->unix_time);
                }
                elseif($column == 'order_id')
                {
                    //echo $row->product_serial_id.'<br>'; print_r($order_data); 
                    $new_grid_data[$key][$column] = $row->order_id;
                }
                elseif($column == 'order_date')
                {
                    $new_grid_data[$key][$column] = date('Y/m/d H:i', $row->order_date);
                }
                elseif($column == 'sent_to_vendor')
                {
                    if($row->sent_to_vendor == 1)
                    {
                        $sent_value = '<span class="badge badge-success">'.lang('yes').'</span>';
                    }
                    else
                    {
                        $sent_value = '<span class="badge badge-danger">'.lang('no').'</span>';
                    }
                    
                    $new_grid_data[$key][$column] = $sent_value;
                }
                else
                {
                    $new_grid_data[$key][$column] = $row->{$column};
                }
            }
        }
        
        
        $this->data['grid_data']         = $new_grid_data;
        //$this->data['unset_edit']        = true;
        $this->data['unset_delete']      = true;
        $this->data['display_lang_id']   = $lang_id;
        $this->data['count_all_records'] = $this->invalid_serials_model->get_count_all_invalid_serials($lang_id, $search_word);
        
        $output_data = $this->load->view('Admin/grid/grid_data',$this->data, true);
        $count_data  = $this->data['count_all_records'];
       
        echo json_encode(array($output_data, $count_data, $search_word));
    }
    
    public function read($id, $display_lang_id)
    {
        $id              = intval($id);
        $display_lang_id = intval($display_lang_id);
        
        if($id && $display_lang_id)
        {
            $data = $this->invalid_serials_model->get_row_data($id, $display_lang_id);
            
            if($data)
            {
                if(in_array($data->store_id, $this->stores_ids))
                {
                    $purchase_order_data = $this->purchase_orders_model->get_purchase_order_vendor_data($display_lang_id, $data->purchase_order_id);
                    $country_name        = $this->countries_model->get_country_name($data->country_id, $display_lang_id);
                    
                    $secret_key  = $this->config->item('new_encryption_key');
                    $secret_iv   = md5('serial_iv');
                    $dec_serials = $this->encryption->decrypt($data->serial, $secret_key, $secret_iv);
                    
                    if($data->sent_to_vendor == 1)
                    {
                        $sent_to_vendor = '<span class="badge badge-success">'.lang('yes').'</span>';
                    }
                    else
                    {
                        $sent_to_vendor = '<span class="badge badge-danger">'.lang('no').'</span>';
                    }
                
                
                     $row_data = array(
                                        lang('purchase_order_id') => $data->purchase_order_id               ,
                                        lang('vendor')            => $purchase_order_data->vendor           ,
                                        lang('product')           => $data->product_name                    ,
                                        lang('country')           => $country_name                          ,
                                        lang('serial')            => $dec_serials                           ,
                                        lang('serial_add_date')   => date('Y/m/d', $data->unix_time)        ,
                                        lang('order_id')          => $data->order_id                        ,
                                        lang('order_date')        => date('Y/m/d H:i', $data->order_date)   ,
                                        lang('status')            => $data->status                          ,
                                        lang('sent_to_vendor')    => $sent_to_vendor
                                     );
                                     
                    $replaced_serial_data = $this->invalid_serials_model->get_replaced_serial_data($id);
            
                    if(count($replaced_serial_data) != 0)
                    {
                        $replaced_serial                = $this->encryption->decrypt($replaced_serial_data->serial, $secret_key, $secret_iv);                    
                        $replaced_serial_edit_link      = '<a href="'.base_url().'/products/admin_products_serials/edit/'.$replaced_serial_data->id.'" target="_blank">'.$replaced_serial.'</a>';
                        
                        $row_data['replaced_serial']    = $replaced_serial_edit_link; 
                    }
                
                    $this->data['row_data'] = $row_data;
                }
                else
                {
                    $this->data['error_msg'] = lang('no_store_permission');
                }
                
                $this->data['content']  = $this->load->view('Admin/grid/read_view', $this->data, true);
                $this->load->view('Admin/main_frame',$this->data);
            }
        }
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
                
                if(isset($_POST['new_serial']) && $_POST['new_serial'] != '')
                {
                    $this->form_validation->set_rules('new_serial', lang('serial'), 'trim|callback_check_serial_before_update');
                }
                
                $this->form_validation->set_rules('invalid_status_id', lang('status'), 'trim');
                $this->form_validation->set_rules('sent_to_vendor', lang('sent_to_vendor'), 'trim');
                
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            }
            
            if($this->form_validation->run() == FALSE)
    		{
    		   $this->_edit_form($id, $validation_msg);
            }
            else
            {
                $invalid_status_id = $this->input->post('invalid_status_id');
                
                $updated_data      = array(
                                            'invalid_status_id' => $invalid_status_id,
                                            'sent_to_vendor'    => (isset( $_POST['sent_to_vendor']))? $_POST['sent_to_vendor']:0,
                                            'last_update_time'  => time()
                                            
                                          );
                
                $this->products_serials_model->update_serial($id, $updated_data);
                
                if(isset($_POST['new_serial']) && $_POST['new_serial'] != '')
                {
                    $lang_id     = $this->data['active_language']->id;
                    $new_serial  = $this->input->post('new_serial');
                    $secret_key  = $this->config->item('new_encryption_key');
                    $secret_iv   = md5('serial_iv');
                    $enc_serial  = $this->encryption->encrypt($new_serial, $secret_key, $secret_iv);
                    
                    $data    = $this->invalid_serials_model->get_row_data($id, $lang_id);
                    
                    $products_serials_data  = array(
                                                     'purchase_order_id'     => $data->purchase_order_id ,
                                                     'product_id'            => $data->product_id        ,
                                                     'country_id'            => $data->country_id        ,
                                                     'serial'                => $enc_serial              ,
                                                     'unix_time'             => time()                   ,
                                                     'last_update_time'      => time()                   ,
                                                     'active'                => 1                        ,
                                                     'invalid_serial_ref_id' => $id
                                                  );
                
                    $this->products_serials_model->insert_products_serials($products_serials_data);
                    
                    //update product quantity
                    
                    $product_new_stock = $this->products_serials_model->get_product_available_serial_count($data->product_id, $data->country_id);
                    $product_updated_data['product_quantity'] = $product_new_stock;
                    
                    $this->products_model->update_products_countries($data->product_id, $data->country_id, $product_updated_data);
                }
                
                $_SESSION['success'] = lang('updated_successfully');
                $this->session->mark_as_flash('success');
               
                redirect('products/admin_invalid_serials', 'refresh');
            }
        }
    }
    
    private function _edit_form($id, $validation_msg)
    {
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }
        
        $this->data['mode']               = 'edit';
        $this->data['form_action']        = $this->data['module'] . "/" . $this->data['controller'] . "/edit/" . $id;
        $this->data['id']                 = $id;
        
        $lang_id = $this->data['active_language']->id;
        $data    = $this->invalid_serials_model->get_row_data($id, $lang_id);
            
        if($data)
        {
            if(in_array($data->store_id, $this->stores_ids))
            {
                $invalid_status      = $this->invalid_serials_model->get_invalid_status($lang_id);
                $purchase_order_data = $this->purchase_orders_model->get_purchase_order_vendor_data($lang_id, $data->purchase_order_id);
                $country_name        = $this->countries_model->get_country_name($data->country_id, $lang_id);
                
                $secret_key  = $this->config->item('new_encryption_key');
                $secret_iv   = md5('serial_iv');
                $dec_serials = $this->encryption->decrypt($data->serial, $secret_key, $secret_iv);
                
                $data->{'serial'}  = $dec_serials;
                $data->{'country'} = $country_name;
                $data->{'vendor'}  = $purchase_order_data->vendor;
                
                $status_array      = array();
                
                foreach($invalid_status as $status)
                {
                    $status_array[$status->status_id] = $status->status;
                }
                
                $replaced_serial_data = $this->invalid_serials_model->get_replaced_serial_data($id);
            
                if(count($replaced_serial_data) != 0)
                {
                    $replaced_serial           = $this->encryption->decrypt($replaced_serial_data->serial, $secret_key, $secret_iv);                    
                    $replaced_serial_edit_link = '<a href="'.base_url().'/products/admin_products_serials/edit/'.$replaced_serial_data->id.'" target="_blank">'.$replaced_serial.'</a>';
                    
                    $this->data['replaced_serial'] = $replaced_serial_edit_link; 
                }
                
                
                $this->data['general_data']   = $data;
                $this->data['status_options'] = $status_array;
            }
            else
            {
                $this->data['error_msg'] = lang('no_store_permission');
            }
        }
        else
        {
            $this->data['error'] = lang('no_data');
        }
        
        $this->data['content']  = $this->load->view('invalid_serials_form', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
    }
    
    public function check_serial_before_update($serial)
    {
        $secret_key  = $this->config->item('new_encryption_key');
        $secret_iv   = md5('serial_iv');
        
        $enc_serial  = $this->encryption->encrypt($serial, $secret_key, $secret_iv);
        
        $is_exist_serial = $this->products_serials_model->check_if_exist_serial($enc_serial);
        
        if ($is_exist_serial)
        {
            $this->form_validation->set_message('check_serial_before_update', lang('serial_already_exist'));
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }
    
     
     
     
     
  
   
/************************************************************************/    
}