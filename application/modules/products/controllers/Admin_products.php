<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Admin_products extends CI_Controller
{
    public $stores;
    public $stores_ids;

    public function __construct()
    {
        parent::__construct();

        require(APPPATH . 'includes/global_vars.php');

        $this->load->library('currency');
        $this->load->library('uploaded_images');
        $this->load->library('amazon_s3_uploads');

        $this->load->model('products_model');
        $this->load->model('vats_model');
        $this->load->model('brands/brands_model');
        $this->load->model('stores/stores_model');
        $this->load->model('categories/cat_model');
        $this->load->model('products_tags_model');
        $this->load->model('products_serials_model');
        $this->load->model('purchase_orders_model');
        $this->load->model('users/countries_model');
        $this->load->model('users/customer_groups_model');
        $this->load->model('optional_fields/optional_fields_model');


        $this->lang_row = $this->admin_bootstrap->get_active_language_row();
        $this->stores   = $this->admin_bootstrap->get_user_available_stores();

        $store_id_array = array();

        foreach($this->stores as $store)
        {
            $store_id_array[] = $store->store_id;
        }

        $this->stores_ids = $store_id_array;

        $this->view_folder = 'Admin';

        if($this->data['store_owner'] == 1)
        {
            $this->view_folder = 'Sell';
        }
        
        
        
    }


    private function _js_and_css_files()
    {
        $this->data['css_files'] = array(
            'global/plugins/jquery-tags-input/jquery.tagsinput.css',
            'global/plugins/bootstrap-summernote/summernote.css'
        );

        $this->data['js_files']  = array(

            //Date Range Picker
            'global/plugins/bootstrap-daterangepicker/moment.min.js',

            //TouchSpin
            'global/plugins/fuelux/js/spinner.min.js',
            'global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js',
            'global/plugins/bootstrap-touchspin/bootstrap.touchspin.js',

            //Tags
            'tags/tag-it.js',

            //editor
            'global/plugins/bootstrap-summernote/summernote.min.js',
            'pages/scripts/components-editors.js'

        );


        $this->data['js_code'] = "ComponentsEditors.init();";
    }


    public function seller_all_products()
    {
        $this->index(1);
    }

    public function index($seller_all_products=0)
    {
        $index_method_id = $this->data['method_id'];
        $lang_id         = $this->data['active_language']->id;

        if($seller_all_products == 1)
        {
          $this->stores_ids = array();
        }

        $this->data['count_all_records']  = $this->products_model->get_count_all_products($lang_id, $this->stores_ids);
        $this->data['data_language']      = $this->lang_model->get_active_data_languages();

        if($this->stores_ids != 0)
        {
            $categories_filter = $this->cat_model->get_stores_categories($lang_id, $this->stores_ids);
        }
        else
        {
            $categories_filter  = $this->cat_model->get_categories($lang_id, 0, 0);
        }

        $options = '';

        foreach($categories_filter as $category)
        {
            if($category->parent_id == 0)
            {
                $options .= '<optgroup label="'.$category->name.'">';

                foreach($categories_filter as $cat)
                {
                    if($cat->parent_id == $category->id)
                    {
                        $options .= '<option value="'.$cat->id.'">'.$cat->name.'</option>';
                    }
                }

                $options .= '</optgroup>';
            }
        }

        $this->data['filters']            = array(
                                                    array(
                                                            'filter_title'    => lang('categories_filter'),
                                                            'filter_name'     => 'cat_id',
                                                            'custom_filter'   => $options
                                                        ),
                                                    /*array(
                                                            'filter_title'    => lang('name_of_store')  ,
                                                            'filter_name'     => 'store_id'             ,
                                                            'filter_data'     => $this->stores
                                                    )*/
                                                 );
      if($this->config->item('business_type') == 'b2b')
      {
        $this->data['filters'][] = array(
                              'filter_title'    => lang('name_of_store')  ,
                              'filter_name'     => 'store_id'             ,
                              'filter_data'     => $this->stores
                            );
      }


        $columns_array            = array(
                                                     lang('product_name')           ,
                                                     lang('category')               ,
                                                     //lang('name_of_store')          ,
                                                     lang('code')                   ,
                                                     //lang('purchase_orders')        ,
                                                     //lang('purchase_orders_drafts') ,
                                                     //lang('product_serials_count')  ,
                                                     lang('thumbnail')              ,
                                                     //lang('discount')               ,
                                                     //lang('comments')
                                               );

       if($this->config->item('business_type') == 'b2b')
       {
         $columns_array[] = lang('name_of_store');
       }

        $columns_array2 = array();
        if($seller_all_products == 0)
        {
          $columns_array2 = array(
                                    lang('discount')               ,
                                    lang('comments')
                                  );
        }

        $this->data['columns'] = array_merge($columns_array, $columns_array2);

        $this->data['orders']         = array(
                                                   lang('product_name') ,
                                                   lang('category')     ,
                                                   lang('serials_count'),
                                                   lang('code')         ,
                                                   lang('sort')
                                               );
        if($seller_all_products == 0)
        {
          $this->data['actions']      = array( 'delete'=>lang('delete'));
        }

        $this->data['search_fields']   = array( lang('product_name'), lang('code'));

        $this->data['index_method_id'] = $index_method_id;
        $this->data['seller_all_products'] = $seller_all_products;

        $this->data['content']  = $this->load->view($this->view_folder.'/grid/grid_html', $this->data, true);
        $this->load->view($this->view_folder.'/main_frame',$this->data);
    }

    public function ajax_list()
    {
        $seller_all_products = isset($_POST['seller_all_products'])?$_POST['seller_all_products']:0;
        /**************************************/
        if($_POST['seller_all_products'] == 1)
        {
          $this->stores == array();
        }
        else {
          $this->stores   = $this->admin_bootstrap->get_user_available_stores($_POST['index_method_id']);
          $store_id_array = array();

          if($this->config->item('business_type') == 'b2b')
          {
              foreach($this->stores as $store)
                {
                 $store_id_array[] = $store->store_id;
                }
            }

          $this->stores_ids = $store_id_array;
        }
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

        if(isset($_POST['filter'])&& isset($_POST['filter_data']))
        {
            $filters = $this->input->post('filter');
            $filters_data = $this->input->post('filter_data');

            $category_id = intval($filters_data[0]);

            if(isset($filters_data[1]))
            {
              $store_id    = intval($filters_data[1]);
            }
            else {
              $store_id = 0;
            }
        }
        else
        {
            $category_id = 0;
            $store_id    = 0;
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

        $grid_data  = $this->products_model->get_products_data($lang_id, $limit, $offset, $search_word, $order_by, $order_state, $category_id, $store_id, $this->stores_ids);

        $db_columns1 = array(
                             'id'               ,
                             'title'            ,
                             'category'         ,
                             //'store_name'       ,
                             'code'             ,
                             //'purchase'         ,
                             //'purchase_drafts'  ,
                             //'product_serials'  ,
                             'image'            ,
                             'sort'
                           );

       if($this->config->item('business_type') == 'b2b')
       {
         $db_columns1[] = 'store_name';

       }


        $db_columns2 = array();

        if($seller_all_products == 0)
        {
          $db_columns2 = array(
                                  'discount',
                                  'comments',
                                  //'purchase'         ,
                                  //'purchase_drafts'  ,
                                  //'product_serials'
                                );
        }

        $db_columns = array_merge($db_columns1, $db_columns2);

        $this->data['hidden_fields'] = array('id', 'sort');

        $new_grid_data = array();

        foreach($grid_data as $key =>$row)
        {
            $product_amount          = 0;
            $product_purchase_orders = 0;
            $product_serials         = 0;

            $product_orders   = $this->products_model->get_product_purchase_orders_count($row->id);
            $drafts           = $this->products_model->get_product_purchase_orders_count($row->id, 1);
            $product_serials  = $this->products_model->get_available_product_serials_count($row->id);


             foreach($db_columns as $column)
            {
                if($column == 'image')
                {
                    if($row->{$column} != '')
                    {
                        $new_grid_data[$key][$column] = "<a href='".$this->data['images_path'].$row->image."' class='image-thumbnail'><img src='".$this->data['images_path'].$row->image."' width='80' height='50' /></a>";
                    }
                    else
                    {
                        $new_grid_data[$key][$column] = '';
                    }
                }
                elseif($column == 'discount')
                {
                   $new_grid_data[$key][$column] = '<a href="'.base_url().'products/admin_products_discounts/add_form/'.$row->id.'"><img src="'.base_url().'assets/template/admin/img/add_discount.png" title="'.lang('add_discount').'" /></a>';

                }elseif($column == 'purchase'){

                    $new_grid_data[$key][$column] = $product_orders;

                }
                elseif($column == 'purchase_drafts'){

                    $new_grid_data[$key][$column] = $drafts;

                }
                elseif($column == 'product_serials'){

                    $new_grid_data[$key][$column] = $product_serials;

                }
                elseif($column == 'comments'){

                    $new_grid_data[$key][$column] = '<a href="'.base_url().'products/products_comments/index/'.$row->id.'"><img src="'.base_url().'assets/template/admin/img/comments.png" title="'.lang('comments').'" /></a>';

                }
                else{
                    $new_grid_data[$key][$column] = $row->{$column};
                }

            }
        }

        if($seller_all_products == 1)
        {
          $this->data['unset_edit'] = true;
          $this->data['unset_delete'] = true;
        }
        $this->data['seller_all_products'] = $seller_all_products;
        $this->data['grid_data']         = $new_grid_data;
        $this->data['count_all_records'] = $this->products_model->get_count_all_products($lang_id, $this->stores_ids, $search_word, $category_id, $store_id);
        $this->data['display_lang_id']   = $lang_id;

        $output_data = $this->load->view($this->view_folder.'/grid/grid_data',$this->data, true);
        $count_data  = $this->data['count_all_records'];

        echo json_encode(array($output_data, $count_data, $search_word,$category_id));
    }

    public function read($id, $display_lang_id, $seller_read=0)
    {
        $id              = intval($id);
        $display_lang_id = intval($display_lang_id);

        if($id && $display_lang_id)
        {
            $image = '';

            $products_countries_array   = array();
            //$product_stock_new_array    = array();

            $data = $this->products_model->get_row_data($id,$display_lang_id);

            if(count((array)$data) != 0)
            {
                if(in_array($data->store_id, $this->stores_ids) || $seller_read=1)
                {
                    $products_countries         = $this->products_model->get_products_countries_data($id,$display_lang_id);
                    $products_customer_groups   = $this->products_model->get_products_customer_groups_data($id,$display_lang_id);
                    $product_discount           = $this->products_model->get_product_discount($id ,$display_lang_id);
                    $product_cat_specs          = $this->products_model->get_product_cat_specs_data($id ,$display_lang_id);
                    $product_optional_fields    = $this->products_model->get_product_optional_fields($id, $display_lang_id);

                    if($data->quantity_per_serial == 1)
                    {
                        $quantity_per_serial_option = false;
                    }
                    else
                    {
                        $quantity_per_serial_option = true;
                    }

                    foreach($products_countries as $product)
                    {
                        $product_orders              = $this->purchase_orders_model->get_product_purchase_orders($product->product_id, $product->country_id);
                        $avg_cost                    = $this->purchase_orders_model->get_product_avg_cost_per_country($product->product_id, $product->country_id);
                        $active_product_quantity     = $this->products_serials_model->get_country_product_serials_count($product->product_id, $product->country_id, 1);
                        $not_active_product_quantity = $this->products_serials_model->get_country_product_serials_count($product->product_id, $product->country_id, 0);

                        $active_product_quantity     = '<span class="badge badge-success">'.lang('active').'</span> ='.$active_product_quantity;
                        $not_active_product_quantity = '<span class="badge badge-danger">'.lang('not_active').'</span> = '. $not_active_product_quantity;

                        $product->{'avg_cost'} = $avg_cost;
                        $product->{'active_product_quantity'}     = $active_product_quantity;
                        $product->{'not_active_product_quantity'} = $not_active_product_quantity;

                        $products_countries_array[] = $product;
                    }


                    if($data)
                    {
                        if($data->quantity_per_serial == 1)
                        {
                            $quantity_per_serial = '<span class="badge badge-success">'.lang('yes').'</span>';
                        }
                        elseif($data->quantity_per_serial == 0)
                        {
                            $quantity_per_serial = '<span class="badge badge-danger">'.lang('no').'</span>';
                            $cost = $data->cost;

                        }

                        if($data->serials_per_country == 1)
                        {
                            $serials_per_country = '<span class="badge badge-success">'.lang('yes').'</span>';

                            $serials_per_country_option = true;
                        }
                        elseif($data->serials_per_country == 0)
                        {
                            $serials_per_country = '<span class="badge badge-danger">'.lang('no').'</span>';
                            $serials_per_country_option = false;
                        }

                        if($data->is_used == 1)
                        {
                            $used_status = '<span class="badge badge-danger">'.lang('yes').'</span>';
                        }
                        elseif($data->is_used == 0)
                        {
                            $used_status = '<span class="badge badge-success">'.lang('no').'</span>';
                        }

                        if($data->image != '')
                        {
                            $image = '<a class="image-thumbnail" href="'.$this->data['images_path'].$data->image.'"><img src="'.$this->data['images_path'].$data->image.'" width="120" height="70"></a>' ;
                        }

                        $row_data = array(
                                        lang('category')            => $data->category,
                                        lang('product_name')        => $data->title,
                                        lang('code')                => $data->code,
                                        lang('thumbnail')           => $image,
                                        lang('product_view')        => $data->view,
                                        lang('description')         => $data->description,
                                        lang('quantity_per_serial') => $quantity_per_serial,
                                        lang('serials_per_country') => $serials_per_country,
                                        lang('used')                => $used_status
                                     );
                        $currency_symbol = $this->currency->get_default_country_symbol();

                        if($data->quantity_per_serial == 0)
                        {
                            $row_data[lang('product_cost')] = $data->cost . ' ' . $currency_symbol;
                        }

                        foreach($product_cat_specs as $row)
                        {
                            $row_data[$row->spec_label] = $row->spec_value;
                        }

                        if($data->serials_per_country == 0)
                        {
                            //$global_quantity = $this->products_serials_model->get_product_global_serials_count($id);
                            $country_id = 0;
                            $active_global_qty     = $this->products_serials_model->get_per_country_product_serials_count($id, $country_id, 1);

                            $not_active_global_qty = $this->products_serials_model->get_per_country_product_serials_count($id, $country_id, 0);
                            //$avg_cost              = $this->purchase_orders_model->get_product_avg_cost_per_country($id, 0);

                            $global_quantity = '<span class="badge badge-success">'.lang('active').'</span> ='.$active_global_qty.'  '.'<span class="badge badge-danger">'.lang('not_active').'</span> ='.$not_active_global_qty;

                            $row_data[lang('global_quantitiy')] = $global_quantity;

                            /*if($data->quantity_per_serial == 1)
                            {
                                $row_data[lang('average_cost')] = round($avg_cost, 2);
                            }*/

                        }

                        if(count($product_optional_fields) != 0)
                        {
                            $optional_fields = array();

                            $all_optional_field_options    = array();
                            foreach ($product_optional_fields as $field)
                            {
                                if($field->required == 1)
                                {
                                    $required_span = '<span class="badge badge-success">'.lang('yes').'</span>';
                                }
                                else
                                {
                                    $required_span = '<span class="badge badge-danger">'.lang('no').'</span>';
                                }

                                $field->{'required_span'} = $required_span;
                                
                                // START EDITING - Mrzok : Add Optional field options to optional data
                                $option_options_data    = array();
                                if($field->has_options == 1)
                                {
                                    $optional_options_data = $this->optional_fields_model->get_optional_field_options($field->id, $this->data['lang_id']);
                                }
                                $field->{'optional_field_options'} = $optional_options_data;
                                $all_optional_field_options = array_merge($all_optional_field_options, $optional_options_data);
                                // END Editing

                                $optional_fields[] = $field;
                            }

                            $this->data['product_optional_fields'] = $optional_fields;
                            $this->data['all_optional_field_options'] = $all_optional_field_options;
                            
                            // if the product is freom vendor
                            if($data->quantity_per_serial == 1)
                            {
                                $active_product_quantity_grouped_by_options     = $this->products_serials_model->get_product_serials_count_grouped_by_options($product->product_id, $product->country_id, 1 , 0);
                                if (count($active_product_quantity_grouped_by_options) > 0)
                                {
                                    $this->data['product_optional_options_quantity'] = $active_product_quantity_grouped_by_options;
                                }
                            }
                        }


                        $default_currency = $this->currency->get_default_country_symbol();

                        $this->data['default_currency']             = $default_currency;
                        $this->data['lang_id']                      = $display_lang_id;
                        $this->data['product_id']                   = $id;
                        $this->data['row_data']                     = $row_data;
                        $this->data['products_countries']           = $products_countries_array;
                        $this->data['products_customer_groups']     = $products_customer_groups;
                        $this->data['product_discount']             = $product_discount;
                        //$this->data['product_stock']                = $product_stock_new_array;
                        //$this->data['product_sales']                = $product_sales;
                        $this->data['serial_per_country']           = $serials_per_country_option;
                        $this->data['quantity_per_serial']          = $quantity_per_serial_option;

                    }
                }
                else
                {
                    $this->data['error'] = lang('no_store_permission');
                }
            }
            else
            {
                $this->data['error'] = lang('no_data');
            }

            if($this->view_folder == 'Sell')
            {
                $this->data['content'] = $this->load->view($this->view_folder.'/products_read', $this->data, true);
            }
            else
            {
                $this->data['content'] = $this->load->view('products_read', $this->data, true);
            }

            $this->load->view($this->view_folder.'/main_frame',$this->data);
        }
    }

    public function products_sales_ajax($page = 1)
    {
        $lang_id    = intval($this->input->post('lang_id'));
        $product_id = intval($this->input->post('product_id'));

        echo $this->_product_sales($lang_id, $page, $product_id);
    }

    public function products_stock_ajax($page = 1)
    {
        $lang_id    = intval($this->input->post('lang_id'));
        $product_id = intval($this->input->post('product_id'));

        echo $this->_product_stock($lang_id, $page, $product_id);
    }

    private function _product_sales($display_lang_id, $page, $product_id)
    {
        $this->load->library('pagination');

        $limit  = 20;
        $offset = ($page -1) * $limit;

        $config['base_url']         = base_url().'products/admin_products/products_sales_ajax';
        $config['total_rows']       = $this->products_model->count_sales_rows($product_id ,$display_lang_id);
        $config['per_page']         = $limit;
        $config['uri_segment']      = 4;
        $config['use_page_numbers'] = TRUE;
        $config['attributes']       = array('class' => 'pages_links_sales');

        $config['first_link']       = lang('first_page');
        $config['last_link']        = lang('last_page');
        $config['first_tag_open']   = '<li>';
        $config['first_tag_close']  = '</li>';
        $config['last_tag_open']    = '<li>';
        $config['last_tag_close']   = '</li>';
        $config['next_tag_open']    = '<li>';
        $config['next_tag_close']   = '</li>';
        $config['prev_tag_open']    = '<li>';
        $config['prev_tag_close']   = '</li>';
        $config['num_tag_open']     = '<li>';
        $config['num_tag_close']    = '</li>';
        $config['cur_tag_open']     = '<li><strong>';
        $config['cur_tag_close']    = '</strong></li>';

        $config['display_pages']    = TRUE;

        $sales_array = array();
        $total       = array();
        $total_price = 0;
        $total_qty   = 0;
        $final_total_price = 0;

        $this->pagination->initialize($config);

        $products_sales       = $this->products_model->get_product_sales_data($limit, $offset, $product_id ,$display_lang_id);
        $products_total_sales = $this->products_model->get_product_sales($product_id ,$display_lang_id);

        foreach ($products_sales as $row)
        {
            $price            = $this->currency->get_amount_with_default_currency($row->final_price, $row->country_id);
            $row->final_price = $price;

            $sales_array[]    = $row;
        }

        foreach($products_total_sales as $row)
        {
            $price            = $this->currency->get_amount_with_default_currency($row->final_price, $row->country_id);

            $total_price       += $price;
            $final_total_price += ($price * $row->qty);
            $total_qty         += $row->qty;

            $total['total_qty']         = $total_qty;
            $total['total_price']       = $total_price;
            $total['final_total_price'] = $final_total_price;
        }



        $default_currency_symbol = $this->currency->get_default_currency_data()->currency_symbol;

        $this->data['page_links']      = $this->pagination->create_links();
        $this->data['product_sales']   = $sales_array;
        $this->data['currency_symbol'] = $default_currency_symbol;
        $this->data['total'] = $total;

        return $this->load->view('products_read_sales', $this->data, true);
    }

    private function _product_stock($display_lang_id, $page, $product_id)
    {
        $this->load->library('pagination');

        $limit  = 20;
        $offset = ($page -1) * $limit;

        $config['base_url']         = base_url().'products/admin_products/products_stock_ajax';
        $config['total_rows']       = $this->products_model->count_product_stock_data($product_id ,$display_lang_id);
        $config['per_page']         = $limit;
        $config['uri_segment']      = 4;
        $config['use_page_numbers'] = TRUE;
        $config['attributes']       = array('class' => 'pages_links_stock');

        $config['first_link']       = lang('first_page');
        $config['last_link']        = lang('last_page');
        $config['first_tag_open']   = '<li>';
        $config['first_tag_close']  = '</li>';
        $config['last_tag_open']    = '<li>';
        $config['last_tag_close']   = '</li>';
        $config['next_tag_open']    = '<li>';
        $config['next_tag_close']   = '</li>';
        $config['prev_tag_open']    = '<li>';
        $config['prev_tag_close']   = '</li>';
        $config['num_tag_open']     = '<li>';
        $config['num_tag_close']    = '</li>';
        $config['cur_tag_open']     = '<li><strong>';
        $config['cur_tag_close']    = '</strong></li>';

        $config['display_pages']    = TRUE;

        $product_stock_new_array = array();
        $total       = array();
        $total_price = 0;
        $total_qty   = 0;
        $final_total_price = 0;

        $this->pagination->initialize($config);

        $products_stock           = $this->products_model->get_product_stock_data($limit, $offset, $product_id ,$display_lang_id);
        //$available_qty           = $this->products_serials_model->get_product_available_serials($product_id);
        $default_currency_symbol  = $this->currency->get_default_currency_data()->currency_symbol;
        $available_active_qty     = $this->products_serials_model->get_country_product_serials_count($product_id);
        $available_not_active_qty = $this->products_serials_model->get_country_product_serials_count($product_id, null, 0);

        $available_active_qty     = '<span class="badge badge-success">'.lang('active').'</span> ='.$available_active_qty;
        $available_not_active_qty = '<span class="badge badge-danger">'.lang('not_active').'</span> = '. $available_not_active_qty;

        foreach ($products_stock as $row)
        {
            if($row->country == '')
            {
                $country = lang('global_quantitiy');
            }
            else
            {
                $country = $row->country;
            }

            $current_qty = $this->products_serials_model->count_order_available_product_serials($row->product_id, $row->id);

            $row->{'country'}     = $country;
            $row->{'current_qty'} = $current_qty;

            $product_stock_new_array[] = $row;
        }







        $this->data['page_links']      = $this->pagination->create_links();
        $this->data['product_stock']   = $product_stock_new_array;
        $this->data['currency_symbol'] = $default_currency_symbol;
        $this->data['current_amount']  = $available_active_qty;
        $this->data['current_not_active_amount'] = $available_not_active_qty;

        return $this->load->view('products_read_stock', $this->data, true);
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
        /**************************************/
        if($this->data['method_id'] == 0)
        {
            $delete_method_id = $this->admin_bootstrap->get_a_controller_method_id($this->data['controller_id'], 'delete');
        }
        else
        {
            $delete_method_id = $this->data['method_id'];
        }

        $this->stores   = $this->admin_bootstrap->get_user_available_stores($delete_method_id);
        $store_id_array = array();

        foreach($this->stores as $store)
        {
            $store_id_array[] = $store->store_id;
        }

        $this->stores_ids = $store_id_array;
        /**************************************/

        $products_ids = $this->input->post('row_id');

        $stores_ids = array();

        if(is_array($products_ids))
        {

            $ids_array = array();

            foreach($products_ids as $product_id)
            {
                $product_data = $this->products_model->get_products_row($product_id['value']);

                $ids_array[]  = $product_id['value'];
                $stores_ids[] = $product_data->store_id;
            }
        }
        else
        {
            $product_data = $this->products_model->get_products_row($products_ids);

            $ids_array    = array($products_ids);
            $stores_ids[] = $product_data->store_id;
        }

        if($this->config->item('business_type')=='b2c' || count(array_diff($stores_ids, $this->stores_ids)) == 0)
        {
            $res = $this->products_model->delete_product_data($ids_array);

            if($res)
            {
                echo '1';
            }
            else
            {
                echo lang('delete_error');
            }
        }
        else
        {
            echo lang('no_store_permission');
        }

    }

    public function add()
    {
        $validation_msg = false;

        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
        {
            $validation_msg = true;

            $languages           = $this->input->post('lang_id');
            $serial_per_country  = isset( $_POST['serials_per_country'])? $_POST['serials_per_country']:0;
            $quantity_per_serial = isset( $_POST['quantity_per_serial'])? $_POST['quantity_per_serial']:0;

           foreach($languages as $lang_id)
           {
                $this->form_validation->set_rules('title['.$lang_id.']', 'Title'.$lang_id, 'required');
           }

            $country_ids    = $this->input->post('country_id');

            foreach($country_ids as $country_id)
            {
                //$this->form_validation->set_rules('activate_price['.$country_id.']', 'activate_price'.$country_id, 'required');
                $this->form_validation->set_rules('price['.$country_id.']', 'price'.$country_id, 'required');
                //$this->form_validation->set_rules('active['.$country_id.']', 'active'.$country_id, 'required');
                //$this->form_validation->set_rules('display_home['.$country_id.']', 'display_home'.$country_id, 'required');

            }

            if($quantity_per_serial == 0)
            {
                //$this->form_validation->set_rules('product_cost', lang('product_cost'), 'required');
            }

            $this->form_validation->set_rules('image', 'image', 'required');
            $this->form_validation->set_rules('route', lang('route'), 'required|is_unique[products.route]|valid_url');
            $this->form_validation->set_rules('cat_id', lang('cat_name'), 'required');

            $this->form_validation->set_message('required', lang('required')."  : %s ");
            $this->form_validation->set_message('is_unique', lang('is_unique')." : %s ");
            $this->form_validation->set_message('valid_url', lang('route_valid_note'));

            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        }

        if ($this->form_validation->run() == FALSE)
    		{

    		  $this->_add_form($validation_msg);
        }
        else
        {
            /*******************general data**********************/
            $cat_id     = $this->input->post('cat_id');
            $code       = $this->input->post('code');
            $image      = $this->input->post('image');
            $route      = $this->input->post('route');
            $weight     = $this->input->post('weight');
            $video      = $this->input->post('video');
            $youtube_link   = $this->input->post('youtube_video');
            $youtube_video  = '';
            if($youtube_link != '')
            {
              $youtube_video = substr($youtube_link, strpos($youtube_link, "=") + 1);
            }
            $brand_id   = $this->input->post('brand_id');
            //$store_id   = $this->input->post('store_id');

            if($this->config->item('business_type') == 'b2b')
            {
              $store_id   = $this->input->post('store_id');
            }
            else {
              $store_id = $this->config->item('default_store_id');
            }

            if($quantity_per_serial == 0)
            {
                $cost = $this->input->post('product_cost');
            }
            else
            {
                $cost = 0;
            }

            //sort value
            $max_cat_sort = $this->products_model->get_category_max_sort($cat_id);
            $product_sort = $max_cat_sort + 1;
            $cat_data = $this->products_model->get_table_data('categories', array('id'=>$cat_id), 'row');

            $general_data = array(
                                    'cat_id'                => $cat_id  ,
                                    'parent_cat_id'         => $cat_data->parent_id,
                                    'store_id'              => $store_id,
                                    'code'                  => $code    ,
                                    'image'                 => $image   ,
                                    'video'                 => $video   ,
                                    'youtube_video'         => $youtube_video   ,
                                    'cost'                  => $cost    ,
                                    'weight'                => $weight  ,
                                    'brand_id'              => $brand_id,
                                    'route'                 => str_replace(' ', '', $route) ,
                                    'serials_per_country'   => (isset( $_POST['serials_per_country']))? $_POST['serials_per_country']:0,
                                    'quantity_per_serial'   => (isset( $_POST['quantity_per_serial']))? $_POST['quantity_per_serial']:0,
                                    'shipping'              => (isset( $_POST['shipping']))? $_POST['shipping']:0                      ,
                                    'non_serials'           => (isset( $_POST['non_serials']))? $_POST['non_serials']:0                ,
                                    'is_used'               => (isset( $_POST['is_used']))? $_POST['is_used']:0                        ,
                                    'is_returned'           => (isset( $_POST['is_returned']))? $_POST['is_returned']:0                ,
                                    'sort'                  => $product_sort
                                  );

            // create image thumb
            $this->uploaded_images->resize_image($image, 3);
            $this->uploaded_images->resize_image($image, 4);

            if($this->products_model->insert_products($general_data))
            {
                $last_insert_id = $this->db->insert_id();


                /**********************products_translation data****************************/

                $title          = $this->input->post('title');
                $description    = $this->input->post('description');
                $meta_title     = $this->input->post('meta_title');


                foreach($languages as $lang_id)
                {
                    $products_translation_data = array(
                                                        'product_id'    => $last_insert_id          ,
                                                        'title'         => $title[$lang_id]         ,
                                                        'description'   => $description[$lang_id]   ,
                                                        'meta_title'    => $meta_title[$lang_id]    ,
                                                        'lang_id'       => $lang_id                 ,
                                                     );

                    $this->products_model->insert_products_translation($products_translation_data);

                     /*******************Tags data **************************/
                    $tags = $this->input->post('tags');

                    if(isset($tags[$lang_id]) && count($tags[$lang_id]))
                    {
                        foreach($tags[$lang_id] as  $tag)
                        {
                            $tag_id = $this->products_tags_model->get_tag_id($tag,$lang_id);

                            $products_tags_data = array ('tag_id'=>$tag_id,'product_id'=>$last_insert_id);

                            $this->products_tags_model->insert_tags_products($products_tags_data);
                        }
                    }


                    /**********************************************************/
                }

                /**********************Cats Specs**************************/

                if(isset($_POST['spec_value']) && count($_POST['spec_value']) != 0)
                {
                    $cats_specs     = $this->input->post('spec_value');
                    //$cats_specs_ids = $this->input->post('cat_spec_id');

                    $this->_insert_product_cat_species($cats_specs, $last_insert_id);
                }

                /*********** products_countries data ***********/
                $activate_price = $this->input->post('activate_price');
                $price          = $this->input->post('price');
               // $reward_points  = $this->input->post('reward_points');
                $vat_id  = $this->input->post('vat_id', true);

                //$points_cost    = $this->input->post('points_cost');

                foreach($country_ids as $country_id)
                {
                    //if(isset($price[$country_id]) && $price[$country_id] != 0)
                    //{
                        if(isset($_POST['sell']))
                        {
                            $product_vat = 0;//$settings->
                        }
                        else
                        {
                            $product_vat = $vat_id[$country_id];
                        }
                        

                    if(isset($_POST['sell']))
                    {
                        $active_val = 1;
                    }
                    else
                    {
                        $active_val = (isset( $_POST['active'][$country_id]))? $_POST['active'][$country_id]:0;
                    }

                    $products_countries_data     = array(
                                                'product_id'            => $last_insert_id ,
                                                'country_id'            => $country_id ,
                                                'vat_id'                => $product_vat,
                                                'price'                 => $price[$country_id] ,
                                                //'reward_points'         => $reward_points[$country_id] ,
                                                //'points_cost'           => $points_cost[$country_id] ,
                                                'active'                => $active_val,
                                                'display_home'          => (isset( $_POST['display_home'][$country_id]))? $_POST['display_home'][$country_id]:0,

                                            );


                    $this->products_model->insert_products_countries_prices($products_countries_data);
                    //}
                }
                /*************************products_customer_groups_prices data**************/

                $group_prices  = $this->input->post('group_price');

                if(count($group_prices) != 0)
                {
                    foreach($group_prices as $country_id=> $group)
                    {
                        if(isset($activate_price[$country_id]) && $activate_price[$country_id] == 1)
                        {
                            foreach($group as $group_id => $group_price)
                            {
                                if($group_price != 0)
                                {
                                    $products_customer_groups_prices_data    = array(
                                                                                    'product_id'            => $last_insert_id ,
                                                                                    'country_id'            => $country_id     ,
                                                                                    'customer_group_id'     => $group_id       ,
                                                                                    'group_price'           => $group_price
                                                                               );

                                    $this->products_model->insert_products_customer_groups_prices($products_customer_groups_prices_data);
                                }
                            }
                        }
                   }
               }


               /*****************Multi Image Upload Data******************/

                if(isset($_FILES['files']) && count(array_filter($_FILES['files']['name'])) != 0)
                {
                    //Configure upload.
                    $this->upload->initialize_multiupload(array(
                        "upload_path"   => realpath(APPPATH. '../assets/uploads/products') ,
                        "allowed_types" => 'gif|png|jpg|jpeg|tif',
                        "encrypt_name"  => TRUE
                    ));

                    //Perform upload.
                    if(! $this->upload->do_multi_upload("files")) {
                        //Code to run upon successful upload.
                        $error['error'] = $this->upload->display_errors();
                    }
                    else
                    {
                        $uploaded_images_data = $this->upload->get_multi_upload_data();

                        foreach($uploaded_images_data as $image)
                        {
                            $image_array = array(
                                                    'image'     => $image['file_name'],
                                                    'unix_time' => time()
                                                );

                            $this->products_model->insert_image($image_array);

                            $image_id = $this->db->insert_id();

                            $prouct_image_data = array(
                                                          'product_id' => $last_insert_id,
                                                          'image_id'   => $image_id
                                                      );

                            $this->products_model->insert_image_product($prouct_image_data);

                            //upload on amazon
                            $this->amazon_s3_uploads->upload_to_o3($image['file_name'], 'products');
                        }
                    }

                }

               /************************Optional Fields****************************/
               if(isset($_POST['option_id']) && count($_POST['option_id']) != 0)
               {
                    $options_ids = $this->input->post('option_id');
                    $values      = $this->input->post('value');
                    $costs       = $this->input->post('cost');
                    $weight      =  $this->input->post('op_weight');
                    $groups_ids  = $this->input->post('field_group_id');

                    $sec_options_ids = $this->input->post('sec_option_id');
                    $sec_values      = $this->input->post('sec_value');

                    $this->_insert_products_optional_fields($last_insert_id, $options_ids, $values, $costs, $weight, $groups_ids, $sec_options_ids, $sec_values);

               }
               /****************************************************/
               $_SESSION['success'] = lang('success');
               $this->session->mark_as_flash('success');

               redirect($this->data['module'] . "/" . $this->data['controller'], 'refresh');
           }
        }
    }

    private function _add_form($validation_msg)
    {
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }

        $this->_js_and_css_files();

        $settings       = $this->global_model->get_config();
        $users_group_id = $settings->new_user_customer_group_id;

        $this->data['mode']               = 'add';
        $this->data['form_action']        = $this->data['module'] . "/" . $this->data['controller'] . "/add";
        $this->data['countries']          = $this->countries_model->get_countries($this->lang_row->id);
        $this->data['customer_groups']    = $this->customer_groups_model->get_available_customer_groups($this->lang_row->id, $users_group_id);
        //$optional_fields                  = $this->optional_fields_model->get_all_optional_fields_result($this->lang_row->id);
        $optional_fields                  = $this->optional_fields_model->get_all_optional_fields_result($this->lang_row->id);
        $secondary_optional_fields        = $this->optional_fields_model->get_all_optional_fields_result($this->lang_row->id, 1);
        $opt_fields_groups                = $this->optional_fields_model->get_optional_fields_groups_translation($this->lang_row->id);

        $categories                       = $this->cat_model->get_categories($this->lang_row->id);
        $brands                           = $this->brands_model->get_all_brands($this->lang_row->id);

        $stores                           = $this->stores;//$this->stores_model->get_all_stores($this->lang_row->id);

        $cats_array   = array();
        $brands_array = array();
        $stores_array = array();
        $groups_array = array();

        $cats_array[NULL]   = '-----------------';
        $brands_array[Null] = '-----------------';
        $stores_array[Null] = '-----------------';

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

        foreach($opt_fields_groups as $row)
        {
            $groups_array[$row->group_id] = $row->name;
        }

        foreach($stores as $store)
        {
            $stores_array[$store->store_id] = $store->name;
        }

        foreach($brands as $brand)
        {
            $brands_array[$brand->brand_id] = $brand->name;
        }

        $vats = $this->vats_model->get_available_vats($this->data['lang_id']);
        $vats_array[0] = lang('no_vats');
        foreach($vats as $vat)
        {
            $vats_array[$vat->id] = $vat->name;
        }

        $this->data['vats']                         = $vats_array;
        $this->data['cats_array']                   = $cats_array;
        //$this->data['optional_fields'] = $optional_fields;
        $this->data['brands']                       = $brands_array;
        $this->data['optional_fields']              = $optional_fields;
        $this->data['secondary_optional_fields']    = $secondary_optional_fields;
        $this->data['opt_fields_groups']            = $groups_array;
        $this->data['stores']                       = $stores_array;


        if($this->view_folder == 'Sell')
        {
            $this->data['content']      = $this->load->view($this->view_folder.'/products_form', $this->data, true);
        }
        else
        {
            $this->data['content']      = $this->load->view('products', $this->data, true);
        }
        $this->load->view($this->view_folder.'/main_frame',$this->data);
    }

    public function edit($id)
    {
        if(is_numeric($id))
        {
            $id = intval($id);

            $validation_msg = false;

            $products_countries = $this->products_model->get_products_countries($id);

            if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
            {
                $validation_msg = true;

                $languages           = $this->input->post('lang_id');
                //$id                  = $this->input->post('product_id');

                $serial_per_country  = isset( $_POST['serials_per_country'])? $_POST['serials_per_country']:0;
                $quantity_per_serial = isset( $_POST['quantity_per_serial'])? $_POST['quantity_per_serial']:0;

                foreach($languages as $lang_id)
                {
                    $this->form_validation->set_rules('title['.$lang_id.']', 'Title'.$lang_id, 'required');
                }

                $country_ids    = $this->input->post('country_id');

                /*if(count($country_ids) != 0)

                {
                    foreach($country_ids as $country_id)
                    {
                        //$this->form_validation->set_rules('price['.$country_id.']', 'price'.$country_id, 'required');
                    }
                }*/
                $this->form_validation->set_rules('image', 'image', 'required');

                /*if($quantity_per_serial == 0)
                {
                    //$this->form_validation->set_rules('product_cost', lang('product_cost'), 'required');
                }*/

                $product_data = $this->products_model->get_products_row($id);
                if($this->input->post('route') != $product_data->route)
                {
                    $this->form_validation->set_rules('route', ('route'), 'required|is_unique[products.route]|valid_url');
                }
                else
                {
                    $this->form_validation->set_rules('route', ('route'), 'required|valid_url');
                }

                $this->form_validation->set_rules('cat_id', lang('cat_name'), 'required');

                $this->form_validation->set_message('is_unique', lang('is_unique')." : %s ");
                $this->form_validation->set_message('required', lang('required')."  : %s ");
                $this->form_validation->set_message('valid_url', lang('route_valid_note'));

                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            }
        }

        if($this->form_validation->run() == FALSE)
		{
            $this->_edit_form($id, $validation_msg);
        }
        else
        {
            
            /*******************general data**********************/
            $cat_id     = $this->input->post('cat_id', true);
            $code       = $this->input->post('code', true);
            $image      = $this->input->post('image', true);
            $route      = $this->input->post('route', true);
            $weight     = $this->input->post('weight', true);
            $brand_id   = intval($this->input->post('brand_id', true));
            $video      = $this->input->post('video');
            $youtube_link  = $this->input->post('youtube_video');
            $youtube_video  = '';
            if($youtube_link != '')
            {
              $youtube_video = substr($youtube_link, strpos($youtube_link, "=") + 1);
            }
            //$store_id   = $this->input->post('store_id', true);
            if($this->config->item('business_type') == 'b2b')
            {
              $store_id = $this->input->post('store_id');
            }
            else
            {
              $store_id = $this->config->item('default_store_id');
            }

            if($quantity_per_serial == 0)
            {
                $cost = $this->input->post('product_cost');
            }
            else
            {
                $cost = 0;
            }

            $cat_data = $this->products_model->get_table_data('categories', array('id'=>$cat_id), 'row');

            $general_data  = array(
                                    'cat_id'                => $cat_id  ,
                                    'parent_cat_id'         => $cat_data->parent_id,
                                    'store_id'              => $store_id,
                                    'code'                  => $code    ,
                                    'image'                 => $image   ,
                                    'video'                 => $video   ,
                                    'youtube_video'         => $youtube_video   ,
                                    'cost'                  => $cost    ,
                                    'weight'                => $weight  ,
                                    'brand_id'              => $brand_id,
                                    'route'                 => str_replace(' ', '', $route),
                                    'serials_per_country'   => (isset( $_POST['serials_per_country']))? $_POST['serials_per_country']:0,
                                    'quantity_per_serial'   => (isset( $_POST['quantity_per_serial']))? $_POST['quantity_per_serial']:0,
                                    'shipping'              => (isset( $_POST['shipping']))? $_POST['shipping']:0                       ,
                                    'non_serials'           => (isset( $_POST['non_serials']))? $_POST['non_serials']:0,
                                    'is_used'               => (isset( $_POST['is_used']))? $_POST['is_used']:0                        ,
                                    'is_returned'           => (isset( $_POST['is_returned']))? $_POST['is_returned']:0                ,
                                  );

            $this->products_model->update_products($id, $general_data);

            // create image thumb
            $this->uploaded_images->resize_image($image, 3);
            $this->uploaded_images->resize_image($image, 4);

            /**********************products_translation data****************************/

            $title          = $this->input->post('title');
            $description    = $this->input->post('description');
            $meta_tag       = $this->input->post('meta_tag');
            $meta_title     = $this->input->post('meta_title');

            $this->products_model->delete_tags_products($id);
            foreach($languages as $lang_id)
            {
                $products_translation_data = array(
                                                    'title'         => $title[$lang_id]         ,
                                                    'description'   => $description[$lang_id]   ,
                                                    'meta_tag'      => $meta_tag[$lang_id]      ,
                                                    'meta_title'    => $meta_title[$lang_id]
                                                   );

                $this->products_model->update_products_translation($id,$lang_id,$products_translation_data);

                /*******************Tags data **************************/
                $tags = $this->input->post('tags');

                if(isset($tags[$lang_id]) && count($tags[$lang_id]))
                {
                    foreach($tags[$lang_id] as  $tag)
                    {
                        $tag_id = $this->products_tags_model->get_tag_id($tag,$lang_id);

                        $products_tags_data = array (
                                                       'tag_id'     => $tag_id,
                                                       'product_id' => $id
                                                    );

                        $this->products_tags_model->insert_tags_products($products_tags_data);
                    }
                }

            /************************************************/
            }

            /*********** products_countries data ***********/
            $activate_price = $this->input->post('activate_price');
            $country_ids    = $this->input->post('country_id');
            $price          = $this->input->post('price');
            //$reward_points  = $this->input->post('reward_points');
            $vat_id  = $this->input->post('vat_id');

            //$points_cost    = $this->input->post('points_cost');

            $this->products_model->delete_products_countries($id);

            if(count($country_ids) != 0)
            {
                foreach ($country_ids as $country_id)
                {
                    if(isset($_POST['sell']))
                    {
                        $product_vat = 0;//$settings->
                    }
                    else
                    {
                        $product_vat = $vat_id[$country_id];
                    }
                        
                        
                    if(isset($_POST['sell']))
                    {
                        $active_val = 1;
                    }
                    else
                    {
                        $active_val = (isset( $_POST['active'][$country_id]))? $_POST['active'][$country_id]:0;
                    }

                    $products_countries_data     = array(
                                                            'product_id'            => $id ,
                                                            'country_id'            => $country_id ,
                                                            'vat_id'                => $product_vat,
                                                            'price'                 => $price[$country_id] ,
                                                            //'reward_points'         => $reward_points[$country_id] ,
                                                            //'points_cost'           => $points_cost[$country_id] ,
                                                            'active'                => $active_val,
                                                            'display_home'          => (isset( $_POST['display_home'][$country_id]))? $_POST['display_home'][$country_id]:0,
                                                            //'average_cost'          => $average_cost[$country_id]
                                                        );
                    //echo '<pre>'; print_r($products_countries_data); die();
                    if(isset($_POST['average_cost']) && count($_POST['average_cost']) != 0)
                    {
                        $average_cost   = $this->input->post('average_cost');
                        $products_countries_data['average_cost'] = $average_cost[$country_id];
                    }

                    $this->products_model->insert_products_countries_prices($products_countries_data);
                    
                }
            }
            /*************************products_customer_groups_prices data**************/
            $this->products_model->delete_products_customer_groups_prices($id);

            $group_prices = $this->input->post('group_price');

            if(count($group_prices) != 0)
            {
                foreach($group_prices as $country_id=> $group)
                {
                    if(isset($activate_price[$country_id]) && $activate_price[$country_id] == 1)
                    {
                        foreach($group as $group_id => $group_price)
                        {
                            if($group_price != 0)
                            {
                                $products_customer_groups_prices_data    = array(
                                                                                'product_id'            => $id ,
                                                                                'country_id'            => $country_id ,
                                                                                'customer_group_id'     => $group_id ,
                                                                                'group_price'           => $group_price ,
                                                                            );

                                $this->products_model->insert_products_customer_groups_prices($products_customer_groups_prices_data);
                            }
                        }
                    }
                }
            }
            /**********************Cats Specs**************************/

            $this->products_model->delete_product_cat_specs($id);

            if(isset($_POST['spec_value']) && count($_POST['spec_value']) != 0)
            {

                // delete old species
                $this->products_model->delete_product_cat_specs($id);

                $cats_specs     = $this->input->post('spec_value');
                //$cats_specs_ids = $this->input->post('cat_spec_id');

                $this->_insert_product_cat_species($cats_specs, $id);

            }


            /*****************Multi Image Upload Data******************/

            if(isset($_FILES['files']) && count(array_filter($_FILES['files']['name'])) != 0)
            {
                //Configure upload.
                $this->upload->initialize_multiupload(array(
                    "upload_path"   => realpath(APPPATH. '../assets/uploads/products') ,
                    "allowed_types" => 'gif|png|jpg|jpeg|tif',
                    "encrypt_name"  => TRUE
                ));

                //Perform upload.
                if(! $this->upload->do_multi_upload("files")) {
                    //Code to run upon successful upload.
                    $error['error'] = $this->upload->display_errors();
                }
                else
                {
                    $uploaded_images_data = $this->upload->get_multi_upload_data();

                    foreach($uploaded_images_data as $image)
                    {
                        $image_array = array(
                                                'image'     => $image['file_name'],
                                                'unix_time' => time()
                                            );

                        $this->products_model->insert_image($image_array);

                        $image_id = $this->db->insert_id();

                        $prouct_image_data = array(
                                                      'product_id' => $id,
                                                      'image_id'   => $image_id
                                                  );

                        $this->products_model->insert_image_product($prouct_image_data);
                        //upload on amazon
                        $this->amazon_s3_uploads->upload_to_o3($image['file_name'], 'products');
                    }
                }

            }

            /************************Optional Fields****************************/
            //$this->products_model->delete_product_optional_field($id);
            if(isset($_POST['option_id']) && count($_POST['option_id']) != 0)
            {
                $options_ids = $this->input->post('option_id');
                $values      = $this->input->post('value');
                $costs       = $this->input->post('cost');
                $weight      =  $this->input->post('op_weight');
                $groups_ids  = $this->input->post('field_group_id');

                $sec_options_ids = $this->input->post('sec_option_id');
                $sec_values      = $this->input->post('sec_value');

                $this->_insert_products_optional_fields($id, $options_ids, $values, $costs, $weight, $groups_ids, $sec_options_ids, $sec_values);
           }

           if(isset($_POST['exist_option_id']) && count($_POST['exist_option_id']) != 0)
            {

                $options_ids = $this->input->post('exist_option_id');
                $values      = $this->input->post('exist_value');
                $costs       = $this->input->post('exist_cost');
                $weights     = $this->input->post('exist_weight');
                $groups_ids  = $this->input->post('exist_field_group_id');

                $sec_options_ids = $this->input->post('exist_sec_option_id');
                $sec_values      = $this->input->post('exist_sec_value');
                $this->_update_exist_product_optional_fields($id, $options_ids, $values, $costs, $weights, $groups_ids, $sec_options_ids, $sec_values);
            }


            /**********************************************************/


           $_SESSION['success'] = lang('updated_successfully');
           $this->session->mark_as_flash('success');

           redirect('products/admin_products/','refresh');
        }
    }

    private function _edit_form($id, $validation_msg)
    {
        $this->_js_and_css_files();
        if($validation_msg)
        {
            $this->data['validation_msg'] = lang('fill_required_fields');
        }

        $settings       = $this->global_model->get_config();
        $users_group_id = $settings->new_user_customer_group_id;

        $this->data['mode']               = 'edit';
        $this->data['form_action']        = $this->data['module'] . "/" . $this->data['controller'] . "/edit/" . $id;
        $this->data['id']                 = $id;

        $lang_id                          = $this->lang_row->id;

        $general_data                     = $this->products_model->get_products_row($id);

        if(in_array($general_data->store_id, $this->stores_ids) || $this->config->item('business_type') == 'b2c')
        {
          if($this->config->item('business_type') == 'b2b')
          {
            $categories = $this->stores_model->get_store_available_cats_data($general_data->store_id, $this->data['lang_id']);//$this->cat_model->get_categories($lang_id);
            $stores     = $this->stores;//$this->stores_model->get_all_stores($this->lang_row->id);

            /***********************************************************************/
            $stores_array       = array();
            $stores_array[Null] = '-----------------';
            foreach($stores as $store)
            {
                $stores_array[$store->store_id] = $store->name;
            }

            $this->data['stores'] = $stores_array;

          }
          else {
            $categories = $this->cat_model->get_categories($lang_id);
          }
            //$categories                       = $this->stores_model->get_store_available_cats_data($general_data->store_id, $this->data['lang_id']);//$this->cat_model->get_categories($lang_id);
            $data                             = $this->products_model->get_products_translation_result($id);
            $tags_data                        = $this->products_tags_model->get_products_tags_result($id);
            $products_countries               = $this->products_model->get_products_countries($id);
            $countries                        = $this->countries_model->get_countries($lang_id);
            $products_customer_groups_prices  = $this->products_model->products_customer_groups_prices($id);
            $customer_groups                  = $this->customer_groups_model->get_available_customer_groups($lang_id, $users_group_id);
            $product_cat_specs                = $this->products_model->get_product_cat_specs_result($general_data->cat_id, $lang_id, $id);
            //$optional_fields                  = $this->optional_fields_model->get_all_optional_fields_result($this->lang_row->id);
            //$product_optional_fields          = $this->products_model->get_product_optional_fields($id, $this->lang_row->id);
            $currency_symbol                  = $this->currency->get_default_country_symbol();
            $product_images                   = $this->products_model->get_product_images($id);
            $brands                           = $this->brands_model->get_all_brands($this->lang_row->id);
            $stores                           = $this->stores;//$this->stores_model->get_all_stores($this->lang_row->id);

            $optional_fields                  = $this->optional_fields_model->get_all_optional_fields_result($this->lang_row->id);
            $secondary_optional_fields        = $this->optional_fields_model->get_all_optional_fields_result($this->lang_row->id, 1);
            $product_optional_fields          = $this->products_model->get_product_main_optional_fields($id, $this->lang_row->id, 0,0);
            //$product_sec_optional_fields      = $this->products_model->get_product_optional_fields($id, $this->lang_row->id,0 , 1);
            $currency_symbol                  = $this->currency->get_default_country_symbol();
            $product_images                   = $this->products_model->get_product_images($id);
            $opt_fields_groups                = $this->optional_fields_model->get_optional_fields_groups_translation($this->lang_row->id);

            /***********product_translation****************/
            $filtered_data              = array();
            foreach($data as $row)
            {
                $filtered_data[$row->lang_id] = $row;
            }

            $cats_array = array();
            $cats_array[NULL] = '-----------------';
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

            /***********product_tags****************/
            $tags = array();
            foreach($tags_data as $row)
            {
                $tags[$row->lang_id][]= $row->tag;
            }

            /***********product_countries****************/
            $products_countries_array   = array();
            $available_serials          = array();
            $average_cost_array         = array();

            if(count($products_countries) != 0)
            {
                foreach($products_countries as $row)
                {
                    // available serials count
                    $products_countries_array[$row->product_id][]           = $row->country_id;
                    $available_serials[$row->product_id][$row->country_id]  = $this->products_serials_model->get_product_country_available_serials($row->product_id,$row->country_id);

                    /*if($general_data->serials_per_country == 0)
                    {
                        $country_id = 0;
                    }
                    else
                    {
                        $country_id = $row->country_id;
                    }*/

                    $avg_cost = $this->products_model->get_product_avg_cost($row->product_id, $row->country_id);

                    $average_cost_array[$row->country_id] = $avg_cost;//->price_per_unit;

                }
            }


            $products_countries_data = array();
            foreach($products_countries as $row)
            {
                $products_countries_data[$row->country_id]= $row;
            }
            /***********products_countries****************/
            $product_group_prices = array ();

            if(count($products_customer_groups_prices) != 0)
            {
                foreach( $products_customer_groups_prices as $country_id => $group )
                {
                    foreach($group as $group->id => $group_price)
                    {
                        $product_group_prices[$group->country_id][$group->customer_group_id] = $group->group_price;
                    }
                }
            }

            /****************Product Cat Species***************/
            $cat_specs_array = array();

            if(count($product_cat_specs) != 0)
            {
                foreach($product_cat_specs as $row)
                {
                    // if cat spec has no value add it to all languages
                    if($row->lang_id == '')
                    {
                        foreach($this->data['data_languages'] as $lang)
                        {
                            $cat_specs_array[$lang->id][] = $row;
                        }
                    }
                    else
                    {
                        $cat_specs_array[$row->lang_id][]= $row;
                    }
                }
            }

            /************Product Images ******************/
            if(count($product_images) != 0)
            {
                $this->data['product_images'] = $product_images;
            }

            /**********************************************************************/

            $brands_array = array();
            $brands_array[Null] = '-----------------';
            foreach($brands as $brand)
            {
                $brands_array[$brand->id] = $brand->name;
            }

            /***********************************************************************/
            $stores_array       = array();
            $stores_array[Null] = '-----------------';
            foreach($stores as $store)
            {
                $stores_array[$store->store_id] = $store->name;
            }

            /****************OPTIONAL FIELDS*****************/
            if(count($product_optional_fields) != 0)
            {
                $op_array = array();

                foreach($product_optional_fields as $field)
                {

                    if($field->free == 0)
                    {
                        $field_cost = $this->products_model->get_product_optional_field_cost($id, $field->optional_field_id, $lang_id);

                        $field_cost_array = array();
                        foreach($field_cost as $one_row)
                        {
                            $group_price_array = array();
                            $op_groups_prices = $this->products_model->get_op_groups_prices($one_row->optional_field_id, $one_row->option_id);

                            foreach($op_groups_prices as $op_gr)
                            {
                                $group_price_array[$op_gr->customer_group_id] = $op_gr->group_price;
                            }
                            $one_row->{'groups_prices'} = $group_price_array;
                            $field_cost_array[] = $one_row;
                        }
                        $field->{'cost'} = $field_cost_array;
                    }


                    $sec_options = $this->products_model->get_product_optional_fields($id, $lang_id, 0, $field->optional_field_id);

                    $sec_options_array = array();

                    foreach($sec_options as $sec_opt)
                    {
                        if($sec_opt->free == 0)
                        {
                            $field_cost = $this->products_model->get_product_optional_field_cost_sec($id, $sec_opt->optional_field_id, $lang_id, $field->optional_field_id);
                            $sec_opt->{'cost'} = $field_cost;
                        }

                        $sec_options_array[] = $sec_opt;
                    }

                    $field->{'secondary_fields'} = $sec_options_array;

                    $op_array[] = $field;
                }


                $this->data['product_optional_fields'] = $op_array;
            }
            
            $groups_array = array();

            foreach($opt_fields_groups as $row)
            {
                $groups_array[$row->group_id] = $row->name;
            }
            $sec_array = array();
            foreach($secondary_optional_fields as $row)
            {
                $sec_array[$row->id] = $row->label;
            }
            /***************************************************/

            $vats = $this->vats_model->get_available_vats($this->data['lang_id']);
            $vats_array[0] = lang('no_vats');
            foreach($vats as $vat)
            {
                $vats_array[$vat->id] = $vat->name;
            }

            $this->data['vats']                             = $vats_array;
            $this->data['general_data']                     = $general_data ;
            $this->data['cats_array']                       = $cats_array;
            $this->data['data']                             = $filtered_data;
            $this->data['tags']                             = $tags;
            $this->data['products_countries_data']          = $products_countries_data ;
            $this->data['products_countries']               = $products_countries_array ;
            $this->data['countries']                        = $countries;
            $this->data['products_customer_groups_prices']  = $product_group_prices;
            $this->data['customer_groups']                  = $customer_groups;
            $this->data['available_serials']                = $available_serials;
            $this->data['average_cost']                     = $average_cost_array;
            $this->data['cat_specs']                        = $cat_specs_array;
            $this->data['optional_fields']                  = $optional_fields;
            $this->data['currency_symbol']                  = $currency_symbol;
            $this->data['brands']                           = $brands_array;
            $this->data['stores']                           = $stores_array;
            $this->data['opt_fields_groups']                = $groups_array;
            $this->data['optional_fields']                  = $optional_fields;
            $this->data['secondary_optional_fields']        = $secondary_optional_fields;
            $this->data['secondary_optional_fields2']       = $sec_array;

        }
        else
        {
            $this->data['error_msg'] = lang('no_store_permission');
        }

        if($this->view_folder == 'Sell')
        {
            $this->data['content']      = $this->load->view($this->view_folder.'/products_form', $this->data, true);
        }
        else
        {
            $this->data['content']      = $this->load->view('products', $this->data, true);
        }
        $this->load->view($this->view_folder.'/main_frame',$this->data);
    }

    private function _insert_product_cat_species($cats_specs, $product_id)
    {

        if(count($cats_specs) != 0)
        {
            foreach($cats_specs as $cat_spec_id=> $lang_array)
            {
                $product_cat_spec_data = array(
                                                'product_id'  => $product_id,
                                                'cat_spec_id' => $cat_spec_id
                                              );

                $this->products_model->insert_product_cat_specs($product_cat_spec_data);

                $product_cat_spec_id = $this->db->insert_id();

                foreach($lang_array as $lang_id=>$spec_value)
                {
                    $product_cat_spec_translation_data = array(
                                                                'lang_id'         => $lang_id,
                                                                'product_spec_id' => $product_cat_spec_id ,
                                                                'spec_value'      => $spec_value
                                                              );

                    $this->products_model->insert_cat_spec_translation_data($product_cat_spec_translation_data);

                }
            }
        }
        /*if(count($cats_specs_ids) != 0)
        {
            foreach ($cats_specs_ids as $key=>$cat_spec_id)
            {
                $product_cat_spec_data = array(
                                                'product_id'  => $product_id,
                                                'cat_spec_id' => $cat_spec_id
                                              );

                $this->products_model->insert_product_cat_specs($product_cat_spec_data);

                $product_cat_spec_id = $this->db->insert_id();

                foreach($languages as $lang_id)
                {
                    $spec_value = $cats_specs[$lang_id][$key];

                    if($spec_value != '')
                    {
                        $product_cat_spec_translation_data = array(
                                                                    'lang_id'         => $lang_id,
                                                                    'product_spec_id' => $product_cat_spec_id ,
                                                                    'spec_value'      => $spec_value
                                                                  );

                        $this->products_model->insert_cat_spec_translation_data($product_cat_spec_translation_data);
                    }
                }
            }
        }*/
    }


    public function get_suggestions()
    {
        $term        = $this->input->post('term');
        $suggestions = $this->products_tags_model->get_suggestions($term);
        $result      = array();

        foreach($suggestions as $row)
        {
            $result[]=array('label'=>$row->tag , 'value'=>$row->tag);
        }

        echo json_encode($result);
    }

    public function get_cat_spec()
    {
        $this->load->model('categories/categories_specifications_model');

        $cat_id    = intval($this->input->post('cat_id'));
        $languages = $this->lang_model->get_active_data_languages();

        $cat_spec  = array();

        $lang_id   = $this->data['active_language']->id;
        foreach ($languages as $lang)
        {
            $cat_lang_specs  = '';
            $cat_specs_ids   = '';

            $cat_specs       = $this->categories_specifications_model->get_cat_all_specifications($cat_id, $lang_id);

            foreach($cat_specs as $row)
            {
                $cat_lang_specs .= '<div class="form-group"><label class="control-label col-md-3">'.$row->spec_label.'</label><div class="col-md-4"><input type="text" name="spec_value['.$lang->id.'][]" value="" class="form-control"></div></div>';
                $cat_specs_ids  .= '<input type="hidden" name="cat_spec_id[]" value="'.$row->id.'" />';
            }

            $cat_spec[$lang->id] = $cat_lang_specs;
        }

        echo json_encode(array($cat_spec, $cat_specs_ids));


    }

    private function _insert_products_optional_fields($product_id, $options_ids, $values, $costs, $weights, $groups_ids=array(),
    $sec_options_ids, $sec_values)
    {
        $index = 0;

        $options_active   = $this->input->post('op_active', true);
        $op_groups_prices = $this->input->post('op_group_price', true);

        $this->load->library('upload');
        $gallery_path = realpath(APPPATH. '../assets/uploads/products');

        if(count($costs) != 0 )
        {

            foreach($costs as $main_option_id=>$cost_array)
            {

              //  $group_id = $groups_ids[$index];

                $option_data = $this->optional_fields_model->get_optional_field_row($main_option_id);
                // insert in optional fields data
                $main_optional_fields_data = array(
                                                    'product_id'        => $product_id,
                                                    'optional_field_id' => $main_option_id,
                                                    'default_value'     => isset($values[$main_option_id]) ? $values[$main_option_id] : '',
                                                    'required'          => isset( $_POST['required'][$main_option_id]) && ($_POST['required'][$main_option_id] == 1) ? $_POST['required'][$main_option_id] : 0,
                                                    //'field_group_id'    => $group_id,
                                                  );

                $this->products_model->insert_product_optional_field($main_optional_fields_data);

                // if has options
                if($option_data->free == 0)
                {
                  $config = array();
                  //$files = $_FILES;
                  $config['upload_path']   = $gallery_path;
                  $config['allowed_types'] = 'png|jpg|jpeg|tif';
                  $config['max_size']      = '10000';


                    foreach($cost_array as $option_cost_id=>$cost_val)
                    {

                        if(!is_array($cost_val))
                        {
                            $image_name = '';
                           //upload option image
                            if(isset($_FILES['op_image']['name'][$main_option_id][$option_cost_id]))
                            {
                                $files = $_FILES;

                                $_FILES['op_image_data']['name']     = $files['op_image']['name'][$main_option_id][$option_cost_id];
                                $_FILES['op_image_data']['type']     = $files['op_image']['type'][$main_option_id][$option_cost_id];
                                $_FILES['op_image_data']['tmp_name'] = $files['op_image']['tmp_name'][$main_option_id][$option_cost_id];
                                $_FILES['op_image_data']['error']    = $files['op_image']['error'][$main_option_id][$option_cost_id];
                                $_FILES['op_image_data']['size']     = $files['op_image']['size'][$main_option_id][$option_cost_id];

                                $this->upload->initialize($config);

                                if(!$this->upload->do_upload('op_image_data'))
                                {
                                    $image_name = '';
                                    //echo $this->upload->display_errors().'<br />error<br />';
                                }
                                else
                                {
                                   $file_data   = $this->upload->data();
                                   $image_name  = $file_data['file_name'];

                                   //upload on amazon
                                   $this->amazon_s3_uploads->upload_to_o3($image_name, 'products');

                                }



                            }

                            $active_op_val = isset($options_active[$main_option_id][$option_cost_id]) ? 1 : 0;
                            $op_weight     = isset($weights[$main_option_id][$option_cost_id]) ? $weights[$main_option_id][$option_cost_id] : 0;

                            $main_cost_data = array(
                                            'product_id'        => $product_id  ,
                                            'optional_field_id' => $main_option_id   ,
                                            'option_id'         => $option_cost_id ,
                                            'cost'              => $cost_val,
                                            'weight'            => $op_weight,
                                            'active'            => $active_op_val,
                                            'image'             => $image_name
                                          );

                            $this->products_model->insert_products_optional_fields_costs($main_cost_data);
                            $cost_id = $this->db->insert_id();

                            //insert op customer groups prices
                            foreach($op_groups_prices[$main_option_id][$option_cost_id] as $group_id=>$group_price)
                            {
                                if($group_price != 0)
                                {
                                    //insert customer group prices
                                    $op_group_prices_data = array(
                                                                    'group_price'       => $group_price,
                                                                    'optional_field_id' => $main_option_id,
                                                                    'option_id'         => $option_cost_id,
                                                                    'option_cost_id'    => $cost_id,
                                                                    'customer_group_id' => $group_id,
                                                                    'product_id'        => $product_id
                                                                 );

                                    $this->products_model->insert_table_data('optional_fields_customer_groups_prices', $op_group_prices_data);
                                }
                            }
                        }

                    }



                }

                $index++;

            }
        }
        else if(count($options_ids) != 0)
        {

          foreach($options_ids as $key => $option_id)
          {
              if($option_id != 0)
              {
              $option_data = $this->optional_fields_model->get_optional_field_row($option_id);
              // insert in optional fields data
              $main_optional_fields_data = array(
                                                  'product_id'        => $product_id,
                                                  'optional_field_id' => $option_id,
                                                  'default_value'     => isset($_POST['sec_value'][$key]) ? $_POST['sec_value'][$key] : '',
                                                  'required'          => isset( $_POST['required'][$option_id]) && ($_POST['required'][$option_id] == 1) ? $_POST['required'][$option_id] : 0
                                                  //'field_group_id'    => $group_id,
                                                );

              $this->products_model->insert_product_optional_field($main_optional_fields_data);
            }
            }
        }

    }

    private function _insert_products_optional_fields_new($product_id, $options_ids, $values, $costs, $groups_ids=array(), $sec_options_ids, $sec_values)
    {
        $index = 0;
        if(count($costs) != 0)
        {
            foreach($costs as $main_option_id=>$cost_array)
            {

                $group_id = $groups_ids[$index];

                $option_data = $this->optional_fields_model->get_optional_field_row($main_option_id);
                // insert in optional fields data
                $main_optional_fields_data = array(
                                                    'product_id'        => $product_id,
                                                    'optional_field_id' => $main_option_id,
                                                    'default_value'     => isset($values[$main_option_id]) ? $values[$main_option_id] : '',
                                                    'required'          => isset( $_POST['required'][$main_option_id]) && ($_POST['required'][$main_option_id] == 1) ? $_POST['required'][$main_option_id] : 0,
                                                    'field_group_id'    => $group_id,
                                                  );

                $this->products_model->insert_product_optional_field($main_optional_fields_data);

                // if has options
                if($option_data->free == 0)
                {

                    foreach($cost_array as $option_cost_id=>$cost_val)
                    {

                        if(!is_array($cost_val))
                        {
                            $main_cost_data = array(
                                            'product_id'        => $product_id  ,
                                            'optional_field_id' => $main_option_id   ,
                                            'option_id'         => $option_cost_id ,
                                            'cost'              => $cost_val
                                          );

                            $this->products_model->insert_products_optional_fields_costs($main_cost_data);
                        }
                        else
                        {
                            //echo 'main id '.$main_option_id.' sec id: '.$option_cost_id.'<br />';
                            // insert sec optional field
                            $sec_optional_fields_data = array(
                                                            'product_id'        => $product_id,
                                                            'optional_field_id' => $option_cost_id,
                                                            'primary_option_id' => $main_option_id,
                                                            'default_value'     => isset($sec_values[$option_cost_id]) ? $values[$option_cost_id] : '',
                                                            'required'          => isset( $_POST['required'][$main_option_id]) && ($_POST['required'][$main_option_id] == 1) ? $_POST['required'][$main_option_id] : 0,
                                                            'field_group_id'    => $group_id
                                                     );

                            $this->products_model->insert_product_optional_field($sec_optional_fields_data);

                            // insert secondery optional fields
                            foreach($cost_val as $option_key=>$sec_op_cost_val)
                            {
                                $cost_data = array(
                                                    'primary_option_id' => $main_option_id,
                                                    'product_id'        => $product_id  ,
                                                    'optional_field_id' => $option_cost_id   ,
                                                    'option_id'         => $option_key ,
                                                    'cost'              => $sec_op_cost_val
                                                  );

                                $this->products_model->insert_products_optional_fields_costs($cost_data);
                            }
                        }
                    }
                }

                $index++;

            }
        }
    }


    private function _update_exist_product_optional_fields($product_id, $options_ids, $values, $costs, $weights, $groups_ids=array(), $sec_options_ids, $sec_values)
    {

        $options_active = $this->input->post('exist_op_active', true);
        $groups_prices  = $this->input->post('exist_op_group_price', true);

        // update existing optional fields
        $index = 0;

        $this->load->library('upload');
        $gallery_path = realpath(APPPATH. '../assets/uploads/products');

        if(count($costs) != 0)
        {
            foreach($costs as $product_option_id=>$cost_array)
            {
                $product_option_data = $this->products_model->get_product_optional_field_data($product_option_id);

                // insert in optional fields data
                $main_optional_fields_data = array(
                                                    'required' => isset( $_POST['exist_required'][$product_option_id]) && ($_POST['exist_required'][$product_option_id] == 1) ? $_POST['exist_required'][$product_option_id] : 0,
                                                  );

                $this->products_model->update_product_optional_field($product_option_id, $main_optional_fields_data);

                // if has options
                if(count($cost_array) != 0)
                {

                    foreach($cost_array as $option_cost_id=>$cost_val)
                    {

                        if(!is_array($cost_val))
                        {
                            // echo "<pre>";
                            // print_r($_FILES);
                            // die();
                            $cost_data = $this->products_model->get_product_optional_field_cost_row($option_cost_id);
                            $image_name = $cost_data->image;

                           //upload option image


                            if(isset($_FILES['exist_op_image']['name'][$product_option_id][$option_cost_id]))
                            {
                                $config = array();
                                $files = $_FILES;
                                $config['upload_path']   = $gallery_path;
                                $config['allowed_types'] = 'png|jpg|jpeg|tif';
                                $config['max_size']      = '50000';

                                $_FILES['exist_op_image_data']['name']     = $files['exist_op_image']['name'][$product_option_id][$option_cost_id];
                                $_FILES['exist_op_image_data']['type']     = $files['exist_op_image']['type'][$product_option_id][$option_cost_id];
                                $_FILES['exist_op_image_data']['tmp_name'] = $files['exist_op_image']['tmp_name'][$product_option_id][$option_cost_id];
                                $_FILES['exist_op_image_data']['error']    = $files['exist_op_image']['error'][$product_option_id][$option_cost_id];
                                $_FILES['exist_op_image_data']['size']     = $files['exist_op_image']['size'][$product_option_id][$option_cost_id];

                                $this->upload->initialize($config);

                                if(!$this->upload->do_upload('exist_op_image_data'))
                                {
                                    $error = $this->upload->display_errors();
                                    $image_name = '';
                                }
                                else
                                {
                                   $file_data   = $this->upload->data();
                                   $image_name  = $file_data['file_name'];

                                   //upload on amazon
                                   $this->amazon_s3_uploads->upload_to_o3($image_name, 'products');
                                }
                            }

                            $op_active = isset($options_active[$product_option_id][$option_cost_id]) ? 1 : 0;
                            $op_weight = isset($weights[$product_option_id][$option_cost_id]) ? $weights[$product_option_id][$option_cost_id] : 0;

                            $main_cost_data = array(
                                                    'cost'   => $cost_val,
                                                    'weight' => $op_weight,
                                                    'active' => $op_active,
                                                    'image'  => $image_name
                                                  );

                            $this->products_model->update_products_optional_fields_costs($option_cost_id, $main_cost_data);

                            if(count($groups_prices) != 0)
                            {
                                foreach($groups_prices[$product_option_id][$option_cost_id] as $group_id=>$group_price)
                                {

                                    //update customer group prices
                                    $op_group_prices_data = array(
                                                                    'group_price' => $group_price
                                                                 );
                                    $prices_conds = array(
                                                            'option_cost_id'    => $option_cost_id,
                                                            'optional_field_id' => $product_option_id,
                                                            'customer_group_id' => $group_id,
                                                            'product_id'        => $product_id
                                                         );

                                    $op_group_price_exist = $this->products_model->check_op_group_price_exist($prices_conds);

                                    if($op_group_price_exist)
                                    {
                                        $this->products_model->update_table_data('optional_fields_customer_groups_prices', $prices_conds, $op_group_prices_data);
                                    }
                                    else
                                    {
                                        if($group_price != 0)
                                        {
                                            $conds = array(
                                                            'id' => $option_cost_id
                                                          );

                                            $option_cost_data = $this->products_model->get_table_data('products_optional_fields_options_costs', $conds, 'row');
                                            $group_price_data = array_merge($op_group_prices_data, $prices_conds);
                                            $group_price_data['option_id'] = $option_cost_data->option_id;

                                            $this->products_model->insert_table_data('optional_fields_customer_groups_prices', $group_price_data);
                                        }
                                    }
                                }
                            }
                        }

                    }
                }


                $index++;

            }
        }
    }

    public function sorting()
    {
        $id         = $this->input->post('id');
        $old_index  = $this->input->post('old_sort');
        $new_index  = $this->input->post('new_sort');
        $sort_state = $this->input->post('sort_state');
        $table      = 'products';

        $this->products_model->update_row_sort($id,$old_index,$new_index,$sort_state, $table);

    }

    public function remove_product_image()
    {
        $msg        = '';
        $success    = 0;

        $image_id   = intval($this->input->post('image_id'));
        $product_id = intval($this->input->post('product_id'));

        $image_data = $this->products_model->get_product_image_data($product_id, $image_id);
        $image_path = realpath(APPPATH. '../assets/uploads/products').'/'.$image_data->image;

        unlink($image_path);
        $this->products_model->delete_product_image($product_id, $image_id);

        $success = 1;
        $msg     = lang('record_deleted_successfully');


        echo json_encode(array($success, $msg));
    }

    public function get_store_cats()
    {
        $store_id = intval($this->input->post('store_id'));

        $available_cats_data = $this->stores_model->get_store_available_cats_data($store_id, $this->data['lang_id']);

        /*$cat_options = '<select name="cat_id" class="form-control select2" id="cat_id">';
        $cat_options .= '-----------------';

        foreach($available_cats_data as $cat)
        {

        }
        $cat_options .= '</select>';
        */
        $cats_array = array();
        $cats_array[Null] = '--------------';

        foreach($available_cats_data as $cat)
        {
            if($cat->parent_id == 0)
            {
                foreach($available_cats_data as $category)
                {
                    if($category->parent_id == $cat->id)
                    {
                        $cats_array["{$cat->name}"][$category->id] = $category->name;
                    }
                }
            }
        }

        echo form_dropdown('cat_id', $cats_array, 0, 'class="form-control select2" id="cat_id"');

    }

    public function get_optional_field_options($sec=0)
    {
        $option_id = intval($this->input->post('option_id', TRUE));

        $settings        = $this->global_model->get_config();
        $users_group_id  = $settings->new_user_customer_group_id;
        $customer_groups = $this->customer_groups_model->get_available_customer_groups($this->data['lang_id'], $users_group_id);

        if(isset($_POST['main_option_id']))
        {
            $main_option_id = intval($this->input->post('main_option_id', true));
        }
        else
        {
            $main_option_id = 0;
        }

        $option_data = $this->optional_fields_model->get_optional_field_row($option_id);

        $html = '';
        if($option_data->has_options == 1)
        {
            $option_options_data = $this->optional_fields_model->get_optional_field_options($option_id, $this->data['lang_id']);

            $html = '<div>';

            foreach($option_options_data as $option)
            {
                if($sec == 1)
                {
                    $active_option_data = array(
                                        'name'           => 'op_active['.$main_option_id.']['.$option_data->id.']['.$option->id.']',
                                        'class'          => 'make-switch',
                                        'data-on-color'  => 'danger',
                                        'data-off-color' => 'default',
                                        'value'          => 1,
                                        'checked'        => set_checkbox("op_active", true, true),
                                        'data-on-text'   => lang('yes'),
                                        'data-off-text'  => lang('no'),
                                        );

                    $html .= '<div class="row" style="width: 100%">';
                    $html .= '<div class="form-group row"><label class="control-label col-md-6">'.$option->field_value.'<input name=op_options['.$option->id.'] value="'.$option->field_value.'" type="hidden" /></label><div class="col-md-6"><input class="form-control" placeholder="'.lang('cost').'" type="text" name="cost['.$main_option_id.']['.$option_data->id.']['.$option->id.']" /></div></div>';
                    $html .= '<div class="form-group row"><label class="control-label col-md-6">'.lang('thumbnail').'</label><div class="col-md-6"><input class="form-control" placeholder="'.lang('thumbnail').'" type="file" name="op_image['.$main_option_id.']['.$option_data->id.']['.$option->id.']" /></div></div>';
                    $html .= '<div class="form-group"><label class="control-label col-md-6">'.lang('active').'</label><div class="col-md-6">'.form_checkbox($active_option_data).'</div></div>';
                    $html .= '</div>';
                }
                else
                {
                    $active_option_data = array(
                                        'name'           => "op_active[".$option_data->id.']['.$option->id.']',
                                        'class'          => 'make-switch',
                                        'data-on-color'  => 'danger',
                                        'data-off-color' => 'default',
                                        'value'          => 1,
                                        'checked'        => set_checkbox("op_active", true, true),
                                        'data-on-text'   => lang('yes'),
                                        'data-off-text'  => lang('no'),
                                        );


                    $html .= '<div class="row" style="width: 100%">';
                    $html .= '<div class="form-group "><label class="control-label col-md-6">'.$option->field_value.'<input name=op_options['.$option->id.'] value="'.$option->field_value.'" type="hidden" /></label><div class="col-md-6"><input class="form-control" placeholder="'.lang('cost').'" type="text" name="cost['.$option_data->id.']['.$option->id.']" /></div></div>';

                    if($option_data->has_weight == 1){
                        $html .= '<div class="form-group "><label class="control-label col-md-6">'.lang('weight').'</label><div class="col-md-6"><input class="form-control" placeholder="'.lang('weight').'" type="text" name="op_weight['.$option_data->id.']['.$option->id.']" /></div></div>';
                    }

                    $html .= '<div class="form-group "><label class="control-label col-md-6">'.lang('thumbnail').'</label><div class="col-md-6"><input class="form-control" placeholder="'.lang('thumbnail').'" type="file" name="op_image['.$option_data->id.']['.$option->id.']" /></div></div>';
                    $html .= '<div class="form-group"><label class="control-label col-md-6">'.lang('active').'</label><div class="col-md-6">'.form_checkbox($active_option_data).'</div></div>';
                    $html .= '</div>';

                    $html .= '<div class="form-group">
                                <label class="control-label col-md-3">'.lang('group_price').'</label>
                                <div class="col-md-6">';

                    foreach($customer_groups as $group)
                    {
                        $group_price_data = array('name'=>"op_group_price[$option_data->id][$option->id][$group->id]" , 'class'=>"form-control price_spinner", 'value'=> set_value("op_group_price[$option_data->id][$option->id][$group->id]"));
                        $html .= '<div class="form-group form-group-border-none" style="display: block;">
                                   <div class="col-md-4 input-inline price_group_label">
                                    <div style="color: #2977f7;">'.form_label($group->title, 'price').'</div>
                                   </div>

                                   <div class="col-md-4">
                                     <div class="input-medium input-inline">
                                       '.form_error("group_price[$option_data->id][$option->id][$group->id]").' '.
                                                form_input($group_price_data).'

                                      </div>
                                   </div>
                                </div>';
                    }

                    $html .= '</div>
                    </div><!--customer groups -->';

                }



            }

            $html .= '</div>';
        }else
        {
            $html .= '<div class="col-md-4"><input class="form-control" placeholder="'.lang('cost').'" name="cost['.$option_data->id.'][]" /></div>';
        }



        // add secondary options button
        if($sec == 0 )
        {
            //$html .= '<button style="margin-right: 200px;margin-top: 20px;" class="add_sec btn btn-sm green filter-success btn-success sec_'.$option_data->id.'" data-option_id="'.$option_data->id.'">'.lang('add').' '.lang('secondary').'</button>';
        }

        echo $html;
    }

    public function remove_option_field_image()
    {
        $option_id  = intval($this->input->post('option_id', true));

        $product_id = intval($this->input->post('product_id', true));

        $option_data = $this->products_model->get_product_option_cost_row($option_id);
        $image_path = realpath(APPPATH. '../assets/uploads/products').'/'.$option_data->image;
        // update cost row data
        $updated_data = array('image'=> '');
        $this->products_model->update_product_option_cost_row($option_id, $updated_data);

        unlink($image_path);

        $success = 1;
        $msg     = lang('record_deleted_successfully');


        echo json_encode(array($success, $msg));
    }

    public function remove_product_optional_field()
    {
        $option_id  = intval($this->input->post('option_id', true));
        $product_id = intval($this->input->post('product_id', true));

        $this->products_model->remove_product_optional_field( $product_id, $option_id);
    }

    public function seller_borrow_product()
    {
      if($this->data['store_owner'])
      {
        $product_id = intval($this->input->post('product_id', true));

        $product_data  = $this->products_model->get_row_data($product_id, $this->data['lang_id']);
        $user_stores   = $this->admin_bootstrap->get_user_available_stores(0,1);

        $user_store_id = $user_stores[0]->id;

        if(count($product_data) != 0)
        {
          //add product to user store
          $general_data  = array(
                                  'cat_id'                => $product_data->cat_id  ,
                                  'store_id'              => $user_store_id,
                                  'code'                  => $product_data->code    ,
                                  'image'                 => $product_data->image   ,
                                  'cost'                  => $product_data->cost    ,
                                  'weight'                => $product_data->weight  ,
                                  'route'                 => $product_data->route.$user_store_id ,
                                  'serials_per_country'   => $product_data->serials_per_country,
                                  'quantity_per_serial'   => $product_data->quantity_per_serial,
                                  'shipping'              => $product_data->shipping,
                                  'non_serials'           => $product_data->non_serials,
                                );

          $this->products_model->insert_products($general_data);
          $new_product_id   = $this->db->insert_id();
          $translation_data = $this->products_model->get_products_translation_result($product_id);

          //translation
          foreach($translation_data as $row)
          {
            $products_translation_data = array(
                                                'product_id'    => $new_product_id  ,
                                                'title'         => $row->title      ,
                                                'description'   => $row->description,
                                                'meta_title'    => $row->meta_title ,
                                                'lang_id'       => $row->lang_id
                                             );

            $this->products_model->insert_products_translation($products_translation_data);
          }

          //tags data
          $tags_data = $this->products_tags_model->get_products_tags_result($product_id);

          foreach($tags_data as $row)
          {
            $tag_id = $this->products_tags_model->get_tag_id($row->tag, $row->lang_id);
            $products_tags_data = array (
                                        'tag_id'     => $tag_id,
                                        'product_id' => $new_product_id
                                      );

            $this->products_tags_model->insert_tags_products($products_tags_data);
          }

          //multi images
          $product_images = $this->products_model->get_product_images($product_id);

          foreach($product_images as $row)
          {
            $prouct_image_data = array(
                                          'product_id' => $new_product_id,
                                          'image_id'   => $row->image_id
                                      );

            $this->products_model->insert_image_product($prouct_image_data);
            //upload on amazon
            $this->amazon_s3_uploads->upload_to_o3($image['file_name'], 'products');
          }

          $_SESSION['success'] = lang('success');
          $this->session->mark_as_flash('success');

          redirect($this->data['module'] . "/" . $this->data['controller'].'/edit/'.$new_product_id, 'refresh');

        }

      }


    }
/************************************************************************/
}
