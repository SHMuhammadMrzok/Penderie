<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Admin_stores extends CI_Controller
{
    public $lang_row;

    public function __construct()
    {
        parent::__construct();

        require(APPPATH . 'includes/global_vars.php');

        $this->load->model('stores_model');
        $this->load->model('categories/cat_model');

        $this->lang_row = $this->admin_bootstrap->get_active_language_row();
    }

    public function index()
    {
        $lang_id = $this->data['active_language']->id;

        $this->data['count_all_records'] = $this->stores_model->get_count_all_stores($lang_id);
        $this->data['data_language']     = $this->lang_model->get_active_data_languages();


        $this->data['columns']           = array(
                                                     lang('name_of_store')      ,
                                                     lang('thumbnail')          ,
                                                     lang('show_in_main_page')  ,
                                                     lang('show_in_menu')       ,
                                                     lang('active')             ,
                                                     lang('phone')              ,
                                                     lang('store_cats')
                                                );

        $this->data['orders']            = array(
                                                    lang('name_of_store'),
                                                    lang('show_in_main_page'),
                                                    lang('show_in_menu'),
                                                );

        $this->data['actions']           = array( 'delete'=>lang('delete'));
        $this->data['search_fields']     = array( lang('name_of_store'), lang('address'), lang('phone'));

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

        if(isset($_POST['filter'])&& isset($_POST['filter_data']))
        {
            $filters = $this->input->post('filter');
            $filters_data = $this->input->post('filter_data');

            $city_id = intval($filters_data[0]);
        }
        else
        {
            $city_id = 0;
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

        $grid_data  = $this->stores_model->get_stores_data($lang_id, $limit, $offset, $search_word, $order_by, $order_state);

        $db_columns = array(
                             'id'               ,
                             'name'             ,
                             'image'            ,
                             'show_in_main_page',
                             'show_in_menu'     ,
                             'active'           ,
                             'phone'            ,
                             'store_cats'
                           );

        $this->data['hidden_fields'] = array('id');

        $new_grid_data = array();

        foreach($grid_data as $key =>$row)
        {
            foreach($db_columns as $column)
            {
                if($column == 'image')
                {
                    if($row->image != '')
                    {
                        $image = "<a href='".$this->data['images_path'].$row->image."' class='image-thumbnail' >
                        <img src='".$this->data['images_path'].$row->image."' width='80' height='50'  /></a>";
                    }
                    else
                    {
                        $image = '';
                    }

                    $new_grid_data[$key][$column] = $image;
                }
                else if($column == 'show_in_main_page')
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
                else if($column == 'show_in_menu')
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
                else if($column == 'active')
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
                else if($column == 'store_cats')
                {
                    $new_grid_data[$key][$column] = '<a class="btn btn-sm green filter-submit margin-bottom" href="'.base_url().'stores/admin_stores/store_cats/'.$row->id.'">'.lang('settings').'</a>';
                }
                else
                {
                    $new_grid_data[$key][$column] = $row->{$column};
                }
            }
        }

        $this->data['grid_data']          = $new_grid_data;
        $this->data['count_all_records']  = $this->stores_model->get_count_all_stores($lang_id, $search_word);
        $this->data['display_lang_id']    = $lang_id;


        $count_data  = $this->data['count_all_records'];
        $output_data = $this->load->view('Admin/grid/grid_data',$this->data, true);

        echo json_encode(array($output_data, $count_data, $search_word));
     }

     public function add()
     {
        $validation_msg = false;

        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
        {
            $languages = $this->input->post('lang_id');

            foreach($languages as $lang_id)
            {
                $this->form_validation->set_rules('name['.$lang_id.']', lang('name'), 'required');
                $this->form_validation->set_rules('description['.$lang_id.']', lang('description'), 'required');
            }

            //$this->form_validation->set_rules('image', lang('thumbnail'), 'required');

            $this->form_validation->set_message('required', lang('required')."  : %s ");
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

            $validation_msg = true;
        }

        if ($this->form_validation->run() == FALSE)
    		{
    		  $this->_add_form($validation_msg);
        }
        else
        {
            $facebook   = $this->input->post('facebook');
            $twitter    = $this->input->post('twitter');
            $instagram  = $this->input->post('instagram');
            $youtube    = $this->input->post('youtube');
            $image      = $this->input->post('image');
            $phone      = $this->input->post('phone');
            $commission_type = $this->input->post('commission_type');
            $commission      = $this->input->post('commission');

            $data    = array(
                                'facebook'  => $facebook        ,
                                'instagram' => $instagram       ,
                                'twitter'   => $twitter         ,
                                'youtube'   => $youtube         ,
                                'image'     => $image           ,
                                'phone'     => $phone           ,
                                'commission'=> $commission      ,
                                'commission_type'       => $commission_type ,
                                'active'                => isset( $_POST['active']) ? $this->input->post('active'):0,
                                'show_in_main_page'     => isset( $_POST['show_in_main_page']) ? $this->input->post('show_in_main_page'):0,
                                'show_in_menu'          => isset( $_POST['show_in_menu']) ? $this->input->post('show_in_menu'):0,
                            );

            if($this->stores_model->insert_store($data))
            {

                $last_insert_id = $this->db->insert_id();
                $name           = $this->input->post('name');
                $address        = $this->input->post('address');
                $description    = $this->input->post('description');

                foreach($languages as $lang_id)
                {
                    $stores_translation_data = array(
                                                        'store_id'      => $last_insert_id          ,
                                                        'name'          => $name[$lang_id]          ,
                                                        'address'       => $address[$lang_id]       ,
                                                        'description'   => $description[$lang_id]   ,
                                                        'lang_id'       => $lang_id
                                                     );

                    $this->stores_model->insert_stores_translation($stores_translation_data);
                }

                $_SESSION['success'] = lang('success');
                $this->session->mark_as_flash('success');

                redirect('stores/admin_stores/','refresh');
            }
        }
     }

     private function _add_form($validation_msg)
     {
        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/add";
        $this->data['mode']         = 'edit';

        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }

        $cats = $this->cat_model->get_categories($this->data['lang_id']);
        $categories_array = array();

        foreach($cats as $cat)
        {
            $categories_array[$cat->parent_id][] = $cat;
        }

        $this->data['cats']     = $categories_array;
        $this->data['content']  = $this->load->view('stores_form', $this->data, true);
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
                $languages = $this->input->post('lang_id');

                foreach($languages as $lang_id)
                {
                    $this->form_validation->set_rules('name['.$lang_id.']', lang('name'), 'required');
                    $this->form_validation->set_rules('description['.$lang_id.']', lang('description'), 'required');
                }

                //$this->form_validation->set_rules('image', lang('thumbnail'), 'required');

                $this->form_validation->set_message('required', lang('required')."  : %s ");
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

                $validation_msg = true;
            }

            if($this->form_validation->run() == FALSE)
    		{
    		   $this->_edit_form($id, $validation_msg);
            }
            else
            {
                $facebook   = $this->input->post('facebook');
                $twitter    = $this->input->post('twitter');
                $instagram  = $this->input->post('instagram');
                $youtube    = $this->input->post('youtube');
                $image      = $this->input->post('image');
                $phone      = $this->input->post('phone');
                $commission_type = $this->input->post('commission_type');
                $commission      = $this->input->post('commission');


                $data    = array(
                                    'facebook'  => $facebook    ,
                                    'instagram' => $instagram   ,
                                    'twitter'   => $twitter     ,
                                    'youtube'   => $youtube      ,
                                    'image'     => $image       ,
                                    'phone'     => $phone        ,
                                    'commission'=> $commission      ,
                                    'commission_type'       => $commission_type ,
                                    'active'            => isset( $_POST['active']) ? $this->input->post('active'):0,
                                    'show_in_main_page' => isset( $_POST['show_in_main_page']) ? $this->input->post('show_in_main_page'):0,
                                    'show_in_menu'      => isset( $_POST['show_in_menu']) ? $this->input->post('show_in_menu'):0,
                                );

                $this->stores_model->update_store($id, $data);

                $name           = $this->input->post('name');
                $address        = $this->input->post('address');
                $description    = $this->input->post('description');

                foreach($languages as $lang_id)
                {
                    $stores_translation_data = array(
                                                        'store_id'      => $id                      ,
                                                        'name'          => $name[$lang_id]          ,
                                                        'address'       => $address[$lang_id]       ,
                                                        'description'   => $description[$lang_id]   ,
                                                        'lang_id'       => $lang_id
                                                     );

                    $this->stores_model->update_stores_translation($id, $lang_id, $stores_translation_data);
                }

                $_SESSION['success'] = lang('updated_successfully');
                $this->session->mark_as_flash('success');

                redirect('stores/Admin_stores/','refresh');
            }
        }
     }

     private function _edit_form($id, $validation_msg)
     {
        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/edit/".$id;
        $this->data['mode']         = 'edit';
        $this->data['id']           = $id;
        $general_data               = $this->stores_model->get_store_row($id);
        $data                       = $this->stores_model->get_store_translation_result($id);

        $filtered_data              = array();

        foreach($data as $row)
        {
            $filtered_data[$row->lang_id] = $row;
        }

        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }

        $this->data['data']             = $filtered_data;
        $this->data['general_data']     = $general_data;

        $this->data['content']          = $this->load->view('stores_form', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
     }

     public function read($id, $display_lang_id)
     {
        $id              = intval($id);
        $display_lang_id = intval($display_lang_id);

        if($id && $display_lang_id)
        {
            $data     = $this->stores_model->get_row_data($id, $display_lang_id);
            $row_data = array(
                                lang('name_of_store')   => $data->name ,
                                lang('address')         => $data->address ,
                                lang('description')     => $data->description,
                                lang('phone')           => $data->phone,
                                lang('facebook')        => $data->facebook,
                                lang('twitter')         => $data->twitter,
                                lang('instagram')       => $data->instagram,
                                lang('youtube')         => $data->youtube,
                                lang('thumbnail')       => "<a href='".base_url()."assets/uploads/".$data->image."' class='image-thumbnail' ><img src='".base_url()."assets/uploads/".$data->image."' width='80' height='50'  /></a>"
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
        $stores_ids = $this->input->post('row_id');

        if(is_array($stores_ids))
        {
            $ids_array = array();

            foreach($stores_ids as $id)
            {
                $ids_array[] = $id['value'];
            }
        }
        else
        {
            $ids_array = array($stores_ids);
        }

        // check store products
        $products_exist = $this->stores_model->check_stores_products($ids_array);

        if($products_exist)
        {
          echo lang('cant_delete_option_has_produts');
        }
        else {
          $this->stores_model->delete_store_data($ids_array);
        }


     }

     public function store_cats($store_id)
     {
        $store_id           = intval($store_id);
        $categories_array   = array();
        $store_old_cats     = array();

        $store_data = $this->stores_model->get_row_data($store_id, $this->data['lang_id']);
        $cats       = $this->cat_model->get_categories($this->data['lang_id']);
        $store_cats = $this->stores_model->get_store_cats($store_id);

        foreach($store_cats as $store_cat)
        {
            $store_old_cats[] = $store_cat->category_id;
        }

        foreach($cats as $cat)
        {
            $categories_array[$cat->parent_id][] = $cat;
        }

        if(count($categories_array) == 0)
        {
            $this->data['error_msg'] = lang('no_available_cats');
        }
        else
        {
            $this->data['store_old_cats']   = $store_old_cats;
            $this->data['store_data']       = $store_data;
            $this->data['store_id']         = $store_id;
            $this->data['cats']             = $categories_array;

        }

        $this->data['content']  = $this->load->view('store_cats', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
     }

     public function save_store_cats()
     {
        $store_id        = $this->input->post('store_id');
        $checked_nodes   = $this->input->post('checked_nodes');

        $this->stores_model->delete_store_cats($store_id);

        if(isset($_POST['checked_nodes']) && count($_POST['checked_nodes']) > 0)
        {
            foreach($checked_nodes as $node)
            {
                $cat_data = explode('_', $node);

                $cat_id         = $cat_data[1];
                $cats_array[]   = $cat_id;

                if(isset($cat_data[2]))
                {
                    $parent_id      = $cat_data[2];
                    $cats_array[]   = $parent_id;
                }
                /*$cat_store_data = array(
                                            'store_id'      => $store_id,
                                            'category_id'   => $cat_id
                                        );

                $this->stores_model->save_store_cats($cat_store_data);
                */

            }

            $cats_array = array_unique(array_filter($cats_array));

            foreach($cats_array as $cat_id)
            {
                $cat_store_data = array(
                                            'store_id'      => $store_id,
                                            'category_id'   => $cat_id
                                        );

                $this->stores_model->save_store_cats($cat_store_data);
            }
        }

        echo "	<div class='alert alert-success alert-dismissable'>
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								<strong>".lang('success')."</strong>".lang('')."</div>";
     }

     public function get_store_cats_options()
     {
        $store_id   = intval($this->input->post('store_id', true));
        $lang_id    = $this->data['active_language']->id;

        $store_cats = $this->stores_model->get_store_available_cats_data($store_id, $lang_id);

        $cats_array = array();
        $options    = '<select name="cat_id" class="form-control select2" id="cat_id">';
        $options   .= '<option value="0">----------------</option>';

        foreach($store_cats as $cat)
        {
            if($cat->parent_id != 0)
            {
                $options .= '<option value="'.$cat->id.'">'.$cat->name.'</option>';
            }
            /*if($cat->parent_id == 0)
            {

                foreach($store_cats as $category)
                {
                    if($category->parent_id == $cat->id)
                    {
                        //$cats_array["{$cat->name}"][$category->id] = $category->name;
                        $options .= '<option value="'.$cat->id.'">'.$category->name.'</option>';
                    }
                }

                //$options .= '</optgroup>';
            }*/
        }

        $options .= '</select>';
        echo $options;
     }



/************************************************************************/
}
