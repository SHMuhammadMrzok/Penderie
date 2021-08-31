<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
/***
 * File: (Codeigniterapp)/libraries/Controllerlist.php
 * 
 * A simple library to list al your controllers with their methods.
 * This library will return an array with controllers and methods
 * 
 * The library will scan the "controller" directory and (in case of) one (1) subdirectory level deep
 * for controllers
 * 
 * Usage in one of your controllers:
 * 
 * $this->load->library('controllerlist');
 * print_r($this->controllerlist->getControllers());
 * 
 * @author Peter Prins 
 */
class ModuleList {
	
	/**
	 * Codeigniter reference 
	 */
	private $CI;
	
	/**
	 * Array that will hold the controller names and methods
	 */
	private $aControllers;
    private $aModules;
    
    private $crud_states = array('list', 'read', 'add', 'edit', 'delete', 'insert', 'update', 'upload_file', 'delete_file', 'export', 'print'
    );
	
	// Construct
	public function __construct() {
		// Get Codeigniter instance 
		$this->CI = get_instance();
		
		// Get all controllers 
		$this->setControllers();
	}
    
    public function listModules()
    {
		$all_modules_list = $this->aModules;
        $modules_list = array();
        
        foreach($all_modules_list as $module_name=>$controllers_list)
        {
            $modules_list[] = $module_name;
        }
        
        return $modules_list;
	}
    
    public function listControllers($module)
    {
        //echo $module;
        $modules_list = $this->aModules;
        $controllers_list = array();
        
        foreach($modules_list as $module_name=>$all_controllers_list)
        {
            if($module_name == $module)
            {
                foreach($all_controllers_list as $controller_name=>$methods_list)
                {
                    $controllers_list[] = $controller_name;
                }
            }
        }
        //print_r($controllers_list);die();
        return $controllers_list;
	}
    
    public function listMethods($module, $controller)
    {
		$modules_list = $this->aModules;
        $all_methods_list = array();
        
        foreach($modules_list as $module_name=>$controllers_list)
        {
            if($module_name == $module)
            {
                foreach($controllers_list as $controller_name=>$all_methods_list)
                {
                    if($controller_name == $controller)
                    {
                        foreach($all_methods_list as $method_name)
                        {
                            $methods_list[] = $method_name;
                        }
                    }
                }
            }
        }
        
        return $methods_list;
	}
    	
	/**
	 * Return all modules and their controllers and their methods
	 * @return array
	 */
	public function getModules() {
		return $this->aModules;
	}
    
    public function setModuleControllers($p_sModuleName, $p_sControllerName) {
		$this->aModules[$p_sModuleName] = $this->aControllers[$p_sModuleName];
	}
	
	/**
	 * Set the array holding the controller name and methods
	 */
	public function setControllerMethods($p_sModuleName, $p_sControllerName, $p_aControllerMethods) {
		$this->aControllers[$p_sModuleName][$p_sControllerName] = $p_aControllerMethods;
	}
	
	/**
	 * Search and set controller and methods.
	 */
	private function setControllers() 
    {
	   
       foreach(glob(APPPATH . 'modules/*') as $module) {
        
        $modulename = end(explode('/', $module));
       
		// Loop through the controller directory
		foreach(glob($module .'/controllers/*') as $controller) {
			
			// if the value in the loop is a directory loop through that directory
			if(is_dir($controller)) {
				// Get name of directory
				$dirname = basename($controller, EXT);
				
				// Loop through the subdirectory
				foreach(glob(APPPATH . 'controllers/'.$dirname.'/*') as $subdircontroller) {
					// Get the name of the subdir
					$subdircontrollername = basename($subdircontroller, EXT);
					
					// Load the controller file in memory if it's not load already
					if(!class_exists($subdircontrollername)) {				
						$this->CI->load->file($subdircontroller);
					}					
					// Add the controllername to the array with its methods
					$aMethods = get_class_methods($subdircontrollername);
					$aUserMethods = array();
					foreach($aMethods as $method) {
						if($method != '__construct' && $method != 'get_instance' && $method != $subdircontrollername && $method{0} != '_') {
							$aUserMethods[] = $method;
						}
					}
					$this->setControllerMethods($modulename, $subdircontrollername, $aUserMethods);	
                    $this->setModuleControllers($modulename, $subdircontrollername);				 					
				}
			}
			else if(pathinfo($controller, PATHINFO_EXTENSION) == "php"){
				// value is no directory get controller name				
			    $controllername = basename($controller, EXT);
									
				// Load the class in memory (if it's not loaded already)
				if(!class_exists($controllername)) {
					$this->CI->load->file($controller);
				}				
					
				// Add controller and methods to the array
                //echo $controllername;
				$aMethods = get_class_methods($controllername);
                //$aVars = array_keys(get_class_vars($controllername));
                $aVars = get_class_vars($controllername);
                if(!empty($aVars))
                {
                    $aVars = array_keys($aVars);
                }
                
               // print_r($aVars);
				$aUserMethods = array();
				if(is_array($aMethods)){
					foreach($aMethods as $method) {
						if($method != '__construct' && $method != 'get_instance' && $method != $controllername && $method{0} != '_') {
							$aUserMethods[] = $method;
						}
					}
				}
               if(!empty($aVars))
               {
                    if(in_array('crud', $aVars))
                    {
                        $aUserMethods = array_merge($aUserMethods, $this->crud_states);
                    
                    }
               } 
                
									
				$this->setControllerMethods($modulename, $controllername, $aUserMethods);
                $this->setModuleControllers($modulename, $controllername);								
			}
		}	//foreach controllers
        }//foreach modules
	}
}
// EOF