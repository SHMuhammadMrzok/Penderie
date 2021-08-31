<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Zajil_api
{
    
    public function __construct()
    {   
        $this->CI = &get_instance();
        $this->CI->config->load('shipping_gateways');
    }
    
    public function create_shippment_request($from, $to, $reciever_name, $reciever_phone, $reciever_city, $reciever_address, $products_names, $total, $items_count, $order_id, $items_weight )
    {
        $url = 'http://212.76.95.85:8844/CreateHWB.aspx';
        
        $passcode       = $this->CI->config->item('passCode');
        $ship_id        = $this->CI->config->item('shipperId');
        $ship_name      = $this->CI->config->item('ShipName');
        $ship_phone     = $this->CI->config->item('ShipPhone');
        $ship_city      = $this->CI->config->item('ShipCity');
        $ship_address   = $this->CI->config->item('ShipAddress');
        $customer_num   = $this->CI->config->item('customerno');
        
        $params = array(
            'from'           => $from,
            'to'             => $to,
			'shipid'         => $ship_id,
			'shipssn'        => '0',
			'shipname'       => $ship_name,
			'shipphone'      => $ship_phone,
			'shipcity'       => $ship_city,
			'shipaddress'    => $ship_address,
			'rcvssn'         => '0',
			'rcvname'        => $reciever_name,
			'rcvphone'       => $reciever_phone,
			'rcvcity'        => $reciever_city,
			'rcvaddress'     => $reciever_address,
			'pkgdesc'        => $products_names,
			'codvalue'       => $total,
			'pcs'            => $items_count,
			'customerno'     => $customer_num,
			'weight'         => $items_weight,
			'passcode'       => $passcode,
			'refno'          => $order_id
		);
        
        $result = $this->CallAPI($url, $params);
        $result = json_decode($result);
        
        $shippment_number = $result[0][0];
        
        return $shippment_number;
        
        
        /*
        http://212.76.95.85:8844/CreateHWB.aspx?
            from=RYD&
            to=DMM&
            shipid=1223456789&
            shipssn=0&
            shipname=mohamad&
            shipphone=098765&
            shipcity=riyadh&
            shipaddress=RiyadhOlayia&
            rcvssn=0&
            rcvname=toto&
            rcvphone=0546445819&
            rcvcity=dammam&
            rcvaddress=dammamRaka%20&
            pkgdesc=IPHONE&
            codvalue=1700&
            pcs=1&
            customerno=110004&
            weight=3&
            passcode=TestPassword&
            refno=0

        */                                                          
    }
    
    public function get_shippment_info($hwb_no)
    {
        $url = "http://212.76.95.85:8844/hwbinfo.aspx";
        
        $params = array(
                         'hwb_no' => $hwb_no
                       );
        $result = $this->CallAPI($url, $params);
        $result = json_decode($result);
        
        return $result;
        
        /**
         * Result
         * [
            [
            "AWB #",
            "Event Status Code",
            "Event Status",
            "Event Status Timestamp",
            "Origin Code",
            "Origin En Name",
            "Origin AR Name",
            "Destination Code",
            "Destination En Name",
            "Destination AR Name",
            "Original Pieces number",
            "Total Received Pieces",
            "Original Pieces number - Total Received Pieces (if something short will appear)",
            "Event Pin Number which is ZJL0012",
            "Activity En",
            "Activity AR"
            ]
           ]
        */
    }
    
    public function CallAPI($url, $params)
    {
        $encoded_params = array_map("urlencode", $params);
		
        $ch = curl_init();                    // Initiate cURL
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_URL, $url. '?' . http_build_query($encoded_params));
        
        curl_setopt($ch, CURLOPT_POST, 0);  // Tell cURL you want to post something
        
        //curl_setopt($ch, CURLOPT_HEADER, true);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8', 'Accept: application/json'));

        curl_setopt($ch,CURLOPT_PORT, 8844);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the output in string format
        $output = curl_exec ($ch); // Execute
        if(!$output) {
				exit(" failed: ".curl_error($ch).'('.curl_errno($ch).')');
			}
		
        curl_close ($ch); // Close cURL handle
        
        return $output;
    
    }
    
    public function handel_status($status)
    {
        if($status == 'Closed' || $status == 'Delivered' || $status == 'Pickup')
        {
            $status = 'delivered';
        }
        else if($status == 'DELIVERY_ATTEMPT' || $status == 'In Transit' || $status == 'Out For Delivery' || $status == 'Ready For Collection' || $status == 'Redirect Shipment' || $status == 'Under Process')
        {
            $status = 'in_progress';
        }
        elseif( $status == 'RETURNED TO ORIGIN' || $status == 'Damaged' || $status == 'DELIVERY_FAILED' || $status == 'Wrong Close Attempt')
        {
            $status = 'not_delivered';
        }
        else
        {
            $status = 'invalid';
        }
        
        return $status;
    }
}    