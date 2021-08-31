<div class="form">
<span class="error"><?php if(isset($validation_msg)) { echo $validation_msg;}?></span>
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
                      <?php echo lang('category');?>
                    </label>
                   <div class="col-md-4">
                        <?php 
                            echo form_error('cat_id');
                            $cat_id = isset($general_data->cat_id) ? $general_data->cat_id : set_value('cat_id') ;
                            echo form_dropdown('cat_id', $options, $cat_id, 'class="form-control select2"');
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
                          <?php echo lang('label');?>
                          <span class="required">*</span>
                        </label>
                       
                       <div class="col-md-4">
                            <?php 
                                echo form_error("label[$lang->id]");
                                $label_data = array('name'=>"label[$lang->id]" , 'class'=>"form-control" , 'value'=> isset($data[$lang->id]->spec_label)? $data[$lang->id]->spec_label : set_value("label[$lang->id]"));
                                echo form_input($label_data);
                            ?>
                       </div>
                       
                    </div>
                    <?php  echo form_hidden('lang_id[]', $lang->id); ?>
                </div>  
             
    		</div>
        <?php } ?>
        <?php  echo isset($id) ? form_hidden('cat_specifications_id', $id) : ''; ?>
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