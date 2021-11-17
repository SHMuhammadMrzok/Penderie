<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
/**
 *
 */
class Api_lib
{
    public $CI ;
    public $settings;
    public $lang_id;

    public function __construct()
    {
        $this->CI = &get_instance();

        $this->CI->load->library('currency');
        $this->CI->load->library('encryption');
        //$this->CI->load->library('user_bootstrap');


        $this->CI->config->load('encryption_keys');

        $this->CI->load->model('global_model');
        $this->CI->load->model('users/user_model');
        $this->CI->load->model('currencies/currency_model');

        // Get settings table data
        $this->settings = $this->CI->global_model->get_config();
        $this->lang_id  = $this->settings->default_lang;
    }

    public function check_user_store_country_id($email, $password, $user_id, $country_id)
    {
        if($this->CI->ion_auth->login($email, $password))
        {
            $this->check_user_country_store_id($user_id, $country_id);
        }
    }

    public function get_site_settings()
    {
      $site_settings = $this->CI->global_model->get_site_settings();

      foreach($site_settings as $key=>$value)
      {
        $result[$key] = $value;
      }

      return $result;
    }

    public function get_images_path()
    {
      $result = $this->CI->global_model->get_site_settings();

      foreach($result as $row)
      {
        $site_settings[$row->field] = $row->value;
      }


      if($site_settings['images_source'] == 'amazon')
     {
       $images_path = "https://".$site_settings['amazon_s3_my_bucket'].".s3.".$site_settings['amazon_s3_region'].".amazonaws.com/".$site_settings['amazon_s3_subfolder'];
     }
     else
     {
       $images_path = base_url().'assets/uploads/';
     }

     return $images_path;


    }



   public function check_user_country_store_id($user_id, $country_id)
   {
        $user_data = $this->CI->user_model->get_row_data($user_id);

        if($country_id != $user_data->store_country_id && $country_id != 0)
        {
            $user_current_balanace  = $this->get_any_user_balance($user_id);
            $user_store_country_id  = $user_data->store_country_id;
            $new_store_country_id   = $country_id;
            if($user_current_balanace != 0)
            {
                $this->update_user_credit($user_current_balanace, $user_id, $user_store_country_id, $new_store_country_id);
            }
        }
   }

   public function update_user_credit($user_current_balanace, $user_id, $user_store_country_id, $new_store_country_id)
   {
       $new_balance_to_current_currency = $this->CI->currency->update_user_credit($user_current_balanace, $user_id, $user_store_country_id, $new_store_country_id);

       return $new_balance_to_current_currency;
   }

   public function convert_balance($current_currency_val, $new_currency_val, $amount)
   {
        if($amount == 0) return 0;

        $factor      = $new_currency_val / $current_currency_val;
        $new_balance = $amount * $factor;

        return $new_balance;
   }

   public function get_user_by_id($user_id)
   {
       $user = $this->CI->user_model->get_row_data($user_id);

       return $user;
   }

   public function get_any_user_balance($user_id)
   {
       $this->CI->load->library('encryption');

       $secret_iv        = $user_id;
       $secret_key       = $this->CI->config->item('new_encryption_key');
       $enc_user_balance = $this->get_user_by_id($user_id)->user_balance;

       $user_balance     = $this->CI->encryption->decrypt($enc_user_balance, $secret_key, $secret_iv);

       if($enc_user_balance == '')
       {
           $user_balance = 0;
       }

       return $user_balance;
   }

   public function encrypt_and_update_users_data($user_id, $field, $data)
   {
       $secret_key    = $this->CI->config->item('new_encryption_key');
       $secret_iv     = $user_id;

       $user_enc_data = $this->CI->encryption->encrypt($data, $secret_key, $secret_iv);
       $user_points_data[$field]  = $user_enc_data;

       return $this->CI->user_model->update_user_balance($user_id, $user_points_data);

   }

   public function get_user_reward_points($user_id)
   {
        $this->CI->load->library('encryption');

        $secret_iv         = $user_id;
        $secret_key        = $this->CI->config->item('new_encryption_key');
        $enc_reward_points = $this->get_user_by_id($user_id)->user_points;

        $reward_points     = $this->CI->encryption->decrypt($enc_reward_points, $secret_key, $secret_iv);

        if($enc_reward_points == '')
        {
            $reward_points = 0;
        }

        return $reward_points;
   }

   public function get_user_reward_points_value($user_id, $country_id)
   {
        $reward_points              = $this->get_user_reward_points($user_id);
        $country_reward_point_value = $this->CI->countries_model->get_reward_points($country_id);

        $user_reward_points_value   = $reward_points * $country_reward_point_value;

        return $user_reward_points_value;
   }

   public function get_image_code($image_path)
    {
        $type = pathinfo($image_path, PATHINFO_EXTENSION);
        $data = file_get_contents($image_path);
        $base64 = base64_encode($data);

        return $base64;
    }
    
   public function insert_log($user_id, $url, $api_name, $agent='', $post_data, $output)
   {
        $device_id = '';
        if($agent == 'web')
        {
            $device_id = session_id();// $this->CI->data['session_id'];
        }
        else
        {
            $device_id = $_POST['deviceId'];
        }
         
         $log_data = array(
        'user_id'       => $user_id,
        'agent'         => $agent,
        'device_id'     => $device_id,
        'url'           => $url,
        'api_name'      => $api_name,
        'post_data'     => json_encode($post_data),
        'recieved_data' => json_encode($output, JSON_UNESCAPED_UNICODE),
        'unix_time'     => time()
       );
       
       //$this->CI->general_model->insert_table_data('users_api_log', $log_data);
   }

}
