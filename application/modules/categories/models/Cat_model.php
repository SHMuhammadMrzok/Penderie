<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Cat_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function insert_cat_vars($data)
    {
        // update all rows sort value
        $cat_rows = $this->db->get('categories');

        if($cat_rows)
        {
            $cat_rows = $cat_rows->result();

            foreach($cat_rows as $cat_row)
            {
                $new_sort = array('sort'=>$cat_row->sort+1);
                $this->db->where('id',$cat_row->id);
                $this->db->update('categories',$new_sort);
            }
        }

        return $this->db->insert('categories',$data);
    }
    public function insert_cat_translation($cat_translation_data)
    {
        return $this->db->insert('categories_translation',$cat_translation_data);
    }
    public function get_cat_result($id)
    {
        $this->db->select('categories_translation.* , categories.*');
        $this->db->join('categories_translation','categories.id = categories_translation.category_id');
        $this->db->where('categories.id',$id);
        $query = $this->db->get('categories');

        if($query)
        {
            return $query->result();
        }
    }

    public function get_category_row($id)
    {
         $row = $this->db->where('id',$id)->get('categories');
         if($row){
            return $row->row();
         }else{
            return false;
        }
    }

    public function update_category($cat_id,$category_data)
    {
        $this->db->where('id',$cat_id);
        $this->db->update('categories',$category_data);
    }
    public function update_cat_translation($cat_id,$lang_id,$cat_translation_data)
    {
        $this->db->where('category_id',$cat_id);
        $this->db->where('lang_id',$lang_id);
        $this->db->update('categories_translation',$cat_translation_data);
    }
    public function get_parent_cats($lang_id)
    {
        $this->db->select('categories.* , categories_translation.name');
        $this->db->join('categories_translation ','categories.id = categories_translation.category_id');
        $this->db->where('categories_translation.lang_id',$lang_id);
        $this->db->where('parent_id',0);
        $this->db->order_by('id','desc');

        $query = $this->db->get('categories');

        if($query)
        {
            return $query->result();
        }
        else
        {
            return false;
        }
    }
    public function get_cat_name($cat_id, $lang_id)
    {
        $this->db->where('category_id',$cat_id);
        $this->db->where('lang_id',$lang_id);
        $query = $this->db->get('categories_translation');
        if($query)
        {
            return $query->row();
        }

    }
    public function get_cat_parent($cat_id, $lang_id)
    {
        $cat_data    = $this->get_category_row($cat_id);
        if($cat_data)
        {
            $parent_data = $this->get_cat_name($cat_data->parent_id, $lang_id);
            if($parent_data)
            {
                $parent_name = $parent_data->name;
                return $parent_name;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }
    public function update_lang_translation($lang_var_id,$lang_id,$lang_translation_data)
    {
        $this->db->where('var_id',$lang_var_id);
        $this->db->where('lang_id',$lang_id);
        $this->db->update('lang_translation',$lang_translation_data);
    }

    public function get_var_value($id)
    {
        $this->db->where('id',$id);
        $row = $this->db->get('lang_vars')->row_array();
        if($row)
        {
            return $row['lang_var'];
        }else{
            return false;
        }
    }
    public function get_categories($lang_id, $limit=0, $active=1, $conds=array())
    {

        $this->db->select('categories_translation.* , categories.*, count(products.id) as products_count');
        $this->db->join('categories_translation','categories.id = categories_translation.category_id');
        $this->db->join('products', 'categories.id = products.cat_id', 'left');

        if($active == 1)
        {
            $this->db->where('categories.active',1);
        }
        $this->db->where('categories_translation.lang_id',$lang_id);

        if(count($conds) != 0)
        {
            foreach($conds as $key=> $val)
            {
                $this->db->where($key, $val);
            }
        }

        $this->db->group_by('categories.id');
        $this->db->order_by('categories.sort', 'desc');

        if($limit != 0)
        {
            $query = $this->db->get('categories', $limit);
        }
        else
        {
            $query = $this->db->get('categories');
        }

        if($query)
        {
            return $query->result();
        }
        else
        {
            return false;
        }
    }

    public function get_cat_tags_result($id)
    {
        $this->db->select('tags.*');
        $this->db->join('tags_categories','tags.id = tags_categories.tag_id');

        $this->db->where('tags_categories.category_id',$id);
        $query = $this->db->get('tags');

        if($query)
        {
            return $query->result();
        }
    }

    public function get_count_all_categories($lang_id, $search_word='', $parent_cat_filter=0)
    {
        $this->db->join('categories_translation' ,'categories.id = categories_translation.category_id');

        if(trim($search_word) !='')
        {
            $this->db->like('categories_translation.name', $search_word, 'both');
            $this->db->or_like('categories_translation.description', $search_word, 'both');
            $this->db->or_like('categories_translation.meta_tag_description', $search_word, 'both');
        }


        if($parent_cat_filter != 0)
        {
            $this->db->where('categories.parent_id', $parent_cat_filter);
        }

        $this->db->where('categories_translation.lang_id',$lang_id);

        return $this->db->count_all_results('categories');
    }

    public function get_cateories_data($lang_id,$limit,$offset,$search_word='',$order_by='',$order_state, $parent_cat_filter=0)
    {
        $this->db->select('categories_translation.*, categories.*, categories.id as id');
        $this->db->join('categories_translation', 'categories.id = categories_translation.category_id');

        if(trim($search_word) !='')
        {
            $this->db->like('categories_translation.name', $search_word, 'both');
            $this->db->or_like('categories_translation.meta_tag_description', $search_word, 'both');
            $this->db->or_like('categories_translation.description', $search_word, 'both');
        }
        else
        {
            $this->db->where('categories_translation.lang_id',$lang_id);
        }

        if($order_by != '')
        {
            if($order_by == lang('title'))
            {
                $this->db->order_by('categories_translation.name',$order_state);
            }
            elseif($order_by == lang('description'))
            {
                $this->db->order_by('categories_translation.description',$order_state);
            }
            elseif($order_by == lang('active'))
            {
                $this->db->order_by('categories.active',$order_state);
            }
            elseif($order_by == lang('sort'))
            {
                $this->db->order_by('categories.sort',$order_state);
            }
            /*elseif($order_by == lang('products_num'))
            {
                $this->db->order_by('products.cat_products',$order_state);
            }*/
            else
            {
                $this->db->order_by('categories.id',$order_state);
            }
        }
        else
        {
            $this->db->order_by('categories.id',$order_state);
        }

        if($parent_cat_filter != 0)
        {
            $this->db->where('categories.parent_id', $parent_cat_filter);
        }

        $result = $this->db->get('categories',$limit,$offset);

        if($result)
        {
            return $result->result();
        }
    }

    public function delete_category_data($categories_id_array)
    {
        $this->db->where_in('cat_id',$categories_id_array);
        $product_count = $this->db->count_all_results('products');

        if($product_count > 0)
        {
            echo lang('error_products_category_exist');
        }
        else
        {
            $this->db->where_in('id',$categories_id_array);

            $rows_to_be_deleted = $this->db->get('categories');

            if($rows_to_be_deleted)
            {
                $rows      = $rows_to_be_deleted->result();

                foreach($rows as $row)
                {
                    $row_sort = $row->sort;

                    $this->db->where('sort >',$row_sort);
                    $higher_sort_rows = $this->db->get('categories');

                    if($higher_sort_rows)
                    {
                        $higher_sort_rows = $higher_sort_rows->result();

                        foreach($higher_sort_rows as $sort_row)
                        {
                            $new_sort = array('sort' => $sort_row->sort - 1);

                            $this->db->where('id',$sort_row->id);
                            $this->db->update('categories',$new_sort);
                        }
                    }
                }

            }

            $this->db->where_in('id',$categories_id_array);
            $this->db->delete('categories');

            $this->db->where_in('category_id',$categories_id_array);
            $this->db->delete('categories_translation');
            echo '1';
        }

    }

    public function get_row_data($id, $display_lang_id)
    {
        $this->db->select('categories_translation.*, categories.*, categories.id as id');

        $this->db->join('categories_translation', 'categories.id = categories_translation.category_id');

        $this->db->where('categories.id',$id);
        $this->db->where('categories_translation.lang_id',$display_lang_id);

        $result = $this->db->get('categories');

        if($result)
        {
            return $result->row();
        }
        else
        {
            return false;
        }
    }

    public function get_parent_data($id, $display_lang_id)
    {
        $this->db->select('categories_translation.*, categories.*, categories.id as id');

        $this->db->join('categories_translation', 'categories.parent_id = categories_translation.category_id');

        $this->db->where('categories.id',$id);
        $this->db->where('categories_translation.lang_id',$display_lang_id);

        $result = $this->db->get('categories');

        if($result)
        {
            return $result->row();
        }
        else
        {
            return false;
        }
    }

    public function get_row_meta_tag_description($cat_id, $lang_id)
    {
        $this->db->where('category_id',$cat_id);
        $this->db->where('lang_id',$lang_id);

        $query = $this->db->get('categories_translation');

        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }
    public function get_category_products($cat_id)
    {
         $this->db->join('products_countries', 'products.id=products_countries.product_id
                          AND products_countries.active=1');
         $this->db->where('products.cat_id',$cat_id);

        return $this->db->count_all_results('products');
    }

    public function update_row_sort($id,$old_index,$new_index,$sort_state)
    {
        $this->db->where('id',$id);
        $row = $this->db->get('categories');
        if($row)
        {
            $row      = $row->row();
            $row_sort = $row->sort;

            // if the row moved down && sort state = ascending
            if($old_index < $new_index && $sort_state == 'asc' )
            {
                $moved_rows = $new_index - $old_index;
                $new_sort = $row_sort + $moved_rows ;

                //update other rows sort value
                $this->db->where('sort >',$row_sort);
                $this->db->where('sort <=',$new_sort);
                $other_rows = $this->db->get('categories');

                if($other_rows)
                {
                    $other_rows = $other_rows->result();

                    foreach($other_rows as $other_row)
                    {
                        $data_array = array('sort' => ($other_row->sort - 1));

                        $this->db->where('id',$other_row->id);
                        $this->db->update('categories',$data_array);
                    }
                }

            }
            //if the row moved up && sort state = ascending
            if(($old_index > $new_index && $sort_state=='asc'))
            {
                $moved_rows = $old_index - $new_index;
                $new_sort   = $row_sort - $moved_rows ;

                //update other rows sort value
                $this->db->where('sort <'  , $row_sort);
                $this->db->where('sort >=' , $new_sort);
                $other_rows = $this->db->get('categories');

                if($other_rows)
                {
                    $other_rows = $other_rows->result();

                    foreach($other_rows as $other_row)
                    {
                        $data_array = array('sort' => ($other_row->sort + 1));

                        $this->db->where('id',$other_row->id);
                        $this->db->update('categories',$data_array);
                    }
                }
            }

            //if the row moved up && sort state = descending
            if(($old_index > $new_index && $sort_state == 'desc' ))
            {
                $moved_rows = $old_index - $new_index ;
                $new_sort = $row_sort + $moved_rows ;

                //update other rows sort value
                $this->db->where('sort >',$row_sort);
                $this->db->where('sort <=',$new_sort);
                $other_rows = $this->db->get('categories');

                if($other_rows)
                {
                    $other_rows = $other_rows->result();

                    foreach($other_rows as $other_row)
                    {
                        $data_array = array('sort' => ($other_row->sort - 1));

                        $this->db->where('id',$other_row->id);
                        $this->db->update('categories',$data_array);
                    }
                }
            }

            //if the row moved up && sort state = descending
            if($old_index < $new_index && $sort_state=='desc')
            {
                $moved_rows = $new_index - $old_index;
                $new_sort   = $row_sort - $moved_rows ;

                //update other rows sort value
                $this->db->where('sort <'  , $row_sort);
                $this->db->where('sort >=' , $new_sort);
                $other_rows = $this->db->get('categories');

                if($other_rows)
                {
                    $other_rows = $other_rows->result();

                    foreach($other_rows as $other_row)
                    {
                        $data_array = array('sort' => ($other_row->sort + 1));

                        $this->db->where('id',$other_row->id);
                        $this->db->update('categories',$data_array);
                    }
                }
            }

            // update row sort value
            $row_new_sort = array('sort' => $new_sort);

            $this->db->where('id',$id);
            $this->db->update('categories',$row_new_sort);

        }
   }

   public function get_sub_cats($cat_id, $ids_only=0)
   {
       if($ids_only == 1)
       {
         $this->db->select('categories.id');
       }
        $this->db->where('parent_id', $cat_id);
        $result = $this->db->get('categories');

        if($result)
        {
            return $result->result();
        }
        else {
          return false;
        }
       // return $this->db->count_all_results('categories');
   }

   public function get_sub_cat_products($sub_cat)
   {
        $this->db->where('cat_id', $sub_cat);
        $result = $this->db->get('products');

        if($result)
        {
            return $result->result();
        }
   }

   public function get_cat_products($ids_array)
   {
       $this->db->where_in('cat_id', $ids_array);
       return $this->db->count_all_results('products');
   }

      public function get_cat_child_cats($cat_id)
    {
        $this->db->where('parent_id', $cat_id);
        $result = $this->db->get('categories');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }

    }

    public function get_sub_cats_ids()
    {
        $this->db->where('parent_id != 0');

        $result = $this->db->get('categories');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_cat_by_route($route)
    {
        $this->db->where('route', $route);
        $query = $this->db->get('categories');

        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }

    public function get_cat_stores($lang_id, $cat_id)
    {
        $this->db->select('stores_translation.*, stores.image as store_image');

        $this->db->join('stores', 'store_categories.store_id = stores.id');
        $this->db->join('stores_translation', 'store_categories.store_id = stores_translation.store_id AND stores_translation.lang_id = '.$lang_id);

        $this->db->where('store_categories.category_id', $cat_id);

        $result = $this->db->get('store_categories');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_stores_categories($lang_id, $stores_ids)
    {
        $this->db->select('categories_translation.*, categories.*, categories.id as id');

        $this->db->join('categories', 'store_categories.category_id = categories.id');
        $this->db->join('categories_translation', 'store_categories.category_id = categories_translation.category_id AND categories_translation.lang_id = '.$lang_id);

        if(count($stores_ids) != 0)
        {
            $this->db->where_in('store_categories.store_id', $stores_ids);
        }
        $this->db->group_by('categories.id');

        $result = $this->db->get('store_categories');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }


    /****************************************************/
}
