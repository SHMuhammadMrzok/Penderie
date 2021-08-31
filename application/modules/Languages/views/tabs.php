<div class="form">
<?php $att=array('class'=> 'form-horizontal form-bordered');
                      echo form_open_multipart($form_action, $att);?>
<div class="tabbable-custom form">
	<ul class="nav nav-tabs ">
         <li class="active">
			<a href="#tab_general" data-toggle="tab">
                
			     <span class="langname"><?php echo lang('general'); ?> </span>
            </a>
		 </li>
	   <?php foreach($data_languages as $key=> $lang){?>
	       <li <?php //echo $key==0?'class="active"':'';?> >
			<a href="#tab_lang_<?php echo $lang->id; ?>" data-toggle="tab">
                <img alt="" src="<?php echo base_url();?>/assets/template/admin/global/img/flags/<?php echo $lang->flag; ?>" />
			     <span class="langname"><?php echo $lang->name; ?> </span>
            </a>
		</li>
	  <?php } ?>
    	
	</ul>
    
	<div class="tab-content">
        <div class="tab-pane active " id="tab_general">
    		      <div class="form-body">
                    <div class="form-group">
                        <label class="control-label col-md-3">
                          <?php echo lang('lang_var');?>
                        </label>
                       <div class="col-md-4">
                            <?php 
 
                                echo form_error("lang_var");
                                $lang_var_data = array('name'=>"lang_var" , 'class'=>"form-control" , 'value'=> isset($general_data->lang_var)? $general_data->lang_var : set_value("lang_var"), 'readonly'=>isset($general_data->lang_var)? 'readonly' : '' );
                                echo form_input($lang_var_data);

                                //$var_value = $this->languages_model->get_var_value($id);
                                //echo $var_value;
                            ?>
                        </div>
                    </div>
                </div>  
             
    		</div>
        
        
            <?php foreach($data_languages as $key=> $lang){ ?>
        
    		  <div class="tab-pane  <?php  //echo $key==0 ? "active" :'';?>" id="tab_lang_<?php echo $lang->id; ?>">
    		      <div class="form-body">
                    <div class="form-group">
                        <label class="control-label col-md-3">
                          <?php echo lang('lang_definition');?>
                          <span class="required">*</span>
                        </label>
                       <div class="col-md-4">
                            <?php 
                                    echo form_error("lang_definition[$lang->id]");
                                    $lang_definition_data = array('name'=>"lang_definition[$lang->id]" , 'class'=>"form-control" , 'value'=> isset($data[$lang->id]->lang_definition)? $data[$lang->id]->lang_definition : set_value("lang_definition[$lang->id]"));
                                    echo form_input($lang_definition_data);
                            ?>
                        </div>
                    </div>

                    <?php  echo form_hidden('lang_id[]', $lang->id); ?>
                </div>  
             
    		</div>
        <?php } ?>
        <?php  echo isset($id) ? form_hidden('lang_var_id', $id) : ''; ?>
        <div class="form-actions">
			<div class="row">
				<div class="col-md-offset-3 col-md-9">
                    <?php
                        $submit_att= array('class'=>"btn green");
                    ?>
					<button type="submit" class="btn green"><i class="fa fa-check"></i> Submit</button>
				 
				</div>
			</div>
        </div>
        
	</div>
</div>
    		
<?php echo form_close();?>
</div>    	