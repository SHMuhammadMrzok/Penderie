<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Return_order extends CI_Controller
{
    public $status=1;
    public $data = array();

    public function __construct()
    {
        parent::__construct();

        $this->load->library('orders/orders');
        $this->load->library('notifications');

        $this->load->model('general_model');
        $this->load->model('orders/orders_model');
        $this->load->model('static_pages/static_pages_model');

        $this->load->library('api_lib');
    }

    public function index()
    {
      //check product in user orders
      $order_product_id = intval($this->input->post('orderProductId'));
      $lang_id          = intval($this->input->post('langId'));
      $email            = strip_tags($this->input->post('email'));
      $password         = strip_tags($this->input->post('password'));
      
      $agent              = strip_tags($this->input->post('agent', TRUE));
      $user_id            = 0;
      
      if($this->ion_auth->login($email, $password))
      {
          $user_data = $this->ion_auth->user()->row();
          $user_id   = $user_data->id;
          $prduct_in_user_orders = $this->orders_model->check_product_in_user_orders($user_id, $order_product_id);
        
          if($prduct_in_user_orders)
          {
    
              $settings = $this->general_model->get_site_settings($lang_id);
              $order_product_data = $this->orders_model->get_order_product_all_data($lang_id, $order_product_id);
              $order_id = $order_product_data->order_id;
              $order_data = $this->orders_model->get_order($order_id);
    
              if($order_data->order_status_id != 1)
              {
                $error_msg = $this->general_model->get_lang_var_translation('cant_return_product', $lang_id);
                $output = array(
                                    'message'  => $error_msg,
                                    'response' => 0
                                );
              }
              else
              {
                  $log_conds  = array(
                    'order_id'  => $order_id,
                    'status_id' => 1
                  );
                  $approve_data = $this->orders_model->get_table_data('orders_log', $log_conds, 'row');
                  $allowed_time = time() - ($settings->return_days * 24 * 60 * 60);
    
                  if($approve_data->unix_time < $allowed_time)
                  {
                    $error_msg = $this->general_model->get_lang_var_translation('return_days_ended', $lang_id);
                    $output = array(
                                        'message'  => $error_msg,
                                        'response' => 0
                                    );
                  }
                  else {
    
                    if($order_product_data->return_status == 0)
                    {
                      $validation_msg = false;
                      if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
                      {
                          $validation_msg = true;
                          
                          $qty_lang = $this->general_model->get_lang_var_translation('qty',$lang_id);
                          $return_reason_lang = $this->general_model->get_lang_var_translation('return_reason',$lang_id);
                          $required_lang = $this->general_model->get_lang_var_translation('required',$lang_id);
                          
                          $this->form_validation->set_rules('qty', $qty_lang, 'trim|required');
                          $this->form_validation->set_rules('reason', $return_reason_lang, 'trim|required');
    
                          $this->form_validation->set_message('required', $required_lang);
                          $this->form_validation->set_error_delimiters('', '');
                      }
    
                      if ($this->form_validation->run() == FALSE)
                      {
                  		  $output = array(
                                            'message'  => strip_tags(validation_errors()),
                                            'response' => 0
                                         );
                        
                      }
                      else
                      {
                          $qty      = intval($this->input->post('qty', true));
                          $reason   = strip_tags($this->input->post('reason', true));
    
                          if($qty <= $order_product_data->qty)
                          {
                              //update oredr products data
                              $return_data = array(
                                                      'returned_qty'  => $qty,
                                                      'return_status' => 1
                                                  );
    
                              $conds = array(
                                              'id' => $order_product_id
                                            );
                              $this->orders_model->update_table_data('orders_products', $conds, $return_data);
    
                              //update order main data
                              $order_data = array(
                                                      'return_status' => 1
                                                  );
    
                              $order_conds = array(
                                              'id' => $order_product_data->order_id
                                            );
                              $this->orders_model->update_table_data('orders', $order_conds, $order_data);
    
                              // insert in return log
                              $log_data = array(
                                                  'order_id'         => $order_product_data->order_id,
                                                  'order_product_id' => $order_product_id,
                                                  'returned_qty'     => $qty,
                                                  'added_by'         => $user_id,
                                                  'status_id' => 1,
                                                  'notes'     => $reason,
                                                  'unix_time' => time()
                                                );
                              $this->orders_model->insert_table_data('orders_return_log', $log_data);
    
    
                              //create notification
    
                              $emails[] = $settings->email;
                              $phone    = $settings->mobile;
                              $order_id = $order_product_data->order_id;
    
                              $template_data = array(
                                                      'logo_path'    => base_url().'assets/uploads/'.$settings->logo,
                                                      'site_name'    => $settings->site_name,
                                                      'unix_time'    => date('Y/m/d', time()),
                                                      'product_name' => $order_product_data->title,
                                                      'qty'          => $qty,
                                                      'order_id'     => $order_id,
                                                      'year'         => date('Y'),
                                                                      'admin_order_link' => base_url().'orders/admin_order/view_order/'.$order_id
                                                    );
    
                              $this->notifications->create_notification('returned_product', $template_data, $emails, $phone);
    
                              $message = $this->general_model->get_lang_var_translation('success', $lang_id);
                              $output = array(
                                                 'message'  => $message,
                                                 'response' => 1
                                             );
                          }
                          else
                          {
                            
                            $message = $this->general_model->get_lang_var_translation('not_allowed_qty', $lang_id);
                            $output  = array(
                                             'message'  => $message,
                                             'response' => 0
                                           );
                          }
                      }
                  }
                  else
                  {
                    $message = $this->general_model->get_lang_var_translation('already_returned_product', $lang_id);
                    $output  = array(
                                     'message'  => $message,
                                     'response' => 0
                                   );
                  }
                }
            }
          }
          else
          {
            $message = $this->general_model->get_lang_var_translation('not_allowed_to_access_this_page', $lang_id);
            $output  = array(
                             'message'  => $message,
                             'response' => 0
                           );
            
          }
      }
      else
      {
        $this->general_model->get_lang_var_translation('login_error',$lang_id);
        $output  = array(
                                'message' => $fail_message,
                                'response' => 0
                                );
      }
      
      //***************LOG DATA***************//
      //insert log
      $this->api_lib->insert_log($user_id, current_url(), 'Return order', $agent, $_POST, $output);
      //***************END LOG***************//
      
      $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));
    }



/************************************************************************/
}
