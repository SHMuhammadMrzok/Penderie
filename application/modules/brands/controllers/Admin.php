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

        $this->load->model('brands_model');
        $this->load->model('categories/cat_model');
        $this->load->library('uploaded_images');

        $this->lang_row = $this->admin_bootstrap->get_active_language_row();
    }

    public function index()
    {
        $lang_id = $this->data['active_language']->id;

        $this->data['count_all_records'] = $this->brands_model->get_count_all_brands($lang_id);
        $this->data['data_language']     = $this->lang_model->get_active_data_languages();


        $this->data['columns']           = array(
                                                     lang('name'),
                                                     lang('thumbnail'),
                                                     lang('active')
                                                );


        $this->data['actions']           = array( 'delete'=>lang('delete'));
        $this->data['search_fields']     = array( lang('name'));

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

        $grid_data  = $this->brands_model->get_brands_data($lang_id, $limit, $offset, $search_word);

        $db_columns = array(
                             'id',
                             'brand_name',
                             'image',
                             'active'
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
                }
                else
                {
                    $new_grid_data[$key][$column] = $row->{$column};
                }

            }
        }

        $this->data['grid_data']          = $new_grid_data;
        $this->data['count_all_records']  = $this->brands_model->get_count_all_brands($lang_id, $search_word);
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
            }

            $this->form_validation->set_rules('image', lang('thumbnail'), 'required');
            //$this->form_validation->set_rules('cat_id', lang('category'), 'required');

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
            $image   = strip_tags($this->input->post('image', true));
            //$cat_id  = intval($this->input->post('cat_id', true));

            $data    = array(
                                'active' => (isset( $_POST['active']))? $this->input->post('active'):0,
                                'image'  => $image,
                                //'cat_id' => $cat_id,
                            );

            if($this->brands_model->insert_brand($data))
            {
                $this->uploaded_images->resize_image($image, 2);

                $last_insert_id = $this->db->insert_id();
                $name           = $this->input->post('name');

                foreach($languages as $lang_id)
                {
                    $brands_translation_data = array(
                                                        'brand_id' => $last_insert_id  ,
                                                        'name'      => $name[$lang_id]  ,
                                                        'lang_id'   => $lang_id
                                                     );

                    $this->brands_model->insert_brands_translation($brands_translation_data);
                }

                $this->session->set_flashdata('success',lang('success'));

                redirect('brands/admin/','refresh');
            }
        }
     }

     private function _add_form($validation_msg)
     {

        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }

        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/add";
        $this->data['mode']         = 'edit';

        $conds = array(
                        'categories.has_brands' => 1
                      );

        /*$categories = $this->cat_model->get_categories($this->lang_row->id, 0, 1, $conds);
        $cats_array = array();

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



        $this->data['categories'] = $cats_array;
        */
        $this->data['content'] = $this->load->view('form', $this->data, true);
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
                }

                $this->form_validation->set_rules('image', lang('thumbnail'), 'required');
                //$this->form_validation->set_rules('cat_id', lang('category'), 'required');

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
                $image   = strip_tags($this->input->post('image', true));
                //$cat_id  = intval($this->input->post('cat_id', true));

                $data    = array(
                                    'active' => (isset( $_POST['active']))? $this->input->post('active'):0,
                                    'image'  => $image,
                                    //'cat_id' => $cat_id,
                                );

                $this->brands_model->update_brand($id, $data);
                $this->uploaded_images->resize_image($image, 2);

                $name   = $this->input->post('name');

                foreach($languages as $lang_id)
                {
                    $brands_translation_data = array(
                                                        'name' => $name[$lang_id]
                                                      );

                    $this->brands_model->update_brands_translation($id, $lang_id, $brands_translation_data);
                }

                $this->session->set_flashdata('success',lang('updated_successfully'));
                redirect('brands/admin/','refresh');
            }
        }
     }

     private function _edit_form($id, $validation_msg)
     {
        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/edit/".$id;
        $this->data['mode']         = 'edit';
        $this->data['id']           = $id;
        $general_data               = $this->brands_model->get_brand_row($id);

        $data                       = $this->brands_model->get_brand_translation_result($id);
        $filtered_data              = array();

        foreach($data as $row)
        {
            $filtered_data[$row->lang_id] = $row;
        }

        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }

        $conds = array(
                        'has_brands' => 1
                      );

        /*$categories = $this->cat_model->get_categories($this->lang_row->id, 0, 1, $conds);

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
        $this->data['categories'] = $cats_array;
*/

        $this->data['data']             = $filtered_data;
        $this->data['general_data']     = $general_data;

        $this->data['content']          = $this->load->view('form', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
     }

     public function read($id,$display_lang_id)
     {
        $id              = intval($id);
        $display_lang_id = intval($display_lang_id);

        if($id && $display_lang_id)
        {
            $data     = $this->brands_model->get_row_data($id, $display_lang_id);

            if($data->active == 1)
            {
                $active_value = lang('active');
            }
            else
            {
                $active_value = lang('not_active');
            }

            $row_data = array(
                                lang('name')        => $data->name ,
                                lang('active')      => '<span class="badge badge-info">'.$active_value.'</span>',
                                lang('thumbnail')   => "<a href='".$images_path.$data->image."' class='image-thumbnail' ><img src='".$images_path.$data->image."' width='150' height='50'  /></a>"
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
        $brands_ids = $this->input->post('row_id');

        if(is_array($brands_ids))
        {

            $ids_array = array();

            foreach($brands_ids as $id)
            {
                $ids_array[] = $id['value'];
            }
        }
        else
        {
            $ids_array = array($brands_ids);
        }

        $this->brands_model->delete_brand_data($ids_array);

     }



/************************************************************************/
}
