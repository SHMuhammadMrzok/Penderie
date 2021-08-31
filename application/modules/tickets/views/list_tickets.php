    <section class="predcramp">
        <div class="container no-padding">
            <ul>
                <li><a href="<?php echo base_url();?>"><?php echo lang('home');?></a></li>
                <li><span>/</span></li>
                <li><a href="<?php echo base_url();?>tickets/tickets/"><?php echo lang('tickets_mangement');?></a></li>
            </ul>
        </div><!--container-->
    </section><!--predcramp-->
    
    <main class="no-padding-top">
        <div class="container no-padding">
            <div class="row"><div class="col-md-3">
                    
                    <div class="registration">
                    <div class="title-page">
                        <h4><?php echo lang('my_personal');?></h4>
                    </div><!--title-page-->

                        <div class="sum-cart">
                            <div class="row no-margin">
                                <ul>
                                    <li><a href="<?php echo base_url();?>Edit_Profile"><?php echo lang('edit_mydata');?></a></li>
                                    <li class="line"></li>
                                    <li><a href="<?php echo base_url();?>users/user_address/list"><?php echo lang('user_address');?></a></li>
                                    <li class="line"></li>
                                    <li><a href="<?php echo base_url();?>products/products/user_wishlist"><?php echo lang('wishlist');?></a></li>
                                    <li class="line"></li>
                                    <li><a href="<?php echo base_url();?>Compare_Products"><?php echo lang('compare_products');?></a></li>
                                    <li class="line"></li>
                                    <?php /*
                                    <li><a href="<?php echo base_url();?>Balance_Recharge"><?php echo lang('recharge');?></a></li>
                                    <li class="line"></li>
                                    */ ?>
                                    <li><a href="<?php echo base_url();?>Orders_Log"><?php echo lang('orders_log');?></a></li>
                                    <li class="line"></li>
                                    <?php /*
                                    <li><a href="<?php echo base_url();?>Payment_Log"> <?php echo lang('balance_details');?></a></li>
                                    <li class="line"></li>
                                    */ ?>
                                    <li><a href="<?php echo base_url();?>Support_Tickets"> <?php echo lang('support_tickets');?></a></li>
                                    <li class="line"></li>
                                    <li><a href="<?php echo base_url();?>User_logout"> <?php echo lang('logout');?></a></li>
                                </ul>
                            </div><!--row-->
                        </div><!--sum-cart -->
 	   	 			</div><!--registration-->
 	   	 		</div><!--col-->
         <div class="col-md-9">
                <div class="title-page">
 	   	 	        <h4><?php echo lang('tickets_mangement');?></h4>
 	   	 	    </div><!--title-page-->
                
                <div class="row no-margin margin-bottom-10px">
                    <a href="<?php echo base_url();?>New_Ticket" class="btn btn-default"><?php echo lang('new_ticket')?></a>
                </div><!--row-->
                        
                <?php if(isset($tickets)&& !empty($tickets)){?>
                    <div class="order-records">
                        <table class="table table-bordered table-hover">
                            <tr class="header-table">   
                                <td>#</td>
                                <td><?php echo lang('title');?></td>
        						<td><?php echo lang('ticket_department');?></td>
        						<td><?php echo lang('ticket_status');?></td>
        						<td><?php echo lang('unix_time');?></td>
        						<td><?php echo lang('last_updated_by');?></td>
        						<td><?php echo lang('last_update_unix_time');?></td>
        						<td></td>
                            </tr>
                            <?php foreach($tickets as $ticket){?>
                                <tr>
                                    <td><?php echo $ticket->id;?></td>
                                    <td><?php echo $ticket->title;?></td>
                                    <td><?php echo $ticket->cat_title;?></td>
                                    <td>
                                        <span class="label label-<?php echo $ticket->class;?>"><?php echo $ticket->status_title;?></span>
                                    </td>
                                    <td><?php echo date('Y/m/d H:i:s',$ticket->unix_time);?></td>
                                    <td><?php echo $ticket->last_updated_by_name;?></td>
                                    <td><?php echo  date('Y/m/d H:i:s',$ticket->last_update_unix_time);?></td>
                                    <td>
                                        <a href="<?php echo base_url();?>Ticket_Details/<?php echo $ticket->id;?>" class="btn btn-default"><?php echo lang('details')?></a>
                                    </td>
        						</tr>
                            <?php }?>
                        </table>
                    </div><!--order-records-->
                    
                    
                    <?php if(isset($pagination) && !empty($pagination)){?>
                        <div class="row no-margin">
                            <div class="pagination-area">
                                <ul>
                                    <?php echo $pagination;?>
                                </ul>
                            </div><!--pagination-area-->
                        </div>
                    <?php }?>
                    
              <?php }?>
            </div></div><!--row-->
        </div><!--container-->
    </main>