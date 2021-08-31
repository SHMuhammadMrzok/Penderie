<?php
/**
 * @author lolkittens
 * @copyright 2016
 */
$key        = $this->config->item('googleapi_key');
$country    = $this->data['active_country_row']->country_symbol;
$language   = $this->data['active_language_row']->symbol;
$languageid = $this->data['lang_id'];


?>
    <style>
      /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
      #map {
        height: 400px;
        width: auto;
                
      }
      
    </style>

    <div id="searchbuttons" style="text-align: center; margin-top: 20px;" dir="rtl"> 
        <input type="text" id="autocomplete" name="autocomplete" placeholder="Enter your address" size="30">
        <input type="button" value="تحديد هذا العنوان" onclick="branchlocate();" />
    </div>
    <div id="right-panel" style="visibility : hidden;">
      <h2>نتائج البحث</h2>
      <ul id="places"></ul>
    </div>
    <div id="map" style="visibility : hidden;"></div>
    
    
    
    <script>
      // This example requires the Places library. Include the libraries=places
      // parameter when you first load the API. For example:
      // <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">

      var map;
      var infowindow;
      var markers = [];
      var infoWindows = [];
      var branchdetails = [];
      var branches = [];
      

 geolocate();
 
 $( document ).ready(function() {
    allbranchlocate();    
}); 
 //allbranchlocate();    

// get current location
      function geolocate() {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
          var geolocation = {
            lat: position.coords.latitude,
            lng: position.coords.longitude
          };
          geocodeLatLng(geolocation.lat, geolocation.lng); 
          var latcoord = geolocation.lat;
          var lngcoord = geolocation.lng;
          
          var brancheslst;
          
          // Mariam Start
          var postData = {
                    langId: '<?php echo $languageid; ?>',
                    lat: latcoord,
                    lng: lngcoord
                };
          // end of ajax
          jQuery.post('<?php echo base_url();?>shopping_cart/cart/get_branch_list/', postData, function(result){ console.log(result);
          
          if(result != '')
          {
             brancheslst = JSON.parse(result);
             
             //alert('yes');
             //$('#submit_order').show();
          }
          else
          {
             brancheslst = '';
             
             //alert('no');
             //$('#submit_order').hide();
          }
          // Mariam End 
          document.getElementById('places').innerHTML = null;
          document.getElementById('map').style.visibility = 'visible';
          document.getElementById('right-panel').style.visibility = 'visible';
          markers = [];
          infoWindows = [];
          initMap(latcoord, lngcoord, brancheslst);
          }, 'json');
          // end of ajax
          });
        } else {
            alert('Error: Your browser doesn\'t support geolocation.');
        }
      }

// get address
 function geocodeLatLng(mylat, mylng) {
        //var input = mylatlong;
        //var latlngStr = input.split(',', 2);
        var latlng = {lat: parseFloat(mylat), lng: parseFloat(mylng)};
        var geocoder = new google.maps.Geocoder;
        geocoder.geocode({'location': latlng}, function(results, status) {
          if (status === 'OK') {
            if (results[1]) {
              document.getElementById("autocomplete").value = results[1].formatted_address;
            }
          }
        });
      }

// get other location
      function branchlocate() {
        var myaddress = document.getElementById('autocomplete').value ;
        
        if (typeof myaddress == "undefined" || myaddress == null || myaddress == '') {
            alert('Enter Your address');
        } else {
        var geodecoder = new google.maps.Geocoder;
        geodecoder.geocode({'address': myaddress}, function(results, status) {
          if (status === 'OK') {
            if (results[0]) {
          
          // Mariam Start
          // Call get latlng API (Send address => Get lat and lng)
          var latcoord = results[0].geometry.location.lat(); // to change
          var lngcoord = results[0].geometry.location.lng(); // to change
          // here will be result of Get LatLng API (Get: Lat, Lng, brancheslst)
          
          $('input[name=shipping_lng]').val(lngcoord);
          $('input[name=shipping_lat]').val(latcoord);
          
          var brancheslst;
          
          var postData = {
                    langId: '<?php echo $languageid; ?>',
                    lat: latcoord,
                    lng: lngcoord
                };
          // end of ajax
          
          jQuery.post('<?php echo base_url();?>shopping_cart/cart/get_branch_list/', postData, function(result){ 
          if(result != '')
          {
             brancheslst = JSON.parse(result);
          }
          else
          {
             brancheslst = '';
          }
          
          if(brancheslst.length == 0)
          {
            alert('<?php echo lang('no_branches_found');?>');
            $('#submit_order').hide();
          }
          else
          {
            $('#submit_order').show();
          }
          
          
          document.getElementById('places').innerHTML = null;
          document.getElementById('map').style.visibility = 'visible';
          document.getElementById('right-panel').style.visibility = 'visible';
          markers = [];
          infoWindows = [];
          initMap(latcoord, lngcoord, brancheslst);
          
          }, 'json');
          // end of ajax
          
          } else {
            alert('No Data');
            document.getElementById('places').innerHTML = null;
            
          }
          }
        });

      }
      }


function initMap(latcoord, lngcoord, brancheslst) {
      var brancheslst = brancheslst;
        // Create the autocomplete object
        var autocomplete = new google.maps.places.Autocomplete(
        /** @type {!HTMLInputElement} */
          (document.getElementById('autocomplete')),
        {types: ['geocode'], componentRestrictions: {'country': '<?php echo $country; ?>'}});

            // vreate map object
            var mapcenterlatlong = {lat: parseFloat(latcoord), lng: parseFloat(lngcoord)};
            map = new google.maps.Map(document.getElementById('map'), {
              center: mapcenterlatlong,
              zoom: 7
            });      
            map.setCenter(mapcenterlatlong);
            // set marker 
            var geomarker = new google.maps.Marker({
                map: map, 
                position: mapcenterlatlong,
                //icon : 'img/blue-marker.png',
                title: 'You are here.',
                draggable: true,
                animation: google.maps.Animation.DROP,
                });
            // action when mouseover on marker
                geomarker.addListener('mouseover', function() {
                geomarker.setAnimation(google.maps.Animation.BOUNCE);
                geomarker.setAnimation(null);
            });
            
            if(brancheslst != '')
            {
                displaylistedmarkers(brancheslst);
            }

// End of HTML5 geolocation.

// Start of List and markers code
function displaylistedmarkers(branches){//console.log(branches);
        var infowindow = new google.maps.InfoWindow();
        var placesList = document.getElementById('places');
        for (i = 0; i < branches.length; i++) {  
            branchdetails[i] = '<b>' + branches[i]["name"] + '</b><br>Address : ' + branches[i]["address"] + '</b><br>phone : ' + branches[i]["phone"] + '<br>';
            createMarker(branches[i]["name"], branches[i]["lat"], branches[i]["lng"], branchdetails[i], 'no');          
            placesList.innerHTML += '<li style="cursor: pointer; cursor: hand;" onclick="selectmarker(' + i + ')";><label style="cursor: pointer; cursor: hand;"> ' + branchdetails[i] + '</label></li>';
        }
      
       function createMarker(name, latcoord, lngcoord, content, setcenter) {
            var marker = new google.maps.Marker({
              map: map,
              position: {lat: parseFloat(latcoord), lng: parseFloat(lngcoord)},
              icon : '<?php echo base_url();?>assets/template/site/img/qassim-icon.png',
              title: name,
              draggable: true,
              animation: google.maps.Animation.DROP,
            });
          marker.addListener('mouseover', function() {
          marker.setAnimation(google.maps.Animation.BOUNCE);
          marker.setAnimation(null);
          if(setcenter == 'yes')
          {
          map.setCenter(marker.getPosition());
          //map.setZoom(11);
          }
          infowindow.setContent(content);
          infowindow.open(map, this);
        });
          markers.push(marker);
          infoWindows.push(infowindow);
      }             
}
// End of List and markers code
      }

      function selectmarker(i) { 
        map.setCenter(markers[i].getPosition());
        map.setZoom(11);
        markers[i].setAnimation(google.maps.Animation.BOUNCE);
        markers[i].setAnimation(null);
        infoWindows[i].setContent(branchdetails[i]);
        infoWindows[i].open(map, markers[i]);
      }
      
      function allbranchlocate() {
        var myaddress = document.getElementById('autocomplete').value ;
        
        /*if (typeof myaddress == "undefined" || myaddress == null || myaddress == '') {
            alert('Enter Your address');
        } else 
        */
        {
        var geodecoder = new google.maps.Geocoder;
        geodecoder.geocode({'address': myaddress}, function(results, status) {
        
        //  if (status === 'OK') {
            //if (results[0]) {
          
          // Mariam Start
          // Call get latlng API (Send address => Get lat and lng)
         // var latcoord = results[0].geometry.location.lat(); // to change
         // var lngcoord = results[0].geometry.location.lng(); // to change
          // here will be result of Get LatLng API (Get: Lat, Lng, brancheslst)
          
        //  $('input[name=shipping_lng]').val(lngcoord);
        //  $('input[name=shipping_lat]').val(latcoord);
        
        /*
          if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
          
         // var latcoord = geolocation.lat;
         // var lngcoord = geolocation.lng;
            var latcoord = '24.7135517';
            var lngcoord = '46.67529569999999';
          
          });
        }
        else
        {
            var latcoord = '24.7135517';
            var lngcoord = '46.67529569999999';
        }
        */
        
        var latcoord = '24.7135517';
            var lngcoord = '46.67529569999999';
          
          var brancheslst;
          
          var postData = {
                    langId: '2<?php //echo $languageid; ?>',
                    //lat: latcoord,
                    //lng: lngcoord
                };
          // end of ajax
          
          jQuery.post('<?php echo base_url();?>static_pages/view/get_all_branches_list/', postData, function(result){ 
          if(result != '')
          {
             brancheslst = JSON.parse(result);
          }
          else
          {
             brancheslst = '';
          }
          //console.log(brancheslst);
          
        /*  if(brancheslst.length == 0)
          {
            alert('<?php echo lang('no_branches_found');?>');
            $('#submit_order').hide();
          }
          else
          {
            $('#submit_order').show();
          }
          */
          
          document.getElementById('places').innerHTML = null;
          document.getElementById('map').style.visibility = 'visible';
          document.getElementById('right-panel').style.visibility = 'visible';
          markers = [];
          infoWindows = [];
          initMap(latcoord, lngcoord, brancheslst);
          
          }, 'json');
          // end of ajax
          
          /*
          } else {
            alert('No Data');
            document.getElementById('places').innerHTML = null;
            
          }
          */
         // }
        });

      }
      }   
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $key;?>&libraries=places&callback=initMap"></script>