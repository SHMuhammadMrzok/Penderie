<?php

    $this->load->library('user_bootstrap');
    $this->load->library('products_lib');
    $this->load->library('shopping_cart');
    $this->load->library('gateways');

    $this->load->model('orders_model');
    $this->load->model('brands/brands_model');
    $this->load->model('products/products_model');
    $this->load->model('products/site_products_model');

    $this->data['user_id']                = $this->user_bootstrap->get_user_id();
    $this->data['user']                   = $this->user_bootstrap->get_user_data();
    $this->data['is_logged_in']           = $this->user_bootstrap->is_logged_in();
    //$this->data['is_wholesaler']          = $this->user_bootstrap->is_wholesaller();
    //$this->data['edit_wholesaler_data']   = $this->user_bootstrap->check_if_is_wholesaler();
    $this->data['customer_group_id']      = $this->user_bootstrap->get_customer_group_id();
    $this->data['customer_group_name']    = $this->user_bootstrap->get_customer_group_name();
    $this->data['session_id']             = session_id();
    $this->data['ip_address']             = $this->input->ip_address();
    $this->data['country_id']             = $this->user_bootstrap->get_active_country_id();
    $this->data['lang_id']                = $this->user_bootstrap->get_active_lang_id();

    $this->data['data_languages']         = $this->user_bootstrap->get_data_languages();
    $this->data['structure_languages']    = $this->user_bootstrap->get_structure_languages();
    $this->data['active_language_row']    = $this->user_bootstrap->get_active_language_row();
    $this->data['active_lang']            = $this->user_bootstrap->get_active_lang();

    $this->data['languages']              = $this->user_bootstrap->get_languages();
    $this->data['countries']              = $this->user_bootstrap->get_countries();
    $this->data['active_country_row']     = $this->user_bootstrap->get_active_country_row();

    $this->data['module']                 = $this->user_bootstrap->get_module();
    $this->data['controller']             = $this->user_bootstrap->get_controller();
    $this->data['method']                 = $this->user_bootstrap->get_method();

    $settings                             = $this->user_bootstrap->get_settings();
    $site_settings                        = $this->user_bootstrap->get_site_settings();

    foreach($settings as $key => $value)
    {
        $this->config->set_item($key, $value);
    }

    foreach ($site_settings as $key => $row)
    {
      $this->config->set_item($row->field, $row->value);
    }

    $cats = $this->user_bootstrap->get_categories();

    foreach($cats as $key=>$cat)
    {
        $categories_array[$cat->parent_id][] = $cat;
        if(isset($categories_array[$cat->parent_id]['products_count']))
        {
          $categories_array[$cat->parent_id]['products_count'] += $cat->products_count;
        }
        else {
          $categories_array[$cat->parent_id]['products_count'] = $cat->products_count;
        }

    }

    $this->data['categories_array']       = $categories_array;
    $this->data['categories']             = $cats;

    $this->data['cats_vertical_limit']    = $this->config->item('categories_vertical_limit');;
    $this->data['menu_horizontal_limit']  = $this->config->item('menu_horizontal_limit');;

    //$this->data['advertisments']          = $this->user_bootstrap->get_advertisments();
    //$this->data['middle_advertisments']   = $this->user_bootstrap->get_middle_advertisments();
    //$this->data['bottom_advertisments']   = $this->user_bootstrap->get_bottom_advertisments();
    //$this->data['side_advertisments']     = $this->user_bootstrap->get_side_advertisments();
    //$this->data['ads_2']     = $this->user_bootstrap->get_ads_with_location('ads_2', 2);
    //$this->data['ads_3']     = $this->user_bootstrap->get_ads_with_location('ads_3', 3);
    //$this->data['ads_4']     = $this->user_bootstrap->get_ads_with_location('ads_4', 4);
    //$this->data['ads_5']     = $this->user_bootstrap->get_ads_with_location('ads_5', 5);

    //$this->data['products']               = $this->user_bootstrap->get_home_products();
    $this->data['menu_stores']            = $this->user_bootstrap->get_menu_stores();




   $pay_banks_images              = $this->user_bootstrap->pay_images();
   $this->data['payment_methods_images'] = $pay_banks_images;

   //$this->data['top_products']          = $this->products_lib->get_top_selling_products($this->data['lang_id'], $this->data['country_id']);

   //$this->data['most_bought_products'] = $this->user_bootstrap->get_most_sold_products();

   $address_array      = json_decode($this->config->item('address'));
   $mobiles_array      = json_decode($this->config->item('mobile'));
   $telephone_array    = json_decode($this->config->item('telephone'));
   $emails_array       = json_decode($this->config->item('email'));

   $adds            = '';
   $mobiles         = '';
   $telephones      = '';
   $emails          = '';

   foreach($address_array as $add)
   {
        $adds .= $add." , ";
   }

   foreach($mobiles_array as $mobile)
   {
        $mobiles .= $mobile." , ";
   }

   foreach($telephone_array as $telephone)
   {
        $telephones .= $telephone." , ";
   }


   foreach($emails_array as $email)
   {
        $emails .= $email." , ";
   }

   $this->data['site_address'] = trim($adds, " , ");
   $this->data['site_mobiles'] = trim($mobiles, " , ");
   $this->data['site_phones']  = trim($telephones, " , ");
   $this->data['site_emails']  = trim($emails, " , ");

   $this->data['product_route']         = $settings->product_route;
   $this->data['main_category_route']   = $settings->main_category_route;
   $this->data['sub_category_route']    = $settings->sub_category_route;

   //shopping cart cntent
   $this->shopping_cart->set_user_data(

                                        $this->data['user_id'],
                                        $this->data['session_id'],
                                        $this->data['ip_address'],
                                        $this->data['country_id'],
                                        $this->data['lang_id']
        );
   $cart_data       = $this->shopping_cart->shopping_cart_data();
   $cart_contents   = $this->shopping_cart->contents();

   $new_array = array();

   foreach($cart_contents as $content)
   {
        if($content->product_id != 0)
        {
            $product_data = $this->products_model->get_products_row($content->product_id);

            $content->{'route'} = $product_data->route;
           // $content->{'image'} = $product_data->image;
        }
        $new_array[] = $content;
   }


   $this->data['cart_data']     = $cart_data;
   $this->data['cart_contents'] = $new_array;

   $this->data['currency_name']         = $this->currency->get_country_currency_name($this->data['country_id'], $this->data['lang_id']);
    
    $this->data['user_balance_new'] = 0;
   if($this->data['is_logged_in'])
   {
       $enc_user_balance_new  =  $this->user_bootstrap->get_user_data()->user_balance;
       $secret_key_new        = $this->config->item('new_encryption_key');
       $secret_iv_new         = $this->data['user_id'];
       $this->data['user_balance_new']      = $this->encryption->decrypt($enc_user_balance_new, $secret_key_new, $secret_iv_new);
   }

   $cart_data       = $this->shopping_cart->shopping_cart_data();
   $fav_products_count = $this->site_products_model->get_user_wishlist_products(1, $this->data['lang_id'], $this->data['country_id'], $this->data['user_id']);
    
    $this->data['cart_items_count'] = $cart_data->items_count;
   //$this->data['user_orders_count'] = $this->user_bootstrap->get_user_orders_count();
   $this->data['fav_products_count'] = $fav_products_count;
   
   $currency = $this->currency->get_country_currency_name($this->data['country_id'], $this->data['lang_id']);
   $this->data['currency'] = $currency;

   if($this->config->item('images_source') == 'amazon')
   {
     $images_path = "https://".$this->config->item('amazon_s3_my_bucket').".s3.".$this->config->item('amazon_s3_region').".amazonaws.com/".$this->config->item('amazon_s3_subfolder');
     //https://sbmcart.s3.eu-west-2.amazonaws.com/qhwastore/54e62-2019-10-19.png
   }
   else
   {
     $images_path = base_url().'assets/uploads/';
   }
   $this->data['images_path'] = $images_path;


   $this->data['reCAPTCHA_site_key'] = $this->config->item('reCAPTCHA_site_key');
//    $this->data['reCAPTCHA_secret_key'] = $this->config->item('reCAPTCHA_secret_key');
//    $this->data['maroof_id']   = $this->config->item('maroof_id');