<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class User_balance extends CI_Controller
{
    public $lang_row;

    public function __construct()
    {
        parent::__construct();

        require(APPPATH . 'includes/front_end_global.php');

        $this->load->library('encryption');

        $this->load->model('user_balance_model');
        $this->load->model('user_model');

    }

   public function recharge()
   {
        $this->session->set_userdata('site_redir', current_url());

       if($this->ion_auth->logged_in())
       {
           $currency_data                 = $this->currency_model->get_country_currency_result($this->data['country_id']);
           $this->data['currency_symbol'] = $currency_data->currency_symbol;

           $this->data['content'] = $this->load->view('recharge_form', $this->data, true);
           $this->load->view('site/main_frame',$this->data);
       }
       else
       {
           $this->session->set_userdata('redir', current_url());
           redirect('users/users/user_login', 'refresh');
       }
   }

   public function user_balance_log($page_id =1)
   {
        $this->session->set_userdata('site_redir', current_url());

       $page_id = intval($page_id);

       if($this->ion_auth->logged_in())
       {
           $this->load->library('pagination');

           $perPage = 15;
           $offset  = ($page_id -1 ) * $perPage;

           if($offset < 0)
           {
               $offset = $perPage;
           }
           //////////////////////
            //balance status_ids//
            /////////////////////

            /*
             ## status_id = 1 --> withdraw balance
             ## status_id = 2 --> recharge balance
             ## status_id = 3 --> request to recharge balance (order)
            */

           $balance_log_array = array();

           $user_id           = $this->ion_auth->user()->row()->id;
           $display_lang_id   = $this->session->userdata('lang_id');
           $enc_user_balance  = $this->user_bootstrap->get_user_data()->user_balance;
           $secret_key        = $this->config->item('new_encryption_key');
           $secret_iv         = $user_id;
           $user_balance      = $this->encryption->decrypt($enc_user_balance, $secret_key, $secret_iv);
           $balance_log_data  = $this->user_balance_model->get_user_balance_log($user_id, $display_lang_id, $perPage, $offset);

           foreach($balance_log_data as $item)
           {
               if($item->code == 1)
               {
                   $item->{'method'} = lang('generated_codes');
               }

               $balance_log_array[] = $item;
           }

           $config['base_url']    = base_url()."payment_options/user_balance/user_balance_log/";
           $config['per_page']    = $perPage;
           $config['first_link']  = FALSE;
           $config['last_link']   = FALSE;
           $config['uri_segment'] = 4;
           $config['use_page_numbers'] = TRUE;

           $config['total_rows']  = $this->user_balance_model->get_all_balance_log_count($user_id);

           $this->pagination->initialize($config);

           $this->data['pagination']   = $this->pagination->create_links();
           $this->data['user_balance'] = $user_balance;
           $this->data['balance_log']  = $balance_log_array;
           $this->data['content']      = $this->load->view('user_balance_log', $this->data, true);
           $this->load->view('site/main_frame',$this->data);
       }
       else
       {
            $this->session->set_userdata('redir', current_url());

			redirect('users/users/user_login', 'refresh');
       }
   }

   public function charge_with_code()
   {
        $serial  = trim(strip_tags($this->input->post('serial', TRUE)));
        $pin     = trim(strip_tags($this->input->post('pin', TRUE)));
        $user_id = $this->user_bootstrap->get_user_id();

        $secret_key = $this->config->item('new_encryption_key');
        $secret_iv  = md5('generated_code');

        $dec_serial = $this->encryption->encrypt($serial, $secret_key, $secret_iv);
        $dec_pin    = $this->encryption->encrypt($pin, $secret_key, $secret_iv);

        $card_data  = $this->user_balance_model->get_code_data($dec_serial, $dec_pin);

        if($card_data)
        {
            if($card_data->sold == 0 && $card_data->charged == 0)
            {
                $card_amount_with_default_currency = $this->encryption->decrypt($card_data->amount, $secret_key, $secret_iv);

                $card_amount = $this->currency->convert_from_default_currency($card_amount_with_default_currency, $this->data['country_id']);

                $user_data   = $this->user_model->get_row_data($user_id);
                $balance     = $this->encryption->decrypt($user_data->user_balance, $secret_key, $user_id);

                $user_new_balance = $card_amount + $balance;
                $enc_balance = $this->encryption->encrypt($user_new_balance, $secret_key, $user_id);

                $serial_data = array(
                                       'charged' => 1,
                                       'sold'    => 1
                                   );

                $this->user_balance_model->update_serial_data($card_data->id, $serial_data);

                $currency_symbol  = $this->currency->get_country_symbol($this->data['country_id']);

                $balance_log_data = array(
                                            'user_id'           => $user_id                     ,
                                            'payment_method_id' => $card_data->id               ,
                                            'currency_symbol'   => $currency_symbol             ,
                                            'store_country_id'  => $this->data['country_id']    ,
                                            'code'              => '1'                          ,
                                            'balance'           => $user_new_balance            ,
                                            'amount'            => $card_amount                 ,
                                            'balance_status_id' => '2'                          ,
                                            'ip_address'        => $this->input->ip_address()   ,
                                            'unix_time'         => time()
                                         );

                $this->user_balance_model->insert_balance_log($balance_log_data);

                $user_updated_data['user_balance'] = $enc_balance;
                $this->user_model->update_user_balance($user_id, $user_updated_data);

                $return_msg = lang('balance_charged_with')." $card_amount ".lang('your_current_balance')." $user_new_balance";
            }
            else
            {
                $return_msg = lang('card_used_before');
            }
        }
        else
        {
            $return_msg = lang('no_data_about_this_card');
        }

        echo $return_msg;

   }

/************************************************************************/
}
