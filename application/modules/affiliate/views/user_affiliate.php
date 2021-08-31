<div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
	<div class="row no-margin">
    	<div class="iner_page">
        	<h1 class="title_h1"> <?php echo lang('user_affiliate_log');?></h1>
			<div class="row no-margin margin-bottom-10px">
            	<?php if (isset($user_affiliate_code) ){?>
                    <div class="alert alert-success">
                        <span><?php echo lang('user_affiliate_url')."  :  ";?> </span>
                        <span><?php echo base_url();?>users/register/affiliate/<?php echo $user_affiliate_code;?></span>
                        <!--<a href="<?php echo base_url();?>users/register/affiliate/<?php echo $user_affiliate_code;?>"><?php echo lang('user affiliate_url');?> </a>-->
                    </div>
                <?php }?>
            </div><!--row-->
            
            <?php if(!empty($user_affiliate_log)){?>
            <div class="table_ticket">
            	<table class="table  table-striped table-hover table-bordered">
                	<tr class="header_tr">
                        <td align="right"><?php echo lang('buyer');?></td>
                        <td align="right"><?php echo lang('order_amount');?></td>
                        <td align="right"><?php echo lang('commission');?></td>
                        <td align="right"><?php echo lang('amount');?></td>
                        <td align="right"><?php echo lang('unix_time');?></td>
                        <td align="right"><?php echo lang('pay_stat');?></td>
                    </tr>
                    <?php foreach($user_affiliate_log as $row){?>
                        <tr>
                        	<td align="right"><?php echo $row->first_name.' '.$row->last_name ;?></td>
                            <td align="right"><?php echo $row->final_total.' '.$row->currency_symbol;?></td>
                            <td align="right"><?php echo $row->commission;?></td>
                            <td align="right"><?php echo $row->amount.' '.$row->currency_symbol;?></td>
                            <td align="right"><?php echo date('Y/m/d - H:i ',$row->unix_time);?></td>
                            <td align="right">
                                <?php 
                                    if($row->pay == 0)
                                    {
                                        $pay = '<span class="badge badge-danger">'.lang('no').'</span>';    
                                    }
                                    elseif($row->pay == 1)
                                    {
                                        $pay = '<span class="badge badge-success">'.lang('yes').'</span>';
                                    }
                                ?>
                                <?php echo $pay;//$row->pay;?>
                            </td>
                        </tr>
                    <?php }?>
                    
                </table>
            </div><!--table_ticket--> 
            <?php }else{ ?>
                <p class="offer_msg"><?php echo lang ('no_aflliate_found');?></p>
            <?php } ?>
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