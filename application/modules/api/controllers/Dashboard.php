<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Dashboard extends CI_Controller
{

    public $settings;
    public $site_settings;
    public function __construct()
    {
        parent::__construct();

        $this->load->model('general_model');
        $this->load->model('categories_model');
        $this->load->model('users/countries_model');
        $this->load->model('products/products_model');
        $this->load->model('products/site_products_model');
        $this->load->model('optional_fields/optional_fields_model');
        $this->load->model('advertisements/advertisement_model');
        $this->load->model('stores/stores_model');
        $this->load->model('brands/Brands_model');

        $this->load->library('api_lib');
        $this->load->library('currency');
        $this->load->library('products_lib');
        $this->load->library('shopping_cart');

        $this->settings = $this->general_model->get_settings();
        $this->site_settings = $this->api_lib->get_site_settings();
    }

    public function index()
    {
        $lang_id        = intval($this->input->post('langId', TRUE));
        $country_id     = intval($this->input->post('countryId', TRUE));
        $email          = strip_tags($this->input->post('email', TRUE));
        $password       = strip_tags($this->input->post('password', TRUE));
        $deviceId       = strip_tags($this->input->post('deviceId', TRUE));
        $ip_address     = $this->input->ip_address();
        $output         = array();
        $spammed_users_array = array();

        $images_path = $this->api_lib->get_images_path();

        if($this->ion_auth->login($email, $password))
        {
            $user              = $this->ion_auth->user()->row();
            $user_id           = $user->id;

            $this->api_lib->check_user_store_country_id($email, $password, $user_id, $country_id);

            $is_logged         = 1;
            $customer_group_id = $user->customer_group_id;

            // check user spammed users
            /*$spammed_users = $this->products_model->get_user_spammed_users($user_id);

            if(count($spammed_users) != 0)
            {
                foreach($spammed_users as $row)
                {
                    $spammed_users_array[] = $row->blocked_user_id;
                }
            }
            */

        }
        else
        {
            $is_logged = 0;
            $user_id   = 0;
        }

        $this->shopping_cart->set_user_data($user_id, $deviceId, $ip_address , $country_id ,$lang_id);
        
        //STORES

        if($this->site_settings[0]->value == 'b2b')
        {

          $stores = $this->get_menu_stores($lang_id);

          if(count($stores) != 0)
          {
              foreach($stores as $store)
              {
                  $stores_array[] = array(
                                              'storeId' => $store->store_id,
                                              'storeName' => $store->name,
                                              'image'     => $images_path.$store->image,
                                          );
              }
              $stores_title = $this->general_model->get_lang_var_translation('stores',$lang_id);

              $output[] = array(
                                  'type'  => 'stores',
                                  'productsListType' => '',
                                  'title' => $stores_title,
                                  'data'  => $stores_array
                               );
          }//END stores
        }

        //MAIN CATEGORIES
        $categories = $this->categories_model->get_categories($lang_id, 0, 0);
        if( count($categories) != 0)
        {
            foreach($categories as $category)
            {
                if(isset($category->image)&& $category->image != '')
                {
                    $pic = $images_path.$category->image;
                }
                else
                {
                   $pic = '';
                }

                if(isset($category->icon)&& $category->icon != '')
                {
                    //$pic = base_url().'assets/uploads/'.$category->image;
                    $icon = $images_path.$category->icon;
                }
                else
                {
                   $icon = '';
                }


                $cats_data[] = array(
                                    'categoryId'        => $category->id        ,
                                    //'categoryParentId'  => $category->parent_id ,
                                    'categoryName'      => $category->name      ,
                                    'categoryImage'     => $pic                 ,
                                    'categoryIcon'     => $icon                 ,

                                    //'thumbnail'         => $images_path.$category->image

                                    );
              }

              $cats_title = $this->general_model->get_lang_var_translation('sub_categories',$lang_id);

              $output[] = array(
                                    'type'  => 'categories',
                                    'productsListType' => '',
                                    'title' => $cats_title,
                                    'data'  => $cats_data
                               );

        }
        
        

        //COLLECTION PRODUCTS BLOCK
        $products_limit = $this->settings->products_limit;
        $collection_products = $this->site_products_model->get_store_products(0, $lang_id, $country_id, 0, $products_limit);

        if(count($collection_products) != 0)
        {
            $collection_products_array = $this->get_products_array($collection_products, $lang_id, $country_id, $user_id, $deviceId);

            $collection_products_title = $this->general_model->get_lang_var_translation('our_collections', $lang_id);
            if(count($collection_products_array) != 0)
            {
              $output[] = array(
                                'type'  => 'products'           ,
                                'cellType' => 'detailed'        ,
                                'productsListType' => 'new',
                                'title' => $collection_products_title   ,
                                'data'  => $collection_products_array
                             );
          }
        }


        //ads_2 ADV
        $ads_2 = $this->advertisement_model->get_advertisments($lang_id, 'ads_2');
        if(count($ads_2) != 0)
        {
            $ads_2_array = array();
          foreach($ads_2 as $ads_2)
          {
            $ads_2_array[] = array(
                                    'advId'     => $ads_2->id,
                                    'advUrl'    => $ads_2->url,
                                    'advImage'  => $images_path.$ads_2->image,
                                  );
          }
        $output[] = array(
                            'type'  => 'image'  ,
                            'title' => ''       ,
                            'productsListType' => '',
                            'data'  => $ads_2_array
                         );

        }//END middle adv


        //MOST BOUGHT PRODUCTS BLOCK
        $products_limit = $this->settings->products_limit;
        $most_bought_products = $this->orders_model->get_most_bought_store_products($lang_id, $country_id, 0, $products_limit, $user_id, $spammed_users_array);

        if(count($most_bought_products) != 0)
        {
            $most_bought_array = $this->get_products_array($most_bought_products, $lang_id, $country_id, $user_id, $deviceId);
            $most_bought_title = $this->general_model->get_lang_var_translation('most_bought_products',$lang_id);

            $output[] = array(
                                'type'  => 'products'           ,
                                'cellType' => 'detailed'        ,
                                'productsListType' => 'most_bought',
                                'title' => $most_bought_title   ,
                                'data'  => $most_bought_array
                             );
        }

        //END most bought products

        //Middle ADV
        $mid_adv = $this->advertisement_model->get_advertisments($lang_id, 'middle');
        if(count($mid_adv) != 0)
        {
            $mid_adv_array = array();
          foreach($mid_adv as $middle)
          {
            $mid_adv_array[] = array(
                                    'advId'     => $middle->id,
                                    'advUrl'    => $middle->url,
                                    'advImage'  => $images_path.$middle->image,
                                  );
          }
            $output[] = array(
                                'type'  => 'image'  ,
                                'title' => ''       ,
                                'productsListType' => '',
                                'data'  => $mid_adv_array
                             );

        }
        //END middle adv


        


        /*
        //MOST SEARCHED PRODUCTS
        $most_searched_products = $this->site_products_model->get_most_searched_store_products($lang_id, $country_id, 0, $products_limit, $user_id, $spammed_users_array);
        if(count($most_searched_products) != 0)
        {
            $most_searched_array = $this->get_products_array($most_searched_products, $lang_id, $country_id, $user_id, $deviceId);

            $most_searched_title = $this->general_model->get_lang_var_translation('most_searched',$lang_id);

            $output[] = array(
                                'type'  => 'products'           ,
                                'productsType' => 'most_searched',
                                'title' => $most_searched_title   ,
                                'data'  => $most_searched_array
                             );
        }//END most searched
        */


        //MOST VIEWED PRODUCTS
        $most_viewed_products = $this->site_products_model->get_most_viewed_products($lang_id, $country_id, 0, $products_limit, $user_id, $spammed_users_array);


        if(count($most_viewed_products) != 0)
        {
            $most_viewed_array = $this->get_products_array($most_viewed_products, $lang_id, $country_id, $user_id, $deviceId);
            $most_viewed_title = $this->general_model->get_lang_var_translation('most_viewed', $lang_id);

            $output[] = array(
                                'type'  => 'products'           ,
                                'cellType' => 'short'           ,
                                'productsListType' => 'most_viewed',
                                'title' => $most_viewed_title   ,
                                'data'  => $most_viewed_array
                             );
        }
        //END most VIEWED

        //Bottom ADV
        $bottom_ads = $this->advertisement_model->get_advertisments($lang_id, 'bottom');

        if(count($bottom_ads) != 0)
        {
            $bottom_array = array();
          foreach($bottom_ads as $bottom)
          {
            $bottom_array[] = array(
                                    'advId'     => $bottom->id,
                                    'advUrl'    => $bottom->url,
                                    'advImage'  => $images_path.$bottom->image,
                                  );
          }
            $output[] = array(
                                'type'  => 'image'  ,
                                'title' => ''       ,
                                'productsListType' => '',
                                'data'  => $bottom_array
                             );

        }
        //END bottom adv


        $limit  = $this->settings->products_limit;
        $offset = 0;

        $images_path = $this->api_lib->get_images_path();

        $all_brands = $this->Brands_model->get_all_brands($lang_id, $limit, $offset);

        if( count($all_brands) != 0)
        {
            foreach($all_brands as $brands)
            {
                $brands_data[] = array(
                                        'brandId'   => $brands->brand_id ,
                                        'name'      => $brands->name     ,
                                        'image'     => $images_path.$brands->image
                                       );
             }

              $brands_title = $this->general_model->get_lang_var_translation('brands',$lang_id);

              $output[] = array(
                                    'type'  => 'brands',
                                    'productsListType' => '',
                                    'title' => $brands_title,
                                    'data'  => $brands_data
                               );

        }
        
        //END Brands



        $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));


    }

    public function get_products_array($products_array, $lang_id, $country_id, $user_id, $deviceId)
    {
        $settings = $this->general_model->get_settings();
        $images_path = $this->api_lib->get_images_path();
        $products_res_array = array();

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

            if(isset($product->image))
            {
                //$pic =  base_url().'assets/uploads/products/'.$product->image;
                $pic =  $images_path.$product->image;
            }

            $product_optional_fields = $this->get_product_optional_fields($product->product_id, $lang_id);

            if(count($product_optional_fields) != 0)
            {
                $optional_fields = 1;
            }
            else
            {
                $optional_fields = 0;
            }


            $vat_val      = $product_price_data['vat_value'];
            $vat_percent  = $product_price_data['vat_percent'];
            $is_cart      = $this->shopping_cart->check_product_in_cart($product->product_id);
            $is_fav       = $this->products_model->check_product_in_fav($product->product_id, $user_id);
            $is_compare   = $this->products_model->check_product_in_compare($product->product_id, $user_id);
            $product_images  = $this->products_model->get_product_images($product->product_id);
            $images_array= array();

            if(count($product_images) != 0)
            {
                foreach($product_images as $image)
                {
                    //$images_array[] = base_url().'assets/uploads/products/'.$image->image;
                    $images_array[] = $images_path.$image->image;
                }
            }

            $brand_name = '';
            if($product->brand_id != 0)
            {
                $brand_name  = $product_details->brand_name;
            }

            $availability = true;  // true means product has stock , false means product has no stock
            $rest_qty     = 0; // only rest (number) items of product "Message"

            if($product->quantity_per_serial == 1)
            {
                $product_qty   = $this->products_model->count_product_available_quantity($product->id, $country_id);
                $min_stock     = $settings->min_product_stock;
                $rest_qty      = $settings->rest_product_qty;
                $available_qty = $product_qty - $min_stock;

                if($product_qty == 0)
                {
                  $availability = false;
                }

                if($available_qty <= $rest_qty && $product_qty!=0)
                {
                  $rest_qty   = $available_qty;  // same as $stock_qty
                }

            }
            else
            {
                $availability = true;
            }

            if($availability)
            {
              $products_res_array[] = array(
                                'productId'                     => $product->product_id         ,
                                //'categoryId'                    => $product->cat_id             ,
                                'productName'                   => $product->title              ,
                                'route'                         => $product->route              ,
                                'productPrice'                  => $product_price               ,
                                'productNewPrice'               => $product_new_price           ,
                                //'vatValue'                      => "$vat_val",
                                //'vatPercent'                    => "$vat_percent",
                                'productImage'                  => $pic                         ,
                                //'productDescription'            => $product->description        ,
                                //'producuctQuantityPerSerial'    => $product->quantity_per_serial,
                                'productCurrency'               => $currency                    ,
                                //'productOptionalFields'         => $product_optional_fields     ,
                                'optionalFieldsExist'           => $optional_fields             ,
                                //'thumbnail'                     => base_url().'assets/uploads/products/thumb/'.$product->image,
                                //'thumbnail'                     => $images_path.$product->image,
                                'storeName'                     => $product->store_name         ,
                                //'new'                           => $product->new                ,
                                'views'                         => $product->view               ,
                                //'isCart'                        => $is_cart                     ,
                                'isFav'                         => $is_fav                      ,
                                //'isCompare'                     => $is_compare                  ,
                                'productImages'                 => $images_array                ,
                                'brandName'                     => $brand_name                  ,
                                //'totalPoints'                   => $product_details->total_rating_points,
                                'ratingTimes'                   => $product->rating_times       ,
                                'ratingAvg'                     => $product->rating_avg         ,
                                'availableProduct'              => $availability                        ,
                                'restQty'                       => $rest_qty                            ,
                                // 'product_route'                 => $this->site_settings->product_route         ,
                                // 'sub_category_route'            => $this->site_settings->sub_category_route

                              );
            }
        }

        return $products_res_array;
    }

    public function get_product_optional_fields($product_id, $lang_id)
    {
        $product_optional_fields = $this->products_model->get_product_optional_fields($product_id, $lang_id);

        if(count($product_optional_fields) != 0)
        {
            $options_array = array();

            foreach ($product_optional_fields as $field)
            {
                $option_options_array = array();

                if($field->has_options == 1)
                {
                    $option_options   = $this->optional_fields_model->get_optional_field_options($field->optional_field_id, $lang_id);

                    foreach ($option_options as $option)
                    {
                        $option_options_array[] = array(
                                                         'optionId'    => $option->id,
                                                         'optionLabel' => $option->field_value
                                                       );
                    }
                }

                $options_array[] = array(
                                            'id'            => $field->id,
                                            'required'      => $field->required,
                                            'label'         => $field->label,
                                            'fieldTypeId'   => $field->field_type_id,
                                            'defaultValue' => $field->default_value,
                                            'options'       => $option_options_array
                                        );

            }

            $output = $options_array;

        }
        else
        {
            $output = array();
        }

        return $output;
    }

    public function get_menu_stores($lang_id)
    {
        $menu_array   = array();
        $firsts_array = array();
        $first_stores_ids = array();

        $menu_first_stores  = $this->stores_model->get_menu_first_stores($lang_id);

        foreach($menu_first_stores as $store)
        {
            if($store->store_id == $this->settings->first_store_id)
            {
                unset($firsts_array[0]);
                $firsts_array[0] = $store;
            }

            if($store->store_id == $this->settings->second_store_id)
            {
                unset($firsts_array[1]);
                $firsts_array[1] = $store;
            }

            if($store->store_id == $this->settings->third_store_id)
            {
                unset($firsts_array[2]);
                $firsts_array[2] = $store;
            }

            if($store->store_id == $this->settings->fourth_store_id)
            {
                unset($firsts_array[3]);
                $firsts_array[3] = $store;
            }

            if($store->store_id == $this->settings->fifth_store_id)
            {
                unset($firsts_array[4]);
                $firsts_array[4] = $store;
            }

            $first_stores_ids[] = $store->store_id;
        }

        ksort($firsts_array);

        //$menu_stores = $this->stores_model->get_menu_stores($lang_id, $this->settings->menu_horizontal_limit, $first_stores_ids, 25, 0);
        $menu_stores = $this->stores_model->get_menu_stores($lang_id, array(), $this->settings->menu_horizontal_limit, 0, 1);

        $stores_array = array_merge($firsts_array, $menu_stores);

        foreach($stores_array as $key=>$store)
        {
            $cats_array         = array();
            $store_cats_data    = $this->stores_model->get_store_available_cats_data($store->id, $lang_id);

            foreach($store_cats_data as $cat)
            {
                $cats_array[$cat->parent_id][] = $cat;
            }

            $store->{'store_cats'} = $cats_array;

            $menu_array[] = $store;
        }

        return $menu_array;
    }



/************************************************************************/
}
