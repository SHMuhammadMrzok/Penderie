<?php if(count($ticket_posts) != 0){?>
    <div class="portlet box blue">
        <div class="portlet-title">
        	<div class="caption"><?php echo lang('ticket_details');?></div>
         </div>
         
         <div class="portlet-body over-flow">
            <div class="col-md-6 col-sm-12 no-padding">
                <?php if(isset($ticket_posts)&& !empty($ticket_posts)){?>
                    <?php foreach($ticket_posts as $post){?>
                      <div>  
                        <div class="static-info border-cell">
                        	<div class="col-md-5 name">
                        		 <?php echo lang('username');?>  :
                        	</div>
                        	<div class="col-md-7 value">
                        		 <?php echo $post->username;?>
                        	</div>
                        </div>
                        
                        <div class="static-info border-cell">
                        	<div class="col-md-5 name">
                        		 <?php echo lang('date');?>  :
                        	</div>
                        	<div class="col-md-7 value">
                        		 <?php echo date('Y/m/d H:i:s',$post->unix_time);?>
                        	</div>
                        </div>
                    
                        <div class="static-info border-cell">
                        	<div class="col-md-5 name">
                        		 <?php echo lang('message_text');?>  :
                        	</div>
                        	<div class="col-md-7 value">
                        		 <p><?php echo $post->post_text; ?></p>
                        	</div>
                        </div>
                        <?php if($post->attachments != ''){?>
                        <div class="static-info border-cell">
                      		<div class="col-md-5 name">
                                <?php echo lang('attachments');?>
                            </div>
                            <div class="col-md-7 value">
                                <?php $post_attachments = explode(" , ", $post->attachments);
                                   
                                   for($i=0 ; $i < count($post_attachments); $i++)
                                   {?>
                                    <a href="<?php echo base_url();?>assets/uploads/tickets_posts/<?php echo $post_attachments[$i];?>" target="_blank"><?php echo $post_attachments[$i];?></a><br />
                                <?php }?>
                              </div>
                        </div>
                        <?php }?>
                       </div>
                        <br />
                        <br />
                    <?php }//for ?>
                 <?php  }//if?>
            	
                <h3 class="title_h1">
                    <a href="<?php echo base_url();?>tickets/admin_tickets/reply/<?php echo $ticket_id;?>"><?php echo lang('replay');?></a>
                </h3>
                
             </div>
        </div><!---->
    </div><!--portlet-->
<?php }?>