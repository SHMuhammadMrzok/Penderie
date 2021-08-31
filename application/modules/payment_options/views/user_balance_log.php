<div class="breadcrumb">
  <div class="container">
    <div class="breadcrumb-inner">
      <ul class="list-inline list-unstyled">
        <li><a href="<?php echo base_url();?>"><?php echo lang('home');?></a></li>
        <li class='active'><?php echo lang('balance_operations');?></li>
      </ul>
    </div>
  </div>
</div>

<main>
  <div class="container">

    <div class="row">
      <?php $this->load->view('site/user_menu');?>
      <div class="col-md-8">
        		<div class="about-us">

            <div class="form-page">



        			<div class="row no-margin margin-bottom-10px">
                    	<div class="alert alert-success"><?php echo lang('current_balance').": ".$user_balance;?> </div>
                    </div><!--row-->
                    <div class="table_ticket">
                    	<table class="table  table-striped table-hover table-bordered">
                        	<tr class="header_tr">
                                <td align="right"><?php echo lang('adding_date');?></td>
                                <td align="right"><?php echo lang('order_id');?></td>
                                <td align="right"><?php echo lang('payment_type');?></td>
                                <td align="right"><?php echo lang('amount');?></td>
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
                                        <a href="<?php echo base_url().'orders/order/view_order_details/'.$row->order_id;?>"><?php echo $row->order_id;?></a>
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
                        <div class="pagination-container">
                            <ul>
                                <?php echo $pagination;?>
                            </ul>

                        </div><!--row-->
                    <?php }?>
                   </div><!--form-page-->
                </div><!--login-->

        </div><!--row-->
				</div><!--row-->
    </div><!--container-->
</main>
