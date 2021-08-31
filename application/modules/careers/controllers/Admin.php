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
        
        $this->load->model('careers_model');
               
        $this->lang_row = $this->admin_bootstrap->get_active_language_row(); 
    }

    

    public function index()
    {
        $lang_id = $this->data['active_language']->id;
        
        $this->data['count_all_records'] = $this->careers_model->get_count_all_messages();
        $this->data['data_language']     = $this->lang_model->get_active_data_languages();
        
       
        $columns           = array(
                                                     lang('name'),
                                                     lang('email'),
                                                     lang('mobile'),
                                                     lang('applied_job'),
                                                     lang('unix_time'),
                                                   );
        
        $this->data['columns']           = $columns;    
        //$this->data['orders']            = $this->data['columns'];
        
        $this->data['actions']           = array( 'delete'=>lang('delete'));
        $this->data['search_fields']     = array( lang('name'), lang('email'));
        
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
       
        $offset  = ($active_page-1) * $limit;
        
        if(isset($_POST['search_word']) || trim($_POST['search_word']) == '')
        { 
            $search_word = $this->input->post('search_word');
        }
        else
        {
            $search_word = '';
        }
        /*
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
        */
        
        $grid_data   = $this->careers_model->get_grid_data($limit, $offset, $search_word);
        
        $db_columns  = array(
                                'id'        ,   
                                'name'      ,
                                'email'     ,   
                                'mobile'    ,
                                'applied_job' ,
                                'unix_time'
                            );
        
        $this->data['hidden_fields'] = array('id');
        
        $new_grid_data = array();
        
        foreach($grid_data as $key =>$row)
        { 
            foreach($db_columns as $column)
            {
                if($column == 'unix_time')
                {    
                    $new_grid_data[$key][$column] = date('Y/m/d',$row->unix_time);   
                }
                else
                {    
                    $new_grid_data[$key][$column] = $row->{$column};
                }
                
                
            }
        }
        
        $this->data['grid_data']          = $new_grid_data; 
        $this->data['unset_edit']         = true;
        $this->data['count_all_records']  = $this->careers_model->get_count_all_messages($search_word);
        
        $this->data['display_lang_id']    = $lang_id;
         
        
        $count_data  = $this->data['count_all_records'];
        $output_data = $this->load->view('Admin/grid/grid_data',$this->data, true);
        
        echo json_encode(array($output_data, $count_data, $search_word));
     }
     
    
     
     
     public function read($id)
     {
        $id = intval($id);
        
        if($id )
        {
            $data     = $this->careers_model->get_row_data($id);
            
            $row_data = array(
                                 lang('name')           => $data->name,
                                 lang('email')          => $data->email,
                                 lang('mobile')         => $data->mobile,
                                 //lang('phone')          => $data->phone,
                                 //lang('city')           => $data->city,
                                 //lang('address')        => $data->address,
                                 //lang('postal_code')    => $data->postal_number,
                                 //lang('mailbox')        => $data->mailbox       ,
                                 //lang('education')      => $data->education     ,
                                 //lang('date_of_birth')  => $data->date_of_birth ,
                                 lang('applied_job')    => $data->applied_job   ,   
                                 //lang('experience')     => $data->experience    ,
                                 lang('cv')             =>"<a href='" . base_url() . "careers/admin/download/". $data->cv ."'>".lang('download')."</a>"
                                
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
        $ids = $this->input->post('row_id');

        if(is_array($ids))
        { 
            
            $ids_array = array();
            
            foreach($ids as $id)
            {
                $ids_array[] = $id['value'];
            }
        }
        else
        { 
            $ids_array = array($ids);
        }
            
        $this->careers_model->delete_data($ids_array);
        
     }  
     
    public function download($filename)
    {
        $this->load->helper('file');
        $this->load->helper('download');
        
        $data = file_get_contents(APPPATH. '../assets/uploads/'.urldecode($filename)); // Read the file's contents
        
        force_download($filename, $data);
    }
    
    
/************************************************************************/    
}