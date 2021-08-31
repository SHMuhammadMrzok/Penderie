<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Products extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        require(APPPATH . 'includes/front_end_global.php');

        $this->load->model('optional_fields/optional_fields_model');
        $this->load->model('categories/categories_tags_model');
        $this->load->model('shipping/costs_model');
        $this->load->model('site_products_model');
        $this->load->model('products_tags_model');
        $this->load->model('products_model');

        $this->load->library('products_lib');
        $this->load->library('pagination');

        $all_products_count = $this->site_products_model->get_cats_products_count(array(), $this->data['lang_id'], $this->data['country_id']);
        $currency           = $this->currency->get_country_currency_name($this->data['country_id'], $this->data['lang_id']);
        $this->data['all_products_count'] = $all_products_count;
        $this->data['currency'] = $currency;
    }

    var $data = array();

    public function store_products($store_id)
    {
        $this->session->set_userdata('site_redir', current_url());

        $m_cats_array = array();
        $store_id   = intval($store_id);
        $m_products_new_array = array();

        $lang_id      = $this->data['lang_id'];
        $country_id   = $this->data['country_id'];

        if(isset($_GET['sort']))
        {
            $sort = intval($this->input->get('sort', true));
            $this->data['sort'] = $sort;
        }
        else
        {
            $sort = 0;
        }

        if(isset($_GET['rating_filter']))
        {
            $rating_filter = intval($this->input->get('rating_filter', true));

            $this->data['rating_filter'] = $rating_filter;
        }
        else
        {
            $rating_filter = 0;
        }

        $brands_filter = array();
        $brands_filter_data = array();
        if(isset($_GET['brands_filter']) && $_GET['brands_filter'] !=0 )
        {
            $brands_filter = $this->input->get('brands_filter', true);
            $brands_filter = explode(',', $brands_filter);
            $brands_filter_data = $this->brands_model->get_all_brands($this->data['lang_id'], array(), $brands_filter);
        }

        $this->data['brand_filter'] = array(
          'ids'   => $brands_filter     ,
          'data'  => $brands_filter_data
        );

        $price_from = 0;
        if(isset($_GET['price_from']) && $_GET['price_from'] != '')
        {
          $price_from = intval($this->input->get('price_from', true));
          $this->data['price_from'] = $price_from;
        }

        $price_to = 0;
        if(isset($_GET['price_to']) && $_GET['price_to'] != '')
        {
          $price_to = intval($this->input->get('price_to', true));
          $this->data['price_to'] = $price_to;
        }

        $m_products_array       = $this->site_products_model->get_store_products($store_id, $lang_id, $country_id, $sort, 0, 0, $brands_filter, $rating_filter, $price_from, $price_to);
        $products_data_array    = $this->_get_products_prices($m_products_array, $sort, 0, 0, 0, 1);
        $m_products_new_array   = $products_data_array[0];
        $new_used_products_count = $products_data_array[1];
        $m_store_data           = $this->stores_model->get_row_data($store_id, $lang_id);
        $m_store_cats           = $this->stores_model->get_store_available_cats_data($store_id, $lang_id);
        $store_brands           = $this->site_products_model->get_store_brands($store_id, $this->data['lang_id']);


        if(count($m_products_new_array) == 0)
        {
            $this->data['error_msg'] = lang('no_available_products');
        }

        foreach($m_store_cats as $cat)
        {
            $m_cats_array[$cat->parent_id][] = $cat;
        }

        $this->data['cat_products'] = $m_products_new_array;
        $this->data['products_count'] = count($m_products_new_array);
        $this->data['cat_brands']   = $store_brands;
        $this->data['store_data']   = $m_store_data;
        $this->data['s_cats']       = $m_cats_array;
        $this->data['sorting']      = true;
        $this->data['page_title']   = $m_store_data->name;
        $this->data['new_used_products_count'] = $new_used_products_count;

        $this->data['content'] = $this->load->view('site_products', $this->data, true);
        $this->load->view('site/main_frame',$this->data);
    }


    public function index($cat='', $store_id=0)
    {
        #SUB CATEGORY PRODUCTS

        $this->session->set_userdata('site_redir', current_url());

        $cat        = strip_tags($cat);
        $store_id   = intval($store_id);

        $products_new_array = array();

        $lang_id      = $this->data['lang_id'];
        $country_id   = $this->data['country_id'];

        $cat_data     = $this->cat_model->get_cat_by_route($cat);
        $cat_id       = $cat_data->id;

        if(isset($_GET['sort']))
        {
            $sort = intval($this->input->get('sort', true));
            $this->data['sort'] = $sort;
        }
        else
        {
            $sort = 0;
        }

        if(isset($_GET['price_filter']))
        {
            /**
             * Price Filter Vals
             * 1 => less than 100
             * 2 => between 100 and 200
             * 3 => between 200 and 300
             * 4 => more than 300
             */

            $price_filter = intval($this->input->get('price_filter', true));

            $this->data['price_filter'] = $price_filter;
        }
        else
        {
            $price_filter = 0;
        }

        if(isset($_GET['rating_filter']))
        {
            $rating_filter = intval($this->input->get('rating_filter', true));

            $this->data['rating_filter'] = $rating_filter;
        }
        else
        {
            $rating_filter = 0;
        }

        $brands_filter = array();
        $brands_filter_data = array();
        if(isset($_GET['brands_filter']) && $_GET['brands_filter'] !=0 )
        {
            $brands_filter = $this->input->get('brands_filter', true);
            $brands_filter = explode(',', $brands_filter);
            $brands_filter_data = $this->brands_model->get_all_brands($this->data['lang_id'], array(), $brands_filter);
        }

        $this->data['brand_filter'] = array(
          'ids'   => $brands_filter     ,
          'data'  => $brands_filter_data
        );

        $price_from = 0;
        if(isset($_GET['price_from']) && $_GET['price_from'] != '')
        {
          $price_from = intval($this->input->get('price_from', true));
          $this->data['price_from'] = $price_from;
        }

        $price_to = 0;
        if(isset($_GET['price_to']) && $_GET['price_to'] != '')
        {
          $price_to = intval($this->input->get('price_to', true));
          $this->data['price_to'] = $price_to;
        }

        $condtion_filter = 0;
        if(isset($_GET['condition_filter']) && $_GET['condition_filter'] != 0)
        {
          $condtion_filter = intval($this->input->get('condition_filter', true));
        }

        $this->data['condition_filter'] = $condtion_filter;

        $currency             = $this->currency->get_country_currency_name($this->data['country_id'], $this->data['lang_id']);
        $products_array       = $this->site_products_model->get_cat_products($cat_id, $lang_id, $country_id, $store_id, $sort, $price_filter, $rating_filter, $brands_filter, $price_from, $price_to, $condtion_filter);
        $cat_meta_tags        = $this->categories_tags_model->get_cat_tags($cat_id, $lang_id);
        $cat_data             = $this->cat_model->get_row_data($cat_id, $lang_id);
        $cat_meta_description = $cat_data->meta_tag_description;
        $cat_brands           = $this->site_products_model->get_category_brands($cat_id, $this->data['lang_id']);
        $products_data_array  = $this->_get_products_prices($products_array, $sort, 0, 0, 0, 1);

        $products_new_array   = $products_data_array[0];
        $new_used_products_count = $products_data_array[1];

        if(count($products_new_array) == 0)
        {
            $this->data['error_msg'] = lang('no_available_products');
        }
        $tags ='';
        foreach($cat_meta_tags as $tag)
        {
            $tags .= $tag->tag.' , ';
        }

        if($cat_data->parent_id != 0)
        {
            $parent_data  = $this->cat_model->get_row_data($cat_data->parent_id, $lang_id);
            $this->data['parent_cat_data']  = $parent_data;
            $this->data['products_count']   = count($products_new_array);
        }
        $this->data['cat_products']         = $products_new_array;
        $this->data['meta_keywords']        = $tags;
        $this->data['meta_description']     = $cat_meta_description;
        $this->data['page_title']           = $cat_data->meta_title;
        $this->data['cat_data']             = $cat_data;
        $this->data['sorting']              = $sort;
        $this->data['cat_brands']           = $cat_brands;
        $this->data['new_used_products_count'] = $new_used_products_count;
        $this->data['currency']               = $currency;


        $this->data['content']  = $this->load->view('site_products', $this->data, true);
        $this->load->view('site/main_frame',$this->data);
    }

    public function all_offers($page=1, $parent_cat_id=0)
    {
        #ALL OFFERS PRODUCTS
        $this->session->set_userdata('site_redir', current_url());

        $limit  = 8;
        $offset = ($page -1) * $limit;


        $store_id   = 0;//intval($store_id);

        $products_new_array = array();

        $lang_id      = $this->data['lang_id'];
        $country_id   = $this->data['country_id'];


        if(isset($_GET['sort']))
        {
            $sort = intval($this->input->get('sort', true));
            $this->data['sort'] = $sort;
        }
        else
        {
            $sort = 0;
        }

        $price_from = 0;
        if(isset($_GET['price_from']) && $_GET['price_from'] != '')
        {
          $price_from = intval($this->input->get('price_from', true));
          $this->data['price_from'] = $price_from;
        }

        $price_to = 0;
        if(isset($_GET['price_to']) && $_GET['price_to'] != '')
        {
          $price_to = intval($this->input->get('price_to', true));
          $this->data['price_to'] = $price_to;
        }

        if(isset($_GET['rating_filter']))
        {
            $rating_filter = intval($this->input->get('rating_filter', true));

            $this->data['rating_filter'] = $rating_filter;
        }
        else
        {
            $rating_filter = 0;
        }

        $brands_filter = array();
        $brands_filter_data = array();
        if(isset($_GET['brands_filter']) && $_GET['brands_filter'] !=0 )
        {
            $brands_filter = $this->input->get('brands_filter', true);
            $brands_filter = explode(',', $brands_filter);
            $brands_filter_data = $this->brands_model->get_all_brands($this->data['lang_id'], array(), $brands_filter);
        }

        $this->data['brand_filter'] = array(
          'ids'   => $brands_filter     ,
          'data'  => $brands_filter_data
        );

        $products_array          = $this->site_products_model->get_all_offers_products($lang_id, $country_id, $store_id, $sort, $rating_filter, $limit, $offset, $parent_cat_id, $brands_filter, $price_from, $price_to);
        $products_count          = $this->site_products_model->count_all_offers_products($lang_id, $country_id, $store_id, $rating_filter, $parent_cat_id, $brands_filter, $price_from, $price_to);
        $products_array_data     = $this->_get_products_prices($products_array, $sort, 0, 1, 0, 1);

        $products_new_array      = $products_array_data[0];
        $new_used_products_count = $products_array_data[1];

        $offer_brands       = $this->site_products_model->get_offer_brands($this->data['lang_id'], $this->data['country_id']);

        if(count($products_new_array) == 0)
        {
            $this->data['error_msg'] = lang('no_available_products');
        }

        $this->data['cat_products'] = $products_new_array;
        $this->data['page_title']   = lang('all_offers');
        $this->data['sorting']      = $sort;

        $config['base_url']         = base_url().'products/products/all_offers/';
        $config['total_rows']       = $products_count;
        $config['per_page']         = $limit;
        $config['uri_segment']      = 4;
        $config['use_page_numbers'] = TRUE;
        $config['first_link']       = '<<';
        $config['last_link']        = '>>';
        $config['first_tag_open']   = '<li>';
        $config['first_tag_close']  = '</li>';
        $config['last_tag_open']    = '<li>';
        $config['last_tag_close']   = '</li>';
        $config['next_tag_open']    = '<li>';
        $config['next_tag_close']   = '</li>';
        $config['prev_tag_open']    = '<li>';
        $config['prev_tag_close']   = '</li>';
        $config['num_tag_open']     = '<li>';
        $config['num_tag_close']    = '</li>';
        $config['cur_tag_open']     = '<li class="active"><span>';
        $config['cur_tag_close']    = '</span></li>';
        $config['display_pages']    = TRUE;

        $this->pagination->initialize($config);
        $this->data['page_links']     = $this->pagination->create_links();
        $this->data['products_count'] = $products_count;
        $this->data['cat_brands']     = $offer_brands;
        $this->data['new_used_products_count'] = $new_used_products_count;

        $this->data['content']  = $this->load->view('site_products', $this->data, true);
        $this->load->view('site/main_frame',$this->data);
    }

    public function product($route='')
    {
        $this->session->set_userdata('site_redir', current_url());
        $route = strip_tags($route);
        if($route)
        {
            $product_array = array();
            $products      = array();
            $tags          = array();
            $products_ids  = array();

            $tags_text  = '';

            $lang_id    = $this->data['lang_id'];
            $country_id = $this->data['country_id'];

            //-->> product view code
            $product_data  = $this->products_model->get_prduct_data_by_route($route);
            $id            = $product_data->id;

            $this->site_products_model->increment_product_view($id);

            $product_array = $this->site_products_model->get_product($id, $lang_id, $country_id);

            if($product_array)
            {
                if($product_array->quantity_per_serial == 1)
                {
                    $product_qty   = $this->products_model->count_product_available_quantity($id, $country_id);
                    $min_stock     = $this->config->item('min_product_stock');
                    $available_qty = $product_qty - $min_stock;

                    $product_qty   = $this->products_model->count_product_available_quantity($id, $this->data['country_id']);
                    $min_stock     = $this->config->item('min_product_stock');
                    $rest_qty      = $this->config->item('rest_product_qty');
                    $available_qty = $product_qty - $min_stock;

                    if($product_qty == 0)
                    {
                      $product_array->{'no_stock'} = true;
                    }
                    if($available_qty <= $rest_qty && $product_qty!=0)
                    {
                      $stock_qty = $available_qty;
                      $product_array->{'rest_qty'} = $stock_qty;
                    }

                    if($available_qty > 0)
                    {
                        $availability = lang('available');
                    }
                    else
                    {
                        $availability = lang('unavailable');
                    }
                }
                else
                {
                    $availability = lang('available');
                }

                $product_array->{'availability'} = $availability;

                $product_tags  =  $this->products_tags_model->get_product_tags($id, $lang_id);

                $product_details         = $this->products_model->get_product_row_details($id, $this->data['lang_id'], $this->data['country_id']);
                $product_price_data      = $this->products_lib->get_product_price_data($product_details);
                $product_cat_specs       = $this->products_model->get_product_cat_specs_data($id, $lang_id);
                $product_optional_fields = $this->products_model->get_product_optional_fields($id, $this->data['lang_id']);
                $product_images          = $this->products_model->get_product_images($id);
                $cat_data                = $this->cat_model->get_row_data($product_details->cat_id, $this->data['lang_id']);
                $parent_data             = $this->cat_model->get_parent_data($cat_data->parent_id, $this->data['lang_id']);
                $product_comments        = $this->site_products_model->get_product_comments($id);
                $shipping_cities         = $this->costs_model->get_shipping_cities($this->data['country_id'], $this->data['lang_id']);
                $cities_array = array();
                foreach($shipping_cities as $city)
                {
                  $cities_array[$city->id] = $city->name;
                }

                $this->data['shipping_cities'] = $cities_array;

                if(isset($product_images[0]->image))
                {
                    $product_array->{'hover_image'} = $product_images[0]->image;
                }
                else
                {
                    $product_array->{'hover_image'}  = $product_details->image;
                }

                $product_array->{'price_before'} = $product_price_data[0];
                $product_array->{'price'}        = $product_price_data[1];
                $product_array->{'strike'}       = $product_price_data[3];
                $product_array->{'discount_percent'} = $product_price_data['discount_percent'];

                //check dailey discount
                if($product_price_data[8] == 1)
                {
                    $product_array->{'daily_end_hour'} = $product_price_data[9];
                }

                foreach($product_tags as $tag)
                {
                    $tags[]     = $tag->id;
                    $tags_text .= $tag->tag.' , ';
                }

                //related products
                $related_limit = 15;
                $related_products_id = $this->site_products_model->get_related_products($id , $tags, $this->data['country_id'], $related_limit);
                $related_products = $this->_get_products_prices($related_products_id, 0, 0, 0, 0, 0, 1);

                if(count($product_optional_fields) != 0)
                {
                    $options_array = array();
                    $optional_fields_images = array();

                    $customer_group_id = isset($this->data['user']->customer_group_id) ? $this->data['user']->customer_group_id:0;

                    foreach ($product_optional_fields as $field)
                    {
                        if($field->has_options == 1)
                        {
                          //echo $field->optional_field_id.'<br>';
                            $option_options = $this->optional_fields_model->get_optional_field_options($field->optional_field_id, $lang_id, $id, 1, array(), array(), $field->free);
                            
                            $option_options_array = array();
                            foreach($option_options as $row)
                            {
                                $option_price = $row->cost;
                                $conds = array(
                                                'customer_group_id' => $customer_group_id,
                                                'option_id'         => $row->id,
                                                'optional_field_id' => $row->optional_field_id,
                                                'product_id'        => $id

                                              );
                                //get customer group cost
                                $customer_group_price = $this->products_model->get_table_data('optional_fields_customer_groups_prices', $conds, 'row');

                                if(count($customer_group_price) != 0)
                                {
                                    if($customer_group_price->group_price != 0)
                                    {
                                        $option_price = $customer_group_price->group_price;
                                    }
                                }

                                $row->{'cost'} = $option_price;
                                $option_options_array[] = $row;

                                if($field->field_type_id == 2 )
                                {
                                  $optional_fields_images[$row->id] = $row->image;
                                }
                            }

                            $field->options = $option_options_array;
                        }

                        $options_array[] = $field;


                    }
//die();
//echo '<pre>'; print_r($options_array); die();
                    $this->data['product_optional_fields'] = $options_array;
                    $this->data['optional_fields_images']  = $optional_fields_images;
                }
                else
                {
                    $this->data['product_optional_fields'] = array();
                }

                //rating data
                $rating_array = array();
                if($product_array->rating_times != 0)
                {
                  $rating_data = $this->products_model->get_product_rating_data($id, $product_array->rating_times);
                  foreach($rating_data as $row)
                  {
                    $rating_array[$row->rate] = $row;
                  }
                }

                $this->data['rating_data'] = $rating_array;
                $this->data['meta_description']  = $product_array->description;
                $this->data['page_title']        = $product_array->meta_title;// . ' - '. lang('like4card');
                $this->data['product_cat_specs'] = $product_cat_specs;
                $this->data['product_images']    = $product_images;
                $this->data['cat_data']          = $cat_data;
                $this->data['parent_data']       = $parent_data;
                $this->data['product_comments']  = $product_comments;
            }

            $this->data['product']           = $product_array;
            $this->data['related_products']  = $related_products;
            $this->data['meta_keywords']     = $tags_text;

            //$this->data['content'] = $this->load->view('products/details', $this->data, true);
            $this->data['content'] = $this->load->view('product_item', $this->data, true);
            $this->load->view('site/main_frame',$this->data);
        }
    }

    public function search()
    {
        $this->session->set_userdata('site_redir', current_url());

        $lang_id        = $this->session->userdata('lang_id');
        $country_id     = $this->session->userdata('country_id');

        if(isset($_POST['product_name']))
        {
            $product_name   = strip_tags($this->input->post('product_name', TRUE));
        }
        elseif(isset($_GET['product_name']))
        {
            $product_name   = strip_tags($this->input->get('product_name', TRUE));
        }

        $this->data['product_name'] =  $product_name;

        if($product_name != '')
        {
            if(isset($_GET['sort']))
            {
                $sort = intval($this->input->get('sort', true));
                $this->data['sort'] = $sort;
            }
            else
            {
                $sort = 0;
            }

            $brands_filter = array();
            $brands_filter_data = array();
            if(isset($_GET['brands_filter']) && $_GET['brands_filter'] !=0 )
            {
                $brands_filter = $this->input->get('brands_filter', true);
                $brands_filter = explode(',', $brands_filter);
                $brands_filter_data = $this->brands_model->get_all_brands($this->data['lang_id'], array(), $brands_filter);
            }

            $this->data['brand_filter'] = array(
              'ids'   => $brands_filter     ,
              'data'  => $brands_filter_data
            );

            $price_from = 0;
            if(isset($_GET['price_from']) && $_GET['price_from'] != '')
            {
              $price_from = intval($this->input->get('price_from', true));
              $this->data['price_from'] = $price_from;
            }

            $price_to = 0;
            if(isset($_GET['price_to']) && $_GET['price_to'] != '')
            {
              $price_to = intval($this->input->get('price_to', true));
              $this->data['price_to'] = $price_to;
            }

            $products_ids_string = '';
            $products_array = array();
            //echo 'lang_id: '.$lang_id.'<br />';
            $products = $this->site_products_model->get_product_by_name($product_name, $lang_id, $country_id, $sort, $brands_filter, $price_from, $price_to);
            $stores = $this->stores_model->get_stores_data($lang_id, 0, 0, $product_name, '', 'desc', array('stores.active'=>1));

            if(count($products) == 0 && count($stores) == 0)
            {
                $this->data['error_msg'] = lang('no_search_result');
            }
            else
            {
              $products_array_data = $this->_get_products_prices($products, $sort, 1, 0, 1, 1);
              $products_array = $products_array_data[0];
              $products_ids_string = $products_array_data[3];
            }
            $brands = $this->site_products_model->get_category_brands(0, $this->data['lang_id'], 0, $products_ids_string);

            $this->data['cat_brands']     = $brands;
            $this->data['cat_products']   = $products_array;
            $this->data['result_num']     = $this->site_products_model->get_product_by_name_count($product_name, $lang_id, $country_id);
            $this->data['page_title']     = lang('product_search_result')." ".$product_name;//." / ".lang('result_num')."  ".count($products_array);//$this->data['result_num'];
            $this->data['products_count'] = count($products_array);
            $this->data['searched_stores'] = $stores;
        }
        else
        {
            $this->data['error_msg'] = lang("please_enter_search_word");
        }

        $this->data['sorting'] = true;

        $this->data['content'] =  $this->load->view('site_products', $this->data, true);
        $this->load->view('site/main_frame',$this->data);
    }

    public function cat_products($page=1, $cat='', $store_id=0)
    {
        #MAIN CATEGORY PRODUCTS

        $this->session->set_userdata('site_redir', current_url());
        $ids_array     = array();
        $sub_cats_data = array();
        $products_ids  = array();
        $cat           = strip_tags($cat);
        $store_id      = intval($store_id);
        $lang_id       = $this->data['lang_id'];
        $country_id    = $this->data['country_id'];

        $limit  = 12;
        $offset = ($page -1) * $limit;

        $cat_id = 0;
        if($cat != '')
        {
          $cat_data = $this->cat_model->get_cat_by_route($cat);
          $cat_id   = $cat_data->id;
        }
        else {
          $this->data['page_title'] = lang('all_prducts');
        }
        $sub_cats_ids = $this->site_products_model->get_category_sub_cats_translation($cat_id, $lang_id);
        $ids_string = '';
        if(count($sub_cats_ids) != 0)
        {
            foreach($sub_cats_ids as $id)
            {
                $ids_array[] = $id->id;
                $sub_cats_data[$id->id] = $id->name;
                $ids_string .= $id->id.",";
            }

            $ids_string = substr_replace($ids_string ,"",-1);

            if(isset($_GET['sort']))
            {
                $sort = intval($this->input->get('sort', true));
                $this->data['sort'] = $sort;
            }
            else
            {
                $sort = 0;
            }

            if(isset($_GET['cat_filter']) && $_GET['cat_filter'] !=0 )
            {
                $cat_filter = strip_tags($this->input->get('cat_filter', true));
                $ids_array  = explode(',', $cat_filter);

                $this->data['cat_filter'] = $ids_array;
            }
            else
            {
                $cat_filter = array();
            }

            if(isset($_GET['price_filter']))
            {
                /**
                 * Price Filter Vals
                 * 1 => less than 100
                 * 2 => between 100 and 200
                 * 3 => between 200 and 300
                 * 4 => more than 300
                 */

                $price_filter = intval($this->input->get('price_filter', true));

                $this->data['price_filter'] = $price_filter;
            }
            else
            {
                $price_filter = 0;
            }

            if(isset($_GET['rating_filter']))
            {
                $rating_filter = intval($this->input->get('rating_filter', true));

                $this->data['rating_filter'] = $rating_filter;
            }
            else
            {
                $rating_filter = 0;
            }

            if(isset($_GET['op_filter']) && $_GET['op_filter'] !=0 )
            {
                $op_filter = strip_tags($this->input->get('op_filter', true));
                $op_ids_array  = explode(',', $op_filter);

                $this->data['op_filter'] = $op_ids_array;
            }
            else
            {
                $op_filter = array();
            }

            $brands_filter = array();
            $brands_filter_data = array();
            if(isset($_GET['brands_filter']) && $_GET['brands_filter'] !=0 )
            {
                $brands_filter = $this->input->get('brands_filter', true);
                $brands_filter = explode(',', $brands_filter);
                $brands_filter_data = $this->brands_model->get_all_brands($this->data['lang_id'], array(), $brands_filter);
            }

            $this->data['brand_filter'] = array(
              'ids'   => $brands_filter     ,
              'data'  => $brands_filter_data
            );

            $products_array     = $this->site_products_model->get_cats_products($ids_array, $lang_id, $country_id, $limit, $offset, $store_id, $sort, $price_filter, $rating_filter,array(), 0, array(), $op_filter);
            $products_count     = $this->site_products_model->get_cats_products_count($ids_array, $lang_id, $country_id, 0);
            $products_new_array = $this->_get_products_prices($products_array, $sort, 0, 0, 1);
            $products_ids       = $products_new_array[1];

            $this->data['cat_products']   = $products_new_array[0];
            $this->data['products_count'] = $products_count;
        }
        else
        {
            $this->data['error_msg'] = lang('no_available_products');
        }

        $cat_data        = $this->cat_model->get_row_data($cat_id, $lang_id);
        $cat_meta_tags   = $this->categories_tags_model->get_cat_tags($cat_id, $lang_id);
        $cat_meta_data   = $this->cat_model->get_row_meta_tag_description($cat_id, $lang_id);
        $cat_brands      = $this->site_products_model->get_category_brands($ids_string, $this->data['lang_id'], 1);

        $this->data['cat_brands'] = $cat_brands;
        $tags = '';
        foreach($cat_meta_tags as $tag)
        {
            $tags .= $tag->tag.' , ';
        }

        $op_array = array();
        if(count($products_ids) != 0)
        {
          $products_optional_fields = $this->optional_fields_model->get_optional_field_options(0, $this->data['lang_id'], 0, 1, $products_ids);

          foreach($products_optional_fields as $row)
          {
            $op_array[$row->optional_field_id][] = $row;
          }
          $this->data['op_filters'] = $op_array;
        }

        $config['base_url']         = base_url().'products/products/cat_products/';
        $config['total_rows']       = $products_count;
        $config['per_page']         = $limit;
        $config['uri_segment']      = 4;
        $config['use_page_numbers'] = TRUE;

        $config['first_link']       = '<<';
        $config['last_link']        = '>>';
        $config['first_tag_open']   = '<li>';
        $config['first_tag_close']  = '</li>';
        $config['last_tag_open']    = '<li>';
        $config['last_tag_close']   = '</li>';
        $config['next_tag_open']    = '<li>';
        $config['next_tag_close']   = '</li>';
        $config['prev_tag_open']    = '<li>';
        $config['prev_tag_close']   = '</li>';
        $config['num_tag_open']     = '<li>';
        $config['num_tag_close']    = '</li>';
        $config['cur_tag_open']     = '<li class="active"><span>';
        $config['cur_tag_close']    = '</span></li>';

        $config['display_pages']    = TRUE;

        $this->pagination->initialize($config);

        if($cat_id != 0)
        {
          $this->data['meta_keywords']     = $tags;
          $this->data['meta_description']  = $cat_meta_data->meta_tag_description;
          $this->data['page_title']        = $cat_meta_data->meta_title;
          $this->data['cat_data']          = $cat_data;
        }
        $this->data['sub_cats_data']     = $sub_cats_data;
        $this->data['sorting']           = true;
        $this->data['page_links']        = $this->pagination->create_links();

        $this->data['content'] = $this->load->view('site_products', $this->data, true);
        $this->load->view('site/main_frame', $this->data);

    }

    public function user_wishlist($page=1)
    {
        $this->session->set_userdata('site_redir', current_url());

        $ids_array    = array();
        $page         = intval($page);
        $user_id      = $this->data['user_id'];
        $lang_id      = $this->data['lang_id'];
        $country_id   = $this->data['country_id'];

        if($user_id == 0)
        {
            redirect('User_login', 'refresh');
        }

        $limit  = 8;
        $offset = ($page -1) * $limit;

        $products_count = $this->site_products_model->get_user_wishlist_products(1, $lang_id, $country_id, $user_id);


        if($products_count == 0)
        {
            $this->data['error_msg'] = lang('no_available_products');
        }
        else
        {
            $config['base_url']         = base_url().'products/products/user_wishlist/';
            $config['total_rows']       = $products_count;
            $config['per_page']         = $limit;
            $config['uri_segment']      = 4;
            $config['use_page_numbers'] = TRUE;

            $config['first_link']       = '<<';
            $config['last_link']        = '>>';
            $config['first_tag_open']   = '<li>';
            $config['first_tag_close']  = '</li>';
            $config['last_tag_open']    = '<li>';
            $config['last_tag_close']   = '</li>';
            $config['next_tag_open']    = '<li>';
            $config['next_tag_close']   = '</li>';
            $config['prev_tag_open']    = '<li>';
            $config['prev_tag_close']   = '</li>';
            $config['num_tag_open']     = '<li>';
            $config['num_tag_close']    = '</li>';
            $config['cur_tag_open']     = '<li class="active"><span>';
            $config['cur_tag_close']    = '</span></li>';

            $config['display_pages']    = TRUE;

            $this->pagination->initialize($config);

            $products_array     = $this->site_products_model->get_user_wishlist_products(0, $lang_id, $country_id, $user_id, $limit, $offset);
            $products_new_array = $this->_get_products_prices($products_array, 0, 0, 0, 0, 0, 0, 1);

            $this->data['page_links']   = $this->pagination->create_links();
            $this->data['cat_products'] = $products_new_array;
        }

        $this->data['wishlist'] = true;

        //$this->data['content']  = $this->load->view('site_products', $this->data, true);
        $this->data['content']  = $this->load->view('wishlist', $this->data, true);
        $this->load->view('site/main_frame',$this->data);
    }

    private function _get_products_prices($products_array, $sort = 0, $search=0, $offers_only=0, $resturn_ids=0, $return_new_used_count=0, $related_products=0, $wishlist=0)
    {
        $products_new_array  = array();
        $products_ids        = array();
        $views               = array();
        $used_products_count = 0;
        $new_products_count  = 0;
        $products_ids_string = '';

        foreach($products_array as $product)
        {
            $product_details    = $this->products_model->get_product_row_details($product->product_id, $this->data['lang_id'], $this->data['country_id']);
            $product_price_data = $this->products_lib->get_product_price_data($product_details);
            $product_details->{'price'} = $product_price_data['product_price'];

            $products_new_array[] = $product_details;
        }

        if($sort == 3 || $sort == 4)
        {
            if($sort == 3)
            {
                function compareOrder($a, $b)
                {
                  if ($a->price < $b->price)
                  {
                      return -1;
                  }
                  else if ($a->price > $b->price)
                  {
                      return 1;
                  }
                  else
                  {
                      return 0;
                  }
                }

                usort($products_new_array, 'compareOrder');
            }
            else if($sort == 4)
            {
                function compareOrder($a, $b)
                {
                  if ($a->price > $b->price)
                  {
                      return -1;
                  }
                  else if ($a->price < $b->price)
                  {
                      return 1;
                  }
                  else
                  {
                      return 0;
                  }
                }

                uasort($products_new_array, 'compareOrder');
            }
        }
        $no_product_lang_check = 0;
        if($search)
        {
          $no_product_lang_check = 0;//1;
        }

        foreach($products_new_array as $key=>$product)
        {
            $product_details = $this->products_model->get_product_row_details($product->product_id, $this->data['lang_id'], $this->data['country_id'], array(), $no_product_lang_check);
            if($product_details->quantity_per_serial == 1)
            {
                $product_qty   = $this->products_model->count_product_available_quantity($product->product_id, $this->data['country_id']);
                $min_stock     = $this->config->item('min_product_stock');
                $rest_qty      = $this->config->item('rest_product_qty');
                $available_qty = $product_qty - $min_stock;
                
                if($product_qty == 0)
                {
                  $product_details->{'no_stock'} = true;
                }

                if($available_qty <= $rest_qty && $product_qty!=0)
                {
                  $stock_qty = $available_qty;
                  $product_details->{'stock_qty'} = $stock_qty;
                }

                if($available_qty <= $rest_qty && $product_qty!=0)
                {
                  $stock_qty = $available_qty;
                  $product_details->{'rest_qty'} = $stock_qty;
                }

                if($available_qty > 0)
                {
                    $availability = lang('available');
                }
                else
                {
                    $availability = lang('unavailable');
                }
            }
            else
            {
                $availability = lang('available');
            }

            $product_details->{'availability'} = $availability;

            $product_price_data = $this->products_lib->get_product_price_data($product_details);
            $product_images     = $this->products_model->get_product_images($product->product_id, 1);

            if(isset($product_images[0]->image))
            {
                $product_details->{'hover_image'}  = $product_images[0]->image;
            }
            else
            {
              $product_details->{'hover_image'}  = $product_details->image;
            }

            $product_details->{'product_images'} = $product_images;

            $currency = $this->currency->get_country_currency_name($this->data['country_id'], $this->data['lang_id']);

            $product_details->{'currency'}      = $currency;
            $product_details->{'price_before'}  = $product_price_data['product_price_before'];
            $product_details->{'price'}         = $product_price_data['product_price'];
            $product_details->{'strike'}        = $product_price_data['allowed_limit'];
            $product_details->{'discount_percent'} = $product_price_data['discount_percent'];
            $product_details->{'special_offer'} = $product_price_data['special_offer'];
            $product_details->{'product_route'} = $this->data['product_route'];
            $product_details->{'sub_category_route'}  = $this->data['sub_category_route'];
            $product_details->{'images_path'}   = $this->data['images_path'];

            if($related_products)
            {
              $product_details->{'card_class'} = 'item';
            }
            else {
              $product_details->{'card_class'} = 'col-lg-4 col-md-6 p-10';
            }
            $product_details->{'img_size'} = 'style="width: 273px !important; height: 251px!important; "';
            if($product_details->is_used == 1)
            {
              $used_products_count += 1;
            }
            else {
              $new_products_count += 1;
            }
//echo 'off '.$offers_only.'=='.$available_qty;die();
            $products_new_data[] = $product_details;



            if(($offers_only == 1 && $available_qty > 0) || $offers_only == 0)
            {
              $product_view = $this->load->view('site/product_card', array('product'=>$product_details), TRUE);
              $views[] = $product_view;
            }

            $products_ids[] = $product->product_id;
            $products_ids_string .= $product->product_id.',';

        }

        if($search == 1 && count($products_ids) !=0)
        {
            $this->site_products_model->update_searched_products($products_ids);
        }

        $products_ids_string = substr_replace($products_ids_string ,"", -1);

        if($wishlist)
        {
          return $products_new_data;
        }
        else if($resturn_ids == 1 || $return_new_used_count==1)
        {
          //return array($views, $products_ids);
          $products_ids_string = substr_replace($products_ids_string ,"",-1);
          $count_array = array(
            'used_products_count' => $used_products_count,
            'new_products_count'  => $new_products_count
          );
          return array($views, $count_array, $products_ids, $products_ids_string);
        }
        else
        {
          return $views;
        }

    }

    public function add_rate()
    {
        $product_id     = intval($this->input->post('product_id', true));
        $rating_points  = intval($this->input->post('ratingPoints', true));
        $user_id        = $this->data['user_id'];

        if($user_id == 0)
        {
            $result = array('login');
        }
        else
        {
          //check user bought this product
          $user_product = $this->site_products_model->check_user_bought_this_product($product_id, $user_id);
          if($user_product)
          {
            // check user rate before
            $user_product_rate_count = $this->site_products_model->check_user_product_rate($product_id, $user_id);

            if($user_product_rate_count == 0)
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
                $product_data = $this->site_products_model->get_product_view($product_id);

                $new_rating_times   = $product_data->rating_times + 1;
                $total_points       = $this->site_products_model->get_product_total_points($product_id);
                $rating_avg         = round($total_points / $new_rating_times, 1);

                $product_data = array(
                                        'total_rating_points' => $total_points,
                                        'rating_times'        => $new_rating_times,
                                        'rating_avg'          => $rating_avg
                                     );

                $this->site_products_model->update_product($product_id, $product_data);


                $result = array(
                                'status'         => 'OK'        ,
                                'average_rating' => $rating_avg ,
                                'rating_number'  => $new_rating_times

                               );

            }
            else
            {
                $result = array('rated_before');
            }
          }
          else {
            $result = array('buy_first');
          }
        }

        echo json_encode($result);
    }

    public function add_to_wishlist()
    {
        $product_id = intval($this->input->post('product_id', true));
        $user_id    = $this->data['user_id'];

        if($product_id == 0)
        {
            $result = 'product_required';
        }
        else
        {
            if($user_id == 0)
            {
                $result = 'login';
            }
            else
            {
                // check if product exist in user wishlist
                $product_exist = $this->site_products_model->check_product_in_wishlist($product_id, $user_id);

                if($product_exist)
                {
                    $result = 'already_exist';
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

                    $result = 'success';
                }
            }
        }

        echo $result;
    }

    public function remove_from_wishlist()
    {
        $product_id = intval($this->input->post('product_id', true));
        $user_id    = $this->data['user_id'];

        if($product_id == 0)
        {
            $result = 'product_required';
        }
        else
        {
            if($user_id == 0)
            {
                $result = 'login';
            }
            else
            {
                // check if product exist in user wishlist
                $product_exist = $this->site_products_model->check_product_in_wishlist($product_id, $user_id);

                if($product_exist)
                {
                    $this->site_products_model->remove_wishlist_product($product_id, $user_id);
                    $result = 'success';
                }
                else
                {
                    $result = 'product_not_exist';
                }
            }
        }

        echo json_encode(array($result, $product_id));
    }

    public function add_compare_product()
    {
        $product_id = intval($this->input->post('product_id', true));

        $session_data =  $product_id;

        if(isset($_SESSION['compare_products']))
        {
            $compare_products = $_SESSION['compare_products'];
            array_push($compare_products, $session_data);

            $new_array =  array();
            foreach($compare_products as $key => $value)
            {
                if(!in_array($value, $new_array))
                {
                    $new_array[] = $value;
                }
                else
                {
                    unset($value[$key]);
                }
            }

            $compare_products   = $new_array;

        }
        else
        {
            $compare_products[] = $session_data;
        }

        if(count($compare_products) > 1)
        {
            $first_product = 0;
        }
        else
        {
            $first_product = 1;
        }

        $this->session->set_userdata('compare_products', $compare_products);

        echo $first_product;
    }

    public function compare_products()
    {
        $products_ids = isset($_SESSION['compare_products'])?$_SESSION['compare_products']:array();

        if(count($products_ids) != 0)
        {
            $lang_id = $this->data['lang_id'];
            $country_id = $this->data['country_id'];

            $products_data      = $this->products_model->get_products($lang_id, 0, 0, $country_id , 0, 0, $products_ids);
            $compare_products   = $this->_get_products_prices($products_data);

            $this->data['compare_products'] = $compare_products;
        }
        else
        {
            $this->data['error_msg'] = lang('no_available_products');
        }

        $this->data['compare'] = true;

        $this->data['content'] = $this->load->view('compare_products', $this->data, true);
        $this->load->view('site/main_frame',$this->data);
    }

    public function remove_compare_product()
    {
        $remove_product_id  = intval($this->input->post('product_id', TRUE));
        $compare_products   = $_SESSION['compare_products'];

        $new_array = array();

        foreach($compare_products as $key => $value)
        {
            if($value != $remove_product_id)
            {
                $new_array[] = $value;
            }
            else
            {
                unset($value[$key]);
            }
        }

        $this->session->set_userdata('compare_products', $new_array);

        echo 1;

    }

    public function add_product_comment()
    {
        if($this->data['user_id'] == 0)
        {
            redirect('User_Login', 'refresh');
        }
        else
        {
            $product_route = strip_tags($this->input->post('route', true));

            $this->form_validation->set_rules('comment', lang('comment'), 'required');
            $this->form_validation->set_rules('username', lang('name'), 'required');
            $this->form_validation->set_rules('product_id', lang('product'), 'required');

            if ($this->form_validation->run() == FALSE)
            {
                redirect('product/'.$product_route.'#reviews', 'refresh');
            }
            else
            {
                $comment    = strip_tags($this->input->post('comment', true));
                $username   = strip_tags($this->input->post('username', true));
                $product_id = intval($this->input->post('product_id', true));
                $user_id    = $this->data['user_id'];

                //check user bought this product
                $user_product = $this->site_products_model->check_user_bought_this_product($product_id, $user_id);
                if($user_product)
                {
                  if($this->config->item('auto_publish_comment') == 1)
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
                                          'username'   => $username,
                                          'comment'    => $comment,
                                          'approved'   => $approved,
                                          'unix_time'  => time()
                                      );

                  $this->site_products_model->insert_product_comment($comment_data);

                  $_SESSION['message'] = lang('review_added');
                  $this->session->mark_as_flash('message');
                }
                else {
                  $_SESSION['error_message'] = lang('buy_product_first');
                  $this->session->mark_as_flash('error_message');
                }
                redirect('product/'.$product_route.'#add-review-tab', 'refresh');
            }
        }
    }

    public function brand_products($brand_id, $page=1)
    {
        $this->session->set_userdata('site_redir', current_url());

        $limit  = 8;
        $offset = ($page -1) * $limit;

        $m_cats_array = array();
        $brand_id   = intval($brand_id);
        $m_products_new_array = array();

        $lang_id      = $this->data['lang_id'];
        $country_id   = $this->data['country_id'];

        if(isset($_GET['sort']))
        {
            $sort = intval($this->input->get('sort', true));
            $this->data['sort'] = $sort;
        }
        else
        {
            $sort = 0;
        }

        if(isset($_GET['rating_filter']))
        {
            $rating_filter = intval($this->input->get('rating_filter', true));

            $this->data['rating_filter'] = $rating_filter;
        }
        else
        {
            $rating_filter = 0;
        }

        $brands_filter = array();
        $brands_filter_data = array();
        if(isset($_GET['brands_filter']) && $_GET['brands_filter'] !=0 )
        {
            $brands_filter = $this->input->get('brands_filter', true);
            $brands_filter = explode(',', $brands_filter);
            $brands_filter_data = $this->brands_model->get_all_brands($this->data['lang_id'], array(), $brands_filter);
        }

        $this->data['brand_filter'] = array(
          'ids'   => $brands_filter     ,
          'data'  => $brands_filter_data
        );

        $price_from = 0;
        if(isset($_GET['price_from']) && $_GET['price_from'] != '')
        {
          $price_from = intval($this->input->get('price_from', true));
          $this->data['price_from'] = $price_from;
        }

        $price_to = 0;
        if(isset($_GET['price_to']) && $_GET['price_to'] != '')
        {
          $price_to = intval($this->input->get('price_to', true));
          $this->data['price_to'] = $price_to;
        }

        $brands_filter = array('brand_id'=> $brand_id);

        $products_data  = $this->site_products_model->get_store_products(0, $lang_id, $country_id, $sort, $limit, $offset, $brands_filter, $rating_filter, $price_from, $price_to);
        $products_count = $this->site_products_model->get_store_products_count(0, $lang_id, $country_id, $brands_filter, $rating_filter, $price_from, $price_to);

        $products_data_array     = $this->_get_products_prices($products_data, $sort, 0, 0, 0, 1);
        $m_products_new_array    = $products_data_array[0];
        $new_used_products_count = $products_data_array[1];
        $brand_data              = $this->brands_model->get_row_data($brand_id, $lang_id);

        $config['base_url']         = base_url().'products/products/brand_products/'.$brand_id.'/';
        $config['total_rows']       = $products_count;
        $config['per_page']         = $limit;
        $config['uri_segment']      = 4;
        $config['use_page_numbers'] = TRUE;
        $config['first_link']       = '<<';
        $config['last_link']        = '>>';
        $config['first_tag_open']   = '<li>';
        $config['first_tag_close']  = '</li>';
        $config['last_tag_open']    = '<li>';
        $config['last_tag_close']   = '</li>';
        $config['next_tag_open']    = '<li>';
        $config['next_tag_close']   = '</li>';
        $config['prev_tag_open']    = '<li>';
        $config['prev_tag_close']   = '</li>';
        $config['num_tag_open']     = '<li>';
        $config['num_tag_close']    = '</li>';
        $config['cur_tag_open']     = '<li class="active"><span>';
        $config['cur_tag_close']    = '</span></li>';
        $config['display_pages']    = TRUE;

        $this->pagination->initialize($config);
        $this->data['page_links']     = $this->pagination->create_links();



        if(count($m_products_new_array) == 0)
        {
            $this->data['error_msg'] = lang('no_available_products');
        }

        $this->data['cat_products'] = $m_products_new_array;
        $this->data['products_count'] = count($m_products_new_array);
        //$this->data['store_data']   = $m_store_data;
        $this->data['sorting']      = true;
        $this->data['page_title']   = $brand_data->name;
        $this->data['new_used_products_count'] = $new_used_products_count;

        $this->data['content'] = $this->load->view('site_products', $this->data, true);
        $this->load->view('site/main_frame',$this->data);
    }


/************************************************************************/
}
