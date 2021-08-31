<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Admin_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /***************Insert***********************/
    public function insert_settings($data)
    {
       return $this->db->insert('settings',$data);
    }

    public function insert_settings_translation($settings_translation_data)
    {
        return $this->db->insert('settings_translation',$settings_translation_data);
    }

/********************Get********************/

    public function get_count_all_settings($lang_id,$search_word='')
    {
        $this->db->join('settings_translation' ,'settings.id = settings_translation.setting_id');

        if(trim($search_word) !='')
        {
            $this->db->like('settings_translation.site_name', $search_word, 'both');
            $this->db->or_like('settings_translation.address', $search_word, 'both');
        }

        $this->db->where('settings_translation.lang_id',$lang_id);

        return $this->db->count_all_results('settings');
    }

   public function get_settings_data($lang_id,$limit,$offset,$search_word='',$order_by='',$order_state)
    {
        $this->db->select('settings_translation.*, settings.*, settings.id as id');

        $this->db->join('settings_translation', 'settings.id = settings_translation.setting_id');

        $this->db->where('settings_translation.lang_id',$lang_id);

        if(trim($search_word) !='')
        {
            $this->db->like('settings_translation.site_name', $search_word, 'both');
            $this->db->or_like('settings_translation.address', $search_word, 'both');

        }

        if($order_by != '')
        {
            if($order_by == lang('site_name'))
            {
                $this->db->order_by('settings_translation.site_name',$order_state);
            }
            else
            {
                $this->db->order_by('settings.id',$order_state);
            }
        }
        else
        {
            $this->db->order_by('settings.id',$order_state);
        }

        $result = $this->db->get('settings',$limit,$offset);

        if($result)
        {
            return $result->result();
        }
    }

    public function get_settings_row($id)
    {
        $row = $this->db->where('id',$id)->get('settings');

        if($row)
        {
           return $row->row();
        }
        else
        {
           return false;
        }
    }

    public function get_sttings_translation_result($id)
    {
         $result = $this->db->where('setting_id',$id)->get('settings_translation');
         if($result){
            return $result->result();
         }else{
            return false;
        }
    }



    public function get_row_data($id)
    {
        $active_lang_id    = $this->data['active_language']->id;

        $this->db->select('settings_translation.*, settings.*, settings.id as id, languages.name as lang_name , countries_translation.name as country_name');

        $this->db->join('settings_translation', 'settings.id = settings_translation.setting_id');
        $this->db->join('languages', 'settings.default_lang = languages.id');
        $this->db->join('countries_translation', 'settings.default_country = countries_translation.country_id');

        $this->db->where('settings.id',$id);
        $this->db->where('settings_translation.lang_id',$active_lang_id);
        $this->db->where('countries_translation.lang_id', $active_lang_id);

        $result = $this->db->get('settings');

        if($result)
        {
            return $result->row();
        }
    }

   public function get_customer_group_name($id  , $lang)
   {
        $this->db->where('customer_group_id' ,$id);
        $this->db->where('lang_id' ,$lang);

        $result = $this->db->get('customer_groups_translation');
        if($result)
        {
            return $result->row();
        }
   }

   public function get_languages()
   {
        $this->db->where('active',1);

        $result = $this->db->get('languages');

        if($result)
        {
            return $result->result();
        }
   }

   public function get_countries($lang_id)
   {
        $this->db->select('countries_translation.*, countries.*, countries.id as id, currencies_translation.name as currency');

        $this->db->join('countries_translation', 'countries.id = countries_translation.country_id');
        $this->db->join('currencies_translation', 'countries.currency_id = currencies_translation.currency_id AND currencies_translation.lang_id ='.$lang_id);

        $this->db->where('countries_translation.lang_id',$lang_id);

        $result = $this->db->get('countries');

        if($result)
        {
            return $result->result();
        }
   }

   public function get_settings($lang_id)
   {
        $this->db->select('settings_translation.*, settings.*, settings.id as id');

        $this->db->join('settings_translation', 'settings.id = settings_translation.setting_id');

        $this->db->where('settings_translation.lang_id',$lang_id);

        $result = $this->db->get('settings');

        if($result)
        {
            return $result->row();
        }
   }

   public function get_settings_general_data()
   {
        $query = $this->db->get('settings');

        if($query)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
   }

   public function get_customer_groups($lang_id)
   {
        $this->db->where('lang_id', $lang_id);
        $result = $this->db->get('customer_groups_translation');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
   }

   public function get_groups($lang_id)
   {
        $this->db->where('lang_id', $lang_id);
        $result = $this->db->get('groups_translation');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
   }

   public function get_wholesaler_groups_data($wholesaler_ids, $active_lang_id)
   {
        $this->db->select('customer_groups.*, customer_groups_translation.*');
        $this->db->join('customer_groups_translation', 'customer_groups.id = customer_groups_translation.customer_group_id AND customer_groups_translation.lang_id='.$active_lang_id);

        $this->db->where_in('customer_groups.id', $wholesaler_ids);

        $result = $this->db->get('customer_groups');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
   }

   public function get_site_name($lang_id)
   {
        $this->db->where('lang_id', $lang_id);
        $query = $this->db->get('settings_translation')->row();

        if($query)
        {
            return $query->site_name;
        }
        else
        {
            return false;
        }
   }
    /*****************Update ************************/

    public function update_settings($setting_id,$settings_data)
    {
        $this->db->where('id',$setting_id);
        $this->db->update('settings',$settings_data);
    }

    public function update_settings_translation($setting_id,$lang_id,$settings_translation_data)
    {
        $this->db->where('setting_id',$setting_id);
        $this->db->where('lang_id',$lang_id);
        $this->db->update('settings_translation',$settings_translation_data);
    }


    /***************************Delete *********************************/

    public function delete_settings_data($setting_id_array)
    {
        $this->db->where_in('id',$setting_id_array);
        $this->db->delete('settings');

        $this->db->where_in('setting_id',$setting_id_array);
        $this->db->delete('settings_translation');
        echo '1';

    }

    public function get_site_settings()
    {
      $result = $this->db->get('site_settings');

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
