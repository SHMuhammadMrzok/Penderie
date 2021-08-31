<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Admin_coupon_codes extends CI_Controller
{
    public $lang_row;
    
    public function __construct()
    {
        parent::__construct();
        
        require(APPPATH . 'includes/global_vars.php');
        
        $this->load->model('coupon_codes_model');
        $this->load->model('categories/cat_model');
        $this->load->model('products/products_model');
        $this->load->model('users/countries_model');
        
        $this->lang_row = $this->admin_bootstrap->get_active_language_row(); 
    }

    private function _js_and_css_files()
    {
        $this->data['css_files'] = array(
        'global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css',
        'global/plugins/fullcalendar/fullcalendar.min.css',
        'global/plugins/clockface/css/clockface.css',
        'global/plugins/bootstrap-datepicker/css/datepicker3.css',
        'global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css',
        'global/plugins/bootstrap-colorpicker/css/colorpicker.css',
        'global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css'
        );
        
        $this->data['js_files']  = array(
        
           //Date Range Picker
            'global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js',
            'global/plugins/bootstrap-daterangepicker/daterangepicker.js',
            'global/plugins/bootstrap-daterangepicker/moment.min.js',
            'pages/scripts/components-pickers.js',
            'pages/scripts/components-form-tools.js',
            
            //TouchSpin
            'global/plugins/fuelux/js/spinner.min.js',
            'global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js',
            'global/plugins/bootstrap-touchspin/bootstrap.touchspin.js',
            
            );
        
        
        $this->data['js_code'] = 'ComponentsPickers.init(); ';
            
    }

    public function index()
    {   
        $lang_id = $this->data['active_language']->id;
        
        $this->data['count_all_records'] = $this->coupon_codes_model->get_count_all_coupon_codes($lang_id);
        $this->data['data_language']     = $this->lang_model->get_active_data_languages();
        
        $this->data['columns']           = array(
                                                     lang('coupon_name'),
                                                     lang('code'),
                                                     lang('coupon_uses')
                                                   );
            
        $this->data['actions']           = array( 'delete'=>lang('delete'));
        $this->data['search_fields']     = array( lang('coupon_name'), lang('code'));
        
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
        $grid_data  = $this->coupon_codes_model->get_coupon_codes_data($lang_id, $limit, $offset, $search_word);
        
        $db_columns = array(
                             'id',
                             'name',
                             'code',
                             'coupon_uses'
                           );
                       
        $this->data['hidden_fields'] = array('id');
        
        $new_grid_data = array();
        
        foreach($grid_data as $key =>$row)
        { 
            foreach($db_columns as $column)
            {
                if($column == 'coupon_uses')
                {
                    $coupon_uses_count = $this->coupon_codes_model->count_coupon_uses($row->id);
                    
                    if($coupon_uses_count > 0)
                    {
                        $uses_link = '<a href="'.base_url().'coupon_codes/admin_coupon_codes/coupon_uses/'.$row->id.'">'.lang('coupon_uses').'</a>';
                    }
                    else
                    {
                        $uses_link = lang('coupon_not_used_before');
                    }
                    
                    $new_grid_data[$key][$column] = $uses_link;
                }
                else
                {
                    $new_grid_data[$key][$column] = $row->{$column};
                }
                
            }
        }
        
        $this->data['grid_data']          = $new_grid_data;  
        
        $this->data['count_all_records']  = $this->coupon_codes_model->get_count_all_coupon_codes($lang_id,$search_word);
        
        $this->data['display_lang_id']    = $lang_id;
    
        $count_data  = $this->data['count_all_records'];
        $output_data = $this->load->view('Admin/grid/grid_data',$this->data, true);
        
        echo json_encode(array($output_data, $count_data, $search_word));
     }
     
     public function coupon_uses($coupon_id)
     {
        $coupon_id  = intval($coupon_id);
        $lang_id    = $this->data['active_language']->id;
        $uses_array = array();
        $uses_data  = $this->coupon_codes_model->get_coupon_uses_data($coupon_id, $lang_id);
        $is_total_coupon = false;
        
        if($uses_data)
        {
            foreach($uses_data as $row)
            {
                if($row->user_id != 0)
                {
                    $user_data = $this->admin_bootstrap->get_user_by_id($row->user_id);
                    $username  = $user_data->first_name.' '.$user_data->last_name;
                }
                else
                {
                    $username = lang('visitor');
                }
                
                $row->{'username'}   = $username;
                $row->{'uses_count'} = $this->coupon_codes_model->count_coupon_uses($coupon_id);
                
                if($row->cat_applied == 1)
                {
                    $coupon_type = lang('product_coupon');
                }
                elseif($row->product_applied == 1)
                {
                    $coupon_type = lang('cat_coupon');
                }
                else
                {
                    $coupon_type = lang('total_coupon');
                }
                
                $row->{'coupon_type'}   = $coupon_type;
                
                // if coupon type is not total
                if($row->product_applied == 1 || $row->cat_applied == 1)
                {
                    $product_data = $this->products_model->get_product_eith_translation_data($row->product_id, $lang_id);
                    $row->{'product_name'} = $product_data->product_name;
                    $row->{'cat_name'}     = $product_data->cat_name;
                }
                else
                {
                    $is_total_coupon = true;
                }
                
                $uses_array[] = $row;
            }
            
            $this->data['uses_data'] = $uses_data;
            $this->data['is_total']  = $is_total_coupon;
            
            $this->data['content']   = $this->load->view('coupon_uses', $this->data, true);
            $this->load->view('Admin/main_frame',$this->data);
        }
     }
     
     public function read($id,$display_lang_id)
    {
        $id              = intval($id);
        $display_lang_id = intval($display_lang_id);
        
        if($id && $display_lang_id)
        {
            $data = $this->coupon_codes_model->get_coupons_result($id, $display_lang_id); 
           
            if($data)
            {
                $coupon_product = false;
                $coupon_cat     = false;
                
                if($data->active == 1)
                {
                    $active_value = lang('active');
                }
                else
                {
                    $active_value = lang('not_active');
                }
                
                if($data->discount_type == 'percentage')
                {
                    $discount_var   = lang('discount_percentage');
                    $discount_value = $data->discount . ' %' ;
                }
                elseif($data->discount_type = 'amount')
                {
                    $discount_var   = lang('discount_amount');
                    $discount_value = $data->discount;
                }
                
                if($data->product_or_category == 'product')
                {
                    $products_names  = '';
                    $coupon_type_var = ('product');
                    
                    $coupon_products = $this->coupon_codes_model->get_coupon_detailed_product($data->id, $display_lang_id);
                    
                    foreach($coupon_products as $product_name)
                    {
                        $products_names .= $product_name->product.' , ';
                    }
                    
                    $coupon_type_value = $products_names;
                    
                }
                elseif($data->product_or_category == 'category')
                {
                    $cat_names        = '';
                    $coupon_type_var = lang('category');
                    
                    $coupon_cats = $this->coupon_codes_model->get_coupon_detailed_cats($data->id, $display_lang_id);
                    
                    foreach($coupon_cats as $cat_name)
                    {
                        $cat_names .= $cat_name->category.' , ';
                    }
                    
                    $coupon_type_value = $cat_names;
                }
                else
                {
                    $coupon_type_var   = lang('discount_type');
                    $coupon_type_value = lang('total');
                }
                
                
                $row_data    = array(
                                        lang('code')                => $data->code                               ,
                                        $discount_var               => $discount_value                           , 
                                        lang('min_amount')          => $data->min_amount                         ,
                                        $coupon_type_var            => $coupon_type_value                        ,
                                        lang('country')             => $data->country                            ,
                                        lang('start_time')          => date('Y-m-d h:i', $data->start_unix_time) ,
                                        lang('end_time')            => date('Y-m-d h:i', $data->end_unix_time)   ,
                                        lang('uses_per_customer')   => $data->uses_per_customer                  ,
                                        lang('uses_per_coupon')     => $data->uses_per_coupon	                 ,
                                        lang('active')              => '<span class="badge badge-info">'.$active_value.'</span>'
                                        
                                    );
                             
            
        
                    $this->data['row_data'] = $row_data;
                    
                    $this->data['content']  = $this->load->view('Admin/grid/read_view', $this->data, true);
                    $this->load->view('Admin/main_frame',$this->data);
            }else{
                    $this->data['row_data'] = $row_data;
                    
                    $this->data['content']  = $this->load->view('Admin/grid/read_view', $this->data, true);
                    $this->load->view('Admin/main_frame',$this->data);
            }
            
            
        }
    }
    
    public function add()
    {
        $validation_msg = false;
        
        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
        {
            $languages = $this->input->post('lang_id');
        
            foreach($languages as $lang_id)
            {  
                $this->form_validation->set_rules('name['.$lang_id.']' ,lang('coupon_name'), 'required');
            }
            $this->form_validation->set_rules('code', lang('code'), 'required|is_unique[coupon_codes.code]');
            $this->form_validation->set_rules('min_amount', lang('min_amount'), 'required');
            $this->form_validation->set_rules('country', lang('country'), 'required');
            $this->form_validation->set_rules('uses_per_customer', lang('uses_per_customer'), 'required');
            $this->form_validation->set_rules('uses_per_coupon', lang('uses_per_coupon'), 'required');
            $this->form_validation->set_rules('start_unix_time', lang('discount_start_unix_time'), 'required');
            $this->form_validation->set_rules('end_unix_time', lang('discount_end_unix_time'), 'required');
            $this->form_validation->set_rules('discount', lang('discount'), 'required');
            $this->form_validation->set_rules('product_or_cat', lang('product_or_cat'), 'required');
            
            $this->form_validation->set_message('is_unique', lang('is_unique')."  : %s ");
            $this->form_validation->set_message('required', lang('required')." : %s ");
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            
            $validation_msg = true;
        }
        
        if ($this->form_validation->run() == FALSE)
		{
		  $this->_add_form($validation_msg);
        }
        else
        {
            $discount_type   = '';
             $discount_value = '';
             
             $coupon_cat     = false;
             $coupon_product = false;
             
             if(isset($_POST['discount'] ))
             {
                if($_POST['discount'] == 1)
                {
                    $discount_type    = 'percentage';
                    $discount_value   = $this->input->post('discount_percentage');
                }
                elseif($_POST['discount'] == 0)
                {
                    $discount_type    = 'amount';
                    $discount_value   = $this->input->post('discount_amount');
                }
                
             }
             
             if(isset($_POST['product_or_cat'] ))
             {
                if($_POST['product_or_cat'] == 1)
                {
                    $product_ids     = $this->input->post('product_id');
                    
                    $cat_id         = 0;
                    $product_or_cat = 'product';
                    $coupon_product = true; 
                }
                elseif($_POST['product_or_cat'] == 0)
                {
                    $product_id     = 0;
                    $cat_id         = $this->input->post('cat_id');
                    $product_or_cat = 'category'; 
                    $coupon_cat     = true;
                }
                elseif($_POST['product_or_cat'] == 2)
                {
                    $product_id     = 0;
                    $cat_id         = 0;
                    $product_or_cat = 'total'; 
                    
                }
            }
            
            $coupon_code_data     = array(
                                            'code'                  => $this->input->post('code'),
                                            'discount'              => $discount_value,
                                            'discount_type'         => $discount_type,
                                            'min_amount'            => $this->input->post('min_amount'),
                                            'product_or_category'   => $product_or_cat,
                                            'start_unix_time'       => strtotime($this->input->post('start_unix_time')),
                                            'end_unix_time'         => strtotime( $this->input->post('end_unix_time')),
                                            'country_id'            => $this->input->post('country'),
                                            'uses_per_customer'     => $this->input->post('uses_per_customer'),
                                            'uses_per_coupon'       => $this->input->post('uses_per_coupon'),
                                            'active'                => (isset( $_POST['active']))? $_POST['active']:0,
                                        );
          
           
            if($this->coupon_codes_model->insert_coupon_codes($coupon_code_data))
            {
            
                $last_insert_id = $this->db->insert_id();
                $title          = $this->input->post('name');
                              
                foreach($languages as $lang_id)
                {
                    $coupon_codes_translation_data = array(
                                                        'coupon_id'    => $last_insert_id ,
                                                        'name'         => $title[$lang_id],
                                                        'lang_id'      => $lang_id ,
                                                     );
                    
                    $this->coupon_codes_model->insert_coupon_codes_translation($coupon_codes_translation_data);
                }
                
                if($coupon_cat)
                {
                    foreach($cat_id as $cat)
                    {
                        $coupon_cats_data = array(
                                                    'coupon_id'  => $last_insert_id, 
                                                    'cat_id'     => $cat,
                                                    'product_id' => 0,
                                                    'unix_time'  => time() 
                                                 );
                        
                        $this->coupon_codes_model->insert_coupon_data($coupon_cats_data);
                    }
                }
                
                if($coupon_product)
                {
                    $this->coupon_codes_model->delete_coupon_cats_and_products($last_insert_id);
                    
                    foreach($product_ids as $product_id)
                    {
                        $coupon_cats_data = array(
                                                    'coupon_id'  => $last_insert_id, 
                                                    'cat_id'     => 0,
                                                    'product_id' => $product_id,
                                                    'unix_time'  => time() 
                                                 );
                        
                        $this->coupon_codes_model->insert_coupon_data($coupon_cats_data);
                    }
                }
                
                $this->session->set_flashdata('success',lang('success'));
                redirect('coupon_codes/admin_coupon_codes/','refresh');
           }
        }
    }
    
    private function _add_form($validation_msg)
    {
        $this->_js_and_css_files();
        
        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/add";
       
        $categories                 = $this->cat_model->get_categories($this->lang_row->id);
        $products                   = $this->products_model->get_all_products($this->lang_row->id);
        $countries                  = $this->countries_model->get_countries($this->lang_row->id);
        
        $countries_array[null]      = lang('choose');
        $products_options[null]     = lang('choose');
        
        
        foreach($categories as $cat)
        {
            if($cat->parent_id == 0)
            {
                foreach($categories as $category)
                {
                    if($category->parent_id == $cat->id)
                    {
                        $cats_array["{$cat->name}"][$category->id] = $category->name;
                    }
                }
            }
        }
        
        foreach($products as $product)
        {
            $products_options["{$product->cat_name}"][$product->id] = $product->title;
        }
        
        foreach($countries as $country)
        {
            $countries_array[$country->id] = $country->name;
        }
        
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }
        
        $this->data['products_options']   = $products_options;
        $this->data['cats_array']         = $cats_array;
        $this->data['countries_array']    = $countries_array;
        $this->data['content']            = $this->load->view('coupon_codes', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
    }
    
    public function edit($id)
    {
        if(is_numeric($id))
        {
            $id             = intval($id);
            $validation_msg = false;
            $general_data   = $this->coupon_codes_model->get_coupon_codes_result($id);
            
            if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
            {
                $coupon_id = $this->input->post('coupon_code_id');
                $languages = $this->input->post('lang_id');
                $code      = $this->input->post('code');
                
                if($general_data->code != $code)
                {
                    $this->form_validation->set_rules('code', lang('code'), 'required|is_unique[coupon_codes.code]');
                }
                else
                {
                    $this->form_validation->set_rules('code', lang('code'), 'required');
                }
                
                $id = $coupon_id;
                
                foreach($languages as $lang_id)
                {
                    $this->form_validation->set_rules('name['.$lang_id.']' ,lang('coupon_name')  , 'required');
                }
                
                $this->form_validation->set_rules('min_amount', lang('min_amount'), 'required');
                $this->form_validation->set_rules('country', lang('country'), 'required');
                $this->form_validation->set_rules('uses_per_customer', lang('uses_per_customer'), 'required');
                $this->form_validation->set_rules('uses_per_coupon', lang('uses_per_coupon'), 'required');
                $this->form_validation->set_rules('start_unix_time', lang('start_unix_time'), 'required');
                $this->form_validation->set_rules('end_unix_time', lang('end_unix_time'), 'required');
                $this->form_validation->set_rules('product_or_cat', lang('product_or_cat'), 'required');
                
                $this->form_validation->set_message('is_unique', lang('is_unique')."  : %s ");
                $this->form_validation->set_message('required', lang('required')." : %s ");
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
                
                $validation_msg = true;
            }
            
            if($this->form_validation->run() == FALSE)
    		{
    		   $this->_edit_form($id, $validation_msg, $general_data);
            }
            else
            {
                $coupon_cat     = false;
                $coupon_product = false;
                
                if(isset($_POST['discount'] ))
                {
                    if($_POST['discount'] == 1)
                    {
                        $discount_type    = 'percentage';
                        $discount_value   = $this->input->post('discount_percentage');
                    }
                    elseif($_POST['discount'] == 0)
                    {
                        $discount_type    = 'amount';
                        $discount_value   = $this->input->post('discount_amount');
                    }
                }
                else
                {
                    $discount_type    = 'none';
                    $discount_value   = 0;
                }
                
                
                if(isset($_POST['product_or_cat'] ))
                {
                    if($_POST['product_or_cat'] == 1)
                    {
                        $product_ids    = $this->input->post('product_id');
                        $cat_id         = 0;
                        $product_or_cat = 'product';
                        $coupon_product = true;
                    }
                    elseif($_POST['product_or_cat'] == 0)
                    {
                        $product_id     = 0;
                        $cat_id         = $this->input->post('cat_id');
                        $product_or_cat = 'category'; 
                        $coupon_cat     = true;
                    }
                    elseif($_POST['product_or_cat'] == 2)
                    {
                        $product_id     = 0;
                        $cat_id         = 0;
                        $product_or_cat = 'total'; 
                    }
                }
                
                $coupon_code_data     = array(
                                                'code'                  => $this->input->post('code'),
                                                'discount'              => $discount_value,
                                                'discount_type'         => $discount_type,
                                                'min_amount'            => $this->input->post('min_amount'),
                                                'product_or_category'   => $product_or_cat,
                                                'start_unix_time'       => strtotime($this->input->post('start_unix_time')),
                                                'end_unix_time'         => strtotime( $this->input->post('end_unix_time')),
                                                'country_id'            => $this->input->post('country'),
                                                'uses_per_customer'     => $this->input->post('uses_per_customer'),
                                                'uses_per_coupon'       => $this->input->post('uses_per_coupon'),
                                                'active'                => (isset( $_POST['active']))? $_POST['active']:0,
                                            );
                
                $this->coupon_codes_model->update_coupon_codes($coupon_id,$coupon_code_data);
             
                $name          = $this->input->post('name');
                  
                foreach($languages as $lang_id)
                {
                    $coupon_codes_translation_data = array( 'name' => $name[$lang_id]);
                    $this->coupon_codes_model->update_coupon_codes_translation($coupon_id,$lang_id,$coupon_codes_translation_data);
                    
                    if($coupon_cat)
                    {
                        $this->coupon_codes_model->delete_coupon_cats_and_products($coupon_id);
                        
                        foreach($cat_id as $cat)
                        {
                            $coupon_cats_data = array(
                                                        'coupon_id'  => $coupon_id, 
                                                        'cat_id'     => $cat,
                                                        'product_id' => 0,
                                                        'unix_time'  => time() 
                                                     );
                            
                            $this->coupon_codes_model->insert_coupon_data($coupon_cats_data);
                        }
                    }
                    
                    if($coupon_product)
                    {
                        $this->coupon_codes_model->delete_coupon_cats_and_products($coupon_id);
                        
                        foreach($product_ids as $product_id)
                        {
                            $coupon_cats_data = array(
                                                        'coupon_id'  => $coupon_id, 
                                                        'cat_id'     => 0,
                                                        'product_id' => $product_id,
                                                        'unix_time'  => time() 
                                                     );
                            
                            $this->coupon_codes_model->insert_coupon_data($coupon_cats_data);
                        }
                    }
                }
                $this->session->set_flashdata('success',lang('updated_successfully'));   
                redirect('coupon_codes/admin_coupon_codes/','refresh');  
            }
         }
        
    }
    
    private function _edit_form($id, $validation_msg, $general_data)
    {
        
        $this->_js_and_css_files();
        
        $this->data['form_action'] = $this->data['module'] . "/" . $this->data['controller'] . "/edit/".$id ;
        $this->data['id']          = $id;
        $lang_id                   = $this->lang_row->id;
        
        //$general_data     = $this->coupon_codes_model->get_coupon_codes_result($id);
        $data             = $this->coupon_codes_model->get_coupon_codes_translation_result($id);
        $products         = $this->products_model->get_country_products($general_data->country_id, $this->lang_row->id);
        $countries        = $this->countries_model->get_countries($lang_id);
        $categories       = $this->cat_model->get_categories($lang_id);
        $coupon_cats_and_products = $this->coupon_codes_model->get_coupon_cats_and_products($id);

        $filtered_data      = array();
        $products_options   = array();
        $countries_array    = array();
        $coupon_cats        = array();
        $coupon_products    = array();
        
        foreach($data as $row)
        {
            $filtered_data[$row->lang_id] = $row;
        }
        
        $countries_array[null]  = lang('choose');
        
        foreach($categories as $cat)
        {
            if($cat->parent_id == 0)
            {
                foreach($categories as $category)
                {
                    if($category->parent_id == $cat->id)
                    {
                        $cats_array["{$cat->name}"][$category->id] = $category->name;
                    }
                 }
            }
        }
        
        foreach($products as $product)
        {
            $cat_name = $this->cat_model->get_cat_name($product->cat_id, $lang_id)->name;
            $products_options["$cat_name"][$product->id] = $product->title;
        }
        
        foreach($countries as $country)
        {
            $countries_array[$country->id] = $country->name;
        }
        
        foreach( $coupon_cats_and_products as $value)
        {
            if($general_data->product_or_category == 'category')
            {
                $coupon_cats[] = $value->cat_id;
            }
            elseif($general_data->product_or_category == 'product')
            {
                $coupon_products[] = $value->product_id;
            }
        }
        
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('validation_msg');
        }
        
        $this->data['general_data']       = $general_data;
        $this->data['data']               = $filtered_data;
        $this->data['products_options']   = $products_options;
        $this->data['cats_array']         = $cats_array;
        $this->data['countries_array']    = $countries_array;
        $this->data['coupon_cats']        = $coupon_cats;
        $this->data['coupon_products']    = $coupon_products;
        
        
        $this->data['content']            = $this->load->view('coupon_codes', $this->data, true);
        $this->load->view('Admin/main_frame', $this->data);
        
    }
     
    public function get_products()
    {
        $lang_id    = $this->lang_row->id;
        $country_id = $this->input->post('country_id');
        
        $categories = $this->cat_model->get_categories($lang_id);
        $options    = "<select name='product_id[]' class='form-control select2-product products' multiple='multiple'><option>".lang('choose')."</option>";
        
        foreach($categories as $cat_row)
        {
            $cat_products = $this->products_model->get_cat_products_per_country($cat_row->id, $country_id, $lang_id);
            
            if($cat_products)
            {
                $options .= "<optgroup label=$cat_row->name>";
                
                foreach($cat_products as $product)
                {
                    $options .= "<option value=$product->product_id>$product->title</option>";
                }
                
               $options .= "</optgroup>";
           }
        }
        
        /*foreach($products as $row)
        {
            $options .= "<option value=$row->product_id>$row->title</option>";
        }*/
        
        $options .= '</select>';
        
        echo $options;
        /*$products = $this->coupon_codes_model->get_category_products($cat_id,$this->lang_row->id);
        $result      = array(); 
        
        $options    = "<option>".lang('choose')."</option>";
       
        foreach($products as $row)
        {
             $options .= "<option value=$row->id>$row->title</option>";
        }
        
        echo $options;*/
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
        $coupon_codes_ids = $this->input->post('row_id');

        if(is_array($coupon_codes_ids))
        { 
            
            $ids_array = array();
            
            foreach($coupon_codes_ids as $country_id)
            {
                $ids_array[] = $country_id['value'];
            }
        }
        else
        { 
            $ids_array = array($coupon_codes_ids);
        }
            
        $this->coupon_codes_model->delete_coupon_data($ids_array);
           
        echo "1";
     }  
     
     
    
/************************************************************************/    
}