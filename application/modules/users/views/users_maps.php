<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/template/site/css/maps/styles.css" />
<div class="portlet-body">
	<div class="row">
		
		<div class="col-md-9 col-sm-9 col-xs-9">
			<div class="tab-content">
				<div class="tab-pane active" id="general">
					<p>
                        <div class="row static-info">
                            <div class="col-md-5 name">
            					 <?php echo lang('device_location');?>  :
            				</div>
               	        </div>
                        
                        <div class="map_canvas"></div>
                        <?php if(isset($msg)){?>
                            <div class="msg"><?php echo $msg;?></div>
                        <?php }?>
                        
                         <?php
                             $geo_att  = array('name'=>'geocomplete', 'id'=>'geocomplete', 'placeholder'=>lang('type_in_address'), 'value'=> "[$lng, $lat]", 'style'=>'display: none;');
                             
                             echo form_input($geo_att);
                             echo form_error('geocomplete');
                         ?>
					</p>
				</div>
				
			</div>
		</div>
	</div>
</div>


 
<script src="http://maps.googleapis.com/maps/api/js?sensor=false&amp;libraries=places"></script>
<script src="<?php echo base_url();?>assets/template/site/js/jquery.geocomplete.js"></script>

<script>
  <?php if(isset($lng) && isset($lat)){?>
      $(function(){
            
        var options = {
          map: ".map_canvas",
          location: ['<?php echo $lng;?>', '<?php echo $lat;?>']
        };
        
        $("#geocomplete").geocomplete(options);
        
      });
  <?php }?>
      
</script>