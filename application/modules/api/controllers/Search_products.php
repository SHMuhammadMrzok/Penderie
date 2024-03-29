<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Search_products extends CI_Controller
{
    public $images_path;
    public function __construct()
    {
        parent::__construct();

        $this->load->model('products/products_model');
        $this->load->model('products/site_products_model');
        $this->load->model('optional_fields/optional_fields_model');
        $this->load->model('stores/stores_model');
        $this->load->model('categories/cat_model');
        $this->load->model('general_model');

        $this->load->library('pagination');
        $this->load->library('products_lib');
        $this->load->library('api_lib');
        $this->load->library('shopping_cart');
        
        $this->images_path = $this->api_lib->get_images_path();
    }

    public function index()
    {
        $lang_id        = intval($this->input->post('langId', TRUE));
        $country_id     = intval($this->input->post('countryId', TRUE));
        $page           = intval($this->input->post('page', TRUE));
        $email          = strip_tags($this->input->post('email', TRUE));
        $password       = strip_tags($this->input->post('password', TRUE));
        $deviceId       = strip_tags($this->input->post('deviceId', TRUE));
        $search_word    = strip_tags($this->input->post('searchWord', TRUE));

        $ip_address     = $this->input->ip_address();

        $agent          = strip_tags($this->input->post('agent', TRUE));

        $limit  = 25;
        $offset = ($page -1) * $limit;


        $store_id = 0;//intval($store_id);

        $products_new_array = array();


        $sort          = 0;
        $price_from    = 0;
        $price_to      = 0;
        $rating_filter = 0;
        $parent_cat_id = 0;
        $brands_filter      = array();
        $brands_filter_data = array();

        if($this->ion_auth->login($email, $password))
        {
            $user    = $this->ion_auth->user()->row();
            $user_id = $user->id;
        }
        else {
          $user_id = 0;
        }
        $this->shopping_cart->set_user_data($user_id, $deviceId, $ip_address , $country_id ,$lang_id);

        $products_array     = $this->site_products_model->get_product_by_name($search_word, $lang_id, $country_id, $sort, $brands_filter, $price_from, $price_to, $limit, $offset);
        $products_count     = $this->site_products_model->get_product_by_name_count($search_word, $lang_id, $country_id, $brands_filter, $price_from, $price_to);//count($products_array);
        $pages_count        = ceil($products_count/$limit);
        $products_new_array = $this->_get_products_array($products_array, $lang_id, $country_id, $user_id, $deviceId, array(), $pages_count);
        
        $stores = $this->stores_model->get_stores_data($lang_id, $limit, $offset, $search_word, '', 'desc', array('stores.active'=>1));


        if(count($products_new_array) == 0 && count($stores) == 0)
        {
            $fail_message   = $this->general_model->get_lang_var_translation('no_search_result', $lang_id);
            $output         = array(
                                        'message'   => $fail_message,
                                        'response'  => 0
                                   );
        }
        else {
          $output = $products_new_array;
          $stores_array = array();
          if(count($stores) != 0)
          {
              foreach($stores as $store)
              {
                $output[] = array(
                                        'id'    => $store->id ,
                                        'name'  => $store->name,
                                        'image' => $this->images_path.$store->image,
                                        'type'  => 'store'
                                    );
              }
          }
          
          /*$output = array(
          'products' => $products_new_array,
          'stores' => $stores_array
          );*/
        }

        //***************LOG DATA***************//
        //insert log
        $this->api_lib->insert_log($user_id, current_url(), 'Search products', $agent, $_POST, $output);
        //***************END LOG***************//

        $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));
    }

    private function _get_products_array($products, $lang_id, $country_id, $user_id, $deviceId, $cat_data=array(), $pages_count)
    {
      $settings   = $this->general_model->get_settings();
      $output = array();
      $currency = $this->currency->get_country_currency_name($country_id, $lang_id);
      $images_path = $this->images_path;

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
              //$pic =  base_url().'assets/uploads/products/'.$product->image;
              //$pic =  base_url().'assets/uploads/products/250x275/250x275_'.$product->image;
              //$thumb_name = base_url().'assets/uploads/products/50x55/50x55_'.$product->image;
              //$pic =  base_url().'assets/uploads/products/thumb150.jpg';
              $pic = $images_path.$product->image;
              $thumb_name = $images_path.$product->image;
          }

          $availability = true;  // true means product has stock , false means product has no stock
          $rest_qty     = 0; // only rest (number) items of product "Message"

          if($product_details->quantity_per_serial == 1)
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

          $vat_val    = $product_price_data['vat_value'];
          $vat_percent = $product_price_data['vat_percent'];
         /*$is_cart    = $this->shopping_cart->check_product_in_cart($product->product_id);
          $is_fav     = $this->products_model->check_product_in_fav($product->product_id, $user_id);
          $is_compare = $this->products_model->check_product_in_compare($product->product_id, $user_id);

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

*/

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
                              //'productOptionalFields'         => $product_optional_fields     ,
                              //'optionalFieldsExist'           => $optional_fields             ,
                              //'new'                           => $product->new                ,
                              'views'                         => $product->view               ,
                              //'isCart'                        => $is_cart                     ,
                              //'isFav'                         => $is_fav                      ,
                              //'isCompare'                     => $is_compare                  ,
                              //'productImages'                 => $images_array                ,
                              //'brandName'                     => $brand_name                  ,
                              'totalPoints'                   => $product_details->total_rating_points,
                              'ratingTimes'                   => $product_details->rating_times       ,
                              'ratingAvg'                     => $product_details->rating_avg         ,
                              'availableProduct'              => $availability                        ,
                              'restQty'                       => $rest_qty                           ,
                              'pagesCount'                    => $pages_count,
                              'type' => 'product'
                            );
      }

      return $output;
    }

    public function autocomplete()
    {
      $lang_id        = intval($this->input->post('langId', TRUE));
      $country_id     = intval($this->input->post('countryId', TRUE));
      $search_word    = strip_tags($this->input->post('searchWord', TRUE));
      $ip_address     = $this->input->ip_address();

      $limit  = 10;

      $products_array = $this->site_products_model->get_product_autocomplete($search_word, $lang_id, $country_id, $limit);
      $stores = $this->stores_model->get_stores_data($lang_id, 0, 0, $search_word, '', 'desc', array('stores.active'=>1));

      $output = array();
      foreach($products_array as $product)
      {
        $output[] = array(
          'title' => $product->title,
          'productId' => $product->product_id,
          'type' => 'product'
        );
      }
      
      if(count($stores) != 0)
      {
        foreach($stores as $store)
        {
            $output[] = array(
              'title' => $store->name,
              'storeId' => $store->store_id,
              'type' => 'store'
            );
        }
      }

      $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));


    }
/*
public function get_product_autocomplete($search_word, $lang_id, $country_id, $limit)
{
  $this->db->join('products_countries', 'products_translation.product_id = products_countries.product_id
                  AND products_countries.active=1
                  AND products_countries.country_id='.$country_id);
  $this->db->like('products_translation.title', $search_word , 'both');
  $this->db->where('lang_id', $lang_id);

  $query = $this->db->get('products_translation', $limit);

  if($query)
  {
    return $query->result();
  }
  else {
    return false;
  }
}
*/

/************************************************************************/
}
