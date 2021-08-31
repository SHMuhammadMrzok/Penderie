<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Product_optional_fields extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->library('api_lib');
        $this->load->library('products_lib');
        $this->load->library('shopping_cart');

        $this->load->model('general_model');
        $this->load->model('products/products_model');
        $this->load->model('optional_fields/optional_fields_model');

    }

    public function index()
    {
        $output      = array();

        //$user_id     = strip_tags($this->input->post('userId', TRUE));
        $email       = strip_tags($this->input->post('email', TRUE));
        $password    = strip_tags($this->input->post('password', TRUE));

        $lang_id     = intval($this->input->post('langId', TRUE));
        $country_id  = intval($this->input->post('countryId', TRUE));

        $product_id  = intval($this->input->post('productId', TRUE));


        if($this->ion_auth->login($email, $password))
        {
            $user_data = $this->ion_auth->user()->row();
            $this->api_lib->check_user_store_country_id($email, $password, $user_data->id, $country_id);
        }

        $product_optional_fields = $this->products_model->get_product_optional_fields($product_id, $lang_id);

        if(count($product_optional_fields) != 0)
        {
            $options_array  = array();
            $groups_array   = array();
            $output_array   = array();

            //$product_groups = $this->optional_fields_model->get_product_groups($product_id, $lang_id);

            //foreach($product_groups as $group)
            foreach($product_optional_fields as $field)
            {
                $options_data  = array();
                $group_options = $this->products_model->get_product_optional_fields($product_id, $lang_id);

                foreach($group_options as $key => $field)
                {
                    $option_options_array = array();

                    if($field->has_options == 1)
                    {
                        $option_options   = $this->optional_fields_model->get_optional_field_options($field->optional_field_id, $lang_id, $product_id, 1);

                        foreach ($option_options as $option)
                        {
                            $image = '';
                            if($option->image != '')
                            {
                                $image = base_url().'assets/uploads/products/'.$option->image;
                            }
                            $option_options_array[] = array(
                                                             'optionId'    => $option->id,
                                                             'optionLabel' => $option->field_value,
                                                             'cost'        => $option->cost         ,
                                                             'image'       => $image
                                                            //'defaultSelection' => $option->default_selection
                                                           );
                        }
                    }

                    $checked_val = 0;
                    if($key == 0)
                    {
                        $checked_val = 1;
                    }

                    $options_data[] = array(
                                            'id'            => $field->id       ,
                                            'required'      => $field->required ,
                                            'label'         => $field->label    ,
                                            'text'          => $field->text     ,
                                            //'group'         => $field->group_name       ,
                                            //'group_id'      => $field->group_id         ,
                                            'fieldTypeId'   => $field->field_type_id    ,
                                            'defaultValue'  => $field->default_value    ,
                                            'options'       => $option_options_array    ,
                                            //'secondaryOptions' => $sec_data             ,
                                            'checked'           => $checked_val
                                          );
                }

                $output_array[] = array(
                                        //'groupName'    => $group->group_name,
                                        //'groupId'      => $group->group_id,
                                        //'groupLimit'   => $group->group_limit,
                                        //'groupSort'    => $group->sort,
                                        'optionsData'  => $options_data
                                     );
            }

            $output = $options_data;


        }
        else
        {
            $message = $this->general_model->get_lang_var_translation('no_optional_fields_for_this_product', $lang_id);

            $output  = array(
                                'message'  => $message,
                                'response' => 0
                            );
        }



        $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));

    }


    public function save_user_optional_fields()
    {
        $output     = array();
        $user_id    = intval($this->input->post('userId'));
        $email      = strip_tags($this->input->post('email'));
        $password   = strip_tags($this->input->post('password'));

        $device_id  = strip_tags($this->input->post('deviceId'));
        $ip_address = $this->input->ip_address();
        $lang_id    = intval($this->input->post('langId'));

        $country_id = intval($this->input->post('countryId'));
        $product_id = intval($this->input->post('productId'));

        $this->shopping_cart->set_user_data($user_id, $device_id, $ip_address , $country_id ,$lang_id);
        $required_lang_var = $this->general_model->get_lang_var_translation('required', $lang_id);


        $optional_fields = $this->input->post('optionalFields', TRUE);

        if($product_id != 0)
        {
            $this->form_validation->set_rules('optionalFields', 'optional fields', 'required|callback_validate_optional_fields');
            $product_optional_fields = $this->products_model->get_product_optional_fields($product_id, $lang_id);

            /*foreach ($product_optional_fields as $key=>$field)
            {
                if($field->required == 1)
                {

                }
            }*/
        }

        $this->form_validation->set_rules('productId', 'product', 'required');
        $this->form_validation->set_message('required', $required_lang_var.': %s');
        $this->form_validation->set_error_delimiters(" ", " ");

        if ($this->form_validation->run() == FALSE)
        {
            $msg     = validation_errors();


            $output = array(
                               'response' => 0,
                               'message'  => ($msg)
                           );

        }
        else
        {
            $optional_fields = $this->input->post('optionalFields');
            $cart_data       = $this->shopping_cart->shopping_cart_data();
            $cart_id         = $cart_data->id;
            $user_fields     = json_decode($optional_fields, true);
            $optional_fields_cost = 0;

            foreach($user_fields as $field)
            {
                $answer    = strip_tags($field['optionLabel']);
                $option_id = strip_tags($field['optionId']);
                $qty       = intval($field['qty']);

                if($answer)
                {
                    $selected_option_data = $this->products_model->products_optional_fields_options_costs_row($product_id, $answer);
                    if(count($selected_option_data) != 0)
                    {
                        $cost = $selected_option_data->cost;
                    }
                    else
                    {
                        $cost = 0;
                    }

                    $weight = $selected_option_data->weight;

                    //check customer group
                    $conds = array(
                                    'customer_group_id' => $user_data->customer_group_id,
                                    'option_id'         => $answer,
                                    'optional_field_id' => $option_id,
                                    'product_id'        => $product_id,

                                  );


                    //get customer group cost
                    $customer_group_price = $this->products_model->get_table_data('optional_fields_customer_groups_prices', $conds, 'row');
                    $cost = $cost_array[$row];

                    if(count($customer_group_price) != 0)
                    {
                      if($customer_group_price->group_price != 0)
                      {
                          $cost = $customer_group_price->group_price;
                      }
                    }


                    $optional_fields_data = array(
                                                    'user_id'                       => $user_id,
                                                    'product_id'                    => $product_id,
                                                    'product_optional_field_id'     => $option_id,
                                                    'product_optional_field_value'  => $answer,
                                                    'shopping_cart_id'              => $cart_id,
                                                    'unix_time'                     => time(),
                                                    'cost'                          => $cost ,
                                                    'qty'                           => $qty
                                                );

                    $this->products_model->insert_user_optional_fields_data($optional_fields_data);

                    $optional_fields_cost   += $cost;
                    $optional_fields_weight += $weight;
                }
            }



            // insert_product in shopping cart

            $product_details    = $this->products_model->get_product_row_details($product_id, $lang_id, $country_id);

            $product_price_data = $this->products_lib->get_product_price_data($product_details, $country_id, $user_id, $device_id);

            $data    = array(
                               'product_id'    => $product_details->id     ,
                               'type'          => 'product'                ,
                               'cat_id'        => $product_details->cat_id ,
                               'store_id'      => $product_details->store_id,
                               'qty'           => 1                        ,
                               'name'          => $product_details->title  ,
                               'weight'        => $product_details->weight ,
                               'price'         => $product_price_data[0]   ,
                               'final_price'   => $product_price_data[1]   ,
                               'discount'      => $product_price_data[5]   ,
                               'image'         => $product_details->image  ,
                               'reward_points' => $product_details->reward_points ,
                               'optional_fields_cost' => $optional_fields_cost ,
                               'vat_value'       => $product_price_data['vat_value'],
                               'vat_percent'     => $product_price_data['vat_percent'],
                               'checked'        => 1,

                            );

            $cart_row_id = $this->shopping_cart->insert($data, 1);

            // update otional fields cart row id
            $updated_data['cart_product_id'] = $cart_row_id;
            $this->products_model->update_user_optional_fields($cart_id, $product_id, $updated_data);

            //check shopping cart coupon
            if($cart_data->coupon_discount != 0)
            {
                $this->shopping_cart->reset_cart_coupon();
            }

            $msg     = $this->general_model->get_lang_var_translation('product_added_to_cart_successfully', $lang_id);
            $success = 1;

            $output = array(
                               'response' => 1,
                               'message'  => $msg
                           );
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_UNESCAPED_UNICODE));
    }

    public function validate_optional_fields($optional_fields)
    {

        $product_id  = intval(strip_tags($this->input->post('productId')));
        $lang_id     = intval(strip_tags($this->input->post('langId')));
        $optional_fields = $_POST['optionalFields'];

        /*$optional_fields = '[{"optionId":"1","optionLabel":"1"},{"optionId":"2","optionLabel":"9"},{"optionId":"4","optionLabel":"555"},{"optionId":"5","optionLabel":"8888"},{"optionId":"6","optionLabel":"5887"},{"optionId":"7","optionLabel":"588888"},{"optionId":"8","optionLabel":"588"},{"optionId":"9","optionLabel":"880"},{"optionId":"10","optionLabel":"2000"},{"optionId":"11","optionLabel":"5455"},{"optionId":"12","optionLabel":"3000"}]';
        */
        $user_fields             = json_decode($optional_fields);
        $product_optional_fields = $this->products_model->get_product_optional_fields($product_id, $lang_id);
        $required_lang_var       = $this->general_model->get_lang_var_translation('required', $lang_id);
        $errors              = '';
        $return              = true;
        $optional_fields_ids = array();
        //echo '<pre>'; print_r($user_fields); die();
        foreach($product_optional_fields as $field)
        {
            $optional_fields_ids[$field->id] = (array)$field;
        }

        foreach ($product_optional_fields as $key=>$field)
        {
            $item_found = false;
            if(count($user_fields) != 0)
            {
                foreach ($user_fields as $user_field)
                {
                    if($user_field->optionId == $field->id)
                    {
                        if($user_field->optionLabel != '')
                        {
                            $item_found = true;
                        }

                    }
                }
            }
            if(! $item_found)
            {
                if($field->required == 1)
                {
                    $errors .= $required_lang_var.' '.$field->label."\n";
                    $return = false;
                }
            }
        }

        $this->form_validation->set_message('validate_optional_fields', $errors);
        return $return;
    }


/************************************************************************/
}
