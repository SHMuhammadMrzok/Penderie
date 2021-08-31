<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
/**
 * 
 */
class Locator
{
    public $CI;
    public $key;
    public $language = 'en';
    public $max_distance_km = 10;
    
    public function __construct()
    {
        $this->CI = &get_instance();
        
        $this->key = $this->CI->config->item('googleapi_key');
        $this->CI->load->model('branches/branches_model');
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
        $json = json_decode(getlatlong($gmap_api_url), true);
        return  $json['results'][0]['formatted_address'];
    }
    
    
    public function get_location_latlong($gmap_address)
    {
        // get address
        // return lng, lat
        
        $gmap_address = str_replace(" ","+",$gmap_address);
        $gmap_api_url = "https://maps.googleapis.com/maps/api/geocode/json?address=". $gmap_address ."&language=". $this->language ."&key=". $this->key;
        $json = json_decode(getlatlong($gmap_api_url), true);
        return  $json['results'][0]['geometry']['location']['lat'].",".$json['results'][0]['geometry']['location']['lng'];
    }
    
    public function get_branch_list($gmap_latlong, $lang_id)
    {
        $max_distance_km = $this->max_distance_km;
        $branchs_latlong = $this->get_branches_latlong($lang_id);
        
        $gmap_api_url   = "https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&origins=". $gmap_latlong ."&destinations=". $branchs_latlong ."&key=". $this->key;
        $json           = json_decode(getlatlong($gmap_api_url), true);
        
        $branchs_latlong_list   = explode("|", $branchs_latlong);
        $branch_list_locations  = [];
        
        foreach ($json['rows'][0]['elements'] as $key => $value) 
        {
            if($value['status'] == "OK" && $value['distance']['value'] <= $max_distance_km) {
                
                $lat_lng_values = explode(",", $branchs_latlong_list[$key]);
                $lat            = $lat_lng_values[0];
                $lng            = $lat_lng_values[1];
                
                $branch_data    = $this->CI->branches_model->get_branch_from_lng_lat_values($lat, $lng);
                
                array_push($branch_list_locations,[
                      'id'          => $branch_data->id,
                      'latlong'     => $branchs_latlong_list[$key],
                      'distance'    => round($value['distance']['value']/1000, 1)
                    ]);
            }
        }
        
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
        $branches           = $this->branches_model->get_branches_data($lang_id);
        
        foreach($branches as $branch)
        {
            $branches_latlng .= $branch->lat.','.$branch->lng.'|';
        }
        
        return $branches_latlng;
    }
    
}
?>