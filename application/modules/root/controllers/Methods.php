<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Methods extends CI_Controller
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
       $this->load->model('methods_model');
       
       $this->lang_row    = $this->admin_bootstrap->get_active_language_row();
       
    }
    public function index()
    {
        $lang_id = $this->data['active_language']->id;
        
        $this->data['count_all_records'] = $this->methods_model->get_count_all_methods($lang_id);
        $this->data['data_language']     = $this->lang_model->get_active_data_languages();
        
        $this->data['filters']           = array(
                                                   array(
                                                          'filter_title' => lang('modules_filters'),
                                                          'filter_name'  => 'modules_filters',
                                                          'filter_data'  => $this->modules_model->get_modules_filter_data($lang_id)
                                                         ) ,
                                                   array(
                                                          'filter_title' => lang('controllers_filters'),
                                                          'filter_name'  => 'controllers_filters',
                                                          'filter_data'  => $this->controllers_model->get_controllers_filter_data($lang_id)
                                                         ) 
                                                     
                                                 );
        
        $this->data['columns']           = array(
                                                     lang('module'),
                                                     lang('controller'),
                                                     lang('method'),
                                                     lang('active')
                                                 );
            
        $this->data['orders']            = $this->data['columns'];
        
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
            $controllers_filter_id = intval($filters_data[1]);
        }
        else
        {
            $modules_filter_id     = 0;
            $controllers_filter_id = 0;       
        }  
        
        
        $grid_data       = $this->methods_model->get_methods_data($lang_id,$limit,$offset,$search_word,$order_by,$order_state,$modules_filter_id,$controllers_filter_id);
        
        $db_columns      = array(
                                 'id'         ,   
                                 'module'     ,
                                 'controller' ,
                                 'method'     ,
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
                    
                    
                }
                else
                {
                    $new_grid_data[$key][$column] = $row->{$column};
                }
                   
            }
        }
        
        
        $this->data['grid_data']         = $new_grid_data;
        $this->data['count_all_records'] = $this->methods_model->get_count_all_methods($lang_id,$search_word,$modules_filter_id,$controllers_filter_id);
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
            $data     = $this->methods_model->get_row_data($id,$display_lang_id);
            
            if($data->active == 1)
            {
                $active_value = lang('active');
            }
            else
            {
                $active_value = lang('not_active');
            }
            
            $row_data = array(
                                lang('method')     => $data->method ,
                                lang('controller') => $data->controller ,
                                lang('module')     => $data->module ,
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
        $method_ids = $this->input->post('row_id');

        if(is_array($method_ids))
        { 
            
            $ids_array = array();
            
            foreach($method_ids as $method_id)
            {
                $ids_array[] = $method_id['value'];
            }
        }
        else
        { 
            $ids_array = array($method_ids);
        }
            
        $this->methods_model->delete_method_data($ids_array);
        
     }  
    

    /*******************************************************/
    
     public function add_form()
     {
        $this->data['form_action']          = $this->data['module'] . "/" . $this->data['controller'] . "/save";
        $active_modules                     = $this->modules_model->get_active_module();
        $module_options     = array();
        
        foreach($active_modules as $row)
            {
                
                $module_options[$row->id]  = $row->module;
            }
            
        
        $controller_options = array();
        $methods_options    = array();
        
        $this->data['module_options']       = $module_options;
        $this->data['controller_options']   = $controller_options;
        $this->data['methods_options']      = $methods_options;
            
        $this->data['content']              = $this->load->view('methods', $this->data, true);
        
        $this->load->view('Admin/main_frame',$this->data);
        
     }
     
     public function save()
     {
        $languages          = $this->input->post('lang_id');
        
        foreach($languages as $lang_id)
        { 
            $this->form_validation->set_rules('name['.$lang_id.']', lang('name') , 'required');
           
        }
        $this->form_validation->set_rules('module_id', lang('module') , 'required');
        $this->form_validation->set_rules('controller_id', lang('controller') , 'required');
        $this->form_validation->set_rules('method', lang('method') , 'required');
        $this->form_validation->set_rules('active', lang('active') , 'required');
        
        $this->form_validation->set_message('required', lang('required'));
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        if ($this->form_validation->run() == FALSE)
		{
		    $this->data['form_action']      = $this->data['module'] . "/" . $this->data['controller'] . "/save";
            $this->data['active_modules']   = $this->modules_model->get_active_module();
            
            $this->data['content']          = $this->load->view('methods', $this->data, true);
            
            $this->load->view('Admin/main_frame',$this->data);
    		
        }
        else
        {
            $module_id          = $this->input->post('module_id');
            $controller_id      = $this->input->post('controller_id');
            $method             = $this->input->post('method');
            $active             = $this->input->post('active');
            
            $data               = array(
                                           'module_id'     => $module_id,
                                           'controller_id' => $controller_id,
                                           'method'        => $method,
                                           'active'        => $active,
                                       );
            
            $check_count = $this->methods_model->check_methods_count($module_id,$controller_id,$method);
            
            if($check_count != 0)
            {
                $this->session->set_flashdata('custom_error_msg',lang('row_already_exist'));
            }
            else
            {                                
                if($this->methods_model->insert_methods($data))
                {
                
                    $last_insert_id = $this->db->insert_id();
                    $name           = $this->input->post('name');
                                   
                    foreach($languages as $lang_id)
                    {
                        $methods_translation_data = array(
                                                            'method_id' => $last_insert_id ,
                                                            'name'          => $name[$lang_id],
                                                            'lang_id'       => $lang_id ,
                                                         );
                        $this->methods_model->insert_methods_translation($methods_translation_data);
                    }
                    
                    $this->session->set_flashdata('success',lang('success')); 
               }
               
            }
            redirect('root/methods/index','refresh');
        }
        
        
     }
     
     public function edit_form($id)
     {
        $id = intval($id);
        
        if($id)
        {
            $this->data['form_action']          = $this->data['module'] . "/" . $this->data['controller'] . "/update";
            $this->data['id']                   = $id;
            
            $general_data                       = $this->methods_model->get_methods_result($id);
            $data                               = $this->methods_model->get_methods_translation_result($id);
            
            $active_modules                     = $this->modules_model->get_active_module();
            $module_controllers                 = $this->controllers_model->get_module_controllers($general_data->module_id ,$this->lang_row->id);
            
            $filtered_data   = array();
            
            foreach($data as $row)
            {
                $filtered_data[$row->lang_id] = $row;
            }
            
            $module_options     = array();
            $controller_options = array();
                        
            foreach($active_modules as $row)
            {
                
                $module_options[$row->id]  = $row->module;
            }
            
            
            foreach($module_controllers as $conroller)
            {
                
                $controller_options[$conroller->id]  = $conroller->name;
            }
            
            $this->data['general_data']         = $general_data ;
            $this->data['data']                 = $filtered_data;
            $this->data['module_options']       = $module_options;
            $this->data['controller_options']   = $controller_options;
            
            $this->data['content']              = $this->load->view('methods', $this->data, true);
            $this->load->view('Admin/main_frame',$this->data);
        }
     }
     
     public function update()
     {
        $languages          = $this->input->post('lang_id');
        
        foreach($languages as $lang_id)
        { 
            $this->form_validation->set_rules('name['.$lang_id.']', lang('name') , 'required');
           
        }
        $this->form_validation->set_rules('module_id', lang('module') , 'required');
        $this->form_validation->set_rules('controller_id', lang('controller') , 'required');
        $this->form_validation->set_rules('method', lang('method') , 'required');
        $this->form_validation->set_rules('active', lang('active') , 'required');
        
        $this->form_validation->set_message('required', lang('required'));
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        
        if ($this->form_validation->run() == FALSE)
		{
		    $this->data['form_action']      = $this->data['module'] . "/" . $this->data['controller'] . "/update";
            $this->data['active_modules']   = $this->modules_model->get_active_module();
            
            $this->data['content']          = $this->load->view('methods', $this->data, true);
            
            $this->load->view('Admin/main_frame',$this->data);
    		
        }
        else{
            $module_id          = $this->input->post('module_id');
            $controller_id      = $this->input->post('controller_id');
            $method             = $this->input->post('method');
            $active             = $this->input->post('active');
            
            $name               = $this->input->post('name');
            $method_id          = $this->input->post('method_id');
            
            
            $methods_data   =  array(
                                        'module_id'     => $module_id,
                                        'controller_id' => $controller_id,
                                        'method'        => $method,
                                        'active'        => $active,
                                    );
                                    
            $this->methods_model->update_methods($method_id,$methods_data);
                                            
            foreach($languages as $lang_id)
            {
                $methods_translation_data = array(
                                                    'name'         => $name[$lang_id],
                                                  );
                $this->methods_model->update_methods_translation($method_id,$lang_id,$methods_translation_data);
            }
            
            redirect('root/methods/index','refresh');
        }
        
        
     }
    /******************************************************/
    
    public function get_controllers($module_id)
    {
        $controllers = $this->controllers_model->get_module_controllers($module_id , $this->lang_row->id);
        
        $options='';
        
        foreach($controllers as $controller)
        {
            $options .= "<option value=$controller->id>$controller->name</option>";
        }
        echo $options;
    }

    public function _callback_controller_id($value, $row)
      {
         $controller_row=$this->controllers_model->get_controller_by_id($value);
         
         return @$controller_row->controller;
      }
      
  	 public function _callback_module_id($value, $row)
      {
         $medule_row = $this->modules_model->get_module_by_id($value);
         
         return @$medule_row->module;
      }
      
    //////////////////////////////////////////////////////////////////////////
    //CALLBACK FUNCTIONS
	
	function _callback_controller_id_dropdown_select($value, $primary_key)
	{
		//CREATE THE EMPTY SELECT STRING
		$empty_select = '<select name="controller_id" class="chosen-select" data-placeholder="Select controller" style="width: 300px; display: none;">';
		$empty_select_closed = '</select>';
		//GET THE ID OF THE LISTING USING URI
		//$listingID = $this->uri->segment(4);
		$listingID =$primary_key;
		//LOAD GCRUD AND GET THE STATE
	    $state = $this->crud->getState();
		
		//CHECK FOR A URI VALUE AND MAKE SURE ITS ON THE EDIT STATE
		if(isset($listingID) && $state == "edit") {
		//GET THE STORED STATE ID
            $this->db->where('id', $listingID);
            
            $row             = $this->db->get('methods')->row();	
            
			$module_id       = $row->module_id;
			$controller_id  = $row->controller_id;
			
			//GET THE CITIES PER STATE ID
            
            $this->db->where('module_id', $module_id);
            
            $controllers     = $this->db->get('controllers')->result();	
            
		
			//APPEND THE OPTION FIELDS WITH VALUES FROM THE STATES PER THE COUNTRY ID
			foreach($controllers as $row):
				if($row->module_id == $module_id) {
					$empty_select .= '<option value="'.$row->id.'" selected="selected">'.$row->name_en.'</option>';
				} else {
					$empty_select .= '<option value="'.$row->id.'">'.$row->name_en.'</option>';
				}
			endforeach;
			
			//RETURN SELECTION COMBO
			return $empty_select.$empty_select_closed;
		} else {
			//RETURN SELECTION COMBO
			return $empty_select.$empty_select_closed;	
		}
	}
	
    function _callback_method_dropdown_select($value, $primary_key)
	{
		//CREATE THE EMPTY SELECT STRING
		$empty_select = '<select name="method" class="chosen-select" data-placeholder="Select method" style="width: 300px; display: none;">';
		$empty_select_closed = '</select>';
		//GET THE ID OF THE LISTING USING URI
		//$listingID = $this->uri->segment(4);
		$listingID = $primary_key;
		//LOAD GCRUD AND GET THE STATE
		$state = $this->crud->getState();
		
		//CHECK FOR A URI VALUE AND MAKE SURE ITS ON THE EDIT STATE
		if(isset($listingID) && $state == "edit")
        {
			//GET THE STORED STATE ID
            
            $this->db->where('id', $listingID);
            
            $row             = $this->db->get('methods')->row();	
            
			$module_id       = $row->module_id;
			$controller_id   = $row->controller_id;
			$method_name     = $row->method;
            
            $this->db->where('id',$controller_id);
            $controller_row         =  $this->db->get('controllers')->row();	
            $controller  =  $controller_row->controller;
       
            //$module_id   =  $row->module_id;  
            $this->db->where('id',$module_id);
            $module_row  =  $this->db->get('modules')->row();
            $module      =  $module_row->module;
       
            $this->load->library('modulelist');
            $methods=$this->modulelist->listMethods($module,$controller);
	   
			//APPEND THE OPTION FIELDS WITH VALUES FROM THE STATES PER THE COUNTRY ID
			foreach($methods as $method):
				if($method == $method_name) {
					$empty_select .= '<option  selected="selected">'.$method.'</option>';
				} else {
					$empty_select .= '<option >'.$method.'</option>';
				}
			endforeach;
			
			//RETURN SELECTION COMBO
			return $empty_select.$empty_select_closed;
		} else {
			//RETURN SELECTION COMBO
			return $empty_select.$empty_select_closed;	
		}
	}
    
	function _get_controllers($moduleID)
	{
	   $this->db->where('module_id', $moduleID);
       $controllers     = $this->db->get('controllers')->result();	
      
       $array = array();
	    foreach($controllers as $controller):
			$array[] = array("value" => $controller->id,"property" => $controller->name_en);
		endforeach;
		
		echo json_encode($array);
		exit;
		
	}

    function _get_methods($controllerID)
	{
	   $this->db->where('id',$controllerID);
       
       $row         =  $this->db->get('controllers')->row();	
       
       $controller  =  $row->controller;
       
       $module_id   =  $row->module_id;  
       
       $this->db->where('id',$module_id);
       
       $module_row  =  $this->db->get('modules')->row();
       
       $module      =  $module_row->module;
      
       $this->load->library('modulelist');
       
       $methods=$this->modulelist->listMethods($module, $controller);
	   
       $array = array();
		foreach($methods as $method):
			$array[] = array("value" => $method,"property" => $method);
		endforeach;
		
		echo json_encode($array);
		exit;
		
	}
}?>