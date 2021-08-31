<div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
	<div class="row no-margin">
    	<div class="iner_page">
            <h1 class="title_h1"><?php echo lang('confirm_payment_page');?></h1>
            
            <form method="post" action="https://secure.payfort.com/ncol/test/orderstandard.asp" id="form1" name="form1" style="text-align: center;">
                <!-- general parameters: see General Payment Parameters -->
                <input type="hidden" name="PSPID" value="<?php echo $pspid;?>" />
                <input type="hidden" name="ORDERID" value="<?php echo $order_id;?>" />
                <input type="hidden" name="AMOUNT" value="<?php echo $final_total*100;?>" />
                <input type="hidden" name="CURRENCY" value="<?php echo $currency;?>" />
                <input type="hidden" name="LANGUAGE" value="<?php echo $language;?>" />
                
                <input type="hidden" name="SHASIGN" value="<?php echo $shasign;?>" />
                <?php if($text){?>
                    <div class="text">
                      <?php echo $text;?>
                    </div>
                <?php }?>
                <input type="submit" value="<?php echo lang('go_to_payment');?>" id="submit2" name="SUBMIT2" class="btn btn-primary" />
            </form>
        </div>
    </div>
</div>
<style>
.btn
{
    display: inline-block;
    margin-bottom: 0;
    font-weight: 400;
    text-align: center;
    vertical-align: middle;
    cursor: pointer;
    background-image: none;
    border: 1px solid transparent;
    white-space: nowrap;
    padding: 6px 12px;
    font-size: 14px;
    line-height: 1.42857143;
    border-radius: 4px;
    -webkit-user-select: none;
}

.blue.btn:hover
{
    color: #FFFFFF;
    background-color: #2977f7
}
    
</style>