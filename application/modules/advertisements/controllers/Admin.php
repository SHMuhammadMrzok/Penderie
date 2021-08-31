<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Admin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        require(APPPATH . 'includes/global_vars.php');

        $this->load->library('uploaded_images');
        $this->load->model('advertisement_model');
        $this->load->model('categories/cat_model');
    }

   public function index()
    {
        $lang_id = $this->data['active_language']->id;

        $this->data['count_all_records']  = $this->advertisement_model->get_count_all_advertisements($lang_id);
        $this->data['data_language']      = $this->lang_model->get_active_data_languages();

        $this->data['columns']            = array(
                                                     lang('location'),
                                                     lang('image'),
                                                     lang('clicks'),
                                                     lang('active')
                                                  );

        $this->data['orders']             = array(
                                                     lang('location'),
                                                     lang('active')
                                                  );

        $this->data['actions']            = array( 'delete'=>lang('delete'));

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



        $grid_data       = $this->advertisement_model->get_advertisements_data($lang_id,$limit,$offset,$search_word,$order_by,$order_state);

        $db_columns      = array(
                                 'id'          ,
                                 'location'    ,
                                 'image'       ,
                                 'hits'      ,
                                 'active'      ,
                                 'sort'
                           );

       $this->data['hidden_fields'] = array('id','sort');

       $new_grid_data = array();

        foreach($grid_data as $key =>$row)
        {
            foreach($db_columns as $column)
            {
                if($column == 'image')
                {
                    if($row->{$column} != '')
                    {
                        $new_grid_data[$key][$column] = "<a href='".$this->data['images_path'].$row->image."' class='image-thumbnail' ><img src='".$this->data['images_path'].$row->image."' width='150' height='50' /></a>";
                    }
                    else
                    {
                        $new_grid_data[$key][$column] = '';
                    }
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
        $this->data['count_all_records']  = $this->advertisement_model->get_count_all_advertisements($lang_id,$search_word);

        $this->data['display_lang_id']    = $lang_id;

        $output_data = $this->load->view('Admin/grid/grid_data',$this->data, true);
        $count_data  = $this->data['count_all_records'];

        echo json_encode(array($output_data, $count_data, $search_word));
    }

    public function sorting()
    {
        $old_sort = intval($this->input->post('old_sort'));
        $new_sort = intval($this->input->post('new_sort'));

        $this->advertisement_model->sort_rows($old_sort,$new_sort);


    }

    public function read($id,$display_lang_id)
    {
        $id              = intval($id);
        $display_lang_id = intval($display_lang_id);

        if($id && $display_lang_id)
        {
            $data     = $this->advertisement_model->get_row_data($id,$display_lang_id);

            if($data->active == 1)
            {
                $active_value = lang('active');
            }
            else
            {
                $active_value = lang('not_active');
            }

            $row_data = array(
                                lang('title')       => $data->title,
                                lang('description') => $data->description,
                                lang('location')    => $data->location,
                                lang('url')         => $data->url,
                                lang('target')      => lang($data->target),
                                lang('image')       => "<a href='".$images_path.$data->image."' class='image-thumbnail' ><img src='".$images_path.$data->image."' width='120' height='70' /></a>",
                                lang('clicks')      => $data->hits,
                                lang('active')      => '<span class="badge badge-info">'.$active_value.'</span>'

                             );



            $this->data['row_data'] = $row_data;

            $this->data['content']  = $this->load->view('Admin/grid/read_view', $this->data, true);
            $this->load->view('Admin/main_frame',$this->data);
        }
    }

     public function add()
     {
        $validation_msg = false;

        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
        {
            $validation_msg = true;
            $languages = $this->input->post('lang_id');

            $this->form_validation->set_rules('location', lang('location'), 'required');
            $this->form_validation->set_rules('image', lang('image'), 'required');
            $this->form_validation->set_rules('target', lang('target'), 'required');

            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        }

        if ($this->form_validation->run() == FALSE)
		{
		  $this->_add_form($validation_msg);
        }
        else
        {
            $location = $this->input->post('location');
            $active   = $this->input->post('active');
            $image    = $this->input->post('image');
            $url      = $this->input->post('url');
            $target   = $this->input->post('target');
            $cat_id   = intval($this->input->post('cat_id', true));

            $data  = array(
                            'location' => $location ,
                            'category_id' => $cat_id   ,
                            'active'   => $active   ,
                            'image'    => $image    ,
                            'url'      => $url      ,
                            'target'   => $target
                        );

            if($this->advertisement_model->insert_advertisements($data))
            {
                $this->uploaded_images->resize_image($image, 1);

                $last_insert_id = $this->db->insert_id();
                $title          = $this->input->post('title');
                $description    = $this->input->post('description');

                foreach($languages as $lang_id)
                {
                    $advertisements_translation_data = array(
                                                                'advertisement_id'     => $last_insert_id ,
                                                                'title'         => $title[$lang_id],
                                                                'description'   => $description[$lang_id],
                                                                'lang_id'       => $lang_id ,
                                                             );
                    $this->advertisement_model->insert_advertisements_translation($advertisements_translation_data);
                }

                $_SESSION['success'] = lang('success');
                $this->session->mark_as_flash('success');

                redirect('advertisements/admin/','refresh');
           }
        }
    }

    private function _add_form($validation_msg)
    {
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }

        $cats_array[NULL]   = '-----------------';

        $categories = $this->cat_model->get_parent_cats($this->data['lang_id']);

        foreach($categories as $cat)
        {
            /*if($cat->parent_id == 0)
            {
                foreach($categories as $category)
                {
                    if($category->parent_id == $cat->id)
                    {
                        $cats_array["{$cat->name}"][$category->id] = $category->name;
                    }
                }
            }
            */

            $cats_array[$cat->id] = $cat->name;
        }

        $this->data['cats'] = $cats_array;

        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/add";
        $this->data['content']      = $this->load->view('advertisment', $this->data, true);

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

                $languages = $this->input->post('lang_id');
                $this->form_validation->set_rules('location', lang('location'), 'required');
                $this->form_validation->set_rules('image', lang('image'), 'required');
                $this->form_validation->set_rules('target', lang('target'), 'required');
                //$this->form_validation->set_rules('active', lang('activate'), 'required');

                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            }

            if($this->form_validation->run() == FALSE)
    		{
    		   $this->_edit_form($id, $validation_msg);
            }
            else
            {
                $location         = $this->input->post('location');
                $active           = $this->input->post('active');
                $image            = $this->input->post('image');
                $url              = $this->input->post('url');
                $target           = $this->input->post('target');
                $advertisement_id = $this->input->post('advertisement_id');
                $title            = $this->input->post('title');
                $description      = $this->input->post('description');
                $cat_id           = intval($this->input->post('cat_id', true));


                $advertisements_data = array(
                                             'location'   => $location ,
                                             'category_id' => $cat_id  ,
                                             'active'     => $active  ,
                                             'image'      => $image   ,
                                             'url'        => $url     ,
                                             'target'     => $target
                                            );

                $this->advertisement_model->update_advertisements($advertisement_id,$advertisements_data);
                $this->uploaded_images->resize_image($image, 1);

                foreach($languages as $lang_id)
                {
                    $advertisements_translation_data = array(
                                                        'title'         => $title[$lang_id],
                                                        'description'   => $description[$lang_id],
                                                      );
                    $this->advertisement_model->update_advertisements_translation($advertisement_id,$lang_id,$advertisements_translation_data);
                }

                $_SESSION['success'] = lang('updated_successfully');
                $this->session->mark_as_flash('success');

                redirect('advertisements/admin/','refresh');
            }
        }


    }

    private function _edit_form($id, $validation_msg)
    {
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }

        $cats_array[NULL]   = '-----------------';

        $categories = $this->cat_model->get_parent_cats($this->data['lang_id']);

        foreach($categories as $cat)
        {
            $cats_array[$cat->id] = $cat->name;
        }

        /*foreach($categories as $cat)
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
        }*/

        $this->data['cats']         = $cats_array;

        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/edit/" . $id;
        $this->data['id']           = $id;

        $general_data               = $this->advertisement_model->get_advertisements_result($id);
        $data                       = $this->advertisement_model->get_advertisements_translation_result($id);

        $filtered_data              = array();

        foreach($data as $row)
        {
            $filtered_data[$row->lang_id] = $row;
        }

        $this->data['general_data'] = $general_data ;
        $this->data['data']         = $filtered_data;

        $this->data['content']      = $this->load->view('advertisment', $this->data, true);

        $this->load->view('Admin/main_frame',$this->data);
    }


     public function update()
     {
        $languages = $this->input->post('lang_id');

        foreach($languages as $lang_id)
        {
            //$this->form_validation->set_rules('title['.$lang_id.']', 'Title', 'required');
            //$this->form_validation->set_rules('description['.$lang_id.']', 'Description', 'required');

        }
        $this->form_validation->set_rules('location', lang('location'), 'required');
        $this->form_validation->set_rules('image', lang('image'), 'required');
        //$this->form_validation->set_rules('url', lang('url'), 'required|valid_url_format|url_exists');
        $this->form_validation->set_rules('target', lang('target'), 'required');
        $this->form_validation->set_rules('active', lang('activate'), 'required');

        //$this->form_validation->set_message('required', lang('required'));
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

        if ($this->form_validation->run() == FALSE)
		{
		    $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/update";
            $this->data['content']      = $this->load->view('advertisment', $this->data, true);

            $this->load->view('Admin/main_frame',$this->data);

        }
        else
        {
            $location         = $this->input->post('location');
            $active           = $this->input->post('active');
            $image            = $this->input->post('image');
            $url              = $this->input->post('url');
            $target           = $this->input->post('target');
            $advertisement_id = $this->input->post('advertisement_id');
            $title            = $this->input->post('title');
            $description      = $this->input->post('description');


            $advertisements_data = array(
                                         'location' => $location ,
                                         'active'   => $active ,
                                         'image'    => $image,
                                         'url'      => $url,
                                         'target'   => $target
                                        );
            $this->advertisement_model->update_advertisements($advertisement_id,$advertisements_data);

            foreach($languages as $lang_id)
            {
                $advertisements_translation_data = array(
                                                    'title'         => $title[$lang_id],
                                                    'description'   => $description[$lang_id],
                                                  );
                $this->advertisement_model->update_advertisements_translation($advertisement_id,$lang_id,$advertisements_translation_data);
            }

            redirect('advertisements/admin/index','refresh');
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
        $advertisements_ids = $this->input->post('row_id');

        if(is_array($advertisements_ids))
        {

            $ids_array = array();

            foreach($advertisements_ids as $adv_id)
            {
                $ids_array[] = $adv_id['value'];
            }
        }
        else
        {
            $ids_array = array($advertisements_ids);
        }

        $this->advertisement_model->delete_advertisment_data($ids_array);

    }

/************************************************************************/
}
