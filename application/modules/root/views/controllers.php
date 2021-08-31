<div class="form">
<?php echo validation_errors(); ?>
<?php $att=array('class'=> 'form-horizontal form-bordered');
                      echo form_open_multipart($form_action, $att);?>
<div class="tabbable-custom form">
	<ul class="nav nav-tabs ">
	   <li class="active" >
		<a href="#tab_general" data-toggle="tab">
            <span class="langname"><?php echo lang('general'); ?> </span>
        </a>
	   </li>
       <?php foreach($data_languages as $key=> $lang){?>
	      <li>
			<a href="#tab_lang_<?php echo $lang->id; ?>" data-toggle="tab">
                <img alt="" src="<?php echo base_url();?>/assets/template/admin/global/img/flags/<?php echo $lang->flag; ?>" />
			     <span class="langname"><?php echo $lang->name; ?> </span>
            </a>
		  </li>
	  <?php } ?>
    	
	</ul>
    <?php //print_r($general_data);?>
	<div class="tab-content">
       <div class="tab-pane active" id="tab_general">
	      <div class="form-body">
            <div class="form-group">
               <label class="control-label col-md-3">
                  <?php echo lang('module_name');?><span class="required">*</span>
               </label>
               <div class="col-md-4">
                  <?php
                     echo form_error("module_id");
                     $module_id = isset($general_data->module_id) ? $general_data->module_id : set_value('module_id') ;                   
                    
                     echo form_dropdown('module_id', $module_options,$module_id,'class="form-control" id="module_id"');
                  ?>   
                
                
                <!--<select name="module_id"  id="module_id" class="form-control">
                    <?php foreach($active_modules as $module){
                            $selected = ($general_data->module_id == $module->id )? 'selected' : set_value('module_id');
                    ?>
                    <option value="<?php echo $module->id;?>" <?php echo $selected;?>><?php echo $module->module;?></option>
                    <?php }?>
                </select>
                -->
               </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3">
                  <?php echo lang('controller');?><span class="required">*</span>
                </label>
               <div class="col-md-4">
               
                <?php 
                    echo form_error("controller");
                    $controller_id = isset($general_data->controller) ? $general_data->controller : set_value('controller') ;                   
                    
                    echo form_dropdown('controller', $controller_options,$controller_id,'class="form-control" id="controller"');
                ?>
             </div>
            </div>
            
            <div class="form-group">
                <label class="control-label col-md-3">
                  <?php echo lang('controller_icon_class');?><span class="required">*</span>
                </label>
               <div class="col-md-4">
                <?php 
                        $icon_class_data = array('name'=>"icon_class" , 'class'=>"form-control" , 'value'=> isset($general_data->icon_class)? $general_data->icon_class : '');
                        echo form_input($icon_class_data);
                ?>
               </div>
            </div>
            
            <div class="form-group">
                <label class="control-label col-md-3">
                  <?php echo lang('active');?>
                </label>
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
                                    'name'           => 'active',
                                    'class'          => 'make-switch',
                                    'value'          => 1,
                                    'checked'        => set_checkbox('active', $active_value, $active_value),
                                    'data-on-text'   => lang('yes'),
                                    'data-off-text'  => lang('no'),
                                    );    
                        echo form_checkbox($active_data);
                                
                 ?>
                </div>
            </div>
            
          </div>  
             
    	</div>
        <?php foreach($data_languages as $key=> $lang){ ?>
        
    		<div class="tab-pane" id="tab_lang_<?php echo $lang->id; ?>">
    		      <div class="form-body">
                    <div class="form-group">
                        <label class="control-label col-md-3">
                          <?php echo lang('controller_name');?><span class="required">*</span>
                        </label>
                       <div class="col-md-4">
                        <?php 
                                $name_data = array('name'=>"name[$lang->id]" , 'class'=>"form-control" , 'value'=> isset($filtered_data[$lang->id]->name)? $filtered_data[$lang->id]->name : set_value("name[$lang->id]"));
                                echo form_input($name_data);
                        ?>
                        
                        </div>
                    </div>
                   
                    <?php  echo form_hidden('lang_id[]', $lang->id); ?>
                </div>  
             
    		</div>
        <?php } ?>
        <?php  echo isset($id) ? form_hidden('controller_id', $id) : ''; ?>
        <div class="form-actions">
			<div class="row">
				<div class="col-md-offset-3 col-md-9">
                 	<button type="submit" class="btn green"><i class="fa fa-check"></i><?php echo lang('submit')?></button>
				</div>
			</div>
        </div>
        
	</div>
</div>
    		
<?php echo form_close();?>
</div>  

<script>
$("#module_id").change(function(){
   var module_id = $(this).val();
   $.ajax({
    type:'post',
    data:{ id: $(this).val()},
    url:"<?php echo base_url(); ?>root/controllers/get_controllers/"+module_id,
    success:function(info){
     $("#controller").html(info);
    }
   });
   return false;
});
</script>  	