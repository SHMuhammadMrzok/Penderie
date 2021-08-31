<?php
    if(!isset($params))
    {
        $params = array();
    }

    $this->load->library('admin_bootstrap', $params);

    $this->load->library('notifications');

    $this->data['module']     = $this->admin_bootstrap->get_opened_module();//get_module();
    $this->data['opened_module']     = $this->admin_bootstrap->get_opened_module();
    $this->data['controller'] = $this->admin_bootstrap->get_controller();
    $this->data['method']     = $this->admin_bootstrap->get_method();
    $this->data['controller_id'] = $this->admin_bootstrap->get_controller_id();
    $this->data['method_id']  = $this->admin_bootstrap->get_method_id();

    $this->data['data_languages']         = $this->admin_bootstrap->get_data_languages();
    $this->data['structure_languages']    = $this->admin_bootstrap->get_structure_languages();
    $this->data['active_language']        = $this->admin_bootstrap->get_active_language_row();
    $this->data['active_countries']       = $this->admin_bootstrap->get_countries_data();

    $this->data['user_id']                = $this->admin_bootstrap->get_user_id();
    $this->data['user']                   = $this->admin_bootstrap->get_user_data();

    $this->data['lang_id']                = $this->admin_bootstrap->get_current_lang_id();

    $this->data['store_owner']            = $this->admin_bootstrap->check_user_store_owner();
    $this->data['is_driver']              = $this->admin_bootstrap->check_if_driver();

    $settings                             = $this->admin_bootstrap->get_settings();
    $site_settings                        = $this->admin_model->get_site_settings();

    foreach($settings as $key => $value)
    {
        $this->config->set_item($key, $value);
    }

    foreach ($site_settings as $key => $row)
    {
      $this->config->set_item($row->field, $row->value);
    }

    $user_stores   = $this->admin_bootstrap->get_user_available_stores();

    $store_id_array = array();

    foreach($user_stores as $store)
    {
        $store_id_array[] = $store->store_id;
    }

    $stores_ids = $store_id_array;
    $this->data['stores_ids'] = $stores_ids;

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
?>
