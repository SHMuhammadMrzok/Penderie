    <section class="predcramp">
        <div class="container no-padding">
            <ul>
                <li><a href="<?php echo base_url();?>"><?php echo lang('home');?></a></li>
                <li><span>/</span></li>
                <li><a href="<?php echo base_url();?>New_Ticket"><?php echo lang('new_ticket')?></a></li>
            </ul>
        </div><!--container-->
    </section><!--predcramp-->
    
    <main class="no-padding-top">
                <div class="container">
                    <div class="row">
                    <div class="col-md-3">
                    
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
                        <div class="center-div">
                            <div class="title">
                                <h3><?php echo lang('new_ticket');?></h3>
                            </div><!--title-->
                            
                            <div class="comment-area">
                                <form method="post" action="<?php echo base_url();?>tickets/tickets/new_ticket" enctype="multipart/form-data">
                                    <div class="row-form required">
    									<label for="input-firstname"><?php echo lang('title');?></label>
    									
                                        <?php
                                            $title_att = array(
                                                                'id'          => 'subject',
                                                                'class'       => 'form-control',
                                                                'name'        => 'title',
                                                                'placeholder' => lang('title'),
                                                                'required'    => 'required',
                                                                'value'       => set_value('title')
                                                              );
                                            echo form_input($title_att);
                                            echo form_error('title');
                                        ?>
                                    </div>
                                    
                                    <div class="row-form">
    									<label><?php echo lang('attached_files').' ('.lang('browse').')';?></label>
                                        <input type="file" name="userfile[]" class="form-control" multiple  />
    								</div>
                                    
    								<div class="row-form required">
    									<label for=""><?php echo lang('ticket_department');?></label>
    									<?php echo form_dropdown('ticket_cat', $tickets_cat, '', 'class="form-control select2"');?>
    								</div>
                                    
    								<div class="row-form required">
    									<label for="input-details"><?php echo lang('details');?></label>
    									<?php
                                            $details_att = array(
                                                                'id'          => 'details',
                                                                'class'       => 'form-control',
                                                                'name'        => 'details',
                                                                'required'    => 'required',
                                                                'value'       => set_value('details')
                                                              );
                                            echo form_textarea($details_att);
                                            echo form_error('details');
                                        ?>
    								</div>
                                    
    								<div class="row-form required">
    									<label for="input-order_id"><?php echo lang('order_id');?></label>
    									<select id="order_id" name="order_id" class="form-control select2">
                                        	<?php foreach($tickets_orders as $orders){?>
                                            <option value="<?php echo $orders->id;?>" <?php echo count($tickets_orders);?>><?php echo $orders->id;?> </option>
                                            <?php }?>
                                        </select>
                                        <?php echo form_error('order_id');?>
    								</div>
                                    
                                    <div class="buttons clearfix">
                						<div>
                                            <button class="btn btn-default"><?php echo lang('send');?> </button>
                                            <!--<button type="reset" class="btn btn-danger"><?php echo lang('cancle');?></button>-->
                						</div>
                					</div>
                                </form>
    					 </div><!--comment-area-->      	   	  	 
           	   	  	 </div>	<!--col-->
           	   	  </div></div><!--row-->
           	    </div><!--container-->
    
    </main>