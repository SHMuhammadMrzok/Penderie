<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Item_product extends CI_Controller
{

    public $settings;
    public function __construct()
    {
        parent::__construct();

        $this->load->model('general_model');
        $this->load->model('users/countries_model');
        $this->load->model('products/products_model');
        $this->load->model('products/advantages_model');
        $this->load->model('products/products_tags_model');
        $this->load->model('products/site_products_model');
        $this->load->model('optional_fields/optional_fields_model');

        $this->load->library('api_lib');
        $this->load->library('currency');
        $this->load->library('products_lib');
        $this->load->library('shopping_cart');

        $this->settings = $this->general_model->get_settings();

    }

    public function index()
    {
        $lang_id        = intval($this->input->post('langId', TRUE));
        $country_id     = intval($this->input->post('countryId', TRUE));
        $email          = strip_tags($this->input->post('email', TRUE));
        $password       = strip_tags($this->input->post('password', TRUE));
        $deviceId       = strip_tags($this->input->post('deviceId', TRUE));
        $product_id     = intval($this->input->post('productId', TRUE));
        $ip_address     = $this->input->ip_address();

        $agent          = strip_tags($this->input->post('agent', TRUE));

        $images_path    = $this->api_lib->get_images_path();

        if($this->ion_auth->login($email, $password))
        {
            $user              = $this->ion_auth->user()->row();
            $user_id           = $user->id;
            $customer_group_id = $user->customer_group_id;

            $this->api_lib->check_user_store_country_id($email, $password, $user_id, $country_id);

            $is_logged         = 1;
            $customer_group_id = $user->customer_group_id;

        }
        else
        {
            $is_logged = 0;
            $user_id   = 0;
            $customer_group_id = 0;
        }

        $product        = $this->products_model->get_product_row_details($product_id, $lang_id, $country_id, $user_id);

        $output         = array();
        $fail_message   = $this->general_model->get_lang_var_translation('no_available_products', $lang_id);

        if(count($product) != 0)
        {


            $this->shopping_cart->set_user_data($user_id, $deviceId, $ip_address , $country_id ,$lang_id);

            $product_details    = $this->products_model->get_product_row_details($product_id, $lang_id, $country_id);
            $product_price_data = $this->products_lib->get_product_price_data($product_details, $country_id, $user_id, $deviceId);
            $currency           = $this->currency->get_country_currency_name($country_id, $lang_id);

            $product_price      = '';

            if($product_price_data[0] != $product_price_data[1])
            {
                $product_price = $product_price_data[0];
            }

            $product_new_price  = $product_price_data[1];

            $pic = '';
            $path = '';
            $location_city = '';
            $location_country = '';

            if(isset($product->image))
            {
                $pic =  $images_path.$product->image;
            }

            //$image_path = realpath(APPPATH. '../assets/uploads/products/'.$path.$product->image);
            //$image_code = $this->api_lib->get_image_code($image_path);
            $product_images  = $this->products_model->get_product_images($product_id);

            $images_array= array();

            if(count($product_images) != 0)
            {
                foreach($product_images as $image)
                {
                    $images_array[] = $images_path.$image->image;
                }
            }

            $product_optional_fields = $this->get_product_optional_fields($product_id, $lang_id, $customer_group_id);

            if(count($product_optional_fields) != 0)
            {
                $optional_fields = 1;
            }
            else
            {
                $optional_fields = 0;
            }
            $brand_name = '';
            if($product->brand_id != 0)
            {
                $brand_name  = $product->brand_name;
            }



            $is_cart    = $this->shopping_cart->check_product_in_cart($product->product_id);
            $is_fav     = $this->products_model->check_product_in_fav($product->product_id, $user_id);
            $is_compare = $this->products_model->check_product_in_compare($product->product_id, $user_id);

            $product_cat_specs = $this->products_model->get_product_cat_specs_data($product->product_id, $lang_id);
            $spec_array = array();
            foreach($product_cat_specs as $row)
            {
                $spec_array[] = array(
                                        $row->spec_label => $row->spec_value
                                    );
            }

            //related products

            $tags = array();
            $product_tags  =  $this->products_tags_model->get_product_tags($product_id, $lang_id);

            if(count($product_tags) != 0)
            {
                $tags_text = '';
                foreach($product_tags as $tag)
                {
                    $tags[]     = $tag->id;
                    $tags_text .= $tag->tag.' , ';
                }
            }

            $related_products_id = $this->site_products_model->get_related_products($product_id , $tags, $country_id, 15);
            $products_ids = array();
            foreach($related_products_id as $re_product_item)
            {
                $products_ids[] = $re_product_item->product_id;
            }

            $products_ids = array_unique($products_ids);

            //$products_limit = $this->settings->products_limit;

            $related_products = $this->get_products_list_array($products_ids, $product_id, $lang_id, $country_id, $user_id, $deviceId, $currency, $customer_group_id);

            $product_route = $this->settings->product_route;

            $availability = true;  // true means product has stock , false means product has no stock
            $rest_qty     = 0; // only rest (number) items of product "Message"

            if($product->quantity_per_serial == 1)
            {
                $product_qty   = $this->products_model->count_product_available_quantity($product_id, $country_id);
                $min_stock     = $this->settings->min_product_stock;
                $rest_qty      = $this->settings->rest_product_qty;
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

            $video = '';
            if($product->video != '')
            {
              $video = $images_path.$product->video;
            }

            $output = array(
                                'productId'                     => $product->product_id         ,
                                'categoryId'                    => $product->cat_id             ,
                                'categoryName'                  => $product->cat_name           ,
                                'productName'                   => $product->title              ,
                                'productPrice'                  => $product_price               ,
                                'productNewPrice'               => $product_new_price           ,
                                'productImage'                  => $pic                         ,
                                'productDescription'            => $product->description        ,
                                'producuctQuantityPerSerial'    => $product->quantity_per_serial,
                                'productCurrency'               => $currency                    ,
                                'new'                           => $product->new                ,
                                'views'                         => $product->view               ,
                                'optionalFieldsExist'           => $optional_fields             ,
                                'optionalFields'                => $product_optional_fields     ,
                                'storeName'                     => $product->store_name         ,
                                'storeId'                       => $product->store_id           ,
                                'locationCountryId'             => $product->location_country_id,
                                'locationCountry'               => $location_country            ,
                                'locationCityId'                => $product->location_city_id   ,
                                'locationCity'                  => $location_city               ,
                                'productImages'                 => $images_array                ,
                                'isCart'                        => $is_cart                     ,
                                'isFav'                         => $is_fav                      ,
                                'isCompare'                     => $is_compare                  ,
                                'specifications'                => $spec_array                  ,
                                'totalPoints'                   => $product->total_rating_points,
                                'ratingTimes'                   => $product->rating_times       ,
                                'ratingAvg'                     => $product->rating_avg         ,
                                'shareURL'                      => base_url().$product_route.$product->route,
                                'relatedProducts'               => $related_products,
                                'brandName'                     => $brand_name,
                                'brandId'                       => $product->brand_id,
                                'availableProduct'              => $availability                ,
                                'restQty'                       => $rest_qty                    ,
                                'code'                          => $product->code               ,
                                'youtubevideo'                  => $product->youtube_video,
                                'video'                         => $video                 ,
                                //'advantages'                    => $advs_array
                              );

            // update product views count
            $this->site_products_model->increment_product_view($product_id);

        }
        else
        {
            $output = array(
                                'message' => $fail_message,
                                'response' => 0
                           );
        }

        //***************LOG DATA***************//
        //insert log
        $this->api_lib->insert_log($user_id, current_url(), 'Product Details', $agent, $_POST, $output);
        //***************END LOG***************//
        
        $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));


    }

    public function get_product_optional_fields($product_id, $lang_id, $customer_group_id)
    {
        $product_optional_fields = $this->products_model->get_product_optional_fields($product_id, $lang_id);
        $images_path = $this->api_lib->get_images_path();


        if(count($product_optional_fields) != 0)
        {
            $options_array = array();

            foreach ($product_optional_fields as $field)
            {
                $option_options_array = array();

                if($field->has_options == 1)
                {
                    $option_options   = $this->optional_fields_model->get_optional_field_options($field->optional_field_id, $lang_id, $product_id, 1, array(), array(), $field->free);//get_optional_field_options($field->optional_field_id, $lang_id);

                    foreach ($option_options as $option)
                    {
                        $image = '';
                        if($option->image != '')
                        {
                            $image = $images_path.$option->image;
                        }

                        $option_price = $option->cost;
                        $conds = array(
                                        'customer_group_id' => $customer_group_id,
                                        'option_id'         => $option->id,
                                        'optional_field_id' => $option->optional_field_id,
                                        'product_id'        => $product_id
                                      );

                        $customer_group_price = $this->products_model->get_table_data('optional_fields_customer_groups_prices', $conds, 'row');

                        if(count($customer_group_price) != 0)
                        {
                            if($customer_group_price->group_price != 0)
                            {
                                $option_price = $customer_group_price->group_price;
                            }
                        }

                        if(is_null($option_price))
                        {
                          $option_price = "0";
                        }

                        $option_options_array[] = array(
                                                         'optionId'    => $option->id         ,
                                                         'optionLabel' => $option->field_value,
                                                         'cost'        => $option_price       ,
                                                         'image'       => $image
                                                       );
                    }
                }


                $options_array[] = array(
                                            'id'              => $field->id,
                                            'optionalFieldId' => $field->optional_field_id,
                                            'required'        => $field->required,
                                            'label'           => $field->label,
                                            'fieldTypeId'     => $field->field_type_id,
                                            'defaultValue'    => $field->default_value,
                                            'options'         => $option_options_array,

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


    public function get_products_list_array($products_array, $main_product_id, $lang_id, $country_id, $user_id, $deviceId, $currency, $customer_group_id)
    {
        $result_array = array();
        $images_path = $this->api_lib->get_images_path();

        //echo $products_limit;die();
        foreach($products_array as $product_id)
        {
            $product_details    = $this->products_model->get_product_row_details($product_id, $lang_id, $country_id);
            //print_r($product_details);
            $product_price_data = $this->products_lib->get_product_price_data($product_details, $country_id, $user_id, $deviceId);

            $product_price      = '';

            if($product_price_data['product_price_before'] != $product_price_data['product_price'])
            {
                $product_price = $product_price_data['product_price_before'];
            }

            $product_new_price  = $product_price_data['product_price'];

            $re_pic = '';

            if(isset($product_details->image))
            {
                $re_pic =  $images_path.$product_details->image;
            }

            $product_optional_fields = $this->get_product_optional_fields($product_id, $lang_id, $customer_group_id);

            if($product_optional_fields != '')
            {
                $optional_fields = 1;
            }
            else
            {
                $optional_fields = 0;
            }

            $product_images  = $this->products_model->get_product_images($product_details->product_id);
            $images_array= array();
            //$amazon_path = "https://maskaninet.s3.eu-west-2.amazonaws.com/goldy/";

            if(count($product_images) != 0)
            {
                foreach($product_images as $image)
                {
                    $images_array[] = $images_path.$image->image;
                }
            }

            $brand_name = '';
            if($product_details->brand_id != 0)
            {
                $brand_name  = $product_details->brand_name;
            }

            $result_array[] = array(
                                        'mainProductId'                 => $main_product_id             ,
                                        'productId'                     => $product_details->product_id ,
                                        'categoryId'                    => $product_details->cat_id     ,
                                        'productName'                   => $product_details->title      ,
                                        'productPrice'                  => $product_price               ,
                                        'productNewPrice'               => $product_new_price           ,
                                        'productImage'                  => $re_pic                      ,
                                        'productDescription'            => $product_details->description,
                                        'optionalFieldsExist'           => $optional_fields             ,
                                        'producuctQuantityPerSerial'    => $product_details->quantity_per_serial,
                                        'productCurrency'               => $currency,
                                        'new'                           => $product_details->new        ,
                                        'productImages'                 => $images_array                ,
                                        'brandName'                     => $brand_name                  ,
                                        'totalPoints'                   => $product_details->total_rating_points,
                                        'ratingTimes'                   => $product_details->rating_times       ,
                                        'ratingAvg'                     => $product_details->rating_avg         ,
                                       );

        }

        return $result_array;
    }

    public function add_rate()
    {

        // $product_id     = 266;//intval($this->input->post('productId', true));
        // $rating_points  = 5;//intval($this->input->post('ratingPoints', true));
        // $email          = 'mariam@shourasoft.com';//strip_tags($this->input->post('email', true));
        // $password       = 12345678;//strip_tags($this->input->post('password', true));
        // $country_id     = 2;//strip_tags($this->input->post('countryId', true));
        // $lang_id        = 2;//intval($this->input->post('langId', true));
        // $rating_comment = 'good product';//strip_tags($this->input->post('ratingComment', true));

        $product_id     = intval($this->input->post('productId', true));
        $rating_points  = intval($this->input->post('ratingPoints', true));
        $email          = strip_tags($this->input->post('email', true));
        $password       = strip_tags($this->input->post('password', true));
        $country_id     = strip_tags($this->input->post('countryId', true));
        $lang_id        = intval($this->input->post('langId', true));
        $rating_comment = strip_tags($this->input->post('ratingComment', true));

        $agent          = strip_tags($this->input->post('agent', TRUE));
        $user_id        = 0;

        if($this->ion_auth->login($email, $password))
        {
            $user              = $this->ion_auth->user()->row();
            $user_id           = $user->id;

            $this->api_lib->check_user_store_country_id($email, $password, $user_id, $country_id);

            $is_logged         = 1;
            $customer_group_id = $user->customer_group_id;

            // check user rate before
            $user_product_rate_count = $this->site_products_model->check_user_product_rate($product_id, $user_id);

            if($user_product_rate_count == 0)
            {
                $product_data = $this->site_products_model->get_product_view($product_id);

                if(count($product_data) != 0)
                {
                    // insert rate row
                    $rate_data = array(
                                        'product_id' => $product_id,
                                        'rate'          => $rating_points,
                                        'user_id'       => $user_id,
                                        'agent'         => $_SERVER['HTTP_USER_AGENT'],
                                        'ip_address'    => $_SERVER['REMOTE_ADDR'],
                                        'unixtime'      => time()
                                      );
                    $this->site_products_model->insert_product_rate($rate_data);

                    // update product rating


                    $rating_times = isset($product_data->rating_times) ? $product_data->rating_times : 0;
                    $new_rating_times   = $rating_times + 1;
                    $total_points       = $this->site_products_model->get_product_total_points($product_id);
                    $rating_avg         = round($total_points / $new_rating_times, 1);

                    $product_data = array(
                                            'total_rating_points' => $total_points,
                                            'rating_times'        => $new_rating_times,
                                            'rating_avg'          => $rating_avg
                                         );

                    $this->site_products_model->update_product($product_id, $product_data);

                    //insert product comment
                    if($rating_comment != '')
                    {
                      if($this->settings->auto_publish_comment == 1)
                      {
                          $approved = 1;
                      }
                      else
                      {
                          $approved = 0;
                      }

                      $comment_data = array(
                                              'product_id' => $product_id,
                                              'user_id'    => $user_id,
                                              'username'   => $user->first_name.' '.$user->last_name,
                                              'comment'    => $rating_comment,
                                              'approved'   => $approved,
                                              'unix_time'  => time()
                                          );

                      $this->site_products_model->insert_product_comment($comment_data);

                    }


                    $message = $this->general_model->get_lang_var_translation('rate_added_successfully', $lang_id);
                    $output = array(
                                        'message' => $message,
                                        'response' => 1
                                   );
                }
                else
                {
                    $fail_message   = $this->general_model->get_lang_var_translation('no_available_products', $lang_id);
                    $output = array(
                                        'message' => $fail_message,
                                        'response' => 0
                                   );
                }



            }
            else
            {
                $fail_message = $this->general_model->get_lang_var_translation('you_rated_product_before', $lang_id);//rated_before
                $output = array(
                                    'message' => $fail_message,
                                    'response' => 0
                               );
            }

        }
        else
        {
            $fail_message = $this->general_model->get_lang_var_translation('login_error', $lang_id);//login failed
            $output = array(
                                'message' => $fail_message,
                                'response' => 0
                           );
        }


        //***************LOG DATA***************//
        //insert log
        $this->api_lib->insert_log($user_id, current_url(), 'Add Product Rate', $agent, $_POST, $output);
        //***************END LOG***************//

        $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));
    }

/************************************************************************/
}
