<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Admin_users_balance extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        require(APPPATH . 'includes/global_vars.php');
        
        $this->load->model('user_model');
        $this->load->model('user_balance_model');
        
    }
 
    private function _js_and_css_files()
     {
        $this->data['css_files'] = array();
        
        $this->data['js_files']  = array( );
        
        $this->data['js_code']   = "";
    }
    
    public function index()
    {
        $lang_id = $this->data['active_language']->id;
        
        $this->data['count_all_records']    = $this->user_balance_model->get_count_all_balance_operations($lang_id);
        $this->data['data_language']        = $this->lang_model->get_active_data_languages();
        
        
        /*$this->data['filters']              = array(
                                                    array(
                                                          'filter_title' => '',
                                                          'filter_name'  => '',
                                                          'filter_data'  => ''
                                                        )
                                                 );
                                                 */
        $this->data['columns']              = array(
                                                     lang('username'),
                                                     lang('payment_method'),
                                                     lang('code'),
                                                     lang('balance'),
                                                     lang('amount'),
                                                     lang('status'),
                                                     lang('ip_address'),
                                                     lang('unix_time')                                    
                                                 );
                                                   
        
        $this->data['actions']              = array( 'delete'=>lang('delete'));
        $this->data['search_fields']        = array( lang('username'), lang('balance'), lang('payment_method'));
        
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
        
        $list_filter = '';
        
        if(isset($_POST['filter'])&& isset($_POST['filter_data']))
        {
            $filters      = $this->input->post('filter');
            $filters_data = $this->input->post('filter_data');
            
            $list_filter  = $filters_data[0];
         
        }
        else
        {
            $list_filter     = 2;      
        }  
        
        $grid_data       = $this->user_balance_model->get_users_balance_data($limit, $offset, $lang_id, $search_word, $order_by, $order_state);
       
        $db_columns      = array(
                                  'id'                ,   
                                  'first_name'        ,
                                  'payment_method'    ,
                                  'code'              ,
                                  'balance'           ,
                                  'amount'            ,
                                  'balance_status'    ,
                                  'ip_address'        ,
                                  'unix_time'
                                 );
                       
       $this->data['hidden_fields'] = array('id');
                                        
       $new_grid_data = array();
        
        foreach($grid_data as $key =>$row)
        { 
            foreach($db_columns as $column)
            {
                if($column == 'first_name')
                {
                    
                    $new_grid_data[$key][$column] = $row->first_name.' '.$row->last_name;
               
                }elseif($column == 'code')
                {
                    if($row->{$column} == 0)
                    {
                        $new_grid_data[$key][$column] = '<span class="badge badge-danger">'.lang('no').'</span>';    
                    }
                    elseif($row->{$column} == 1)
                    {
                        $new_grid_data[$key][$column] = '<span class="badge badge-success">'.lang('yes').'</span>';
                    }
                }
                elseif($column == 'unix_time')
                {
                    $new_grid_data[$key][$column] = date('Y/m/d', $row->unix_time);
                }
                else
                {
                    $new_grid_data[$key][$column] = $row->{$column};
                }
            }
        }
        
        $this->data['grid_data']         = $new_grid_data;
        $this->data['count_all_records'] = $this->user_balance_model->get_count_all_balance_operations($lang_id,$search_word , $list_filter);
        $this->data['display_lang_id']   = $lang_id;
         
        $output_data = $this->load->view('Admin/grid/grid_data',$this->data, true);
        $count_data  = $this->data['count_all_records'];
        
        echo json_encode(array($output_data, $count_data, $search_word));
    }
    
     public function add()
    {
        $validation_msg = false;
        
        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
        {
            $validation_msg = true;
        }
        
        if ($this->form_validation->run() == FALSE)
		{
		  $this->_add_form($validation_msg);
        }
        else
        {
            
        }
    }
    
    private function _add_form($validation_msg)
    {
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }
        
    }
    
    
    public function read($id, $display_lang_id)
    {
        $this->load->model('orders/orders_model');
        $this->load->model('payment_options/bank_accounts_model');
        $this->load->model('payment_options/payment_methods_model');
        
        $id              = intval($id);
        $display_lang_id = intval($display_lang_id);
        
        if($id && $display_lang_id)
        {
            $data       = $this->user_model->get_user($display_lang_id,$id);
            $active     = '';
            $mail_list  ='' ;
            
            if($data)
            {
               if($data->active == 0)
                {
                    $active = '<span class="badge badge-danger">'.lang('not_active').'</span>';    
                }
                elseif($data->active == 1)
                {
                    $active = '<span class="badge badge-success">'.lang('active').'</span>';
                }
                
                if($data->account_sms_activated == 0)
                {
                    $account_sms_activated = '<span class="badge badge-danger">'.lang('not_active').'</span>';    
                }
                elseif($data->account_sms_activated == 1)
                {
                    $account_sms_activated = '<span class="badge badge-success">'.lang('active').'</span>';
                }
                
                if($data->mail_list == 0)
                {
                    $mail_list = '<span class="badge badge-danger">'.lang('no').'</span>';    
                }
                elseif($data->mail_list == 1)
                {
                    $mail_list = '<span class="badge badge-success">'.lang('yes').'</span>';
                }
                
                 $pic ="<a href='".base_url()."assets/uploads/84e3c-user_default.jpg"."' class='image-thumbnail' ><img src='".base_url()."assets/uploads/84e3c-user_default.jpg"."' width='150' height='50'  /></a>";;
        
                if($data->image)
                {
                    $pic = "<a href='".base_url()."assets/uploads/".$data->image."' class='image-thumbnail' ><img src='".base_url()."assets/uploads/".$data->image."' width='150' height='50'  /></a>";
                }
                
                $row_data = array(
                                   lang('first_name')           => $data->first_name ,
                                   lang('last_name')            => $data->last_name  ,
                                   lang('email')                => $data->email      ,
                                   lang('address')              => $data->address    ,
                                   lang('group')                => $data->name       ,
                                   lang('customer_group_name')  => $data->title      ,
                                   lang('photo')                => $pic              ,
                                   lang('active')               => $active           ,
                                   lang('mobile_active')        => $account_sms_activated,
                                   lang('mail_list')            => $mail_list        ,
                                   lang('resend_verification_email') => '<a type="button" class="btn blue-hoki" href="'.base_url().'users/admin_users/resend_user_activation_email/'.$data->id.'/1" ><i class="fa fa-send-o (alias)"></i> '.lang('e-mail').'</a>',
                                   lang('resend_verification_sms')   => '<a type="button" class="btn blue-madison" href="'.base_url().'users/admin_users/resend_user_sms_code/'.$data->id.'/1"><i class="fa fa-mobile-phone (alias)"></i> '.lang('sms').'</a>' 
                                 );
          
                
                
                $user_affiliate_log_data                = $this->user_model->get_user_affiliate_log($data->id);
                $user_orders_log_data                   = $this->user_model->get_user_orders_data($data->id, $display_lang_id);
                $user_log_data                          = $this->user_model->get_user_log_data($data->id, $display_lang_id);
                $visits_log_data                        = $this->user_model->get_visits_log_data($data->id, $display_lang_id);
                
                $this->data['row_data']                 = $row_data;
                $this->data['user_affiliate_log_data']  = $user_affiliate_log_data;
                $this->data['user_orders_log_data']     = $user_orders_log_data;
                $this->data['user_log_data']            = $user_log_data;
                $this->data['visits_log_data']          = $visits_log_data;
                $this->data['lang_id']                  = $display_lang_id;
            }
            
            $this->data['content'] = $this->load->view('users_read', $this->data, true);
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
        $this->load->model('orders/orders_model');
        
        $users_ids = $this->input->post('row_id');

        if(is_array($users_ids))
        {  
            $ids_array = array();
            
            foreach($users_ids as $user_id)
            {
                $ids_array[] = $user_id['value'];
            }
        }
        else
        { 
            $ids_array = array($users_ids);
        }
        
       
        foreach($ids_array as $id)
        {
            $check_user_orders = $this->orders_model->check_user_have_orders($id);
            $user_data         = $this->user_model->get_row_data($id);
            $secret_key        = $this->config->item('new_encryption_key');
            $secret_iv         = $id;
            
            $user_balance      = $this->encryption->decrypt($user_data->user_balance, $secret_key, $secret_iv);
            $user_points       = $this->encryption->encrypt($user_data->user_points, $secret_key, $secret_iv);
            
            if($check_user_orders)
            {
                echo lang('cant_delete_user_have_orders');
            }
            elseif($user_balance > 0)
            {
                echo lang('cant_delete_user_has_pocket_money');
            }
            elseif($user_points > 0)
            {
                echo lang('cant_delete_user_has_points');
            }
            else
            {
                $this->ion_auth->delete_user($id);
                echo "1";
            }
        }
        
        
    }  
/////////////////////////////////////////////////////////////////////
    
    public function add()
    {
        $validation_msg = false;
        
        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
        {
            $this->form_validation->set_rules('first_name', lang('first_name'), 'trim|required');
            $this->form_validation->set_rules('email', lang('email'), 'trim|valid_email|required|is_unique[users.email]');
            $this->form_validation->set_rules('password', lang('password'), 'trim|required');
            $this->form_validation->set_rules('last_name', lang('last_name'), 'trim|required');
            $this->form_validation->set_rules('customer_group_id', lang('customer_group_name'), 'required');
           
            $this->form_validation->set_message('required', lang('required')." : %s ");
            $this->form_validation->set_message('is_unique', lang('is_unique')."  : %s ");
            $this->form_validation->set_message('valid_email', lang('valid_email')."  : %s ");
            
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');  
            $validation_msg = true;
        }
        
        if ($this->form_validation->run() == FALSE)
		{
		  $this->_add_form($validation_msg);
        }
        else
        {
            $username          = $this->input->post('first_name');
            $password          = $this->input->post('password');
            $email             = $this->input->post('email');
            $group             = $this->input->post('group_id');
            $first_name        = $this->input->post('first_name');
            $last_name         = $this->input->post('last_name');
            $phone             = $this->input->post('phone');
            $address           = $this->input->post('address');
            $image             = $this->input->post('image');
            $customer_group_id = $this->input->post('customer_group_id');
            
            
            if(isset($_POST['rep_user_id']))
            {
                $rep_user_id = $this->input->post('rep_user_id');
            }
            else
            {
                $rep_user_id = 0;
            }
            
            $login_auth  = 0;
            
            if(isset($_POST['auth'] ))
            {
                if($_POST['auth'] == 'google_auth')
                {
                    $login_auth = 2;
                }
                if($_POST['auth'] == 'sms_auth')
                {
                    $login_auth = 1;
                }
            }
            
            $additional_data = array(
                                        'first_name'            => $first_name,
                                        'last_name'             => $last_name,
                                        'phone'                 => $phone,
                                        'address'               => $address,
                                        'customer_group_id'     => $customer_group_id,
                                        'image'                 => $image,
                                        'rep_user_id'           => $rep_user_id,
                                        'active'                => (isset( $_POST['active']))? $this->input->post('active'):0,
                                        'account_sms_activated' => (isset( $_POST['mobile_active']))? $this->input->post('mobile_active'):0,
                                        'mail_list'             => (isset( $_POST['mail_list']))? $this->input->post('mail_list'):0,
                                        'login_auth'            => $login_auth
                                    );
            
            if (!$this->ion_auth->email_check($email))
            {
                $user_id   = $this->ion_auth->register($username, $password, $email, $additional_data, $group);
                $user_data = $this->user_model->get_user_data_by_field('id', $user_id);
                /***************************************/
                $data       = array (
                                        '{id}'         => $user_id, 
                                        '{username}'   => $first_name.' '.$last_name ,
                                        '{email}'      => $user_data->email,
                                        '{ip_address}' => $user_data->ip_address,
                                        '{created_on}' => date('Y-m-d H:i', $user_data->created_on),
                                        '{logo_path}'  => base_url().'assets/template/admin/img/logo.png'
                                    );
                $emails []  = $email;
                $this->notifications->create_notification('add_new_user',$data ,$emails);
                /****************************************/
                $this->session->set_flashdata('success',lang('success'));
                redirect('users/admin_users/','refresh');
            }
            else
            {
                $this->session->set_flashdata('user_register_error',lang('user_register_error'));
                redirect('users/admin_users/add','refresh');
            }
        }
        
    }
    
    private function _add_form($validation_msg)
    {
        $this->_js_and_css_files();
        $this->data['mode']         = 'add';
        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/add";
        
        $groups                     = $this->groups_model->get_groups($this->data['active_language']->id);
        $customers_groups           = $this->customer_groups_model->get_customer_groups($this->data['active_language']->id);
        
        $groups_options             = array();
        $customer_groups_options    = array();
        
        foreach($groups as $row)
        {
            
            $groups_options[$row->id] = $row->name;
        }
        
        foreach($customers_groups as $row)
        {   
            $customer_groups_options[$row->id] = $row->title;
        }
        
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }
        
        $this->data['groups_options']           = $groups_options;
        $this->data['customer_groups_options']  = $customer_groups_options;
        
        $this->data['content']                  = $this->load->view('users_view', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
    }
    
    public function sms_activate($code, $user)
    {
        $id = $user->id;
        $activation_code = array('sms_code'=>$code);
        // set hook for update meta data
        $this->ion_auth->set_hook('post_register_user', 'send_user_sms', $this->user_model, 'send_sms', array($id,$update));                    
        $this->ion_auth->update($id,$update);
    	return true;
    }
    
    public function edit($id)
    {
        if(is_numeric($id))
        {
            $id = intval($id);
            
            $validation_msg = false;
            
            if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
            {
                $id        = intval($this->input->post('id'));
                $email     = $this->input->post('email');
                $phone     = $this->input->post('phone');
                $user      = $this->user_model->get_row_data($id);
                
                $customer_group_id = $this->input->post('customer_group_id');
                if($user->customer_group_id != $customer_group_id)
                {
                    $data['customer_group_id'] = $customer_group_id;
                    $this->ion_auth->update($id, $data);
                    
                    $user = $this->user_model->get_row_data($id);
                }
                
                $is_wholesaler       = false;
                $settings            = $this->global_model->get_config();
                $wholesaler_group_id = $settings->wholesaler_customer_group_id;
                
                if($wholesaler_group_id == $user->customer_group_id)
                {
                    $is_wholesaler = true;
                }
                
                if($user->email != $email)
                {
                    $this->form_validation->set_rules('email', lang('email'), 'trim|valid_email|required|is_unique[users.email]');
                }
                
                if($user->phone != $phone)
                {
                    $this->form_validation->set_rules('phone', lang('phone'), 'trim|required|is_unique[users.phone]');
                }
                
                $this->form_validation->set_rules('first_name', lang('first_name'), 'trim|required');
                $this->form_validation->set_rules('last_name', lang('last_name'), 'trim|required');
                $this->form_validation->set_rules('customer_group_id', lang('customer_group_name'), 'trim|required');
                
                
                if($is_wholesaler)
                {
                    $this->form_validation->set_rules('image2', lang('google_maps_image'), 'trim|required');
                    $this->form_validation->set_rules('image3', lang('logo'), 'trim|required');
                    $this->form_validation->set_rules('sms_name', lang('sms_sender'), 'trim|required');
                    $this->form_validation->set_rules('sms_content', lang('sms_content'), 'trim|required');
                    $this->form_validation->set_rules('header', lang('header'), 'trim|required');
                    $this->form_validation->set_rules('footer', lang('footer'), 'trim|required');
                }
                
                $this->form_validation->set_message('required', lang('required')." : %s ");
                $this->form_validation->set_message('is_unique', lang('is_unique')."  : %s ");
                $this->form_validation->set_message('valid_email', lang('valid_email')."  : %s ");
                
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');  
                
                $general_data   = $this->user_model->get_user($this->data['active_language']->id , $id);
                $validation_msg = true;
            }
            
            if($this->form_validation->run() == FALSE)
    		{
    		   $this->_edit_form($id, $validation_msg);
            }
            else
            {
                $customer_group_id = $this->input->post('customer_group_id');
                $username          = $this->input->post('first_name');
                $first_name        = $this->input->post('first_name');
                $last_name         = $this->input->post('last_name');
                
                if(isset($_POST['rep_user_id']))
                {
                    $rep_user_id = $this->input->post('rep_user_id');
                }
                else
                {
                    $rep_user_id = 0;
                }
                
                $login_auth = 0;
                if(isset($_POST['auth'] ))
                {
                    //->>login_auth = 1 for login sms auth
                    //->>login_auth = 2 for login google auth
                    if($_POST['auth'] == 'google_auth')
                    {
                        $login_auth = 2;
                    }
                    if($_POST['auth'] == 'sms_auth')
                    {
                        $login_auth = 1;
                    }
                }
                
                if(isset( $_POST['password']) && $_POST['password']!= '')
                {
                   
                   $data = array(
                        'username'              => $username,
                        'password'              => $this->input->post('password'),
                        'email'                 => $this->input->post('email'),
                        'first_name'            => $first_name,
                        'last_name'             => $last_name,
                        'phone'                 => $this->input->post('phone'),
                        'address'               => $this->input->post('address'),
                        'customer_group_id'     => $customer_group_id,
                        'image'                 => $this->input->post('image'),
                        'rep_user_id'           => $rep_user_id,
                        'active'                => (isset( $_POST['active']))? $this->input->post('active'):0,
                        'account_sms_activated' => (isset( $_POST['mobile_active']))? $this->input->post('mobile_active'):0,
                        'mail_list'             => (isset( $_POST['mail_list']))? $this->input->post('mail_list'):0,
                        'login_auth'            => $login_auth
                        );
                }else{
              
                    $data = array(
                        'username'              => $username,
                        'email'                 => $this->input->post('email'),
                        'first_name'            => $first_name,
                        'last_name'             => $last_name,
                        'phone'                 => $this->input->post('phone'),
                        'address'               => $this->input->post('address'),
                        'customer_group_id'     => $customer_group_id,
                        'image'                 => $this->input->post('image'),
                        'rep_user_id'           => $rep_user_id,
                        'active'                => (isset( $_POST['active']))? $this->input->post('active'):0,
                        'account_sms_activated' => (isset( $_POST['mobile_active']))? $this->input->post('mobile_active'):0,
                        'mail_list'             => (isset( $_POST['mail_list']))? $this->input->post('mail_list'):0,
                        'login_auth'            => $login_auth
                        );
                }
                
                if($is_wholesaler)
                {
                    $data['google_map_image'] = $this->input->post('image2');
                    $data['logo']             = $this->input->post('image3');
                    $data['sms_name']         = $this->input->post('sms_name');
                    $data['sms_content']      = $this->input->post('sms_content');
                    $data['header']           = $this->input->post('header');
                    $data['footer']           = $this->input->post('footer');
                    
                }
                
                $this->ion_auth->update($id, $data);
                
                $groupData = $this->input->post('group_id');
                if (isset($groupData) && !empty($groupData)) 
                {
                    $this->ion_auth->remove_from_group('', $id);
                    foreach ($groupData as $grp) 
                    {
    		          $this->ion_auth->add_to_group($grp, $id);
                    }
                }
                
                $settings                     = $this->global_model->get_config();
                $wholesaler_customer_group_id = $settings->wholesaler_customer_group_id;
                
                if($customer_group_id == $wholesaler_customer_group_id)
                {
                    $emails[]      = $this->input->post('email');
                    $phone         = $this->input->post('mobile');
                    $template_data = array(
                                            'unix_time' => time(),
                                            'username'  => $username, 
                                          );
                                          
                    $this->notifications->create_notification('add_wholesaler', $template_data, $emails, $phone);
                }
                
                $this->session->set_flashdata('success',lang('updated_successfully'));
                redirect('users/admin_users','refresh');
            }
        }
    }
    
    private function _edit_form($id, $validation_msg)
    {
        $this->_js_and_css_files();
        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/edit/".$id;
        $this->data['mode']         = 'edit';
        $this->data['id']           = $id;
        
        $general_data               = $this->user_model->get_user($this->data['active_language']->id , $id);
        $user_groups                = $this->user_model->get_user_groups($this->data['active_language']->id , $id);
        
        $groups                     = $this->groups_model->get_groups($this->data['active_language']->id);
        $customers_groups           = $this->customer_groups_model->get_customer_groups($this->data['active_language']->id);
        
        $is_wholesaler       = false;
        $settings            = $this->global_model->get_config();
        $wholesaler_group_id = $settings->wholesaler_customer_group_id;
        
        if($wholesaler_group_id == $general_data->customer_group_id)
        {
            $is_wholesaler = true;
        }
        
        $groups_options             = array();
        $customer_groups_options    = array();
        $ugroups                    = array();
        
        foreach($user_groups as $group)
        {
            
            $ugroups [$group->group_id]=$group->group_id;
        }
      
        foreach($groups as $row)
        {
            
            $groups_options[$row->id] = $row->name;
        }
        
        foreach($customers_groups as $row)
        {
            
            $customer_groups_options[$row->id] = $row->title;
        }
        
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }
        
        $this->data['general_data']             = $general_data;
        $this->data['groups_options']           = $groups_options;
        $this->data['customer_groups_options']  = $customer_groups_options;
        $this->data['user_groups']              = $ugroups;
        $this->data['is_wholesaler']            = $is_wholesaler;
        
        $this->data['content']                  = $this->load->view('users_view', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
    }
    
    function Resend_verification($user_id)
    {
        $user= $this->admin_bootstrap->get_user_by_id($user_id);  
        $data = array(
    				'identity'   => $user->email,
    				'id'         => $user->id,
    				'email'      => $user->email,
    				'activation' => $user->activation_code,
    			);
                
            
        $message = $this->load->view($this->config->item('email_templates', 'ion_auth').$this->config->item('email_activate', 'ion_auth'), $data, true);
        
        $this->load->library('email');
       	$this->lang->load('ion_auth');
        $this->load->model('ion_auth_model');
        
		$this->email->clear();
		$this->email->from($this->config->item('admin_email', 'ion_auth'), $this->config->item('site_title', 'ion_auth'));
		$this->email->to($user->email);
		$this->email->subject($this->config->item('site_title', 'ion_auth') . ' - ' . $this->lang->line('email_activation_subject'));
		$this->email->message($message);

		if ($this->email->send() == TRUE)
		{
			$this->ion_auth_model->trigger_events(array('post_account_creation', 'post_account_creation_successful', 'activation_email_successful'));
			$this->ion_auth->set_message('activation_email_successful');
			//return $user->id;
		}
        redirect(base_url().'users/users/','refresh');
    }
    
    public function get_representatives()
    {
        $group_id = $this->input->post('customer_group');
        $settings = $this->global_model->get_config();
        $wholesaler_group_id = $settings->wholesaler_customer_group_id;
        
        if($group_id == $wholesaler_group_id)
        {
            $rep_group_id    = $settings->rep_group_id;
            $representatives = $this->users_model->get_representivives_users($rep_group_id);
            
            $rep_users       = "<select name='rep_user_id' class='form-control select2'><option value='0'>-----------</option>";
             
            foreach($representatives as $rep)
            {
                $rep_users .= "<option value=$rep->id>$rep->username</option>";
            }
            
            $rep_users .= "</select>";
            echo $rep_users;
        }
    }
    
    public function get_username()
    {
        $user_id = $this->input->post('rep_user_id');
        
        $user = $this->users_model->get_user($user_id);
        
        echo $user->username;
    }
    
    public function resend_user_sms_code($user_id, $details=0)
    {
        $lang_id = $this->data['active_language']->id;
        $user_id = intval($user_id);
        $user    = $this->user_model->get_user_data_by_field('id', $user_id);
        
        $this->load->library('notifications');
        $msg = lang('sms_activation_code').' : '.$user->sms_code;
        $this->notifications->send_sms($msg, $user->phone);
        
        $this->session->set_flashdata('success',lang('message_send'));
        
        if($details ==1)
        {
            redirect('users/admin_users/read/'.$user_id.'/'.$lang_id, 'refresh');
        }
        else
        {
            redirect('users/admin_users/', 'refresh');
        }
        
    }
    
    public function resend_user_activation_email($user_id, $details = 0)
    {
        $lang_id = $this->data['active_language']->id;
        //-->> make activation code
        $deactivate = $this->ion_auth_model->deactivate($user_id);
        
    	$activation_code = $this->ion_auth_model->activation_code;
		$identity        = $this->config->item('identity', 'ion_auth');
		$user            = $this->ion_auth_model->user($user_id)->row();
        $email           = $user->email;
        
		$data = array(
        				'identity'   => $user->{$identity},
        				'id'         => $user_id,
        				'email'      => $email,
        				'activation' => $activation_code,
        			 );
        
        //-->>>send email 	
        $message = $this->load->view($this->config->item('email_templates', 'ion_auth').$this->config->item('email_activate', 'ion_auth'), $data, true);
        
		$this->email->clear();
		$this->email->from($this->config->item('admin_email', 'ion_auth'), $this->config->item('site_title', 'ion_auth'));
		$this->email->to($email);
		$this->email->subject($this->config->item('site_title', 'ion_auth') . ' - ' . $this->lang->line('email_activation_subject'));
		$this->email->message($message);

		if($this->email->send())
		{
            $this->session->set_flashdata('success', lang('email_sent')); 
		}
        else
        {
            $this->session->set_flashdata('faild',lang('email_not_sent'));
        }
        
        if($details ==1)
        {
            redirect('users/admin_users/read/'.$user_id.'/'.$lang_id, 'refresh');
        }
        else
        {
            redirect('users/admin_users/', 'refresh');
        }
        
    }
  
  
}