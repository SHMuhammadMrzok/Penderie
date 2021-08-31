<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Optional_fields_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    /**********************INSERT*******************************/
    public function insert_option($data)
    {
        return $this->db->insert('optional_fields', $data);
    }

    public function insert_option_translation($translation_data)
    {
        return $this->db->insert('optional_fields_translation', $translation_data);
    }

    public function insert_optional_field_option($data)
    {
        return $this->db->insert('optional_fields_options', $data);
    }

    public function insert_optional_field_option_translation($translation_data)
    {
        return $this->db->insert('optional_fields_options_translation', $translation_data);
    }

    public function insert_data($table_name, $data)
    {
        return $this->db->insert($table_name, $data);
    }


    /**********************Update*******************************/
    public function update_option_field($id, $option_data)
    {
        $this->db->where('id', $id);
        return $this->db->update('optional_fields', $option_data);
    }

    public function update_option_translation($option_id, $lang_id, $option_translation_data)
    {
        $this->db->where('optional_field_id', $option_id);
        $this->db->where('lang_id', $lang_id);

        return $this->db->update('optional_fields_translation', $option_translation_data);
    }

    /**********************DELETE*******************************/
    public function delete_optional_field_data($ids_array)
    {
        foreach($ids_array as $id)
        {
            $this->delete_option_options($id);
        }

        $this->db->where_in('id', $ids_array);
        $this->db->delete('optional_fields');

        $this->db->where_in('optional_field_id', $ids_array);
        $this->db->delete('optional_fields_translation');
    }

    public function delete_option_options($option_id)
    {
        $option_options = $this->get_optional_field_options_result($option_id);

        foreach($option_options as $option)
        {
            $this->db->where('optional_field_option_id', $option->id);
            $this->db->delete('optional_fields_options_translation');

            $this->db->where('id', $option->id);
            $this->db->delete('optional_fields_options');
        }
    }


    /**********************GET*******************************/


    public function get_count_all_optional_fields($lang_id, $search_word='')
    {
        $this->db->join('form_fields_types' ,'optional_fields.field_type_id = form_fields_types.id');
        $this->db->join('optional_fields_translation', 'optional_fields.id = optional_fields_translation.optional_field_id AND optional_fields_translation.lang_id ='.$lang_id);

        if(trim($search_word) !='')
        {
            $this->db->where('(form_fields_types.type_name LIKE "%'.$search_word.'%" OR optional_fields_translation.label LIKE "%'.$search_word.'%")');
        }

        return $this->db->count_all_results('optional_fields');
    }

    public function get_optional_fields_data($lang_id, $limit, $offset, $search_word='', $order_state)
    {
        $this->db->select('optional_fields.* , form_fields_types.* ,optional_fields_translation.*, optional_fields.id as id');

        $this->db->join('form_fields_types' ,'optional_fields.field_type_id = form_fields_types.id');
        $this->db->join('optional_fields_translation', 'optional_fields.id = optional_fields_translation.optional_field_id AND optional_fields_translation.lang_id ='.$lang_id);

        if(trim($search_word) !='')
        {
            $this->db->where('(form_fields_types.type_name LIKE "%'.$search_word.'%" OR optional_fields_translation.label LIKE "%'.$search_word.'%")');
        }

        $this->db->order_by('optional_fields.id', $order_state);


        $result = $this->db->get('optional_fields', $limit, $offset);

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }

    }

    public function get_all_optional_fields_result($lang_id, $secondary=0)
    {
        $this->db->select('optional_fields.*, optional_fields_translation.*');
        $this->db->join('optional_fields_translation', 'optional_fields.id = optional_fields_translation.optional_field_id AND optional_fields_translation.lang_id ='.$lang_id);
        $this->db->where('optional_fields.secondary', $secondary);

        $result = $this->db->get('optional_fields');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_types()
    {
        $this->db->order_by('id', 'asc');
        $query = $this->db->get('form_fields_types');

        if($query)
        {
            return $query->result();
        }
        else
        {
            return false;
        }
    }

    public function get_optional_field_row($option_id)
    {
        $this->db->where('id', $option_id);

        $row = $this->db->get('optional_fields');

        if($row)
        {
            return $row->row();
        }
        else
        {
            return false;
        }
    }

    public function get_optional_field_translation_result($option_id)
    {
        $this->db->where('optional_field_id', $option_id);
        $result = $this->db->get('optional_fields_translation');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_optional_field_options_result($option_id)
    {
        $this->db->select('optional_fields_options.*, optional_fields_options_translation.*');
        $this->db->join('optional_fields_options_translation', 'optional_fields_options.id = optional_fields_options_translation.optional_field_option_id');

        $this->db->where('optional_fields_options.optional_field_id', $option_id);
        $result = $this->db->get('optional_fields_options');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_optional_field_options($option_id=0, $lang_id, $product_id=0, $active_options_only=0, $products_ids=array(), $cats_ids=array(), $is_free=0)
    {
        $this->db->select('optional_fields_options.*, optional_fields_options.id as option_id,
                           optional_fields_options_translation.*, products_optional_fields_options_costs.cost,
                           products_optional_fields_options_costs.image, products_optional_fields_options_costs.active,
                           optional_fields_translation.label');

        $this->db->join('products_optional_fields', 'optional_fields_options.optional_field_id = products_optional_fields.optional_field_id', 'left');

        $this->db->join('optional_fields_options_translation', 'optional_fields_options.id = optional_fields_options_translation.optional_field_option_id
                         AND optional_fields_options_translation.lang_id = '.$lang_id);

        $this->db->join('optional_fields_translation', 'optional_fields_options.optional_field_id = optional_fields_translation.optional_field_id
                         AND optional_fields_translation.lang_id = '.$lang_id);

        if(count($products_ids) !=0)
        {
          $this->db->join('products_optional_fields_options_costs', 'optional_fields_options.id = products_optional_fields_options_costs.option_id', 'left');
          $this->db->where_in('products_optional_fields.product_id', $products_ids);
        }
        else if($product_id != 0){
          /*
          $this->db->join('products_optional_fields_options_costs', 'optional_fields_options.id = products_optional_fields_options_costs.option_id', 'left');
          $this->db->where('products_optional_fields.product_id', $product_id);
          */
          
          $this->db->join('products_optional_fields_options_costs', 'products_optional_fields_options_costs.option_id = optional_fields_options.id 
          AND products_optional_fields_options_costs.product_id = '.$product_id , 'left');
          $this->db->where('products_optional_fields.product_id', $product_id);
        }
        else if(count($cats_ids) != 0) {
          $this->db->join('products_optional_fields_options_costs', 'optional_fields_options.id = products_optional_fields_options_costs.option_id', 'left');
          $this->db->join('products', 'products_optional_fields_options_costs.product_id = products.id');

          $this->db->where_in('products.cat_id', $cats_ids);
        }
        else {
          $this->db->join('products_optional_fields_options_costs', 'optional_fields_options.id = products_optional_fields_options_costs.option_id', 'left');
        }


        if($active_options_only == 1 && $is_free==0)
        {
            $this->db->where('products_optional_fields_options_costs.active', 1);
        }

        if($option_id != 0)
        {
          $this->db->where('optional_fields_options.optional_field_id', $option_id);
        }

        $this->db->group_by('optional_fields_options.id');

        $this->db->order_by('optional_fields_options.priority', 'asc');

        $result = $this->db->get('optional_fields_options');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function count_options_products($options_ids)
    {
        $this->db->where_in('optional_field_id', $options_ids);

        return $this->db->count_all_results('products_optional_fields');
    }

    public function get_row_data($id, $lang_id)
    {
        $this->db->select('optional_fields.*, optional_fields_translation.*, form_fields_types.type_name');

        $this->db->join('optional_fields_translation', 'optional_fields.id = optional_fields_translation.optional_field_id AND optional_fields_translation.lang_id ='.$lang_id);
        $this->db->join('form_fields_types', 'optional_fields.field_type_id = form_fields_types.id');

        $this->db->where('optional_fields.id', $id);

        $result = $this->db->get('optional_fields')->row();

        if($result)
        {
            return $result;
        }
        else
        {
            return false;
        }
    }

    public function get_option_options_data($option_id, $lang_id)
    {
        $this->db->select('optional_fields_options.*, optional_fields_options_translation.*');
        $this->db->join('optional_fields_options_translation', 'optional_fields_options.id = optional_fields_options_translation.optional_field_option_id AND optional_fields_options_translation.lang_id ='.$lang_id);

        $this->db->where('optional_fields_options.optional_field_id', $option_id);
        $result = $this->db->get('optional_fields_options');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_option_options_row($option_id, $lang_id)
    {
        $this->db->select('optional_fields_options.*, optional_fields_options_translation.*');
        $this->db->join('optional_fields_options_translation', 'optional_fields_options.id = optional_fields_options_translation.optional_field_option_id AND optional_fields_options_translation.lang_id ='.$lang_id);

        $this->db->where('optional_fields_options.id', $option_id);
        $query = $this->db->get('optional_fields_options');

        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }

    public function get_optional_field_form_data($option_id)
    {
        $this->db->select('form_fields_types.*, optional_fields.*');

        $this->db->join('optional_fields', 'products_optional_fields.optional_field_id = optional_fields.id');
        $this->db->join('form_fields_types', 'optional_fields.field_type_id = form_fields_types.id');

        $this->db->where('products_optional_fields.id', $option_id);

        $result = $this->db->get('products_optional_fields');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_product_option_row($option_id)
    {
        $this->db->where('id', $option_id);

        $result = $this->db->get('products_optional_fields');

        if($result)
        {
            return $result->row();
        }
        else
        {
            return false;
        }
    }

    public function get_optional_fields_groups_translation($lang_id)
    {
        $this->db->where('lang_id', $lang_id);
        $result = $this->db->get('optional_fields_groups_translation');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_product_groups($product_id, $lang_id)
    {
        $this->db->select('optional_fields_groups_translation.name as group_name, optional_fields_groups_translation.group_id as group_id, optional_fields_groups.*');

        $this->db->join('optional_fields', 'products_optional_fields.optional_field_id = optional_fields.id');
        $this->db->join('optional_fields_groups', 'products_optional_fields.field_group_id = optional_fields_groups.id');
        $this->db->join('optional_fields_groups_translation', 'products_optional_fields.field_group_id = optional_fields_groups_translation.group_id
                         AND optional_fields_groups_translation.lang_id ='.$lang_id, 'left');

        $this->db->where('products_optional_fields.product_id', $product_id);
        $this->db->group_by('optional_fields_groups_translation.group_id');

        $result = $this->db->get('products_optional_fields');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }

    }


    /**********************Groups**********************/

    public function get_count_all_options_groups($lang_id, $search_word='')
    {
        $this->db->join('optional_fields_groups_translation', 'optional_fields_groups.id = optional_fields_groups_translation.group_id
                            AND optional_fields_groups_translation.lang_id ='.$lang_id);

        if(trim($search_word) !='')
        {
            $this->db->where('(optional_fields_groups_translation.name LIKE "%'.$search_word.'%" OR optional_fields_groups.group_limit LIKE "%'.$search_word.'%")');
        }

        return $this->db->count_all_results('optional_fields_groups');
    }

    public function get_optional_fields_groups_data($lang_id, $limit, $offset, $search_word, $order_state)
    {
        $this->db->select('optional_fields_groups.*, optional_fields_groups_translation.name');
        $this->db->join('optional_fields_groups_translation', 'optional_fields_groups.id = optional_fields_groups_translation.group_id
                            AND optional_fields_groups_translation.lang_id ='.$lang_id);

        if(trim($search_word) !='')
        {
            $this->db->where('(optional_fields_groups_translation.name LIKE "%'.$search_word.'%" OR optional_fields_groups.group_limit LIKE "%'.$search_word.'%")');
        }

        $this->db->order_by('optional_fields_groups.id', $order_state);

        $result = $this->db->get('optional_fields_groups');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_table_row($table, $id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get($table);

        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }

    public function get_group_translation_result($group_id)
    {
        $this->db->where('group_id', $group_id);
        $result = $this->db->get('optional_fields_groups_translation');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function update_data($table, $data, $condition)
    {
        foreach($condition as $key=>$val)
        {
            $this->db->where($key, $val);
        }

        return $this->db->update($table, $data);
    }

    public function count_options_products_groups($options_ids)
    {
        $this->db->where_in('field_group_id', $options_ids);

        return $this->db->count_all_results('products_optional_fields');
    }

    public function delete_group_data($ids_array)
    {
        $this->db->where_in('id', $ids_array);
        $this->db->delete('optional_fields_groups');

        $this->db->where_in('group_id', $ids_array);
        $this->db->delete('optional_fields_groups_translation');
    }

    public function get_group_row_data($id, $lang_id)
    {
        $this->db->select('optional_fields_groups.*, optional_fields_groups_translation.*');
        $this->db->join('optional_fields_groups_translation', 'optional_fields_groups.id = optional_fields_groups_translation.group_id
                            AND optional_fields_groups_translation.lang_id ='.$lang_id);

        $this->db->where('optional_fields_groups.id', $id);

        $query = $this->db->get('optional_fields_groups');

        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }

    public function update_optional_field_option($id, $option_data)
    {
        $this->db->where('id', $id);
        return $this->db->update('optional_fields_options', $option_data);
    }

    public function update_optional_field_option_translation($id, $lang_id, $data)
    {
        $this->db->where('lang_id', $lang_id);
        $this->db->where('optional_field_option_id', $id);

        return $this->db->update('optional_fields_options_translation', $data);
    }

    public function check_op_products($op_id)
    {
        $this->db->where('option_id', $op_id);

        return $this->db->count_all_results('products_optional_fields_options_costs');
    }

    public function delete_options_option($option_id)
    {
        $this->db->where('id', $option_id);
        $this->db->delete('optional_fields_options');

        $this->db->where('optional_field_option_id', $option_id);
        $this->db->delete('optional_fields_options_translation');


    }

   /****************************************************************/
}
