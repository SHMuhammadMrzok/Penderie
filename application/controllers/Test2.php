<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test2 extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        //echo '<pre>';print_r($_SERVER['REMOTE_ADDR']); die();
        //$this->load->model('home_model');
        // $this->load->model('products/products_model');

        //$this->load->library('shipping_gateways/smsa');

        //$this->load->library('shipping_gateways/aramex_curl');
        //$this->load->library('shipping_gateways/aymakan_curl');

        //require(APPPATH . 'includes/front_end_global.php');
        //$this->session->set_userdata('site_redir', current_url());
    }

    public function test_not()
    {//echo dirname(__FILE__);die();
        $this->load->library('notifications');
        $this->notifications->send_emails_notifications('test', 'msg from penederi' , array('mariam@shourasoft.com'));

    }
    
    public function canJump() {
        $nums = array(2,5,0,0);
        $last_index = count($nums) - 1;
        $postion= 0;
        
        for($i=0;$i<count($nums)-1; $i++)
        {
            if($i>= $postion && $postion != 0 && $nums[$postion] != 0)
            {
                $current_index = $nums[$postion];
                $postion = $nums[$i] + $i;
                
                echo 'i:'.$i.'<br />';
                echo 'val:'.$nums[$i].'<br />';
                echo 'postion:'.$postion.'<br />'; 
            }
            else if($postion == 0 && $i==0)
            {
                $current_index = $nums[$postion];
                $postion = $nums[$i];
                
                echo 'first index: '.$current_index.'<br />';
                echo 'first pos: '.$postion.'<br />';
            }
            
        }
        
        if($postion >= $last_index)
        {
            echo 'Output: true';
        }
        else
        {
            echo 'Output: false';
        }
    }
    
    public function plusOne($digits=array()) {
        $digits = array(8,9,9);
        
        $arr = array_reverse($digits);
        $arr[0] = $arr[0] +1;
        if($arr[0] > 9)
        {
            $arr[0] = 0;
            
            for($i=1;$i<=count($arr);$i++)
            {
                $arr[$i] = $arr[$i] + 1;
                if($arr[$i] <= 9)
                {
                    break;
                }
                else
                {
                    $arr[$i] = 0;
                }
                
                if($i == count($arr))
                {
                    if($arr[$i] > 9)
                    {
                        $arr[] = 0;
                    }
                }
            }
        }
        
        $result = array_reverse($arr);
        print_r($result); die();
        return $result;
    }
    
    public function addTwoNumbers()
    {
        $l1 = (object)array(
            'val' => 2,
            'next' => array(
                'val' => 4,
                'next' => array(
                    'val' => 3,
                    'next' => array()
                )
            )
        );
        
        $l2 = (object)array(
            'val' => 5,
            'next' => array(
                'val' => 6,
                'next' => array(
                    'val' => 4,
                    'next' => array()
                )
            )
        );
        
        
    $first_num  = $this->my_print($l1);
    $second_num = $this->my_print($l2);
    echo $first_num.'+' .$second_num;
    $result = $first_num + $second_num;
    
    $split = str_split($result, 1);
    print_r($split); 
        
    }
    
    function my_print($array) {
        
        $output = $array->val;
        
        foreach ($array as $value) {
            if (!is_null($value->next)) {
                $output .= $this->my_print($value);
            } else {
                $output .= $value->val;
            }
            
        }
        return $output;
    }
    
    public function addTwoNumbers2($l1, $l2)
    {
        $a = new ListNode(0);
        $b = $a;
        $carry = 0;
        while($l1 != NULL || $l2!= NULL || $carry !== 0){
            $sum = ($l1 ? $l1->val : 0)+ ($l2 ? $l2->val : 0) + $carry;
            
            if($sum > 9){
                $carry = intval($sum / 10);
                $remainder = $sum % 10;
                $final = $remainder;
            }else{
                $carry = 0;
                $final = $sum;
            }
            
            $b = $b->next = new ListNode($final);
            $l1 = $l1 ->next;
            $l2 = $l2 ->next;
        }
        return $a->next;
    }
    
    public function check_next($items)
    {
         foreach($l1 as $items)
         {
            if(isset($items['next']) && count($items['next']) != 0)
            {
                return $items['next']['val'];
            }
            else
            {
                return false;
            }
         }
    }
    
    
    function removeElement(&$nums=array(1,2,2,1), $val=2) {
        
        for($i=0;$i<=count($nums);$i++)
        {
            if($nums[$i] == $val)
            {
                unset($nums[$i]);
            }
        }
        print_r($nums); die();
        return $nums;
    }
    
    function moveZeroes() {
        $nums=array(0,1,5,3,0);
        $nums = sort($nums);
        print_r($nums); die();
        $lngth = count($nums);
        for($i=0;$i<$lngth;$i++)
        {
            if($nums[$i] == 0)
            {
                unset($nums[$i]);
                $nums[] = 0;
            }
        }
        
        print_r($nums); die();
        
    }


    public function fb_login()
    {
        $this->load->view('fb_login');
    }

    public function twitter_login()
    {
        $this->load->view('twitter_login');
    }

    public function test_enc()
    {
        $this->load->library('encryption');
        $this->config->load('encryption_keys');

        $text = $_GET['text'];//hello world';
        $secret_key  = $_GET['key'];//00000key';//$this->config->item('new_encryption_key');
        $secret_iv   = $_GET['iv'];//('enc_iv');

        $enc_text  = $this->encryption->encrypt($text, $secret_key, $secret_iv);
        echo $enc_text;
    }

    public function test_sms()
    {
      $this->load->library('notifications');

      $msg = 'كود التفعيل 1111';
      $mobile = '555995653';
      echo $this->notifications->send_sms($msg, $mobile);
      //http://api.unifonic.com/wrapper/sendSMS.php?userid=[username]&password=[password]&msg=[english_body]&sender=[SenderID]&to=[Destination]
    }

    public function moyasar()
    {
        $this->load->view('moyasar');
    }

    public function resize_products_images()
    {
      $this->load->library('uploaded_images');

      $products = $this->db->get('products');
      $products = $products->result();

      foreach ($products as $product) {
        //$this->uploaded_images->resize_image($product->image, 3);
        $this->uploaded_images->resize_image($product->image, 4);
      }
    }

    public function upload_s3()
    {
      echo realpath(APPPATH. '../assets/uploads/');
      die();
      $this->load->view('upload');
    }

    public function create_shippment_aramex()
    {
      $result = $this->aramex_curl->createShipment();

      echo '<pre>'; print_r($result);
    }

    public function shipping_cities()
    {
      $cities = array(
        "Abha",
        "Abqaiq",
        "Abu Areish",
        "Afif",
        "Aflaj",
        "Ahad Masarha ",
        "Ahad Rufaidah",
        "Ain Dar",
        "Al Bada",
        "Al Dalemya",
        "Al Hassa",
        "Al Mada",
        "Al Moya",
        "Al-Jsh",
        "Alghat",
        "Alhada",
        "Alrass",
        "Amaq",
        "Anak",
        "Aqiq",
        "Arar",
        "Artawiah",
        "Asfan",
        "Ash Shuqaiq",
        "Assiyah",
        "Atawleh",
        "Awamiah",
        "Ayn Fuhayd",
        "Badaya",
        "Bader",
        "Baha",
        "Bahara",
        "Bahrat Al Moujoud",
        "Balahmar",
        "Balasmar",
        "Bareq",
        "Batha",
        "Biljurashi",
        "Birk",
        "Bish ",
        "Bisha",
        "Bukeiriah",
        "Buraidah",
        "Daelim",
        "Damad",
        "Dammam",
        "Darb",
        "Dawadmi",
        "Daws",
        "Deraab",
        "Dere'Iyeh",
        "Dhahran",
        "Dhahran Al Janoob",
        "Dhalm",
        "Dhurma",
        "Domat Al Jandal",
        "Duba",
        "Farasan",
        "Gilwa",
        "Gizan",
        "Hadeethah",
        "Hafer Al Batin",
        "Hail",
        "Halat Ammar",
        "Haqil",
        "Harad",
        "Hareeq",
        "Harjah",
        "Hawea/Taif",
        "Haweyah",
        "Hawtat Bani Tamim",
        "Hinakeya",
        "Hofuf",
        "Horaimal",
        "Hotat Sudair",
        "Ja'Araneh",
        "Jadid",
        "Jafar",
        "Jalajel",
        "Jeddah",
        "Jouf",
        "Jubail",
        'Jumum',
        'Kara',
        'Kara A',
        "Karboos",
        'Khafji',
        "Khaibar",
        'Khamaseen',
        "Khamis Mushait",
        "Kharj",
        "Khasawyah",
        'Khobar',
        "Khodaria",
        'Khulais',
        "Khurma",
        'Laith',
        "Madinah",
        "Mahad Al Dahab",
        "Majarda",
        'Majma',
        'Makkah',
        'Mandak',
        'Mastura',
        'Midinhab',
        'Mikhwa',
        'Mnefah',
        'Mohayel Aseer',
        'Molejah',
        'Mrat',
        "Mubaraz",
        "Mulaija",
        "Muthaleif",
        "Muzahmiah",
        "Muzneb",
        'Nabiya',
        "Najran",
        "Namas",
        "Nanyah",
        "Nimra",
        "Noweirieh",
        "Nwariah",
        "Ojam",
        "Onaiza",
        'Othmanyah',
        "Oula",
        "Oyaynah",
        "Qahmah",
        "Qarah  ",
        "Qariya Al Olaya",
        "Qasab"           ,
        "Qassim",
        'Qatif',
        "Qaysoomah",
        "Qunfudah",
        "Qurayat",
        "Quwei'Ieh",
        "Rabigh",
        "Rafha",
        "Rahima",
        "Rania",
        "Ras Al Kheir",
        "Ras Tanura",
        "Rejal Alma'A",
        "Remah",
        "Riyadh",
        "Riyadh Al Khabra",
        "Rowdat Sodair",
        "Rwaydah",
        "Sabt El Alaya",
        "Sabya",
        "Safanyah",
        "Safwa",
        "Sahna",
        "Sajir",
        "Sakaka",
        "Salbookh",
        "Salwa",
        "Samtah",
        "Sarar",
        "Sarat Obeida",
        "Seihat",
        'Shaqra',
        "Sharourah",
        "Shefa'A",
        "Shoaiba",
        "Shraie'E",
        "Shumeisi",
        "Snabs",
        "Subheka",
        "Sulaiyl",
        "Tabrjal",
        "Tabuk",
        "Taif",
        "Tanda",
        "Tanjeeb",
        "Tanuma",
        "Tarut",
        "Tatleeth",
        "Tayma    ",
        "Tebrak",
        "Thabya",
        "Thadek",
        "Thumair",
        "Thuqba",
        "THUWAL",
        "Towal",
        "Turaib",
        "Turaif",
        "Turba",
        "Udhaliyah",
        "Um Aljamajim",
        "Umluj",
        "Uqlat Al Suqur",
        "Uyun",
        "Wadeien",
        "Wadi Bin Hasbal",
        "Wadi El Dwaser",
        "Wadi Fatmah",
        "Wajeh (Al Wajh)",
        "Yanbu",
        "Yanbu Al Baher",
        "Zahban ",
        "Zulfi");
      foreach($cities as $city)
      {
        $insert_data = array(
          'city'  => $city,
          'country_id' => 191,
          'shipping_company' => 'smsa'
        );

        $this->db->insert('shipping_cities', $insert_data);
      }
    }

    public function track_shippment($tracking_id)
    {
      $response = $this->aramex_curl->getTracking($tracking_id);
      print_r($response);
    }

    public function payfort()
    {
      $this->load->view('payfort');
    }

    public function Aymakan()
    {
        $this->aymakan_curl->createShipment();

        //echo"<center><h1>Hello</h1><center>";
    }

    public function track_aymakan_shipment()
    {
        $this->aymakan_curl->track_shipment();
        //echo"Hello";die();
    }

    public function naqel()
    {
      //echo date('Y/m/d H:i'); die();
        $soapUrl = "https://infotrack.naqelexpress.com/NaqelAPIServices/NaqelAPIDemo/9.0/XMLShippingService.asmx"; // asmx URL of WSDL

        /*
        //Billing types:
        1 => On Account - An invoice will be sent in the end of the month to your company contain a list of waybills which has been manifested on Account.
        2 => Cash - Sender will pay the money cash.
        5 => COD - Cash On Delivery Consignee will pay the money when receive the shipment.
        */
        // xml post structure
        $xml_post_string = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <CreateBooking xmlns="http://tempuri.org/">
      <_BookingShipmentDetail>
        <ClientInfo>
          <ClientAddress>
            <PhoneNumber>123456</PhoneNumber>
            <POBox>552v</POBox>
            <ZipCode>1221</ZipCode>
            <Fax>122522000</Fax>
            <FirstAddress>my address</FirstAddress>
            <Location>location to my location</Location>
            <CountryCode>SA</CountryCode>
            <CityCode>RUH</CityCode>
          </ClientAddress>
          <ClientContact>
            <Name>mariam</Name>
            <Email>mariam@shourasoft.com</Email>
            <PhoneNumber>1234566654</PhoneNumber>
            <MobileNo>321113</MobileNo>
          </ClientContact>
          <ClientID>9019201</ClientID>
          <Password>R912c01!p</Password>
          <Version>9.0</Version>
        </ClientInfo>
        <BillingType>1</BillingType>
        <PickUpReqDateTime>9:00</PickUpReqDateTime>
        <PicesCount>3</PicesCount>
        <Weight>2.5</Weight>
        <PickUpPoint>address to pick up from</PickUpPoint>
        <SpecialInstruction>some notes here</SpecialInstruction>
        <OriginStationID>501</OriginStationID>
        <DestinationStationID>502</DestinationStationID>
        <OfficeUpTo>15:00</OfficeUpTo>
        <ContactPerson>mariam</ContactPerson>
        <ContactNumber>5567900</ContactNumber>
        <LoadTypeID>5</LoadTypeID>
      </_BookingShipmentDetail>
    </CreateBooking>
  </soap:Body>
</soap:Envelope>';

        $headers = array(
                        "Content-type: text/xml;charset=\"utf-8\"",
                        "Accept: text/xml",
                        "Cache-Control: no-cache",
                        "Pragma: no-cache",
                        //"SOAPAction: $soap_action",

                        "Content-length: ".strlen($xml_post_string),
            						"POST: /NaqelAPIServices/NaqelAPIDemo/9.0/XMLShippingService.asmx HTTP/1.1",
            						"Accept-Encoding: gzip,deflate",
            						"Host: infotrack.naqelexpress.com",
            						"Connection: Keep-Alive",
            						"User-Agent: Apache-HttpClient/4.1.1 (java 1.5)"
					);


        $url = $soapUrl;

		  // PHP cURL  for https connection with auth
  		$ch = curl_init();
  		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
  		curl_setopt($ch, CURLOPT_URL, $url);
  		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
  		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
  		curl_setopt($ch, CURLOPT_POST, true);
  		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
	    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

  		// converting
  		$response = curl_exec($ch);

        if(curl_errno($ch))
        {
    		//return curl_error($ch);
    		print "<br> Error: " .curl_error($ch);
    	    die();
            curl_close($ch);
    	}
        else
        {
	    	curl_close($ch);

        echo '<pre>';print_r($response); die();
      //$response_array = $this->get_response($response);

      return $response;


    	}

	}
    
    public function aramex_print()
    {
        $track_number = 45751541776;
        
        $this->aramex_curl->print_Shipment($track_number);
    }
    
    public function cancel_print()
    {
        $track_number = 45751933102;
        $comments = "Iam Sorry But The Customer Dosenot Need The Product Beacuse He Died";
        
        $this->aramex_curl->cancel_Shipment($track_number, $comments);
    }
    
    public function empty_tables()
    {
        $limit = 300;
        $ids_array = array();
        
        for($i=1; $i++;$i<90)
        {
            $offset= ($i-1)*$limit;
        
            $this->db->join('products', 'products_images.product_id=products.id', 'left');
            $result = $this->db->get('products_images', $limit, $offset);
            
            foreach($result->result() as $row)
            {
                if(is_null($row->id))
                {
                    echo $row->product_id.'<br />';
                    $ids_array[] =  $row->product_id;
                    $this->db->where('products_images.product_id', $row->product_id);
                    $this->db->delete('products_images');
                }
            }
        
        
        }
        
        print_r($ids_array); die();
    }
	
	public function update_products_route()
	{
		$updated_data = array('product_route'=>'products/products/product/');	
		return $this->db->update('settings', $updated_data);
	}
	
	public function get_cats()
	{
		$result = $this->db->get('categories')->result();
		echo '<pre>'; print_r($result);
	}
    
    public function create_order()
    {
        $this->load->library('orders/cart_orders');
        $res = $this->cart_orders->create_order_from_cart(14362);
        
        echo '<pre>'; print_r($res); 
        die();
    }

    /*********************************************************/

}
/* End of file test.php */
/* Location: ./application/controllers/test.php */
