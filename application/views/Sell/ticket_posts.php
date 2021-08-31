<?php if(count($ticket_posts) != 0){?>
    <div class="col-12 dashboard-left">
        <div class="row">
            <div class="col-md-12">
                <div class="title">
                    <h3><?php echo lang('ticket_details');?></h3>
                </div>
                <?php if(isset($ticket_posts)&& !empty($ticket_posts)){?>
                    <div class="table-area">
                        <table class="table table-striped table-bordered table-hover">
                            <tbody>
                                <?php foreach($ticket_posts as $post){?>
                                    <tr class="header-ta">
                                    	<td><?php echo lang('username');?></td>
                                        <td><?php echo $post->username;?></td>
                                    </tr>
                                    
                                    <tr>
                                    	<td><?php echo lang('date');?></td>
                                        <td><?php echo date('Y/m/d H:i:s',$post->unix_time);?></td>
                                    </tr>
                                    
                                    <tr>
                                    	<td><?php echo lang('message_text');?></td>
                                        <td><p><?php echo $post->post_text; ?></p></td>
                                    </tr>
                                    
                                    <?php if($post->attachments != ''){?>
                                        <tr>
                                        	<td><?php echo lang('attachments');?></td>
                                            <td>
                                                <?php
                                                $post_attachments = explode(" , ", $post->attachments);
                                                for($i=0 ; $i < count($post_attachments); $i++)
                                                {?>
                                                    <a href="<?php echo base_url();?>assets/uploads/tickets_posts/<?php echo $post_attachments[$i];?>" target="_blank"><?php echo $post_attachments[$i];?></a><br />
                                                <?php }?>
                                            </td>
                                        </tr>
                                    <?php }?>
                                <?php }//for?>
                            </tbody>
                        </table>
                    </div>
                <?php  }//if?>
                
                <div class="form-group">
                    <div class="row no-gutters align-items-left">
                        <div class="col-md-12">
                            <a href="<?php echo base_url();?>tickets/admin_tickets/reply/<?php echo $ticket_id;?>" class="button"><?php echo lang('reply');?></a>
                        </div>
                    </div>
                </div>
                
            </div><!--col-->
        </div><!--row-->
        
    </div><!--col-->
<?php }?>