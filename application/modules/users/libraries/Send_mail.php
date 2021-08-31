<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Send_mail
{
    function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->library('email');
        $this->_init();
    }
    
    function _init()
    {
        $this->CI->load->model('start_up_model');
        foreach($this->CI->start_up_model->get_mail_config()->result() as $config)
        {
            $this->CI->config->set_item($config->Var_Name,$config->Var_Value);
        }
    }
    
    function mailer_add($mail)
    {
        $this->CI->email->from($this->CI->config->item('site_email'));
        $this->CI->email->to($mail['Email']);
        $this->CI->email->subject($mail['Subject']);
        $this->CI->email->message($mail['Text']);
        
        if($this->CI->email->send())
        {
            return true;
        }else{
            return false;
        }
    }
    
	
    function mail_send($mail)
    {        
			 $config['charset'] = 'utf-8';
			 $config['mailtype'] = "html";
			 //$config['send_multipart'] = false;
			 $this->CI->email->initialize($config);
        $this->CI->email->from($this->CI->config->item('site_email'));
        $this->CI->email->to($mail['Email']);
        $this->CI->email->subject($mail['Subject']);
        $this->CI->email->message($mail['Text']);
        
        if($this->CI->email->send())
        {
            return true;
        }else{
            return false;
        }
    }
    
    function mail_admin($data)
    {
        $message = "<p dir='".lang('dir')."'>تم استلام إستشارة جديدة .... <br /><br />رقم الإستشارة : ".$data['id']
                ."<br /><br />".lang('Consultingtext')." : ".$data['consult']
                ."<br /><br />".lang('Consultingserialcode')." : ".$data['serialcode']."</p>";
        
        $this->CI->email->from($data['email'], $data['person_name']);
        $this->CI->email->to($this->CI->config->item('site_email'));
        $this->CI->email->message($data['consult']);
        $this->CI->email->subject($data['title']);
        
        if($this->CI->email->send())
        {
            return true;
        }else{
            return false;
        }
    }
    
    function mail_user($data)
    {
        $message = "<p dir='".lang('dir')."'>تم استلام الاستشارة وسيتم الرد عليها في أقرب وقت ممكن .... <br /><br />رقم الإستشارة : ".$data['id']
                ."<br /><br />".lang('Consultingtext')." : ".$data['consult']
                ."<br /><br />".lang('Consultingserialcode')." : ".$data['serialcode']
                ."<br /><br />".lang('Consultinglink')." : ".anchor('ConsultingVerify',lang('Press_here'))."</p>";
        
        $this->CI->email->from($this->CI->config->item('site_email'));
        $this->CI->email->to($data['email']);
        $this->CI->email->message($message);
        $this->CI->email->subject($data['title']);
        
        if($this->CI->email->send())
        {
            return true;
        }else{
            return false;
        }
    }
    
    function mail_reply_user($mail)
    {
        $message = "<p dir='".lang('dir')."'>".lang('thank_alert')
                ."<br /><br />".lang('answered')." ".lang('on')." ".$mail['typee']
                ."<br /><br />".lang('op_title')." : ".$mail['title']
                ."<br /><br />".lang('op_text')." : ".$mail['text']
                ."<br /><br />".lang('reply')." : ".$mail['reply']."</p>";
        
        $this->CI->email->from($this->CI->config->item('site_email'));
        $this->CI->email->to($mail['email']);
        $this->CI->email->message($message);
        $this->CI->email->subject(lang('reply').' '.lang('on').' : '.$mail['title']);
        
        if($this->CI->email->send())
        {
            return true;
        }else{
            return false;
        }
    }

}