<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Branches_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    /**********************INSERT*******************************/
    public function insert_branch($data)
    {
        return $this->db->insert('branches', $data);
    }
    
    public function insert_branches_translation($branch_translation_data)
    {
        return $this->db->insert('branches_translation', $branch_translation_data);
    }
   
    /**********************GET*******************************/
    public function get_branch_row($id)
    {
        $this->db->where('id',$id);
        $query = $this->db->get('branches');
        
        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }
    public function get_branch_translation_result($id)
    {
        //$this->db->select('branches_translation.*');
        //$this->db->join('branches_translation','branches.id = branches_translation.branch_id');
        
        $this->db->where('branches_translation.branch_id', $id);
        $query = $this->db->get('branches_translation');
        
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
    public function update_branch($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('branches',$data);
    }
    public function update_branches_translation($id, $lang_id, $branch_translation_data)
    {
        $this->db->where('branch_id', $id);
        $this->db->where('lang_id', $lang_id);
        return $this->db->update('branches_translation', $branch_translation_data);
    }
   
    /**********************DELETE*******************************/ 
    public function delete_branch_data($ids_array)
    {
        $this->db->where_in('id', $ids_array);
        $this->db->delete('branches');
        
        $this->db->where_in('branch_id', $ids_array);
        $this->db->delete('branches_translation');
        
        echo '1'; 
    }
    
    public function get_count_all_branches($lang_id ,$search_word ='')
    {
        $this->db->join('branches_translation' ,'branches.id = branches_translation.branch_id');
        
        if(trim($search_word) !='')
        {
            $this->db->like('branches_translation.name', $search_word, 'both');  
        }
        
        $this->db->where('branches_translation.lang_id',$lang_id);
        
        return $this->db->count_all_results('branches');
    }
    
    public function get_branches_data($lang_id, $limit=0, $offset=0, $search_word='', $order_by='', $order_state='desc')
    {
        $this->db->select('branches_translation.* , branches.* ,cities_translation.*, branches.id as id, cities_translation.name as city_name, branches_translation.name as branch_name');
        
        $this->db->join('branches_translation' ,'branches.id = branches_translation.branch_id AND branches_translation.lang_id ='. $lang_id);
        $this->db->join('cities_translation','branches.city_id = cities_translation.city_id AND cities_translation.lang_id ='. $lang_id);
        
        if(trim($search_word) !='')
        {
            $this->db->like('branches_translation.title', $search_word, 'both');  
        }
        
        if($order_by != '')
        {
            if($order_by == lang('city'))
            {
                $this->db->order_by('cities_translation.name', $order_state);
            }
            elseif($order_by == lang('branch'))
            {
                $this->db->order_by('branches_translation.name', $order_state);
            }
            else
            {
                $this->db->order_by('branches.id', $order_state);
            }
        }
        else
        {
            $this->db->order_by('branches.id', $order_state);
        }
        
        if($limit != 0)
        {
            $result = $this->db->get('branches', $limit,$offset);
        }
        else
        {
            $result = $this->db->get('branches');
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
        $this->db->select('branches_translation.* , branches.* ,cities_translation.*, branches.id as id, branches_translation.name as name, cities_translation.name as city_name');
        
        $this->db->join('branches_translation' ,'branches.id = branches_translation.branch_id');
        $this->db->join('cities_translation','branches.city_id = cities_translation.city_id');
        
        $this->db->where('branches.id',$id);
        $this->db->where('cities_translation.lang_id',$display_lang_id);
        $this->db->where('branches_translation.lang_id',$display_lang_id);
        
        $result = $this->db->get('branches');

        if($result)
        {
            return $result->row();    
        }
        else
        {
            return false;
        }
    }
    
    public function get_branch_from_lng_lat_values($lat, $lng, $lang_id)
    {
        $this->db->select('branches.*, branches_translation.*, branches.id as id, branches_translation.name as branch_name');
        
        $this->db->join('branches_translation' ,'branches.id = branches_translation.branch_id AND branches_translation.lang_id ='. $lang_id);
        
        $this->db->where('branches.lat', $lat);
        $this->db->where('branches.lng', $lng);
        $this->db->where('branches.active', 1);
        
        $result = $this->db->get('branches');
        
        if($result)
        {
            return $result->row();
        }
        else
        {
            return false;
        }
        
    }
    
    public function get_near_branches($lat, $lng, $max_distance_km)
    {
        $result = $this->db->query('SELECT id, ( 6371  * acos( cos( radians('.$lat.') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('.$lng.') ) + sin( radians('.$lat.') ) * sin( radians( lat ) ) ) ) AS distance 
        FROM branches HAVING distance < '.$max_distance_km.' ORDER BY distance asc LIMIT 0 , 10;');
        
        //$query  = 'SELECT id, ( 6371 * acos( cos( radians('.$lat.') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(-'.$lng.') ) + sin( radians('.$lat.') ) * sin( radians( lat ) ) ) ) AS distance FROM markers HAVING distance < '.$max_distance_km.' ORDER BY distance LIMIT 0 , 10;';
        //$result = $this->db->query($query);
       
        if($result)
        {
            return $result->result_array();
        }
        else
        {
            return false;
        }
    }
    
    public function all_branches()
    {
        $this->db->where('active', 1);
        
        $result = $this->db->get('branches');
        
        if($result)
        {
            return $result->result_array();
        }
        else
        {
            return false;
        }
    }
    
    public function get_all_branches($display_lang_id)
    {
        $this->db->select('branches_translation.* , branches.* , branches.id as id, branches_translation.name as name');
        
        $this->db->join('branches_translation' ,'branches.id = branches_translation.branch_id');
        //$this->db->join('cities_translation','branches.city_id = cities_translation.city_id');
        
        //$this->db->where('cities_translation.lang_id',$display_lang_id);
        $this->db->where('branches_translation.lang_id',$display_lang_id);
        
        $result = $this->db->get('branches');

        if($result)
        {
            return $result->result();    
        }
        else
        {
            return false;
        }
    }
    
    
/****************************************************************/
}