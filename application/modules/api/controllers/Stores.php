<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Stores extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('general_model');
        $this->load->model('stores/stores_model');
        $this->load->model('products/products_model');
        $this->load->model('products/site_products_model');

        $this->load->library('api_lib');
        $this->load->library('products_lib');

    }

    public function all_stores()
    {
        $firsts_array = array();

        $lang_id      = 1;//intval(strip_tags($this->input->post('langId', TRUE)));
        $deviceId     = 'dhf';//strip_tags($this->input->post('deviceId', TRUE));
        $page     = 1;//strip_tags($this->input->post('page', TRUE));

        $settings     = $this->general_model->get_settings();
        $images_path = $this->api_lib->get_images_path();
        
        $menu_first_stores  = $this->stores_model->get_menu_first_stores($lang_id);
        $first_stores_ids   = array();

        foreach($menu_first_stores as $store)
        {
            if(!is_null($store->id))
            {
                if($store->store_id == $settings->first_store_id)
                {
                    unset($firsts_array[0]);
                    $firsts_array[0] = $store;
                }
    
                if($store->store_id == $settings->second_store_id)
                {
                    unset($firsts_array[1]);
                    $firsts_array[1] = $store;
                }
    
                if($store->store_id == $settings->third_store_id)
                {
                    unset($firsts_array[2]);
                    $firsts_array[2] = $store;
                }
    
                if($store->store_id == $settings->fourth_store_id)
                {
                    unset($firsts_array[3]);
                    $firsts_array[3] = $store;
                }
    
                if($store->store_id == $settings->fifth_store_id)
                {
                    unset($firsts_array[4]);
                    $firsts_array[4] = $store;
                }
    
                $first_stores_ids[] = $store->store_id;
            }
        }

        ksort($firsts_array);

        if(!$page) $page = 1;
        $limit           = $settings->menu_horizontal_limit;
        $offset          = ($page -1)*$limit;
        
        
        $menu_stores = $this->stores_model->get_menu_stores($lang_id, $first_stores_ids, $limit, $offset, 1);
        //print_r($menu_stores); die();
        //print_r($menu_stores);die();

        $stores_array = array_merge($firsts_array, $menu_stores);
        
        if(count($stores_array) != 0)
        {
            foreach($stores_array as $key=>$store)
            {
                $store_main_cats = $this->stores_model->get_store_main_cats($store->id, $lang_id);
    
                foreach($store_main_cats as $main_cat)
                {
                    $store_sub_cats = $this->stores_model->get_store_sub_cats($main_cat->id, $store->id, $lang_id);
    
                    $sub_cats_array = array();
                    foreach($store_sub_cats as $sub_cat)
                    {
                        $sub_cats_array[] = array(
                                                    'categoryId'     => $sub_cat->id         ,
                                                    'parentId'       => $sub_cat->parent_id  ,
                                                    'categoryName'   => $sub_cat->name       ,
                                                    'categoryDesc'   => $sub_cat->description
                                                );
                    }
    
    
                    $cats_array[] = array(
                                            'categoryId'     => $main_cat->id         ,
                                            'parentId'       => $main_cat->parent_id  ,
                                            'categoryName'   => $main_cat->name       ,
                                            'categoryDesc'   => $main_cat->description,
                                            'subCats'        => $sub_cats_array
                                          );
                }
    
                if(isset($store->image)&& $store->image != '')
                {
                    $pic = $images_path.$store->image;
                }
                else
                {
                   $pic = '';
                }
    
                $output[] = array(
                                    'id'            => $store->id           ,
                                    'facebook'      => $store->facebook     ,
                                    'twitter'       => $store->twitter      ,
                                    'instagram'     => $store->instagram    ,
                                    'youtube'       => $store->youtube      ,
                                    'phone'         => $store->phone        ,
                                    'name'          => $store->name         ,
                                    'address'       => $store->address      ,
                                    'description'   => $store->description  ,
                                    'photo'         => $pic                 ,
                                    'storeCats'     => $cats_array
                                 );
            }
        }
        else
            {
                $fail_message   = $this->general_model->get_lang_var_translation('no_data', $lang_id);
                $output         = array(
                                            'message' => $fail_message,
                                            'response' => 0
                                        );
            }
    
            $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));
    
        
    }







/*


        $stores = $this->stores_model->get_menu_stores($lang_id);
        $output = array();

        if(count($stores) != 0)
        {
            foreach($stores as $store)
            {
                if(isset($store->image)&& $store->image != '')
                {
                    $pic = base_url().'assets/uploads/'.$store->image;
                }
                else
                {
                   $pic = '';
                }

                /*$cats_array         = array();
                $store_cats_data    = $this->stores_model->get_store_available_cats_data($store->id, $lang_id);

                foreach($store_cats_data as $cat)
                {
                    $cats_array[$cat->parent_id][] = array(
                                                            'categoryId'     => $cat->id         ,
                                                            'parentId'  => $cat->parent_id  ,
                                                            'categoryName'   => $cat->name       ,
                                                            'categoryDesc'   => $cat->description
                                                          );
                }

                */

/*                $store_main_cats = $this->stores_model->get_store_main_cats($store->id, $lang_id);

                foreach($store_main_cats as $main_cat)
                {
                    $store_sub_cats = $this->stores_model->get_store_sub_cats($main_cat->id, $store->id, $lang_id);

                    $sub_cats_array = array();
                    foreach($store_sub_cats as $sub_cat)
                    {
                        $sub_cats_array[] = array(
                                                    'categoryId'     => $sub_cat->id         ,
                                                    'parentId'       => $sub_cat->parent_id  ,
                                                    'categoryName'   => $sub_cat->name       ,
                                                    'categoryDesc'   => $sub_cat->description
                                                );
                    }


                    $cats_array[] = array(
                                            'categoryId'     => $main_cat->id         ,
                                            'parentId'       => $main_cat->parent_id  ,
                                            'categoryName'   => $main_cat->name       ,
                                            'categoryDesc'   => $main_cat->description,
                                            'subCats'        => $sub_cats_array
                                          );
                }

                $output[] = array(
                                    'id'            => $store->id           ,
                                    'facebook'      => $store->facebook     ,
                                    'twitter'       => $store->twitter      ,
                                    'instagram'     => $store->instagram    ,
                                    'youtube'       => $store->youtube      ,
                                    'phone'         => $store->phone        ,
                                    'name'          => $store->name         ,
                                    'address'       => $store->address      ,
                                    'description'   => $store->description  ,
                                    'photo'         => $pic                 ,
                                    'storeCats'     => $cats_array
                                 );

            }
        }
        else
        {
            $fail_message   = $this->general_model->get_lang_var_translation('no_data', $lang_id);
            $output         = array(
                                        'message' => $fail_message,
                                        'response' => 0
                                    );
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));
    }
 */
    public function store_products()
    {
        $lang_id      = intval(strip_tags($this->input->post('langId', TRUE)));
        $country_id   = strip_tags($this->input->post('countryId', TRUE));
        $store_id     = strip_tags($this->input->post('storeId', TRUE));
        $email        = strip_tags($this->input->post('email', TRUE));
        $password     = strip_tags($this->input->post('password', TRUE));
        $deviceId     = strip_tags($this->input->post('deviceId', TRUE));

        if($this->ion_auth->login($email, $password))
        {
            $user              = $this->ion_auth->user()->row();
            $user_id           = $user->id;

            $this->api_lib->check_user_store_country_id($email, $password, $user_id, $country_id);

            $user              = $this->ion_auth->user()->row();
            $is_logged         = 1;
            $customer_group_id = $user->customer_group_id;

        }
        else
        {
            $is_logged = 0;
            $user_id   = 0;
        }

        $m_products_array       = $this->site_products_model->get_store_products($store_id, $lang_id, $country_id);
        $m_products_new_array   = $this->_get_products_prices($m_products_array, $lang_id, $country_id, $user_id, $deviceId);
        $m_store_data           = $this->stores_model->get_row_data($store_id, $lang_id);
        //$m_store_cats           = $this->stores_model->get_store_available_cats_data($store_id, $lang_id);

        if(count($m_products_new_array) != 0)
        {
            /*foreach($m_store_cats as $cat)
            {
                $m_cats_array[$cat->parent_id][] = $cat;
            }

            $this->data['s_products']     = $m_products_new_array;
            $this->data['s_data']   = $m_store_data;
            $this->data['s_cats']   = $m_cats_array;
            */

            $output = $m_products_new_array;
        }
        else
        {
            $fail_message   = $this->general_model->get_lang_var_translation('no_available_products', $lang_id);
            $output         = array(
                                        'message' => $fail_message,
                                        'response' => 0
                                    );
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));

    }


    private function _get_products_prices($products_array, $lang_id, $country_id, $user_id, $deviceId)
    {
        $output = array();

        foreach($products_array as $product)
        {
            $product_details    = $this->products_model->get_product_row_details($product->id, $lang_id, $country_id);
            $product_price_data = $this->products_lib->get_product_price_data($product_details, $country_id, $user_id, $deviceId);
            $currency           = $this->currency->get_country_currency_name($country_id, $lang_id);

            $product_price      = '';

            if($product_price_data[0] != $product_price_data[1])
            {
                $product_price = $product_price_data[0];
            }

            $product_new_price  = $product_price_data[1];

            $pic = '';
            $images_path = $this->api_lib->get_images_path();
            if(isset($product->image))
            {
                $pic =  $images_path.$product->image;
            }

            //$product_optional_fields = $this->get_product_optional_fields($product->product_id, $lang_id);

            $image_path = realpath(APPPATH. '../assets/uploads/products/'.$product->image);
            //$image_code = $this->api_lib->get_image_code($image_path);

            $output[] = array(
                                'productId'                     => $product->product_id         ,
                                'categoryId'                    => $product->cat_id             ,
                                //'parentId'                      => $cat_data->parent_id         ,
                                'productName'                   => $product->title              ,
                                'productPrice'                  => $product_price               ,
                                'productNewPrice'               => $product_new_price           ,
                                'productImage'                  => $pic                         ,
                                'productDescription'            => $product->description        ,
                                'producuctQuantityPerSerial'    => $product->quantity_per_serial,
                                'productCurrency'               => $currency                    ,
                                //'productOptionalFields'         => $product_optional_fields     ,
                                'thumbnail'                     => base_url().'assets/uploads/products/thumb/'.$product->image,
                                'storeName'                     => $product->store_name
                              );
        }

        return $output;
    }




/************************************************************************/
}
