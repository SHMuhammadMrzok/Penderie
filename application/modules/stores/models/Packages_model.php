<?php #File Path application/modules/packages/models/Packages_model.php?>
<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Packages_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    /****************Get****************/
  
    public function get_count_all_data($lang_id, $search_word='')
    {
        $this->db->select('packages.* ,packages_translation.*');
        
        $this->db->join('packages_translation', 'packages.id = packages_translation.package_id AND packages_translation.lang_id ='.$lang_id);
        
        if(trim($search_word) !='')
        {
            $this->db->where('(packages_translation.name LIKE "%'.$search_word.'%")');
        }
        
        return $this->db->count_all_results('packages');
    }
    
    public function get_grid_data($lang_id, $limit, $offset, $search_word='', $order_by='', $order_state='desc')
    {
        $this->db->select('packages.* ,packages_translation.*');
        
        $this->db->join('packages_translation', 'packages.id = packages_translation.package_id AND packages_translation.lang_id ='.$lang_id);
        
        if(trim($search_word) !='')
        {
            $this->db->where('(packages_translation.name LIKE "%'.$search_word.'%")');
        }
        
        if($order_by != '')
        {
            if($order_by == lang('name'))
            { 
                $this->db->order_by('packages_translation.name', $order_state);
            }
            elseif($order_by == lang('products_limit'))
            {
                $this->db->order_by('packages.products_limit', $order_state);
            }
            elseif($order_by == lang('users_limit'))
            {
                $this->db->order_by('packages.users_limit', $order_state);
            }
            else
            {
                $this->db->order_by('packages.id',$order_state);
            }
        }
        else
        {
            $this->db->order_by('packages.id',$order_state);
        }
        
        $result = $this->db->get('packages',$limit,$offset);
 
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
        $this->db->select('packages.* ,packages_translation.*');
        
        $this->db->join('packages_translation', 'packages.id = packages_translation.package_id AND packages_translation.lang_id ='.$lang_id);
        
        $this->db->where('packages.id',$id);
        
        $result = $this->db->get('packages');

        if($result)
        {
            return $result->row();    
        }
        else
        {
            return false;
        }
    }
    
    public function get_packages_data($id)
    {
        $this->db->where('id', $id);
        $row = $this->db->get('packages');
        
        if($row)
        {
            return $row->row();
        }
        else
        {
            return false;
        }
    }
    
    public function get_packages_translation_result($package_id)
    {
        $this->db->where('package_id', $package_id);
        $result = $this->db->get('packages_translation');
        
        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }
    
    public function get_all_packages_data($lang_id)
    {
        $this->db->select('packages.* ,packages_translation.*');
        $this->db->join('packages_translation', 'packages.id = packages_translation.package_id AND packages_translation.lang_id ='.$lang_id);
        
        $result = $this->db->get('packages');
        
        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }
   
   /*************************DELETE*******************************/
   
    public function delete_packages_data($ids_array)
    {
        $this->db->where_in('id', $ids_array);
        $this->db->delete('packages');
        
        $this->db->where_in('package_id', $ids_array);
        $this->db->delete('packages_translation');
        
        echo '1';  
    }
    
  /*****************INSERT***************************************/
  
    public function insert_packages($data)
    {
                
        return $this->db->insert('packages', $data);
    }
    
    public function insert_packages_translation($data)
    {
                
        return $this->db->insert('packages_translation', $data);
    } 
    
   
    /***********************UPDATE*************************/
    
    public function update_packages($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('packages', $data);
    }
    
    public function update_packages_translation($id, $lang_id, $trans_data)
    {
        $this->db->where('package_id', $id);
        $this->db->where('lang_id', $lang_id);
        
        return $this->db->update('packages_translation', $trans_data);
    }  
    
   
/////////////////////////////////////////////////   
}
