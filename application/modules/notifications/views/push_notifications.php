<div class="form">
<span class="error"><?php if(isset($validation_msg)){echo $validation_msg;}?></span>
<?php 
    $att=array('class'=> 'form-horizontal form-bordered');
    echo form_open_multipart($form_action, $att);
?>
<div class="tabbable-custom form">
	<ul class="nav nav-tabs ">
         <li class="active">
			<a href="#tab_general" data-toggle="tab">
                
			     <span class="langname"><?php echo lang('general'); ?> </span>
            </a>
		 </li>
      
	</ul>
    
	<div class="tab-content">
        <div class="tab-pane active " id="tab_general">
	      <div class="form-body">
            
            <div class="form-group">
                <label class="control-label col-md-3"><?php echo lang('title');?><span class="required">*</span></label>
               <div class="col-md-4">
                  <?php 
                       echo form_error("title");  
                       $title_data = array('name'=>"title" , 'class'=>"form-control ", 'value'=> isset($general_data->title)? $general_data->title : set_value('title'));
                       echo form_input($title_data);
                  ?>
               </div>
            </div><!--text div-->
            
            <div class="form-group">
                <label class="control-label col-md-3"><?php echo lang('text');?><span class="required">*</span></label>
               <div class="col-md-4">
                  <?php 
                       echo form_error("text");  
                       $text_data = array('name'=>"text" , 'class'=>"form-control ", 'value'=> isset($general_data->text)? $general_data->text : set_value('text'));
                       echo form_textarea($text_data);
                  ?>
               </div>
            </div><!--text div-->
            
        </div>  
             
	   </div>
        
        <?php  echo isset($id) ? form_hidden('id', $id) : ''; ?>
        <div class="form-actions">
			<div class="row">
				<div class="col-md-offset-3 col-md-9">
                    <?php
                        $submit_att= array('class'=>"btn green");
                    ?>
					<button type="submit" class="btn green"><i class="fa fa-check"></i> <?php echo lang('submit');?></button>
				 
				</div>
			</div>
        </div>
  	</div>
</div>
<?php echo form_close();?>
</div>   