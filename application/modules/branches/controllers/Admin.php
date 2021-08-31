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
        
        $this->load->model('branches_model');
        $this->load->model('users/cities_model');
        
        $this->lang_row = $this->admin_bootstrap->get_active_language_row(); 
    }

    

    public function index()
    {   
        $lang_id = $this->data['active_language']->id;
        
        $this->data['count_all_records'] = $this->branches_model->get_count_all_branches($lang_id);
        $this->data['data_language']     = $this->lang_model->get_active_data_languages();
        
        
        $this->data['columns']           = array(
                                                     lang('city_name'),
                                                     lang('title')
                                                );
            
        $this->data['orders']            = array(
                                                    lang('city'),
                                                    lang('branch')
                                                );
        
        $this->data['actions']           = array( 'delete'=>lang('delete'));
        $this->data['search_fields']     = array( lang('branch'));
        
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
        
        if(isset($_POST['filter'])&& isset($_POST['filter_data']))
        {
            $filters = $this->input->post('filter');
            $filters_data = $this->input->post('filter_data');
            
            $city_id = intval($filters_data[0]);
        }
        else
        {
            $city_id = 0;            
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
        
        $grid_data  = $this->branches_model->get_branches_data($lang_id, $limit, $offset, $search_word, $order_by, $order_state);
        
        $db_columns = array(
                             'id',   
                             'city_name',
                             'branch_name'
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
        
        $this->data['grid_data']          = $new_grid_data; 
        $this->data['count_all_records']  = $this->branches_model->get_count_all_branches($lang_id, $search_word);
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
            $languages = $this->input->post('lang_id');
        
            foreach($languages as $lang_id)
            {  
                $this->form_validation->set_rules('title['.$lang_id.']', lang('name'), 'required');
                $this->form_validation->set_rules('address['.$lang_id.']', lang('address'), 'required');
            }
            
            $this->form_validation->set_rules('city_id', lang('city'), 'required');
            $this->form_validation->set_rules('lng', lang('longitude'), 'required');
            $this->form_validation->set_rules('lat', lang('latitude'), 'required');
            $this->form_validation->set_rules('phone', lang('phone'), 'required');
            $this->form_validation->set_rules('image', lang('thumbnail'), 'required');
            
            $this->form_validation->set_message('required', lang('required')."  : %s ");
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            
            $validation_msg = true;
        }
        
        if ($this->form_validation->run() == FALSE)
		{
		  $this->_add_form($validation_msg);
        }
        else
        {
            $city_id = $this->input->post('city_id');
            $active  = $this->input->post('active');
            $image   = $this->input->post('image');
            $lng     = $this->input->post('lng');
            $lat     = $this->input->post('lat');
            $phone   = $this->input->post('phone');
            
            $data    = array(
                                'city_id'  => $city_id,
                                'active'   => (isset( $_POST['active']))? $this->input->post('active'):0,
                                'image'    => $image,
                                'lng'      => $lng,
                                'lat'      => $lat,
                                'phone'    => $phone,
                            );
            
            if($this->branches_model->insert_branch($data))
            {
            
                $last_insert_id = $this->db->insert_id();
                $title          = $this->input->post('title');
                $address        = $this->input->post('address');
                               
                foreach($languages as $lang_id)
                {
                    $branches_translation_data = array(
                                                        'branch_id' => $last_insert_id  ,
                                                        'name'      => $title[$lang_id]  ,
                                                        'address'   => $address[$lang_id]  ,
                                                        'lang_id'   => $lang_id 
                                                     );
                    
                    $this->branches_model->insert_branches_translation($branches_translation_data);
                }
                
                $this->session->set_flashdata('success',lang('success'));
               
                redirect('branches/admin/','refresh');
            }
        }
     }
     
     private function _add_form($validation_msg)
     {
        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/add";
        $this->data['mode']         = 'edit';
        
        $cities                     = $this->cities_model->get_cities( $this->data['lang_id']);
        
        $cities_options             = array();
        $cities_options[null]       = lang('choose');
        
        foreach($cities as $row)
        {
            $cities_options[$row->id] = $row->name;
        }
        
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }
        
        $this->data['cities']  = $cities_options;
        
        $this->data['content'] = $this->load->view('form', $this->data, true);
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
                    $this->form_validation->set_rules('title['.$lang_id.']', lang('name'), 'required');
                    $this->form_validation->set_rules('address['.$lang_id.']', lang('address'), 'required');
                }
                
                $this->form_validation->set_rules('city_id', lang('city'), 'required');
                $this->form_validation->set_rules('lng', lang('longitude'), 'required');
                $this->form_validation->set_rules('lat', lang('latitude'), 'required');
                $this->form_validation->set_rules('phone', lang('phone'), 'required');
                $this->form_validation->set_rules('image', lang('thumbnail'), 'required');
                
                $this->form_validation->set_message('required', lang('required')."  : %s ");
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
                
                $validation_msg = true;
            }
            
            if($this->form_validation->run() == FALSE)
    		{
    		   $this->_edit_form($id, $validation_msg);
            }
            else
            {
                $city_id = $this->input->post('city_id');
                $active  = $this->input->post('active');
                $image   = $this->input->post('image');
                $lng     = $this->input->post('lng');
                $lat     = $this->input->post('lat');
                $phone   = $this->input->post('phone');
                
                $data    = array(
                                    'city_id'  => $city_id,
                                    'active'   => (isset( $_POST['active']))? $this->input->post('active'):0,
                                    'image'    => $image,
                                    'lng'      => $lng,
                                    'lat'      => $lat,
                                    'phone'    => $phone
                                );
               
                $this->branches_model->update_branch($id, $data);
               
                $title   = $this->input->post('title');
                $address = $this->input->post('address');
                 
                foreach($languages as $lang_id)
                {
                    $branches_translation_data = array(
                                                        'name'      => $title[$lang_id],
                                                        'address'   => $address[$lang_id],
                                                      );
                                                   
                    $this->branches_model->update_branches_translation($id, $lang_id, $branches_translation_data);
                }
                
                $this->session->set_flashdata('success',lang('updated_successfully'));
                redirect('branches/admin/','refresh');   
            }   
        }
     }
     
     private function _edit_form($id, $validation_msg)
     {
        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/edit/".$id;
        $this->data['mode']         = 'edit';
        $this->data['id']           = $id;
        $general_data               = $this->branches_model->get_branch_row($id);
        
        $data                       = $this->branches_model->get_branch_translation_result($id);
        $filtered_data              = array();
        
        foreach($data as $row)
        {
            $filtered_data[$row->lang_id] = $row;
        }
        
        $cities                     = $this->cities_model->get_cities( $this->data['lang_id']);
        
        $cities_options             = array();
        $cities_options[null]       = lang('choose');
        
        foreach($cities as $row)
        {
            $cities_options[$row->id] = $row->name;
        }
        
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }
        
        $this->data['cities']           = $cities_options;
        $this->data['data']             = $filtered_data;
        $this->data['general_data']     = $general_data;
        
        $this->data['content']          = $this->load->view('form', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
     }
     
     public function read($id,$display_lang_id)
     {
        $id              = intval($id);
        $display_lang_id = intval($display_lang_id);
        
        if($id && $display_lang_id)
        {
            $data     = $this->branches_model->get_row_data($id, $display_lang_id);
            $row_data = array(
                                lang('title')       => $data->title ,
                                lang('city')        => $data->city ,
                                lang('longitude')   => $data->lat,
                                lang('latitude')    => $data->lat,
                                //lang('thumbnail')   => '<'
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
        $branches_ids = $this->input->post('row_id');

        if(is_array($branches_ids))
        { 
            
            $ids_array = array();
            
            foreach($branches_ids as $id)
            {
                $ids_array[] = $id['value'];
            }
        }
        else
        { 
            $ids_array = array($branches_ids);
        }
            
        $this->branches_model->delete_branch_data($ids_array);
        
     }  
     
     
    
/************************************************************************/    
}