<div class="form">
    <span class="error"><?php if(isset($validation_msg)) echo $validation_msg; ?></span>
    <?php $att=array('class'=> 'form-horizontal form-bordered cmxform');
          echo form_open_multipart($form_action, $att);
    ?>
    <div class="tabbable-custom form">
        <ul class="nav nav-tabs ">
	       <!--<li class="active" >
    		<a href="#tab_general" data-toggle="tab">
                <span class="langname"><?php echo lang('general'); ?> </span>
            </a>
    	   </li>
          -->
           <?php foreach($data_languages as $key=> $lang){?>
    	       <li class="<?php echo $key==0?'active':'';?>" >
    			 <a href="#tab_lang_<?php echo $lang->id; ?>" data-toggle="tab">
                    <img alt="" src="<?php echo base_url();?>/assets/template/admin/global/img/flags/<?php echo $lang->flag; ?>" />
    			     <span class="langname"><?php echo $lang->name; ?> </span>
                 </a>
    		  </li>
	       <?php } ?>
    	
	   </ul>
       
       <div class="tab-content">
          <?php /*
          <!-- <div class="tab-pane active" id="tab_general">
               <div class="form-body">        
                    <div class="form-group">
                        <label class="control-label col-md-3"><?php echo lang('cost');?><span class="required">*</span></label>
                       <div class="col-md-4">
                          <?php 
                               echo form_error("cost");  
                               $cost_data = array('name'=>"cost" , 'class'=>"form-control" , 'value'=> isset($general_data->cost)? $general_data->cost : set_value('cost'));
                               echo form_input($cost_data);
                          ?>
                          
                       </div>
                       <div class="col-md-4">
                        <input type="text" readonly="readonly" name="symbol" value="<?php echo $currency_symbol;?>" class="form-control" style="width: 100px!important;" />
                       </div>
                       
         <!--           </div><!--cost div-->
         <!--       </div>
            </div>
            -->
            */?>
            <?php foreach($data_languages as $key=> $lang){ ?>
                <div class="tab-pane <?php echo $key==0?'active':'';?>"  id="tab_lang_<?php echo $lang->id; ?>">
    		      <div class="form-body">
                    
                    <div class="form-group">
                       <label class="control-label col-md-3">
                         <?php echo lang('country_name');?><span class="required">*</span>
                       </label>
                       <div class="col-md-4">
                          <?php 
                                echo form_error("country[$lang->id]");
                                $country_data = array('name'=>"country[$lang->id]" , 'class'=>"form-control" , 'value'=> isset($data[$lang->id]->country)? $data[$lang->id]->country : set_value("country[$lang->id]"));
                                echo form_input($country_data);
                          ?>
                        </div>
                    </div>
                    
                    <?php  echo form_hidden('lang_id[]', $lang->id); ?>
                </div>  
             
    		</div>
      <?php }?>
      
      
            <div class="form-actions">
    			<div class="row">
    				<div class="col-md-offset-3 col-md-9">
                        <?php  echo isset($id) ? form_hidden('id', $id) : ''; ?> 
                       <button type="submit"  class="btn green"><i class="fa fa-check"></i><?php echo lang('submit');?></button>
                   </div>
    			</div>
            </div>
       </div>
    </div>
    <?php echo form_close();?>
  
</div>
