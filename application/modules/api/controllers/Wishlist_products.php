<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Wishlist_products extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('general_model');
        $this->load->model('users/countries_model');
        $this->load->model('products/products_model');
        $this->load->model('products/site_products_model');
        $this->load->model('optional_fields/optional_fields_model');

        $this->load->library('api_lib');
        $this->load->library('currency');
        $this->load->library('products_lib');


    }

    public function add_to_wishlist()
    {
        $lang_id        = intval($this->input->post('langId', TRUE));
        $email          = strip_tags($this->input->post('email', TRUE));
        $password       = strip_tags($this->input->post('password', TRUE));
        $deviceId       = strip_tags($this->input->post('deviceId', TRUE));
        $product_id     = intval($this->input->post('productId', TRUE));

        if(!$this->ion_auth->login($email, $password))
        {
            $login_error_message = $this->general_model->get_lang_var_translation('login_error', $lang_id);

            $output = array(
                               'response' => "0",
                               'message'  => $login_error_message
                           );
        }
        else
        {
            $user_id = $this->ion_auth->user()->row()->id;

            // check if product exist in user wishlist
            $product_exist = $this->site_products_model->check_product_in_wishlist($product_id, $user_id);

            if($product_exist)
            {
                $error_msg = $this->general_model->get_lang_var_translation('product_exist_in_wishlist', $lang_id);

                $output = array(
                                   'response' => "0",
                                   'message'  => $error_msg
                               );
            }
            else
            {
                //insert wishlist product
                $data = array(
                                'user_id'    => $user_id,
                                'product_id' => $product_id,
                                'unixtime'   => time()
                             );

                $this->site_products_model->insert_wishlist_product($data);

                $message = $this->general_model->get_lang_var_translation('added_to_wishlist_successfully', $lang_id);

                $output = array(
                                   'response' => "1",
                                   'message'  => $message
                               );

            }
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));
    }

    public function user_wishlist()
    {
        $lang_id    = intval($this->input->post('langId', TRUE));
        $country_id = intval($this->input->post('countryId', TRUE));
        $email      = strip_tags($this->input->post('email', TRUE));
        $password   = strip_tags($this->input->post('password', TRUE));
        $deviceId   = strip_tags($this->input->post('deviceId', TRUE));
        $page       = intval($this->input->post('page', true));

        $settings   = $this->general_model->get_settings();
        $images_path = $this->api_lib->get_images_path();

        if(!$this->ion_auth->login($email, $password))
        {
            $login_error_message = $this->general_model->get_lang_var_translation('login_error', $lang_id);

            $output = array(
                               'response' => "0",
                               'message'  => $login_error_message
                           );
        }
        else
        {
            $user_id = $this->ion_auth->user()->row()->id;

            if($page == 0) $page = 1;
            $limit  = 8;
            $offset = ($page - 1) * $limit;

            $products_array = $this->site_products_model->get_user_wishlist_products(0, $lang_id, $country_id, $user_id, $limit, $offset);

            if(count($products_array) == 0)
            {
                $fail_message   = $this->general_model->get_lang_var_translation('no_available_products',$lang_id);
                $output         = array(
                                            'message'   => $fail_message,
                                            'response'  => 0
                                       );
            }
            else
            {
                foreach($products_array as $product)
                {
                    $product_details    = $this->products_model->get_product_row_details($product->product_id, $lang_id, $country_id);
                    $product_price_data = $this->products_lib->get_product_price_data($product_details, $country_id, $user_id, $deviceId);
                    $currency           = $this->currency->get_country_currency_name($country_id, $lang_id);

                    $product_price      = '';

                    if($product_price_data[0] != $product_price_data[1])
                    {
                        $product_price = $product_price_data[0];
                    }

                    $product_new_price  = $product_price_data[1];


                    $product_optional_fields = $this->get_product_optional_fields($product->product_id, $lang_id);

                    if(count($product_optional_fields) != 0)
                    {
                        $optional_fields = 1;
                    }
                    else
                    {
                        $optional_fields = 0;
                    }


                    $image_path  = $images_path.$product->image;;
                    $image_thumb = $images_path.$product->image;

                    $vat_val     = $product_price_data['vat_value'];
                    $vat_percent = $product_price_data['vat_percent'];

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
                        $brand_name  = $product_details->brand_name;
                    }

                    $availability = true;  // true means product has stock , false means product has no stock
                    $rest_qty     = 0; // only rest (number) items of product "Message"

                    if($product_array->quantity_per_serial == 1)
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
                          $rest_qty   = $available_qty;  // same as stock qty
                        }

                    }
                    else
                    {
                        $availability = true;
                    }

                    $output[] = array(
                                        'productId'                     => $product->product_id         ,
                                        'categoryId'                    => $product->cat_id             ,
                                        'productName'                   => $product->title              ,
                                        'productPrice'                  => $product_price               ,
                                        'productNewPrice'               => $product_new_price           ,
                                        'vatValue'                      => "$vat_val"                   ,
                                        'vatPercent'                    => "$vat_percent"               ,
                                        'productImage'                  => $image_path                  ,
                                        'productDescription'            => $product->description        ,
                                        'producuctQuantityPerSerial'    => $product->quantity_per_serial,
                                        'productCurrency'               => $currency                    ,
                                        'productOptionalFields'         => $product_optional_fields     ,
                                        'optionalFieldsExist'           => $optional_fields             ,
                                        'thumbnail'                     => $image_thumb                 ,
                                        'storeName'                     => $product->store_name         ,
                                        'new'                           => $product->new                ,
                                        'views'                         => $product->view               ,
                                        'productImages'                 => $images_array                ,
                                        'brandName'                     => $brand_name                  ,
                                        'totalPoints'                   => $product_details->total_rating_points,
                                        'ratingTimes'                   => $product_details->rating_times       ,
                                        'ratingAvg'                     => $product_details->rating_avg         ,
                                        'availableProduct'              => $availability                        ,
                                        'restQty'                       => $rest_qty                            ,
                                        'pagesCount'                    => $pages_count
                                      );
                }
            }


        }

        $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));
    }

    public function remove_from_wishlist()
    {
        $lang_id        = intval($this->input->post('langId', TRUE));
        $email          = strip_tags($this->input->post('email', TRUE));
        $password       = strip_tags($this->input->post('password', TRUE));
        $deviceId       = strip_tags($this->input->post('deviceId', TRUE));
        $product_id     = intval($this->input->post('productId', TRUE));

        if(!$this->ion_auth->login($email, $password))
        {
            $login_error_message = $this->general_model->get_lang_var_translation('login_error', $lang_id);

            $output = array(
                               'response' => "0",
                               'message'  => $login_error_message
                           );
        }
        else
        {
            $user_id = $this->ion_auth->user()->row()->id;

            // check if product exist in user wishlist
            $product_exist = $this->site_products_model->check_product_in_wishlist($product_id, $user_id);

            if($product_exist)
            {
                $this->site_products_model->remove_wishlist_product($product_id, $user_id);

                $message = $this->general_model->get_lang_var_translation('product_removed_successfully_from_wishlist', $lang_id);
                $output  = array(
                                   'response' => "1",
                                   'message'  => $message
                               );
            }
            else
            {
                $message = $this->general_model->get_lang_var_translation('product_not_exist_in_wishlist', $lang_id);
                $output  = array(
                                   'response' => "0",
                                   'message'  => $message
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


}
