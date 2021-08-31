<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Front_end_global extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        require(APPPATH . 'includes/front_end_global.php');

        $this->load->library('shopping_cart');
        $this->load->library('user_bootstrap');

    }

    var $data = array();

    /*public function change_lang_country($lang_id=2)
    {
        $lang_ids        = array();
        $countries_ids   = array();

        //$lang_id         = intval(strip_tags($this->input->post('lang_id', TRUE)));
        $country_id      = intval(strip_tags($this->input->post('country_id', TRUE)));
        $prev_country_id = $_SESSION['country_id'];

        $user_id         = $this->user_bootstrap->get_user_id();
        $languages       = $this->lang_model->get_active_structure_languages();
        $countries       = $this->global_model->get_countries_structure();

        foreach ($languages as $lang)
        {
            $lang_ids[] = $lang->id;
        }

        foreach ($countries as $country)
        {
            $countries_ids[] = $country->id;
        }


        if($lang_id == 0 || ! in_array($lang_id, $lang_ids))
        {
            $lang_id = $_SESSION['lang_id'];
        }

        if($country_id == 0 || ! in_array($country_id, $countries_ids))
        {
            $country_id = $_SESSION['country_id'];
        }


        if($country_id != $prev_country_id )
        {
            //destroy shopping cart
            $session_id = session_id();
            $ip_address = $this->input->ip_address();

            $this->shopping_cart->set_user_data($user_id, $session_id, $ip_address, $country_id, $lang_id);
            $this->shopping_cart->delete();
        }

        // update user balance to new store

        if($user_id != 0)
        {
            $this->user_bootstrap->check_user_country_store_id($user_id, $country_id);
        }

        $this->user_bootstrap->set_lang_id($lang_id);

        $_SESSION['country_id'] = $country_id;

        if(isset($_SESSION['site_redir']))
        {
            redirect($_SESSION['site_redir']);
        }
        else
        {
            redirect(base_url(),'refresh');
        }
    }*/

    public function change_lang_country($lang_id=0)
    {
        $lang_ids        = array();
        $countries_ids   = array();
        
        if($lang_id == 0)
        {
            $lang_id         = intval($this->input->post('lang_id', TRUE));
        }
        $country_id      = intval($this->input->post('country_id', TRUE));
        $prev_country_id = $_SESSION['country_id'];

        $user_id         = $this->user_bootstrap->get_user_id();
        $languages       = $this->lang_model->get_active_structure_languages();
        $countries       = $this->global_model->get_countries_structure();

        foreach ($languages as $lang)
        {
            $lang_ids[] = $lang->id;
        }

        foreach ($countries as $country)
        {
            $countries_ids[] = $country->id;
        }

        if($lang_id == 0 || ! in_array($lang_id, $lang_ids))
        {
            $lang_id = $_SESSION['lang_id'];
        }

        if($country_id == 0 || ! in_array($country_id, $countries_ids))
        {
            $country_id = $_SESSION['country_id'];
        }

        if($country_id != $prev_country_id )
        {
            //destroy shopping cart
            $session_id = session_id();
            $ip_address = $this->input->ip_address();

            $this->shopping_cart->set_user_data($user_id, $session_id, $ip_address, $country_id, $lang_id);
            $this->shopping_cart->delete();
        }

        // update user balance to new store

        if($user_id != 0)
        {
            $this->user_bootstrap->check_user_country_store_id($user_id, $country_id);
        }

        $this->user_bootstrap->set_lang_id($lang_id);

        $_SESSION['country_id'] = $country_id;

        if(isset($_SESSION['site_redir']))
        {
            redirect($_SESSION['site_redir']);
        }
        else
        {
            redirect(base_url(),'refresh');
        }
    }
/******************************************************************************/
}
