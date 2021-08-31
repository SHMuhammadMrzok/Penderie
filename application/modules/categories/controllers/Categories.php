<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Categories extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        require(APPPATH . 'includes/front_end_global.php');

        $this->load->model('cat_model');
        $this->load->model('categories_tags_model');
        $this->load->model('products/site_products_model');
    }

    var $data = array();

    public function sub_cats($cat='')
    {
        #MAIN CATEGORY PRODUCTS

        $this->session->set_userdata('site_redir', current_url());
        $ids_array     = array();
        $sub_cats_data = array();
        $products_ids  = array();
        $cat           = strip_tags($cat);
        $store_id      = 0;
        $lang_id       = $this->data['lang_id'];
        $country_id    = $this->data['country_id'];

        $cat_data      = $this->cat_model->get_cat_by_route($cat);
        $cat_id        = $cat_data->id;

        $sub_cats_data = $this->site_products_model->get_category_sub_cats_translation($cat_id, $lang_id);

        if(count($sub_cats_data) != 0)
        {
            foreach($sub_cats_data as $row)
            {
                $ids_array[] = $row->id;
                //$sub_cats_names[$id->id] = $id->name;
                $products_count = $this->cat_model->get_category_products($row->id);
                $row->{'products_count'} = $products_count;
                $cats_array[] = $row;

            }

        }
        else
        {
            $this->data['error_msg'] = lang('no_available_products');
        }

        $cat_data        = $this->cat_model->get_row_data($cat_id, $lang_id);
        $cat_meta_tags   = $this->categories_tags_model->get_cat_tags($cat_id, $lang_id);
        $cat_meta_data   = $this->cat_model->get_row_meta_tag_description($cat_id, $lang_id);


        $tags = '';
        foreach($cat_meta_tags as $tag)
        {
            $tags .= $tag->tag.' , ';
        }


        $this->data['meta_keywords']     = $tags;
        $this->data['meta_description']  = $cat_meta_data->meta_tag_description;
        $this->data['page_title']        = $cat_meta_data->meta_title;
        $this->data['cat_data']          = $cat_data;
        $this->data['sub_cats']          = $cats_array;


        $this->data['content'] = $this->load->view('category_index', $this->data, true);
        $this->load->view('site/main_frame', $this->data);

    }


/************************************************************************/
}
