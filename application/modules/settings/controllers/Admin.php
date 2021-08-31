<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Admin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

         require(APPPATH . 'includes/global_vars.php');

        $this->load->model('admin_model');
        $this->load->model('categories/cat_model');
        $this->load->model('stores/stores_model');
        $this->load->model('shipping/Companies_model');

        $this->load->library('encryption');
        $this->config->load('encryption_keys');
    }

     private function _js_and_css_files()
     {
        $this->data['css_files'] = array();

        $this->data['js_files']  = array(
            //Date Range Picker
            'global/plugins/bootstrap-daterangepicker/moment.min.js',

            );

        $this->data['js_code'] = "";
    }


    public function index()
    {
        $lang_id = $this->data['active_language']->id;

        $this->data['count_all_records']    = $this->admin_model->get_count_all_settings($lang_id);
        $this->data['data_language']        = $this->lang_model->get_active_data_languages();

        $this->data['columns']              = array(
                                                     lang('site_name')
                                                   );

        $this->data['orders']                = array(
                                                     lang('site_name')
                                                   );


        $this->data['actions']              = array( 'delete'=>lang('delete'));

        $this->data['content']  = $this->load->view('Admin/grid/grid_html', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
    }

    public function ajax_list()
    {
        if(isset($_POST['lang_id']))
        {
            $lang_id = intval($this->input->post('lang_id'));
        }else{
            $lang_id = $this->data['active_language']->id;
        }
        if(isset($_POST['limit']))
        {
            $limit = intval($this->input->post('limit'));
        }else{
            $limit = 1;
        }

        if(isset($_POST['page_number']))
        {
            $active_page = intval($this->input->post('page_number'));
        }else{
            $active_page = 1;
        }

        $offset  = ($active_page-1) * $limit;


        if(isset($_POST['search_word']) || trim($_POST['search_word']) == '')
        {
            $search_word = $this->input->post('search_word');
        }
        else
        {
            $search_word = '';
        }

        if(isset($_POST['order_by']))
        {
            $order_by = $this->input->post('order_by');
        }
        else
        {
            $order_by = '';
        }

        if(isset($_POST['order_state']))
        {
            $order_state = $this->input->post('order_state');
        }
        else
        {
            $order_state = 'desc';
        }


        $grid_data       = $this->admin_model->get_settings_data($lang_id,$limit,$offset,$search_word,$order_by,$order_state);

        $db_columns      = array(
                                 'id' ,
                                 'site_name'
                                );

       $this->data['hidden_fields'] = array('id','sort');

       $new_grid_data = array();

        foreach($grid_data as $key =>$row)
        {

            foreach($db_columns as $column)
            {
                $new_grid_data[$key][$column] = $row->{$column};

            }
        }

        $this->data['unset_delete']      = 'true';
        $this->data['unset_add']         = 'true';
        $this->data['grid_data']         = $new_grid_data;
        $this->data['count_all_records'] = $this->admin_model->get_count_all_settings($lang_id,$search_word);
        $this->data['display_lang_id']   = $lang_id;

        $output_data = $this->load->view('Admin/grid/grid_data',$this->data, true);
        $count_data  = $this->data['count_all_records'];

        echo json_encode(array($output_data, $count_data, $search_word));
    }


    public function read($id)
    {
        $id = intval($id);
        $wholesaler_groups = '';
        $active_lang_id    = $this->data['active_language']->id;

        if($id)
        {
            $data     = $this->admin_model->get_row_data($id);

            $new_user_customer_group    = $this->admin_model->get_customer_group_name($data->new_user_customer_group_id  , $active_lang_id);
            $rep_customer_group         = $this->admin_model->get_customer_group_name($data->rep_group_id  , $active_lang_id);

            $wholesaler_ids             = json_decode($data->wholesaler_customer_group_id);
            $wholesaler_groups_data     = $this->admin_model->get_wholesaler_groups_data($wholesaler_ids, $active_lang_id);

            foreach($wholesaler_groups_data as $group)
            {
                $wholesaler_groups .= $group->title.' ,';
            }


            $row_data = array(
                                lang('default_lang')                => $data->lang_name                                 ,
                                lang('default_country')             => $data->country_name                              ,
                                lang('wholesaler_customer_group')   => $wholesaler_groups                               ,
                                lang('new_user_customer_group')     => $new_user_customer_group->title                  ,
                                lang('rep_customer_group')          => $rep_customer_group->title                       ,
                                lang('incorrect_login_email')       => $data->incorrect_login_email                     ,
                                lang('site_email')                  => implode(" , ",json_decode($data->email))         ,
                                lang('site_telephones')             => implode(" , ",json_decode($data->telephone))     ,
                                lang('site_mobile')                 => implode(" , ",json_decode($data->mobile))        ,
                                lang('site_fax')                    => $data->fax                                       ,
                                lang('facebook')                    => $data->facebook                                  ,
                                lang('twitter')                     => $data->twitter                                   ,
                                lang('youtube')                     => $data->youtube                                   ,
                                lang('instagram')                   => $data->instagram                                 ,
                                lang('min_product_stock')           => $data->min_product_stock                         ,
                                lang('min_order_hours')             => $data->min_order_hours                           ,
                                lang('site_name')                   => $data->site_name                                 ,
                                lang('site_address')                => implode(" , ",json_decode($data->address))       ,
                                lang('site_keywords')               => $data->keywords                                  ,
                                lang('site_description')            => $data->description                               ,
                             );



            $this->data['row_data'] = $row_data;

            $this->data['content']  = $this->load->view('Admin/grid/read_view', $this->data, true);
            $this->load->view('Admin/main_frame',$this->data);

        }
    }

    public function do_action()
    {
        $action = $this->input->post('action');
        if($action == 'delete')
        {
            $this->delete();
        }
    }

    public function edit($id)
    {
        if(is_numeric($id))
        {
            $id = intval($id);

            $validation_msg = false;

            if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
            {
                $validation_msg = true;

                $languages  = $this->input->post('lang_id');
                $id         = $this->input->post('id');

                foreach($languages as $lang_id)
                {
                    $this->form_validation->set_rules('site_name['.$lang_id.']', lang('site_name'), 'trim|required');
                }

                $this->form_validation->set_rules('sender_email', lang('sender_email'), 'valid_email|trim|required');
                $this->form_validation->set_rules('incorrect_login_email', lang('incorrect_login_email'), 'valid_email|trim|required');
                $this->form_validation->set_rules('image2', lang('fav_ico'), 'required');
                $this->form_validation->set_rules('image', lang('logo'), 'required');

                $this->form_validation->set_message('valid_email', lang('valid_email')."  : %s ");
                $this->form_validation->set_message('required', lang('required'));
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            }

            if($this->form_validation->run() == FALSE)
        		{
        		   $this->_edit_form($id, $validation_msg);
            }
            else
            {

                $default_lang                   = $this->input->post('default_lang', true);
                $default_country                = $this->input->post('default_country', true);
                $email                          = $this->input->post('email', true);
                $sender_email                   = $this->input->post('sender_email', true);
                $incorrect_login_email          = $this->input->post('incorrect_login_email', true);
                $telephone                      = $this->input->post('telephone', true);
                $mobile                         = $this->input->post('mobile', true);
                $fax                            = $this->input->post('fax', true);
                $whats_app_number               = $this->input->post('whats_app_number', true);
                $facebook                       = $this->input->post('facebook', true);
                $twitter                        = $this->input->post('twitter', true);
                $youtube                        = $this->input->post('youtube', true);
                $instagram                      = $this->input->post('instagram', true);
                $linkedin                       = $this->input->post('linkedin', true);
                $googleapi_key                  = $this->input->post('googleapi_key', true);
                $wholesaler_customer_group_id   = $this->input->post('wholesaler_customer_group_id', true);
                $new_user_group_id              = $this->input->post('new_user_customer_group_id', true);
                $rep_group_id                   = $this->input->post('rep_group_id', true);
                $min_product_stock              = $this->input->post('min_product_stock', true);
                $rest_product_qty               = $this->input->post('rest_product_qty', true);
                $min_order_hours                = $this->input->post('min_order_hours', true);
                $locator_max_distance           = $this->input->post('locator_max_distance', true);
                $admin_notification_lang_id     = $this->input->post('admin_notification_lang_id', true);
                $home_delivery                  = isset( $_POST['home_delivery'])? $this->input->post('home_delivery', true):0;
                $shipping                       = isset( $_POST['shipping'])? $this->input->post('shipping', true):0;
                $recieve_from_branch            = isset( $_POST['recieve_from_branch'])? $this->input->post('recieve_from_branch', true):0;
                $user_address                   = isset( $_POST['user_address'])? $this->input->post('user_address', true):0;
                $min_order_delivery             = $this->input->post('min_order_for_delivery', true);
                $locator_type                   = $this->input->post('locator_type', true);
                $categories_vertical_limit      = intval($this->input->post('categories_vertical_limit', true));
                $menu_horizontal_limit          = intval($this->input->post('menu_horizontal_limit', true));
                $products_limit                 = intval($this->input->post('products_limit', true));
                $first_store_id                 = intval($this->input->post('first_store_id', true));
                $second_store_id                = intval($this->input->post('second_store_id', true));
                $third_store_id                 = intval($this->input->post('third_store_id', true));
                $fourth_store_id                = intval($this->input->post('fourth_store_id', true));
                $fifth_store_id                 = intval($this->input->post('fifth_store_id', true));
                $default_store_id               = intval($this->input->post('default_store_id', true));
                $vat_percent                    = strip_tags($this->input->post('vat_percent', true));
                $vat_type                       = intval($this->input->post('vat_type', true));
                $fav_ico                        = strip_tags($this->input->post('image2', true));
                $logo                           = strip_tags($this->input->post('image', true));
                $login_background               = strip_tags($this->input->post('image3', true));
                $register_background            = strip_tags($this->input->post('image4', true));
                $forget_password_background     = strip_tags($this->input->post('image5', true));
                $max_blocks                     = intval($this->input->post('max_blocks', true));
                $maintenance_cat_id             = intval($this->input->post('maintenance_cat_id'));
                $auto_active_product            = isset( $_POST['auto_active_product'])? $this->input->post('auto_active_product', true):0;
                $android_app_link               = strip_tags($this->input->post('android_app_link', true));
                $ios_app_link                   = strip_tags($this->input->post('ios_app_link', true));
                $drivers_group_id               = intval($this->input->post('drivers_group_id'));
                $return_days                    = intval($this->input->post('return_days'));
                $google_map_key                 = strip_tags($this->input->post('google_map_key', true));
                $seller_video                   = strip_tags($this->input->post('seller_video', true));
                $allow_user_auth                = intval($this->input->post('allow_user_auth', true));
                $tax_number                     = strip_tags($this->input->post('tax_number', true));
                $toaster_seconds                = intval($this->input->post('toaster_seconds', true));
                $default_shipping_company_id    = strip_tags($this->input->post('shipping_Company', true));
                $map_country_lat                = strip_tags($this->input->post('map_country_lat'));
                $map_country_lng                = strip_tags($this->input->post('map_country_lng'));

                $email      = explode("\n", $email);
                $telephone  = explode("\n", $telephone);
                $mobile     = explode("\n", $mobile);

                $email      = json_encode($email);
                $telephone  = json_encode($telephone);
                $mobile     = json_encode($mobile);

                $wholesaler_customer_group_ids = json_encode($wholesaler_customer_group_id);

                if($locator_type == 1)
                {
                    $locator_type == 'approximate';
                }
                else if($locator_type == 2)
                {
                    $locator_type == 'google_api';
                }

                $general_data = array(
                                        'default_lang'                 => $default_lang                     ,
                                        'default_country'              => $default_country                  ,
                                        'email'                        => $email                            ,
                                        'sender_email'                 => $sender_email                     ,
                                        'incorrect_login_email'        => $incorrect_login_email            ,
                                        'telephone'                    => $telephone                        ,
                                        'mobile'                       => $mobile                           ,
                                        'whats_app_number'             => $whats_app_number                 ,
                                        'fax'                          => $fax                              ,
                                        'facebook'                     => $facebook                         ,
                                        'twitter'                      => $twitter                          ,
                                        'youtube'                      => $youtube                          ,
                                        'instagram'                    => $instagram                        ,
                                        'linkedin'                     => $linkedin                         ,
                                        'googleapi_key'                => $googleapi_key                    ,
                                        'min_product_stock'            => $min_product_stock                ,
                                        'rest_product_qty'             => $rest_product_qty                 ,
                                        'min_order_hours'              => $min_order_hours                  ,
                                        'rep_group_id'                 => $rep_group_id                     ,
                                        'new_user_customer_group_id'   => $new_user_group_id                ,
                                        'wholesaler_customer_group_id' => $wholesaler_customer_group_ids    ,
                                        'admin_notification_lang_id'   => $admin_notification_lang_id       ,
                                        'home_delivery'                => $home_delivery                    ,
                                        'shipping'                     => $shipping                         ,
                                        'recieve_from_branch'          => $recieve_from_branch              ,
                                        'user_address'                 => $user_address                     ,
                                        'locator_max_distance'         => $locator_max_distance             ,
                                        'min_order_for_delivery'       => $min_order_delivery               ,
                                        'locator_type'                 => $locator_type                     ,
                                        'categories_vertical_limit'    => $categories_vertical_limit        ,
                                        'menu_horizontal_limit'        => $menu_horizontal_limit            ,
                                        'products_limit'               => $products_limit                   ,
                                        'first_store_id'               => $first_store_id                   ,
                                        'second_store_id'              => $second_store_id                  ,
                                        'third_store_id'               => $third_store_id                   ,
                                        'fourth_store_id'              => $fourth_store_id                  ,
                                        'fifth_store_id'               => $fifth_store_id                   ,
                                        'default_store_id'             => $default_store_id                 ,
                                        'vat_percent'                  => $vat_percent                      ,
                                        'vat_type'                     => $vat_type                         ,
                                        'fav_ico'                      => $fav_ico                          ,
                                        'logo'                         => $logo                             ,
                                        'login_background'             => $login_background                 ,
                                        'register_background'          => $register_background              ,
                                        'forget_password_background'   => $forget_password_background       ,
                                        'max_blocks'                   => $max_blocks                       ,
                                        'maintenance_cat_id'           => $maintenance_cat_id               ,
                                        'auto_active_product'          => $auto_active_product              ,
                                        'android_app_link'             => $android_app_link                 ,
                                        'ios_app_link'                 => $ios_app_link                     ,
                                        'drivers_group_id'             => $drivers_group_id                 ,
                                        'return_days'                  => $return_days                      ,
                                        'google_map_key'               => $google_map_key                   ,
                                        'seller_video'                 => $seller_video                     ,
                                        'allow_user_auth'              => $allow_user_auth                  ,
                                        'tax_number'                   => $tax_number                       ,
                                        'toaster_seconds'              => $toaster_seconds                  ,
                                        'default_shipping_company_id'  => $default_shipping_company_id      ,
                                        'map_country_lat'              => $map_country_lat                  ,
                                        'map_country_lng'              => $map_country_lng
                                      );

                $this->admin_model->update_settings($id ,$general_data);

                $lang_id	   = $this->input->post('lang_id');
                $site_name   = $this->input->post('site_name');
                $address     = $this->input->post('address');
                $keywords    = $this->input->post('keywords');
                $description = $this->input->post('description');


                foreach($languages as $lang_id)
                {
                    $address[$lang_id]    = explode("\n", $address[$lang_id]);
                    $address[$lang_id]    = json_encode($address[$lang_id]);

                    $settings_translation_data = array(
                                                        'site_name'     => $site_name[$lang_id] ,
                                                        'address'       => $address[$lang_id],
                                                        'keywords'      => $keywords[$lang_id],
                                                        'description'   => $description[$lang_id],
                                                    );

                    $this->admin_model->update_settings_translation($id ,$lang_id ,$settings_translation_data);
               }

                $_SESSION['success'] = lang('success');
                $this->session->mark_as_flash('success');

                redirect('settings/admin/', 'refresh');
            }
        }


    }

    private function _edit_form($id, $validation_msg)
    {
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }

        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/edit/" . $id;
        $this->data['id']           = $id;
        $lang_id                    = $this->data['active_language']->id;
        $languages                  = $this->data['data_languages'];

        $cat_conds = array(
                        'parent_id'     => 0,
                      );

        $categories         = $this->cat_model->get_categories($lang_id, 0, 1, $cat_conds);

        $general_data       = $this->admin_model->get_settings_row($id);
        $data               = $this->admin_model->get_sttings_translation_result($id);
        $db_languages       = $this->admin_model->get_languages();
        $countries          = $this->admin_model->get_countries($lang_id);
        $customer_groups    = $this->admin_model->get_customer_groups($lang_id);
        $stores             = $this->stores_model->get_menu_stores($lang_id);
        $groups             = $this->admin_model->get_groups($lang_id);
        
        $limit = 0;
        $offset = 0;

        $get_Compinies   = $this->Companies_model->get_shipping_companies_result($lang_id);
                         
        $lang_options    = array();
        $lang_array      = array();
        $filtered_data   = array();
        $country_options = array();
        $groups_options  = array();
        $stores_array    = array();
        $groups_array    = array();
        $all_Copinies    = array();

        $stores_array[NULL] = '------------------';

        foreach($db_languages as $row)
        {
            $lang_options[$row->id] = $row->name;
        }

        foreach($languages as $row)
        {
            $lang_array[$row->id] = $row->name;
        }

        foreach($countries as $row)
        {
            $country_options[$row->id] = $row->name ."    :    ".$row->currency;
        }

        foreach($customer_groups as $row)
        {
            $groups_options[$row->customer_group_id]  = $row->title;
        }

        foreach($data as $row)
        {
            $filtered_data[$row->lang_id] = $row;
        }

        foreach($stores as $row)
        {
            $stores_array[$row->store_id] = $row->name;
        }

        foreach($categories as $row)
        {
            $cats_array[$row->id] = $row->name;
        }

        foreach($groups as $row)
        {
            $groups_array[$row->group_id] = $row->name;
        }
        
        foreach($get_Compinies as $row)
        {
            $all_Copinies[$row->id]  = $row->name;
        }

        $wholesaler_groups = json_decode($general_data->wholesaler_customer_group_id);
        $general_data->wholesaler_customer_group_id = $wholesaler_groups;



        $this->data['general_data']    = $general_data;
        $this->data['data']            = $filtered_data;
        $this->data['lang_options']    = $lang_options;
        $this->data['lang_ids']        = $lang_array;
        $this->data['country_options'] = $country_options;
        $this->data['groups_options']  = $groups_options;
        $this->data['users_groups']    = $groups_array;
        $this->data['stores']          = $stores_array;
        $this->data['categories']      = $cats_array;
        $this->data['compinies']       = $all_Copinies;
        
        
        //echo"<pre>";print_r($this->data['compinies']);die();

        $this->data['content']         = $this->load->view('admin_settings', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
    }

/************************************************************************/
}
