<h1 class="title_h1"><?php echo lang('payment_options');?></h1>
<span class="validation_error" style="color: red; text-align: center; display: block; overflow: hidden; font: message-box;" ></span>
<?php //echo validation_errors();?>
<div class="row no-margin">
    <?php foreach($payment_options as $option)
     {
        // if banks
        if($option->id == 3)
        {?>
            <div class="row no-margin margin-bottom-10px">
            	<?php

                 echo form_error('payment_method');

                 $payment_method_data = array(
                                                'name'  => "payment_method",
                                                'class' => 'bank_btn other_payment_options',
                                                'value' => $option->id,
                                                'id'    => 'bank_payment_selection'
                                             );
                 echo form_radio($payment_method_data);
                ?>

                <label for="options_input_<?php echo $option->id;?>"><?php echo $option->name;?></label>
            </div><!--row-->

            <?php echo form_error('bank_id'); ?>
             <div id="bank_options_div" style="display: none; margin-left: 20px; overflow: hidden;">
                 <?php
                 foreach($bank_accounts as $account){?>
                    <div class="row no-margin margin-bottom-10px">
                    	<input type="radio" name="bank_id" class="bank_btn" data-bank_id="<?php echo $account->id;?>" value="<?php echo $account->id;?>" id="bank_input_<?php echo $account->id;?>" />
                        <label for="bank_input_<?php echo $account->id;?>"><?php echo $account->bank;?></label>
                    </div><!--row-->
                    <div style="display:none ;" id="bank_details_<?php echo $account->id;?>" class="bank_details">
                        <div class="loader">
                            <div id="bank_data_<?php echo $account->id;?>" class="bank_acc_data" style="display: ;">
                                <div class="row no-margin">
                                     <div class="name_acc"> <?php echo lang('account_name');?> :</div>
                                     <div class="name_a"><?php echo $account->bank_account_name;?></div>
                                 </div><!--row-->
                                 <div class="row no-margin">
                                      <div class="name_acc"><?php echo lang('account_number');?> :</div>
                                      <div class="name_a"><?php echo $account->bank_account_number; ?></div>
                                 </div><!--row-->
                            </div>

                            <div class="user_bank_accounts" id="bank_acc_<?php echo $account->id;?>"></div>
                        </div><!--loader-->
                        <?php /*<div class="user_account" id="user_acc_<?php echo $account->id;?>" >
                            <div class="row no-margin">
        											<?php
                                 if(isset($account->user_bank_id))
                                 {
                                    echo form_hidden('user_bank_account_id', $account->user_bank_account_id);
                                 }
                                 ?>
                                 <div class="row no-margin margin-top-10px">
		                                 <div class="name_acc">
				                             	<?php echo lang('your_account_name').' : '; ?>
				                             </div><!--name_acc-->
		                                 <div class="name_a">
				                             <?php
				                                 echo form_error('account_name');
				                                 $acc_att = array('name'=>"account_name[$account->id]",'id'=>"account_name_$account->user_bank_account_id", 'style'=>'display:block', 'value'=> isset($account->user_bank_account_name)? $account->user_bank_account_name : set_value("account_name"), 'data-bank_id'=> $account->id);

				                                 echo form_input($acc_att);
				                             ?>
				                            </div><!--name_a-->
		                            </div><!--row-->
                                 <div class="row no-margin margin-top-10px">
	                                 <div class="name_acc">
				                            <?php
				                             echo lang('your_account_number').' : ';
				                            ?>
				                           </div><!--name_acc-->
	                                 <div class="name_a">
				                            <?php
				                             echo form_error('account_numer');
				                             $acc_number_att = array('name'=>"account_number[$account->id]", 'id'=>"account_number_$account->user_bank_account_id", 'style'=>'display:block', 'value'=> isset($account->user_bank_account_number)? $account->user_bank_account_number : set_value("account_code"), 'data-bank_id'=> $account->id);
				                             echo form_input($acc_number_att);
				                            ?>
				                            <input type="hidden" name="bank_id2" value="<?php echo $account->id;?>" data-bank_id="<?php echo $account->id;?>" />
				                           </div><!--name_a-->
	                            </div><!--row-->
                            </div><!--row-->
                        </div><!--user_account-->
												*/?>
                    </div><!--bank_details_-->
                 <?php }?>

             </div><!--bank_options-->

        <?php }

        else{?>
            <div class="row no-margin margin-bottom-10px">
            	<?php

                 echo form_error('payment_method');
                 if($option->id == 7)
                 {
                    $id    = 'voucher';
                    $class = '';
                 }
                 else
                 {
                    $id    = 'options_input_'.$option->id;
                    $class = 'payment_options other_payment_options';
                 }
                 $payment_method_data = array(
                                                'name'  => 'payment_method',
                                                'class' => $class,
                                                'value' => $option->id,
                                                'id'    => $id
                                             );
                 echo form_radio($payment_method_data);
                ?>

                <label for="options_input_<?php echo $option->id;?>"><?php echo $option->name;?></label>
                <?php if($option->id == 7){?>
                    <input name="voucher" type="text" class="voucher_input" style="display: none;"/>
                <?php }?>
            </div><!--row-->
     <?php }
     }?>

</div>
