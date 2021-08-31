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
        //$price_filter           = intval($this->input->post('priceFilter', TRUE));
        $price_filter = 0;
        $price_from             = intval($this->input->post('priceFrom', TRUE));
        $price_to               = intval($this->input->post('priceTo', TRUE));

        $optional_fields_filter = $this->input->post('optionalFieldsFilter', TRUE);
        $optional_fields_filter = json_decode($optional_fields_filter);
        $cats_filter            = $this->input->post('catIdsFilter', TRUE);
        $cats_filter            = json_decode($cats_filter);
        $rating_filter          = $this->input->post('rating', true);

        $output       = array();
        $spammed_users_array = array();

        /****************pagination**************/
        if(!$page) $page = 1;
        $limit           = 25;
        $offset          = ($page -1)*$limit;

        $cat_data        = $this->cat_model->get_category_row($category_id);

        if(count($cat_data) == 0)
        {
            $parent_id = 0;
        }
        else
        {
            $parent_id = $cat_data->parent_id;
        }

        if($this->ion_auth->login($email, $password))
        {
            $user              = $this->ion_auth->user()->row();
            $user_id           = $user->id;

            $this->api_lib->check_user_store_country_id($email, $password, $user_id, $country_id);

            $user              = $this->ion_auth->user()->row();
            $is_logged         = 1;
            $customer_group_id = $user->customer_group_id;

            // check user spammed users
            $spammed_users = $this->products_model->get_user_spammed_users($user_id);

            if(count($spammed_users) != 0)
            {
                foreach($spammed_users as $row)
                {
                    $spammed_users_array[] = $row->blocked_user_id;
                }
            }
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
        else
        {
            if($category_id != 0)
            {
                $sub_cats_ids = $this->site_products_model->get_category_sub_cats_ids($category_id);
            }
            else
            {
                // get all sub categories
                $sub_cats_ids = $this->cat_model->get_sub_cats_ids();
            }

            if(count($sub_cats_ids) != 0)
            {
                foreach($sub_cats_ids as $id)
                {
                    $ids_array[] = $id->id;
                }
            }
            else
            {
                $fail_message   = $this->general_model->get_lang_var_translation('no_available_products',$lang_id);
                $output         = array(
                                            'message'   => $fail_message,
                                            'response'  => 0
                                       );
            }
        }

        if(count($ids_array) != 0)
        {
          if(count($cats_filter) != 0)
          {
            $ids_array = $cats_filter;
          }

          $conds = array();

          if($price_from != 0)
          {
            $conds['products_countries.price > '] = $price_from;
          }

          if($price_to != 0)
          {
            $conds['products_countries.price < '] = $price_to;
          }

          if($brand_id != 0)
          {
            $conds['products.brand_id'] = $brand_id;
          }

          $products = $this->site_products_model->get_cats_products($ids_array, $lang_id, $country_id, $limit, $offset, $store_id, 0,$price_filter,$rating_filter, $conds, $user_id, $spammed_users_array, $optional_fields_filter);
          $all_products_count = $this->site_products_model->get_cats_products_count($ids_array, $lang_id, $country_id, $store_id, 0,$price_filter,$rating_filter, $conds, $user_id, $spammed_users_array, $optional_fields_filter);
          $pages_count = ceil($all_products_count/$limit);

          $this->shopping_cart->set_user_data($user_id, $deviceId, $ip_address , $country_id ,$lang_id);

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

        }
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
      $currency = $this->currency->get_country_currency_name($country_id, $lang_id);
      $settings = $this->general_model->get_settings();

      $images_path    = $this->api_lib->get_images_path();


      foreach($products as $product)
      {
          $product_details    = $this->products_model->get_product_row_details($product->id, $lang_id, $country_id);
          $product_price_data = $this->products_lib->get_product_price_data($product_details, $country_id, $user_id, $deviceId);

          $product_price      = '';

          if($product_price_data[0] != $product_price_data[1])
          {
              $product_price = $product_price_data[0];
          }

          $product_new_price  = $product_price_data[1];

          $pic = '';

          if(isset($product->image))
          {
              //$pic =  base_url().'assets/uploads/products/thumb150.jpg';
              //$pic =  base_url().'assets/uploads/products/'.$product->image;
              /*
              $pic =  base_url().'assets/uploads/products/250x275/250x275_'.$product->image;
              $thumb_name = base_url().'assets/uploads/products/50x55/50x55_'.$product->image;
              */
              //$pic =  $amazon_path.'products/250x275/250x275_'.$product->image;
              //$thumb_name = $amazon_path.'products/50x55/50x55_'.$product->image;
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



          $vat_val    = $product_price_data['vat_value'];
          $vat_percent = $product_price_data['vat_percent'];
          $is_cart    = $this->shopping_cart->check_product_in_cart($product->product_id);
          $is_fav     = $this->products_model->check_product_in_fav($product->product_id, $user_id);
          $is_compare = $this->products_model->check_product_in_compare($product->product_id, $user_id);

          $product_images  = $this->products_model->get_product_images($product->product_id);
          $images_array= array();

          if(count($product_images) != 0)
          {
              foreach($product_images as $image)
              {
                //   $images_array[] = base_url().'assets/uploads/products/'.$image->image;
                  $images_array[] = $images_path.$image->image;
              }
          }

          $brand_name = '';
          if($product->brand_id != 0)
          {
              $brand_name  = $product_details->brand_name;
          }



          $output[] = array(
                              'productId'                     => $product->product_id         ,
                              'categoryId'                    => $product->cat_id             ,
                              'productName'                   => $product->title              ,
                              'storeName'                     => $product->store_name         ,
                              'productPrice'                  => $product_price               ,
                              'productNewPrice'               => $product_new_price           ,
                              'vatValue'                      => "$vat_val",
                              'vatPercent'                    => "$vat_percent",
                              'productImage'                  => $pic                         ,
                              'productImageThumb'             => $thumb_name                  ,
                              'productDescription'            => $product->description        ,
                              'producuctQuantityPerSerial'    => $product->quantity_per_serial,
                              'productCurrency'               => $currency                    ,
                              'productOptionalFields'         => $product_optional_fields     ,
                              'optionalFieldsExist'           => $optional_fields             ,
                              //'new'                           => $product->new                ,
                              'views'                         => $product->view               ,
                              'isCart'                        => $is_cart                     ,
                              'isFav'                         => $is_fav                      ,
                              'isCompare'                     => $is_compare                  ,
                              'productImages'                 => $images_array                ,
                              'brandName'                     => $brand_name                  ,
                              'totalPoints'                   => $product_details->total_rating_points,
                              'ratingTimes'                   => $product_details->rating_times       ,
                              'ratingAvg'                     => "$product_details->rating_avg"         ,
                              'availableProduct'              => $availability                        ,
                              'restQty'                       => $rest_qty                           ,
                              'pagesCount'                    => $pages_count
                            );



      }

      return $output;
    }


/************************************************************************/
}
