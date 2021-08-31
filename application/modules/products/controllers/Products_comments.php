<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Products_comments extends CI_Controller
{
    public $stores;
    public $stores_ids;
    
    public function __construct()
    {
        parent::__construct();
        
        require(APPPATH . 'includes/global_vars.php');
        
        $this->load->model('products_model');
        
        $this->lang_row = $this->admin_bootstrap->get_active_language_row();
        $this->stores   = $this->admin_bootstrap->get_user_available_stores();
        
        $store_id_array = array();
        
        foreach($this->stores as $store)
        {
            $store_id_array[] = $store->store_id;
        }
        
        $this->stores_ids = $store_id_array;
        
        $this->view_folder = 'Admin';
        
        if($this->data['store_owner'] == 1)
        {
            $this->view_folder = 'Sell';
        }
        
    }

    
    private function _js_and_css_files()
    {
        $this->data['css_files'] = array();
        
        $this->data['js_files']  = array();
        
        
        $this->data['js_code'] = "";
    }

    
    public function index($product_id)
    {
        $product_id      = intval($product_id);
        $index_method_id = $this->data['method_id'];
        $lang_id         = $this->data['active_language']->id;
        
        $this->data['count_all_records']  = $this->products_model->get_count_all_product_comments($product_id, $this->stores_ids);
        $this->data['data_language']      = $this->lang_model->get_active_data_languages();  
        
        $options = '';
        
        //$this->data['filters']            = array();
        
        
        
        $this->data['columns']            = array(
                                                     lang('name')       ,
                                                     lang('product_name') ,
                                                     lang('approved')   ,
                                                     lang('date')
                                                     
                                                   );
        
        $this->data['orders']         = array();
        
        $this->data['actions']         = array( 'delete'=>lang('delete'));
        $this->data['search_fields']   = array( lang('name'));
        
        $this->data['index_method_id'] = $index_method_id;
        $this->data['product_id']      = $product_id;
        
        $this->data['content']  = $this->load->view($this->view_folder.'/grid/grid_html', $this->data, true);
        $this->load->view($this->view_folder.'/main_frame',$this->data);
    }
    
    public function ajax_list()
    {
        $product_id = intval($_POST['product_id']);
         
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
        
        
        if(isset($_POST['search_word']) || trim($_POST['search_word']) == '')
        { 
            $search_word = $this->input->post('search_word');
        }
        else
        {
            $search_word = '';
        }
        
        
        
        if(isset($_POST['order_state']))
        {
            $order_state = $this->input->post('order_state');
        }
        else
        {
            $order_state = 'desc';
        }
        
        $grid_data  = $this->products_model->get_product_comments_data($lang_id, $limit, $offset, $search_word, $order_state, $product_id, $this->stores_ids);
        
        $db_columns = array(
                             'id'           ,   
                             'username'    ,
                             'product_name' ,
                             'approved'     ,
                             'unix_time'
                             
                           );
        
        $this->data['hidden_fields'] = array('id');
        
        $new_grid_data = array();
        
        foreach($grid_data as $key =>$row)
        { 
            foreach($db_columns as $column)
            {
                if($column == 'approved')
                {
                    if($row->{$column} == 1)
                    {
                        $new_grid_data[$key][$column] = "<span class='badge badge-success'>".lang('yes')."</span>";    
                    }
                    else
                    {
                        $new_grid_data[$key][$column] = "<span class='badge badge-danger'>".lang('no')."</span>";
                    }
                }
                elseif($column == 'unix_time')
                {
                   $new_grid_data[$key][$column] = date('Y / m / d H:i', $row->unix_time);
                   
                }
                else
                {
                    $new_grid_data[$key][$column] = $row->{$column};
                }
                
            }
        }
        
        $this->data['grid_data']         = $new_grid_data;
        
        $this->data['count_all_records'] = $this->products_model->get_count_all_product_comments($product_id, $this->stores_ids, $search_word);
        
        $this->data['display_lang_id']   = $lang_id;
         
        $output_data = $this->load->view($this->view_folder.'/grid/grid_data',$this->data, true);
        $count_data  = $this->data['count_all_records'];
        
        echo json_encode(array($output_data, $count_data, $search_word));
    }
    
    public function read($id, $display_lang_id)
    {
        $id              = intval($id);
        $display_lang_id = intval($display_lang_id);
        
        if($id && $display_lang_id)
        {
            $data = $this->products_model->get_product_comment_row_data($id,$display_lang_id);
            
            
            if(count($data) != 0)
            {
                if(in_array($data->store_id, $this->stores_ids))
                {
                
                    if($data->approved == 1)
                    {
                        $approved = '<span class="badge badge-success">'.lang('yes').'</span>';
                    }
                    elseif($data->approved == 0)
                    {
                        $approved = '<span class="badge badge-danger">'.lang('no').'</span>';
                    }
                    
            
                    $row_data = array(
                                    lang('product_name') => $data->product_name,
                                    lang('name')         => $data->username,
                                    lang('comment')      => $data->comment,
                                    lang('approved')     => $approved,
                                    lang('date')         => date('d-m-Y', $data->unix_time),
                                    
                                 );
                    
                    $this->data['row_data'] = $row_data;
                }
                else
                {
                    $this->data['error'] = lang('no_store_permission');
                }
            }
            else
            {
                $this->data['error'] = lang('no_data');
            }
            
            $this->data['content'] = $this->load->view($this->view_folder.'/grid/read_view', $this->data, true);
            $this->load->view($this->view_folder.'/main_frame',$this->data);
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
        /**************************************/
        if($this->data['method_id'] == 0)
        {
            $delete_method_id = $this->admin_bootstrap->get_a_controller_method_id($this->data['controller_id'], 'delete');
        }
        else
        {
            $delete_method_id = $this->data['method_id'];
        }
        
        $this->stores   = $this->admin_bootstrap->get_user_available_stores($delete_method_id);
        $store_id_array = array();
        
        foreach($this->stores as $store)
        {
            $store_id_array[] = $store->store_id;
        }
        
        $this->stores_ids = $store_id_array;
        /**************************************/
        
        $rows_ids = $this->input->post('row_id');
        
        $stores_ids = array();
        
        if(is_array($rows_ids))
        { 
            
            $ids_array = array();
            
            foreach($rows_ids as $row_id)
            {
                
                $ids_array[]  = $row_id['value'];
            }
        }
        else
        { 
            $ids_array    = array($products_ids);
        }
        $this->products_model->delete_product_comment_data($ids_array);
        
        
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
                
                $this->form_validation->set_rules('product_id', lang('product_name'), 'required');                
                
                $this->form_validation->set_message('required', lang('required')."  : %s ");
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            }
        }
        
        if($this->form_validation->run() == FALSE)
		{
		   $this->_edit_form($id, $validation_msg);
        }
        else
        {
            
            /*******************general data**********************/
            $product_id = intval($this->input->post('product_id', true));
            
            $general_data  = array(
                                    'approved' => (isset( $_POST['approved']))? $_POST['approved']:0,   
                                  );
            
            $this->products_model->update_product_comments($id, $general_data);
            
            
            /**********************************************************/
                                                                                
            
           $_SESSION['success'] = lang('updated_successfully');
           $this->session->mark_as_flash('success');
           
           redirect('products/products_comments/index/'.$product_id,'refresh');
        }
    }
    
    private function _edit_form($id, $validation_msg)
    {
        $this->_js_and_css_files();
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }
        
        $this->data['mode']               = 'edit';
        $this->data['form_action']        = $this->data['module'] . "/" . $this->data['controller'] . "/edit/" . $id;
        $this->data['id']                 = $id;
        
        $lang_id                          = $this->lang_row->id;
                   
        $general_data                     = $this->products_model->get_product_comment_row_data($id, $lang_id);
        
        if(in_array($general_data->store_id, $this->stores_ids))
        {
           $this->data['general_data']                     = $general_data ;
            
        }
        else
        {
            $this->data['error_msg'] = lang('no_store_permission');
        }   
        $this->data['content'] = $this->load->view('product_comment', $this->data, true);
        $this->load->view($this->view_folder.'/main_frame',$this->data);
    }
    
    
/************************************************************************/    
}