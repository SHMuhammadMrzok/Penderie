<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Admin_countries extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        require(APPPATH . 'includes/global_vars.php');

        $this->load->model('countries_model');
        $this->load->model('currencies/currency_model');
    }

    private function _js_and_css_files()
    {
        $this->data['css_files'] = array('');

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

        $lang_id = $this->data['active_language']->id;

        $this->data['count_all_records']  = $this->countries_model->get_count_all_countries($lang_id);
        $this->data['data_language']      = $this->lang_model->get_active_data_languages();

        $this->data['columns']            = array(
                                                     lang('country'),
                                                     lang('currency'),
                                                     lang('flag'),
                                                     lang('reward_point_value'),
                                                  );

        $this->data['orders']             = array(
                                                     lang('country'),
                                                     lang('currency')
                                                  );

        $this->data['actions']            = array( 'delete'=>lang('delete'));
        $this->data['search_fields']            = array( lang('country_name'));

        $this->data['content']  = $this->load->view('Admin/grid/grid_html', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
    }

    public function ajax_list()
    {
        if(isset($_POST['lang_id']))
        {
            $lang_id = intval($this->input->post('lang_id'));
        }else{
            $lang_id = $this->data['active_language']->id;
        }
        if(isset($_POST['limit']))
        {
            $limit = intval($this->input->post('limit'));
        }else{
            $limit = 1;
        }

        if(isset($_POST['page_number']))
        {
            $active_page = intval($this->input->post('page_number'));
        }else{
            $active_page = 1;
        }

        $offset  = ($active_page-1) * $limit;


        if(isset($_POST['search_word']) || trim($_POST['search_word']) == '')
        {
            $search_word = $this->input->post('search_word');
        }
        else
        {
            $search_word = '';
        }

        if(isset($_POST['order_by']))
        {
            $order_by = $this->input->post('order_by');
        }
        else
        {
            $order_by = '';
        }

        if(isset($_POST['order_state']))
        {
            $order_state = $this->input->post('order_state');
        }
        else
        {
            $order_state = 'desc';
        }



        $grid_data  = $this->countries_model->get_countries_data($lang_id, $limit, $offset, $search_word, $order_by, $order_state);

        $db_columns = array(
                             'id'               ,
                             'name'             ,
                             'currency_symbol'  ,
                             'flag'             ,
                             'reward_points'
                           );

       $this->data['hidden_fields'] = array('id');

       $new_grid_data = array();

        foreach($grid_data as $key =>$row)
        {
            foreach($db_columns as $column)
            {
                if($column == 'flag')
                {
                    $new_grid_data[$key][$column] = "<img src='".$images_path.$row->flag."' width = 30 hight = 20 />";
                }
                else
                {
                    $new_grid_data[$key][$column] = $row->{$column};
                }
            }
        }


        $this->data['grid_data']         = $new_grid_data;
        $this->data['count_all_records'] = $this->countries_model->get_count_all_countries($lang_id,$search_word);
        $this->data['display_lang_id']   = $lang_id;

        $output_data = $this->load->view('Admin/grid/grid_data',$this->data, true);
        $count_data  = $this->data['count_all_records'];

        echo json_encode(array($output_data, $count_data, $search_word));
     }

     public function add()
     {
        $validation_msg = false;

        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
        {
            $validation_msg = true;

            $languages = $this->input->post('lang_id');

            foreach($languages as $lang_id)
            {
                $this->form_validation->set_rules('name['.$lang_id.']', lang('country_name'), 'required');
                //$this->form_validation->set_rules('currency['.$lang_id.']', lang('currency'), 'required');
            }
            $this->form_validation->set_rules('image', lang('flag'), 'required');
            $this->form_validation->set_rules('currency_id', lang('currency'), 'required');
            $this->form_validation->set_rules('reward_points', lang('reward_point_value'), 'required');

            $this->form_validation->set_message('required', lang('required'));
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        }

        if ($this->form_validation->run() == FALSE)
		{
		  $this->_add_form($validation_msg);
        }
        else
        {
            $flag            = $this->input->post('image');
            $name            = $this->input->post('name');
            //$currency        = $this->input->post('currency');
            $country_symbol  = $this->input->post('country_symbol');
            $currency_id     = $this->input->post('currency_id');
            $reward_points   = $this->input->post('reward_points');
            $user_nationality_id   = $this->input->post('user_nationality_id');

            $data            = array(
                                        'flag'            => $flag,
                                        'unix_time'       => time(),
                                        'currency_id'     => $currency_id,
                                        'country_symbol'     => $country_symbol,
                                        'reward_points'   => $reward_points ,
                                        'user_nationality_id'  => $user_nationality_id,
                                    );

            if($this->countries_model->insert_countries($data))
            {

                $last_insert_id = $this->db->insert_id();

                foreach($languages as $lang_id)
                {
                    $countries_translation_data = array(
                                                        'country_id'   => $last_insert_id ,
                                                        'name'         => $name[$lang_id],
                                                        'lang_id'      => $lang_id ,
                                                        //'currency'     => $currency[$lang_id] ,
                                                     );
                    $this->countries_model->insert_countries_translation($countries_translation_data);
                }

                $_SESSION['success'] = lang('success');
                $this->session->mark_as_flash('success');

                redirect('users/admin_countries/','refresh');
           }
        }
    }

    private function _add_form($validation_msg)
    {
        $this->_js_and_css_files();

        $currency_array = array();
        $lang_id    = $this->data['active_language']->id;
        $currencies = $this->currency_model->get_currecies_result($lang_id);
        $user_nationalities         = $this->users_model->get_all_countries($lang_id);

        foreach($currencies as $currency)
        {
            $currency_array[$currency->id] = $currency->name;
        }

        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }

         $user_nationalities                 = $this->users_model->get_all_countries($this->data['lang_id']); 

        $nationalities_options = array();
        $nationalities_options[null]= lang('choose');

        foreach($user_nationalities as $row)
        {
            $nationalities_options[$row->id] = $row->name;
        }


        $this->data['nationalities_options']  = $nationalities_options;
        $this->data['currencies']   = $currency_array;
        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/add";
        $this->data['content']      = $this->load->view('countries', $this->data, true);

        $this->load->view('Admin/main_frame',$this->data);
    }

    public function edit($id)
    {
        if(is_numeric($id))
        {
            $id = intval($id);

            $validation_msg = false;

            if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
            {
                $validation_msg = true;
                $country_id     = $this->input->post('country_id');
                $languages      = $this->input->post('lang_id');

                foreach($languages as $lang_id)
                {
                    $this->form_validation->set_rules('name['.$lang_id.']', lang('country_name'), 'required');
                    //$this->form_validation->set_rules('currency['.$lang_id.']', lang('currency'), 'required');
                }
                $this->form_validation->set_rules('image', lang('flag'), 'required');
                $this->form_validation->set_rules('currency_id', lang('currency'), 'required');
                $this->form_validation->set_rules('reward_points', lang('reward_point_value'), 'required');

                $this->form_validation->set_message('required', lang('required'));
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            }
        }

        if($this->form_validation->run() == FALSE)
		{
		   $this->_edit_form($id, $validation_msg);
        }
        else
        {
            $flag            = $this->input->post('image');
            $currency_id     = $this->input->post('currency_id');
            $name            = $this->input->post('name');
            $country_symbol  = $this->input->post('country_symbol');
            $reward_points   = $this->input->post('reward_points');
            $user_nationality_id   = $this->input->post('user_nationality_id');

            $data      = array(
                                    'flag'              => $flag ,
                                    'currency_id'       => $currency_id ,
                                    'country_symbol'    => $country_symbol,
                                    'reward_points'     => $reward_points,
                                    'user_nationality_id'  => $user_nationality_id,
                                );

            $this->countries_model->update_countries($country_id,$data);

            foreach($languages as $lang_id)
            {
                $countries_translation_data = array(
                                                    'name'         => $name[$lang_id],
                                                    //'currency'     => $currency[$lang_id]
                                                  );

                $this->countries_model->update_countries_translation($country_id,$lang_id,$countries_translation_data);
            }

            $_SESSION['success'] = lang('updated_successfully');
            $this->session->mark_as_flash('success');

            redirect('users/admin_countries/','refresh');
        }
    }

    private function _edit_form($id, $validation_msg)
    {
        $this->_js_and_css_files();
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }

        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/edit/".$id;
        $this->data['id']           = $id;
        $lang_id                    = $this->data['active_language']->id;
        $general_data               = $this->countries_model->get_countries_result($id);
        $data                       = $this->countries_model->get_countries_translation_result($id);
        $currencies                 = $this->currency_model->get_currecies_result($lang_id);
        $user_nationalities         = $this->users_model->get_all_countries($lang_id);

        $filtered_data              = array();
        $currency_array = array();

        foreach($data as $row)
        {
            $filtered_data[$row->lang_id] = $row;
        }

        foreach($currencies as $currency)
        {
            $currency_array[$currency->id] = $currency->name;
        }

        $nationalities_options = array();
        $nationalities_options[null]= lang('choose');

        foreach($user_nationalities as $row)
        {
            $nationalities_options[$row->id] = $row->name;
        }

        $this->data['general_data'] = $general_data;
        $this->data['data']         = $filtered_data;
        $this->data['currencies']   = $currency_array;
        $this->data['nationalities_options']  = $nationalities_options;

        $this->data['content']      = $this->load->view('countries', $this->data, true);

        $this->load->view('Admin/main_frame',$this->data);
    }


     public function read($id, $display_lang_id)
     {
        $id              = intval($id);
        $display_lang_id = intval($display_lang_id);

        if($id && $display_lang_id)
        {
            $data     = $this->countries_model->get_row_data($id,$display_lang_id);

            if($data)
            {
                 $row_data = array(
                                    lang('country')            => $data->name,
                                    lang('currency')           => $data->currency,
                                    lang('symbol')             => $data->currency_symbol,
                                    lang('reward_point_value') => $data->reward_points,
                                    lang('flag')               => '<img src="'.$images_path.$data->flag.'" class="image-thumbnail" width="150" height="150">'
                                 );

                $this->data['row_data'] = $row_data;

                $this->data['content']  = $this->load->view('Admin/grid/read_view', $this->data, true);
                $this->load->view('Admin/main_frame',$this->data);
            }
        }
    }
    public function do_action()
     {
        $action = $this->input->post('action');
        if($action == 'delete')
        {
            $this->delete();
        }
     }

     public function delete()
     {
        $countries_ids = $this->input->post('row_id');

        if(is_array($countries_ids))
        {

            $ids_array = array();

            foreach($countries_ids as $country_id)
            {
                $ids_array[] = $country_id['value'];
            }
        }
        else
        {
            $ids_array = array($countries_ids);
        }

        $this->countries_model->delete_country_data($ids_array);

     }



/************************************************************************/
}
