<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Module extends CI_Controller
{
    public $data = array();
    public $crud;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->crud = new grocery_CRUD();
        $params     = array($this->crud);
        
        require(APPPATH . 'includes/global_vars.php');
        
        $this->load->model('modules_model');
    }
    public function index()
    {
        $lang_id = $this->data['active_language']->id;
        
        $this->data['count_all_records'] = $this->modules_model->get_count_all_modules($lang_id);
        $this->data['data_language']     = $this->lang_model->get_active_data_languages();
        
        $this->data['columns']           = array(
                                                     lang('module') ,
                                                     lang('icon_class')   ,
                                                     lang('active')
                                                   );
            
        $this->data['orders']           = array(
                                                     lang('module')     ,
                                                     lang('icon_class') ,
                                                     lang('active')     ,
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
        
        
        $grid_data       = $this->modules_model->get_modules_data($lang_id,$limit,$offset,$search_word,$order_by,$order_state);
        
        $db_columns      = array(
                                 'id'     ,   
                                 'module_name' ,
                                 'icon_class'   ,
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
        $this->data['count_all_records'] = $this->modules_model->get_count_all_modules($lang_id,$search_word);
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
        
        $this->modules_model->update_row_sort($id,$old_index,$new_index,$sort_state);
    }
    
    public function read($id,$display_lang_id)
    {
        $id              = intval($id);
        $display_lang_id = intval($display_lang_id);
        
        if($id && $display_lang_id)
        {
            $data     = $this->modules_model->get_row_data($id,$display_lang_id);
            
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
                                    lang('module')     => $data->module_name ,
                                    lang('icon_class') => $data->icon_class ,
                                    lang('active')     => '<span class="badge badge-info">'.$active_value.'</span>'
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
        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/save";
        $this->data['content']      = $this->load->view('modules', $this->data, true);
        
        $this->load->view('Admin/main_frame',$this->data);
        
     }
     
     public function save()
     {
        
        $languages   = $this->input->post('lang_id');
        
        foreach($languages as $lang_id)
        { 
            $this->form_validation->set_rules('name['.$lang_id.']', lang('name') , 'required');
           
        }
        $this->form_validation->set_rules('module', lang('module') , 'required');
        $this->form_validation->set_rules('active', lang('active') , 'required');
        $this->form_validation->set_rules('icon_class', lang('icon_class') , 'required');
        
        $this->form_validation->set_message('required', lang('required'));
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
       
        if ($this->form_validation->run() == FALSE)
		{
		    $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/save";
            $this->data['content']      = $this->load->view('modules', $this->data, true);
            
            $this->load->view('Admin/main_frame',$this->data);
    		
        }else{ /**/
        
            $module      = $this->input->post('module');
            $active      = $this->input->post('active');
            $icon_class  = $this->input->post('icon_class');
          
            $data           = array(
                                        'module'     => $module,
                                        'active'     => $active,
                                        'icon_class' => $icon_class,
                                    );
            
            if($this->modules_model->insert_modules($data))
            {
            
                $last_insert_id = $this->db->insert_id();
                $name           = $this->input->post('name');
                               
                foreach($languages as $lang_id)
                {
                    $modules_translation_data = array(
                                                        'module_id'     => $last_insert_id ,
                                                        'name'          => $name[$lang_id],
                                                        'lang_id'       => $lang_id ,
                                                     );
                    $this->modules_model->insert_modules_translation($modules_translation_data);
                }
                
                $this->session->set_flashdata('success',lang('success'));
               
                redirect('root/module/index','refresh');
           }
        }//
     }
     
     public function edit_form($id)
     {
        $id = intval($id);
        
        if($id)
        {
            $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/update";
            $this->data['id']           = $id;
            
            $general_data               = $this->modules_model->get_modules_result($id);
            $data                       = $this->modules_model->get_modules_translation_result($id);
            
            $filtered_data              = array();
            
            foreach($data as $row)
            {
                $filtered_data[$row->lang_id] = $row;
            }
            
            $this->data['general_data'] = $general_data ;
            $this->data['data']         = $filtered_data;
            
            $this->data['content']      = $this->load->view('modules', $this->data, true);
            
            $this->load->view('Admin/main_frame',$this->data);
        }
     }
     
     public function update()
     {
        $languages      =  $this->input->post('lang_id');
        
        foreach($languages as $lang_id)
        { 
            $this->form_validation->set_rules('name['.$lang_id.']', lang('name') , 'required');
           
        }
        $this->form_validation->set_rules('module', lang('module') , 'required');
        $this->form_validation->set_rules('active', lang('active') , 'required');
        $this->form_validation->set_rules('icon_class', lang('icon_class') , 'required');
        
        $this->form_validation->set_message('required', lang('required'));
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        
        
        if ($this->form_validation->run() == FALSE)
		{
		    $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/update";
            $this->data['content']      = $this->load->view('modules', $this->data, true);
            
            $this->load->view('Admin/main_frame',$this->data);
    		
        }
        else
        {
            $module         =  $this->input->post('module');
            $active         =  $this->input->post('active');
            $icon_class     =  $this->input->post('icon_class');
            $module_id      =  $this->input->post('module_id');
            $name           =  $this->input->post('name');
            
            
            $modules_data   =  array(
                                        'module'     => $module,
                                        'active'     => $active,
                                        'icon_class' => $icon_class
                                    );
                                    
            $this->modules_model->update_modules($module_id,$modules_data);
                                            
            foreach($languages as $lang_id)
            {
                $modules_translation_data = array(
                                                    'name'         => $name[$lang_id],
                                                  );
                $this->modules_model->update_modules_translation($module_id,$lang_id,$modules_translation_data);
            }
            
            redirect('root/module/index','refresh');
            }
        
        
        
     }
    /******************************************************/
    public function _callback_add_field_active()
    {
        return '<div style="display:block;overflow:hidden;width:100%;height:auto;"> <input style="float: right;width: 15px;margin-left: 10px;" type="radio" name="active" value="1" checked="checked" /> <span style="float: right;">Active</span></div>
            <div style="display:block;overflow:hidden;width:100%;height:auto;"><input style="float: right;width: 15px;margin-left: 10px;" type="radio" name="active" value="0" /><span style="float: right;">Not Active</span></div>';
    }
    
    public function _callback_edit_field_active($value)
    {
        if($value == 1)
        {
            $check='checked="checked"';
            $asc='';
        }elseif($value == 0){
            $asc='checked="checked"';
            $check='';
        }
        return ' <div style="display:block;overflow:hidden;width:100%;height:auto;"><input style="float: right;width: 15px;margin-left: 10px;" type="radio" name="active"'. $check .'value="1" /> <span style="float: right;">Active</span></div>
            <div style="display:block;overflow:hidden;width:100%;height:auto;"><input style="float: right;width: 15px;margin-left: 10px;" type="radio" name="active"'. $asc .'  value="0" /><span style="float: right;">Not Active</span></div>';
    }
  
    public function _callback_add_field_module()
    {
        $this->load->library('modulelist');
        $modules=$this->modulelist->listModules();
        $select='<select name="module" class="chosen-select">';
        //$option='';
        foreach($modules as $module)
        {
           $select .='<option>'.$module.'</option>'; 
        }
        $select .='</select>';
        return $select;
    }
    
    public function _callback_edit_field_module($value)
    {
        
        $this->load->library('modulelist');
        $modules=$this->modulelist->listModules();
        $select='<select name="module" class="chosen-select">';
        //$option='';
        foreach($modules as $module)
        {
           // echo $value." --- ".$module;
            if($value == $module)
            {
                $check='selected="selected"';
            }else{
                $check='';
            }
           $select .='<option '.$check.' >'.$module.'</option>'; 
        }
        $select .='</select>';
        return $select;
    }
    
    
}
/* End of file admin_modules.php */
/* Location: ./application/modules/Modules/controllers/admin_modules.php */