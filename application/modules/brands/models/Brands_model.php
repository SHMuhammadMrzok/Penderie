<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Brands_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    /**********************INSERT*******************************/
    public function insert_brand($data)
    {
        return $this->db->insert('brands', $data);
    }

    public function insert_brands_translation($brand_translation_data)
    {
        return $this->db->insert('brands_translation', $brand_translation_data);
    }

    /**********************GET*******************************/
    public function get_brand_row($id)
    {
        $this->db->where('id',$id);
        $query = $this->db->get('brands');

        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }
    public function get_brand_translation_result($id)
    {
        $this->db->where('brands_translation.brand_id', $id);
        $query = $this->db->get('brands_translation');

        if($query)
        {
            return $query->result();
        }
        else
        {
            return false;
        }
    }


    /**********************Update*******************************/
    public function update_brand($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('brands',$data);
    }
    public function update_brands_translation($id, $lang_id, $brand_translation_data)
    {
        $this->db->where('brand_id', $id);
        $this->db->where('lang_id', $lang_id);
        return $this->db->update('brands_translation', $brand_translation_data);
    }

    /**********************DELETE*******************************/
    public function delete_brand_data($ids_array)
    {
        $this->db->where_in('id', $ids_array);
        $this->db->delete('brands');

        $this->db->where_in('brand_id', $ids_array);
        $this->db->delete('brands_translation');

        echo '1';
    }

    public function get_count_all_brands($lang_id ,$search_word ='')
    {
        $this->db->join('brands_translation' ,'brands.id = brands_translation.brand_id');

        if(trim($search_word) !='')
        {
            $this->db->like('brands_translation.name', $search_word, 'both');
        }

        $this->db->where('brands_translation.lang_id',$lang_id);

        return $this->db->count_all_results('brands');
    }

    public function get_brands_data($lang_id, $limit=0, $offset=0, $search_word='', $order_by='', $order_state='desc')
    {
        $this->db->select('brands_translation.* , brands.*, brands.id as id, brands_translation.name as brand_name');

        $this->db->join('brands_translation' ,'brands.id = brands_translation.brand_id AND brands_translation.lang_id ='. $lang_id);

        if(trim($search_word) !='')
        {
            $this->db->like('brands_translation.name', $search_word, 'both');
        }

        if($order_by != '')
        {
            if($order_by == lang('brand'))
            {
                $this->db->order_by('brands_translation.name', $order_state);
            }
            else
            {
                $this->db->order_by('brands.id', $order_state);
            }
        }
        else
        {
            $this->db->order_by('brands.id', $order_state);
        }

        if($limit != 0)
        {
            $result = $this->db->get('brands', $limit,$offset);
        }
        else
        {
            $result = $this->db->get('brands');
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

    public function get_row_data($id, $display_lang_id)
    {
        $this->db->select('brands_translation.* , brands.* , brands_translation.name as name');

        $this->db->join('brands_translation' ,'brands.id = brands_translation.brand_id');

        $this->db->where('brands.id',$id);
        $this->db->where('brands_translation.lang_id',$display_lang_id);

        $result = $this->db->get('brands');

        if($result)
        {
            return $result->row();
        }
        else
        {
            return false;
        }
    }


    public function all_brands()
    {
        $this->db->where('active', 1);

        $result = $this->db->get('brands');

        if($result)
        {
            return $result->result_array();
        }
        else
        {
            return false;
        }
    }

    public function get_all_brands($display_lang_id, $limit=0, $offset=0, $conds_array=array(), $brands_ids=array())
    {
        $this->db->select('brands_translation.* , brands.* , brands.id as id, brands_translation.name as name');

        $this->db->join('brands_translation' ,'brands.id = brands_translation.brand_id');
        $this->db->where('brands_translation.lang_id',$display_lang_id);
        $this->db->where('brands.active', 1);

        if(count($conds_array) != 0)
        {
            foreach($conds_array as $key=> $val)
            {
                $this->db->where($key, $val);
            }
        }

        if(count($brands_ids) != 0)
        {
          $this->db->where_in('brands.id', $brands_ids);
        }
        
        if($limit != 0)
        {
            $result = $this->db->get('brands', $limit, $offset);
        }
        else
        {
            $result = $this->db->get('brands');
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

    public function get_cat_brands($lang_id, $cat_id)
    {
      $this->db->select('brands.*, brands_translation.*');

      $this->db->join('products', 'brands.id=products.brand_id');
      $this->db->join('brands_translation', 'brands.id = brands_translation.brand_id
                       AND brands_translation.lang_id='.$lang_id);

      $this->db->where('products.cat_id', $cat_id);
      $this->db->group_by('brands.id');

      $result = $this->db->get('brands');

      if($result)
      {
        return $result->result();
      }
      else {
        return false;
      }
    }


/****************************************************************/
}
