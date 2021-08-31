<!--START upload single image like in GROCERY CRUD-->

<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>assets/grocery_crud/css/jquery_plugins/chosen/chosen.css" />
<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>assets/grocery_crud/css/ui/simple/jquery-ui-1.10.1.custom.min.css" />
<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>assets/grocery_crud/css/jquery_plugins/file_upload/file-uploader.css" />
<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>assets/grocery_crud/css/jquery_plugins/file_upload/jquery.fileupload-ui.css" />
<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>assets/grocery_crud/css/jquery_plugins/fancybox/jquery.fancybox.css" />
<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>assets/grocery_crud/css/jquery_plugins/file_upload/fileuploader.css" />

<?php
    $field_name        = 'image';
    $unique_id         = mt_rand(); 
    $unique_name       = 's'.substr(md5($field_name),0,8);//'s5ae0c1c8';//'p'.substr(md5($unique_id), 0, 10);
    $upload_path       = base_url().'assets/uploads/';
    $display_style     = '';
    $display_image_div = '';
    $value             = ''; 
 ?>
 
<div class="right-content">
    <div class="list">
        <div class="relate"><a href="#addA" class="active"><?php echo lang('general'); ?></a></div>
        <div class="relate"><a href="#addB"><?php echo lang('description');?></a></div>
        
    </div>
    <div class="add">
        <div id="addA" class="relateDiv row">
        
         <span class="error"><?php if(isset($validation_msg)){echo $validation_msg;}?></span>
            
        <form method="post" class="col-12" id="main_form">
            
    
            <div class="col-12 col-sm-6">
                <div class="form-group">
                    <div class="form-item">
                        <label><?php echo lang('store_route');?> <span> * </span> </label>
                        <?php 
                            $route_data = array('name'=>'route',
                                                'class'=>"form-control" , 
                                                'value'=> isset($general_data->route)? $general_data->route : set_value('route'));
                            echo form_input($route_data);
                            echo form_error("route"); 
                        ?>
                    </div>
                </div>
            </div>
            
            <div class="col-12 col-sm-6">
                <div class="form-group">
                    <div class="form-item">
                        <label><?php echo lang('phone');?> <span>  </span> </label>
                        <?php 
                            $phone_data = array('name'=>'phone',
                                                'class'=>"form-control" , 
                                                'value'=> isset($general_data->phone)? $general_data->phone : set_value('phone'));
                            echo form_input($phone_data);
                            echo form_error("phone"); 
                        ?>
                    </div>
                </div>
            </div>
            
            <div class="col-12 col-sm-6">
                <div class="form-group">
                    <div class="form-item">
                        <label><?php echo lang('facebook');?> <span>  </span> </label>
                        <?php 
                            $facebook_data = array('name'=>'facebook',
                                                'class'=>"form-control" , 
                                                'value'=> isset($general_data->facebook)? $general_data->facebook : set_value('facebook'));
                            echo form_input($facebook_data);
                            echo form_error("facebook"); 
                        ?>
                    </div>
                </div>
            </div>
            
            <div class="col-12 col-sm-6">
                <div class="form-group">
                    <div class="form-item">
                        <label><?php echo lang('twitter');?> <span>  </span> </label>
                        <?php 
                            $twitter_data = array('name'=>'twitter',
                                                'class'=>"form-control" , 
                                                'value'=> isset($general_data->twitter)? $general_data->twitter : set_value('twitter'));
                            echo form_input($twitter_data);
                            echo form_error("twitter"); 
                        ?>
                    </div>
                </div>
            </div>
            
            <div class="col-12 col-sm-6">
                <div class="form-group">
                    <div class="form-item">
                        <label><?php echo lang('instagram');?> <span>  </span> </label>
                        <?php 
                            $instagram_data = array('name'=>'instagram',
                                                'class'=>"form-control" , 
                                                'value'=> isset($general_data->instagram)? $general_data->instagram : set_value('instagram'));
                            echo form_input($instagram_data);
                            echo form_error("instagram"); 
                        ?>
                    </div>
                </div>
            </div>
            
            <div class="col-12 col-sm-6">
                <div class="form-group">
                    <div class="form-item">
                        <label><?php echo lang('youtube');?> <span>  </span> </label>
                        <?php 
                            $youtube_data = array('name'=>'youtube',
                                                'class'=>"form-control" , 
                                                'value'=> isset($general_data->youtube)? $general_data->youtube : set_value('youtube'));
                            echo form_input($youtube_data);
                            echo form_error("youtube"); 
                        ?>
                    </div>
                </div>
            </div>
    
            
            <div class="col-12 col-sm-6">
                <div class="form-group">
                    <div class="form-item">
                       <label >
                          <?php echo lang('thumbnail');?><span class="required">*</span>
                       </label>
                       
                       <div >
                         <?php
                            echo form_error('image');
                          if(isset($general_data->image) && $general_data->image !='')
                          {
                            $display_style     = "display:none;";
                            $display_image_div = '
                                                 <div id="success_'.$unique_id.'" class="upload-success-url" style=" padding-top:7px;">
                                                     <a href="'.$upload_path.$general_data->image.'" id="file_'.$unique_id.'" class="open-file image-thumbnail">
                                                         <img src="'.$upload_path.$general_data->image.'" height="50px" >
                                                     </a> 
                                                     <a href="javascript:void(0)" id="delete_'.$unique_id.'" class="delete-anchor">delete</a>
                                                 </div>';
                            $value              = $general_data->image;
                          }
                         ?>
                            <!-- image upload-->    
                            <div class="form-div">
                                <div class="form-field-box odd" id="<?php echo $field_name;?>_field_box">
                                    <div class="form-input-box" id="<?php echo $field_name;?>_input_box">
                                       
                                        <span class="fileinput-button qq-upload-button" id="upload-button-<?php echo $unique_id; ?>" style="<?php echo $display_style;?>">
                                			<span>Upload a file</span>
                                			<input type="file" name="<?php echo $unique_name; ?>" class="gc-file-upload" rel="<?php echo base_url();?>uploads/upload_image/image_uploads/upload_file/<?php echo $field_name;?>" id="<?php echo $unique_id; ?>">
                                			<input class="hidden-upload-input" type="hidden" name="<?php echo $field_name;?>" value="<?php if(isset($general_data->image)){echo $general_data->image;}?>" rel="<?php echo $unique_name; ?>">
                                		</span>
                                        
                                        <div id="uploader_<?php echo $unique_id; ?>" rel="<?php echo $unique_id; ?>" class="grocery-crud-uploader" style=""></div>
                                        
                                        <?php echo $display_image_div; ?>
                                        
                                        <div id="success_<?php echo $unique_id; ?>" class="upload-success-url" style="display:none; padding-top:7px;">
                                            <a href="<?php echo base_url();?>assets/uploads/" id="file_<?php echo $unique_id; ?>" class="open-file" target="_blank"></a> 
                                            <a href="javascript:void(0)" id="delete_<?php echo $unique_id; ?>" class="delete-anchor">delete</a> 
                                        </div>
                                        
                                        <div style="clear:both"></div>
                                        
                                        <div id="loading-<?php echo $unique_id; ?>" style="display:none">
                                            <span id="upload-state-message-<?php echo $unique_id; ?>"></span> 
                                            <span class="qq-upload-spinner"></span> 
                                            <span id="progress-<?php echo $unique_id; ?>"></span>
                                        </div>
                                        
                                        <div style="display:none">
                                            <a href="<?php echo base_url();?>uploads/upload_image/image_uploads/upload_file/<?php echo $field_name;?>" id="url_<?php echo $unique_id; ?>"></a>
                                        </div>
                                        
                                        <div style="display:none">
                                            <a href="<?php echo base_url();?>uploads/upload_image/image_uploads/delete_file/<?php echo $field_name;?>" id="delete_url_<?php echo $unique_id; ?>" rel=""></a>
                                        </div>				
                                  
                                  </div>
                                  <div class="clear"></div>
                                
                                </div>
                            
                            </div>
                            <!-- image upload-->
                            
                       </div>
                    </div>
                </div>
            </div>
    <div class="container-fluid button-style butcenter">
                        <button class="next"><a href="#"><?php echo lang('continue');?></a></button>
                        
                    </div> 
           
           
        </div>
        <div id="addB" class="relateDiv row">
            <?php foreach(array_reverse($data_languages) as $key=> $lang){?>
                
                <div class="col-12 col-sm-6 <?php echo $lang->direction == 'ltr' ? 'en-version':'';?>">
                    <h5 style="color: #f79d36;padding: 10px 0"><?php echo $lang->name; ?></h5>
                    <div class="form-group">
                        <div class="form-item">
                            <label for="address"> <?php echo lang('title');?></label>
                            <?php 
                                echo form_error("title[$lang->id]"); 
                                $title_data = array('name'=>"name[$lang->id]" , 'class'=>"form-control" ,'value'=> isset($data[$lang->id]->name)? $data[$lang->id]->name : set_value("name[$lang->id]") );
                                echo form_input($title_data);
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-item">
                            <label for="desc"><?php echo lang('description');?></label>
                            <?php 
                                $text_data = array('name'=> "description[$lang->id]" , 'class'=>"form-control summernote_1" , 'value'=> isset($data[$lang->id]->description)? $data[$lang->id]->description : set_value("description[$lang->id]"));
                                echo form_textarea($text_data);
                            ?>
                        </div>
                    </div>
                    
                    
                    
                </div>
                <?php  echo form_hidden('lang_id[]', $lang->id); ?>
            <?php }?>
            
              <div class="container-fluid button-style butcenter">
                <button class="back"><a href="#"><?php echo lang('previous');?></a></button>
                <button class="next"><a href="#" class="finish"><?php echo lang('finish');?></a></button>
              </div>
        </div>
        
                  
    </div>
    
    </form>
</div>

<script>
/**********Show store cats****************************************/
$(document).ready(function() {
     /*next button*/
    $(".next a[href='#']").click(function(e){
        e.preventDefault();
        
        var attrbefore = $(".list .relate a[class='active']").attr('href').replace('#','');
        $(".list .relate a[class='active']").removeClass('active').parent().next().children().addClass("active");
         var attrafter = $(".list .relate a[class='active']").attr('href').replace('#','');
         $(".add div[id='"+attrbefore+"']").css("display","none");
         $(".add div[id='"+attrafter+"']").css("display","flex");
         
         if(attrbefore == null){
             alert("hello");
         }
        
       
    });
    /*back button*/
    $(".back a[href='#']").click(function(e){
        e.preventDefault();
    var attrbefore = $(".list .relate a[class='active']").attr('href').replace('#','');
        $(".list .relate a[class='active']").removeClass('active').parent().prev().children().addClass("active");
         var attrafter = $(".list .relate a[class='active']").attr('href').replace('#','');
         $(".add div[id='"+attrbefore+"']").css("display","none");
         $(".add div[id='"+attrafter+"']").css("display","flex");
    
    
    });
    
    
});
/*************************************************/
</script>

<script type="text/javascript">

var upload_info_<?php echo $unique_id; ?> = {
	accepted_file_types: /(\.|\/)(gif|jpeg|jpg|png|tiff|doc|docx|txt|odt|xls|xlsx|pdf|ppt|pptx|pps|ppsx|mp3|m4a|ogg|wav|mp4|m4v|mov|wmv|flv|avi|mpg|ogv|3gp|3g2)$/i,
	accepted_file_types_ui : ".gif,.jpeg,.jpg,.png,.tiff,.doc,.docx,.txt,.odt,.xls,.xlsx,.pdf,.ppt,.pptx,.pps,.ppsx,.mp3,.m4a,.ogg,.wav,.mp4,.m4v,.mov,.wmv,.flv,.avi,.mpg,.ogv,.3gp,.3g2",
	max_file_size: 20971520,
	max_file_size_ui: "20MB"
};

var string_upload_file 	= "Upload a file";
var string_delete_file 	= "Deleting file";
var string_progress 			= "Progress: ";
var error_on_uploading 			= "An error has occurred on uploading.";
var message_prompt_delete_file 	= "Are you sure that you want to delete this file?";

var error_max_number_of_files 	= "You can only upload one file each time.";
var error_accept_file_types 	= "You are not allow to upload this kind of extension.";
var error_max_file_size 		= "The uploaded file exceeds the 20MB directive that was specified.";
var error_min_file_size 		= "You cannot upload an empty file.";

var upload_a_file_string = "Upload a file";
</script>

<script>
$(".finish").click(function(){
    
    $( "#main_form" ).submit();
});
</script>
</script>