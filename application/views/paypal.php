<?php
//include_once("config.php");
?>
<style type="text/css">
<!--
body{font-family: arial;color: #7A7A7A;margin:0px;padding:0px;}
.procut_item {width: 550px;margin-right: auto;margin-left: auto;padding: 20px;background: #F1F1F1;margin-bottom: 1px;font-size: 12px;border-radius: 5px;text-shadow: 1px 1px 1px #FCFCFC;}
.procut_item h4 {margin: 0px;padding: 0px;font-size: 20px;}
-->
</style>

<h2 align="center">Test Products</h2>
<div class="product_wrapper">
<form method="post" action="<?php echo base_url();?>test2/submit">
<table class="procut_item" border="0" cellpadding="4">
  <tr>
    <td width="70%"><h4>Canon EOS Rebel XS</h4>(Capture all your special moments with the Canon EOS Rebel XS/1000D DSLR camera and cherish the memories over and over again.)</td>
    <td width="30%">
	<input type="hidden" name="itemname[]" value="Canon EOS Rebel XS" /> 
    <input type="hidden" name="itemdesc[]" value="Capture all your special moments with the Canon EOS Rebel XS/1000D DSLR camera and cherish the memories over and over again." /> 
	<input type="hidden" name="itemprice[]" value="1.00" />
    Quantity : <select name="itemQty[]"><option value="1">1</option><option value="2">2</option><option value="3">3</option></select>
    </td>
  </tr>
</table>

<table class="procut_item" border="0" cellpadding="4">
  <tr>
    <td width="70%"><h4>Canon EOS Rebel XS2</h4>(Capture2 all your special moments with the Canon EOS Rebel XS/1000D DSLR camera and cherish the memories over and over again.)</td>
    <td width="30%">
	<input type="hidden" name="itemname[]" value="Canon EOS Rebel XS2" /> 
    <input type="hidden" name="itemdesc[]" value="Capture2 all your special moments with the Canon EOS Rebel XS/1000D DSLR camera and cherish the memories over and over again." /> 
	<input type="hidden" name="itemprice[]" value="7.00" />
    Quantity : <select name="itemQty[]"><option value="1">1</option><option value="2">2</option><option value="3">3</option></select>
     
    </td>
  </tr>
</table>

<table class="procut_item" border="0" cellpadding="4">
  <tr>
    <td width="70%"><h4>Canon EOS Rebel XS3</h4>(Capture3 all your special moments with the Canon EOS Rebel XS/1000D DSLR camera and cherish the memories over and over again.)</td>
    <td width="30%">
	<input type="hidden" name="itemname[]" value="Canon EOS Rebel XS3" /> 
    <input type="hidden" name="itemdesc[]" value="Capture3 all your special moments with the Canon EOS Rebel XS/1000D DSLR camera and cherish the memories over and over again." /> 
	<input type="hidden" name="itemprice[]" value="3.00" />
    Quantity : <select name="itemQty[]"><option value="1">1</option><option value="2">2</option><option value="3">3</option></select>
     
    </td>
  </tr>
</table>

<table class="procut_item" border="0" cellpadding="4">
  <tr>
    <td width="70%"><h4>Canon EOS Rebel XS4</h4>(Capture4 all your special moments with the Canon EOS Rebel XS/1000D DSLR camera and cherish the memories over and over again.)</td>
    <td width="30%">
	<input type="hidden" name="itemname[]" value="Canon EOS Rebel XS4" /> 
    <input type="hidden" name="itemdesc[]" value="Capture4 all your special moments with the Canon EOS Rebel XS/1000D DSLR camera and cherish the memories over and over again." /> 
	<input type="hidden" name="itemprice[]" value="5.00" />
    Quantity : <select name="itemQty[]"><option value="1">1</option><option value="2">2</option><option value="3">3</option></select>
     
    <input class="dw_button" type="submit" name="submitbutt" value="Buy (225.00 <?php echo 'USD'; ?>)" />
    </td>
  </tr>
</table>
</form>
</div>
</body>
</html>