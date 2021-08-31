<div class="form">
    <span class="error"><?php  if(isset($validation_msg))echo $validation_msg;?></span>
    <?php
        $att = array('class'=> 'form-horizontal form-bordered');
        echo form_open_multipart($form_action, $att);
    ?>
    <div class="tabbable-custom form">
    	<ul class="nav nav-tabs ">
             <li class="active">
    			<a href="#tab_general" data-toggle="tab">
    			     <span class="langname"><?php echo lang('general'); ?> </span>
                </a>
    		 </li>
    	     <li>
    			<a href="#tab_payment_details" data-toggle="tab">
                    <span class="langname"><?php echo lang('payment_details'); ?> </span>
                </a>
    		</li>
    	 </ul>
    	<div class="tab-content">
            <div class="tab-pane active " id="tab_general">
                <div class="form-body">
                    <div class="form-group">
                        <label class="control-label col-md-3"><?php echo lang('username');?><span class="required">*</span></label>
                        <div class="col-md-4">
                            <?php 
                                 echo form_error("user_id");
                                $user_id = isset($general_data->user_id) ? $general_data->user_id : set_value('user_id') ;                   
                                
                                echo form_dropdown('user_id', $options, $user_id, 'class="form-control select2"');
        
                            ?>
                        </div><!--col-md-4 -->
                    </div><!--form-group -->
                    
                    <div class="form-group">
                        <label class="control-label col-md-3"><?php echo lang('code');?><span class="required">*</span></label>
                       <div class="col-md-4">
                            <?php 
                                echo form_error("code");
                                $code_data = array('name'=>'code','class'=>"form-control" , 'value'=> isset($general_data->code)? $general_data->code :$code);
                                echo form_input($code_data);
                            ?>
                       </div>
                    </div><!--form-group -->
                    
                     <div class="form-group">
                        <label class="control-label col-md-3"><?php echo lang('num_uses');?><span class="required">*</span></label>
                       <div class="col-md-4">
                            <?php 
                                echo form_error("num_uses");
                                $num_uses_data = array('name'=>'num_uses','class'=>"form-control num_uses" , 'value'=> isset($general_data->num_uses)? $general_data->num_uses :0);
                                echo form_input($num_uses_data);
                            ?>
                            <span class="error"><?php echo lang('num_uses_note');?></span>   
                       </div>
                       
                    </div><!--form-group -->
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
            
            <div class="tab-pane" id="tab_payment_details">
              <div class="form-body">
                <div class="form-group">
                   <label class="control-label col-md-3"><?php echo lang('commission');?><span class="required">*</span></label>
                   <div class="col-md-4">
                    <?php 
                        echo form_error('commission');
                        $commission_data = array('name'=>"commission" , 'class'=>"form-control commission_spinner" , 'value'=> isset($general_data->commission)? $general_data->commission : set_value("commission"));
                        echo form_input($commission_data);
                    ?>
                   </div>
                </div><!--form-group -->
                
                <!--<div class="form-group">
                   <label class="control-label col-md-3"><?php echo lang('tax_id');?></label>
                   <div class="col-md-4">
                    <?php 
                        $tax_id_data = array('name'=>"tax_id" , 'class'=>"form-control" , 'value'=> isset($general_data->tax_id)? $general_data->tax_id : set_value("tax_id"));
                        echo form_input($tax_id_data);
                    ?>
                   </div>
                </div><!--form-group -->
                <?php /*?>
                <div class="form-group">
                    
					<label class="control-label col-md-3"><?php echo lang('payment_method_id');?></label>
					<div class="radio-list col-md-4">
                        <label class="radio-inline">
                            <?php 
                                 $cheque_data = array(
                                        'name'           => "payment_method_id",
                                        'class'          => 'radio-inline cheque',
                                        'id'             => 'cheque_radio',
                                        'value'          => 1,
                                        'checked'        => isset($general_data->payment_method_id)&&($general_data->payment_method_id == 1)? set_checkbox("cheque", true, true) : set_checkbox("cheque", false, false),
                                        //set_checkbox("discount", false, false),
                                        );    
                            echo form_radio($cheque_data); echo lang('cheque');?> 
                        </label>
                       
                       <label class="radio-inline"> 
                           <?php $pay_pal_data = array(
                                        'name'           => "payment_method_id",
                                        'class'          => 'radio-inline pay_pal',
                                        'id'             => 'pay_pal_radio',
                                        'value'          => 0,
                                        'checked'        => isset($general_data->payment_method_id)&&($general_data->payment_method_id == 2)? set_checkbox("pay_pal", true, true) : set_checkbox("pay_pal", false, false),
                                        //'checked'        => set_checkbox("discount_amount", true, true),
                                        );    
                            echo form_radio($pay_pal_data); echo lang('pay_pal'); ?>
                        </label>
                        
                        <label class="radio-inline"> 
                           <?php $bank_transfer_data = array(
                                        'name'           => "payment_method_id",
                                        'class'          => 'radio-inline bank_transfer',
                                        'id'             => 'bank_transfer_radio',
                                        'value'          => 0,
                                        'checked'        => isset($general_data->payment_method_id)&&($general_data->payment_method_id == 3)? set_checkbox("bank_transfer", true, true) : set_checkbox("bank_transfer", false, false),
                                        //'checked'        => set_checkbox("discount_amount", true, true),
                                        );    
                            echo form_radio($bank_transfer_data); echo lang('bank_transfer'); ?>
                        </label>
                        </div><!--radio-list col-md-4 -->
                        </div><!--form-group -->
                
                        <div class="form-group" id="cheque" style="display: <?php echo isset($general_data->payment_method_id)&& $general_data->payment_method_id == 1? 'block' : 'none' ;?>" >
                               <label class="control-label col-md-3"><?php echo lang('cheque_name');?></label>
                                <div class="col-md-4" >
                                <?php  
                                       $cheque_name_data = array('name'=>"cheque_name" , 'class'=>"form-control" , 'value'=> isset($general_data->cheque_name)? $general_data->cheque_name : set_value('cheque_name',''));
                                       echo form_input($cheque_name_data);
                                ?>
                                </div>
                        </div><!--form-group -->
                        <div class="form-group" id="pay_pal" style="display: <?php echo isset($general_data->payment_method_id)&& $general_data->payment_method_id == 2? 'block' : 'none' ;?>" >
                           <label class="control-label col-md-3"><?php echo lang('pay_pal_email_account');?></label>
                            <div class="col-md-4">
                            <?php   
                                   $pay_pal_email_account_data = array('name'=>"pay_pal_email_account" , 'class'=>"form-control ", 'value'=> isset($general_data->pay_pal_email_account ) ? $general_data->pay_pal_email_account : set_value('pay_pal_email_account',''));
                                   echo form_input($pay_pal_email_account_data);
                            ?>
                            </div>
                        </div>
                        <div  id="bank_transfer" style="display: <?php echo isset($general_data->payment_method_id)&& $general_data->payment_method_id == 3? 'block' : 'none' ;?>" >
                            <div  class="form-group">
                                <label class="control-label col-md-3" ><?php echo lang('bank_name');?></label>
                                <div class="col-md-4" >
                                <?php   
                                       $bank_name_data = array('name'=>"bank_name" , 'class'=>"form-control ", 'value'=> isset($general_data->bank_name ) ? $general_data->bank_name : set_value('bank_name',''));
                                       echo form_input($bank_name_data);
                                ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3" ><?php echo lang('branch_number');?></label>
                                <div class="col-md-4" >
                                <?php      
                                       $branch_number_data = array('name'=>"branch_number" , 'class'=>"form-control ", 'value'=> isset($general_data->branch_number ) ? $general_data->branch_number : set_value('branch_number',''));
                                       echo form_input($branch_number_data);
                                 ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3" ><?php echo lang('swift_code');?></label>
                                <div class="col-md-4" >
                                <?php            
                                       $swift_code_data = array('name'=>"swift_code" , 'class'=>"form-control ", 'value'=> isset($general_data->swift_code ) ? $general_data->swift_code : set_value('swift_code',''));
                                       echo form_input($swift_code_data);
                                 ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3" ><?php echo lang('account_name');?>
                                    <span class="required">*</span>
                                </label>
                                <div class="col-md-4" >
                                <?php            
                                       $account_name_data = array('name'=>"account_name" , 'class'=>"form-control ", 'value'=> isset($general_data->account_name ) ? $general_data->account_name : set_value('account_name',''));
                                       echo form_input($account_name_data);
                                 ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3" ><?php echo lang('account_number');?>
                                    <span class="required">*</span>
                                </label>
                                <div class="col-md-4" >
                                <?php            
                                       $account_number_data = array('name'=>"account_number" , 'class'=>"form-control ", 'value'=> isset($general_data->account_number ) ? $general_data->account_number : set_value('account_number',''));
                                       echo form_input($account_number_data);
                                  ?>
                                </div>
                            </div>
                        </div><?php */?>
				</div><!--discount div-->
            </div>  
            </div><!--tab_payment_details -->
            
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
<script>
 $(".cheque").change(function () {    
     $('#cheque').show();
     $('#pay_pal').hide();
     $('#bank_transfer').hide();
   });
 
 $(".pay_pal").change(function () { 
     $('#cheque').hide();
     $('#pay_pal').show();
     $('#bank_transfer').hide();
   });
   
   $(".bank_transfer").change(function () { 
     $('#cheque').hide();
     $('#pay_pal').hide();
     $('#bank_transfer').show();
   });
   
   //////////////////////////////
     $(function(){
        $(".num_uses").TouchSpin({          
            buttondown_class: 'btn green',
            buttonup_class: 'btn green',
            min: 0,
            max: 1000000000,
            stepinterval: 1,
            maxboostedstep: 1,
        }); 
    });
    
    $(function(){
        $(".commission_spinner").TouchSpin({          
            buttondown_class: 'btn green',
            buttonup_class: 'btn green',
            min: 0,
            max: 1000000000,
            step: .1,
            stepinterval: 1,
            maxboostedstep: 1,
        }); 
    })
                    
                    
</script>  	