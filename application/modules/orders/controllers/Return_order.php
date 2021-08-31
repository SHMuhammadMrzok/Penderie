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

        $this->load->library('orders');
        $this->load->library('notifications');

        $this->load->model('orders_model');
        $this->load->model('static_pages/static_pages_model');

        require(APPPATH . 'includes/front_end_global.php');
    }

    public function return_order_product($order_product_id)
    {
      //check product in user orders
      $order_product_id = intval($order_product_id);
      $user_id = $this->data['user_id'];
      $lang_id = $this->data['lang_id'];

      $prduct_in_user_orders = $this->orders_model->check_product_in_user_orders($user_id, $order_product_id);

      if($prduct_in_user_orders)
      {

          $order_product_data = $this->orders_model->get_order_product_all_data($this->data['lang_id'], $order_product_id);
          $order_id = $order_product_data->order_id;
          $this->data['order_id'] = $order_id;
          $order_data = $this->orders_model->get_order($order_id);

          if($order_data->order_status_id != 1)
          {
            $this->data['error'] = lang('cant_return_product');
          }
          else
          {
              $log_conds  = array(
                'order_id'  => $order_id,
                'status_id' => 1
              );
              $approve_data = $this->orders_model->get_table_data('orders_log', $log_conds, 'row');
              $allowed_time = time() - ($this->config->item('return_days') * 24 * 60 * 60);

              if($approve_data->unix_time < $allowed_time)
              {
                $this->data['error'] = ('return_days_ended');
              }
              else {

                if($order_product_data->return_status == 0)
                {
                  $validation_msg = false;
                  if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
                  {
                      $validation_msg = true;

                      $this->form_validation->set_rules('qty', lang('qty'), 'trim|required');
                      $this->form_validation->set_rules('reason', lang('return_reason'), 'trim|required');

                      $this->form_validation->set_message('required', lang('required'));
                      $this->form_validation->set_error_delimiters('<div class="error" style="color: red">', '</div>');
                  }

                  if ($this->form_validation->run() == FALSE)
    		        {
              		  //$this->_return_form($order_product_id, $validation_msg);
                    $this->data['order_product'] = $order_product_data;
                  }
                  else
                  {
                      $qty = intval($this->input->post('qty', true));
                      $reason = strip_tags($this->input->post('reason', true));

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
                                              'added_by'         => $this->data['user_id'],
                                              'status_id' => 1,
                                              'notes'     => $reason,
                                              'unix_time' => time()
                                            );
                          $this->orders_model->insert_table_data('orders_return_log', $log_data);


                          //create notification

                          $emails[] = $this->config->item('email');
                          $phone    = $this->config->item('mobile');
                          $order_id = $order_product_data->order_id;

                          $template_data = array(
                                                  'logo_path'    => base_url().'assets/uploads/'.$this->config->item('logo'),
                                                  'site_name'    => $this->config->item('site_name'),
                                                  'unix_time'    => date('Y/m/d', time()),
                                                  'product_name' => $order_product_data->title,
                                                  'qty'          => $qty,
                                                  'order_id'     => $order_id,
                                                  'year'         => date('Y'),
                                                                  'admin_order_link' => base_url().'orders/admin_order/view_order/'.$order_id
                                                );

                          $this->notifications->create_notification('returned_product', $template_data, $emails, $phone);



                          $_SESSION['message'] = lang('success');
                          $this->session->mark_as_flash('message');

                          redirect(base_url(), 'refresh');
                      }
                      else
                      {
                        $this->data['error'] = lang('not_allowed_qty');
                      }
                  }
              }
              else
              {
                $this->data['error'] = lang('already_returned_product');
              }
            }
        }
      }
      else
      {
        $this->data['error'] = lang('not_allowed_to_access_this_page');
      }

      $this->data['return_policy'] = $this->static_pages_model->get_row_data(8, $this->data['lang_id']);


      $this->data['content'] = $this->load->view('return_product', $this->data, true);
      $this->load->view('site/main_frame',$this->data);
    }



/************************************************************************/
}
