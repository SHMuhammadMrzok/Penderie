<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Careers extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        require(APPPATH . 'includes/front_end_global.php');
        $this->load->model('users/cities_model');
        $this->load->model('Careers_model');

        $this->session->set_userdata('site_redir', current_url());
    }

    var $data = array();

    public function index()
    {
        $countries              = $this->cities_model->get_user_nationality_filter_data($this->data['lang_id']);

        $countries_array        = array();
        $countries_array[null]  = lang('choose');

        foreach($countries as $country)
        {
            $countries_array[$country->id]  = $country->name;
        }

        $this->data['user_countries'] = $countries_array;

        $this->data['content'] = $this->load->view('careers_form', $this->data, true);
        $this->load->view('site/main_frame',$this->data);
    }

    public function save()
    {
        $this->form_validation->set_rules('name', lang('name') , 'required');
        $this->form_validation->set_rules('email',lang('email') , 'required|valid_email');
        $this->form_validation->set_rules('mobile', lang('mobile') , 'required|integer');
        //$this->form_validation->set_rules('phone', lang('phone') , 'required');
        //$this->form_validation->set_rules('experience', lang('experience') , 'required');
        //$this->form_validation->set_rules('address', lang('address') , 'required');
        //$this->form_validation->set_rules('city', lang('city') , 'required');
        //$this->form_validation->set_rules('country_id', lang('country_id') , 'required');
        //$this->form_validation->set_rules('date_of_birth', lang('date_of_birth') , 'required');
        $this->form_validation->set_rules('applied_job_att', lang('applied_job_att') , 'required');
        //$this->form_validation->set_rules('education', lang('education') , 'required');
        $this->form_validation->set_rules('image', lang('cv') , 'required');

        $this->form_validation->set_message('required', lang('required')."  : %s ");
        $this->form_validation->set_message('integer', "%s : ".lang('integer_required'));
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');


        if ($this->form_validation->run() == FALSE)
        {

            $_SESSION['error_msg'] = validation_errors();
            $this->session->mark_as_flash('error_msg');

            $this->_view_form();

        }
        else
        {

            $user_name          = strip_tags($this->input->post('name', TRUE));
            $user_email         = strip_tags($this->input->post('email', TRUE));
            $user_mobile        = strip_tags($this->input->post('mobile', TRUE));
            //$user_phone         = strip_tags($this->input->post('phone', TRUE));
            //$experience         = strip_tags($this->input->post('experience', TRUE));
            //$address            = strip_tags($this->input->post('address', TRUE));
            //$city               = strip_tags($this->input->post('city', TRUE));
            //$country_id         = strip_tags($this->input->post('country_id', TRUE));
            //$date_of_birth      = strip_tags($this->input->post('date_of_birth', TRUE));
            $applied_job_att    = strip_tags($this->input->post('applied_job_att', TRUE));
            //$education          = strip_tags($this->input->post('education', TRUE));


            /*********** Upload****************************/
            $error_exist = false;
            /*$this->load->library('upload');
            $gallery_path = realpath(APPPATH. '../assets/uploads/');

            $config = array();
            $config['upload_path']   = $gallery_path;
            $config['allowed_types'] = 'xls|xls|pdf|docs|docx|doc|text|odt|png|jpg|jpeg|tif';
            $config['max_size']      = '1000';

            $files_name = '';
            $error      = array();

            $this->upload->initialize($config);
            if(!$this->upload->do_upload())
            {
                $error = $this->upload->display_errors();

                $_SESSION['error_msg'] = $error;
                $this->session->mark_as_flash('error_msg');

                $error_exist = true;

            }
            else
            {
               $file_data   = $this->upload->data();
               $files_name = $file_data['file_name'];

               $error_exist = false;
            }
            */

            if($error_exist)
            {
                $this->_view_form();
            }
            else
            {
                $files_name = $this->input->post('image', true);
                $data = array(
                                'name'              => $user_name       ,
                                'email'             => $user_email      ,
                                'mobile'            => $user_mobile     ,
                                //'phone'             => $user_phone      ,
                                //'experience'        => $experience      ,
                                //'address'           => $address         ,
                                //'city'              => $city            ,
                                //'country_id'        => $country_id      ,
                                //'date_of_birth' => $date_of_birth   ,
                                'applied_job'   => $applied_job_att ,
                                //'education'         => $education       ,
                                'cv'                => $files_name      ,
                                'unix_time'         => time()
                            );

                $this->Careers_model->save_careers_form_data($data);

                $_SESSION['success_msg'] = lang('execution_success');
                $this->session->mark_as_flash('success_msg');

                redirect (base_url().'careers/careers/','refresh');
            }
        }
    }

    private function _view_form()
    {
        $countries              = $this->cities_model->get_user_nationality_filter_data($this->data['lang_id']);

        $countries_array        = array();
        $countries_array[null]  = lang('choose');

        foreach($countries as $country)
        {
            $countries_array[$country->id]  = $country->name;
        }

        $this->data['user_countries'] = $countries_array;

        $this->data['content'] = $this->load->view('careers_form', $this->data, true);
        $this->load->view('site/main_frame',$this->data);
    }
/************************************************************************/
}
