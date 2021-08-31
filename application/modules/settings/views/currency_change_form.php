<div class="form">
    <span class="error"><?php if(isset($validation_msg)) echo $validation_msg;?></span>
    <?php
      $att=array('class'=> 'form-horizontal form-bordered');
      echo form_open_multipart($form_action, $att);
    ?>
    <div class="tabbable-custom form">
    	
        
    	<div class="tab-content">
            <div class="tab-pane active " id="tab_general">
                <div class="form-body">
                    <?php foreach($data as $key=>$row){?>    
                        
                        <div class="form-group">
                            <label class="control-label col-md-3"><?php echo lang('dollar_value');?></label>
                           <div class="col-md-4">
                              <?php 
                                   $currency_val_att = array(
                                                                'name'  => 'dollar_vals[]',
                                                                'class' => 'form-control points_spinner_'.$row->id,
                                                                'value' => isset($row->dollar_val) && $row->dollar_val != ''  ? $row->dollar_val : set_value("dollar_vals[$key]")
                                                            );
                                                      
                                   echo form_input($currency_val_att);
                                   
                                   
                                   echo form_error("dollar_vals[$key]");
                                                            
                                   echo form_hidden('country_ids[]', $row->country_id);
    
                                ?>
                            </div>
                            <div class="col-md-4">
                                <span><?php echo $row->currency_symbol;?></span>
                            </div>
                        </div><!-- currency_val div-->
                        
                        <script>
                        $(function(){    
                            $(".points_spinner_<?php echo $row->id;?>").TouchSpin({          
                                buttondown_class: 'btn green',
                                buttonup_class: 'btn green',
                                min: .1,
                                max: 1000000000,
                                step: .1,
                                stepinterval: 1,
                                maxboostedstep: 1,
                               
                            }); 
                        })
                        </script>
                        
                    <?php }?>  
                   
                </div><!--form_body-->
            </div><!--general tab-->  
             
                   
            
            <div class="form-actions">
    			<div class="row">
    				<div class="col-md-offset-3 col-md-9">
                        <?php
                            $submit_att= array('class'=>"btn green");
                        ?>
    					<button type="submit" class="btn green"><i class="fa fa-check"></i> <?php echo lang('edit');?></button>
    				</div>
    			</div>
            </div>
            
            <span></span>
            <?php echo form_close();?>
            
    	</div>
    </div>
</div>    	
