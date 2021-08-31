<div class="portlet-body">
    <div>
        <div class="row" style="margin: 5px; margin-bottom: 15px;">
            <label class="control-label col-md-3"><?php echo lang('user_current_balance');?></label>
            <div class="col-md-4">
              <span class="user_balance_val alert alert-success"><?php echo $user_balance;?></span>
              <span class="alert alert-success"><?php echo $currency_symbol;?></span>
           </div>
        </div>
        <span id="add_amount"></span>
        
        <div class="row" style="margin: 5px;">
            <label class="control-label col-md-3"><?php echo lang('money_amount');?></label>
           <div class="col-md-4">
              <?php 
                   echo form_error("amount");  
                   $amount_data = array('name'=>"amount", 'class'=>"form-control amount_spinner", 'id'=>'amount', 'value'=> set_value('amount'));
                   echo form_input($amount_data);
              ?>
           </div>
        </div>
        
        <div class="row" style="margin: 5px;">
            <label class="control-label col-md-3"><?php echo lang('type');?></label>
           <div class="col-md-4">
              <?php 
                   echo form_error("type");  
                   echo form_dropdown('type', $types, 0, 'class="form-control select2" id="type"');
              ?>
           </div>
        </div>
        <input type="hidden" name="user_id" value="<?php echo $user_id;?>" id="user_id" />
        <button type="submit" style="margin: 5px;" class="btn green-meadow" id="submit_btn"><?php echo lang('submit');?></button>
    </div>
    <div class="table-scrollable">
        <table class="table table-hover" id="balance_log_table">
        <thead>
            <tr>
                <td ><?php echo lang('unix_time');?></td>
                <td ><?php echo lang('order_id');?></td>
                <td ><?php echo lang('payment_type');?></td>
                <td ><?php echo lang('money_amount');?></td>
                <td ><?php echo lang('currency');?></td>
                <td ><?php echo lang('description');?></td>
                <td ><?php echo lang('ip_address');?></td>
                <td ><?php echo lang('balance');?></td>
                <td ><?php echo lang('type');?></td>
                <td ><?php echo lang('added_by');?></td>
            </tr>
        </thead>
        <tbody>
        <?php 
        if(count($user_balance_data) != 0)
        {
            foreach($user_balance_data as $balance_log)
            {
        ?>
        <tr>
            <td><?php echo date('Y/m/d H:i', $balance_log->unix_time);?></td>
            <td>
                <?php if($balance_log->order_id != 0){?>
                    <a href="<?php echo base_url() . 'orders/admin_order/view_order/' . $balance_log->order_id;?>" target="_blank"><?php echo $balance_log->order_id;?></a></td>
                <?php }else{?>-<?php }?>
            <td><?php echo $balance_log->method;?></td>
            <td><?php echo $balance_log->amount;?></td>
            <td><?php echo $balance_log->currency_symbol;?></td>
            <td><?php echo $balance_log->status;?></td>
            <td><?php echo $balance_log->ip_address;?></td>
            <td><?php echo $balance_log->balance;?></td>
            <td><?php echo $balance_log->type;?></td>
            <td><?php echo $balance_log->added_by;?></td>
        </tr>
        <?php
            }
        }
        else
        {
        ?>
          <tr><td colspan="6" style="text-align: center;"><?php echo lang('no_data');?></td></tr>
        <?php
        }
        ?>
        </tbody>
        </table>
    </div>
    <?php if(count($user_balance_data) != 0){?>
        <ul class="pagination"><?php echo $page_links; ?></ul>
    <?php }?>
</div>

<script>
  $(function(){
        $(".amount_spinner").TouchSpin({          
            buttondown_class: 'btn red',
            buttonup_class: 'btn red',
            min: .1,
            max: 1000000000,
            step: .1,
            stepinterval: 1,
            maxboostedstep: 1,
            
        }); 
    })
</script>

<script>
    $('#submit_btn').click(function(event){
        event.preventDefault();
        
        var user_id = $('#user_id').val();
        var amount  = $('#amount').val();
        var type    = $('#type').val();
        
        if(amount == '')
        {
            $('#validation_msg').html('<?php echo lang('amount_field_is_required');?>');
        }
        else
        {
            var postData = {
                               user_id : user_id,
                               amount  : amount,
                               type    : type 
                           };
            
            $.post('<?php echo base_url().'users/admin_users/update_user_balance';?>', postData, function(result){
                if(result[0] == 1)
                {
                    showToast(result[1],'<?php echo lang('success');?>','success');
                    $('.user_balance_val').html(result[2]);
                    //$('#balance_log_table tbody tr:first').before(result[3]);
                    $('#balance_log_table tbody ').prepend(result[3]);
                }
                else
                {
                    showToast(result[1],'<?php echo lang('error');?>','error');
                }
                
            }, 'json');
        }
        
    });
</script>