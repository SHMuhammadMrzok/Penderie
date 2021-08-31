<div class="form">
<span class="error"><?php if(isset($validation_msg)){echo $validation_msg;} ?></span>
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
                  <?php echo lang('country_name');?><span class="required">*</span>
               </label>
               <div class="col-md-4">
                  <?php
                     echo form_error("user_nationality_id");
                     $user_nationality_id = isset($general_data->user_nationality_id) ? $general_data->user_nationality_id : set_value('user_nationality_id') ;                   
                    
                     echo form_dropdown('user_nationality_id', $nationalities_options,$user_nationality_id,'class="form-control" ');
                  ?>   
               </div>
            </div>
          </div>  
       	</div>
        <?php
        
        foreach($data_languages as $key=> $lang){ ?>
        
    		<div class="tab-pane" id="tab_lang_<?php echo $lang->id; ?>">
    		      <div class="form-body">
                    <div class="form-group">
                        <label class="control-label col-md-3">
                          <?php echo lang('city_name');?><span class="required">*</span>
                        </label>
                       <div class="col-md-4">
                        <?php 
                                echo form_error("name[$lang->id]");
                                $name_data = array('name'=>"name[$lang->id]" , 'class'=>"form-control" , 'value'=> isset($filtered_data[$lang->id]->name)? $filtered_data[$lang->id]->name : set_value("name[$lang->id]"));
                                echo form_input($name_data);
                        ?>
                        
                        </div>
                    </div>
                   
                    <?php  echo form_hidden('lang_id[]', $lang->id); ?>
                </div>  
             
    		</div>
        <?php } ?>
        <?php  echo isset($id) ? form_hidden('city_id', $id) : ''; ?>
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
 	