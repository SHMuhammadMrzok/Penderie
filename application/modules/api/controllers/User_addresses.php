<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class User_addresses extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->library('api_lib');

        $this->load->model('users/users_model');
        $this->load->model('general_model');

    }

    public function index()
    {
        $lang_id            = intval($this->input->post('langId', TRUE));
        $email              = strip_tags($this->input->post('email', TRUE));
        $password           = strip_tags($this->input->post('password', TRUE));
        $deviceId           = strip_tags($this->input->post('deviceId', TRUE));
        $store_country_id   = intval($this->input->post('storeCountryId', TRUE));
        $page               = intval($this->input->post('page', TRUE));


        $output    = array();

        $fail_message = $this->general_model->get_lang_var_translation('execution_fail',$lang_id);
        $success_message = $this->general_model->get_lang_var_translation('execution_success',$lang_id);

       if($this->ion_auth->login($email, $password))
       {
            $user    = $this->ion_auth->user()->row();
            $user_id = $user->id;

            $this->api_lib->check_user_store_country_id($email, $password, $user_id, $store_country_id);

            $conds = array(
                              'user_id' => $user_id,
                          );
            $limit = 25;
            if(!$page) $page = 1;
            $offset  = ($page -1 ) * $limit;

            $address_list = $this->users_model->get_result_data_where('user_addresses', 'result', $conds, $limit, $offset);

            foreach($address_list as $row)
            {

              $output[] = array(
                                  'id'              => $row->id,
                                  'title'           => $row->title,
                                  'address'         => $row->address,
                                  'lat'             => $row->lat,
                                  'lng'             => $row->lng,
                                  'defaultAddress'  => $row->default_add,
                                  'cityName'        => $row->city
                               );
            }
       }
       else
       {
            $login_error_message = $this->general_model->get_lang_var_translation('login_error', $lang_id);
            $output = array(
              'message'  => $login_error_message,
              'response' => 0
            );
       }

       $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));
    }

    public function address_details()
    {
      $lang_id = intval($this->input->post('langId', TRUE));

      $title_lang    = $this->general_model->get_lang_var_translation('title', $lang_id);
      $address_lang  = $this->general_model->get_lang_var_translation('address', $lang_id);
      $location_lang = $this->general_model->get_lang_var_translation('site_map', $lang_id);
      $required_lang = $this->general_model->get_lang_var_translation('required', $lang_id);

      $this->form_validation->set_rules('title', $title_lang, 'required');
      $this->form_validation->set_rules('address', $address_lang, 'required');
      $this->form_validation->set_rules('lat', $location_lang, 'required');
      $this->form_validation->set_rules('lng', $location_lang, 'required');

      $this->form_validation->set_message('required', $required_lang."  : %s ");
      $this->form_validation->set_error_delimiters('', '');

      if($this->form_validation->run() == FALSE)
      {
          $message = validation_errors();
          $output = array(
                               'message'  => $message,
                               'response' => '0'
                             );
      }
      else {
        $email              = strip_tags($this->input->post('email', TRUE));
        $password           = strip_tags($this->input->post('password', TRUE));
        $deviceId           = strip_tags($this->input->post('deviceId', TRUE));
        $store_country_id   = strip_tags($this->input->post('storeCountryId', TRUE));

        if($this->ion_auth->login($email, $password))
        {
          $user    = $this->ion_auth->user()->row();
          $user_id = $user->id;

          $title        = strip_tags($this->input->post('title', true));
          $address      = strip_tags($this->input->post('address', true));
          $lat          = strip_tags($this->input->post('lat', true));
          $lng          = strip_tags($this->input->post('lng', true));
          $default_add  = intval($this->input->post('defaultAddress', true));
          $city         = strip_tags($this->input->post('city', TRUE));

          $id    = intval($this->input->post('id', true));

          if($default_add == 1)
          {
            //update user old address to be not default_add
            $conds = array(
                            'user_id' => $user_id,
                            'default_add' => 1
                          );
            $updated_data = array('default_add' => 0);
            $this->users_model->update_table_data('user_addresses', $conds, $updated_data);

          }

          $new_data = array(
                              'user_id' => $user_id ,
                              'title'   => $title   ,
                              'address' => $address ,
                              'lat'     => $lat     ,
                              'lng'     => $lng     ,
                              'default_add' => $default_add,
                              'city'    => $city
                            );
          if($id == 0)
          {
            $this->users_model->insert_table_data('user_addresses', $new_data);

            $response = 1;
            $message  = $this->general_model->get_lang_var_translation('added_successfully', $lang_id);
          }
          else {

            $conds = array(
                            'id' => $id
                          );

            $address_row = $this->users_model->get_result_data_where('user_addresses', 'row', $conds);
            if($address_row->user_id == $user_id)
            {
              $this->users_model->update_table_data('user_addresses', $conds, $new_data);

              $response = 1;
              $message  = $this->general_model->get_lang_var_translation('updated_successfully', $lang_id);
            }
            else {
              $response = 0;
              $message  = $this->general_model->get_lang_var_translation('no_permission', $lang_id);
            }
          }

          $output = array(
            'message'  => $message,
            'response' => $response
          );

        }
        else
        {
          $login_error_message = $this->general_model->get_lang_var_translation('login_error', $lang_id);
          $output = array(
            'message'  => $login_error_message,
            'response' => 0
          );
        }
      }
      $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));

    }

/************************************************************************/
}
