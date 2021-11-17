<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Products extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('products/products_model');
        $this->load->model('products/site_products_model');
        $this->load->model('optional_fields/optional_fields_model');
        $this->load->model('categories/cat_model');
        $this->load->model('general_model');

        $this->load->library('pagination');
        $this->load->library('products_lib');
        $this->load->library('api_lib');
        $this->load->library('shopping_cart');
    }

  public function index_query()
  {
    $lang_id =1;
    $user_id =0;
    $country_id =2;
    $cat_id = 7;//$this->input->post('categoryId', true);
    $productid = 1;

    $product_sql = "SELECT products.id as productId, products.cat_id as categoryId, categories_translation.name as categoryName , products_translation.title as productName, stores_translation.name as storeName, products.image as productImage, products.view as views, IF(shopping_cart_products.cart_id IS NULL, 0, 1 ) as isCart, IF(users_favourite_products.id IS NULL, 0, 1 ) as isFav, IF(users_compare_products.id IS NULL, 0, 1 ) as isCompare,
    brands_translation.name as brandName, products.total_rating_points as totalPoints, products.rating_times as ratingTimes, products.rating_avg as ratingAvg, products_images.productImagesIds as productImages

    FROM products
    join categories_translation on products.cat_id = categories_translation.category_id AND categories_translation.lang_id = ". $lang_id ."
    join products_translation on products.id = products_translation.product_id AND products_translation.lang_id = ". $lang_id ."

    left join stores_translation on products.store_id = stores_translation.store_id AND stores_translation.lang_id = ". $lang_id ."
    left join brands_translation on products.brand_id = brands_translation.brand_id AND brands_translation.lang_id = ". $lang_id ."
    left join ( select products_images.product_id, group_concat(gallery_images.image) AS productImagesIds
                from products_images
                join gallery_images on products_images.image_id = gallery_images.id
                group by products_images.product_id
              ) products_images on products.id = products_images.product_id

    left join shopping_cart on shopping_cart.user_id = ". $user_id ."
    left join shopping_cart_products on products.id = shopping_cart_products.product_id AND shopping_cart.id = shopping_cart_products.cart_id
    left join users_favourite_products on products.id = users_favourite_products.product_id AND users_favourite_products.user_id = ". $user_id ."
    left join users_compare_products ON products.id = users_compare_products.product_id AND users_compare_products.user_id = ". $user_id ."

    WHERE products.cat_id = ". $cat_id ." LIMIT 20";
    $result = $this->db->query($product_sql)->result();
    $images_path    = $this->api_lib->get_images_path();

    foreach($result as $product)
    {
      $images_array = array();
      if($product->productImages != '')
      {
          $images_arr = explode(',', $product->productImages);
          foreach($images_arr as $image)
          {
            $images_array[] = $images_path.$image;
          }
        }
                $output[] = array(
                'productId'                     => $product->productId         ,
                'categoryId'                    => $product->categoryId             ,
                'productName'                   => $product->productName              ,
                'storeName'                     => $product->storeName         ,
                'productPrice'                  => "20"               ,
                'productNewPrice'               => "20"           ,
                'vatValue'                      => "1",
                'vatPercent'                    => "5",
                'productImage'                  => $images_path.$product->productImage                         ,
                'productImageThumb'             => $images_path.$product->productImage                  ,
                'productCurrency'               => 'SAR'                    ,
                'optionalFieldsExist'           => "0"             ,
                'views'                         => $product->views               ,
                'isCart'                        => $product->isCart                     ,
                'isFav'                         => $product->isFav                      ,
                'isCompare'                     => $product->isCompare                  ,
                'productImages'                 => $images_array                ,
                'brandName'                     => $product->brandName                  ,
                'totalPoints'                   => $product->totalPoints,
                'ratingTimes'                   => $product->ratingTimes       ,
                'ratingAvg'                     => $product->ratingAvg         ,
                'availableProduct'              => "true"                        ,
                'restQty'                       => 0                           ,
                'pagesCount'                    => 3
                );
      }

      $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));
  }


  public function index()
  {

        $lang_id        = intval($this->input->post('langId', TRUE));
        $category_id    = intval($this->input->post('categoryId', TRUE));
        $store_id       = intval($this->input->post('storeId', TRUE));
        $brand_id       = intval($this->input->post('brandId', TRUE));
        $country_id     = intval($this->input->post('countryId', TRUE));
        $page           = intval($this->input->post('page', TRUE));
        $email          = strip_tags($this->input->post('email', TRUE));
        $password       = strip_tags($this->input->post('password', TRUE));
        $deviceId       = strip_tags($this->input->post('deviceId', TRUE));
        $ip_address     = $this->input->ip_address();

        //filters
        $price_filter = 0;
        $price_from             = intval($this->input->post('priceFrom', TRUE));
        $price_to               = intval($this->input->post('priceTo', TRUE));

        $optional_fields_filter = $this->input->post('optionalFieldsFilter', TRUE);
        $optional_fields_filter = json_decode($optional_fields_filter);
        $cats_filter            = $this->input->post('catIdsFilter', TRUE);
        $cats_filter            = json_decode($cats_filter);
        $rating_filter          = $this->input->post('rating', true);
        $products_type          = $this->input->post('productsListType', true);

        $agent              = strip_tags($this->input->post('agent', TRUE));
        
        $output       = array();
        $spammed_users_array = array();

        /****************pagination**************/
        if(!$page) $page = 1;
        $limit           = 25;
        $offset          = ($page -1)*$limit;
        $conds           = array();
        $cat_data        = $this->cat_model->get_category_row($category_id);

        if(count($cat_data) == 0)
        {
            $parent_id = 0;
        }
        else
        {
            $parent_id = $cat_data->parent_id;

            if($parent_id == 0)
            {
              // if parent category
              $conds['products.parent_cat_id'] = $category_id;
            }
            else {
              $conds['products.cat_id'] = $category_id;
            }
        }

        if($this->ion_auth->login($email, $password))
        {
            $user              = $this->ion_auth->user()->row();
            $user_id           = $user->id;

            $is_logged         = 1;
            $customer_group_id = $user->customer_group_id;
        }
        else
        {
            $is_logged = 0;
            $user_id   = 0;
        }

        if( $parent_id != 0 && $category_id !=0)
        {
            $ids_array = array($category_id);
        }

        if(count($cats_filter) != 0)
        {
          $ids_array = $cats_filter;
        }

        if($price_from != 0)
        {
          $conds['products_countries.price >= '] = $price_from;
        }

        if($price_to != 0)
        {
          $conds['products_countries.price <= '] = $price_to;
        }

        if($brand_id != 0)
        {
          $conds['products.brand_id'] = $brand_id;
        }

        $sort = 0;
        $most_bought_products = false;

        if($products_type == 'most_bought')
        {
          $most_bought_products = true;
        }
        else if($products_type == 'most_viewed')
        {
          $sort = 7;
        }

        $ids_array = array();
        $this->shopping_cart->set_user_data($user_id, $deviceId, $ip_address , $country_id ,$lang_id);
        $cart_id   = $this->shopping_cart->get_cart_id();

        if($most_bought_products)
        {
          $products = $this->orders_model->get_most_bought_store_products($lang_id, $country_id, $store_id, $limit, $user_id, array(), $offset);
          $all_products_count = $this->orders_model->get_most_bought_store_products_count($lang_id, $country_id, $store_id, $user_id, array());
        }
        else
        {
          $products  = $this->site_products_model->get_cats_products($ids_array, $lang_id, $country_id, $limit, $offset, $store_id, $sort,$price_filter,$rating_filter, $conds, $user_id, array(), $optional_fields_filter, 1, $cart_id);
          $all_products_count = $this->site_products_model->get_cats_products_count($ids_array, $lang_id, $country_id, $store_id, 0,$price_filter,$rating_filter, $conds, $user_id, array(), $optional_fields_filter);
        }
        $pages_count = ceil($all_products_count/$limit);


        if(count($products) != 0)
        {
            $output = $this->_get_products_array($products, $lang_id, $country_id, $user_id, $deviceId, $cat_data, $pages_count);
        }
        else
        {
            $fail_message   = $this->general_model->get_lang_var_translation('no_available_products',$lang_id);
            $output         = array(
                                        'message'   => $fail_message,
                                        'response'  => 0
                                   );
        }


        //***************LOG DATA***************//
        //insert log
        $this->api_lib->insert_log($user_id, current_url(), 'Products', $agent, $_POST, $output);
        //***************END LOG***************//

        $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));
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
                    $option_options   = $this->optional_fields_model->get_optional_field_options($field->optional_field_id, $lang_id, $product_id);

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

  private function _get_products_array($products, $lang_id, $country_id, $user_id, $deviceId, $cat_data, $pages_count)
  {
      $output = array();
      $settings = $this->general_model->get_settings();

      $images_path = $this->api_lib->get_images_path();


      foreach($products as $product)
      {
          $product_price_data = $this->products_lib->get_product_price_data($product, $country_id, $user_id, $deviceId);

          $product_price      = '';

          if($product_price_data[0] != $product_price_data[1])
          {
              $product_price = $product_price_data[0];
          }

          $product_new_price  = $product_price_data[1];

          $pic = '';

          if(isset($product->image))
          {
              $pic = $images_path.$product->image;
              $thumb_name = $images_path.$product->image;
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


          $is_cart    = 0;
          $is_fav     = 0;
          $is_compare = 0;

          if($product->cart_id > 0 && !is_null($product->cart_id))
          {
            $is_cart = 1;
          }

          if($product->fav_id > 0 && !is_null($product->fav_id))
          {
            $is_fav = 1;
          }

          if($product->compare_id > 0 && !is_null($product->compare_id))
          {
            $is_compare = 1;
          }

          $product_images  = $this->products_model->get_product_images($product->product_id);
          $images_array= array();

          if(count($product_images) != 0)
          {
              foreach($product_images as $image)
              {
                  $images_array[] = $images_path.$image->image;
              }
          }

          $brand_name = '';
          if($product->brand_id != 0)
          {
              $brand_name  = $product->brand_name;
          }

          $output[] = array(
                              'productId'                     => $product->product_id         ,
                              'categoryId'                    => $product->cat_id             ,
                              'productName'                   => $product->title              ,
                              'storeName'                     => $product->store_name         ,
                              'productPrice'                  => $product_price               ,
                              'productNewPrice'               => $product_new_price           ,
                              'productImage'                  => $pic                         ,
                              'productCurrency'               => $product->currency           ,
                              'optionalFieldsExist'           => $optional_fields             ,
                              'views'                         => $product->view               ,
                              'isCart'                        => $is_cart                     ,
                              'isFav'                         => $is_fav                      ,
                              'isCompare'                     => $is_compare                  ,
                              'productImages'                 => $images_array                ,
                              'brandName'                     => $brand_name                  ,
                              'ratingTimes'                   => $product->rating_times       ,
                              'ratingAvg'                     => "$product->rating_avg"         ,
                              'availableProduct'              => $availability                        ,
                              'restQty'                       => $rest_qty                           ,
                              'pagesCount'                    => $pages_count
                            );

      }

      return $output;
  }


/************************************************************************/
}
