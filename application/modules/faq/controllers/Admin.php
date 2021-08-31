<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Admin extends CI_Controller
{
    public $lang_row;
    public function __construct()
    {
        parent::__construct();
        
        require(APPPATH . 'includes/global_vars.php');
        
        $this->load->model('admin_faq_model');
               
        $this->lang_row = $this->admin_bootstrap->get_active_language_row(); 
    }

    

    public function index()
    {   
        $lang_id = $this->data['active_language']->id;
        
        $this->data['count_all_records'] = $this->admin_faq_model->get_count_all_faq($lang_id);
        $this->data['data_language']     = $this->lang_model->get_active_data_languages();
        
       
        $this->data['columns']           = array(
                                                     lang('question'),
                                                     lang('unix_time'),
                                                     lang('active')
                                                   );
            
        $this->data['orders']           = $this->data['columns'];
        
        $this->data['actions']           = array( 'delete'=>lang('delete'));
        
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
        
        
        $grid_data                  = $this->admin_faq_model->get_faq_data($lang_id,$limit,$offset,$search_word,$order_by,$order_state);
        
        $db_columns                 = array(
                                             'id',   
                                             'question',
                                             'unix_time',
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
                    
                    
                }elseif($column == 'unix_time'){
                    
                    $new_grid_data[$key][$column] = date('Y/m/d H:i',$row->unix_time);
                    
                } else{
                    
                    $new_grid_data[$key][$column] = $row->{$column};
                }
                
                
            }
        }
        
        $this->data['grid_data']                  = $new_grid_data; 
        
        $this->data['count_all_records']  = $this->admin_faq_model->get_count_all_faq($lang_id,$search_word);
        
        $this->data['display_lang_id']    = $lang_id;
         
        
        $count_data  = $this->data['count_all_records'];
        $output_data = $this->load->view('Admin/grid/grid_data',$this->data, true);
        
        echo json_encode(array($output_data, $count_data, $search_word));
     }
     
     public function read($id,$display_lang_id)
     {
        $id              = intval($id);
        $display_lang_id = intval($display_lang_id);
        
        if($id && $display_lang_id)
        {
            $data     = $this->admin_faq_model->get_row_data($id,$display_lang_id);
            
            $active_value = '';
            
            if($data->active == 0)
            {
                $active_value = '<span class="badge badge-danger">'.lang('not_active').'</span>';    
            }
            elseif($data->active = 1)
            {
                $active_value = '<span class="badge badge-success">'.lang('active').'</span>';
            }
            
            $row_data = array(
                                lang('question')      => $data->question ,
                                lang('answer')        => $data->answer ,
                                lang('unix_time')     => date('Y/m/d H:i',$data->unix_time),
                                lang('active')        => $active_value
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
        $faq_ids = $this->input->post('row_id');

        if(is_array($faq_ids))
        { 
            
            $ids_array = array();
            
            foreach($faq_ids as $country_id)
            {
                $ids_array[] = $country_id['value'];
            }
        }
        else
        { 
            $ids_array = array($faq_ids);
        }
            
        $this->admin_faq_model->delete_faq_data($ids_array);
        
     }  
     
   
     /*******************************************************************************/
     
     public function add()
     {
        $validation_msg = false;
        
        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
        {
            $validation_msg = true;
            
            $languages      = $this->input->post('lang_id');
        
            foreach($languages as $lang_id)
            {  
                $this->form_validation->set_rules('question['.$lang_id.']' ,lang('question') , 'required');
                $this->form_validation->set_rules('answer['.$lang_id.']' , lang('answer') , 'required');
    		
            }
            
            $this->form_validation->set_message('required', lang('required'));
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        }
        
        if ($this->form_validation->run() == FALSE)
		{
		  $this->_add_form($validation_msg);
        }
        else
        {
            $data = array(
                            'active'    => (isset( $_POST['active']))? $this->input->post('active'):0,
                            'unix_time' => time(),
                         );
             
            if($this->admin_faq_model->insert_faq($data))
            {
                $last_insert_id  = $this->db->insert_id();
                $question        = $this->input->post('question');
                $answer          = $this->input->post('answer');
                               
                foreach($languages as $lang_id)
                {
                    $faq_translation_data = array(
                                                    'faq_id'     => $last_insert_id ,
                                                    'question'   => $question[$lang_id],
                                                    'answer'     => $answer[$lang_id],
                                                    'lang_id'    => $lang_id ,
                                                  );
                    $this->admin_faq_model->insert_faq_translation($faq_translation_data);
                }
                
                $_SESSION['success'] = lang('success');
                $this->session->mark_as_flash('success');
               
                redirect('faq/admin/','refresh');
           }
        }
    }
    
    private function _add_form($validation_msg)
    {
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }
        
        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/add";
      
        $this->data['content']      = $this->load->view('admin_faq', $this->data, true);
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
                $validation_msg = true;
                
                $faq_id    = $this->input->post('id');
                $languages = $this->input->post('lang_id');
                
                foreach($languages as $lang_id)
                {
                    $this->form_validation->set_rules('question['.$lang_id.']' ,lang('question') , 'required');
                    $this->form_validation->set_rules('answer['.$lang_id.']' , lang('answer') , 'required');
        		
                }
                
                $this->form_validation->set_message('required', lang('required'));
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            }
            
            if($this->form_validation->run() == FALSE)
    		{
    		   $this->_edit_form($id, $validation_msg);
            }
            else
            {
                $data           = array(
                                        'active'    => (isset( $_POST['active']))? $this->input->post('active'):0,
                                       );
            
                $this->admin_faq_model->update_faq($faq_id,$data);
               
                $question = $this->input->post('question');
                $answer   = $this->input->post('answer');
                            
                foreach($languages as $lang_id)
                {
                    $faq_translation_data = array(
                                                   'question'   => $question[$lang_id],
                                                   'answer'     => $answer[$lang_id],
                                                 );
                                                      
                    $this->admin_faq_model->update_faq_translation($faq_id,$lang_id,$faq_translation_data);
                }
                
                $_SESSION['success'] = lang('updated_successfully');
                $this->session->mark_as_flash('success');
                
                redirect('faq/admin/','refresh');
            }
        }
        
        
    }
    
    private function _edit_form($id, $validation_msg)
    {
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }
        
        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/edit/" . $id;
        $this->data['id']           = $id;
        $this->data['general_data'] = $this->admin_faq_model->get_faq_result($id);
        
        $data                       = $this->admin_faq_model->get_faq_translation_result($id);
        $filtered_data              = array();
        
        foreach($data as $row)
        {
            $filtered_data[$row->lang_id] = $row;
        }
        $this->data['data'] = $filtered_data;
        
        $this->data['content'] = $this->load->view('admin_faq', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
    }
     
     
    
/************************************************************************/    
}