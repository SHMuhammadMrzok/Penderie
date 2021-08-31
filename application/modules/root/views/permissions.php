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
       <?php foreach($data_languages as $key=> $lang){?>
	      <li>
			<a href="#tab_lang_<?php echo $lang->id; ?>" data-toggle="tab">
                <img alt="" src="<?php echo base_url();?>/assets/template/admin/global/img/flags/<?php echo $lang->flag; ?>" />
			     <span class="langname"><?php echo $lang->name; ?> </span>
            </a>
		  </li>
	  <?php } ?>
    	
	</ul>
    
	<div class="tab-content">
        <?php //print_r($general_data );echo $general_data->module_id;?>
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
               
               </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3">
                  <?php echo lang('controller');?><span class="required">*</span>
                </label>
               <div class="col-md-4">
               
                 <?php 
                    echo form_error("controller");
                    $controller_id = isset($general_data->controller_id) ? $general_data->controller_id : set_value('controller') ;                   
                    
                    echo form_dropdown('controller_id', $controller_options,$controller_id,'class="form-control" id="controller_id"');
                ?> 
                
               
               </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3">
                  <?php echo lang('method');?><span class="required">*</span>
                </label>
               <div class="col-md-4">
               
                
                <?php 
                    echo form_error("method_id");
                    $method_id = isset($general_data->method_id) ? $general_data->method_id : set_value('method') ;                   
                    
                    echo form_dropdown('method_id', $methods_options,$method_id,'class="form-control" id="method_id"');
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
                          <?php echo lang('permission_name');?><span class="required">*</span>
                        </label>
                       <div class="col-md-4">
                        <?php 
                                echo form_error("name[$lang->id]");
                                $name_data = array('name'=>"name[$lang->id]" , 'class'=>"form-control" , 'value'=> isset($data[$lang->id]->name)? $data[$lang->id]->name : set_value("name[$lang->id]"));
                                echo form_input($name_data);
                        ?>
                        
                        </div>
                    </div>
                   
                    <?php  echo form_hidden('lang_id[]', $lang->id); ?>
                </div>  
             
    		</div>
        <?php } ?>
        <?php  echo isset($id) ? form_hidden('permission_id', $id) : ''; ?>
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
    url:"<?=base_url()?>root/permissions/get_controllers/"+module_id,
    success:function(info){
     $("#controller_id").html(info);
    }
   });
   return false;
});

////////////////////////////////
$("#controller_id").change(function(){
   var controller_id = $(this).val();
   $.ajax({
    type:'post',
    data:{ id: $(this).val()},
    url:"<?=base_url()?>root/permissions/get_methods/"+controller_id,
    success:function(info){
     $("#method_id").html(info);
    }
   });
   return false;
});
</script>   	