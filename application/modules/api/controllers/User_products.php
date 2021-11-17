<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class User_products extends CI_Controller
{
  
    public $settings;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model('users/users_model');
        $this->load->model('products/products_model');
        $this->load->model('products/site_products_model');
        $this->load->model('categories/cat_model');
        $this->load->model('brands/brands_model');
        $this->load->model('general_model');
        $this->load->model('root/lang_model');
        $this->load->model('users/countries_model');
        
        $this->load->library('pagination');
        $this->load->library('products_lib');
        $this->load->library('uploaded_images');
        $this->load->library('api_lib');
        $this->load->library('notifications');
        
        $this->settings = $this->general_model->get_settings();
        $images_path = $this->api_lib->get_images_path();
    }
    
    public function products_cats()
    {
        $lang_id        = intval($this->input->post('langId', TRUE));
        $email          = strip_tags($this->input->post('email', TRUE));
        $password       = strip_tags($this->input->post('password', TRUE));
        $deviceId       = strip_tags($this->input->post('deviceId', TRUE));
        $country_id     = intval($this->input->post('countryId', TRUE));
        
        $maintenance_cat_id = $this->settings->maintenance_cat_id;
        
        $agent              = strip_tags($this->input->post('agent', TRUE));
        $user_id            = 0;

        if($this->ion_auth->login($email, $password))
        {
            $user_data  = $this->ion_auth->user()->row();
            $user_id    = $user_data->id;
        }

        $conds = array(
                        //'categories.has_brands' => 1,
                        'parent_id'     => 0,
                      );
        
        $categories = $this->cat_model->get_categories($lang_id, 0, 1, $conds);
        
        $cats_array = array();
        foreach($categories as $key=>$cat)
        {
            // get sub cats 
            $sub_conds = array(
                            'categories.has_brands' => 1,
                            'parent_id' => $cat->id
                          );
            
            $sub_cats = $this->cat_model->get_categories($lang_id, 0, 1, $sub_conds);
            $sub_array = array();
            foreach($sub_cats as $row)
            {
                $cat_brands  = array();
                $brand_conds = array(
                                        'brands.cat_id' => $row->id
                                    );
                                    
                $brands = $this->brands_model->get_all_brands($lang_id, $brand_conds);
                foreach($brands as $key=>$brand)
                {
                    $cat_brands[] = array(
                                            'brandId' => $brand->id,
                                            'brandName' => $brand->name
                                         );
                }
                
                $sub_array[] = array(
                                    'categoryId'     => $row->id         ,
                                    'categoryName'   => $row->name       ,
                                    'categoryImage'  => $images_path.$row->image,
                                    'hasBrands'      => $row->has_brands ,
                                    'brands'         => $cat_brands
                               );
            }
            
            
            $cats_array[] = array(
                                    //'catId'     => $cat->id         ,
                                    'categoryName'   => $cat->name       ,
                                    'categoryImage'  => $images_path.$cat->image,
                                    //'hasBrands' => $cat->has_brands ,
                                    //'brands'    => $cat_brands
                                    'subCats'       => $sub_array
                               );
        }
        
        $output = $cats_array;
        
        //***************LOG DATA***************//
        //insert log
        $this->api_lib->insert_log($user_id, current_url(), 'User products - Products cats', $agent, $_POST, $output);
        //***************END LOG***************//

        $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));
    }
    
    public function index()
    {
        $lang_id        = intval($this->input->post('langId', TRUE));
        $email          = strip_tags($this->input->post('email', TRUE));
        $password       = strip_tags($this->input->post('password', TRUE));
        $deviceId       = strip_tags($this->input->post('deviceId', TRUE));
        $country_id     = intval($this->input->post('countryId', TRUE));
        
        // owner data 
        $owner_name     = strip_tags($this->input->post('ownerName', true));
        $owner_email    = strip_tags($this->input->post('ownerEmail', true));
        $owner_phone    = strip_tags($this->input->post('ownerPhone', true));
        $contact_data   = intval($this->input->post('contactData', true));
        $location_country_id = intval($this->input->post('locationCountryId', true));
        $location_city_id = intval($this->input->post('locationCityId', true));
        
        // product data
        $product_name   = strip_tags($this->input->post('productName', true));
        $product_desc   = strip_tags($this->input->post('productDescription', true));
        $new            = intval($this->input->post('new', true));
        $category_id    = intval($this->input->post('categoryId', TRUE));
        $brand_id       = intval($this->input->post('brandId', TRUE));
        $price          = intval($this->input->post('price', TRUE));
        
        $agent              = strip_tags($this->input->post('agent', TRUE));
        $user_id            = 0;
        
        $output         = array();
        
        if($this->ion_auth->login($email, $password))
        {
            $user     = $this->ion_auth->user()->row();
            $user_id  = $user->id;
            $def_group_id = 2;//$this->settings->new_user_customer_group_id;
            
            //check validation
            
            $product_name_lang          = $this->general_model->get_lang_var_translation('product_name', $lang_id);
            $product_description_lang   = $this->general_model->get_lang_var_translation('description', $lang_id);
            //$product_status_lang        = $this->general_model->get_lang_var_translation('status', $lang_id);
            $new_lang                   = $this->general_model->get_lang_var_translation('new', $lang_id);
            $cat_lang                   = $this->general_model->get_lang_var_translation('category', $lang_id);
            $price_lang                 = $this->general_model->get_lang_var_translation('price', $lang_id);
            $name_lang                  = $this->general_model->get_lang_var_translation('name', $lang_id);
            $email_lang                 = $this->general_model->get_lang_var_translation('email', $lang_id);
            $phone_lang                 = $this->general_model->get_lang_var_translation('phone', $lang_id);
            $required_lang              = $this->general_model->get_lang_var_translation('required', $lang_id);
            
            $this->form_validation->set_rules('productName', $product_name_lang, 'required');
            $this->form_validation->set_rules('productDescription', $product_description_lang, 'required');
            //$this->form_validation->set_rules('productStatus', $product_status_lang, 'required');
            $this->form_validation->set_rules('new', $new_lang, 'required');
            $this->form_validation->set_rules('categoryId', $cat_lang, 'required');
            $this->form_validation->set_rules('price', $price_lang, 'required');
            $this->form_validation->set_rules('ownerName', $name_lang, 'required');
            $this->form_validation->set_rules('ownerEmail', $email_lang, 'required');
            $this->form_validation->set_rules('ownerPhone', $phone_lang, 'required');
            
            $this->form_validation->set_message('required', $required_lang."  : %s ");
            
            if($this->form_validation->run() == FALSE)
    		{
    		   $output = array(
                                'response' => 0,
                                'message' => strip_tags(validation_errors())
                              );
            }
            else
            {
                
                // check user group
                $user_groups_cond = array(
                                            'user_id' => $user_id
                                         );
                $user_groups = $this->users_model->get_result_data_where('users_groups', 'result_array', $user_groups_cond);
                
                //check user group
                
                if(in_array($def_group_id, array_column($user_groups, 'group_id'))) { // search value in the array
                    
                    $store_id = $this->settings->default_store_id;
                    
                    $category_id        = intval($this->input->post('categoryId', true));
                    //$product_status_id  = intval($this->input->post('productStatus', true));
                    $new                = intval($this->input->post('new', true));
                    $price              = strip_tags($this->input->post('price', true));
                    
                    
                    $owner_name         = strip_tags($this->input->post('ownerName', true));
                    $owner_email        = strip_tags($this->input->post('ownerEmail', true));
                    $owner_phone        = strip_tags($this->input->post('ownerPhone', true));
                    $show_owner_data    = intval($this->input->post('contactData', true));
                    $serial             = strip_tags($this->input->post('serial', true));
                    
                    $cat_data = $this->cat_model->get_category_row($category_id);
                    
                    $needs_shipping = $cat_data->needs_shipping;
                    
                    if($this->settings->auto_active_product == 1)
                    {
                        $product_status = 2; // verified
                    }
                    else
                    {
                        $product_status = 1;  // not vertificated
                    }
                    
                    $general_data  = array(
                                            'cat_id'                => $category_id         ,
                                            'store_id'              => $store_id            ,
                                            //'code'                  => $code    ,
                                            //'image'                 => $image   ,
                                            //'cost'                  => $price               ,
                                            'brand_id'              => $brand_id            ,
                                            'serials_per_country'   => 0                    ,
                                            'quantity_per_serial'   => 0                    ,
                                            'shipping'              => $needs_shipping      ,
                                            'new'                   => $new                 ,
                                            'non_serials'           => 1                    ,
                                            'status_id'             => $product_status      , 
                                            'serial'                => $serial              ,
                                            // owner data
                                            'owner_id'              => $user_id             ,
                                            'owner_name'            => $owner_name          ,
                                            'owner_email'           => $owner_email         ,
                                            'owner_phone'           => $owner_phone         ,
                                            'show_owner_data'       => $show_owner_data     ,
                                            'location_country_id'   => $location_country_id ,
                                            'location_city_id'      => $location_city_id
                                          
                                          );
                    
                    $this->products_model->insert_products($general_data);
                    
                    $product_id = $this->db->insert_id();
                    
                    
                    
                    // inert translation data
                    $languages = $this->lang_model->get_active_data_languages();
                    foreach($languages as $lang)
                    {
                        $trans_data = array(
                                                'product_id'    => $product_id     ,
                                                'lang_id'       => $lang->id       ,
                                                'title'         => $product_name   ,
                                                'description'   => $product_desc
                                           );
                                           
                        $this->products_model->insert_products_translation($trans_data);
                    }
                    
                    // insert countries data
                    $countries = $this->countries_model->get_countries($lang_id);
                    foreach($countries as $country)
                    {
                    
                        $products_countries_data     = array(
                                                    'product_id'            => $product_id,
                                                    'country_id'            => $country->id ,
                                                    'price'                 => $price ,
                                                    'reward_points'         => 0 ,
                                                    'active'                => 1,
                                                    'display_home'          => 1,
                                                   
                                                );
                                                
                     
                        $this->products_model->insert_products_countries_prices($products_countries_data);  
                    
                    }
                    
                    // upload images 
                    $first_image_name = $this->upload_images($product_id);
                    
                    $updated_data = array(
                                            'route' => $product_id,
                                            'image' => $first_image_name
                                         );
                    
                    $this->products_model->update_products($product_id, $updated_data);
                    /***************************************/ 
                    
                    if($this->settings->auto_active_product == 0)
                    {
                        $needsVerification = 1;
                        // verificate user phone
                        $this->_verificate_product($product_id, $user_id, $owner_phone, $lang_id);
                    }
                    else
                    {
                        $needsVerification = 0;
                    }
                    
                    $suc_message   = $this->general_model->get_lang_var_translation('product_added_successfully',$lang_id);
                    $output         = array( 
                                                'message'   => $suc_message,
                                                'productId' => $product_id,
                                                'response'  => 1,
                                                'needsVerification' => $needsVerification
                                           );
                
                }
                else
                {
                    $fail_message   = $this->general_model->get_lang_var_translation('no_add_product_permission',$lang_id);
                    $output         = array( 
                                                'message'   => $fail_message,
                                                'response'  => 0
                                           );
                }
            }
        }
        else
        {
            $fail_message   = $this->general_model->get_lang_var_translation('login_error',$lang_id);
            $output         = array( 
                                        'message'   => $fail_message,
                                        'response'  => 0
                                   );
        }
        
        
        //***************LOG DATA***************//
        //insert log
        $this->api_lib->insert_log($user_id, current_url(), 'User products', $agent, $_POST, $output);
        //***************END LOG***************//

        $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));
    }
    
    private function _verificate_product($product_id, $user_id, $user_phone, $lang_id)
    {
        /**
         * Result:
         * 0 : sms not sent 
         * 1 : sent successfully
         * 2 : cant send , product is activated before
         */ 
        $product_data = $this->products_model->get_products_row($product_id);
        
        if($product_data->status_id == 1)
        {
            //-->>Generate code
            $sms_code = rand(1000, 9999);
            
            
            $data = array(
                		   'sms_code' => $sms_code,
                		 );
                    
            $this->products_model->update_products($product_id, $data);
            
            //-->>  send new sms_code
            $sms_res = $this->send_code_sms($sms_code, $user_phone, $lang_id);
            return $sms_res;
            
        }
        else
        {
            return 2;
        }
    }
    
    private function send_code_sms($code, $phone, $lang_id)
    {
        $msg_lang   = $this->general_model->get_lang_var_translation('sms_activation_code', $lang_id);
        $msg = $msg_lang.' : '. $code;
        $res = $this->notifications->send_sms($msg, $phone);
        
        return $res;
    }
    
    public function resend_product_code()
    {
        $product_id = intval($this->input->post('productId', true));
        $email      = strip_tags($this->input->post('email', true));
        $password   = strip_tags($this->input->post('password', true));
        $lang_id    = intval($this->input->post('langId', true));
        
        $agent              = strip_tags($this->input->post('agent', TRUE));
        $user_id            = 0;

        if($this->ion_auth->login($email, $password))
        {
            $product_data = $this->products_model->get_products_row($product_id);
            
            $user     = $this->ion_auth->user()->row();
            $user_id  = $user->id;
            
            if($user_id == $product_data->owner_id)
            {
                $res = $this->_verificate_product($product_id, $user_id, $product_data->owner_phone, $lang_id);
                
                if($res == 1)
                {
                    $message    = $this->general_model->get_lang_var_translation('execution_success', $lang_id);
                    $output     = array( 
                                            'message'   => $message,
                                            'response'  => 1
                                       );
                }
                else if($res == 0)
                {
                    $fail_message   = $this->general_model->get_lang_var_translation('message_not_send', $lang_id);
                    $output         = array( 
                                                'message'   => $fail_message,
                                                'response'  => 0
                                           );
                }
                else if($res == 2)
                {
                    $fail_message   = $this->general_model->get_lang_var_translation('no_permission', $lang_id);
                    $output         = array( 
                                                'message'   => $fail_message,
                                                'response'  => 0
                                           );
                }
                
            }
            else
            {
                $fail_message   = $this->general_model->get_lang_var_translation('no_permission', $lang_id);
                $output         = array( 
                                            'message'   => $fail_message,
                                            'response'  => 1
                                       );
            }
        }
        else
        {
            $fail_message   = $this->general_model->get_lang_var_translation('login_error',$lang_id);
            $output         = array( 
                                        'message'   => $fail_message,
                                        'response'  => 0
                                   );
        }
        
        //***************LOG DATA***************//
        //insert log
        $this->api_lib->insert_log($user_id, current_url(), 'User products - Resend Product code', $agent, $_POST, $output);
        //***************END LOG***************//
        
        $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));
        
    }
    
    public function activate_product_code()
    {
        $email      = strip_tags($this->input->post('email', true));
        $password   = strip_tags($this->input->post('password', true));
        $lang_id    = intval($this->input->post('langId', true));
        
        $product_id = intval($this->input->post('productId', true));
        $code       = intval($this->input->post('code', true));
        
        $agent              = strip_tags($this->input->post('agent', TRUE));
        $user_id            = 0;

        if($this->ion_auth->login($email, $password))
        {
            $product_data = $this->products_model->get_products_row($product_id);
            
            $user     = $this->ion_auth->user()->row();
            $user_id  = $user->id;
            
            if($user_id == $product_data->owner_id)
            {
                if($product_data->status_id == 1)
                {
                    if($product_data->sms_code == $code)
                    {
                        //update product status
                        $updated_data = array(
                                                'status_id' => 2
                                             );
                        
                        $this->products_model->update_products($product_id, $updated_data);
                        
                        $message   = $this->general_model->get_lang_var_translation('code_applied_successfully', $lang_id);
                        $output    = array( 
                                                    'message'   => $message,
                                                    'response'  => 1
                                               );
                    }
                    else
                    {
                        $fail_message   = $this->general_model->get_lang_var_translation('code_not_match', $lang_id);
                        $output         = array( 
                                                    'message'   => $fail_message,
                                                    'response'  => 0
                                               );
                    }
                    
                }
                else
                {
                    $fail_message   = $this->general_model->get_lang_var_translation('no_permission', $lang_id);
                    $output         = array( 
                                                'message'   => $fail_message,
                                                'response'  => 0
                                           );
                }
                
                
            }
            else
            {
                $fail_message   = $this->general_model->get_lang_var_translation('no_permission', $lang_id);
                $output         = array( 
                                            'message'   => $fail_message,
                                            'response'  => 0
                                       );
            }
        }
        else
        {
            $fail_message   = $this->general_model->get_lang_var_translation('login_error',$lang_id);
            $output         = array( 
                                        'message'   => $fail_message,
                                        'response'  => 0
                                   );
        }
        
        //***************LOG DATA***************//
        //insert log
        $this->api_lib->insert_log($user_id, current_url(), 'User products - Activate Product code', $agent, $_POST, $output);
        //***************END LOG***************//
        
        $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));
        
    }
    
    
    public function upload_images($product_id=0, $edit=0)
    {
        $images_count = intval($this->input->post('imagesCount', true));
        
        if($product_id == 0)
        {
            $product_id = intval($this->input->post('productId', true));
        }
        
        $add_image = true;
        $current_count = 0;
        
        if($edit == 1)
        {
            $products_images = $this->products_model->get_product_images($product_id);
            
            if(count($products_images) >= 8)
            {
                $add_image = false;
            }
            else
            {
                $current_count = count($products_images);
            }
        }
        
        if($add_image)
        {
            for($i=1;$i<=$images_count;$i++)
            {
                if($current_count >= 8)
                {
                    break;
                    exit();
                }
                else
                {
                    ${'image_'.$i} = strip_tags($this->input->post('image_'.$i));
                    
                    if(${'image_'.$i} != '' && !is_null(${'image_'.$i}) )
                    {
                        $file_name = $this->uploaded_images->upload_image(${'image_'.$i}, $product_id, $i);
                        $files_names[] = $file_name;
                        $files_array[] = base_url().'assets/uploads/products/'.$file_name;
                        
                        //first image is the default image
                        // rest of images are product images
                        if($i>=1)
                        {
                             $image_array = array(
                                                    'image'     => $file_name,
                                                    'unix_time' => time()
                                                );
                            
                            $this->products_model->insert_image($image_array);
                            
                            $image_id = $this->db->insert_id();
                            
                            $prouct_image_data = array(
                                                          'product_id' => $product_id,
                                                          'image_id'   => $image_id 
                                                      );
                            
                            $this->products_model->insert_image_product($prouct_image_data);
                        }
                        
                    }
                    $current_count++;
                }
            }
            
            return $files_names[0];
        }
        
        
        
        //$output = $files_array;
        //$this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));
    }
    
    public function products_list()
    {
        $email      = strip_tags($this->input->post('email', true));
        $password   = strip_tags($this->input->post('password', true));
        $lang_id    = intval($this->input->post('langId', true));
        $country_id = intval($this->input->post('countryId', true));
        $device_id  = strip_tags($this->input->post('deviceId', true));
        $page       = intval($this->input->post('page', TRUE));
        $type       = intval($this->input->post('type', true));
        
        
        $agent              = strip_tags($this->input->post('agent', TRUE));
        $user_id            = 0;

        /**
         * Type vals
         * 1 => pending
         * 2 => activated
         */
         
        if($this->ion_auth->login($email, $password))
        {
            $user     = $this->ion_auth->user()->row();
            $user_id  = $user->id;
            
            /****************pagination**************/
            if(!$page) $page = 1;
            $limit           = 25;
            $offset          = ($page -1)*$limit;        
            
            
            $conditions = array(
                                    'products.owner_id' => $user_id,
                               );
            
            if($type == 1)
            {
                $conditions['products.status_id'] = 1;
            }
            else if($type == 2)
            {
                $conditions['products.status_id'] = 2;
            }
            
            $products  = $this->products_model->get_products($lang_id ,$limit ,$offset, $country_id, 0, 0, array(), $conditions, 1);
            $currency  = $this->currency->get_country_currency_name($country_id, $lang_id);
            
            if(count($products) != 0)
            {
                foreach($products as $product)
                {
                    $status_conds = array(
                                            'lang_id' => $lang_id,
                                            'status_id' => $product->status_id,
                                         );
                    
                    $product_status     = $this->products_model->get_product_status($status_conds);
                    $product_details    = $this->products_model->get_product_row_details($product->id, $lang_id, $country_id);
                    $product_price_data = $this->products_lib->get_product_price_data($product_details, $country_id, $user_id, $device_id);
                    
                    $product_price  = '';
                    $pic            = '';
                    
                    if($product->image != '')
                    {
                        $pic = base_url().'assets/uploads/products/'.$product->image;
                    }
                    
                    if($product_price_data[0] != $product_price_data[1])
                    {
                        $product_price = $product_price_data[0];
                    }
                    
                    $product_new_price  = $product_price_data[1];
                    
                    
                                         
                    
                    
                    
                    $output[] = array(
                                        'productId'                     => $product->product_id         ,
                                        'categoryId'                    => $product->cat_id             ,
                                        'productName'                   => $product->title              ,
                                        'storeName'                     => $product->store_name         ,
                                        'productPrice'                  => $product_price               ,
                                        'productNewPrice'               => $product_new_price           ,
                                        'productImage'                  => $pic                         ,
                                        'productDescription'            => $product->description        ,
                                        'producuctQuantityPerSerial'    => $product->quantity_per_serial,
                                        'productCurrency'               => $currency                    ,
                                        'new'                           => $product->new                ,
                                        'views'                         => $product->view               ,
                                        'status'                        => $product_status->name        ,
                                        'productStatusId'               => $product->status_id          ,
                                      );
                }
            }
            else
            {
                $fail_message   = $this->general_model->get_lang_var_translation('no_data',$lang_id);
                $output         = array( 
                                            'message'   => $fail_message,
                                            'response'  => 0
                                       );
            }
            
            
        }
        else
        {
            $fail_message   = $this->general_model->get_lang_var_translation('login_error',$lang_id);
            $output         = array( 
                                        'message'   => $fail_message,
                                        'response'  => 0
                                   );
        }
        
        
        //***************LOG DATA***************//
        //insert log
        $this->api_lib->insert_log($user_id, current_url(), 'User products - Products List', $agent, $_POST, $output);
        //***************END LOG***************//

        $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));
                     
    }
    
    public function delete_product()
    {
        $email      = strip_tags($this->input->post('email', true));
        $password   = strip_tags($this->input->post('password', true));
        $lang_id    = intval($this->input->post('langId', true));
        
        $product_id = intval($this->input->post('productId', true));
        
        $agent              = strip_tags($this->input->post('agent', TRUE));
        $user_id            = 0;

        if($this->ion_auth->login($email, $password))
        {
            $user     = $this->ion_auth->user()->row();
            $user_id  = $user->id;
            
            $product_data = $this->products_model->get_products_row($product_id);
            
            if(count($product_data) != 0)
            {
                if($user_id == $product_data->owner_id)
                {
                    if($product_data->status_id == 3)   // if product is sold , do not delete it
                    {
                        $fail_message   = $this->general_model->get_lang_var_translation('cant_delete_sold_product',$lang_id);
                        $output         = array( 
                                                    'message'   => $fail_message,
                                                    'response'  => 0
                                               );
                    }
                    else
                    {
                        $ids_array = array($product_id);
                        $delete_result = $this->products_model->delete_product_data($ids_array, 1);
                        
                        if($delete_result == 1)
                        {
                            $message   = $this->general_model->get_lang_var_translation('record_deleted_successfully',$lang_id);
                            $output    = array( 
                                                    'message'   => $message,
                                                    'response'  => 1
                                               );
                        }
                        else
                        {
                            $output  = array( 
                                                'message'   => $delete_result,
                                                'response'  => 0
                                            );
                        }
                    }
                }
                else
                {
                    $fail_message   = $this->general_model->get_lang_var_translation('no_add_product_permission',$lang_id);
                    $output         = array( 
                                                'message'   => $fail_message,
                                                'response'  => 0
                                           );
                }
            }
            else
            {
                $fail_message   = $this->general_model->get_lang_var_translation('no_product_details',$lang_id);
                $output         = array( 
                                            'message'   => $fail_message,
                                            'response'  => 0
                                       );
            }
            
        }
        else
        {
            $fail_message   = $this->general_model->get_lang_var_translation('login_error',$lang_id);
            $output         = array( 
                                        'message'   => $fail_message,
                                        'response'  => 0
                                   );
        }
        
        //***************LOG DATA***************//
        //insert log
        $this->api_lib->insert_log($user_id, current_url(), 'User products - Delete Product', $agent, $_POST, $output);
        //***************END LOG***************//
        
        $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));
                  
    }
    
    public function user_product_details()
    {
        $email      = strip_tags($this->input->post('email', true));
        $password   = strip_tags($this->input->post('password', true));
        $deviceId   = strip_tags($this->input->post('deviceId', true));
        $lang_id    = intval($this->input->post('langId', true));
        $country_id = intval($this->input->post('countryId', true));
        $product_id = intval($this->input->post('productId', true));
        
        $agent              = strip_tags($this->input->post('agent', TRUE));
        $user_id            = 0;

        if($this->ion_auth->login($email, $password))
        {
            $user     = $this->ion_auth->user()->row();
            $user_id  = $user->id;
            
            $product_data       = $this->products_model->get_product_row_details($product_id, $lang_id, $country_id);
            
            if(count($product_data) != 0)
            {
                $product_price_data = $this->products_lib->get_product_price_data($product_data, $country_id, $user_id, $deviceId);
                $currency           = $this->currency->get_country_currency_name($country_id, $lang_id);
            
                if($user_id == $product_data->owner_id)
                {
                    $status_conditions = array(
                                                'lang_id'   => $lang_id,
                                                'status_id' => $product_data->status_id
                                              );
                    
                    $product_status = $this->products_model->get_product_status($status_conditions);
                    $product_images = $this->products_model->get_product_images($product_id);
                    $images_array   = array();
                    
                    foreach($product_images as $row)
                    {
                        $images_array[] = array(
                                                    'id'    => $row->id,
                                                    'image' => base_url().'assets/uploads/products/'.$row->image
                                                );
                    }
                    
                    $pic                = '';
                    $brand_name         = '';
                    $location_country   = '';
                    $location_city      = '';
                    
                    if($product_data->image != '')
                    {
                        $pic = base_url().'assets/uploads/products/'.$product_data->image;
                    }
                    
                    if($product_data->brand_name)
                    {
                        $brand_name  = $product_data->brand_name;
                    }
                    
                    if($product_data->location_country)
                    {
                        $location_country = $product_data->location_country;
                    }
                    
                    if($product_data->location_city)
                    {
                        $location_city = $product_data->location_city;
                    }
                    
                    $output = array(
                                        'productId'                     => $product_data->product_id            ,
                                        'categoryId'                    => $product_data->cat_id                ,
                                        'brandId'                       => $product_data->brand_id              ,
                                        'catName'                       => $product_data->cat_name              ,
                                        'locationCountryId'             => $product_data->location_country_id   ,
                                        'locationCountry'               => $location_country                    ,
                                        'locationCityId'                => $product_data->location_city_id      ,
                                        'locationCity'                  => $location_city                       ,
                                        'brandName'                     => $brand_name                          ,
                                        'ownerName'                     => $product_data->owner_name            ,
                                        'ownerEmail'                    => $product_data->owner_email           ,
                                        'ownerPhone'                    => $product_data->owner_phone           ,
                                        'showOwnerData'                 => $product_data->show_owner_data       ,
                                        'productName'                   => $product_data->title                 ,
                                        'storeName'                     => $product_data->store_name            ,
                                        'productPrice'                  => $product_price_data[0]               ,
                                        'productNewPrice'               => $product_price_data[0]               ,
                                        'productImage'                  => $pic                                 ,
                                        'productDescription'            => $product_data->description           ,
                                        'producuctQuantityPerSerial'    => $product_data->quantity_per_serial   ,
                                        'productCurrency'               => $currency                            ,
                                        'new'                           => $product_data->new                   ,
                                        'views'                         => $product_data->view                  ,
                                        'status'                        => $product_status->name                ,
                                        'productStatusId'               => $product_data->status_id             ,
                                        'serial'                        => '',
                                        'productsImages'                => $images_array    
                                      );
                }
                else
                {
                    $fail_message   = $this->general_model->get_lang_var_translation('no_add_product_permission', $lang_id);
                    $output         = array( 
                                                'message'   => $fail_message,
                                                'response'  => 0
                                           );
                }
            }
            else
            {
                $fail_message   = $this->general_model->get_lang_var_translation('no_product_details',$lang_id);
                $output         = array( 
                                            'message'   => $fail_message,
                                            'response'  => 0
                                       );
            }
        }
        else
        {
            $fail_message   = $this->general_model->get_lang_var_translation('login_error',$lang_id);
            $output         = array( 
                                        'message'   => $fail_message,
                                        'response'  => 0
                                   );
        }
        
        //***************LOG DATA***************//
        //insert log
        $this->api_lib->insert_log($user_id, current_url(), 'User products - User Product Details', $agent, $_POST, $output);
        //***************END LOG***************//
        
        $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));
        
    }
    
    public function update_product_data()
    {
        $lang_id        = intval($this->input->post('langId', TRUE));
        $email          = strip_tags($this->input->post('email', TRUE));
        $password       = strip_tags($this->input->post('password', TRUE));
        $deviceId       = strip_tags($this->input->post('deviceId', TRUE));
        $country_id     = intval($this->input->post('countryId', TRUE));
        
        // owner data 
        $owner_name     = strip_tags($this->input->post('ownerName', true));
        $owner_email    = strip_tags($this->input->post('ownerEmail', true));
        $owner_phone    = strip_tags($this->input->post('ownerPhone', true));
        $show_owner_data        = intval($this->input->post('contactData', true));
        $location_country_id    = intval($this->input->post('locationCountryId', true));
        $location_city_id       = intval($this->input->post('locationCityId', true));
        
        // product data
        $product_id     = strip_tags($this->input->post('productId', true));
        $product_name   = strip_tags($this->input->post('productName', true));
        $product_desc   = strip_tags($this->input->post('productDescription', true));
        $new            = intval($this->input->post('new', true));
        $category_id    = intval($this->input->post('categoryId', TRUE));
        $brand_id       = intval($this->input->post('brandId', TRUE));
        $price          = intval($this->input->post('price', TRUE));
        
        $agent              = strip_tags($this->input->post('agent', TRUE));
        $user_id            = 0;
        
        $output         = array();
        
        if($this->ion_auth->login($email, $password))
        {
            $user     = $this->ion_auth->user()->row();
            $user_id  = $user->id;
            
            // check user product 
            $product_data = $this->products_model->get_products_row($product_id);
            
            if(count($product_data) == 0)
            {
                $fail_message   = $this->general_model->get_lang_var_translation('no_product_details',$lang_id);
                $output         = array( 
                                            'message'   => $fail_message,
                                            'response'  => 0
                                       );
            }
            else
            {
                if($product_data->owner_id != $user_id)
                {
                    $fail_message   = $this->general_model->get_lang_var_translation('no_add_product_permission',$lang_id);
                    $output         = array( 
                                                'message'   => $fail_message,
                                                'response'  => 0
                                           );
                }
                else
                {
                    //check validation            
                    $product_name_lang          = $this->general_model->get_lang_var_translation('product_name', $lang_id);
                    $product_description_lang   = $this->general_model->get_lang_var_translation('description', $lang_id);
                    $new_lang                   = $this->general_model->get_lang_var_translation('new', $lang_id);
                    $cat_lang                   = $this->general_model->get_lang_var_translation('category', $lang_id);
                    $price_lang                 = $this->general_model->get_lang_var_translation('price', $lang_id);
                    $name_lang                  = $this->general_model->get_lang_var_translation('name', $lang_id);
                    $email_lang                 = $this->general_model->get_lang_var_translation('email', $lang_id);
                    $phone_lang                 = $this->general_model->get_lang_var_translation('phone', $lang_id);
                    $address_lang               = $this->general_model->get_lang_var_translation('address', $lang_id);
                    $required_lang              = $this->general_model->get_lang_var_translation('required', $lang_id);
                    
                    $this->form_validation->set_rules('productName', $product_name_lang, 'required');
                    $this->form_validation->set_rules('productDescription', $product_description_lang, 'required');
                    $this->form_validation->set_rules('new', $new_lang, 'required');
                    $this->form_validation->set_rules('categoryId', $cat_lang, 'required');
                    $this->form_validation->set_rules('price', $price_lang, 'required');
                    $this->form_validation->set_rules('ownerName', $name_lang, 'required');
                    $this->form_validation->set_rules('ownerEmail', $email_lang, 'required');
                    $this->form_validation->set_rules('ownerPhone', $phone_lang, 'required');
                    $this->form_validation->set_rules('locationCountryId', $address_lang, 'required');
                    $this->form_validation->set_rules('locationCityId', $address_lang, 'required');
                    
                    $this->form_validation->set_message('required', $required_lang."  : %s ");
                    
                    if($this->form_validation->run() == FALSE)
            		{
            		   $output = array(
                                        'response' => 0,
                                        'message' => strip_tags(validation_errors())
                                      );
                    }
                    else
                    {
                        $needs_shipping = 1;
                        $general_data  = array(
                                                'cat_id'                => $category_id         ,
                                                'brand_id'              => $brand_id            ,
                                                'serials_per_country'   => 0                    ,
                                                'quantity_per_serial'   => 1                    ,
                                                'shipping'              => $needs_shipping      ,
                                                'new'                   => $new                 ,
                                                //'definition'            => $product_status_id   ,
                                                //'non_serials'           => 0                    ,
                                                //'status_id'             => 1                    , // not vertificated
                                                
                                                // owner data
                                                'owner_id'              => $user_id             ,
                                                'owner_name'            => $owner_name          ,
                                                'owner_email'           => $owner_email         ,
                                                'owner_phone'           => $owner_phone         ,
                                                'show_owner_data'       => $show_owner_data     ,
                                                'location_country_id'   => $location_country_id ,
                                                'location_city_id'      => $location_city_id
                                              );
                                              
                        $this->products_model->update_products($product_id, $general_data);
                        
                        
                        // insert translation data
                        $languages = $this->lang_model->get_active_data_languages();
                        foreach($languages as $lang)
                        {
                            $trans_data = array(
                                                    'product_id'    => $product_id     ,
                                                    'lang_id'       => $lang->id       ,
                                                    'title'         => $product_name   ,
                                                    'description'   => $product_desc
                                               );
                                               
                            $this->products_model->update_products_translation($product_id, $lang->id, $trans_data);
                        }
                        
                        // insert countries data
                        $this->products_model->delete_products_countries($product_id); 
                        
                        $countries = $this->countries_model->get_countries($lang_id);
                        foreach($countries as $country)
                        {
                        
                            $products_countries_data     = array(
                                                        'product_id'            => $product_id,
                                                        'country_id'            => $country->id ,
                                                        'price'                 => $price ,
                                                        'reward_points'         => 0 ,
                                                        'active'                => 1,
                                                        'display_home'          => 1,
                                                       
                                                    );
                                                    
                         
                            $this->products_model->insert_products_countries_prices($products_countries_data); 
                        }
                        
                        if(isset($_POST['imagesCount']) && $_POST['imagesCount'] != 0)
                        {
                            // upload images 
                            $first_image_name = $this->upload_images($product_id, 1);
                        }
                        
                        
                        
                        
                        $message   = $this->general_model->get_lang_var_translation('updated_successfully',$lang_id);
                        $output    = array( 
                                                'message'   => $message,
                                                'response'  => 1
                                           ); 
                        
                    }
                 }
              }
        }
        else
        {
        $fail_message   = $this->general_model->get_lang_var_translation('login_error',$lang_id);
        $output         = array( 
                                    'message'   => $fail_message,
                                    'response'  => 0
                                );
        }

        //***************LOG DATA***************//
        //insert log
        $this->api_lib->insert_log($user_id, current_url(), 'User products - Update Product data', $agent, $_POST, $output);
        //***************END LOG***************//
    
    
        $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));
    }
    
    public function delete_product_image()
    {
        $lang_id        = intval($this->input->post('langId', TRUE));
        $email          = strip_tags($this->input->post('email', TRUE));
        $password       = strip_tags($this->input->post('password', TRUE));
        $deviceId       = strip_tags($this->input->post('deviceId', TRUE));
        $country_id     = intval($this->input->post('countryId', TRUE));
        $product_id     = intval($this->input->post('productId', TRUE));
        //$image_name     = strip_tags($this->input->post('imageName', TRUE));
        $image_id       = strip_tags($this->input->post('imageId', TRUE));
        
        $agent              = strip_tags($this->input->post('agent', TRUE));
        $user_id            = 0;

        if($this->ion_auth->login($email, $password))
        {
            $user     = $this->ion_auth->user()->row();
            $user_id  = $user->id;
            
            $product_data = $this->products_model->get_products_row($product_id);
            
            if(count($product_data) != 0)
            {
            
                if($product_data->owner_id == $user_id)
                {
                    $product_images = $this->products_model->get_product_images($product_id);
                    $images_ids     = array();
                    foreach($product_images as $row)
                    {
                        $images_ids[] = $row->id;
                        if($row->id == $image_id)
                        {
                            $image_data = $row;
                        }
                    }
                    
                    
                    if(in_array($image_id, $images_ids))
                    {
                        $image_name = $image_data->image;
                        //remove image product
                        $this->products_model->delete_product_image($product_id, $image_id);
                        unlink(realpath(APPPATH. '../assets/uploads/products/'.$image_name));
                        
                        $message   = $this->general_model->get_lang_var_translation('record_deleted_successfully', $lang_id);
                        $output    = array( 
                                            'message'   => $message,
                                            'response'  => 1
                                          );
                    }
                    else
                    {
                        $fail_message   = $this->general_model->get_lang_var_translation('no_permission', $lang_id);
                        $output         = array( 
                                                    'message'   => $fail_message,
                                                    'response'  => 0
                                               );
                    }
                }
                else
                {
                    $fail_message   = $this->general_model->get_lang_var_translation('no_add_product_permission',$lang_id);
                    $output         = array( 
                                                'message'   => $fail_message,
                                                'response'  => 0
                                           );
                }
            }
            else
            {
                $fail_message   = $this->general_model->get_lang_var_translation('no_product_details',$lang_id);
                $output         = array( 
                                            'message'   => $fail_message,
                                            'response'  => 0
                                       );
            }
              
        }
        else
        {
            $fail_message   = $this->general_model->get_lang_var_translation('login_error',$lang_id);
            $output         = array( 
                                        'message'   => $fail_message,
                                        'response'  => 0
                                   );
        }
        
        //***************LOG DATA***************//
        //insert log
        $this->api_lib->insert_log($user_id, current_url(), 'User products - Delete Product image', $agent, $_POST, $output);
        //***************END LOG***************//
        
        $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));        
    }
       
     
/************************************************************************/    
}