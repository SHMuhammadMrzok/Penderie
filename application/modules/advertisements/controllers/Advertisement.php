<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Advertisement extends CI_Controller
{
    public $crud;
    
    public function __construct()
    {
        parent::__construct();
        
        require(APPPATH . 'includes/front_end_global.php');
        $this->load->model('advertisement_model');
    }

    public function track_link($adv_id)
    {
        if(is_numeric($adv_id))
        {
            $adv_id   = intval($adv_id);
            $lang_id  = $this->data['lang_id'];
            $adv_data = $this->advertisement_model->get_row_data($adv_id, $lang_id);
            
            if($adv_data)
            {
                // update hits value
                $this->advertisement_model->update_hits($adv_id);
                redirect($adv_data->url, 'refresh');
            }
        }
    }
   
/************************************************************************/    
}