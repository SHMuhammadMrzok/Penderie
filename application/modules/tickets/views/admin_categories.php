<div class="form">
    <?php  if(isset($validation_msg)){?>
              <span class="error"><?php   echo $validation_msg;?></span>
    <?php }
    
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
                        <label class="control-label col-md-3"><?php echo lang('active');?></label>
                        <div class="col-md-4">
                            <?php 
                                echo form_error('active');
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
                        </div><!--col-md-4 -->
                    </div><!--form-group -->
                </div> <!--form-body --> 
            </div><!--tab_general --> 
            
            <?php foreach($data_languages as $key=> $lang){ ?>
            <div class="tab-pane" id="tab_lang_<?php echo $lang->id; ?>">
              <div class="form-body">
                <div class="form-group">
                    <label class="control-label col-md-3">
                      <?php echo lang('title');?>
                      <span class="required">*</span>
                    </label>
                   <div class="col-md-4">
                        <?php 
                                echo form_error("title[$lang->id]");
                                $title_data = array('name'=>"title[$lang->id]" , 'class'=>"form-control" , 'value'=> isset($data[$lang->id]->title)? $data[$lang->id]->title : set_value("title[$lang->id]"));
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
    					<button type="submit" class="btn green"><i class="fa fa-check"></i> Submit</button>
    				</div>
    			</div>
            </div>
    	</div>
</div>
<?php echo form_close();?>
</div>    	