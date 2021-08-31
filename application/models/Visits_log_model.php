<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Visits_log_model extends CI_Model
{
    public function insert_visit_log($data)
    {
        $this->db->insert('visits_log', $data);
    }
////////////////////////////
}
