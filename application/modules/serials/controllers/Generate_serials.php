<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Generate_serials extends CI_Controller
{
    public   $lang_row;

    public function __construct()
    {
        parent::__construct();

        require(APPPATH . 'includes/global_vars.php');

        $this->load->model('generate_serials_model');
        $this->load->library('encryption');
        $this->config->load('encryption_keys');

        $this->lang_row = $this->admin_bootstrap->get_active_language_row();
    }

     private function _js_and_css_files()
    {
        $this->data['css_files'] = array();

        $this->data['js_files']  = array(
            //TouchSpin
            'global/plugins/fuelux/js/spinner.min.js',
            'global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js',
            'global/plugins/bootstrap-touchspin/bootstrap.touchspin.js',

            );


        $this->data['js_code'] = '';
    }

    public function index()
    {
        redirect('serials/generate_serials/add', 'refresh');
    }

    public function add()
    {
        $validation_msg = false;

        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
        {
            $validation_msg = true;

            $this->form_validation->set_rules('amount' , lang('amount') , 'required');
            $this->form_validation->set_rules('number_of_codes' , lang('number_of_codes') , 'required');

            $this->form_validation->set_message('required', lang('required').' : %s ');
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        }

        if ($this->form_validation->run() == FALSE)
    		{
    		  $this->_add_form($validation_msg);
        }
        else
        {
            $amount           = $this->input->post('amount');
            $number_of_codes  = $this->input->post('number_of_codes');
            $active           = $this->input->post('active');
            $export           = $this->input->post('export');

            $active_value = '';
            if($active == 0)
            {
                $active_value ='False';
            }elseif($active == 1)
            {
                 $active_value ='True';
            }

            $download_data = array ();
            $insert_time   = time();
            $secret_key    = $this->config->item('new_encryption_key');
            $secret_iv     = md5('generated_code');

            for($i=0 ; $i<$number_of_codes;$i++)
            {

                $enc_amount   = $this->encryption->encrypt($amount, $secret_key, $secret_iv);

                $serials_data = array(
                                    'amount'    => $enc_amount ,
                                    'serial'    => $this->generateRandomString($insert_time),
                                    'pin'       => $this->generateRandomPin($insert_time),
                                    'unix_time' => $insert_time,
                                    'active'    => $active,
                                  );

                $this->generate_serials_model->insert_serials_data($serials_data);

                $download_data  []= $serials_data;
            }

            $this->data['created_serial'] = true;

           if($export == 1)
           {
                /************ download file with serials***************/
                //START DOWNLOAD
               // ob_clean();
                header("Content-type: text/x-csv ; charset=utf-8");
                header("Content-Transfer-Encoding: binary");
                header("Content-Disposition: attachment; filename=csv".date('YmdHis',strtotime('now')).".csv");
                header("Pragma: no-cache");
                header("Expires: 0");

                $output ="serial , PIN code, Amount , date , Active \r\n";

                foreach($download_data as $row)
                {
                    $dec_amount   = $this->encryption->decrypt($row['amount'], $secret_key, $secret_iv);
                    $dec_serial   = $this->encryption->decrypt($row['serial'], $secret_key, $secret_iv);
                    $dec_pin      = $this->encryption->decrypt($row['pin'], $secret_key, $secret_iv);

                    $output .= $dec_serial.",".$dec_pin.",".$dec_amount.",".date('d/m/Y',$row['unix_time']).",".$row['active']."\r\n";
                }

                echo $output;

                /******************************************************/
            }

             $_SESSION['success'] = lang('success');
             $this->session->mark_as_flash('success');

             redirect('serials/display_serials/','refresh');
        }
    }

    private function _add_form($validation_msg)
    {
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }

        $this->_js_and_css_files();
        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/add";
        $this->data['content']      = $this->load->view('generate_serials', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
    }

     public function generateRandomString($time, $length = 16)
     {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $length; $i++)
            {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }

            $secret_key = $this->config->item('new_encryption_key');
            $secret_iv  = md5('generated_code');

            $encrypted_serial = $this->encryption->encrypt($randomString, $secret_key, $secret_iv);

            return $encrypted_serial;
     }

     public function generateRandomPin($time, $length = 10)
     {
            $characters       = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomPin        = date('Ymd');

            for ($i = 0; $i < $length; $i++)
            {
                $randomPin .= $characters[rand(0, $charactersLength - 1)];
            }

            $secret_key    = $this->config->item('new_encryption_key');
            $secret_iv     = md5('generated_code');

            $encrypted_Pin = $this->encryption->encrypt($randomPin, $secret_key, $secret_iv);

            return $encrypted_Pin;
     }


/************************************************************************/
}
