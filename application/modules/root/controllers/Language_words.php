<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Language_words extends CI_Controller
{
    public $data = array();
    public $crud;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->crud = new grocery_CRUD();
        $params     = array($this->crud);
        
        require(APPPATH . 'includes/global_vars.php');
        
        $this->load->model('lang_model');
    }

    public function index()
    {
       $lang_id = $this->data['active_language']->id;
        
        $this->data['count_all_records'] = $this->lang_model->get_count_all_lang_vars($lang_id);
        $this->data['data_language']     = $this->lang_model->get_active_data_languages();
        
        $this->data['columns']           = array(
                                                     lang('language'),
                                                     lang('lang_var'),
                                                     lang('lang_definition')
                                                   );
            
        $this->data['orders']            = array(
                                                    'id',
                                                     lang('lang_var'),
                                                     lang('lang_definition')
                                                 );
        
        
        $this->data['actions']           = array();
        
        $this->data['content']  = $this->load->view('Admin/grid/grid_html', $this->data, true);
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
        
        
        $grid_data       = $this->lang_model->get_lang_vars_data($lang_id,$limit,$offset,$search_word,$order_by,$order_state);
        
        $db_columns      = array(
                                 'id'          ,   
                                 'name'     ,
                                 'lang_var'    ,
                                 'lang_definition'
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
        
        
        $this->data['grid_data']         = $new_grid_data;
        $this->data['count_all_records'] = $this->lang_model->get_count_all_lang_vars($lang_id,$search_word);
        $this->data['display_lang_id']   = $lang_id;
        
        $this->data['unset_delete']      = true;
         
        $output_data = $this->load->view('Admin/grid/grid_data',$this->data, true);
        $count_data  = $this->data['count_all_records'];
        
        echo json_encode(array($output_data, $count_data, $search_word));
    }
    
    public function read($id,$display_lang_id)
    {
        $id              = intval($id);
        $display_lang_id = intval($display_lang_id);
        
        if($id && $display_lang_id)
        {
            $data     = $this->lang_model->get_row_data($id,$display_lang_id);
            
            $row_data = array(
                                lang('lang_var')        => $data->lang_var,
                                lang('lang_definition') => $data->lang_definition ,
                                lang('language')        => $data->language 
                             );
                             
            
        
            $this->data['row_data'] = $row_data;
            
            $this->data['content']  = $this->load->view('Admin/grid/read_view', $this->data, true);
            $this->load->view('Admin/main_frame',$this->data);
        }
    }

    public function add()
    {
        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/save";
        $this->data['content']      = $this->load->view('lang_tabs', $this->data, true);
        
        $this->load->view('Admin/main_frame',$this->data);
    }
    
    public function save()
    {
        $languages      = $this->input->post('lang_id');
        
        foreach($languages as $lang_id)
        { 
            $this->form_validation->set_rules('lang_definition['.$lang_id.']', lang('lang_definition'), 'trim|required');
        }
        $this->form_validation->set_rules('lang_var' , lang('lang_var') , 'trim|required');
        
        $this->form_validation->set_message('required', lang('required'));
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');  
        
        if ($this->form_validation->run() == FALSE)
		{ 
		    $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/save";
            $this->data['content']      = $this->load->view('lang_tabs', $this->data, true);
            
            $this->load->view('Admin/main_frame',$this->data);
		}
        else
        {
            $lang_general_data = array(
                                        'lang_var'   => $this->input->post('lang_var'),
                                        'mobile_app' => isset($_POST['mobile_app']) ? $_POST['mobile_app'] : 0
                                      );
            
            if($this->lang_model->insert_lang_var($lang_general_data))
            {
                $last_insert_id   = $this->db->insert_id();
                $lang_id	      = $this->input->post('lang_id	');
                $lang_definition  = $this->input->post('lang_definition');
                
                foreach($languages as $lang_id)
                {
                    $lang_translation_data = array(
                                                        'var_id'          => $last_insert_id ,
                                                        'lang_definition' => $lang_definition[$lang_id],
                                                        'lang_id'         => $lang_id ,
                                                     );
                    $this->lang_model->insert_lang_translation($lang_translation_data);
                }
                    
                    $this->session->set_flashdata('success',lang('success'));
                   
                    redirect('root/language_words/index','refresh');
            }
        }
    }
    public function edit($id)
    {
        $id = intval($id);
        
        if($id)
        {
            $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/update";
            $this->data['id']           = $id;
            $data                       = $this->lang_model->get_lang_result($id);
            $general_data               = $this->lang_model->get_general_lang_data($id);
            $filtered_data              = array();
            
            foreach($data as $row)
            {
                $filtered_data[$row->lang_id] = $row;
            }
    
            $this->data['data']         = $filtered_data;
            
            $this->data['general_data']         = $general_data;
            
            $this->data['content']      = $this->load->view('lang_tabs', $this->data, true);
            
            $this->load->view('Admin/main_frame',$this->data);
        }
    }
    
    public function update()
    {
        $lang_var_id     = intval($this->input->post('lang_var_id'));
        $languages       = $this->input->post('lang_id');
        
        foreach($languages as $lang_id)
        { 
            $this->form_validation->set_rules('lang_definition['.$lang_id.']', lang('lang_definition'), 'trim|required');  
        }
        $this->form_validation->set_rules('lang_var' , lang('lang_var') , 'trim|required');
        
        $this->form_validation->set_message('required', lang('required'));
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        
        if($this->form_validation->run() == FALSE)
        { 
            $this->data['id']           = $lang_var_id;
            $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/save";
            $this->data['content']      = $this->load->view('lang_tabs', $this->data, true);
            
            $this->load->view('Admin/main_frame',$this->data);
        }
        else
        { 
            $lang_general_data = array(
                                        'lang_var'   => $this->input->post('lang_var'),
                                        'mobile_app' => isset($_POST['mobile_app']) ? $_POST['mobile_app'] : 0
                                      );
            
            $this->lang_model->update_general_data($lang_var_id,$lang_general_data);
            
            $lang_definition   = $this->input->post('lang_definition');
           
            foreach($languages as $lang_id)
            {
                $lang_translation_data = array(  'lang_definition' => $lang_definition[$lang_id] );
                $this->lang_model->update_lang_translation($lang_var_id,$lang_id,$lang_translation_data);
            }
            
            redirect('root/language_words/index','refresh');
        }
        
    }

}
/* End of file admin_lang.php */
/* Location: ./application/modules/Importance/controllers/admin_lang.php */