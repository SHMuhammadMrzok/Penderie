<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Products_filters extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('optional_fields/optional_fields_model');
        $this->load->model('products/site_products_model');
        $this->load->model('categories/cat_model');
        $this->load->model('general_model');
        $this->load->library('api_lib');

    }

    public function index()
    {

        $lang_id      = intval($this->input->post('langId', true));
        $country_id   = intval($this->input->post('countryId', true));
        $cat_id       = intval($this->input->post('catId', true));

        // Added for api log
        $email              = strip_tags($this->input->post('email', TRUE));
        $password           = strip_tags($this->input->post('password', TRUE));  
        $agent              = strip_tags($this->input->post('agent', TRUE));
        $user_id            = 0;

        if($this->ion_auth->login($email, $password))
        {
            $user_data  = $this->ion_auth->user()->row();
            $user_id    = $user_data->id;
        }
        ///

        /*******CATEGORIES FILTER START********/
        $cats_title = $this->general_model->get_lang_var_translation('sub_categories',$lang_id);

        $parent_cats_conds = array('categories.parent_id' => 0);
        $cats = $this->cat_model->get_categories($lang_id, 0, 1, $parent_cats_conds);
        $cats_data = array();
        foreach($cats as $key=>$cat)
        {
            $sub_cat_conds = array('categories.parent_id' => $cat->id);
            $sub_cats = $this->cat_model->get_categories($lang_id, 0, 1, $sub_cat_conds);
            $sub_cats_array = array();
            if(count($sub_cats) != 0)
            {
                foreach($sub_cats as $sub_cat)
                {
                    $sub_cats_array[] = array(
                                                'catId' => $sub_cat->id,
                                                'title' => $sub_cat->name,
                                                'subCats' =>array()
                                             );
                }
            }

            $cats_data[] = array(
                'catId' => $cat->id,
                'title' => $cat->name,
                'subCats' => $sub_cats_array
            );
        }


        $output[] = array(
        'data' => $cats_data,
        'title' => $cats_title ,
        'type' => 'categories'
        );

      /**********CATEGORIES FILTER END***************/

      /**********CATEGORY BRANDS START***************/
      $brands_array = array();
      $brands_title = $this->general_model->get_lang_var_translation('brands',$lang_id);
      /*$brands_filter_data = $this->site_products_model->get_category_brands($cat_id, $lang_id);

      foreach($brands_filter_data as $brand)
      {
        $brands_array[] = array(
        'brandId' => $brand->brand_id,
        'title' => $brand->name,
        );
      }
      */

      $output[] = array(
        'type' => 'brands',
        'data' => $brands_array,
        'title' => $brands_title
      );
      /**********CATEGORY BRANDS END***************/

      /**********PRICES FILTER START***************/
      $price_title = $this->general_model->get_lang_var_translation('price',$lang_id);
      $output[] = array(
        'type' => 'price',
        'title' => $price_title,
        'data' => array()
      );
      /**********PRICES FILTER END***************/

      /**********RATING FILTER START***************/
      $rating_title = $this->general_model->get_lang_var_translation('avg_rating',$lang_id);
      $output[] = array(
        'type' => 'rating',
        'title' => $rating_title,
        'data' => array()
      );
      /**********RATING FILTER END***************/

        
      //***************LOG DATA***************//
      //insert log
      $this->api_lib->insert_log($user_id, current_url(), 'Products filter', $agent, $_POST, $output);
      //***************END LOG***************//

      $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));
    }

    public function get_brands_filter()
    {
        $lang_id      = intval($this->input->post('langId', true));
        $country_id   = intval($this->input->post('countryId', true));
        $cat_id       = intval($this->input->post('catId', true));

        // Added for api log
        $email              = strip_tags($this->input->post('email', TRUE));
        $password           = strip_tags($this->input->post('password', TRUE));  
        $agent              = strip_tags($this->input->post('agent', TRUE));
        $user_id            = 0;

        if($this->ion_auth->login($email, $password))
        {
            $user_data  = $this->ion_auth->user()->row();
            $user_id    = $user_data->id;
        }
        ///
        $cat_brands = $this->site_products_model->get_category_brands($cat_id, $lang_id);
        $output = array();
        if(count($cat_brands) != 0)
        {
          foreach($cat_brands as $brand)
          {
              $output[] = array(
              'brandId' => $brand->brand_id,
              'brandName' => $brand->name,
              );
          }
        }

        //***************LOG DATA***************//
        //insert log
        $this->api_lib->insert_log($user_id, current_url(), 'Products filter - Get brands filter', $agent, $_POST, $output);
        //***************END LOG***************//

        $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));
    }
}
