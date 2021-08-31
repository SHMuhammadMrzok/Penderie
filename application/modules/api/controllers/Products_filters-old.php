<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Products_filters extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('optional_fields/optional_fields_model');
        $this->load->model('categories/cat_model');

    }

    public function index()
    {
      $lang_id = intval($this->input->post('langId', true));
      $cat_ids = $this->input->post('catId', true);
      $cat_ids = json_decode($cat_ids);

      $options_filter = array();
      $cats_ids = array();

      /*********OPTIONAL FIELDS FILTER ******/
      foreach($cat_ids as $cat_id)
      {
        $cat_data = $this->cat_model->get_category_row($cat_id);
        if($cat_data->parent_id != 0)
        {
          $cats_ids = array($cat_id);
        }
        else {
          $sub_cats = $this->cat_model->get_sub_cats($cat_id, 1);

          foreach($sub_cats as $cat)
          {
            $cats_ids[] = $cat->id;
          }
        }
      }

      $products_optional_fields = $this->optional_fields_model->get_optional_field_options(0, $lang_id, 0, 1, array(), $cats_ids);

      foreach($products_optional_fields as $row)
      {
        /*$options_filter[$row->label][] = array(
                          'fieldValue' => $row->field_value,
                          'optionId'   => $row->optional_field_option_id,
                          'image'      => base_url().'assets/uploads/'.$row->image
                        );
                        */
        $options_filter[$row->optional_field_id][] = array(
          'fieldValue' => $row->field_value,
          'optionId'   => $row->optional_field_option_id,
          'label'      => $row->label,
          'image'      => base_url().'assets/uploads/'.$row->image
        );
      }

      foreach($options_filter as $key=>$item)
      {
        $output[] = array(
          'label' => $options_filter[$key][0]['label'],
          'data'  => $item
        );
      }

      $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));
    }
}
