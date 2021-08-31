<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Products_tags_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }


    public function get_suggestions($term)
    {
        $this->db->like('tag', $term, 'after');
        $query=$this->db->get('tags', 10);
        if($query)
        {
            return $query->result();
        }
        else
        {
            return false;
        }

    }

    public function get_products_tags_result($id)
    {
        $this->db->select('tags.*');
        $this->db->join('tags_products','tags.id = tags_products.tag_id');

        $this->db->where('tags_products.product_id',$id);
        $query = $this->db->get('tags');

        if($query)
        {
            return $query->result();
        }else{
            return false ;
        }
    }

    public function get_product_tags($product_id, $lang_id)
    {
        $this->db->select('tags.*');
        $this->db->join('tags_products','tags.id = tags_products.tag_id');

        $this->db->where('tags.lang_id', $lang_id);
        $this->db->where('tags_products.product_id', $product_id);

        $query = $this->db->get('tags');

        if($query)
        {
            return $query->result();
        }else{
            return false ;
        }
    }

    public function get_tag_id($tag,$lang_id)
    {
        $this->db->where('tag',$tag);
        $query=$this->db->get('tags');

        if($row = $query->row())
        {
            return $row->id;
        }else{
           return $this->insert_tag($tag,$lang_id);
        }
    }

    public function insert_tag($tag,$lang_id)
    {
        $data = array('lang_id'=>$lang_id,'tag'=>$tag);
        $this->db->insert('tags',$data);
        return $this->db->insert_id();
    }

    public function insert_tags_products($products_tags_data)
    {
         $this->db->insert('tags_products',$products_tags_data);
    }


}
