<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Admin_reward_points extends CI_Controller
{
    public   $crud;
    public   $lang_row;
    public function __construct()
    {
        parent::__construct();
        
        $this->crud = new grocery_CRUD();
        $params     = array($this->crud);
        
        require(APPPATH . 'includes/global_vars.php');
        
        $this->load->model('reward_points_model');
        
        $this->lang_row = $this->admin_bootstrap->get_active_language_row(); 
    }

    /**************** List functions **********************/

    public function index()
    {   
        $lang_id = $this->data['active_language']->id;
        
        $this->data['count_all_records'] = $this->reward_points_model->get_count_all_points();
        $this->data['data_language']     = $this->lang_model->get_active_data_languages();
        
       
        $this->data['columns']           = array(
                                                  lang('points'),
                                                  lang('price'),
                                                  lang('active'),
                                                );
        $this->data['orders']                = $this->data['columns'] ;
                
        $this->data['actions']           = array( 'delete'=>lang('delete'));
        
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
        
        $grid_data                  = $this->reward_points_model->get_reward_points_data($limit,$offset,$search_word,$order_by,$order_state);
        
        $db_columns                 = array(
                                             'id',   
                                             'points',
                                             'price',
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
        
        $this->data['grid_data']          = $new_grid_data; 
        
        $this->data['count_all_records']  = $this->reward_points_model->get_count_all_points($search_word);
        
        $this->data['display_lang_id']    = $lang_id; 
         
        
        $count_data  = $this->data['count_all_records'];
        $output_data = $this->load->view('Admin/grid/grid_data',$this->data, true);
        
        echo json_encode(array($output_data, $count_data, $search_word));
     }
     
     
     public function read($id,$display_lang_id)
     {
        $id              = intval($id);
        $display_lang_id = intval($display_lang_id);
        
        if($id && $display_lang_id)
        {
            $data     = $this->reward_points_model->get_row_data($id,$display_lang_id);
            $row_data = array(
                                lang('points')  => $data->points ,
                                lang('price')   => $data->price ,
                                lang('active')  => $data->active
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
        $points_ids = $this->input->post('row_id');

        if(is_array($points_ids))
        { 
            
            $ids_array = array();
            
            foreach($points_ids as $point_id)
            {
                $ids_array[] = $point_id['value'];
            }
        }else{ 
            
            $ids_array = array($points_ids);
        }
            
        $this->reward_points_model->delete_reward_points_data($ids_array);
        echo "1";
     }  
     
     /***********************ADD & Edit Functions ************************/
    
     private function _js_and_css_files()
    {
        $this->data['css_files'] = array();
        
        $this->data['js_files']  = array(
            //TouchSpin
            'global/plugins/fuelux/js/spinner.min.js',
            'global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js',
            'global/plugins/bootstrap-touchspin/bootstrap.touchspin.js',
            
            );
        
        
        $this->data['js_code'] = 'ComponentsPickers.init()';
    }
    
    public function add_form()
     {
        $this->_js_and_css_files();
        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/save";
        $this->data['content']      = $this->load->view('reward_points', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
        
     }
     
     public function save()
     {
        
        $this->form_validation->set_rules('points', lang('points'), 'required');
        $this->form_validation->set_rules('price' , lang('price') , 'required');
        
        $this->form_validation->set_message('required', lang('required'));
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        
        if ($this->form_validation->run() == FALSE)
		{
		    $this->_js_and_css_files();
            $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/save";
            
            $this->data['content']      = $this->load->view('reward_points', $this->data, true);
            $this->load->view('Admin/main_frame',$this->data);
		
        }else{
		   
            $points    = $this->input->post('points');
            $price     = $this->input->post('price');
            $active    = $this->input->post('active');
           
            $reward_points_data = array(
                                        'points'    => $points ,
                                        'price'     => $price ,
                                        'unix_time' => time(),
                                        'active'    => (isset( $_POST['active']))? $this->input->post('active'):0,
                                       );
            
            $this->reward_points_model->insert_reward_points_data($reward_points_data);
       
            $this->session->set_flashdata('success',lang('success'));
            redirect('reward_points/admin_reward_points/index','refresh');
		}
            
            
     }
     public function edit_form($id)
     {
        $id = intval($id);
        
        if($id)
        {
            $this->_js_and_css_files();
        
            $this->data['mode']                  = 'edit';
            $this->data['form_action']           = $this->data['module'] . "/" . $this->data['controller'] . "/update";
            $this->data['id']                    = $id;
            
            $this->data['general_data']          = $this->reward_points_model->get_row_data($id);
            
           
            $this->data['content']               = $this->load->view('reward_points', $this->data, true);
            $this->load->view('Admin/main_frame',$this->data);
        }
     }
     
     public function update()
     {
        $id               = $this->input->post('id');
        
        $this->form_validation->set_rules('points', lang('points'), 'required');
        $this->form_validation->set_rules('price' , lang('price') , 'required');
        
        $this->form_validation->set_message('required', lang('required'));
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        
        if ($this->form_validation->run() == FALSE)
		{
		     $this->_js_and_css_files();
        
            $this->data['mode']                  = 'edit';
            $this->data['form_action']           = $this->data['module'] . "/" . $this->data['controller'] . "/update";
            $this->data['id']                    = $id;
            
            $this->data['general_data']          = $this->reward_points_model->get_row_data($id);
            
           
            $this->data['content']               = $this->load->view('reward_points', $this->data, true);
            $this->load->view('Admin/main_frame',$this->data);
		
        }else{
		   
            $points    = $this->input->post('points');
            $price     = $this->input->post('price');
            
           
            $reward_points_data = array(
                                        'points'    => $points ,
                                        'price'     => $price ,
                                        'unix_time' => time(),
                                        'active'    => (isset( $_POST['active']))? $this->input->post('active'):0,
                                       );
            
            $this->reward_points_model->update_reward_points_data($id,$reward_points_data);
       
            $this->session->set_flashdata('success',lang('success'));
            redirect('reward_points/admin_reward_points/index','refresh');
		}
    
     }
     
     
/************************************************************************/    
}