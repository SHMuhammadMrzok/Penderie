<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/template/site/css/maps/styles.css" />
<div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
	<div class="row no-margin">
    	<div class="iner_page">
            <div class="row no-margin">
                <h1 class="title_h1"><?php echo lang('edit_mydata');?></h1>
                <span class="error" style="color: red;"><?php if(isset($validation_msg)) echo $validation_msg; ?></span>
                <div class="registration">
                    <form action="<?php echo base_url();?>users/users/edit_wholesaler_data" method="post" enctype="multipart/form-data">
                        <?php if(isset($_SESSION['message'])){?>   
                            <div class="success_message"><?php echo $_SESSION['message'];?></div><!--success_message-->
                        <?php }?>
                        <?php if(isset($_SESSION['error'])){?>
                            <div class="fail_message"><?php echo $_SESSION['error'];?></div><!--fail_message-->
                        <?php }?>
                        <div class="block_regist">
                           <?php echo validation_errors();?>
                            
                            <div class="row no-margin margin-bottom-10px">
                                <h3><?php echo lang('wholesaler_data');?></h3>
                                <div class="gray">
                                    <div class="map_canvas"></div>
                                    <div class="row no-margin margin-bottom-10px">
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <label for="google_image"><?php echo lang('google_maps_image');?><span class="required">*</span></label>
                                        </div><!--col-->
                                        
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                           <div>
                                           <?php
                                             $readonly = isset($user_data->geocomplete) && $user_data->geocomplete!='' ? 'readonly' : '';
                                             $geo_att  = array('name'=>'geocomplete', 'id'=>'geocomplete', 'placeholder'=>lang('type_in_address'), 'value'=>isset($user_data->geocomplete) && $user_data->geocomplete !='' ? $user_data->geocomplete: 'المملكة العربية السعودية', $readonly=>$readonly);
                                             
                                             echo form_input($geo_att);
                                             echo form_error('geocomplete');
                                           ?>
                                           <input id="find" type="button" value="<?php echo lang('find');?>" />
                                             
                                           <div class="row" >
                                               <label><?php echo lang('latitude');?></label>
                                               <?php
                                                 $readonly = isset($user_data->google_map_lat) && $user_data->google_map_lat!='' ? 'readonly' : '';
                                                 $lat_att = array('name'=>'lat', 'value'=>isset($user_data->google_map_lat) && $user_data->google_map_lat !=''? $user_data->google_map_lat: '', $readonly=>$readonly);
                                                 echo form_input($lat_att);
                                               ?>
                                           </div>
                                           
                                           <div class="row">
                                               <label><?php echo lang('longitude');?></label>
                                               
                                               <?php
                                                 $readonly = isset($user_data->google_map_lng) && $user_data->google_map_lng!='' ? 'readonly' : '';
                                                 $lng_att = array('name'=>'lng', 'value'=>isset($user_data->google_map_lng) && $user_data->google_map_lng != '' ? $user_data->google_map_lng: '', $readonly=>$readonly);
                                                 echo form_input($lng_att);
                                               ?>
                                           </div>
                                           
                                           <a id="reset" href="#" style="display:none;">Reset Marker</a>
                                           </div>

                                        </div>
                                   </div><!--google map image-->
                                   
                                   <div class="row no-margin margin-bottom-10px">
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <label for="logo"><?php echo lang('logo');?><span class="required">*</span></label>
                                        </div><!--col-->
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                             <?php if(isset($user_data->logo) && $user_data->logo!=''){?>
                                                <img src="<?php echo base_url();?>assets/uploads/<?php echo $user_data->logo;?>" width="120" height="100" />
                                             <?php }
                                                   else
                                                   {   
                                                     $logo_data = array('name'=>'image3', 'id'=>'logo', 'required'=>'required');
                                                     echo form_upload($logo_data);
                                                     echo form_error('image3');
                                                   }
                                            ?>
                                        </div><!--col-->
                                   </div><!--Logo-->
                                   
                                    <div class="row no-margin margin-bottom-10px">
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <label for="sms_sender"><?php echo lang('sms_sender');?><span class="required">*</span></label>
                                        </div><!--col-->
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                            <?php
                                             $readonly       = isset($user_data->sms_name) && $user_data->sms_name!='' ? 'readonly' : '';
                                             $sms_sender_att = array('name'=>'sms_name', 'id'=>'sms_sender', 'placeholder'=>lang('sms_sender'), 'class'=>'form-control', 'required'=>'required', 'value'=> isset($user_data->sms_name) && $user_data->sms_name != '' ? $user_data->sms_name : set_value('sms_name'), $readonly=>$readonly, 'maxlength'=>'11');
                                             echo form_input($sms_sender_att);
                                            ?>
                                        </div><!--col-->
                                        <?php echo form_error('sms_name');?>
                                    </div><!--sms sender-->
                                    
                                    <div class="row no-margin margin-bottom-10px">
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <label for="sms_content"><?php echo lang('sms_content');?><span class="required">*</span></label>
                                        </div><!--col-->
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                            <?php
                                             $readonly        = isset($user_data->sms_content) && $user_data->sms_content!='' ? 'readonly' : '';
                                             $sms_content_att = array('name'=>'sms_content', 'id'=>'sms_content', 'placeholder'=>'', 'class'=>'form-control', 'required'=>'required', 'value'=> isset($user_data->sms_content) && $user_data->sms_content != '' ? $user_data->sms_content : set_value('sms_content'), $readonly=>$readonly, 'maxlength'=>'50');
                                             echo form_textarea($sms_content_att);
                                            ?>
                                        </div><!--col-->
                                        <?php echo form_error('sms_content');?>
                                    </div> 
                                    
                                    <div class="row no-margin margin-bottom-10px">
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <label for="header"><?php echo lang('header');?><span class="required">*</span></label>
                                        </div><!--col-->
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                            <?php
                                             $readonly   = isset($user_data->header) && $user_data->header!='' ? 'readonly' : '';
                                             $header_att = array('name'=>'header', 'id'=>'header', 'placeholder'=>'', 'class'=>'form-control', 'required'=>'required', 'value'=> isset($user_data->header) && $user_data->header != '' ? $user_data->header : set_value('header'), $readonly=>$readonly);
                                             echo form_textarea($header_att);
                                            ?>
                                        </div><!--col-->
                                        <?php echo form_error('header');?>
                                    </div><!--header-->
                                    
                                    <div class="row no-margin margin-bottom-10px">
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <label for="footer"><?php echo lang('footer');?><span class="required">*</span></label>
                                        </div><!--col-->
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                            <?php
                                             $readonly   = isset($user_data->footer) && $user_data->footer!='' ? 'readonly' : '';
                                             $footer_att = array('name'=>'footer', 'placeholder'=>'', 'class'=>'form-control', 'required'=>'required', 'value'=> isset($user_data->footer) && $user_data->footer != '' ? $user_data->footer : set_value('footer'), $readonly=>$readonly);
                                             echo form_textarea($footer_att);
                                            ?>
                                        </div><!--col-->
                                        <?php echo form_error('footer');?>
                                    </div>
                                    
                                </div><!--gray-->
                            </div><!--wholesaler data-->
                            
                            <div class="row no-margin margin-bottom-10px margin-top-20px">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <button class="btn btn-primary"><?php echo lang('save');?></button>
                                </div><!--col-->
                            </div><!--row-->
                        </div><!--block_regist-->
                    </form>
                </div><!--registration-->
            </div><!--row-->
	    </div><!--iner_page-->
    </div><!--row-->
</div><!--col-->

<script src="http://maps.googleapis.com/maps/api/js?sensor=false&amp;libraries=places"></script>
<script src="<?php echo base_url();?>assets/template/site/js/jquery.geocomplete.js"></script>

<script>
  
  $(function(){
        $("#geocomplete").geocomplete({
          map: ".map_canvas",
          details: "form ",
          location : [<?php echo $user_data->google_map_lat != '' ? $user_data->google_map_lat : '23.885942';?>, <?php echo $user_data->google_map_lng != '' ? $user_data->google_map_lng : '45.079162';?>],
          
          markerOptions: {
            draggable: <?php echo $user_data->geocomplete == '' ? 'true' : 'false';?>
          }
          
        });
        
        <?php if($user_data->geocomplete == ''){?>
            $("#geocomplete").bind("geocode:dragged", function(event, latLng){
              $("input[name=lat]").val(latLng.lat());
              $("input[name=lng]").val(latLng.lng());
              $("#reset").show();
            });
        <?php }?>
        
        $("#reset").click(function(){
          $("#geocomplete").geocomplete("resetMarker");
          $("#reset").hide();
          return false;
        });
        
        $("#find").click(function(){
          $("#geocomplete").trigger("geocode");
        }).click();
      });
</script>
