<?php #File Path application/modules/stores/libraries/Stores_lib.php?>
<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
/**
 * 
 */
class Stores_lib
{
    public $CI ;
    
    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->model('stores_model');
    }
    
    public function insert_store($store_data)
    {
        $this->CI->stores_model->insert_store($store_data);
        return $this->CI->db->insert_id();
    }
    
    public function insert_store_translation($stores_translation_data)
    {
        return $this->CI->stores_model->insert_stores_translation($stores_translation_data);
    }
}
?>
