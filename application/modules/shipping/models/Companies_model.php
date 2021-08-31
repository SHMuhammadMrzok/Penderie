<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Companies_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /***************Insert***********************/
    public function insert_company($data)
    {
       return $this->db->insert('shipping_companies', $data);
    }

    public function insert_companies_translation($companies_translation_data)
    {
        return $this->db->insert('shipping_companies_translation', $companies_translation_data);
    }

/********************Get********************/

    public function get_count_all_compinies($lang_id, $search_word='')
    {
        $this->db->join('shipping_companies_translation' ,'shipping_companies.id = shipping_companies_translation.shipping_company_id');

        if(trim($search_word) !='')
        {
            $this->db->where('(shipping_companies_translation.name LIKE "%'.$search_word.'%" OR shipping_companies.service_name LIKE "%'.$search_word.'%")');
        }

        $this->db->where('shipping_companies_translation.lang_id',$lang_id);

        return $this->db->count_all_results('shipping_companies');
    }

    public function get_compinies_data($lang_id, $limit, $offset, $search_word='', $order_by='', $order_state)
    {
        $this->db->join('shipping_companies_translation' ,'shipping_companies.id = shipping_companies_translation.shipping_company_id');

        if(trim($search_word) !='')
        {
            $this->db->where('(shipping_companies_translation.name LIKE "%'.$search_word.'%" OR shipping_companies.service_name LIKE "%'.$search_word.'%")');
        }

        $this->db->where('shipping_companies_translation.lang_id',$lang_id);

        $this->db->order_by('shipping_companies.id', $order_state);


        $result = $this->db->get('shipping_companies', $limit, $offset);

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_company_row($id)
    {
        $this->db->where('id', $id);
        $row = $this->db->get('shipping_companies');

        if($row)
        {
           return $row->row();
        }
        else
        {
           return false;
        }
    }

    public function get_company_translation_result($company_id)
    {
        $this->db->where('shipping_company_id', $company_id);
        $result = $this->db->get('shipping_companies_translation');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }



    public function get_row_data($id, $lang_id)
    {
        $this->db->join('shipping_companies_translation' ,'shipping_companies.id = shipping_companies_translation.shipping_company_id AND shipping_companies_translation.lang_id='.$lang_id);
        $this->db->where('shipping_companies.id', $id);

        $result = $this->db->get('shipping_companies');

        if($result)
        {
            return $result->row();
        }
        else
        {
            return false;
        }
    }

    public function get_shipping_companies_result($lang_id)
    {
        $this->db->join('shipping_companies_translation' ,'shipping_companies.id = shipping_companies_translation.shipping_company_id AND shipping_companies_translation.lang_id='.$lang_id);
        $this->db->where('shipping_companies.active', 1); // Mrzok Edit

        $result = $this->db->get('shipping_companies');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_shipping_companies_translation_result()
    {
        $this->db->join('shipping_companies_translation' ,'shipping_companies.id = shipping_companies_translation.shipping_company_id');

        $result = $this->db->get('shipping_companies');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    /*****************Update ************************/

    public function update_company($id, $company_data)
    {
        $this->db->where('id', $id);
        $this->db->update('shipping_companies', $company_data);
    }

    public function update_company_translation($company_id, $lang_id, $company_translation_data)
    {
        $this->db->where('shipping_company_id', $company_id);
        $this->db->where('lang_id', $lang_id);

        $this->db->update('shipping_companies_translation', $company_translation_data);
    }


    /***************************Delete *********************************/

    public function delete_company_data($ids_array)
    {
        $this->db->where_in('id', $ids_array);
        $this->db->delete('shipping_companies');

        $this->db->where_in('shipping_company_id', $ids_array);
        $this->db->delete('shipping_companies_translation');

        echo '1';
    }

    public function get_companies_data($lang_id, $conds=array())
    {
      $this->db->join('shipping_companies_translation' ,'shipping_companies.id = shipping_companies_translation.shipping_company_id
                      AND shipping_companies_translation.lang_id='.$lang_id);

      if(count($conds) != 0)
      {
        foreach($conds as $key=>$val)
        {
          $this->db->where($key,$val);
        }
      }

      $result = $this->db->get('shipping_companies');

      if($result)
      {
        return $result->result();
      }
      else {
        return false;
      }
    }



    /****************************************************/
}
