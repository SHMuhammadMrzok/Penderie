<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Admin_users extends CI_Controller
{
    public $data = array();

    public function __construct()
    {
        parent::__construct();

        require(APPPATH . 'includes/global_vars.php');

        $this->load->model('user_model');
        $this->load->model('cities_model');
        $this->load->model('groups_model');
        $this->load->model('permissions_model');
        $this->load->model('customer_groups_model');
        $this->load->model('stores/stores_model');
        $this->load->model('payment_options/user_balance_model');

        $this->load->library('encryption');
        $this->config->load('encryption_keys');

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

        $this->data['js_code']   = "ComponentsPickers.init();";
    }

    public function index()
    {
        $lang_id = $this->data['active_language']->id;

        $this->data['count_all_records']    = $this->user_model->get_count_all_users();
        $this->data['data_language']        = $this->lang_model->get_active_data_languages();



        $columns_array   = array(
                                  //lang('name')                      ,
                                  lang('email')                     ,
                                  lang('phone')                     ,
                                  lang('customer_group')            ,
                                  //lang('reward_points')             ,
                                  lang('user_created_on')           ,
                                  //lang('active')                    ,
                                  //lang('mail_list')                 ,
                                  lang('resend_verification_sms')   ,
                                  //lang('store_permissions')
                                );

        $columns_array2 = array();
        if($this->config->item('business_type')=='b2b'){
          $columns_array2 = array(
                                    lang('store_permissions')
                                  );
        }

        $this->data['columns'] = array_merge($columns_array, $columns_array2);

        $this->data['orders']    = array(
                                          //lang('first_name'),
                                          lang('email'),
                                          //lang('group'),
                                          //lang('active')
                                        );

        $this->data['actions']       = array( 'delete'=>lang('delete'));
        $this->data['search_fields'] = array( lang('first_name'), lang('email'), lang('phone'));

        $this->data['content']  = $this->load->view('Admin/grid/grid_html', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);


    }

   public function ajax_list()
    {
        $this->_js_and_css_files();

        $secret_key = $this->config->item('new_encryption_key');


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

        $list_filter = '';



        $grid_data       = $this->user_model->get_users_data($limit, $offset, $this->data['lang_id'], $search_word, $order_by, $order_state);

        $db_columns      = array(
                                 'id'           ,
                                 //'name'         ,
                                 'email'        ,
                                 'phone'        ,
                                 'customer_group_name' ,
                                 //'reward_points',
                                 'created_on'   ,
                                 //'active'       ,
                                 //'mail_list'    ,
                                 //'resend_email' ,
                                 'resend_sms'   ,
                                 //'store_permissions'

                                 );
       if($this->config->item('business_type') == 'b2b')
       {
         $db_columns[] = 'store_permissions';

       }

       $this->data['hidden_fields'] = array('id');

       $new_grid_data = array();

        foreach($grid_data as $key =>$row)
        {
            $groups = $this->user_model->get_user_groups($lang_id ,$row->id);



            foreach($db_columns as $column)
            {
                if($column == 'resend_sms')
                {
                    $button = '<a type="button" class="btn blue-madison" href="'.base_url().'users/admin_users/resend_user_sms_code/'.$row->id.'"><i class="fa fa-mobile-phone (alias)"></i> '.lang('sms').'</a>';
                    $new_grid_data[$key][$column] = $button;
                }
                elseif($column == 'created_on')
                {
                    $new_grid_data[$key][$column] = date('Y/m/d H:i', $row->created_on);
                }
                elseif($column == 'store_permissions')
                {
                    //check user stores
                    $user_stores_count = $this->user_model->user_stores_count($row->id);
                    if($user_stores_count > 0)
                    {
                        $button = '<a type="button" class="btn green" href="'.base_url().'users/admin_users/view_user_stores_permissions/'.$row->id.'"><i class="fa fa-cogs"></i> '.lang('store_permissions').'</a>';
                    }
                    else
                    {
                        $button = '';
                    }
                    $new_grid_data[$key][$column] = $button;
                }
                else
                {
                    $new_grid_data[$key][$column] = $row->{$column};
                }
            }
        }

        $this->data['grid_data']         = $new_grid_data;
        $this->data['count_all_records'] = $this->user_model->get_count_all_users($search_word, $list_filter);
        $this->data['display_lang_id']   = $lang_id;

        $output_data = $this->load->view('Admin/grid/grid_data',$this->data, true);
        $count_data  = $this->data['count_all_records'];

        echo json_encode(array($output_data, $count_data, $search_word));
    }

    public function read($id, $display_lang_id)
    {
        $this->_js_and_css_files();

        $id              = intval($id);
        $display_lang_id = intval($display_lang_id);

        $secret_key      = $this->config->item('new_encryption_key');
        $secret_iv       = $id;

        if($id && $display_lang_id)
        {
            $data       = $this->user_model->get_user($display_lang_id,$id);
            $active     = '';
            $mail_list  = '' ;


            if($data)
            {

                if($data->account_sms_activated == 0)
                {
                    $account_sms_activated = '<span class="badge badge-danger">'.lang('not_active').'</span>';
                }
                elseif($data->account_sms_activated == 1)
                {
                    $account_sms_activated = '<span class="badge badge-success">'.lang('active').'</span>';
                }


                $row_data = array(
                                   //lang('first_name')           => $data->first_name                ,
                                   //lang('last_name')            => $data->last_name                 ,
                                   lang('email')                => $data->email                     ,
                                   lang('phone')                => $data->phone                     ,
                                   //lang('address')              => $data->address                   ,
                                   lang('group')                => $data->name                      ,
                                   lang('customer_group_name')  => $data->title                     ,
                                   //lang('photo')                => $pic                             ,
                                   //lang('user_balance')         => $user_balance                    ,
                                   //lang('reward_points')        => $reward_points                   ,
                                   //lang('active')               => $active                          ,
                                   lang('mobile_active')        => $account_sms_activated           ,
                                   //lang('mail_list')            => $mail_list                       ,
                                   lang('join_date')            => date('Y-m-d', $data->created_on) ,
                                   //lang('resend_verification_email') => '<a type="button" class="btn blue-hoki" href="'.base_url().'users/admin_users/resend_user_activation_email/'.$data->id.'/1" ><i class="fa fa-send-o (alias)"></i> '.lang('e-mail').'</a>',
                                   lang('resend_verification_sms')   => '<a type="button" class="btn blue-madison" href="'.base_url().'users/admin_users/resend_user_sms_code/'.$data->id.'/1"><i class="fa fa-mobile-phone (alias)"></i> '.lang('sms').'</a>'
                                 );




                $user_affiliate_log_data                = $this->user_model->get_user_affiliate_log($data->id);
                //$user_orders_log_data                   = $this->user_model->get_user_orders_data($data->id, $display_lang_id);

                $this->data['lang_id']                  = $display_lang_id;
                $this->data['user_id']                  = $data->id;

                $this->data['user_data']                = $data;
                $this->data['row_data']                 = $row_data;
                $this->data['user_affiliate_log_data']  = $user_affiliate_log_data;

            }

            $this->data['content'] = $this->load->view('users_read', $this->data, true);
            $this->load->view('Admin/main_frame', $this->data);
        }
    }

    public function user_visits_log_ajax($page = 1)
    {
        $page    = intval($page);
        $lang_id = intval($this->input->post('lang_id'));
        $user_id = intval($this->input->post('user_id'));

        echo $this->_user_visits_log($lang_id, $page, $user_id);
    }

    public function user_userslog_ajax($page = 1)
    {
        $page    = intval($page);
        $lang_id = intval($this->input->post('lang_id'));
        $user_id = intval($this->input->post('user_id'));

        echo $this->_user_userslog($lang_id, $page, $user_id);
    }

    public function user_orders_ajax($page = 1)
    {
        $page    = intval($page);
        $lang_id = intval($this->input->post('lang_id'));
        $user_id = intval($this->input->post('user_id'));

        echo $this->_user_orders($lang_id, $page, $user_id);
    }

    public function user_balance_ajax($page = 1)
    {
        $page    = intval($page);
        $lang_id = intval($this->input->post('lang_id'));
        $user_id = intval($this->input->post('user_id'));

        echo $this->_user_balance($lang_id, $page, $user_id);
    }


    private function _user_visits_log($lang_id, $page, $user_id)
    {
        $this->load->model('visit_log_model');
        $this->load->library('pagination');

        $limit  = 20;
        $offset = ($page -1) * $limit;

        $config['base_url']         = base_url().'users/admin_users/user_visits_log_ajax/';
        $config['total_rows']       = $this->visit_log_model->get_count_all_visits_log($lang_id,'', $user_id, 0, 0, 0);
        $config['per_page']         = $limit;
        $config['uri_segment']      = 4;
        $config['use_page_numbers'] = TRUE;
        $config['attributes']       = array('class' => 'pages_links_visits');

        $config['first_link'] = lang('first_page');
        $config['last_link'] = lang('last_page');
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li><strong>';
        $config['cur_tag_close'] = '</strong></li>';

        $config['display_pages'] = TRUE;


        $this->pagination->initialize($config);

        $this->data['page_links'] = $this->pagination->create_links();

        $this->data['visits_log_data'] = $this->visit_log_model->get_visits_log_data($lang_id, $limit, $offset, '', 'visits_log.id', 'desc', $user_id);

        return $this->load->view('users_read_visits', $this->data, true);
    }

    private function _user_userslog($lang_id, $page, $user_id)
    {
        $this->load->model('userslog_model');
        $this->load->library('pagination');

        $limit  = 20;
        $offset = ($page -1) * $limit;

        $config['base_url']    = base_url().'users/admin_users/user_userslog_ajax/';
        $config['total_rows']  = $this->userslog_model->get_count_all_users_log($lang_id ,'',$user_id, 0, 0, 0);
        $config['per_page']    = $limit;
        $config['uri_segment'] = 4;
        $config['use_page_numbers'] = TRUE;
        $config['attributes'] = array('class' => 'pages_links_userslog');

        $config['first_link'] = lang('first_page');
        $config['last_link'] = lang('last_page');
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li><strong>';
        $config['cur_tag_close'] = '</strong></li>';

        $config['display_pages'] = TRUE;


        $this->pagination->initialize($config);

        $this->data['page_links'] = $this->pagination->create_links();

        $this->data['user_log_data'] = $this->userslog_model->get_users_log_data($lang_id, $limit, $offset, '', 'userlog.id', 'desc', $user_id);

        return $this->load->view('users_read_userslog', $this->data, true);
    }

    private function _user_orders($lang_id, $page, $user_id)
    {
        $this->load->library('pagination');

        $limit  = 20;
        $offset = ($page -1) * $limit;

        $config['base_url']    = base_url().'users/admin_users/user_orders_ajax/';
        $config['total_rows']  = $this->orders_model->get_count_all_orders($lang_id, $limit, $offset, '', '', '', $user_id, 0, 0, 0);
        $config['per_page']    = $limit;
        $config['uri_segment'] = 4;
        $config['use_page_numbers'] = TRUE;
        $config['attributes'] = array('class' => 'pages_links_orders');

        $config['first_link'] = lang('first_page');
        $config['last_link'] = lang('last_page');
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li><strong>';
        $config['cur_tag_close'] = '</strong></li>';

        $config['display_pages'] = TRUE;


        $this->pagination->initialize($config);

        $this->data['page_links'] = $this->pagination->create_links();
        $this->data['lang_id']    = $lang_id;

        $this->data['user_orders_data'] = $this->orders_model->get_orders_data($lang_id, $limit, $offset, '', 'id', 'desc', $user_id, '', '', '');

        return $this->load->view('users_read_orders', $this->data, true);
    }

    private function _user_balance($lang_id, $page, $user_id)
    {
        $this->load->model('currencies/currency_model');

        $balance_data_array = array();
        $types_options      = array();

        $this->load->library('pagination');

        $limit  = 10;
        $offset = ($page -1) * $limit;

        $config['base_url']    = base_url().'users/admin_users/user_balance_ajax/';
        $config['total_rows']  = $this->user_balance_model->get_all_balance_log_count($user_id);
        $config['per_page']    = $limit;
        $config['uri_segment'] = 4;
        $config['use_page_numbers'] = TRUE;
        $config['attributes']       = array('class' => 'pages_links_balance');

        $config['first_link']      = lang('first_page');
        $config['last_link']       = lang('last_page');
        $config['first_tag_open']  = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open']   = '<li>';
        $config['last_tag_close']  = '</li>';
        $config['next_tag_open']   = '<li>';
        $config['next_tag_close']  = '</li>';
        $config['prev_tag_open']   = '<li>';
        $config['prev_tag_close']  = '</li>';
        $config['num_tag_open']    = '<li>';
        $config['num_tag_close']   = '</li>';
        $config['cur_tag_open']    = '<li><strong>';
        $config['cur_tag_close']   = '</strong></li>';

        $config['display_pages']   = TRUE;

        $secret_key    = $this->config->item('new_encryption_key');
        $secret_iv     = $user_id;

        $balance_data  = $this->user_balance_model->get_user_balance_log($user_id, $lang_id, $limit, $offset);
        $user_data     = $this->admin_bootstrap->get_user_by_id($user_id);
        $currency_data = $this->currency_model->get_country_currency_result($user_data->store_country_id);
        if($currency_data)
        {
            $currency = $currency_data->currency_symbol;
        }
        else
        {
            $currency = '';
        }

        if($user_data->user_balance == '')
        {
            $user_balance = 0;
        }
        else
        {
            $user_balance = $this->encryption->decrypt($user_data->user_balance, $secret_key, $secret_iv);
        }

        foreach($balance_data as $row)
        {
            if($row->added_by_admin == '1')
            {
                $added_by = lang('admin');
            }
            else
            {
                $added_by = lang('user');
            }

            if($row->code == 1)
            {
                $type = lang('generated_codes');
            }
            else
            {
                $type = lang('recharge_pocket');
            }

            $row->{'added_by'} = $added_by;
            $row->{'type'}     = $type;

            $balance_data_array[] = $row;
        }

        $types_options[1] = lang('add');
        $types_options[2] = lang('deduct');

        $this->pagination->initialize($config);

        $this->data['page_links']        = $this->pagination->create_links();
        $this->data['lang_id']           = $lang_id;
        $this->data['user_id']           = $user_id;
        $this->data['user_balance']      = $user_balance;

        $this->data['user_balance_data'] = $balance_data_array;
        $this->data['types']             = $types_options;
        $this->data['currency_symbol']   = $currency;

        return $this->load->view('users_read_balance', $this->data, true);
    }

    public function update_user_balance()
    {
        $new_row      = '';
        $balance_data = array();

        $lang_id = $this->data['active_language']->id;

        $amount  = $this->input->post('amount');
        $type    = $this->input->post('type');
        $user_id = $this->input->post('user_id');

        $user_data  = $this->admin_bootstrap->get_user_by_id($user_id);
        $secret_key = $this->config->item('new_encryption_key');
        $secret_iv  = $user_id;

        $user_balance = $this->encryption->decrypt($user_data->user_balance, $secret_key, $secret_iv);

        if($type == 1)
        {
            $balance_data = $this->_balance_operations($user_id, $user_balance, $amount, $type, $lang_id);

            $status         = $balance_data[0];
            $message        = $balance_data[1];
            $return_balance = $balance_data[2];
            $new_row        = $balance_data[3];
        }
        else if($type == 2)
        {
            if($amount > $user_balance)
            {
                $status = 0;
                $message = lang('amount_is_higher_than_balance');
                $return_balance = $user_balance;
            }
            else
            {
                $balance_data = $this->_balance_operations($user_id, $user_balance, $amount, $type, $lang_id);

                $status  = $balance_data[0];
                $message = $balance_data[1];
                $return_balance = $balance_data[2];
                $new_row        = $balance_data[3];
            }
        }

        echo json_encode(array($status, $message, $return_balance, $new_row));
    }

    private function _balance_operations($user_id, $user_balance, $amount, $type, $lang_id)
    {
        $return_status    = 0;
        $message          = '';
        $user_new_balance = $user_balance;

        if($type == 1)
        {
            $user_new_balance = $user_balance + $amount;
            $status_id        = 4;     // admin add

            $return_status    = 1;
            $message          = lang('balance_updated_successfully');

        }
        else if($type == 2)
        {
            $user_new_balance = $user_balance - $amount;
            $status_id        = 5;     // admin deduce

            $return_status    = 1;
            $message          = lang('balance_updated_successfully');
        }

        if($this->admin_bootstrap->encrypt_and_update_users_data($user_id, 'user_balance', $user_new_balance))
        {
            $user_data        = $this->user_model->get_row_data($user_id);
            $currency_symbol  = $this->currency->get_country_symbol($user_data->store_country_id);

            $balance_log_data = array(
                                        'user_id'           => $user_id                     ,
                                        'balance'           => $user_new_balance            ,
                                        'amount'            => $amount                      ,
                                        'store_country_id'  => $user_data->store_country_id ,
                                        'currency_symbol'   => $currency_symbol             ,
                                        'balance_status_id' => $status_id                   ,
                                        'ip_address'        => $this->input->ip_address()   ,
                                        'added_by_admin'    => 1                            ,
                                        'unix_time'         => time()
                                     );

            $this->user_balance_model->insert_balance_log($balance_log_data);

            $log_status = $this->user_balance_model->get_log_status_name($status_id, $lang_id);

            $new_row = "<tr>
                            <td>".date('Y/m/d H:i', time())."</td>
                            <td></td>
                            <td>".lang('admin')."</td>
                            <td>$amount</td>
                            <td>$currency_symbol</td>
                            <td>$log_status</td>
                            <td>".$this->input->ip_address()."</td>
                            <td>$user_new_balance $currency_symbol</td>
                            <td>".lang('recharge_pocket')."</td>
                            <td>".lang('admin')."</td>
                        </tr>";
        }

        return array($return_status, $message, $user_new_balance, $new_row);
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
        $this->load->model('orders/orders_model');

        $users_ids = $this->input->post('row_id');

        if(is_array($users_ids))
        {
            $ids_array = array();

            foreach($users_ids as $user_id)
            {
                $ids_array[] = $user_id['value'];
            }
        }
        else
        {
            $ids_array = array($users_ids);
        }


        foreach($ids_array as $id)
        {
            $check_user_orders = $this->orders_model->check_user_have_orders($id);
            $user_data         = $this->user_model->get_row_data($id);
            $secret_key        = $this->config->item('new_encryption_key');
            $secret_iv         = $id;

            $user_balance      = $this->encryption->decrypt($user_data->user_balance, $secret_key, $secret_iv);
            $user_points       = $this->encryption->encrypt($user_data->user_points, $secret_key, $secret_iv);

            if($check_user_orders)
            {
                echo lang('cant_delete_user_have_orders');
            }
            elseif($user_balance > 0)
            {
                echo lang('cant_delete_user_has_pocket_money');
            }
            elseif($user_points > 0)
            {
                echo lang('cant_delete_user_has_points');
            }
            else
            {
                $this->user_model->remove_user_stores($id);

                $this->ion_auth->delete_user($id);
                echo "1";
            }
        }


    }
/////////////////////////////////////////////////////////////////////

    public function add()
    {
        $validation_msg = false;

        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
        {
            $customer_group_id = $this->input->post('customer_group_id');

            $check_wholesaler  = $this->admin_bootstrap->check_if_wholesaler_group($customer_group_id);
            $is_wholesaller    = false;

            if($check_wholesaler)
            {
                $is_wholesaller = true;
                $this->form_validation->set_rules('sms_name', lang('sms_name'), 'trim|max_length[11]');
                $this->form_validation->set_rules('sms_content', lang('sms_content'), 'trim|max_length[50]');
            }

            $this->form_validation->set_rules('first_name', lang('first_name'), 'trim|required');
            $this->form_validation->set_rules('email', lang('email'), 'trim|valid_email|required|is_unique[users.email]');
            //$this->form_validation->set_rules('phone', lang('phone'), 'trim|required|callback_check_phone');//is_unique[users.phone]');
            $this->form_validation->set_rules('phone', lang('phone'), 'trim|required|is_unique[users.phone]');//');
            $this->form_validation->set_rules('password', lang('password'), 'trim|required');
            $this->form_validation->set_rules('last_name', lang('last_name'), 'trim|required');
            $this->form_validation->set_rules('customer_group_id', lang('customer_group_name'), 'required');
            $this->form_validation->set_rules('country_id', lang('country'), 'required');

            $this->form_validation->set_message('required', lang('required')." : %s ");
            $this->form_validation->set_message('is_unique', lang('is_unique')."  : %s ");
            $this->form_validation->set_message('valid_email', lang('valid_email')."  : %s ");
            $this->form_validation->set_message('max_length', '%s '.lang('max_length_is').': '.' %s');

            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            $validation_msg = true;
        }

        if ($this->form_validation->run() == FALSE)
    		{
    		  $this->_add_form($validation_msg);
        }
        else
        {
            $username          = $this->input->post('first_name');
            $password          = $this->input->post('password');
            $email             = $this->input->post('email');
            $group             = $this->input->post('group_id');
            $first_name        = $this->input->post('first_name');
            $last_name         = $this->input->post('last_name');
            $phone             = $this->input->post('phone');
            $address           = $this->input->post('address');
            $image             = $this->input->post('image');
            $country_id        = $this->input->post('country_id');
            $user_stores       = $this->input->post('user_stores_id');
            $id_image          = $this->input->post('image3');


            $login_auth  = 0;

            if(isset($_POST['auth'] ))
            {
                if($_POST['auth'] == 'google_auth')
                {
                    $login_auth = 2;
                }
                if($_POST['auth'] == 'sms_auth')
                {
                    $login_auth = 1;
                }
            }

            $country          = $this->cities_model->get_country_call_code($country_id);
            $calling_code     = $country->calling_code;
            $phone            = $calling_code.$phone;

            $additional_data = array(
                                        'first_name'            => $first_name,
                                        'last_name'             => $last_name,
                                        'phone'                 => $phone,
                                        'Country_ID'            => $country_id,
                                        'address'               => $address,
                                        'customer_group_id'     => $customer_group_id,
                                        'image'                 => $image,
                                        'active'                => (isset( $_POST['active']))? $this->input->post('active'):0,
                                        'account_sms_activated' => (isset( $_POST['mobile_active']))? $this->input->post('mobile_active'):0,
                                        'mail_list'             => (isset( $_POST['mail_list']))? $this->input->post('mail_list'):0,
                                        'login_auth'            => $login_auth,
                                        'id_image'              => $id_image  ,
                                        'sms_code'              => rand(1000, 9999)
                                    );

            $check_wholesaler  = $this->admin_bootstrap->check_if_wholesaler_group($customer_group_id);
            $is_wholesaller    = false;

            if($check_wholesaler)
            {
                $is_wholesaller = true;

                $additional_data['representative_id'] = $this->input->post('representative_id');
                $additional_data['geocomplete']       = $this->input->post('geocomplete');
                $additional_data['google_map_lat']    = $this->input->post('lat');
                $additional_data['google_map_lng']    = $this->input->post('lng');
                $additional_data['logo']              = $this->input->post('image3');
                $additional_data['sms_name']          = $this->input->post('sms_name');
                $additional_data['sms_content']       = $this->input->post('sms_content');
                $additional_data['header']            = $this->input->post('header');
                $additional_data['footer']            = $this->input->post('footer');
                $additional_data['stop_wholesaler_sms'] = isset($_POST['wholesaler_sms']) ? '1' : '0';
            }

            if (!$this->ion_auth->email_check($email))
            {
                $user_id = $this->ion_auth->register($username, $password, $email, $additional_data, $group);

                if (count($user_stores) != 0)
                {
                    foreach ($user_stores as $store_id)
                    {
                        $user_store_data = array(
                                                    'store_id' => $store_id,
                                                    'user_id'  => $user_id
                                                );

                        $this->user_model->insert_user_store($user_store_data);
                    }
                }

                $this->resend_user_sms_code($user_id);
                $user_data = $this->user_model->get_user_data_by_field('id', $user_id);


                /***************************************/
                $data       = array (
                                        '{id}'         => $user_id,
                                        '{username}'   => $first_name.' '.$last_name ,
                                        '{email}'      => $user_data->email,
                                        '{ip_address}' => $user_data->ip_address,
                                        '{created_on}' => date('Y/m/d H:i', $user_data->created_on),
                                        '{logo_path}'  => base_url().'assets/template/admin/img/logo.png'
                                    );
                $emails []  = $email;
                $this->notifications->create_notification('add_new_user',$data ,$emails);
                /****************************************/
                $this->session->set_flashdata('success',lang('success'));
                redirect('users/admin_users/','refresh');
            }
            else
            {
                $this->session->set_flashdata('user_register_error',lang('user_register_error'));
                redirect('users/admin_users/add', 'refresh');
            }
        }

    }

    private function _add_form($validation_msg)
    {
        $this->_js_and_css_files();

        $lang_id                    = $this->data['active_language']->id;
        $this->data['mode']         = 'add';
        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/add";

        $groups_options             = array();
        $customer_groups_options    = array();
        $representatives_array      = array();
        $countries_array            = array();

        $groups                     = $this->groups_model->get_groups($lang_id);
        $customers_groups           = $this->customer_groups_model->get_customer_groups($lang_id);
        $settings                   = $this->global_model->get_config();
        $wholesaler_group_ids       = $settings->wholesaler_customer_group_id;
        $rep_group_id               = $settings->rep_group_id;
        $representatives            = $this->users_model->get_representivives_users($rep_group_id);
        $countries                  = $this->cities_model->get_user_nationality_filter_data($lang_id);
        $stores                     = $this->stores_model->get_all_stores($lang_id);

        $customer_groups_options[NULL] = '----------------';
        $countries_array[NULL]         = '----------------';

        foreach($groups as $row)
        {
            $groups_options[$row->id] = $row->name;
        }

        foreach($representatives as $row)
        {
            $representatives_array[$row->id] = $row->first_name.' '.$row->last_name;
        }

        foreach($customers_groups as $row)
        {
            $customer_groups_options[$row->id] = $row->title;
        }

        foreach($stores as $row)
        {
            $stores_options[$row->id] = $row->name;
        }

        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }

        foreach($countries as $country)
        {
            $countries_array[$country->id]  = $country->name;
        }

        $this->data['groups_options']           = $groups_options;
        $this->data['customer_groups_options']  = $customer_groups_options;
        $this->data['wholesaler_group_ids']     = $wholesaler_group_ids;
        $this->data['representatives']          = $representatives_array;
        $this->data['user_countries']           = $countries_array;
        $this->data['stores']                   = $stores_options;

        $this->data['content']                  = $this->load->view('users_view', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
    }

    public function geo_maps()
    {

        $this->data['content']       = $this->load->view('maps', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
    }

    public function sms_activate($code, $user)
    {
        $id = $user->id;
        $activation_code = array('sms_code'=>$code);
        // set hook for update meta data
        $this->ion_auth->set_hook('post_register_user', 'send_user_sms', $this->user_model, 'send_sms', array($id,$update));
        $this->ion_auth->update($id,$update);
    	return true;
    }

    public function edit($id)
    {
        if(is_numeric($id))
        {
            $id = intval($id);

            $validation_msg = false;

            $user_old_data  = $this->user_model->get_row_data($id);

            if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
            {
                $email     = trim($this->input->post('email'));
                $phone     = $this->input->post('phone');
                $user      = $this->user_model->get_row_data($id);

                $customer_group_id = $this->input->post('customer_group_id');

                $is_wholesaler     = false;
                $send_notification = false;

                if($user->customer_group_id != $customer_group_id)
                {
                    $data['customer_group_id'] = $customer_group_id;
                    $this->ion_auth->update($id, $data);

                    $send_notification = true;
                }

                $is_wholesaler  = $this->admin_bootstrap->check_if_wholesaler_group($customer_group_id);

                if($is_wholesaler)
                {
                    $is_wholesaler = true;
                    $this->form_validation->set_rules('sms_name', lang('sms_name'), 'trim|max_length[11]');
                    $this->form_validation->set_rules('sms_content', lang('sms_content'), 'trim|max_length[50]');
                }

                if($user_old_data->Country_ID != 0)
                {
                    $user_old_country = $this->cities_model->get_country_call_code($user_old_data->Country_ID);
                    $old_calling_code = $user_old_country->calling_code;
                    $user_phone       = $user_old_data->phone;//substr($user_old_data->phone, strlen($old_calling_code));
                }
                else
                {
                    $user_phone = $phone;
                }
                if($user_phone != $phone)
                {
                    $this->form_validation->set_rules('phone', lang('phone'), 'trim|required|is_unique[users.phone]');
                    //$this->form_validation->set_rules('phone', lang('phone'), 'trim|required|callback_check_phone');
                }

                if($user->email != $email)
                {
                    $this->form_validation->set_rules('email', lang('email'), 'trim|valid_email|required|is_unique[users.email]');
                }

                /*if($user->phone != $phone)
                {
                    $this->form_validation->set_rules('phone', lang('phone'), 'trim|required|callback_check_phone');
                }
                */
                $this->form_validation->set_rules('first_name', lang('first_name'), 'trim|required');
                $this->form_validation->set_rules('last_name', lang('last_name'), 'trim|required');
                $this->form_validation->set_rules('customer_group_id', lang('customer_group_name'), 'trim|required');
                $this->form_validation->set_rules('country_id', lang('country'), 'trim|required');

                $this->form_validation->set_message('required', lang('required')." : %s ");
                $this->form_validation->set_message('is_unique', lang('is_unique')."  : %s ");
                $this->form_validation->set_message('valid_email', lang('valid_email')."  : %s ");
                $this->form_validation->set_message('max_length', '%s '.lang('max_length_is').': '.' %s');

                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

                $general_data   = $this->user_model->get_user($this->data['active_language']->id , $id);
                $validation_msg = true;
            }

            if($this->form_validation->run() == FALSE)
        		{
        		   $this->_edit_form($id, $validation_msg);
            }
            else
            {


                $customer_group_id = $this->input->post('customer_group_id');
                $username          = $this->input->post('first_name');
                $first_name        = $this->input->post('first_name');
                $last_name         = $this->input->post('last_name');
                $country_id        = $this->input->post('country_id');
                $phone             = $this->input->post('phone');
                $id_image          = $this->input->post('image3');


                //$new_country_data = $this->cities_model->get_country_call_code($country_id);
                $new_phone        = $phone;//$new_country_data->calling_code.$phone;


                $login_auth = 0;
                if(isset($_POST['auth'] ))
                {
                    //->>login_auth = 1 for login sms auth
                    //->>login_auth = 2 for login google auth
                    if($_POST['auth'] == 'google_auth')
                    {
                        $login_auth = 2;
                    }
                    if($_POST['auth'] == 'sms_auth')
                    {
                        $login_auth = 1;
                    }
                }

                if(isset( $_POST['password']) && $_POST['password']!= '')
                {

                   $data = array(
                        'username'              => $username,
                        'password'              => $this->input->post('password'),
                        'email'                 => $this->input->post('email'),
                        'first_name'            => $first_name,
                        'last_name'             => $last_name,
                        'phone'                 => $new_phone,
                        'address'               => $this->input->post('address'),
                        'Country_ID'            => $country_id,
                        'customer_group_id'     => $customer_group_id,
                        'image'                 => $this->input->post('image'),
                        'active'                => (isset( $_POST['active']))? $this->input->post('active'):0,
                        'account_sms_activated' => (isset( $_POST['mobile_active']))? $this->input->post('mobile_active'):0,
                        'mail_list'             => (isset( $_POST['mail_list']))? $this->input->post('mail_list'):0,
                        'login_auth'            => $login_auth,
                        'id_image'              => $id_image,
                        'sms_code'              => rand(1000, 9999)
                        );
                }else{

                    $data = array(
                        'username'              => $username,
                        'email'                 => $this->input->post('email'),
                        'first_name'            => $first_name,
                        'last_name'             => $last_name,
                        'phone'                 => $new_phone,
                        'address'               => $this->input->post('address'),
                        'Country_ID'            => $country_id,
                        'customer_group_id'     => $customer_group_id,
                        'image'                 => $this->input->post('image'),
                        'active'                => (isset( $_POST['active']))? $this->input->post('active'):0,
                        'account_sms_activated' => (isset( $_POST['mobile_active']))? $this->input->post('mobile_active'):0,
                        'mail_list'             => (isset( $_POST['mail_list']))? $this->input->post('mail_list'):0,
                        'login_auth'            => $login_auth,
                        'sms_code'              => rand(1000, 9999)
                        );
                }

                if($is_wholesaler)
                {
                    $data['representative_id'] = $this->input->post('representative_id');
                    $data['geocomplete']       = $this->input->post('geocomplete');
                    $data['google_map_lat']    = $this->input->post('lat');
                    $data['google_map_lng']    = $this->input->post('lng');
                    $data['logo']              = $this->input->post('image3');
                    $data['sms_name']          = $this->input->post('sms_name');
                    $data['sms_content']       = $this->input->post('sms_content');
                    $data['header']            = $this->input->post('header');
                    $data['footer']            = $this->input->post('footer');
                    $data['stop_wholesaler_sms']    = isset($_POST['wholesaler_sms']) ? '1' : '0';


                }

                $this->ion_auth->update($id, $data);

                $groupData = $this->input->post('group_id');
                if (isset($groupData) && !empty($groupData))
                {
                    $this->ion_auth->remove_from_group('', $id);
                    foreach ($groupData as $grp)
                    {
    		          $this->ion_auth->add_to_group($grp, $id);
                    }
                }

                $user_stores = $this->input->post('user_stores_id');
                $this->user_model->remove_user_stores($id, $user_stores);

                if (count($user_stores) != 0)
                {
                    foreach ($user_stores as $store_id)
                    {
                        $user_store_data = array(
                                                    'store_id' => $store_id,
                                                    'user_id'  => $id
                                                );

                        $this->user_model->insert_user_store($user_store_data);
                    }

                    //remove other stores permissions

                    //$this->user_model->remove_other_stores_permission($user_stores);
                }

                if($is_wholesaler && $send_notification)
                {
                    $user_new_data = $this->admin_bootstrap->get_user_by_id($id);

                    $emails[]      = $user_new_data->email;
                    $phone         = $user_new_data->phone;

                    $template_data = array(
                                            'logo_path' => base_url().'assets/template/admin/img/logo.png',
                                            'unix_time' => time(),
                                            'username'  => $username,
                                            'wholesaler_link' => base_url().'users/Users/edit_wholesaler_data'
                                          );

                    $this->notifications->create_notification('add_wholesaler', $template_data, $emails, $phone);
                }

                $_SESSION['success'] = lang('updated_successfully');
                $this->session->mark_as_flash('success');

                redirect('users/admin_users','refresh');
            }
        }
    }

    private function _edit_form($id, $validation_msg)
    {
        $this->_js_and_css_files();

        $lang_id                    = $this->data['active_language']->id;

        $groups_options             = array();
        $countries_array            = array();
        $customer_groups_options    = array();
        $ugroups                    = array();
        $representatives_array      = array();
        $u_stores                   = array();
        $stores_options             = array();

        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/edit/".$id;
        $this->data['mode']         = 'edit';
        $this->data['id']           = $id;

        $general_data               = $this->user_model->get_user($lang_id , $id);
        $user_groups                = $this->user_model->get_user_groups($lang_id, $id);
        $user_stores                = $this->user_model->get_user_stores($lang_id, $id);

        $groups                     = $this->groups_model->get_groups($lang_id);
        $customers_groups           = $this->customer_groups_model->get_customer_groups($lang_id);
        $stores                     = $this->stores_model->get_all_stores($lang_id);

        $is_wholesaler       = false;
        $settings            = $this->global_model->get_config();
        $wholesaler_group_id = $settings->wholesaler_customer_group_id;
        $rep_group_id        = $settings->rep_group_id;
        $representatives     = $this->users_model->get_representivives_users($rep_group_id);
        $countries           = $this->cities_model->get_user_nationality_filter_data($lang_id);

        foreach($representatives as $row)
        {
            $representatives_array[$row->id] = $row->first_name.' '.$row->last_name;
        }

        $wholesaler_ids = json_decode($wholesaler_group_id);


        foreach($user_groups as $group)
        {

            $ugroups [$group->group_id]=$group->group_id;
        }

        foreach($groups as $row)
        {

            $groups_options[$row->id] = $row->name;
        }

        foreach($customers_groups as $row)
        {

            $customer_groups_options[$row->id] = $row->title;
        }

        foreach($countries as $country)
        {
            $countries_array[$country->id]  = $country->name;
        }

        foreach($user_stores as $store)
        {
            $u_stores[$store->store_id] = $store->store_id;
        }

        foreach($stores as $row)
        {
            $stores_options[$row->id] = $row->name;
        }


        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }


        /*$user_country = $this->cities_model->get_country_call_code($general_data->Country_ID);
        $calling_code = $user_country->calling_code;
        $user_phone   = substr($general_data->phone, strlen($calling_code));

        $general_data->{'phone'} = $user_phone;
          */

        $this->data['general_data']             = $general_data;
        $this->data['groups_options']           = $groups_options;
        $this->data['customer_groups_options']  = $customer_groups_options;
        $this->data['user_groups']              = $ugroups;
        $this->data['is_wholesaler']            = $is_wholesaler;
        $this->data['wholesaler_group_ids']     = $wholesaler_group_id;
        $this->data['representatives']          = $representatives_array;
        $this->data['user_countries']           = $countries_array;
        $this->data['user_stores']              = $u_stores;
        $this->data['stores']                   = $stores_options;

        $this->data['content']                  = $this->load->view('users_view', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
    }

    function Resend_verification($user_id)
    {
        $user= $this->admin_bootstrap->get_user_by_id($user_id);
        $data = array(
    				'identity'   => $user->email,
    				'id'         => $user->id,
    				'email'      => $user->email,
    				'activation' => $user->activation_code,
    			);


        $message = $this->load->view($this->config->item('email_templates', 'ion_auth').$this->config->item('email_activate', 'ion_auth'), $data, true);

        $this->load->library('email');
       	$this->lang->load('ion_auth');
        $this->load->model('ion_auth_model');

		$this->email->clear();
		$this->email->from($this->config->item('admin_email', 'ion_auth'), $this->config->item('site_title', 'ion_auth'));
		$this->email->to($user->email);
		$this->email->subject($this->config->item('site_title', 'ion_auth') . ' - ' . $this->lang->line('email_activation_subject'));
		$this->email->message($message);

		if ($this->email->send() == TRUE)
		{
			$this->ion_auth_model->trigger_events(array('post_account_creation', 'post_account_creation_successful', 'activation_email_successful'));
			$this->ion_auth->set_message('activation_email_successful');
			//return $user->id;
		}
        redirect(base_url().'users/users/','refresh');
    }

    public function get_representatives()
    {
        $group_id          = $this->input->post('customer_group');
        $check_wholesaler  = $this->admin_bootstrap->check_if_wholesaler_group($group_id);

        if($check_wholesaler)
        {
            $settings        = $this->global_model->get_config();
            $rep_group_id    = $settings->rep_group_id;
            $representatives = $this->users_model->get_representivives_users($rep_group_id);

            $rep_users       = "<select name='representative_id' class='form-control select2'><option value='0'>-----------</option>";

            foreach($representatives as $rep)
            {
                $rep_users .= "<option value=$rep->id>$rep->first_name $rep->last_name</option>";
            }

            $rep_users .= "</select>";
            echo $rep_users;
        }
    }

    public function get_username()
    {
        $user_id = $this->input->post('rep_user_id');

        $user = $this->users_model->get_user($user_id);

        echo $user->first_name.' '.$user->last_name;
    }

    public function resend_user_sms_code($user_id, $details=0)
    {
        $details = intval($details);
        $lang_id = $this->data['active_language']->id;
        $user_id = intval($user_id);
        $user    = $this->user_model->get_user_data_by_field('id', $user_id);

        $this->load->library('notifications');
        $msg = lang('sms_activation_code').' : '.$user->sms_code;
        $this->notifications->send_sms($msg, $user->phone);

        $this->session->set_flashdata('success',lang('message_send'));

        if($details ==1)
        {
            redirect('users/admin_users/read/'.$user_id.'/'.$lang_id, 'refresh');
        }
        else
        {
            redirect('users/admin_users/', 'refresh');
        }

    }

    public function resend_user_activation_email($user_id, $details = 0)
    {
        $user_id = intval($user_id);
        $details = intval($user_id);

        $lang_id = $this->data['active_language']->id;
        //-->> make activation code
        $deactivate = $this->ion_auth_model->deactivate($user_id);

    	$activation_code = $this->ion_auth_model->activation_code;
		$identity        = $this->config->item('identity', 'ion_auth');
		$user            = $this->ion_auth_model->user($user_id)->row();
        $email           = $user->email;

		$data = array(
        				'identity'   => $user->{$identity},
        				'id'         => $user_id,
        				'email'      => $email,
        				'activation' => $activation_code,
        			 );

        //-->>>send email
        $message = $this->load->view($this->config->item('email_templates', 'ion_auth').$this->config->item('email_activate', 'ion_auth'), $data, true);

		$this->email->clear();
		$this->email->from($this->config->item('admin_email', 'ion_auth'), $this->config->item('site_title', 'ion_auth'));
		$this->email->to($email);
		$this->email->subject($this->config->item('site_title', 'ion_auth') . ' - ' . $this->lang->line('email_activation_subject'));
		$this->email->message($message);

		if($this->email->send())
		{
            //$this->session->set_flashdata('success', lang('email_msg_sent'));
            $_SESSION['success'] = lang('email_msg_sent');
            $this->session->mark_as_flash('success');
		}
        else
        {
            //$this->session->set_flashdata('faild',lang('email_not_sent'));
            $_SESSION['faild'] = lang('email_not_sent');
            $this->session->mark_as_flash('faild');
        }

        if($details ==1)
        {
            redirect('users/admin_users/read/'.$user_id.'/'.$lang_id, 'refresh');
        }
        else
        {
            redirect('users/admin_users/', 'refresh');
        }

    }

    public function check_phone($phone)
    {
        $country_id   = $this->input->post('country_id');
        $country      = $this->cities_model->get_country_call_code($country_id);

        $calling_code = $country->calling_code;
        $user_phone   = $calling_code.$phone;

        $user_data = $this->user_model->get_row_data($this->input->post('id'));

        $phone_changed = true;
        if(substr($user_data->phone, strlen($calling_code)) == $phone)
        {
            $phone_changed = false;
        }

        $user_phone_exist              = $this->user_model->check_user_phone_exist($phone);
        $phone_with_calling_code_exist = $this->user_model->check_user_phone_exist($user_phone);



        if(($user_phone_exist || $phone_with_calling_code_exist) && !$phone_changed )
        {
            $this->form_validation->set_message('check_phone', lang('is_unique')."  : %s ");

            return FALSE;
        }
        elseif(substr($phone, 0, 1) == 0 || substr($phone, 0, 1) == '+')
        {
            $this->form_validation->set_message('check_phone', lang('phone_number_not_start_with_zero'));
            return FALSE;
        }
        elseif(substr($phone, 0, strlen($calling_code)) == $calling_code)
        {
            $this->form_validation->set_message('check_phone', lang('phone_number_not_start_with_calling_code'));
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    public function show_map($lng, $lat)
    {
        if($lng != '' && $lat != '')
        {
            $this->data['lng'] = trim(strip_tags($lng));
            $this->data['lat'] = trim(strip_tags($lat));
        }
        else
        {
            $this->data['msg'] = 'error';
        }

        $this->data['content'] = $this->load->view('users_maps', $this->data, true);
        $this->load->view('Admin/main_frame', $this->data);
    }

    public function view_user_stores_permissions($user_id)
    {
        $user_id = intval($user_id);

        $user_stores_count = $this->user_model->user_stores_count($user_id);

        if($user_stores_count > 0)
        {
            $stores_permissions = $this->user_stores_permissions($user_id);
        }
        else
        {
            $this->data['error_msg'] = ('user_has_no_stores');
        }

        $this->data['content']  = $this->load->view('user_stores_permissions', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
    }

    public function user_stores_permissions($user_id)
    {
        $user_id = intval($user_id);
        $this->load->model('root/controllers_model');

        $store_permissions_array = array();
        $lang_id = $this->data['active_language']->id;

        $user_stores_data = $this->user_model->get_user_stores($lang_id, $user_id);

        foreach($user_stores_data as $store)
        {
            //->>>GET User's old permissions
            $user_permissions  = $this->permissions_model->get_user_stores_permissions($user_id, $store->store_id);
            //echo '<pre>'; print_r($user_permissions); die();
            $old_permissions   = array();
            $old_controllers   = array();

            foreach($user_permissions as $permission)
            {
                $old_permissions["{$permission->controller_id}"][]  = $permission->permission_id;
                $old_controllers[]                                  = $permission->controller_id;
            }

            //->>>GET Controllers
            $controllers            = $this->controllers_model->get_store_controllers($lang_id);
            $modules                = array();
            $controller_permissions = array();

            foreach($controllers as $controller)
            {
                //ticket status and tickets categories are not included
                if(($controller->id != 53) && ($controller->id != 54))
                {
                     $modules["{$controller->module_id}"]         = array(
                                                                            'module'            => $controller->module,
                                                                            'module_name'       => $controller->module_name,
                                                                            'module_icon_class' => $controller->module_icon_class
                                                                        );

                     $controller_permissions["{$controller->id}"] = $this->permissions_model->get_permissions($controller->id, $lang_id);
                }
            }

            //->>>GET permissions
            $store->{'permissions'}                 = $controller_permissions;
            $store->{'user_store_old_permissions'}  = $old_permissions;
            $store->{'user_store_old_controllers'}  = $old_controllers;

            $store_permissions_array[] = $store;
        }


        $this->data['user_id']              = $user_id;
        $this->data['controllers']          = $controllers;
        $this->data['modules']              = $modules;
        $this->data['stores_permissions']   = $store_permissions_array;

    }

    public function save_store_permission()
    {
        $user_id        = $this->input->post('user_id');
        $checked_nodes   = $this->input->post('checked_nodes');

        $this->permissions_model->delete_user_store_permissions($user_id);

        if(isset($_POST['checked_nodes']) && count($_POST['checked_nodes']) > 0)
        {
            foreach($checked_nodes as $node)
            {
                $p = explode('_',$node);

                if($p[0]=='p')
                {
                    $controller_id  = $p[4];
                    $permission     = $p[1];
                    $store_id       = $p[2];

                    $user_store_data = array(
                                                'store_id'      => $store_id,
                                                'user_id'       => $user_id ,
                                                'permission_id' => $permission,
                                                'controller_id' => $controller_id
                                            );

                    $this->permissions_model->save_user_store_permissions($user_store_data);
                }


            }
        }

        echo "	<div class='alert alert-success alert-dismissable'>
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								<strong>".lang('success')."</strong>".lang('')."</div>";
    }


}
