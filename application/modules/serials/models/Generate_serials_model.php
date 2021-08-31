<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Generate_serials_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    /********************Insert *****************************/
    public function insert_serials_data($serials_data)
    {
        return $this->db->insert('serials', $serials_data);
    }
    
/****************************************************************/
}