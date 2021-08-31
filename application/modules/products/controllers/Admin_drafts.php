<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Admin_drafts extends CI_Controller
{
    public $lang_row ;
    public $stores;
    public $stores_ids;
    
    public function __construct()
    {
        parent::__construct();
        
        require(APPPATH . 'includes/global_vars.php');
        $this->load->model('purchase_orders_model');
        $this->load->model('vendors/vendors_model');
        $this->load->model('categories/cat_model');
        
        $this->lang_row = $this->admin_bootstrap->get_active_language_row();
        
        /*************************************************************************/
        $this->stores   = $this->admin_bootstrap->get_user_available_stores();
        
        $store_id_array = array();
        
        foreach($this->stores as $store)
        {
            $store_id_array[] = $store->store_id;
        }
        
        $this->stores_ids = $store_id_array;
        /*************************************************************************/
    }

    private function _js_and_css_files()
    {
        $this->data['css_files'] = array();
        
        $this->data['js_files']  = array(
                                            //confirmation
                                            'global/plugins/bootstrap-confirmation/bootstrap-confirmation.min.js',
                                            'pages/scripts/ui-confirmations.js',
                                            //TouchSpin
                                            'global/plugins/fuelux/js/spinner.min.js',
                                            'global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js',
                                            'global/plugins/bootstrap-touchspin/bootstrap.touchspin.js',
                                            
                                        );                                                                                                   
    }
    
    public function index()
    {
        $lang_id = $this->data['active_language']->id;
        
        $this->data['count_all_records'] = $this->purchase_orders_model->get_count_all_drafts('', $this->stores_ids);
        $this->data['data_language']     = $this->lang_model->get_active_data_languages();
        
        $this->data['columns']           = array(
                                                   lang('order_number'),
                                                   lang('purchase_order_number'),
                                                   lang('products_number'),
                                                   lang('vendor_id'),
                                                   lang('unix_time'),
                                                );
            
        $this->data['actions']          = array( 'delete'=>lang('delete'));
        $this->data['index_method_id']  = $this->data['method_id'];
        
        $this->data['content']  = $this->load->view('Admin/grid/grid_html', $this->data, true);
        $this->load->view('Admin/main_frame',$this->data);
    }
  
    public function ajax_list()
    {
        /**************************************/
        $this->stores   = $this->admin_bootstrap->get_user_available_stores($_POST['index_method_id']);
        $store_id_array = array();
        
        foreach($this->stores as $store)
        {
            $store_id_array[] = $store->store_id;
        }
        
        $this->stores_ids = $store_id_array;
        /**************************************/
        
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
        
        $grid_data       = $this->purchase_orders_model->get_drafts_data($lang_id, $limit, $offset, $search_word, $this->stores_ids);
        
        $db_columns      = array(
                                 'id',   
                                 'order_number',
                                 'products_number',
                                 'title' ,
                                 'unix_time',
                          );
                       
       $this->data['hidden_fields'] = array();
                                           
       $new_grid_data = array();
        
       foreach($grid_data as $key =>$row)
       { 
            $products_number = 0 ;
            $product_num     = 0;
            $products_number = $this->purchase_orders_model->get_purchase_order_products($row->id);
            $product_num     = count($products_number);
            
            foreach($db_columns as $column)
            {
                if($column == 'products_number'){
                    
                    $new_grid_data[$key][$column] = $product_num;
                
                }
                elseif($column == 'unix_time')
                {
                    $new_grid_data[$key][$column] = date('Y-m-d H:i', $row->unix_time);
                }
                else
                {
                    $new_grid_data[$key][$column] = $row->{$column};
                }
            }
        }
        
        
        $this->data['grid_data']            = $new_grid_data;
        $this->data['unset_view']           = true;
        $this->data['count_all_records']    = $this->purchase_orders_model->get_count_all_drafts($search_word, $this->stores_ids);
        
        $output_data = $this->load->view('Admin/grid/grid_data',$this->data, true);
        $count_data  = $this->data['count_all_records'];
       
        echo json_encode(array($output_data, $count_data, $search_word));
    }      
  
     public function edit($id)
    {
        if(is_numeric($id))
        {
            $id = intval($id);
            
            $validation_msg = false;
            
            $purchase_orders_data  = $this->purchase_orders_model->get_purchase_orders_row($id);
            $order_number          =  $this->input->post('order_number');
            

            if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
            {
                if($purchase_orders_data->order_number != $order_number)
                {
                    $this->form_validation->set_rules('order_number' , lang('order_number') , 'required|callback_unique_per_vendor');
                }
                
                $this->form_validation->set_rules('vendor_id' , lang('vendor_id') , 'required');
                
                
                $this->form_validation->set_message('required', lang('required'));
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            }
            
            $default_currency_data = $this->currency_model->get_default_currency_data();
            
            if($this->form_validation->run() == FALSE)
            {
    		   $this->_edit_form($id, $validation_msg, $default_currency_data->currency_symbol);
            }
            else
            {
                
                $purchase_orders_products_data = $this->purchase_orders_model->get_purchase_orders_products_result($id,$this->lang_row->id);
                $draft ='';
            
                if (isset($_POST['draft']) && $_POST['draft'] == 1) 
                {
                    $draft = 1;
                }
                else
                {
                    $draft = 0;
                }
                
                
                $vendor_id    = $this->input->post('vendor_id');
                
                $purchase_orders_data  = array(
                                                'order_number'  => $order_number ,
                                                'vendor_id'     => $vendor_id ,
                                                'currency_id'   => $default_currency_data->id,
                                                'unix_time'     => time() ,
                                                'draft'         => $draft ,
                                              );
                
                $this->purchase_orders_model->update_purchase_orders($id,$purchase_orders_data);    
                
                /**********************purchase_orders_products data****************************/
                //-->>> Delete Old Data
                
                $this->purchase_orders_model->delete_purchase_orders_old_products_data($id);
                
                $product_ids    = $this->input->post('product_id');
                $quantity       = $this->input->post('quantity');
                $price_per_unit = $this->input->post('price_per_unit');
                $country_id     = $this->input->post('country_id');
                $serials_count  = 0;
                 
                foreach($product_ids as $key => $product_id)
                {
                    $purchase_orders_products_data = array(
                                                        'purchase_order_id'   => $id ,
                                                        'product_id'          => $product_id ,
                                                        'quantity'            => $quantity[$key],
                                                        'price_per_unit'      => $price_per_unit[$key],
                                                        'country_id'          => $country_id[$key],
                                                     );
               
                    $this->purchase_orders_model->insert_purchase_orders_products($purchase_orders_products_data);

                    //-->>> GET Product_country count from purchase_orders_products  table

                    $products_per_country = $this->purchase_orders_model->get_products_country_data($product_id, $country_id[$key]);

                    $total_product_price_per_country = 0;

                    foreach($products_per_country as $product)
                    {
                        $total_product_price_per_country += $product->price_per_unit;
                    }

                    $product_average_cost_in_each_country = round ($total_product_price_per_country / count($products_per_country),2);

                    //-->>> save product_average_cost_in_each_country value in products_countries table

                    $data= array('average_cost'=>$product_average_cost_in_each_country);

                    $this->purchase_orders_model->update_products_country_average_cost($product_id, $country_id[$key], $data);

                    $serials_count += $quantity[$key];
                }

                $purchase_order_updated_data['serials_count'] = $serials_count;
                $this->purchase_orders_model->update_purchase_orders($id, $purchase_order_updated_data);

                $_SESSION['success'] = lang('success');
                $this->session->mark_as_flash('success');

                redirect('products/admin_drafts/','refresh');
            }
        }
    }
     
    private function _edit_form($id, $validation_msg)
    {
        $this->_js_and_css_files();
        
        $this->data['mode']               = 'edit';
        $this->data['form_action']        = $this->data['module'] . "/" . $this->data['controller'] . "/edit/".$id;
        $this->data['id']                 = $id;
        
        $purchase_orders_data             = $this->purchase_orders_model->get_purchase_orders_row($id);
        
        if(in_array($purchase_orders_data->store_id, $this->stores_ids))
        {
            $purchase_orders_products_data    = $this->purchase_orders_model->get_purchase_orders_products_result($id,$this->lang_row->id);
            
            $vendors                          = $this->vendors_model->get_vendors($this->lang_row->id);
            $categories                       = $this->cat_model->get_categories($this->lang_row->id);
           
            
            $vendors_array      = array();
            $products_options   = array();
            $countries_options  = array();
            $cats_array         = array();
            
            $vendors_array[0]   = lang('choose');
            $cats_array[0]      = lang('choose');
            
            foreach($vendors as $row)
            {
                $vendors_array[$row->id] = $row->title;
            }
            
            foreach($categories as $cat)
            {
                if($cat->parent_id == 0)
                {
                    foreach($categories as $category)
                    {
                        if($category->parent_id == $cat->id)
                        {
                            $cats_array["{$cat->name}"][$category->id] = $category->name;
                        }
                    }
                }
            }
            
            if($validation_msg)
            {
                $this->data['validation_msg'] = $validation_msg;
            }
            
            $this->data['purchase_orders_data']          = $purchase_orders_data;
            $this->data['purchase_orders_products_data'] = $purchase_orders_products_data;
            $this->data['cats_array']                    = $cats_array;
            $this->data['vendors_array']                 = $vendors_array;
            $this->data['products_options']              = $products_options;
            $this->data['countries_options']             = $countries_options;
            //$this->data['currency_symbol']               = $currency_symbol;
        }
        else
        {
            $this->data['error_msg'] = lang('no_store_permission');
        }
        $this->data['content']      = $this->load->view('draft_orders', $this->data, true);
        
        $this->load->view('Admin/main_frame', $this->data);
        
    }
  
    public function get_products($cat_id)
    {
        $cat_id   = intval($cat_id);
        $products = $this->purchase_orders_model->get_category_products($cat_id, $this->lang_row->id);
        
        $options    = "<option>".lang('choose')."</option>";
       
        foreach($products as $row)
        {
             $options .= "<option value=$row->id>$row->title</option>";
        }
        
        echo $options;
    } 
    
    public function get_product_details($product_id)
    {
       $product_id = intval($product_id);
       $countries  = $this->purchase_orders_model->get_product_countries($product_id,$this->lang_row->id);
       
       $options    = '';
       
       foreach($countries as $row)
       {
           $options .= "<option value=$row->country_id>$row->name</option>";
        }
        
        echo $options;
    }
    
    public function get_vendor_currency($vendor_id)
    {
        $vendor_currency = $this->purchase_orders_model->get_vendor_currency($vendor_id,$this->lang_row->id);
        
        echo "<input type='text' name='vendor_currency' value=".$vendor_currency->currency." class='form-control' id='vendor_currency' readonly='true' />";
    }
   
    public function do_action()
    {
        $action = $this->input->post('action');
        if($action == 'delete')
        {
            $this->delete();
        }
    }
   
   public function delete()
   {
       $order_ids = $this->input->post('row_id');
       
       if(is_array($order_ids))
       {   
           $ids_array = array();
            
           foreach($order_ids as $order_id)
           {
               $ids_array[] = $order_id['value'];
           }
       }
       else
       {
           $ids_array = array($order_ids);
       }
            
       $this->purchase_orders_model->delete_purchase_orders_data($ids_array);
        
       echo 1; 
   }
   
   public function unique_per_vendor($order_number) 
   {
        $vendor_id = $this->input->post('vendor_id');
        $is_exist_order_number = $this->purchase_orders_model->check_order_number_with_vendor($order_number, $vendor_id);
        
        if($is_exist_order_number) 
        {
            $this->form_validation->set_message('unique_per_vendor', lang('order_number_is_unique_per_vendor'));
            return FALSE;
        }
        else
        {
            return TRUE;
        }
   }
   
   
/************************************************************************/    
}