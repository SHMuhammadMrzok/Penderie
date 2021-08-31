<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Admin_Bank_accounts extends CI_Controller
{
    public $crud;
    
    public function __construct()
    {
        parent::__construct();
        
        require(APPPATH . 'includes/global_vars.php');
        $this->load->model('bank_accounts_model');
        $this->load->model('payment_methods_model');
    }
    
    /******************* list functions ****************************/
    
    public function index()
    {
        $lang_id = $this->data['active_language']->id;
        
        $this->data['count_all_records'] = $this->bank_accounts_model->get_count_all_accounts();
        $this->data['data_language']     = $this->lang_model->get_active_data_languages();
              
        $this->data['columns']           = array(
                                                  lang('bank_name'),
                                                  lang('account_name'),
                                                  lang('account_number'),
                                                  lang('thumbnail'),
                                                  lang('active'),
                                                );
            
        $this->data['actions']           = array( 'delete'=>lang('delete'));
        $this->data['search_fields']     = array( lang('bank_name'), lang('bank_account_name'), lang('bank_account_number'));
        
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
            $search_word = $this->input->post('search_word');
        }
        else
        {
            $search_word = '';
        }
        
        
        
        $grid_data  = $this->bank_accounts_model->get_bank_accounts_data($lang_id, $limit, $offset, $search_word);
        
        $db_columns = array(
                             'id',   
                             'bank',
                             'account_name',
                             'account_number',
                             'image',
                             'active'
                           );
                       
        $this->data['hidden_fields'] = array('id');
        
        $new_grid_data = array();
        
        foreach($grid_data as $key =>$row)
        { 
            foreach($db_columns as $column)
            {
                if($column == 'active')
                {
                    if($row->active == 0)
                    {
                        $new_grid_data[$key][$column] = '<span class="badge badge-danger">'.lang('not_active').'</span>';    
                    }
                    elseif($row->active = 1)
                    {
                        $new_grid_data[$key][$column] = '<span class="badge badge-success">'.lang('active').'</span>';
                    }
                    
                    
                }elseif($column == 'image')
                {
                    if($row->image != '')
                    {
                        $image = "<a href='".$this->data['images_path'].$row->image."' class='image-thumbnail' ><img src='".$this->data['images_path'].$row->image."' width='150' height='50'  /></a>";
                    }
                    else
                    {
                        $image = "<a href='".base_url()."assets/template/admin/img/banks.png/' class='image-thumbnail' ><img src='".base_url()."assets/template/admin/img/banks.png' width='150' height='50'  /></a>";
                    } 
                    
                    $new_grid_data[$key][$column] = $image;
                }
                else
                {
                    $new_grid_data[$key][$column] = $row->{$column};
                }
                
            }
        }
        
        $this->data['grid_data']          = $new_grid_data; 
        
        $this->data['count_all_records']  = $this->bank_accounts_model->get_count_all_accounts($search_word);
        $this->data['display_lang_id']    = $lang_id; 
        
        $count_data  = $this->data['count_all_records'];
        $output_data = $this->load->view('Admin/grid/grid_data',$this->data, true);
        
        echo json_encode(array($output_data, $count_data, $search_word));
     }
     
     
     public function read($id,$display_lang_id)
     {
        $id = intval($id);
        $display_lang_id = intval($display_lang_id);
        $pic = '';
        if($id && $display_lang_id)
        {
            $data     = $this->bank_accounts_model->get_row_data($id,$display_lang_id);
           
            if($data->active == 0)
            {
                $active_value = '<span class="badge badge-danger">'.lang('not_active').'</span>';    
            }
            elseif($data->active = 1)
            {
                $active_value = '<span class="badge badge-success">'.lang('active').'</span>';
            }
            if($data->image !='')
            {
                $pic = "<a href='".$images_path.$data->image."' class='image-thumbnail' ><img src='".$images_path.$data->image."' width='150' height='50'  /></a>";
            }
            
            if($data->gateway_status == 1)
            {
                $gateway_status = lang('yes');
            }
            else
            {
                $gateway_status = lang('no');
            }
            
            $row_data = array(

                                lang('bank_name')       => $data->bank                          ,
                                lang('account_name')    => $data->account_name                  ,
                                lang('account_number')  => $data->account_number                ,
                                lang('gateway_status')  => $gateway_status                      ,
                                lang('min_order_value') => $data->min_order_value               ,
                                lang('thumbnail')       => $pic,
                                lang('unix_time')       => date('Y-m-d H:i:s',$data->unix_time) ,
                                lang('active')          => $active_value
                             );
        
            $this->data['row_data'] = $row_data;
            
            $this->data['content']  = $this->load->view('Admin/grid/read_view', $this->data, true);
            $this->load->view('Admin/main_frame',$this->data);
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
        $acounts_ids = $this->input->post('row_id');

        if(is_array($acounts_ids))
        { 
            
            $ids_array = array();
            
            foreach($acounts_ids as $account_id)
            {
                $ids_array[] = $account_id['value'];
            }
        }else{ 
            
            $ids_array = array($acounts_ids);
        }
            
        //check orders banks before delete
        $banks_orders_count = $this->bank_accounts_model->count_banks_orders($ids_array);
        
        if($banks_orders_count != 0)
        {
            echo lang('cant_delete_bank_used_in_order');
        }
        else
        {
            $this->bank_accounts_model->delete_bank_accounts_data($ids_array);
            echo 1;
        }
        
     }  
     
     /***********************ADD & Edit Functions ************************/
    
     private function _js_and_css_files()
    {
        $this->data['css_files'] = array();
        
        $this->data['js_files']  = array(
            //TouchSpin
            'global/plugins/fuelux/js/spinner.min.js',
            'global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js',
            'global/plugins/bootstrap-touchspin/bootstrap.touchspin.js',
            
            );
        
        
        $this->data['js_code'] = 'ComponentsPickers.init()';
    }
    
    public function add()
    {
        $this->_js_and_css_files();
        $validation_msg = false;
        
        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
        {
            $languages = $this->input->post('lang_id');
            foreach($languages as $lang_id)
            { 
                $this->form_validation->set_rules('bank['.$lang_id.']', lang('bank_name'), 'trim|required');
                
            }
            $this->form_validation->set_rules('account_name' , lang('account_name') , 'required');
            $this->form_validation->set_rules('account_number', lang('account_number'), 'required');
            
            $this->form_validation->set_message('required', lang('required')." : %s ");
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            $validation_msg = true;
        }
        
        if ($this->form_validation->run() == FALSE)
		{
		  $this->_add_form($validation_msg);
        }
        else
        {
            $bank_accounts_data = array(
                                            'account_name'    => $this->input->post('account_name') ,
                                            'account_number'  => $this->input->post('account_number') ,
                                            'unix_time'       => time(),
                                            'image'           => $this->input->post('image'),
                                            'active'          => (isset( $_POST['active']))? $this->input->post('active'):0,
                                         );
            
            if($this->bank_accounts_model->insert_bank_accounts_data($bank_accounts_data))
            {
                $last_insert_id = $this->db->insert_id();
                $bank           = $this->input->post('bank');
                
                foreach($languages as $lang_id)
                {
                    $bank_translation_data = array(
                                                    'bank_account_id' => $last_insert_id ,
                                                    'bank'            => $bank[$lang_id],
                                                    'lang_id'         => $lang_id 
                                                  );
                    $this->bank_accounts_model->insert_bank_account_translation($bank_translation_data);
                }                
                
                $this->session->set_flashdata('success',lang('success'));
                redirect('payment_options/admin_bank_accounts/','refresh');
                
            }
        }
    }
    
    private function _add_form($validation_msg)
    {
        $this->_js_and_css_files();
        $lang_id = $this->data['active_language']->id;
        
        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/add";
        
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }
        
        $this->data['content']              = $this->load->view('bank_accounts', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
    }
    
    public function edit($id)
    {
        if(is_numeric($id))
        {
            $id = intval($id);
            
            $validation_msg = false;
            
            if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
            {
                $languages = $this->input->post('lang_id');
                foreach($languages as $lang_id)
                { 
                    $this->form_validation->set_rules('bank['.$lang_id.']', lang('bank_name'), 'trim|required');
                }
                
                $this->form_validation->set_rules('account_name' , lang('account_name') , 'required');
                $this->form_validation->set_rules('account_number', lang('account_number'), 'required');
                
                $this->form_validation->set_message('required', lang('required')." : %s ");
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
                $validation_msg = true;
            }
            
            if($this->form_validation->run() == FALSE)
    		{
    		   $this->_edit_form($id, $validation_msg);
            }
            else
            {
                $bank_accounts_data = array(
                                            'account_name'    => $this->input->post('account_name') ,
                                            'account_number'  => $this->input->post('account_number') ,
                                            'unix_time'       => time(),
                                            'image'           => $this->input->post('image'),
                                            'active'          => (isset( $_POST['active']))? $this->input->post('active'):0,
                                           );
                
                $this->bank_accounts_model->update_bank_accounts_data($id, $bank_accounts_data);
                
                $bank = $this->input->post('bank');
                foreach($languages as $lang_id)
                {
                    $bank_translation_data = array(
                                                    'bank'            => $bank[$lang_id],
                                                  );
                    $this->bank_accounts_model->update_bank_accounts_translation_data($id,$lang_id, $bank_translation_data);
                }                
                
                $this->session->set_flashdata('success',lang('updated_successfully'));
                redirect('payment_options/admin_bank_accounts/','refresh');
            }
        }
    }
    
    private function _edit_form($id, $validation_msg)
    {
        $this->_js_and_css_files();
        $lang_id = $this->data['active_language']->id;
        
        $this->data['mode']         = 'edit';
        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/edit/" . $id;
        $this->data['id']           = $id;
        
        
        $this->data['general_data'] = $this->bank_accounts_model->get_row_data($id ,$lang_id);
        
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }
        
        $this->data['content']              = $this->load->view('bank_accounts', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
    }
    
     
/************************************************************************/    
}