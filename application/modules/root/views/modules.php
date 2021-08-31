<div class="form">
<?php //echo validation_errors(); ?>
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
        <?php //print_r($general_data );echo $general_data->icon_class;?>
        <div class="tab-pane active" id="tab_general">
	      <div class="form-body">
            <div class="form-group">
                <label class="control-label col-md-3">
                  <?php echo lang('module');?><span class="required">*</span>
                </label>
               <div class="col-md-4">
                <?php   echo form_error("module");
                        $module_data = array('name'=>"module" , 'class'=>"form-control" , 'value'=> isset($general_data->module)? $general_data->module : set_value('module'));
                        echo form_input($module_data);
                ?>
               </div>
            </div>
            
            <div class="form-group">
                <label class="control-label col-md-3">
                  <?php echo lang('module_icon_class');?><span class="required">*</span>
                </label>
               <div class="col-md-4">
                <?php 
                        echo form_error("icon_class");
                        $icon_class_data = array('name'=>"icon_class" , 'class'=>"form-control" , 'value'=> isset($general_data->icon_class)? $general_data->icon_class : set_value('icon_class'));
                        echo form_input($icon_class_data);
                ?>
               </div>
            </div>
            
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
                          <?php echo lang('module_name');?><span class="required">*</span>
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
        <?php  echo isset($id) ? form_hidden('module_id', $id) : ''; ?>
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