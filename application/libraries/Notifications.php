<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Notifications
{
    public $CI ;
    public $active_lang ;
    public $lang_id  ;
    public $global_vars;


    public function __construct()
    {
        $this->CI=&get_instance();

        $this->CI->load->model('notifications_model');
        $this->CI->load->model('root/lang_model');
        $this->CI->load->model('settings/admin_model');
        $this->CI->load->model('users/users_model');

        $this->CI->config->load('config');

        $settings = $this->CI->admin_model->get_settings_general_data();

        //$lang = $this->CI->session->userdata('lang');
        $lang_id = isset($_SESSION['lang_id'])?$_SESSION['lang_id']:'';

        if($lang_id == '')
        {
            //$lang_row = $this->CI->lang_model->get_language_row_by_lang('english');
            $this->lang_id = $settings->admin_notification_lang_id;
        }
        else
        {
            $lang_row       = $this->CI->lang_model->get_language_row_by_id($lang_id);
            $this->lang_id  = $lang_row->id;
        }

        $this->admin_lang_id = $settings->admin_notification_lang_id;

        $site_name = $this->CI->admin_model->get_site_name($lang_id);
        $base_url  = $this->CI->config->item('base_url');

        $this->global_vars = array(
                                    'site_name' => $site_name,
                                    'site_url'  => $base_url,
                                    'year'      => date('Y')
                                   );
    }


    public function create_notification($event , $data , $emails_array = array(), $sms_number = '', $store_id=0)
    {
        // merge data with global vars
        $data = array_merge($data, $this->global_vars);
        
        $data['logo_path'] = base_url().'assets/template/site/img/logo.png'; // base_url().'assets/uploads/'.$this->config->item('logo'); //$this->data['images_path'].$this->config->item('logo'); 
        
        //echo '<pre>'; print_r($data); die();
        //-->>Get event data
        $event_row = $this->CI->notifications_model->get_event_row($event);

        $admin_message          = '';
        $admin_email_message    = '';
        $admin_sms_message      = '';

        $sms_message            = '';
        $email_message          = '';

        $admin_template_row     = '';
        $template_row           = '';

        if($event_row != false)
        {
            /*******************Admin Notification***************************/

            if($event_row->enable_admin == 1 || $event_row->enable_admin_sms == 1 || $event_row->enable_admin_email == 1)
            {
                $admin_template_row   = $this->CI->notifications_model->get_template_row($event_row->admin_template_id , $this->admin_lang_id);
            }

            if($admin_template_row != false )
            {
                /*************send dashboard notification**************/

                if($event_row->enable_admin == 1)
                {
                    $admin_message = $admin_template_row->template;

                    foreach($data as $key => $val)
                    {
                        $admin_message = str_replace("{".$key."}",$val ,$admin_message);
                    }

                    $admin_data = array (
                                            'notification_text' => $admin_message,
                                            'type'              => 'admin',
                                            'unix_time'         => time(),
                                            'read'              => 0,
                                            'event_id'          => $event_row->id
                                        );
                   $this->CI->notifications_model->insert_notification($admin_data);

                }

                /*************send admin email notification**************/

                if($event_row->enable_admin_email == 1)
                {
                    $admin_email_message = $admin_template_row->email_template;
                    $admin_email_title   = $admin_template_row->email_title;

                    foreach($data as $key => $val)
                    {
                        $admin_email_message = str_replace("{".$key."}",$val, $admin_email_message);
                        $admin_email_title   = str_replace("{".$key."}",$val, $admin_email_title);
                    }

                    $admin_emails = $this->CI->notifications_model->get_notification_emails($store_id);

                    if($admin_emails != false || $event_row->user_group_id != 0)
                    {
                       foreach($admin_emails as $email)
                       {
                            $emails[] = $email->email;

                            $admin_email_data = array (
                                                        'notification_text' => $admin_email_message ,
                                                        'type'              => 'email'              ,
                                                        'unix_time'         => time()               ,
                                                        'to'                => $email->email        ,
                                                        'event_id'          => $event_row->id
                                                   );
                            //$this->CI->notifications_model->insert_notification($admin_email_data);
                        }

                        $group_users = $this->CI->users_model->get_group_users($event_row->user_group_id);

                        if(count($group_users) != 0)
                        {
                            foreach($group_users as $user)
                            {
                                $emails[] = $user->email;

                                $group_email_data = array(
                                                            'notification_text' => $admin_email_message ,
                                                            'type'              => 'user_group'         ,
                                                            'unix_time'         => time()               ,
                                                            'to'                => $user->email         ,
                                                            'event_id'          => $event_row->id
                                                         );
                                //$this->CI->notifications_model->insert_notification($group_email_data);
                            }

                        }

                        $emails = array_unique($emails);

                       /***send email to admin emails (notification emails table)*****/
                       $this->send_emails_notifications($admin_email_title, $admin_email_message, $emails);

                    }

                 }
                /*************send admin sms notification**************/
                if($event_row->enable_admin_sms == 1)
                {
                    $admin_sms_message = $admin_template_row->sms_template;

                    foreach($data as $key => $val)
                    {
                        $admin_sms_message = str_replace("{".$key."}",$val ,$admin_sms_message);
                    }

                     $admin_sms      =  $this->CI->notifications_model->get_notification_sms();
                     $mobile_numbers = '';

                    if($admin_sms != false || $event_row->user_group_id != 0)
                    {

                       foreach($admin_sms as $sms)
                       {
                            $mobile_numbers .= $sms->mobile.",";

                            $admin_sms_data = array (
                                            'notification_text' => $admin_sms_message ,
                                            'type'              => 'sms'              ,
                                            'unix_time'         => time()             ,
                                            'to'                => $sms->mobile       ,
                                            'event_id'          => $event_row->id
                                       );
                            //$this->CI->notifications_model->insert_notification($admin_sms_data);
                       }


                        $group_users = $this->CI->users_model->get_group_users($event_row->user_group_id);

                        if(count($group_users) != 0)
                        {
                            foreach($group_users as $user)
                            {
                                $mobile_numbers .= $user->phone.",";

                                $user_group_sms_data = array (
                                                                'notification_text' => $admin_sms_message ,
                                                                'type'              => 'sms'              ,
                                                                'unix_time'         => time()             ,
                                                                'to'                => $user->phone       ,
                                                                'event_id'          => $event_row->id
                                                            );
                                //$this->CI->notifications_model->insert_notification($user_group_sms_data);
                            }

                        }



                       /***send sms to admin sms numbers ( sms_notifications table)*****/
                       $this->send_sms($admin_sms_message , $mobile_numbers);

                    }


                 }
            }
           /*******************End Admin Notifications ********************/

           /*******************user SMS Notification***************************/
            if($event_row != false )
            {
                $template_row   = $this->CI->notifications_model->get_template_row($event_row->template_id , $this->lang_id);
            }

            if($template_row != false )
            {
                if($event_row->enable_sms == 1)
                {
                    $sms_message = $template_row->sms_template;

                    foreach($data as $key => $val)
                    {
                        $sms_message = str_replace("{".$key."}",$val,$sms_message);
                    }

                    if($sms_number != '')
                    {
                        /***send sms to user number ******/

                        $this->send_sms($sms_message , $sms_number);

                        $sms_data = array (
                                                    'notification_text' => $sms_message ,
                                                    'type'              => 'sms'        ,
                                                    'unix_time'         => time()       ,
                                                    'to'                => $sms_number  ,
                                                    'event_id'          => $event_row->id
                                               );
                        //$this->CI->notifications_model->insert_notification($sms_data);

                    }

                }

                /*******************Email Notification***************************/

                if($event_row->enable_email == 1)
                {
                    $email_message = $template_row->email_template;
                    $email_title   = $template_row->email_title;


                    foreach($data as $key => $val)
                    {
                        $email_message = str_replace("{".$key."}", $val, $email_message);
                        $email_title   = str_replace("{".$key."}", $val, $email_title);
                    }

                     if(!empty($emails_array))
                     {
                        $this->send_emails_notifications($email_title, $email_message , $emails_array);


                        for($i=0 ; $i < count($emails_array); $i++)
                        {
                            $email_data = array (
                                                    'notification_text' => $email_message    ,
                                                    'type'              => 'email'           ,
                                                    'unix_time'         => time()            ,
                                                    'to'                => $emails_array[$i] ,
                                                    'event_id'          => $event_row->id
                                               );
                          // $this->CI->notifications_model->insert_notification($email_data);
                        }
                     }
                 }
           }//if(template_row)
        }//if(event != false)
    }//function

   public function send_sms ($msg , $mobile, $sender_name='')
   {
        $this->CI->load->library('sms_lib');
        $this->CI->load->library('gateways');

        //////////////////////////////////////////////////////
        //   Variables Decleration.
        //////////////////////////////////////////////////////

        //$this->load->model('settings/gateways_model');

        $sms_username = $this->CI->gateways->get_gateway_field_value('sms_username');
        $sms_password = $this->CI->gateways->get_gateway_field_value('sms_password');

        if($sender_name == '')
        {
            $sender_name = $this->CI->gateways->get_gateway_field_value('sms_sender_name');//'0504208820';
        }

        //$GatewayURL   = "http://www.oursms.net/api/sendsms.php";//"http://www.mobily.ws/api/msgSend.php";
        $UserName     = $sms_username;
        $UserPassword = $sms_password;
        $Ucode        = 'E'; // U for unicode , E for english

        ////////////////////////////////

        $FainalResult = $this->CI->sms_lib->SendSms($UserName, $UserPassword, '966'.$mobile, $sender_name, $msg);

        if($FainalResult=="1")
        {
    	   //echo "<p class=note>Thanks, Your Message has Sent successfully</p>";die();
           return true;

        }else{

           return false;
        }


   }

   public function send_emails_notifications($email_title, $email_message , $emails)
   {
        $this->CI->load->model('global_model');
        $this->CI->load->library('email');

        /*$config['protocol'] = 'sendmail';
		$config['mailpath'] = '/usr/sbin/sendmail';
		$config['charset'] = 'utf-8';
		$config['wordwrap'] = TRUE;
*/
$config['protocol'] = 'smtp';
$config['smtp_host'] = 'ssl://smtp.googlemail.com';
$config['smtp_port'] = '465';
$config['smtp_user'] = 'admin@penderie.net';
$config['smtp_pass'] = '123456aA@#zxc';

        $config['mailtype'] = 'html';
        $config['newline'] = "\r\n";


		$config['charset']   = 'utf-8';
		$config['wordwrap']  = TRUE;

        $this->CI->email->initialize($config);
        $this->CI->email->clear();

        $settings = $this->CI->global_model->get_config();
        $from     = $settings->sender_email;

        for($i=0 ; $i < count($emails); $i++)
        {
            if(isset($emails[$i]))
            {
              $this->CI->email->set_newline("\r\n");
              $this->CI->email->from($from);
              $this->CI->email->to($emails[$i]);
      		  $this->CI->email->subject($email_title);
      		  $this->CI->email->message($email_message);

              if(!$this->CI->email->send())
              {
                  //echo $this->CI->email->print_debugger();
                  //die();
              }
            }

        }

   }

}
