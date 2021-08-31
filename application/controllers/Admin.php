<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
   
class Admin extends CI_Controller {
    
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model('global_model');     
        
        if(!isset($_SESSION['lang_id']))
        {    
             $settings     = $this->global_model->get_config();
             $default_lang = $settings->default_lang;
             $lang_row     = $this->global_model->get_lang_row($default_lang);
             
             $this->lang->load($lang_row->language, $lang_row->language);
        }
        else
        {
            $lang      = $_SESSION['lang_id'];
            $lang_row  = $this->global_model->get_lang_row($lang);
            $this->lang->load($lang_row->language, $lang_row->language);
        }
        
        $settings = $this->global_model->get_config();
    
        foreach($settings as $key => $value)
        {
            $this->config->set_item($key, $value);
        }
        
    }


    var $data = array();
    
    public function dashboard()
    {
        require(APPPATH . 'includes/global_vars.php');
        
        $this->data['content'] = $this->load->view('Admin/dashboard','null',true);
        $this->load->view('Admin/main_frame',$this->data);
    }
	public function index()
	{
    	  if ($this->ion_auth->logged_in())
          { 
             redirect(base_url().'admin/dashboard','refresh');
          }
          else
          {
             redirect(base_url().'admin/login','refresh');
          }
	}
    
   function change_lang($lang='english')
   {
       $lang_row  = $this->global_model->get_language_row_by_lang($lang);
       $this->session->set_userdata('lang_id', $lang_row->id);
       redirect($this->session->userdata('last_location'),'refresh');
   }
   
   function login()
   {
        if ($this->ion_auth->logged_in()){
            
             redirect(base_url().'admin/dashboard','refresh');
          
          }else{
             
            $this->load->model('root/lang_model');
            $data['structure_languages']= $this->lang_model->get_active_structure_languages();
            
            $this->form_validation->set_rules('email', lang('email'),'required|valid_email');
            $this->form_validation->set_rules('password', lang('password'), 'required');
            
            $this->form_validation->set_message('required', lang('required'));
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
           
            if ($this->form_validation->run() == true)
            { 
                
                $remember = (bool)$this->input->post('remember'); 
                if ($this->ion_auth->login($this->input->post('email'), $this->input->post('password'),$remember))
                {
                     $lang     = $this->input->post('lang');
                     $lang_row = $this->global_model->get_language_row_by_lang($lang);
                     $this->session->set_userdata('lang_id', $lang_row->id);
                     
                     if($redir = $this->session->userdata('redir')) 
                     {
                         redirect($redir);
                     }  
                     
                     //-->>if the login is successful redirect  to dashboard
                     $this->session->set_flashdata('message', $this->ion_auth->messages());
                     redirect(base_url().'admin/dashboard','refresh');
                
                }
                else
                {
                    //-->>if the login was un-successful redirect  to the login page
                    $data['login_error']=$this->ion_auth->errors();
                    $this->session->set_flashdata('login_error', $this->ion_auth->errors());
                    
                    $this->load->view('Admin/login',$data);
                    
                    //use redirects instead of loading views for compatibility with MY_Controller libraries
                }
            
            }
            else
            {
               $data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
               
               $this->load->view('Admin/login', $data);
            }
        }
   } 
   
    public function logout()
	{
	   $this->ion_auth->logout();
       redirect(base_url().'admin/login','refresh');
	}
    
    public function forgot_password()
	{
	   	//get the identity type from config and send it when you load the view
		$identity = 'email'; 
		$identity_human = lang('email'); //if someone uses underscores to connect words in the column names
		$this->form_validation->set_rules($identity, $identity_human, 'required | valid_email');
	
    	if ($this->form_validation->run() == false)
		{
			//setup the input
			$this->data[$identity] = array(
											'name' => $identity,
											'id' => $identity, //changed
											'type' => 'text',											
			                             );
			//set any errors and display the form
			$this->data['forget_message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
			$this->data['identity'] = $identity; 
			$this->data['identity_human'] = $identity_human;
			$this->load->view('Admin/login', $this->data,true);
			
		}else{
    		  
              //-->>> check if email found in db
              if ($this->ion_auth->email_check($this->input->post($identity)))
    		  {
        			//run the forgotten password method to email an activation code to the user
        			$forgotten = $this->ion_auth->forgotten_password($this->input->post($identity));
        			
        			if ($forgotten)
        			{ //if there were no errors
        				$this->session->set_flashdata('message',$this->ion_auth->messages());
        				 //we should display a confirmation page here instead of the login page
        			     redirect('admin/login', 'refresh');
                    }else{
        				$this->session->set_flashdata('message', $this->ion_auth->errors());
        				redirect('admin/login', 'refresh');
        			}
              
              }else {
    		        
                    $this->session->set_flashdata('message',lang('email_not_register'));
    				redirect('admin/login', 'refresh');
    		 }
       
        }//if validation 
	}
  
  public function reset_password($code)
	{
		$reset = $this->ion_auth->forgotten_password_complete($code);

		if ($reset) {  //if the reset worked then send them to the login page
			$this->session->set_flashdata('message', $this->ion_auth->messages());
			redirect("login", 'refresh');
		}
		else { //if the reset didnt work then send them back to the forgot password page
			$this->session->set_flashdata('message', $this->ion_auth->errors());
			redirect("auth/forgot_password", 'refresh');
		}
	}
    
      
   public function _transform_data()
   {
     $query=$this->db->get('methods');
     foreach($query->result() as $row)
     {
        $data=array('method_id'=>$row->id,'lang_id'=>1,'name'=>$row->name_en);
        $this->db->insert('methods_translation',$data);
        
         $ardata=array('method_id'=>$row->id,'lang_id'=>2,'name'=>$row->name);
        $this->db->insert('methods_translation',$ardata);
     }
   }
   
   public function _transform_lang()
   {
        $query=$this->db->get('lang_words');
        $lang_array=array();
        
        foreach($query->result() as $row)
        {
            $lang_array["{$row->lang_var}"]["{$row->lang_id}"] =$row->lang_text;
        }
        
        
        foreach($lang_array as $lang_var=>$lang_text)
        {
            $data=array('lang_var'=>$lang_var);
            $this->db->insert('lang_vars',$data);
            
            $lang_var_id=$this->db->insert_id();
            
            foreach($lang_text as $lang_id=>$lang_def)
            {
                $ardata=array('var_id'=>$lang_var_id,'lang_id'=>$lang_id,'lang_definition'=>$lang_def);
                $this->db->insert('lang_translation',$ardata);
            }
            
        }
   }
/******************************************************************************/
  
}
/* End of file admin.php */
/* Location: ./application/controllers/admin.php */
