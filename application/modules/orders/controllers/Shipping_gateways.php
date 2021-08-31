<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Shipping_gateways extends CI_Controller {

    public $data;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('orders/orders_model');
        $this->load->library('shipping_gateways/smsa');
        $this->load->library('shipping_gateways/aramex_curl');
        $this->load->library('shipping_gateways/quick_curl');
        $this->load->library('shipping_gateways/aymakan_curl');

        require(APPPATH . 'includes/global_vars.php');
    }

    public function create_shipping_request()
    {
        $order_id            = intval($this->input->post('order_id', true));
        $shipping_company_id = intval($this->input->post('shipping_company_id', true));

        $order_data = $this->orders_model->get_order_details($order_id, $this->data['lang_id']);/*get_order_main_details*/


        if($shipping_company_id == 1) //SMSA
        {
            $this->create_smsa_request();
        }
        else if($shipping_company_id == 2) // Zajil
        {
            $this->create_zajil_request();
        }
        else if($shipping_company_id == 3) // aramex
        {
          $this->create_aramex_request();
        }
        else if($shipping_company_id == 4) // Aymakan
        {
          $this->create_Aymakan_request();
        }
        else if($shipping_company_id == 5) // Quick
        {
            $this->create_Quick_request();
        }

        redirect('orders/admin_order/view_order/'.$order_id, 'refresh');
    }

    public function create_smsa_request()
    {
        $order_id            = intval($this->input->post('order_id', true));
        $shipping_company_id = intval($this->input->post('shipping_company_id', true));
        $to_city = intval($this->input->post('city', true));
        $date = strtotime($_POST['preferred_receipt_time']);

        $sent_date = date('d / m / Y' , $date);
        
        
        //echo"<pre>";print_r($_POST);die();

        $order_data = $this->orders_model->get_order_details($order_id, $this->data['lang_id']);

        if($order_data->delivered == 0 )
        {
            $products_names = '';
            $order_products = $this->orders_model->get_order_products($order_id, $this->data['lang_id']);

            foreach($order_products as $product)
            {
                $products_names .= $product->title." , ";
            }

            $ship_type = 'DLV';

            if($order_data->payment_method_id == 10)
            {
                $cod_amount = $order_data->final_total;
            }
            else
            {
                $cod_amount = 0;
            }

            $add_result = $this->smsa->addShipment($order_id, $sent_date, $order_data->first_name.' '.$order_data->last_name, $order_data->country_symbol,
                                $to_city, $order_data->phone, $order_data->shipping_address, $ship_type,
                                $order_data->items_count, $order_data->email, $order_data->final_total, $order_data->currency_symbol,
                                $cod_amount, $order_data->total_weight, $products_names );


            if(isset($add_result['response']) && ($add_result['response'] == 0))
            {
                $_SESSION['custom_error_msg'] = $add_result['message'];
                $this->session->mark_as_flash('custom_error_msg');
            }
            else
            {
                $updated_data = array(
                                        'tracking_number'   => $add_result,
                                        'delivered'         => 1
                                   );

                $this->orders_model->update_order_data($order_id, $updated_data);

                $_SESSION['success'] = lang('order_successfully_start_track');
                $this->session->mark_as_flash('success');
            }

        }
        else
        {
            // order already has a shippment_request

            $_SESSION['custom_error_msg'] = lang('order_already_has_track_request');
            $this->session->mark_as_flash('custom_error_msg');
        }
    }

    public function create_zajil_request()
    {
        $order_id            = intval($this->input->post('order_id', true));
        $shipping_company_id = intval($this->input->post('shipping_company_id', true));

        $ship_city_from_code = strip_tags($this->input->post('ship_from_branch', true));
        $ship_city_to_code   = strip_tags($this->input->post('ship_to_branch', true));

        $order_data = $this->orders_model->get_order_main_details($order_id, $this->data['lang_id']);

        if($order_data->shipping_type != 2)
        {
            if($order_data->delivered == 0 )
            {
                $products_names = '';
                $order_products = $this->orders_model->get_order_products($order_id, $this->data['lang_id']);

                foreach($order_products as $product)
                {
                    $products_names .= $product->title." , ";
                }

                $shipment_number = $this->zajil_api->create_shippment_request($ship_city_from_code, $ship_city_to_code, $order_data->shipping_name, $order_data->shipping_phone, $order_data->shipping_city, $order_data->shipping_address, $products_names, $order_data->final_total, $order_data->items_count, $order_id, $order_data->total_weight );

                $updated_data = array(
                                        'tracking_number'   => $shipment_number,
                                        'delivered'         => 1
                                   );

                $this->orders_model->update_order_data($order_id, $updated_data);

                $_SESSION['success'] = lang('order_successfully_start_track');
                $this->session->mark_as_flash('success');

            }
            else
            {
                // order already has a shippment_request

                $_SESSION['custom_error_msg'] = ('order_already_has_track_request');
                $this->session->mark_as_flash('custom_error_msg');
            }
        }
        else
        {
            // recieve from branch method can not be shipped
            $_SESSION['custom_error_msg'] = ('order_shipping_method_cant_be_shipped');
            $this->session->mark_as_flash('custom_error_msg');
        }
    }

    public function create_aramex_request()
    {
      $order_id       = intval($this->input->post('order_id', true));
      $to_city        = strip_tags($this->input->post('shipping_city', true));
      $date           = strtotime($_POST['preferred_receipt_time']);
      $ship_date      = strtotime($_POST['ship_date']);

      $order_data     = $this->orders_model->get_order_details($order_id, $this->data['lang_id']);
      $order_products = $this->orders_model->get_order_products($order_id, $this->data['lang_id']);
      $products_names = '';
      $site_phone     = json_decode($this->config->item('telephone'))[0];
      $site_mobile    = json_decode($this->config->item('mobile'))[0];

      foreach($order_products as $product)
      {
        $products_names .= $product->title.', ';
      }

      if($order_data->total_weight != 0)
      {
        $order_weight = $order_data->total_weight;
      }
      else {
        $order_weight = .1;
      }
      
      if($order_data->payment_method_id == 10) //cash on delivery
      {
        $cash_on_delivery_amount = '{"Value":'.$order_data->final_total.',"CurrencyCode":"SAR"}';//$order_data->final_total;
        $payment_type = 'P';
        $services = 'CODS';
      }
      else
      {
        $cash_on_delivery_amount = 'null';
        $payment_type = 'P';
        $services = '';
      }
      
      $request_data = array(
        'order_id'        => $order_id,
        'products_names'  => $products_names,
        'from_city'       => 'Riyadh', // from city
        'from_phone'      => $site_phone,  //site phone
        'from_cell'       => $site_mobile, //site cell
        'email'           => $this->config->item('sender_email'), // site email
        'to_city'         => $to_city, // shipped to this city
        'to_country_code' => $order_data->country_symbol, // shipped to this country
        'to_phone'        => $order_data->phone,
        'to_cell'         => $order_data->phone,
        'to_email'        => $order_data->email,
        'weight'          => $order_weight,
        'items_count'     => $order_data->items_count,
        'ship_date'       => $ship_date.'000',
        'due_date'        => $date.'000',
        'to_address'      => $order_data->address,
        'to_user_name'    => $order_data->first_name.' '.$order_data->last_name,
        'cod' => $cash_on_delivery_amount,
        'payment_type' => $payment_type,
        'services' => $services
    
      );


      $result = $this->aramex_curl->createShipment($request_data);

      if($result['response'] == 1)
      {
        $updated_data = array(
                                'tracking_number'   => $result['id'],
                                'delivered'         => 1
                           );

        $this->orders_model->update_order_data($order_id, $updated_data);

        $_SESSION['success'] = lang('order_successfully_start_track');
        $this->session->mark_as_flash('success');
      }
      else {
        $_SESSION['custom_error_msg'] = $result['error'];
        $this->session->mark_as_flash('custom_error_msg');
      }
    }

    public function aramex_print($order_id)
    {
        //$order_id   = intval($this->input->post('order_id', true));

        $order_data = $this->orders_model->get_order_details($order_id, $this->data['lang_id']);

        $track_number = $order_data->tracking_number;

        $resultData = $this->aramex_curl->print_Shipment($track_number);

        //echo"<centre>Hello</centre>";

        if($resultData['response'] == 1)
        {
            redirect($resultData['URL']);
        }
        elseif($resultData['response'] == 0)
        {
            print_r($resultData['error']);
        }
    }

    public function create_Quick_request()
    {

        $order_id   = intval($this->input->post('order_id', true));

        $order_data = $this->orders_model->get_order_details($order_id, $this->data['lang_id']);

        if($order_data->payment_method_id == 10)
        {
            $payment_method_id = 1;
        }
        else
        {
            $payment_method_id = 4;
        }

        $quick_arr = array(
                            'CustomerName'               => $order_data->first_name.' '.$order_data->last_name  ,
                            'CustomerPhoneNumber'        => $order_data->phone                                  ,
                            'PaymentMethodId'            => $payment_method_id                                  ,
                            'ShipmentContentValueSAR'    => $order_data->final_total                            ,
                            'Desciption'                 => $order_data->shipping_address                       ,
                            'Longitude'                  => $order_data->lng                                    ,
                            'Latitude'                   => $order_data->lat                                    ,
                            'CityAsString'               => $order_data->shipping_city                          ,
                            'ExternalStoreShipmentIdRef' => $order_data->order_id                               ,
                            'Currency'                   => $order_data->currency_symbol                        ,
                            'preferred_receipt_time'     => $_POST['preferred_receipt_time']                    ,
                            'delivery_time'              => $_POST['delivery_time']                             ,
                            'delivery_notes'             => $_POST['delivery_notes']
                          );

        $result =  $this->quick_curl->createShipment($quick_arr);
        //strtotime();
        if($result['response'] == 1)
        {
            $updated_data = array(
                                    'tracking_number'           => $result['id'],
                                    'delivered'                 => 1,
                                    'delivery_time'             => strtotime($_POST['delivery_time']),
                                    'preferred_receipt_time'    => strtotime($_POST['preferred_receipt_time']),
                                    'delivery_notes'            => $_POST['delivery_notes']
                               );

            $this->orders_model->update_order_data($order_id, $updated_data);

            $_SESSION['success'] = lang('order_successfully_start_track');
            $this->session->mark_as_flash('success');
        }
      else
      {
        $_SESSION['custom_error_msg'] = $result['error'];
        $this->session->mark_as_flash('custom_error_msg');
      }



    }

    public function quick_print()
    {
        $order_id   = intval($this->input->post('order_id', true));

        $order_data = $this->orders_model->get_order_details($order_id, $this->data['lang_id']);

        $track_number = $order_data->tracking_number;

        $resultData = $this->quick_curl->print_quick($track_number);

        $pdf_base64 = $resultData['0'];//"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aCA0MDEvTWF4V2lkdGggMjYxNC9Gb250V2VpZ2h0IDQwMC9YSGVpZ2h0IDI1MC9MZWFkaW5nIDQyL1N0ZW1WIDQwL0ZvbnRCQm94WyAtNTY4IC0yMTYgMjA0NiA2OTNdIC9Gb250RmlsZTIgMTUyIDAgUj4+DQplbmRvYmoNCjEwIDAgb2JqDQo8PC9UeXBlL0V4dEdTdGF0ZS9CTS9Ob3JtYWwvY2EgMT4+DQplbmRvYmoNCjExIDAgb2JqDQo8PC9UeXBlL0V4dEdTdGF0ZS9CTS9Ob3JtYWwvQ0EgMT4+DQplbmRvYmoNCjEyIDAgb2JqDQo8PC9UeXBlL0ZvbnQvU3VidHlwZS9UcnVlVHlwZS9OYW1lL0YyL0Jhc2VGb250L0FyaWFsTVQvRW5jb2RpbmcvV2luQW5zaUVuY29kaW5nL0ZvbnREZXNjcmlwdG9yIDEzIDAgUi9GaXJzdENoYXIgMzIvTGFzdENoYXIgMzIvV2lkdGhzIDE1NCAwIFI+Pg0KZW5kb2JqDQoxMyAwIG9iag0KPDwvVHlwZS9Gb250RGVzY3JpcHRvci9Gb250TmFtZS9BcmlhbE1UL0ZsYWdzIDMyL0l0YWxpY0FuZ2xlIDAvQXNjZW50IDkwNS9EZXNjZW50IC0yMTAvQ2FwSGVpZ2h0IDcyOC9BdmdXaWR0aCA0NDEvTWF4V2lkdGggMjY2NS9Gb250V2VpZ2h0IDQwMC9YSGVpZ2h0IDI1MC9MZWFkaW5nIDMzL1N0ZW1WIDQ0L0ZvbnRCQm94WyAtNjY1IC0yMTAgMjAwMCA3MjhdID4+DQplbmRvYmoNCjE0IDAgb2JqDQo8PC9UeXBlL0ZvbnQvU3VidHlwZS9UcnVlVHlwZS9OYW1lL0YzL0Jhc2VGb250L1RpbWVzTmV3Um9tYW5QU01UL0VuY29kaW5nL1dpbkFuc2lFbmNvZGluZy9Gb250RGVzY3JpcHRvciAxNSAwIFIvRmlyc3RDaGFyIDMyL0xhc3RDaGFyIDEyMC9XaWR0aHMgMTU1IDAgUj4+DQplbmRvYmoNCjE1IDAgb2JqDQo8PC9UeXBlL0ZvbnREZXNjcmlwdG9yL0ZvbnROYW1lL1RpbWVzTmV3Um9tYW5QU01UL0ZsYWdzIDMyL0l0YWxpY0FuZ2xlIDAvQXNjZW50IDg5MS9EZXNjZW50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IDI0NCAyNDQgMjgyIDM3NSA0NTAgMzk0IDQzMiA0MzJdICAxMDA4WyA1ODggNjM4IDU4OCAyNDQgMjQ0XSAgMTAxNVsgNTQ0XSAgMTAxN1sgNTQ0XSAgMTAxOVsgNTQ0IDYwMV0gXSANCmVuZG9iag0KMTU0IDAgb2JqDQpbIDI3OF0gDQplbmRvYmoNCjE1NSAwIG9iag0KWyAyNTAgMCAwIDAgMCAwIDAgMCAwIDAgMCAwIDAgMCAwIDAgNTAwIDUwMCA1MDAgMCA1MDAgNTAwIDUwMCAwIDUwMCA1MDAgMCAwIDAgMCAwIDAgMCAwIDAgMCAwIDYxMSAwIDAgMCAwIDAgMCAwIDAgNzIyIDAgMCAwIDAgMCAwIDAgMCAwIDAgMCAwIDAgMCAwIDAgMCAwIDQ0NCAwIDQ0NCA1MDAgNDQ0IDMzMyA1MDAgMCAyNzggMCAwIDI3OCA3NzggNTAwIDUwMCA1MDAgMCAzMzMgMzg5IDI3OCA1MDAgMCA3MjIgNTAwXSANCmVuZG9iag0KMTU2IDAgb2JqDQo8PC9UeXBlL01ldGFkYXRhL1N1YnR5cGUvWE1ML0xlbmd0aCAzMDY0Pj4NCnN0cmVhbQ0KPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz48eDp4bXBtZXRhIHhtbG5zOng9ImFkb2JlOm5zOm1ldGEvIiB4OnhtcHRrPSIzLjEtNzAxIj4KPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4KPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgIHhtbG5zOnBkZj0iaHR0cDovL25zLmFkb2JlLmNvbS9wZGYvMS4zLyI+CjxwZGY6UHJvZHVjZXI+TWljcm9zb2Z0wq4gV29yZCAyMDE5PC9wZGY6UHJvZHVjZXI+PC9yZGY6RGVzY3JpcHRpb24+CjxyZGY6RGVzY3JpcHRpb24gcmRmOmFib3V0PSIiICB4bWxuczpkYz0iaHR0cDovL3B1cmwub3JnL2RjL2VsZW1lbnRzLzEuMS8iPgo8ZGM6Y3JlYXRvcj48cmRmOlNlcT48cmRmOmxpPkFiZHVsbGFoIGZhcmFqPC9yZGY6bGk+PC9yZGY6U2VxPjwvZGM6Y3JlYXRvcj48L3JkZjpEZXNjcmlwdGlvbj4KPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyI+Cjx4bXA6Q3JlYXRvclRvb2w+TWljcm9zb2Z0wq4gV29yZCAyMDE5PC94bXA6Q3JlYXRvclRvb2w+PHhtcDpDcmVhdGVEYXRlPjIwMTktMTEtMDlUMTY6MDU6MzYrMDM6MDA8L3htcDpDcmVhdGVEYXRlPjx4bXA6TW9kaWZ5RGF0ZT4yMDE5LTExLTA5VDE2OjA1OjM2KzAzOjAwPC94bXA6TW9kaWZ5RGF0ZT48L3JkZjpEZXNjcmlwdGlvbj4KPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIj4KPHhtcE1NOkRvY3VtZW50SUQ+dXVpZDo5MjBFNURDNi0wNTVFLTQzOTMtOTczQy00NTVCODY1NTIwRDI8L3htcE1NOkRvY3VtZW50SUQ+PHhtcE1NOkluc3RhbmNlSUQ+dXVpZDo5MjBFNURDNi0wNTVFLTQzOTMtOTczQy00NTVCODY1NTIwRDI8L3htcE1NOkluc3RhbmNlSUQ+PC9yZGY6RGVzY3JpcHRpb24+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAo8L3JkZjpSREY+PC94OnhtcG1ldGE+PD94cGFja2V0IGVuZD0idyI/Pg0KZW5kc3RyZWFtDQplbmRvYmoNCjE1NyAwIG9iag0KPDwvRGlzcGxheURvY1RpdGxlIHRydWU+Pg0KZW5kb2JqDQoxNTggMCBvYmoNCjw8L1R5cGUvWFJlZi9TaXplIDE1OC9XWyAxIDQgMl0gL1Jvb3QgMSAwIFIvSW5mbyAxNiAwIFIvSURbPEM2NUQwRTkyNUUwNTkzNDM5NzNDNDU1Qjg2NTUyMEQyPjxDNjVEMEU5MjVFMDU5MzQzOTczQzQ1NUI4NjU1MjBEMj5dIC9GaWx0ZXIvRmxhdGVEZWNvZGUvTGVuZ3RoIDM0Mz4+DQpzdHJlYW0NCnicNdPJN9ZxFAfgH0ooyuwlol6NCA3SgKRBKRplaDA0l6QkaW7VukWnf8DJqq2TfcemZXunhX+D1/fJXdzn3M859+5uFCVqaSkp0bOjaIUfWAgkR4HcTHzFz0BeF74H8r/hT6AgBdOBwi+BooZALO5uLHqPD/iIT3iHZPxf+JxYL05bnZIwlQgrJlbDDKQjBWuwFqlYhzQUIB/rsQGZyMJGbEI2cpCLPJRjCwpRhBiKUYLNKEUZtqMSFdiKbYijBtXYgZ3Yhd3YgyocxAHsRS3qUI992I+jOIIGHEIjDqMVx3EMTWhGC87gNE6gDSdxCl3oRDvO4hw6cB4XcA1XcRGXcBlX0I8+dOM6etCLQQzgBm7iFm7jPu5hCMO4g7sYwRM8wEM8wmO8wHM8xSieYQyvMYlxvMQEXuEt3qw810z42/jfwGxj4Ndv/AvMLQbm66NoGY9vQrINCmVuZHN0cmVhbQ0KZW5kb2JqDQp4cmVmDQowIDE1OQ0KMDAwMDAwMDAxNyA2NTUzNSBmDQowMDAwMDAwMDE3IDAwMDAwIG4NCjAwMDAwMDAxNjggMDAwMDAgbg0KMDAwMDAwMDIyNCAwMDAwMCBuDQowMDAwMDAwNTEyIDAwMDAwIG4NCjAwMDAwMDQ4NzcgMDAwMDAgbg0KMDAwMDAwNTAxMSAwMDAwMCBuDQowMDAwMDA1MDM5IDAwMDAwIG4NCjAwMDAwMDUyMDAgMDAwMDAgbg0KMDAwMDAwNTI3MyAwMDAwMCBuDQowMDAwMDA1NTI3IDAwMDAwIG4NCjAwMDAwMDU1ODEgMDAwMDAgbg0KMDAwMDAwNTYzNSAwMDAwMCBuDQowMDAwMDA1Nzk4IDAwMDAwIG4NCjAwMDAwMDYwMjUgMDAwMDAgbg0KMDAwMDAwNjE5OSAwMDAwMCBuDQowMDAwMDA2NDM2IDAwMDAwIG4NCjAwMDAwMDAwMTggNjU1MzUgZg0KMDAwMDAwMDAxOSA2NTUzNSBmDQowMDAwMDAwMDIwIDY1NTM1IGYNCjAwMDAwMDAwMjEgNjU1MzUgZg0KMDAwMDAwMDAyMiA2NTUzNSBmDQowMDAwMDAwMDIzIDY1NTM1IGYNCjAwMDAwMDAwMjQgNjU1MzUgZg0KMDAwMDAwMDAyNSA2NTUzNSBmDQowMDAwMDAwMDI2IDY1NTM1IGYNCjAwMDAwMDAwMjcgNjU1MzUgZg0KMDAwMDAwMDAyOSA2NTUzNSBmDQowMDAwMDA4NTY3IDAwMDAwIG4NCjAwMDAwMDAwMzAgNjU1MzUgZg0KMDAwMDAwMDAzMSA2NTUzNSBmDQowMDAwMDAwMDMyIDY1NTM1IGYNCjAwMDAwMDAwMzMgNjU1MzUgZg0KMDAwMDAwMDAzNCA2NTUzNSBmDQowMDAwMDAwMDM1IDY1NTM1IGYNCjAwMDAwMDAwMzYgNjU1MzUgZg0KMDAwMDAwMDAzNyA2NTUzNSBmDQowMDAwMDAwMDM4IDY1NTM1IGYNCjAwMDAwMDAwMzkgNjU1MzUgZg0KMDAwMDAwMDA0MCA2NTUzNSBmDQowMDAwMDAwMDQxIDY1NTM1IGYNCjAwMDAwMDAwNDIgNjU1MzUgZg0KMDAwMDAwMDA0MyA2NTUzNSBmDQowMDAwMDAwMDQ0IDY1NTM1IGYNCjAwMDAwMDAwNDUgNjU1MzUgZg0KMDAwMDAwMDA0NiA2NTUzNSBmDQowMDAwMDAwMDQ3IDY1NTM1IGYNCjAwMDAwMDAwNDggNjU1MzUgZg0KMDAwMDAwMDA0OSA2NTUzNSBmDQowMDAwMDAwMDUwIDY1NTM1IGYNCjAwMDAwMDAwNTEgNjU1MzUgZg0KMDAwMDAwMDA1MiA2NTUzNSBmDQowMDAwMDAwMDUzIDY1NTM1IGYNCjAwMDAwMDAwNTQgNjU1MzUgZg0KMDAwMDAwMDA1NSA2NTUzNSBmDQowMDAwMDAwMDU2IDY1NTM1IGYNCjAwMDAwMDAwNTcgNjU1MzUgZg0KMDAwMDAwMDA1OCA2NTUzNSBmDQowMDAwMDAwMDU5IDY1NTM1IGYNCjAwMDAwMDAwNjAgNjU1MzUgZg0KMDAwMDAwMDA2MSA2NTUzNSBmDQowMDAwMDAwMDYyIDY1NTM1IGYNCjAwMDAwMDAwNjMgNjU1MzUgZg0KMDAwMDAwMDA2NCA2NTUzNSBmDQowMDAwMDAwMDY1IDY1NTM1IGYNCjAwMDAwMDAwNjYgNjU1MzUgZg0KMDAwMDAwMDA2NyA2NTUzNSBmDQowMDAwMDAwMDY4IDY1NTM1IGYNCjAwMDAwMDAwNjkgNjU1MzUgZg0KMDAwMDAwMDA3MCA2NTUzNSBmDQowMDAwMDAwMDcxIDY1NTM1IGYNCjAwMDAwMDAwNzIgNjU1MzUgZg0KMDAwMDAwMDA3MyA2NTUzNSBmDQowMDAwMDAwMDc0IDY1NTM1IGYNCjAwMDAwMDAwNzUgNjU1MzUgZg0KMDAwMDAwMDA3NiA2NTUzNSBmDQowMDAwMDAwMDc3IDY1NTM1IGYNCjAwMDAwMDAwNzggNjU1MzUgZg0KMDAwMDAwMDA3OSA2NTUzNSBmDQowMDAwMDAwMDgwIDY1NTM1IGYNCjAwMDAwMDAwODEgNjU1MzUgZg0KMDAwMDAwMDA4MiA2NTUzNSBmDQowMDAwMDAwMDgzIDY1NTM1IGYNCjAwMDAwMDAwODQgNjU1MzUgZg0KMDAwMDAwMDA4NSA2NTUzNSBmDQowMDAwMDAwMDg2IDY1NTM1IGYNCjAwMDAwMDAwODcgNjU1MzUgZg0KMDAwMDAwMDA4OCA2NTUzNSBmDQowMDAwMDAwMDg5IDY1NTM1IGYNCjAwMDAwMDAwOTAgNjU1MzUgZg0KMDAwMDAwMDA5MSA2NTUzNSBmDQowMDAwMDAwMDkyIDY1NTM1IGYNCjAwMDAwMDAwOTMgNjU1MzUgZg0KMDAwMDAwMDA5NCA2NTUzNSBmDQowMDAwMDAwMDk1IDY1NTM1IGYNCjAwMDAwMDAwOTYgNjU1MzUgZg0KMDAwMDAwMDA5NyA2NTUzNSBmDQowMDAwMDAwMDk4IDY1NTM1IGYNCjAwMDAwMDAwOTkgNjU1MzUgZg0KMDAwMDAwMDEwMCA2NTUzNSBmDQowMDAwMDAwMTAxIDY1NTM1IGYNCjAwMDAwMDAxMDIgNjU1MzUgZg0KMDAwMDAwMDEwMyA2NTUzNSBmDQowMDAwMDAwMTA0IDY1NTM1IGYNCjAwMDAwMDAxMDUgNjU1MzUgZg0KMDAwMDAwMDEwNiA2NTUzNSBmDQowMDAwMDAwMTA3IDY1NTM1IGYNCjAwMDAwMDAxMDggNjU1MzUgZg0KMDAwMDAwMDEwOSA2NTUzNSBmDQowMDAwMDAwMTEwIDY1NTM1IGYNCjAwMDAwMDAxMTEgNjU1MzUgZg0KMDAwMDAwMDExMiA2NTUzNSBmDQowMDAwMDAwMTEzIDY1NTM1IGYNCjAwMDAwMDAxMTQgNjU1MzUgZg0KMDAwMDAwMDExNSA2NTUzNSBmDQowMDAwMDAwMTE2IDY1NTM1IGYNCjAwMDAwMDAxMTcgNjU1MzUgZg0KMDAwMDAwMDExOCA2NTUzNSBmDQowMDAwMDAwMTE5IDY1NTM1IGYNCjAwMDAwMDAxMjAgNjU1MzUgZg0KMDAwMDAwMDEyMSA2NTUzNSBmDQowMDAwMDAwMTIyIDY1NTM1IGYNCjAwMDAwMDAxMjMgNjU1MzUgZg0KMDAwMDAwMDEyNCA2NTUzNSBmDQowMDAwMDAwMTI1IDY1NTM1IGYNCjAwMDAwMDAxMjYgNjU1MzUgZg0KMDAwMDAwMDEyNyA2NTUzNSBmDQowMDAwMDAwMTI4IDY1NTM1IGYNCjAwMDAwMDAxMjkgNjU1MzUgZg0KMDAwMDAwMDEzMCA2NTUzNSBmDQowMDAwMDAwMTMxIDY1NTM1IGYNCjAwMDAwMDAxMzIgNjU1MzUgZg0KMDAwMDAwMDEzMyA2NTUzNSBmDQowMDAwMDAwMTM0IDY1NTM1IGYNCjAwMDAwMDAxMzUgNjU1MzUgZg0KMDAwMDAwMDEzNiA2NTUzNSBmDQowMDAwMDAwMTM3IDY1NTM1IGYNCjAwMDAwMDAxMzggNjU1MzUgZg0KMDAwMDAwMDEzOSA2NTUzNSBmDQowMDAwMDAwMTQwIDY1NTM1IGYNCjAwMDAwMDAxNDEgNjU1MzUgZg0KMDAwMDAwMDE0MiA2NTUzNSBmDQowMDAwMDAwMTQzIDY1NTM1IGYNCjAwMDAwMDAxNDQgNjU1MzUgZg0KMDAwMDAwMDE0NSA2NTUzNSBmDQowMDAwMDAwMTQ2IDY1NTM1IGYNCjAwMDAwMDAxNDcgNjU1MzUgZg0KMDAwMDAwMDE0OCA2NTUzNSBmDQowMDAwMDAwMTQ5IDY1NTM1IGYNCjAwMDAwMDAxNTAgNjU1MzUgZg0KMDAwMDAwMDAwMCA2NTUzNSBmDQowMDAwMDA4NjE3IDAwMDAwIG4NCjAwMDAwMDk0MjkgMDAwMDAgbg0KMDAwMDA0NzE2MSAwMDAwMCBuDQowMDAwMDQ3ODE0IDAwMDAwIG4NCjAwMDAwNDc4NDIgMDAwMDAgbg0KMDAwMDA0ODEwMiAwMDAwMCBuDQowMDAwMDUxMjUwIDAwMDAwIG4NCjAwMDAwNTEyOTYgMDAwMDAgbg0KdHJhaWxlcg0KPDwvU2l6ZSAxNTkvUm9vdCAxIDAgUi9JbmZvIDE2IDAgUi9JRFs8QzY1RDBFOTI1RTA1OTM0Mzk3M0M0NTVCODY1NTIwRDI+PEM2NUQwRTkyNUUwNTkzNDM5NzNDNDU1Qjg2NTUyMEQyPl0gPj4NCnN0YXJ0eHJlZg0KNTE4NDINCiUlRU9GDQp4cmVmDQowIDANCnRyYWlsZXINCjw8L1NpemUgMTU5L1Jvb3QgMSAwIFIvSW5mbyAxNiAwIFIvSURbPEM2NUQwRTkyNUUwNTkzNDM5NzNDNDU1Qjg2NTUyMEQyPjxDNjVEMEU5MjVFMDU5MzQzOTczQzQ1NUI4NjU1MjBEMj5dIC9QcmV2IDUxODQyL1hSZWZTdG0gNTEyOTY+Pg0Kc3RhcnR4cmVmDQo1NTE4MQ0KJSVFT0Y=";


        $decoded = base64_decode($pdf_base64);

        $file = 'quick_'.$track_number.'.pdf';
        file_put_contents($file, $decoded);

        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($file).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            readfile($file);
            exit;
        }


    }

    public function create_Aymakan_request()
    {

        $order_id = intval($this->input->post('order_id', true));

        $old_date = strtotime($_POST['preferred_receipt_time']);

        $new_date = date('Y-m-d H:i:s' , $old_date);

        $order_data = $this->orders_model->get_order_details($order_id, $this->data['lang_id']);


        if($order_data->payment_method_id == 10)
        {
            $is_cod = 1;
            $cod_amount = $order_data->final_total;
        }
        else
        {
            $is_cod = 0;
            $cod_amount = 0;
        }

        $address = json_decode($this->config->item('address'));
        $collection_address = $address['0'];

        $mopile = json_decode($this->config->item('mobile'));
        $phone = $mopile['0'];

        $Aymakan_arr = array(
                            'order_id'                  => $order_id                                           ,
                            'requested_by'              => $order_data->first_name.' '.$order_data->last_name  ,
                            'declared_value'            => $order_data->final_total                            ,
                            'declared_value_currency'   => $order_data->currency_symbol                        ,
                            'is_cod'                    => $is_cod                                             ,
                            'cod_amount'                => $cod_amount                                         ,
                            'currency'                  => $order_data->currency_symbol                        ,
                            'delivery_name'             => $order_data->first_name.' '.$order_data->last_name  ,
                            'delivery_email'            => $order_data->email                                  ,
                            'delivery_city'             => $order_data->city                                   ,
                            'delivery_address'          => $order_data->address                                ,
                            'delivery_region'           => $order_data->city                                   ,
                            'delivery_country'          => $order_data->country_symbol                         ,
                            'delivery_phone'            => $order_data->phone                                  ,
                            'collection_name'           => $this->config->item('site_name')                    ,
                            'collection_email'          => $this->config->item('sender_email')                 ,
                            'collection_address'        => $collection_address                                 ,
                            'collection_phone'          => $phone                                              ,
                            'collection_description'    => $this->config->item('description')                  ,
                            'pickup_date'               => $new_date                                           ,
                            'weight'                    => $order_data->total_weight                           ,
                            'pieces'                    => $order_data->items_count                            ,
                            'items_count'               => $order_data->items_count
                          );


        $result =  $this->aymakan_curl->createShipment($Aymakan_arr);

        //echo"<pre>";print_r($result);die();
        //strtotime();
        if($result['response'] == 1)
        {
            $updated_data = array(
                                    'tracking_number'           => $result['sucsess']['tracking_number'],
                                    'delivered'                 => 1
                               );

            $this->orders_model->update_order_data($order_id, $updated_data);

            $_SESSION['success'] = lang('order_successfully_start_track');
            $this->session->mark_as_flash('success');
        }
      else
      {
        $_SESSION['custom_error_msg'] = $result['error'];
        $this->session->mark_as_flash('custom_error_msg');
      }



    }

    public function get_shipping_info()
    {
        $order_id = intval($this->input->post('order_id', true));
        $admin    = isset($_POST['admin']) ? 1 : 0;

        $order_data = $this->orders_model->get_order_data($order_id, $this->data['lang_id']);



        if($order_data->delivered == 1 && $order_data->tracking_number != 0)
        {

            if($order_data->shipping_company_id == 1)  //SMSA
            {
                $info = $this->smsa->getTracking($order_data->tracking_number);

                $status = $this->smsa->handel_status($info->Activity);
                $status_id = $this->_get_status_id($status);

                // insert in shipping log
                $log_data = array(
                                    'order_id'           => $order_id                       ,
                                    'shipping_method_id' => $order_data->shipping_company_id,
                                    'status_id'          => $status_id                      ,
                                    'admin'              => $admin                          ,
                                    'AWB_number'         => $order_data->tracking_number    ,
                                    'feed_back_text'     => json_encode($info),
                                    'unix_time'          => time()
                                 );

                $this->orders_model->insert_shipping_log($log_data);
            }
            elseif($order_data->shipping_company_id == 2)  //ZAJIL
            {
                $info   = $this->zajil_api->get_shippment_info($order_data->tracking_number);

                $status = $this->zajil_api->handel_status($info[0][2]);
                $status_id = $this->_get_status_id($status);

                // insert in shipping log

                $log_data = array(
                                    'order_id'           => $order_id    ,
                                    'shipping_method_id' => $order_data->shipping_company_id,
                                    'status_id'          => $status_id   ,
                                    'admin'              => $admin       ,
                                    'AWB_number'         => $info[0][0]  ,
                                    'feed_back_text'     => json_encode($info),
                                    'unix_time'          => time()
                                 );

                $this->orders_model->insert_shipping_log($log_data);
            }
            else if($order_data->shipping_company_id == 5)//Quick
            {
                $order_data = $this->orders_model->get_order_details($order_id, $this->data['lang_id']);

                $tracking_number = $order_data->tracking_number;

                $info =  $this->quick_curl->track_shipment($tracking_number);


                $log_data = array(
                                    'order_id'           => $order_id                       ,
                                    'shipping_method_id' => $order_data->shipping_company_id,
                                    'status_id'          => ''                              ,
                                    'admin'              => 1                               ,
                                    'AWB_number'         => $order_data->tracking_number    ,
                                    'feed_back_text'     => json_encode($info)            ,
                                    'unix_time'          => time()
                                 );

                $this->orders_model->insert_shipping_log($log_data);

            }
            else if($order_data->shipping_company_id == 4)//Aymakan
            {
                $order_data = $this->orders_model->get_order_details($order_id, $this->data['lang_id']);

                $tracking_number = $order_data->tracking_number;

                $info =  $this->aymakan_curl->track_shipment($tracking_number);

                $log_data = array(
                                    'order_id'           => $order_id                                           ,
                                    'shipping_method_id' => $order_data->shipping_company_id                    ,
                                    'status_id'          => ''                                                  ,
                                    'admin'              => 1                                                   ,
                                    'AWB_number'         => $order_data->tracking_number                        ,
                                    'feed_back_text'     => json_encode($info['sucsess']['tracking_info']['0']) ,
                                    'unix_time'          => time()
                                 );

                $this->orders_model->insert_shipping_log($log_data);

            }
        }
        else
        {
            // No shipping for this order
            $_SESSION['custom_error_msg'] = ('no_shipping_for_this_order');
            $this->session->mark_as_flash('custom_error_msg');
        }

        if($admin == 1)
        {
            redirect('orders/admin_order/view_order/'.$order_id.'#shipping_data', 'refresh');
        }
        else
        {
            redirect('orders/order/view_order_details/'.$order_id.'#shipping_data', 'refresh');
        }

    }

    public function cancel_shippment()
    {
        $shipping_company_id = intval($this->input->post('shipping_company_id', true));

        if($shipping_company_id == 1) // smsa
        {
            $this->cancel_smsa_shipment();
        }
        elseif($shipping_company_id == 3) // aramex
        {
            $this->cancel_aramex_shipment();
        }
        elseif($shipping_company_id == 5) // quick
        {
            $this->cancel_quick_shipment();
        }
    }

    public function cancel_smsa_shipment()
    {
        $order_id = intval($this->input->post('order_id', true));
        $reason   = strip_tags($this->input->post('cancel_reason', true));

        $order_data = $this->orders_model->get_order_data($order_id, $this->data['lang_id']);

        $result = $this->smsa->cancelShipment($order_data->tracking_number, $reason);

        $status = $this->smsa->handel_status($result);
        $status_id = $this->_get_status_id($status);

        // insert in shipping log
        $log_data = array(
                            'order_id'           => $order_id                       ,
                            'shipping_method_id' => $order_data->shipping_company_id,
                            'status_id'          => $status_id                      ,
                            'admin'              => 1                          ,
                            'AWB_number'         => $order_data->tracking_number    ,
                            'feed_back_text'     => json_encode(array('reason'=>$result)),
                            'unix_time'          => time()
                         );

        $this->orders_model->insert_shipping_log($log_data);

        redirect('orders/admin_order/view_order/'.$order_id, 'refresh');

    }

    public function cancel_aramex_shipment()
    {
        $order_id = intval($this->input->post('order_id', true));
        $comments = strip_tags($this->input->post('cancel_reason', true));

        //echo $order_id.'<br />---<br />'.$comments;die();

        $order_data = $this->orders_model->get_order_data($order_id, $this->data['lang_id']);

        $track_number = $order_data->tracking_number;

        //echo $track_number;die();

        $resultData = $this->aramex_curl->cancel_Shipment($track_number, $comments);


        // insert in shipping log
      if($resultData['response'] == 1)
      {
        $log_data = array(
                            'order_id'           => $order_id                       ,
                            'shipping_method_id' => $order_data->shipping_company_id,
                            'status_id'          => 5                      ,
                            'admin'              => 1                          ,
                            'AWB_number'         => $order_data->tracking_number    ,
                            'feed_back_text'     => json_encode($resultData),
                            'unix_time'          => time()
                         );
        $this->orders_model->insert_shipping_log($log_data);
      }
      else
      {
        $_SESSION['custom_error_msg'] = $resultData['error'];
        $this->session->mark_as_flash('custom_error_msg');
      }

        redirect('orders/admin_order/view_order/'.$order_id, 'refresh');

    }

    public function cancel_quick_shipment()
    {
        $order_id = intval($this->input->post('order_id', true));

        $order_data = $this->orders_model->get_order_data($order_id, $this->data['lang_id']);

        $track_number = $order_data->tracking_number;

        $resultData = $this->quick_curl->Delete_quick_shippment($track_number);


        // insert in shipping log
        $log_data = array(
                            'order_id'           => $order_id                       ,
                            'shipping_method_id' => $order_data->shipping_company_id,
                            'status_id'          => 5                      ,
                            'admin'              => 1                          ,
                            'AWB_number'         => $order_data->tracking_number    ,
                            'feed_back_text'     => json_encode($resultData),
                            'unix_time'          => time()
                         );
        $this->orders_model->insert_shipping_log($log_data);

        redirect('orders/admin_order/view_order/'.$order_id, 'refresh');

    }

    private function _get_status_id($status)
    {
        $status_id = $this->orders_model->get_shipping_status_id($status);
        return $status_id;
    }
    /*********************************************************/
}
/* End of file shipping_gateways.php */
/* Location: ./application/modules/orders/controllers/shipping_gateways.php */
