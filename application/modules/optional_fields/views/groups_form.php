<div class="form">
    <?php if(isset($validation_msg)){?><span class="error"><?php echo $validation_msg;?></span><?php } 
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
                 <?php echo lang('limit');?>
                 <span class="required">*</span>
               </label>
               <div class="col-md-4">
                  <?php 
                    echo form_error("limit");
                    $limit_data = array(
                                        'name'=>"limit", 
                                        'class'=>"form-control" , 
                                        'value'=> isset($general_data->group_limit)? $general_data->group_limit : set_value("limit")
                                       );
                    
                    echo form_input($limit_data);
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
                         <?php echo lang('name');?><span class="required">*</span>
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
        <?php  echo isset($id) ? form_hidden('coupon_code_id', $id) : ''; ?>
        <div class="form-actions">
			<div class="row">
				<div class="col-md-offset-3 col-md-9">
                    <?php $submit_att= array('class'=>"btn green");?>
					<button type="submit" class="btn green"><i class="fa fa-check"></i> Submit</button>
				 
				</div>
			</div>
        </div>
        
	</div>
</div>

<div class="loading_modal"><!-- Place at bottom of page --></div>
<?php echo form_close();?>
</div>
<style>
        .loading_modal {
        display:     none;
        position:    fixed;
        z-index:     1000;
        top:         0;
        left:        0;
        height:      100%;/*750px;*/
        width:       100%;/*900px;*/
        margin-left: 300px;
        background:  rgba( 255, 255, 255, .8 ) 
                     url('<?php echo base_url().'assets/ajax-loader.gif';?>') 
                     50% 50% 
                     no-repeat;
    }
    
    body.loading {
        overflow: hidden;   
    }
    
    body.loading .loading_modal {
        display: block;
    }
    </style>
