<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Categories extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('categories/cat_model');
        $this->load->model('categories_model');
        $this->load->model('general_model');
        $this->load->library('api_lib');

    }

    public function index()
    {
        $lang_id        = intval($this->input->post('langId', TRUE));
        $deviceId       = strip_tags($this->input->post('deviceId', TRUE));
        $parent_id      = $this->input->post('parentId', TRUE);
        $store_id       = intval($this->input->post('storeId', TRUE));
        $email          = strip_tags($this->input->post('email', TRUE));
        $password       = strip_tags($this->input->post('password', TRUE));

        $agent          = strip_tags($this->input->post('agent', TRUE));
        
        if($this->ion_auth->login($email, $password))
        {
            $user_data = $this->ion_auth->user()->row();
            $user_id = $user_data->id;
            // $this->api_lib->check_user_store_country_id($email, $password, $user_data->id, $country_id);
        }
        else {
            $user_id = 0;
        }
        
        if($parent_id != 'sub')
        {
            $conds = array(
              'categories.parent_id'=>$parent_id,
            //'products_countries.country_id' => 2
          );
            $categories = $this->cat_model->get_categories($lang_id, 0, 1, $conds);
            //$this->categories_model->get_categories($lang_id, $parent_id, $store_id);
        }
        else
        {
            $categories = $this->categories_model->get_sub_categories($lang_id, $store_id);
        }

        $output = array();

        $images_path = $this->api_lib->get_images_path();

        if(isset($categories) && count($categories) != 0)
        {
            foreach($categories as $category)
            {


                if(isset($category->image)&& $category->image != '')
                {
                     //$pic = base_url().'assets/uploads/'.$category->image;
                    // $thump  = base_url().'assets/uploads/thumb/'.$category->image;
                    $pic    = $images_path.$category->image;
                    $thump  = $images_path.'thumb/'.$category->image;
                }
                else
                {
                   $pic = '';
                   $thump = '';
                }
                
                if(isset($category->icon)&& $category->icon != '')
                {
                    $icon   = $images_path.$category->icon;
                }
                else
                {
                   $icon    = '';
                }

                //$image_path = realpath(APPPATH. '../assets/uploads/'.$category->image);

                //$image_code = $this->api_lib->get_image_code($image_path);
                if(isset($category->products_count))
                {
                    $products_count = $category->products_count;
                }
                else
                {
                    $products_count = 0;
                }
                
                //print_r($category);die();
                
                $output[] = array(
                                    'categoryId'        => $category->id        ,
                                    'categoryParentId'  => $category->parent_id ,
                                    'categoryName'      => $category->name      ,
                                    'categoryImage'     => $pic                 ,
                                    'thumbnail'         => $thump               ,
                                    'productsCount'     => $products_count      ,
                                    'icon'              => $icon                ,
                                    //'imageBitMap'       => $image_code
                                    );
              }

        }
        else
        {
            $fail_message   = $this->general_model->get_lang_var_translation('no_available_cats',$lang_id);
            $output         = array(
                                        'message' => $fail_message,
                                        'response' => 0
                                    );
        }
        
        //***************LOG DATA***************//
        $this->api_lib->insert_log($user_id, current_url(), 'Categories', $agent, $_POST, $output);
        //***************END LOG***************//

        $this->output->set_content_type('application/json')->set_output(json_encode($output));


    }




/************************************************************************/
}
