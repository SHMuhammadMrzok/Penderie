<div class="form">
    <?php $att=array('class'=> 'form-horizontal form-bordered');
                      echo form_open_multipart($form_action, $att);?>
    <div class="tabbable-custom form">
	   <ul class="nav nav-tabs ">
	       <li class="active" >
    		<a href="#tab_general" data-toggle="tab">
                <span class="langname"><?php echo lang('general'); ?> </span>
            </a>
    	   </li>
          
	   </ul>
    
	<div class="tab-content">
        <div class="tab-pane active" id="tab_general">
	      <div class="form-body">
                <div class="form-group">
                   <label class="control-label col-md-3">
                     <?php echo lang('name');?><span class="required"></span>
                   </label>
                   <div class="col-md-4">
                    <span class="form-control"><?php echo $general_data->username;?></span>
                    </div>
                </div>
                
                <div class="form-group">
                   <label class="control-label col-md-3">
                     <?php echo lang('comment');?><span class="required"></span>
                   </label>
                   <div class="col-md-4">
                    <span class="form-control"><?php echo $general_data->comment;?></span>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="control-label col-md-3">
                      <?php echo lang('approved');?><span class="required"></span>
                    </label>
                    
                    <div class="col-md-4">
                        <?php 
                            echo form_error('approved');
                           
                            $active_value     = true ;
                            
                            if(isset($general_data->approved)) 
                            {
                                if($general_data->approved == 1)
                                {
                                    $active_value     = true;
                                }
                                if($general_data->approved == 0)
                                {
                                    $active_value     = false;
                                }
                            }  
                            
                            $active_data = array(
                                        'name'           => 'approved',
                                        'class'          => 'make-switch',
                                        'value'          => 1,
                                        'checked'        => set_checkbox('approved', $active_value, $active_value),
                                        'data-on-text'   => lang('yes'),
                                        'data-off-text'  => lang('no'),
                                        );    
                            echo form_checkbox($active_data); 
                           ?>
                   </div>
                 </div>
            </div>
         </div>
        
        <?php  echo isset($id) ? form_hidden('product_id', $id) : ''; ?>
        <div class="form-actions">
			<div class="row">
				<div class="col-md-offset-3 col-md-9">
                    <?php
                        $submit_att= array('class'=>"btn green");
                       // echo form_submit('mysubmit', 'Submit',$submit_att);
                    ?>
					<button type="submit" class="btn green"><i class="fa fa-check"></i> Submit</button>
				 
				</div>
			</div>
        </div>
        
	</div>
</div>
    		
<?php echo form_close();?>
</div>    	