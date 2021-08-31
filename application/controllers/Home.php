<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {

    public $data = array();

    public function __construct()
    {
        parent::__construct();

        $this->load->library('products_lib');

        $this->load->model('categories/cat_model');
        $this->load->model('home_model');
        $this->load->model('products/products_model');
        $this->load->model('products/site_products_model');
        $this->load->model('brands/brands_model');
        $this->load->model('orders/orders_model');
        $this->load->model('stores/stores_model');

        require(APPPATH . 'includes/front_end_global.php');

        $this->user_bootstrap->set_back_redirection_url(current_url());

    }

    public function index()
    {
        //insert log
        $this->visits_log->add_log(30, 94, 315, $this->data['lang_id']);

        $stores_array       = array();
        $most_searched      = array();

        $brands             = $this->brands_model->get_all_brands($this->data['lang_id']);
        $main_cats          = $this->cat_model->get_parent_cats($this->data['lang_id']);

        $products_limit     = 15;//$this->config->item('products_limit');
        $lang_id            = $this->data['lang_id'];
        $country_id         = $this->data['country_id'];
        $cats_array         = array();

        foreach($main_cats as $cat)
        {
            $sub_cats_ids  = $this->site_products_model->get_category_sub_cats_translation($cat->id, $lang_id);
            $ids_array = array();
            if(count($sub_cats_ids) != 0)
            {
                foreach($sub_cats_ids as $id)
                {
                    $ids_array[] = $id->id;
                    $sub_cats_data[$id->id] = $id->name;
                }
            }

            /*$cats_products = $this->site_products_model->get_cats_products($ids_array, $lang_id, $country_id, $products_limit, 0, 0, 0, 0, 0);
            $conds = array('category_id'=> $cat->id);
            $cat_ads = $this->advertisement_model->get_advertisments($lang_id, 'menu_cats',2, $conds);

            if(count($cats_products) !=  0)
            {
                ///mariam///$cat_products_array = $this->_get_products_prices($cats_products);
                $cat->{'cat_products'} = array();//$cat_products_array;
            }
            */
            $cat->{'ads'} = array();//$cat_ads;

            $cats_array[] = $cat;
        }

        $store_id = 0;
        $offers_products  = $this->site_products_model->get_all_offers_products($this->data['lang_id'], $this->data['country_id'], $store_id, 0, 0, 0, 12, 0);
        $offers_new_array = $this->_get_products_prices($offers_products);
        $newly_added_products = $this->site_products_model->get_store_products(0, $this->data['lang_id'], $this->data['country_id'], 0, $products_limit, 0, array(), 0, 0, 0, array('products_countries.display_home'=>1));
        $newly_added_array = $this->_get_products_prices($newly_added_products);
        $most_sold_products = $this->orders_model->get_most_bought_store_products($this->data['lang_id'], $this->data['country_id'], 0, $this->config->item('products_limit'), $this->data['user_id']);

        //get_most_bought_products($this->data['lang_id'], $this->data['country_id'], '', 8);
        $most_sold_products_array = $this->_get_products_prices($most_sold_products);
        //print_r($most_sold_products);
        //print_r($most_sold_products_array); 
        //die();
        $home_stores = $this->stores_model->get_all_stores($this->data['lang_id'], array(), array('stores.show_in_main_page'=>1));

        $this->data['new_products']     = $newly_added_array;
        $this->data['offers_products']  = $offers_new_array;
        $this->data['brands']           = $brands;
        $this->data['stores_products']  = $stores_array;
        $this->data['most_products']    = $most_sold_products_array;
        $this->data['searched_products']= $most_searched;
        $this->data['cats_products']    = $cats_array;
        $this->data['advertisments']    = $this->user_bootstrap->get_advertisments('top');
        $this->data['top_advertisments']    = $this->user_bootstrap->get_advertisments('ads_2', 5);
        //print_r($this->data['top_advertisments']); die();
        $this->data['middle_advertisments'] = $this->user_bootstrap->get_advertisments('middle', 5);
        $this->data['bottom_advertisments'] = $this->user_bootstrap->get_advertisments('bottom', 5);
        $this->data['home_stores']      = $home_stores;

        //echo '<pre>';print_r($this->data['bottom_advertisments']); die();

        $this->data['content'] = $this->load->view('site/home', $this->data, true);

        $this->load->view('site/main_frame', $this->data);
    }

    private function _get_products_prices($products_array, $offers_only=0)
    {
        $products_new_array = array();
        $products_ids       = array();
        $views              = array();
        $currency = $this->currency->get_country_currency_name($this->data['country_id'], $this->data['lang_id']);

        foreach($products_array as $product)
        {
            $product_details = $this->products_model->get_product_row_details($product->product_id, $this->data['lang_id'], $this->data['country_id']);
  //          $product_details = $this->products_model->get_product_row_details(118, $this->data['lang_id'], $this->data['country_id']);

            if(count($product_details) != 0)
            {
              if($product_details->quantity_per_serial == 1)
              {
                  $product_qty   = $this->products_model->count_product_available_quantity($product->product_id, $this->data['country_id']);
                  $min_stock     = $this->config->item('min_product_stock');
                  $rest_qty      = $this->config->item('rest_product_qty');
                  $available_qty = $product_qty - $min_stock;

                  if($product_qty == 0)
                  {
                    $product->{'no_stock'} = true;
                  }

                  if($available_qty <= $rest_qty && $product_qty!=0)
                  {
                    $stock_qty = $available_qty;
                    $product->{'rest_qty'} = $stock_qty;
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

              $product_images = $this->products_model->get_product_images($product->product_id, 1);

              $product->{'availability'} = $availability;

              if(isset($product_images[0]->image))
              {
                  $product->{'hover_image'}  = $product_images[0]->image;
              }
              else
              {
                $product->{'hover_image'}  = $product->image;
              }
            $product_price_data        = $this->products_lib->get_product_price_data($product_details);

            $product->{'price_before'}        = $product_price_data[0];
            $product->{'price'}               = $product_price_data[1];
            $product->{'strike'}              = $product_price_data[3];
            $product->{'discount_percent'}    = $product_price_data['discount_percent'];
            $product->{'multi_images'}        = $product_images;
            $product->{'currency'}            = $currency;
            $product->{'special_offer'}       = $product_price_data['special_offer'];
            $product->{'product_route'}       = $this->data['product_route'];
            $product->{'sub_category_route'}  = $this->data['sub_category_route'];
            $product->{'card_class'}          = 'col-md-3';
            $product->{'img_size'}            = ' width="251" height="230" ';
            $product->{'images_path'}         = $this->data['images_path'];

            if(! isset($product->{'no_stock'}))
            {
              $products_new_array[] = $product;

              $product_view = $this->load->view('site/product_card', array('product'=>$product), TRUE);
              $views[] = $product_view;
            }
          }

        }

        return $views;
    }

/******************************************************************************/

}
/* End of file home.php hh */ 
/* Location: ./application/controllers/home.php */
