<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Languages extends CI_Controller
{
    public $data = array();
    public $crud;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->crud = new grocery_CRUD();
        $params     = array($this->crud);
        
        require(APPPATH . 'includes/global_vars.php');
    }

    public function _output_data($output)
    {
        $this->data['content'] = $this->load->view('Admin/crud',$output,true);
        $this->load->view('Admin/main_frame',$this->data);
        
    }
    
    public function index()
    {   
        
        $lang_id = $this->data['active_language']->id;
        
        $this->data['count_all_records']  = $this->lang_model->get_count_all_languages();
        $this->data['data_language']      = $this->lang_model->get_active_data_languages();
        
        $this->data['columns']            = array(
                                                     lang('language')  ,
                                                     lang('direction') ,
                                                     lang('flag')      ,
                                                     lang('active')
                                                  );
            
        $this->data['orders']             = array(
                                                     lang('language')  ,
                                                     lang('direction') ,
                                                     lang('active')
                                                  );
        
        $this->data['actions']            = array( 'delete'=>lang('delete'));
        
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
        
        
        
        $grid_data       = $this->lang_model->get_languages_data($limit,$offset,$search_word,$order_by,$order_state);
        
        $db_columns      = array(
                                  'id'        ,   
                                  'name'      ,
                                  'direction' ,
                                  'flag'      ,
                                  'active'
                                );
                       
       $this->data['hidden_fields'] = array('id');
                                           
       $new_grid_data = array();
        
        foreach($grid_data as $key =>$row)
        { 
            foreach($db_columns as $column)
            {
                if($column == 'flag')
                {
                    $new_grid_data[$key][$column] = "<a href='".base_url()."assets/template/admin/global/img/flags/".$row->flag."' class='image-thumbnail' data-rel='fancybox-button'><img src='".base_url()."assets/template/admin/global/img/flags/".$row->flag."' /></a>";
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
                else
                {
                    $new_grid_data[$key][$column] = $row->{$column};
                }
            }
        }
        
        
        $this->data['grid_data']         = $new_grid_data;
        $this->data['count_all_records'] = $this->lang_model->get_count_all_languages($search_word);
        $this->data['display_lang_id']   = $lang_id;
        
         
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
            $data     = $this->lang_model->get_language_row_data($id,$display_lang_id);
            
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
                                lang('name')      => $data->name      ,
                                lang('direction') => $data->direction ,
                                lang('language')  => $data->language  ,
                                lang('flag')      => '<img src="'.base_url().'assets/template/admin/global/img/flags/'.$data->flag.'" class="image-thumbnail" width="120" height="70">' ,
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
        $languages_ids = $this->input->post('row_id');

        if(is_array($languages_ids))
        { 
            
            $ids_array = array();
            
            foreach($languages_ids as $lang_id)
            {
                $ids_array[] = $lang_id['value'];
            }
        }
        else
        { 
            $ids_array = array($languages_ids);
        }
            
        $this->lang_model->delete_languages_data($ids_array);
        
    }  
    
    public function add_form()
    {                        
        $this->data['form_action']          = $this->data['module'] . "/" . $this->data['controller'] . "/save";
        
        $direction_options                  = array('ltr'=>'ltr','rtl'=>'rtl');
        
        $this->data['direction_options']    = $direction_options;
        
        $this->data['content']              = $this->load->view('languages', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
    }
    
    public function save()
    {       
        $languages      = $this->input->post('lang_id');
        
        $this->form_validation->set_rules('language_name', lang('language'), 'trim|required');
        $this->form_validation->set_rules('direction', lang('direction') , 'trim|required');
        $this->form_validation->set_rules('language', lang('language') , 'trim|required');
        $this->form_validation->set_rules('image', lang('flag') , 'trim|required');
         
        $this->form_validation->set_message('required', lang('required'));
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            
        if ($this->form_validation->run() == FALSE)
		{ 
		    $this->data['form_action']      = $this->data['module'] . "/" . $this->data['controller'] . "/save";
              
            $direction_options               = array(
                                                     'ltr' => 'ltr' ,
                                                     'rtl' => 'rtl'
                                                    );
            $this->data['direction_options'] = $direction_options;
        
            $this->data['content']           = $this->load->view('languages', $this->data, true);
            $this->load->view('Admin/main_frame',$this->data);
		}
        else
        {
            
            $general_data = array(
                                        'name'      => $this->input->post('language_name') , 
                                        'direction' => $this->input->post('direction')     ,
                                        'flag'      => $this->input->post('image')         ,
                                        'language'  => $this->input->post('language')      ,
                                        'active'    => (isset( $_POST['active']))? $this->input->post('active'):0
                                        
                                      );
            
            if($this->lang_model->insert_lang_vars($general_data))
            {   
                $this->session->set_flashdata('success',lang('success'));   
                redirect('root/languages/index','refresh');
            }
        } 
        
    }
    
    public function edit_form($id)
     {
        $id = intval($id);
        
        if($id)
        {
            $this->data['form_action']       = $this->data['module'] . "/" . $this->data['controller'] . "/update";
            $this->data['id']                = $id;
            
            $general_data                    = $this->lang_model->get_language_result($id);
            
            $direction_options               = array('ltr'=>'ltr','rtl'=>'rtl');
        
            $this->data['direction_options'] = $direction_options;
            
            $this->data['general_data']      = $general_data ;
            
            $this->data['content']           = $this->load->view('languages', $this->data, true);
            
            $this->load->view('Admin/main_frame',$this->data);
        }
     }
     
     public function update()
     {
        $id         =  $this->input->post('language_id');
        $languages  =  $this->input->post('lang_id');
        
        $this->form_validation->set_rules('language_name', lang('language'), 'trim|required');
        $this->form_validation->set_rules('direction', lang('direction') , 'trim|required');
        $this->form_validation->set_rules('language', lang('language') , 'trim|required');
        
        $this->form_validation->set_message('required', lang('required'));
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        
        if ($this->form_validation->run() == FALSE)
		{ 
		    $this->data['form_action']          = $this->data['module'] . "/" . $this->data['controller'] . "/update";
            $this->data['id']                   = $id;
            
            $general_data                    = $this->lang_model->get_language_result($id);
            
            $direction_options               = array('ltr'=>'ltr','rtl'=>'rtl');
        
            $this->data['direction_options'] = $direction_options;
            
            $this->data['general_data']      = $general_data ;
            
            $this->data['content']           = $this->load->view('languages', $this->data, true);
            
            $this->load->view('Admin/main_frame',$this->data);
		}
        else
        {
            $language_id  =  $this->input->post('language_id');
            
            $lang_data   =  array(
                                        'name'      => $this->input->post('language_name') , 
                                        'direction' => $this->input->post('direction')     ,
                                        'flag'      => $this->input->post('image')         ,
                                        'language'  => $this->input->post('language')      ,
                                        'active'    => $this->input->post('active')
                                    );
                                    
            $this->lang_model->update_language($language_id,$lang_data);
            
            $this->session->set_flashdata('success',lang('updated_successfully'));
            redirect('root/languages/index','refresh');
        }
        
        
        
     }

    public function index2()
    {
       $this->crud->set_table('languages');
       
       $this->crud->display_as('name',lang('language'));
       $this->crud->display_as('direction',lang('direction'));
       $this->crud->display_as('language',lang('language'));
       $this->crud->display_as('flag',lang('flag'));
       $this->crud->display_as('active',lang('active'));
       $this->crud->display_as('strucure',lang('strucure_lang'));
       $this->crud->display_as('data',lang('data_lang'));
       
       $this->crud->set_subject(lang('language'));
    
       $this->crud->required_fields('name','direction');
        
       $this->crud->columns('name','direction');
       //$this->crud->fields('name','direction');
       
       $this->crud->set_field_upload('flag','assets/template/admin/global/img/flags');
             
       $output = $this->crud->render();
       $this->_output_data($output); 
    }
    
    

}
/* End of file admin_lang.php */
/* Location: ./application/modules/Importance/controllers/admin_lang.php */