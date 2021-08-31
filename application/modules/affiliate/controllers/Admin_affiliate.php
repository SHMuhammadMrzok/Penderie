<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Admin_affiliate extends CI_Controller
{
   
    public function __construct()
    {
        parent::__construct();
        
        require(APPPATH . 'includes/global_vars.php');
       
        $this->load->model('admin_affiliate_model');
        $this->load->model('users/users_model');
    }
    
     private function _js_and_css_files()
     {
        $this->data['css_files'] = array(
            'global/plugins/jquery-tags-input/jquery.tagsinput.css',
            );
        
        $this->data['js_files']  = array(
            //Date Range Picker
            'global/plugins/bootstrap-daterangepicker/moment.min.js',
             //TouchSpin
            'global/plugins/fuelux/js/spinner.min.js',
            'global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js',
            'global/plugins/bootstrap-touchspin/bootstrap.touchspin.js',
            
            //Tags
            'tags/tag-it.js',
            );
        
        $this->data['js_code'] = "";
    }

    
    
    public function index()
    {
        $lang_id = $this->data['active_language']->id;
        
        $this->data['count_all_records']    = $this->admin_affiliate_model->get_count_all_affiliate($lang_id);
        $this->data['data_language']        = $this->lang_model->get_active_data_languages();
        
        $this->data['columns']              = array(
                                                     lang('username') ,
                                                     lang('email'),
                                                     lang('unix_time') ,
                                                     lang('active')
                                                   );
                                                   
        $this->data['orders']               = $this->data['columns'] ;                                                     
            
        $this->data['actions']              = array( 'delete'=>lang('delete'));
        $this->data['search_fields']        = array( lang('username'));
        
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
        
        
        $grid_data       = $this->admin_affiliate_model->get_affiliate_data($limit,$offset,$search_word,$order_by,$order_state);
        
        $db_columns      = array(
                                  'id'        ,
                                 'first_name' ,   
                                 'email'      ,
                                 'unix_time'  ,
                                 'active'     ,
                                );
                       
       $this->data['hidden_fields'] = array('id','sort');
                                           
       $new_grid_data = array();
        
        foreach($grid_data as $key =>$row)
        { 
            //$user
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
               elseif($column == 'first_name')
               {
                   $new_grid_data[$key][$column] = $row->first_name . " " . $row->last_name; 
               }
               elseif($column == 'unix_time')
               {         
                   $new_grid_data[$key][$column] = date('Y/m/d',$row->unix_time);
               }
               else
               {     
                    $new_grid_data[$key][$column] = $row->{$column};
               }
            }
        }
        
        
        $this->data['grid_data']         = $new_grid_data;
        $this->data['count_all_records'] = $this->admin_affiliate_model->get_count_all_affiliate($search_word);
        $this->data['display_lang_id']   = $lang_id;
         
        $output_data = $this->load->view('Admin/grid/grid_data',$this->data, true);
        $count_data  = $this->data['count_all_records'];
        
        echo json_encode(array($output_data, $count_data, $search_word));
    }
    
    
    
    public function read($id,$display_lang_id)
    {
        $id = intval($id);
    
        if($id)
        {
            $data = $this->admin_affiliate_model->get_row_data($id);
            
            if($data)
            {
                if($data->active == 1)
                {
                    $active_value = lang('active');
                    $class        = 'success';
                }
                else
                {
                    $active_value = lang('not_active');
                    $class        = 'danger';  
                }
                
                $row_data = array(
                                    lang('affiliate_user') => $data->first_name.' '. $data->last_name ,
                                    lang('email')          => $data->email ,
                                    lang('code')           => $data->code ,
                                    lang('commission')     => $data->commission."%" ,
                                    //lang('tax_id')         => $data->tax_id ,
                                    lang('num_uses')       => $data->num_uses ,
                                    lang('num_uses_done')  => $data->num_uses_done ,
                                    lang('unix_time')      => date('Y/m/d',$data->unix_time) ,
                                    lang('active')         => '<span class="badge badge-' . $class . '">' . $active_value . '</span>'
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
        $affiliate_ids = $this->input->post('row_id');

        if(is_array($affiliate_ids))
        { 
            
            $ids_array = array();
            
            foreach($affiliate_ids as $cat_id)
            {
                $ids_array[] = $cat_id['value'];
            }
        }
        else
        { 
            $ids_array = array($affiliate_ids);
        }
            
        $this->admin_affiliate_model->delete_affiliate_data($ids_array);
        
        
    }  
 
   /**************************************************************************/ 
   
    public function add()
    {
        $validation_msg = false;
        
        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
        {
            $validation_msg = true;
            
            $this->form_validation->set_rules('user_id', lang('username'), 'trim|required');
            $this->form_validation->set_rules('code', lang('code'), 'trim|required');
            $this->form_validation->set_rules('commission', lang('commission'), 'trim|required');
            
            $this->form_validation->set_message('required', lang('required')."  : %s ");
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        }
        
        if ($this->form_validation->run() == FALSE)
		{
		  $this->_add_form($validation_msg);
        }
        else
        {
            $general_data = array(
                                    'user_id'               => $this->input->post('user_id'),
                                    'code'                  => $this->input->post('code'),
                                    'unix_time'             => time(),
                                    'active'                => (isset( $_POST['active']))? $this->input->post('active'):0,
                                    'commission'            => $this->input->post('commission'),
                                    //'tax_id'                => $this->input->post('tax_id'),
                                    'num_uses'              => $this->input->post('num_uses'),
                                  );
            
            if($this->admin_affiliate_model->insert_affiliate($general_data))
            {
                $_SESSION['success'] = lang('success');
                $this->session->mark_as_flash('success');
                
                redirect('affiliate/admin_affiliate/','refresh');
            
            }
            else
            {
                $this->session->set_flashdata('failed',lang('failed'));   
                redirect('affiliate/admin_affiliate/','refresh');
            }
        }
    }
    
    private function _add_form($validation_msg)
    {
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }
        
        $this->_js_and_css_files();
                
        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/add";
        
        $affiliate_data = $this->admin_affiliate_model->get_affiliate_users();
        $affiliate_ids  = array();
        
        foreach($affiliate_data as $user)
        {
            $affiliate_ids[] = $user->user_id;
        }
        
        $users = $this->admin_affiliate_model->get_affiliae_available_users($affiliate_ids); 
        
        $options[NULL] = '-----------------';
        foreach($users as $row)
        {
            $options[$row->id] = $row->first_name . ' ' . $row->last_name;
        }
        
        /***********************************************/
        
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 13; $i++) 
        {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        /***********************************************/
        
        $this->data['options']  = $options;
        $this->data['code']     = $randomString;
        
        $this->data['content']  = $this->load->view('admin_affiliate', $this->data, true);
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
                
                $id = $this->input->post('id');
        
                $this->form_validation->set_rules('user_id', lang('user_id'), 'trim|required');
                $this->form_validation->set_rules('code', lang('code'), 'trim|required');
                
                $this->form_validation->set_message('required', lang('required'));
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            }
        }
        
        if($this->form_validation->run() == FALSE)
		{
		   $this->_edit_form($id, $validation_msg);
        }
        else
        {
            $general_data = array(
                                    'user_id'               => $this->input->post('user_id'),
                                    'code'                  => $this->input->post('code'),
                                    'unix_time'             => time(),
                                    'active'                => (isset( $_POST['active']))? $this->input->post('active'):0,
                                    'commission'            => $this->input->post('commission'),
                                    //'tax_id'                => $this->input->post('tax_id'),
                                    'num_uses'              => $this->input->post('num_uses')
                                  );
            
          if($this->admin_affiliate_model->update_category($id,$general_data))
          {
             $_SESSION['success'] = lang('success');
             $this->session->mark_as_flash('success');
          }
          else
          {    
             $_SESSION['failed'] = lang('failed');
             $this->session->mark_as_flash('failed');
          }
          redirect('affiliate/admin_affiliate/','refresh');
        }
    }
    
    private function _edit_form($id, $validation_msg)
    {
        $this->_js_and_css_files();
        
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }
        
        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/edit/" . $id;
        $this->data['id']           = $id;
        
        $general_data               = $this->admin_affiliate_model->get_row_data($id); 
        $affiliate_data             = $this->admin_affiliate_model->get_affiliate_users();
        $affiliate_ids              = array();
        
        foreach($affiliate_data as $user)
        {
            if($user->user_id != $general_data->user_id)
            {
                $affiliate_ids[] = $user->user_id;
            }
        }
        
        $users = $this->admin_affiliate_model->get_affiliae_available_users($affiliate_ids);
        
        $options[NULL] = '-----------------';
        foreach($users as $row)
        {
            $options[$row->id] = $row->first_name . ' ' . $row->last_name;
        }
        
       
        $this->data['general_data'] = $general_data;
        $this->data['options']      = $options;
        
        $this->data['content']      = $this->load->view('admin_affiliate', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
    }
    
    
    
/************************************************************************/    
}