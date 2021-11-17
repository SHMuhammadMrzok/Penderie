<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Follow_balance extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        //$this->load->model('follow_balance_model');
        $this->load->model('general_model');

        $this->load->library('api_lib');
        $this->load->library('pagination');
        $this->load->model('payment_options/user_balance_model');
    }

    public function index($page =1)
    {
        $user_id             = intval(strip_tags($this->input->post('userId', TRUE)));
        $email              = strip_tags($this->input->post('email', TRUE));
        $password           = strip_tags($this->input->post('password', TRUE));

        $lang_id            = intval(strip_tags($this->input->post('langId', TRUE)));
        $page               = intval(strip_tags($this->input->post('page', TRUE)));
        $deviceId           = strip_tags($this->input->post('deviceId', TRUE));

        $store_country_id   = intval(strip_tags($this->input->post('storeCountryId', TRUE)));

        $agent              = strip_tags($this->input->post('agent', TRUE));

        $output = array();

        //$fail_message = $this->general_model->get_lang_var_translation('execution_fail',$lang_id);

        if($this->ion_auth->login($email, $password))
        {
            $user_data     = $this->ion_auth->user()->row();
            $user_id       = $user_data->id;

            $this->api_lib->check_user_store_country_id($email, $password, $user_id, $store_country_id);

            $user_data     = $this->ion_auth->user()->row();


            if(!$page) $page = 1;
            $perPage = 10;
            $offset  = ($page -1 ) * $perPage;

            $users_balance_data          = $this->user_balance_model->get_user_balance_log($user_id, $lang_id, $perPage, $offset);
            //follow_balance_model->get_user_balance_log($userId, $lang_id, $perPage, $offset);

            if(!empty($users_balance_data))
            {
                foreach($users_balance_data as $balance)
                {
                    if($balance->code == 1)
                    {
                        $paymentType = $this->general_model->get_lang_var_translation('generated_codes',$lang_id);
                    }else{
                        $paymentType = $balance->method;
                    }

                    $output[] = array(
                                        'chargeDate'            => date('Y-m-d',$balance->unix_time),
                                        'chargeDescription'     => $balance->status,
                                        'chargeCredit'          => $balance->amount.' '.$balance->currency_symbol,
                                        'walletCredit'          => round($balance->balance).' '.$balance->currency_symbol,
                                        'paymentType'           => $paymentType,
                                        );
                }

            }else{
                $fail_message = $this->general_model->get_lang_var_translation('no_data',$lang_id);
                $output  = array(
                                    'message' => $fail_message,
                                    'response' => 0
                                );

            }//if data

        }else{
            $fail_message = $this->general_model->get_lang_var_translation('login_error',$lang_id);
            $output  = array(
                                'message' => $fail_message,
                                'response' => 0
                            );
            //$output  = array( 'message' => '0');
        }//if login

        //***************LOG DATA***************//
        //insert log
        $this->api_lib->insert_log($user_id, current_url(), 'Follow balance', $agent, $_POST, $output);
        //***************END LOG***************//

        $this->output->set_content_type('application/json')->set_output(json_encode($output));

    }


/************************************************************************/
}
