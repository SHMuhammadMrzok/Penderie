<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Vats_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function insert_table_data($table_name, $data)
    {
      return $this->db->insert($table_name, $data);
    }

    public function get_count_all_vats($lang_id ,$search_word ='')
    {
       $this->db->join('vats_translation' ,'vats.id = vats_translation.vat_id
                        AND vats_translation.lang_id ='.$lang_id);

       if(trim($search_word) !='')
       {
           $this->db->where('(vats_translation.name LIKE "%'.$search_word.'%" OR vats.amount LIKE "%'.$search_word.'%" )');
       }

       return $this->db->count_all_results('vats');
   }

   public function get_vats_data($lang_id, $limit, $offset, $search_word='', $order_by='', $order_state)
   {
       $this->db->select('vats_translation.*, vats.* , vats.id as id');

       $this->db->join('vats_translation' ,'vats.id = vats_translation.vat_id
                        AND vats_translation.lang_id='.$lang_id);

       if(trim($search_word) !='')
       {
           $this->db->where('(vats_translation.name LIKE "%'.$search_word.'%" OR vats.amount LIKE "%'.$search_word.'%" )');
       }

       if($order_by != '')
       {
           if($order_by == lang('amount'))
           {
               $this->db->order_by('vats.amount', $order_state);
           }
           elseif($order_by == lang('type'))
           {
               $this->db->order_by('vats.amount', $order_state);
           }
           elseif($order_by == lang('name'))
           {
               $this->db->order_by('vats_translation.type', $order_state);
           }
           else
           {
               $this->db->order_by('vats.id', $order_state);
           }
       }
       else
       {
           $this->db->order_by('vats.id',$order_state);
       }

       $result = $this->db->get('vats',$limit,$offset);

       if($result)
       {
           return $result->result();
       }
       else {
         return false;
       }
   }

   public function get_row_data($id, $lang_id)
   {
     $this->db->select('vats_translation.*, vats.* , vats.id as id');

     $this->db->join('vats_translation' ,'vats.id = vats_translation.vat_id
                      AND vats_translation.lang_id='.$lang_id);
     $this->db->where('vats.id', $id);

     $query = $this->db->get('vats');
     if($query)
     {
       return $query->row();
     }
     else {
       return false;
     }
   }

   public function check_if_used_vats($vat_ids)
   {
     $this->db->where_in('products_countries.vat_id', $vat_ids);
     $count = $this->db->count_all_results('products_countries');
     if($count > 0)
     {
       return true;
     }
     else {
       return false;
     }
   }

   public function update_table_data($table_name, $conds, $updated_data)
   {
     foreach($conds as $key=>$val)
     {
       $this->db->where($key, $val);
     }

     return $this->db->update($table_name, $updated_data);
   }

   public function get_table_data($table_name, $conds, $type)
   {
     foreach($conds as $key=>$val)
     {
       $this->db->where($key, $val);
     }

     $query = $this->db->get($table_name);

     if($query)
     {
       return $query->$type();
     }
     else {
       return false;
     }
   }

   public function get_available_vats($lang_id)
   {
     $this->db->select('vats_translation.*, vats.* , vats.id as id');

     $this->db->join('vats_translation' ,'vats.id = vats_translation.vat_id
                      AND vats_translation.lang_id='.$lang_id);

     $this->db->where('vats.active', 1);

     $query = $this->db->get('vats');

     if($query)
     {
       return $query->result();
     }
     else {
       return false;
     }

   }

/****************************************************************/
}
