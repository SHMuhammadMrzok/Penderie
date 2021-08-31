<div class="form">
    <span class="error"><?php if(isset($validation_msg)) echo $validation_msg;?></span>
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
                    <label class="control-label col-md-3"><?php echo $general_data->field;?></label>
                    <div class="col-md-4">
                      <?php 
                           $value_data = array(
                                            'name'=>"value", 
                                            'class'=>"form-control", 
                                            'value'=> isset($general_data->value)? $general_data->value: set_value('value')
                                         );
                                         
                           echo form_input($value_data);
                      ?>
                    </div>
                    
                </div><!-- default_country div-->
              </div><!--form_body-->
           </div><!--general tab-->   
                   
            
            
            
            
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
            <?php echo form_close();?>
            
            
            
            
    	</div>
    </div>
    		

</div>    	
