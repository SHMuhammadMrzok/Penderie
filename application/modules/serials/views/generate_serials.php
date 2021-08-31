<div class="form">
<span class="error"><?php if(isset($validation_msg)){echo $validation_msg;} ?></span>
<?php $att=array('class'=> 'form-horizontal form-bordered cmxform');
      echo form_open_multipart($form_action, $att);?>

    <div class="form-body">
        <div class="form-group">
            <label class="control-label col-md-3"><?php echo lang('amount_code');?><span class="required">*</span></label>
           <div class="col-md-4">
              <?php
                    echo form_error("amount");
                   $amount_data = array('name'=>"amount" , 'class'=>"form-control amount_spinner" , 'value'=> isset($general_data->amount)? $general_data->amount : set_value('amount'));
                   echo form_input($amount_data);
              ?>
           </div>
        </div><!--amount div-->

        <div class="form-group">
            <label class="control-label col-md-3"><?php echo lang('number_of_codes');?><span class="required">*</span></label>
           <div class="col-md-4">
              <?php
                   echo form_error("number_of_codes");
                   $number_of_codes_data = array('name'=>"number_of_codes" , 'class'=>"form-control number_of_codes_spinner" , 'value'=> isset($general_data->number_of_codes)? $general_data->number_of_codes : set_value('number_of_codes'));
                   echo form_input($number_of_codes_data);
              ?>
           </div>
        </div><!--number_of_codes div-->

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
                            'name'           => "active",
                            'class'          => 'make-switch',
                            'data-on-color'  => 'danger',
                            'data-off-color'  => 'default',
                            'value'          => 1,
                            'checked'        => set_checkbox("active", $active_value, $active_value),
                            'data-on-text'   => lang('yes'),
                            'data-off-text'  => lang('no'),
                            );
                echo form_checkbox($active_data);
             ?>
            </div>
        </div><!-- active -->

        <div class="form-group">
           <label class="control-label col-md-3"><?php echo lang('export');?></label>
           <div class="col-md-4">
             <?php
                $active_value = false ;

                $active_data = array(
                            'name'           => "export",
                            'class'          => 'make-switch',
                            'data-on-color'  => 'danger',
                            'data-off-color'  => 'default',
                            'value'          => 1,
                            'checked'        => set_checkbox("export", false, false),
                            'data-on-text'   => lang('yes'),
                            'data-off-text'  => lang('no'),
                            );

                echo form_checkbox($active_data);
             ?>
            </div>
        </div><!--Export-->

         <div class="form-actions">
    			<div class="row">
    				<div class="col-md-offset-3 col-md-9">
                <?php  echo isset($id) ? form_hidden('id', $id) : ''; ?>
               <button type="submit"  class="btn green"><i class="fa fa-check"></i><?php echo lang('submit');?></button>
           </div>
    			</div>
        </div>

    </div>
    <?php echo form_close();?>

</div>
<script>
    $(function(){
                $(".amount_spinner").TouchSpin({
                    buttondown_class: 'btn green',
                    buttonup_class: 'btn green',
                    min: 0,
                    max: 1000000000,
                    step: .1,
                    stepinterval: 1,
                    maxboostedstep: 1,

                });
            })
 ///////////////////////////////////////////
  $(function(){
                $(".number_of_codes_spinner").TouchSpin({
                    buttondown_class: 'btn red',
                    buttonup_class: 'btn red',
                    min: 0,
                    max: 1000000000,
                    stepinterval: 1,
                    maxboostedstep: 1,

                });
            })

</script>
