<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Qrcode extends CI_Controller
{
    public $crud;
    
    public function __construct()
    {
        parent::__construct();
        
        require(APPPATH . 'includes/front_end_global.php');
        require(APPPATH . 'libraries/pHPGangsta_GoogleAuthenticator.php');
        //require(APPPATH . 'libraries/FixedBitNotation.php');
        //require(APPPATH . 'libraries/GoogleAuthenticator.php');
        //$this->load->library('GoogleAuthenticator');
        
    }
    
    public function index()
    {
        $user = $this->user_bootstrap->get_user_data();
        
        if($user->login_auth == 2)
        {
            $ga = new PHPGangsta_GoogleAuthenticator();

            $secret = $ga->createSecret();
            
            $qrCodeUrl = $ga->getQRCodeGoogleUrl($user->email, $secret, 'Lik4card');

            //$secret                  = $this->GoogleAuthenticator->createSecret();
            
            $data = array('google_auth_secret_key'=>$secret);
            
            if($this->ion_auth->update($user->id, $data))
            {
                $this->session->set_flashdata('message', $this->ion_auth->messages());
            }
            else
            {
                $this->session->set_flashdata('error', $this->ion_auth->errors()); 
            }
            //$qrCodeUrl               = $this->GoogleAuthenticator->getQRCodeGoogleUrl($user->email, $secret, 'Lik4card');
            
                /*$g = new GoogleAuthenticator();
                
                $secret = $g->generateSecret();
                
                $data = array('google_auth_secret_key'=>$secret);
            
                if($this->ion_auth->update($user->id, $data))
                {
                    $this->session->set_flashdata('message', $this->ion_auth->messages());
                }
                else
                {
                    $this->session->set_flashdata('error', $this->ion_auth->errors()); 
                }
                
               // $code = $g->getCode($secret);
               
                $qrCodeUrl= $g->getURL('test','like4card' , $secret);
                */
                
            $this->data['qrCodeUrl'] = $qrCodeUrl;
            
            $this->data['content']   = $this->load->view('qrcode', $this->data, true);
            $this->load->view('site/inner_main_frame',$this->data);
        }
    }
/*********************************************************/
}