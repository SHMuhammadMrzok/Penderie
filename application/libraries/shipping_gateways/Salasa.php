<?php if(! defined('BASEPATH')) exit('No direct script access allowed');

class Salasa
{
    public $CI;
    public $api_url;
    public $api_version;
    public $api_token_key;
    public $api_authorization_secret;
    public $integration_mode;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->config->load('shipping_gateways');
        $this->CI->load->library('Gateways');
    
        $this->integration_mode         = $this->CI->gateways->get_gateway_field_value('salasa_test_mode'); // '1'; // 1 => Test Mode , 0 => Live Mode
        $this->api_token_key            = trim($this->CI->gateways->get_gateway_field_value('salasa_api_key'));
        $this->api_authorization_secret = "Bearer ".trim($this->CI->gateways->get_gateway_field_value('salasa_api_secret'));
        $this->api_url                  = ($this->integration_mode) ? 'https://staging-salasa-api-public.eunimart.com' : 'https://salasa-api-public.eunimart.com';
        $this->api_version              = '/api/v2/';
    }


    // Public Function addShipment($refNo, $sent_date, $cName, $cntry, $cCity, $cMobile, $cAddr1, $shipType, $PCs, $cEmail, $carrValue, $carrCurr, $codAmt, $weight, $itemDesc)
    public function addShipment($post_order_id, $grouped_order, $sent_date, $cod_amount, $order_data, $order_products )
    {
        #Add Shipment 
        /**
         * 
         * OBJECT
            {
                data* {
                    receiver_address* {
                        receiver_email_id*: string -> Constraints: Min 1 chars
                        receiver_name*: string -> Constraints: Min 1 chars
                        receiver_address_1*: string -> Constraints: 1 to 50 chars
                        receiver_address_2: string -> Constraints: 1 to 50 chars
                        receiver_address_3: string -> Constraints: 1 to 50 chars
                        receiver_city*: string -> Constraints: Min 1 chars
                        receiver_state: string
                        receiver_pincode: string -> Constraints: Min 1 chars
                        receiver_company_name: string -> Constraints: Min 1 chars
                        receiver_district: string -> Constraints: Min 1 chars
                        receiver_mobile_no*: string -> Constraints: Min 1 chars
                        receiver_country*: string -> Constraints: Min 1 chars
                    }
                    billing_address* {
                        billing_email_id*: string -> Constraints: Min 1 chars
                        billing_company_name: string -> Constraints: Min 1 chars
                        billing_name*: string -> Constraints: Min 1 chars
                        billing_address_1*: string -> Constraints: 1 to 50 chars
                        billing_address_2: string -> Constraints: 1 to 50 chars
                        billing_address_3: string -> Constraints: 1 to 50 chars
                        billing_city*: string -> Constraints: Min 1 chars
                        billing_district: string -> Constraints: Min 1 chars
                        billing_state: string
                        billing_pincode: string -> Constraints: Min 1 chars
                        billing_mobile_no*: string -> Constraints: Min 1 chars
                        billing_country*: string -> Constraints: Min 1 chars
                    }
                    pickup_address: {
                        pickup_location_id: string -> Constraints: Min 1 chars
                        pickup_email_id: string -> Constraints: Min 1 chars
                        pickup_name: string -> Constraints: Min 1 chars
                        pickup_company_name: string -> Constraints: Min 1 chars
                        pickup_address_1: string
                        pickup_address_2: string
                        pickup_address_3: string
                        pickup_address_type: string -> Constraints: Min 1 chars
                        pickup_district: string -> Constraints: Min 1 chars
                        pickup_city: string -> Constraints: Min 1 chars
                        pickup_country: string -> Constraints: Min 1 chars
                        pickup_country_code: string -> Constraints: Min 1 chars
                        pickup_state: string -> Constraints: Min 1 chars
                        pickup_pincode: string -> Constraints: Min 1 chars
                        pickup_mobile_no: string -> Constraints: Min 1 chars
                    }
                    channel_id: string -> Constraints: Min 1 chars
                    store_id: string -> Constraints: Min 1 chars
                    channel_type*: enum -> Allowed: webstore┃offline
                    instructions: string -> Constraints: Min 1 chars
                    marketplace_order_id*: string -> Constraints: Min 1 chars
                    payment_details* {
                        payment_method: enum -> Allowed: cod┃prepaid
                    }
                    order_amount* {
                        price: number
                        currency_code: string -> Constraints: Min 1 chars
                        cod_amount: number -> Constraints: Min 1
                    }
                    shipping_type: enum -> Allowed: salasa_shipping┃salasa_pickup_points
                    pickup_timings: {
                        pickup_date: date
                        pickup_from_time: string -> Constraints: Min 1 chars
                        pickup_to_time: string -> Constraints: Min 1 chars
                    }
                    package_details: {
                        package_weight: number -> Constraints: Min 0.01
                        package_length: number -> Constraints: Min 0.01
                        package_width: number -> Constraints: Min 0.01
                        package_height: number -> Constraints: Min 0.01
                    }
                    tracking_details: {
                        shipping_partner_id: string -> Constraints: Min 1 chars
                    }
                    shipment_service_level: string -> Constraints: Min 1 chars
                    order_line_items* [{
                        sku_id*: string -> Constraints: Min 2 chars
                        title: string
                        item_quantity*: number -> Constraints: Min 1
                        fulfill_inventory_bucket: enum -> Allowed: in_available┃in_damaged
                        item_amount: number -> Constraints: Min 1
                        expiry_date: date
                        marketplace_item_id: string
                        instructions: string
                    }]
                    purchase_date: date-time
                }
            }
         * 
         */
        
        // echo "<br />Admin Salasa - addShipment || order_data : <br /> <pre>";
        // print_r($order_data);
        // die();

        if($order_data->shipping_type == 1){
            $shipping_address_1 = $order_data->shipping_address;
            $shipping_address_2 = $order_data->shipping_address;
            $shipping_city      = $order_data->city_name;

        }
        elseif($order_data->shipping_type == 2){ // Recieve from branch
            $shipping_address_1 = "";
            $shipping_address_2 = "";
            $shipping_city      = "";
        }
        elseif($order_data->shipping_type == 3){
            $shipping_address_1 = $order_data->order_shipping_address;
            $shipping_address_2 = $order_data->order_shipping_town. ' - ' .$order_data->orders_shipping_district;
            $shipping_city      = $order_data->order_shipping_city;
        }
        else{
            $shipping_address_1 = $order_data->shipping_address;
            $shipping_address_2 = $order_data->lat.','.$order_data->lng;
            $shipping_city      = $order_data->shipping_city;
        }

        $receiver_address = array (
            "receiver_email_id"     => $order_data->email ,
            "receiver_name"         => $order_data->first_name.' '.$order_data->last_name ,
            "receiver_address_1"    => $shipping_address_1 ,
            "receiver_address_2"    => $shipping_address_2 , // 'https://www.google.com/maps/place/'.
            "receiver_city"         => $shipping_city ,
            "receiver_mobile_no"    => $order_data->phone ,
            "receiver_country"      => $order_data->country_name 
        );
        
        $billing_address = array (
            "billing_email_id"     => $order_data->email ,
            "billing_name"         => $order_data->first_name.' '.$order_data->last_name ,
            "billing_address_1"    => $shipping_address_1 ,
            "billing_address_2"    => $shipping_address_2 , // 'https://www.google.com/maps/place/'.
            "billing_city"         => $shipping_city ,
            "billing_mobile_no"    => $order_data->phone ,
            "billing_country"      => $order_data->country_name 
        );

        $order_line_items = array();
        foreach($order_products as $product){
            $product_item = array(
                "sku_id"                    => $product->sku,
                "title"                     => $product->title,
                "item_quantity"             => (int) $product->qty,
                "fulfill_inventory_bucket"  => "in_available"
            );
    
            $order_line_items[] = $product_item;
        }

        $marketplace_order_id   = $order_data->orders_number."_".$order_data->related_orders_ids ;
        
        $order_amount           = array(
            "price"             => ($order_data->final_total == (int) $order_data->final_total ) ? (int) $order_data->final_total : (float) $order_data->final_total,
            "currency_code"     => $order_data->currency_symbol,
            "cod_amount"        => ($cod_amount == (int) $cod_amount ) ? (int) $cod_amount : (float) $cod_amount 
        );

        if($cod_amount == 0){
            unset($order_amount["cod_amount"]);
        }

        $params = array(
            "data" => array(
                'receiver_address'      => $receiver_address,
                'billing_address'       => $billing_address,
                'channel_type'          => "offline",
                'marketplace_order_id'  => $marketplace_order_id,
                'payment_details'       => array(
                    "payment_method"    => $cod_amount == 0 ? "prepaid" : "cod" // cod ┃ prepaid
                ),
                'order_amount'          => $order_amount,
                'shipping_type'         => "salasa_shipping",      //date('d / m / Y')
                'order_line_items'      => $order_line_items
                // ,'purchase_date'         => $sent_date
            )
        );

        $json_parameters = json_encode($params);
        
        // echo"<pre>";print_r($params);die();
        
        $method         = 'order_management/whole_order/create';
        $api_end_point  = $this->api_url.$this->api_version.$method;
        $result         = $this->request($api_end_point, $json_parameters , 'POST');
        
        $response_data  = json_decode($result,true);
        return $response_data;
    }


    Public Function cancelShipment($awbNo, $reason)
    {
        /**
         * 
         * OBJECT
            {
                data* {
                    channel_id: string
                    channel_type: enum -> Allowed: webstore┃offline┃pos┃marketchannel
                    store_id: string
                    order_id*: string
                    comments: string
                    reason_code*: enum -> Allowed: NO_INVENTORY┃SHIPPING_ADDRESS_UNDELIVERABLE┃CUSTOMER_EXCHANGE┃BUYER_CANCELLED┃GENERAL_ADJUSTMENT┃CARRIER_CREDIT_DECISION┃RISK_ASSESSMENT_INFORMATION_NOT_VALID┃CARRIER_COVERAGE_FAILURE┃CUSTOMER_RETURN┃MERCHANDISE_NOT_RECEIVED┃CANNOT_VERIFY_INFORMATION┃PRICING_ERROR┃REJECT_ORDER┃WEATHER_DELAY
                    account_id*: uuid
                    status_timestamp: date-time
                    reason_text: enum -> Allowed: Performance or quality not adequate┃Missing parts or accessories┃Product damaged but shipping box OK┃Both product and shipping box damaged┃Wrong item was sent┃Item defective or doesn't work
                }
            }
        * 
        */

        $marketplace_order_id = $order_data->orders_number."_".$order_data->related_orders_ids ;

        $params = array(
            "data" => array(
                'order_id'      => $awbNo,
                'reason_code'   => "CANNOT_VERIFY_INFORMATION",
                'comments'      => $reason
            )
        );


        $json_parameters = json_encode($params);
        
        //echo"<pre>";print_r($params);die();
        
        $method         = 'order_management/order/cancel';
        $api_end_point  = $this->api_url.$this->api_version.$method;
        $result         = $this->request($api_end_point, $json_parameters , 'POST');

        $response_data  = json_decode($result,true);
        return $response_data;

        /**
         * #result
         * stdClass Object ( [cancelShipmentResult] => Success Cancellation )
         *
         */
    }


    public function getTracking($awbNo)
    {
        /**
         * Get , order_id
         */
        $params = "?order_id=$awbNo";

        $method         = 'order_management/order_status_history/get';
        $api_end_point  = $this->api_url.$this->api_version.$method.$params;
        $result         = $this->request($api_end_point, "" , 'GET');

        $response_data  = json_decode($result,true);
        
        if(isset($response_data['data']) && !empty($response_data['data']))
        {
            $response = $response_data['data'][0]; // catch the last state of the gateway order statuses list
        }
        else
        {
            $response = array(
                                'response' => 0,
                                'message'  => 'Failed'
                            );
        }
        
        return $response;
    }

    function request($url, $parameters, $type)
    {
        //$post = json_encode($post);
        //echo $post;
        $headers = array(
            'Authorization: '.$this->api_authorization_secret,
            'x-api-key: '.$this->api_token_key,
            "Content-Type: application/json",
            'Accept: application/json'
        );

        $channal = curl_init($url);
        curl_setopt($channal, CURLOPT_POST, 1);
        curl_setopt($channal, CURLOPT_SAFE_UPLOAD, true);
        curl_setopt($channal, CURLOPT_CUSTOMREQUEST, $type);//'POST/GET'
        curl_setopt($channal, CURLOPT_RETURNTRANSFER, true);
        if($type == 'POST')
        {
            curl_setopt($channal, CURLOPT_POSTFIELDS,$parameters);
        }
        curl_setopt($channal, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($channal, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($channal, CURLOPT_SSL_VERIFYPEER, false);

        // echo "<br />Admin Salasa - addShipment || url : $url <br /> <pre>";
        // echo "<br />Admin Salasa - addShipment || parameters : $parameters <br /> <pre>";
        // echo "<br />Admin Salasa - addShipment || headers : <br /> <pre>";
        // print_r($headers);
        // die();

        $response = curl_exec($channal);
        curl_close($channal);

        // echo "<br />Admin Salasa - addShipment || response : <br /> <pre>";
        // print_r($response);
        // die();

        if ($channal === false) {
            return 'cURL Error #:' . $channal;
        } else {
            return $response;
        }
    }

    public function handel_status($status)
    {
        if($status == 'DATA RECEIVED' )
        {
            $status = 'delivered';
        }
        elseif($status == 'Success Cancellation ')
        {
            $status = 'canceled';
        }
        else
        {
            $status = 'invalid';
        }

        return $status;
    }

}
