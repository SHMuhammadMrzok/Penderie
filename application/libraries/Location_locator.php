<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
/**
 * 
 */
class Location_locator
{
    public $CI;
    public $key;
    public $settings;
    public $language = 'en';
    public $max_distance_km ;
    
    
    public function __construct()
    {
        $this->CI = &get_instance();
        
        $this->CI->load->model('branches/branches_model');
        $this->CI->load->model('global_model');
        
        $settings       = $this->CI->global_model->get_config();
        $this->settings = $settings;
        $this->key      = $settings->googleapi_key;
        $this->max_distance_km = $settings->locator_max_distance;
        
    }
    
    public function getlatlong($gmap_api_url) 
    {
        // function to get file content
        return  file_get_contents($gmap_api_url);
    }
    
    public function get_location_address($gmap_latlong)
    {
        // get lng, lat values 
        // return adress
        
        $gmap_api_url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=". $gmap_latlong ."&language=". $this->language ."&key=". $this->key;
        $json = json_decode($this->getlatlong($gmap_api_url), true);
        return  $json['results'][0]['formatted_address'];
    }
    
    
    public function get_location_latlong($gmap_address)
    {
        // get address
        // return lng, lat
        if($gmap_address != '')
        {
            $gmap_address = str_replace(" ","+",$gmap_address);
            $gmap_api_url = "https://maps.googleapis.com/maps/api/geocode/json?address=". $gmap_address ."&language=". $this->language ."&key=". $this->key;
            $json = json_decode($this->getlatlong($gmap_api_url), true);
            
            $result = array ($json['results'][0]['geometry']['location']['lat'], $json['results'][0]['geometry']['location']['lng']);
            return $result;
            //return  $json['results'][0]['geometry']['location']['lat'].",".$json['results'][0]['geometry']['location']['lng'];
        }
        else
        {
            return false;
        }
    }
    
    public function get_branch_list($gmap_latlong, $lang_id)
    {
        $max_distance_km = $this->max_distance_km;
        $branchs_latlong = $this->get_branches_latlong($lang_id);
        
        if($this->settings->locator_type == 'google_api')
        {
            $gmap_api_url   = "https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&origins=". $gmap_latlong ."&destinations=". $branchs_latlong ."&key=". $this->key;
            
            $json           = json_decode($this->getlatlong($gmap_api_url), true);
            
            $array_loop     = $json['rows'][0]['elements'];
            
            $branchs_latlong_list   = explode("|", $branchs_latlong);
            $branch_list_locations  = array();
        
            foreach ($array_loop as $key => $value) 
            {
                if($value['status'] == "OK" && $value['distance']['value'] <= $max_distance_km) {
                    
                    $lat_lng_values = explode(",", $branchs_latlong_list[$key]);
                    $lat            = $lat_lng_values[0];
                    $lng            = $lat_lng_values[1];
                    
                    $branch_data    = $this->CI->branches_model->get_branch_from_lng_lat_values($lat, $lng, $lang_id);
                    
                    /*$branch_list_locations[] = (object)array(
                              'id'          => $branch_data->id,
                              'lat'         => $lat,//$branchs_latlong_list[$key],
                              'lng'         => $lng,
                              'distance'    => round($value['distance']['value']/1000, 1),
                              'address'     => $branch_data->address,
                              'name'        => $branch_data->name,
                              'phone'       => $branch_data->phone,
                              'image'       => base_url().'assets/uploads/'.$branch_data->image
                                                    );
                    */
                    if(count($branch_data) != 0)
                    {
                        array_push($branch_list_locations,[
                              'id'          => $branch_data->id,
                              'lat'         => $lat,//$branchs_latlong_list[$key],
                              'lng'         => $lng,
                              'distance'    => round($value['distance']['value']/1000, 1),
                              'address'     => $branch_data->address,
                              'name'        => $branch_data->name,
                              'phone'       => $branch_data->phone,
                              'image'       => base_url().'assets/uploads/'.$branch_data->image
                            ]);
                    }    
                }
            }
            
        }
        else
        {
            $gmap_api_url = $this->aproximate_near_branches($gmap_latlong);
            $json         = json_decode($gmap_api_url);
            
            $array_loop   = $json->rows;
            
            $branchs_latlong_list   = explode("|", $branchs_latlong);
            $branch_list_locations  = array();
            
            foreach ($array_loop as $key => $value) 
            {
                if($value->status == "OK" && $value->distance->value <= $max_distance_km) {
                    
                  /*  $lat_lng_values = explode(",", $branchs_latlong_list[$key]);
                    $lat            = $lat_lng_values[0];
                    $lng            = $lat_lng_values[1];
                    */
                    $branch_data    = $this->CI->branches_model->get_row_data($value->id->value, $lang_id);
                    
                    if(count($branch_data) != 0)
                    {
                        array_push($branch_list_locations,[
                              'id'          => $branch_data->id,
                              'lat'         => $branch_data->lat,//$branchs_latlong_list[$key],
                              'lng'         => $branch_data->lng,
                              'distance'    => round($value->distance->value, 1),//round($value->distance->value/1000, 1),
                              'address'     => $branch_data->address,
                              'name'        => $branch_data->name,
                              'phone'       => $branch_data->phone,
                              'image'       => base_url().'assets/uploads/'.$branch_data->image
                            ]);
                    }    
                }
            }
        }
        
        
        $distance = array();
        foreach ($branch_list_locations as $key => $row)
        {
            $distance[$key]  = $row['distance'];
        }
        
        array_multisort($distance, SORT_ASC, $branch_list_locations);
        return json_encode($branch_list_locations);
    }
    
    public function get_branches_latlong($lang_id)
    {
        $branches_latlng    = '';
        $branches           = $this->CI->branches_model->get_branches_data($lang_id);
        
        foreach($branches as $branch)
        {
            $branches_latlng .= $branch->lat.','.$branch->lng.'|';
        }
        
        return $branches_latlng;
    }
    
    public function aproximate_near_branches2($lat_lng)
    {
        $lat_lng_values = explode(",", $lat_lng);
        $lat            = $lat_lng_values[0];
        $lng            = $lat_lng_values[1];
        $final_array    = (object)array();
        $new_array      = array();
        
        $all_branches   = $this->CI->branches_model->all_branches();
        
        foreach($all_branches as $branch)
        {
            $distance = $this->distance($lat, $lng, $branch['lat'], $branch['lng'], 'K');
            
            if($distance < $this->settings->locator_max_distance)
            {
                 array_push($new_array,[
                          'distance' => (object)array('value' => $distance),
                          'status'=>"OK"
                          
                        ]);
            }
        }
    }
    
    function distance($lat1, $lon1, $lat2, $lon2, $unit) 
    {

      $theta = $lon1 - $lon2;
      $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
      $dist = acos($dist);
      $dist = rad2deg($dist);
      $miles = $dist * 60 * 1.1515;
      $unit = strtoupper($unit);
    
      if ($unit == "K") {
        return ($miles * 1.609344);
      } else if ($unit == "N") {
          return ($miles * 0.8684);
        } else {
            return $miles;
          }
    }

    
    
    public function aproximate_near_branches($lat_lng)
    {
        $lat_lng_values = explode(",", $lat_lng);
        $lat            = $lat_lng_values[0];
        $lng            = $lat_lng_values[1];
        $final_array    = (object)array();
        $new_array      = array();
        
        $near_branches  = $this->CI->branches_model->get_near_branches($lat, $lng, $this->settings->locator_max_distance); 
        
        foreach($near_branches as $branch)
        {
            //$new_array[] = (object)array('distance'=>(object)array('value' => $branch['distance']), 'status'=>"OK");
            
            array_push($new_array,[
                          'distance' => (object)array('value' => $branch['distance']),
                          'id'       => (object)array('value' => $branch['id']),
                          'status'=>"OK"
                          
                          
                        ]);
        }
        
        $final_array->{'rows'} = (object)$new_array;
        $final_array->{'status'} = "OK";
        
        return json_encode($final_array);
        
         
    }
    
    public function get_all_branch_list($lang_id)
    {
        $branch_list_locations = array();
        $branches_list = $this->CI->branches_model->get_all_branches($lang_id);
        
        foreach ($branches_list as $key => $branch_data) 
        {
            array_push($branch_list_locations,[
                          'id'          => $branch_data->id,
                          'lat'         => $branch_data->lat,//$branchs_latlong_list[$key],
                          'lng'         => $branch_data->lng,
                          //'distance'    => round($value->distance->value, 1),//round($value->distance->value/1000, 1),
                          'address'     => $branch_data->address,
                          'name'        => $branch_data->name,
                          'phone'       => $branch_data->phone,
                          'image'       => base_url().'assets/uploads/'.$branch_data->image
                        ]);
        }
        return json_encode($branch_list_locations);
    }    
    
}
?>