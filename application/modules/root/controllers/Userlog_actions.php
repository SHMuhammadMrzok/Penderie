<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Userlog_actions extends CI_Controller
{
    public $data = array();
    public $crud;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->crud = new grocery_CRUD();
        $params     = array($this->crud);
        
        require(APPPATH . 'includes/global_vars.php');
        $this->load->model('log_actions_model');
    }
    
    public function index()
    {
        $lang_id = $this->data['active_language']->id;
        
        $this->data['count_all_records'] = $this->log_actions_model->get_count_all_actions($lang_id);
        $this->data['data_language']     = $this->lang_model->get_active_data_languages();
        
        $this->data['columns']           = array(
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
        
        
        $grid_data       = $this->log_actions_model->get_actions_data($lang_id,$limit,$offset,$search_word,$order_by,$order_state);
        
        $db_columns      = array(
                                 'id'          ,   
                                 'action_name' ,
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
        $this->data['count_all_records'] = $this->log_actions_model->get_count_all_actions($lang_id,$search_word);
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
            $data     = $this->log_actions_model->get_user_log_action_row($id,$display_lang_id);

            if($data->active == 1)
            {
                $active_value = lang('active');
            }
            if($data->active == 0)
            {
                $active_value = lang('not_active');
            }
            
            $row_data = array(
                                lang('action')     => $data->action_name ,
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
        $actions_ids = $this->input->post('row_id');

        if(is_array($actions_ids))
        { 
            
            $ids_array = array();
            
            foreach($actions_ids as $action_id)
            {
                $ids_array[] = $action_id['value'];
            }
        }
        else
        { 
            $ids_array = array($actions_ids);
        }
            
        $this->log_actions_model->delete_actions_data($ids_array);
        
     }  
    
    
    /*******************************************************/
    
     public function add_form()
     {
        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/save";
                
        $this->data['content']      = $this->load->view('userlog_actions', $this->data, true);
        
        $this->load->view('Admin/main_frame',$this->data);
        
     }
     
     public function save()
     {
        $method             = $this->input->post('method');
        $active             = $this->input->post('active');
        $languages          = $this->input->post('lang_id');
        
        foreach($languages as $lang_id)
        { 
            $this->form_validation->set_rules('name['.$lang_id.']', 'Name'.$lang_id, 'required');
           
        }
        $this->form_validation->set_message('required', lang('required'));
       /* if ($this->form_validation->run() == FALSE)
		{
		    $this->session->set_flashdata('faild',lang('faild'));  
            redirect('vendors/admin/add_form/','refresh');
		
        }else{*/
		  
          
            $data           = array(
                                        'method'     => $method,
                                        'active'     => $active,
                                        'unix_time'=>time()
                                    );
            
            if($this->log_actions_model->insert_userlog_actions($data))
            {
            
                $last_insert_id = $this->db->insert_id();
                $name           = $this->input->post('name');
                               
                foreach($languages as $lang_id)
                {
                  $userlog_actions_translation_data = array(
                                                        'userlog_actions_id'  => $last_insert_id ,
                                                        'name'                => $name[$lang_id],
                                                        'lang_id'             => $lang_id ,
                                                     );
                    $this->log_actions_model->insert_userlog_actions_translation($userlog_actions_translation_data);
                }
                
                $this->session->set_flashdata('success',lang('success'));
               
                redirect('root/userlog_actions/index','refresh');
           }
       // }
     }
     
     public function edit_form($id)
     {
        $id = intval($id);
        
        if($id)
        {
            $this->data['form_action']      = $this->data['module'] . "/" . $this->data['controller'] . "/update";
            $this->data['id']               = $id;
            
            $general_data                   = $this->log_actions_model->get_userlog_actions_result($id);
            $data                           = $this->log_actions_model->get_userlog_actions_translation_result($id);
            
            $filtered_data   = array();
            
            foreach($data as $row)
            {
                $filtered_data[$row->lang_id] = $row;
            }
            
            $this->data['general_data'] = $general_data ;
            $this->data['data']         = $filtered_data;
            
            $this->data['content']      = $this->load->view('userlog_actions', $this->data, true);
            
            $this->load->view('Admin/main_frame',$this->data);
        }
     }
     
     public function update()
     {
        $method                 = $this->input->post('method');
        $active                 = $this->input->post('active');
        $name                   = $this->input->post('name');
        $userlog_actions_id     = $this->input->post('userlog_actions_id');
        $languages              = $this->input->post('lang_id');
        
        $userlog_actions_data   =  array(
                                    'method'        => $method,
                                    'active'        => $active,
                                );
                                
        $this->log_actions_model->update_userlog_actions($userlog_actions_id,$userlog_actions_data);
                                        
        foreach($languages as $lang_id)
        {
            $userlog_actions_translation_data = array(
                                                'name'         => $name[$lang_id],
                                              );
            $this->log_actions_model->update_userlog_actions_translation($userlog_actions_id,$lang_id,$userlog_actions_translation_data);
        }
        
        $this->session->set_flashdata('success',lang('success'));
        redirect('root/userlog_actions/index','refresh');
        
     }
    /******************************************************/
  public function log_user_after_insert($post_array,$primary_key)
  {
        $data=array('unix_time'=>time() );
        $this->db->where('id',$primary_key);
        $this->db->update('userlog_actions',$data);
  }
 /*******************************************************************/ 
}?>