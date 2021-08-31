<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Exampless extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		
		/* Standard Libraries */
		$this->load->database();
		$this->load->helper('url');
		/* ------------------ */	
		
		$this->load->library('grocery_CRUD');	
	}
	
	function _example_output($output = null)
	{
		$this->load->view('example.php', $output);	
	}
	
	function index()
	{
		$this->_example_output((object)array('output' => '' , 'js_files' => array() , 'css_files' => array()));
	}	
	
	/*
	* CUSTOM DEPENDENT DROPDOWN
	*/
	function methods_management()
	{
			//GROCERY CRUD SETUP
			$crud = new grocery_CRUD();

			$crud->set_table('methods');
			//$crud->columns('customerName','contactLastName','phone','countryID','stateID','cityID');
            $crud->columns('method','module_id','controller_id');
            
            $crud->display_as('name',lang('name'));
            $crud->display_as('name_en',lang('name_en'));
            $crud->display_as('controller_id',lang('controller_name_en'));
            $crud->display_as('module_id',lang('module_name'));
           
		/*	$crud->set_relation('salesRepEmployeeNumber','employees','{lastName} {firstName}');
			$crud->set_relation('countryID','country','country_title');
			$crud->set_relation('stateID','state','state_title');
			$crud->set_relation('cityID','city','city_title');*/
            
           if($this->session->userdata('lang')=='english')
           {
              $crud->set_relation('module_id','modules','name_en');
              $crud->set_relation('controller_id','controllers','name_en');
             
           }else{
              $crud->set_relation('module_id','modules','name');
              $crud->set_relation('controller_id','controllers','name');
             
           }
			//$crud->fields('customerName','contactLastName','phone','countryID','stateID','cityID');
			//$crud->required_fields('countryID','stateID','cityID');		
			
			//IF YOU HAVE A LARGE AMOUNT OF DATA, ENABLE THE CALLBACKS BELOW - FOR EXAMPLE ONE USER HAD 36000 CITIES AND SLOWERD UP THE LOADING PROCESS. THESE CALLBACKS WILL LOAD EMPTY SELECT FIELDS THEN POPULATE THEM AFTERWARDS
			$crud->callback_add_field('controller_id', array($this, 'empty_state_dropdown_select'));
			$crud->callback_edit_field('controller_id', array($this, 'empty_state_dropdown_select'));
			/*$crud->callback_add_field('cityID', array($this, 'empty_city_dropdown_select'));
			$crud->callback_edit_field('cityID', array($this, 'empty_city_dropdown_select'));*/
						
			$output = $crud->render();
			
			//DEPENDENT DROPDOWN SETUP
			$dd_data = array(
				//GET THE STATE OF THE CURRENT PAGE - E.G LIST | ADD
				'dd_state' =>  $crud->getState(),
				//SETUP YOUR DROPDOWNS
				//Parent field item always listed first in array, in this case countryID
				//Child field items need to follow in order, e.g stateID then cityID
				'dd_dropdowns' => array('module_id','controller_id'),
				//SETUP URL POST FOR EACH CHILD
				//List in order as per above
				'dd_url' => array('', site_url().'/root/exampless/get_controllers/'),
				//LOADER THAT GETS DISPLAYED NEXT TO THE PARENT DROPDOWN WHILE THE CHILD LOADS
				'dd_ajax_loader' => base_url().'ajax-loader.gif'
			);
			$output->dropdown_setup = $dd_data;
			
			$this->_example_output($output);
	}	
	
	//CALLBACK FUNCTIONS
	function empty_state_dropdown_select()
	{
		//CREATE THE EMPTY SELECT STRING
		$empty_select = '<select name="controller_id" class="chosen-select" data-placeholder="Select State/Province" style="width: 300px; display: none;">';
		$empty_select_closed = '</select>';
		//GET THE ID OF THE LISTING USING URI
		$listingID = $this->uri->segment(4);
		
		//LOAD GCRUD AND GET THE STATE
		$crud = new grocery_CRUD();
		$state = $crud->getState();
		
		//CHECK FOR A URI VALUE AND MAKE SURE ITS ON THE EDIT STATE
		if(isset($listingID) && $state == "edit") {
			//GET THE STORED STATE ID
			$this->db->select('modulr_id, controller_id')
					 ->from('methods')
					 ->where('id', $listingID);
			$db = $this->db->get();
			$row = $db->row(0);
			$countryID = $row->modulr_id;
			$stateID = $row->controller_id;
			
			//GET THE STATES PER COUNTRY ID
			$this->db->select('*')
					 ->from('controllers')
					 ->where('module_id', $countryID);
			$db = $this->db->get();
			
			//APPEND THE OPTION FIELDS WITH VALUES FROM THE STATES PER THE COUNTRY ID
			foreach($db->result() as $row):
				if($row->state_id == $stateID) {
					$empty_select .= '<option value="'.$row->controller_id.'" selected="selected">'.$row->name_en.'</option>';
				} else {
					$empty_select .= '<option value="'.$row->controller_id.'">'.$row->name_en.'</option>';
				}
			endforeach;
			
			//RETURN SELECTION COMBO
			return $empty_select.$empty_select_closed;
		} else {
			//RETURN SELECTION COMBO
			return $empty_select.$empty_select_closed;	
		}
	}
	function empty_city_dropdown_select()
	{
		//CREATE THE EMPTY SELECT STRING
		$empty_select = '<select name="cityID" class="chosen-select" data-placeholder="Select City/Town" style="width: 300px; display: none;">';
		$empty_select_closed = '</select>';
		//GET THE ID OF THE LISTING USING URI
		$listingID = $this->uri->segment(4);
		
		//LOAD GCRUD AND GET THE STATE
		$crud = new grocery_CRUD();
		$state = $crud->getState();
		
		//CHECK FOR A URI VALUE AND MAKE SURE ITS ON THE EDIT STATE
		if(isset($listingID) && $state == "edit") {
			//GET THE STORED STATE ID
			$this->db->select('stateID, cityID')
					 ->from('customers')
					 ->where('customerNumber', $listingID);
			$db = $this->db->get();
			$row = $db->row(0);
			$stateID = $row->stateID;
			$cityID = $row->cityID;
			
			//GET THE CITIES PER STATE ID
			$this->db->select('*')
					 ->from('city')
					 ->where('stateID', $stateID);
			$db = $this->db->get();
			
			//APPEND THE OPTION FIELDS WITH VALUES FROM THE STATES PER THE COUNTRY ID
			foreach($db->result() as $row):
				if($row->city_id == $cityID) {
					$empty_select .= '<option value="'.$row->city_id.'" selected="selected">'.$row->city_title.'</option>';
				} else {
					$empty_select .= '<option value="'.$row->city_id.'">'.$row->city_title.'</option>';
				}
			endforeach;
			
			//RETURN SELECTION COMBO
			return $empty_select.$empty_select_closed;
		} else {
			//RETURN SELECTION COMBO
			return $empty_select.$empty_select_closed;	
		}
	}
				
	//GET JSON OF STATES
	function get_modules()
	{
		$moduleID = $this->uri->segment(3);
		
		$this->db->select("*")
				 ->from('modules')
				 ->where('module_id', $moduleID);
		$db = $this->db->get();
		
		$array = array();
		foreach($db->result() as $row):
			$array[] = array("value" => $row->module_id, "property" => $row->name_en);
		endforeach;
		
		echo json_encode($array);
		exit;
	}
	
	//GET JSON OF CITIES
	function get_controllers($stateID)
	{
		//$stateID = $this->uri->segment(3);
		
		$this->db->select("*")
				 ->from('controllers')
				 ->where('module_id', $stateID);
		$db = $this->db->get();
		
		$array = array();
		foreach($db->result() as $row):
			$array[] = array("value" => $row->id, "property" => $row->name_en);
		endforeach;
		
		echo json_encode($array);
		exit;
	}
}