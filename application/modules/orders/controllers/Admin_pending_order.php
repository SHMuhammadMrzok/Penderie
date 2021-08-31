<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Admin_pending_order extends CI_Controller
{
    public $lang_row;
    public $settings;
    public $status = 1;
    
    public function __construct()
    {
        parent::__construct();
        
        require(APPPATH . 'includes/global_vars.php');
        
        $this->load->library('encryption');
        $this->load->library('notifications');
        //$this->load->library('admin_products_lib');
        $this->load->library('products_lib');
        
        $this->load->library('payment_gateways/payfort');
        $this->load->library('payment_gateways/paypal');
        $this->load->library('payment_gateways/cashu');
        
        $this->config->load('encryption_keys');
        
        $this->load->model('orders_model');
        $this->load->model('admin_order_model');
        $this->load->model('order_status_model');
        $this->load->model('users/user_model');
        $this->load->model('users/countries_model');
        $this->load->model('users/customer_groups_model');
        $this->load->model('notifications/templates_model');
        $this->load->model('affiliate/affiliate_log_model');
        $this->load->model('affiliate/admin_affiliate_model');
        $this->load->model('coupon_codes/coupon_codes_model');
        $this->load->model('products/invalid_serials_model');
        $this->load->model('products/products_serials_model');
        $this->load->model('payment_options/user_balance_model');
        $this->load->model('payment_options/bank_accounts_model');
        $this->load->model('payment_options/payment_methods_model');
        $this->load->model('optional_fields/optional_fields_model');
        $this->load->model('shopping_cart/user_bank_accounts_model');
        
        $this->lang_row = $this->admin_bootstrap->get_active_language_row(); 
        $this->settings = $this->global_model->get_config();
    }
    
    public function index()
    {
        $lang_id = $this->data['active_language']->id;
        
        $this->data['count_all_records'] = $this->orders_model->get_count_all_orders($lang_id);
        $this->data['data_language']     = $this->lang_model->get_active_data_languages();
        
        
        $this->data['columns']           = array(
                                                     lang('order_number')           ,													 lang('order_details')          ,
                                                     lang('username')               ,
                                                     lang('country')                ,
                                                     lang('user_previous_orders')   ,
                                                     lang('agent')                  ,
                                                     lang('status')                 ,
                                                     lang('purchased_products')     ,
                                                     lang('payment_method')         ,
                                                     lang('total')                  ,
                                                     lang('date')                   ,
                                                     
                                                     //lang('delete')
                                                );
                                                   
        $this->data['orders']            = array(   
                                                     lang('date'),
                                                     lang('username'),
                                                     lang('final_total')
                                                 );
                                                 
        
        $this->data['filters']           = array(
                                                   /*array(
                                                          'filter_title' => lang('users_filter'),
                                                          'filter_name'  => 'username_filter',
                                                          'filter_data'  => $this->user_model->get_active_users_data()
                                                         ) ,
                                                   */
                                                   array(
                                                          'filter_title' => lang('countries_filter'),
                                                          'filter_name'  => 'countries_filters',
                                                          'filter_data'  => $this->countries_model->get_countries($lang_id)
                                                         ),
                                                   /* array(
                                                          'filter_title' => lang('status_filter'),
                                                          'filter_name'  => 'status_filter',
                                                          'filter_data'  => $this->order_status_model->get_all_statuses($lang_id)
                                                         ),
                                                   */
                                                    array(
                                                          'filter_title' => lang('payment_methods_filter'),
                                                          'filter_name'  => 'payment_methods_filter',
                                                          'filter_data'  => $this->admin_order_model->get_payment_methods_translation($lang_id)
                                                         )  
                                                 );
        
        
        //$this->data['actions']           = array( 'delete'=>lang('delete'));
        $this->data['search_fields_data']     = array( lang('order_number'), lang('total'), lang('username'));
        
        $this->data['unset_actions']     = true;
        
        $this->data['content']           = $this->load->view('Admin/grid/grid_html', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
    }
    
    public function ajax_list()
    {
        if(isset($_POST['lang_id']))
        {
            $lang_id = intval($this->input->post('lang_id'));
        }else{
            $lang_id = $this->data['active_language']->id;    
        }
        if(isset($_POST['limit']))
        {
            $limit = intval($this->input->post('limit'));
        }else{
            $limit = 1;    
        }
        
        if(isset($_POST['page_number']))
        {
            $active_page = intval($this->input->post('page_number'));
        }else{
            $active_page = 1;    
        }  
        
        $offset  = ($active_page-1) * $limit;
        
        
        
        if(isset($_POST['search_word']) || trim($_POST['search_word']) == '')
        { 
            $search_word = $this->input->post('search_word');
        }
        else
        {
            $search_word = '';
        }
        
        if(isset($_POST['order_by']))
        {
            $order_by = $this->input->post('order_by');
        }
        else
        {
            $order_by = '';
        }
        
        if(isset($_POST['order_state']))
        {
            $order_state = $this->input->post('order_state');
        }
        else
        {
            $order_state = 'desc';
        }
        
        if(isset($_POST['filter'])&& isset($_POST['filter_data']))
        {
            $filters      = $this->input->post('filter');
            $filters_data = $this->input->post('filter_data');
            
            //$username_filter_id  = intval($filters_data[0]);
            $username_filter_id  = 0;
            $countries_filter_id = intval($filters_data[0]);
            $status_filter_id    = 2;//intval($filters_data[2]);
            $payment_filter_id   = intval($filters_data[1]);
        }
        else
        {
            $username_filter_id  = 0;
            $countries_filter_id = 0;     
            $status_filter_id    = 2;//0;
            $payment_filter_id   = 0;
        }
        
        if(isset($_POST['search_field_id']))
        {
            $search_field_id = $this->input->post('search_field_id');
        }
        else
        {
            $search_field_id = 0;
        }
        
        
        $grid_data  = $this->orders_model->get_orders_data($lang_id, $limit, $offset,$search_word, $order_by, $order_state, $username_filter_id, $countries_filter_id, $status_filter_id, $payment_filter_id, $search_field_id);
        
        $db_columns = array(
                             'id'               ,							 'order_details'    ,
                             'username'         ,
                             'country'          ,
                             'previous_orders'  ,
                             'agent'            ,
                             'status'           ,
                             'products'         ,
                             'payment_method'   ,
                             'final_total'      ,
                             'unix_time'        ,
                             
                             //'delete'
                           );
                       
        $this->data['hidden_fields'] = array();
        
        $new_grid_data = array();
        
        foreach($grid_data as $key =>$row)
        { 
            foreach($db_columns as $column)
            {
                if($column == 'country')
                {
                    $country = $this->countries_model->get_country_name($row->country_id, $lang_id);
                    if($row->order_status_id == 4)
                    {
                        $country = '<span style="color: #B79999;">'.$country.'</span>';
                    }
                    
                    
                    $new_grid_data[$key][$column] = $country;
                }
                else if($column == 'previous_orders')
                {
                    $user_previous_count = $this->orders_model->get_user_previous_orders_count($row->user_id, $row->id);
                    
                    if($row->order_status_id == 4)
                    {
                        $user_previous_count = '<span style="color: #B79999;">'.$user_previous_count.'</span>';
                    }
                    
                    $new_grid_data[$key][$column] = $user_previous_count;
                }
                else if($column == 'agent')
                {
                    $new_grid_data[$key][$column] = substr($row->agent, 0, 20);
                }
                else if($column == 'products')
                {  
                    $products_names     = '';
                    $products_data      = $this->orders_model->get_order_products($row->id, $lang_id);
                    $order_charge_cards = $this->orders_model->get_recharge_card($row->id);
                    
                    foreach($products_data as $item)
                    {
                        $products_names .= $item->qty." X ".$item->title." <br> ";
                    }
                    foreach($order_charge_cards as $card)
                    {
                        $products_names .= lang('recharge_card')." X ".$card->price;
                    }
                    
                    if($row->order_status_id == 4)
                    {
                        $products_names = '<span style="color: #B79999;">'.$products_names.'</span>';
                    }
                    $new_grid_data[$key][$column] = $products_names;
                  
                }
                  
                else if($column == 'payment_method')
                {
                    $payment_method_data = $this->payment_methods_model->get_row_data($row->payment_method_id, $lang_id);
                    
                    $payment_method = $payment_method_data->name;
                    
                    if($row->payment_method_id == 3)
                    {
                        $bank_data = $this->bank_accounts_model->get_row_data($row->bank_id, $lang_id);
                        $payment_method .= ': '.$bank_data->bank;
                    }
                    
                    if($row->order_status_id == 4)
                    {
                        $payment_method = '<span style="color: #B79999;">'.$payment_method.'</span>';
                    }
                    
                    $new_grid_data[$key][$column] = $payment_method . " <img height='20' src='".base_url()."assets/uploads/".$payment_method_data->image."'>";; 
                }
                else if($column == 'final_total')
                {
                    $final_total = $row->final_total." ".$row->currency_symbol;
                    if($row->order_status_id == 4)
                    {
                        $final_total = '<span style="color: #B79999;">'.$final_total.'</span>';
                    }
                    $new_grid_data[$key][$column] = $final_total ;
                }
                else if($column == 'unix_time')
                {
                    $unix_time = date('Y/m/d H:i', $row->unix_time);
                    if($row->order_status_id == 4)
                    {
                        $unix_time = '<span style="color: #B79999;">'.$unix_time.'</span>';
                    }
                    $new_grid_data[$key][$column] = $unix_time;
                }
                else if($column == 'order_details')
                {
                    $details = '<a class="btn btn-sm blue table-group-action-submit" href="'.base_url().'orders/admin_order/view_order/'.$row->id.'">'.lang('order_details').'</a>';
                    if($row->order_status_id == 4)
                    {
                        $details = '';
                    }
                    $new_grid_data[$key][$column] = $details;
                }
                /*elseif($column == 'delete')
                {
                    if($row->order_status_id == 2 || $row->order_status_id == 8)
                    {
                        $delete = '<a class="btn btn-sm red table-group-action-submit" href="'.base_url().'orders/admin_order/delete_order/'.$row->id.'">'.lang('delete').'</a>';
                    }
                    else
                    {
                        $delete = '';
                    }
                    if($row->order_status_id == 4)
                    {
                        $delete = '<span style="color: red;">'. lang('deleted_order').'</span>';
                    }
                    
                    $new_grid_data[$key][$column] = $delete;
                }*/
                elseif($column == 'username')
                {
                    $new_grid_data[$key][$column] = $row->first_name. ' ' . $row->last_name;;
                }
                else
                {
                    $field = $row->{$column};
                    //echo $row->{$column}; 
                    if($row->order_status_id == 4 )
                    {
                        if($column == 'username')
                        {
                            $field = '<span style="color: #B79999;">'.$row->first_name . ' ' . $row->last_name . '</span>';
                        }
                        elseif($column == 'status')
                        {
                            $field = '<span style="color: #B79999;">'.$row->status.'</span>';
                        }
                    }
                    $new_grid_data[$key][$column] = $field;
                }
            }
        }
        
        $this->data['grid_data']          = $new_grid_data; 
        
        $this->data['count_all_records']  = $this->orders_model->get_count_all_orders($lang_id, $limit, $offset,$search_word, $order_by, $order_state, $username_filter_id, $countries_filter_id, $status_filter_id, $payment_filter_id);
        
        $this->data['display_lang_id']    = $lang_id;
        
        $this->data['unset_view']   = true;
        $this->data['unset_edit']   = true;
        $this->data['unset_delete'] = true;
         
        
        $count_data  = $this->data['count_all_records'];
        $output_data = $this->load->view('Admin/grid/grid_data', $this->data, true);
        
        echo json_encode(array($output_data, $count_data, $search_word));
     }
     
     public function stream()
     {
        $last_row_id  = $this->input->post('last_row_id');
        $lang_id      = $this->input->post('lang_id');
        $limit        = $this->input->post('limit');
        $page_number  = $this->input->post('page_number');
        
        $condtions    = array('order_status_id'=> '2');
        
        if($page_number !=1)
        {
            $rows_count = $page_number * $limit;
            
            
            $previous_rows_count = $this->orders_model->count_previous_rows($last_row_id, $condtions);
            
            $new_rows_count = $previous_rows_count % $rows_count;
            
            if($new_rows_count != 0)
            {
                $new_row_data = $this->orders_model->get_new_row_data($last_row_id, $lang_id, $condtions);
            }
        }
        
        
        $rows_count = $page_number * $limit;
        $previous_rows_count = $this->orders_model->count_previous_rows($last_row_id, $condtions);
        
        $new_rows_count = $previous_rows_count % $rows_count;
        
        if($new_rows_count != 0)
        {   
            $new_row_data = $this->orders_model->get_new_row_data($last_row_id, $lang_id, $condtions);
            if($new_row_data)
            {
                $user_previous_count = $this->orders_model->get_user_previous_orders_count($new_row_data->user_id, $new_row_data->id);
                
                $products_names     = '';
                $products_data      = $this->orders_model->get_order_products($new_row_data->id, $lang_id);
                $order_charge_cards = $this->orders_model->get_recharge_card($new_row_data->id);
                
                foreach($products_data as $item)
                {
                    $products_names .= $item->qty." X ".$item->title." <br> ";
                }
                foreach($order_charge_cards as $card)
                {
                    $products_names .= lang('recharge_card')." X ".$card->price;
                }
                
                $payment_method = $this->payment_methods_model->get_payment_method_name($new_row_data->payment_method_id, $lang_id);
                
                $row = '<tr data-sort="" data-id="'.$new_row_data->id.' " class="sorting nodrag">
                             <td width="5%">
                        		<div class="checker"><span><input type="checkbox" class="group-checkable checkbox" name="row_id[]" value="'.$new_row_data->id.'"></span></div>
                        	</td>
                            
                            <td style="text-align: center;"><a href="'.base_url().'orders/admin_order/view_order/'.$new_row_data->id.'">'.$new_row_data->id.'</a></td>
        	                <td style="text-align: center;">'.$new_row_data->first_name.' '.$new_row_data->last_name.'</td>
                            <td style="text-align: center;">'.$new_row_data->country.'</td>
                            <td style="text-align: center;">'.$user_previous_count.'</td>
                            <td style="text-align: center;">'.substr($new_row_data->agent, 0, 20).'</td>
                            <td style="text-align: center;">'.$new_row_data->status.'</td>
                            <td style="text-align: center;">'.$products_names.'</td>
                            <td style="text-align: center;">'.$payment_method.'</td>
                            <td style="text-align: center;">'.$new_row_data->final_total.' '.$new_row_data->currency_symbol.'</td>
                            <td style="text-align: center;">'.date('Y/m/d H:i', $new_row_data->unix_time).'</td>
                            <td style="text-align: center;">
                      			<a class="btn btn-sm blue table-group-action-submit" href="'.base_url().'orders/admin_order/view_order/'.$new_row_data->id.'">'.lang('order_details').'</a>    			
                            </td>
                        	
                        </tr>';
                        
                echo $row;
            }
            else
            {
                echo '0';
            }
        }
        else
        {
            echo '0';
        }
     }
      
    
/************************************************************************/    
}