<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Controllers extends CI_Controller
{
    public  $data = array();
    public $crud;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->crud = new grocery_CRUD();
        $params     = array($this->crud);
        
        require(APPPATH . 'includes/global_vars.php');
       
        $this->load->model('modules_model');
        $this->load->model('controllers_model');
        
        $this->module_row  = $this->admin_bootstrap->get_module();
        $this->lang_row    = $this->admin_bootstrap->get_active_language_row();
        
    }
    
    public function index()
    {
        $lang_id = $this->data['active_language']->id;
        
        $this->data['count_all_records'] = $this->controllers_model->get_count_all_controllers($lang_id);
        $this->data['data_language']     = $this->lang_model->get_active_data_languages();
        
        $this->data['filters']              = array(
                                                      array(
                                                            'filter_title' => lang('modules_filters'),
                                                            'filter_name'  => 'modules_filters',
                                                            'filter_data'  => $this->modules_model->get_modules_filter_data($lang_id)
                                                            )
                                                    );
                                                    
        $this->data['columns']           = array(
                                                     lang('module'),
                                                     lang('controller'),
                                                     lang('active')
                                                   );
            
        $this->data['orders']            = array(
                                                     lang('module'),
                                                     lang('controller'),
                                                     lang('active'),
                                                     lang('sort')
                                                   );
        
        $this->data['actions']           = array( 'delete'=>lang('delete'));
        
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
            
            $modules_filter_id     = intval($filters_data[0]);
        }
        else
        {
            $modules_filter_id     = 0;       
        }  
        
        
        $grid_data       = $this->controllers_model->get_controllers_data($lang_id,$limit,$offset,$search_word,$order_by,$order_state,$modules_filter_id);
        
        $db_columns      = array(
                                 'id'         ,   
                                 'module'     ,
                                 'controller_name' ,
                                 'active' ,
                                 'sort' 
                                );
                       
       $this->data['hidden_fields'] = array('id','sort');
                                           
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
                    
                    
                }
                else
                {
                    $new_grid_data[$key][$column] = $row->{$column};
                }
                   
            }
        }
        
        
        $this->data['grid_data']         = $new_grid_data;
        $this->data['count_all_records'] = $this->controllers_model->get_count_all_controllers($lang_id,$search_word,$modules_filter_id);
        $this->data['display_lang_id']   = $lang_id;
         
        $output_data = $this->load->view('Admin/grid/grid_data',$this->data, true);
        $count_data  = $this->data['count_all_records'];
        
        echo json_encode(array($output_data, $count_data, $search_word));
    }
    
    public function sorting()
    {
        $id         = $this->input->post('id');
        $old_index  = $this->input->post('old_sort');
        $new_index  = $this->input->post('new_sort');
        $sort_state = $this->input->post('sort_state');
        
        $this->controllers_model->update_row_sort($id,$old_index,$new_index,$sort_state);
        
     }
    
    public function read($id,$display_lang_id)
    {
        $id              = intval($id);
        $display_lang_id = intval($display_lang_id);
        
        if($id && $display_lang_id)
        {
            $data     = $this->controllers_model->get_row_data($id,$display_lang_id);
            
            if($data->active == 1)
            {
                $active_value = lang('active');
            }
            else
            {
                $active_value = lang('not_active');
            }
            
            $row_data = array(
                                lang('controller') => $data->controller_name ,
                                lang('module')     => $data->module ,
                                lang('icon_class') => $data->icon_class ,
                                lang('active')     => '<span class="badge badge-info">'.$active_value.'</span>'
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
            
        $this->controllers_model->delete_controller_data($ids_array);
        
     }  

    /*******************************************************/
    
     public function add_form()
     {
        $this->data['form_action']      = $this->data['module'] . "/" . $this->data['controller'] . "/save";
        $active_modules                 = $this->modules_model->get_active_module(); 
        
        $module_options = array();
        $controller_options = array();
        $module_options[0]= lang('choose');
        
        foreach($active_modules as $row)
        {
            
            $module_options[$row->id] = $row->module;
        }
        
        $this->data['active_modules']       = $active_modules;
        $this->data['module_options']       = $module_options;
        $this->data['controller_options']   = $controller_options;
        
        $this->data['content']              = $this->load->view('controllers', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
        
     }
     
     public function save()
     {
        $languages      = $this->input->post('lang_id');
        
        foreach($languages as $lang_id)
        {  
            $this->form_validation->set_rules('name['.$lang_id.']', lang('name'), 'required');
        }
        $this->form_validation->set_rules('module_id' , lang('module') , 'required');
        $this->form_validation->set_rules('controller' , lang('controller') , 'required');
        $this->form_validation->set_rules('icon_class' , lang('icon_class') , 'required');
        
        $this->form_validation->set_message('required', lang('required'));
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        
        if ($this->form_validation->run() == FALSE)
		{ 
		    $this->data['form_action']      = $this->data['module'] . "/" . $this->data['controller'] . "/save";
            $active_modules                 = $this->modules_model->get_active_module(); 
            
            $module_options     = array();
            $controller_options = array();            
            $module_options[0]  = lang('choose');
            
            foreach($active_modules as $row)
            {
                
                $module_options[$row->id] = $row->module;
            }
            
            $this->data['active_modules']     = $active_modules;
            $this->data['controller_options'] = $controller_options;            
            $this->data['module_options']     = $module_options;
            
            $this->data['content']            = $this->load->view('controllers', $this->data, true);
            
            $this->load->view('Admin/main_frame',$this->data);
		}
        else
        {
            $module_id   = $this->input->post('module_id');
            $controller  = $this->input->post('controller');
            $active      = $this->input->post('active');
            $icon_class  = $this->input->post('icon_class');
            $languages   = $this->input->post('lang_id');
            
            
            $data        = array(
                                    'module_id'  => $module_id,
                                    'controller' => $controller,
                                    'active'     => (isset( $_POST['active']))? $_POST['active']:0,
                                    'icon_class' => $icon_class,
                                 );
            
            $check_controller_count = $this->controllers_model->check_controllers_count($module_id,$controller);
            
            if($check_controller_count != 0)
            { 
                $this->session->set_flashdata('custom_error_msg',lang('row_already_exist'));
            }
            else
            { 
                if($this->controllers_model->insert_controllers($data))
                {
                
                    $last_insert_id = $this->db->insert_id();
                    $name           = $this->input->post('name');
                                   
                    foreach($languages as $lang_id)
                    {
                        $controllers_translation_data = array(
                                                                'controller_id' => $last_insert_id ,
                                                                'name'          => $name[$lang_id],
                                                                'lang_id'       => $lang_id ,
                                                             );
                        $this->controllers_model->insert_controllers_translation($controllers_translation_data);
                    }
                    
                    $this->session->set_flashdata('success',lang('success'));
                   
                    
               }
            }
            
            redirect('root/controllers/index','refresh');
       
        }
        
        
     }
     
     public function edit_form($id)
     {
        $id = intval($id);
        
        if($id)
        {
            $this->data['form_action']          = $this->data['module'] . "/" . $this->data['controller'] . "/update";
            $this->data['id']                   = $id;
            
            $general_data                       = $this->controllers_model->get_controllers_result($id);
            //print_r($general_data);die();
            $data                               = $this->controllers_model->get_controllers_translation_result($id);
            $module_row                         = $this->modules_model->get_module_by_id($general_data->module_id); 
            $module_controllers                 = $this->controllers_model->get_module_controllers($general_data->module_id ,$this->lang_row->id);   
            $active_modules                     = $this->modules_model->get_active_module(); 
        
            $module_options     = array();
            $controller_options = array();
            $module_options[0]  = lang('choose');
            
            foreach($active_modules as $row)
            {
                
                $module_options[$row->id]  = $row->module;
            }
            
            
            foreach($module_controllers as $conroller)
            {
                
                $controller_options[$conroller->controller]  = $conroller->controller;
            }
            
            $this->data['active_modules']       = $active_modules;
            $this->data['module_options']       = $module_options;
    

            $this->load->library('modulelist');
            $this->data['active_controllers']   = $this->modulelist->listControllers($module_row->module );
                       
            $filtered_data              = array();
            foreach($data as $row)
            {
                $filtered_data[$row->lang_id] = $row;
            }
            
            
            $this->data['controller_options']     = $controller_options ;
            $this->data['general_data']           = $general_data ;
            $this->data['filtered_data']          = $filtered_data;
            
            $this->data['content']                = $this->load->view('controllers', $this->data, true);
            
            $this->load->view('Admin/main_frame',$this->data);
        }
     }
     
     public function update()
     {
        $id         =  $this->input->post('controller_id');
        $languages  =  $this->input->post('lang_id');
        
        foreach($languages as $lang_id)
        {  
            $this->form_validation->set_rules('name['.$lang_id.']' , lang('name'), 'required');
        }
        $this->form_validation->set_rules('module_id' , lang('module') , 'required');
        $this->form_validation->set_rules('controller' , lang('controller') , 'required');
        $this->form_validation->set_rules('icon_class' , lang('icon_class') , 'required');
        
        $this->form_validation->set_message('required', lang('required'));
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        
        if ($this->form_validation->run() == FALSE)
		{ 
		    $this->data['form_action']          = $this->data['module'] . "/" . $this->data['controller'] . "/update";
            $this->data['id']                   = $id;
            
            $general_data                       = $this->controllers_model->get_controllers_result($id);
            $data                               = $this->controllers_model->get_controllers_translation_result($id);
            $this->data['active_modules']       = $this->modules_model->get_active_module();
            $module_row                         = $this->modules_model->get_module_by_id($general_data->module_id);
            
            $this->load->library('modulelist');
            $this->data['active_controllers']   = $this->modulelist->listControllers($module_row->module );
                       
            $filtered_data              = array();
            
            foreach($data as $row)
            {
                $filtered_data[$row->lang_id] = $row;
            }
            $module_options                   = array();
            $controller_options               = array();
            
            $this->data['module_options']     = $module_options;
            $this->data['controller_options'] = $controller_options;
            $this->data['general_data']       = $general_data ;
            $this->data['data']               = $filtered_data;
            $this->data['content']            = $this->load->view('controllers', $this->data, true);
            
            $this->load->view('Admin/main_frame',$this->data);
		}
        else
        {
            $module_id      = $this->input->post('module_id');
            $controller     = $this->input->post('controller');
            $active         = $this->input->post('active');
            $icon_class     = $this->input->post('icon_class');
            
            $controller_id  =  $this->input->post('controller_id');
            $name           =  $this->input->post('name');
                 
            $controllers_data   =  array(
                                        'module_id'  => $module_id,
                                        'controller' => $controller,
                                        'active'     => $active,
                                        'icon_class' => $icon_class,
                                    );
                                    
            $this->controllers_model->update_controllers($controller_id,$controllers_data);
                                            
            foreach($languages as $lang_id)
            {
                $controllers_translation_data = array(
                                                    'name'         => $name[$lang_id],
                                                  );
                $this->controllers_model->update_controllers_translation($controller_id,$lang_id,$controllers_translation_data);
            }
            
            redirect('root/controllers/index','refresh');
        }
        
        
        
     }
    /******************************************************/
  public function _callback_module_id($value, $row)
  {
     $module_row=$this->controllers_model->get_controller_module($value);
     
     return @$module_row->name;
  }
  
  public function _callback_add_field_controller($value, $primary_key)
    {
        //CREATE THE EMPTY SELECT STRING
		$empty_select = '<select name="controller" class="chosen-select" data-placeholder="Select controller" style="width: 300px; display: none;">';
		$empty_select_closed = '</select>';
		//GET THE ID OF THE LISTING USING URI
		//$listingID = $this->uri->segment(4);
		$listingID = $primary_key;
		//LOAD GCRUD AND GET THE STATE
		//$crud = new grocery_CRUD();
		$state = $this->crud->getState();
		
		//CHECK FOR A URI VALUE AND MAKE SURE ITS ON THE EDIT STATE
		if(isset($listingID) && $state == "edit") {
			//GET THE STORED STATE ID
            $this->db->where('id', $listingID);
            $row             = $this->db->get('controllers')->row();	
            
			$moduleID = $row->module_id;
			$controller_name = $row->controller;
			
            $this->db->where('id', $moduleID);
       
            $module_row     = $this->db->get('modules')->row();	
            $module  = $module_row->module;
            
            $this->load->library('modulelist');
            $controllers=$this->modulelist->listControllers($module);
			//print_r($controllers);
			//APPEND THE OPTION FIELDS WITH VALUES FROM THE STATES PER THE COUNTRY ID
			foreach($controllers as $controller):
				if($controller == $controller_name) {
					$empty_select .= '<option  selected="selected">'.$controller.'</option>';
				} else {
					$empty_select .= '<option >'.$controller.'</option>';
				}
			endforeach;
			
			//RETURN SELECTION COMBO
			return $empty_select.$empty_select_closed;
		} else {
			//RETURN SELECTION COMBO
			return $empty_select.$empty_select_closed;	
		}
    }
    public function _get_controllers($module_id)
    {
        $controllers = $this->controllers_model->get_module_controllers($module_id);
        
        $options='';
        
        foreach($controllers as $controller)
        {
            $options .= "<option value=$controller->controller>$controller->controller</option>";
        }
        echo $options;
    }
   	function get_controllers($moduleID)
	{
	   $this->db->where('id', $moduleID);
       
       $row     = $this->db->get('modules')->row();	
       $module  = $row->module;
        
        $this->load->library('modulelist');
        $controllers=$this->modulelist->listControllers($module);
	   //print_r($controllers);
        $options='';
        foreach($controllers as $controller)
        {
            $options .= "<option value=$controller>$controller</option>";
        }
        echo $options;
        /*$array = array();
		foreach($controllers as $controller):
			$array[] = array("value" =>$controller , "property" => $controller);
		endforeach;
		
		echo json_encode($array);
		exit;*/
		
	}
    
}?>