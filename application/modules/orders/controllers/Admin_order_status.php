<?php 
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Admin_order_status extends CI_Controller
{
    public $crud;
    public $lang_row;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->crud = new grocery_CRUD();
        $params     = array($this->crud);
        
        require(APPPATH . 'includes/global_vars.php');
        
        $this->load->model('order_status_model');
        
        $this->lang_row = $this->admin_bootstrap->get_active_language_row(); 
    }

    

    public function index()
    {   
        $lang_id = $this->data['active_language']->id;
        
        $this->data['count_all_records'] = $this->order_status_model->get_count_all_status($lang_id);
        $this->data['data_language']     = $this->lang_model->get_active_data_languages();
        
        
        $this->data['columns']           = array(
                                                     lang('status'),
                                                     lang('name')
                                                   );
            
        $this->data['orders']            = array(
                                                     lang('date'),
                                                     lang('status'),
                                                     lang('name')
                                                   );
        
        //$this->data['actions']           = array( 'delete'=>lang('delete'));
        
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
        
        
        
        if(isset($_POST['search_word']) || trim($_POST['search_word']) == '')
        { 
            $search_word = $this->input->post('search_word');
        }
        else
        {
            $search_word = '';
        }
        
        if(isset($_POST['order_by']))
        {
            $order_by = $this->input->post('order_by');
        }
        else
        {
            $order_by = '';
        }
        
        if(isset($_POST['order_state']))
        {
            $order_state = $this->input->post('order_state');
        }
        else
        {
            $order_state = 'desc';
        }
        
        
        $grid_data                  = $this->order_status_model->get_order_status_data($lang_id,$limit,$offset,$search_word,$order_by,$order_state);
        
        $db_columns                 = array(
                                             'id',   
                                             'status',
                                             'name'
                                           );
                       
        $this->data['hidden_fields'] = array('id');
        
        $new_grid_data = array();
        
        foreach($grid_data as $key =>$row)
        { 
            foreach($db_columns as $column)
            {
                
                $new_grid_data[$key][$column] = $row->{$column};
                
            }
        }
        
        $this->data['unset_delete']       = true;
        $this->data['grid_data']          = $new_grid_data; 
        
        $this->data['count_all_records']  = $this->order_status_model->get_count_all_status($lang_id,$search_word);
        
        $this->data['display_lang_id']    = $lang_id;
         
        
        $count_data  = $this->data['count_all_records'];
        $output_data = $this->load->view('Admin/grid/grid_data',$this->data, true);
        
        echo json_encode(array($output_data, $count_data, $search_word));
     }
     
     public function add_form()
     {
        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/save";
       
        $this->data['content']      = $this->load->view('order_status', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
        
     }
     
     public function save()
     {
        $languages      = $this->input->post('lang_id');
        
        foreach($languages as $lang_id)
        {  
            $this->form_validation->set_rules('status_translation['.$lang_id.']', lang('status_translation'), 'required');
		
        }
        
        $this->form_validation->set_rules('status', lang('status'), 'required');
        $this->form_validation->set_message('required', lang('required'));
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        
        if ($this->form_validation->run() == FALSE)
		{  
            $this->data['form_action'] = $this->data['module'] . "/" . $this->data['controller'] . "/save";
            
            $this->data['content']     = $this->load->view('order_status', $this->data, true);
            $this->load->view('Admin/main_frame',$this->data);
		
        }else{
		  
            $status     = $this->input->post('status');
            $data           = array('status'  => $status,);
            
            if($this->order_status_model->insert_order_status($data))
            {
            
                $last_insert_id = $this->db->insert_id();
                $status_translation = $this->input->post('status_translation');
                               
                foreach($languages as $lang_id)
                {
                    $status_translation_data = array(
                                                        'status_id'     => $last_insert_id ,
                                                        'name'          => $status_translation[$lang_id],
                                                        'lang_id'       => $lang_id ,
                                                     );
                    $this->order_status_model->insert_status_translation($status_translation_data);
                }
                
                $this->session->set_flashdata('success',lang('success'));
               
                redirect('orders/admin_order_status/index','refresh');
           }
        }
     }
     
     public function edit_form($id)
     {
        $id = intval($id);
        
        if($id)
        {
            $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/update";
            $this->data['id']           = $id;
            $lang_id                    = $this->data['active_language']->id;
            
            $general_data               = $this->order_status_model->get_status_row($id);
            $data                       = $this->order_status_model->get_status_translation($id);
            $filtered_data              = array();

            foreach($data as $row)
            {
                $filtered_data[$row->lang_id] = $row;
            }
            
            $this->data['data']                 = $filtered_data;
            $this->data['general_data']         = $general_data;
            
            $this->data['content']              = $this->load->view('order_status', $this->data, true);
            $this->load->view('Admin/main_frame',$this->data);
        }
     }
     
     public function update()
     {
        $status_id      = $this->input->post('status_id');
        $languages      = $this->input->post('lang_id');
        
        foreach($languages as $lang_id)
        {
            $this->form_validation->set_rules('status_translation['.$lang_id.']', lang('status_translation'), 'required');
        }
        
        $this->form_validation->set_rules('status', lang('status'), 'required');
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
         
        if($this->form_validation->run() == FALSE)
        { 
            $this->data['id']           = $status_id;
            $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/update";
            
            $this->data['content']     = $this->load->view('order_status', $this->data, true);
            $this->load->view('Admin/main_frame',$this->data);
        }
        else
        { 
            $status = $this->input->post('status');
            $data   = array('status'  => $status);
           
            $this->order_status_model->update_status($status_id, $data);
           
            $status_translation = $this->input->post('status_translation');
             
            foreach($languages as $lang_id)
            {
                $status_translation_data = array(
                                                    'name' => $status_translation[$lang_id]
                                                  );
                                                  
                $this->order_status_model->update_status_translation($status_id, $lang_id, $status_translation_data);
            }
            
            redirect('orders/admin_order_status/index','refresh');   
        }
       
        
        
     }
     
     public function read($id,$display_lang_id)
     {
        $id              = intval($id);
        $display_lang_id = intval($display_lang_id);
        
        if($id && $display_lang_id)
        {
            $data     = $this->order_status_model->get_row_data($id,$display_lang_id);
            $row_data = array(
                                lang('status') => $data->status ,
                                lang('name')   => $data->name 
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
        $status_ids = $this->input->post('row_id');

        if(is_array($status_ids))
        { 
            
            $ids_array = array();
            
            foreach($status_ids as $row)
            {
                $ids_array[] = $row['value'];
            }
        }
        else
        { 
            $ids_array = array($status_ids);
        }
            
        $this->order_status_model->delete_status_data($ids_array);
        
     }  
     
     
   
/************************************************************************/    
}