    <section class="predcramp">
        <div class="container no-padding">
            <ul>
                <li><a href="<?php echo base_url();?>"><?php echo lang('home');?></a></li>
                <li><span>/</span></li>
                <li><a href="<?php echo base_url().'tickets/tickets/ticket_details/'.$ticket->id;?>"><?php echo lang('ticket_details')?></a></li>
            </ul>
        </div><!--container-->
    </section><!--predcramp-->
    
    <main class="no-padding-top">
        
        <section class="details-block">
            <div class="container no-padding">
                <div class="row no-margin">
                    <div class="title-page">
                        <h4><?php echo lang('ticket_details');?></h4>
                    </div><!--title-page-->
                    
                    <div class="order-records">
                        <table class="table table-bordered table-hover">
                            <tr class="header-table">
                                <td><?php echo lang('ticket_id');?></td>
                                <td><?php echo lang('title');?></td>
                                <td><?php echo lang('details');?></td>
                                <td><?php echo lang('ticket_department');?></td>
                                <?php if ($ticket->order_id != 0){?>
                                    <td><?php echo lang('order_id');?></td>
                                <?php }?>
                                <?php if ($order_serial){?>
                                    <td><?php echo lang('serial');?></td>
                                <?php }?>
                                <td><?php echo lang('ticket_status');?></td>
                                <td><?php echo lang('unix_time');?></td>
                                <td><?php echo lang('last_updated_by');?></td>
                                <td><?php echo lang('last_update_unix_time');?></td>
                                <?php if($ticket->attachments){?>
                                    <td><?php echo lang('attachments');?></td>
                                <?php }?>
                            </tr>
                                <td>#<?php echo $ticket->id;?> </td>
                                <td><?php echo htmlspecialchars($ticket->title);?> </td>
                                <td><?php echo htmlspecialchars($ticket->details);?></td>
                                <td><?php echo $ticket->cat_title;?></td>
                                <?php if ($ticket->order_id != 0){?>
                                    <td><?php echo $ticket->order_id;?></td>
                                <?php }?>
                                <?php if ($order_serial){?>
                                    <td><?php echo $order_serial;?></td>
                                <?php }?>
                                <td><?php echo $ticket->status_title;?></td>
                                <td><?php echo date('Y/m/d H:i:s',$ticket->unix_time);?></td>
                                <td><?php echo $ticket_last_updated->username;?></td>
                                <td><?php echo date('Y/m/d H:i:s',$ticket->last_update_unix_time);?></td>
                                <?php if($ticket->attachments){?>
                                    <td>
                                        <?php $ticket_attachments = explode(" , ", $ticket->attachments);
                                            for($i=0 ; $i < count($ticket_attachments); $i++)
                                        {?>
                                            <i class="fa fa-paperclip"></i>
                                            <a href="<?php echo base_url();?>assets/uploads/tickets_posts/<?php echo $ticket_attachments[$i];?>" target="_blank"><?php echo $ticket_attachments[$i];?></a><br />
                                        <?php }?>
                                    </td>
                                <?php }?>
                            </tr>
                        </table>
                    </div><!--order-records-->
                </div><!--row-->
            </div><!--container-->
        </section><!--details-block-->
        
        <?php if(isset($ticket_posts) && !empty($ticket_posts)){?>
            <section class="review" id="review">
           	   <div class="container">
           	   	  <div class="row">
          	   	  	 <div class="col-md-9 center-div">
    					 <div class="title">
    						<h3><?php echo lang('ticket_posts');?></h3>
    					 </div><!--title-->
                         
                         <?php foreach($ticket_posts as $post){?>
                            <div class="comment-area">
        					 	<div class="row no-margin">
        							<div class="user-img">
        								<a href="#">
                                            <?php if($post->user_image && file_exists(base_url().'assets/uploads/'.$post->user_image)){?>
                                                <img src="<?php echo base_url();?>assets/uploads/<?php echo $post->user_image;?>" alt="<?php echo $post->username;?>" width="70" height="70"/>
                                            <?php }else{?>
                                                <img width="100" src="<?php echo base_url();?>assets/template/home/img/logo.png" alt="<?php echo $post->username;?>"/>
                                            <?php }?>
                                        </a>
        							</div><!--user-img-->
        							<div class="user-name">
        								<h5><a href="#"><?php echo $post->username;?></a></h5>
        								<p><?php echo date('Y/m/d H:i:s',$post->unix_time);?></p>
        							</div><!--user-name-->
        					 	</div><!--row-->
        					 	<div class="row no-margin margin-top-5px">
        					 		<article>
        					 			<?php echo htmlspecialchars($post->post_text); ?>
        					 		</article>
        					 	</div><!--row-->
                                <?php if($post->attachments){?>
                                    <div class="row no-margin margin-top-5px">
                                        <p><i class="fa fa-paperclip"></i> <?php echo lang('attachments');?></p>
                                        <?php $attachments = explode(" , ", $post->attachments);
                                            for($i=0 ; $i < count($attachments); $i++)
                                            {?>
                                                <a href="<?php echo base_url();?>assets/uploads/tickets_posts/<?php echo $attachments[$i];?>" target="_blank"><?php echo $attachments[$i];?></a><br />
                                            <?php }?>
                                    </div>
                                <?php } ?>
        					 </div><!--comment-area-->
                         <?php }?>
           	   	  	 </div>	<!--col-->
           	   	  </div><!--row-->
           	   </div><!--container-->
           </section><!--review-->
       <?php }?>
       
       <?php if($ticket->status_id != 3 && $ticket->status_id != 4 ){ ?>
            <section class="review" id="review">
                <div class="container">
                    <div class="row">
                        <div class="col-md-9 center-div">
                            <div class="title">
                                <h3><?php echo lang('send_message');?></h3>
                            </div><!--title-->
                            
                            <div class="comment-area">
                                <form method="post" action="<?php echo base_url();?>tickets/tickets/save_post" enctype="multipart/form-data">
                                    <div class="row-form required">
    									<label><?php echo lang('attached_files').' ('.lang('browse').')';?></label>
                                        <input type="file" name="userfile[]" class="form-control" multiple  />
    								</div>
                                    <div class="row-form required">
    									<label><?php echo lang('message_text');?></label>
                                        <textarea class="form-control" name="message_text" placeholder="<?php echo lang('message_text');?>"></textarea>
    								</div>
                                    <div class="row-form required">
                                        <label class="checkbox">
    										<input type="checkbox" class="form-control" name="status"/>
    										<span></span>
    									</label>
    									<label><?php echo lang('solved');?></label>
    								</div>
                                    <div class="buttons clearfix">
                						<div>
                							<input type="hidden" name="ticket_id" value="<?php echo $ticket_id; ?>" />
                                            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
                                            <button class="btn btn-default"><?php echo lang('send');?> </button>
                                            <!--<button type="reset" class="btn btn-danger"><?php echo lang('cancle');?></button>-->
                						</div>
                					</div>
                                </form>
    					 </div><!--comment-area-->      	   	  	 
           	   	  	 </div>	<!--col-->
           	   	  </div><!--row-->
           	    </div><!--container-->
            </section><!--review-->
        <?php }else{?>
            <section class="review" >
                <div class="container">
                    <div class="row no-margin margin-bottom-10px col-md-9 center-div">
                        <p><a href="<?php echo base_url()?>tickets/tickets/reopen/<?php echo $ticket_id ;?>" class="btn btn-default"><?php echo lang('reopen_ticket')?></a></p>
                    </div>
                </div>
            </section>
        <?php }?>
    
    </main>