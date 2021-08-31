<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Admin extends CI_Controller
{
    public $crud;

    public function __construct()
    {
        parent::__construct();

        require(APPPATH . 'includes/global_vars.php');

        $this->load->library('uploaded_images');

        $this->load->model('cat_model');
        $this->load->model('categories_tags_model');

    }

     private function _js_and_css_files()
     {
        $this->data['css_files'] = array(
            'global/plugins/jquery-tags-input/jquery.tagsinput.css',
            );

        $this->data['js_files']  = array(
            //Date Range Picker
            'global/plugins/bootstrap-daterangepicker/moment.min.js',
            //Tags
            'tags/tag-it.js',
            );

        $this->data['js_code'] = "";
    }


    public function index()
    {
        $lang_id = $this->data['active_language']->id;

        $this->data['count_all_records']    = $this->cat_model->get_count_all_categories($lang_id);
        $this->data['data_language']        = $this->lang_model->get_active_data_languages();

        $this->data['columns']              = array(
                                                     lang('title'),
                                                     lang('parent'),
                                                     lang('products_num'),
                                                     lang('thumbnail'),
                                                     lang('active')
                                                   );

        $this->data['orders']                = array(
                                                     lang('title'),
                                                     //lang('products_num'),
                                                     lang('active'),
                                                     lang('sort')
                                                   );

        $this->data['filters']              = array(
                                                      array(
                                                             'filter_title'         => lang('parent_cat_filter'),
                                                             'filter_name'          => 'parent_cat_filter',
                                                             'filter_data'          => $this->cat_model->get_parent_cats($lang_id),
                                                           )
                                                   );

        $this->data['actions']              = array( 'delete'=>lang('delete'));

        $this->data['search_fields']        = array(lang('cat_name'), lang('description'), lang('meta_tag_description'));

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

        if(isset($_POST['filter'])&& isset($_POST['filter_data']))
        {
            $filters      = $this->input->post('filter');
            $filters_data = $this->input->post('filter_data');

            $parent_cat_filter = intval($filters_data[0]);
        }
        else
        {
            $parent_cat_filter = 0;
        }


        $grid_data       = $this->cat_model->get_cateories_data($lang_id, $limit, $offset, $search_word, $order_by, $order_state, $parent_cat_filter);

        $db_columns      = array(
                                 'id'           ,
                                 'name'         ,
                                 'parent_id'    ,
                                 'cat_products' ,
                                 'image'        ,
                                 'active'       ,
                                 'sort'
                           );

       $this->data['hidden_fields'] = array('id','sort');

       $new_grid_data = array();

        foreach($grid_data as $key =>$row)
        {
            $cat_products = 0;

            $cat_products = $this->cat_model->get_category_products($row->id);

            foreach($db_columns as $column)
            {
                if($column == 'image')
                {
                    if($row->image != '')
                    {
                        $image = "<a href='".$this->data['images_path'].$row->image."' class='image-thumbnail' ><img src='".$this->data['images_path'].$row->image."' width='80' height='50'  /></a>";
                    }
                    else
                    {
                        $image = '';
                    }
                    $new_grid_data[$key][$column] = $image;
                }
                elseif($column == 'active')
                {
                    if($row->{$column} == 0)
                    {
                        $new_grid_data[$key][$column] = '<span class="badge badge-danger">'.lang('not_active').'</span>';
                    }
                    elseif($row->{$column} == 1)
                    {
                        $new_grid_data[$key][$column] = '<span class="badge badge-success">'.lang('active').'</span>';
                    }

                }elseif($column == 'cat_products'){

                        $new_grid_data[$key][$column] = $cat_products;

                }elseif($column == 'parent_id')
                {
                    if($row->parent_id != 0)
                    {
                        $parent = $this->cat_model->get_cat_name($row->parent_id, $lang_id)->name;
                    }else{
                        $parent = '';
                    }

                    $new_grid_data[$key][$column] = $parent;
                }
                else{

                    $new_grid_data[$key][$column] = $row->{$column};
                }
            }
        }


        $this->data['grid_data']         = $new_grid_data;
        $this->data['count_all_records'] = $this->cat_model->get_count_all_categories($lang_id, $search_word, $parent_cat_filter);
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

        $this->cat_model->update_row_sort($id,$old_index,$new_index,$sort_state);

     }

     public function add()
     {
        $this->_js_and_css_files();
        $validation_msg = false;

        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
        {
            $languages = $this->input->post('lang_id');

            foreach($languages as $lang_id)
            {
                $this->form_validation->set_rules('cat_name['.$lang_id.']', lang('cat_name'), 'trim|required');
                $this->form_validation->set_rules('description['.$lang_id.']', lang('description') , 'trim|required');
            }

            $this->form_validation->set_rules('route', lang('route'), 'required|is_unique[categories.route]');
            $this->form_validation->set_rules('image', lang('thumbnail'), 'trim|required');

            $this->form_validation->set_message('required', lang('required')." : %s ");
            $this->form_validation->set_message('unique', lang('is_unique')." : %s ");

            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

            $validation_msg = true;
        }

        if ($this->form_validation->run() == FALSE)
    		{
    		  $this->_add_form($validation_msg);
        }
        else
        {
            $route      = $this->input->post('route');
            $image_name =  $this->input->post('image');
            $icon       =  $this->input->post('image2');

            $cat_general_data = array(
                                        'parent_id'     => intval($this->input->post('parent', true)) ,
                                        'image'         => $image_name ,
                                        'icon'          => $icon,
                                        'has_brands'    => (isset( $_POST['has_brands']))? $_POST['has_brands']:0,
                                        'needs_shipping' => (isset( $_POST['needs_shipping']))? $_POST['needs_shipping']:0,
                                        'active'        => (isset( $_POST['active']))? $this->input->post('active'):0,
                                        'show_home'     => (isset( $_POST['show_home']))? $this->input->post('show_home'):0,
                                        'sort'          => '1',
                                        'route'         => str_replace(' ', '', $route)
                                      );
            // create image thumb
            //$this->uploaded_images->resize_image($image_name, 0);

            if($this->cat_model->insert_cat_vars($cat_general_data))
            {
                $last_insert_id         = $this->db->insert_id();
                $lang_id	            = $this->input->post('lang_id', true);
                $cat_name               = $this->input->post('cat_name', true);
                $description            = $this->input->post('description', true);
                $meta_tag_description   = $this->input->post('meta_tag_description', true);
                $meta_title             = $this->input->post('meta_title', true);


                foreach($languages as $lang_id)
                {

                    $cat_translation_data = array(
                                                        'category_id'          => $last_insert_id                ,
                                                        'name'                 => $cat_name[$lang_id]            ,
                                                        'meta_tag_description' => $meta_tag_description[$lang_id],
                                                        'description'          => $description[$lang_id]         ,
                                                        'meta_title'           => $meta_title[$lang_id]          ,
                                                        'lang_id'              => $lang_id                       ,
                                                     );

                    $this->cat_model->insert_cat_translation($cat_translation_data);

                     /*******************Tags**************************/
                        $tags           = $this->input->post('tags');
                    if(isset($tags[$lang_id]) && count($tags[$lang_id]))
                    {
                        foreach($tags[$lang_id] as  $tag)
                        {
                            $tag_id = $this->categories_tags_model->get_tag_id($tag,$lang_id);

                            $categories_tags_data = array ('tag_id'=>$tag_id,'category_id'=>$last_insert_id);

                            $this->categories_tags_model->insert_tags_categories($categories_tags_data);
                        }
                    }
                    /************************************************/

                }

                $_SESSION['success'] = lang('success');
                $this->session->mark_as_flash('success');

                redirect('categories/admin/','refresh');
            }
        }
     }

     private function _add_form($validation_msg = false)
     {
	    $cats_value = $this->cat_model->get_parent_cats($this->data['active_language']->id);
        $options = array();
        $options[0]= lang('choose');

        foreach($cats_value as $row)
        {

            $options[$row->id] = $row->name;
        }

        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }

        $this->data['options'] = $options;
	    $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/add";
        $this->data['content']      = $this->load->view('categories', $this->data, true);

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
                $cat_id    = intval($this->input->post('cat_id'));
                $languages = $this->input->post('lang_id');

                foreach($languages as $lang_id)
                {
                    $this->form_validation->set_rules('cat_name['.$lang_id.']', lang('cat_name'), 'trim|required');
                    $this->form_validation->set_rules('description['.$lang_id.']', lang('description') , 'trim|required');
                }
                $this->form_validation->set_rules('image', lang('thumbnail') , 'trim|required');

                $general_data = $this->cat_model->get_category_row($id);
                if($this->input->post('route') != $general_data->route)
                {
                    $this->form_validation->set_rules('route', ('route'), 'required|is_unique[categories.route]');
                }

                $this->form_validation->set_message('required', lang('required')." : %s ");
                $this->form_validation->set_message('unique', lang('is_unique')." : %s ");

                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
                $validation_msg = true;
            }

            if($this->form_validation->run() == FALSE)
        		{
        		   $this->_edit_form($id, $validation_msg);
            }
            else
            {
                $parent               = $this->input->post('parent', true);
                $thumbnail            = $this->input->post('image', true);
                $active               = $this->input->post('active', true);
                $name                 = $this->input->post('cat_name', true);
                $description          = $this->input->post('description', true);
                $meta_tag_description = $this->input->post('meta_tag_description', true);
                $meta_title           = $this->input->post('meta_title', true);
                $route                = $this->input->post('route', true);
                $icon                 =  $this->input->post('image2');

                $category_data = array(
                                        'parent_id'     => $parent ,
                                        'image'         => $thumbnail ,
                                        'has_brands'    => (isset( $_POST['has_brands']))? $_POST['has_brands']:0,
                                        'needs_shipping' => (isset( $_POST['needs_shipping']))? $_POST['needs_shipping']:0,
                                        'show_home'     => (isset( $_POST['show_home']))? $this->input->post('show_home'):0,
                                        'active'        => $active,
                                        'icon'          => $icon  ,
                                        'route'         => str_replace(' ', '', $route)
                                      );
                                      
                $this->cat_model->update_category($cat_id, $category_data);

                $this->categories_tags_model->delete_tags_categories($cat_id);

                foreach($languages as $lang_id)
                {


                    $cat_translation_data = array(
                                                    'name'                 => $name[$lang_id]                 ,
                                                    'description'          => $description[$lang_id]          ,
                                                    'meta_tag_description' => $meta_tag_description[$lang_id] ,
                                                    'meta_title'           => $meta_title[$lang_id]
                                                   );

                    $this->cat_model->update_cat_translation($cat_id, $lang_id, $cat_translation_data);

                     /*******************Tags**************************/
                    $tags = $this->input->post('tags');

                    if(isset($tags[$lang_id]) && count($tags[$lang_id]))
                    {
                        foreach($tags[$lang_id] as  $tag)
                        {
                            $tag_id = $this->categories_tags_model->get_tag_id($tag,$lang_id);

                            $categories_tags_data = array ('tag_id'=>$tag_id,'category_id'=>$cat_id);

                            $this->categories_tags_model->insert_tags_categories($categories_tags_data);
                        }
                    }
                    /************************************************/
                }

                $_SESSION['success'] = lang('updated_successfully');
                $this->session->mark_as_flash('success');

                redirect('categories/admin/', 'refresh');
            }

        }
    }

    private function _edit_form($id, $validation_msg)
    {
        $this->_js_and_css_files();

        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/edit/".$id;
        $this->data['id']           = $id;
        $general_data               = $this->cat_model->get_category_row($id);
        $data                       = $this->cat_model->get_cat_result($id);
        $tags_data                  = $this->cat_model->get_cat_tags_result($id);

        $filtered_data              = array();

        foreach($data as $row)
        {
            $filtered_data[$row->lang_id] = $row;
        }

        $tags = array();

        foreach($tags_data as $row)
        {
            $tags[$row->lang_id][]= $row->tag;
        }

        $cats_value = $this->cat_model->get_parent_cats($this->data['active_language']->id);
        $options    = array();
        $options[0] = lang('choose');

        foreach($cats_value as $row)
        {
            $options[$row->id] = $row->name;
        }

        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }

        $this->data['general_data'] = $general_data;
        $this->data['data']         = $filtered_data;
        $this->data['tags']         = $tags;
        $this->data['options']      = $options;

        $this->data['content'] = $this->load->view('categories', $this->data, true);

        $this->load->view('Admin/main_frame',$this->data);
    }

    public function get_suggestions()
    {
        $term        = $this->input->post('term');
        $suggestions = $this->categories_tags_model->get_suggestions($term);
        $result      = array();

        foreach($suggestions as $row)
        {
            $result[]=array('label'=>$row->tag , 'value'=>$row->tag);
        }

        echo json_encode($result);
    }

    public function read($id, $display_lang_id=0)
    {
        $id              = intval($id);
        $display_lang_id = intval($display_lang_id);

        if($id && $display_lang_id)
        {
            $data     = $this->cat_model->get_row_data($id,$display_lang_id);

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
                                    lang('category')             => $data->name                 ,
                                    lang('description')          => $data->description          ,
                                    lang('meta_tag_description') => $data->meta_tag_description ,
                                    lang('meta_title')           => $data->meta_title           ,
                                    lang('description')          => $data->description          ,
                                    lang('thumbnail')            => "<a href='".$images_path.$data->image."' class='image-thumbnail' ><img src='".$images_path.$data->image."' width='150' height='50'  /></a>",//'<img src="'.base_url().'assets/uploads/'.$data->image.'"  width="120" height="70">' ,//class="image-thumbnail"
                                    lang('active')               => '<span class="badge badge-info">'.$active_value.'</span>'
                                 );


                if($data->parent_id != 0)
                {
                    $active_lang_id  = $this->data['active_language']->id;
                    $parent          = $this->cat_model->get_cat_name($data->parent_id,$active_lang_id);
                    $row_data[lang('parent_category')] = $parent->name;
                    $parent_name = $parent->name;
                }

                $category_tags = $this->categories_tags_model->get_cat_tags($id, $display_lang_id);

                if(count($category_tags) != 0)
                {
                    $tags = '';
                    foreach($category_tags as $tag)
                    {
                        $tags .= $tag->tag.', ';
                    }
                    $row_data[lang('meta_tag_keywords')] = $tags;
                }

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
        $categories_ids = $this->input->post('row_id');

        $ids_array      = array();
        $sub_cat_ids    = array();

        if(is_array($categories_ids))
        {
            foreach($categories_ids as $cat_id)
            {
                $ids_array[] = $cat_id['value'];
            }

            // check sub cats
            foreach($ids_array as $cat_id)
            {
                $sub_cats = $this->cat_model->get_sub_cats($cat_id);

                if(count($sub_cats) > 0)
                {
                    echo lang('error_sub_category_exist');
                    /*foreach($sub_cats as $sub_cat)
                    {
                        $sub_cat_ids[] = $sub_cat->id;
                    }

                    foreach($sub_cats as $sub_cat)
                    {
                        //-->> get sub cat products
                        $sub_cat_products = $this->cat_model->get_sub_cat_products($sub_cat->id);

                        if(count($sub_cat_products) > 0)
                        {
                            echo lang('error_products_category_exist');

                         }else{
                            //delete sub cat
                            $this->cat_model->delete_category_data($sub_cat_ids);
                            //delete parent cat
                            $this->cat_model->delete_category_data($ids_array);
                        }
                    }*/
                }
                else
                {
                    $cat_products_count = $this->cat_model->get_cat_products($ids_array);

                    if($cat_products_count > 0)
                    {
                        echo lang('error_products_category_exist');
                    }else{
                        $this->cat_model->delete_category_data($ids_array);
                    }
                }
            }
        }
        else
        {
               $sub_cats = $this->cat_model->get_sub_cats($categories_ids);

                if(count($sub_cats) > 0)
                {
                    echo lang('error_sub_category_exist');
                    /*foreach($sub_cats as $sub_cat)
                    {
                        $sub_cat_ids[] = $sub_cat->id;
                    }

                    foreach($sub_cats as $sub_cat)
                    {
                        //-->> get sub cat products
                        $sub_cat_products = $this->cat_model->get_sub_cat_products($sub_cat->id);

                        if(count($sub_cat_products) > 0)
                        {
                            echo lang('error_products_category_exist');

                         }else{
                            //delete sub cat
                            $this->cat_model->delete_category_data($sub_cat_ids);
                            //delete parent cat
                            $this->cat_model->delete_category_data($ids_array);
                        }
                    }*/
                }
                else
                {
                    $ids_array = array($categories_ids);

                    $cat_products_count = $this->cat_model->get_cat_products($ids_array);

                    if($cat_products_count > 0)
                    {
                        echo lang('error_products_category_exist');
                    }
                    else
                    {
                        $this->cat_model->delete_category_data($ids_array);
                    }

                     //$this->cat_model->delete_category_data($ids_array);
                }
        }
    }

/************************************************************************/
}
