<?php
    if (!defined('BASEPATH'))
    exit('No direct script access allowed');

    class Test extends CI_Controller
    {
        public $settings;
        public function __construct()
        {
            parent::__construct();
            $this->load->model('products/products_model');
            $this->load->model('products/Site_products_model');
            $this->load->model('products/products_tags_model');
            $this->load->library('products_lib');
            $this->load->library('currency');
        }
        public function Product_details()
        {
            $lang_id                = intval($this->input->post('langId', TRUE));
            $product_id             = intval($this->input->post('product_id', TRUE));
            $country_id             = intval($this->input->post('country_id', TRUE));
            $email                  = strip_tags($this->input->post('email', TRUE));
            $password               = strip_tags($this->input->post('password', TRUE));
            $deviceId               = strip_tags($this->input->post('deviceId', TRUE));
            
            if($this->ion_auth->login($email, $password))
            {
                $user_data     = $this->ion_auth->user()->row();
                $user_id = $user_data->id;
            }else
             {
                $user_id = 0;
             }
            
            $product_details = $this->Site_products_model->get_product($product_id, $lang_id, $country_id);
            
            $products_price_data = $this->products_lib->get_product_price_data($product_details , $country_id , $user_id , $deviceId);
            $currency           = $this->currency->get_country_currency_name($country_id, $lang_id);
            $product        = $this->products_model->get_product_row_details($product_id, $lang_id, $country_id, $user_id);
            //Related Products
            $tags = array();
            $get_product_tags = $this->products_tags_model->get_product_tags($product_id, $lang_id);
            
            if(count($get_product_tags) !=0)
            {
                $tags_text = '';
                foreach($get_product_tags as $tag)
                {
                    $tags[]     = $tag->id;
                    $tags_text .= $tag->tag.' , ';
                }
            }
            $related_products_id = $this->Site_products_model->get_related_products($product_id , $tags, $country_id);
            
            $products_ids = array();
             
            foreach($related_products_id as $re_product_item)
            {
                $products_ids[] = $re_product_item->product_id;
            }
            
            $products_ids = array_unique($products_ids);

            
            $related_products = $this->get_products_list_array($products_ids, $product_id, $lang_id, $country_id, $user_id, $deviceId, $currency);
            die(print_r($related_products));
            $product_route = $this->settings->product_route;
            
            $advs_ids = json_decode($product->advantages_ids);
            
            if(count($advs_ids) != 0)
            {
                $advantages = $this->advantages_model->get_all_advantages($lang_id, 0, $advs_ids);
                foreach($advantages as $adv)
                {
                    $advs_arry[] = array(
                                            'name'  => $adv->name,
                                            'image' => base_url().'assets/uploads/'.$adv->image,
                                        );  
                }
            }
            
            
            $output = array
                     (
                        'reward_points'         => $product_details->reward_points,
                        'product_id'            => $product_details->product_id,
                        'title'                 => $product_details->title,
                        'description'           => $product_details->description,
                        'image'                 => base_url().'assets/uploads/products/'.$product_details->image,
                        'cat_name'              => $product_details->cat_name,
                        'product_price_before'  => $products_price_data['product_price_before'],
                        'product_price'         => $products_price_data['product_price'],
                        'relatedProducts'       => $related_products,
                     );
                     $this->output->set_content_type('application/json')->set_output(json_encode($output));
        }
        
        
        public function get_products_list_array($products_array, $main_product_id, $lang_id, $country_id, $user_id, $deviceId, $currency)
        {
            $result_array = array();
    
            foreach($products_array as $product_id)
            {
                $product_details    = $this->products_model->get_product_row_details($product_id, $lang_id, $country_id);
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
                    $re_pic =  base_url().'assets/uploads/products/'.$product_details->image;
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
                                            'producuctQuantityPerSerial'    => $product_details->quantity_per_serial,
                                            'productCurrency'               => $currency,
                                            'new'                           => $product_details->new
                                           );
    
            }
    
            return $result_array;
        }
    }
?>