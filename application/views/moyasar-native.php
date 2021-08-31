<?php /*<form accept-charset="UTF-8" action="<?php echo $action?>" method="POST">

    <input type="hidden" name="callback_url" value="<?php echo $redirect_url;?>" />
    <input type="hidden" name="publishable_api_key" value="<?php echo $api_key;?>'">
    <input type="hidden" name="amount" value="<?php echo $total_in_halalas;?>">
    <input type="hidden" name="currency" value="<?php echo $currency_symbol;?>">
    <input type="hidden" name="source[type]" value="creditcard">

    <input type="hidden" name="order_id" value="<?php echo $order_id;?>">
    name<input type="text" name="source[name]" /><br>
    number<input type="text" name="source[number]" /><br>
    month<input type="text" name="source[month]" /><br>
    year<input type="text" name="source[year]" /><br>
    cvc<input type="text" name="source[cvc]" /><br>

    <button type="submit">Purchase</button>
</form>*/?>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<form accept-charset="UTF-8" id="form_id" action="https://api.moyasar.com/v1/payments.html" method="POST">

  <input type="hidden" name="callback_url" value="<?php echo base_url().'orders/payment_gateways/feed_back_moyasar/'.$order_id;// $redirect_url;?>" />
  <input type="hidden" name="publishable_api_key" value="pk_test_QGHvAeaWy6aKY26tPBCapFkKbUZcs3qY32uEzyvo<?php //echo $api_key;?>">
  <input type="hidden" name="amount" value="<?php echo $total_in_halalas;?>">
  <input type="hidden" name="currency" value="<?php echo $currency_symbol;?>">
  <input type="hidden" name="source[type]" value="creditcard">

  name<input type="text" name="source[name]" /><br>
  number<input type="text" name="source[number]" /><br>
  month<input type="text" name="source[month]" /><br>
  year<input type="text" name="source[year]" /><br>
  cvc<input type="text" name="source[cvc]" /><br>

<button type="submit" id="submit_button_id">Purchase</button>
</form>


<script>
$("#submit_button_id").click(function(event){
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
  window.location.href='<?php echo base_url().'orders/order/view_order_details/'.$order_id;?>';
})


  alert('yesssss');
}

});
});
</script>
