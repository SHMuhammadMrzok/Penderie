<?php
if(!Defined('BASEPATH'))
  exit('No direct script access allowed');

  class Send_as_gift extends CI_Controller
  {
    public function __construct()
    {
        parent::__construct();

        $this->load->library('api_lib');
        $this->load->library('currency');
        $this->load->library('shopping_cart');
        $this->load->model('general_model');
        $this->load->model('wrapping/admin_wrapping_model');
        $this->load->library('api_lib');
    }

    public function wrapping_data()
    {
        $lang_id    = intval($this->input->post('langId', true));
        $country_id = intval($this->input->post('countryId', true));

        // Added for api log
        $email              = strip_tags($this->input->post('email', TRUE));
        $password           = strip_tags($this->input->post('password', TRUE));  
        $agent              = strip_tags($this->input->post('agent', TRUE));
        $user_id            = 0;

        if($this->ion_auth->login($email, $password))
        {
            $user_data  = $this->ion_auth->user()->row();
            $user_id    = $user_data->id;
        }
        ///

        $options        = array();
        $wrapping_array = array();
        $output         = array();

        $wrapping_type_lang   = $this->general_model->get_lang_var_translation('wrapping', $lang_id);
        $ribbon_type_lang   = $this->general_model->get_lang_var_translation('ribbon', $lang_id);
        $box_type_lang   = $this->general_model->get_lang_var_translation('box', $lang_id);

        $wrapping = $this->admin_wrapping_model->get_wrapping_type_data($lang_id, 1);
        $ribbons  = $this->admin_wrapping_model->get_wrapping_type_data($lang_id, 2);
        $boxes    = $this->admin_wrapping_model->get_wrapping_type_data($lang_id, 3);

        $currency = $this->currency->get_country_currency_name($country_id, $lang_id);

        if(count($wrapping) !=0)
        {
          foreach($wrapping as $row)
          {
              $wrapping_array[] = array(
                                          'id'    => $row->id,
                                          'color' => $row->color,
                                          'cost'  => $row->cost.' '.$currency,
                                          'image' => base_url().'assets/uploads/'.$row->image
                                      );
          }

          $output[] = array(
                            'title'   => $wrapping_type_lang,
                            'type'    => 1,
                            'options' => $wrapping_array
                        );

        }

        if(count($ribbons) !=0)
        {
          foreach($ribbons as $row)
          {
              $ribbons_array[] = array(
                                          'id'    => $row->id,
                                          'color' => $row->color,
                                          'cost'  => $row->cost.' '.$currency,
                                          'image' => base_url().'assets/uploads/'.$row->image
                                      );
          }

          $output[] = array(
                              'title'   => $ribbon_type_lang,
                              'type'    => 2,
                              'options' => $ribbons_array,
                          );
        }

        if(count($boxes) !=0)
        {
          foreach($boxes as $row)
          {
              $boxes_array[] = array(
                                          'id'    => $row->id,
                                          'color' => $row->color,
                                          'cost'  => $row->cost.' '.$currency,
                                          'image' => base_url().'assets/uploads/'.$row->image
                                      );
          }

          $output[] = array(
                  'title'   => $box_type_lang,
                  'type'    => 3,
                  'options' => $boxes_array,
              );

        }

        /*$output = array(
                             array(
                                    'title'   => $wrapping_type_lang,
                                    'type'    => 1,
                                    'options' => $wrapping_array
                                ),
                            array(
                                    'title'   => $ribbon_type_lang,
                                    'type'    => 2,
                                    'options' => $ribbons_array,
                                ),
                            array(
                                    'title'   => $box_type_lang,
                                    'type'    => 3,
                                    'options' => $boxes_array,
                                )
                        );
                        */

        //***************LOG DATA***************//
        //insert log
        $this->api_lib->insert_log($user_id, current_url(), 'Send as gift', $agent, $_POST, $output);
        //***************END LOG***************//

        $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));
    }

    public function update_cart_wrapping_data()
    {
        $lang_id        = strip_tags($this->input->post('langId', TRUE));
        $userId         = strip_tags($this->input->post('userId', TRUE));
        $deviceId       = strip_tags($this->input->post('deviceId', TRUE));
        $country_id     = strip_tags($this->input->post('countryId', TRUE));
        $not_gift       = intval($this->input->post('notGift', true));
        $ip_address     = $this->input->ip_address();

        $email          = strip_tags($this->input->post('email', TRUE));
        $password       = strip_tags($this->input->post('password', TRUE));

        $wrapping_id    = intval($this->input->post('wrappingId', true));
        $ribbon_id      = intval($this->input->post('ribbonId', true));
        $box_id         = intval($this->input->post('boxId', true));
        $gift_msg       = strip_tags($this->input->post('giftMsg', true));

        // Added for api log
        $agent              = strip_tags($this->input->post('agent', TRUE));
        $user_id            = 0;

        if($this->ion_auth->login($email, $password))
        {
            $user_data  = $this->ion_auth->user()->row();
            $user_id    = $user_data->id;
            $this->api_lib->check_user_store_country_id($email, $password, $user_data->id, $country_id);
            
        }
        ///

        $wrapping_type_lang = $this->general_model->get_lang_var_translation('wrapping', $lang_id);
        $ribbon_type_lang   = $this->general_model->get_lang_var_translation('ribbon', $lang_id);
        $box_type_lang      = $this->general_model->get_lang_var_translation('box', $lang_id);
        $required_lang      = $this->general_model->get_lang_var_translation('required', $lang_id);

        $this->shopping_cart->set_user_data($user_id, $deviceId, $ip_address , $country_id ,$lang_id);

        $cart_data      = $this->shopping_cart->shopping_cart_data();

        if($not_gift == 1)
        {
          //reset cart gift data
          $updated_data   = array(
                                      'send_as_gift'          => 0  ,
                                      'wrapping_id'           => 0  ,
                                      'ribbon_id'             => 0  ,
                                      'box_id'                => 0  ,
                                      'wrapping_only_cost'    => 0  ,
                                      'ribbon_only_cost'      => 0  ,
                                      'box_only_cost'         => 0  ,
                                      'wrapping_cost'         => 0  ,
                                      'gift_msg'              => ''
                                    );

          if($this->shopping_cart->update_cart($cart_data->id, $updated_data, 1))
          {
            $message  = '';//$this->general_model->get_lang_var_translation('updated_successfully', $lang_id);
            $response = 1;
            $output = array(
                               'message'   => $message,
                               'response'  => $response
                           );
          }
        }
        else {


          $this->form_validation->set_rules('wrappingId', $wrapping_type_lang, 'required');
          //$this->form_validation->set_rules('ribbonId', $ribbon_type_lang, 'required');
          //$this->form_validation->set_rules('boxId', $box_type_lang, 'required');
          $this->form_validation->set_message('required', $required_lang."  : %s ");



        if ($this->form_validation->run() == FALSE)
		    {

		        $output = array(
                                'message'   => strip_tags(validation_errors()),
                                'response'  => 0
                            );
        }
        else
        {
            //$wrapping_data  = $this->admin_wrapping_model->get_wrapping_data($wrapping_id);

            $wrapping_data  = $this->admin_wrapping_model->get_wrapping_row($wrapping_id);
            $ribbon_data    = $this->admin_wrapping_model->get_wrapping_row($ribbon_id);
            $box_data       = $this->admin_wrapping_model->get_wrapping_row($box_id);

            $total_cost     = $wrapping_data->cost + $ribbon_data->cost + $box_data->cost;

            $wrapping_cost  = $wrapping_data->cost;

            $updated_data   = array(
                                        'send_as_gift'          => 1,
                                        'wrapping_id'           => $wrapping_id,
                                        'ribbon_id'             => $ribbon_id,
                                        'box_id'                => $box_id,
                                        'wrapping_only_cost'    => $wrapping_data->cost ,
                                        'ribbon_only_cost'      => $ribbon_data->cost   ,
                                        'box_only_cost'         => $box_data->cost      ,
                                        'wrapping_cost'         => $total_cost          ,
                                        'gift_msg'              => $gift_msg
                                        );

            if($this->shopping_cart->update_cart($cart_data->id, $updated_data, 1))
            {
                $message  = $this->general_model->get_lang_var_translation('updated_successfully', $lang_id);
                $response = 1;
            }
            else
            {
                $message  = $this->general_model->get_lang_var_translation('not_updated', $lang_id);
                $response = 0;
            }

             $output = array(
                                'message'   => $message,
                                'response'  => $response
                            );
        }
      }

        //***************LOG DATA***************//
        //insert log
        $this->api_lib->insert_log($user_id, current_url(), 'Send as gift - Update cart wrapping data', $agent, $_POST, $output);
        //***************END LOG***************//

        $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));

    }

  }
