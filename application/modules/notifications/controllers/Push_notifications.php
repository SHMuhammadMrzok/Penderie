<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Push_notifications extends CI_Controller
{
    public   $lang_row;
    public function __construct()
    {
        parent::__construct();

        require(APPPATH . 'includes/global_vars.php');

        $this->load->model('notification_model');

        $this->lang_row = $this->admin_bootstrap->get_active_language_row();
    }

    /**************** List functions **********************/

    public function index()
    {
        $lang_id = $this->data['active_language']->id;

        $this->data['count_all_records'] = $this->notification_model->get_count_all_push_notifications();
        $this->data['data_language']     = $this->lang_model->get_active_data_languages();


        $this->data['columns']           = array(
                                                  lang('text'),
                                                  lang('send'),
                                                );

        $this->data['orders']            = array(
                                                  lang('text')
                                                );


        $this->data['actions']           = array( 'delete'=>lang('delete'));

        $this->data['content']           = $this->load->view('Admin/grid/grid_html', $this->data, true);
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


        $grid_data  = $this->notification_model->get_push_notifications_data($limit, $offset, $search_word, $order_by, $order_state);

        $db_columns = array(
                             'id',
                             'text',
                             'send'
                           );

        $this->data['hidden_fields'] = array('id','sort');

        $new_grid_data = array();

        foreach($grid_data as $key =>$row)
        {

            foreach($db_columns as $column)
            {
                if($column == 'send')
                {
                    $new_grid_data[$key][$column] = '<a class="btn btn-sm green filter-submit margin-bottom" href="'.base_url().'notifications/push_notifications/send_push/'.$row->id.'">'.lang('send').'</a>';
                }
                else
                {
                    $new_grid_data[$key][$column] = $row->{$column};
                }

            }
        }

        $this->data['grid_data']          = $new_grid_data;
        $this->data['count_all_records']  = $this->notification_model->get_count_all_push_notifications($search_word);
        $this->data['display_lang_id']    = $lang_id;



        $count_data  = $this->data['count_all_records'];
        $output_data = $this->load->view('Admin/grid/grid_data',$this->data, true);

        echo json_encode(array($output_data, $count_data, $search_word));
    }

    public function send_push($push_id)
    {
        // to send push notification message
        $this->load->library('pushnotification');
        $data = $this->notification_model->get_push_notifications_row_data($push_id);

        $this->pushnotification->sendPush($data->text); // parse
        $this->pushnotification->sendMessage($data->title, $data->text); //one signal

        $_SESSION['success'] = ('notification sent successfully');
        $this->session->mark_as_flash('success');
        redirect('notifications/push_notifications/','refresh');
    }

    public function read($id)
    {
        $id = intval($id);

        if($id)
        {
            $data = $this->notification_model->get_push_notifications_row_data($id);


            $row_data = array(
                                lang('title')   => $data->title ,
                                lang('text')    => $data->text ,
                                lang('send')    => '<a href="'.base_url().'notifications/push_notifications/send_push/'.$data->id.'">'.lang('send').'</a>'
                             );

            $this->data['row_data'] = $row_data;

            $this->data['content']  = $this->load->view('Admin/grid/read_view', $this->data, true);
            $this->load->view('Admin/main_frame',$this->data);
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
        $notification_ids = $this->input->post('row_id');

        if(is_array($notification_ids))
        {
            $ids_array = array();

            foreach($notification_ids as $notification_id)
            {
                $ids_array[] = $notification_id['value'];
            }
        }
        else
        {
            $ids_array = array($notification_ids);
        }

        $this->notification_model->delete_push_notifications_data($ids_array);


    }

     /***********************ADD & Edit Functions ************************/

    public function add()
    {
        $validation_msg = false;

        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
        {
            $this->form_validation->set_rules('text', lang('text'), 'trim|required');
            $this->form_validation->set_rules('title', lang('title'), 'trim|required');
            $this->form_validation->set_message('required', lang('required')."  : %s ");
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

            $validation_msg = true;
        }

        if ($this->form_validation->run() == FALSE)
		{
		  $this->_add_form($validation_msg);
        }
        else
        {
            $text   = strip_tags($this->input->post('text', true));
            $title  = strip_tags($this->input->post('title', true));

            $general_data = array(
                                            'text'      => $text,
                                            'title'     => $title,
                                            'unix_time' => time()
                                          );

            if($this->notification_model->insert_push_notification_data($general_data))
            {
                $_SESSION['success'] = lang('success');
                $this->session->mark_as_flash('success');
            }
            else
            {
                $_SESSION['error'] = lang('error');
                $this->session->mark_as_flash('error');
            }

            redirect('notifications/push_notifications/','refresh');
        }
    }

    private function _add_form($validation_msg)
    {
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }

        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/add";
        $this->data['content']      = $this->load->view('push_notifications', $this->data, true);
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
                $noti_id = intval($this->input->post('id', true));

                $this->form_validation->set_rules('text', lang('text'), 'trim|required');
                $this->form_validation->set_rules('title', lang('title'), 'trim|required');

                $this->form_validation->set_message('required', lang('required')."  : %s ");
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

                $validation_msg = true;
            }
        }

        if($this->form_validation->run() == FALSE)
		{
		   $this->_edit_form($id, $validation_msg);
        }
        else
        {
            $text   = strip_tags($this->input->post('text', true));
            $title  = strip_tags($this->input->post('title', true));

            $template_data = array(
                                    'text'  => $text,
                                    'title' => $title
                                  );

            $this->notification_model->update_push_notification($noti_id, $template_data);



            $_SESSION['success'] = lang('updated_successfully');
            $this->session->mark_as_flash('success');

            redirect('notifications/push_notifications/','refresh');
        }
    }

    private function _edit_form($id, $validation_msg)
    {
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }

        $this->data['form_action']  = $this->data['module'] . "/" . $this->data['controller'] . "/edit/".$id;
        $this->data['id']           = $id;
        $lang_id                    = $this->data['active_language']->id;
        $general_data               = $this->notification_model->get_push_notifications_row_data($id);


        $this->data['general_data'] = $general_data;
        $this->data['content']      = $this->load->view('push_notifications', $this->data, true);

        $this->load->view('Admin/main_frame',$this->data);
    }

/************************************************************************/
}
