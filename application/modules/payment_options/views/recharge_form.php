<div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
	<div class="row no-margin">
    	<div class="iner_page">
          <div class="row no-margin">  
            
    		<h1 class="title_h1"><?php echo lang('recharge');?></h1>
            <div class="fail_message" style="display: none;"><i class="fa fa-exclamation-triangle"></i>  </div>
            <h1 class="type_recharge"><?php echo lang('balance_msg');?></h1>
				<div class="row no-margin top_header_recharge">
					<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 no-padding">
						<input id="balance_input" checked="checked"  type="radio" name="balance" value="1" class="charge_balance" />
						<label for="balance_input"><?php echo lang('balance');?></label>
					</div><!--col-->
					<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 no-padding">
						<input id="code" type="radio" name="balance" value="2" class="charge_card" />
						<label for="code"><?php echo lang('code');?></label>
					</div><!--col-->	
                </div><!--row-->
            <div id="balance">
                <form action="<?php echo base_url();?>shopping_cart/cart/add_to_cart" method="post">
                    <input type="hidden" name="type" value="recharge_card" />
                    <div class="row no-margin">
                    	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        	<div class="payment_bar">
                                <h1 class="title_h1"><?php echo lang('buy_balance');?></h1>
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-4">
									<label for="blan" class="line2_5"><?php echo lang('Amount_of_money');?></label>
								</div><!--col-->	
								<div class="col-lg-9 col-md-9 col-sm-9 col-xs-8">
									<input id="blan" name="balance" type="number" value="25" min="1" required="required" />
								</div><!---->
                            </div><!--payment_bar-->
                        </div><!--col-->
                       
                    </div><!--row-->
                    <div class="row no-margin footer_shopcart">
                    	
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                            <?php 
                                echo form_error('conditions');
                                
                                $active_value = true;
                                
                                $active_data  = array(
                                                       'name'           => 'conditions',
                                                       'id'             => 'priv',
                                                       'value'          => 1,
                                                       'checked'        => set_checkbox('active', $active_value, $active_value),
                                                       'data-on-text'   => lang('yes'),
                                                       'data-off-text'  => lang('no'),
                                                       'required'       => 'required'
                                                    );    
                                echo form_checkbox($active_data); 
                               ?>
                            <label for="priv"><?php echo lang('recharge_balance_condition');?> </label>
                        </div><!--col-->
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-left">
                        	<button class="btn bg-primary"><?php echo lang('continue');?></button>
                        </div><!--col-->
                        
                    </div><!--row-->
                </form>
        </div>
            
            
            <div class="registration" id="serial" style="display: none;">
                <form action="#" method="post">	
                    <div class="row no-margin">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        	<div class="payment_bar">
                                <h1 class="title_h1"><?php echo lang('charge_using_codes');?></h1>
                                <div class="row no-margin margin-bottom-10px">
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                        <label for="serial_input" class="line2_5"><?php echo lang('serial');?></label>
                                    </div>
                                    <div class="col-lg-9 col-md-9 col-sm-19 col-xs-12">
                                        <input name="serial" type="text" required="required" id="serial_input" />
                                    </div><!--col-->
                                </div>
                                <div class="row no-margin margin-bottom-10px">
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                        <label for="pin_input" class="line2_5"><?php echo lang('pin');?></label>
                                    </div>
                                    
                                    <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
                                        <input name="pin" type="text" required="required" id="pin_input" />
                                    </div><!--col-->
                                </div>
                            </div><!--payment_bar-->
                        </div><!--col--> 
                    </div><!--row-->
                    <div class="row no-margin footer_shopcart">
                        
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
                        	<button class="btn bg-primary" id="submit_card"><?php echo lang('submit');?></button>
                        </div><!--col-->
                    </div><!--row-->
                </form>
              </div>
            
            </div>
            
	    </div><!--iner_page-->
    </div><!--row-->
</div>

<script>
$('.charge_balance').click(function(){
    $('#balance').show();
    $('#serial').hide();
});

$('.charge_card').click(function(){
    $('#balance').hide();
    $('#serial').show();
});
////////////////////////////////////////////////////////////////
$('#submit_card').click(function(event){
    event.preventDefault();
    var serial = $("#serial_input").val();
    var pin    = $("#pin_input").val();
    
    
    if($.trim(serial) == '')
    {
        showToast('<?php echo lang('add_serial_first');?>', '', 'error');
    }
    if($.trim(pin) == '')
    {
        showToast('<?php echo lang('add_pin_first');?>', '', 'error');
    }
    if($.trim(pin) != '' && $.trim(serial) != '')
    {
        var postData = {
                         serial: serial,
                         pin   : pin
                       };
        $.post('<?php echo base_url();?>payment_options/user_balance/charge_with_code', postData, function(result){
            if(result == '<?php echo lang('no_data_about_this_card') ?>' )
            {
                showToast(result, '', 'warning');
            }
            else if( result == '<?php echo lang('card_used_before')?>')
            {
                showToast(result, '', 'warning');
            }
            else
            {
                showToast(result, '', 'success');
            }
        });
    }
    
});

</script>
