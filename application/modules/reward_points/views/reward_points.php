<div class="form">
<?php //echo validation_errors(); ?>
<?php $att=array('class'=> 'form-horizontal form-bordered cmxform');
      echo form_open_multipart($form_action, $att);?>
    
    <div class="form-body">
        <div class="form-group">
            <label class="control-label col-md-3"><?php echo lang('points');?><span class="required">*</span></label>
           <div class="col-md-4">
              <?php   
                   echo form_error("points");  
                   $points_data = array('name'=>"points" , 'class'=>"form-control points_spinner" , 'value'=> isset($general_data->points)? $general_data->points : set_value('points',''));
                   echo form_input($points_data);
              ?>
           </div>
        </div><!--amount div-->
        
        <div class="form-group">
            <label class="control-label col-md-3"><?php echo lang('price');?><span class="required">*</span></label>
           <div class="col-md-4">
              <?php 
                   echo form_error("price");  
                   $price_data = array('name'=>"price" , 'class'=>"form-control price_spinner" , 'value'=> isset($general_data->price)? $general_data->price : set_value('price', ''));
                   echo form_input($price_data);
              ?>
           </div>
        </div><!--price div-->
        
        <div class="form-group">
           <label class="control-label col-md-3"><?php echo lang('active');?></label>
           <div class="col-md-4">
             <?php 
                
                $active_value     = true ;
                if(isset($general_data->active)) 
                {
                    if($general_data->active == 1)
                    {
                        $active_value     = true;
                    }
                    if($general_data->active == 0)
                    {
                        $active_value     = false;
                    }
                }  
                
                $active_data = array(
                            'name'           => "active",
                            'class'          => 'make-switch',
                            'data-on-color'  => 'danger',
                            'data-off-color'  => 'default',
                            'value'          => 1,
                            'checked'        => set_checkbox("active", $active_value, $active_value),
                            'data-on-text'   => lang('yes'),
                            'data-off-text'  => lang('no'),
                            );    
                echo form_checkbox($active_data);  
             ?>
            </div>
        </div><!-- active -->
         <div class="form-actions">
			<div class="row">
				<div class="col-md-offset-3 col-md-9">
                    <?php  echo isset($id) ? form_hidden('id', $id) : ''; ?> 
                   <button type="submit"  class="btn green"><i class="fa fa-check"></i><?php echo lang('submit');?></button>
               </div>
			</div>
        </div>
        
    </div>
    <?php echo form_close();?>
  
</div>
<script>
    $(function(){
                $(".points_spinner").TouchSpin({          
                    buttondown_class: 'btn green',
                    buttonup_class: 'btn green',
                    min: 0,
                    max: 1000000000,
                    stepinterval: 1,
                    maxboostedstep: 1,
                   
                }); 
            })
 ///////////////////////////////////////////
  $(function(){
                $(".price_spinner").TouchSpin({          
                    buttondown_class: 'btn red',
                    buttonup_class: 'btn red',
                    min: 0,
                    max: 1000000000,
                    stepinterval: 1,
                    maxboostedstep: 1,
                    
                }); 
            })

</script>