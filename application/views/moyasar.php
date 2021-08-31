<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SEJJADCOM</title>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/template/payment/css/payment_creditcard.css">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
</head>

<body>
    <div class="container-fluid">


        <div class="creditCardForm">
            <div class="heading">
                <h1><?php echo lang('confirm_payment');?></h1>
            </div>
            <div class="payment">
                <form accept-charset="UTF-8" id="form_id" action="https://api.moyasar.com/v1/payments.html" method="POST">
                    <div class="form-group owner">
                        <label for="owner"><?php echo lang('owner_name');?></label>
                        <input type="text" class="form-control" id="userName" name="source[name]" />
                    </div>
                    <div class="form-group CVV">
                        <label for="cvv">CVV</label>
                        <input type="text" class="form-control" id="CVC" name="source[cvc]" />
                    </div>
                    <div class="form-group" id="card-number-field">
                        <label for="cardNumber"><?php echo lang('card_number');?></label>
                        <input type="text" class="form-control" id="cardNumber" name="source[number]" />
                    </div>
                    <div class="form-group" id="expiration-date">
                        <label><?php echo lang('expire_date');?></label>
                        <select id="year" name="source[month]">
                            <option value="1">January</option>
                            <option value="2">February </option>
                            <option value="3">March</option>
                            <option value="4">April</option>
                            <option value="5">May</option>
                            <option value="6">June</option>
                            <option value="7">July</option>
                            <option value="8">August</option>
                            <option value="9">September</option>
                            <option value="10">October</option>
                            <option value="11">November</option>
                            <option value="12">December</option>
                        </select>
                        <select id="mon" name="source[year]">
                            <option value="<?php echo date('Y');?>"> <?php echo date('Y');?></option>
                            <?php for($i=1;$i<=10;$i++){?>
                              <option value="<?php echo date('Y')+$i;?>"> <?php echo date('Y') +$i;?></option>
                            <?php }?>

                        </select>
                    </div>
                    <div class="form-group" id="credit_cards">
                        <img src="<?php echo base_url();?>assets/template/payment/assets/images/visa.jpg" id="visa">
                        <img src="<?php echo base_url();?>assets/template/payment/assets/images/mastercard.jpg" id="mastercard">
                    </div>
                    <input type="hidden" name="callback_url" value="<?php echo $redirect_url;?>" />
                    <input type="hidden" name="publishable_api_key" value="pk_test_QGHvAeaWy6aKY26tPBCapFkKbUZcs3qY32uEzyvo<?php //echo $api_key;?>">
                    <input type="hidden" name="amount" value="<?php echo $total_in_halalas;?>">
                    <input type="hidden" name="currency" value="<?php echo $currency_symbol;?>">
                    <input type="hidden" name="source[type]" value="creditcard">

                    <div class="form-group" id="pay-now">
                        <button type="button" class="btn btn-default" id="submit_form"><?php echo lang('confirm');?></button>
                    </div>
                    
                </form>
            </div>
        </div>


    </div>
</body>

<script>
$("#submit_form").click(function(event){
  event.preventDefault();
  // Get form data
  var form_data = $("#form_id").serialize();
  // Sending a POST request to Moyasar API using AJAX
  $.ajax({
  url: "https://api.moyasar.com/v1/payments",
  type: "POST",
  data: form_data,
  dataType: "json",
})
// uses `.done` callback to handle a successful AJAX request
.done(function(data) {
// Here we will handle JSON response and do step3 & step4
if (data.id == 'undefined') {

  alert(data.message);
  alert(data.errors);

}
else {
  // Save the payment id in your System
  var payment_id = data.id;
//alert(payment_id);

$.post('<?php echo base_url().'orders/payment_gateways/feed_back_moyasar/'.$order_id;?>', data, function(){
  // Redirect the user to transaction_url
  var url = data.source.transaction_url;
  window.location.href = url;
  
  //window.location.href='<?php echo base_url().'orders/order/view_order_details/'.$order_id;?>';
})


//  alert('yesssss');
}

});
});
</script>

</html>
