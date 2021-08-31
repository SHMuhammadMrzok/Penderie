<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Reward_points_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    /********************Insert *****************************/
    public function insert_reward_points_data($reward_points_data)
    {
        return $this->db->insert('reward_points', $reward_points_data);
    }
   /**********************GET*******************************/
    
    public function get_count_all_points()
    {
        return $this->db->count_all_results('reward_points');
    }
   
   public function get_reward_points_data($limit,$offset,$search_word='',$order_by='',$order_state)
    {
       
        if(trim($search_word) !='')
        {
            $this->db->like('points', $search_word, 'both'); 
        }
        if($order_by != '')
        {
            if($order_by == lang('points'))
            { 
                $this->db->order_by('points',$order_state);
            }
            
            elseif($order_by == lang('price'))
            {
                $this->db->order_by('price',$order_state);
            }
            elseif($order_by == lang('active'))
            {
                $this->db->order_by('active',$order_state);
            }
            else
            {
                $this->db->order_by('id',$order_state);
            }
        }
        else
        {
            $this->db->order_by('id',$order_state);
        }
        $result = $this->db->get('reward_points',$limit,$offset);

        if($result)
        {
            return $result->result();    
        }
    }
    
    public function get_row_data($id,$display_lang_id)
    {
       $this->db->where('id',$id);
         
        $result = $this->db->get('reward_points');

        if($result)
        {
            return $result->row();    
        }
    }
  
   
    /**********************Update*******************************/
    public function update_reward_points_data($id,$data)
    {
        $this->db->where('id',$id);
        return $this->db->update('reward_points',$data);
    }
    
    /**********************DELETE*******************************/ 
   
    public function delete_reward_points_data($reward_points_id_array)
    {
        $this->db->where_in('id',$reward_points_id_array);
        $this->db->delete('reward_points');
        
    }
    
   
    
/****************************************************************/
}