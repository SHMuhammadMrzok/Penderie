<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Admin extends CI_Controller
{
    public $crud;
    public   $lang_row;
    public function __construct()
    {
        parent::__construct();
        
        require(APPPATH . 'includes/global_vars.php');
        
        $this->load->model('static_pages_model');
        
        $this->lang_row = $this->admin_bootstrap->get_active_language_row(); 
    }

    

    public function index()
    {   
        $lang_id = $this->data['active_language']->id;
        
        $this->data['count_all_records'] = $this->static_pages_model->get_count_all_rows($lang_id);
        $this->data['data_language']     = $this->lang_model->get_active_data_languages();
        
        $this->data['columns']           = array(
                                                     lang('title'),
                                                     //lang('page_text'),
                                                     lang('active')
                                                   );
            
        $this->data['orders']            = $this->data['columns'];
    
        
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
        
        
        $grid_data   = $this->static_pages_model->get_static_pages_data($lang_id,$limit,$offset,$search_word,$order_by,$order_state);
        
        $db_columns  = array(
                             'id'        ,   
                             'title'     ,
                             //'page_text' ,
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
                    if($row->{$column} == 0)
                    {
                        $new_grid_data[$key][$column] = '<span class="badge badge-danger">'.lang('not_active').'</span>';    
                    }
                    elseif($row->{$column} == 1)
                    {
                        $new_grid_data[$key][$column] = '<span class="badge badge-success">'.lang('active').'</span>';
                    }
                     
                }
                elseif($column == 'page_text')
                {
                    $new_grid_data[$key][$column] = substr($row->{$column}, 0, 100);
                }
                else
                {
                    $new_grid_data[$key][$column] = $row->{$column};
                }
            }
        }
        
        $this->data['grid_data']          = $new_grid_data; 
        $this->data['unset_delete']       = true;
        
        $this->data['count_all_records']  = $this->static_pages_model->get_count_all_rows($lang_id,$search_word);
        
        $this->data['display_lang_id']    = $lang_id;
         
        
        $count_data  = $this->data['count_all_records'];
        $output_data = $this->load->view('Admin/grid/grid_data',$this->data, true);
        
        echo json_encode(array($output_data, $count_data, $search_word));
     }
     
     public function add()
     {
        $validation_msg = false;
        
        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
        {
            $validation_msg = true;
            
            $languages      = $this->input->post('lang_id');
        
            foreach($languages as $lang_id)
            {  
                $this->form_validation->set_rules('title['.$lang_id.']', 'Title', 'required');
                $this->form_validation->set_rules('page_text['.$lang_id.']', 'page_text', 'required');
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
            $active = $this->input->post('active');
            $data   = array(
                             'active'  => $active, 
                             'unix_time' => time()
                           );
            
            if($this->static_pages_model->insert_static_pages($data))
            {
            
                $last_insert_id = $this->db->insert_id();
                $title          = $this->input->post('title');
                $page_text      = $this->input->post('page_text');
                               
                foreach($languages as $lang_id)
                {
                    $static_page_translation_data = array(
                                                           'page_id'     => $last_insert_id ,
                                                           'title'       => $title[$lang_id],
                                                           'page_text'   => $page_text[$lang_id],
                                                           'lang_id'     => $lang_id ,
                                                         );
                    $this->static_pages_model->insert_static_pages_translation($static_page_translation_data);
                }
                
                $_SESSION['success'] = lang('success');
                $this->session->mark_as_flash('success');
           }
           else
           {
                $_SESSION['error'] = lang('error');
                $this->session->mark_as_flash('error');
           }
           
           
           
           redirect('static_pages/admin/','refresh');
        }
     }
     
     private function _add_form($validation_msg)
     {
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }
        
        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/add";
       
        $this->data['content']      = $this->load->view('form', $this->data, true);
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
                
                $static_page_id = $this->input->post('static_page_id');
                $languages       = $this->input->post('lang_id');
                
                foreach($languages as $lang_id)
                {
                    $this->form_validation->set_rules('title['.$lang_id.']', lang('title'), 'required');
                    $this->form_validation->set_rules('page_text['.$lang_id.']', lang('page_text'), 'required');
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
                $active = $this->input->post('active');
                $data   = array('active' => $active);
               
                $this->static_pages_model->update_static_page($static_page_id, $data);
               
                $title     = $this->input->post('title');
                $page_text = $this->input->post('page_text'); 
                 
                foreach($languages as $lang_id)
                {
                    $static_page_translation_data = array(
                                                           'title'     => $title[$lang_id],
                                                           'page_text' => $page_text[$lang_id],
                                                         );
                                                      
                    $this->static_pages_model->update_static_page_translation($static_page_id, $lang_id, $static_page_translation_data);
                }
                
                $_SESSION['success'] = lang('updated_successfully');
                $this->session->mark_as_flash('success');
                
                redirect('static_pages/admin/','refresh');   
            }
        }
        
        
    }
    
    private function _edit_form($id, $validation_msg)
    {
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }
        
        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/edit/".$id;
        $this->data['id']           = $id;
        $general_data               = $this->static_pages_model->get_static_pages_result($id);
        
        $data                       = $this->static_pages_model->get_static_pages_translation_result($id);
        $filtered_data              = array();
        
        foreach($data as $row)
        {
            $filtered_data[$row->lang_id] = $row;
        }
        
        $this->data['data']                 = $filtered_data;
        $this->data['general_data']         = $general_data;
        
        $this->data['content']              = $this->load->view('form', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
        
        
    }
    
    
     /*public function edit_form($id)
     {
        $id = intval($id);
        
        if($id)
        {
            $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/update";
            $this->data['id']           = $id;
            $general_data               = $this->static_pages_model->get_static_pages_result($id);
            
            $data                       = $this->static_pages_model->get_static_pages_translation_result($id);
            $filtered_data              = array();
            
            foreach($data as $row)
            {
                $filtered_data[$row->lang_id] = $row;
            }
            
            $this->data['data']                 = $filtered_data;
            $this->data['general_data']         = $general_data;
            
            $this->data['content']              = $this->load->view('form', $this->data, true);
            $this->load->view('Admin/main_frame',$this->data);
        }
     }*/
     
     /*public function update()
     {
        $static_page_id = $this->input->post('static_page_id');
        $languages       = $this->input->post('lang_id');
        
        foreach($languages as $lang_id)
        {
            $this->form_validation->set_rules('title['.$lang_id.']', lang('title'), 'required');
            $this->form_validation->set_rules('page_text['.$lang_id.']', lang('page_text'), 'required');
        }
        $this->form_validation->set_message('required', lang('required'));
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
         
        if($this->form_validation->run() == FALSE)
        { 
            $this->data['id']           = $static_page_id;
            $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/update";
            
            $this->data['content']      = $this->load->view('form', $this->data, true);
            $this->load->view('Admin/main_frame',$this->data);
        }
        else
        { 
            $active = $this->input->post('active');
            $data   = array('active' => $active);
           
            $this->static_pages_model->update_static_page($static_page_id,$data);
           
            $title     = $this->input->post('title');
            $page_text = $this->input->post('page_text'); 
             
            foreach($languages as $lang_id)
            {
                $static_page_translation_data = array(
                                                       'title'     => $title[$lang_id],
                                                       'page_text' => $page_text[$lang_id],
                                                     );
                                                  
                $this->static_pages_model->update_static_page_translation($static_page_id,$lang_id,$static_page_translation_data);
            }
            
            redirect('static_pages/admin/index','refresh');   
        }
       
     }*/
     
     public function read($id,$display_lang_id)
     {
        $id              = intval($id);
        $display_lang_id = intval($display_lang_id);
        
        if($id && $display_lang_id)
        {
            $data     = $this->static_pages_model->get_row_data($id,$display_lang_id);
            if($data)
            {
                if($data->active == 1)
                {
                    $active_value = lang('active');
                }
                else
                {
                    $active_value = lang('not_active');
                }
                
                $row_data = array(
                                   lang('title')     => $data->title ,
                                   lang('page_text') => $data->page_text ,
                                   lang('active')    => '<span class="badge badge-info">'.$active_value.'</span>'
                                );
        
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
        $pages_ids = $this->input->post('row_id');

        if(is_array($pages_ids))
        { 
            
            $ids_array = array();
            
            foreach($pages_ids as $page_id)
            {
                $ids_array[] = $page_id['value'];
            }
        }
        else
        { 
            $ids_array = array($pages_ids);
        }
            
        $this->static_pages_model->delete_static_pages_data($ids_array);
        
     }  
     
     
    
/************************************************************************/    
}