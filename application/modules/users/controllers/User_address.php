<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class User_address extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        require(APPPATH . 'includes/front_end_global.php');
        require(APPPATH . 'libraries/PHPGangsta_GoogleAuthenticator.php');

        $this->load->model('users_model');

    }

    public function list()
    {
      if(!$this->ion_auth->logged_in())
      {
          $this->session->set_flashdata('not_allow',lang('please_login_first'));
          redirect('User_login', 'refresh');
      }
      else
      {
        $conds = array(
                          'user_id' => $this->data['user_id'],
                      );
        $address_list = $this->users_model->get_result_data_where('user_addresses', 'result', $conds);

        $this->data['list'] = $address_list;
        $this->data['user_address'] = true;

        $this->data['content'] = $this->load->view('address_list', $this->data, true);
        $this->load->view('site/main_frame', $this->data);

      }
    }

    public function address($id=0, $cart=0)
    {
        $this->session->set_userdata('site_redir', current_url());

        if(!$this->ion_auth->logged_in())
        {
            $this->session->set_flashdata('not_allow',lang('please_login_first'));
            redirect('User_login', 'refresh');
        }
        else
        {
            // Set validation rules if the method is $_POST
            if($_SERVER['REQUEST_METHOD'] == 'POST')
            {
                $this->form_validation->set_rules('title', lang('name'), 'required');
                //$this->form_validation->set_rules('address', lang('address'), 'required');

                $this->form_validation->set_rules('lat', lang('get_current_location'), 'required'); // Mrzok Edit
                $this->form_validation->set_rules('lng', lang('get_current_location'), 'required'); // Mrzok Edit

                $this->form_validation->set_message('required', lang('required')." : %s ");
                $this->form_validation->set_error_delimiters('<div style="color:red;"><p class="validation">', '</p></div>');
            }

            if($this->form_validation->run() == FALSE)
            {
                $this->_user_address_form($id);
            }
            else
            {
                $title    = strip_tags($this->input->post('title', TRUE));
                $address  = strip_tags($this->input->post('address', TRUE));
                $lat      = strip_tags($this->input->post('lat', TRUE));
                $lng      = strip_tags($this->input->post('lng', TRUE));
                $city     = strip_tags($this->input->post('city', TRUE));

                $default_add = isset($_POST['default_add']) ? 1 : 0;

                if($default_add == 1)
                {
                  //update user old address to be not default_add
                  $conds = array(
                                  'user_id' => $this->data['user_id'],
                                  'default_add' => 1
                                );
                  $updated_data = array('default_add' => 0);
                  $this->users_model->update_table_data('user_addresses', $conds, $updated_data);

                }

                $new_data = array(
                                    'user_id' => $this->data['user_id']  ,
                                    'title'   => $title  ,
                                    'lat'     => $lat,
                                    'lng'     => $lng,
                                    'address' => $address,
                                    'city'    => $city,
                                    'default_add' => $default_add
                                  );

                if($id == 0)
                {
                  $this->users_model->insert_table_data('user_addresses', $new_data);
                }
                else {
                  $conds = array(
                                  'id' => $id
                                );

                  $this->users_model->update_table_data('user_addresses', $conds, $new_data);
                }
                if($cart == 1)
                {
                    redirect('Cart_Address', 'refresh');
                }
                else
                {
                    redirect('Addresses_List','refresh');
                }
            }
        }
    }

    private function _user_address_form($id)
    {
      if(!$this->data['is_logged_in'])
      {
        redirect('User_login', 'refresh');
      }
      else
      {
        $lang_id = $this->data['lang_id'];

        if($id != 0)
        {
          $conditions = array(
                               'id' => $id
                             );
          $address_data = $this->users_model->get_result_data_where('user_addresses', 'row', $conditions);

          $this->data['general_data'] = $address_data;

        }

        $this->data['user_add_page'] = true;

        $this->data['content']  = $this->load->view('user_address', $this->data, true);
        $this->load->view('site/main_frame', $this->data);
      }

    }

    public function delete_address($address_id)
    {
      $address_id = intval($address_id);
      $conditions = array(
                           'id' => $address_id
                         );
      $address_data = $this->users_model->get_result_data_where('user_addresses', 'row', $conditions);

      if($this->data['user_id'] == $address_data->user_id)
      {
        //check user address
        $address_used = $this->users_model->check_user_address_used($address_id);

        if(!$address_used)
        {
          $this->users_model->delete_user_address($address_id);
          $message = lang('record_deleted_successfully');
          $error = 0;

        }
        else {
          $message = lang('can_not_delete_used_address');
          $error = 1;
        }

      }
      else {
        $message = lang('no_data');
        $error = 1;
      }

      //$this->data['message'] = $message;
      //$this->data['error'] = $error;

      $_SESSION['list_msg'] = $message;
      $this->session->mark_as_flash('list_msg');

      $_SESSION['list_error'] = $error;
      $this->session->mark_as_flash('list_error');


      $this->list();
    }



/*********************************************************/
}
