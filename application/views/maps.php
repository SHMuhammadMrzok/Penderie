<!DOCTYPE html>
<html>
  <head>
    <title>GeoComplete</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/template/site/css/maps/styles.css" />
  </head>
  <body>

    <form>
      <input id="geocomplete" type="text" placeholder="Type in an address" size="90" />
    </form>
    
    <div class="map_canvas"></div>
    
    <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&sensor=false"></script>
    <script src="http://localhost/like4card/assets/template/admin/global/plugins/jquery.min.js" type="text/javascript"></script>


    <script src="http://ubilabs.github.io/geocomplete/jquery.geocomplete.js"></script> 
    <!--<script src="<?php echo base_url();?>assets/template/site/js/jquery.geocomplete.js"></script> -->
    <script>
      $(function(){
        
        var options = {
          map: ".map_canvas",
          location: "NYC"
        };
        
        $("#geocomplete").geocomplete(options);
        
      });
    </script>
  </body>
</html>

