<div class="user_account" id="user_acc_<?php echo $bank_id;?>" >
    <?php
     if(isset($general_data->id))
     {
        echo form_hidden('user_bank_account_id', $general_data->id); 
     }
     
     echo lang('bank_account_name');
    
     echo form_error('account_name');
     $acc_att = array('name'=>"account_name_$bank_id",'id'=>"account_name_$bank_id", 'style'=>'display:block', 'value'=> isset($general_data->account_name)? $general_data->account_name : set_value("account_name"), 'required'=>'required');
     
     echo form_input($acc_att); 
    
    
     echo lang('bank_account_number');
    
     echo form_error('account_numer');
     $acc_number_att = array('name'=>"account_number_$bank_id", 'id'=>"account_number_$bank_id", 'style'=>'display:block', 'value'=> isset($general_data->account_number)? $general_data->account_number : set_value("account_code"), 'required'=>'required');
     echo form_input($acc_number_att); 
    ?>
    <input type="hidden" name="bank_id" value="<?php echo $bank_id;?>" id="bank_id" />
</div>