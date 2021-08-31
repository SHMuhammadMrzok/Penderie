<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Advertisement_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    /********Delet*************/
    public function delete_advertisements_translation($advertisement_id)
    {
        $this->db->where('advertisement_id',$advertisement_id);
        $this->db->delete('advertisements_translation');
    }


    public function delete_advertisment_data($advertisements_id_array)
    {
        $this->db->where_in('id',$advertisements_id_array);
        $this->db->delete('advertisements');

        $this->db->where_in('advertisement_id',$advertisements_id_array);
        $this->db->delete('advertisements_translation');

        echo '1';
    }
    /********insert*************/

    public function insert_advertisements($data)
    {
        return $this->db->insert('advertisements', $data);
    }

    public function insert_advertisements_translation($advertisements_translation_data)
    {
        return $this->db->insert('advertisements_translation', $advertisements_translation_data);
    }

     /********get*************/

    public function get_advertisements_translation_result($id)
    {
        $this->db->select('advertisements_translation.*');
        $this->db->join('advertisements_translation','advertisements.id = advertisements_translation.advertisement_id');
        $this->db->where('advertisements.id',$id);
        $query = $this->db->get('advertisements');

        if($query)
        {
            return $query->result();
        }
    }


    public function get_advertisements_result($id)
    {
        $this->db->where('id',$id);
        $query = $this->db->get('advertisements');

        if($query)
        {
            return $query->row();
        }
    }

     public function get_advertisements_data($lang_id,$limit,$offset,$search_word='',$order_by='',$order_state)
    {
        $this->db->select('advertisements.*, advertisements_translation.*, advertisements.id as id ');

        $this->db->join('advertisements_translation' ,'advertisements.id = advertisements_translation.advertisement_id');

        if(trim($search_word) !='')
        {
            $this->db->like('advertisements_translation.title', $search_word, 'both');
            $this->db->or_like('advertisements_translation.description', $search_word, 'both');
        }

        if($order_by != '')
        {
            if($order_by == lang('location'))
            {
                $this->db->order_by('advertisements.location',$order_state);
            }
            elseif($order_by == lang('active'))
            {
                $this->db->order_by('advertisements.active',$order_state);
            }
            else
            {
                $this->db->order_by('advertisements.id',$order_state);
            }
        }
        else
        {
            $this->db->order_by('advertisements.id',$order_state);
        }

        $this->db->where('advertisements_translation.lang_id',$lang_id);

        $result = $this->db->get('advertisements',$limit,$offset);

        if($result)
        {
            return $result->result();
        }
    }

    public function get_count_all_advertisements($lang_id,$search_word='')
    {
        $this->db->join('advertisements_translation' ,'advertisements.id = advertisements_translation.advertisement_id');

        if(trim($search_word) !='')
        {
            $this->db->like('advertisements_translation.title', $search_word, 'both');
            $this->db->or_like('advertisements_translation.description', $search_word, 'both');
        }


        $this->db->where('advertisements_translation.lang_id',$lang_id);

        return $this->db->count_all_results('advertisements');
    }

     public function get_row_data($id,$display_lang_id)
    {
        $this->db->select('advertisements.*, advertisements_translation.*, advertisements.id as id ');

        $this->db->join('advertisements_translation' ,'advertisements.id = advertisements_translation.advertisement_id');

        $this->db->where('advertisements.id',$id);
        $this->db->where('advertisements_translation.lang_id',$display_lang_id);

        $result = $this->db->get('advertisements');

        if($result)
        {
            return $result->row();
        }
    }

    public function get_advertisments($lang_id, $location, $limit=0, $conditions=array())
    {
        $this->db->select('advertisements.*, advertisements_translation.*, advertisements.id as id ');

        $this->db->join('advertisements_translation' ,'advertisements.id = advertisements_translation.advertisement_id');

        if(count($conditions) != 0)
        {
          foreach($conditions as $key=>$val)
          {
            $this->db->where($key, $val);
          }
        }

        $this->db->where('advertisements.active', 1);
        $this->db->where('advertisements.location', $location);
        $this->db->where('advertisements_translation.lang_id',$lang_id);

        $this->db->order_by('advertisements.id', 'desc');

        if($limit == 0)
        {
            $result = $this->db->get('advertisements');
        }
        else
        {
            $result = $this->db->get('advertisements', $limit);
        }

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_right_advertisments($lang_id)
    {
        $this->db->select('advertisements.*, advertisements_translation.*, advertisements.id as id ');

        $this->db->join('advertisements_translation' ,'advertisements.id = advertisements_translation.advertisement_id');

        $this->db->where('advertisements.location','left');
        $this->db->where('advertisements_translation.lang_id',$lang_id);

        $result = $this->db->get('advertisements');
        if($result)
        {
            return $result->result();
        }
    }
    /********update*************/

    public function update_advertisements_translation($advertisement_id,$lang_id,$advertisements_translation_data)
    {
        $this->db->where('advertisement_id',$advertisement_id);
        $this->db->where('lang_id',$lang_id);
        $this->db->update('advertisements_translation',$advertisements_translation_data);
    }

    public function update_advertisements($advertisement_id,$advertisements_data)
    {
        $this->db->where('id',$advertisement_id);
        $this->db->update('advertisements',$advertisements_data);
    }

    public function sort_rows($old_index,$new_index)
    { echo $old_index."####".$new_index;die();
        if($old_index < $new_index)  ///IF ROW MOVED UP
        { echo '1111';die();
            $this->db->where('sort',$old_index);
            $data = array('sort'=>$new_index);
            $this->db->update('advertisements',$data);

            $this->db->where('sort >',$old_index);
            $query = $this->db->get('advertisements');
            print_r($query);die();
            /*foreach($query as $row)
            {
                $old_sort_value = $row->sort;
                $new_sort_value = $old_sort_value + 1;
                $new_sort_data = array('sort' => $new_sort_data);

                $this->db->where('id',$row->id);
                $this->db->update('advertisements',$new_sort_data);

            }*/
        }
        if($old_index > $new_index)
        { echo '222';die();

        }
    }

    public function update_hits($adv_id)
    {
        $this->db->where('id', $adv_id);
        $this->db->set('hits', 'hits+1', FALSE);
        return $this->db->update('advertisements');
    }


/****************************************************************/
}
