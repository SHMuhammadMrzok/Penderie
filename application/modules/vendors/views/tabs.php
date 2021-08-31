<div class="form">
    <span class="error"><?php if(isset($validation_msg)){echo $validation_msg;}?></span>
    
    <?php   
        $att=array('class'=> 'form-horizontal form-bordered');
                      echo form_open_multipart($form_action, $att);?>
    <div class="tabbable-custom form">
	   <ul class="nav nav-tabs ">
	       <li class="active" >
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
        <div class="tab-pane active" id="tab_general">
	      <div class="form-body">
                <div class="form-group">
                    <label class="control-label col-md-3">
                      <?php echo lang('name_of_store');?><span class="required">*</span>
                    </label>
                   <div class="col-md-4">
                    <?php //print_r($general_data);   
                        echo form_error("store_id");
                        $store_id = isset($general_data->store_id) ? $general_data->store_id : set_value('store_id') ; 
                        echo form_dropdown('store_id', $stores, $store_id,'class="form-control select2"');
                    ?>
                   </div>
                 </div>
            </div>
            
            <div class="form-body">
                <div class="form-group">
                    <label class="control-label col-md-3">
                      <?php echo lang('country');?><span class="required">*</span>
                    </label>
                   <div class="col-md-4">
                    <?php //print_r($general_data);   
                        echo form_error("country_id");
                        $country_id = isset($general_data->country_id) ? $general_data->country_id : set_value('country_id') ; 
                        echo form_dropdown('country_id', $countries_options,$country_id,'class="form-control select2"');
                    ?>
                   </div>
                 </div>
            </div>
            
         </div>
         
         <?php foreach($data_languages as $key=> $lang){ ?>
        
    		<div class="tab-pane  <?php //echo $key==0 ? "active" :'';?>" id="tab_lang_<?php echo $lang->id; ?>">
    		      <div class="form-body">
                    
                    <div class="form-group">
                       <label class="control-label col-md-3">
                         <?php echo lang('title');?><span class="required">*</span>
                       </label>
                       <div class="col-md-4">
                          <?php 
                                echo form_error("title[$lang->id]");
                                $title_data = array('name'=>"title[$lang->id]" , 'class'=>"form-control" , 'value'=> isset($data[$lang->id]->title)? $data[$lang->id]->title : set_value("title[$lang->id]"));
                                echo form_input($title_data);
                          ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label col-md-3"><?php echo lang('description');?></label>
                        <div class="col-md-4">
                        <?php 
                                //echo form_error("description[$lang->id]");
                                $description_data = array('name'=> "description[$lang->id]" , 'class'=>"form-control text_editor" , 'value'=> isset($data[$lang->id]->description)? $data[$lang->id]->description : set_value("description[$lang->id]"));
                                echo form_textarea($description_data);
                        ?>
                        </div>
                    </div>
                    <?php  echo form_hidden('lang_id[]', $lang->id); ?>
                </div>  
             
    		</div>
        <?php } ?>
        <?php  echo isset($id) ? form_hidden('vendor_id', $id) : ''; ?>
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