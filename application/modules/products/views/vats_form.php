<div class="form">
<span class="error"><?php if(isset($validation_msg)) echo $validation_msg; ?></span>
    <?php $att=array('class'=> 'form-horizontal form-bordered');
          echo form_open_multipart($form_action, $att);
          if(isset($message))
          {
            echo $message;
          }
    ?>
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
             <label class="control-label col-md-3"><?php echo lang('percent');?></label>
             <div class="col-md-4">
               <?php
                 echo form_error("amount");
                 $amount_data = array(
                   'name'  => "amount" ,
                   'class' => "form-control" ,
                   'value' => isset($general_data->amount)? $general_data->amount : set_value("amount"));
                 echo form_input($amount_data);
               ?>
              </div>
           </div>

           <div class="form-group">
              <label class="control-label col-md-3"><?php echo lang('type');?></label>
              <div class="col-md-4">
                <?php
                  $inclusive_selected = isset($general_data->type)&&$general_data->type==1 ? 'selected' : '';
                  $exclusive_selected = isset($general_data->type)&&$general_data->type==2 ? 'selected' : '';
                ?>
                <select name="type" class="form-control">
                  <option value="1" <?php echo $inclusive_selected;?> ><?php echo lang('inclusive_vat');?></option>
                  <option value="2" <?php echo $exclusive_selected;?> ><?php echo lang('exclusive_vat');?></option>
                </select>
               </div>
            </div>
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

                        </div>
                 </div>
            </div>
         </div>
        <?php foreach($data_languages as $key=> $lang){ ?>

    		<div class="tab-pane" id="tab_lang_<?php echo $lang->id; ?>">
    		      <div class="form-body">

                <div class="form-group">
                   <label class="control-label col-md-3"><?php echo lang('name');?><span class="required">*</span></label>
                   <div class="col-md-4">
                      <?php
                            echo form_error("name[$lang->id]");
                            $name_data = array(
                              'name'=>"name[$lang->id]" ,
                              'class'=>"form-control" ,
                              'value'=> isset($data[$lang->id]->name)? $data[$lang->id]->name : set_value("name[$lang->id]"));
                            echo form_input($name_data);
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
          <button type="submit" class="btn green"><i class="fa fa-check"></i> <?php echo lang('submit');?></button>
				</div>
			</div>
        </div>

	</div>
</div>

<?php echo form_close();?>
</div>
