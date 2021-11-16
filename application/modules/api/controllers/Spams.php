<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Spams extends CI_Controller
{
  
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model('spams/spams_model');
        $this->load->model('products/products_model');
        $this->load->model('general_model');
        $this->load->library('api_lib');
        $this->load->library('notifications');
        
    }

    public function spams_reasons()
    {
        
        $lang_id      = intval(strip_tags($this->input->post('langId', TRUE)));
        
        // Added for api log
        $email              = strip_tags($this->input->post('email', TRUE));
        $password           = strip_tags($this->input->post('password', TRUE));  
        $agent              = strip_tags($this->input->post('agent', TRUE));
        $user_id            = 0;

        if($this->ion_auth->login($email, $password))
        {
            $user_data  = $this->ion_auth->user()->row();
            $user_id    = $user_data->id;
        }
        ///
        
        $output = array();
        $spams = $this->spams_model->get_all_spams($lang_id);
        
        if( count($spams) != 0)
        {
            foreach($spams as $row)
            {   
                $output[] = array(
                                    'id'    => $row->id,
                                    'title' => $row->name
                                    );
              }
        
        }
        else
        {
            $fail_message   = $this->general_model->get_lang_var_translation('no_data',$lang_id);
            $output         = array(
                                        'message' => $fail_message,
                                        'response' => 0
                                    );  
        }
        
        //***************LOG DATA***************//
        //insert log
        $this->api_lib->insert_log($user_id, current_url(), 'Spams - Spams reasons', $agent, $_POST, $output);
        //***************END LOG***************//

        $this->output->set_content_type('application/json')->set_output(json_encode($output));
    }
    
    public function submit_report()
    {
        $lang_id        = intval($this->input->post('langId', TRUE));
        $country_id     = intval($this->input->post('countryId', TRUE));
        $email          = strip_tags($this->input->post('email', TRUE));
        $password       = strip_tags($this->input->post('password', TRUE));
        $deviceId       = strip_tags($this->input->post('deviceId', TRUE));
        $product_id     = intval($this->input->post('productId', TRUE));
        $reason_id      = intval($this->input->post('reasonId', TRUE));
        $comment        = strip_tags($this->input->post('comment', TRUE));
        $block_user     = strip_tags($this->input->post('blockUser', TRUE));
        
        $agent              = strip_tags($this->input->post('agent', TRUE));
        $user_id            = 0;

        if($this->ion_auth->login($email, $password))
        {
            $user              = $this->ion_auth->user()->row();
            $user_id           = $user->id;
            
            //$this->api_lib->check_user_store_country_id($email, $password, $user_id, $country_id);
            
            $is_logged         = 1;
            $customer_group_id = $user->customer_group_id;
            
            $product_data = $this->products_model->get_product_row_details($product_id, $lang_id, $country_id);
            
            if(count($product_data) != 0)
            {
                $spam_data = array(
                                    'user_id'           => $user_id,
                                    'product_id'        => $product_id,
                                    'reason_id'         => $reason_id,
                                    'comment'           => $comment ,
                                    'block_user'        => $block_user,
                                    'product_owner_id'  => $product_data->owner_id,
                                    'unix_time'         => time()
                                  );
                
                $this->spams_model->insert_user_spam_data($spam_data);
                
                // check if reached to max spam number 
                $settings  = $this->general_model->get_settings();
                $max_spams = $settings->max_blocks;
                $product_spams_count = $this->products_model->get_product_blocked_count($product_id);
                
                if($product_spams_count >= $max_spams)
                {
                    // deactivate product
                    $product_updated_data = array(
                                                    'active' => 1
                                                 );
                    $this->products_model->update_product_countries($product_id, 0, $product_updated_data);
                    
                    //add max blocks event
                    $website_name_lang = $this->general_model->get_lang_var_translation('store_name', $lang_id);
                    $product_name  = $this->products_model->get_product_name($product_id, $lang_id);
                    $emails[]      = $settings->email;
                    $mobile_number = $settings->mobile;
                    
                    $template_data = array(
                                            'product_name'  => '<a href="'.base_url().'products/admin_products/edit/'.$product_id.'">'.$product_name.'</a>',
                                            'logo_path'     => base_url().'assets/uploads/'.$settings->logo,
                                            'site_name'     => $website_name_lang,
                                            'year'          => date('Y')
                                          );
                    
                    $this->notifications->create_notification('product_reached_max_blocks', $template_data, $emails, $mobile_number);
                }
                
                $message = $this->general_model->get_lang_var_translation('execution_success', $lang_id);
                $output  = array( 
                                'message' => $message,
                                'response' => 1
                           );
            }
            else
            {
                $fail_message = $this->general_model->get_lang_var_translation('no_available_products', $lang_id);
                $output = array( 
                                'message' => $fail_message,
                                'response' => 0
                           );
            }
            
        }
        else
        {
            $fail_message = $this->general_model->get_lang_var_translation('login_error', $lang_id);
             $output = array( 
                                'message' => $fail_message,
                                'response' => 0
                           );
        }

        //***************LOG DATA***************//
        //insert log
        $this->api_lib->insert_log($user_id, current_url(), 'Spams - Submit Spam report', $agent, $_POST, $output);
        //***************END LOG***************//
        
        $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));
    } 
    
    public function blocked_users()
    {
        $lang_id        = intval($this->input->post('langId', TRUE));
        $country_id     = intval($this->input->post('countryId', TRUE));
        $email          = strip_tags($this->input->post('email', TRUE));
        $password       = strip_tags($this->input->post('password', TRUE));
        $deviceId       = strip_tags($this->input->post('deviceId', TRUE));
        $page           = intval($this->input->post('page', TRUE));
        
        $agent              = strip_tags($this->input->post('agent', TRUE));
        $user_id            = 0;

        if($this->ion_auth->login($email, $password))
        {
            $user     = $this->ion_auth->user()->row();
            $user_id  = $user->id;
            
            if(!$page) $page = 1;
            $limit  = 10;
            $offset = ($page -1)*$limit;
            
            
            $user_blocked_users = $this->spams_model->get_user_blocked_users($user_id, $limit, $offset);
            
            if(count($user_blocked_users) != 0)
            {
                foreach($user_blocked_users as $row)
                {
                    $output[] = array(
                                        'blockedUserId' => $row->blocked_user_id, 
                                        'blockedUserEmail' => $row->email ,
                                        'blockedUsername' => $row->first_name.' '.$row->last_name
                                     );
                    
                     
                }
            }
            else
            {
                $fail_message = $this->general_model->get_lang_var_translation('no_data', $lang_id);
                $output = array( 
                                'message' => $fail_message,
                                'response' => 0
                                );
            }
            
        }
        else
        {
            $fail_message = $this->general_model->get_lang_var_translation('login_error', $lang_id);
             $output = array( 
                                'message' => $fail_message,
                                'response' => 0
                           );
        }
        

        //***************LOG DATA***************//
        //insert log
        $this->api_lib->insert_log($user_id, current_url(), 'Spams - Blocked users', $agent, $_POST, $output);
        //***************END LOG***************//

        $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));
    }
    
    public function unblock_user()
    {
        $lang_id        = intval($this->input->post('langId', TRUE));
        $unblocked_user = intval($this->input->post('unblockedUserId', TRUE));
        $email          = strip_tags($this->input->post('email', TRUE));
        $password       = strip_tags($this->input->post('password', TRUE));
        $deviceId       = strip_tags($this->input->post('deviceId', TRUE));
        
        $agent              = strip_tags($this->input->post('agent', TRUE));
        $user_id            = 0;

        if($this->ion_auth->login($email, $password))
        {
            $user     = $this->ion_auth->user()->row();
            $user_id  = $user->id;
            
            //remove from blocked users
            $unblock_data = array(
                                    'block_user' => 0,
                                 );
            $conditions_array = array(
                                        'user_id' => $user_id,
                                        'product_owner_id' => $unblocked_user
                                     );
            
            $this->spams_model->update_user_spams($unblock_data, $conditions_array);
            
             $message = $this->general_model->get_lang_var_translation('execution_success', $lang_id);
             $output = array( 
                                'message' => $message,
                                'response' => 1
                           );
            
        }
        else
        {
            $fail_message = $this->general_model->get_lang_var_translation('login_error', $lang_id);
             $output = array( 
                                'message' => $fail_message,
                                'response' => 0
                           );
        }
        
        //***************LOG DATA***************//
        //insert log
        $this->api_lib->insert_log($user_id, current_url(), 'Spams - UnBlock user', $agent, $_POST, $output);
        //***************END LOG***************//

        $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));
        
    }
    
    
       
     
/************************************************************************/    
}