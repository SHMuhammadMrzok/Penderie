<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Later_payment extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        require(APPPATH . 'includes/global_vars.php');

        $this->load->model('pay_later_model');
        $this->load->model('orders_model');
        
        $this->load->library('orders');


    }

    public function add_bill($order_id)
    {
       $order_id = intval($order_id);
       $validation_msg = false;

       $order_data = $this->orders_model->get_order_data($order_id);

       if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
       {

           $this->form_validation->set_rules('amount', lang('amount'), 'required|callback_amount_validation');
           $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

           $validation_msg = true;
       }

       if ($this->form_validation->run() == FALSE)
       {
         $this->_bill_form($order_id, $validation_msg, $order_data);
       }
       else
       {

           $amount = strip_tags($this->input->post('amount', true));
           $notes  = strip_tags($this->input->post('notes', true));

           $new_order_paid_amount = $amount + $order_data->paid_amount;
           $new_order_rest_amount = $order_data->rest_amount - $amount;

           $data   = array(
                               'added_by'    => $this->data['user_id'],
                               'order_id'    => $order_id,
                               'amount'      => $amount,
                               'notes'       => $notes,
                               'order_total' => $order_data->final_total,
                               'order_rest'  => $new_order_rest_amount ,
                               'order_paid'  => $new_order_paid_amount ,
                               'unix_time'   => time()
                           );

           if($this->pay_later_model->insert_bill($data))
           {
               //update order data


               $order_new_data = array(
                 'paid_amount' => $new_order_paid_amount,
                 'rest_amount' => $new_order_rest_amount
               );

               $this->orders_model->update_order_data($order_id, $order_new_data);


               $this->session->set_flashdata('success',lang('success'));

               redirect('orders/admin_order/view_order/'.$order_id,'refresh');
           }
           
           
           /*if($this->orders->add_order_bill($order_id, $amount, $notes, $order_data))
           {                
                $this->session->set_flashdata('success',lang('success'));
                redirect('orders/admin_order/view_order/'.$order_id,'refresh');
           }*/

       }
    }

    private function _bill_form($order_id, $validation_msg)
    {

      $order_data = $this->orders_model->get_order_data($order_id);

       if($validation_msg)
       {
           $this->data['validation_msg'] = lang('fill_required_fields');
       }

       if($order_data->payment_method_id != 14) //order is paied with pay later payment method
       {
         $this->data['error_msg'] = lang('can_not_pay_bills_for_order‏');
       }
       else
       {
         if($order_data->final_total == $order_data->paid_amount)
         {

           //order completley paied before
           $this->data['error_msg'] = lang('order_is_completly_paid_before');

         }

         $old_bills = $this->pay_later_model->get_order_bills($order_id);

         $this->data['order_bills']  = $old_bills;
         $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/add_bill/".$order_id;
         $this->data['mode']         = 'add';
         $this->data['order_id']     = $order_id;
       }

       $this->data['content'] = $this->load->view('later_payment_bills', $this->data, true);
       $this->load->view('Admin/main_frame',$this->data);
    }

    public function amount_validation($amount)
    {
      $order_id = intval($this->input->post('order_id', true));
      $order_data = $this->orders_model->get_order_data($order_id);

      if($amount > $order_data->rest_amount)
      {
        $this->form_validation->set_message('amount_validation', lang('amount_is_larger_than_rest‏‎'));
        return false;
      }
      else {
      {
        return true;
      }
      }
    }




/************************************************************************/
}
