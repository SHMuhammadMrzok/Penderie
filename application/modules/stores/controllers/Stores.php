<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class stores extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        require(APPPATH . 'includes/front_end_global.php');
        $this->load->model('stores_model');

        $this->session->set_userdata('site_redir', current_url());
    }

    var $data = array();

    public function index()
    {
        $firsts_array  = array();
        $first_stores  = $this->stores_model->get_menu_first_stores($this->data['lang_id'], 1);
        $first_stores_ids = array();

        foreach($first_stores as $store)
        {
            if(! is_null($store->id ))
            {
                if($store->store_id == $this->config->item('first_store_id') && $this->config->item('first_store_id') != 0 )
                {
                    unset($firsts_array[0]);
                    $firsts_array[0] = $store;
                }
    
                if($store->store_id == $this->config->item('second_store_id') && $this->config->item('second_store_id')!= 0 )
                {
                    unset($firsts_array[1]);
                    $firsts_array[1] = $store;
                }
    
                if($store->store_id == $this->config->item('third_store_id') && $this->config->item('third_store_id') != 0)
                {
                    unset($firsts_array[2]);
                    $firsts_array[2] = $store;
                }
    
                if($store->store_id == $this->config->item('fourth_store_id') && $this->config->item('fourth_store_id') != 0)
                {
                    unset($firsts_array[3]);
                    $firsts_array[3] = $store;
                }
    
                if($store->store_id == $this->config->item('fifth_store_id'))
                {
                    unset($firsts_array[4]);
                    $firsts_array[4] = $store;
                }
    
                $first_stores_ids[] = $store->store_id;
            }
        }

        ksort($firsts_array);
//echo '<pre>'; print_r(); die();
        $stores = $this->stores_model->get_all_stores($this->data['lang_id'], $first_stores_ids);
        

        $stores_array = array_merge($firsts_array, $stores);
//echo '<pre>'; print_r($stores); die();

        $this->data['stores'] = $stores_array;

        $this->data['content'] = $this->load->view('stores_view', $this->data, true);
        $this->load->view('site/main_frame',$this->data);
    }



/************************************************************************/
}
