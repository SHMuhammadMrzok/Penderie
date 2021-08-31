<div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
	<div class="row no-margin">
    	<div class="iner_page">
        	<h1 class="title_h1"> <?php echo lang('balance_operations');?></h1>
			<div class="row no-margin margin-bottom-10px">
            	<div class="alert alert-success"><?php echo lang('current_balance').": ".$user_balance;?> </div>
            </div><!--row-->
            <div class="table_ticket">
            	<table class="table  table-striped table-hover table-bordered">
                	<tr class="header_tr">
                        <td align="right"><?php echo lang('adding_date');?></td>
                        <td align="right"><?php echo lang('order_id');?></td>
                        <td align="right"><?php echo lang('payment_type');?></td>
                        <td align="right"><?php echo lang('money_amount');?></td>
                        <td align="right"><?php echo lang('currency');?></td>                        
                        <td align="right"><?php echo lang('description');?></td>
                        <td align="right"><?php echo lang('balance');?></td>
                    </tr>
                    <?php if($balance_log){
                        foreach($balance_log as $row){?>
                        <tr>
                        	<td align="right"><?php echo date('Y-m-d H:i', $row->unix_time);?></td>
                            <td align="right">
                                <?php if($row->order_id != 0){?>
                                    <a href="<?php echo base_url() . 'orders/order/view_order_details/' . $row->order_id;?>"><?php echo $row->order_id;?></a>
                                <?php }?>
                            </td>
                            <td align="right"><?php echo $row->method;?></td>
                            <td align="right"><?php echo $row->amount;?></td>
                            <td align="right"><?php echo $row->currency_symbol;?></td>                            
                            <td align="right"><?php echo $row->status;?></td>
                            <td align="right"><?php echo $row->balance;?></td>
                        </tr>
                    <?php }
                    }else{?>
                    <tr><td colspan="5"><?php echo lang('no_data');?></td></tr>
                    <?php }?>
                    
                </table>
            </div><!--table_ticket--> 
            <?php if($pagination){?>
                <div class="row no-margin text-center">
                	<nav>
                      <ul class="pagination">
                          <?php echo $pagination;?>
                      </ul>
                    </nav>
                </div><!--row-->
            <?php }?>
	    </div><!--iner_page-->
    </div><!--row-->
</div>