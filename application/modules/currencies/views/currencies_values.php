<div class="form">
    <span class="error"><?php if(isset($validation_msg)){echo $validation_msg;}?></span>
    
    <?php   
        $att=array('class'=> 'form-horizontal form-bordered');
        echo form_open_multipart($form_action, $att);
    ?>
    <div class="tabbable-custom form">
    	<div class="tab-content">
            <div class="tab-pane active" id="tab_general">
    	      <div class="form-body">
                <div class="form-group">
                    <label class="control-label col-md-3">
                       <?php echo lang('currency');?>
                    </label>
                   <label class="control-label col-md-3">
                       <?php echo lang('value');?>
                   </label>
                   <label class="control-label col-md-3">
                       <?php echo lang('default_currency');?>
                   </label>
                 </div>
              
                 <?php foreach ($data as $row){?>
                    
                    <div class="form-group">
                        <label class="control-label col-md-3">
                          <?php echo $row->name;?>
                        </label>
                       <div class="col-md-4">
                        <?php    
                            echo form_error("currency_value[$row->id]");
                            $symbol_data = array('name'=>"currency_value[$row->id]" , 'class'=>"form-control points_spinner_$row->id" , 'value'=> isset($row->currency_value)? $row->currency_value : set_value("currency_value[$row->id]"));
                            echo form_input($symbol_data); 
                             
                        ?>
                       </div>
                       <div class="col-md-4">
                        <?php
                             $default_value = false;
                            
                            if(!isset($validation_msg)) 
                            {
                                if($row->system_default == 1)
                                {
                                    $default_value = true;
                                }
                            }
                            
                            $default_data  = array(
                                                        'name'    => "default",
                                                        'class'   => 'make-switch switch-radio1',
                                                        'id'      => 'option1',
                                                        'data-on-text'   => lang('yes'),
                                                        'data-off-text'  => lang('no'),
                                                        'value'   => $row->id,
                                                        'checked' => set_radio("default", 1, $default_value)
                                                    );
                                             
                            echo form_radio($default_data);
                        ?> 
                       </div>
                     </div>
                     
                     <?php  echo form_hidden('currency_id[]', $row->id); ?>
                     
                     
                        
                 <?php }?>
                </div>
             </div>
            
            
            
            <div class="form-actions">
    			<div class="row">
    				<div class="col-md-offset-3 col-md-9">
                        <button type="submit" class="btn green"><i class="fa fa-check"></i> <?php echo lang('edit');?></button>
    				</div>
    			</div>
            </div>
            
    	</div>
</div>
    		
<?php echo form_close();?>
</div>    	