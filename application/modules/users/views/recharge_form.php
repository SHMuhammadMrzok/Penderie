<div class="breadcrumb">
  <div class="container">
    <div class="breadcrumb-inner">
      <ul class="list-inline list-unstyled">
        <li><a href="<?php echo base_url();?>"><?php echo lang('home');?></a></li>
        <li class='active'><?php echo lang('recharge');?></li>
      </ul>
    </div>
  </div>
</div>
<main>
    <div class="container">
        <div class="row">
            <?php $this->load->view('site/user_menu');?>
              <div class="col-md-8">
                  <div class="balance-title">
                      <h3><?php echo lang('recharge');?></h3>
                      <p><?php echo lang('balance_msg');?></p>
                    </div>
                    <div class="balance-recharge">
                      <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                          <a class="nav-link active" id="balance-tab" data-toggle="tab" href="#balance" role="tab"
                            aria-controls="balance" aria-selected="true"><?php echo lang('balance');?></a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" id="code-tab" data-toggle="tab" href="#code" role="tab" aria-controls="cose"
                            aria-selected="false"><?php echo lang('code');?></a>
                        </li>

                      </ul>
                      <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="balance" role="tabpanel" aria-labelledby="balance-tab">
                          <h4><?php echo lang('buy_balance');?></h4>
													<form action="<?php echo base_url();?>shopping_cart/cart/add_to_cart" method="post">
	                            <input type="hidden" name="type" value="recharge_card" />
                            <div class="form-group">
                              <label><?php echo lang('Amount_of_money');?></label>
                              <div class="d-flex">
                                <input id="blan" name="balance" type="number" value="25" min="1" required="required" class="form-control"/>
                                <span><?php echo $currency_symbol;?></span>
                              </div>

                            </div>
                            <div class="form-group">
                              <?php
																echo form_error('conditions');

																$active_value = true;

																$active_data  = array(
																											 'name'           => 'conditions',
																											 'id'             => 'agree',
																											 'value'          => 1,
																											 'checked'        => set_checkbox('active', $active_value, $active_value),
																											 'data-on-text'   => lang('yes'),
																											 'data-off-text'  => lang('no'),
																											 'required'       => 'required'
																										);
																echo form_checkbox($active_data);
															 ?>
                              <label for="agree"><?php echo lang('recharge_balance_condition');?></label>
                            </div>
                            <div class="form-group">
                              <button class="button"><?php echo lang('continue');?></button>
                            </div>
                          </form>
                        </div>
                        <div class="tab-pane fade" id="code" role="tabpanel" aria-labelledby="code-tab">
                          <h4><?php echo lang('charge_using_codes');?></h4>

                          <form action="#" method="post">
                            <div class="form-group">
                              <label><?php echo lang('serial');?></label>
                              <input name="serial" type="text" required="required" id="serial_input" class="form-control" />
                            </div>

                            <div class="form-group">
                              <label><?php echo lang('pin');?></label>
                              <input name="pin" type="text" required="required" id="pin_input" class="form-control"/>
                            </div>

                            <div class="form-group">
                              <button class="button" id="submit_card"><?php echo lang('continue');?></button>
                            </div>
                          </form>

                        </div>
                      </div>
                    </div>
              </div>
        </div>
    </div>
</main>

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
        $.post('<?php echo base_url();?>users/user_balance/charge_with_code', postData, function(result){
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
                window.location = "<?php echo base_url();?>Payment_Log";
            }
        });
    }

});

</script>
