<div class="form">
    <span class="error"><?php if(isset($validation_msg)){echo $validation_msg;};?></span>
    <?php
    $att=array('class'=> 'form-horizontal form-bordered cmxform' );
        echo form_open_multipart($form_action, $att);
    if(!empty($countries)){
    ?>

        <ul class="nav nav-tabs ">

    	   <?php $index=0; foreach($countries as $key=> $country){?>
    	    <li <?php echo $index==0?'class="active"':'';?>>
    			<a href="#tab_country_<?php echo $country->country_id; ?>" data-toggle="tab">
                    <img alt=""src="<?php echo base_url();?>assets/uploads/<?php echo $country->flag; ?>" />
    			     <span class="langname"><?php echo $country->name; ?> </span>
                </a>
    		</li>
    	  <?php $index++;} ?>

    	</ul>
        <div class="tab-content">
            <?php $index=0;
               foreach($countries as $key=> $country){
                 echo isset($general_data->id) ? form_hidden("product_discount_id[$general_data->id]", $general_data->id) : '';
              ?>

                <div class="tab-pane <?php echo $index==0?'active':'';?>" id="tab_country_<?php echo $country->country_id; ?>">
                  <div class="form-body">

                    <div class="form-group">
                        <label class="control-label col-md-3">
                          <?php echo lang('product_name');?>
                        </label>
                       <div class="col-md-4">
                           <?php
                             $name_data = array('name'=>"name" , 'class'=>"form-control" ,'readonly'=>'true' , 'value'=> isset($product_name)? $product_name : set_value("name"));
                             echo form_input($name_data);
                          ?>
                       </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3">
                          <?php echo lang('current_quantity');?>
                        </label>
                       <div class="col-md-4">
                           <?php
                               $current_quantity_data = array('name'=>"current_quantity[$country->country_id]" , 'class'=>"form-control" ,'readonly'=>'true' , 'value'=> isset($country->available_serials)? $country->available_serials : 00);
                               echo form_input($current_quantity_data);
                          ?>
                       </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3">
                          <?php echo lang('price_before_discount');?>
                        </label>
                       <div class="col-md-4">
                           <?php
                               $price_data = array('name'=>"price[$country->country_id]" , 'class'=>"form-control" ,'readonly'=>'true' , 'value'=> isset($country->country_price)? $country->country_price : 00);
                               echo form_input($price_data);
                           ?>
                       </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3">
                          <?php echo lang('currency');?>
                        </label>
                       <div class="col-md-4">
                           <?php
                                   $currency_data = array('name'=>"currency[$country->country_id]" , 'class'=>"form-control" ,'readonly'=>'true' , 'value'=> isset($country->currency )? $country->currency  : set_value("currency"));
                                   echo form_input($currency_data);
                          ?>
                       </div>
                    </div>

                    <script type="text/javascript">

                    $(function(){
                        $(".price_spinner_<?php echo $country->country_id; ?>").TouchSpin({
                            buttondown_class: 'btn green',
                            buttonup_class: 'btn green',
                            //min: 0,
                            max: 1000000000,
                            stepinterval: 1,
                            maxboostedstep: 1,
                            step: .1

                        });
                    })

                    </script>

                    <div class="form-group">
                       <label class="control-label col-md-3">
                         <?php echo lang('price_after_discount');?><span class="required">*</span>
                       </label>
                       <div class="col-md-4">
                           <?php
                               $price_after_data = array('name'=>"price[$country->country_id]" , 'class'=>"form-control price_spinner_". $country->country_id , 'value'=> isset($country->price)? $country->price : set_value("price[$country->country_id]"));
                               echo form_input($price_after_data);
                               echo form_error("price[$country->country_id]");
                           ?>
                       </div>
                    </div>



                    <div class="form-group">

                        <label class="control-label col-md-3">
                          <?php echo lang('discount_period');?><span class="required">*</span>
                        </label>

                        <div class="col-md-4">
							<div class="input-group input-large date-picker input-daterange" data-date="11-10-2012" data-date-format="dd-mm-yyyy">

                                <?php
                                    $date_start_data = array('name' => "from[$country->country_id]" ,'class'=>"form-control" , 'value'=>isset($country->discount_start_unix_time) && ($country->discount_start_unix_time != 0) ? date('d-m-Y',$country->discount_start_unix_time) : set_value("from[$country->country_id]") );
                                    echo form_input($date_start_data);
                                ?>

                                <span class="input-group-addon">to </span>

                                <?php
                                    $date_end_data = array('name' => "to[$country->country_id]" ,'class'=>"form-control" , 'value'=>isset($country->discount_end_unix_time) && ($country->discount_end_unix_time != 0) ? date('d-m-Y', $country->discount_end_unix_time) : set_value("to[$country->country_id]") );
                                    echo form_input($date_end_data);
                                ?>

							</div>
							<?php
                                echo form_error("from[$country->country_id]");
                                echo form_error("to[$country->country_id]");
                            ?>

						</div>

                    </div>

                    <div class="form-group">
                       <label class="control-label col-md-3">
                         <?php echo lang('dailey');?><span class="required"></span>
                       </label>
                       <div class="col-md-4">
                         <?php
                            echo form_error("dailey[$country->country_id]");
                            $dailey_value     = false ;

                            if(isset($country->dailey))
                            {
                                if($country->dailey == 1)
                                {
                                    $dailey_value = true;
                                }
                                if($country->dailey == 0)
                                {
                                    $dailey_value = false;
                                }
                            }

                            $dailey_data = array(
                                                    'name'           => "dailey[$country->country_id]",
                                                    'class'          => 'make-switch',
                                                    'id'             => "dailey[$country->country_id]",
                                                    'data-on-color'  => 'danger',
                                                    'data-off-color' => 'default',
                                                    'value'          => 1,
                                                    'checked'        => set_checkbox("dailey[$country->country_id]", $dailey_value, $dailey_value),
                                                    'data-on-text'   => lang('yes'),
                                                    'data-off-text'  => lang('no'),
                                                );

                            echo form_checkbox($dailey_data);
                            ?>

                            <script>

                         /*   $(document).ready(function ()
{
   $(":checkbox").change(function (){

    check = $("#dailey[<?php echo $country->country_id?>]").prop("checked");
    alert(check);
        if(check === true) {
            alert('I have been checked');
        }
        else
        {
            alert('not checked');
        }

      });
});

    */                     /*   $(document).ready(function() {
                                $( "body" ).on( "click", "#dailey[<?php echo $country->country_id?>]", function(){
                                    if($('#dailey[<?php echo $country->country_id?>]').attr('checked')) {
                                        //$("#txtAge").show();
                                        alert('1111');
                                    } else {
                                        alert('222');
                                        //$("#txtAge").hide();
                                        }
                                });
                            });
                            */

                            </script>
                        </div>
                    </div>

                    <div class="form-group">

                        <label class="control-label col-md-3">
                          <?php echo lang('discount_period').' '.lang('dailey');?><span class="required"></span>
                        </label>

                        <div class="col-md-4">

                            <?php
                                    $time_start_data = array('name' => "time_from[$country->country_id]" ,'class'=>"form-control" , 'value'=>isset($country->discount_start_time)? $country->discount_start_time : set_value("from_time[$country->country_id]") );
                                    echo form_input($time_start_data);
                                ?>

                                <span class="input-group-addon">to </span>

                                <?php
                                    $time_end_data = array('name' => "time_to[$country->country_id]" ,'class'=>"form-control" , 'value'=>isset($country->discount_end_time) ? $country->discount_end_time : set_value("to_time[$country->country_id]") );
                                    echo form_input($time_end_data);
                                ?>

							<?php
                                echo form_error("time_from[$country->country_id]");
                                echo form_error("time_to[$country->country_id]");
                            ?>
							<span class="error"><?php echo lang('hour_time_hint');?></span>
						</div>

                    </div>

                    <div class="form-group">

                        <script type="text/javascript">

                             $(function(){
                                $(".spinner1_<?php echo $country->country_id; ?>").TouchSpin({
                                    buttondown_class: 'btn blue',
                                    buttonup_class: 'btn blue',
                                    //min: 0,
                                    max: 1000000000,
                                    stepinterval: 1,
                                    maxboostedstep: 1
                                });
                             })
                        </script>

                        <label class="control-label col-md-3">
                          <?php echo lang('max_units_customers');?><span class="required">*</span>
                        </label>

                        <div class="col-md-4">

                            <?php

                             $spinner_data = array('name'=>"max_units_customers[$country->country_id]" , 'class'=>"form-control spinner1_". $country->country_id , 'value'=> isset($country->max_units_customers) ? $country->max_units_customers : set_value("max_units_customers[$country->country_id]"));
                             echo form_input($spinner_data);

                             echo form_error("max_units_customers[$country->country_id]");
                            ?>

                            <span class="error"><?php echo lang('zero_hint');?></span>
						           </div>

                    </div>

                    <div class="form-group">
                       <label class="control-label col-md-3">
                         <?php echo lang('active');?><span class="required"></span>
                       </label>
                       <div class="col-md-4">
                         <?php
                            echo form_error("active[$country->country_id]");
                            $active_value     = true ;

                            if(isset($country->active))
                            {
                                if($country->active == 1)
                                {
                                    $active_value = true;
                                }
                                if($country->active == 0)
                                {
                                    $active_value = false;
                                }
                            }

                            $active_data = array(
                                                    'name'           => "active[$country->country_id]",
                                                    'class'          => 'make-switch',
                                                    'data-on-color'  => 'danger',
                                                    'data-off-color' => 'default',
                                                    'value'          => 1,
                                                    'checked'        => set_checkbox("active[$country->country_id]", $active_value, $active_value),
                                                    'data-on-text'   => lang('yes'),
                                                    'data-off-text'  => lang('no'),
                                                );

                            echo form_checkbox($active_data);
                            ?>
                        </div>
                    </div>

                    <?php /*<div class="form-group">
                       <label class="control-label col-md-3">
                         <?php echo lang('special_offer_label');?><span class="required"></span>
                       </label>
                       <div class="col-md-4">
                         <?php
                            echo form_error("special_offer_label[$country->country_id]");
                            $special_offer_label_value = false ;

                            if(isset($country->special_offer_label))
                            {
                                if($country->special_offer_label == 1)
                                {
                                    $special_offer_label_value = true;
                                }
                                if($country->special_offer_label == 0)
                                {
                                    $special_offer_label_value = false;
                                }
                            }

                            $special_offer_label_data = array(
                                                    'name'           => "special_offer_label[$country->country_id]",
                                                    'class'          => 'make-switch',
                                                    'data-on-color'  => 'danger',
                                                    'data-off-color' => 'default',
                                                    'value'          => 1,
                                                    'checked'        => set_checkbox("special_offer_label[$country->country_id]", $special_offer_label_value, $special_offer_label_value),
                                                    'data-on-text'   => lang('yes'),
                                                    'data-off-text'  => lang('no'),
                                                );

                            echo form_checkbox($special_offer_label_data);
                            ?>
                        </div>
                    </div>
                    */?>

                  </div>
               </div>
               <?php
                 echo form_hidden('country_id[]', $country->country_id);


               ?>

           <?php $index++;} ?>
           <div class="form-actions">
			<div class="row">
				<div class="col-md-offset-3 col-md-9">
                    <?php

                        echo form_hidden('product_id' ,  $product_id) ;
                    ?>
                 	<button type="submit" class="btn green"><i class="fa fa-check"></i> Submit</button>
				 </div>
			</div>
           </div>
        </div>
         <?php }else{?>
            <label><?php echo lang('no_data')?></label>
       <?php }?>
    <?php echo form_close();?>

</div>
<style>
.error{
    color: #a94442;
}
input.error {
  border: 1px dotted red;
}
</style>
