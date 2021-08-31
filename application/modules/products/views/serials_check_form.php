<div class="form">
    <span class="error"><?php if(isset($validation_msg)){echo $validation_msg;}?></span>
    
    <?php   
        $att=array('class'=> 'form-horizontal form-bordered');
                      echo form_open_multipart($form_action, $att);?>
    <div class="tabbable-custom form">
	   
    
	<div class="tab-content">
        <div class="tab-pane active" id="tab_general">
	      <div class="form-body">
                <div class="form-group">
                    <label class="control-label col-md-3">
                      <?php echo lang('serials');?><span class="required">*</span>
                    </label>
                   <div class="col-md-4">
                    <?php    
                        echo form_error("serials");
                        $serials_data = array('name'=> "serials" , 'class'=>"form-control" , 'value'=> '');
                        echo form_textarea($serials_data);
                    ?>
                   </div>
                 </div>
            </div>
         </div>
        
        
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