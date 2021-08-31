
<div class="form">
    <span class="error"><?php if(isset($validation_msg)){echo $validation_msg;}?></span>
    
    <?php   
        $att = array('class'=> 'form-horizontal form-bordered');
                      echo form_open_multipart($form_action, $att);?>
    <div class="tabbable-custom form">
	   <ul class="nav nav-tabs ">
	       
           <?php foreach($data_languages as $key=> $lang){?>
    	       <li class="<?php echo $key==0?'active':'';?> ">
    			 <a href="#tab_lang_<?php echo $lang->id; ?>" data-toggle="tab">
                    <img alt="" src="<?php echo base_url();?>/assets/template/admin/global/img/flags/<?php echo $lang->flag; ?>" />
    			     <span class="langname"><?php echo $lang->name; ?> </span>
                 </a>
    		  </li>
	       <?php } ?>
    	
	   </ul>
    
	<div class="tab-content">
        
        <?php foreach($data_languages as $key=> $lang){ ?>
        
    		<div class="tab-pane  <?php echo $key==0 ? "active" :'';?>" id="tab_lang_<?php echo $lang->id; ?>">
    		      <div class="form-body">
                    
                    <div class="form-group">
                       <label class="control-label col-md-3">
                         <?php echo lang('name');?><span class="required">*</span>
                       </label>
                       <div class="col-md-4">
                          <?php 
                                echo form_error("title[$lang->id]");
                                $title_data = array('name'=>"title[$lang->id]" , 'class'=>"form-control" , 'value'=> isset($data[$lang->id]->name)? $data[$lang->id]->name : set_value("title[$lang->id]"));
                                echo form_input($title_data);
                          ?>
                        </div>
                    </div>
                    
                    
                    
                    <?php  echo form_hidden('lang_id[]', $lang->id); ?>
                </div>  
             
    		</div>
        <?php } ?>
        <?php  echo isset($id) ? form_hidden('id', $id) : ''; ?>
        <div class="form-actions">
			<div class="row">
				<div class="col-md-offset-3 col-md-9">
                    <?php
                        $submit_att= array('class'=>"btn green");
                    ?>
					<button type="submit" class="btn green"><i class="fa fa-check"></i> <?php echo lang('submit');?></button>
				 
				</div>
			</div>
        </div>
        
	</div>
</div>
    		
<?php echo form_close();?>
</div>


