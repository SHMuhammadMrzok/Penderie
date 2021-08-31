<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Admin_wrapping_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    /****************Get****************/

    public function get_count_all_data($lang_id, $search_word='')
    {
        $this->db->select('wrapping.* ,colors_translation.*');

        $this->db->join('colors_translation', 'wrapping.color_id = colors_translation.color_id AND colors_translation.lang_id ='.$lang_id);

        if(trim($search_word) !='')
        {
            $this->db->where('( colors_translation.name LIKE "%'.$search_word.'%")');
        }

        return $this->db->count_all_results('wrapping');
    }

    public function get_grid_data($lang_id, $limit, $offset, $search_word='', $order_by='', $order_state='desc')
    {
        $this->db->select('wrapping.* ,colors_translation.*, colors_translation.name as color');

        $this->db->join('colors_translation', 'wrapping.color_id = colors_translation.color_id AND colors_translation.lang_id ='.$lang_id);

        if(trim($search_word) !='')
        {
            $this->db->where('( colors_translation.name LIKE "%'.$search_word.'%")');
        }

        if($order_by != '')
        {
            if($order_by == lang('type'))
            {
                $this->db->order_by('wrapping.type', $order_state);
            }
            elseif($order_by == lang('colors'))
            {
                $this->db->order_by('colors_translation.name', $order_state);
            }
            else
            {
                $this->db->order_by('wrapping.id',$order_state);
            }
        }
        else
        {
            $this->db->order_by('wrapping.id',$order_state);
        }

        $result = $this->db->get('wrapping',$limit,$offset);

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_wrapping_row($id)
    {
        $this->db->where('wrapping.id',$id);

        $result = $this->db->get('wrapping');

        if($result)
        {
            return $result->row();
        }
        else
        {
            return false;
        }
    }

    public function get_wrapping_data($id, $lang_id)
    {
        $this->db->select('wrapping.*, colors_translation.name as color');
        $this->db->join('colors_translation', 'wrapping.color_id = colors_translation.color_id AND colors_translation.lang_id ='.$lang_id);

        $this->db->where('wrapping.id', $id);
        $row = $this->db->get('wrapping');

        if($row)
        {
            return $row->row();
        }
        else
        {
            return false;
        }
    }

    public function get_wrapping_type_data($lang_id, $type)
    {
        $this->db->select('wrapping.*, colors_translation.name as color');
        $this->db->join('colors_translation', 'wrapping.color_id = colors_translation.color_id
        AND colors_translation.lang_id ='.$lang_id, 'left');

        $this->db->where('wrapping.type', $type);
        $this->db->where('wrapping.active', 1);
        //$this->db->order_by('colors_translation.name', 'asc');

        $result = $this->db->get('wrapping');
        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function check_existed_type($color_id, $type)
    {
        $this->db->where('color_id', $color_id);
        $this->db->where('type', $type);

        $count = $this->db->count_all_results('wrapping');

        if($count > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function get_wrapping_cost($ids_array=array())
    {
        $this->db->select('SUM(wrapping.cost) as total_cost');

        if(count($ids_array) != 0)
        {
            $this->db->where_in('id', $ids_array);
        }

        $query = $this->db->get('wrapping')->row();

        if($query)
        {
            return $query->total_cost;
        }
        else
        {
            return false;
        }
    }

   /*************************DELETE*******************************/

    public function delete_wrapping_data($ids_array)
    {
        $this->db->where_in('id', $ids_array);
        $this->db->delete('wrapping');

/*        $this->db->where_in('wrapping_id', $ids_array);
        $this->db->delete('wrapping_translation');
  */
        echo '1';
    }

  /*****************INSERT***************************************/

    public function insert_wrapping($data)
    {

        return $this->db->insert('wrapping', $data);
    }

    public function insert_wrapping_translation($data)
    {

        return $this->db->insert('wrapping_translation', $data);
    }


    /***********************UPDATE*************************/

    public function update_wrapping($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('wrapping', $data);
    }

/////////////////////////////////////////////////
}
