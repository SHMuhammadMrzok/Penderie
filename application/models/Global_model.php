<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Global_model extends CI_Model
{

    /*********************Language And countries********************************/
    public function get_languages()
    {
        $this->db->where('active',1);

        $result = $this->db->get('languages');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_countries($lang_id)
    {
        $this->db->select('countries.*, countries.id as id, countries_translation.name, countries_translation.currency');
        $this->db->join('countries_translation', 'countries_translation.country_id = countries.id');

        $this->db->where('countries_translation.lang_id',$lang_id);

        $result = $this->db->get('countries');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

    public function get_active_country($lang_id, $country_id)
    {
        $this->db->select('countries.*, countries.id as id, countries_translation.name, countries_translation.currency');
        $this->db->join('countries_translation', 'countries_translation.country_id = countries.id');

        $this->db->where('countries.id', $country_id);
        $this->db->where('countries_translation.lang_id', $lang_id);

        $result = $this->db->get('countries');

        if($result)
        {
            return $result->row();
        }
        else
        {
            return false;
        }
    }

    public function get_countries_structure()
    {
        $result = $this->db->get('countries');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
    }

  public function get_module_id($module = '')
  {
        $this->db->where('module',$module);

        $result = $this->db->get('modules');

        if($result)
        {
            return @$result->row()->id;
        }
        else
        {
            return false;
        }
  }

  public function get_controller_id($controller = '')
  {
        $this->db->where('controller',$controller);

        $result = $this->db->get('controllers');

        if($result)
        {
            return @$result->row()->id;
        }
        else
        {
            return false;
        }
  }

  public function get_config()
  {
        return $this->db->get('settings')->row();
  }

  public function get_site_settings()
  {
        return $this->db->get('site_settings')->result();
  }

  public function payment_images()
  {
        $this->db->select('image');
        $this->db->where('active', 1);
        
        $this->db->order_by('payment_methods.sort','asc');
        
        $result = $this->db->get('payment_methods');

        if($result)
        {
            return $result->result();
        }
        else
        {
            return false;
        }
  }

  public function banks_images()
  {
       $this->db->select('image');
        $result = $this->db->get('bank_accounts');

        if($result)
        {
            return @$result->result();
        }
        else
        {
            return false;
        }
  }

  public function get_lang_row($lang_id)
  {
    $this->db->where('id', $lang_id);
    $query = $this->db->get('languages');

    if($query)
    {
        return $query->row();
    }
    else
    {
        return false;
    }
  }

   public function get_language_row_by_lang($lang)
   {
       $query = $this->db->get_where('languages', array('language' => $lang));

       if($query)
       {
           return $query->row();
       }
       else
       {
           return false;
       }
   }

   public function get_site_business_type()
   {
     $this->db->where('field', 'business_type');

     $query = $this->db->get('site_settings');

     if($query->row())
     {
       return $query->row()->value;
     }
     else
     {
       return false;
     }

   }

   public function get_table_data($table_name, $conds, $type='row')
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
////////////////////////////
}
