<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Admin_cities extends CI_Controller
{
    public $data = array();
    public $lang_row;
    
    public function __construct()
    {
        parent::__construct();
        
        require(APPPATH . 'includes/global_vars.php');
       
        $this->load->model('cities_model');
        $this->load->model('users_model');
        
        $this->lang_row    = $this->admin_bootstrap->get_active_language_row();
        
    }
    
    public function index()
    {
        //$lang_id = $this->data['active_language']->id;
        
        $this->data['count_all_records'] = $this->cities_model->get_count_all_cites($this->lang_row->id);
        $this->data['data_language']     = $this->lang_model->get_active_data_languages();
        
        $this->data['filters']            = array(
                                                      array(
                                                            'filter_title' => lang('country_name'),
                                                            'filter_name'  => 'user_nationality_filters',
                                                            'filter_data'  => $this->cities_model->get_user_nationality_filter_data($this->lang_row->id)
                                                            )
                                                    );
                                                    
        $this->data['columns']           = array(
                                                     lang('country_name'),
                                                     lang('city_name'),
                                                 );
            
        $this->data['orders']            = array(
                                                    lang('country_name'),
                                                    lang('city_name'),
                                                   );
        
        $this->data['actions']           = array( 'delete'=>lang('delete'));
        $this->data['search_fields']     = array( lang('city_name'), lang('country_name'));
        
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
        
        if(isset($_POST['filter'])&& isset($_POST['filter_data']))
        {
            $filters      = $this->input->post('filter');
            $filters_data = $this->input->post('filter_data');
            
            $user_nationality_filter_id     = intval($filters_data[0]);
        }
        else
        {
            $user_nationality_filter_id     = 0;       
        }  
        
        
        $grid_data       = $this->cities_model->get_cities_data($lang_id,$limit,$offset,$search_word,$order_by,$order_state,$user_nationality_filter_id);
        
        $db_columns      = array(
                                 'id'         ,   
                                 'user_nationality_name'     ,
                                 'name' ,
                                  
                                );
                       
       $this->data['hidden_fields'] = array('id','sort');
                                           
       $new_grid_data = array();
        
        foreach($grid_data as $key =>$row)
        { 
            foreach($db_columns as $column)
            {
               $new_grid_data[$key][$column] = $row->{$column};
            }
        }
        
        
        $this->data['grid_data']         = $new_grid_data;
        $this->data['count_all_records'] = $this->cities_model->get_count_all_cites($lang_id,$search_word,$user_nationality_filter_id);
        $this->data['display_lang_id']   = $lang_id;
        // echo "<pre/>";print_r($grid_data);echo "<pre/>";print_r($new_grid_data);
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
            $data     = $this->cities_model->get_row_data($id ,$display_lang_id);
            
            
            $row_data = array(
                                lang('country_name') => $data->user_nationality_name ,
                                lang('city_name')        => $data->name ,
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
        $controller_ids = $this->input->post('row_id');

        if(is_array($controller_ids))
        { 
            
            $ids_array = array();
            
            foreach($controller_ids as $controller_id)
            {
                $ids_array[] = $controller_id['value'];
            }
        }
        else
        { 
            $ids_array = array($controller_ids);
        }
            
        $this->cities_model->delete_cities_data($ids_array);
        
     }  

    /*******************************************************/
    
    public function add()
    {
        $validation_msg = false;
        
        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
        {
            $validation_msg = true;
            
            $languages      = $this->input->post('lang_id');
        
            foreach($languages as $lang_id)
            {  
                $this->form_validation->set_rules('name['.$lang_id.']', lang('city_name'), 'required');
            }
            $this->form_validation->set_rules('user_nationality_id' , lang('country_name') , 'required');
            
            $this->form_validation->set_message('required', lang('required').' : %s ');
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        }
        
        if ($this->form_validation->run() == FALSE)
		{
		  $this->_add_form($validation_msg);
        }
        else
        {
            $user_nationality_id   = $this->input->post('user_nationality_id');
           
            $data                  = array(
                                              'user_nationality_id'  => $user_nationality_id,
                                          );
         
            if($this->cities_model->insert_cities($data))
            {
                $last_insert_id     = $this->db->insert_id();
                $name               = $this->input->post('name');
                                
                foreach($languages as $lang_id)
                {
                    $cities_translation_data = array(
                                                        'city_id'   => $last_insert_id ,
                                                        'lang_id'   => $lang_id ,
                                                        'name'      => $name[$lang_id],
                                                     );
                    
                    $this->cities_model->insert_cities_translation($cities_translation_data);
                }
                
               
          }
          
          $_SESSION['success'] = lang('success');
          $this->session->mark_as_flash('success');
          
          redirect('users/admin_cities/','refresh');
        }
    }
    
    private function _add_form($validation_msg)
    {
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }
        
        $this->data['form_action']      = $this->data['module'] . "/" . $this->data['controller'] . "/add";
        $user_nationalities             = $this->users_model->get_all_countries($this->lang_row->id); 
        
        $nationalities_options = array();
        $nationalities_options[null]= lang('choose');
        
        foreach($user_nationalities as $row)
        {
            
            $nationalities_options[$row->id] = $row->name;
        }
        
    
        $this->data['nationalities_options'] = $nationalities_options;
      
        $this->data['content'] = $this->load->view('cities', $this->data, true);
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
                $id        =  $this->input->post('city_id');
                $languages =  $this->input->post('lang_id');
                
                foreach($languages as $lang_id)
                {  
                    $this->form_validation->set_rules('name['.$lang_id.']' , lang('name'), 'required');
                }
                
                $this->form_validation->set_rules('user_nationality_id' , lang('user_nationality') , 'required');
                
                $this->form_validation->set_message('required', lang('required').' : %s ');
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            }
        }
        
        if($this->form_validation->run() == FALSE)
		{
		   $this->_edit_form($id, $validation_msg);
        }
        else
        {
            $user_nationality_id   = $this->input->post('user_nationality_id');
           
            $data                   = array(
                                            'user_nationality_id'  => $user_nationality_id,
                                           );
         
            $this->cities_model->update_cities($id , $data);
            
            
            $name               = $this->input->post('name');
                            
            foreach($languages as $lang_id)
            {
                $cities_translation_data = array(
                                                    'name'      => $name[$lang_id],
                                                 );
                $this->cities_model->update_cities_translation($lang_id , $id,  $cities_translation_data);
            }
            
            $_SESSION['success'] = lang('updated_successfully');
            $this->session->mark_as_flash('success');
            
            redirect('users/admin_cities/','refresh');
        }
    }
    
    private function _edit_form($id, $validation_msg)
    {
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }
        
        $this->data['form_action']          = $this->data['module'] . "/" . $this->data['controller'] . "/edit/" . $id;
        $this->data['id']                   = $id;
        
        $general_data                       = $this->cities_model->get_row_data($id , $this->lang_row->id);
        $data                               = $this->cities_model->get_cities_translation_result($id);
        $user_nationalities                 = $this->users_model->get_all_countries($this->lang_row->id); 
    
        $nationalities_options = array();
        $nationalities_options[null]= lang('choose');
        
        foreach($user_nationalities as $row)
        {
            $nationalities_options[$row->id] = $row->name;
        }
        
        $filtered_data              = array();
        
        foreach($data as $row)
        {
            $filtered_data[$row->lang_id] = $row;
        }
       
       
        $this->data['nationalities_options']  = $nationalities_options;
        $this->data['general_data']           = $general_data;
        $this->data['filtered_data']          = $filtered_data;
        
        $this->data['content']                = $this->load->view('cities', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
    }
    
    /******************************************************/
   
}?>